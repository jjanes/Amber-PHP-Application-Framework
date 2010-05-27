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
abstract class System_Controller extends Amber_Attachable_Class
{
	private $parent;
	private $active_model;
	private $viewActive 	= false;
	public  $loadViews 	= true;
	public  $loadModels 	= true; 
	protected  $model;
	
	abstract public function __init();
	abstract protected function __index();
	abstract protected function __finished();
	
	final public function __initSystem() {
		$this->initView();
		if (count($controllers) > 0) {
			$con_name = $controllers[0]; // if controller exists 
		} else {
			// 404 maybe throw an load a default  maybe load 404 controller
		}
		
		if (isset($con_name)) {
			$controller 	= $this->loadController($con_name);
			if (method_exists($controller,'attach')) {
				$controller->attach(&$this);
			} else {
				// throw error? 
			}
			$active_modules = array();
			if (count($this->modules) > 0) { // make sure we have some modules 
				foreach ((array) $this->modules as $module) {
					$active_modules[] = &$module;
					if (method_exists($module,'controllerLoad')) { $module->controllerLoad($controller); }
				}
			}
			// set hook sequence array. this is the order all system/application hooks will be called.
			$hook_sequence 	= 	array(
							HOOK_SEQ_MASTER,
							HOOK_SEQ_SYSTEM,
							HOOK_SEQ_INIT,
							HOOK_SEQ_MODELS,
							HOOK_SEQ_INDEX,
							HOOK_SEQ_FINISHED
						);
			
			if (count($hook_sequence) < 1)
			{
				throw new Exception('*** Hook Sequence array unset.');	
			}
			__log("*** INIT APP: " . get_class($controller));
			
			$models = array();
			
			if (array_key_exists(get_class($controller),$this->model))
			{
				$models = & $this->model[get_class($controller)];
			}
			
			foreach ( (array) $hook_sequence as $sequence)
			{
				$method = '';
				
				$object_order = array('models', 'application', 'controller');
				
				switch ($sequence)
				{
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
					default:
						// throw exception					
				}
				
				$i = 0;
				
				while (1)
				{
					if (100 < $i)
					{
						throw new Exception();	
					}
					
					$i++;
					
					if (is_array($method))
					{
						$_method = current($method);
						if ($i > count($method))
							break;						
					}
					else
					{
						$_method = $method;
					}
					
					foreach ((array) $object_order as $obj)		// set the order which we envoke the methods
					{
						switch ($obj)
						{
							case 'application';
								if (method_exists($this,$_method))
								{
									$this->$_method();
								}
								break;
							case 'models':
								if (count($models) > 0)
								{
									foreach ($models as $_model)
									{
										if  (method_exists($_model,$_method)) { $_model->$_method(); }
									}
								}
								break;
							case 'controller';	
								if (method_exists($controller,$_method))
								{
									$controller->$_method();
								}
								break;
						}
					}
					if (is_array($method))
						next($method);
					else
						break;
				}
			
			
			}
			
			
		}	// end if isset($con_name)
		else
		{
			// no controller .. throw error?
		}
		
		if (method_exists($this,"__init"))
		{
			$this->__init();
		}
	}
	
	final function loadModel($model = null) 
	{
		if (func_get_args() > 1)
		{
			$args = func_get_args();
			$this->mvc()->loadModel($this,$args);
		}
		else 
		{
			$this->mvc()->loadModel($this);
		}
	}
	final protected function mvc()
	{
		// $mod = $this->_getApp()->_modules();
		return $this->attached_objects['Module_MVC'];
		
	}
	final public function model($mod = null)
	{
		$models = $this->mvc()->model(get_class($this));
		
		if ($mod !== null)
		{
			if (is_numeric($mod))
			{
				if (isset($models[$mod]))
				{
					$this->active_model = get_class($models[$mod]);
				}
			}
			else
			{
				$this->active_model = 'Model_' . ucwords(strtolower($mod));
			}
			
		}
	
		if (!isset($this->active_model))
		{
			$this->active_model = 'Model_' . $this->name();
		}
		
		$_model = null;
		
		foreach ( (array) $models as $model)
		{
			# echo "\n" . get_class($model) . ' ' . $this->active_model . "\n";
		
			if (get_class($model) == $this->active_model)
			{	
				$_model = $model;
			}
		}
		if (!is_object($_model))
		{
			$_model = $models[0];
		}

		return $_model;
	}
	
	final function name()
	{
		$name = explode('_',get_class($this));
		
		return $name[1];
	}
	
	final public function parent()
	{
		return $this->parent;
	}
	
	
	protected function initView()
	{
	
		if ($this->viewActive)
		{
			return true;
		}
		
		$this->viewActive = true;
		
		// lets init views
		if ($this->loadViews)
		{			
			$_PATH = APPLICATION_PATH . DS . 'views' . DS ;
			$view_object = null;
			 
			try {
				if (file_exists($_PATH . "_init.view.php"))
				{
					require_once ($_PATH . "_init.view.php");
					
					if (class_exists('View_Initialize'))
					{
						$view_object = new View_Initialize();
						
						if (!is_subclass_of($view_object,'System_View'))
						{
							throw new Exception();
						}
					}
					else
					{
						throw new Exception();
					}
				}
				else
				{
					throw new Exception();	
				}
			}
			catch (Exception $e)
			{
				if (class_exists('System_View'))
				{
					$view_object = new System_View();
				}
				else
				{
					throw new Exception('Fatal Error');
				}
			}
			$view_object->attach($this);
			$this->view = & $view_object;
			
		}		
	}
	
	protected function view()
	{
		return $this->view;
	}
	
	
}