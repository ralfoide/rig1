<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Affichage en Espanol pour RIG


// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'ISO-8859-1';		// cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'es';				// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


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

$html_theme			= 'Color de pagina:';
$html_desc_theme	= array('blue'  => 'Azul',
							'sand'  => 'Arena',
							'khaki' => 'Caqui',
							'egg'	=> 'Amarillo',
							'none'	=> 'Ninguna');


// HTML content
//-------------

$html_current_album	= 'Album Actual';
$html_albums		= 'Albums Dispon&igrave;bles';
$html_images		= 'Images';
$html_options		= 'Optiones';

$html_generated		= 'Creado en [time] secondos el <i>[date]</i> por <i>[rig-version]</i>';

$html_admin_intrfce	= 'Interfaz de administraci&oacute;n';

$html_rig_admin		= 'Interfaz de administraci&oacute;n de RIG'; 
$html_comment_stats	= 'Estad&iacute;sticas par este &aacute;lbum y sub-&aacute;lbums :';
$html_album_stat	= '[bytes] octets occupados por [files] ficheros en [folders] carpetas electr&oacute;nicas';
$html_actions		= 'Actiones';
$html_mk_previews	= 'Crear todas las diapos&iacute;tivas';
$html_rm_previews	= 'Borrar todas las diapos&iacute;tivas';
$html_rand_previews	= 'Cambiar al azar el icono del &aacute;lbum';
$html_rename_canon	= 'Retitule los ficheros electr&oacute;nicos 100-1234_img.jpg de Canon';
$html_use_as_icon	= 'Usar como icono del &aacute;lbum';
$html_rename_image	= 'Retitule la imagen';
$html_set_desc		= 'Cambiar la descripci&oacute;n';
$html_hide_album	= 'Ocultar el &aacute;lbum';
$html_show_album	= 'Mostrar el &aacute;lbum ';
$html_avail_albums	= 'Albums disponibles';
$html_avail_prevws	= 'Diapos&iacute;tivas disponibles';
$html_comment1		= 'Puede necesitarse recargar esta p&aacute;gina para ver las verdaderas im&aacute;genes.';
$html_comment2		= 'Chasque encendido las im&aacute;genes para tener acceso a opciones imagen-espec&iacute;ficas.';
$html_back_to		= 'Para volver al';
$html_back_album	= 'Para volver al &aacute;lbum';
$html_back_previous	= 'Para volver al &aacute;lbum anterior';
$html_hidden		= 'ocultado';
$html_vis_on		= 'Mostrar';
$html_vis_off		= 'Ocultar';

$html_credits		= 'Cr&eacute;ditos';
$html_show_credits	= 'Mostrar los cr&eacute;ditos de RIG y PHP';
$html_hide_credits	= 'Ocultar los cr&eacute;ditos';
$html_text_credits	= '<a href=\"http://rig.powerpulsar.com\">RIG</a> &copy; 2001 por R\'alf<br>';
$html_text_credits .= 'RIG se distribuye bajo los t&eacute;rminos de la <a href=\"LICENSE.html\">licencia RIG</a>.<br>';
$html_text_credits .= 'Basado en <a href=\"http://www.php.net\">PHP</a> y ';
$html_text_credits .= '<a href=\"ftp://ftp.uu.net/graphics/jpeg\">JpegLib</a>.<br>';

$html_phpinfo		= 'Informationes del server PHP';
$html_show_phpinfo	= 'Mostrar las informaciones del server PHP';
$html_hide_phpinfo	= 'Ocultar las informaciones del server PHP';

$html_login			= 'Login';
$html_validate		= 'Validar';
$html_remember		= 'Memorizar';
$html_username		= 'Usuario';
$html_password		= 'Contrase&ntilde;a';
$html_welcome		= 'Bienvenido <b>[name]</b> ! ([change-link])';
$html_chg_user		= 'Cambiar de usuario';
$html_guest_login	= 'Modo \'Invitado\'';

// Script Content
//---------------

// Date formatiing
// Date formating for $html_date and $html_img_date uses
// the PHP's date() notation, cf http://www.php.net/manual/en/function.date.php
$html_date			= 'd/m/Y H:m:s';

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

// Number formating
$html_num_dec_sep	= '.';		// separator for decimals (ex 25.00 in English)
$html_num_th_sep	= ',';		// separator for thousand (ex 1,000 in English)


// Image date displayed
$html_img_date		= 'd/m/Y H:m:s';


// Overriding prefs.php
//---------------------

// Format to display date in the album titles

$pref_date_YM		= 'M-Y';            // Short format. Must contain M & Y.
$pref_date_YMD      = 'D-M-Y';          // Long format.  Must contain D & M & Y.


// end

//-------------------------------------------------------------
//	$Log$
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
