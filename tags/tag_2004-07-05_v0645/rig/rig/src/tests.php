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

require_once $dir_abs_src . "common.php";
require_once $dir_abs_src . "phpunit.php";

?>
<html>
<head>
	<title>PHP-Unit Results</title>
	<link rel="stylesheet" type="text/css" href="phpunit.css" />
</head>
<body>


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


//*************************************
class RTest_InnerClass extends TestCase
//*************************************
{
	function RTest_phpUnit($name = "RTest_InnerClass")
	{
		$this->TestCase($name);
	}

	function test_include_class_file()
	{
		// To enable this test, you must comment out the require_once for RTestModule.php below...
		// $this->assertEquals(class_exists("RModule"), FALSE, "RModule already defined before require_once!");
		
		global $dir_abs_src;
		require_once $dir_abs_src . "RModule.php";

		$this->assertEquals(class_exists("RModule"), TRUE, "RModule not defined after require_once!");
		
		$m = new RModule();
		$this->assert($m != NULL, "RModule can't be instantiated");
	}

	/* can't nest class definitions -- this creates a parser error
	function test_inner_class()
	{
		class MyInnerClass
		{
			var $a;
			
			function MyInnerClass()
			{
				$a = 1;
			}
		}
	}
	*/
}

//---------------------------

$rig_suite = new TestSuite();
// $rig_suite->addTest(new TestSuite("RTest_phpUnit")); // -- self-test boot
$rig_suite->addTest(new TestSuite("RTest_InnerClass"));

require_once $dir_abs_src . "test_str.php";
$rig_suite->addTest(new TestSuite("RTest_I18l_Strings"));

if ($rig_vernum >= 0.65)
{
	require_once $dir_abs_src . "RTestModuleManager.php";
	$rig_suite->addTest(new TestSuite("RTest_RModuleManager"));
	
	require_once $dir_abs_src . "RTestModule.php";
	$rig_suite->addTest(new TestSuite("RTest_RModule"));
}

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
