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

if (isset($_GET['image']) && $_GET['image'])
	rig_prepare_image(rig_get($_GET,'album'), rig_get($_GET,'image'), $html_admin);
else
	rig_prepare_album(rig_get($_GET,'album'), -1, -1, $html_admin);

rig_display_header($html_rig_admin);			
rig_display_body();

?>

<center>

<?php
	// RM 20030308 TBT -- Translate "Edit"
	rig_display_section("<h1> $html_rig_admin </h1>" .
						"<font size=\"+2\"><b> Translation for " . $html_desc_lang[$current_language] . " </b></font>",
						$color_title_bg,
						$color_title_text);

	rig_display_user_name($rig_adm_user);

?>

<p>
</center>

<center>

<p>

<?php
	$filelist = array('str_' . $current_language . '.php',
					  'data_' . $current_language . 'u8.bin');

	foreach($filelist as $filename)
	{
		$absfile1 = rig_post_sep($dir_abs_src) . $filename;
		$absfile2 = rig_post_sep($abs_upload_src_path)    . $filename;
		$nodot = str_replace('.', '_', $filename);

		// use the overriden uploaded file or the main one
		if (rig_is_file($absfile2))
			$absfile = $absfile2;
		else if (rig_is_file($absfile1))
			$absfile = $absfile1;
		else
			$absfile = "";

		if ($absfile)
		{
			rig_display_section("<a name=\"$nodot\"></a><b> File '$filename' </b>");


			$textfieldname = "text_$nodot";
			
			// DEBUG
			// echo "<br>-- var: "; var_dump($var);
			// echo "<br>- *var: "; var_dump($$var);

			if (isset($$textfieldname) && is_string($$textfieldname))
			{
				if (saveUploadFile($$textfieldname, $absfile2, $filename, $nodot))
					echo "<font size=+2><b>Successfully submitted file $filename!</b></font>";
				else
					echo "<font size=+2><b>FAILED to submit file $filename!</b></font>";
			}

			?>
				<form name="form_<?= $nodot ?>" method="post" action="<?= rig_self_url(-1, -1, -1, "#$nodot") ?>">
					<table border="0">	<!-- width="100%" -->
					<tr align="center"> 
						<td colspan="2">
							<textarea name="<?= $textfieldname ?>" cols="100" rows="30" wrap="OFF"><?php
								$file = fopen($absfile, "rt");
								while(!feof($file))
								{
									$line = fgets($file, 1023);
									echo htmlspecialchars($line, ENT_QUOTES, $html_encoding);
									rig_flush();
								}
								fclose($file);
							?></textarea>
							<br>
							Warning: the submit button will only submit <em>this</em> file, and not all of them!
						</td>
					</tr>
					<tr align="center"> 
						<td width="50%"> 
							<input type="reset" name="Reset" value="  Reset  ">
						</td>
						<td width="50%"> 
							<input type="submit" name="Submit" value="  Submit  ">
						</td>
					</tr>
					</table>
				</form>
			<p>
			<?php
		} // if file
	} // foreach file


	//***************************************************************
	function saveUploadFile($textfield, $absfile2, $filename, $nodot)
	//***************************************************************
	{
		global $abs_upload_src_path;

		// important: filter out php code from the file!

		// write content to temp file
		$today = date('Ymd-Gis');
		$tmpname = rig_post_sep($abs_upload_src_path) . $nodot . '_' . $today . '.tmp';

		// DEBUG
		// echo "<p> tmpname = '$tmpname'";
		// echo "<br> absfile2 = '$absfile2'\n";

		$file = fopen($tmpname, "wt");
		if (!$file)
			return FALSE;

		fwrite($file, $textfield, strlen($textfield));
		fclose($file);

		// backup existing file if any
		if (rig_is_file($absfile2))
		{
			if (!rename($absfile2, rig_post_sep($abs_upload_src_path) . $nodot . '_' . $today . '.bak'))
				return FALSE;
		}

		// move temp file to abs file
		if (!rename($tmpname, $absfile2))
			return FALSE;

		// remove temp file
		// unlink($tmpname);

		return TRUE;
	}

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
//	Revision 1.3  2003/08/15 07:11:27  ralfoide
//	Album HTML cache generation
//	
//	Revision 1.2  2003/05/26 17:52:56  ralfoide
//	Removed unused language strings. Added new rig_display_back_to_album method
//	
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
