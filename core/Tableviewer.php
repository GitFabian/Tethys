<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Creates a HTML table from a database table.
 * require_once ROOT_HDD_CORE."/core/Tableviewer.php";
 */
namespace core;

require_once ROOT_HDD_CORE."/core/Html.php";
require_once ROOT_HDD_CORE."/core/Table.php";

use tools\T_Html;

class Tableviewer {

	private $table_name;
	/**
	 * @var Table
	 */
	private $table = null;

	public function __construct($table_name) {
		$this->table_name = $table_name;
	}

	public function __toString() {
		$table = $this->getTable();
		return $table->__toString();
	}

	/**
	 * @return Table
	 */
	public function getTable(){

		$table_escaped = "`".escape_sql($this->table_name)."`";
		$data = \core\Database::select("SELECT * FROM $table_escaped LIMIT 999;");

		$this->table = new Table($data);

		return $this->table;
	}

	public static function getLink( $table, $html = null ){
		if(!$table){
			Errors::die_hard("No table given!");
		}
		if($html===null){
			$html = $table;
		}
		return new Html_a($html, ROOT_HTTP_CORE."/core/tableview.".EXTENSION."?table=".urlencode($table));
	}

}