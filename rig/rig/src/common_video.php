<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************


//-----------------------------------------------------------------------


//*******************************
function rig_display_video($type)
//*******************************
{
	global $dir_album;
	global $abs_album_path;
	global $current_real_album;		// RM 20030907
	global $current_image;
	global $pretty_image;
	global $rig_img_size;
	global $pref_image_size;
	global $pref_image_quality;

	global $_test_;
	if (isset($_test_)) echo "Test type: $_test_<br>";

	if ($rig_img_size != -2 && $rig_img_size < 1)
		$rig_img_size = $pref_image_size;

	// get the file type
	if (strncmp($type, "video/", 6) == 0)
	{
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

		$subtype = substr($type, 6);


		// get some details based on the video codec
		$codec_info = rig_display_codec_detail($info);
		$codec_install = "";

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

			// RM 20031110 TBT To Be Translated
			$codec_detail = "Video format: <i>$codec_name</i>";
			
			if ($codec_url != NULL && is_array($codec_url))
			{
				foreach($codec_url as $name => $url)
				{
					if (is_string($name))
						// RM 20031124 TBT To Be Translated
						$codec_install .= "[&nbsp;<a href=\"$url\">Install $name</a>&nbsp;] ";
					else
						// RM 20031124 TBT To Be Translated
						$codec_install .= "[&nbsp;<a href=\"$url\">Install the player</a>&nbsp;]";
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

		if ($_test_==4)
		{
			// Win32 Moz 1.4: doesn't work
			// Win32 IE6: unsafe ActiveX, won't play
			?>
		
			<object data="<?= $full ?>" type="video/x-msvideo" />
		
			<?php
		}
		else if ($_test_==3)
		{
			// Win32  Moz 1.4: WMV7 displays but doesn't play
			// Win32  IE6: WMV9 shows with bad aspect ratio, plays.
			// MacOSX Safari: yes if WMV 7.1/MacOS X installed before
			
			?>
				<embed
					type="application/x-msvideo"
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy ?>"
				>
				</embed>
			<?php
		}
		else
		{
			// Win32  Moz 1.4: WMV7 displays but doesn't play
			// Win32  IE6: WMV9 shows, plays (using lack of image size, otherwise bad aspect ratio).
			// MacOSX Safari: no pluging, and MS download page invalid

				?>
				<object 
					classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6"
					codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112"
					id="mediaplayer1">
					<!-- width="<?= $sx ?>" height="<?= $sy ?>" -->
					<param name="URL" value="<?= $full ?>">
					<param name="Filename" value="<?= $full ?>">
					<param name="AutoStart" value="True">
					<param name="ShowControls" value="True">
					<param name="ShowStatusBar" value="True">
					<param name="ShowDisplay" value="True">
					<param name="AutoRewind" value="True">
		
				<embed 
					type="application/x-mplayer2"
					pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/MediaPlayer/"
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy ?>"
					filename="<?= $full ?>"
					autostart="True" 
					showcontrols="True"
					showstatusbar="True" 
					showdisplay="True"
					autorewind="True">
				</embed> 
				</object>
	
				<p>
				<font size="-1">
				<?= $codec_detail ?>
				<br>
				<?= $codec_install ?>
				[&nbsp;<a href="<?= $full ?>">External display</a>&nbsp;]
				</font>
			<?php
			}
		}
		else if ($subtype == "mpeg")
		{
			// ----------------------------------------
			// -------------- MPEG --------------------
			// ----------------------------------------
	
			if ($_test_==4)
			{
				?>
			
				<object data="<?= $full ?>" type="video/mpeg" />
			
				<?php
			}
			else
			{
				?>
					<embed
						src="<?= $full ?>"
						width="<?= $sx ?>" height="<?= $sy ?>"
					>
					</embed>

				<p>
				<font size="-1">
				<?= $codec_detail ?>
				<br>
				<?= $codec_install ?>
				[&nbsp;<a href="<?= $full ?>">External display</a>&nbsp;]
				</font>

				<?php
			}
		}
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

			// for QT, add 16 to the height to see the controls (cf doc above)
			$sy2 = $sy+16;

			


	
			/*
				The following EMBED attributes are supposedly supported but break the QT player
				when used:
					type="video/quicktime"
					qtsrc="<?= $full ?>"
					qtsrcdontusebrowser
	
				codebase="http://www.apple.com/qtactivex/qtplugin.cab">
			*/
	
	if ($_test_==4)
	{
		?>
	
		<object data="<?= $full ?>" type="video/quicktime" />
	
		<?php
	}
	else if ($_test_==3)
	{
	
			?>
				<embed
					src="<?= $full ?>"
					width="<?= $sx ?>" height="<?= $sy2 ?>"
					controller="true"
					scale="aspect"
					autohref="yes"
					autoplay="yes"
				>
				</embed>
				
				<br>
				
				<font size="-1">
				[&nbsp;<a href="<?= $full ?>">External display</a>&nbsp;]
				<br>
				Players: 
					<a href="http://www.mplayerhq.hu/homepage/">Linux MPlayer</a>
					|
					<a href="http://www.apple.com/quicktime/download/">Apple Quicktime</a>
					|
					<a href="http://www.microsoft.com/windows/windowsmedia/download/">Windows Media</a>
				</font>
			<?php
	}
	else if ($_test_==2)
	{
//	<script language="JavaScript" type="text/javascript" src="browser_detect.js"></script>

	    ?>
	
	
	<script type="text/javascript" language="JavaScript">

alert("here");
document.write("is_ie4up = " + is_ie4up + " -- is_win32 = " + is_win32 + "<br>");
	
	document.write("BROWSER: ")
	document.write(navigator.appName + "<br>")
	document.write("BROWSERVERSION: ")
	document.write(navigator.appVersion + "<br>")
	document.write("CODE: ")
	document.write(navigator.appCodeName + "<br>")
	document.write("PLATFORM: ")
	document.write(navigator.platform + "<br>")
	
		document.write('<embed')
		document.write('src="<?= $full ?>"')
		document.write('width="<?= $sx ?>" height="<?= $sy2 ?>"')
		document.write('controller="true"')
		document.write('scale="aspect"')
		document.write('autohref="yes"')
		document.write('autoplay="yes"')
		document.write('pluginspage="http://www.apple.com/quicktime/download/">')
		document.write('</embed>')
	
	
	<noscript>
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
	</noscript>
	</script>
	
	    <?php
	}
	else
	{
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
				
				<p>
				<font size="-1">
				<?= $codec_detail ?>
				<br>
				<?= $codec_install ?>
				[&nbsp;<a href="http://www.apple.com/quicktime/download/">Apple Quicktime</a>&nbsp;]
				[&nbsp;<a href="<?= $full ?>">External display</a>&nbsp;]
				</font>
				<?php
		}
	} // non-javascript test
	} // if video

    // debug
    // echo "<br>rig_img_size = '$rig_img_size'<br>\n";
    // echo "preview = '$preview'<br>\n";
}


