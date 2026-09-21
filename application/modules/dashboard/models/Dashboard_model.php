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

	public function get_action_notifications($site_id, $month_id, $year_id)
	{
		$site_id  = (int) $site_id;
		$month_id = (int) $month_id;
		$year_id  = (int) $year_id;

		$notifications = array();

		if ($site_id <= 0 || $month_id < 1 || $month_id > 12 || $year_id <= 0) {
			return $notifications;
		}
		//GET SITE + CURRENT MONTH DATA + LAST YEAR SAME MONTH

		$site = $this->db
			->where('id', $site_id)
			->where('status', 1)
			->get('sites')
			->row_array();

		if (empty($site)) {
			return $notifications;
		}

		// Get current month utility record

		$current = $this->db
			->where('site_id', $site_id)
			->where('month_id', $month_id)
			->where('year_id', $year_id)
			->order_by('id', 'DESC')
			->limit(1)
			->get('utilities_cost')
			->row_array();


		// Get last year's same month utility record

		$last_year = $this->db
			->where('site_id', $site_id)
			->where('month_id', $month_id)
			->where('year_id', $year_id - 1)
			->order_by('id', 'DESC')
			->limit(1)
			->get('utilities_cost')
			->row_array();


		$has_current_entry = !empty($current);


		/*
		*  MONTH NAME
		*/

		$month_names = array(
			1  => 'January',
			2  => 'February',
			3  => 'March',
			4  => 'April',
			5  => 'May',
			6  => 'June',
			7  => 'July',
			8  => 'August',
			9  => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December'
		);

		$report_period = $month_names[$month_id] . ' ' . $year_id;


		// UTILITY CONFIGURATION

		$utils = array(

			'electricity' => array(
				'raw'       => 'total_electricity_kwh',
				'cost'      => 'total_electricity_cost',
				'ly_raw'    => 'total_electricity_kwh',
				'ly_cost'   => 'total_electricity_cost',
				'label'     => 'Electricity',
				'show_flag' => 'show_utility_electricity'
			),

			'water' => array(
				'raw'       => 'water_total_consumption',
				'cost'      => 'water_total_consumption_cost',
				'ly_raw'    => 'water_total_consumption',
				'ly_cost'   => 'water_total_consumption_cost',
				'label'     => 'Water',
				'show_flag' => 'show_utility_water'
			),

			'fuel' => array(
				'raw'       => 'total_fuel_oil',
				'cost'      => 'total_fuel_oil_cost',
				'ly_raw'    => 'total_fuel_oil',
				'ly_cost'   => 'total_fuel_oil_cost',
				'label'     => 'Fuel',
				'show_flag' => 'show_utility_fuel_oil'
			),

			'lpg' => array(
				'raw'       => 'total_lpg',
				'cost'      => 'total_lpg_cost',
				'ly_raw'    => 'total_lpg',
				'ly_cost'   => 'total_lpg_cost',
				'label'     => 'LPG',
				'show_flag' => 'show_utility_lpg'
			),

			'natural_gas' => array(
				'raw'       => 'total_natural_gas',
				'cost'      => 'total_natural_gas_cost',
				'ly_raw'    => 'total_natural_gas',
				'ly_cost'   => 'total_natural_gas_cost',
				'label'     => 'Natural Gas',
				'show_flag' => 'show_utility_natural_gas'
			),

			'heating' => array(
				'raw'       => 'district_heating',
				'cost'      => 'district_heating_cost',
				'ly_raw'    => 'district_heating',
				'ly_cost'   => 'district_heating_cost',
				'label'     => 'Heating',
				'show_flag' => 'show_utility_district_heating'
			),

			'cooling' => array(
				'raw'       => 'district_cooling',
				'cost'      => 'district_cooling_cost',
				'ly_raw'    => 'district_cooling',
				'ly_cost'   => 'district_cooling_cost',
				'label'     => 'Cooling',
				'show_flag' => 'show_utility_district_cooling'
			)
		);


		//=====CHECK EACH ENABLED UTILITY
		foreach ($utils as $key => $utility) {

			// Utility must be enabled for this site

			$utility_enabled = isset($site[$utility['show_flag']]) && (int) $site[$utility['show_flag']] === 1;


			//If hotel/site has NOT selected this utility,

			if (!$utility_enabled) {
				continue;
			}

			// Current values

			$cur_raw = null;
			$cur_cost = null;

			if ($has_current_entry) {

				if (isset($current[$utility['raw']]) && $current[$utility['raw']] !== '' && $current[$utility['raw']] !== null) {
					$cur_raw = (float) $current[$utility['raw']];
				}

				if (isset($current[$utility['cost']]) && $current[$utility['cost']] !== '' && $current[$utility['cost']] !== null) {
					$cur_cost = (float) $current[$utility['cost']];
				}
			}


			// Last year values
			$ly_raw = null;
			$ly_cost = null;

			if (!empty($last_year)) {

				if (isset($last_year[$utility['ly_raw']]) &&  $last_year[$utility['ly_raw']] !== '' && $last_year[$utility['ly_raw']] !== null) {
					$ly_raw = (float) $last_year[$utility['ly_raw']];
				}

				if (isset($last_year[$utility['ly_cost']]) && $last_year[$utility['ly_cost']] !== '' && $last_year[$utility['ly_cost']] !== null) {
					$ly_cost = (float) $last_year[$utility['ly_cost']];
				}
			}


			//==== MISSING CURRENT DATA

			if (!$has_current_entry || $cur_raw === null || $cur_raw <= 0) {

			// 	$notifications[] = array(
			// 		'site_id'     => $site_id,
			// 		'site'        => $site['site_location_name'],
			// 		'period'      => $report_period,
			// 		'utility'     => $utility['label'],
			// 		'type'        => 'missing',
			// 		'message'     => 'Missing ' . $utility['label'] . ' Data'
			// 	);

			// 	// If current data is missing, don't calculate consumption/tariff variance.

				continue;
			}


			//===== CONSUMPTION YOY CHECK

			if ($ly_raw !== null && $ly_raw > 0) {

				$yoy_consumption = ($cur_raw - $ly_raw) / $ly_raw;
				if (abs($yoy_consumption) > 0.20) {
					$direction = ($yoy_consumption > 0)? 'increased': 'decreased';

					$percentage = round(abs($yoy_consumption) * 100);
					$notifications[] = array(
						'site_id'     => $site_id,
						'site'        => $site['site_location_name'],
						'period'      => $report_period,
						'utility'     => $utility['label'],
						'type'        => 'consumption',
						'direction'   => $direction,
						'percentage'  => $percentage,
						'message'     => $utility['label'] .' consumption ' .$direction .' by ' .$percentage .'% compared to last year, verify data'
					);
				}
			}
			//===  TARIFF YOY CHECK

			// $current_tariff = null;
			// $last_year_tariff = null;

			// if ($cur_raw > 0 && $cur_cost !== null && $cur_cost > 0) {
			// 	$current_tariff = $cur_cost / $cur_raw;
			// }
			// if ($ly_raw !== null && $ly_raw > 0 && $ly_cost !== null && $ly_cost > 0) {
			// 	$last_year_tariff = $ly_cost / $ly_raw;
			// }
			// if ($current_tariff !== null && $last_year_tariff !== null && $last_year_tariff > 0) {
			// 	$yoy_tariff = ($current_tariff - $last_year_tariff)/ $last_year_tariff;
			// 	if (abs($yoy_tariff) > 0.20) {
			// 		$direction = ($yoy_tariff > 0)? 'increased': 'decreased';

			// 		$percentage = round(abs($yoy_tariff) * 100);
			// 		$notifications[] = array(
			// 			'site_id'     => $site_id,
			// 			'site'        => $site['site_location_name'],
			// 			'period'      => $report_period,
			// 			'utility'     => $utility['label'],
			// 			'type'        => 'tariff',
			// 			'direction'   => $direction,
			// 			'percentage'  => $percentage,
			// 			'message'     => $utility['label'] .' tariff ' .$direction .' by ' .$percentage .'% compared to last year, verify data'
			// 		);
			// 	}
			// }
		}


		//====  WASTE CHECK

		// $waste_current_count = $this->db
		// 	->where('site_id', $site_id)
		// 	->where('month_id', $month_id)
		// 	->where('year_id', $year_id)
		// 	->where('deleted_at IS NULL', null, false)
		// 	->count_all_results('site_waste');


		// if ($waste_current_count == 0) {

		// 	$notifications[] = array(
		// 		'site_id' => $site_id,
		// 		'site'    => $site['site_location_name'],
		// 		'period'  => $report_period,
		// 		'utility' => 'Waste',
		// 		'type'    => 'missing',
		// 		'message' => 'Missing Waste Data'
		// 	);
		// }

		return $notifications;
	}
}

