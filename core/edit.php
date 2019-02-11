<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Edit a table's data.
 */
require_once '../Start.php';
$page = Start::init("core_edit", "Edit");
if(request_cmd("save")){ $page->send_and_quit(); }
include_once(ROOT_HDD_CORE.'/core/Form.php');

$module = request_value("tmodule");
$table = request_value("ttable");
$id = request_value("id");
$return_url = request_value("return", null);

if($return_url === null && isset($_SERVER["HTTP_REFERER"])){
	$return_url = $_SERVER["HTTP_REFERER"];
}

if($return_url === null){
	#$return_url = ROOT_HTTP_CORE;
}

if(!$module||!$table||!$id){
	$page->exit_with_error("Fehler bei der Eingabe! #18");
}

#$constraints = \core\Database::dbio_information_schema_constraints($table);

$col_info = \core\Database::dbio_info_columns($table);

$new = (isset($_REQUEST['id']) && (strcasecmp($_REQUEST['id'], "NEW") == 0));

if($new){
	$page->reset($page->get_id(), "New $table");
}else{
	$page->reset($page->get_id(), "Edit $table #$id");
}

$query = array();
if ($new) {
	foreach ($col_info as $key => $dummy) {
		$query[$key] = "";
	}
} else {
	$table_escaped = "`" . escape_sql($table) . "`";
	$id_escaped = "'" . escape_sql($id) . "'";
	$sql = "-- 
		select * from $table_escaped where `id`=$id_escaped;";
	$query = \core\Database::select_single($sql);
	if(!$query){
		$page->exit_with_error("Datensatz #$id nicht gefunden!");
	}
}

$form = new \core\Form($return_url,"Speichern","save");
$form->add_field(new \core\Formfield_hidden("id",$id));

foreach ($query as $key => $value) {
	$ff = false;
	if ($key!="id"){

		/*
		 * Default-Value
		 */
		if($new){
			$value=$col_info[$key]['Default'];
		}
		$v=request_value($key,$value);

		$ff = new \core\Formfield_text($key, null, $v, false);
	}
	if($ff){
		$form->add_field($ff);
	}
}

$page->add($form);

$page->send_and_quit();