<?php 
	header('Content-Type: application/json');


	class mainClass {
		//constents
		// const DB_USERNAME = "b24_22765359";
		// const DB_PASSWORD = "perlnova_password_852";
		// const DB_HOST = "sql313.byethost.com";
		// const DB_NAME = "b24_22765359_moviesmedia_beta";
		private const DB_USERNAME = "root";
		private const DB_PASSWORD = "";
		private const DB_HOST = "127.0.0.1";
		private const DB_NAME = "moviesmedia";

		protected $database;

		protected $api_key = "1836b7fa49fccf1fd2ed0be57c06209c";
		protected $JWT_key;
		protected $user_id; 
		protected $auth_key;
		protected $user_ip;
		public $username;
		public $email;
		public $name;
		public $image;
		public $this_date;
		public $user_agent;

		public $error = array();

		function __construct() {
			$this->database = new mysqli(self::DB_HOST, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

			if ($this->database->connect_error) {
				$this->error['error'] = $this->database->connect_error;
				$this->throwError();
			}
		}

		protected function generateJWT() {
			//Get Auth key if unavailable
			if (empty($auth_key)) {
				$result = $this->getUserDatabase(array("auth_key"), "user_id", $this->user_id);
				$this->auth_key = $result['auth_key'];
			}

			if (!isset($this->username) || !isset($this->email) || !isset($this->user_id)) {
				$this->error['error'] = "credentials error!";
				$this->throwError();
			}

			//Setup JWT Key
			$JWT_header = (object)array();
			$JWT_header->alg = "sha256";
			$JWT_header->type = "JWT";
			$JWT_header = base64_encode(json_encode($JWT_header));

			$JWT_payload = (object)array();
			$JWT_payload->unm = $this->username;
			$JWT_payload->ema = $this->email;
			$JWT_payload->img = $this->image;
			$JWT_payload->nme = $this->name;
			$JWT_payload->uid = $this->user_id;
			$JWT_payload->exp = time() + (7 * 24 * 60 * 60);
			$JWT_payload = base64_encode(json_encode($JWT_payload));

			//Sign JWT key
			$key = $JWT_header . "." . $JWT_payload;
			$signature = hash_hmac("sha256", $key, $this->auth_key);

			return $key . "." . $signature;
		}

		protected function verifyJWT() {
			$jwt_parts = explode(".", $this->JWT_key);
			$header = json_decode((base64_decode($jwt_parts[0])));
			$payload = json_decode((base64_decode($jwt_parts[1])));
			$signature = $jwt_parts[2];

			$this->user_id = $payload->uid;
			$this->username = $payload->unm;
			$this->email = $payload->ema;

			$user_key = $this->getUserDatabase(array("auth_key"), "user_id", $this->user_id);

			if (empty($user_key)) {
				return false;
			}else {
				$this->auth_key = $user_key['auth_key'];
				$new_signature = hash_hmac($header->alg, $jwt_parts[0].".".$jwt_parts[1], $this->auth_key);

				if ($signature == $new_signature) {
					return true;
				}else {
					return false;
				}
			}
		}

		protected function getUserDatabase($props, $condition, $value) {
			$p1_query = "SELECT ";
			$p2_query = "FROM users WHERE $condition = '$value'";
			$array_size = sizeof($props);

			foreach ($props as $key => $prop) {
				$p1_query .= $prop;
				$p1_query .= $key < $array_size - 1 ? ", " : " ";
			}

			$p1_query .= $p2_query;

			if($result = $this->database->query($p1_query)) {
				return $this->databaseResultParse($result);
			}else {
				$this->error['query'] = $this->database->error;
				$this->throwError();
			}
			
		}

		protected function addActivity() {
			$this->getMoreDetails();
			$activity_query = "INSERT INTO users_activity (user_id, new_ip, last_login, agent) 
							VALUES ('$this->user_id', '$this->user_ip', '$this->this_date', '$this->user_agent')";
			$this->database->query($activity_query);
		}

		public function databaseResultParse($result) {
			return $result->fetch_array(MYSQLI_ASSOC);
		}

		public function curl($link) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $link); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$out = curl_exec($ch);
			curl_close($ch);

			return $out;
		}

		protected function addParams($link) {
			$size = sizeof($this->params);
			foreach ($this->params as $i => $value) {
				$link .= $i == 0 ? '?' : '&';
				$link .= "$value=";
				$link .= $this->{$value};
			}

			return $link;
		}

		protected function checkData($data) {
			if (gettype($data) == "string") $data = json_decode($data);
			
			foreach ($data->results as $movie) {
				$movieClass = new movie($movie->id, null, true, $this->user_id);
				$returnedValues = $movieClass->checkIfInCollection();
				$movie->watched = $returnedValues->watched;
				$movie->watchlist = $returnedValues->watchlist;
			}

			return $data;
		}

		public function throwError() {
			die(json_encode($this->error));
		}

		protected function getMoreDetails() {
			//User IP
			$this->user_ip = $this->getRealIpAddr();
			//Register Date
			$this->this_date = date('d-m-Y / H:i:s');
			//User Agent
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		}

		protected function getRealIpAddr() {
		    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		     	$ip = $_SERVER['HTTP_CLIENT_IP'];
		    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		     	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }else {
		     	$ip = $_SERVER['REMOTE_ADDR'];
		    }
		    return $ip;
		}
	}
 ?>