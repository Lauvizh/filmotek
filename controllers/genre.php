<?php
/**
 * @route /genre/:genre
 * @view /views/genre.html
 */
function genre($ChosenGenre='all') {
	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	else{
		global $CONFIG;
		$pageTitle = $ChosenGenre;
		$results = array();
		$nbresults = 0;
		//-- Creation of the search token
		$token = uniqid(rand(), true);
		$_SESSION['form_search']['token'] = $token;

		$genres = getGenres();
		if ($ChosenGenre != 'all') {
			$results = getMoviesByGenre($genres[$ChosenGenre]);
			$nbresults = count($results);
		}

	}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'CONFIG'=>$CONFIG,
		'auth'=>$_SESSION['auth'],
		'form'=>$_SESSION['form_search'],
		'pageTitle'=>$pageTitle,
		'genres'=>$genres,
		'ChosenGenre'=>$ChosenGenre,
		'nbresults' => $nbresults,
		'results'=>$results
		);
}