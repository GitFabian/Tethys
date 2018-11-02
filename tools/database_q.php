<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Required by Database-Update-Scripts (e.g. tethys/inst/database.php) to run queries from the right version.
 * require_once ROOT_HDD_CORE.'/tools/database_q.php';
 */
require_once ROOT_HDD_CORE . "/core/Errors.php";
function q($ver, $query, $level = 0){
	$last_db_version = Config::get_core_value("DB_VERSION","0",false);

	//Check version:
	$new_version = $last_db_version+1;
	//Too new:
	if($ver>$new_version){
		Errors::die_hard("Datenbank-Versionsfolge ist verletzt!",$level+1);
	}
	//Next version:
	if($ver==$new_version){
		$result = Database::doexecute($query);
		if($result===false){
			Errors::die_hard("Datenbank-Update #$ver fehlgeschlagen! ".Database::get_error_msg(),$level+1);
		}
		//Update:
		Config::set_core_value("DB_VERSION", $ver);
	}

}