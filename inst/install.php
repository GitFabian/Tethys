<?php

class install{

	public static function create_config_link($message){
		self::initialize_standalone();
		include_once(ROOT_HDD_CORE.'/classes/Form.php');
		$page = page::get_global_page()->reset("installer_config_link", "Installation of Tethys");

		if(request_cmd("cmd_save_cfglink"))self::save_config_link($page, request_value("cfglink"));

		$page->addMessageError($message);

		$cfgfile_proposal=dirname(ROOT_HDD_CORE).'/tethys_cfg.php';

		$form=new Form("","Speichern","cmd_save_cfglink");
		$form->add_field(new Formfield_text("cfglink","Konfigurationsdatei",$cfgfile_proposal));

		$page->addDiv("Bitte geben Sie den Pfad zur Konfigurationsdatei an:");
		$page->addHtml($form);

		$page->send_and_quit();
	}

	public static function initialize_standalone(){
		if(!defined("SKIN_HTTP"))define("SKIN_HTTP","demo/skins/synergy");//<= Works only from the root directory, which we should be in when installing
	}

	public static function create_config_file(){
		self::initialize_standalone();
		include_once(ROOT_HDD_CORE.'/classes/Form.php');
		$page = page::get_global_page()->reset("installer_config_link", "Installation of Tethys");

		if(request_cmd("cmd_save_installer"))self::save_config($page);

		/*
		 * Input form for config file
		 */
		$form=new Form("","Speichern","cmd_save_installer");
		$form->add_field(new Formfield_select("db_type","Engine",array("mysql"=>"MySQL")));
		$form->add_field(new Formfield_text("server_addr","Server","localhost"));
		$form->add_field(new Formfield_text("db_name","Name","tethys"));
		$form->add_field($ff=new Formfield_text("username","Benutzer"));
			$ff->tooltip="Der Benutzer muss über Rechte fürs Anlegen von Datenbanken und Tabellen verfügen";
		$form->add_field(new Formfield_password("dbpass","Passwort"));
		$page->addHtml($form);

		$page->send_and_quit();
	}

	public static function save_config_link(page $page, $link){

		//Load template
		$template = template_load(ROOT_HDD_CORE."/inst/tpl_cfglink.php",array(
			":cfglink"=>escape_value_bs($link),
		));

		//Write config-link file
		$file = fopen(ROOT_HDD_CORE."/config_link.php","w");
		$success=false;
		if($file!==false){
			$success = fwrite($file,$template);
			fclose($file);
		}
		if($success===false){
			$page->exit_with_error("Speichern der config-link-Datei fehlgeschlagen!");
		}

		$page->addMessageConfirm("Datei config_link.php erfolgreich gespeichert.");
		$page->addHtml(html_a_button($_SERVER['HTTP_REFERER'],"Weiter"));
		$page->send_and_quit();
	}

	public static function save_config(page $page){

		if(file_exists(TCFGFILE)){ page::get_global_page()->exit_with_error("Config-Datei kann nicht überschrieben werden!"); }

		//Load template
		$template = template_load(ROOT_HDD_CORE."/inst/tpl_config.php",array(
			":db_type"=>escape_value_bs(request_value("db_type")),
			":server_addr"=>escape_value_bs(request_value("server_addr")),
			":db_name"=>escape_value_bs(request_value("db_name")),
			":username"=>escape_value_bs(request_value("username")),
			":dbpass"=>escape_value_bs(request_value("dbpass")),
		));

		//TODO:Funktion fürs Speichern von Dateien mit Fehlerabfrage
		$file = fopen(TCFGFILE,"w");
		$success=false;
		if($file!==false){
			$success = fwrite($file,$template);
			fclose($file);
		}
		if($success===false){
			$page->exit_with_error("Speichern der config-Datei fehlgeschlagen!");
		}

		$page->addMessageConfirm("Konfigurationsdatei erfolgreich gespeichert.");
		$page->addHtml(html_a_button($_SERVER['HTTP_REFERER'],"Weiter"));

		self::initialize_standalone();
		$page->send_and_quit();
	}

}
