<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// --- system-dependent prefs ---

/***********************************************************
 *
 *	Section:	system-dependent prefs
 *
 *	For new installations: most likely, you will not need to
 *	change anything in this section.
 *
 ***********************************************************/

if (PHP_OS == 'WINNT')
{
	/***********************************************************
	 *
	 *	Section:	Windows (Win32) specific settings.
	 *
	 *	For new installations: most likely, you will not need to
	 *	change anything in this section.
	 *
	 ***********************************************************/


	// --- rig-thumbnail.exe options ---

	/***********************************************************
	 *
	 *	Setting: 		$pref_preview_exec
	 *	Type:			File-system relative path with \\ separators
	 *	Relative to:	$dir_install
	 *	Default:		thumbnail\\Release\\rig_thumbnail.exe
	 *	
	 *	This path indicates where the rig_thumbnail.exe application
	 *	can be find. It has to be relative to the installation
	 *	directory. For a Windows path, use \\ as directory separator.
	 *	The path must NOT start by \\. 
	 ***********************************************************/

	$pref_preview_exec		= "thumbnail\\Release\\rig_thumbnail.exe";


	/***********************************************************
	 *
	 *	Setting: 		$pref_mkdir_mask
	 *	Type:			Octal mask
	 *	Default:		0777
	 *	
	 *	The mask used to create the various cache and option directories
	 *	for rig. Consult "man mkdir" on a Unix box or Cygwin for more info.
	 *	The default is 0777: "0" to make it an octal number. Then each 7
	 *	indicates full access rights for user/group/others.
	 *
	 ***********************************************************/

	$pref_mkdir_mask		= 0777;


	/***********************************************************
	 *
	 *	Setting: 		$pref_umask
	 *	Type:			Octal mask
	 *	Default:		0022
	 *	
	 *	The global mask for creating files (previews, thumbnails, options, etc.)
	 *	for rig. Consult "man umask" on a Unix box or Cygwin for more info which
	 *	mainly says "umask sets the umask to mask & 0777".
	 *	The default is 0022: "0" to make it an octal number. Then 022
	 *	to make it accessible only by the current user, not by group/others.
	 *
	 ***********************************************************/

	$pref_umask				= 0022;

	// --- customization of cookies ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_cookie_host
	 *	Type:			File-system relative path with \\ separators
	 *	Default:		Empty string ""
	 *	
	 *	The host used for cookies.
	 *	It is best to leave empty, in which case the host will be
	 *	figured out automatically.
	 *
	 ***********************************************************/

    $pref_cookie_host       = "";

	// --- pages rendering options ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_use_jhead
	 *	Type:			String
	 *	Default:		Empty string ""
	 *	
	 *	The path were jhead is located.
	 *	JHead is a nice tool to extract EXIF information from digital camera images.
	 *	Home page: http://www.sentex.net/~mwandel/jhead/
	 *
	 *	The default is an empty path since jhead is not generally installed
	 *	by default under Windows.
	 *
	 ***********************************************************/

	$pref_use_jhead			= "";
}
else // Un*x
{
	/***********************************************************
	 *
	 *	Section:	Un*x specific settings.
	 *
	 *	For new installations: most likely, you will not need to
	 *	change anything in this section.
	 *
	 ***********************************************************/

	// --- rig-thumbnail.exe options ---

	/***********************************************************
	 *
	 *	Setting: 		$pref_preview_exec
	 *	Type:			File-system relative path with / separators
	 *	Relative to:	$dir_install
	 *	Default:		thumbnail/rig_thumbnail.exe
	 *	
	 *	This path indicates where the rig_thumbnail.exe application
	 *	can be find. It has to be relative to the installation
	 *	directory. For a Unix path, use / as directory separator.
	 *	The path must NOT start by /. 
	 ***********************************************************/

	$pref_preview_exec		= "thumbnail/rig_thumbnail.exe";


	/***********************************************************
	 *
	 *	Setting: 		$pref_mkdir_mask
	 *	Type:			Octal mask
	 *	Default:		0777
	 *	
	 *	The mask used to create the various cache and option directories
	 *	for rig. Consult "man mkdir" for more info.
	 *	The default is 0777: "0" to make it an octal number. Then each 7
	 *	indicates full access rights for user/group/others.
	 *
	 ***********************************************************/

	$pref_mkdir_mask		= 0777;


	/***********************************************************
	 *
	 *	Setting: 		$pref_umask
	 *	Type:			Octal mask
	 *	Default:		0022
	 *	
	 *	The global mask for creating files (previews, thumbnails, options, etc.)
	 *	for rig. Consult "man umask" which mainly says 
	 *	"umask sets the umask to mask & 0777".
	 *	The default is 0022: "0" to make it an octal number. Then 022
	 *	to make it accessible only by the current user, not by group/others.
	 *
	 ***********************************************************/

	$pref_umask				= 0022;

	// --- customization of cookies ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_cookie_host
	 *	Type:			String
	 *	Default:		Empty string ""
	 *	
	 *	The host used for cookies.
	 *	It is best to leave empty, in which case the host will be
	 *	figured out automatically.
	 *
	 ***********************************************************/

    $pref_cookie_host       = "";

	// --- page rendering options ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_use_jhead
	 *	Type:			String
	 *	Default:		exec("which jhead")
	 *	
	 *	The path were jhead is located.
	 *	Use either exec("which jhead") or a path like "/usr/bin/jhead"
	 *
	 *	JHead is a nice tool to extract EXIF information from digital camera images.
	 *	Home page: http://www.sentex.net/~mwandel/jhead/
	 *
	 ***********************************************************/

	// The path to jhead or an empty string to disable it's usage.
	$pref_use_jhead			= exec("which jhead");
}



