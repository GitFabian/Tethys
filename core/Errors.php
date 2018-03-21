<?php

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