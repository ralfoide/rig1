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

// Administration Routines

require_once($dir_abs_src . "common.php");

//-------------------------------------------------------------

//*********************************************************
function rig_admin_perform_before_header($refresh_url = "")
//*********************************************************
{
	global $current_album;

	if (rig_get($_GET, 'admin') == "rand_prev")
	{
		rig_select_random_album_icon($current_album);
	}
	else
	{
		return;
	}

	if ($refresh_url)
	{
		header("Location: $refresh_url");
		exit;
	}
}



//********************************
function rig_admin_perform_defer()
//********************************
{
	global $current_album;
	global $current_image;

	$item		= rig_get($_GET,'item'		);
	$admin		= rig_get($_GET,'admin'		);
	$show_album	= rig_get($_GET,'show_album');
	$show_image	= rig_get($_GET,'show_image');



	// DEBUG
	// echo "admin defer: admin = '$admin' -- album = '$current_album' -- image = '$current_image'<br>\n";

//------
	if ($admin == "fix_option")
	{
		rig_admin_fix_options($current_album);			// non recursive version
	}
	else if ($admin == "fix_options")
	{
		rig_admin_fix_all_options($current_album);	// recursive version
	}
//-------
	else if ($admin == "mk_previews")
	{
		rig_admin_mk_preview($current_album, TRUE, FALSE);
	}
	else if ($admin == "mk_images")
	{
		rig_admin_mk_preview($current_album, FALSE, TRUE);
	}
	else if ($admin == "mk_prev_img")
	{
		rig_admin_mk_preview($current_album, TRUE, TRUE);
	}
	else if ($admin == "rm_previews")
	{
		rig_admin_rm_previews($current_album, TRUE, FALSE);
	}
	else if ($admin == "rm_images")
	{
		rig_admin_rm_previews($current_album, FALSE, TRUE);
	}
	else if ($admin == "rm_prev_img")
	{
		rig_admin_rm_previews($current_album, TRUE, TRUE);
	}
	else if ($admin == "rm_html_caches")
	{
		rig_admin_rm_html_caches($current_album);
	}
	else if ($admin == "rnm_canon")
	{
		rig_admin_rename_canon($current_album);
	}
	else if ($admin == "set_icon" && $current_album && $current_image)
	{
		echo "Changing icon for album...<br>";
		rig_set_album_icon($current_album, $current_album, $current_image);
	}
	else if ($admin == "show_album" && $show_album && $item)
	{
		// RM 20041005 url-decode item
		$item = rig_decode_argument($item);
		rig_admin_set_album_visible($item, ($show_album == 'on'));
	}
	else if ($admin == "show_image" && $show_image && $item)
	{
		// RM 20041005 url-decode item
		$item = rig_decode_argument($item);
		// RM 20021022 fix for changing image visibility
		rig_admin_set_image_visible($item, ($show_image == 'on'));
	}
}

//-------------------------------------------------------------


//*****************************************
function rig_admin_mk_preview($album,
							  $do_previews,
							  $do_images)
