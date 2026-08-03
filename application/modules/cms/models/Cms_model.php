<?php

/**
 *  Cms Model
 *
 *  To perform queries related to cms management.
 * 
 * @package CIDemoApplication
 * @subpackage Cms
 * @copyright	(c) 2013, TatvaSoft
 * @author AMPT
 */
class Cms_model extends Base_Model {

    protected $_table = TBL_CMS;
    protected $_cms_meta = TBL_CMS_META;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $offset = "";
    public $title = "";
    public $slug_url = "";
    public $description = "";
    public $status = "";

    function __construct() {
        parent::__construct();
    }

    /**
     * Function get_cms_detail_by_slug to fetch detail of records by slug
     */
    public function get_cms_detail_by_slug($slug_url) {
        $slug_url = trim(strip_tags($slug_url));
        $this->db->select("c.*, cm.*");
        $this->db->from($this->_table . " as c");
        $this->db->join(TBL_CMS_META . ' as cm', 'c.cms_id = cm.cms_id AND c.lang_id = cm.lang_id', 'left');
        $this->db->where('c.slug_url', $slug_url);
        $this->db->where('c.status', 1);
        $this->db->where('c.lang_id', $this->{'cms_model'}->language_id);
        $query = $this->db->get();

        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function get_cms_id_from_slug_url to fetch cms id from slug 
     */
    function get_cms_id_from_slug_url($slug_url) {
        $slug_url = trim(strip_tags($slug_url));
        $this->db->select("c.*");
        $this->db->from($this->_table . " as c");
        $this->db->where('c.slug_url', $slug_url);
        $this->db->where('c.status', 1);
        $query = $this->db->get();

        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function insert_cms to insert record 
     */
    function insert_cms($cms_id, $lang_id) {
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);

        $data_array = array();

        $data_array['cms_id'] = $cms_id;
        $data_array['lang_id'] = $lang_id;
        if (isset($this->title)) {
            $data_array['title'] = $this->title;
        }
        if (isset($this->slug_url)) {
            $data_array['slug_url'] = $this->slug_url;
        }
        if (isset($this->description)) {
            $data_array['description'] = $this->description;
        }
        if (isset($this->status)) {
            $data_array['status'] = $this->status;
        }
        $data_array['created'] = GetCurrentDateTime();
        $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];

        $this->db->set($data_array);
        $this->db->insert($this->_table);
        // return $this->db->_error_number(); // return the error occurred in last query
        $error =  $this->db->error(); // return the error occurred in last query
        if(isset($error['message']) && $error['message'] != ''){
            return $error['message'];
        }     
        return true;
    }

    /**
     * Function update_cms to update record 
     */
    function update_cms($cms_id, $lang_id) {
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);

        $data_array = array();

        if (isset($this->title)) {
            $data_array['title'] = $this->title;
        }
        if (isset($this->slug_url)) {
            $data_array['slug_url'] = $this->slug_url;
        }
        if (isset($this->description)) {
            $data_array['description'] = $this->description;
        }
        if (isset($this->status)) {
            $data_array['status'] = $this->status;
            ;
        }
        $data_array['modified'] = GetCurrentDateTime();
        $data_array['modified_by'] = $this->session->userdata[get_section($this, true)]['user_id'];

        $this->db->where(array('cms_id' => $cms_id, 'lang_id' => $lang_id));
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    public function save_cms($data, $data_meta, $site_id, $user_id) {
        //Type Casting
        $id = intval($data['id']);

        if ($id != 0 && $id != "") {
            $this->db->where('id', $id);
            $data['cms_id'] = $id;
            $this->db->set('modified', 'NOW()', FALSE);
            $this->db->update($this->_table, $data);
            $this->db->where('cms_id', $id);
            $this->db->update($this->_cms_meta, $data_meta);
            $id = $id;
            $data_action = 'Update';
        } else {
            $this->db->set('modified', 'NOW()', FALSE);
            $this->db->set('created', 'NOW()', FALSE);
            $this->db->insert($this->_table, $data);
            $cms_id = $this->db->insert_id();
            $data = array();
            $data['cms_id'] = $cms_id;
            $this->db->where('id', $cms_id);
            $this->db->update($this->_table, $data);
            $data_meta['cms_id'] = $cms_id;
            $this->db->insert($this->_cms_meta, $data_meta);
            $id = $id;
            $data_action = 'Insert';
        }
       // Save audit trail
        saveAuditTrail($user_id, $site_id, 'CMS ('.$data['title'].')', $data_action);
        return $id;
    }

