<?php
// vim: set tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Spanish strings for RIG

// Cambios:
// 20021101 - Fixes by Pedro del Gallego and Roberto Francisco (sf.net)

// Vocabulario:
// byte: octeto ?
// file: fichero
// folder: carpeta /  carpetas electr&oacute;nicas ?
// click on image: pulse sobre la imagen / haga click en la imagen ?
// PHP web server: servidor PHP



// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'ISO-8859-1';		// cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'es';				// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


// Current Locale
//---------------

// Lib-C locale, mainly used to generate dates and time with the correct language.
// On Debian, run 'dpkg-reconfigure locales' as root and make sure the locale is installed.
// Locales are expected to be ISO-8859 not UTF-8
// Using the C locale as a fallback.

$lang_locale        = array('es_ES', 'es', 'C');


// Languages available
//--------------------

$html_language		= 'Idioma:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '&#26085;&#26412;&#35486;'
							);

// Themes available
//-----------------

$html_theme			= 'Color de p&aacute;gina:';
$html_desc_theme	= array('gray'  => 'Gris',
							'blue'  => 'Azul',
							'sand'  => 'Arena',
							'khaki' => 'Caqui',
							'egg'	=> 'Amarilla',
							'none'	=> 'Ninguna');


// HTML content
//-------------

$html_current_album	= 'Album Actual';
$html_albums		= 'Albums Disponibles';
$html_images		= 'Im&aacute;genes';																			// rf
$html_options		= 'Opciones';																					// pdg, rf

$html_generated		= 'Creado en [time] secondos el dia <i>[date]</i> por <i>[rig-version]</i>';					// pdg

$html_admin_intrfce	= 'Interfaz de administraci&oacute;n';

$html_rig_admin		= 'Interfaz de administraci&oacute;n de RIG'; 
$html_comment_stats	= 'Estad&iacute;sticas par este &aacute;lbum y sub-&aacute;lbumes :';							// rf
$html_album_stat	= '[bytes] bytes ocupados por [files] ficheros en [folders] carpetas';							// rf

$html_actions		= 'Acciones';																					// pdg, rf
// RM 20030120 splitting Mk/Del / All Previews / All Images / Both
$html_act_create	= 'Crear todas las :';
$html_act_delete	= 'Borrar todas las :';
$html_act_previews	= 'Diapos&iacute;tivas';
$html_act_images	= 'Im&aacute;genes';
$html_act_prev_img	= 'Diapos&iacute;tivas y im&aacute;genes';
$html_act_rnd_prev	= 'Cambiar al azar el icono del &aacute;lbum';
$html_act_canon		= 'Renombrar los ficheros 100-1234_img.jpg de Canon';											// pdg, rf

$html_use_as_icon	= 'Usar como icono del &aacute;lbum';
$html_rename_image	= 'Renombrar la imagen';																		// rf
$html_avail_albums	= 'Albumes disponibles';																		// rf
$html_avail_prevws	= 'Diapos&iacute;tivas disponibles';
$html_comment		= 'Puede necesitarse recargar esta p&aacute;gina para ver las verdaderas im&aacute;genes.';
$html_back_to		= 'Volver al [name]';																			// rf
$html_back_album	= 'Volver al &aacute;lbum';
$html_back_previous	= 'Volver al &aacute;lbum anterior';
$html_vis_on		= 'Mostrar';
$html_vis_off		= 'Ocultar';

$html_credits		= 'Cr&eacute;ditos';
$html_show_credits	= 'Mostrar los cr&eacute;ditos de RIG y PHP';
$html_hide_credits	= 'Ocultar los cr&eacute;ditos';
$html_text_credits	= 'R\'alf Image Gallery ([rig-name-url]) &copy; 2001-2003 por R\'alf<br>';
$html_text_credits .= 'RIG se distribuye bajo los t&eacute;rminos de la <a href="LICENSE.html">licencia RIG</a> (<a href="http://www.opensource.org/licenses/">OSL</a>).<br>';
$html_text_credits .= 'Basado en <a href="http://www.php.net">PHP</a> y ';
$html_text_credits .= '<a href="ftp://ftp.uu.net/graphics/jpeg">JpegLib</a>.<br>';

