<?php

namespace adapt\locales\us{
    
    /* Prevent Direct Access */
    defined('ADAPT_STARTED') or die;
    
    class bundle_locales_us extends \adapt\bundle{
        
        public function __construct($data){
            parent::__construct('locales_us', $data);
        }
        
        public function boot(){
            if (parent::boot()){
                

                
                return true;
            }
            
            return false;
        }
        
    }
    
    
}

?>