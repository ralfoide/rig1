<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------


//**************************************************
function display_current_album($link_current = TRUE)
//**************************************************
{
	global $current_album;
	global $html_root;

	$sep = CURRENT_ALBUM_ARROW;

	echo "<a href=\"" . self_url("", "") . "\">[$html_root]</a>\n";
	$name = "";
	$items = explode(SEP, $current_album);
	while($items)
	{
		$item = array_shift($items);
		$pretty = pretty_name($item, FALSE, TRUE);
		$name = post_sep($name) . $item;

		if (!$item)
			break;

		if ($items)	// if not last...
		{
			echo "$sep<i><a href=\"" . self_url("", $name). "\">$pretty</a></i>\n";
		}
		else
		{
			if ($link_current)
				echo "$sep<b><a href=\"" . self_url("", $name) . "\">$pretty</a></b>\n";
			else
				echo "$sep<b>$pretty</b>\n";
		}
	}
}


//***************************
function display_back_album()
//***************************
{
	global $html_back_previous;
	global $current_album;

	$items = explode(SEP, $current_album);

	if ($current_album && count($items) > 0)
	{
		// remove the last item
		unset($items[count($items)-1]);
		// glue it back
		$path = implode(SEP, $items);

		// write the link
		echo "<a href=\"" . self_url("", $path) . "\">$html_back_previous</a>\n";
	}
}


