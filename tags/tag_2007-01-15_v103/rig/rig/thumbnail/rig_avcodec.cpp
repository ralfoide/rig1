// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: rig_avifile.cpp 332 2006-12-07 01:08:35Z ralfoide $

	Copyright 2001-2005 and beyond, Raphael MOLL.

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
	Copyright:		2008 (c) Ralf

	File:			rig_avcodec.cpp
	Author:			RM
	Description:	interface with libavcodec/libavformats

	Inspired from libavcodec tutorial from:
      http://www.inb.uni-luebeck.de/~boehme/using_libavcodec.html
	and
	  aviexample.c in the libavcodec source code.

	To compile this on linux/debian:
	- add "deb http://www.debian-multimedia.org stable main"
	  to /etc/apt/sources.list
	- apt-get install libavcodec-dev libavformat-dev

	To run this on linux/debian:
	- apt-get install libavcodec0d libavformat0d

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"
#include "rig_avcodec.h"

#ifdef RIG_USES_AVCODEC

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#ifndef WIN32
    #include <alloca.h>
	#include <unistd.h>
#endif

//----------------------------------------------------------------------------
// libavcodec headers

#include <avcodec.h>
#include <avformat.h>

//----------------------------------------------------------------------------
// Debug macro utility

#define DEBUG 0

#if DEBUG
	#define DPRINTF(s) rig_dprintf s
#else
	#define DPRINTF(s)
#endif



//---------------------------------------------------------------




//*************************************
void rig_avcodec_filetype_support(void)
//*************************************
{
#ifndef RIG_USES_AVIFILE
	printf("/\\.avi$/i\n");
	printf("video/avi:video/x-msvideo\n");
	printf("/\\.wmv$/i\n");
	printf("video/avi:video/x-ms-wmv\n");
	printf("/\\.as[fx]$/i\n");
	printf("video/avi:video/x-ms-asf\n");
	printf("/\\.(mov|qt|sdp|rtsp)$/i\n");
	printf("video/quicktime\n");
	printf("/\\.(mpe?g[124]?|m[12]v|mp4)$/i\n");
	printf("video/mpeg\n");
	printf("/\\.rm$/i\n");
	printf("video/real:application/vnd.rn-realmedia\n");
	printf("/\\.flv$/i\n");
	printf("video/flash\n");
#endif
}


//*****************************************************************************
static AVCodecContext* rig_avcodec_openFirstStream(AVFormatContext *pFormatCtx,
												   const char* filename,
												   int *videoStream) {
//*****************************************************************************
	// Retrieve stream information
    if (av_find_stream_info(pFormatCtx) >= 0) {

		#if DEBUG
			// Dump information about file onto standard error
			dump_format(pFormatCtx, 0, filename, false);
		#endif
	
		// Find the first video stream
		for (int i=0; i < pFormatCtx->nb_streams; i++) {
		    AVCodecContext *pCodecCtx = pFormatCtx->streams[i]->codec;
		    if (pCodecCtx != NULL && pCodecCtx->codec_type == CODEC_TYPE_VIDEO) {
		    	if (videoStream != NULL) {
		    		*videoStream = i;
		    	}
		    	return pCodecCtx;
			}
	    }
	}
	
	return NULL;
}



//*************************************************************************************
bool rig_avcodec_info(const char* filename, int32 &width, int32 &height, uint32 &codec)
//*************************************************************************************
{
 	DPRINTF(("[rig] rig_avcodec_info: '%s'\n", filename));

    // Register all formats and codecs
    av_register_all();

    // Open video file
    bool ok = false;
    AVFormatContext *pFormatCtx;
    if (av_open_input_file(&pFormatCtx, filename, NULL, 0, NULL) == 0) {

		AVCodecContext *pCodecCtx = rig_avcodec_openFirstStream(pFormatCtx, filename, NULL);
		if (pCodecCtx != NULL) {
            width  = pCodecCtx->width;
            height = pCodecCtx->height;
            codec  = (uint32) pCodecCtx->codec_tag;
            ok = true;

	        // Close the codec
    	    avcodec_close(pCodecCtx);
        }

        // Close the video file
        av_close_input_file(pFormatCtx);
    }

    return ok;
}


