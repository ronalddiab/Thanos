<?php

/**
 *  Email template Model
 *
 *  To perform queries related to email template management.
 * 
 * @package CIDemoApplication
 * @subpackage Email Template
 * @copyright	(c) 2013
 * @author AMPT
 */
class Email_Template_model extends Base_Model {

    protected $_table = TBL_EMAIL_TEMPLATE;
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
     * Function insert_email_template to insert record 
     */
//    function _save_template($template_id) {
//        $template_id = intval($template_id);
//        //$lang_id = intval($lang_id);
//
//        $data_array = array();
//
//        $data_array['template_id'] = $template_id;
//        $data_array['lang_id'] = $lang_id;
//        if (isset($this->template_name)) {
//            $data_array['template_name'] = $this->template_name;
//        }
//        if (isset($this->template_subject)) {
//            $data_array['template_subject'] = $this->template_subject;
//        }
//        if (isset($this->template_body)) {
//            $data_array['template_body'] = $this->template_body;
//        }
//        if (isset($this->status)) {
//            $data_array['status'] = $this->status;
//        }
//
//        $data_array['created'] = GetCurrentDateTime();
//        $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
//
//        $this->db->set($data_array);
//        $this->db->insert($this->_table);
//        return $this->db->_error_number(); // return the error occurred in last query
//    }

    public function save_template($data) {
        //Type Casting
        $id = intval($data['id']);

        if ($id != 0 && $id != "") {
            $this->db->where('id', $id);
            $this->db->set('modified', 'NOW()', FALSE);
            $this->db->update($this->_table, $data);
            $id = $id;
        } else {
            $this->db->set('modified', 'NOW()', FALSE);
            $this->db->set('created', 'NOW()', FALSE);
            $this->db->insert($this->_table, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * Function update_email_template to update record 
     */
    function update_email_template($template_id, $lang_id) {
        $template_id = intval($template_id);
        $lang_id = intval($lang_id);

        $data_array = array();

        if (isset($this->template_name)) {
            $data_array['template_name'] = $this->template_name;
        }
        if (isset($this->template_subject)) {
            $data_array['template_subject'] = $this->template_subject;
        }
        if (isset($this->template_body)) {
            $data_array['template_body'] = $this->template_body;
        }
        if (isset($this->status)) {
            $data_array['status'] = $this->status;
        }
        $data_array['modified'] = GetCurrentDateTime();
        $data_array['modified_by'] = $this->session->userdata[get_section($this, true)]['user_id'];

        $this->db->where(array('template_id' => $template_id, 'lang_id' => $lang_id));
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_template_detail_by_id to get email tempalte detail 
     */
    public function get_template_detail_by_id($template_id) {
        $template_id = intval($template_id);
        //$lang_id = intval($lang_id);

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                //->where(array('c.template_id' => $template_id, 'c.lang_id' => $lang_id))
                ->where('c.id', $template_id)
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_email_template_listing to get email template listing 
     */
    function get_email_template_listing() {

        //$language_id = intval($language_id);

        if ($this->search_term != "") {
            $this->db->like('LOWER(c.template_name)', strtolower($this->search_term), 'both');
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        //$this->db->select('c.* , l.*');
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
     * Function delete_email_tempalte to delte template record 
     */
    public function delete_email_template($id) {
        $id = intval($id);

        $data_array = array('status' => '-1');
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_related_lang_template to check email template record exist or not
     */
    public function get_related_lang_template($template_id, $lang_id) {
        $template_id = intval($template_id);
        $lang_id = intval($lang_id);

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                ->where(array('c.template_id' => $template_id, 'c.lang_id' => $lang_id))
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_last_email_template_id to get lasr email template inserted id
     */
    function get_last_email_template_id() {
        $this->db->select_max('template_id')
                ->from($this->_table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['template_id'];
        } else {
            return 0;
        }
    }

    function get_template_id_by_name($template_name, $lang_id) {

        $lang_id = intval($lang_id);

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                ->where(array('c.template_name' => $template_name, 'c.lang_id' => $lang_id))
                ->where('c.status !=', -1);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        return $this->db->custom_result($query);
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
        // $this->db->where('lang_id =', $lang_id);
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

    public function check_unique_template_name($template_name, $id = "") {
        //Type Casting 

        $template_name = trim(strip_tags($template_name));
        $id = intval($id);

        $this->db->select('em.*');
        $this->db->from($this->_table . " as em");
        $this->db->where('em.template_name', $template_name);
        if ($id != '0' && $id != '') {
            $this->db->where('em.id !=', $id);
        }
        $this->db->where('em.status !=', -1);

        $result = $this->db->get();

        return $this->db->custom_result($result);
    }

}
