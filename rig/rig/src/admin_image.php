<?php

// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once($dir_install . $dir_src . "common.php");
require_once($dir_install . $dir_src . "admin_util.php");

rig_enter_login(rig_self_url(), TRUE);
rig_nocache_headers();

rig_prepare_image(-1, rig_get($_GET,'album'), rig_get($_GET,'image'), $html_admin);

rig_admin_perform_before_header(rig_self_url());

rig_display_header($html_rig_admin);
rig_display_body();


// the image has a fixed size of $pref_admin_size in details
$rig_img_size = $pref_admin_size;
	
// gather various information on the image
if ($detail > 0)
	$key = "&detail=$detail#$detail";
$file = $current_image;

$pretty = rig_pretty_name($file, FALSE);
$preview = rig_encode_url_link(rig_build_preview($current_album, $file, -1, -1, FALSE));

if (rig_is_visible())
	$vis_val = "selected";

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
		<!-- img src="<?= rig_encode_url_link(rig_get_album_preview($current_album, TRUE)) ?>" -->
		<p>
		<font color="<?= $color_index_text ?>">
			<?php rig_display_current_album() ?>
		</font>
<?php
	}
	echo "<p>";

	rig_admin_perform_defer();

	rig_load_album_list();
	rig_get_images_prev_next();
?>


<?php
	if ($current_image)
	{
?>

	<!-- BEGIN Image Options Form -->

	<form name="rig_form_img_opt" method="POST" action="<?= rig_self_url(-1, -1, RIG_SELF_URL_ADMIN, "admin=set_XXX") ?>">


	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td width="<?= $pref_admin_size+16 ?>" valign="top" align="center">


		<table border="0" cellpadding="0" cellspacing="5">
		<!--  bgcolor="<?= $color_table_bg ?>" -->
	
			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>

			<!--- image on the left side -->
			<tr><td><center><?php rig_display_image() ?></center></td></tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>

			<!-- Submit button UNDER-IMAGE -->	
			<tr><td align="center">
				<input type="submit" name="rig_form_img_set" value="&nbsp;&nbsp;Submit All&nbsp;&nbsp;">
			</td></tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>

		</table>


	</td><td width="5" valign="top" bgcolor="<?= $color_section_bg ?>">
	</td><td valign="top" align="left">


		<!-- Main Form Options -->


		<table width="100%" border="0" cellpadding="0" cellspacing="5">
		
			<!-- image name -->
			<tr> 
				<td bgcolor="<?= $color_section_bg ?>">&nbsp;&nbsp;Image Name</td>
			</tr><tr>
				<td> 
					<font size="-1"><i>Name of the file stored on server</i></font>
					<br>
					<input type="text" name="rig_form_img_name" size="80" value="<?= $current_image ?>">
				</td>
			</tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>
			<tr><td><font size="-2">&nbsp;</td></tr>


			<!-- alternate image name -->
			<tr> 
				<td bgcolor="<?= $color_section_bg ?>">&nbsp;&nbsp;Alternate Name</td>
			</tr><tr>
				<td> 
					<font size="-1"><i>Alternate display name, used if not empty</i></font>
					<br>
					<input type="text" name="rig_form_img_altname" size="80" value="<?= $current_altname ?>">
				</td>
			</tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>
			<tr><td><font size="-2">&nbsp;</td></tr>


			<!-- visibility -->
			<tr> 
				<td bgcolor="<?= $color_section_bg ?>">&nbsp;&nbsp;Visibility</td>
			</tr><tr>
				<td valign="bottom"> 
					<input type="checkbox" name="rig_form_img_vis" value="Visible" <?= $vis_val ?>>
						Visible<br>
					<input type="checkbox" name="rig_form_img_vis_guest" value="Visible">
						Visible by non-guests only
				</td>
			</tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>
			<tr><td><font size="-2">&nbsp;</td></tr>


			<!-- album icon -->
			<tr> 
				<td bgcolor="<?= $color_section_bg ?>">&nbsp;&nbsp;Album Icon</td>
			</tr><tr>
				<td> 
					<table width="100%">
					<tr>
					<td width="50%">
						<font size="-1"><i><?= $current_album ?></i></font>
						<br>
						<input type="checkbox" name="rig_form_img_icon" value="AlbumIcon">
						Use as icon for this album
					</td>
					<td width="50%">
						Set as icon for this parent album:<br>
							<select size="1" name="rig_form_img_parent">
								<?php
									rig_admin_insert_icon_popup();
								?>
							</select>
					</td>
					</tr>
					</table>

				</td>
			</tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>

		</table>


		<!-- End of option table -->

	</td>
	</tr>

	<!-- Sub-options table -->

	<tr><td colspan="3">
		<table width="100%">

			<tr><td><font size="-2">&nbsp;</td></tr>
			<tr> 
				<td bgcolor="<?= $color_section_bg ?>" align="center">Description &nbsp;&nbsp; (<font size="-1"><i>HTML accepted, scripts rejected</i></font>)</td>
			</tr>


			<!-- HTML Descrition -->
			<tr>
				<td align="center"><textarea name="rig_form_img_desc" wrap="PHYSICAL" cols="80" rows="10"></textarea></td>
			</tr>

			<!-- separator -->
			<tr height="5" cellpadding="2"><td bgcolor="<?= $color_section_bg ?>"></td></tr>
			<tr><td><font size="-2">&nbsp;</td></tr>


		</table>
	</td></tr>

	<!-- Submit button BOTTOM -->

	<tr><td colspan="3" align="center">
		<input type="submit" name="rig_form_img_set" value="&nbsp;&nbsp;Submit All&nbsp;&nbsp;">
	</td></tr>

	<!-- End of image/options table -->

	</table>



<!-- END Image Options Form -->

<?php
	}
	else
	{
?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
		<td width="<?= $pref_admin_size+16 ?>" valign="top" align="center">
	
			<table border="1" bgcolor="<?= $color_table_bg ?>" cellpadding="0" cellspacing="0">
			<tr height="<?= $pref_admin_size*0.75 ?>"><td width="<?= $pref_admin_size ?>" valign="center" align="center">No image selected</td></tr>
			</table>

		</td><td valign="top" align="left">
			<center>Please select an image in the administration page first.</center>
		</td>
		</tr>
		</table>
<?php
	}
