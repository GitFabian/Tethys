<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace tools;
/**
 * Dateien-Toolbox
 * require_once ROOT_HDD_CORE . "/tools/T_Files.php";
 */
class T_Files {

	/**
	 * Saves a string to a file.
	 * @param string $file
	 * @param string $content
	 * @param bool $append
	 */
	public static function save($file, $content, $append = false) {
		/** Explanation of the file params: http://gitfabian.github.io/Tethys/php/files.html */
		$file = fopen($file, $append ? "a" : "w");
		$success = false;
		if ($file !== false) {
			$success = fwrite($file, $content);
			fclose($file);
		}
		if ($success === false) {
			\core\Page::get_global_page()->exit_with_error("Speichern der Datei \"$file\" fehlgeschlagen!");
		}
	}

}
