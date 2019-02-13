<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * CSV.
 * require_once ROOT_HDD_CORE."/core/CSV.php";
 */

namespace core;


class CSV {

	public static $debug = false;
	const TYPE_ASSOC = 1;

	private $data;
	private $data_type;
	private $filename;
	public $mscsv = false;

	public function __construct($data, $filename="export.csv", $data_type = self::TYPE_ASSOC) {
		$this->data = $data;
		$this->data_type = $data_type;
		$this->filename = $filename;
	}

	public function export_out($prefix="", $suffix=""){
		if(self::$debug){
			echo "<pre>";
		}

		if($this->data===null || $this->data===false){
			$this->data = array();
		}

		if(!self::$debug){
			header('Content-type: text/csv; charset=ISO-8859-1');
			header("Content-Disposition: attachment; filename=\"$this->filename\"");
		}
		if($this->mscsv){
			echo "sep=,\r\n";
		}

		if($prefix){
			echo utf8_decode($prefix)."\r\n";
		}

		$header = array();
		foreach ($this->data as $row){
			foreach ($row as $key=>$value){
				$header[$key]=$key;
			}
		}

		$this->out_row($header, $header);

		foreach ($this->data as $row){
			$this->out_row($header, $row);
		}

		if($suffix){
			echo utf8_decode($suffix)."\r\n";
		}
		if(self::$debug){
			echo "</pre>";
		}
		exit;
	}

	private function out_row(&$header, &$row) {
		$row_csv = array();
		foreach ($header as $key => $dummy) {
			if (isset($row[$key])) {
				$value = utf8_decode($row[$key]);

				if(strpos($value, "\"")!==false
					||strpos($value, ",")!==false
					||strpos($value, "\n")!==false
				){
					$value = preg_replace("/\"/", "\"\"", $value);
					$value = '"' . $value . '"';
				}

				$row_csv[] = $value;
			} else {
				$row_csv[] = "";
			}
		}
		echo implode(",", $row_csv) . "\r\n";
	}

}