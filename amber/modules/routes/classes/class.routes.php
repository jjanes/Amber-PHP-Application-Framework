<?php
class Route {
	public $preg;
	public $controller;
	public $hook;
	
	function __construct($args = array())
	{
		foreach ($args as $key => $value)
		{
			switch ($key)
			{
				case 'preg':
					$this->setPreg($value);
					break;
				case 'controller':
					$this->setController($value);
					break;
				case 'hook':
					$this->setHook($value);
					break;
			}
		}
		
	}
	
	function setHook($hook)
	{
		$this->hook = $hook;
		return $this;
	}
	
	function setController($controller)
	{
		$this->controller = $controller;
		return $this;
	}
	
	function setPreg($preg)
	{
		$this->preg = $preg;
		return $this;
	}
	
	
	function isMatch() 
	{
		try {
			$preg_array = (is_array($this->preg))? preg_match($this->preg): array($this->preg);
			foreach ($preg_array as $preg)
			{
				if (preg_match($preg, $_SERVER['REQUEST_URI'])) 
				{
					return true;
				}
			}
		} 
		catch (Exception $e)
		{
			
		}
		return false;
	}
	
}

