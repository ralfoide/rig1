<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


// Include sibbling classes
require_once(rig_require_once("RPath.php", $dir_src));


//****************
class RContentBase
//****************
{
	var $mPref;
	var $mPath;


	//**************************
	function RContentBase($path)
	//**************************
	// Initializes the class
	// Note that the path is copied, not referenced
	// Derived classes should call this constructor first
	// Derived classes should affect mPref the desired pref class
	{
		echo "<h3>RContentBase -> new</h3>";
		$this->mPath = $path;
	}


	//*************
	function Load()
	//*************
	// Loads the content
	// Derived classes should this Load() first
	// then load their specific content.
	{
		echo "<h3>RContentBase -> Load</h3>";
		$this->mPref->Load($this->mPath);
	}


	//*************
	function Sync()
	//*************
	// Synchronizes (save) the state
	// Currently there's only need to save the pref state
	// Needs not be derived (but can)
	{
		echo "<h3>RContentBase -> Sync</h3>";
		$this->mPref->Save($this->mPath);
	}


	//***************
	function Render()
	//***************
	// Renders the content into an HTML string
	// Base class does nothing. Derived classes
	// should return a string. Avoid using echo or print directly
	// as the content may be cached for later use.
	{
		// Typical rendering should have steps like this:
		// 1- Document declaration
		$str  = "<html>";
		// 2- Header
		$str .= "<head><title>RIG RContent Test</title></head>";
		// 3- Body
		$str .= "<body><h3>Empty content</h3></body>";
		// 4- Footer and document end
		$str .= "</html>";
		
		return $str;
	}

} // RContentBase


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//
//	
//-------------------------------------------------------------
?>
