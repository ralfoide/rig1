<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2004, Raphael MOLL.

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


//---------------------------------------------------------


require_once(rig_require_once("RModuleManager.php"));


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
		$m =& $this->m;

		$this->assert($m != NULL, "Error: RModuleManager not initialized in setUp");
		$this->assert($m->CountModules() == 0, "Error: No modules loaded");
	}
	
	function test_has_module()
	{
		$m =& $this->m;
		$name = 'RModTest';
		
		$this->assertNotNull(NULL,         "Error: NULL != NULL");
		$this->assertEquals ($m->HasModule($name),      TRUE,  "Error: $name not a known module");
		$this->assertNotNull($m->GetModuleDesc($name),         "Error: $name does not have a description");
		$this->assertEquals ($m->IsModuleLoaded($name), FALSE, "Error: $name is already loaded");
	}
	
	function test_get_module_desc()
	{
		$m =& $this->m;
		$name = 'RModTest';

		$d = $m->GetModuleDesc($name);

		$this->assertIsNull($d, "Error: $name does not have a description");
		$this->assertEquals($m->IsModuleLoaded($name), FALSE, "Error: $name is already loaded");
	}
	
	function test_get_module()
	{
		$m =& $this->m;
		$name = 'RModTest';
		
		$this->assertEquals($m->HasModule($name),      FALSE, "Error: $name not a known module");
		$this->assertNotNull($m->GetModuleDesc($name),         "Error: $name does not have a description");
		$this->assertEquals($m->IsModuleLoaded($name), FALSE, "Error: $name is already loaded");

		$mod = $m->GetModule("RModTest");

		$this->assert($mod != NULL, "Error: Failed to load $name");
		$this->assertEquals($m->IsModuleLoaded($name), FALSE,  "Error: $name is not listed as loaded");

		if ($mod != NULL)
		{
			$this->assert($mod->IsName($name), "Error: Loaded $name name is not correct");
		}
	}


} // RTest_RModuleManager


//-------------------------------------------------------------
//	$Log$
//	Revision 1.3.2.2  2004/07/17 07:52:31  ralfoide
//	GPL headers
//
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
