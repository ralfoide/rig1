/*****************************************************************************
// vim: set tabstop=4 shiftwidth=4: //

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_thumbnail.cpp
	Author:			RM
	Description:	main application

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"
#include "rig_jpeg.h"
#include "rig_avifile.h"

#include <stdio.h>
#include <stdarg.h>
#include <stdlib.h>
#include <string.h>

#ifdef WIN32
	#include <time.h>
	#include <Windows.h>	// for SystemTimeToFileTime
#else
	#include <ctype.h>
	#include <time.h>
	#include <sys/times.h>	// for times under Linux
	#include <signal.h>		// for signal/sighandler
	#include <unistd.h>		// for _exit & alarm
#endif


/*--------------------------------------------------------------------------

Thumbnail Generator
-------------------

Usage:

-v			turns verbose debug on for further operations (i.e. place first)

-i in-file	reports information on file: text output, in the form
			-	file format \n	(either "jpeg" or "unknown", without quotes)
			-	width \n		(in pixels, "0" if unknown)
			-	height \n		{in pixels, "0" if unknown)

-r in-file out-file out-size [quality=80 [gamma=1.0]]
			resize image in-file to out-file, fitting the largest size to
			out-size pixels, respecting the aspect ratio. Optional "quality"
			for jpeg save operation, defaults to 80. Optional "gamma" for
			changing gamma after rescaling (default to 1., which is no-op)

-f			reports support file type information. The output are text lines
			in the form: <perl-compatible regexp> \n <major/minor filetype> \n
			For matching pattern syntax,
			cf http://www.php.net/manual/en/function.preg-match.php
			or http://www.perldoc.com/perl5.8.0/pod/perlre.html
			For example for images:
				/\.jpe?g$/i
				image/jpeg
			and videos:
				/\.(avi|wmv|as[fx])$/i
				video/avi
				/\.(mov|qt|sdp|rtsp)$/i
				video/quicktime
				/\.(mpe?g[124]?|m[12]v|mp4)$/i
				video/mpeg

-t			Test mode. Performs and prints some basic benchmark information.


Important: the filenames will be entirely "unslashed", i.e. every backslash
		   is going to be removed (pairs of backslashes are turned into one)

--------------------------------------------------------------------------*/



/*--------------------------------------------------------------------------------

VISUAL C++ LIBRARY USAGE FOR LINK
---------------------------------
[RM 072501]

This is the list of libraries that the project must include when the linker setting
'Ignore All Default Libraries' is ON. This setting should be on if we need control
over what is included (for example, to avoid mixing up debug and release libs, and non
multithreaded with multithreaded ones).

NOTE on how to regenerate this: remove the list of libs in the linker settings, set
'Ignore Default Libs' to OFF and set the flag 'Print progress messages' to ON, link
and look at the beginning of the output. The list of libs found will be printed,
check for missing libs and add them here. Then revert the linker setting (set the list
of libs, reset verbose off and ignore default libs ON).

----------

Default Libs for Win/App:
-------------------------
kernel32.lib user32.lib gdi32.lib winspool.lib comdlg32.lib advapi32.lib shell32.lib
ole32.lib oleaut32.lib uuid.lib odbc32.lib odbccp32.lib

Additionnal libraries:
----------------------

dbg += MSVCRTD.LIB MSVCPRTD.LIB MSVCIRTD.LIB OLDNAMES.LIB LIBCMTD.LIB NAFXCWD.LIB

rel += MSVCRT.LIB MSVCPRT.LIB MSVCIRT.LIB OLDNAMES.LIB LIBCMT.LIB NAFXCW.LIB

--------------------------------------------------------------------------*/




//---------------------------------------------------------------------------
//----------------------------------------------------------------------------
// Debug macro utility


#if 1
	#define DPRINTF(s) rig_dprintf s
#else
	#define DPRINTF(s)
#endif




//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// Common Utilities
//
//---------------------------------------------------------------------------------


bool rig_dprintf_verbose = false;


//****************************************
void rig_dprintf(const char * format, ...)
//****************************************
{
	if (!rig_dprintf_verbose)
		return;

	va_list ap;

	// print the string on the console
	va_start(ap, format);

#ifdef WIN32
	vprintf(format, ap);
#else
	vfprintf(stderr, format, ap);
#endif

	va_end(ap);
}


