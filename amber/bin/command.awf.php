<?php
require_once("init.php");

class Awf_CommandLine extends AmberCommandLine
{
	protected function main($args)
	{
		

		
		$params 	= new CL_Params();
		$cparams 	= new CL_Params();
		

		$cparams	->addParam('--test','test')
					->addParam('--testing','test2')
					->addParam('new','test1');
	
		$params		->addParam('controller',$cparams)
					->addParam('--test')	
					->addParam('--version','appVersion')	
					->attachContainer($this)
					->attachHandler('rootHandler')
					->check($args);
		
		
		
		
			
		
		echo "\n\n\n\n";
		
		
	}
	
	function rootHandler($params)
	{
		print_r($params);	
	}
	
	function test1($args)
	{
		echo ">>1>> ";
		print_r($args);
	}
	
	function no_params($args)
	{
		echo ">>> nogood \n";
	}
	
	function appVersion()
	{
		echo "\n";
		echo "   +--------------------------------------------------------+\n";
		echo "   | Amber PHP Application Framework                        |\n";
		echo "   | Version v" . AMBER_VERSION . "                                           |\n" ;
		echo "   | Author  " . AMBER_AUTHOR . "                    |\n" ;
		echo "   +--------------------------------------------------------+\n\n";
	}
}


Application();
Application()->exec();


?>