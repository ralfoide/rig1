<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

/*
	List of URL-variables:
	----------------------
	idn						- integers album or image indexes
	album					- string
	img						- string
	credits					- boolean string 'on' or nothing
	login					- boolean string 'force' or nothing
	keep					- boolean string 'on' or nothing
	user					- string username
	passwd					- string passwd (clear)
	admusr					- string username
	admpwd					- string passwd (clear)
	apage					- integer (-1: disable, 0: default, 1..N: select page, cf rig_prepare_album for comments)
	ipage					- integer (-1: disable, 0: default, 1..N: select page, cf rig_prepare_album for comments)

	IMPORTANT [RM 20040702] As of RIG 0.6.4.5, the "image" URL-Query variable is being
	renamed "img" to avoid a conflict with lesser browsers that interpret "&image" incorrectly
	as the HTML Entity "&image;" (namely I'm refering to Pocket Internet Explorer here.)


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
	current_language		- string 'en' (default) or 'fr', 'sp', 'jp'
	current_album			- string
	current_real_album		= sttring					-- RM 20030907
	current_image			- string
	current_type			- string 'img' or 'video' -- RM 20030713
	current_album_page		- integer (-1, 0, 1..N: cf rig_prepare_album for comments -- RM 20030908) 
	current_image_page		- integer (-1, 0, 1..N: cf rig_prepare_album for comments -- RM 20030908) 
	rig_file_types			- array of {string, string} tuples
	list_albums				- array of string
	list_images				- array of filename
	display_title			- string
	display_album_title		- string
	display_language		- string
	display_exec_date		- string
	display_softname		- string, constant
	display_prev_link		- string
	display_prev_img		- string
	display_next_link		- string
	display_next_img		- string


	List of global access paths:
	----------------------------
	dir_abs_album
	dir_images
	dir_album
	dir_image_cache
	dir_album_cache
	dir_option
	abs_images_path
	abs_album_path
	abs_image_cache_path
	abs_option_path
	abs_preview_exec
	abs_upload_src_path
	abs_upload_album_path


	List of globals (from album options):
	-------------------------------------
	list_hide				- array of filename
	list_album_icon			- array of icon info { a:album(relative) , f:file, s:size }
	list_description		- array of [filename] => description (text and/or html) -- RM 20030713

*/
//-----------------------------------------------------------------------

define("SEP_URL", "/");
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
define("RIG_SOFT_NAME",			"RIG");							// RM 20030918 removed "Ralf Image Gallery"
define("RIG_SOFT_URL",			"http://rig.powerpulsar.com");
define("ALBUM_ICON",			"album_icon.jpg");
// define("ALBUM_OPTIONS",		"options");						// RM 20030809 obsolete
define("ALBUM_OPTIONS_TXT",		"options.txt");
define("ALBUM_OPTIONS_XML",		"options.xml");

define("DESCRIPTION_TXT",		"descript.ion");				// RM 20030713
define("FILEINFODIZ_TXT",		"file_info.diz");

define("ALBUM_CACHE_NAME",		"cache_");				// RM 20030809
define("ALBUM_CACHE_EXT",		".html");

// start timing...
$time_start = rig_getmicrotime();

// read site-prefs and then override with local prefs, if any
require_once(rig_require_once("prefs.php", $dir_abs_globset));

// $dir_abs_locset is optional: it is either an empty string or an absolute path -- RM 20030919 fixed
//	RM 20040708 fix: missing rig_post_sep
if (is_string($dir_abs_locset) && $dir_abs_locset != "" && rig_is_file(rig_post_sep($dir_abs_locset) . "prefs.php"))
	require_once(rig_post_sep($dir_abs_locset) . "prefs.php");

// setup...
require_once(rig_require_once("version.php"));
require_once(rig_require_once("login_util.php"));

rig_read_prefs_paths();
rig_handle_cookies();


// DEBUG default prefs/curr for lang and theme
// echo "<p> rig_version = "   ; var_dump($rig_version);
// echo "<p> pref_default_lang = "   ; var_dump($pref_default_lang);
// echo "<p> current_language = "; var_dump($current_language);
// echo "<p> pref_default_theme = "; var_dump($pref_default_theme);
// echo "<p> current_theme = "   ; var_dump($current_theme);


// include language strings
//-------------------------
// RM 20020714 fix: always load the str_en first
require_once(rig_require_once("str_en.php", $dir_abs_src, $abs_upload_src_path));

// and override with other language if not english

// DEBUG
// rig_check_src_file(rig_post_sep($dir_abs_src) . "str_$current_language.php");


// Fix (Paul S. 20021013): if requested lang doesn't exist, revert to english
if (!isset($current_language) || !rig_is_file(rig_post_sep($dir_abs_src) . "str_$current_language.php"))
	$current_language = $pref_default_lang;

if (is_string($current_language) && $current_language != 'en')
{
	require_once(rig_require_once("str_$current_language.php", $dir_abs_src, $abs_upload_src_path));
}

// include theme strings
//----------------------

// DEBUG
// rig_check_src_file($dir_abs_src . "theme_$current_theme.php");

if (!isset($current_theme) || !rig_is_file(rig_post_sep($dir_abs_src) . "theme_$current_theme.php"))
	$current_theme = $pref_default_theme;

require_once(rig_require_once("theme_$current_theme.php", $dir_abs_src, $abs_upload_src_path));

// load common source code -- note these do not use the src_upload override
require_once(rig_require_once("common_display.php"));
require_once(rig_require_once("common_images.php"));
require_once(rig_require_once("common_xml.php"));			// RM 20030216
require_once(rig_require_once("common_comment.php"));		// RM 20030928
require_once(rig_require_once("common_video.php"));		// RM 20030928

rig_setup();
rig_create_option_dir("");

// RM 20021021 not for rig 062 yet
// require_once(rig_require_once("common_db.php"));

rig_setup_db();


//-----------------------------------------------------------------------
//-----------------------------------------------------------------------

//***************************************
function rig_html_error($title_str,
						$error_str,
						$file_str = NULL,
						$php_str  = NULL)
//***************************************
{
	global $color_table_bg;
	global $color_error1_bg;
	global $color_error2_bg;

	// Trick to close the title (if we were writing the title and the header)
	// and to close enough tables to be wide-screen. This assumes that most browser
	// will silently ignore what is blatlantly offensive!
	echo "</title></head></table></table></table></table>";
	echo "<body>\n";

	if (!$title_str)
		$title_str = "A Runtime Error Occured";

	echo "<center><table border=1 bgcolor=\"$color_error1_bg\" width=\"100%\" cellpadding=\"5\">\n";

	// title
	echo "<tr><td bgcolor=\"$color_error1_bg\"><center><font size=\"+2\">$title_str</font></center></td></tr>\n";

	// description
	echo "<tr><td bgcolor=\"$color_error2_bg\">\n $error_str\n </td></tr>\n";

	// file argument
	if ($file_str != NULL)
	{
		if (is_array($file_str))
		{
			echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>File:</b><pre>";
			var_dump($file_str);
			echo "<pre></td></tr>\n";
		}
		else
		{
			echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>File:</b> $file_str\n </td></tr>\n";
		}
	}

	// php error msg
	if ($php_str != NULL)
	{
		if (is_array($php_str))
		{
			echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>PHP Error:</b><pre>";
			var_dump($php_str);
			echo "</pre>\n</td></tr>\n";
		}
		else
		{
			echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>PHP Error:</b> $php_str\n </td></tr>\n";
		}
	}

	echo "</table></center><p>\n";

	// Also assumes that browsers will continue displaying the HTML after a
	// bad </body>.
	echo "</body>\n";

	return FALSE;
}

//-----------------------------------------------------------------------


//*****************************************************************************
function rig_require_once($filename, $abs_main_dir = "", $abs_override_dir = "")
//******************************************************************************
// RM 20030308
//
// Includes a PHP source file, looking in $abs_$main_dir
// or $abs_override_dir. The override dir is checked FIRST and is ABSOLUTE!
// it's purpose is to override the main file with the overriding one.
//
// If $abs_main_dir is not specified, it defaults to $dir_abs_src from location.php
// simply because this function is most generally used to include source files.
//
// It is ok for the override dir not to exist or not contain the file.
// It is mandatory that the main dir exists and contains the file.
//
// IMPORTANT: require_once uses the caller's scope which means the
// file can't be included/required here or it wouldn't have a global scope
// thus this function actually returns a string with the file to be
// required and it's up to the caller to actually perform the require_once().
{
	global $dir_abs_src, $abs_upload_src_path;

	// DEBUG
	// echo "<p>rig_require_once: filename='$filename', abs_main_dir='$abs_main_dir', abs_override_dir='$abs_override_dir' \n";

	if (is_string($abs_main_dir) && $abs_main_dir != "")
		$main = rig_post_sep($abs_main_dir);
	else
		$main = rig_post_sep($dir_abs_src);

	if (is_string($abs_override_dir) && $abs_override_dir != "")
		$over = rig_post_sep($abs_override_dir);
	else
		$over = "";

	// check params

	if (!$filename)
	{
		return rig_html_error("Invalid parameter!",
			                  "Empty 'filename' argument in function rig_require_once!",
	            		      $main);
	}

	// check main file exists -- it must, even if we're going to use the override

	if (!(@file_exists(rig_check_src_file($main . $filename))))
		return FALSE;

	// check override file and use it exists
	if ($over && rig_is_file($over . $filename))
	{
		return $over . $filename;
	}

	// otherwise default to the main one
	return $main . $filename;
}


//-----------------------------------------------------------------------


//**********************************************
function rig_get($array, $name, $default = NULL)
//**********************************************
{
	if (isset($array) && isset($name) && isset($array[$name]))
		return $array[$name];
	
	return $default;
}


//**********************************
function rig_unset_global($var_name)
//**********************************
{
	// RM 20040204 IMPORTANT! There's a very weird behavior in PHP
	// when using unset() on a global variable from within a function:
	// It does not unset the global but instead of the "local" view of
	// global from the function's scope.
	//
	// I personnally consider this a bug. This method does the Right
	// Thing [tm] and really unsets a global as indicated by the PHP.net
	// page on unset.
	
	unset($GLOBALS[$var_name]);
}

//-----------------------------------------------------------------------

//*************************
function rig_is_file($name)
//*************************
{
    return file_exists($name) && is_file($name);
}

//************************
function rig_is_dir($name)
//************************
{
    return file_exists($name) && is_dir($name);
}

//*************************
function rig_getmicrotime()
//*************************
// extracted from PHP doc for microtime()
{
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
} 


//*************************
function rig_time_elapsed()
//*************************
{
	global $time_start;
	return sprintf("%2.2f", rig_getmicrotime() - $time_start);
}


