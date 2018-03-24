<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

class Page {

	private $id;
	private $title;

	private $inner_body = "";
	private $messages = array();

	/** @var Page */
	static private $global_page = null;

	private function __construct($id, $title) {
		$this->reset($id, $title);
	}

	public function get_id() {
		return $this->id;
	}

	public static function get_global_page() {
		return self::$global_page;
	}

	public static function init($pageId, $page_title) {
		if (self::$global_page === null) {
			self::$global_page = new page($pageId, $page_title);
		} else {
			self::$global_page->exit_with_error("Invalid page initialization! Please call reset instead!");
		}
	}

	public function reset($pageId, $title) {
		$this->id = $pageId;
		$this->title = $title;
		return self::$global_page;
	}

	public function addHtml($html) {
		$this->inner_body .= $html;
	}

	public function addDiv($html, $params = array()) {
		$this->inner_body .= "<div " . html_tag_keyValues($params) . ">$html</div>";
	}

	private function addMessage($class, $html) {
		$this->messages[] = "<div class='message $class'>$html</div>";
	}

	public function addMessageInfo($html) {
		$this->addMessage('msg_info', $html);
	}

	public function addMessageConfirm($html) {
		$this->addMessage('msg_confirm', $html);
	}

	public function addMessageError($html) {
		$this->addMessage('msg_error', $html);
	}

	public function exit_with_error($msg) {
		$this->messages = array();
		$this->addMessageError($msg);
		$this->inner_body = "";
		$this->send_and_quit();
	}

	public function send_and_quit() {

		/*
		 * Page title
		 */
		$title = $this->title;

		/*
		 * Messages
		 */
		$messages = "";
		if ($this->messages) {
			//@formatter:off
			$messages = "<div class='messages'>\n\t"
					. implode("\n\t", $this->messages) . "\n"
				. "</div>";
			//@formatter:on
		}

		/*
		 * CSS
		 */
		$css_links = array(SKIN_HTTP . '/screen.css' => "all");
		$css_html = "";
		foreach ($css_links as $url => $media) {
			$css_html .= "<link href=\"$url\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\"/>\n";
		}

		//@formatter:off
		echo "<!DOCTYPE html>"
			."<html>\n"
				."<head>\n"
					."<meta charset=\"UTF-8\">\n"
					."<title>$title</title>\n"
					.$css_html
				."</head>\n"
				."<body id='$this->id'><div class='body_outer'>\n"
					.$messages
					."<div class='body_inner'>$this->inner_body</div>"
				."</div></body>"
			."</html>";
		//@formatter:on

		exit;
	}

}