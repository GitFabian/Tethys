<?php
require_once 'start.php';
$page = start::init("core_index",settings::get_core_value("INDEX_TITLE"));

$page->addHtml("Hello World");

$page->send_and_quit();
