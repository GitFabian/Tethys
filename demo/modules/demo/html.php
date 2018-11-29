<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

/**
 * Demonstration of Tethys-specific HTML- and CSS-notations.
 */


use core\Html_a_button;
use core\Html_p_div;

require_once '../../tethys/Start.php';
$page = Start::init("demo_html", "HTML");
require_once ROOT_HDD_CORE."/core/Html.php";

$page->add(new Html_p_div(
	"Dies ist ein ".new \core\Html_a("Link","")."."
	." Dieser Link sieht aus wie ein " .new Html_a_button("Button","#")."."
	." Dies ist ein " .new Html_button("Button")."."
));

$page->send_and_quit();
