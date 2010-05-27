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

abstract class Amber_Module  extends amberBase
{
    public function sendHook()
    {
	
    }
    
    final public function getModuleInstance($module)
    {
	$modules = $this->_getApp()->__modules();
	
	if (array_key_exists($module,$modules))
	{
	    return $modules[$module];
	}
	else
	{
	    
	}
    }
    
    final public function _modules()
    {
	return $this->_getApp()->__modules();
    }
    
    final public function moduleLoaded($module)
    {
	$modules = $this->_getApp()->__modules();
	
	if (array_key_exists($module,$modules))
	{
	    return true;
	}
	else
	{
	    return false;
	}
    }
    
    final public function classLoad()
    {
	$args = func_get_args();
	do
	{
	    $arg = current($args);	    
	    $dir = dirname($this->__path());	    
	    $file = $dir . DS . "classes" . DS . "class." . $arg . ".php";
	    if (!file_exists($file)) { throw new Exception("module class file not found: $arg (PATH: $file)"); }
	    include_once($file);
	    
	} while (next($args));
	
    }
    
}

final class modules
{
    public function find()
    {
	$found		= array();
	$classes 	= get_declared_classes();
	
	foreach ($classes as $class)
	{
	    if (preg_match('/^Module_(.*)$/',$class))
	    {
		$found[] = $class;
	    }
	}
	
	return $found;	
    }
}

interface AmberModule
{
    public function __name(); 
    public function __init();
    public function __path();
}
abstract class Amber_Attachable_Class
{
    protected $attached_objects = array();
    
    final function attach(&$object)
    {
	if (is_object($object))
	{
	    if (is_subclass_of($object,'Amber_Module'))
	    {
		if  (!array_key_exists(get_class($object),$this->attached_objects))
		{
		    $this->attached_objects[get_class($object)] = $object;   
		}
	    }	    
	}
    }
}

