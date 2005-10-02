<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

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

//************************************************************************
/*
	Simple templating system for RIG.
	
	Templates are invoked by a query argument such as:
		&template=somename
		&template=			=> results in "default" being used.
	
	On init, the system will look for templates in:
		$dir_abs_templates / templates / somename
		$dir_abs_templates / templates / default
	
	Templates are loaded on demand:
		rig_init_template();
		if (rig_process_template($filename, $parameters)) exit;
	This mean each template file is only read when needed.
	The template system does not attempt to scan all template folder nor
	all files.

	Templates are stored as "filename.txt" in a directory called after the
	template name. Both the filename and template dir name will be cleaned
	up (i.e. only leaf names, no .. and other shell characters.)

	Currently a template file must have only one instruction per line,
	although there can be any amount of html or whitespace after or
	before. Lines starting with # on their FIRST character are ignored
	as comments.
	There are 3 different tags:
	- [[name]] or [[name(parameters)]] is a function call.
	- {{variable}} is replaced by the content of the global variable.
	- == xx == represents a template instruction.

	Currently the list of instructions is:
	- start-buffer: starts buffering. If the file is already cached, the cached
		version is used and processing jumps to the next ==end-buffer==
	- end-buffer: ends buffering.
	- flush: flush the browser output. There is no waranty this does anything when
		buffering is being used.
	- if followed by {{ }} or [[ ]]: Enables the block if the evaluation is true.
	- else: between if and endif.
	- endif: ends an if block.
	- insert followed by a template filename: inserts this template right there.

	Simplification:
	- if-else-endif blocks cannot be nested (will change later.)
	- end-buffer must be called after start-buffer.
	- Only one start-buffer / end-buffer per master file.

	Evaluation:
	- All {{ }} variables are considered global.
	- rig_process_template accepts an associative array as second argument
	  that allows tags to be rewritten. If a tag has an entry in the array,
	  the associated value will be directly evalued in-place. Otherwise,
	  the tag name will be evaluated as a function call. This process can
	  only be used for [[ ]] function calls without parameters.
	  
	Templates are parsed in an execution array. 
	The array contains one tuple (instruction, argument) for each line:
	[0]= Char Type:
	  	C: content 
		F: function
		V: var
		sb: start-buffer
		eb: end-buffer
		if: if
		el: else
		ei: endif
		fl: flush
		in: insert
	[1]= Value (HTML content, F/V/if parameter) or NULL
	The parameter for 'if' is a type of the same format with type F or V.
	The parameter for 'in' is the exact string that followed it.
*/
//************************************************************************



//******************************************
function rig_init_template($template = NULL)
//******************************************
{
	global $abs_curr_template_dir;
	global $dir_abs_templates;
	global $rig_template_cache;
	
	$rig_template_cache = array();

	// Sanitize input template name
	$template = rig_decode_argument($template);

	if ($template == NULL || !is_string($template) || $template == "")
		$template = "default";

	$abs_curr_template_dir = realpath(rig_post_sep($dir_abs_templates) . $template);
	if (!rig_is_dir($abs_curr_template_dir))
		$abs_curr_template_dir = NULL;
}


//************************************************
function rig_process_template($filename, $rewrite)
//************************************************
{
	// DEBUG
	// echo "<p> process template: " ; var_dump($filename); var_dump($rewrite);
	
	// Sanitize input file name
	$filename = rig_decode_argument($filename);

	// Open file and parse
	$lines = rig_parse_template($filename);
	
	// DEBUG
	// echo "<pre>"; print_r($lines); echo "</pre>";
	
	if ($lines === false || !is_array($lines) || count($lines) < 1)
		return false;

	return rig_exec_template($lines, $rewrite);
}


//----------------------------------------------------------
// Parser
//----------------------------------------------------------


