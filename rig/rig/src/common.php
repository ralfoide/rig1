<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

/*
	List of URL-variables:
	----------------------
	album					- string
	image					- string
	credits					- boolean string 'on' or nothing
	login					- boolean string 'force' or nothing
	keep					- boolean string 'on' or nothing
	user					- string username
	passwd					- string passwd (clear)
	admusr					- string username
	admpwd					- string passwd (clear)

	List of cookies:
	----------------
	rig_lang				- string 'fr' or 'en'
	rig_img_size			- integer (0=original size)
	rig_user				- string
	rig_passwd				- string (crypt)
	rig_adm_user			- string
	rig_adm_passwd			- string (crypt)

	List of globals:
	----------------
	rig_version				- string
	current_language		- string 'en' (default) or 'fr'
	current_album			- string
	current_image			- string
	list_albums				- array of string
	list_images				- array of filename
	display_title			- string
	display_album_title		- string
	display_language		- string
	display_date			- string
	display_softname		- string, constant
	display_prev_link		- string
	display_prev_img		- string
	display_next_link		- string
	display_next_img		- string

	List of global access paths:
	----------------------------
	dir_abs_album
	dir_album
	dir_preview
	dir_option
	abs_album_path
	abs_preview_path
	abs_option_path
	abs_preview_exec

	List of globals (from album options):
	-------------------------------------
	list_hide				- array of filename
	list_album_icon			- array of icon info { album , file }

*/
//-----------------------------------------------------------------------

if (PHP_OS == 'WINNT')
{
	define("SEP", "\\");
	define("SEP2", "");			// Windows: accept either / or \ in paths
}
else // Un*x
{
	define("SEP", "/");
	define("SEP2", "\\");		// Unix: converts \ to / in paths
}


define("CURRENT_ALBUM_ARROW",	"&nbsp;=&gt;&nbsp;");
define("EXTLIST",				".jpg.jpeg");
define("SOFT_NAME",				"Rig [Ralf Image Gallery]");
define("ALBUM_ICON",			"album_icon.jpg");
define("ALBUM_OPTIONS",			"options.txt");

// start timing...
$time_start = getmicrotime();

// read site-prefs and then override with local prefs, if any
require_once($dir_install . $dir_globset . "prefs.php");
if (rig_is_file($dir_locset . "prefs.php"))
	require_once($dir_locset . "prefs.php");

// setup...
require_once($dir_install . $dir_src . "login_util.php");
read_prefs_paths();
handle_cookies();

// include language strings
if (!$current_language)
	$current_language = 'en';
require_once($dir_install . $dir_src . "str_$current_language.php");
require_once($dir_install . $dir_src . "version.php");
setup();
create_option_dir();

require_once($dir_install . $dir_src . "common_display.php");
require_once($dir_install . $dir_src . "common_images.php");

//-----------------------------------------------------------------------

//**************************************
function html_error($str, $body = FALSE)
//**************************************
{
	global $color_table_bg;
	global $color_error_bg;

	if ($body) echo "<body>\n";

	echo "<center><table border=1 bgcolor=\"$color_table_bg\">\n";
	echo "<tr><td bgcolor=\"$color_error_bg\"><center><br><h3>An Error Occured</h3></center></td></tr>\n";
	echo "<tr><td><br> $str\n <p></td></tr>\n";
	echo "</table></center>\n";

	if ($body) echo "</body>\n";
}


//-----------------------------------------------------------------------

//*************************
function rig_is_file($name)
//*************************
{
    return file_exists($name) && is_file($name);
}

//*********************
function getmicrotime()
//*********************
// extracted from PHP doc for microtime()
{
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
} 


//*********************
function time_elapsed()
//*********************
{
	global $time_start;
	return sprintf("%2.2f", getmicrotime() - $time_start);
}


//****************************
function print_array_str($str)
//****************************
{
	echo "str[] = '$str'<br>\n";
	for($i=0, $n=strlen($str); $i<$n; $i++)
	{
		$a = ord($str{$i});
		echo "str[$i] = {$a} = '{$str{$i}}'<br>\n";
	}
}


