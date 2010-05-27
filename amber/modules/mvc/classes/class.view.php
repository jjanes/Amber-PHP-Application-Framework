<?php
DEFINE ('APPLICATION_VIEWS', APPLICATION_PATH . DS . 'views');

class System_View extends Amber_Attachable_Class
{
    private $controller;
    protected $displayQueue;
    public $files 	= array();
    public $args	= array();
    public $built;
    
    protected $stopLoad = false;
    
    final public function __construct()
    {
	if (method_exists($this,'__init'))
	{
	    $this->__init();
	}
	
	if (!$this->stopLoad)
	{
	    
	    
	}

	
    }
    
    public function load($file = null)
    {
	// @todo maybe intergrate differnt view managements
	$_PATH = APPLICATION_VIEWS . DS . 'template' . DS . $file;
	if (file_exists($_PATH) && is_file($_PATH))
	{
	    $this->files[] = $_PATH;
	}
	else if (file_exists($_PATH . '.php') && is_file($_PATH . '.php'))
	{
	    $this->files[] = $_PATH . '.php';
	}
	else
	{
	    throw new Exception();
	}
	return $this;
    }
    

    protected function controller()
    {
	return $this->controller;
    }
    
    public function clear()
    {
	$this->args = array();
	return $this;
    }
    public function display()
    {
	echo $this->output();
	return $this;
    }
    
    public function build()
    {
	foreach ($this->files as $file)
	{
	    ob_start();
	    extract($this->args);
	    require($file);
	    $content = ob_get_contents();
	    ob_end_clean();
	    $this->built[] = $content;
	
	}
	return $this;
    }
    public function output()
    {
	if (count($this->built) < 1)
	{
	   $this->build();
	}
	 return join('',$this->built);
    }
    
    public function set()
    {
	switch (func_num_args())
	{
	    case 1:
		$args = func_get_arg(0);
		if (is_array($args))
		{
		    foreach ($args as $key => $value)
		    {
			$this->args[$key] = $value; 
		    }
		}
		else
		{
		    throw new Exception();
		}
		break;
	    case 2:
		list ($key,$value) 	= func_get_args();
		$this->args[$key] 	= $value; 
		break;
	    default:
		throw new Exception();
	}
	return $this;
    }
    
    
    
}



?>