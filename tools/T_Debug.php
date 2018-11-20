<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Debugging-Toolbox
require_once ROOT_HDD_CORE . "/tools/T_Debug.php";
 */
namespace tools;

class T_Debug {

	/**
	 * Shows $var and quits.
	 * @param $var
	 */
	public static function out($var){
		echo "<pre>".print_r($var,1)."</pre>";
		exit;
	}

}