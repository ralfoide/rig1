<?php
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


// This script is called directly by index.php
// At this point, nothing has been loaded yet.
// The only rig function defined is rig_check_src_file.

// ------------------------------------------------------------

// depending on the query string, call the right php script
// 1- upload	&  admin    -> admin_upload.php [N/A]
// 2- translate	&  admin    -> admin_translate.php [obsolete]
// 3- admin  	&  image    -> admin_image.php
// 4- admin  	& !img      -> admin_album.php
// 5- !admin 	&  img      -> image.php
// 6- !admin 	& !img      -> album.php
// 7- !admin    &  comment  -> comment.php [N/A]
// 8- !admin    &  overview -> overview.php [experimental]
// 9- !admin    &  tests    -> tests.php    [phpUnit testing]

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
//	Revision 1.12  2004/07/17 07:52:31  ralfoide
//	GPL headers
//
//	Revision 1.11  2004/07/09 05:52:22  ralfoide
//	Updated comments
//	
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