//************************************
function rig_parse_template($filename)
//************************************
// Parse the filename into an execution array
// The parse result is cached in a global array.
// If a file is accedded again, the cached version is returned.
// Returns false on error.
{
	global $abs_curr_template_dir;
	global $rig_template_cache;
	
	// Check the global directory
	if (!$abs_curr_template_dir)
		return false;

	if (isset($rig_template_cache[$filename]))
		return $rig_template_cache[$filename];

	// Check the template file
	$filename = realpath(rig_post_sep($abs_curr_template_dir) . $filename);
	if (!rig_is_file($filename))
		return false;

	// Result
	$result = array();

	// Open file and read
	$f = fopen($filename, "r");

	if (!$f)
		return false;

	while($line = fgets($f))
	{
		if (strlen($line) < 1 || $line[0] == "#" || $line[0] == "\n")
			continue;

		if ($r = rig_parse_inst($line))
			$result = array_merge($result, $r);
		else if ($r = rig_parse_var($line))
			$result = array_merge($result, $r);
		else if ($r = rig_parse_func($line))
			$result = array_merge($result, $r);
		else
			$result[] = array("C", rtrim(ltrim($line)));
	}

	fclose($f);
	
	// End
	$rig_template_cache[$filename] = $result;
	return $result;
}


//***************************
function rig_parse_var($line)
//***************************
{
	$result = array();

	list($a, $b, $c) = rig_parse_atom($line, "{{", "}}");

	if ($a != NULL)
		$result[] = array("C", $a);

	if ($b != NULL)
		$result[] = array("V", $b);

	if ($c != NULL)
		$result[] = array("C", $c);

	return $result;
}


//****************************
function rig_parse_func($line)
//****************************
{
	$result = array();

	list($a, $b, $c) = rig_parse_atom($line, "[[", "]]");

	if ($a != NULL)
		$result[] = array("C", $a);

	if ($b != NULL)
		$result[] = array("F", $b);

	if ($c != NULL)
		$result[] = array("C", $c);

	return $result;
}

//****************************
function rig_parse_inst($line)
//****************************
{
	$result = array();

	list($a, $b, $c) = rig_parse_atom($line, "==", "==");

	if ($a != NULL)
		$result[] = array("C", $a);

	if ($b != NULL)
	{

		$instructions = array(
			"start-buffer" => "sb",
			"end-buffer" => "eb",
			"if"		=> "if",
			"endif"		=> "ei",
			"else"		=> "el",
			"flush"		=> "fl",
			"insert"	=> "in");

		foreach($instructions as $key => $value)
		{
			if (strncmp($b, $key, strlen($key)) == 0)
			{
				$i = array($value, NULL);
				if ($value == "if")
				{
					$b = substr($b, 2);
					if ($r = rig_parse_var($b))
						$i[1] = $r[0];
					else if ($r = rig_parse_func($b))
						$i[1] = $r[0];
				}
				else if ($value == "in")
				{
					$b = substr($b, strlen($key));
					$i[1] = rtrim(ltrim($b));
				}
				$result[] = $i;
				break;
			}
		}
	}

	if ($c != NULL)
		$result[] = array("C", $c);

	return $result;
}


//******************************************
function rig_parse_atom($line, $sep1, $sep2)
//******************************************
// Assumption: strlen($sep1/sep2) == 2
{
	$a = $b = $c = NULL;

	$p1 = strpos($line, $sep1);
	if ($p1 !== false && $p1 >= 0)
	{
		$p2 = strpos($line, $sep2, $p1+3); // +3 since we want at least one char inside
		if ($p2 !== false && $p2 >= 0)
		{
			if ($p1 > 0)
				$a = ltrim(rtrim(substr($line, 0, $p1)));
			if ($p2 < strlen($line)-2)
				$c = ltrim(rtrim(substr($line, $p2+2)));
			$b = substr($line, $p1+2, $p2-$p1-2);
		}
	}
	
	return array($a, $b, $c);
}



//----------------------------------------------------------
// Execution
//----------------------------------------------------------

