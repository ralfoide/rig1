<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2004, Raphael MOLL.

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

// Japanese Language Strings for RIG


// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

// Note that $html_encoding is defined in the data file (see below)
// For the content of $html_encoding, 	   cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'ja';				// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


// Current Locale
//---------------

// Lib-C locale, mainly used to generate dates and time with the correct language.
// On Debian, run 'dpkg-reconfigure locales' as root and make sure the locale is installed.
//
// 'ja_JP' doesn't really work for me but 'ja_JP.UTF-8' does.
// This is expected to be UTF-8 not ISO-8859 anyway.
// Better use the 'C' locale as a fallback in case everything else fails.


$lang_locale        = array('ja_JP.UTF-8', 'ja_JP', 'ja', 'C');



// Script Content
//---------------

// Date formatiing
// Date formating for $html_footer_date, $html_img_date and $html_album_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%c';

// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)

// Image date displayed
// Now using notation from http://www.php.net/manual/en/function.strftime.php
// $html_img_date		= '%c';
// Year  = 0x5E74 = 24180 decimal
// Month = 0x6708 = 26376
// Day   = 0x65E5 = 26085
// Hour  = 0x6642 = 26178
// Min.  = 0x5206 = 20998
// Second= 0x79D2 = 31186
// Note that %A does not work under Windows
if (PHP_OS == 'WINNT')
	$html_img_date		= '%Y&#24180;%m&#26376;%d&#26085;&nbsp;%H&#26178;%M&#20998;%S&#31186;';
else
	$html_img_date		= '%Y&#24180;%m&#26376;%d&#26085;&nbsp;%A&nbsp;%H&#26178;%M&#20998;%S&#31186;';


// Album date displayed
// cf http://www.php.net/manual/en/function.strftime.php

$html_album_date	= '%Y&#24180;&nbsp;%m&#26376;';
// The following works under Debian GNU/Linux but not Windows (%B is not recognized)
// $html_album_date	= '%Y&#24180; %B';


// Overriding prefs.php
//---------------------

// Format to display date in the album titles

$pref_date_YM		= 'M-Y';            // Short format. Must contain M & Y.
$pref_date_YMD      = 'Y-M-D';          // Long format.  Must contain D & M & Y.


// Parsing of the external data file
//----------------------------------

// At the site level you can decide which encoding you want to use for Japanse.
// Each data file describes its own encoding, uncomment the correct one. UTF-8
// is the preferred one.

rig_parse_string_data('data_jpu8.bin');
// rig_parse_string_data('data_jpjis.bin');
// rig_parse_string_data('data_jpsjis.bin');
// rig_parse_string_data('data_jpeuc.bin');


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.10  2004/07/17 07:52:31  ralfoide
//	GPL headers
//
//	Revision 1.9  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.8  2004/02/29 08:07:08  ralfoide
//	Real Japanese translations for video strings rom Tatsuo
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
