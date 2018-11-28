<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

/**
 * Demonstration of all available formfields.
 */

use core\Form;
use core\Formfield_password;
use core\Formfield_select;
use core\Formfield_text;
use core\Formfield_textarea;

require_once '../../tethys/Start.php';
$page = Start::init("demo_forms", "Forms");
include_once(ROOT_HDD_CORE . '/core/Form.php');

$form = new Form();

$form->add_field(new Formfield_text("text"));

$form->add_field(new Formfield_password("password","A long long long long long long long long long long long long long long label"));

$form->add_field(new Formfield_textarea("text"));

$select_values = array(
	"one" => "Eins",
	"two" => "Zwei",
);
$form->add_field(new Formfield_select("select", null, $select_values));

$page->addHtml($form);

$page->send_and_quit();