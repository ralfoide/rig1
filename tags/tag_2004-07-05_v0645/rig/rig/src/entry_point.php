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

// RM 20040703 using "img" query param instead of "image"
$rig_is_image = (isset($_GET['img']) && is_string($_GET['img']) && $_GET['img']);

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
	// TEST -- RM 20040201
	else if (isset($_GET['overview']))
	{
		require_once(rig_check_src_file($dir_abs_src . "overview.php"));
	}
	// TEST -- RM 20040222
	else if (isset($_GET['tests']))
	{
		require_once(rig_check_src_file($dir_abs_src . "tests.php"));
	}
	else
	{
		require_once(rig_check_src_file($dir_abs_src . "album.php"));
	}
}

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.10  2004/07/06 04:10:58  ralfoide
//	Fix: using "img" query param instead of "image"
//	Some browsers (at least PocketIE) will interpret "&image=" as "&image;" in URL.
//
//	Revision 1.9  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.8  2004/02/23 04:09:00  ralfoide
//	Entry point for overview test and for phpUnit testing
//
//	[...]
//
//	Revision 1.2  2003/05/26 17:52:30  ralfoide
//	Disabled admin_image (not finished -- experimental)
//	
//	Revision 1.1  2003/03/12 07:02:08  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//
//	[...]
//
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