//*********************
function prep_sep($str)
//*********************
{
	if ($str && $str[0] != SEP)
		return SEP . $str;
	else
		return $str;
}


//*********************
function post_sep($str)
//*********************
{
	if ($str && $str[strlen($str)-1] != SEP)
		return $str . SEP;
	else
		return $str;
}


//***********************
function valid_ext($name)
//***********************
{
	// get the extension
	$dot  = strrchr($name, '.');
	$len = strlen($dot);

	// if has extension, check we accept it
	if ($dot && $len <= 4)
		return (bool)stristr(EXTLIST, $dot);

	// reject file
	return FALSE;
}


//****************************
function decode_argument($arg)
//****************************
// Removes shell-magic characters ( . / \ & ../ ) from album or image arguments
// Decode arguments received from the URL line
{
	if ($arg)
	{
		// remove double-seps
		$arg = str_replace(SEP . SEP, SEP, $arg);
		$arg = str_replace("\\'", "'", $arg);

		// convert SEP2 into SEP (dos->unix path)
		if (SEP2 != "")
			$arg = str_replace(SEP2, SEP, $arg);

		// remove any "../" present in the filename
		$arg = str_replace("../", "", $arg);

		// remove these stange characters if present at the beginning of the string
		$n = strspn($arg, "./\\&^%!\$");
		if ($n)
			return substr($arg, $n);
	}

	return $arg;
}


//****************************
function encode_argument($arg)
//****************************
// Encode arguments that are used in the URL line
{
	// remove double-seps
	$arg = str_replace("/" . "/", "/", $arg);
	$arg = str_replace("\\'", "'", $arg);

	// convert SEP2 into SEP (dos->unix path)
	if (SEP2 != "")
		$arg = str_replace(SEP2, SEP, $arg);

	// remove these strange characters if present at the beginning of the string
	$n = strspn($arg, "./\\&^%!\$");
	if ($n)
		$arg = substr($arg, $n);

	// Now protect characters that have a meaning in HTTP URLs.
	// cf Section 3.2 of RFC 2068 HTTP 1.1
	// reserved = ";/?:@&=+";
	// extra    = "!*'(),";
	// unsafe   = " \"#%<>";
	// safe     = "$-_.";

	$match = ";/?:@&=+!*'(), \"#%<>";

	$n = strlen($arg);
	$res = "";
	for($i=0; $i<$n; $i++)
	{
		$c = substr($arg, $i, 1);
		if (strrchr($match, $c))
			$res .= sprintf("%%%02x", ord($c));
		else
			$res .= $c;
	}
	return $res;
}


//****************************
function encode_url_link($arg)
//****************************
// Encode IMG SRC and HREF links
{
	// Now protect characters that have a meaning in HTTP URLs.
	// cf Section 3.2 of RFC 2068 HTTP 1.1
	// reserved = ";/?:@&=+";
	// extra    = "!*'(),";
	// unsafe   = " \"#%<>";
	// safe     = "$-_.";

	$match = ";?:@&=+!*'(), \"#%<>";

	$n = strlen($arg);
	$res = "";
	for($i=0; $i<$n; $i++)
	{
		$c = substr($arg, $i, 1);
		if (strrchr($match, $c))
			$res .= sprintf("%%%02x", ord($c));
		else
			$res .= $c;
	}
	return $res;
}



//***************************
function shell_filename($str)
//***************************
// Encode a filename before using it in a shell argument call
// The thumbnail app will un-backslash the full argument filename before using it
{
	// RM 102201 -- escapeshellarg is "almost" a good candidate for linux
	// but for windows we need escapeshellcmd because a path may contain backslashes too

	return "\"" . escapeshellcmd($str) . "\"";
}



//*******************************
function simplify_filename($name)
//*******************************
{
	$name = trim($name);
	// replace weird characters by underscores
	$name = strtr($name, " \'\"\\/&" , "______");
	return $name;
}


