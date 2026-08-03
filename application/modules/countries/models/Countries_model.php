<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Countries_model extends Base_Model {

    protected $_tbl_countries = TBL_COUNTRIES;
    protected $_tbl_sites = TBL_SITES;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $_record_count;

    public function saveCountry($data) {
        $save_data = array();

        $save_data ['country'] = $data['country'];
        $save_data ['status']  = $data['status'];
        $site_id               = $data['site_id'];

        if (!empty($data['id'])) {
            $save_data['id'] = $data['id'];
            $save_data['modify_by'] = $this->user_id;

            $this->db->where('id', $save_data['id']);
            $this->db->update($this->_tbl_countries, $save_data);
            $id = $data['id'];
            $data_action = 'Update';    
        } else {
            $save_data['modify_by'] = $this->user_id;
            $save_data['created_by'] = $this->user_id;
            $this->db->set('created_on', 'NOW()', FALSE);
            if ($this->db->insert($this->_tbl_countries, $save_data)) {
                $id = $this->db->insert_id();
            }
            $data_action = 'Create';
        }
        saveAuditTrail($this->user_id, $site_id, 'Country ('.$save_data ['country'] .')', $data_action);
        return $id;
    }
   
    function getCountry($id = 0) {
        $id = intval($id);

        $this->db->where("id", $id);
        $this->db->where_in("status", array(1, 0));
        $result = $this->db->get($this->_tbl_countries);
        return $result->row_array();
    }

    function getCountries() {

        if ($this->search_term != "") {
            $this->db->like("LOWER(c.country)", strtolower($this->search_term));
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('c.*');
        $this->db->from($this->_tbl_countries . ' AS c');
        $this->db->where('c.status !=', -1);

        $query = $this->db->get();
     
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    public function deleteCountry($id) {
        //Check sites for selected countries
        $this->db->select('c.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_countries.' AS c','s.country_id = c.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('c.id', $id);
        $this->db->group_by('c.id');
        $result = $this->db->get();
        $sites_countries = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_countries)){
            $ids = array();
            foreach ($sites_countries as $value) {
                $ids[] = $value['id'];
            }

            foreach ($id as $value) {
                if(in_array($value, $ids)){
                   $sitecheck = true;
                }
            }
        }

        if($sitecheck){
            return false;
        }

        //Type Casting
        $id = intval($id);

        $this->db->where('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_countries);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        //Check sites for selected countries
        $this->db->select('c.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_countries.' AS c','s.country_id = c.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('c.id', $id);
        $this->db->group_by('c.id');
        $result = $this->db->get();
        $sites_countries = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_countries)){
            $ids = array();
            foreach ($sites_countries as $value) {
                $ids[] = $value['id'];
            }

            foreach ($id as $value) {
                if(in_array($value, $ids)){
                   $sitecheck = true;
                }
            }
        }


        if($sitecheck){
            return false;
        }

        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_countries);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        //Check sites for selected countries
        $this->db->select('c.id');
        $this->db->from($this->_tbl_countries.' as c');
        $this->db->join($this->_tbl_sites.' AS s','s.country_id = c.id');
        $this->db->where('s.status !=', -1);
        $this->db->where('c.status !=', -1);
        $this->db->group_by('c.id');
        $result = $this->db->get();
        
        $sites_countries = $result->result_array();
        if(!empty($sites_countries)){
            return false;
        }
    
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_countries);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_countries);

        return true;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_countries);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array()) {
        //Check sites for selected countries
        $this->db->select('c.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_countries.' AS c','s.country_id = c.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('c.id', $id);
        $this->db->group_by('c.id');
        $result = $this->db->get();
        $sites_countries = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_countries)){
            $ids = array();
            foreach ($sites_countries as $value) {
                $ids[] = $value['id'];
            }

            foreach ($id as $value) {
                if(in_array($value, $ids)){
                   $sitecheck = true;
                }
            }
        }


        if($sitecheck){
            return false;
        }

        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_countries);
    }

}
