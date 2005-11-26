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


require_once($dir_abs_src . "common.php");

// Important: this page can only display an image.
// If there is no image parameter, redirect to the album.
// This test is performed by location/index.php.

rig_enter_login(rig_self_url());

rig_prepare_image(rig_get($_GET,'album'), rig_get($_GET,'img'));

if (isset($_GET['template']))
{
	require_once($dir_abs_src . "template.php");
	rig_init_template(rig_get($_GET,'template'));
	if (rig_process_template(
			"image.txt",
			array(
			'rig_html_header_start' => 
				'global $display_title; rig_display_header_start($display_title);',
			'rig_html_header_close' => 
				'rig_display_header_close();',
			'rig_admin_link' =>
				'global $html_admin_intrfce;'
				. 'echo "<a href=\"" . rig_self_url(-1, -1, RIG_SELF_URL_ADMIN) . "\">" . $html_admin_intrfce . "</a>";',
			'rig_logo' =>
				'global $dir_images ; echo rig_post_sep($dir_images) . "riglogo.png";',
			'is_img' =>
				'global $current_type; return ($current_type == "image");',
			'show_jhead' =>
				'global $current_type; global $pref_use_jhead; return ($pref_use_jhead != "" && $current_type == "image");',
	
			)))
	{
		exit;
	}
}

rig_display_header($display_title);
rig_display_body();

$n = rig_begin_buffering(); // returns html filename to include or TRUE to start buffering and output or FALSE on errors
if (is_string($n) && $n != '')
{
	include($n);
}
else
{
	// begin output (captured by buffering)

?>

<center>

<!-- top header -->

<?php
	rig_display_section("<font size=\"+2\"><b> $display_title </b></font><br>$display_album_title",
						$color_title_bg,
						$color_title_text);

	rig_display_user_name();
	rig_load_album_list();
	rig_get_images_prev_next();
?>

</center>

<?php

	// get layout preference
	if (!isset($pref_image_layout) || !rig_is_file($dir_abs_src . "image_layout_$pref_image_layout.php"))
	    $pref_image_layout = '1';

	if (is_string($pref_image_layout))
	{
	    require_once(rig_require_once("image_layout_$pref_image_layout.php", $dir_abs_src, $abs_upload_src_path));
	}
		

?>

<center>

<!-- options/credits/gen-time information at bottom -->

<br>
	<?php
		rig_display_options();
	?>
	<a href="<?= rig_self_url(-1, -1, TRUE) ?>"><?= $html_admin_intrfce ?></a>
<p>


<?php
	rig_display_credits();
} // end output buffering

rig_end_buffering();

// footer is not buffered as it contains the generation's time output
rig_display_footer();
?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.20  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//
//	Revision 1.19  2005/10/07 05:40:09  ralfoide
//	Extracted album/image handling from common into common_media.php.
//	Removed all references to obsolete db/id.
//	Added preliminary default image template.
//	
//	Revision 1.18  2005/10/05 03:53:45  ralfoide
//	Made usage of template conditional on presence of query &template=
//	
//	Revision 1.17  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.16  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.15  2004/07/06 04:10:58  ralfoide
//	Fix: using "img" query param instead of "image"
//	Some browsers (at least PocketIE) will interpret "&image=" as "&image;" in URL.
//	
//	Revision 1.14  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.13  2003/09/13 21:55:55  ralfoide
//	New prefs album nb col vs image nb col, album nb row vs image nb row.
//	New pagination system (several pages for image/album grids if too many items)
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
