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


<!-- prev/size/next link on top of image -->

<p>

<table width="100%" border=0><tr>
<td width="33%" valign="top">
	<div align="left">
		<table><tr><td><center>
			<?php
				if ($display_prev_link)
				{
			?>
					<a href="<?= $display_prev_link ?>"><?= $display_prev_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_prev_link ?>"><?= $html_prev ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= rig_self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%" valign="top">
	<center>

	<!-- a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a -->
	
	<!-- display image itself -->
	
	<table border="0" bgcolor="<?= $color_table_bg ?>" cellpadding="0" cellspacing="4">

		<tr><td><center><?php rig_display_image() ?></center></td></tr>

		<!-- RM 20030119 v0.6.3 display copyright -->
			<!--tr><td>&nbsp;</td></tr -->
			<tr><td bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
				<div align="left"><?php rig_display_image_copyright() ?></div>
			</font></td></tr>

	</table>
	
	</center>
</td><td width="33%" valign="top">
	<div align="right">
		<table><tr><td><center>
			<?php
				if ($display_next_link)
				{
			?>
					<a href="<?= $display_next_link ?>"><?= $display_next_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_next_link ?>"><?= $html_next ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
</tr></table>


<p>

<!-- prev/info/next link below image -->


<table width="100%" border=0><tr>
<td width="33%" valign="bottom">
	<div align="left">
		<table><tr><td><center>
			<?php
				if ($display_prev_link)
				{
			?>
					<a href="<?= $display_prev_link ?>"><?= $display_prev_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_prev_link ?>"><?= $html_prev ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= rig_self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%">
	<center>

	<table border="0" bgcolor="<?= $color_caption_bg ?>" cellpadding="0" cellspacing="4">
		<tr><td>
			<center>
				<font color="<?= $color_caption_text ?>">
					<b><?= $display_title ?></b>
					<br>
					<?php echo rig_display_image_info() ?>
					<br>
				</font>
				<font color="<?= $color_index_text ?>">
					<?php rig_display_current_album() ?>
				</font>
				<br>
			</center>
		</td></tr>
	</table>
	
	<p>
	
	<a href="<?= rig_self_url("") ?>">
		<?= $html_back_album ?>
	</a>

	<p>

	<!-- RM 20021014 moved image resize below image info -->
	<form method="POST" action="<?= rig_self_url() ?>">
		<?= "$html_img_size" ?>
		<select size="1" name="img_size">
		<?php rig_insert_size_popup() ?>
		</select>
		<input type="submit" value="<?= $html_ok ?>" name="ok">
	</form>

	
	</center>
</td><td width="33%" valign="bottom">
	<div align="right">
		<table><tr><td><center>
			<?php
				if ($display_next_link)
				{
			?>
					<a href="<?= $display_next_link ?>"><?= $display_next_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_next_link ?>"><?= $html_next ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
<?php
	// If the use of jhead is enabled, output jhead's output here -- RM 20021020
	if ($pref_use_jhead != "")
	{
?>
</tr><tr>
	<td colspan=3>
	<center>
		<!-- jhead info -->
		<table border="0" bgcolor="<?= $color_caption_bg ?>" cellpadding="0" cellspacing="4">
			<tr><td>
				<font color="<?= $color_caption_text ?>">
					<?php rig_display_jhead() ?>
				</font>
			</td></tr>
		</table>
	</center>
	</td>
<?php
	}
?>
</tr></table>


<p>

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