//---------------------------------------------------------------


void pgm_save(unsigned char *buf,int wrap, int xsize,int ysize, int index)
{
    FILE *f;
    int i;

	char filename[1024];
	sprintf(filename, "img_%02d.pgm", index);

	DPRINTF(("  =========> WRITE %s\n", filename));
	
    f=fopen(filename,"w");
    fprintf(f,"P5\n%d %d\n%d\n",xsize,ysize,255);
    for(i=0;i<ysize;i++)
        fwrite(buf + i * wrap,1,xsize,f);
    fclose(f);
}


static bool rig_avcodec_getNextPacket(AVFormatContext* pFormatCtx, AVPacket* packet, int videoStream) {
    while (true) {
        // Free old packet
        if (packet->data != NULL) av_free_packet(packet);

        // Read new packet
        if (av_read_packet(pFormatCtx, packet) < 0)
            return false;

        //--DPRINTF(("  READ PACKET: size=%d, stream=%d\n", packet->size, packet->stream_index));

        if (packet->stream_index == videoStream)
            return true;
    }

    DPRINTF(("  !!!!!!!!!!!!!!!!!!!! GET NEXT PACKET FALSE\n"));
    return false;
}

//********************************************************************
static bool rig_avcodec_getNextFrame(AVFormatContext* pFormatCtx,
									 AVCodecContext* pCodecCtx,
									 int videoStream,
                                     AVFrame* pFrame,
                                     AVPacket* packet,
                                     int& packet_bytes)
//********************************************************************
{
    while (true) {
        //--DPRINTF(("GET NEXT FRAME: packet_bytes=%d. videoStream=%d\n", packet_bytes, videoStream));
        if (packet_bytes <= 0) {
            if (!rig_avcodec_getNextPacket(pFormatCtx, packet, videoStream)) {
                DPRINTF(("  get next packet FALSE\n"));
                return false;
            }
        }

        uint8_t* raw_data = packet->data;
        packet_bytes = packet->size;

        while (packet_bytes > 0) {
            int finished = false;
            int decoded = avcodec_decode_video(pCodecCtx, pFrame, &finished,
                                               raw_data, packet_bytes);

            //--DPRINTF(("  DECODE: packet_bytes=%d, decoded=%d, finished=%s\n",
            //--         packet_bytes, decoded, finished?"TRUE":"false"));

            if (decoded < 0) {
                DPRINTF(("Error while decoding frame\n"));
                return false;
            }

            packet_bytes -= decoded;

            // Did we finish the current frame? Then we can return
            if (finished) {
                DPRINTF(("  GOT FRAME\n"));
                return true;
            }
            
        }
    }

    DPRINTF(("  !!!!!!!!!!!!!!!!!!!! GET NEXT FRAME FALSE\n"));
    return false;
}



