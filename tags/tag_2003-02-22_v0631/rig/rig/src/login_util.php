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

//********************************************
function rig_enter_login($url, $admin = FALSE)
//********************************************
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
	global $login_error;

	global $pref_auto_guest;
	global $pref_allow_guest;
	global $pref_guest_username;

	if ($admin)
	{
		$valid    = rig_test_user_pwd($admin, &$rig_adm_user, &$rig_adm_passwd, &$login_error);
		$var_user = "admusr";
		$var_pwd  = "admpwd";
		$title    = "$html_rig_admin $html_login";
	}
	else
	{
		$valid    = rig_test_user_pwd($admin, &$rig_user, &$rig_passwd, &$login_error);
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
		$valid = rig_test_user_pwd($admin, &$rig_user, &$rig_passwd, &$login_error);
	}

	if ($force_login || !$valid)
	{
		rig_remove_login_cookies($admin);
		rig_nocache_headers();

		if ($force_login == "force")
			$login_error = "";
		else
			$force_login = "fail";

		include($dir_install . $dir_src . "login.php");
		exit;
	}
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
	# - a wrong type will invalid the user
	# - the display name is everything after the third colon till the end of the line and is optional
	# - colons are accepted in the display name
	# - colons are NOT accepted in user name, type or any form of password!
	# - the minimum user line should look like "username:::", colons are mandatory!
	#
*/
{
	global $dir_locset;
	global $dir_install;
	global $dir_globset;
	global $display_user;
	$valid = FALSE;
	$logerr = "";
	$display_user = "";

	// DEBUG
	// echo "<p>testing user/pwd: user='$user' pwd='$passwd' admin='$admin'\n";

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
					// see comments in function header for line format
					list($u, $t, $p, $n) = split(':', $line, 4);

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
				}
			}
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
	global $html_chg_user;
	global $html_welcome;
	global $display_user;
	global $rig_user;

	if ($display_user)
		$user = $display_user;
	
	if (!$user)
		$user = $rig_user;

	if ($user)
	{
		$s = str_replace("[name]", $user, $html_welcome);
		$s = str_replace("[change-link]", "<a href=\"" . rig_self_url(-1, -1, -1, "force_login=force") . "\">$html_chg_user</a>", $s);
		echo $s;
	}
}


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.8  2003/02/23 10:18:36  ralfoide
//	plain vs crypt vs MD5 password in the password file
//
//	Revision 1.7  2003/02/23 08:14:36  ralfoide
//	Login: display error msg when invalid password or invalid user
//	
//	Revision 1.6  2003/02/16 20:22:56  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.5  2002/10/24 23:57:49  ralfoide
//	Fix for end-of-file
//	
//	Revision 1.4  2002/10/24 21:32:47  ralfoide
//	dos2unix fix
//	
//	Revision 1.3  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------

// IMPORTANT: the "? >" must be the LAST LINE of this file, otherwise
// some HTTP output will be started by PHP4 and setting headers or cookies
// will fail with a PHP error message.
?>