//*****************************************
// RM 20020712 support for only previews or images
{
	global $abs_album_path;
	global $pref_image_size;
	global $pref_image_quality;
	global $pref_preview_timeout;

	global $pref_album_ignore_list;     // RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;


	$abs_dir = $abs_album_path . rig_prep_sep($album);

	// echo "<hr width=\"50%\">\n";

	// -1- get all files and dirs, process local files and then recurse in sub directories
	$dir_list = array();

	echo "<p><center>Creating ";
	if ($do_previews) echo "Previews ";
	if ($do_previews && $do_images) echo "and ";
	if ($do_images) echo "Images ";
	echo " for <b>$album</b></center><p><code>\n";

	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		echo "</code>\n";
		return rig_html_error("Admin: Create Previews", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		// inform PHP this may take a while...
		if ($pref_preview_timeout)
			set_time_limit($pref_preview_timeout);

		rig_create_preview_dir($album);

		$start_table = TRUE;

		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					if (!rig_check_ignore_list($file, $pref_album_ignore_list))			// RM 20030814
					{
						// process directories after files
						$dir_list[] = $file;
					}
				}
				else if (!rig_check_ignore_list($file, $pref_image_ignore_list) && rig_valid_ext($file))	// RM 20030814
				{
					// image exists, create an id if not done yet
					// RM 20021021 not for rig 062

					if ($do_previews)
					{
						$t = rig_getmicrotime();
						rig_build_preview_info($album, $file);
						$t1 = rig_getmicrotime() - $t;
					}
					else
					{
						$t1 = 0;
					}

					if ($do_images)
					{
						$t = rig_getmicrotime();
						rig_build_preview_info($album, $file, $pref_image_size, $pref_image_quality);
						$t2 = rig_getmicrotime() - $t;
					}
					else
					{
						$t2 = 0;
					}

					if ($start_table)
					{
						echo "&nbsp;Preview";
						echo "\t&nbsp;|&nbsp;\t";
						echo "&nbsp;Image&nbsp;&nbsp;";
						echo "\t&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;\tFilename<br>\n";
						$start_table = FALSE;
					}

					if ($t1 > 0.01)
						echo str_replace(" ", "&nbsp;", sprintf("% 3.2f s", $t1));
					else
						echo "&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "\t&nbsp;|&nbsp;\t";
					if ($t2 > 0.01)
						echo str_replace(" ", "&nbsp;", sprintf("% 3.2f s", $t2));
					else
						echo "&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;";
					echo "\t&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;\t$file<br>\n";
					rig_flush();
			    } // if file
			} // if not . or ..
		} // while readdir

		closedir($handle);

	}

	echo "</code><center>\n";

	// -2- make sure the album's icon is up-to-date
	if ($do_previews)
	{
		$abs_path = "";
		$url_path = "";

		rig_build_album_preview($album, $abs_path, $url_path, -1, -1, TRUE, TRUE);
	}

	echo "<p>Done for <i>$album</i><hr></center><p>\n";
	rig_flush();

	// -3- process sub directories now

	if (is_array($dir_list) && count($dir_list) > 0)
	{
		foreach($dir_list as $file)
		{
			$name = rig_post_sep($album) . $file;
			rig_admin_mk_preview($name, $do_previews, $do_images);
		}
	}
}


//******************************************
function rig_admin_rm_previews($album,
							   $do_previews,
							   $do_images)
//******************************************
// RM 20030120 [0.6.3] support for only previews or images
//
// Important: RIG doesn't have different names for thumbnail previews
// and resized images. Here "do_preview" means to remove only thumbnail
// previews. Since there's no garantee a file is a thumbnail, the size is
// used: if the resized size is equal or smaller than the current thumbnail
// size then this is a thumbnail preview and it is erase by do_previews.
{
	global $pref_preview_size;
	global $abs_image_cache_path;

	global $pref_album_ignore_list;     // RM 20030813 - v0.6.3.5
	global $pref_image_ignore_list;
		

	$abs_dir = $abs_image_cache_path . rig_prep_sep($album);

	// tell php this may take a while...
	// (30 s is php's default for the script processing. I allow 30s
	// per directory, which is a lot)
	set_time_limit(30);

	echo "<center>Deleting Previews for <b>$album</b><p><code>\n";

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$n = 0;
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		rig_html_error("Admin: Delete Previews", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					if (!rig_check_ignore_list($file, $pref_album_ignore_list))            // RM 20030814
					{
						$name = rig_post_sep($album) . $file;
						echo "</code></center>\n";
						rig_admin_rm_previews($name, $do_previews, $do_images);
						echo "<center><code>\n";
					}
				}
				// the pattern for previews is "prevSize_SimplifiedFileName"
				else if (eregi("^prev([0-9]+)_", $file, $regs)
						 && (   ($do_previews && $do_images)
						 	 || ($do_previews && ($regs[1] <= $pref_preview_size))
						 	 || ($do_images   && ($regs[1] >  $pref_preview_size))))
				{
					// should we erase them all or differentiate?
					if (!$do_previews || !$do_images)
					{
						// get the size
					}

					echo "$file<br>\n";
					rig_flush();
					unlink($abs_file);
			    }
				else
				{
					// there is an item we did not delete
					$n++;
				}
			}
		}

		closedir($handle);
	}

	// remove the previews directory, if empty
	// the root is never removed
	if ($album && !$n)
	{
		// this will fail if the directory is not empty, which may happen
		if (@rmdir($abs_dir))
			echo "'$abs_dir' Deleted";
	}

	echo "</code><p>Done<hr></center><p>\n";
	rig_flush();
}




