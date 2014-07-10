<?php
session_start();
require_once 'config.php';
require_once 'models/functions.php';
require_once 'models/class.php';

//----- DB CONNECTION
$db = dbConnection();

//----- CONTROLLERS
require_once 'controllers/connection.php';
require_once 'controllers/home.php';
require_once 'controllers/genre.php';
require_once 'controllers/searchengine.php';
require_once 'controllers/movie.php';

//----- PHP BOTTLE
require_once 'bottle.php';