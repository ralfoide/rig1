/*****************************************************************************
// vim: set tabstop=4 shiftwidth=4: //

	Project:		Thumbnail
	Copyright:		2001 (c) Ralf

	File:			rig_rgb.h
	Author:			RM
	Description:	rgb bitmap class

*****************************************************************************/


#ifndef _RIG_RGB_H_
#define _RIG_RGB_H_


#include "rig_mem.h"


//---------------------------------------------------------------



//**********
class RigRgb
//**********
{
public:
	RigRgb(int32 sx, int32 sy);
	~RigRgb(void);

	uint8 * R(void)		{ return mR; }
	uint8 * G(void)		{ return mG; }
	uint8 * B(void)		{ return mB; }

	int32	Sx(void)	{ return mSx; }
	int32	Sy(void)	{ return mSy; }

	void	ApplyGamma(double gamma);
	RigRgb*	Rescale(int32 sx, int32 sy);

private:
	
	int32			mSx, mSy;
	RigMem<uint8>	mR, mG, mB;
};




//---------------------------------------------------------------


#endif // _RIG_RGB_H_

/****************************************************************

	$Log$
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail

	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.2  2001/10/24 18:26:14  ralf
	fixes
	
	Revision 1.1  2001/10/24 07:14:21  ralf
	new rig_thumbnail, on the way
	
	Revision 1.1  2001/10/21 02:11:56  ralf
	new thumbnail app
	
****************************************************************/

// eoc

