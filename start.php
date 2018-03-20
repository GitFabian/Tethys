<?php

//Root directory is where the start.php is:
define("ROOT_HDD_CORE",__DIR__);

//Global includes:
require_once ROOT_HDD_CORE."/core/page.php";
require_once ROOT_HDD_CORE."/tools/basic.php";
require_once ROOT_HDD_CORE."/core/settings.php";

class start{


	public static function init($pageId, $page_title){
		page::init($pageId, $page_title);

		define("USER_DEV",true);//TODO
		define("USER_ADMIN",true);//TODO

		//Load config:
		include_once ROOT_HDD_CORE."/core/config.php";
		config::load_config();

		return page::get_global_page();
	}

}