//*********************************************
RigRgb * rig_avcodec_read(const char* filename)
//*********************************************
{
	RigRgb *rgb = NULL;

	if (!filename)
		return NULL;

	DPRINTF(("[rig] rig_avcodec_read: '%s'\n", filename));

	try
	{
        // Register all formats and codecs
        av_register_all();

        // Open video file
	    AVFormatContext *pFormatCtx;
        if (av_open_input_file(&pFormatCtx, filename, NULL, 0, NULL) == 0) {

			int videoStream;
			AVCodecContext *pCodecCtx = rig_avcodec_openFirstStream(pFormatCtx, filename, &videoStream);
			if (pCodecCtx != NULL) {
                pCodecCtx->skip_frame = AVDISCARD_NONKEY;
				
				// Find the decoder for the video stream
				AVCodec *pCodec = avcodec_find_decoder(pCodecCtx->codec_id);
				if (pCodec != NULL) {
					// Inform the codec that we can handle truncated bitstreams
					// i.e., bitstreams where frame boundaries can fall in the
					// middle of packets
				    if (pCodec->capabilities & CODEC_CAP_TRUNCATED) {
				    	pCodecCtx->flags |= CODEC_FLAG_TRUNCATED;
				    }
				    
				    // Open codec
				    if (avcodec_open(pCodecCtx, pCodec) >= 0) {

						// Allocate video frame
						AVFrame *pFrame = avcodec_alloc_frame();
						
						// Allocate an AVPicture structure
						AVPicture pPictureRgb;
						if (avpicture_alloc(&pPictureRgb, PIX_FMT_RGB24, pCodecCtx->width, pCodecCtx->height) == 0) {

							// Read some frames and keep the first keyframe found
                            AVPacket packet;
                            packet.data = NULL;
                            int packet_bytes = 0;
                            bool got_image = false;
							for (int i = 0;
							     i < 100 && rig_avcodec_getNextFrame(pFormatCtx, pCodecCtx, videoStream,
                                                                    pFrame, &packet, packet_bytes);
							     i++) {

                                DPRINTF(("  Frame type: %d, keyframe: %d, coded# %d, display# %d\n", pFrame->pict_type,
                                			pFrame->key_frame, pFrame->coded_picture_number,
                                			pFrame->display_picture_number ));


								// DEBUG
								#if DEBUG
									pgm_save(pFrame->data[0], pFrame->linesize[0],
	                         					pCodecCtx->width, pCodecCtx->height, i);
                         		#endif


								// stop at the first keyframe found
								if (pFrame->key_frame) {
	                                got_image = true;
    	                            //--TODO use libswcale
        	                        DPRINTF(("  IMG CONVERT\n"));
									img_convert(&pPictureRgb, PIX_FMT_RGB24, (AVPicture*)pFrame,
												pCodecCtx->pix_fmt, pCodecCtx->width, pCodecCtx->height);
									break;
								}
						    }

                            if (packet.data) av_free_packet(&packet);

							// pPictureRgb contains a single RGB plane
							int32 w = pCodecCtx->width;
							int32 h = pCodecCtx->height;
							
							// bytes to skip at end of each line (ideally zero)
							int32 delta = 3*w - pPictureRgb.linesize[0];
							assert(delta >= 0);

                            if (got_image) {
                				DPRINTF(("  WRITE RGB\n"));
                                rgb = new RigRgb(w, h);
                                if (rgb) {
                                    uint8 *r = rgb->R();
                                    uint8 *g = rgb->G();
                                    uint8 *b = rgb->B();
                                    // convert RGB24 into RGB triplets
                                    uint8 *m = (uint8 *) pPictureRgb.data[0];
                                    for(int32 y=0; y<h; y++) {
                                        for(int32 x=0; x<w; x++) {
                                            // the source is R-G-B
                                            *(r++) = *(m++);
                                            *(g++) = *(m++);
                                            *(b++) = *(m++);
                                        }
                                        m += delta;
                                    }

                                    /*
                                    uint8* dests[3] = {r, g, b};
                                    for (int k = 0; k < 3; k++) {
                                        uint8* dest = dests[k];
                                        uint8_t* src = pPictureRgb.data[k];
                                        delta = w - pPictureRgb.linesize[k];
                                        assert(delta >= 0);

                                        for(int32 y=0; y<h; y++, dest += delta) {
                                            for(int32 x=0; x<w; x++) {
                                                *(dest++) = *(src++);
                                            }
                                        }
                                    }
                                    */
                                    
                                } // if rgb
                            }
						}

                        // Free RGB picture
                        avpicture_free(&pPictureRgb);

						// Free the YUV frame
						av_free(pFrame);
				    }
				}
            
	            // Close the codec
    	        avcodec_close(pCodecCtx);
		    }

            // Close the video file
            av_close_input_file(pFormatCtx);
        }
	}
	catch(...)
	{
		DPRINTF(("[rig %s:%d] Unexpected exception\n", __FILE__, __LINE__));
	}

	// ---
	DPRINTF(("[rig %s:%d] -- end rgb = %p\n", __FILE__, __LINE__, rgb));

	return rgb;

} // end of rig_avcodec_read


//---------------------------------------------------------------

#endif // RIG_USES_AVCODEC

//---------------------------------------------------------------

// eoc

//------------------------
// Local Variables:
// mode: c++
// tab-width: 4
// indent-tabs-mode: nil
// c-basic-offset: 4
// sentence-end-double-space: nil
// fill-column: 79
// End:
