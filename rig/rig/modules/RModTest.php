<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------
/*

rig-module-1.0

Name:     RModTest
Desc:     Module for phpUnit testing
Category: Core

*/
//---------------------------------------------------------



//****************************
class RModTest extends RModule
//****************************
{
	var $mTested;

	//******************
	function RModQuery()
	//******************
	// Initializes the class
	{
		parent::RModule();
		
		$this->mTested = FALSE;
	}

	
	//*******************
	function DebugPrint()
	//*******************
	{
		parent::DebugPrint();
	}


	//******************
	function SetTested()
	//******************
	{
		$this->mTested = TRUE;
	}


	//******************
	function GetTested()
	//******************
	{
		return $this->mTested;
	}
	

} // RModQuery


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/07/07 03:25:32  ralfoide
//	Experimental modules
//
//-------------------------------------------------------------
?>
