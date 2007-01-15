<?php
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Polish Language Strings for RIG



// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'ISO-8859-2';		// cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'pl';			// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


// Current Locale
//---------------

// Lib-C locale, mainly used to generate dates and time with the correct language.
// On Debian, run 'dpkg-reconfigure locales' as root and make sure the locale is installed.
//
// Neither 'en' nor 'en_EN' work for me in English. Using 'C' instead as fallback.
// This is expected to be be ISO-8859 not UTF-8

$lang_locale        = array('pl_PL', 'pl', 'C');


// Languages availables
//---------------------

$html_language		= 'J&#x0119;zyk:';

$html_desc_lang		= array('en' => 'English',						
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'pl' => 'Polski',
							'jp' => '&#26085;&#26412;&#35486;'
							);

// Themes available
//-----------------

$html_theme			= 'Schemat Kolor&#x00F3;w:';
$html_desc_theme	= array('gray'  => 'Szary',
							'blue'  => 'Niebieski',
							'sand'  => 'Piaskowy',
							'khaki' => 'Khaki',
							'egg'	=> 'Jajeczny',
							'none'	=> 'Brak');


// HTML content
//-------------

$html_current_album	= 'Bie&#x017C;&#x0105;cy Album';
$html_albums		= 'Dost&#x0119;pne Albumy';
$html_images		= 'Zdj&#x0119;cia';
$html_options		= 'Opcje';

$html_generated		= 'Wygenerowane w [time] sekund <i>[date]</i> przez <i>[rig-version]</i>';

$html_admin_intrfce	= 'Interfejs Administratora';

$html_rig_admin		= 'Interfejs Administratora RIG';
$html_comment_stats	= 'Statystyki dla albumu i podalbum&#x00F3;w:';
$html_album_stat	= '[bytes] bajt&#x00F3;w zajmowanych przez [files] plik&#x00F3;w w [folders] folderach';

$html_actions		= 'Polecenia';
// RM 20030120 splitting Mk/Del / All Previews / All Images / Both
$html_act_create	= 'Stw&#x00F3;rz wszystkie:';
$html_act_delete	= 'Skasuj wszystkie:';
$html_act_previews	= 'Miniaturki';
$html_act_images	= 'Zdj&#x0119;cia';
$html_act_prev_img	= 'Miniaturki i Zdj&#x0119;cia';
$html_act_rnd_prev	= 'Zmie&#x0144; losow&#x0105; ikon&#x0119; albumu';
$html_act_canon		= 'Zmie&#x0144; nazwy plik&#x00F3;w Canon 100-1234_img.jpg';

$html_use_as_icon	= 'U&#x017C;yj jako Ikon&#x0119; Albumu';
$html_rename_image	= 'Zmie&#x0144; Nazwe Zdj&#x0119;cia';
$html_set_desc		= 'Opisz';
$html_avail_albums	= 'Dostepne Albumy';
$html_comment		= 'By&#x0107; mo&#x017C;e trzeba odwierzy&#x0107; stron&#x0119; by zobaczy&#x0107; prawid&#x0142;owe zdj&#x0119;cia';
$html_back_to		= 'Powr&#x00F3;t do [name]';
$html_back_album	= 'Powr&#x00F3;t do albumu';
$html_back_previous	= 'Powr&#x00F3;t do poprzedniego albumu';
$html_vis_on		= 'Poka&#x017C;';
$html_vis_off		= 'Ukryj';

$html_credits		= 'Tw&#x00F3;rcy';
$html_show_credits	= 'Poka&#x017C; tw&#x00F3;rc&#x00F3;w RIG i PHP';
$html_hide_credits	= 'Ukryj tw&#x00F3;rc&#x00F3;w';
$html_text_credits	= 'R\'alf Image Gallery (<a href="http://rig.powerpulsar.com">RIG</a>) &copy; 2001-2003 R\'alf<br>';
$html_text_credits .= 'Polskie t&#x0142;umaczenie: <a href="mailto:guaranga@wp.pl">Alfred Broda</a><br>';
$html_text_credits .= 'RIG jest rozprowadzany na warunkach <a href="LICENSE.html">licencji RIG</a> (<a href="http://www.opensource.org/licenses/">OSL</a>).<br>';
$html_text_credits .= 'Bazuje na <a href="http://www.php.net">PHP</a> ';
$html_text_credits .= 'oraz na <a href="ftp://ftp.uu.net/graphics/jpeg">JpegLib</a>.<br>';

