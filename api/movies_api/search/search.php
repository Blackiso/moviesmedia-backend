<?php 
	include "../../../autoload.php";

	class Search extends mainClass {
		protected $keyWord_link = "https://api.themoviedb.org/3/search/movie";
		protected $filter_link = "https://api.themoviedb.org/3/discover/movie";
		protected $params = array('api_key');
		protected $query;
		protected $page = 1;

		function __construct($jwt, $page) {
			parent::__construct();

			$this->JWT_key = "eyJhbGciOiJzaGEyNTYiLCJ0eXBlIjoiSldUIn0=.eyJ1bm0iOiJibGFja2lzbyIsImVtYSI6ImJsYWNraXNvQGJsYWNrLmNvbSIsImltZyI6ImJsYWNrLmpwZyIsIm5tZSI6bnVsbCwidWlkIjowLCJleHAiOjE1NDU4NTg5MDN9.e7cc4c6e75468f436ec1af512404883a139128e906f5e1bf1249d572cdf87629";

			if(!$this->verifyJWT()) {
				$this->error['error'] = "Please Login!";
				$this->throwError();
			}

			if (isset($page)) {
				$this->page = $page;
				array_push($this->params, "page");
			}
		}

		public function keyWordSearch($keyWord) {
			array_push($this->params, "query");
			$this->query = urlencode($keyWord);
			$this->keyWord_link = $this->addParams($this->keyWord_link);
			$data = $this->curl($this->keyWord_link);
			echo json_encode($this->checkData($data));
		}

		public function filtredData($filter) {
			$this->filter_link = $this->addParams($this->filter_link);
			$filter = json_decode(base64_decode($filter));
			foreach ($filter as $key => $value) {
				if (!empty($value)) {
					$this->filter_link .= "&";
					if (gettype($value) == "array") {
						$value = implode(",", $value);
					}
					$this->filter_link .= $key."=".urlencode($value);
				}
			}
			$data = $this->curl($this->filter_link);
			echo json_encode($this->checkData($data));
		}

	}

 ?>