//***************************************
function rig_admin_rm_html_caches($album)
//***************************************
// RM 20040711 Recursively remove all HTML Caches
{
	$notice = true;
	
	global $abs_image_cache_path;

	$abs_dir = $abs_image_cache_path . rig_prep_sep($album);

	// tell php this may take a while...
	// (30 s is php's default for the script processing. I allow 30s
	// per directory, which is a lot)
	set_time_limit(30);

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$n = 0;
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		rig_html_error("Admin: Delete HTML Caches", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					if (!rig_check_ignore_list($file, $pref_album_ignore_list)) // RM 20030814
					{
						$name = rig_post_sep($album) . $file;
						rig_admin_rm_html_caches($name);
					}
				}
				// the pattern for previews is "prevSize_SimplifiedFileName"
				else if (preg_match("/^" . ALBUM_CACHE_NAME . ".*" . ALBUM_CACHE_EXT . "$/", $file) == 1)
				{
					if ($notice)
					{
						echo "Deleting HTML Caches for <b>$album</b><p>\n";
						$notice = false;
						rig_flush();
					}

					echo "$file<br>\n";
					unlink($abs_file);
			    }
				else
				{
					// there is an item we did not delete
					$n++;
				}
			}
		}

		closedir($handle);
	}

	// remove the previews directory, if empty
	// the root is never removed
	if ($album && !$n)
	{
		// this will fail if the directory is not empty, which may happen
		if (@rmdir($abs_dir))
			echo "'$abs_dir' Deleted<p>";
	}

	rig_flush();
}


//****************************************
function rig_admin_fix_all_options($album)
//****************************************
// RM 20030120 old options.txt files are buggy
{
	global $abs_image_cache_path;
	global $pref_album_ignore_list;

	$abs_dir = $abs_image_cache_path . rig_prep_sep($album);

	// get all files and dirs, recurse in dirs first
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		rig_html_error("Admin: Remove Options",
					   "Failed to open album directory, probably does not exist!",
					   $abs_dir,
					   $php_errormsg);
	}
	else
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file) && !rig_check_ignore_list($file, $pref_album_ignore_list))
				{
					$name = rig_post_sep($album) . $file;
					rig_admin_fix_all_options($name);
				}
			}
		}

		closedir($handle);
	}


	//----------------

	// fix the options for this album
	rig_admin_fix_options($album);

	//----------------

	echo "<p>Done<hr><p>\n";
}


