<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 0.6-1.0
// Copyright (c) 2004 Ralf
//********************************************************
// $Id$
//********************************************************


//---------------------------------------------------------



//******************
class RModuleManager
//******************
{
	var $mModules;


	//***********************
	function RModuleManager()
	//***********************
	// Initializes the class
	{
		// empty the module list
		$this->mModules = array();

		// initialize the global variable to access this instance
		global $rig_mod_man;
		$rig_mod_man = $this;
	}


	
	//*******************
	function DebugPrint()
	//*******************
	{
		echo "<P>Class RModule: ";
		var_dump($this->mName);

		echo "<br>\n";
	}


	//***********************
	function GetModule($name)
	//***********************
	//! Find the named module and loads it.
	//! Returns a pointer on the module instance or NULL.
	//! If the module is already loaded, return the existing instance.
	{
		if (isset($this->mModules['$name']))
			return $this->mModules['$name'];

		$m = $this->loadModule($name);
		
		if ($m != NULL)
			$this->mModules[$name] = $m;

		return $m;
	}



	//--------------------------------------------------------
	//--------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------
	//--------------------------------------------------------


	//************************
	function loadModule($name)
	//************************
	//! Tries to load a module by its class name
	//! The module should be in a file "name.php" with the same case.
	{
		global $dir_abs_src;
		require_once $dir_abs_src . $name . ".php";
		
		return new $name();
	}


} // RModuleManager


//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//
//-------------------------------------------------------------
?>
