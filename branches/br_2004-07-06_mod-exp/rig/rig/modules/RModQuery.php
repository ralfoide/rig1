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

Name:     RModQuery
Desc:     Analyze incoming query string, register vars for outgoing queries
Category: Core

*/
//---------------------------------------------------------



//*****************************
class RModQuery extends RModule
//*****************************
{
	var $mQuery;


	//******************
	function RModQuery()
	//******************
	// Initializes the class
	{
		parent::RModule();
		
		$this->mQuery = array();
	}

	
	//*******************
	function DebugPrint()
	//*******************
	{
		parent::DebugPrint();
	}

} // RModQuery


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1.2.1  2004/07/14 06:24:17  ralfoide
//	dos2unix
//
//	Revision 1.1  2004/07/07 03:25:32  ralfoide
//	Experimental modules
//	
//-------------------------------------------------------------
?>
