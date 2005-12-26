// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

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

	File:			rig_mem.h
	Author:			RM
	Description:	templated memory block

*****************************************************************************/


#ifndef _RIG_MEM_H_
#define _RIG_MEM_H_


//---------------------------------------------------------------
// Declaration
//-------------


//*****************************
template <class T> class RigMem
//*****************************
{
public:
			RigMem(void);
			RigMem(int32 size);
			~RigMem(void) { Free(); }

	void	Alloc  (int32 size);
	void	Realloc(int32 size);
	void	Free   (void);

	int32	Size(void) const				{ return mSize; }
	T *		Data(void) const				{ return mData; }
	T &		ItemAt(int32 index) const;
	operator T* (void) const				{ return mData; }

	// operator[] is redundant with operator T*, if you want checking use ItemAt.
	// T &		operator [] (int32 index) const	{ return ItemAt(index); }

private:
	
	T *		mData;
	int32	mSize;
};


//---------------------------------------------------------------
// Implementation
//----------------


//***********************************************
template <class T> inline RigMem<T>::RigMem(void)
//***********************************************
{
	mData = NULL;
	mSize = 0;
}


//*****************************************************
template <class T> inline RigMem<T>::RigMem(int32 size)
//*****************************************************
{
	mData = NULL;
	mSize = 0;

	Alloc(size);
}


//*********************************************************
template <class T> inline void RigMem<T>::Alloc(int32 size)
//*********************************************************
{
	Free();

	// don't allocate anything if size is nul

	if (!size)
		return;

	try
	{
		mData = new T[size];
		mSize = size;
	}
	catch(...)
	{
	}
}


//***************************************************************
template <class T> inline void RigMem<T>::Realloc(int32 new_size)
//***************************************************************
{
	// get "old" values
	T * data = mData;
	mData = NULL;

	int32 old_size = mSize;
	mSize = 0;

	// alloc the new space
	Alloc(new_size, mac_limit);

	// if there was old memory, copy it to the new one
	if (mData && old_size && new_size && data)
		memcpy(mData, data, (old_size < new_size ? old_size : new_size));

	// release old memory

	delete [] data;
}


//**************************************************
template <class T> inline void RigMem<T>::Free(void)
//**************************************************
{
	// release memory

	delete [] mData;
	mData = NULL;
	mSize = 0;
}


//****************************************************************
template <class T> inline T & RigMem<T>::ItemAt(int32 index) const
//****************************************************************
{
	rig_assert(mData);
	rig_assert(index >= 0 && index < mSize);
	return mData[index];
}


//---------------------------------------------------------------


#endif // _RIG_MEM_H_

/****************************************************************

	$Log$
	Revision 1.3  2005/09/25 22:36:15  ralfoide
	Updated GPL header date.

	Revision 1.2  2004/07/17 07:52:32  ralfoide
	GPL headers
	
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail
	
	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.1  2001/10/24 18:27:43  ralf
	fixes
	
****************************************************************/

// eoc

