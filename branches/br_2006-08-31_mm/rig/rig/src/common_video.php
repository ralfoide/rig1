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


//-----------------------------------------------------------------------


//****************************
function rig_video_type($type)
//****************************
// Decodes the type string for a video.
// The type string has the following format:
//   video/subtype[:realmimetype]
// When realmimetype, "video/subtype" is the real mime-type.
//
// The mime-type can be used in an HTTP header.
//
// Result is returned in an array:
// ['s'] = sub-type for internal usage
// ['m'] = mime-type
//
// If the type is not a video type, NULL is returned.
{
	if (preg_match('@^video/([^:]+)(?::(.+))?$@', $type, $m) == 1)
	{
		$ret = array('s' => $m[1]);
		if (isset($m[2]) && $m[2])
			$ret['m'] = $m[2];
		else
			$ret['m'] = $m[0];
		return $ret;
	}

	return NULL;
}


//*******************************
function rig_display_video($type)
//*******************************
{
	global $dir_album;
	global $abs_album_path;
	global $current_real_album;					// RM 20030907
	global $current_image;
	global $pretty_image;
	global $rig_img_size;
	global $pref_image_size;
	global $pref_image_quality;
	global $html_video_codec_detail;			// RM 20040222
	global $html_video_install_named_player;	// RM 20040222
	global $html_video_install_unnamed_player;	// RM 20040222
	global $html_video_download;				// RM 20040226


	global $_test_;
	if (isset($_test_)) echo "Test type: $_test_<br>";

	if ($rig_img_size != -2 && $rig_img_size < 1)
		$rig_img_size = $pref_image_size;

	// get the file type
	$type_info = rig_video_type($type);
	if ($type_info)
	{
        $subtype = $type_info["s"];   // RM 20081002 for MM branch

		// RM 20030628 v0.6.3.4

		// get the full relative URL to the media file
		$full = rig_post_sep($dir_album) . rig_post_sep($current_real_album) . $current_image;

		// get actual size of media
		$abs = rig_post_sep($abs_album_path) . rig_post_sep($current_real_album) . $current_image;
		$info = rig_image_info($abs);
		
		if (isset($info["w"]))
			$sx = $info["w"];
		else
			$sx = 320;

		if (isset($info["h"]))
			$sy = $info["h"];
		else
			$sy = 240;

		// for QT, add 16 to the height to see the controls (cf doc above)
		$sy2 = $sy+16;


		// get some details based on the video codec
		$codec_info = array_merge(rig_display_codec_detail($info), rig_display_os_detail());
		$codec_install = "";

		$codec_is_divx = false;
		$codec_is_windowsmedia = false;

		if ($codec_info != NULL)
		{
			if (is_array($codec_info))
			{
				$codec_name = array_shift($codec_info);
				$codec_url  = $codec_info;
			}
			else if (is_string($codec_info))
			{
				$codec_name = $codec_info;
			}

			$codec_detail = str_replace('[codec_name]', $codec_name, $html_video_codec_detail); // RM 20040222 translated

			if ($codec_url != NULL && is_array($codec_url))
			{
				foreach($codec_url as $name => $url)
				{
					if (strpos($url, "rig:" ) === 0)
					{
						// filter out and process rig-specific commands

						if (strpos($url, "divx"        ) !== FALSE)
							$codec_is_divx = TRUE;
						if (strpos($url, "windowsmedia") !== FALSE)
							$codec_is_windowsmedia = TRUE;
					}
					else
					{
						// display links
						// // RM 20040222 translated strings

						if (is_string($name) && preg_match("/\(([^\)]*)\)(.*)/", $name, $matches) == 1)
						{
							$t = str_replace('[url]',  $url,        $html_video_install_named_player);
							$t = str_replace('[name]', $matches[2], $t);
							$codec_install .= rig_video_javascript_testline($matches[1], $t);
						}
						else if (is_string($name))
						{
							$t = str_replace('[url]',  $url,  $html_video_install_named_player);
							$t = str_replace('[name]', $name, $t);
							$codec_install .= $t;
						}
						else
						{
							$codec_install .= str_replace('[url]', $url, $html_video_install_unnamed_player);
						}
					}
				}
			}
		}
		else
		{
			$codec_detail = "";
		}


		if ($subtype == "avi")
		{
			// ----------------------------------------
			// -------------- AVI ---------------------
			// ----------------------------------------
	
			// Link
			// Windows Media Player <object>
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/dnwmt/html/addwmwebpage.asp?frame=true&hidetoc=true
			//
			// WMV 7   class id: 6BF52A52-394A-11d3-B153-00C04F79FAA6
			// WMV 6.4 class id: 22D6f312-B0F6-11D0-94AB-0080C74C7E95
			
			// Other random links:
			// http://www.macromedia.com/support/dreamweaver/ts/documents/mediaplayer.htm
			// http://www.webreference.com/js/column51/install.html
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/wmp6sdk/htm/controlclsids.asp
			//		MediaPlayer 22D6F312-B0F6-11D0-94AB-0080C74C7E95
			//		NSPlay 2179C5D3-EBFF-11cf-B6FD-00AA00B4E220
			//		ActiveMovie 05589FA1-C356-11CE-BF01-00AA0055595A
			// http://msdn.microsoft.com/library/default.asp?url=/library/en-us/dnwmt/html/6-4compat.asp
			//		Windows Media Player 9 Series 6BF52A52-394A-11d3-B153-00C04F79FAA6
			// http://home.maconstate.edu/dadams/Tutorials/AV/AV03/av03-03.htm
	
			// Win32 Moz 1.4: doesn't work
			// Win32 IE6: unsafe ActiveX, won't play
			// <!-- object data="<@= $full @>" type="video/x-msvideo" / -->
			// Win32  Moz 1.4: WMV7 displays but doesn't play
			// Win32  IE6: WMV9 shows with bad aspect ratio, plays.
			// MacOSX Safari: yes if WMV 7.1/MacOS X installed before
	
			rig_video_generate_javascript(
				array(
				"is_win" => array(
							TRUE,
							"<object "
							. "	classid=\"CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6\""
							. "	codebase=\"http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112\""
							. "	id=\"mediaplayer1\">"
							. "	<param name=\"URL\" value=\"$full\">"
							. "	<param name=\"Filename\" value=\"$full\">"
							. "	<param name=\"AutoStart\" value=\"True\">"
							. "	<param name=\"ShowControls\" value=\"True\">"
							. "	<param name=\"ShowStatusBar\" value=\"True\">"
							. "	<param name=\"ShowDisplay\" value=\"True\">"
							. "	<param name=\"AutoRewind\" value=\"True\"> "
							. "<embed "
							. "	type=\"application/x-mplayer2\""
							. "	pluginspage=\"http://www.microsoft.com/windows/windowsmedia/download/\""
							. "	src=\"$full\""
							. "	width=\"$sx\" height=\"$sy\""
							. "	filename=\"$full\""
							. "	autostart=\"True\" "
							. "	showcontrols=\"True\""
							. "	showstatusbar=\"False\" "
							. "	showdisplay=\"False\""
							. "	autorewind=\"True\"> "
							. "</embed> "
							. "</object> "
							),
				"is_mac" => array(
							$codec_is_windowsmedia && !$codec_is_divx,
							"<embed "
							. "	type=\"application/x-mplayer2\" "
							. "	pluginspage=\"http://www.microsoft.com/windows/windowsmedia/download/\" "
							. "	src=\"$full\" "
							. "	width=\"$sx\" height=\"$sy2\" "
							. "	filename=\"$full\" "
							. "	autostart=\"True\" "
							. "	showcontrols=\"True\" "
							. "	showstatusbar=\"False\" "
							. "	showdisplay=\"False\" "
							. "	autorewind=\"True\">"
							. "</embed> ",
							TRUE,
							"<embed "
							. "	pluginspage=\"http://www.microsoft.com/windows/windowsmedia/download/\" "
							. "	src=\"$full\" "
							. "	width=\"$sx\" height=\"$sy2\" "
							. "	filename=\"$full\" "
							. "	autostart=\"True\" "
							. "	showcontrols=\"True\" "
							. "	showstatusbar=\"False\" "
							. "	showdisplay=\"False\" "
							. "	autorewind=\"True\">"
							. "</embed> "
							),
				// not windows, not mac (includes is_linux, etc.)
				"else" => array(
							TRUE,
							"<embed "
							. "	type=\"application/x-mplayer2\" "
							. "	pluginspage=\"http://www.microsoft.com/windows/windowsmedia/download/\" "
							. "	src=\"$full\" "
							. "	width=\"$sx\" height=\"$sy\" "
							. "	filename=\"$full\" "
							. "	autostart=\"True\" "
							. "	showcontrols=\"True\" "
							. "	showstatusbar=\"False\" "
							. "	showdisplay=\"False\" "
							. "	autorewind=\"True\">"
							. "</embed> "
							),
				// javascript not enabled
				"noscript" => array(
							"<embed "
							. "	src=\"$full\" "
							. "	width=\"$sx\" height=\"$sy\" "
							. "	filename=\"$full\" "
							. "	autostart=\"True\" "
							. "	showcontrols=\"True\" "
							. "	showstatusbar=\"False\" "
							. "	showdisplay=\"False\" "
							. "	autorewind=\"True\">"
							. "</embed> ".
							"<p>",
							"Please enable JavaScript to allow this programm to select the most appropriate video ",
							"viewer for your configuration."
							)
				)
			);
		} // end if avi
		else if ($subtype == "mpeg")
		{
			// ----------------------------------------
			// -------------- MPEG --------------------
			// ----------------------------------------
	
			// Always use <embed>

			?>
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy ?>"
				>
				</embed>
			<?php
		} // end if mpeg
		else if ($subtype == "quicktime")
		{
			// ----------------------------------------
			// -------------- QuickTime ---------------
			// ----------------------------------------
	
			// QuickTime EMBED attributes are described here:
			// http://www.apple.com/quicktime/authoring/embed2.html
			//
			// QuickTime OBJECT tag:
			// http://www.apple.com/quicktime/tools_tips/tutorials/activex.html

			/*
				The following EMBED attributes are supposedly supported but break the QT player
				when used:
					type="video/quicktime"
					qtsrc="url"
					qtsrcdontusebrowser
	
				codebase="http://www.apple.com/qtactivex/qtplugin.cab">
			*/
	
			?>
				<object classid="clsid:02bf25d5-8c17-4b23-bc80-d3488abddc6b"
					codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0"
					width="<?= $sx ?>" height="<?= $sy ?>">
					<param name="src" value="sample.mov">
					<param name="autoplay" value="true">
					<param name="controller" value="true">
					<param name="autohref" value="true">
					<param name="scale" value="aspect">
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy2 ?>"
					controller="true"
					scale="aspect"
					autohref="yes"
					autoplay="yes"
					pluginspage="http://www.apple.com/quicktime/download/"
				>
				</embed>
				</object>
			<?php
		} // end if qt

		// ---------------------------------------------
		// --- Display links & info below the player ---
		// ---------------------------------------------

		$video_link = str_replace('[url]', $full, $html_video_download);

		?>
			<p>
			<font size="-1">
			<?= $codec_detail ?>
			<br>
			<?= $codec_install ?>

			<?= $video_link ?>
			</font>
		<?php

	} // if video

    // debug
    // echo "<br>rig_img_size = '$rig_img_size'<br>\n";
    // echo "preview = '$preview'<br>\n";
}


