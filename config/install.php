<?php

class install{

	public static function create_config_file(){
		$page = new page("installer_config", "Installation of Tethys");
		$page->addHtml("Create config file");//TODO
		$page->send_and_quit();
	}

}
