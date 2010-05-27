<?php
### system constants ##########################################################################################################
DEFINE ('AMBER_VERSION',		'1.0');
DEFINE ('AMBER_AUTHOR',			'John Janes <john@johnjanes>');
DEFINE ("CLASS_ROUTES",			'Route');
DEFINE ('DIRECTORY_CACHE',		'cache');
DEFINE ('WB_PATH',			BASE_SYSTEM . DS . 'workerBee');
DEFINE ('DIRECTORY_MODULES',		'modules');
DEFINE ('CACHE_DIRECTORY',  		uniqid('cache'));
### application init settings #################################################################################################
IF (!DEFINED('INIT_AUTOLOADER'))	DEFINE ("INIT_AUTOLOADER",	TRUE);			// tell the system to init autoloader
IF (!DEFINED('INIT_CACHE'))		DEFINE ("INIT_CACHE",		TRUE);			// tell the system to init the cache if it exists
IF (!DEFINED('INIT_MODULES'))		DEFINE ("INIT_MODULES",		TRUE);			// tell the system to init the module system
IF (!DEFINED('INIT_ROUTES'))		DEFINE ("INIT_ROUTES",		TRUE);			// tell the system to init the routes
IF (!DEFINED('INIT_MVC'))		DEFINE ("INIT_MVC",		TRUE);			// tell the system to init the mvc
### hook sequence
DEFINE ('HOOK_SEQ_INIT',		uniqid('hook_init'));
DEFINE ('HOOK_SEQ_INDEX',		uniqid('hook_index'));
DEFINE ('HOOK_SEQ_SYSTEM',		uniqid('hook_system'));
DEFINE ('HOOK_SEQ_MASTER',		uniqid('hook_master'));
DEFINE ('HOOK_SEQ_MODELS',		uniqid('hook_models'));
DEFINE ('HOOK_SEQ_FINISHED',		uniqid('hook_finished'));

### System hooks ###############################################################################################################
DEFINE ('HOOK_ALL',			uniqid('all_load'));
DEFINE ('HOOK_CONTROLLER_LOAD',		uniqid('con_load'));
