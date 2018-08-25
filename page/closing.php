<?php


class page_closing extends Page {
	
	function page_index(){

		$closing = $this->add('Model_ClosingDetails');
		$closing = $this->add('Model_Closing');

		$grid = $this->add('Grid')->addClass('closing_grid');
		$grid->setModel($closing);

		if($_GET['details']){
			$this->app->redirect($this->app->url('./details',['closing_id'=>$_GET['details']]));
		}

		$grid->addColumn('Button','details');

		$closing_btn = $grid->addButton('Perform Closing');
		$closing_btn->js('click')->univ()->frameURL("Do Closing",$this->app->url('./close'));
	}

	function page_close(){

		$form = $this->add('Form');
		$form->addField('closing_name')->validate('required');
		$form->addSubmit('Do Closing');

		if($form->isSubmitted()){

			// transaction safe 
			
			// save closing and get id for next closing details

			$closing = $this->add('Model_Closing');
			$closing['name'] = $form['closing_name'];
			$closing['till_id'] = $this->add('Model_Member')->setOrder('id','desc')->tryLoadAny()->get('id');
			$closing->save();

			$closing_id = $closing->id;

			// (completed_2_4 and ! paid_completed) OR (completed_sponsored_ids > 0 )
			// Copy from member to closing details

			$this->query(
				"INSERT INTO closing_details
							(id, closing_id, member_id, roi_to_pay     						  , completed_sponsored_ids)
					SELECT 	  0, $closing_id, id      , IF(paid_completed=0,completed_2_4,0)  , completed_sponsored_ids FROM member WHERE  (completed_2_4 = 1 AND paid_completed = 0) OR completed_sponsored_ids > 0
				");

			// set amounts 

			$this->query("UPDATE closing_details SET roi_amount = 3000 WHERE roi_to_pay=1 AND closing_id = $closing_id");
			$this->query("UPDATE closing_details SET completed_sponsored_ids_amount = completed_sponsored_ids * 600 WHERE completed_sponsored_ids > 0 AND closing_id = $closing_id");
			$this->query("UPDATE closing_details SET total = IFNULL(roi_amount,0) + IFNULL(completed_sponsored_ids_amount,0) WHERE closing_id = $closing_id");
			$this->query("UPDATE member SET paid_completed = 1 WHERE completed_2_4 = 1 AND paid_completed=0");
			$this->query("UPDATE member SET completed_sponsored_ids = 0 ");

			// set paid_completed = 1 where (completed_2_4 and !paid_completed)
			// set completed_sponsored_ids=0 where (completed_sponsored_ids > 0 )

		}

	}
	
	function page_details(){
		$id = $this->app->stickyGET('closing_id');

		$closing = $this->add('Model_Closing')->load($id);

		$this->add('View_Info')->set($closing['name'].' '. $closing['created_at']);

		$closing_details = $this->add('Model_ClosingDetails')
							->addCondition('closing_id',$id)
							;

		$grid = $this->add('Grid');
		$grid->add('misc/Export');


		$grid->setModel($closing_details);
		$grid->addTotals(['total']);

	}

	function query($query, $gethash=false){
		if($gethash){
			return $this->app->db->dsql()->expr($query)->get();
		}else{
			return $this->app->db->dsql()->expr($query)->execute();
		}
	}
}