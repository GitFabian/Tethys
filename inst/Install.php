<?php
/**
 * Installation of Tethys
 * ======================
 * After cloning the repository please run index.php from your browser. You will be guided through the installation.
 *
 * Technical details
 * =================
 * Step 1: Create file with link to the config.
 *         index.php calls Start::init calls Config::load_config calls Config::load_hdd_config.
 *         Before loading the config this routine recognizes missing of the file config_link.php and calls
 *         @see Install::create_config_link.
 * Step 2: Create config file.
 *         Function @see Config::load_hdd_config detects missing config file and calls
 *         @see Install::create_config_file.
 * Step 3: Creation of the database.
 *         Configuration file (template: tpl_config.php) calls new Database calls new PDO with name of not-yet-existing
 *         Tethys database thus throws error handled in @see Database::fehler_beim_pdo_erstellen, which in case of a
 *         1049 ("Unknown database") calls @see Install::dbinit.
 * Next step ist the initialization of the Database that is done by the @see Config.
 *
 * include_once ROOT_HDD_CORE . '/inst/Install.php';
 */
class Install {

	/**
	 * First call (no cmd set): Shows a form prompting for the path to the configuration file.
	 * Second call (cmd value set): @see Install::save_config_link
	 * @param $message string
	 */
	public static function create_config_link($message) {
		$page=self::initialize_install("installer_config_link");
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
		$template = template_load(ROOT_HDD_CORE . "/inst/tpl_cfglink.php", array(
			":cfglink" => escape_value_bs($link),
		));

		//Write config-link file
		//TODO (s.u.)
		$file = fopen(ROOT_HDD_CORE . "/config_link.php", "w");
		$success = false;
		if ($file !== false) {
			$success = fwrite($file, $template);
			fclose($file);
		}
		if ($success === false) {
			$page->exit_with_error("Speichern der config-link-Datei fehlgeschlagen!");
		}

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
		$page=Page::get_global_page()->reset($page_id, "Installation of Tethys");

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
		$page=self::initialize_install("installer_config");
		include_once(ROOT_HDD_CORE . '/classes/Form.php');

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
		$template = template_load(ROOT_HDD_CORE . "/inst/tpl_config.php", array(
			":db_type" => escape_value_bs(request_value("db_type")),
			":server_addr" => escape_value_bs(request_value("server_addr")),
			":db_name" => escape_value_bs(request_value("db_name")),
			":username" => escape_value_bs(request_value("username")),
			":dbpass" => escape_value_bs(request_value("dbpass")),
		));

		//TODO:Funktion fürs Speichern von Dateien mit Fehlerabfrage
		$file = fopen(TCFGFILE, "w");
		$success = false;
		if ($file !== false) {
			$success = fwrite($file, $template);
			fclose($file);
		}
		if ($success === false) {
			$page->exit_with_error("Speichern der config-Datei fehlgeschlagen!");
		}

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
	public static function dbinit(){

		//Datenbank-Initialisierung:
		if(request_cmd("dodbinit")){
			try {
				$dbh = new PDO("mysql:host=".request_value("server_addr"), request_value("username"), request_value("dbpass"));

				$dbh->exec("CREATE DATABASE `".TETHYSDB."`;") or die(print_r($dbh->errorInfo(), true));

			} catch (PDOException $e) {
				die("DB ERROR: ". $e->getMessage());
			}

			$page=Page::get_global_page();
			$page->addMessageConfirm("Datenbank erstellt!");
			$page->addHtml(html_a_button($_SERVER['SCRIPT_NAME'], "Weiter"));
			$page->send_and_quit();
		}

		//Eingabe von Benutzername und Passwort:
		include_once(ROOT_HDD_CORE.'/classes/Form.php');
		$form=new Form("","Absenden","dodbinit");
		$form->add_field(new Formfield_text("server_addr","Server","localhost"));
		$form->add_field(new Formfield_text("username","Username","root"));
		$form->add_field(new Formfield_password("dbpass","Password"));
		return "<h2>Datenbank anlegen</h2>".$form;
	}

}
