<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Reports_energy_model extends Base_Model {



    protected $_tbl_utilities = TBL_UTILITIES_COST;

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_energy_modelling = 'energy_modelling';

            

    public $utilities_month = "";

    public $utilities_year = "";

    public $site_id = "";

    public $role_id = "";

    public $user_id = "";

    public $year_id = '';



    function __construct() {

        parent::__construct();

    }

    

    function getUtility() {

        $this->db->select('*');

        $this->db->from($this->_tbl_utilities);

        $this->db->where('site_id', $this->site_id);

        $this->db->where('year_id', $this->utilities_year);

        $this->db->order_by('month_id');



        $result = $this->db->get();

        return $result->result_array();

    }

    

    function getSiteDetails(){

        $this->db->select('*');

        $this->db->from($this->_tbl_sites);

        $this->db->where('id', $this->site_id);

        $result = $this->db->get();

        $siteArray = $result->row_array();
        $this->load->model('sites/sites_model');
        if (!empty($siteArray)) {
            $siteArray = $this->sites_model->getEmissionFactorYearly($siteArray);
            $siteArray = $this->sites_model->getSiteListAreaFormat($siteArray);
            $siteArray = $this->sites_model->getSiteResidenceAllocation($siteArray);
            return $siteArray;
        } else {
            return array();
        }

    }

    

    function get_energy_modelling(){

        $energy_data = array();

        $this->db->select('*');

        $this->db->from($this->_tbl_energy_modelling);

        $this->db->where('site_id', $this->site_id);

        // $this->db->where('year_id', $this->year_id);

        

        $result = $this->db->get()->result_array();

        

        foreach ($result as $energy) {

            $energy_data[$energy['utility']] = [

                'cdd' => $energy['cdd'],

                'hdd' => $energy['hdd'],

                'occupancy' => $energy['occupancy'],

                'x' => $energy['x'],

                'days' => $energy['days'],

                'r2' => $energy['r2'],

                'report' => $energy['report']

            ];

        }

        return $energy_data;

    }

}

