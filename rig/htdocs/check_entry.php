<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//**********************************************
// $Id$
//**********************************************


// ------------------------------------------------------------
// DEBUG -- Test installation variable and cry if not happy
// Note this is not technically necessary but is of great help for new installations

//********************************
function rig_check_src_file($name)
//********************************
{
	global $dir_abs_install, $dir_abs_src, $dir_abs_admin_src, $dir_abs_globset, $dir_abs_locset;
	
	// enabling track_errors is a big help
	ini_set("track_errors", "1");
	
	// check it worked
	$track_errors = (ini_get("track_errors") == 1);
	
	if ($track_errors)
	    $result = @file_exists($name);
	else
	    $result = file_exists($name);
	if (!$result)
	{
	    echo "<h1>RIG Configuration Error</h1>";
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
	}
	
	return $name;
}

// ------------------------------------------------------------
// call the entry point

require_once(rig_check_src_file($dir_abs_src . "entry_point.php"));

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2003/08/21 20:10:11  ralfoide
//	New check_entry.php
//
//-------------------------------------------------------------
?>
