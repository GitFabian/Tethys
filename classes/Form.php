<?php

/**
 * Class Form
 *
include_once(ROOT_HDD_CORE.'/classes/Form.php');
 *
 */
class Form {

	private $action;
	private $method;
	private $buttons=array();
	private $fields = array();

	public function __construct($action="", $submit_text="Absenden", $cmd=null, $method=null) {

		$this->action=$action;

		//The submission method is "post" by default, for developers "get".
		$this->method=$method?:(USER_DEV?"get":"post");

		if($cmd){
			$this->fields[]=new Formfield_hidden("cmd",$cmd);
		}

		if($submit_text){
			$this->buttons[]="<input type='submit' value='$submit_text'>";
		}

	}

	public function add_field(Formfield $formfield){
		$this->fields[]=$formfield;
	}

	public function __toString() {
		return $this->toHtml();
	}

	public function toHtml(){
		$buttons = implode("\n",$this->buttons);
		$fields_html = implode("\n",$this->fields);
		return "<form action=\"$this->action\" method='$this->method'>\n$fields_html\n$buttons\n</form>";
	}

}

class Formfield{

	//Formfield:
	protected $name;
	protected $title;
	protected $value;
	public $id=null;
	public $more_params=array();

	//Surrounding div:
	public $tooltip="";
	public $outer_id=null;
	public $outer_class=null;
	public $outer_more_params=array();

	function __construct($name, $title=null, $value=null){
		$this->name=$name;
		$this->title=($title===null?$name:$title);
		$this->value=request_value($name,$value);
	}

	protected function inner_html(){
		return "UNSPECIFIED FORMTYPE";
	}

	public function __toString() {
		return $this->toHtml();
	}

	protected function toHtml(){
		$label=$this->title;
		$tooltip=$this->tooltip;

		//Tooltip? Change label
		if($tooltip)$label.=" (!)";

		//Developers see the fieldname
		if(USER_DEV)$tooltip.=" [".$this->name."]";

		$title=$tooltip?"title='".escape_value_html($tooltip)."'":"";

		return "<div".$this->getParams_outer().">"
				."<label $title>$label</label>"
				.$this->inner_html()
			."</div>";
	}

	protected function getParams_inner($value=true){
		$params = $this->more_params;

		if($this->name)$params["name"]=$this->name;
		if($value)if($this->value)$params["value"]=$this->value;
		if($this->id)$params["id"]=$this->id;

		return html_tag_keyValues($params);
	}

	protected function getParams_outer(){
		$params = $this->outer_more_params;

		if($this->outer_id)$params["id"]=$this->outer_id;
		$params["class"]="form_field".($this->outer_class?" ".$this->outer_class:"");

		return html_tag_keyValues($params);
	}

}

class Formfield_hidden extends Formfield {

	public function __construct($name, $value) {
		parent::__construct($name, null, $value);
	}

	public function toHtml() {
		return "<input type='hidden'".$this->getParams_inner()." />";
	}

}

class Formfield_text extends Formfield {

	public function inner_html() {
		return "<input type='text'".$this->getParams_inner()." />";
	}

}

class Formfield_password extends Formfield {

	public function inner_html() {
		return "<input type='password'".$this->getParams_inner()." />";
	}

}

class Formfield_select extends Formfield {

	private $values;

	public function __construct($name, $title, $values, $selected=null) {
		parent::__construct($name, $title, $selected);
		$this->values=$values;
	}

	public function inner_html() {
		$options=array();
		foreach ($this->values as $key=>$value){
			$selected=($key==$this->value?"selected":"");
			$options[]="\t<option value='$key' $selected>$value</option>";
		}
		return "<select".$this->getParams_inner(false).">\n".implode("\n",$options)."\n</select>";
	}

}
