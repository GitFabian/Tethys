<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/
/**
 * This file contains classes Form, Formfield and children (different types) of Formfield.
 */
namespace core;
/**
 *
 * Class Form
 * ==========
 * Example of creating a form in Tethys:
 *     include_once(ROOT_HDD_CORE.'/classes/Form.php');
 *     $form = new Form("", "Speichern", "cmd_save_cfglink");
 *     $form->add_field(new Formfield_text("cfglink", "Konfigurationsdatei", $cfgfile_proposal));
 *     $page->addHtml($form);
 *
 */
class Form {

	/**
	 * $action is an URL that is called on form submission. Can be left empty (same page is called).
	 * @var string
	 */
	private $action;
	/**
	 * Form submission method. The submission method is "post" by default.
	 * @var string "get"|"post"
	 */
	private $method;
	private $buttons = array();
	private $fields = array();

	/**
	 * Form constructor.
	 * @param string      $action is an URL that is called on form submission. Can be left empty (same page is called).
	 * @param string      $submit_text
	 *                    Label of the submit button.
	 * @param string|null $cmd
	 *                    If set, a hidden key "cmd" is sent on submission.
	 * @param string|null $method
	 *                    Form submission method. The submission method is "post" by default.
	 */
	public function __construct($action = "", $submit_text = "Absenden", $cmd = null, $method = "post") {

		$this->action = $action;

		$this->method = $method;

		if ($cmd) {
			$this->fields[] = new Formfield_hidden("cmd", $cmd);
		}

		if ($submit_text) {
			$this->buttons[] = "<input type='submit' value='$submit_text'>";
		}

	}

	public function add_field(Formfield $formfield) {
		$this->fields[] = $formfield;
	}

	public function __toString() {
		return $this->toHtml();
	}

	public function toHtml() {
		$buttons = implode("\n", $this->buttons);
		$fields_html = implode("\n", $this->fields);
		return "<form action=\"$this->action\" method='$this->method'>\n$fields_html\n$buttons\n</form>";
	}

}

/**
 * Class Formfield
 * Generic class representing all formfields.
 */
class Formfield {

	//Formfield:
	protected $name;
	protected $value;
	public $id = null;
	public $more_params = array();

	//Label:
	protected $title;
	public $tooltip = "";

	//Surrounding div:
	public $outer_id = null;
	public $outer_class = null;
	public $outer_more_params = array();

	/**
	 * Formfield constructor.
	 * @param             $name
	 * @param string|null $title
	 *                    If set to null, the fieldname is used as label.
	 * @param string|null $value
	 * @param bool        $val_from_request
	 *                    If set to true, the default value ($value) can be overwritten by the request.
	 *                    Example: .../myform.php?myvalue=Foo
	 */
	function __construct($name, $title = null, $value = null, $val_from_request = true) {
		$this->name = $name;

		//Title: If set to null, the fieldname is used as label.
		$this->title = ($title === null ? $name : $title);

		$this->value = $val_from_request ? request_value($name, $value) : $value;
	}

	/**
	 * Generic function is overwritten with the respective HTML by the children.
	 * @return string
	 */
	protected function inner_html() {
		return "UNSPECIFIED FORMTYPE";
	}

	public function __toString() {
		return $this->toHtml();
	}

	protected function toHtml() {
		$label = $this->title;
		$tooltip = $this->tooltip;

		//Tooltip? Change label
		if ($tooltip) $label .= " (!)";

		//Developers see the fieldname
		if (USER_DEV) $tooltip .= " [" . $this->name . "]";

		$title = $tooltip ? "title='" . escape_value_html($tooltip) . "'" : "";

		return "<div" . $this->getParams_outer() . ">"
				. "<label $title>$label</label>"
				. $this->inner_html()
			. "</div>";
	}

	/**
	 * Every formfield has a name, a value, an id and possibly a list of some other parameters ($more_params).
	 * This function creates the corresponding HTML-snippet.
	 * @param bool $value If set to false, the parameter "value" is skipped.
	 * @return string String to insert into the HTML code.
	 */
	protected function getParams_inner($value = true) {
		$params = $this->more_params;

		if ($this->name) $params["name"] = $this->name;
		if ($value) if ($this->value) $params["value"] = $this->value;
		if ($this->id) $params["id"] = $this->id;

		return html_tag_keyValues($params);
	}

	/**
	 * For documentation @see getParams_inner.
	 */
	protected function getParams_outer() {
		$params = $this->outer_more_params;

		if ($this->outer_id) $params["id"] = $this->outer_id;
		$params["class"] = "form_field" . ($this->outer_class ? " " . $this->outer_class : "");

		return html_tag_keyValues($params);
	}

}

/**
 * Class Formfield_hidden
 * Hidden "input"s can -for example- be used to send commands.
 */
class Formfield_hidden extends Formfield {

	public function __construct($name, $value) {
		parent::__construct($name, null, $value, false);
	}

	public function toHtml() {
		return "<input type='hidden'" . $this->getParams_inner() . " />";
	}

}

/**
 * Class Formfield_text
 * A single-line text input field.
 */
class Formfield_text extends Formfield {

	public function inner_html() {
		return "<input type='text'" . $this->getParams_inner() . " />";
	}

}

/**
 * Class Formfield_password
 * Prompt for a password.
 */
class Formfield_password extends Formfield {

	public function inner_html() {
		return "<input type='password'" . $this->getParams_inner() . " />";
	}

}

/**
 * Class Formfield_select
 * An input field to select from predefined values.
 */
class Formfield_select extends Formfield {

	private $values;

	public function __construct($name, $title, $values, $selected = null) {
		parent::__construct($name, $title, $selected);
		$this->values = $values;
	}

	public function inner_html() {
		$options = array();
		foreach ($this->values as $key => $value) {
			$selected = ($key == $this->value ? "selected" : "");
			$options[] = "\t<option value='$key' $selected>$value</option>";
		}
		return "<select" . $this->getParams_inner(false) . ">\n" . implode("\n", $options) . "\n</select>";
	}

}