//**************************************************
function pretty_name($name, $strip_numbers = TRUE,
                            $pretty_dirname = FALSE)
//**************************************************
{
	global $html_image;
	global $pref_date_YM;
	global $pref_date_YMD;
	global $pref_date_sep;

	$name = trim($name);

	// remove any extension
	$dot  = strrchr($name, '.');
	$len1 = strlen($name);
	$len2 = strlen($dot);

	if ($dot && $len2 <= 4)
		$name = substr($name, 0, $len1 - $len2);

    // We show leading numbers in a directory if it's looks like a date (20001231)
    // We'll interpret at least 6 leading digits as a year + date
	if ($pretty_dirname)
	{
	    if (ereg("^([0-9]{4})([0-9]{2})([0-9]{2})$", $name, $reg))
		{
            // First deal with full dates
			// --> "YYYYMMDD"
			$name = str_replace("Y", $reg[1], $pref_date_YMD);
			$name = str_replace("M", $reg[2], $name);
			$name = str_replace("D", $reg[3], $name);
        }
		else if (ereg("^([0-9]{4})[-/]{0,1}([0-9]{2})[-/]{0,1}([0-9]{2})[- _]*(.+)$", $name, $reg))
		{
            // A full date followed by a description with optional date separators
			// --> "YYYYMMDD_text" or "YYYY-MM-DD_text"
			$name = str_replace("Y", $reg[1], $pref_date_YMD);
			$name = str_replace("M", $reg[2], $name);
			$name = str_replace("D", $reg[3], $name);
			$name .= $pref_date_sep.$reg[4];
        }
		else if (ereg("^([0-9]{4})([0-9]{2})$", $name, $reg))
		{
            // and then partial dates YYYYMM
			$name = str_replace("Y", $reg[1], $pref_date_YM);
			$name = str_replace("M", $reg[2], $name);
			$name .= $pref_date_delim.$reg[2];
        }
		else if (ereg("^([0-9]{4})[-/]{0,1}([0-9]{2})[- _]*(.+)$", $name, $reg))
		{
            // a partial date followed by a description with optional date separators
			// --> "YYYYMM_text" or "YYYY-MM_text"
			$name = str_replace("Y", $reg[1], $pref_date_YM);
			$name = str_replace("M", $reg[2], $name);
			$name .= $pref_date_sep.$reg[3];
        }
		else if (ereg("^[0-9]{1,5}[- _](.+)$", $name, $reg))
		{
            // Remove leading digits if there are less than 5
			$name = $reg[1];
        }
	}
	else if ($strip_numbers)
	{
		// remove numbers from begining of file name

		// unless the name contains the name "IMG" as such, try to strip any leading numbers
		if (eregi("^([0-9]+)[- _]([0-9]+)[- _]img$", $name, $reg))
		{
			// numbered sequence direct from the camera.
			$name = "$html_image " . $reg[2];
		}
		else if (ereg("^([0-9]+)$", $name, $reg))
		{
			// if the image name is just a number, generate a text for it
			$name = "$html_image " . $reg[1];
		}
		else if (ereg("^[0-9]+[- _]*(.+)$", $name, $reg))
		{
			// trim number at beginning if any
			$name = $reg[1];
		}
	}
	else
	{
		// do not remove numbers at begining of file name
		// (that doesn't mean we can't rearange things !)

		if (eregi("^([0-9]+)[ _]([a-z_0-9].+)$", $name, $reg))
		{
			// if image starts with a number, insert a dash after it for lisibility
			// note that the reg exp above has [ _] in it, not [- _]. If there's a dash, leave it!
			$name = "$reg[1] - $reg[2]";
		}
	}

	// replace underscores by spaces
	$name = str_replace('_', ' ', $name);
	return $name;
}


//*********************************
function create_preview_dir($album)
//*********************************
{
	global $pref_mkdir_mask;
	global $abs_preview_path;
	$album = $abs_preview_path . prep_sep($album);

	if (is_dir($album))
		return;

	if (!mkdir($album, $pref_mkdir_mask))
		html_error("Can't create preview directory '$album'", TRUE);
}


