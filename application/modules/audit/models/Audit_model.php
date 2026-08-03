<?php

/**
 *  Audit Model
 *
 *  To perform queries related to audit management.
 *
 * @package CIDemoApplication
 * @subpackage Audit
 * @copyright	(c) 2013, TatvaSoft
 * @author HTDO
 */
class Audit_model extends Base_Model {

    protected $_table = TBL_ENERGY_AUDIT;
    protected $_inventory_table = TBL_ENERGY_INVENTORY;
    protected $_inventory_titles = TBL_INVENTORY_TITLES;
    public $search_term_start_date = "";
    public $search_term_end_date = "";
    public $sort_by = "";
    public $sort_order = "";
    public $offset = "";
    public $audit_on = "";
    public $inventory_on = "";
    public $inventory_title_id = "";
    public $status = "";

    function __construct() {
        parent::__construct();
    }

    /**
     * Function get_energy_audit_listing to get audit listing
     */
    function get_energy_audit_listing() {
        if ($this->search_term_start_date != "") {
            $this->db->where('ea.audit_on >=', $this->search_term_start_date);
        }
        
        if ($this->search_term_end_date != "") {
            $this->db->where('ea.audit_on <=', $this->search_term_end_date);
        }

        $this->db->where('ea.site_id', $this->site_id);
        
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        } else {            
            $this->db->order_by('ea.audit_on', "DESC");
        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        $this->db->select('ea.*');
        $this->db->from($this->_table . ' as ea');

        $this->db->where('ea.status !=', -1);

        $query = $this->db->get();
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function get_inventory_listing to get audit listing
     */
    function get_inventory_listing() {
        $this->db->where('ei.site_id', $this->site_id);
        
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        } else {            
            $this->db->order_by('ei.inventory_on', "DESC");
        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('ei.*');
        $this->db->from($this->_inventory_table . ' as ei');
        $this->db->where('ei.status !=', -1);
        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function insert_energy_audit to insert record
     */
    // function insert_energy_audit($energy_audit_id, $lang_id) {
    function insert_energy_audit($energy_audit_id) {

        $energy_audit_id = intval($energy_audit_id);
        // $lang_id = intval($lang_id);

        $data_array = array();
        $data_array['id'] = $energy_audit_id;

        if (isset($this->audit_on)) {
            $data_array['audit_on'] = $this->audit_on;
        }
        if (isset($this->full_report)) {
            $data_array['full_report'] = $this->full_report;
        }
        if (isset($this->full_report_title)) {
            $data_array['full_report_title'] = $this->full_report_title;
        }
        if (isset($this->executive_summary)) {
            $data_array['executive_summary'] = $this->executive_summary;
        }
        if (isset($this->executive_summary_title)) {
            $data_array['executive_summary_title'] = $this->executive_summary_title;
        }
        if (isset($this->status)) {
            $data_array['status'] = $this->status;
        }
        $data_array['site_id'] = $this->site_id;
        $data_array['created_on'] = GetCurrentDateTime();
        $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];

        $this->db->set($data_array);
        $id = $this->db->insert($this->_table);

        // Save audit trail
        $site_id = $this->site_id;
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $data_action = 'Create';
        saveAuditTrail($user_id, $site_id, 'Energy Audit', $data_action);

        return $id; 
        // return $this->db->_error_number(); // return the error occurred in last query
    }
 
    /**
     * Function insert_inventory to insert record
     */
    function insert_inventory($inventory_id) {

        $inventory_id = intval($inventory_id);

        $data_array = array();
        $data_array['id'] = $inventory_id;

        if (isset($this->inventory_on)) {
            $data_array['inventory_on'] = $this->inventory_on;
        }
        if (isset($this->inventory_title_id)) {
            $data_array['inventory_title_id'] = $this->inventory_title_id;
        }
        if (isset($this->full_report)) {
            $data_array['full_report'] = $this->full_report;
        }
        if (isset($this->full_report_title)) {
            $data_array['inventory_title'] = $this->full_report_title;
        }
        $data_array['status'] = 0;
        $data_array['site_id'] = $this->site_id;
        $data_array['created_on'] = GetCurrentDateTime();
        $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $data_array['modify_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $this->db->set($data_array);
        $id = $this->db->insert($this->_inventory_table);
        return $id; 
    }
 
    /**
     * Function update_energy_audit to update record
     */
    function update_energy_audit($energy_audit_id) {
        $energy_audit_id = intval($energy_audit_id);
        // $lang_id = intval($lang_id);

        $data_array = array();

        $data_array['id'] = $energy_audit_id;
        if (isset($this->audit_on)) {
            $data_array['audit_on'] = $this->audit_on;
        }
        if (isset($this->full_report)) {
            $data_array['full_report'] = $this->full_report;
        }
        if (isset($this->full_report_title)) {
            $data_array['full_report_title'] = $this->full_report_title;
        }
        if (isset($this->executive_summary)) {
            $data_array['executive_summary'] = $this->executive_summary;
        }
        if (isset($this->executive_summary_title)) {
            $data_array['executive_summary_title'] = $this->executive_summary_title;
        }
        if (isset($this->status)) {
            $data_array['status'] = $this->status;
        }
        $data_array['site_id'] = $this->site_id;
        $data_array['modify_on'] = GetCurrentDateTime();
        $data_array['modify_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];

        $this->db->where(array('id' => $energy_audit_id));
        $this->db->set($data_array);
        $this->db->update($this->_table);

        // Save audit trail
        $site_id = $this->site_id;
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $data_action = 'Update';
        saveAuditTrail($user_id, $site_id, 'Energy Audit', $data_action);
    }

    /**
     * Function get_energy_audit_detail_by_id to get audit detail
     */
    public function get_energy_audit_detail_by_id($energy_audit_id) {
        $energy_audit_id = intval($energy_audit_id);
        $lang_id = intval($lang_id);

        $this->db->select('ea.*')
                ->from($this->_table . ' as ea')
                ->where(array('ea.id' => $energy_audit_id))
                ->where('ea.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function delete_energy_audit to delete energy audit record
     */
    public function delete_energy_audit($id) {
        $id = intval($id);

        $data_array = array('status' => '-1');
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function delete_inventory to delete energy audit record
     */
    public function delete_inventory($id) {
        $id = intval($id);
        $data_array = array('status' => '-1');
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_inventory_table);
    }

    /**
     * Function get_last_energy_audit_id to get last audit inserted id
     */
    function get_last_energy_audit_id() {
        $this->db->select_max('id')
                ->from($this->_table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['id'];
        } else {
            return 0;
        }
    }
    
    /**
     * Function get_last_energy_audit_date to get last audit inserted date
     */
    function get_last_energy_audit_date() {
        $this->db->where('site_id', $this->site_id);
        $this->db->select_max('audit_on')->from($this->_table);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['audit_on'];
        } else {
            return 0;
        }
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
        $this->db->update($this->_table);

        return true;
    }

    /**
     * Function delete_records to delete records
     * @param integer $id
     */
    public function delete_records($id = array()) {
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_table);
    }

    /*
    * Function get invetory title list
    */

    public function get_inventory_titles(){
        $this->db->select('id,title');
        $this->db->from($this->_inventory_titles);
        $inventoryTitles = $this->db->get();
        if(!empty($inventoryTitles)){
            return $inventoryTitles->result_array();
}
    }

}
