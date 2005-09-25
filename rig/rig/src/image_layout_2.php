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

<?php
	// RM 20030713 only display image popup for image type
	if ($current_type == "image")
	{
?>
	<!-- image resize -->
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
		
	<a href="<?= rig_self_url("") ?>"><?= $html_back_album ?></a>
	

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


<!-- middle section with custom-sized image -->

<p>

<table width="100%" border=0><tr>
<td width="33%" valign="top">
	<div align="left">
		&nbsp;
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
		&nbsp;
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
//	Revision 1.5  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//
//	Revision 1.4  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.3  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.2  2003/07/14 18:31:14  ralfoide
//	Don't show image size popup for videos
//	
//	Revision 1.1  2003/03/22 01:22:56  ralfoide
//	Fixed album/image count display in admin mode
//	Added "old" layout for image display, with image layout pref variable.
//-------------------------------------------------------------
?>
