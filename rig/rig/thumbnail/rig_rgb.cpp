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

	File:			rig_rgb.cpp
	Author:			RM
	Description:	rgb bitmap class

*****************************************************************************/

#include "rig_thumbnail.h"
#include "rig_rgb.h"

#include <math.h>
#include <string.h>
#include <stdio.h>

//---------------------------------------------------------------------------
//----------------------------------------------------------------------------
// Debug macro utility


#if 0
	#define DPRINTF(s) rig_dprintf s
#else
	#define DPRINTF(s)
#endif



//********************************
RigRgb::RigRgb(int32 sx, int32 sy)
//********************************
{
	mSx = sx;
	mSy = sy;

	int32 sxy = sx*sy;

	mR.Alloc(sxy);
	mG.Alloc(sxy);
	mB.Alloc(sxy);
}


//*******************
RigRgb::~RigRgb(void)
//*******************
{
}


//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------


//***********************************
void RigRgb::ApplyGamma(double gamma)
//***********************************
{
	uint8 table[256];
	int32 i;

	if (gamma < 1e-3)
		return;

	gamma = 1./gamma;

	for (i=0; i<256; i++)
		table[i] = (uint8)(255. * pow((double)(i/255.), gamma));

	uint8 *r = R();
	uint8 *g = G();
	uint8 *b = B();
	int32 sxy = Sx()*Sy();

	while (sxy--)
	{
		*(r++) = table[*r];
		*(g++) = table[*g];
		*(b++) = table[*b];
	}
}


//*********************************************************
void rig_rescale_down_fast(int32 ssx, int32 ssy,
						   int32 dsx, int32 dsy,
						   uint8 *sr, uint8 *sg, uint8 *sb,
						   uint8 *dr, uint8 *dg, uint8 *db)
//*********************************************************
{
	// ------------------------------
	// ------- DOWNSIZING -----------
	// ------- USE FILTER -----------
	// ------------------------------

	// downsize image...
	// need to compute some kind of average
	// 1600 / 75 = 21.333 -> ix=21, fx=.33

	int32 x, y;

	double fx = (double)ssx/(double)dsx;
	double fy = (double)ssy/(double)dsy;

	int32 ix = (int32)fx;
	int32 iy = (int32)fy;
	fx -= ix;
	fy -= iy;

	// prepare a window filter
	// using the sinc filter:
	// x=d*pi, sinc(x) = sin x/x or sinc(0)=1
	//
	// recalculate fxy with the actual sum of the filter

	RigMem<uint8> filter(ix*iy);

	uint8 *sf = filter;
	double fxy = 0.;

	double ix2 = (double)(ix-1)/2.;
	double iy2 = (double)(iy-1)/2.;
	double fdmax = sqrt(ix2*ix2+iy2*iy2);
	for(y=0; y<iy; y++)
	{
		double y2 = (y-iy2);
		for(x=0; x<ix; x++)
		{
			double x2 = (x-ix2);
			double d = sqrt(x2*x2+y2*y2)/fdmax;
			uint8 f = 255;
			if (d != 0.)
			{
				d *= rig_pi;
				d = sin(d)/d;
				f = (uint8)(255 * d);
				fxy += d;
			}
			else
			{
				fxy += 1.;
			}
			*(sf++) = f;
		}
	}

	fxy = 1. / fxy;

	double ax = 0.;
	double ay = 0.;

	// offset int source
	int32 tsy = 0;

	// for all destination lines
	for(y=0; y<dsy; y++)
	{
		// for all destination columns
		for(int32 tsx=tsy, x=0; x<dsx; x++)
		{
			// source pointers
			uint8 *r2 = sr + tsx;
			uint8 *g2 = sg + tsx;
			uint8 *b2 = sb + tsx;

			// accumulation data
			int32 r=0;
			int32 g=0;
			int32 b=0;

			uint8 *sf = filter;

			// accumulate everything, apply filtering
			for(int32 j=0; j<iy; j++, r2 += ssx, g2 += ssx, b2 += ssx)
			{
				for(int32 i=0; i<ix; i++)
				{
					uint8 f = *(sf++);
					if (f == 255)
					{
						r += r2[i];
						g += g2[i];
						b += b2[i];
					}
					else if (f)
					{
						r += (r2[i] * f) >> 8;
						g += (g2[i] * f) >> 8;
						b += (b2[i] * f) >> 8;
					}
				}
			}

			// output pixel
			*(dr++) = (uint8)(r * fxy);
			*(dg++) = (uint8)(g * fxy);
			*(db++) = (uint8)(b * fxy);

			// prepare for next line in source
			tsx += ix;

			// compensate on x
			ax += fx;
			if (ax >= 1.)
			{
				tsx++;
				ax -= 1.;
			}
		} // for x

		// next group of line in source
		tsy += iy * ssx;

		// compensate on y
		ay += fy;
		if (ay >= 1.)
		{
			tsy += ssx;
			ay -= 1.;
		}
	} // for y

} // rig_rescale_down_fast


