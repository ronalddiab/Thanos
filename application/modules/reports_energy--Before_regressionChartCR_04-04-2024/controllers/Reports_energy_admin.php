<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reports_energy_admin extends Base_Admin_Controller {

    function __construct() {
	parent::__construct();
	$this->access_control($this->access_rules());
	$this->load->model('reports_energy_model');
	$this->load->model('utilities/utilities_model');
	$this->reports_energy_model->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
	$this->reports_energy_model->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
	$this->reports_energy_model->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;

	$this->load->library('form_validation');
	$this->language = $this->uri->segment(4);
    }

    private function access_rules() {
	return array(
	    array(
		'actions' => array('index'),
		'users' => array('@'),
	    ),array(
		'actions' => array('calculateRegression'),
		'users' => array('*'),
	    ), array(
		'actions' => array(''),
		'users' => array('*'),
	    )
	);
    }

    function index() {
	$data = array();
	$decimal_point = 4;
	$current_year = date('Y');

	$utility = 'electricity';
	$selected_year = $current_year;
	if ($this->input->post()) {
	    $utility = $this->input->post('utility');
	    $selected_year = $this->input->post('selected_year');
	}

	$utility_array = [
	    'electricity' => [
		'db_key' => 'total_electricity_kwh',
		'unit' => 'KWh',
		'Label' => 'Electricity',
	    ],
	    'fuel_oil' => [
		'db_key' => 'total_fuel_oil',
		'unit' => 'L',
		'Label' => 'Fuel Oil',
	    ],
	    'lpg' => [
		'db_key' => 'total_lpg',
		'unit' => 'Kg',
		'Label' => 'LPG',
	    ],
	    'water' => [
		'db_key' => 'water_total_consumption',
		'unit' => 'm3',
		'Label' => 'Water',
	    ],
	    'natural_gas' => [
		'db_key' => 'total_natural_gas',
		'unit' => 'm3',
		'Label' => 'Natural Gas',
	    ],
	    'district_cooling' => [
		'db_key' => 'district_cooling',
		'unit' => 'KWh',
		'Label' => 'District Cooling',
	    ],
	    'district_heating' => [
		'db_key' => 'district_heating',
		'unit' => 'KWh',
		'Label' => 'District Heating',
	    ],
	];

	$fullmontharray = array(
	    1 => 'January',
	    2 => 'February',
	    3 => 'March',
	    4 => 'April',
	    5 => 'May',
	    6 => 'June',
	    7 => 'July',
	    8 => 'August',
	    9 => 'September',
	    10 => 'October',
	    11 => 'November',
	    12 => 'December');

	$residuals = array();
	$percentage = array();

	$total_consumption = 0;
	$total_regression = 0;
	$total_consumption_cur = 0;
	$total_regression_cur = 0;
	$table_data = [];
	$table_data_cur = [];

	// Breadcrumb settings
	$this->breadcrumb->add('Reports', base_url() . $this->section_name . '/reports');
	$this->breadcrumb->add('Energy', base_url() . $this->section_name . '/reports_energy');

	//current site details
	$site_details = $this->reports_energy_model->getSiteDetails();
	$room_keys = $site_details['rooms_keys'];
	$baseline_regression_year = !empty($site_details['baseline_regression_year']) ? $site_details['baseline_regression_year'] : ($current_year - 1);

	/* ==============Previous year======================= */

	/*
	  //set Year as Previous year
	  $this->reports_energy_model->year_id = ($baseline_regression_year);

	  //get energy modeling data
	  $energy_modelling_data = $this->reports_energy_model->get_energy_modelling();
	  $utility_energy_modeling = $energy_modelling_data[$utility];
	 *
	 */

	//get utility consumption data
	$this->reports_energy_model->utilities_year = ($baseline_regression_year );
	$utilities = $this->reports_energy_model->getUtility();
	/* ===================================== */

	/* =================Current year==================== */
	//set Year as Current year
	$this->reports_energy_model->year_id = $current_year;

	//get energy modeling data
	$energy_modelling_data_cur = $this->reports_energy_model->get_energy_modelling();
	$utility_energy_modeling_cur = $energy_modelling_data_cur[$utility];

	//get utility consumption data
	$this->reports_energy_model->utilities_year = $selected_year;
	$utilities_cur = $this->reports_energy_model->getUtility();

	//set data for charts
	$energy_data_cur[0] = [
	    'Month',
	    $utility_array[$utility]['Label'] . ' - ' . ($selected_year),
	    'Regression - ' . ($selected_year)
	];
	/* ===================================== */

	//set data for charts
	$energy_data[0] = [
	    'Month',
	    $utility_array[$utility]['Label'] . ' - ' . ($baseline_regression_year ),
	    'Regression - ' . ($baseline_regression_year)
	];

	//calculation of residuals will be store in this array
	$residuals[$baseline_regression_year]['Month'] = "Variation (" . $utility_array[$utility]['unit'] . ")";
	$percentage[$baseline_regression_year]['Month'] = "Variation (%)";

	/*
	 * *********************************************************************
	 * formulas used
	 * *********************************************************************
	 *
	 * CDD,HDD,OCC -> consumption form monthly utilities
	 * cdd,hdd,occ,x -> consumption from site info
	 *
	 * Regression = x + (cdd * CDD) + (hdd * HDD) + (occ * OCC) + (days * $days_of_month);
	 * Occupacy = ((total_room_night / (rooms_keys * $days_of_month)) * 100);
	 * difference = Regression - consumption / consumption;
	 * variation% = (Regression - consumption / consumption) * 100;
	 * *********************************************************************
	 */

	foreach ($utilities as $utl) {

	    $consumtion = 0;
	    $regression = 0;

	    $days_of_month = (int) cal_days_in_month(CAL_GREGORIAN, $utl['month_id'], $utl['year_id']);
	    $consumtion = !empty($utl[$utility_array[$utility]['db_key']]) ? $utl[$utility_array[$utility]['db_key']] : 0;
	    $cdd = $utl['cdd'];
	    $hdd = $utl['hdd'];
	    $occupancy = round(($utl['total_room_night'] / ($room_keys * $days_of_month)), $decimal_point);

	    $regression = floatval($utility_energy_modeling_cur['x']) + (floatval($cdd) * floatval($utility_energy_modeling_cur['cdd'])) + (floatval($hdd) * floatval($utility_energy_modeling_cur['hdd'])) + (floatval($occupancy) * floatval($utility_energy_modeling_cur['occupancy'])) + (floatval($days_of_month) * floatval($utility_energy_modeling_cur['days']));


	    /* echo "<pre>";
	      echo $days_of_month;
	      echo $utility_energy_modeling_cur['x'].' + ('.$cdd.' * '.$utility_energy_modeling_cur['cdd'].') + ('.$hdd.' * '.$utility_energy_modeling_cur['hdd'].') + ('.$occupancy.' * '.$utility_energy_modeling_cur['occupancy'].')';
	      echo " = ";
	      echo $utility_energy_modeling_cur['x']
	      + ($cdd * $utility_energy_modeling_cur['cdd'])
	      + ($hdd * $utility_energy_modeling_cur['hdd'])
	      + ($occupancy * $utility_energy_modeling_cur['occupancy']);
	      echo "</pre>"; */

	    $total_consumption += $consumtion;
	    $total_regression += $regression;

	    $energy_data[] = [
		$fullmontharray[$utl['month_id']],

		is_finite($consumtion) ? round($consumtion) : 0,

		is_finite($regression) ? round($regression) : 0

	    ];

	    $residuals[$baseline_regression_year][$fullmontharray[$utl['month_id']]] = round($consumtion - $regression, $decimal_point);

	    if ($consumtion == 0) {
		$percent = 100;
		$percentage[$baseline_regression_year][$fullmontharray[$utl['month_id']]] = 100;
	    } else {
		$percent = round((($consumtion - $regression) / $consumtion) * 100, $decimal_point);
		$percentage[$baseline_regression_year][$fullmontharray[$utl['month_id']]] = round((($consumtion - $regression) / $consumtion) * 100, $decimal_point);
	    }

	    $table_data[$fullmontharray[$utl['month_id']]] = [
		'consumtion' => $consumtion,
		'regression' => $regression,
		'variation' => round($consumtion - $regression, $decimal_point),
		'precentage' => $percent
	    ];
	}

	/*
	 * *********************************************************************
	 * Get data for current Year
	 * *********************************************************************
	 */

	//calculation of residuals will be store in this array
	$residuals[$current_year]['Month'] = "Variation (" . $utility_array[$utility]['unit'] . ")";
	$percentage[$current_year]['Month'] = "Variation (%)";

	foreach ($utilities_cur as $utl) {

	    $consumtion = 0;
	    $regression = 0;

	    $days_of_month = (int) cal_days_in_month(CAL_GREGORIAN, $utl['month_id'], $utl['year_id']);
	    $consumtion = !empty($utl[$utility_array[$utility]['db_key']]) ? $utl[$utility_array[$utility]['db_key']] : 0;
	    $cdd = $utl['cdd'];
	    $hdd = $utl['hdd'];
	    $occupancy = round(($utl['total_room_night'] / ($room_keys * $days_of_month)), $decimal_point);
	    $regression = floatval($utility_energy_modeling_cur['x']) + (floatval($cdd) * floatval($utility_energy_modeling_cur['cdd'])) + (floatval($hdd) * floatval($utility_energy_modeling_cur['hdd'])) + (floatval($occupancy) * floatval($utility_energy_modeling_cur['occupancy'])) + (floatval($days_of_month) * floatval($utility_energy_modeling_cur['days']));

	    // Data only upto last month
	    if ($utl['month_id'] > (date('m') - 1) && $selected_year==$current_year) {
		$consumtion = 0;
		$regression = 0;
	    }

	    $total_consumption_cur += $consumtion;
	    $total_regression_cur += $regression;

	    $energy_data_cur[] = [
		$fullmontharray[$utl['month_id']],

		is_finite($consumtion) ? round($consumtion) : 0,

		is_finite($regression) ? round($regression) : 0

	    ];

	    $residuals[$current_year][$fullmontharray[$utl['month_id']]] = round($consumtion - $regression, $decimal_point);

	    if ($consumtion == 0) {
		$percent = 100;
		$percentage[$current_year][$fullmontharray[$utl['month_id']]] = 100;
	    } else {
		$percent = round((($consumtion - $regression) / $consumtion) * 100, $decimal_point);
		$percentage[$current_year][$fullmontharray[$utl['month_id']]] = round((($consumtion - $regression) / $consumtion) * 100, $decimal_point);
	    }

	    // Data only upto last month
	    if ($utl['month_id'] > (date('m') - 1)) {
		$percent = 0;
	    }

	    $table_data_cur[$fullmontharray[$utl['month_id']]] = [
		'consumtion' => $consumtion,
		'regression' => $regression,
		'variation' => round($consumtion - $regression, $decimal_point),
		'precentage' => $percent
	    ];
	}

	//data to pass to view
	$data['selected_year'] = $selected_year;
	$data['current_year'] = $current_year;
	$data['site_detail'] = $site_details;
	$data['utility'] = $utility;
	$data['energy_data'] = $energy_data;
	$data['energy_data_cur'] = $energy_data_cur;
	$data['residuals'] = $residuals;
	$data['percentage'] = $percentage;
	$data['utility_array'] = $utility_array;
	$data['baseline_regression_year'] = $baseline_regression_year;
//        $data['utility_energy_modeling'] = $utility_energy_modeling;
	$data['utility_energy_modeling_cur'] = $utility_energy_modeling_cur;
	$data['table_data'] = $table_data;
	$data['table_data_cur'] = $table_data_cur;

	$data['total_consumption'] = $total_consumption;
	$data['total_regression'] = $total_regression;
	$data['total_consumption_cur'] = $total_consumption_cur;
	$data['total_regression_cur'] = $total_regression_cur;

	//Create page-title
	$this->theme->set('page_title', 'Regression Analysis');
	//Render view
	$this->theme->view($data);
    }

    function calculateRegression() {

	$cdd = isset($_POST['cdd'])?$_POST['cdd']:0;
	$hdd = isset($_POST['hdd'])?$_POST['hdd']:0;
	$occ = isset($_POST['occ'])?$_POST['occ']:0;
	$days = isset($_POST['days'])?$_POST['days']:0;
	$fixCDD = isset($_POST['fixCDD'])?$_POST['fixCDD']:0;
	$fixHDD = isset($_POST['fixHDD'])?$_POST['fixHDD']:0;
	$fixOCC = isset($_POST['fixOCC'])?$_POST['fixOCC']:0;
	$fixDAYS = isset($_POST['fixDAYS'])?$_POST['fixDAYS']:0;
	$fixX = isset($_POST['fixX'])?$_POST['fixX']:0;

	$result = floatval($fixX) + (floatval($cdd) * floatval($fixCDD)) + (floatval($hdd) * floatval($fixHDD)) + (floatval($occ) * floatval($fixOCC)) + (floatval($days) * floatval($fixDAYS));

	echo $result;
    }

}
