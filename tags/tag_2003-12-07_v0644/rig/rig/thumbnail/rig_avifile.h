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

