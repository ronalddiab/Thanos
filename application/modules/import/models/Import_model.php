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



    /**
     * Delete existing utilities_cost row (site/month/year) then insert a fresh record.
     * CDD/HDD: if not supplied in $data, keep previously stored DB values.
     */
    public function insert_entity_details($data, $cddhddData = array())
    {
        unset($data['']);

        $siteId = $data['site_id'];
        $monthId = $data['month_id'];
        $yearId = $data['year_id'];

        // Read existing CDD/HDD before delete so blank Excel cells do not wipe them
        $existingCdd = null;
        $existingHdd = null;
        $this->db->select('cdd, hdd');
        $this->db->from($this->_table);
        $this->db->where('site_id', $siteId);
        $this->db->where('month_id', $monthId);
        $this->db->where('year_id', $yearId);
        $existing = $this->db->get()->row_array();
        if (!empty($existing)) {
            $existingCdd = $existing['cdd'];
            $existingHdd = $existing['hdd'];
        } else {
            if (!empty($cddhddData[$this->_table][$siteId][$yearId][$monthId]['cdd'])) {
                $existingCdd = $cddhddData[$this->_table][$siteId][$yearId][$monthId]['cdd'];
            }
            if (!empty($cddhddData[$this->_table][$siteId][$yearId][$monthId]['hdd'])) {
                $existingHdd = $cddhddData[$this->_table][$siteId][$yearId][$monthId]['hdd'];
            }
        }

        $cddProvided = array_key_exists('cdd', $data) && $data['cdd'] !== '' && $data['cdd'] !== null;
        $hddProvided = array_key_exists('hdd', $data) && $data['hdd'] !== '' && $data['hdd'] !== null;

        if (!$cddProvided) {
            $data['cdd'] = $existingCdd;
        }
        if (!$hddProvided) {
            $data['hdd'] = $existingHdd;
        }

        $now = date('Y-n-j h:i:s');
        $data['created_on'] = $now;
        $data['modify_on'] = $now;

        $this->db->where('site_id', $siteId);
        $this->db->where('month_id', $monthId);
        $this->db->where('year_id', $yearId);
        $this->db->delete($this->_table);

        $this->db->insert($this->_table, $data);

        return $this->db->insert_id();
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
        
        // $this->db->where('site_id',$site_id);

        // $this->db->where('month_id',$month_id);

        // $this->db->where('year_id',$year_id); 

        // $this->db->delete($this->_table);

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

        // $this->db->select("id");

        // $this->db->from($this->_table_daily);

        // $this->db->where('site_id',$site_id);

        // $this->db->where('month_id',$month_id);

        // $this->db->where('year_id',$year_id);

        // $this->db->where('date_id',$date_id);

        // $query1 = $this->db->get();



        // if ($query1->num_rows() > 0) {

            $this->db->where('site_id',$site_id);

            $this->db->where('month_id',$month_id);

            $this->db->where('year_id',$year_id); 

            $this->db->where('date_id',$date_id); 

            // echo 'In delete()'.'<br/>';           

            $this->db->delete($this->_table_daily);  

        // }

    }



    public function insert_daily_utilities($batchData, $cddhddData = array()){

        // unset($batchData['']);



        foreach ($batchData as $key => $data) {

            if(isset($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){

                $data['cdd'] = $cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];

            }



            if(isset($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){

                $data['hdd'] = $cddhddData[$this->_table_daily][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];

            }
            $this->db->insert($this->_table_daily, $data);

        }

        return '';

        }



    public function insert_daily_fixed_submission_utilities($batchData, $cddhddData = array()){

        unset($batchData['']);

        $status = false;

        foreach ($batchData as $key => $data) {

            if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){

                $data['cdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];

            }



            if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){

                $data['hdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];

            }

            $status = $this->db->insert('daily_reading_utilities_data', $data);

        }

        return $status;

        }



    public function insert_daily_dynamic_submission_utilities($data){

        saveBulkData($data,'daily_reading_utilities_title_data');

        $status = false;

        foreach ($data as $value) {

           $status = $this->db->insert('daily_reading_utilities_title_data', $value);             

        }

        return $status;

    }



    public function delete_daily_fixed_submission_utility_ifexists($data = array()){

        $site_id = $data['site_id'];

        $month_id = $data['month_id'];

        $year_id = $data['year_id'];

        $date_id = $data['date_id'];



        $this->db->select("id");

        $this->db->from('daily_reading_utilities_data');

        $this->db->where('site_id',$site_id);

        $this->db->where('month_id',$month_id);

        $this->db->where('year_id',$year_id);

        $this->db->where('date_id',$date_id);

        $query1 = $this->db->get();

        if(!is_bool($query1)){

            if ($query1->num_rows() > 0) {

                $this->db->where('site_id',$site_id);

                $this->db->where('month_id',$month_id);

                $this->db->where('year_id',$year_id); 

                $this->db->where('date_id',$date_id);

                $this->db->delete('daily_reading_utilities_data');  

            }

        }

        

    }



    public function delete_daily_dynamic_submission_utility_ifexists($data = array()){

        $site_id = $data['site_id'];

        $month_id = $data['month_id'];

        $year_id = $data['year_id'];

        $date_id = $data['date_id'];

        $utility_title_id = $data['utility_title_id'];



        $this->db->select("id");

        $this->db->from('daily_reading_utilities_title_data');

        $this->db->where('site_id',$site_id);

        $this->db->where('month_id',$month_id);

        $this->db->where('year_id',$year_id);

        $this->db->where('date_id',$date_id);

        $this->db->where('utility_title_id',$utility_title_id);        

        $query1 = $this->db->get();

        if(!is_bool($query1)){

            if ($query1->num_rows() > 0) {

                $this->db->where('site_id',$site_id);

                $this->db->where('month_id',$month_id);

                $this->db->where('year_id',$year_id); 

                $this->db->where('date_id',$date_id);

                $this->db->where('utility_title_id',$utility_title_id);

                $this->db->delete('daily_reading_utilities_title_data');  

            }

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



        $this->db->select("id");

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

    public function insert_hourly_utilities($bulkData, $cddhddData = array()){

//        unset($bulkData['']);

        // get data for hourly 

        foreach ($bulkData as $key => $data) {

            if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){

                $bulkData[$key]['cdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];

            }



            if(isset($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){

                $bulkData[$key]['hdd'] = $cddhddData[$this->_table_hourly][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];

            }
            $bulkData[$key]['created_on'] = 'NOW()';
            $bulkData[$key]['modify_on'] = 'NOW()';
        }

        

        if($this->db->insert_batch($this->_table_hourly, $bulkData)) 

        {

            return true;

        }

        return false;

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

        $this->db->select("id");

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

        $this->db->delete('hourly_reading_utilities_title_data');  

        /*

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

        */

    } 

    // insert data into hourly utilies cost table from import

    public function insert_hourly_dynamic_submission_utilities($data){

        if($this->db->insert_batch('hourly_reading_utilities_title_data', $data)){

            return true;    

        }

        return false;    

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

        $this->db->select("id");

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

        $this->db->delete('hourly_reading_utilities_data');  

        /*

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

        */

    }

    // to insert data 

    public function insert_hourly_fixed_submission_utilities($bulkdata, $cddhddData = array()){



        unset($bulkdata['']);

        foreach ($bulkdata as $key => $data) {

            if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'])){

            $bulkdata[$key]['cdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['cdd'];

            }



            if(isset($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd']) && !empty($cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'])){

                $bulkdata[$key]['hdd'] = $cddhddData['daily_reading_utilities_data'][$data['site_id']][$data['year_id']][$data['month_id']][$data['date_id']]['hdd'];

            }

        }

        if($this->db->insert_batch('hourly_reading_utilities_data', $bulkdata)){

            return true;

        }

        return false;

    }



    // Daily utilities custom

    public function getSiteDetailByName($siteNames = array(),$fields = ""){

        if(strlen($fields) != 0){

            $this->db->select($fields);

        }else{

            $this->db->select('*');

        }

        $this->db->from(TBL_SITES);

        if(!empty($siteNames)){

            $this->db->where_in('site_location_name', $siteNames);

        }

        $this->db->where('status',1);

        $query = $this->db->get();

        $result = array();

        if(!is_bool($query)){

            if ($query->num_rows() != 0) {

                $resultData = $query->result_array();

                foreach ($resultData as $value) {

                    $result[$value['site_location_name']] = $value;

                }

            }

        }

        return $result;

    }

    

    /*

    * Function getExistingDailyUtilities

    */



    public function getExistingDailyUtilities($filter = array(),$fields = ""){

        $resultData = array();

        $siteIds = $filter['site_id'];

        $monthIds = $filter['month_id'];

        $yearIds = $filter['year_id'];

        $dateIds = $filter['date_id'];



        if(strlen($fields) != 0){

            $this->db->select($fields);

        }else{

            $this->db->select('*');

        }

        

        $this->db->from($this->_table_daily);

        $this->db->where('site_id',$siteIds);

        $this->db->where('month_id',$monthIds);

        $this->db->where('year_id',$yearIds);

        $this->db->where_in('date_id',$dateIds);

        $query = $this->db->get();

        if(!is_bool($query)){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }

        return $resultData;

    }



    //batchSelectUtility

    public function batchSelectUtility($conditions = array(),$fields = ""){

        $resultData = array();
        if(sizeof($conditions) > 0){
            if(strlen($fields) != 0){
                $this->db->select($fields);
            }else{
                $this->db->select('*');
            }
            $this->db->from($this->_table_daily);
            foreach ($conditions as $siteId => $site) {

                $siteWhere = "";
                $siteWhere .= ' (`site_id` = '.$siteId.' AND `year_id` = '.$site['year_id'].') ';
                $this->db->or_where($siteWhere);

                $this->db->where_in('month_id', $site['month_id']);

            }
            $query = $this->db->get();

        }


        if(!is_bool($query) && !empty($query) && $query != null){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }

        $finalArray = array();

        if(!empty($resultData)){

            foreach ($resultData as $key => $value) {

                if(!array_key_exists($value['id'], $finalArray[$value['site_id']][$value['year_id']][$value['month_id']])){

                    $finalArray[$value['site_id']][$value['year_id']][$value['month_id']][$value['date_id']] = $value['id'];

                }

            }

        }

        return $finalArray;

    }



    public function deleteDailyUtilityIfexists($data){

        $this->db->where('site_id',$data['site_id']);
        $this->db->where('year_id',$data['year_id']);
        $this->db->where('month_id',$data['month_id']);
        $this->db->where('date_id',$data['date_id']);

        $this->db->delete($this->_table_daily);

    }



    public function deleteDailyFixedSubmissionUtilityIfexists($data){

        $this->db->where_in('id',$data);

        return $this->db->delete('daily_reading_utilities_data');

    }



    public function batchSelectFixedUtility($conditions = array(),$fields = ""){

        if(sizeof($conditions) > 0){

            if(strlen($fields) != 0){

                $this->db->select($fields);

            }else{

                $this->db->select('*');

            }

            $this->db->from('daily_reading_utilities_data');

            foreach ($conditions as $siteId => $site) {

                $siteWhere = "";

                $siteWhere .= ' (`site_id` = '.$siteId.' AND `month_id` = '.$site['month_id'].' AND `year_id` = '.$site['year_id'].') ';

                $this->db->or_where($siteWhere);

            }

        }

        $query = $this->db->get();

        if(!is_bool($query)){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }



        $finalArray = array();

        if(!empty($resultData)){

            foreach ($resultData as $key => $value) {

                if(!array_key_exists($value['id'], $finalArray[$value['site_id']][$value['year_id']][$value['month_id']])){

                    $finalArray[$value['site_id']][$value['year_id']][$value['month_id']][$value['date_id']] = $value['id'];

                }

            }

        }

        return $finalArray;

    }



    public function deleteDailyDynamicSubmissionUtilityIfexists($data = array()){

        $this->db->where_in('id',$data);

       return $this->db->delete('daily_reading_utilities_title_data');        

    }



    public function batchSelectDynamicUtility($conditions = array(),$fields = ""){

        if(sizeof($conditions) > 0){

            if(strlen($fields) != 0){

                $this->db->select($fields);

            }else{

                $this->db->select('*');

            }

            $this->db->from('daily_reading_utilities_title_data');

            foreach ($conditions as $siteId => $site) {

                $siteWhere = "";

                $siteWhere .= ' (`site_id` = '.$siteId.' AND `month_id` = '.$site['month_id'].' AND `year_id` = '.$site['year_id'].') ';

                $this->db->or_where($siteWhere);

            }

        }

        $query = $this->db->get();

        if(!is_bool($query)){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }

        

        $finalArray = array();

        if(!empty($resultData)){

            foreach ($resultData as $key => $value) {

                $mkey = $value['site_id'].'_'.$value['year_id'].'_'.$value['month_id'].'_'.$value['date_id'].'_'.$value['utility_title_id'];

                if(!array_key_exists($mkey, $finalArray)){

                    $finalArray[$mkey] = $value['id'];

                }

            }

        }

        return $finalArray;

    }



    // delete data from hourly utilies cost table from import

    public function deleteHourlyUtilityIfexists($data){

        $this->db->where_in('id',$data);

        $this->db->delete($this->_table_hourly);

    }





    public function selectBatchOfHourlyUtilities($conditions,$fields = ""){

        $resultData = array();

        if(sizeof($conditions) > 0){

            if(strlen($fields) != 0){

                $this->db->select($fields);

            }else{

                $this->db->select('*');

            }

            $this->db->from($this->_table_hourly);

            foreach ($conditions as $siteId => $site) {

                $siteWhere = "";

                $siteWhere = ' (`site_id` = '.$siteId.' AND `month_id` = '.$site['month_id'].' AND `year_id` = '.$site['year_id'].' AND `date_id` = '.$site['date_id'].' ) ';

                $this->db->or_where($siteWhere);

            }

        }

        $query = $this->db->get();

        if(!is_bool($query)){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }

        

        return $resultData;

    }





    public function batchSelectDynamicSubmissionUtilityIfexists($conditions,$fields = ""){



    }



    public function batchSelectFixedSubmissionUtilityIfexists($conditions,$fields = ""){

        $table = 'hourly_reading_utilities_data';

        $resultData = array();

        if(sizeof($conditions) > 0){

            if(strlen($fields) != 0){

                $this->db->select($fields);

            }else{

                $this->db->select('*');

            }

            $this->db->from($table);

            foreach ($conditions as $siteId => $site) {

                $siteWhere = "";

                $siteWhere = ' (`site_id` = '.$siteId.' AND `month_id` = '.$site['month_id'].' AND `year_id` = '.$site['year_id'].') ';

                $this->db->or_where($siteWhere);

            }

        }

        $query = $this->db->get();

        if(!is_bool($query)){

            if($query->num_rows() > 0){

                $resultData = $query->result_array();

            }

        }

        return $resultData;



    }



    public function deleteHourlyFixedSubmissionUtilityIfexists($data = array()){

        $this->db->where_in('id',$data);

        $this->db->delete('hourly_reading_utilities_data');

    }

}
