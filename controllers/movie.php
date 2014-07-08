<?php
/**
 * @route /movie/:id
 * @view /views/movie.html
 */
function movie($id) {
	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	else{
		global $CONFIG;
		$movie = new MovieDetails($id);
		$pageTitle = '';
	}
	$flashMsgs = Flash();
	return array('flashMsgs'=>$flashMsgs, 'CONFIG'=>$CONFIG, 'auth'=>$_SESSION['auth'], 'pageTitle'=>$pageTitle, 'movie'=>$movie);
}