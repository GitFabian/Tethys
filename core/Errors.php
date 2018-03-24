<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

class Errors {

	public static function format_exception(Exception $e){
		$html="";
		if(USER_DEV){
			$html.="<pre>=========== Fehler #".$e->getCode()." ===========\n";
			$html.=$e->getMessage()."\n";
			$html.="----------------------\n";
			$html.=$e->getTraceAsString()."\n";
			$html.="======================</pre>\n";

		}else{
			$html.=$e->getMessage();
		}
		return $html;
	}

}