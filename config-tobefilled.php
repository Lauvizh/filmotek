<?php
$CONFIG=array();
/*----------- CONFIG DE CONNECTION BASE -------------*/

	$CONFIG['hote'] ='localhost'; // le chemin vers le serveur
	$CONFIG['port'] ='8889'; // port
	$CONFIG['nom_bd'] =''; // le nom de votre base de données
	$CONFIG['dsn'] = 'mysql:dbname='.$CONFIG['nom_bd'].';host='.$CONFIG['hote'];
	$CONFIG['utilisateur'] =''; // nom d'utilisateur pour se connecter
	$CONFIG['mot_passe'] =''; // mot de passe de l'utilisateur pour se connecter
	$CONFIG['options']  = array(PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8");
	$CONFIG['PrefixDB'] = 'filmo'; // suffixe des tables pour acroitre la securité.


/*------- Adresse du dossier image pour fonctions dans www/ -----*/
	$CONFIG['dir_posters'] = 'posters/';
	$CONFIG['prefix_posters'] = 'filmo';

/*----------- FILMO CONFIGURATION -------------*/
	$CONFIG['siteTitle'] = 'filmo';
	$CONFIG['movies_lastadded'] = 10;
	$CONFIG['movies_by_page'] = 5; // nombre d'images par pages
	$CONFIG['shortcut_by_page'] = 5; // nombre de numéro de page en bas de l'index !! impaire
