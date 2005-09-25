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
//
//
// LOCAL PREFS for _this_ specific album.
// Defaults are in rig/settings/prefs.php and can
// be overrided here.

// ---- Access/Misc Prefs ----

// login options
$pref_allow_guest		= TRUE;				// can be TRUE (default) or FALSE
$pref_auto_guest		= TRUE;				// should guest authentificate? TRUE (default) or FALSE

// global gamma override
$pref_global_gamma		= 1.0;	            // use 1.0 for no-op

// --- default language & theme ---

// These are the defaults from the global prefs.
// You can overrride them for each local album if you need to.
// Simply uncomment the desired line and change the value.
//
// $pref_default_lang		= 'en';				// choices are en, fr, sp, jp
// $pref_default_theme		= 'blue';			// choices are blue, gray, khaki, egg, sand


// --- dates at beginning of album names ---

// These are the defaults from the global prefs.
// You can overrride them for each local album if you need to.
// Simply uncomment the desired line and change the value.
//
// $pref_date_YM					= 'M/Y';	// format for short dates. M & Y must appear.
/* American */ // $pref_date_YMD	= 'M/D/Y';	// format for long dates. D & M & Y must appear.
/* Japanese */ // $pref_date_YMD	= 'Y/M/D';	// format for long dates. D & M & Y must appear.
/* French   */ // $pref_date_YMD	= 'D/N/Y';	// format for long dates. D & M & Y must appear.
// $pref_date_sep					= ' - ';	// separator between date and description


// ---- Copyright Name for albums & images ----

// Format is HTML. Use HTML-compliant characters (like &eacute; or &#129;)
// Important: if you want to insert Japanese here, add a line in data_jpu8.bin
// or use UTF-8 bytes directly in hexa.

$pref_copyright_name = 'Your Name Here';



// --- meta tags for album/image pages ---

// Uncomment the next line if you want robots index and follow autorized for this album
// $pref_html_meta = "";


// ---- URL-Rewrite support ---

// If non empty, URLs will be rewritten using this rule.
// %A is the URL-encoded album name, %I is the URL-encoded image name.
// There are 3 kind of urls: main index, album URL and image URL.

// Example:
// If you define something like this in your Apache's httpd.conf file
//
// LoadModule rewrite_module /usr/lib/apache/1.3/mod_rewrite.so
// <VirtualHost 192.168.0.0>
//     ServerName www.example.com
//     DocumentRoot /home/user/rig/
//     RewriteEngine On
//     RewriteRule ^/i=([^/]+)/(.*)$   http://www.example.com/index.php?image=$1&album=$2
//     RewriteRule ^/a=(.*)$           http://www.example.com/index.php?album=$1
//     RewriteRule ^/$                 http://www.example.com/index.php
// </VirtualHost>
//
// Then you can use an URL-rewrite like this:
// 
// $pref_url_rewrite = array('index' => "http://www.example.com/",
// 							 'album' => "http://www.example.com/a=%A",
// 							 'image' => "http://www.example.com/i=%I/%A");


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.3  2005/09/25 22:36:12  ralfoide
//	Updated GPL header date.
//
//	Revision 1.2  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.1  2003/08/18 02:10:13  ralfoide
//	Reorganazing
//	
//	Revision 1.8  2003/03/17 08:24:42  ralfoide
//	Fix: added pref_disable_web_translate_interface (disabled by default)
//	Fix: added pref_disable_album_borders (enabled by default)
//	Fix: missing pref_copyright_name in settings/prefs.php
//	Fix: outdated pref_album_copyright_name still present. Eradicated now :-)
//	
//	Revision 1.7  2003/03/12 07:11:45  ralfoide
//	New upload dirs, new entry_point, new meta override
//	
//	Revision 1.6  2003/02/16 20:09:40  ralfoide
//	Update. Version 0.6.3.1
//	
//	Revision 1.5  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.4  2003/01/07 17:52:50  ralfoide
//	URL-Rewrite conf array moved in the album-specific pref file
//	
//	Revision 1.3  2002/10/22 22:32:03  ralfoide
//	Global gamma can be changed in the site-specific pref file
//	
//	Revision 1.2  2002/10/21 01:56:53  ralfoide
//	Added local override of gamma
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
