<?php
// vim: set tabstop=4 shiftwidth=4: //
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
	global $admin;

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

	// prepare the meta tags line
	$meta = "";

	if (!$admin && $pref_html_meta)
		$meta = $pref_html_meta;


// The indentation below is made on purpose, to make sure there's nothing before doctype
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?= $lang ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $html_encoding ?>">
	<?= $meta ?> 
 	<title>
		<?= $title ?>
	</title>
	<script language="JavaScript" type="text/javascript" src="browser_detect.js"></script>
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


//******************************************************
function rig_display_current_album($link_current = TRUE)
//******************************************************
{
	global $current_album;
	global $html_root;

	$sep = CURRENT_ALBUM_ARROW;

	echo "<a href=\"" . rig_self_url("", "") . "\">[$html_root]</a>\n";
	$name = "";
	$items = explode(SEP, $current_album);
	while($items)
	{
		$item = array_shift($items);
		$pretty = rig_pretty_name($item, FALSE, TRUE);
		$name = rig_post_sep($name) . $item;

		if (!$item)
			break;

		if ($items)	// if not last...
		{
			echo "$sep<i><a href=\"" . rig_self_url("", $name). "\">$pretty</a></i>\n";
		}
		else
		{
			if ($link_current)
				echo "$sep<b><a href=\"" . rig_self_url("", $name) . "\">$pretty</a></b>\n";
			else
				echo "$sep<b>$pretty</b>\n";
		}
	}
}


//*******************************
function rig_display_back_album()
//*******************************
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
		echo "<a href=\"" . rig_self_url("", $path) . "\">$html_back_previous</a>\n";
	}
}


