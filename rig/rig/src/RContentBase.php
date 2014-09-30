<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id: RContentBase.php,v 1.4 2003/08/21 20:18:02 ralfoide Exp $
//********************************************************


// Include sibbling classes
require_once(rig_require_once("RPath.php"));


//****************
class RContentBase
//****************
{
	var $mPref;
	var $mPath;

	var $mPageTitle;
	var $mDisplayTitle;


	//**************************
	function RContentBase($path)
	//**************************
	// Initializes the class
	// Note that the path is copied, not referenced
	// Derived classes should call parent constructor first
	// Derived classes should affect mPref the desired pref class
	{
		echo "<h3>RContentBase -> new</h3>";
		$this->mPath = $path;
	}


	//*************
	function Load()
	//*************
	// Loads the content
	// Derived classes should call this Load() first
	// then load their specific content.
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		echo "<h3>RContentBase -> Load</h3>";
		return $this->mPref->Load($this->mPath);
	}


	//*************
	function Sync()
	//*************
	// Synchronizes (saves) the state
	// Currently there's only need to save the pref state
	// Needs not be derived (but can)
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		echo "<h3>RContentBase -> Sync</h3>";
		
		ok = $this->mPref->Save($this->mPath);
		
		// rig_setup_db was called in common.php
		rig_terminate_db();
		return ok;
	}


	//***************
	function Render()
	//***************
	// Renders the content to the web browser.
	// Base class does nothing. Derived classes can output
	// strings anyway needed (echo or direct print should be OK).
	// The content may be loggued using PHP's ob_start() for
	// caching purposes.
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		// Typical rendering should have steps like this:
		// 1- Document declaration
		echo "<html>";
		// 2- Header
		echo "<head><title>RIG RContentBase</title></head>";
		// 3- Body
		echo "<body><h3>Empty content</h3></body>";
		// 4- Footer and document end
		echo "</html>";

		return TRUE;
	}

} // RContentBase


//-------------------------------------------------------------
//	$Log: RContentBase.php,v $
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
