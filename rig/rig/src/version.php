<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2001-2005 and beyond, Raphael MOLL.

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


$rig_vernum  =  1.01;
$rig_version = "1.0.1";

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.31  2006/09/12 14:15:49  ralfoide
//	Fixed broken image resize
//
//	Revision 1.30  2006/06/24 21:20:34  ralfoide
//	Version 1.0:
//	- Source: Set filename in thumbnail streaming headers
//	- Source: Added pref_site_name and pref_site_link.
//	- Fix: Fixed security vulnerability in check_entry.php
//	
//	Revision 1.29  2006/01/11 08:21:54  ralfoide
//	Added polish translation by Alfred Broda, http://krypa.homelinux.net/
//	
//	Revision 1.28  2005/12/26 22:09:30  ralfoide
//	Added link to view full resolution image.
//	Album thumbnail in admin album page.
//	Incorrect escaping of "&" in jhead call.
//	Submitting 0.7.3.
//	
//	Revision 1.27  2005/11/26 18:00:53  ralfoide
//	Version 0.7.2.
//	Ability to have absolute paths for albums, caches & options.
//	Explained each setting in location.php.
//	Fixed HTML cache invalidation bug.
//	Added HTML cache to image view and overview.
//	Added /th to stream images & movies previews via PHP.
//	
//	Revision 1.26  2005/10/01 23:44:27  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//	
//	Revision 1.25  2005/09/26 01:13:35  ralfoide
//	Fixed vernum to match version.
//	
//	Revision 1.24  2005/09/25 22:34:06  ralfoide
//	Upgrading to version 0.7
//	
//	Revision 1.23  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.22  2004/07/09 05:53:33  ralfoide
//	Started version 0.6.5. Now discarding the useless sub sub version numbers.
//	
//	Revision 1.21  2004/07/06 04:57:04  ralfoide
//	Preparing to tag 0.6.4.5
//	
//	Revision 1.20  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.19  2004/02/18 07:41:18  ralfoide
//	Version 0.6.4.5/dev
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	[...]
//
//	Revision 1.7  2001/11/17 12:35:58  ralf
//	Manage albums with dates YMD and display as MDY. Version 0.5.2
//-------------------------------------------------------------
?>
