<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// --- album & system-dependent locations ---

// ---- global settings ---
  
// base installation directory (absolute path)
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_install		= "/home/ralf/rig/";

// php sources: view sources and admin sources
// Note: $dir_abs_src semantic has changed! Both are *absolute* paths instead
// of being relative to $dir_abs_install.
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_src			= $dir_abs_install . "src/";
$dir_abs_admin_src		= $dir_abs_install . "admin/";

// global settings
// Note: $dir_abs_globset semantic has changed! Path is *absolute* instead of relative to $dir_abs_install
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_globset		= $dir_abs_install . "settings/";


// ---- local settings ---

// The site-album directory (i.e. _this_ directory in absolute)
// The 2 lines below automatically compute the absolute local file-system path
// to the entry point "index.php" file.
$dir_info_album			= pathinfo($PATH_TRANSLATED);
$dir_abs_album			= $dir_info_album["dirname"];

// local settings
// This directory is *always* relative to $dir_abs_album.
// The directory string must end with / (un*x) or \\ (windows)
$dir_abs_locset				= "./";


// ---- URL settings ---

// Relative-URL for rig images
// Physically, this is relative to $dir_abs_album (i.e. where index.php is)
// The URL string must end with /
$dir_images				= "rig-images/";

// Album location
// Physically, this is relative to $dir_abs_album (i.e. where index.php is)
// The URL string must end with /
$dir_album				= "my-photos/";
$dir_image_cache		= "rig-cache/";
$dir_album_cache		= "rig-cache/";
$dir_option				= "rig-options/";
$dir_comment			= "rig-options/";
$dir_vote				= "rig-options/";

// Upload locations
// Physically, this is relative to $dir_abs_album (i.e. where index.php is)
// The URL string must end with /
$dir_upload_src         = "upload-src/";
$dir_upload_album       = "upload-photos/";



// end

//-------------------------------------------------------------
//	$Log$
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
