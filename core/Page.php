<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace core;
use tools\T_Arrays;

require_once ROOT_HDD_CORE."/tools/T_Arrays.php";

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

	private $stylesheets = array();

	private $javascripts = array();
	public $inline_js = "";

	/**
	 * Page constructor.
	 * There can be only one page, stored in the static $global_page. To change the current page use Page::reset.
	 * @see Page::reset
	 * @param string $id
	 * @param string $title
	 */
	private function __construct($id, $title) {
		$this->reset($id, $title);
	}

	public function add_stylesheet($url, $media = null) {
		$this->stylesheets[$url] = $media;
	}

	public function add_javascript($url) {
		$this->javascripts[$url] = true;
	}

	public function get_id() {
		return $this->id;
	}

	public static function get_global_page() {
		return self::$global_page;
	}

	/**
	 * Singleton self::$global_page is set here. This function is called by the class Start.
	 * @see Start
	 * @param string $pageId
	 * @param string $page_title
	 */
	public static function init($pageId, $page_title) {
		if (self::$global_page === null) {
			self::$global_page = new Page($pageId, $page_title);
		} else {
			self::$global_page->exit_with_error("Invalid page initialization! Please call reset instead!");
		}
	}

	/**
	 * A page usually calls Page::init in the very beginning. Some core features like installation or login
	 * are called later but "overwrite" the requested page. This function updates the pageId and title.
	 * @see Page::init
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
	 * @deprecated Use "add" instead.
	 * @param string $html
	 */
	public function addHtml($html) {
		$this->inner_body .= $html;
	}

	/**
	 * Echo HTML to the page body.
	 * TODO:Add Message
	 * @param string|Html $anything
	 */
	public function add($anything) {
		$this->inner_body .= $anything;
	}

	/**
	 * Adds a HTML div element to the page.
	 * @param string $html
	 * @param array  $params
	 */
	public function addHtmlDiv($html, $params = array()) {
		$this->inner_body .= new Html("div",$html,$params);
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
		$css_links = T_Arrays::merge_assoc_greedy(array(SKIN_HTTP . '/screen.css' => null), $this->stylesheets);
		$css_html = "";
		foreach ($css_links as $url => $media) {
			if($media===null){
				$media="all";
			}
			$css_html .= "<link href=\"$url\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\"/>\n";
		}

		/*
		 * JS
		 */
		$js_links = T_Arrays::merge_assoc_greedy(array(ROOT_HTTP_CORE."/tools/tethys.js"=>true),$this->javascripts);
		$js_html = "";
		foreach ($js_links as $url => $dummy) {
			$js_html .= "<script type=\"text/javascript\" src=\"$url\"></script>\n";
		}
		if ($this->inline_js){
			$js_html.= /** @lang text */"<script>$this->inline_js</script>\n";
		}

		// @formatter:off
		echo "<!DOCTYPE html>"
			."<html>\n"
				."<head>\n"
					."<meta charset=\"UTF-8\">\n"
					."<title>$title</title>\n"
					.$css_html
					.$js_html
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