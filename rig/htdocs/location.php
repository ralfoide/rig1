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

/***********************************************************
 *
 *  Type:			Macro
 *
 *  Requirement:	Please do not modify this define.
 *
 *  "S" is a macro you can use at the end of the path settings
 *  below to add a final terminator depending on your system
 *  (that is \\ on windows and / on linux).
 *  
 ***********************************************************/


if (PHP_OS == 'WINNT')
	define("S", "\\");
else // Un*x
	define("S", "/");

// ---- global settings ---

/***********************************************************
 *
 *  Setting: 		$dir_abs_install
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		None
 *
 *  Requirement:	You MUST edit this to match your installation.
 *  
 *  This setting contains the *absolute* base installation directory.
 *  It indicates where the global site scripts and settings are installed.
 *
 *  Security: You may want this path to be outside of the document root,
 *  i.e. not directly served by your web server.
 *  This directory as well as all files whithin should be readable by your
 *  web server and ideally not-writable by the web server.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

// base installation directory (absolute path)
$dir_abs_install		= "/opt/rig-thumbnail" . S;



/***********************************************************
 *
 *  Setting: 		$dir_abs_src
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		$dir_abs_install . "rig". S . "src" . S
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *absolute* path to the non-administrator
 *  sources (that is the normal image/album views.)
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_abs_src			= $dir_abs_install . "rig". S . "src" . S;



/***********************************************************
 *
 *  Setting: 		$dir_abs_admin_src
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		$dir_abs_install . "rig". S . "admin" . S
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *absolute* path to the administrator
 *  sources (that is the pages to hide/show albums & images.)
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_abs_admin_src		= $dir_abs_install . "rig". S . "admin" . S;



/***********************************************************
 *
 *  Setting: 		$dir_abs_templates
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		$dir_abs_install . "rig". S . "templates" . S
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *absolute* path to the templates.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_abs_templates		= $dir_abs_install . "rig". S . "templates" . S;



/***********************************************************
 *
 *  Setting: 		$dir_abs_globset
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		$dir_abs_install . "rig". S . "settings" . S
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *absolute* path to the global site settings.
 *  This is used to read the global site preferences as well as the users
 *  and admin users names & passwords.
 *
 *  Security: You may want this path to be outside of the DocumentRoot,
 *  i.e. not directly served by your web server.
 *  This directory as well as all files whithin should be readable by your
 *  web server and ideally not-writable by the web server.
 *
 *  Note: Local preference settings (located in $dir_abs_locset below)
 *  will override any global preferences settings.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_abs_globset		= $dir_abs_install . "rig". S . "settings" . S;


// ---- local settings ---



/***********************************************************
 *
 *  Setting: 		$dir_abs_base
 *  Type:			Automatically generated absolute path to the main
 *  				"index.php" when running this script.
 *  Default:		A pathinfo/PATH_TRANSLATED that automatically detects
 *  				the absolute path of this script as indicated by PHP.
 *
 *  Requirement:	No need to edit this. The default value should work.
 *
 *  This setting contains the *absolute* path to the "index.php".
 *
 *  Security: You may want this path to be outside of the document root,
 *  i.e. not directly served by your web server.
 *  This directory as well as all files whithin should be readable by your
 *  web server and ideally not-writable by the web server.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_info_base			= pathinfo($_SERVER['PATH_TRANSLATED']);
$dir_abs_base			= $dir_info_base["dirname"];



/***********************************************************
 *
 *  Setting: 		$dir_abs_locset
 *  Type:			File-system absolute path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Default:		$dir_abs_base
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *absolute* path to this album-specific local
 *  settings.
 *
 *  If this path is set to an empty string ("") there are no local settings
 *  and by default all settings will be read from the global settings
 *  directory as specified by $dir_abs_globset.
 *
 *  Security: You may want this path to be outside of the DocumentRoot,
 *  i.e. not directly served by your web server.
 *  This directory as well as all files whithin should be readable by your
 *  web server and ideally not-writable by the web server.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_abs_locset				= $dir_abs_base;


// ---- Data settings ---



/***********************************************************
 *
 *  Setting: 		$dir_abs_locset
 *  Type:			File-system relative path with native separators
 *  				(that is \\ on windows and / on linux).
 *  Relative to:	$dir_abs_base (where "index.php" is located)
 *  Default:		"rig-images" . S
 *
 *  Requirement:	No need to edit this. The default value should work.
 *  
 *  This setting contains the *relative* path to rig default icons.
 *
 *  The macros helper ". S" at the end adds the necessary ending separator
 *  (that is \\ on windows and / on linux).
 *
 ***********************************************************/

$dir_images				= "rig-images" . S;



