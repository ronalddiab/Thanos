<?php

/**
 *  certification_menu Model
 *
 *  To perform queries related to certification_menu management.
 *
 * @package CIDemoApplication
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Certification_menu_model extends Base_Model {

    protected $_tbl_menus = TBL_MENU_NAVIGATION;
    protected $_tbl_questions = TBL_SECTION_QUESTIONS;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $_record_count;

    function get_certification_menu_listing()
    {
        if ($this->search_term != "") {
            $this->db->like("LOWER(c.title)", strtolower($this->search_term));
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->from($this->_tbl_menus . ' as c');
        $this->db->join($this->_tbl_menus . ' as main_menu','main_menu.id = c.main_menu_id','left');
        $this->db->select('c.*,main_menu.title as main_menu_title');
        $this->db->where('c.status !=', -1);
        $this->db->where('c.certification_menu',1);
        $this->db->where('c.parent_id!=',0);
        $query = $this->db->get();
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }
    public function inactive_all_records() {
        $this->db->set('status', 0);
        $this->db->where('certification_menu', 1);
        $this->db->update($this->_tbl_menus);
        $this->db->where('status !=', -1);
        return true;
    }
    public function active_all_records() {
        $this->db->set('status', 1);
        $this->db->where('certification_menu', 1);
        $this->db->update($this->_tbl_menus);
        $this->db->where('status !=', -1);
        return true;
    }
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->where('certification_menu', 1);
        // $this->db->or_where_in('parent_id',$id);
        // $this->db->or_where_in('main_menu_id',$id);
        $this->db->update($this->_tbl_menus);
        return $id;
    }
    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        $this->db->from($this->_tbl_menus);
        $this->db->where('certification_menu', 1);
        // $this->db->or_where_in('parent_id',$id);
        // $this->db->or_where_in('main_menu_id',$id);
        $this->db->set('status', 0);
        $this->db->update($this->_tbl_menus);
        return $id;
    }
      /**
     * Function delete_section to delete menu
     * @param integer $id
     */
    public function delete_section($id) {
        $this->db->from($_tbl_menus);
        $this->db->where('status !=', -1);
        $this->db->where_in('id', $id);
        // $this->db->or_where_in('parent_id',$id);
        // $this->db->or_where_in('main_menu_id',$id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_menus);
    }
    function get_section_detail($id = 0) {
        //Type Casting
        $id = intval($id);
        $this->db->where("c.id", $id);
        $this->db->from($this->_tbl_menus . ' as c');
        $this->db->join($this->_tbl_menus . ' as main_menu','main_menu.id = c.main_menu_id','left');
        $this->db->where_in("c.status", array(1, 0));
        $this->db->select('c.*,main_menu.title as main_menu_title');
        $tableMenu = $this->db->get($this->_tbl_menus);
        $menuArray = $tableMenu->row_array();
        if (!empty($menuArray)) {
            return $menuArray;
        } else {
            return [];
        }
    }
  
    function get_section_questions($id = 0)
    {
        $id = intval($id);   
        $this->db->where("menu_navigation_submenu_id", $id);
        $this->db->select('*');
        $tableMenu = $this->db->get($this->_tbl_questions);
        $queArray = $tableMenu->result_array();
        if (!empty($queArray)) {
            return $queArray;
        } else {
            return [];
        }
    }
    function get_certification_parent_menu()
    {       
        $this->db->where("slug", 'certification');
        $this->db->where("parent_id",0);
        $this->db->select('id');
        $tablemenus = $this->db->get($this->_tbl_menus);
        $menuArray = $tablemenus->row_array();
        if (!empty($menuArray)) {
            return $menuArray['id'];
        } else {
            return '';
        }
    }
  
    
    public function getAncestors($id,$all_info=0) {
         $menuArray = [];
         $all_info_arr=[];
         $i=0;
         $certification_parent_id = $this->get_certification_parent_menu();
         $current_id = $id;
     
         while ($current_id && $current_id != $certification_parent_id) {
             $this->db->where("certification_menu", 1);
             $this->db->where("id", $current_id);
             $menu = $this->db->get($this->_tbl_menus)->row_array();
     
             if ($menu) {
                 array_push($menuArray,$menu['id']);
                 if ($i != 0) {
                    $all_info_arr[$i] = [
                        'id'    => $menu['id'],
                        'title' => $menu['title'],
                        'link' => $menu['link'],
                    ];
                 }
                 $current_id = $menu['parent_id'];
             } else {
                 break;
             }
             $i++;
         }
         return ($all_info == 1) ? $all_info_arr : $menuArray;
    }
    
      
    public function get_certification_menu($parent_id) {  
        $menuArray = [];
    
        $this->db->where("certification_menu", 1);
        $this->db->where("parent_id", $parent_id);
        $tablemenus = $this->db->get($this->_tbl_menus);
        $menus = $tablemenus->result_array();
    
        foreach ($menus as $menu) {
            $menuArray[$menu['id']] = $menu;
        }
    
        return $menuArray;
    }
    /**
     * Function save_menu to add/update menu
     * @param array $data for menu table
     */
    public function save_menu($data) {
        if(isset($data['id'])) {
            $certification_data['id'] = $data['id'];
        }
        if(isset($data['title'])) {
            $certification_data['title'] = $data['title'];
        }
        if(isset($data['parent_id']) && $data['parent_id']!='') {
            $certification_data['parent_id'] = $data['parent_id'];
        }
        else{
            $certification_data['parent_id'] = $data['main_menu_id'];
        }
        $parent_details = $this->get_section_detail($certification_data['parent_id']);
        $certification_data['order'] = $parent_details['order'] + 1;
        if(isset($data['main_menu_id']))
        {
            $certification_data['main_menu_id'] = $data['main_menu_id'];
        }
        if(isset($data['status'])) {
            $certification_data['status'] = $data['status'];
        }
        if(isset($data['section_description'])) {
            $certification_data['section_description'] = $data['section_description'];
        }
        if(isset($data['is_upload_checked'])) 
        {
            $certification_data['is_upload_checked'] = $data['is_upload_checked'] ? 1:0;
        }
        
        if(isset($data['section_logo'])) {
            $certification_data['section_logo'] = $data['section_logo'];
        }
        if(isset($data['section_image'])) {
            $certification_data['section_image'] = $data['section_image'];
        }
        $certification_data['menu_name'] = 'admin_menu';
        $certification_data['certification_menu'] = 1;
        if (isset($certification_data['id']) && $certification_data['id'] != 0 && $certification_data['id'] != "") {
            $this->db->where('id', $certification_data['id']);
            $this->db->update($this->_tbl_menus, $certification_data);
            $id = $certification_data['id'];
        } else {
            if(isset($data['slug'])) {
                $slug = $data['slug'];

                // Check if slug already exists in the table
                $this->db->where('slug', $slug);
                if (isset($certification_data['id'])) {
                    $this->db->where('id !=', $certification_data['id']);
                }
                $query = $this->db->get($this->_tbl_menus);

                if ($query->num_rows() > 0) {
                    // Slug already exists, generate a unique slug
                    $slug_base = $slug;
                    $i = 1;
                    while ($query->num_rows() > 0) {
                        $slug = $slug_base . '-' . $i++;
                        $this->db->where('slug', $slug);
                        if (isset($certification_data['id'])) {
                            $this->db->where('id !=', $certification_data['id']);
                        }
                        $query = $this->db->get($this->_tbl_menus);
                    }
                    $certification_data['slug'] = $slug;
                } else {
                    $certification_data['slug'] = $slug;
                }

                $certification_data['link'] = 'certification/section/'.$certification_data['slug'];
            }
            if ($this->db->insert($this->_tbl_menus, $certification_data)) {
                $id = $this->db->insert_id();
            }
        }
        return $id;
    }
    function save_question($menu_id = 0, $post_data = array())
    {
        if (empty($post_data) or empty($post_data['ids'])) {
			return true;
		}
        $data['menu_navigation_submenu_id'] = $menu_id;
        $data['year'] = date('Y');
		foreach ($post_data['ids'] as $key => $id) {
            $data['section_question']  = $post_data['titles'][$key];
            if(!empty($post_data['titles'][$key]))
            {
                if (!empty($id)) {
                    $this->db->where('id', $id);
                    $this->db->update('section_questions', $data);
                } else {
                    $this->db->insert('section_questions', $data);
                }
            }
        }
    }
}
