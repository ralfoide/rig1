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
	Copyright:		2003 (c) Ralf

	File:			rig_avifile.cpp
	Author:			RM
	Description:	interface with libavifile

	Inspired from libavifile's sample source code:
	: avitype.cc -- Print info about a .AVI file
	: Copyright (C) 2001  Tom Pavel <pavel@alum.mit.edu>

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"
#include "rig_avifile.h"

#ifndef RIG_EXCLUDE_AVIFILE

#include <stdio.h>

//----------------------------------------------------------------------------
// libavifile headers

#include <aviplay.h>		// for GetAvifileVersion
#include <version.h>		// for AVIFILE_VERSION
#include <avifile.h>		// for IReadFile, IReadStream
#include <infotypes.h>		// for StreamInfo
#include <image.h>			// for CImage


//----------------------------------------------------------------------------
// Debug macro utility

#if 0
	#define DPRINTF(s) rig_dprintf s
#else
	#define DPRINTF(s)
#endif



//---------------------------------------------------------------




//*************************************
void rig_avifile_filetype_support(void)
//*************************************
{
	printf("/\\.(avi|wmv|as[fx])$/i\n");
	printf("video/avi\n");
	printf("/\\.(mov|qt|sdp|rtsp)$/i\n");
	printf("video/quicktime\n");
	printf("/\\.(mpe?g[124]?|m[12]v|mp4)$/i\n");
	printf("video/mpeg\n");
}


//*************************************************************************************
bool rig_avifile_info(const char* filename, int32 &width, int32 &height, uint32 &codec)
//*************************************************************************************
{
	DPRINTF(("[rig] rig_avifile_info: '%s'\n", filename));

	avm::IReadFile* aviFile = avm::CreateReadFile(filename);

   	if (aviFile)
	{
		if (aviFile->VideoStreamCount() > 0)
		{
			avm::IReadStream* stream = aviFile->GetStream(0, avm::IStream::Video);
			if (stream)
			{
				avm::StreamInfo* info = stream->GetStreamInfo(); // must dispose afterwards

				if (info)
				{
					width  = info->GetVideoWidth();
					height = info->GetVideoHeight();
					codec  = info->GetFormat();

					// dispose stuff
					delete info;
					
					return true;
				}
			}
		}
	}

	return false;
}


//---------------------------------------------------------------


//******************************************************
static framepos_t rig_avi_closest_key(framepos_t target,
									  framepos_t key1,
									  framepos_t key2,
									  framepos_t key3)
//******************************************************
{
	#define RIG_DIST(x, y) (x < y ? y-x : x-y)
	
	framepos_t d1 = RIG_DIST(target, key1);
	framepos_t d2 = RIG_DIST(target, key2);
	framepos_t d3 = RIG_DIST(target, key3);
	
	if (d2 < d1)
	{
		if (d3 < d2)
			return key3;
		else
			return key2;
	}
	else
	{
		if (d3 < d1)
			return key3;
		else
			return key1;
	}
}