//*******************************
function rig_display_album_list()
//*******************************
{
	global $dir_images;
	global $pref_nb_col;
	global $pref_preview_size;
	global $pref_preview_quality;
	global $pref_small_preview_size;		// RM 20030720 for 'vert' layout
	global $pref_small_preview_quality;
	global $abs_preview_path;
	global $html_images;
	global $list_albums;
	global $html_album;
	global $current_album;
	global $list_description;				// RM 20030713
	global $pref_album_layout;				// RM 20030718 'grid' or 'vert'
	global $pref_album_with_description_layout;	// RM 20030720 auto-switch
	global $pref_use_album_border;			// RM 20030814

	$i = 0;

	// select the layout
	// - by default, $pref_album_layout is used
	// - if this album as descriptiom, $pref_album_with_description_layout
	//   is used if defined and not empty

	$layout = $pref_album_layout;
	if (is_array($list_description)
		&& count($list_description) > 0
		&& is_string($pref_album_with_description_layout)
		&& $pref_album_with_description_layout != '')
	{
		$layout = $pref_album_with_description_layout;
	}

	// only one item per line if not in grid layout
	if ($layout != 'grid')
	{
		$n = 1;
		$p = $pref_preview_size;
		$w_td = " width=\"$p\" valign=\"top\" align=\"center\"";

	}
	else
	{
		$n = $pref_nb_col;

		$m = count($list_albums);
		if ($m < $n)
			$n = $m;
	
		$p = (int)(100/$n);
		$w_td = " width=\"$p%\" valign=\"top\" align=\"center\"";
		
	}

	if ($layout == 'vert')
	{
		// for vertical layout, use small previews [RM 20030720]
		$preview_size    = $pref_small_preview_size;
		$preview_quality = $pref_small_preview_quality;
	}
	else
	{
		// for non-vertical layout, use normal previews
		$preview_size    = $pref_preview_size;
		$preview_quality = $pref_preview_quality;
	}

	
	echo "<tr>\n";

	foreach($list_albums as $dir)
	{
		$name = rig_post_sep($current_album) . $dir;

		if (!rig_is_visible(-1, $dir))
			continue;

		$pretty = rig_pretty_name($dir, FALSE, TRUE);

		$link = rig_self_url("", $name);

		// ---- collect information on the album ----

		// prepare title

		$title = "<a href=\"$link\">$pretty</a>\n";

		
		// prepare description, if any

		if (is_string($list_description[$dir]))
			$desc = $list_description[$dir];
		else
			$desc = "";

			
		// get the album's date

		$album_date = rig_get_album_date($name);
		

		// get the album's image count
		// TBDL when the RAlbum class will be coded
		// (right now since albums are managed thru global lists and variables
		//  recursing to read an album is a bad idea).

		$sub_count = 0;

		// prepare image tooltip and alt attributes
		
		$alt     = "$html_album: $pretty";
		$tooltip = "$html_album: $pretty; Last updated: $album_date";


		// --------------------------------------------
		
		$square_size = $preview_size + 12;

		if ($layout == 'vert')
		{
			// no header per item in vert layout
			echo "<td>&nbsp;&nbsp;&nbsp;</td>";
			echo "<td $w_td>\n";
		}
		else // default: 'grid' mode
		{
		echo "<td $w_td>\n";
			echo "<table border=\"0\">\n";
			echo "<tr><td align=\"center\">\n";
		}

		// get the relative and absolute path to the preview icon
		$abs_path = "";
		$url_path = "";
		$res = rig_build_album_preview($name, &$abs_path, &$url_path, $preview_size, $preview_quality);
		$url_path = rig_encode_url_link($url_path);
		if (!$res)
		{
			// if we can't have the preview icon, use a little album icon

			$dx = $preview_size;
			$dy = $preview_size;

			?>

			<table width="<?= $dx ?>" height="<?= $dy ?>" border="0">
				<tr>
					<td align="center" valign="middle">
						<a href="<?= $link ?>"><img src="<?= $url_path ?>" alt="<? $alt ?>" title="<?= $tooltip ?>" border=0></a>

					</td>
				</tr>
			</table>

			<?php
		}
		else
		{
			// otherwise get the size of the icon and display it with a nice fancy table

			$dx = $preview_size;
			$dy = $preview_size;

			$icon_info = rig_image_info($abs_path);
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

			if (isset($pref_use_album_border) && !$pref_use_album_border)
			{
				// Do not display any border around the thumbnail

				$x2 = ($dx-$sx-2)/2;
				$y2 = ($dy-$sy-2)/2;

				?>
				<table width="<?= $dx ?>" height="<?= $dy ?>" border="0">
					<tr>
						<td align="center" valign="middle"><a href="<?= $link ?>" title="<?= $tooltip ?>"><img src="<?= $url_path ?>" alt="<?= $alt ?>" width="<?= $sx ?>" height="<?= $sy ?>" border="1"></a></td>
					</tr>
				</table>
				<?php
			}
			else
			{
				// RM 20030713 -- better layout that almost works with Mozilla 1.0
				// It uses background pictures for table cells, which means it won't
				// render on old browser (and that's actually better than a broken
				// cell layout anyway)

				$sx2 = $sx+2;		$sy2 = $sy+2;			// image size including border=1
				$sx8 = $sx2+6;		$sy8 = $sy2+6;			// image size including border=1 and including 6-pixel shadow frame
				$sxL = $sx2-9;		$syL = $sy2-9;
				$sxT = $sx8+2*$x2;	$syT = $sy8 + 2*$y2;	// the overal table size including surrounding spacing

				// name of album-border images
				// old names
				$box_tr = $dir_images . "album_topright.gif";
				$box_br = $dir_images . "album_bottomright.gif";
				$box_bl = $dir_images . "album_bottomleft.gif";
				$line_b = $dir_images . "album_bottomline.gif";
				$line_r = $dir_images . "album_rightline.gif";

				?>
				<table  width="<?= $sxT ?>" height="<?= $syT ?>" border="0" cellspacing="0" cellpadding="0">
				  <tr> 
				    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
				    <td width="<?= $sx8 ?>" height="<?= $y2  ?>" colspan="3"></td>
				    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
				  </tr>
				  <tr> 
				    <td width="<?= $x2  ?>" height="<?= $sy8 ?>" rowspan="3"></td>
				    <td width="<?= $sx2 ?>" height="<?= $sy2 ?>" colspan="2" rowspan="2" align="center">
					    <table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0">
						    <tr>
							    <td><a href="<?= $link ?>"><img src="<?= $url_path ?>" alt="<?= $alt ?>" title="<?= $tooltip ?>" width="<?= $sx ?>" height="<?= $sy ?>" border="0"></a></td>
						    </tr>
						</table></td>
				    <td width="6"           height="9"           background="<?= $box_tr ?>"></td>
				    <td width="<?= $x2  ?>" height="<?= $sy8 ?>" rowspan="3"></td>
				  </tr>
				  <tr> 
				    <td width="6"           height="<?= $syL ?>" background="<?= $line_r ?>"></td>
				  </tr>
				  <tr> 
				    <td width="9"           height="6" background="<?= $box_bl ?>"></td>
				    <td width="<?= $sxL ?>" height="6" background="<?= $line_b ?>"></td>
				    <td width="6"           height="6" background="<?= $box_br ?>"></td>
				  </tr>
				  <tr> 
				    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
				    <td width="<?= $sx8 ?>" height="<?= $y2  ?>" colspan="3"></td>
				    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
				  </tr>
				</table>
				<?php

			}
		}

		if ($layout == 'vert')
		{
			?>
			</td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td width="100%" align="left">
				<table width="100%" border="0">
				<td align="left"><?= $title ?></td>
				<td align="right"><font size="-1"><?= $album_date ?></font></td>
				</table>
			<?php
			if ($sub_count > 0)
				echo "$sub_count $html_images<br>\n";
			echo "$desc\n";
			?>
				</td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				</tr><tr <?= $w_tr ?> >
			<?php
		}
		else // default: 'grid' mode
		{
			echo "</td></tr>\n";
			echo "<tr><td><center>$title</center></td></tr>\n";
			echo "<tr><td title=\"Last updated: $album_date\"><center><font size=\"-1\"><span>$album_date</span></font></center></td></tr>\n";
			echo "<tr><td><center>$desc</center></td></tr>\n";
			echo "</table>";

			$i++;
			if ($i >= $n)
			{
				echo "</td></tr><tr $w_tr>\n";
				$i = 0;
			}
			else
			{
				echo "</td>\n";
			}
		}
		

		rig_flush();
	}

	echo "</tr>\n";
}


