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
 * @deprecated Use \tools\T_Strings::escape_value_html instead.
 */
function escape_value_html($value) {
	return \tools\T_Strings::escape_value_html($value);
}

/**
 * Escapes quotes with a backslash.
 * Escapes single quotes, double quotes, backticks and the backslash itself.
 * @param string $string
 * @return string
 */
function escape_value_bs($string) {
	return \tools\T_Strings::replace_byArray(array(
		"\\" => "\\\\",
		"'" => "\\'",
		"`" => "\\`",
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
 * @deprecated
 */
function html_a_button($link, $label) {
	$obj = new \core\Html_a_button($label, $link);
	return $obj->__toString();
}

/**
 * @deprecated
 */
function template_load($file, $replacements) {
	return \tools\T_Templates::load($file, $replacements);
}

/**
 * @deprecated
 */
function file_save($file, $content, $append = false) {
	\tools\T_Files::save($file, $content, $append);
}
