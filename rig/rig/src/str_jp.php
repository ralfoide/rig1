<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Japanese Language Strings for RIG



// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'Shift_JIS';


// Languages available
//--------------------

$html_language		= 'Language:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '“ú–{Œê');	// Shift-JIS
//							'jp' => '$BF|K\8l(B');	// ISO-2022-JP
//							'jp' => 'æ—¥æœ¬èªž');		// UTF-8

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

$html_current_album	= "Current Album";
$html_albums		= "Available ‚Ð‚ç‚ª‚È Albums";
$html_images		= "Images";
$html_options		= "Options";

$html_generated		= "Generated in";
$html_seconds		= "seconds";
$html_the			= "the";
$html_by			= "by";

$html_admin_intrfce	= "Administration Interface";

$html_rig_admin		= "RIG Administration Interface";
$html_comment_stats	= "Stats for album and sub-albums:";
$html_album_stat	= "%d bytes occupied by %d files in %d folders";
$html_actions		= "Actions";
$html_mk_previews	= "Create All Previews";
$html_rm_previews	= "Delete All Previews";
$html_rand_previews	= "Change Album Random Icon";
$html_rename_canon	= "Rename Canon 100-1234_img.jpg files";
$html_hide_album	= "Hide Album";
$html_show_album	= "Show Album";
$html_use_as_icon	= "Use as album icon";
$html_rename_image	= "Rename Image";
$html_set_desc		= "Set Description";
$html_avail_albums	= "Available Albums";
$html_avail_prevws	= "Available Previews";
$html_comment1		= "You may need to reload this page to see the real images.";
$html_comment2		= "Click on images to access image-specific options.";
$html_back_to		= "Back to";
$html_back_album	= "Back to album";
$html_back_previous	= "Back to previous album";
$html_hidden		= "Hidden";
$html_vis_on		= "Show";
$html_vis_off		= "Hide";

$html_credits		= "Credits";
$html_show_credits	= "Display RIG & PHP Credits";
$html_hide_credits	= "Hide Credits";
$html_text_credits	= "<a href=\"http://rig.powerpulsar.com\">RIG</a> &copy; 2001 by R'alf<br>";
$html_text_credits .= "RIG is diffused under the terms of the <a href=\"LICENSE.html\">RIG license</a>.<br>";
$html_text_credits .= "Based on <a href=\"http://www.php.net\">PHP</a> and ";
$html_text_credits .= "the <a href=\"ftp://ftp.uu.net/graphics/jpeg\">JpegLib</a>.<br>";

$html_phpinfo		= "PHP Server Information";
$html_show_phpinfo	= "Display PHP Server Information";
$html_hide_phpinfo	= "Hide PHP Server Information";

$html_login			= "Login";
$html_validate		= "Enter";
$html_remember		= "Remember me";
$html_username		= "Username";
$html_password		= "Password";
$html_welcome		= "Welcome <b>%s</b>! (%s)";
$html_chg_user		= "change user";
$html_guest_login	= "'Guest' Mode";

// Script Content
//---------------

// Date formatiing
// Date formating for $html_date and $html_img_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
$html_date			= "h:i a m/d/Y";

// Album Title
$html_album			= "Album";
$html_admin			= "Admin";
$html_none			= "Start";

// Current Album
$html_root			= "Start";

// Images
$html_image			= "Image";
$html_prev			= "Previous";
$html_next			= "Next";
$html_image2		= "image";
$html_pixels		= "pixels";
$html_ok			= "Change";
$html_img_size		= "Image Size";
$html_original		= "Original";

// Image date displayed
$html_img_date		= "l\, F d\, Y\, g:m A";


// Modifications de prefs.php
//---------------------------

// affichage de la date au debut des noms d'albums

$pref_date_YM		= "M-Y";            // format court. Doit contenir M & Y.
$pref_date_YMD      = "Y-M-D";          // format long.  Doit contenir D & M & Y.


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//	
//-------------------------------------------------------------
?>
