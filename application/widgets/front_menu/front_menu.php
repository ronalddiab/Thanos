<?php

/**
 * Description of maps
 *
 * @author pankit shah
 * @property Template $template
 */
class front_menu extends widgets {

    //put your code here
    public function __construct() {
        parent::__construct();        
         //load module from application/models
        //$this->load->model('config_model');        
        
        //this function use to load model from modules 
        $this->load->model('menu/menu_model');
    }

    function run($options = array()) {
        $name = !empty($options) ? $options['menu_name'] : 'admin_menu';                
        $section_name = !empty($options) ? $options['section_name'] : 'front';                        
        $this->menu_model->language_id =  $this->session->userdata[$section_name]['site_lang_id'];
        $menu_array = $this->menu_model->get_menu($name,1);
        $menu_data = array("parentId" => 0, "menu" => array('items' => array(), 'parents' => array()));
        foreach ($menu_array as $key => $val) {
            $menu_data['menu']['items'][$val['m']['id']] = $val;
            $menu_data['menu']['parents'][$val['m']['parent_id']][] = $val['m']['id'];
        }     
        return $this->build('menu_view', $menu_data);
    }

}