    /**
     * Function get_cms_detail_by_id to get cms detail 
     */
    public function get_cms_detail_by_id($cms_id) {
        $cms_id = intval($cms_id);

        $this->db->select('c.*,cm.*')
                ->from($this->_table . ' as c')
                ->join(TBL_CMS_META . ' as cm', 'c.cms_id = cm.cms_id', 'left')
                ->where('c.id', $cms_id)
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_cms_listing to get cms listing 
     */
    function get_cms_listing() {
        //$language_id = intval($language_id);

        if ($this->search_term != "") {
            $this->db->like('LOWER(c.title)', strtolower($this->search_term), 'both');
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        $this->db->select('c.*');
        $this->db->from($this->_table . ' as c');
        //$this->db->join(TBL_LANGUAGES . ' as l', 'c.lang_id = l.id', 'left');
//        if ($language_id != '')
//        {
//            $this->db->where("c.lang_id", $language_id);
//        }
        $this->db->where('c.status !=', -1);

        $query = $this->db->get();
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function delete_cms to delte cms record 
     */
    public function delete_cms($id) {
        $id = intval($id);

        $data_array = array('status' => '-1');
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_related_lang_cms to check cms record exist or not
     */
    public function get_related_lang_cms($cms_id, $lang_id) {
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);

        $this->db->select('c.*, cm.*')
                ->from($this->_table . ' as c')
                ->join(TBL_CMS_META . ' as cm', 'c.cms_id = cm.cms_id AND c.lang_id = cm.lang_id', 'left')
                ->where(array('c.cms_id' => $cms_id, 'c.lang_id' => $lang_id))
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_cms_default_page to get default cms page
     */
    public function get_cms_default_page() {
        $lang = $this->languages_model->get_default_language(); //TODO - UPDATE Functionality to return only 1 associated array        

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                ->where('c.slug_url', DEFAULT_CMS_PAGE)
                ->where('c.status', 1)
                ->where('c.lang_id', $lang[0]['l']['id']);
        //$query = $this->db->get_where($this->_table, array('slug_url' => DEFAULT_CMS_PAGE, 'status' => 1, 'lang_id' => $lang[0]['id']));        
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function get_last_cms_id to get lasr cms inserted id
     */
    function get_last_cms_id() {
        $this->db->select_max('cms_id')
                ->from($this->_table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['cms_id'];
        } else {
            return 0;
        }
    }

    /**
     * Function update_slug to update slug url
     */
    function update_slug($id, $slug_url) {
        $id = intval($id);
        $slug_url = trim(strip_tags($slug_url));
        $data_array = array(
            'slug_url' => $slug_url,
            'modified' => GetCurrentDateTime(),
            'modified_by' => ''
        );
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id 
     */
    public function inactive_records($id = array()) {
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_table);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        //$this->db->where('lang_id =', $lang_id);
        $this->db->update($this->_table);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id 
     */
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_table);

        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        //$this->db->where('lang_id =', $lang_id);
        $this->db->update($this->_table);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id 
     */
    public function delete_records($id = array()) {
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_table);
    }

    function check_unique_slug($data, $id) {
        $slug_url = trim(strip_tags($data['slug_url']));        

        $this->db->select('id,slug_url');
        $this->db->from($this->_table);

        $this->db->where('LOWER(slug_url) = ', mb_strtolower($slug_url, 'UTF-8'));
        $this->db->where('status != ', -1);
        $this->db->where('id != ', $id);
        
        $this->db->limit(1);
        $result = $this->db->get()->num_rows();
        return $result;
    }

}