//**************************************
function rig_display_codec_detail($info)
//**************************************
// Input: an array with 'e' element contains the video codec FourCC
// Output: an array with:
//	'n' => name, f.ex. "Divx 3.0"
//	'u' => download/install URL
// Return NULL if there's no info detail.
{
	if (is_array($info) && is_string($info['e']))
	{
		$fourcc = $info['e'];

		// detection array:
		// FourCC regexp => "name|warning string"

		$map = array(
			"DIVX"			=> array("DivX ;-)",
									 "DivX Codec" => "http://www.divx.com/",
									 "Windows Media Player" => "http://www.microsoft.com/windows/windowsmedia/default.aspx"
									),
			"DIV3"			=> array("DivX 3", "http://www.divx.com/"),
			"DX50"			=> array("DivX 5", "http://www.divx.com/"),
			"XVID"			=> array("XVID", "http://www.xvid.org/"),
			
			"MP42"			=> "Microsoft MPEG-4 v2",
			"WMV[1-9]"		=> array("Windows Media Format", "http://www.microsoft.com/windows/windowsmedia/default.aspx"),
			
			"M[LJ]PG"		=> "Motion JPEG",
			"MPG1"			=> "MPEG 1 Stream",
			"MPG2"			=> "MPEG 2 Stream",
			"MPG4"			=> "MPEG 4 Stream",
			"MPG[1-9]"		=> "MPEG Stream",
			
			"RV[1-9][0-9]"	=> array("Real Video", "http://www.real.com/"),
			"SVQ[1-9]"		=> array("Quicktime Sorenson", "http://www.apple.com/quicktime/"),
			"MOV."			=> array("Quicktime Movie", "http://www.apple.com/quicktime/"),
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




//-----------------------------------------------------------------------
// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2003/11/25 05:05:34  ralfoide
//	Version 0.6.4.4 started.
//	Added video install codec/player link & codec info.
//	Isolated video display routines in new source file.
//
//-------------------------------------------------------------
?>
