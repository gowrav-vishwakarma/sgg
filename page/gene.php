<?php



class page_gene extends Page {

	function init(){
		parent::init();

		$array = [];

		$memo_root_id = $this->app->recall('root_id',false);
		
		if(!$memo_root_id){
			if(!isset($_GET['id']) || $_GET['id']=='#') 
				$id = $this->add('Model_Member')->loadRoot()->get('id');
			else
				$id= $_GET['id'];
		}else{
			$id=$memo_root_id;
			$this->app->forget('root_id');
		}
		// echo '[{"id":1,"text":"Root node","children":[{"id":2,"text":"Child node 1","children":true},{"id":3,"text":"Child node 2"}]}]';
		// exit;

		if($_GET['id']=='#'){
			$root_id = $this->add('Model_Member')->load($id);
			
			$backgroundcolor = 'black';
			if($root_id['completed_2_4']){
				$backgroundcolor='orange';
				if($root_id['paid_completed']){
					$backgroundcolor='green';
				}
			}

			$array[] = ['id'=>$root_id['id'],
							'text'=>$root_id['member_name'].' ['.$root_id['id'].']',
							'parent'=>'#',
							'children'=>$root_id['left_id']?true:false,
							'li_attr'=>['title'=>$this->getTitle($root_id),'style'=>'color:'.$backgroundcolor]
						];
			echo json_encode($array);
			exit;
		}

		$downline = $this->add('Model_Member')
						->addCondition('parent_id',$id);

		foreach ($downline as $key => $data) {
			$backgroundcolor = 'black';
			
			if($data['completed_2_4']==1){
				$backgroundcolor='orange';
				if($data['paid_completed']==1){
					$backgroundcolor='green';
				}
			}

			$array[] = ['id'=>$data['id'],
						'text'=>$data['member_name']. ' ['.$data['id'].']',
						'parent'=>$data['parent_id'],
						'children'=>$data['left_id']?true:false,
						'li_attr'=>['title'=>$this->getTitle($data),'style'=>'color:'.$backgroundcolor]
					];
		}
		// $array = [
		// 		['id'=>1,'text'=>'a','parent'=>'#'],
		// 		['id'=>2,'parent'=>1,'text'=>'ac','children'=>true],
		// 		['id'=>3,'text'=>'c','parent'=>'#']
		// 	];

		echo json_encode($array);
		exit;
	}

	function getTitle($model){

		return $model['name'];

		if($model['greened_on'] !== null)
			$greened_on_date = date("d M Y", strtotime($model['greened_on']));
		else
			$greened_on_date = "--/---/----";
		if($model['kit_item_id']){
			$kit_model = $this->add('xepan/commerce/Model_Item')->tryLoad($model['kit_item_id']);
			$kit_item = $kit_model['sku'];
		}
		else
			$kit_item="-";

		$str = "<table class='tooltipdetail-generation'  style='width:100%;text-align:left;'>
					<tr>
				  		<th valign='top' style='width:30%;'>Name</th>
				  		<th> : ".$model['name'].' ('.$model['user'].')'."</th>
					</tr>
					<tr>
				  		<th valign='top'>Reg Date</th>
				  		<th> : ".date("d M Y", strtotime($model['created_at']))."</th>
				  	</tr>
				  	<tr>
				  		<th valign='top'>Act Date</th>
				  		<th> : ".$greened_on_date."</th>
				  	</tr>
				  	<tr>
				  		<th valign='top'>Package</th>
				  		<th>:".$kit_item."<br/>:SV:(".$model['sv'].") BV:(".$model['bv'].")</th>
				  	</tr>
				  	<tr>
				  		<th valign='top'>Intro</th>
				  		<th>:".$model['introducer']."</th>
				  	</tr>
					</table>
					<br/>
					<table style='width:100%;text-align:left;' class='tooltipdetail-generation'>
						<tr>
				    		<th valign='top' style='text-align:left;width:50%;'>Total Team (Left)<br/>".$model->newInstance()->addCondition('path','like',$model['path'].'A%')->count()->getOne()."</th>
				    		<th style='text-align:right;width:50%;'>Total Team (Right)<br/>".$model->newInstance()->addCondition('path','like',$model['path'].'B%')->count()->getOne()."</th>
					    </tr>
					    <tr>
					    	<th><br/></th>
					    	<th><br/></th>
					    </tr>
						<tr>
							<th valign='top' style='text-align:left !important;width:50%;'>Total SV (Left)<br/>".$model['total_left_sv']."</th>
					    	<th style='text-align:right !important;width:50%;'>Total SV (Right)<br/>".$model['total_right_sv']."</th>
						</tr>
						<tr>
					    	<th><br/></th>
					    	<th><br/></th>
					    </tr>
					    <tr>
					    	<th valign='top' style='text-align:left;width:50%;'>Month Self BV<br/>".$model['month_self_bv']."</th>
					    	<th style='text-align:right;width:50%;'>Accumulated BV<br/>".$model['total_month_bv']."</th>
					    </tr>
					</table>
				  ";

		$str = str_replace( "\'","'", $str);
		$str = str_replace("\n", "", $str);
		return $str;
	}

	function getTitle_old($model){
		if($model['greened_on'] !== null)
			$greened_on_date = date("d M Y", strtotime($model['greened_on']));
		else
			$greened_on_date = "--/---/----";
		$str=  
				$model['name']. " [".$model['user']."]".
				"<br/>Jn: ". date("d M Y", strtotime($model['created_at'])). 
				"<br/>Gr: ". $greened_on_date. 
				"<br/>Kit: ". ($model['kit_item']?:'') ." SV(".$model['sv'].")"."BV(".$model['bv'].")".
				"<br/>Intro: ". $model['introducer'] .
				"<br/>Month Self BV: ". $model['month_self_bv'].
				"<br/>Commulative Month BV: ". $model['total_month_bv'].
				"<br/>Rank: ". $model['current_rank'].
				"<br/>Slab Percentage: ". $model->ref('current_rank_id')->get('slab_percentage')
				// "<br/>Slab Percentage: ". $model['temp']
				;
				
		$str= str_replace("'", "\'", $str);
		$str= str_replace("\n", "", $str);
		return $str;
	}

}