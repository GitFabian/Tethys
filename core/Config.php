<?php
/**
 * Load Tethys configuration files and tables.
include_once ROOT_HDD_CORE."/core/Config.php";
 */

class Config{

	public static function load_config($pageId){
		self::load_hdd_config($pageId);
	}

	private static function create_config_link($message){
		include_once ROOT_HDD_CORE . '/inst/Install.php';
		Install::create_config_link($message);
	}

	private static function create_config_file(){
		include_once ROOT_HDD_CORE . '/inst/Install.php';
		Install::create_config_file();
	}

	private static function load_hdd_config($pageId){

		if(!file_exists(ROOT_HDD_CORE.'/config_link.php')){
			self::create_config_link("No config link installed!"); }

		include_once(ROOT_HDD_CORE.'/config_link.php');

		if(!defined("TCFGFILE"))
			self::create_config_link("Config link currupt!");

		if(!file_exists(TCFGFILE))
			self::create_config_file();

	}

}