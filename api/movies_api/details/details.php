<?php 
	include "../../../autoload.php";

	class Details extends mainClass {
		protected $link = "https://api.themoviedb.org/3/movie/";
		protected $collectionLink = "https://api.themoviedb.org/3/collection/";
		protected $append_to_response = "credits,releases";
		protected $params = array('api_key', 'append_to_response');
		private $data; 

		function __construct($jwt, $id) {
			parent::__construct();

			// $this->JWT_key = $jwt;
			$this->JWT_key = "eyJhbGciOiJzaGEyNTYiLCJ0eXBlIjoiSldUIn0=.eyJ1bm0iOiJibGFja2lzbyIsImVtYSI6ImJsYWNraXNvQGJsYWNrLmNvbSIsImltZyI6ImJsYWNrLmpwZyIsIm5tZSI6bnVsbCwidWlkIjowLCJleHAiOjE1NDcwNTY3ODR9.ef323938c9ac35cbfd2c1b88217dbd580f6b135db65840451bdcf47e427f8370";
			$this->link .= $id;
			$this->id = $id;

			if(!$this->verifyJWT()) {
				$this->error['error'] = "Please Login!";
				$this->throwError();
			}

			$this->getDetails();
		}

		function getDetails() {
			$this->link = $this->addParams($this->link);
			$this->data = json_decode($this->curl($this->link));

			$movie = new movie($this->id, null, true, $this->user_id);
			$returnedData = $movie->checkIfInCollection();
			$this->data->watched = $returnedData->watched;
			$this->data->watchlist = $returnedData->watchlist;

			if ($this->data->belongs_to_collection !== null) {
				$this->getMovieCollection();
			}else {
				$this->output();
			}
		}

		function getMovieCollection() {
			unset($this->params[1]);
			$this->collectionLink .= $this->data->belongs_to_collection->id;
			$this->collectionLink = $this->addParams($this->collectionLink);
			$collectionData = json_decode($this->curl($this->collectionLink));
			$this->data->belongs_to_collection = $collectionData;
			$this->output();
		}

		function output() {
			echo json_encode($this->data);
		}
	}

 ?>