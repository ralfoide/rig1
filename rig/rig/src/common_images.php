<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------



//******************************************************************
function rig_make_image($abs_source, $abs_dest, $size, $quality = 0)
//******************************************************************
// returns the status code from the executable: 0=no error, 1-2-etc=error
{
	global $abs_preview_exec;
	global $pref_preview_timeout;
	global $pref_preview_quality;
	global $pref_global_gamma;

	// inform PHP this may take a while...
	if ($pref_preview_timeout)
		set_time_limit($pref_preview_timeout);

	$args = "-r " . rig_shell_filename($abs_source) . " " . rig_shell_filename($abs_dest) . " $size";

	// add quality argument
	if ($quality > 0)
		$args .= " $quality";
	else if ($pref_preview_quality)
		$args .= " $pref_preview_quality";

	// add gamma argument
	$args .= " $pref_global_gamma";

	// debug
	// echo "mk image:<br>src=$abs_source<br>dst=$abs_dest<p>\n";
	// echo "<br> args = $args <br> exec = $abs_preview_exec <br>\n";

	// create the preview now
	// RM 20030628 using exec instead of system (system's output goes directly in the HTML!)
	// $res = @system($abs_preview_exec . " " . $args, $retvar);
	$res = exec($abs_preview_exec . " " . $args, $output, $retvar);

	// debug
	// echo "<br> res = $res\n";

	if ($retvar)
		rig_html_error("Create Thumbnail",
					   "Error $retvar during image creation<p>" .
					   "<b>Source:</b> $abs_source<br>" .
					   "<b>Dest:</b> $abs_dest<br>" .
					   "<b>Size</b> $size, <b>Quality</b> $quality<br>" .
					   "<b>Exec:</b><br>&nbsp;&nbsp;|<i>$abs_preview_exec</i><br>&nbsp;&nbsp;$args|", 
					   $php_errormsg);

	return $retvar;
}


//*********************************************************************
function rig_build_image_type($album, $file,
							  $size = -1, $quality = -1,
							  $auto_create = TRUE, $use_default = TRUE)
//*********************************************************************
// Builds a resized version of the original $album->$file image
// Size and quality default to the preview size, unless specified
// auto_create ask the image to be created if no existing
// use_default ask the pref_empty_album name to be returned if the image cannot be build
//
// returns the name for the preview as an array { r:pref_path , a:abs_pref_path, p:image_path }
// Caller should thus use [r]+[p] or [a]+[p]
{
	global $abs_album_path;
	global $abs_preview_path;
	global $dir_abs_album;
	global $dir_album;
	global $dir_preview;
	global $dir_images;
	global $pref_preview_size;
	global $pref_empty_album;
	global $current_img_info;

	// debug
	// echo "size = $size<br>\n";

	if ($size == -1)
		$size = $pref_preview_size;


	// a size of 0 is a special argument: the original image size should be used
	// it is to be noted that in this case, no preview is created, the original
	// image path is simply returned!
	// or
	// if we have the img information (width and height) and the largest one
	// matches the requested size, then we can use the original image too
	if ($size <= 0
		||
		($current_img_info
		 && (($current_img_info["w"] >= $current_img_info["h"] && $current_img_info["w"] == $size)
			 ||
			 ($current_img_info["h"] >  $current_img_info["w"] && $current_img_info["h"] == $size)
	   ))   )
	{
		return array("r" => $dir_album,
					 "a" => $abs_album_path,
					 "p" => rig_prep_sep($album) . rig_prep_sep($file));
	}

	$prev_prefix = "prev" . $size . "_";
	$dest_file = $prev_prefix . rig_simplify_filename($file);

	$dest		= $album . rig_prep_sep($dest_file);
	$abs_dest	= $abs_preview_path . rig_prep_sep($dest);
	$abs_source	= $abs_album_path   . rig_prep_sep($album) . rig_prep_sep($file);

	if (rig_is_file($abs_source) && !rig_is_file($abs_dest) && $auto_create)
	{
		if (rig_make_image($abs_source, $abs_dest, $size, $quality) != 0)
		{
			// in case of error, use the default icon...
			// RM 20030628 TBDL fix path (cf video)
			if ($use_default)
			{
				// RM 20030628 fix: the fixed image in /images/ not in the album's root
				return array("r" => $dir_images,
							 "a" => $abs_images_path,
							 "p" => $pref_empty_album);
			}
		}
	}

	return array("r" => $dir_preview,
				 "a" => $abs_preview_path,
				 "p" => $dest);
}