//*******************************
function rig_display_image_list()
//*******************************
{
	// output should be like:
    // <!-- tr>
    //	<td width="20%" align="center">img1</td>
	// </tr -->

	global $pref_nb_col;
	global $dir_images;
	global $list_images;
	global $html_image;
	global $current_album;
	global $list_description;				// RM 20030713
	global $pref_preview_size;
	global $pref_use_image_border;			// RM 20030814

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

		$pretty1 = rig_pretty_name($file);
		$pretty2 = rig_pretty_name($file, FALSE);

		$info = rig_build_preview_info($current_album, $file);
		$preview = $info["p"];

		if (isset($info["w"]))
			$width = "width=\"" . $info["w"] . "\"";
		else
			$width = "";
	
		if (isset($info["h"]))
			$height = "height=\"" . $info["h"] . "\"";
		else
			$height = "";

		$preview = rig_encode_url_link($preview);


		// RM 20021101 important: the <img> and the title must have the </td>
		// immediately after without any new-line in between (most browsers would insert
		// a vertical space otherwise).
		// For everything but the last line, add a <br> in the title to create an
		// extra space between rows or images.

		$link = rig_self_url($file);
		$title = "<center><a href=\"$link\">$pretty1</a></center>";

		$tooltip = "$html_image: $pretty2";

		// prepare description, if any
		if (is_string($list_description[$file]))
			$desc = $list_description[$file];
		else
			$desc = "";

		// add a vertical space for the last line
		if (!$is_last_line)
			$desc .= "<br>";

		$square_size = $pref_preview_size + 8;


		?>
			<td <?= $w ?>>
				<table border="0">
					<tr><td align="center" valing="center" height="<?= $square_size ?>">
					<?php
						if (isset($pref_use_image_border) && !$pref_use_image_border)
						{
							// Do not display any border around the thumbnail
					?>
						<table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0">
						<tr><td>
							<a href="<?= $link ?>"><img src="<?= $preview ?>" alt="<?= $pretty2 ?>" title="<?= $tooltip ?>" <?= $width ?> <?= $height ?> border="0" align="middle"></a></td>
						</tr>
						</table>
					<?php
						}
						else
						{
							// Display using a border around the thumbnail

							// get the size of the icon
					
							$dx = $preview_size;
							$dy = $preview_size;

							$icon_info = rig_image_info($abs_path);
							
							if (isset($info["w"]))
								$sx = $info["w"];
							else
								$sx = $dx;
						
							if (isset($info["h"]))
								$sy = $info["h"];
							else
								$sy = $dy;
							
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

							// RM 20030713 -- better layout that almost works with Mozilla 1.0
							// It uses background pictures for table cells, which means it won't
							// render on old browser (and that's actually better than a broken
							// cell layout anyway)
			
							$sx2 = $sx+2;		$sy2 = $sy+2;			// image size including border=1
							$sx8 = $sx2+6;		$sy8 = $sy2+6;			// image size including border=1 and including 6-pixel shadow frame
							$sxL = $sx2-9;		$syL = $sy2-9;
							$sxT = $sx8+2*$x2;	$syT = $sy8 + 2*$y2;	// the overal table size including surrounding spacing
			
							// name of album-border images
							// old names
							$box_tr = $dir_images . "image_topright.gif";
							$box_br = $dir_images . "image_bottomright.gif";
							$box_bl = $dir_images . "image_bottomleft.gif";
							$line_b = $dir_images . "image_bottomline.gif";
							$line_r = $dir_images . "image_rightline.gif";
			
							?>
							<table  width="<?= $sxT ?>" height="<?= $syT ?>" border="0" cellspacing="0" cellpadding="0">
							  <tr> 
							    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
							    <td width="<?= $sx8 ?>" height="<?= $y2  ?>" colspan="3"></td>
							    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
							  </tr>
							  <tr> 
							    <td width="<?= $x2  ?>" height="<?= $sy8 ?>" rowspan="3"></td>
							    <td width="<?= $sx2 ?>" height="<?= $sy2 ?>" colspan="2" rowspan="2" align="center">
								    <table border="0" bgcolor="#000000" cellspacing="1" cellpadding="0">
									    <tr>
										    <td><a href="<?= $link ?>"><img src="<?= $preview ?>" alt="<?= $pretty2 ?>" title="<?= $tooltip ?>" width="<?= $sx ?>" height="<?= $sy ?>" border="0"></a></td>
									    </tr>
									</table></td>
							    <td width="6"           height="9"           background="<?= $box_tr ?>"></td>
							    <td width="<?= $x2  ?>" height="<?= $sy8 ?>" rowspan="3"></td>
							  </tr>
							  <tr> 
							    <td width="6"           height="<?= $syL ?>" background="<?= $line_r ?>"></td>
							  </tr>
							  <tr> 
							    <td width="9"           height="6" background="<?= $box_bl ?>"></td>
							    <td width="<?= $sxL ?>" height="6" background="<?= $line_b ?>"></td>
							    <td width="6"           height="6" background="<?= $box_br ?>"></td>
							  </tr>
							  <tr> 
							    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
							    <td width="<?= $sx8 ?>" height="<?= $y2  ?>" colspan="3"></td>
							    <td width="<?= $x2  ?>" height="<?= $y2  ?>"></td>
							  </tr>
							</table>
							<?php
						}
					?>
	
					</td></tr>
					<tr>
						<td><?= $title ?></td>
					</tr>
					<tr>
						<td><center><?= $desc ?></center></td>
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

		rig_flush();
	}

	echo "</tr>\n";
}



//********************************
function rig_display_album_count()
//********************************
// RM 20030119 v0.6.3
// Don't display the album count if there is less than 3 albums
// because most of the time the table won't be large enough
{
	global $html_album_count;
	global $list_albums_count;	// updated in rig_has_albums()

	if ($list_albums_count >= 3)
	{
		// replace "[count]" in the $html by the number
		$str = str_replace("[count]", $list_albums_count, $html_album_count);

		echo $str;
	}
}



//********************************
function rig_display_image_count()
//********************************
// RM 20030119 v0.6.3
// Don't display the image count if there is less than 3 albums
// because most of the time the table won't be large enough
{
	global $html_image_count;
	global $list_images_count;	// updated in rig_display_image_list()

	if ($list_images_count >= 3)
	{
		// replace "[count]" in the $html by the number
		$str = str_replace("[count]", $list_images_count, $html_image_count);
		
		echo $str;
	}
}


//-----------------------------------------------------------------------


