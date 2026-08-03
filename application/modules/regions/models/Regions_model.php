<?php

/**
 *  regions Model
 *
 *  To perform queries related to region management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Regions_model extends Base_Model {

    protected $_tbl_regions = TBL_REGIONS;
    protected $_tbl_sites = TBL_SITES;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $_record_count;

    /**
     * Function save_region to add/update region
     * @param array $data for region table
     */
    public function save_region($data) {
        if (isset($data['id'])) {
            $region_data ['id'] = $data['id'];
        }
        if (isset($data['region_name'])) {
            $region_data ['region_name'] = $data['region_name'];
        }
        if (isset($data['status'])) {
            $region_data ['status'] = $data['status'];
        }
        if (isset($data['created_on'])) {
            $region_data ['created_on'] = $data['created_on'];
        }
        if (isset($data['modify_on'])) {
            $region_data ['modify_on'] = $data['modify_on'];
        }
        if (isset($region_data['id']) && $region_data['id'] != 0 && $region_data['id'] != "") {
            $this->db->where('id', $region_data['id']);
            $this->db->update($this->_tbl_regions, $region_data);
            $id = $region_data['id'];
        } else {

            $this->db->set('created_on', 'NOW()', FALSE);
            if ($this->db->insert($this->_tbl_regions, $data)) {
                $id = $this->db->insert_id();
            }
        }
        return $id;
    }
   
    /**
     * Function get_region_detail to return region array of particular id
     * @param integer $id
     */
    function get_region_detail($id = 0) {
        //Type Casting
        $id = intval($id);

        $this->db->where("id", $id);
        $this->db->where_in("status", array(1, 0));
        $tableregions = $this->db->get($this->_tbl_regions);
        $regionArray = $tableregions->row_array();

        if (!empty($regionArray)) {
            return $regionArray;
        } else {
            return '';
        }
    }


    /**
     * Function get_region_listing to fetch all records of regions
     * @param integer $region_id default = 0
     */
    function get_region_listing() {

        if ($this->search_term != "") {
            $this->db->like("LOWER(r.region_name)", strtolower($this->search_term));
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('r.*');
        $this->db->from($this->_tbl_regions . ' AS r');
        $this->db->where('r.status !=', -1);

        $query = $this->db->get();
     
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function delete_region to delete region
     * @param integer $id
     */
    public function delete_region($id) {
        //Check sites for selected regions
        $this->db->select('r.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_regions.' AS r','s.region_id = r.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('r.id', $id);
        $this->db->group_by('r.id');
        $result = $this->db->get();
        $sites_regions = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_regions)){
            $ids = array();
            foreach ($sites_regions as $value) {
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
        return $this->db->update($this->_tbl_regions);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        //Check sites for selected regions
        $this->db->select('r.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_regions.' AS r','s.region_id = r.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('r.id', $id);
        $this->db->group_by('r.id');
        $result = $this->db->get();
        $sites_regions = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_regions)){
            $ids = array();
            foreach ($sites_regions as $value) {
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
        $this->db->update($this->_tbl_regions);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        //Check sites for selected regions
        /*$this->db->select('r.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_regions.' AS r','s.region_id = r.id');
        $this->db->where('s.status !=', -1);
        $this->db->group_by('r.id');
        $result = $this->db->get();
        $sites_regions = $result->result_array();

        if(!empty($sites_regions)){
            $ids = array();
            foreach ($sites_regions as $value) {
                $ids[] = $value['id'];
            }
            $this->db->where_not_in('id', $ids);
        }*/


        //Check sites for selected regions
        $this->db->select('r.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_regions.' AS r','s.region_id = r.id');
        $this->db->where('s.status !=', -1);
        $this->db->where('r.status !=', -1);
        //$this->db->where_in('r.id', $id);
        $this->db->group_by('r.id');
        $result = $this->db->get();
        $sites_regions = $result->result_array();
        if(!empty($sites_regions)){
            return false;
        }
    
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);

        //$this->db->where('id !=', 1);
        $this->db->update($this->_tbl_regions);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array()) {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_regions);

        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        //$this->db->where('id !=', 1);
        $this->db->update($this->_tbl_regions);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array()) {
        //Check sites for selected regions
        $this->db->select('r.id');
        $this->db->from($this->_tbl_sites.' as s');
        $this->db->join($this->_tbl_regions.' AS r','s.region_id = r.id');
        $this->db->where('s.status !=', -1);
        $this->db->where_in('r.id', $id);
        $this->db->group_by('r.id');
        $result = $this->db->get();
        $sites_regions = $result->result_array();
        $sitecheck = false;
        if(!empty($sites_regions)){
            $ids = array();
            foreach ($sites_regions as $value) {
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
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_regions);
    }

}
