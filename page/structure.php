<?php


class page_structure extends Page {
	
	function init(){
		parent::init();

		$this->add('View_GenerationJSTree');

	}
}