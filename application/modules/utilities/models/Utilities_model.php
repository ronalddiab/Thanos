<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Utilities_model extends Base_Model
{

    protected $_tbl_utilities            = 'utilities_cost';
    protected $_tbl_utilities_cost_daily = 'utilities_cost_daily';
    protected $_tbl_electricity_tariff   = 'total_purchased_electricity_tariff';
    protected $_tbl_csr_ngo              = 'csr_ngo';
    protected $_tbl_csr_ngo_actions      = 'csr_ngo_actions';
    protected $_tbl_csr_ngo_actions_images = 'csr_ngo_actions_images';
    protected $_tbl_sites                = 'sites';
    protected $_tbl_notifications        = 'notifications';
    protected $_tbl_csr_hr_data          = 'csr_hr_data';
    protected $_tbl_csr_waste_data       = 'csr_waste_data';
    protected $_tbl_csr_biodiversity_data = 'csr_biodiversity_data';
    protected $_tbl_csr_biodiversity_images = 'csr_biodiversity_images';
    public $utilities_month              = "";
    public $utilities_year               = "";
    public $site_id                      = "";
    public $role_id                      = "";
    public $user_id                      = "";
    public $utilities_quarter_quarter    = "";
    public $utilities_quarter_year       = "";

    public $field_forex = [
	"maximum_demand_price",
	"total_maximum_demand",
	"fixed_fees",
	"total_purchased_electricity",
	"total_purchased_electricity_cost",
	"average_purchased_electricity",
	"total_onsite_generators_cost",
	"total_renewable_energy_production_cost",
	"total_renewable_energy_exported_cost",
	"total_electricity_cost",
	"average_cost_per_kwh",
	"electricity_total_budget_cost",
	"fuel_oil_hot_water_boilers_rate",
	"fuel_oil_hot_water_boilers_cost",
	"fuel_oil_steam_boilers_rate",
	"fuel_oil_steam_boilers_cost",
	"fuel_oil_others_rate",
	"fuel_oil_others_cost",
	"total_fuel_oil_rate",
	"total_fuel_oil_cost",
	"fuel_total_budget_cost",
	"lpg_hot_water_boilers_rate",
	"lpg_hot_water_boilers_cost",
	"lpg_steam_boilers_rate",
	"lpg_steam_boilers_cost",
	"lpg_kitchen_rate",
	"lpg_kitchen_cost",
	"total_lpg_rate",
	"total_lpg_cost",
	"lpg_fixed_cost",
	"lpg_total_budget_cost",
	"natural_gas_hot_water_boilers_rate",
	"natural_gas_hot_water_boilers_cost",
	"natural_gas_steam_boilers_rate",
	"natural_gas_steam_boilers_cost",
	"natural_gas_kitchen_rate",
	"natural_gas_kitchen_cost",
	"total_natural_gas_rate",
	"total_natural_gas_cost",
	"natural_gas_fixed_cost",
	"natural_gas_total_budget_cost",
	"district_heating_rate",
	"district_heating_cost",
	"district_heating_fixed_cost",
	"district_heating_total_budget_cost",
	"district_cooling_rate",
	"district_cooling_cost",
	"district_cooling_fixed_cost",
	"district_cooling_total_budget_cost",
	"water_utility_supply_rate",
	"water_utility_supply_cost",
	"waste_water_rate",
	"waste_water_cost",
	"water_ro_rate",
	"water_ro_cost",
	"water_Cisterns_rate",
	"water_Cisterns_cost",
	"water_total_consumption_rate",
	"water_total_consumption_cost",
	"water_fixed_cost",
	"water_total_consumption_budget_cost",
    ];

    public function __construct()
    {
	parent::__construct();
    }

    public function getUtilityDaily()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities_cost_daily);
	$this->db->where('site_id', $this->site_id);
	//$this->db->where('id =', $id);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);
	$this->db->where('date_id', $this->utilities_date);

	$result = $this->db->get();
	//echo $this->db->last_query();
	return $result->row_array();
    }

    public function saveUtilityDaily($postdata = array())
    {
	$data           = array();
	$decimal_places = 4;
	$utility        = $this->getUtilityDaily();

	// Auto set
	$data['site_id']    = $postdata['site_id'];
	$data['project_id'] = 0; //$postdata['project_id']; (NO need project id in this table)
	$data['year_id']    = $postdata['year'];
	$data['month_id']   = $postdata['month'];
	$data['date_id']    = $postdata['date'];

	// User inputs
	$data['total_room_night']             = isset($postdata['total_room_night']) ? $postdata['total_room_night'] : '';
	$data['total_guests']                 = isset($postdata['total_guests']) ? $postdata['total_guests'] : '';
	$data['total_electricity_kwh']        = isset($postdata['total_electricity_kwh']) ? $postdata['total_electricity_kwh'] : '';
	$data['total_electricity_kwh_tariff'] = isset($postdata['total_electricity_kwh_tariff']) ? $postdata['total_electricity_kwh_tariff'] : '';
	$data['total_diesel_fuel']            = isset($postdata['total_diesel_fuel']) ? $postdata['total_diesel_fuel'] : '';
	$data['total_diesel_fuel_tariff']     = isset($postdata['total_diesel_fuel_tariff']) ? $postdata['total_diesel_fuel_tariff'] : '';
	/* $data['total_heavy_fuel']                          = isset($postdata['total_heavy_fuel'])?$postdata['total_heavy_fuel']:'';
	$data['total_heavy_fuel_tariff']                   = isset($postdata['total_heavy_fuel_tariff'])?$postdata['total_heavy_fuel_tariff']:''; */
	$data['total_lpg_consumption']                     = isset($postdata['total_lpg_consumption']) ? $postdata['total_lpg_consumption'] : '';
	$data['total_lpg_consumption_tariff']              = isset($postdata['total_lpg_consumption_tariff']) ? $postdata['total_lpg_consumption_tariff'] : '';
	$data['total_water_consumption']                   = isset($postdata['total_water_consumption']) ? $postdata['total_water_consumption'] : '';
	$data['total_water_consumption_tariff']            = isset($postdata['total_water_consumption_tariff']) ? $postdata['total_water_consumption_tariff'] : '';
	$data['total_landscape_water_consumption']         = isset($postdata['total_landscape_water_consumption']) ? $postdata['total_landscape_water_consumption'] : '';
	$data['total_landscape_water_consumption_tariff']  = isset($postdata['total_landscape_water_consumption_tariff']) ? $postdata['total_landscape_water_consumption_tariff'] : '';

	$data['total_waste_water_consumption']         = isset($postdata['total_waste_water_consumption']) ? $postdata['total_waste_water_consumption'] : '';
	$data['total_waste_water_consumption_tariff']  = isset($postdata['total_waste_water_consumption_tariff']) ? $postdata['total_waste_water_consumption_tariff'] : '';

	$data['total_water_ro_consumption']         = isset($postdata['total_water_ro_consumption']) ? $postdata['total_water_ro_consumption'] : '';
	$data['total_water_ro_consumption_tariff']  = isset($postdata['total_water_ro_consumption_tariff']) ? $postdata['total_water_ro_consumption_tariff'] : '';

	$data['total_natural_gas_consumption']             = isset($postdata['total_natural_gas_consumption']) ? $postdata['total_natural_gas_consumption'] : '';
	$data['total_natural_gas_consumption_tariff']      = isset($postdata['total_natural_gas_consumption_tariff']) ? $postdata['total_natural_gas_consumption_tariff'] : '';
	$data['total_district_cooling_consumption']        = isset($postdata['total_district_cooling_consumption']) ? $postdata['total_district_cooling_consumption'] : '';
	$data['total_district_cooling_consumption_tariff'] = isset($postdata['total_district_cooling_consumption_tariff']) ? $postdata['total_district_cooling_consumption_tariff'] : '';
	$data['total_district_heating_consumption']        = isset($postdata['total_district_heating_consumption']) ? $postdata['total_district_heating_consumption'] : '';
	$data['total_district_heating_consumption_tariff'] = isset($postdata['total_district_heating_consumption_tariff']) ? $postdata['total_district_heating_consumption_tariff'] : '';
	$data['cdd']                                       = isset($postdata['cdd']) ? $postdata['cdd'] : '';
	$data['hdd']                                       = isset($postdata['hdd']) ? $postdata['hdd'] : '';

	//round tariff values upto 4 decimal places
	$data['total_electricity_kwh_tariff']              = round($data['total_electricity_kwh_tariff'], $decimal_places);
	$data['total_diesel_fuel_tariff']                  = round($data['total_diesel_fuel_tariff'], $decimal_places);
	$data['total_heavy_fuel_tariff']                   = round($data['total_heavy_fuel_tariff'], $decimal_places);
	$data['total_lpg_consumption_tariff']              = round($data['total_lpg_consumption_tariff'], $decimal_places);
	$data['total_water_consumption_tariff']            = round($data['total_water_consumption_tariff'], $decimal_places);
	$data['total_landscape_water_consumption_tariff']  = round($data['total_landscape_water_consumption_tariff'], $decimal_places);
	$data['total_waste_water_consumption_tariff']      = round($data['total_waste_water_consumption_tariff'], $decimal_places);
	$data['total_water_ro_consumption_tariff']         = round($data['total_water_ro_consumption_tariff'], $decimal_places);
	$data['total_natural_gas_consumption_tariff']      = round($data['total_natural_gas_consumption_tariff'], $decimal_places);
	$data['total_district_cooling_consumption_tariff'] = round($data['total_district_cooling_consumption_tariff'], $decimal_places);
	$data['total_district_heating_consumption_tariff'] = round($data['total_district_heating_consumption_tariff'], $decimal_places);

	$data['modify_by'] = $postdata['user_id'];
	if (!empty($utility)) {
	    $data['id'] = $postdata['id'];
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->where('id', $postdata['id']);
	    $this->db->where('month_id', $postdata['month']);
	    $this->db->where('year_id', $postdata['year']);
	    $this->db->where('date_id', $postdata['date']);
	    $this->db->update($this->_tbl_utilities_cost_daily, $data);
	    $id = $data['id'];
	    $action = "Update";
	} else {
	    $data['created_by'] = $postdata['user_id'];
	    $this->db->set('created_on', 'NOW()', false);
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->insert($this->_tbl_utilities_cost_daily, $data);
	    $id = $this->db->insert_id();
	    $action = "Create";
	}
	// Save audit trail
	$date  = $postdata['date'];

	$dateObj   = DateTime::createFromFormat('!m', $postdata['month']);
	$monthName = $dateObj->format('F');
	$year  = $postdata['year'];
	$additional_field = $date.' '.$monthName.', '.$year;

	saveAuditTrail($postdata['user_id'],$postdata['site_id'],'Utilities (Daily) - ('.$additional_field.')',$action);
	return $id;
    }

    public function getUtility()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	//$this->db->where('id =', $id);
	$this->db->where('month_id', $this->utilities_month);
        $this->db->where('year_id', $this->utilities_year);

        $result = $this->db->get();
        return $result->row_array();
    }

    public function getUtilityYTD()
    {
        $this->db->select('SUM(COALESCE(total_electricity_kwh, 0)) as total_electricity_kwh,
            SUM(COALESCE(total_lpg, 0)) as total_lpg,
            SUM(COALESCE(total_fuel_oil, 0)) as total_fuel_oil,
            SUM(COALESCE(total_natural_gas, 0)) as total_natural_gas,
            SUM(COALESCE(district_heating, 0)) as district_heating,
            SUM(COALESCE(district_cooling, 0)) as district_cooling,
            SUM(COALESCE(water_total_consumption, 0)) as water_total_consumption');
        $this->db->from($this->_tbl_utilities);
        $this->db->where('site_id', $this->site_id);
        $this->db->where('month_id <=', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);

	$result = $this->db->get();
	return $result->row_array();
    }

    public function getUtilityForExportToMonth($id)
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('id', $id);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);

	$result = $this->db->get();
	return $result->row_array();
    }

    public function getUtilitylm()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	//$this->db->where('id =', $id);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id1', $this->utilities_year);

	$result = $this->db->get();
	//echo $this->db->last_query();
	return $result->row_array();
    }

    public function getUtilityWithForex()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	//$this->db->where('id =', $id);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);
	$result      = $this->db->get();
	$resultArray = $result->row_array();

	$resultArray['forex'] = (isset($resultArray['forex']) && $resultArray['forex'] != 0) ? $resultArray['forex'] : 1 ;



	//site_info
	$this->db->select('*');
	$this->db->from($this->_tbl_sites);
	$this->db->where('id', $this->site_id);
	$site_info = $this->db->get()->row_array();

	if (!empty($site_info['local_currency']) && !empty($resultArray['forex'])) {
	    foreach ($this->field_forex as $field) {
		if (!empty($resultArray[$field])) {
		    $resultArray[$field] = ($resultArray[$field] * $resultArray['forex']);
		}
	    }
	}

	return $resultArray;
    }

    public function getMonthUtility()
    {
	//from daily utility table
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities_cost_daily);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);

	$result = $this->db->get();
	return $result->result_array();
    }

    public function getSiteBaselineUtility($filters)
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	if ($this->utilities_year) {
	    $this->db->where_in('year_id', [$this->utilities_year, $filters['baseline_regression_year']]);
	}
	$this->db->order_by("year_id", "asc");
	$this->db->order_by("month_id", "asc");
	$this->db->order_by("utilities_cost.id", "asc");

	$result = $this->db->get();
	return $result->result_array();
    }

    public function getSiteUtility()
    {
        $this->db->select('*');
        $this->db->from($this->_tbl_utilities);
        if(!empty($this->site_id)) {
            $this->db->where('site_id', $this->site_id);
        }
        if ($this->utilities_year) {
            // $this->db->where('year_id', $this->utilities_year);
            $this->db->where_in('year_id', [$this->utilities_year, date('Y') - 1, date('Y') - 2]);
        }
        $this->db->where('utilities_cost.site_id != ', 0); 
        $this->db->where('utilities_cost.year_id != ', 0); 
        $this->db->where('utilities_cost.month_id != ', 0); 
        if(empty($this->site_id)) {
            $this->db->order_by('utilities_cost.site_id', "asc");
        }
        $this->db->order_by("year_id", "asc");        
        $this->db->order_by("month_id", "asc");

        $result = $this->db->get();
        return $result->result_array();
    }

    
    public function getSiteUtilityDaily()
    {
        $this->db->select('*');
        $this->db->from($this->_tbl_utilities_cost_daily);
        if(empty($this->site_id)) {
           if ($this->utilities_year) {
                $this->db->where_in('year_id', [$this->utilities_year, date('Y') - 1, date('Y') - 2]);
            }
        }
        $this->db->where('utilities_cost_daily.site_id != ', 0); 
        $this->db->where('utilities_cost_daily.year_id != ', 0); 
        $this->db->where('utilities_cost_daily.month_id != ', 0); 
        $this->db->where('utilities_cost_daily.date_id != ', 0); 
        $this->db->order_by('utilities_cost_daily.site_id', "asc");
        $this->db->order_by("year_id", "asc");        
        $this->db->order_by("month_id", "asc");
        $this->db->order_by("date_id", "asc");

        $result = $this->db->get();
        return $result->result_array();
    }
    
    public function getSiteDailyByMonthlyUtility($site_id,$year_id,$month_id,$utility_name, $key)
    {
        $this->db->select('SUM(COALESCE('.$utility_name.', 0)) as '.$key.'');
        $this->db->where('site_id', $site_id);
        $this->db->where('year_id', $year_id);
        $this->db->where('month_id', $month_id);
        $this->db->from($this->_tbl_utilities_cost_daily);

        $result = $this->db->get();
        return $result->row_array();
    }

    public function getSiteUtilityLastTenMonths()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	if ($this->utilities_year) {
	    $this->db->where_in('year_id', [$this->utilities_year, date('Y') - 1, date('Y') - 2, date('Y') - 3, date('Y') - 4, date('Y') - 5, date('Y') - 6, date('Y') - 7, date('Y') - 8, date('Y') - 9]);
	}
	$this->db->order_by("year_id", "asc");
	$this->db->order_by("month_id", "asc");
	$this->db->order_by("utilities_cost.id", "asc");

	$result = $this->db->get();
	return $result->result_array();
    }

    public function getSiteUtilityLastYear()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	if ($this->utilities_year) {
	    $this->db->where('year_id', $this->utilities_year);
	}
	$this->db->where('month_id >= ', $this->utilities_month);

	$this->db->order_by("month_id", "asc");

	$result = $this->db->get();
	return $result->result_array();
    }
    public function getSiteUtilityCurYear()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $this->site_id);
	if ($this->utilities_year) {
	    $this->db->where('year_id', $this->utilities_year);
	}
	$this->db->where('month_id <= ', $this->utilities_month);

	$this->db->order_by("month_id", "asc");

	$result = $this->db->get();
	return $result->result_array();
    }

    public function saveUtility($postdata = array())
    {
	$data           = array();
	$decimal_places = 4;
	$utilityCheck        = $this->getUtility();
	$this->site_id    = $postdata['site_id'];
	$this->project_id = 0;
	$this->utilities_year    = $postdata['year'];
	$this->utilities_month   = $postdata['month'];
	$utility        = $this->getUtilityForExportToMonth($postdata['id']);

	// Auto set
	$data['site_id']    = $postdata['site_id'];
	$data['project_id'] = 0; //$postdata['project_id']; (NO need project id in this table)
	$data['year_id']    = $postdata['year'];
	$data['month_id']   = $postdata['month'];

	// User inputs
	$data['maximum_demand']                             = isset($postdata['maximum_demand']) ? $postdata['maximum_demand'] : '';
	$data['natural_gas_hot_water_boilers']              = isset($postdata['natural_gas_hot_water_boilers']) ? $postdata['natural_gas_hot_water_boilers'] : '';
	$data['natural_gas_steam_boilers']                  = isset($postdata['natural_gas_steam_boilers']) ? $postdata['natural_gas_steam_boilers'] : '';
	$data['natural_gas_kitchen']                        = isset($postdata['natural_gas_kitchen']) ? $postdata['natural_gas_kitchen'] : '';
	$data['lpg_hot_water_boilers']                      = isset($postdata['lpg_hot_water_boilers']) ? $postdata['lpg_hot_water_boilers'] : '';
	$data['lpg_steam_boilers']                          = isset($postdata['lpg_steam_boilers']) ? $postdata['lpg_steam_boilers'] : '';
	$data['lpg_kitchen']                                = isset($postdata['lpg_kitchen']) ? $postdata['lpg_kitchen'] : '';
	$data['fuel_oil_hot_water_boilers']                 = isset($postdata['fuel_oil_hot_water_boilers']) ? $postdata['fuel_oil_hot_water_boilers'] : '';
	$data['fuel_oil_steam_boilers']                     = isset($postdata['fuel_oil_steam_boilers']) ? $postdata['fuel_oil_steam_boilers'] : '';
	$data['fuel_oil_others']                            = isset($postdata['fuel_oil_others']) ? $postdata['fuel_oil_others'] : '';
	$data['water_utility_supply']                       = isset($postdata['water_utility_supply']) ? $postdata['water_utility_supply'] : '';
	$data['water_irrigation']                           = isset($postdata['water_irrigation']) ? $postdata['water_irrigation'] : '';
	$data['operation_general_waste']                    = isset($postdata['operation_general_waste']) ? $postdata['operation_general_waste'] : '';
	$data['operation_paper_waste']                      = isset($postdata['operation_paper_waste']) ? $postdata['operation_paper_waste'] : '';
	$data['operation_food_waste']                       = isset($postdata['operation_food_waste']) ? $postdata['operation_food_waste'] : '';
	$data['operation_cardboard_waste']                  = isset($postdata['operation_cardboard_waste']) ? $postdata['operation_cardboard_waste'] : '';
	$data['operation_plastic_waste']                    = isset($postdata['operation_plastic_waste']) ? $postdata['operation_plastic_waste'] : '';
	$data['operation_glass_waste']                      = isset($postdata['operation_glass_waste']) ? $postdata['operation_glass_waste'] : '';
	$data['waste_water']                                = isset($postdata['waste_water']) ? $postdata['waste_water'] : '';
	$data['water_ro']                                   = isset($postdata['water_ro']) ? $postdata['water_ro'] : '';
	$data['water_Cisterns']                             = isset($postdata['water_Cisterns']) ? $postdata['water_Cisterns'] : '';
	/*$data['water_consumption_breakdown_cooling_towers'] = isset($postdata['water_consumption_breakdown_cooling_towers']) ? $postdata['water_consumption_breakdown_cooling_towers'] : '';
	$data['water_consumption_breakdown_boh']            = isset($postdata['water_consumption_breakdown_boh']) ? $postdata['water_consumption_breakdown_boh'] : '';
	$data['water_consumption_breakdown_rooms']          = isset($postdata['water_consumption_breakdown_rooms']) ? $postdata['water_consumption_breakdown_rooms'] : '';*/
	$data['total_room_night']                           = isset($postdata['total_room_night']) ? $postdata['total_room_night'] : '';
	$data['total_guests']                               = isset($postdata['total_guests']) ? $postdata['total_guests'] : '';
	$data['total_laundered']                            = isset($postdata['total_laundered']) ? $postdata['total_laundered'] : '';
	$data['total_fb_services']                          = isset($postdata['total_fb_services']) ? $postdata['total_fb_services'] : '';
        $data['total_room_night_budget']                          = isset($postdata['total_room_night_budget']) ? $postdata['total_room_night_budget'] : '';
        $data['total_guests_budget']                        = isset($postdata['total_guests_budget']) ? $postdata['total_guests_budget'] : '';
	$data['cdd']                                        = isset($postdata['cdd']) ? $postdata['cdd'] : '';
	$data['hdd']                                        = isset($postdata['hdd']) ? $postdata['hdd'] : '';
	$data['revenue']                                    = isset($postdata['revenue']) ? $postdata['revenue'] : '';
	$data['forex']                                      = isset($postdata['forex']) ? $postdata['forex'] : 1;
		$data['vehicle_petrol']                             = isset($postdata['vehicle_petrol']) ? $postdata['vehicle_petrol'] : 1;
		$data['fleet_petrol']                             = isset($postdata['fleet_petrol']) ? $postdata['fleet_petrol'] : 1;
	$data['total_f_b_sales']                            = isset($postdata['total_f_b_sales']) ? $postdata['total_f_b_sales'] : '';

	// New Fields
	$data['fixed_fees']                       = isset($postdata['fixed_fees']) ? $postdata['fixed_fees'] : '';
	$data['maximum_demand_unit']              = isset($postdata['maximum_demand_unit']) ? $postdata['maximum_demand_unit'] : '';
	$data['maximum_demand_price']             = isset($postdata['maximum_demand_price']) ? $postdata['maximum_demand_price'] : '';
	$data['total_maximum_demand']             = isset($postdata['total_maximum_demand']) ? $postdata['total_maximum_demand'] : '';
	$data['onsite_generators_quantity']       = isset($postdata['onsite_generators_quantity']) ? $postdata['onsite_generators_quantity'] : '';
	$data['onsite_generators_price']          = isset($postdata['onsite_generators_price']) ? $postdata['onsite_generators_price'] : '';
	$data['onsite_generators_fuel_oil_quantity']       = isset($postdata['onsite_generators_fuel_oil_quantity']) ? $postdata['onsite_generators_fuel_oil_quantity'] : '';
	$data['onsite_generators_fuel_oil_price']          = isset($postdata['onsite_generators_fuel_oil_price']) ? $postdata['onsite_generators_fuel_oil_price'] : '';
	$data['onsite_generators_natural_gas_quantity']       = isset($postdata['onsite_generators_natural_gas_quantity']) ? $postdata['onsite_generators_natural_gas_quantity'] : '';
	$data['onsite_generators_natural_gas_price']          = isset($postdata['onsite_generators_natural_gas_price']) ? $postdata['onsite_generators_natural_gas_price'] : '';
	$data['total_electricity_kwh']            = isset($postdata['total_electricity_kwh']) ? $postdata['total_electricity_kwh'] : '';
	$data['total_purchased_electricity_cost'] = isset($postdata['total_purchased_electricity_cost']) ? $postdata['total_purchased_electricity_cost'] : '';

	$data['average_pf']                                  = isset($postdata['average_pf']) ? $postdata['average_pf'] : '';
	$data['average_cost_per_kwh']                        = isset($postdata['average_cost_per_kwh']) ? $postdata['average_cost_per_kwh'] : '';
	$data['water_utility_supply_rate']                   = isset($postdata['water_utility_supply_rate']) ? $postdata['water_utility_supply_rate'] : '';
	$data['water_utility_supply_cost']                   = isset($postdata['water_utility_supply_cost']) ? $postdata['water_utility_supply_cost'] : '';
	$data['water_irrigation_rate']                       = isset($postdata['water_irrigation_rate']) ? $postdata['water_irrigation_rate'] : '';
	$data['water_irrigation_cost']                       = isset($postdata['water_irrigation_cost']) ? $postdata['water_irrigation_cost'] : '';
	$data['waste_water_rate']                            = isset($postdata['waste_water_rate']) ? $postdata['waste_water_rate'] : '';
	$data['waste_water_cost']                            = isset($postdata['waste_water_cost']) ? $postdata['waste_water_cost'] : '';
	$data['water_ro_rate']                               = isset($postdata['water_ro_rate']) ? $postdata['water_ro_rate'] : '';
	$data['water_ro_cost']                               = isset($postdata['water_ro_cost']) ? $postdata['water_ro_cost'] : '';
	$data['water_Cisterns_rate']                         = isset($postdata['water_Cisterns_rate']) ? $postdata['water_Cisterns_rate'] : '';
	$data['water_Cisterns_cost']                         = isset($postdata['water_Cisterns_cost']) ? $postdata['water_Cisterns_cost'] : '';
	$data['water_total_consumption_rate']                = isset($postdata['water_total_consumption_rate']) ? $postdata['water_total_consumption_rate'] : '';
	$data['water_total_consumption_cost']                = isset($postdata['water_total_consumption_cost']) ? $postdata['water_total_consumption_cost'] : '';
	$data['water_total_consumption_budget']              = isset($postdata['water_total_consumption_budget']) ? $postdata['water_total_consumption_budget'] : '';
	$data['water_total_consumption_budget_cost']         = isset($postdata['water_total_consumption_budget_cost']) ? $postdata['water_total_consumption_budget_cost'] : '';
	$data['total_consumption_breakdown_budget']          = isset($postdata['total_consumption_breakdown_budget']) ? $postdata['total_consumption_breakdown_budget'] : '';
	$data['total_consumption_breakdown_budget_cost']     = isset($postdata['total_consumption_breakdown_budget_cost']) ? $postdata['total_consumption_breakdown_budget_cost'] : '';
	$data['fuel_oil_hot_water_boilers_rate'] = isset($postdata['fuel_oil_hot_water_boilers_rate']) ? $postdata['fuel_oil_hot_water_boilers_rate'] : '';
	$data['fuel_oil_hot_water_boilers_cost'] = isset($postdata['fuel_oil_hot_water_boilers_cost']) ? $postdata['fuel_oil_hot_water_boilers_cost'] : '';
	$data['fuel_oil_steam_boilers_rate']     = isset($postdata['fuel_oil_steam_boilers_rate']) ? $postdata['fuel_oil_steam_boilers_rate'] : '';
	$data['fuel_oil_steam_boilers_cost']     = isset($postdata['fuel_oil_steam_boilers_cost']) ? $postdata['fuel_oil_steam_boilers_cost'] : '';
	$data['fuel_oil_others_rate']            = isset($postdata['fuel_oil_others_rate']) ? $postdata['fuel_oil_others_rate'] : '';
	$data['fuel_oil_others_cost']            = isset($postdata['fuel_oil_others_cost']) ? $postdata['fuel_oil_others_cost'] : '';
	$data['fuel_total_budget']               = isset($postdata['fuel_total_budget']) ? $postdata['fuel_total_budget'] : '';
	$data['fuel_total_budget_cost']          = isset($postdata['fuel_total_budget_cost']) ? $postdata['fuel_total_budget_cost'] : '';
	 $data['total_fuel_oil']                  = isset($postdata['total_fuel_oil']) ? $postdata['total_fuel_oil'] : '';
	$data['total_fuel_oil_rate']             = isset($postdata['total_fuel_oil_rate']) ? $postdata['total_fuel_oil_rate'] : '';

	$data['lpg_hot_water_boilers_rate'] = isset($postdata['lpg_hot_water_boilers_rate']) ? $postdata['lpg_hot_water_boilers_rate'] : '';
	$data['lpg_hot_water_boilers_cost'] = isset($postdata['lpg_hot_water_boilers_cost']) ? $postdata['lpg_hot_water_boilers_cost'] : '';
	$data['lpg_steam_boilers_rate']     = isset($postdata['lpg_steam_boilers_rate']) ? $postdata['lpg_steam_boilers_rate'] : '';
	$data['lpg_steam_boilers_cost']     = isset($postdata['lpg_steam_boilers_cost']) ? $postdata['lpg_steam_boilers_cost'] : '';
	$data['lpg_kitchen_rate']           = isset($postdata['lpg_kitchen_rate']) ? $postdata['lpg_kitchen_rate'] : '';
	$data['lpg_kitchen_cost']           = isset($postdata['lpg_kitchen_cost']) ? $postdata['lpg_kitchen_cost'] : '';
	$data['lpg_total_budget']           = isset($postdata['lpg_total_budget']) ? $postdata['lpg_total_budget'] : '';
	$data['lpg_total_budget_cost']      = isset($postdata['lpg_total_budget_cost']) ? $postdata['lpg_total_budget_cost'] : '';
	$data['total_lpg']                  = isset($postdata['total_lpg']) ? $postdata['total_lpg'] : '';
	$data['total_lpg_rate']             = isset($postdata['total_lpg_rate']) ? $postdata['total_lpg_rate'] : '';

	$data['natural_gas_hot_water_boilers_rate'] = isset($postdata['natural_gas_hot_water_boilers_rate']) ? $postdata['natural_gas_hot_water_boilers_rate'] : '';
	$data['natural_gas_hot_water_boilers_cost'] = isset($postdata['natural_gas_hot_water_boilers_cost']) ? $postdata['natural_gas_hot_water_boilers_cost'] : '';
	$data['natural_gas_steam_boilers_rate']     = isset($postdata['natural_gas_steam_boilers_rate']) ? $postdata['natural_gas_steam_boilers_rate'] : '';
	$data['natural_gas_steam_boilers_cost']     = isset($postdata['natural_gas_steam_boilers_cost']) ? $postdata['natural_gas_steam_boilers_cost'] : '';
	$data['natural_gas_kitchen_rate']           = isset($postdata['natural_gas_kitchen_rate']) ? $postdata['natural_gas_kitchen_rate'] : '';
	$data['natural_gas_kitchen_cost']           = isset($postdata['natural_gas_kitchen_cost']) ? $postdata['natural_gas_kitchen_cost'] : '';
	$data['total_natural_gas']                  = isset($postdata['total_natural_gas']) ? $postdata['total_natural_gas'] : '';
	$data['total_natural_gas_rate']             = isset($postdata['total_natural_gas_rate']) ? $postdata['total_natural_gas_rate'] : '';
	$data['natural_gas_total_budget']           = isset($postdata['natural_gas_total_budget']) ? $postdata['natural_gas_total_budget'] : '';
	$data['natural_gas_total_budget_cost']      = isset($postdata['natural_gas_total_budget_cost']) ? $postdata['natural_gas_total_budget_cost'] : '';

	$data['district_heating_rate']                  = isset($postdata['district_heating_rate']) ? $postdata['district_heating_rate'] : '';
	$data['district_heating_cost']                  = isset($postdata['district_heating_cost']) ? $postdata['district_heating_cost'] : '';
	$data['district_cooling_rate']                  = isset($postdata['district_cooling_rate']) ? $postdata['district_cooling_rate'] : '';
	$data['district_cooling_cost']                  = isset($postdata['district_cooling_cost']) ? $postdata['district_cooling_cost'] : '';
	$data['district_heating_total_budget']          = isset($postdata['district_heating_total_budget']) ? $postdata['district_heating_total_budget'] : '';
	$data['district_heating_total_budget_cost']     = isset($postdata['district_heating_total_budget_cost']) ? $postdata['district_heating_total_budget_cost'] : '';
	$data['district_cooling_total_budget']          = isset($postdata['district_cooling_total_budget']) ? $postdata['district_cooling_total_budget'] : '';
	$data['district_cooling_total_budget_cost']     = isset($postdata['district_cooling_total_budget_cost']) ? $postdata['district_cooling_total_budget_cost'] : '';

	$data['district_heating_fixed_cost'] = isset($postdata['district_heating_fixed_cost']) ? $postdata['district_heating_fixed_cost'] : '';
	$data['district_cooling_fixed_cost'] = isset($postdata['district_cooling_fixed_cost']) ? $postdata['district_cooling_fixed_cost'] : '';
	$data['lpg_fixed_cost'] = isset($postdata['lpg_fixed_cost']) ? $postdata['lpg_fixed_cost'] : '';
	$data['natural_gas_fixed_cost'] = isset($postdata['natural_gas_fixed_cost']) ? $postdata['natural_gas_fixed_cost'] : '';
	$data['water_fixed_cost'] = isset($postdata['water_fixed_cost']) ? $postdata['water_fixed_cost'] : '';

	$data['maximum_demand_cost']           = isset($postdata['maximum_demand_cost']) ? $postdata['maximum_demand_cost'] : '';
	$data['electricity_total_budget']      = isset($postdata['electricity_total_budget']) ? $postdata['electricity_total_budget'] : '';
	$data['electricity_total_budget_cost'] = isset($postdata['electricity_total_budget_cost']) ? $postdata['electricity_total_budget_cost'] : '';


	$data['electricity_hotel']                  = isset($postdata['electricity_hotel']) ? $postdata['electricity_hotel'] : '';
	$data['fuel_oil_hotel']                     = isset($postdata['fuel_oil_hotel']) ? $postdata['fuel_oil_hotel'] : '';
	$data['natural_gas_hotel']                  = isset($postdata['natural_gas_hotel']) ? $postdata['natural_gas_hotel'] : '';
	$data['district_heating_hotel']             = isset($postdata['district_heating_hotel']) ? $postdata['district_heating_hotel'] : '';
	$data['district_cooling_hotel']             = isset($postdata['district_cooling_hotel']) ? $postdata['district_cooling_hotel'] : '';
	$data['water_hotel']                        = isset($postdata['water_hotel']) ? $postdata['water_hotel'] : '';
	$data['electricity_hotel_rate']             = isset($postdata['electricity_hotel_rate']) ? round($postdata['electricity_hotel_rate'], $decimal_places) : '';
	$data['fuel_oil_hotel_rate']                = isset($postdata['fuel_oil_hotel_rate']) ? round($postdata['fuel_oil_hotel_rate'], $decimal_places) : '';
	$data['natural_gas_hotel_rate']             = isset($postdata['natural_gas_hotel_rate']) ? round($postdata['natural_gas_hotel_rate'], $decimal_places) : '';
	$data['district_heating_hotel_rate']        = isset($postdata['district_heating_hotel_rate']) ? round($postdata['district_heating_hotel_rate'], $decimal_places) : '';
	$data['district_cooling_hotel_rate']        = isset($postdata['district_cooling_hotel_rate']) ? round($postdata['district_cooling_hotel_rate'], $decimal_places) : '';
	$data['water_hotel_rate']                   = isset($postdata['water_hotel_rate']) ? round($postdata['water_hotel_rate'], $decimal_places) : '';
	$data['electricity_hotel_cost']             = isset($postdata['electricity_hotel_cost']) ? $postdata['electricity_hotel_cost'] : '';
	$data['fuel_oil_hotel_cost']                = isset($postdata['fuel_oil_hotel_cost']) ? $postdata['fuel_oil_hotel_cost'] : '';
	$data['natural_gas_hotel_cost']             = isset($postdata['natural_gas_hotel_cost']) ? $postdata['natural_gas_hotel_cost'] : '';
	$data['district_heating_hotel_cost']        = isset($postdata['district_heating_hotel_cost']) ? $postdata['district_heating_hotel_cost'] : '';
	$data['district_cooling_hotel_cost']        = isset($postdata['district_cooling_hotel_cost']) ? $postdata['district_cooling_hotel_cost'] : '';
	$data['water_hotel_cost']                   = isset($postdata['water_hotel_cost']) ? $postdata['water_hotel_cost'] : '';

	/* ======================================ROUND To 4 DECIMAL PLACES========================================= */
	$data['average_pf']                         = round($data['average_pf'], $decimal_places);
	$data['average_cost_per_kwh']               = round($data['average_cost_per_kwh'], $decimal_places);
	$data['water_utility_supply_rate']          = round($data['water_utility_supply_rate'], $decimal_places);
	$data['water_irrigation_rate']              = round($data['water_irrigation_rate'], $decimal_places);
	$data['waste_water_rate']                   = round($data['waste_water_rate'], $decimal_places);
	$data['water_ro_rate']                      = round($data['water_ro_rate'], $decimal_places);
	$data['water_Cisterns_rate']                = round($data['water_Cisterns_rate'], $decimal_places);
	$data['water_total_consumption_rate']       = round($data['water_total_consumption_rate'], $decimal_places);
	$data['fuel_oil_hot_water_boilers_rate']    = round($data['fuel_oil_hot_water_boilers_rate'], $decimal_places);
	$data['fuel_oil_steam_boilers_rate']        = round($data['fuel_oil_steam_boilers_rate'], $decimal_places);
	$data['fuel_oil_others_rate']               = round($data['fuel_oil_others_rate'], $decimal_places);
	$data['total_fuel_oil_rate']                = round($data['total_fuel_oil_rate'], $decimal_places);
	$data['lpg_hot_water_boilers_rate']         = round($data['lpg_hot_water_boilers_rate'], $decimal_places);
	$data['lpg_steam_boilers_rate']             = round($data['lpg_steam_boilers_rate'], $decimal_places);
	$data['lpg_kitchen_rate']                   = round($data['lpg_kitchen_rate'], $decimal_places);
	$data['total_lpg_rate']                     = round($data['total_lpg_rate'], $decimal_places);
	$data['natural_gas_hot_water_boilers_rate'] = round($data['natural_gas_hot_water_boilers_rate'], $decimal_places);
	$data['natural_gas_steam_boilers_rate']     = round($data['natural_gas_steam_boilers_rate'], $decimal_places);
	$data['natural_gas_kitchen_rate']           = round($data['natural_gas_kitchen_rate'], $decimal_places);
	$data['total_natural_gas_rate']             = round($data['total_natural_gas_rate'], $decimal_places);
	$data['district_heating_rate']              = round($data['district_heating_rate'], $decimal_places);
	$data['district_cooling_rate']              = round($data['district_cooling_rate'], $decimal_places);
	/* ======================================ROUND To 4 DECIMAL PLACES========================================= */

	// Calculation
	$data['total_purchased_electricity']       = isset($postdata['total_purchased_electricity']) ? $postdata['total_purchased_electricity'] : '';
	$data['average_purchased_electricity']     = isset($postdata['average_purchased_electricity']) ? $postdata['average_purchased_electricity'] : '';
	$data['total_electricity_cost']            = isset($postdata['total_electricity_cost']) ? $postdata['total_electricity_cost'] : '';
	$data['total_lpg_cost']                    = isset($postdata['total_lpg_cost']) ? $postdata['total_lpg_cost'] : '';
	$data['total_fuel_oil_cost']               = isset($postdata['total_fuel_oil_cost']) ? $postdata['total_fuel_oil_cost'] : '';
	$data['total_natural_gas_cost']            = isset($postdata['total_natural_gas_cost']) ? $postdata['total_natural_gas_cost'] : '';
	$data['water_total_consumption']           = isset($postdata['water_total_consumption']) ? $postdata['water_total_consumption'] : '';
	$data['operation_recycled_waste']          = isset($postdata['operation_recycled_waste']) ? $postdata['operation_recycled_waste'] : '';
	$data['district_heating']                  = isset($postdata['district_heating']) ? $postdata['district_heating'] : '';
	$data['district_cooling']                  = isset($postdata['district_cooling']) ? $postdata['district_cooling'] : '';
	$data['total_consumption_breakdown']       = isset($postdata['total_consumption_breakdown']) ? $postdata['total_consumption_breakdown'] : '';
	$data['total_renewable_energy_production'] = isset($postdata['total_renewable_energy_production']) ? $postdata['total_renewable_energy_production'] : '';
	$data['total_onsite_generators_cost']      = isset($postdata['total_onsite_generators_cost']) ? $postdata['total_onsite_generators_cost'] : '';
	$data['total_renewable_energy_production_cost']      = isset($postdata['total_renewable_energy_production_cost']) ? $postdata['total_renewable_energy_production_cost'] : '';
	$data['total_renewable_energy_exported']      = isset($postdata['total_renewable_energy_exported']) ? $postdata['total_renewable_energy_exported'] : '';
	$data['total_renewable_energy_exported_cost']      = isset($postdata['total_renewable_energy_exported_cost']) ? $postdata['total_renewable_energy_exported_cost'] : '';

	/* ======================================REMOEVE FROM SERVERSIDE CALCULATION========================================= */
	/* $this->db->select('tariff,total_cost');
	$this->db->from($this->_tbl_electricity_tariff);
	$this->db->where('month_id', $postdata['month']);
	$this->db->where('year_id', $postdata['year']);
	$result = $this->db->get();

	var_dump($result->result_array());exit;
	$total_purchased_electricity = 0;
	$total_kWh = 0;
	foreach ($variable as $key => $value) {
	$total_purchased_electricity += $value['total_cost'];
	$total_kWh += $value['tariff'];
	}
	$average_purchased_electricity = ($total_purchased_electricity/$total_kWh);
	$data['total_purchased_electricity'] = $total_purchased_electricity;
	$data['average_purchased_electricity'] = $average_purchased_electricity;
	$data['total_electricity_cost'] = $postdata['total_electricity_cost'];
	$data['total_lpg_cost'] = ($data['lpg_hot_water_boilers']+$data['lpg_steam_boilers']+$data['lpg_kitchen']);
	$data['total_fuel_oil_cost'] = ($data['fuel_oil_hot_water_boilers']+$data['fuel_oil_steam_boilers']+$data['fuel_oil_others']);
	$data['total_natural_gas_cost'] = ($data['natural_gas_hot_water_boilers']+$data['natural_gas_steam_boilers']+$data['natural_gas_kitchen']);
	$data['water_total_consumption'] = ($data['water_utility_supply']+$data['waste_water']+$data['water_Cisterns']+$data['water_ro']);
	$data['District_heating'] = $postdata['District_heating'];
	$data['District_cooling'] = $postdata['District_cooling'];
	$data['total_consumption_breakdown'] = $postdata['total_consumption_breakdown'];
	$data['total_renewable_energy_production'] = $postdata['total_renewable_energy_production'];
	$data['total_onsite_generators_cost'] = $postdata['total_onsite_generators_cost']; */
	/* ======================================REMOEVE FROM SERVERSIDE CALCULATION========================================= */

	$data['modify_by'] = $postdata['user_id'];

	// Residences CR updates related code start
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($data['site_id']);
	$residence_types = isset($site_detail['residence_types']) ? explode(',', $site_detail['residence_types']) : [];
	if(isset($residence_types) && in_array(RENTAL_PROGRAM_RESIDENCE, $residence_types)) {
	    $data['rental_program_residence_electricity'] = isset($postdata['rental_program_residence_electricity']) ? $postdata['rental_program_residence_electricity'] : NULL;
	    $data['rental_program_residence_electricity_rate'] = isset($postdata['rental_program_residence_electricity_rate']) ? $postdata['rental_program_residence_electricity_rate'] : NULL;
	    $data['rental_program_residence_electricity_cost'] = isset($postdata['rental_program_residence_electricity_cost']) ? $postdata['rental_program_residence_electricity_cost'] : NULL;
	    $data['rental_program_residence_fuel_oil'] = isset($postdata['rental_program_residence_fuel_oil']) ? $postdata['rental_program_residence_fuel_oil'] : NULL;
	    $data['rental_program_residence_fuel_oil_rate'] = isset($postdata['rental_program_residence_fuel_oil_rate']) ? $postdata['rental_program_residence_fuel_oil_rate'] : NULL;
	    $data['rental_program_residence_fuel_oil_cost'] = isset($postdata['rental_program_residence_fuel_oil_cost']) ? $postdata['rental_program_residence_fuel_oil_cost'] : NULL;
	    $data['rental_program_residence_lpg'] = isset($postdata['rental_program_residence_lpg']) ? $postdata['rental_program_residence_lpg'] : NULL;
	    $data['rental_program_residence_lpg_rate'] = isset($postdata['rental_program_residence_lpg_rate']) ? $postdata['rental_program_residence_lpg_rate'] : NULL;
	    $data['rental_program_residence_lpg_cost'] = isset($postdata['rental_program_residence_lpg_cost']) ? $postdata['rental_program_residence_lpg_cost'] : NULL;
	    $data['rental_program_residence_natural_gas'] = isset($postdata['rental_program_residence_natural_gas']) ? $postdata['rental_program_residence_natural_gas'] : NULL;
	    $data['rental_program_residence_natural_gas_rate'] = isset($postdata['rental_program_residence_natural_gas_rate']) ? $postdata['rental_program_residence_natural_gas_rate'] : NULL;
	    $data['rental_program_residence_natural_gas_cost'] = isset($postdata['rental_program_residence_natural_gas_cost']) ? $postdata['rental_program_residence_natural_gas_cost'] : NULL;
	    $data['rental_program_residence_district_heating'] = isset($postdata['rental_program_residence_district_heating']) ? $postdata['rental_program_residence_district_heating'] : NULL;
	    $data['rental_program_residence_district_heating_rate'] = isset($postdata['rental_program_residence_district_heating_rate']) ? $postdata['rental_program_residence_district_heating_rate'] : NULL;
	    $data['rental_program_residence_district_heating_cost'] = isset($postdata['rental_program_residence_district_heating_cost']) ? $postdata['rental_program_residence_district_heating_cost'] : NULL;
	    $data['rental_program_residence_district_cooling'] = isset($postdata['rental_program_residence_district_cooling']) ? $postdata['rental_program_residence_district_cooling'] : NULL;
	    $data['rental_program_residence_district_cooling_rate'] = isset($postdata['rental_program_residence_district_cooling_rate']) ? $postdata['rental_program_residence_district_cooling_rate'] : NULL;
	    $data['rental_program_residence_district_cooling_cost'] = isset($postdata['rental_program_residence_district_cooling_cost']) ? $postdata['rental_program_residence_district_cooling_cost'] : NULL;
	    $data['rental_program_residence_water'] = isset($postdata['rental_program_residence_water']) ? $postdata['rental_program_residence_water'] : NULL;
	    $data['rental_program_residence_water_rate'] = isset($postdata['rental_program_residence_water_rate']) ? $postdata['rental_program_residence_water_rate'] : NULL;
	    $data['rental_program_residence_water_cost'] = isset($postdata['rental_program_residence_water_cost']) ? $postdata['rental_program_residence_water_cost'] : NULL;
	}
	if(isset($residence_types) && in_array(PRIVATE_RESIDENCE, $residence_types)) {
	    $data['private_program_electricity'] = isset($postdata['private_program_electricity']) ? $postdata['private_program_electricity'] : NULL;
	    $data['private_program_electricity_rate'] = isset($postdata['private_program_electricity_rate']) ? $postdata['private_program_electricity_rate'] : NULL;
	    $data['private_program_electricity_cost'] = isset($postdata['private_program_electricity_cost']) ? $postdata['private_program_electricity_cost'] : NULL;
	    $data['private_program_fuel_oil'] = isset($postdata['private_program_fuel_oil']) ? $postdata['private_program_fuel_oil'] : NULL;
	    $data['private_program_fuel_oil_rate'] = isset($postdata['private_program_fuel_oil_rate']) ? $postdata['private_program_fuel_oil_rate'] : NULL;
	    $data['private_program_fuel_oil_cost'] = isset($postdata['private_program_fuel_oil_cost']) ? $postdata['private_program_fuel_oil_cost'] : NULL;
	    $data['private_program_lpg'] = isset($postdata['private_program_lpg']) ? $postdata['private_program_lpg'] : NULL;
	    $data['private_program_lpg_rate'] = isset($postdata['private_program_lpg_rate']) ? $postdata['private_program_lpg_rate'] : NULL;
	    $data['private_program_lpg_cost'] = isset($postdata['private_program_lpg_cost']) ? $postdata['private_program_lpg_cost'] : NULL;
	    $data['private_program_natural_gas'] = isset($postdata['private_program_natural_gas']) ? $postdata['private_program_natural_gas'] : NULL;
	    $data['private_program_natural_gas_rate'] = isset($postdata['private_program_natural_gas_rate']) ? $postdata['private_program_natural_gas_rate'] : NULL;
	    $data['private_program_natural_gas_cost'] = isset($postdata['private_program_natural_gas_cost']) ? $postdata['private_program_natural_gas_cost'] : NULL;
	    $data['private_program_district_heating'] = isset($postdata['private_program_district_heating']) ? $postdata['private_program_district_heating'] : NULL;
	    $data['private_program_district_heating_rate'] = isset($postdata['private_program_district_heating_rate']) ? $postdata['private_program_district_heating_rate'] : NULL;
	    $data['private_program_district_heating_cost'] = isset($postdata['private_program_district_heating_cost']) ? $postdata['private_program_district_heating_cost'] : NULL;
	    $data['private_program_district_cooling'] = isset($postdata['private_program_district_cooling']) ? $postdata['private_program_district_cooling'] : NULL;
	    $data['private_program_district_cooling_rate'] = isset($postdata['private_program_district_cooling_rate']) ? $postdata['private_program_district_cooling_rate'] : NULL;
	    $data['private_program_district_cooling_cost'] = isset($postdata['private_program_district_cooling_cost']) ? $postdata['private_program_district_cooling_cost'] : NULL;
	    $data['private_program_water'] = isset($postdata['private_program_water']) ? $postdata['private_program_water'] : NULL;
	    $data['private_program_water_rate'] = isset($postdata['private_program_water_rate']) ? $postdata['private_program_water_rate'] : NULL;
	    $data['private_program_water_cost'] = isset($postdata['private_program_water_cost']) ? $postdata['private_program_water_cost'] : NULL;
	}
	if(isset($residence_types) && in_array(EMPLOYEE_LIVING_QUARTERS, $residence_types)) {
	    $data['employee_living_quarter_electricity'] = isset($postdata['employee_living_quarter_electricity']) ? $postdata['employee_living_quarter_electricity'] : NULL;
	    $data['employee_living_quarter_fuel_oil'] = isset($postdata['employee_living_quarter_fuel_oil']) ? $postdata['employee_living_quarter_fuel_oil'] : NULL;
	    $data['employee_living_quarter_lpg'] = isset($postdata['employee_living_quarter_lpg']) ? $postdata['employee_living_quarter_lpg'] : NULL;
	    $data['employee_living_quarter_natural_gas'] = isset($postdata['employee_living_quarter_natural_gas']) ? $postdata['employee_living_quarter_natural_gas'] : NULL;
	    $data['employee_living_quarter_district_heating'] = isset($postdata['employee_living_quarter_district_heating']) ? $postdata['employee_living_quarter_district_heating'] : NULL;
	    $data['employee_living_quarter_district_cooling'] = isset($postdata['employee_living_quarter_district_cooling']) ? $postdata['employee_living_quarter_district_cooling'] : NULL;
	    $data['employee_living_quarter_water'] = isset($postdata['employee_living_quarter_water']) ? $postdata['employee_living_quarter_water'] : NULL;
	}
	if(isset($residence_types) && in_array(EMPLOYEE_LIVING_QUARTERS_OFFSITE, $residence_types)) {
	    $data['employee_living_quarter_offsite_electricity'] = isset($postdata['employee_living_quarter_offsite_electricity']) ? $postdata['employee_living_quarter_offsite_electricity'] : NULL;
	    $data['employee_living_quarter_offsite_fuel_oil'] = isset($postdata['employee_living_quarter_offsite_fuel_oil']) ? $postdata['employee_living_quarter_offsite_fuel_oil'] : NULL;
	    $data['employee_living_quarter_offsite_lpg'] = isset($postdata['employee_living_quarter_offsite_lpg']) ? $postdata['employee_living_quarter_offsite_lpg'] : NULL;
	    $data['employee_living_quarter_offsite_natural_gas'] = isset($postdata['employee_living_quarter_offsite_natural_gas']) ? $postdata['employee_living_quarter_offsite_natural_gas'] : NULL;
	    $data['employee_living_quarter_offsite_district_heating'] = isset($postdata['employee_living_quarter_offsite_district_heating']) ? $postdata['employee_living_quarter_offsite_district_heating'] : NULL;
	    $data['employee_living_quarter_offsite_district_cooling'] = isset($postdata['employee_living_quarter_offsite_district_cooling']) ? $postdata['employee_living_quarter_offsite_district_cooling'] : NULL;
	    $data['employee_living_quarter_offsite_water'] = isset($postdata['employee_living_quarter_offsite_water']) ? $postdata['employee_living_quarter_offsite_water'] : NULL;
	}
	// Residences CR updates related code end



	foreach ($postdata['files'] as $key => $value) {
	    $fileCount = count($postdata['files'][$key]["name"]);
	    $uploadFileName = [];
	    for ($i = 0; $i < $fileCount; $i++) {
		$uploads  = $value['name'][$i];
		$type     = $value['type'][$i];
		$tmp_name = $value['tmp_name'][$i];
		$size     = $value['size'][$i];

		if(($uploads != '') && ($type != '') && ($tmp_name != ''))
		{
		    if (!file_exists(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/utility_invoices/")) {
			mkdir(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/utility_invoices/", 0777, true);
		    }

		    $config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
		    $config['max_size']       = '2048';
		    $config['maintain_ratio'] = true;
		    $config['width']          = 140;
		    $config['height']         = 100;

		    $this->load->library('upload', $config);
		    $this->upload->initialize($config);

		    $valid_formats = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma", "pdf");

		    $imagename     = $uploads;
		    $cnt    = strrpos($imagename, ".");
		    if (!$cnt) {
			$ext = '';
		    }
		    $l                = strlen($imagename) - $cnt;
		    $ext              = strtolower(substr($imagename, $cnt + 1, $l));
		    $upload_file_name = $i.'_'.$key . rand(11111, 9999999) . '.' . $ext;

		    if ($ext) {
			if (in_array($ext, $valid_formats)) {

			    $uploadedfile = $tmp_name;

			    $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/utility_invoices/".$upload_file_name;

			    $_movestatus  = move_uploaded_file($uploadedfile, $target_file);

			    if (!$_movestatus) {
				// $this->theme->set_message('waste image is not uploaded', 'error');
			    } else {
				$uploadFileName[] = $upload_file_name;
				// $data[$key]    = $upload_file_name;
			    }

			}
		    }
		}
	    }
	    $data[$key]    = implode(",",$uploadFileName);
		}
	if (!empty($utilityCheck)) {
			if ((empty($data['electricity_invoice_scan']) && !empty($utilityCheck['electricity_invoice_scan']))) {
				$data['electricity_invoice_scan'] = $utilityCheck['electricity_invoice_scan'];
			}
			if ((empty($data['fuel_oil_invoice_scan']) && !empty($utilityCheck['fuel_oil_invoice_scan']))) {
				$data['fuel_oil_invoice_scan'] = $utilityCheck['fuel_oil_invoice_scan'];
			}
			if ((empty($data['lpg_invoice_scan']) && !empty($utilityCheck['lpg_invoice_scan']))) {
				$data['lpg_invoice_scan'] = $utilityCheck['lpg_invoice_scan'];
			}
			if ((empty($data['natural_gas_invoice_scan']) && !empty($utilityCheck['natural_gas_invoice_scan']))) {
				$data['natural_gas_invoice_scan'] = $utilityCheck['natural_gas_invoice_scan'];
			}
			if ((empty($data['district_heating_invoice_scan']) && !empty($utilityCheck['district_heating_invoice_scan']))) {
				$data['district_heating_invoice_scan'] = $utilityCheck['district_heating_invoice_scan'];
			}
			if ((empty($data['district_cooling_invoice_scan']) && !empty($utilityCheck['district_cooling_invoice_scan']))) {
				$data['district_cooling_invoice_scan'] = $utilityCheck['district_cooling_invoice_scan'];
			}
			if ((empty($data['water_invoice_scan']) && !empty($utilityCheck['water_invoice_scan']))) {
				$data['water_invoice_scan'] = $utilityCheck['water_invoice_scan'];
			}

	    $data['id'] = $postdata['id'];
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->where('id', $postdata['id']);
	    $this->db->where('month_id', $postdata['month']);
	    $this->db->where('year_id', $postdata['year']);
	    $this->db->update($this->_tbl_utilities, $data);
	    $id = $data['id'];
	} else {

	    $data['created_by'] = $postdata['user_id'];
	    $this->db->set('created_on', 'NOW()', false);
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->insert($this->_tbl_utilities, $data);
	    $id = $this->db->insert_id();
	}
	return $id;
    }

    public function saveUtilityWithoutForex($postdata = array())
    {
	$data           = array();
	$decimal_places = 4;
	$utility        = $this->getUtility();

	// Auto set
	$data['site_id']    = $postdata['site_id'];
	$data['project_id'] = 0; //$postdata['project_id']; (NO need project id in this table)
	$data['year_id']    = $postdata['year'];
	$data['month_id']   = $postdata['month'];

	// User inputs
	$data['maximum_demand']                             = isset($postdata['maximum_demand']) ? $postdata['maximum_demand'] : '';
	$data['natural_gas_hot_water_boilers']              = isset($postdata['natural_gas_hot_water_boilers']) ? $postdata['natural_gas_hot_water_boilers'] : '';
	$data['natural_gas_steam_boilers']                  = isset($postdata['natural_gas_steam_boilers']) ? $postdata['natural_gas_steam_boilers'] : '';
	$data['natural_gas_kitchen']                        = isset($postdata['natural_gas_kitchen']) ? $postdata['natural_gas_kitchen'] : '';
	$data['lpg_hot_water_boilers']                      = isset($postdata['lpg_hot_water_boilers']) ? $postdata['lpg_hot_water_boilers'] : '';
	$data['lpg_steam_boilers']                          = isset($postdata['lpg_steam_boilers']) ? $postdata['lpg_steam_boilers'] : '';
	$data['lpg_kitchen']                                = isset($postdata['lpg_kitchen']) ? $postdata['lpg_kitchen'] : '';
	$data['fuel_oil_hot_water_boilers']                 = isset($postdata['fuel_oil_hot_water_boilers']) ? $postdata['fuel_oil_hot_water_boilers'] : '';
	$data['fuel_oil_steam_boilers']                     = isset($postdata['fuel_oil_steam_boilers']) ? $postdata['fuel_oil_steam_boilers'] : '';
	$data['fuel_oil_others']                            = isset($postdata['fuel_oil_others']) ? $postdata['fuel_oil_others'] : '';
	$data['water_utility_supply']                       = isset($postdata['water_utility_supply']) ? $postdata['water_utility_supply'] : '';
	$data['operation_general_waste']                    = isset($postdata['operation_general_waste']) ? $postdata['operation_general_waste'] : '';
	$data['operation_paper_waste']                      = isset($postdata['operation_paper_waste']) ? $postdata['operation_paper_waste'] : '';
	$data['operation_food_waste']                       = isset($postdata['operation_food_waste']) ? $postdata['operation_food_waste'] : '';
	$data['operation_cardboard_waste']                  = isset($postdata['operation_cardboard_waste']) ? $postdata['operation_cardboard_waste'] : '';
	$data['operation_plastic_waste']                    = isset($postdata['operation_plastic_waste']) ? $postdata['operation_plastic_waste'] : '';
	$data['operation_glass_waste']                      = isset($postdata['operation_glass_waste']) ? $postdata['operation_glass_waste'] : '';
	$data['waste_water']                                = isset($postdata['waste_water']) ? $postdata['waste_water'] : '';
	$data['water_ro']                                   = isset($postdata['water_ro']) ? $postdata['water_ro'] : '';
	$data['water_Cisterns']                             = isset($postdata['water_Cisterns']) ? $postdata['water_Cisterns'] : '';
	/*$data['water_consumption_breakdown_cooling_towers'] = isset($postdata['water_consumption_breakdown_cooling_towers']) ? $postdata['water_consumption_breakdown_cooling_towers'] : '';
	$data['water_consumption_breakdown_boh']            = isset($postdata['water_consumption_breakdown_boh']) ? $postdata['water_consumption_breakdown_boh'] : '';
	$data['water_consumption_breakdown_rooms']          = isset($postdata['water_consumption_breakdown_rooms']) ? $postdata['water_consumption_breakdown_rooms'] : '';*/
	$data['total_room_night']                           = isset($postdata['total_room_night']) ? $postdata['total_room_night'] : '';
	$data['total_guests']                               = isset($postdata['total_guests']) ? $postdata['total_guests'] : '';
	$data['total_laundered']                            = isset($postdata['total_laundered']) ? $postdata['total_laundered'] : '';
	$data['total_fb_services']                          = isset($postdata['total_fb_services']) ? $postdata['total_fb_services'] : '';
        $data['total_room_night_budget']                          = isset($postdata['total_room_night_budget']) ? $postdata['total_room_night_budget'] : '';
        $data['total_guests_budget']                               = isset($postdata['total_guests_budget']) ? $postdata['total_guests_budget'] : '';
	$data['cdd']                                        = isset($postdata['cdd']) ? $postdata['cdd'] : '';
	$data['hdd']                                        = isset($postdata['hdd']) ? $postdata['hdd'] : '';
	$data['revenue']                                    = isset($postdata['revenue']) ? $postdata['revenue'] : '';
	$data['forex']                                      = isset($postdata['forex']) ? $postdata['forex'] : 1;

	// New Fields
	$data['fixed_fees']                       = isset($postdata['fixed_fees']) ? $postdata['fixed_fees'] : '';
	$data['maximum_demand_unit']              = isset($postdata['maximum_demand_unit']) ? $postdata['maximum_demand_unit'] : '';
	$data['maximum_demand_price']             = isset($postdata['maximum_demand_price']) ? $postdata['maximum_demand_price'] : '';
	$data['total_maximum_demand']             = isset($postdata['total_maximum_demand']) ? $postdata['total_maximum_demand'] : '';
	$data['onsite_generators_quantity']       = isset($postdata['onsite_generators_quantity']) ? $postdata['onsite_generators_quantity'] : '';
	$data['onsite_generators_price']          = isset($postdata['onsite_generators_price']) ? $postdata['onsite_generators_price'] : '';
	$data['onsite_generators_fuel_oil_quantity']       = isset($postdata['onsite_generators_fuel_oil_quantity']) ? $postdata['onsite_generators_fuel_oil_quantity'] : '';
	$data['onsite_generators_fuel_oil_price']          = isset($postdata['onsite_generators_fuel_oil_price']) ? $postdata['onsite_generators_fuel_oil_price'] : '';
	$data['onsite_generators_natural_gas_quantity']       = isset($postdata['onsite_generators_natural_gas_quantity']) ? $postdata['onsite_generators_natural_gas_quantity'] : '';
	$data['onsite_generators_natural_gas_price']          = isset($postdata['onsite_generators_natural_gas_price']) ? $postdata['onsite_generators_natural_gas_price'] : '';
	$data['total_electricity_kwh']            = isset($postdata['total_electricity_kwh']) ? $postdata['total_electricity_kwh'] : '';
	$data['total_purchased_electricity_cost'] = isset($postdata['total_purchased_electricity_cost']) ? $postdata['total_purchased_electricity_cost'] : '';

	$data['average_pf']                                  = isset($postdata['average_pf']) ? $postdata['average_pf'] : '';
	$data['average_cost_per_kwh']                        = isset($postdata['average_cost_per_kwh']) ? $postdata['average_cost_per_kwh'] : '';
	$data['water_utility_supply_rate']                   = isset($postdata['water_utility_supply_rate']) ? $postdata['water_utility_supply_rate'] : '';
	$data['water_utility_supply_cost']                   = isset($postdata['water_utility_supply_cost']) ? $postdata['water_utility_supply_cost'] : '';
	$data['waste_water_rate']                            = isset($postdata['waste_water_rate']) ? $postdata['waste_water_rate'] : '';
	$data['waste_water_cost']                            = isset($postdata['waste_water_cost']) ? $postdata['waste_water_cost'] : '';
	$data['water_ro_rate']                               = isset($postdata['water_ro_rate']) ? $postdata['water_ro_rate'] : '';
	$data['water_ro_cost']                               = isset($postdata['water_ro_cost']) ? $postdata['water_ro_cost'] : '';
	$data['water_Cisterns_rate']                         = isset($postdata['water_Cisterns_rate']) ? $postdata['water_Cisterns_rate'] : '';
	$data['water_Cisterns_cost']                         = isset($postdata['water_Cisterns_cost']) ? $postdata['water_Cisterns_cost'] : '';
	$data['water_total_consumption_rate']                = isset($postdata['water_total_consumption_rate']) ? $postdata['water_total_consumption_rate'] : '';
	$data['water_total_consumption_cost']                = isset($postdata['water_total_consumption_cost']) ? $postdata['water_total_consumption_cost'] : '';
	$data['water_total_consumption_budget']              = isset($postdata['water_total_consumption_budget']) ? $postdata['water_total_consumption_budget'] : '';
	$data['water_total_consumption_budget_cost']         = isset($postdata['water_total_consumption_budget_cost']) ? $postdata['water_total_consumption_budget_cost'] : '';
	$data['total_consumption_breakdown_budget']          = isset($postdata['total_consumption_breakdown_budget']) ? $postdata['total_consumption_breakdown_budget'] : '';
	$data['total_consumption_breakdown_budget_cost']     = isset($postdata['total_consumption_breakdown_budget_cost']) ? $postdata['total_consumption_breakdown_budget_cost'] : '';

	$data['fuel_oil_hot_water_boilers_rate'] = isset($postdata['fuel_oil_hot_water_boilers_rate']) ? $postdata['fuel_oil_hot_water_boilers_rate'] : '';
	$data['fuel_oil_hot_water_boilers_cost'] = isset($postdata['fuel_oil_hot_water_boilers_cost']) ? $postdata['fuel_oil_hot_water_boilers_cost'] : '';
	$data['fuel_oil_steam_boilers_rate']     = isset($postdata['fuel_oil_steam_boilers_rate']) ? $postdata['fuel_oil_steam_boilers_rate'] : '';
	$data['fuel_oil_steam_boilers_cost']     = isset($postdata['fuel_oil_steam_boilers_cost']) ? $postdata['fuel_oil_steam_boilers_cost'] : '';
	$data['fuel_oil_others_rate']            = isset($postdata['fuel_oil_others_rate']) ? $postdata['fuel_oil_others_rate'] : '';
	$data['fuel_oil_others_cost']            = isset($postdata['fuel_oil_others_cost']) ? $postdata['fuel_oil_others_cost'] : '';
	$data['fuel_total_budget']               = isset($postdata['fuel_total_budget']) ? $postdata['fuel_total_budget'] : '';
	$data['fuel_total_budget_cost']          = isset($postdata['fuel_total_budget_cost']) ? $postdata['fuel_total_budget_cost'] : '';
	$data['total_fuel_oil']                  = isset($postdata['total_fuel_oil']) ? $postdata['total_fuel_oil'] : '';
	$data['total_fuel_oil_rate']             = isset($postdata['total_fuel_oil_rate']) ? $postdata['total_fuel_oil_rate'] : '';

	$data['lpg_hot_water_boilers_rate'] = isset($postdata['lpg_hot_water_boilers_rate']) ? $postdata['lpg_hot_water_boilers_rate'] : '';
	$data['lpg_hot_water_boilers_cost'] = isset($postdata['lpg_hot_water_boilers_cost']) ? $postdata['lpg_hot_water_boilers_cost'] : '';
	$data['lpg_steam_boilers_rate']     = isset($postdata['lpg_steam_boilers_rate']) ? $postdata['lpg_steam_boilers_rate'] : '';
	$data['lpg_steam_boilers_cost']     = isset($postdata['lpg_steam_boilers_cost']) ? $postdata['lpg_steam_boilers_cost'] : '';
	$data['lpg_kitchen_rate']           = isset($postdata['lpg_kitchen_rate']) ? $postdata['lpg_kitchen_rate'] : '';
	$data['lpg_kitchen_cost']           = isset($postdata['lpg_kitchen_cost']) ? $postdata['lpg_kitchen_cost'] : '';
	$data['lpg_total_budget']           = isset($postdata['lpg_total_budget']) ? $postdata['lpg_total_budget'] : '';
	$data['lpg_total_budget_cost']      = isset($postdata['lpg_total_budget_cost']) ? $postdata['lpg_total_budget_cost'] : '';
	$data['total_lpg']                  = isset($postdata['total_lpg']) ? $postdata['total_lpg'] : '';
	$data['total_lpg_rate']             = isset($postdata['total_lpg_rate']) ? $postdata['total_lpg_rate'] : '';

	$data['natural_gas_hot_water_boilers_rate'] = isset($postdata['natural_gas_hot_water_boilers_rate']) ? $postdata['natural_gas_hot_water_boilers_rate'] : '';
	$data['natural_gas_hot_water_boilers_cost'] = isset($postdata['natural_gas_hot_water_boilers_cost']) ? $postdata['natural_gas_hot_water_boilers_cost'] : '';
	$data['natural_gas_steam_boilers_rate']     = isset($postdata['natural_gas_steam_boilers_rate']) ? $postdata['natural_gas_steam_boilers_rate'] : '';
	$data['natural_gas_steam_boilers_cost']     = isset($postdata['natural_gas_steam_boilers_cost']) ? $postdata['natural_gas_steam_boilers_cost'] : '';
	$data['natural_gas_kitchen_rate']           = isset($postdata['natural_gas_kitchen_rate']) ? $postdata['natural_gas_kitchen_rate'] : '';
	$data['natural_gas_kitchen_cost']           = isset($postdata['natural_gas_kitchen_cost']) ? $postdata['natural_gas_kitchen_cost'] : '';
	$data['total_natural_gas']                  = isset($postdata['total_natural_gas']) ? $postdata['total_natural_gas'] : '';
	$data['total_natural_gas_rate']             = isset($postdata['total_natural_gas_rate']) ? $postdata['total_natural_gas_rate'] : '';
	$data['natural_gas_total_budget']           = isset($postdata['natural_gas_total_budget']) ? $postdata['natural_gas_total_budget'] : '';
	$data['natural_gas_total_budget_cost']      = isset($postdata['natural_gas_total_budget_cost']) ? $postdata['natural_gas_total_budget_cost'] : '';

	$data['district_heating_rate']                  = isset($postdata['district_heating_rate']) ? $postdata['district_heating_rate'] : '';
	$data['district_heating_cost']                  = isset($postdata['district_heating_cost']) ? $postdata['district_heating_cost'] : '';
	$data['district_cooling_rate']                  = isset($postdata['district_cooling_rate']) ? $postdata['district_cooling_rate'] : '';
	$data['district_cooling_cost']                  = isset($postdata['district_cooling_cost']) ? $postdata['district_cooling_cost'] : '';
	$data['district_heating_total_budget']          = isset($postdata['district_heating_total_budget']) ? $postdata['district_heating_total_budget'] : '';
	$data['district_heating_total_budget_cost']     = isset($postdata['district_heating_total_budget_cost']) ? $postdata['district_heating_total_budget_cost'] : '';
	$data['district_cooling_total_budget']          = isset($postdata['district_cooling_total_budget']) ? $postdata['district_cooling_total_budget'] : '';
	$data['district_cooling_total_budget_cost']     = isset($postdata['district_cooling_total_budget_cost']) ? $postdata['district_cooling_total_budget_cost'] : '';

	$data['district_heating_fixed_cost'] = isset($postdata['district_heating_fixed_cost']) ? $postdata['district_heating_fixed_cost'] : '';
	$data['district_cooling_fixed_cost'] = isset($postdata['district_cooling_fixed_cost']) ? $postdata['district_cooling_fixed_cost'] : '';
	$data['lpg_fixed_cost'] = isset($postdata['lpg_fixed_cost']) ? $postdata['lpg_fixed_cost'] : '';
	$data['natural_gas_fixed_cost'] = isset($postdata['natural_gas_fixed_cost']) ? $postdata['natural_gas_fixed_cost'] : '';
	$data['water_fixed_cost'] = isset($postdata['water_fixed_cost']) ? $postdata['water_fixed_cost'] : '';

	$data['maximum_demand_cost']           = isset($postdata['maximum_demand_cost']) ? $postdata['maximum_demand_cost'] : '';
	$data['electricity_total_budget']      = isset($postdata['electricity_total_budget']) ? $postdata['electricity_total_budget'] : '';
	$data['electricity_total_budget_cost'] = isset($postdata['electricity_total_budget_cost']) ? $postdata['electricity_total_budget_cost'] : '';

	/* ======================================ROUND To 4 DECIMAL PLACES========================================= */
	$data['average_pf']                         = round($data['average_pf'], $decimal_places);
	$data['average_cost_per_kwh']               = round($data['average_cost_per_kwh'], $decimal_places);
	$data['water_utility_supply_rate']          = round($data['water_utility_supply_rate'], $decimal_places);
	$data['waste_water_rate']                   = round($data['waste_water_rate'], $decimal_places);
	$data['water_ro_rate']                      = round($data['water_ro_rate'], $decimal_places);
	$data['water_Cisterns_rate']                = round($data['water_Cisterns_rate'], $decimal_places);
	$data['water_total_consumption_rate']       = round($data['water_total_consumption_rate'], $decimal_places);
	$data['fuel_oil_hot_water_boilers_rate']    = round($data['fuel_oil_hot_water_boilers_rate'], $decimal_places);
	$data['fuel_oil_steam_boilers_rate']        = round($data['fuel_oil_steam_boilers_rate'], $decimal_places);
	$data['fuel_oil_others_rate']               = round($data['fuel_oil_others_rate'], $decimal_places);
	$data['total_fuel_oil_rate']                = round($data['total_fuel_oil_rate'], $decimal_places);
	$data['lpg_hot_water_boilers_rate']         = round($data['lpg_hot_water_boilers_rate'], $decimal_places);
	$data['lpg_steam_boilers_rate']             = round($data['lpg_steam_boilers_rate'], $decimal_places);
	$data['lpg_kitchen_rate']                   = round($data['lpg_kitchen_rate'], $decimal_places);
	$data['total_lpg_rate']                     = round($data['total_lpg_rate'], $decimal_places);
	$data['natural_gas_hot_water_boilers_rate'] = round($data['natural_gas_hot_water_boilers_rate'], $decimal_places);
	$data['natural_gas_steam_boilers_rate']     = round($data['natural_gas_steam_boilers_rate'], $decimal_places);
	$data['natural_gas_kitchen_rate']           = round($data['natural_gas_kitchen_rate'], $decimal_places);
	$data['total_natural_gas_rate']             = round($data['total_natural_gas_rate'], $decimal_places);
	$data['district_heating_rate']              = round($data['district_heating_rate'], $decimal_places);
	$data['district_cooling_rate']              = round($data['district_cooling_rate'], $decimal_places);
	/* ======================================ROUND To 4 DECIMAL PLACES========================================= */

	// Calculation
	$data['total_purchased_electricity']       = isset($postdata['total_purchased_electricity']) ? $postdata['total_purchased_electricity'] : '';
	$data['average_purchased_electricity']     = isset($postdata['average_purchased_electricity']) ? $postdata['average_purchased_electricity'] : '';
	$data['total_electricity_cost']            = isset($postdata['total_electricity_cost']) ? $postdata['total_electricity_cost'] : '';
	$data['total_lpg_cost']                    = isset($postdata['total_lpg_cost']) ? $postdata['total_lpg_cost'] : '';
	$data['total_fuel_oil_cost']               = isset($postdata['total_fuel_oil_cost']) ? $postdata['total_fuel_oil_cost'] : '';
	$data['total_natural_gas_cost']            = isset($postdata['total_natural_gas_cost']) ? $postdata['total_natural_gas_cost'] : '';
	$data['water_total_consumption']           = isset($postdata['water_total_consumption']) ? $postdata['water_total_consumption'] : '';
	$data['operation_recycled_waste']          = isset($postdata['operation_recycled_waste']) ? $postdata['operation_recycled_waste'] : '';
	$data['district_heating']                  = isset($postdata['district_heating']) ? $postdata['district_heating'] : '';
	$data['district_cooling']                  = isset($postdata['district_cooling']) ? $postdata['district_cooling'] : '';
	$data['total_consumption_breakdown']       = isset($postdata['total_consumption_breakdown']) ? $postdata['total_consumption_breakdown'] : '';
	$data['total_renewable_energy_production'] = isset($postdata['total_renewable_energy_production']) ? $postdata['total_renewable_energy_production'] : '';
	$data['total_onsite_generators_cost']      = isset($postdata['total_onsite_generators_cost']) ? $postdata['total_onsite_generators_cost'] : '';
	$data['total_renewable_energy_production_cost']      = isset($postdata['total_renewable_energy_production_cost']) ? $postdata['total_renewable_energy_production_cost'] : '';
	$data['total_renewable_energy_exported']      = isset($postdata['total_renewable_energy_exported']) ? $postdata['total_renewable_energy_exported'] : '';
	$data['total_renewable_energy_exported_cost']      = isset($postdata['total_renewable_energy_exported_cost']) ? $postdata['total_renewable_energy_exported_cost'] : '';
	$data['modify_by']                         = $postdata['user_id'];

	//saving originaldata elimi nating local currency factor
	foreach ($this->field_forex as $field) {
	    if (!empty($data[$field])) {
		$data[$field] = round($data[$field] / $data['forex'], 4);
	    }
	}

	if (!empty($utility)) {

	    $data['id'] = $postdata['id'];
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->where('id', $postdata['id']);
	    $this->db->where('month_id', $postdata['month']);
	    $this->db->where('year_id', $postdata['year']);
	    $this->db->update($this->_tbl_utilities, $data);
	    $id = $data['id'];
	} else {

	    $data['created_by'] = $postdata['user_id'];
	    $this->db->set('created_on', 'NOW()', false);
	    $this->db->set('modify_on', 'NOW()', false);
	    $this->db->insert($this->_tbl_utilities, $data);
	    $id = $this->db->insert_id();
	}
	return $id;
    }

    public function saveElectricityTariff($postdata = array())
    {

	$decimal_places = 4;

	//set 4 decimal places to tariff
	foreach ($postdata['tariff']['tariff'] as $key => $tarrif) {
	    $postdata['tariff']['tariff'][$key] = round($tarrif, $decimal_places);
	}

	$tariffs = $postdata['tariff'];

	$this->utilities_month = $postdata['month'];
	$this->utilities_year  = $postdata['year'];
	$electricityTariff     = $this->getElectricityTariff();

	// Delete not posted data
	if(!empty($postdata['tariff']['tariff_id'])){
	    $this->db->where_not_in('id', $postdata['tariff']['tariff_id']);
	    $this->db->where('month_id', $postdata['month']);
	    $this->db->where('year_id', $postdata['year']);
	    $this->db->where('site_id', $postdata['site_id']);
	    $this->db->delete($this->_tbl_electricity_tariff);
	}

	// Save new recoreds
	$data['site_id']  = $postdata['site_id'];
	$data['month_id'] = $postdata['month'];
	$data['year_id']  = $postdata['year'];

	if (!empty($tariffs['tariff_id'])) {
	    foreach ($tariffs['tariff_id'] as $key => $tariff) {
		$data['tariff']     = $tariffs['tariff'][$key];
		$data['total_kwh']  = $tariffs['total_kwh'][$key];
		$data['total_cost'] = $tariffs['total_cost'][$key]; //($data['tariff']*$data['total_kwh']);

		$checked = false;
		$this->db->select('id');
		$this->db->from($this->_tbl_electricity_tariff);
		$this->db->where('id', $tariff);
		$this->db->where('month_id', $postdata['month']);
		$this->db->where('year_id', $postdata['year']);
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
		    $checked = true;
		}

		if (!empty($tariff) && $checked) {
		    // Add new tariff
		    $this->db->where('id', $tariff);
		    $this->db->update($this->_tbl_electricity_tariff, $data);
		    $action = 'Update';
		} else {
		    //Delete notification
		    $this->db->delete($this->_tbl_notifications, array('site_id' => $data['site_id'], 'field_name' => 'purchased_electricity_cost', 'month' => $postdata['month'], 'year' => $postdata['year']));

		    // Update tariff
		    $this->db->insert($this->_tbl_electricity_tariff, $data);
		    $action = 'Create';
		}
	    }
	}

	$dateObj = DateTime::createFromFormat('!m', $postdata['month']);
	$month   = $dateObj->format('F');

	$year    = $postdata['year'];
	$additional_field = $month.' - '.$year;
	saveAuditTrail($postdata['user_id'],$postdata['site_id'],'Utilities (Monthly) - ('.$additional_field.')',$action);
	return true;
    }

    public function getElectricityTariff()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_electricity_tariff);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);
	$this->db->where('site_id', $this->site_id);

	$result = $this->db->get();
	return $result->result_array();
    }

    /* function yearlyUtilityReport(){
    $current_year = date('Y');
    $last_year = $current_year-1;

    $query = "SELECT SUM(total_electricity_cost) as electricity, SUM(total_fuel_oil_cost) as fuel, SUM(total_lpg_cost) as lpg, SUM(total_natural_gas_cost) as natural_gas, SUM(district_heating_cost) as heating_district, SUM(district_cooling_cost) as cooling_district, SUM(water_total_consumption_cost) as water
    FROM {$this->_tbl_utilities}
    WHERE (year_id='$last_year' AND month_id='12') OR (year_id='$current_year' AND month_id<'12')
    AND site_id={$this->site_id}";

    $result = $this->db->query($query);
    return $result->row_array();
    } */

    //notification
    public function getNotificationElectricityTariff()
    {
	$this->db->select('SUM(COALESCE(total_kwh, 0)) as kwh_sum');
	$this->db->from($this->_tbl_electricity_tariff);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);
	$this->db->where('site_id', $this->site_id);
	$result = $this->db->get();
	return $result->result_array();
    }

    /* Check notification site wise */

    public function getNotificationSiteConfig()
    {
	$this->db->select('*');
	$this->db->where('site_id', $this->site_id);
	$this->db->from('site_notifications');
	$notification_key_result = $this->db->get()->result_array();
	return $notification_key_result;
    }

    /* This function gives wrong records when no data found */
    /* function getNotificationUtility($site_id) {
    $column_array = array('id');

    $this->db->select('*');
    $this->db->where('site_id',$this->site_id);
    $this->db->from('site_notifications');
    $notification_key_result = $this->db->get()->result_array();

    if(!empty($notification_key_result)){
    foreach ($notification_key_result as $key => $value) {
    $column_array[] = $value['notification_title'];
    }
    }

    $column_string = implode(',', $column_array);
    $this->db->select($column_string);
    $this->db->from($this->_tbl_utilities);
    $this->db->where('month_id', $this->utilities_month);
    $this->db->where('year_id', $this->utilities_year);
    $this->db->where('site_id', $this->site_id);
    $result = $this->db->get();
    return $result->row_array();
    } */

    public function getNotificationUtility()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_utilities);
	$this->db->where('month_id', $this->utilities_month);
	$this->db->where('year_id', $this->utilities_year);
	$this->db->where('site_id', $this->site_id);
	$result = $this->db->get();
	//echo $this->db->last_query()."<br><br>";exit;
	return $result->row_array();
    }

    public function addNotification($postdata = array())
    {
	if ($postdata) {
	    if(isset($postdata['field_label']) && !empty($postdata['field_label']) && isset($postdata['month']) && !empty($postdata['month']) && $postdata['month'] > 0) {
	    //delete data first
	    $this->db->delete($this->_tbl_notifications, array('site_id' => $postdata['site_id'], 'field_name' => $postdata['field_name'], 'month' => $postdata['month'], 'year' => $postdata['year']));

	    $this->db->insert($this->_tbl_notifications, $postdata);
	    }
	}
	return true;
    }

    public function clearNotification()
    {
	$currYear = intval(date('Y'));
	$currMonth = intval(date('m'));
	$modelYear = $this->utilities_year;
	$modelMonth = $this->utilities_month;
	$this->db
	->where("month BETWEEN '$modelMonth' AND '$currMonth'")
	->where("year BETWEEN '$modelYear' AND '$currYear'")
	->where('site_id', $this->site_id)
	->delete($this->_tbl_notifications);

        $this->db
        ->where("month <= '$modelMonth'")
        ->where("year <= '$modelYear'")
        ->where('site_id', $this->site_id)
        ->delete($this->_tbl_notifications);

        return true;
    }

    public function getNotifications()
    {
	if (isset($this->site_id)) {
	    // Get sites notification config
	    $utility_notification_config_result = $this->getNotificationSiteConfig();
	    // pre($utility_notification_config_result);
	    $utility_notification_config_array = array();
	    if (!empty($utility_notification_config_result)) {
		foreach ($utility_notification_config_result as $key => $value) {
		    $utility_notification_config_array[] = $value['notification_title'];
		}
	    }

	    if (!empty($utility_notification_config_array)) {
		$this->db->select('*');
		$this->db->from($this->_tbl_notifications);
		$this->db->where('site_id', $this->site_id);
		$this->db->where_in('field_name', $utility_notification_config_array);
		$this->db->order_by("month", "desc");
		$result = $this->db->get();
		return $result->result_array();
	    } else {
		return array();
	    }
	}
    }

    public function deleteNotification($postdata = array())
    {
	if ($postdata) {
	    $this->db->delete($this->_tbl_notifications, $postdata);
	}
    }

    // get ngo data with actions
    public function getCSRwithActions()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_csr_ngo);
	$this->db->where('site_id', $this->site_id);
	//$this->db->where('id =', $id);
	$this->db->where('quarter', $this->utilities_quarter_quarter);
	$this->db->where('year', $this->utilities_quarter_year);
	$result      = $this->db->get();
	$resultArray = $result->result_array();

	$ngo_data = array();

	if (!empty($resultArray)) {
	    foreach ($resultArray as $result) {

		$ngo_id = $result['id'];
		$ngo_data[$ngo_id] = $result;

		//actions_info
		$this->db->select('*');
		$this->db->from($this->_tbl_csr_ngo_actions);
		$this->db->where('site_id', $this->site_id);
		$this->db->where('ngo_id', $ngo_id);
		$actions = $this->db->get()->result_array();

		if (!empty($actions)) {
		    foreach ($actions as $action) {

			$action_id = $action['id'];

			$this->db->select('*');
			$this->db->from($this->_tbl_csr_ngo_actions_images);
			$this->db->where('action_id', $action_id);
			$this->db->where('ngo_id', $ngo_id);
			$images = $this->db->get()->result_array();

			$actions_data['id'] = $action['id'];
			$actions_data['site_id'] = $action['site_id'];
			$actions_data['ngo_id']  = $action['ngo_id'];
			$actions_data['text']    = $action['text'];
			$actions_data['photo']   = $action['photo'];
			$actions_data['sdg']     = $action['sdg'];
			$actions_data['media']   = $images;

			$ngo_data[$ngo_id]['actions'][] = $actions_data;
		    }
		}
	    }
	}
	return $ngo_data;
    }

    // delete action image
    public function delete_image($id)
    {
	$id = intval($id);
	$this->db->where('id', $id);
	$this->db->delete($this->_tbl_csr_ngo_actions_images);
    }

    // delete_biodiversity_image
    public function delete_biodiversity_image($id)
    {
	$id = intval($id);
	$this->db->where('id', $id);
	$this->db->delete($this->_tbl_csr_biodiversity_images);
    }

    // delete action
    public function delete_action($id)
    {
	$id = intval($id);
	$this->db->where('id', $id);
	$this->db->delete($this->_tbl_csr_ngo_actions);

	$this->db->where('action_id', $id);
	$this->db->delete($this->_tbl_csr_ngo_actions_images);
    }

    // delete biodiversity with images
    public function delete_biodiversity($id)
    {
	$id = intval($id);
	$this->db->where('id', $id);
	$this->db->delete($this->_tbl_csr_biodiversity_data);

	$this->db->where('biodiversity_id', $id);
	$this->db->delete($this->_tbl_csr_biodiversity_images);
    }

	// delete ngo, actions and images
	public function delete_ngo($id)
    {
	$id = intval($id);
	$this->db->where('id', $id);
	$this->db->delete($this->_tbl_csr_ngo);

	$this->db->where('ngo_id', $id);
	$this->db->delete($this->_tbl_csr_ngo_actions);

	$this->db->where('ngo_id', $id);
	$this->db->delete($this->_tbl_csr_ngo_actions_images);
    }

    // save ngo
    public function save_ngo($postdata = array())
    {
	$ngo_name = array();
	$csr_year    =  $postdata['year'];
	$quarter     =  $postdata['quarter'];
	$ngo_name    =  $postdata['ngo_name'];
	$action_text =  $postdata['action_text'];
	$action_sdg  =  $postdata['action_sdg'];
	$action_id   =  $postdata['action_id'];
	$ngo_id_data =  $postdata['ngo_id'];
	$action_media =  $postdata['files']['action_media'];
	$cnt_name = count($ngo_name);
	$inserted = 0;

	for ($i=0; $i < $cnt_name ; $i++) {

	    $csr_ngo_data['site_id']    = $postdata['site_id'];
	    $csr_ngo_data['quarter']    = $quarter;
	    $csr_ngo_data['year']       = $csr_year;
	    $csr_ngo_data['user_id']    = $postdata['user_id'];
	    $csr_ngo_data['ngo_name']   = $ngo_name[$i];
	    $ngo_id                     = $ngo_id_data[$i];

	    $checked = false;
	    $this->db->select('id');
	    $this->db->from($this->_tbl_csr_ngo);
	    $this->db->where('id', $ngo_id);
	    $result = $this->db->get();
	    if ($result->num_rows() > 0) {
		$checked = true;
	    }

	    // if($quarter[$i] != '' && $csr_year[$i] != '' && $ngo_name[$i] != '')
	    if($ngo_name[$i] != '')
	    {
		if (!empty($csr_ngo_data) && $checked) {
		    // update action
		    $this->db->where('id', $ngo_id);
		    $this->db->update($this->_tbl_csr_ngo, $csr_ngo_data);
		    $inserted++;
		    $data_action = 'Update';
		} else {
		    // add new action
		    $this->db->insert($this->_tbl_csr_ngo, $csr_ngo_data);
		    $ngo_id = $this->db->insert_id();
		    $inserted++;
		    $data_action = 'Create';
		}

		$data['site_id']  = $postdata['site_id'];
		$data['ngo_id']   = $ngo_id;

		for ($j=0; $j < count($action_text) ; $j++) {

		    if((!empty($action_text[$i][$j]))  && (!empty($action_sdg[$i][$j] )))
		    {
			$data['text'] = $action_text[$i][$j];
			$data['sdg']  = $action_sdg[$i][$j];
			$actionid     = $action_id[$i][$j];

			$checked = false;
			$this->db->select('id');
			$this->db->from($this->_tbl_csr_ngo_actions);
			$this->db->where('id', $actionid);
			$this->db->where('ngo_id', $ngo_id);
			$result = $this->db->get();
			if ($result->num_rows() > 0) {
			    $checked = true;
			}
			if (!empty($actionid) && $checked) {
			    // update action
			    $this->db->where('id', $actionid);
			    $this->db->update($this->_tbl_csr_ngo_actions, $data);
			    $insert_action_id = $actionid;
			} else {
			    // add new action
			    $this->db->insert($this->_tbl_csr_ngo_actions, $data);
			    $insert_action_id = $this->db->insert_id();
			}
		       // Insert Action media

			$action_media_temp =array();

			foreach ($action_media['name'] as $key_media => $actionmedia) {
			    $action_media_temp['name'][] = array_values($actionmedia);
			}
			foreach ($action_media['size'] as $key_media => $actionmedia) {
			    $action_media_temp['size'][] = array_values($actionmedia);
			}
			foreach ($action_media['tmp_name'] as $key_media => $actionmedia) {
			    $action_media_temp['tmp_name'][] = array_values($actionmedia);
			}
			foreach ($action_media['type'] as $key_media => $actionmedia) {
			    $action_media_temp['type'][] = array_values($actionmedia);
			}
			foreach ($action_media['error'] as $key_media => $actionmedia) {
			    $action_media_temp['error'][] = array_values($actionmedia);
			}

			for ($k=0; $k < count($action_media_temp) ; $k++) {

			    $uploads = $action_media_temp['name'][$i][$j];

			    $image_data = array();

			    if (isset($uploads[$k])) {

				if (!file_exists(BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/actions/".$insert_action_id)) {
				    mkdir(BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/actions/".$insert_action_id, 0777, true);
				}

				$config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
				$config['max_size']       = '2048';
				$config['maintain_ratio'] = true;
				$config['width']          = 140;
				$config['height']         = 100;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				$valid_formats = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma");

				$imagename     = $uploads[$k];
				$size = $action_media_temp['size'][$i][$j][$k];

				$cnt    = strrpos($imagename, ".");
				if (!$cnt) {
				    $ext = '';
				}
				$l              = strlen($imagename) - $cnt;
				$ext            = substr($imagename, $cnt + 1, $l);
				$action_name    = 'action_' . rand(11111, 9999999) . '.' . $ext;

				if ($ext) {
				    if (in_array($ext, $valid_formats)) {

					// procedure further if and only if image size can not be more than 10MB.
					if ($size < (1024 * 1024 * 10)) {

					    $uploadedfile = $action_media_temp['tmp_name'][$i][$j][$k];

					    $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/actions/".$insert_action_id."/".$action_name;

					    $_movestatus  = move_uploaded_file($uploadedfile, $target_file);

					    if (!$_movestatus) {
						$this->theme->set_message('site image is not uploaded', 'error');
					    } else {

						$image_data['ngo_id']    = $ngo_id;
						$image_data['action_id'] = $insert_action_id;
						$image_data['is_image']  = 1;
						$image_data['image']     = $action_name;

						$this->db->insert($this->_tbl_csr_ngo_actions_images, $image_data);

					    }
					} else {
					    $this->theme->set_message('site image size is too large', 'error');
					}
				    } else {
					$this->theme->set_message('site image extension is not .jpg or .png formate', 'error');
				    }
				}
			    }
			}
		    }
		}
	    }
	}
	if(!empty($ngo_name))
	{
	    // Save audit trail
	    $data_quarter  = $csr_ngo_data['quarter'];
	    $data_action   = 'Create/Update';
	    $additional_field = $data_quarter.' - '.$csr_ngo_data['year'];
	    saveAuditTrail($postdata['user_id'],$postdata['site_id'],'Utilities (Quarterly - Social) - ('.$additional_field.')',$data_action);
	}

	$difference = $cnt_name - $inserted;

	if($difference == 0)
	{
	    $msg = $inserted." NGOs data saved successfully";
	    return $msg;
	}
	else
	{
	    $msg = $inserted." NGOs data saved successfully. Error in ".$difference." NGOs data, please fill it properly";
	    return $msg;
	}
	return true;
    }

    // get details of HR tab
    public function get_hr()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_csr_hr_data);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('quarter', $this->utilities_quarter_quarter);
	$this->db->where('year', $this->utilities_quarter_year);
	$result      = $this->db->get();
	$resultArray = $result->row_array();
	return $resultArray;
    }

    // get waste tab detail
    public function get_waste()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_csr_waste_data);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('quarter', $this->utilities_quarter_quarter);
	$this->db->where('year', $this->utilities_quarter_year);
	$result      = $this->db->get();
	$resultArray = $result->row_array();
	return $resultArray;
    }

    // save hr tab data
    public function save_hr($postdata = array())
    {
	$data           = array();
	$decimal_places = 4;

	$this->db->select('*');
	$this->db->from($this->_tbl_csr_hr_data);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('quarter', $postdata['hr_quarter']);
	$this->db->where('year', $postdata['hr_year']);
	$this->db->where('id', $postdata['hr_id']);
	$result  = $this->db->get();
	$hr_data = $result->row_array();

	// Auto set
	$data['site_id']  = $this->site_id;
	$data['year']     = $postdata['hr_year'];
	$data['quarter']  = $postdata['hr_quarter'];
	$data['user_id']  = $postdata['user_id'];

	// User inputs
	$data['hr_no_of_hrs']  = isset($postdata['hr_no_of_hrs']) ? $postdata['hr_no_of_hrs'] : '';
	$data['hr_no_of_employees'] = isset($postdata['hr_no_of_employees']) ? $postdata['hr_no_of_employees'] : '';
	$data['nd_no_of_incidents_of_discrimination'] = isset($postdata['nd_no_of_incidents_of_discrimination']) ? $postdata['nd_no_of_incidents_of_discrimination'] : '';
	$data['nd_incident_reviewed_by_org']  = isset($postdata['nd_incident_reviewed_by_org']) ? $postdata['nd_incident_reviewed_by_org'] : '';
	$data['nd_remediation_plans_implemented'] = isset($postdata['nd_remediation_plans_implemented']) ? $postdata['nd_remediation_plans_implemented'] : '';
	$data['lpd_hires_age_under_thirty'] = isset($postdata['lpd_hires_age_under_thirty']) ? $postdata['lpd_hires_age_under_thirty'] : '';
	$data['lpd_hires_age_between_thirty_to_fifty'] = isset($postdata['lpd_hires_age_between_thirty_to_fifty']) ? $postdata['lpd_hires_age_between_thirty_to_fifty'] : '';
	$data['lpd_hires_age_more_than_fifty'] = isset($postdata['lpd_hires_age_more_than_fifty']) ? $postdata['lpd_hires_age_more_than_fifty'] : '';
	$data['lpd_hires_gender_male'] = isset($postdata['lpd_hires_gender_male']) ? $postdata['lpd_hires_gender_male'] : '';
	$data['lpd_hires_gender_female']  = isset($postdata['lpd_hires_gender_female']) ? $postdata['lpd_hires_gender_female'] : '';
	$data['lpd_turnover_age_under_thirty'] = isset($postdata['lpd_turnover_age_under_thirty']) ? $postdata['lpd_turnover_age_under_thirty'] : '';
	$data['lpd_turnover_age_between_thirty_to_fifty'] = isset($postdata['lpd_turnover_age_between_thirty_to_fifty']) ? $postdata['lpd_turnover_age_between_thirty_to_fifty'] : '';
	$data['lpd_turnover_age_more_than_fifty'] = isset($postdata['lpd_turnover_age_more_than_fifty']) ? $postdata['lpd_turnover_age_more_than_fifty'] : '';
	$data['lpd_turnover_gender_male'] = isset($postdata['lpd_turnover_gender_male']) ? $postdata['lpd_turnover_gender_male'] : '';
	$data['lpd_turnover_gender_female'] = isset($postdata['lpd_turnover_gender_female']) ? $postdata['lpd_turnover_gender_female'] : '';
	$data['ohs_rate_of_occupational_diseases'] = isset($postdata['ohs_rate_of_occupational_diseases']) ? $postdata['ohs_rate_of_occupational_diseases'] : '';
	$data['ohs_lost_day_rates']   = isset($postdata['ohs_lost_day_rates']) ? $postdata['ohs_lost_day_rates'] : '';
	$data['ohs_absentee_rate']    = isset($postdata['ohs_absentee_rate']) ? $postdata['ohs_absentee_rate'] : '';
	$data['ohs_gender_male']      = isset($postdata['ohs_gender_male']) ? $postdata['ohs_gender_male'] : '';
	$data['ohs_gender_female']    = isset($postdata['ohs_gender_female']) ? $postdata['ohs_gender_female'] : '';
	$data['te_gender_male']       = isset($postdata['te_gender_male']) ? $postdata['te_gender_male'] : '';
	$data['te_gender_female']     = isset($postdata['te_gender_female']) ? $postdata['te_gender_female'] : '';
	$data['te_team_member']       = isset($postdata['te_team_member']) ? $postdata['te_team_member'] : '';
	$data['te_supervisor']        = isset($postdata['te_supervisor']) ? $postdata['te_supervisor'] : '';
	$data['te_manager']           = isset($postdata['te_manager']) ? $postdata['te_manager'] : '';
	$data['te_head_of_department']    = isset($postdata['te_head_of_department']) ? $postdata['te_head_of_department'] : '';
	$data['te_assistant_head_of_department']  = isset($postdata['te_assistant_head_of_department']) ? $postdata['te_assistant_head_of_department'] : '';
	$data['te_general_manager']   = isset($postdata['te_general_manager']) ? $postdata['te_general_manager'] : '';
	$data['te_senior_manager']    = isset($postdata['te_senior_manager']) ? $postdata['te_senior_manager'] : '';
	$data['tae_gender_male']       = isset($postdata['tae_gender_male']) ? $postdata['tae_gender_male'] : '';
	$data['tae_gender_female']     = isset($postdata['tae_gender_female']) ? $postdata['tae_gender_female'] : '';
	$data['tae_team_member']       = isset($postdata['tae_team_member']) ? $postdata['tae_team_member'] : '';
	$data['tae_supervisor']        = isset($postdata['tae_supervisor']) ? $postdata['tae_supervisor'] : '';
	$data['tae_manager']           = isset($postdata['tae_manager']) ? $postdata['tae_manager'] : '';
	$data['tae_head_of_department']    = isset($postdata['tae_head_of_department']) ? $postdata['tae_head_of_department'] : '';
	$data['tae_assistant_head_of_department']  = isset($postdata['tae_assistant_head_of_department']) ? $postdata['tae_assistant_head_of_department'] : '';
	$data['tae_general_manager']   = isset($postdata['tae_general_manager']) ? $postdata['tae_general_manager'] : '';
	$data['tae_senior_manager']    = isset($postdata['tae_senior_manager']) ? $postdata['tae_senior_manager'] : '';
	$data['diversity_and_opportunity']    = isset($postdata['diversity_and_opportunity']) ? $postdata['diversity_and_opportunity'] : '';
	$data['ermw_gender_male']    = isset($postdata['ermw_gender_male']) ? $postdata['ermw_gender_male'] : '';
	$data['ermw_gender_female']  = isset($postdata['ermw_gender_female']) ? $postdata['ermw_gender_female'] : '';
	$data['ermw_team_member']    = isset($postdata['ermw_team_member']) ? $postdata['ermw_team_member'] : '';
	$data['ermw_supervisor']     = isset($postdata['ermw_supervisor']) ? $postdata['ermw_supervisor'] : '';
	$data['ermw_manager']    = isset($postdata['ermw_manager']) ? $postdata['ermw_manager'] : '';
	$data['ermw_head_of_department']    = isset($postdata['ermw_head_of_department']) ? $postdata['ermw_head_of_department'] : '';
	$data['ermw_assistant_head_of_department']    = isset($postdata['ermw_assistant_head_of_department']) ? $postdata['ermw_assistant_head_of_department'] : '';
	$data['ermw_senior_manager']    = isset($postdata['ermw_senior_manager']) ? $postdata['ermw_senior_manager'] : '';
	$data['ec_ratios_of_std_gender_male']    = isset($postdata['ec_ratios_of_std_gender_male']) ? $postdata['ec_ratios_of_std_gender_male'] : '';
	$data['ec_ratios_of_std_gender_female']    = isset($postdata['ec_ratios_of_std_gender_female']) ? $postdata['ec_ratios_of_std_gender_female'] : '';
	$data['ec_proportion_of_senior_management_hired']    = isset($postdata['ec_proportion_of_senior_management_hired']) ? $postdata['ec_proportion_of_senior_management_hired'] : '';
	$data['tmsr_global_index'] = isset($postdata['tmsr_global_index']) ? $postdata['tmsr_global_index'] : '';
	$data['tmsr_leadership_index'] = isset($postdata['tmsr_leadership_index']) ? $postdata['tmsr_leadership_index'] : '';
	$data['tmsr_loyalty_index'] = isset($postdata['tmsr_loyalty_index']) ? $postdata['tmsr_loyalty_index'] : '';
	$data['tmsr_other_index']  = isset($postdata['tmsr_other_index']) ? $postdata['tmsr_other_index'] : '';
	$data['talent_management'] = isset($postdata['talent_management']) ? $postdata['talent_management'] : '';

	if (!empty($hr_data)) {

	    // $this->db->where('id', $postdata['id']);
	    $this->db->where('quarter', $postdata['hr_quarter']);
	    $this->db->where('year', $postdata['hr_year']);
	    $this->db->where('site_id', $this->site_id);
	    $this->db->update($this->_tbl_csr_hr_data, $data);
	    $id = $postdata['hr_id'];
	    $data_action   = 'Update';
	} else {
	    $this->db->insert($this->_tbl_csr_hr_data, $data);
	    $id = $this->db->insert_id();
	    $data_action   = 'Create';
	}

	$data_quarter  = $postdata['hr_quarter'];
	$additional_field = $data_quarter.' - '.$postdata['hr_year'];
	saveAuditTrail($postdata['user_id'], $this->site_id,'Utilities (Quarterly - HR) - ('.$additional_field.')',$data_action);
	return $id;
    }

    // save waste tab data
    public function save_waste($postdata = array())
    {
	$data           = array();
	$decimal_places = 4;

	$this->db->select('*');
	$this->db->from($this->_tbl_csr_waste_data);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('quarter', $postdata['waste_quarter']);
	$this->db->where('year', $postdata['waste_year']);
	$this->db->where('id', $postdata['waste_id']);
	$result  = $this->db->get();
	$waste_data = $result->row_array();

	// Auto set
	$data['site_id']  = $this->site_id;
	$data['year']     = $postdata['waste_year'];
	$data['quarter']  = $postdata['waste_quarter'];
	$data['user_id']  = $postdata['user_id'];

	// User inputs
	// For CSR Waste tab data
	$data['pete_waste_kg']  = isset($postdata['pete_waste_kg']) ? $postdata['pete_waste_kg'] : '';
	$data['pete_cost_of_waste_removal_per_kg']  = isset($postdata['pete_cost_of_waste_removal_per_kg']) ? $postdata['pete_cost_of_waste_removal_per_kg'] : '';
	$data['pete_qty_recycled_kg']  = isset($postdata['pete_qty_recycled_kg']) ? $postdata['pete_qty_recycled_kg'] : '';
	$data['pete_revenue_from_recycling_per_kg']  = isset($postdata['pete_revenue_from_recycling_per_kg']) ? $postdata['pete_revenue_from_recycling_per_kg'] : '';

	$data['hdpe_waste_kg']  = isset($postdata['hdpe_waste_kg']) ? $postdata['hdpe_waste_kg'] : '';
	$data['hdpe_cost_of_waste_removal_per_kg']  = isset($postdata['hdpe_cost_of_waste_removal_per_kg']) ? $postdata['hdpe_cost_of_waste_removal_per_kg'] : '';
	$data['hdpe_qty_recycled_kg']  = isset($postdata['hdpe_qty_recycled_kg']) ? $postdata['hdpe_qty_recycled_kg'] : '';
	$data['hdpe_revenue_from_recycling_per_kg']  = isset($postdata['hdpe_revenue_from_recycling_per_kg']) ? $postdata['hdpe_revenue_from_recycling_per_kg'] : '';

	$data['pvc_waste_kg']  = isset($postdata['pvc_waste_kg']) ? $postdata['pvc_waste_kg'] : '';
	$data['pvc_cost_of_waste_removal_per_kg']  = isset($postdata['pvc_cost_of_waste_removal_per_kg']) ? $postdata['pvc_cost_of_waste_removal_per_kg'] : '';
	$data['pvc_qty_recycled_kg']  = isset($postdata['pvc_qty_recycled_kg']) ? $postdata['pvc_qty_recycled_kg'] : '';
	$data['pvc_revenue_from_recycling_per_kg']  = isset($postdata['pvc_revenue_from_recycling_per_kg']) ? $postdata['pvc_revenue_from_recycling_per_kg'] : '';

	$data['ldpe_waste_kg']  = isset($postdata['ldpe_waste_kg']) ? $postdata['ldpe_waste_kg'] : '';
	$data['ldpe_cost_of_waste_removal_per_kg']  = isset($postdata['ldpe_cost_of_waste_removal_per_kg']) ? $postdata['ldpe_cost_of_waste_removal_per_kg'] : '';
	$data['ldpe_qty_recycled_kg']  = isset($postdata['ldpe_qty_recycled_kg']) ? $postdata['ldpe_qty_recycled_kg'] : '';
	$data['ldpe_revenue_from_recycling_per_kg']  = isset($postdata['ldpe_revenue_from_recycling_per_kg']) ? $postdata['ldpe_revenue_from_recycling_per_kg'] : '';

	$data['pp_waste_kg']  = isset($postdata['pp_waste_kg']) ? $postdata['pp_waste_kg'] : '';
	$data['pp_cost_of_waste_removal_per_kg']  = isset($postdata['pp_cost_of_waste_removal_per_kg']) ? $postdata['pp_cost_of_waste_removal_per_kg'] : '';
	$data['pp_qty_recycled_kg']  = isset($postdata['pp_qty_recycled_kg']) ? $postdata['pp_qty_recycled_kg'] : '';
	$data['pp_revenue_from_recycling_per_kg']  = isset($postdata['pp_revenue_from_recycling_per_kg']) ? $postdata['pp_revenue_from_recycling_per_kg'] : '';

	$data['ps_waste_kg']  = isset($postdata['ps_waste_kg']) ? $postdata['ps_waste_kg'] : '';
	$data['ps_cost_of_waste_removal_per_kg']  = isset($postdata['ps_cost_of_waste_removal_per_kg']) ? $postdata['ps_cost_of_waste_removal_per_kg'] : '';
	$data['ps_qty_recycled_kg']  = isset($postdata['ps_qty_recycled_kg']) ? $postdata['ps_qty_recycled_kg'] : '';
	$data['ps_revenue_from_recycling_per_kg']  = isset($postdata['ps_revenue_from_recycling_per_kg']) ? $postdata['ps_revenue_from_recycling_per_kg'] : '';

	$data['op_waste_kg']  = isset($postdata['op_waste_kg']) ? $postdata['op_waste_kg'] : '';
	$data['op_cost_of_waste_removal_per_kg']  = isset($postdata['op_cost_of_waste_removal_per_kg']) ? $postdata['op_cost_of_waste_removal_per_kg'] : '';
	$data['op_qty_recycled_kg']  = isset($postdata['op_qty_recycled_kg']) ? $postdata['op_qty_recycled_kg'] : '';
	$data['op_revenue_from_recycling_per_kg']  = isset($postdata['op_revenue_from_recycling_per_kg']) ? $postdata['op_revenue_from_recycling_per_kg'] : '';

	$data['op_waste_kg']  = isset($postdata['op_waste_kg']) ? $postdata['op_waste_kg'] : '';
	$data['op_cost_of_waste_removal_per_kg']  = isset($postdata['op_cost_of_waste_removal_per_kg']) ? $postdata['op_cost_of_waste_removal_per_kg'] : '';
	$data['op_qty_recycled_kg']  = isset($postdata['op_qty_recycled_kg']) ? $postdata['op_qty_recycled_kg'] : '';
	$data['op_revenue_from_recycling_per_kg']  = isset($postdata['op_revenue_from_recycling_per_kg']) ? $postdata['op_revenue_from_recycling_per_kg'] : '';

	$data['fw_waste_kg']  = isset($postdata['fw_waste_kg']) ? $postdata['fw_waste_kg'] : '';
	$data['fw_cost_of_waste_removal_per_kg']  = isset($postdata['fw_cost_of_waste_removal_per_kg']) ? $postdata['fw_cost_of_waste_removal_per_kg'] : '';
	$data['fw_qty_recycled_kg']  = isset($postdata['fw_qty_recycled_kg']) ? $postdata['fw_qty_recycled_kg'] : '';
	$data['fw_revenue_from_recycling_per_kg']  = isset($postdata['fw_revenue_from_recycling_per_kg']) ? $postdata['fw_revenue_from_recycling_per_kg'] : '';

	$data['glass_waste_kg']  = isset($postdata['glass_waste_kg']) ? $postdata['glass_waste_kg'] : '';
	$data['glass_cost_of_waste_removal_per_kg']  = isset($postdata['glass_cost_of_waste_removal_per_kg']) ? $postdata['glass_cost_of_waste_removal_per_kg'] : '';
	$data['glass_qty_recycled_kg']  = isset($postdata['glass_qty_recycled_kg']) ? $postdata['glass_qty_recycled_kg'] : '';
	$data['glass_revenue_from_recycling_per_kg']  = isset($postdata['glass_revenue_from_recycling_per_kg']) ? $postdata['glass_revenue_from_recycling_per_kg'] : '';

	$data['wh_waste_kg']  = isset($postdata['wh_waste_kg']) ? $postdata['wh_waste_kg'] : '';
	$data['wh_cost_of_waste_removal_per_kg']  = isset($postdata['wh_cost_of_waste_removal_per_kg']) ? $postdata['wh_cost_of_waste_removal_per_kg'] : '';
	$data['wh_qty_recycled_kg']  = isset($postdata['wh_qty_recycled_kg']) ? $postdata['wh_qty_recycled_kg'] : '';
	$data['wh_revenue_from_recycling_per_kg']  = isset($postdata['wh_revenue_from_recycling_per_kg']) ? $postdata['wh_revenue_from_recycling_per_kg'] : '';

	$data['wg_waste_kg']  = isset($postdata['wg_waste_kg']) ? $postdata['wg_waste_kg'] : '';
	$data['wg_cost_of_waste_removal_per_kg']  = isset($postdata['wg_cost_of_waste_removal_per_kg']) ? $postdata['wg_cost_of_waste_removal_per_kg'] : '';
	$data['wg_qty_recycled_kg']  = isset($postdata['wg_qty_recycled_kg']) ? $postdata['wg_qty_recycled_kg'] : '';
	$data['wg_revenue_from_recycling_per_kg']  = isset($postdata['wg_revenue_from_recycling_per_kg']) ? $postdata['wg_revenue_from_recycling_per_kg'] : '';

	$data['wg_waste_kg']  = isset($postdata['wg_waste_kg']) ? $postdata['wg_waste_kg'] : '';
	$data['wg_cost_of_waste_removal_per_kg']  = isset($postdata['wg_cost_of_waste_removal_per_kg']) ? $postdata['wg_cost_of_waste_removal_per_kg'] : '';
	$data['wg_qty_recycled_kg']  = isset($postdata['wg_qty_recycled_kg']) ? $postdata['wg_qty_recycled_kg'] : '';
	$data['wg_revenue_from_recycling_per_kg']  = isset($postdata['wg_revenue_from_recycling_per_kg']) ? $postdata['wg_revenue_from_recycling_per_kg'] : '';

	$data['wuko_waste_kg']  = isset($postdata['wuko_waste_kg']) ? $postdata['wuko_waste_kg'] : '';
	$data['wuko_cost_of_waste_removal_per_kg']  = isset($postdata['wuko_cost_of_waste_removal_per_kg']) ? $postdata['wuko_cost_of_waste_removal_per_kg'] : '';
	$data['wuko_qty_recycled_kg']  = isset($postdata['wuko_qty_recycled_kg']) ? $postdata['wuko_qty_recycled_kg'] : '';
	$data['wuko_revenue_from_recycling_per_kg']  = isset($postdata['wuko_revenue_from_recycling_per_kg']) ? $postdata['wuko_revenue_from_recycling_per_kg'] : '';

	$data['wp_waste_kg']  = isset($postdata['wp_waste_kg']) ? $postdata['wp_waste_kg'] : '';
	$data['wp_cost_of_waste_removal_per_kg']  = isset($postdata['wp_cost_of_waste_removal_per_kg']) ? $postdata['wp_cost_of_waste_removal_per_kg'] : '';
	$data['wp_qty_recycled_kg']  = isset($postdata['wp_qty_recycled_kg']) ? $postdata['wp_qty_recycled_kg'] : '';
	$data['wp_revenue_from_recycling_per_kg']  = isset($postdata['wp_revenue_from_recycling_per_kg']) ? $postdata['wp_revenue_from_recycling_per_kg'] : '';

	$data['wc_waste_kg']  = isset($postdata['wc_waste_kg']) ? $postdata['wc_waste_kg'] : '';
	$data['wc_cost_of_waste_removal_per_kg']  = isset($postdata['wc_cost_of_waste_removal_per_kg']) ? $postdata['wc_cost_of_waste_removal_per_kg'] : '';
	$data['wc_qty_recycled_kg']  = isset($postdata['wc_qty_recycled_kg']) ? $postdata['wc_qty_recycled_kg'] : '';
	$data['wc_revenue_from_recycling_per_kg']  = isset($postdata['wc_revenue_from_recycling_per_kg']) ? $postdata['wc_revenue_from_recycling_per_kg'] : '';

	$data['gw_waste_kg']  = isset($postdata['gw_waste_kg']) ? $postdata['gw_waste_kg'] : '';
	$data['gw_cost_of_waste_removal_per_kg']  = isset($postdata['gw_cost_of_waste_removal_per_kg']) ? $postdata['gw_cost_of_waste_removal_per_kg'] : '';
	$data['gw_qty_recycled_kg']  = isset($postdata['gw_qty_recycled_kg']) ? $postdata['gw_qty_recycled_kg'] : '';
	$data['gw_revenue_from_recycling_per_kg']  = isset($postdata['gw_revenue_from_recycling_per_kg']) ? $postdata['gw_revenue_from_recycling_per_kg'] : '';

	if (!empty($waste_data)) {

	    // $this->db->where('id', $postdata['id']);
	    $this->db->where('quarter', $postdata['waste_quarter']);
	    $this->db->where('year', $postdata['waste_year']);
	    $this->db->where('site_id', $this->site_id);
	    $this->db->update($this->_tbl_csr_waste_data, $data);
	    $id = $postdata['waste_id'];
	    $data_action   = 'Update';

	} else {
	    $this->db->insert($this->_tbl_csr_waste_data, $data);
	    $id = $this->db->insert_id();
	    $data_action   = 'Create';
	}

	$data_quarter  = $postdata['waste_quarter'];
	$additional_field = $data_quarter.' - '.$postdata['waste_year'];
	saveAuditTrail($postdata['user_id'], $this->site_id,'Utilities (Quarterly - Waste) - ('.$additional_field.')',$data_action);

	$image_data = array();

	foreach ($postdata['files'] as $key => $value) {

		$uploads  = $value['name'];
		$type     = $value['type'];
		$tmp_name = $value['tmp_name'];
		$size     = $value['size'];

		if(($uploads != '') && ($type != '') && ($tmp_name != ''))
		{
			if (!file_exists(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/")) {
			mkdir(BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/", 0777, true);
		    }

		    $config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
		    $config['max_size']       = '2048';
		    $config['maintain_ratio'] = true;
		    $config['width']          = 140;
		    $config['height']         = 100;

		    $this->load->library('upload', $config);
		    $this->upload->initialize($config);

		    $valid_formats = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma");

		    $imagename     = $uploads;
		    $cnt    = strrpos($imagename, ".");
		    if (!$cnt) {
			$ext = '';
		    }
		    $l                = strlen($imagename) - $cnt;
		    $ext              = substr($imagename, $cnt + 1, $l);
		    $upload_file_name = $id.'_'.$key . rand(11111, 9999999) . '.' . $ext;

		    if ($ext) {
			if (in_array($ext, $valid_formats)) {

				$uploadedfile = $tmp_name;

			    $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/site_".$this->site_id."/waste_invoices/".$upload_file_name;

			    $_movestatus  = move_uploaded_file($uploadedfile, $target_file);

			    if (!$_movestatus) {
				// $this->theme->set_message('waste image is not uploaded', 'error');
			    } else {

				$image_data[$key]    = $upload_file_name;
			    }

			}
		    }
		}
		}

	if (!empty($image_data)) {

	    $this->db->where('id', $id);
	    $this->db->where('quarter', $postdata['waste_quarter']);
	    $this->db->where('year', $postdata['waste_year']);
	    $this->db->where('site_id', $this->site_id);
	    $this->db->update($this->_tbl_csr_waste_data, $image_data);
	}
	return $id;
    }

    // save biodiversity tab data
    public function save_biodiversity($postdata= array())
    {
	$data = array();

	$bio_media =  $postdata['files']['bio_media'];
	$inserted = 0;

	foreach ($postdata as $key => $value) {

		$post_detail_bio[$key] = array_values($value);
	}

	$measure = $post_detail_bio['measure'];
	$partner = $post_detail_bio['partner'];
	$bio_id  = $post_detail_bio['bio_id'];
	$measure_count = count($measure);

	for ($j=0; $j < $measure_count ; $j++) {

	    if((!empty($measure[$j]))  && (!empty($partner[$j] )))
	    {
		$data['measure']  = $measure[$j];
		$data['partner']  = $partner[$j];
		$data['site_id']  = $postdata['site_id'];
		    $data['quarter']  = $postdata['biodiversity_quarter'];
		    $data['year']     = $postdata['biodiversity_year'];
		    $data['user_id']  = $postdata['user_id'];
		$bioid            = $bio_id[$j];

		$checked = false;
		$this->db->select('id');
		$this->db->from($this->_tbl_csr_biodiversity_data);
		$this->db->where('id', $bioid);
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
		    $checked = true;
		}
		if (!empty($bioid) && $checked) {
		    // update bio
		    $this->db->where('id', $bioid);
		    $this->db->update($this->_tbl_csr_biodiversity_data, $data);
		    $insert_bio_id = $bioid;
		    $inserted++;
		} else {
		    // add new bio
		    $this->db->insert($this->_tbl_csr_biodiversity_data, $data);
		    $insert_bio_id = $this->db->insert_id();
		    $inserted++;
		}

		// Insert biodiversity media files
		$bio_media_temp =array();

		foreach ($bio_media['name'] as $key_media => $biomedia) {
		    $bio_media_temp['name'][] = array_values($biomedia);
		}
		foreach ($bio_media['size'] as $key_media => $biomedia) {
		    $bio_media_temp['size'][] = array_values($biomedia);
		}
		foreach ($bio_media['tmp_name'] as $key_media => $biomedia) {
		    $bio_media_temp['tmp_name'][] = array_values($biomedia);
		}
		foreach ($bio_media['type'] as $key_media => $biomedia) {
		    $bio_media_temp['type'][] = array_values($biomedia);
		}
		foreach ($bio_media['error'] as $key_media => $biomedia) {
		    $bio_media_temp['error'][] = array_values($biomedia);
		}

		for ($k=0; $k < count($bio_media_temp) ; $k++) {

		    $uploads = $bio_media_temp['name'][$j];

		    $image_data = array();

		    if (isset($uploads[$k])) {

			if (!file_exists(BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/biodiversity/".$insert_bio_id)) {
			    mkdir(BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/biodiversity/".$insert_bio_id, 0777, true);
			    $biopath = BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/biodiversity/".$insert_bio_id;
			    chmod($biopath, 0755);
			}

			$config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
			$config['max_size']       = '2048';
			$config['maintain_ratio'] = true;
			$config['width']          = 140;
			$config['height']         = 100;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			$valid_formats = array("jpg", "jpeg", "gif", "png", "mp3", "mp4", "wma");

			$imagename     = $uploads[$k];
			$size = $bio_media_temp['size'][$j][$k];

			$cnt    = strrpos($imagename, ".");
			if (!$cnt) {
			    $ext = '';
			}
			$l              = strlen($imagename) - $cnt;
			$ext            = substr($imagename, $cnt + 1, $l);
			$bio_name       = 'bio_' . rand(11111, 9999999) . '.' . $ext;

			if ($ext) {
			    if (in_array($ext, $valid_formats)) {

				// procedure further if and only if image size can not be more than 10MB.
				if ($size < (1024 * 1024 * 10)) {

				    $uploadedfile = $bio_media_temp['tmp_name'][$j][$k];

				    $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/site_".$data['site_id']."/biodiversity/".$insert_bio_id."/".$bio_name;

				    $_movestatus  = move_uploaded_file($uploadedfile, $target_file);

				    if (!$_movestatus) {
					$this->theme->set_message('site image is not uploaded', 'error');
				    } else {

					$image_data['biodiversity_id'] = $insert_bio_id;
					$image_data['is_image']        = 1;
					$image_data['image']           = $bio_name;

					$this->db->insert($this->_tbl_csr_biodiversity_images, $image_data);
				    }
				} else {
				    $this->theme->set_message('site image size is too large', 'error');
				}
			    } else {
				$this->theme->set_message('site image extension is not .jpg or .png formate', 'error');
			    }
			}
		    }
		}
	    }
	}

	$difference = $measure_count - $inserted;

	$data_quarter  = $postdata['biodiversity_quarter'];

	// Save audit trail
	$data_action   = 'Create/Update';
	$additional_field = $data_quarter.' - '.$postdata['biodiversity_year'];
	saveAuditTrail($postdata['user_id'],$postdata['site_id'],'Utilities (Quarterly - Biodiversity) - ('.$additional_field.')',$data_action);

	if($difference == 0)
	{
	    $msg = $inserted." biodiversity(s) data saved successfully";
	    return $msg;
	}
	else
	{
	    $msg = $inserted." biodiversity(s) data saved successfully. Error in ".$difference." biodiversity(s) data, please fill it properly";
	    return $msg;
	}
	return true;
    }

    // get biodiversity data with images
    public function get_biodiversity_with_images()
    {
	$this->db->select('*');
	$this->db->from($this->_tbl_csr_biodiversity_data);
	$this->db->where('site_id', $this->site_id);
	$this->db->where('quarter', $this->utilities_quarter_quarter);
	$this->db->where('year', $this->utilities_quarter_year);
	$result      = $this->db->get();
	$resultArray = $result->result_array();

	$bio_data = array();

	if (!empty($resultArray)) {
	    foreach ($resultArray as $result) {

		$biodiversity_id = $result['id'];
		$bio_data[$biodiversity_id] = $result;

		$this->db->select('*');
		$this->db->from($this->_tbl_csr_biodiversity_images);
		$this->db->where('biodiversity_id', $biodiversity_id);
		$images = $this->db->get()->result_array();

		$bio_data[$biodiversity_id]['media'] = $images;
	    }
	}
	return $bio_data;
    }

    public function getLastMonthForSite($site_id) {
	$this->db->select(['month_id','year_id']);
	$this->db->from($this->_tbl_utilities);
	$this->db->where('site_id', $site_id);
	$this->db->where('total_electricity_kwh != ', 0);
	$this->db->where('year_id != ', 0);
	$this->db->where('month_id != ', 0);
    $this->db->order_by('year_id', 'DESC');
    $this->db->order_by('month_id', 'DESC');
    $this->db->order_by('id', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
	$result = $query->row();
	$result = json_decode(json_encode($result), true);
	$lastMonthUpdated = date('F', mktime(0, 0, 0, $result['month_id'])).' '.$result['year_id'];
	return $lastMonthUpdated;
    }
}