<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_abs_src . "common.php");

// OO test -- RM 20030805
if (isset($_GET['_test_']) && $_GET['_test_'] == 1)
{
	require_once($dir_abs_src . "common.php");
	require_once(rig_require_once("RUser.php"));
	require_once(rig_require_once("RAlbum.php"));

	// log in and get the current user
	$rig_user = new RUser();

	// path onto this album or image
	$rig_path = new RPath($dir_album, $abs_album_path, rig_get($_GET,'album'), rig_get($_GET,'image'));

	// this is always an album, initialize an instance
	$rig_album = new RAlbum($rig_path);

	// load preferences (ptions) and file lists...
	if ($rig_album->Load())
	{
		// render album
		if ($rig_album->Render())
		{
			// update album options (no-op right now)
			$rig_album->Sync();
		}
	}

	exit("");
}
// END OO test -- RM 20030805

rig_enter_login(rig_self_url(""));

rig_prepare_album(rig_get($_GET,'id', 0), rig_get($_GET,'album'));
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

<?php
	rig_display_section("<h1> $display_title </h1>",
						$color_title_bg,
						$color_title_text);
	rig_display_user_name();

	// RM 20020714 id: album->current_album
	if ($current_album)
	{
?>
<p>
	<table border="0" cellpadding="0" cellspacing="0">
		<tr><td>
			<table width="100%" border="0" bgcolor="<?= $color_table_border ?>" cellpadding="10" cellspacing="1">
				<tr><td bgcolor="<?= $color_header_bg ?>">
					<center><b><font size="+2" color="<?= $color_header_text ?>">
						<?= $html_current_album ?>
					</font></b></center>
				</td></tr>
				<tr><td bgcolor="<?= $color_table_bg ?>">
					<center><font color="<?= $color_index_text ?>">
						<?php rig_display_current_album(FALSE) ?>
					</font></center>
				</td></tr>
			</table>
		</td></tr>
	</table>
<?php
		rig_flush();
	} // end of if album
?>
<p>

<?php
	rig_load_album_list(TRUE);
	if (rig_has_albums())
	{
?>

	<table border="0" cellpadding="0" cellspacing="0">
		<tr><td>
			<table width="100%" border="0" bgcolor="<?= $color_table_border ?>" cellpadding="10" cellspacing="1">
				<tr><td bgcolor="<?= $color_header_bg ?>">
					<center><font size="+2" color="<?= $color_header_text ?>"><b>
						<?= $html_albums ?>
					</b></font></center>
				</td></tr>
				<tr><td width="100%" bgcolor="<?= $color_table_bg ?>">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<?php rig_display_album_list() ?>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr><td>&nbsp;</td></tr>
						<tr><td width="80%" bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
							<div align="left"><?php rig_display_album_copyright() ?></div>
						</font></td>
						<td width="20%" bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
							<div align="right"><?php rig_display_album_count() ?></div>
						</font></td></tr>
					</table>
				</td></tr>
			</table>
		</td></tr>
	</table>
<p>

<?php
		rig_flush();
	}	// end of if-has-albums

	if (rig_has_images())
	{
?>

  <table border="0" cellpadding="0" cellspacing="0">
		<tr><td>
			<table width="100%" border="0" bgcolor="<?= $color_table_border ?>" cellpadding="10" cellspacing="1">
				<tr><td bgcolor="<?= $color_header_bg ?>">
					<center><font size="+2" color="<?= $color_header_text ?>"><b>
						<?= $html_images ?>
					</b></font></center>
				</td></tr>
				<tr><td bgcolor="<?= $color_table_bg ?>">
					<table width="100%" border="0" cellpadding="10" cellspacing="0">
						<?php rig_display_image_list() ?>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr><td width="80%" bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
							<div align="left"><?php rig_display_album_copyright() ?></div>
						</font></td>
						<td width="20%" bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
							<div align="right"><?php rig_display_image_count() ?></div>
						</font></td></tr>
					</table>
					</table>
				</td></tr>
			</table>
		</td></tr>
	</table>
<p>

<?php
		rig_flush();
	}	// end of if-has-images

	rig_display_back_album();
?>

<p>
	<?php
		rig_display_options();
	?>
	<a href="<?= rig_self_url(-1, -1, RIG_SELF_URL_ADMIN) ?>"><?= $html_admin_intrfce ?></a>
<p>


<?php

} // end output buffering

rig_end_buffering();

rig_display_credits();
rig_display_footer();
rig_terminate_db();

?>

</body>
</html>
<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.10  2003/08/21 20:18:02  ralfoide
//	Renamed dir/path variables, updated rig_require_once and rig_check_src_file
//
//	Revision 1.9  2003/08/18 03:07:14  ralfoide
//	PHP 4.3.x support, new runtime filetype support
//	
//	Revision 1.8  2003/08/15 07:12:07  ralfoide
//	Album HTML cache generation
//	
//	Revision 1.7  2003/03/12 07:02:08  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.6  2003/02/16 20:22:54  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.5  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.4  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.3  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
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