//**************************
function rig_display_image()
//**************************
{
	global $dir_album;
	global $abs_album_path;
	global $current_album;
	global $current_image;
	global $pretty_image;
	global $rig_img_size;
	global $pref_image_size;
	global $pref_image_quality;

	global $_test_;
	if (isset($_test_)) echo "Test type: $_test_<br>";

	if ($rig_img_size != -2 && $rig_img_size < 1)
		$rig_img_size = $pref_image_size;

	// get the file type
	$type = rig_get_file_type($current_image);

	if (strncmp($type, "image/", 6) == 0)
	{
		// get image (build resized preview if necessary)
		$preview = rig_build_preview($current_album, $current_image, $rig_img_size, $pref_image_quality);
	
		// RM 110801 -- use size of image in img tag if available
		// get actual size of image
		$icon_info = rig_image_info($preview);
	
		// url-encode filename
		$preview = rig_encode_url_link($preview);
	
		if (is_array($icon_info) && count($icon_info) > 2)
		{
			// if we have the size, use it in the img tag
			$sx = $icon_info["w"];
			$sy = $icon_info["h"];
	
			echo "<img src=\"$preview\" alt=\"$pretty_image\" title=\"$pretty_image\" border=0 width=\"$sx\" height=\"$sy\">";
		}
		else
		{
			// there's no size (probably a problem when creating the preview)
			// just use the img name anyway
			echo "<img src=\"$preview\" alt=\"$pretty_image\" title=\"$pretty_image\" border=0>";
		}
	}
	else if (strncmp($type, "video/", 6) == 0)
	{
		// RM 20030628 v0.6.3.4

		// get the full relative URL to the media file
		$full = rig_post_sep($dir_album) . rig_post_sep($current_album) . $current_image;

		// get actual size of media
		$abs = rig_post_sep($abs_album_path) . rig_post_sep($current_album) . $current_image;
		$info = rig_image_info($abs);
		
		if (isset($info["w"]))
			$sx = $info["w"];
		else
			$sx = 320;

		if (isset($info["h"]))
			$sy = $info["h"];
		else
			$sy = 240;

		$subtype = substr($type, 6);



		if ($subtype == "avi")
		{
			// ----------------------------------------
			// -------------- AVI ---------------------
			// ----------------------------------------

			// Link
			// Windows Media Player <object>
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/dnwmt/html/addwmwebpage.asp?frame=true&hidetoc=true
			//
			// WMV 7   class id: 6BF52A52-394A-11d3-B153-00C04F79FAA6
			// WMV 6.4 class id: 22D6f312-B0F6-11D0-94AB-0080C74C7E95
			
			// Other random links:
			// http://www.macromedia.com/support/dreamweaver/ts/documents/mediaplayer.htm
			// http://www.webreference.com/js/column51/install.html
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/wmp6sdk/htm/controlclsids.asp
			//		MediaPlayer 22D6F312-B0F6-11D0-94AB-0080C74C7E95
			//		NSPlay 2179C5D3-EBFF-11cf-B6FD-00AA00B4E220
			//		ActiveMovie 05589FA1-C356-11CE-BF01-00AA0055595A
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/dnwmt/html/6-4compat.asp
			//		Windows Media Player 9 Series 6BF52A52-394A-11d3-B153-00C04F79FAA6
			// http://home.maconstate.edu/dadams/Tutorials/AV/AV03/av03-03.htm

		if ($_test_==4)
		{
			// Win32 Moz 1.4: doesn't work
			// Win32 IE6: unsafe ActiveX, won't play
			?>
		
			<object data="<?= $full ?>" type="video/x-msvideo" />
		
			<?
		}
		else if ($_test_==3)
		{
			// Win32  Moz 1.4: WMV7 displays but doesn't play
			// Win32  IE6: WMV9 shows with bad aspect ratio, plays.
			// MacOSX Safari: yes if WMV 7.1/MacOS X installed before
			
			?>
				<embed
					type="application/x-msvideo"
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy ?>"
				>
				</embed>
			<?
		}
		else
		{
			// Win32  Moz 1.4: WMV7 displays but doesn't play
			// Win32  IE6: WMV9 shows, plays (using lack of image size, otherwise bad aspect ratio).
			// MacOSX Safari: no pluging, and MS download page invalid

				?>
				<object 
					classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6"
					codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112"
					id="mediaplayer1">
					<!-- width="<?= $sx ?>" height="<?= $sy ?>" -->
					<param name="URL" value="<?= $full ?>">
					<param name="Filename" value="<?= $full ?>">
					<param name="AutoStart" value="True">
					<param name="ShowControls" value="True">
					<param name="ShowStatusBar" value="True">
					<param name="ShowDisplay" value="True">
					<param name="AutoRewind" value="True">
		
				<embed 
					type="application/x-mplayer2"
					pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/"
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy ?>"
					filename="<?= $full ?>"
					autostart="True" 
					showcontrols="True"
					showstatusbar="True" 
					showdisplay="True"
					autorewind="True">
				</embed> 
				</object>
	
				<br>
				
				<font size="-1">
				[ <a href="<?= $full ?>">External display</a> ]
				<br>
				Players: 
					<a href="http://www.mplayerhq.hu/homepage/">Linux MPlayer</a>
					|
					<a href="http://www.microsoft.com/windows/windowsmedia/download/">Windows Media</a>
				</font>
			<?
			}
		}
		else if ($subtype == "mpeg")
		{
			// ----------------------------------------
			// -------------- MPEG --------------------
			// ----------------------------------------
	
			if ($_test_==4)
			{
				?>
			
				<object data="<?= $full ?>" type="video/mpeg" />
			
				<?
			}
			else
			{
				?>
					<embed
						src="<?= $full ?>"
						width="<?= $sx ?>" height="<?= $sy ?>"
					>
					</embed>
				<?
			}
		}
		else if ($subtype == "quicktime")
		{
			// ----------------------------------------
			// -------------- QuickTime ---------------
	
			// ----------------------------------------

			// QuickTime EMBED attributes are described here:
			// http://www.apple.com/quicktime/authoring/embed2.html
			//
			// QuickTime OBJECT tag:
			// http://www.apple.com/quicktime/tools_tips/tutorials/activex.html

			// for QT, add 16 to the height to see the controls (cf doc above)
			$sy2 = $sy+16;

			


	
			/*
				The following EMBED attributes are supposedly supported but break the QT player
				when used:
					type="video/quicktime"
					qtsrc="<?= $full ?>"
					qtsrcdontusebrowser
	
				codebase="http://www.apple.com/qtactivex/qtplugin.cab">
			*/
	
	if ($_test_==4)
	{
		?>
	
		<object data="<?= $full ?>" type="video/quicktime" />
	
		<?
	}
	else if ($_test_==3)
	{
	
			?>
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy2 ?>"
					controller="true"
					scale="aspect"
					autohref="yes"
					autoplay="yes"
				>
				</embed>
				
				<br>
				
				<font size="-1">
				[ <a href="<?= $full ?>">External display</a> ]
				<br>
				Players: 
					<a href="http://www.mplayerhq.hu/homepage/">Linux MPlayer</a>
					|
					<a href="http://www.apple.com/quicktime/download/">Apple Quicktime</a>
					|
					<a href="http://www.microsoft.com/windows/windowsmedia/download/">Windows Media</a>
				</font>
			<?
	}
	else if ($_test_==2)
	{
//	<script language="JavaScript" type="text/javascript" src="browser_detect.js"></script>

	    ?>
	
	
	<script type="text/javascript" language="JavaScript">

alert("here");
document.write("is_ie4up = " + is_ie4up + " -- is_win32 = " + is_win32 + "<br>");
	
	document.write("BROWSER: ")
	document.write(navigator.appName + "<br>")
	document.write("BROWSERVERSION: ")
	document.write(navigator.appVersion + "<br>")
	document.write("CODE: ")
	document.write(navigator.appCodeName + "<br>")
	document.write("PLATFORM: ")
	document.write(navigator.platform + "<br>")
	
		document.write('<embed')
		document.write('src="<?= $full ?>"')
		document.write('width="<?= $sx ?>" height="<?= $sy2 ?>"')
		document.write('controller="true"')
		document.write('scale="aspect"')
		document.write('autohref="yes"')
		document.write('autoplay="yes"')
		document.write('pluginspage="http://www.apple.com/quicktime/download/">')
		document.write('</embed>')
	
	
	<noscript>
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy2 ?>"
					controller="true"
					scale="aspect"
					autohref="yes"
					autoplay="yes"
					pluginspage="http://www.apple.com/quicktime/download/"
				>
				</embed>
	</noscript>
	</script>
	
	    <?php
	}
	else
	{
			?>
				<object classid="clsid:02bf25d5-8c17-4b23-bc80-d3488abddc6b"
					codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0"
					width="<?= $sx ?>" height="<?= $sy ?>">
					<param name="src" value="sample.mov">
					<param name="autoplay" value="true">
					<param name="controller" value="true">
					<param name="autohref" value="true">
					<param name="scale" value="aspect">
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy2 ?>"
					controller="true"
					scale="aspect"
					autohref="yes"
					autoplay="yes"
					pluginspage="http://www.apple.com/quicktime/download/"
				>
				</embed>
				</object>
				
				<br>
				
				<font size="-1">
				[ <a href="<?= $full ?>">External display</a> ]
				<br>
				Players: 
					<a href="http://www.mplayerhq.hu/homepage/">Linux MPlayer</a>
					|
					<a href="http://www.apple.com/quicktime/download/">Apple Quicktime</a>
					|
					<a href="http://www.microsoft.com/windows/windowsmedia/download/">Windows Media</a>
				</font>
			<?
		}
	} // non-javascript test
	} // if video

    // debug
    // echo "<br>rig_img_size = '$rig_img_size'<br>\n";
    // echo "preview = '$preview'<br>\n";
}