//***************************************
function rig_php_array_search($val, $arr)
//***************************************
// Simulates array_search for PHP <= 4.0.5
// Searches for value "val" in array "arr" and return the array's key
{
	if (PHP_VERSION > "4.0.5")
	{
		# array_search is >= PHP 4.0.5
		$k = array_search($val, $arr, TRUE);
		
		// http://www.php.net/manual/en/function.array-search.php
		// "prior to PHP 4.2.0, array_search returns NULL instead of FALSE"
		if ($k === NULL)
			$k = FALSE;
		
		return $k;
	}
	else
	{
		$key = FALSE;
		foreach($arr as $n => $item)
		{
			if ($item == $val)
			{
				return $n;
				break;
			}
			$n++;
		}
	}

	return FALSE;
}


//********************************
function rig_print_array_str($str)
//********************************
{
	echo "str[] = '$str'<br>\n";
	for($i=0, $n=strlen($str); $i<$n; $i++)
	{
		$a = ord($str{$i});
		echo "str[$i] = {$a} = '{$str{$i}}'<br>\n";
	}
}


//*************************
function rig_var_dump($str)
//*************************
// For debug purposes -- RM 20030928
{
	global $$str;
	echo "<br><b>$str =</b> ";
	var_dump($$str);
}


//*************************
function rig_prep_sep($str)
//*************************
{
	if ($str && $str[0] != SEP)
		return SEP . $str;
	else
		return $str;
}


//*************************
function rig_post_sep($str)
//*************************
{
	if ($str && $str[strlen($str)-1] != SEP)
		return $str . SEP;
	else
		return $str;
}


//*************************
function rig_post_url($str)
//*************************
// RM 20030629 v0.6.3.4
{
	if ($str && $str[strlen($str)-1] != SEP_URL)
		return $str . SEP_URL;
	else
		return $str;
}


//*******************************
function rig_get_file_type($name)
//*******************************
// Returns the file type string ("image/jpeg" or "video/qt|avi") for the file.
// Returns an empty string if the file is not supported.
// RM 20030628 new v0.6.3.4
// RM 20030928 using rig_file_types which combines pref_internal_file_types
// and pref_extra_file_types
{
	global $rig_file_types;

	if (is_array($rig_file_types) && count($rig_file_types) > 0)
	{
		foreach($rig_file_types as $filter => $type)
		{
			if (preg_match($filter, $name) > 0)
				return $type;
		}
	}

	return "";
}


//***************************
function rig_valid_ext($name)
//***************************
// RM 20030628 changed the use file types
{
	return rig_get_file_type($name) != "";
}


//********************************
function rig_decode_argument($arg)
//********************************
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


//********************************
function rig_encode_argument($arg)
//********************************
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


