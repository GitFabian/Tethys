<?php

//Root-Verzeichnis ist das, in dem die start.php liegt:
define("ROOT_HDD_CORE",__DIR__);

//Globale Includes:
require_once ROOT_HDD_CORE."/page.php";
require_once ROOT_HDD_CORE."/tools/basic.php";
require_once ROOT_HDD_CORE."/settings.php";

class start{

	public static function init($pageId, $page_title){
		$page = self::standalone_init($pageId, $page_title);
		return $page;
	}

	public static function standalone_init($pageId, $page_title){

		global $page;

		if(file_exists(ROOT_HDD_CORE."/config.php")){

		}else{

			//Config-Datei ist nicht vorhanden -> Installer aufrufen
			include_once ROOT_HDD_CORE.'/config/install.php';
			install::create_config_file();

		}

		$page = new page($pageId, $page_title);

		return $page;
	}

}