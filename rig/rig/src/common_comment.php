<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//**********************************************
// $Id$
//**********************************************


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
//	Revision 1.1  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//
//-------------------------------------------------------------
?>