//********************************
function rig_encode_url_link($arg)
//********************************
// Encode IMG SRC and HREF links
{
	// Now protect characters that have a meaning in HTTP URLs.
	// cf Section 3.2 of RFC 2068 HTTP 1.1
	// reserved = ";/?:@&=+";
	// extra    = "!*'(),";
	// unsafe   = " \"#%<>";
	// safe     = "$-_.";

	// RM 20020713 note: tried to encode c>=127 into &#dd;
	// but that breaks URLs and many other things.
	

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



//*******************************
function rig_shell_filename($str)
//*******************************
// Encode a filename before using it in a shell argument call
// The thumbnail app will un-backslash the full argument filename before using it
{
	if (PHP_OS == 'WINNT')
	{
		// RM 102201 -- escapeshellarg is "almost" a good candidate for linux
		// but for windows we need escapeshellcmd because a path may contain backslashes too

		// RM 20040709 For some reason with a default install of PHP 4.3.7
		// on IIS 5/Windows XP, escapeshellcmd removes all backslashes.
		// In fact on this case escapeshellcmd seemed to not escape anything but
		// simply remove any "dangerous" shell stuff instead. Most innapropriate.

		// Escape a bunch of characters except : which is used for full DOS-like paths

		$s = $str;
		
		$s = str_replace("\\", "\\\\", $s);
		$s = str_replace("!", "\\!", $s);
		$s = str_replace("@", "\\@", $s);
		$s = str_replace("#", "\\#", $s);
		$s = str_replace("$", "\\$", $s);
		$s = str_replace("%", "\\%", $s);
		$s = str_replace("^", "\\^", $s);
		$s = str_replace("&", "\\&", $s);
		$s = str_replace("*", "\\*", $s);
		$s = str_replace("(", "\\(", $s);
		$s = str_replace(")", "\\)", $s);
		$s = str_replace("'", "\\'", $s);
		$s = str_replace("\"", "\\\"", $s);
		$s = str_replace(";", "\\:", $s);
		$s = str_replace(",", "\\,", $s);

		$s = "\"" . $s . "\"";

	}
	else
	{
		$s = "\"" . escapeshellcmd($str) . "\"";
	}
	
	return $s;
}


//********************************
function rig_shell_filename2($str)
//********************************
// Encode a filename before using it in a shell argument call
// This one is more dedicated for directly unix calls.
// Escapeshellcmd will transform ' into \' which is not always appropriate.
{
	if (PHP_OS == 'WINNT')
	{
		// RM 20040709 use our own escape routine
		// $s = "\"" . escapeshellcmd($str) . "\"";
		$s = rig_shell_filename($str);
	
		// $s = str_replace("\\'", "'", $s);
	}
	else
	{
		$s = "\"" . escapeshellcmd($str) . "\"";
		$s = str_replace("\\'", "'", $s);
	}
	
	return $s;
}


//***********************************
function rig_simplify_filename($name)
//***********************************
{
	$name = trim($name);
	// replace weird characters by underscores
	$name = strtr($name, " \'\"\\/&" , "______");
	return $name;
}


//***********************************************
function rig_pretty_name($name,
						 $strip_numbers = TRUE,
						 $pretty_dirname = FALSE)
//***********************************************
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
	    if (ereg("^([0-9]{4})[-/]([0-9]{2})[-/]([0-9]{2})$", $name, $reg))
		{
            // First deal with full dates with optional date separators
            // RM 20020713 fix: added optional separators (for albums name with only a date value)
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


//*************************************
function rig_mkdir($base, $path, $mode)
//*************************************
// RM 20030124
// This function creates a full path, recursively if needed.
//
// This function adds a bit of checking on the base directory.
// One has to exist since RIG never changes base directories.
{
	// first, explode the path
	$dirs = explode(SEP, $path);

	$n = count($dirs);

	// if there are no directories to create, do nothing
	if ($n < 1 || ($n == 1 && $dirs[0] == ""))
		return TRUE;

	// the very first part must always exists and be a directory
	// RIG does not create root directories, by security
	$full = $base;
	if (!rig_is_dir($base))
    {
		return rig_html_error( "Create Directory",
		                       "Non-existant base directory<br>\n" .
    		                   "RIG does not create base directories, by security.\n",
            		           $base,
                		       $php_errormsg);
    }

	// check for or create all the intermediary paths
	foreach($dirs as $dir)
	{
		// reject ".." directories, ignore "." and empty directories
		if ($dir == "..")
		{
			return rig_html_error( "Create Directory",
			                       "Invalid \"..\" directory name in path<br>\n",
	            		           $path,
	                		       $php_errormsg);
	    }

		if ($dir != "." && $dir != "")
		{
			// get the full path up to the current component
			$full = $full . rig_prep_sep($dir);
		
			// create if it does not exists
			if (!rig_is_dir($full))
				if (!mkdir($full, $mode))
				{
					return rig_html_error( "Directory Creation Failed",
					                       "Directory mode is $mode\n",
			            		           $full,
			                		       $php_errormsg);
				}
		}
	}

	return TRUE;
} 

//*************************************
function rig_create_preview_dir($album)
//*************************************
{
	global $pref_mkdir_mask;
	global $abs_image_cache_path;
	global $abs_album_cache_path;

	if (!rig_mkdir($abs_image_cache_path, $album, $pref_mkdir_mask))
	{
		return rig_html_error("Create Image Cache Directory",
							  "Failed to create directory",
							  $album,
							  $php_errormsg);
	}

	if ($abs_album_cache_path != $abs_image_cache_path)
	{
		if (!rig_mkdir($abs_album_cache_path, $album, $pref_mkdir_mask))
		{
			return rig_html_error("Create Album Cache Directory",
								  "Failed to create directory",
								  $album,
								  $php_errormsg);
		}
	}

	return TRUE;
}


//************************************
function rig_create_option_dir($album)
//************************************
{
	global $pref_mkdir_mask;
	global $abs_option_path;

	if (!rig_mkdir($abs_option_path, $album, $pref_mkdir_mask))
    {
        global $dir_abs_album, $dir_option;
		return rig_html_error( "Create Options Directory",
		                       "Failed to create directory<br>\n" .
    		                   "<b>Dir Abs Album:</b> $dir_abs_album<br>\n" . 
        		               "<b>Dir Option:</b> $dir_option\n",
            		           $album,
                		       $php_errormsg);
    }

	return TRUE;
}


// defines for in_page in rig_self_url -- RM 20030308
define("RIG_SELF_URL_NORMAL",		0);	// album+image user view
define("RIG_SELF_URL_ADMIN",		1);	// album+image admin view
define("RIG_SELF_URL_UPLOAD",		2);	// upload *admin* view
define("RIG_SELF_URL_TRANSLATE",	3);	// translate *admin* view

//*****************************************************************
function rig_self_url($in_image = -1,
					  $in_album = -1,
					  $in_page  = -1,
					  $in_extra = "",
					  $in_apage = -1,
					  $in_ipage = -1)
//*****************************************************************
// encode album/image name as url links
// in_image: -1 (use current if any) or text for image=...
// in_album: -1 (use current if any) or text for album=...
// in_page : -1 (use current if any) or RIG_SELF_URL_xxx (see above)
// in_extra: extra parameters (in the form name=val&name=val etc)
//
// Use URL-Rewriting when defined in prefs [RM 20030107]
{
	global $current_album;
	global $current_image;
	global $current_album_page;		// RM 20030908
	global $current_image_page;		// RM 20030908
	global $pref_url_rewrite;		// RM 20030107


	// RM 20040703 using "img" query param instead of "image"

	$image		= rig_get($_GET,'img'		);
	$album		= rig_get($_GET,'album'		);
	$admin		= rig_get($_GET,'admin'		);
	$translate	= rig_get($_GET,'translate'	);
	$upload		= rig_get($_GET,'upload'	);
	$credits	= rig_get($_GET,'credits'	);
	$phpinfo	= rig_get($_GET,'phpinfo'	);
	$_debug_	= rig_get($_GET,'_debug_'	);

	// DEBUG
	// echo "<p>rig_self_url: in_page=$in_page\n";


	// Using Url rewrite?

	$use_rewrite = (is_array($pref_url_rewrite) && count($pref_url_rewrite) >= 3);

	if ($use_rewrite)
		$url = $pref_url_rewrite['index'];
	else
		$url = rig_get($_SERVER, 'PHP_SELF');	// RM 20040516 use rig_get to access global

	// Parameters and ? character to start parameters

	$params = "";
	$param_concat_char = "?";

	// Prepare album variable

	if ($in_album == -1)
	{
		$in_album = $current_album;

		if (!$in_album)
			$in_album = rig_decode_argument($album);
	}

	if ($in_album)
		$in_album = rig_encode_url_link($in_album);


	// Prepare image variable

	if ($in_image == -1)
	{
		$in_image = $current_image;

		if (!$in_image)
			$in_image = rig_decode_argument($image);
	}

	if ($in_image)
		$in_image = rig_encode_url_link($in_image);


	// Page type parameter

	if (is_int($in_page) && $in_page == -1)
	{
		// translate and upload imply admin so they must be tested first
		if ($translate)		$in_page = RIG_SELF_URL_TRANSLATE;
		else if ($upload)	$in_page = RIG_SELF_URL_UPLOAD;
		else if ($admin)	$in_page = RIG_SELF_URL_ADMIN;
		else				$in_page = RIG_SELF_URL_NORMAL;
	}

	// switch on in_page values
	switch($in_page)
	{
		case RIG_SELF_URL_ADMIN:
			rig_url_add_param($params, 'admin',		'on');
			break;

		case RIG_SELF_URL_TRANSLATE:
			rig_url_add_param($params, 'admin',		'on');
			rig_url_add_param($params, 'translate', 'on');
			break;

		case RIG_SELF_URL_UPLOAD:
			rig_url_add_param($params, 'admin',		'on');
			rig_url_add_param($params, 'upload',	'on');
			break;
	}


	// Add album parameter

	if ($in_album)
	{
		if ($use_rewrite)
		{
			$url = $pref_url_rewrite['album'];
			$param_concat_char = "&";
		}
		else
		{
			rig_url_add_param($params, 'album', $in_album);
		}
	}

	
	// Add image parameter

	if ($in_image)
	{
		if ($use_rewrite)
		{
			$url = $pref_url_rewrite['img'];
			$param_concat_char = "&";
		}
		else
		{
			rig_url_add_param($params, 'img', $in_image);
		}
	}


	// Add album pagination index

	if ($in_apage > -1)
	{
		if ($in_apage > 1)
			rig_url_add_param($params, 'apage', $in_apage);
	}
	else if (is_integer($current_album_page) && $current_album_page > 1)
	{
		rig_url_add_param($params, 'apage', $current_album_page);
	}


	// Add image pagination index

	if ($in_ipage > -1)
	{
		if ($in_ipage > 1)
			rig_url_add_param($params, 'ipage', $in_ipage);
	}
	else if (is_integer($current_image_page) && $current_image_page > 1)
	{
		rig_url_add_param($params, 'ipage', $current_image_page);
	}


	// Add debug, credits and phpinfo parameters

	if ($_debug_)
		rig_url_add_param($params, '_debug_', '1');

	if ($credits == 'on')
		rig_url_add_param($params, 'credits', $credits);

	if ($phpinfo == 'on')
		rig_url_add_param($params, 'phpinfo', $phpinfo);


	// Add any extra
	// the extra must always be the last one

	if ($in_extra)
	{
		// don't add the & if the extra is <a name=> jump label (#label)
		if ($params && $in_extra[0] != '#')
			$params .= "&";

		$params .= "$in_extra";
	}


	// [RM 20030107]
	if ($use_rewrite)
	{
		// Replace %A by album name and %I by image name
		$url = str_replace('%A', $in_album, $url);
		$url = str_replace('%I', $in_image, $url);
	}

	if ($params)
		return $url . $param_concat_char . $params;
	else
		return $url;
}


//***********************************************************
function rig_url_add_param(&$inout_url, $in_param, $in_value)
//***********************************************************
// RM 20030308 utility function to add one parameter in rig_self_url()
{
	
	// param can't be empty
	if (!is_string($in_param) || $in_param == '')
		return;

	// param must end with a '='
	if ($in_param[strlen($in_param)-1] != '=')
		$in_param .= '=';
	
	// check param is not already in the url
	if (!strstr($inout_url, $in_param))
	{
		// append to url
		if ($inout_url)
			$inout_url .= '&';

		// add param=value to the url
		$inout_url .= $in_param . $in_value;
	}
}


//-----------------------------------------------------------------------


//*****************************
function rig_read_prefs_paths()
//*****************************
{
	global $dir_abs_album;

	// append a separator to the abs album dir if not already done
	$dir_abs_album = rig_post_sep($dir_abs_album);

	// make some paths absolute
	// RM 20021021 check these absolute paths

	// --- image directory (rig's own images) ---
	// RM 20030628 added v0.6.3.4

	global $dir_images, $abs_images_path;
	$abs_images_path   = realpath($dir_abs_album . $dir_images);	// RM 20030726 $dir_abs_images => $dir_abs_album

	if (!is_string($abs_images_path))
	{
		rig_html_error("Missing Image Directory",
					   "Can't get absolute path for the images directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_images<br>" .
					   "<b>Target directory:</b> $dir_images<br>" ,
					   $dir_abs_images . $dir_images);
	}

	// --- album directory ---

	global $dir_album, $abs_album_path;
	$abs_album_path   = realpath($dir_abs_album . $dir_album);

	if (!is_string($abs_album_path))
	{
		rig_html_error("Missing Album Directory",
					   "Can't get absolute path for the album directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_album<br>" ,
					   $dir_abs_album . $dir_album);
	}

	// --- cache directory ---

	global $dir_image_cache, $abs_image_cache_path;
	$abs_image_cache_path = realpath($dir_abs_album . $dir_image_cache);

	if (!is_string($abs_image_cache_path))
	{
		rig_html_error("Missing Image Cache Directory",
					   "Can't get absolute path for the previews directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_image_cache<br>" ,
					   $dir_abs_album . $dir_image_cache);
	}

	global $dir_album_cache, $abs_album_cache_path;
	$abs_album_cache_path = realpath($dir_abs_album . $dir_album_cache);

	if (!is_string($abs_album_cache_path))
	{
		rig_html_error("Missing Album Cache Directory",
					   "Can't get absolute path for the previews directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_album_cache<br>" ,
					   $dir_abs_album . $dir_album_cache);
	}

	// --- options directory ---

	global $dir_option, $abs_option_path;
	$abs_option_path = realpath($dir_abs_album . $dir_option);

	if (!is_string($abs_option_path))
	{
		rig_html_error("Missing Options Directory",
					   "Can't get absolute path for the options directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_option<br>" ,
					   $dir_abs_album . $dir_option);
	}

	// --- upload_src directory ---

	global $dir_upload_src, $abs_upload_src_path;
	$abs_upload_src_path = realpath($dir_abs_album . $dir_upload_src);

	if (!is_string($abs_upload_src_path))
	{
		rig_html_error("Missing Upload Sources Directory",
					   "Can't get absolute path for the upload_src directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_upload_src<br>" ,
					   $dir_abs_album . $dir_upload_src);
	}

	// --- upload_album directory ---

	global $dir_upload_album, $abs_upload_album_path;
	$abs_upload_album_path = realpath($dir_abs_album . $dir_upload_album);

	if (!is_string($abs_upload_album_path))
	{
		rig_html_error("Missing Upload Albums Directory",
					   "Can't get absolute path for the upload_album directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_upload_album<br>" ,
					   $dir_abs_album . $dir_upload_album);
	}

	// --- rig_thumbnail application ---

	global $pref_preview_exec, $dir_abs_install, $abs_preview_exec;
	$abs_preview_exec = realpath($dir_abs_install . $pref_preview_exec);

	if (!is_string($abs_preview_exec))
	{
		rig_html_error("Missing rig_thumbnail application",
					   "Can't get absolute path for rig_thumbnail application. " .
					   "<br>Check file has actually been compiled, or check permissions.<p>" .
					   "<b>Installation directory:</b> $dir_abs_install<br>" .
					   "<b>Path of exectable:</b> $pref_preview_exec<br>" ,
					   $dir_abs_install . $pref_preview_exec);
	}
}


//********************************
function rig_clear_album_options()
//********************************
// Currently clears:
//	list_hide				- array of filename
//	list_album_icon			- array of icon info { a:album(relative) , f:file, s:size }
//	list_description		- array of [filename] => description (text and/or html) -- RM 20030713
{
	global $list_hide;
	global $list_album_icon;
	global $list_description;

	// DEBUG
	// echo "<h1>Clear options</h1>";

	rig_unset_global('list_hide');			// RM 20040204 fix see rig_unset_global description
	rig_unset_global('list_album_icon');	// RM 20040204 fix see rig_unset_global description
	rig_unset_global('list_description');	// RM 20040204 fix see rig_unset_global description
}


//*************************************
function rig_read_album_options($album)
//*************************************
{
	// first clear current options
	rig_clear_album_options();

/* RM 20030814 -- disabled, not fully implemented and buggy
	// if XML options are available, just read them
	if (rig_xml_read_options($album))
		return TRUE;
*/
	
	// then grab new ones
	global $abs_image_cache_path;	// old location for options was with previews
	global $abs_option_path;		// new options have their own base directory (may be shared with previews anyway)

	// make sure the directory exists
	// don't output an error message, the create function does it for us
	if (!rig_create_option_dir($album))
		return FALSE;

	// RM 20030121 moving options to option's dir -- amazing design isn't it?
	// first try to get options at the new location
	$abs_options = $abs_option_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_OPTIONS_TXT);

	if (!rig_is_file($abs_options))
	{
		// if that fails, try the old location
		$abs_options = $abs_image_cache_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_OPTIONS_TXT);
	
		// silently abort if the file does not exist
		// RM 20030919 bug fix: lack of existing option file is NOT a failure. Nothing to read is OK.
		if (!rig_is_file($abs_options))
			return TRUE;
	}

	// DEBUG
	// global $_debug_;
	// if ($_debug_) echo "<p>Reading abs_options '$abs_options'<br>";

	$file = @fopen($abs_options, "rt");

	if (!$file)
		return rig_html_error("Read Album Options", "Failed to read from file", $abs_options, $php_errormsg);

	$var_name = "";
	$local = array();

	while(!feof($file))
	{
		$line = fgets($file, 1023);

		if (!is_string($line) || $line == FALSE || $line[0] == '#')
			continue;
		
		if (substr($line, -1) == "\n")
			$line = substr($line, 0, -1);
		if (substr($line, -1) == "\r")
			$line = substr($line, 0, -1);

		if ($line[0] == ':')
		{
			if ($var_name)
			{
				global $$var_name;
				$$var_name = array_merge($$var_name, $local);
				$local = array();

				// DEBUG
				// echo "<br>DUMP array; '$var_name' -- ";
				// var_dump($$var_name);
			}

			$var_name = substr($line, 1);
			
			if ($var_name != "")
			{
				global $$var_name;
				$$var_name = array();
			}

			// DEBUG
			// echo "<br>NEW array; '$var_name'";
		}
		else if ($line)
		{
			$key = -1;
			$value = "";
			$c = substr($line, 0, 1);
			// DEBUG
			// if ($_debug_) echo "<br>Read line; '$line'";
			if ($c == '[')
			{
				// DEBUG
				// if ($_debug_) echo "<br>----- format is [key]value";

				// format is "[key]value"
				if (ereg("^\[(.*)\](.*)", $line, $reg) && is_string($reg[1]))
				{
					$key   = $reg[1];
					// the reg-exp will return false if nothing can be matched for the second part
					if (!($reg[2] === FALSE))
						$value = $reg[2];
				}
			}
			else if ($c == '_')
			{
				// DEBUG
				// if ($_debug_) echo "<br>----- format is _value";
		
				// format is "_value"
				$line = substr($line, 1);		// RM 20030215 bug fix (..., 1, -1) => (..., 1);
			}

			// DEBUG
			// if ($_debug_) echo "<br>----- key = '$key'";
			// if ($_debug_) echo "<br>----- value = '$value'";
			// if ($_debug_) echo "<br>----- line = '$line'";

			if ($key == -1)
				$local[] = $line;
			else
				$local[$key] = $value;

			// DEBUG
			// if ($_debug_) { echo "<p>local: "; var_dump($local); }
		}
	}

	// DEBUG
	// if ($_debug_) global $list_hide;
	// if ($_debug_) global $list_album_icon;
	// { echo "<p>Reading list_hide: "; var_dump($list_hide);}
	// { echo "<p>Reading list_album_icon: "; var_dump($list_album_icon);}

	fclose($file);		// RM 20020713 fix
	return TRUE;
}


