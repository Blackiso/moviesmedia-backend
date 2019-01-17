<?php 

	include 'details.php';

	// if (isset($_SERVER['HTTP_AUTH'])) {
		if (isset($_GET['id'])) {
			$details = new Details(null, $_GET['id']);
		}else {
			echo json_encode(array("error" => "Parameters Error!"));
		}
	// }else {
	// 	echo json_encode(array("error" => "Authentication Error!"));
	// }

 ?>