$html_phpinfo		= 'Informationes del servidor PHP';
$html_show_phpinfo	= 'Mostrar las informaciones del servidor PHP';
$html_hide_phpinfo	= 'Ocultar las informaciones del servidor PHP';

$html_login			= 'Login';
$html_validate		= 'Validar';
$html_remember		= 'Memorizar';
$html_username		= 'Usuario';
$html_password		= 'Contrase&ntilde;a';
$html_welcome		= 'Bienvenido <b>[name]</b> ! ([change-link])';
$html_welcome_guest	= 'Bienvenido! ([change-link])';	// RM 20040222 0.6.4.5
$html_chg_user		= 'Cambiar de usuario';
$html_guest_login	= 'Modo \'Invitado\'';

// RM 20030119 - v0.6.3
$html_album_copyrt	= 'Im&aacute;genes &copy; [year] [name]';
$html_image_copyrt	= 'Imagen &copy; [year] [name]';
$html_album_count	= '[count] albums';
$html_image_count	= '[count] im&aacute;genes';

// RM 20040222 - v0.6.4.5
$html_video_codec_detail			= "Códec de la vídeo: <i>[codec_name]</i>";
$html_video_install_named_player	= "[&nbsp;<a href=\"[url]\">Instale&nbsp;[name]</a>&nbsp;] ";
$html_video_install_unnamed_player	= "[&nbsp;<a href=\"[url]\">Instale&nbsp;el&nbsp;reproductor</a>&nbsp;]";
$html_video_download				= "[&nbsp;<a title=\"Descargar la vídeo y reproducirla con su computadora\" href=\"[url]\">Descargar</a>&nbsp;]";

// Script Content
//---------------

// Date formatiing
// Date formating for $html_footer_date, $html_img_date and $html_album_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%d/%m/%Y %H:%M:%S';

// Album Title
$html_album			= 'Album';
$html_admin			= 'Admin';
$html_none			= 'Primero';

// Current Album
$html_root			= 'Primero';

// Images
$html_image			= 'Imagen';
$html_prev			= 'Anterior';
$html_next			= 'Siguiente';
$html_image2		= 'imagen';
$html_pixels		= 'pixels';
$html_ok			= 'Cambiar';
$html_img_size		= 'Tama&ntilde;o de la imagen';
$html_original		= 'Original';

// Tooltips
$html_image_tooltip	= '[type]: [name]';
$html_album_tooltip	= '[type]: [name]; Last Updated: [date]';

// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)


// Image date displayed
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_img_date		= '%A %d %B %Y %H:%M:%S';

// Album date displayed
// cf http://www.php.net/manual/en/function.strftime.php
$html_album_date	= '%B %Y';


// Overriding prefs.php
//---------------------

// Format to display date in the album titles

$pref_date_YM		= 'M-Y';            // Short format. Must contain M & Y.
$pref_date_YMD      = 'D-M-Y';          // Long format.  Must contain D & M & Y.


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.13  2004/02/27 08:49:42  ralfoide
//	Translation for video strings
//
//	Revision 1.12  2003/11/09 20:52:13  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//	
//	Revision 1.11  2003/07/23 01:19:13  ralfoide
//	Language: strings for tooltip details
//	
//	Revision 1.10  2003/07/21 04:54:45  ralfoide
//	Added date format for album display; changed dates format to strftime (localizable); setting locale
//	
//	Revision 1.9  2003/06/15 19:09:49  ralfoide
//	Version 0.6.3.3: Japanese translation completed
//	
//	Revision 1.8  2003/05/26 17:51:08  ralfoide
//	Lang: Update jp/fr/es strings to match en, removed unused strings. Using latest jp translation file.
//	
//	Revision 1.7  2003/02/21 09:03:03  ralfoide
//	Added gray theme color
//	
//	Revision 1.6  2003/02/16 20:22:58  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.5  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.4  2002/11/02 04:09:09  ralfoide
//	Fixes by Pedro del Gallego and Roberto Francisco (sf.net)
//	
//	Revision 1.3  2002/10/23 16:01:00  ralfoide
//	Added <html lang>; now transmitting charset via http headers.
//	
//	Revision 1.2  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//	
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//	
//-------------------------------------------------------------
?>
