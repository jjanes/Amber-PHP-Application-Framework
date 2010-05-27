<?php
// workerBee for agile php developement.

class workerBee {
	private $status_messages = array();

	
	function __log($string)
	{
		$this->status_messages[] = array('log',$string,time());
	}
	
	
	function draw()
	{
		echo "<pre>";
		print_r($this->status_messages);
		echo "</pre>";
	}
	
	
}