/*****************************************************************************
// vim: set tabstop=4 shiftwidth=4: //

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_avifile.cpp
	Author:			RM
	Description:	interface with libavifile

	Inspired from libavifile's sample source code:
	: avitype.cc -- Print info about a .AVI file
	: $Id$
	: Copyright (C) 2001  Tom Pavel <pavel@alum.mit.edu>

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"
#include "rig_avifile.h"


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


//**********************************************************************
bool rig_avifile_info(const char* filename, int32 &width, int32 &height)
//**********************************************************************
{
	DPRINTF(("rig_avifile_info: '%s'\n", filename));

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


//*********************************************
RigRgb * rig_avifile_read(const char* filename)
//*********************************************
{
	RigRgb *rgb = NULL;

	if (!filename)
		return NULL;

	DPRINTF(("rig_avifile_info: '%s'\n", filename));

	avm::IReadFile* aviFile = avm::CreateReadFile(filename);

   	if (aviFile)
	{
		if (aviFile->VideoStreamCount() > 0)
		{
			avm::IReadStream* stream = aviFile->GetStream(0, avm::IStream::Video);
			if (stream)
			{
DPRINTF(("\nstream = %p\n", stream));

				if (stream->StartStreaming(NULL) >= 0)
				{
					avm::CImage* image = stream->GetFrame(true); // must dispose afterwards
	DPRINTF(("\nimage = %p\n", image));

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
	
						}
	
					} // if image
				} // if start stream
			} // if stream
		} // if has videostreams
	} // if avifile

	// ---
DPRINTF(("\n end rg = %p\n", rgb));

	return rgb;

} // end of rig_avifile_read





//---------------------------------------------------------------

/****************************************************************

	$Log$
	Revision 1.1  2003/06/30 06:05:59  ralfoide
	Avifile support (get info and thumbnail for videos)

	
****************************************************************/

// eoc

