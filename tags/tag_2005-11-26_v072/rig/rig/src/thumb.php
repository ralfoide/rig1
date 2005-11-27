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

// Setup an error handler as early as possible
// Because this script is supposed to output an img, the usual text
// reporting and the default PHP reply is mostly inappropriate.
// ++ set_error_handler("rig_thumb_error");

require_once($dir_abs_src . "common.php");

rig_enter_login(rig_self_url());

$album = rig_get($_GET, 'album', FALSE);
$img   = rig_get($_GET, 'img',   FALSE);

if ($img === FALSE)
{
	// This is just an album. No image.
	rig_prepare_album($album, 0, 0);
	rig_display_album_thumb(rig_get($_GET,'sz', FALSE), rig_get($_GET,'q', FALSE));
}
else
{
	// Thumbnail for an image in an album
	rig_prepare_image($album, $img);
	rig_display_image_thumb(rig_get($_GET,'sz', FALSE), rig_get($_GET,'q', FALSE));
}

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2005/11/27 18:21:59  ralfoide
//	Image streaming
//
//-------------------------------------------------------------
?>
