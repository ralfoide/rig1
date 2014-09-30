<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: admin_album.php,v 1.8 2005/10/07 05:40:11 ralfoide Exp $

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
						"<font size=\"+2\"><b> $display_title </b></font>",
						$color_title_bg,
						$color_title_text);

	rig_display_user_name($rig_adm_user);
	if (isset($_GET['album']) && $_GET['album'])
	{
?>
		<p>
		<img src="<?= rig_encode_url_link(rig_get_album_preview($current_album, TRUE)) ?>">
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
//	$Log: admin_album.php,v $
//	Revision 1.8  2005/10/07 05:40:11  ralfoide
//	Extracted album/image handling from common into common_media.php.
//	Removed all references to obsolete db/id.
//	Added preliminary default image template.
//	
//	Revision 1.7  2005/09/25 22:36:12  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.6  2004/07/17 07:52:30  ralfoide
//	GPL headers
//	
//	Revision 1.5  2004/07/14 06:08:34  ralfoide
//	Clean html caches
//	
//	Revision 1.4  2004/07/06 04:10:57  ralfoide
//	Fix: using "img" query param instead of "image"
//	Some browsers (at least PocketIE) will interpret "&image=" as "&image;" in URL.
//	
//	Revision 1.3  2004/03/09 06:22:29  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.2  2003/09/13 21:55:54  ralfoide
//	New prefs album nb col vs image nb col, album nb row vs image nb row.
//	New pagination system (several pages for image/album grids if too many items)
//	
//	Revision 1.1  2003/08/21 20:15:32  ralfoide
//	Moved admin src into separate folder
//	
//	Revision 1.4  2003/08/18 03:05:12  ralfoide
//	PHP 4.3.x support
//
//	[...]
//
//	Revision 1.1  2003/03/12 07:02:07  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.7  2003/02/17 07:47:00  ralfoide
//	Debugging. Fixed album visibility not being used correctly
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