//*******************************************************
function rig_write_album_options($album, $silent = FALSE)
//*******************************************************
// Currently writes:
// - list_hide				- array of filename
// - list_album_icon		- array of icon info { a:album(relative) , f:file, s:size }
// RM 20030121 moving options to option's dir -- amazin design isn't it?
// RM 20030121 always writing header for the array name even if the array is empty or missing
// RM 20030121 not ready to move to XML yet (DomXml is only in PHP 4.2.1+ experimental yet)
{
	global $list_hide;
	global $list_album_icon;
	global $rig_version;
	global $abs_option_path;

	// DEBUG
	// echo "<p> rig_write_album_options( $album, $silent )\n";
	// echo "<br>list_album_icon = \n"; var_dump($list_album_icon);

	// make sure the directory exists
	// don't output an error message, the create function does it for us
	if (!rig_create_option_dir($album))
		return FALSE;

	$abs_options = $abs_option_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_OPTIONS_TXT);

	// DEBUG
	// echo "<p> abs_options = $abs_options\n";

	// make sure the directory exists

	$file = fopen($abs_options, "wb");

	if (!$file)
	{
		return rig_html_error("Write Album Options",
							  "Failed to write to file",
							  $abs_options,
							  $php_errormsg);
	}

	if (!$silent)
		echo "<p>Write album <b>'$album'</b> options - file: <b>$file</b>\n";

	// ------

	fputs($file, "# Album options - RIG $rig_version\n");
	fputs($file, "# Format: :var_name/val/val.../: to end\n");
	fputs($file, "# Values: one entry per line, either _String\\n or [Key]String\\n\n");

	// ------

	// DEBUG
	// echo "<p> list_hide = \n"; var_dump($list_hide);

	fputs($file, ":list_hide\n");
	if (is_array($list_hide))
	{
		if (!$silent)
			echo "<br>Write album options - list_hide: " . count($list_hide) . " items\n";

		foreach($list_hide as $str)
			fputs($file, '_' . $str . "\n");
	}

	// ------

	// DEBUG
	// echo "<p> list_album_icon = \n"; var_dump($list_album_icon);

	fputs($file, ":list_album_icon\n");
	if (is_array($list_album_icon))
	{
		if (!$silent)
			echo "<br>Write album options - list_album_icon: " . count($list_album_icon) . " items\n";

		foreach($list_album_icon as $key => $str)
			fputs($file, '[' . $key . ']' . $str . "\n");
	}

	fputs($file, ":\n");
	fclose($file);

	return TRUE;
}


//*********************************
function rig_get_album_date($album)
//*********************************
// RM 20030719 v0.3.6.5 using strftime
{
	global $abs_album_path;
	global $html_album_date;	// RM 20030719

	$abs_dir = $abs_album_path . rig_prep_sep($album);

	// read the timestamp on the file "." in the directory (aka the directory itself)
	$t = filemtime(rig_post_sep($abs_dir) . ".");
	$t = strftime($html_album_date, $t);
	
	// RM 20030817 capitalize first word (month name in French or Spanish)
	return ucfirst($t);
}


//******************************************
function rig_read_album_descriptions($album)
//******************************************
// Reloads the content of $list_description
//	list_description		- array of [filename] => description (text and/or html) -- RM 20030713
{
	// Is the feature enabled? [RM 20030821]
	global $pref_enable_descriptions;
	if (!$pref_enable_descriptions)
		return TRUE;
	
	
	global $abs_album_path;		// descriptions
	global $abs_option_path;	// new options have their own base directory (may be shared with previews anyway)


	// first clear current options
	global $list_description;
	rig_unset_global('list_description');	// RM 20040204 fix see rig_unset_global description

		
	// then grab new ones
	//
	// descriptions are stored either with the album itself or in the options directory
	// the options dir's version superseedes the one from the album, if any


	// first read the main dir files

	$abs_dir = $abs_album_path . rig_prep_sep($album);


	if (!rig_parse_description_file($abs_dir . rig_prep_sep(DESCRIPTION_TXT)))
		 rig_parse_description_file($abs_dir . rig_prep_sep(FILEINFODIZ_TXT));


	// then override with the options directory


	// make sure the directory exists
	// don't output an error message, the create function does it for us
	if (!rig_create_option_dir($album))
		return FALSE;


	$abs_options = $abs_option_path . rig_prep_sep($album);

	if (!rig_parse_description_file($abs_options . rig_prep_sep(DESCRIPTION_TXT)))
		 rig_parse_description_file($abs_options . rig_prep_sep(FILEINFODIZ_TXT));

	return TRUE;
}



//********************************************
function rig_parse_description_file($abs_path)
//********************************************
// This reads in the description file for an album list or image list.
// This function merges the file into the existing array list_description with format:
//	list_description		- array of [filename] => description (text and/or html) -- RM 20030713
// Format:
/*
	# Description file for rig
	# Accepted names are "descript.ion" or "file_info.diz"
	# Lines starting with # are ignored. So are empty lines.
	# Line format is:
	#   <img or album name>[ \t]+<description>\n
	#   [ \t]+<continuation of previous description>\n
*/
{
	global $list_description;

	$file = @fopen($abs_path, "rt");

	if (!$file)
		return FALSE;

	$continuing = FALSE;
	$name = "";

	while(!feof($file))
	{
		// read till we get a full line
		$line = "";
		
		$same_line = TRUE;
		$is_comment = FALSE;

		while($same_line && !feof($file))
		{
			$temp = fgets($file, 1023);

			if (is_string($temp))
			{
				if ($line == "")
					$is_comment = ($temp[0] == '#');
	
				if (substr($temp, -1) == "\n")
				{
					$temp = substr($temp, 0, -1);
					$same_line = FALSE;
				}
		
				if (substr($temp, -1) == "\r")
				{
					$temp = substr($temp, 0, -1);
					$same_line = FALSE;
				}
	
				// store if not a comment line
				if (!$is_comment)
					$line .= $temp;
			}
		}

		// need a valid line
		if (!$line || $line == "" || $line == FALSE || $is_comment)
			continue;

		// if starts by a whitespace, it's the continuation of the previous line
		$nb_ws = strspn($line, " \t");
		if ($nb_ws > 0 && $name != "")
		{
			// skip whitespace
			$line = substr($line, $nb_ws);

			// note that if the previous line nor the new one end or start with a
			// whitespace, one must be added.
			$t = $list_description[$name];
			if ($t != "")
			{
				$t = $t[strlen($t)-1];
				if ($t != " " && $t != "\t")
					$line = " " . $line;
			}

			$list_description[$name] .= $line;
		}
		else
		{
			// this is a new entry, get the name and the text
			// format is "(name)[ \t]+(text)"
			if (ereg("^([^ \t]+)[ \t]+(.*)", $line, $reg) && is_string($reg[1]))
			{
				$name = $reg[1];
				$list_description[$name] = $reg[2];
			}
		}

	} // end while feof

	fclose($file);

	// DEBUG
	// var_dump($album);
	// var_dump($list_description);
	

	return TRUE;
}


//-----------------------------------------------------------------------

//****************************
function rig_nocache_headers()
//****************************
// used by the admin pages to prevent caching
// RM see HTTP doc to determine if html vs. img can be cached selectively (IMG tag?)
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");				// Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");	// always modified
	header("Cache-Control: no-cache, must-revalidate");				// HTTP/1.1
	header("Pragma: no-cache");										// HTTP/1.0
}


//***************************************************
function rig_set_cookie_val($name, $val, $set = TRUE)
//***************************************************
// $set: TRUE to set cookie, FALSE to delete cookie
{
	global $dir_abs_album;
    global $pref_cookie_host;

	$delay = 3600 * 24 * 365;

    $host = $pref_cookie_host;
	if (!$host)
		$host = $_SERVER['HTTP_HOST'];

    $path = $dir_abs_album;

	$time = ($set ? time() + $delay : time() - $delay);
	// $time = gmstrftime("%A, %d-%b-%Y %H:%M:%S", ($set ? time() + $delay : time() - $delay));

	$host = "";
	$path = "";

	// debug
    // if (rig_get($_GET,'admin') && rig_get($_GET,'_debug_'))
	// echo "Set Cookie: name='$name' -- val='$val' -- date='$time' -- path='$path' -- host='$host'<br>\n";

	setcookie($name, $val, $time, $path, $host);

	// Update the global cookie array
	if ($set)
		$_COOKIE[$name] = $val;
	else
		$_COOKIE[$name] = NULL;		
}


