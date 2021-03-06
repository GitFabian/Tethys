<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace inst;

use core\Config;
use core\Form;
use core\Formfield_password;
use core\Formfield_select;
use core\Formfield_text;
use core\Page;
use core\UpdateDB;

require_once ROOT_HDD_CORE . "/inst/core/UpdateDB.php";
require_once ROOT_HDD_CORE . "/tools/T_Debug.php";

/**
 * Installation of Tethys
 * ======================
 * http://gitfabian.github.io/Tethys/install.html
 * After cloning the repository please run index.php from your browser. You will be guided through the installation.
 *
 * Technical details
 * =================
 * Step 1: Create file with link to the config.
 *         index.php calls Start::init calls Config::load_config calls Config::load_hdd_config.
 *         Before loading the config this routine recognizes missing of the file config_link.php and calls
 *         Install::create_config_link.
 * Step 2: Create config file.
 *         Function Config::load_hdd_config detects missing config file and calls
 *         Install::create_config_file.
 * Step 3: Creation of the database.
 *         Configuration file (template: tpl_config.php) calls new Database calls new PDO with name of not-yet-existing
 *         Tethys database thus throws error handled in Database::fehler_beim_pdo_erstellen, which in case of a
 *         1049 ("Unknown database") calls Install::dbinit.
 * Next step is the initialization of the Database that is done by the Config.
 *
 * @see Install::create_config_link
 * @see Config::load_hdd_config
 * @see Install::create_config_file
 * @see Database::fehler_beim_pdo_erstellen
 * @see Install::dbinit
 * @see Config
 *
 * include_once ROOT_HDD_CORE . '/inst/core/Install.php';
 */
class Install {

	/**
	 * First call (no cmd set): Shows a form prompting for the path to the configuration file.
	 * Second call (cmd value set): @see Install::save_config_link
	 * @param $message string
	 */
	public static function create_config_link($message) {
		$page = self::initialize_install("installer_config_link");
		include_once(ROOT_HDD_CORE . '/classes/Form.php');

		if (request_cmd("cmd_save_cfglink")) self::save_config_link($page, request_value("cfglink"));

		$page->addMessageError($message);

		$cfgfile_proposal = dirname(ROOT_HDD_CORE) . '/tethys_cfg.php';

		$form = new Form("", "Speichern", "cmd_save_cfglink");
		$form->add_field(new Formfield_text("cfglink", "Konfigurationsdatei", $cfgfile_proposal));

		$page->addDiv("Bitte geben Sie den Pfad zur Konfigurationsdatei an:");
		$page->addHtml($form);

		$page->send_and_quit();
	}

	/**
	 * Saves file config_link.php (generated from the template tpl_cfglink.php) containing the path
	 * to the configuration file.
	 * @param Page   $page
	 * @param string $link
	 */
	private static function save_config_link(Page $page, $link) {

		//Load template
		$template = template_load(ROOT_HDD_CORE . "/inst/core/tpl_cfglink.php", array(
			":cfglink" => escape_value_bs($link),
		));

		//Write config-link file
		file_save(ROOT_HDD_CORE . "/config_link.php", $template);

		$page->addMessageConfirm("Datei config_link.php erfolgreich gespeichert.");
		$page->addHtml(html_a_button($_SERVER['SCRIPT_NAME'], "Weiter"));
		$page->send_and_quit();
	}

	/**
	 * Is used to set the skin and page title for installation routine, which is called before the setting of these
	 * config values.
	 * @param $page_id
	 * @return Page
	 */
	public static function initialize_install($page_id) {

		//Installation needs to be called from the index-page:
		if (Page::get_global_page()->get_id() != "core_index") {
			echo "Projekt nicht initialisiert. Bitte rufen Sie die Index-Seite auf.";
			exit;
		}

		//Set page title:
		$page = Page::get_global_page()->reset($page_id, "Installation of Tethys");

		//Set skin.
		//Works only from the root directory:
		if (!defined("SKIN_HTTP")) define("SKIN_HTTP", "demo/skins/synergy");

		//Set to true, if you're developing the installation routine
		if (!defined("USER_DEV")) define("USER_DEV", false);

		return $page;
	}

