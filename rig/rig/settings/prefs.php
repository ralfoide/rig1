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
	 *	Default:		Empty string ''
	 *	
	 *	The host used for cookies.
	 *	It is best to leave empty, in which case the host will be
	 *	figured out automatically.
	 *
	 ***********************************************************/

    $pref_cookie_host       = '';

	// --- pages rendering options ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_use_jhead
	 *	Type:			String
	 *	Default:		Empty string ''
	 *	
	 *	The path were jhead is located.
	 *	JHead is a nice tool to extract EXIF information from digital camera images.
	 *	Home page: http://www.sentex.net/~mwandel/jhead/
	 *
	 *	The default is an empty path since jhead is not generally installed
	 *	by default under Windows.
	 *
	 ***********************************************************/

	$pref_use_jhead			= '';

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
	 *	Default:		Empty string ''
	 *	
	 *	The host used for cookies.
	 *	It is best to leave empty, in which case the host will be
	 *	figured out automatically.
	 *
	 ***********************************************************/

    $pref_cookie_host       = '';


	// --- page rendering options ---


	/***********************************************************
	 *
	 *	Setting: 		$pref_use_jhead
	 *	Type:			String
	 *	Default:		exec('which jhead')
	 *	
	 *	The path were jhead is located.
	 *	Use either exec('which jhead') or a path like '/usr/bin/jhead'
	 *
	 *	JHead is a nice tool to extract EXIF information from digital camera images.
	 *	Home page: http://www.sentex.net/~mwandel/jhead/
	 *
	 ***********************************************************/

	$pref_use_jhead			= exec('which jhead');
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

// RM 20030720
// size and quality of small preview (used for vertical album layout)
$pref_small_preview_size	= 64;
$pref_small_preview_quality	= 60;


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
$pref_empty_album		= 'empty_album.gif';

$pref_global_gamma		= 1.0;	// use 1.0 for no-op


// --- supported file types [RM 20030627 v0.6.3.4] ---

// For matching pattern syntax, cf http://www.php.net/manual/en/function.preg-match.php
// or http://www.perldoc.com/perl5.8.0/pod/perlre.html

$pref_file_types		= array("/\.jpe?g$/i"					 => 'image/jpeg',
								"/\.(avi|wmv|as[fx])$/i"		 => 'video/avi',
								"/\.(mov|qt|sdp|rtsp)$/i"		 => 'video/quicktime',
								"/\.(mpe?g[124]?|m[12]v|mp4)$/i" => 'video/mpeg');
								

// --- files and albums ignore list [RM 20030813 v0.6.3.5] ---

// TBDL: comment on this
// Quick notes: these arrays list reg-exp patterns of names to avoid for images and albums.
// The ignore list is taken into account when reading the filesystem's content. That means
// that filenames ignored here are never seen *ever* (not even in admin mode!) and are
// never accessed by RIG. As far as RIG is concerned, they do not exist.
//
// Arrays can be affected NULL or FALSE or the empty string or the empty array if you want to
// disable the feature.
//
// Typical pattenrs:
// - "/^foo$/" matched a filename being _exactly_ "foo"
// - "/^foo.*/" matches any filename starting by "foo".
// - "/\bfoo\b/" matches any filename with the word "foo" (word-boundary check)
// - "/foo/i" matches any filename containing "foo" or "FOO" or "FoO" anywhere in the name
//   (i means case-insenstive)

$pref_album_ignore_list	= NULL;
$pref_image_ignore_list	= NULL;

/* Example:

$pref_album_ignore_list	= array("/^CVS$/",
								"/AlbumToBeIgnored/");

$pref_image_ignore_list	= array("/fuzzy/",
								"/ImageToBeIgnored/",
								"/^temp_/i");
*/



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
$pref_guest_username	= 'guest';			// must be in the user_list.txt file


// --- default language & theme ---

$pref_default_lang		= 'en';				// choices are en, fr, sp, jp
$pref_default_theme		= 'blue';			// choices are blue, gray, khaki, egg, sand


// --- dates at beginning of album names ---

$pref_date_YM						= 'M/Y';	// format for short dates. M & Y must appear.
/* American */ $pref_date_YMD		= 'M/D/Y';	// format for long dates. D & M & Y must appear.
/* Japanese */ // $pref_date_YMD	= 'Y/M/D';	// format for long dates. D & M & Y must appear.
/* French   */ // $pref_date_YMD	= 'D/N/Y';	// format for long dates. D & M & Y must appear.
$pref_date_sep						= ' - ';	// separator between date and description



/***********************************************************
 *
 *	Setting: 		$pref_copyright_name
 *	Type:			String
 *	Default:		Empty string ''
 *	
 *	The copyright name that appears under albums or images.
 *	
 *	Format is HTML. Use HTML-compliant characters (like &eacute; or &#129;)
 *	Important: if you want to insert Japanese here, add a line in data_jpu8.bin
 *	or use UTF-8 bytes directly in hexa.
 *
 *	This should ideally be overriden by album-specific prefs.php files
 *
 ***********************************************************/

$pref_copyright_name = '';



