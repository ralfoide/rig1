<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


//*************
class RPrefBase
//*************
{
	var $mShortDesc;
	var $mLongDesc;
	var $mVisible;


	//******************
	function RPrefBase()
	//******************
	// Initializes the class
	// Derived classes should call this constructor first
	{
		echo "<h3>RPrefBase -> new</h3>";
		$this->mVisible   = true;
		$this->mShortDesc = "";
		$this->mLongDesc  = "";
	}


	//*******************
	function Load(&$path)
	//*******************
	// Loads the prefs
	{
		echo "<h3>RPrefBase -> Load</h3>";
		var_dump($path->GetAbs());
		var_dump($path->GetRel());
	}


	//*******************
	function Save(&$path)
	//*******************
	// Saves the prefs
	{
		echo "<h3>RPrefBase -> Save</h3>";
		var_dump($path->GetAbs());
		var_dump($path->GetRel());
		
		echo "<h3>Object vars:</h3>";
		print_r(get_object_vars($this));
		
		echo "<h3>Class vars:</h3>";
		print_r(get_class_vars($this));
	}


} // RPrefBase


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
