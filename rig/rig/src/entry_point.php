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

$rig_is_image = (isset($image) && is_string($image) && $image);


if ($admin)
{
	if ($upload)
	{
		rig_check_src_file($dir_install . $dir_src . "admin_upload.php");
		require_once(      $dir_install . $dir_src . "admin_upload.php");
	}
	else if ($translate)
	{
		rig_check_src_file($dir_install . $dir_src . "admin_translate.php");
		require_once(      $dir_install . $dir_src . "admin_translate.php");
	}
	else if (0 && $rig_is_image) // RM 20030525 deactivated
	{
		rig_check_src_file($dir_install . $dir_src . "admin_image.php");
		require_once(      $dir_install . $dir_src . "admin_image.php");
	}
	else
	{
		rig_check_src_file($dir_install . $dir_src . "admin_album.php");
		require_once(      $dir_install . $dir_src . "admin_album.php");
	}
}
else
{
	if ($rig_is_image)
	{
		rig_check_src_file($dir_install . $dir_src . "image.php");
		require_once      ($dir_install . $dir_src . "image.php");
	}
	else
	{
		rig_check_src_file($dir_install . $dir_src . "album.php");
		require_once      ($dir_install . $dir_src . "album.php");
	}
}

// end

//-------------------------------------------------------------
//	$Log$
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
