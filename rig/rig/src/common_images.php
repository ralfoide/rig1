<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------



//**************************************************************
function make_image($abs_source, $abs_dest, $size, $quality = 0)
//**************************************************************
// returns the status code from the executable: 0=no error, 1-2-etc=error
{
	global $abs_preview_exec;
	global $pref_preview_timeout;
	global $pref_preview_quality;
	global $pref_global_gamma;

	// inform PHP this may take a while...
	if ($pref_preview_timeout)
		set_time_limit($pref_preview_timeout);

	# $args = "-r \"" . shell_filename($abs_source) . "\" \"" . shell_filename($abs_dest) . "\" $size";
	$args = "-r " . shell_filename($abs_source) . " " . shell_filename($abs_dest) . " $size";

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
	$res = system($abs_preview_exec . " " . $args, $retvar);

	// debug
	// echo "<br> res = $res\n";

	if ($retvar)
		html_error("Error $retvar during preview creation<br>Source: $abs_source<br>Dest: $abs_dest<br>Run string |$abs_preview_exec $args|)<br>");

	return $retvar;
}


//*****************************************************************
function build_preview_ex($album, $file,
						  $size = -1, $quality = -1,
						  $auto_create = TRUE, $use_default = TRUE)
//*****************************************************************
// Builds a resized version of the original $album->$file image
// Size and quality default to the preview size, unless specified
// auto_create ask the image to be created if no existing
// use_default ask the pref_empty_album name to be returned if the image cannot be build
//
// returns the name for the preview as an array { r:pref_path , a:abs_pref_path, p:image_path }
// Caller should thus use [0]+[2] or [1]+[2]
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
					 "p" => prep_sep($album) . prep_sep($file));
	}

	$prev_prefix = "prev" . $size . "_";
	$dest_file = $prev_prefix . simplify_filename($file);

	$dest		= prep_sep($album)  . prep_sep($dest_file);
	$abs_dest	= $abs_preview_path . $dest;
	$abs_source	= $abs_album_path   . prep_sep($album) . prep_sep($file);

	if (rig_is_file($abs_source) && !rig_is_file($abs_dest) && $auto_create)
	{
		if (make_image($abs_source, $abs_dest, $size, $quality) != 0)
		{
			// in case of error, use the default icon...
			if ($use_default)
				return array("r" => $dir_abs_album,
							 "a" => $dir_abs_album,
							 "p" => $dir_images . $pref_empty_album);
		}
	}

	return array("r" => $dir_preview,
				 "a" => $abs_preview_path,
				 "p" => $dest);
}


//**************************************************************
function build_preview($album, $file,
					   $size = -1, $quality = -1,
					   $auto_create = TRUE, $use_default = TRUE)
//**************************************************************
{
	$info = build_preview_ex($album, $file, $size, $quality, $auto_create, $use_default);

	return $info["r"] . $info["p"];
}


//*******************************************************************
function build_preview_info($album, $file,
							$size = -1, $quality = -1,
							$auto_create = TRUE, $use_default = TRUE)
//*******************************************************************
// returns an array:
// [p]=translated URL path
// [w]=width
// [h]=height
{
	$info = build_preview_ex($album, $file, $size, $quality, $auto_create, $use_default);

	// build the output array, fill in the first field, i.e. the URL path
	$res = array();
	$res["p"] = $info["r"] . $info["p"];

	// build the abs path
	// get the info
	$info = image_info($info["a"] . $info["p"]);

	// set the info and return the array
	$res["w"] = $info["w"];
	$res["h"] = $info["h"];

	return $res;
}