//***************************
function rig_handle_cookies()
//***************************
// Some literature:
// http://developer.netscape.com:80/docs/manuals/js/client/jsguide/cookies.htm
{
	global $current_language;
	global $current_theme;

	global $login_error;

	global $pref_image_size;
	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;


	// Description of variables:
	//
	//	GET/POSTDATA name	COOKIE name
	// lang					rig_lang
	// theme				rig_theme
	// img_size				rig_img_size
	// force_login			n/a
	// user					rig_user
	// passwd				rig_passwd
	// admusr				rig_adm_user
	// admpwd				rig_adm_passwd

	global $lang,		$rig_lang;
	global $theme,		$rig_theme;
	global $img_size,	$rig_img_size;
	global $user,		$rig_user;
	global $passwd,		$rig_passwd;
	global $admusr,		$rig_adm_user;
	global $admpwd,		$rig_adm_passwd;
	global $force_login;

	// Vars that are transmitted thru the GET url
	$lang			= rig_get($_GET,'lang'			);
	$theme			= rig_get($_GET,'theme'			);
	$force_login	= rig_get($_GET,'force_login'	);
	$keep			= rig_get($_GET,'keep'			);

	// Vars that are transmitted thru a GET url or a form POST
	$img_size		= rig_get($_GET,'img_size', rig_get($_POST,'img_size'));

	$user			= rig_get($_GET,'user',   rig_get($_POST,'user'  ));
	$passwd			= rig_get($_GET,'passwd', rig_get($_POST,'passwd'));
	$admusr			= rig_get($_GET,'admusr', rig_get($_POST,'admusr'));
	$admpwd			= rig_get($_GET,'admpwd', rig_get($_POST,'admpwd'));

	// Cookie vars
	$rig_lang		= rig_get($_COOKIE,'rig_lang'	);
	$rig_theme		= rig_get($_COOKIE,'rig_theme'	);
	$rig_img_size	= rig_get($_COOKIE,'rig_img_size');
	$rig_user		= rig_get($_COOKIE,'rig_user'	);
	$rig_passwd		= rig_get($_COOKIE,'rig_passwd'	);
	$rig_adm_user	= rig_get($_COOKIE,'rig_adm_user');
	$rig_adm_passwd	= rig_get($_COOKIE,'rig_adm_passwd');

	if ($lang)
	{
		rig_set_cookie_val("rig_lang", $lang);
		$current_language = $lang;
		$rig_lang = $lang;
	}
	else
	{
		$current_language = $rig_lang;
	}

	if ($theme)
	{
		rig_set_cookie_val("rig_theme", $theme);
		$current_theme = $theme;
		$rig_theme = $theme;
	}
	else
	{
		$current_theme = $rig_theme;
	}

	if ($img_size)
	{
		// an img_size of '0' or less means to use the original image size
		rig_set_cookie_val("rig_img_size", (int)$img_size);
		$rig_img_size = (int)$img_size;
	}
	else if (!$rig_img_size)
	{
		$rig_img_size = $pref_image_size;
	}

	// colons are NOT accepted in username or password from GET/POSTDATA
	// but they may appear in the cookie (to make sure encrypted cookies values
	// are not used to feed a faked GET/POSTDATA)
	if (is_string($user  )) $user   = str_replace(':', '', $user  );
	if (is_string($passwd)) $passwd = str_replace(':', '', $passwd);
	if (is_string($admusr)) $admusr = str_replace(':', '', $admusr);
	if (is_string($admpwd)) $admpwd = str_replace(':', '', $admpwd);

	if (!$force_login && $user)
	{
		// first erase existing cookie (set time to past value)
		rig_set_cookie_val("rig_user"  , $rig_user,	  false);
		rig_set_cookie_val("rig_passwd", $rig_passwd, false);

		// this info will be validated and modified by the test function
		$rig_user   = $user;
		$rig_passwd = $passwd;

		if (rig_test_user_pwd(FALSE, $rig_user, $rig_passwd, $login_error))
		{
			// set the expiration date to +1 year if we want to keep it,
			// or 0 if it's only for this session
			if ($keep == 'on')
				$t = $time;
			else
				$t = 0;

			rig_set_cookie_val("rig_user"  , $rig_user);
			rig_set_cookie_val("rig_passwd", $rig_passwd);
		}
	}

	if (!$force_login && $admusr && isset($admpwd))
	{
		// first erase existing cookie (set time to past value)
		rig_set_cookie_val("rig_adm_user"  , $rig_adm_user  , false);
		rig_set_cookie_val("rig_adm_passwd", $rig_adm_passwd, false);

		// this info will be validated and modified by the test function
		$rig_adm_user   = $admusr;
		$rig_adm_passwd = $admpwd;

		if (rig_test_user_pwd(TRUE, $rig_adm_user, $rig_adm_passwd, $login_error))
		{
			// set the expiration date to +1 year if we want to keep it,
			// or 0 if it's only for this session
			if ($keep == 'on')
				$t = $time;
			else
				$t = 0;

			rig_set_cookie_val("rig_adm_user"  , $rig_adm_user);
			rig_set_cookie_val("rig_adm_passwd", $rig_adm_passwd);
		}
	}
}


//******************************************
function rig_remove_login_cookies($is_admin)
//******************************************
{
	global $rig_user;
	global $rig_passwd;
	global $rig_adm_user;
	global $rig_adm_passwd;

	// debug
    // echo "remove login cookies $is_admin<br>\n";

	if ($is_admin)
	{
		rig_set_cookie_val("rig_adm_user"  , $rig_adm_user  , false);
		rig_set_cookie_val("rig_adm_passwd", $rig_adm_passwd, false);
	}
	else
	{
		rig_set_cookie_val("rig_user"  , $rig_user  , false);
		rig_set_cookie_val("rig_passwd", $rig_passwd, false);
	}
}


//******************
function rig_setup()
//*****************
{
	// List of globals defined for the album page by prepare_album():
	// $current_album		- string
	// $display_exec_date	- string
	// $display_softname	- string, constant

	global $pref_umask;
	global $pref_use_db;
	global $pref_use_db_id;
	global $pref_use_id_in_url;
	global $pref_internal_file_types;		// RM 20030807
	global $pref_extra_file_types;			// RM 20030928
	global $rig_file_types;					// RM 20030928
	global $current_language;
	global $display_exec_date;
	global $display_softname;
	global $html_footer_date;
	global $html_desc_lang;
	global $lang_locale;

	// -- setup umask

	if ($pref_umask)
		umask($pref_umask);


	// -- setup locale

	if (isset($lang_locale))
	{
		$l = FALSE;
		if (is_string($lang_locale))
		{
			$l = setlocale(LC_TIME, $lang_locale);
		}
		else if (is_array($lang_locale))
		{
			// setlocale does not accept array before php 4.3... simulate
			foreach($lang_locale as $name)
			{
				$l = setlocale(LC_TIME, $name);
				if (is_string($l) && $l != '')
					break;
			}
		}

		if ($l == FALSE)
		{
			rig_html_error("Invalid Locale!",
			               "The specified locale is not recognized by your system!",
	            		   is_string($lang_locale) ? $lang_locale : implode(', ', $lang_locale) );
		}
	}


	// -- setup date & soft name
	$display_exec_date = strftime($html_footer_date);	// RM 20030719 using strftime
	$display_softname  = "<a href=\"" . RIG_SOFT_URL . "\">" . RIG_SOFT_NAME . "</a>";


	// -- keep track of php errors with $php_errormsg (cf html_error)
	ini_set("track_errors", "1");


	// -- validate prefs use flags --
	// RM 20021021 invalidate DB support in rig 0.6.2
	$pref_use_db = FALSE;
	$pref_use_db_id = FALSE;
	$pref_use_id_in_url = FALSE;


	// -- setup filetypes --
	
	// If global $pref_internal_file_types is not defined, request file type support
	// information from the rig_thumbnail.exe application. [RM 20030807]
	
	if (!is_array($pref_internal_file_types) || count($pref_internal_file_types) == 0)
	{
		$pref_internal_file_types = rig_runtime_filetype_support();

		if (!is_array($pref_internal_file_types) || count($pref_internal_file_types) == 0)
			rig_html_error("Runtime File Type Array Error",
						   "Runtime File Type Array is not valid<p>" .
						   "<b>Is array?</b> " . ($pref_internal_file_types == NULL || !is_array($pref_internal_file_types) ? "No" : "Yes") . "<br>" .
						   "<b>Is empty?</b> " . (is_array($pref_internal_file_types) && count($pref_internal_file_types) >= 0 ? "No" : "Yes"),
						   $pref_internal_file_types);
	}

	// rig_file_types combines pref_internal_file_types and pref_extra_file_types
	// [RM 20030928]
	
	
	if (is_array($pref_extra_file_types) && count($pref_extra_file_types) > 0)
		$rig_file_types = $pref_internal_file_types + $pref_extra_file_types;
	else
		$rig_file_types = $pref_internal_file_types;

}


//*********************
function rig_setup_db()
//*********************
{
	// RM 20021021 this function does nothing in rig 0.6.2
}



//*************************
function rig_terminate_db()
//*************************
{
	// RM 20021021 this function does nothing in rig 0.6.2
}


//-----------------------------------------------------------------------



//***********************************
function rig_get_parent_album($album)
//***********************************
// Returns the parent album path, or '' if already at the top.
{
	if ($album)
	{
		$items = explode(SEP, $album);
	
		if (count($items) > 0)
		{
			// remove the last item
			unset($items[count($items)-1]);
			
			// glue it back
			return implode(SEP, $items);
		}
	}

	return '';
}


//***********************************************
function rig_check_album_access($abs_dir, $album)
//***********************************************
{
	global $pref_enable_access_hidden_albums;

	// If pref_enable_access_hidden_albums is FALSE and the album
	// exists yet it is hidden, album is not accessible.

	// If pref_enable_access_hidden_albums is TRUE and the album
	// exists yet it is hidden, allow access to it.

	$can_access = rig_is_dir($abs_dir);

	if (    $album != ''
		&&  $can_access
		&& !$pref_enable_access_hidden_albums)
	{
		// To know if the album is visible, we must load its parents
		// options. We do that only if access to hidden albums is
		// disabled and this is not the top album (which can never
		// be hidden, by design).
		// Now maybe the current album is visible but one of its
		// parents is hidden... in which case this album is to be
		// considered hidden too. So we need to explore all the
		// way up to make sure it's all valid.
		
		$curr = $album;
		while($curr && $can_access)
		{
			$parent = rig_get_parent_album($curr);

			$can_access = rig_read_album_options($parent);

			if ($can_access)
				$can_access = rig_is_visible(-1, $curr);
				
			$curr = $parent;
		}
	}

	return $can_access;
}


//********************************************************************************
function rig_follow_album_symlink($abs_dir, &$current_album, &$current_real_album)
//********************************************************************************
// Returns FALSE if access to the album should be denied
// Returns TRUE and update current_album and current_real_album directly if it is
// a symlink that must be followed.
// Returns TRUE and does not change current_album and current_real_album if it is
// a symlink that must NOT be followed.
{
	global $pref_follow_album_symlinks;
	global $abs_album_path;


	if (   $pref_follow_album_symlinks
		&& $current_album
		&& rig_is_dir($abs_dir)
		&& is_link($abs_dir))
	{
		// ok so abs_dir is a directory and it is a symlink
		// get the real directory it points to:

		$rp = realpath($abs_dir);

		// now $abs_album_path is the root of the album and
		// it is a real path too. The symlink points onto
		// the same album if $abs_album_path is exactly
		// present at the beginning of $rp
		
		if (strncmp($rp, $abs_album_path, strlen($abs_album_path)) == 0)
		{
			// if so, the rest of the rp string gives the linked-to
			// album, and there should be a directory separator too
			// that we can ignore
			
			$s_album = substr($rp, strlen($abs_album_path));

			// check the string contains at least the directory separator
			// and some more
			
			if (strlen($s_album) > 1 && ($s_album[0] == SEP || $s_album[0] == SEP2))
			{
				// strip the directory sep
				$s_album = substr($s_album, 1);
				
				// we got ourselves our candidate new album...
				// check this album can be accessed

				$can_access = rig_check_album_access($rp, $s_album);
		
				if (!$can_access)
				{
					return FALSE;
				}
				else
				{
					// access allowed, remap physical access variables

					$current_real_album = $s_album;
				}
			}
		}
	}

	return TRUE;

} // follow symlink


