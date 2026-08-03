<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/** 
 * Menu
 * Class is use for build menu widget. 
 *
 * @package CIDemoApplication
 * @subpackage widget
 * @copyright (c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 * 
 */
class menu extends widgets {
    
    /*
     * create an instance
     */
    public function __construct() {
        parent::__construct();           
        //load module from application/models
        $this->load->model('menu/menu_model');
    }
    
    /**
     * run() method is use for passing data in build widget method and get menu view. 
     * @param type $options
     * @return html
     */
    function run($options = array()) {        
        $name = !empty($options) ? $options['menu_name'] : 'admin_menu';
        $section_name = !empty($options) ? $options['section_name'] : 'admin';        
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