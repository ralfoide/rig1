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
// $Id$


require_once($dir_abs_src . "common.php");

rig_enter_login(rig_self_url(""));

?>
<script language="JavaScript" type="text/JavaScript">
<!--
//-------------
// Source: http://www.breakingpar.com/bkp/home.nsf/Doc!OpenNavigator&U=87256B14007C5C6A87256B4B0005BFA6
// Global variables
var xScreen = 0; // Width of the page
var yScreen = 0; // Height of the page

function getWindowSize()
{
    if (document.layers)
	{
        xScreen = window.innerWidth+window.pageXOffset;
        yScreen = window.innerHeight+window.pageYOffset;
    }
	else if (document.all)
	{
        xScreen = document.body.clientWidth+document.body.scrollLeft;
        yScreen = document.body.clientHeight+document.body.scrollTop;
    }
	else if (document.getElementById)
	{
        xScreen = window.innerWidth+window.pageXOffset;
        yScreen = window.innerHeight+window.pageYOffset;
    }
}

function go(link)
{
	var s = 'index.php?';
	top.location.href = s + 'album=' + link;
}
function go_top(a)
{
	var s = 'index.php?overview=1';
	getWindowSize();
	top.location.href = s + '&album=' + a;
}

function go_sub(a)
{
	var s = 'index.php?overview=2';
	getWindowSize();
	top.location.href = s + '&sw=' + xScreen  + '&sh=' + yScreen + '&album=' + a;
}

function body_over(a)
{
	top.status = 'Album: ' + a + ' (click to zoom in)';
}

function find_obj(n)
{
	// here top is a Window, document is an HTMLDocument
	// var d = (top ? top.document : document);
	var d = document;
	var x = d[n];
	if (!x && d.all)
		x = d.all[n];
	if (!x && d.getElementById)
		x = d.getElementById(n);
	return x;
}

function show_zoom(s, x, y)
{
	var o = find_obj('zoom');
	if (o && s)
	{
		o.src = s;
		o.style.left = x;
		o.style.top = y;
		o.style.visibility = 'visible';
	}
}

function hide_zoom()
{
	var o = find_obj('zoom');
	if (o)
		o.style.visibility = 'hidden';
}

//-->
</script>

<style type="text/css">
<!--
.bgclick {
	margin: 0px;
	padding: 0px;
	overflow: hidden;
	position: absolute;
	z-index: 0;
}
.frame {
	margin: 0px;
	padding: 0px;
	overflow: hidden;
	position: absolute;
	z-index: 1;
	border-style: solid;
	border-width: 3px;
	marginwidth: 0;
	marginheight: 0;
	scrolling: no;
	hspace: 0;
	vspace: 0;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-style: normal;
	line-height: normal;
	font-variant: normal;
	text-align: center;
	text-decoration: none;
	font-weight: normal;
}
.image {
	margin: 0px;
	padding: 0px;
	overflow: hidden;
	position: absolute;
	z-index: 2;
}
.zoom {
	margin: 3px;
	padding: 0px;
	overflow: hidden;
	visibility: hidden;
	position: absolute;
	z-index: 99;
	left: 0px;
	top: 0px;
	background-color: #FFFFFF;
	border: outset #FFFF00;
}
-->
</style>
<?php

	$body_extra = "leftmargin='0' topmargin='0' marginwidth='0' marginheight='0' ";

	// screen size
	$sw = rig_get($_GET,'sw');
	$sh = rig_get($_GET,'sh');

	// view size
