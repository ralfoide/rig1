<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: str_en.php,v 1.21 2005/09/25 22:36:15 ralfoide Exp $

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

// English Language Strings for RIG



// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'ISO-8859-1';		// cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'en-US';			// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


// Current Locale
//---------------

// Lib-C locale, mainly used to generate dates and time with the correct language.
// On Debian, run 'dpkg-reconfigure locales' as root and make sure the locale is installed.
//
// Neither 'en' nor 'en_EN' work for me in English. Using 'C' instead as fallback.
// This is expected to be be ISO-8859 not UTF-8

$lang_locale        = array('en_US', 'en_EN', 'en', 'C');


// Languages availables
//---------------------

$html_language		= 'Language:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '&#26085;&#26412;&#35486;'
							);

// Themes available
//-----------------

$html_theme			= 'Color Theme:';
$html_desc_theme	= array('gray'  => 'Gray',
							'blue'  => 'Blue',
							'sand'  => 'Sand',
							'khaki' => 'Khaki',
							'egg'	=> 'Egg',
							'none'	=> 'None');


// HTML content
//-------------

$html_current_album	= 'Current Album';
$html_albums		= 'Available Albums';
$html_images		= 'Images';
$html_options		= 'Options';

$html_generated		= 'Generated in [time] seconds the <i>[date]</i> by <i>[rig-version]</i>';

$html_admin_intrfce	= 'Administration Interface';

$html_rig_admin		= 'RIG Administration Interface';
$html_comment_stats	= 'Stats for album and sub-albums:';
$html_album_stat	= '[bytes] bytes used by [files] files in [folders] folders';

$html_actions		= 'Actions';
// RM 20030120 splitting Mk/Del / All Previews / All Images / Both
$html_act_create	= 'Create All:';
$html_act_delete	= 'Delete All:';
$html_act_previews	= 'Previews';
$html_act_images	= 'Images';
$html_act_prev_img	= 'Previews &amp; Images';
$html_act_htmlcache	= 'HTML Caches';	// RM 20040711 TBT
$html_act_rnd_prev	= 'Change Album Random Icon';
$html_act_canon		= 'Rename Canon 100-1234_img.jpg files';

$html_use_as_icon	= 'Use as album icon';
$html_rename_image	= 'Rename Image';
$html_set_desc		= 'Set Description';
$html_avail_albums	= 'Available Albums';
$html_comment		= 'You may need to reload this page to see the real images.';
$html_back_to		= 'Back to [name]';
$html_back_album	= 'Back to album';
$html_back_previous	= 'Back to previous album';
$html_vis_on		= 'Show';
$html_vis_off		= 'Hide';

$html_credits		= 'Credits';
$html_show_credits	= 'Display RIG & PHP Credits';
$html_hide_credits	= 'Hide Credits';
$html_text_credits	= 'R\'alf Image Gallery ([rig-name-url]) &copy; 2001-2003 by R\'alf<br>';
$html_text_credits .= 'RIG is diffused under the terms of the <a href="LICENSE.html">RIG license</a> (<a href="http://www.opensource.org/licenses/">OSL</a>).<br>';
$html_text_credits .= 'Based on <a href="http://www.php.net">PHP</a> and ';
$html_text_credits .= 'the <a href="ftp://ftp.uu.net/graphics/jpeg">JpegLib</a>.<br>';

$html_phpinfo		= 'PHP Server Information';
$html_show_phpinfo	= 'Display PHP Server Information';
$html_hide_phpinfo	= 'Hide PHP Server Information';

$html_login			= 'Login';
$html_validate		= 'Enter';
$html_remember		= 'Remember me';
$html_username		= 'Username';
$html_password		= 'Password';
$html_welcome		= 'Welcome <b>[name]</b>! ([change-link])';
$html_welcome_guest	= 'Welcome! ([change-link])';				// RM 20040222 0.6.4.5
$html_chg_user		= 'change user';
$html_guest_login	= '\'Guest\' Mode';

// RM 20030119 - v0.6.3
$html_album_copyrt	= 'All images &copy; [year] [name]';		// [name] will become $pref_copyright_name
$html_image_copyrt	= 'Image &copy; [year] [name]';		   		// [name] will become $pref_copyright_name
$html_album_count	= '[count] albums';
$html_image_count	= '[count] images';

// RM 20040222 - v0.6.4.5
$html_video_codec_detail			= "Video format: <i>[codec_name]</i>";
$html_video_install_named_player	= "[&nbsp;<a href=\"[url]\">Install&nbsp;[name]</a>&nbsp;] ";
$html_video_install_unnamed_player	= "[&nbsp;<a href=\"[url]\">Install&nbsp;the&nbsp;player</a>&nbsp;]";
$html_video_download				= "[&nbsp;<a title=\"Download video and play on your computer\" href=\"[url]\">Download</a>&nbsp;]";


// Script Content
//---------------



// Date formatiing
// Date formating for $html_footer_date, $html_img_date and $html_album_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%m/%d/%Y, %I:%M %p';

// Album Title
$html_album_title	= 'RIG Album';
$html_album			= 'Album';
$html_admin			= 'Admin';
$html_none			= 'Start';

// Current Album
$html_root			= 'Start';

// Images
$html_image_title	= 'RIG Image';
$html_image			= 'Image';
$html_prev			= 'Previous';
$html_next			= 'Next';
$html_image2		= 'image';
$html_pixels		= 'pixels';
$html_ok			= 'Change';
$html_img_size		= 'Image Size';
$html_original		= 'Original';

// Tooltips
$html_image_tooltip	= '[type]: [name]';
$html_album_tooltip	= '[type]: [name]; Last Updated: [date]';
$html_last_update   = 'Last Updated: [date]';


// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)


// Image date displayed
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_img_date		= '%A %B %d %Y, %I:%M %p'; //l\, F d\, Y\, g:m A';

// Album date displayed
// cf http://www.php.net/manual/en/function.strftime.php
$html_album_date	= '%B %Y';

// end

//-------------------------------------------------------------
//	$Log: str_en.php,v $
//	Revision 1.21  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.20  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.19  2004/07/14 06:19:50  ralfoide
//	Admin option to clean HTML caches
//	
//	Revision 1.18  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.17  2004/03/02 10:38:01  ralfoide
//	Translation of tooltip string.
//	New page title strings.
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
