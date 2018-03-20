<?php

//Root directory is where the start.php is:
define("ROOT_HDD_CORE",__DIR__);

//Global includes:
require_once ROOT_HDD_CORE . "/core/Config.php";
require_once ROOT_HDD_CORE . "/core/Page.php";
require_once ROOT_HDD_CORE . "/core/Settings.php";
require_once ROOT_HDD_CORE."/tools/basic.php";

class Start{

	public static function init($pageId, $page_title){

		//Load page:
		Page::init($pageId, $page_title);

		//Load config:
		Config::load_config($pageId);

		return Page::get_global_page();
	}

}