//***************************
function display_album_list()
//***************************
{
	global $dir_images;
	global $pref_nb_col;
	global $pref_preview_size;
	global $abs_preview_path;
	global $list_albums;
	global $current_album;

	// name of album-border images
	$box_tr = $dir_images . "box_tr.gif";
	$box_br = $dir_images . "box_br.gif";
	$line_b = $dir_images . "line_b.gif";
	$line_r = $dir_images . "line_r.gif";


	$i = 0;
	$n = $pref_nb_col;
	$m = count($list_albums);
	if ($m < $n)
		$n = $m;

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_albums as $dir)
	{
		$name = post_sep($current_album) . $dir;
		$pretty = pretty_name($dir, FALSE, TRUE);

		$link = self_url("", $name);

		// prepare title
		$title = "<center><a href=\"$link\">$pretty</a></center>\n";

		$square_size = $pref_preview_size + 12;
		echo "<td $w>\n";
		echo "<table border=\"0\">\n";
		echo "<tr><td align=\"center\" height=\"$square_size\">\n";

		// get the relative and absolute path to the preview icon
		$abs_path = "";
		$url_path = "";
		$res = build_album_preview($name, &$abs_path, &$url_path);
		$url_path = encode_url_link($url_path);
		if (!$res)
		{
			// if we can't have the preview icon, use a little album icon
			?>
			<a href="<?= $link ?>">
				<img src="<?= $url_path ?>" alt="<? $pretty ?>" border=0>
			</a>
			<?php
		}
		else
		{
			// otherwise get the size of the icon and display it with a nice fancy table

			$icon_info = image_info($abs_path);
			if (is_array($icon_info) && count($icon_info) > 2)
			{
				$sx = $icon_info["w"];
				$sy = $icon_info["h"];
			}
			else
			{
				$sx = $pref_preview_size;
				$sy = $pref_preview_size;
			}
			?>

			<table border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td><table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0"><tr><td>
					<a href="<?= $link ?>"><img src="<?= $url_path ?>" alt="<? $pretty ?>" width="<?= $sx ?>" height="<?= $sy ?>" border="0"></a></td></tr></table></td>
			    <td><img src="<?= $box_tr ?>" width="3" height="3"><br><img src="<?= $line_r ?>" width="3" height="<?= $sy+2-3 ?>"></td>
			    <td><img src="<?= $box_tr ?>" width="3" height="6"><br><img src="<?= $line_r ?>" width="3" height="<?= $sy+2-6 ?>"></td>
			  </tr>
			  <tr>
			    <td><img src="<?= $box_tr ?>" width="3" height="3"><img src="<?= $line_b ?>" width="<?= $sx+2-3 ?>" height="3"></td>
			    <td><img src="<?= $box_br ?>" width="3" height="3"></td>
			    <td><img src="<?= $line_r ?>" width="3" height="3"></td>
			  </tr>
			  <tr>
			    <td><img src="<?= $box_tr ?>" width="6" height="3"><img src="<?= $line_b ?>" width="<?= $sx+2-6 ?>" height="3"></td>
			    <td><img src="<?= $line_b ?>" width="3" height="3"></td>
			    <td><img src="<?= $box_br ?>" width="3" height="3"></td>
			  </tr>
			</table>
			
			<?php
		}

		echo "</td></tr>\n";
		echo "<tr><td>$title</td></tr>\n";
		echo "</table>";

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


//***************************
function display_image_list()
//***************************
{
	// output should be like:
    // <!-- tr>
    //	<td width="20%" align="center">img1</td>
	// </tr -->

	global $pref_nb_col;
	global $list_images;
	global $current_album;
	global $pref_preview_size;
	
	$i = 0;
	$n = $pref_nb_col;
	$m = count($list_images);
	if ($m < $n)
		$n = $m;

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	echo "<tr>\n";

	foreach($list_images as $file)
	{
		$pretty1 = pretty_name($file);
		$pretty2 = pretty_name($file, FALSE);

		$info = build_preview_info($current_album, $file);
		$preview = $info["p"];
		$width   = $info["w"];
		$height  = $info["h"];

		$preview = encode_url_link($preview);

		$link = self_url($file);
		$title = "<center><a href=\"$link\">$pretty1</a></center><br>\n";

		$square_size = $pref_preview_size + 8;

		?>
			<td <?= $w ?>>
				<table border="0">
					<tr><td align="center" valing="center" height="<?= $square_size ?>">
						<table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0">
						<tr><td>
							<a href="<?= $link ?>"><img src="<?= $preview ?>" alt="<?= $pretty2 ?>" width="<?= $width ?>" height="<?= $height ?>" border="0" align="middle"></a></td>
						</tr>
						</table>
	
					</td></tr>
					<tr><td>
						<?= $title ?>
					</td></tr>
				</table>
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

//-----------------------------------------------------------------------


//**********************
function display_image()
//**********************
{
	global $current_album;
	global $current_image;
	global $pretty_image;
	global $rig_img_size;
	global $pref_image_size;
	global $pref_image_quality;

	if ($rig_img_size != -2 && $rig_img_size < 1)
		$rig_img_size = $pref_image_size;

	// get image (build resized preview if necessary)
	$preview = build_preview($current_album, $current_image, $rig_img_size, $pref_image_quality);

	// RM 110801 -- use size of image in img tag if available
	// get actual size of image
	$icon_info = image_info($preview);

	// url-encode filename
	$preview = encode_url_link($preview);

	if (is_array($icon_info) && count($icon_info) > 2)
	{
		// if we have the size, use it in the img tag
		$sx = $icon_info["w"];
		$sy = $icon_info["h"];

		echo "<img src=\"$preview\" alt=\"$pretty_image\" border=0 width=\"$sx\" height=\"$sy\">";
	}
	else
	{
		// there's no size (probably a problem when creating the preview)
		// just use the img name anyway
		echo "<img src=\"$preview\" alt=\"$pretty_image\" border=0>";
	}

    // debug
    // echo "<br>rig_img_size = '$rig_img_size'<br>\n";
    // echo "preview = '$preview'<br>\n";
}


//***************************
function display_image_info()
//***************************
{
	global $current_album;
	global $current_image;
	global $current_img_info;
	global $html_pixels, $html_image2;

	if ($current_img_info)
		$res = $current_img_info;
	else
		$res = build_info($current_album, $current_image);

	$s  = $res["f"] . " $html_image2" . ", " . $res["w"] . "x" . $res["h"] . " $html_pixels";

	if ($res["d"])
	{
		$s .= "<br>";
		$s .= $res["d"];
	}

	return $s;
}


//**************************
function insert_size_popup()
//**************************
{
	global $pref_size_popup;
	global $html_original;
	global $rig_img_size;

    // debug
    // echo "<br>rig_img_size = '$rig_img_size'<br>\n";
 
	// duplicate the list of sizes, makes sure the default is there
	$list = $pref_size_popup;
	if ($rig_img_size > 0 && !in_array($rig_img_size, $list))
		$list[] = $rig_img_size;

	// remove duplicates, sort it
	array_unique($list);
	sort($list);

	foreach($list as $item)
	{
		if ($item == $rig_img_size)
			echo "<option value='$item' selected>$item *</option>\n";
		else
			echo "<option value='$item'>$item</option>\n";
	}

	// add the "original" size at the end
	if ($rig_img_size > 0)
		echo "<option value='-2'>$html_original</option>\n";
	else
		echo "<option value='-2' selected>$html_original *</option>\n";
}


//-----------------------------------------------------------------------


//***********************************
function insert_credits($has_credits)
//***********************************
{
	global $html_text_credits;
	global $html_show_credits;
	global $html_credits;
	global $color_header_bg;

	if ($has_credits != 'on')
	{
		echo "<a href=\"" . self_url(-1, -1, -1, "credits=on") . "\">$html_show_credits</a><p>";
	}
	else
	{
		?>
			<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
				<center><b>
					<?php echo "$html_credits" ?>
				</b></center>
			</td></tr></table>
	
			<p>
			<?php echo "$html_text_credits" ?>
	
			<p>
		<?php
			phpinfo(INFO_CREDITS);
	}
}


//**********************
function insert_footer()
//**********************
{
    global $_debug_;
	global $rig_version;
	global $display_date;
	global $display_softname;
	global $html_generated;
	global $html_seconds;
	global $html_the;
	global $html_by;
	global $color_header_bg;

	?>
		<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
			<center><font size="-1">
				&lt;
	<?php
		echo "$html_generated "
			 . time_elapsed()
			 . " $html_seconds $html_the <i>$display_date</i> $html_by <i>$display_softname $rig_version</i>"
	?>
				&gt;
			</font></center>
		</td></tr></table>
	<?php

	// debug
    if ($_debug_)
    {
	    phpinfo(INFO_VARIABLES);
        phpinfo(INFO_ENVIRONMENT);
    }
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
//	Revision 1.4  2001/11/28 11:52:48  ralf
//	v0.6.1: display image last modification date
//	
//	Revision 1.3  2001/11/26 06:40:50  ralf
//	fix for display credits
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
