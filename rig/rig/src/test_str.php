<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************
//
// phpUnit testing for RIG -- "http://phpunit.sourceforge.net/" for more information

//***************************************
class RTest_I18l_Strings extends TestCase
//***************************************
{
	function RTest_I18l_Strings($name = "RTest_I18l_Strings")
	{
		$this->TestCase($name);
	}
	
	function setUp()
	{
	}
	
	function tearDown() 
	{
	}
	
	function test_video_strings()
	{
		check_string($this, 'html_video_codec_detail');
		check_string($this, 'html_video_install_named_player');
		check_string($this, 'html_video_install_unnamed_player');
		check_string($this, 'html_video_download');
		check_string($this, 'html_album_title');
		check_string($this, 'html_image_title');
		check_string($this, 'html_image_tooltip', true);
		check_string($this, 'html_album_tooltip');
		check_string($this, 'html_last_update');
	}

}

function check_string(&$test, $str_name, $same_as_english = FALSE)
{
	global $dir_abs_src;
	global $abs_upload_src_path;
	global $$str_name;

	$reference = NULL;

	// make sure the string doesn't already exists in the global space
	unset($$str_name);
	rig_unset_global($str_name);


	// load english
	require(rig_require_once("str_en.php", $dir_abs_src, $abs_upload_src_path));

	// check that the string exists in English
	$test->assert(isset($$str_name), "Error: $${str_name} is not defined in English");
	if (isset($$str_name))
	{
		$test->assert(is_string($$str_name), "Error: $${str_name} is not a string in English");
		$test->assert($$str_name != '', 	"Error: $${str_name} is empty in English");

		// keep the english string as a reference
		$reference = $$str_name;
	}


	$lang = array(	'str_fr.php' => 'French',
					'str_sp.php' => 'Spanish',
					'str_jp.php' => 'Japanese');

	foreach($lang as $lang_file => $lang_name)
	{
		// unset the string
		unset($$str_name);
		rig_unset_global($str_name);

		// load file
		require(rig_require_once($lang_file, $dir_abs_src, $abs_upload_src_path));

		// HACK: if the string doesn't exist "in the function scope", look for it
		// in the global scope -- this is necessary for Japanese -- RM 20040226
		if (!isset($$str_name) && isset($GLOBALS[$str_name]))
			$$str_name = $GLOBALS[$str_name];

		// check that the string exists
		$test->assert(isset($$str_name), "Error: $${str_name} is not defined in $lang_name");
		if (isset($$str_name))
		{
			$test->assert(is_string($$str_name), "Error: $${str_name} is not a string in $lang_name");
			$test->assert($$str_name != '', 	"Error: $${str_name} is empty in $lang_name");
	
			//compare with the english reference if any if ! same_as_english
			if ($reference != NULL && !$same_as_english)
				$test->assert($reference != $$str_name, "Error: $${str_name} in $lang_name is the same as in English");
		}
	}

}

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2004/03/02 10:38:01  ralfoide
//	Translation of tooltip string.
//	New page title strings.
//
//	Revision 1.1  2004/02/27 08:44:25  ralfoide
//	Test unit for strings.
//	Ability to check strings are present and different in the 4 languages
//	
//	Revision 1.1  2004/02/23 04:08:25  ralfoide
//	Setting up phpUnit testing
//	
//-------------------------------------------------------------
?>
