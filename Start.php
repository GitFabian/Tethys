<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

//Root directory is where the Start.php is:
define("ROOT_HDD_CORE", __DIR__);

/**
 * Class Start
 * Holds the static function init, which loads the Tethys framework.
 * Code example:
 * <code>
 * require_once 'Start.php';
 * $page = Start::init("pageId", "Page title");
 * $page->addHtml("Hello World");
 * $page->send_and_quit();
 * </code>
 */
class Start {

	public static function init($pageId, $page_title) {

		//Global toolbox:
		require_once ROOT_HDD_CORE . "/tools/basic.php";

		//Load page:
		require_once ROOT_HDD_CORE . "/core/Page.php";
		\core\Page::init($pageId, $page_title);

		//Load config:
		require_once ROOT_HDD_CORE . "/core/Database.php";
		require_once ROOT_HDD_CORE . "/core/Config.php";
		\core\Config::load_config($pageId);

		return \core\Page::get_global_page();
	}

}