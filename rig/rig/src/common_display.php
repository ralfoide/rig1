<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------


//*********************************
function rig_display_header($title)
//*********************************
{
	global $html_encoding;
	global $html_language_code;
	global $theme_css_head;

	// Online reference:

	// For the DocType, consult the W3C HTML 4.0:
	// http://www.w3.org/TR/REC-html40/struct/global.html#h-7.2

	// For the Meta Content-Type, consult the W3C HTML 4.0
	// http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
	//
	// This list of charset is available here:
	// http://www.iana.org/assignments/character-sets

	// For the Robots Meta Tag, consult robotstxt.org:
	// http://www.robotstxt.org/wc/exclusion.html#meta

	// The HTML language code is described by the W3C HTML 4.0 here:
	// http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1
	// http://www.w3.org/TR/REC-html40/struct/dirlang.html#langcodes

	// Setup the language information for the HTML tag -- RM 20021023
	if ($html_language_code)
		$lang = "lang=\"$html_language_code\"";
	else
		$lang = "";

	// Provide the web server with a HTTP Header describing the right charset -- RM 20021023
	// This is the one step that will make the browser switch to the right encoding...
	if ($html_encoding)
		header("Content-Type: text/html; charset=$html_encoding");

// The indentation below is made on purpose, to make sure there's nothing before doctype
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?= $lang ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $html_encoding ?>">
	<meta name="robots" content="noindex, nofollow"> 
 	<title>
		<?= $title ?>
	</title>
	<?= $theme_css_head ?>
</head>
<?php

}