//************************************
function rig_admin_fix_options($album)
//************************************
// RM 20030120 old options.txt files are buggy
{
	echo "<p>Fixing Options for <b>$album</b>\n";

	//----------------

	// check that the target album still exists
	// do not process non-existing albums

	global $abs_album_path;
	$abs_dir = $abs_album_path . rig_prep_sep($album);

	if (!rig_is_dir($abs_dir))
	{
		echo "<br>Album no longer exists\n";
		return FALSE;
	}

	//----------------

	// get the options for this album
	rig_read_album_options($album);

	//----------------

	// update the image/album list for this album
	// HACK using global variable $current_album
	global $current_album;
	$copy_current_album = $current_album;
	$current_album = $album;

	rig_load_album_list(TRUE);	// ask to load everything

	// restore global $current_album
	$current_album = $copy_current_album;

	//----------------

	// we have the list of files and sub-albums for this album
	// process the options

	//----------------

	// fix list_hide

	global $list_hide;
	global $list_albums;
	global $list_images;

	if (is_array($list_hide))
	{
		foreach($list_hide as $key => $item)
		{
			$a = -1;
			$i = -1;
			if (is_array($list_albums))
				$a = array_search($item, $list_albums);
			if (is_array($list_images))
				$i = array_search($item, $list_images);
	
			if (!(is_int($a) && $a >= 0) && !(is_int($i) && $i >= 0))
			{
				echo "<br>Reject hidden item '$item'\n";
				unset($list_hide[$key]);
			}
		}
	}

	echo "<p>";

	//----------------

	// convert list_album_icon to new format

	global $list_album_icon;
	global $pref_preview_size;

	echo "<p><b>Icon</b>: "; var_dump($list_album_icon);


	// old list_album_icon used a 2-values array: full album path + image name
	// the new format is a named array with relative album path + image + size
	// new array of icon info { a:album(relative) , f:file, s:size }

	if (is_array($list_album_icon) && count($list_album_icon) == 2)
	{
		
		$a = $list_album_icon['a'];	if (!is_string($a)) $a = $list_album_icon[0];
		$f = $list_album_icon['f'];	if (!is_string($f)) $f = $list_album_icon[1];
		$s = $list_album_icon['s'];	if (!is_string($s) && !is_int($s)) $s = $pref_preview_size;
		
		if (is_string($a) && $a != "" && $a[0] != '/')
		{
			/*
			// this case is specific to my public album
			if (strcmp($album, "Public" . rig_prep_sep($a)) == 0)
				$a = "";
			else if (ereg("^Public/([^/]*).*$", $album, $reg1) && ereg("^" . $reg1[1] . "(/.*)$", $a, $reg2))
				$a = $reg2[1];
			*/

			// get prev_album relative to dest_album
			$a = str_replace($album, "", $a);
		}

		// try to get the size of the _existing_ album icon
		global $abs_image_cache_path;
		$info = rig_image_info($abs_image_cache_path . rig_prep_sep($album) . rig_prep_sep(ALBUM_ICON));
		if (is_array($info) && isset($info['w']) && isset($info['h']))
		{
			// get the max size
			$s = max($info['w'], $info['h']);
		}

		// recreate the array
		$list_album_icon = array('a' => $a,
								 'f' => $f,
								 's' => $s);
	}

	echo "<br>"; var_dump($list_album_icon);

	//----------------

	// write the options
	rig_write_album_options($album);

	//----------------
	// invalidate albums & images lists (they contain visible images)
	
	unset($GLOBALS['list_albums']);
	unset($GLOBALS['list_images']);

	//----------------

	echo "<p>Done<hr><p>\n";

	return TRUE;
}


//*************************************
function rig_admin_rename_canon($album)
//*************************************
{
	global $abs_album_path;
    global $pref_image_ignore_list;
	
	$abs_dir = $abs_album_path . rig_prep_sep($album);

	echo "Renaming Canon 100-1234_IMG.JPG for <b>$album</b><p>\n";

	// get all files in this dir
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		rig_html_error("Admin: Rename Canon Files", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);

				if (!rig_check_ignore_list($file, $pref_image_ignore_list) && rig_is_file($abs_file))
				{
					if (eregi("^([0-9]+)[- _]([0-9]+)[- _]img\.jpg$", $file, $reg))
					{
						$dest = "$reg[2].jpg";
	
						echo "Renaming '$file' --> '$dest'<br>\n";

						$abs_dest = $abs_dir . rig_prep_sep($dest);
						rename($abs_file, $abs_dest);
				    }
					else
					{
						echo "Ignoring '$file'<br>\n";
					}
				}
			}
		}

		closedir($handle);
	}

	echo "<p>Done<hr><p>\n";
}


//***********************************************************
function rig_admin_recurse_previnfo($album, &$nb, &$nf, &$sz)
//***********************************************************
{
	global $abs_image_cache_path;

	global $pref_album_ignore_list;     // RM 20030813 - v0.6.3.5
    global $pref_image_ignore_list;
	
	$abs_dir = $abs_image_cache_path . rig_prep_sep($album);

	// we're processing one more directory
	$nf++;

	// get all files and dirs, recurse in dirs first
	$handle = @opendir($abs_dir);
	if ($handle)
	{
		while (($file = readdir($handle)) !== FALSE)
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					if (!rig_check_ignore_list($file, $pref_album_ignore_list)) // RM 20030814
					{
						$name = rig_post_sep($album) . $file;
						rig_admin_recurse_previnfo($name, $nb, $nf, $sz);
					}
				}
				else if (!rig_check_ignore_list($file, $pref_image_ignore_list) && rig_valid_ext($file)) // RM 20030814
				{
					$nb++;
					$sz += filesize($abs_file);
			    }
			}
		}
		closedir($handle);
	}
}



