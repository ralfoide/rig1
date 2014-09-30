<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: theme_khaki.php,v 1.8 2005/09/25 22:36:15 ralfoide Exp $

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

// RIG Theme: khaki


// --- css header ---

$theme_css_head			= '';


// --- page colors ---

$color_body_bg			= '#999966';
$color_body_text		= '#FFFFCC';
$color_body_link		= '#003333';
$color_body_alink		= '#CCCC99';
$color_body_vlink		= '#006666';

$color_title_bg			= '#003333';
$color_title_text		= '#FFFFCC';

$color_section_bg		= $color_title_bg;
$color_section_text		= $color_title_text;

$color_header_bg		= $color_section_bg;
$color_header_text		= $color_section_text;

$color_table_border		= $color_section_bg;
$color_table_bg			= $color_section_text;
$color_table_infos		= '#BBBBAA';
$color_table_desc		= $color_title_bg;

$color_image_border		= $color_table_bg;
$color_caption_bg		= $color_table_bg;
$color_caption_text		= '#003333';

$color_index_text		= $color_title_bg;
$color_warning_bg		= '#00CC66';

$color_error1_bg		= '#FF9966';
$color_error2_bg		= '#FFFF99';


// --- page colors for admin ---
// RM 20040712 red title for admin

if (isset($_GET['admin']) && $_GET['admin'])
{
	$color_title_bg			= '#BB0000';
}

// end

//-------------------------------------------------------------
//	$Log: theme_khaki.php,v $
//	Revision 1.8  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.7  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
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
