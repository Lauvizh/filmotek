<?php
$CONFIG=array();

/*----------- SETTINGS CHAGEABLE -------------*/

	$CONFIG['serverHost'] =''; // Server <--- fill this
	$CONFIG['dbName'] =''; // Datebase Name <--- fill this
	$CONFIG['dbUser'] =''; // database user name <--- fill this
	$CONFIG['dbPassword'] =''; // database password <--- fill this
	$CONFIG['PrefixDB'] = 'filmo'; // tables prefix to insure unique tables. <--- (optional)

/*----------- INTERFACE PARAMETERS -------------*/

	$CONFIG['siteTitle'] = 'filmo';
	$CONFIG['homepage_movies_by_category'] = 10;
	$CONFIG['movies_by_pages'] = 5; // number of movies by categories.
	$CONFIG['shortcut_by_page'] = 5; // nombre de numéro de page en bas de l'index !! impaire

/*----------- MYSQL CONFIGURATION -------------*/

	$CONFIG['options']  = array(PDO::MYSQL_ATTR_INIT_COMMAND    => "SET NAMES utf8"); // ! don't change exept
	$CONFIG['dsn'] = 'mysql:dbname='.$CONFIG['dbName'].';host='.$CONFIG['serverHost']; // ! don't change exept


