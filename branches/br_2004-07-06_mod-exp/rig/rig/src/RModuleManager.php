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

define("RIG_MOD_SIG", "rig-module-1.0");

//---------------------------------------------------------


//******************
class RModuleManager
//******************
{
	//! mModules contains an entry per module:
	//! [lowercase-mod-name] => array:
	//		'i'=instance ptr,
	//		'f'=file name (abs path)
	//		and any field from the description block:
	//		'field'=value from desc block
	var $mModules;


	//***********************
	function RModuleManager()
	//***********************
	// Initializes the class
	{
		// initialize the global variable to access this instance
		global $rig_mod_man;
		$rig_mod_man = $this;

		// empty the module list
		$this->mModules = array();
		
		// get module description list
		$this->GetModuleList();
	}


	
	//*******************
	function DebugPrint()
	//*******************
	{
		echo "<P>Class RModule: ";
		var_dump($this->mName);

		echo "<br>\n";
	}


	//**********************
	function GetModuleList()
	//**********************
	//! Initialized the internal module list from the directory.
	//! It will unload existing modules first.
	{
		$this->UnloadAllModules();

		// empty the module list
		$this->mModules = array();

		// get module description list
		$this->parseModDir();
	}


	//*************************
	function UnloadAllModules()
	//*************************
	//! Unload all modules previously loaded.
	{
		foreach($this->mModules as $m)
		{
			if (isset($m['i']) && $m['i'] != NULL)
			{
				$m['i']->OnUnload();
				unset($m['i']);
			}
		}
	}


	//*********************
	function CountModules()
	//*********************
	{
		return count($this->mModules);
	}


	//***********************
	function HasModule($name)
	//***********************
	{
		$name = strtolower($name);

		return (isset($this->mModules['$name']));
	}


	//***************************
	function GetModuleDesc($name)
	//***************************
	{
		$name = strtolower($name);

		if (isset($this->mModules['$name']))
			return $this->mModules['$name'];
		else
			return NULL;		
	}


	//****************************
	function IsModuleLoaded($name)
	//****************************
	{
		$name = strtolower($name);

		if (!isset($this->mModules['$name']))
		{
			// Unknown module.
			// RM 20040607 TODO print warning message (global warning function)
			echo "WARNING: IsModuleLoaded: Unknwon module '$name'<p>";
			return FALSE;
		}
		
		// get a reference on the module description
		$m =& $this->mModules['$name'];

		// indicate if there's already an instance
		return isset($m['i']) && $m['i'] != NULL;
	}


	//***********************
	function GetModule($name)
	//***********************
	//! Find the named module and loads it.
	//! Returns a pointer on the module instance or NULL.
	//! If the module is already loaded, return the existing instance.
	{
		$lcname = strtolower($name);
		if (!isset($this->mModules['$lcname']))
		{
			// Unknown module.
			// RM 20040607 TODO print warning message (global warning function)
			echo "WARNING: GetModule: Unknown module '$name'<p>";
			return NULL;
		}

		// get a reference on the module description
		$m =& $this->mModules['$lcname'];
		
		// if there's already an instance, use it
		// else get the instance
		if (!isset($m['i']) || $m['i'] == NULL)
			$m['i'] = $this->loadModule($name);
		
		return $m['i'];
	}



	//--------------------------------------------------------
	//--------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------
	//--------------------------------------------------------


	//********************
	function parseModDir()
	//********************
	//! Reads the module directory.
	//! Fills in the mModules array.
	{
		global $dir_abs_mod;
		
		$handle = @opendir($dir_abs_mod);
		if (!$handle)
		{
			rig_html_error("Load Module List",
						   "Can't open module directory, make sure directory exist and can be accessed",
						   $dir_abs_mod,
						   $php_errormsg);
		}
		else
		{
			while (($file = readdir($handle)) !== FALSE)
			{
				if (preg_match('/(.*)\.php$/', $file, $matches) == 1)
				{
					$abs_file = rig_post_sep($dir_abs_mod) . $file;
					if (rig_is_file($abs_file))
						$this->parseModDescBlock($abs_file, $matches[1]);
				}
			}
			closedir($handle);
		}
	}


	//*********************************************
	function parseModDescBlock($abs_file, $modname)
	//*********************************************
	//! Parse the description block of a module and
	//! fills an entry in the mModules array for it.
	{
		$file = @fopen($abs_file, 'r');

		if (!$file)
		{
			rig_html_error("Load Module File",
						   "Can't open module file, make sure file can be accessed.",
						   $abs_file,
						   $php_errormsg);
			return;
		}

		// the result is held in this array
		$desc = array();

		// look for the module block sig in the 50 first lines...
		$has_header = FALSE;
		for($n = 0; $n < 50 && !feof($file); $n++)
		{
			$line = fgets($file, 1023);

			// if the line is empty, we skip it
			if (!is_string($line) || !$line || $line == FALSE)
				continue;

			// strip end-of-line
			if (substr($line, -1) == "\n")
				$line = substr($line, 0, -1);
			if (substr($line, -1) == "\r")
				$line = substr($line, 0, -1);

			// if the line is empty, we skip it
			if (strlen($line) <= 0)
				continue;


			if (!$has_header)
			{
				$has_header = (preg_match('/^[ \t]*' . RIG_MOD_SIG . '[ \t]*$/', $line) == 1);
				// if a header has been found, reset the line limit counter
				if ($has_header)
					$n = 0;
			}
			else // has_header
			{
				// a header has been found earlier.
				// next lines can be empty or comment or a valid field
				if (preg_match('/^[ \t]*([a-zA-Z0-9-_]+):[ \t]*(.+)[ \t]*$/', $line, $matches) == 1)
				{
					// this is a field with a non empty value
					$desc[$matches[1]] = $matches[2];
				}
				else if (preg_match('/^[ \t]*#/', $line) == 1)
				{
					// this is a comment... ignore it.
				}
				else
				{
					// abort processing at the first non-empty, non-comment, non-field line
					break;
				}
			} // if has header
		} // for feof or 50 lines


		// Set the module description block
		
		if ($has_header)
			$this->mModules['$name'] = $desc;


		fclose($file);
	}


	//************************
	function loadModule($name)
	//************************
	//! Tries to load a module by its class name
	//! The module should be in a file "$name.php" with the same case.
	{
		global $dir_abs_mod;

		require_once(rig_require_once($name . ".php", $dir_abs_mod));
		
		$m = new $name();
		$m->OnLoad();
		
		return $m;
	}


} // RModuleManager

//-------------------------------------------------------------
//	$Log$
//	Revision 1.3.2.2  2004/07/14 06:24:32  ralfoide
//	dos2unix
//
//	Revision 1.3.2.1  2004/07/09 05:49:37  ralfoide
//	Fixed typo in dir mod variable
//	
//	Revision 1.3  2004/07/07 03:26:04  ralfoide
//	Experimental modules
//	
//	Revision 1.1  2004/06/03 14:16:24  ralfoide
//	Experimenting with module classes
//	
//-------------------------------------------------------------
?>
