<?php
	
	include '../autoload.php';

	class login extends mainClass {
		private $password;
		
		function __construct($username, $password) {
			parent::__construct();
			$this->username = $username;
			$this->password = $password;
			$this->verifyCreds();
		}

		private function verifyCreds() {
			if (!preg_match('/([0-9_-]*[a-z][0-9_-]*){3}/', $this->username)) {
				$this->error['error'] = "Error Username or Password!";
			}

			if ($this->password == "" || $this->username == "") {
				$this->error['error'] = "Please Enter your Information!";
			}

			if (empty($this->error)) {
				$this->checkLogin();
			}else {
				$this->throwError();
			}
		}
		
		private function checkLogin() {
			$data_elemnts = array("user_id", "password", "email", "name", "image", "auth_key");
			$condition = "username";
			$value = $this->username;
			$result = $this->getUserDatabase($data_elemnts, $condition, $value);

			if (empty($result)) {
				$this->error['error'] = "Error Username or Password!";
			}else {
				$this->auth_key = $result['auth_key'];
				$this->email = $result['email'];
				$this->name = $result['name'];
				$this->image = $result['image'];
				$this->user_id = (int)$result['user_id'];

				$this->password = hash_hmac('sha256', $this->password, $this->auth_key);

				if ($this->password !== $result['password']) {
					$this->error['error'] = "Error Username or Password!";
				}
			}
			
			if (empty($this->error)) {
				$key = $this->generateJWT();
				$this->addActivity();
				$final_data = array("sucsses" => true, "JWT_key" => $key);
				setcookie("login", true, time() + (900400 * 30), "/");
				setcookie("key", $key, time() + (900400 * 30), "/");
				echo json_encode($final_data);
			}else {
				$this->throwError();
			}
		}
	}


	if (isset($_GET['data'])) {
		if (preg_match("/^(ey)/", $_GET['data'])) {
			$data = json_decode(base64_decode($_GET['data']));
			$login = new login($data->username, $data->password);
		}else {
			echo json_encode(array("error" => "Base64 Error!"));
		}
	}else {
		echo json_encode(array("error" => "Data Error!"));
	}
 ?>