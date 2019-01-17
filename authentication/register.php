<?php 
	
	include '../autoload.php';

	class register extends mainClass {
		
		private $password;
		private $re_password;

		function __construct($username, $email, $password, $re_password) {
			parent::__construct();
			
			$this->username = $username;
			$this->email = $email;
			$this->password = $password;
			$this->re_password = $re_password;
			$this->image = "default.png";
			$this->name = null;
			$this->verifyCreds();
		}

		private function verifyCreds() {
			
			if (strlen($this->password) < 5 || strlen($this->password) > 32) {
				$this->error['error'] = "Username Length Minimum 5 Characters Maximum 32!";	
			}

			if ($this->password !== $this->re_password) {
				$this->error['error'] = "Passwords not Matching!";
			}

			if (!preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $this->email)) {
				$this->error['error'] = "Enter a Valid Email!";	
			}

			if (strlen($this->username) < 5 || strlen($this->username) > 20) {
				$this->error['error'] = "Username Length Minimum 5 Characters Maximum 20!";	
				exit;
			}

			if (!preg_match('/([0-9_-]*[a-z][0-9_-]*){3}/', $this->username)) {
				$this->error['error'] = "Username not Supported!";
			}

			if ($this->password == "" || $this->re_password == "" || $this->username == "" || $this->email == "") {
				$this->error['error'] = "Please Enter your Information!";
			}
			
			$check_email_query = "SELECT email FROM users WHERE email = '$this->email'";
			$check_username_query = "SELECT username FROM users WHERE username = '$this->username'";

			if ($result = $this->database->query($check_email_query)) {
				if (!empty($this->databaseResultParse($result))) {
					$this->error['error'] = "Email Already Taken!";	
				}
			}

			if ($result = $this->database->query($check_username_query)) {
				if (!empty($this->databaseResultParse($result))) {
					$this->error['error'] = "Username Already Taken!";	
				}
			}

			if (isset($this->error['error'])) {
				$this->throwError();
			}else {
				$this->register();
			}
		}

		private function register() {
			$this->getMoreDetails();
			$this->auth_key = hash_hmac('sha256', $this->username, time());
			$this->password = hash_hmac('sha256', $this->password, $this->auth_key);

			$register_query = "INSERT INTO users (username, password, email, auth_key, register_date, user_ip)
							VALUES ('$this->username', '$this->password', '$this->email', '$this->auth_key', '$this->this_date', '$this->user_ip')";

			if ($result = $this->database->query($register_query)) {
				$this->user_id = $this->database->insert_id;
				$key = $this->generateJWT();

				$final_data = array("sucsses" => true, "JWT_key" => $key);
				echo json_encode($final_data);
			}else if ($this->database->error) {
				$this->error['query'] = $this->database->error;
				$this->throwError();
			}
		}
	}

	if (isset($_GET['data'])) {
		if (preg_match("/^(ey)/", $_GET['data'])) {
			$data = json_decode(base64_decode($_GET['data']));
			$register = new register($data->username, $data->email, $data->password, $data->re_password);
		}else {
			echo json_encode(array("error" => "Base64 Error!"));
		}
	}else {
		echo json_encode(array("error" => "Data Error!"));
	}
 ?>