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

require_once($dir_install . $dir_src . "common.php");
require_once($dir_install . $dir_src . "admin_util.php");

rig_enter_login(rig_self_url(), TRUE);
rig_nocache_headers();

if ($image)
	rig_prepare_image(-1, $album, $image, $html_admin);
else
	rig_prepare_album(-1, $album, $html_admin);

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
	if ($album)
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
	if ($_debug_)
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
	if (rig_has_albums())
	{
?>
	<p>
		<?php
			rig_display_section("<b> $html_avail_albums </b>");
		?>
	<br>
		<table colspan="<?= $pref_nb_col ?>" border="1" cellpadding="5" cellspacing="0">
			<?php rig_admin_display_album() ?>
			<tr><td colspan="<?= $pref_nb_col ?>">
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

	if (rig_has_images())
	{
?>

	<p>
		<?php
			rig_display_section("<b> $html_avail_prevws </b>");
		?>
	<br>
	<font size="-1">
		<?= $html_comment1 ?>
	<br>
		<!--?= $html_comment2 ?-->
	</font>
	<p>
		<table colspan="<?= $pref_nb_col ?>" border="1" cellpadding="5" cellspacing="0">	<!-- colspan="2" -->
			<?php rig_admin_display_image() ?>
			<tr><td colspan="<?= $pref_nb_col ?>">
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
				<tr><td width="80%">
					<div align="left"><?php rig_display_album_copyright() ?></div>
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
	?>
	<?= $html_back_to ?>
	<a href="<?= rig_self_url("", -1, RIG_SELF_URL_NORMAL) ?>"><?= $display_album_title ?></a>
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
//	Revision 1.1  2003/03/12 07:02:07  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//
//	Revision 1.7  2003/02/17 07:47:00  ralfoide
//	Debugging. Fixed album visibility not being used correctly
//	
//	Revision 1.6  2003/02/16 20:22:53  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.5  2002/10/23 08:39:34  ralfoide
//	Fixes for internationalization of strings
//	
//	Revision 1.4  2002/10/21 07:33:59  ralfoide
//	Admin page which respect themes
//	
//	Revision 1.3  2002/10/21 01:53:43  ralfoide
//	prefixing functions with rig_
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