//**************************
function create_option_dir()
//**************************
{
	global $pref_mkdir_mask;
	global $abs_option_path;

	if (is_dir($abs_option_path))
		return;

	if (!mkdir($abs_option_path, $pref_mkdir_mask))
		html_error("Can't create option directory", TRUE);
}


//*************************************************************
function self_url($in_image = -1,
				  $in_album = -1,
				  $in_admin = -1,
				  $in_extra = "")
//*************************************************************
// encode album/image name as url links
// in_image: -1 (use current if any) or text for image=...
// in_album: -1 (use current if any) or text for album=...
// in_admin: -1 (use current if any) or TRUE for "admin=on"
// in_extra: extra parameters (in the form name=val&name=val etc)
{
	global $album;				// from index.php url line
	global $image;				// from index.php url line
	global $admin;				// from index.php url line
	global $current_album;
	global $current_image;
	global $PHP_SELF;
	global $_debug_;

	$url = $PHP_SELF;
	$params = "";

	if ($in_album == -1)
	{
		$in_album = $current_album;

		if (!$in_album)
			$in_album = decode_argument($album);
	}

	if ($in_album)
		$in_album = encode_url_link($in_album);

	if ($in_image == -1)
	{
		$in_image = $current_image;

		if (!$in_image)
			$in_image = decode_argument($image);
	}

	if ($in_image)
		$in_image = encode_url_link($in_image);

	if (is_int($in_admin) && $in_admin == -1)
	{
		$in_admin = $admin;
	}

	if ($in_admin && !strstr($in_extra, "admin="))
	{
		// note that if in_extra contains a specific "admin="
		// parameter, we don't need to do that
		if ($params)
			$params .= "&";
		$params .= "admin=on";
	}

	if ($in_album)
	{
		if ($params)
			$params .= "&";
		$params .= "album=$in_album";
	}


	if ($in_image)
	{
		if ($params)
			$params .= "&";
		$params .= "image=$in_image";
	}

	if ($in_extra)
	{
		if ($params)
			$params .= "&";
		$params .= "$in_extra";
	}

	if ($_debug_)
	{
		if ($params)
			$params .= "&";
		$params .= "_debug_=1";
	}

	if ($params)
		return $url . "?" . $params;
	else
		return $url;
}


//-----------------------------------------------------------------------


//*************************
function read_prefs_paths()
//*************************
{
	global $dir_abs_album;

	// append a separator to the abs album dir if not already done
	$dir_abs_album = post_sep($dir_abs_album);

	// make some paths absolute
	global $dir_album, $abs_album_path;
	$abs_album_path   = realpath($dir_abs_album . $dir_album);

	global $dir_preview, $abs_preview_path;
	$abs_preview_path = realpath($dir_abs_album . $dir_preview);

	global $dir_option, $abs_option_path;
	$abs_option_path = realpath($dir_abs_album . $dir_option);

	global $pref_preview_exec, $dir_install, $abs_preview_exec;
	$abs_preview_exec = realpath($dir_install . $pref_preview_exec);
}


//****************************
function clear_album_options()
//****************************
// Currently clears:
//	list_hide				- array of filename
//	list_album_icon			- array of icon info { album , file }
{
	global $list_hide;
	global $list_album_icon;

	unset($list_hide);
	unset($list_album_icon);
}


//*********************************
function read_album_options($album)
//*********************************
{
	// first clear current options
	clear_album_options();

	// then grab new ones
	global $abs_preview_path;

	$abs_options = $abs_preview_path . prep_sep($album) . prep_sep(ALBUM_OPTIONS);

	$file = @fopen($abs_options, "rt");
	if (!$file)
		return;

	$var_name = "";
	$local = array();

	while(!feof($file))
	{
		$line = fgets($file, 1023);
		if (substr($line, -1) == "\n")
			$line = substr($line, 0, -1);
		if (substr($line, -1) == "\r")
			$line = substr($line, 0, -1);
		if (!$line || $line == EOF || $line[0] == '#')
			continue;

		if ($line[0] == ':')
		{
			if ($var_name)
			{
				global $$var_name;
				$$var_name = array_merge($$var_name, $local);
				$local = array();
			}

			$var_name = substr($line, 1);
			global $$var_name;
			$$var_name = array();
		}
		else if ($line)
		{
			$local[] = $line;
		}
	}
}