/***********************************************************
 *
 *  Setting: 		$dir_url_album
 *  Type:			Relative path.
 *  Relative to:	$dir_abs_base (where "index.php" is located)
 *  Default:		"my-photos"
 *
 * OR
 *
 *  Setting: 		$dir_abs_album
 *  Type:			File-system absolute path.
 *  Default:		""
 *
 *  Requirement:	You should edit this or at least review it carefully.
 *
 *  This setting specify where to read the photos albums on your
 *  system. You can choose either a path relative to the base directory
 *  or an absolute path. If both settings are set, the absolute path will be
 *  be used.
 *
 *  Important: If both settings are set, they must describe the same physical
 *  location. It is strongly suggest to set only one of these two settings and
 *  to set the unused one to an empty string.
 *
 *  Security: It is recommended to use absolute path. Ideally you may want this
 *  path to be outside of the DocumentRoot, i.e. not directly served by your
 *  web server. This directory as well as all files whithin should be readable
 *  by your web server and ideally not-writable by the web server.
 *
 ***********************************************************/
 
$dir_url_album			= "my-photos/";
$dir_abs_album			= "";



/***********************************************************
 *
 *  Setting: 		$dir_url_image_cache
 *  Type:			Relative path.
 *  Relative to:	$dir_abs_base (where "index.php" is located)
 *  Default:		"rig-cache"
 *
 * OR
 *
 *  Setting: 		$dir_abs_image_cache
 *  Type:			File-system absolute path.
 *  Default:		""
 *
 *  Requirement:	You should edit this or at least review it carefully.
 *
 *  This setting specify where to store the local caches for your images.
 *  You can choose either a path relative to the base directory
 *  or an absolute path. If both settings are set, the absolute path will be
 *  be used.
 *  It is possible for this path to point to the same physical location than
 *  the $dir_abs_album or $dir_abs_options but this is highly NOT recommended.
 *  On the other hand, it is recommended to use the same setting than for the
 *  album cache.
 *
 *  Important: If both settings are set, they must describe the same physical
 *  location. It is strongly suggest to set only one of these two settings and
 *  to set the unused one to an empty string.
 *
 *  Security: It is recommended to use absolute path. Ideally you may want this
 *  path to be outside of the DocumentRoot, i.e. not directly served by your
 *  web server. This directory as well as all files whithin should be readable
 *  and writable by your web server.
 *
 ***********************************************************/

$dir_url_image_cache	= "rig-cache/";
$dir_abs_image_cache	= "";



/***********************************************************
 *
 *  Setting: 		$dir_url_album_cache
 *  Type:			Relative path.
 *  Relative to:	$dir_abs_base (where "index.php" is located)
 *  Default:		$dir_url_image_cache
 *
 * OR
 *
 *  Setting: 		$dir_abs_album_cache
 *  Type:			File-system absolute path.
 *  Default:		$dir_abs_image_cache
 *
 *  Requirement:	You should edit this or at least review it carefully.
 *
 *  This setting specify where to store the local caches for your albums.
 *  You can choose either a path relative to the base directory
 *  or an absolute path. If both settings are set, the absolute path will be
 *  be used.
 *  It is possible for this path to point to the same physical location than
 *  the $dir_abs_album or $dir_abs_options but this is highly NOT recommended.
 *  On the other hand, it is recommended to use the same setting than for the
 *  image cache.
 *
 *  Important: If both settings are set, they must describe the same physical
 *  location. It is strongly suggest to set only one of these two settings and
 *  to set the unused one to an empty string.
 *
 *  Security: It is recommended to use absolute path. Ideally you may want this
 *  path to be outside of the DocumentRoot, i.e. not directly served by your
 *  web server. This directory as well as all files whithin should be readable
 *  and writable by your web server.
 *
 ***********************************************************/

$dir_url_album_cache	= $dir_url_image_cache;
$dir_abs_album_cache	= $dir_abs_image_cache;



/***********************************************************
 *
 *  Setting: 		$dir_url_option
 *  Type:			Relative path.
 *  Relative to:	$dir_abs_base (where "index.php" is located)
 *  Default:		"my-photos"
 *
 * OR
 *
 *  Setting: 		$dir_abs_option
 *  Type:			File-system absolute path.
 *  Default:		""
 *
 *  Requirement:	You should edit this or at least review it carefully.
 *
 *  This setting specify where to store the options for your albums
 *  that is preferences you can set using the online administration mode such
 *  as which album or image is hidden or visible or which thumbnail to use for
 *  alums. You can choose either a path relative to the base directory
 *  or an absolute path. If both settings are set, the absolute path will be
 *  be used.
 *  It is possible for this path to point to the same physical location than
 *  the $dir_abs_album or $dir_abs_image_cache or $dir_abs_album_cache yet this
 *  this is not recommended. Separating options from images and caches make it
 *  easier to reset options without affecting the others.
 *
 *  Important: If both settings are set, they must describe the same physical
 *  location. It is strongly suggest to set only one of these two settings and
 *  to set the unused one to an empty string.
 *
 *  Security: It is recommended to use absolute path. Ideally you may want this
 *  path to be outside of the DocumentRoot, i.e. not directly served by your
 *  web server. This directory as well as all files whithin should be readable
 *  and writable by your web server.
 *
 ***********************************************************/

$dir_url_option			= "rig-options/";
$dir_abs_option			= "";



// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.10  2005/11/27 19:10:32  ralfoide
//	Fixed locset
//
//	Revision 1.9  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//	
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
