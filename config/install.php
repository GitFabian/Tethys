<?php

class install{

	public static function create_config_file(){
		include_once(ROOT_HDD_CORE.'/classes/Form.php');
		$page = new page("installer_config", "Installation of Tethys");

		if(request_cmd("save"))self::save_config($page);

		/*
		 * Input form for dbconfig.php
		 */
		$form=new Form("","Speichern","save");
		$form->add_field(new Formfield_select("db_type","Engine",array("mysql"=>"MySQL","b"=>"c")));
		$form->add_field(new Formfield_text("server_addr","Server","localhost"));
		$form->add_field(new Formfield_text("db_name","Name","tethys"));
		$form->add_field($ff=new Formfield_text("username","Benutzer"));
			$ff->tooltip="Der Benutzer muss über Rechte fürs Anlegen von Datenbanken und Tabellen verfügen";
		$form->add_field(new Formfield_password("dbpass","Passwort"));
		$page->addHtml($form);

		$page->send_and_quit();
	}

	public static function save_config(page $page){
		//TODO:Save file
		$page->addMessageConfirm("OK!");
		$page->send_and_quit();
	}

}
