<?php

class Model_Staff extends Model_Table{
	public $table="staffs";

	function init(){
		parent::init();

		$this->addField('name')->mandatory(true);
		$this->addField('username');
		$this->addField('password')->type('password');
		$this->addField('is_super')->type('boolean')->defaultValue(false);
		
		$this->addHook('beforeSave',$this);
		$this->add('dynamic_model/Controller_AutoCreator');
	}

	function beforeSave(){
		if($this['name']=='admin') $this['is_super']=true;
	}
}
