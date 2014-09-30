<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: common_xml.php,v 1.5 2005/09/25 22:36:15 ralfoide Exp $

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

//****************************
function rig_xml_dom_enabled()
//****************************
// indicates if this PHP version supports the new DOM XML we need here
// we check two functions here:
// - xmldocfile : old DOM XML API... just avoid it right now
// - domxml_open_file() : new DOM XML API... let's use it
{
	$old_api = function_exists('xmldocfile');
	$new_api = function_exists('domxml_open_file');

	global $_debug_;
	if ($_debug_)
	{
		echo "<p>";
		if ( $new_api) echo "New DOM XML API is available<br>";
		if ( $old_api) echo "Old DOM XML API is available<br>";
		if (!$new_api) echo "New DOM XML API is <font color=red>NOT</font> available<br>";
		if (!$old_api) echo "Old DOM XML API is <font color=red>NOT</font> available<br>";
	}

	return $new_api;
}


//-----------------------------------------------------------------------

//***********************************
function rig_xml_read_options($album)
//***********************************
{
	if (!rig_xml_dom_enabled())
		return FALSE;

	// TBDL -- return false since does nothing
	return FALSE;
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log: common_xml.php,v $
//	Revision 1.5  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.4  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.3  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.2  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//	
//	Revision 1.1  2003/02/17 10:03:00  ralfoide
//	Toying with XML
//-------------------------------------------------------------
?>
