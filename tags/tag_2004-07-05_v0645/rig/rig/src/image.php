<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_abs_src . "common.php");

// Important: this page can only display an image.
// If there is no image parameter, redirect to the album
// RM 20020714 disabled since this test is already performed by location/index.php
// RM 20040703 using "img" query param instead of "image"
// if (!((isset($_GET['img']) && $_GET['img']) || (isset($_GET['id']) && rig_db_is_image_id($_GET['id']))))
// 	header("Location: " . rig_self_url(""));

rig_enter_login(rig_self_url());

rig_prepare_image(rig_get($_GET,'album'), rig_get($_GET,'img'));
rig_display_header($display_title);
rig_display_body();

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
	rig_display_footer();
	rig_terminate_db();
?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log$
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
