<?php

class Model_Closing extends Model_Table{
	public $table="closings";

	function init(){
		parent::init();

		$this->addField('name')->mandatory(true);
		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->sortable(true);
		$this->addField('till_id')->type('int');
		
		$this->hasMany('ClosingDetails','closing_id');
		$this->add('dynamic_model/Controller_AutoCreator',['engine'=>'INNODB']);
	}

}
