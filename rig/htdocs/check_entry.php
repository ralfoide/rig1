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


// ------------------------------------------------------------
// DEBUG -- Test installation variable and cry if not happy
// Note this is not technically necessary but is of great help for new installations

//********************************
function rig_check_src_file($name)
//********************************
{
	global $dir_abs_install;
	global $dir_abs_src;
	global $dir_abs_admin_src;
	global $dir_abs_globset;
	global $dir_abs_locset;

	// disable auto-globals from CGI params -- RM 20060624 - v1.0
	ini_set("register_globals", "0");

	// complain if that didn't work
	if (ini_get("register_globals") == 1)
	{
	    echo "<h1>RIG Security Error</h1>";
	    echo "<h2>Important!</h2>To prevent a security vulnerability, you <b>must disable</b> <em>register_globals</em> in your PHP.INI file! <br/>";
	    echo "Read <a href='http://us2.php.net/manual/en/configuration.php#ini.register-globals'>http://us2.php.net/manual/en/configuration.php#ini.register-globals</a> for more information. <p/>";
	    exit;
	}

	// enabling track_errors is a big help
	ini_set("track_errors", "1");
	
	// check it worked
	$track_errors = (ini_get("track_errors") == 1);

	$name = str_replace("..", ".", str_replace("://", "", $name));
	if ($track_errors)
	    $result = @file_exists($name);
	else
	    $result = file_exists($name);

	if (!$result)
	{
	    echo "<h1>RIG Configuration Error</h1>";

		// Uncomment the following line for debugging new installations -- RM 20060624 - v1.0
	    exit;

	    echo "<h2>Error</h2>A source file could not be located! Please check <em>location.php</em> file!";
	    if ($track_errors)
	        echo "<h2>Reason</h2>$php_errormsg";
	    else
	        echo "<h2>Important!</h2>Please consider enabling <em>track_errors</em> in your PHP.INI file!";
	    echo "<h2>Details</h2>";
	    echo "<pre>";
	    echo "<b>file path</b>         = '$name'<br>";
	    echo "<br>";
	    echo "<b>dir_instal</b>        = '$dir_abs_install'<br>";
	    echo "<b>dir_abs_src</b>       = '$dir_abs_src'<br>";
	    echo "<b>dir_abs_admin_src</b> = '$dir_abs_admin_src'<br>";
	    echo "<br>";
	    echo "<b>dir_abs_globset</b>   = '$dir_abs_globset'<br>";
	    echo "<b>dir_abs_locset</b>    = '$dir_abs_locset'<br>";
	    echo "</pre>";
	    echo "<hr>";
	    
	    // Do not pursue with an invalid file -- RM 20060624 - v1.0
	    exit;
	}

	return $name;
}

// ------------------------------------------------------------
// call the entry point

require_once(rig_check_src_file($dir_abs_src . "entry_point.php"));

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.5  2006/06/24 21:20:34  ralfoide
//	Version 1.0:
//	- Source: Set filename in thumbnail streaming headers
//	- Source: Added pref_site_name and pref_site_link.
//	- Fix: Fixed security vulnerability in check_entry.php
//
//	Revision 1.4  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//	
//	Revision 1.3  2005/09/25 22:36:12  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.2  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.1  2003/08/21 20:10:11  ralfoide
//	New check_entry.php
//	
//-------------------------------------------------------------
?>
