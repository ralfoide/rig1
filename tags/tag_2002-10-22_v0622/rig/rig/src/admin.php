<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
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

enter_login(self_url(), TRUE);
nocache_headers();

if ($image)
	rig_prepare_image(-1, $album, $image, $html_admin);
else
	rig_prepare_album(-1, $album, $html_admin);

rig_admin_perform_before_header(self_url());

rig_display_header($html_rig_admin);
rig_display_body();

?>

<center>

<?php
	rig_display_section("<h1> $html_rig_admin </h1>" .
						"<font size=\"+2\"><b> $display_title </b></font>",
						$color_title_bg,
						$color_title_text);

	display_user_name($rig_adm_user);
	if ($album)
	{
?>
		<p>
		<img src="<?= rig_encode_url_link(get_album_preview($current_album, TRUE)) ?>">
		<br>
		<font color="<?= $color_index_text ?>">
			<?php display_current_album() ?>
		</font>
		<p>
<?php
	}
	echo "<p>";

	rig_admin_perform_defer();
?>

<p>
	<?= $html_comment_stats ?>
<br>
<?php
	$res = rig_admin_get_preview_info($current_album);
	// Result: array{ 0:nb_files, 1:nb_folders, 2:nb_bytes}
	printf($html_album_stat, $res[2], $res[0], $res[1]);
?>
<br>


<p>
	<?php
		rig_display_section("<b> $html_actions </b>");
	?>
<br>
<a href="<?= self_url("") . "&admin=mk_all_prev" ?>">
	<?= $html_mk_previews ?>
</a>
<br>
<a href="<?= self_url("") . "&admin=rm_all_prev" ?>">
	<?= $html_rm_previews ?>
</a>
<br>
<a href="<?= self_url("") . "&admin=rand_prev" ?>">
	<?= $html_rand_previews ?>
</a>
<br>
<a href="<?= self_url("") . "&admin=rnm_canon" ?>">
	<?= $html_rename_canon ?>
</a>
<br>

<?php
	load_album_list(TRUE);
	if (has_albums())
	{
?>
	<p>
		<?php
			rig_display_section("<b> $html_avail_albums </b>");
		?>
	<br>
		<table colspan="<?= $pref_nb_col ?>" border="1" cellpadding="5" cellspacing="0">
			<?php rig_admin_display_album() ?>
		</table>
		<hr width="10%">
		<font color="<?= $color_index_text ?>">
			<?php display_current_album() ?>
		</font>
	<br>
<?php
	}

	if (has_images())
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
		</table>
		<hr width="10%">
		<font color="<?= $color_index_text ?>">
			<?php display_current_album() ?>
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
	<a href="<?= self_url("", -1, FALSE) ?>"><?= $display_album_title ?></a>
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
