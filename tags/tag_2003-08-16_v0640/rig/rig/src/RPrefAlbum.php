<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


// Include parent class
require_once(rig_require_once("RPrefBase.php", $dir_src));


//********************************
class RPrefAlbum extends RPrefBase
//********************************
{
	var $mIconDir;
	var $mIconName;


	//*******************
	function RPrefAlbum()
	//*******************
	// Initializes the class
	// Derived classes should call this constructor first
	{
		echo "<h3>RPrefAlbum -> new</h3>";
		parent::RPrefBase();

		$this->mIconDir  = "icon_dir";
		$this->mIconName = "album_icon.jpg";
	}

} // RPrefBase


//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2003/07/11 15:55:25  ralfoide
//	Cosmetics
//
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//	
//-------------------------------------------------------------
?>