//***************************************************
function write_album_options($album, $silent = false)
//***************************************************
// Currently writes:
//	list_hide				- array of filename
//	list_album_icon			- array of icon info { album , file }
{
	global $list_hide;
	global $list_album_icon;
	global $abs_preview_path;

	$abs_options = $abs_preview_path . prep_sep($album) . prep_sep(ALBUM_OPTIONS);

	$file = fopen($abs_options, "wt");

	if (!$silent)
		echo "write album '$album' options - file: $file<br>\n";

	if (!$file)
		return FALSE;

	fputs($file, "# album options - format: :var_name/val/val.../: to end\n");

	if (count($list_hide) > 0)
	{
		if (!$silent)
			echo "write album options - list_hide: " . count($list_hide) . " items<br>\n";

		fputs($file, ":list_hide\n");
		foreach($list_hide as $str)
			fputs($file, $str . "\n");
	}

	if (count($list_album_icon) > 0)
	{
		if (!$silent)
			echo "write album options - list_album_icon: " . count($list_album_icon) . " items<br>\n";

		fputs($file, ":list_album_icon\n");
		foreach($list_album_icon as $str)
			fputs($file, $str . "\n");
	}

	fputs($file, ":\n");
	fclose($file);

	return TRUE;
}


//-----------------------------------------------------------------------

//************************
function nocache_headers()
//************************
// used by the admin pages to prevent caching
// RM see HTTP doc to determine if html vs. img can be cached selectively (IMG tag?)
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");				// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
	header("Cache-Control: no-cache, must-revalidate");				// HTTP/1.1
	header("Pragma: no-cache");										// HTTP/1.0
}


//***********************************************
function set_cookie_val($name, $val, $set = TRUE)
//***********************************************
// $set: TRUE to set cookie, FALSE to delete cookie
{
	global $HTTP_HOST;
	global $dir_abs_album;
    global $pref_cookie_host;
    global $admin;
    global $_debug_;

	$delay = 3600 * 24 * 365;

    $host = $pref_cookie_host;
	if (!$host)
		$host = $HTTP_HOST;

    $path = $dir_abs_album;

	$time = ($set ? time() + $delay : time() - $delay);
	// $time = gmstrftime("%A, %d-%b-%Y %H:%M:%S", ($set ? time() + $delay : time() - $delay));

	$host = "";
	$path = "";

	// debug
    // if ($admin && $_debug_)
	//	echo "Set Cookie: name='$name' -- val='$val' -- date='$time' -- path='$path' -- host='$host'<br>\n";

	setcookie($name, $val, $time, $path, $host);
}


