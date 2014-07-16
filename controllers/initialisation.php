<?php
/**
 * @route /initialisation
 * @view /views/initialisation.html
 */
function initialisation() {
		global $CONFIG;
		$formErrors = array();
		$pageTitle = 'initialisation';

		if(isset($_SESSION['form_init']['token']) && isset($_POST['token'])){
			if($_SESSION['form_init']['token'] == $_POST['token']){

				if(!isset($_POST['surname']) || empty($_POST['surname']) || strlen($_POST['surname'])>35 || strlen($_POST['surname'])<1){
	        		$formErrors['surname'] = 'invalid surname, need to contain 1 to 35 characters';
	        		}
	        	if(!isset($_POST['email']) || !isEmail($_POST['email'])){
	        		$formErrors['email'] = 'invalid email';
	        		}
	        	if (!isset($_POST['password']) || empty($_POST['password']) || strlen($_POST['password'])<6) {
	        		$formErrors['password'] = 'invalid password need to contain 6 characters minimum';
	                }
	            if (!isset($_POST['passwordconf']) || empty($_POST['passwordconf'])) {
	        		$formErrors['passwordconf'] = 'missing password confirmation';
	                }

	            if (!empty($_POST['password']) && !empty($_POST['passwordconf']) && $_POST['password'] != $_POST['passwordconf']) {
	            	$formErrors['password'] = 'Passwords are differents';
	        		$formErrors['passwordconf'] = 'Passwords are differents';
	                }

	            if (empty($formErrors)) {
	            	if (initializeFilmotek($_POST['email'], $_POST['surname'], $_POST['password'])) {
	            		$admin = UserConnect($_POST['email'], $_POST['password']);
		            	if ($admin){
		            		$_SESSION['auth'] = $admin;
		            		$_SESSION['auth']['status'] = true;
		            		setFlash('Initialisation ok', 'success');
		            		unset($_SESSION['form_init']);
		            		header('Location: /');
							die();
		            	}
		            	else{
		            		setFlash('admin connection failed ', 'danger');
		            	}
					}
					else{
		            	setFlash('Initialisation failed', 'danger');
		            }
	            }
		    }

		    else{
		    	setFlash('Unvalid initialisation token', 'danger');
		    }

		}
		else{
			//----- CSRF Protection
			$token = uniqid(rand(), true);
			$_SESSION['form_init']['token'] = $token;
		}
	$flashMsgs = Flash();
	return array(
		'flashMsgs'=>$flashMsgs,
		'formErrors'=>$formErrors,
		'CONFIG'=>$CONFIG,
		'pageTitle'=>$pageTitle,
		'form'=>$_SESSION['form_init']
		);
}