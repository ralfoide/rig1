<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id$
//********************************************************


//*********
class RUser
//*********
{
	var $mLogin;
	var $mName;
	var $mIsAdmin;


	//**************
	function RUser()
	//**************
	// Initializes the class
	// Derived classes should call this constructor first
	{
		echo "<h3>RUser -> new</h3>";

		global $display_user;
		global $rig_user;
		global $admin;

		$this->mLogin	= $rig_user;
		$this->mName	= $display_user;
		$this->mIsAdmin	= isset($admin);
	}


	//****************
	function IsAdmin()
	//****************
	{
		return $this->mIsAdmin;
	}

} // RUser


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//
//	
//-------------------------------------------------------------
?>
