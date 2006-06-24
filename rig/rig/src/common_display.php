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

//*********************************
function rig_display_header($title)
//*********************************
{
	rig_display_header_start($title);
	rig_display_header_close();
}


//***************************************
function rig_display_header_start($title)
//***************************************
{
	global $html_encoding;
	global $html_language_code;
	global $theme_css_head;
	global $pref_html_meta;

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


	$admin = rig_get($_GET,'admin');

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
<?php
}

//*********************************
function rig_display_header_close()
//*********************************
{
echo "\n</head>\n";
}


//-------------------------------------------------


//************************************
function rig_display_body($extra = "")
//************************************
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
	  <?= $extra ?>
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

	$parent = rig_get_parent_album($current_album);
	if ($parent)
	{
		// write the link
		echo "<a href=\"" . rig_self_url("", $parent) . "\">$html_back_previous</a>\n";
	}
}


//*******************************
function rig_display_album_list()
//*******************************
{
	global $dir_images;
	global $html_images;
	global $list_albums;
	global $html_album;
	global $current_album;
	global $list_description;					// RM 20030713
	global $list_albums_count;
	global $current_album_page;					// RM 20030908
	global $max_album_page;						// RM 20030908
	global $pref_album_nb_col;
	global $pref_preview_size;
	global $pref_preview_quality;
	global $pref_small_preview_size;			// RM 20030720 for 'vert' layout
	global $pref_small_preview_quality;
	global $pref_album_layout;					// RM 20030718 'grid' or 'vert'
	global $pref_album_with_description_layout;	// RM 20030720 auto-switch
	global $pref_enable_album_border;			// RM 20030814
	global $color_table_desc;					// RM 20030817
	global $html_last_update;					// RM 20040302 v0.6.4.5 i18l strings
	global $html_image_tooltip;
	global $html_album_tooltip;


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

	$m = is_integer($list_albums_count) ? $list_albums_count : count($list_albums);

	// only one item per line if not in grid layout

	if ($layout != 'grid')
	{
		$n = 1;
		$p = $pref_preview_size;
		$w_td = " width=\"$p\" valign=\"top\" align=\"center\"";
	}
	else
	{
		$n = $pref_album_nb_col;

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


	// -- pagination info --

	// use the number of columns ($n) to get the current and
	// max number of pages ($current_album_page / $max_album_page)
	$thumb_per_page = rig_max_album_page($n);

	if ($thumb_per_page > 0 && $current_album_page > 0)
	{
		$min_index = $thumb_per_page * ($current_album_page - 1);
		$max_index = $min_index + $thumb_per_page - 1;
	}
	else
	{
		$min_index = 0;
		$max_index = $m - 1;
	}

	// --

	if ($current_album_page > 0)
	{
		echo "<tr><td colspan=\"$n\" align=\"right\">Page: \n";
		rig_display_pager($current_album_page, $max_album_page, TRUE);
		echo "</td></tr>\n";
	}
	
	echo "<tr>\n";

	$i = 0;
	
	// RM 20030913 note: foreach($list as $index => $val) is not used
	// as there is no guarantee the first item index be 0 or 1 (by construction
	// it should be 1 though that may change later...). So let's use our own counter.
	$item_index = -1;

	foreach($list_albums as $dir)
	{
		$name = rig_post_sep($current_album) . $dir;

		// continue if thumbnail is visible

		if (!rig_is_visible(-1, $dir))
			continue;

		// continue if thumbnail fits in current page

		$item_index++;

		if ($item_index < $min_index)
			continue;

		if ($item_index > $max_index)
			break;

		// in page and visible... continue

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
		
		$alt = str_replace('[type]', $html_album, $html_image_tooltip);
		$alt = str_replace('[name]', $pretty,     $alt);

		$tooltip = str_replace('[type]', $html_album, $html_album_tooltip);
		$tooltip = str_replace('[name]', $pretty,     $tooltip);
		$tooltip = str_replace('[date]', $album_date, $tooltip);
		

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
		$res = rig_build_album_preview($name, $preview_size, $preview_quality);
		$abs_path = $res["a"];
		$url_path = $res["u"]; // already url-escaped 

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

			if (isset($pref_enable_album_border) && !$pref_enable_album_border)
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
				$box_tr = rig_post_sep($dir_images) . "album_topright.gif";
				$box_br = rig_post_sep($dir_images) . "album_bottomright.gif";
				$box_bl = rig_post_sep($dir_images) . "album_bottomleft.gif";
				$line_b = rig_post_sep($dir_images) . "album_bottomline.gif";
				$line_r = rig_post_sep($dir_images) . "album_rightline.gif";

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
				<td align="right"><font color="<?= $color_table_desc ?>" size="-1"><?= $album_date ?></font></td>
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
			$last_update = str_replace('[date]', $album_date, $html_last_update);
			
			echo "</td></tr>\n";
			echo "<tr><td><center>$title</center></td></tr>\n";
			echo "<tr><td title=\"$last_update\"><center><font color=\"$color_table_desc\" size=\"-1\"><span>$album_date</span></font></center></td></tr>\n";
			echo "<tr><td><center><font color=\"$color_table_desc\">$desc</font></center></td></tr>\n";
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
		}
		

		rig_flush();
	}

	echo "</tr>\n";
	
	//--

	if ($current_album_page > 0)
	{
		echo "<tr><td colspan=\"$n\" align=\"right\">Page: \n";
		rig_display_pager($current_album_page, $max_album_page, TRUE);
		echo "</td></tr>\n";
	}
}


