<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id: RPath.php,v 1.4 2003/08/21 20:18:02 ralfoide Exp $
//********************************************************


//*********
class RPath
//*********
{
	/*
		This class represents either a pure absolute path (i.e. local filesystem only)
		or a relative path that can be represented as an URL *and* as an absolute local path.
		
		Translation between relative and absolute paths is governed by the global variables
		set in common.php :: rig_read_prefs_paths. The current list is the following:
		
		Relative (rel)		Absolute (abs)
		-----------------------------------------
		dir_images			abs_images_path
		dir_album			abs_album_path
		dir_image_cache		abs_image_cache_path
		dir_option			abs_option_path
		dir_upload_src		abs_upload_src_path
		dir_upload_album	abs_upload_album_path


		A path can represent either a file or a directory.
		The full path for a directory *always* ends with a directory separator.
		
		Directory separators can be non obvious:
		- for URLs, it is always a '/'
		- for absolute paths, it can be either '/' or '\' depending on the platform,
		  as indicated by the SEP define in common.php
	*/

	var $mRelDir;
	var $mAbsDir;
	var $mSubDir;
	var $mFilename;


	//*****************************************************
	function RPath($rel_dir, $abs_dir, $sub_dir, $filename)
	//*****************************************************
	// Initializes the class
	{
		echo "<h3>RPath -> new</h3>";
		
		$this->mRelDir	 = $rel_dir;
		$this->mAbsDir	 = $abs_dir;
		$this->mSubDir	 = $sub_dir;
		$this->mFilename = $filename;
	}


	//**************
	function IsDir()
	//**************
	{
		return is_string($mAbsDir) && (!is_string($mFilename) || $mFilename == "");
	}


	//***************
	function IsFile()
	//***************
	{
		return is_string($mFilename) && $mFilename != "";
	}


	//******************
	function DirExists()
	//******************
	{
		$name = GetAbs();
		return file_exists($name) && is_dir($name);
	}


	//*******************
	function FileExists()
	//*******************
	{
		$name = GetAbs();
		return file_exists($name) && is_file($name);
	}


	//***************
	function GetUrl()
	//***************
	{
		// RM 20030629 TBDL Fix WIN32 -> / separator

		$str = rig_post_url($this->mRelDir);

		if (is_string($this->mSubDir))
			$str .= rig_post_url($this->mSubDir);

		if (is_string($this->mFilename))
			$str .= $this->mFilename;

		return $str;
	}


	//***************
	function GetAbs()
	//***************
	{
		$str = rig_post_sep($this->mAbsDir);

		if (is_string($this->mSubDir))
			$str .= rig_post_sep($this->mSubDir);

		if (is_string($this->mFilename))
			$str .= rig_post_sep($this->mFilename);

		return $str;
	}


	//***************
	function GetRel()
	//***************
	{
		$str = rig_post_sep($this->mRelDir);

		if (is_string($this->mSubDir))
			$str .= rig_post_sep($this->mSubDir);

		if (is_string($this->mFilename))
			$str .= rig_post_sep($this->mFilename);

		return $str;
	}


} // RPath


//-------------------------------------------------------------
//	$Log: RPath.php,v $
//	Revision 1.4  2003/08/21 20:18:02  ralfoide
//	Renamed dir/path variables, updated rig_require_once and rig_check_src_file
//	
//	Revision 1.3  2003/08/18 03:06:44  ralfoide
//	OO experiment continued
//	
//	Revision 1.2  2003/07/11 15:55:25  ralfoide
//	Cosmetics
//	
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//	
//-------------------------------------------------------------
?>
