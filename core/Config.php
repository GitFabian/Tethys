<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace core;

use inst\Install;

/**
 * Load Tethys configuration files and tables.
 * include_once ROOT_HDD_CORE."/core/Config.php";
 */
class Config {

	/**
	 * All config values that has been loaded from the database are stored here.
	 * The structure of the 3-level associative array is:
	 * @var array self::$core_config[module|core][user|0][key]
	 */
	private static $core_config = array();

	/**
	 * Called by @see Start::init.
	 * @param string $pageId
	 */
	public static function load_config($pageId) {
		self::load_hdd_config($pageId);
		self::load_db_basic();
	}

	/**
	 * Part of the installer
	 * @param string $message
	 */
	private static function create_config_link($message) {
		include_once ROOT_HDD_CORE . '/inst/core/Install.php';
		Install::create_config_link($message);
	}

	/**
	 * The file config_link.php holds a link to the configuration file.
	 * The configuration file (TCFGFILE) loads the first project specific settings.
	 * See: https://github.com/GitFabian/Tethys/blob/master/inst/core/tpl_config.php#L1
	 * @param string $pageId
	 */
	private static function load_hdd_config($pageId) {

		if (!file_exists(ROOT_HDD_CORE . '/config_link.php')) {
			self::create_config_link("No config link installed!");
		}

		include_once(ROOT_HDD_CORE . '/config_link.php');

		if (!defined("TCFGFILE"))
			self::create_config_link("Config link currupt!");

		if (!file_exists(TCFGFILE)) {
			include_once ROOT_HDD_CORE . '/inst/core/Install.php';
			Install::create_config_file();
		}

		include_once TCFGFILE;

	}

	/**
	 * Loads basic configuration from the DB into the cache (self::$core_config).
	 * If no config table is found, the installation process is triggered.
	 */
	private static function load_db_basic() {
		$data = Database::select("SELECT `key`,`value` FROM core_config WHERE `key` IN (
				'INDEX_TITLE',
				'SKIN',
				'EXTENSION'
			  );");
		if ($data === false) {
			$error_code = Database::get_error_code();
			if ($error_code == "42S02"/*Table 'TETHYSDB.core_config' doesn't exist*/) {
				include_once ROOT_HDD_CORE . '/inst/core/Install.php';
				Install::dbinit_2();
			}
			echo("Unbekannter Datenbank-Fehler! " . Database::get_error_msg());
			exit;
		}
		foreach ($data as $row) {
			self::$core_config["core"][0][$row["key"]] = $row["value"];
		}

		define("EXTENSION", self::$core_config["core"][0]["EXTENSION"]);
		define("EXT", EXTENSION);

		//SKIN_HTTP:
		$skinname = self::$core_config["core"][0]["SKIN"];
		if (substr($skinname, 0, 5) == "demo_") {
			define("SKIN_HTTP", ROOT_HTTP_CORE . "/demo/skins/".substr($skinname,5));
		} else {
			define("SKIN_HTTP", ""/*(Konstante für Skin-Verzeichnis noch nicht definiert) (TODO)*/);
		}

	}

	public static function get_core_value($id, $default_value = null, $user = null, $use_cache = true) {
		return self::get_value($id, null, $user, $default_value, $use_cache);
	}

	/**
	 * The default value is NOT cached (self::$core_config),
	 * so the next call of this function can return a different value.
	 * @param string      $id
	 * @param string|null $module
	 * @param int|null    $user
	 * @param mixed       $default_value
	 * @param bool        $use_cache
	 * @return string|mixed
	 */
	public static function get_value($id, $module = null, $user = null, $default_value = null, $use_cache = true) {
		if ($use_cache && isset(self::$core_config[$module ?: "core"][$user ?: 0][$id])) {
			return self::$core_config[$module ?: "core"][$user ?: 0][$id];
		}
		$where1 = "`module`" . ($module ? "='" . escape_sql($module) . "'" : " IS NULL");
		$where2 = "`user`" . ($user ? "='" . escape_sql($user) . "'" : " IS NULL");
		$data = Database::select_single("
				SELECT `value` FROM core_config WHERE `key`='" . escape_sql($id) . "' AND $where1 AND $where2;
			");
		if (empty($data)) {
			return $default_value;
		}
		$value = $data["value"];
		if ($use_cache) self::$core_config["core"][0][$id] = $value;
		return $value;
	}

	public static function set_core_value($id, $value, $user = null) {
		self::set_value($id, $value, null, $user);
	}

	public static function set_value($id, $value, $module = null, $user = null) {
		$where = array(
			"key" => $id,
			"module" => $module == "core" ? null : $module,
			"user" => $user
		);
		Database::update_or_insert("core_config", $where, array("value" => $value));
	}

}