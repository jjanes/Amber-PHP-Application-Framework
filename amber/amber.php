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

// Set up some contants 
DEFINE ("EXT", 			".php");					// set ext type
DEFINE ("DS", 			DIRECTORY_SEPARATOR);				// nuff said
DEFINE ("DIRECTORY_SYSTEM",	"system");					// system directory
DEFINE ("BASE_SYSTEM", 		dirname(__FILE__));				// set up the system base
DEFINE ("PATH_SYSTEM",		BASE_SYSTEM .  DS . DIRECTORY_SYSTEM);		// set up base path

// load framework contants 
require (PATH_SYSTEM . DS . "system.constants.php");	// load up amber constants
require (PATH_SYSTEM . DS . "system.base.php");		// load up the amber base system


if (file_exists(PATH_SYSTEM . DS . "system.application.php"))				// lets check to see if system application path exists
{
	if (empty($_SERVER['REQUEST_URI']))						// lets check to see if this has been ran from the command line
	{
		DEFINE ("AMBER_COMMANDLINE_APP",TRUE);					// let the system know we have a command line app
		if (!file_exists('PATH_SYSTEM . DS . "system.commandline.php'))
		{
			throw new Exception('Attempted to load command line app, cannot find system.commandline.php, check installation and try again.');
		}
		include (PATH_SYSTEM . DS . "system.application.php");			// load the base system
		include (PATH_SYSTEM . DS . "system.commandline.php");			// load command line system
	}
	else										// lets load up web application
	{
		DEFINE ("AMBER_COMMANDLINE_APP",FALSE);					// let sytem know we arent running a command line app
		include (PATH_SYSTEM . DS . "system.application.php");			// load up system file
	}
}
else 
{
	throw new Exception("Cannot Locate system.application.php");
}
