<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


require_once("location.php");

// depending on the command-line, call the right php script
//    1- "admin" -> admin.php
// or 2- "image" -> image.php
// or 3- "album" or nothing -> album.php


if ($admin)
{
	require_once($dir_install . $dir_src . "admin.php");
}
else if ($image)
{
	require_once($dir_install . $dir_src . "image.php");
}
else
{
	require_once($dir_install . $dir_src . "album.php");
}

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	Revision 1.1  2001/11/26 04:35:05  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