//*************************************************************
void rig_rescale_down_generic2(int32 ssx, int32 ssy,
							   int32 dsx, int32 dsy,
							   uint8 *sr, uint8 *sg, uint8 *sb,
							   uint8 *dr, uint8 *dg, uint8 *db)
//*************************************************************
{
	// ------------------------------
	// ------- DOWNSIZING -----------
	// -------- NO FILTER -----------
	// ------- INT32 COEFS ----------
	// ------------------------------


	double dx = (double)ssx/(double)dsx;
	double dy = (double)ssy/(double)dsy;

	int32 ix = (int32)dx;
	int32 iy = (int32)dy;

	double dxy = dx * dy;

	// divider for the accumulator,
	double idxy = 1. / dxy;

	double f;

	//--------------------
	// precompute columns
	//--------------------

	int32 ixc = ix+2;

	RigMem<int32> xcol (dsx * ixc);
	RigMem<int32> xcoef(dsx * ixc);

	f = 0.;
	for(int32 ixs=0, xs=0, xd=0; xd<dsx; xd++, ixs += ixc)
	{
		int32 j = ixs, j2 = ixs+ixc;

		// modulo for start of block
		int32 fi = (int32)(256. * f);
		if (fi)
		{
			xcol [j  ] = xs++;
			xcoef[j++] = fi;
		}

		// integer part of block
		int32 n = (int32)(dx - f);

		for(int32 i=0; i<n; i++)
		{
			xcol [j  ] = xs++;
			xcoef[j++] = 256;
		}

		// modulo for end of block
		f = dx - f - n;
		fi = (int32)(256. * f);

		if (fi)
		{
			xcol [j  ] = xs;
			xcoef[j++] = fi;
		}

		// modulo for next block
		rig_assert(f < 1.);
		f = 1. - f;

		// mark remaining cells as unused
		while (j<j2)
		{
			xcol [j++] = -1;
		}
	}

	//-----------------
	// precompute rows
	//-----------------

	int32 iyc = iy+2;

	RigMem<int32> ycol (dsy * iyc);
	RigMem<int32> ycoef(dsy * iyc);

	f = 0.;
	for(int32 iys=0, ys=0, yd=0; yd<dsy; yd++, iys += iyc)
	{
		int32 j = iys, j2 = iys + iyc;

		// modulo for start of block
		int32 fi = (int32)(256. * f);
		if (fi)
		{
			ycol [j  ] = ys++;
			ycoef[j++] = fi;
		}

		// integer part of block
		int32 n = (int32)(dy - f);

		for(int32 i=0; i<n; i++)
		{
			ycol [j  ] = ys++;
			ycoef[j++] = 256;
		}

		// modulo for end of block
		f = dy - f - n;
		fi = (int32)(256. * f);

		if (fi)
		{
			ycol [j  ] = ys;
			ycoef[j++] = fi;
		}

		// modulo for next block
		rig_assert(f < 1.);
		f = 1. - f;

		// mark remaining cells as unused
		while (j<j2)
		{
			ycol [j++] = -1;
		}
	}

	//---------------
	// compute image
	//---------------

	for(int32 y=0, sj=0; y<dsy; y++, sj+=iyc)
	{
		for(int32 x=0, si=0; x<dsx; x++, si+=ixc)
		{
			// data accumulator
			int32 r = 0;
			int32 g = 0;
			int32 b = 0;

			for(int32 j=0; j<iyc; j++)
			{
				int32 jn = ycol[sj + j];

				if (jn < 0)
					continue;

				int32 jxy = jn * ssx;

				// source pointers
				uint8 *r2 = sr + jxy;
				uint8 *g2 = sg + jxy;
				uint8 *b2 = sb + jxy;

				int32 jf = ycoef[sj + j];

				for(int32 i=0; i<ixc; i++)
				{
					int32  in = xcol [si + i];

					if (in < 0)
						continue;

					int32 cf = jf * xcoef[si + i];

					rig_assert(jxy + in < ssx*ssy);

					r += (r2[in] * cf) >> 16;
					g += (g2[in] * cf) >> 16;
					b += (b2[in] * cf) >> 16;
				} // i
			} // j

			int32 dd=0;
			rig_assert(dd < dsx*dsy);
			dd++;

			// output pixel
			*(dr++) = (uint8)(r * idxy);
			*(dg++) = (uint8)(g * idxy);
			*(db++) = (uint8)(b * idxy);

		} // x
	} // y

} // rig_rescale_down_generic2


