<?php
// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2001 Ralf
//**********************************************
// $Id$
//**********************************************

// Affichage en Francais pour RIG


// HTML encoding
//--------------

// Encoding for HTML web pages. Cannot be empty.

$html_encoding		= 'ISO-8859-1';


// Languages available
//--------------------

$html_language		= 'Langue:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '&#26085;&#26412;&#35486;');

// Themes available
//-----------------

$html_theme			= 'Couleur de page:';
$html_desc_theme	= array('blue'  => 'Bleue',
							'sand'  => 'Sable',
							'khaki' => 'Khaki',
							'egg'	=> 'Oeuf',
							'none'	=> 'Aucune');


// Contenu HTML
//-------------

$html_current_album	= "Album Courant";
$html_albums		= "Albums Disponibles";
$html_images		= "Images";
$html_options		= "Options";

$html_generated		= "G&eacute;n&eacute;r&eacute; en";
$html_seconds		= "secondes";
$html_the			= "le";
$html_by			= "par";

$html_admin_intrfce	= "Maintenance";		// Interface d'administration

$html_rig_admin		= "Maintenance de RIG";	// Interface d'administration 
$html_comment_stats	= "Statistiques pour cet album et sous-albums :";
$html_album_stat	= "%d octets occup&eacute;s par %d fichiers dans %d dossiers";
$html_actions		= "Actions";
$html_mk_previews	= "Cr&eacute;er toutes les imagettes";
$html_rm_previews	= "Effacer toutes les imagettes";
$html_rand_previews	= "Changer al&eacute;atoirement l'imagette de l'album";
$html_rename_canon	= "Renomer les fichiers Canon 100-1234_img.jpg";
$html_use_as_icon	= "Utiliser comme imagette pour l'album";
$html_rename_image	= "Renommer l'image";
$html_set_desc		= "Changer la description";
$html_hide_album	= "Cacher l'album";
$html_show_album	= "Afficher l'album";
$html_avail_albums	= "Albums Disponibles";
$html_avail_prevws	= "Imagettes Disponibles";
$html_comment1		= "Vous devrez probablement recharger cette page afin de voir les imagettes r&eacute;ellement pr&eacute;sentes.";
$html_comment2		= "Cliquez sur les imagettes pour afficher leurs options sp&eacute;cifiques.";
$html_back_to		= "Retour &agrave;";
$html_back_album	= "Retour &agrave; l'album";
$html_back_previous	= "Retour &agrave; l'album pr&eacute;c&egrave;dent";
$html_hidden		= "Cach&eacute;";
$html_vis_on		= "Afficher";
$html_vis_off		= "Masquer";

$html_credits		= "Cr&eacute;dits";
$html_show_credits	= "Afficher les cr&eacute;dits de RIG et PHP";
$html_hide_credits	= "Masquer les cr&eacute;dits";
$html_text_credits	= "<a href=\"http://rig.powerpulsar.com\">RIG</a> &copy; 2001 par R'alf<br>";
$html_text_credits .= "RIG est diffus&eacute; sous les conditions de la <a href=\"LICENSE.html\">license RIG</a>.<br>";
$html_text_credits .= "Bas&eacute; sur <a href=\"http://www.php.net\">PHP</a> et ";
$html_text_credits .= "la <a href=\"ftp://ftp.uu.net/graphics/jpeg\">JpegLib</a>.<br>";

$html_phpinfo		= "Informations sur le serveur PHP";
$html_show_phpinfo	= "Afficher les informations sur le serveur PHP";
$html_hide_phpinfo	= "Masquer les informations sur le serveur PHP";

$html_login			= "Login"; // "Entr&eacute;e";
$html_validate		= "Valider";
$html_remember		= "M&eacute;moriser";
$html_username		= "Utilisateur";
$html_password		= "Mot de passe";
$html_welcome		= "Bienvenue <b>%s</b> ! (%s)";
$html_chg_user		= "changer d'utilisateur";
$html_guest_login	= "Mode 'invit&eacute;'";

// Contenu des Scripts
//--------------------

// Format de la date (dans le bas de page)
// Le format de la date pour $html_date et $html_img_date utilise
// la notation de PHP pour date(), cf http://www.php.net/manual/en/function.date.php
$html_date			= "d/m/Y H:m:s";

// Titres pour les albums
$html_album			= "Album";
$html_admin			= "Admin";
$html_none			= "D&eacute;but";

// Nom du la racine des albums
$html_root			= "D&eacute;but";

// Images
$html_image			= "Image";
$html_prev			= "Pr&eacute;c&egrave;dente";
$html_next			= "Suivante";
$html_image2		= "image";
$html_pixels		= "pixels";
$html_ok			= "Changer";
$html_img_size		= "Taille d'image";
$html_original		= "Originale";

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
//	Revision 1.3  2002/10/21 01:52:48  ralfoide
//	Multiple language and theme support
//
//	Revision 1.2  2002/10/16 04:48:37  ralfoide
//	Version 0.6.2.1
//	
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//	
//	Revision 1.4  2001/11/28 11:52:48  ralf
//	v0.6.1: display image last modification date
//	
//	Revision 1.3  2001/11/26 07:27:59  ralf
//	links from credits to license
//	
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//	
//-------------------------------------------------------------
?>