//*************************
int64 rig_system_time(void)
//*************************
// rig_system_time returns a system time in microseconds
{
#if WIN32

	SYSTEMTIME st;
	GetSystemTime(&st);
	FILETIME ft;
	SystemTimeToFileTime(&st,&ft);
	LARGE_INTEGER li;
	li.LowPart = ft.dwLowDateTime;
	li.HighPart = ft.dwHighDateTime;
	return (li.QuadPart/10); // since measured in 100 nanoseconds


#else

	// tms.utime returns the USER time in clocks per sec
	// tms.stime returns the SYSTEM time in clocks per sec

	tms buf;
	times(&buf);	// times is POSIX

	clock_t total = buf.tms_utime + buf.tms_stime;

	// RM 20030807 need to invert the #if/#else test to make it specific
	// for Linux (libc version?) when CLOCKS_PER_SEC is wrong

	#if __CYGWIN__ || (defined(CLOCKS_PER_SEC) && (CLOCKS_PER_SEC <= 1000 ))
		// CLOCKS_PER_SEC is correct under Cygwin
		const double clock_per_sec = (double) CLOCKS_PER_SEC;
	#else
		// Under Linux, I noticed CLOCKS_PER_SEC is 1e6 but times() returns
		// value in 100*seconds (that's not 100% Posix).
		const double clock_per_sec = 100.;	// instead of CLOCKS_PER_SEC
	#endif

	return (int64)(1e6 * (double)buf.tms_utime / clock_per_sec);

#endif
}



//**************************************
char * rig_unslash(const char *filename)
//**************************************
// RIG will protect special characters in the name by backslashing them.
// Returns a pointer onto a static allocated memory. By construction, 
// this is called once and the result it used right after, so it works.
{
	static char *res = NULL;

	int32 len = 0;

	if (filename)
		len = strlen(filename);

	delete [] res;
	res = new char[len+1];
	res[len] = '\0';

	if (!len)
		return res;

	const char *s = filename;
	char *d = res;
	while(*s)
	{
		if (*s == '\\')
			*(d++) = *(++s);
		else
			*(d++) = *s;
		s++;
	}
	*d = '\0';

	return res;
}


//**************************************
void rig_video_frame(RigRgb *in_out_rgb)
//**************************************
{
	if (!in_out_rgb)
		return;

	int32 sx = in_out_rgb->Sx();
	int32 sy = in_out_rgb->Sy();

	// the pattern is 10x10, so a reasonable thumbnail size is at least 30x10 to use it
	if (sx < 30 || sy < 10)
		return;

	// define the pattern (00=black, FF=white)
	#define K_VIDFRAME_SX 8
	#define K_VIDFRAME_SY 10
	#define B 0x00,
	#define w 0xC0,
	const uint8 pattern[] =
	{
		B B B B B B B B
		B B w w w w B B
		B w w w w w w B	// 1
		B w w w w w w B	// 2
		B w w w w w w B	// 3
		B w w w w w w B	// 4
		B w w w w w w B	// 5
		B w w w w w w B	// 6
		B B w w w w B B
		B B B B B B B B
		0
	};
	#undef B
	#undef w

	// destinations
	int32 of7 = sx-K_VIDFRAME_SX;
	uint8 * dest[6] =
	{
		in_out_rgb->R(), in_out_rgb->R() + of7,
		in_out_rgb->G(), in_out_rgb->G() + of7,
		in_out_rgb->B(), in_out_rgb->B() + of7
	};

	const uint8 *src;
	for(int32 i = 0; sy; sy--, i--, src += K_VIDFRAME_SX)
	{
		if (!i)
		{
			src = pattern;
			i = K_VIDFRAME_SY;
		}

		for(int32 j=0; j<6; j++)
		{
			for(int32 k=0; k<K_VIDFRAME_SX; k++)
			{
				uint8 a = src[k];
				if (a)
					*(dest[j]++) |= a;
				else
					*(dest[j]++) = a;
			}
			dest[j] += of7;
		}
	}
}



//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// Actions
//
//---------------------------------------------------------------------------------


//***********************************
void rig_print_filetype_support(void)
//***********************************
{

	rig_jpeg_filetype_support();

#ifndef RIG_EXCLUDE_AVIFILE

	rig_avifile_filetype_support();

#endif
}


//*******************************************
void rig_print_info(const char * in_filename)
//*******************************************
{
	int32 width, height;
#ifndef RIG_EXCLUDE_AVIFILE
	uint32 codec;
#endif

	char *name = rig_unslash(in_filename);

	if (rig_jpeg_info(name, width, height))
	{
		printf("[rig-thumbnail-result] jpeg %ld %ld\n", width, height);
	}
#ifndef RIG_EXCLUDE_AVIFILE
	else if (rig_avifile_info(name, width, height, codec))
	{
		printf("[rig-thumbnail-result] video %ld %ld @%.4s@\n", width, height, (const char *)(&codec));
	}
#endif
	else
	{
		printf("[rig-thumbnail-result] unknown 0 0\n");
	}
}


//*********************************************
void rig_resize_image(const char * in_filename,
					  const char * out_filename,
					  int32		 target_size,
					  int32		 quality,
					  double	 gamma)
