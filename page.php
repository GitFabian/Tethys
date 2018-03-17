<?php

class page{

	private $id;
	private $title;

	private $inner_body="";

	public function __construct($id, $title) {
		$this->id=$id;
		$this->title=$title;
	}

	public function addHtml($html){
		$this->inner_body.=$html;
	}

	public function send_and_quit(){

		$title = $this->title;

		$body = $this->inner_body;

		echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>$title</title>\n</head><body>$body</body></html>";
		exit;
	}

}