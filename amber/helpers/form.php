<?php
//////////////////////////////////////////////////
// constants
//
DEFINE (FRM_SID     ,uniqid('sid'));
DEFINE (FRM_SCLASS  ,uniqid('class'));

//
/////////////////////////////////////////////////

class hlpForm
{
    // @return null dont bother passing params
    final public function __construct() { /* reserving this, dont want this to be used by inherited class. */ }
    
    
    // @param constant FRM_SID -
    // @return object $this
    final public function set($target, $setting)
    {
        
        switch ($target)
        {
            case FRM_SID:
              
              break;
            case FRM_SCLASS:
                
               break;    
            default:
              throw new Exception("Must pass in valid constant to ". __CLASS__ . " set target param");
        }
       
       return $this; 
    }
    
    
    
    
    final public function attach($object)
    {
        
        
        return $this;
    }  
    
}


class formDB
{
    public function getTable($table,$rows)
    {
        
    }
    
    
    
}
