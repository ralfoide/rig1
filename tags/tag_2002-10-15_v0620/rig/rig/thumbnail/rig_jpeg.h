/*****************************************************************************
// vim: set tabstop=4 shiftwidth=4: //

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_jpeg.h
	Author:			RM
	Description:	interface with JpegLib

*****************************************************************************/


#ifndef _RIG_JPEG_H_
#define _RIG_JPEG_H_


//---------------------------------------------------------------


bool	rig_jpeg_info (const char* filename, int32 &width, int32 &height);
RigRgb*	rig_jpeg_read (const char* filename);
bool	rig_jpeg_write(const char* filename, RigRgb *rgb, int32 quality, bool interlace);


//---------------------------------------------------------------


#endif // _RIG_JPEG_H_

/****************************************************************

	$Log$
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

