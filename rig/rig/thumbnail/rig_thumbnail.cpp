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
#endif


/*--------------------------------------------------------------------------

Thumbnail Generator
-------------------

Usage:

-i in-file	reports information on file: text output, in the form
			-	file format \n	(either "jpeg" or "unknown", without quotes)
			-	width \n		(in pixels, "0" if unknown)
			-	height \n		{in pixels, "0" if unknown)

-r in-file out-file out-size [quality=80 [gamma=1.0]]
			resize image in-file to out-file, fitting the largest size to
			out-size pixels, respecting the aspect ratio. Optional "quality"
			for jpeg save operation, defaults to 80. Optional "gamma" for
			changing gamma after rescaling (default to 1., which is no-op)

-v			turns verbose debug on for further operations (i.e. place first)

Important: the filename will be entirely "unslashed", i.e. every backslash
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

	// under Linux, I noticed CLOCKS_PER_SEC is 1e6 but times() returns
	// value in 100*seconds.
	const double clock_per_sec = 100.;	// instead of CLOCKS_PER_SEC

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


//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------
//
// Actions
//
//---------------------------------------------------------------------------------



//*******************************************
void rig_print_info(const char * in_filename)
//*******************************************
{
	int32 width, height;

	char *name = rig_unslash(in_filename);

	if (rig_jpeg_info(name, width, height))
	{
		printf("[rig-thumbnail-result] jpeg %ld %ld\n", width, height);
	}
	else if (rig_avifile_info(name, width, height))
	{
		printf("[rig-thumbnail-result] video %ld %ld\n", width, height);
	}
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

		if (!in_rgb)
			in_rgb = rig_avifile_read(name);

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

		DPRINTF(("\n*** Resize [%dx%d] -> [%dx%d]\n\n", wsrc, hsrc, wdst, hdst));

		// resize image

		out_rgb = in_rgb->Rescale(wdst, hdst);

		// we no longer need the original image

		delete in_rgb; in_rgb = NULL;

		// apply gamma to image

		if (gamma > 0. && gamma != 1.)
		{
			out_rgb->ApplyGamma(gamma);
		}

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
	const char *f_in = "in.jpg";
	const char *f_out = "out.jpg";
	
	int32 qual = 75;
	int32 size = 64;
	int32 nmax = 15;

	printf("Starting test... please wait\n");

	for(double gamma=1.; gamma <= 1.6; gamma += .6)
	{
		printf("Gamma %.1f\n", gamma);

		double start = rig_system_time() / 1e6;

		for(int32 i=0; i<nmax; i++)
		{
			rig_resize_image(f_in, f_out, size, qual, gamma);
		}

		double t = rig_system_time() / 1e6 - start;

		printf("loop %d times -> %.2f s -> %.2lf i/s -- %.1lf ms/i\n",
				nmax,
				t,
				(double)nmax/(double)t,
				1000.*(double)t/(double)nmax);
	}
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
	DPRINTF(("Running app_main: %s\n", ctime(&_now)));
	DPRINTF(("Argc: %d\n",argc));

	if (argc == 1)
	{
		printf( "Usage:\n"
				"\t%s [-v] [-i ...] [-r ...]\n"
				"\t-v : verbose output (debug)\n"
				"\t-i in-file : prints out information on file (format\\nwidth\\nheight\\n)\n"
				"\t-r in-file out-file out-size [quality=80] : resize image\n"
				"\t-t debug test\n"
				"\nReturns: 0=no error, 1=processing error, 2=no arguments\n",
				argv[0]);
		return 2;
	}
	
	try
	{
		// Parse arguments
		// we start at 1 cause the first one is the app name
		for (int32 i = 1 ; i < argc ; i++)
		{
			if (!strcmp(argv[i], "-v"))
			{
				rig_dprintf_verbose = true;
			}
			else if (!strcmp(argv[i], "-i") && i<argc-1)
			{
				rig_print_info(argv[++i]);
			}
			else if (!strcmp(argv[i], "-r") && i<argc-3)
			{
				rig_resize_image(argv[i+1], argv[i+2],
							 atol(argv[i+3]),
							 (i+4<argc && isdigit(argv[i+4][0])) ? atol(argv[i+4]) : 80,
							 (i+5<argc && isdigit(argv[i+5][0])) ? atof(argv[i+5]) : 1.);
			}
			else if (!strcmp(argv[i], "-t"))
			{
				rig_dprintf_verbose = false;
				rig_test();
			}
		}
	}
	catch(const char * s)
	{
		DPRINTF(("%s: Catched string exception... -- result 1 --\n", argv[0]));
		DPRINTF((s));
		return 1;
	}
	catch(...)
	{
		DPRINTF(("%s: Catched unknown exception... -- result 1 --\n", argv[0]));
		return 1;
	}

	DPRINTF(("%s: Closing normally -- result 0 --\n", argv[0]));
	return 0;

}


//---------------------------------------------------------------------------



/*****************************************************************************

	$Log$
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
