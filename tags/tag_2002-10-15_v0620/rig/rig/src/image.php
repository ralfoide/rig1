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

// Important: this page can only display an image.
// If there is no image parameter, redirect to the album
if (!isset($image) || !$image)
{
	header("Location: " . self_url(""));
}

enter_login(self_url());

?>
<html>
<head>
	<title>
		<?php
			prepare_image($album, $image);
			echo "$display_title";
		?>
	</title>
</head>

<body bgcolor="<?= $color_body_bg ?>" text="<?= $color_body_text ?>">

<center>


<!-- top header -->

<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
	<center>
		<font size="+2"><b>
			<?php echo $display_title ?>
		</b></font><br>
			<?php echo $display_album_title ?>
	</center>
</td></tr></table>



<?php
	display_user_name();
	load_album_list();
	get_images_prev_next();
?>


<!-- prev/size/next link on top of image -->

<table width="100%" border=0><tr>
<td width="33%">
	<div align="left">
		<table><tr><td><center>
			<?php
				if ($display_prev_link)
				{
			?>
					<a href="<?= $display_prev_link ?>"><?= $display_prev_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_prev_link ?>"><?= $html_prev ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%">
	<center>

	<form method="POST" action="<?= self_url() ?>">
		<?= "$html_img_size" ?>
		<select size="1" name="img_size">
		<?php insert_size_popup() ?>
		</select>
		<input type="submit" value="<?= $html_ok ?>" name="ok">
	</form>

	<a href="<?= self_url("") ?>"><?= $html_back_album ?></a>
	
	</center>
</td><td width="33%">
	<div align="right">
		<table><tr><td><center>
			<?php
				if ($display_next_link)
				{
			?>
					<a href="<?= $display_next_link ?>"><?= $display_next_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_next_link ?>"><?= $html_next ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
</tr></table>


<!-- display image itself -->

<table border="0" bgcolor="<?= $color_table_bg ?>" cellpadding="0" cellspacing="4">
<tr><td><center><?php display_image() ?></center></td></tr>
</table>
<p>


<!-- prev/info/next link below image -->


<table width="100%" border=0><tr>
<td width="33%">
	<div align="left">
		<table><tr><td><center>
			<?php
				if ($display_prev_link)
				{
			?>
					<a href="<?= $display_prev_link ?>"><?= $display_prev_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_prev_link ?>"><?= $html_prev ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%">
	<center>

	<table border="0" bgcolor="<?= $color_table_bg ?>" cellpadding="0" cellspacing="4">
		<tr><td>
			<center>
				<b><?= $display_title ?></b>
				<br>
				<?php echo display_image_info() ?>
				<br>
				<?php display_current_album() ?>
				<br>
			</center>
		</td></tr>
	</table>
	
	<p>
	
	<a href="<?= self_url("") ?>">
		<?= $html_back_album ?>
	</a>
	
	</center>
</td><td width="33%">
	<div align="right">
		<table><tr><td><center>
			<?php
				if ($display_next_link)
				{
			?>
					<a href="<?= $display_next_link ?>"><?= $display_next_img ?></a>
					</center></td></tr><tr><td><center>
					<a href="<?= $display_next_link ?>"><?= $html_next ?></a>
			<?php
				}
				else
				{
			?>
					<a href="<?= self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
</tr></table>


<p>

<!-- options/credits/gen-time information at bottom -->

<table width="100%" bgcolor="<?= $color_header_bg ?>"><tr><td>
	<center><b>
		<?= $html_options ?>
	</b></center>
</td></tr></table>

<br>
	<?= $display_language ?>&nbsp;|&nbsp;<a href="<?= self_url(-1, -1, -1, "lang=$html_symb_lang") ?>"><?= $html_desc_lang ?></a>
<br>
	<a href="<?= self_url(-1, -1, TRUE) ?>"><?= $html_admin_intrfce ?></a>
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
//	Revision 1.9  2001/11/09 06:40:18  ralf
//	rig 0.5.1: image mode: adds width/height attr in img tag. adds prev/next links below image too.
//	
//	Revision 1.8  2001/08/27 08:47:56  ralf
//	several updates
//	
//	Revision 1.7  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//	
//	Revision 1.6  2001/08/13 05:37:36  ralf
//	Fixes in preview creation, added back album links, etc.
//	
//	Revision 1.5  2001/08/13 01:43:35  ralf
//	Changed appareance of album table
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