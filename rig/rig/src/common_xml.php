<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------

//****************************
function rig_xml_dom_enabled()
//****************************
// indicates if this PHP version supports the new DOM XML we need here
// we check two functions here:
// - xmldocfile : old DOM XML API... just avoid it right now
// - domxml_open_file() : new DOM XML API... let's use it
{
	$old_api = function_exists('xmldocfile');
	$new_api = function_exists('domxml_open_file');

	global $_debug_;
	if ($_debug_)
	{
		echo "<p>";
		if ( $new_api) echo "New DOM XML API is available<br>";
		if ( $old_api) echo "Old DOM XML API is available<br>";
		if (!$new_api) echo "New DOM XML API is <font color=red>NOT</font> available<br>";
		if (!$old_api) echo "Old DOM XML API is <font color=red>NOT</font> available<br>";
	}

	return $new_api;
}


//-----------------------------------------------------------------------

//***********************************
function rig_xml_read_options($album)
//***********************************
{
	if (!rig_xml_dom_enabled())
		return FALSE;

	return TRUE;
}


//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2003/02/17 10:03:00  ralfoide
//	Toying with XML
//
//-------------------------------------------------------------
?>
