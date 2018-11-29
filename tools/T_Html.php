<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

/**
 * HTML-Toolbox
 * require_once ROOT_HDD_CORE . "/tools/T_Html.php";
 */

namespace tools;

class T_Html {

	/**
	 * Creates key-value pairs as used by HTML tags.
	 * @param array $params
	 * @return string
	 */
	public static function tag_keyValues($params) {
		if(!is_array($params)){
			return "";
		}
		$html = "";
		foreach ($params as $key => $value) {
			$html .= " $key='" . escape_value_html($value) . "'";
		}
		return $html;
	}

}
