<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


//****************
class RContentBase
//****************
{
	var $mPref;
	var $mPath;


	//*******************
	function Init(&$path)
	//*******************
	// Initializes the class
	// Derived classes should call this Init() first
	// Derived classes should affect mPref the desired pref class
	{
		$this->mPath = $path;
	}


	//*******************
	function Load(&$path)
	//*******************
	// Loads the content
	// Derived classes should this Load() first
	// then load their specific content.
	{
		$this->mPref.Load($path);
	}


	//*************
	function Sync()
	//*************
	// Synchronizes (save) the state
	// Currently there's only need to save the pref state
	// Needs not be derived (but can)
	{
		$this->mPref.Save($this->mPath);
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
		str  = "<html>";
		// 2- Header
		str .= "<head><title>RIG RContent Test</title></head>";
		// 3- Body
		str .= "<body><h3>Empty content</h3></body>;
		// 4- Footer and document end
		str .= "</html>;
		
		return str;
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
