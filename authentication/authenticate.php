<?php 
	
	header('Content-Type: application/json');
	header("Access-Control-Allow-Origin: *");
	// header("Access-Control-Allow-Credentials: true");
	header("Access-Control-Allow-Methods: GET, POST");
	header("Access-Control-Allow-Headers: Content-Type, *");

	if (isset($_COOKIE['login']) && isset($_COOKIE['key'])) {
		if ($_COOKIE['login'] == true) {
			echo json_encode(array("login" => true, "JWT_key" => $_COOKIE['key']));
		}else {
			echo json_encode(array("login" => false));
		}
	}else {
		echo json_encode(array("login" => false));
	}

 ?>