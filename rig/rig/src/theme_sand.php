<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// RIG Theme: sand


// --- css header ---

$theme_css_head			= '';


// --- page colors ---

$color_body_bg			= '#cc9933';
$color_body_text		= '#000000';
$color_body_link		= '#000099';
$color_body_alink		= '#000099';
$color_body_vlink		= '#990099';

$color_title_bg			= '#996633';
$color_title_text		= '#ffcc66';

$color_section_bg		= $color_title_bg;
$color_section_text		= '#ffff99';

$color_header_bg		= $color_title_bg;
$color_header_text		= $color_title_text;

$color_table_border		= '#000000';
$color_table_bg			= '#ffcc66';
$color_table_infos		= '#cc9933';
$color_table_desc		= $color_title_text;

$color_image_border		= $color_table_bg;
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
	$color_title_bg			= '#CC0000';
}

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.6  2004/07/14 06:20:12  ralfoide
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
