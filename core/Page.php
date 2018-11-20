<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace core;
/**
 * Class Page
 * Read about this concept: http://gitfabian.github.io/Tethys/pages.html
 * This class represents the HTML-page that is returned to browser in the end.
 * The static $global_page is set on initialization (called by @see Start::init ).
 */
class Page {

	/**
	 * @var string Unique string used to address page in navigation and CSS.
	 */
	private $id;
	/**
	 * @var string The HTML title tag value.
	 */
	private $title;

	private $inner_body = "";
	private $messages = array();

	/** @var Page */
	static private $global_page = null;

	/**
	 * Page constructor.
	 * There can be only one page, stored in the static $global_page. To change the current page use @see Page::reset.
	 * @param string $id
	 * @param string $title
	 */
	private function __construct($id, $title) {
		$this->reset($id, $title);
	}

	public function get_id() {
		return $this->id;
	}

	public static function get_global_page() {
		return self::$global_page;
	}

	/**
	 * Kind of a constructor.
	 * @param string $pageId
	 * @param string $page_title
	 */
	public static function init($pageId, $page_title) {
		if (self::$global_page === null) {
			self::$global_page = new page($pageId, $page_title);
		} else {
			self::$global_page->exit_with_error("Invalid page initialization! Please call reset instead!");
		}
	}

	/**
	 * A page usually calls @see Page::init in the very beginning. Some core features like installation or login
	 * are called later but "overwrite" the requested page. This function updates the pageId and title.
	 * @param string $pageId
	 * @param string $title
	 * @return Page
	 */
	public function reset($pageId, $title) {
		$this->id = $pageId;
		$this->title = $title;
		return self::$global_page;
	}

	/**
	 * The "echo" function.
	 * @param string $html
	 */
	public function addHtml($html) {
		$this->inner_body .= $html;
	}

	/**
	 * To markup the added text with some CSS, JS or whatever this function can be used.
	 * @param string $html
	 * @param array  $params
	 */
	public function addDiv($html, $params = array()) {
		$this->inner_body .= "<div " . html_tag_keyValues($params) . ">$html</div>";
	}

	/**
	 * This private function is called by the three message types "confirm", "info" and "error".
	 * @param string $class
	 * @param string $html
	 */
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

	/**
	 * Displays only an error message instead of the page and quits.
	 * @param string $msg
	 */
	public function exit_with_error($msg) {
		$this->messages = array();
		$this->addMessageError($msg);
		$this->inner_body = "";
		$this->send_and_quit();
	}

	/**
	 * Builds and sends the HTML page.
	 */
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
			$messages = "<div class='messages'>\n\t"
				. implode("\n\t", $this->messages) . "\n"
				. "</div>";
		}

		/*
		 * CSS
		 */
		$css_links = array(SKIN_HTTP . '/screen.css' => "all");
		$css_html = "";
		foreach ($css_links as $url => $media) {
			$css_html .= "<link href=\"$url\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\"/>\n";
		}

		// @formatter:off
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
		// @formatter:on

		exit;
	}

}