//*******************************
function rig_display_image_info()
//*******************************
{
	global $current_album;
	global $current_image;
	global $current_img_info;
	global $html_pixels, $html_image2;

	if ($current_img_info)
		$res = $current_img_info;
	else
		$res = rig_build_info($current_album, $current_image);

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


//******************************
function rig_insert_size_popup()
//******************************
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
	global $admin;
	global $translate;
	global $html_desc_lang;
	global $html_language;
	global $current_language;
	global $pref_disable_web_translate_interface;

	$sep = FALSE;

	echo "<tr><td align=\"right\"><a name=\"lang\">$html_language</td><td> \n";

	foreach($html_desc_lang as $key => $value)
	{
		if ($sep)
			echo "&nbsp;|&nbsp;";

		if ($current_language == $key)
		{
			if (isset($pref_disable_web_translate_interface) && $pref_disable_web_translate_interface)
			{
				echo $value;
			}
			else
			{
				// if in admin mode and not already in translate mode, display the edit language link
				// RM 20030308 TBT -- Translate "Edit"
				if ($admin && !$translate)
				{
					echo " [<a href=\"" . rig_self_url(-1, -1, RIG_SELF_URL_TRANSLATE, "lang=$key#lang") . "\">Edit $value</a>] \n";
				}
				else if ($admin && $translate)
				{
					echo " [<a href=\"" . rig_self_url(-1, -1, RIG_SELF_URL_TRANSLATE, "lang=$key#lang") . "\">Reload $value</a>] \n";
					echo " [<a href=\"" . rig_self_url(-1, -1, RIG_SELF_URL_ADMIN, "lang=$key#lang") . "\">Exit Edit $value</a>] \n";
				}
				else
				{
					echo $value;
				}
			}
		}
		else
		{
			echo "<a href=\"" . rig_self_url(-1, -1, -1, "lang=$key#lang") . "\">$value</a>\n";
		}

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

	echo "<tr><td align=\"right\"><a name=\"theme\">$html_theme</td><td> \n";

	foreach($html_desc_theme as $key => $value)
	{
		if ($sep)
			echo "&nbsp;|&nbsp;";

		if ($current_theme == $key)
			echo $value;
		else
			echo "<a href=\"" . rig_self_url(-1, -1, -1, "theme=$key#theme") . "\">$value</a>\n";

		$sep = TRUE;
	}

	echo "</td></tr>";
}


//***************************************
function rig_display_back_to_album($link)
//***************************************
{
	global $html_back_to;
	global $display_album_title;

	$link = "<a href=\"$link\">$display_album_title</a>";

	// replace "[name]" in the html string by the full link
	echo str_replace("[name]", $link, $html_back_to);
}


//-----------------------------------------------------------------------


//************************************
function rig_display_album_copyright()
//************************************
// RM 20030119 v0.6.3
{
	global $html_album_copyrt;
	global $pref_copyright_name;

	if ($pref_copyright_name <> "")
	{
		$str = $html_album_copyrt;

		// replace "[year]" in the html string by the current's year date (aka. "2003")
		$str = str_replace("[year]", date("Y"), $str);

		// replace "[name]" in the html string by the pref name
		$str = str_replace("[name]", $pref_copyright_name, $str);
	
		echo $str;
	}
}


//************************************
function rig_display_image_copyright()
//************************************
// RM 20030119 v0.6.3
{
	global $html_image_copyrt;
	global $pref_copyright_name;

	if ($pref_copyright_name <> "")
	{
		$str = $html_image_copyrt;

		// RM 20030625 missing for [year] replacement for image
		// replace "[year]" in the html string by the current's year date (aka. "2003")
		$str = str_replace("[year]", date("Y"), $str);

		// replace "[name]" in the $html by the $pref name
		$str = str_replace("[name]", $pref_copyright_name, $str);
	
		echo $str;
	}
}


//-----------------------------------------------------------------------

//******************************************************
function rig_display_credits($has_credits, $has_phpinfo)
//******************************************************
{
	global $admin;
	global $_debug_;
	global $html_text_credits;
	global $html_hide_credits;
	global $html_show_credits;
	global $html_credits;

	global $html_show_phpinfo;
	global $html_hide_phpinfo;
	global $html_phpinfo;

	global $color_section_bg;

	// link to show or hide the credits
	$v = ($has_credits == "on" ? "off" : "on");
	$l = ($has_credits == "on" ? $html_hide_credits : $html_show_credits);
	echo "<a name=\"credits\" href=\"" . rig_self_url(-1, -1, -1, "credits=$v#credits") . "\" target=\"_top\">$l</a><br>";

	// link to show or hide the PHP Info
	// RM 20030118 this is only available in _debug_ or admin, not longuer in normal mode
	if ($_debug_ || $admin)
	{
		$v = ($has_phpinfo == "on" ? "off" : "on");
		$l = ($has_phpinfo == "on" ? $html_hide_phpinfo : $html_show_phpinfo);
		echo "<a name=\"phpinfo\" href=\"" . rig_self_url(-1, -1, -1, "phpinfo=$v#phpinfo") . "\" target=\"_top\">$l</a><br>";
	}

	// actually display the credits if activated
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

	// actually display the PHP info if activated
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
	global $display_exec_date;
	global $display_softname;
	global $html_generated;
	global $html_seconds;
	global $html_the;
	global $html_by;
	global $color_section_bg;
	global $color_section_text;

	$sgen = str_replace("[time]", rig_time_elapsed(), $html_generated);
	$sgen = str_replace("[date]", $display_exec_date, $sgen);
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
	// RM 20030119 obsolete since it can be done directly from the page now
    // if ($_debug_)
    // {
	//     phpinfo(INFO_VARIABLES);
    //     phpinfo(INFO_ENVIRONMENT);
    // }
    
    // RM 20030713 javascript page debug
    if ($_debug_)
    {
?>

</center>
<b>Navigator Object Data</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--

document.write("navigator.appCodeName: " + navigator.appCodeName + "<BR>");
document.write('<A HREF="http://www.webreference.com/js/column6/"><code>navigator.appName<\/code><\/A>: ' + navigator.appName + "<BR>");
document.write('<A HREF="http://www.webreference.com/js/column6/"><code>navigator.appVersion<\/code><\/A>: ' + navigator.appVersion + "<BR>");
document.write("navigator.userAgent: " + navigator.userAgent + "<BR>");

document.write("navigator.platform: " + navigator.platform + "<BR>");
document.write("navigator.javaEnabled(): " + is_java + "<BR>");
//-->
</script>

</tt>

<p>
<b>Version Number</b>
<br><tt><script LANGUAGE="JavaScript">
<!--
        if (is_opera) {
           document.write("<TT>***Version numbers here are only valid</TT><BR>");
           document.write("<TT>***if Opera is set to identify itself as Opera</TT><BR>");
           document.write("<TT>***use is_opera vars instead</TT><BR>");
        }
	document.write("<TT>major version number: " + is_major + "</TT><BR>");
	document.write("<TT>major/minor version number: " + is_minor + "</TT><BR>");
//--></script>
</tt>
<p>

<b>Browser Version</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write("nav:" + is_nav + "<BR>");
	document.write("nav2:" + is_nav2 + "<BR>");
	document.write("nav3:" + is_nav3 + "<BR>");
	document.write("nav4:" + is_nav4 + "<BR>");
	document.write("nav4up:" + is_nav4up + "<BR>");
	document.write("nav5:" + is_nav5 + "<BR>");
	document.write("nav5up:" + is_nav5up + "<BR>");
	document.write("nav6:" + is_nav6 + "<BR>");
	document.write("nav6up:" + is_nav6up + "<BR>"); // 001121 new - abk
	document.write("nav7:" + is_nav7 + "<BR>"); 
	document.write("nav7up:" + is_nav7up + "<BR>");
//	document.write("navonly:" + is_navonly + "<BR>");
// is false in ns6?
	document.write("<P>ie:" + is_ie + "<BR>");
	document.write("ie3:" + is_ie3 + "<BR>");
	document.write("ie4:" + is_ie4 + "<BR>");
	document.write("ie4up:" + is_ie4up + "<BR>");
	document.write("ie5:" + is_ie5 + "<BR>");
	document.write("ie5up:" + is_ie5up + "<BR>");
 	document.write("ie5_5:" + is_ie5_5 + "<BR>");
    	document.write("ie5_5up:" + is_ie5_5up + "<BR>");
	document.write("ie6:" + is_ie6 + "<BR>");
	document.write("ie6up:" + is_ie6up + "<BR>");

	document.write("<P>aol:" + is_aol + "<BR>");
	document.write("aol3:" + is_aol3 + "<BR>");
	document.write("aol4:" + is_aol4 + "<BR>");
	document.write("aol5:" + is_aol5 + "<BR>");
	document.write("aol6:" + is_aol6 + "<BR>");
	document.write("aol7:" + is_aol7 + "<BR>"); // 020214 - dmr
        document.write("aol8:" + is_aol8 + "<BR>");
	
	document.write("<P>opera:" + is_opera + "<BR>");
	document.write("opera2:" + is_opera2 + "<BR>");
	document.write("opera3:" + is_opera3 + "<BR>");
	document.write("opera4:" + is_opera4 + "<BR>");
	document.write("opera5:" + is_opera5 + "<BR>");
	document.write("opera5up:" + is_opera5up + "<BR>");
	document.write("opera6:" + is_opera6 + "<BR>");
	document.write("opera6up:" + is_opera6up + "<BR>");
	document.write("opera7:" + is_opera7 + "<BR>");
	document.write("opera7up:" + is_opera7up + "<BR>");

        document.write("<P>safari:" + is_safari + "<BR>");

        document.write("<P>konqueror:" + is_konq + "<BR>");

        document.write("<P>Gecko based: " + is_gecko + "<BR>");
        if (is_gecko) {
           document.write("Gecko build: " + is_gver + "<BR>");
        }

	document.write("<P>mozilla (guessing): " + is_moz + "<BR>");
	if (is_moz) {
           document.write("mozilla version (guessing): " + is_moz_ver + "<BR>");
	}

	document.write("<P>" + "webtv:" + is_webtv + "<BR>");
	document.write("<P>" + "hotjava:" + is_hotjava + "<BR>");
	document.write("hotjava3:" + is_hotjava3 + "<BR>");
	document.write("hotjava3up:" + is_hotjava3up + "<BR>");
	document.write("<P>" + "AOL TV(TVNavigator):" + is_TVNavigator + "<BR>");
//-->
</script>
</tt>
<p>
<b>JavaScript Version</b>
<br><tt>
<script LANGUAGE="JavaScript">
<!--
        if (is_opera) {
           if (is_opera7up) {
              document.write("js:" + is_js + " <a href='http://www.opera.com/docs/specs/opera07/#ecmascript'>Opera compatibility statement</a>");
           } else {
              document.write("js:" + is_js + " <a href='http://www.opera.com/docs/specs/#ecmascript'>(but see Opera's compatibility statements)</a>");
           }
        } else {
           document.write("js:" + is_js + "<BR>");
        }
//-->
</script>

<script LANGUAGE=JScript>
<!--
// 020131 included is_ie check to filter opera which doesn't recognize
// ScriptEngine() and spawns an error - dragle
if((document.all) && (is_ie)) {
	document.write("<P>IE 4/5/6 Script Engines Installed: " + ScriptEngine() + "<BR>");
	document.write("Version: " + ScriptEngineMajorVersion() + "." + ScriptEngineMinorVersion() + "." + ScriptEngineBuildVersion() + "<BR>");
}
//-->
</SCRIPT>
</tt>

<p>
<b>OS</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write("win:" + is_win + "<BR>");
//	document.write("win16:" + is_win16 + "<BR>");
//	document.write("win31:" + is_win31 + "<BR>");
//	document.write("win32:" + is_win32 + "<BR>");
//	document.write("win95:" + is_win95 + "<BR>");
//	document.write("win98:" + is_win98 + "<BR>");
	//	document.write("winme:" + is_winme + "<BR>");
//	document.write("winnt:" + is_winnt + "<BR>");
	//	document.write("win2k:" + is_win2k + "<BR>");
//      document.write("winxp:" + is_winxp + "<BR>");

	document.write("os2:" + is_os2 + "<BR>");

	document.write("mac:" + is_mac + "<BR>");
//	document.write("mac68k:" + is_mac68k + "<BR>");
//	document.write("macppc:" + is_macppc + "<BR>");

	document.write("unix:" + is_unix + "<BR>");
	document.write("sun:" + is_sun + "<BR>");
//	document.write("sun4:" + is_sun4 + "<BR>");
//	document.write("sun5:" + is_sun5 + "<BR>");
//	document.write("suni86:" + is_suni86 + "<BR>");
	document.write("irix:" + is_irix + "<BR>");
//	document.write("irix5:" + is_irix5 + "<BR>");
//	document.write("irix6:" + is_irix6 + "<BR>");
	document.write("hpux:" + is_hpux + "<BR>");
//	document.write("hpux9:" + is_hpux9 + "<BR>");
//	document.write("hpux10:" + is_hpux10 + "<BR>");
	document.write("aix:" + is_aix + "<BR>");
//	document.write("aix1:" + is_aix1 + "<BR>");
//	document.write("aix2:" + is_aix2 + "<BR>");
//	document.write("aix3:" + is_aix3 + "<BR>");
//	document.write("aix4:" + is_aix4 + "<BR>");
	document.write("linux:" + is_linux + "<BR>");
	document.write("sco:" + is_sco + "<BR>");
	document.write("unixware:" + is_unixware + "<BR>");
	document.write("mpras:" + is_mpras + "<BR>");
	document.write("reliant:" + is_reliant + "<BR>");
	document.write("dec:" + is_dec + "<BR>");
	document.write("sinix:" + is_sinix + "<BR>");
	document.write("bsd:" + is_bsd + "<BR>");
	document.write("freebsd:" + is_freebsd + "<BR>");

	document.write("vms:" + is_vms + "<BR>");
//-->
</script>
</tt>

<p>
<b>Object Detection Tests</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write('<A HREF="/dhtml/diner/browsvars/">document.all<\/A>: ' + is_all + "<BR>");
	document.write('document.anchors: ' + is_anchors + "<BR>");
	document.write('<A HREF="http://webreference.com/js/column8/">document.cookie<\/A>: ' + is_cookie + "<BR>");
	document.write('<a href="http://www.webreference.com/programming/javascript/beginning/chap6/1/2.html">document.forms</a>: ' + is_forms + "<BR>");
	document.write('<A HREF="http://www.webreference.com/programming/javascript/domscripting/1/">document.getElementById</A>: ' + is_getElementById + "*<BR>"); // new 001121 abk for ns6+
	document.write('<A HREF="http://www.webreference.com/programming/javascript/domscripting/1/3.html">document.getElementsByTagName</A>: ' + is_getElementsByTagName + "<BR>");
	document.write('<A HREF="http://www.webreference.com/js/column75/3.html">document.documentElement</A>: ' + is_documentElement + "<BR>");

	document.write('<A HREF="http://webreference.com/dhtml/column1/">document<\/A>.<A HREF="http://www.webreference.com/js/column1/">images<\/A>: ' + is_images + "<BR>");
document.write('<A HREF="/dhtml/diner/browsvars/">document.layers<\/A>: ' + is_layers + " - NS6 gives false here**" + "<BR>");
// ' + is_layers + "<BR>");
	document.write('<A HREF="http://www.webreference.com/js/column16/byurl.html">document.links</A>: ' + is_links + "<BR><BR>");
	document.write('<A HREF="http://webreference.com/js/column36/">window.frames<\/A>: ' + is_frames + "<BR>");
	document.write('window.length: ' + window.length +"<BR>");

//-->
</script>
</tt>

<p>

<b>Method Detection Tests</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write('<A HREF="http://www.webreference.com/js/column5/">window.RegExp<\/A>: ' + is_regexp + "<BR>");
	document.write('<A HREF="/dev/menus/">window.Option<\/A>: ' + is_option + "<BR>");

//-->
</script>

</tt>

<p>
<b>Screen Properties</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write('<A HREF="http://webreference.com/js/column17/">window.screen</A>: ' + is_screen + "<BR>");
if (window.screen) {
	document.write('screen.height: ' + screen.height + "<BR>");
	document.write('screen.width: ' + screen.width + "<BR>");
	document.write('screen.availHeight: ' + screen.availHeight + "<BR>");
	document.write('screen.availWidth: ' + screen.availWidth + "<BR>");
	document.write('screen.colorDepth: ' + screen.colorDepth + "<BR>");
}
//-->
</script>


<script LANGUAGE=JScript>
<!--
if (window.screen) {
	document.write("fontSmoothingEnabled: " + screen.fontSmoothingEnabled + "<BR>");
}
//-->

</SCRIPT>
</tt>

<p>
<b>Document Properties</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
//	document.write('document.lastModified: ' + document.lastModified + "<BR>"); // gecko bug?
//	document.write('"Not Your Business!": ' + "Not Your Business!" + "<BR>");
	document.write('document.URL: ' + document.URL + "<BR>");
//-->
</script>

</tt>

<p>
<b>Flash Detection</b>
<br>
<tt><script LANGUAGE="JavaScript">
<!--
	document.write('Flash Player Present: ');
        if (is_Flash) {
           document.write("true<BR>");
           document.write("Player Version: " + is_FlashVersion);
        } else {
           document.write("Can't Tell");
        }
//-->

</script>
</tt>

<center>
<?php

    }
}

//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.22  2003/08/15 07:14:02  ralfoide
//	Album HTML cache generation, image thumbnail borders
//
//	Revision 1.21  2003/07/21 04:58:26  ralfoide
//	Tooltips that work with Mozilla (using title attribute); Date description in grid albums and tooltips; Small preview for vertical album layout; Auto-switch album layout on description presence.
//	
//	Revision 1.20  2003/07/19 07:52:36  ralfoide
//	Vertical layout for albums
//	
//	Revision 1.19  2003/07/14 18:32:23  ralfoide
//	New album frame table, support for descriptions, javascript testing
//	
//	Revision 1.18  2003/07/11 15:56:38  ralfoide
//	Fixes in video html tags. Added video/mpeg mode. Experimenting with Javascript
//	
//	Revision 1.17  2003/06/30 06:08:11  ralfoide
//	Version 0.6.3.4 -- Introduced support for videos -- new version of rig_thumbnail.exe
//	
//	Revision 1.16  2003/05/26 17:52:55  ralfoide
//	Removed unused language strings. Added new rig_display_back_to_album method
//	
//	Revision 1.15  2003/03/17 08:24:43  ralfoide
//	Fix: added pref_disable_web_translate_interface (disabled by default)
//	Fix: added pref_disable_album_borders (enabled by default)
//	Fix: missing pref_copyright_name in settings/prefs.php
//	Fix: outdated pref_album_copyright_name still present. Eradicated now :-)
//	
//	Revision 1.14  2003/03/12 07:02:08  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.13  2003/02/17 07:47:03  ralfoide
//	Debugging. Fixed album visibility not being used correctly
//	
//	Revision 1.12  2003/02/16 20:22:55  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.11  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
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
