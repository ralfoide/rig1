<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

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


// --- Detect different kind of devices depending on the current referer ---
// RM 20040711 Support for palm sized screens (Experimental)
// RM 20040711 TBDL move somewhere else, this is not to be in prefs.php

if (preg_match("/; ([1-9][0-9]+)x([0-9]{2,})\)$/", rig_get($_SERVER, 'HTTP_USER_AGENT', ''), $match) == 1)
{
	$rig_referer_sx = (int)$match[1];
	$rig_referer_sy = (int)$match[2];
	$rig_is_small_screen = is_int($rig_referer_sx) && $rig_referer_sx > 0 && $rig_referer_sx < 640
						&& is_int($rig_referer_sy) && $rig_referer_sy > 0 && $rig_referer_sy < 480;
}
else
{
	$rig_referer_sx = 0;
	$rig_referer_sy = 0;
	$rig_is_small_screen = FALSE;
}


// --- system-dependent prefs ---

/***********************************************************
 *
 *	Section:	system-dependent prefs
 *
 *	For new installations: most likely, you will not need to
 *	change anything in this section.
 *
 ***********************************************************/


if (PHP_OS != 'WINNT')
{
	/***********************************************************
	 *
	 *	Section:	Un*x specific settings.
	 *
	 *	For new installations: most likely, you will not need to
	 *	change anything in this section.
	 *
	 ***********************************************************/


	/***********************************************************
	 *
	 *	Setting: 		$pref_preview_exec
	 *	Type:			File-system relative path with / separators
	 *	Relative to:	$dir_abs_install
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
	 *	Setting: 		$pref_use_jhead
	 *	Type:			String
	 *	Default:		exec('which jhead')
	 *	
	 *	The path were jhead is located.
	 *	Use either exec('which jhead') or a path like '/usr/bin/jhead'
	 *	To disable jhead, use an empty string ''.
	 *
	 *	JHead is a nice tool to extract EXIF information from digital camera images.
	 *	Home page: http://www.sentex.net/~mwandel/jhead/
	 *
	 ***********************************************************/

	$pref_use_jhead			= exec('which jhead');

}
else // WINNT
{

	/***********************************************************
	 *
	 *	Section:	Windows (Win32) specific settings.
	 *
	 *	Note:		Un*x settings are right below, please scroll.
	 *
	 *	For new installations: most likely, you will not need to
	 *	change anything in this section.
	 *
	 ***********************************************************/


	/***********************************************************
	 *
	 *	Setting: 		$pref_preview_exec
	 *	Type:			File-system relative path with \ separators
	 *	Relative to:	$dir_abs_install
	 *	Default:		thumbnail\Release\rig_thumbnail.exe
	 *	
	 *	This path indicates where the rig_thumbnail.exe application
	 *	can be find. It has to be relative to the installation
	 *	directory. For a Windows path, use \ as directory separator.
	 *	The path must NOT start by \. 
	 ***********************************************************/

	$pref_preview_exec		= 'rig\thumbnail\release\rig_thumbnail.exe';


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


/***********************************************************
 *
 *	Setting: 		$pref_site_name
 *	Setting: 		$pref_site_link
 *	Type:			String
 *	Default:		""
 *	
 *	Site name to be used in page title.
 *  The site link is an HTTP address that will be linked in the page header
 *  if present.
 *
 *  You would typically override this in an album-specific pref file rather
 *  than in the global site pref file.
 *
 ***********************************************************/

$pref_site_name		= "";
$pref_site_link		= "";



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





// --- thumbnails creation ---


/***********************************************************
 *
 *	Section:	Thumbnail options.
 *
 *
 ***********************************************************/


$pref_preview_size		= 80;
$pref_preview_quality	= 70;
$pref_preview_timeout	= 20;


// RM 20030720
// size and quality of small preview (used for vertical album layout)
$pref_small_preview_size	= $pref_preview_size;
$pref_small_preview_quality	= $pref_preview_quality;


// --- admin viewing options ---

// RM 20030821: not currently used -- for future admin_image.php page

$pref_admin_size		= 256;


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
$pref_missing_video		= 'n_a_video.gif';

$pref_global_gamma		= 1.0;	// use 1.0 for no-op



// --- files and albums ignore list [RM 20030813 v0.6.3.5] ---

/***********************************************************
 *
 *	Setting: 		$pref_album_ignore_list
 *	Type:			Array of regexp strings
 *	Default:		NULL
 *	
 *	This array lists reg-exp patterns of names to avoid for albums.
 *	The ignore list is taken into account when reading the filesystem's
 *	content. That means that filenames ignored here are never seen *ever*
 *	(not even in admin mode!) and are never accessed by RIG.
 *	As far as RIG is concerned, they do not exist.
 *
 *	Arrays can be affected NULL or FALSE or the empty string or the empty
 *	array if you want to disable the feature.
 *
 *	For matching pattern syntax, cf http://www.php.net/manual/en/function.preg-match.php
 *	or http://www.perldoc.com/perl5.8.0/pod/perlre.html
 *
 *	Typical pattenrs:
 *	- "/^foo$/"   matches a filename being _exactly_ "foo"
 *	- "/^foo/"    matches any filename starting by "foo".
 *	- "/\bfoo\b/" matches any filename with the word "foo" (word-boundary check)
 *	- "/foo/i"    matches any filename containing "foo" or "FOO" or "FoO" anywhere
 *				  in the name (i means case-insenstive)
 *
 *	Example:
 *	
 *	$pref_album_ignore_list	= array("/^CVS$/",
 *									"/AlbumToBeIgnored/");
 *
 ***********************************************************/

$pref_album_ignore_list	= NULL;


/***********************************************************
 *
 *	Setting: 		$pref_image_ignore_list
 *	Type:			Array of regexp strings
 *	Default:		NULL
 *	
 *	This array lists reg-exp patterns of names to avoid for images.
 *	The ignore list is taken into account when reading the filesystem's
 *	content. That means that filenames ignored here are never seen *ever*
 *	(not even in admin mode!) and are never accessed by RIG.
 *	As far as RIG is concerned, they do not exist.
 *
 *	Arrays can be affected NULL or FALSE or the empty string or the empty
 *	array if you want to disable the feature.
 *
 *	For matching pattern syntax, cf http://www.php.net/manual/en/function.preg-match.php
 *	or http://www.perldoc.com/perl5.8.0/pod/perlre.html
 *
 *	Typical pattenrs:
 *	- "/^foo$/"   matches a filename being _exactly_ "foo"
 *	- "/^foo/"    matches any filename starting by "foo".
 *	- "/\bfoo\b/" matches any filename with the word "foo" (word-boundary check)
 *	- "/foo/i"    matches any filename containing "foo" or "FOO" or "FoO" anywhere
 *				  in the name (i means case-insenstive)
 *
 *	Example:
 *	
 *	$pref_album_ignore_list	= array("/fuzzy/",
 *									"/ImageToBeIgnored/",
 *									"/^temp_/i");
 *
 ***********************************************************/


$pref_image_ignore_list	= NULL;





// --- login options ---


/***********************************************************
 *
 *	Section:	Login options.
 *
 *
 ***********************************************************/


$pref_allow_guest		= TRUE;				// can be TRUE (default) or FALSE
$pref_auto_guest		= TRUE;				// FALSE will force login, TRUE will auto-log as guest
$pref_guest_username	= 'guest';			// must be in the user_list.txt file


// --- default language & theme ---

$pref_default_lang		= 'en';				// choices are en, fr, sp, jp
$pref_default_theme		= 'blue';			// choices are blue, gray, khaki, egg, sand


// --- dates at beginning of album names ---

$pref_date_YM						= 'M/Y';	// format for short dates. M & Y must appear.
/* American */ $pref_date_YMD		= 'M/D/Y';	// format for long dates. D & M & Y must appear.
/* Japanese */ // $pref_date_YMD	= 'Y/M/D';	// format for long dates. D & M & Y must appear.
/* French   */ // $pref_date_YMD	= 'D/M/Y';	// format for long dates. D & M & Y must appear.
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
 *	Use an empty string '' to disable.
 *
 ***********************************************************/

$pref_html_meta = "<meta name=\"robots\" content=\"noindex, nofollow\">";



/***********************************************************
 *
 *	Setting: 		$pref_extra_html_footer
 *	Type:			String
 *	Default:		Empty string ''
 *	
 *	Extra HTML that can appear right below the "display RIG credits"
 *  and before the "generated in x seconds."
 *
 *	Format is HTML. Use HTML-compliant characters (like &eacute; or &#129;)
 *	Important: if you want to insert Japanese here, add a line in data_jpu8.bin
 *	or use UTF-8 bytes directly in hexa.
 *
 *	This should ideally be overriden by album-specific prefs.php files.
 *	If you have a tracking code (such as a Google Analytics urchin code),
 *	that's the perfect place to use it.
 *
 ***********************************************************/

$pref_extra_html_footer = '';



// --- Global display preferences ---



/***********************************************************
 *
 *	Setting: 		$pref_disable_web_translate_interface
 *	Type:			Boolean (0 or 1)
 *	Default:		1
 *	
 *	When set to 1, disables web-interface for translating language.
 *	This is the site-wide setting.
 *	If you want to enable it, please do so in the album prefs.php!
 *
 *	There are security issues with the translation interface.
 *	The feature currently does not check for cross-scripting exploits in the
 *	language strings. This will be fixed later, in the meantime it is strongly
 *	recommanded that you keep the feature disabled unless you strongly trust
 *	your admin editors. You have been warned.
 *
 *	Default: line with value 1. Set to 0 to enable language translation
 *	by admins directly in the web interface.
 *
 ***********************************************************/

$pref_disable_web_translate_interface = 1;


/***********************************************************
 *
 *	Setting: 		$pref_image_layout
 *	Type:			String ('1' or '2')
 *	Default:		'1'
 *	
 *	Selects the default image layout.
 *
 *	There are currently two image layouts:
 *
 *	1: The image is on the top of the page, with the prev/next previews on each side.
 *	   The image size popup and all other options are *below* the image.
 *	   This is an ideal layout if you hate to scroll down to see an image on a small screen
 *	   (1024x768 or less) and rarely use the image size popup anyway.
 *
 *	2: The image size popup and the prev/next previews are on *top* of the image.
 *	   There is nothing to left/right of the image. This is ideal if you like to see big
 *	   sizes for images.
 *
 *	I like 1 now. Most others like 2 with was RIG's original layout.
 *
 *	WARNING: this is a TEMPORARY hack whilst waiting for more powerful template-based layout pages
 *	This is the reason why you cannot choose the layout in live. I'll add that later.
 *
 ***********************************************************/

$pref_image_layout = '1';


/***********************************************************
 *
 *	Setting: 		$pref_album_layout
 *	Type:			String (either 'grid' or 'vert' only)
 *	Default:		'grid'
 *	
 *	Selects the default layout for albums with NO descriptions.
 *
 *	There are currently two album layouts:
 *
 *	'grid': This is default layout.
 *	        Album lists are presented in a N-per-row grid.
 *	        Image lists are presented in a N-per-row grid.
 *	        There are as many row as necessary to display all the visible items.
 *	        N is defined by $pref_album_nb_col, described below (default: 5)
 *
 *	'vert': An alternate vertical layout for albums.
 *	        Album lists are presented one per line, vertically.
 *	        Image lists are presented in a N-per-row grid like in 'grid' layout.
 *	        There are as many row as necessary to display all the visible items.
 *	        N is defined by $pref_album_nb_col, described below (default: 5)
 *
 *	WARNING: this is a TEMPORARY hack whilst waiting for more powerful template-based layout pages
 *	This is the reason why you cannot choose the layout in live. I'll add that later.
 *
 ***********************************************************/

$pref_album_layout = 'grid';



/***********************************************************
 *
 *	Setting: 		$pref_album_with_description_layout
 *	Type:			String (either 'grid' or 'vert' only)
 *	Default:		'vert'
 *	
 *	Selects the default layout for albums with descriptions.
 *
 *	If you want the same layout for both kind of albums (with or without
 *	descriptions), you can either use an empty string or use
 *	the same layout that for $pref_album_layout by writing:
 *		$pref_album_with_description_layout = $pref_album_layout;
 *
 *	For the various album layouts, please look at $pref_album_layout.
 *
 ***********************************************************/

$pref_album_with_description_layout = 'vert';



/***********************************************************
 *
 *	Setting: 		$pref_album_nb_col
 *	Type:			Integer >= 1
 *	Default:		5
 *	
 *	Selects the number of album per row in grid layout.
 *
 ***********************************************************/

$pref_album_nb_col		= 5;

// RM 20040711 Support for palm sized screens (Experimental)
if ($rig_is_small_screen)
	$pref_album_nb_col = min(1, $rig_referer_sx / $pref_preview_size);


/***********************************************************
 *
 *	Setting: 		$pref_image_nb_col
 *	Type:			Integer >= 1
 *	Default:		5
 *	
 *	Selects the number of images per row in grid layout.
 *
 ***********************************************************/

$pref_image_nb_col		= 5;

// RM 20040711 Support for palm sized screens (Experimental)
if ($rig_is_small_screen)
	$pref_image_nb_col = min(1, $rig_referer_sx / $pref_preview_size);


/***********************************************************
 *
 *	Setting: 		$pref_enable_album_pagination
 *	Type:			Boolean (TRUE of FALSE)
 *	Default:		TRUE
 *	
 *	Enable use of the pagination in the album display.
 *	When enabled, an album page displays a maximum
 *	of $pref_album_nb_row lines of thumbnails, both in
 *	grid and vertical layouts.
 *
 ***********************************************************/

$pref_enable_album_pagination = TRUE;



/***********************************************************
 *
 *	Setting: 		$pref_album_nb_row
 *	Type:			Integer >= 1
 *	Default:		5
 *	
 *	Selects the maximum number of rows in the list of sub-album
 *	in grid layout.
 *
 ***********************************************************/

$pref_album_nb_row = 5;



/***********************************************************
 *
 *	Setting: 		$pref_image_nb_row
 *	Type:			Integer >= 1
 *	Default:		5
 *	
 *	Selects the maximum number of rows in the list of images
 *	in grid layout.
 *
 ***********************************************************/

$pref_image_nb_row = $pref_album_nb_row;



/***********************************************************
 *
 *	Setting: 		$pref_enable_album_border
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		TRUE
 *	
 *	When set to true, displays a border around the album thumbnails
 *	using the rig_images/album_(bottom|right)(left|line|right).gif files
 *
 ***********************************************************/

$pref_enable_album_border	= TRUE;



/***********************************************************
 *
 *	Setting: 		$pref_enable_image_border
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		TRUE
 *	
 *	When set to true, displays a border around the image thumbnails
 *	using the rig_images/image_(bottom|right)(left|line|right).gif files
 *
 ***********************************************************/

$pref_enable_image_border	= TRUE;



// --- Global features preferences ---



/***********************************************************
 *
 *	Setting: 		$pref_enable_descriptions
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		TRUE
 *	
 *	When set to true, 'descript.ion' and 'file_info.diz' files are read
 *	in every album folders to extract description strings for albums.
 *
 *	Note that albums that have description will automatically show
 *	up using $pref_album_with_description_layout.
 *
 ***********************************************************/

$pref_enable_descriptions = TRUE;

// RM 20040711 Support for palm sized screens (Experimental)
if ($rig_is_small_screen)
	$pref_enable_descriptions = FALSE;


/***********************************************************
 *
 *	Setting: 		$pref_enable_album_html_cache
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		TRUE
 *	
 *	When set to true, the HTML generated for each album page is
 *	cached in the $dir_album_cache folder. The HTML is automatically
 *	expired whenever one of these folders is modified:
 *	- The album folder
 *	- The album's option folder
 *	- The $dir_abs_src or $dir_abs_admin_src folders
 *	- The $dir_abs_globset or $dir_abs_locset folders
 *	- The current template folder
 *
 ***********************************************************/

$pref_enable_album_html_cache = TRUE;


/***********************************************************
 *
 *	Setting: 		$pref_enable_access_hidden_albums
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		FALSE
 *	
 *	When set to true, it possible to view hidden albums by
 *	giving their exact name in the web browser's query string.
 *
 *	Note that albums matched by $pref_album_ignore_list can
 *	NEVER be accessed, even if this preference is TRUE.
 *
 ***********************************************************/

$pref_enable_access_hidden_albums = FALSE;


/***********************************************************
 *
 *	Setting: 		$pref_enable_access_hidden_images
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		FALSE
 *	
 *	When set to true, it possible to view hidden images by
 *	giving their exact name in the web browser's query string.
 *
 *	Note that images matched by $pref_image_ignore_list can
 *	NEVER be accessed, even if this preference is TRUE.
 *	Also, it is not possible to view an image which is in an
 *	album that cannot be accessed (either because it is in the
 *	album ignore list or because it's hidden with access hidden
 *	albums disabled above).
 *
 ***********************************************************/

$pref_enable_access_hidden_images = FALSE;


/***********************************************************
 *
 *	Setting: 		$pref_follow_album_symlinks
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		FALSE
 *	
 *	When set to true, if an album path is actually a symbolic link
 *	onto a folder representing another album path in the same
 *	RIG album, the link will be followed internally. Images, cache
 *	and options from the original album will be used.
 *
 *	This option has a number of restrictions:
 *	- Your filesystem must support symbolic links.
 *	- The links must point into the same album.
 *	- Options and cache files from link origin are used.
 *	- Naviguation URLs will most likely reflect the path traversal
 *	  (this is actually implementation-dependant and I may change
 *	   this behavior as it best suits me. Ideally it should not,
 *	   but it might be easier to implement a true traversal first)
 *
 *	Note that albums matched by $pref_album_ignore_list can
 *	NEVER be accessed, even if this preference is TRUE (this is true
 *	for both source and destination of the symlink).
 *
 ***********************************************************/

$pref_follow_album_symlinks = TRUE;


/***********************************************************
 *
 *	Setting: 		$pref_follow_image_symlinks
 *	Type:			Boolean (TRUE or FALSE)
 *	Default:		FALSE
 *	
 *	When set to true, if an image path is actually a symbolic link
 *	onto another image path in the same RIG album, the link will
 *	be followed internally. The image cache from the original image
 *	will be used *but* the album option of the original album will
 *	be used.
 *
 *	This option has a number of restrictions:
 *	- Your filesystem must support symbolic links.
 *	- The links must point into the same album.
 *	- Naviguation URLs will most likely reflect the path traversal
 *	  (this is actually implementation-dependant and I may change
 *	   this behavior as it best suits me. Ideally it should not,
 *	   but it might be easier to implement a true traversal first)
 *
 *	Note that images matched by $pref_image_ignore_list can
 *	NEVER be accessed, even if this preference is TRUE (this is true
 *	for both source and destination of the symlink).
 *
 ***********************************************************/

$pref_follow_image_symlinks = TRUE;



// --- supported file types [RM 20030627 v0.6.3.4] ---

/***********************************************************
 *
 *	Setting: 		$pref_internal_file_types
 *	Type:			Array of (key/value) strings
 *	Default:		NULL
 *	
 *	This array describes which filename maps to which filetype
 *	but *ONLY* for file types handled by the rig_thumbnail.exe
 *	application.
 *
 *	When the variable is set to NULL (the default), the help application
 *	rig_thumbnail.exe is queried to get the set of actually supported
 *	filetypes and the associated filename patterns.
 *	Use "rig_thumbnail.exe -f" on a command-line to see the list of
 *	file types supported by the application.
 *
 *	Unless you are really familliar with the internals of RIG, you do not
 *	want to change this array.
 *
 *	If you're stubborn or in case you really need to override the file type
 *	array and you really know what you are doing, here is a sample of what it
 *	should currently contain:
 *	 
 *	$pref_internal_file_types= array("/\.jpe?g$/i"					  => 'image/jpeg',
 *									 "/\.(avi|wmv|as[fx])$/i"		  => 'video/avi',
 *									 "/\.(mov|qt|sdp|rtsp)$/i"		  => 'video/quicktime',
 *									 "/\.(mpe?g[124]?|m[12]v|mp4)$/i" => 'video/mpeg');
 *
 *	For matching pattern syntax, cf http://www.php.net/manual/en/function.preg-match.php
 *	or http://www.perldoc.com/perl5.8.0/pod/perlre.html
 *
 ***********************************************************/

$pref_internal_file_types	= NULL;



/***********************************************************
 *
 *	Setting: 		$pref_extra_file_types
 *	Type:			Array of (key/value) strings
 *	Default:		NULL
 *	
 *	This array describes *EXTRA* filename to filetype mapping information.
 *	This is not currently used (as of rig 0.6.4.3).
 *	It will be used later to add extra media type processors, for example
 *	to support Synthetic Plan files, Izumi files or Zip archives.
 *
 *	Unless you are really familliar with the internals of RIG, you do not
 *	want to change this array.
 *
 ***********************************************************/


$pref_extra_file_types	= NULL;



/***********************************************************
 *
 *	Setting: 		$pref_auto_hide_images
 *	Type:			Regexp string
 *	Default:		NULL
 *	
 *	Regular expression to match image filenames to hide by default.
 *  Example: '/^[0-9]+\..+/';
 *
 ***********************************************************/


$pref_auto_hide_images = NULL;




// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.33  2006/12/07 01:08:34  ralfoide
//	v1.0.2:
//	- Feature: Ability to automatically hide images based on name regexp
//	- Exp: Experimental support for mplayer to create movie thumbnails. Doesn't work. Commented out.
//
//	Revision 1.32  2006/06/24 21:20:34  ralfoide
//	Version 1.0:
//	- Source: Set filename in thumbnail streaming headers
//	- Source: Added pref_site_name and pref_site_link.
//	- Fix: Fixed security vulnerability in check_entry.php
//	
//	Revision 1.31  2006/04/13 05:04:22  ralfoide
//	Version 0.7.4. Polish translation. Fixes.
//	
//	Revision 1.30  2006/01/11 08:25:14  ralfoide
//	Default vertical layout thumbnails size to same than grid's ones.
//	
//	Revision 1.29  2005/11/27 18:22:29  ralfoide
//	Revert default auto login pref
//	
//	Revision 1.28  2005/10/07 05:40:09  ralfoide
//	Extracted album/image handling from common into common_media.php.
//	Removed all references to obsolete db/id.
//	Added preliminary default image template.
//	
//	Revision 1.27  2005/10/05 03:56:28  ralfoide
//	New missing video thumbnails.
//	
//	Revision 1.26  2005/09/25 22:36:14  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.25  2004/12/25 09:46:46  ralfoide
//	Fixes and cleanup
//	
//	Revision 1.24  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.23  2004/07/14 06:08:16  ralfoide
//	Experimental small pda screen support
//	
//	Revision 1.22  2004/07/09 05:48:47  ralfoide
//	Bumped default script timeout to 20s
//	
//	Revision 1.21  2004/02/18 07:37:01  ralfoide
//	Allow viewing hidden images by direct access
//	
//	Revision 1.20  2003/11/09 20:50:58  ralfoide
//	Added pref_internal_file_types
//	
//	Revision 1.19  2003/09/13 21:55:54  ralfoide
//	New prefs album nb col vs image nb col, album nb row vs image nb row.
//	New pagination system (several pages for image/album grids if too many items)
//	
//	Revision 1.18  2003/09/01 20:54:24  ralfoide
//	More variable descriptions.
//	Added pref_follow_album/image_symlinks
//	
//	Revision 1.17  2003/08/21 20:20:52  ralfoide
//	New enable prefs (album/image hidden, descriptions, album cache)
//	
//	Revision 1.16  2003/08/18 02:14:35  ralfoide
//	Updated, new filetype support
//	
//	Revision 1.15  2003/08/15 07:15:03  ralfoide
//	Album/image border usage flags
//	
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
