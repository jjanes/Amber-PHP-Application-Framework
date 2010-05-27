<?php
class Controller_Test extends Master_Controller
{
	public  $loadModels = false;
	 
	public function __init()
	{
		$this->loadModel('index','test');
		$this->model = & $this->model(1);
	}
	
	public function __index()
	{
		echo "Test";
		$view = $this->view();
		$view->set('test','test');
		$view->set(
			array(
				'test1' => 'test1',	
				'test2'	=> 'test2'
			)
		);
		$view->load('test');	
	}
	
	
	public function __finished() 
	{

	}

	
	
}
?>