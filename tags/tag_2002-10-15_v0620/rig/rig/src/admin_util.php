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

//*****************************************************
function admin_perform_before_header($refresh_url = "")
//*****************************************************
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


//****************************
function admin_perform_defer()
//****************************
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
		create_all_previews($current_album);
	}
	else if ($admin == "rm_all_prev")
	{
		delete_all_previews($current_album);
	}
	else if ($admin == "rnm_canon")
	{
		rename_all_canon_images($current_album);
	}
	else if ($admin == "set_icon" && $current_album && $current_image)
	{
		echo "Changing icon for album...<br>";
		set_album_icon($current_album, $current_album, $current_image);
	}
	else if ($admin == "show_album" && $show_album && $item)
	{
		set_album_visible($item, ($show_album == 'on'));
	}
	else if ($admin == "show_image" && $show_image && $current_image)
	{
		set_image_visible($current_image, ($show_image == 'on'));
	}
}

//-------------------------------------------------------------


//**********************************
function create_all_previews($album)
//**********************************
{
	global $abs_album_path;
	global $pref_image_size, $pref_image_quality;

	$abs_dir = $abs_album_path . prep_sep($album);

	echo "Creation Previews for <b>$album</b><p></center><code>\n";

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$file_list = array();
	$handle = @opendir($abs_dir);
	if (!$handle)
		html_error("Album directory '$abs_dir' does not exist!");
	else
	{
		create_preview_dir($album);

		$start_table = TRUE;

		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = post_sep($album) . $file;
					echo "</code><center>\n";
					create_all_previews($name);
					echo "</center><code>\n";
				}
				else if (valid_ext($file))
				{
					$t = getmicrotime();
					$preview = build_preview($album, $file);
					$t1 = getmicrotime() - $t;

					$t = getmicrotime();
					$preview = build_preview($album, $file, $pref_image_size, $pref_image_quality);
					$t2 = getmicrotime() - $t;

					/*
					if ($start_table)
					{
						echo "<table border=1>\n";
						echo "<tr><td>filename</td><td align=\"center\">preview time</td><td align=\"center\">image time</td></tr>";
						$start_table = FALSE;
					}

					echo "<tr><td>\n$file</td>";
					echo "<td align=\"center\">";
					if ($t1 > 0.01)
						printf("%2.2f s", $t1);
					else
						echo "-";
					echo "</td><td align=\"center\">";
					if ($t2 > 0.01)
						printf("%2.2f s", $t2);
					else
						echo "-";
					echo "</td></tr>";
					*/

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

		//if (!$start_table)
		//	echo "</table>\n";

		closedir($handle);
	}

	echo "</code><center><p>Done<hr><p>\n";
	flush();
}