//*****************************************************************
function rig_prepare_album($album, $apage=-1, $ipage=-1, $title="")
//******************************************************************
{
	// List of globals defined for the album page by prepare_album():
	// $current_album		- string
	// $display_title		- string
	// $display_album_title	- string

	global $abs_album_path;
	global $current_album;
	global $current_real_album;					// RM 20030907
	global $current_album_page;					// RM 20030908
	global $current_image_page;					// RM 20030908
	global $pref_use_db_id;
	global $display_title;
	global $display_album_title;
	global $html_album_title;
	global $html_image_title;
	global $html_none;
	global $pref_album_ignore_list;				// RM 20030813 - v0.6.3.5
	global $pref_enable_album_pagination;		// RM 20030908 - v0.6.4.3

	$current_album		= FALSE;
	$current_real_album = FALSE;
	$can_access			= FALSE;
	$current_album_page	= -1;
	$current_image_page	= -1;
	$abs_dir			= '';					// RM 20040601 - v0.6.4.5 - fix: declare vars
	

	// first try the index argument
	// RM 20021021 not for rig 0.6.2

	// second try the named argument
	if (!$current_album && isset($album))
	{	
		$current_album = rig_decode_argument($album);
	}

	// check the ignore lists and invalidate names if necessary
	if ($current_album && rig_check_ignore_list($current_album, $pref_album_ignore_list))
	{
		$album = '';
		$current_album = '';
	}

	// does the album really exist?
	if ($current_album)
	{
		$abs_dir = $abs_album_path . rig_prep_sep($current_album);

		$can_access = rig_check_album_access($abs_dir, $current_album);

		if (!$can_access)
		{
			// access denied, unset variables
			$current_album		= '';
			$current_real_album	= '';
			$abs_dir			= '';
		}
		else
		{
			// access allowed
			
			$current_real_album = $current_album;
		}
	}
	
	
	// -- follow album symlinks

	if (!rig_follow_album_symlink($abs_dir, $current_album, $current_real_album))
	{
		// if the function returns false, access to the album should be denied
		// the function modifies current_album and current_real_album directly

		// access denied, unset variables
		$current_album		= '';
		$current_real_album	= '';
		$abs_dir			= '';
	}

	// -- setup page indexes
	
	if ($pref_enable_album_pagination)
	{
		// the list of images or sub-albums is unknown yet
		// so the values are just accepted as-is and will be
		// adjusted later when the count of image/albums is known
		// cf rig_has_album() and rig_has_images().

		if ($apage >= 0)
			$current_album_page = $apage;

		if ($ipage >= 0)
			$current_image_page = $ipage;
	}
	

	// -- setup title of album
	
	if (!$title)
		$title = $html_album_title;

	if ($current_album)
	{
		$items = explode(SEP, $current_album);
		$pretty = rig_pretty_name($items[count($items)-1], FALSE, TRUE);
		$display_title = "$title - " . $pretty;
		$display_album_title = "$html_album_title - " . $pretty;
	}
	else
	{
		$display_title = "$title - $html_none";
		$display_album_title = "$html_album_title - $html_none";
	}

	// Read this album's options right now
	rig_read_album_options($current_real_album);
}


//***************************************
function rig_build_recursive_list($album)
//***************************************
// returns a list with pairs { a:$album, f:$file }
{
	global $abs_album_path;

	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;


	// make sure we have the options for this album
	rig_read_album_options($album);

	// get the absolute album path
	$abs_dir = $abs_album_path . rig_prep_sep($album);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$result = array();
	$handle = @opendir($abs_dir);
	if ($handle)
	{
		rig_create_preview_dir($album);

		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..' && rig_is_visible(-1, $album, $file))
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (rig_is_dir($abs_file))
				{
					// it is a directory
					if (!rig_check_ignore_list($file, $pref_album_ignore_list))
					{
						$name = rig_post_sep($album) . $file;
						$res = rig_build_recursive_list($name);
						if (is_array($res) && count($res)>0)
							$result = array_merge($result, $res);
	
						// restore the options for this album
						// (the local array will have been modified by the recursive call)
						rig_read_album_options($album);
					}
				}
				else
				{
					// it is a file
					if (!rig_check_ignore_list($file, $pref_image_ignore_list))
					{
						if (rig_valid_ext($file))
						{
							// create entry and add it
							$entry = array('a' => $album, 'f' => $file);
							$result[] = $entry;
					    }
					}
				}
			}
		}
		closedir($handle);
	}

	return $result;
}


//*********************************
function rig_get_album_info($album)
//*********************************
// returns a tuple { a: number of albums, i: number of images }
{
	global $abs_album_path;
	global $current_album;
	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;

	$album_count = 0;
	$image_count = 0;

	// make sure we have the options for this album
	if ($album != $current_album)
		rig_read_album_options($album);

	// get the absolute album path
	$abs_dir = $abs_album_path . rig_prep_sep($album);

	// get all files and dirs, don't recurse
	$result = array();
	$handle = @opendir($abs_dir);
	if ($handle)
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..' && rig_is_visible(-1, $album, $file))
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (rig_is_dir($abs_file))
				{
					// it is a directory
					if (!rig_check_ignore_list($file, $pref_album_ignore_list))
						$album_count++;
				}
				else
				{
					// it is a file
					if (!rig_check_ignore_list($file, $pref_image_ignore_list))
						if (rig_valid_ext($file))
							$image_count++;
				}
			}
		}
		closedir($handle);
	}

	// restore options
	if ($album != $current_album)
		rig_read_album_options($current_real_album);

	return array('a' => $album_count, 'i' => $image_count);
}



//**********************************
function rig_cmp_pretty_name($a, $b)
//**********************************
{
	// $a = rig_pretty_name($a);
	// $b = rig_pretty_name($b);
	return strcasecmp($a, $b);
}


//*********************************************
function rig_load_album_list($show_all = FALSE)
//*********************************************
{
	// This function populates the folowing 
	// $list_albums			- array of string
	// $list_images			- array of filename

	global $list_albums;
	global $list_images;

	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;
	
	global $current_album;
	global $current_real_album;			// RM 20030907
	global $abs_album_path;

	// DEBUG
	// echo "<br>Current Album = \"$current_album\" -- Real Album = \"$current_real_album\"";

	$abs_dir = $abs_album_path . rig_prep_sep($current_real_album);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$list_albums = array(); // RM 20040204 fix: reset album list
	$list_images = array();
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		// RM 20020713 better error codes
		rig_html_error("Load Album List",
					   "Can't open directory, probably does not exist",
					   $abs_dir,
					   $php_errormsg);
	}
	else
	{
		rig_create_preview_dir($current_real_album);

		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (rig_is_dir($abs_file))
				{
					// it is a directory
					if (!rig_check_ignore_list($file, $pref_album_ignore_list))
					{
						if ($show_all || rig_is_visible(-1, $current_album, $file))
						{
							$list_albums[] = $file;
		
							// DEBUG
							// echo "<br>Album: $file";
						}
					}
				}
				else
				{
					// it is a file
					if (!rig_check_ignore_list($file, $pref_image_ignore_list))
					{
						if (rig_valid_ext($file) && ($show_all || rig_is_visible(-1, -1, $file)))
						{
					    	$list_images[] = $file;
		
							// DEBUG
							// echo "<br>Image: $file";
					    }
					}
				}
			}
		}
		closedir($handle);

		if (count($list_albums))
			usort($list_albums, "rig_cmp_pretty_name");
	
		if (count($list_images))
			usort($list_images, "rig_cmp_pretty_name");
	}
	
	rig_read_album_descriptions($current_real_album);
}


//*****************************************************
function rig_max_album_page($nb_col = -1, $nb_row = -1)
//*****************************************************
// This method computes how many pages will be used to display
// the current album list.
// It also enables/disable pagination as required.
//
// This function computes $max_album_page 
// It also adjusts $current_album_page
// And it returns the number of thumbnails per page, or 0
{
	global $max_album_page;
	global $current_album_page;
	global $list_albums_count;
	global $pref_album_nb_col;
	global $pref_album_nb_row;

	$nb = 0;
	$max_album_page = 0;

	// adjust the number of pages needed for the album list
	
	if ($list_albums_count < 1)
	{
		$current_album_page = 0;
	}
	else if ($current_album_page >= 0)
	{
		if ($nb_col == -1)
			$nb_col = $pref_album_nb_col;

		if ($nb_row == -1)
			$nb_row = $pref_album_nb_row;

		if ($nb_col > 0 && $nb_row > 0)
		{
			$nb = ($nb_col * $nb_row);
			$max_album_page = (int)ceil($list_albums_count / $nb);

			// don't walk past the last page
			if ($current_album_page > $max_album_page)
				$current_album_page = $max_album_page;
			
			// if more than one page, enable pagination
			// RM 20040708 fix: enable pagination only if more than one page
			if ($current_album_page == 0 && $max_album_page > 1)
				$current_album_page = 1;
		}
		else
		{
			// deactive pagination if nb_col/row invalid
			$current_album_page = -1;
		}
	}

	return $nb;
}


//*****************************************************
function rig_max_image_page($nb_col = -1, $nb_row = -1)
//*****************************************************
// This method computes how many pages will be used to display
// the current image list.
// It also enables/disable pagination as required.
{
	global $max_image_page;
	global $current_image_page;
	global $list_images_count;
	global $pref_image_nb_col;
	global $pref_image_nb_row;

	$max_image_page = 0;

	// adjust the number of pages needed for the image list
	
	if ($list_images_count < 1)
	{
		$current_image_page = 0;
	}
	else if ($current_image_page >= 0)
	{
		if ($nb_col == -1)
			$nb_col = $pref_image_nb_col;

		if ($nb_row == -1)
			$nb_row = $pref_image_nb_row;

		if ($nb_col > 0 && $nb_row > 0)
		{
			$nb = ($nb_col * $nb_row);
			$max_image_page = (int)ceil($list_images_count / $nb);

			// don't walk past the last page
			if ($current_image_page > $max_image_page)
				$current_image_page = $max_image_page;
			
			// if more than one page, enable pagination
			// RM 20040708 fix: enable pagination only if more than one page
			if ($current_image_page == 0 && $max_image_page > 1)
				$current_image_page = 1;
		}
		else
		{
			// deactive pagination if nb_col/row invalid
			$current_image_page = -1;
		}
	}

	return $nb;
}



