<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_install . $dir_src . "common.php");
enter_login(self_url(""));

rig_prepare_album($id, $album);
rig_display_header($display_title);
rig_display_body();

?>

<center>

<?php
	rig_display_section("<h1> $display_title </h1>",
						$color_title_bg,
						$color_title_text);
	display_user_name();

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
						<?php display_current_album(FALSE) ?>
					</font></center>
				</td></tr>
			</table>
		</td></tr>
	</table>
<?php
		flush();
	} // end of if album
?>
<p>

<?php
	load_album_list(TRUE);
	if (has_albums())
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
					<table width="100%" border="0" cellpadding="10" cellspacing="0">
						<?php display_album_list() ?>
					</table>
				</td></tr>
			</table>
		</td></tr>
	</table>
<p>

<?php
		flush();
	}	// end of if-has-albums

	if (has_images())
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
						<?php display_image_list() ?>
					</table>
				</td></tr>
			</table>
		</td></tr>
	</table>
<p>

<?php
		flush();
	}	// end of if-has-images

	display_back_album();
?>

<p>
	<?php
		rig_display_options();
	?>
	<a href="<?= self_url(-1, -1, TRUE) ?>"><?= $html_admin_intrfce ?></a>
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
