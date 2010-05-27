<?php
$startTime = microtime(true);
DEFINE("WB_DA", TRUE);
error_reporting(E_ALL);
ini_set("display_errors", 1);



include("project/init.php");







function __inDevelopement()
{
	return false;
}

## __workerBee()->draw();

Application()->start();


echo  "<br />" . (memory_get_usage() / 1024) . ' ' . (microtime(true) -$startTime);
?>
