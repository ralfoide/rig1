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
$dir_install			= "/home/ralf/rig/";
// php sources
$dir_src				= "src/";
// global settings
$dir_globset			= "settings/";

// ---- local settings ---

// the site-album directory (i.e. _this_ directory in absolute)
$dir_info_album			= pathinfo($PATH_TRANSLATED);
$dir_abs_album			= $dir_info_album["dirname"];
// relative-url for rig images
$dir_images				= "rig-images/";
// local settings
$dir_locset				= "";
// album location
$dir_album				= "my-photos/";
$dir_preview			= "rig-cache/";
$dir_option				= "rig-options/";

// upload locations
$dir_upload_src         = "upload_src/";
$dir_upload_album       = "upload_photos/";



// end

//-------------------------------------------------------------
//	$Log$
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
