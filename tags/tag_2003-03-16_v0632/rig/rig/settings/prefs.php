<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// --- system-dependent prefs ---

if (PHP_OS == 'WINNT')
{
	// --- rig-thumbnail.exe options ---
	$pref_preview_exec		= "thumbnail\\Release\\rig_thumbnail.exe";
	$pref_mkdir_mask		= 0777;
	$pref_umask				= 0022;

	// --- customization of cookies ---
	// cookie hostname (defaults to empty if not set)
    $pref_cookie_host       = "";

	// --- pages rendering options ---
	// The path to jhead or an empty string to disable it's usage.
	// Disabled by default under Windows.
	$pref_use_jhead			= "";
}
else // Un*x
{
	// --- rig-thumbnail.exe options ---
	$pref_preview_exec		= "thumbnail/rig_thumbnail.exe";
	$pref_mkdir_mask		= 0777;
	$pref_umask				= 0022;

	// --- customization of cookies ---
	// cookie hostname (defaults to empty if not set)
    $pref_cookie_host       = "";

	// --- page rendering options ---
	// The path to jhead or an empty string to disable it's usage.
	// Use either exec("which jhead") or a path like "/usr/bin/jhead"
	$pref_use_jhead			= exec("which jhead");
}



// --- DB-links options ---

$pref_use_db			= FALSE;			// not for rig062 yet
$pref_use_db_id			= $pref_use_db;		// use ids rather than names internally
$pref_use_id_in_url		= $pref_use_db_id;	// use numeric ids in URLs rather than album/image names

// --- thumbnails creation ---

$pref_preview_size		= 80;
$pref_preview_quality	= 70;
$pref_preview_timeout	= 10;
$pref_nb_col			= 5;

// --- image viewing options ---

$pref_image_size		= 512;
$pref_image_quality		= 75;
$pref_size_popup		= array(256, 300, 384, 400, 512, 640, 800, 1024, 1280, 1600);
$pref_empty_album		= "empty_album.gif";

$pref_global_gamma		= 1.0;	// use 1.0 for no-op

// --- admin viewing options ---

$pref_admin_size		= 256;



// --- login options ---

$pref_allow_guest		= TRUE;				// can be TRUE (default) or FALSE
$pref_auto_guest		= FALSE;			// FALSE will force login, TRUE will auto-log as guest
$pref_guest_username	= "guest";			// must be in the user_list.txt file


// --- default language & theme ---

$pref_default_lang		= 'en';				// choices are en, fr, sp, jp
$pref_default_theme		= 'blue';			// choices are blue, gray, khaki, egg, sand


// --- dates at beginning of album names ---

$pref_date_YM						= 'M/Y';	// format for short dates. M & Y must appear.
/* American */ $pref_date_YMD		= 'M/D/Y';	// format for long dates. D & M & Y must appear.
/* Japanese */ // $pref_date_YMD	= 'Y/M/D';	// format for long dates. D & M & Y must appear.
/* French   */ // $pref_date_YMD	= 'D/N/Y';	// format for long dates. D & M & Y must appear.
$pref_date_sep						= ' - ';	// separator between date and description

// ---- Copyright Name for albums & images ----

// Format is HTML. Use HTML-compliant characters (like &eacute; or &#129;)
// Important: if you want to insert Japanese here, add a line in data_jpu8.bin
// or use UTF-8 bytes directly in hexa.
// This should ideally be overriden by album-specific prefs.php files

$pref_copyright_name = '';


// --- meta tags for album/image pages ---
// Each album's pref can override this. The default is here.

$pref_html_meta = "<meta name=\"robots\" content=\"noindex, nofollow\">";


// --- Global display preferences ---
// WARNING: these are mostly development hacks that will be removed later!!!

// -------------
// Disable album thumbnails borders. Necessary for Mozilla 1.0 lovers, since it doesn't render the
// actualy table correctly. Will be fixed later (Note that Mozilla 1.2 works just fine!)
// -------------
// Default: line commented or value 0. Uncomment and set to 1 to disable image border.

$pref_disable_album_borders = 0;


// -------------
// Disable web-interface for translating language.
// This is the site-wide setting. If you want to enable it, please do so in the album prefs.php!
// -------------
// Default: line with value 1. Set to 0 to enable language translation by admins directly in the web interface.

$pref_disable_web_translate_interface = 1;



// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.7  2003/03/17 08:24:42  ralfoide
//	Fix: added pref_disable_web_translate_interface (disabled by default)
//	Fix: added pref_disable_album_borders (enabled by default)
//	Fix: missing pref_copyright_name in settings/prefs.php
//	Fix: outdated pref_album_copyright_name still present. Eradicated now :-)
//
//	Revision 1.6  2003/03/12 07:03:16  ralfoide
//	Prefs can override <meta> in album/image display
//	
//	Revision 1.5  2003/02/16 20:10:35  ralfoide
//	Update. Version 0.6.3.1
//	
//	Revision 1.4  2003/01/07 17:54:03  ralfoide
//	Moved URL-Rewrite conf array from global pref file to album-local pref file
//	
//	Revision 1.3  2002/10/21 01:51:36  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.2  2002/10/20 11:48:42  ralfoide
//	jhead support
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.2  2001/11/26 04:35:17  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
