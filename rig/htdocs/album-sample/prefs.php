<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************
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


// ---- Copyright Name for albums & images ----

// Format is HTML. Use HTML-compliant characters (like &eacute; or &#129;)
// Important: if you want to insert Japanese here, add a line in data_jpu8.bin
// or use UTF-8 bytes directly in hexa.

$pref_album_copyright_name = 'Your Name Here';



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
