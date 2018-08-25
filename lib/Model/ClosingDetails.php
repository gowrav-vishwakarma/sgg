<?php

class Model_ClosingDetails extends Model_Table{
	public $table="closing_details";

	function init(){
		parent::init();

		$this->hasOne('Closing','closing_id')->mandatory(true);
		$this->hasOne('Member','member_id')->mandatory(true);

		$this->addField('roi_to_pay')->type('boolean')->defaultValue(false);
		$this->addField('completed_sponsored_ids')->type('int')->defaultValue(0);

		$this->addField('roi_amount')->type('money');
		$this->addField('completed_sponsored_ids_amount')->type('money');
		$this->addField('total')->type('money');

		$this->add('dynamic_model/Controller_AutoCreator',['engine'=>'INNODB']);
	}

}
