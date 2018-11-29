<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

require_once ROOT_HDD_CORE . "/tools/T_Strings.php";
require_once ROOT_HDD_CORE . "/tools/T_Html.php";

/*
 * Basic toolbox
 * TODO: In Klassen überführen
 */

/**
 * Encapsulates reading of a value from the $_REQUEST array.
 * @param string      $key
 * @param string|null $default
 * @return string|null
 */
function request_value($key, $default = null) {
	if (isset($_REQUEST[$key])) return $_REQUEST[$key];
	return $default;
}

/**
 * Checks, if the $_REQUEST value of "cmd" is set to command $cmd.
 * @param string $cmd
 * @return bool
 */
function request_cmd($cmd) {
	return (isset($_REQUEST["cmd"]) && ($_REQUEST["cmd"] == $cmd));
}

/**
 * @deprecated
 */
function escape_value_html($value) {
	return \tools\T_Strings::escape_value_html($value);
}

/**
 * Escapes quotes with a backslash.
 * Escapes single quotes, double quotes and the backslash itself.
 * @param string $string
 * @return string
 */
function escape_value_bs($string) {
	return str_replace_byArray(array(
		"\\" => "\\\\",
		"'" => "\\'",
		"\"" => "\\\"",
	), $string);
}

/**
 * Alias for @see escape_value_bs . Useful for later introduction of PDO's.
 * @param string $string
 * @return string
 */
function escape_sql($string) {
	return escape_value_bs($string);
}

/**
 * @deprecated
 */
function str_replace_byArray($substitutions, $string) {
	return \tools\T_Strings::replace_byArray($substitutions, $string);
}

/**
 * @deprecated
 */
function html_tag_keyValues($params) {
	return \tools\T_Html::tag_keyValues($params);
}

/**
 * HTML-code for a link with the class "abutton".
 * @param string $link
 * @param string $label
 * @return string HTML
 */
function html_a_button($link, $label) {
	return "<a href='" . escape_value_html($link) . "' class='abutton'>$label</a>";
}

/**
 * Loads a template file, fills in the values and returns the content as a string.
 * Comments marked as follows will be removed:
 * &#47;&#42;&#42;TPLDOCSTART This comment will be removed TPLDOCEND&#42;&#47;
 * @param string $file
 * @param array  $replacements
 * @return string
 */
function template_load($file, $replacements) {
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

/**
 * Saves a string to a file.
 * @param string $file
 * @param string $content
 * @param bool   $append
 */
function file_save($file, $content, $append = false) {
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