//*********************************************
{
	int32 wsrc, hsrc;
	int32 wdst, hdst;
	RigRgb *in_rgb = NULL;
	RigRgb *out_rgb = NULL;


	DPRINTF(("FILE-IN:'%s'\nFILE-OUT:'%s'\n", in_filename, out_filename));

	try
	{
		// read input image

		char *name = rig_unslash(in_filename);

		in_rgb = rig_jpeg_read(name);

#ifndef RIG_EXCLUDE_AVIFILE
		bool  is_video = false;

		if (!in_rgb)
		{
			in_rgb = rig_avifile_read(name);
			is_video = (in_rgb != NULL);
		}
#endif
		
		if (!in_rgb)
			throw("rig_resize_image: could not read image.\n");
		rig_throwifnot(in_rgb);

		// determine the correct output size

		wdst = wsrc = in_rgb->Sx();
		hdst = hsrc = in_rgb->Sy();

		double aspect = (double)wsrc / (double)hsrc;

		if (wsrc >= hsrc && wdst != target_size)
		{
			wdst = target_size;
			hdst = (int32)((double)target_size / aspect);
		}
		else if (hsrc > wsrc && hdst != target_size)
		{
			hdst = target_size;
			wdst = (int32)((double)target_size*aspect);
		}

		DPRINTF(("[rig] Resize [%dx%d] -> [%dx%d]\n\n", wsrc, hsrc, wdst, hdst));

		// resize image

		out_rgb = in_rgb->Rescale(wdst, hdst);

		// we no longer need the original image

		delete in_rgb; in_rgb = NULL;

		// apply gamma to image

		if (gamma > 0. && gamma != 1.)
		{
			out_rgb->ApplyGamma(gamma);
		}

		// apply decorations
		
#ifndef RIG_EXCLUDE_AVIFILE
		if (is_video)
			rig_video_frame(out_rgb);
#endif
		
		// write output image

		rig_jpeg_write(rig_unslash(out_filename), out_rgb, quality, true);

		// free destination raster

		delete out_rgb;	out_rgb = NULL;

	}
	catch(...)
	{
		delete in_rgb;	in_rgb = NULL;
		delete out_rgb;	out_rgb = NULL;
		throw;
	}
}


//---------------------------------------------------------------------------------

//*****************
void rig_test(void)
//*****************
{
	const char *f_in  = "in.jpg";
	const char *f_out = "out.jpg";
	
	const int32 qual =  75;
	const int32 size = 128;	// 64 is the default size but 128 is a more common thumbnail size
	const int32 nmax =  20;

	printf("Starting test... please wait\n");
	fflush(stdout);

	// Resize image with Gamma 1.0 (no-op) or 1.6 (normal op)

	for(double gamma=1.; gamma <= 1.6; gamma += .6)
	{
		printf("Gamma %.1f\n", gamma);

		double start = rig_system_time() / 1e6;

		for(int32 i=0; i<nmax; i++)
			rig_resize_image(f_in, f_out, size, qual, gamma);

		double t = rig_system_time() / 1e6 - start;

		printf("  loop %d times -> %.2f s -> %.2lf i/s -- %.1lf ms/i\n",
				nmax,
				t,
				(double)nmax/(double)t,
				1000.*(double)t/(double)nmax);
	}

	// Add "video frame" around a thumbnail
	// Read the output from last test (thumbnail in max size*size pixels)
	
	RigRgb *rgb = rig_jpeg_read(f_out);

	const int32 fmax = 20000;

	if (rgb)
	{
		printf("Video Frame\n");
		fflush(stdout);

		double start = rig_system_time() / 1e6;

		for(int32 i=0; i<fmax; i++)
			rig_video_frame(rgb);
		
		double t = rig_system_time() / 1e6 - start;

		printf("  loop %d times -> %.2f s -> %.2lf i/s -- %.1lf ms/i\n",
				fmax,
				t,
				(double)fmax/(double)t,
				1000.*(double)t/(double)fmax);

		delete rgb;
		rgb = NULL;
	}
	


}


//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// Signal Handler
//
//---------------------------------------------------------------------------------


#ifndef WIN32

//***********************
void rig_sig_alarm(int s)
//***********************
{
	DPRINTF(("\n[rig] timeout: signal %d **\n", s));
	// timeout... let's give up without notice but as nicely as possible
	_exit(3);
}

#endif


//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// Usage
//
//---------------------------------------------------------------------------------


//******************************
int rig_usage(const char *argv0)
//******************************
{
	printf( "Usage:\n"
			"\t%s [-v] [-f] [-i if] [-r if of [q [g]]]\n"
			"\t-v : verbose output (debug)\n"
			"\t-i in-file : prints out information on file (format, width & height)\n"
			"\t-r in-file out-file out-size [quality=80 [gamma=1.0]] : build jpeg thumbnail\n"
			"\t-f file types' regexp list\n"
			"\t-t debug test\n"
			"\nReturns: 0=no error, 1=processing error, 2=not enough arguments, 3=timeout\n",
			(argv0 != NULL ? argv0 : "rig-thumbnail.exe"));

	return 2;
}


