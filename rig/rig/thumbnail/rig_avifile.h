/*****************************************************************************
// vim: set tabstop=4 shiftwidth=4: //

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_avifile.h
	Author:			RM
	Description:	interface with libavifile

*****************************************************************************/


#ifndef _RIG_AVIFILE_H_
#define _RIG_AVIFILE_H_


//---------------------------------------------------------------


bool	rig_avifile_info (const char* filename, int32 &width, int32 &height);
RigRgb*	rig_avifile_read (const char* filename);


//---------------------------------------------------------------


#endif // _RIG_AVIFILE_H_

/****************************************************************

	$Log$
	Revision 1.1  2003/06/30 06:05:59  ralfoide
	Avifile support (get info and thumbnail for videos)

	
****************************************************************/

// eoc