	/**
	 * First call (no cmd set): Shows a form prompting for all relevant data for the config file.
	 * Second call (cmd value set): @see Install::save_config
	 */
	public static function create_config_file() {
		$page = self::initialize_install("installer_config");
		include_once(ROOT_HDD_CORE . '/core/Form.php');

		if (request_cmd("cmd_save_installer")) self::save_config($page);

		/*
		 * Input form for config file
		 */
		$form = new Form("", "Speichern", "cmd_save_installer");
		$form->add_field(new Formfield_select("db_type", "Engine", array("mysql" => "MySQL")));
		$form->add_field(new Formfield_text("server_addr", "Server", "localhost"));
		$form->add_field(new Formfield_text("db_name", "Name", "tethys"));

		$form->add_field($ff = new Formfield_text("username", "Benutzer", "root"));
		#$ff->tooltip = "Der Benutzer muss über Rechte fürs Anlegen von Tabellen verfügen";

		$form->add_field(new Formfield_password("dbpass", "Passwort"));

		$http_root = $_SERVER["SCRIPT_URL"];
		$http_root = preg_replace("/\\/$/", "", $http_root);
		$form->add_field(new Formfield_text("ROOT_HTTP_CORE", "HTTP-Root", $http_root));

		$page->addHtml($form);

		$page->send_and_quit();
	}

	/**
	 * Saves config file (generated from the template SEE tpl_config.php).
	 * @param Page $page
	 */
	private static function save_config(Page $page) {

		if (file_exists(TCFGFILE)) {
			Page::get_global_page()->exit_with_error("Config-Datei kann nicht überschrieben werden!");
		}

		//Load template
		$template = template_load(ROOT_HDD_CORE . "/inst/core/tpl_config.php", array(
			":db_type" => escape_value_bs(request_value("db_type")),
			":server_addr" => escape_value_bs(request_value("server_addr")),
			":db_name" => escape_value_bs(request_value("db_name")),
			":username" => escape_value_bs(request_value("username")),
			":dbpass" => escape_value_bs(request_value("dbpass")),
			":ROOT_HTTP_CORE" => escape_value_bs(request_value("ROOT_HTTP_CORE")),
		));

		file_save(TCFGFILE, $template);

		$page->addMessageConfirm("Konfigurationsdatei erfolgreich gespeichert.");
		$page->addHtml(html_a_button($_SERVER['SCRIPT_NAME'], "Weiter"));
		$page->send_and_quit();
	}

	/**
	 * Creates Tethys database
	 * First call (no cmd set): Returns a form prompting for username and password.
	 * Second call (cmd value set): Creates database.
	 *
	 * @return string
	 */
	public static function dbinit_1() {

		//Datenbank-Initialisierung:
		if (request_cmd("dodbinit")) {
			try {
				$dbh = new \PDO("mysql:host=" . request_value("server_addr"), request_value("username"), request_value("dbpass"));

				$dbh->exec("CREATE DATABASE `" . TETHYSDB . "`;") or die(print_r($dbh->errorInfo(), true));

			} catch (\PDOException $e) {
				die("DB ERROR: " . $e->getMessage());
			}

			$page = Page::get_global_page();
			$page->addMessageConfirm("Datenbank erstellt!");
			$page->addHtml(html_a_button($_SERVER['SCRIPT_NAME'], "Weiter"));
			$page->send_and_quit();
		}

		//Eingabe von Benutzername und Passwort:
		include_once(ROOT_HDD_CORE . '/classes/Form.php');
		$form = new Form("", "Absenden", "dodbinit");
		$form->add_field(new Formfield_text("server_addr", "Server", "localhost"));
		$form->add_field(new Formfield_text("username", "Username", "root"));
		$form->add_field(new Formfield_password("dbpass", "Password"));
		return "<h2>Datenbank anlegen</h2>" . $form;
	}

	/**
	 * Runs the first basic database queries.
	 * Started by @see Config::load_db_basic (when no config table is found)
	 */
	public static function dbinit_2() {

		/*
		 * Build database up to the latest version
		 */
		$updater = new UpdateDB();
		$updater->update();

		$page = Page::get_global_page();
		$page->addMessageInfo("Datenbank wurde initialisiert!");
		$page->addHtml(html_a_button($_SERVER['SCRIPT_NAME'], "Weiter"));
		$page->send_and_quit();
	}

}
