<?php



if (!defined('BASEPATH')) {

    exit('No direct script access allowed');

}



class Quarterlycron_admin extends Base_Admin_Controller

{



    public function __construct()

    {

	/*ini_set('display_errors', 1);

	error_reporting(E_ALL);

	ini_set('max_execution_time', 900);*/



	parent::__construct();

	$this->load->model('sites/sites_model');

	$this->load->model('hotels/hotels_model');

	$this->load->model('users/users_model');

	// Login admin user

	$this->loginOnFly();

	$this->access_control($this->access_rules());

	$this->load->model('reportscron_model');

	$this->load->model('reportscron_forex_model');

	$this->load->model('utilities/utilities_model');

	$this->reportscron_model->user_id = isset($this->session->userdata['hep_cron_session']['user_id']) ? $this->session->userdata['hep_cron_session']['user_id'] : 0;

	$this->reportscron_model->role_id = isset($this->session->userdata['hep_cron_session']['role_id']) ? $this->session->userdata['hep_cron_session']['role_id'] : 0;

	$this->reportscron_model->site_id = isset($this->session->userdata['hep_cron_session']['site_id']) ? $this->session->userdata['hep_cron_session']['site_id'] : 0;



	$this->reportscron_forex_model->user_id = isset($this->session->userdata['hep_cron_session']['user_id']) ? $this->session->userdata['hep_cron_session']['user_id'] : 0;

	$this->reportscron_forex_model->role_id = isset($this->session->userdata['hep_cron_session']['role_id']) ? $this->session->userdata['hep_cron_session']['role_id'] : 0;

	$this->reportscron_forex_model->site_id = isset($this->session->userdata['hep_cron_session']['site_id']) ? $this->session->userdata['hep_cron_session']['site_id'] : 0;



	$this->load->library('form_validation');

	$this->language = $this->uri->segment(4);



	$this->hotel = $this->reportscron_model->getHotel();

	$this->myfile = '';



	// Get max current year's month id for chart



	if (date('m') == 1) {

	    define('CURRENT_YEAR_MAX_MONTH_ID', 12);

	} else {

	    define('CURRENT_YEAR_MAX_MONTH_ID', date('m') - 1);

	}



	define('CURRENT_CURRENCY', 'local');

    }



    private function access_rules()

    {

	return array(

	    array(

		'actions' => array('index'),

		'users' => array('*'),

	    ),

	);

    }



    public function index($cview = 'index')

