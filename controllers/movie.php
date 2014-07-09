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
		$pageTitle = $movie->title;
		//-- Creation of the search token
		$token = uniqid(rand(), true);
		$_SESSION['form_search']['token'] = $token;
	}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'CONFIG'=>$CONFIG,
		'auth'=>$_SESSION['auth'],
		'form'=>$_SESSION['form_search'],
		'pageTitle'=>$pageTitle,
		'movie'=>$movie
		);
}