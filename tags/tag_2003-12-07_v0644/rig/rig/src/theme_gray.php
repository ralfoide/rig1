<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// RIG Theme: gray


// --- css header ---

$theme_css_head			= '';


// --- page colors ---

$color_body_bg			= '#FFFFFF';
$color_body_text		= '#000000';
$color_body_link		= '#000099';
$color_body_alink		= '#000099';
$color_body_vlink		= '#990099';

$color_title_bg			= '#CCCCCC';
$color_title_text		= '#6666CC';

$color_section_bg		= $color_title_bg;
$color_section_text		= $color_title_text;

$color_header_bg		= $color_title_bg;
$color_header_text		= $color_title_text;

$color_table_border		= $color_title_bg;
$color_table_bg			= '#FFFFFF';
$color_table_infos		= '#BBBBBB';
$color_table_desc		= $color_title_text;

$color_image_border		= $color_title_bg;
$color_caption_bg		= $color_title_bg;
$color_caption_text		= $color_body_text;

$color_index_text		= '#800000';
$color_warning_bg		= '#00CC66';

$color_error1_bg		= '#FF9966';	// '#FFFF33';
$color_error2_bg		= '#FFFF99';	// '#FFFF33';

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2003/08/18 06:10:03  ralfoide
//	Moving on to 0.6.4.2
//	Added color_table_desc in themes for description and dates in album view.
//
//	Revision 1.1  2003/02/21 09:03:03  ralfoide
//	Added gray theme color
//	
//	Revision 1.3  2003/02/16 20:22:58  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.2  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//-------------------------------------------------------------
?>
