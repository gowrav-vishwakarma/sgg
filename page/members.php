<?php


class page_members extends Page {
	
	public $title = "Member Management";

	function page_index(){

		$member = $this->add('Model_Member');
		$crud = $this->add('CRUD',['allow_add'=>false,'allow_del'=>false])->addClass('memebr_grid');
		$crud->setModel($member,['member_name','city'],['id','member_name','parent','sponsor','left','right','city','created_at']);

		$crud->grid->addQuickSearch(['member_name','id']);

		$add_btn = $crud->grid->addButton('Add Members');
		$add_root = $crud->grid->addButton('Add Root');

		$crud->grid->addPaginator(100);

		$add_btn->js('click')->univ()->frameURL("Add Member(s)",$this->app->url('./add'));
		$crud->grid->js('reload')->reload();

		$crud->grid->addFormatter('member_name','template')->setTemplate('<a href="?page=memberdetails&id={$id}" target="details">{$member_name}</a> <a href="?page=structure&id={$id}" target="structure">[T]</a>','member_name');
			
		$add_root->on('click',function($js,$data){
			$member = $this->add('Model_Member');
			$member->addCondition('sponsor_id',null);
			$member->tryLoadAny();
			if($member->loaded()){
				throw new \Exception("You already have root added", 1);
			}

			$member['member_name']='Root';
			$member->save();
			
			return $js->trigger('reload')->_selector('.memebr_grid');
		});
	}

	function page_add(){
		$member = $this->add('Model_Member');

		$system=['left_id','right_id','parent_id','parent_side','created_at','completed_2_4','paid_completed','completed_sponsored_ids','created_by_id'];
		
		foreach ($system as $f) {
			$member->getElement($f)->system(true);
		}
		

		$form = $this->add('Form');
		$form->addField('Number','no_of_ids');
		$form->setModel($member);

		$form->getElement('sponsor_id')->validate('required');

		$form->addSubmit('Add');

		if($form->isSubmitted()){
			$loop=1;
			if($form['no_of_ids']) $loop = $form['no_of_ids'];
			for ($i=0; $i < $loop; $i++) { 				
				$member->addMember($form->get());
			}
			$form->js(null,$form->js()->trigger('reload')->_selector('.memebr_grid'))->univ()->closeDialog()->execute();
		}
	}
}