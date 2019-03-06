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

use tools\T_Debug;

class Webparser {

	/**
	 * SIMPLE: Returns content of a webpage as a string.
	 * @param string $url
	 * @param bool $check_status
	 * @return string|false
	 */
	public static function webtransmission_simple($url, $check_status = false){
		if($check_status && (($code=self::get_http_response_code($url)) != "200")){
			require_once ROOT_HDD_CORE."/core/Webparser_Error.php";
			$error = new Webparser_Error();
			$error->statuscode = $code;
			return $error;
		}else{
			return file_get_contents($url);
		}
	}

	public static function get_http_response_code($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

}