//*********************************************
function rig_has_albums($exclude_hidden = TRUE)
//*********************************************
// Indicates how many albums there are.
// If $exclude_hidden is TRUE, which is the default, only indicates
// how many visible albums there are. If false, also count hidden albums.
{
	global $list_albums;
	global $list_albums_count;

	// Is there any albums at all?
	if (count($list_albums) >= 1)
	{
		if ($exclude_hidden)
		{
			// There are albums. But some are hidden.
			// Find how many are visible. Do this only once.
			if (!isset($list_albums_count))
			{
				$list_albums_count = 0;
				foreach($list_albums as $dir)
				{
					// count visible albums
					if (rig_is_visible(-1, $dir))
						$list_albums_count++;
				}
			}
		}
		else
		{
			// count everything
			$list_albums_count = count($list_albums);
		}

		return ($list_albums_count > 0);
	}

	// None at all, so that's a positive false
	$list_albums_count = 0;
	return false;
}


//*********************************************
function rig_has_images($exclude_hidden = TRUE)
//*********************************************
// Indicates how many images there are.
// If $exclude_hidden is TRUE, which is the default, only indicates
// how many visible images there are. If false, also count hidden images.
{
	global $list_images;
	global $list_images_count;

	// Is there any images at all?
	if (count($list_images) >= 1)
	{
		if ($exclude_hidden)
		{
			// There are images. But some are hidden.
			// Find how many are visible. Do this only once.
			if (!isset($list_images_count))
			{
				$list_images_count = 0;

				foreach($list_images as $index => $file)
				{
					// count visible images
					if (rig_is_visible(-1, -1, $file))
						$list_images_count++;
				}
			}
		}
		else
		{
			// count everything
			$list_images_count = count($list_images);
		}

		return ($list_images_count > 0);
	}

	// by default count everything
	$list_images_count = $list_images;

	// None at all, so that's a positive false
	return false;
}


//*********************************************************
function rig_is_visible($id = -1, $album = -1, $image = -1)
//*********************************************************
// Input:
// - if id is given, use solely that
// - if both album and image are given, get image id from composited path
//   (compare with current_album/image)
// - if album is given but not image, get album id (compare with current_album)
// - if image is given but not album, get image id in current_album
{
	global $current_album;
	global $current_image;
	global $pref_use_db_id;
	global $list_hide;


	// DEBUG
	// echo "<p><b>rig_is_visible</b>(id = $id, album = $album, image = $image)<br>";
	// echo "<p><b>list_hide</b>"; var_dump($list_hide);

	// old option mechanism (rig <= 0.6.2)

	if ($image != -1)
		$item = $image;
	else if ($album != -1)
		$item = $album;

	return !$list_hide || !in_array($item, $list_hide, TRUE);
}

//-----------------------------------------------------------------------


//***************************************************
function rig_prepare_image($album, $image, $title="")
//***************************************************
// $page is an integer:
// -1: the pagination must be disabled (even if enabled in the preferences)
//  0: default page must be shown (typically the first one) and there is
//	   no need to generate the page display if there's only one page
// 1..N: display page N, generate the page HTML display, pass back in URLs, etc.
{
	rig_setup();

	// List of globals defined for the album page by prepare_album():
	// $current_image		- string
	// $pretty_image		- string
	// $current_album		- string
	// $current_real_album	- string
	// $current_img_info	- array of {format, width, height}
	// $display_title		- string
	// $display_album_title	- string

	global $current_album;
	global $current_real_album;			// RM 20030907
	global $current_image;
	global $current_type;				// RM 20030713
	global $current_img_info;
	global $pref_use_db_id;
	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;
	global $pref_enable_access_hidden_images;
	global $abs_album_path;
	global $pretty_image;
	global $display_title;
	global $display_album_title;
	global $html_image_title;


	$current_album		= FALSE;
	$current_image		= FALSE;
	$current_real_album	= FALSE;
	$current_type		= '';

	$can_album			= FALSE;
	$can_access_album	= FALSE;

	// try the named argument from the GET query string
	
	if (!$current_image && isset($image))
	{
		$current_album = rig_decode_argument($album);
		$current_image = rig_decode_argument($image);
		
		$current_real_album = $current_album;
	}

	// check the ignore lists and invalidate names if necessary
	if ($current_album && rig_check_ignore_list($current_album, $pref_album_ignore_list))
	{
		$album				= '';
		$current_album		= '';
		$current_real_album = '';
	}

	if ($current_image && rig_check_ignore_list($current_image, $pref_image_ignore_list))		// RM 20030907 fix: was testing current-album name against image-ignore-list
	{
		$image			= '';
		$current_image	= '';
	}


	// -- validate album and follow album symlinks

	if ($current_album)
	{
		$abs_dir = $abs_album_path . rig_prep_sep($current_album);

		$can_access = rig_check_album_access($abs_dir, $current_album);

		if ($can_access)
			$can_access = rig_follow_album_symlink($abs_dir, $current_album, $current_real_album);

		$can_access_album = $can_access;
	}
	

	// does the image really exist?
	// is the image hidden?
	if ($current_image && $can_access)
	{
		$rel_img = $current_real_album  . rig_prep_sep($current_image);
		$abs_img = $abs_album_path      . rig_prep_sep($current_real_album) . rig_prep_sep($current_image);

		// If pref_enable_access_hidden_images is FALSE and the image
		// exists yet it is hidden, redirect to the album.
		// If pref_enable_access_hidden_images is TRUE and the image
		// exists yet it is hidden, allow access to it.

		$can_access = rig_is_file($abs_img);
		if ($can_access && !rig_is_visible(-1, -1, $current_image))
			$can_access = $pref_enable_access_hidden_images;
	}

	if (!$can_access)
	{
		// access denied, unset variables
		// invalidate current image and then redirect to the album

		global $image;
		$image			= '';
		$current_image	= '';

		// if the album is invalid, remove to so that the page
		// be redirected to the album root
		
		if (!$can_access_album)
		{
			$current_album		= '';
			$current_real_album	= '';
		}

		// redirect

		$refresh_url = rig_self_url();
		header("Location: $refresh_url");
		exit;
	}

	$pretty_image  = rig_pretty_name($current_image, FALSE);

	$current_img_info = rig_build_info($current_album, $current_image);

	// -- get image type
	// (that's the part before / in the file's type)
	
	list($current_type, $dummy) = explode("/", rig_get_file_type($current_image), 2);

	// -- setup title of album
	$title = $html_image_title;
	if ($title)
		$title .= " - ";

	$display_title = $title . $pretty_image;

	// RM 20020715 fix: use current_album
	if ($current_album)
	{
		$items = explode(SEP, $current_album);
		// RM 20020711: rig_pretty_name with strip_numbers=FALSE
		$display_album_title = rig_pretty_name($items[count($items)-1], FALSE);
	}

	rig_read_album_options($current_real_album);
}


//*********************************
function rig_get_images_prev_next()
//*********************************
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
	global $current_real_album;		// RM 20030907
	global $current_image;
	global $html_image;
	global $list_images;


	// find the index of the current image in the array
	$key = rig_php_array_search($current_image, $list_images);


	// DEBUG
	// echo "current = $current_image -- array = $list_images -- key = $key";

	if (is_bool($key) && $key == FALSE)
		return rig_html_error("Get Prev/Next Images", "Can't find image in internal list!", $current_image);

	if ($key > 0)
	{
		$file = $list_images[$key-1];

		$pretty = rig_pretty_name($file, FALSE);
		$preview = rig_encode_url_link(rig_build_preview($current_real_album, $file));

		$display_prev_link = rig_self_url($file);
		$display_prev_img = "<img src=\"$preview\" alt=\"$pretty\" title=\"$html_image: $pretty\" border=0>";
	}

	if ($key < count($list_images)-1)
	{
		$file = $list_images[$key+1];

		$pretty = rig_pretty_name($file, FALSE);
		$preview = rig_encode_url_link(rig_build_preview($current_real_album, $file));

		$display_next_link = rig_self_url($file);
		$display_next_img = "<img src=\"$preview\" alt=\"$pretty\" title=\"$html_image: $pretty\" border=0>";
	}
}


//-----------------------------------------------------------------------


//***************************************
function rig_parse_string_data($filename)
//***************************************
// Parses a data file for foreign strings
//
// Format of the file:
// - entries are composed of 2 lines:
//	1- the variable to set (with the $ like in PHP)
//	2- the string value for the variable
// The scanner looks for lines starting with $ or @ and use the first word as the variable name
// If the line was starting with @$, the second word will be the named index in an array
// It then reads the *next* line, whatever it's content being, except the ending linefeed
//
// As a side effect, empty lines or lines starting with // or # will be ignored.
//
// The prefered line separator is the Unix mode, i.e. only LF (/n) as linefeed.
{
	global $dir_abs_src, $abs_upload_src_path;


	// get the installation-relative path of the file
	$file1 = rig_post_sep($dir_abs_src) . $filename;
	$file2 = rig_post_sep($abs_upload_src_path) . $filename;

	if (rig_is_file($file2))
		$filename = $file2;
	else
		$filename = $file1;

	// open the file
	$file = @fopen($filename, "rt");

	// make sure the file exist or display an error to the user	
	if (!$file)
	{
		rig_html_error("Can't read i18n string file",
					   "Failed to read from file",
					   $filename,
					   $php_errormsg);

		// just for the sake of it, present the error again :-p
		rig_check_src_file($filename);
		return FALSE;
	}

	$tok_sep = " \t\n\r";

	// for every line...
	while(!feof($file))
	{
		$line = fgets($file, 1023);

		// if the line is empty, we skip it
		if (!is_string($line) || !$line || $line == FALSE)
			continue;

		// if the line does not start with @$ or $, we skip it
		if ($line[0] != '@' && $line[0] != '$')
			continue;

		$is_array = ($line[0] == '@');
		if ($is_array && $line[1] != '$')
			continue;

		// get the variable name
		$var_name = strtok(substr($line, $is_array ? 2 : 1), $tok_sep);

		if (is_string($var_name))
		{
			// access the global variable
			global $$var_name;

			// if an array, get the array index name
			if ($is_array)
			{
				$index_name = strtok($tok_sep);
				
				if (!is_string($index_name))
					continue;
			}

			// read the actual data line
			$data = fgets($file, 1023);

			if (is_string($data))
			{
				// strip end-of-line
				if (substr($data, -1) == "\n")
					$data = substr($data, 0, -1);
				if (substr($data, -1) == "\r")
					$data = substr($data, 0, -1);

				// DEBUG
				// echo "<br> [$var_name] + [$index_name] = $data";

				// set the value
				if ($is_array)
					$$var_name[$index_name] = $data;
				else
					$$var_name = $data;
			}
		} // if var_name
	} // while !eof

	fclose($file);		// RM 20020713 fix
	return true;
}




