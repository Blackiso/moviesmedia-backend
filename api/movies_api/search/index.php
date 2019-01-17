<?php 
	include 'search.php';

	if (isset($_SERVER['HTTP_AUTH'])) {
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$search = new Search(null, $page);
		if (isset($_GET['keyword'])) {
			$search->keyWordSearch($_GET['keyword']);
		}elseif (isset($_GET['filter'])) {
			$search->filtredData($_GET['filter']);
		}else {
			echo json_encode(array("error" => "Parameters Error!"));
		}
	}else {
		echo json_encode(array("error" => "Authentication Error!"));
	}
 ?>