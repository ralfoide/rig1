// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: rig_jpeg.h,v 1.4 2005/09/25 22:36:15 ralfoide Exp $

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
	Copyright:		2001 (c) Ralf

	File:			rig_jpeg.h
	Author:			RM
	Description:	interface with JpegLib

*****************************************************************************/


#ifndef _RIG_JPEG_H_
#define _RIG_JPEG_H_


//---------------------------------------------------------------


void	rig_jpeg_filetype_support(void);
bool	rig_jpeg_info (const char* filename, int32 &width, int32 &height);
RigRgb*	rig_jpeg_read (const char* filename);
bool	rig_jpeg_write(const char* filename, RigRgb *rgb, int32 quality, bool interlace);


//---------------------------------------------------------------


#endif // _RIG_JPEG_H_

/****************************************************************

	$Log: rig_jpeg.h,v $
	Revision 1.4  2005/09/25 22:36:15  ralfoide
	Updated GPL header date.
	
	Revision 1.3  2004/07/17 07:52:32  ralfoide
	GPL headers
	
	Revision 1.2  2003/08/18 02:06:16  ralfoide
	New filetype support
	
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

