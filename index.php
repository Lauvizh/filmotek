<?php
session_start();
require_once 'config.php';
require_once 'models/functions.php';
require_once 'models/class.php';

//----- DB CONNECTION
$db = dbConnection();

//----- ADMINISTRATION CONTROLERS

/**
 * @route /admin
 */
function admin() {
	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	if (!isset($_SESSION['auth']['rank']) || $_SESSION['auth']['rank'] != "admin" ) {
		setFlash('Sorry, only administrators have acces there', 'danger');
		header('Location: /');
		die();
	}

	header('Location: /admin/movies');
	die();
}

require_once 'controllers/adminusers.php';
require_once 'controllers/adminmovies.php';

//----- CONTROLLERS
require_once 'controllers/connection.php';
require_once 'controllers/initialisation.php';
require_once 'controllers/home.php';
require_once 'controllers/genre.php';
require_once 'controllers/searchengine.php';
require_once 'controllers/movie.php';
require_once 'controllers/person.php';

//----- PHP BOTTLE
require_once 'bottle.php';