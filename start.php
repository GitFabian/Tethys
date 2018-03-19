<?php

//Root directory is where the start.php is:
define("ROOT_HDD_CORE",__DIR__);

//Global includes:
require_once ROOT_HDD_CORE."/page.php";
require_once ROOT_HDD_CORE."/tools/basic.php";
require_once ROOT_HDD_CORE."/config/settings.php";

class start{

	static private $global_page = null;

	public function get_global_page(){
		return self::$global_page;
	}

	public static function init($pageId, $page_title){

		define("USER_DEV",true);//TODO
		define("USER_ADMIN",true);//TODO

		if(!file_exists(ROOT_HDD_CORE."/config/dbconfig.php")){
			//Call installer if config file is not existing
			include_once ROOT_HDD_CORE.'/config/install.php';
			install::create_config_file();
		}


		self::$global_page = new page($pageId, $page_title);

		return self::$global_page;
	}

}