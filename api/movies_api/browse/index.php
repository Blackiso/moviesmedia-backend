<?php 

	include 'browse.php';

	if (isset($_SERVER['HTTP_AUTH'])) {
		if (isset($_GET['type'])) {
			$page = isset($_GET['page']) ? $_GET['page'] : null;
			$browse = new Browse($_SERVER['HTTP_AUTH'], $_GET['type'], $page);
		}else {
			echo json_encode(array("error" => "Parameters Error!"));
		}
	}else {
		echo json_encode(array("error" => "Authentication Error!"));
	}

 ?>