//***********************
function handle_cookies()
//***********************
// Some literature:
// http://developer.netscape.com:80/docs/manuals/js/client/jsguide/cookies.htm
{
	global $lang;
	global $rig_lang;
	global $current_language;

	global $img_size;
	global $rig_img_size;
	global $pref_image_size;

	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;

	global $force_login,  $keep;
	global $user, 		  $passwd;
	global $admusr, 	  $admpwd;
	global $rig_user,	  $rig_passwd;
	global $rig_adm_user, $rig_adm_passwd;

	if ($lang)
	{
		set_cookie_val("rig_lang", $lang);
		$current_language = $lang;
		$rig_lang = $lang;
	}
	else
	{
		$current_language = $rig_lang;
	}

	if ($img_size)
	{
		// an img_size of '0' or less means to use the original image size
		set_cookie_val("rig_img_size", (int)$img_size);
		$rig_img_size = (int)$img_size;
	}
	else if (!$rig_img_size)
	{
		$rig_img_size = $pref_image_size;
	}

	if (!$force_login && $user)
	{
		// first erase existing cookie (set time to past value)
		set_cookie_val("rig_user"  , $rig_user,	  false);
		set_cookie_val("rig_passwd", $rig_passwd, false);

		$rig_user   = $user;
		$rig_passwd = crypt($passwd);

		if (test_user_pwd(FALSE, &$rig_user, &$rig_passwd))
		{
			// set the expiration date to +1 year if we want to keep it,
			// or 0 if it's only for this session
			if ($keep == 'on')
				$t = $time;
			else
				$t = 0;

			set_cookie_val("rig_user"  , $rig_user);
			set_cookie_val("rig_passwd", $rig_passwd);
		}
	}

	if (!$force_login && $admusr && isset($admpwd))
	{
		// first erase existing cookie (set time to past value)
		set_cookie_val("rig_adm_user"  , $rig_adm_user  , false);
		set_cookie_val("rig_adm_passwd", $rig_adm_passwd, false);

		$rig_adm_user   = $admusr;
		$rig_adm_passwd = crypt($admpwd);

		if (test_user_pwd(TRUE, &$rig_adm_user, &$rig_adm_passwd))
		{
			// set the expiration date to +1 year if we want to keep it,
			// or 0 if it's only for this session
			if ($keep == 'on')
				$t = $time;
			else
				$t = 0;

			set_cookie_val("rig_adm_user"  , $rig_adm_user);
			set_cookie_val("rig_adm_passwd", $rig_adm_passwd);
		}
	}
}


//**************************************
function remove_login_cookies($is_admin)
//**************************************
{
	// debug
    // echo "remove login cookies $is_admin<br>\n";

	if ($is_admin)
	{
		set_cookie_val("rig_adm_user"  , $rig_adm_user  , false);
		set_cookie_val("rig_adm_passwd", $rig_adm_passwd, false);
	}
	else
	{
		set_cookie_val("rig_user"  , $rig_user  , false);
		set_cookie_val("rig_passwd", $rig_passwd, false);
	}
}


//**************
function setup()
//**************
{
	// List of globals defined for the album page by prepare_album():
	// $current_album		- string
	// $display_language	- string
	// $display_date		- string
	// $display_softname	- string, constant

	global $pref_umask;
	global $current_language;
	global $display_language;
	global $display_date;
	global $display_softname;
	global $html_date;

	// -- setup umask

	if ($pref_umask)
		umask($pref_umask);

	// -- setup language
	switch($current_language)
	{
		case 'fr':
			$display_language = 'Francais';
			break;
		case 'en':
		default:
			$current_language = 'en';
			$display_language = 'English';
	}

	// -- setup date & soft name
	$display_date = date($html_date);
	$display_softname = SOFT_NAME;
}


//-----------------------------------------------------------------------


//***************************************
function prepare_album($album, $title="")
//***************************************
{
	// List of globals defined for the album page by prepare_album():
	// $current_album		- string
	// $display_title		- string
	// $display_album_title	- string

	global $current_album;
	global $display_title;
	global $display_album_title;
	global $html_album, $html_none;

	$current_album = decode_argument($album);

	// -- setup title of album
	if (!$title)
		$title = $html_album;

	if ($album)
	{
		$items = explode(SEP, $current_album);
		$pretty = pretty_name($items[count($items)-1], FALSE, TRUE);
		$display_title = "$title - " . $pretty;
		$display_album_title = "$html_album - " . $pretty;
	}
	else
	{
		$display_title = "$title - $html_none";
		$display_album_title = "$html_album - $html_none";
	}

	read_album_options($current_album);
}


