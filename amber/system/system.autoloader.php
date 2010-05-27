<?php
class Amber_Autoloader
{
	public $name;
	public $path;
	public $preg;
	public $file;
	public $class;
	
	
	function __construct($args = array())
	{
		foreach ($args  as $key => $value)
		{
			switch ($key)
			{
				case 'name':	
					$this->setName($value);	
					break;
				case 'path':	
					$this->setPath($value);
					break;
				case 'preg':	
					$this->setPreg($value); 
					break;
				case 'attach': 	
					if ($value) { $this->attach(); }
					break;
			}		
		}
	}
	
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	public function setPath($path)
	{
		if (file_exists($path))
		{
			$this->path = $path;
		} else {
			$this->path = "PATH_DOES_NOT_EXISTS";
			__log( "$path does not exists.");
		}
		
		return $this;
	}
	public function setPreg($preg)
	{
		$this->preg = $preg;
		return $this;
	}
	public function setFile($file)
	{
		$this->file = $file;
		return $this;
	}
	
	
	public function attach()
	{
		if (!isset($this->name))
		{
			$this->name = Autoloader::uniqueName();
		}
		
		Autoloader::attach($this);
		
	}
	
	public function load()
	{
		if (isset($this->class))
		{
			preg_match($this->preg,$this->class,$a);

			foreach ($a as $match)
			{
				if ($match != $this->class)
				{
					continue;
				}
			}
			if ($match)
			{
				$file = preg_replace('/{class}/',strtolower($match),$this->file);
				if (is_array($this->path))
				{
					foreach ($this->path as $path)
					{
						$full_path = $path . DS . $file;
						if (file_exists($full_path))
						{
							include ($full_path);
							continue;
						}
					}
					
				} 
				else 
				{
					$full_path = $this->path . DS . $file; // create teh full path 
					
					__log("full path $full_path");
					
					if (file_exists($full_path))
					{
						include ($full_path);
					} 
					else 
					{
						__log("autoload $full_path does not exists.");
					}
				}
			}
			
			
		}
	}
	public  function isMatch($class) 
	{
		try {
			
			$preg = $this->preg;
			
			if (preg_match($preg,$class)) {
				$this->class = $class;
				return true;
			}
			else 
			{
				return false;
			}
		} 
		catch (Exception $e)
		{
			// at some point we want to log this some how
			throw new Exception("Cannot use " . $this->preg);
		}
		
	}
	
}

class Autoloader
{
	static $autoload = array();

	function __construct() 
	{
		throw new Exception("");
	}
	
	function attach($object)
	{
		if (!key_exists($object->name,self::$autoload))
		{
			self::$autoload[$object->name] =& $object;
		}
	}
	
	function uniqueName()
	{
		$name = rand(100,9999);
		while (key_exists($name,self::$autoload))
		{
			$name  = rand(100,9999);
		}
		return $name;
	}
	
	function load($class)
	{
		foreach (self::$autoload as $obj)
		{
			if ($obj->isMatch($class))
			{
				$obj->load();
			}
		}
		
	}
}

if (function_exists("__autoload"))
{
	throw new Exception("function __autoload has already been declared");	
}
else 
{
	function __autoload($class)
	{
		switch ($class)
		{
			default:
				Autoloader::load($class);
		}
		
	}	
}



?>