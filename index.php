<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
require_once 'Start.php';
$page = Start::init("core_index", Settings::get_core_value("INDEX_TITLE"));

$page->addHtml("Hello World");

$page->send_and_quit();