//-----------------------------------------------------------------------


//****************************
function rig_modif_date($path)
//****************************
{
	if (is_string($path) && (rig_is_dir($path) || rig_is_file($path)))
		return filemtime($path);
	else
		return 0;
}


//****************************************************
function rig_check_expired($compare_date, &$path_list)
//****************************************************
// This function checks to see if any of the path in the path_list
// contains something newer than compare_date. If yes, the comparison
// date is expired and TRUE is returned. If no, the comparison date
// is valid and FALSE is returned.
//
// The path_list may contain either folders or files paths.
// When a folder is given, the folder's modif date is compared and
// then all its first-level files are compared.
// Note that no implicit recursion is used.
// Note that if a similar path is given twice, it will be checked twice.
//
// On a filesystem, the modification date of a folder will reflect
// folder's content update (i.e. files deletes, added, etc.). It will
// not reflect if the *inside* of a file is modified.
{
	foreach($path_list as $path)
	{
		if (!is_string($path) || $path == '')
			continue;

		if (rig_is_file($path))
		{
			// check if file date is newer

			$tm = filemtime($path);
			
			if ($tm > $compare_date)
				return TRUE;
		}
		else if (rig_is_dir($path))
		{
			// check if dir modif date is newer
			
			$tm = filemtime($path);

			if ($tm > $compare_date)
				return TRUE;

			// if not, check all files in the directory (no recursion)
			// note that since there is no semantic associated to the path,
			// so it is not possible to exclude files based on the content
			// of pref_album_ignore_list or pref_image_ignore_list.

			$path = rig_post_sep($path);

			$handle = @opendir($path);
			if ($handle)
			{
				while (($file = readdir($handle)) !== FALSE)
				{
					// check if file date is newer
		
					$tm = filemtime($path . $file);
					
					if ($tm > $compare_date)
					{
						closedir($handle);
						return TRUE;				
					}
				}
				
				closedir($handle);
			}
		}
	}
	
	// comparison date not expired
	return FALSE;
}



//****************************
function rig_begin_buffering()
//****************************
// RM 20030809 v0.6.4.1
// Returns html filename to include or TRUE to start buffering and output or FALSE on errors
// When FALSE is returned, the caller should proceed generating the page is if with buffering
// except no buffering will occur.
{
	global $rig_abs_cache;
	global $rig_tmp_cache;

	// Is the feature enabled? [RM 20030821]

	global $pref_enable_album_html_cache;

	if (!$pref_enable_album_html_cache)
	{
		$rig_abs_cache = FALSE;
		$rig_tmp_cache = FALSE;
		return FALSE;
	}
	
	global $current_real_album;			// RM 20030907
	global $current_album_page;
	global $current_image_page;			// RM 20030908
	global $abs_album_path;
	global $abs_album_cache_path;
	global $abs_option_path;
	global $dir_abs_src;
	global $dir_abs_admin_src;			// RM 20040601 v0.6.4.5 - fix: missing globals
	global $dir_abs_globset;
	global $dir_abs_locset;
	global $pref_image_layout;
	global $pref_album_layout;
	global $pref_album_nb_col;
	global $pref_image_nb_col;


	global $rig_lang;
	global $rig_theme;
	global $rig_user;

	// Get the absolute cache filename
	// Note that the cache file depends on the follwing variables:
	// - current loggued user name (different users have different visibilities)
	// - color theme name
	// - language name
	// - current preference image layout
	// - current preference album layout
	// - current preference album nb col
	// - current preference image nb col

	$hash =  ($current_album_page > 1 ? rig_simplify_filename($current_album_page) . 'a_' : '')
			. ($current_image_page > 1 ? rig_simplify_filename($current_image_page) . 'i_' : '')
			. rig_simplify_filename($rig_lang) . '_'
			. rig_simplify_filename($rig_theme) . '_'
			. rig_simplify_filename($rig_user)
			. $pref_image_layout . "-"
			. $pref_album_layout . "-"
			. $pref_album_nb_col . "-"
			. $pref_image_nb_col;

	$hash = md5($hash);

	$abs_html =   rig_post_sep($abs_album_cache_path)
				. rig_post_sep($current_real_album)
				. ALBUM_CACHE_NAME
				. $hash
				. ALBUM_CACHE_EXT;

	$is_valid = rig_is_file($abs_html);

	if ($is_valid)
	{
		// To be valid, the cache must exist and must be older than:
		// - the album folder
		// - the option folder for this album         (can affect album visibility)
		// - the local  pref folder modification date (can affect album visibility)
		// - the global pref folder modification date (can affect album visibility)
		// - the RIG source  folder modification date (can affect album content)
		// - the cache folder for this album          (can affect previews sizes, etc.)
		// (in that order, most likely to change tested first)

		$tm_html   = rig_modif_date($abs_html);

		// set the list of files or folders to check
		$check_list = array($abs_album_path  . rig_prep_sep($current_real_album),
							$abs_option_path . rig_prep_sep($current_real_album),
							$dir_abs_src);

		if ($dir_abs_src != $dir_abs_admin_src)
			$check_list[] = $dir_abs_admin_src;

		if ($dir_abs_src != $dir_abs_globset)
			$check_list[] = $dir_abs_globset;

		if ($dir_abs_src != $dir_abs_locset)
			$check_list[] = $dir_abs_locset;

		// RM 20040601 v.0.6.4.5 - fix image=>album var name
		$check_list[] = $abs_album_cache_path . rig_prep_sep($current_real_album);

		// cache is valid if not expired
		$is_valid  = !rig_check_expired($tm_html, $check_list);

		// if cache is no longer valid, remove existing cache file
		if (!$is_valid)
			unlink($abs_html);
	}

	if ($is_valid)
	{
		// no buffering is going on
		$rig_abs_cache = FALSE;

		// return the filename of the cached html
		return $abs_html;
	}
	else
	{
		// memorize the cached filename
		$rig_abs_cache = $abs_html;

		// Simple version:
		// - Make sure that PHP ini's "implicit_flush" is off
		// - Use ob_start
		// when stopping:
		// - Get everything from ob_get_contents into the cache html file
		// - Use ob_end_flush

		// Advanced version:
		// - Use a callback to flush output regularly
		// - Regularly copy this output to a temporary file
		// - When stopped, swap the temp file with the destination file
		//   (using system's move, dest file must be on same volume)

		// RM 20030809 => Implementation of advanced version

		// the temp file is the destination file plus a unique id
		// [RM 20030809] originally I want to use posix_getpid() but the posix functions
		// do not seem to be available at least under Windows with the default PHP 4.3.2
		$rig_tmp_cache = $rig_abs_cache . uniqid('_');
		if (rig_is_file($rig_tmp_cache))
			unlink($rig_tmp_cache);

		ob_implicit_flush(0);
		ob_start();


		// indicate should start output
		return TRUE;
	}
	
	// errors... should never get here
	return FALSE;
}


//**************************
function rig_end_buffering()
//**************************
// RM 20030809 v0.6.4.1
{
	global $rig_abs_cache;
	global $rig_tmp_cache;


	rig_flush();


	// if cache buffering is activated...
	if (is_string($rig_abs_cache) && $rig_abs_cache != '')
	{
		if (!is_string($rig_tmp_cache) || $rig_tmp_cache == '')
		{
			// implement the output for the simplified caching mode
	
			// w=write to new to file, create as needed
			// b=binary (output whatever the script outputs, don't reinterpret end-of-lines)
			$file = fopen($rig_abs_cache, "wb");
			
			if ($file)
			{
				fwrite($file, ob_get_contents());
				fclose($file);
			}
	
			ob_end_flush();
		}
		else
		{
			// implements end-of-buffering for advanced cache mechanism
	
			ob_end_flush();
	
			// by design the target cache file should not be present
			// (if it was, it has been erased when the cache was invalidated)
			// if present, that may mean another RIG process is building the same
			// directory, so let's give up on that one
	
			if (rig_is_file($rig_abs_cache))
			{
				// remove the temp cache, no longuer needed
				unlink($rig_tmp_cache);
			}
			else
			{
				// move the temp cache to the destination cache
				rename($rig_tmp_cache, $rig_abs_cache);
			}

		}

	}

	// disable buffering
	$rig_abs_cache = FALSE;
	$rig_tmp_cache = FALSE;

	return TRUE;
}


//******************
function rig_flush()
//******************
{
	global $rig_abs_cache;
	global $rig_tmp_cache;


	// implements callback for advanced cache mechanism

	if (   is_string($rig_tmp_cache) && $rig_tmp_cache != ''
		&& is_string($rig_abs_cache) && $rig_abs_cache != '')
	{
		$str = ob_get_contents();
		if (is_string($str) && strlen($str))
		{
			// append str to temp file
			
			// a=append at end of file, create as needed
			// b=binary (output whatever the script outputs, don't reinterpret end-of-lines)
			$file = fopen($rig_tmp_cache, "ab");
			
			if ($file)
			{
				fwrite($file, $str);
				fclose($file);
			}
		}

		// ob_flush only starts with PHP 4.2.0
		if (PHP_VERSION >= "4.2.0")
		{
			ob_flush();
		}
		else
		{
			// simulate ob_flush on older PHPs
			ob_end_flush();
			ob_start();
		}
	}
	else
	{
		// cache buffering not activated, or the simplified one...
		
		flush();
	}

}


//-----------------------------------------------------------------------


//*************************************************
function rig_check_ignore_list($name, $ignore_list)
//*************************************************
// Returns TRUE if the name is to be ignore, FALSE if to be accepted
// RM 20030813 - v0.6.3.5
{
	if (is_array($ignore_list) && count($ignore_list) > 0)
		foreach($ignore_list as $pattern)
			if (preg_match($pattern, $name) == 1)
				return TRUE;
	
	return FALSE;
}




//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.44  2004/07/14 06:09:41  ralfoide
//	Renamed html caches. Minor fixes for Win32/PHP 4.3.7 support
//
//	Revision 1.43  2004/07/09 05:51:10  ralfoide
//	Fixes: don't add useless parameters in URL, no pagination when only one page, etc.
//	
//	Revision 1.42  2004/07/06 04:10:58  ralfoide
//	Fix: using "img" query param instead of "image"
//	Some browsers (at least PocketIE) will interpret "&image=" as "&image;" in URL.
//	
//	Revision 1.41  2004/06/03 14:14:47  ralfoide
//	Fixes to support PHP 4.3.6
//	
//	Revision 1.40  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.39  2004/03/02 10:38:01  ralfoide
//	Translation of tooltip string.
//	New page title strings.
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------

// IMPORTANT: the "? >" must be the LAST LINE of this file, otherwise
// some HTTP output will be started by PHP4 and setting headers or cookies
// will fail with a PHP error message.
?>