$html_phpinfo		= 'Informacje o Serwerze PHP';
$html_show_phpinfo	= 'Poka&#x017C; informacje o Serwerze PHP';
$html_hide_phpinfo	= 'Ukryj informacje o Serwerze PHP';

$html_login			= 'Zaloguj';
$html_validate		= 'Zaloguj';
$html_remember		= 'Zapami&#x0119;taj mnie';
$html_username		= 'Login';
$html_password		= 'Has&#x0142;o';
$html_welcome		= 'Witaj <b>[name]</b>! ([change-link])';
$html_welcome_guest = 'Witaj! ([change-link])';
$html_chg_user		= 'Zmie&#x0144; u&#x017C;ytkownika';
$html_guest_login	= '\'Go&#x015B;&#x0107;\'';

// RM 20030119 - v0.6.3
$html_album_copyrt	= 'Wszystkie zdj&#x0119;cia &copy; [year] [name]';	// [name] will become $pref_copyright_name
$html_image_copyrt	= 'Zdj&#x0119;cie &copy; [year] [name]';		    // [name] will become $pref_copyright_name
$html_album_count	= '[count] albumy';
$html_image_count	= '[count] zdj&#x0119;&#x0107;';

// RM 20040222 - v0.6.4.5
$html_video_codec_detail		= "Format video: <i>[codec_name]</i>";
$html_video_install_named_player	= "[&nbsp;<a href=\"[url]\">Instaluj&nbsp;[name]</a>&nbsp;] ";
$html_video_install_unnamed_player	= "[&nbsp;<a href=\"[url]\">Instaluj&nbsp;odtwarzacz</a>&nbsp;]";
$html_video_download				= "[&nbsp;<a title=\"Zapisz plik na dysk i odtw&#x00F3;rz\" href=\"[url]\">Download</a>&nbsp;]";

// RM 20051226 - v0.7.3
$html_viewfullrez_title	= "Zobacz obraz w pe&#x0142;nej rozdzielczo&#x015B;ci (prawy klik, \"Zapisz jako...\" by zapisa&#x0107; na swoim komputerze)";
$html_viewfullrez_link	= "Zobacz obraz w pe&#x0142;nej rozdzielczo&#x015B;ci";

// Script Content
//---------------

// Date formatiing
// Date formating for $html_footer_date, $html_img_date and $html_album_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%m/%d/%Y, %I:%M %p';

// Album Title
$html_album			= 'Album';
$html_admin			= 'Administrator';
$html_none			= 'Pocz&#x0105;tek';

// Current Album
$html_root			= 'Pocz&#x0105;tek';

// Images
$html_image			= 'Zdj&#x0119;cie';
$html_prev			= 'Poprzednie';
$html_next			= 'Nast&#x0119;pne';
$html_image2		= 'zdj&#x0119;cie';
$html_pixels		= 'pixeli';
$html_ok			= 'Zmie&#x0144;';
$html_img_size		= 'Wielko&#x015B;&#x0107; zdj&#x0119;cia';
$html_original		= 'Orygina&#x0142;';

// Tooltips
$html_image_tooltip	= '[type]: [name]';
$html_album_tooltip	= '[type]: [name]; ostatnia aktualizacja: [date]';


// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)


// Image date displayed
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_img_date		= '%A %B %d %Y, %I:%M %p';

// Album date displayed
// cf http://www.php.net/manual/en/function.strftime.php
$html_album_date	= '%B %Y';

// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.2  2006/04/13 05:04:57  ralfoide
//	Version 0.7.4. Polish translation. Fixes.
//
//	Revision 1.1  2006/01/11 08:21:54  ralfoide
//	Added polish translation by Alfred Broda, http://krypa.homelinux.net/
//	
//	Revision 1.1  2005/12/31 01:21:11  Freder
//	Language: strings for tooltip details
//-------------------------------------------------------------
?>