//***********************************
function build_recursive_list($album)
//***********************************
// returns a list with pairs ($album, $file)
{
	global $abs_album_path;

	// make sure we have the options for this album
	read_album_options($album);

	// get the absolute album path
	$abs_dir = $abs_album_path . prep_sep($album);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$result = array();
	$handle = @opendir($abs_dir);
	if ($handle)
	{
		create_preview_dir($album);

		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..' && is_visible($file))
			{
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = post_sep($album) . $file;
					$res = build_recursive_list($name);
					if (is_array($res) && count($res)>0)
						$result = array_merge($result, $res);

					// restore the options for this album
					// (the local array will have been modified by the recursive call)
					read_album_options($album);
				}
				else if (valid_ext($file))
				{
					// create entry and add it
					$entry = array($album, $file);
					$result[] = $entry;
			    }
			}
		}
		closedir($handle);
	}

	return $result;
}



//******************************
function cmp_pretty_name($a, $b)
//******************************
{
	// $a = pretty_name($a);
	// $b = pretty_name($b);
	return strcasecmp($a, $b);
}


//*****************************************
function load_album_list($show_all = FALSE)
//*****************************************
{
	// This function populates the folowing 
	// $list_albums			- array of string
	// $list_images			- array of filename

	global $list_albums;
	global $list_images;
	global $current_album;
	global $abs_album_path;

	$abs_dir = $abs_album_path . prep_sep($current_album);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$list_images = array();
	$handle = @opendir($abs_dir);
	if (!$handle)
		html_error("Album directory '$abs_dir' does not exist!");
	else
	{
		create_preview_dir($current_album);

		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..' && ($show_all || is_visible($file)))
			{
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$list_albums[] = $file;
				}
				else if (valid_ext($file))
				{
			    	$list_images[] = $file;
			    }
			}
		}
		closedir($handle);
	}

	if (count($list_albums))
		usort($list_albums, "cmp_pretty_name");

	if (count($list_images))
		usort($list_images, "cmp_pretty_name");
}


//*******************
function has_albums()
//*******************
{
	global $list_albums;
	return (count($list_albums) >= 1);
}


//*******************
function has_images()
//*******************
{
	global $list_images;
	return (count($list_images) >= 1);
}


//************************
function is_visible($item)
//************************
{
	global $list_hide;
	return !$list_hide || !in_array($item, $list_hide, TRUE);
}

//-----------------------------------------------------------------------


//***********************************************
function prepare_image($album, $image, $title="")
//***********************************************
{
	setup();

	// List of globals defined for the album page by prepare_album():
	// $current_image		- string
	// $pretty_image		- string
	// $current_album		- string
	// $current_img_info	- array of {format, width, height}
	// $display_title		- string
	// $display_album_title	- string

	global $current_album;
	global $current_image;
	global $current_img_info;
	global $pretty_image;
	global $display_title;
	global $display_album_title;
	global $html_image;

	$current_album = decode_argument($album);
	$current_image = decode_argument($image);
	$pretty_image  = pretty_name($current_image, FALSE);

	$current_img_info = build_info($current_album, $current_image);

	// -- setup title of album
	if ($title)
		$title .= " - ";

	$display_title = $title . $pretty_image;

	if ($album)
	{
		$items = explode(SEP, $current_album);
		$display_album_title = pretty_name($items[count($items)-1]);
	}

	read_album_options($current_album);
}


