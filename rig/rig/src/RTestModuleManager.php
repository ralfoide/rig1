<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------

require_once $dir_abs_src . "RModuleManager.php";

//---------------------------------------------------------


//*****************************************
class RTest_RModuleManager extends TestCase
//*****************************************
{
	var $m;
	
	function RTest_RModuleManager($name = "RTest_RModuleManager")
	{
		$this->TestCase($name);
	}
	
	function setUp()
	{
		$this->m = new RModuleManager();
	}
	
	function tearDown() 
	{
		$this->m = NULL;
	}
	
	function test_init()
	{
		$this->assert($this->m != NULL, "Error: RModuleManager not initialized in setUp");
		$this->assert(is_array($this->m->mModules), "Error: mModules not an array");
		$this->assert(count($this->m->mModules) == 0, "Error: mModules not empty");
	}
	
	function test_get_module()
	{
		$name = 'RModAlbum';
		$this->assertEquals(isset($this->m->mModules[$name]), FALSE, "Error: module already loaded");

		$mod = $this->m->GetModule("RModAlbum");

		$this->assert($mod != NULL, "Error: Failed to load test module");
		$this->assertEquals(isset($this->m->mModules[$name]), TRUE, "Error: module not internaly listed");

		if ($mod != NULL)
		{
			$this->assert($mod->IsName($name), "Error: Loaded module name is not correct");
			$this->assertEquals($mod->GetName(), "Rmodalbum", "Error: Loaded module name has wrong case");
		}

	}


} // RTest_RModuleManager


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//
//-------------------------------------------------------------
?>
