<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
// $Id$

//******************
function http_auth()
//******************
{
	if(!isset($PHP_AUTH_USER))
	{
		header("WWW-Authenticate: Basic realm=\"My Realm\"");
		header("HTTP/1.0 401 Unauthorized");
		echo "Text to send if user hits Cancel button\n";
		exit;
	}
	else
	{
		echo "<p>Hello $PHP_AUTH_USER.</p>";
		echo "<p>You entered $PHP_AUTH_PW as your password.</p>";
	}
}


//********************************
function rig_print_array_str($str)
//********************************
{
	echo "str[] = '$str'<br>\n";
	for($i=0, $n=strlen($str); $i<$n; $i++)
	{
		$a = ord($str{$i});
		echo "str[$i] = {$a} = '{$str{$i}}'<br>\n";
	}
}


//*******************
function read_prefs()
//*******************
{
	//use @ to prevent any error from being reported
	$fp = @fopen(PREF_FILE_NAME, 'r');

	if (!$fp)
	{
		rig_html_error("Preferences", "Can't access preference file", PREF_FILE_NAME, $php_errormsg);
		return;
	}

	while(!feof($fp))
	{
		$line = fgets($fp, 4096);
		if (!$line || $line[0]=='#' || $line=="\n") continue;

		if (eregi("([a-z0-9_]+)[ \t]+([^\n]+)", $line, $reg))
		{
			$name = $reg[1];
			$value = $reg[2];

			if ($name && $value)
			{
				// assign the value to a global variable
				global $$name;
				$$name = $value;

				// debug
				// echo "name: $name -- value: $value -- text: $line<br>\n";
			}
		}
	}

	fclose($fp);

	rig_read_prefs_paths();
}


//*************************
function show_album($album)
//*************************
{
	global $abs_album_path;
	$abs_dir = $abs_album_path . rig_prep_sep($album);

	// debug
	// echo "Album = $album<br>abs = $abs_dir<p>\n";

	echo "<center>\n";

	if ($album)
	{
		echo "<h2>Current</h2><p>\n";
		echo "<a href=\"./album.php\">[Root]</a>\n";
		$name = "";
		$items = explode(SEP, $album);
		while($items)
		{
			$item = array_shift($items);
			if (!$item)
				break;
			if ($items)	// if not last...
				$tag = 'i';
			else
				$tag = 'b';
			echo "&nbsp;|&nbsp;";
			$name = rig_post_sep($name) . $item;
			$pretty = pretty_name($item);
			echo "<$tag><a href=\"./album.php?album=$name\">$pretty</a></$tag>\n";
		}
		echo "<p>\n";

	}

	// get all files and dirs, recurse in dirs first
	// display sub albums if any
	$file_list = array();
	$handle = @opendir($abs_dir);
	if (!$handle)
		rig_html_error("Album", "Album directory does not exist!", $abs_dir, $php_errormsg);
	else
	{
		create_preview_dir($album);

		$n = 0;
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
			{
				$abs_file = $abs_dir . rig_prep_sep($file);
				if (is_dir($abs_file))
				{
					$n++;
					if ($n == 1)
						echo "<h2>Albums</h2><p>\n";

					$name = rig_post_sep($album) . $file;
					$pretty = pretty_name($file);
					echo "<a href=\"./album.php?album=$name\">$pretty</a><br>\n";
				}
				else if (rig_valid_ext($file))
				{
			    	$file_list[] = $file;
			    }
			}
		}

		if ($n)
			echo "<p>\n";

		closedir($handle);
	}

	echo "<h2>Images</h2><p>\n";
	echo "<table border=0><tr><td>\n";
	$i = 0;
	$n = 5;
	// process all files
	if (!count($file_list))
	{
		echo "<br>This album does not contain any image.<p>";
	}
	else
	{
		foreach($file_list as $file)
		{
			$album_file = rig_post_sep($album) . $file;
			$pretty = pretty_name($file);
			$preview = preview_file($album_file);
			$link = "\"./image.php?album=$name&image=$file\"";
			echo "<center><a href=$link><img src=\"$preview\" alt=\"$pretty\" border=0></a><br>\n";
			echo "<a href=$link>$pretty</a></center><br>\n";

			$i++;
			if ($i >= $n)
			{
				$i = 0;
				echo "</td></tr><tr><td>\n";
			}
			else
			{
				echo "</td><td>\n";
			}
		}
	}
	echo "</td></tr></table>\n";
	echo "</center><p>\n";
}



//-----------------------------------------------------------------------
// end


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
//-------------------------------------------------------------
?>
