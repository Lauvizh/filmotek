<?php
/**
 * @route /person/:urlperson
 * @view /views/person.html
 */
function person($urlperson) {
	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	else{
		global $CONFIG;
		$results['results'] = array();
		$results_temp['results'] = array();
		$person = 'Unknown person';
		$pageTitle = 'Unknown person';
		$allactors = getList('actors');
		$alldirectors = getList('directors');

		$allpersons = array_merge($allactors,$alldirectors);

		if (isset($allpersons[$urlperson])) {
			$pageTitle = $allpersons[$urlperson];
			$person = $allpersons[$urlperson];
			$search = getSearchResults($allpersons[$urlperson],'person',array('actors','directors'));
			foreach ($search['results'] as $movies) {
				$results_temp['results'] = array_merge($results['results'],$movies);
			}
			$results['results'] = $results_temp['results'];
		}
		
		
		//-- Creation of the search token
		$token = uniqid(rand(), true);
		$_SESSION['form_search']['token'] = $token;
	}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'CONFIG'=>$CONFIG,
		'auth'=>$_SESSION['auth'],
		'pageTitle'=>$pageTitle,
		'form'=>$_SESSION['form_search'],
		'person' => $person,
		'results'=>$results['results']
		);
}