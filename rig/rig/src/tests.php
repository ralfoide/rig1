<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************
//
// phpUnit testing for RIG -- "http://phpunit.sourceforge.net/" for more information

require_once $dir_abs_src . "phpunit.php";
?>
<html>
<head>
	<title>PHP-Unit Results</title>
	<link rel="stylesheet" type="text/css" href="phpunit.css" />
</head>
<body>


<?php

//*********************************
class phpUnit_Test extends TestCase
//*********************************
{
	function phpUnitTest($name = "phpUnit_Test")
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
		$this->assert(TRUE, FALSE, "This should fail");
		$this->assertEquals(1, 2, "This should fail");
	}

	function test_OK()
	{
		$this->assert(TRUE, TRUE, "This should work");
		$this->assertEquals(42, 42, "This should work");
	}
}

//---------------------------

$rig_suite = new TestSuite();
$rig_suite->addTest(new TestSuite("phpUnit_Test"));

//---------------------------

$result = new PrettyTestResult;
$rig_suite->run($result);
$result->report();


?>

</body>
</html>

<?php
//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/02/23 04:08:25  ralfoide
//	Setting up phpUnit testing
//
//-------------------------------------------------------------
?>
