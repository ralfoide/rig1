<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------

require_once $dir_abs_src . "RModule.php";

//---------------------------------------------------------


//**********************************
class RTest_RModule extends TestCase
//**********************************
{
	var $m;
	
	function RTest_RModule($name = "RTest_RModule")
	{
		$this->TestCase($name);
	}
	
	function setUp()
	{
		$this->m = new RModule();
	}
	
	function tearDown() 
	{
		$this->m = NULL;
	}
	
	function test_init()
	{
		$this->assert($this->m != NULL, "Error: RModule not initialized in setUp");
	}
	
	function test_name()
	{
		$this->assertEquals($this->m->GetName(), "Rmodule", "Error: module is not an RModule");
		$this->assert($this->m->IsName("rmodule"), "Error: module doesn't match name 'rmodule'");
		$this->assert($this->m->IsName("RModule"), "Error: module doesn't match name 'RModule'");
	}


} // RTest_RModule


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//
//-------------------------------------------------------------
?>
