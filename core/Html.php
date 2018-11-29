<?php
/*GPL
 * This file is part of the Tethys framework;
 * Copyright (C) 2014-2018 Fabian Perder (tethys@qnote.de) and contributors
 * Tethys comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 GPL*/

namespace core;
use tools\T_Html;

/**
 * HTML-Element
 * require_once ROOT_HDD_CORE."/core/Html.php";
 */

class Html {

	private $tag;
	private $content;
	/**
	 * @var Html[]
	 */
	private $children = array();
	private $params;

	/**
	 * Html constructor.
	 * @param string    $tag e.g. DIV, P, A, BUTTON
	 * @param string    $content
	 * @param array     $params
	 * @param Html|null $child
	 */
	public function __construct($tag, $content, $params = array(), $child = null) {
		$this->tag = $tag;
		$this->content = $content;
		$this->params = $params;
		if($child!==null){
			$this->addChild($child);
		}
	}

	public function addChild($child){
		$this->children[] = $child;
	}

	public function __toString() {
		$params = T_Html::tag_keyValues($this->params);
		return "<$this->tag$params>$this->content".implode("",$this->children)."</$this->tag>";
	}

}
