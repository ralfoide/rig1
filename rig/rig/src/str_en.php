<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
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
$html_desc_theme	= array('blue'  => 'Blue',
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
$html_mk_previews	= 'Create All Previews';
$html_rm_previews	= 'Delete All Previews';
$html_rand_previews	= 'Change Album Random Icon';
$html_rename_canon	= 'Rename Canon 100-1234_img.jpg files';
$html_hide_album	= 'Hide Album';
$html_show_album	= 'Show Album';
$html_use_as_icon	= 'Use as album icon';
$html_rename_image	= 'Rename Image';
$html_set_desc		= 'Set Description';
$html_avail_albums	= 'Available Albums';
$html_avail_prevws	= 'Available Previews';
$html_comment1		= 'You may need to reload this page to see the real images.';
$html_comment2		= 'Click on images to access image-specific options.';
$html_back_to		= 'Back to';
$html_back_album	= 'Back to album';
$html_back_previous	= 'Back to previous album';
$html_hidden		= 'Hidden';
$html_vis_on		= 'Show';
$html_vis_off		= 'Hide';

$html_credits		= 'Credits';
$html_show_credits	= 'Display RIG & PHP Credits';
$html_hide_credits	= 'Hide Credits';
$html_text_credits	= '<a href=\"http://rig.powerpulsar.com\">RIG</a> &copy; 2001 by R\'alf<br>';
$html_text_credits .= 'RIG is diffused under the terms of the <a href=\"LICENSE.html\">RIG license</a>.<br>';
$html_text_credits .= 'Based on <a href=\"http://www.php.net\">PHP</a> and ';
$html_text_credits .= 'the <a href=\"ftp://ftp.uu.net/graphics/jpeg\">JpegLib</a>.<br>';

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

// Script Content
//---------------

// Date formatiing
// Date formating for $html_date and $html_img_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
$html_date			= 'm/d/Y \a\\t h:i a';

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

// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)


// Image date displayed
$html_img_date		= 'l\, F d\, Y\, g:m A';

// end

//-------------------------------------------------------------
//	$Log$
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
