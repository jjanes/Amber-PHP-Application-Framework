<?php
// @todo commandline exceptions 

class AmberCommandLine extends Amber_Application
{
	public $args = array(), $scriptdir;
		
	public function __initSystem()
	{
		$args = $_SERVER['argv'];
		$this->scriptdir = $args[0];
		
		unset($args[0]);
		$this->args = $args;	
		

		if (method_exists($this,'main'))
		{
			$this->main($args);
		}
		
		
		
		echo "\n\n\n";
	}
	

}

class CL_Params {
	private $handler = 'handler', $paremeters = array();
	public $last_arg, $container, $last;
	
	public function addParam($param,$executer)
	{
		$this->parameters[] = array($param,$executer);		
		return $this;
	}
	
	public function check($args)
	{
		$params_master = array();
		$first_params  = array();
		$current_args  = array();
		$invalid_flags = array();
		$current 	   = 0;
		
		$i = 0;
		foreach ($this->parameters as $param)
		{
			$param = $param[0];
				
			if (substr($param,0,2) == '--')
			{
				$params_master[$i] = $param;
			}	
			else 
			{
				$first_params[$i] = $param;
			}
			$i++;	
		}
		$match_found = false;
		
		$i = 0;
		foreach ($args as $arg)
		{
			$_args  = trim(join(' ', $args));
			
			if ($i == 0 && substr($arg,0,2) != '--')
			{
				if (in_array($arg,$first_params) === true)
				{
					
					$key 			= array_search($arg,$first_params);
					$match_found 	= true;
					
					list ($p,$exec) = $this->parameters[$key];
			
					$x = (strlen($_args) - strlen($p)) * -1;
					$remaining_args = trim(substr($_args, $x ));
					
					
					if (is_object($exec) && get_class($exec) == __CLASS__)
					{
						if (is_object($this->container) && !is_object($exec->container))
						{
							$exec->attachContainer($this->container);
						}
						
						$exec->last_arg = $p;
						$exec->last		= $this;
						$exec->check(explode(' ',$remaining_args));
					}
					else 
					{
						$this->exec($exec,$remaining_args);
					}
				}
				break;
			}
			else if (substr($arg,0,2) == '--')
			{
				$x = (strlen($arg) - 2) * -1;
				$current = substr($arg,$x);
				
				// @todo check for dups
				$current_args[$current] = array();			
				
				if (in_array($arg,$params_master) === true)
				{
					$key 			= array_search($arg,$params_master);
					$match_found 	= true;
					
					if (count($this->parameters[$key]) > 1)
					{
						list ($p,$exec) = $this->parameters[$key];
						
						if ($exec)
						{
							$this->exec($exec,$remaining_args);
						}
					}
					
				}
				else 
				{
					// @todo a halt on invalid flags
					$invalid_flags[] = $arg;
				}				
			}
			else 
			{
				$current_args[$current][] = $arg; 				
			}
			
			$i++;
		}
		
		if ($match_found)
		{
			if (count($current_args) > 0)
			{	
				$good_params = array();
	
				foreach ($current_args as $param => $args)
				{
					$good_params[] = array($param,join(' ',$args));				
				}
				
				$this->exec($this->handler,$good_params);
			}
			else 
			{
				
			}
		}
		else if (method_exists($this->container,'no_params'))
		{
			$this->container->no_params($args);
		}
		
		return $this;
	}
	
	public function attachHandler($handler)
	{
		$this->handler = $handler;
		return $this;	
	}
	
	private function exec($exec,$params = null)
	{
		if (is_object($this->container))
		{
			if (method_exists($this->container,$exec))
			{
				try {
					$this->container->$exec($params);
				} 
				catch (Exception $e)
				{
					throw new Exception($e->getMessage());	
				}
			}
			else 
			{
				throw new Exception("Method: " . $exec . " does not exists in class " . get_class($this->container));
			}
		}
		else if ($params) 
		{
			try {
				$exec($params);
			} 
			catch (Exception $e)
			{
				throw new Exception($e->getMessage());	
			}
		}
				
	}
	
	public function attachContainer($object)
	{
		$this->container = & $object;
		return $this;
	}
	
	
	
}




?>