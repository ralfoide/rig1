<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------

require_once(rig_require_once("RModule.php"));

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
		$m =& $this->m;
		
		$this->assert($m->IsName("rmodule"), "Error: module doesn't match name 'rmodule'");
		$this->assert($m->IsName("RModule"), "Error: module doesn't match name 'RModule'");

		$this->m->SetName('test-module');
		$this->assertEquals($m->GetName(), "test-module", "Error: Set name test-module failed");
		$this->assert($m->IsName("test-module"), "Error: module doesn't match name 'test-module'");
	}
	


} // RTest_RModule


//-------------------------------------------------------------
//	$Log$
//	Revision 1.3.2.1  2004/07/14 06:24:32  ralfoide
//	dos2unix
//
//	Revision 1.3  2004/07/07 03:26:04  ralfoide
//	Experimental modules
//	
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
