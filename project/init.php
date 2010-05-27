<?php
/*
 * 
 *  application front controller
 * 
 * 
 * 
 * 
 * 
 * 
 */
include("amber/amber.php"); // load init script for framework

final class Test_Application extends Amber_Application {
	// your front controller must always extend the amberapplication class
	protected $modules = array('users','routes','mvc');

	protected function __path()
	{
		return dirname(__FILE__);
	}	
	public function __desc()
	{
		// we will put a description of our app here
		return "Amber Test Application";
	}
	protected function __init()
	{
		$this->loadlib('database');
		
		$db = new Database_Connection();					// create new db connection
		$cid = $db->connect("mysql://root:whatis321@localhost/test");		// connect 
		
		$this->loadLib('sessions');						// load lib 
	}

	protected  function __index() // application index
	{
		
	}
	protected function __finished() 
	{
		Database_Connections::destroy_all();
	}
	
	
	// module hooks
	 
	public function __initRoutes() 							// we are initilizing what uri will load what controller 
	{
		// init routs for application 
		$this->attach(
			new Route(
				array(
					'preg'		=> '/^(.*)\/joe$/',
					'controller'	=> 'test'
				)
			)
		);
		$this->attach(new Route(array('preg' => '/^(.*)$/', 'controller' => 'index')));
	}	
	
}


?>