//*********************************************************************
function rig_build_video_type($album, $file,
							  $size = -1, $quality = -1,
							  $auto_create = TRUE, $use_default = TRUE)
//*********************************************************************
// Builds a resized version of the original $album->$file image
// Size and quality default to the preview size, unless specified
// auto_create ask the image to be created if no existing
// use_default ask the pref_empty_album name to be returned if the image cannot be build
//
// returns the name for the preview as an array { r:pref_path , a:abs_pref_path, p:image_path }
// Caller should thus use [r]+[p] or [a]+[p]
{
	global $abs_album_path;
	global $abs_preview_path;
	global $abs_images_path;
	global $dir_abs_album;
	global $dir_album;
	global $dir_preview;
	global $dir_images;
	global $pref_preview_size;
	global $pref_empty_album;
	global $current_img_info;

	// debug
	// echo "size = $size<br>\n";

	// a size of 0 is a special argument: the original image size should be used
	// this does not apply to video, so the preview size is used anyway
	if ($size <= 0)
		$size = $pref_preview_size;

	$prev_prefix = "prev" . $size . "_";
	$dest_file = $prev_prefix . rig_simplify_filename($file);

	$dest		= $album . rig_prep_sep($dest_file);
	$abs_dest	= $abs_preview_path . rig_prep_sep($dest);
	$abs_source	= $abs_album_path   . rig_prep_sep($album) . rig_prep_sep($file);

	if (rig_is_file($abs_source) && !rig_is_file($abs_dest) && $auto_create)
	{
		if (rig_make_image($abs_source, $abs_dest, $size, $quality) != 0)
		{
			// in case of error, use the default icon...
			// RM 20030628 fix: the fixed image in /images/ not in the album's root
			return array("r" => $dir_images,
						 "a" => $abs_images_path,
						 "p" => $pref_empty_album);
		}
	}

	return array("r" => $dir_preview,
				 "a" => $abs_preview_path,
				 "p" => $dest);
}


//*********************************************************************
function rig_build_preview_ex($album, $file,
							  $size = -1, $quality = -1,
							  $auto_create = TRUE, $use_default = TRUE)
//*********************************************************************
{
	$type = rig_get_file_type($file);

	if (strncmp($type, "image/", 6) == 0)
		return rig_build_image_type($album, $file, $size, $quality, $auto_create, $use_default);
	else if (strncmp($type, "video/", 6) == 0)
		return rig_build_video_type($album, $file, $size, $quality, $auto_create, $use_default);		

	return null;
}


//******************************************************************
function rig_build_preview($album, $file,
						   $size = -1, $quality = -1,
						   $auto_create = TRUE, $use_default = TRUE)
//******************************************************************
{
	$info = rig_build_preview_ex($album, $file, $size, $quality, $auto_create, $use_default);

	return rig_post_sep($info["r"]) . $info["p"];
}


//***********************************************************************
function rig_build_preview_info($album, $file,
								$size = -1, $quality = -1,
								$auto_create = TRUE, $use_default = TRUE)
