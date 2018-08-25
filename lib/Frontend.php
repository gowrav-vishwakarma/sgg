<?php
class Frontend extends ApiFrontend {

    public $title = "Estimator";

    function init() {
        parent::init();

        $this->dbConnect();
        $this->api->pathfinder
            ->addLocation(array(
                'addons' => array('vendor','shared/atk4-addons'),
                'template'=>array('.','shared/atk4-addons')
            ))
            ->setBasePath($this->pathfinder->base_location->getPath());
        
        $this->add('jUI');

        $this->today = date('Y-m-d',strtotime($this->recall('current_date',date('Y-m-d'))));
        $this->now = date('Y-m-d H:i:s',strtotime($this->recall('current_date',date('Y-m-d H:i:s'))));
       
        // If you wish to restrict access to your pages, use BasicAuth class
        $auth=$this->add('BasicAuth');
        $auth->setModel('Staff','username','password');
            // use check() and allowPage for white-list based auth checking
        $auth->check();
      
        $m=$this->add('Menu',null,'Menu');
        $m->addItem('Home','index');
        $m->addItem('Members','members');
        $m->addItem('Structure','structure');
        $m->addItem('Closings','closing');
        $m->addItem('Config','config');
        $m->addItem('Logout','logout');
            ;

        $this->addLayout('UserMenu');

        $this->template->appendHTML('js_include',
                '<style> @media print{ .atk-layout-row:first-child, .atk-form {display: none !important; height:0}'."}</style>\n");

    }

}
