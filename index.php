<?php
require_once 'Start.php';
$page = Start::init("core_index",Settings::get_core_value("INDEX_TITLE"));

$page->addHtml("Hello World");

$page->send_and_quit();