//***********************************************************************
// returns an array:
// [p]=translated URL path
// [w]=width
// [h]=height
{
	$info = rig_build_preview_ex($album, $file, $size, $quality, $auto_create, $use_default);

	// build the output array, fill in the first field, i.e. the URL path
	$res = array();
	$res["p"] = rig_post_sep($info["r"]) . $info["p"];

	// build the abs path
	// get the info
	$info = rig_image_info(rig_post_sep($info["a"]) . $info["p"]);

	// set the info and return the array
	$res["w"] = $info["w"];
	$res["h"] = $info["h"];

	return $res;
}



//********************************
function rig_image_info($abs_file)
//********************************
// Returns an array of strings:
// { f:format, w:width, h:height, d:date }
// Returns an empty array if file does not exists
{
	global $html_img_date;
	global $abs_preview_exec;
	global $pref_preview_size;

	$info = array();

	if (rig_is_file($abs_file))
	{
		// --- get the file's creation date ---

		$modified  = stat($abs_file);
		$info["d"] = date($html_img_date, $modified[9]);

		// get the file type -- RM 20030628
		$type = rig_get_file_type($abs_file);

		if (strncmp($type, "image/", 6) == 0 || strncmp($type, "video/", 6) == 0)
		{
			// --- use the thumbnail application to extract info ---
	
			$args = "-i " . rig_shell_filename($abs_file) . "";
	
			// get the info now
			$res = exec($abs_preview_exec . " " . $args, $output, $retvar);
	
			if ($retvar == 127)
			{
				rig_html_error("Get Image Information",
							   "Error $retvar during image info: <em>file not found</em><p>" .
							   "<b>Exec:</b><br>&nbsp;&nbsp;|<i>$abs_preview_exec</i><br>&nbsp;&nbsp;$args|",
							   $abs_preview_exec,
							   $php_errormsg);
			}
			else if ($retvar)
			{
				rig_html_error("Get Image Information",
							   "Unexpected error $retvar during image info<p>" .
							   "<b>Exec:</b><br>&nbsp;&nbsp;|<i>$abs_preview_exec</i><br>&nbsp;&nbsp;$args|",
							   $abs_preview_exec,
							   $php_errormsg);
			}
			else
			{
				// usually the output is the last line
				// lib avifile tends to have a verbose output so filter starting by last line
				$n = count($output);

				for($i = $n-1; $i>=0; $i--)
				{
					if (preg_match("/\[rig-thumbnail-result\][ \t]+([a-z]+)[ \t]+([0-9]+)[ \t]+([0-9]+)/", $output[$i], $res) > 0)
					{
						// $res[0] contains the full line, 1/2/3 contain the several matches
						$info["f"] = $res[1];
						$info["w"] = $res[2];
						$info["h"] = $res[3];

						break;
					}
				}
			}
		}
		else
		{
			// Not a know file type.
			// Return the default size of the previews

			$info["f"] = "Unknown";
			// $info["w"] = $pref_preview_size;
			// $info["h"] = $pref_preview_size * 3 / 4;
		}

	}

	return $info;
}


//************************************
function rig_build_info($album, $file)
//************************************
// Returns an array of strings:
// { f:format, w:width, h:height, d:date }
{
	global $abs_album_path;

	return rig_image_info($abs_album_path . rig_prep_sep($album) . rig_prep_sep($file));
}


//*********************************************************
function rig_get_album_preview($album, $use_default = TRUE)
//*********************************************************
{
	$abs_path = "";
	$url_path = "";
	rig_build_album_preview($album, &$abs_path, &$url_path, $use_default);
	return $url_path;
}


//***********************************************************************************
function rig_build_album_preview($album, &$abs_path, &$url_path,
								 $use_default = TRUE, $check_icon_properties = FALSE)
