<?php
/*******************************************************************************************************************************
 * Copyright (c) 2010, John R Janes
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the Amber Application Framework nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *******************************************************************************************************************************/
// amber module
// smart ajax
// HOOK_SEQ_MASTER, HOOK_SEQ_SYSTEM, HOOK_SEQ_INIT, HOOK_SEQ_MODELS, HOOK_SEQ_INDEX, HOOK_SEQ_FINISHED 
class Module_Routes extends Amber_Module implements AmberModule 
{
    protected $controllers;
    private $routes = array();
    
    public function __children() { return array('route'); }
    public function __name() { return 'routes'; }
    public function __path() { return __FILE__; }
    
    public function __init()
    {
	$this->classLoad('routes');
	
	// $this->hook('^load','module','load');
	// $this->hook('^onLoad','module:controller','controllerLoaded');
    }

    public function __index()
    {
	if ($this->moduleLoaded('mvc'))
	{
	    if (method_exists($this->_getApp(),'__initRoutes'))
            {
	    	$this->_getApp()->__initRoutes(); 
	    }
	
	    $controllers = $this->checkRoutes();
	    
	    if (count($controllers) > 0)
	    {
		$controller = current($controllers);
		
		$mvc = $this->getModuleInstance('mvc');
		
		if (method_exists($mvc,'loadMVC'))
		{
		    $mvc->loadMVC($controller);
		    $mvc->initMVC();
		}
		
	    }
	    else
	    {
		// @todo 404 error handle this later
	    }
	}
	else
	{
	    throw new Exception(__CLASS_ . " requires the mvc module.");
	}
    }
    public function __attach($object)
    {
	switch (strtolower(get_class($object)))
	{
	    case 'route':
		$this->routes[] =& $object;
		break;
	    default:
		get_class($object);
	}
    }
    final public function checkRoutes()
    {
	    $controllers = array();	    
	    foreach ( (array) $this->routes as $object)
	    {
		if ($object->isMatch())						// check if we have a match		 
		{		
			$controllers[] = $object->controller;			// add controller to array
		}	
	    }
  
	    return $controllers;		
    }
}



?>