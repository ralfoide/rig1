<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// RIG Theme: blue


// --- css header ---

$theme_css_head			= '';


// --- page colors ---

$color_body_bg			= '#99CCFF';
$color_body_text		= '#000000';
$color_body_link		= '#000099';
$color_body_alink		= '#000099';
$color_body_vlink		= '#990099';

$color_title_bg			= '#3399FF';
$color_title_text		= '#000000';

$color_section_bg		= $color_title_bg;
$color_section_text		= $color_title_text;

$color_header_bg		= '#3399FF';
$color_header_text		= '#FFFFCC';

$color_table_border		= '#000000';
$color_table_bg			= '#FFFFFF';
$color_table_infos		= '#BBBBBB';
$color_table_desc		= $color_title_text;

$color_image_border		= $color_table_bg;
$color_caption_bg		= $color_table_bg;
$color_caption_text		= $color_body_text;

$color_index_text		= '#800000';
$color_warning_bg		= '#00CC66';

$color_error1_bg		= '#FF9966';	// '#FFFF33';
$color_error2_bg		= '#FFFF99';	// '#FFFF33';


// --- page colors for admin ---
// RM 20040712 red title for admin

if (isset($_GET['admin']) && $_GET['admin'])
{
	$color_title_bg			= '#FF3333';
}


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.6  2004/07/14 06:20:13  ralfoide
//	Red title in admin mode to be more obvious
//
//	Revision 1.5  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.4  2003/08/18 06:10:02  ralfoide
//	Moving on to 0.6.4.2
//	Added color_table_desc in themes for description and dates in album view.
//
//	[...]
//
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//-------------------------------------------------------------
?>
