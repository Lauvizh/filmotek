<?php
/**
 * @route /admin/users
 * @view /views/adminusers.html
 */
function adminUsers() {
	global $CONFIG;
	$pageTitle = 'home';

	if (!isset($_SESSION['auth']['status']) || !$_SESSION['auth']['status']) {
		header('Location: /connection');
		die();
	}
	if (!isset($_SESSION['auth']['rank']) || $_SESSION['auth']['rank'] != "admin" ) {
		setFlash('Sorry, only administrators have acces there', 'danger');
		header('Location: /');
		die();
	}

	$flashMsgs = Flash();

	//-- Creation of the search token
	$token = uniqid(rand(), true);
	$_SESSION['form_search']['token'] = $token;

    return array(
    	'flashMsgs'=>$flashMsgs,
    	'CONFIG'=>$CONFIG,
    	'pageTitle'=>$pageTitle,
    	'auth'=>$_SESSION['auth'],
    	'form'=>$_SESSION['form_search']
    	);
}