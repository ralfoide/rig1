<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

?>
<center>
<!-- prev/size/next link on top of image -->

<p>

<table width="100%" border=0><tr>
<td width="33%" valign="top">
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
					<a href="<?= rig_self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%" valign="top">
	<center>

	<!-- a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a -->
	
	<!-- display image itself -->
	
	<table border="0" bgcolor="<?= $color_table_bg ?>" cellpadding="0" cellspacing="4">

		<tr><td><center><?php rig_display_image() ?></center></td></tr>

		<!-- RM 20030119 v0.6.3 display copyright -->
			<!--tr><td>&nbsp;</td></tr -->
			<tr><td bgcolor="<?= $color_table_bg ?>"><font color="<?= $color_table_infos ?>">
				<div align="left"><?php rig_display_image_copyright() ?></div>
			</font></td></tr>

	</table>
	
	</center>
</td><td width="33%" valign="top">
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
					<a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
</tr></table>


<p>

<!-- prev/info/next link below image -->


<table width="100%" border=0><tr>
<td width="33%" valign="bottom">
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
					<a href="<?= rig_self_url("") ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%">
	<center>

	<table border="0" bgcolor="<?= $color_caption_bg ?>" cellpadding="0" cellspacing="4">
		<tr><td>
			<center>
				<font color="<?= $color_caption_text ?>">
					<b><?= $display_title ?></b>
					<br>
					<?php echo rig_display_image_info() ?>
					<br>
				</font>
				<font color="<?= $color_index_text ?>">
					<?php rig_display_current_album() ?>
				</font>
				<br>
			</center>
		</td></tr>
	</table>
	
	<p>
	
	<a href="<?= rig_self_url("") ?>">
		<?= $html_back_album ?>
	</a>

	<p>

<?php
	// RM 20021014 moved image resize below image info
	// RM 20030713 only display image popup for image type
	if ($current_type == "image")
	{
?>
		<form method="POST" action="<?= rig_self_url() ?>">
			<?= "$html_img_size" ?>
			<select size="1" name="img_size">
			<?php rig_insert_size_popup() ?>
			</select>
			<input type="submit" value="<?= $html_ok ?>" name="ok">
		</form>
<?php
	}
?>
	
	</center>
</td><td width="33%" valign="bottom">
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
					<a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
<?php
	// If the use of jhead is enabled, output jhead's output here -- RM 20021020
	// Only use jhead for images -- RM 20030713
	if ($pref_use_jhead != "" && $current_type == "image")
	{
?>
</tr><tr>
	<td colspan=3>
	<center>
		<!-- jhead info -->
		<table border="0" bgcolor="<?= $color_caption_bg ?>" cellpadding="0" cellspacing="4">
			<tr><td>
				<font color="<?= $color_caption_text ?>">
					<?php rig_display_jhead() ?>
				</font>
			</td></tr>
		</table>
	</center>
	</td>
<?php
	}
?>
</tr></table>
</center>


<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2003/07/14 18:31:14  ralfoide
//	Don't show image size popup for videos
//
//	Revision 1.1  2003/03/22 01:22:56  ralfoide
//	Fixed album/image count display in admin mode
//	Added "old" layout for image display, with image layout pref variable.
//	
//-------------------------------------------------------------
?>
