<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------
/*

rig-module-1.0

Name:     RModule
Desc:     A generic directory listing
Category: MediaDir
Depends:  
Query:    /[?&^]album[=&$]/

*/
//---------------------------------------------------------



//***********
class RModule
//***********
{
	var $mName;


	//****************
	function RModule()
	//****************
	// Initializes the class
	{
		$this->mName = get_class($this);
	}


	//***************
	function OnLoad()
	//***************
	//! Indicates this module has just beeen loaded
	//! (i.e. referenced by the module manager)
	{
	}


	//*****************
	function OnUnload()
	//*****************
	//! Indicates this module is about to be unloaded
	//! (i.e. unreferenced by the module manager)
	{
	}


	
	//*******************
	function DebugPrint()
	//*******************
	{
		echo "<P>Class RModule: ";
		var_dump($this->mName);

		echo "<br>\n";
	}


	//*********************
	function SetName($name)
	//*********************
	//! Sets the module PHP class name.
	{
		$this->mName = $name;
	}


	//****************
	function GetName()
	//****************
	//! Returns the module PHP class name.
	//! Case is NOT sensitive.
	{
		return $this->mName;
	}


	//********************
	function IsName($name)
	//********************
	//! Returns TRUE if this module has the given name.
	//! Comparison is case insensitive.
	{
		return $name != NULL && is_string($name) && strcasecmp($name, $this->GetName()) == 0;
	}


} // RModule


//-------------------------------------------------------------
//	$Log$
//	Revision 1.3  2004/07/07 03:26:04  ralfoide
//	Experimental modules
//
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
