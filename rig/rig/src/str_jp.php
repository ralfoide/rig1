<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Japanese Language Strings for RIG



// Script Content
//---------------

// Date formatiing
// Date formating for $html_date and $html_img_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
$html_date			= 'h:i - m/d/Y';

// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)

// Image date displayed
$html_img_date		= 'l\, F d\, Y\, g:m A';


// Overriding prefs.php
//---------------------

// Format to display date in the album titles

$pref_date_YM		= 'M-Y';            // Short format. Must contain M & Y.
$pref_date_YMD      = 'Y-M-D';          // Long format.  Must contain D & M & Y.


// Parsing of the external data file
//----------------------------------


rig_parse_string_data('data_jpjis.bin');
// rig_parse_string_data('data_jpsjis.bin');
// rig_parse_string_data('data_jpeuc.bin');
// rig_parse_string_data('data_jpu8.bin');


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//-------------------------------------------------------------
?>
