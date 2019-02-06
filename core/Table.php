<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Class representing a HTML-table.
 * require_once ROOT_HDD_CORE."/core/Table.php";
 */

namespace core;


class Table {

	private $data;
	private $header = null;
	public $tbl_class = "tethysTable";
	public $no_data_info = "(Keine Daten)";

	public function __construct($data = null) {
		$this->setData($data);
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function getHeader() {
		if ($this->header === null && $this->data) {
			$this->header = array();
			$this_data_0 = reset($this->data);
			foreach ($this_data_0 as $header => $dummy) {
				$this->header[$header] = $header;
			}
		}
		return $this->header;
	}

	public function setHeader($header) {
		$this->header = $header;
	}

	public function __toString() {
		return $this->toHTML();
	}

	public function toHTML(){

		$header = $this->getHeader();
		if (!$header){
			return $this->no_data_info;
		}

		$thead = array();
		$header_names = array();
		foreach ($header as $name => $string) {
			$header_names[] = $name;
			$thead[] = "<th>$string</th>";
		}
		$thead_html = implode("\n", $thead);

		$tbody = array();

		$this_data_0 = reset($this->data);
		if (is_array($this_data_0)) {
			foreach ($this->data as $row) {
				$trow = array();
				foreach ($header_names as $name) {
					$trow[] = "<td>" . (isset($row[$name]) ? $row[$name] : "") . "</td>";
				}
				$tbody[] = "<tr>" . implode("", $trow) . "</tr>";
			}
		} else if (is_object($this_data_0)) {
			foreach ($this->data as $row) {
				$trow = array();
				foreach ($header_names as $name) {
					$trow[] = "<td>" . (isset($row->$name) ? $row->$name : "") . "</td>";
				}
				$tbody[] = "<tr>" . implode("", $trow) . "</tr>";
			}
		} else {
			Errors::die_hard("Unbekannter Datentyp!");
		}
		$tbody_html = implode("\n", $tbody);
		$html = <<<ENDE
<table class="$this->tbl_class">
	<thead>
		<tr>
			$thead_html
		</tr>
	</thead>
	<tbody>
		$tbody_html
	</tbody>
</table>
ENDE;

		return $html;
	}

}