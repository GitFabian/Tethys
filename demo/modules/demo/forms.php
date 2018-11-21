<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Created.
 * Date: 21.11.2018
 */
require_once '../../tethys/Start.php';
$page = Start::init("demo_forms", "Forms");
include_once(ROOT_HDD_CORE.'/core/Form.php');

$form = new \core\Form();

$form->add_field(new \core\Formfield_text("text"));

$page->addHtml($form);

$page->send_and_quit();