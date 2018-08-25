<?php

/**
* 
*/
class View_GenerationJSTree extends \View{
	
	public $options = [
						'generation-depth-of-tree'=> 3,
						'generation-show-info-on'=>"hover"
	];

	public $distributor = null ;
	public $start_distributor = null ;
	public $start_id = null ;
	public $level = 5 ;

	function init(){
		parent::init();

		$this->app->jui->addStaticInclude('xtooltip');
		$this->app->jui->addStaticInclude('jstree\dist\jstree.min');
		$this->app->jui->addStaticStyleSheet('jstree/dist/themes/default/style.min');

		$this->level = $this->options['generation-depth-of-tree'];

		if(isset($_GET['id'])){
			$this->app->memorize('root_id',$_GET['id']);
		}else{
			$this->app->forget('root_id');
		}

		$this->js(true)->jstree(['core'=>[
						'data'=>[
								'url'=>$this->app->js(null,'return ev.id ==="#" ? "index.php?page=gene":"index.php?page=gene"')->_enclose(),
								"dataType" => "json",
								"data"=> $this->app->js(null,"console.log(ev);return {'id':ev.id}")->_enclose()
							]
					]]);
		$this->js(true)->xtooltip();
	}

	
	
	// function defaultTemplate(){
	// 	return array('xavoc/tool/generation-new-tree');
	// }
}
						