//*************************************************************
void rig_rescale_up_generic(int32 ssx, int32 ssy,
							int32 dsx, int32 dsy,
							uint8 *sr, uint8 *sg, uint8 *sb,
							uint8 *dr, uint8 *dg, uint8 *db)
//*************************************************************
{
	// ------------------------------
	// -------- UP SIZING -----------
	// -------- NO FILTER -----------
	// ------- INT32 COEFS ----------
	// ------------------------------


	double dx = (double)dsx/(double)(ssx-1);
	double dy = (double)dsy/(double)(ssy-1);

	//--------------------
	// precompute columns
	//--------------------
	// x/col contains 2 entries per destination pixel:
	// entry[n*dsx + 0] = source column/row index
	// entry[n*dsx + 1] = coef for source column/row
	// the second source column/row index is not stored because
	// we know anyway it's the next source if the coef is not nul.
	// the second coefficient is not stored because it is 256-first coef

	RigMem<int32> xcol(dsx * 2);

	{
		int32 * px = xcol;

		double i = 1./dx;
		double f = 1.;
		for(int32 xs=0, xd=0; xd<dsx; xd++, px += 2)
		{
			if (f == 1.)
			{
				px[0] = xs;
				px[1] = 256;
				f -= i;
			}
			else // f in [0..1[, 1 excluded
			{
				px[0] = xs;
				px[1] = (int32)(256. * f);
				f -= i;
			}

			if (f <= 0.)
			{
				f += 1.;
				xs++;
			}
		}
	}

	//-----------------
	// precompute rows
	//-----------------

	RigMem<int32> ycol(dsy * 2);

	{
		int32 * py = ycol;

		double i = 1./dy;
		double f = 1.;
		for(int32 ys=0, yd=0; yd<dsy; yd++, py += 2)
		{
			if (f == 1.)
			{
				py[0] = ys;
				py[1] = 256;
				f -= i;
			}
			else // f in [0..1[, 1 excluded
			{

				py[0] = ys;
				py[1] = (int32)(256. * f);
				f -= i;
			}

			if (f <= 0.)
			{
				f += 1.;
				ys++;
			}
		}
	}

	int32 *py = ycol;
	for(int32 y=0; y<dsy; y++)
	{
		int32 yc0 = *(py)++ * ssx;
		int32 yf0 = *(py)++;
		int32 yf1 = 256 - yf0;
		int32 yc1 = yc0 + ssx;

		uint8 *sr0 = sr + yc0;
		uint8 *sg0 = sg + yc0;
		uint8 *sb0 = sb + yc0;

		uint8 *sr1 = sr + yc1;
		uint8 *sg1 = sg + yc1;
		uint8 *sb1 = sb + yc1;

		DPRINTF(("[rig] rgb: y=%2d / yf0=%03d -> r=%03d / g=%03d / b=%03d\n", y, yf0, *sr0, *sg0, *sb0));

		int32 *px = xcol;
		for(int32 x=0; x<dsx; x++)
		{
			int32 xc0 = *(px)++;
			int32 xf0 = *(px)++;
			int32 xf1 = 256 - xf0;
			int32 xc1 = xc0 + 1;

			DPRINTF(("[rig] rgb:   x=%2d / xf0=%03d -> r=%03d / g=%03d / b=%03d\n", x, xf0, sr0[xc0], sg0[xc0], sb0[xc0]));

			uint8 r, g, b;

			// interpolate pixel

			if (yf0 == 256)
			{
				if (xf0 == 256)
				{
					// dest: [xc0/yc0]
					r = sr0[xc0];
					g = sg0[xc0];
					b = sb0[xc0];
				}
				else
				{
					// dest: [xc0/yc0]*(xf0) + [xc1/yc0]*(xf1)
					r = (uint8)((sr0[xc0]*xf0 + sr0[xc1]*xf1) >> 8);
					g = (uint8)((sg0[xc0]*xf0 + sg0[xc1]*xf1) >> 8);
					b = (uint8)((sb0[xc0]*xf0 + sb0[xc1]*xf1) >> 8);
				}
			}
			else if (xf0 == 256)
			{
				// dest: [xc0/yc0]*(yf0) + [xc0/yc1]*(yf1)
				r = (uint8)((sr0[xc0]*yf0 + sr1[xc0]*yf1) >> 8);
				g = (uint8)((sg0[xc0]*yf0 + sg1[xc0]*yf1) >> 8);
				b = (uint8)((sb0[xc0]*yf0 + sb1[xc0]*yf1) >> 8);
			}
			else
			{
				// dest: the whole nine yards
				r = (uint8)((sr0[xc0]*yf0*xf0 + sr0[xc1]*yf0*xf1 + sr1[xc0]*yf1*xf0 + sr1[xc1]*yf1*xf1) >> 16);
				g = (uint8)((sg0[xc0]*yf0*xf0 + sg0[xc1]*yf0*xf1 + sg1[xc0]*yf1*xf0 + sg1[xc1]*yf1*xf1) >> 16);
				b = (uint8)((sb0[xc0]*yf0*xf0 + sb0[xc1]*yf0*xf1 + sb1[xc0]*yf1*xf0 + sb1[xc1]*yf1*xf1) >> 16);
			}

			DPRINTF(("[rig] rgb: -> r=%03d / g=%03d / b=%03d\n", r, g, b));

			// write pixel
			*(dr++) = r;
			*(dg++) = g;
			*(db++) = b;

		} // x
	} // y

} // rig_rescale_up_generic




