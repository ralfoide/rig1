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

Name:     RModAlbum
Desc:     A generic directory listing
Category: MediaDir
Depends:  
Query:    /[?&^]album[=&$]/

*/
//---------------------------------------------------------



//*****************************
class RModAlbum extends RModule
//*****************************
{
	//******************
	function RModAlbum()
	//******************
	// Initializes the class
	{
		parent::RModule();
	}

	
	//*******************
	function DebugPrint()
	//*******************
	{
		parent::DebugPrint();
	}

} // RModAlbum


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/07/07 03:25:31  ralfoide
//	Experimental modules
//
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
