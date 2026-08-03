<?php

/**
 *  Forum Model (actual table -  forum_post)
 *
 *  To perform queries related to  Forum management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 *
 * @author AVSH
 */
class Import_model extends Base_Model {

    protected $_tbl_electricity_tariff = TBL_ELECTRICITY_TARIFF;
    protected $_table = TBL_UTILITIES_COST;
    protected $_table_daily  = 'utilities_cost_daily';
    protected $_table_hourly = 'utilities_cost_hourly';
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $site_name = "";
    public $table_name = "";
    public $keywords = "";

    function __construct() {
        parent::__construct();
    }
    
    public function get_siteId($site_name) {
        $site_name = trim($site_name);
        $this->db->select("id");
        $this->db->from(TBL_SITES);
        $this->db->where('site_location_name', $site_name);
        $this->db->where('status',1);
        $query = $this->db->get();

        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }

    public function get_site_ids_by_name_array($siteNames = array()){
        $this->db->select('id,site_location_name as site_name');
        $this->db->from(TBL_SITES);
        $this->db->where_in('site_location_name', $siteNames);
        $this->db->where('status',1);
        $query = $this->db->get();


        $result = array();
        if ($query->num_rows() != 0) {
            $resultData = $query->result_array();
            foreach ($resultData as $value) {
                $result[$value['site_name']] = $value['id'];
            }
        }

        return $result;
    }

    public function get_columns($table_name) {
        $table_name = trim($table_name);
        $this->db->select("*");
        $this->db->from($table_name);
        $this->db->limit(1, 0);
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }

