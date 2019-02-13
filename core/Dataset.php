<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Dataset.
 * require_once ROOT_HDD_CORE."/core/Dataset.php";
 */

namespace core;


class Dataset {

	public function __construct($values) {
		$keys = array_keys(get_object_vars($this));
		foreach ($values as $key => $value) {
			if (in_array($key, $keys)) {
				$this->$key = $value;
			} else {
				Errors::die_hard("Bitte Datenfeld definieren: " . get_class($this) . "->" . $key, 1);
			}
		}
	}

}
