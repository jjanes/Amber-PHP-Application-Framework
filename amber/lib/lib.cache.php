<?php

DEFINE ('CACHE_PATH', APPLICATION_PATH . DS . DIRECTORY_CACHE);

$CACHE_PATH = CACHE_PATH;

if (is_dir($CACHE_PATH) && is_writeable($CACHE_PATH))
{
    // WOOT! we have a writeable cache path
    __cache(CACHE_DIRECTORY,$CACHE_PATH);
    __log('init cache');
}
else
{
    // throw error
}


function __cache()
{
    if (func_num_args() == 0)
    {
	return new __cache();
    }
    else
    {
	$args = func_get_args();
	
	switch ($args[0])
	{
	    case CACHE_DIRECTORY:
		if (isset($args[1]))
		{
		    __cache::setDirectory($args[1]);
		}
		else
		{
		    return __cache::getDirectory();
		}
		break;
	    default:
	}
    }
}

class __cache
{
    private static $cache_directory;
   
    function setDirectory($directory)
    {
	if (isset(self::$cache_directory))
	{
	    throw new Exception("cache directory has been set already.");   
	}
	else
	{
	    if (is_dir($directory) && is_writable($directory) && is_readable($directory))
            {
        	self::$cache_directory = $directory;
	    }
	    else
	    {
	        throw new Exception("$directory no read write access.");
	    }
	}
    }
    function getDirectory()
    {
	return self::$cache_directory;
    }
    
    
}


?>