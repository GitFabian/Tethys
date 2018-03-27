<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Load Tethys configuration files and tables.
 * include_once ROOT_HDD_CORE."/core/Config.php";
 */
class Config {

	/**
	 * Called by @see Start::init.
	 * @param string $pageId
	 */
	public static function load_config($pageId) {
		self::load_hdd_config($pageId);
	}

	/**
	 * Part of the installer
	 * @param string $message
	 */
	private static function create_config_link($message) {
		include_once ROOT_HDD_CORE . '/inst/Install.php';
		Install::create_config_link($message);
	}

	/**
	 * The file config_link.php holds a link to the configuration file.
	 * The configuration file (TCFGFILE) loads the first project specific settings.
	 * See: https://raw.githubusercontent.com/GitFabian/Tethys/master/inst/tpl_config.php
	 * @param string $pageId
	 */
	private static function load_hdd_config($pageId) {

		if (!file_exists(ROOT_HDD_CORE . '/config_link.php')) {
			self::create_config_link("No config link installed!");
		}

		include_once(ROOT_HDD_CORE . '/config_link.php');

		if (!defined("TCFGFILE"))
			self::create_config_link("Config link currupt!");

		if (!file_exists(TCFGFILE)){
			include_once ROOT_HDD_CORE . '/inst/Install.php';
			Install::create_config_file();
		}

		include_once TCFGFILE;

	}

	public static function get_core_value($id) {

	}

}