//******************************
function rig_display_os_detail()
//******************************
// Returns an array similar to codec_detail but that
// contains links that are always to be displayed in the
// context of a given OS (typically linux)
{
	return array("(is_linux)MPlayer&nbsp;Plugin" 	=> "http://mplayerplug-in.sourceforge.net/",
				 "(is_linux)MPlayer" 				=> "http://www.mplayerhq.hu/",
				 "(is_linux)Xine" 					=> "http://xine.sf.net/",
				 "(is_win)Windows&nbsp;Media&nbsp;Player" => "http://www.microsoft.com/windows/windowsmedia/default.aspx"
				 );
}


//**************************************
function rig_display_codec_detail($info)
//**************************************
// Input: an array with 'e' element contains the video codec FourCC
// Output: an array with:
// - the codec name
// - entries in the format "(javascript-condition)Program Name" => "program url"
//   that will result in the text "Install <program name>"
// - entries in the format "program url"
//   that will result in the text "Install the player"
//
// TODO: refactor to get it rid of the annoying duplication of detailed entries.
//
// Return NULL if there's no info detail.
{
	if (is_array($info) && is_string($info['e']))
	{
		$fourcc = $info['e'];

		// detection array:
		// FourCC regexp => "name|warning string"

		$map = array(
			"DIVX"			=> array("DivX ;-)",
									 "rig:is-divx-format",
									 "(!is_mac && !is_linux)DivX Codec" 		=> "http://www.divx.com/",
									 "(is_mac)DivX&nbsp;Codec" 					=> "http://www.divx.com/divx/mac",
									 "(is_linux)DivX&nbsp;Codec" 				=> "http://www.divx.com/divx/linux/"
									),
			"DIV3"			=> array("DivX 3",
									 "rig:is-divx-format",
									 "(!is_mac && !is_linux)DivX&nbsp;Codec" 	=> "http://www.divx.com/",
									 "(is_mac)DivX&nbsp;Codec" 					=> "http://www.divx.com/divx/mac",
									 "(is_linux)DivX&nbsp;Codec" 				=> "http://www.divx.com/divx/linux/"
									),
			"DX50"			=> array("DivX 5",
									 "rig:is-divx-format",
									 "(!is_mac && !is_linux)DivX&nbsp;Codec" 	=> "http://www.divx.com/",
									 "(is_mac)DivX&nbsp;Codec" 					=> "http://www.divx.com/divx/mac",
									 "(is_linux)DivX&nbsp;Codec" 				=> "http://www.divx.com/divx/linux/"
									),
			"XVID"			=> array("XVID",
									 "XviD&nbsp;Codec" 							=> "http://www.xvid.org/"
									),
			
			"MP42"			=> "Microsoft MPEG-4 v2",
			"WMV[1-9]"		=> array("Windows Media Format",
									 "rig:is-windowsmedia-format",
									 "(!is_win)Windows&nbsp;Media&nbsp;Player" 	=> "http://www.microsoft.com/windows/windowsmedia/default.aspx"
									),
			
			"M[LJ]PG"		=> "Motion JPEG",
			"MPG1"			=> "MPEG 1 Stream",
			"MPG2"			=> "MPEG 2 Stream",
			"MPG4"			=> "MPEG 4 Stream",
			"MPG[1-9]"		=> "MPEG Stream",
			
			"RV[1-9][0-9]"	=> array("Real Video",
									 "RealOne" => "http://www.real.com/"
									 ),
			"SVQ[1-9]"		=> array("Quicktime Sorenson",
									 "QuickTime" => "http://www.apple.com/quicktime/"
									 ),
			"MOV."			=> array("Quicktime Movie",
									 "QuickTime" => "http://www.apple.com/quicktime/"
									 ),
			"IV[3-5][0-9]"	=> "Intel Indeo",
			"cvid"			=> "Cinepak",

			// Some other generic mappings
			// More info at: http://www.microsoft.com/whdc/hwdev/archive/devdes/fourcc.mspx
			
			"ANIM" => "Intel - RDX",
			"AUR2" => "AuraVision - Aura 2 Codec - YUV 422",
			"AURA" => "AuraVision - Aura 1 Codec - YUV 411",
			"BT20" => "Brooktree - MediaStream codec",
			"BTCV" => "Brooktree - Composite Video codec",
			"CC12" => "Intel - YUV12 codec",
			"CDVC" => "Canopus - DV codec",
			"CHAM" => "Winnov, Inc. - MM_WINNOV_CAVIARA_CHAMPAGNE",
			"CPLA" => "Weitek - 4:2:0 YUV Planar",
			"CVID" => "Supermac - Cinepak",
			"CWLT" => "reserved",
			"DUCK" => "Duck Corp. - TrueMotion 1.0",
			"DVE2" => "InSoft - DVE-2 Videoconferencing codec",
			"DXT1" => "reserved",
			"DXT2" => "reserved",
			"DXT3" => "reserved",
			"DXT4" => "reserved",
			"DXT5" => "reserved",
			"DXTC" => "DirectX Texture Compression",
			"FLJP" => "D-Vision - Field Encoded Motion JPEG With LSI Bitstream Format",
			"GWLT" => "reserved",
			"H260" => "Intel - Conferencing codec",
			"H261" => "Intel - Conferencing codec",
			"H262" => "Intel - Conferencing codec",
			"H263" => "Intel - Conferencing codec",
			"H264" => "Intel - Conferencing codec",
			"H265" => "Intel - Conferencing codec",
			"H266" => "Intel - Conferencing codec",
			"H267" => "Intel - Conferencing codec",
			"H268" => "Intel - Conferencing codec",
			"H269" => "Intel - Conferencing codec",
			"I263" => "Intel - I263",
			"I420" => "Intel - Indeo 4 codec",
			"IAN." => "Intel - RDX",
			"ICLB" => "InSoft - CellB Videoconferencing codec",
			"ILVC" => "Intel - Layered Video",
			"ILVR" => "ITU-T - H.263+ compression standard",
			"IRAW" => "Intel - YUV uncompressed",
			"IV30" => "Intel - Indeo Video 3 codec",
			"IV31" => "Intel - Indeo Video 3.1 codec",
			"IV32" => "Intel - Indeo Video 3 codec",
			"IV33" => "Intel - Indeo Video 3 codec",
			"IV34" => "Intel - Indeo Video 3 codec",
			"IV35" => "Intel - Indeo Video 3 codec",
			"IV36" => "Intel - Indeo Video 3 codec",
			"IV37" => "Intel - Indeo Video 3 codec",
			"IV38" => "Intel - Indeo Video 3 codec",
			"IV39" => "Intel - Indeo Video 3 codec",
			"IV40" => "Intel - Indeo Video 4 codec",
			"IV41" => "Intel - Indeo Video 4 codec",
			"IV42" => "Intel - Indeo Video 4 codec",
			"IV43" => "Intel - Indeo Video 4 codec",
			"IV44" => "Intel - Indeo Video 4 codec",
			"IV45" => "Intel - Indeo Video 4 codec",
			"IV46" => "Intel - Indeo Video 4 codec",
			"IV47" => "Intel - Indeo Video 4 codec",
			"IV48" => "Intel - Indeo Video 4 codec",
			"IV49" => "Intel - Indeo Video 4 codec",
			"IV50" => "Intel - Indeo 5.0",
			"MP42" => "Microsoft - MPEG-4 Video Codec V2",
			"MPEG" => "Chromatic - MPEG 1 Video I Frame",
			"MRCA" => "FAST Multimedia - Mrcodec",
			"MRLE" => "Microsoft - Run Length Encoding",
			"MSVC" => "Microsoft - Video 1",
			"NTN1" => "Nogatech - Video Compression 1",
			"qpeq" => "Q-Team - QPEG 1.1 Format video codec",
			"RGBT" => "Computer Concepts - 32 bit support",
			"RT21" => "Intel - Indeo 2.1 codec",
			"RVX." => "Intel - RDX",
			"SDCC" => "Sun Communications - Digital Camera Codec",
			"SFMC" => "Crystal Net - SFM Codec",
			"SMSC" => "Radius - proprietary",
			"SMSD" => "Radius - proprietary",
			"SPLC" => "Splash Studios - ACM audio codec",
			"SQZ2" => "Microsoft - VXtreme Video Codec V2",
			"SV10" => "Sorenson - Video R1",
			"TLMS" => "TeraLogic - Motion Intraframe Codec",
			"TLST" => "TeraLogic - Motion Intraframe Codec",
			"TM20" => "Duck Corp. - TrueMotion 2.0",
			"TMIC" => "TeraLogic - Motion Intraframe Codec",
			"TMOT" => "Horizons Technology - TrueMotion Video Compression Algorithm",
			"TR20" => "Duck Corp. - TrueMotion RT 2.0",
			"V422" => "Vitec Multimedia - 24 bit YUV 4:2:2 format (CCIR 601). For this format, 2 consecutive pixels are represented by a 32 bit (4 byte) Y1UY2V color value.",
			"V655" => "Vitec Multimedia - 16 bit YUV 4:2:2 format.",
			"VCR1" => "ATI - VCR 1.0",
			"VIVO" => "Vivo - H.263 Video Codec",
			"VIXL" => "Miro Computer Products AG - for use with the Miro line of capture cards.",
			"VLV1" => "Videologic - VLCAP.DRV",
			"WBVC" => "Winbond Electronics - W9960",
			"XLV0" => "NetXL, Inc. - XL Video Decoder",
			"YC12" => "Intel - YUV12 codec",
			"YUV8" => "Winnov, Inc. - MM_WINNOV_CAVIAR_YUV8",
			"YUV9" => "Intel - YUV9",
			"YUYV" => "Canopus - YUYV compressor",
			"ZPEG" => "Metheus - Video Zipper",
			"CYUV" => "Creative Labs, Inc - Creative Labs YUV",
			"FVF1" => "Iterated Systems, Inc. - Fractal Video Frame",
			"IF09" => "Intel - Intel Intermediate YUV9",
			"JPEG" => "Microsoft - Still Image JPEG DIB",
			"MJPG" => "Microsoft - Motion JPEG DIB Format",
			"PHMO" => "IBM - Photomotion",
			"ULTI" => "IBM - Ultimotion",
			"VDCT" => "Vitec Multimedia - Video Maker Pro DIB",
			"VIDS" => "Vitec Multimedia - YUV 4:2:2 CCIR 601 for V422",
			"YU92" => "Intel - YUV"
		);


		foreach($map as $filter => $detail)
		{
			if (preg_match('/' . $filter . '/', $fourcc) > 0)
			{
				return $detail;
			}
		}

	}
	
	return NULL;
}


