<?php

class Model_Member extends Model_Table{
	public $table="member";
	public $skip_dynamic = false;
	
	function init(){
		parent::init();

		$this->getElement('id')->sortable(true);

		$this->hasOne('Staff','created_by_id')->defaultValue($this->app->auth->model->id);

		$this->addField('member_name')->mandatory(true);
		$this->addField('father_name');
		$this->addExpression('name')->set(function($m,$q){
			return "CONCAT(id,' ',member_name)";
		})->sortable(true);

		$this->hasOne('Parent','parent_id')->mandatory(true);
		$this->addField('parent_side')->enum(['left','right']);
		$this->hasOne('Sponsor','sponsor_id')->mandatory(true)->display(['form'=>'autocomplete/Basic']);
		$this->hasOne('Left','left_id');
		$this->hasOne('Right','right_id');

		$this->addField('address')->type('text');
		$this->addField('city');
		$this->addField('state');
		$this->addField('pan_no');
		$this->addField('adhaar_no');
		$this->addField('bank_name');
		$this->addField('bank_branch');
		$this->addField('ifsc_code');
		
		$this->addField('completed_2_4')->type('boolean')->defaultValue(false);
		$this->addField('paid_completed')->type('boolean')->defaultValue(false);
		$this->addField('completed_sponsored_ids')->type('int')->defaultValue(0);

		$this->addField('created_at')->type('datetime')->defaultValue($this->app->now)->sortable(true);
		
		

		$this->hasMany('Sponsor','sponsor_id',null,'SponsoredIds');
		$this->hasMany('Left','left_id',null,'Left');
		$this->hasMany('Right','right_id',null,'Right');


		// $this->addField('sponsor_id')->type('int');
		// $this->addField('left_id')->type('int');
		// $this->addField('right_id')->type('int');

		if(!$this->skip_dynamic)
			$this->add('dynamic_model/Controller_AutoCreator',['engine'=>'INNODB']);
	}

	function findNextPosition(){
		$first_available_member = $this->add('Model_Member');
		$first_available_member->addCondition([['left_id',null],['right_id',null]]);
		$first_available_member->setOrder('id');
		$first_available_member->tryLoadAny();

		return [
			'id'=>$first_available_member->id,
			'side'=>$first_available_member['left_id'] == null ?'left':'right'
		];
	}

	function addMember($data,$sponsor_id=null){
		$available_location = $this->findNextPosition();

		$to_remove_fields = ['in_right'];
		if($sponsor_id) $to_remove_fields[] = 'sponsor_id';
		$removed_fields=[];

		foreach ($data as $key => $value) {
			if(in_array($key, $to_remove_fields)) {
				$removed_fields[$key] = $value;
				unset($data[$key]);
			}
		}

		$new_memebr = $this->add('Model_Member');
		$new_memebr->set($data);
		$new_memebr->save();

		$in_right=false;
		if($available_location['side']=='right') $in_right=true;

		$parent = $this->add('Model_Member');
		$parent->load($available_location['id']);
		$parent[$available_location['side'].'_id'] = $new_memebr->id;
		$parent->save();

		$parent_in_right = false;
		if($parent['parent_side'] == 'right') $parent_in_right = true;

		$new_memebr['parent_id'] = $parent->id;
		$new_memebr['parent_side'] = $available_location['side'];
		$new_memebr->save();

		if($in_right && $parent_in_right && $parent['parent_id']){
			$parent_parent = $parent->ref('parent_id');
			$parent_parent['completed_2_4'] = true;
			$parent_parent->save();

			
			if($parent_parent['sponsor_id']){
				$sponsor = $parent_parent->ref('sponsor_id');
				$sponsor['completed_sponsored_ids'] = $sponsor['completed_sponsored_ids'] +1;
				$sponsor->save();
			}

		}
	}

	function loadRoot(){
		$this->setOrder('id');
		$this->tryLoadAny();
		return $this;
	}



}