//*************************
function rig_display_body()
//*************************
{
	global $color_body_bg;
	global $color_body_text;
	global $color_body_link;
	global $color_body_alink;
	global $color_body_vlink;

	?>
		<body bgcolor="<?= $color_body_bg    ?>"
			  text   ="<?= $color_body_text  ?>"
			  link   ="<?= $color_body_link	 ?>"
			  alink  ="<?= $color_body_alink ?>"
			  vlink  ="<?= $color_body_vlink ?>"
		>
	<?php
}



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
		$name = rig_post_sep($name) . $item;

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
		$name = rig_post_sep($current_album) . $dir;

		if (!rig_is_visible(-1, $name))
			continue;

		$pretty = pretty_name($dir, FALSE, TRUE);

		$link = self_url("", $name);

		// prepare title
		$title = "<center><a href=\"$link\">$pretty</a></center>\n";

		$square_size = $pref_preview_size + 12;
		echo "<td $w>\n";
		echo "<table border=\"0\">\n";
		// echo "<tr><td align=\"center\" height=\"$square_size\">\n";
		echo "<tr><td align=\"center\">\n";

		// get the relative and absolute path to the preview icon
		$abs_path = "";
		$url_path = "";
		$res = build_album_preview($name, &$abs_path, &$url_path);
		$url_path = rig_encode_url_link($url_path);
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

			$dx = $pref_preview_size;
			$dy = $pref_preview_size;

			$icon_info = image_info($abs_path);
			if (is_array($icon_info) && count($icon_info) > 2)
			{
				$sx = $icon_info["w"];
				$sy = $icon_info["h"];
			}
			else
			{
				$sx = $dx;
				$sy = $dy;
			}

			// space around the album thumbnail and the shadow (to take into account the fact that
			// the thumbnail may be actually smaller than the expected default thumbnail size)
			// dx/dy is the default thumbnail size
			// sx/sy is the real thumbail size of this thumbnail
			// -8 is because the thumbnail has a 1-pixel border (*2) and the two shadows are 3 pixels each
			$x2 = ($dx-$sx-8)/2;
			$y2 = ($dy-$sy-8)/2;

			// make sure this size is positive non nul
			if ($x2 <= 0) $x2 = 1;
			if ($y2 <= 0) $y2 = 1;

			// RM 20021101 important: the various <img> and the title must have the </td>
			// immediately after without any new-line in between (most browsers would insert
			// a vertical space otherwise)
			
			?>
			<table border="0" cellspacing="0" cellpadding="0">
				<!-- top row -->
				<tr>
					<td><img src="<?= $box_tr ?>" width="<?= $x2   ?>" height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $sx+2 ?>" height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="3"            height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="3"            height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $x2   ?>" height="<?= $y2 ?>"></td>
				</tr>
				<!-- center rows -->
				<tr>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="<?= $sy+2 ?>"></td>
					<td><table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0">
					    <tr>
						    <td><a href="<?= $link ?>"><img src="<?= $url_path ?>" alt="<?= $pretty ?>" width="<?= $sx ?>" height="<?= $sy ?>" border="0"></a></td>
					    </tr>
					</table></td>
					<td valign="bottom"><img src="<?= $line_r ?>" width="3" height="<?= $sy+2-3 ?>"></td>
					<td valign="bottom"><img src="<?= $line_r ?>" width="3" height="<?= $sy+2-6 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="<?= $sy+2 ?>"></td>
				</tr>
				<tr>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="3"></td>
					<td align="right"><img src="<?= $line_b ?>" width="<?= $sx+2-3 ?>" height="3"></td>
					<td><img src="<?= $box_br ?>" width="3" height="3"></td>
					<td><img src="<?= $line_r ?>" width="3" height="3"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="3"></td>
				</tr>
				<tr>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="3"></td>
					<td align="right"><img src="<?= $line_b ?>" width="<?= $sx+2-6 ?>" height="3"></td>
					<td><img src="<?= $line_b ?>" width="3" height="3"></td>
					<td><img src="<?= $box_br ?>" width="3" height="3"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $x2 ?>" height="3"></td>
				</tr>
				<!-- bottom row -->
				<tr>
					<td><img src="<?= $box_tr ?>" width="<?= $x2   ?>" height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $sx+2 ?>" height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="3"            height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="3"            height="<?= $y2 ?>"></td>
					<td><img src="<?= $box_tr ?>" width="<?= $x2   ?>" height="<?= $y2 ?>"></td>
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
		{
			echo "</td>\n";
		}

		flush();
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

	foreach($list_images as $index => $file)
	{
		if (!rig_is_visible(-1, -1, $file))
			continue;

		// is this the last line? [RM 20021101]
		$is_last_line = ($index >= $m-$n);

		$pretty1 = pretty_name($file);
		$pretty2 = pretty_name($file, FALSE);

		$info = build_preview_info($current_album, $file);
		$preview = $info["p"];
		$width   = $info["w"];
		$height  = $info["h"];

		$preview = rig_encode_url_link($preview);

		// RM 20021101 important: the <img> and the title must have the </td>
		// immediately after without any new-line in between (most browsers would insert
		// a vertical space otherwise).
		// For everything but the last line, add a <br> in the title to create an
		// extra space between rows or images.

		$link = self_url($file);
		$title = "<center><a href=\"$link\">$pretty1</a></center>";
		
		if (!$is_last_line)
			$title .= "<br>";

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
					<tr>
						<td><?= $title ?></td>
					</tr>
				</table>
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

		flush();
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
	$preview = rig_encode_url_link($preview);

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
function rig_display_jhead()
//**************************
// RM 20021020 Added jhead support
// $pref_use_jhead is a string. When set to an empty string, nothing is printed.
// Otherwise it is the path of the jhead command on the current system.
// This function calls the command using exec and prints out each result line.
{
	global $current_album;
	global $current_image;
	global $abs_album_path;
	global $pref_use_jhead;
	global $display_title;

	// --- use the jhead application to extract info ---

	$name = $abs_album_path . rig_prep_sep($current_album) . rig_prep_sep($current_image);

	$retvar = 1;
	$output = array();

	$args = $pref_use_jhead . " " . rig_shell_filename($name);

	$res = exec($args, $output, $retvar);

	// if the command failed, try a variation on the shell-escaping
	if ($retvar == 1)
	{
		$args = $pref_use_jhead . " " . rig_shell_filename2($name);
		$res = exec($args, $output, $retvar);
	}

	// DEBUG
	// echo "<p> res -> ";    var_dump($res);
	// echo "<p> retvar -> "; var_dump($retvar);
	// echo "<p> args -> "  ; var_dump($args);
	// echo "<p> output-> " ; var_dump($output);

	// --- use output if jhead was successful ---

	if ($retvar == 0)
	{
		// Output every line except the one called "File name" (usually the first one)
		// which we'll replace by the pretty name of the image

		echo "<table>";

		foreach($output as $n => $line)
		{
			// Each line is in the format "Name : Value"
			$p = strpos($line, ":");

			if ($p <= 0)
			{
				// separator did not exist, just use the string
				echo "<tr><td colspan=2>" . $line . "</td><tr>\n";
			}
			else
			{
				// separator was found, use the fancy string
				if (strncmp($line, "File name", 9) == 0)
				{
					// only use up to 60 characters of the display title
					$s = $display_title;
					if (strlen($s) > 60)
					{
						$s = substr($s, 0, 40) . "...";
					}

					echo "<tr><td><b>" . substr($line, 0, $p-1) . " : </b></td><td>" . $s . "</td></tr>\n";
				}
				else
				{
					echo "<tr><td><b>" . substr($line, 0, $p-1) . " : </b></td><td>" . substr($line, $p+1) . "</td></tr>\n";
				}
			} // if
		} // foreach

		echo "</table>";

	} // if
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


//********************************************
function rig_display_section($html_content,
							 $color_bg   = "",
							 $color_text = "")
//********************************************
{
	global $color_section_bg;
	global $color_section_text;

	if ($color_bg == "")
		$color_bg = $color_section_bg;

	if ($color_text == "")
		$color_text = $color_section_text;
		

	?>
		<table width="100%" bgcolor="<?= $color_bg ?>"><tr><td>
			<font color="<?= $color_text ?>"><center>
				<?= $html_content ?>
			</center></font>
		</td></tr></table>
	<?php
}


//******************************************
function rig_display_options($use_hr = TRUE)
//******************************************
{
	global $color_section_bg;
	global $html_options;

	rig_display_section("<b>$html_options</b>");

	echo "<br><table>";

	rig_display_language();
	rig_display_theme();

	if ($use_hr)
		echo "<tr><td colspan=\"2\"  height=\"2\" bgcolor=\"$color_section_bg\"></td></tr>";
	// or use a <hr>:
	//	echo "<tr><td colspan=\"2\"><hr></td></tr>";

	echo "</table>";

	if (!$use_hr)
		echo "<p>";
}


//*****************************
function rig_display_language()
//*****************************
{
	global $html_desc_lang;
	global $html_language;
	global $current_language;

	$sep = FALSE;

	echo "<tr><td align=\"right\"><a name=\"lang\"><i>$html_language</i></td><td> \n";

	foreach($html_desc_lang as $key => $value)
	{
		if ($sep)
			echo "&nbsp;|&nbsp;";

		if ($current_language == $key)
			echo $value;
		else
			echo "<a href=\"" . self_url(-1, -1, -1, "lang=$key#lang") . "\">$value</a>\n";

		$sep = TRUE;
	}

	echo "</td></tr>";
}


//**************************
function rig_display_theme()
//**************************
{
	global $html_desc_theme;
	global $html_theme;
	global $current_theme;

	$sep = FALSE;

	echo "<tr><td align=\"right\"><a name=\"theme\"><i>$html_theme</i></td><td> \n";

	foreach($html_desc_theme as $key => $value)
	{
		if ($sep)
			echo "&nbsp;|&nbsp;";

		if ($current_theme == $key)
			echo $value;
		else
			echo "<a href=\"" . self_url(-1, -1, -1, "theme=$key#theme") . "\">$value</a>\n";

		$sep = TRUE;
	}

	echo "</td></tr>";
}


//-----------------------------------------------------------------------


//******************************************************
function rig_display_credits($has_credits, $has_phpinfo)
//******************************************************
{
	global $html_text_credits;
	global $html_hide_credits;
	global $html_show_credits;
	global $html_credits;

	global $html_show_phpinfo;
	global $html_hide_phpinfo;
	global $html_phpinfo;

	global $color_section_bg;

	$v = ($has_credits == "on" ? "off" : "on");
	$l = ($has_credits == "on" ? $html_hide_credits : $html_show_credits);
	echo "<a name=\"credits\" href=\"" . self_url(-1, -1, -1, "credits=$v#credits") . "\" target=\"_top\">$l</a><br>";

	$v = ($has_phpinfo == "on" ? "off" : "on");
	$l = ($has_phpinfo == "on" ? $html_hide_phpinfo : $html_show_phpinfo);
	echo "<a name=\"phpinfo\" href=\"" . self_url(-1, -1, -1, "phpinfo=$v#phpinfo") . "\" target=\"_top\">$l</a><br>";

	if ($has_credits == "on")
	{
		?>
			<p>
				<?php rig_display_section("<b>$html_credits<b>") ?>
			<p>
				<?php echo "$html_text_credits" ?>
			<p>
		<?php
	
		phpinfo(INFO_CREDITS);
	}

	if ($has_phpinfo == "on")
	{
		?>
			<p>
				<?php rig_display_section("<b>$html_phpinfo<b>") ?>
			<p>
		<?php

		phpinfo(INFO_ALL);
	}

	echo "<p>";
}


//***************************
function rig_display_footer()
//***************************
{
    global $_debug_;
	global $rig_version;
	global $display_date;
	global $display_softname;
	global $html_generated;
	global $html_seconds;
	global $html_the;
	global $html_by;
	global $color_section_bg;
	global $color_section_text;

	$sgen = str_replace("[time]", rig_time_elapsed(), $html_generated);
	$sgen = str_replace("[date]", $display_date, $sgen);
	$sgen = str_replace("[rig-version]", $display_softname . " " . $rig_version, $sgen);


	?>
		<table width="100%" bgcolor="<?= $color_section_bg ?>"><tr><td>
			<center><font size="-1" color="<?= $color_section_text ?>">
				&lt;
				<?= $sgen ?>
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
//	Revision 1.10  2002/11/02 04:08:46  ralfoide
//	Removed empty line after last row of thumbnails in image list.
//
//	Revision 1.9  2002/10/30 09:12:29  ralfoide
//	Finalized album thumbnail table, cleaned up experimental code. Checked with IE5, IE6, NS4.7 and Mozilla 1.1
//	
//	Revision 1.8  2002/10/30 09:06:18  ralfoide
//	Experimenting with alternate table to display album thumbnails
//	
//	Revision 1.7  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.6  2002/10/23 16:01:01  ralfoide
//	Added <html lang>; now transmitting charset via http headers.
//	
//	Revision 1.5  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
//	Revision 1.4  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.3  2002/10/20 11:50:49  ralfoide
//	jhead support
//	
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
