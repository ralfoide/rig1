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
	prepare_image($album, $image, $html_admin);
else
	prepare_album($album, $html_admin);

admin_perform_before_header(self_url());

?>
<html>
<head>
	<title>
		<?= $html_rig_admin ?>
	</title>
</head>

<body bgcolor="<?= $color_body_bg ?>" text="<?= $color_body_text ?>">

<center>

<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
	<center>
		<h1>
			<?= $html_rig_admin ?>
		</h1>
		<font size="+2"><b>
			<?= $display_title ?>
		</b></font>
	</center>
</td></tr></table>

<?php
	display_user_name($rig_adm_user);
	if ($album)
	{
?>
		<p>
		<img src="<?= rig_encode_url_link(get_album_preview($current_album, TRUE)) ?>">
		<br>
		<?php display_current_album() ?>
		<p>
<?php
	}
	admin_perform_defer();
?>

<p>
	<?= $html_comment_stats ?>
<br>
<?php
	$res = get_preview_info($current_album);
	// Result: array{ 0:nb_files, 1:nb_folders, 2:nb_bytes}
	printf($html_album_stat, $res[2], $res[0], $res[1]);
?>
<br>


<p>
<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
	<center><b>
		<?= $html_actions ?>
	</b></center>
</td></tr></table>
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
		<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
			<center><b>
				<?= $html_avail_albums ?>
			</b></center>
		</td></tr></table>
	<br>
	<table colspan="<?= $pref_nb_col ?>" border="1" cellpadding="5" cellspacing="0">
		<?php display_album_admin() ?>
	</table>
	<hr width="10%">
		<?php display_current_album() ?>
	<br>
<?php
	}

	if (has_images())
	{
?>

	<p>
		<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
			<center><b>
				<?= $html_avail_prevws ?>
			</b></center>
		</td></tr></table>
	<br>
	<font size="-1">
		<?= $html_comment1 ?>
	<br>
		<!--?= $html_comment2 ?-->
	</font>
	<p>
	<table colspan="<?= $pref_nb_col ?>" border="1" cellpadding="5" cellspacing="0">	<!-- colspan="2" -->
		<?php display_image_admin() ?>
	</table>
	<hr width="10%">
		<?php display_current_album() ?>
	<p>

<?php
	}	// end of if-has-images
?>

<p>
	<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
		<center><b>
			<?php echo $html_options ?>
		</b></center>
	</td></tr></table>
	<br>
		<?= $display_language ?>&nbsp;|&nbsp;<a href="<?= self_url(-1, -1, -1, "lang=$html_symb_lang") ?>"><?= $html_desc_lang ?></a>
	<br>
		<?= $html_back_to ?>
		<a href="<?= self_url("", -1, FALSE) ?>"><?= $display_album_title ?></a>
<p>

<?php
	insert_credits($credits);
	insert_footer();
?>

</body>
</html>
<?php
//-------------------------------------------------------------
//	$Log$
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
