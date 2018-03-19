<?php

/*
 * Basic toolbox
 */

function request_value($key, $default=null){
	if (isset($_REQUEST[$key])) return $_REQUEST[$key];
	return $default;
}

function request_cmd($cmd){
	return (isset($_REQUEST["cmd"]) && $_REQUEST["cmd"]==$cmd);
}

/**
 * Example: "<tag value = '".string_escape_html_value($value)."' />"
 *
 * @param $value string
 * @return string
 */
function string_escape_html_value($value){
	return str_replace_byArray(array(
		"\"" => "&quot;",
		"'" => "&apos;",
	),$value);
}

function str_replace_byArray($substitutions, $string){
	return str_replace(array_keys($substitutions),array_values($substitutions),$string);
}

/**
 * array("id"=>"link1","href"=>"http://tethys-framework.de")
 * =>
 * <a href='http://tethys-framework.de' id='link1'>
 */
function html_tag_keyValues($params){
	$html="";
	foreach ($params as $key=>$value){
		$html.=" $key='".string_escape_html_value($value)."'";
	}
	return $html;

}
