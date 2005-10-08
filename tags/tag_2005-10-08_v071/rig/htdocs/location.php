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

// --- album & system-dependent locations ---

if (PHP_OS == 'WINNT')
	define("S", "\\");
else // Un*x
	define("S", "/");

// ---- global settings ---
  
// base installation directory (absolute path)
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_install		= "/opt/rig-thumbnail" . S;

// php sources: view sources and admin sources
// Note: $dir_abs_src semantic has changed! Both are *absolute* paths instead
// of being relative to $dir_abs_install.
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_src			= $dir_abs_install . "rig/src" . S;
$dir_abs_admin_src		= $dir_abs_install . "rig/admin" . S;

$dir_abs_templates		= $dir_abs_install . "rig/templates" . S;

// global settings
// Note: $dir_abs_globset semantic has changed! Path is *absolute* instead of relative to $dir_abs_install
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_globset		= $dir_abs_install . "rig/settings" . S;


// ---- local settings ---

// The site-album directory (i.e. _this_ directory in absolute)
// The 2 lines below automatically compute the absolute local file-system path
// to the entry point "index.php" file.
$dir_info_album			= pathinfo($_SERVER['PATH_TRANSLATED']);
$dir_abs_album			= $dir_info_album["dirname"];

// local settings
// $dir_abs_locset is optional: it is either an empty string or an absolute path
// RM 20040601: when dir_abs_locset is empty, it is *ignored*.
// RM 20040603: set to the current album absolute directory by default.
$dir_abs_locset				= $dir_abs_album;

// ---- URL settings ---

// Relative-URL for rig images
// Physically, this is relative to $dir_abs_album (i.e. where index.php is)
// The URL string must end with /
$dir_images				= "rig-images" . S;

// Album location
// Physically, this is relative to $dir_abs_album (i.e. where index.php is)
// The URL string must end with /
$dir_album				= "my-photos/";
$dir_image_cache		= "rig-cache/";
$dir_album_cache		= "rig-cache/";
$dir_option				= "rig-options/";



// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.8  2005/10/01 23:44:25  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//
//	Revision 1.7  2005/09/25 22:36:12  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.6  2004/12/25 09:46:46  ralfoide
//	Fixes and cleanup
//	
//	Revision 1.5  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.4  2004/07/09 05:47:21  ralfoide
//	Updated.
//	
//	Revision 1.3  2003/11/09 20:53:57  ralfoide
//	Fixed dir_abs_locset
//	
//	Revision 1.2  2003/08/21 20:14:10  ralfoide
//	New dir_variables, some made absolute, some renamed for clarity
//	
//	Revision 1.1  2003/08/18 02:10:13  ralfoide
//	Reorganazing
//	
//	Revision 1.3  2003/03/12 07:11:45  ralfoide
//	New upload dirs, new entry_point, new meta override
//	
//	Revision 1.2  2003/02/16 20:09:41  ralfoide
//	Update. Version 0.6.3.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
