<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Administration Routines

require_once($dir_install . $dir_src . "common.php");

//-------------------------------------------------------------

//*********************************************************
function rig_admin_perform_before_header($refresh_url = "")
//*********************************************************
{
	global $admin;
	global $current_album;

	if ($admin == "rand_prev")
	{
		select_random_album_icon($current_album);
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
	global $admin;
	global $item;
	global $show_album;
	global $show_image;
	global $current_album;
	global $current_image;

	// debug
	// echo "admin defer: admin = '$admin' -- album = '$current_album' -- image = '$current_image'<br>\n";

	if ($admin == "mk_all_prev")
	{
		rig_admin_mk_preview($current_album);
	}
	else if ($admin == "rm_all_prev")
	{
		rig_admin_rm_previews($current_album);
	}
	else if ($admin == "rnm_canon")
	{
		rig_admin_rename_canon($current_album);
	}
	else if ($admin == "set_icon" && $current_album && $current_image)
	{
		echo "Changing icon for album...<br>";
		set_album_icon($current_album, $current_album, $current_image);
	}
	else if ($admin == "show_album" && $show_album && $item)
	{
		rig_admin_set_album_visible($item, ($show_album == 'on'));
	}
	else if ($admin == "show_image" && $show_image && $item)
	{
		// RM 20021022 fix for changing image visibility
		rig_admin_set_image_visible($item, ($show_image == 'on'));
	}
}

//-------------------------------------------------------------


//************************************************
function rig_admin_mk_preview($album,
							  $do_previews = TRUE,
							  $do_images   = TRUE)
//************************************************
// RM 20020712 support for only previews or images
{
	global $abs_album_path;
	global $pref_image_size;
	global $pref_image_quality;
	global $pref_preview_timeout;

	$abs_dir = $abs_album_path . rig_prep_sep($album);

	echo "<hr width=\"50%\">\n";

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$file_list = array();
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		echo "Creation Previews for <b>$album</b><p></center>\n";

		rig_html_error("Admin: Create Previews", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		echo "Creation Previews for <b>$album</b><br>\n";

		// inform PHP this may take a while...
		if ($pref_preview_timeout)
			set_time_limit($pref_preview_timeout);


		echo "</center><p><code>\n";

		create_preview_dir($album);

		$start_table = TRUE;

		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = rig_post_sep($album) . $file;
					echo "</code><center>\n";
					rig_admin_mk_preview($name, $do_previews, $do_images);
					echo "</center><code>\n";
				}
				else if (rig_valid_ext($file))
				{
					// image exists, create an id if not done yet
					// RM 20021021 not for rig 062

					if ($do_previews)
					{
						$t = rig_getmicrotime();
						$preview = build_preview($album, $file);
						$t1 = rig_getmicrotime() - $t;
					}
					else
					{
						$t1 = 0;
					}

					if ($do_images)
					{
						$t = rig_getmicrotime();
						$preview = build_preview($album, $file, $pref_image_size, $pref_image_quality);
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
					flush();
			    }
			}
		}

		closedir($handle);

		echo "</code>\n";
	}

	echo "<center><p>Done for <i>$album</i><hr><p>\n";
	flush();
}


//************************************
function rig_admin_rm_previews($album)
//************************************
{
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . rig_prep_sep($album);

	// tell php this may take a while...
	// (30 s is php's default for the script processing. I allow 30s
	// per directory, which is a lot)
	set_time_limit(30);

	echo "Deleting Previews for <b>$album</b><p></center><code>\n";

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
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = rig_post_sep($album) . $file;
					echo "</code><center>\n";
					rig_admin_rm_previews($name);
					echo "</center><code>\n";
				}
				else if (eregi("^prev[0-9]+_", $file))
				{
					echo "$file<br>\n";
					flush();
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

	echo "</code><center><p>Done<hr><p>\n";
	flush();
}


//***********************************
function rig_admin_rm_options($album)
//***********************************
{
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . rig_prep_sep($album);

	echo "Processing Options for for <b>$album</b><p>\n";

	$file_list = array();
	$album_list = array();

	// get all files and dirs, recurse in dirs first
	$handle = @opendir($abs_dir);
	if (!$handle)
	{
		rig_html_error("Admin: Remove Options", "Failed to open album directory, probably does not exist!", $abs_dir, $php_errormsg);
	}
	else
	{
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					$album_list[] = $file;

					$name = rig_post_sep($album) . $file;
					rig_admin_rm_options($name);
				}
				else
				{
					$file_list[] = $file;
				}
			}
		}

		closedir($handle);
	}


	// get the options for this album
	read_album_options($album);

	// we have the list of files and sub-albums for this album
	// process the options


	// write the options
	write_album_options($album);

	echo "<p>Done<hr><p>\n";
}


