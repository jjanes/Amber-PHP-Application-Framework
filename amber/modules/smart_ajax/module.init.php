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
// amber module
// smart ajax
// HOOK_SEQ_MASTER, HOOK_SEQ_SYSTEM, HOOK_SEQ_INIT, HOOK_SEQ_MODELS, HOOK_SEQ_INDEX, HOOK_SEQ_FINISHED 
class Module_Ajax extends Amber_Module
{
    protected $controllers;
    
    public function name()
    {
	return 'ajax';
    }
    
    public function __init()
    {

    }

    public function controllerLoad($controller)			// anytime a controller is loaded this function is called
    {
	echo ">>> " . get_class($controller);


    }
    
    
    
    public function onHook($hook)	// this handels all system hooks 
    {
	switch ($hook)
	{
	    case HOOK_SEQ_MASTER:
	    case HOOK_SEQ_SYSTEM:
	    case HOOK_SEQ_INIT:
	    case HOOK_SEQ_MODELS:
	    case HOOK_SEQ_INDEX:
	    case HOOK_SEQ_FINISHED: 
	}
    }

    
}



?>