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

	File:			rig_thumbnail.cpp
	Author:			RM
	Description:	main application

*****************************************************************************/


#ifndef _RIG_THUMBNAIL_H_
#define _RIG_THUMBNAIL_H_


//---------------------------------------------------------------------------
// types
//-------

#if defined(WIN32)

	typedef unsigned __int8		uint8;
	typedef	signed   __int32	int32;
	typedef	unsigned __int32	uint32;
	typedef	signed   __int64	int64;

	#include <crtdbg.h>
	#define rig_assert(x)		_ASSERT(x)

#else	// Linux

	typedef unsigned char		uint8;
	typedef	long				int32;
	typedef	unsigned long		uint32;
	typedef	long long			int64;

	#include <assert.h>
	#define rig_assert(x)			assert(x)

#endif


//--------
// macros
//--------

#ifndef NULL
	#define NULL				(0)
#endif

#define rig_throwifnot(x)		if (!(x)) throw(x)

#define rig_pi					3.14159265358979323846264338


//------------------
// common utilities
//------------------


extern void rig_dprintf(const char * format, ...);
extern bool rig_dprintf_verbose;


extern int64 rig_system_time(void);



//---------------------------------------------------------------------------

#endif // _RIG_THUMBNAIL_H_

/*****************************************************************************

	$Log$
	Revision 1.3  2004/07/17 07:52:32  ralfoide
	GPL headers

	Revision 1.2  2003/11/25 05:01:35  ralfoide
	Added unsigned int32 type
	
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail
	
	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.3  2001/10/25 21:06:51  ralf
	makefile for new rig_thumbnail
	
	Revision 1.2  2001/10/24 18:26:14  ralf
	fixes
	
	Revision 1.1  2001/10/24 07:14:21  ralf
	new rig_thumbnail, on the way
	
	Revision 1.1  2001/10/21 02:11:56  ralf
	new thumbnail app
	
	
****************************************************************************/
