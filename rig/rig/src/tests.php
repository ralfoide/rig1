<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id: tests.php,v 1.9 2005/09/25 22:33:47 ralfoide Exp $

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
//
// phpUnit testing for RIG -- "http://phpunit.sourceforge.net/" for more information

require_once($dir_abs_src . "common.php");
require_once(rig_require_once("phpunit.php"));

?>
<html>
<head>
	<title>PHP-Unit Results</title>
	<link rel="stylesheet" type="text/css" href="phpunit.css" />
</head>
<body>

<center>
<a href="<?= rig_self_url(-1, -1, RIG_SELF_URL_TESTS) ?>"><?= RIG_SOFT_NAME ?> Unit Tests Page</a>
</center>
<hr>

<?php

//**********************************
class RTest_phpUnit extends TestCase
//**********************************
{
	function RTest_phpUnit($name = "RTest_phpUnit")
	{
		$this->TestCase($name);
	}
	
	function setUp()
	{
	}
	
	function tearDown() 
	{
	}
	
	function test_fails()
	{
		$this->assert(FALSE, "This should fail");
		$this->assertEquals(1, 2, "This should fail");
	}

	function test_OK()
	{
		$this->assert(TRUE, "This should work");
		$this->assertEquals(42, 42, "This should work");
	}
}

//---------------------------

$rig_suite = new TestSuite();
// $rig_suite->addTest(new TestSuite("RTest_phpUnit")); // -- self-test boot, use if nothing else works

require_once(rig_require_once("test_str.php"));
$rig_suite->addTest(new TestSuite("RTest_I18l_Strings"));

//---------------------------

$result = new PrettyTestResult;
$rig_suite->run($result);
$result->report();


?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log: tests.php,v $
//	Revision 1.9  2005/09/25 22:33:47  ralfoide
//	Removed modules
//	
//	Revision 1.8  2004/12/25 09:46:47  ralfoide
//	Fixes and cleanup
//	
//	Revision 1.7  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.6  2004/07/09 05:52:48  ralfoide
//	Update
//	
//	Revision 1.5  2004/07/06 04:57:04  ralfoide
//	Preparing to tag 0.6.4.5
//	
//	Revision 1.4  2004/06/03 14:16:25  ralfoide
//	Experimenting with module classes
//	
//	Revision 1.3  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.2  2004/02/27 08:44:57  ralfoide
//	New test unit for strings
//	
//	Revision 1.1  2004/02/23 04:08:25  ralfoide
//	Setting up phpUnit testing
//-------------------------------------------------------------
?>
