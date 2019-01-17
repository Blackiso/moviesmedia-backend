<?php 
	include 'collection.php';

	if (isset($_SERVER['HTTP_AUTH'])) {
		if (isset($_GET['type'])) {
			$page = isset($_GET['page']) ? $_GET['page'] : null;
			$sort = isset($_GET['sort']) ? $_GET['sort'] : null;
			$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
			$collection = new Collection($_SERVER['HTTP_AUTH']);
			$collection->getCollection($_GET['type'], $sort, $page, $filter);
		}else if(isset($_GET['movie']) && isset($_GET['action'])) {
			$collection = new Collection($_SERVER['HTTP_AUTH']);
			$collection->updateCollection($_GET['movie'], $_GET['action']);
		}else {
			echo json_encode(array("error" => "Parameters Error!"));
		}
	}else {
		echo json_encode(array("error" => "Authentication Error!"));
	}

	
 ?>