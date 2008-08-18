// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: rig_avifile.h 332 2006-12-07 01:08:35Z ralfoide $

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
	Copyright:		2003 (c) Ralf

	File:			rig_avcodec.h
	Author:			RM
	Description:	interface with libavcodec

*****************************************************************************/


#ifndef _RIG_AVCODEC_H_
#define _RIG_AVCODEC_H_

#ifdef RIG_USES_AVCODEC

//---------------------------------------------------------------


void	rig_avcodec_filetype_support(void);
bool	rig_avcodec_info (const char* filename, int32 &width, int32 &height, uint32 &codec);
RigRgb*	rig_avcodec_read (const char* filename);


//---------------------------------------------------------------

#endif // RIG_USES_AVCODEC

#endif // _RIG_AVCODEC_H_