//	$rw = rig_get($_GET,'recw');
//	$rh = rig_get($_GET,'rech');
//	if (!isset($rw))
//		$rw = $sw;
//	if (!isset($rh))
//		$rh = $sh;
//
//	// view origin in top page coordinates
//	$ox = rig_get($_GET,'ox');
//	$oy = rig_get($_GET,'oy');

	// recursive index
	$rec = rig_get($_GET,'overview');

	if ($rec <= 1)
	{
		rig_display_body($body_extra);

		$album = rig_get($_GET,'album')

		?>
		<script language="JavaScript" type="text/JavaScript">
		<!--
			go_sub('<?= $album ?>');
		//-->
		</script>
		<noscript>
			JavaScript support is required to use this feature.
			<p>
			Please activate JavaScript or use a JavaScript compliant browser.
			<br>
			Supported browsers are Internet Explorer (4.0+), Mozilla (1.4+) and all Gecko-based browser such as Safari or Konqueror.
		</noscript>
		</body>
		</html>
		<?php
	}
	else
	{
		rig_display_body($body_extra);

		// the floating zoom image
		?>
			<img class="zoom" name="zoom" id="zoom" title="zoom" src="image.jpg" />
		<?php

		process_album(rig_get($_GET,'album'), $sw, $sh, $sw, $sh);
		?>
			</body>
			</html>
		<?php
		exit;
	}
	
	if (0)
	{
		global $color_body_bg;
		
		// change the background color depending on the level
		$bg_col = array( "#FFFFFF", "#EEEEEE", "#DDDDDD", "#CCCCCC", "#BBBBBB", "#AAAAAA",
						 "#999999", "#888888", "#777777", "#666666", "#555555", "#444444", 
						 "#333333", "#222222", "#111111", "#000000");
		$color_body_bg = $bg_col[$rec <= 2 ? 0 : ($rec <= 17 ? $rec-2 : 15)];

		rig_prepare_album(rig_get($_GET,'album'), rig_get($_GET,'apage', 0), rig_get($_GET,'ipage', 0));

		// TODO: if rec=2, add onresize="..." to body tag (reload with rec=1)

		//$extra = "rec=" . ($rec+1) . "&recw=" . $wx . "&rech=" . $wy;
		//$link = rig_self_url("", $name, -1, $extra);
		if ($rec > 2)
			$body_extra .= " onclick=\"go_top('$current_album')\" onmouseover=\"body_over('$current_album')\"";


		rig_display_body($body_extra);


		if (0) // debug
		{
			?>
				Rec: <?= $rec ?> <br>
				SX: <?= $sw ?> <br>
				SY: <?= $sh ?> <br>
				
				Window size is: 
				<script language="JavaScript" type="text/JavaScript">
				<!--
					getWindowSize();
					// document.write('w=' + xScreen + ', h=' + yScreen + '<br>')
				//-->
				</script>
			<?php
		}

		// background click area
		// ...		

		rig_load_album_list(FALSE);

		if (rig_has_albums())
		{
			global $current_album;
			global $list_albums;
			global $list_albums_count;

			// size of the iframe border & border style
			$border = ($rec < 5 ? 5-$rec : 1);
			$margin = ($rec < 6 ? 6-$rec : 0);

			// the complete size of an iframe is:
			// the iframe-width/height + border*2 + margin*2
			

			$m = is_integer($list_albums_count) ? $list_albums_count : count($list_albums);

			$ps = $rw/$rh;
			$mx = floor(sqrt($ps * $m) + .5);
			$my = ceil($m / $mx);

			$wx = floor(($rw - $margin) / $mx);
			$wy = floor(($rh - $margin) / $my);

			// the inner size of the iframe minus its border and the margin
			$wx2 = $wx - 2*$margin - 2*$border;
			$wy2 = $wy - 2*$margin - 2*$border;

			$i = 0;
			$j = 0;

//echo "[a:$rec] $m / $mx + $my / $wx + $wy";

			// the floating zoom image
			?>
				<img class="zoom" name="zoom" id="zoom" title="zoom" src="image.jpg" />
			<?php

			foreach($list_albums as $dir)
			{
				$name = rig_post_sep($current_album) . $dir;
		
				// continue if thumbnail is visible

				if (!rig_is_visible(-1, $dir))
					continue;

				// compute pos
				
				$px = $i * $wx;
				$py = $j * $wy;

				// ---

				$pretty = rig_pretty_name($dir, FALSE, TRUE);
				$extra =  "rec=" . ($rec+1)
						. "&scrw=" . $sw . "&scrh=" . $sh
						. "&recw=" . ($wx2) . "&rech=" . ($wy2)
						. "&ox="   . ($ox+$px+$border+$margin) . "&oy=" . ($oy+$py+$border+$margin);

				$link = ""; // rig_self_url("", $name, -1, $extra);

//echo "<p>$link<br>";
//echo "<br>$extra";
				// insert the iframe
				?>
					<iframe class="frame"
							style="left:<?= $px ?>px; top:<?= $py ?>px; margin:<?= $margin ?>px; border-width:<?= $border ?>px"
							src="<?= $link ?>" name="<?= $pretty ?>"
							width="<?= $wx2 ?>" height="<?= $wy2 ?>"
							title="<?= $pretty ?>" alt="<?= $pretty ?>">
						Your browser does not support iframe elements.
					</iframe>
				<?php			

				// force flush to the browser
				rig_flush();

				// compute next index

				$i++;
				if ($i >= $mx)
				{
					$i = 0;
					$j++;
				}
			}
	
		}
		else if (rig_has_images())
		{
			global $current_album;
			global $list_images;
			global $list_images_count;
			global $current_real_album;

			// 2 pixel margin around div
			$margin = 1;
		
			$m = is_integer($list_images_count) ? $list_images_count : count($list_images);

			//$mx = ceil(sqrt($m));
			$ps = $rw/$rh;
			$mx = floor(sqrt($ps * $m) + .5);
			$my = ceil($m / $mx);

			$wx = floor($rw / $mx);
			$wy = floor($rh / $my);

			if ($wx < 20) 
				$margin=0;

			$wx2 = $wx - 2*$margin;
			$wy2 = $wy - 2*$margin;

			$i = 0;
			$j = 0;
			$skip = 0;
			$skiper = 0;

			// if images become smaller than 20 pixels, skip some images
			if (0) // $wx2 < 20 || $wy2 < 20)
			{
				$nx = min(1, ($wx2 < 20 ? floor($mx * $wx2 / 20) : $mx));
				$ny = min(1, ($wy2 < 20 ? floor($my * $wy2 / 20) : $my));
				
				$d = $m - ($nx * $ny);
				$skip = $m / $d;
				
				$mx = $nx;
				$my = $ny;

				$wx = floor($rw / $mx);
				$wy = floor($rh / $my);
	
				$wx2 = $wx - 2*$margin;
				$wy2 = $wy - 2*$margin;
			}

			foreach($list_images as $file)
			{
				// continue if thumbnail is visible
		
				if (!rig_is_visible(-1, -1, $file))
					continue;

				// skip some?
				if ($skip > 0)
				{
					$skiper++;
					if ($skiper > (int)$skip)
					{
						$skiper -= (int)$skip;
						continue;
					}
				}

				// compute pos
				
				$px = $i * $wx;
				$py = $j * $wy;

				$px2 = $px+$marginx;
				$py2 = $py+$marginy;

				// ---

				$pretty2 = rig_pretty_name($file, FALSE);
		
				$info = rig_build_preview_info($current_real_album, $file);
				$preview = $info["p"];
				$preview = rig_encode_url_link($preview);
				$link = rig_self_url($file);
				$tooltip = "$html_image: $pretty2";

				$ix = $ix1 = (isset($info["w"]) ? $info["w"] : $wx2);
				$iy = $iy1 = (isset($info["h"]) ? $info["h"] : $wy2);


				// proportional rescaling
				if ($ix != $wx2 && $iy != $wy2)
				{
					$p = $ix / $iy;
					$r = $wx2 / $wy2;
					if ($p >= $r)
					{
						// match width, rescale height
						$ix = $wx2;
						$iy = $ix / $p;
					}
					else
					{
						// match height, rescale width
						$iy = $wy2;
						$ix = $iy * $p;
					}
				}

				$width  = "width=\""  . $ix . "\"";
				$height = "height=\"" . $iy . "\"";

				// center image
				$px2 += floor(($wx2 - $ix) / 2);
				$py2 += floor(($wy2 - $iy) / 2);

				// zoom position

				$zm = 15;	// browser margin for right/bottom
				$zd = 10;	// border size around the div

				$zx = $ox + $px2 - floor(($ix1 - $ix)/2);
				$zy = $oy + $py2 + $iy + $zd;

				if ($zx + $ix1 >= $sw - $zm)
					$zx = $sw - $zm - $ix1;

				if ($zy + $iy1 >= $sh - $zm)
					$zy = $oy + $py2 - $zd - $iy1;


if(1){
				// insert the div with the image
				?>
					<a  class="image"
						style="left:<?= $px2 ?>px; top:<?= $py2 ?>px; width:<?= $ix ?>px; height:<?= $iy ?>px"
						href="<?= $link ?>" target="_new"
					 ><img src="<?= $preview ?>" 
						alt="<?= $pretty2 ?>" 
						title="<?= $tooltip ?>"
						onmouseover="show_zoom('<?= $preview ?>','<?= $zx ?>px','<?= $zy ?>px')" 
						onmouseout="hide_zoom()"
					 	<?= $width ?> <?= $height ?> border="0"/></a>
				<?php
}

				// force flush to the browser
//				rig_flush();

				// compute next index

				$i++;
				if ($i >= $mx)
				{
					$i = 0;
					$j++;
				}
				if ($j >= $my)
					break;
			}

			rig_flush();
		}
		
		?>
			</body>
			</html>
		<?php
		
	}

