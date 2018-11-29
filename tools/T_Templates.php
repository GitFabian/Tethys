<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace tools;
/**
 * Templates-Toolbox
 * require_once ROOT_HDD_CORE . "/tools/T_Templates.php";
 */
class T_Templates {

	/**
	 * Loads a template file, fills in the values and returns the content as a string.
	 * Comments marked as follows will be removed:
	 * &#47;&#42;&#42;TPLDOCSTART This comment will be removed TPLDOCEND&#42;&#47;
	 * @param string $file
	 * @param array $replacements
	 * @return string
	 */
	public static function load($file, $replacements) {
		if (!file_exists($file)) {
			\core\Page::get_global_page()->exit_with_error("Template-Datei nicht gefunden!");
		}

		//Read template file:
		$content = file_get_contents($file);

		if ($content === false) {
			\core\Page::get_global_page()->exit_with_error("Template-Datei konnte nicht geladen werden!");
		}

		//Replacements:
		$content = str_replace_byArray($replacements, $content);

		//Remove TPLDOC:
		/** Explanation of the RegEx: http://gitfabian.github.io/Tethys/php/regex.html */
		$content = preg_replace("/\\/\\*\\*TPLDOCSTART.*?TPLDOCEND\\*\\/\\R?/s", "", $content);

		return $content;
	}

}