//****************************
function image_info($abs_file)
//****************************
// Returns an array of strings:
// { f:format, w:width, h:height, d:date }
{
	global $html_img_date;
	global $abs_preview_exec;

	$info = array();

	if (rig_is_file($abs_file))
	{
		// --- get the file's creation date ---

		$modified  = stat($abs_file);
		$info["d"] = date($html_img_date, $modified[9]);

		// --- use the thumbnail application to extract info ---

		$args = "-i " . shell_filename($abs_file) . "";

		// get the info now
		$res = exec($abs_preview_exec . " " . $args, $output, $retvar);

		if ($retvar == 127)
		{
			html_error("Error $retvar during image info<br>$abs_preview_exec doesn't exist<br>");
		}
		else if ($retvar)
		{
			html_error("Error $retvar during image info<br>$abs_preview_exec $abs_file<br>Error: $res<br>");
		}
		else
		{
			$info["f"] = $output[0];
			$info["w"] = $output[1];
			$info["h"] = $output[2];
		}
	}

	return $info;
}


//********************************
function build_info($album, $file)
//********************************
// Returns an array of strings:
// { format, width, height, date }
{
	global $abs_album_path;

	return image_info($abs_album_path . prep_sep($album) . prep_sep($file));
}


//*****************************************************
function get_album_preview($album, $use_default = TRUE)
//*****************************************************
{
	$abs_path = "";
	$url_path = "";
	build_album_preview($album, &$abs_path, &$url_path, $use_default);
	return $url_path;
}


//*******************************************************************************
function build_album_preview($album, &$abs_path, &$url_path, $use_default = TRUE)
//*******************************************************************************
// returns TRUE if album icon could be found, otherwise FALSE and returns the
// path to the default one
{
	global $dir_images;
	global $dir_abs_album;
	global $pref_empty_album;
	global $abs_preview_path, $dir_preview;

	$dest_file = prep_sep($album) . prep_sep(ALBUM_ICON);

	$abs_path = $abs_preview_path  . $dest_file;
	$url_path = $dir_preview . $dest_file;

	// if no file, try to build one
	if (!rig_is_file($abs_path))
	{
		// if this album has information about the icon, use it
		// make sure we have the correct album options
		if ($album != $current_album)
			read_album_options($album);

		global $list_album_icon;
		if ($list_album_icon && count($list_album_icon) > 1)
			set_album_icon($album, $list_album_icon[0], $list_album_icon[1], false);

		// read the current options back
		if ($album != $current_album)
			read_album_options($current_album);

		// if no info, try to make a default random one
		select_random_album_icon($album);
	}

	// if there's a file, just use that
	if (rig_is_file($abs_path) || !$use_default)
	{
		return TRUE;
	}

	// otherwise, use the default icon...
	$abs_path = realpath($dir_abs_album . $dir_images . $pref_empty_album);
	$url_path = $pref_empty_album;
}


//************************************************************************************
function set_album_icon($dest_album, $prev_album, $prev_image, $update_options = true)
//************************************************************************************
{
	global $abs_preview_path;

	// get its preview
	$preview = build_preview($prev_album, $prev_image, -1, -1, TRUE, FALSE);

	// if the preview actually exist...
	if ($preview && rig_is_file($preview))
	{
		// copy it as the album icon
		$abs_dest = $abs_preview_path . prep_sep($dest_album) . prep_sep(ALBUM_ICON);

		create_preview_dir($dest_album);

		if (!copy($preview, $abs_dest))
			html_error("Can't copy preview '$preview' to album icon '$abs_dest'!");

		// now keep that in the album options
		if ($update_options)
		{
			// make sure we have the correct album options
			read_album_options($dest_album);
	
			global $list_album_icon;
			$list_album_icon = array($prev_album, $prev_image);
	
			// write the options back
			write_album_options($dest_album, true);
		}
	}

}


//***************************************
function select_random_album_icon($album)
//***************************************
{
	// build a list of pairs (album, file)
	$list = build_recursive_list($album);
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
		// if the list is empty, there's no much we can do
		return;
	}

	$item = $list[$n];

	// and use it as icon, if possible
	if (is_array($item))
		set_album_icon($album, $item[0], $item[1]);
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
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