//****************************************************
function rig_admin_set_album_visible($album, $visible)
//****************************************************
{
	global $list_hide;
	global $current_album;

	echo "set album: '$album' visible: " . ($visible ? "Yes" : "No") . "<br>\n";

	if (!$album)
		return;

	if ($visible && !rig_is_visible(-1, $album))
	{
		// remove the name from the hide list
		foreach($list_hide as $key => $value)
		{
			if ($value == $album)
			{
				unset($list_hide[$key]);
				break;
			}
		}

		rig_write_album_options($current_album);
	}
	else if (!$visible && rig_is_visible(-1, $album))
	{
		// add the name to the hide list
		$list_hide[] = $album;
		rig_write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	rig_read_album_options($current_album);
}


//****************************************************
function rig_admin_set_image_visible($image, $visible)
//****************************************************
{
	global $list_hide;
	global $current_album;

	echo "set image: '$image' visible: " . ($visible ? "Yes" : "No") . "<br>\n";

	if (!$image)
		return;

	if ($visible && !rig_is_visible(-1, -1, $image))
	{
		// remove the name from the hide list
		foreach($list_hide as $key => $value)
		{
			if ($value == $image)
			{
				unset($list_hide[$key]);
				break;
			}
		}

		rig_write_album_options($current_album);
	}
	else if (!$visible && rig_is_visible(-1, -1, $image))
	{
		// add the name to the hide list
		$list_hide[] = $image;
		rig_write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	rig_read_album_options($current_album);
}


//*****************************************
function rig_admin_get_preview_info($album)
//*****************************************
// Result array:
// [nfil] = nb_files,
// [ndir] = nb_directories
// [size] = nb_bytes (size)
{
	$nf = 0;
	$nd = 0;
	$sz = 0;
	rig_admin_recurse_previnfo($album, $nf, $nd, $sz);

	$res = array("nfil" => $nf,
				 "ndir" => $nd,
				 "size" => $sz);

	return $res;
}


//************************************************
function rig_admin_display_album_stat($html, $res)
//************************************************
{
	global $html_num_dec_sep, $html_num_th_sep;

	$size = number_format($res["size"], 0, $html_num_dec_sep, $html_num_th_sep);
	$nfil = number_format($res["nfil"], 0, $html_num_dec_sep, $html_num_th_sep);
	$ndir = number_format($res["ndir"], 0, $html_num_dec_sep, $html_num_th_sep);

	$s = str_replace("[bytes]",   $size, $html);
	$s = str_replace("[files]",   $nfil, $s);
	$s = str_replace("[folders]", $ndir, $s);

	echo $s;
}


//-----------------------------------------------------------


//********************************
function rig_admin_display_album()
//********************************
{
	global $pref_album_nb_col;
	global $current_album;
	global $list_hide;
	global $list_albums;
	global $html_options, $html_album;
	global $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $color_section_bg;
	global $color_warning_bg;

	$i = 0;
	$n = $pref_album_nb_col;

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_albums as $key => $dir)
	{
		$name = rig_post_sep($current_album) . $dir;
		$pretty = rig_pretty_name($dir, FALSE);
		$preview = rig_get_album_preview($name); // url is properly url-escaped

		if (rig_is_visible(-1, $dir))
		{
			$visible = $html_vis_off;
			$vis_val = "off";
			$header_color = $color_section_bg;
		}
		else
		{
			$visible = $html_vis_on;
			$vis_val = "on";
			$header_color = $color_warning_bg;
		}		

		// link to change album visibility
		$vis_link = rig_self_url(-1, -1, RIG_SELF_URL_ADMIN, "admin=show_album&item=$dir&show_album=$vis_val#$key");
	?>
			<td <?= $w ?>>
			<center>

				<?php
					rig_display_section("<a name=\"$key\">$html_album<br><b>$pretty</b></a>\n",
										$header_color);
				?>

				<br>

				<a href="<?= rig_self_url("", $name, RIG_SELF_URL_ADMIN) ?>"><img src="<?= $preview ?>" alt="<?= $dir ?>" border="1" ></a>
				<br>

				<a href="<?= $vis_link ?>">
					<?= $visible ?>
				</a>

			</center>
			</td>
		<?php

		$i++;
		if ($i >= $n)
		{
			echo "</td></tr><tr>\n";
			$i = 0;
		}
		else
		{
			echo "</td>\n";
		}
	}

	echo "</tr>\n";
}


//********************************
function rig_admin_display_image()
//********************************
{
	global $pref_image_nb_col;
	global $current_album;
	global $list_images;
	global $list_images_count;		// RM 20030125
	global $html_options, $html_image;
	global $html_use_as_icon;
	global $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $color_section_bg;
	global $color_warning_bg;

	$list_images_count = 0;
	
	$i = 0;
	$n = $pref_image_nb_col;

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_images as $key => $file)
	{
		$pretty = rig_pretty_name($file, FALSE);
		$preview = rig_build_preview_info($current_album, $file, -1, -1, FALSE);
		$preview = $preview["u"];

		// count visible images
		$list_images_count++;

		if (rig_is_visible(-1, -1, $file))
		{
			$visible = $html_vis_off;
			$vis_val = "off";
			$header_color = $color_section_bg;
		}
		else
		{
			$visible = $html_vis_on;
			$vis_val = "on";
			$header_color = $color_warning_bg;
		}

		// link to change image visibility
		// RM 20041005 url-encode the item name
		$item = rig_encode_url_link($file);
		// RM 20021022 fix for changing image visibility
		$vis_link = rig_self_url(-1, -1, RIG_SELF_URL_ADMIN, "admin=show_image&item=$item&show_image=$vis_val#$key");

		?>
			<td <?= $w ?>>
			<center>

				<?php
					rig_display_section("<a name=\"$key\">$html_image<br><b>$pretty</b></a>\n",
										$header_color);
				?>

				<br>

				<font size="-1">
					<a href="<?= rig_self_url($file, -1, TRUE, "#$key") ?>"><img src="<?= $preview ?>" alt="<?= $file ?>" border="1" ></a>
					<br>
	
					<a href="<?= rig_self_url($file, -1, TRUE, "admin=set_icon#$key") ?>">
						<?= $html_use_as_icon ?>
					</a>
					<br>
	
					<a href="<?= $vis_link ?>" target="_top">
						<?= $visible ?>
					</a>
				</font>

			</center>
			</td>
		<?php

		$i++;
		if ($i >= $n)
		{
			echo "</td></tr><tr>\n";
			$i = 0;
		}
		else
		{
			echo "</td>\n";
		}
	}

	echo "</tr>\n";
}


