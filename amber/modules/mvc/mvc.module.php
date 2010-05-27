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
final class Module_MVC extends Amber_Module implements AmberModule {
    private $controllers = array();

    public function __path() { return __FILE__; }
    public function __name() { return 'mvc'; }
    
    public function __init() {
	$model_master = APPLICATION_PATH . DS . 'models' . DS . '_master.php';
	$this->classLoad( 'view', 'model', 'controller' );
	if (file_exists($model_master)) { include_once($model_master); }
	$controller_master = APPLICATION_PATH . DS . 'controllers' . DS . '_master.php';
	if (file_exists($controller_master)) { include_once($controller_master); }
	$item1 = new Amber_Autoloader();
	$item1	->setName('controller')
		->setPath(APPLICATION_PATH . DS . "controllers")
		->setPreg( '/^Controller_(.*)$/')
		->setFile('{class}.controller.php')
		->attach();

	// Autoload for Application Models
	$item1 = new Amber_Autoloader();
	$item1	->setName('models')
		->setPath(APPLICATION_PATH . DS . "models")
		->setPreg( '/^Model_(.*)$/')
		->setFile('{class}.model.php')
		->attach();
    }
    public function loadMVC($controller) {
	$c = "Controller_" . ucwords(strtolower($controller));    
	// check to see if we are going to auto load the model			    
	try {
	    $controller = new $c();		// create new controller
	    // lets load of the model if it autoloads 
	    $this->attach($controller);
	    $this->controllers[] =& get_class($controller);
	} catch (Exception $e) { throw new Exception($e->getMessage()); }
	return $controller;
    }
    public function initMVC() {
	$controller = current($this->controllers);
	$controller = $this->attached_objects[$controller];
	if (method_exists($controller,'attach')) {
	    $this->attach($controller);
	} else {
	    // throw error? 
	}
	$active_modules = array();
	/*
	if (count($this->modules) > 0) 		// make sure we have some modules 
	{
		foreach ((array) $this->modules as $module)
		{
			$active_modules[] = &$module;
			if (method_exists($module,'controllerLoad')) { $module->controllerLoad($controller); }
		}
	}
	*/
	// set hook sequence array. this is the order all system/application hooks will be called.
	$hook_sequence 	= array(
				    HOOK_SEQ_MASTER,
				    HOOK_SEQ_SYSTEM,
				    HOOK_SEQ_INIT,
				    HOOK_SEQ_MODELS,
				    HOOK_SEQ_INDEX,
				    HOOK_SEQ_FINISHED
				);
	
	if (count($hook_sequence) < 1) { throw new Exception('*** Hook Sequence array unset.');	}
	__log("*** INIT APP: " . get_class($controller));
	$models = array();
	// if (array_key_exists(get_class($controller),$this->model)) { $models = & $this->model[get_class($controller)]; }
	foreach ( (array) $hook_sequence as $sequence ) {
	    $method = '';
	    $object_order = array('models', 'application', 'controller');
	    switch ($sequence) {
		case HOOK_SEQ_MASTER:
		    $method	= '__initMaster';
		    $object_order = array('controller');
		    break;
		case HOOK_SEQ_INIT:
		    $method = '__init';
		    break;
		case HOOK_SEQ_SYSTEM:
		    $method = 'initSystem';
		    break;
		case HOOK_SEQ_INDEX:
		    $method = '__index';
		    break;
		case HOOK_SEQ_FINISHED:
		    $method = array('__finishedMaster','__finished');
		    break;
		default: // throw exception					
	    }
	    $i = 0;
	    while (1) {
		if (100 < $i) { throw new Exception(); }
		$i++;
		if (is_array($method)) {
		    $_method = current($method);
		    if ($i > count($method)) break;						
		} else {
		    $_method = $method;
		}
		foreach ((array) $object_order as $obj)	{
		    // set the order which we envoke the methods
		    switch ($obj) {
			case 'application';
			    if (method_exists($this,$_method)) { $this->$_method(); }
			    break;
			case 'models':
			    if (count($models) > 0) {
				foreach ($models as $_model) {
				    if  (method_exists($_model,$_method)) { $_model->$_method(); }
				}
			    }
			    break;
			case 'controller';	
			    if (method_exists($controller,$_method)) { $controller->$_method(); }
			    break;
		    }
		}
		if (is_array($method)) next($method); else break;
	    }
	}
    } 
    public function loadModel($controller,$mod_array = null)  {
	$load_models = array();
	$model_array = array();
	if (is_object($controller)) {
	    try {
		if (count($mod_array) > 0) {
		    foreach ($mod_array as $item) { $load_models[] = ucwords($item); }   
		} else  { throw new Exception(); }
	    } catch (Exception $e) {
		$class 		= get_class($controller);
		$model 		= substr($class, ((count($class) - 12) * -1) );
		$load_models[]  = $model;
	    }
	} else {
		
	}
	// check for model if it doesnts exist throw error
	foreach ( (array) $load_models as $model) {
	    $path 	= APPLICATION_PATH . DS . 'models' . DS . strtolower($model) . '.model.php';
	    if (!file_exists($path))  {  throw new Exception("Model could not be loaded for " . get_class($controller)); }
	    $model 		= 'Model_' . $model;
	    $model_object	= new $model( $controller );
	    $this->attach($model_object);
	    $this->models[$model] = get_class($model_object);
	    $model_object->__initSystem();
	}
    }
	
    
}


?>