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
		<img src="<?= encode_url_link(get_album_preview($current_album, TRUE)) ?>">
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
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	Revision 1.3  2001/11/26 06:40:50  ralf
//	fix for diaply credits
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//	Revision 1.1  2001/11/26 00:07:37  ralf
//	Starting version 0.6: location and split of site vs album files
//	
//	Revision 1.10  2001/10/20 02:06:56  ralf
//	Marc's patch Sept-2001
//	
//	Revision 1.9  2001/08/28 07:12:59  ralf
//	Made album/images list in admin a table with sub links
//	
//	Revision 1.8  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//	
//	Revision 1.7  2001/08/13 05:37:36  ralf
//	Fixes in preview creation, added back album links, etc.
//	
//	Revision 1.6  2001/08/13 01:43:34  ralf
//	Changed appareance of album table
//	
//	Revision 1.5  2001/08/07 18:28:03  ralf
//	Rename Canon Images
//	
//	Revision 1.4  2001/08/07 09:04:30  ralf
//	Updated ID and VIM tag
//	
//	Revision 1.3  2001/08/07 09:01:17  ralf
//	Added globals for the html colors (in pref).
//	Fixed &lang in the language change URL
//	
//	Revision 1.2  2001/08/07 08:04:17  ralf
//	Added a cvs log entry
//	
//-------------------------------------------------------------
?>
