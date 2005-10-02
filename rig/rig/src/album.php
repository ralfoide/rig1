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

rig_enter_login(rig_self_url(""));

rig_prepare_album(rig_get($_GET,'album'), rig_get($_GET,'apage', 0), rig_get($_GET,'ipage', 0));

require_once($dir_abs_src . "template.php");
rig_init_template(rig_get($_GET,'template'));
if (rig_process_template(
		"album.txt",
		array(
		'rig_html_header_start' => 
			'global $display_title; rig_display_header_start($display_title);',
		'rig_html_header_close' => 
			'rig_display_header_close();',
		'rig_admin_link' =>
			'global $html_admin_intrfce;'
			. 'echo "<a href=\"" . rig_self_url(-1, -1, RIG_SELF_URL_ADMIN) . "\">" . $html_admin_intrfce . "</a>";',
		'rig_logo' =>
			'global $dir_images ; echo rig_post_sep($dir_images) . "riglogo.png";'
		)))
{
	exit;
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
//	Revision 1.19  2005/10/02 21:15:08  ralfoide
//	Album template that starts working (header & info divs properly positionned)
//
//	Revision 1.18  2005/10/01 23:44:27  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//	
//	Revision 1.17  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.16  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
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
