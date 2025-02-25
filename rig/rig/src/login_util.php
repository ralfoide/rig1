<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: login_util.php,v 1.17 2005/09/25 22:36:15 ralfoide Exp $

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

//********************************************
function rig_enter_login($url, $admin = FALSE)
//********************************************
{
	global $dir_abs_src;
	global $html_login,		$html_rig_admin;
	global $html_username,	$html_passwd;
	global $html_remember,	$html_validate;
	global $rig_adm_user,	$rig_adm_passwd;
	global $rig_user,		$rig_passwd;
	global $user,			$passwd;
	global $admusr,			$admpwd;
	global $force_login,	$keep;
	global $login_error;

	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;


//DEBUG
//echo "rig_enter_login<br>";
//var_dump($_POST);	echo "<br>";
//var_dump($_GET);	echo "<p>";

	$user			= rig_get($_GET,'user',   rig_get($_POST,'user'  ));
	$passwd			= rig_get($_GET,'passwd', rig_get($_POST,'passwd'));
	$admusr			= rig_get($_GET,'admusr', rig_get($_POST,'admusr'));
	$admpwd			= rig_get($_GET,'admpwd', rig_get($_POST,'admpwd'));

	$force_login	= rig_get($_GET,'force_login', rig_get($_POST,'force_login'));
	$keep			= rig_get($_GET,'keep',		   rig_get($_POST,'keep'));

	$rig_user		= rig_get($_COOKIE,'rig_user'		);
	$rig_passwd		= rig_get($_COOKIE,'rig_passwd'		);
	$rig_adm_user	= rig_get($_COOKIE,'rig_adm_user'	);
	$rig_adm_passwd	= rig_get($_COOKIE,'rig_adm_passwd'	);

	if ($admin)
	{
		$login_error = "";
		$valid = rig_test_user_pwd($admin, $rig_adm_user, $rig_adm_passwd, $login_error);

		$var_user = "admusr";
		$var_pwd  = "admpwd";
		$title    = "$html_rig_admin $html_login";
	}
	else
	{
		$login_error = "";
		$valid = rig_test_user_pwd($admin, $rig_user, $rig_passwd, $login_error);

		$var_user = "user";
		$var_pwd  = "passwd";
		$title    = "$html_login";
	}

	if (   !$valid
		&& !$admin
		&& !$force_login
		&&  $pref_allow_guest
		&&  $pref_auto_guest
		&& !$rig_user
		&& !$user
		&& !$admusr)
	{
		$rig_user = $pref_guest_username;
		$rig_passwd = "";
		$valid = rig_test_user_pwd($admin, $rig_user, $rig_passwd, $login_error);
	}

	if ($force_login || !$valid)
	{
		rig_remove_login_cookies($admin);
		rig_nocache_headers();

		if ($force_login == "force")
			$login_error = "";
		else
			$force_login = "fail";

		include(rig_post_sep($dir_abs_src) . "login.php");
		exit;
	}

	// update cookies/globals

	$_COOKIE['rig_user'	]      = $rig_user;
	$_COOKIE['rig_passwd'	]  = $rig_passwd;
	$_COOKIE['rig_adm_user']   = $rig_adm_user;
	$_COOKIE['rig_adm_passwd'] = $rig_adm_passwd;

}


