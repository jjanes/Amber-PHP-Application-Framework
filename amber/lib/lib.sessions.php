<?php
if (headers_sent())
{
    throw new Exception("Headers have been sent already, please load this sessions lib before any headers are sent.");
}

class Amber_Sessions 
{
    final private function __construct() { /* */ }
    public function start()
    {
	
    }
}
?>