//*****************************
function get_images_prev_next()
//*****************************
{
	// this function exports the following variables:
	// display_prev_link	- string
	// display_prev_img		- string
	// display_next_link	- string
	// display_next_img		- string

	global $display_prev_link;
	global $display_prev_img;
	global $display_next_link;
	global $display_next_img;
	global $current_album;
	global $current_image;
	global $list_images;

# array_search is >= PHP 4.0.5
#	$key = array_search($current_image, $list_images, TRUE);
    $key = FALSE;
    foreach($list_images as $n => $item)
    {
        if ($item == $current_image)
        {
            $key = $n;
            break;
        }
        $n++;
    }


	// echo "current = $current_image -- array = $list_images -- key = $key";

	if (is_bool($key) && $key == FALSE)
	{
		html_error("Can't find image '$current_image' in internal list!");
		return;
	}

	if ($key > 0)
	{
		$file = $list_images[$key-1];

		$pretty = pretty_name($file, FALSE);
		$preview = encode_url_link(build_preview($current_album, $file));

		$display_prev_link = self_url($file);
		$display_prev_img = "<img src=\"$preview\" alt=\"$pretty\" border=0>";
	}

	if ($key < count($list_images)-1)
	{
		$file = $list_images[$key+1];

		$pretty = pretty_name($file, FALSE);
		$preview = encode_url_link(build_preview($current_album, $file));

		$display_next_link = self_url($file);
		$display_next_img = "<img src=\"$preview\" alt=\"$pretty\" border=0>";
	}
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//	Revision 1.1  2001/11/26 00:07:37  ralf
//	Starting version 0.6: location and split of site vs album files
//	
//	Revision 1.30  2001/11/17 12:35:58  ralf
//	Manage albums with dates YMD and display as MDY. Version 0.5.2
//	
//	Revision 1.29  2001/10/24 07:13:14  ralf
//	fix for backslashes
//	
//	Revision 1.28  2001/10/21 02:15:16  ralf
//	debug
//	
//	Revision 1.27  2001/10/20 02:06:56  ralf
//	Marc's patch Sept-2001
//	
//	Revision 1.26  2001/10/18 18:27:09  ralf
//	cookie debug, changed default size, version 0.3
//	
//	Revision 1.25  2001/09/05 07:47:21  ralf
//	fix: url link should not encode dot or slash!
//	
//	Revision 1.24  2001/09/05 07:42:25  ralf
//	fix
//	
//	Revision 1.23  2001/09/05 07:19:02  ralf
//	Encode URLs and links using % hex hex.
//	Backslash special characters of filenames before running Thumbnail.
//	
//	Revision 1.22  2001/09/05 05:43:53  ralf
//	fix for marc
//	
//	Revision 1.21  2001/08/31 08:40:10  ralf
//	cookie mess
//	
//	Revision 1.20  2001/08/31 08:09:25  ralf
//	try to make cookies work
//	
//	Revision 1.19  2001/08/31 07:10:46  ralf
//	Cookie path
//	
//	Revision 1.18  2001/08/31 03:14:25  ralf
//	added pref cookie host
//	
//	Revision 1.17  2001/08/31 02:34:17  ralf
//	Auto guest mode
//	
//	Revision 1.16  2001/08/27 23:57:59  ralf
//	fix in cookie for image size
//	
//	Revision 1.15  2001/08/27 09:21:16  ralf
//	fix for cookie size when changed from original to something else
//	
//	Revision 1.14  2001/08/27 09:13:11  ralf
//	fixed problem with original size
//	
//	Revision 1.13  2001/08/27 08:47:18  ralf
//	splitted common in 3 parts
//	
//	Revision 1.12  2001/08/16 19:01:35  ralf
//	store album icon in album options (need more debug)
//	
//	Revision 1.11  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//	
//	Revision 1.10  2001/08/14 08:06:57  ralf
//	Fixes for login & redirection. Passwd entry no longer necessary in url
//	
//	Revision 1.9  2001/08/13 05:37:36  ralf
//	Fixes in preview creation, added back album links, etc.
//	
//	Revision 1.8  2001/08/13 01:43:35  ralf
//	Changed appareance of album table
//	
//	Revision 1.7  2001/08/10 03:55:43  ralf
//	Fixed filter for images names in form number.jpg
//	
//	Revision 1.6  2001/08/07 18:28:03  ralf
//	Rename Canon Images
//	
//	Revision 1.5  2001/08/07 09:04:30  ralf
//	Updated ID and VIM tag
//	
//	Revision 1.4  2001/08/07 09:01:17  ralf
//	Added globals for the html colors (in pref).
//	Fixed &lang in the language change URL
//	
//	Revision 1.3  2001/08/07 08:04:17  ralf
//	Added a cvs log entry
//	
//-------------------------------------------------------------
?>
