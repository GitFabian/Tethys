<?php

/*
 * Basic toolbox
 */

function request_value($key, $default = null) {
	if (isset($_REQUEST[$key])) return $_REQUEST[$key];
	return $default;
}

function request_cmd($cmd) {
	return (isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == $cmd);
}

/**
 * Examples:
 *      "<tag value = '".escape_value_html($value)."' />"
 *      "<tag value = \"".escape_value_html($value)."\" />"
 *      '<tag value = "'.escape_value_html($value).'" />'
 *
 * @param $value string
 * @return string
 */
function escape_value_html($value) {
	return str_replace_byArray(array(
		"&" => "&amp;",
		"\"" => "&quot;",
		"'" => "&apos;",
	), $value);
}

/**
 * @param $string string
 * @return string
 */
function escape_value_bs($string) {
	return str_replace_byArray(array(
		"\\" => "\\\\",
		"'" => "\\'",
		"\"" => "\\\"",
	), $string);
}

function str_replace_byArray($substitutions, $string) {
	return str_replace(array_keys($substitutions), array_values($substitutions), $string);
}

/**
 * array("id"=>"link1","href"=>"http://tethys-framework.de")
 * =>
 * <a href='http://tethys-framework.de' id='link1'>

 * @param $params string[]
 * @return string
 */
function html_tag_keyValues($params) {
	$html = "";
	foreach ($params as $key => $value) {
		$html .= " $key='" . escape_value_html($value) . "'";
	}
	return $html;

}

function html_a_button($link, $label) {
	return "<a href='" . escape_value_html($link) . "' class='abutton'>$label</a>";
}

function template_load($file, $replacements) {
	if (!file_exists($file)) {
		Page::get_global_page()->exit_with_error("Template-Datei nicht gefunden!");
	}

	//Template-Datei einlesen:
	$content = file_get_contents($file);

	if ($content === false) {
		Page::get_global_page()->exit_with_error("Template-Datei konnte nicht geladen werden!");
	}

	//Ersetzungen:
	$content = str_replace_byArray($replacements, $content);

	//TPLDOC entfernen:
	/** Explanation of the RegEx: http://gitfabian.github.io/Tethys/regex.html */
	$content = preg_replace("/\\/\\*\\*TPLDOCSTART.*?TPLDOCEND\\*\\/\\R?/s", "", $content);

	return $content;
}
