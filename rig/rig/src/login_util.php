<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Variables that this URL can receive:
// login		- boolean string 'force' or 'fail' or nothing
// keep			- boolean string 'on' or nothing
// user			- string username
// passwd		- string passwd
// admusr		- string username
// admpwd		- string passwd
//
// Global variable this module can set:
// display_user	- displayed username

//****************************************
function enter_login($url, $admin = FALSE)
//****************************************
{
	global $dir_install,	$dir_src;
	global $html_login,		$html_rig_admin;
	global $html_username,	$html_passwd;
	global $html_remember,	$html_validate;
	global $rig_adm_user,	$rig_adm_passwd;
	global $rig_user,		$rig_passwd;
	global $user,			$passwd;
	global $admusr,			$admpwd;
	global $force_login,	$keep;

	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;

	if ($admin)
	{
		$valid    = test_user_pwd($admin, &$rig_adm_user, &$rig_adm_passwd);
		$var_user = "admusr";
		$var_pwd  = "admpwd";
		$title    = "$html_rig_admin $html_login";
	}
	else
	{
		$valid    = test_user_pwd($admin, &$rig_user, &$rig_passwd);
		$var_user = "user";
		$var_pwd  = "passwd";
		$title    = "$html_login";
	}

	if (   !$valid
		&& !$force_login
		&&  $pref_allow_guest
		&&  $pref_auto_guest
		&& !$rig_user
		&& !$user
		&& !$admusr)
	{
		$rig_user = $pref_guest_username;
		$rig_passwd = "";
		$valid = test_user_pwd($admin, &$rig_user, &$rig_passwd);
	}

	if ($force_login || !$valid)
	{
		remove_login_cookies($admin);
		nocache_headers();
		if ($force_login != "force") $force_login = "fail";
		include($dir_install . $dir_src . "login.php");
		exit;
	}
}


//**********************************************
function test_user_pwd($admin, &$user, &$passwd)
//**********************************************
// returns TRUE if user/passwd is valid
// returns FALSE otherwise and clear the user/passwd variables
{
	global $dir_locset;
	global $dir_install;
	global $dir_globset;
	global $display_user;
	$valid = FALSE;

	// debug
	// echo "testing user/pwd: user='$user' pwd='$passwd' admin='$admin'\n";
	// RM 090401 TBDL
	// $passwd = "";

	if ($user)
	{
		// look for a file in the local settings
		$b = $dir_locset;
		$file = @fopen($admin ? $b . "admin_list.txt" : $b . "user_list.txt", "rt");

		// if we cannot find it, look for a file in the global settings
		if (!$file)
		{
			$b = $dir_install . $dir_globset;
			$file = @fopen($admin ? $b . "admin_list.txt" : $b . "user_list.txt", "rt");
		}

		if ($file)
		{
			while (!feof($file))
			{
				$line = fgets($file, 1023);
				if (is_string($line) && $line[0] != '#')
				{
					$p = split(':', $line, 3);
					if (is_array($p))
					{
						$valid = ($p[0] == $user && ($p[1][0] == '' || $p[1] == $passwd));
						if ($valid)
						{
							$display_user = $p[2];
							break;
						}
					}
				}
			}
			fclose($file);
		}
	}

	if (!$valid)
	{
		$user = "";
		$passwd = "";
		$display_user = "";
	}

	// echo "valid='$valid'<br>\n";

	return $valid;
}


//************************************
function display_user_name($user = "")
//************************************
{
	global $html_welcome;
	global $html_chg_user;
	global $html_welcome;
	global $display_user;
	global $rig_user;

	if ($display_user)
		$user = $display_user;
	
	if (!$user)
		$user = $rig_user;

	if ($user)
		printf ($html_welcome,
				$user,
				"<a href=\"" . self_url(-1, -1, -1, "force_login=force") . "\">$html_chg_user</a>");
}


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//	Revision 1.1  2001/11/26 00:07:37  ralf
//	Starting version 0.6: location and split of site vs album files
//	
//	Revision 1.6  2001/10/21 02:15:16  ralf
//	debug
//	
//	Revision 1.5  2001/08/31 02:34:17  ralf
//	Auto guest mode
//	
//	Revision 1.4  2001/08/14 17:48:07  ralf
//	Fixes: login can appear in both languages.
//	Feature: added the guest mode in user login (not admin).
//	
//	Revision 1.3  2001/08/07 09:04:30  ralf
//	Updated ID and VIM tag
//	
//	Revision 1.2  2001/08/07 08:04:17  ralf
//	Added a cvs log entry
//	
//-------------------------------------------------------------
?>