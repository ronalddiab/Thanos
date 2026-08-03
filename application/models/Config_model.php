<?php



class Config_model extends CI_Model {



    function  __construct() {

        parent::__construct();

    }

    function getSiteDetail($id) {
        $this->db->select('*')
         ->from("sites")
         ->where('id', $id);
         $result = $this->db->get();
        $siteArray = $result->result_array();
        return $siteArray;
     }
     

    function get_langs() {

        $this->db->order_by('lang', 'asc');

        $langs = $this->db->get('languages');

        return $langs;

    }



    function get_menu_array($menu_name) {

        global $menu_data;

        $this->db->select('*')

                ->from("menu_navigation")

                ->where('menu_name', $menu_name);

        $result = $this->db->get();

        $menu_array = $result->result_array();



        $menu_data = array("menu" => array('items' => array(), 'parents' => array()));



        foreach ($menu_array as $key => $val) {

            $menu_data['menu']['items'][$val['id']] = $val;

            $menu_data['menu']['items'][$val['parent_id']]['haschild'] = 1;

            $menu_data['menu']['parents'][$val['parent_id']][] = $val['id'];

        } 

    }



    function change_slug_url_management($old_slug_url, $slug_url, $module_name, $related_id, $core_url, $status,$language_id) {

        $data_array = array(

            'slug_url' => $slug_url,

            'module_name' => $module_name,

            'language_id' => $language_id,

            'related_id' => $related_id,

            'core_url' => $core_url,

            'status' => $status

            

        );  

        $update = 0;

        

        if($old_slug_url != '')

            $update = $this->check_slug_exist_or_not($old_slug_url);

        if ($update > 0) {

            $this->db->where('slug_url', $old_slug_url);

            $this->db->set($data_array);

            $this->db->update("url_management");            

        } else {

            $this->db->set($data_array);

            $this->db->insert("url_management");            

        }

    }



    function check_slug_exist_or_not($slug_url='') {        

        $this->db->select('*')

                ->from("url_management")

                ->where('slug_url', $slug_url);

        $result = $this->db->get();

        

        return count($result->result_array());

    }



}