?>


<!-- ******************************************* -->

<p>

<!-- prev/size/next links -->

<?php
	rig_display_section("<table width=\"100%\"><tr>" .
						"<td width=\"50%\" align=\"left\">&nbsp;&nbsp;<b>$html_prev</b></td>" .
						"<td width=\"50%\" align=\"right\"><b>$html_next</b>&nbsp;&nbsp;</td>" .
						"</tr></table>");
?>
<br>

<table width="100%" border="0"><tr>
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
					<a href="<?= rig_self_url("", -1, RIG_SELF_URL_ADMIN) ?>">
						<?= $html_back_album ?>
					</a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td><td width="34%">
	<center>

	<a href="<?= rig_self_url("", -1, TRUE) ?>"><?= $html_back_album ?></a>
	
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
					<a href="<?= rig_self_url("", -1, RIG_SELF_URL_ADMIN) ?>"><?= $html_back_album ?></a>
			<?php
				}
			?>
		</center></td></tr></table>
	</div>
</td>
</tr></table>


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
//	Revision 1.3  2003/08/18 03:05:12  ralfoide
//	PHP 4.3.x support
//
//	Revision 1.2  2003/05/26 17:52:56  ralfoide
//	Removed unused language strings. Added new rig_display_back_to_album method
//	
//	Revision 1.1  2003/03/12 07:02:07  ralfoide
//	New admin image vs album (alpha version not finished).
//	New admin translate page (alpha version not finished).
//	New pref to override the <meta> line in album/image display.
//	
//	Revision 1.1  2002/11/09 08:55:44  ralf
//	Re-inserted 0.6.3 files
//	
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//	
//-------------------------------------------------------------
?>
