<?php

//Root directory is where the start.php is:
define("ROOT_HDD_CORE",__DIR__);

//Global includes:
require_once ROOT_HDD_CORE."/core/config.php";
require_once ROOT_HDD_CORE."/core/page.php";
require_once ROOT_HDD_CORE."/core/settings.php";
require_once ROOT_HDD_CORE."/tools/basic.php";

class start{

	public static function init($pageId, $page_title){

		//Load page:
		page::init($pageId, $page_title);

		//Load config:
		config::load_config($pageId);

		return page::get_global_page();
	}

}