//*******************************************
function rig_video_generate_javascript($desc)
//*******************************************
/*
	desc is an array of array, which syntax goes more or less like this:
	"javascript_boolean" => array(php_boolean, "<codec>", "link1", "link2",
								   php_boolean, "<codec>", "link1", "link2",
								   etc.);
*/
{
	echo "<script LANGUAGE=\"JavaScript\">\n";
	echo "<!--\n";

	$need_else = FALSE;
	$no_script_detail = FALSE;

	foreach($desc as $js_test => $detail)
	{
		if ($js_test == "noscript")
		{
			$no_script_detail = $detail;
		}
		else
		{
			while(count($detail))
			{
				$php_test = array_shift($detail);
	
				if (is_bool($php_test) && $php_test === TRUE)
				{
					$codec = array_shift($detail);
						
					if (is_string($codec))
					{
						if ($need_else)
							echo "else ";
						else
							$need_else = TRUE;
	
						if ($js_test != "else")
							echo "if ($js_test)\n";
	
						// Need to escape some characters from the written line
						// f.ex. at least ' is not acceptable in write('something').
						
						$codec = str_replace("'", "\\'", $codec);
	
					// Generate the Javascript line
	
						echo " document.write('$codec');\n";
					}
					
					// get remaining strings as links			
				}
			} // while
		} // if
	}

	echo "//-->\n";
	echo "</script>\n";

	if (is_array($no_script_detail))
	{
		echo "<noscript>\n";
		foreach($no_script_detail as $str)
			echo $str . "\n";
		echo "</noscript>\n";
	}
	
}


