<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// RIG Theme: Egg (White and Yellow)


// --- css header ---

$theme_css_head			= '';


// --- page colors ---

$color_body_bg			= '#FFFFFF';
$color_body_text		= '#000000';
$color_body_link		= '#CC0000';
$color_body_alink		= '#000000';
$color_body_vlink		= '#0044AA';

$color_title_bg			= '#FFCC00';
$color_title_text		= '#CC0000';

$color_section_bg		= $color_title_bg;
$color_section_text		= '#000000';

$color_table_border		= $color_section_bg;
$color_table_bg			= '#FFFFCC';
$color_table_infos		= '#BBBBBB';

$color_header_bg		= $color_section_bg;
$color_header_text		= $color_section_text;
$color_table_desc		= $color_title_text;
$color_table_desc		= $color_title_text;

$color_table_border		= $color_section_bg;
$color_table_bg			= '#FFFFCC';

$color_image_border		= '#FFCC00';
$color_caption_bg		= $color_table_bg;
$color_caption_text		= $color_body_text;

$color_index_text		= '#800000';
$color_warning_bg		= '#00CC66';

$color_error1_bg		= '#FF9966';
$color_error2_bg		= '#FFFF99';


// --- page colors for admin ---
// RM 20040712 red title for admin

if (isset($_GET['admin']) && $_GET['admin'])
{
	$color_title_bg			= '#FF3333';
	$color_title_text		= '#330000';
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
//	Revision 1.4  2003/08/18 06:10:03  ralfoide
//	Moving on to 0.6.4.2
//	Added color_table_desc in themes for description and dates in album view.
//
//	[...]
//
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//-------------------------------------------------------------
?>
