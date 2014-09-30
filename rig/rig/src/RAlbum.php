<?php
// vim: set tabstop=4 shiftwidth=4: //
//********************************************************
// RIG version 1.0
// Copyright (c) 2003 Ralf
//********************************************************
// $Id: RAlbum.php,v 1.4 2003/08/21 20:18:02 ralfoide Exp $
//********************************************************


// Include parent class
require_once(rig_require_once("RContentBase.php"));

// Include sibbling classes
require_once(rig_require_once("RPrefAlbum.php"));



//*******************************
class RAlbum extends RContentBase
//*******************************
{
	var $mAlbumList;
	var $mMediaList;



	//*********************
	function RAlbum(&$path)
	//*********************
	// Initializes the class
	// Note that the path is referenced here but copied in the base class
	// Derived classes should call parent constructor first
	// Derived classes should affect mPref the desired pref class
	{
		global $html_album;
		global $html_admin;
		
		echo "<h3>RAlbum -> new</h3>";

		// Adjust the given path...
		// Removes shell-magic characters from the album path
		// Nullify the image filename, if any
		$path->mSubDir   = rig_decode_argument($path->mSubDir);
		$path->mFilename = null;
		
		parent::RContentBase($path);
		$this->mPref = new RPrefAlbum;
		
		if ($rig_user->IsAdmin())
			$this->mPageTitle = $html_admin;
		else
			$this->mPageTitle = $html_album;
	}
	
	
	//********************
	function GetSubAlbum()
	//********************
	{
		returm $this->mPath->mSubDir();
	}


	//*************
	function Load()
	//*************
	// Loads the content
	// Derived classes should call this Load() first
	// then load their specific content.
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		echo "<h3>RAlbum -> Load</h3>";

		// does the album really exist?
		if ($this->mPath->DirExists())
		{
			$items = explode(SEP, $this->GetSubAlbum);
			$pretty = rig_pretty_name($items[count($items)-1], FALSE, TRUE);
			$this->mDisplayTitle = $this->mPageTitle . " - " . $pretty;
		}
		else
		{
			return FALSE;
		}

		// load preferences
		if (!parent::Load())
			return FALSE;

		return TRUE;
	}


	//*************
	function Sync()
	//*************
	// Synchronizes (save) the state
	// Currently there's only need to save the pref state
	// Needs not be derived (but can)
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		echo "<h3>RAlbum -> Sync</h3>";
		return parent::Sync();
	}


	//***************
	function Render()
	//***************
	// Renders the content to the web browser.
	// Base class does nothing. Derived classes can output
	// strings anyway needed (echo or direct print should be OK).
	// The content may be loggued using PHP's ob_start() for
	// caching purposes.
	// Returns TRUE if operation was successfull, FALSE otherwise
	{
		// Typical rendering should have steps like this:
		// 1- Document declaration
		// 2- Header
		rig_display_header($display_title);

		// 3- Body
		rig_display_body();

		rig_display_section("<h1> $display_title </h1>",
							$color_title_bg,
							$color_title_text);
		rig_display_user_name();

		if ($current_album)
			rig_display_current_album(FALSE);

		rig_load_album_list(TRUE);
		if (rig_has_albums())
		{
			rig_display_album_list();
			rig_display_album_copyright();
			rig_display_album_count();
		}

		if (rig_has_images())
		{
			rig_display_image_list();
			rig_display_album_copyright();
			rig_display_image_count();
		}

		rig_display_back_album();

		rig_display_options();

		// 4- Footer and document end
		rig_display_credits();
		rig_display_footer();

		return TRUE;
	}

} // RAlbum


//-------------------------------------------------------------
//	$Log: RAlbum.php,v $
//	Revision 1.4  2003/08/21 20:18:02  ralfoide
//	Renamed dir/path variables, updated rig_require_once and rig_check_src_file
//	
//	Revision 1.3  2003/08/18 03:06:44  ralfoide
//	OO experiment continued
//	
//	Revision 1.2  2003/07/11 15:55:25  ralfoide
//	Cosmetics
//	
//	Revision 1.1  2003/06/30 06:09:22  ralfoide
//	New OO code layout
//	
//-------------------------------------------------------------
?>
