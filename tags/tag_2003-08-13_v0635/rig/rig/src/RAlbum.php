<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


// Include parent class
require_once(rig_require_once("RContentBase.php", $dir_src));

// Include sibbling classes
require_once(rig_require_once("RPrefAlbum.php", $dir_src));



//*******************************
class RAlbum extends RContentBase
//*******************************
{
	var $mAlbumList;
	var $mMediaList;


	//*********************
	function RAlbum(&$path)
	//*********************
	// Initializes the class
	// Note that the path is referenced here but copied in the base class
	// Derived classes should call this Init() first
	// Derived classes should affect mPref the desired pref class
	{
		echo "<h3>RAlbum -> new</h3>";
		parent::RContentBase($path);
		$this->mPref = new RPrefAlbum;
	}


	//*************
	function Load()
	//*************
	// Loads the content
	// Derived classes should this Load() first
	// then load their specific content.
	{
		echo "<h3>RAlbum -> Load</h3>";
		parent::Load();
	}


	//*************
	function Sync()
	//*************
	// Synchronizes (save) the state
	// Currently there's only need to save the pref state
	// Needs not be derived (but can)
	{
		echo "<h3>RAlbum -> Sync</h3>";
		parent::Sync();
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
		$str .= "<head><title>RIG RAlbum Test</title></head>";
		// 3- Body
		$str .= "<body><h3>Empty RAlbum</h3></body>";
		// 4- Footer and document end
		$str .= "</html>";
		
		return $str;
	}

} // RAlbum


//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2003/07/11 15:55:25  ralfoide
//	Cosmetics
//
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//	
//-------------------------------------------------------------
?>