//**********************************
function delete_all_previews($album)
//**********************************
{
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . prep_sep($album);

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
		html_error("Album directory '$abs_dir' does not exist!");
	else
	{
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = post_sep($album) . $file;
					echo "</code><center>\n";
					delete_all_previews($name);
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


//********************************
function clean_all_options($album)
//********************************
{
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . prep_sep($album);

	echo "Processing Options for for <b>$album</b><p>\n";

	$file_list = array();
	$album_list = array();

	// get all files and dirs, recurse in dirs first
	$handle = @opendir($abs_dir);
	if (!$handle)
		html_error("Album directory '$abs_dir' does not exist!");
	else
	{
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$album_list[] = $file;

					$name = post_sep($album) . $file;
					clean_all_options($name);
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


//**************************************
function rename_all_canon_images($album)
//**************************************
{
	global $abs_album_path;

	$abs_dir = $abs_album_path . prep_sep($album);

	echo "Renaming Canon 100-1234_IMG.JPG for <b>$album</b><p>\n";

	// get all files in this dir
	$handle = @opendir($abs_dir);
	if (!$handle)
		html_error("Album directory '$abs_dir' does not exist!");
	else
	{
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . prep_sep($file);

				if (rig_is_file($abs_file))
				{
					if (eregi("^([0-9]+)[- _]([0-9]+)[- _]img\.jpg$", $file, $reg))
					{
						$dest = "$reg[2].jpg";
	
						echo "Renaming '$file' --> '$dest'<br>\n";

						$abs_dest = $abs_dir . prep_sep($dest);
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


//*****************************************************
function recurse_preview_info($album, &$nb, &$nf, &$sz)
//*****************************************************
{
	global $abs_preview_path;
	$abs_dir = $abs_preview_path . prep_sep($album);

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
				$abs_file = $abs_dir . prep_sep($file);
				if (is_dir($abs_file))
				{
					$name = post_sep($album) . $file;
					recurse_preview_info($name, &$nb, &$nf, &$sz);
				}
				else if (valid_ext($file))
				{
					$nb++;
					$sz += filesize($abs_file);
			    }
			}
		}
		closedir($handle);
	}
}


//******************************************
function set_album_visible($album, $visible)
//******************************************
{
	global $list_hide;
	global $current_album;

	echo "set album: '$album' visible: '$visible'<br>\n";

	if (!$album)
		return;

	if ($visible && !is_visible($album))
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
	else if (!$visible && is_visible($album))
	{
		// add the name to the hide list
		$list_hide[] = $album;
		write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	read_album_options($current_album);
}


//******************************************
function set_image_visible($image, $visible)
//******************************************
{
	global $list_hide;
	global $current_album;

	echo "set image: '$image' visible: '$visible'<br>\n";

	if (!$image)
		return;

	if ($visible && !is_visible($image))
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
	else if (!$visible && is_visible($image))
	{
		// add the name to the hide list
		$list_hide[] = $image;
		write_album_options($current_album);
	}

	// make sure we read back the written options...
	// takes some time, but this is a neat debug thingy
	read_album_options($current_album);
}


//*******************************
function get_preview_info($album)
//*******************************
// Result: array{ nb_files, nb_folders, nb_bytes}
{
	$nb = 0;
	$nf = 0;
	$sz = 0;
	recurse_preview_info($album, &$nb, &$nf, &$sz);

	$res = array($nb,
				 $nf,
				 $sz);

	return $res;
}


//-----------------------------------------------------------


//****************************
function display_album_admin()
//****************************
{
	global $pref_nb_col;
	global $list_albums;
	global $list_hide;
	global $current_album;
	global $html_options, $html_album;
	global $html_hidden, $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $html_set_desc;
	global $color_header_bg;
	global $color_warning_bg;

	$i = 0;
	$n = $pref_nb_col;
	$m = count($list_images);

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_albums as $key => $dir)
	{
		$name = post_sep($current_album) . $dir;
		$pretty = pretty_name($dir, FALSE);
		$preview = encode_url_link(get_album_preview($name));

		if (is_visible($dir))
		{
			$visible = $html_vis_off;
			$vis_val = "off";
			$header_color = $color_header_bg;
		}
		else
		{
			$visible = $html_vis_on;
			$vis_val = "on";
			$header_color = $color_warning_bg;
		}
	?>
			<td <?= $w ?>>
			<center>

				<table width="100%" bgcolor="<?= $header_color ?>">
				  <tr><td>
					<center>
						<a name="<?= $key ?>">
							<?php echo "$html_album<br><b>$pretty</b>\n" ?>
						</a>
					</center>
				  </td></tr>
				</table>
				<br>

				<a href="<?= self_url("", $name, TRUE) ?>"><img src="<?= $preview ?>" alt="<?= $dir ?>" border="1" ></a>
				<br>

				<a href="<?= self_url(-1, -1, -1, "admin=show_album&item=$dir&show_album=$vis_val#$key") ?>">
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


//****************************
function display_image_admin()
//****************************
{
	global $pref_nb_col;
	global $list_images;
	global $current_album;
	global $html_options, $html_image;
	global $html_use_as_icon;
	global $html_hidden, $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $html_set_desc;
	global $color_header_bg;
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
		$preview = encode_url_link(build_preview($current_album, $file, -1, -1, FALSE));

		if (is_visible($file))
		{
			$visible = $html_vis_off;
			$vis_val = "off";
			$header_color = $color_header_bg;
		}
		else
		{
			$visible = $html_vis_on;
			$vis_val = "on";
			$header_color = $color_warning_bg;
		}

		?>
			<td <?= $w ?>>
			<center>

				<table width="100%" bgcolor="<?= $header_color ?>">
				  <tr><td>
					<center>
						<a name="<?= $key ?>">
							<?php echo "$html_image<br><b>$pretty</b>\n" ?>
						</a>
					</center>
				  </td></tr>
				</table>
				<br>

				<img src="<?= $preview ?>" alt="<?= $file ?>" border="1" >
				<br>

				<a href="<?= self_url($file, -1, TRUE, "admin=set_icon#$key") ?>">
					<?= $html_use_as_icon ?>
				</a>
				<br>

				<a href="<?= self_url($file, -1, TRUE, "admin=show_image&show_image=$vis_val#$key") ?>">
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
function display_image_admin_old()
//********************************
{
	global $list_images;
	global $current_album;
	global $html_options, $html_image;
	global $html_use_as_icon;
	global $html_hidden, $html_visible, $html_vis_on, $html_vis_off, $html_ok;
	global $html_rename_album;
	global $html_set_desc;
	global $color_header_bg;
	global $color_warning_bg;

	foreach($list_images as $key => $file)
	{
		$pretty = $file;
		$preview = encode_url_link(build_preview($current_album, $file, -1, -1, FALSE));
		$link = self_url($file, -1, TRUE);

		if (is_visible($file))
		{
			$check_on  = "checked";
			$check_off = "";
			$header_color = $color_header_bg;
		}
		else
		{
			$check_on  = "";
			$check_off = "checked";
			$header_color = $color_warning_bg;
		}
	?>
		<tr><td width="25%" align="center">
			<a name="<?= $key ?>">
				<img src="<?= $preview ?>" alt="<?= $pretty ?>" border=0>
			</a>
			<br>
			<?= $pretty ?>
		</td><td>
			<table width="100%" bgcolor="<?= $header_color ?>"><tr><td>
				<center><b>
					<?php echo "$html_image - $html_options" ?>
				</b></center>
			</td></tr></table>
			<br>
			<table colspan="2"><tr><td colspan="2">
				<a href="<?= "$link&admin=set_icon" ?>">
					<?= $html_use_as_icon ?>
				</a>
				<br>
				<?= $html_rename_image ?>
			</td></tr><tr><td>
				<form method="POST" action="<?= $link ?>" >
					<input type="hidden" name="admin" value="show_image">
					<input type="hidden" name="item" value="<?= $file ?>">
					<?= $html_visible ?>
					<br>
				  	<input type="radio" name="show_image" value="on" <?= $check_on ?> >
					<?= $html_vis_on ?>
					<br>
				  	<input type="radio" name="show_image" value="off" <?= $check_off ?> >
					<?= $html_vis_off ?>
					<p>
					<input type="submit" value="<?= $html_ok ?>" name="ok">
				</form>
			</td><td>
				<form method="POST" action="<?= $link ?>" >
					<input type="hidden" name="admin" value="set_desc">
					<input type="hidden" name="item" value="<?= $file ?>">
					<?= $html_set_desc ?>
					<br>
					<textarea rows="2" cols="40" name="set_desc">(comment system not ready yet)</textarea>
					<p>
					<div align="lef">
					<input type="submit" value="<?= $html_ok ?>" name="ok">
					</div>
				</form>
			</td></tr></table>
		</td></tr>
	<?php
	}
}

//-------------------------------------------------------------
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
//	Revision 1.15  2001/10/24 07:13:02  ralf
//	timeout issue
//	
//	Revision 1.14  2001/10/20 02:06:56  ralf
//	Marc's patch Sept-2001
//	
//	Revision 1.13  2001/09/05 08:40:29  ralf
//	code output and flush of web server output when creating previews
//	
//	Revision 1.12  2001/08/28 07:12:59  ralf
//	Made album/images list in admin a table with sub links
//	
//	Revision 1.11  2001/08/27 08:47:56  ralf
//	several updates
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
//	Revision 1.7  2001/08/07 18:33:02  ralf
//	Rename canon images finished
//	
//	Revision 1.6  2001/08/07 18:28:03  ralf
//	Rename Canon Images
//	
//	Revision 1.5  2001/08/07 09:40:43  ralf
//	Ability to toggle images on/off
//	
//	Revision 1.4  2001/08/07 09:04:30  ralf
//	Updated ID and VIM tag
//	
//	Revision 1.3  2001/08/07 09:01:17  ralf
//	Added globals for the html colors (in pref).
//	Fixed &lang in the language change URL
//	
//	Revision 1.2  2001/08/07 08:04:17  ralf
//	Added a cvs log entry
//	
//-------------------------------------------------------------
?>
