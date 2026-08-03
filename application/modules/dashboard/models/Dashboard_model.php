<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Dashboard_model extends Base_Model {



    protected $_tbl_utilities = 'utilities_cost';

    protected $_tbl_site_custom_notifications = TBL_SITE_CUSTOM_NOTIFICATIONS;

    protected $_tbl_sites = 'sites';



    function __construct() {

	parent::__construct();

    }



    function getUtilityComparisionForLastMonth($filters = array()) {

	$where_more = "((u.year_id={$filters['pyear']} AND u.month_id={$filters['pmonth']}) OR (u.year_id={$filters['cyear']} AND u.month_id={$filters['cmonth']}))";

	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

			    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

			    COALESCE(u.total_lpg_cost, 0) as lpg,

			    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

			    COALESCE(u.district_heating_cost, 0) as heating_district,

			    COALESCE(u.district_cooling_cost, 0) as cooling_district,

			    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

			    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption_cost, 0) as water,

			    COALESCE(u.revenue, 0) as revenue,

			    u.month_id,

			    u.year_id,

			    COALESCE(u.total_room_night, 0) as total_room_night

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND $where_more

		    ORDER BY u.year_id ASC,u.month_id ASC";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function getUtilityComparisionForLastMonthWithForex($filters = array()) {
	if($this->site_id) {
	$where_more = "((u.year_id={$filters['pyear']} AND u.month_id={$filters['pmonth']}) OR (u.year_id={$filters['cyear']} AND u.month_id={$filters['cmonth']}))";

	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

			    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

			    COALESCE(u.total_lpg_cost, 0) as lpg,

			    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

			    COALESCE(u.district_heating_cost, 0) as heating_district,

			    COALESCE(u.district_cooling_cost, 0) as cooling_district,

			    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

			    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption_cost, 0) as water,

			    COALESCE(u.revenue, 0) as revenue,

			    u.month_id,

			    u.year_id,

			    COALESCE(u.total_room_night, 0) as total_room_night,

			    COALESCE(u.forex, 1) as forex

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND $where_more

		    ORDER BY u.year_id ASC,u.month_id ASC";



	$result = $this->db->query($query);

	$resultArray = $result->result_array();



	//fields to which forex multiplied

	$filed_array = [

	    'electricity',

	    'fuel',

	    'lpg',

	    'natural_gas',

	    'heating_district',

	    'cooling_district',

	    'district_heating_fixed_cost',

	    'district_cooling_fixed_cost',

	    'lpg_fixed_cost',

	    'natural_gas_fixed_cost',

	    'water_fixed_cost',

	    'water'

	];



	foreach ($resultArray as $key => $res) {
	    $forex = (isset($res['forex']) && $res['forex'] != 0) ? $res['forex'] : 1;
	    if (!empty($forex)) {

		foreach ($filed_array as $filed) {

		    if (array_key_exists($filed, $res)) {

			$res[$filed] *= $forex;

		    }

		}

		$resultArray[$key] = $res;

	    }

	}

	return $resultArray;
	} else {
	    return [];
	}
    }



    function getUtilityComparisionForLastMonthByUnit($filters = array()) {

	if($this->site_id) {
	$where_more = "((u.year_id={$filters['pyear']} AND u.month_id={$filters['pmonth']}) OR (u.year_id={$filters['cyear']} AND u.month_id={$filters['cmonth']}))";

	$query = "SELECT COALESCE(u.total_electricity_kwh, 0) as electricity,

			    COALESCE(u.total_fuel_oil, 0) as fuel,

			    COALESCE(u.total_lpg, 0) as lpg,

			    COALESCE(u.total_natural_gas, 0) as natural_gas,

			    COALESCE(u.district_heating, 0) as heating_district,

			    COALESCE(u.district_cooling, 0) as cooling_district,

			    COALESCE(u.water_total_consumption, 0) as water,

			    COALESCE(u.revenue, 0) as revenue,

			    u.month_id,

			    u.year_id,

			    COALESCE(u.total_room_night, 0) as total_room_night

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND $where_more

		    ORDER BY u.year_id ASC,u.month_id ASC";


	$result = $this->db->query($query);

	return $result->result_array();
	} else {
	    return [];
	}
    }



    function getSiteCustomNotifications($filterArray = array()) {

	$this->db->select("*");

	$this->db->from($this->_tbl_site_custom_notifications);

	$this->db->where('site_id', $this->site_id);



	if(!empty($filterArray)){

	    if(isset($filterArray['month']) && !empty($filterArray['month'])){

		$this->db->where('MONTH(date) =',$filterArray['month']);

	    }

	    $year = intval($filterArray['year']);

	    $lastyear = $year-1;

	    if(isset($filterArray['year']) && !empty($filterArray['year'])){

		if(($filterArray['start_month'] == 12) || ($filterArray['start_month'] == 0))

		{

		    $this->db->where('YEAR(date) in ('.$year.', '.$lastyear.')');

		}

		else

		{

		    $this->db->where('YEAR(date) =',$filterArray['year']);

		}

	    }

	    if(isset($filterArray['start_month']) && isset($filterArray['end_month']) && !empty($filterArray['start_month']) && !empty($filterArray['end_month'])){

		$start_month = $filterArray['start_month'];

		$end_month   = $filterArray['end_month'];

		if(($start_month == 12) || ($start_month == 0))

		{

		    $this->db->where('MONTH(date) in ('.$start_month.', '.$end_month.')');

		}

		else

		{

		    $this->db->where('MONTH(date) >=',$filterArray['start_month']);

		    $this->db->where('MONTH(date) <=',$filterArray['end_month']);

		}

	    }

	}

	$this->db->order_by('date','DESC');

	$result = $this->db->get();

	return $result->result_array();

    }

    function getUserSiteRegion($user_id) {
	$query = "SELECT sites.region_id FROM `users` LEFT JOIN `sites` ON sites.id = users.site_id WHERE users.`id` = {$user_id}";
	$result = $this->db->query($query);
	return $result->row_array();
    }
}

