<?php
final class Controller_Index extends Master_Controller {
	public function __init() {
		
	}
	public  function __index() {
		$this	->view()
			->load('index');
	}
	public function __finished() {
	
	}
}
?>