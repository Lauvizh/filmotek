<?php
/**
 * @route /searchengine
 * @view /views/searchengine.html
 */
function search() {
	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	else{
		global $CONFIG;
		$formErrors = array();
		$results = array();
		$nbresults = 0;
		$pageTitle = 'searchresult';
		if(isset($_SESSION['form_search']['token']) && isset($_POST['token'])){
			if($_SESSION['form_search']['token'] == $_POST['token']){
	        	if (!isset($_POST['search']) || empty($_POST['search'])) {
	        		$formErrors['search'] = 'missing search parameters';
	                }
	            if (empty($formErrors)) {
	            	$results = getSearchResults($_POST['search']);
	            	$nbresults = count($results);
	            	
	            }
		    }
		    else{
		    	setFlash('Unvalid Search token', 'danger');
		    }
		}
		else{
			//----- CSRF Protection
			$token = uniqid(rand(), true);
			$_SESSION['form_search']['token'] = $token;
		}
	}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'CONFIG'=>$CONFIG,
		'auth'=>$_SESSION['auth'],
		'pageTitle'=>$pageTitle,
		'form'=>$_SESSION['form_search'],
		'formErrors'=>$formErrors,
		'nbresults' => $nbresults,
		'results'=>$results,
		);
}