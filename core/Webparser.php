<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2019 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * Webparser.
 * require_once ROOT_HDD_CORE."/core/Webparser.php";
 */
namespace core;


class Webparser {

	/**
	 * SIMPLE: Returns content of a webpage as a string.
	 * @param string $url
	 * @return string|false
	 */
	public static function webtransmission_simple($url){
		return file_get_contents($url);
	}

}