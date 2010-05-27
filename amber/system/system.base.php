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
abstract class amberBase
{
    protected $attached_objects = array();
    final protected function attach(&$object)
    {	 
	if (is_object($object))
	{
	    if (preg_match("/^(.*)_Application$/", get_class($this)) && is_subclass_of($this,'Amber_Application'))
	    {
		foreach ( (array) $this->__modules() as $module )
		{
		    if (method_exists($module,'__children') && method_exists($module,'__attach'))
		    {
			$children = (array) $module->__children();
	
			if (strtolower(trim($children[array_search(get_class($object),$children)])) == strtolower(trim(get_class($object))))
			{
			    $module->__attach($object);
			}
		    }
		}
	    }
	    else if (is_subclass_of($object,'Amber_Application') && preg_match("/^(.*)_Application$/", get_class($object)))
	    {
		if (is_subclass_of($this,'Amber_Module') || preg_match("/^Module(.*)$/", get_class($this)) || preg_match("/^(.*)_Controller$/", get_class($this)))
		{
		    if (!array_key_exists('Application',$this->attached_objects))
		    {
			$this->parent = &$object;
			$this->attached_objects['Application'] = &$object;
		    }
		}
	    }
	    else if (get_class($object) == 'Amber_Hook')
	    {
		if (is_subclass_of($this,'Amber_Module'))
		{
		    $this->_getApp()->addHook($object);
		}
		else
		{
		    
		}
	    }
	    else if (is_subclass_of($object,'Amber_Attachable_Class') && is_subclass_of($this,'Amber_Module'))
	    {
		if (!method_exists($object,'attach'))
		{
		    throw new Exception("Fatal error");
		}
		
		$this->attached_objects[get_class($object)] = $object;
		$object->attach(&$this);
	    }
	}
	else
	{
    
	}
    }
    final protected function _getApp()
    {
   	if (array_key_exists('Application',$this->attached_objects))
	{
	    return $this->attached_objects['Application'];
	}
	else
	{
	    throw new Exception("Application not found.");
	}
    }
    final protected function getType($object)
    {
	
    }
    final protected function loadLib()
    {
	$array = func_get_args();
	do {
	    $arg = current($array);
	    
	    $file = BASE_SYSTEM . DS . "lib" . DS . "lib." . $arg . ".php";
	    
	    if (!file_exists($file))
		throw new Exception("Could not locate library $arg in [" .  dirname($file) . "]"); 
	    
	    include_once($file);
	 
	} while(next($array));
    }
}