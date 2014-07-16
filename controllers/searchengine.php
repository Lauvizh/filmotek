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
		$search['key_words'] = array();
		$search['results'] = array();
		$nbresults = array();
		$pageTitle = 'searchresult';
		if(isset($_SESSION['form_search']['token']) && isset($_POST['token'])){
			if($_SESSION['form_search']['token'] == $_POST['token']){
	        	if (!isset($_POST['search']) || empty($_POST['search'])) {
	        		$formErrors['search'] = 'missing search parameters';
	                }
	            else if (isset($_POST['search']) && strlen($_POST['search']) > 64) {
	        			$formErrors['search'] = 'your request is to long please try again with less than 64 characteres';
	        		}

	            if (empty($formErrors)) {
	            	$search = getSearchResults($_POST['search']);
	            	foreach ($search['results'] as $k => $s) {
	            		$nbresults[$k] = count($s);
	            	}
	            		
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
		'search_key_words'=>$search['key_words'], 
		'results'=>$search['results']
		);
}