//***********************************************************************************
// Returns TRUE if album icon could be found, otherwise FALSE and returns the
// path to the default one
// RM 20030125 added check_icon_properties to rebuild the icon if necessary
{
	global $dir_images;
	global $dir_abs_album;
	global $pref_empty_album;
	global $pref_preview_size;
	global $abs_album_path;
	global $abs_preview_path, $dir_preview;

	// memorize if the global album options will have been changed
	$album_options_changed = FALSE;
	// by default, don't re create icons
	$create_icon = FALSE;

	// the target file this is all about
	$dest_file = rig_prep_sep($album) . rig_prep_sep(ALBUM_ICON);

	$abs_path = $abs_preview_path  . $dest_file;
	$url_path = $dir_preview . $dest_file;

	// -1- perform various checks

	if (!rig_is_file($abs_path))
	{
		// if there is no icon, we need to build one
		$create_icon = TRUE;
	}
	else if ($check_icon_properties)
	{
		// check several properties of the icon: the file's date, the size, etc.

		// try to get the size of the _existing_ album icon
		$s = 0;
		$info = rig_image_info($abs_path);
		if (is_array($info) && isset($info['w']) && isset($info['h']))
			$s = max($info['w'], $info['h']);

		if ($s == 0)
		{
			echo "<br>Album icon <font color=red>does not exist!</font>\n";
		}
		else if ($s != $pref_preview_size)
		{
			echo "<br>Album icon needs to be rebuild: does not have preview size\n";

			// need to create if size is not up-to-date
			$create_icon = TRUE;
		}
		else
		{
			// now check the date of the album icon vs the original one

			// first the existing album icon, aka the "destination" file
			$date_dest = filemtime($abs_path);

			// if this album has information about the icon, use it
			// make sure we have the correct album options
			if ($album != $current_album)
				$album_options_changed = rig_read_album_options($album);

			// now get the name of the source of the icon
			global $list_album_icon;
			$source_file = $abs_album_path . rig_prep_sep($album) . rig_prep_sep($list_album_icon['a']) . rig_prep_sep($list_album_icon['f']);

			// if the source is newer or does not exist, icon needs to be updated
			if (!rig_is_file($source_file))
			{
				$create_icon = TRUE;
				echo "<br>Album icon needs to be rebuild: source file does not exist\n";
			}
			else if (filemtime($source_file) > $date_dest)
			{
				$create_icon = TRUE;
				echo "<br>Album icon needs to be rebuild: source file is newer!\n";
			}
		}
	}

	// -2- create icon as requested

	if ($create_icon)
	{
		// if this album has information about the icon, use it
		// make sure we have the correct album options
		if ($album != $current_album)
			$album_options_changed = rig_read_album_options($album);

		global $list_album_icon; // array of icon info { a:album(relative) , f:file, s:size }
		if (is_array($list_album_icon) && is_string($list_album_icon['f']))
			$create_icon = !rig_set_album_icon($album, $album . rig_prep_sep($list_album_icon['a']), $list_album_icon['f'], FALSE);

		// if previous didn't work, try to make a default random one
		if ($create_icon)
			rig_select_random_album_icon($album);
	}

	// read the current options back if changed
	if ($album_options_changed)
		rig_read_album_options($current_album, $check_icon_properties);

	// if there's a file, just use that
	if (rig_is_file($abs_path) || !$use_default)
	{
		return TRUE;
	}

	// otherwise, use the default icon...
	$abs_path = realpath($dir_abs_album . $dir_images . $pref_empty_album);
	$url_path = $dir_images . $pref_empty_album;	// RM 20020713 fix missing dir_images

	return FALSE;
}


