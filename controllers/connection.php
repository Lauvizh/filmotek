<?php
/**
 * @route /connection
 * @view /views/connection.html
 */
function connection() {
	if (!tableExists('filmoUsers')) {
		header('Location: /initialisation');
		die();
	}
	else if (isset($_SESSION['auth']['status']) && $_SESSION['auth']['status']) {
		header('Location: /');
		die();
	}
	else{
		global $CONFIG;
		$formErrors = array();
		$pageTitle = 'connection';
		if(isset($_SESSION['form_conection']['token']) && isset($_POST['token'])){
			if($_SESSION['form_conection']['token'] == $_POST['token']){
	        	if(!isset($_POST['email']) || !isEmail($_POST['email'])){
	        		$formErrors['email'] = 'invalid email';
	        		}
	        	if (!isset($_POST['password']) || empty($_POST['password'])) {
	        		$formErrors['password'] = 'missing password';
	                }
	            if (empty($formErrors)) {
	            	$user = UserConnect($_POST['email'], $_POST['password']);
	            	if ($user){
	            		$_SESSION['auth'] = $user;
	            		$_SESSION['auth']['status'] = true;
	            		setFlash('Connection ok', 'success');
	            		unset($_SESSION['form_conection']);
	            		header('Location: /');
						die();
	            	}
	            	else{
	            		setFlash('Connection failed', 'danger');
	            	}
	            }
		    }
		    else{
		    	setFlash('Unvalid Connection token', 'danger');
		    }
		}
		else{
			//----- CSRF Protection
			$token = uniqid(rand(), true);
			$_SESSION['form_conection']['token'] = $token;
		}
	}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'formErrors'=>$formErrors,
		'CONFIG'=>$CONFIG,
		'pageTitle'=>$pageTitle,
		'form'=>$_SESSION['form_conection']
		);
}