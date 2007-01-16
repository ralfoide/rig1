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

// Variables that this URL can receive:
// album	- string
// image	- string

require_once($dir_abs_src       . "common.php");
require_once($dir_abs_admin_src . "admin_util.php");

rig_enter_login(rig_self_url(), TRUE);
rig_nocache_headers();

// RM 20040703 using "img" query param instead of "image"
if (isset($_GET['img']) && $_GET['img'])
	rig_prepare_image(rig_get($_GET,'album'), rig_get($_GET,'img'), $html_admin);
else
	rig_prepare_album(rig_get($_GET,'album'), -1, -1, $html_admin);

rig_admin_perform_before_header(rig_self_url());

rig_display_header($html_rig_admin);
rig_display_body();

?>

<center>

<?php
	rig_display_section("<h1> $html_rig_admin </h1>" .
						"<font size=\"+2\"><b> $display_title_html </b></font>",
						$color_title_bg,
						$color_title_text);

	rig_display_user_name($rig_adm_user);
	if (isset($_GET['album']) && $_GET['album'])
	{
?>
		<p>
		<img src="<?= rig_self_url(-1, $current_album, RIG_SELF_URL_THUMB) ?>">
		<br>
		<font color="<?= $color_index_text ?>">
			<?php rig_display_current_album() ?>
		</font>
		<p>
<?php
	}
?>
<p>
</center>

<?php
	rig_admin_perform_defer();
?>

<center>

<p>
	<?= $html_comment_stats ?>
<br>
<?php
	$res = rig_admin_get_preview_info($current_album);
	rig_admin_display_album_stat($html_album_stat, $res);
?>
<br>


<p>
	<?php
		rig_display_section("<b> $html_actions </b>");
	?>
<br>
<?= $html_act_create ?>
&nbsp;
  <a href="<?= rig_self_url("") . "&admin=mk_previews" ?>"><?= $html_act_previews ?></a>
| <a href="<?= rig_self_url("") . "&admin=mk_images"   ?>"><?= $html_act_images ?></a>
| <a href="<?= rig_self_url("") . "&admin=mk_prev_img" ?>"><?= $html_act_prev_img ?></a>
<br>
<?= $html_act_delete ?>
&nbsp;
  <a href="<?= rig_self_url("") . "&admin=rm_previews" ?>"><?= $html_act_previews ?></a>
| <a href="<?= rig_self_url("") . "&admin=rm_images"   ?>"><?= $html_act_images ?></a>
| <a href="<?= rig_self_url("") . "&admin=rm_prev_img" ?>"><?= $html_act_prev_img ?></a>
| <a href="<?= rig_self_url("") . "&admin=rm_html_caches" ?>"><?= $html_act_htmlcache ?></a>
<br>
  <a href="<?= rig_self_url("") . "&admin=rand_prev"   ?>"><?= $html_act_rnd_prev ?></a>
<br>
  <a href="<?= rig_self_url("") . "&admin=rnm_canon"   ?>"><?= $html_act_canon ?></a>
<?php
	if (rig_get($_GET, '_debug_'))
	{
?>
<br>
  <a href="<?= rig_self_url("") . "&admin=fix_option"   ?>">Fix Current Option</a>
| <a href="<?= rig_self_url("") . "&admin=fix_options"   ?>">Fix All Options</a>
<?php
}
?>
<br>

<?php
	rig_load_album_list(TRUE);
	if (rig_has_albums(FALSE))
	{
?>
	<p>
		<?php
			rig_display_section("<b> $html_avail_albums </b>");
		?>
	<br>
		<table colspan="<?= $pref_album_nb_col ?>" border="1" cellpadding="5" cellspacing="0">
			<?php rig_admin_display_album() ?>
			<tr><td colspan="<?= $pref_album_nb_col ?>">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr><td width="80%">
					<div align="left"><?php rig_display_album_copyright() ?></div>
				</td>
				<td width="20%">
					<div align="right"><?php rig_display_album_count() ?></div>
				</td></tr>
			</table>
			</td></tr>
		</table>
		<hr width="10%">
		<font color="<?= $color_index_text ?>">
			<?php rig_display_current_album() ?>
		</font>
	<br>
<?php
	}

	if (rig_has_images(FALSE))
	{
?>

	<p>
		<?php
			rig_display_section("<b> $html_current_album </b>");
		?>
	<br>
	<font size="-1">
		<?= $html_comment ?>
	<br>
	</font>
	<p>
		<table colspan="<?= $pref_image_nb_col ?>" border="1" cellpadding="5" cellspacing="0">	<!-- colspan="2" -->
			<?php rig_admin_display_image() ?>
			<tr><td colspan="<?= $pref_image_nb_col ?>">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr><td width="80%">
					<div align="left"><?php rig_display_image_copyright() ?></div>
				</td>
				<td width="20%">
					<div align="right"><?php rig_display_image_count() ?></div>
				</td></tr>
			</table>
			</td></tr>
		</table>
		<hr width="10%">
		<font color="<?= $color_index_text ?>">
			<?php rig_display_current_album() ?>
		</font>
	<p>

<?php
	}	// end of if-has-images
?>

<p>
	<?php
		rig_display_options();
		rig_display_back_to_album(rig_self_url("", -1, FALSE));
	?>
<p>

<?php
	rig_display_credits();
	rig_display_footer();

?>

</body>
</html>
<?php
//-------------------------------------------------------------
// end
//-------------------------------------------------------------
?>