//****************************************************************************************
function rig_set_album_icon($dest_album, $prev_album, $prev_image, $update_options = TRUE)
//****************************************************************************************
/*
	Creates and sets the icon for this album.
	Returns TRUE if the icon could be made succesfully, FALSE otherwise.

	dest_album = the album for which the icon is to be set (ex: "Public/Ralf")
	prev_album = the album containing the preview icon, which must be UNDER dest_album
				 (for example "Public/Ralf/Friends/Pictures")
	prev_image = the image file name in the prev_album album (ex: "102-1234_IMG.JPG")
	update_options = TRUE if settings should be written, FALSE if not. Default is TRUE.

	When stored in the list_album_icon, the prev_album name will be made relative to dest_album.
	Currently this will only work if prev_album is under dest_album, "../" won't be inserted.
	Note that by design the relative album name will be either empty (local) or "/something".
*/
{
	global $abs_preview_path;

	// get its preview
	$preview = rig_build_preview($prev_album, $prev_image, -1, -1, TRUE, FALSE);

	// if the preview actually exist
	if ($preview && rig_is_file($preview))
	{
		// if preview is of a known type...
		if (rig_get_file_type($preview) != "")
		{
			// copy it as the album icon
			$abs_dest = $abs_preview_path . rig_prep_sep($dest_album) . rig_prep_sep(ALBUM_ICON);
	
			rig_create_preview_dir($dest_album);
	
			if (!copy($preview, $abs_dest))
			{
				return rig_html_error( "Set Album Icon",
									   "Can't copy preview '$preview' to album icon '$abs_dest'!",
									   NULL,
									   $php_errormsg);
			}
	
			// now keep that in the album options
			if ($update_options)
			{
				// make sure we have the correct album options
				rig_read_album_options($dest_album);
	
	
				// remove existing settings if any
				// note the trick here -- in PHP4 unset will always delete the "local" variable
				// cf http://www.php.net/manual/en/function.unset.php
				unset($GLOBALS['list_album_icon']);
	
				// get prev_album relative to dest_album
				$rel_album = str_replace($dest_album, "", $prev_album);
	
				// DEBUG
				// echo "<p>SET ALBUM ICON:\n";
				// echo "<br>dest_album = $dest_album\n";
				// echo "<br>prev_album = $dest_album\n";
				// echo "<br>rel_album = $rel_album\n";
	
				// create new info -- fill in global variable
				global $list_album_icon;	// array of icon info { a:album(relative) , f:file, s:size }
				global $pref_preview_size;
				$list_album_icon = array('a' => $rel_album,
										 'f' => $prev_image,
										 's' => $pref_preview_size);
	
				// write the options back
				return rig_write_album_options($dest_album, TRUE);	// use FALSE to debug

			} // update options

		} // check file type

		return TRUE;

	} // file exists

	return FALSE;		// RM 20030215 fix: return FALSE on error, not TRUE!
}


//*******************************************
function rig_select_random_album_icon($album)
//*******************************************
/*
	Selects, create and set a random icon for this album.
	Returns TRUE if the icon could be made succesfully, FALSE otherwise.
*/
{
	// build a list of pairs { a:$album, f:$file }
	$list = rig_build_recursive_list($album);
	$n = count($list);

	// if there's only one, don't use the random ;-)
	if ($n > 1)
	{
		// pick one random
		mt_srand((double) microtime() * 1000000);
		$n = mt_rand(0, $n-1);
	}
	else if ($n == 1)
	{
		$n = 0;
	}
	else
	{
		// if the list is empty, there's not much we can do
		return FALSE;
	}

	$item = $list[$n];

	// and use it as icon, if possible
	if (is_array($item))
		return rig_set_album_icon($album, $item['a'], $item['f']);

	return FALSE;
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.9  2003/06/30 06:08:11  ralfoide
//	Version 0.6.3.4 -- Introduced support for videos -- new version of rig_thumbnail.exe
//
//	Revision 1.8  2003/02/17 07:47:04  ralfoide
//	Debugging. Fixed album visibility not being used correctly
//	
//	Revision 1.7  2003/02/16 20:22:55  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.6  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.5  2002/10/22 22:32:52  ralfoide
//	Fix to prevent dup slashes in preview links
//	
//	Revision 1.4  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.3  2002/10/20 11:50:21  ralfoide
//	Misc fixes
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.3  2001/11/28 11:52:48  ralf
//	v0.6.1: display image last modification date
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//	
//-------------------------------------------------------------
?>
