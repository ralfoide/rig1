<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

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
	rig_terminate_db();

?>

</body>
</html>
<?php
//-------------------------------------------------------------
//	$Log$
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
