<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: comment.php,v 1.6 2005/10/07 05:40:09 ralfoide Exp $

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


require_once($dir_abs_src . "common.php");

rig_enter_login(rig_self_url());

// RM 20040703 using "img" query param instead of "image"
if (isset($_GET['img']))
	rig_prepare_image(rig_get($_GET,'album'), rig_get($_GET,'img'));
else
	rig_prepare_album(rig_get($_GET,'album'), rig_get($_GET,'apage', 0), rig_get($_GET,'ipage', 0));

rig_display_header($display_title);
rig_display_body();

?>

<center>

<!-- top header -->

<?php
	rig_display_section("<font size=\"+2\"><b> $display_title </b></font><br>$display_album_title",
						$color_title_bg,
						$color_title_text);

	rig_display_user_name();
	rig_load_album_list();
//	rig_get_images_prev_next();
?>

<!-- form start -->

<form name="form_comment" method="post" action="<?= rig_self_url(-1, -1, -1) ?>">



<table width="100%" border="0">
<tr><td>

	<!-- icon and source info -->

	<p>
	<center>
	<table border="0" bgcolor="<?= $color_caption_bg ?>" cellpadding="4" cellspacing="4">
		<tr align="center">
			<td rowspan="1" colspan="2">
				<i>Write a comment for</i>
			</td>
		</tr>
		<tr>
			<td rowspan="2" colspan="1">
				<?php rig_comment_insert_icon() ?>
			</td>
			<td>
				<b><?= $display_title ?></b>
			</td>
		<tr>
			<td>
				<font color="<?= $color_index_text ?>">
					<?php rig_display_current_album() ?>
				</font>
			</td>
		</tr>
	</table>
	</center>

</td></tr>

<?php
	// BEGING preview area
	if (rig_comment_has_preview())
	{
?>
<tr><td>

	<!-- preview area -->

	<p>
	<center>

	<?php rig_display_section('Preview') ?>

	Please review your comment. You can either <a href="#c_submit1">submit</a> or <a href="#c_edit">edit</a> your comment.
	<p>

	<table border="0" bgcolor="<?= $color_table_border ?>" cellpadding="10" cellspacing="1">
	<tr>
		<td bgcolor="<?= $color_body_bg ?>" valign="middle">
			<center>
				<?php rig_comment_insert_icon() ?>
				<br>
				<?php rig_comment_insert_name() ?>
			</center>
		</td>
		<td bgcolor="<?= $color_body_bg ?>" valign="top">
				<?php rig_comment_insert_comment() ?>
		</td>
	</tr>
	</table>

	<p>
	
	<a name="c_submit1">
		<input type="submit" name="Submit" value="  Submit  ">
	</a>
	
	</center>

</td></tr>
<?php
	}
	// END preview area
?>

<tr><td>

	<!-- comment input -->

	<p>
	<center>
	<a name="c_edit"></a>
	<?php rig_display_section('Edit') ?>

	Please enter your comment here. You must <a href="#c_preview">preview</a> it before being able to submit it.
	<br>
	Please edit your comment here. You can either <a href="#c_preview">preview</a> or <a href="#c_submit2">submit</a> your comment.

	<p>

	<table border="0" cellpadding="0" cellspacing="4">
		<tr><td rowspan="1" colspan="3">
			<textarea name="rig_field_comment" cols="70" rows="10" wrap="ON"><?php
				rig_comment_insert_text()
				?></textarea>
			<p>
		</td></tr>
		<tr align="center"> 
			<td width="33%"> 
				<a name="c_submit1">
					<input type="reset" name="Reset" value="  Reset  ">
				</a>
			</td>
			<td width="33%"> 
				<a name="c_preview">
					<input type="submit" name="Preview" value="  Preview  ">
				</a>
			</td>
			<td width="33%"> 
				<a name="c_submit2">
					<input type="submit" name="Submit" value="  Submit  ">
				</a>
			</td>
		</tr>
		<tr><td rowspan="1" colspan="3">
			<p>
			<em>Easy Formatting</em> guidelines:<br>
			<ul>
				<li>Enclose words with a pair a single-quotes like <code>''this''</code> to write in <i>italics</i>.</li>
				<li>Enclose words with a pair a underscores like <code>__this__ </code>to write in <b>bold</b>.</li>
				<li>Start a line with a star (*) to insert a bullet point.</li>
				<li>Simply enters URLs as <code><a href="http://www.example.com">http://www.example.com</a></code> for links to appear.</li>
				<li>A link can be <a href="http://www.example.com">named</a> by writing <code>[name of link|http://www.example.com]</code>.</li>
			</ul>
			Direct HTML tags or scripts are not interpreted in the comments.
		</td></tr>
	</table>
	</center>

</td></tr>
</table>

<!-- form end -->

</form>


<!-- options/credits/gen-time information at bottom -->

<br>
	<?php
		rig_display_options();
	?>
	<a href="<?= rig_self_url(-1, -1, TRUE) ?>"><?= $html_admin_intrfce ?></a>
<p>


<?php
	rig_display_credits();
	rig_display_footer();
?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log: comment.php,v $
//	Revision 1.6  2005/10/07 05:40:09  ralfoide
//	Extracted album/image handling from common into common_media.php.
//	Removed all references to obsolete db/id.
//	Added preliminary default image template.
//	
//	Revision 1.5  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.4  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.3  2004/07/06 04:10:58  ralfoide
//	Fix: using "img" query param instead of "image"
//	Some browsers (at least PocketIE) will interpret "&image=" as "&image;" in URL.
//	
//	Revision 1.2  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.1  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//-------------------------------------------------------------
?>