//**************************************************
function rig_video_javascript_testline($test, $line)
//**************************************************
{
	// Need to escape some characters from the written line
	// f.ex. at least ' is not acceptable in write('something').
	
	$line = str_replace("'", "\\'", $line);
	
	
	// Generate the Javascript line
	
	return
		  "<script LANGUAGE=\"JavaScript\">\n"
		. "<!--\n"
		. "if ($test)\n"
		. "	document.write('$line');\n"
		. "//-->\n"
		. "</script>\n";
	
}



//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.7  2005/09/25 22:36:15  ralfoide
//	Updated GPL header date.
//
//	Revision 1.6  2004/07/17 07:52:31  ralfoide
//	GPL headers
//	
//	Revision 1.5  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.4  2004/02/27 08:49:42  ralfoide
//	Translation for video strings
//	
//	Revision 1.3  2003/11/29 22:44:23  ralfoide
//	Fixed line endings (some lines in dos mode converted to unix mode)
//	
//	Revision 1.2  2003/11/29 22:35:42  ralfoide
//	Video: JavaScript browser & OS detection, customize install codec links, etc.
//	Tested against Win/IE6, Win/Mozilla 1.4, Linux/Mozilla, Linux/Konqueror, MacOS X/Safari (Panther)
//	
//	Revision 1.1  2003/11/25 05:05:34  ralfoide
//	Version 0.6.4.4 started.
//	Added video install codec/player link & codec info.
//	Isolated video display routines in new source file.
//-------------------------------------------------------------
?>
