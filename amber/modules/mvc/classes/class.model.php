<?php
class System_Model extends Amber_Attachable_Class
{
	private $parent;
	public 	$model;
	private $return = array();
	protected $extract = array();
	
	final public function extract()
	{
		// print_r($this->extract);
		return extract($this->extract);
	}
	
	function __initSystem()
	{
		if (method_exists($this,"__init"))
		{
			$this->__init();
		}
		
	}
	
	function model($model = null)
	{
	
	}

	public function controller()
	{
		
	}
	
	function bind()
	{
		
	}
	
}
