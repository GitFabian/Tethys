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
	public $data_id_key = "id";
	private $header = null;
	public $tbl_class = "tethysTable";
	public $no_data_info = "(Keine Daten)";

	/**
	 * @var array
	 * [[ID]] row's primary id
	 */
	private $options = array();

	/**
	 * @var array
	 * Global options
	 */
	private $options2 = array();

	public function __construct($data = null) {
		$this->setData($data);
	}

	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * @param string $module
	 * @param string $table
	 * @param bool|string $edit_link
	 * @param bool|string $delete_link
	 * @param bool|string $new_link
	 *
	 * [[ID]] row's primary id
	 */
	public function setOptions($module, $table, $edit_link = false, $delete_link = false, $new_link = false){

		if($edit_link){

			if($edit_link===true){
				$edit_link = ROOT_HTTP_CORE."/core/edit.".EXTENSION."?tmodule=".urlencode($module)."&ttable=".urlencode($table)."&id=[[ID]]";
			}

			$this->options["edit"] = new Html_a_button("Bearbeiten", $edit_link);

		}

		if($delete_link){

			if($delete_link===true){
				$delete_link = new Html_form_submitButton("Löschen", array("cmd"=>"delete", "module"=>$module, "table"=>$table, "id"=>"[[ID]]"));
			}else{
				$delete_link = new Html_a_button("Löschen", $delete_link);
			}

			$this->options["delete"] = $delete_link;

		}

		if($new_link){

			if($new_link===true){
				$new_link = ROOT_HTTP_CORE."/core/edit.".EXTENSION."?tmodule=".urlencode($module)."&ttable=".urlencode($table)."&id=NEW";
			}

			$this->options2["new"] = new Html_a_button("Neu", $new_link);

		}

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
			$table_html = $this->no_data_info;
		}else{

			$thead = array();
			$header_names = array();
			foreach ($header as $name => $string) {
				$header_names[] = $name;
				$thead[] = "<th>$string</th>";
			}

			if($this->options){
				$thead[] = "<th></th>";
			}

			$thead_html = implode("\n", $thead);

			$tbody = array();

			$this_data_0 = reset($this->data);
			foreach ($this->data as $row) {
				$trow = array();
				if (is_array($this_data_0)) {
					foreach ($header_names as $name) {
						$trow[] = "<td>" . (isset($row[$name]) ? $row[$name] : "") . "</td>";
					}
				} else if (is_object($this_data_0)) {
					foreach ($header_names as $name) {
						$trow[] = "<td>" . (isset($row->$name) ? $row->$name : "") . "</td>";
					}
				} else {
					Errors::die_hard("Unbekannter Datentyp!");
				}
				if($this->options){
					$options = "<td>".implode("", $this->options)."</td>";

					if (isset($row[$this->data_id_key])){
						$options = str_ireplace("[[ID]]", $row[$this->data_id_key], $options);
					}

					$trow[] = $options;
				}
				$tbody[] = "<tr>" . implode("", $trow) . "</tr>";
			}

			$tbody_html = implode("\n", $tbody);

			$table_html = "
				<table class=\"$this->tbl_class\">
					<thead>
						<tr>
							$thead_html
						</tr>
					</thead>
					<tbody>
						$tbody_html
					</tbody>
				</table>
			";

		}

		$options2="";
		if($this->options2){
			$options2.=implode("",$this->options2);
		}
		$options2 = "<div class=\"options2\">$options2</div>";

		$html = "
			<div class=\"tTable\">
				$table_html
				$options2
			</div>
		";

		return $html;
	}

}