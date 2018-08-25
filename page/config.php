<?php


class page_config extends Page {
	
	public $title = "Configuration Management";

	function init(){
		parent::init();

		$staff_m = $this->add('Model_Staff');
		$c = $this->add('CRUD',$this->add('Model_ACL')->setNoneForOthers());
		$c->setModel($staff_m);

		// $c->grid->add('VirtualPage')
		//       ->addColumn('permissions','Permissions')
		//       ->set(function($page){
		// 			$staff_id = $_GET[$page->short_name.'_id'];
					
		//        });

		$this->add('HR');

		if(!file_exists('templates/memberprint.html')) file_put_contents('templates/memberprint.html', '');
		$template_file = file_get_contents('templates/memberprint.html');

		$form = $this->add('Form');
		$field = $form->addField('Text','member_print_layout')->set($template_file);

		$form->addSubmit('Update');

		if($form->isSubmitted()){
			file_put_contents('templates/memberprint.html', $form['member_print_layout']);
			$form->js()->reload()->univ()->successMessage('Done')->execute();
		}


	}
}