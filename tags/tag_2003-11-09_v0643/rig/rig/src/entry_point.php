<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


// This script is called directly by index.php
// At this point, nothing has been loaded yet.
// The only rig function defined is rig_check_src_file.

// ------------------------------------------------------------

// depending on the query string, call the right php script
// 1- upload	&  admin -> admin_upload.php
// 2- translate	&  admin -> admin_translate.php
// 3- admin  	&  image -> admin_image.php
// 4- admin  	& !image -> admin_album.php
// 5- !admin 	&  image -> image.php
// 6- !admin 	& !image -> album.php

$rig_is_image = (isset($_GET['image']) && is_string($_GET['image']) && $_GET['image']);

if (isset($_GET['admin']) && $_GET['admin'])
{
	if (0 && isset($_GET['upload']) && $_GET['upload']) // RM 20030820 -- not yet implemented
	{
		// RM 20030820 -- not yet implemented
		// require_once(rig_check_src_file($dir_abs_admin_src . "admin_upload.php"));
	}
	else if (isset($_GET['translate']) && $_GET['translate'])
	{
		require_once(rig_check_src_file($dir_abs_admin_src . "admin_translate.php"));
	}
	else if (0 && $rig_is_image) // RM 20030525 deactivated
	{
		// RM 20030820 -- not yet implemented
		// require_once(rig_check_src_file($dir_abs_admin_src . "admin_image.php"));
	}
	else
	{
		require_once(rig_check_src_file($dir_abs_admin_src . "admin_album.php"));
	}
}
else
{
	if (isset($_GET['comment']) && $_GET['comment'])
	{
		require_once(rig_check_src_file($dir_abs_src . "comment.php"));
	}
	else if ($rig_is_image)
	{
		require_once(rig_check_src_file($dir_abs_src . "image.php"));
	}
	else
	{
		require_once(rig_check_src_file($dir_abs_src . "album.php"));
	}
}

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.6  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//
//	Revision 1.5  2003/08/21 20:18:02  ralfoide
//	Renamed dir/path variables, updated rig_require_once and rig_check_src_file
//	
//	Revision 1.4  2003/08/18 03:05:12  ralfoide
//	PHP 4.3.x support
//	
//	Revision 1.3  2003/06/30 06:08:11  ralfoide
//	Version 0.6.3.4 -- Introduced support for videos -- new version of rig_thumbnail.exe
//	
//	Revision 1.2  2003/05/26 17:52:30  ralfoide
//	Disabled admin_image (not finished -- experimental)
//	
//	Revision 1.1  2003/03/12 07:02:08  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.3  2003/02/16 20:09:41  ralfoide
//	Update. Version 0.6.3.1
//	
//	Revision 1.2  2002/10/20 09:03:19  ralfoide
//	Display error when require_once files cannot be located
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
