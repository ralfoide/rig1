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


// Load location settings (must be done before anything else)

require_once("location.php");

// Include and execute the code that checks source path validaty
// and branch onto the entry_point in the source folder

require_once("check_entry.php");


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.3  2004/07/17 07:52:30  ralfoide
//	GPL headers
//
//	Revision 1.2  2003/08/21 20:10:11  ralfoide
//	New check_entry.php
//	
//	Revision 1.1  2003/08/18 02:10:13  ralfoide
//	Reorganazing
//	
//	Revision 1.4  2003/03/12 07:11:45  ralfoide
//	New upload dirs, new entry_point, new meta override
//	
//	Revision 1.3  2003/02/16 20:09:41  ralfoide
//	Update. Version 0.6.3.1
//	
//	Revision 1.2  2002/10/20 09:03:19  ralfoide
//	Display error when require_once files cannot be located
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