//******************************************
function rig_exec_template($atoms, $rewrite)
//******************************************
// Execute the template instructions.
// Return true if succesfully output the template.
{
	$exec_else = false;

	$atoms_len = count($atoms);
	for($counter = 0; $counter < $atoms_len; $counter++)
	{
		list($key, $value) = $atoms[$counter];

		// DEBUG
		// echo "<br>" . htmlentities("[ $key: $value ]");

		switch($key)
		{
			case "C":
				echo $value . "\n";
				break;
			
			case "V":
				global $$value;
				echo $$value . "\n";
				break;
			
			case "F":
				if (isset($rewrite[$value]))
				{
					eval($rewrite[$value]);
				}
				else
				{
					$p1 = strpos($value, "(");
					if ($p1 !== false && $p1 > 0)
					{
						$p2 = strpos($value, ")", $p1+1);
						if ($p2 !== false && $p2 > $p1)
						{
							eval($value . ";");
						}
					}
					else
					{
						eval("$value();");
					}
				}
				break;
			
			case "sb":
				// RM 20050928 DEBUG deactivate buffering
				//break;
				// returns html filename to include or TRUE to start buffering and output or FALSE on errors
				$n = rig_begin_buffering();
				if (is_string($n) && $n != '')
				{
					// use cached version
					include($n);
					
					// find matching "eb" instruction
					$counter = rig_find_next_inst($atoms, $counter, "eb");
				}
				break;
			
			case "eb":
				rig_end_buffering();
				break;
			
			case "fl":
				rig_flush();
				break;
			
			case "if":
				list($kif, $vif) = $value;
				$match = true;
				switch($kif)
				{
					case "V":
						global $$vif;
						$match = !!( $$vif );
						break;
					
					case "F":
						$p1 = strpos($vif, "(");
						if ($p1 !== false && $p1 > 0)
						{
							$p2 = strpos($vif, ")", $p1+1);
							if ($p2 !== false && $p2 > $p1)
							{
								$match = !!eval("return $vif ;");
							}
						}
						else
						{
							$match = !!eval("return $vif();");
						}
						break;
				}

				// if the if matched, we'll want to find an "else" to skip up 
				$rig_temp_need_else = $match;

				if (!$match)
				{
					// skip to next else/endif, find matching else or endif
					$counter = rig_find_next_inst($atoms, $counter, "el", "ei");
					// the "if" didn't match so the "else" section needs to be
					// executed if present
					$exec_else = true;
				}
				else
				{
					// the "if" is being executed so we couldn't care less about
					// any "else" part.
					$exec_else = false;
				}
				
				break;

			case "el":
				// "else" found. Skip the next endif if not executed
				if (!$exec_else)
					$counter = rig_find_next_inst($atoms, $counter, "ei");
				break;
			
			case "ei":
				// 'endif' found, discard need to process 'else'
				$exec_else = false;
				break;
				
			case "in":
				// 'insert': value is a template file name.
				// process it recursively and abort if it returns an error.
				if (!rig_process_template($value, $rewrite))
				{
					rig_html_error("Template Parsing Error",
								   "Insert: file not found.",
								   $value);
					return false;
				}
				break;
		}
	}

	return true;	
}


//*****************************************************************
function rig_find_next_inst($atoms, $counter, $inst1, $inst2=false)
//*****************************************************************
// Advances counter to the next instruction.
// If not found, counter will be equal to count($atoms) (i.e. past end of array)
// If found, counter will point on the element PREVIOUS to the matched element,
// so far the next iteration of the main for(...; counter++) loop executes the
// matched one.
// Returns the new counter value as explained above.
{
	if ($inst2 === false)
		$inst2 = $inst1;
	
	$atoms_len = count($atoms);
	for(; $counter < $atoms_len; $counter++)
		if ($atoms[$counter][0] == $inst1 || $atoms[$counter][0] == $inst2)
			return $counter - 1;

	return $counter;
}

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2005/10/02 21:13:45  ralfoide
//	Support for else in if-else-endif
//
//	Revision 1.1  2005/10/01 23:44:27  ralfoide
//	Removed obsolete files (admin translate) and dirs (upload dirs).
//	Fixes for template support.
//	Preliminary default template for album.
//	
//-------------------------------------------------------------
?>
