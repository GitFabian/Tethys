<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

/**
 * In this file the main database is built.
 * After each query a version number is stored, so you can update to the newest version from every state of your
 * database.
 *
 * require_once ROOT_HDD_CORE . "/inst/core/UpdateDB.php";
 */

namespace core;

require_once ROOT_HDD_CORE . "/core/UpdaterDB.php";

class UpdateDB extends UpdaterDB {

	protected $module = "core";

	/**
	 * Called by Install::dbinit_2 to initialize Tethys Core Database
	 * @see Install::dbinit_2
	 * @inheritdoc
	 */
	protected function do_update() {

		$this->q("1", "
			CREATE TABLE `core_config` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `key` VARCHAR(20) COLLATE utf8_bin NOT NULL,
			  `module` VARCHAR(20) COLLATE utf8_bin DEFAULT NULL,
			  `user` INT(11) DEFAULT NULL,
			  `value` TEXT COLLATE utf8_bin NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `user` (`user`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
		");

		//Insert first config values
		$this->q("2", "
			INSERT INTO `core_config` (`key`, `module`, `user`, `value`) VALUES
			('INDEX_TITLE', NULL, NULL, 'MyTethys'),
			('SKIN', NULL, NULL, 'demo_synergy');
		");

		//
//		$this->q("", "
//		");

		/*
		 * v0.3
		 */

		return true;
	}
}