<?php
/**
 * @route /
 * @view /views/home.html
 */
function home() {
	global $CONFIG;
	$pageTitle = 'home';

	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}

	$last_added['movie'] = new lastMoviesByGenre('movie');
	$last_added['animation'] = new lastMoviesByGenre('animation');
	$last_added['show'] = new lastMoviesByGenre('show');
	$last_added['documentary'] = new lastMoviesByGenre('documentary');
	$last_added['kids'] = new lastMoviesByGenre('kids');

	$flashMsgs = Flash();

    return array('flashMsgs'=>$flashMsgs, 'CONFIG'=>$CONFIG, 'pageTitle'=>$pageTitle, 'auth'=>$_SESSION['auth'], 'last_added'=>$last_added,);
}