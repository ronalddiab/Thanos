<?php

/**
 * Description of custom pagination
 *
 * @author Mehul Panchal
 
 */
class custom_pagination extends widgets {

    
    public function __construct() {
        parent::__construct();        
    }

    /* run method for display pagination */
    
    function run($options = array()) {
              
        // load config file
        $pagination_config = config_item('pagination');
        
        // merge array of config and custom varible        
        $pagination_config=array_merge($pagination_config,$options,array('section_name'=>get_current_section($this)));

        // initialize variable for pagination
        $this->my_pagination->initialize($pagination_config);
        
        // pass to $data variable
        $data = $pagination_config;
        
        // define view
        return $this->build('custom_pagination_get_view', $data);
    }

}