<?php
// vim: set tabstop=4 shiftwidth=4: //
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

$html_encoding		= 'ISO-8859-1';		// cf http://www.w3.org/TR/REC-html40/charset.html#h-5.2.2
$html_language_code	= 'fr';				// cf http://www.w3.org/TR/REC-html40/struct/dirlang.html#h-8.1.1


// Locale courrante
//-----------------

// Lib-C locale, utilisee principalement pour l'affichage de la date et l'heure.
// Sous Debian, utiliser 'dpkg-reconfigure locales' en tant que root pour verifier
// que les locales necessaires sont installees.
// Les locales doivent etre de type ISO-8859 et non pas UTF-8
// Utiliser la locale C en tant que defaut.

$lang_locale        = array('fr_FR', 'fr', 'C');


// Languages available
//--------------------

$html_language		= 'Langue:';
$html_desc_lang		= array('en' => 'English',
							'fr' => 'Fran&ccedil;ais',
							'sp' => 'Espa&ntilde;ol',
							'jp' => '&#26085;&#26412;&#35486;'
							);

// Themes available
//-----------------

$html_theme			= 'Couleur de page:';
$html_desc_theme	= array('gray'  => 'Grise',
							'blue'  => 'Bleue',
							'sand'  => 'Sable',
							'khaki' => 'Khaki',
							'egg'	=> 'Oeuf',
							'none'	=> 'Aucune');


// Contenu HTML
//-------------

$html_current_album	= 'Album Courant';
$html_albums		= 'Albums Disponibles';
$html_images		= 'Images';
$html_options		= 'Options';

$html_generated		= 'G&eacute;n&eacute;r&eacute; en [time] secondes le <i>[date]</i> par <i>[rig-version]</i>';

$html_admin_intrfce	= 'Maintenance';		// Interface d'administration

$html_rig_admin		= 'Maintenance de RIG';	// Interface d'administration 
$html_comment_stats	= 'Statistiques pour cet album et sous-albums :';
$html_album_stat	= '[bytes] octets occup&eacute;s par [files] fichiers dans [folders] dossiers';

$html_actions		= 'Actions';
// RM 20030120 splitting Mk/Del / All Previews / All Images / Both
$html_act_create	= 'Cr&eacute;er toutes les :';
$html_act_delete	= 'Effacer toutes les :';
$html_act_previews	= 'Imagettes';
$html_act_images	= 'Images';
$html_act_prev_img	= 'Imagettes et images';
$html_act_rnd_prev	= 'Changer al&eacute;atoirement l\'imagette de l\'album';
$html_act_canon		= 'Renomer les fichiers Canon 100-1234_img.jpg';

$html_use_as_icon	= 'Utiliser comme imagette pour l\'album';
$html_rename_image	= 'Renommer l\'image';
$html_avail_albums	= 'Albums Disponibles';
$html_avail_prevws	= 'Imagettes Disponibles';
$html_comment		= 'Vous devrez probablement recharger cette page afin de voir les imagettes r&eacute;ellement pr&eacute;sentes.';
$html_back_to		= 'Retour &agrave; [name]';
$html_back_album	= 'Retour &agrave; l\'album';
$html_back_previous	= 'Retour &agrave; l\'album pr&eacute;c&egrave;dent';
$html_vis_on		= 'Afficher';
$html_vis_off		= 'Masquer';

$html_credits		= 'Cr&eacute;dits';
$html_show_credits	= 'Afficher les cr&eacute;dits de RIG et PHP';
$html_hide_credits	= 'Masquer les cr&eacute;dits';
$html_text_credits	= 'R\'alf Image Gallery ([rig-name-url]) &copy; 2001-2003 par R\'alf<br>';
$html_text_credits .= 'RIG est diffus&eacute; sous les conditions de la <a href="LICENSE.html">license RIG</a> (<a href="http://www.opensource.org/licenses/">OSL</a>).<br>';
$html_text_credits .= 'Bas&eacute; sur <a href="http://www.php.net">PHP</a> et ';
$html_text_credits .= 'la <a href="ftp://ftp.uu.net/graphics/jpeg">JpegLib</a>.<br>';

$html_phpinfo		= 'Informations sur le serveur PHP';
$html_show_phpinfo	= 'Afficher les informations sur le serveur PHP';
$html_hide_phpinfo	= 'Masquer les informations sur le serveur PHP';

$html_login			= 'Login'; // 'Entr&eacute;e';
$html_validate		= 'Valider';
$html_remember		= 'M&eacute;moriser';
$html_username		= 'Utilisateur';
$html_password		= 'Mot de passe';
$html_welcome		= 'Bienvenue <b>[name]</b> ! ([change-link])';
$html_welcome_guest	= 'Bienvenue! ([change-link])';	// RM 20040222 0.6.4.5
$html_chg_user		= 'changer d\'utilisateur';
$html_guest_login	= 'Mode \'invit&eacute;\'';

