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
	current_language		- string 'en' (default) or 'fr', 'sp', 'jp'
	current_id				- integer
	current_album			- string
	current_image			- string
	current_type			- string 'image' or 'video' -- RM 20030713
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
	dir_preview
	dir_option
	abs_images_path
	abs_album_path
	abs_preview_path
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
define("SOFT_NAME",				"Rig [Ralf Image Gallery]");
define("ALBUM_ICON",			"album_icon.jpg");
define("ALBUM_OPTIONS",			"options");
define("ALBUM_OPTIONS_TXT",		"options.txt");
define("ALBUM_OPTIONS_XML",		"options.xml");

define("DESCRIPTION_TXT",		"descript.ion");		// RM 20030713
define("FILEINFODIZ_TXT",		"file_info.diz");

// start timing...
$time_start = rig_getmicrotime();

// read site-prefs and then override with local prefs, if any
require_once(rig_require_once("prefs.php", $dir_globset));

if (rig_is_file ($dir_locset . "prefs.php"))
	require_once($dir_locset . "prefs.php");

// setup...
require_once(rig_require_once("version.php",    $dir_src));
require_once(rig_require_once("login_util.php", $dir_src));

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
require_once(rig_require_once("str_en.php", $dir_src, $abs_upload_src_path));

// and override with other language if not english

// DEBUG
// rig_check_src_file($dir_install . $dir_src . "str_$current_language.php");


// Fix (Paul S. 20021013): if requested lang doesn't exist, revert to english
if (!isset($current_language) || !rig_is_file($dir_install . $dir_src . "str_$current_language.php"))
	$current_language = $pref_default_lang;

if (is_string($current_language) && $current_language != 'en')
{
	require_once(rig_require_once("str_$current_language.php", $dir_src, $abs_upload_src_path));
}

// include theme strings
//----------------------

// DEBUG
// rig_check_src_file($dir_install . $dir_src . "theme_$current_theme.php");

if (!isset($current_theme) || !rig_is_file($dir_install . $dir_src . "theme_$current_theme.php"))
	$current_theme = $pref_default_theme;
require_once(rig_require_once("theme_$current_theme.php", $dir_src, $abs_upload_src_path));

rig_setup();
rig_create_option_dir("");

// load common source code -- note these do not use the src_upload override
require_once(rig_require_once("common_display.php", $dir_src));
require_once(rig_require_once("common_images.php",  $dir_src));
require_once(rig_require_once("common_xml.php",     $dir_src));			// RM 20030216

// RM 20021021 not for rig 062 yet
// require_once(rig_require_once("common_db.php",  $dir_src));

rig_setup_db();


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
	if ($file_str)
		echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>File:</b> $file_str\n </td></tr>\n";

	// php error msg
	if ($php_str)
		echo "<tr><td bgcolor=\"$color_error2_bg\">\n<b>PHP Error:</b> $php_str\n </td></tr>\n";

	echo "</table></center><p>\n";

	// Also assumes that browsers will continue displaying the HTML after a
	// bad </body>.
	echo "</body>\n";

	return FALSE;
}

//-----------------------------------------------------------------------


