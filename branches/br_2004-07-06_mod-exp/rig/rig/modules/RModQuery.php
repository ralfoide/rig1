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
//	Revision 1.1.2.2  2004/07/17 07:52:30  ralfoide
//	GPL headers
//
//	Revision 1.1.2.1  2004/07/14 06:24:17  ralfoide
//	dos2unix
//	
//	Revision 1.1  2004/07/07 03:25:32  ralfoide
//	Experimental modules
//	
//-------------------------------------------------------------
?>
