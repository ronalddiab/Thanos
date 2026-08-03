<?php

/**
 * Description of maps
 *
 * @author Mehul Panchal
 * @property Template $template
 */
class login extends widgets {

    //put your code here
    public function __construct() {
        parent::__construct();        
    }
    /*
     * Function run()
     */
    function run() {              
       return $this->build('login_view');
    }

}