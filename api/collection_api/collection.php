<?php 
	include "../../autoload.php";

	class Collection extends mainClass {
		private $type;
		private $page = 1;
		private $movie;
		private $sort = "DESC";
		private $filter;
		private $output = array();

		function __construct($jwt) {
			parent::__construct();
			$this->filter = (object) array();
			$this->JWT_key = $jwt;
			if(!$this->verifyJWT()) {
				$this->error['error'] = "Please Login!";
				$this->throwError();
			}
		}

		public function updateCollection($movie_id, $action) {
			$this->movie = new movie($movie_id, $action);
			if ($action == "remove") {
				$this->removeFromCollection();
			}else {
				$this->addToCollection();
			}
		}

		public function getCollection($type, $sort, $page, $filter) {
			$this->type = $type;
			if ($sort !== null) $this->sort = $sort;
			if ($page !== null) $this->page = $page;
			if ($filter !== null) {
				$this->filter = json_decode(base64_decode($filter));
			}
			$this->getCollectionData();
		}

		public function getCollectionData() {
			$this->output['movies'] = array();
			$page_start = ($this->page - 1)*20;
			$filter = $this->constructFilterQuery();
			$query = "SELECT * FROM collection INNER JOIN movies ON `collection`.`movie_id` = `movies`.`movie_id` $filter";

			if($total_pages = $this->database->query($query)) {
				$this->output['total_pages'] = ceil($total_pages->num_rows/20);
			}

			$collection_qr = $query." ORDER BY `collection`.`id` $this->sort LIMIT $page_start, 20";

			if($result = $this->database->query($collection_qr)) {
				while($r = $this->databaseResultParse($result)) {
					$r['id'] = (int) $r['id'];
					$r['user_id'] = (int) $r['user_id'];
					$r['movie_id'] = (int) $r['movie_id'];
					$r['average'] = (int) $r['average'];
					$r['genres'] = json_decode($r['genres']);
					$r['year'] = (int) $r['year'];
					$r['runtime'] = (int) $r['runtime'];
					$r['watched'] = filter_var($r['watched'], FILTER_VALIDATE_BOOLEAN);
					$r['watchlist'] = filter_var($r['watchlist'], FILTER_VALIDATE_BOOLEAN);

					array_push($this->output['movies'], $r);
				}

				echo json_encode($this->output);
			}else {
				$this->error['error'] = $this->database->error;
				$this->throwError();
			}
		}

		private function constructFilterQuery() {
			$qr = "WHERE user_id=$this->user_id AND $this->type=true ";
			foreach ($this->filter as $name => $value) {
				if (!empty($value)) {
					$init_name = explode("_", $name);
					$md_name = explode(".", $init_name[sizeof($init_name) - 1]);
					$af_name = $md_name[0];
					
					if ($af_name == "cast") {
						$af_name = "actor";
						$value = $this->getCastID($value);
					}

					if (gettype($value) == "array") {
						$size = sizeof($value);
						$append = "AND ";
						foreach ($value as $i => $item) {
							$append .= "genres LIKE '%$item%'";
							if ($i < $size - 1) $append .= " AND ";
						}
						$qr .= $append;

					}else {
						$mark = isset($md_name[1]) ? ">" : "=";
						$qr .= " AND $af_name $mark $value";
					}
				}
			}
			return $qr;
		}

		private function getCastID($name) {
			$reg_name = implode('%', explode(' ', $name));
			$db_ckeck_qr = "SELECT * FROM cast_crew WHERE cc_name LIKE '%$reg_name%' AND cc_type = 'actor'"; 

			if($result = $this->database->query($db_ckeck_qr)) {
				$result = $this->databaseResultParse($result);
				if (empty($result)) {
					# code...
				}else {
					return $result['cc_id'];
				}
			}
		}

		private function addToCollection() {
			if ($this->movie->insertMovie()) {
				$movie_id = $this->movie->movie_id;
				$watched = $this->movie->watched == true ? "true" : "false";
				$watchlist = $this->movie->watchlist == true ? "true" : "false";

				if ($this->checkIfInCollection($movie_id)) {
					$collection_add_qr = "INSERT INTO collection (user_id, movie_id, watched, watchlist) VALUES ($this->user_id, $movie_id, $watched, $watchlist)";
					$this->database->query($collection_add_qr);
				}else {
					$collection_update_qr = "UPDATE collection SET watched = $watched, watchlist = $watchlist WHERE movie_id = $movie_id AND user_id = $this->user_id";
					$this->database->query($collection_update_qr);
				}
				
				if ($this->database->error) {
					$this->error['error'] = $this->database->error;
					$this->throwError();
				}else {
					echo json_encode(array("succses" => true));
				}
			}		
		}

		private function removeFromCollection() {
			$movie_id = $this->movie->movie_id;
			if (!$this->checkIfInCollection($movie_id)) {
				$remove_qr = "DELETE FROM collection WHERE movie_id = $movie_id AND user_id = $this->user_id";
				if (!$this->database->query($remove_qr)) {
					$this->error['error'] = $this->database->error;
					$this->throwError();
				}else {
					echo json_encode(array("succses" => true));
				}
			}
		}

		private function checkIfInCollection($movie_id) {
			$check_qr = "SELECT movie_id FROM collection WHERE movie_id = $movie_id AND user_id = $this->user_id";
			if ($result = $this->database->query($check_qr)) {
				$result = $this->databaseResultParse($result);
				if (empty($result)) {
					return true;
				}else {
					return false;
				}
			}
		}
	}
 ?>