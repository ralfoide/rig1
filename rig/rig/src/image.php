<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_install . $dir_src . "common.php");

// Important: this page can only display an image.
// If there is no image parameter, redirect to the album
// RM 20020714 disabled since this test is already performed by location/index.php
// if (!((isset($image) && $image) || (isset($id) && rig_db_is_image_id($id))))
// 	header("Location: " . rig_self_url(""));

rig_enter_login(rig_self_url());

rig_prepare_image($id, $album, $image);
rig_display_header($display_title);
rig_display_body();

if ($_test_==2)
{
	?>

<script language="JavaScript" type="text/javascript">

document.write("is_ie4up = " + is_ie4up + " -- is_win32 = " + is_win32 + "<br>");

/*
document.write("CODE: ")
document.write(navigator.appCodeName + "<br>")
document.write("PLATFORM: ")
document.write(navigator.platform + "<br>")
*/
</script>
	
	<?php
}

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
	if (!isset($pref_image_layout) || !rig_is_file($dir_install . $dir_src . "image_layout_$pref_image_layout.php"))
	    $pref_image_layout = '1';

	if (is_string($pref_image_layout))
	{
	    require_once(rig_require_once("image_layout_$pref_image_layout.php", $dir_src, $abs_upload_src_path));
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
	rig_display_credits($credits, $phpinfo);
	rig_display_footer();
	rig_terminate_db();
?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.9  2003/07/11 15:56:38  ralfoide
//	Fixes in video html tags. Added video/mpeg mode. Experimenting with Javascript
//
//	Revision 1.8  2003/03/22 01:22:56  ralfoide
//	Fixed album/image count display in admin mode
//	Added "old" layout for image display, with image layout pref variable.
//	
//	Revision 1.7  2003/02/16 20:22:56  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.6  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.5  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.4  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.3  2002/10/20 11:50:49  ralfoide
//	jhead support
//	
//	Revision 1.2  2002/10/16 04:47:59  ralfoide
//	Changed layout: prev/next links aside the image, image size at bottom
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.3  2001/11/26 06:40:50  ralf
//	fix for diaply credits
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
