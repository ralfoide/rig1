// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2004, Raphael MOLL.

	This file is part of RIG-Thumbnail.

	RIG-Thumbnail is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	RIG-Thumbnail is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with RIG-Thumbnail; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
//************************************************************************

/*****************************************************************************

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_jpeg.cpp
	Author:			RM
	Description:	interface with JpegLib

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"
#include "rig_jpeg.h"

//----------------------------------------------------------------------------
// Jpeg Lib headers

#include <stdio.h>
#include <setjmp.h>
extern "C"
{
	#define  JPEG_INTERNAL_OPTIONS
	#include "jpeglib.h"
	#include "jerror.h"
}


//----------------------------------------------------------------------------
// Debug macro utility


#if 0
	#define DPRINTF(s) rig_dprintf s
#else
	#define DPRINTF(s)
#endif



//---------------------------------------------------------------
//---------------------------------------------------------------


// JPEG Error Handler
//
// More comments about the jpeglib's error hanlder in :
// - jpeglib/(jpeg-6a)/libjpeg.doc
// - jpeglib/(jpeg-6a)/example.c

struct rig_jpegio_error_mgr
{
  struct jpeg_error_mgr pub;	// "public" fields
  jmp_buf setjmp_buffer;		// for return to caller
};

typedef struct rig_jpegio_error_mgr * rig_jpegio_error_ptr;

METHODDEF(void) rig_jpegio_error_exit (j_common_ptr cinfo)
{
	/* cinfo->err really points to a rig_jpegio_error_mgr struct, so coerce pointer */
	rig_jpegio_error_ptr myerr = (rig_jpegio_error_ptr) cinfo->err;
	
	/* Always display the message. */
	/* We could postpone this until after returning, if we chose. */
	#ifndef NDEBUG
		(*cinfo->err->output_message)(cinfo);
		#ifdef WIN32
			char buffer[JMSG_LENGTH_MAX];
			(*cinfo->err->format_message)(cinfo, buffer);
			DPRINTF(("[rig] jpegio error: %s\n", buffer));
		#endif
	#endif
	
	/* Return control to the setjmp point */
	longjmp(myerr->setjmp_buffer, 1);
}


//---------------------------------------------------------------


//**********************************
void rig_jpeg_filetype_support(void)
//**********************************
{
	printf("/\\.jpe?g$/i\n");
	printf("image/jpeg\n");
}


//*******************************************************************
bool rig_jpeg_info(const char* filename, int32 &width, int32 &height)
//*******************************************************************
{
	bool result = false;

	FILE * infile = NULL;

	DPRINTF(("[rig] rig_jpeg_info: '%s'\n", filename));

	struct jpeg_decompress_struct cinfo;
	struct rig_jpegio_error_mgr jerr;

	// ---

	infile = fopen(filename, "rb");
	if (infile)
	{
	
		cinfo.err = jpeg_std_error(&jerr.pub);
		jerr.pub.error_exit = rig_jpegio_error_exit;
		// Establish the setjmp return context for my_error_exit to use.
		if (setjmp(jerr.setjmp_buffer))
		{
			// If we get here, the JPEG code has signaled an error.
			// We need to clean up the JPEG object, close the input file, and return.
	
			jpeg_destroy_decompress(&cinfo);
			fclose(infile);
			return false;
		}

		jpeg_create_decompress(&cinfo);

		jpeg_stdio_src(&cinfo, infile);

		// --- try decoding the header of the jpeg file

		int value = jpeg_read_header(&cinfo, FALSE);
		result = (value == JPEG_HEADER_OK);

		if (result)
		{
			jpeg_start_decompress(&cinfo);

			width = cinfo.output_width;
			height = cinfo.output_height;
		}

		// ---
	
		jpeg_destroy_decompress(&cinfo);
	}

	if (infile)
		fclose(infile);

	return result;

}

//---------------------------------------------------------------


//******************************************
RigRgb * rig_jpeg_read(const char* filename)
//******************************************
{
	RigRgb *rgb = NULL;

	if (!filename)
		return NULL;

	JSAMPLE  *buffer = NULL;
	FILE     *infile = NULL;

	DPRINTF(("[rig] rig_jpeg_read '%s'\n", filename));

	// ---

	struct jpeg_decompress_struct cinfo;
	struct rig_jpegio_error_mgr jerr;

	// ---

	infile = fopen(filename, "rb");
	if (infile)
	{
		cinfo.err = jpeg_std_error(&jerr.pub);
		jerr.pub.error_exit = rig_jpegio_error_exit;
		// Establish the setjmp return context for my_error_exit to use.
		if (setjmp(jerr.setjmp_buffer))
		{
			// If we get here, the JPEG code has signaled an error.
			// We need to clean up the JPEG object, close the input file, and return.
	
			delete [] buffer;
			buffer = NULL;
			delete rgb;
			rgb = NULL;
			jpeg_destroy_decompress(&cinfo);
			fclose(infile);
			return NULL;
		}


		// ---

		jpeg_create_decompress(&cinfo);

		jpeg_stdio_src(&cinfo, infile);

		// ---

		int result = jpeg_read_header(&cinfo, TRUE);

		if (result == JPEG_HEADER_OK)
		{
			// RM 2002101 Fix: grayscale jpeg support
			//
			// The JPEG's color format can be tested via cinfo.jpeg_color_space
			// Whatever the JPEG color format actually is, we ask the decompression
			// routine to provide us with RGB data.

			cinfo.out_color_space = JCS_RGB;
			cinfo.dct_method = JDCT_FLOAT;	// choices are JDCT_FLOAT, JDCT_ISLOW, JDCT_IFAST
	
			jpeg_start_decompress(&cinfo);
	
			// ---
	
			int32 w = cinfo.output_width;
			rgb = new RigRgb(w, cinfo.output_height);

			if (rgb)
			{
				JSAMPROW row_pointer[1];	// pointer to a single row
				int row_stride;				// physical row width in buffer
						
				// JSAMPLEs per row in image_buffer
				row_stride = w * cinfo.output_components;	
		
				uint8 *r = rgb->R();
				uint8 *g = rgb->G();
				uint8 *b = rgb->B();
		
				buffer = new JSAMPLE[row_stride];
				if (buffer)
				{
					row_pointer[0] = buffer;
				
					for( ;cinfo.output_scanline < cinfo.output_height; )
					{
						// read the data
					    jpeg_read_scanlines(&cinfo, row_pointer, 1);
			
						// convert rgba into RGB triplets
						JSAMPLE *m = buffer;
						for(int32 x=0; x<w; x++)
						{
							*(r++) = *(m++);
							*(g++) = *(m++);
							*(b++) = *(m++);
						}
					}
				}
			}
		}

		// ---
	
		jpeg_finish_decompress(&cinfo);
	}

	// ---

	delete [] buffer;
	if (infile)
		fclose(infile);

	return rgb;

} // end of rig_jpeg_read