    public function insert_entity_details($data, $cddhddData = array()){
        unset($data['']);
        if(isset($cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['cdd']) && !empty($cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['cdd'])){
            $data['cdd'] = $cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['cdd'];
        }

        if(isset($cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['hdd']) && !empty($cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['hdd'])){
            $data['hdd'] = $cddhddData[$this->_table][$data['site_id']][$data['year_id']][$data['month_id']]['hdd'];
        }

        $this->db->set('created_on', 'NOW()', FALSE);
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->insert($this->_table, $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function insert_entity_details_electricity_tariff($data){
        $this->db->insert($this->_tbl_electricity_tariff, $data);
        $id = $this->db->insert_id();  
        return $id;
    }

    public function delete_entry_ifexist($data){
        $site_id  = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id  = $data['year_id'];
        $this->db->select("*");
        $this->db->from($this->_tbl_electricity_tariff);
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id);
            $this->db->delete($this->_tbl_electricity_tariff);  
        }

        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id); 
        $this->db->delete($this->_table);
    }

    // Daily utilities
    public function get_site_detail_by_name($siteNames = array()){
        $this->db->select('*');
        $this->db->from(TBL_SITES);
        if(!empty($siteNames)){
            $this->db->where_in('site_location_name', $siteNames);
        }
        $this->db->where('status',1);
        $query = $this->db->get();
        $result = array();
        if ($query->num_rows() != 0) {
            $resultData = $query->result_array();
            foreach ($resultData as $value) {
                $result[$value['site_location_name']] = $value;
            }
        }

        return $result;
    }

    public function delete_daily_utility_ifexists($data){
        $site_id = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id = $data['year_id'];
        $date_id = $data['date_id'];
        $this->db->select("*");
        $this->db->from($this->_table_daily);
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->delete($this->_table_daily);  
        }
    }

    public function insert_daily_utilities($data, $cddhddData = array()){
        unset($data['']);
        if(isset($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){
            $data['cdd'] = $cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];
        }

        if(isset($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){
            $data['hdd'] = $cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];
        }

        $this->db->set('created_on', 'NOW()', FALSE);
        $this->db->set('modify_on', 'NOW()', FALSE);
        $this->db->insert($this->_table_daily, $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function insert_daily_fixed_submission_utilities($data, $cddhddData = array()){

        unset($data['']);
        
        if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){
            $data['cdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];
        }

        if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){
            $data['hdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];
        }

        $this->db->insert('daily_reading_utilities_data', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function insert_daily_dynamic_submission_utilities($data){
        $this->db->insert('daily_reading_utilities_title_data', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function delete_daily_fixed_submission_utility_ifexists($data = array()){
        $site_id = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id = $data['year_id'];
        $date_id = $data['date_id'];

        $this->db->select("*");
        $this->db->from('daily_reading_utilities_data');
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->delete('daily_reading_utilities_data');  
        }
    }

    public function delete_daily_dynamic_submission_utility_ifexists($data = array()){
        $site_id = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id = $data['year_id'];
        $date_id = $data['date_id'];
        $utility_title_id = $data['utility_title_id'];

        $this->db->select("*");
        $this->db->from('daily_reading_utilities_title_data');
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $this->db->where('utility_title_id',$utility_title_id);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->where('utility_title_id',$utility_title_id);
            $this->db->delete('daily_reading_utilities_title_data');  
        }
    } 

    public function getDailyCddHddValues(){
        $data = array();

        $this->db->select("site_id,year_id,month_id,date_id,cdd,hdd");
        $this->db->from('utilities_cost_daily');
        $query1 = $this->db->get();
        if ($query1->num_rows() > 0) {
            $results = $query1->result_array();
            foreach ($results as $result) {
                $data['utilities_cost_daily'][$result['site_id']][$result['year_id']][$result['month_id']][$result['date_id']]['cdd'] = $result['cdd'];
                $data['utilities_cost_daily'][$result['site_id']][$result['year_id']][$result['month_id']][$result['date_id']]['hdd'] = $result['hdd'];
            }
        }

        $this->db->select("site_id,year_id,month_id,date_id,cdd,hdd");
        $this->db->from('daily_reading_utilities_data');
        $query1 = $this->db->get();
        if ($query1->num_rows() > 0) {
            $results = $query1->result_array();
            foreach ($results as $result) {
                $data['daily_reading_utilities_data'][$result['site_id']][$result['year_id']][$result['month_id']][$result['date_id']]['cdd'] = $result['cdd'];
                $data['daily_reading_utilities_data'][$result['site_id']][$result['year_id']][$result['month_id']][$result['date_id']]['hdd'] = $result['hdd'];
            }
        }

        return $data;
    }   

    public function getMonthlyCddHddValues(){
        $data = array();

        $this->db->select("site_id,year_id,month_id,cdd,hdd");
        $this->db->from('utilities_cost');
        $query1 = $this->db->get();
        if ($query1->num_rows() > 0) {
            $results = $query1->result_array();
            foreach ($results as $result) {
                $data['utilities_cost'][$result['site_id']][$result['year_id']][$result['month_id']]['cdd'] = $result['cdd'];
                $data['utilities_cost'][$result['site_id']][$result['year_id']][$result['month_id']]['hdd'] = $result['hdd'];
            }
        }

        return $data;
    }
    public function get_measureId($measure_name) {
        $measure_name = trim($measure_name);
        $this->db->select("id");
        $this->db->from('measures');
        $this->db->where('title', $measure_name);
        $this->db->where('status',1);
        $query = $this->db->get();

        if ($query->num_rows() != 0) {
            return $this->db->custom_result($query);
        }
    }
    public function insert_site_measures_reading($data){
        $this->db->insert('site_measures_reading', $data);
        $id = $this->db->insert_id();  
        return $id;
    }
    public function delete_measure_entry_ifexist($data){
        $site_id = $data['site_id'];
        $measure_id = $data['measure_id'];
        $this->db->select("*");
        $this->db->from('site_measures_reading');
        $this->db->where('site_id',$site_id);
        $this->db->where('measure_id',$measure_id);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('measure_id',$measure_id);
            $this->db->delete('site_measures_reading');  
        }

    }
    // delete data from hourly utilies cost table from import
    public function delete_hourly_utility_ifexists($data){

        $site_id = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id = $data['year_id'];
        $date_id = $data['date_id'];
        $hour = $data['hour'];
        $is_half_hourly = $data['is_half_hourly'];

        $this->db->select("*");
        $this->db->from($this->_table_hourly);
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $this->db->where('hour',$hour);
        $this->db->where('is_half_hourly',$is_half_hourly);
        $query1 = $this->db->get();

        // If data is exist in database, delete it
        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->where('hour',$hour);
            $this->db->where('is_half_hourly',$is_half_hourly);
            $this->db->delete($this->_table_hourly);  
        }
    }

    // insert data into hourly utilies cost table from import
    public function insert_hourly_utilities($data, $cddhddData = array()){
        
        unset($data['']);
        // get data for hourly 
        if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){
            $data['cdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];
        }

        if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){
            $data['hdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];
        }

        if(preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $data['hour'])) 
        {
            $this->db->set('created_on', 'NOW()', FALSE);
            // $this->db->set('created_by', $user_id, FALSE);
            $this->db->set('modify_on', 'NOW()', FALSE);
            $this->db->insert($this->_table_hourly, $data);
            $id = $this->db->insert_id(); 
            return $id;
        }
    }

    // delete data from hourly utilies cost table from import
    public function delete_half_hourly_utility_ifexists($data){

        $site_id  = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id  = $data['year_id'];
        $date_id  = $data['date_id'];
        $hour     = $data['hour'];
        $this->db->select("*");
        $this->db->from($this->_table_hourly);
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $this->db->where('hour',$hour);
        $this->db->where('is_half_hourly', 1);
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->where('hour',$hour);
            $this->db->where('is_half_hourly', 1);
            $this->db->delete($this->_table_hourly);  
        }
    }

    // insert data into hourly utilies cost table from import
    public function insert_half_hourly_utilities($data, $cddhddData = array()){
        
        unset($data['']);
        // get data for hourly 
        if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){
            $data['cdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];
        }

        if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){
            $data['hdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];
        }
        $data['is_half_hourly'] = 1;

        if(preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $data['hour'])) 
        {
            $this->db->set('created_on', 'NOW()', FALSE);
            // $this->db->set('created_by', $user_id, FALSE);
            $this->db->set('modify_on', 'NOW()', FALSE);
            $this->db->insert($this->_table_hourly, $data);
            $id = $this->db->insert_id(); 
            return $id;
        }
    }

    // to delete data if already exist in database
    public function delete_hourly_dynamic_submission_utility_ifexists($data = array()){

        $site_id = $data['site_id'];
        $month_id = $data['month_id'];
        $year_id = $data['year_id'];
        $date_id = $data['date_id'];
        $hour    = $data['hour'];
        $utility_title_id = $data['utility_title_id'];

        $this->db->select("*");
        $this->db->from('hourly_reading_utilities_title_data');
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $this->db->where('hour',$hour);
        $this->db->where('utility_title_id',$utility_title_id);
        if(isset($data['is_half_hourly'])){
            $this->db->where('is_half_hourly', $data['is_half_hourly']);
        }
        $query1 = $this->db->get();

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id',$site_id);
            $this->db->where('month_id',$month_id);
            $this->db->where('year_id',$year_id); 
            $this->db->where('date_id',$date_id);
            $this->db->where('hour',$hour);
            $this->db->where('utility_title_id',$utility_title_id);
            if(isset($data['is_half_hourly'])){
                $this->db->where('is_half_hourly', $data['is_half_hourly']);
            }
            $this->db->delete('hourly_reading_utilities_title_data');  
        }
    } 
    // insert data into hourly utilies cost table from import
    public function insert_hourly_dynamic_submission_utilities($data){
        $this->db->insert('hourly_reading_utilities_title_data', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    // to delete data if already exist in database 
    public function delete_hourly_fixed_submission_utility_ifexists($data = array()){
        
        $site_id        = $data['site_id'];
        $month_id       = $data['month_id'];
        $year_id        = $data['year_id'];
        $date_id        = $data['date_id'];
        $hour           = $data['hour'];
        $is_half_hourly = $data['is_half_hourly'];

        // is_half_hourly field is used for 
        $this->db->select("*");
        $this->db->from('hourly_reading_utilities_data');
        $this->db->where('site_id',$site_id);
        $this->db->where('month_id',$month_id);
        $this->db->where('year_id',$year_id);
        $this->db->where('date_id',$date_id);
        $this->db->where('hour',$hour);
        $this->db->where('is_half_hourly',$is_half_hourly);
        if(isset($data['is_half_hourly'])){
            $this->db->where('is_half_hourly', $data['is_half_hourly']);
        }
        $query1 = $this->db->get(); 

        if ($query1->num_rows() > 0) {
            $this->db->where('site_id', $site_id);
            $this->db->where('month_id', $month_id);
            $this->db->where('year_id', $year_id); 
            $this->db->where('date_id', $date_id);
            $this->db->where('hour', $hour);
            $this->db->where('is_half_hourly', $is_half_hourly);
            if(isset($data['is_half_hourly'])){
                $this->db->where('is_half_hourly', $data['is_half_hourly']);
            }
            $this->db->delete('hourly_reading_utilities_data');  
        }
    }
    // to insert data 
    public function insert_hourly_fixed_submission_utilities($data, $cddhddData = array()){

        unset($data['']);
        
        if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){
            $data['cdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];
        }

        if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){
            $data['hdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];
        }

        $this->db->insert('hourly_reading_utilities_data', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    
}
