<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Shows table from the database using class Tableviewer.
 * @see \core\Tableviewer
 */
require_once '../Start.php';
$page = Start::init("core_tableviewer", "Datenansicht");

require_once ROOT_HDD_CORE."/core/Tableviewer.php";

$table = request_value("table");
if(!$table){
	$page->exit_with_error("Keine Tabelle angegeben!");
}

$page->reset($page->get_id(), $table." - Datenansicht");

$module = request_value("module");
if(!$module){
	$module = substr($table, 0, strpos($table, "_"));
}

if(!$module){
	$page->exit_with_error("Kein Modul angegeben!");
}

$tableviewer = new \core\Tableviewer($module, $table);

$page->add($tableviewer);

$page->send_and_quit();