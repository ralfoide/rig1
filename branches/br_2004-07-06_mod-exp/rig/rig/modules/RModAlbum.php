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
//	Revision 1.1.2.2  2004/07/17 07:52:30  ralfoide
//	GPL headers
//
//	Revision 1.1.2.1  2004/07/14 06:24:17  ralfoide
//	dos2unix
//	
//	Revision 1.1  2004/07/07 03:25:31  ralfoide
//	Experimental modules
//	
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