//*************************************
function rig_admin_rename_canon($album)
//*************************************
{
	global $abs_album_path;

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
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);

				if (rig_is_file($abs_file))
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
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . rig_prep_sep($album);

	// we're processing one more directory
	$nf++;

	// get all files and dirs, recurse in dirs first
	$handle = @opendir($abs_dir);
	if ($handle)
	{
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = rig_post_sep($album) . $file;
					rig_admin_recurse_previnfo($name, &$nb, &$nf, &$sz);
				}
				else if (rig_valid_ext($file))
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

		write_album_options($current_album);
	}
	else if (!$visible && rig_is_visible(-1, $album))
	{
		// add the name to the hide list
		$list_hide[] = $album;
		write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	read_album_options($current_album);
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

		write_album_options($current_album);
	}
	else if (!$visible && rig_is_visible(-1, -1, $image))
	{
		// add the name to the hide list
		$list_hide[] = $image;
		write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	read_album_options($current_album);
}


//*****************************************
function rig_admin_get_preview_info($album)
//*****************************************
// Result: array{ nb_files, nb_folders, nb_bytes}
{
	$nb = 0;
	$nf = 0;
	$sz = 0;
	rig_admin_recurse_previnfo($album, &$nb, &$nf, &$sz);

	$res = array($nb,
				 $nf,
				 $sz);

	return $res;
}


//-----------------------------------------------------------


//********************************
function rig_admin_display_album()
//********************************
{
	global $pref_nb_col;
	global $list_albums;
	global $list_hide;
	global $current_album;
	global $html_options, $html_album;
	global $html_hidden, $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $html_set_desc;
	global $color_section_bg;
	global $color_warning_bg;

	$i = 0;
	$n = $pref_nb_col;
	$m = count($list_images);

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_albums as $key => $dir)
	{
		$name = rig_post_sep($current_album) . $dir;
		$pretty = pretty_name($dir, FALSE);
		$preview = rig_encode_url_link(get_album_preview($name));

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
		$vis_link = self_url(-1, -1, -1, "admin=show_album&item=$dir&show_album=$vis_val#$key");
	?>
			<td <?= $w ?>>
			<center>

				<?php
					rig_display_section("<a name=\"$key\">$html_album<br><b>$pretty</b></a>\n",
										$header_color);
				?>

				<br>

				<a href="<?= self_url("", $name, TRUE) ?>"><img src="<?= $preview ?>" alt="<?= $dir ?>" border="1" ></a>
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
			echo "</td>\n";
	}

	echo "</tr>\n";
}


//********************************
function rig_admin_display_image()
//********************************
{
	global $pref_nb_col;
	global $list_images;
	global $current_album;
	global $html_options, $html_image;
	global $html_use_as_icon;
	global $html_hidden, $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $html_set_desc;
	global $color_section_bg;
	global $color_warning_bg;

	$i = 0;
	$n = $pref_nb_col;
	$m = count($list_images);

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_images as $key => $file)
	{
		$pretty = pretty_name($file, FALSE);
		$preview = rig_encode_url_link(build_preview($current_album, $file, -1, -1, FALSE));

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
		// RM 20021022 fix for changing image visibility
		$vis_link = self_url(-1, -1, TRUE, "admin=show_image&item=$file&show_image=$vis_val#$key");

		?>
			<td <?= $w ?>>
			<center>

				<?php
					rig_display_section("<a name=\"$key\">$html_image<br><b>$pretty</b></a>\n",
										$header_color);
				?>

				<br>

				<font size="-1">
					<a href="<?= self_url($file, -1, TRUE, "#$key") ?>"><img src="<?= $preview ?>" alt="<?= $file ?>" border="1" ></a>
					<br>
	
					<a href="<?= self_url($file, -1, TRUE, "admin=set_icon#$key") ?>">
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
			echo "</td>\n";
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

	echo "<option value='0'>$html_root</option>\n";

	foreach($list as $key => $item)
	{
		echo "<option value='$key'>$item</option>\n";
	}
}


//-------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.5  2002/10/22 08:37:47  ralfoide
//	Fix for chaning image visibility
//
//	Revision 1.4  2002/10/21 07:34:16  ralfoide
//	Comment about end-of-file
//	
//	Revision 1.3  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.2  2002/10/16 04:46:44  ralfoide
//	Added timeout for image preview
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
