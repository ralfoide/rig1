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
	global $php_errormsg;			// RM 20040709 removes php_errormsg undeclared in PHP 4.3.7

	// inform PHP this may take a while...
	if ($pref_preview_timeout)
		set_time_limit($pref_preview_timeout);

	$args = "-r " . rig_shell_filename($abs_source)
		    . " " . rig_shell_filename($abs_dest)
		    . " $size";

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
	$res = @exec($abs_preview_exec . " " . $args, $output, $retvar);

	// debug
	// echo "<br> res = $res\n";

	// There was an error if the return code was != 0
	if ($retvar)
	{
		$desc = "(unknown)";
		if ($retvar == 3)
			$desc = "(timeout)";
		else if ($retvar == 2)
			$desc = "(invalid arguments)";
		else if ($retvar == 1)
			$desc = "(processing error)";
		
		rig_html_error("Create Thumbnail",
					   "Error $retvar $desc during image creation<p>" .
					   "<b>Source:</b> $abs_source<br>" .
					   "<b>Dest:</b> $abs_dest<br>" .
					   "<b>Size:</b> $size, <b>Quality:</b> $quality<br>" .
					   "<b>CWD:</b> " . getcwd() . "<br>" .
					   "<b>Exec:</b><br>&nbsp;&nbsp;|<i>$abs_preview_exec</i><br>&nbsp;&nbsp;$args|", 
					   $php_errormsg);
	}

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
	global $abs_image_cache_path;
	global $dir_abs_album;
	global $dir_album;
	global $dir_image_cache;
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
	$abs_dest	= $abs_image_cache_path . rig_prep_sep($dest);
	$abs_source	= $abs_album_path       . rig_prep_sep($album) . rig_prep_sep($file);

	if (rig_is_file($abs_source) && !rig_is_file($abs_dest) && $auto_create)
	{
		$retvar = rig_make_image($abs_source, $abs_dest, $size, $quality);
		
		if ($retvar != 0)
		{
			// The return code is 3 for a timeout, which means the next request
			// for the same thumbnail will generate one more timeout... so instead
			// in this very specific case we actually *copy* the default N/A icon
			// as the generated thumbnail
			if ($retvar == 3)
			{
				@copy(rig_post_sep($abs_images_path) . $pref_empty_album, $abs_dest);

				rig_html_error("Timeout Warning!",
							   "A timeout occured whilst generating the thumbnail." .
							   "<br>A default thumbnail will be used instead." .
							   "<br>This error should not occur again unless you erase all preview thumbnails from the cache."
							   );
			}

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

	return array("r" => $dir_image_cache,
				 "a" => $abs_image_cache_path,
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
// If size -1 is given, a thumbnail is created. The thumbnail is always a JPEG file as of today.
//
// returns the name for the preview as an array { r:pref_path , a:abs_pref_path, p:image_path }
// Caller should thus use [r]+[p] or [a]+[p]
{
	global $abs_album_path;
	global $abs_image_cache_path;
	global $abs_images_path;
	global $dir_abs_album;
	global $dir_album;
	global $dir_image_cache;
	global $dir_images;
	global $pref_preview_size;
	global $pref_empty_album;
	global $current_img_info;

	// debug
	// echo "size = $size<br>\n";

	// If size -1 is given, a thumbnail is created. The thumbnail is always a JPEG file as of today.
	$ext = "";
	if ($size == -1)
		$ext = ".jpg";

	// a size of 0 is a special argument: the original image size should be used
	// this does not apply to video, so the preview size is used anyway
	if ($size <= 0)
		$size = $pref_preview_size;

	$prev_prefix = "prev" . $size . "_";
	$dest_file = $prev_prefix . rig_simplify_filename($file);

	// the destination is appended a .jpg extension if a thumbnail is requested

	$dest		= $album . rig_prep_sep($dest_file) . $ext;
	$abs_dest	= $abs_image_cache_path . rig_prep_sep($dest);
	$abs_source	= $abs_album_path       . rig_prep_sep($album) . rig_prep_sep($file);

	if (rig_is_file($abs_source) && !rig_is_file($abs_dest) && $auto_create)
	{
		$retvar = rig_make_image($abs_source, $abs_dest, $size, $quality);
		
		if ($retvar != 0)
		{
			// The return code is 3 for a timeout, which means the next request
			// for the same thumbnail will generate one more timeout... so instead
			// in this very specific case we actually *copy* the default N/A icon
			// as the generated thumbnail
			if ($retvar == 3)
			{
				$timeout_image = "timeout.jpg";
				$source = rig_post_sep($abs_images_path) . $timeout_image;
				
				$copyok = copy($source, $abs_dest);

				if ($copyok)
				{
					rig_html_error("Timeout Warning!",
								   "A timeout occured whilst generating the thumbnail." .
								   "<br>A default thumbnail will be used instead." .
								   "<br>This error should not occur again unless you erase all preview thumbnails from the cache."
								   );
				}
				else
				{
					rig_html_error("Error accessing $timeout_image",
								   "Error whilst trying to copy a file:<p>" .
								   "<b>Source:</b>$source<br>" .
								   "<b>Dest:</b>$abs_dest<br>",
								   $source,
								   $php_errormsg);
				}
			}

			// in case of error, use the default icon...
			// RM 20030628 fix: the fixed image in /images/ not in the album's root
			if ($use_default)
			{
				// RM 20030628 fix: the fixed image in /images/ not in the album's root
				return array("r" => $dir_images,
							 "a" => $abs_images_path,
							 "p" => $pref_empty_album);
			}
		}
	}

	return array("r" => $dir_image_cache,
				 "a" => $abs_image_cache_path,
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

	return NULL;
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
// { f:format, w:width, h:height, d:date, e:extra }
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
		$info["d"] = strftime($html_img_date, $modified[9]);

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
					// The line we're looking for as the following format:
					//
					// [rig-thumbnail-result] type width height @extra@\n
					//
					// - type is a string: current accepted values are "jpeg", "video" and "unknown".
					// - width & height are decimal integer pixel size.
					// - @...@: everything between a pair of @ is taken as an extra string [RM 20031109 v0.6.4.4]
					// The meaning of the extra string depends on the type:
					// - for images: not currently used
					// - for video: FourCC codec type
					// The extra string is optional and it can be empty.
					
					if (preg_match("/\[rig-thumbnail-result\][ \t]+([a-z]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]*(@[^@]*@)?/", $output[$i], $res) > 0)
					{
						// $res[0] contains the full line, 1/2/3/4 contain the several matches
						$info["f"] = $res[1];
						$info["w"] = $res[2];
						$info["h"] = $res[3];
						
						// RM 20040602 check res[4] is set before accessing it
						if (isset($res[4]) && is_string($res[4]) && preg_match("/@([^@]*)@/", $res[4], $extra) > 0)
							$info["e"] = $extra[1];
						else
							$info["e"] = '';

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
	rig_build_album_preview($album, $abs_path, $url_path, -1, -1, $use_default);
	return $url_path;
}


//***********************************************************************************
function rig_build_album_preview($album, &$abs_path, &$url_path,
								 $size = -1, $quality = -1,
								 $use_default = TRUE, $check_icon_properties = FALSE)
//***********************************************************************************
// Returns TRUE if album icon could be found, otherwise FALSE and returns the
// path to the default one
// RM 20030125: added check_icon_properties to rebuild the icon if necessary
// RM 20030720: an preview with a non-default size/quality can be requested, in which case
// the album's default preview is still the standard size but a copy is returned with a 
// different size.
{
	global $dir_images;
	global $dir_abs_album;
	global $pref_empty_album;
	global $pref_preview_size;
	global $abs_album_path;
	global $abs_image_cache_path;
	global $dir_image_cache;
	global $current_real_album;			// RM 20030907

	// DEBUG
	/*
	echo "<br><b>Current (real) album:</b> $album -- $current_real_album\n";
	echo "<br><b>dir_abs_album:</b> $dir_abs_album\n";
	echo "<br><b>dir_images:</b> $dir_images\n";
	echo "<br><b>abs_path:</b> $abs_path\n";
	echo "<br><b>url_path:</b> $url_path\n";
	echo "<br><b>use_default:</b> $use_default\n";
	$check_icon_properties=TRUE;
	echo "<br><b>check_icon_properties:</b> $check_icon_properties\n";
	*/

	// the root album does not have any preview [RM 20030728]
	if ($album != '')
	{
		// memorize if the global album options will have been changed
		$album_options_changed = FALSE;
		// by default, don't re create icons
		$create_icon = FALSE;
	
		// the target file this is all about
		$dest_file = rig_post_sep($album) . ALBUM_ICON;
	
		$abs_path = rig_post_sep($abs_image_cache_path) . $dest_file;
		$url_path = rig_post_sep($dir_image_cache)  . $dest_file;
	
		// -1- perform various checks
	
		if (!rig_is_file($abs_path))
		{
			// if there is no icon, we need to build one
			$create_icon = TRUE;
		}
		else if ($check_icon_properties || ($size != -1 && $size != $pref_preview_size))
		{
			// check several properties of the icon: the file's date, the size, etc.
	
			// try to get the size of the _existing_ album icon
			$s = 0;
			$info = rig_image_info($abs_path);
			if (is_array($info) && isset($info['w']) && isset($info['h']))
				$s = max($info['w'], $info['h']);
	
			if ($s == 0)
			{
				if ($admin)
					echo "<br>Album icon <font color=red>does not exist!</font>\n";
	
				$create_icon = TRUE;
			}
			else if ($s != $pref_preview_size)
			{
				if ($admin)
					echo "<br>Album icon needs to be rebuild: does not have preview size\n";
	
				// need to create if size is not up-to-date
				$create_icon = TRUE;
			}
			else
			{
				global $list_album_icon;

				// now check the date of the album icon vs the original one
	
				// first the existing album icon, aka the "destination" file
				$date_dest = filemtime($abs_path);

				// if this album has information about the icon, use it
				// make sure we have the correct album options
				if ($album != $current_real_album)
					$album_options_changed = rig_read_album_options($album);

				// now get the name of the source of the icon
				// fix: if $album is not the current album, the album's icon may be of a relative
				// path that is already specified in $album, and thus must not be listed again.
				$a = $list_album_icon['a'];
				$f = $list_album_icon['f'];
				if (substr($album, -1*strlen($a)) == $a)
					$source_file = $abs_album_path . rig_prep_sep($album) . rig_prep_sep($f);
				else
					$source_file = $abs_album_path . rig_prep_sep($album) . rig_prep_sep($a) . rig_prep_sep($f);
	
				$admin = rig_get($_GET, 'admin', false);
	
				// if the source is newer or does not exist, icon needs to be updated
				if (!rig_is_file($source_file))
				{
					$create_icon = TRUE;
					if ($admin)
					{
						echo '<br>Album icon needs to be rebuild: source file does not exist';
						echo '<br><b>File:</b> '          . $source_file;
						echo '<br><b>Current album:</b> ' . $album;
						echo '<br><b>Icon album:</b> '    . $a;
						echo '<br><b>Icon name:</b> '	  . $f;
					}
				}
				else if (filemtime($source_file) > $date_dest)
				{
					$create_icon = TRUE;
					if ($admin)
					{
						echo '<br>Album icon needs to be rebuild: source file is newer!';
						echo '<br><b>File:</b> '          . $source_file;
						echo '<br><b>Current album:</b> ' . $album;
						echo '<br><b>Icon album:</b> '    . $a;
						echo '<br><b>Icon name:</b> '	  . $f;
					}
				}
			}
		}
	
		// -2- create icon as requested
	
		if ($create_icon)
		{
			// if this album has information about the icon, use it
			// make sure we have the correct album options
			if ($album != $current_real_album && !$album_options_changed)
				$album_options_changed = rig_read_album_options($album);
	
			global $list_album_icon; // array of icon info { a:album(relative) , f:file, s:size }
			if (is_array($list_album_icon) && is_string($list_album_icon['f']))
				$create_icon = !rig_set_album_icon($album, $album . rig_prep_sep($list_album_icon['a']), $list_album_icon['f'], FALSE);
	
			// if previous didn't work, try to make a default random one
			if ($create_icon)
				rig_select_random_album_icon($album);
		}
	
		// -3- If a non-default size is requested, create the preview now
		if ($size != -1 && $size != $pref_preview_size)
		{
			// if this album has information about the icon, use it
			// make sure we have the correct album options
			if ($album != $current_real_album && !$album_options_changed)
				$album_options_changed = rig_read_album_options($album);
	
			global $list_album_icon; // array of icon info { a:album(relative) , f:file, s:size }
	
			if (is_array($list_album_icon) && is_string($list_album_icon['f']))
			{
				$info = rig_build_preview_ex(rig_post_sep($album) . $list_album_icon['a'],
											 $list_album_icon['f'],
											 $size, $quality);
				$abs_path = rig_post_sep($info['a']) . $info['p'];
				$url_path = rig_post_sep($info['r']) . $info['p'];
	  		}
		}
	
		// read the current options back if changed
		if ($album_options_changed)
			rig_read_album_options($current_real_album, $check_icon_properties);
	
		// if there's a file, just use it
		if (rig_is_file($abs_path) || !$use_default)
		{
			return TRUE;
		}
	} // album <> ''

	// otherwise, use the default icon...
	$abs_path = realpath($dir_abs_album . $dir_images . $pref_empty_album);
	$url_path = $dir_images . $pref_empty_album;	// RM 20020713 fix missing dir_images

	return FALSE;
}


//****************************************************************
function rig_set_album_icon($dest_album, $prev_album, $prev_image,
							$update_options = TRUE)
//****************************************************************
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
	global $abs_image_cache_path;

	// get its preview
	$preview = rig_build_preview($prev_album, $prev_image, -1, -1, TRUE, FALSE);

	// if the preview actually exist
	if ($preview && rig_is_file($preview))
	{
		// if preview is of a known type...
		if (rig_get_file_type($preview) != "")
		{
			// copy it as the album icon
			$abs_dest = $abs_image_cache_path . rig_prep_sep($dest_album) . rig_prep_sep(ALBUM_ICON);
	
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
				global $list_album_icon;	// array of icon info { a:album(relative), f:file, s:size }
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

	// DEBUG
	// echo "<br>Randomize: $n";


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

	// DEBUG
	// echo "==&gt; $n<br>";

	$item = $list[$n];

	// DEBUG
	// var_dump($item);

	// and use it as icon, if possible
	if (is_array($item))
		return rig_set_album_icon($album, $item['a'], $item['f']);

	return FALSE;
}


//*************************************
function rig_runtime_filetype_support()
//*************************************
// Returns an array suitable for $pref_file_types or NULL
// Uses rig_thumbnail.exe with flag -f [new RM 20030807]
{
	global $abs_preview_exec;

	$args = "-f";

	// gather filetype output now
	// RM 20030628 using exec instead of system (system's output goes directly in the HTML!)
	$res = @exec($abs_preview_exec . " " . $args, $output, $retvar);

	// output should be an even number of lines

	$filetypes = NULL;
	$n = count($output);

	if ($retvar || !is_array($output) || $n == 0 || ($n % 2) != 0)
	{
		rig_html_error("Runtime File Type Array Error",
					   "Error $retvar when collecting supported file type information<p>" .
					   "<b>CWD:</b> " . getcwd() . "<br>" .
					   "<b>Exec:</b><br>&nbsp;&nbsp;|<i>$abs_preview_exec</i><br>&nbsp;&nbsp;$args|<br>" .
					   "<b>Error:</b>$php_errormsg",
					   $output);
	}
	else
	{
		$filetypes = array();
		$i = 0;
		while($n >= 2)
		{
			$filetypes[$output[$i]] = $output[$i+1];
			$i += 2;
			$n -= 2;
		}
	}	

	return $filetypes;
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.19  2004/07/14 06:19:13  ralfoide
//	Minor fixes for Win32/PHP 4.3.7 support
//
//	Revision 1.18  2004/07/09 05:52:06  ralfoide
//	Handling of timeout in thumbnail creation
//	
//	Revision 1.17  2004/06/03 14:14:47  ralfoide
//	Fixes to support PHP 4.3.6
//	
//	Revision 1.16  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.15  2003/11/25 05:05:34  ralfoide
//	Version 0.6.4.4 started.
//	Added video install codec/player link & codec info.
//	Isolated video display routines in new source file.
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
