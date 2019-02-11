<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Core commands
 * require_once ROOT_HDD_CORE . "/core/Commands.php";
 */
namespace core;

use tools\T_Debug;

class Commands {

	public static function handle($cmd){
		switch ($cmd){
			case "save":
				self::cmd_save();
				break;
			case "delete":
				self::cmd_delete();
				break;
			default:
				break;
		}
	}

	public static function cmd_save(){
		$page = Page::get_global_page();

		$table = request_value("table");
		$id = request_value("id");

		if(!$table || !$id){
			Errors::die_hard("Fehler beim Speichern!");
			return false;
		}

		$fields = Database::dbio_info_columns($table);
		$data = array();
		foreach ($fields as $key => $dummy){
			if($key!="id"){
				$data[$key] = request_value($key);
			}
		}

		if(strcasecmp($id,"NEW")==0){
			$insert_id = Database::insert_assoc($table, $data);
		}else{
			$id_escaped = escape_sql($id);
			$insert_id = Database::update_assoc($table, "`id`='$id_escaped'", $data);
		}
		if(!$insert_id){
			if(Database::get_error_code()===false){
				$page->addMessageInfo("No changes to $table #$insert_id.");
				return true;
			}else{
				$page->addMessageError("Fehler beim Speichern von $table #$insert_id!<br>".Database::get_error_msg());
				return false;
			}
		}

		$page->addMessageConfirm("Saved $table #$insert_id.");
		return true;
	}

	public static function cmd_delete(){
		$page = Page::get_global_page();

		$table = request_value("table");
		$id = request_value("id");

		if(!$table || !$id){
			Errors::die_hard("Fehler beim Löschen!");
			return false;
		}

		$id_escaped = escape_sql($id);
		$table_escaped = "`".escape_sql($table)."`";
		$insert_id = Database::delete("-- 
			DELETE FROM $table_escaped WHERE `id`='$id_escaped'");
		if($insert_id==1){
			$page->addMessageConfirm("$table #$id gelöscht.");
			return true;
		}
		if($insert_id===0){
			$page->addMessageInfo("Löschen: $table #$id nicht gefunden!");
			return false;
		}

		$page->addMessageError("Fehler beim Löschen von $table #$id!<br>".Database::get_error_msg());
		return false;
	}
}