//-------------------------------------------------------------



//************************************
function rig_admin_insert_icon_popup()
//************************************
{
	global $current_album;
	global $html_root;

	// split the path into its components
	$list = explode(SEP, $current_album);

	// the last item is the current album name
	$n = count($list)-1;

	// remove the last item
	unset($list[$n]);

	echo "<option value='0'>[Select an album name]</option>\n";

	foreach($list as $key => $item)
	{
		echo "<option value='$key'>$item</option>\n";
	}
}


//-------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.8  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//
//	Revision 1.7  2005/09/25 22:36:14  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.6  2004/10/07 01:20:01  ralfoide
//	Fix for encoding in admin url
//	
//	Revision 1.5  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.4  2004/07/14 06:08:34  ralfoide
//	Clean html caches
//	
//	Revision 1.3  2004/03/09 06:22:29  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.2  2003/09/13 21:55:54  ralfoide
//	New prefs album nb col vs image nb col, album nb row vs image nb row.
//	New pagination system (several pages for image/album grids if too many items)
//	
//	Revision 1.1  2003/08/21 20:15:32  ralfoide
//	Moved admin src into separate folder
//	
//	Revision 1.15  2003/08/18 03:07:14  ralfoide
//	PHP 4.3.x support, new runtime filetype support
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------

// IMPORTANT: the "? >" must be the LAST LINE of this file, otherwise
// some HTTP output will be started by PHP4 and setting headers or cookies
// will fail with a PHP error message.
?>
