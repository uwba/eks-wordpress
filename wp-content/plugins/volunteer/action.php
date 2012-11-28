<?php
if (!session_id()) session_start();

//var_dump($_SESSION);



if (count($_POST)) {
	switch ($_SESSION['volunteer']['step']) {
		case 1:
			// TODO: validate fields
			// TODO: display errors
			$_SESSION['volunteer']['name'] = $_POST['name'];
			$_SESSION['volunteer']['phone'] = $_POST['phone'];
			$_SESSION['volunteer']['email'] = $_POST['email'];
			
			$valid = TRUE;
			$_SESSION['volunteer']['step'] = 2;
			header('Location: http://' . $_SERVER["SERVER_NAME"] . '/volunteer-registraion/');
			break;
		case 2:
			// TODO: validate fields
			// TODO: display errors
			foreach ($_POST['position'] as $value) {
				$_SESSION['volunteer']['position'][] = $value;
			}
//			echo 'ff';
			$_SESSION['volunteer']['preparer'] = $_POST['preparer'];
			$valid = TRUE;
			$_SESSION['volunteer']['step'] = 3;
//			wp_redirect('http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
			header('Location: http://' . $_SERVER["SERVER_NAME"] . '/volunteer-registraion/');
			//wp_redirect( site_url('wp-login.php?action=login') );
			break;
		default:
			break;
	}
}
?>
