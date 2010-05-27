<?php
abstract class Master_Controller extends System_Controller {
    public function __initMaster() {
	$this->initView();
	$view = $this->view();
	$view->set('test','test');
	$view->set(
		array(
			'test1' => 'test1',	
			'test2'	=> 'test2'
		)
	);
	$view->load('head');
    }
    function __finishedMaster() {
	$this->view()->load('foot');
	$this->view()->display();
    }
}
?>