//******************************************
RigRgb * RigRgb::Rescale(int32 sx, int32 sy)
//******************************************
{
	int32 ssx = Sx();
	int32 ssy = Sy();
	int32 dsx = sx;
	int32 dsy = sy;

	RigRgb *dest = new RigRgb(dsx, dsy);

	if (!dest)
		return NULL;

	if (dsx == ssx && dsy == ssy)
	{
		// ------------------------------
		// ------- NO SCALING -----------
		// ------------------------------

		int32 sxy = dsx*dsy;
		memcpy(dest->R(), R(), sxy);
		memcpy(dest->G(), G(), sxy);
		memcpy(dest->B(), B(), sxy);
	}
	else if (dsx < ssx && dsy < ssy)
	{
#if 0
		rig_rescale_down_fast(ssx, ssy,
							  dsx, dsy,
							  R(), G(), B(),
							  dest->R(), dest->G(), dest->B());
#elif 0
		rig_rescale_down_generic(ssx, ssy,
								 dsx, dsy,
								 R(), G(), B(),
								 dest->R(), dest->G(), dest->B());
#else
		rig_rescale_down_generic2(ssx, ssy,
								  dsx, dsy,
								  R(), G(), B(),
								  dest->R(), dest->G(), dest->B());
#endif
	}
	else
	{
		rig_rescale_up_generic(ssx, ssy,
							   dsx, dsy,
							   R(), G(), B(),
							   dest->R(), dest->G(), dest->B());
	}

	return dest;
}





//---------------------------------------------------------------------------



/*****************************************************************************

	$Log$
	Revision 1.3  2004/07/17 07:52:32  ralfoide
	GPL headers

	Revision 1.2  2004/07/09 05:55:57  ralfoide
	Comments
	
	Revision 1.1  2002/08/04 00:58:08  ralfoide
	Uploading 0.6.2 on sourceforge.rig-thumbnail
	
	Revision 1.1  2001/11/26 00:07:40  ralf
	Starting version 0.6: location and split of site vs album files
	
	Revision 1.9  2001/10/27 22:10:37  ralf
	latest fixes
	
	Revision 1.8  2001/10/26 18:16:12  ralf
	debug
	
	Revision 1.7  2001/10/26 17:19:40  ralf
	upsize fixed
	
	Revision 1.6  2001/10/26 17:14:38  ralf
	upsize
	
	Revision 1.5  2001/10/26 03:04:58  ralf
	implemented up-size resize
	
	Revision 1.4  2001/10/25 20:51:06  ralf
	fix
	
	Revision 1.3  2001/10/25 18:38:03  ralf
	downsize generic is working nice and fast
	
	Revision 1.2  2001/10/24 18:26:14  ralf
	fixes
	
	Revision 1.1  2001/10/24 07:14:21  ralf
	new rig_thumbnail, on the way
	
	Revision 1.1  2001/10/21 02:11:56  ralf
	new thumbnail app
	
****************************************************************************/