/***********************************************************
 *
 *	Setting: 		$pref_html_meta
 *	Type:			String
 *	Default:		"<meta name=\"robots\" content=\"noindex, nofollow\">"
 *	
 *	The <meta> tag that appears on top of every html page.
 *	
 *	Each album's pref can override this. The default is here.
 *
 ***********************************************************/

$pref_html_meta = "<meta name=\"robots\" content=\"noindex, nofollow\">";



// --- Global display preferences ---


/***********************************************************
 *
 *	Setting: 		$pref_disable_album_borders
 *	Type:			Boolean (0 or 1)
 *	Default:		0
 *	
 * When set to 1, disables album thumbnails borders.
 * Necessary for Mozilla 1.0 lovers, since it doesn't render the actual table correctly.
 * Will be fixed later (Note that Mozilla 1.2 and above works just fine!)
 *
 * Default: line commented or value 0. Uncomment and set to 1 to disable image border.
 *
 ***********************************************************/


$pref_disable_album_borders = 0;


/***********************************************************
 *
 *	Setting: 		$pref_disable_web_translate_interface
 *	Type:			Boolean (0 or 1)
 *	Default:		1
 *	
 * When set to 1, disables web-interface for translating language.
 * This is the site-wide setting.
 * If you want to enable it, please do so in the album prefs.php!
 *
 * There are security issues with the translation interface.
 * The feature currently does not check for cross-scripting exploits in the
 * language strings. This will be fixed later, in the meantime it is strongly
 * recommanded that you keep the feature disabled unless you strongly trust
 * your admin editors. You have been warned.
 *
 * Default: line with value 1. Set to 0 to enable language translation
 * by admins directly in the web interface.
 *
 ***********************************************************/

$pref_disable_web_translate_interface = 1;


/***********************************************************
 *
 *	Setting: 		$pref_image_layout
 *	Type:			String ('1' or '2')
 *	Default:		'1'
 *	
 * Selects the default image layout.
 *
 * There are currently two image layouts:
 *
 * 1: The image is on the top of the page, with the prev/next previews on each side.
 *    The image size popup and all other options are *below* the image.
 *    This is an ideal layout if you hate to scroll down to see an image on a small screen
 *    (1024x768 or less) and rarely use the image size popup anyway.
 *
 * 2: The image size popup and the prev/next previews are on *top* of the image.
 *    There is nothing to left/right of the image. This is ideal if you like to see big
 *    sizes for images.
 *
 * I like 1 now. Most others like 2 with was RIG's original layout.
 *
 * WARNING: this is a TEMPORARY hack whilst waiting for more powerful template-based layout pages
 * This is the reason why you cannot choose the layout in live. I'll add that later.
 *
 ***********************************************************/

$pref_image_layout = '1';


/***********************************************************
 *
 *	Setting: 		$pref_album_layout
 *	Type:			String (either 'grid' or 'vert' only)
 *	Default:		'grid'
 *	
 * Selects the default layout for albums with NO descriptions.
 *
 * There are currently two album layouts:
 *
 * 'grid': This is default layout.
 *         Album lists are presented in a N-per-row grid.
 *         Image lists are presented in a N-per-row grid.
 *         There are as many row as necessary to display all the visible items.
 *         N is defined by $pref_nb_col, described below (default: 5)
 *
 * 'vert': An alternate vertical layout for albums.
 *         Album lists are presented one per line, vertically.
 *         Image lists are presented in a N-per-row grid like in 'grid' layout.
 *         There are as many row as necessary to display all the visible items.
 *         N is defined by $pref_nb_col, described below (default: 5)
 *
 * WARNING: this is a TEMPORARY hack whilst waiting for more powerful template-based layout pages
 * This is the reason why you cannot choose the layout in live. I'll add that later.
 *
 ***********************************************************/

$pref_album_layout = 'grid';



/***********************************************************
 *
 *	Setting: 		$pref_album_with_description_layout
 *	Type:			String (either 'grid' or 'vert' only)
 *	Default:		'vert'
 *	
 * Selects the default layout for albums with descriptions.
 *
 * If you want the same layout for both kind of albums (with or without
 * descriptions), you can either use an empty string or use
 * the same layout that for $pref_album_layout by writing:
 *	$pref_album_with_description_layout = $pref_album_layout;
 *
 * For the various album layouts, please look at $pref_album_layout.
 *
 ***********************************************************/

$pref_album_with_description_layout = 'vert';



/***********************************************************
 *
 *	Setting: 		$pref_nb_col
 *	Type:			Integer >= 1
 *	Default:		5
 *	
 * Selects the number of album or images per line/row in grid layout.
 *
 ***********************************************************/

$pref_nb_col		= 5;



// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.14  2003/08/14 04:42:08  ralfoide
//	Album & Image ignore lists
//
//	Revision 1.13  2003/07/21 04:59:29  ralfoide
//	Alternate album layout for description.
//	Auto-swithc album layout on description presence.
//	
//	Revision 1.12  2003/07/19 07:52:36  ralfoide
//	Vertical layout for albums
//	
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
