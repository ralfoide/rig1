<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

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
$html_text_credits	= 'R\'alf Image Gallery (<a href="http://rig.powerpulsar.com">RIG</a>) &copy; 2001-2003 by R\'alf<br>';
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
$html_chg_user		= 'change user';
$html_guest_login	= '\'Guest\' Mode';

// RM 20030119 - v0.6.3
$html_album_copyrt	= 'All images &copy; [year] [name]';	// [name] will become $pref_copyright_name
$html_image_copyrt	= 'Image &copy; [year] [name]';		    // [name] will become $pref_copyright_name
$html_album_count	= '[count] albums';
$html_image_count	= '[count] images';


// Script Content
//---------------

// Date formatiing
// Date formating for $html_footer_date, $html_img_date and $html_album_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%m/%d/%Y, %I:%M %p';

// Album Title
$html_album			= 'Album';
$html_admin			= 'Admin';
$html_none			= 'Start';

// Current Album
$html_root			= 'Start';

// Images
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
//	$Log$
//	Revision 1.14  2003/07/23 01:19:13  ralfoide
//	Language: strings for tooltip details
//
//	Revision 1.13  2003/07/21 04:54:45  ralfoide
//	Added date format for album display; changed dates format to strftime (localizable); setting locale
//	
//	Revision 1.12  2003/06/15 19:09:49  ralfoide
//	Version 0.6.3.3: Japanese translation completed
//	
//	Revision 1.11  2003/05/26 17:51:08  ralfoide
//	Lang: Update jp/fr/es strings to match en, removed unused strings. Using latest jp translation file.
//	
//	Revision 1.10  2003/03/17 08:24:43  ralfoide
//	Fix: added pref_disable_web_translate_interface (disabled by default)
//	Fix: added pref_disable_album_borders (enabled by default)
//	Fix: missing pref_copyright_name in settings/prefs.php
//	Fix: outdated pref_album_copyright_name still present. Eradicated now :-)
//	
//	Revision 1.9  2003/02/21 09:03:03  ralfoide
//	Added gray theme color
//	
//	Revision 1.8  2003/02/16 20:22:57  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.7  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.6  2002/11/02 04:09:32  ralfoide
//	Fixes for URLs in international strings
//	
//	Revision 1.5  2002/10/23 16:01:01  ralfoide
//	Added <html lang>; now transmitting charset via http headers.
//	
//	Revision 1.4  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
//	Revision 1.3  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.4  2001/11/28 11:52:48  ralf
//	v0.6.1: display image last modification date
//	
//	Revision 1.3  2001/11/26 07:27:59  ralf
//	links from credits to license
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
