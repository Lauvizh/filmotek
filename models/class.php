<?php

Class LastMoviesByGenre{
	protected $category;
	public $list;
	public $errors;

	public function __construct($category){
		$this->category = $category;
		$this->list = array();
		$this->errors = '';
		try {
			$l = getLastAdd($category);
		} catch (Exception $e) {
			$this->errors = $e->getMessage();
		}
		if ($this->errors == '') {
			foreach ($l as $k => $movie) {
			$this->list[] = new MovieShort($movie);
			}
		}

	}

}
Class MovieShort{
	public $id;
	public $title;
	public $date_add;

	public function __construct($infos){
		$this->id = $infos['id'];
		$this->title = $infos['title'];
		$this->date_add = $infos['date_add'];
	}
}
Class MovieDetails{
	public $id;
	public $title;
	public $title_orig;
	public $category;
	public $genre;
	public $country;
	public $year;
	public $duration;
	public $description;
	public $actors;
	public $directors;
	public $media_location;
	public $media_language;
	public $date_add;
	public $date_update;
	public $trailer_path;

	public $errors;

	public function __construct($id){
		$this->id = $id;
		$this->errors = '';
		try {
			$infos = getMovieInfos($id);
		} catch (Exception $e) {
			$this->errors = $e->getMessage();
		}
		if ($this->errors == '') {
			foreach ($infos as $k => $info) {
				$this->$k = $info;
			}
		}
	}


}