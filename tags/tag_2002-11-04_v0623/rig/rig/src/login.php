<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

global $html_username;
global $html_password;
global $html_validate;
global $html_guest_login;
global $html_symb_lang;
global $html_desc_lang;
global $color_body_bg;
global $color_body_text;
global $color_title_bg;
global $color_title_text;
global $pref_allow_guest;
global $pref_guest_username;

rig_display_header($title);
rig_display_body();
?>

<center>

<?php
	rig_display_section("<h1> $title </h1>",
						$color_title_bg,
						$color_title_text);
?>
<p>

<table border=0>
	<tr>
		<td>
			<?= $html_username ?>
		</td><td>
			<form method="POST" action="<?= $url ?>">
				<input type="text" name="<?= $var_user ?>" size="20" tabindex="1">
		</td>
	</tr><tr>
		<td>
			<?= $html_password ?>
		</td><td>
				<input type="password" name="<?= $var_pwd ?>" size="20" tabindex="2">
		</td>
	</tr><tr>
		<td></td>
		<td>
				<input type="checkbox" name="keep" value="on" checked tabindex="3"> <?= $html_remember ?>
		</td>
	</tr><tr>
		<td></td>
		<td>
				<input type="submit" value="&nbsp;&nbsp;&nbsp;<?= $html_validate ?>&nbsp;&nbsp;&nbsp;" name="ok" tabindex="3">
			</form>
		</td>
	</tr>
<?php
	// include the guest mode button, if allowed and not in administrator mode
	if ($pref_allow_guest && !$admin)
	{
?>
	<tr>
		<td></td>
		<td>
			<p>
			<form method="POST" action="<?= $url ?>">
				<input type="hidden" name="<?= $var_user ?>" value="<?= $pref_guest_username ?>" >
				<input type="hidden" name="<?= $var_pwd ?>"  value=""   >
				<input type="hidden" name="keep"			 value="on" >
				<input type="submit" value="&nbsp;&nbsp;&nbsp;<?= $html_guest_login ?>&nbsp;&nbsp;&nbsp;" name="ok">
			</form>
		</td>
	</tr>
<?php
	}	// if guest allowed
?>
</table>

<p>
&nbsp;
<p>
<?php
	rig_display_options(FALSE);
	rig_display_footer();
	rig_terminate_db();
?>

</body>
</html>
<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.4  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//
//	Revision 1.3  2002/10/21 01:55:12  ralfoide
//	Prefixing functions with rig_, multiple language and theme support, better error reporting
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 00:07:37  ralf
//	Starting version 0.6: location and split of site vs album files
//	
//	Revision 1.5  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//	
//-------------------------------------------------------------
?>
