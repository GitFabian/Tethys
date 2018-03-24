<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

//Root directory is where the start.php is:
define("ROOT_HDD_CORE", __DIR__);

//Global includes:
require_once ROOT_HDD_CORE . "/core/Config.php";
require_once ROOT_HDD_CORE . "/core/Database.php";
require_once ROOT_HDD_CORE . "/core/Page.php";
require_once ROOT_HDD_CORE . "/core/Settings.php";
require_once ROOT_HDD_CORE . "/tools/basic.php";

class Start {

	public static function init($pageId, $page_title) {

		//Load page:
		Page::init($pageId, $page_title);

		//Load config:
		Config::load_config($pageId);

		return Page::get_global_page();
	}

}