<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_abs_src . "common.php");

rig_enter_login(rig_self_url(""));

rig_prepare_album(rig_get($_GET,'album'), rig_get($_GET,'apage', 0), rig_get($_GET,'ipage', 0));
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
//	Revision 1.15  2004/03/09 06:22:29  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//
//	Revision 1.14  2004/02/23 04:18:13  ralfoide
//	Removed obsolete OO test
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
