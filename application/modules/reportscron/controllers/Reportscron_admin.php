<?php



if (!defined('BASEPATH')) {

	exit('No direct script access allowed');
}



class Reportscron_admin extends Base_Admin_Controller

{



	public function __construct()

	{
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
		$this->load->model('reports_energy/reports_energy_model');

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

				'actions' => array('index', 'upper_management_report', 'get_image_from_uri', 'send_mail_to_user', 'prepare_chart', 'upper_management_utilities_report', 'generate_mpdf_new', 'get_image_from_uri_new', 'generate_combined_eui_chart'),

				'users' => array('*'),

			),

		);
	}



	public function prepare_chart()
	{

		if (isset($_GET["debug"])) {

			exit("debug");
		}

		exit("no debug");
	}


	public function index($cview = 'index')

	{
		// URL: ?type=mytd|annual&ni={site_id} ? mytd maps to monthly+YTD runs; user-facing report titles and mail subjects use "<site> - Month (Month YYYY) ?" patterns (see generate_report_pdf / sendMail)

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

		$is_site_used = false;

		if (!empty($users)) {
			foreach ($users as $user_value) {

				if (!empty($user_value['sites'])) {

					if (array_key_exists($getData['ni'], $user_value['sites']) && in_array($report_post_type, $user_value['reports'])) {

						if (in_array(QUARTERLY_REPORT, $user_value['reports'])) {

							$fisrtSiteId = array_key_first($user_value['sites']);

							if ($fisrtSiteId == $site_id) {

								$site_id = $fisrtSiteId;

								$is_site_used = true;

								break;
							} else {

								$is_site_used = false;
							}
						} else {

							$is_site_used = true;

							break;
						}
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



		register_shutdown_function(function () {

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

			$data['sites'][$key]['monthly'] = $this->indexCalculation($cview, true);

			/* REGRESSION CHART APPEND ON END*/
			$filterArray = [
				'site_id' => $site_id,
				'year'    => date('Y'),
			];
			$energy_modelling_data            = $this->sites_model->get_energy_modelling($filterArray);
			$data['sites'][$key]['monthly']['energy_modelling_data']    = $energy_modelling_data;

			$regressionUtility = [];
			$utility = ['electricity', 'fuel_oil', 'lpg', 'water', 'natural_gas', 'district_heating', 'district_cooling'];
			$utility_array = [
				'electricity' => [
					'db_key' => 'total_electricity_kwh',
					'unit' => 'kWh',
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
					'unit' => 'kWh',
					'Label' => 'District Cooling',
				],
				'district_heating' => [
					'db_key' => 'district_heating',
					'unit' => 'kWh',
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

			foreach ($utility as $energy) {
				$energy_data_cur = $energy_data = $table_data_cur = [];
				$showLabel = "show_utility_" . $energy;
				if (isset($energy_modelling_data[$energy]['report']) && $energy_modelling_data[$energy]['report'] == 1 && $data['sites'][$key]['monthly']['site_detail'][$showLabel] == 1) {
					array_push($regressionUtility, $energy);
					$room_keys = $data['sites'][$key]['monthly']['site_detail']['rooms_keys'];

					$this->reports_energy_model->year_id = $filterArray['year'];
					$this->reports_energy_model->utilities_year = $filterArray['year'];
					$this->reports_energy_model->site_id = $filterArray['site_id'];

					$energy_modelling_data_cur = $this->reports_energy_model->get_energy_modelling();
				$utility_energy_modeling_cur = $energy_modelling_data_cur[$energy];

				$utilities = $this->reports_energy_model->getUtility();

					$baseline_regression_year = !empty($data['sites'][$key]['monthly']['site_detail']['baseline_regression_year']) ? $data['sites'][$key]['monthly']['site_detail']['baseline_regression_year'] : (date('Y') - 1);
					$this->reports_energy_model->utilities_year = ($baseline_regression_year);
					$utilities_LY = $this->reports_energy_model->getUtility();

					$energy_data[0] = [
						'Month',
						$utility_array[$energy]['Label'] . ' - ' . ($baseline_regression_year ),
						'Regression - ' . ($baseline_regression_year)
					];

					foreach ($utilities_LY as $utl_LY) {
						$consumtion = 0;
						$regression = 0;
						$days_of_month = (int) cal_days_in_month(CAL_GREGORIAN, $utl_LY['month_id'], $utl_LY['year_id']);
						$consumtion = !empty($utl_LY[$utility_array[$energy]['db_key']]) ? $utl_LY[$utility_array[$energy]['db_key']] : 0;

						$cdd = $utl_LY['cdd'];
						$hdd = $utl_LY['hdd'];

						$occupancy = round(($utl_LY['total_room_night'] / ($room_keys * $days_of_month)), 4);
						$regression = floatval($utility_energy_modeling_cur['x']) + (floatval($cdd) * floatval($utility_energy_modeling_cur['cdd'])) + (floatval($hdd) * floatval($utility_energy_modeling_cur['hdd'])) + (floatval($occupancy) * floatval($utility_energy_modeling_cur['occupancy'])) + (floatval($days_of_month) * floatval($utility_energy_modeling_cur['days']));

						$energy_data[] = [
							$fullmontharray[$utl_LY['month_id']],
							is_finite($consumtion) ? round($consumtion) : 0,
							is_finite($regression) ? round($regression) : 0
						];
					}

					$energy_data_cur[0] = [
						'Month',
						$utility_array[$energy]['Label'] . ' - ' . ($filterArray['year']),
						'Regression - ' . ($filterArray['year'])
					];

					foreach ($utilities as $utl) {
						$consumtion = $regression = 0;
						$consumtion = !empty($utl[$utility_array[$energy]['db_key']]) ? $utl[$utility_array[$energy]['db_key']] : 0;
						$cdd = $utl['cdd'];
						$hdd = $utl['hdd'];
						$days_of_month = (int) cal_days_in_month(CAL_GREGORIAN, $utl['month_id'], $utl['year_id']);
						$occupancy = round(($utl['total_room_night'] / ($room_keys * $days_of_month)), 4);
						$regression = floatval($utility_energy_modeling_cur['x']) + (floatval($cdd) * floatval($utility_energy_modeling_cur['cdd'])) + (floatval($hdd) * floatval($utility_energy_modeling_cur['hdd'])) + (floatval($occupancy) * floatval($utility_energy_modeling_cur['occupancy'])) + (floatval($days_of_month) * floatval($utility_energy_modeling_cur['days']));

						if ($utl['month_id'] > (date('m') - 1)) {
							$percent = 0;
						}

						if ($utl['month_id'] > (date('m') - 1) && $filterArray['year'] == date('Y')) {
							$consumtion = 0;
							$regression = 0;
							$percent = 0;
						}
						$energy_data_cur[] = [
							$fullmontharray[$utl['month_id']],
							is_finite($consumtion) ? round($consumtion) : 0,
							is_finite($regression) ? round($regression) : 0
						];

							$total_consumption_cur += $consumtion;
							$total_regression_cur += $regression;

							if ($consumtion == 0) {
								$percent = 100;
							} else {
								$percent = round((($consumtion - $regression) / $consumtion) * 100, 4);
							}

						$table_data_cur[substr($fullmontharray[$utl['month_id']],0,3)] = [
							'consumtion' => $consumtion,
							'regression' => $regression,
							'variation' => round($consumtion - $regression, 4),
							'precentage' => $percent
						];

					}

					foreach ($fullmontharray as $keyMonth => $valueMonth) {
						if($keyMonth >= sizeof($energy_data_cur)) {
							$energy_data_cur[] = [
								$fullmontharray[$valueMonth],
								0,
								0
							];
							$table_data_cur[substr($fullmontharray[$valueMonth],0,3)] = [
								'consumtion' => 0,
								'regression' => 0,
								'variation' => 0,
								'precentage' => 0
							];
						}
					}
					$data['sites'][$key]['monthly']['regression'][$energy.'_table_data_cur'] = $table_data_cur;
					$data['sites'][$key]['monthly']['regression'][$energy."_LY"] = $energy_data;
					$data['sites'][$key]['monthly']['regression'][$energy] = $energy_data_cur;
					$data['sites'][$key]['monthly']['regression']['energy_modelling_data'][$energy] = $utility_energy_modeling_cur;
					$data['sites'][$key]['monthly']['regression']['total_consumption_cur'][$energy] = $total_consumption_cur;
					$data['sites'][$key]['monthly']['regression']['total_regression_cur'][$energy] = $total_regression_cur;
				}
			}
			$data['sites'][$key]['monthly']['regression']['regressionUtility'] = $regressionUtility;
			$data['sites'][$key]['monthly']['regression']['utility_array'] = $utility_array;

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
				cron_log('[CRON-Start] ['. $report_post_type . '] trigger start for site id : ' . $siteID);

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

					'utility_regression_electricity_Img',
					'utility_regression_lpg_Img',
					'utility_regression_fuel_oil_Img',
					'utility_regression_natural_gas_Img',
					'utility_regression_district_cooling_Img',
					'utility_regression_district_heating_Img',

					'utility_regression_LY_electricity_Img',
					'utility_regression_LY_lpg_Img',
					'utility_regression_LY_fuel_oil_Img',
					'utility_regression_LY_natural_gas_Img',
					'utility_regression_LY_district_cooling_Img',
					'utility_regression_LY_district_heating_Img',

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

				$files['files'] = $this->generate_report_pdf($site, $data_get['type']);
				cron_log('[CRON-End] ['. $report_post_type . '] trigger for site id : ' . $siteID);
			}

			$this->sendMail($files);

			$this->logoutOnFly();

			echo '<script>

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


		$this->load->model('sites/sites_model');
		$this->sites_model->year = $filters['start_year'];
		$site_details = $this->sites_model->get_site_detail_custom($site_id);
		// FIlters for comparisional bar chart

		$filters_comparision_chart = array();

		$startdate = '1/' . date('Y');

		$enddate = CURRENT_YEAR_MAX_MONTH_ID . '/' . date('Y');

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
				if (isset($data['utility_cost_chart'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart'][$value['month_id']][$value['year_id']])) {
					continue;
				} else {
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
					$value['onsite_generator_fuel_oil'] = !empty($value['onsite_generator_fuel_oil']) ? $value['onsite_generator_fuel_oil'] : 0;
					$value['onsite_generator_natural_gas'] = !empty($value['onsite_generator_natural_gas']) ? $value['onsite_generator_natural_gas'] : 0;
					$value['renewable_energy'] = !empty($value['renewable_energy']) ? $value['renewable_energy'] : 0;

					$value['cdd'] = !empty($value['cdd']) ? $value['cdd'] : 0;

					$value['hdd'] = !empty($value['hdd']) ? $value['hdd'] : 0;

					$value['total_budget'] = !empty($value['total_budget']) ? $value['total_budget'] : 0;

					$value['total_purchased_electricity'] = !empty($value['total_purchased_electricity']) ? $value['total_purchased_electricity'] : 0;

					$value['total_purchased_electricity_cost'] = !empty($value['total_purchased_electricity_cost']) ? $value['total_purchased_electricity_cost'] : 0;

					$value['total_electricity_kwh'] = !empty($value['total_electricity_kwh']) ? $value['total_electricity_kwh'] : 0;

					$value['total_room_night'] = !empty($value['total_room_night']) ? $value['total_room_night'] : 0;
					$value['total_guest_nights'] = !empty($value['total_guest_nights']) ? $value['total_guest_nights'] : 0;
					$value['total_room_night_budget'] = !empty($value['total_room_night_budget']) ? $value['total_room_night_budget'] : 0;

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
							$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['onsite_generator_fuel_oil'] = (!empty($value['onsite_generator_fuel_oil'])) ? $value['onsite_generator_fuel_oil'] : 0;
							$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['onsite_generator_natural_gas'] = (!empty($value['onsite_generator_natural_gas'])) ? $value['onsite_generator_natural_gas'] : 0;

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
					$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_room_night_budget'] = $value['total_room_night_budget'];
					$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['guest_night'] = $value['total_guests'];
					$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_guests_budget'] = $value['total_guests_budget'];

					$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

					$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
				}
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



		$currentYear = date('Y');

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
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['total_room_night_budget'] = $value['total_room_night_budget'];
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['guest_night'] = $value['total_guests'];
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['total_guests_budget'] = $value['total_guests_budget'];
				
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['onsite_generator'] = (!empty($value['onsite_generator'])) ? $value['onsite_generator'] : 0;

				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['renewable_energy'] = (!empty($value['renewable_energy'])) ? $value['renewable_energy'] : 0;
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['onsite_generator_fuel_oil'] = (!empty($value['onsite_generator_fuel_oil'])) ? $value['onsite_generator_fuel_oil'] : 0;
				$data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['onsite_generator_natural_gas'] = (!empty($value['onsite_generator_natural_gas'])) ? $value['onsite_generator_natural_gas'] : 0;

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
			if($site_details['show_utility_electricity']) {
			$data['kwh_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? ($kwh_report_results_pre['electricity'] * $dataFactor['electricity']) : 0;
			}
			if($site_details['show_utility_fuel_oil']) {
			$data['kwh_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? ($kwh_report_results_pre['fuel'] * $dataFactor['fuel_oil']) : 0;
			}
			if($site_details['show_utility_lpg']) {
			$data['kwh_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? ($kwh_report_results_pre['lpg'] * $dataFactor['lpg']) : 0;
			}
			if($site_details['show_utility_natural_gas']) {
			$data['kwh_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? ($kwh_report_results_pre['natural_gas'] * $dataFactor['natural_gas']) : 0;
			}
			if($site_details['show_utility_district_heating']) {
			$data['kwh_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? ($kwh_report_results_pre['heating_district'] * $dataFactor['district_heating']) : 0;
			}
			if($site_details['show_utility_district_cooling']) {
			$data['kwh_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? ($kwh_report_results_pre['cooling_district'] * $dataFactor['district_cooling']) : 0;
			}
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


			if($site_details['show_utility_electricity']) {
			$data['cost_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? $kwh_report_results_pre['electricity'] : 0;
			}
			if($site_details['show_utility_fuel_oil']) {
			$data['cost_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? $kwh_report_results_pre['fuel'] : 0;
			}
			if($site_details['show_utility_lpg']) {
			$data['cost_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? $kwh_report_results_pre['lpg'] : 0;
			}
			if($site_details['show_utility_natural_gas']) {
			$data['cost_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? $kwh_report_results_pre['natural_gas'] : 0;
			}
			if($site_details['show_utility_district_heating']) {
			$data['cost_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? $kwh_report_results_pre['heating_district'] : 0;
			}
			if($site_details['show_utility_district_cooling']) {
			$data['cost_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? $kwh_report_results_pre['cooling_district'] : 0;
			}
			if($site_details['show_utility_water']) {
			$data['cost_pie_chart_pre']['water'] = (!empty($kwh_report_results_pre['water'])) ? $kwh_report_results_pre['water'] : 0;
			}
		} else {

			$data['cost_pie_chart_pre'] = array();
		}



		$currentYear = date('Y');

		$currentMonth = intval(date('m'));

		if ($currentMonth == 1) {

			$currentYear = $currentYear - 1;

			$currentMonth = 12;
		}

		$filters_pre['currentYear'] = $currentYear;

		$filters_pre['currentMonth'] = $currentMonth;



		$filters['filters_comparision_chart_pre'] = $filters_comparision_chart_pre;

		if (date('m') == 1) {

			$data['monthly_chart_data'] = $this->getMonthlyReportChart(12, (date('Y') - 1));
		} else {

			$data['monthly_chart_data'] = $this->getMonthlyReportChart((date('m') - 1), date('Y'));
		}

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
				$data['utility_cost_chart_5years'][$value['year_id']]['total_room_night_budget'] = $value['total_room_night_budget'];
				$data['utility_cost_chart_5years'][$value['year_id']]['guest_night'] = $value['total_guests'];
				$data['utility_cost_chart_5years'][$value['year_id']]['total_guests_budget'] = $value['total_guests_budget'];

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

		// KWH Pie chart for current year

		$filters['report_year'] = date('Y');

		$filters['report_month'] = date('m');

		$kwh_report_results = $this->reportscron_model->kwhUnitBasedReportForCurrentYear($filters);

		if (!empty($kwh_report_results)) {

			$kwh_report_results = array_map('intval', $kwh_report_results);


			if($site_details['show_utility_electricity']) {
			$data['kwh_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $dataFactor['electricity']) : 0;
			}
			if($site_details['show_utility_fuel_oil']) {
			$data['kwh_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $dataFactor['fuel_oil']) : 0;
			}
			if($site_details['show_utility_lpg']) {
			$data['kwh_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $dataFactor['lpg']) : 0;
			}
			if($site_details['show_utility_natural_gas']) {
			$data['kwh_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $dataFactor['natural_gas']) : 0;
			}
			if($site_details['show_utility_district_heating']) {
			$data['kwh_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $dataFactor['district_heating']) : 0;
			}
			if($site_details['show_utility_district_cooling']) {
			$data['kwh_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $dataFactor['district_cooling']) : 0;
			}
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

			if ($site_details['show_utility_electricity']) {
				$data['cost_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? $kwh_report_results['electricity'] : 0;
			}

			if ($site_details['show_utility_fuel_oil']) {
				$data['cost_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? $kwh_report_results['fuel'] : 0;
			}

			if ($site_details['show_utility_lpg']) {
				$data['cost_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? $kwh_report_results['lpg'] : 0;
			}

			if ($site_details['show_utility_natural_gas']) {
				$data['cost_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? $kwh_report_results['natural_gas'] : 0;
			}

			if ($site_details['show_utility_district_heating']) {
				$data['cost_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? $kwh_report_results['heating_district'] : 0;
			}

			if ($site_details['show_utility_district_cooling']) {
				$data['cost_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? $kwh_report_results['cooling_district'] : 0;
			}

			if ($site_details['show_utility_water']) {
				$data['cost_pie_chart']['water'] = (!empty($kwh_report_results['water'])) ? $kwh_report_results['water'] : 0;
			}
		} else {

			$data['cost_pie_chart'] = array();
		}

		// kWh pie chart for last 12 months

		$kwh_report_results = $this->reportscron_model->kwhUnitBasedReportForPreviousMonth($filters);

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



			if ($site_details['show_utility_electricity']) {
				$data['cost_pie_chart_previousmonth']['electricity'] = (!empty($kwh_report_results['electricity'])) ? $kwh_report_results['electricity'] : 0;
			}

			if ($site_details['show_utility_fuel_oil']) {
				$data['cost_pie_chart_previousmonth']['fuel'] = (!empty($kwh_report_results['fuel'])) ? $kwh_report_results['fuel'] : 0;
			}

			if ($site_details['show_utility_lpg']) {
				$data['cost_pie_chart_previousmonth']['lpg'] = (!empty($kwh_report_results['lpg'])) ? $kwh_report_results['lpg'] : 0;
			}

			if ($site_details['show_utility_natural_gas']) {
				$data['cost_pie_chart_previousmonth']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? $kwh_report_results['natural_gas'] : 0;
			}

			if ($site_details['show_utility_district_heating']) {
				$data['cost_pie_chart_previousmonth']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? $kwh_report_results['heating_district'] : 0;
			}

			if ($site_details['show_utility_district_cooling']) {
				$data['cost_pie_chart_previousmonth']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? $kwh_report_results['cooling_district'] : 0;
			}

			if ($site_details['show_utility_water']) {
				$data['cost_pie_chart_previousmonth']['water'] = (!empty($kwh_report_results['water'])) ? $kwh_report_results['water'] : 0;
			}
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

		$site_detail_result = $this->sites_model->get_site_detail_custom($site_id);

		$data['site_detail'] = $site_detail_result;
		$data['site_id'] = $site_id;


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

		$hotel_detail = $this->hotels_model->get_hotel_detail(1);



		// Site detail: load before assigning $this->sites_model->year (model state differs from other actions)

		$this->load->model('sites/sites_model');
		$this->sites_model->year = (date('Y') - 1);
		$result = $this->sites_model->get_site_detail_custom($site_id);

		$region_list = $this->sites_model->region_list();

		$country_list = $this->sites_model->country_list();

		$hotel_list = $this->sites_model->hotel_list();

		$data['site_detail'] = $result;
		$data['site_id'] = $site_id;
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

		define('PDF_HEADER_SITE_NAME', $result['site_location_name']);



		// Report labelling: PDF cover titles and sendMail() subjects follow "<site name> - Month (Month YYYY) ? Utilities Report" (site name = $result['site_location_name'], e.g. Bahrain; period from filters). Annual PDF uses "Full Year Utilities Report - {year}" below.

		if (!empty($this->input->post())) {

			$postdata = [];
			$postdata = $chartData;

			// Annual utilities PDF: titles like "Full Year Utilities Report - {previous calendar year}"; file name includes site and "annual report"

			if ($report_flag == 'annual') {

				$waste_chart = '';

				$waste_pie_chart = '';

				$waste_anuual_chart = '';

				$waste_anuual_pie_chart = '';

				$waste_monthly_reports = '';

				$waste_monthly_piechart_reports = '';



				$data['pdf_report_title'] = 'Full Year Utilities Report - ' . (date('Y') - 1);

				$data['previous_title'] = 'Previous Year - ' . (date('Y') - 2);

				$data['current_title'] = 'Current Year - ' . (date('Y') - 1);

				$data['budget_title'] = 'Budget - ' . (date('Y') - 1);

				$data['table_title'] = 'Annual Report';

				$data['type'] = 'annual';

				$data['show_site_details'] = false;

				$show_site_details = false;

				$show_piechart = true;

				$data['showCostBudgetVariance'] = true;



				$data['columnChartImg'] = $postdata['columnChartImg_hidden'];

				$data['pieChartImg'] = $postdata['pieChartImg_hidden'];

				$data['pieChartNewImg'] = $postdata['pieChartNewImg_hidden'];

				$data['wasteChartPreImg_hidden'] = $postdata['wasteChartPreImg_hidden'];

				$data['pieAnnualChartNewImg_hidden'] = $postdata['pieAnnualChartNewImg_hidden'];

				$data['pieAnnualLandfillImg_hidden'] = $postdata['pieAnnualLandfillImg_hidden'];

				$annualcurrentBudgetActualData = $data['currentBudgetActualData']['annual'];

				$data['currentBudgetActualData'] = $annualcurrentBudgetActualData;



				$filter_custom_notification = array(

					'year' => (date('Y') - 1),

				);

				$customnotifications = $this->sites_model->getSiteCustomNotifications($site_id, $filter_custom_notification);

				foreach ($customnotifications as $notification) {

					if ($notification['annual']) {

						$data['customNotifications'][] = $notification;
					}
				}


				/* Waste Page & Dashboard widget **START** in annual cron */
				// progress on Target report data
				$this->load->model('reports/reports_model');
				$this->load->model('sites/site_waste_model');
				$this->reports_model->site_id = $site_id;
				$progressOnTarget = array();
				$dateParams = getProgressWidgetDateParams();
				$current_month = $dateParams['month'];
				$current_year = $dateParams['year'];
				$running_year = $dateParams['running_year'];
				$baselineYear = $site_detail['baseline_regression_year'];
				$progressOnTargetMonthly = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear, 'month');
				$progressOnTarget = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear);
				$wasteDiversionNumeratorData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, 'recycling_wte', $current_year, $current_month);
				$totalWasteData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, '', $current_year, $current_month);

				$progressOnTarget[$baselineYear]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$baselineYear]) ? $wasteDiversionNumeratorData['YTDTotal'][$baselineYear] : 0;
				$progressOnTarget[$baselineYear]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0;
				$progressOnTarget[$running_year]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$running_year]) ? $wasteDiversionNumeratorData['YTDTotal'][$running_year] : 0;
				$progressOnTarget[$running_year]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0;

				$progressValueWasteYTD = [
					'total_waste_baseline_target' => isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0,
					'total_waste_target' => isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0
				];
				$dataNew = $currentMonth = $lastMonth = $sameMonthLastYear = [];
				$dataNew = $this->sites_model->getMySitesWidgetData($site_detail);
				$currentMonth = $dataNew[0] ?? [];
				$lastMonth = $dataNew[1] ?? [];
				$sameMonthLastYear = $dataNew[2] ?? [];
				$carbonData = [
					'carbon_footprint_currentMonth' => $currentMonth['carbon_footprint'] ?? 0,
					'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYear['carbon_footprint'] ?? 0,
					'ytd_carbon_footprint_new' => $dataNew['ytd_carbon_footprint_new'] ?? 0,
					'ytd_carbon_footprintPreviousYear' => $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0,
					'ytd_carbon_footprint_baseline_new' => $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0
				];
				$progressOnTargetResult = calculateProgressOnTarget(
					$progressOnTarget,
					$current_month,
					$current_year,
					$site_detail,
					$carbonData,
					$progressValueWasteYTD
				);
				$data['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
				$data['progressOnTargetMonthly'] = isset($progressOnTargetMonthly) ? $progressOnTargetMonthly : [];
				$data['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
				$data['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
				$data['progressTarget'] = $progressOnTargetResult['progressTarget'] ?? [];
				$data['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
				$data['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
				$data['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
				$data['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];
				$site_detials =  $this->sites_model->get_site_detail_custom($site_id);
				$data['site_detials'] = $site_detials;
				$data['carbon'] = $this->sites_model->getCarbonRecords($site_id, $site_detials);
				$content_progress_on_target_report = $this->load->view('admin_landing_progress_on_target_report', $data, true);
				// Waste report data
				$currYear = date('Y') - 1;
				$currMonth = 12;
				$data['waste']['total_room_night'] = $data['utility_cost_chart'][$currMonth][$currYear]['room_night'];
				$data['waste']['total_guests'] = $data['utility_cost_chart'][$currMonth][$currYear]['guest_night'];
				$data['waste']['WasteReport'] = $this->site_waste_model->getWasteReportData($site_id, $data['waste'], $currYear, $currMonth);
				if(empty($data['waste']['WasteReport'])) {
					$content_reports_waste_report_annual = '';
				} else {
					$content_reports_waste_report_annual = $this->load->view('admin_landing_pdf_reports_waste', $data['waste'], true);
				}
				/* Waste Page & Dashboard widget **END** in annual cron */

				$content_reports = $this->load->view('admin_landing_pdf_reports_hidden', $data, true);
				$content_reports_piecharts = $this->load->view('admin_landing_pdf_reports_hidden_piecharts', $data, true);
				$data['columnChartCarbonFootprintImg'] = $postdata['columnChartCarbonFootprintAnnualImg'];
				$content_reports_carbon_footprint = $this->load->view('admin_landing_pdf_reports_carbon_footprint_annual', $data, true);
				$content_site_detail = $this->load->view('admin_landing_pdf_site_detail', $data, true);

				$this->utilities_model->utilities_month = date("n") - 1;

				$this->utilities_model->utilities_year = date("Y") - 1;



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

				$lpg_value = 0;

				$electricity_value = 0;

				$natural_gas_value = 0;

				$fuel_value = 0;

				$heating_district_value = 0;

				$cooling_district_value = 0;

				foreach ($getUtilityData as $getUtilities) {
					$this->sites->model->year = date('Y') - 1;
					$site_detials = $this->sites_model->get_site_detail_custom($site_id);
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
					$this->sites->model->year = date('Y') - 2;
					$site_detials = $this->sites_model->get_site_detail_custom($site_id);
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







				$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

				$pdf->site_name = $data['site_detail']['site_location_name'];

				$pdf->site_logo = $data['site_detail']['site_logo'];



				$pdf->SetFont('helvetica', '', 9);

				$pdf->SetCreator(PDF_CREATOR);

				$pdf->SetPrintHeader(true);

				$pdf->SetPrintFooter(true);

				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				$pdf->SetMargins(10, 20, 10); // Left,Top,Right



				$pdfName = $site_detail['site_location_name'] . " " . "annual report " . date('dmY') . ".pdf";

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

				if ($content_reports_waste_report_annual != '') {
					$pdf->AddPage();
					$pdf->writeHTML($content_reports_waste_report_annual, true, false, true, false, '');
				}



				$file_name = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $pdfName;



				$pdf->Output($file_name, 'F'); // D - downlaod, F- Save

				$files['annual'] = $file_name;
			}



			/*
			 * Monthly utilities report PDF (stacked in same TCPDF as annual when post builds both; title example: "Monthly Utilities report - December 2018" via $data['monthly']['pdf_report_title']).
			 */

			$waste_chart = '';

			$waste_pie_chart = '';

			$waste_anuual_chart = '';

			$waste_anuual_pie_chart = '';

			$waste_monthly_reports = '';

			$waste_monthly_piechart_reports = '';



			$data['monthly']['currentBudgetActualData'] = $allcurrentBudgetActualData;

			$data['monthly']['pdf_report_title'] = 'Monthly Utilities report - ' . ($fullmontharray[$data['monthly']['filters']["filters_comparision_chart"]['start_month']]) . ' ' . ($data['monthly']['filters']["filters_comparision_chart"]['start_year']);

			$data['monthly']['previous_title'] = '' . ($montharray[$data['monthly']['filters']['filters_comparision_chart']['start_month']]) . ' ' . ($data['monthly']['filters']["filters_comparision_chart"]['start_year'] - 1);

			$data['monthly']['current_title'] = '' . ($montharray[$data['monthly']['filters']['filters_comparision_chart']['start_month']]) . ' ' . ($data['monthly']['filters']["filters_comparision_chart"]['start_year']);

			$data['monthly']['budget_title'] = 'Budget - ' . ($montharray[$data['monthly']['filters']['filters_comparision_chart']['start_month']]) . ' ' . ($data['monthly']['filters']["filters_comparision_chart"]['start_year']);

			$data['monthly']['table_title'] = 'Monthly Report';

			$data['monthly']['type'] = 'monthly';

			$data['monthly']['show_site_details'] = false;

			$show_piechart = true;

			$show_site_details = false;

			$show_actionplans = false;

			$data['monthly']['showCostBudgetVariance'] = true;

			$data['currentBudgetActualData'] = $allcurrentBudgetActualData;

			$monthlycurrentBudgetActualData = $data['currentBudgetActualData']['monthly'];

			$data['monthly']['currentBudgetActualData'] = $monthlycurrentBudgetActualData;

			$data['monthly']['columnChartImg'] = $postdata['columnChartImg'];

			$data['monthly']['columnChartImg_month'] = $postdata['columnChartImg_monthly'];

			$data['monthly']['pieChartImg'] = $postdata['pieChartImg'];

			$data['monthly']['pieChartNewImg'] = $postdata['pieChartNewImg'];

			$data['monthly']['pieChartNew2Img'] = $postdata['pieChartNew2Img'];

			$data['monthly']['pieChartNew3Img'] = $postdata['pieChartNew3Img'];

			$data['monthly']['wasteMonthlyChartImg'] = $postdata['wasteMonthlyChartImg_month'];

			$data['monthly']['wastePieLandfillMonthlyChartImg'] = $postdata['wastePieLandfillMonthlyChartImg'];

			$data['monthly']['wastePieMonthlyChartImg'] = $postdata['wastePieMonthlyChartImg_month'];

			$data['monthly']['columnChartCarbonFootprintImg'] = $postdata['columnChartCarbonFootprintImg'];

			$data['monthly']['utility_regression_electricity_Img'] = $postdata['utility_regression_electricity_Img'];
			$data['monthly']['utility_regression_lpg_Img'] = $postdata['utility_regression_lpg_Img'];
			$data['monthly']['utility_regression_fuel_oil_Img'] = $postdata['utility_regression_fuel_oil_Img'];
			$data['monthly']['utility_regression_natural_gas_Img'] = $postdata['utility_regression_natural_gas_Img'];
			$data['monthly']['utility_regression_district_cooling_Img'] = $postdata['utility_regression_district_cooling_Img'];
			$data['monthly']['utility_regression_district_heating_Img'] = $postdata['utility_regression_district_heating_Img'];
			$data['monthly']['utility_regression_LY_electricity_Img'] = $postdata['utility_regression_LY_electricity_Img'];
			$data['monthly']['utility_regression_LY_lpg_Img'] = $postdata['utility_regression_LY_lpg_Img'];
			$data['monthly']['utility_regression_LY_fuel_oil_Img'] = $postdata['utility_regression_LY_fuel_oil_Img'];
			$data['monthly']['utility_regression_LY_natural_gas_Img'] = $postdata['utility_regression_LY_natural_gas_Img'];
			$data['monthly']['utility_regression_LY_district_cooling_Img'] = $postdata['utility_regression_LY_district_cooling_Img'];
			$data['monthly']['utility_regression_LY_district_heating_Img'] = $postdata['utility_regression_LY_district_heating_Img'];

			$filter_custom_notification = array(

				'month' => $data['monthly']['filters']["filters_comparision_chart"]['start_month'],

				'year' => $data['monthly']['filters']["filters_comparision_chart"]['start_year'],

			);

			$data['monthly']['customNotifications'] = $this->sites_model->getSiteCustomNotifications($site_id, $filter_custom_notification);

			$data['monthly']['is_monthly'] = true;

			/* Dashboard widget in pdf START */
			// progress on Target report data
			$this->load->model('reports/reports_model');
			$this->load->model('sites/site_waste_model');
			$this->reports_model->site_id = $site_id;
			$progressOnTarget = array();
			$dateParams = getProgressWidgetDateParams();
			$current_month = $dateParams['month'];
			$current_year = $dateParams['year'];
			$running_year = $dateParams['running_year'];
			$baselineYear = $site_detail['baseline_regression_year'];
			$progressOnTargetMonthly = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear, 'month');
			$progressOnTarget = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear);
			$wasteDiversionNumeratorData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, 'recycling_wte', $current_year, $current_month);
			$totalWasteData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, '', $current_year, $current_month);

			$progressOnTarget[$baselineYear]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$baselineYear]) ? $wasteDiversionNumeratorData['YTDTotal'][$baselineYear] : 0;
			$progressOnTarget[$baselineYear]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0;
			$progressOnTarget[$running_year]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$running_year]) ? $wasteDiversionNumeratorData['YTDTotal'][$running_year] : 0;
			$progressOnTarget[$running_year]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0;

			$progressValueWasteYTD = [
				'total_waste_baseline_target' => isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0,
				'total_waste_target' => isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0
			];
			$dataNew = $currentMonth = $lastMonth = $sameMonthLastYear = [];
			$dataNew = $this->sites_model->getMySitesWidgetData($site_detail);
			$currentMonth = $dataNew[0] ?? [];
			$lastMonth = $dataNew[1] ?? [];
			$sameMonthLastYear = $dataNew[2] ?? [];
			$carbonData = [
				'carbon_footprint_currentMonth' => $currentMonth['carbon_footprint'] ?? 0,
				'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYear['carbon_footprint'] ?? 0,
				'ytd_carbon_footprint_new' => $dataNew['ytd_carbon_footprint_new'] ?? 0,
				'ytd_carbon_footprintPreviousYear' => $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0,
				'ytd_carbon_footprint_baseline_new' => $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0
			];
			$progressOnTargetResult = calculateProgressOnTarget(
				$progressOnTarget,
				$current_month,
				$current_year,
				$site_detail,
				$carbonData,
				$progressValueWasteYTD
			);
			$data['monthly']['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
			$data['monthly']['progressOnTargetMonthly'] = isset($progressOnTargetMonthly) ? $progressOnTargetMonthly : [];
			$data['monthly']['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
			$data['monthly']['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
			$data['monthly']['progressTarget'] = $progressOnTargetResult['progressTarget'] ?? [];
			$data['monthly']['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
			$data['monthly']['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
			$data['monthly']['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
			$data['monthly']['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];

			$data['monthly']['site_detials'] = $site_detail;
			$data['monthly']['carbon'] = $this->sites_model->getCarbonRecords($site_id, $site_detail);
			$content_progress_on_target_report = $this->load->view('admin_landing_progress_on_target_report', $data['monthly'], true);
			/* Dashboard widget in pdf END */
			
			$content_reports = $this->load->view('admin_landing_pdf_reports_monthly_hidden', $data['monthly'], true);
			
			$content_reports_piecharts = $this->load->view('admin_landing_pdf_reports_monthly_hidden_piecharts', $data['monthly'], true);
			
			$content_reports_carbon_footprint = $this->load->view('admin_landing_pdf_reports_carbon_footprint', $data['monthly'], true);
			// Waste report data
			$currYear = $data['monthly']['filters']['current_year'];
			$currMonth = $data['monthly']['filters']['previous_month'];
			$data['monthly']['waste']['total_room_night'] = $data['monthly']['utility_cost_chart'][$currMonth][$currYear]['room_night'];
			$data['monthly']['waste']['total_guests'] = $data['monthly']['utility_cost_chart'][$currMonth][$currYear]['guest_night'];
			$data['monthly']['waste']['WasteReport'] = $this->site_waste_model->getWasteReportData($site_id, $data['monthly']['waste'], $currYear, $currMonth);
			if(empty($data['monthly']['waste']['WasteReport'])) {
				$content_reports_waste_report = '';
			} else {
				$content_reports_waste_report = $this->load->view('admin_landing_pdf_reports_waste', $data['monthly']['waste'], true);
			}

			$content_site_detail = $this->load->view('admin_landing_pdf_site_detail', $data['monthly'], true);

			$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			$pdf->site_name = $data['site_detail']['site_location_name'];

			$pdf->site_logo = $data['site_detail']['site_logo'];



			$pdf->SetFont('helvetica', '', 9);

			$pdf->SetCreator(PDF_CREATOR);

			$pdf->SetPrintHeader(true);

			$pdf->SetPrintFooter(true);

			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			$pdf->SetMargins(10, 20, 10); // Left,Top,Right



			$pdfName = $site_detail['site_location_name'] . " " . "monthly report " . date('dmY') . ".pdf";

			$pdfName = strtolower(str_replace(array(' ', '-'), array('_', ''), $pdfName));



			if ($show_site_details) {

				$pdf->AddPage();

				$pdf->writeHTML($content_site_detail, true, false, true, false, '');
			}

			$pdf->AddPage();
			$pdf->writeHTML($content_progress_on_target_report);

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

			if ($content_reports_waste_report != '') {
				$pdf->AddPage();
				$pdf->writeHTML($content_reports_waste_report, true, false, true, false, '');
			}

			/* REGRESSION CHARTS POPULATE IN PDF */
			foreach ($data['monthly']['regression']['regressionUtility'] as $key => $currentUtility) {
				if ($data['monthly']['utility_regression_'.$currentUtility.'_Img'] != '' || $data['monthly']['utility_regression_LY_'.$currentUtility.'_Img'] != '') {

					$utility_energy_modeling_cur = $data['monthly']['regression']['energy_modelling_data'][$currentUtility];
					$total_consumption_cur = $data['monthly']['regression']['total_consumption_cur'][$currentUtility];
					$total_regression_cur = $data['monthly']['regression']['total_regression_cur'][$currentUtility];
					$total_variation_cur = (($total_consumption_cur - $total_regression_cur) / $total_consumption_cur) * 100;

					$pdf->AddPage();
					$html = '<br/><br/><br/><div style="border:2px solid  #f69546;">
						<table width="100%" cellpadding="4" cellspacing="4">
							<thead>
								<tr style="font-size:12px;color:blue;text-align: center;">
									<td><strong>Regression Analysis: '.$data['monthly']['regression']['utility_array'][$currentUtility]['Label'].'</strong></td>
								</tr>
							</thead>
							<tr>
								<td width="100%">
									<img style="height:200px !important; width:400px!important;margin-top:10% !important;" src="'. $data['monthly']['utility_regression_LY_'.$currentUtility.'_Img']. '" />
								</td>
							</tr>
							<tr class="col-sm-12 alert text-center font-18" style="padding: 2px;color: #ff0000;">
								<td style="text-align: center;">
									<strong>Regression Equation : ' . GetSiteUtilityUnitName($site_id, $currentUtility) . ' =
									' . round($utility_energy_modeling_cur['x'], 2) . '
									' . (!empty(round($utility_energy_modeling_cur['cdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['cdd'], 2) . ' * CDD )' : '') . '
									' . (!empty(round($utility_energy_modeling_cur['hdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['hdd'], 2) . ' * HDD )' : '') . '
									' . (!empty(round($utility_energy_modeling_cur['occupancy'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['occupancy'], 2) . ' * OCC )' : '') . '
									' . (!empty(round($utility_energy_modeling_cur['days'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['days'], 2) . ' * Days of month )' : '') . '
									</strong>
									<strong>R<sup>2</sup> : ' . round($utility_energy_modeling_cur['r2'], 2) . '</strong>
								</td>
							</tr>
							<tr><hr/></tr>
							<tr>
								<td width="60%">
									<img style="height:400px !important; width:400px!important;" src="'. $data['monthly']['utility_regression_'.$currentUtility.'_Img'].'" />
								</td>
								<td width="40%" style="margin:0px;padding:1px;">
								<br/><br/><br/><br/><br/><br/>
									<table style="border: 1px solid black; font-size: 8px !important; margin:0px;padding:1px;text-align: center;" class="table table-responsive table-striped">
										<thead>
											<tr>
												<th width="15%" style="font-weight: bold;color:blue;border: 1px solid #000000;border-right: 1px black solid;">Month</th>
												<th width="25%" style="font-weight: bold;color:blue;border: 1px solid #000000;">'.$data['monthly']['regression']['utility_array'][$currentUtility]['Label'] . ' - ' . ($data['monthly']['filters']['report_year']).'</th>
												<th width="25%" style="font-weight: bold;color:blue;border: 1px solid #000000;">'."Prediction - " . ($data['monthly']['filters']['report_year']).'</th>
												<th width="25%" style="font-weight: bold;color:blue;border: 1px solid #000000;">'."Variation (" . GetSiteUtilityUnitName($site_id, $currentUtility) . ")".'</th>
												<th width="10%" style="font-weight: bold;color:blue;border: 1px solid #000000;">'."(%)".'</th>
											</tr>
										</thead>
										<tbody>';
										foreach ($data['monthly']['regression'][$currentUtility.'_table_data_cur'] as $key => $table_energy) {
											$html .= '
											<tr>
												<td width="15%" style="border-right: 1px black solid;">'.$key.'</td>
												<td width="25%" style="text-align: center;">'.number_format($table_energy['consumtion']).'</td>
												<td width="25%" style="text-align: center;">'.number_format($table_energy['regression']).'</td>
												<td width="25%" style="text-align: center;">'.number_format($table_energy['variation']).'</td>
												<td width="10%" style="text-align: center;">'.number_format($table_energy['precentage']).'</td>
											</tr>
											';
											}
											$html .= '
											<tr>
												<th width="15%" style="border-top: 1px black solid;border-right: 1px black solid;"><strong>Total</strong></th>
												<th width="25%" style="border-top: 1px black solid;text-align: center;"><strong>'.number_format($total_consumption_cur).'</strong></th>
												<th width="25%" style="border-top: 1px black solid;text-align: center;"><strong>'.number_format($total_regression_cur).'</strong></th>
												<th width="25%" style="border-top: 1px black solid;text-align: center;"><strong>'.number_format($total_consumption_cur - $total_regression_cur).'</strong></th>
												<th width="10%" style="border-top: 1px black solid;text-align: center;"><strong>'.number_format($total_variation_cur).'</strong></th>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							</td>
							</tr>
						</table>
					</div>';
					$pdf->writeHtml($html, true, false, true, false, '');
				}
			}

			$file_name = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $pdfName;

			$pdf->Output($file_name, 'F'); // D - downlaod, F- Save



			$files['monthly'] = $file_name;

			// YTD (monthly_ytd) PDF: e.g. "YTD Utilities report - 2019" ? generated when the same run continues with report_flag mytd

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



			file_put_contents($imagePath, $url);

			$image_path = $imageUrl;
		}



		return $image_path;
	}



	public function get_image_from_uri1($url = '')

	{

		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function start " . date("Y-m-d H:i:s") . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		$userIdArray = array(123, 125, 139, 27, 279, 3, 30, 44, 48, 7, 91, 94);

		$url = $_POST['img_url'];



		$filenm = $_POST['filenm'];

		$user_id = $_POST['user_id'];

		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "User_id " . $user_id . "\n";

		fwrite($fh, $stringData);

		fclose($fh);

		$previousmonthdata = date("m/Y", strtotime(date('Y-m') . " -1 months"));

		$previousdateexplode = explode('/', $previousmonthdata);

		$previous_month = (int) $previousdateexplode[0];

		$i = 0;

		$imageName = $filenm . '.png';



		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function start " . $filenm . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		$imageDir = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $filenm;

		$imagePath = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $filenm . "/" . $imageName;

		if (!file_exists($imageDir)) {

			mkdir($imageDir, 0777, true);
		}

		$imageUrl = site_url() . "assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $filenm . "/" . $imageName;







		list($type, $url) = explode(';', $url);

		list(, $url) = explode(',', $url);

		$url = base64_decode($url);



		file_put_contents($imagePath, $url);



		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function end " . date("Y-m-d H:i:s") . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		echo $imageUrl;
	}



	public function get_image_from_uri($url = '')

	{

		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function start " . date("Y-m-d H:i:s") . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		$userIdArray = array(123, 125, 139, 27, 279, 3, 30, 44, 48, 7, 91, 94);

		$url = $_POST['img_url'];

		$filenm = $_POST['filenm'];

		$user_id = $_POST['user_id'];

		$region_id = $_POST['region_id_key'];

		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "User_id " . $user_id . "\n";

		fwrite($fh, $stringData);

		fclose($fh);

		$previousmonthdata = date("m/Y", strtotime(date('Y-m') . " -1 months"));

		$previousdateexplode = explode('/', $previousmonthdata);

		$previous_month = (int) $previousdateexplode[0];

		$i = 0;

		$imageName = $filenm . '.png';



		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function start " . $filenm . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		$imageDir = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm;

		$imagePath = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm . "/" . $imageName;

		if (!file_exists($imageDir)) {

			mkdir($imageDir, 0777, true);
		}

		$imageUrl = site_url() . "assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm . "/" . $imageName;







		list($type, $url) = explode(';', $url);

		list(, $url) = explode(',', $url);

		$url = base64_decode($url);



		file_put_contents($imagePath, $url);



		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "from get_image_from_uri function end " . date("Y-m-d H:i:s") . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		echo $imageUrl;
	}



	private function sendMail($files = array())

	{

		$request_report_type = $this->input->get('type');

		$site_id = $this->input->get('ni');

		$this->load->library('mailer');

		$this->mailer->mail->IsHTML(true);



		$users = $this->reportscron_model->getUserDetails();



		if (!empty($files)) {

			$site_name = $files['site_name'];

			// Email subject lines (informational): "<Site> - Month (Month YYYY) ? Utilities Reports" where Site = $site_name (e.g. Bahrain) and month/year = previous month context for the cron. Variants: "& YTD {year}" for mytd, or "and  Annual Utilities Reports" in January for annual+monthly.

			$current_month_text = date('F');

			$previous_month_text = date('F', strtotime($current_month_text . " last month"));

			$current_year = date('Y');

			$currentMonth = intval(date('m'));

			foreach ($users as $user) {

				if (empty($user['email'])) {

					continue;
				}
				$this->mailer->mail->AddAddress($user['email']);
				// $this->mailer->mail->AddAddress('garima.pandey@tatvasoft.com');

				$sites = $user['sites'];

				$reports = $user['reports'];

				// e.g. subject: "Bahrain - Month (December 2018) & YTD 2019 Utilities Reports" (December = $previous_month_text, year = $monthly_report_year; January uses subject without YTD part below)

				if (in_array('monthly_ytd', $reports) && $request_report_type == 'mytd' && in_array(trim($site_name), $sites)) {

					if (in_array(QUARTERLY_REPORT, $reports)) {

						$fisrtSiteId = array_key_first($sites);

						if ($fisrtSiteId != $site_id) {

							continue;
						}
					}

					if (file_exists($files['files']['monthly']) && file_exists($files['files']['ytd'])) {

						$monthly_report_year = $current_year;

						if ($currentMonth == 1) {

							$monthly_report_year = $monthly_report_year - 1;
						}



						$subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') & YTD ' . $current_year . ' Utilities Reports';

						if ($currentMonth == 1) {

							$subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') Utilities Reports';
						}



						$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

						$bodyHtml .= '<div>You can find attached the ' . $previous_month_text . ' ' . $monthly_report_year . ' and YTD ' . $current_year . ' Utilities report for ' . $this->hotel['hotel_name'] . ' - ' . $site_name . '</div>';

						$bodyHtml .= '<div>For more information and analysis, log on to <a href="' . base_url() . '">' . base_url() . '</a></div>';

						$email_template['html'] = $bodyHtml;

						$body = $this->load->view('email_template', $email_template, true);

						$this->mailer->mail->Subject = $subject;

						$this->mailer->mail->Body = $body;

						$this->mailer->mail->addAttachment($files['files']['monthly'], $site_name . ' Monthly Report.pdf');

						if ($currentMonth != 1) {

							$this->mailer->mail->addAttachment($files['files']['ytd'], $site_name . ' YTD Report.pdf');
						}

						$this->mailer->mail->Send();
					}
				}

				// e.g. January: "Bahrain - Month (December 2018) and  Annual Utilities Report" (matches $subject when $currentMonth == 1 below; spacing matches historical template)

				if (in_array('annual', $reports) && $request_report_type == 'annual' && in_array(trim($site_name), $sites)) {

					if (file_exists($files['files']['annual']) && file_exists($files['files']['monthly'])) {

						$monthly_report_year = $current_year;

						if ($currentMonth == 1) {

							$monthly_report_year = $monthly_report_year - 1;
						}

						$subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') & YTD ' . $current_year . ' Utilities Reports';

						if ($currentMonth == 1) {

							$subject = $site_name . ' - Month (' . $previous_month_text . ' ' . $monthly_report_year . ') and  Annual Utilities Reports';
						}

						$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';



						if ($currentMonth == 1) {



							$bodyHtml .= '<div>You can find attached the ' . $previous_month_text . ' ' . $monthly_report_year . ' and Annual ' . $monthly_report_year . ' Utilities report for ' . $this->hotel['hotel_name'] . ' - ' . $site_name . '</div>';
						} else {

							$bodyHtml .= '<div>You can find attached the ' . $previous_month_text . ' ' . $monthly_report_year . ' and YTD ' . $current_year . ' Utilities report for ' . $this->hotel['hotel_name'] . ' - ' . $site_name . '</div>';
						}

						$bodyHtml .= '<div>For more information and analysis, log on to <a href="' . base_url() . '">' . base_url() . '</a></div>';

						$email_template['html'] = $bodyHtml;

						$body = $this->load->view('email_template', $email_template, true);



						$this->mailer->mail->Subject = $subject;

						$this->mailer->mail->Body = $body;



						$this->mailer->mail->addAttachment($files['files']['annual'], $site_name . ' Annual Report.pdf');

						$this->mailer->mail->addAttachment($files['files']['monthly'], $site_name . ' Monthly Report.pdf');

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

		$startdateexplode = explode('/', $startdate);

		$enddateexplode = explode('/', $enddate);



		$filters_comparision_chart['startdate'] = (isset($startdate)) ? $startdate : '';

		$filters_comparision_chart['enddate'] = (isset($enddate)) ? $enddate : '';



		$filters_comparision_chart['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

		$filters_comparision_chart['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';

		$filters_comparision_chart['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';

		$filters_comparision_chart['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';

		$this->load->model('sites/sites_model');
		$this->sites_model->year = $filters_comparision_chart['start_year'];
		$site_details = $this->sites_model->get_site_detail_custom($this->session->userdata['hep_cron_session']['site_id']);

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

				if (isset($data['utility_cost_chart'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart'][$value['month_id']][$value['year_id']])) {
					continue;
				} else {


					$value = array_map('intval', $value);

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
					$utility_cost_chart[$value['month_id']][$value['year_id']]['onsite_generator_fuel_oil'] = (!empty($value['onsite_generator_fuel_oil'])) ? $value['onsite_generator_fuel_oil'] : 0;
					$utility_cost_chart[$value['month_id']][$value['year_id']]['onsite_generator_natural_gas'] = (!empty($value['onsite_generator_natural_gas'])) ? $value['onsite_generator_natural_gas'] : 0;

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
					$utility_cost_chart[$value['month_id']][$value['year_id']]['total_room_night_budget'] = $value['total_room_night_budget'];
					$utility_cost_chart[$value['month_id']][$value['year_id']]['guest_night'] = $value['total_guests'];
					$utility_cost_chart[$value['month_id']][$value['year_id']]['total_guests_budget'] = $value['total_guests_budget'];

					$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

					$utility_cost_chart[$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
				}
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

					$pre_data_fuel_consumption = (!empty($utility_cost_chart[$month][$prevYear]['fuel_consumption'])) ? ($utility_cost_chart[$month][$prevYear]['fuel_consumption'] - $utility_cost_chart[$month][$prevYear]['onsite_generator_fuel_oil']) : 0;

					$pre_data_lpg_consumption = (!empty($utility_cost_chart[$month][$prevYear]['lpg_consumption'])) ? $utility_cost_chart[$month][$prevYear]['lpg_consumption'] : 0;

					$pre_data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas_consumption'])) ? ($utility_cost_chart[$month][$prevYear]['natural_gas_consumption'] - $utility_cost_chart[$month][$prevYear]['onsite_generator_natural_gas']) : 0;

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

					$data_fuel_consumption = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? ($utility_cost_chart[$month][$year]['fuel_consumption'] - $utility_cost_chart[$month][$year]['onsite_generator_fuel_oil']) : 0;

					$data_lpg_consumption = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;

					$data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? ($utility_cost_chart[$month][$year]['natural_gas_consumption'] - $utility_cost_chart[$month][$year]['onsite_generator_natural_gas']) : 0;

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

		$data['chart_waste_data'] = [];

		$data['chart_pie_data'] = [];

		return $data;
	}



	public function upper_management_report()

	{

		$myFile = "logs.txt";

		$fh = fopen($myFile, 'a') or die("can't open file");

		$stringData = "CRON start : " . date("Y-m-d H:i:s") . "\n";

		fwrite($fh, $stringData);

		fclose($fh);



		$users = $this->reportscron_model->getUserDetails();



		$hotelName = $this->hotel['hotel_name'];

		$allChartData = array();

		$userIdArray = array(123, 125, 139, 27, 279, 3, 30, 44, 48, 7, 91, 94, 173, 174, 175, 180, 209, 212, 213);

		foreach ($users as $user) {



			if ($user['role_id'] == 1) {

				continue;
			}



			if (!empty($user['sites']) && in_array('upper_management', $user['reports'])) {

				$user_id = $user['id'];

				$data = array();

				$reportData = array();



				$this->load->model('sites/sites_model');

				$this->load->model('utilities/utilities_model');

				$this->load->model('dashboard/dashboard_model');

				$this->load->model('reports/reports_model');

				$this->load->model('reports/reports_forex_model');



				$site_ids = array();

				foreach ($user['sites'] as $key => $val) {

					$site_ids[] = $key;
				}

				if (!empty($site_ids)) {

					$site_filters = array();

					$site_filters['order_by'] = 'site_location_name';

					$site_filters['order'] = 'asc';

					$data['sites'] = $this->sites_model->get_site_detail_multiple($site_ids, $site_filters);



					if (!empty($data['sites'])) {



						foreach ($data['sites'] as $key => $site_detials) {

							$site_id = $site_detials['id'];



							// Get Actionplans for site

							$actionPlans = $this->projects_model->getAllActionPlansBySiteId($site_id);

							$data['sites'][$key]['data']['actionplan']['actionPlanCounts'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalCounts'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanOnholdCounts'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanInProgressCounts'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanCompleteCounts'] = 0;



							$data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalPercentage'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanOnholdPercentage'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanInProgressPercentage'] = 0;

							$data['sites'][$key]['data']['actionplan']['actionPlanCompletePercentage'] = 0;



							if (!empty($actionPlans)) {

								foreach ($actionPlans as $key1 => $value1) {

									if ($value1['status'] == 1) {

										$data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalCounts']++;
									} else if ($value1['status'] == 2) {

										$data['sites'][$key]['data']['actionplan']['actionPlanOnholdCounts']++;
									} else if ($value1['status'] == 3) {

										$data['sites'][$key]['data']['actionplan']['actionPlanInProgressCounts']++;
									} else if ($value1['status'] == 4) {

										$data['sites'][$key]['data']['actionplan']['actionPlanCompleteCounts']++;
									}



									$data['sites'][$key]['data']['actionplan']['actionPlanCounts']++;
								}
							}



							if ($data['sites'][$key]['data']['actionplan']['actionPlanCounts'] > 0) {

								if ($data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalCounts'] > 0) {

									$data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalPercentage'] = (($data['sites'][$key]['data']['actionplan']['actionPlanAwaitingApprovalCounts'] * 100) / $data['sites'][$key]['data']['actionplan']['actionPlanCounts']);
								}



								if ($data['sites'][$key]['data']['actionplan']['actionPlanOnholdCounts'] > 0) {

									$data['sites'][$key]['data']['actionplan']['actionPlanOnholdPercentage'] = (($data['sites'][$key]['data']['actionplan']['actionPlanOnholdCounts'] * 100) / $data['sites'][$key]['data']['actionplan']['actionPlanCounts']);
								}



								if ($data['sites'][$key]['data']['actionplan']['actionPlanInProgressCounts'] > 0) {

									$data['sites'][$key]['data']['actionplan']['actionPlanInProgressPercentage'] = (($data['sites'][$key]['data']['actionplan']['actionPlanInProgressCounts'] * 100) / $data['sites'][$key]['data']['actionplan']['actionPlanCounts']);
								}



								if ($data['sites'][$key]['data']['actionplan']['actionPlanCompleteCounts'] > 0) {

									$data['sites'][$key]['data']['actionplan']['actionPlanCompletePercentage'] = (($data['sites'][$key]['data']['actionplan']['actionPlanCompleteCounts'] * 100) / $data['sites'][$key]['data']['actionplan']['actionPlanCounts']);
								}
							}



							// Get KPI detail

							//carbon foot print

							$this->utilities_model->utilities_month = date("n") - 1;

							$this->utilities_model->utilities_year = date("Y");



							if ($this->utilities_model->utilities_month == 0) {

								$this->utilities_model->utilities_month = 12;

								$this->utilities_model->utilities_year = date("Y") - 1;
							}



							$this->utilities_model->site_id = $site_id;

							$getUtilities = $this->utilities_model->getUtilityWithForex();



							$currentMonth_footPrint = ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);



							$data['sites'][$key]['data']['kpi']['carbon_footprint_currentMonth'] = $currentMonth_footPrint;

							$data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] = $getUtilities['total_electricity_cost'] + $getUtilities['total_fuel_oil_cost'] + $getUtilities['total_lpg_cost'] + $getUtilities['total_natural_gas_cost'] + $getUtilities['district_heating_cost'] + $getUtilities['district_cooling_cost'] + $getUtilities['water_total_consumption_cost'] + $getUtilities['district_cooling_fixed_cost'] + $getUtilities['district_heating_fixed_cost'];



							$total_budgeted_cost_currentMonth = $getUtilities['electricity_total_budget_cost'] + $getUtilities['fuel_total_budget_cost'] + $getUtilities['lpg_total_budget_cost'] + $getUtilities['natural_gas_total_budget_cost'] + $getUtilities['district_heating_total_budget_cost'] + $getUtilities['district_cooling_total_budget_cost'] + $getUtilities['water_total_consumption_budget_cost'];



							$variation = ($data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] != '' && $total_budgeted_cost_currentMonth != '') ? $total_budgeted_cost_currentMonth - $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] : 0;

							$data['sites'][$key]['data']['kpi']['variation'] = $variation;

							$data['sites'][$key]['data']['kpi']['variationPercentage'] = $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] != '' ? ($variation * 100) / $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] : 0;



							//same month previous year added by hp18

							$this->utilities_model->utilities_month = date("n") - 1;

							$this->utilities_model->utilities_year = date("Y") - 1;



							if ($this->utilities_model->utilities_month == 0) {

								$this->utilities_model->utilities_month = 12;

								$this->utilities_model->utilities_year = date("Y") - 2;
							}



							$this->utilities_model->site_id = $site_id;

							$utilitiesSameMonthPreviousYear = $this->utilities_model->getUtilityWithForex();

							$SameMonthPreviousYear_footPrint = ($utilitiesSameMonthPreviousYear['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($utilitiesSameMonthPreviousYear['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($utilitiesSameMonthPreviousYear['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($utilitiesSameMonthPreviousYear['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($utilitiesSameMonthPreviousYear['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);



							$data['sites'][$key]['data']['kpi']['carbon_footprint_SameMonthPreviousYear'] = $SameMonthPreviousYear_footPrint;



							// YTD

							$ytd_carbon_footprint = $data['sites'][$key]['data']['kpi']['carbon_footprint_currentMonth'];

							$total_utility_costs = $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'];

							$total_budgeted_costs = $total_budgeted_cost_currentMonth;

							$currentMonth_footPrint_new = 0;

							if (date("n") > 1) {

								for ($i = 1; $i <= (date("n") - 1); $i++) {

									$this->utilities_model->utilities_month = $i;

									$this->utilities_model->utilities_year = date("Y");



									$getUtilities = $this->utilities_model->getUtilityWithForex();

									$currentMonth_footPrint_new += ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
								}
							} else {

								for ($i = 1; $i <= 12; $i++) {

									$this->utilities_model->utilities_month = $i;

									$this->utilities_model->utilities_year = date("Y") - 1;



									$getUtilities = $this->utilities_model->getUtilityWithForex();

									$currentMonth_footPrint_new += ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
								}
							}



							for ($i = 1; $i <= date("n"); $i++) {

								$this->utilities_model->utilities_month = $i;

								$this->utilities_model->utilities_year = date("Y");



								$YtdUtilities = $this->utilities_model->getUtilityWithForex();

								$ytd_carbon_footprint += ($YtdUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($YtdUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($YtdUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($YtdUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($YtdUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);



								//For variation

								$total_utility_costs += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'];



								$total_budgeted_costs += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
							}



							//ytd variation

							if (date("n") > 1) {

								$total_utility_costs_variation = 0;

								$total_budgeted_costs_variation = 0;

								for ($i = 1; $i <= (date("n") - 1); $i++) {

									$this->utilities_model->utilities_month = $i;

									$this->utilities_model->utilities_year = date("Y");



									$YtdUtilities = $this->utilities_model->getUtilityWithForex();



									//For variation

									$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'];



									$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
								}
							} else {

								$total_utility_costs_variation = 0;

								$total_budgeted_costs_variation = 0;

								for ($i = 1; $i <= 12; $i++) {

									$this->utilities_model->utilities_month = $i;

									$this->utilities_model->utilities_year = date("Y") - 1;



									$YtdUtilities = $this->utilities_model->getUtilityWithForex();

									//For variation

									$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'] + $YtdUtilities['district_cooling_fixed_cost'] + $YtdUtilities['district_heating_fixed_cost'];



									$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
								}
							}



							$variation_ytd = ($total_utility_costs_variation != '' && $total_budgeted_costs_variation != '') ? $total_budgeted_costs_variation - $total_utility_costs_variation : 0;

							$data['sites'][$key]['data']['kpi']['variation_ytd'] = $variation_ytd;

							$data['sites'][$key]['data']['kpi']['variationPercentage_ytd'] = $total_utility_costs_variation != '' ? ($variation_ytd * 100) / $total_utility_costs_variation : 0;



							$data['sites'][$key]['data']['kpi']['ytd_carbon_footprint'] = $ytd_carbon_footprint;

							$data['sites'][$key]['data']['kpi']['ytd_carbon_footprint_new'] = $currentMonth_footPrint_new;



							$currentMonth_cost_roomNight = ($data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] != '' && $getUtilities['total_room_night']) ? $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] / $getUtilities['total_room_night'] : 0;



							//last month - utilities cost/room night

							$this->utilities_model->utilities_month = date('n') - 2;

							$this->utilities_model->utilities_year = date("Y");



							if ($this->utilities_model->utilities_month == -1) {

								$this->utilities_model->utilities_month = 11;

								$this->utilities_model->utilities_year = date("Y") - 1;
							} else if ($this->utilities_model->utilities_month == 0) {

								$this->utilities_model->utilities_month = 12;

								$this->utilities_model->utilities_year = date("Y") - 1;
							}



							$getUtilities_lastMonth = $this->utilities_model->getUtilityWithForex();



							$data['sites'][$key]['data']['kpi']['total_utility_cost_lastMonth'] = $getUtilities_lastMonth['total_electricity_cost'] + $getUtilities_lastMonth['total_fuel_oil_cost'] + $getUtilities_lastMonth['total_lpg_cost'] + $getUtilities_lastMonth['total_natural_gas_cost'] + $getUtilities_lastMonth['district_heating_cost'] + $getUtilities_lastMonth['district_cooling_cost'] + $getUtilities_lastMonth['water_total_consumption_cost'] + $getUtilities_lastMonth['district_cooling_fixed_cost'] + $getUtilities_lastMonth['district_heating_fixed_cost'];



							$lastMonth_cost_roomNight = ($data['sites'][$key]['data']['kpi']['total_utility_cost_lastMonth'] != '' && $getUtilities_lastMonth['total_room_night']) ? $data['sites'][$key]['data']['kpi']['total_utility_cost_lastMonth'] / $getUtilities_lastMonth['total_room_night'] : 0;



							//same month last year- utilities cost/room night

							$this->utilities_model->utilities_month = date('n') - 1;

							$this->utilities_model->utilities_year = date("Y", strtotime("-1 year"));



							if ($this->utilities_model->utilities_month == 0) {

								$this->utilities_model->utilities_month = 12;

								$this->utilities_model->utilities_year = date("Y") - 2;
							}



							$getUtilities_sameMonth_lastYear = $this->utilities_model->getUtilityWithForex();



							$data['sites'][$key]['data']['kpi']['total_utility_cost_sameMonth_lastYear'] = $getUtilities_sameMonth_lastYear['total_electricity_cost'] + $getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] + $getUtilities_sameMonth_lastYear['total_lpg_cost'] + $getUtilities_sameMonth_lastYear['total_natural_gas_cost'] + $getUtilities_sameMonth_lastYear['district_heating_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_cost'] + $getUtilities_sameMonth_lastYear['water_total_consumption_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] + $getUtilities_sameMonth_lastYear['district_heating_fixed_cost'];



							$sameMonth_lastYear_cost_roomNight = ($data['sites'][$key]['data']['kpi']['total_utility_cost_sameMonth_lastYear'] != '' && $getUtilities_sameMonth_lastYear['total_room_night']) ? $data['sites'][$key]['data']['kpi']['total_utility_cost_sameMonth_lastYear'] / $getUtilities_sameMonth_lastYear['total_room_night'] : 0;



							$data['sites'][$key]['data']['kpi']['currentMonth_cost_roomNight'] = $currentMonth_cost_roomNight;

							$data['sites'][$key]['data']['kpi']['lastMonth_cost_roomNight'] = $lastMonth_cost_roomNight;

							$data['sites'][$key]['data']['kpi']['sameMonth_lastYear_cost_roomNight'] = $sameMonth_lastYear_cost_roomNight;



							$currentMonth_cost_roomNight = ($data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] != '' && $getUtilities['total_room_night']) ? $data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] / $getUtilities['total_room_night'] : 0;



							//last month - utilities cost/room night

							$this->utilities_model->utilities_month = date('n') - 1;

							$this->utilities_model->utilities_year = date("Y");



							if ($this->utilities_model->utilities_month == -1) {

								$this->utilities_model->utilities_month = 11;

								$this->utilities_model->utilities_year = date("Y") - 1;
							} else if ($this->utilities_model->utilities_month == 0) {

								$this->utilities_model->utilities_month = 12;

								$this->utilities_model->utilities_year = date("Y") - 1;
							}



							$getUtilities_lastMonth = $this->utilities_model->getUtilityWithForex();





							/*hdd cdd added in report*/

							$utility_cost_calculation_chr['cdd']['consumption'] = ($getUtilities_sameMonth_lastYear['cdd'] != '' && $getUtilities_lastMonth['cdd']) ? ($getUtilities_lastMonth['cdd'] - $getUtilities_sameMonth_lastYear['cdd']) * 100 / $getUtilities_sameMonth_lastYear['cdd'] : 0;

							$utility_cost_calculation_chr['cdd']['consumption'] = $utility_cost_calculation_chr['cdd']['consumption'];

							$utility_cost_calculation_chr['cdd']['title'] = 'CDD';

							$utility_cost_calculation_chr['cdd']['consumption_image'] = $getUtilities_sameMonth_lastYear['cdd'] < $getUtilities_lastMonth['cdd'] ? 'upArrow.png' : 'downArrow.png';



							$utility_cost_calculation_chr['hdd']['consumption'] = ($getUtilities_sameMonth_lastYear['hdd'] != '' && $getUtilities_lastMonth['hdd']) ? ($getUtilities_lastMonth['hdd'] - $getUtilities_sameMonth_lastYear['hdd']) * 100 / $getUtilities_sameMonth_lastYear['hdd'] : 0;

							$utility_cost_calculation_chr['hdd']['consumption'] = $utility_cost_calculation_chr['hdd']['consumption'];

							$utility_cost_calculation_chr['hdd']['title'] = 'HDD';

							$utility_cost_calculation_chr['hdd']['consumption_image'] = $getUtilities_sameMonth_lastYear['hdd'] < $getUtilities_lastMonth['hdd'] ? 'upArrow.png' : 'downArrow.png';



							// if it is higher than last year, thn it will be green arrow up

							$utility_cost_calculation_chr['room_nights']['consumption'] = ($getUtilities_sameMonth_lastYear['total_room_night'] != '' && $getUtilities_lastMonth['total_room_night']) ? ($getUtilities_lastMonth['total_room_night'] - $getUtilities_sameMonth_lastYear['total_room_night']) * 100 / $getUtilities_sameMonth_lastYear['total_room_night'] : 0;

							$utility_cost_calculation_chr['room_nights']['consumption'] = $utility_cost_calculation_chr['room_nights']['consumption'];

							$utility_cost_calculation_chr['room_nights']['title'] = 'Room Nights';

							// $utility_cost_calculation_chr['room_nights']['consumption_image'] = $getUtilities_sameMonth_lastYear['room_nights'] < $getUtilities_lastMonth['room_nights'] ? 'downArrowRed.png' : 'upArrowGreen.png';

							$utility_cost_calculation_chr['room_nights']['consumption_image'] = $getUtilities_sameMonth_lastYear['total_room_night'] < $getUtilities_lastMonth['total_room_night'] ? 'upArrowGreen.png' : 'downArrowRed.png';



							$data['sites'][$key]['cdd_hdd'] = $utility_cost_calculation_chr;

							// $cdd_hdd_arr[] = $utility_cost_calculation_chr;



							/*hdd cdd added in report*/



							//get notifications

							// Load after prior $this->utilities_model->? assignments in this loop
							$this->load->model('utilities/utilities_model');

							$this->utilities_model->site_id = $site_id;

							$notifications = $this->utilities_model->getNotifications();

							$data['sites'][$key]['notifications'] = $notifications;



							// Default notifications for electricity,water and Thermal (Fuel + LPG + Natural Gaz + District Energy)
							$siteId = $this->session->userdata['hep_cron_session']['site_id'];
							$dataFactor = getMmbtuFactorConversionAllUtility($siteId);


							$filters_notification = array();

							$filters_notification['previousmonth'] = date("F-Y", strtotime(date('Y-m') . " -13 months"));

							$filters_notification['currentmonth'] = date("F-Y", strtotime(date('Y-m') . " -1 months"));

							$filters_notification['pmonth'] = (int) date("m", strtotime(date('Y-m') . " -13 months"));

							$filters_notification['pyear'] = date("Y", strtotime(date('Y-m') . " -13 months"));

							$filters_notification['cmonth'] = (int) date("m", strtotime(date('Y-m') . " -1 months"));

							$filters_notification['cyear'] = date("Y", strtotime(date('Y-m') . " -1 months"));

							$this->dashboard_model->site_id = $site_id;



							// Based on unit

							$utilityForLastMonthCompare_result = $this->dashboard_model->getUtilityComparisionForLastMonthWithForex($filters_notification);

							$utilityForLastMonthCompare = array();

							foreach ($utilityForLastMonthCompare_result as $value) {

								$value['total_utility'] = ($value['electricity'] + $value['fuel'] + $value['lpg'] + $value['natural_gas'] + $value['heating_district'] + $value['district_heating_fixed_cost'] + $value['cooling_district'] + $value['district_cooling_fixed_cost'] + $value['water']);

								$utilityForLastMonthCompare[$value['year_id']][$value['month_id']] = $value;
							}



							// Based on total cost

							$utilityForLastMonthCompare_unit_result = $this->dashboard_model->getUtilityComparisionForLastMonthByUnit($filters_notification);

							$utilityForLastMonthCompare_unit = array();

							foreach ($utilityForLastMonthCompare_unit_result as $value) {



								$value['total_utility'] = ($value['electricity'] + $value['fuel'] + $value['lpg'] + $value['natural_gas'] + $value['heating_district'] + $value['cooling_district'] + $value['water']);



								// Convert to MBTU data

								$value['fuel'] = $value['fuel'] * $dataFactor['fuel_oil'];

								$value['lpg'] = $value['lpg'] * $dataFactor['lpg'];

								$value['natural_gas'] = $value['natural_gas'] * $dataFactor['natural_gas'];

								$value['heating_district'] = $value['heating_district'] * $dataFactor['district_heating'];

								$value['cooling_district'] = $value['cooling_district'] * $dataFactor['district_cooling'];



								$utilityForLastMonthCompare_unit[$value['year_id']][$value['month_id']] = $value;
							}



							$data['sites'][$key]['utilityForLastMonthCompare'] = $utilityForLastMonthCompare;

							$data['sites'][$key]['utilityForLastMonthCompare_unit'] = $utilityForLastMonthCompare_unit;

							$data['sites'][$key]['filters_notification'] = $filters_notification;



							//co2 emission start

							// FIlters for comparisional bar chart

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

							$enddate = '12/' . date('Y');



							// Change filter for monthly report pdf // conditional

							if ($monthly) {

								$startdate = $this->input->post('monthly_report_month', 1) . '/' . $this->input->post('monthly_report_year', date('Y'));

								$enddate = $this->input->post('monthly_report_month', 12) . '/' . $this->input->post('monthly_report_year', date('Y'));
							}



							$startdateexplode = explode('/', $startdate);

							$enddateexplode = explode('/', $enddate);



							$filters_comparision_chart['previousmonth'] = date("F-Y", strtotime(date('Y-m') . " -13 months"));

							$filters_comparision_chart['currentmonth'] = date("F-Y", strtotime(date('Y-m') . " -1 months"));

							$filters_comparision_chart['pmonth'] = (int) date("m", strtotime(date('Y-m') . " -13 months"));

							$filters_comparision_chart['pyear'] = date("Y", strtotime(date('Y-m') . " -13 months"));

							$filters_comparision_chart['cmonth'] = (int) date("m", strtotime(date('Y-m') . " -1 months"));

							$filters_comparision_chart['cyear'] = date("Y", strtotime(date('Y-m') . " -1 months"));



							$filters_comparision_chart['previous_month'] = (int) $previousdateexplode[0];

							$filters_comparision_chart['previous_year'] = $previousdateexplode[1];



							$filters_comparision_chart['startdate'] = (isset($startdate)) ? $startdate : '';

							$filters_comparision_chart['enddate'] = (isset($enddate)) ? $enddate : '';



							$filters_comparision_chart['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

							$filters_comparision_chart['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';

							$filters_comparision_chart['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';

							$filters_comparision_chart['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';

							$filters_comparision_chart['site_id'] = $site_id;

							$utility_cost_chart_results = $this->reports_model->utilityCostBarChartUpperManagement($filters_comparision_chart);



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

								$totalElectricityConsumption = 0;





								// $result = array_map('intval', $utility_cost_chart_results);



								foreach ($utility_cost_chart_results as $key1 => $value) {



									$value = array_map('intval', $value);



									$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];

									$value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];

									$data['sites'][$key]['utility_cost_chart'] = $value;



									$totalElectricity += $value['electricity'];

									$totalFuel += $value['fuel'];

									$totalFuelConsumption += $value['fuel_consumption'];

									$totalLpg += $value['lpg'];

									$totalLpgConsumption += $value['lpg_consumption'];

									$totalNaturalGas += $value['natural_gas_consumption'];

									$totalNaturalGasConsumption += $value['natural_gas_consumption'];

									$totalWater += $value['water'];

									$totalWaterConsumption += $value['water_consumption'];

									$totalHeatingDistrict += $value['heating_district'];

									$totalHeatingDistrictConsumption += $value['heating_district_consumption'];

									$totalCoolingDistrict += $value['cooling_district'];

									$totalCoolingDistrictConsumption += $value['cooling_district_consumption'];

									$totalElectricityConsumption += $value['total_electricity_kwh'];



									switch ($cview) {

										case 'roomnight':

											$cview_file = 'admin_index_roomnight';

											$data['utility_cost_chart']['utility_cost_chart_title'] = lang('utility-cost-chart-roomnight-title');

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

											$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;

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
											$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['onsite_generator_fuel_oil'] = (!empty($value['onsite_generator_fuel_oil'])) ? $value['onsite_generator_fuel_oil'] : 0;
											$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['onsite_generator_natural_gas'] = (!empty($value['onsite_generator_natural_gas'])) ? $value['onsite_generator_natural_gas'] : 0;

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
									$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_room_night_budget'] = $value['total_room_night_budget'];
									$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['guest_night'] = $value['total_guests'];
									$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['total_guests_budget'] = $value['total_guests_budget'];
									$days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);

									$data['utility_cost_chart'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
								}
							} else {

								$data['utility_cost_chart'] = array();
							}
						}
					}

					$filter_site_ids = array();

					foreach ($data['sites'] as $key => $value) {

						if ($value['id'] != '') {

							$filter_site_ids[] = $value['id'];
						}
					}

					$filters['site_ids'] = implode(',', $filter_site_ids);



					// for 3 charts of total_utilities_by_room_night_and_build_area



					$chart_data = array();

					$report_type = 'total_utilities_by_room_night_and_build_area';

					$startdate = date("m/Y", strtotime(date('Y-m') . " -1 months"));

					$time_type = 'sites_select_choose_month';

					$site_type = 0;

					// site_custom_filter: optional subset of site ids (array); empty = all $filter_site_ids. Other entry points may set from comma-separated POST via explode.
					$site_custom_filter = array();


					$site_filters['site_type'] = $site_type;

					$site_filters['site_ids'] = $site_custom_filter;



					$sites = $data['sites'];



					if (!empty($sites)) {



						switch ($time_type) {



							case 'sites_select_choose_month':

								$startdateexplode = explode('/', $startdate);

								$filters['month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';

								$filters['year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';



								$results = $this->reports_forex_model->allsitesUtilityBasedReportByMonth($filters);



								$tariff_results = $this->reports_model->allsitesTariffBasedReportByMonth($filters);



								// Tariff Result

								$days_of_month = cal_days_in_month(CAL_GREGORIAN, $filters['month'], $filters['year']);



								if (!empty($results)) {

									foreach ($results as $key => $result) {

										$results[$key]['cooling_district_cost'] = $result['cooling_district_cost'] + $result['district_cooling_fixed_cost'];

										$results[$key]['heating_district_cost'] = $result['heating_district_cost'] + $result['district_heating_fixed_cost'];



										$result['occupancy'] = (($result['total_room_night'] / ($result['rooms_keys'] * $days_of_month)) * 100);



										if ($report_type == 'water_liters_per_room_night') {

											$result['water'] = $result['water'] > 0 && $result['total_room_night'] > 0 ? $result['water'] / $result['total_room_night'] : 0;
										} else if ($report_type == 'electricity_kwh_per_room_night') {

											$result['electricity'] = $result['electricity'] > 0 && $result['total_room_night'] > 0 ? $result['electricity'] / $result['total_room_night'] : 0;
										} else if ($report_type == 'water_liters') {

											$result['water_liters'] = $result['water'] > 0 ? $result['water'] : 0;
										} else if ($report_type == 'electricity_kwh') {

											$result['electricity_kwh'] = $result['electricity'] > 0 ? $result['electricity'] : 0;
										}



										$reportData[$result['site_id']] = $result;
									}
								}



								if (!empty($tariff_results)) {

									foreach ($tariff_results as $tresult) {

										$reportData[$tresult['site_id']]['tariff'] = $tresult['tariff'];
									}
								}

								break;



							default:

								break;
						}



						// For electricity kWh scatter report only(To show previous year data)

						if ($report_type == 'electricity_consumption_site_efficiency_benchmark' or $report_type == 'electricity_cost_consumption_site_efficiency_benchmark' or $report_type == 'utilities_cost_consumption_site_efficiency_benchmark') {

							$filters_more['month'] = $filters['month']; //12;

							$filters_more['year'] = $filters['year'] - 1; //(date('Y')-1);

							$pa_months = $filters_more['month'];

							$filters_more['site_ids'] = implode(',', $filter_site_ids);



							if ($report_type == "utilities_cost_consumption_site_efficiency_benchmark") {

								$previous_results = $this->reports_forex_model->allsitesUtilityBasedReportByAvg($filters_more); // Utility Result

							} else {

								$previous_results = $this->reports_model->allsitesUtilityBasedReportByAvg($filters_more); // Utility Result

							}



							// Devide value with average months

							if (!empty($previous_results)) {

								foreach ($previous_results as $keyY => $presult) {



									$previous_results[$keyY]['cooling_district_cost'] = $presult['cooling_district_cost'] + $presult['district_cooling_fixed_cost'];

									$previous_results[$keyY]['heating_district_cost'] = $presult['heating_district_cost'] + $presult['district_heating_fixed_cost'];



									$presult['occupancy'] = ($presult['occupancy'] * 100);



									$reportData['previousdata'][$presult['site_id']] = $presult;
								}
							}
						}



						// Set template and report data

						$filters['is_buildarea'] = true;

						$report_title = 'sites_total_utilities_by_room_night_and_build_area_report_title';

						$x_axis_title = 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')';

						$filters['utility_type'] = '';

						$report_tmpl = 'admin_sites_all_utilities';
					}



					$filters['site_custom_filter'] = $site_custom_filter;

					$filters['startdate'] = (isset($startdate)) ? $startdate : '';

					$chart_data['sites'] = $sites;

					$chart_data['sites_list'] = $sites;

					$chart_data['reportdata'] = $reportData;

					$chart_data['report_type'] = $report_type;

					$chart_data['time_type'] = $time_type;

					$chart_data['site_type'] = $site_type;

					$chart_data['report_title'] = $report_title;

					$chart_data['x_axis_title'] = $x_axis_title;

					$chart_data['filters'] = $filters;

					$chart_data['data'] = $data;

					$chart_data['user'] = $user;





					unset($reportData);

					unset($report_type);

					unset($data);
				}

				$allChartData[] = $chart_data;
			}
		}



		$view_data = $this->load->view('admin_sites_all_utilities_new', array('allChartData' => $allChartData), true);

		echo $view_data;



		echo '<script>

	    console.log("complete_pdf_cron");

	</script>';

		echo "END";

		exit;
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

	public function generate_combined_eui_chart()
	{
		// Build ranking (unchanged logic)
		$sites = $this->sites_model->get_site_detail_multiple();
		$siteCronSettings = $this->sites_model->getSiteCronSettings();
		$monthlyTickedSites = array();
		foreach ($siteCronSettings as $cronSettings) {
			if ($cronSettings['site_cron_settings']['cron_type'] == 'MONTHLY') {
				array_push($monthlyTickedSites,  $cronSettings['site_cron_settings']['site_id']);
			}
		}

		$this->load->model('reports/reports_model');
		$allSiteRanking = [];
		// Same "last completed month" as dashboard progress widget
		$dateParams = getProgressWidgetDateParams();
		$currentMonth = (int) $dateParams['month'];
		$currentYear = (int) $dateParams['year'];
		foreach ($sites as $site) {
			$site_id = $site['id'];
			if (!$site_id) continue;
			if (!in_array($site_id, $monthlyTickedSites)) continue;
			$this->reports_model->site_id = $site_id;
			$groupUtilityChartData = $this->reports_model->groupUtilityChart();
			if (empty($groupUtilityChartData)) continue;

			$totalPerRoomNight = $totalPerMeterSquare = $electricity = $electricityPerMeter = $gases = $gasesPerMeter =
			$others = $othersPerMeter = 0;

			foreach ($groupUtilityChartData as $value) {
				// Keep only the exact target month+year (was &&, which kept any matching month OR year)
				if ((int) $value['month_id'] != $currentMonth || (int) $value['year_id'] != $currentYear) continue;
				$electricity =
					(!empty($value['electricity']) && !empty($value['room_night']))
					? round(($value['electricity'] / $value['room_night']), 2)
					: 0;

				$gases =
					(!empty($value['gases']) && !empty($value['room_night']))
					? round(($value['gases'] / $value['room_night']), 2)
					: 0;

				$others =
					(!empty($value['others']) && !empty($value['room_night']))
					? round(($value['others'] / $value['room_night']), 2)
					: 0;

				$electricityPerMeter =
					(!empty($value['electricity']) && !empty($value['cooled_builtup_area']))
					? round(($value['electricity'] / $value['cooled_builtup_area']), 2)
					: 0;

				$gasesPerMeter =
					(!empty($value['gases']) && !empty($value['cooled_builtup_area']))
					? round(($value['gases'] / $value['cooled_builtup_area']), 2)
					: 0;

				$othersPerMeter =
					(!empty($value['others']) && !empty($value['cooled_builtup_area']))
					? round(($value['others'] / $value['cooled_builtup_area']), 2)
					: 0;
			}
			$totalPerRoomNight = $electricity + $gases + $others;
			$totalPerMeterSquare = $electricityPerMeter + $gasesPerMeter + $othersPerMeter;
			$siteEui = $totalPerRoomNight;
			$siteEuiPerMeter = $totalPerMeterSquare;
			$allSiteRanking[] = ['site' => $site['site_location_name'], 'eui' => $siteEui, 'eui_per_meter' => $siteEuiPerMeter];
		}

		if (empty($allSiteRanking)) {
			log_message('error', "Combined EUI Chart Failed: No valid data.");
			return;
		}

		$allSiteRankingPerMeter = $allSiteRanking;
		usort($allSiteRanking, function ($a, $b)  {
			return $b['eui'] <=> $a['eui'];
		});

		usort($allSiteRankingPerMeter, function ($a, $b)  {
			return $b['eui_per_meter'] <=> $a['eui_per_meter'];
		});
		// ---- Prepare Chart JSON ----
		$chartData = $this->prepareCombinedEuiChart($allSiteRanking);
		$chartPerMeterData = $this->prepareCombinedEuiChart($allSiteRankingPerMeter, true);

		echo '<script>console.log("complete_pdf_cron");</script>';
		echo "END";
		exit;
	}

    private function prepareCombinedEuiChart($ranking, $isPerMeter = false)
	{
		$categories = array_column($ranking, 'site');
		$data = ($isPerMeter) ? array_column($ranking, 'eui_per_meter') : array_column($ranking, 'eui');
		$siteCount = count($categories);
    	$chartHeight = max(280, ($siteCount * 24) + 120);
		$Unit = $isPerMeter ? 'kWh/m\u{00B2}' : 'kWh/RN';
		$chartData = [
			"chart" => [
				"type" => "bar",
				"height" => $chartHeight
			],
			"title" => [
				"text" => "Site EUI Benchmark ({$Unit})"
			],
			"xAxis" => [
				"categories" => $categories,
				"title" => [
					"text" => "Sites"
				],
				"labels" => [
					"style" => ["fontSize" => "10px"]
				],
				"gridLineWidth" => 1,
				'lineWidth' => 0
			],
			"yAxis" => [
				"min" => 0,
				"title" => [
					"text" => "EUI ({$Unit})",
					"align" => "high"
				],
				"gridLineWidth" => 0
			],
			"plotOptions" => [
				"bar" => [
					"dataLabels" => [
						"enabled" => true,
						"inside" => false,
						"align" => "left",
						"format" => "{point.y:.2f}",
						"style" => [
							"fontSize" => "9px",
							"fontWeight" => "bold",
							"color" => "#000"
						]
					]
				]
			],
			"legend" => [
				"enabled" => false
			],
			"series" => [[
				"name" => "",
				"showInLegend" => false,
				"data" => $data,
				"colorByPoint" => true
			]]
		];

		// ---- Export Highcharts Image ----
		$exportUrl = "https://export.highcharts.com/";

		$postData = [
			"infile" => json_encode($chartData),
			"type"   => "png",
			"constr" => "Chart",
			"scale"  => 2,
			"resolution" => 2
		];

		$headers = [
			'Accept: image/png',
			'Content-Type: application/x-www-form-urlencoded',
			'User-Agent: Mozilla/5.0',
			'Origin: https://export.highcharts.com',
			'Referer: https://export.highcharts.com'
		];

		$ch = curl_init($exportUrl);
		curl_setopt_array($ch, [
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query($postData),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CONNECTTIMEOUT => 20,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_FAILONERROR    => false,
		]);

		$imageData = curl_exec($ch);
		$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if ($imageData === false || $httpCode !== 200 || strlen($imageData) < 2000) {
			log_message('error', "Combined EUI Chart Failed: " . $curlError);
			return;
		}

		// ---- Save file ----
		$dirPath = BASE_PATH_CUSTOM . "/assets/uploads/highcharts/";
		if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);

		if($isPerMeter) {
			$filePath = $dirPath . "site_eui_per_meter_combined.png";
		} else {
			$filePath = $dirPath . "site_eui_combined.png";
		}
		file_put_contents($filePath, $imageData);

		return true;
	}

	public function upper_management_utilities_report()
	{
		$allChartData = [];

		// Get all users
		$users = $this->reportscron_model->getUserDetailsWithRegion();

		$this->load->model('dashboard/dashboard_model');

		foreach ($users as $user) {

			// ==========================================================
			// CASE 1: USER WITH MULTIPLE REGIONS (regions_count > 1, site_count >= 10)
			// ==========================================================
			if ($user['site_count'] >= 10) {

				// Build region-wise sites for user
				$region_specific_sites = [];
				foreach ($user['user_regions'] as $regionId => $regionValue) {
					$region_specific_sites[$regionId] = $this->reportscron_model->getUserSitesWithRegionId($user['id'], $regionId);
				}

				$user['sites'] = $region_specific_sites;

				if (!empty($user['sites']) && in_array('upper_management', $user['reports'])) {

					$user_id = $user['id'];

					foreach ($user['sites'] as $regionId => $idVal) {

						$data       = [];
						$reportData = [];
						$chart_data = [];

						// ----------------- Sites Collection -----------------
						$site_ids = [];
						$sitesRaw = $idVal;

						foreach ($sitesRaw as $siteKey => $val) {
							$site_ids[] = $siteKey;
						}

						if (!empty($site_ids)) {

							// Per region: reset chart/locals so values from a previous $regionId iteration are not reused.
							unset($filters, $startdate, $site_custom_filter, $report_type, $time_type, $site_type, $report_title, $x_axis_title, $sites);

							$site_filters = [
								'order_by' => 'site_location_name',
								'order'    => 'asc'
							];

							$data['sites'] = $this->sites_model->get_site_detail_multiple($site_ids, $site_filters);

							if (!empty($data['sites'])) {

								foreach ($data['sites'] as $key => $site_detials) {

									$site_id = $site_detials['id'];
									$dataNew = $currentMonth = $lastMonth = $sameMonthLastYear = [];
									$dataNew = $this->sites_model->getMySitesWidgetData($site_detials);
									$currentMonth = $dataNew[0] ?? [];
									$lastMonth = $dataNew[1] ?? [];
									$sameMonthLastYear = $dataNew[2] ?? [];
									$data['sites'][$key]['data']['kpi']['carbon_footprint_currentMonth'] = $currentMonth['carbon_footprint'] ?? 0;
									$data['sites'][$key]['data']['kpi']['total_utility_cost_currentMonth'] = $currentMonth['total_utility_cost'] ?? 0;
									$data['sites'][$key]['data']['kpi']['carbon_footprint_SameMonthPreviousYear'] = $sameMonthLastYear['carbon_footprint'] ?? 0;
									$data['sites'][$key]['data']['kpi']['variation'] = ($currentMonth['total_budgeted_cost'] - $currentMonth['total_utility_cost']);
									$data['sites'][$key]['data']['kpi']['variationPercentage'] = ($currentMonth['total_utility_cost'] != 0) ? (($currentMonth['total_budgeted_cost'] - $currentMonth['total_utility_cost']) * 100 / $currentMonth['total_utility_cost']) : 0;
									$data['sites'][$key]['data']['kpi']['variation_ytd'] = ($currentMonth['total_budgeted_cost_ytd'] - $currentMonth['total_utility_cost_ytd']);
									$data['sites'][$key]['data']['kpi']['variationPercentage_ytd'] = ($currentMonth['total_utility_cost_ytd'] != 0) ? (($currentMonth['total_budgeted_cost_ytd'] - $currentMonth['total_utility_cost_ytd']) * 100 / $currentMonth['total_utility_cost_ytd']) : 0;
									$data['sites'][$key]['data']['kpi']['ytd_carbon_footprint'] = $dataNew['ytd_carbon_footprint'] ?? 0;
									$data['sites'][$key]['data']['kpi']['ytd_carbon_footprintPreviousYear'] = $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0;
									$data['sites'][$key]['data']['kpi']['ytd_carbon_footprint_new'] = $dataNew['ytd_carbon_footprint_new'] ?? 0;
									$data['sites'][$key]['data']['kpi']['ytd_carbon_footprint_baseline_new'] = $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0;
									$data['sites'][$key]['data']['kpi']['total_utility_cost_lastMonth'] = $lastMonth['total_utility_cost'] ?? 0;
									$data['sites'][$key]['data']['kpi']['total_utility_cost_sameMonth_lastYear'] = $sameMonthLastYear['total_utility_cost'] ?? 0;
									$data['sites'][$key]['data']['kpi']['currentMonth_cost_roomNight'] = $currentMonth['cost_roomNight'] ?? 0;
									$data['sites'][$key]['data']['kpi']['lastMonth_cost_roomNight'] = $lastMonth['cost_roomNight'] ?? 0;
									$data['sites'][$key]['data']['kpi']['sameMonth_lastYear_cost_roomNight'] = $sameMonthLastYear['cost_roomNight'] ?? 0;
									// ----------------- UTILITY LAST MONTH COMPARISON -----------------
									$siteId    = $this->session->userdata['hep_cron_session']['site_id'];
									$dataFactor = getMmbtuFactorConversionAllUtility($siteId);

									$filters_notification = [];
									$filters_notification['previousmonth'] = date("F-Y", strtotime(date('Y-m') . " -13 months"));
									$filters_notification['currentmonth']  = date("F-Y", strtotime(date('Y-m') . " -1 months"));
									$filters_notification['pmonth']        = (int) date("m", strtotime(date('Y-m') . " -13 months"));
									$filters_notification['pyear']         = date("Y", strtotime(date('Y-m') . " -13 months"));
									$filters_notification['cmonth']        = (int) date("m", strtotime(date('Y-m') . " -1 months"));
									$filters_notification['cyear']         = date("Y", strtotime(date('Y-m') . " -1 months"));

									$this->dashboard_model->site_id = $site_id;

									// Based on unit (with forex)
									$utilityForLastMonthCompare_result = $this->dashboard_model
										->getUtilityComparisionForLastMonthWithForex($filters_notification);

									$utilityForLastMonthCompare = [];
									foreach ($utilityForLastMonthCompare_result as $value) {
										$value = array_map('floatval', $value);
										$value['total_utility'] =
											$value['electricity'] +
											$value['fuel'] +
											$value['lpg'] +
											$value['natural_gas'] +
											$value['heating_district'] +
											$value['district_heating_fixed_cost'] +
											$value['cooling_district'] +
											$value['district_cooling_fixed_cost'] +
											$value['water'];

										$utilityForLastMonthCompare[$value['year_id']][$value['month_id']] = $value;
									}

									// Based on total cost -> MBTU conversion
									$utilityForLastMonthCompare_unit_result =
										$this->dashboard_model->getUtilityComparisionForLastMonthByUnit($filters_notification);

									$utilityForLastMonthCompare_unit = [];
									foreach ($utilityForLastMonthCompare_unit_result as $value) {
										$value = array_map('floatval', $value);

										$value['total_utility'] =
											$value['electricity'] +
											$value['fuel'] +
											$value['lpg'] +
											$value['natural_gas'] +
											$value['heating_district'] +
											$value['cooling_district'] +
											$value['water'];

										// Convert to MBTU data
										$value['fuel']            = $value['fuel'] * $dataFactor['fuel_oil'];
										$value['lpg']             = $value['lpg'] * $dataFactor['lpg'];
										$value['natural_gas']     = $value['natural_gas'] * $dataFactor['natural_gas'];
										$value['heating_district']= $value['heating_district'] * $dataFactor['district_heating'];
										$value['cooling_district']= $value['cooling_district'] * $dataFactor['district_cooling'];

										$utilityForLastMonthCompare_unit[$value['year_id']][$value['month_id']] = $value;
									}

									$data['sites'][$key]['utilityForLastMonthCompare']       = $utilityForLastMonthCompare;
									$data['sites'][$key]['utilityForLastMonthCompare_unit']  = $utilityForLastMonthCompare_unit;
									$data['sites'][$key]['filters_notification']             = $filters_notification;

									// ----------------- CO2 EMISSION / COMPARISION FILTERS -----------------
									$startdate = date("m/Y", strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -11 months"));
									$enddate   = date(CURRENT_YEAR_MAX_MONTH_ID . '/Y');

									$startdateexplode = explode('/', $startdate);
									$enddateexplode   = explode('/', $enddate);

									$filters = [];
									$filters['startdate']   = $startdate;
									$filters['enddate']     = $enddate;
									$filters['max_month_id']= CURRENT_YEAR_MAX_MONTH_ID;
									$filters['start_month'] = (int) $startdateexplode[0];
									$filters['start_year']  = $startdateexplode[1];
									$filters['end_month']   = (int) $enddateexplode[0];
									$filters['end_year']    = $enddateexplode[1];
									$filters['current_month'] = (int) date('m');
									$filters['current_year']  = date('Y');

									$previousmonthdata   = date("m/Y", strtotime(date('Y-m') . " -1 months"));
									$previousdateexplode = explode('/', $previousmonthdata);
									$filters['previous_month'] = (int) $previousdateexplode[0];
									$filters['previous_year']  = $previousdateexplode[1];

									// Filters for comparisional bar chart
									$filters_comparision_chart = [];
									$startdate  = '1/' . date('Y');
									$enddate    = '12/' . date('Y');

									if (!empty($monthly)) { // assumed external
										$startdate = $this->input->post('monthly_report_month', 1) . '/' .
													$this->input->post('monthly_report_year', date('Y'));
										$enddate   = $this->input->post('monthly_report_month', 12) . '/' .
													$this->input->post('monthly_report_year', date('Y'));
									}

								}
							}

							// Defaults for admin_sites_all_utilities_new: match the same report scope as the main upper-management chart block (total_utilities_by_room_night_and_build_area) when the per-site loop did not set these.
							$site_custom_filter = $site_custom_filter ?? array();
							$startdate         = $startdate ?? (date("m/Y", strtotime(date('Y-m') . " -1 months")));
							if (empty($filters) || !is_array($filters)) {
								$filters = array();
							}
							$sites        = $data['sites'] ?? array();
							$report_type  = $report_type ?? 'total_utilities_by_room_night_and_build_area';
							$time_type    = $time_type ?? 'sites_select_choose_month';
							$site_type    = $site_type ?? 0;
							$report_title = $report_title ?? 'sites_total_utilities_by_room_night_and_build_area_report_title';
							$x_axis_title = $x_axis_title ?? ('Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')');

							$filters['site_custom_filter'] = $site_custom_filter;
							$filters['startdate']          = $startdate;

							$chart_data['sites']        = $sites;
							$chart_data['sites_list']   = $sites;
							$chart_data['reportdata']   = $reportData;
							$chart_data['report_type']  = $report_type;
							$chart_data['time_type']    = $time_type;
							$chart_data['site_type']    = $site_type;
							$chart_data['report_title'] = $report_title;
							$chart_data['x_axis_title'] = $x_axis_title;
							$chart_data['filters']      = $filters;
							$chart_data['data']         = $data;
							$chart_data['user']         = $user;

							unset($reportData, $report_type, $data);
						}

						$allChartData[$user_id][$regionId] = $chart_data;
					}
				}

			}
		}

		$view_data = $this->load->view('admin_sites_all_utilities_new', array('allChartData' => $allChartData), true);

		echo $view_data;
		echo '<script>console.log("complete_pdf_cron");</script>';
		echo "END";
		exit;
	}

    private function generateEUIChartPDF()
	{
		$chartImagePath = BASE_PATH_CUSTOM . "/assets/uploads/highcharts/site_eui_combined.png";
		$chartPerMeterImagePath = BASE_PATH_CUSTOM . "/assets/uploads/highcharts/site_eui_per_meter_combined.png";

		if (!file_exists($chartImagePath)) {
			return null;
		}
		if (!file_exists($chartPerMeterImagePath)) {
			return null;
		}
		list($imgWidthPx, $imgHeightPx) = getimagesize($chartImagePath);

		if (!$imgWidthPx || !$imgHeightPx) {
			return null;
		}
		$pxToMm = 0.264583;

		$pageWidthMm  = ($imgWidthPx  * $pxToMm) + 6; 
		$pageHeightMm = ($imgHeightPx * $pxToMm) + 6;

		$maxWidth  = 210; // mm
		$maxHeight = 297;

		$pageWidthMm  = min($pageWidthMm, $maxWidth);
		$pageHeightMm = min($pageHeightMm, $maxHeight);

		require_once $_SERVER['DOCUMENT_ROOT'] . 'Rotana/vendor/autoload.php';

		$mpdfConfig = [
			'mode' => 'utf-8',
			'format' => [$pageWidthMm, $pageHeightMm],
			'margin_left'   => 3,
			'margin_right'  => 3,
			'margin_top'    => 3,
			'margin_bottom' => 3
		];

		$pdf = new \Mpdf\Mpdf($mpdfConfig);

		$pdf->use_kwt = true;
		$pdf->simpleTables = true;

		$html = '
		<html>
		<body style="margin:0; padding:0;">
			<div style="text-align:center;">
				<img src="' . $chartImagePath . '" style="width:100%; height:auto; display:block;">
			</div>
			<div style="text-align:center;">
				<img src="' . $chartPerMeterImagePath . '" style="width:100%; height:auto; display:block;">
			</div>
		</body>
		</html>';

		$pdf->WriteHTML($html);

		$fileName = 'EUI_Chart.pdf';
		$fullPath = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $fileName;

		$pdf->Output($fullPath, \Mpdf\Output\Destination::FILE);

		return $fullPath;
	}

	public function generate_mpdf_new()
	{
		set_time_limit(0);
		$users = $this->reportscron_model->getUserDetailsWithRegion();
		$this->load->model('reports/reports_model');
		$this->load->model('sites/site_waste_model');
		$hotel_detail = $this->hotels_model->get_hotel_detail(1);
		$regionName = [
			'EMEA',
			'Asia Pacific',
			'Americas',
		];
        $siteCronSettings = $this->sites_model->getSiteCronSettings();
        $monthlyTickedSites = array();
        foreach ($siteCronSettings as $cronSettings) {
            if ($cronSettings['site_cron_settings']['cron_type'] == 'MONTHLY') {
                array_push($monthlyTickedSites,  $cronSettings['site_cron_settings']['site_id']);
            }
        }
		foreach ($users as $key => $user) {
			foreach ($user['user_regions'] as $key => $value) {
				$region_specific_sites[$key] = $this->reportscron_model->getUserSitesWithRegionId($user['id'], $key);
			}
			$user['sites'] = $region_specific_sites;
			if (!empty($user['sites']) && in_array('upper_management', $user['reports'])) {
				$attachments = array();
				$data = array();
				$reportData = array();
				foreach ($user['sites'] as $regionId => $idVal) {
					// Reset each region so we never reuse another region's site list (was causing wrong/blank PDFs).
					$chart_data      = array(
						'sites'      => array(),
						'sites_list' => array(),
					);
					$monthlyTickedIds = array_map('intval', (array) $monthlyTickedSites);
					$site_ids         = array();
					$user['sites']    = $idVal;
					foreach ($user['sites'] as $key => $val) {
						$sid = (int) $key;
						if ($sid > 0 && in_array($sid, $monthlyTickedIds, true)) {
							$site_ids[] = $sid;
						}
					}
					if (empty($site_ids)) {
						continue;
					}
					$site_filters                  = array();
					$site_filters['order_by']      = 'site_location_name';
					$site_filters['order']         = 'asc';
					$data['sites']                 = $this->sites_model->get_site_detail_multiple($site_ids, $site_filters);
					$sites                         = $data['sites'];
					if (empty($sites)) {
						continue;
					}
					$chart_data['sites']      = $sites;
					$chart_data['sites_list'] = $sites;
					unset($reportData);
					unset($report_type);
					unset($data);
					$allChartData[] = $chart_data;
					$data           = $chart_data;
					$mpdfConfig = array(
						'mode' => 'utf-8',
						'format' => 'A4-L',
						'default_font_size' => 0,
						'default_font' => '',
						'margin_left' => 5,
						'margin_right' => 5,
						'margin_top' => 25,
						'margin_bottom' => 15,
						'margin_header' => 2,
						'margin_footer' => 5,
						'orientation' => 'P'   
					);
					// PDF output file: "{username}{regionId} upper management report {ddmmYYYY}.pdf" (multi-site upper management; distinct from per-site monthly/annual email subject format)

					$pdfName = $user['username'] . $regionId . " upper management report " . date('dmY') . ".pdf";
					$mypdfName = strtolower(str_replace(array(' ', '-'), array('_', ''), $pdfName));
					require_once $_SERVER['DOCUMENT_ROOT'] . 'Rotana/vendor/autoload.php';
					$pdf = new \Mpdf\Mpdf($mpdfConfig);
					//adding css
					$basepath = site_url() . 'themes/default/css/';
					$css_array = array('reset', 'font-awesome', 'bootstrap.min', 'bootstrap-theme.min', 'easy-responsive-tabs', 'style', 'media', 'jquery-ui', 'site_pdf_custom', 'custom');

					foreach ($css_array as $css) {
						$filename = $css . ".css";
						$stylesheet = file_get_contents($basepath . $filename);
						$pdf->WriteHTML($stylesheet, 1);
					}
					$this->hotel = $this->reportscron_model->getHotel();
					$hotelLogo = $this->hotel['hotel_logo'];
					//pages for each site
					if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $hotelLogo)) {
						$header_logo_src = site_url() . "assets/uploads/" . $hotelLogo;
					} else {
						$header_logo_src = site_url() . NOT_AVAILABLE_SITE_LOGO;
					}

					$pdf->SetHTMLHeader('<div name="myHTMLHeader"><table width="100%" style="vertical-align: central; font-size: 9pt;"><tr><td width="33%"><img src="' . $header_logo_src . '" height="40"/></td><td width="33%" style="vertical-align: central;font-weight:bold; text-align: center;"><i>Utilities Management Report</i></td><td width="33%" style="text-align: right; vertical-align: central;"><span style="font-weight:bold;" ><i>{DATE j-m-Y}</i></span> </td></tr></table></div><div style="padding-top:5px;width: 100%;border-bottom: 1px solid #000000;"></div>');

					$pdf->SetFooter("HEP - Hotel Energy Portal | Copyright@" . date("Y") . " EEG - Energy Efficiency Group. All rights reserved. |  Page {PAGENO}/{nb}");

					$margin_flag = 0;
					$margin_flag_chart = 0;
					$html = '';
					$site_count = count($data['sites']);
					foreach ($data['sites'] as $key => $site) {
						$data['site_key'] = $key;
						// New PDF page every 4 sites (2 rows x 2 columns); works for any site count.
						if ($margin_flag > 0 && $margin_flag % 4 === 0) {
							$pdf->AddPage();
						}
						$data['temp'] = $margin_flag;
						$postData = $data;
						unset($postData['sites']);
						$postData['site'] = array_merge($site);

						if ($margin_flag % 2 == 0) {
							$html .= '<div style="width:100%;clear:both;float:left;">&nbsp;';
						}

						// progress on Target report data
						$progressOnTarget = array();
						$site_detials = $site;
						$dateParams = getProgressWidgetDateParams();
						$current_month = $dateParams['month'];
						$current_year = $dateParams['year'];
						$running_year = $dateParams['running_year'];
						$this->reports_model->site_id = $site['id'];
						if ($site['id']) {
							$baselineYear = $site_detials['baseline_regression_year'];
							$progressOnTargetMonthly = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear, 'month');
							$progressOnTarget = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear);
							$wasteDiversionNumeratorData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detials, 'recycling_wte', $current_year, $current_month);
							$totalWasteData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detials, '', $current_year, $current_month);

							$progressOnTarget[$baselineYear]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$baselineYear]) ? $wasteDiversionNumeratorData['YTDTotal'][$baselineYear] : 0;
							$progressOnTarget[$baselineYear]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0;
							$progressOnTarget[$running_year]['waste_diversion_numerator'] = isset($wasteDiversionNumeratorData['YTDTotal'][$running_year]) ? $wasteDiversionNumeratorData['YTDTotal'][$running_year] : 0;
							$progressOnTarget[$running_year]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0;

							$progressValueWasteYTD = [
								'total_waste_baseline_target' => isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0,
								'total_waste_target' => isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0
							];
							$data['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
							$postData['site']['site_id'] = isset($site['id']) ? $site['id'] : [];
							$postData['site']['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
							$postData['site']['progressOnTargetMonthly'] = isset($progressOnTargetMonthly) ? $progressOnTargetMonthly : [];
							$dataNew = $currentMonth = $lastMonth = $sameMonthLastYear = [];
							$dataNew = $this->sites_model->getMySitesWidgetData($site_detials);
							$currentMonth = $dataNew[0] ?? [];
							$lastMonth = $dataNew[1] ?? [];
							$sameMonthLastYear = $dataNew[2] ?? [];

							$carbonData = [
								'carbon_footprint_currentMonth' => $currentMonth['carbon_footprint'] ?? 0,
								'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYear['carbon_footprint'] ?? 0,
								'ytd_carbon_footprint_new' => $dataNew['ytd_carbon_footprint_new'] ?? 0,
								'ytd_carbon_footprintPreviousYear' => $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0,
								'ytd_carbon_footprint_baseline_new' => $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0
							];
							$progressOnTargetResult = calculateProgressOnTarget(
								$progressOnTarget,
								$current_month,
								$current_year,
								$site_detials,
								$carbonData,
								$progressValueWasteYTD
							);
							$postData['site']['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
							$postData['site']['progressTarget'] = $progressOnTargetResult['progressTarget'] ?? [];
							$postData['site']['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
							$postData['site']['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
							$postData['site']['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
							$postData['site']['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];

							$reportComparisonMonth = new DateTime('first day of last month');
							$reportComparisonMonthLastYear = (clone $reportComparisonMonth)->modify('-1 year');
							$postData['site']['report_comparison_month_label'] = $reportComparisonMonth->format('F Y');
							$postData['site']['report_comparison_month_last_year_label'] = $reportComparisonMonthLastYear->format('F Y');

							$cdd_consumption = ($sameMonthLastYear['cdd'] != '' && $currentMonth['cdd']) ? ($currentMonth['cdd'] - $sameMonthLastYear['cdd']) * 100 / $sameMonthLastYear['cdd'] : 0;
							$hdd_consumption = ($sameMonthLastYear['hdd'] != '' && $currentMonth['hdd']) ? ($currentMonth['hdd'] - $sameMonthLastYear['hdd']) * 100 / $sameMonthLastYear['hdd'] : 0;
							$total_room_night_consumption = ($sameMonthLastYear['total_room_night'] != '' && $currentMonth['total_room_night']) ? ($currentMonth['total_room_night'] - $sameMonthLastYear['total_room_night']) * 100 / $sameMonthLastYear['total_room_night'] : 0;
							$cdd_consumption_image = $cdd_consumption > 0 ? 'upArrowGreen.png' : 'downArrowRed.png';
							$hdd_consumption_image = $hdd_consumption > 0 ? 'upArrowGreen.png' : 'downArrowRed.png';
							$total_room_night_consumption_image = $total_room_night_consumption > 0 ? 'upArrowGreen.png' : 'downArrowRed.png';

							$postData['site']['kpi']['cdd_consumption'] = $cdd_consumption;
							$postData['site']['kpi']['hdd_consumption'] = $hdd_consumption;
							$postData['site']['kpi']['total_room_night_consumption'] = $total_room_night_consumption;
							$postData['site']['kpi']['cdd_consumption_image'] = $cdd_consumption_image;
							$postData['site']['kpi']['hdd_consumption_image'] = $hdd_consumption_image;
							$postData['site']['kpi']['total_room_night_consumption_image'] = $total_room_night_consumption_image;
							$postData['site']['kpi']['carbon_footprint_currentMonth'] = $currentMonth['carbon_footprint'] ?? 0;
							$postData['site']['kpi']['total_utility_cost_currentMonth'] = $currentMonth['total_utility_cost'] ?? 0;
							$postData['site']['kpi']['carbon_footprint_SameMonthPreviousYear'] = $sameMonthLastYear['carbon_footprint'] ?? 0;
							$postData['site']['kpi']['variation'] = ($currentMonth['total_utility_cost'] - $currentMonth['total_budgeted_cost']);
							$postData['site']['kpi']['variationPercentage'] = ($currentMonth['total_budgeted_cost'] != 0) ? (($currentMonth['total_utility_cost'] - $currentMonth['total_budgeted_cost']) * 100 / $currentMonth['total_budgeted_cost']) : 0;
							$postData['site']['kpi']['variationPercentage'] = $postData['site']['kpi']['variationPercentage'] == 100 ? 0 : $postData['site']['kpi']['variationPercentage'];
							$postData['site']['kpi']['variation_ytd'] = ($currentMonth['total_utility_cost_ytd'] - $currentMonth['total_budgeted_cost_ytd']);
							$postData['site']['kpi']['variationPercentage_ytd'] = ($currentMonth['total_budgeted_cost_ytd'] != 0) ? (($currentMonth['total_utility_cost_ytd'] - $currentMonth['total_budgeted_cost_ytd']) * 100 / $currentMonth['total_budgeted_cost_ytd']) : 0;
							$postData['site']['kpi']['variationPercentage_ytd'] = $postData['site']['kpi']['variationPercentage_ytd'] == 100 ? 0 : $postData['site']['kpi']['variationPercentage_ytd'];
							$postData['site']['kpi']['ytd_carbon_footprint'] = $dataNew['ytd_carbon_footprint'] ?? 0;
							$postData['site']['kpi']['ytd_carbon_footprintPreviousYear'] = $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0;
							$postData['site']['kpi']['ytd_carbon_footprint_new'] = $dataNew['ytd_carbon_footprint_new'] ?? 0;
							$postData['site']['kpi']['ytd_carbon_footprint_baseline_new'] = $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0;
							$postData['site']['kpi']['total_utility_cost_lastMonth'] = $lastMonth['total_utility_cost'] ?? 0;
							$postData['site']['kpi']['total_utility_cost_sameMonth_lastYear'] = $sameMonthLastYear['total_utility_cost'] ?? 0;
							$postData['site']['kpi']['currentMonth_cost_roomNight'] = $currentMonth['cost_roomNight'] ?? 0;
							$postData['site']['kpi']['lastMonth_cost_roomNight'] = $lastMonth['cost_roomNight'] ?? 0;
							$postData['site']['kpi']['sameMonth_lastYear_cost_roomNight'] = $sameMonthLastYear['cost_roomNight'] ?? 0;
							// UtilityConsumption unit for last month comparison
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_electricity_raw'] = ($site['show_utility_electricity']) ? $currentMonth['electricity_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_fuel_raw'] = ($site['show_utility_fuel_oil']) ? $currentMonth['fuel_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_lpg_raw'] = ($site['show_utility_lpg']) ? $currentMonth['lpg_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_natural_gas_raw'] = ($site['show_utility_natural_gas']) ? $currentMonth['natural_gas_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_heating_raw'] = ($site['show_utility_district_heating']) ? $currentMonth['heating_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_cooling_raw'] = ($site['show_utility_district_cooling']) ? $currentMonth['cooling_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_water_raw'] = ($site['show_utility_water']) ? $currentMonth['water_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_total_utility_consumption'] = $currentMonth['total_utility_consumption'] ?? 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_mmbtu_fuel'] = ($site['show_utility_fuel_oil']) ? $currentMonth['mmbtu_fuel'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_mmbtu_lpg'] = ($site['show_utility_lpg']) ? $currentMonth['mmbtu_lpg'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_mmbtu_natural_gas'] = ($site['show_utility_natural_gas']) ? $currentMonth['mmbtu_natural_gas'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_mmbtu_heating_district'] = ($site['show_utility_district_heating']) ? $currentMonth['mmbtu_heating_district'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_currentMonth_mmbtu_cooling_district'] = ($site['show_utility_district_cooling']) ? $currentMonth['mmbtu_cooling_district'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_electricity_raw'] = ($site['show_utility_electricity']) ? $lastMonth['electricity_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_fuel_raw'] = ($site['show_utility_fuel_oil']) ? $lastMonth['fuel_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_lpg_raw'] = ($site['show_utility_lpg']) ? $lastMonth['lpg_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_natural_gas_raw'] = ($site['show_utility_natural_gas']) ? $lastMonth['natural_gas_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_heating_raw'] = ($site['show_utility_district_heating']) ? $lastMonth['heating_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_cooling_raw'] = ($site['show_utility_district_cooling']) ? $lastMonth['cooling_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_water_raw'] = ($site['show_utility_water']) ? $lastMonth['water_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_total_utility_consumption'] = $lastMonth['total_utility_consumption'] ?? 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_mmbtu_fuel'] = ($site['show_utility_fuel_oil']) ? $lastMonth['mmbtu_fuel'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_mmbtu_lpg'] = ($site['show_utility_lpg']) ? $lastMonth['mmbtu_lpg'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_mmbtu_natural_gas'] = ($site['show_utility_natural_gas']) ? $lastMonth['mmbtu_natural_gas'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_mmbtu_heating_district'] = ($site['show_utility_district_heating']) ? $lastMonth['mmbtu_heating_district'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_lastMonth_mmbtu_cooling_district'] = ($site['show_utility_district_cooling']) ? $lastMonth['mmbtu_cooling_district'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_electricity_raw'] = ($site['show_utility_electricity']) ? $sameMonthLastYear['electricity_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_fuel_raw'] = ($site['show_utility_fuel_oil']) ? $sameMonthLastYear['fuel_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_lpg_raw'] = ($site['show_utility_lpg']) ? $sameMonthLastYear['lpg_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_natural_gas_raw'] = ($site['show_utility_natural_gas']) ? $sameMonthLastYear['natural_gas_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_heating_raw'] = ($site['show_utility_district_heating']) ? $sameMonthLastYear['heating_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_cooling_raw'] = ($site['show_utility_district_cooling']) ? $sameMonthLastYear['cooling_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_water_raw'] = ($site['show_utility_water']) ? $sameMonthLastYear['water_raw'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_total_utility_consumption'] = ($site['show_utility_total_consumption']) ? $sameMonthLastYear['total_utility_consumption'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_mmbtu_fuel'] = ($site['show_utility_fuel_oil']) ? $sameMonthLastYear['mmbtu_fuel'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_mmbtu_lpg'] = ($site['show_utility_lpg']) ? $sameMonthLastYear['mmbtu_lpg'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_mmbtu_natural_gas'] = ($site['show_utility_natural_gas']) ? $sameMonthLastYear['mmbtu_natural_gas'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_mmbtu_heating_district'] = ($site['show_utility_district_heating']) ? $sameMonthLastYear['mmbtu_heating_district'] ?? 0 : 0;
								$postData['site']['kpi']['utilityForLastMonthCompare_unit_sameMonthLastYear_mmbtu_cooling_district'] = ($site['show_utility_district_cooling']) ? $sameMonthLastYear['mmbtu_cooling_district'] ?? 0 : 0;
						}
						$html .= $this->load->view('admin_sites_pdf', $postData, true);
						if ($margin_flag % 2 == 1 || $site_count == ($margin_flag + 1)) {
							$html .= '</div>';
							$pdf->WriteHTML($html, 2);
							$html = '';
						}
						$margin_flag++;
					}
					if ($html !== '') {
						$html .= '</div>';
						$pdf->WriteHTML($html, 2);
					}						
					$file_name = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $mypdfName;
					$pdf->Output($file_name, 'F'); // D - downlaod, F- Save
					$attachments[$regionName[$regionId - 1]] = $file_name;
				}
					/* NEW : generate separate EUI chart pdf */
					$euiChartPdf = $this->generateEUIChartPDF();
					if ($euiChartPdf) {
						$attachments['EUI Combined Chart'] = $euiChartPdf;
					}
				        $today = new DateTime();
					$current = (clone $today)->modify('first day of last month');
					$previous = (clone $current)->modify('-1 month');
				        $lastYearDt = (clone $current)->modify('-1 year');
					$currMonth= (int)$current->format('m');
					$currentYear= (int)$current->format('Y');
					$prevMonth= (int)$previous->format('m');
					$prevYear = (int)$previous->format('Y');
				        $lastYear    = (int) $lastYearDt->format('Y');

					$filePath = $this->sites_model->generateGroupUtilityReport($currentYear,$currMonth,$prevYear,$prevMonth,$lastYear,1);
					if (file_exists($filePath) && filesize($filePath) > 0) {
						$attachments['Group Utility Report'] = $filePath;
					} else {
						log_message('error', 'Excel not generated: ' . $filePath);
				}
				$bodyHtml = '<div><h4>Dear ' . $user['firstname'] . ' ' . $user['lastname'] . '</h4></div>';

				$bodyHtml .= '<div>
					To help you quickly analyze performance across your portfolio, this 
					<strong>Utilities Management Reports</strong> dispatch includes three key components:
				</div>';

				$bodyHtml .= '
				<ul>
					<li>
						<strong>Executive Dashboard Report:</strong>
						A high-level visual summary tracking actual utility consumption, costs, and current progress against your property reduction targets.
					</li>
					<li>
						<strong>EUI Comparison Chart:</strong>
						An End-Use Intensity (kWh/m\u{00B2}) comparative chart of all your properties on HEP.
					</li>
					<li>
						<strong>Detailed Data Spreadsheet:</strong>
						A granular dataset detailing consumption trends, costs, and critical operational metrics for deeper, custom analysis.
					</li>
				</ul>';

				$bodyHtml .= '<div style="margin-top:15px;">
					For more information and analysis you can log on to
					<a href="' . base_url() .'">' . base_url() . '/</a>
				</div>';

				$email_template['html'] = $bodyHtml;
				$body = $this->load->view('email_template', $email_template, true);

				$this->load->library('mailer');
				$this->mailer->mail->IsHTML(true);
				$this->mailer->mail->ClearAttachments();
				$this->mailer->mail->ClearAllRecipients();
				$this->mailer->mail->ClearAddresses();

				// Email Subject
				$subject = 'Utilities Management Reports | '.$hotel_detail['hotel_name'];

				$this->mailer->mail->AddAddress($user['email']);
				// $this->mailer->mail->AddAddress('garima.pandey@tatvasoft.com');
				$this->mailer->mail->Subject = $subject;
				$this->mailer->mail->Body = $body;
				foreach ($attachments as $regionNamePdf => $file_name) {
					$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
					$this->mailer->mail->addAttachment(
						$file_name,
						"{$regionNamePdf} - Utilities Dashboard Report.{$extension}"
					);
				}


				if (!$this->mailer->mail->Send()) {
					$myFile = "logs.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					$stringData = $this->mailer->mail->ErrorInfo . date("Y-m-d H:i:s") . "\n";
					fwrite($fh, $stringData);
					fclose($fh);
				} else {
					$myFile = "logs.txt";
					$fh = fopen($myFile, 'a') or die("can't open file");
					$stringData = "Mail has been sent successfully" . date("Y-m-d H:i:s") . "\n";
					fwrite($fh, $stringData);
					fclose($fh);
				}
				$this->mailer->mail->ClearAddresses();
				$myFile = "logs.txt";
				$fh = fopen($myFile, 'a') or die("can't open file");
				$stringData = $user['username'] . " - from mpdf function " . date("Y-m-d H:i:s") . "\n";
				fwrite($fh, $stringData);
				fclose($fh);
			}
		}

		return $mypdfName;
	}

	private function getBase64PngOrNull(string $path): ?string
	{
		if (!file_exists($path) || !is_readable($path)) {
			return null;
		}

		clearstatcache(true, $path);
		if (filesize($path) < 2048) {
			return null;
		}

		// PNG header check
		$fh = fopen($path, 'rb');
		if (!$fh) return null;

		$header = fread($fh, 8);
		fclose($fh);

		if ($header !== "\x89PNG\r\n\x1A\n") {
			return null;
		}

		// Structural validation
		if (@getimagesize($path) === false) {
			return null;
		}

		$data = file_get_contents($path);
		if ($data === false || strlen($data) < 2048) {
			return null;
		}

		return base64_encode($data);
	}

	public function get_image_from_uri_new($url = '')
	{
		$myFile = "logs.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		$stringData = "from get_image_from_uri function start " . date("Y-m-d H:i:s") . "\n";
		fwrite($fh, $stringData);
		fclose($fh);

		$url = $_POST['img_url'];
		$filenm = $_POST['filenm'];
		$user_id = $_POST['user_id'];
		$region_id = $_POST['region_id_key'];
		$myFile = "logs.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		$stringData = "User_id " . $user_id . "\n";
		fwrite($fh, $stringData);
		fclose($fh);
		$previousmonthdata = date("m/Y", strtotime(date('Y-m') . " -1 months"));
		$previousdateexplode = explode('/', $previousmonthdata);
		$previous_month = (int) $previousdateexplode[0];
		$imageName = $filenm . '.png';
		$imageDir = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm;
		$imagePath = BASE_PATH_CUSTOM . "/assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm . "/" . $imageName;
		if (!file_exists($imageDir)) {
			mkdir($imageDir, 0777, true);
		}
		$imageUrl = site_url() . "assets/uploads/cron/" . $previous_month . "/" . $user_id . "/" . $region_id . "/" . $filenm . "/" . $imageName;



		list($type, $url) = explode(';', $url);
		list(, $url) = explode(',', $url);
		$url = base64_decode($url);

		file_put_contents($imagePath, $url);

		$myFile = "logs.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		$stringData = "from get_image_from_uri function end " . date("Y-m-d H:i:s") . "\n";
		fwrite($fh, $stringData);
		fclose($fh);

		echo $imageUrl;
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
		$this->Cell(150, 22, $this->site_name, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 'B', 8);
		$this->Cell(0, 22, DATE('d-M-Y'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		$footText = "Page " . $this->getAliasNumPage() . "/" . $this->getAliasNbPages();
		$footText .= "\n HEP - Hotel Energy Portal | Copyright@" . date("Y") . " EEG - Energy Efficiency Group. All rights reserved.";
		$this->MultiCell(0, 10, $footText, 0, 'C');
	}
}
