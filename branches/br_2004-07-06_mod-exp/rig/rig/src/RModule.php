<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2004, Raphael MOLL.

	This file is part of RIG-Thumbnail.

	RIG-Thumbnail is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	RIG-Thumbnail is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with RIG-Thumbnail; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
//************************************************************************


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
//	Revision 1.3.2.2  2004/07/17 07:52:30  ralfoide
//	GPL headers
//
//	Revision 1.3.2.1  2004/07/14 06:24:32  ralfoide
//	dos2unix
//	
//	Revision 1.3  2004/07/07 03:26:04  ralfoide
//	Experimental modules
//	
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
