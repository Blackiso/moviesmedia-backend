<?php 
	include "../../../autoload.php";

	class Browse extends mainClass {
		protected $link = "https://api.themoviedb.org/3/movie/";
		protected $params = array('api_key', 'page');
		protected $page = 1;
		protected $data;
		
		function __construct($jwt, $type, $page) {
			parent::__construct();

			$this->type = $type;
			$this->page = $page !== null ? $page : 1;
			$this->JWT_key = $jwt;

			if(!$this->verifyJWT()) {
				$this->error['error'] = "Please Login!";
				$this->throwError();
			}

			$this->runBrowse();
		}

		private function runBrowse() {
			$this->link .= $this->type;
			$this->link = $this->addParams($this->link);
			$this->data = $this->curl($this->link);
			echo json_encode($this->checkData($this->data));
		}
	}

 ?>