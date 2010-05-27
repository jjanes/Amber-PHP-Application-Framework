<?php
function myErrorHandler($errno, $errstr, $errfile, $errline)
{	
    switch ($errno) {
	    case E_USER_NOTICE:
	    	// echo "<pre>";print_r(debug_backtrace());echo "</pre>";
        if (WB_DA)
    {
	    __workerBee()->attach(
	    	new __wbMessage($type,$errno, $errstr, $errfile, $errline,debug_backtrace())
	    );
    }
	    	$type = "log";
			__log("[$errno] ($errfile) $errstr<br />\n");
	        break;
	    default:
	    	$type = "errror";
	       //  __error("[$errno] ($errfile) $errstr<br />\n");
	        break;
    }
    


    /* Don't execute PHP internal error handler */
    return true;
}
set_error_handler("myErrorHandler");

function __error($string) 
{
	if (function_exists("__inDevelopement"))
	{	
		if (WB_DA)
		{
			// __workerBee()->__error($string);
		}
		else if (__inDevelopement())
		{
			echo "**** $string <br />";
		}
		
	}
	
}