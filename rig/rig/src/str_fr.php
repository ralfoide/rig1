<?php
// vim: set tabstop=4 shiftwidth=4: //
//************************************************************************
/*
	$Id$

	Copyright 2004, Raphael MOLL.

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
$html_album_title	= 'Album RIG';
$html_album			= 'Album';
$html_admin			= 'Admin';
$html_none			= 'D&eacute;but';

// Nom du la racine des albums
$html_root			= 'D&eacute;but';

// Images
$html_image_title	= 'Image RIG';
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
$html_last_update   = 'Mise &agrave; jour: [date]';

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
//	Revision 1.19  2004/07/17 07:52:31  ralfoide
//	GPL headers
//
//	Revision 1.18  2004/03/09 06:22:30  ralfoide
//	Cleanup of extraneous CVS logs and unused <script> test code, with the help of some cognac.
//	
//	Revision 1.17  2004/03/02 10:38:01  ralfoide
//	Translation of tooltip string.
//	New page title strings.
//
//	[...]
//
//	Revision 1.1  2002/08/04 00:58:08  ralfoide
//	Uploading 0.6.2 on sourceforge.rig-thumbnail
//
//	[...]
//
//	Revision 1.2  2001/11/26 04:35:20  ralf
//	version 0.6 with location.php
//-------------------------------------------------------------
?>
