<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************
//
//
// LOCAL PREFS for _this_ specific album.
// Defaults are in rig/settings/prefs.php and can
// be overrided here.


// login options
$pref_allow_guest		= TRUE;				// can be TRUE (default) or FALSE
$pref_auto_guest		= TRUE;				// should guest authentificate? TRUE (default) or FALSE

// global gamma override
$pref_global_gamma		= 1.0;	            // use 1.0 for no-op


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.3  2002/10/22 22:32:03  ralfoide
//	Global gamma can be changed in the site-specific pref file
//
//	Revision 1.2  2002/10/21 01:56:53  ralfoide
//	Added local override of gamma
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
