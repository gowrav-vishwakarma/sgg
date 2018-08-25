<?php

class Model_Parent extends Model_Member {
	
	public $table_alias='parent';	
	public $skip_dynamic = true;

	function init(){
		parent::init();

	}
}
