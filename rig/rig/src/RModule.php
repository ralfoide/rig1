<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


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
		$this->mName = ucfirst(get_class($this));
	}

	
	//*******************
	function DebugPrint()
	//*******************
	{
		echo "<P>Class RModule: ";
		var_dump($this->mName);

		echo "<br>\n";
	}


	//****************
	function GetName()
	//****************
	//! Returns the module PHP class name, as seen by PHP.
	//! Case is NOT sensitive (PHP uses all lowercase, here R is added)
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
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//
//-------------------------------------------------------------
?>
