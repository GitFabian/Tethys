<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 *
 */
namespace core;
require_once ROOT_HDD_CORE . "/core/Errors.php";

abstract class UpdaterDB {

	protected $module = null;

	protected function q($ver, $query){
		$last_db_version = Config::get_value("DB_VERSION",$this->module=="core"?null:$this->module,0,null,false);

		//Check version:
		$new_version = $last_db_version+1;

		//Too new:
		if($ver>$new_version){
			Errors::die_hard("Datenbank-Versionsfolge ist verletzt!",1);
		}

		//Next version:
		if($ver==$new_version){
			$result = Database::doexecute($query);
			if($result===false){
				Errors::die_hard("Datenbank-Update #$ver fehlgeschlagen! ".Database::get_error_msg(),1);
			}
			//Update:
			Config::set_value("DB_VERSION", $ver, $this->module);
		}

	}

	/**
	 * @return bool
	 */
	abstract protected function do_update();

	public function update(){
		$db_version1 = Config::get_value("DB_VERSION",$this->module=="core"?null:$this->module,0,null,false);
		$this->do_update();
		$db_version2 = Config::get_value("DB_VERSION",$this->module=="core"?null:$this->module,0,null,false);
		if ($db_version1==$db_version2){
			return false;
		}
		return "v$db_version1 &rarr; v$db_version2";
	}

}