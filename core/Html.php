<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace core;
use tools\T_Html;
use tools\T_Strings;

/**
 * HTML-Element
 * require_once ROOT_HDD_CORE."/core/Html.php";
 */
class Html {

	protected $tag;
	private $content;
	protected $params;
	/**
	 * @var Html[]
	 */
	private $children = array();

	/**
	 * Html constructor.
	 * @param string $tag e.g. DIV, P, A, BUTTON
	 * @param string $content
	 * @param array|null $params Key-Value pairs of HTML-Attributes
	 */
	public function __construct($tag, $content, $params = null) {
		$this->tag = $tag;
		$this->content = $content;
		$this->setParams($params);
	}

	/**
	 * @param Html|string $child
	 */
	public function addChild($child){
		$this->children[] = $child;
	}

	/**
	 * @param array $childs
	 */
	public function addChildren($childs){
		foreach ($childs as $child) {
			$this->addChild($child);
		}
	}

	public function addClass($class){
		if($class===null){
			return;
		}
		if(isset($this->params["class"])) {
			$this->params["class"] .= $class;
		} else {
			$this->params["class"] = $class;
		}
	}

	public function setParam($key, $value){
		if($value===null){
			unset($this->params[strtolower($key)]);
		}
		$this->params[strtolower($key)] = $value;
	}

	public function setId($value){
		$this->setParam("id", $value);
	}

	public function setParams($array){
		if(!is_array($array)){
			return;
		}
		foreach ($array as $key=>$value){
			$this->setParam($key, $value);
		}
	}

	public function __toString() {
		$params = T_Html::tag_keyValues($this->params);
		return "<$this->tag$params>$this->content".implode("",$this->children)."</$this->tag>";
	}

}

class Html_standalone extends Html {
	public function __construct($tag, array $params = null) {
		parent::__construct($tag, null, $params);
	}
	public function __toString() {
		$params = T_Html::tag_keyValues($this->params);
		return "<$this->tag$params />";
	}
}

class Html_button extends Html_standalone {
	public function __construct($value, $type = "button", array $params = array()) {
		$params["value"] = $value;
		$params["type"] = $type;
		parent::__construct("input", $params);
	}
}

class Html_a extends Html {
	public function __construct($content, $href, array $params = array()) {
		parent::__construct("a", $content, $params);
		$this->setParam("href", $href);
	}
}

class Html_h1 extends Html {
	public function __construct($content, array $params = array()) {
		parent::__construct("h1", $content, $params);
	}
}

class Html_h2 extends Html {
	public function __construct($content, array $params = array()) {
		parent::__construct("h2", $content, $params);
	}
}

class Html_h3 extends Html {
	public function __construct($content, array $params = array()) {
		parent::__construct("h3", $content, $params);
	}
}

class Html_h4 extends Html {
	public function __construct($content, array $params = array()) {
		parent::__construct("h4", $content, $params);
	}
}

class Html_div extends Html {
	public function __construct($content, $class=null, $id=null, array $params = null) {
		parent::__construct("div", $content, $params);
		if($class!==null){
			$this->addClass($class);
		}
		if($id!==null){
			$this->setId($id);
		}
	}
}

/**
 * A paragraph
 */
class Html_p_div extends Html_div {
	public function __construct($content, array $params = null) {
		parent::__construct($content, null, null, $params);
		$this->addClass("tparagraph");
	}
}

class Html_a_button extends Html_a {
	public function __construct($content, $href, array $params = array()) {
		parent::__construct($content, $href, $params);
		$this->addClass("abutton");
	}
}

class Html_form_submitButton extends Html {
	public function __construct($label, array $values, $target = "", $method="post", array $params_form = array(), array $params_button = array()) {
		$params_form["target"] = $target;
		$params_form["method"] = $method;
		$params_form["class"] = isset($params_form["class"])?$params_form["class"]." oneButton":"oneButton";
		parent::__construct("form", "", $params_form);

		//Hidden Fields
		foreach ($values as $key=>$value){
			$hidden = new Html_standalone("input", array("type"=>"hidden", "name"=>$key, "value"=>$value));
			$this->addChild($hidden);
		}

		$button = new Html_button($label, "submit", $params_button);

		$this->addChild($button);
	}
}
