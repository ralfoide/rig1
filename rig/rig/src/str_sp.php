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

$html_encoding		= 'ISO-8859-1';


// Languages available
//--------------------

$html_language		= 'Idioma:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '&#26085;&#26412;&#35486;');

// Themes available
//-----------------

$html_theme			= 'Color de pagina:';
$html_desc_theme	= array('blue'  => 'Azul',
							'sand'  => 'Arena',
							'khaki' => 'Caqui',
							'egg'	=> 'Amarillo',
							'none'	=> 'Ninguna');


// Contenu HTML
//-------------

$html_current_album	= "Album Actual";
$html_albums		= "Albums Dispon&igrave;bles";
$html_images		= "Images";
$html_options		= "Optiones";

$html_generated		= "Creado en";
$html_seconds		= "secondos";
$html_the			= "el";
$html_by			= "par";

$html_admin_intrfce	= "Interfaz de administraci&oacute;n";

$html_rig_admin		= "Interfaz de administraci&oacute;n de RIG"; 
$html_comment_stats	= "Estad&iacute;sticas par este &aacute;lbum y sub-&aacute;lbums :";
$html_album_stat	= "%d octets occupados por %d ficheros en %d carpetas electr&oacute;nicas";
$html_actions		= "Actiones";
$html_mk_previews	= "Crear todas las diapos&iacute;tivas";
$html_rm_previews	= "Borrar todas las diapos&iacute;tivas";
$html_rand_previews	= "Cambiar el icono al azar del &aacute;lbum";
$html_rename_canon	= "Retitule los ficheros electr&oacute;nicos 100-1234_img.jpg de Canon";
$html_use_as_icon	= "Usar como icono del &aacute;lbum";
$html_rename_image	= "Retitule la imagen";
$html_set_desc		= "Cambiar la descripci&oacute;n";
$html_hide_album	= "Ocultar el &aacute;lbum";
$html_show_album	= "Mostrar el &aacute;lbum ";
$html_avail_albums	= "Albums disponibles";
$html_avail_prevws	= "Imagenitas disponibles";
$html_comment1		= "Puede necesitarse recargar esta p&aacute;gina para ver las verdaderas im&aacute;genes.";
$html_comment2		= "Chasque encendido las im&aacute;genes para tener acceso a opciones imagen-espec&iacute;ficas.";
$html_back_to		= "Para volver al";
$html_back_album	= "Para volver al &aacute;lbum";
$html_back_previous	= "Para volver al &aacute;lbum anterior";
$html_hidden		= "ocultado";
$html_vis_on		= "Mostrar";
$html_vis_off		= "Ocultar";

$html_credits		= "Cr&eacute;ditos";
$html_show_credits	= "Mostrar los cr&eacute;ditos de RIG y PHP";
$html_hide_credits	= "Ocultar los cr&eacute;ditos";
$html_text_credits	= "<a href=\"http://rig.powerpulsar.com\">RIG</a> &copy; 2001 por R'alf<br>";
$html_text_credits .= "RIG se distribuye bajo los t&eacute;rminos de la <a href=\"LICENSE.html\">licencia RIG</a>.<br>";
$html_text_credits .= "Basado en <a href=\"http://www.php.net\">PHP</a> y ";
$html_text_credits .= "<a href=\"ftp://ftp.uu.net/graphics/jpeg\">JpegLib</a>.<br>";

$html_phpinfo		= "Informationes del server PHP";
$html_show_phpinfo	= "Mostrar las informaciones del server PHP";
$html_hide_phpinfo	= "Ocultar las informaciones del server PHP";

$html_login			= "Login"; // "Entr&eacute;e";
$html_validate		= "Validar";
$html_remember		= "Memorizar";
$html_username		= "Usuario";
$html_password		= "Contrase&ntilde;a";
$html_welcome		= "Bienvenido <b>%s</b> ! (%s)";
$html_chg_user		= "Cambiar de usuario";
$html_guest_login	= "Modo 'Invitado'";

// Contenu des Scripts
//--------------------

// Format de la date (dans le bas de page)
// Le format de la date pour $html_date et $html_img_date utilise
// la notation de PHP pour date(), cf http://www.php.net/manual/en/function.date.php
$html_date			= "d/m/Y H:m:s";

// Titres pour les albums
$html_album			= "Album";
$html_admin			= "Admin";
$html_none			= "Primero";

// Nom du la racine des albums
$html_root			= "Primero";

// Images
$html_image			= "Imagen";
$html_prev			= "Anterior";
$html_next			= "Siguiente";
$html_image2		= "imagen";
$html_pixels		= "pixels";
$html_ok			= "Cambiar";
$html_img_size		= "Tama&ntilde;o de la imagen";
$html_original		= "Original";

// Affichage de la date d'image date
$html_img_date		= "d/m/Y H:m:s";


// Modifications de prefs.php
//---------------------------

// affichage de la date au debut des noms d'albums

$pref_date_YM		= "M-Y";            // format court. Doit contenir M & Y.
$pref_date_YMD      = "D-M-Y";          // format long.  Doit contenir D & M & Y.


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.1  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//
//	Revision 1.1  2002/10/14 07:05:17  ralf
//	Update 0.6.3 build 1
//	
//	
//-------------------------------------------------------------
?>