    {

	$getData = $this->input->get();

	$report_post_type = $getData['type'];

	if ($report_post_type == 'annual') {

	    $report_post_type = 'annual';

	} else if ($report_post_type == 'mytd') {

	    $report_post_type = 'monthly_ytd';

	}



	if (isset($getData['ni']) && !empty($getData['ni'])) {

	    $site_id = $getData['ni'];

	} else {

	    $site_id = 0;

	}



	$siteCronSettings = $this->sites_model->getSiteCronSettings();

	$monthly = array();

	$annual = array();


	foreach ($siteCronSettings as $cronSettings) {

	    if ($cronSettings['site_cron_settings']['cron_type'] == 'MONTHLY') {

		array_push($monthly,  $cronSettings['site_cron_settings']['site_id']);

	    }



	    if ($cronSettings['site_cron_settings']['cron_type'] == 'ANNUAL') {

		array_push($annual,  $cronSettings['site_cron_settings']['site_id']);

	    }

	}

	$isMonthlyChecked = false;

	$isAnnualChecked = false;

	$isMonthlyChecked = in_array($site_id, $monthly);

	$isAnnualChecked = in_array($site_id, $annual);


	if ($report_post_type == 'monthly_ytd') {

	    if (!$isMonthlyChecked) {

		$this->logoutOnFly();

		echo '<script>

		    console.log("complete_pdf_cron");

		</script>';

		exit;

	    }

	}



	if ($report_post_type == 'annual') {

	    if (!$isAnnualChecked) {

		$this->logoutOnFly();

		echo '<script>

		    console.log("complete_pdf_cron");

		</script>';

		exit;

	    }

	}

	// Check if report assigned to any user or not

	$users = $this->reportscron_model->getUserDetails();

	$sites = $this->reportscron_model->get_site_listing_for_reports($site_id);

	$is_site_used = false;

	if (!empty($users)) {

	    foreach ($users as $user_value) {
		if (!empty($user_value['sites'])) {

		    if (array_key_exists($getData['ni'], $user_value['sites']) && in_array($report_post_type, $user_value['reports'])) {

			// $is_site_used = true;

			// check if quartely report checked

			if (in_array(QUARTERLY_REPORT, $user_value['reports'])) {

			    $fisrtSiteId = array_key_first ($user_value['sites']);

			    if ($fisrtSiteId != $site_id) {

				// saveBulkData(['siteId' => $site_id, 'fisrtSiteId' => $fisrtSiteId, 'first' => current($user_value['sites'])], "QUARTER_QUARTERLY_REPORT_".$user_value['id']);

				$is_site_used = true;

			    } else {

				$is_site_used = false;

			    }

			}

			break;

		    }

		}

	    }

	}



	if (!$is_site_used) {

	    echo '<script>

		console.log("complete_pdf_cron");

	    </script>';

	    exit;

	}

	// Check END



	register_shutdown_function(function ()

	{

	    $last_error = error_get_last();

	    if ($last_error['type'] === E_ERROR) {

		header('HTTP/1.1 500 Internal Server Error');

		exit;

	    }

	});



	$this->breadcrumb->add(lang('reports'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports');

	$sites = $this->reportscron_model->get_site_listing_for_reports($site_id);

	if (empty($sites)) {

	    header('HTTP/1.1 500 Internal Server Error');

	    exit;

	}

	$data = array();





	foreach ($sites as $key => $site) {

	    $this->session->userdata['hep_cron_session']['site_id'] = $site['id'];

	    $this->session->userdata['hep_cron_session']['local_currency'] = $site['local_currency'];

	    $this->reportscron_model->site_id = isset($this->session->userdata['hep_cron_session']['site_id']) ? $this->session->userdata['hep_cron_session']['site_id'] : 0;

	    $this->reportscron_forex_model->site_id = isset($this->session->userdata['hep_cron_session']['site_id']) ? $this->session->userdata['hep_cron_session']['site_id'] : 0;

	    $data['sites'][$key] = $this->indexCalculation($cview);

	    // $data['sites'][$key]['monthly'] = $this->indexCalculation($cview, true);



	    if (CURRENT_CURRENCY == "local") {

		if (!empty($site['local_currency'])) {

		    define('CURRENCY', "");

		    define('CURRENCY_SYMBOL', $site['local_currency']);



		    define('REPORT_CURRENCY', "");

		    define('REPORT_CURRENCY_SYMBOL', $site['local_currency']);



		} else {

		    define('CURRENCY', BASE_CURRENCY);

		    define('CURRENCY_SYMBOL', BASE_CURRENCY_SYMBOL);



		    define('REPORT_CURRENCY', BASE_CURRENCY);

		    define('REPORT_CURRENCY_SYMBOL', BASE_CURRENCY_SYMBOL);

		}

	    } else {

		define('CURRENCY', BASE_CURRENCY);

		define('CURRENCY_SYMBOL', BASE_CURRENCY_SYMBOL);



		define('REPORT_CURRENCY', BASE_CURRENCY);

		define('REPORT_CURRENCY_SYMBOL', BASE_CURRENCY_SYMBOL);

	    }

	}



	if ($this->input->post()) {

	    $data_get = $this->input->get();

	    $data_post = $this->input->post();

	    $chartData = array();

	    foreach ($sites as $key => $site) {

		$siteID = $site['id'];



		// Genrate image from post chart data

		$chartImagePostArray = array(

		    'columnChartImg',

		    'columnChartCarbonFootprintImg',

		    'columnChartCarbonFootprintMonthlyImg',

		    'columnChartCarbonFootprintAnnualImg',

		    'columnChartImg_hidden',

		    'columnChartImg_5years_hidden',

		    'columnChartImg_monthly',

		    'columnChartImg_monthly_month',

		    'pieChartImg',

		    'pieChartNewImg',

		    'pieChartImg_hidden',

		    'pieChartNewImg_hidden',

		    'pieChartNew2Img',

		    'pieChartNew3Img',

		    'wasteChartImg',

		    'wastePieChartImg',

		    'wasteLandfillPieChartImg',

		    'pieAnnualChartNewImg_hidden',

		    'pieAnnualLandfillImg_hidden',

		    'wasteMonthlyChartImg',

		    'wasteMonthlyChartImg_month',

		    'wastePieMonthlyChartImg',

		    'wastePieMonthlyChartImg_month',

		    'wastePieLandfillMonthlyChartImg',

		    'wasteChartPreImg_hidden',

		);



		foreach ($chartImagePostArray as $ichart) {

		    if (isset($data_post[$ichart . '_' . $siteID]) && !empty($data_post[$ichart . '_' . $siteID])) {

			$chartData[$siteID][$ichart] = $this->genrateImageFromBase64($data_post[$ichart . '_' . $siteID]);

		    } else {

			$chartData[$siteID][$ichart] = '';

		    }

		}

	    }



	    $data['chartData'] = $chartData;

	}



	$view_type = ($this->input->post()) ? $this->input->post('view_type') : '';

	if ($view_type == 'pdf') {



	    $files_array = array();

	    $files = array();

	    foreach ($data['sites'] as $key => $site) {

		$siteID = $site['site_detail']['id'];



		$site['chartData'] = $chartData[$siteID];

		$files['site_name'] = $site['site_detail']['site_location_name'];

		$files['site_id'] = $siteID;

		$files['files'] = $this->generate_report_pdf($site, $data_get['type']);

	    }

	    $this->sendMail($files);

	    $this->logoutOnFly();

	    echo '<script>

		console.log("complete_pdf_cron 23");

		console.log("complete_pdf_cron");

	    </script>';

	    echo "END";

	    exit;

	} else {

	    $this->logoutOnFly();

	    $this->theme->view($data, $cview_file);

	}

    }



    public function indexCalculation($cview = 'index', $monthly = false)

    {

	$data = array();

	$isLocal = true;

	if (CURRENT_CURRENCY == 'base') {

	    $isLocal = false;

	}

	$data['currency'] = CURRENT_CURRENCY;

	/**

	 * Set value based on user's selected report

	 */

	$data['reportForPost'] = 'annual_report_popup_btn_hidden';



	$data['CURRENT_YEAR_MAX_MONTH_ID'] = CURRENT_YEAR_MAX_MONTH_ID;

	$data['cview'] = $cview;

	$cview_file = 'admin_index';

		$site_id = $this->session->userdata['hep_cron_session']['site_id'];
		$dataFactor = getMmbtuFactorConversionAllUtility($site_id);


	// For last 12 months

	$startdate = date("m/Y", strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -11 months"));

	$enddate = date(CURRENT_YEAR_MAX_MONTH_ID . '/Y');



	$startdateexplode = explode('/', $startdate);

	$enddateexplode = explode('/', $enddate);



	$filters = array();

	$filters['startdate'] = (isset($startdate)) ? $startdate : '';

	$filters['enddate'] = (isset($enddate)) ? $enddate : '';

	$filters['max_month_id'] = CURRENT_YEAR_MAX_MONTH_ID;



	$filters['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

	$filters['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';



	$filters['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';

	$filters['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';



	$filters['current_month'] = (int) date('m');

	$filters['current_year'] = date('Y');



	$previousmonthdata = date("m/Y", strtotime(date('Y-m') . " -1 months"));

	$previousdateexplode = explode('/', $previousmonthdata);

	$filters['previous_month'] = (int) $previousdateexplode[0];

	$filters['previous_year'] = $previousdateexplode[1];



	// FIlters for comparisional bar chart

	$filters_comparision_chart = array();

	$startdate = '1/' . date('Y');

	$enddate = CURRENT_YEAR_MAX_MONTH_ID.'/' . date('Y');

	// Change filter for monthly report pdf // conditional

	if ($monthly) {

	    $startdate = $this->input->post('monthly_report_month', 1) . '/' . $this->input->post('monthly_report_year', date('Y'));

	    $enddate = $this->input->post('monthly_report_month', 12) . '/' . $this->input->post('monthly_report_year', date('Y'));

	}

	$startdateexplode = explode('/', $startdate);

	$enddateexplode = explode('/', $enddate);



	$filters_comparision_chart['startdate'] = (isset($startdate)) ? $startdate : '';

	$filters_comparision_chart['enddate'] = (isset($enddate)) ? $enddate : '';



	$filters_comparision_chart['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

	$filters_comparision_chart['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';

	$filters_comparision_chart['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';

	$filters_comparision_chart['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';


	if ($isLocal) {

	    $utility_cost_chart_results = $this->reportscron_model->utilityCostBarChart($filters_comparision_chart);

	} else {

	    $utility_cost_chart_results = $this->reportscron_forex_model->utilityCostBarChart($filters_comparision_chart);

	}

	if (!empty($utility_cost_chart_results)) {

	    //Check if data is empty for whole chart

	    $totalElectricity = 0;

	    $totalFuel = 0;

	    $totalLpg = 0;

	    $totalNaturalGas = 0;

	    $totalWater = 0;

	    $totalHeatingDistrict = 0;

	    $totalCoolingDistrict = 0;

	    $totalFuelConsumption = 0;

	    $totalLpgConsumption = 0;

	    $totalNaturalGasConsumption = 0;

	    $totalWaterConsumption = 0;

	    $totalHeatingDistrictConsumption = 0;

	    $totalCoolingDistrictConsumption = 0;



	    foreach ($utility_cost_chart_results as $key => $value) {

		/* check for empty value */

		$value['electricity'] = !empty($value['electricity']) ? $value['electricity'] : 0;

		$value['fuel'] = !empty($value['fuel']) ? $value['fuel'] : 0;

		$value['fuel_consumption'] = !empty($value['fuel_consumption']) ? $value['fuel_consumption'] : 0;

		$value['lpg'] = !empty($value['lpg']) ? $value['lpg'] : 0;

		$value['lpg_consumption'] = !empty($value['lpg_consumption']) ? $value['lpg_consumption'] : 0;

		$value['natural_gas'] = !empty($value['natural_gas']) ? $value['natural_gas'] : 0;

		$value['natural_gas_consumption'] = !empty($value['natural_gas_consumption']) ? $value['natural_gas_consumption'] : 0;

		$value['heating_district'] = !empty($value['heating_district']) ? $value['heating_district'] : 0;

		$value['heating_district_consumption'] = !empty($value['heating_district_consumption']) ? $value['heating_district_consumption'] : 0;

		$value['cooling_district'] = !empty($value['cooling_district']) ? $value['cooling_district'] : 0;

		$value['cooling_district_consumption'] = !empty($value['cooling_district_consumption']) ? $value['cooling_district_consumption'] : 0;

		$value['district_heating_fixed_cost'] = !empty($value['district_heating_fixed_cost']) ? $value['district_heating_fixed_cost'] : 0;

		$value['district_cooling_fixed_cost'] = !empty($value['district_cooling_fixed_cost']) ? $value['district_cooling_fixed_cost'] : 0;

		$value['water'] = !empty($value['water']) ? $value['water'] : 0;

		$value['water_consumption'] = !empty($value['water_consumption']) ? $value['water_consumption'] : 0;

		$value['onsite_generator'] = !empty($value['onsite_generator']) ? $value['onsite_generator'] : 0;

		$value['renewable_energy'] = !empty($value['renewable_energy']) ? $value['renewable_energy'] : 0;

		$value['cdd'] = !empty($value['cdd']) ? $value['cdd'] : 0;

		$value['hdd'] = !empty($value['hdd']) ? $value['hdd'] : 0;

		$value['total_budget'] = !empty($value['total_budget']) ? $value['total_budget'] : 0;

		$value['total_purchased_electricity'] = !empty($value['total_purchased_electricity']) ? $value['total_purchased_electricity'] : 0;

		$value['total_purchased_electricity_cost'] = !empty($value['total_purchased_electricity_cost']) ? $value['total_purchased_electricity_cost'] : 0;

		$value['total_electricity_kwh'] = !empty($value['total_electricity_kwh']) ? $value['total_electricity_kwh'] : 0;

		$value['total_room_night'] = !empty($value['total_room_night']) ? $value['total_room_night'] : 0;

		$value['rooms_keys'] = !empty($value['rooms_keys']) ? $value['rooms_keys'] : 0;

		/* check for empty value */



		$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];

		$value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];



		$totalElectricity += $value['electricity'];

		$totalFuel += $value['fuel'];

		$totalFuelConsumption += $value['fuel_consumption'];

		$totalLpg += $value['lpg'];

		$totalLpgConsumption += $value['lpg'];

		$totalNaturalGas += $value['natural_gas'];

		$totalNaturalGasConsumption += $value['natural_gas'];

		$totalWater += $value['water'];

		$totalWaterConsumption += $value['water'];

		$totalHeatingDistrict += $value['heating_district'];

		$totalHeatingDistrictConsumption += $value['heating_district'];

		$totalCoolingDistrict += $value['cooling_district'];

		$totalCoolingDistrictConsumption += $value['cooling_district'];



		switch ($cview) {

		    case 'roomnight':

			$cview_file = 'admin_index_roomnight';

			$data['utility_cost_chart']['utility_cost_chart_title'] = lang('utility-cost-chart-roomnight-title') . ' (' . $cur_symbol . ')';

			if (!empty($value['total_room_night'])) {

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? ($value['electricity'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? ($value['fuel'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? ($value['fuel_consumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? ($value['lpg'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpgconsumption'])) ? ($value['lpgconsumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? ($value['natural_gas'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gasral_gas_consumption'])) ? ($value['natural_gasral_gas_consumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? ($value['heating_district'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_districting_district_consumption'])) ? ($value['heating_districting_district_consumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? ($value['cooling_district'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_districting_district_consumption'])) ? ($value['cooling_districting_district_consumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? ($value['water'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['waterr_consumption'])) ? ($value['waterr_consumption'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? ($value['cdd'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? ($value['hdd'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? ($value['total_budget'] / $value['total_room_night']) : 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? ($value['total_electricity_kwh'] / $value['total_room_night']) : 0;



			    if (!empty($value['total_electricity_kwh'])) {

				$electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];

			    } else {

				$electricity_tariff_cost_per_kwh = 0;

			    }



			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? ($electricity_tariff_cost_per_kwh / $value['total_room_night']) : 0;

			} else {

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water_consumption'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cdd'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['hdd'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['budget'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_tariff'] = 0;

			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = 0;

			}

			break;



		    case 'budget':

			$cview_file = 'admin_index_budget';

			$data['utility_cost_chart']['utility_cost_chart_title'] = lang('utility-cost-chart-budget-title');

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? $value['total_budget'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;



			if (!empty($value['total_electricity_kwh'])) {

			    $electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];

			} else {

			    $electricity_tariff_cost_per_kwh = 0;

			}



			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;

			break;



		    default:

			$data['utility_cost_chart']['utility_cost_chart_title'] = lang('utility-cost-chart-budget-title');

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? $value['total_budget'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['onsite_generator'] = (!empty($value['onsite_generator'])) ? $value['onsite_generator'] : 0;

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['renewable_energy'] = (!empty($value['renewable_energy'])) ? $value['renewable_energy'] : 0;



			if (!empty($value['total_electricity_kwh'])) {

			    $electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];

			} else {

			    $electricity_tariff_cost_per_kwh = 0;

			}

			$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;

			break;

		}



		$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];

		$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];

		$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];

		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

		$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);

	    }

	    //Add total values in data array

	    $data['totalElectricity'] = $totalElectricity;

	    $data['totalFuel'] = $totalFuel;

	    $data['totalLpg'] = $totalLpg;

	    $data['totalNaturalGas'] = $totalNaturalGas;

	    $data['totalWater'] = $totalWater;

	    $data['totalHeatingDistrict'] = $totalHeatingDistrict;

	    $data['totalCoolingDistrict'] = $totalCoolingDistrict;

	} else {

	    $data['utility_cost_chart'] = array();

	}



	$filters['filters_comparision_chart'] = $filters_comparision_chart;



	/*         * *********************************************************************** */

	// For last 12 months from previous year (Only for PDF)

	$startdate_pre = date("m/Y", strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -11 months -1 years"));

	$enddate_pre = date('m/Y', strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -1 years"));



	$startdateexplode_pre = explode('/', $startdate_pre);

	$enddateexplode_pre = explode('/', $enddate_pre);



	$filters['startdate_pre'] = (isset($startdate_pre)) ? $startdate_pre : '';

	$filters['enddate_pre'] = (isset($enddate_pre)) ? $enddate_pre : '';

	$filters['max_month_id_pre'] = 12;



	$filters['start_month_pre'] = (isset($startdateexplode_pre[0])) ? (int) $startdateexplode_pre[0] : '';

	$filters['start_year_pre'] = (isset($startdateexplode_pre[1])) ? $startdateexplode_pre[1] : '';

	$filters['end_month_pre'] = (isset($enddateexplode_pre[0])) ? (int) $enddateexplode_pre[0] : '';

	$filters['end_year_pre'] = (isset($enddateexplode_pre[1])) ? $enddateexplode_pre[1] : '';



	$previousmonthdata_pre = date("m/Y", strtotime(date('Y-m') . " -1 months -1 years"));

	$previousdateexplode_pre = explode('/', $previousmonthdata_pre);

	$filters['previous_month_pre'] = (int) $previousdateexplode_pre[0];

	$filters['previous_year_pre'] = $previousdateexplode_pre[1];



	// FIlters for comparisional bar chart

	$filters_comparision_chart_pre = array();

	$startdate_pre = '1/' . date("Y", strtotime(date('Y') . " -1 years"));

	$enddate_pre = '12/' . date("Y", strtotime(date('Y') . " -1 years"));


	$startdateexplode_pre = explode('/', $startdate_pre);

	$enddateexplode_pre = explode('/', $enddate_pre);



	$filters_comparision_chart_pre['startdate'] = (isset($startdate_pre)) ? $startdate_pre : '';

	$filters_comparision_chart_pre['enddate'] = (isset($enddate_pre)) ? $enddate_pre : '';



	$filters_comparision_chart_pre['start_month'] = (isset($startdateexplode_pre[0])) ? (int) $startdateexplode_pre[0] : '';

	$filters_comparision_chart_pre['start_year'] = (isset($startdateexplode_pre[1])) ? $startdateexplode_pre[1] : '';

	$filters_comparision_chart_pre['end_month'] = (isset($enddateexplode_pre[0])) ? (int) $enddateexplode_pre[0] : '';

	$filters_comparision_chart_pre['end_year'] = (isset($enddateexplode_pre[1])) ? $enddateexplode_pre[1] : '';



	$currentYear = date('Y'); //date('Y');

	$currentMonth = intval(date('m'));

	if ($currentMonth == 1) {

	    $currentYear = $currentYear - 1;

	    $currentMonth = 12;

	}

	$filters_comparision_chart_pre['currentYear'] = $currentYear;

	$filters_comparision_chart_pre['currentMonth'] = $currentMonth;



	if ($isLocal) {

	    $utility_cost_chart_results_pre = $this->reportscron_model->utilityCostBarChart($filters_comparision_chart_pre);

	} else {

	    $utility_cost_chart_results_pre = $this->reportscron_forex_model->utilityCostBarChart($filters_comparision_chart_pre);

	}

	if (!empty($utility_cost_chart_results_pre)) {

	    //Check if data is empty for whole chart

	    $totalElectricity_utility_cost_pre = 0;

	    $totalFuel_utility_cost_pre = 0;

	    $totalLpg_utility_cost_pre = 0;

	    $totalNaturalGas_utility_cost_pre = 0;

	    $totalWater_utility_cost_pre = 0;

	    $totalHeatingDistrict_utility_cost_pre = 0;

	    $totalCoolingDistrict_utility_cost_pre = 0;



	    foreach ($utility_cost_chart_results_pre as $key => $value) {

		$value['cooling_district'] = !empty($value['cooling_district']) ? $value['cooling_district'] : 0;

		$value['heating_district'] = !empty($value['heating_district']) ? $value['heating_district'] : 0;

		$value['district_cooling_fixed_cost'] = !empty($value['district_cooling_fixed_cost']) ? $value['district_cooling_fixed_cost'] : 0;

		$value['district_heating_fixed_cost'] = !empty($value['district_heating_fixed_cost']) ? $value['district_heating_fixed_cost'] : 0;

		$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];

		$value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];



		$totalElectricity_utility_cost_pre += (!empty($value['electricity'])) ? $value['electricity'] : 0;

		$totalFuel_utility_cost_pre += (!empty($value['fuel'])) ? $value['fuel'] : 0;

		$totalLpg_utility_cost_pre += (!empty($value['lpg'])) ? $value['lpg'] : 0;

		$totalNaturalGas_utility_cost_pre += (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

		$totalWater_utility_cost_pre += (!empty($value['water'])) ? $value['water'] : 0;

		$totalHeatingDistrict_utility_cost_pre += (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

		$totalCoolingDistrict_utility_cost_pre += (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;



		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['electricity_consumption'])) ? $value['electricity_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['onsite_generator'] = (!empty($value['onsite_generator'])) ? $value['onsite_generator'] : 0;

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['renewable_energy'] = (!empty($value['renewable_energy'])) ? $value['renewable_energy'] : 0;



		if (!empty($value['total_electricity_kwh'])) {

		    $electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];

		} else {

		    $electricity_tariff_cost_per_kwh = 0;

		}

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;



		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

		$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);

	    }

	    //Add total values in data array

	    $data['totalElectricity_utility_cost_pre'] = $totalElectricity_utility_cost_pre;

	    $data['totalFuel_utility_cost_pre'] = $totalFuel_utility_cost_pre;

	    $data['totalLpg_utility_cost_pre'] = $totalLpg_utility_cost_pre;

	    $data['totalNaturalGas_utility_cost_pre'] = $totalNaturalGas_utility_cost_pre;

	    $data['totalWater_utility_cost_pre'] = $totalWater_utility_cost_pre;

	    $data['totalHeatingDistrict_utility_cost_pre'] = $totalHeatingDistrict_utility_cost_pre;

	    $data['totalCoolingDistrict_utility_cost_pre'] = $totalCoolingDistrict_utility_cost_pre;

	} else {

	    $data['utility_cost_chart_pre'] = array();

	}

	// KWH Pie chart for current year

	$filters_pre['report_year'] = (date('Y') - 1);

	$filters_pre['max_month_id'] = 12;



	$filters['report_year_pre'] = $filters_pre['report_year'];



	$kwh_report_results_pre = $this->reportscron_model->kwhUnitBasedReportForCurrentYear($filters_pre);

	if (!empty($kwh_report_results_pre)) {

	    $data['kwh_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? ($kwh_report_results_pre['electricity'] * $dataFactor['electricity']) : 0;

	    $data['kwh_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? ($kwh_report_results_pre['fuel'] * $dataFactor['fuel_oil']) : 0;

	    $data['kwh_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? ($kwh_report_results_pre['lpg'] * $dataFactor['lpg']) : 0;

	    $data['kwh_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? ($kwh_report_results_pre['natural_gas'] * $dataFactor['natural_gas']) : 0;

	    $data['kwh_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? ($kwh_report_results_pre['heating_district'] * $dataFactor['district_heating']) : 0;

	    $data['kwh_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? ($kwh_report_results_pre['cooling_district'] * $dataFactor['district_cooling']) : 0;

	} else {

	    $data['kwh_pie_chart_pre'] = array();

	}



	// Cost Pie chart for current year

	if ($isLocal) {

	    $kwh_report_results_pre = $this->reportscron_model->costBasedReportForCurrentYear($filters_pre);

	} else {

	    $kwh_report_results_pre = $this->reportscron_forex_model->costBasedReportForCurrentYear($filters_pre);

	}



	if (!empty($kwh_report_results_pre)) {



	    $kwh_report_results_pre['cooling_district'] = $kwh_report_results_pre['cooling_district'] + $kwh_report_results_pre['district_cooling_fixed_cost'];

	    $kwh_report_results_pre['heating_district'] = $kwh_report_results_pre['heating_district'] + $kwh_report_results_pre['district_heating_fixed_cost'];

	    // $kwh_report_results_pre['cooling_district'] = $kwh_report_results_pre['cooling_district'];

	    // $kwh_report_results_pre['heating_district'] = $kwh_report_results_pre['heating_district'];





	    $data['cost_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? $kwh_report_results_pre['electricity'] : 0;

	    $data['cost_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? $kwh_report_results_pre['fuel'] : 0;

	    $data['cost_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? $kwh_report_results_pre['lpg'] : 0;

	    $data['cost_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? $kwh_report_results_pre['natural_gas'] : 0;

	    $data['cost_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? $kwh_report_results_pre['heating_district'] : 0;

	    $data['cost_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? $kwh_report_results_pre['cooling_district'] : 0;

	    $data['cost_pie_chart_pre']['water'] = (!empty($kwh_report_results_pre['water'])) ? $kwh_report_results_pre['water'] : 0;

	} else {

	    $data['cost_pie_chart_pre'] = array();

	}



	$currentYear = date('Y'); //date('Y');

	$currentMonth = intval(date('m'));

	if ($currentMonth == 1) {

	    $currentYear = $currentYear - 1;

	    $currentMonth = 12;

	}

	$filters_pre['currentYear'] = $currentYear;

	$filters_pre['currentMonth'] = $currentMonth;

	$filters['filters_comparision_chart_pre'] = $filters_comparision_chart_pre;

	/*         * *********************************************************************** */

	if (date('m') == 1) {

	    $data['monthly_chart_data'] = $this->getMonthlyReportChart(12, (date('Y') - 1));

	} else {

	    $data['monthly_chart_data'] = $this->getMonthlyReportChart((date('m') - 1), date('Y'));

	}



	/*         * *********************************************************************** */

	// For last 5 Years (Only for PDF)

	$filters_comparision_chart_5years = array();



	$startdate_pre = '1/' . date("Y", strtotime(date('Y') . " -5 years"));

	$enddate_pre = '12/' . date("Y", strtotime(date('Y') . " -1 years"));



	$startdateexplode_5year = explode('/', $startdate_pre);

	$enddateexplode_5year = explode('/', $enddate_pre);



	$filters_comparision_chart_5years['start_month'] = (isset($startdateexplode_5year[0])) ? (int) $startdateexplode_5year[0] : '';

	$filters_comparision_chart_5years['start_year'] = (isset($startdateexplode_5year[1])) ? $startdateexplode_5year[1] : '';

	$filters_comparision_chart_5years['end_month'] = (isset($enddateexplode_5year[0])) ? (int) $enddateexplode_5year[0] : '';

	$filters_comparision_chart_5years['end_year'] = (isset($enddateexplode_5year[1])) ? $enddateexplode_5year[1] : '';



	if ($isLocal) {

	    $utility_cost_chart_results_5years = $this->reportscron_model->utilityCostBarChartByYears($filters_comparision_chart_5years);

	} else {

	    $utility_cost_chart_results_5years = $this->reportscron_forex_model->utilityCostBarChartByYears($filters_comparision_chart_5years);

	}



	if (!empty($utility_cost_chart_results_5years)) {

	    //Check if data is empty for whole chart

	    $totalElectricity_utility_cost_5years = 0;

	    $totalFuel_utility_cost_5years = 0;

	    $totalLpg_utility_cost_5years = 0;

	    $totalNaturalGas_utility_cost_5years = 0;

	    $totalWater_utility_cost_5years = 0;

	    $totalHeatingDistrict_utility_cost_5years = 0;

	    $totalCoolingDistrict_utility_cost_5years = 0;

	    foreach ($utility_cost_chart_results_5years as $key => $value) {



		$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];

		$value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];



		$data['utility_cost_chart_5years'][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;



		$data['utility_cost_chart_5years'][$value['year_id']]['electricity_unit'] = (!empty($value['electricity_unit'])) ? $value['electricity_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['fuel_unit'] = (!empty($value['fuel_unit'])) ? $value['fuel_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['lpg_unit'] = (!empty($value['lpg_unit'])) ? $value['lpg_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['natural_gas_unit'] = (!empty($value['natural_gas_unit'])) ? $value['natural_gas_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['heating_district_unit'] = (!empty($value['heating_district_unit'])) ? $value['heating_district_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['cooling_district_unit'] = (!empty($value['cooling_district_unit'])) ? $value['cooling_district_unit'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['water_unit'] = (!empty($value['water_unit'])) ? $value['water_unit'] : 0;



		$data['utility_cost_chart_5years'][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;

		$data['utility_cost_chart_5years'][$value['year_id']]['month_id'] = $value['month_id'];

		$data['utility_cost_chart_5years'][$value['year_id']]['year_id'] = $value['year_id'];

		$data['utility_cost_chart_5years'][$value['year_id']]['room_night'] = $value['total_room_night'];

		$data['utility_cost_chart_5years'][$value['year_id']]['occupancy'] = $value['occupancy'];

		$data['utility_cost_chart_5years'][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;



		if (!empty($data['utility_cost_chart_5years'][$value['year_id']]['total_electricity_kwh'])) {

		    $electricity_tariff_cost_per_kwh = $data['utility_cost_chart_5years'][$value['year_id']]['electricity'] / $data['utility_cost_chart_5years'][$value['year_id']]['total_electricity_kwh'];

		} else {

		    $electricity_tariff_cost_per_kwh = 0;

		}



		$data['utility_cost_chart_5years'][$value['year_id']]['electricity_tariff'] = $electricity_tariff_cost_per_kwh;



		$totalElectricity_utility_cost_5years += $value['electricity'];

		$totalFuel_utility_cost_5years += $value['fuel'];

		$totalLpg_utility_cost_5years += $value['lpg'];

		$totalNaturalGas_utility_cost_5years += $value['natural_gas'];

		$totalWater_utility_cost_5years += $value['water'];

		$totalHeatingDistrict_utility_cost_5years += $value['heating_district'];

		$totalCoolingDistrict_utility_cost_5years += $value['cooling_district'];

	    }

	    //Add total values in data array

	    $data['totalElectricity_utility_cost_5years'] = $totalElectricity_utility_cost_5years;

	    $data['totalFuel_utility_cost_5years'] = $totalFuel_utility_cost_5years;

	    $data['totalLpg_utility_cost_5years'] = $totalLpg_utility_cost_5years;

	    $data['totalNaturalGas_utility_cost_5years'] = $totalNaturalGas_utility_cost_5years;

	    $data['totalWater_utility_cost_5years'] = $totalWater_utility_cost_5years;

	    $data['totalHeatingDistrict_utility_cost_5years'] = $totalHeatingDistrict_utility_cost_5years;

	    $data['totalCoolingDistrict_utility_cost_5years'] = $totalCoolingDistrict_utility_cost_5years;

	} else {

	    $data['utility_cost_chart_5years'] = array();

	}

	/*         * *********************************************************************** */



	// KWH Pie chart for current year

	$filters['report_year'] = date('Y');

	$filters['report_month'] = date('m');

	$kwh_report_results = $this->reportscron_model->kwhUnitBasedReportForCurrentYear($filters);

	if (!empty($kwh_report_results)) {

	    $kwh_report_results = array_map('intval', $kwh_report_results);

	    // $kwh_report_results['cooling_district'] = $kwh_report_results['cooling_district'] + $kwh_report_results['district_cooling_fixed_cost'];

	    $kwh_report_results['cooling_district'] = $kwh_report_results['cooling_district'];

	    // $kwh_report_results['heating_district'] = $kwh_report_results['heating_district'] + $kwh_report_results['district_heating_fixed_cost'];

	    $kwh_report_results['heating_district'] = $kwh_report_results['heating_district'];



	    $data['kwh_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $dataFactor['electricity']) : 0;

	    $data['kwh_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $dataFactor['fuel_oil']) : 0;

	    $data['kwh_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $dataFactor['lpg']) : 0;

	    $data['kwh_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $dataFactor['natural_gas']) : 0;

	    $data['kwh_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $dataFactor['district_heating']) : 0;

	    $data['kwh_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $dataFactor['district_cooling']) : 0;

	} else {

	    $data['kwh_pie_chart'] = array();

	}



	// Cost Pie chart for current year

	if ($isLocal) {

	    $kwh_report_results = $this->reportscron_model->costBasedReportForCurrentYear($filters);

	} else {

	    $kwh_report_results = $this->reportscron_forex_model->costBasedReportForCurrentYear($filters);

	}



	if (!empty($kwh_report_results)) {

	    $data['cost_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? $kwh_report_results['electricity'] : 0;

	    $data['cost_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? $kwh_report_results['fuel'] : 0;

	    $data['cost_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? $kwh_report_results['lpg'] : 0;

	    $data['cost_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? $kwh_report_results['natural_gas'] : 0;

	    $data['cost_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? $kwh_report_results['heating_district'] : 0;

	    $data['cost_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? $kwh_report_results['cooling_district'] : 0;

	    $data['cost_pie_chart']['water'] = (!empty($kwh_report_results['water'])) ? $kwh_report_results['water'] : 0;

	} else {

	    $data['cost_pie_chart'] = array();

	}

	// KWh pie chart for last 12 months

	$kwh_report_results = $this->reportscron_model->kwhUnitBasedReportForPreviousMonth($filters);

	// $kwh_report_results = $this->reportscron_model->kwhUnitBasedReportForCurrentYearPieCharts($filters);

	if (!empty($kwh_report_results)) {

	    $data['kwh_pie_chart_previousmonth']['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $dataFactor['electricity']) : 0;

	    $data['kwh_pie_chart_previousmonth']['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $dataFactor['fuel_oil']) : 0;

	    $data['kwh_pie_chart_previousmonth']['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $dataFactor['lpg']) : 0;

	    $data['kwh_pie_chart_previousmonth']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $dataFactor['natural_gas']) : 0;

	    $data['kwh_pie_chart_previousmonth']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $dataFactor['district_heating']) : 0;

	    $data['kwh_pie_chart_previousmonth']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $dataFactor['district_cooling']) : 0;

	} else {

	    $data['kwh_pie_chart_previousmonth'] = array();

	}



	// Cost pie chart for last 12 months

	if ($isLocal) {

	    $kwh_report_results = $this->reportscron_model->costBasedReportForPreviousMonth($filters);

	} else {

	    $kwh_report_results = $this->reportscron_forex_model->costBasedReportForPreviousMonth($filters);

	}



	if (!empty($kwh_report_results)) {

			$kwh_report_results = array_map('floatval', $kwh_report_results);

	    $kwh_report_results['cooling_district'] = $kwh_report_results['cooling_district'] + $kwh_report_results['district_cooling_fixed_cost'];

	    $kwh_report_results['heating_district'] = $kwh_report_results['heating_district'] + $kwh_report_results['district_heating_fixed_cost'];



	    $data['cost_pie_chart_previousmonth']['electricity'] = (!empty($kwh_report_results['electricity'])) ? $kwh_report_results['electricity'] : 0;

	    $data['cost_pie_chart_previousmonth']['fuel'] = (!empty($kwh_report_results['fuel'])) ? $kwh_report_results['fuel'] : 0;

	    $data['cost_pie_chart_previousmonth']['lpg'] = (!empty($kwh_report_results['lpg'])) ? $kwh_report_results['lpg'] : 0;

	    $data['cost_pie_chart_previousmonth']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? $kwh_report_results['natural_gas'] : 0;

	    $data['cost_pie_chart_previousmonth']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? $kwh_report_results['heating_district'] : 0;

	    $data['cost_pie_chart_previousmonth']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? $kwh_report_results['cooling_district'] : 0;

	    $data['cost_pie_chart_previousmonth']['water'] = (!empty($kwh_report_results['water'])) ? $kwh_report_results['water'] : 0;

	} else {

	    $data['cost_pie_chart_previousmonth'] = array();

	}



	// Budget vs Actual data of Current Month, YTD and Annual

	// for ytd

	$start_year = date('Y');

	$start_month = 1;

	$end_year = date('Y');

	$end_month = intval(date('m')) - 1;

	$current_budget_actual_data = [];



	$filter_budget_actual_comparision_ytd = array(

	    'site_id' => $this->session->userdata['hep_cron_session']['site_id'],

	    'start_month' => $start_month,

	    'end_month' => $end_month,

	    'start_year' => $start_year,

	    'end_year' => $end_year,

	);



	if ($isLocal) {

	    $current_budget_actual_array_ytd = $this->reportscron_model->getUtilityActualBudgetData($filter_budget_actual_comparision_ytd);

	} else {

	    $current_budget_actual_array_ytd = $this->reportscron_forex_model->getUtilityActualBudgetData($filter_budget_actual_comparision_ytd);

	}



	foreach ($current_budget_actual_array_ytd as $key => $value) {



	    /* Check for empty */

	    $value['hdd'] = !empty($value['hdd']) ? $value['hdd'] : 0;

	    $value['cdd'] = !empty($value['cdd']) ? $value['cdd'] : 0;

	    $value['total_room_night'] = !empty($value['total_room_night']) ? $value['total_room_night'] : 0;

	    $value['district_cooling_actual'] = !empty($value['district_cooling_actual']) ? $value['district_cooling_actual'] : 0;

	    $value['district_cooling_budget'] = !empty($value['district_cooling_budget']) ? $value['district_cooling_budget'] : 0;

	    $value['district_cooling_cost_actual'] = !empty($value['district_cooling_cost_actual']) ? $value['district_cooling_cost_actual'] : 0;

	    $value['district_cooling_cost_budget'] = !empty($value['district_cooling_cost_budget']) ? $value['district_cooling_cost_budget'] : 0;

	    $value['district_heating_actual'] = !empty($value['district_heating_actual']) ? $value['district_heating_actual'] : 0;

	    $value['district_heating_budget'] = !empty($value['district_heating_budget']) ? $value['district_heating_budget'] : 0;

	    $value['district_heating_cost_actual'] = !empty($value['district_heating_cost_actual']) ? $value['district_heating_cost_actual'] : 0;

	    $value['district_heating_cost_budget'] = !empty($value['district_heating_cost_budget']) ? $value['district_heating_cost_budget'] : 0;

	    $value['total_electricity_kwh_actual'] = !empty($value['total_electricity_kwh_actual']) ? $value['total_electricity_kwh_actual'] : 0;

	    $value['total_electricity_kwh_budget'] = !empty($value['total_electricity_kwh_budget']) ? $value['total_electricity_kwh_budget'] : 0;

	    $value['total_electricity_cost_actual'] = !empty($value['total_electricity_cost_actual']) ? $value['total_electricity_cost_actual'] : 0;

	    $value['total_electricity_cost_budget'] = !empty($value['total_electricity_cost_budget']) ? $value['total_electricity_cost_budget'] : 0;

	    $value['total_fuel_oil_actual'] = !empty($value['total_fuel_oil_actual']) ? $value['total_fuel_oil_actual'] : 0;

	    $value['total_fuel_oil_budget'] = !empty($value['total_fuel_oil_budget']) ? $value['total_fuel_oil_budget'] : 0;

	    $value['total_fuel_oil_cost_actual'] = !empty($value['total_fuel_oil_cost_actual']) ? $value['total_fuel_oil_cost_actual'] : 0;

	    $value['total_fuel_oil_cost_budget'] = !empty($value['total_fuel_oil_cost_budget']) ? $value['total_fuel_oil_cost_budget'] : 0;

	    $value['total_lpg_actual'] = !empty($value['total_lpg_actual']) ? $value['total_lpg_actual'] : 0;

	    $value['total_lpg_budget'] = !empty($value['total_lpg_budget']) ? $value['total_lpg_budget'] : 0;

	    $value['total_lpg_cost_actual'] = !empty($value['total_lpg_cost_actual']) ? $value['total_lpg_cost_actual'] : 0;

	    $value['total_lpg_cost_budget'] = !empty($value['total_lpg_cost_budget']) ? $value['total_lpg_cost_budget'] : 0;

	    $value['total_natural_gas_actual'] = !empty($value['total_natural_gas_actual']) ? $value['total_natural_gas_actual'] : 0;

	    $value['total_natural_gas_budget'] = !empty($value['total_natural_gas_budget']) ? $value['total_natural_gas_budget'] : 0;

	    $value['total_natural_gas_cost_actual'] = !empty($value['total_natural_gas_cost_actual']) ? $value['total_natural_gas_cost_actual'] : 0;

	    $value['total_natural_gas_cost_budget'] = !empty($value['total_natural_gas_cost_budget']) ? $value['total_natural_gas_cost_budget'] : 0;

	    $value['water_total_consumption_actual'] = !empty($value['water_total_consumption_actual']) ? $value['water_total_consumption_actual'] : 0;

	    $value['water_total_consumption_budget'] = !empty($value['water_total_consumption_budget']) ? $value['water_total_consumption_budget'] : 0;

	    $value['water_total_consumption_cost_actual'] = !empty($value['water_total_consumption_cost_actual']) ? $value['water_total_consumption_cost_actual'] : 0;

	    $value['water_total_consumption_cost_budget'] = !empty($value['water_total_consumption_cost_budget']) ? $value['water_total_consumption_cost_budget'] : 0;

	    /* Check for empty */



	    $current_budget_actual_data['ytd']['total_room_night'] += $value["total_room_night"];

	    $current_budget_actual_data['ytd']['hdd'] += $value['hdd'];

	    $current_budget_actual_data['ytd']['cdd'] += $value['cdd'];

	    $current_budget_actual_data['ytd']["district_cooling_actual"] += $value["district_cooling_actual"];

	    $current_budget_actual_data['ytd']["district_cooling_budget"] += $value["district_cooling_budget"];

	    $current_budget_actual_data['ytd']["district_cooling_cost_actual"] += $value["district_cooling_cost_actual"];

	    $current_budget_actual_data['ytd']["district_cooling_cost_budget"] += $value["district_cooling_cost_budget"];

	    $current_budget_actual_data['ytd']["district_heating_actual"] += $value["district_heating_actual"];

	    $current_budget_actual_data['ytd']["district_heating_budget"] += $value["district_heating_budget"];

	    $current_budget_actual_data['ytd']["district_heating_cost_actual"] += $value["district_heating_cost_actual"];

	    $current_budget_actual_data['ytd']["district_heating_cost_budget"] += $value["district_heating_cost_budget"];

	    $current_budget_actual_data['ytd']["total_electricity_kwh_actual"] += $value["total_electricity_kwh_actual"];

	    $current_budget_actual_data['ytd']["total_electricity_kwh_budget"] += $value["total_electricity_kwh_budget"];

	    $current_budget_actual_data['ytd']["total_electricity_cost_actual"] += $value["total_electricity_cost_actual"];

	    $current_budget_actual_data['ytd']["total_electricity_cost_budget"] += $value["total_electricity_cost_budget"];

	    $current_budget_actual_data['ytd']["total_fuel_oil_actual"] += $value["total_fuel_oil_actual"];

	    $current_budget_actual_data['ytd']["total_fuel_oil_budget"] += $value["total_fuel_oil_budget"];

	    $current_budget_actual_data['ytd']["total_fuel_oil_cost_actual"] += $value["total_fuel_oil_cost_actual"];

	    $current_budget_actual_data['ytd']["total_fuel_oil_cost_budget"] += $value["total_fuel_oil_cost_budget"];

	    $current_budget_actual_data['ytd']["total_lpg_actual"] += $value["total_lpg_actual"];

	    $current_budget_actual_data['ytd']["total_lpg_budget"] += $value["total_lpg_budget"];

	    $current_budget_actual_data['ytd']["total_lpg_cost_actual"] += $value["total_lpg_cost_actual"];

	    $current_budget_actual_data['ytd']["total_lpg_cost_budget"] += $value["total_lpg_cost_budget"];

	    $current_budget_actual_data['ytd']["total_natural_gas_actual"] += $value["total_natural_gas_actual"];

	    $current_budget_actual_data['ytd']["total_natural_gas_budget"] += $value["total_natural_gas_budget"];

	    $current_budget_actual_data['ytd']["total_natural_gas_cost_actual"] += $value["total_natural_gas_cost_actual"];

	    $current_budget_actual_data['ytd']["total_natural_gas_cost_budget"] += $value["total_natural_gas_cost_budget"];

	    $current_budget_actual_data['ytd']["water_total_consumption_actual"] += $value["water_total_consumption_actual"];

	    $current_budget_actual_data['ytd']["water_total_consumption_budget"] += $value["water_total_consumption_budget"];

	    $current_budget_actual_data['ytd']["water_total_consumption_cost_actual"] += $value["water_total_consumption_cost_actual"];

	    $current_budget_actual_data['ytd']["water_total_consumption_cost_budget"] += $value["water_total_consumption_cost_budget"];

	}



	//for monthly

	if (date('m') == 1) {

	    $start_month = ($this->input->post('monthly_report_month')) ? $this->input->post('monthly_report_month') : 12;

	    $start_year = ($this->input->post('monthly_report_year')) ? $this->input->post('monthly_report_year') : (date('Y') - 1);

	} else {

	    $start_month = ($this->input->post('monthly_report_month')) ? $this->input->post('monthly_report_month') : date('m') - 1;

	    $start_year = ($this->input->post('monthly_report_year')) ? $this->input->post('monthly_report_year') : date('Y');

	}



	$end_month = $start_month;

	$end_year = $start_year;



	$filter_budget_actual_comparision_monthly = array(

	    'site_id' => $this->session->userdata['hep_cron_session']['site_id'],

	    'start_month' => $start_month,

	    'end_month' => $end_month,

	    'start_year' => $start_year,

	    'end_year' => $end_year,

	);





	if ($isLocal) {

	    $current_budget_actual_array_monthly = $this->reportscron_model->getUtilityActualBudgetData($filter_budget_actual_comparision_monthly);

	} else {

	    $current_budget_actual_array_monthly = $this->reportscron_forex_model->getUtilityActualBudgetData($filter_budget_actual_comparision_monthly);

	}



	foreach ($current_budget_actual_array_monthly as $key => $value) {



	    /* Check for empty */

	    $value['hdd'] = !empty($value['hdd']) ? $value['hdd'] : 0;

	    $value['cdd'] = !empty($value['cdd']) ? $value['cdd'] : 0;

	    $value['total_room_night'] = !empty($value['total_room_night']) ? $value['total_room_night'] : 0;

	    $value['district_cooling_actual'] = !empty($value['district_cooling_actual']) ? $value['district_cooling_actual'] : 0;

	    $value['district_cooling_budget'] = !empty($value['district_cooling_budget']) ? $value['district_cooling_budget'] : 0;

	    $value['district_cooling_cost_actual'] = !empty($value['district_cooling_cost_actual']) ? $value['district_cooling_cost_actual'] : 0;

	    $value['district_cooling_cost_budget'] = !empty($value['district_cooling_cost_budget']) ? $value['district_cooling_cost_budget'] : 0;

	    $value['district_heating_actual'] = !empty($value['district_heating_actual']) ? $value['district_heating_actual'] : 0;

	    $value['district_heating_budget'] = !empty($value['district_heating_budget']) ? $value['district_heating_budget'] : 0;

	    $value['district_heating_cost_actual'] = !empty($value['district_heating_cost_actual']) ? $value['district_heating_cost_actual'] : 0;

	    $value['district_heating_cost_budget'] = !empty($value['district_heating_cost_budget']) ? $value['district_heating_cost_budget'] : 0;

	    $value['total_electricity_kwh_actual'] = !empty($value['total_electricity_kwh_actual']) ? $value['total_electricity_kwh_actual'] : 0;

	    $value['total_electricity_kwh_budget'] = !empty($value['total_electricity_kwh_budget']) ? $value['total_electricity_kwh_budget'] : 0;

	    $value['total_electricity_cost_actual'] = !empty($value['total_electricity_cost_actual']) ? $value['total_electricity_cost_actual'] : 0;

	    $value['total_electricity_cost_budget'] = !empty($value['total_electricity_cost_budget']) ? $value['total_electricity_cost_budget'] : 0;

	    $value['total_fuel_oil_actual'] = !empty($value['total_fuel_oil_actual']) ? $value['total_fuel_oil_actual'] : 0;

	    $value['total_fuel_oil_budget'] = !empty($value['total_fuel_oil_budget']) ? $value['total_fuel_oil_budget'] : 0;

	    $value['total_fuel_oil_cost_actual'] = !empty($value['total_fuel_oil_cost_actual']) ? $value['total_fuel_oil_cost_actual'] : 0;

	    $value['total_fuel_oil_cost_budget'] = !empty($value['total_fuel_oil_cost_budget']) ? $value['total_fuel_oil_cost_budget'] : 0;

	    $value['total_lpg_actual'] = !empty($value['total_lpg_actual']) ? $value['total_lpg_actual'] : 0;

	    $value['total_lpg_budget'] = !empty($value['total_lpg_budget']) ? $value['total_lpg_budget'] : 0;

	    $value['total_lpg_cost_actual'] = !empty($value['total_lpg_cost_actual']) ? $value['total_lpg_cost_actual'] : 0;

	    $value['total_lpg_cost_budget'] = !empty($value['total_lpg_cost_budget']) ? $value['total_lpg_cost_budget'] : 0;

	    $value['total_natural_gas_actual'] = !empty($value['total_natural_gas_actual']) ? $value['total_natural_gas_actual'] : 0;

	    $value['total_natural_gas_budget'] = !empty($value['total_natural_gas_budget']) ? $value['total_natural_gas_budget'] : 0;

	    $value['total_natural_gas_cost_actual'] = !empty($value['total_natural_gas_cost_actual']) ? $value['total_natural_gas_cost_actual'] : 0;

	    $value['total_natural_gas_cost_budget'] = !empty($value['total_natural_gas_cost_budget']) ? $value['total_natural_gas_cost_budget'] : 0;

	    $value['water_total_consumption_actual'] = !empty($value['water_total_consumption_actual']) ? $value['water_total_consumption_actual'] : 0;

	    $value['water_total_consumption_budget'] = !empty($value['water_total_consumption_budget']) ? $value['water_total_consumption_budget'] : 0;

	    $value['water_total_consumption_cost_actual'] = !empty($value['water_total_consumption_cost_actual']) ? $value['water_total_consumption_cost_actual'] : 0;

	    $value['water_total_consumption_cost_budget'] = !empty($value['water_total_consumption_cost_budget']) ? $value['water_total_consumption_cost_budget'] : 0;

	    /* Check for empty */



	    $current_budget_actual_data['monthly']['total_room_night'] += $value["total_room_night"];

	    $current_budget_actual_data['monthly']['hdd'] += $value['hdd'];

	    $current_budget_actual_data['monthly']['cdd'] += $value['cdd'];

	    $current_budget_actual_data['monthly']["district_cooling_actual"] += $value["district_cooling_actual"];

	    $current_budget_actual_data['monthly']["district_cooling_budget"] += $value["district_cooling_budget"];

	    $current_budget_actual_data['monthly']["district_cooling_cost_actual"] += $value["district_cooling_cost_actual"];

	    $current_budget_actual_data['monthly']["district_cooling_cost_budget"] += $value["district_cooling_cost_budget"];

	    $current_budget_actual_data['monthly']["district_heating_actual"] += $value["district_heating_actual"];

	    $current_budget_actual_data['monthly']["district_heating_budget"] += $value["district_heating_budget"];

	    $current_budget_actual_data['monthly']["district_heating_cost_actual"] += $value["district_heating_cost_actual"];

	    $current_budget_actual_data['monthly']["district_heating_cost_budget"] += $value["district_heating_cost_budget"];

	    $current_budget_actual_data['monthly']["total_electricity_kwh_actual"] += $value["total_electricity_kwh_actual"];

	    $current_budget_actual_data['monthly']["total_electricity_kwh_budget"] += $value["total_electricity_kwh_budget"];

	    $current_budget_actual_data['monthly']["total_electricity_cost_actual"] += $value["total_electricity_cost_actual"];

	    $current_budget_actual_data['monthly']["total_electricity_cost_budget"] += $value["total_electricity_cost_budget"];

	    $current_budget_actual_data['monthly']["total_fuel_oil_actual"] += $value["total_fuel_oil_actual"];

	    $current_budget_actual_data['monthly']["total_fuel_oil_budget"] += $value["total_fuel_oil_budget"];

	    $current_budget_actual_data['monthly']["total_fuel_oil_cost_actual"] += $value["total_fuel_oil_cost_actual"];

	    $current_budget_actual_data['monthly']["total_fuel_oil_cost_budget"] += $value["total_fuel_oil_cost_budget"];

	    $current_budget_actual_data['monthly']["total_lpg_actual"] += $value["total_lpg_actual"];

	    $current_budget_actual_data['monthly']["total_lpg_budget"] += $value["total_lpg_budget"];

	    $current_budget_actual_data['monthly']["total_lpg_cost_actual"] += $value["total_lpg_cost_actual"];

	    $current_budget_actual_data['monthly']["total_lpg_cost_budget"] += $value["total_lpg_cost_budget"];

	    $current_budget_actual_data['monthly']["total_natural_gas_actual"] += $value["total_natural_gas_actual"];

	    $current_budget_actual_data['monthly']["total_natural_gas_budget"] += $value["total_natural_gas_budget"];

	    $current_budget_actual_data['monthly']["total_natural_gas_cost_actual"] += $value["total_natural_gas_cost_actual"];

	    $current_budget_actual_data['monthly']["total_natural_gas_cost_budget"] += $value["total_natural_gas_cost_budget"];

	    $current_budget_actual_data['monthly']["water_total_consumption_actual"] += $value["water_total_consumption_actual"];

	    $current_budget_actual_data['monthly']["water_total_consumption_budget"] += $value["water_total_consumption_budget"];

	    $current_budget_actual_data['monthly']["water_total_consumption_cost_actual"] += $value["water_total_consumption_cost_actual"];

	    $current_budget_actual_data['monthly']["water_total_consumption_cost_budget"] += $value["water_total_consumption_cost_budget"];

	}



	//for annual

	$start_year = date('Y') - 1;

	$start_month = 1;

	$end_year = $start_year;

	$end_month = 12;



	$filter_budget_actual_comparision_annual = array(

	    'site_id' => $this->session->userdata['hep_cron_session']['site_id'],

	    'start_month' => $start_month,

	    'end_month' => $end_month,

	    'start_year' => $start_year,

	    'end_year' => $end_year,

	);



	if ($isLocal) {

	    $current_budget_actual_array_annual = $this->reportscron_model->getUtilityActualBudgetData($filter_budget_actual_comparision_annual);

	} else {

	    $current_budget_actual_array_annual = $this->reportscron_forex_model->getUtilityActualBudgetData($filter_budget_actual_comparision_annual);

	}



	foreach ($current_budget_actual_array_annual as $key => $value) {

		// change empty value to 0

		$value = array_map('floatval', $value);



	    $current_budget_actual_data['annual']['total_room_night'] += $value["total_room_night"];

	    $current_budget_actual_data['annual']['hdd'] += $value['hdd'];

	    $current_budget_actual_data['annual']['cdd'] += $value['cdd'];

	    $current_budget_actual_data['annual']["district_cooling_actual"] += $value["district_cooling_actual"];

	    $current_budget_actual_data['annual']["district_cooling_budget"] += $value["district_cooling_budget"];

	    $current_budget_actual_data['annual']["district_cooling_cost_actual"] += $value["district_cooling_cost_actual"];

	    $current_budget_actual_data['annual']["district_cooling_cost_budget"] += $value["district_cooling_cost_budget"];

	    $current_budget_actual_data['annual']["district_heating_actual"] += $value["district_heating_actual"];

	    $current_budget_actual_data['annual']["district_heating_budget"] += $value["district_heating_budget"];

	    $current_budget_actual_data['annual']["district_heating_cost_actual"] += $value["district_heating_cost_actual"];

	    $current_budget_actual_data['annual']["district_heating_cost_budget"] += $value["district_heating_cost_budget"];

	    $current_budget_actual_data['annual']["total_electricity_kwh_actual"] += $value["total_electricity_kwh_actual"];

	    $current_budget_actual_data['annual']["total_electricity_kwh_budget"] += $value["total_electricity_kwh_budget"];

	    $current_budget_actual_data['annual']["total_electricity_cost_actual"] += $value["total_electricity_cost_actual"];

	    $current_budget_actual_data['annual']["total_electricity_cost_budget"] += $value["total_electricity_cost_budget"];

	    $current_budget_actual_data['annual']["total_fuel_oil_actual"] += $value["total_fuel_oil_actual"];

	    $current_budget_actual_data['annual']["total_fuel_oil_budget"] += $value["total_fuel_oil_budget"];

	    $current_budget_actual_data['annual']["total_fuel_oil_cost_actual"] += $value["total_fuel_oil_cost_actual"];

	    $current_budget_actual_data['annual']["total_fuel_oil_cost_budget"] += $value["total_fuel_oil_cost_budget"];

	    $current_budget_actual_data['annual']["total_lpg_actual"] += $value["total_lpg_actual"];

	    $current_budget_actual_data['annual']["total_lpg_budget"] += $value["total_lpg_budget"];

	    $current_budget_actual_data['annual']["total_lpg_cost_actual"] += $value["total_lpg_cost_actual"];

	    $current_budget_actual_data['annual']["total_lpg_cost_budget"] += $value["total_lpg_cost_budget"];

	    $current_budget_actual_data['annual']["total_natural_gas_actual"] += $value["total_natural_gas_actual"];

	    $current_budget_actual_data['annual']["total_natural_gas_budget"] += $value["total_natural_gas_budget"];

	    $current_budget_actual_data['annual']["total_natural_gas_cost_actual"] += $value["total_natural_gas_cost_actual"];

	    $current_budget_actual_data['annual']["total_natural_gas_cost_budget"] += $value["total_natural_gas_cost_budget"];

	    $current_budget_actual_data['annual']["water_total_consumption_actual"] += $value["water_total_consumption_actual"];

	    $current_budget_actual_data['annual']["water_total_consumption_budget"] += $value["water_total_consumption_budget"];

	    $current_budget_actual_data['annual']["water_total_consumption_cost_actual"] += $value["water_total_consumption_cost_actual"];

	    $current_budget_actual_data['annual']["water_total_consumption_cost_budget"] += $value["water_total_consumption_cost_budget"];

	}

	$data['currentBudgetActualData'] = $current_budget_actual_data;



	$data['filters'] = $filters;



	$this->theme->set('page_title', lang('reports'));



	$view_type = $this->input->post('view_type', '');



	$site_id = $this->session->userdata['hep_cron_session']['site_id'];

	$this->load->model('sites/sites_model');

	$site_detail_result = $this->sites_model->get_site_detail_custom($site_id);

	$data['site_detail'] = $site_detail_result;



	//measure readings

	$measure_readings = $this->sites_model->get_measure_readings($site_id);

	foreach ($measure_readings as $m_reading) {

	    $data['measure_readings'][$m_reading['measure_id']] = $m_reading;

	}



	return $data;

    }



    public function viewactionplans($site_id)

    {

	$projects_categories = $this->reportscron_model->getEMACategories();

	$actiondata = array();

	$actiondata['site_id'] = $site_id;

	$actiondata['user_id'] = $this->user_id;



	$is_actionplans = false;

	if (!empty($projects_categories)) {

	    foreach ($projects_categories as $key => $category) {

		$todocount = 0;

		$projects = $this->reportscron_model->getEMAPublicProjects($category['pc']['id']);

		foreach ($projects as $key1 => $project) {

		    $actiondata['project_id'] = $project['p']['id'];



		    $project_todos = $this->reportscron_model->get_ema_actionplans_todos_bysite($actiondata);

		    $projects[$key1]['p']['project_todos'] = $project_todos;

		    $todocount += count($project_todos);

		}

		$projects_categories[$key]['pc']['category_static_image'] = str_replace(' ', '_', $projects_categories[$key]['pc']['name']) . '.png';

		$projects_categories[$key]['pc']['projects_todo_count'] = $todocount;

		$projects_categories[$key]['pc']['projects'] = $projects;

	    }

	}



	$data['is_actionplans'] = $is_actionplans;

	$data['projects_categories'] = $projects_categories;

	$data['action_categories'] = $this->reportscron_model->getEMACategoriesList();



	return $data;

    }



    public function generate_report_pdf($data, $report_flag = "")

    {

	extract($data);

	$this->lang->load('sites/sites', 'english');

	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

	$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

	$user_id = $this->session->userdata['hep_cron_session']['user_id'];

	$site_id = $site_detail['id'];

	$role_id = $this->session->userdata['hep_cron_session']['role_id'];



	//Hotel detail

	$this->load->model('hotels/hotels_model');

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	// Site detail

	$this->load->model('sites/sites_model');

	$result = $this->sites_model->get_site_detail_custom($site_id);



	$region_list = $this->sites_model->region_list();

	$country_list = $this->sites_model->country_list();

	$hotel_list = $this->sites_model->hotel_list();

	$data['site_detail'] = $result;

	$data['region_list'] = $region_list;

	$data['country_list'] = $country_list;

	$data['hotel_list'] = $hotel_list;



	$substations = $this->sites_model->get_substations($site_id);

	$data['substations'] = $substations;



	$generators = $this->sites_model->get_generators($site_id);

	$data['generators'] = $generators;



	$hot_water_boilers = $this->sites_model->get_hot_water_boilers($site_id);

	$data['hot_water_boilers'] = $hot_water_boilers;



	$steam_boilers = $this->sites_model->get_steam_boilers($site_id);

	$data['steam_boilers'] = $steam_boilers;



	$renewable_energys = $this->sites_model->get_renewable_energys($site_id);

	$data['renewable_energys'] = $renewable_energys;



	$allcurrentBudgetActualData = $data['currentBudgetActualData'];



	$show_piechart = false;

	$show_actionplans = false;

	$show_site_details = false;



	$this->session->userdata['hep_cron_session']['pdf_site_logo'] = $result['site_logo'];

	$this->session->userdata['hep_cron_session']['pdf_site_name'] = $result['site_location_name'];

	define('SITE_PDF_HEADER_LOGO', "/assets/uploads/" . $result['site_logo']);

	// define('PDF_HEADER_SITE_NAME', 'Site Report');

	define('PDF_HEADER_SITE_NAME', $result['site_location_name']);



	if (!empty($this->input->post())) {

	    $postdata = [];

	    $postdata = $chartData;



	    $site_detials = $this->sites_model->get_site_detail_custom($site_id);



	    /*

	     * **********************************************************************************

	     * ytd report

	     * **********************************************************************************

	     */

	    if ($report_flag == "mytd") {

		$waste_chart = '';

		$waste_pie_chart = '';

		$waste_anuual_chart = '';

		$waste_anuual_pie_chart = '';

		$waste_monthly_reports = '';

		$waste_monthly_piechart_reports = '';



		$data['currentBudgetActualData'] = $allcurrentBudgetActualData;

		$data['pdf_report_title'] = 'YTD Utilities report - ' . date('Y');

		$data['previous_title'] = 'Previous Year - ' . (date('Y') - 1);

		$data['current_title'] = 'Current Year - ' . date('Y');

		$data['budget_title'] = 'Budget - ' . date('Y');

		$data['table_title'] = 'Year To Date Report';

		$data['type'] = 'ytd';

		$data['show_site_details'] = false;

		$show_piechart = true;

		$show_site_details = false;

		$show_actionplans = false;

		$data['showCostBudgetVariance'] = true;

		$ytdcurrentBudgetActualData = $data['currentBudgetActualData']['ytd'];

		$data['currentBudgetActualData'] = $ytdcurrentBudgetActualData;

		$data['columnChartImg'] = $postdata['columnChartImg'];

		$data['pieChartImg'] = $postdata['pieChartImg'];

		$data['pieChartNewImg'] = $postdata['pieChartNewImg'];

		$data['pieChartNew2Img'] = $postdata['pieChartNew2Img'];

		$data['pieChartNew3Img'] = $postdata['pieChartNew3Img'];

		$data['wasteChartImg'] = $postdata['wasteChartImg'];

		$data['wastePieChartImg'] = $postdata['wastePieChartImg'];

		$data['wasteLandfillPieChartImg'] = $postdata['wasteLandfillPieChartImg'];

		$data['is_monthly'] = false;

		$filter_custom_notification = array(

		    'year' => date('Y'),

		);

		$customnotifications = $this->sites_model->getSiteCustomNotifications($site_id, $filter_custom_notification);

		foreach ($customnotifications as $notification) {

		    $month = date('m', $notification['date']);

		    if ($notification['ytd'] && $month <= date('m')) {

			$data['customNotifications'][] = $notification;

		    }

		}



		$content_reports = $this->load->view('admin_landing_pdf_reports', $data, true);

		$content_reports_piecharts = $this->load->view('admin_landing_pdf_reports_piecharts', $data, true);

		$data['columnChartCarbonFootprintImg'] = $postdata['columnChartCarbonFootprintImg'];

		$content_reports_carbon_footprint = $this->load->view('admin_landing_pdf_reports_carbon_footprint', $data, true);



		if ($result['show_waste_management']) {

		    $waste_chart = $this->load->view('admin_landing_waste_pdf_reports', $data, true);

		    $waste_pie_chart = $this->load->view('admin_landing_waste_piechart_pdf_reports', $data, true);

		}



		$this->utilities_model->utilities_month = date("n") - 1;

		$this->utilities_model->utilities_year = date("Y");



		if ($this->utilities_model->utilities_month == 0) {

		    $this->utilities_model->utilities_month = 12;

		    $this->utilities_model->utilities_year = date("Y") - 1;

		}

		$this->utilities_model->site_id = $site_id;

		$getUtilityData = $this->utilities_model->getSiteUtilityCurYear();



		$this->utilities_model->utilities_month = date("n");

		$this->utilities_model->utilities_year = date("Y") - 1;

		$getUtilityData_prev = $this->utilities_model->getSiteUtilityLastYear();



		$carbon_footPrint_measure = 0;

		$total_room_night_measure = 0;

		$utility_kwh_total_measure = 0;

		$water_total_consumption_measure = 0;



		foreach ($getUtilityData as $getUtilities) {

		    $getUtilities = array_map('floatval', $getUtilities);

		    $carbon_footPrint_measure += ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);



		    $total_room_night_measure += $getUtilities['total_room_night'];

		    $water_total_consumption_measure += $getUtilities['water_total_consumption'];



		    $lpg_value = $getUtilities['total_lpg'] * 13.269;

		    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;

		    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;

		    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;

		    $heating_district_value = $getUtilities['district_heating'] * 1;

		    $cooling_district_value = $getUtilities['district_cooling'] * 1;



		    $utility_kwh_total_measure += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);

		}



		foreach ($getUtilityData_prev as $getUtilities) {

		    $getUtilities = array_map('floatval', $getUtilities);

		    $carbon_footPrint_measure += ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);



		    $total_room_night_measure += $getUtilities['total_room_night'];

		    $water_total_consumption_measure += $getUtilities['water_total_consumption'];



		    $lpg_value = $getUtilities['total_lpg'] * 13.269;

		    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;

		    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;

		    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;

		    $heating_district_value = $getUtilities['district_heating'] * 1;

		    $cooling_district_value = $getUtilities['district_cooling'] * 1;



		    $utility_kwh_total_measure += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);

		}



		$data['measures']['HotelCarbonFootprintPerRoom'] = round($carbon_footPrint_measure / $result['rooms_keys'], 2);

		$data['measures']['HotelCarbonFootprintPerOccupiedRoom'] = round($carbon_footPrint_measure / $total_room_night_measure, 2);

		$data['measures']['HotelCarbonFootprintPerSquareMeter'] = round($carbon_footPrint_measure / $result['site_builtup_area'], 2);

		$data['measures']['HotelEnergyUsagePerOccupiedRoom'] = round($utility_kwh_total_measure / $total_room_night_measure, 2);

		$data['measures']['HotelEnergyUsagePerSquareMeter'] = round($utility_kwh_total_measure / $result['site_builtup_area'], 2);

		$data['measures']['HotelWaterUsagePerOccupiedRoom'] = round($water_total_consumption_measure / $total_room_night_measure, 2);

		$data['measures']['HotelWaterUsagePerSquareMeter'] = round($water_total_consumption_measure / $result['site_builtup_area'], 2);



		if ($result['chsb_reporting'] == 1) {

		    $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports', $data, true);

		}



		$content_site_detail = $this->load->view('admin_landing_pdf_site_detail', $data, true);



		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



		$pdf->site_name = $data['site_detail']['site_location_name'];

		$pdf->site_logo = $data['site_detail']['site_logo'];



		$pdf->SetFont('helvetica', '', 9);

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetPrintHeader(true);

		$pdf->SetPrintFooter(true);

		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		$pdf->SetMargins(10, 20, 10); // Left,Top,Right



		$pdfName = $site_detail['site_location_name'] . " YTD report " . date('dmY') . ".pdf";

		$pdfName = strtolower(str_replace(array(' ', '-'), array('_', ''), $pdfName));



		if ($show_site_details) {

		    $pdf->AddPage();

		    $pdf->writeHTML($content_site_detail, true, false, true, false, '');

		}



		$pdf->AddPage();

		$pdf->writeHTML($content_reports, true, false, true, false, '');



		if ($show_piechart) {

		    $pdf->AddPage();

		    $pdf->writeHTML($content_reports_piecharts, true, false, true, false, '');

		}



		if ($show_actionplans) {

		    $pdf->AddPage();

		    $pdf->writeHTML($content_reports_actionplans, true, false, true, false, '');

		}



		if ($content_reports_carbon_footprint != '') {

		    $pdf->AddPage();

		    $pdf->writeHTML($content_reports_carbon_footprint, true, false, true, false, '');

		}



		if ($result['show_waste_management']) {



		    if ($waste_chart != '') {

			$pdf->AddPage();

			$pdf->writeHTML($waste_chart, true, false, true, false, '');

		    }



		    if ($waste_pie_chart != '') {



			$pdf->AddPage();

			$pdf->writeHTML($waste_pie_chart, true, false, true, false, '');

		    }

		}

		if ($result['chsb_reporting'] == 1) {



		    if ($chsb_reporting != '') {

			$pdf->AddPage();

			$pdf->writeHTML($chsb_reporting, true, false, true, false, '');

		    }

		}



		$file_name = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $pdfName;



		$pdf->Output($file_name, 'F'); // D - downlaod, F- Save

		$files['ytd'] = $file_name;



		// Remove chart images

	    }



	    // Remove chart's image

	    $this->removeChartImages($data['monthly']);

	    $this->removeChartImages($data);

	    return $files;

	}

    }



    private function removeChartImages($data)

    {

	$imagePath = BASE_PATH_CUSTOM . "/assets/uploads/cron/";

	$imageUrl = site_url() . "assets/uploads/cron/";



	$imageArray = array();

	$imageArray[] = $data['columnChartImg'];

	$imageArray[] = $data['columnChartImg_month'];

	$imageArray[] = $data['columnChartCarbonFootprintImg'];

	$imageArray[] = $data['columnChartCarbonFootprintAnnualImg'];

	$imageArray[] = $data['columnChartImg_hidden'];

	$imageArray[] = $data['columnChartImg_5years_hidden'];

	$imageArray[] = $data['columnChartImg_monthly'];

	$imageArray[] = $data['columnChartImg_monthly_month'];

	$imageArray[] = $data['pieChartImg'];

	$imageArray[] = $data['pieChartNewImg'];

	$imageArray[] = $data['pieChartImg_hidden'];

	$imageArray[] = $data['pieChartNewImg_hidden'];

	$imageArray[] = $data['pieChartNew2Img'];

	$imageArray[] = $data['pieChartNew3Img'];

	$imageArray[] = $data['wasteChartImg'];

	$imageArray[] = $data['wastePieChartImg'];

	$imageArray[] = $data['wasteLandfillPieChartImg'];

	$imageArray[] = $data['pieAnnualChartNewImg_hidden'];

	$imageArray[] = $data['pieAnnualLandfillImg_hidden'];

	$imageArray[] = $data['wasteMonthlyChartImg'];

	$imageArray[] = $data['wasteMonthlyChartImg_month'];

	$imageArray[] = $data['wastePieMonthlyChartImg'];

	$imageArray[] = $data['wastePieMonthlyChartImg_month'];

	$imageArray[] = $data['wastePieLandfillMonthlyChartImg'];

	$imageArray[] = $data['wasteChartPreImg_hidden'];



	foreach ($imageArray as $value) {

	    $image = str_replace($imageUrl, $imagePath, $value);

	    if (file_exists($image)) {

		unlink($image);

	    }

	}



	return true;

    }



    private function genrateImageFromBase64($url = '')

    {

	$image_path = '';

	if ($url != '') {

	    $imageName = uniqid() . uniqid() . '.png';

	    $imagePath = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $imageName;

	    if (!is_dir(BASE_PATH_CUSTOM . "/assets/uploads/cron/")) {

		mkdir(BASE_PATH_CUSTOM . "/assets/uploads/cron/", 0777, true);

	    }

	    $imageUrl = site_url() . "assets/uploads/cron/" . $imageName;



	    list($type, $url) = explode(';', $url);

	    list(, $url) = explode(',', $url);

	    $url = base64_decode($url);



	    $result = file_put_contents($imagePath, $url);

	    /* $this->load->library('image_lib');

	      $config['image_library'] = 'gd2';

	      $config['source_image'] = $imagePath;

	      $this->image_lib->clear();

	      $this->image_lib->initialize($config);

	      $this->image_lib->resize(); */



	    $image_path = $imageUrl;

	}



	return $image_path;

    }



    private function sendMail($files = array())

    {



	$request_report_type = $this->input->get('type');

	$this->load->library('mailer');

	$this->mailer->mail->IsHTML(true);



	$users = $this->reportscron_model->getUserDetails();

	if (!empty($files)) {

	    $site_name = $files['site_name'];
	    $site_id = $files['site_id'];

	    $current_month_text = date('F');

	    $previous_month_text = date('F', strtotime($current_month_text . " last month"));

	    $current_year = date('Y');

	    $currentMonth = intval(date('m'));



	    $sendFiles = array();

	    foreach ($users as $user) {

		if (empty($user['email'])) {

		    continue;

		}



		/* if($user['email'] != "naresh.prajapati@sparsh-technologies.co.in"){

		  continue;

		  } */



		 $this->mailer->mail->AddAddress($user['email']);

		// $this->mailer->mail->AddAddress('rdiab@eegroup.info');

		// $this->mailer->mail->AddAddress('mitesh.koshiya@tatvasoft.com');

		// $this->mailer->mail->AddAddress('surbhi.ladhava@tatvasoft.com');

		// $this->mailer->mail->AddAddress('dhaval.prajapati@tatvasoft.com');
		// $this->mailer->mail->AddAddress('pandeygarima3160@gmail.com');



		$sites = $user['sites'];

		$reports = $user['reports'];

		$fisrtSiteId = array_key_first ($sites);

		if ($fisrtSiteId == $site_id) {
		    continue;
		}


		if (in_array('monthly_ytd', $reports) && in_array('quarterly', $reports) && $request_report_type == 'mytd' && in_array(trim($site_name), $sites)) {

		    if (file_exists($files['files']['ytd'])) {

			$monthly_report_year = $current_year;

			if ($currentMonth == 1) {

			    $monthly_report_year = $monthly_report_year - 1;

			}



			// $subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') & YTD ' . $current_year . ' Utilities Reports';

			$subject = $site_name . ' - YTD ' . $current_year . ' Utilities Reports';

			if ($currentMonth == 1) {

			    $subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') Utilities Reports';

			}



			$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

			$bodyHtml .= '<div>You can find attached the YTD ' . $current_year . ' Utilities report for ' . $this->hotel['hotel_name'] . ' - ' . $site_name . '</div>';

			$bodyHtml .= '<div>For more information and analysis, log on to <a href="' . base_url() . '">' . base_url() . '</a></div>';



			$email_template['html'] = $bodyHtml;

			$body = $this->load->view('email_template', $email_template, true);



			$this->mailer->mail->Subject = $subject;

			$this->mailer->mail->Body = $body;



			// $this->mailer->mail->addAttachment($files['files']['monthly'], $site_name . ' Monthly Report.pdf');



			if ($currentMonth != 1) {

			    $this->mailer->mail->addAttachment($files['files']['ytd'], $site_name . ' YTD Report.pdf');

			}

			saveBulkData(array('email' => $user['email']), "MYTD_EMAIL");

			$this->mailer->mail->Send();

		    }

		}



		$this->mailer->mail->ClearAttachments();

		$this->mailer->mail->ClearAllRecipients();

	    }



	    //delete pdf reports

	    foreach ($files['files'] as $key => $file) {

		if (file_exists($file)) {

		    unlink($file);

		}

	    }



	    return true;

	}



	return true;

    }





    public function getMonthlyReportChart($month, $year)

    {

	$isLocal = true;

	if (CURRENT_CURRENCY == "base") {

	    $isLocal = false;

	}

	$cur_symbol = cur_symbol($isLocal);



	$startdate = $month . '/' . $year;

	$enddate = $startdate;

	$decimal_places = 2;



	$this->load->model('sites/sites_model');

	$site_details = $this->sites_model->get_site_detail_custom($this->session->userdata['hep_cron_session']['site_id']);



	$startdateexplode = explode('/', $startdate);

	$enddateexplode = explode('/', $enddate);



	$filters_comparision_chart['startdate'] = (isset($startdate)) ? $startdate : '';

	$filters_comparision_chart['enddate'] = (isset($enddate)) ? $enddate : '';



	$filters_comparision_chart['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

	$filters_comparision_chart['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';

	$filters_comparision_chart['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';

	$filters_comparision_chart['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';

	if ($isLocal) {

	    $utility_cost_chart_results = $this->reportscron_model->utilityCostBarChart($filters_comparision_chart);

	} else {

	    $utility_cost_chart_results = $this->reportscron_forex_model->utilityCostBarChart($filters_comparision_chart);

	}



	$chart_data = array();

	$carbon_footprint = array();

	$chart_index = array(

	    "electricity",

	    "fuel",

	    "lpg",

	    "natural_gas",

	    "water",

	    "heating_district",

	    "cooling_district",

	);

	$chart_index_carbon = array(

	    "electricity",

	    "fuel",

	    "lpg",

	    "natural_gas",

	    "heating_district",

	    "cooling_district",

	);



	$electricityTitle = lang("electricity");

	$fuelTitle = lang("fuel");

	$lpgTitle = lang("lpg");

	$naturalTitle = lang("natural-gas");

	$waterTitle = lang("water");

	$heatingTitle = lang("heating-district");

	$coolingTitle = lang("cooling-district");

	$occupancyTitle = lang("occupancy");



	$chart_index = array(

	    "electricity",

	    "fuel",

	    "lpg",

	    "natural_gas",

	    "water",

	    "heating_district",

	    "cooling_district",

	);



	$chart_data_title = array(

	    'Month',

	    $electricityTitle,

	    $fuelTitle,

	    $lpgTitle,

	    $naturalTitle,

	    $waterTitle,

	    $heatingTitle,

	    $coolingTitle,

	    $occupancyTitle,

	);

	$carbon_footprint_title = array(

	    'Month',

	    $electricityTitle,

	    $fuelTitle,

	    $lpgTitle,

	    $naturalTitle,

	    $heatingTitle,

	    $coolingTitle,

	    $occupancyTitle,

	);



	$chart_data[0] = $chart_data_title;

	$carbon_footprint[0] = $carbon_footprint_title;



	// Set Defaults

	$chart_data[1] = array(

	    $montharray[$filters_comparision_chart['start_month']] . ' ' . ($filters_comparision_chart['start_year'] - 1),

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	);

	$carbon_footprint[1] = array(

	    $montharray[$filters_comparision_chart['start_month']] . ' ' . ($filters_comparision_chart['start_year'] - 1),

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	);



	$chart_data[2] = array(

	    $montharray[$filters_comparision_chart['start_month']] . ' ' . ($filters_comparision_chart['start_year']),

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	);

	$carbon_footprint[2] = array(

	    $montharray[$filters_comparision_chart['start_month']] . ' ' . ($filters_comparision_chart['start_year']),

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	    0,

	);



	if (!empty($utility_cost_chart_results)) {

	    foreach ($utility_cost_chart_results as $key => $value) {

		$value = array_map('floatval', $value);

		$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];

		$value['heating_district'] = floatval((string) $value['heating_district']) + floatval((string) $value['district_heating_fixed_cost']);



		$utility_cost_chart['utility_cost_chart_title'] = lang('utility-cost-chart-title');

		$utility_cost_chart[$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? $value['total_budget'] : 0;



		$utility_cost_chart[$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['onsite_generator'] = (!empty($value['onsite_generator'])) ? $value['onsite_generator'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['renewable_energy'] = (!empty($value['renewable_energy'])) ? $value['renewable_energy'] : 0;



		$utility_cost_chart[$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;

		$utility_cost_chart[$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;



		if (!empty($value['total_electricity_kwh'])) {

		    $electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];

		} else {

		    $electricity_tariff_cost_per_kwh = 0;

		}

		$utility_cost_chart[$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;



		$utility_cost_chart[$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];

		$utility_cost_chart[$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];

		$utility_cost_chart[$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];

		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

		$utility_cost_chart[$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);

	    }



	    $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');



	    $resultkeysMonthlyreport = array();

	    $resultkeysMonthlyreport[$filters_comparision_chart['start_year']] = array($filters_comparision_chart['start_month']);

	    foreach ($resultkeysMonthlyreport as $year => $value) {

		foreach ($value as $key1 => $month) {

		    $prevYear = $year - 1;

		    $pre_monthdata = $montharray[$month] . ' ' . ($prevYear);

		    $pre_data_electricity = (!empty($utility_cost_chart[$month][$prevYear]['electricity'])) ? $utility_cost_chart[$month][$prevYear]['electricity'] : 0;

		    $pre_data_fuel = (!empty($utility_cost_chart[$month][$prevYear]['fuel'])) ? $utility_cost_chart[$month][$prevYear]['fuel'] : 0;

		    $pre_data_lpg = (!empty($utility_cost_chart[$month][$prevYear]['lpg'])) ? $utility_cost_chart[$month][$prevYear]['lpg'] : 0;

		    $pre_data_natural_gas = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas'] : 0;

		    $pre_data_heating_district = (!empty($utility_cost_chart[$month][$prevYear]['heating_district'])) ? $utility_cost_chart[$month][$prevYear]['heating_district'] : 0;

		    $pre_data_cooling_district = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district'] : 0;



			$pre_data_electricity_consumption = (!empty($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'] - $utility_cost_chart[$month][$prevYear]['onsite_generator'] - $utility_cost_chart[$month][$prevYear]['renewable_energy']) : 0;

		    $pre_data_fuel_consumption = (!empty($utility_cost_chart[$month][$prevYear]['fuel_consumption'])) ? $utility_cost_chart[$month][$prevYear]['fuel_consumption'] : 0;

		    $pre_data_lpg_consumption = (!empty($utility_cost_chart[$month][$prevYear]['lpg_consumption'])) ? $utility_cost_chart[$month][$prevYear]['lpg_consumption'] : 0;

		    $pre_data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas_consumption'] : 0;

		    $pre_data_heating_district_consumption = (!empty($utility_cost_chart[$month][$prevYear]['heating_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['heating_district_consumption'] : 0;

		    $pre_data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district_consumption'] : 0;



		    $pre_data_water = (!empty($utility_cost_chart[$month][$prevYear]['water'])) ? $utility_cost_chart[$month][$prevYear]['water'] : 0;

		    $pre_data_cdd = (!empty($utility_cost_chart[$month][$prevYear]['cdd'])) ? $utility_cost_chart[$month][$prevYear]['cdd'] : 0;

		    $pre_data_hdd = (!empty($utility_cost_chart[$month][$prevYear]['hdd'])) ? $utility_cost_chart[$month][$prevYear]['hdd'] : 0;

		    $pre_data_occupancy = (!empty($utility_cost_chart[$month][$prevYear]['occupancy'])) ? $utility_cost_chart[$month][$prevYear]['occupancy'] : 0;

		    $pre_data_budget = (!empty($utility_cost_chart[$month][$prevYear]['budget'])) ? $utility_cost_chart[$month][$prevYear]['budget'] : 0;



		    // Current year data

		    $monthdata = $montharray[$month] . ' ' . $year;



		    $data_electricity = (!empty($utility_cost_chart[$month][$year]['electricity'])) ? $utility_cost_chart[$month][$year]['electricity'] : 0;

		    $data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;

		    $data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;

		    $data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas'])) ? $utility_cost_chart[$month][$year]['natural_gas'] : 0;

		    $data_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district'])) ? $utility_cost_chart[$month][$year]['heating_district'] : 0;

		    $data_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district'])) ? $utility_cost_chart[$month][$year]['cooling_district'] : 0;



		    $data_electricity_consumption = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year]['total_electricity_kwh'] - $utility_cost_chart[$month][$year]['onsite_generator'] - $utility_cost_chart[$month][$year]['renewable_energy']) : 0;

		    $data_fuel_consumption = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? $utility_cost_chart[$month][$year]['fuel_consumption'] : 0;

		    $data_lpg_consumption = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;

		    $data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;

		    $data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year]['heating_district_consumption'] : 0;

		    $data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year]['cooling_district_consumption'] : 0;



		    $data_water = (!empty($utility_cost_chart[$month][$year]['water'])) ? $utility_cost_chart[$month][$year]['water'] : 0;

		    $data_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

		    $data_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

		    $data_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

		    $data_budget = (!empty($utility_cost_chart[$month][$year]['budget'])) ? $utility_cost_chart[$month][$year]['budget'] : 0;



		    // Round values

		    $pre_data_occupancy = round($pre_data_occupancy, 2);

		    $data_occupancy = round($data_occupancy, 2);



		    $chart_data = $carbon_footprint = array();

		    $chart_index = $chart_index_carbon = array();



		    $chart_data[0][] = $carbon_footprint[0][] = "Month";

		    $chart_data[1][] = $carbon_footprint[1][] = $pre_monthdata;

		    $chart_data[2][] = $carbon_footprint[2][] = $monthdata;



		    if ($pre_data_electricity != 0 || $data_electricity != 0) {

			$chart_data[0][] = $electricityTitle;

			$chart_data[1][] = $pre_data_electricity;

			$chart_data[2][] = $data_electricity;



			$carbon_footprint[0][] = $electricityTitle;

			$carbon_footprint[1][] = round($pre_data_electricity_consumption * $site_details['electricity_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_electricity_consumption * $site_details['electricity_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "electricity";

		    }



		    if ($pre_data_fuel != 0 || $data_fuel != 0) {

			$chart_data[0][] = $fuelTitle;

			$chart_data[1][] = $pre_data_fuel;

			$chart_data[2][] = $data_fuel;



			$carbon_footprint[0][] = $fuelTitle;

			$carbon_footprint[1][] = round($pre_data_fuel_consumption * $site_details['fuel_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_fuel_consumption * $site_details['fuel_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "fuel";

		    }



		    if ($pre_data_lpg != 0 || $data_lpg != 0) {

			$chart_data[0][] = $lpgTitle;

			$chart_data[1][] = $pre_data_lpg;

			$chart_data[2][] = $data_lpg;



			$carbon_footprint[0][] = $lpgTitle;

			$carbon_footprint[1][] = round($pre_data_lpg_consumption * $site_details['lpg_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_lpg_consumption * $site_details['lpg_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "lpg";

		    }



		    if ($pre_data_natural_gas != 0 || $data_natural_gas != 0) {

			$chart_data[0][] = $naturalTitle;

			$chart_data[1][] = $pre_data_natural_gas;

			$chart_data[2][] = $data_natural_gas;



			$carbon_footprint[0][] = $naturalTitle;

			$carbon_footprint[1][] = round($pre_data_natural_gas_consumption * $site_details['natural_gas_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_natural_gas_consumption * $site_details['natural_gas_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "natural_gas";

		    }



		    if ($pre_data_water != 0 || $data_water != 0) {

			$chart_data[0][] = $waterTitle;

			$chart_data[1][] = $pre_data_water;

			$chart_data[2][] = $data_water;

			$chart_index[] = "water";

		    }



		    if ($pre_data_heating_district != 0 || $data_heating_district != 0) {

			$chart_data[0][] = $heatingTitle;

			$chart_data[1][] = $pre_data_heating_district;

			$chart_data[2][] = $data_heating_district;



			$carbon_footprint[0][] = $heatingTitle;

			$carbon_footprint[1][] = round($pre_data_heating_district_consumption * $site_details['district_heating_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_heating_district_consumption * $site_details['district_heating_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "heating_district";

		    }



		    if ($pre_data_cooling_district != 0 || $data_cooling_district != 0) {

			$chart_data[0][] = $coolingTitle;

			$chart_data[1][] = $pre_data_cooling_district;

			$chart_data[2][] = $data_cooling_district;



			$carbon_footprint[0][] = $coolingTitle;

			$carbon_footprint[1][] = round($pre_data_cooling_district_consumption * $site_details['district_cooling_emission_factor'], $decimal_places);

			$carbon_footprint[2][] = round($data_cooling_district_consumption * $site_details['district_cooling_emission_factor'], $decimal_places);



			$chart_index[] = $chart_index_carbon[] = "cooling_district";

		    }



		    $chart_data[0][] = $carbon_footprint[0][] = $occupancyTitle;

		    $chart_data[1][] = $carbon_footprint[1][] = $pre_data_occupancy;

		    $chart_data[2][] = $carbon_footprint[2][] = $data_occupancy;

		}

	    }

	}

	$data['chart_data'] = array_values($chart_data);

	$data['carbon_footprint'] = array_values($carbon_footprint);

	$data['chart_index'] = array_values($chart_index);

	$data['chart_index_carbon'] = array_values($chart_index_carbon);

	$data['chart_waste_data'] = $chart_waste_data;

	$data['chart_pie_data'] = [];

	return $data;

    }



    private function loginOnFly()

    {

	$this->users_model->username = 'admin';

	$this->users_model->password = 'demo@123';



	$data['username'] = $this->input->post('username');

	$result = $this->users_model->login();

	$hotel_detail = $this->hotels_model->get_hotel_detail(1);



	// Set default site for user start



	if ($result[0]['u']['site_id'] == "0") {

	    $this->sites_model->record_per_page = 1;

	    $this->sites_model->offset = 0;

	    $first_site = $this->sites_model->get_site_listing_for_users($result[0]['u']['site_id'], $result[0]['u']['role_id'], $result[0]['u']['id']);

	    $result[0]['u']['site_id'] = $first_site[0]['s']['id'];

	} else {

	    $this->users_model->site = $result[0]['u']['site_id'];

	}

	// Set default site for user end



	if ($result[0]['u']['site_id'] != "0") {

	    $site_color_logo = $this->users_model->get_site_color_logo($result[0]['u']['site_id']);

	}



	if (!empty($result)) {

	    if ($result[0]['u']['status'] == 1) {

		//add all data to session

		$newdata = array(

		    'user_id' => $result[0]['u']['id'],

		    'role_id' => $result[0]['u']['role_id'],

		    'site_id' => $result[0]['u']['site_id'],

		    'hotel_id' => $hotel_detail['id'],

		    'hotel_logo' => $hotel_detail['hotel_logo'],

		    'project_id' => $result[0]['u']['project_id'],

		    'email' => $result[0]['u']['email'],

		    'username' => $result[0]['u']['username'],

		    'firstname' => $result[0]['u']['firstname'],

		    'lastname' => $result[0]['u']['lastname'],

		    'local_currency' => '',

		    'logged_in' => true,

		);



		$this->session->set_custom_userdata('hep_cron_session', $newdata);

		// Set permission in session

		$this->allowed_permission_list($newdata['role_id']);

		if ($result[0]['u']['id'] == '1') {

		    $this->session->set_custom_userdata('hep_cron_session', "super_user", "1");

		}

	    }

	}

    }



    private function logoutOnFly()

    {

	$this->session->unset_userdata('hep_cron_session');

    }



}



define('K_PATH_IMAGES', BASE_PATH_CUSTOM . "/assets/uploads/");

require_once BASE_PATH_CUSTOM . '/application/libraries/tcpdf/tcpdf.php';



class MYPDF extends TCPDF

{



    public $site_name = '';

    public $site_logo = '';



    public function Header()

    {

	$image_file = K_PATH_IMAGES . $this->site_logo;



	if (file_exists($image_file)) {

	    $this->Image($image_file, 10, 2, 30, 18, '', '', 'T', false, 300, '', false, false, 0, false, false, false);

	}



	$this->Line(10, 24, '202', 24);

	$this->SetFont('helvetica', 'B', 12);

	$this->Cell(150, 22, PDF_HEADER_SITE_NAME, 0, false, 'C', 0, '', 0, false, 'T', 'M');

	$this->SetFont('helvetica', 'B', 8);

	$this->Cell(0, 22, DATE('d-M-Y'), 0, false, 'R', 0, '', 0, false, 'T', 'M');

    }



    public function Footer()

    {

	$this->SetY(-15);

	$this->SetFont('helvetica', 'I', 8);

	$footText = "Page " . $this->getAliasNumPage() . "/" . $this->getAliasNbPages();

	$footText .= "\n HEP - Hotel Energy Portal | Copyright © " . date("Y") . " EEG - Energy Efficiency Group. All rights reserved.";

	$this->MultiCell(0, 10, $footText, 0, 'C');

    }



}

