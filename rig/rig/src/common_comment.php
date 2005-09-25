<?php
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


//-----------------------------------------------------------------------


//********************************
function rig_comment_has_preview()
//********************************
{
	return TRUE;
}


//-----------------------------------------------------------------------

//********************************
function rig_comment_insert_icon()
//********************************
// Insert the html for the current icon
{
	global $dir_images;
	global $pref_empty_album;
	
	
	$link = $dir_images . $pref_empty_album;
	echo "<img src=\"$link\">";
}


//********************************
function rig_comment_insert_name()
//********************************
{
	echo "image name";
}


//***********************************
function rig_comment_insert_comment()
//***********************************
{
	echo "blah blah<br>blah <b>lbah blha</b> lbhas fv <i>dfbhd bhdfk</i> vbdfhbvdgf bydgbudi budibue rguwgfu hvudfvwiuvh bv e veuiv v!";
}


//********************************
function rig_comment_insert_text()
//********************************
{
	echo "blah blah\nblah __lbah blha__ lbhas fv ''dfbhd bhdfk'' vbdfhbvdgf bydgbudi budibue rguwgfu hvudfvwiuvh bv e veuiv v!";
}


//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.4  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//
//	Revision 1.3  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.2  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.1  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//-------------------------------------------------------------
?>