//************************************************************
function rig_test_user_pwd($admin, &$user, &$passwd, &$logerr)
//************************************************************
// returns TRUE if user/passwd is valid
// returns FALSE otherwise and clear the user/passwd variables
// RM 20030222 adding error message
//
// RM 20030222 documenting the format of the user/password file:
/*
	# User/password file - RIG 0.6.3 # do not remove signature line
	# Format:
	# - lines starting by # are comment, empty lines are ignored
	# - each line is in the format  "user:type:password:[display name]"
	# - the type is one of these letters:
	#	 empty : there is no password, _anythink_ is accepted
	#    t     : plain-text password
	#    c     : crypt(3) password -- cf mkpasswd(1)
	#    m     : md5 password -- not implemented yet, cf md5sum(1) and echo -n
	#    i     : invalid user, cannot log in
	# - a wrong type will invalidate the user
	# - the display name is everything after the third colon till the end of the line and is optional
	# - colons are accepted in the display name
	# - colons are NOT accepted in user name, type or any form of password!
	# - the minimum user line should look like "username:::", colons are mandatory!
	#
*/
{
	global $dir_abs_locset;
	global $dir_abs_globset;
	global $display_user;
	$valid = FALSE;
	$logerr = "";
	$display_user = "";

	// DEBUG
	//echo "<p>testing user/pwd: user='$user' pwd='$passwd' admin='$admin'\n";
//var_dump($user);
//var_dump($passwd);

	// If we get here and neither user nor passwd are assigned strings,
	// that means the script is opening the login screen and the user
	// didn't have a chance to enter the login info yet so it is an error
	// to have nothing at all but no error message should be displayed.
	if ($user === null && $passwd === null)
		return FALSE;

	if ($user)
	{
		// look for a file in the local settings
		// $dir_abs_locset is optional: it is either an empty string or an absolute path -- RM 20030919 fixed
		$file = FALSE;
		if (is_string($dir_abs_locset) && $dir_abs_locset != "")
		{
			$name = rig_post_sep($dir_abs_locset);
			$name = $admin ? $name . "admin_list.txt" : $name . "user_list.txt";
			$file = @fopen($name, "rt");
		}

		// if we cannot find it, look for a file in the global settings
		if (!$file)
		{
			$name = rig_post_sep($dir_abs_globset);
			$name = $admin ? $name . "admin_list.txt" : $name . "user_list.txt";
			$file = @fopen($name, "rt");
		}

		if ($file)
		{
			while (!feof($file))
			{
				$line = fgets($file, 1023);
				if (!is_string($line) || $line == '' || $line[0] == '#')
					continue;

				// RM 20040104 fix: remove trailing \n \r if any
				if (substr($line, -1) == "\n")
					$line = substr($line, 0, -1);

				if (substr($line, -1) == "\r")
					$line = substr($line, 0, -1);

				// see comments in function header for line format
				$a = split(':', $line, 4);
				
				if (count($a) < 4)
					continue;

				list($u, $t, $p, $n) = $a;

				// DEBUG
				// echo "<br>U=$u T=$t P=$p N=$n\n";

				if (is_string($u))
				{	
					if ($u == $user)
					{
						// invalid types are not tested, they just invalid the user
						// type 'i' is obviously not tested here :-)
						if ($t == '')
						{
							// empty password, accept everything
							$valid = TRUE;
						}
						else if ($t == 't')
						{
							$valid = ($passwd == $p);

							// leave $passwd as is, we'll store the plain password

							if (!$valid)
								$logerr = "Error: Invalid password";	// RM 20030222 TBDL translation string
						}
						else if ($t == 'c')
						{
							// if the stored password is already a crypt, it should match texto
							if (substr($passwd, 0, 2) == "c:")
							{
								$valid = (substr($passwd, 2) == $p);
							}
							else
							{
								// cf http://www.php.net/manual/en/function.crypt.php
								$valid = (crypt($passwd, $p) == $p);

								// store the crypted password from the config file
								if ($valid)
									$passwd = "c:$p";
							}

							if (!$valid)
								$logerr = "Error: Invalid password";	// RM 20030222 TBDL translation string
						}
						else if ($t == 'm')
						{
							// if the stored password is already a MD5, it should match texto
							if (substr($passwd, 0, 2) == "m:")
							{
								$valid = (substr($passwd, 2) == $p);
							}
							else
							{
								// get the MD5 of the user's input the first time
								if (!$pass_md5)
									$pass_md5 = md5($passwd);
								
								$valid = ($p == $pass_md5);

								// store the MD5 from the config file
								if ($valid)
									$passwd = "m:$p";
							}
							
							if (!$valid)
								$logerr = "Error: Invalid password";	// RM 20030222 TBDL translation string
						} // if type
					} // if user
							
					if ($valid)
					{
						$display_user = $n;
						break;
					}
				}
			} // while
			
			fclose($file);
		}
		else
		{
			$logerr = "Error: impossible to read the user/password file!";

			$valid = rig_html_error("Can't read the user/password file",
								    "Failed to read from file",
								    $file,
								    $php_errormsg);
		}
	}

	if (!$valid)
	{
		$user = "";
		$passwd = "";
	}

	if (!$logerr && !$display_user)
		$logerr = "Error: Invalid user name or password";	// RM 20030222 TBDL translation string

	// DEBUG
	// echo "<br>valid='$valid' -- logerr=$logerr\n";

	return $valid;
}


//****************************************
function rig_display_user_name($user = "")
//****************************************
{
	global $html_welcome;
	global $html_welcome_guest;	// RM 20040222 0.6.4.5
	global $html_chg_user;
	global $html_welcome;
	global $display_user;
	global $rig_user;
	global $pref_guest_username;	// RM 20040222 0.6.4.5

	if ($display_user)
		$user = $display_user;
	
	if (!$user)
		$user = $rig_user;

	if ($rig_user == $pref_guest_username)
	{
		// RM 20040222 0.6.4.5
		$s = str_replace("[name]", $user, $html_welcome_guest);
		$s = str_replace("[change-link]", "<a href=\"" . rig_self_url(-1, -1, -1, "force_login=force") . "\">$html_chg_user</a>", $s);
		echo $s;
	}
	else if ($user)
	{
		$s = str_replace("[name]", $user, $html_welcome);
		$s = str_replace("[change-link]", "<a href=\"" . rig_self_url(-1, -1, -1, "force_login=force") . "\">$html_chg_user</a>", $s);
		echo $s;
	}
}


// end

//-------------------------------------------------------------
//	$Log: login_util.php,v $
//	Revision 1.17  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//	
//	Revision 1.16  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.15  2004/07/09 05:52:37  ralfoide
//	Update
//	
//	Revision 1.14  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.13  2004/02/27 08:46:39  ralfoide
//	Don't print guest user name (avoid adding -san to guest in Japanese)
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------

// IMPORTANT: the "? >" must be the LAST LINE of this file, otherwise
// some HTTP output will be started by PHP4 and setting headers or cookies
// will fail with a PHP error message.
?>
