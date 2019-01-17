<?php

	class movie extends mainClass {
		public $movie_id;
		public $title;
		public $poster_path;
		public $release_date;
		public $year;
		public $average;
		public $genres;
		public $runtime;
		public $actor;
		public $director;
		public $watched;
		public $watchlist;

		function __construct($movie_id, $action, $check = false, $user_id = null) {
			parent::__construct();
			$this->movie_id = $movie_id;
			$this->user_id = $user_id;
			if (!$check) {
				$this->watched = $action == "watched" ? true : false;
				$this->watchlist = $action == "watchlist" ? true : false;
			}
		}

		public function insertMovie() {
			if (!$this->checkIfInserted()) {
				$this->initData();
				$actor_id = $this->actor->id;
				$director_id = $this->director->id;
				
				$movie_qr = "INSERT INTO movies(movie_id, title, poster_path, release_date, year, average, genres, runtime, actor, director) VALUES ($this->movie_id, '$this->title', '$this->poster_path', '$this->release_date', $this->year, $this->average, '$this->genres', $this->runtime, $actor_id, $director_id)";

				if ($this->database->query($movie_qr)) {
					if ($this->insertCastCrew()) {
						return true;
					}
				}else {
					echo $movie_qr;
					$this->error['error'] = $this->database->error;
					$this->throwError();
				}
			}else {
				return true;
			}
		}

		private function insertCastCrew() {
			$groupe = (object) array();

			$groupe->actor = (object) array();
			$groupe->actor->id = $this->actor->id;
			$groupe->actor->type = "actor";
			$groupe->actor->name = $this->actor->name;
			$groupe->actor->profile_path = $this->actor->profile_path;

			$groupe->director = (object) array();
			$groupe->director->id = $this->director->id;
			$groupe->director->type = "director";
			$groupe->director->name = $this->director->name;
			$groupe->director->profile_path = $this->director->profile_path;

			foreach ($groupe as $cc) {
				$qr_check = "SELECT cc_id FROM cast_crew WHERE cc_id = $cc->id";
				if ($check_result = $this->database->query($qr_check)) {
					$check_result = $this->databaseResultParse($check_result);
					if (empty($check_result)) {
						$insert_qr = "INSERT INTO cast_crew (cc_id, cc_name, cc_image, cc_type) VALUES ($cc->id, '$cc->name', '$cc->profile_path', '$cc->type')";
						if (!$this->database->query($insert_qr)) {
							$this->error = $this->database->error;
						}
					}
				}else {
					$this->error = $this->database->error;
				}
			}

			if ($this->error) {
				$this->throwError();
			}else {
				return true;
			}
		}

		private function checkIfInserted() {
			$check_qr = "SELECT movie_id FROM movies WHERE movie_id=$this->movie_id";
			if ($result = $this->database->query($check_qr)) {
				$result = $this->databaseResultParse($result);
				if (empty($result)) {
					return false;
				}else {
					return true;
				}
			}
		}

		public function checkIfInCollection() {
			$qr_to_check = "SELECT * FROM collection WHERE user_id=$this->user_id AND movie_id=$this->movie_id";
			if ($result = $this->database->query($qr_to_check)) {
				$result = $this->databaseResultParse($result);
				$data = (object)array();
				if (empty($result)) {
					$data->watched = false;
					$data->watchlist = false;
					return $data;
				}else {
					$data->watched = $result['watched'] == 1 ? true : false;
					$data->watchlist = $result['watchlist']  == 1 ? true : false;
					return $data;
				}
			}else {
				$this->error['error'] = $this->database->error;
				$this->throwError();
			}
		}

		private function initData() {
			$link = "http://api.themoviedb.org/3/movie/$this->movie_id?api_key=$this->api_key&append_to_response=credits";
			$movieData = json_decode($this->curl($link));
			if ($movieData !== null) {
				$this->title = addslashes($movieData->title);
				$this->poster_path = $movieData->poster_path;
				$this->release_date = $movieData->release_date;
				$this->year = explode("-", $movieData->release_date)[0];
				$this->average = $movieData->vote_average;
				$this->genres = [];
				foreach ($movieData->genres as $value) {
					array_push($this->genres, $value->id);
				}
				$this->genres = json_encode($this->genres);
				$this->runtime = $movieData->runtime == null ? 0 : $movieData->runtime;
				$this->actor = $movieData->credits->cast[0];
				foreach ($movieData->credits->crew as $crew) {
					if ($crew->job == "Director") {
						$this->director = $crew;
					}
				}
			}else {
				$this->error['error'] = "Curl Error!";
				$this->throwError();
			}
		}
	}
	
 ?>