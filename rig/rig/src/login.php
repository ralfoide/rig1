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
global $color_error1_bg;
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
<form method="POST" action="<?= $url ?>">
<?php
	if ($login_error)
	{
?>
	<tr align="center"> 
		<td colspan="2" bgcolor="<?= $color_error1_bg ?>"><?= $login_error ?></td>
	</tr>
<?php
}
?>
  	<tr>
		<td valign="baseline">
			<?= $html_username ?>
		</td><td>
			<input type="text" name="<?= $var_user ?>" size="20" tabindex="1">
		</td>
	</tr><tr valign="baseline">
		<td>
			<?= $html_password ?>
		</td><td>
				<input type="password" name="<?= $var_pwd ?>" size="20" tabindex="2">
		</td>
	</tr><tr valign="baseline">
		<td></td>
		<td>
				<input type="checkbox" name="keep" value="on" checked tabindex="3"> <?= $html_remember ?>
		</td>
	</tr><tr valign="baseline">
		<td></td>
		<td>
				<input type="submit" value="&nbsp;&nbsp;&nbsp;<?= $html_validate ?>&nbsp;&nbsp;&nbsp;" name="ok" tabindex="3">
		</td>
	</tr>
</form>
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
//	Revision 1.9  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//
//	Revision 1.8  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.7  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.6  2003/02/23 08:14:36  ralfoide
//	Login: display error msg when invalid password or invalid user
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	[...]
//
//	Revision 1.5  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//-------------------------------------------------------------
?>
