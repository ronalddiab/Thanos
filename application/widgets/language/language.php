<?php

/**
 * Description of maps
 *
 * @author pankit shah
 * @property Template $template
 */
class language extends widgets {

    //put your code here
    public function __construct() {
        parent::__construct();        
        $this->load->model('languages/languages_model');
    }

    function run($data = array()) {          
        $languages = $this->languages_model->get_languages();
        $data['languages'] = $languages;                 
        return $this->build('language_view', $data);
    }

}