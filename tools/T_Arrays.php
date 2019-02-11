<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Arrays-Toolbox.
 * require_once ROOT_HDD_CORE."/tools/T_Arrays.php";
 */

namespace tools;


class T_Arrays {

	public static function merge_assoc_greedy($array1, $array2){

		$result = $array1;

		foreach ($array2 as $key => $value) {
			$result[$key] = $value;
		}

		return $result;

	}

}