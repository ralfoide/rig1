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

	/*
		This class represents one user, which may be loggued or not.
		In the final version, the RGroup class will list groups (i.e. classes of
		user with specific rights) and one given user will below to one or more
		groups.
		
		In this first basic implementation, there is only one current user
		so it's always loggued. The only properties the user have are its name
		and being admin or not.
	*/

	//**************
	function RUser()
	//**************
	// Initializes the class
	// Derived classes should call this constructor first
	{
		// in this simplified implementation, the user must be loggued to exist
		rig_enter_login(rig_self_url(""));

		// if login is successful, continue, getting the info from the current user
		// (if not successful, a redirect on the login page will have been done)

		global $display_user;
		global $rig_user;

		$this->mLogin	= $rig_user;
		$this->mName	= $display_user;
		$this->mIsAdmin	= isset($_GET['admin']);
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
//	Revision 1.3  2003/08/18 03:06:44  ralfoide
//	OO experiment continued
//
//	Revision 1.2  2003/07/11 15:55:25  ralfoide
//	Cosmetics
//	
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//	
//-------------------------------------------------------------
?>
