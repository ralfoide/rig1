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

	File:			rig_avifile.h
	Author:			RM
	Description:	interface with libavifile

*****************************************************************************/


#ifndef _RIG_AVIFILE_H_
#define _RIG_AVIFILE_H_

#ifndef RIG_EXCLUDE_AVIFILE

//---------------------------------------------------------------


void	rig_avifile_filetype_support(void);
bool	rig_avifile_info (const char* filename, int32 &width, int32 &height, uint32 &codec);
RigRgb*	rig_avifile_read (const char* filename);


//---------------------------------------------------------------

#endif // RIG_EXCLUDE_AVIFILE

#endif // _RIG_AVIFILE_H_

/****************************************************************

	$Log$
	Revision 1.6  2004/07/17 07:52:32  ralfoide
	GPL headers

	Revision 1.5  2003/11/25 05:02:04  ralfoide
	Video: report the video codec
	
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

