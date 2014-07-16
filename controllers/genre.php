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
		$pageTitle = 'Unknown genre';
		$results = array();
		$genres = array();
		$nbresults = 0;
		//-- Creation of the search token
		$token = uniqid(rand(), true);
		$_SESSION['form_search']['token'] = $token;

		try {
			$genres = getList('genre');
		} catch (Exception $e) {
			$errors['genres'] = $e->getMessage(); 
		}
		
		if ($ChosenGenre != 'all') {
			if (isset($genres[$ChosenGenre])) {
				$pageTitle = $genres[$ChosenGenre];
				try {
					$results = getMoviesByGenre($genres[$ChosenGenre]);
				} catch (Exception $e) {
					$errors['movies'] = $e->getMessage();
				}
				
			}
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
		'errors'=>$errors,
		'genres'=>$genres,
		'ChosenGenre'=>$ChosenGenre,
		'nbresults' => $nbresults,
		'results'=>$results
		);
}