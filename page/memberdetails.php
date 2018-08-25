<?php


class page_memberdetails extends Page {
	
	function page_index(){

		$id = $this->app->stickyGET('id');
		$member = $this->add('Model_Member')->load($id);

		$details = $this->add('View_ModelDetails');
		$details->setModel($member);

		$print_btn = $this->add('Button')->set('Print');

		$print_btn->js('click',$this->js()->univ()->newWindow($this->app->url('./print',['id'=>$id,'cut_page'=>1])),'Print');

		$this->add('HR');
		$this->add('View_Info')->set('Member\'s Direct Sponsors');

		$grid = $this->add('Grid');
		$grid->setModel($member->ref('SponsoredIds'),['id','member_name','parent']);
		$grid->addPaginator(50);

	}

	function page_print(){
		$id = $this->app->stickyGET('id');
		$member = $this->add('Model_Member')->load($id);

		if(!file_exists('templates/memberprint.html')) file_put_contents('templates/memberprint.html', '');
		$template_file = file_get_contents('templates/memberprint.html');

		$v=$this->add('View',null,null,['memberprint']);
		$v->setModel($member);

	}
}