<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/


namespace tools;
/**
 * Strings-Toolbox
 * require_once ROOT_HDD_CORE . "/tools/T_Strings.php";
 */

class T_Strings {

	/**
	 * Escapes quotes with htmlentities.
	 * Escapes single quotes, double quotes and the ampersand.
	 * Examples:
	 *      "<tag value = '".escape_value_html($value)."' />"
	 *      "<tag value = \"".escape_value_html($value)."\" />"
	 *      '<tag value = "'.escape_value_html($value).'" />'
	 * @param string $value
	 * @return string
	 */
	public static function escape_value_html($value) {
		return self::replace_byArray(array(
			"&" => "&amp;",
			"\"" => "&quot;",
			"'" => "&apos;",
		), $value);
	}

	/**
	 * Other syntax for the str_replace function.
	 * @param array  $substitutions An associative array containing the substitutions.
	 * @param string $string
	 * @return mixed
	 */
	public static function replace_byArray($substitutions, $string) {
		return str_replace(array_keys($substitutions), array_values($substitutions), $string);
	}

}
