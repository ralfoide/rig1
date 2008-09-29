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

	if (!$pref_follow_album_symlinks || !$current_album || !rig_is_dir($abs_dir)) {
		// we don't allow symlinks or this is not right. Don't block it but don't change it either.
		return TRUE;
	}

	if (is_link($abs_dir))
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

		return TRUE;
	} else {
		// FIXED by RM 20080928 for MM branch
		// abs_dir is not a symlink. but maybe one of its parents is

		$abs_parent = rig_get_parent_album($abs_dir);
		$leaf = substr($abs_dir, strlen($abs_parent));
		if (strlen($leaf) > 1 && ($leaf[0] == SEP || $leaf[0] == SEP2)) {
			$leaf = substr($leaf, 1);
		}

		$new_real_album = "";
		$result = rig_follow_album_symlink($abs_parent, $current_album, $new_real_album);

		if ($result) {
			// True here means either to follow a smylink (new_real_album) or to keep using
			// the original.
			if ($new_real_album != "") {
				$current_real_album = rig_post_sep($new_real_album) . $leaf;
			}
		} else {
			// False means access was denied on the parent level. Nothing to do.
		}

		return $result;
    }

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
		// RM 20051006 Don't show the $title part any 
		// $display_title = "$title - " . $pretty;
		$display_title = $pretty;
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

	// RM 20051006 Don't show the $title part any 
	// $display_title = $title . $pretty_image;
	$display_title = $pretty_image;

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



//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2005/10/07 06:18:28  ralfoide
//	Don't show "rig album - " or "rig image - " in titles.
//
//	Revision 1.1  2005/10/07 05:40:09  ralfoide
//	Extracted album/image handling from common into common_media.php.
//	Removed all references to obsolete db/id.
//	Added preliminary default image template.
//	
//	Revision 1.53  2005/10/05 03:55:20  ralfoide
//	Added new rig_is_debug method. Simply checks &_debug_ in query.
//	
//	Revision 1.52  2005/10/02 21:13:12  ralfoide
//	Invalidate html cache when templates modified.
//	
//	Revision 1.51  2005/10/01 23:44:27  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//	
//	Revision 1.50  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
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