//**********************************************************************
function process_album($album, $sw, $sh, $rw, $rh, $ox=0, $oy=0, $z = 0)
//**********************************************************************
{
	// change the background color depending on the level
	$bg_col = array( "#FFFFFF", "#EEEEEE", "#DDDDDD", "#CCCCCC", "#BBBBBB", "#AAAAAA",
					 "#999999", "#888888", "#777777", "#666666", "#555555", "#444444", 
					 "#333333", "#222222", "#111111", "#000000");

	rig_prepare_album($album, 0, 0);
	rig_load_album_list(FALSE);
	// fix -- force reloading the album/image lists & computing their count
	rig_unset_global('list_albums_count');
	rig_unset_global('list_images_count');
	
	// ofset content according to the _previous_ font size
	$fs1 = 2*($z == 0 ? 0 : min(10, max(5, 10-$z+1)));
	
	if ($rh > $fs1 + 16)
	{
		$oy += $fs1;
		$rh -= $fs1;
	}

	if (rig_has_albums())
	{
		global $current_album;
		global $list_albums;
		global $list_albums_count;

		// size of the frame border & border style
		$border = 1;
		$margin = 1;

		// font size...
		$font_size = min(10, max(5, 10-$z));

		// the complete size of a frame is:
		// the frame-width/height + border*2 + margin*2
		
		$m = is_integer($list_albums_count) ? $list_albums_count : count($list_albums);

		$ps = $rw/$rh;
		$mx = floor(sqrt($ps * $m) + .5);
		if ($mx < 1) $mx = 1; // don't show less than one column
		$my = ceil($m / $mx);
		if ($my < 1) $my = 1; // don't show less than one row

		$wx = floor(($rw - $margin) / $mx);
		$wy = floor(($rh - $margin) / $my);

		// the inner size of the frame minus its border and the margin
		$wx2 = $wx - 2*$margin - 2*$border;
		$wy2 = $wy - 2*$margin - 2*$border;


		$i = 0;
		$j = 0;

		// keep a cache of the info to recurse later
		$local_cache = array();

		foreach($list_albums as $dir)
		{
			$name = rig_post_sep($current_album) . $dir;
			
			// continue if thumbnail is visible

			if (!rig_is_visible(-1, $dir))
				continue;

			// compute pos
			
			$px = $ox + $i * $wx;
			$py = $oy + $j * $wy;

			// ---
			// compute if this album has sub albums
			
			$info = rig_get_album_info($name);
			
			$album_count = (isset($info['a']) ? $info['a'] : 0);
			$image_count = (isset($info['i']) ? $info['i'] : 0);

			// ---

			$pretty_alt   = "Click to zoom in album " . rig_pretty_name($dir, FALSE, TRUE);
			$pretty_title = rig_pretty_name($dir, TRUE, FALSE);

			$extra =  "overview=1"
					. "&sw=" . $sw . "&sh=" . $sh;

			if ($album_count > 0)
				$onclick = "go_top('" . rig_encode_argument($name) . "')";
			else
				$onclick = "go('" . rig_encode_argument($name) . "')";

			$local_cache[] = array('n' => $name,
								   'x' => $px,
								   'y' => $py);

			$style =  "z-index:" .$z . ";"
					. " left:" . $px . "px; top:" . $py . "px; width:" . $wx2 . "px; height:" . $wy2 . "px;"
					. " margin:" . $margin . "px; border-width:" . $border . "px;"
					. " font-size:" . $font_size . "px;"
					. " background-color: " . $bg_col[($z+0*$i+0*$j) % 16];

			$script = " onclick=\"$onclick\" onmouseover=\"body_over('$name')\"";

			// insert the bottom div
			?>
				<div class="frame"
					style="<?= $style ?>"
					title="<?= $pretty_alt ?>" alt="<?= $pretty_alt ?>" <?= $script ?>><?= $pretty_title ?></div>
			<?php

			// force flush to the browser
			rig_flush();

			// compute next index

			$i++;
			if ($i >= $mx)
			{
				$i = 0;
				$j++;
			}
		}
		
		// now recurse in sub albums
		foreach($local_cache as $item)
		{
			process_album($item['n'], $sw, $sh, $wx, $wy, $item['x'], $item['y'], $z+1);
		}
	}		
	else if (rig_has_images())
	{
		process_images($sw, $sh, $rw, $rh, $ox, $oy, $z);
	}

	rig_flush();
}