//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// *   *  ***  *** *   *
// ** ** *   *  *  **  *
// * * * *****  *  * * *
// *   * *   *  *  *  **
// *   * *   * *** *   *
//
//---------------------------------------------------------------------------------



//******************************
int main(int argc, char *argv[])
//******************************
{
	time_t _now = time(NULL);
	DPRINTF(("[rig] Running app_main: %s\n", ctime(&_now)));
	DPRINTF(("[rig] Argc: %d\n",argc));

	if (argc <= 1)
	{
		return rig_usage(argv[0]);
	}

	
	try
	{
		#ifndef WIN32
			// Let's abort if processing is longuer than say 20 seconds
			// (which is a huge amount of time to get a thumbnail since it's generally
			// less than a second or two on modern hardware). 20 seconds also happens
			// to be the time PHP's call will wait.
			signal(SIGALRM, rig_sig_alarm);
			alarm(20);
		#endif

		// Check for verbose flag anywhere in the command line
		for (int32 i = 1 ; i < argc ; i++)
		{
			if (!strcmp(argv[i], "-v"))
			{
				// operation: verbose
				rig_dprintf_verbose = true;
				DPRINTF(("[rig] build " __DATE__ " " __TIME__ "\n"));
				DPRINTF(("[rig] verbose mode on\n"));
				break;
			}
		}

		// Parse arguments (all except -v)
		for (int32 i = 1 ; i < argc ; i++)
		{
			if (!strcmp(argv[i], "-i"))
			{
				// operation: report image info on file name

				if (i >= argc-1)
				{
					DPRINTF(("[rig] missing file name for option -i\n"));
					return rig_usage(argv[0]);
				}

				rig_print_info(argv[++i]);
			}
			else if (!strcmp(argv[i], "-r"))
			{
				// operation: resize image
				// arg 2 = in  file name
				// arg 3 = out file name
				// arg 4 = largest width/height size in pixels
				// arg 5 = jpeg quality (default is 80)
				// arg 6 = gamma (default is 1.0 for a no-op)

				if (i >= argc-3)
				{
					DPRINTF(("[rig] not enough arguments for option -r\n"));
					return rig_usage(argv[0]);
				}

				rig_resize_image(argv[i+1], argv[i+2],
							 atol(argv[i+3]),
							 (i+4<argc && isdigit(argv[i+4][0])) ? atol(argv[i+4]) : 80,
							 (i+5<argc && isdigit(argv[i+5][0])) ? atof(argv[i+5]) : 1.);
			}
			else if (!strcmp(argv[i], "-f"))
			{
				// operation: report supported file types
				rig_print_filetype_support();
			}
			else if (!strcmp(argv[i], "-t"))
			{
				// operation: test/benchmark
				rig_dprintf_verbose = false;
				rig_test();
			}
		}
	}
	catch(const char * s)
	{
		DPRINTF(("[rig] Catched string exception... -- result 1 --\n[rig]   Exception: %s\n", s));
		return 1;
	}
	catch(...)
	{
		DPRINTF(("[rig] Catched unknown exception... -- result 1 --\n"));
		return 1;
	}

	#ifndef WIN32
		// Remove the alarm timeout
		alarm(0);
		signal(SIGALRM, SIG_DFL);
	#endif

	DPRINTF(("[rig] Closing normally -- result 0 --\n"));
	return 0;

}


//---------------------------------------------------------------------------



/*****************************************************************************

	$Log$
	Revision 1.7  2004/07/09 05:54:33  ralfoide
	Better command line processing. Added timeout alarm signal.

	Revision 1.6  2003/11/25 05:02:04  ralfoide
	Video: report the video codec
	
	Revision 1.5  2003/08/18 02:06:16  ralfoide
	New filetype support
	
	Revision 1.4  2003/07/16 06:46:23  ralfoide
	Made video support optional
	
	Revision 1.3  2003/07/14 18:42:01  ralfoide
	Frame in video thumbnail
	
	Revision 1.2  2003/06/30 06:05:59  ralfoide
	Avifile support (get info and thumbnail for videos)
	
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail
	
	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.3  2001/10/28 00:08:21  ralf
	missing include string.h
	
	Revision 1.2  2001/10/25 21:06:51  ralf
	makefile for new rig_thumbnail
	
	Revision 1.1  2001/10/24 07:14:21  ralf
	new rig_thumbnail, on the way
	
	Revision 1.3  2001/10/21 02:15:33  ralf
	new thumbnail app
	
	Revision 1.2  2001/09/05 05:43:53  ralf
	fix for marc
	
****************************************************************************/
