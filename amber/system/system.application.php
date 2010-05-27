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
abstract class Amber_Application extends amberBase {
	private		$_modules 		= array();
	private 	$_hooks			= array();
	
	abstract protected 	function __path();	
	abstract public 	function __desc();
	abstract protected 	function __init();
	abstract protected 	function __index();
	abstract protected 	function __finished();
	
	final public function __initSystem() {
		// load system file autoloader for smoother development
		__log("Firing up application " . get_class($this));
		// check to see if file exists
	
		if (file_exists(PATH_SYSTEM . DS . "system.autoloader.php")) {
			__log("LOADING SYSTEM autoloader");
			include(PATH_SYSTEM . DS . "system.autoloader.php");
			// $this->__initAutoload();	// load init autoload classes	
		} else {
			// throw a fatal exception - autoloader is required
			throw new Exception("Unable to locate system file auto loader");
		}
		// check to see if application path exists
		if (method_exists($this,"__path")) {
			if (!is_dir($this->__path())) { throw new Exception("__path method must return project directory"); }
			DEFINE('APPLICATION_PATH',$this->__path());
		}  else {
			throw new Exception("__path method must exists in application class");	
		}
		if (!is_subclass_of($this,__CLASS__)) { throw new Exception ("Application must be subclass of " . __CLASS__); } 
		// check modules
		$this->_loadModules();
		if (method_exists($this,"__init")) {
			__log("Application Init");
			// initialize application 
			$this->__init();	
		}
		foreach ($this->_modules as $module) {
			if (method_exists($module,'__index')) { $module->__index(); }
		}
	}
	final public function __modules() {
		return (array) $this->_modules;
	}
	final public function addHook($object) {
		if (!class_exists('Amber_Hook')) {
			if (file_exists('system.hook.php')) {
				require_once("system.hook.php");
			} else {
				throw new Exception();
			}
		}
		if (get_class($object) == 'Amber_Hook') {
			
		}
		
	}
	private function _loadModules() {
		$modules = (array) $this->modules;
		
		if (count($modules) > 0 && defined('INIT_MODULES') && INIT_MODULES) {
			// start module init
			$_paths = array();
			require_once('system.modules.php');			// application users modules - lets load up the system module file. 
			
			__log("Loading " . count($modules) . " modules.");	// report how many modules we are loading. 
			
			foreach ((array) $modules as $_module) {
				if (is_dir(BASE_SYSTEM . DS . DIRECTORY_MODULES . DS . '_' . $_module))	{
					// any module contained in a directory with _ in front is considered a must load over any  modules in use directory
					$module_directory = BASE_SYSTEM . DS . DIRECTORY_MODULES . DS . '_' . $_module;
				}				
				else if (is_dir(APPLICATION_PATH . DS . DIRECTORY_MODULES . DS . $_module)) {
					// rule off thumb always use modules in users application path before we use modules in system directory 
					$module_directory = APPLICATION_PATH . DS . DIRECTORY_MODULES . DS . $_module;
				} else if (is_dir(BASE_SYSTEM . DS . DIRECTORY_MODULES . DS . $_module)) {
					$module_directory = BASE_SYSTEM . DS . DIRECTORY_MODULES . DS . $_module;
				} else {
					throw new Exception('Tried loading a system module that does not exists');
				}
				if (file_exists($module_directory . DS .  $_module . '.module.php')) {
					include($module_directory . DS .  $_module . '.module.php');
					$_paths[$_module]	= $module_directory . DS . $_module . '.module.php';
				} else {
					throw new Exception("required system file was not install module.init.php was not found in $module_directory.");
				}			
			}
			
			$modules = modules::find(); 	// find all loaded module classes

			// interate through each module and execute  	
			foreach ((array) $modules as $module) {
				$module_object 	= &new $module;
				$name 		= $module_object->__name();
				if (key_exists($name,$this->_modules)) {
					throw new Exception($module . ": fatal Conflict with the module name: " . $name . " taken by the class: " . get_class($this->modules[$name]) );
				}
				__log("Loading module: $name");
				
				$module_object->attach($this);
				$this->_modules[$name] = $module_object;
				if (method_exists($module_object,'__init')) {
					__log("Initializing module. ");
					$module_object->__init();
				}	// odd a module should have an init				
			}
			// end of module init
		}		
	}
}
class application  {
	private $current_applications = array();
	
 	function __construct($application = null){
 		foreach (get_declared_classes() as $class){
 			if (AMBER_COMMANDLINE_APP){
 				if (preg_match("/(.*)_CommandLine/i",$class)){ $this->current_applications[] = new $class(); }
 			} else {
				// go threw all declared classes for a application match
	 			if (preg_match("/(.*)_Application/i",$class)) {
					// check for application classes
	 				$app = substr($class,0,strlen($class) - 12); // get applicatio name
	 				switch ($app) {
						// check application
	 					case 'Amber':	
	 						break;
	 					default:
	 						$this->current_applications[] = new $class();	
	 				}
	 			} 
 			}
 		}
 		// @todo lets throw an error here when no application classes exists
 		return $this;
 	}
 	function start($application = null){
 		if ($application){
 		
 			
 		}  else  {
 			foreach ((array) $this->current_applications as $application) {
 				if (method_exists($application,"__initSystem")){ $application->__initSystem(); }
			}
 		}
 		return $this;
 	}
	function paths($app = null)
	{
		
		
	}		
}
function Application($app = null) {
	static $application;
	if (!is_object($application)) {
		$application = new application($app);
	} if (get_class($application) != "application") {
		$application = new application($app);
	}
	return $application;
}
function __log($string) {
	if (function_exists("__inDevelopement")) {	
		if (__inDevelopement()) { echo "**** $string <br />"; }
	}
}
final class amber_class {
	private static $good = array();	
	final function __construct() {
		$args = func_get_args();
		if (!class_exists(current($arg))) { throw new Exception("Class $arg has not been loaded or does not exists."); }
		$class = current($arg);
		array_shift($args);
		$args = array();
		do { $arg = current($args); } while(next($args));
		// elf::$good[] = $classs
	}
}


?>