//*******************************
function rig_display_image_list()
//*******************************
{
	// output should be like:
    // <!-- tr>
    //	<td width="20%" align="center">img1</td>
	// </tr -->

	global $pref_image_nb_col;
	global $dir_images;
	global $list_images;
	global $html_image;
	global $current_real_album;					// RM 20030907
	global $list_description;					// RM 20030713
	global $list_images_count;
	global $current_image_page;					// RM 20030908
	global $max_image_page;
	global $pref_preview_size;
	global $pref_enable_image_border;			// RM 20030814

	$i = 0;
	$n = $pref_image_nb_col;
	$m = is_integer($list_images_count) ? $list_images_count : count($list_images);
	if ($m < $n)
		$n = $m;

	$p = (int)(100/$n);
	$w = " width=\"$p%\" valign=\"top\" align=\"center\"";

	// -- pagination info --

	// use the number of columns ($n) to get the current and
	// max number of pages ($current_image_page / $max_image_page)
	$thumb_per_page = rig_max_image_page($n);

	if ($thumb_per_page > 0 && $current_image_page > 0)
	{
		$min_index = $thumb_per_page * ($current_image_page - 1);
		$max_index = $min_index + $thumb_per_page - 1;
	}
	else
	{
		$min_index = 0;
		$max_index = $m - 1;
	}

	// --

	if ($current_image_page > 0)
	{
		echo "<tr><td colspan=\"$n\" align=\"right\">Page: \n";
		rig_display_pager($current_image_page, $max_image_page, FALSE);
		echo "</td></tr>\n";
	}

	echo "<tr>\n";

	// RM 20030913 note: foreach($list as $index => $val) is not used
	// as there is no guarantee the first item index be 0 or 1 (by construction
	// it should be 1 though that may change later...). So let's use our own counter.
	$item_index = -1;

	foreach($list_images as $file)
	{
		// continue if thumbnail is visible

		if (!rig_is_visible(-1, -1, $file))
			continue;

		// continue if thumbnail fits in current page
		
		$item_index++;

		if ($item_index < $min_index)
			continue;

		if ($item_index > $max_index)
			break;


		// is this the last line? [RM 20021101]
		$is_last_line = ($item_index >= $max_index - $n);

		$pretty1 = rig_pretty_name($file);
		$pretty2 = rig_pretty_name($file, FALSE);

		$info = rig_build_preview_info($current_real_album, $file);
		$preview = $info["u"]; //[u] is already url-escaped

		if (isset($info["w"]))
			$width = "width=\"" . $info["w"] . "\"";
		else
			$width = "";
	
		if (isset($info["h"]))
			$height = "height=\"" . $info["h"] . "\"";
		else
			$height = "";


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
						if (isset($pref_enable_image_border) && !$pref_enable_image_border)
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
					
							$dx = $pref_preview_size;
							$dy = $pref_preview_size;

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
							$box_tr = rig_post_sep($dir_images) . "image_topright.gif";
							$box_br = rig_post_sep($dir_images) . "image_bottomright.gif";
							$box_bl = rig_post_sep($dir_images) . "image_bottomleft.gif";
							$line_b = rig_post_sep($dir_images) . "image_bottomline.gif";
							$line_r = rig_post_sep($dir_images) . "image_rightline.gif";
			
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

	// --

	if ($current_image_page > 0)
	{
		echo "<tr><td colspan=\"$n\" align=\"right\">Page: \n";
		rig_display_pager($current_image_page, $max_image_page, FALSE);
		echo "</td></tr>\n";
	}

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



//*****************************************************************
function rig_display_pager($curr_page, $max_page, $is_album = TRUE)
//*****************************************************************
{
	// nothing to do if not at least positionned on page 1
	if ($curr_page < 1)
		return;

	$v = @array();

	// build an array of 'interesting' values:
	// - always page 1
	// - always last page
	// - always pages -10 / -5 / -2 / -1 / 0 / +1 / +2 / +5 / +10

	$v[] = 1;

	if ($max_page > 1)
		$v[] = $max_page;

	$p = array(-10, -5, -2, -1, 0, 1, 2, 5, 10,15,20,30,40,50);
	foreach($p as $d)
	{
		$n = $curr_page + $d;
		if ($n > 0 && $n <= $max_page)
		{
			$k = rig_php_array_search($n, $v);

			if ($k === FALSE)
				$v[] = $n;
		}
	}

	// sort numbers now
	sort($v);

	// display prev

	$last = 0;

	// RM 20040708 test: temporarily removed the pager_index, forcing the links to
	// always go to the top pager
	// global $pager_index;
	// $pager_index++;
	$pager_index = '';
	$pname = "pag" . $pager_index . ($is_album ? "a" : "i");
	echo "<a name=\"$pname\"></a>";
	$pname = "#" . $pname;

	if ($curr_page > 1)
	{
		$last = $curr_page-1;
		$u = rig_self_url(-1, -1, -1, $pname, ($is_album ? $last : -1), ($is_album ? -1 : $last));
		echo "<a href=\"$u\">Prev</a>&nbsp;";
	}		

	// and display links
	foreach($v as $n)
	{
		if ($last > 0)
			if ($n > $last+1)
				echo "&nbsp;...&nbsp;";
			else
				echo "&nbsp;|&nbsp;";
		
		if ($n == $curr_page)
		{
			// RM 20040708 test: force 2 more non-breakable spaces around
			echo "&nbsp;&lt;$n&gt;&nbsp;";
		}
		else
		{
			$u = rig_self_url(-1, -1, -1, $pname, ($is_album ? $n : -1), ($is_album ? -1 : $n));
			// RM 20040708 test: force 2 more non-breakable spaces around
			echo "<a href=\"$u\">&nbsp;$n&nbsp;</a>";
		}
		
		$last = $n;
	}

	if ($curr_page < $max_page)
	{
		if ($last > 0)
			echo "&nbsp;|&nbsp;";
		
		$last = $curr_page+1;
		$u = rig_self_url(-1, -1, -1, $pname, ($is_album ? $last : -1), ($is_album ? -1 : $last));
		echo "&nbsp;<a href=\"$u\">Next</a>";
	}
}


//-----------------------------------------------------------------------


//***************************************************************
function rig_display_album_thumb($size = FALSE, $quality = FALSE)
//***************************************************************
// This function streams the album icon to the browser.
// If the precomputed album icon can't be used, a default gif icon is streamed.
{
	global $current_real_album;

	if ($quality === FALSE)
		$quality = -1;
	if ($size === FALSE)
		$size = -1;

	$res = rig_build_album_preview($current_real_album);
	$abs = $res["a"];
	if (rig_is_file($abs))
	{
		header("Content-type: " . $type);
		header("Content-Length: " . filesize($abs));
		header("Content-Disposition: filename=thumb_$current_real_album.jpg");	// RM 20060624 v 1.0

		readfile($abs);

		return;
	}

	// In case of error, return a default icon.
	global $dir_images;
	global $dir_abs_base;
	global $pref_empty_album;
	$abs = realpath(rig_post_sep($dir_abs_base) . $dir_images . $pref_empty_album);
	header("Content-type: image/gif");
	header("Content-length: " . filesize($abs));
	echo file_get_contents($abs);
}


//***************************************************************
function rig_display_image_thumb($size = FALSE, $quality = FALSE)
//***************************************************************
// This function is misnamed: it streams an "image" into the browser.
//
// For regular images, this can be either a preview thumbnail or a resized
// version of the image.
// - Size is the largest pixel size of the image. -1 generally means to use
//   the "preview size" (i.e. thumbnails), whereas FALSE means to use the
//   default image size.
// - Quality is a value between 1-100 for the JPEG quality.
//
// For video media, it's a bit confusing since this is called to stream
// either the preview thumbnail or the actual movie .
// - Size has the same meaning than for images. The actualy pixel size is
//   is ignored since movies are not resized on the fly. A value of -1 means
//   to render the thumbnail preview, anything else means to stream the
//   actual movie file.
// - Quality is ignored. We don't reencode videos on the fly.
//
// If the actual file can't be found, a default gif icon is streamed, which
// is of course totally irrelevant for videos (TODO: have a default one-frame
// stream that says "video missing" that we could stream instead.)
{
	global $rig_img_size;
	global $current_image;
	global $current_real_album;
	global $pref_image_quality;

	if ($quality === FALSE)
		$quality = $pref_image_quality;
	if ($size === FALSE)
		$size = $rig_img_size;

	// get the file type
	$type = rig_get_file_type($current_image);

	// DEBUG
	// echo "<p> size $size, quality $quality, type $type\n";

	if ($size == -1 || strncmp($type, "image/", 6) == 0)
	{
		// If size is -1, rig_build_preview_info will know how to create a thumbnail
		// no matter what the input datatype is.
		// Otherwise it will rescale and render an image as appropriate.

		$res = rig_build_preview_info($current_real_album, $current_image, $size, $quality);

		$abs = $res["a"];
		if (rig_is_file($abs))
		{
			header("Content-type: " . ($size == -1 ? "image/jpeg" : $type));
			header("Content-length: " . filesize($abs));
			$base = "";
			if ($size == -1)
				$base = "thumb_";
			else if ($size == -2)
				$base = "full_";
			else
				$base = "img_$size" . "px_";
			header("Content-Disposition: filename=$base$current_image");	// RM 20060624 v 1.0
			echo file_get_contents($abs);
			return;
		}
	}
	else if (strncmp($type, "video/", 6) == 0)
	{
		// Stream an actual data file.
		if (rig_stream_video($type))
			return;
	}

	// In case of error, return a default icon.
	global $dir_images;
	global $dir_abs_base;
	global $pref_empty_album;
	$abs = realpath(rig_post_sep($dir_abs_base) . $dir_images . $pref_empty_album);
	header("Content-type: image/gif");
	header("Content-length: " . filesize($abs));
	echo file_get_contents($abs);
}


//-----------------------------------------------------------------------


//**************************
function rig_display_image()
//**************************
{
	global $abs_album_path;
	global $current_real_album;		// RM 20030907
	global $current_image;
	global $pretty_image;
	global $rig_img_size;
	global $pref_image_size;
	global $pref_image_quality;

	if ($rig_img_size != -2 && $rig_img_size < 1)
		$rig_img_size = $pref_image_size;

	// get the file type
	$type = rig_get_file_type($current_image);

	if (strncmp($type, "image/", 6) == 0)
	{
		// get image (build resized preview if necessary)
		$res = rig_build_preview_info($current_real_album, $current_image, $rig_img_size, $pref_image_quality);
		$link = $res["u"]; // already url-encoded
		
		if (isset($res["w"]) && isset($res["h"]))
		{
			// if we have the size, use it in the img tag
			$sx = $res["w"];
			$sy = $res["h"];
	
			echo "<img id=\"content-img\" src=\"$link\" alt=\"$pretty_image\" title=\"$pretty_image\" border=0 width=\"$sx\" height=\"$sy\">";
		}
		else
		{
			// there's no size (probably a problem when creating the preview)
			// just use the img name anyway
			echo "<img id=\"content-img\" src=\"$link\" alt=\"$pretty_image\" title=\"$pretty_image\" border=0>";
		}
	}
	else if (strncmp($type, "video/", 6) == 0)
	{
		rig_display_video($type);
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
	global $html_pixels;
	// global $html_image2; -- RM 20040226 v.0.6.4.5 removed as it can be either image or video

	if ($current_img_info)
		$res = $current_img_info;
	else
		$res = rig_build_info($current_album, $current_image);

	// RM 20040226 v.0.6.4.5 removed ." $html_image2" below as it can be either image or video
	$s  = $res["f"] . ", " . $res["w"] . "x" . $res["h"] . " $html_pixels";

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
	global $current_real_album;		// RM 20030907
	global $current_image;
	global $abs_album_path;
	global $pref_use_jhead;
	global $display_title;

	// --- use the jhead application to extract info ---

	$name = $abs_album_path . rig_prep_sep($current_real_album) . rig_prep_sep($current_image);

	$retvar = 1;
	$output = array();

	$args = $pref_use_jhead . " " . rig_shell_filename($name);

	$res = exec($args, $output, $retvar);

	// if the command failed, try a variation on the shell-escaping
	if ($retvar == 1)
	{
		// echo "<p> res[1] -> ";    var_dump($res);
		// echo "<p> retvar[1] -> "; var_dump($retvar);
		// echo "<p> args[1] -> "  ; var_dump($args);
		// echo "<p> output[1] -> " ; var_dump($output);

		$args = $pref_use_jhead . " " . rig_shell_filename2($name);
		$res = exec($args, $output, $retvar);
	}

	// DEBUG
	// echo "<p> res[2] -> ";    var_dump($res);
	// echo "<p> retvar[2] -> "; var_dump($retvar);
	// echo "<p> args[2] -> "  ; var_dump($args);
	// echo "<p> output[2] -> " ; var_dump($output);

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

				$admin     = isset($_GET['admin'    ]) ? $_GET['admin'    ] : null;
				$translate = isset($_GET['translate']) ? $_GET['translate'] : null;

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

//****************************************************************
function rig_display_credits($has_credits = -1, $has_phpinfo = -1)
//****************************************************************
{
	global $display_softname;
	global $html_text_credits;
	global $html_hide_credits;
	global $html_show_credits;
	global $html_credits;

	global $html_show_phpinfo;
	global $html_hide_phpinfo;
	global $html_phpinfo;

	global $color_section_bg;

	$admin   = rig_get($_GET,'admin'  );
	$_debug_ = rig_get($_GET,'_debug_');

	if (!is_string($has_credits) && $has_credits == -1)
		$has_credits = rig_get($_GET,'credits', '');

	if (!is_string($has_phpinfo) && $has_phpinfo == -1)
		$has_phpinfo = rig_get($_GET,'phpinfo', '');


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
		$credits = str_replace("[rig-name-url]", $display_softname, $html_text_credits);

		?>
			<p>
				<?php rig_display_section("<b>$html_credits<b>") ?>
			<p>
				<?php echo "$credits" ?>
			<p>

			<a href="?php_credits=on">PHP Credits</a>
		<?php
	
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
	global $pref_extra_html_footer;

	if ($pref_extra_html_footer)
		echo "\n" . $pref_extra_html_footer . "\n";


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
}

//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.47  2006/06/24 21:20:34  ralfoide
//	Version 1.0:
//	- Source: Set filename in thumbnail streaming headers
//	- Source: Added pref_site_name and pref_site_link.
//	- Fix: Fixed security vulnerability in check_entry.php
//
//	Revision 1.46  2006/04/13 05:04:57  ralfoide
//	Version 0.7.4. Polish translation. Fixes.
//	
//	Revision 1.45  2006/01/11 08:18:42  ralfoide
//	PHP credits displayed in separate window to avoid running current document's stylesheet
//	
//	Revision 1.44  2005/12/26 22:09:30  ralfoide
//	Added link to view full resolution image.
//	Album thumbnail in admin album page.
//	Incorrect escaping of "&" in jhead call.
//	Submitting 0.7.3.
//	
//	Revision 1.43  2005/11/27 18:33:20  ralfoide
//	Changed file_get_contents() to readfile() for php 4.2.x compatibility
//	
//	Revision 1.42  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//	
//	Revision 1.41  2005/10/05 03:54:52  ralfoide
//	Added img id for template/css/js
//	
//	Revision 1.40  2005/10/01 23:44:27  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//	
//	Revision 1.39  2005/09/25 22:35:08  ralfoide
//	Renamed paginator to pager ;-)
//	Also displaying pager at the bottom of album table.
//	Updated GPL header date.
//	
//	Revision 1.38  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.37  2004/07/14 06:20:59  ralfoide
//	Layout
//	
//	Revision 1.36  2004/07/09 05:51:35  ralfoide
//	Fixes for pagination
//	
//	Revision 1.35  2004/06/03 14:14:47  ralfoide
//	Fixes to support PHP 4.3.6
//	
//	Revision 1.34  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.33  2004/03/02 10:38:01  ralfoide
//	Translation of tooltip string.
//	New page title strings.
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