//*********************************************************************
function rig_require_once($filename, $main_dir, $abs_override_dir = "")
//*********************************************************************
// RM 20030308
//
// Includes a PHP source file, looking in $dir_install + $main_dir
// or $abs_override_dir. The override dir is checked FIRST and is ABSOLUTE!
// it's purpose is to override the main file with the overriding one.
//
// It is ok for the override dir not to exist or not contain the file.
// It is mandatory that the main dir exists and contains the file.
//
// IMPORTANT: require_once uses the caller's scope which means the
// file can't be included/required here or it wouldn't have a global scope
// thus this function actually returns a string with the file to be
// required and it's up to the caller to actually perform the require_once().
{
	global $dir_install, $abs_upload_src_path;

	// DEBUG
	// echo "<p>rig_require_once: filename='$filename', main_dir='$main_dir', abs_override_dir='$abs_override_dir' \n";

	$main = rig_post_sep($dir_install) . rig_post_sep($main_dir);
	$over = rig_post_sep($abs_override_dir);

	// check params

	if (!$filename)
	{
		return rig_html_error("Invalid parameter!",
			                  "Empty 'filename' argument in function rig_require_once!",
	            		      $main_dir);
	}

	if (!$main_dir)
	{
		return rig_html_error("Invalid parameter!",
			                  "Empty 'main_dir' argument in function rig_require_once!",
	            		      $filename);
	}

	// check main file exists -- it must, even if we're going to use the override

	if (!rig_check_src_file($main . $filename))
		return FALSE;

	// check override file and use it exists
	if ($abs_override_dir && rig_is_file($over . $filename))
	{
		return $over . $filename;
	}

	// otherwise default to the main one
	return $main . $filename;
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
{
	global $pref_file_types;

	foreach($pref_file_types as $filter => $type)
	{
		if (preg_match($filter, $name) > 0)
			return $type;
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
	// RM 102201 -- escapeshellarg is "almost" a good candidate for linux
	// but for windows we need escapeshellcmd because a path may contain backslashes too

	return "\"" . escapeshellcmd($str) . "\"";
}


//********************************
function rig_shell_filename2($str)
//********************************
// Encode a filename before using it in a shell argument call
// This one is more dedicated for directly unix calls.
// Escapeshellcmd will transform ' into \' which is not always appropriate.
{
	$s = "\"" . escapeshellcmd($str) . "\"";
	$s = str_replace("\\'", "'", $s);
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
	global $abs_preview_path;

	if (!rig_mkdir($abs_preview_path, $album, $pref_mkdir_mask))
	{
		return rig_html_error("Create Preview Directory",
					   "Failed to create directory",
					   $album,
					   $php_errormsg);
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
					  $in_page = -1,
					  $in_extra = "")
//*****************************************************************
// encode album/image name as url links
// in_image: -1 (use current if any) or text for image=...
// in_album: -1 (use current if any) or text for album=...
// in_page : -1 (use current if any) or RIG_SELF_URL_xxx (see above)
// in_extra: extra parameters (in the form name=val&name=val etc)
//
// Use URL-Rewriting when defined in prefs [RM 20030107]
{
	global $album;				// from index.php url line
	global $image;				// from index.php url line
	global $admin;				// from index.php url line
	global $translate;			// from index.php url line -- RM 20030308
	global $upload;				// from index.php url line -- RM 20030308
	global $credits;
	global $phpinfo;
	global $current_id;
	global $current_album;
	global $current_image;
	global $PHP_SELF;
	global $_debug_;
	global $pref_url_rewrite;	// RM 20030107

	// DEBUG
	// echo "<p>rig_self_url: in_page=$in_page\n";


	$use_rewrite = (is_array($pref_url_rewrite) && count($pref_url_rewrite) >= 3);

	if ($use_rewrite)
		$url = $pref_url_rewrite['index'];
	else
		$url = $PHP_SELF;

	$params = "";
	$param_concat_char = "?";

	if ($in_album == -1)
	{
		$in_album = $current_album;

		if (!$in_album)
			$in_album = rig_decode_argument($album);
	}

	if ($in_album)
		$in_album = rig_encode_url_link($in_album);

	if ($in_image == -1)
	{
		$in_image = $current_image;

		if (!$in_image)
			$in_image = rig_decode_argument($image);
	}

	if ($in_image)
		$in_image = rig_encode_url_link($in_image);

	// check in_page param
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
			rig_url_add_param(&$params, 'admin', 'on');
			break;

		case RIG_SELF_URL_TRANSLATE:
			rig_url_add_param(&$params, 'admin', 'on');
			rig_url_add_param(&$params, 'translate', 'on');
			break;

		case RIG_SELF_URL_UPLOAD:
			rig_url_add_param(&$params, 'admin', 'on');
			rig_url_add_param(&$params, 'upload', 'on');
			break;
	}


	if ($in_album)
	{
		if ($use_rewrite)
		{
			$url = $pref_url_rewrite['album'];
			$param_concat_char = "&";
		}
		else
		{
			rig_url_add_param(&$params, 'album', $in_album);
		}
	}


	if ($in_image)
	{
		if ($use_rewrite)
		{
			$url = $pref_url_rewrite['image'];
			$param_concat_char = "&";
		}
		else
		{
			rig_url_add_param(&$params, 'image', $in_image);
		}
	}

	if ($_debug_)
		rig_url_add_param(&$params, '_debug_', '1');

	if ($credits == 'on')
		rig_url_add_param(&$params, 'credits', $credits);

	if ($phpinfo == 'on')
		rig_url_add_param(&$params, 'phpinfo', $phpinfo);


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
	$abs_images_path   = realpath($dir_abs_images . $dir_images);

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

	// --- previews directory ---

	global $dir_preview, $abs_preview_path;
	$abs_preview_path = realpath($dir_abs_album . $dir_preview);

	if (!is_string($abs_preview_path))
	{
		rig_html_error("Missing Previews Directory",
					   "Can't get absolute path for the previews directory. <p>" .
					   "<b>Base directory:</b> $dir_abs_album<br>" .
					   "<b>Target directory:</b> $dir_preview<br>" ,
					   $dir_abs_album . $dir_preview);
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

	global $pref_preview_exec, $dir_install, $abs_preview_exec;
	$abs_preview_exec = realpath($dir_install . $pref_preview_exec);

	if (!is_string($abs_preview_exec))
	{
		rig_html_error("Missing rig_thumbnail application",
					   "Can't get absolute path for rig_thumbnail application. " .
					   "<br>Check file has actually been compiled, or check permissions.<p>" .
					   "<b>Installation directory:</b> $dir_install<br>" .
					   "<b>Path of exectable:</b> $pref_preview_exec<br>" ,
					   $dir_install . $pref_preview_exec);
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

	unset($list_hide);
	unset($list_album_icon);
	unset($list_description);
}


//*************************************
function rig_read_album_options($album)
//*************************************
{
	// first clear current options
	rig_clear_album_options();

	// if XML options are available, just read them
	if (rig_xml_read_options($album))
		return TRUE;
		
	// then grab new ones
	global $abs_preview_path;	// old location for options was with previews
	global $abs_option_path;	// new options have their own base directory (may be shared with previews anyway)

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
		$abs_options = $abs_preview_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_OPTIONS_TXT);
	
		// silently abort if the file does not exist
		if (!rig_is_file($abs_options))
			return FALSE;
	}

	// DEBUG
	// global $_debug_;
	// if ($_debug_)  echo "<p>Reading abs_options '$abs_options'<br>";

	$file = @fopen($abs_options, "rt");

	if (!$file)
		return rig_html_error("Read Album Options", "Failed to read from file", $abs_options, $php_errormsg);

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
			$key = -1;
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
					if ($reg[2] === FALSE)
						$value = "";
					else
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
	// if ($_debug_) { echo "<p>Reading list_hide: "; var_dump($list_hide);}

	fclose($file);		// RM 20020713 fix
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
	$tm = filemtime(rig_post_sep($abs_dir) . ".");
	return strftime($html_album_date, $tm);	
}


//******************************************
function rig_read_album_descriptions($album)
//******************************************
// Reloads the content of $list_description
//	list_description		- array of [filename] => description (text and/or html) -- RM 20030713
{
	global $abs_album_path;		// descriptions
	global $abs_option_path;	// new options have their own base directory (may be shared with previews anyway)


	// first clear current options
	global $list_description;
	unset($list_description);

		
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

		// need a valid line
		if (!$line || $line == "" || $line == EOF || $is_comment)
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


//*******************************************************
function rig_write_album_options($album, $silent = FALSE)
//*******************************************************
// Currently writes:
//	list_hide				- array of filename
//	list_album_icon			- array of icon info { a:album(relative) , f:file, s:size }
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
	// echo "<p> abs_options = $abs_options\n";

	// make sure the directory exists
	// don't output an error message, the create function does it for us
	if (!rig_create_option_dir($album))
		return FALSE;

	$abs_options = $abs_option_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_OPTIONS_TXT);

	// make sure the directory exists

	$file = fopen($abs_options, "wt");

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
	//echo "<p> list_album_icon = \n"; var_dump($list_album_icon);

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


//***************************
function rig_handle_cookies()
//***************************
// Some literature:
// http://developer.netscape.com:80/docs/manuals/js/client/jsguide/cookies.htm
{
	global $lang;
	global $rig_lang;
	global $current_language;

	global $theme;
	global $rig_theme;
	global $current_theme;

	global $img_size;
	global $rig_img_size;
	global $pref_image_size;

	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;

	global $login_error;
	global $force_login,  $keep;
	global $user, 		  $passwd;
	global $admusr, 	  $admpwd;
	global $rig_user,	  $rig_passwd;
	global $rig_adm_user, $rig_adm_passwd;

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

		if (rig_test_user_pwd(FALSE, &$rig_user, &$rig_passwd, &$login_error))
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

		if (rig_test_user_pwd(TRUE, &$rig_adm_user, &$rig_adm_passwd, &$login_error))
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
	$display_softname  = SOFT_NAME;


	// -- keep track of php errors with $php_errormsg (cf html_error)
	ini_set("track_errors", "1");


	// -- validate prefs use flags --
	// RM 20021021 invalidate DB support in rig 0.6.2
	$pref_use_db = FALSE;
	$pref_use_db_id = FALSE;
	$pref_use_id_in_url = FALSE;
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


//************************************************
function rig_prepare_album($id, $album, $title="")
//************************************************
{
	// List of globals defined for the album page by prepare_album():
	// $current_album		- string
	// $display_title		- string
	// $display_album_title	- string

	global $abs_album_path;
	global $current_album;
	global $current_id;
	global $pref_use_db_id;
	global $display_title;
	global $display_album_title;
	global $html_album, $html_none;
	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5

	$current_album = FALSE;
	$current_id = 0;

	// first try the index argument
	// RM 20021021 not for rig 0.6.2

	// second try the named argument
	if (!$current_album && isset($album))
	{	
		$current_album = rig_decode_argument($album);
	}

	// does the album really exist?
	if ($current_album)
	{
		$abs_dir = $abs_album_path . rig_prep_sep($current_album);

		if (rig_check_ignore_list($current_album, $pref_album_ignore_list) || !rig_is_dir($abs_dir))
		{
			// directory doesn't exist or is to be ignored => unset variables
			$current_id = 0;
			$current_album = "";
		}
		// else ... RM 20021021 not for rig 0.6.2
	}

	// -- setup title of album
	if (!$title)
		$title = $html_album;

	if ($current_album)
	{
		$items = explode(SEP, $current_album);
		$pretty = rig_pretty_name($items[count($items)-1], FALSE, TRUE);
		$display_title = "$title - " . $pretty;
		$display_album_title = "$html_album - " . $pretty;
	}
	else
	{
		$display_title = "$title - $html_none";
		$display_album_title = "$html_album - $html_none";
	}

	rig_read_album_options($current_album);
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
	global $abs_album_path;

	// DEBUG
	// echo "<br>Current Album = \"$current_album\"";

	$abs_dir = $abs_album_path . rig_prep_sep($current_album);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
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
		rig_create_preview_dir($current_album);

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
	
	rig_read_album_descriptions($current_album);
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

	// by default count everything
	$list_albums_count = $list_albums;

	// None at all, so that's a positive false
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
	global $current_id;
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


//********************************************************
function rig_prepare_image($id, $album, $image, $title="")
//********************************************************
{
	rig_setup();

	// List of globals defined for the album page by prepare_album():
	// $current_image		- string
	// $pretty_image		- string
	// $current_album		- string
	// $current_img_info	- array of {format, width, height}
	// $display_title		- string
	// $display_album_title	- string

	global $current_id;
	global $current_album;
	global $current_image;
	global $current_type;		// RM 20030713
	global $current_img_info;
	global $pref_use_db_id;
	global $abs_album_path;
	global $pretty_image;
	global $display_title;
	global $display_album_title;
	global $html_image;

	global $pref_album_ignore_list;		// RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;

	$current_album = FALSE;
	$current_image = FALSE;
	$current_type = "";
	$current_id = 0;

	// first try the index argument
	// RM 20021021 no db in rig 062

	// second try the named argument
	if (!$current_image && isset($image))
	{
		$current_album = rig_decode_argument($album);
		$current_image = rig_decode_argument($image);
	}

	// check the ignore lists and invalidate names if necessary
	if ($current_album && rig_check_ignore_list($current_album, $pref_album_ignore_list))
	{
		$album = '';
		$current_album = '';
	}

	if ($current_image && rig_check_ignore_list($current_album, $pref_image_ignore_list))
	{
		$image = '';
		$current_image = '';
	}
	

	// does the image really exist?
	// is the image hidden?
	if ($current_image)
	{
		$rel_img = $current_album . rig_prep_sep($current_image);
		$abs_img = $abs_album_path . rig_prep_sep($current_album) . rig_prep_sep($current_image);

		// RM 20030713 if the image is hidden, redirect to the album
		// to prevent viewing hidden images by using a direct name
		// Note: for albums, this is allowed (on purpose). That's just the way I feel it.

		if (!rig_is_file($abs_img) || !rig_is_visible(-1, -1, $current_image))
		{
			// no, unset variables
			$current_id = 0;
			$current_album = "";
			$current_image = "";
			
			// [RM 20021225] invalidate current image
			// and then redirect to the album
			global $image;
			$image = "";
			$refresh_url = rig_self_url();
			header("Location: $refresh_url");
			exit;
		}
		else if ($pref_use_db_id && !$current_id)
		{
			// image exists, create an id if not done yet
			$current_id = rig_db_id_for_image($rel_img, TRUE);
		}
	}

	$pretty_image  = rig_pretty_name($current_image, FALSE);

	$current_img_info = rig_build_info($current_album, $current_image);

	// -- get image type
	// (that's the part before / in the file's type)
	
	list($current_type, $dummy) = explode("/", rig_get_file_type($current_image), 2);

	// -- setup title of album
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

	rig_read_album_options($current_album);
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
	global $current_album;
	global $current_image;
	global $html_image;
	global $list_images;


	if (PHP_VERSION > "4.0.5")
	{
		# array_search is >= PHP 4.0.5
		$key = array_search($current_image, $list_images, TRUE);
	}
	else
	{
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
	}


	// DEBUG
	// echo "current = $current_image -- array = $list_images -- key = $key";

	if (is_bool($key) && $key == FALSE)
		return rig_html_error("Get Prev/Next Images", "Can't find image in internal list!", $current_image);

	if ($key > 0)
	{
		$file = $list_images[$key-1];

		$pretty = rig_pretty_name($file, FALSE);
		$preview = rig_encode_url_link(rig_build_preview($current_album, $file));

		$display_prev_link = rig_self_url($file);
		$display_prev_img = "<img src=\"$preview\" alt=\"$pretty\" title=\"$html_image: $pretty\" border=0>";
	}

	if ($key < count($list_images)-1)
	{
		$file = $list_images[$key+1];

		$pretty = rig_pretty_name($file, FALSE);
		$preview = rig_encode_url_link(rig_build_preview($current_album, $file));

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
	global $dir_install, $dir_src, $abs_upload_src_path;


	// get the installation-relative path of the file
	$file1 = rig_post_sep($dir_install . $dir_src) . $filename;
	$file2 = rig_post_sep($abs_upload_src_path)    . $filename;

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
		return rig_check_src_file($filename);
	}

	$tok_sep = " \t\n\r";

	// for every line...
	while(!feof($file))
	{
		$line = fgets($file, 1023);

		// if the line is empty, we skip it
		if (!is_string($line) || !$line || $line == EOF)
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
			// acces the global variable
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
//	Revision 1.26  2003/08/14 04:42:08  ralfoide
//	Album & Image ignore lists
//
//	Revision 1.25  2003/07/21 04:56:46  ralfoide
//	Using strftime (localizable) for dates; Ability to set locale depending on page language
//	
//	Revision 1.24  2003/07/19 07:52:36  ralfoide
//	Vertical layout for albums
//	
//	Revision 1.23  2003/07/14 18:30:14  ralfoide
//	Support for descript.ion and file_info.diz
//	
//	Revision 1.22  2003/06/30 06:08:11  ralfoide
//	Version 0.6.3.4 -- Introduced support for videos -- new version of rig_thumbnail.exe
//	
//	Revision 1.21  2003/03/22 01:22:56  ralfoide
//	Fixed album/image count display in admin mode
//	Added "old" layout for image display, with image layout pref variable.
//	
//	Revision 1.20  2003/03/17 08:24:42  ralfoide
//	Fix: added pref_disable_web_translate_interface (disabled by default)
//	Fix: added pref_disable_album_borders (enabled by default)
//	Fix: missing pref_copyright_name in settings/prefs.php
//	Fix: outdated pref_album_copyright_name still present. Eradicated now :-)
//	
//	Revision 1.19  2003/03/12 07:02:08  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.18  2003/02/23 10:18:36  ralfoide
//	plain vs crypt vs MD5 password in the password file
//	
//	Revision 1.17  2003/02/23 08:14:36  ralfoide
//	Login: display error msg when invalid password or invalid user
//	
//	Revision 1.16  2003/02/17 10:03:00  ralfoide
//	Toying with XML
//	
//	Revision 1.15  2003/02/17 07:47:01  ralfoide
//	Debugging. Fixed album visibility not being used correctly
//	
//	Revision 1.14  2003/02/17 07:34:54  ralfoide
//	Conditional debuggin
//	
//	Revision 1.13  2003/02/16 22:42:27  ralfoide
//	Report mkdir failure. Misc fix.
//	
//	Revision 1.12  2003/02/16 21:30:32  ralfoide
//	fix reading _value lines in options.txt
//	
//	Revision 1.11  2003/02/16 20:22:54  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.10  2003/01/07 18:02:01  ralfoide
//	Support for URL-Rewrite conf array
//	
//	Revision 1.9  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.8  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
//	Revision 1.7  2002/10/21 07:33:33  ralfoide
//	debug stuff
//	
//	Revision 1.6  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.5  2002/10/20 11:49:37  ralfoide
//	Added shell_filename2
//	
//	Revision 1.4  2002/10/16 06:58:21  ralfoide
//	Fixed typo
//	
//	Revision 1.3  2002/10/16 05:05:24  ralfoide
//	Fix (Paul S. 20021013): if requested lang doesn't exist, revert to english
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------

// IMPORTANT: the "? >" must be the LAST LINE of this file, otherwise
// some HTTP output will be started by PHP4 and setting headers or cookies
// will fail with a PHP error message.
?>