// RM 20030119 - v0.6.3
$html_album_copyrt	= 'Images &copy; [year] [name]';
$html_image_copyrt	= 'Image &copy; [year] [name]';
$html_album_count	= '[count] albums';
$html_image_count	= '[count] images';

// RM 20040222 - v0.6.4.5
$html_video_codec_detail			= "Format du film: <i>[codec_name]</i>";
$html_video_install_named_player	= "[&nbsp;<a href=\"[url]\">Installer &nbsp;[name]</a>&nbsp;] ";
$html_video_install_unnamed_player	= "[&nbsp;<a href=\"[url]\">Installer &nbsp;le&nbsp;lecteur</a>&nbsp;]";
$html_video_download				= "[&nbsp;<a title=\"Télécharger la vidéo et la jouer sur votre ordinateur\" href=\"[url]\">Télécharger</a>&nbsp;]";


// Contenu des Scripts
//--------------------

// Format de la date (dans le bas de page)
// Le format de la date pour $html_footer_date, $html_img_date et $html_album_date utilise
// la notation de PHP pour date(), cf http://www.php.net/manual/en/function.date.php
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_footer_date	= '%d/%m/%Y %H:%M:%S';

// Titres pour les albums
$html_album			= 'Album';
$html_admin			= 'Admin';
$html_none			= 'D&eacute;but';

// Nom du la racine des albums
$html_root			= 'D&eacute;but';

// Images
$html_image			= 'Image';
$html_prev			= 'Pr&eacute;c&egrave;dente';
$html_next			= 'Suivante';
$html_image2		= 'image';
$html_pixels		= 'pixels';
$html_ok			= 'Changer';
$html_img_size		= 'Taille d\'image';
$html_original		= 'Originale';

// Tooltips
$html_image_tooltip	= '[type]: [name]';
$html_album_tooltip	= '[type]: [name]; Mise &agrave; jour: [date]';

// Format des nombres
$html_num_dec_sep	= ',';		// separateur des decimales (ex 25,00)
$html_num_th_sep	= ' ';		// separateur des milliers  (ex 1 000)

// Affichage de la date d'image
// Now using notation from http://www.php.net/manual/en/function.strftime.php
$html_img_date		= '%A %d %B %Y %H:%M:%S';

// Affichage de la date d'album
// cf http://www.php.net/manual/en/function.strftime.php
$html_album_date	= '%B %Y';


// Modifications de prefs.php
//---------------------------

// affichage de la date au debut des noms d'albums

$pref_date_YM		= 'M-Y';            // format court. Doit contenir M & Y.
$pref_date_YMD      = 'D-M-Y';          // format long.  Doit contenir D & M & Y.


// end

//-------------------------------------------------------------
//	$Log$
//	Revision 1.16  2004/02/27 08:49:42  ralfoide
//	Translation for video strings
//
//	Revision 1.15  2003/11/09 20:52:12  ralfoide
//	Fix: image resize popup broken (img_size value not memorized?)
//	Feature: Comments (edit page, organizing workflow)
//	Fix: Album check code fails if no options.txt -- reading options.txt must not fail if absent.
//	Fix: Changed credit line
//	Feature: Split album pages in several pages with H*V max grid size (or V max if vertical)
//	Source: rewrote follow-album-symlinks to read synlinked album yet stay in current album
//	
//	Revision 1.14  2003/07/23 01:19:13  ralfoide
//	Language: strings for tooltip details
//	
//	Revision 1.13  2003/07/21 04:54:45  ralfoide
//	Added date format for album display; changed dates format to strftime (localizable); setting locale
//	
//	Revision 1.12  2003/06/15 19:09:49  ralfoide
//	Version 0.6.3.3: Japanese translation completed
//	
//	Revision 1.11  2003/05/26 17:51:08  ralfoide
//	Lang: Update jp/fr/es strings to match en, removed unused strings. Using latest jp translation file.
//	
//	Revision 1.10  2003/02/21 09:03:03  ralfoide
//	Added gray theme color
//	
//	Revision 1.9  2003/02/16 20:22:57  ralfoide
//	New in 0.6.3:
//	- Display copyright in image page, display number of images/albums in tables
//	- Hidden fix_option in admin page to convert option.txt from 0.6.2 to 0.6.3 (experimental)
//	- Using rig_options directory
//	- Renamed src function with rig_ prefix everywhere
//	- Only display phpinfo if _debug_ enabled or admin mode
//	
//	Revision 1.8  2003/01/20 12:39:51  ralfoide
//	Started version 0.6.3. Display: show number of albums or images in table view.
//	Display: display copyright in images or album mode with pref name and language strings.
//	
//	Revision 1.7  2002/11/02 04:09:32  ralfoide
//	Fixes for URLs in international strings
//	
//	Revision 1.6  2002/10/24 21:32:46  ralfoide
//	dos2unix fix
//	
//	Revision 1.5  2002/10/23 16:01:00  ralfoide
//	Added <html lang>; now transmitting charset via http headers.
//	
//	Revision 1.4  2002/10/23 08:41:03  ralfoide
//	Fixes for internation support of strings, specifically Japanese support
//	
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
