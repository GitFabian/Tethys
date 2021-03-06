<?php
/**TPLDOCSTART
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 *
 * This template is used by the installer ( @see Install::save_config ) to create the configuration file
 * (location specified in the file "config_link.php" (default: directory above Tethys root folder)).
 * TPLDOCEND*/
/**
 * This file was created by the Tethys installer using the template tpl_config.php.
 * It should be part of your project's repository.
 * Is is called by the routine @see Config::load_hdd_config.
 */

//Name of the Tethys database:
define('TETHYSDB', ':db_name');

//Creation of the main database connection:
\core\Database::set_main_connection(new \core\Database(":server_addr", TETHYSDB, ":username", ":dbpass"));

define("ROOT_HTTP_CORE", ':ROOT_HTTP_CORE');
