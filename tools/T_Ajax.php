<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Ajax-Toolbox.
 * require_once ROOT_HDD_CORE."/tools/T_Ajax.php";
 */

namespace tools;


class T_Ajax {

	public static function ajax_exit(array $data){

		echo json_encode($data);

		exit;

	}

}