// --- DB-links options ---


/***********************************************************
 *
 *	Section:	Database options.
 *
 *	This version of RIG does not rely on database storage.
 *	These settings do not apply here and MUST NOT be modified!
 *
 ***********************************************************/

$pref_use_db			= FALSE;			// not for rig062 yet
$pref_use_db_id			= $pref_use_db;		// use ids rather than names internally
$pref_use_id_in_url		= $pref_use_db_id;	// use numeric ids in URLs rather than album/image names



// --- thumbnails creation ---


/***********************************************************
 *
 *	Section:	Thumbnail options.
 *
 *
 ***********************************************************/


$pref_preview_size		= 80;
$pref_preview_quality	= 70;
$pref_preview_timeout	= 10;
$pref_nb_col			= 5;



// --- image viewing options ---


/***********************************************************
 *
 *	Section:	Preview options.
 *
 *
 ***********************************************************/


$pref_image_size		= 512;
$pref_image_quality		= 75;
$pref_size_popup		= array(256, 300, 384, 400, 512, 640, 800, 1024, 1280, 1600);
$pref_empty_album		= "empty_album.gif";

$pref_global_gamma		= 1.0;	// use 1.0 for no-op


// --- supported file types [RM 20030627 v0.6.3.4] ---

// For matching pattern syntax, cf http://www.php.net/manual/en/function.preg-match.php
// or http://www.perldoc.com/perl5.8.0/pod/perlre.html

$pref_file_types		= array("/\.jpe?g$/i"					 => "image/jpeg",
								"/\.(avi|wmv|as[fx])$/i"		 => "video/avi",
								"/\.(mov|qt|sdp|rtsp)$/i"		 => "video/quicktime",
								"/\.(mpe?g[124]?|m[12]v|mp4)$/i" => "video/mpeg");
								

// --- admin viewing options ---

$pref_admin_size		= 256;



// --- login options ---


/***********************************************************
 *
 *	Section:	Login options.
 *
 *
 ***********************************************************/


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


// -------------
// Select the default image page layout.
// Default is "1". Current choices are "1" ro "2".
// WARNING: this is a TEMPORARY hack whilst waiting for more powerful template-based layout pages
// -------------
$pref_image_layout = "1";




// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.11  2003/07/14 18:29:12  ralfoide
//	Experimenting with better comments
//
//	Revision 1.10  2003/07/11 15:56:38  ralfoide
//	Fixes in video html tags. Added video/mpeg mode. Experimenting with Javascript
//	
//	Revision 1.9  2003/06/30 06:10:00  ralfoide
//	Introduced file-types (for video vs image support)
//	
//	Revision 1.8  2003/03/22 01:22:56  ralfoide
//	Fixed album/image count display in admin mode
//	Added "old" layout for image display, with image layout pref variable.
//	
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
