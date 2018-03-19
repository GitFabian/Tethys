<?php

class page{

	public static $MESSAGETYPE_CONFIRM = 'confirm';
	public static $MESSAGETYPE_ERROR = 'error';
	public static $MESSAGETYPE_INFO = 'info';

	private $id;
	private $title;

	private $inner_body="";
	private $messages = array();

	public function __construct($id, $title) {
		$this->id=$id;
		$this->title=$title;
	}

	public function addHtml($html){
		$this->inner_body.=$html;
	}

	public function addMessage($messagetype, $html){
		$this->messages[]="<div class='message msg_$messagetype'>$html</div>";
	}
	public function addMessageInfo($html){
		$this->addMessage(self::$MESSAGETYPE_INFO, $html);
	}
	public function addMessageConfirm($html){
		$this->addMessage(self::$MESSAGETYPE_CONFIRM, $html);
	}
	public function addMessageError($html){
		$this->addMessage(self::$MESSAGETYPE_ERROR, $html);
	}

	public function send_and_quit(){

		/*
		 * Page title
		 */
		$title = $this->title;

		/*
		 * Messages
		 */
		$messages="";
		if($this->messages){
			$messages="<div class='messages'>\n\t"
					.implode("\n\t",$this->messages)."\n"
				."</div>";
		}

		echo "<!DOCTYPE html>"
			."<html>\n"
				."<head><meta charset=\"UTF-8\"><title>$title</title></head>\n"
				."<body id='$this->id'><div class='body_outer'>\n"
					.$messages
					."<div class='body_inner'>$this->inner_body</div>"
				."</div></body>"
			."</html>";
		exit;
	}

}