//***********************************************************************************
bool rig_jpeg_write(const char* filename, RigRgb *rgb, int32 quality, bool interlace)
//***********************************************************************************
{
	FILE * outfile = NULL;
	JSAMPLE *buffer = NULL;

	if (!rgb || !filename)
		return false;

	int32 w, h;
	w = rgb->Sx();
	h = rgb->Sy();

	// keep next line, to remember alpha is supposedly not used here...
	// -- RemoveTransparency(own_raster, GetBackgroundColor(info, doc)); --

	// go for the Jpeg saving part...
	// first create the jpeg object structures and the default error handler

	struct jpeg_compress_struct cinfo;
	struct rig_jpegio_error_mgr jerr;

	// ---

	outfile = fopen(filename, "wb");
	if (outfile && rgb)
	{
		cinfo.err = jpeg_std_error(&jerr.pub);
		jerr.pub.error_exit = rig_jpegio_error_exit;

		// Establish the setjmp return context for my_error_exit to use.

		if (setjmp(jerr.setjmp_buffer))
		{
			// If we get here, the JPEG code has signaled an error.
			// We need to clean up the JPEG object, close the input file, and return.

			delete [] buffer;
			buffer = NULL;
			if (outfile)
				fclose(outfile);
			jpeg_destroy_compress(&cinfo);
			return false;
		}

		//  create the cinfo struct

  		jpeg_create_compress(&cinfo);

		// now specifiy the output for the jpeg lib. Use the default stdio stuff.

		DPRINTF(("[rig] jpeg_stdio_dest\n"));
		jpeg_stdio_dest(&cinfo, outfile);

		// now set image main characteristics :
		// - image width and height, in pixels
		// - # of color components per pixel
		// - colorspace of input image
		cinfo.image_width = w; 	
		cinfo.image_height = h;
		cinfo.input_components = 3;	
		cinfo.in_color_space = JCS_RGB;

	
		DPRINTF(("[rig] jpeg_set_defaults\n"));
		jpeg_set_defaults(&cinfo);

		// Make optional parameter settings here

		// set quality
		jpeg_set_quality(&cinfo, quality, false);

		// set progressive
		if (interlace)
			jpeg_simple_progression(&cinfo);

		// now go for it

		DPRINTF(("[rig] jpeg_start_compress\n"));
		jpeg_start_compress(&cinfo, TRUE);

		// actually write the data

		JSAMPROW row_pointer[1];	// pointer to a single row
		int row_stride;				// physical row width in buffer
	
		row_stride = w * RGB_PIXELSIZE;	// JSAMPLEs per row in image_buffer
										// RM 071599 use RGB_PIXELSIZE

		uint8 *r = rgb->R();
		uint8 *g = rgb->G();
		uint8 *b = rgb->B();
		int32 count_progress = 0;
		int32 count = 2;
		
		buffer = new JSAMPLE[row_stride];
		if (buffer)
		{
			DPRINTF(("[rig] jpeg buffer %p\n", buffer));
			row_pointer[0] = buffer;
		
			for( ;cinfo.next_scanline < cinfo.image_height; count_progress++)
			{
				// convert rgba into RGB triplets
				JSAMPLE *m = buffer;

				for(int32 x=0; x<w; x++, m+=3)
				{
					// RM 071599 use RGB_xxx JPEG constants
					m[RGB_RED  ] = *(r++);
					m[RGB_GREEN] = *(g++);
					m[RGB_BLUE ] = *(b++);
				}
	
				// write the data
			    jpeg_write_scanlines(&cinfo, row_pointer, 1);
			}
			DPRINTF(("[rig] jpeg write done\n"));
		}

		// indicate end of processing
		DPRINTF(("[rig] jpeg_finish_compress\n"));
		jpeg_finish_compress(&cinfo);
	}

	delete [] buffer;

	if (outfile)
		fclose(outfile);

	return true;

} // end of rig_jpeg_write


//---------------------------------------------------------------

/****************************************************************

	$Log$
	Revision 1.5  2004/07/17 07:52:32  ralfoide
	GPL headers

	Revision 1.4  2004/07/09 05:55:57  ralfoide
	Comments
	
	Revision 1.3  2003/08/18 02:06:16  ralfoide
	New filetype support
	
	Revision 1.2  2002/10/20 09:04:10  ralfoide
	Fix for non-RGB jpeg
	
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail
	
	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.1  2001/10/24 07:14:21  ralf
	new rig_thumbnail, on the way
	
	Revision 1.1  2001/10/21 02:11:56  ralf
	new thumbnail app
	
****************************************************************/

// eoc