//*******************************************************
function process_images($sw, $sh, $rw, $rh, $ox, $oy, $z)
//*******************************************************
{
	global $current_album;
	global $list_images;
	global $list_images_count;
	global $current_real_album;
	global $html_image;

	// size of the frame border & border style
	$border = 1;
	$margin = 0;

	// the complete size of a frame is:
	// the frame-width/height + border*2 + margin*2
	
	$m = is_integer($list_images_count) ? $list_images_count : count($list_images);

	$ps = $rw/$rh;
	$mx = floor(sqrt($ps * $m) + .5);
	if ($mx < 1) $mx = 1; // don't show less than one column
	$my = ceil($m / $mx);
	if ($my < 1) $my = 1; // don't show less than one row

	$wx = floor(($rw - $margin) / $mx);
	$wy = floor(($rh - $margin) / $my);

	// if images get too small, limit their number
	$min_limit = 24;
	if ($wx < $min_limit || $wy < $min_limit)
	{
		if ($wx < $min_limit)
			$mx = floor(($rw - $margin) / ($min_limit + $margin));
		if ($wy < $min_limit)
			$my = floor(($rh - $margin) / ($min_limit + $margin));
		if ($mx < 1) $mx = 1; // don't show less than one column
		if ($my < 1) $my = 1; // don't show less than one row
		$wx = floor(($rw - $margin) / $mx);
		$wy = floor(($rh - $margin) / $my);
	}

	// the inner size of the frame minus its border and the margin
	$wx2 = $wx - 2*$margin - 2*$border;
	$wy2 = $wy - 2*$margin - 2*$border;

	$i = 0;
	$j = 0;

	foreach($list_images as $file)
	{
		// continue if thumbnail is visible

		if (!rig_is_visible(-1, -1, $file))
			continue;

		// skip some?
		// ...

		// compute pos
			
		$px = $ox + $i * $wx;
		$py = $oy + $j * $wy;

		// ---

		$pretty2 = rig_pretty_name($file, FALSE);

		$info = rig_build_preview_info($current_real_album, $file);
		$preview = $info["p"];
		$preview = rig_encode_url_link($preview);
		$link = rig_self_url($file);
		$tooltip = "Click to view full " . "$html_image: $pretty2";

		$ix = $ix1 = (isset($info["w"]) ? $info["w"] : $wx2);
		$iy = $iy1 = (isset($info["h"]) ? $info["h"] : $wy2);

		// proportional rescaling if image too big
		if ($ix > $wx2 || $iy > $wy2)
		{
			$p = $ix / $iy;
			$r = $wx2 / $wy2;
			if ($p >= $r)
			{
				// match width, rescale height
				$ix = $wx2;
				$iy = $ix / $p;
			}
			else
			{
				// match height, rescale width
				$iy = $wy2;
				$ix = $iy * $p;
			}
		}

		$width  = "width=\""  . $ix . "\"";
		$height = "height=\"" . $iy . "\"";

		// center image
		$px += floor(($wx2 - $ix) / 2);
		$py += floor(($wy2 - $iy) / 2);

		// zoom position

		$zm = 15;	// browser margin for right/bottom
		$zd = 10;	// border size around the div

		$zx = $px - floor(($ix1 - $ix)/2);
		$zy = $py + $iy + $zd;

		if ($zx + $ix1 >= $sw - $zm)
			$zx = $sw - $zm - $ix1;
		
		if ($zy + $iy1 >= $sh - $zm)
			$zy = $py + $py2 - $zd - $iy1;


if(1){
		// insert the div with the image
		?>
			<a  class="image"
				style="left:<?= $px ?>px; top:<?= $py ?>px; width:<?= $ix ?>px; height:<?= $iy ?>px"
				href="<?= $link ?>" target="_new"
			 ><img src="<?= $preview ?>" 
				alt="<?= $pretty2 ?>" 
				title="<?= $tooltip ?>"
				onmouseover="show_zoom('<?= $preview ?>','<?= $zx ?>px','<?= $zy ?>px')" 
				onmouseout="hide_zoom()"
			 	<?= $width ?> <?= $height ?> border="0"/></a>
		<?php
}


		// compute next index

		$i++;
		if ($i >= $mx)
		{
			$i = 0;
			$j++;
		}
		if ($j >= $my)
			break;
	}
}

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//
//	Revision 1.1  2004/02/18 07:40:46  ralfoide
//	Album overview
//	
//-------------------------------------------------------------
?>