//*********************************************
RigRgb * rig_avifile_read(const char* filename)
//*********************************************
{
	avm::IReadFile *	aviFile	= NULL;
	avm::IReadStream *	stream	= NULL;
	avm::CImage *		image	= NULL;
	RigRgb *			rgb		= NULL;

	if (!filename)
		return NULL;

	DPRINTF(("[rig] rig_avifile_read: '%s'\n", filename));

	try
	{
		aviFile = avm::CreateReadFile(filename);
	
	   	if (aviFile)
		{
			if (aviFile->VideoStreamCount() > 0)
			{
				stream = aviFile->GetStream(0, avm::IStream::Video);
				if (stream)
				{
					DPRINTF(("[rig] -- stream = %p\n", stream));

					// Get the length of the stream and seek to 10% of the length
					framepos_t frame_len = stream->GetLength();
					DPRINTF(("[rig] -- length = %d frames (%.3f s)\n", frame_len, stream->GetLengthTime()));

					framepos_t key1 = stream->GetNextKeyFrame();
					DPRINTF(("[rig] -- key1 = %d frame\n", key1));

					framepos_t key2 = stream->GetNextKeyFrame(key1);
					DPRINTF(("[rig] -- key2 = %d frame\n", key2));

					// Start at 10% of the length
					if (frame_len > 0)
					{
						framepos_t target = frame_len / 10;
						DPRINTF(("[rig] -- target = %d frame\n", target));

						framepos_t key3 = stream->GetNextKeyFrame(target);
						DPRINTF(("[rig] -- key3 = %d frame\n", key3));

						target = rig_avi_closest_key(target, key1, key2, key3);

						int sk = stream->Seek(target);
						DPRINTF(("[rig] -- seek %d res= %d\n", target, sk));
					}
					

					// Start streaming	
					if (stream->StartStreaming(NULL) >= 0)
					{
						DPRINTF(("[rig] -- current time = %.3f s\n", stream->GetTime()));

						image = stream->GetFrame(true); // must dispose afterwards
						DPRINTF(("[rig] -- image = %p\n", image));
	
						// stop the stream once we got the first image
						stream->StopStreaming();

						if (image)
						{
							// whatever the read image format being, convert it to BGR 24 bits
							// IMPORTANT: the format will be B-G-R, not R-G-B!
		
							CImage image2(image, 24);
							
							// dispose the original image
							
							delete image;
							image = NULL;
						
							int32 w = image2.Width();
							int32 h = image2.Height();
							
							// bytes to skip at end of each line (ideally zero)
							int32 delta = image2.Bpl() - 3*w;
		
							rgb = new RigRgb(w, h);
							if (rgb)
							{
								uint8 *r = rgb->R();
								uint8 *g = rgb->G();
								uint8 *b = rgb->B();
	
								// convert RGB24 into RGB triplets
								uint8 *m = image2.Data();
								for(int32 y=0; y<h; y++)
								{
									for(int32 x=0; x<w; x++)
									{
										// IMPORTANT: the source is B-G-R, not R-G-B!
										*(b++) = *(m++);
										*(g++) = *(m++);
										*(r++) = *(m++);
									}
									m += delta;
								}
		
							} // if rgb
		
						} // if image
					} // if start stream
				} // if stream
			} // if has videostreams
		} // if avifile
	}
	catch(...)
	{
		DPRINTF(("[rig] Unexpected exception\n"));
	}

	// RM 20040707: these objects are not deleted on purpose.
	// At least deleting the aviFile generates a seg fault.
	// Leave the 3 lines commented, understand why it crashes later.
	// delete image;
	// delete stream;
	// delete aviFile;

	// ---
	DPRINTF(("[rig] -- end rgb = %p\n", rgb));

	return rgb;

} // end of rig_avifile_read



//---------------------------------------------------------------

#endif // RIG_EXCLUDE_AVIFILE

//---------------------------------------------------------------

/****************************************************************

	$Log$
	Revision 1.9  2004/07/17 07:52:32  ralfoide
	GPL headers

	Revision 1.8  2004/07/09 05:58:47  ralfoide
	Disabled debug printfs
	
	Revision 1.7  2004/07/09 05:55:28  ralfoide
	Thumbnail for video is now based on the closest keyframe to 10% of the length of movie.
	
	Revision 1.6  2003/11/25 05:02:05  ralfoide
	Video: report the video codec
	
	Revision 1.5  2003/08/18 03:22:19  ralfoide
	Fixed missing include
	
	Revision 1.4  2003/08/18 02:06:16  ralfoide
	New filetype support
	
	Revision 1.3  2003/07/16 06:46:23  ralfoide
	Made video support optional
	
	Revision 1.2  2003/07/11 15:56:38  ralfoide
	Fixes in video html tags. Added video/mpeg mode. Experimenting with Javascript
	
	Revision 1.1  2003/06/30 06:05:59  ralfoide
	Avifile support (get info and thumbnail for videos)
	
****************************************************************/

// eoc

