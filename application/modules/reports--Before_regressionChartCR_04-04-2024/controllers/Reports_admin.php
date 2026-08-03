<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Reports_admin extends Base_Admin_Controller
{
    var $mCurrency = '';

    public function __construct()
    {
	parent::__construct();
	$this->access_control($this->access_rules());
	$this->load->model('reports_model');
	$this->load->model('reports_forex_model');
	$this->load->model('utilities/utilities_model');
	$this->load->model('reportscron/reportscron_model');
	$this->reports_model->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
	$this->reports_model->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
	$this->reports_model->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->reports_forex_model->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
	$this->reports_forex_model->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
	$this->reports_forex_model->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->library('form_validation');
	$this->language = $this->uri->segment(4);
	// Get max current year's month id for chart
	if (date('m') == 1) {
	    define('CURRENT_YEAR_MAX_MONTH_ID', 12);
	} else {
	    define('CURRENT_YEAR_MAX_MONTH_ID', date('m') - 1);
	}
	if ($this->input->post('currency')) {
	    $this->mCurrency = $this->input->post('currency');
	    $this->session->set_custom_userdata($this->section_name, "mCurrency", $this->mCurrency);
	}
    }

    private function access_rules()
    {
	return array(
	    array(
		'actions' => array('index', 'advance', 'sites', 'roomnight', 'carbon', 'budget', 'management', 'daily', 'export', 'generate_report_pdf', 'generate_report_excel', 'report_calculations', 'report_layout_excel', 'daily_metering', 'annual_excel_report', 'CalculateMeasures'),
		'users' => array('@'),
	    ), array(
		'actions' => array('getMonthlyReportChart', 'prepare_budget', 'carbon_footprint', 'saveimage', 'getimage'),
		'users' => array('*'),
	    ),
	);
    }

    public function index($cview = 'index')
    {
	$data = array();
	$data['currency'] = "local";
	if ($this->input->post('currency')) {
	    $data['currency'] = $this->input->post('currency');
	}
	$data['CURRENT_YEAR_MAX_MONTH_ID'] = CURRENT_YEAR_MAX_MONTH_ID;
	$data['cview'] = $cview;
	$cview_file = 'admin_index';


		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');

		$currentYear = date('Y');
		if(date('n') == 1) {
			$currentYear = date('Y') - 1;
		}
	// For last 12 months
	$startdate = date("m/".$currentYear, strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -11 months"));
	$enddate = date(CURRENT_YEAR_MAX_MONTH_ID .'/'. $currentYear);
	if ($this->input->post('yearly_report_year')) {
	    $startdate = '1/' . $this->input->post('yearly_report_year', $currentYear);
	    $enddate = '12/' . $this->input->post('yearly_report_year', $currentYear);
	}
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
	$filters['current_year'] = $currentYear;
	if(date('n') == 1) {
		$filters['previous_month'] = (int) 12;
		$filters['previous_year'] = $currentYear - 1;
	} else {
		$previousmonthdata = date("m/".$currentYear - 1);
		$previousdateexplode = explode('/', $previousmonthdata);
		$filters['previous_month'] = (int) $previousdateexplode[0];
		$filters['previous_year'] = $previousdateexplode[1];
	}

	// FIlters for comparisional bar chart
	$filters_comparision_chart = array();
	$startdate = '1/' . $currentYear;
	$enddate = '12/' . $currentYear;

	// Change filter for monthly report pdf // conditional
	if ($this->input->post('submit') == 'download_monthly_hidden') {
	    $startdate = $this->input->post('monthly_report_month', 1) . '/' . $this->input->post('monthly_report_year', $currentYear);
	    $enddate = $this->input->post('monthly_report_month', 12) . '/' . $this->input->post('monthly_report_year', $currentYear);
	}
	if ($this->input->post('yearly_report_year')) {
	    $startdate = '1/' . $this->input->post('yearly_report_year', $currentYear);
	    $enddate = '12/' . $this->input->post('yearly_report_year', $currentYear);
	}
	$startdateexplode = explode('/', $startdate);
	$enddateexplode = explode('/', $enddate);
	$filters_comparision_chart['startdate'] = (isset($startdate)) ? $startdate : '';
	$filters_comparision_chart['enddate'] = (isset($enddate)) ? $enddate : '';
	$filters_comparision_chart['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
	$filters_comparision_chart['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';
	$filters_comparision_chart['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';
	$filters_comparision_chart['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';
	//if utility year is selected

	if ($this->input->post('utility_year_selected')) {
	    $utility_year_selected = $this->input->post('utility_year_selected');
	} else {
	    $utility_year_selected = $currentYear;
	}
	$utilitystartdate = '1/' . $utility_year_selected;
	$utilityenddate = '12/' . $utility_year_selected;
	$utilitystartdateexplode = explode('/', $utilitystartdate);
	$utilityenddateexplode = explode('/', $utilityenddate);
	$utility_cost_chart_filter = [];
	$utility_cost_chart_filter['startdate'] = (isset($utilitystartdateexplode)) ? $utilitystartdateexplode : '';
	$utility_cost_chart_filter['enddate'] = (isset($utilityenddateexplode)) ? $utilityenddateexplode : '';
	$utility_cost_chart_filter['start_month'] = (isset($utilitystartdateexplode[0])) ? (int) $utilitystartdateexplode[0] : '';
	$utility_cost_chart_filter['start_year'] = (isset($utilitystartdateexplode[1])) ? $utilitystartdateexplode[1] : '';
	$utility_cost_chart_filter['end_month'] = (isset($utilityenddateexplode[0])) ? (int) $utilityenddateexplode[0] : '';
	$utility_cost_chart_filter['end_year'] = (isset($utilityenddateexplode[1])) ? $utilityenddateexplode[1] : '';

	if ($this->input->post('submit') == 'download_monthly_hidden') {
	    if ($data['currency'] == "base") {
		$utility_cost_chart_results = $this->reports_forex_model->utilityCostBarChart($filters_comparision_chart);
	    } else {
		$utility_cost_chart_results = $this->reports_model->utilityCostBarChart($filters_comparision_chart);
	    }
	} else {
	    if ($data['currency'] == "base") {
		$utility_cost_chart_results = $this->reports_forex_model->utilityCostBarChart($utility_cost_chart_filter);
	    } else {
		$utility_cost_chart_results = $this->reports_model->utilityCostBarChart($utility_cost_chart_filter);
	    }
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
	    $totalElectricityConsumption = 0;
	    // $result = array_map('intval', $utility_cost_chart_results);
	    foreach ($utility_cost_chart_results as $key => $value) {
		if (isset($data['utility_cost_chart'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart'][$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    $value = array_map('intval', $value);
		    $value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		    $value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];
		    $value['lpg'] = $value['lpg'] + $value['lpg_fixed_cost'];
		    $value['water'] = $value['water'] + $value['water_fixed_cost'];
		    $value['natural_gas_consumption'] = $value['natural_gas_consumption'] + $value['natural_gas_fixed_cost'];
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
			case 'carbon_footprint':
			    $cview_file = 'admin_carbon_footprint';
			    $results = $this->reports_model->utilityUnitBarChart($filters);
			    //carbon foot print
			    $this->load->model('sites/sites_model');
			    $site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
			    $this->sites_model->year = $filters['start_year'];
			    $site_details = $this->sites_model->get_site_detail_custom($site_id);
			    // Note : water_utility,water irrigation,water_cisterns fields are for only one report so no need to calculate by other parameters and no need in cost based report
			    $reportData = array();
			    if (!empty($results)) {
				foreach ($results as $key => $result) {
				    if(isset($reportData[$result['month_id']][$result['year_id']]) && !empty($reportData[$result['month_id']][$result['year_id']])) {
					continue;
				    } else {
				    $totalElectricity += $result['electricity'];
				    $totalFuel += $result['fuel'];
				    $totalLpg += $result['lpg'];
				    $totalNaturalGas += $result['natural_gas'];
				    $totalWater += $result['water'];
				    $totalHeatingDistrict += $result['heating_district'];
				    $totalCoolingDistrict += $result['cooling_district'];
				    $calculated_result['electricity'] = (!empty($result['electricity'])) ? ($result['electricity']) : 0;
				    $calculated_result['fuel'] = (!empty($result['fuel'])) ? ($result['fuel']) : 0;
				    $calculated_result['lpg'] = (!empty($result['lpg'])) ? ($result['lpg']) : 0;
				    $calculated_result['natural_gas'] = (!empty($result['natural_gas'])) ? ($result['natural_gas']) : 0;
				    $calculated_result['heating_district'] = (!empty($result['heating_district'])) ? ($result['heating_district']) : 0;
				    $calculated_result['cooling_district'] = (!empty($result['cooling_district'])) ? ($result['cooling_district']) : 0;
				    $calculated_result['water'] = (!empty($result['water'])) ? ($result['water']) : 0;
				    $calculated_result['cdd'] = (!empty($result['cdd'])) ? ($result['cdd']) : 0;
				    $calculated_result['hdd'] = (!empty($result['hdd'])) ? ($result['hdd']) : 0;
				    $calculated_result['budget'] = (!empty($result['total_budget'])) ? ($result['total_budget']) : 0;
				    $calculated_result['total_electricity_kwh'] = (!empty($result['total_electricity_kwh'])) ? ($result['total_electricity_kwh']) : 0;
				    if (!empty($result['total_electricity_kwh'])) {
					$electricity_tariff_cost_per_kwh = $result['electricity'] * $result['total_electricity_kwh'];
				    } else {
					$electricity_tariff_cost_per_kwh = 0;
				    }
				    $calculated_result['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? ($electricity_tariff_cost_per_kwh) : 0;
				    $days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
				    // Based on give formula
				    $calculated_result['occupancy'] = (($result['total_room_night'] / ($result['rooms_keys'] * $days_of_month)) * 100);
				    $reportData[$result['month_id']][$result['year_id']] = $calculated_result;
				    }
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
			    $data['utility_cost_chart_carbon_footprint'] = $reportData;
			    $data['current_year'] = isset($_POST['yearly_report_year']) ? $_POST['yearly_report_year'] : date('Y');
			    $data['last_year'] = $data['current_year'] - 1;
			    break;
			case 'roomnight':
			    $cview_file = 'admin_index_roomnight';
			    $data['current_year'] = isset($_POST['yearly_report_year']) ? $_POST['yearly_report_year'] : date('Y');
			    $data['last_year'] = $data['current_year'] - 1;
			    $data['utility_cost_chart_roomnight']['utility_cost_chart_title'] = lang('utility-cost-chart-roomnight-title');
			    if (!empty($value['total_room_night'])) {
				if(isset($data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']])){
				    continue;
				} else {
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? ($value['electricity'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? ($value['fuel'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? ($value['fuel_consumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? ($value['lpg'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpgconsumption'])) ? ($value['lpgconsumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? ($value['natural_gas'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gasral_gas_consumption'])) ? ($value['natural_gasral_gas_consumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? ($value['heating_district'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_districting_district_consumption'])) ? ($value['heating_districting_district_consumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? ($value['cooling_district'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_districting_district_consumption'])) ? ($value['cooling_districting_district_consumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? ($value['water'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['waterr_consumption'])) ? ($value['waterr_consumption'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? ($value['cdd'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? ($value['hdd'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? ($value['total_budget'] / $value['total_room_night']) : 0;
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? ($value['total_electricity_kwh'] / $value['total_room_night']) : 0;
				if (!empty($value['total_electricity_kwh'])) {
				    $electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];
				} else {
				    $electricity_tariff_cost_per_kwh = 0;
				}
				$data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? ($electricity_tariff_cost_per_kwh / $value['total_room_night']) : 0;
				}
			    } else {
				if(isset($data['utility_cost_chart'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart'][$value['month_id']][$value['year_id']])) {
				    continue;
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
			    }
			    break;
			case 'budget':
			    $cview_file = 'admin_index_budget';
			    $data['utility_cost_chart_budget']['utility_cost_chart_budget_title'] = lang('utility-cost-chart-budget-title');
			    if(isset($data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']])) {
				continue;
			    } else {
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['budget'] = (!empty($value['total_budget'])) ? $value['total_budget'] : 0;
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;
			    if (!empty($value['total_electricity_kwh'])) {
				$electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];
			    } else {
				$electricity_tariff_cost_per_kwh = 0;
			    }
			    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;
			    }
			    break;
			default:
			    $data['utility_cost_chart']['utility_cost_chart_title'] = lang('utility-cost-chart-title');
			    if(isset($data['utility_cost_chart'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart'][$value['month_id']][$value['year_id']])) {
				continue;
			    } else {
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
			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];
			    if (!empty($value['total_electricity_kwh'])) {
				$electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];
			    } else {
				$electricity_tariff_cost_per_kwh = 0;
			    }
			    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;
			    }
			    break;
		    }
		    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];
		    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];
		    $days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);
		    $data['utility_cost_chart'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
		    $data['utility_cost_chart_roomnight'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
		    $data['utility_cost_chart_budget'][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
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
	    $data['utility_year_selected'] = $utility_year_selected;
	    $data['totalElectricityConsumption'] = $totalElectricityConsumption;
	    $data['totalFuelConsumption'] = $totalFuelConsumption;
	    $data['totalLpgConsumption'] = $totalLpgConsumption;
	    $data['totalNaturalGasConsumption'] = $totalNaturalGasConsumption;
	    $data['totalWaterConsumption'] = $totalWaterConsumption;
	    $data['totalHeatingDistrictConsumption'] = $totalHeatingDistrictConsumption;
	    $data['totalCoolingDistrictConsumption'] = $totalCoolingDistrictConsumption;
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
	if ($this->input->post('yearly_report_year')) {
	    $startdate_pre = '1/' . $this->input->post('yearly_report_year', date('Y'));
	    $enddate_pre = '12/' . $this->input->post('yearly_report_year', date('Y'));
	}
	$startdateexplode_pre = explode('/', $startdate_pre);
	$enddateexplode_pre = explode('/', $enddate_pre);
	$filters_comparision_chart_pre['startdate'] = (isset($startdate_pre)) ? $startdate_pre : '';
	$filters_comparision_chart_pre['enddate'] = (isset($enddate_pre)) ? $enddate_pre : '';
	$filters_comparision_chart_pre['start_month'] = (isset($startdateexplode_pre[0])) ? (int) $startdateexplode_pre[0] : '';
	$filters_comparision_chart_pre['start_year'] = (isset($startdateexplode_pre[1])) ? $startdateexplode_pre[1] : '';
	$filters_comparision_chart_pre['end_month'] = (isset($enddateexplode_pre[0])) ? (int) $enddateexplode_pre[0] : '';
	$filters_comparision_chart_pre['end_year'] = (isset($enddateexplode_pre[1])) ? $enddateexplode_pre[1] : '';
	$currentYear = date('Y'); //date('Y');
	if ($this->input->post('yearly_report_year')) {
	    $currentYear = $this->input->post('yearly_report_year');
	}
	$currentMonth = intval(date('m'));
	if ($currentMonth == 1) {
	    $currentYear = $currentYear - 1;
	    $currentMonth = 12;
	}
	$filters_comparision_chart_pre['currentYear'] = $currentYear;
	$filters_comparision_chart_pre['currentMonth'] = $currentMonth;
	if ($data['currency'] == "base") {
	    $utility_cost_chart_results_pre = $this->reports_forex_model->utilityCostBarChart($filters_comparision_chart_pre);
	} else {
	    $utility_cost_chart_results_pre = $this->reports_model->utilityCostBarChart($filters_comparision_chart_pre);
	}
	// for CO2 calculation indea annual report
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
		if (isset($data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    $value = array_map('floatval', $value);
		    $value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		    $value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];
		    $value['lpg'] = $value['lpg'] + $value['lpg_fixed_cost'];
		    $value['water'] = $value['water'] + $value['water_fixed_cost'];
		    $value['natural_gas'] = $value['natural_gas'] + $value['natural_gas_fixed_cost'];
		    $totalElectricity_utility_cost_pre += $value['electricity'];
		    $totalFuel_utility_cost_pre += $value['fuel'];
		    $totalLpg_utility_cost_pre += $value['lpg'];
		    $totalNaturalGas_utility_cost_pre += $value['natural_gas'];
		    $totalWater_utility_cost_pre += $value['water'];
		    $totalHeatingDistrict_utility_cost_pre += $value['heating_district'];
		    $totalCoolingDistrict_utility_cost_pre += $value['cooling_district'];
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['electricity_consumption'])) ? $value['electricity_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption']: 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;
		    $data['utility_cost_chart_pre'][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption']: 0;
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
	if ($this->input->post('yearly_report_year')) {
	    $filters_pre['report_year'] = $this->input->post('yearly_report_year');
	}
	$filters['report_year_pre'] = $filters_pre['report_year'];
	$kwh_report_results_pre = $this->reports_model->kwhUnitBasedReportForCurrentYear($filters_pre);
	if (!empty($kwh_report_results_pre)) {
	    $data['kwh_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? ($kwh_report_results_pre['electricity'] * $electricity_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? ($kwh_report_results_pre['fuel'] * $fuel_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? ($kwh_report_results_pre['lpg'] * $lpg_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? ($kwh_report_results_pre['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? ($kwh_report_results_pre['heating_district'] * $heating_district_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? ($kwh_report_results_pre['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	} else {
	    $data['kwh_pie_chart_pre'] = array();
	}
	// Cost Pie chart for current year
	if ($data['currency'] == "base") {
	    $kwh_report_results_pre = $this->reports_forex_model->costBasedReportForCurrentYear($filters_pre);
	} else {
	    $kwh_report_results_pre = $this->reports_model->costBasedReportForCurrentYear($filters_pre);
	}
	if (!empty($kwh_report_results_pre)) {
	    // $kwh_report_results_pre['cooling_district'] = $kwh_report_results_pre['cooling_district'] + $kwh_report_results_pre['district_cooling_fixed_cost'];
	    // $kwh_report_results_pre['heating_district'] = $kwh_report_results_pre['heating_district'] + $kwh_report_results_pre['district_heating_fixed_cost'];
	    $kwh_report_results_pre['cooling_district'] = $kwh_report_results_pre['cooling_district'];
	    $kwh_report_results_pre['heating_district'] = $kwh_report_results_pre['heating_district'];
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
	if ($data['currency'] == "base") {
	    $utility_cost_chart_results_5years = $this->reports_forex_model->utilityCostBarChartByYears($filters_comparision_chart_5years);
	} else {
	    $utility_cost_chart_results_5years = $this->reports_model->utilityCostBarChartByYears($filters_comparision_chart_5years);
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
		if(isset($data['utility_cost_chart_5years'][$value['year_id']]) && !empty($data['utility_cost_chart_5years'][$value['year_id']])) {
		    continue;
		} else {
		$value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		$value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];
		$value['lpg'] = $value['lpg'] + $value['lpg_fixed_cost'];
		$value['water'] = $value['water'] + $value['water_fixed_cost'];
		$value['natural_gas'] = $value['natural_gas'] + $value['natural_gas_fixed_cost'];
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
		$data['utility_cost_chart_5years'][$value['year_id']]['month_id'] = (isset($value['month_id'])) ? $value['month_id'] : '';
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
	if ($this->input->post('submit') == 'download_monthly_hidden') {
	    $filters['report_year_piechart'] = $this->input->post('monthly_report_year');
	    $filters['report_month_piechart'] = $this->input->post('monthly_report_month');
	    $kwh_report_results = $this->reports_model->kwhUnitBasedReportForCurrentYearPieCharts($filters);
	} else {
	    $kwh_report_results = $this->reports_model->kwhUnitBasedReportForCurrentYear($filters);
	}
	if (!empty($kwh_report_results)) {
	    $kwh_report_results = array_map('intval', $kwh_report_results);
	    $kwh_report_results['cooling_district'] = $kwh_report_results['cooling_district'] + $kwh_report_results['district_cooling_fixed_cost'];
	    $kwh_report_results['heating_district'] = $kwh_report_results['heating_district'] + $kwh_report_results['district_heating_fixed_cost'];
	    $kwh_report_results['lpg'] = $kwh_report_results['lpg'] + $kwh_report_results['lpg_fixed_cost'];
	    $kwh_report_results['natural_gas'] = $kwh_report_results['natural_gas'] + $kwh_report_results['natural_gas_fixed_cost'];
	    $kwh_report_results['water'] = $kwh_report_results['water'] + $kwh_report_results['water_fixed_cost'];
	    $data['kwh_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $electricity_mmbtu_rate) : 0;
	    $data['kwh_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $fuel_mmbtu_rate) : 0;
	    $data['kwh_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $lpg_mmbtu_rate) : 0;
	    $data['kwh_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
	    $data['kwh_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $heating_district_mmbtu_rate) : 0;
	    $data['kwh_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	} else {
	    $data['kwh_pie_chart'] = array();
	}
	// Cost Pie chart for current year
	if ($this->input->post('submit') == 'download_monthly_hidden') {
	    $filters['report_year_piechart'] = $this->input->post('monthly_report_year');
	    $filters['report_month_piechart'] = $this->input->post('monthly_report_month');
	    if ($data['currency'] == "base") {
		$kwh_report_results = $this->reports_forex_model->costBasedReportForCurrentYearPiecharts($filters);
	    } else {
		$kwh_report_results = $this->reports_model->costBasedReportForCurrentYearPiecharts($filters);
	    }
	} else {
	    if ($data['currency'] == "base") {
		$kwh_report_results = $this->reports_forex_model->costBasedReportForCurrentYear($filters);
	    } else {
		$kwh_report_results = $this->reports_model->costBasedReportForCurrentYear($filters);
	    }
	}
	if (!empty($kwh_report_results)) {
	    $data['cost_pie_chart']['electricity'] = (!empty($kwh_report_results['electricity'])) ? $kwh_report_results['electricity'] : 0;
	    $data['cost_pie_chart']['fuel'] = (!empty($kwh_report_results['fuel'])) ? $kwh_report_results['fuel'] : 0;
	    $data['cost_pie_chart']['lpg'] = (!empty($kwh_report_results['lpg'])) ? $kwh_report_results['lpg'] : 0;
	    $data['cost_pie_chart']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? $kwh_report_results['natural_gas'] : 0;
	    $data['cost_pie_chart']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? $kwh_report_results['heating_district'] : 0;
	    $data['cost_pie_chart']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? $kwh_report_results['cooling_district'] + $kwh_report_results['district_cooling_fixed_cost'] : 0;
	    $data['cost_pie_chart']['water'] = (!empty($kwh_report_results['water'])) ? $kwh_report_results['water'] : 0;
	} else {
	    $data['cost_pie_chart'] = array();
	}
	// KWh pie chart for last 12 months
	$kwh_report_results = $this->reports_model->kwhUnitBasedReportForPreviousMonth($filters);
	if (!empty($kwh_report_results)) {
	    $data['kwh_pie_chart_previousmonth']['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $electricity_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_previousmonth']['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $fuel_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_previousmonth']['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $lpg_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_previousmonth']['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_previousmonth']['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $heating_district_mmbtu_rate) : 0;
	    $data['kwh_pie_chart_previousmonth']['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	} else {
	    $data['kwh_pie_chart_previousmonth'] = array();
	}
	// Cost pie chart for last 12 months
	if ($data['currency'] == "base") {
	    $kwh_report_results = $this->reports_forex_model->costBasedReportForPreviousMonth($filters);
	} else {
	    $kwh_report_results = $this->reports_model->costBasedReportForPreviousMonth($filters);
	}
	if (!empty($kwh_report_results)) {
	    $kwh_report_results['cooling_district'] = $kwh_report_results['cooling_district'] + $kwh_report_results['district_cooling_fixed_cost'];
	    $kwh_report_results['heating_district'] = $kwh_report_results['heating_district'] + $kwh_report_results['district_heating_fixed_cost'];
	    $kwh_report_results['lpg'] = $kwh_report_results['lpg'] + $kwh_report_results['lpg_fixed_cost'];
	    $kwh_report_results['natural_gas'] = $kwh_report_results['natural_gas'] + $kwh_report_results['natural_gas_fixed_cost'];
	    $kwh_report_results['water'] = $kwh_report_results['water'] + $kwh_report_results['water_fixed_cost'];
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
	$start_year = date('Y');
	$start_month = 1;
	$end_year = date('Y');
	$end_month = intval(date('m')) - 1;
	// Change filter for monthly report pdf and annual report pdf
	if ($this->input->post('submit') == 'download_monthly_hidden') {
	    $start_month = $this->input->post('monthly_report_month');
	    $end_month = $this->input->post('monthly_report_month');
	    $start_year = $this->input->post('monthly_report_year');
	    $end_year = $start_year;
	} else if ($this->input->post('submit') == 'download_hidden') {
	    $start_year = date('Y') - 1;
	    $end_year = $start_year;
	    $end_month = 12;
	}
	$filter_budget_actual_comparision = [
	    'site_id' => $this->session->userdata[$this->section_name]['site_id'],
	    'start_month' => $start_month,
	    'end_month' => $end_month,
	    'start_year' => $start_year,
	    'end_year' => $end_year,
	];
	if ($data['currency'] == "base") {
	    $current_budget_actual_array = $this->reports_forex_model->getUtilityActualBudgetData($filter_budget_actual_comparision);
	} else {
	    $current_budget_actual_array = $this->reports_model->getUtilityActualBudgetData($filter_budget_actual_comparision);
	}
	$current_budget_actual_data = [];
	$current_budget_actual_data['months'] = 0;
	foreach ($current_budget_actual_array as $key => $value) {
	    $value = array_map('intval', $value);
	    $current_budget_actual_data['months'] += 1;
	    $current_budget_actual_data['total_room_night'] += $value['total_room_night'];
	    $current_budget_actual_data['hdd'] += $value['hdd'];
	    $current_budget_actual_data['cdd'] += $value['cdd'];
	    $current_budget_actual_data["district_cooling_actual"] += $value["district_cooling_actual"];
	    $current_budget_actual_data["district_cooling_budget"] += $value["district_cooling_budget"];
	    $current_budget_actual_data["district_cooling_cost_actual"] += $value["district_cooling_cost_actual"];
	    $current_budget_actual_data["district_cooling_cost_budget"] += $value["district_cooling_cost_budget"];
	    $current_budget_actual_data["district_heating_actual"] += $value["district_heating_actual"];
	    $current_budget_actual_data["district_heating_budget"] += $value["district_heating_budget"];
	    $current_budget_actual_data["district_heating_cost_actual"] += $value["district_heating_cost_actual"];
	    $current_budget_actual_data["district_heating_cost_budget"] += $value["district_heating_cost_budget"];
	    $current_budget_actual_data["total_electricity_kwh_actual"] += $value["total_electricity_kwh_actual"];
	    $current_budget_actual_data["total_electricity_kwh_budget"] += $value["total_electricity_kwh_budget"];
	    $current_budget_actual_data["total_electricity_cost_actual"] += $value["total_electricity_cost_actual"];
	    $current_budget_actual_data["total_electricity_cost_budget"] += $value["total_electricity_cost_budget"];
	    $current_budget_actual_data["total_fuel_oil_actual"] += $value["total_fuel_oil_actual"];
	    $current_budget_actual_data["total_fuel_oil_budget"] += $value["total_fuel_oil_budget"];
	    $current_budget_actual_data["total_fuel_oil_cost_actual"] += $value["total_fuel_oil_cost_actual"];
	    $current_budget_actual_data["total_fuel_oil_cost_budget"] += $value["total_fuel_oil_cost_budget"];
	    $current_budget_actual_data["total_lpg_actual"] += $value["total_lpg_actual"];
	    $current_budget_actual_data["total_lpg_budget"] += $value["total_lpg_budget"];
	    $current_budget_actual_data["total_lpg_cost_actual"] += $value["total_lpg_cost_actual"];
	    $current_budget_actual_data["total_lpg_cost_budget"] += $value["total_lpg_cost_budget"];
	    $current_budget_actual_data["total_natural_gas_actual"] += $value["total_natural_gas_actual"];
	    $current_budget_actual_data["total_natural_gas_budget"] += $value["total_natural_gas_budget"];
	    $current_budget_actual_data["total_natural_gas_cost_actual"] += $value["total_natural_gas_cost_actual"];
	    $current_budget_actual_data["total_natural_gas_cost_budget"] += $value["total_natural_gas_cost_budget"];
	    $current_budget_actual_data["water_total_consumption_actual"] += $value["water_total_consumption_actual"];
	    $current_budget_actual_data["water_total_consumption_budget"] += $value["water_total_consumption_budget"];
	    $current_budget_actual_data["water_total_consumption_cost_actual"] += $value["water_total_consumption_cost_actual"];
	    $current_budget_actual_data["water_total_consumption_cost_budget"] += $value["water_total_consumption_cost_budget"];
	}
	$data['currentBudgetActualData'] = $current_budget_actual_data;
	$data['filters'] = $filters;
	$this->breadcrumb->add(lang('reports'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports');
	$this->theme->set('page_title', lang('reports'));
	$view_type = $this->input->post('view_type', '');
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$this->load->model('sites/sites_model');
	$this->sites_model->year = $start_year;
	$site_detail_result = $this->sites_model->get_site_detail_custom($site_id);
	$data['site_detail'] = $site_detail_result;
	$data['site_id'] = $site_id;
	//measure readings
	$measure_readings = $this->sites_model->get_measure_readings($site_id);
	foreach ($measure_readings as $m_reading) {
	    $data['measure_readings'][$m_reading['measure_id']] = $m_reading;
	}
	if ($view_type == 'pdf') {
	    if ($this->input->is_ajax_request()) {
		$msg = $this->theme->view($data, $cview_file, true);
		echo $msg;
	    } else {
		$this->generate_report_pdf($data);
	    }
	} else if ($view_type == 'excel') {
	    if ($this->input->is_ajax_request()) {
		$msg = $this->theme->view($data, $cview_file, true);
		echo $msg;
	    } else {
		$this->generate_report_excel($data);
	    }
	} else {
	    $currYear = $this->input->post('monthly_report_year') ? $this->input->post('monthly_report_year') : date('Y');
	    $currMonth = $this->input->post('monthly_report_month') ? $this->input->post('monthly_report_month') : 12;
	    $data = $this->CalculateMeasures($data, $site_detail_result, $currYear, $currMonth);
	    $this->theme->view($data, $cview_file);
	}
    }

    public function getMonthlyReportChart()
    {
	//error_reporting(E_ALL);
	//ini_set('display_error', 1);
	$startdate = $this->input->post('monthly_report_month', 1) . '/' . $this->input->post('monthly_report_year', date('Y'));
	$enddate = $this->input->post('monthly_report_month', 12) . '/' . $this->input->post('monthly_report_year', date('Y'));
	$currency = $this->input->post('currency', 'local');
	$decimal_places = 3;
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
	$site_details = $this->sites_model->get_site_detail_custom($this->reports_model->site_id);
	if ($currency == "base") {
	    $utility_cost_chart_results = $this->reports_forex_model->utilityCostBarChart($filters_comparision_chart);
	} else {
	    $utility_cost_chart_results = $this->reports_model->utilityCostBarChart($filters_comparision_chart);
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
		if (isset($utility_cost_chart[$value['month_id']][$value['year_id']]) && !empty($utility_cost_chart[$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    $value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		    $value['heating_district'] = floatval((string) $value['heating_district']) + floatval((string) $value['district_heating_fixed_cost']);
		    $value['lpg'] = floatval((string) $value['lpg']) + floatval((string) $value['lpg_fixed_cost']);
		    $value['natural_gas'] = floatval((string) $value['natural_gas']) + floatval((string) $value['natural_gas_fixed_cost']);
		    $value['water'] = floatval((string) $value['water']) + floatval((string) $value['water_fixed_cost']);
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
	    }
	    $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $resultkeysMonthlyreport = array();
	    $resultkeysMonthlyreport[$filters_comparision_chart['start_year']] = array($filters_comparision_chart['start_month']);
	    foreach ($resultkeysMonthlyreport as $year => $value) {
		foreach ($value as $key1 => $month) {
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['electricity'])) ? $utility_cost_chart[$month][$year - 1]['electricity'] : 0;
		    $pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;
		    $pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;
		    $pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas'] : 0;
		    $pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;
		    $pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;
		    $pre_data_electricity_consumption = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] - $utility_cost_chart[$month][$year - 1]['onsite_generator'] - $utility_cost_chart[$month][$year - 1]['renewable_energy']) : 0;
		    $pre_data_fuel_consumption = (!empty($utility_cost_chart[$month][$year - 1]['fuel_consumption'])) ? ($utility_cost_chart[$month][$year - 1]['fuel_consumption'] - $utility_cost_chart[$month][$year - 1]['onsite_generator_fuel_oil']) : 0;
		    $pre_data_lpg_consumption = (!empty($utility_cost_chart[$month][$year - 1]['lpg_consumption'])) ? $utility_cost_chart[$month][$year - 1]['lpg_consumption'] : 0;
		    $pre_data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? ($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] - $utility_cost_chart[$month][$year - 1]['onsite_generator_natural_gas']) : 0;
		    $pre_data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['heating_district_consumption'] : 0;
		    $pre_data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district_consumption'] : 0;
		    $pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;
		    $pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;
		    $pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;
		    $pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;
		    $pre_data_budget = (!empty($utility_cost_chart[$month][$year - 1]['budget'])) ? $utility_cost_chart[$month][$year - 1]['budget'] : 0;
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
	// get kwh data for monthly selected
	$pie_filters = array();
	$pie_filters['report_year_piechart'] = $this->input->post('monthly_report_year');
	$pie_filters['report_month_piechart'] = $this->input->post('monthly_report_month');
	if ($currency == "base") {
	    $cost_pie_chart_results = $this->reports_forex_model->costBasedReportForCurrentYearPiecharts($pie_filters);
	} else {
	    $cost_pie_chart_results = $this->reports_model->costBasedReportForCurrentYearPiecharts($pie_filters);
	}
	$kwh_report_results = $this->reports_model->kwhUnitBasedReportForCurrentYearPieCharts($pie_filters);
	// $kwh_report_results = $this->reports_model->kwhUnitBasedReportForCurrentYearPieChartsForMonthly($pie_filters);
	// calculation for monthly MJ
		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');

	$kwh_report_results = array_map('intval', $kwh_report_results);
	$kwh_pie_chart['electricity'] = (!empty($kwh_report_results['electricity'])) ? ($kwh_report_results['electricity'] * $electricity_mmbtu_rate) : 0;
	$kwh_pie_chart['fuel'] = (!empty($kwh_report_results['fuel'])) ? ($kwh_report_results['fuel'] * $fuel_mmbtu_rate) : 0;
	$kwh_pie_chart['lpg'] = (!empty($kwh_report_results['lpg'])) ? ($kwh_report_results['lpg'] * $lpg_mmbtu_rate) : 0;
	$kwh_pie_chart['natural_gas'] = (!empty($kwh_report_results['natural_gas'])) ? ($kwh_report_results['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
	$kwh_pie_chart['heating_district'] = (!empty($kwh_report_results['heating_district'])) ? ($kwh_report_results['heating_district'] * $heating_district_mmbtu_rate) : 0;
	$kwh_pie_chart['cooling_district'] = (!empty($kwh_report_results['cooling_district'])) ? ($kwh_report_results['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	$kwh_pie_chart_electricity_value = $kwh_pie_chart['electricity'];
	$kwh_pie_chart_fuel_value = $kwh_pie_chart['fuel'];
	$kwh_pie_chart_lpg_value = $kwh_pie_chart['lpg'];
	$kwh_pie_chart_natural_gas_value = $kwh_pie_chart['natural_gas'];
	$kwh_pie_chart_heating_district_value = $kwh_pie_chart['heating_district'];
	$kwh_pie_chart_cooling_district_value = $kwh_pie_chart['cooling_district'];
	$kwh_pie_chart_utility_kwh_total = ($kwh_pie_chart_electricity_value + $kwh_pie_chart_fuel_value + $kwh_pie_chart_lpg_value + $kwh_pie_chart_natural_gas_value + $kwh_pie_chart_heating_district_value + $kwh_pie_chart_cooling_district_value);
	$kwh_pie_chart_electricity_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_electricity_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$kwh_pie_chart_fuel_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_fuel_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$kwh_pie_chart_lpg_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_lpg_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$kwh_pie_chart_natural_gas_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_natural_gas_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$kwh_pie_chart_heating_district_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_heating_district_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$kwh_pie_chart_cooling_district_share = ($kwh_pie_chart_utility_kwh_total != 0) ? round(($kwh_pie_chart_cooling_district_value * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
	$total_share = ($kwh_pie_chart_electricity_share + $kwh_pie_chart_fuel_share + $kwh_pie_chart_lpg_share + $kwh_pie_chart_natural_gas_share + $kwh_pie_chart_heating_district_share + $kwh_pie_chart_cooling_district_share);
	$kwh_chart_pie_data[] = array(
	    'Utility',
	    'Value',
	);
	if ($kwh_pie_chart_electricity_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$electricityTitle,
		$kwh_pie_chart_electricity_share,
	    );
	}
	if ($kwh_pie_chart_fuel_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$fuelTitle,
		$kwh_pie_chart_fuel_share,
	    );
	}
	if ($kwh_pie_chart_lpg_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$lpgTitle,
		$kwh_pie_chart_lpg_share,
	    );
	}
	if ($kwh_pie_chart_natural_gas_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$naturalTitle,
		$kwh_pie_chart_natural_gas_share,
	    );
	}
	if ($kwh_pie_chart_heating_district_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$heatingTitle,
		$kwh_pie_chart_heating_district_share,
	    );
	}
	if ($kwh_pie_chart_cooling_district_share != 0) {
	    $kwh_chart_pie_data[] = array(
		$coolingTitle,
		$kwh_pie_chart_cooling_district_share,
	    );
	}
	$dateObj = DateTime::createFromFormat('!m', $pie_filters['report_month_piechart']);
	$monthName = $dateObj->format('F');
	$cost_pie_chart['electricity'] = (!empty($cost_pie_chart_results['electricity'])) ? $cost_pie_chart_results['electricity'] : 0;
	$cost_pie_chart['fuel'] = (!empty($cost_pie_chart_results['fuel'])) ? $cost_pie_chart_results['fuel'] : 0;
	$cost_pie_chart['lpg'] = (!empty($cost_pie_chart_results['lpg'])) ? $cost_pie_chart_results['lpg'] : 0;
	$cost_pie_chart['natural_gas'] = (!empty($cost_pie_chart_results['natural_gas'])) ? $cost_pie_chart_results['natural_gas'] : 0;
	$cost_pie_chart['heating_district'] = (!empty($cost_pie_chart_results['heating_district'])) ? $cost_pie_chart_results['heating_district'] : 0;
	$cost_pie_chart['cooling_district'] = (!empty($cost_pie_chart_results['cooling_district'])) ? $cost_pie_chart_results['cooling_district'] : 0;
	$cost_pie_chart['water'] = (!empty($cost_pie_chart_results['water'])) ? $cost_pie_chart_results['water'] : 0;
	$cost_pie_chart_t_sum = ($cost_pie_chart['electricity'] + $cost_pie_chart['fuel'] + $cost_pie_chart['lpg'] + $cost_pie_chart['natural_gas'] + $cost_pie_chart['heating_district'] + $cost_pie_chart['cooling_district'] + $cost_pie_chart['water']);
	// Calculation for percantage share
	$kwh_pie_chart_cmonth_electricity_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['electricity'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_fuel_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['fuel'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_lPG_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['lpg'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_gas_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['natural_gas'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_heating_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['heating_district'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_cooling_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['cooling_district'] * 100) / $cost_pie_chart_t_sum) : 0;
	$kwh_pie_chart_cmonth_water_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['water'] * 100) / $cost_pie_chart_t_sum) : 0;
	$cost_chart_pie_data[] = array(
	    'Utility',
	    'Value',
	);
	if ($kwh_pie_chart_cmonth_electricity_share != 0) {
	    $cost_chart_pie_data[] = array(
		$electricityTitle,
		$kwh_pie_chart_cmonth_electricity_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_fuel_share != 0) {
	    $cost_chart_pie_data[] = array(
		$fuelTitle,
		$kwh_pie_chart_cmonth_fuel_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_lPG_share != 0) {
	    $cost_chart_pie_data[] = array(
		$lpgTitle,
		$kwh_pie_chart_cmonth_lPG_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_gas_share != 0) {
	    $cost_chart_pie_data[] = array(
		$naturalTitle,
		$kwh_pie_chart_cmonth_gas_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_heating_share != 0) {
	    $cost_chart_pie_data[] = array(
		$heatingTitle,
		$kwh_pie_chart_cmonth_heating_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_cooling_share != 0) {
	    $cost_chart_pie_data[] = array(
		$coolingTitle,
		$kwh_pie_chart_cmonth_cooling_share,
	    );
	}
	if ($kwh_pie_chart_cmonth_water_share != 0) {
	    $cost_chart_pie_data[] = array(
		'Water',
		$kwh_pie_chart_cmonth_water_share,
	    );
	}
	$this->load->model('sites/sites_model');
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$site_detials = $this->sites_model->get_site_detail_custom($site_id);
	if ($site_detials['chsb_reporting']) {
	    $currYear = $this->input->post('monthly_report_year') ? $this->input->post('monthly_report_year') : date('Y');
	    $currMonth = $this->input->post('monthly_report_month') ? $this->input->post('monthly_report_month') : 12;
	    //measure readings
	    $measure_readings = $this->sites_model->get_measure_readings($site_id);
	    foreach ($measure_readings as $m_reading) {
		$measure_reading_data['measure_readings'][$m_reading['measure_id']] = $m_reading;
	    }
	    $chsb_data = $this->CalculateMeasures($data, $site_detials, $currYear, $currMonth);
	    $data['chsb_measures_chart_data']['measures'] = $chsb_data['measures'];
	    $data['chsb_measures_chart_data']['measure_readings'] = $measure_reading_data['measure_readings'];
	}
	$data['chart_data'] = array_values($chart_data);
	$data['carbon_footprint'] = array_values($carbon_footprint);
	$data['chart_index'] = array_values($chart_index);
	$data['chart_index_carbon'] = array_values($chart_index_carbon);
	$data['chart_waste_data'] = [];
	$data['cost_pie_chart'] = array_values($cost_chart_pie_data);
	$data['kwh_pie_chart'] = array_values($kwh_chart_pie_data);
		$data['kwh_pie_chart_title'] = 'Energy Consumption (kwh) - ' . $monthName . ' ' . $pie_filters['report_year_piechart'];
	$data['cost_pie_chart_title'] = 'Utilities Cost - ' . $monthName . ' ' . $pie_filters['report_year_piechart'];
	$data['chart_pie_data'] = [];
	echo json_encode($data, JSON_NUMERIC_CHECK);
	exit;
    }

    public function roomnight()
    {
	$this->index('roomnight');
    }

    public function carbon_footprint()
    {
	$this->index('carbon_footprint');
    }

    public function management()
    {
	if (!UTILITIES_DAILY_MENU) {
	    redirect("/dashboard");
	}
	$this->breadcrumb->add(lang('10-days-management-report'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/managment');
	$this->load->model('sites/sites_model');
	$user_id = $this->reports_model->user_id;
	$site_id = $this->reports_model->site_id;
	$role_id = $this->reports_model->role_id;
	$montharray       = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	$month            = (int) date('m');
	$year             = (int) date('Y');
	$data             = array();
	$utility_selected = '';
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $data = $this->input->post();
	    if (isset($data['month'])) {
		$month = (int) $this->input->post('month');
	    }
	    if (isset($data['year'])) {
		$year = (int) $this->input->post('year');
	    }
	    if ($data['utility_select']) {
		$utility_selected = $data['utility_select'];
	    }
	}
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$data['site_detail'] = $site_detail;
	$data['site_id'] = $site_id;
	$last_year_month = $month;
	$last_year       = $year - 1;
	$totalDays       = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$totalDays_last  = (int) cal_days_in_month(CAL_GREGORIAN, $last_year_month, $last_year);
	$today_date      = (int) date('d');
	if ($month == (int) date('m') && $year == (int) date('Y')) {
	    $to_date           = $today_date - 1;
	    $to_date_last_year = $today_date - 1;
	} else {
	    $to_date           = $totalDays;
	    $to_date_last_year = $totalDays_last;
	}
	//Get selected month Data
	$this->reports_model->site_id = $site_id;
	$selected_month_year_data     = $this->reports_model->getDailyReportData($month, $year, $to_date);
	$last_month_year_data         = $this->reports_model->getDailyReportData($last_year_month, $last_year, $to_date_last_year);
	// Get budget data
	$filters['startdate']   = $month . '/' . $year;
	$filters['enddate']     = $month . '/' . $year;
	$filters['start_month'] = $month;
	$filters['start_year']  = $year;
	$filters['end_month']   = $month;
	$filters['end_year']    = $year;
	$monthly_utility_by_unit = $this->reports_model->unitBudget($filters);
	$monthly_utility_by_cost = $this->reports_model->costBudget($filters);
	$monthly_utility = array();
	if (!empty($monthly_utility_by_unit)) {
	    foreach ($monthly_utility_by_unit as $key => $result) {
		$monthly_utility['unit'][$result['year_id']][$result['month_id']] = $result;
	    }
	}
	if (!empty($monthly_utility_by_cost)) {
	    foreach ($monthly_utility_by_cost as $key => $result) {
		$monthly_utility['cost'][$result['year_id']][$result['month_id']] = $result;
	    }
	}
	$data['utility_key_array'] = array();
	$electricity_unit = GetSiteUtilityUnitName($site_id, 'electricity');
	$fuel_oil_unit = GetSiteUtilityUnitName($site_id, 'fuel_oil');
	$lpg_unit = GetSiteUtilityUnitName($site_id, 'lpg');
	$water_unit = GetSiteUtilityUnitName($site_id, 'water');
	$natural_gas_unit = GetSiteUtilityUnitName($site_id, 'natural_gas');
	$district_cooling_unit = GetSiteUtilityUnitName($site_id, 'district_cooling');
	$district_heating_unit = GetSiteUtilityUnitName($site_id, 'district_heating');
	$data['utility_key_array'] = array();
	if ($site_detail['show_utility_electricity']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'electricity_kwh',
		'title'      => 'Electricity',
		'unit'       =>  $electricity_unit,
		'budget_key' => 'electricity_total_budget',
	    );
	}
	if ($site_detail['show_utility_fuel_oil']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'diesel_fuel',
		'title'      => 'Diesel Fuel',
		'unit'       =>  $fuel_oil_unit,
		'budget_key' => 'fuel_total_budget',
	    );
	}
	if ($site_detail['show_utility_lpg']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'lpg_consumption',
		'title'      => 'L.P. Gas',
		'unit'       =>  $lpg_unit,
		'budget_key' => 'lpg_total_budget',
	    );
	}
	if ($site_detail['show_utility_water']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'water_consumption',
		'title'      => 'Water',
		'unit'       =>  $water_unit,
		'budget_key' => 'water_total_consumption_budget',
	    );
	}
	if ($site_detail['show_utility_natural_gas']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'natural_gas_consumption',
		'title'      => 'Natural Gas',
		'unit'       =>  $natural_gas_unit,
		'budget_key' => 'natural_gas_total_budget',
	    );
	}
	if ($site_detail['show_utility_district_cooling']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'district_cooling_consumption',
		'title'      => 'District Cooling',
		'unit'       =>  $district_cooling_unit,
		'budget_key' => 'district_cooling_total_budget',
	    );
	}
	if ($site_detail['show_utility_district_heating']) {
	    $data['utility_key_array'][] = array(
		'db_key'     => 'district_heating_consumption',
		'title'      => 'District Heating',
		'unit'       =>  $district_heating_unit,
		'budget_key' => 'district_heating_total_budget',
	    );
	}
	// Prepare data
	$data['month']                                                   = $month;
	$data['year']                                                    = $year;
	$data['last_year']                                               = $last_year;
	$data['to_date']                                                 = $to_date;
	$data['current_year']['total_room_night']                        = 0;
	$data['current_year']['cdd']                                     = 0;
	$data['current_year']['hdd']                                     = 0;
	$data['current_year']['total_guests']                            = 0;
	$data['current_year']['total_electricity_kwh']                   = 0;
	$data['current_year']['total_electricity_kwh_cost']              = 0;
	$data['current_year']['total_diesel_fuel']                       = 0;
	$data['current_year']['total_diesel_fuel_cost']                  = 0;
	$data['current_year']['total_lpg_consumption']                   = 0;
	$data['current_year']['total_lpg_consumption_cost']              = 0;
	$data['current_year']['total_water_consumption']                 = 0;
	$data['current_year']['total_water_consumption_cost']            = 0;
	$data['current_year']['total_landscape_water_consumption']       = 0;
	$data['current_year']['total_landscape_water_consumption_cost']  = 0;
	$data['current_year']['total_waste_water_consumption']       = 0;
	$data['current_year']['total_waste_water_consumption_cost']  = 0;
	$data['current_year']['total_natural_gas_consumption']           = 0;
	$data['current_year']['total_natural_gas_consumption_cost']      = 0;
	$data['current_year']['total_district_cooling_consumption']      = 0;
	$data['current_year']['total_district_cooling_consumption_cost'] = 0;
	$data['current_year']['total_district_heating_consumption']      = 0;
	$data['current_year']['total_district_heating_consumption_cost'] = 0;
	$data['current_year']['occupancy']                               = 0;
	$data['previous_year']['total_room_night']                        = 0;
	$data['previous_year']['cdd']                                     = 0;
	$data['previous_year']['hdd']                                     = 0;
	$data['previous_year']['total_guests']                            = 0;
	$data['previous_year']['total_electricity_kwh']                   = 0;
	$data['previous_year']['total_electricity_kwh_cost']              = 0;
	$data['previous_year']['total_diesel_fuel']                       = 0;
	$data['previous_year']['total_diesel_fuel_cost']                  = 0;
	$data['previous_year']['total_lpg_consumption']                   = 0;
	$data['previous_year']['total_lpg_consumption_cost']              = 0;
	$data['previous_year']['total_water_consumption']                 = 0;
	$data['previous_year']['total_water_consumption_cost']            = 0;
	$data['previous_year']['total_landscape_water_consumption']       = 0;
	$data['previous_year']['total_landscape_water_consumption_cost']  = 0;
	$data['previous_year']['total_waste_water_consumption']       = 0;
	$data['previous_year']['total_waste_water_consumption_cost']  = 0;
	$data['previous_year']['total_natural_gas_consumption']           = 0;
	$data['previous_year']['total_natural_gas_consumption_cost']      = 0;
	$data['previous_year']['total_district_cooling_consumption']      = 0;
	$data['previous_year']['total_district_cooling_consumption_cost'] = 0;
	$data['previous_year']['total_district_heating_consumption']      = 0;
	$data['previous_year']['total_district_heating_consumption_cost'] = 0;
	$data['previous_year']['occupancy']                               = 0;
	foreach ($selected_month_year_data as $value) {
	    $value = array_map('floatval', $value);
	    $total_electricity_kwh_cost              = ($value['total_electricity_kwh'] * $value['total_electricity_kwh_tariff']);
	    $total_diesel_fuel_cost                  = ($value['total_diesel_fuel'] * $value['total_diesel_fuel_tariff']);
	    $total_lpg_consumption_cost              = ($value['total_lpg_consumption'] * $value['total_lpg_consumption_tariff']);
	    $total_water_consumption_cost            = ($value['total_water_consumption'] * $value['total_water_consumption_tariff']);
	    $total_landscape_water_consumption_cost  = ($value['total_landscape_water_consumption'] * $value['total_landscape_water_consumption_tariff']);
	    $total_waste_water_consumption_cost  = ($value['total_waste_water_consumption'] * $value['total_waste_water_consumption_tariff']);
	    $total_natural_gas_consumption_cost      = ($value['total_natural_gas_consumption'] * $value['total_natural_gas_consumption_tariff']);
	    $total_district_cooling_consumption_cost = ($value['total_district_cooling_consumption'] * $value['total_district_cooling_consumption_tariff']);
	    $total_district_heating_consumption_cost = ($value['total_district_heating_consumption'] * $value['total_district_heating_consumption_tariff']);
	    // Merge irrigation and waste water in water section
	    $total_water_consumption_cost = $total_water_consumption_cost + $total_landscape_water_consumption_cost + $total_waste_water_consumption_cost;
	    $data['current_year']['total_room_night'] += $value['total_room_night'];
	    $data['current_year']['cdd'] += $value['cdd'];
	    $data['current_year']['hdd'] += $value['hdd'];
	    $data['current_year']['total_guests'] += $value['total_guests'];
	    $data['current_year']['total_electricity_kwh'] += $value['total_electricity_kwh'];
	    $data['current_year']['total_diesel_fuel'] += $value['total_diesel_fuel'];
	    $data['current_year']['total_lpg_consumption'] += $value['total_lpg_consumption'];
	    $data['current_year']['total_water_consumption'] += $value['total_water_consumption'];
	    $data['current_year']['total_landscape_water_consumption'] += $value['total_landscape_water_consumption'];
	    $data['current_year']['total_waste_water_consumption'] += $value['total_waste_water_consumption'];
	    $data['current_year']['total_natural_gas_consumption'] += $value['total_natural_gas_consumption'];
	    $data['current_year']['total_district_cooling_consumption'] += $value['total_district_cooling_consumption'];
	    $data['current_year']['total_district_heating_consumption'] += $value['total_district_heating_consumption'];
	    $data['current_year']['total_electricity_kwh_cost'] += $total_electricity_kwh_cost;
	    $data['current_year']['total_diesel_fuel_cost'] += $total_diesel_fuel_cost;
	    $data['current_year']['total_lpg_consumption_cost'] += $total_lpg_consumption_cost;
	    $data['current_year']['total_water_consumption_cost'] += $total_water_consumption_cost;
	    $data['current_year']['total_landscape_water_consumption_cost'] += $total_landscape_water_consumption_cost;
	    $data['current_year']['total_waste_water_consumption_cost'] += $total_waste_water_consumption_cost;
	    $data['current_year']['total_natural_gas_consumption_cost'] += $total_natural_gas_consumption_cost;
	    $data['current_year']['total_district_cooling_consumption_cost'] += $total_district_cooling_consumption_cost;
	    $data['current_year']['total_district_heating_consumption_cost'] += $total_district_heating_consumption_cost;
	}
	foreach ($last_month_year_data as $value) {
	    $value = array_map('floatval', $value);
	    $total_electricity_kwh_cost              = ($value['total_electricity_kwh'] * $value['total_electricity_kwh_tariff']);
	    $total_diesel_fuel_cost                  = ($value['total_diesel_fuel'] * $value['total_diesel_fuel_tariff']);
	    $total_lpg_consumption_cost              = ($value['total_lpg_consumption'] * $value['total_lpg_consumption_tariff']);
	    $total_water_consumption_cost            = ($value['total_water_consumption'] * $value['total_water_consumption_tariff']);
	    $total_landscape_water_consumption_cost  = ($value['total_landscape_water_consumption'] * $value['total_landscape_water_consumption_tariff']);
	    $total_waste_water_consumption_cost  = ($value['total_waste_water_consumption'] * $value['total_waste_water_consumption_tariff']);
	    $total_natural_gas_consumption_cost      = ($value['total_natural_gas_consumption'] * $value['total_natural_gas_consumption_tariff']);
	    $total_district_cooling_consumption_cost = ($value['total_district_cooling_consumption'] * $value['total_district_cooling_consumption_tariff']);
	    $total_district_heating_consumption_cost = ($value['total_district_heating_consumption'] * $value['total_district_heating_consumption_tariff']);
	    // Merge irrigation and waste water in water section
	    $total_water_consumption_cost = $total_water_consumption_cost + $total_landscape_water_consumption_cost + $total_waste_water_consumption_cost;
	    $data['previous_year']['total_room_night'] += $value['total_room_night'];
	    $data['previous_year']['cdd'] += $value['cdd'];
	    $data['previous_year']['hdd'] += $value['hdd'];
	    $data['previous_year']['total_guests'] += $value['total_guests'];
	    $data['previous_year']['total_electricity_kwh'] += $value['total_electricity_kwh'];
	    $data['previous_year']['total_diesel_fuel'] += $value['total_diesel_fuel'];
	    $data['previous_year']['total_lpg_consumption'] += $value['total_lpg_consumption'];
	    $data['previous_year']['total_water_consumption'] += $value['total_water_consumption'];
	    $data['previous_year']['total_landscape_water_consumption'] += $value['total_landscape_water_consumption'];
	    $data['previous_year']['total_waste_water_consumption'] += $value['total_waste_water_consumption'];
	    $data['previous_year']['total_natural_gas_consumption'] += $value['total_natural_gas_consumption'];
	    $data['previous_year']['total_district_cooling_consumption'] += $value['total_district_cooling_consumption'];
	    $data['previous_year']['total_district_heating_consumption'] += $value['total_district_heating_consumption'];
	    $data['previous_year']['total_electricity_kwh_cost'] += $total_electricity_kwh_cost;
	    $data['previous_year']['total_diesel_fuel_cost'] += $total_diesel_fuel_cost;
	    $data['previous_year']['total_lpg_consumption_cost'] += $total_lpg_consumption_cost;
	    $data['previous_year']['total_water_consumption_cost'] += $total_water_consumption_cost;
	    $data['previous_year']['total_landscape_water_consumption_cost'] += $total_landscape_water_consumption_cost;
	    $data['previous_year']['total_waste_water_consumption_cost'] += $total_waste_water_consumption_cost;
	    $data['previous_year']['total_natural_gas_consumption_cost'] += $total_natural_gas_consumption_cost;
	    $data['previous_year']['total_district_cooling_consumption_cost'] += $total_district_cooling_consumption_cost;
	    $data['previous_year']['total_district_heating_consumption_cost'] += $total_district_heating_consumption_cost;
	}
	if (isset($site_detail['rooms_keys']) && !empty($site_detail['rooms_keys'])) {
	    $data['current_year']['occupancy'] = (($data['current_year']['total_room_night'] / ($site_detail['rooms_keys'] * $to_date)) * 100);
	}
	if (isset($site_detail['rooms_keys']) && !empty($site_detail['rooms_keys'])) {
	    $data['previous_year']['occupancy'] = (($data['previous_year']['total_room_night'] / ($site_detail['rooms_keys'] * $to_date_last_year)) * 100);
	}
	$data['current_year']['electricity_total_budget']       = (!empty($monthly_utility['unit'][$year][$month]['electricity_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['electricity_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['fuel_total_budget']              = (!empty($monthly_utility['unit'][$year][$month]['fuel_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['fuel_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['lpg_total_budget']               = (!empty($monthly_utility['unit'][$year][$month]['lpg_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['lpg_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['natural_gas_total_budget']       = (!empty($monthly_utility['unit'][$year][$month]['natural_gas_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['natural_gas_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['district_heating_total_budget']  = (!empty($monthly_utility['unit'][$year][$month]['district_heating_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['district_heating_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['district_cooling_total_budget']  = (!empty($monthly_utility['unit'][$year][$month]['district_cooling_total_budget'])) ? (($monthly_utility['unit'][$year][$month]['district_cooling_total_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['water_total_consumption_budget'] = (!empty($monthly_utility['unit'][$year][$month]['water_total_consumption_budget'])) ? (($monthly_utility['unit'][$year][$month]['water_total_consumption_budget'] / $totalDays) * $to_date) : 0;
	$data['current_year']['electricity_total_budget_cost']       = (!empty($monthly_utility['cost'][$year][$month]['electricity_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['electricity_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['fuel_total_budget_cost']              = (!empty($monthly_utility['cost'][$year][$month]['fuel_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['fuel_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['lpg_total_budget_cost']               = (!empty($monthly_utility['cost'][$year][$month]['lpg_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['lpg_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['natural_gas_total_budget_cost']       = (!empty($monthly_utility['cost'][$year][$month]['natural_gas_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['natural_gas_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['district_heating_total_budget_cost']  = (!empty($monthly_utility['cost'][$year][$month]['district_heating_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['district_heating_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['district_cooling_total_budget_cost']  = (!empty($monthly_utility['cost'][$year][$month]['district_cooling_total_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['district_cooling_total_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['water_total_consumption_budget_cost'] = (!empty($monthly_utility['cost'][$year][$month]['water_total_consumption_budget_cost'])) ? (($monthly_utility['cost'][$year][$month]['water_total_consumption_budget_cost'] / $totalDays) * $to_date) : 0;
	$data['current_year']['total_utility_cost']  = 0;
	$data['current_year']['total_budget_cost']   = 0;
	$data['previous_year']['total_utility_cost'] = 0;
	if ($site_detail['show_utility_electricity']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_electricity_kwh_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['electricity_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_electricity_kwh_cost'], 2);
	}
	if ($site_detail['show_utility_fuel_oil']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_diesel_fuel_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['fuel_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_diesel_fuel_cost'], 2);
	}
	if ($site_detail['show_utility_lpg']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_lpg_consumption_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['lpg_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_lpg_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_water']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_water_consumption_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['water_total_consumption_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_water_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_irrigation_water']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_landscape_water_consumption_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_landscape_water_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_water_waste']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_waste_water_consumption_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_waste_water_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_natural_gas']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_natural_gas_consumption_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['natural_gas_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_natural_gas_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_district_cooling']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_district_cooling_consumption_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['district_cooling_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_district_cooling_consumption_cost'], 2);
	}
	if ($site_detail['show_utility_district_heating']) {
	    $data['current_year']['total_utility_cost'] += round($data['current_year']['total_district_heating_consumption_cost'], 2);
	    $data['current_year']['total_budget_cost'] += round($data['current_year']['district_heating_total_budget_cost'], 2);
	    $data['previous_year']['total_utility_cost'] += round($data['previous_year']['total_district_heating_consumption_cost'], 2);
	}
	$data['current_year']['total_utility_cost_per_roomnight']  = ($data['current_year']['total_room_night'] != 0 && $data['current_year']['total_room_night'] != '') ? round(($data['current_year']['total_utility_cost'] / $data['current_year']['total_room_night']), 2) : 0;
	$data['previous_year']['total_utility_cost_per_roomnight'] = ($data['previous_year']['total_room_night'] != 0 && $data['previous_year']['total_room_night'] != '') ? round(($data['previous_year']['total_utility_cost'] / $data['previous_year']['total_room_night']), 2) : 0;
	/*
	 * ************************************************************
	 * utilities daily chart generation data
	 * ************************************************************
	 */
	$year_month_daily_utility = array();
	$this->utilities_model->utilities_month   = $month;
	$this->utilities_model->utilities_year    = $year;
	$this->utilities_model->site_id           = $this->reports_model->site_id;
	$year_month_daily_utility['current_year'] = $this->utilities_model->getMonthUtility();
	$this->utilities_model->utilities_month    = $last_year_month;
	$this->utilities_model->utilities_year     = $last_year;
	$year_month_daily_utility['previous_year'] = $this->utilities_model->getMonthUtility();
	$last_year_utility                         = $this->utilities_model->getUtility();
	$selected_utility_key_array                = array();
	if ($utility_selected == 'electricity') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_electricity_kwh',
		'title'  => 'Electricity',
		'unit'   => $electricity_unit,
	    );
	} elseif ($utility_selected == 'fuel_oil') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_diesel_fuel',
		'title'  => 'Diesel Fuel',
		'unit'   => $fuel_oil_unit,
	    );
	} else
	if ($utility_selected == 'lpg') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_lpg_consumption',
		'title'  => 'L.P. Gas',
		'unit'   => $lpg_unit,
	    );
	} else
	if ($utility_selected == 'water') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_water_consumption',
		'title'  => 'Water',
		'unit'   => $water_unit,
	    );
	} else
	if ($utility_selected == 'water_waste') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_waste_water_consumption',
		'title'  => 'Waste Water',
		'unit'   => $water_unit,
	    );
	} else
	if ($utility_selected == 'water_irrigation') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_landscape_water_consumption',
		'title'  => 'Irrigation Water',
		'unit'   => $water_unit,
	    );
	} else
	if ($utility_selected == 'natural_gas') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_natural_gas_consumption',
		'title'  => 'Natural Gas',
		'unit'   => $natural_gas_unit,
	    );
	} else
	if ($utility_selected == 'cooling') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_district_cooling_consumption',
		'title'  => 'District Cooling',
		'unit'   => $district_cooling_unit,
	    );
	} else
	if ($utility_selected == 'heating') {
	    $selected_utility_key_array = array(
		'db_key' => 'total_district_heating_consumption',
		'title'  => 'District Heating',
		'unit'   => $district_heating_unit,
	    );
	}
	$utility_daily_data           = array();
	$utility_daily_data_occupancy = array();
	$utility_daily_data[]         = array(
	    'Date',
	    $montharray[$last_year_month] . " - " . $last_year,
	    $montharray[$month] . " - " . $year,
	    "CDD - " . $last_year,
	    "CDD - " . $year,
	    "HDD - " . $last_year,
	    "HDD - " . $year,
	);
	$utility_daily_data_occupancy[] = array(
	    'Date',
	    $montharray[$last_year_month] . " - " . $last_year,
	    $montharray[$month] . " - " . $year,
	    "Occupancy - " . $last_year,
	    "Occupancy - " . $year,
	);
	$month_length = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	for ($i = 1; $i <= $month_length; $i++) {
	    if (!empty($year_month_daily_utility['current_year'])) {
		foreach ($year_month_daily_utility['current_year'] as $current) {
		    if ($current['date_id'] == $i) {
			$current_year_utility   = $current[$selected_utility_key_array['db_key']];
			$current_year_cdd       = !empty($current['cdd']) ? intval($current['cdd']) : 0;
			$current_year_hdd       = !empty($current['hdd']) ? intval($current['hdd']) : 0;
			$current_year_occupancy = is_nan(round(($current['total_room_night'] / $site_detail['rooms_keys']) * 100, 2)) ? 0 : round(($current['total_room_night'] / $site_detail['rooms_keys']) * 100, 2);
			break;
		    } else {
			$current_year_utility   = 0;
			$current_year_cdd       = 0;
			$current_year_hdd       = 0;
			$current_year_occupancy = 0;
		    }
		}
	    } else {
		$current_year_utility   = 0;
		$current_year_cdd       = 0;
		$current_year_hdd       = 0;
		$current_year_occupancy = 0;
	    }
	    if (!empty($year_month_daily_utility['previous_year'])) {
		foreach ($year_month_daily_utility['previous_year'] as $previous) {
		    if ($previous['date_id'] == $i) {
			$previous_year_utility   = $previous[$selected_utility_key_array['db_key']];
			$previous_year_cdd       = !empty($previous['cdd']) ? intval($previous['cdd']) : 0;
			$previous_year_hdd       = !empty($previous['hdd']) ? intval($previous['hdd']) : 0;
			$previous_year_occupancy = is_infinite(round(($previous['total_room_night'] / $site_detail['rooms_keys']) * 100, 2)) ? 0 : round(($previous['total_room_night'] / $site_detail['rooms_keys']) * 100, 2);
			break;
		    } else {
			$previous_year_utility   = 0;
			$previous_year_cdd       = 0;
			$previous_year_hdd       = 0;
			$previous_year_occupancy = 0;
		    }
		}
	    } else {
		$previous_year_utility   = 0;
		$previous_year_cdd       = 0;
		$previous_year_hdd       = 0;
		$previous_year_occupancy = 0;
	    }
	    $single_node           = array();
	    $single_node_occupancy = array();
	    $single_node[]         = $i;
	    $single_node[] = round($previous_year_utility, 2);
	    $single_node[] = round($current_year_utility, 2);
	    $single_node[] = $previous_year_cdd;
	    $single_node[] = $current_year_cdd;
	    $single_node[] = $previous_year_hdd;
	    $single_node[] = $current_year_hdd;
	    $single_node_occupancy[] = $i;
	    $single_node_occupancy[] = round($previous_year_utility, 2);
	    $single_node_occupancy[] = round($current_year_utility, 2);
	    $single_node_occupancy[] = $previous_year_occupancy;
	    $single_node_occupancy[] = $current_year_occupancy;
	    $utility_daily_data[$i]           = $single_node;
	    $utility_daily_data_occupancy[$i] = $single_node_occupancy;
	}
	$data['utility_daily_data']           = $utility_daily_data;
	$data['utility_daily_data_occupancy'] = $utility_daily_data_occupancy;
	$data['selected_utility_key_array']   = $selected_utility_key_array;
	$data['month_length']                 = $month_length;
	$view_type = $this->input->post('view_type', '');
	if ($view_type == 'excel') {
	    require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	    $montharray          = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	    $fullmontharray      = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $optioncurrencyvalue = array('currency' => true);
	    $this->lang->load('sites/sites', 'english');
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("HEP")
		->setTitle("Excel Report")
		->setKeywords("Excel Report");
	    // Add logo
	    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	    } else {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	    }
	    $objDrawing = new PHPExcel_Worksheet_Drawing();
	    $objDrawing->setName('Logo');
	    $objDrawing->setDescription('Logo');
	    $objDrawing->setPath($site_logo);
	    $objDrawing->setCoordinates('A1');
	    $objDrawing->setHeight(100); // logo height
	    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	    // Prepare excel data
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $site_detail['site_location_name']);
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B4', $montharray[$month] . ' - ' . $year)
		->setCellValue('C4', $montharray[$month] . ' - ' . $last_year)
		->setCellValue('D4', 'Budget')
		->setCellValue('E4', 'Difference v/s Last Year')
		->setCellValue('G4', 'Difference v/s Budget');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A5', "MONTH TO DATE")
		->setCellValue('B5', "# Days - $to_date")
		->setCellValue('E5', 'Value')
		->setCellValue('F5', '%')
		->setCellValue('G5', 'Value')
		->setCellValue('H5', '%');
	    // Calculation
	    $last_year_deference  = 0;
	    $last_year_percantage = 0;
	    $last_year_deference  = $data['current_year']['total_room_night'] - $data['previous_year']['total_room_night'];
	    $last_year_percantage = ($data['previous_year']['total_room_night'] != 0) ? (($last_year_deference * 100) / $data['previous_year']['total_room_night']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A6', "Room Nights")
		->setCellValue('B6', number_format($data['current_year']['total_room_night']))
		->setCellValue('C6', number_format($data['previous_year']['total_room_night']))
		->setCellValue('D6', "")
		->setCellValue('E6', number_format($last_year_deference))
		->setCellValue('F6', number_format($last_year_percantage))
		->setCellValue('G6', "")
		->setCellValue('H6', "");
	    // Calculation
	    $last_year_cdd_deference  = 0;
	    $last_year_cdd_percantage = 0;
	    $last_year_cdd_deference  = $data['current_year']['cdd'] - $data['previous_year']['cdd'];
	    $last_year_cdd_percantage = ($data['previous_year']['cdd'] != 0) ? (($last_year_cdd_deference * 100) / $data['previous_year']['cdd']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A7', "CDD")
		->setCellValue('B7', round($data['current_year']['cdd'], 2))
		->setCellValue('C7', round($data['previous_year']['cdd'], 2))
		->setCellValue('D7', "")
		->setCellValue('E7', number_format($last_year_cdd_deference))
		->setCellValue('F7', number_format($last_year_cdd_percantage))
		->setCellValue('G7', "")
		->setCellValue('H7', "");
	    // Calculation
	    $last_year_hdd_deference  = 0;
	    $last_year_hdd_percantage = 0;
	    $last_year_hdd_deference  = $data['current_year']['hdd'] - $data['previous_year']['hdd'];
	    $last_year_hdd_percantage = ($data['previous_year']['hdd'] != 0) ? (($last_year_hdd_deference * 100) / $data['previous_year']['hdd']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A8', "HDD")
		->setCellValue('B8', round($data['current_year']['hdd'], 2))
		->setCellValue('C8', round($data['previous_year']['hdd'], 2))
		->setCellValue('D8', "")
		->setCellValue('E8', number_format($last_year_hdd_deference))
		->setCellValue('F8', number_format($last_year_hdd_percantage))
		->setCellValue('G8', "")
		->setCellValue('H8', "");
	    $alphas        = range('A', 'Z');
	    $active_row    = 9;
	    $active_column = 0;
	    $merge_cells  = array();
	    $legent_cells = array();
	    foreach ($data['utility_key_array'] as $utility) {
		if (in_array($utility['db_key'], array('landscape_water_consumption', 'waste_water_consumption'))) {
		    continue;
		}
		// For style
		$legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 7] . $active_row;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, $utility['title']);
		$active_column++;
		// Reset
		$active_row++;
		$active_column = 0;
		/************** First row (Consumpition)************* */
		$last_year_deference  = 0;
		$last_year_percantage = 0;
		$last_year_deference  = $data['current_year']['total_' . $utility['db_key']] - $data['previous_year']['total_' . $utility['db_key']];
		$last_year_percantage = (($last_year_deference * 100) / $data['current_year']['total_' . $utility['db_key']]);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, $utility['title'] . ' Consumption ' . '(' . $utility['unit'] . ')');
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['current_year']['total_' . $utility['db_key']]));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['previous_year']['total_' . $utility['db_key']]));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['current_year'][$utility['budget_key']]));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_deference));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
		$active_column++;
		if ($utility['budget_key'] == '') {
		    $current_row    = $active_row;
		    $current_column = $alphas[$active_column];
		    $last_row       = $active_row + 3;
		    $next_column    = $alphas[$active_column + 1];
		    $merge_cells[]  = $current_column . $current_row . ':' . $next_column . $last_row;
		} else {
		    $budget_deference  = 0;
		    $budget_percantage = 0;
		    $budget_deference  = $data['current_year']['total_' . $utility['db_key']] - $data['current_year'][$utility['budget_key']];
		    $budget_percantage = (($budget_deference * 100) / $data['current_year']['total_' . $utility['db_key']]);
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($budget_deference));
		    $active_column++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($budget_percantage));
		    $active_column++;
		}
		if ($utility['db_key'] == 'water_consumption') {
		    if ($site_detail['show_utility_irrigation_water']) {
			// Reset
			$active_row++;
			$active_column = 0;
			/* ************* Custom row (Consumpition)************* */
			$last_year_deference  = 0;
			$last_year_percantage = 0;
			$last_year_deference  = $data['current_year']['total_' . 'landscape_water_consumption'] - $data['previous_year']['total_' . 'landscape_water_consumption'];
			$last_year_percantage = (($last_year_deference * 100) / $data['current_year']['total_' . 'landscape_water_consumption']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, 'Irrigation Water Consumption ' . '(' . $utility['unit'] . ')');
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['current_year']['total_' . 'landscape_water_consumption']));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['previous_year']['total_' . 'landscape_water_consumption']));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format());
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_deference));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
			$active_column++;
			// Empty rows because no budget
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, '');
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, '');
			$active_column++;
		    }
		    if ($site_detail['show_utility_water_waste']) {
			// Reset
			$active_row++;
			$active_column = 0;
			/* ************ Custom row (Consumpition)************* */
			$last_year_deference  = 0;
			$last_year_percantage = 0;
			$last_year_deference  = $data['current_year']['total_' . 'waste_water_consumption'] - $data['previous_year']['total_' . 'waste_water_consumption'];
			$last_year_percantage = (($last_year_deference * 100) / $data['current_year']['total_' . 'waste_water_consumption']);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, 'Waste Water Consumption ' . '(' . $utility['unit'] . ')');
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['current_year']['total_' . 'waste_water_consumption']));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($data['previous_year']['total_' . 'waste_water_consumption']));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, '');
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_deference));
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
			$active_column++;
			// Empty rows because no budget
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, '');
			$active_column++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, '');
			$active_column++;
		    }
		}
		// Reset
		$active_row++;
		$active_column = 0;
		/* ************ Second row (Cost)************* */
		$last_year_deference  = 0;
		$last_year_percantage = 0;
		$last_year_deference  = $data['current_year']['total_' . $utility['db_key'] . '_cost'] - $data['previous_year']['total_' . $utility['db_key'] . '_cost'];
		$last_year_percantage = (($last_year_deference * 100) / $data['current_year']['total_' . $utility['db_key'] . '_cost']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, "Total {$utility['title']} Cost");
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($data['current_year']['total_' . $utility['db_key'] . '_cost'], $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($data['previous_year']['total_' . $utility['db_key'] . '_cost'], $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($data['current_year'][$utility['budget_key'] . '_cost'], $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($last_year_deference, $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
		$active_column++;
		if ($utility['budget_key'] != '') {
		    $budget_deference  = 0;
		    $budget_percantage = 0;
		    $budget_deference  = $data['current_year']['total_' . $utility['db_key'] . '_cost'] - $data['current_year'][$utility['budget_key'] . '_cost'];
		    $budget_percantage = (($budget_deference * 100) / $data['current_year']['total_' . $utility['db_key'] . '_cost']);
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($budget_deference, $optioncurrencyvalue));
		    $active_column++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($budget_percantage));
		    $active_column++;
		}
		// Reset
		$active_row++;
		$active_column = 0;
		/* ************ Third row (Consumption / roonnight)************* */
		$current_per_room_night  = 0;
		$previous_per_room_night = 0;
		$last_year_deference     = 0;
		$last_year_percantage    = 0;
		$current_per_room_night  = ($data['current_year']['total_room_night'] != '' && $data['current_year']['total_room_night'] != 0) ? $data['current_year']['total_' . $utility['db_key']] / $data['current_year']['total_room_night'] : 0;
		$previous_per_room_night = ($data['previous_year']['total_room_night'] != '' && $data['previous_year']['total_room_night'] != 0) ? $data['previous_year']['total_' . $utility['db_key']] / $data['previous_year']['total_room_night'] : 0;
		$last_year_deference  = $current_per_room_night - $previous_per_room_night;
		$last_year_percantage = ($current_per_room_night != 0) ? (($last_year_deference * 100) / $current_per_room_night) : 0;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, "{$utility['title']}/room night");
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(($current_per_room_night)));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(($previous_per_room_night)));
		$active_column++;
		$current_row   = $active_row;
		$next_row      = $active_row + 1;
		$merge_cells[] = $alphas[$active_column] . $current_row . ':' . $alphas[$active_column] . $next_row;
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_deference));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
		$active_column++;
		if ($utility['budget_key'] != '') {
		    $current_row    = $active_row;
		    $next_row       = $active_row + 1;
		    $current_column = $active_column;
		    $next_column    = $active_column + 1;
		    $merge_cells[] = $alphas[$current_column] . $current_row . ':' . $alphas[$next_column] . $next_row;
		}
		// Reset
		$active_row++;
		$active_column = 0;
		/** ************ Fourth row (cost / roonnight)************* */
		$current_per_room_night  = 0;
		$previous_per_room_night = 0;
		$last_year_deference     = 0;
		$last_year_percantage    = 0;
		$current_per_room_night  = ($data['current_year']['total_room_night'] != 0 && $data['current_year']['total_room_night'] != '') ?  $data['current_year']['total_' . $utility['db_key'] . '_cost'] / $data['current_year']['total_room_night'] : 0;
		$previous_per_room_night = ($data['previous_year']['total_room_night'] != 0 && $data['previous_year']['total_room_night'] != '') ? $data['previous_year']['total_' . $utility['db_key'] . '_cost'] / $data['previous_year']['total_room_night'] : '';
		$last_year_deference  = $current_per_room_night - $previous_per_room_night;
		$last_year_percantage = ($current_per_room_night != 0) ? (($last_year_deference * 100) / $current_per_room_night) : 0;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, "{$utility['title']} cost/room night");
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($current_per_room_night, $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($previous_per_room_night, $optioncurrencyvalue));
		$active_column++;
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, report_value_format($last_year_deference, $optioncurrencyvalue));
		$active_column++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($last_year_percantage));
		$active_column++;
		// Reset
		$active_row++;
		$active_column = 0;
	    }
	    $last_year_deference  = 0;
	    $last_year_percantage = 0;
	    $last_year_deference  = $data['current_year']['total_utility_cost'] - $data['previous_year']['total_utility_cost'];
	    $last_year_percantage = ($data['current_year']['total_utility_cost'] != '' && $data['current_year']['total_utility_cost'] != '') ? (($last_year_deference * 100) / $data['current_year']['total_utility_cost']) : 0;
	    $budget_deference  = 0;
	    $budget_percantage = 0;
	    $budget_deference  = $data['current_year']['total_utility_cost'] - $data['current_year']['total_budget_cost'];
	    $budget_percantage = ($data['current_year']['total_utility_cost'] != 0 && $data['current_year']['total_utility_cost'] != '') ? (($budget_deference * 100) / $data['current_year']['total_utility_cost']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $active_row, 'TOTAL')
		->setCellValue('B' . $active_row, report_value_format($data['current_year']['total_utility_cost'], $optioncurrencyvalue))
		->setCellValue('C' . $active_row, report_value_format($data['previous_year']['total_utility_cost'], $optioncurrencyvalue))
		->setCellValue('D' . $active_row, report_value_format($data['current_year']['total_budget_cost'], $optioncurrencyvalue))
		->setCellValue('E' . $active_row, report_value_format($last_year_deference, $optioncurrencyvalue))
		->setCellValue('F' . $active_row, number_format($last_year_percantage))
		->setCellValue('G' . $active_row, report_value_format($budget_deference, $optioncurrencyvalue))
		->setCellValue('H' . $active_row, number_format($budget_percantage));
	    // For style
	    $legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 7] . $active_row;
	    $active_row++;
	    $last_year_deference  = 0;
	    $last_year_percantage = 0;
	    $last_year_deference  = ($data['current_year']['total_utility_cost_per_roomnight'] != '' && $data['previous_year']['total_utility_cost_per_roomnight'] != '') ? $data['current_year']['total_utility_cost_per_roomnight'] - $data['previous_year']['total_utility_cost_per_roomnight'] : 0;
	    $last_year_percantage = ($data['current_year']['total_utility_cost_per_roomnight'] != '' && $data['current_year']['total_utility_cost_per_roomnight'] != 0) ? (($last_year_deference * 100) / $data['current_year']['total_utility_cost_per_roomnight']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $active_row, 'Total Cost Per room night')
		->setCellValue('B' . $active_row, report_value_format($data['current_year']['total_utility_cost_per_roomnight'], $optioncurrencyvalue))
		->setCellValue('C' . $active_row, report_value_format($data['previous_year']['total_utility_cost_per_roomnight'], $optioncurrencyvalue))
		->setCellValue('D' . $active_row, '')
		->setCellValue('E' . $active_row, report_value_format($last_year_deference, $optioncurrencyvalue))
		->setCellValue('F' . $active_row, number_format($last_year_percantage))
		->setCellValue('G' . $active_row, '')
		->setCellValue('H' . $active_row, '');
	    // For style
	    $legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 7] . $active_row;
	    // Excel cell formation
	    // Merge
	    foreach ($merge_cells as $cell) {
		$objPHPExcel->getActiveSheet()->mergeCells($cell);
	    }
	    $objPHPExcel->getActiveSheet()->mergeCells('B5:D5');
	    $objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
	    $objPHPExcel->getActiveSheet()->mergeCells('E4:F4');
	    $objPHPExcel->getActiveSheet()->mergeCells('G4:H4');
	    $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
	    // Style
	    $style           = array('font' => array('bold' => true));
	    $second_last_row = $active_row - 1;
	    $objPHPExcel->getActiveSheet()->getStyle("A{$second_last_row}:H{$second_last_row}")->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle("A{$active_row}:H{$active_row}")->applyFromArray($style);
	    $style = array('font' => array('size' => 20, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $style = array('font' => array('size' => 16, 'bold' => true, 'color' => array('rgb' => 'ffffff')));
	    $objPHPExcel->getActiveSheet()->getStyle('B4:H4')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:H4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    $style = array('font' => array('size' => 14, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B6:H48')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $legent_utility_style = array('font' => array('color' => array('rgb' => 'ffffff')));
	    foreach ($legent_cells as $cell) {
		$objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($legent_utility_style);
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    }
	    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	    // Borders
	    $objPHPExcel->getActiveSheet()->getStyle($alphas[$active_column] . $active_row . ":" . $alphas[$active_column + 7] . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'bottom' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A4:A" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("D4:D" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("F4:F" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("H4:H" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A5:H5")->applyFromArray(
		array(
		    'borders' => array(
			'top'    => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
			'bottom' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $file_name = 'Month_To_Date_Report_' . $fullmontharray[$month] . '_' . $year . '.xls';
	    ob_end_clean();
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="' . $file_name . '"');
	    header('Cache-Control: max-age=0');
	    // If you're serving to IE 9, then the following may be needed
	    header('Cache-Control: max-age=1');
	    // If you're serving to IE over SSL, then the following may be needed
	    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	    header('Pragma: public'); // HTTP/1.0
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	if ($view_type == 'chart') {
	    $template = 'admin_management_chart';
	} else {
	    $template = 'admin_management';
	}
	$this->theme->view($data, $template);
    }

    public function carbon()
    {
	$startdate = $this->input->post('startdate', '');
	$enddate = $this->input->post('enddate', '');
	$formtype = $this->input->post('formtype', 'usage_by_room_nights');
	$base_type = $this->input->post('base_type', 'cost');
	$utility_type = $this->input->post('utility_type', 'electricity');
	$utilitychanger = $this->input->post('utilitychanger', '');
	$utility_type_select = $this->input->post('utility_type_select', 'electricity');
	$time_type = 'advance_select_avg_ytd';
	$days_of_month = date('t');
	$filters = array();
	$filters['formtype'] = $formtype;
	$filters['utility_type'] = $utility_type;
	// YTD reports
	$YTDdefaultreports = array(
	    /* 'electricity_kwh_compare_last_year',
	      'electricity_kwh_budget_forecast',
	      'electricity_cost_budget_forecast',
	      'lpg_m3_budget_forecast',
	      'lpg_cost_budget_forecast',
	      'natural_gas_m3_budget_forecast',
	      'natural_gas_cost_budget_forecast',
	      'oil_liters_budget_forecast',
	      'oil_cost_budget_forecast', */
	    'water_liters_utility_cisterns_ro',
	);
	// FIlters for comparisional bar chart
	$filters_comparision_chart = array();
	$startdate = '1/' . date('Y');
	$enddate = '12/' . date('Y');
	$startdateexplode = explode('/', $startdate);
	$enddateexplode = explode('/', $enddate);
	$filters['startdate'] = (isset($startdate)) ? $startdate : '';
	$filters['enddate'] = (isset($enddate)) ? $enddate : '';
	$filters['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
	$filters['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';
	$filters['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';
	$filters['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';
	$filters['CURRENT_YEAR_MAX_MONTH_ID'] = CURRENT_YEAR_MAX_MONTH_ID;
	$results = $this->reports_model->utilityUnitBarChart($filters);
	//carbon foot print
	$this->load->model('sites/sites_model');
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->sites_model->year = $filters['start_year'];
	$site_details = $this->sites_model->get_site_detail_custom($site_id);
	// Note : water_utility,water irrigation,water_cisterns fields are for only one report so no need to calculate by other parameters and no need in cost based report
	$reportData = array();
	if (!empty($results)) {
	    foreach ($results as $key => $result) {
		$totalElectricity += $result['electricity'];
		$totalFuel += $result['fuel'];
		$totalLpg += $result['lpg'];
		$totalNaturalGas += $result['natural_gas'];
		$totalWater += $result['water'];
		$totalHeatingDistrict += $result['heating_district'];
		$totalCoolingDistrict += $result['cooling_district'];
		$calculated_result['electricity'] = (!empty($result['electricity'])) ? ($result['electricity'] * $site_details['electricity_emission_factor']) : 0;
		$calculated_result['fuel'] = (!empty($result['fuel'])) ? ($result['fuel'] * $site_details['fuel_emission_factor']) : 0;
		$calculated_result['lpg'] = (!empty($result['lpg'])) ? ($result['lpg'] * $site_details['lpg_emission_factor']) : 0;
		$calculated_result['natural_gas'] = (!empty($result['natural_gas'])) ? ($result['natural_gas'] * $site_details['natural_gas_emission_factor']) : 0;
		$calculated_result['heating_district'] = (!empty($result['heating_district'])) ? ($result['heating_district'] * $site_details['district_heating_emission_factor']) : 0;
		$calculated_result['cooling_district'] = (!empty($result['cooling_district'])) ? ($result['cooling_district'] * $site_details['district_cooling_emission_factor']) : 0;
		$calculated_result['water'] = (!empty($result['water'])) ? ($result['water']) : 0;
		$calculated_result['cdd'] = (!empty($result['cdd'])) ? ($result['cdd']) : 0;
		$calculated_result['hdd'] = (!empty($result['hdd'])) ? ($result['hdd']) : 0;
		$calculated_result['budget'] = (!empty($result['total_budget'])) ? ($result['total_budget']) : 0;
		$calculated_result['total_electricity_kwh'] = (!empty($result['total_electricity_kwh'])) ? ($result['total_electricity_kwh']) : 0;
		if (!empty($result['total_electricity_kwh'])) {
		    $electricity_tariff_cost_per_kwh = $result['electricity'] * $result['total_electricity_kwh'];
		} else {
		    $electricity_tariff_cost_per_kwh = 0;
		}
		$calculated_result['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? ($electricity_tariff_cost_per_kwh * $site_details['electricity_emission_factor']) : 0;
		//$calculated_result = $result;
		//$calculated_result['cdd'] = $result['cdd'];
		//$calculated_result['hdd'] = $result['hdd'];
		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
		// Based on give formula
		$calculated_result['occupancy'] = (($result['total_room_night'] / ($result['rooms_keys'] * $days_of_month)) * 100);
		$reportData[$result['month_id']][$result['year_id']] = $calculated_result;
	    }
	}
	$is_occupancy = true;
	$x_axis_title = 'report-axis-cost';
	$report_title = '';
	$report_tmpl = ''; // Default template file
	//Add total values in data array
	$data['totalElectricity'] = $totalElectricity;
	$data['totalFuel'] = $totalFuel;
	$data['totalLpg'] = $totalLpg;
	$data['totalNaturalGas'] = $totalNaturalGas;
	$data['totalWater'] = $totalWater;
	$data['totalHeatingDistrict'] = $totalHeatingDistrict;
	$data['totalCoolingDistrict'] = $totalCoolingDistrict;
	$data['time_type'] = $time_type;
	$data['report_title'] = $report_title;
	$data['x_axis_title'] = $x_axis_title;
	$data['is_occupancy'] = $is_occupancy;
	$data['utility_type_select'] = ($utility_type_select == '') ? 'electricity' : $utility_type_select;
	$data['utility_type'] = ($utility_type == '') ? 'electricity' : $utility_type;
	$data['formtype'] = ($formtype == '') ? 'usage_by_room_nights' : $formtype;
	$data['base_type'] = ($base_type == '') ? 'cost' : $base_type;
	$data['utilitychanger'] = $utilitychanger;
	$data['utility_cost_chart'] = $reportData;
	$data['filters'] = $filters;
	$this->breadcrumb->add(lang('advance_reports'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/advance');
	$this->theme->set('page_title', lang('advance_reports'));
	$view_type = $this->input->post('view_type', '');
	if ($view_type == 'excel') {
	    require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	    $this->lang->load('sites/sites', 'english');
	    $user_id = $this->session->userdata[$this->section_name]['user_id'];
	    $site_id = $this->session->userdata[$this->section_name]['site_id'];
	    $role_id = $this->session->userdata[$this->section_name]['role_id'];
	    // $base_type : cost / unit
	    // $formtype; : Type of chart
	    // $utility_type : Energy Type
	    // $report_tmpl : Report template for how to show data
	    $postdata = $this->input->post();
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("HEP")
		->setTitle("Excel Report")
		->setKeywords("Excel Report");
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $fullAlphaArray = array_merge(range('A', 'Z'));
	    // Add report info in cell
	    // Set header bold
	    $alphaInc = 0;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", 'Month');
	    $alphaInc++;
	    if ($totalElectricity > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_electricity_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_electricity_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalFuel > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_fuel_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_fuel_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalLpg > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_lpg_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_lpg_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalNaturalGas > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_natural_gas_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_natural_gas_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    /* if($totalWater > 0){
	      $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_water_kgco2_year"),$filters["end_year"]));
	      $alphaInc++;
	      $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_water_kgco2_year"),$filters["end_year"]-1));
	      $alphaInc++;
	      } */
	    if ($totalHeatingDistrict > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_heating_district_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_heating_district_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalCoolingDistrict > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_cooling_district_kgco2_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_cooling_district_kgco2_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_occupancy_current"), $filters["end_year"], '%'));
	    $alphaInc++;
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_occupancy_previous"), $filters["end_year"] - 1, '%'));
	    $alphaInc++;
	    $dateIteratorArray = array();
	    if ($filters["start_year"] == $filters["end_year"]) {
		// If start and end year is same
		$startmonthsarray = array();
		for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
		    $startmonthsarray[] = $i;
		}
		$dateIteratorArray[$filters["start_year"]] = $startmonthsarray;
	    } else {
		// If start and end year is not same
		$startmonthsarray = array();
		$endmonthsarray = array();
		for ($i = $filters['start_month']; $i <= 12; $i++) {
		    $startmonthsarray[] = $i;
		}
		for ($i = 1; $i <= $filters['end_month']; $i++) {
		    $endmonthsarray[] = $i;
		}
		$dateIteratorArray[$filters["start_year"]] = $startmonthsarray;
		$dateIteratorArray[$filters["end_year"]] = $endmonthsarray;
	    }
	    $j = 1;
	    foreach ($dateIteratorArray as $year => $value) {
		foreach ($value as $key1 => $month) {
		    $j++;
		    $current_electricity_data = (!empty($data['utility_cost_chart'][$month][$year]['electricity'])) ? $data['utility_cost_chart'][$month][$year]['electricity'] : 0;
		    $previous_electricity_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['electricity'])) ? $data['utility_cost_chart'][$month][$year - 1]['electricity'] : 0;
		    $current_fuel_data = (!empty($data['utility_cost_chart'][$month][$year]['fuel'])) ? $data['utility_cost_chart'][$month][$year]['fuel'] : 0;
		    $previous_fuel_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['fuel'])) ? $data['utility_cost_chart'][$month][$year - 1]['fuel'] : 0;
		    $current_lpg_data = (!empty($data['utility_cost_chart'][$month][$year]['lpg'])) ? $data['utility_cost_chart'][$month][$year]['lpg'] : 0;
		    $previous_lpg_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['lpg'])) ? $data['utility_cost_chart'][$month][$year - 1]['lpg'] : 0;
		    $current_natural_gas_data = (!empty($data['utility_cost_chart'][$month][$year]['natural_gas'])) ? $data['utility_cost_chart'][$month][$year]['natural_gas'] : 0;
		    $previous_natural_gas_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['natural_gas'])) ? $data['utility_cost_chart'][$month][$year - 1]['natural_gas'] : 0;
		    /* $current_water_data = (!empty($data['utility_cost_chart'][$month][$year]['water'])) ? $data['utility_cost_chart'][$month][$year]['water'] : 0;
		      $previous_water_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['water'])) ? $data['utility_cost_chart'][$month][$year - 1]['water'] : 0; */
		    $current_heating_district_data = (!empty($data['utility_cost_chart'][$month][$year]['heating_district'])) ? $data['utility_cost_chart'][$month][$year]['heating_district'] : 0;
		    $previous_heating_district_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['heating_district'])) ? $data['utility_cost_chart'][$month][$year - 1]['heating_district'] : 0;
		    $current_cooling_district_data = (!empty($data['utility_cost_chart'][$month][$year]['cooling_district'])) ? $data['utility_cost_chart'][$month][$year]['cooling_district'] : 0;
		    $previous_cooling_district_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['cooling_district'])) ? $data['utility_cost_chart'][$month][$year - 1]['cooling_district'] : 0;
		    $occupancydata = (!empty($data['utility_cost_chart'][$month][$year]['occupancy'])) ? $data['utility_cost_chart'][$month][$year]['occupancy'] : 0;
		    $previousoccupancydata = (!empty($data['utility_cost_chart'][$month][$year - 1]['occupancy'])) ? $data['utility_cost_chart'][$month][$year - 1]['occupancy'] : 0;
		    $alphaInc = 0;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", $fullmontharray[$month]);
		    $alphaInc++;
		    if ($totalElectricity > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_electricity_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_electricity_data, 2));
			$alphaInc++;
		    }
		    if ($totalFuel > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_fuel_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_fuel_data, 2));
			$alphaInc++;
		    }
		    if ($totalLpg > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_lpg_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_lpg_data, 2));
			$alphaInc++;
		    }
		    if ($totalNaturalGas > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_natural_gas_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_natural_gas_data, 2));
			$alphaInc++;
		    }
		    /* if($totalWater > 0){
		      $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_water_data,2));
		      $alphaInc++;
		      $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_water_data,2));
		      $alphaInc++;
		      } */
		    if ($totalHeatingDistrict > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_heating_district_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_heating_district_data, 2));
			$alphaInc++;
		    }
		    if ($totalCoolingDistrict > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_cooling_district_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_cooling_district_data, 2));
			$alphaInc++;
		    }
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($occupancydata, 2));
		    $alphaInc++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previousoccupancydata, 2));
		    $alphaInc++;
		}
	    }
	    ob_end_clean();
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="Excel Report.xls"');
	    header('Cache-Control: max-age=0');
	    // If you're serving to IE 9, then the following may be needed
	    header('Cache-Control: max-age=1');
	    // If you're serving to IE over SSL, then the following may be needed
	    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	    header('Pragma: public'); // HTTP/1.0
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	$this->theme->view($data, $report_tmpl);
    }

    public function budget()
    {
	$this->index('budget');
    }

    public function advance()
    {
	$startdate = $this->input->post('startdate', '');
	$enddate = $this->input->post('enddate', '');
	$formtype = $this->input->post('formtype', 'usage_by_room_nights');
	$base_type = $this->input->post('base_type', 'cost');
	$utility_type = $this->input->post('utility_type', 'electricity');
	$utilitychanger = $this->input->post('utilitychanger', '');
	$utility_type_select = $this->input->post('utility_type_select', 'electricity');
	$time_type = $this->input->post('time_type', 'advance_select_choose_date');
	$days_of_month = date('t');
	$filters = array();
	$filters['formtype'] = $formtype;
	$filters['utility_type'] = $utility_type;
	$filters['selected_year'] = $this->input->post('year');
	// YTD reports
	$YTDdefaultreports = array(
	    'electricity_kwh_compare_last_year',
	    'electricity_kwh_budget_forecast',
	    'electricity_cost_budget_forecast',
	    'lpg_m3_budget_forecast',
	    'lpg_cost_budget_forecast',
	    'district_heating_kwh_budget_forecast',
	    'district_heating_cost_budget_forecast',
	    'district_cooling_kwh_budget_forecast',
	    'district_cooling_cost_budget_forecast',
	    'natural_gas_m3_budget_forecast',
	    'natural_gas_cost_budget_forecast',
	    'oil_liters_budget_forecast',
	    'oil_cost_budget_forecast',
	    'water_liters_utility_cisterns_ro',
	);
	$filters['CURRENT_YEAR_MAX_MONTH_ID'] = CURRENT_YEAR_MAX_MONTH_ID;
	if (in_array($utilitychanger, $YTDdefaultreports) || $time_type == 'advance_select_avg_ytd') {
	    $startdate = '1/' . date('Y');
	    $enddate = '12/' . date('Y');
	}
	//added by pcs148 for selected year vs its previous year
	if ($time_type == 'advance_select_choose_year') {
	    $startdate = '1/' . ($this->input->post('year'));
	    $enddate = '12/' . $this->input->post('year');
	    $filters['CURRENT_YEAR_MAX_MONTH_ID'] = 12;
	}
	// Last 12 months report
	$last12monthsdefaultreports = array();
	if (in_array($utilitychanger, $last12monthsdefaultreports)) {
	    $startdate = date("m/Y", strtotime(date('Y-' . CURRENT_YEAR_MAX_MONTH_ID) . " -11 months"));
	    $enddate = date(CURRENT_YEAR_MAX_MONTH_ID . '/Y');
	}
	$startdateexplode = explode('/', $startdate);
	$enddateexplode = explode('/', $enddate);
	$filters['startdate'] = (isset($startdate)) ? $startdate : '';
	$filters['enddate'] = (isset($enddate)) ? $enddate : '';
	$filters['start_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
	$filters['start_year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';
	$filters['end_month'] = (isset($enddateexplode[0])) ? (int) $enddateexplode[0] : '';
	$filters['end_year'] = (isset($enddateexplode[1])) ? $enddateexplode[1] : '';
	if ($base_type == 'unit') {
	    $results = $this->reports_model->monthlyUtilityBasedReportByUnit($filters);
	} else {
	    $results = $this->reports_model->monthlyUtilityBasedReportByCost($filters);
	    if (!empty($results)) {
		foreach ($results as $key => $value) {
		    $results[$key]['district_cooling'] = $value['district_cooling'] + $value['district_cooling_fixed_cost'];
		    $results[$key]['district_heating'] = $value['district_heating'] + $value['district_heating_fixed_cost'];
		    $results[$key]['lpg'] = $value['lpg'] + $value['lpg_fixed_cost'];
		    $results[$key]['natural_gas'] = $value['natural_gas'] + $value['natural_gas_fixed_cost'];
		    $results[$key]['water'] = $value['water'] + $value['water_fixed_cost'];
		}
	    }
	}
	// Note : water_utility,water irrigation,water_cisterns fields are for only one report so no need to calculate by other parameters and no need in cost based report
	$reportData = array();
	if (!empty($results)) {
	    foreach ($results as $key => $result) {
		switch ($formtype) {
		    case 'usage_by_utility':
			// No calculation Require
			$calculated_result = $result;
			break;
		    case 'usage_by_guests':
			if (!empty($result['total_guests']) && $result['total_guests'] > 0) {
			    $calculated_result['electricity'] = ($result['electricity'] / $result['total_guests']);
			    $calculated_result['fuel'] = ($result['fuel'] / $result['total_guests']);
			    $calculated_result['lpg'] = ($result['lpg'] / $result['total_guests']);
			    $calculated_result['natural_gas'] = ($result['natural_gas'] / $result['total_guests']);
			    $calculated_result['heating_district'] = ($result['heating_district'] / $result['total_guests']);
			    $calculated_result['cooling_district'] = ($result['cooling_district'] / $result['total_guests']);
			    $calculated_result['water'] = ($result['water'] / $result['total_guests']);
			    $calculated_result['electricity_budget'] = ($result['electricity_budget'] / $result['total_guests']);
			    $calculated_result['fuel_budget'] = ($result['fuel_budget'] / $result['total_guests']);
			    $calculated_result['lpg_budget'] = ($result['lpg_budget'] / $result['total_guests']);
			    $calculated_result['natural_gas_budget'] = ($result['natural_gas_budget'] / $result['total_guests']);
			    $calculated_result['heating_district_budget'] = ($result['heating_district_budget'] / $result['total_guests']);
			    $calculated_result['cooling_district_budget'] = ($result['cooling_district_budget'] / $result['total_guests']);
			    $calculated_result['water_budget'] = ($result['water_budget'] / $result['total_guests']);
			} else {
			    $calculated_result['electricity'] = 0;
			    $calculated_result['fuel'] = 0;
			    $calculated_result['lpg'] = 0;
			    $calculated_result['natural_gas'] = 0;
			    $calculated_result['heating_district'] = 0;
			    $calculated_result['cooling_district'] = 0;
			    $calculated_result['water'] = 0;
			    $calculated_result['electricity_budget'] = 0;
			    $calculated_result['fuel_budget'] = 0;
			    $calculated_result['lpg_budget'] = 0;
			    $calculated_result['natural_gas_budget'] = 0;
			    $calculated_result['heating_district_budget'] = 0;
			    $calculated_result['cooling_district_budget'] = 0;
			    $calculated_result['water_budget'] = 0;
			}
			break;
		    case 'usage_by_room_nights':
			if (!empty($result['total_room_night']) && $result['total_room_night'] > 0) {
			    $calculated_result['electricity'] = ($result['electricity'] / $result['total_room_night']);
			    $calculated_result['fuel'] = ($result['fuel'] / $result['total_room_night']);
			    $calculated_result['lpg'] = ($result['lpg'] / $result['total_room_night']);
			    $calculated_result['natural_gas'] = ($result['natural_gas'] / $result['total_room_night']);
			    $calculated_result['heating_district'] = ($result['heating_district'] / $result['total_room_night']);
			    $calculated_result['cooling_district'] = ($result['cooling_district'] / $result['total_room_night']);
			    $calculated_result['water'] = ($result['water'] / $result['total_room_night']);
			    $calculated_result['electricity_budget'] = ($result['electricity_budget'] / $result['total_room_night']);
			    $calculated_result['fuel_budget'] = ($result['fuel_budget'] / $result['total_room_night']);
			    $calculated_result['lpg_budget'] = ($result['lpg_budget'] / $result['total_room_night']);
			    $calculated_result['natural_gas_budget'] = ($result['natural_gas_budget'] / $result['total_room_night']);
			    $calculated_result['heating_district_budget'] = ($result['heating_district_budget'] / $result['total_room_night']);
			    $calculated_result['cooling_district_budget'] = ($result['cooling_district_budget'] / $result['total_room_night']);
			    $calculated_result['water_budget'] = ($result['water_budget'] / $result['total_room_night']);
			} else {
			    $calculated_result['electricity'] = 0;
			    $calculated_result['fuel'] = 0;
			    $calculated_result['lpg'] = 0;
			    $calculated_result['natural_gas'] = 0;
			    $calculated_result['heating_district'] = 0;
			    $calculated_result['cooling_district'] = 0;
			    $calculated_result['water'] = 0;
			    $calculated_result['electricity_budget'] = 0;
			    $calculated_result['fuel_budget'] = 0;
			    $calculated_result['lpg_budget'] = 0;
			    $calculated_result['natural_gas_budget'] = 0;
			    $calculated_result['heating_district_budget'] = 0;
			    $calculated_result['cooling_district_budget'] = 0;
			    $calculated_result['water_budget'] = 0;
			}
			break;
		    case 'usage_by_built_area':
			if (!empty($result['site_builtup_area']) && $result['site_builtup_area'] > 0) {
			    $calculated_result['electricity'] = ($result['electricity'] / $result['site_builtup_area']);
			    $calculated_result['fuel'] = ($result['fuel'] / $result['site_builtup_area']);
			    $calculated_result['lpg'] = ($result['lpg'] / $result['site_builtup_area']);
			    $calculated_result['natural_gas'] = ($result['natural_gas'] / $result['site_builtup_area']);
			    $calculated_result['heating_district'] = ($result['heating_district'] / $result['site_builtup_area']);
			    $calculated_result['cooling_district'] = ($result['cooling_district'] / $result['site_builtup_area']);
			    $calculated_result['water'] = ($result['water'] / $result['site_builtup_area']);
			    $calculated_result['electricity_budget'] = ($result['electricity_budget'] / $result['site_builtup_area']);
			    $calculated_result['fuel_budget'] = ($result['fuel_budget'] / $result['site_builtup_area']);
			    $calculated_result['lpg_budget'] = ($result['lpg_budget'] / $result['site_builtup_area']);
			    $calculated_result['natural_gas_budget'] = ($result['natural_gas_budget'] / $result['site_builtup_area']);
			    $calculated_result['heating_district_budget'] = ($result['heating_district_budget'] / $result['site_builtup_area']);
			    $calculated_result['cooling_district_budget'] = ($result['cooling_district_budget'] / $result['site_builtup_area']);
			    $calculated_result['water_budget'] = ($result['water_budget'] / $result['site_builtup_area']);
			} else {
			    $calculated_result['electricity'] = 0;
			    $calculated_result['fuel'] = 0;
			    $calculated_result['lpg'] = 0;
			    $calculated_result['natural_gas'] = 0;
			    $calculated_result['heating_district'] = 0;
			    $calculated_result['cooling_district'] = 0;
			    $calculated_result['water'] = 0;
			    $calculated_result['electricity_budget'] = 0;
			    $calculated_result['fuel_budget'] = 0;
			    $calculated_result['lpg_budget'] = 0;
			    $calculated_result['natural_gas_budget'] = 0;
			    $calculated_result['heating_district_budget'] = 0;
			    $calculated_result['cooling_district_budget'] = 0;
			    $calculated_result['water_budget'] = 0;
			}
			break;
		    case 'usage_by_conditional_area':
			if (!empty($result['cooled_builtup_area']) && $result['cooled_builtup_area'] > 0) {
			    $calculated_result['electricity'] = ($result['electricity'] / $result['cooled_builtup_area']);
			    $calculated_result['fuel'] = ($result['fuel'] / $result['cooled_builtup_area']);
			    $calculated_result['lpg'] = ($result['lpg'] / $result['cooled_builtup_area']);
			    $calculated_result['natural_gas'] = ($result['natural_gas'] / $result['cooled_builtup_area']);
			    $calculated_result['heating_district'] = ($result['heating_district'] / $result['cooled_builtup_area']);
			    $calculated_result['cooling_district'] = ($result['cooling_district'] / $result['cooled_builtup_area']);
			    $calculated_result['water'] = ($result['water'] / $result['cooled_builtup_area']);
			    $calculated_result['electricity_budget'] = ($result['electricity_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['fuel_budget'] = ($result['fuel_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['lpg_budget'] = ($result['lpg_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['natural_gas_budget'] = ($result['natural_gas_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['heating_district_budget'] = ($result['heating_district_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['cooling_district_budget'] = ($result['cooling_district_budget'] / $result['cooled_builtup_area']);
			    $calculated_result['water_budget'] = ($result['water_budget'] / $result['cooled_builtup_area']);
			} else {
			    $calculated_result['electricity'] = 0;
			    $calculated_result['fuel'] = 0;
			    $calculated_result['lpg'] = 0;
			    $calculated_result['natural_gas'] = 0;
			    $calculated_result['heating_district'] = 0;
			    $calculated_result['cooling_district'] = 0;
			    $calculated_result['water'] = 0;
			    $calculated_result['electricity_budget'] = 0;
			    $calculated_result['fuel_budget'] = 0;
			    $calculated_result['lpg_budget'] = 0;
			    $calculated_result['natural_gas_budget'] = 0;
			    $calculated_result['heating_district_budget'] = 0;
			    $calculated_result['cooling_district_budget'] = 0;
			    $calculated_result['water_budget'] = 0;
			}
			break;
		    case 'usage_by_laundered':
			if (!empty($result['total_laundered']) && $result['total_laundered'] > 0) {
			    $calculated_result['electricity'] = ($result['electricity'] / $result['total_laundered']);
			    $calculated_result['fuel'] = ($result['fuel'] / $result['total_laundered']);
			    $calculated_result['lpg'] = ($result['lpg'] / $result['total_laundered']);
			    $calculated_result['natural_gas'] = ($result['natural_gas'] / $result['total_laundered']);
			    $calculated_result['heating_district'] = ($result['heating_district'] / $result['total_laundered']);
			    $calculated_result['cooling_district'] = ($result['cooling_district'] / $result['total_laundered']);
			    $calculated_result['water'] = ($result['water'] / $result['total_laundered']);
			    $calculated_result['electricity_budget'] = ($result['electricity_budget'] / $result['total_laundered']);
			    $calculated_result['fuel_budget'] = ($result['fuel_budget'] / $result['total_laundered']);
			    $calculated_result['lpg_budget'] = ($result['lpg_budget'] / $result['total_laundered']);
			    $calculated_result['natural_gas_budget'] = ($result['natural_gas_budget'] / $result['total_laundered']);
			    $calculated_result['heating_district_budget'] = ($result['heating_district_budget'] / $result['total_laundered']);
			    $calculated_result['cooling_district_budget'] = ($result['cooling_district_budget'] / $result['total_laundered']);
			    $calculated_result['water_budget'] = ($result['water_budget'] / $result['total_laundered']);
			} else {
			    $calculated_result['electricity'] = 0;
			    $calculated_result['fuel'] = 0;
			    $calculated_result['lpg'] = 0;
			    $calculated_result['natural_gas'] = 0;
			    $calculated_result['heating_district'] = 0;
			    $calculated_result['cooling_district'] = 0;
			    $calculated_result['water'] = 0;
			    $calculated_result['electricity_budget'] = 0;
			    $calculated_result['fuel_budget'] = 0;
			    $calculated_result['lpg_budget'] = 0;
			    $calculated_result['natural_gas_budget'] = 0;
			    $calculated_result['heating_district_budget'] = 0;
			    $calculated_result['cooling_district_budget'] = 0;
			    $calculated_result['water_budget'] = 0;
			}
			break;
		}
		$calculated_result['cdd'] = $result['cdd'];
		$calculated_result['hdd'] = $result['hdd'];
		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
		// Based on give formula
		$calculated_result['occupancy'] = (($result['total_room_night'] / ($result['rooms_keys'] * $days_of_month)) * 100);
		if(isset($reportData[$result['month_id']][$result['year_id']]) && !empty($reportData[$result['month_id']][$result['year_id']])) {
		    continue;
		} else {
		$reportData[$result['month_id']][$result['year_id']] = $calculated_result;
		}
	    }
	}
	$this->load->model('sites/sites_model');
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$this->sites_model->year = $filters['start_year'];
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$data['site_detail'] = $site_detail;
	$data['site_id'] = $site_id;
	$is_occupancy = true;
	$x_axis_title = 'report-axis-cost';
	$report_title = '';
	$report_tmpl = ''; // Default template file
	if (in_array($utilitychanger, $YTDdefaultreports) || $time_type == 'advance_select_avg_ytd') {
	    $report_tmpl = 'admin_advance_ytd';
	}
	if (in_array($utilitychanger, $YTDdefaultreports) || $time_type == 'advance_select_choose_year') {
	    $report_tmpl = 'admin_advance_ytd';
	}
	switch ($utilitychanger) {
	    case 'electricity_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_electricity_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'electricity_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_electricity_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'electricity_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_electricity_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'electricity_cost_per_conditional_area':
		$x_axis_title = 'report-axis-cost-per-conditional-area';
		$report_title = 'report_title_electricity_cost_per_conditional_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'electricity_kwh_per_room_night':
		$x_axis_title = 'report-axis-kwh-per-room-night';
		$report_title = 'report_title_electricity_kwh_per_room_night';
		$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('per_room_night');
		$x_axis_title_value = GetSiteUtilityUnitName($site_id, 'electricity') . " / " . lang('report-axis-room-night');
		$excel_title = lang('electricity') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		break;
	    case 'electricity_kwh_per_guest':
		$x_axis_title = 'report-axis-kwh-per-guest';
		$report_title = 'report_title_electricity_kwh_per_guest';
		$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('per_guest');
		$x_axis_title_value = GetSiteUtilityUnitName($site_id, 'electricity') . " / " . lang('report-axis-guest');
		$excel_title = lang('electricity') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		break;
	    case 'electricity_kwh_per_built_area':
		$x_axis_title = 'report-axis-kwh-per-built-area';
		$report_title = 'report_title_electricity_kwh_per_built_area';
		$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('per_built_area');
		$x_axis_title_value = GetSiteUtilityUnitName($site_id, 'electricity') . " / " . lang('report-axis-built-area');
		$excel_title = lang('electricity') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		break;
	    case 'electricity_kwh_per_conditional_area':
		$x_axis_title = 'report-axis-kwh-per-conditional-area';
		$report_title = 'report_title_electricity_kwh_per_conditional_area';
		$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('per_cooled_area');
		$x_axis_title_value = GetSiteUtilityUnitName($site_id, 'electricity') . " / " . lang('report-axis-conditional-area');
		$excel_title = lang('electricity') . "/" . lang('report-axis-conditional-area') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		break;
		/* case 'electricity_kwh_compare_last_year':
	      $x_axis_title = 'report-axis-kwh';
	      $report_title = 'report_title_electricity_kwh_compare_last_year';
	      $report_tmpl  = 'admin_advance_ytd';
	      break; */
	    case 'electricity_kwh_budget_forecast':
		$x_axis_title = 'report-axis-kwh';
		$report_title = 'report_title_electricity_kwh_budget_forecast';
		$report_tmpl = 'admin_advance_budget_forcasted';
		$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('vs_budget');
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		$excel_title_1 = lang('electricity') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		$excel_title_2 = lang('electricity') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
		break;
	    case 'electricity_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_electricity_cost_budget_forecast';
		$report_tmpl = 'admin_advance_budget_forcasted';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('electricity') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('electricity') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
	    case 'lpg_m3_per_room_night':
		$x_axis_title = 'report-axis-kg-per-room-night';
		$report_title = 'report_title_lpg_m3_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('lpg') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		break;
	    case 'lpg_m3_per_guest':
		$x_axis_title = 'report-axis-kg-per-guest';
		$report_title = 'report_title_lpg_m3_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ") / " . lang('report-axis-guest');
		$excel_title = lang('lpg') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		break;
	    case 'lpg_m3_per_built_area':
		$x_axis_title = 'report-axis-kg-per-built-area';
		$report_title = 'report_title_lpg_m3_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ") / " . lang('report-axis-built-area');
		$excel_title = lang('lpg') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		break;
	    case 'lpg_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_lpg_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'lpg_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_lpg_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'lpg_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_lpg_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'lpg_m3_budget_forecast':
		$x_axis_title = 'report-axis-m3';
		$report_title = 'report_title_lpg_m3_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		$excel_title_1 = lang('lpg') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		$excel_title_2 = lang('lpg') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'lpg') . ")";
		break;
	    case 'lpg_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_lpg_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('lpg') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('lpg') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
		/**/
	    case 'district_heating_kwh_per_room_night':
		$x_axis_title = 'report-axis-consumption-kwh-per-room-night';
		$report_title = 'report_title_heating_m3_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('heating') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		break;
	    case 'district_heating_kwh_per_guest':
		$x_axis_title = 'report-axis-consumption-kwh-per-guest';
		$report_title = 'report_title_heating_m3_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ") / " . lang('report-axis-guest');
		$excel_title = lang('heating') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		break;
	    case 'district_heating_kwh_per_built_area':
		$x_axis_title = 'report-axis-consumption-kwh-per-built-area';
		$report_title = 'report_title_heating_m3_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ") / " . lang('report-axis-built-area');
		$excel_title = lang('heating') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		break;
	    case 'district_heating_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_heating_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_heating_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_heating_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_heating_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_heating_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_heating_kwh_budget_forecast':
		$x_axis_title = 'report-axis-kwh';
		$report_title = 'report_title_heating_kwh_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		$excel_title_1 = lang('heating') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		$excel_title_2 = lang('heating') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'district_heating') . ")";
		break;
	    case 'district_heating_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_heating_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('heating') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('heating') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
		/**/
		/**/
	    case 'district_cooling_kwh_per_room_night':
		$x_axis_title = 'report-axis-consumption-kwh-per-room-night';
		$report_title = 'report_title_cooling_m3_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('cooling') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		break;
	    case 'district_cooling_kwh_per_guest':
		$x_axis_title = 'report-axis-consumption-kwh-per-guest';
		$report_title = 'report_title_cooling_m3_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ") / " . lang('report-axis-guest');
		$excel_title = lang('cooling') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		break;
	    case 'district_cooling_kwh_per_built_area':
		$x_axis_title = 'report-axis-consumption-kwh-per-built-area';
		$report_title = 'report_title_cooling_m3_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ") / " . lang('report-axis-built-area');
		$excel_title = lang('cooling') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		break;
	    case 'district_cooling_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_cooling_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_cooling_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_cooling_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_cooling_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_cooling_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'district_cooling_kwh_budget_forecast':
		$x_axis_title = 'report-axis-kwh';
		$report_title = 'report_title_cooling_kwh_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		$excel_title_1 = lang('cooling') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		$excel_title_2 = lang('cooling') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'district_cooling') . ")";
		break;
	    case 'district_cooling_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_cooling_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('cooling') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('cooling') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
		/**/
	    case 'natural_gas_m3_per_room_night':
		$x_axis_title = 'report-axis-m3-per-room-night';
		$report_title = 'report_title_natural_gas_m3_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('natural_gas') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		break;
	    case 'natural_gas_m3_per_guest':
		$x_axis_title = 'report-axis-m3-per-guest';
		$report_title = 'report_title_natural_gas_m3_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ") / " . lang('report-axis-guest');
		$excel_title = lang('natural_gas') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		break;
	    case 'natural_gas_m3_per_built_area':
		$x_axis_title = 'report-axis-m3-per-built-area';
		$report_title = 'report_title_natural_gas_m3_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ") / " . lang('report-axis-built-area');
		$excel_title = lang('natural_gas') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		break;
	    case 'natural_gas_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_natural_gas_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'natural_gas_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_natural_gas_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'natural_gas_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_natural_gas_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'natural_gas_m3_budget_forecast':
		$x_axis_title = 'report-axis-m3';
		$report_title = 'report_title_natural_gas_m3_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		$excel_title_1 = lang('natural_gas') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		$excel_title_2 = lang('natural_gas') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'natural_gas') . ")";
		break;
	    case 'natural_gas_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_natural_gas_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('natural_gas') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('natural_gas') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
	    case 'oil_liters_per_room_night':
		$x_axis_title = 'report-axis-liters-per-room-night';
		$report_title = 'report_title_oil_liters_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('fuel') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		break;
	    case 'oil_liters_per_guest':
		$x_axis_title = 'report-axis-liters-per-guest';
		$report_title = 'report_title_oil_liters_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ") / " . lang('report-axis-guest');
		$excel_title = lang('fuel') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		break;
	    case 'oil_liters_per_built_area':
		$x_axis_title = 'report-axis-liters-per-built-area';
		$report_title = 'report_title_oil_liters_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ") / " . lang('report-axis-built-area');
		$excel_title = lang('fuel') . "/" . lang('report-axis-built-area') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		break;
	    case 'oil_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_oil_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'oil_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_oil_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'oil_cost_per_built_area':
		$x_axis_title = 'report-axis-cost-per-built-area';
		$report_title = 'report_title_oil_cost_per_built_area';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'oil_liters_budget_forecast':
		$x_axis_title = 'report-axis-liters';
		$report_title = 'report_title_oil_liters_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		$excel_title_1 = lang('fuel') . " " . $filters["end_year"] . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		$excel_title_2 = lang('fuel') . " " . ($filters["end_year"] - 1) . " (" . GetSiteUtilityUnitName($site_id, 'fuel_oil') . ")";
		break;
	    case 'oil_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_oil_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('fuel') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('fuel') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
	    case 'water_liters_per_room_night':
		$x_axis_title = 'report-axis-m3-per-room-night';
		$report_title = 'report_title_water_liters_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ") / " . lang('report-axis-room-night');
		$excel_title = lang('water') . "/" . lang('report-axis-room-night') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
		break;
	    case 'water_liters_per_guest':
		$x_axis_title = 'report-axis-m3-per-guest';
		$report_title = 'report_title_water_liters_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ") / " . lang('report-axis-guest');
		$excel_title = lang('water') . "/" . lang('report-axis-guest') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
		break;
	    case 'water_liters_per_laundered':
		$x_axis_title = 'report-axis-m3-per-laundered';
		$report_title = 'report_title_water_liters_per_laundered';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ") / " . lang('report-axis-laundered');
		$excel_title = lang('water') . "/" . lang('report-axis-laundered') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
		break;
	    case 'water_cost_per_room_night':
		$x_axis_title = 'report-axis-cost-per-room-night';
		$report_title = 'report_title_water_cost_per_room_night';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'water_cost_per_guest':
		$x_axis_title = 'report-axis-cost-per-guest';
		$report_title = 'report_title_water_cost_per_guest';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'water_liters_utility_cisterns_ro':
		$x_axis_title = 'report-axis-m3';
		$report_title = 'report_title_water_liters_utility_cisterns_ro';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_water_report';
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
		break;
	    case 'water_liters':
		$x_axis_title = 'report-axis-m3';
		$report_title = 'report_title_water_liters';
		$view_title = lang($report_title);
		$x_axis_title_value = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	    case 'water_cost_budget_forecast':
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report_title_water_cost_budget_forecast';
		$view_title = lang($report_title);
		$report_tmpl = 'admin_advance_budget_forcasted';
		$x_axis_title_value = lang($x_axis_title);
		$excel_title_1 = lang('lpg') . " " . $filters["end_year"] . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		$excel_title_2 = lang('lpg') . " " . ($filters["end_year"] - 1) . " (" . CURRENCY . CURRENCY_SYMBOL . ")";
		break;
		// electricity_kwh
	    default:
		$x_axis_title = 'report-axis-cost';
		$report_title = 'report-title-consumption-cost';
		$view_title = lang($report_title);
		$x_axis_title_value = lang($x_axis_title);
		$excel_title = lang("excel_" . $utility_type . "_" . $formtype . "_" . $base_type . "");
		break;
	}
	$this->load->model('sites/sites_model');
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$this->sites_model->year = $filters['start_year'];
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$data['site_detail'] = $site_detail;
	$data['site_id'] = $site_id;
	$data['time_type'] = $time_type;
	$data['view_title'] = $view_title;
	$data['x_axis_title_value'] = $x_axis_title_value;
	$data['report_title'] = $report_title;
	$data['x_axis_title'] = $x_axis_title;
	$data['is_occupancy'] = $is_occupancy;
	$data['utility_type_select'] = ($utility_type_select == '') ? 'electricity' : $utility_type_select;
	$data['utility_type'] = ($utility_type == '') ? 'electricity' : $utility_type;
	$data['formtype'] = ($formtype == '') ? 'usage_by_room_nights' : $formtype;
	$data['base_type'] = ($base_type == '') ? 'cost' : $base_type;
	$data['utilitychanger'] = $utilitychanger;
	$data['reportdata'] = $reportData;
	$data['filters'] = $filters;
	$view_type = $this->input->post('view_type', '');
	if ($view_type == 'excel') {
	    require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	    $this->lang->load('sites/sites', 'english');
	    $user_id = $this->session->userdata[$this->section_name]['user_id'];
	    $site_id = $this->session->userdata[$this->section_name]['site_id'];
	    $role_id = $this->session->userdata[$this->section_name]['role_id'];
	    // $base_type : cost / unit
	    // $formtype; : Type of chart
	    // $utility_type : Energy Type
	    // $report_tmpl : Report template for how to show data
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("HEP")
		->setTitle("Excel Report")
		->setKeywords("Excel Report");
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    // Add report info in cell
	    if (in_array($utilitychanger, $YTDdefaultreports) || $time_type == 'advance_select_avg_ytd') {
		switch ($report_tmpl) {
		    case 'admin_advance_budget_forcasted':
			// Set header bold
			$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Month')
			    ->setCellValue('B1', $excel_title_1)
			    ->setCellValue('C1', $excel_title_2)
			    ->setCellValue('D1', sprintf(lang("excel_budgert_year"), $filters["end_year"]));
			$j = 1;
			for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
			    $j++;
			    $previousdata = (!empty($reportData[$i][$filters["end_year"] - 1][$filters['utility_type']])) ? $reportData[$i][$filters["end_year"] - 1][$filters['utility_type']] : 0;
			    $currentdata = (!empty($reportData[$i][$filters["end_year"]][$filters['utility_type']])) ? $reportData[$i][$filters["end_year"]][$filters['utility_type']] : 0;
			    $budgetdata = (!empty($reportData[$i][$filters["end_year"]][$filters['utility_type'] . '_budget'])) ? $reportData[$i][$filters["end_year"]][$filters['utility_type'] . '_budget'] : 0;
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $fullmontharray[$i])
				->setCellValue("B{$j}", round($currentdata, 2))
				->setCellValue("C{$j}", round($previousdata, 2))
				->setCellValue("D{$j}", round($budgetdata, 2));
			}
			break;
		    case 'admin_advance_water_report':
			// Set header bold
			$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Month')
			    ->setCellValue('B1', sprintf(lang("excel_water_utility_year"), $filters["end_year"]))
			    ->setCellValue('C1', sprintf(lang("excel_water_utility_year"), $filters["end_year"] - 1))
			    ->setCellValue('D1', sprintf(lang("excel_water_ro_year"), $filters["end_year"]))
			    ->setCellValue('E1', sprintf(lang("excel_water_ro_year"), $filters["end_year"] - 1))
			    ->setCellValue('F1', sprintf(lang("excel_water_cisterns_year"), $filters["end_year"]))
			    ->setCellValue('G1', sprintf(lang("excel_water_cisterns_year"), $filters["end_year"] - 1))
			    ->setCellValue('H1', sprintf(lang("excel_budgert_year"), $filters["end_year"]))
			    ->setCellValue('I1', sprintf(lang("excel_occupancy_current"), $filters["end_year"], '%'))
			    ->setCellValue('J1', sprintf(lang("excel_occupancy_previous"), $filters["end_year"], '%'));
			$j = 1;
			for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
			    $j++;
			    $previousutilitydata = (!empty($reportData[$i][$filters["end_year"] - 1]['water_utility'])) ? $reportData[$i][$filters["end_year"] - 1]['water_utility'] : 0;
			    $currentutilitydata = (!empty($reportData[$i][$filters["end_year"]]['water_utility'])) ? $reportData[$i][$filters["end_year"]]['water_utility'] : 0;
			    $previouscisternsdata = (!empty($reportData[$i][$filters["end_year"] - 1]['water_cisterns'])) ? $reportData[$i][$filters["end_year"] - 1]['water_cisterns'] : 0;
			    $currentcisternsdata = (!empty($reportData[$i][$filters["end_year"]]['water_cisterns'])) ? $reportData[$i][$filters["end_year"]]['water_cisterns'] : 0;
			    $previousrodata = (!empty($reportData[$i][$filters["end_year"] - 1]['water_irrigation'])) ? $reportData[$i][$filters["end_year"] - 1]['water_irrigation'] : 0;
			    $currentrodata = (!empty($reportData[$i][$filters["end_year"]]['water_irrigation'])) ? $reportData[$i][$filters["end_year"]]['water_irrigation'] : 0;
			    $budgetdata = (!empty($reportData[$i][$filters["end_year"]][$filters['utility_type'] . '_budget'])) ? $reportData[$i][$filters["end_year"]][$filters['utility_type'] . '_budget'] : 0;
			    $occupancydata = (!empty($reportData[$i][$filters["end_year"]]['occupancy'])) ? $reportData[$i][$filters["end_year"]]['occupancy'] : 0;
			    $previousoccupancydata = (!empty($reportData[$i][$filters["end_year"] - 1]['occupancy'])) ? $reportData[$i][$filters["end_year"] - 1]['occupancy'] : 0;
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $fullmontharray[$i])
				->setCellValue("B{$j}", round($currentutilitydata, 2))
				->setCellValue("C{$j}", round($previousutilitydata, 2))
				->setCellValue("D{$j}", round($currentcisternsdata, 2))
				->setCellValue("E{$j}", round($previouscisternsdata, 2))
				->setCellValue("F{$j}", round($currentrodata, 2))
				->setCellValue("G{$j}", round($previousrodata, 2))
				->setCellValue("H{$j}", round($budgetdata, 2))
				->setCellValue("I{$j}", round($occupancydata, 2))
				->setCellValue("J{$j}", round($previousoccupancydata, 2));
			}
			break;
		    case 'admin_advance_ytd':
			// Set header bold
			$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Month')
			    ->setCellValue('B1', sprintf(lang("excel_{$utility_type}_{$formtype}_{$base_type}_current"), $filters["end_year"]))
			    ->setCellValue('C1', sprintf(lang("excel_{$utility_type}_{$formtype}_{$base_type}_previous"), $filters["end_year"] - 1))
			    ->setCellValue('D1', sprintf(lang("excel_occupancy_current"), $filters["end_year"], '%'))
			    ->setCellValue('E1', sprintf(lang("excel_occupancy_previous"), $filters["end_year"] - 1, '%'));
			$j = 1;
			for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
			    $j++;
			    $previousdata = (!empty($reportData[$i][$filters["end_year"] - 1][$filters['utility_type']])) ? $reportData[$i][$filters["end_year"] - 1][$filters['utility_type']] : 0;
			    $currentdata = (!empty($reportData[$i][$filters["end_year"]][$filters['utility_type']])) ? $reportData[$i][$filters["end_year"]][$filters['utility_type']] : 0;
			    $occupancydata = (!empty($reportData[$i][$filters["end_year"]]['occupancy'])) ? $reportData[$i][$filters["end_year"]]['occupancy'] : 0;
			    $previousoccupancydata = (!empty($reportData[$i][$filters["end_year"] - 1]['occupancy'])) ? $reportData[$i][$filters["end_year"] - 1]['occupancy'] : 0;
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $fullmontharray[$i])
				->setCellValue("B{$j}", round($currentdata, 2))
				->setCellValue("C{$j}", round($previousdata, 2))
				->setCellValue("D{$j}", round($occupancydata, 2))
				->setCellValue("E{$j}", round($previousoccupancydata, 2));
			}
			break;
		    default: // Default template : admin_advance
			break;
		}
	    } else {
		$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
		$fullAlphaArray = array_merge(range('A', 'Z'));
		$dateIteratorArray = array();
		if ($filters["start_year"] == $filters["end_year"]) {
		    // If start and end year is same
		    $startmonthsarray = array();
		    for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
			$startmonthsarray[] = $i;
		    }
		    $dateIteratorArray[$filters["start_year"]] = $startmonthsarray;
		} else {
		    // If start and end year is not same
		    $startmonthsarray = array();
		    $endmonthsarray = array();
		    for ($i = $filters['start_month']; $i <= 12; $i++) {
			$startmonthsarray[] = $i;
		    }
		    for ($i = 1; $i <= $filters['end_month']; $i++) {
			$endmonthsarray[] = $i;
		    }
		    $dateIteratorArray[$filters["start_year"]] = $startmonthsarray;
		    $dateIteratorArray[$filters["end_year"]] = $endmonthsarray;
		}
		// Set header bold
		$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Month')
		    ->setCellValue('B1', $excel_title)
		    ->setCellValue('D1', lang("excel_occupancy"))
		    ->setCellValue('F1', lang("excel_cdd"))
		    ->setCellValue('H1', lang("excel_hdd"));
		$objPHPExcel->getActiveSheet()->mergeCells('B1:C1');
		$objPHPExcel->getActiveSheet()->mergeCells('D1:E1');
		$objPHPExcel->getActiveSheet()->mergeCells('F1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('H1:I1');
		// Set month name for column
		$alphaIncColumn = 1;
		foreach ($dateIteratorArray as $year => $value) {
		    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("{$fullAlphaArray[$alphaIncColumn]}2", $year)
			->setCellValue("{$fullAlphaArray[$alphaIncColumn + 2]}2", $year)
			->setCellValue("{$fullAlphaArray[$alphaIncColumn + 4]}2", $year)
			->setCellValue("{$fullAlphaArray[$alphaIncColumn + 6]}2", $year);
		    $alphaIncColumn++;
		}
		$j = 2; // For excel row ittrator for months
		$k = 0; // For excel row ittrator for years
		$alphaIncColumn = 1;
		foreach ($dateIteratorArray as $year => $value) {
		    foreach ($value as $key1 => $month) {
			$j++;
			$currentdata = (!empty($reportData[$month][$year][$filters['utility_type']])) ? $reportData[$month][$year][$filters['utility_type']] : 0;
			$occupancydata = (!empty($reportData[$month][$year]['occupancy'])) ? $reportData[$month][$year]['occupancy'] : 0;
			$cdddata = (!empty($reportData[$month][$year]["cdd"])) ? $reportData[$month][$year]["cdd"] : 0;
			$hdddata = (!empty($reportData[$month][$year]["hdd"])) ? $reportData[$month][$year]["hdd"] : 0;
			$objPHPExcel->setActiveSheetIndex(0)
			    ->setCellValue("A{$j}", $fullmontharray[$month])
			    ->setCellValue("{$fullAlphaArray[$alphaIncColumn +$k]}{$j}", round($currentdata, 2))
			    ->setCellValue("{$fullAlphaArray[$alphaIncColumn +$k + 2]}{$j}", round($occupancydata, 2))
			    ->setCellValue("{$fullAlphaArray[$alphaIncColumn +$k + 4]}{$j}", round($cdddata, 2))
			    ->setCellValue("{$fullAlphaArray[$alphaIncColumn +$k + 6]}{$j}", round($hdddata, 2));
		    }
		    $k++;
		}
		/* ============================Old code for single year only=================================
		  $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);
		  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Month')
		  ->setCellValue('B1', sprintf(lang("excel_{$utility_type}_{$formtype}_{$base_type}_current"),$filters["end_year"]))
		  ->setCellValue('C1', sprintf(lang("excel_occupancy_current"),$filters["end_year"],'%'))
		  ->setCellValue('D1', sprintf(lang("excel_cdd_year"),$filters["end_year"]))
		  ->setCellValue('E1', sprintf(lang("excel_hdd_year"),$filters["end_year"]));
		  $j = 1;
		  for ($i=$filters['start_month']; $i <= $filters["end_month"]; $i++) {
		  $j++;
		  $currentdata = (!empty($reportData[$i][$filters["end_year"]][$filters['utility_type']])) ? $reportData[$i][$filters["end_year"]][$filters['utility_type']] : 0;
		  $occupancydata = (!empty($reportData[$i][$filters["end_year"]]['occupancy']))?$reportData[$i][$filters["end_year"]]['occupancy']:0;
		  $cdddata = (!empty($reportData[$i][$filters["end_year"]]["cdd"]))?$reportData[$i][$filters["end_year"]]["cdd"]:0;
		  $hdddata = (!empty($reportData[$i][$filters["end_year"]]["hdd"]))?$reportData[$i][$filters["end_year"]]["hdd"]:0;
		  $objPHPExcel->setActiveSheetIndex(0)
		  ->setCellValue("A{$j}", $fullmontharray[$i])
		  ->setCellValue("B{$j}", round($currentdata,2))
		  ->setCellValue("C{$j}", round($occupancydata,2))
		  ->setCellValue("D{$j}", round($cdddata,2))
		  ->setCellValue("E{$j}", round($hdddata,2));
		  }
		 */
	    }
	    ob_end_clean();
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="Excel Report.xls"');
	    header('Cache-Control: max-age=0');
	    // If you're serving to IE 9, then the following may be needed
	    header('Cache-Control: max-age=1');
	    // If you're serving to IE over SSL, then the following may be needed
	    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	    header('Pragma: public'); // HTTP/1.0
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	$this->breadcrumb->add(lang('advance_reports'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/advance');
	$this->theme->set('page_title', lang('advance_reports'));
	$this->theme->view($data, $report_tmpl);
    }

    public function sites()
    {
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	if (!SHOW_GROUP_REPORT) {
	    redirect('dashboard');
	}
	$regions = $this->reports_model->getAllRegions();
	$regionDropdownArray = array();
	$regionDropdownArray[''] = 'Choose region';
	if ($regions) {
	    foreach ($regions as $key => $region) {
		$regionDropdownArray[$region['id']] = $region['region_name'];
	    }
	}
	$days_of_month = date('t');
	$report_tmpl = 'admin_sites';
	$results = array();
	$reportData = array();
	$site_filters = array();
	// $sites_list = $this->reports_model->getSites($site_filters);
	$site_custom_filter = array();
	$selected_region = '';
	$startdate = '';
	$postData = $this->input->post();
	$selectedYear = (isset($postData['annual_year']) && $postData['annual_year'] != '') ? $postData['annual_year'] : date('Y');
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $report_type = $this->input->post('report_type', '');
	    $startdate = $this->input->post('startdate', '');
	    $time_type = $this->input->post('time_type', 'sites_select_choose_month');
	    $site_type = $this->input->post('site_type', 0);
	    $selected_region = $this->input->post('regions');
	    $site_custom_filter = $this->input->post('site_custom_filter', array());
	    $site_custom_filter = explode(',', $site_custom_filter);
	    $site_filters['site_type'] = $site_type;
	    $site_filters['site_ids'] = $site_custom_filter;
	    $site_filters['region_id'] = $this->input->post('regions');
	    $sites = $this->reports_model->getSites($site_filters);
	    if (!empty($sites)) {
		$filter_site_ids = array();
		foreach ($sites as $key => $value) {
		    if ($value['id'] != '') {
			$filter_site_ids[] = $value['id'];
		    }
		}
		$filters['site_ids'] = implode(',', $filter_site_ids);
		switch ($time_type) {
		    case 'sites_select_choose_month':
			$startdateexplode = explode('/', $startdate);
			$filters['month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
			$filters['year'] = (isset($startdateexplode[1])) ? $startdateexplode[1] : '';
			if ($report_type == "total_utilities_by_room_night_and_build_area" || $report_type == "utilities_cost_consumption_site_efficiency_benchmark" || $report_type == "average_kwh_tariff" || $report_type == "electricity_cost_consumption_site_efficiency_benchmark") {
			    $results = $this->reports_forex_model->allsitesUtilityBasedReportByMonth($filters); // Utility Result
			} else if ($report_type == 'sites_annual_group_energy_report') {
			    $chartArray = array(
				'startdate' => $startdate,
				'enddate' => CURRENT_YEAR_MAX_MONTH_ID . '/Y',
				'selected_region' => $selected_region,
			    );
			    $results = $this->get_all_sites_electricity_consumption($chartArray, 'month', $selectedYear);
			} else {
			    $results = $this->reports_model->allsitesUtilityBasedReportByMonth($filters); // Utility Result
			}
			$reportData = $results;
			if ($report_type != 'sites_annual_group_energy_report') {
			    $tariff_results = $this->reports_model->allsitesTariffBasedReportByMonth($filters); // Tariff Result
			    $days_of_month = cal_days_in_month(CAL_GREGORIAN, $filters['month'], $filters['year']);
			    if (!empty($results)) {
				foreach ($results as $key => $result) {
				    $results[$key]['cooling_district_cost'] = $result['cooling_district_cost'] + $result['district_cooling_fixed_cost'];
				    $results[$key]['heating_district_cost'] = $result['heating_district_cost'] + $result['district_heating_fixed_cost'];
				    $results[$key]['lpg'] = $result['lpg'] + $result['lpg_fixed_cost'];
				    $results[$key]['natural_gas'] = $result['natural_gas'] + $result['natural_gas_fixed_cost'];
				    $results[$key]['water'] = $result['water'] + $result['water_fixed_cost'];
				    $result['occupancy'] = ($result['site_type'] != 4 && $result['site_type'] != 5) ? (($result['total_room_night'] / ($result['rooms_keys'] * $days_of_month)) * 100) : 0;
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
				    $reportData[$result['site_id']]['tariff'] = $result['electricity_cost'] / $result['electricity'];
				}
			    }
			    if (!empty($tariff_results)) {
				foreach ($tariff_results as $tresult) {
				    // $reportData[$tresult['site_id']]['tariff'] //= $tresult['tariff'];
				}
			    }
			}
			break;
		    case 'sites_select_avg_ytd':
			$filters['month'] = CURRENT_YEAR_MAX_MONTH_ID;
			$filters['year'] = $selectedYear;
			$a_months = $filters['month'];
			if ($report_type == "total_utilities_by_room_night_and_build_area" || $report_type == "utilities_cost_consumption_site_efficiency_benchmark" || $report_type == "average_kwh_tariff" || $report_type == "electricity_cost_consumption_site_efficiency_benchmark") {
			    $results = $this->reports_forex_model->allsitesUtilityBasedReportByAvg($filters); // Utility Result
			} else if ($report_type == 'sites_annual_group_energy_report') {
			    $chartArray = array(
				'startdate' => $startdate,
				'enddate' => CURRENT_YEAR_MAX_MONTH_ID . '/Y',
				'selected_region' => $selected_region,
			    );
			    $results = $this->get_all_sites_electricity_consumption($chartArray, 'ytd', $selectedYear);
			} else {
			    $results = $this->reports_model->allsitesUtilityBasedReportByAvg($filters); // Utility Result
			}
			$reportData = $results;
			if ($report_type != 'sites_annual_group_energy_report') {
			    $tariff_results = $this->reports_model->allsitesTariffBasedReportByAvg($filters); // Tariff Result
			    // Devide value with average months
			    if (!empty($results)) {
				foreach ($results as $key => $result) {
				    /* $result['electricity'] = ($result['electricity']/$a_months);
				      $result['electricity_cost'] = ($result['electricity_cost']/$a_months);
				      $result['fuel'] = ($result['fuel']/$a_months);
				      $result['fuel_cost'] = ($result['fuel_cost']/$a_months);
				      $result['lpg'] = ($result['lpg']/$a_months);
				      $result['lpg_cost'] = ($result['lpg_cost']/$a_months);
				      $result['natural_gas'] = ($result['natural_gas']/$a_months);
				      $result['natural_gas_cost'] = ($result['natural_gas_cost']/$a_months);
				      $result['heating_district'] = ($result['heating_district']/$a_months);
				      $result['heating_district_cost'] = ($result['heating_district_cost']/$a_months);
				      $result['cooling_district'] = ($result['cooling_district']/$a_months);
				      $result['cooling_district_cost'] = ($result['cooling_district_cost']/$a_months);
				      $result['water'] = ($result['water']/$a_months);
				      $result['water_cost'] = ($result['water_cost']/$a_months);
				      $result['total_room_night'] = ($result['total_room_night']/$a_months);
				      $result['cdd'] = ($result['cdd']/$a_months);
				      $result['occupancy'] = (($result['occupancy']*100)/$a_months); */
				    $results[$key]['cooling_district_cost'] = $result['cooling_district_cost'] + $result['district_cooling_fixed_cost'];
				    $results[$key]['heating_district_cost'] = $result['heating_district_cost'] + $result['district_heating_fixed_cost'];
				    $results[$key]['lpg'] = $result['lpg'] + $result['lpg_fixed_cost'];
				    $results[$key]['natural_gas'] = $result['natural_gas'] + $result['natural_gas_fixed_cost'];
				    $results[$key]['water'] = $result['water'] + $result['water_fixed_cost'];
				    $result['occupancy'] = ($result['site_type'] != 4 && $result['site_type'] != 5) ? (($result['occupancy'] * 100) / $a_months) : 0;
				    if ($report_type == 'water_liters_per_room_night') {
					$result['water'] = $result['water'] > 0 && $result['total_room_night'] > 0 ? $result['water_liters'] / $result['total_room_night'] : 0;
				    } else if ($report_type == 'electricity_kwh_per_room_night') {
					$result['electricity_kwh'] = $result['electricity'] > 0 && $result['total_room_night'] > 0 ? $result['electricity'] / $result['total_room_night'] : 0;
				    } else if ($report_type == 'water_liters') {
					$result['water_liters'] = $result['water'] > 0 ? $result['water'] : 0;
				    } else if ($report_type == 'electricity_kwh') {
					$result['electricity_kwh'] = $result['electricity'] > 0 ? $result['electricity'] : 0;
				    }
				    $reportData[$result['site_id']] = $result;
				    $reportData[$result['site_id']]['tariff'] = $result['electricity_cost'] / $result['electricity'];
				}
			    }
			    if (!empty($tariff_results)) {
				foreach ($tariff_results as $tresult) {
				    /* $reportData[$tresult['site_id']]['tariff'] = ($tresult['tariff']/$a_months); */
				    //$reportData[$tresult['site_id']]['tariff'] //= $tresult['tariff'];
				}
			    }
			}
			break;
		    case 'sites_select_avg_last_year':
			// Condition for scatter chart only (1 type of @)
			/* if($report_type == 'electricity_consumption_site_efficiency_benchmark' or $report_type == 'electricity_cost_consumption_site_efficiency_benchmark'){
			  $filters['month'] = 12;
			  $filters['year'] = (date('Y'));
			  $a_months = 12;
			  }else{
			  $filters['month'] = 12;
			  $filters['year'] = (date('Y')-1);
			  $a_months = 12;
			  } */
			$filters['month'] = 12;
			$filters['year'] = $selectedYear;
			// $filters['year'] = (date('Y') - 1);
			$a_months = 12;
			if ($report_type == "total_utilities_by_room_night_and_build_area" || $report_type == "utilities_cost_consumption_site_efficiency_benchmark" || $report_type == "average_kwh_tariff" || $report_type == "electricity_cost_consumption_site_efficiency_benchmark") {
			    $results = $this->reports_forex_model->allsitesUtilityBasedReportByAvg($filters); // Utility Result
			} else if ($report_type == 'sites_annual_group_energy_report') {
			    $chartArray = array(
				'startdate' => $startdate,
				'enddate' => CURRENT_YEAR_MAX_MONTH_ID . '/' . $selectedYear,
				'selected_region' => $selected_region,
			    );
			    $results = $this->get_all_sites_electricity_consumption($chartArray, '', $selectedYear);
			} else {
			    $results = $this->reports_model->allsitesUtilityBasedReportByAvg($filters); // Utility Result
			}
			$reportData = $results;
			if ($report_type != 'sites_annual_group_energy_report') {
			    $tariff_results = $this->reports_model->allsitesTariffBasedReportByAvg($filters); // Tariff Result
			    // Devide value with average months
			    if (!empty($results)) {
				foreach ($results as $key => $result) {
				    /* $result['electricity'] = ($result['electricity']/$a_months);
				      $result['electricity_cost'] = ($result['electricity_cost']/$a_months);
				      $result['fuel'] = ($result['fuel']/$a_months);
				      $result['fuel_cost'] = ($result['fuel_cost']/$a_months);
				      $result['lpg'] = ($result['lpg']/$a_months);
				      $result['lpg_cost'] = ($result['lpg_cost']/$a_months);
				      $result['natural_gas'] = ($result['natural_gas']/$a_months);
				      $result['natural_gas_cost'] = ($result['natural_gas_cost']/$a_months);
				      $result['heating_district'] = ($result['heating_district']/$a_months);
				      $result['heating_district_cost'] = ($result['heating_district_cost']/$a_months);
				      $result['cooling_district'] = ($result['cooling_district']/$a_months);
				      $result['cooling_district_cost'] = ($result['cooling_district_cost']/$a_months);
				      $result['water'] = ($result['water']/$a_months);
				      $result['water_cost'] = ($result['water_cost']/$a_months);
				      $result['total_room_night'] = ($result['total_room_night']/$a_months);
				      $result['cdd'] = ($result['cdd']/$a_months);
				      $result['occupancy'] = (($result['occupancy']*100)/$a_months); */
				    $results[$key]['cooling_district_cost'] = $result['cooling_district_cost'] + $result['district_cooling_fixed_cost'];
				    $results[$key]['heating_district_cost'] = $result['heating_district_cost'] + $result['district_heating_fixed_cost'];
				    $results[$key]['lpg'] = $result['lpg'] + $result['lpg_fixed_cost'];
				    $results[$key]['natural_gas'] = $result['natural_gas'] + $result['natural_gas_fixed_cost'];
				    $results[$key]['water'] = $result['water'] + $result['water_fixed_cost'];
				    $result['occupancy'] = ($result['site_type'] != 4 && $result['site_type'] != 5) ? (($result['occupancy'] * 100) / $a_months) : 0;
				    if ($report_type == 'water_liters_per_room_night') {
					$result['water'] = $result['water'] > 0 && $result['total_room_night'] > 0 ? $result['water'] / $result['total_room_night'] : 0;
				    } else if ($report_type == 'electricity_kwh_per_room_night') {
					$result['electricity'] = $result['electricity'] > 0 && $result['total_room_night'] > 0 ? $result['electricity'] / $result['total_room_night'] : 0;
				    } else if ($report_type == 'water_liters') {
					$result['water_liters'] = $result['water'] > 0 ? $result['water'] : 0;
				    } else if ($report_type == 'electricity_kwh_per_room_night') {
					$result['electricity_kwh'] = $result['electricity'] > 0 ? $result['electricity'] : 0;
				    }
				    $reportData[$result['site_id']] = $result;
				    $reportData[$result['site_id']]['tariff'] = (isset($result['electricity']) && $result['electricity'] != 0) ? ($result['electricity_cost'] / $result['electricity']) : 0;
				}
			    }
			    if (!empty($tariff_results)) {
				foreach ($tariff_results as $tresult) {
				    /* $reportData[$tresult['site_id']]['tariff'] = ($tresult['tariff']/$a_months); */
				    //$reportData[$tresult['site_id']]['tariff'] //= $tresult['tariff'];
				}
			    }
			}
			break;
		    default:
			break;
		}
		// For electricity kwh scatter report only(To show previous year data)
		if ($report_type == 'electricity_consumption_site_efficiency_benchmark' or $report_type == 'electricity_cost_consumption_site_efficiency_benchmark' or $report_type == 'utilities_cost_consumption_site_efficiency_benchmark') {
		    $filters_more['month'] = $filters['month']; //12;
		    $filters_more['year'] = $filters['year'] - 1; //(date('Y')-1);
		    $pa_months = $filters_more['month'];
		    $filters_more['site_ids'] = implode(',', $filter_site_ids);
		    if ($report_type == "utilities_cost_consumption_site_efficiency_benchmark") {
			// $previous_results = $this->reports_forex_model->allsitesUtilityBasedReportByAvg($filters_more); // Utility Result
			$previous_results = $this->reports_forex_model->allsitesUtilityBasedReportByAvgForBenchmark($filters_more); // Utility Result
		    } else {
			$previous_results = $this->reports_model->allsitesUtilityBasedReportByAvg($filters_more); // Utility Result
		    }
		    // Devide value with average months
		    if (!empty($previous_results)) {
			foreach ($previous_results as $keyY => $presult) {
			    /* $presult['electricity'] = ($presult['electricity']/$pa_months);
			      $presult['electricity_cost'] = ($presult['electricity_cost']/$pa_months);
			      $presult['fuel'] = ($presult['fuel']/$pa_months);
			      $presult['fuel_cost'] = ($presult['fuel_cost']/$pa_months);
			      $presult['lpg'] = ($presult['lpg']/$pa_months);
			      $presult['lpg_cost'] = ($presult['lpg_cost']/$pa_months);
			      $presult['natural_gas'] = ($presult['natural_gas']/$pa_months);
			      $presult['natural_gas_cost'] = ($presult['natural_gas_cost']/$pa_months);
			      $presult['heating_district'] = ($presult['heating_district']/$pa_months);
			      $presult['heating_district_cost'] = ($presult['heating_district_cost']/$pa_months);
			      $presult['cooling_district'] = ($presult['cooling_district']/$pa_months);
			      $presult['cooling_district_cost'] = ($presult['cooling_district_cost']/$pa_months);
			      $presult['water'] = ($presult['water']/$pa_months);
			      $presult['water_cost'] = ($presult['water_cost']/$pa_months);
			      $presult['total_room_night'] = ($presult['total_room_night']/$pa_months);
			      $presult['cdd'] = ($presult['cdd']/$pa_months);
			      $presult['occupancy'] = (($presult['occupancy']*100)/$pa_months); */
			    $previous_results[$keyY]['cooling_district_cost'] = $presult['cooling_district_cost'] + $presult['district_cooling_fixed_cost'];
			    $previous_results[$keyY]['heating_district_cost'] = $presult['heating_district_cost'] + $presult['district_heating_fixed_cost'];
			    $results[$key]['lpg'] = $result['lpg'] + $result['lpg_fixed_cost'];
			    $results[$key]['natural_gas'] = $result['natural_gas'] + $result['natural_gas_fixed_cost'];
			    $results[$key]['water'] = $result['water'] + $result['water_fixed_cost'];
			    $presult['occupancy'] = ($presult['occupancy'] * 100);
			    $reportData['previousdata'][$presult['site_id']] = $presult;
			}
		    }
		}
		// If report type is annual group report
		if ($report_type == 'sites_annual_group_energy_report') {
		}
		// Set template and report data
		switch ($report_type) {
		    case 'water_liters':
			$report_title = 'sites_water_liters_report_title';
			$view_title = lang('water_consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
			$x_axis_title = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
			$filters['utility_type'] = 'water';
			break;
		    case 'electricity_kwh':
			$report_title = 'sites_electricity_kwh_report_title';
			$view_title = lang('electricity_consumption') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
			$x_axis_title = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
			$filters['utility_type'] = 'electricity';
			break;
		    case 'water_liters_per_room_night':
			$report_title = 'sites_water_liters_per_room_night_report_title';
			$view_title = lang('water_consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ") " . lang('per_room_night');
			$x_axis_title = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'water') . ")";
			$filters['utility_type'] = 'water';
			break;
		    case 'electricity_kwh_per_room_night':
			$report_title = 'sites_electricity_kwh_per_room_night_report_title';
			$view_title = lang('electricity_consumption') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ") " . lang('per_room_night');
			$x_axis_title = lang('consumption') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ")";
			$filters['utility_type'] = 'electricity';
			break;
		    case 'average_kwh_tariff':
			$report_title = 'sites_average_kwh_tariff_report_title';
			$view_title = lang('average') . " (" . GetSiteUtilityUnitName($site_id, 'electricity') . ") " . lang('s_tariff');
			$x_axis_title = lang('report-axis-cost-average_kwh_tariff');
			$filters['utility_type'] = 'tariff';
			$report_tmpl = 'admin_sites_avg_kwh'; // Change report template for different type of report
			break;
		    case 'total_utilities_by_room_night_and_build_area':
			if ($time_type == 'sites_select_avg_ytd') {
			    $filters['is_buildarea'] = false;
			} else {
			    $filters['is_buildarea'] = true;
			}
			$report_title = 'sites_total_utilities_by_room_night_and_build_area_report_title';
			$view_title = lang('sites_total_utilities_by_room_night_and_build_area_report_title');
			$x_axis_title = 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')';
			$filters['utility_type'] = '';
			$report_tmpl = 'admin_sites_all_utilities'; // Change report template for different type of report
			break;
		    case 'electricity_consumption_site_efficiency_benchmark':
			$report_title = 'sites_electricity_consumption_site_efficiency_benchmark_report_title';
			$view_title = lang('electricity') . " " . GetSiteUtilityUnitName($site_id, 'electricity') . " " . lang('consumption_site_efficiency_benchmark');
			$report_tmpl = 'admin_sites_scatter';
			$x_axis_title = lang('axis-title-kwh-per-m');
			$filters['base_type'] = 'unit';
			break;
		    case 'electricity_cost_consumption_site_efficiency_benchmark':
			$report_title = 'electricity_cost_consumption_site_efficiency_benchmark_report_title';
			$view_title = lang('electricity_cost_consumption_site_efficiency_benchmark_report_title');
			$report_tmpl = 'admin_sites_scatter';
			$x_axis_title = lang('axis-title-cost-per-m');
			$filters['base_type'] = 'cost';
			break;
		    case 'utilities_cost_consumption_site_efficiency_benchmark':
			$report_title = 'utilities_cost_consumption_site_efficiency_benchmark_report_title';
			$view_title = lang('utilities_cost_consumption_site_efficiency_benchmark_report_title');
			$report_tmpl = 'admin_sites_total_utilities_scatter';
			$x_axis_title = BASE_CURRENCY_SYMBOL . '/';
			$filters['base_type'] = 'cost';
			break;
		    case 'sites_annual_group_energy_report':
			$report_title = 'sites_annual_group_energy_report';
			$view_title = lang('sites_annual_group_energy_report');
			$x_axis_title = lang('consumption_mj');
			$filters = array();
			$filters['year'] = $selectedYear;
			break;
		    default:
			# code...
			break;
		}
	    }
	    $reportType = '';
	    $view_type = $this->input->post('view_type', '');
	    if ($view_type == 'excel') {
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$this->lang->load('sites/sites', 'english');
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP")
		    ->setTitle("Excel Report")
		    ->setKeywords("Excel Report");
		$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
		switch ($time_type) {
		    case 'sites_select_choose_month':
			$monthName = date('F', mktime(0, 0, 0, (int) $startdateexplode[0], 10));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, sprintf(lang('excel_time_duration_select_month'), $monthName, $startdateexplode[1]));
			$reportType = 'month';
			break;
		    case 'sites_select_avg_ytd':
			$firstDay = date('01/01/Y');
			$lastDay = date('d/m/Y', strtotime('last day of previous month'));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, sprintf(lang('excel_time_duration_ytd'), $firstDay, $lastDay));
			$reportType = 'ytd';
			break;
		    case 'sites_select_avg_last_year':
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, sprintf(lang('excel_time_duration_last_year'), ($selectedYear)));
			break;
		    default:
			break;
		}
		$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
		$objPHPExcel->getActiveSheet()->getStyle('A2:Q2')->getFont()->setBold(true)->setSize(12);
		switch ($report_type) {
		    case 'water_liters':
			$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Site')
			    ->setCellValue('B3', lang('excel_water_consumption'))
			    ->setCellValue('C3', lang('excel_occupancy_label'));
			$j = 4;
			foreach ($sites as $site) {
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $site['site_location_name'])
				->setCellValue("B{$j}", round($reportData[$site['id']]['water'], 2))
				->setCellValue("C{$j}", round($reportData[$site['id']]['occupancy'], 2));
			    $j++;
			}
			break;
		    case 'electricity_kwh':
			$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Site')
			    ->setCellValue('B3', lang('excel_electricity_consumption'))
			    ->setCellValue('C3', lang('excel_occupancy_label'));
			$j = 4;
			foreach ($sites as $site) {
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $site['site_location_name'])
				->setCellValue("B{$j}", round($reportData[$site['id']]['electricity'], 2))
				->setCellValue("C{$j}", round($reportData[$site['id']]['occupancy'], 2));
			    $j++;
			}
			break;
		    case 'water_liters_per_room_night':
			$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Site')
			    ->setCellValue('B3', lang('excel_water_consumption'))
			    ->setCellValue('C3', lang('excel_occupancy_label'));
			$j = 4;
			foreach ($sites as $site) {
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $site['site_location_name'])
				->setCellValue("B{$j}", round($reportData[$site['id']]['water'], 2))
				->setCellValue("C{$j}", round($reportData[$site['id']]['occupancy'], 2));
			    $j++;
			}
			break;
		    case 'electricity_kwh_per_room_night':
			$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Site')
			    ->setCellValue('B3', lang('excel_electricity_consumption'))
			    ->setCellValue('C3', lang('excel_occupancy_label'));
			$j = 4;
			foreach ($sites as $site) {
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $site['site_location_name'])
				->setCellValue("B{$j}", round($reportData[$site['id']]['electricity'], 2))
				->setCellValue("C{$j}", round($reportData[$site['id']]['occupancy'], 2));
			    $j++;
			}
			break;
		    case 'average_kwh_tariff':
			$objPHPExcel->getActiveSheet()->getStyle('A3:B3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Site')
			    ->setCellValue('B3', lang('sites_average_kwh_tariff'));
			$j = 4;
			foreach ($sites as $site) {
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $site['site_location_name'])
				->setCellValue("B{$j}", round($reportData[$site['id']]['tariff'], 2));
			    $j++;
			}
			break;
		    case 'total_utilities_by_room_night_and_build_area':
			$objPHPExcel->getActiveSheet()->getStyle('A3:Y3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B2:Y2')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B2:Y2')->getAlignment()->setHorizontal("center");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', lang('electricity'));
			$objPHPExcel->getActiveSheet()->mergeCells('B2:D2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', lang('fuel'));
			$objPHPExcel->getActiveSheet()->mergeCells('E2:G2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', lang('lpg'));
			$objPHPExcel->getActiveSheet()->mergeCells('H2:J2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K2', lang('natural_gas'));
			$objPHPExcel->getActiveSheet()->mergeCells('K2:M2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N2', lang('water'));
			$objPHPExcel->getActiveSheet()->mergeCells('N2:P2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q2', lang('heating_district'));
			$objPHPExcel->getActiveSheet()->mergeCells('Q2:S2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T2', lang('cooling_district'));
			$objPHPExcel->getActiveSheet()->mergeCells('T2:V2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W2', lang('excel_occupancy_label'));
			//$objPHPExcel->getActiveSheet()->mergeCells('W2:Y2');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', lang('site'))
			    ->setCellValue('B3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('C3', lang('excel_room_night'))
			    ->setCellValue('D3', lang('excel_built_area'))
			    ->setCellValue('E3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('F3', lang('excel_room_night'))
			    ->setCellValue('G3', lang('excel_built_area'))
			    ->setCellValue('H3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('I3', lang('excel_room_night'))
			    ->setCellValue('J3', lang('excel_built_area'))
			    ->setCellValue('K3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('L3', lang('excel_room_night'))
			    ->setCellValue('M3', lang('excel_built_area'))
			    ->setCellValue('N3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('O3', lang('excel_room_night'))
			    ->setCellValue('P3', lang('excel_built_area'))
			    ->setCellValue('Q3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('R3', lang('excel_room_night'))
			    ->setCellValue('S3', lang('excel_built_area'))
			    ->setCellValue('T3', 'Cost (' . BASE_CURRENCY . '' . BASE_CURRENCY_SYMBOL . ')')
			    ->setCellValue('U3', lang('excel_room_night'))
			    ->setCellValue('V3', lang('excel_built_area'));
			//->setCellValue('W3', lang('excel_cost'))
			//->setCellValue('X3', lang('excel_room_night'))
			//->setCellValue('Y3', lang('excel_built_area'));
			$j = 4;
			foreach ($sites as $site) {
			    $sitedata = $site['site_location_name'];
			    if (!empty($site['country'])) {
				// $sitedata .= '-' . $site['country'];
			    }
			    $electricitydataCost = (!empty($reportData[$site['id']]['electricity_cost'])) ? ($reportData[$site['id']]['electricity_cost']) : 0;
			    $fueldataCost = (!empty($reportData[$site['id']]['fuel_cost'])) ? ($reportData[$site['id']]['fuel_cost']) : 0;
			    $lpgdataCost = (!empty($reportData[$site['id']]['lpg_cost'])) ? ($reportData[$site['id']]['lpg_cost']) : 0;
			    $natural_gasdataCost = (!empty($reportData[$site['id']]['natural_gas_cost'])) ? ($reportData[$site['id']]['natural_gas_cost']) : 0;
			    $waterdataCost = (!empty($reportData[$site['id']]['water_cost'])) ? ($reportData[$site['id']]['water_cost']) : 0;
			    $heating_districtdataCost = (!empty($reportData[$site['id']]['heating_district_cost'])) ? ($reportData[$site['id']]['heating_district_cost']) : 0;
			    $cooling_districtdataCost = (!empty($reportData[$site['id']]['cooling_district_cost'])) ? ($reportData[$site['id']]['cooling_district_cost']) : 0;
			    $cdddataCost = (!empty($reportData[$site['id']]['cdd'])) ? ($reportData[$site['id']]['cdd']) : 0;
			    $occupancydataCost = (!empty($reportData[$site['id']]['occupancy'])) ? ($reportData[$site['id']]['occupancy']) : 0;
			    $electricitydata = (!empty($reportData[$site['id']]['electricity_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['electricity_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $fueldata = (!empty($reportData[$site['id']]['fuel_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['fuel_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $lpgdata = (!empty($reportData[$site['id']]['lpg_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['lpg_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $natural_gasdata = (!empty($reportData[$site['id']]['natural_gas_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['natural_gas_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $waterdata = (!empty($reportData[$site['id']]['water_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['water_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $heating_districtdata = (!empty($reportData[$site['id']]['heating_district_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['heating_district_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $cooling_districtdata = (!empty($reportData[$site['id']]['cooling_district_cost']) && !empty($reportData[$site['id']]['total_room_night'])) ? ($reportData[$site['id']]['cooling_district_cost'] / $reportData[$site['id']]['total_room_night']) : 0;
			    $cdddata = (!empty($reportData[$site['id']]['cdd'])) ? ($reportData[$site['id']]['cdd']) : 0;
			    $occupancydata = (!empty($reportData[$site['id']]['occupancy'])) ? ($reportData[$site['id']]['occupancy']) : 0;
			    $electricitybuildareadata = (!empty($reportData[$site['id']]['electricity_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['electricity_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $fuelbuildareadata = (!empty($reportData[$site['id']]['fuel_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['fuel_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $lpgbuildareadata = (!empty($reportData[$site['id']]['lpg_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['lpg_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $natural_gasbuildareadata = (!empty($reportData[$site['id']]['natural_gas_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['natural_gas_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $waterbuildareadata = (!empty($reportData[$site['id']]['water_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['water_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $heating_districtbuildareadata = (!empty($reportData[$site['id']]['heating_district_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['heating_district_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $cooling_districtbuildareadata = (!empty($reportData[$site['id']]['cooling_district_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? ($reportData[$site['id']]['cooling_district_cost'] / $reportData[$site['id']]['site_builtup_area']) : 0;
			    $cddbuildareadata = (!empty($reportData[$site['id']]['cdd'])) ? ($reportData[$site['id']]['cdd']) : 0;
			    $occupancybuildareadata = (!empty($reportData[$site['id']]['occupancy'])) ? ($reportData[$site['id']]['occupancy']) : 0;
			    // Cost
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A{$j}", $sitedata)
				->setCellValue("B{$j}", round($electricitydataCost, 2))
				->setCellValue("E{$j}", round($fueldataCost, 2))
				->setCellValue("H{$j}", round($lpgdataCost, 2))
				->setCellValue("K{$j}", round($natural_gasdataCost, 2))
				->setCellValue("N{$j}", round($waterdataCost, 2))
				->setCellValue("Q{$j}", round($heating_districtdataCost, 2))
				->setCellValue("T{$j}", round($cooling_districtdataCost, 2))
				->setCellValue("W{$j}", round($occupancydataCost, 2));
			    // Roomnight
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("C{$j}", round($electricitydata, 2))
				->setCellValue("F{$j}", round($fueldata, 2))
				->setCellValue("I{$j}", round($lpgdata, 2))
				->setCellValue("L{$j}", round($natural_gasdata, 2))
				->setCellValue("O{$j}", round($waterdata, 2))
				->setCellValue("R{$j}", round($heating_districtdata, 2))
				->setCellValue("U{$j}", round($cooling_districtdata, 2));
			    //->setCellValue("X{$j}", round($occupancydata,2));
			    // Build area
			    $objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("D{$j}", round($electricitybuildareadata, 2))
				->setCellValue("G{$j}", round($fuelbuildareadata, 2))
				->setCellValue("J{$j}", round($lpgbuildareadata, 2))
				->setCellValue("M{$j}", round($natural_gasbuildareadata, 2))
				->setCellValue("P{$j}", round($waterbuildareadata, 2))
				->setCellValue("S{$j}", round($heating_districtbuildareadata, 2))
				->setCellValue("V{$j}", round($cooling_districtbuildareadata, 2));
			    //->setCellValue("Y{$j}", round($occupancybuildareadata,2));
			    $j++;
			}
			break;
		    case 'electricity_consumption_site_efficiency_benchmark':
			$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', lang('site'))
			    ->setCellValue('B3', lang('excel_this_year'))
			    ->setCellValue('C3', lang('excel_prev_year'))
			    ->setCellValue('D3', lang('excel_per_change'))
			    ->setCellValue('E3', lang('excel_built_area'));
			$j = 4;
			foreach ($sites as $site) {
			    if (!empty($reportData[$site['id']]['electricity'])) {
				$currentdata = (!empty($reportData[$site['id']]['electricity']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? $reportData[$site['id']]['electricity'] / $reportData[$site['id']]['site_builtup_area'] : 0;
				$previousdata = (!empty($reportData['previousdata'][$site['id']]['electricity']) && !empty($reportData['previousdata'][$site['id']]['site_builtup_area'])) ? $reportData['previousdata'][$site['id']]['electricity'] / $reportData['previousdata'][$site['id']]['site_builtup_area'] : 0;
				$built_up_areadata = (!empty($reportData[$site['id']]['site_builtup_area'])) ? $reportData[$site['id']]['site_builtup_area'] : 0;
				$currentdata = round($currentdata, 2);
				$previousdata = round($previousdata, 2);
				$built_up_areadata = round($built_up_areadata, 2);
				$difference = $currentdata - $previousdata;
				$percentage_change = ($difference * 100 / $currentdata);
				$percentage_change = round($percentage_change, 2);
				$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue("A{$j}", $site['site_location_name'])
				    ->setCellValue("B{$j}", $currentdata)
				    ->setCellValue("C{$j}", $previousdata)
				    ->setCellValue("D{$j}", $percentage_change . '%')
				    ->setCellValue("E{$j}", $built_up_areadata);
				$j++;
			    }
			}
			break;
		    case 'electricity_cost_consumption_site_efficiency_benchmark':
			$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true)->setSize(12);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', lang('site'))
			    ->setCellValue('B3', lang('excel_this_year'))
			    ->setCellValue('C3', lang('excel_prev_year'))
			    ->setCellValue('D3', lang('excel_per_change'))
			    ->setCellValue('E3', lang('excel_built_area'));
			$j = 4;
			foreach ($sites as $site) {
			    if (!empty($reportData[$site['id']]['electricity'])) {
				$currentdata = (!empty($reportData[$site['id']]['electricity_cost']) && !empty($reportData[$site['id']]['site_builtup_area'])) ? $reportData[$site['id']]['electricity_cost'] / $reportData[$site['id']]['site_builtup_area'] : 0;
				$previousdata = (!empty($reportData['previousdata'][$site['id']]['electricity_cost']) && !empty($reportData['previousdata'][$site['id']]['site_builtup_area'])) ? $reportData['previousdata'][$site['id']]['electricity_cost'] / $reportData['previousdata'][$site['id']]['site_builtup_area'] : 0;
				$built_up_areadata = (!empty($reportData[$site['id']]['site_builtup_area'])) ? $reportData[$site['id']]['site_builtup_area'] : 0;
				$currentdata = round($currentdata, 2);
				$previousdata = round($previousdata, 2);
				$built_up_areadata = round($built_up_areadata, 2);
				$difference = $currentdata - $previousdata;
				$percentage_change = ($difference * 100 / $currentdata);
				$percentage_change = round($percentage_change, 2);
				$objPHPExcel->setActiveSheetIndex(0)
				    ->setCellValue("A{$j}", $site['site_location_name'])
				    ->setCellValue("B{$j}", $currentdata)
				    ->setCellValue("C{$j}", $previousdata)
				    ->setCellValue("D{$j}", $percentage_change . '%')
				    ->setCellValue("E{$j}", $built_up_areadata);
				$j++;
			    }
			}
			break;
		    case 'sites_annual_group_energy_report':
			$this->annual_excel_report($reportType, $selected_region, $selectedYear);
			break;
		    default:
			# code...
			break;
		}
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Excel Report.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	    }
	}
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	    $sites_list = $this->reports_model->getSites($site_filters);
	}
	$filters['site_custom_filter'] = $site_custom_filter;
	$filters['startdate'] = (isset($startdate)) ? $startdate : '';
	$data['sites'] = $sites;
	$data['sites_list'] = $sites_list;
	$data['reportdata'] = $reportData;
	$data['report_type'] = $report_type;
	$data['time_type'] = $time_type;
	$data['site_type'] = $site_type;
	$data['report_title'] = $report_title;
	$data['x_axis_title'] = $x_axis_title;
	$data['filters'] = $filters;
	$data['regions'] = $regionDropdownArray;
	$data['selected_region'] = $selected_region;
	$data['site_detail'] = $site_detail;
	$data['site_id'] = $site_id;
	$data['view_title'] = $view_title;
	$this->breadcrumb->add(lang('sites-reports'), base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/sites');
	$this->theme->set('page_title', lang('sites-reports'));
	$this->theme->view($data, $report_tmpl);
    }

    public function viewactionplans($site_id)
    {
	$projects_categories = $this->reports_model->getEMACategories();
	$actiondata = array();
	$actiondata['site_id'] = $site_id;
	$actiondata['user_id'] = $this->user_id;
	$tdata['user_id'] = $this->user_id;
	$tdata['site_id'] = $site_id;
	$is_actionplans = false;
	if (!empty($projects_categories)) {
	    foreach ($projects_categories as $key => $category) {
		$todocount = 0;
		$projects = $this->reports_model->getEMAPublicProjects($category['pc']['id']);
		foreach ($projects as $key1 => $project) {
		    $actiondata['project_id'] = $project['p']['id'];
		    $project_todos = $this->reports_model->get_ema_actionplans_todos_bysite($actiondata);
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
	$data['action_categories'] = $this->reports_model->getEMACategoriesList();
	return $data;
    }

    public function daily()
    {
	if (!UTILITIES_DAILY_MENU) {
	    redirect("/dashboard");
	}
	$this->breadcrumb->add('Month to Date submetering', base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/daily');
	$this->load->model('sites/sites_model');
	$user_id = $this->reports_model->user_id;
	$site_id = $this->reports_model->site_id;
	$role_id = $this->reports_model->role_id;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$room_keys = $site_detail['rooms_keys'];
	$month = (int) date('m');
	if (isset($_GET['q'])) {
	    $month = 10;
	}
	$year = (int) date('Y');
	$data = array();
	$data['site_id'] = $site_id;
	$data['year'] = $year;
	$data['month'] = $month;
	$decimal_point = 4;
	$chartData_titles = [];
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $data = $this->input->post();
	    if (isset($data['month'])) {
		$month = (int) $this->input->post('month');
	    }
	    if (isset($data['year'])) {
		$year = (int) $this->input->post('year');
	    }
	}
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$room_keys = $site_detail['rooms_keys'];
	$last_year_month = $month;
	$last_year = $year - 1;
	$totalDays = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$totalDays_last = (int) cal_days_in_month(CAL_GREGORIAN, $last_year_month, $last_year);
	$today_date = (int) date('d');
	if ($month == (int) date('m') && $year == (int) date('Y')) {
	    $to_date = $today_date - 1;
	    $to_date_last_year = $today_date - 1;
	} else {
	    $to_date = $totalDays;
	    $to_date_last_year = $totalDays_last;
	}
	//Get selected month Data
	$this->reports_model->site_id = $site_id;
	$selected_month_year_data = $this->reports_model->get_daily_reading_data($site_id, $month, $year, $to_date);
	$last_month_year_data = $this->reports_model->get_daily_reading_data($site_id, $last_year_month, $last_year, $to_date_last_year);
	$selected_month_year_static_data_results = $this->reports_model->get_daily_reading_static_data($site_id, $month, $year, $to_date);
	$last_month_year_static_data_results = $this->reports_model->get_daily_reading_static_data($site_id, $last_year_month, $last_year, $to_date_last_year);
	// Prepare Data
	$selected_month_year_static_data = array();
	$selected_month_year_static_data['electricity_cost'] = 0;
	$selected_month_year_static_data['lpg_cost'] = 0;
	$selected_month_year_static_data['water_cost'] = 0;
	$selected_month_year_static_data['fuel_oil_cost'] = 0;
	$selected_month_year_static_data['natural_gas_cost'] = 0;
	$selected_month_year_static_data['district_cooling_cost'] = 0;
	$selected_month_year_static_data['district_heating_cost'] = 0;
	$selected_month_year_static_data['cdd'] = 0;
	$selected_month_year_static_data['hdd'] = 0;
	$selected_month_year_static_data['total_room_night'] = 0;
	$selected_month_year_static_data['total_guests'] = 0;
	$last_month_year_static_data = array();
	$last_month_year_static_data['electricity_cost'] = 0;
	$last_month_year_static_data['lpg_cost'] = 0;
	$last_month_year_static_data['water_cost'] = 0;
	$last_month_year_static_data['fuel_oil_cost'] = 0;
	$last_month_year_static_data['natural_gas_cost'] = 0;
	$last_month_year_static_data['district_cooling_cost'] = 0;
	$last_month_year_static_data['district_heating_cost'] = 0;
	$last_month_year_static_data['cdd'] = 0;
	$last_month_year_static_data['hdd'] = 0;
	$last_month_year_static_data['total_room_night'] = 0;
	$last_month_year_static_data['total_guests'] = 0;
	foreach ($selected_month_year_static_data_results as $key => $value) {
	    $selected_month_year_static_data['electricity_cost'] += $value['electricity_cost'];
	    $selected_month_year_static_data['lpg_cost'] += $value['lpg_cost'];
	    $selected_month_year_static_data['water_cost'] += $value['water_cost'];
	    $selected_month_year_static_data['fuel_oil_cost'] += $value['fuel_oil_cost'];
	    $selected_month_year_static_data['natural_gas_cost'] += $value['natural_gas_cost'];
	    $selected_month_year_static_data['district_cooling_cost'] += $value['district_cooling_cost'];
	    $selected_month_year_static_data['district_heating_cost'] += $value['district_heating_cost'];
	    $selected_month_year_static_data['cdd'] += $value['cdd'];
	    $selected_month_year_static_data['hdd'] += $value['hdd'];
	    $selected_month_year_static_data['total_room_night'] += $value['total_room_night'];
	    $selected_month_year_static_data['total_guests'] += $value['total_guests'];
	}
	foreach ($last_month_year_static_data_results as $key => $value) {
	    $value = array_map('floatval', $value);
	    $last_month_year_static_data['electricity_cost'] += $value['electricity_cost'];
	    $last_month_year_static_data['lpg_cost'] += $value['lpg_cost'];
	    $last_month_year_static_data['water_cost'] += $value['water_cost'];
	    $last_month_year_static_data['fuel_oil_cost'] += $value['fuel_oil_cost'];
	    $last_month_year_static_data['natural_gas_cost'] += $value['natural_gas_cost'];
	    $last_month_year_static_data['district_cooling_cost'] += $value['district_cooling_cost'];
	    $last_month_year_static_data['district_heating_cost'] += $value['district_heating_cost'];
	    $last_month_year_static_data['cdd'] += $value['cdd'];
	    $last_month_year_static_data['hdd'] += $value['hdd'];
	    $last_month_year_static_data['total_room_night'] += $value['total_room_night'];
	    $last_month_year_static_data['total_guests'] += $value['total_guests'];
	}
	$report_data = array();
	foreach ($selected_month_year_data as $utility) {
	    $tempData = array();
	    $tempData['id'] = $utility['id'];
	    $tempData['title'] = $utility['title'];
	    $tempData['submission'] = array();
	    foreach ($utility['submission_titles'] as $submission) {
		$tempData['submission'][$submission['title']]['current_year_total'] = $submission['total'];
	    }
	    $report_data[$utility['id']] = $tempData;
	}
	foreach ($last_month_year_data as $utility) {
	    foreach ($utility['submission_titles'] as $submission) {
		$report_data[$utility['id']]['submission'][$submission['title']]['last_year_total'] = $submission['total'];
	    }
	}
	// Get budget data
	$filters['startdate'] = $month . '/' . $year;
	$filters['enddate'] = $month . '/' . $year;
	$filters['start_month'] = $month;
	$filters['start_year'] = $year;
	$filters['end_month'] = $month;
	$filters['end_year'] = $year;
	//data array for dropdown utilities title
	$dropdownArray = $this->reports_model->get_daily_reading_utilities_titles();
	$utilityArray = [];
	$utilityTitlesArray = [];
	$utilityTitles = [];
	$total_room_nights = [
	    'current' => 0,
	    'previous' => 0,
	];
	$i = 1;
	foreach ($dropdownArray as $option) {
	    if ($i == 1) {
		$utility_select = $option['id'];
		$i = 0;
	    }
	    $utilityArray[$option['utility_id']] = $option['utility'];
	    $utilityTitlesArray[$option['utility_id']][$option['id']] = [
		'id' => $option['id'],
		'title' => $option['utility_title'] . GetSiteUtilityUnitName($site_id, $option['utility']),
	    ];
	    $utilityTitles[$option['id']] = [
		'name' => $option['utility_title'] . GetSiteUtilityUnitName($site_id, $option['utility']),
		'parent' => $option['utility'],
	    ];
	}
	$utilityArray = array_unique($utilityArray);
	if ($this->input->post('chart')) {
	    $utility_select = $data['utility_select'];
	    if (empty($utility_select)) {
		$this->theme->set_message(lang('utility-generate-chart-no-data'), 'error');
		redirect("/reports/daily");
	    }
	    /* =================== current year data ======================= */
	    $filterData = [
		'title_id' => $utility_select,
		'month' => $month,
		'year_id' => $year,
	    ];
	    $utility_title_data['current'] = $this->reports_model->get_daily_reading_utilities_title_data($filterData);
	    $daily_reading_utilities_data['current'] = $this->reports_model->get_daily_reading_utilities_data($filterData);
	    $days_of_month_current = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    /* =================== current year data ======================= */
	    /* =================== previos year data ======================= */
	    $filterData = [
		'title_id' => $utility_select,
		'month' => $month,
		'year_id' => ($year - 1),
	    ];
	    $utility_title_data['previous'] = $this->reports_model->get_daily_reading_utilities_title_data($filterData);
	    $daily_reading_utilities_data['previous'] = $this->reports_model->get_daily_reading_utilities_data($filterData);
	    $days_of_month_previous = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    /* =================== previos year data ======================= */
	    $total_days = ($days_of_month_current > $days_of_month_previous) ? $days_of_month_current : $days_of_month_previous;
	    if (count($data['utility_select']) == 1) {
		/* =================== chart data iitialization ================= */
		$chartData_cdd_hdd = [];
		$chartData_occupancy = [];
		$chartData_cdd_hdd[0] = [
		    'Date',
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year - 1),
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year),
		    'CDD - ' . ($year - 1),
		    'CDD - ' . $year,
		    'HDD - ' . ($year - 1),
		    'HDD - ' . $year,
		];
		$chartData_occupancy[0] = [
		    'Date',
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year - 1),
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year),
		    'Occupancy - ' . ($year - 1),
		    'Occupancy - ' . $year,
		];
		/* =================== chart data iitialization ================= */
		for ($i = 1; $i <= $total_days; $i++) {
		    /* ============================= CDD - HDD values ============================== */
		    //Previous year
		    if (array_key_exists($i, $daily_reading_utilities_data['previous'])) {
			$previous_cdd = $daily_reading_utilities_data['previous'][$i]['cdd'];
			$previous_hdd = $daily_reading_utilities_data['previous'][$i]['hdd'];
		    } else {
			$previous_cdd = 0;
			$previous_hdd = 0;
		    }
		    //Current year
		    if (array_key_exists($i, $daily_reading_utilities_data['current'])) {
			$current_cdd = $daily_reading_utilities_data['current'][$i]['cdd'];
			$current_hdd = $daily_reading_utilities_data['current'][$i]['hdd'];
		    } else {
			$current_cdd = 0;
			$current_hdd = 0;
		    }
		    /* ============================= CDD - HDD values ============================== */
		    /* ============================= Utilities values ============================== */
		    //Previous year
		    if (array_key_exists($i, $utility_title_data['previous'])) {
			$utility_value_previous = $utility_title_data['previous'][$i][$utility_select[0]]['value'];
		    } else {
			$utility_value_previous = 0;
		    }
		    //Current year
		    if (array_key_exists($i, $utility_title_data['current'])) {
			$utility_value_current = $utility_title_data['current'][$i][$utility_select[0]]['value'];
		    } else {
			$utility_value_current = 0;
		    }
		    /* ============================= Utilities values ============================== */
		    /* ============================= Occupancy values ============================== */
		    //Previous year
		    if (array_key_exists($i, $daily_reading_utilities_data['previous'])) {
			$occupancy_previous = round(($daily_reading_utilities_data['previous'][$i]['total_room_night'] / $room_keys) * 100, $decimal_point);
		    } else {
			$occupancy_previous = 0;
		    }
		    //Current year
		    if (array_key_exists($i, $daily_reading_utilities_data['current'])) {
			$occupancy_current = round(($daily_reading_utilities_data['current'][$i]['total_room_night'] / $room_keys) * 100, $decimal_point);
		    } else {
			$occupancy_current = 0;
		    }
		    /* ============================= Occupancy values ============================== */
		    /* ============================= Chart Data ==================================== */
		    //chart data for cdd hdd
		    $chartData_cdd_hdd[] = [
			$i,
			(float) $utility_value_previous,
			(float) $utility_value_current,
			(float) $previous_cdd,
			(float) $current_cdd,
			(float) $previous_hdd,
			(float) $current_hdd,
		    ];
		    //chart data for Occupancy
		    $chartData_occupancy[] = [
			$i,
			(float) $utility_value_previous,
			(float) $utility_value_current,
			(float) $occupancy_previous,
			(float) $occupancy_current,
		    ];
		    /* ============================= Chart Data ============================== */
		}
	    } else {
		/* =================== chart data iitialization ================= */
		$chartData_cdd_hdd = [];
		$chartData_occupancy = [];
		$chartData_cdd_hdd[0] = ['Date'];
		$chartData_occupancy[0] = ['Date'];
		foreach ($utility_select as $utilityTitleId) {
		    //array_push($chartData_cdd_hdd[0],$utilityTitles[$utilityTitleId]['name'] . " - " . ($year - 1),$utilityTitles[$utilityTitleId]['name'] . " - " . ($year));
		    array_push($chartData_cdd_hdd[0], $utilityTitles[$utilityTitleId]['name']);
		    array_push($chartData_occupancy[0], $utilityTitles[$utilityTitleId]['name']);
		    $chartData_titles[$utilityTitleId]['name'] = $utilityTitles[$utilityTitleId]['name'];
		    $chartData_titles[$utilityTitleId]['parent'] = $utilityTitles[$utilityTitleId]['parent'];
		}
		array_push($chartData_cdd_hdd[0], 'CDD', 'HDD');
		array_push($chartData_occupancy[0], 'Occupancy');
		/* =================== chart data iitialization ================= */
		for ($i = 1; $i <= $total_days; $i++) {
		    /* ============================= CDD - HDD values ============================== */
		    //Previous year
		    if (array_key_exists($i, $daily_reading_utilities_data['previous'])) {
			$previous_cdd = $daily_reading_utilities_data['previous'][$i]['cdd'];
			$previous_hdd = $daily_reading_utilities_data['previous'][$i]['hdd'];
		    } else {
			$previous_cdd = 0;
			$previous_hdd = 0;
		    }
		    //Current year
		    if (array_key_exists($i, $daily_reading_utilities_data['current'])) {
			$current_cdd = $daily_reading_utilities_data['current'][$i]['cdd'];
			$current_hdd = $daily_reading_utilities_data['current'][$i]['hdd'];
		    } else {
			$current_cdd = 0;
			$current_hdd = 0;
		    }
		    /* ============================= CDD - HDD values ============================== */
		    /* ============================= Occupancy values ============================== */
		    //Previous year
		    if (array_key_exists($i, $daily_reading_utilities_data['previous'])) {
			$occupancy_previous = round(($daily_reading_utilities_data['previous'][$i]['total_room_night'] / $room_keys) * 100, $decimal_point);
		    } else {
			$occupancy_previous = 0;
		    }
		    //Current year
		    if (array_key_exists($i, $daily_reading_utilities_data['current'])) {
			$occupancy_current = round(($daily_reading_utilities_data['current'][$i]['total_room_night'] / $room_keys) * 100, $decimal_point);
		    } else {
			$occupancy_current = 0;
		    }
		    /* ============================= Occupancy values ============================== */
		    /* ============================= Utilities values ============================== */
		    $tempHDD = $tempOcu = array();
		    array_push($tempHDD, "$i");
		    array_push($tempOcu, "$i");
		    foreach ($data['utility_select'] as $selectedID) {
			/* //Previous year
			  if (array_key_exists($i, $utility_title_data['previous'])) {
			  if(isset($utility_title_data['previous'][$i][$selectedID])) {
			  array_push($tempHDD,$utility_title_data['previous'][$i][$selectedID]['value']);
			  } else {
			  array_push($tempHDD,0);
			  }
			  } else {
			  array_push($tempHDD,0);
			  } */
			//Current year
			if (array_key_exists($i, $utility_title_data['current'])) {
			    if (isset($utility_title_data['current'][$i][$selectedID])) {
				array_push($tempHDD, (float) $utility_title_data['current'][$i][$selectedID]['value']);
				array_push($tempOcu, (float) $utility_title_data['current'][$i][$selectedID]['value']);
			    } else {
				array_push($tempHDD, 0);
				array_push($tempOcu, 0);
			    }
			} else {
			    array_push($tempHDD, 0);
			    array_push($tempOcu, 0);
			}
		    }
		    //array_push($tempHDD,(float) $previous_cdd, (float) $current_cdd, (float) $previous_hdd, (float) $current_hdd);
		    array_push($tempHDD, (float) $current_cdd, (float) $current_hdd);
		    array_push($tempOcu, (float) $occupancy_current);
		    $chartData_cdd_hdd[] = $tempHDD;
		    $chartData_occupancy[] = $tempOcu;
		    /* ============================= Utilities values ============================== */
		}
	    }
	}
	$template = 'admin_daily';
	// Prepare data
	$data['month'] = $month;
	$data['site_id'] = $site_id;
	$data['year'] = $data['year'];
	$data['last_year'] = $last_year;
	$data['to_date'] = $to_date;
	$data['report_data'] = $report_data;
	$data['current_year_static_data'] = $selected_month_year_static_data;
	$data['last_year_static_data'] = $last_month_year_static_data;
	$data['utilityArray'] = $utilityArray;
	$data['utilityTitlesArray'] = $utilityTitlesArray;
	$data['chartData_titles'] = $chartData_titles;
	if ($this->input->post('chart')) {
	    $data['chartData_cdd_hdd'] = $chartData_cdd_hdd;
	    $data['chartData_occupancy'] = $chartData_occupancy;
	    $data['utilityTitles'] = $utilityTitles;
	    if (!empty($utilityTitles)) {
		if (count($data['utility_select']) == 1) {
		    $template = 'admin_daily_chart_single';
		} else {
		    $template = 'admin_daily_chart';
		}
	    }
	}
	$view_type = $this->input->post('view_type', '');
	if (!$this->input->post('chart') && $view_type == 'excel') {
	    require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	    $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $optioncurrencyvalue = array('currency' => true);
	    $this->lang->load('sites/sites', 'english');
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("HEP")
		->setTitle("Excel Report")
		->setKeywords("Excel Report");
	    // Add logo
	    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	    } else {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	    }
	    $objDrawing = new PHPExcel_Worksheet_Drawing();
	    $objDrawing->setName('Logo');
	    $objDrawing->setDescription('Logo');
	    $objDrawing->setPath($site_logo);
	    $objDrawing->setCoordinates('A1');
	    $objDrawing->setHeight(100); // logo height
	    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	    // Prepare excel data
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $site_detail['site_location_name']);
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B4', $montharray[$month] . ' - ' . $year)
		->setCellValue('C4', $montharray[$month] . ' - ' . $last_year)
		->setCellValue('D4', 'Difference v/s Last Year');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A5', "MONTH TO DATE")
		->setCellValue('B5', "# Days - $to_date")
		->setCellValue('D5', 'Value')
		->setCellValue('E5', '%');
	    // Calculation
	    $last_year_deference = 0;
	    $last_year_percantage = 0;
	    $last_year_deference = $data['current_year_static_data']['total_room_night'] - $data['last_year_static_data']['total_room_night'];
	    $last_year_percantage = (($data['current_year_static_data']['total_room_night'] != '') && ($data['current_year_static_data']['total_room_night'] != 0)) ? (($last_year_deference * 100) / $data['current_year_static_data']['total_room_night']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A6', "Room Nights")
		->setCellValue('B6', number_format($data['current_year_static_data']['total_room_night']))
		->setCellValue('C6', number_format($data['last_year_static_data']['total_room_night']))
		->setCellValue('D6', number_format($last_year_deference))
		->setCellValue('E6', number_format($last_year_percantage));
	    // Calculation
	    $last_year_cdd_deference = 0;
	    $last_year_cdd_percantage = 0;
	    $last_year_cdd_deference = $data['current_year_static_data']['cdd'] - $data['last_year_static_data']['cdd'];
	    $last_year_cdd_percantage = (($data['current_year_static_data']['cdd'] != '') && ($data['current_year_static_data']['cdd'] != 0)) ? (($last_year_cdd_deference * 100) / $data['current_year_static_data']['cdd']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A7', "CDD")
		->setCellValue('B7', number_format($data['current_year_static_data']['cdd']))
		->setCellValue('C7', number_format($data['last_year_static_data']['cdd']))
		->setCellValue('D7', number_format($last_year_cdd_deference))
		->setCellValue('E7', number_format($last_year_cdd_percantage));
	    // Calculation
	    $last_year_hdd_deference = 0;
	    $last_year_hdd_percantage = 0;
	    $last_year_hdd_deference = $data['current_year_static_data']['hdd'] - $data['last_year_static_data']['hdd'];
	    $last_year_hdd_percantage = (($data['current_year_static_data']['hdd'] != '') && ($data['current_year_static_data']['hdd'] != 0)) ? (($last_year_hdd_deference * 100) / $data['current_year_static_data']['hdd']) : 0;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A8', "HDD")
		->setCellValue('B8', number_format($data['current_year_static_data']['hdd']))
		->setCellValue('C8', number_format($data['last_year_static_data']['hdd']))
		->setCellValue('D8', number_format($last_year_hdd_deference))
		->setCellValue('E8', number_format($last_year_hdd_percantage));
	    $alphas = range('A', 'Z');
	    $active_row = 9;
	    $active_column = 0;
	    $merge_cells = array();
	    $legent_cells = array();
	    // Display Data
	    $current_year_total = 0;
	    $last_year_total = 0;
	    foreach ($report_data as $utility) {
		if (empty($utility['submission'])) {
		    continue;
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('A' . $active_row, lang('daily_report_title_' . $utility['title']));
		$legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 4] . $active_row;
		$active_row++;
		foreach ($utility['submission'] as $stitle => $submission) {
		    $last_year_deference = 0;
		    $last_year_percantage = 0;
		    $last_year_deference = $submission['current_year_total'] - $submission['last_year_total'];
		    $last_year_percantage = (($submission['current_year_total'] != '') || ($submission['current_year_total'] != 0)) ? (($last_year_deference * 100) / $submission['current_year_total']) : 0;
		    $current_year_total += $submission['current_year_total'];
		    $last_year_total += $submission['last_year_total'];
		    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . $active_row, $stitle)
			->setCellValue('B' . $active_row, number_format($submission['current_year_total']))
			->setCellValue('C' . $active_row, number_format($submission['last_year_total']))
			->setCellValue('D' . $active_row, number_format($last_year_deference))
			->setCellValue('E' . $active_row, number_format($last_year_percantage));
		    $active_row++;
		}
	    }
	    // Excel cell formation
	    // Merge
	    foreach ($merge_cells as $cell) {
		$objPHPExcel->getActiveSheet()->mergeCells($cell);
	    }
	    $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
	    $objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
	    $objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
	    // Style
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $style = array('font' => array('bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle("A{$active_row}:H{$active_row}")->applyFromArray($style);
	    $style = array('font' => array('size' => 20, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	    $style = array('font' => array('size' => 14, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B6:E100')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $style = array('font' => array('size' => 16, 'bold' => true, 'color' => array('rgb' => 'ffffff')));
	    $objPHPExcel->getActiveSheet()->getStyle('B4:E4')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:E4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    $legent_utility_style = array('font' => array('color' => array('rgb' => 'ffffff')));
	    foreach ($legent_cells as $cell) {
		$objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($legent_utility_style);
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    }
	    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	    // Borders
	    $objPHPExcel->getActiveSheet()->getStyle("A4:A" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("C4:C" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("E4:E" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A5:E5")->applyFromArray(
		array(
		    'borders' => array(
			'top' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
			'bottom' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ":E" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'top' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $file_name = 'Month_To_Date_submetering_Report_' . $fullmontharray[$month] . '_' . $year . '.xls';
	    ob_end_clean();
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="' . $file_name . '"');
	    header('Cache-Control: max-age=0');
	    // If you're serving to IE 9, then the following may be needed
	    header('Cache-Control: max-age=1');
	    // If you're serving to IE over SSL, then the following may be needed
	    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	    header('Pragma: public'); // HTTP/1.0
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	$this->theme->view($data, $template);
    }

    public function prepare_budget()
    {
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$year = (int) date('Y');
	$current_month = (int) date('m');
	$this->load->model('sites/sites_model');
	if (!$year or empty($year) or $year <= 0 or empty($this->session->userdata[$this->section_name]['user_id'])) {
	    redirect("/dashboard");
	}
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);

	$isLocal = true;
	$this->mCurrency = $this->session->userdata[$this->section_name]['mCurrency'];
	$this->session->set_custom_userdata($this->section_name, "mCurrency", '');
	if ($this->mCurrency == "base") {
	    $isLocal = false;
	}
	$data['utility_key_array'] = array();
	if ($site_detail['show_utility_electricity']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'electricity_kwh',
		'title' => 'Electricity',
		'unit' => GetSiteUtilityUnitName($site_id, 'electricity'),
	    );
	}
	if ($site_detail['show_utility_fuel_oil']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'diesel_fuel',
		'title' => 'Diesel Fuel',
		'unit' => GetSiteUtilityUnitName($site_id, 'fuel_oil'),
	    );
	}
	if ($site_detail['show_utility_lpg']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'lpg_consumption',
		'title' => 'L.P. Gas',
		'unit' => GetSiteUtilityUnitName($site_id, 'lpg'),
	    );
	}
	if ($site_detail['show_utility_water']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'water_consumption',
		'title' => 'Water',
		'unit' => GetSiteUtilityUnitName($site_id, 'water'),
	    );
	}
	if ($site_detail['show_utility_natural_gas']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'natural_gas_consumption',
		'title' => 'Natural Gas',
		'unit' => GetSiteUtilityUnitName($site_id, 'natural_gas'),
	    );
	}
	if ($site_detail['show_utility_district_cooling']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'district_cooling_consumption',
		'title' => 'District Cooling',
		'unit' => GetSiteUtilityUnitName($site_id, 'district_cooling'),
	    );
	}
	if ($site_detail['show_utility_district_heating']) {
	    $data['utility_key_array'][] = array(
		'db_key' => 'district_heating_consumption',
		'title' => 'District Heating',
		'unit' => GetSiteUtilityUnitName($site_id, 'district_heating'),
	    );
	}
	$all_report_data = array();
	$total_years = 3;
	for ($i = 0; $i < $total_years; $i++) {
	    $filters = array();
	    $filters['site_id'] = $site_id;
	    $filters['year_id'] = $year - $i;
	    $report_data_result = $this->reports_model->getFIlterUtilityMonthly($filters);
	    if (!empty($report_data_result)) {
		$all_report_data = array_merge($all_report_data, $report_data_result);
	    }
	}
	$report_data = array();
	if (!empty($all_report_data)) {
	    foreach ($all_report_data as $key => $value) {
		$dataValue = array();
		// 03-09-2019 Issue related to budget preparation report
		// $dataValue['total_electricity_kwh'] = $value['total_purchased_electricity'];
		// $dataValue['total_electricity_kwh'] = $value['electricity_total_budget'];
		// 03-09-2019 Issue related to budget preparation report
		$dataValue['total_electricity_kwh'] = $value['total_electricity_kwh'];
		$dataValue['total_diesel_fuel'] = $value['total_fuel_oil'];
		$dataValue['total_lpg_consumption'] = $value['total_lpg'];
		$dataValue['total_water_consumption'] = $value['water_total_consumption'];
		$dataValue['total_natural_gas_consumption'] = $value['total_natural_gas'];
		$dataValue['total_district_cooling_consumption'] = $value['district_cooling'];
		$dataValue['total_district_heating_consumption'] = $value['district_heating'];
		$dataValue['total_electricity_kwh_cost'] = $value['total_purchased_electricity_cost'];
		$dataValue['total_diesel_fuel_cost'] = $value['total_fuel_oil_cost'];
		$dataValue['total_lpg_consumption_cost'] = $value['total_lpg_cost'];
		$dataValue['total_water_consumption_cost'] = $value['water_total_consumption_cost'];
		$dataValue['total_natural_gas_consumption_cost'] = $value['total_natural_gas_cost'];
		$dataValue['total_district_cooling_consumption_cost'] = $value['district_cooling_cost'];
		$dataValue['total_district_heating_consumption_cost'] = $value['district_heating_cost'];
		$dataValue['total_electricity_kwh_tariff'] = round($value['total_purchased_electricity_cost'] / $value['total_purchased_electricity'], 3);
		$dataValue['total_diesel_fuel_tariff'] = round($value['total_fuel_oil_cost'] / $value['total_fuel_oil'], 3);
		$dataValue['total_lpg_consumption_tariff'] = round($value['total_lpg_cost'] / $value['total_lpg'], 3);
		$dataValue['total_water_consumption_tariff'] = round($value['water_total_consumption_cost'] / $value['water_total_consumption'], 3);
		$dataValue['total_natural_gas_consumption_tariff'] = round($value['total_natural_gas_cost'] / $value['total_natural_gas'], 3);
		$dataValue['total_district_cooling_consumption_tariff'] = round($value['district_cooling_cost'] / $value['district_cooling'], 3);
		$dataValue['total_district_heating_consumption_tariff'] = round($value['district_heating_cost'] / $value['district_heating'], 3);
		$report_data[$value['year_id']][$value['month_id']] = $dataValue;
		// Roomnight, CDD, HDD values
		$report_data['data']['full_year']['roomnight'][$value['year_id']][$value['month_id']] = $report_data['data']['full_year']['roomnight'][$value['year_id']][$value['month_id']] + $value['total_room_night'];
		if ($value['month_id'] >= $current_month) {
		    $report_data['data']['ytd']['roomnight'][$value['year_id']][$value['month_id']] = $report_data['data']['ytd']['roomnight'][$value['year_id']][$value['month_id']] + $value['total_room_night'];
		}
		$report_data['data']['full_year']['cdd'][$value['month_id']] = $report_data['data']['full_year']['cdd'][$value['month_id']] + $value['cdd'];
		if ($value['month_id'] >= $current_month) {
		    $report_data['data']['ytd']['cdd'][$value['month_id']] = $report_data['data']['ytd']['cdd'][$value['month_id']] + $value['cdd'];
		}
		$report_data['data']['full_year']['hdd'][$value['month_id']] = $report_data['data']['full_year']['hdd'][$value['month_id']] + $value['hdd'];
		if ($value['month_id'] >= $current_month) {
		    $report_data['data']['ytd']['hdd'][$value['month_id']] = $report_data['data']['ytd']['hdd'][$value['month_id']] + $value['hdd'];
		}
	    }
	}
	/**
	 * Prepare excel file
	 */
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	$alphas = array_merge(range('A', 'Z'));
	$optioncurrencyvalue = array('currency' => true);
	$this->lang->load('sites/sites', 'english');
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Excel Report")
	    ->setKeywords("Excel Report");
	// Add logo and name
	if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	} else {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	}
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Logo');
	$objDrawing->setDescription('Logo');
	$objDrawing->setPath($site_logo);
	$objDrawing->setCoordinates('A1');
	$objDrawing->setHeight(100); // logo height
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $site_detail['site_location_name'] . '- Budget Preparation');
	/**
	 * Prepare header
	 */
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Description');
	$active_row = 4;
	$active_column = 1;
	foreach ($fullmontharray as $month_key => $month_name) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, $month_name);
	    $active_column++;
	}
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, 'Total');
	/**
	 * Prepare excel data
	 */
	$active_column = 0;
	$active_row = 5;
	// Average data
	// $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, 'Average Room Nights');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, 'Occupancy '.($year-2).'(%)');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 1), 'Occupancy '.($year-1).'(%)');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 2), 'Occupancy '.($year).'(%)');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 3), 'Average CDD');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 4), 'Average HDD');
	$active_column++;
	foreach ($montharray as $month_id => $month_name) {
	    if ($month_id < $current_month) {
		// $average_roomnight_value = ($report_data['data']['full_year']['roomnight'][$month_id] / $total_years);
		$average_roomnight_value_LLY = ($report_data['data']['full_year']['roomnight'][$year - 2][$month_id]);
		$average_roomnight_value_LY = ($report_data['data']['full_year']['roomnight'][$year - 1][$month_id]);
		$average_roomnight_value = ($report_data['data']['full_year']['roomnight'][$year][$month_id]);
		$average_cdd_value = ($report_data['data']['full_year']['cdd'][$month_id] / $total_years);
		$average_hdd_value = ($report_data['data']['full_year']['hdd'][$month_id] / $total_years);
	    } else {
		// $average_roomnight_value = ($report_data['data']['ytd']['roomnight'][$month_id] / ($total_years - 1));
		$average_roomnight_value_LLY = ($report_data['data']['ytd']['roomnight'][$year-2][$month_id]);
		$average_roomnight_value_LY = ($report_data['data']['ytd']['roomnight'][$year-1][$month_id]);
		$average_roomnight_value = ($report_data['data']['ytd']['roomnight'][$year][$month_id]);
		$average_cdd_value = ($report_data['data']['ytd']['cdd'][$month_id] / ($total_years - 1));
		$average_hdd_value = ($report_data['data']['ytd']['hdd'][$month_id] / ($total_years - 1));
	    }
	    $average_cdd_total += number_format(round($average_cdd_value, 0));
	    $average_hdd_total += number_format(round($average_hdd_value, 0));
	    $average_roomnight_value_LLY = number_format(round($average_roomnight_value_LLY) / $site_detail['rooms_keys']);
	    $average_roomnight_value_LY = number_format(round($average_roomnight_value_LY) / $site_detail['rooms_keys']);
	    $average_roomnight_value = number_format(round($average_roomnight_value) / $site_detail['rooms_keys']);
	    $average_roomnight_total_LLY += number_format(round($average_roomnight_value_LLY, 0));
	    $average_roomnight_total_LY += number_format(round($average_roomnight_value_LY, 0));
	    $average_roomnight_total += number_format(round($average_roomnight_value, 0));
	    $average_cdd_value = number_format(round($average_cdd_value));
	    $average_hdd_value = number_format(round($average_hdd_value));
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, $average_roomnight_value_LLY);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 1), $average_roomnight_value_LY);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 2), $average_roomnight_value);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 3), $average_cdd_value);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 4), $average_hdd_value);
	    $active_column++;
	}
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ($average_roomnight_total_LLY / 100) *12);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 1), ($average_roomnight_total_LY / 100) *12);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 2), ($average_roomnight_total / 100) *12);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 3), $average_cdd_total);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . ($active_row + 4), $average_hdd_total);
	// Reset
	$active_column = 0;
	$active_row = $active_row + 4;
	// Reset
	$active_row++;
	$active_column = 0;
	foreach ($data['utility_key_array'] as $utility) {
	    // For style
	    $legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 13] . $active_row;
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, $utility['title']);
	    $active_column++;
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    $average_values = array();
	    /*             * ************ No display only calculation of cost ************* */
	    foreach ($montharray as $month_id => $month_name) {
		$average_values['full_year']['cost'][$month_id] = $average_values['full_year']['cost'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key']];
		if ($month_id >= $current_month) {
		    $average_values['ytd']['cost'][$month_id] = $average_values['ytd']['cost'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key']];
		}
	    }
	    /*             * ************ 1st row (Consumpition)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Consumption ' . ($year - 2) . ' (' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, round($report_data[$year - 2][$month_id]['total_' . $utility['db_key']]));
		if ($report_data[$year - 2][$month_id]['total_' . $utility['db_key']] > 0) {
		    $total += $report_data[$year - 2][$month_id]['total_' . $utility['db_key']];
		}
		$average_values['full_year']['consumption'][$month_id] = $average_values['full_year']['consumption'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key']];
		if ($month_id >= $current_month) {
		    $average_values['ytd']['consumption'][$month_id] = $average_values['ytd']['consumption'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key']];
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format($total));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 2nd row (Tariff)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Tariff ' . ($year - 2) . ' (' . currency_symbol($isLocal) . '/' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$tariff_val2 = floatval((string) $report_data[$year - 2][$month_id]['total_' . $utility['db_key'] . '_tariff']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, (round($tariff_val2, 3)));
		if ($report_data[$year - 2][$month_id]['total_' . $utility['db_key'] . '_tariff'] > 0) {
		    $total += $tariff_val2;
		}
		$average_values['full_year']['tariff'][$month_id] = $average_values['full_year']['tariff'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key'] . '_tariff'];
		if ($month_id >= $current_month) {
		    $average_values['ytd']['tariff'][$month_id] = $average_values['ytd']['tariff'][$month_id] + $report_data[$year - 2][$month_id]['total_' . $utility['db_key'] . '_tariff'];
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total / 12, 3)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 3rd row (Consumpition)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Consumption ' . ($year - 1) . ' (' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($report_data[$year - 1][$month_id]['total_' . $utility['db_key']])));
		if ($report_data[$year - 1][$month_id]['total_' . $utility['db_key']] > 0) {
		    $total += $report_data[$year - 1][$month_id]['total_' . $utility['db_key']];
		}
		$average_values['full_year']['consumption'][$month_id] = $average_values['full_year']['consumption'][$month_id] + $report_data[$year - 1][$month_id]['total_' . $utility['db_key']];
		if ($month_id >= $current_month) {
		    $average_values['ytd']['consumption'][$month_id] = $average_values['ytd']['consumption'][$month_id] + $report_data[$year - 1][$month_id]['total_' . $utility['db_key']];
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 4th row (Tariff)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Tariff ' . ($year - 1) . ' (' . currency_symbol($isLocal) . '/' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$tariff_val1 = floatval((string) $report_data[$year - 1][$month_id]['total_' . $utility['db_key'] . '_tariff']);
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, (round($tariff_val1, 3)));
		if ($report_data[$year - 1][$month_id]['total_' . $utility['db_key'] . '_tariff'] > 0) {
		    $total += $tariff_val1;
		}
		$average_values['full_year']['tariff'][$month_id] = $average_values['full_year']['tariff'][$month_id] + $report_data[$year - 1][$month_id]['total_' . $utility['db_key'] . '_tariff'];
		if ($month_id >= $current_month) {
		    $average_values['ytd']['tariff'][$month_id] = $average_values['ytd']['tariff'][$month_id] + $report_data[$year - 1][$month_id]['total_' . $utility['db_key'] . '_tariff'];
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total / 12, 3)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 5th row (Consumpition)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Consumption ' . ($year) . ' (' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($report_data[$year][$month_id]['total_' . $utility['db_key']], 2)));
		if ($report_data[$year][$month_id]['total_' . $utility['db_key']] > 0) {
		    $total += $report_data[$year][$month_id]['total_' . $utility['db_key']];
		}
		$average_values['full_year']['consumption'][$month_id] = $average_values['full_year']['consumption'][$month_id] + $report_data[$year][$month_id]['total_' . $utility['db_key']];
		// No need YTD in current year
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 6th row (Tariff)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Tariff ' . ($year) . ' (' . currency_symbol($isLocal) . '/' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$tariff_val = (floatval((string) $report_data[$year][$month_id]['total_' . $utility['db_key'] . '_tariff']));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, (round($tariff_val, 3)));
		if ($report_data[$year][$month_id]['total_' . $utility['db_key'] . '_tariff'] > 0) {
		    $total += $tariff_val;
		}
		$average_values['full_year']['tariff'][$month_id] = $average_values['full_year']['tariff'][$month_id] + $report_data[$year][$month_id]['total_' . $utility['db_key'] . '_tariff'];
		// No need YTD in current year
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, round($total / 12, 2));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 7th row (Average Consumption)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Average Consumption ' . '(' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		if ($month_id < $current_month) {
		    $avg_consumption = ($average_values['full_year']['consumption'][$month_id]) / $total_years;
		} else {
		    $avg_consumption = (($average_values['ytd']['consumption'][$month_id]) / ($total_years - 1));
		}
		$average_values['total_avg_consumption'][$month_id] = $avg_consumption;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($avg_consumption)));
		if ($avg_consumption > 0) {
		    $total += $avg_consumption;
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total / 12)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 8th row (Average Tariff)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Average Tariff ' . '(' . currency_symbol($isLocal) . '/' . $utility['unit'] . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		if ($month_id < $current_month) {
		    $avg_tariff = floatval(((string) $average_values['full_year']['tariff'][$month_id]) / $total_years);
		} else {
		    $avg_tariff = floatval((((string) $average_values['ytd']['tariff'][$month_id]) / ($total_years - 1)));
		}
		$avg_tariff = round($avg_tariff, 3);
		$average_values['total_avg_tariff'][$month_id] = $avg_tariff;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, (round($avg_tariff, 3)));
		if ($avg_tariff > 0) {
		    $total += $avg_tariff;
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, (round($total / 12, 3)));
	    // Reset
	    $active_row++;
	    $active_column = 0;
	    /*             * ************ 9th row (Average Cost)************* */
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, ' Average Cost ' . '(' . currency_symbol($isLocal) . ')');
	    $active_column++;
	    $total = 0;
	    foreach ($montharray as $month_id => $month_name) {
		$avg_cost = $average_values['total_avg_tariff'][$month_id] * $average_values['total_avg_consumption'][$month_id];
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($avg_cost, 3)));
		if ($avg_cost > 0) {
		    $total += $avg_cost;
		}
		$active_column++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$active_column] . $active_row, number_format(round($total / 12)));
	    // Reset
	    $active_row++;
	    $active_row++;
	    $active_column = 0;
	}
	/**
	 * Prepare excel style
	 */
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	$style = array('font' => array('bold' => true));
	$objPHPExcel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($style);
	$style = array('font' => array('bold' => true));
	$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$style = array('font' => array('bold' => true, 'color' => array('rgb' => 'ffffff')));
	$objPHPExcel->getActiveSheet()->getStyle('B4:N4')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('B4:N4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	$objPHPExcel->getActiveSheet()->getStyle('B4:N4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$style = array('font' => array('bold' => true, 'color' => array('rgb' => '0066CC')));
	$objPHPExcel->getActiveSheet()->getStyle('A5')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('A6')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->applyFromArray($style);
	for ($i = 15; $i < 85; $i += 11) {
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':N' . $i)->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 1) . ':N' . ($i + 1))->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 2) . ':N' . ($i + 2))->applyFromArray($style);
	}
	/* $objPHPExcel->getActiveSheet()->getStyle('A15')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A16')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A17')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A26')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A27')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A28')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A37')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A38')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A39')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A48')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A49')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A50')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A59')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A60')->applyFromArray($style);
	  $objPHPExcel->getActiveSheet()->getStyle('A61')->applyFromArray($style); */
	$objPHPExcel->getActiveSheet()->getStyle('B5:N5')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('B6:N6')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('B7:N7')->applyFromArray($style);
	// Value align to Center
	for ($i = 5; $i < 85; $i++) {
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':N' . $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
	$legent_utility_style = array('font' => array('color' => array('rgb' => 'ffffff')));
	foreach ($legent_cells as $cell) {
	    $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($legent_utility_style);
	    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	}
	$style = array('font' => array('size' => 14));
	$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	$style = array('font' => array('size' => 9));
	$objPHPExcel->getActiveSheet()->getStyle('A4:N85')->applyFromArray($style);
	/**
	 * Border
	 */
	$objPHPExcel->getActiveSheet()->getStyle("A4:N4")->applyFromArray(
	    array(
		'borders' => array(
		    'top' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		    'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		),
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle("A4:A" . ($active_row - 1))->applyFromArray(
	    array(
		'borders' => array(
		    'right' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		),
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle("N4:N" . ($active_row - 1))->applyFromArray(
	    array(
		'borders' => array(
		    'right' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		),
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle("A" . ($active_row - 1) . ":N" . ($active_row - 1))->applyFromArray(
	    array(
		'borders' => array(
		    'right' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		    'bottom' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb' => '000000'),
		    ),
		),
	    )
	);
	/**
	 * Prepare excel merge
	 */
	$objPHPExcel->getActiveSheet()->mergeCells('B1:F1');
	$file_name = 'Budget_Prepare_' . $year . '.xls';
	ob_end_clean();
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $file_name . '"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
    }

    public function export()
    {
	$decimal_places = 4;
	$result = [];
	$month = 0;
	$year = 0;
	$total_days = 0;
	$total_room_night = 0;
	$total_guests = 0;
	$total_electricity_kwh = 0;
	$average_cost_per_kwh = 0;
	$total_electricity_cost = 0;
	$average_purchased_electricity = 0;
	$total_purchased_electricity_cost = 0;
	$total_fuel_oil = 0;
	$total_fuel_oil_rate = 0;
	$total_fuel_oil_cost = 0;
	$total_lpg = 0;
	$total_lpg_cost = 0;
	$total_lpg_rate = 0;
	$water_total_consumption = 0;
	$water_total_consumption_rate = 0;
	$water_total_consumption_cost = 0;
	/* $water_consumption_breakdown_boh   = 0;
	  $water_consumption_breakdown_rooms = 0;
	  $total_consumption_breakdown       = 0; */
	$total_natural_gas = 0;
	$total_natural_gas_rate = 0;
	$total_natural_gas_cost = 0;
	$district_cooling = 0;
	$district_cooling_rate = 0;
	$district_cooling_cost = 0;
	$district_heating = 0;
	$district_heating_rate = 0;
	$district_heating_cost = 0;
	$cdd = 0;
	$hdd = 0;
	//for calculating electric tarrif
	$total_kwh = array();
	$tariff = array();
	$tariff_id = array();
	$total_cost = array();
	if ($this->input->post()) {
	    $data = $this->input->post();
	    $month = $data["month"];
	    $year = $data["year"];
	    $this->utilities_model->utilities_month = $month;
	    $this->utilities_model->utilities_year = $year;
	    $this->utilities_model->site_id = $this->reports_model->site_id;
	    //get Daily utilities of full month
	    $utility = $this->utilities_model->getMonthUtility();
	    $utilityMonth = $this->utilities_model->getUtility();
	    $id = (!empty($utilityMonth)) ? $utilityMonth["id"] : 0;
	    foreach ($utility as $utl) {
		++$total_days;
		$total_room_night += $utl["total_room_night"];
		$total_guests += $utl["total_guests"];
		$total_electricity_kwh += $utl["total_electricity_kwh"];
		$total_purchased_electricity_cost += $utl["total_electricity_kwh"] * $utl["total_electricity_kwh_tariff"];
		$total_electricity_cost += $utl["total_electricity_kwh"] * $utl["total_electricity_kwh_tariff"];
		$water_total_consumption += $utl["total_water_consumption"] + $utl["total_landscape_water_consumption"];
		$water_total_consumption_cost += ($utl["total_water_consumption"] * $utl["total_water_consumption_tariff"]) + ($utl["total_landscape_water_consumption"] * $utl["total_landscape_water_consumption_tariff"]);
		/* $water_consumption_breakdown_boh += $utl["total_landscape_water_consumption"];
		  $water_consumption_breakdown_rooms += $utl["total_water_consumption"]; */
		$total_fuel_oil += $utl["total_diesel_fuel"] + $utl["total_heavy_fuel"];
		$total_fuel_oil_cost += ($utl["total_diesel_fuel"] * $utl["total_diesel_fuel_tariff"]) + ($utl["total_heavy_fuel"] * $utl["total_heavy_fuel_tariff"]);
		$total_lpg += $utl["total_lpg_consumption"];
		$total_lpg_cost += $utl["total_lpg_consumption"] * $utl["total_lpg_consumption_tariff"];
		$total_natural_gas += $utl["total_natural_gas_consumption"];
		$total_natural_gas_cost += $utl["total_natural_gas_consumption"] * $utl["total_natural_gas_consumption_tariff"];
		$district_cooling += $utl["total_district_cooling_consumption"];
		$district_cooling_cost += $utl["total_district_cooling_consumption"] * $utl["total_district_cooling_consumption_tariff"];
		$district_heating += $utl["total_district_heating_consumption"];
		$district_heating_cost += $utl["total_district_heating_consumption"] * $utl["total_district_heating_consumption_tariff"];
		$cdd += $utl["cdd"];
		$hdd += $utl["hdd"];
		$water_irrigation += $utl["total_landscape_water_consumption"];
		$water_irrigation_cost += $utl["total_landscape_water_consumption"] * $utl["total_landscape_water_consumption_tariff"];
	    }
	    /* $total_consumption_breakdown   = $water_consumption_breakdown_boh + $water_consumption_breakdown_rooms; */
	    $average_purchased_electricity = is_nan($total_purchased_electricity_cost / $total_electricity_kwh) ? 0 : $total_purchased_electricity_cost / $total_electricity_kwh;
	    //defining average tariffs
	    $average_cost_per_kwh = is_nan($total_purchased_electricity_cost / $total_electricity_kwh) ? 0 : $total_purchased_electricity_cost / $total_electricity_kwh;
	    $water_total_consumption_rate = is_nan($water_total_consumption_cost / $water_total_consumption) ? 0 : $water_total_consumption_cost / $water_total_consumption;
	    $total_fuel_oil_rate = is_nan($total_fuel_oil_cost / $total_fuel_oil) ? 0 : $total_fuel_oil_cost / $total_fuel_oil;
	    $total_lpg_rate = is_nan($total_lpg_cost / $total_lpg) ? 0 : $total_lpg_cost / $total_lpg;
	    $total_natural_gas_rate = is_nan($total_natural_gas_cost / $total_natural_gas) ? 0 : $total_natural_gas_cost / $total_natural_gas;
	    $district_cooling_rate = is_nan($district_cooling_cost / $district_cooling) ? 0 : $district_cooling_cost / $district_cooling;
	    $district_heating_rate = is_nan($district_heating_cost / $district_heating) ? 0 : $district_heating_cost / $district_heating;
	    $water_irrigation_rate = is_nan($water_irrigation_cost / $water_irrigation) ? 0 : ($water_irrigation_cost / $water_irrigation);
	    // exit;
	    //round Floating variables to 4 decimal points
	    $total_purchased_electricity_cost = round($total_purchased_electricity_cost, $decimal_places);
	    $total_electricity_cost = round($total_electricity_cost, $decimal_places);
	    $water_total_consumption_cost = round($water_total_consumption_cost, $decimal_places);
	    $total_fuel_oil_cost = round($total_fuel_oil_cost, $decimal_places);
	    $total_lpg_cost = round($total_lpg_cost, $decimal_places);
	    $total_natural_gas_cost = round($total_natural_gas_cost, $decimal_places);
	    $district_cooling_cost = round($district_cooling_cost, $decimal_places);
	    $district_heating_cost = round($district_heating_cost, $decimal_places);
	    $average_purchased_electricity = round($average_purchased_electricity, $decimal_places);
	    $average_cost_per_kwh = round($average_cost_per_kwh, $decimal_places);
	    $water_total_consumption_rate = round($water_total_consumption_rate, $decimal_places);
	    $total_fuel_oil_rate = round($total_fuel_oil_rate, $decimal_places);
	    $total_lpg_rate = round($total_lpg_rate, $decimal_places);
	    $total_natural_gas_rate = round($total_natural_gas_rate, $decimal_places);
	    $district_cooling_rate = round($district_cooling_rate, $decimal_places);
	    $district_heating_rate = round($district_heating_rate, $decimal_places);
	    //electric tarrif
	    $electricity_tariff = $this->utilities_model->getElectricityTariff();
	    if (!empty($electricity_tariff)) {
		foreach ($electricity_tariff as $elc_tariff) {
		    if (!empty($elc_tariff["id"])) {
			$tariff_id[] = $elc_tariff["id"];
		    }
		}
	    }
	    $total_kwh[] = $total_electricity_kwh;
	    $tariff[] = round($average_cost_per_kwh, $decimal_places);
	    $total_cost[] = $total_electricity_cost;
	    $tariff_array = [
		'tariff_id' => $tariff_id,
		'total_kwh' => $total_kwh,
		'tariff' => $tariff,
		'total_cost' => $total_cost,
	    ];
	    $utilityMonth['year'] = $year;
	    $utilityMonth['month'] = $month;
	    $utilityMonth['total_room_night'] = $total_room_night;
	    $utilityMonth['total_guests'] = $total_guests;
	    $utilityMonth['total_electricity_kwh'] = $total_electricity_kwh;
	    $utilityMonth['total_electricity_cost'] = $total_electricity_cost;
	    $utilityMonth['total_purchased_electricity'] = $total_electricity_kwh;
	    $utilityMonth['average_cost_per_kwh'] = $average_cost_per_kwh;
	    $utilityMonth['average_purchased_electricity'] = $average_purchased_electricity;
	    $utilityMonth['total_purchased_electricity_cost'] = $total_purchased_electricity_cost;
	    $utilityMonth['total_fuel_oil'] = $total_fuel_oil;
	    $utilityMonth['total_fuel_oil_rate'] = $total_fuel_oil_rate;
	    $utilityMonth['total_fuel_oil_cost'] = $total_fuel_oil_cost;
	    $utilityMonth['fuel_oil_hot_water_boilers'] = $total_fuel_oil;
	    $utilityMonth['fuel_oil_hot_water_boilers_rate'] = $total_fuel_oil_rate;
	    $utilityMonth['fuel_oil_hot_water_boilers_cost'] = $total_fuel_oil_cost;
	    $utilityMonth['total_lpg'] = $total_lpg;
	    $utilityMonth['total_lpg_cost'] = $total_lpg_cost;
	    $utilityMonth['total_lpg_rate'] = $total_lpg_rate;
	    $utilityMonth['lpg_hot_water_boilers'] = $total_lpg;
	    $utilityMonth['lpg_hot_water_boilers_rate'] = $total_lpg_rate;
	    $utilityMonth['lpg_hot_water_boilers_cost'] = $total_lpg_cost;
	    $utilityMonth['water_total_consumption'] = $water_total_consumption;
	    $utilityMonth['water_total_consumption_rate'] = $water_total_consumption_rate;
	    $utilityMonth['water_total_consumption_cost'] = $water_total_consumption_cost;
	    $utilityMonth['water_utility_supply'] = $water_total_consumption;
	    $utilityMonth['water_utility_supply_rate'] = $water_total_consumption_rate;
	    $utilityMonth['water_utility_supply_cost'] = $water_total_consumption_cost;
	    /* $utilityMonth['water_consumption_breakdown_boh']   = $water_consumption_breakdown_boh;
	      $utilityMonth['water_consumption_breakdown_rooms'] = $water_consumption_breakdown_rooms;
	      $utilityMonth['total_consumption_breakdown']       = $total_consumption_breakdown; */
	    $utilityMonth['total_natural_gas'] = $total_natural_gas;
	    $utilityMonth['total_natural_gas_rate'] = $total_natural_gas_rate;
	    $utilityMonth['total_natural_gas_cost'] = $total_natural_gas_cost;
	    $utilityMonth['natural_gas_hot_water_boilers'] = $total_natural_gas;
	    $utilityMonth['natural_gas_hot_water_boilers_rate'] = $total_natural_gas_rate;
	    $utilityMonth['natural_gas_hot_water_boilers_cost'] = $total_natural_gas_cost;
	    $utilityMonth['district_cooling'] = $district_cooling;
	    $utilityMonth['district_cooling_rate'] = $district_cooling_rate;
	    $utilityMonth['district_cooling_cost'] = $district_cooling_cost;
	    $utilityMonth['district_heating'] = $district_heating;
	    $utilityMonth['district_heating_rate'] = $district_heating_rate;
	    $utilityMonth['district_heating_cost'] = $district_heating_cost;
	    $utilityMonth['water_irrigation'] = $water_irrigation;
	    $utilityMonth['water_irrigation_rate'] = $water_irrigation_rate;
	    $utilityMonth['water_irrigation_cost'] = $water_irrigation_cost;
	    $utilityMonth['hdd'] = $hdd;
	    $utilityMonth['cdd'] = $cdd;
	    $utilityMonth['user_id'] = $this->utilities_model->user_id;
	    $utilityMonth['site_id'] = $this->utilities_model->site_id;
	    $utilityMonth['tariff'] = $tariff_array;
	    $result_id = $this->utilities_model->saveUtility($utilityMonth);
	    $this->utilities_model->saveElectricityTariff($utilityMonth);
	    if ($result_id) {
		$this->theme->set_message(lang('utility-export-success'), 'success');
		//session data for exportData show in month utilities
		$exportData = ['month' => $month, 'year' => $year];
		$this->session->set_custom_userdata($this->section_name, "exportData", $exportData);
		// For notifications
		$fieldNamesArray = array(
		    "total_purchased_electricity",
		    "electricity_tariff",
		    "fuel_oil_hot_water_boilers_cost",
		    "lpg_hot_water_boilers_cost",
		    "natural_gas_hot_water_boilers_cost",
		    "district_heating_cost",
		    "district_cooling_cost",
		    "water_utility_supply_cost",
		    "water_consumption_breakdown_rooms",
		    "total_room_night",
		    "total_guests",
		    "total_laundered",
		    "total_fb_services",
		    "cdd",
		    "hdd",
		);
		$utilityMonthNotificationData = $utilityMonth;
		$utilityMonthNotificationData['electricity_tariff'] = $utilityMonth['average_cost_per_kwh'];
		foreach ($utilityMonthNotificationData as $key => $value) {
		    if (in_array($key, $fieldNamesArray)) {
			if (!empty($value)) {
			    $deleteData = array(
				'site_id' => $utilityMonthNotificationData['site_id'],
				'field_name' => $key,
				'month' => $utilityMonthNotificationData['month_id'],
				'year' => $utilityMonthNotificationData['year_id'],
			    );
			    $this->utilities_model->deleteNotification($deleteData);
			}
		    }
		}
	    } else {
		$this->theme->set_message(lang('utility-export-error'), 'error');
	    }
	} else {
	    $this->theme->set_message(lang('utility-export-no-data'), 'error');
	}
	redirect("/reports/management");
    }

    public function generate_report_pdf($data)
    {
	extract($data);
	$this->lang->load('sites/sites', 'english');
	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];
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
	$show_piechart = false;
	$show_actionplans = false;
	$show_site_details = false;
	if (!empty($this->input->post())) {
	    $postdata = [];
	    $postdata = $this->input->post();
	    if ($postdata['submit'] == 'download_hidden') {
		$data['pdf_report_title'] = 'Full Year Utilities Report - ' . (date('Y') - 1);
		$data['previous_title'] = 'Previous Year - ' . (date('Y') - 1);
		$data['current_title'] = 'Current Year - ' . (date('Y') - 1);
		$data['budget_title'] = 'Budget - ' . (date('Y') - 1);
		if ($this->input->post('yearly_report_year')) {
		    $data['pdf_report_title'] = 'Full Year Utilities Report - ' . ($this->input->post('yearly_report_year', date('Y')));
		    $data['previous_title'] = 'Previous Year - ' . ($this->input->post('yearly_report_year', date('Y')) - 1);
		    $data['current_title'] = 'Current Year - ' . ($this->input->post('yearly_report_year', date('Y')));
		    $data['budget_title'] = 'Budget - ' . ($this->input->post('yearly_report_year', date('Y')));
		}
		$data['table_title'] = 'Annual Report';
		$data['type'] = 'annual';
		$data['show_site_details'] = true;
		$show_site_details = true;
		$show_piechart = true;
		$data['showCostBudgetVariance'] = true;
		$data['columnChartImg'] = $postdata['columnChartImg_hidden'];
		$data['pieChartImg'] = $postdata['pieChartImg_hidden'];
		$data['pieChartNewImg'] = $postdata['pieChartNewImg_hidden'];
		$data['wasteChartPreImg_hidden'] = $postdata['wasteChartPreImg_hidden'];
		$data['pieAnnualChartNewImg_hidden'] = $postdata['pieAnnualChartNewImg_hidden'];
		$data['pieAnnualLandfillImg_hidden'] = $postdata['pieAnnualLandfillImg_hidden'];
		$filter_custom_notification = array(
		    'year' => (date('Y') - 1),
		);
		$customnotifications = $this->sites_model->getSiteCustomNotifications($site_id, $filter_custom_notification);
		foreach ($customnotifications as $notification) {
		    if ($notification['annual']) {
			$data['customNotifications'][] = $notification;
		    }
		}
		$content_reports = $this->load->view('admin_landing_pdf_reports_hidden', $data, true);
		$content_reports_piecharts = $this->load->view('admin_landing_pdf_reports_hidden_piecharts', $data, true);
		$data['columnChartCarbonFootprintImg'] = $postdata['columnChartCarbonFootprintAnnualImg'];
		$content_reports_carbon_footprint = $this->load->view('admin_landing_pdf_reports_carbon_footprint_annual', $data, true);
		$this->load->model('sites/sites_model');
		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$site_detials = $this->sites_model->get_site_detail_custom($site_id);
		$carbon_footPrint = 0;
		$total_room_night = 0;
		$water_total_consumption = 0;
		$lpg_value = 0;
		$electricity_value = 0;
		$natural_gas_value = 0;
		$fuel_value = 0;
		$heating_district_value = 0;
		$cooling_district_value = 0;
		$utility_kwh_total = 0;
		$currYear = $this->input->post('yearly_report_year') ? $this->input->post('yearly_report_year') : date('Y');
		$currMonth = 0;
		$data = $this->CalculateMeasures($data, $site_detail_result, $currYear, $currMonth);
		if ($result['chsb_reporting'] == 1) {
		    $postChartData = $this->input->post();
		    $data['chsb_report_chart_1'] = $postChartData['chsb_report_chart_1'];
		    $data['chsb_report_chart_2'] = $postChartData['chsb_report_chart_2'];
		    $data['chsb_report_chart_3'] = $postChartData['chsb_report_chart_3'];
		    $data['chsb_report_chart_4'] = $postChartData['chsb_report_chart_4'];
		    $data['chsb_report_chart_5'] = $postChartData['chsb_report_chart_5'];
		    $data['chsb_report_chart_6'] = $postChartData['chsb_report_chart_6'];
		    $data['chsb_report_chart_7'] = $postChartData['chsb_report_chart_7'];
		    $data['chsb_report_chart_8'] = $postChartData['chsb_report_chart_8'];
		    // $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports', $data, true);
		    $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports_chart_view', $data, true);
		}
	    } else if ($postdata['submit'] == 'download_5years_hidden') {
		$data['pdf_report_title'] = '5 Years Total Utilities Report with Efficiency Actions';
		$data['show_site_details'] = false;
		$show_site_details = false;
		$data['showCostBudgetVariance'] = false;
		$actionplans = $this->viewactionplans($this->reports_model->site_id);
		$show_actionplans = true;
		$data['columnChartImg'] = $postdata['columnChartImg_5years_hidden'];
		$content_reports = $this->load->view('admin_landing_pdf_reports_5years_hidden', $data, true);
		$content_reports_actionplans = $this->load->view('admin_landing_pdf_actionplans', $actionplans, true);
		$content_reports_carbon_footprint = '';
	    } else if ($postdata['submit'] == 'download_monthly_hidden') {
		$this->load->model('sites/sites_model');
		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$site_detials = $this->sites_model->get_site_detail_custom($site_id);
		$currYear = $this->input->post('monthly_report_year');
		$currMonth = $this->input->post('monthly_report_month');
		$data = $this->CalculateMeasures($data, $site_detail_result, $currYear, $currMonth);
		$data['pdf_report_title'] = 'Monthly Utilities report - ' . ($fullmontharray[$filters["filters_comparision_chart"]['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
		$data['previous_title'] = '' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year'] - 1);
		$data['current_title'] = '' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
		$data['budget_title'] = 'Budget - ' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
		$data['table_title'] = 'Monthly Report';
		$data['type'] = 'monthly';
		$data['show_site_details'] = false;
		$show_piechart = true;
		$show_site_details = false;
		$data['showCostBudgetVariance'] = true;
		$data['columnChartImg'] = $postdata['columnChartImg_monthly'];
		$data['pieChartImg'] = $postdata['pieChartImg'];
		$data['pieChartNewImg'] = $postdata['pieChartNewImg'];
		$data['pieChartNew2Img'] = $postdata['pieChartNew2Img'];
		$data['pieChartNew3Img'] = $postdata['pieChartNew3Img'];
		$data['wasteMonthlyChartImg'] = $postdata['wasteMonthlyChartImg'];
		$data['wastePieLandfillMonthlyChartImg'] = $postdata['wastePieLandfillMonthlyChartImg'];
		$data['wastePieMonthlyChartImg'] = $postdata['wastePieMonthlyChartImg'];
		$data['columnChartCarbonFootprintImg'] = $postdata['columnChartCarbonFootprintMonthlyImg'];
		$data['pieChartImgkwhMonthly'] = $postdata['pieChartImgkwhMonthly'];
		$data['pieChartImgcostMonthly'] = $postdata['pieChartImgcostMonthly'];
		$filter_custom_notification = array(
		    'month' => $filters["filters_comparision_chart"]['start_month'],
		    'year' => $filters["filters_comparision_chart"]['start_year'],
		);
		$data['customNotifications'] = $this->sites_model->getSiteCustomNotifications($site_id, $filter_custom_notification);
		$data['is_monthly'] = true;

		// progress on Target report data
		$progressOnTarget = $filtersBaseline = array();
		// For Jan month of new year
		if ($filters['previous_month'] == 0) {
			$filters['previous_month'] = 12;
			$filters['previous_year'] = date('Y') - 2;
			$filters['Progress_previous_year'] = date('Y') - 2;
		}
		if ($filters['previous_month'] == 0) {
			$filtersBaseline['Progress_start_year'] = date('Y') - 1;
			$filtersBaseline['Progress_previous_year'] = date('Y') - 2;
		} else {
			$filtersBaseline['Progress_start_year'] = date('Y');
			$filtersBaseline['Progress_previous_year'] = date('Y') - 1;
		}
		$progressOnTargetData = $this->reports_model->monthlyUtilityProgressOnTarget($filters);
		$filtersBaseline['Progress_start_year'] = $site_detials['baseline_regression_year'];
		$filtersBaseline['Progress_previous_year'] = 'NOTSET';
		$progressOnTargetBaselineData = $this->reports_model->monthlyUtilityProgressOnTarget($filtersBaseline);

		if (!empty($progressOnTargetData)) {
			foreach ($progressOnTargetData as $key => $result) {
			if (isset($progressOnTarget[$result['month_id']][$result['year_id']]) && !empty($progressOnTarget[$result['month_id']][$result['year_id']])) {
				continue;
			} else {
				$progressValue['energy_target'] = (isset($result['energy']) && $result['energy'] != 0) ? $result['energy'] : 0;
				$progressValue['energy_baseline_target'] = (isset($progressOnTargetBaselineData[$key]['energy']) && $progressOnTargetBaselineData[$key]['energy'] != 0) ? $progressOnTargetBaselineData[$key]['energy'] : 0;
				$progressValue['water_target'] = (isset($result['water']) && $result['water'] != 0) ? $result['water'] : 0;
				$progressValue['water_baseline_target'] = (isset($progressOnTargetBaselineData[$key]['water']) && $progressOnTargetBaselineData[$key]['water'] != 0) ? $progressOnTargetBaselineData[$key]['water'] : 0;
				$progressValue['month_id'] = $result['month_id'];
				$progressValue['year_id'] = $result['year_id'];
				$progressOnTarget[$result['month_id']][$result['year_id']] = $progressValue;
			}
			}
		}
		$data['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
		$data['site_detail']['data']['kpi'] = $this->getCarbonRecords($site_id, $data['site_detail']);
		$content_reports = $this->load->view('admin_landing_pdf_reports_monthly_hidden', $data, true);
		$content_reports_piecharts = $this->load->view('admin_landing_pdf_reports_monthly_hidden_piecharts', $data, true);
		$content_reports_carbon_footprint = $this->load->view('admin_landing_pdf_reports_carbon_footprint', $data, true);
		if ($result['chsb_reporting'] == 1) {
		    $postChartData = $this->input->post();
		    $data['chsb_report_chart_1'] = $postChartData['chsb_report_chart_1'];
		    $data['chsb_report_chart_2'] = $postChartData['chsb_report_chart_2'];
		    $data['chsb_report_chart_3'] = $postChartData['chsb_report_chart_3'];
		    $data['chsb_report_chart_4'] = $postChartData['chsb_report_chart_4'];
		    $data['chsb_report_chart_5'] = $postChartData['chsb_report_chart_5'];
		    $data['chsb_report_chart_6'] = $postChartData['chsb_report_chart_6'];
		    $data['chsb_report_chart_7'] = $postChartData['chsb_report_chart_7'];
		    $data['chsb_report_chart_8'] = $postChartData['chsb_report_chart_8'];
		    // $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports', $data, true);
		    $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports_chart_view', $data, true);
		}
	    } else {
		$carbon_footPrint = 0;
		$total_room_night = 0;
		$water_total_consumption = 0;
		$lpg_value = 0;
		$electricity_value = 0;
		$natural_gas_value = 0;
		$fuel_value = 0;
		$heating_district_value = 0;
		$cooling_district_value = 0;
		$utility_kwh_total = 0;
		$data['pdf_report_title'] = 'YTD Utilities report - ' . date('Y');
		$data['previous_title'] = 'Previous Year - ' . (date('Y') - 1);
		$data['current_title'] = 'Current Year - ' . date('Y');
		$data['budget_title'] = 'Budget - ' . date('Y');
		$data['table_title'] = 'Year To Date Report';
		$data['type'] = 'ytd';
		$data['show_site_details'] = false;
		$show_piechart = true;
		$show_site_details = false;
		$data['showCostBudgetVariance'] = true;
		$data['columnChartImg'] = $postdata['columnChartImg'];
		$data['columnChartImgCarbon'] = $postdata['columnChartImgCarbon'];
		$data['pieChartImg'] = $postdata['pieChartImg'];
		$data['pieChartNewImg'] = $postdata['pieChartNewImg'];
		$data['pieChartNew2Img'] = $postdata['pieChartNew2Img'];
		$data['pieChartNew3Img'] = $postdata['pieChartNew3Img'];
		$data['wasteChartImg'] = $postdata['wasteChartImg'];
		$data['wastePieChartImg'] = $postdata['wastePieChartImg'];
		$data['wasteLandfillPieChartImg'] = $postdata['wasteLandfillPieChartImg'];
		$data['pieChartImgkwhMonthly'] = $postdata['pieChartImgkwhMonthly'];
		$data['pieChartImgcostMonthly'] = $postdata['pieChartImgcostMonthly'];
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
		// Calculation For CHSB report
		$this->load->model('sites/sites_model');
		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$site_detials = $this->sites_model->get_site_detail_custom($site_id);
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
		$this->utilities_model->utilities_year = date("Y") - 2;
		$getUtilityData_minus_two_year = $this->utilities_model->getSiteUtilityLastYear();
		$this->utilities_model->utilities_year = date("Y") - 3;
		$getUtilityData_minus_three_year = $this->utilities_model->getSiteUtilityLastYear();
		foreach ($getUtilityData as $getUtilities) {
		    $site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		    $this->sites_model->year = date("Y") - 1;
		    $site_detials = $this->sites_model->get_site_detail_custom($site_id);
		    $carbon_footPrint += (($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0 * $site_detials['electricity_emission_factor']) + (($getUtilities['total_lpg_cost'] != '') ? $getUtilities['total_lpg_cost'] : 0 * $site_detials['lpg_emission_factor']) + (($getUtilities['total_fuel_oil_cost'] != '') ? $getUtilities['total_fuel_oil_cost'] : 0 * $site_detials['fuel_emission_factor']) + (($getUtilities['district_heating_cost'] != '') ? $getUtilities['district_heating_cost'] : 0 * $site_detials['district_heating_emission_factor']) + (($getUtilities['district_cooling_cost'] != '') ? $getUtilities['district_cooling_cost'] : 0 * $site_detials['district_cooling_emission_factor']);
		    $total_room_night += $getUtilities['total_room_night'];
		    $water_total_consumption += ($getUtilities['water_total_consumption'] != '') ? $getUtilities['water_total_consumption'] : 0;
		    $lpg_value = $getUtilities['total_lpg'] * 13.269;
		    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;
		    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;
		    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;
		    $heating_district_value = $getUtilities['district_heating'] * 1;
		    $cooling_district_value = $getUtilities['district_cooling'] * 1;
		    $utility_kwh_total += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
		}
		foreach ($getUtilityData_prev as $getUtilities) {
		    $site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		    $this->sites_model->year = date("Y") - 2;
		    $site_detials = $this->sites_model->get_site_detail_custom($site_id);
		    $carbon_footPrint += ($getUtilities['total_electricity_kwh'] * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($getUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
		    $total_room_night += $getUtilities['total_room_night'];
		    $water_total_consumption += $getUtilities['water_total_consumption'];
		    $lpg_value = (int) $getUtilities['total_lpg'] * 13.269;
		    $electricity_value = (int) $getUtilities['total_electricity_kwh'] * 1;
		    $natural_gas_value = (int) $getUtilities['total_natural_gas'] * 10.3454063;
		    $fuel_value = (int) $getUtilities['total_fuel_oil'] * 9.95342803564829;
		    $heating_district_value = (int) $getUtilities['district_heating'] * 1;
		    $cooling_district_value = (int) $getUtilities['district_cooling'] * 1;
		    $utility_kwh_total += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
		}
		$data['measures']['HotelCarbonFootprintPerRoom'] = round($carbon_footPrint / $result['rooms_keys'], 2);
		$data['measures']['HotelCarbonFootprintPerOccupiedRoom'] = round($carbon_footPrint / $total_room_night, 2);
		$data['measures']['HotelCarbonFootprintPerSquareMeter'] = round($carbon_footPrint / $result['site_builtup_area'], 2);
		$data['measures']['HotelEnergyUsagePerOccupiedRoom'] = round($utility_kwh_total / $total_room_night, 2);
		$data['measures']['HotelEnergyUsagePerSquareMeter'] = round($utility_kwh_total / $result['site_builtup_area'], 2);
		$data['measures']['HotelWaterUsagePerOccupiedRoom'] = round($water_total_consumption / $total_room_night, 2);
		$data['measures']['HotelWaterUsagePerSquareMeter'] = round($water_total_consumption / $result['site_builtup_area'], 2);
		if ($result['chsb_reporting'] == 1) {
		    $chsb_reporting = $this->load->view('admin_landing_pdf_chsb_reporting_reports', $data, true);
		}
	    }
	}
	$data['site_id'] = $site_id;
	$content_site_detail = $this->load->view('admin_landing_pdf_site_detail', $data, true);
	define('SITE_PDF_HEADER_LOGO', "/assets/uploads/" . $result['site_logo']);
	define('PDF_HEADER_SITE_NAME', $result['site_location_name']);
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetFont('helvetica', '', 9);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetPrintHeader(true);
	$pdf->SetPrintFooter(true);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetMargins(10, 20, 10); // Left,Top,Right
	if ($data['currency'] == "local") {
	    $pdfName = strtolower(str_replace(array(' ', '-'), array('_', ''), $data['pdf_report_title'])) . '_in_local.pdf'; //"site_details.pdf";
	} else {
	    $pdfName = strtolower(str_replace(array(' ', '-'), array('_', ''), $data['pdf_report_title'])) . '_in_base.pdf'; //"site_details.pdf";
	}
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
	    /* if ($postdata['submit'] != 'download_monthly_hidden') {
	      $pdf->AddPage();
	      } */
	    $pdf->AddPage();
	    $pdf->writeHTML($content_reports_carbon_footprint, true, false, true, false, '');
	}
	if ($result['chsb_reporting']) {
	    $pdf->SetFont('helvetica', '', 7);
	    if ($chsb_reporting != '') {
		$pdf->AddPage();
		$pdf->writeHTML($chsb_reporting, true, false, true, false, '');
	    }
	}
	$pdf->Output($pdfName, 'D'); // D - downlaod, F- Save
	exit;
    }

    public function generate_report_excel($data)
    {
	extract($data);
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$this->lang->load('sites/sites', 'english');
	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];
	// $base_type : cost / unit
	// $formtype; : Type of chart
	// $utility_type : Energy Type
	// $report_tmpl : Report template for how to show data
	$postdata = $this->input->post();
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Excel Report")
	    ->setKeywords("Excel Report");
	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	$fullAlphaArray = array_merge(range('A', 'Z'));
	$percentage_decimal = 3;
	$value_decimal = 4;
	$fileName = 'Excel Report';
	if ($postdata['submit'] == 'download_excel_index') {
	    // Add report info in cell
	    // Set header bold
	    $alphaInc = 0;
	    $objPHPExcel->getActiveSheet()->getStyle('A1:Q1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", 'Month');
	    $alphaInc++;
	    if ($totalElectricity > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_electricity_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_electricity_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalFuel > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_fuel_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_fuel_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalLpg > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_lpg_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_lpg_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalNaturalGas > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_natural_gas_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_natural_gas_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalWater > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_water_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_water_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalHeatingDistrict > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_heating_district_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_heating_district_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    if ($totalCoolingDistrict > 0) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_cooling_district_cost_year"), $filters["end_year"]));
		$alphaInc++;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_cooling_district_cost_year"), $filters["end_year"] - 1));
		$alphaInc++;
	    }
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_occupancy_current"), $filters["end_year"], '%'));
	    $alphaInc++;
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}1", sprintf(lang("excel_occupancy_previous"), $filters["end_year"] - 1, '%'));
	    $alphaInc++;
	    $dateIteratorArray = array();
	    if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) {
		// If start and end year is same
		$startmonthsarray = array();
		for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $filters['filters_comparision_chart']["end_month"]; $i++) {
		    $startmonthsarray[] = $i;
		}
		$dateIteratorArray[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
	    } else {
		// If start and end year is not same
		$startmonthsarray = array();
		$endmonthsarray = array();
		for ($i = $filters['filters_comparision_chart']['start_month']; $i <= 12; $i++) {
		    $startmonthsarray[] = $i;
		}
		for ($i = 1; $i <= $filters['filters_comparision_chart']['end_month']; $i++) {
		    $endmonthsarray[] = $i;
		}
		$dateIteratorArray[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
		$dateIteratorArray[$filters['filters_comparision_chart']["end_year"]] = $endmonthsarray;
	    }
	    $j = 1;
	    foreach ($dateIteratorArray as $year => $value) {
		foreach ($value as $key1 => $month) {
		    $j++;
		    $current_electricity_data = (!empty($data['utility_cost_chart'][$month][$year]['electricity'])) ? $data['utility_cost_chart'][$month][$year]['electricity'] : 0;
		    $previous_electricity_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['electricity'])) ? $data['utility_cost_chart'][$month][$year - 1]['electricity'] : 0;
		    $current_fuel_data = (!empty($data['utility_cost_chart'][$month][$year]['fuel'])) ? $data['utility_cost_chart'][$month][$year]['fuel'] : 0;
		    $previous_fuel_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['fuel'])) ? $data['utility_cost_chart'][$month][$year - 1]['fuel'] : 0;
		    $current_lpg_data = (!empty($data['utility_cost_chart'][$month][$year]['lpg'])) ? $data['utility_cost_chart'][$month][$year]['lpg'] : 0;
		    $previous_lpg_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['lpg'])) ? $data['utility_cost_chart'][$month][$year - 1]['lpg'] : 0;
		    $current_natural_gas_data = (!empty($data['utility_cost_chart'][$month][$year]['natural_gas'])) ? $data['utility_cost_chart'][$month][$year]['natural_gas'] : 0;
		    $previous_natural_gas_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['natural_gas'])) ? $data['utility_cost_chart'][$month][$year - 1]['natural_gas'] : 0;
		    $current_water_data = (!empty($data['utility_cost_chart'][$month][$year]['water'])) ? $data['utility_cost_chart'][$month][$year]['water'] : 0;
		    $previous_water_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['water'])) ? $data['utility_cost_chart'][$month][$year - 1]['water'] : 0;
		    $current_heating_district_data = (!empty($data['utility_cost_chart'][$month][$year]['heating_district'])) ? $data['utility_cost_chart'][$month][$year]['heating_district'] : 0;
		    $previous_heating_district_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['heating_district'])) ? $data['utility_cost_chart'][$month][$year - 1]['heating_district'] : 0;
		    $current_cooling_district_data = (!empty($data['utility_cost_chart'][$month][$year]['cooling_district'])) ? $data['utility_cost_chart'][$month][$year]['cooling_district'] : 0;
		    $previous_cooling_district_data = (!empty($data['utility_cost_chart'][$month][$year - 1]['cooling_district'])) ? $data['utility_cost_chart'][$month][$year - 1]['cooling_district'] : 0;
		    $occupancydata = (!empty($data['utility_cost_chart'][$month][$year]['occupancy'])) ? $data['utility_cost_chart'][$month][$year]['occupancy'] : 0;
		    $previousoccupancydata = (!empty($data['utility_cost_chart'][$month][$year - 1]['occupancy'])) ? $data['utility_cost_chart'][$month][$year - 1]['occupancy'] : 0;
		    $alphaInc = 0;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", $fullmontharray[$month]);
		    $alphaInc++;
		    if ($totalElectricity > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_electricity_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_electricity_data, 2));
			$alphaInc++;
		    }
		    if ($totalFuel > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_fuel_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_fuel_data, 2));
			$alphaInc++;
		    }
		    if ($totalLpg > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_lpg_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_lpg_data, 2));
			$alphaInc++;
		    }
		    if ($totalNaturalGas > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_natural_gas_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_natural_gas_data, 2));
			$alphaInc++;
		    }
		    if ($totalWater > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_water_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_water_data, 2));
			$alphaInc++;
		    }
		    if ($totalHeatingDistrict > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_heating_district_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_heating_district_data, 2));
			$alphaInc++;
		    }
		    if ($totalCoolingDistrict > 0) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($current_cooling_district_data, 2));
			$alphaInc++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previous_cooling_district_data, 2));
			$alphaInc++;
		    }
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($occupancydata, 2));
		    $alphaInc++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$fullAlphaArray[$alphaInc]}{$j}", round($previousoccupancydata, 2));
		    $alphaInc++;
		}
	    }
	}
	if ($postdata['submit'] == 'download_excel_index_kwh_pie_chart') {
	    $current_year = $filters['report_year'];
	    $current_month = $filters['max_month_id'];
	    // Set header bold
	    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', lang('energy'));
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', lang('consumption'));
	    $j = 1;
	    foreach ($data['kwh_pie_chart'] as $key => $value) {
		if ($value != 0) {
		    $j++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$j}", lang($key));
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B{$j}", round($value, 2));
		}
	    }
	}
	if ($postdata['submit'] == 'download_excel_index_cost_pie_chart') {
	    // Set header bold
	    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', lang('energy'));
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', lang('excel_cost'));
	    $j = 1;
	    foreach ($data['cost_pie_chart'] as $key => $value) {
		if ($value != 0) {
		    $j++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$j}", lang($key));
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B{$j}", round($value, 2));
		}
	    }
	}
	if ($postdata['submit'] == 'download_excel_index_kwh_pie_chart_previousmonth') {
	    // Set header bold
	    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', lang('energy'));
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', lang('consumption'));
	    $j = 1;
	    foreach ($data['kwh_pie_chart_previousmonth'] as $key => $value) {
		if ($value != 0) {
		    $j++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$j}", lang($key));
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B{$j}", round($value, 2));
		}
	    }
	}
	if ($postdata['submit'] == 'download_excel_index_cost_pie_chart_previousmonth') {
	    $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true)->setSize(12);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', lang('energy'));
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', lang('excel_cost'));
	    $j = 1;
	    foreach ($data['cost_pie_chart_previousmonth'] as $key => $value) {
		if ($value != 0) {
		    $j++;
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A{$j}", lang($key));
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("B{$j}", round($value, 2));
		}
	    }
	}
	if ($postdata['submit'] == 'download') {
	    $data['showCostBudgetVariance'] = true;
	    $fileName = 'ytd_utility_report_' . (date('Y'));
	    $objPHPExcel = $this->report_layout_excel($data, 'ytd');
	}
	if ($postdata['submit'] == 'download_hidden') {
	    $data['showCostBudgetVariance'] = true;
	    $fileName = 'full_year_utility_report_' . (date('Y') - 1);
	    if ($this->input->post('yearly_report_year')) {
		$fileName = 'full_year_utility_report_' . ($this->input->post('yearly_report_year'));
	    }
	    $objPHPExcel = $this->report_layout_excel($data, 'annual');
	}
	if ($postdata['submit'] == 'download_monthly_hidden') {
	    $data['showCostBudgetVariance'] = true;
	    $fileName = 'monthly_utility_report_' . ($fullmontharray[$data['filters']["filters_comparision_chart"]['start_month']]) . '_' . ($data['filters']["filters_comparision_chart"]['start_year']);
	    $objPHPExcel = $this->report_layout_excel($data, 'monthly');
	}
	if ($data['currency'] == "base") {
	    $fileName .= "_in_base";
	} else {
	    $fileName .= "_in_local";
	}
	ob_end_clean();
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $fileName . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
    }

    public function report_calculations($data, $report_type)
    {
	extract($data);
	$percentage_decimal = 3;
	$value_decimal = 4;
	$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	if ($report_type == 'ytd') {
	    //table titles
	    $return['pdf_report_title'] = 'YTD Utilities report - ' . date('Y');
	    $return['previous_title'] = 'Previous Year - ' . (date('Y') - 1);
	    $return['current_title'] = 'Current Year - ' . date('Y');
	    $return['budget_title'] = 'Budget - ' . date('Y');
	    $return['table_title'] = 'Year To Date Report';
	    $return['showCostBudgetVariance'] = true;
	    //Bar chart show last year data
	    $current_year = date('Y');
	    $last_year = $current_year - 1;
	    if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) {
		// If start and end year is same
		for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $CURRENT_YEAR_MAX_MONTH_ID; $i++) {
		    $startmonthsarray[] = $i;
		}
		$resultkeys = array();
		$resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
	    } else {
		// If start and end year is not same
		for ($i = $filters['filters_comparision_chart']['start_month']; $i <= 12; $i++) {
		    $startmonthsarray[] = $i;
		}
		for ($i = 1; $i <= $filters['filters_comparision_chart']['end_month']; $i++) {
		    $endmonthsarray[] = $i;
		}
		$resultkeys = array();
		$resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
		$resultkeys[$filters['filters_comparision_chart']["end_year"]] = $endmonthsarray;
	    }
	} elseif ($report_type == 'monthly') {
	    //table titles
	    $return['pdf_report_title'] = 'Monthly Utilities report - ' . ($fullmontharray[$filters["filters_comparision_chart"]['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
	    $return['previous_title'] = '' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year'] - 1);
	    $return['current_title'] = '' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
	    $return['budget_title'] = 'Budget - ' . ($montharray[$filters['filters_comparision_chart']['start_month']]) . ' ' . ($filters["filters_comparision_chart"]['start_year']);
	    $return['table_title'] = 'Monthly Report';
	    $return['showCostBudgetVariance'] = true;
	    //Bar chart show last year data
	    $resultkeys = array();
	    $resultkeys[$filters['filters_comparision_chart']['start_year']] = array($filters['filters_comparision_chart']['start_month']);
	} elseif ($report_type == 'annual') {
	    //table titles
	    $return['pdf_report_title'] = 'Full Year Utilities Report - ' . (date('Y') - 1);
	    $return['previous_title'] = 'Previous Year - ' . (date('Y') - 1);
	    $return['current_title'] = 'Current Year - ' . (date('Y') - 1);
	    $return['budget_title'] = 'Budget - ' . (date('Y') - 1);
	    if ($this->input->post('yearly_report_year')) {
		$return['pdf_report_title'] = 'Full Year Utilities Report - ' . ($this->input->post('yearly_report_year'));
		$return['previous_title'] = 'Previous Year - ' . ($this->input->post('yearly_report_year') - 1);
		$return['current_title'] = 'Current Year - ' . ($this->input->post('yearly_report_year'));
		$return['budget_title'] = 'Budget - ' . ($this->input->post('yearly_report_year'));
	    }
	    $return['table_title'] = 'Annual Report';
	    $return['showCostBudgetVariance'] = true;
	    //Bar chart show last year data
	    $current_year = date('Y');
	    $last_year = $current_year - 1;
	    if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) {
		// If start and end year is same
		for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']['end_month']; $i++) {
		    $startmonthsarray[] = $i;
		}
		$resultkeys = array();
		$resultkeys[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray;
	    } else {
		// If start and end year is not same
		for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= 12; $i++) {
		    $startmonthsarray[] = $i;
		}
		for ($i = 1; $i <= $filters['filters_comparision_chart_pre']['end_month']; $i++) {
		    $endmonthsarray[] = $i;
		}
		$resultkeys = array();
		$resultkeys[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray;
		$resultkeys[$filters['filters_comparision_chart_pre']["end_year"]] = $endmonthsarray;
	    }
	    $utility_cost_chart = $utility_cost_chart_pre;
	}
	if (!empty($utility_cost_chart)) {
	    $ci = get_instance();
	    $total_months = 0;
	    foreach ($resultkeys as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Previous year data
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['electricity'])) ? $utility_cost_chart[$month][$year - 1]['electricity'] : 0;
		    $pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;
		    $pre_data_fuel_consumption = (!empty($utility_cost_chart[$month][$year - 1]['fuel_consumption'])) ? $utility_cost_chart[$month][$year - 1]['fuel_consumption'] : 0;
		    $pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;
		    $pre_data_lpg_consumption = (!empty($utility_cost_chart[$month][$year - 1]['lpg_consumption'])) ? $utility_cost_chart[$month][$year - 1]['lpg_consumption'] : 0;
		    $pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas'] : 0;
		    $pre_data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] : 0;
		    $pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;
		    $pre_data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['heating_district_consumption'] : 0;
		    $pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;
		    $pre_data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district_consumption'] : 0;
		    $pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;
		    $pre_data_water_consumption = (!empty($utility_cost_chart[$month][$year - 1]['water_consumption'])) ? $utility_cost_chart[$month][$year - 1]['water_consumption'] : 0;
		    $pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;
		    $pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;
		    $pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;
		    $pre_data_room_night = (!empty($utility_cost_chart[$month][$year - 1]['room_night'])) ? $utility_cost_chart[$month][$year - 1]['room_night'] : 0;
		    $pre_data_electricity_tariff = (!empty($utility_cost_chart[$month][$year - 1]['electricity_tariff'])) ? $utility_cost_chart[$month][$year - 1]['electricity_tariff'] : 0;
		    $pre_data_electricity_kwh = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] : 0;
		    // Current year data
		    $monthdata = $montharray[$month] . ' ' . $year;
		    $data_electricity = (!empty($utility_cost_chart[$month][$year]['electricity'])) ? $utility_cost_chart[$month][$year]['electricity'] : 0;
		    $data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;
		    $data_fuel_consumption = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? $utility_cost_chart[$month][$year]['fuel_consumption'] : 0;
		    $data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;
		    $data_lpg_consumption = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas'])) ? $utility_cost_chart[$month][$year]['natural_gas'] : 0;
		    $data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;
		    $data_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district'])) ? $utility_cost_chart[$month][$year]['heating_district'] : 0;
		    $data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year]['heating_district_consumption'] : 0;
		    $data_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district'])) ? $utility_cost_chart[$month][$year]['cooling_district'] : 0;
		    $data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year]['cooling_district_consumption'] : 0;
		    $data_water = (!empty($utility_cost_chart[$month][$year]['water'])) ? $utility_cost_chart[$month][$year]['water'] : 0;
		    $data_water_consumption = (!empty($utility_cost_chart[$month][$year]['water_consumption'])) ? $utility_cost_chart[$month][$year]['water_consumption'] : 0;
		    $data_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;
		    $data_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;
		    $data_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;
		    $data_room_night = (!empty($utility_cost_chart[$month][$year]['room_night'])) ? $utility_cost_chart[$month][$year]['room_night'] : 0;
		    $data_electricity_tariff = (!empty($utility_cost_chart[$month][$year]['electricity_tariff'])) ? $utility_cost_chart[$month][$year]['electricity_tariff'] : 0;
		    $data_electricity_kwh = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year]['total_electricity_kwh'] : 0;
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
		    // Total sum Previous year data
		    $return["total_sum_pre_data_electricity"] += $pre_data_electricity;
		    $return["total_sum_pre_data_fuel"] += $pre_data_fuel;
		    $return["total_sum_pre_data_fuel_consumption"] += $pre_data_fuel_consumption;
		    $return["total_sum_pre_data_lpg"] += $pre_data_lpg;
		    $return["total_sum_pre_data_lpg_consumption"] += $pre_data_lpg_consumption;
		    $return["total_sum_pre_data_natural_gas"] += $pre_data_natural_gas;
		    $return["total_sum_pre_data_natural_gas_consumption"] += $pre_data_natural_gas_consumption;
		    $return["total_sum_pre_data_heating_district"] += $pre_data_heating_district;
		    $return["total_sum_pre_data_heating_district_consumption"] += $pre_data_heating_district_consumption;
		    $return["total_sum_pre_data_cooling_district"] += $pre_data_cooling_district;
		    $return["total_sum_pre_data_cooling_district_consumption"] += $pre_data_cooling_district_consumption;
		    $return["total_sum_pre_data_water"] += $pre_data_water;
		    $return["total_sum_pre_data_water_consumption"] += $pre_data_water_consumption;
		    $return["total_sum_pre_data_cdd"] += $pre_data_cdd;
		    $return["total_sum_pre_data_hdd"] += $pre_data_hdd;
		    $return["total_sum_pre_data_occupancy"] += $pre_data_occupancy;
		    $return["total_sum_pre_data_room_night"] += $pre_data_room_night;
		    //$return["total_sum_pre_data_electricity_tariff"] += $pre_data_electricity_tariff;
		    $return["total_sum_pre_data_electricity_kwh"] += $pre_data_electricity_kwh;
		    // Total sum Current year data
		    $return["total_sum_data_electricity"] += $data_electricity;
		    $return["total_sum_data_fuel"] += $data_fuel;
		    $return["total_sum_data_fuel_consumption"] += $data_fuel_consumption;
		    $return["total_sum_data_lpg"] += $data_lpg;
		    $return["total_sum_data_lpg_consumption"] += $data_lpg_consumption;
		    $return["total_sum_data_natural_gas"] += $data_natural_gas;
		    $return["total_sum_data_natural_gas_consumption"] += $data_natural_gas_consumption;
		    $return["total_sum_data_heating_district"] += $data_heating_district;
		    $return["total_sum_data_heating_district_consumption"] += $data_heating_district_consumption;
		    $return["total_sum_data_cooling_district"] += $data_cooling_district;
		    $return["total_sum_data_cooling_district_consumption"] += $data_cooling_district_consumption;
		    $return["total_sum_data_water"] += $data_water;
		    $return["total_sum_data_water_consumption"] += $data_water_consumption;
		    $return["total_sum_data_cdd"] += $data_cdd;
		    $return["total_sum_data_hdd"] += $data_hdd;
		    $return["total_sum_data_occupancy"] += $data_occupancy;
		    $return["total_sum_data_room_night"] += $data_room_night;
		    //$return["total_sum_data_electricity_tariff"] += $data_electricity_tariff;
		    $return["total_sum_data_electricity_kwh"] += $data_electricity_kwh;
		    $total_months++;
		}
	    }
	    if ($return["total_sum_pre_data_electricity_kwh"] > 0) {
		$return["total_sum_pre_data_electricity_tariff"] = ($return["total_sum_pre_data_electricity"] / $return["total_sum_pre_data_electricity_kwh"]);
	    } else {
		$return["total_sum_pre_data_electricity_tariff"] = 0;
	    }
	    if ($return["total_sum_data_electricity_kwh"] > 0) {
		$return["total_sum_data_electricity_tariff"] = ($return["total_sum_data_electricity"] / $return["total_sum_data_electricity_kwh"]);
	    } else {
		$return["total_sum_data_electricity_tariff"] = 0;
	    }
	    $return["total_sum_pre_data_sum"] = ($return["total_sum_pre_data_electricity"] + $return["total_sum_pre_data_fuel"] + $return["total_sum_pre_data_lpg"] + $treturn["otal_sum_pre_data_natural_gas"] + $return["total_sum_pre_data_water"] + $return["total_sum_pre_data_heating_district"] + $return["total_sum_pre_data_cooling_district"]);
	    $return["total_sum_data_sum"] = ($return["total_sum_data_electricity"] + $return["total_sum_data_fuel"] + $return["total_sum_data_lpg"] + $return["total_sum_data_natural_gas"] + $return["total_sum_data_water"] + $return["total_sum_data_heating_district"] + $return["total_sum_data_cooling_district"]);
	    $return["total_sum_difference_value"] = $return["total_sum_data_sum"] - $return["total_sum_pre_data_sum"];
	    if (!empty($return["total_sum_pre_data_sum"])) {
		$return["total_sum_difference_percent"] = round($return["total_sum_difference_value"] * 100 / $return["total_sum_pre_data_sum"], $percentage_decimal);
	    } else {
		$return["total_sum_difference_percent"] = 0;
	    }
	    if (!empty($return["total_sum_data_fuel"])) {
		$return["total_sum_data_fuel_tariff"] = round($return["total_sum_data_fuel"] / $return["total_sum_data_fuel_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_fuel_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_fuel"])) {
		$return["total_sum_pre_data_fuel_tariff"] = round($return["total_sum_pre_data_fuel"] / $return["total_sum_pre_data_fuel_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_fuel_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_data_lpg"])) {
		$return["total_sum_data_lpg_tariff"] = round($return["total_sum_data_lpg"] / $return["total_sum_data_lpg_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_lpg_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_lpg"])) {
		$return["total_sum_pre_data_lpg_tariff"] = round($return["total_sum_pre_data_lpg"] / $return["total_sum_pre_data_lpg_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_lpg_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_data_natural_gas"])) {
		$return["total_sum_data_natural_gas_tariff"] = round($return["total_sum_data_natural_gas"] / $return["total_sum_data_natural_gas_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_natural_gas_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_natural_gas"])) {
		$return["total_sum_pre_data_natural_gas_tariff"] = round($total_sum_pre_data_natural_gas / $return["total_sum_pre_data_natural_gas_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_natural_gas_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_data_heating_district"])) {
		$return["total_sum_data_heating_district_tariff"] = round($return["total_sum_data_heating_district"] / $return["total_sum_data_heating_district_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_heating_district_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_heating_district"])) {
		$return["total_sum_pre_data_heating_district_tariff"] = round($return["total_sum_pre_data_heating_district"] / $return["total_sum_pre_data_heating_district_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_heating_district_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_data_cooling_district"])) {
		$return["total_sum_data_cooling_district_tariff"] = round($return["total_sum_data_cooling_district"] / $return["total_sum_data_cooling_district_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_cooling_district_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_cooling_district"])) {
		$return["total_sum_pre_data_cooling_district_tariff"] = round($return["total_sum_pre_data_cooling_district"] / $return["total_sum_pre_data_cooling_district_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_cooling_district_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_data_water"])) {
		$return["total_sum_data_water_tariff"] = round($return["total_sum_data_water"] / $return["total_sum_data_water_consumption"], $value_decimal);
	    } else {
		$return["total_sum_data_water_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_water"])) {
		$return["total_sum_pre_data_water_tariff"] = round($return["total_sum_pre_data_water"] / $return["total_sum_pre_data_water_consumption"], $value_decimal);
	    } else {
		$return["total_sum_pre_data_water_tariff"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_room_night"])) {
		$return["total_sum_pre_data_fuel_per_room_night"] = round($return["total_sum_pre_data_fuel_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_lpg_per_room_night"] = round($return["total_sum_pre_data_lpg_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_natural_gas_per_room_night"] = round($return["total_sum_pre_data_natural_gas_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_heating_district_per_room_night"] = round($return["total_sum_pre_data_heating_district_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_cooling_district_per_room_night"] = round($return["total_sum_pre_data_cooling_district_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_water_per_room_night"] = round($return["total_sum_pre_data_water_consumption"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
		$return["total_sum_pre_data_electricity_per_room_night"] = round($return["total_sum_pre_data_electricity_kwh"] / $return["total_sum_pre_data_room_night"], $percentage_decimal);
	    } else {
		$return["total_sum_pre_data_fuel_per_room_night"] = 0;
		$return["total_sum_pre_data_lpg_per_room_night"] = 0;
		$return["total_sum_pre_data_natural_gas_per_room_night"] = 0;
		$return["total_sum_pre_data_heating_district_per_room_night"] = 0;
		$return["total_sum_pre_data_cooling_district_per_room_night"] = 0;
		$return["total_sum_pre_data_water_per_room_night"] = 0;
		$return["total_sum_pre_data_electricity_per_room_night"] = 0;
	    }
	    if (!empty($return["total_sum_data_room_night"])) {
		$return["total_sum_data_fuel_per_room_night"] = round($return["total_sum_data_fuel_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_lpg_per_room_night"] = round($return["total_sum_data_lpg_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_natural_gas_per_room_night"] = round($return["total_sum_data_natural_gas_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_heating_district_per_room_night"] = round($return["total_sum_data_heating_district_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_cooling_district_per_room_night"] = round($return["total_sum_data_cooling_district_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_water_per_room_night"] = round($return["total_sum_data_water_consumption"] / $return["total_sum_data_room_night"], $percentage_decimal);
		$return["total_sum_data_electricity_per_room_night"] = round($return["total_sum_data_electricity_kwh"] / $return["total_sum_data_room_night"], $percentage_decimal);
	    } else {
		$return["total_sum_data_fuel_per_room_night"] = 0;
		$return["total_sum_data_lpg_per_room_night"] = 0;
		$return["total_sum_data_natural_gas_per_room_night"] = 0;
		$return["total_sum_data_heating_district_per_room_night"] = 0;
		$return["total_sum_data_cooling_district_per_room_night"] = 0;
		$return["total_sum_data_water_per_room_night"] = 0;
		$return["total_sum_data_electricity_per_room_night"] = 0;
	    }
	    $return["fuel_per_room_night_difference_value"] = $return["total_sum_data_fuel_per_room_night"] - $return["total_sum_pre_data_fuel_per_room_night"];
	    if (!empty($return["total_sum_pre_data_fuel_per_room_night"])) {
		$return["fuel_per_room_night_difference_percent"] = round($return["fuel_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_fuel_per_room_night"], $percentage_decimal);
	    } else {
		$return["fuel_per_room_night_difference_percent"] = 0;
	    }
	    $return["lpg_per_room_night_difference_value"] = $return["total_sum_data_lpg_per_room_night"] - $return["total_sum_pre_data_lpg_per_room_night"];
	    if (!empty($return["total_sum_pre_data_lpg_per_room_night"])) {
		$return["lpg_per_room_night_difference_percent"] = round($return["lpg_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_lpg_per_room_night"], $percentage_decimal);
	    } else {
		$return["lpg_per_room_night_difference_percent"] = 0;
	    }
	    $return["natural_gas_per_room_night_difference_value"] = $return["total_sum_data_natural_gas_per_room_night"] - $return["total_sum_pre_data_natural_gas_per_room_night"];
	    if (!empty($return["total_sum_pre_data_natural_gas_per_room_night"])) {
		$return["natural_gas_per_room_night_difference_percent"] = round($return["natural_gas_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_natural_gas_per_room_night"], $percentage_decimal);
	    } else {
		$return["natural_gas_per_room_night_difference_percent"] = 0;
	    }
	    $return["heating_district_per_room_night_difference_value"] = $return["total_sum_data_heating_district_per_room_night"] - $return["total_sum_pre_data_heating_district_per_room_night"];
	    if (!empty($return["total_sum_pre_data_heating_district_per_room_night"])) {
		$return["heating_district_per_room_night_difference_percent"] = round($return["heating_district_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_heating_district_per_room_night"], $percentage_decimal);
	    } else {
		$return["heating_district_per_room_night_difference_percent"] = 0;
	    }
	    $return["cooling_district_per_room_night_difference_value"] = $return["total_sum_data_cooling_district_per_room_night"] - $return["total_sum_pre_data_cooling_district_per_room_night"];
	    if (!empty($return["total_sum_pre_data_cooling_district_per_room_night"])) {
		$return["cooling_district_per_room_night_difference_percent"] = round($return["cooling_district_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_cooling_district_per_room_night"], $percentage_decimal);
	    } else {
		$return["cooling_district_per_room_night_difference_percent"] = 0;
	    }
	    $return["water_per_room_night_difference_value"] = $return["total_sum_data_water_per_room_night"] - $return["total_sum_pre_data_water_per_room_night"];
	    if (!empty($return["total_sum_pre_data_water_per_room_night"])) {
		$return["water_per_room_night_difference_percent"] = round($return["water_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_water_per_room_night"], $percentage_decimal);
	    } else {
		$return["water_per_room_night_difference_percent"] = 0;
	    }
	    $return["electricity_per_room_night_difference_value"] = $return["total_sum_data_electricity_per_room_night"] - $return["total_sum_pre_data_electricity_per_room_night"];
	    if (!empty($return["total_sum_pre_data_electricity_per_room_night"])) {
		$return["electricity_per_room_night_difference_percent"] = round($return["electricity_per_room_night_difference_value"] * 100 / $return["total_sum_pre_data_electricity_per_room_night"], $percentage_decimal);
	    } else {
		$return["electricity_per_room_night_difference_percent"] = 0;
	    }
	    // Variation data
	    $return["electricity_consumption_difference"] = $return["total_sum_data_electricity_kwh"] - $return["total_sum_pre_data_electricity_kwh"];
	    $return["fuel_consumption_difference"] = $return["total_sum_data_fuel_consumption"] - $return["total_sum_pre_data_fuel_consumption"];
	    $return["lpg_consumption_difference"] = $return["total_sum_data_lpg_consumption"] - $return["total_sum_pre_data_lpg_consumption"];
	    $return["natural_gas_consumption_difference"] = $return["total_sum_data_natural_gas_consumption"] - $return["total_sum_pre_data_natural_gas_consumption"];
	    $return["heating_district_consumption_difference"] = $return["total_sum_data_heating_district_consumption"] - $return["total_sum_pre_data_heating_district_consumption"];
	    $return["cooling_district_consumption_difference"] = $return["total_sum_data_cooling_district_consumption"] - $return["total_sum_pre_data_cooling_district_consumption"];
	    $return["water_consumption_difference"] = $return["total_sum_data_water_consumption"] - $return["total_sum_pre_data_water_consumption"];
	    if (!empty($return["total_sum_pre_data_electricity_kwh"])) {
		$return["electricity_consumption_variation"] = round($return["electricity_consumption_difference"] * 100 / $return["total_sum_pre_data_electricity_kwh"], $percentage_decimal);
	    } else {
		$return["electricity_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_fuel_consumption"])) {
		$return["fuel_consumption_variation"] = round($return["fuel_consumption_difference"] * 100 / $return["total_sum_pre_data_fuel_consumption"], $percentage_decimal);
	    } else {
		$return["fuel_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_lpg_consumption"])) {
		$return["lpg_consumption_variation"] = round($return["lpg_consumption_difference"] * 100 / $return["total_sum_pre_data_lpg_consumption"], $percentage_decimal);
	    } else {
		$return["lpg_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_natural_gas_consumption"])) {
		$return["natural_gas_consumption_variation"] = round($return["natural_gas_consumption_difference"] * 100 / $return["total_sum_pre_data_natural_gas_consumption"], $percentage_decimal);
	    } else {
		$return["natural_gas_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_heating_district_consumption"])) {
		$return["heating_district_consumption_variation"] = round($return["heating_district_consumption_difference"] * 100 / $return["total_sum_pre_data_heating_district_consumption"], $percentage_decimal);
	    } else {
		$return["heating_district_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_cooling_district_consumption"])) {
		$return["cooling_district_consumption_variation"] = round($return["cooling_district_consumption_difference"] * 100 / $return["total_sum_pre_data_cooling_district_consumption"], $percentage_decimal);
	    } else {
		$return["cooling_district_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_water_consumption"])) {
		$return["water_consumption_variation"] = round($return["water_consumption_difference"] * 100 / $return["total_sum_pre_data_water_consumption"], $percentage_decimal);
	    } else {
		$return["water_consumption_variation"] = 0;
	    }
	    if (!empty($return["total_sum_pre_data_electricity"]) && $return["total_sum_pre_data_electricity"] > 0) {
		$return["total_sum_data_electricity_variation"] = round(((($return["total_sum_data_electricity"] - $return["total_sum_pre_data_electricity"]) * 100) / $return["total_sum_pre_data_electricity"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_electricity"] == 0) {
		    $return["total_sum_data_electricity_variation"] = 0;
		} else {
		    $return["total_sum_data_electricity_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_fuel"]) && $return["total_sum_pre_data_fuel"] > 0) {
		$return["total_sum_data_fuel_variation"] = round(((($return["total_sum_data_fuel"] - $return["total_sum_pre_data_fuel"]) * 100) / $return["total_sum_pre_data_fuel"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_fuel"] == 0) {
		    $return["total_sum_data_fuel_variation"] = 0;
		} else {
		    $return["total_sum_data_fuel_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_lpg"]) && $return["total_sum_pre_data_lpg"] > 0) {
		$return["total_sum_data_lpg_variation"] = round(((($return["total_sum_data_lpg"] - $return["total_sum_pre_data_lpg"]) * 100) / $return["total_sum_pre_data_lpg"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_lpg"] == 0) {
		    $return["total_sum_data_lpg_variation"] = 0;
		} else {
		    $return["total_sum_data_lpg_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_natural_gas"]) && $return["total_sum_pre_data_natural_gas"] > 0) {
		$return["total_sum_data_natural_gas_variation"] = round(((($return["total_sum_data_natural_gas"] - $return["total_sum_pre_data_natural_gas"]) * 100) / $return["total_sum_pre_data_natural_gas"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_natural_gas"] == 0) {
		    $return["total_sum_data_natural_gas_variation"] = 0;
		} else {
		    $return["total_sum_data_natural_gas_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_heating_district"]) && $return["total_sum_pre_data_heating_district"] > 0) {
		$return["total_sum_data_heating_district_variation"] = round(((($return["total_sum_data_heating_district"] - $return["total_sum_pre_data_heating_district"]) * 100) / $return["total_sum_pre_data_heating_district"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_heating_district"] == 0) {
		    $return["total_sum_data_heating_district_variation"] = 0;
		} else {
		    $return["total_sum_data_heating_district_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_cooling_district"]) && $return["total_sum_pre_data_cooling_district"] > 0) {
		$return["total_sum_data_cooling_district_variation"] = round(((($return["total_sum_data_cooling_district"] - $return["total_sum_pre_data_cooling_district"]) * 100) / $return["total_sum_pre_data_cooling_district"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_cooling_district"] == 0) {
		    $return["total_sum_data_cooling_district_variation"] = 0;
		} else {
		    $return["total_sum_data_cooling_district_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_water"]) && $return["total_sum_pre_data_water"] > 0) {
		$return["total_sum_data_water_variation"] = round(((($return["total_sum_data_water"] - $return["total_sum_pre_data_water"]) * 100) / $return["total_sum_pre_data_water"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_water"] == 0) {
		    $return["total_sum_data_water_variation"] = 0;
		} else {
		    $return["total_sum_data_ater_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_cdd"]) && $return["total_sum_pre_data_cdd"] > 0) {
		$return["total_sum_data_cdd_variation"] = round(((($return["total_sum_data_cdd"] - $return["total_sum_pre_data_cdd"]) * 100) / $return["total_sum_pre_data_cdd"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_cdd"] == 0) {
		    $return["total_sum_data_cdd_variation"] = 0;
		} else {
		    $return["total_sum_data_cdd_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_hdd"]) && $return["total_sum_pre_data_hdd"] > 0) {
		$return["total_sum_data_hdd_variation"] = round(((($return["total_sum_data_hdd"] - $return["total_sum_pre_data_hdd"]) * 100) / $return["total_sum_pre_data_hdd"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_hdd"] == 0) {
		    $return["total_sum_data_hdd_variation"] = 0;
		} else {
		    $return["total_sum_data_hdd_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_occupancy"]) && $return["total_sum_pre_data_occupancy"] > 0) {
		$return["total_sum_data_occupancy_variation"] = round(((($return["total_sum_data_occupancy"] - $return["total_sum_pre_data_occupancy"]) * 100) / $return["total_sum_pre_data_occupancy"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_occupancy"] == 0) {
		    $return["total_sum_data_occupancy_variation"] = 0;
		} else {
		    $return["total_sum_data_occupancy_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_room_night"]) && $return["total_sum_pre_data_room_night"] > 0) {
		$return["total_sum_data_room_night_variation"] = round(((($return["total_sum_data_room_night"] - $return["total_sum_pre_data_room_night"]) * 100) / $return["total_sum_pre_data_room_night"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_room_night"] == 0) {
		    $return["total_sum_data_room_night_variation"] = 0;
		} else {
		    $return["total_sum_data_room_night_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_electricity_tariff"]) && $return["total_sum_pre_data_electricity_tariff"] > 0) {
		$return["total_sum_data_electricity_tariff_variation"] = round(((($return["total_sum_data_electricity_tariff"] - $return["total_sum_pre_data_electricity_tariff"]) * 100) / $return["total_sum_pre_data_electricity_tariff"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_electricity_tariff"] == 0) {
		    $return["total_sum_data_electricity_tariff_variation"] = 0;
		} else {
		    $return["total_sum_data_electricity_tariff_variation"] = 100;
		}
	    }
	    // utility variation
	    $return["fuel_difference"] = $return["total_sum_data_fuel_tariff"] - $return["total_sum_pre_data_fuel_tariff"];
	    if (!empty($return["total_sum_pre_data_fuel_tariff"])) {
		$return["fuel_variation"] = round($return["fuel_difference"] * 100 / $return["total_sum_pre_data_fuel_tariff"], $percentage_decimal);
	    } else {
		$return["fuel_variation"] = 0;
	    }
	    $return["lpg_difference"] = $return["total_sum_data_lpg_tariff"] - $return["total_sum_pre_data_lpg_tariff"];
	    if (!empty($return["total_sum_pre_data_lpg_tariff"])) {
		$return["lpg_variation"] = round($return["lpg_difference"] * 100 / $return["total_sum_pre_data_lpg_tariff"], $percentage_decimal);
	    } else {
		$return["lpg_variation"] = 0;
	    }
	    $return["natural_gas_difference"] = $return["total_sum_data_natural_gas_tariff"] - $return["total_sum_pre_data_natural_gas_tariff"];
	    if (!empty($return["total_sum_pre_data_natural_gas_tariff"])) {
		$return["natural_gas_variation"] = round($return["natural_gas_difference"] * 100 / $return["total_sum_pre_data_natural_gas_tariff"], $percentage_decimal);
	    } else {
		$return["natural_gas_variation"] = 0;
	    }
	    $return["heating_district_difference"] = $return["total_sum_data_heating_district_tariff"] - $return["total_sum_pre_data_heating_district_tariff"];
	    if (!empty($return["total_sum_pre_data_heating_district_tariff"])) {
		$return["heating_district_variation"] = round($return["heating_district_difference"] * 100 / $return["total_sum_pre_data_heating_district_tariff"], $percentage_decimal);
	    } else {
		$return["heating_district_variation"] = 0;
	    }
	    $return["cooling_district_difference"] = $return["total_sum_data_cooling_district_tariff"] - $return["total_sum_pre_data_cooling_district_tariff"];
	    if (!empty($return["total_sum_pre_data_cooling_district_tariff"])) {
		$return["cooling_district_variation"] = round($return["cooling_district_difference"] * 100 / $return["total_sum_pre_data_cooling_district_tariff"], $percentage_decimal);
	    } else {
		$return["cooling_district_variation"] = 0;
	    }
	    $return["water_difference"] = $return["total_sum_data_water_tariff"] - $return["total_sum_pre_data_water_tariff"];
	    if (!empty($return["total_sum_pre_data_water_tariff"])) {
		$return["water_variation"] = round($return["water_difference"] * 100 / $return["total_sum_pre_data_water_tariff"], $percentage_decimal);
	    } else {
		$return["water_variation"] = 0;
	    }
	    // Total variation
	    if (!empty($return["total_sum_pre_data_sum"]) && $return["total_sum_pre_data_sum"] > 0) {
		$return["total_sum_data_variation"] = round(((($return["total_sum_data_sum"] - $return["total_sum_pre_data_sum"]) * 100) / $return["total_sum_pre_data_sum"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_sum"] == 0) {
		    $return["total_sum_data_variation"] = 0;
		} else {
		    $return["total_sum_data_variation"] = 100;
		}
	    }
	    //Cost @ LY Tariff calculation (utility_difference * previous_year_tariff);
	    $return["fuel_oil_ly"] = round($return["fuel_consumption_difference"] * $return["total_sum_pre_data_fuel_tariff"], $percentage_decimal);
	    $return["lpg_ly"] = round($return["lpg_consumption_difference"] * $return["total_sum_pre_data_lpg_tariff"], $percentage_decimal);
	    $return["natural_gas_ly"] = round($return["natural_gas_consumption_difference"] * $return["total_sum_pre_data_natural_gas_tariff"], $percentage_decimal);
	    $return["heating_district_ly"] = round($return["heating_district_consumption_difference"] * $return["total_sum_pre_data_heating_district_tariff"], $percentage_decimal);
	    $return["cooling_district_ly"] = round($return["cooling_district_consumption_difference"] * $return["total_sum_pre_data_cooling_district_tariff"], $percentage_decimal);
	    $return["water_ly"] = round($return["water_consumption_difference"] * $return["total_sum_pre_data_water_tariff"], $percentage_decimal);
	    $return["electricity_ly"] = round($return["electricity_consumption_difference"] * $return["total_sum_pre_data_electricity_tariff"], $percentage_decimal);
	    $return["total_cost_ly"] = $return["fuel_oil_ly"] + $return["lpg_ly"] + $natural_gas_ly + $return["heating_district_ly"] + $return["cooling_district_ly"] + $return["water_ly"] + $return["electricity_ly"];
	    // Calculate utitlity per room night and builtup area
	    $return["total_sum_pre_data_electricity_kwh_per_roomnight"] = ($return["total_sum_pre_data_electricity_kwh"] / $return["total_sum_pre_data_room_night"]);
	    $return["total_sum_pre_data_electricity_kwh_per_m2"] = ($return["total_sum_pre_data_electricity_kwh"] / $site_detail['site_builtup_area']);
	    $return["total_sum_pre_data_water_liter_per_roomnight"] = ($return["total_sum_pre_data_water"] / $return["total_sum_pre_data_room_night"]);
	    $return["total_sum_pre_data_utility_cost_per_roomnight"] = ($return["total_sum_pre_data_room_night"] != '') ? round($return["total_sum_pre_data_sum"] / $return["total_sum_pre_data_room_night"], $value_decimal) : 0;
	    $return["total_sum_pre_data_utility_cost_per_m2"] = round($return["total_sum_pre_data_sum"] / $site_detail['site_builtup_area'], $value_decimal);
	    $return["total_sum_data_electricity_kwh_per_roomnight"] = ($return["total_sum_data_electricity_kwh"] / $return["total_sum_data_room_night"]);
	    $return["total_sum_data_electricity_kwh_per_m2"] = ($return["total_sum_data_electricity_kwh"] / $site_detail['site_builtup_area']);
	    $return["total_sum_data_water_liter_per_roomnight"] = ($return["total_sum_data_water"] / $return["total_sum_data_room_night"]);
	    $return["total_sum_data_utility_cost_per_roomnight"] = ($return["total_sum_data_room_night"] != '') ? round($return["total_sum_data_sum"] / $return["total_sum_data_room_night"], $value_decimal) : 0;
	    $return["total_sum_data_utility_cost_per_m2"] = round($return["total_sum_data_sum"] / $site_detail['site_builtup_area'], $value_decimal);
	    $return["data_utility_cost_per_roomnight_difference"] = floatval($return["total_sum_data_utility_cost_per_roomnight"] - $return["total_sum_pre_data_utility_cost_per_roomnight"]);
	    $return["data_utility_cost_per_roomnight_variation"] = ($return["total_sum_data_utility_cost_per_roomnight"] != 0) ? round($return["data_utility_cost_per_roomnight_difference"] * 100 / $return["total_sum_data_utility_cost_per_roomnight"], $percentage_decimal) : 0;
	    $return["data_utility_cost_per_m2_difference"] = $return["total_sum_data_utility_cost_per_m2"] - $return["total_sum_pre_data_utility_cost_per_m2"];
	    $return["data_utility_cost_per_m2_variation"] = ($return["total_sum_data_utility_cost_per_m2"] != 0) ? round($return["data_utility_cost_per_m2_difference"] * 100 / $return["total_sum_data_utility_cost_per_m2"], $percentage_decimal) : 0;
	    if (!empty($return["total_sum_pre_data_electricity_kwh_per_roomnight"]) && $return["total_sum_pre_data_electricity_kwh_per_roomnight"] > 0) {
		$return["total_sum_data_electricity_kwh_per_roomnight_variation"] = round(((($return["total_sum_data_electricity_kwh_per_roomnight"] - $return["total_sum_pre_data_electricity_kwh_per_roomnight"]) * 100) / $return["total_sum_pre_data_electricity_kwh_per_roomnight"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_electricity_kwh_per_roomnight"] == 0) {
		    $return["total_sum_data_electricity_kwh_per_roomnight_variation"] = 0;
		} else {
		    $return["total_sum_data_electricity_kwh_per_roomnight_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_electricity_kwh_per_m2"]) && $return["total_sum_pre_data_electricity_kwh_per_m2"] > 0) {
		$return["total_sum_data_electricity_per_m2_kwh_variation"] = round(((($return["total_sum_data_electricity_kwh_per_m2"] - $return["total_sum_pre_data_electricity_kwh_per_m2"]) * 100) / $return["total_sum_pre_data_electricity_kwh_per_m2"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_sum"] == 0) {
		    $return["total_sum_data_electricity_per_m2_kwh_variation"] = 0;
		} else {
		    $return["total_sum_data_electricity_per_m2_kwh_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_water_liter_per_roomnight"]) && $return["total_sum_pre_data_water_liter_per_roomnight"] > 0) {
		$return["total_sum_data_water_liter_per_roomnight_variation"] = round(((($return["total_sum_data_water_liter_per_roomnight"] - $return["total_sum_pre_data_water_liter_per_roomnight"]) * 100) / $return["total_sum_pre_data_water_liter_per_roomnight"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_sum"] == 0) {
		    $return["total_sum_data_water_liter_per_roomnight_variation"] = 0;
		} else {
		    $return["total_sum_data_water_liter_per_roomnight_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_utility_cost_per_roomnight"]) && $return["total_sum_pre_data_utility_cost_per_roomnight"] > 0) {
		$return["total_sum_data_utility_cost_per_roomnight_variation"] = ($return["total_sum_pre_data_utility_cost_per_roomnight"] != 0) ? round(((($return["total_sum_data_utility_cost_per_roomnight"] - $return["total_sum_pre_data_utility_cost_per_roomnight"]) * 100) / $return["total_sum_pre_data_utility_cost_per_roomnight"]), $percentage_decimal) : 0;
	    } else {
		if ($return["total_sum_data_sum"] == 0) {
		    $return["total_sum_data_utility_cost_per_roomnight_variation"] = 0;
		} else {
		    $return["total_sum_data_utility_cost_per_roomnight_variation"] = 100;
		}
	    }
	    if (!empty($return["total_sum_pre_data_utility_cost_per_m2"]) && $return["total_sum_pre_data_utility_cost_per_m2"] > 0) {
		$return["total_sum_data_utility_cost_per_m2_variation"] = round(((($return["total_sum_data_utility_cost_per_m2"] - $return["total_sum_pre_data_utility_cost_per_m2"]) * 100) / $return["total_sum_pre_data_utility_cost_per_m2"]), $percentage_decimal);
	    } else {
		if ($return["total_sum_data_sum"] == 0) {
		    $return["total_sum_data_utility_cost_per_m2_variation"] = 0;
		} else {
		    $return["total_sum_data_utility_cost_per_m2_variation"] = 100;
		}
	    }
	    //$return["total_sum_data_variation"] = ($return["total_sum_data_electricity_variation"]+$return["total_sum_data_return["fuel_variation"]"]+$return["return["total_sum_data_lpg_variation"]"]+$return["total_sum_data_return["natural_gas_variation"]"]+$return["total_sum_data_return["water_variation"]"]+$return["total_sum_data_return["heating_district_variation"]"]+$return["return["total_sum_data_cooling_district_variation"]"]);
	}
	if ($showCostBudgetVariance) {
	    $return["electricity_tariff_budget"] = ($currentBudgetActualData["total_electricity_kwh_budget"] != 0) ? round($currentBudgetActualData["total_electricity_cost_budget"] / $currentBudgetActualData["total_electricity_kwh_budget"], $value_decimal) : 0;
	    $return["fuel_oil_tariff_budget"] = ($currentBudgetActualData["total_fuel_oil_budget"] != 0) ? round($currentBudgetActualData["total_fuel_oil_cost_budget"] / $currentBudgetActualData["total_fuel_oil_budget"], $value_decimal) : 0;
	    $return["lpg_tariff_budget"] = ($currentBudgetActualData["total_lpg_budget"] != 0) ? round($currentBudgetActualData["total_lpg_cost_budget"] / $currentBudgetActualData["total_lpg_budget"], $value_decimal) : 0;
	    $return["natural_gas_tariff_budget"] = ($currentBudgetActualData["total_natural_gas_budget"] != 0) ? round($currentBudgetActualData["total_natural_gas_cost_budget"] / $currentBudgetActualData["total_natural_gas_budget"], $value_decimal) : 0;
	    $return["water_tariff_budget"] = ($currentBudgetActualData["water_total_consumption_budget"] != 0) ? round($currentBudgetActualData["water_total_consumption_cost_budget"] / $currentBudgetActualData["water_total_consumption_budget"], $value_decimal) : 0;
	    $return["district_cooling_tariff_budget"] = ($currentBudgetActualData["water_total_consumption_budget"] != 0) ? round($currentBudgetActualData["district_cooling_cost_budget"] / $currentBudgetActualData["district_cooling_budget"], $value_decimal) : 0;
	    $return["district_heating_tariff_budget"] = ($currentBudgetActualData["district_heating_budget"] != 0) ? round($currentBudgetActualData["district_heating_cost_budget"] / $currentBudgetActualData["district_heating_budget"], $value_decimal) : 0;
	    $return["electricity_tariff_actual"] = ($currentBudgetActualData["total_electricity_kwh_actual"] != 0) ? round($currentBudgetActualData["total_electricity_cost_actual"] / $currentBudgetActualData["total_electricity_kwh_actual"], $value_decimal) : 0;
	    $return["fuel_oil_tariff_actual"] = ($currentBudgetActualData["total_fuel_oil_actual"] != 0) ? round($currentBudgetActualData["total_fuel_oil_cost_actual"] / $currentBudgetActualData["total_fuel_oil_actual"], $value_decimal) : 0;
	    $return["lpg_tariff_actual"] = ($currentBudgetActualData["total_lpg_actual"] != 0) ? round($currentBudgetActualData["total_lpg_cost_actual"] / $currentBudgetActualData["total_lpg_actual"], $value_decimal) : 0;
	    $return["natural_gas_tariff_actual"] = ($currentBudgetActualData["total_natural_gas_actual"] != 0) ? round($currentBudgetActualData["total_natural_gas_cost_actual"] / $currentBudgetActualData["total_natural_gas_actual"], $value_decimal) : 0;
	    $return["water_tariff_actual"] = ($currentBudgetActualData["water_total_consumption_actual"] != 0) ? round($currentBudgetActualData["water_total_consumption_cost_actual"] / $currentBudgetActualData["water_total_consumption_actual"], $value_decimal) : 0;
	    $return["district_cooling_tariff_actual"] = ($currentBudgetActualData["district_cooling_actual"] != 0) ? round($currentBudgetActualData["district_cooling_cost_actual"] / $currentBudgetActualData["district_cooling_actual"], $value_decimal) : 0;
	    $return["district_heating_tariff_actual"] = ($currentBudgetActualData["district_heating_actual"] != 0) ? round($currentBudgetActualData["district_heating_cost_actual"] / $currentBudgetActualData["district_heating_actual"], $value_decimal) : 0;
	    $return["electricity_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_electricity_kwh_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["fuel_oil_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_fuel_oil_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["lpg_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_lpg_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["natural_gas_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_natural_gas_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["water_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["water_total_consumption_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["district_cooling_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["district_cooling_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["district_heating_per_room_night_actual"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["district_heating_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["electricity_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_electricity_kwh_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["fuel_oil_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_fuel_oil_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["lpg_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_lpg_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["natural_gas_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["total_natural_gas_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["water_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["water_total_consumption_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["district_cooling_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["district_cooling_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["district_heating_per_room_night_budget"] = ($currentBudgetActualData["total_room_night"] != 0) ? round($currentBudgetActualData["district_heating_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal) : 0;
	    $return["electricity_tariff_variation"] = ($return["electricity_tariff_budget"] != 0) ? round((($return["electricity_tariff_actual"] - $return["electricity_tariff_budget"]) * 100) / $return["electricity_tariff_budget"], $percentage_decimal) : 0;
	    $return["fuel_oil_tariff_variation"] = ($return["fuel_oil_tariff_budget"] != 0) ? round((($return["fuel_oil_tariff_actual"] - $return["fuel_oil_tariff_budget"]) * 100) / $return["fuel_oil_tariff_budget"], $percentage_decimal) : 0;
	    $return["lpg_tariff_variation"] = ($return["lpg_tariff_budget"] != 0) ? round((($return["lpg_tariff_actual"] - $return["lpg_tariff_budget"]) * 100) / $return["lpg_tariff_budget"], $percentage_decimal) : 0;
	    $return["natural_gas_tariff_variation"] = ($return["natural_gas_tariff_budget"] != 0) ? round((($return["natural_gas_tariff_actual"] - $return["natural_gas_tariff_budget"]) * 100) / $return["natural_gas_tariff_budget"], $percentage_decimal) : 0;
	    $return["water_tariff_variation"] = ($return["water_tariff_budget"] != 0) ? round((($return["water_tariff_actual"] - $return["water_tariff_budget"]) * 100) / $return["water_tariff_budget"], $percentage_decimal) : 0;
	    $return["district_cooling_tariff_variation"] = ($return["district_cooling_tariff_budget"] != 0) ? round((($return["district_cooling_tariff_actual"] - $return["district_cooling_tariff_budget"]) * 100) / $return["district_cooling_tariff_budget"], $percentage_decimal) : 0;
	    $return["district_heating_tariff_variation"] = ($return["district_heating_tariff_budget"] != 0) ? round((($return["district_heating_tariff_actual"] - $return["district_heating_tariff_budget"]) * 100) / $return["district_heating_tariff_budget"], $percentage_decimal) : 0;
	    $return["electricity_cost_variation"] = ($currentBudgetActualData["total_electricity_cost_budget"] != 0) ? round(($currentBudgetActualData["total_electricity_cost_actual"] - $currentBudgetActualData["total_electricity_cost_budget"]) * 100 / $currentBudgetActualData["total_electricity_cost_budget"], $percentage_decimal) : 0;
	    $return["fuel_oil_cost_variation"] = ($currentBudgetActualData["total_fuel_oil_cost_budget"] != 0) ? round(($currentBudgetActualData["total_fuel_oil_cost_actual"] - $currentBudgetActualData["total_fuel_oil_cost_budget"]) * 100 / $currentBudgetActualData["total_fuel_oil_cost_budget"], $percentage_decimal) : 0;
	    $return["lpg_cost_variation"] = ($currentBudgetActualData["total_lpg_cost_budget"] != 0) ? round(($currentBudgetActualData["total_lpg_cost_actual"] - $currentBudgetActualData["total_lpg_cost_budget"]) * 100 / $currentBudgetActualData["total_lpg_cost_budget"], $percentage_decimal) : 0;
	    $return["natural_gas_cost_variation"] = ($currentBudgetActualData["total_natural_gas_cost_budget"] != 0) ? round(($currentBudgetActualData["total_natural_gas_cost_actual"] - $currentBudgetActualData["total_natural_gas_cost_budget"]) * 100 / $currentBudgetActualData["total_natural_gas_cost_budget"], $percentage_decimal) : 0;
	    $return["water_cost_variation"] = ($currentBudgetActualData["water_total_consumption_cost_budget"] != 0) ? round(($currentBudgetActualData["water_total_consumption_cost_actual"] - $currentBudgetActualData["water_total_consumption_cost_budget"]) * 100 / $currentBudgetActualData["water_total_consumption_cost_budget"], $percentage_decimal) : 0;
	    $return["district_cooling_cost_variation"] = ($currentBudgetActualData["district_cooling_cost_budget"] != '') ? round(($currentBudgetActualData["district_cooling_cost_actual"] - $currentBudgetActualData["district_cooling_cost_budget"]) * 100 / $currentBudgetActualData["district_cooling_cost_budget"], $percentage_decimal) : 0;
	    $return["district_heating_cost_variation"] = ($currentBudgetActualData["district_heating_cost_budget"] != 0) ? round(($currentBudgetActualData["district_heating_cost_actual"] - $currentBudgetActualData["district_heating_cost_budget"]) * 100 / $currentBudgetActualData["district_heating_cost_budget"], $percentage_decimal) : 0;
	    $return["electricity_per_room_night_variation"] = ($return["electricity_per_room_night_budget"] != 0) ? round(($return["electricity_per_room_night_actual"] - $return["electricity_per_room_night_budget"]) * 100 / $return["electricity_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["fuel_oil_per_room_night_variation"] = ($return["fuel_oil_per_room_night_budget"] != 0) ? round(($return["fuel_oil_per_room_night_actual"] - $return["fuel_oil_per_room_night_budget"]) * 100 / $return["fuel_oil_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["lpg_per_room_night_variation"] = ($return["lpg_per_room_night_budget"] != 0) ? round(($return["lpg_per_room_night_actual"] - $return["lpg_per_room_night_budget"]) * 100 / $return["lpg_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["natural_gas_per_room_night_variation"] = ($return["natural_gas_per_room_night_budget"] != 0) ? round(($return["natural_gas_per_room_night_actual"] - $return["natural_gas_per_room_night_budget"]) * 100 / $return["natural_gas_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["water_per_room_night_variation"] = ($return["water_per_room_night_budget"] != 0) ? round((floatval($return["water_per_room_night_actual"]) - floatval($return["water_per_room_night_budget"])) * 100 / floatval($return["water_per_room_night_budget"]), $percentage_decimal) : 0;
	    $return["district_cooling_per_room_night_variation"] = ($return["district_cooling_per_room_night_budget"] != 0) ? round(($return["district_cooling_per_room_night_actual"] - $return["district_cooling_per_room_night_budget"]) * 100 / $return["district_cooling_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["district_heating_per_room_night_variation"] = ($return["district_heating_per_room_night_budget"] != 0) ? round(($return["district_heating_per_room_night_actual"] - $return["district_heating_per_room_night_budget"]) * 100 / $return["district_heating_per_room_night_budget"], $percentage_decimal) : 0;
	    $return["total_cost_budget"] = $currentBudgetActualData["total_electricity_cost_budget"] + $currentBudgetActualData["total_fuel_oil_cost_budget"] + $currentBudgetActualData["total_lpg_cost_budget"] + $currentBudgetActualData["total_natural_gas_cost_budget"] + $currentBudgetActualData["water_total_consumption_cost_budget"] + $currentBudgetActualData["district_cooling_cost_budget"] + $currentBudgetActualData["district_heating_cost_budget"];
	    $return["total_cost_actual"] = $currentBudgetActualData["total_electricity_cost_actual"] + $currentBudgetActualData["total_fuel_oil_cost_actual"] + $currentBudgetActualData["total_lpg_cost_actual"] + $currentBudgetActualData["total_natural_gas_cost_actual"] + $currentBudgetActualData["water_total_consumption_cost_actual"] + $currentBudgetActualData["district_cooling_cost_actual"] + $currentBudgetActualData["district_heating_cost_actual"];
	    $return["total_cost_variation"] = $return["total_cost_actual"] - $return["total_cost_budget"];
	    $return["total_cost_variation_percentage"] = ($return["total_cost_budget"] != 0) ? round($return["total_cost_variation"] * 100 / $return["total_cost_budget"], $percentage_decimal) : 0;
	    $return["total_cost_actual_per_room_night"] = round($return["total_cost_actual"] / $currentBudgetActualData["total_room_night"], $value_decimal);
	    //$return["total_cost_budget_per_room_night"] = round($return["total_cost_budget"] / $currentBudgetActualData["total_room_night"], $value_decimal);
	    //$return["total_cost_per_room_night_variation"] = $return["total_cost_actual_per_room_night"] - $return["total_cost_budget_per_room_night"];
	    //$return["total_cost_per_room_night_variation_percentage"] = round($return["total_cost_per_room_night_variation"] * 100 / $return["total_cost_actual_per_room_night"], $percentage_decimal);
	    $return["total_cost_actual_per_m2"] = round($return["total_cost_actual"] / $site_detail['site_builtup_area'], $value_decimal);
	    //$return["total_cost_budget_per_m2"] = round($return["total_cost_budget"] / $site_detail['site_builtup_area'], $value_decimal);
	    //$return["total_cost_per_m2_variation"] = $return["total_cost_actual_per_m2"] - $return["total_cost_budget_per_m2"];
	    //$return["total_cost_per_m2_variation_percentage"] = round($total_cost_per_m2_variation * 100 / $return["total_cost_actual_per_m2"], $percentage_decimal);
	}
	return $return;
    }

    public function report_layout_excel($data, $flag = 'ytd')
    {
	//make local variable of the data array with key as a variable and value as a value of that variable
	// eg. $data['key'] = value  ->   $key = value;
	$data['showCostBudgetVariance'] = true;
	extract($data);
	extract($this->report_calculations($data, $flag));
	$isLocal = true;
	if ($currency == "base") {
	    $isLocal = false;
	}
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$this->lang->load('sites/sites', 'english');
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Excel Report")
	    ->setKeywords("Excel Report");
	$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	$fullAlphaArray = array_merge(range('A', 'Z'));
	$percentage_decimal = 3;
	$value_decimal = 4;
	$header_row = 0;
	$row1 = 0;
	$row2 = 0;
	$row3 = 0;
	$row4 = 0;
	$box_display = array(
	    'borders' => array(
		'right' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'bottom' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'top' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'left' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
	    ),
	);
	$style_border_right_left = array(
	    'borders' => array(
		'right' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'left' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
	    ),
	);
	$style_positive_number = array(
	    'font' => array(
		'color' => array('rgb' => 'ff0000'),
	    ),
	);
	$style_total_number = array(
	    'font' => array(
		'bold' => true,
		'color' => array('rgb' => '001dff'),
	    ),
	);
	/*
	 * ##################################################
	 *          TABLE STYLES & ALIGNMENT
	 * ##################################################
	 */
	//$objPHPExcel->setActiveSheetIndex(0)->setShowGridlines();
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	//#d8e1f2;
	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	$style = array('font' => array('size' => 20));
	$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('A2:I3')->applyFromArray($style);
	$style = array('font' => array('size' => 12));
	$objPHPExcel->getActiveSheet()->getStyle('A4:J60')->applyFromArray($style);
	// Add logo
	if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	} else {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	}
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Logo');
	$objDrawing->setDescription('Logo');
	$objDrawing->setPath($site_logo);
	$objDrawing->setCoordinates('A1');
	$objDrawing->setHeight(100); // logo height
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	// Prepare excel data
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $site_detail['site_location_name']);
	/*
	 * ##################################################
	 *          HEADERS STYLES
	 * ##################################################
	 */
	$objPHPExcel->getActiveSheet()->getStyle('B6:D8')->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('E6:G8')->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('H6:I8')->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	$objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($box_display);
	$objPHPExcel->getActiveSheet()->getStyle('A5:I5')->applyFromArray($box_display);
	$objPHPExcel->getActiveSheet()->getStyle('A5:I8')->applyFromArray($box_display);
	$objPHPExcel->getActiveSheet()->getStyle('B4:D5')->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('E5:G5')->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('H4:I5')->applyFromArray($style_border_right_left);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $pdf_report_title);
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A4', $table_title)
	    ->setCellValue('B4', $current_title)
	    ->setCellValue('C4', $previous_title)
	    ->setCellValue('D4', $budget_title)
	    ->setCellValue('E4', 'Difference v/s Last Year')
	    ->setCellValue('H4', 'Difference v/s Budget');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:I3');
	$objPHPExcel->getActiveSheet()->mergeCells('B1:C1');
	$objPHPExcel->getActiveSheet()->mergeCells('E4:G4');
	$objPHPExcel->getActiveSheet()->mergeCells('H4:I4');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
	$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('E5', 'Value')
	    ->setCellValue('F5', '%')
	    ->setCellValue('G5', 'Cost @ LY Tariff')
	    ->setCellValue('H5', 'Value')
	    ->setCellValue('I5', '%');
	//rom night, cdd and hdd data
	$room_night_difference = ($total_sum_data_room_night - $total_sum_pre_data_room_night);
	$cdd_difference = $total_sum_data_cdd - $total_sum_pre_data_cdd;
	$hdd_difference = $total_sum_data_hdd - $total_sum_pre_data_hdd;
	if ($room_night_difference < 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E6:F6')->applyFromArray($style_positive_number);
	}
	if ($cdd_difference > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E7:F7')->applyFromArray($style_positive_number);
	}
	if ($hdd_difference > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E8:F8')->applyFromArray($style_positive_number);
	}
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A6', 'Room Night')
	    ->setCellValue('A7', 'CDD')
	    ->setCellValue('A8', 'HDD');
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('B6', number_format($total_sum_data_room_night))
	    ->setCellValue('B7', number_format($total_sum_data_cdd))
	    ->setCellValue('B8', number_format($total_sum_data_hdd));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('C6', number_format($total_sum_pre_data_room_night))
	    ->setCellValue('C7', number_format($total_sum_pre_data_cdd))
	    ->setCellValue('C8', number_format($total_sum_pre_data_hdd));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('E6', number_format($room_night_difference))
	    ->setCellValue('E7', number_format($cdd_difference))
	    ->setCellValue('E8', number_format($hdd_difference));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('F6', number_format($total_sum_data_room_night_variation, 2) . "%")
	    ->setCellValue('F7', number_format($total_sum_data_cdd_variation, 2) . "%")
	    ->setCellValue('F8', number_format($total_sum_data_hdd_variation, 2) . "%");
	$header_row = 9;
	//electricity data
	if ($site_detail['show_utility_electricity']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          ELECTRICITY BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $electricity_tariff_cl_diff = $total_sum_data_electricity_tariff - $total_sum_pre_data_electricity_tariff;
	    $electricity_cost_cl_diff = $total_sum_data_electricity - $total_sum_pre_data_electricity;
	    $electricity_actual_budget_diff = $currentBudgetActualData["total_electricity_kwh_actual"] - $currentBudgetActualData["total_electricity_kwh_budget"];
	    $electricity_actual_budget_tariff_diff = $electricity_tariff_actual - $electricity_tariff_budget;
	    $elecricity_act_bud_cost_diff = $currentBudgetActualData["total_electricity_cost_actual"] - $currentBudgetActualData["total_electricity_cost_budget"];
	    $electricity_act_bud_pr_rn_diff = $electricity_per_room_night_actual - $electricity_per_room_night_budget;
	    if ($electricity_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_tariff_cl_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_cost_cl_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_actual_budget_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_actual_budget_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($elecricity_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($electricity_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'ELECTRICITY')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'electricity') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'electricity') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_electricity_kwh))
		->setCellValue('B' . $row2, num_format($total_sum_data_electricity_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_electricity, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_electricity_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_electricity_kwh))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_electricity_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_electricity, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_electricity_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["total_electricity_kwh_budget"]))
		->setCellValue('D' . $row2, num_format($electricity_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["total_electricity_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($electricity_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($electricity_consumption_difference))
		->setCellValue('E' . $row2, num_format($electricity_tariff_cl_diff, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($electricity_cost_cl_diff, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($electricity_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($electricity_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($total_sum_data_electricity_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_electricity_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($electricity_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($electricity_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($electricity_actual_budget_diff))
		->setCellValue('H' . $row2, num_format($electricity_actual_budget_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($elecricity_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($electricity_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["total_electricity_kwh_actual"] != 0) ? $electricity_actual_budget_diff * 100 / $currentBudgetActualData["total_electricity_kwh_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($electricity_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($electricity_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($electricity_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_fuel_oil']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          FUEL OIL BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $fuel_cost_cl_diff = $total_sum_data_fuel - $total_sum_pre_data_fuel;
	    $fuel_act_bud_diff = $currentBudgetActualData["total_fuel_oil_actual"] - $currentBudgetActualData["total_fuel_oil_budget"];
	    $fuel_act_bud_tariff_diff = $fuel_oil_tariff_actual - $fuel_oil_tariff_budget;
	    $fuel_act_bud_cost_diff = $currentBudgetActualData["total_fuel_oil_cost_actual"] - $currentBudgetActualData["total_fuel_oil_cost_budget"];
	    $fuel_act_bud_rn_diff = $fuel_oil_per_room_night_actual - $fuel_oil_per_room_night_budget;
	    if ($fuel_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_cost_cl_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_oil_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_act_bud_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($fuel_act_bud_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'FUEL OIL')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'fuel_oil') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'fuel_oil') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_fuel_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_fuel_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_fuel, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_fuel_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_fuel_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_fuel_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_fuel, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_fuel_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["total_fuel_oil_budget"]))
		->setCellValue('D' . $row2, num_format($fuel_oil_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["total_fuel_oil_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($fuel_oil_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($fuel_consumption_difference))
		->setCellValue('E' . $row2, num_format($fuel_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($fuel_cost_cl_diff, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($fuel_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($fuel_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($fuel_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_fuel_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($fuel_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($fuel_oil_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($fuel_act_bud_diff))
		->setCellValue('H' . $row2, num_format($fuel_act_bud_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($fuel_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, num_format($fuel_act_bud_rn_diff, $percentage_decimal, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["total_fuel_oil_actual"] != 0) ? $fuel_act_bud_diff * 100 / $currentBudgetActualData["total_fuel_oil_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($fuel_oil_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($fuel_oil_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($fuel_oil_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_lpg']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          LPG BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $lpg_cl_cost_diff = $total_sum_data_lpg - $total_sum_pre_data_lpg;
	    $lpg_act_bud_diff = $currentBudgetActualData["total_lpg_actual"] - $currentBudgetActualData["total_lpg_budget"];
	    $lpg_act_bud_tarrif_diff = $lpg_tariff_actual - $lpg_tariff_budget;
	    $lpg_act_bud_cost_diff = $currentBudgetActualData["total_lpg_cost_actual"] - $currentBudgetActualData["total_lpg_cost_budget"];
	    $lpg_act_bud_pr_rn_diff = $lpg_per_room_night_actual - $lpg_per_room_night_budget;
	    if ($lpg_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_cl_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_act_bud_tarrif_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($lpg_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'LPG')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'lpg') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'lpg') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_lpg_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_lpg_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_lpg, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_lpg_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_lpg_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_lpg_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_lpg, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_lpg_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["total_lpg_budget"]))
		->setCellValue('D' . $row2, num_format($lpg_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["total_lpg_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($lpg_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($lpg_consumption_difference))
		->setCellValue('E' . $row2, num_format($lpg_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($lpg_cl_cost_diff, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($lpg_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($lpg_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($lpg_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_lpg_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($lpg_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($lpg_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($lpg_act_bud_diff))
		->setCellValue('H' . $row2, num_format($lpg_act_bud_tarrif_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($lpg_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($lpg_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["total_lpg_actual"] != 0) ? $lpg_act_bud_diff * 100 / $currentBudgetActualData["total_lpg_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($lpg_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($lpg_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($lpg_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_natural_gas']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          NATURAL GAS BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $natural_gas_act_bud_diff = $currentBudgetActualData["total_natural_gas_actual"] - $currentBudgetActualData["total_natural_gas_budget"];
	    $natural_gas_act_bud_tariff_diff = $natural_gas_tariff_actual - $natural_gas_tariff_budget;
	    $natural_gas_act_bud_cost_diff = $currentBudgetActualData["total_natural_gas_cost_actual"] - $currentBudgetActualData["total_natural_gas_cost_budget"];
	    $natural_gas_act_bud_pr_rn_diff = $natural_gas_per_room_night_actual - $natural_gas_per_room_night_budget;
	    if ($natural_gas_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if (($total_sum_data_natural_gas - $total_sum_pre_data_natural_gas) > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_act_bud_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($natural_gas_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'NATURAL GAS')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'natural_gas') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'natural_gas') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_natural_gas_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_natural_gas_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_natural_gas, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_natural_gas_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_natural_gas_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_natural_gas_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_natural_gas, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_natural_gas_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["total_natural_gas_budget"]))
		->setCellValue('D' . $row2, num_format($natural_gas_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["total_natural_gas_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($natural_gas_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($natural_gas_consumption_difference))
		->setCellValue('E' . $row2, num_format($natural_gas_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($total_sum_data_natural_gas - $total_sum_pre_data_natural_gas, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($natural_gas_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($natural_gas_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($natural_gas_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_natural_gas_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($natural_gas_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($natural_gas_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($natural_gas_act_bud_diff))
		->setCellValue('H' . $row2, num_format($natural_gas_act_bud_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($natural_gas_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($natural_gas_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["total_natural_gas_actual"] != 0) ? $natural_gas_act_bud_diff * 100 / $currentBudgetActualData["total_natural_gas_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($natural_gas_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($natural_gas_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($natural_gas_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_water']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          WATER BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $water_act_bud_diff = $currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"];
	    $water_act_bud_tariff_diff = $water_tariff_actual - $water_tariff_budget;
	    $water_act_bud_cost_diff = $currentBudgetActualData["water_total_consumption_cost_actual"] - $currentBudgetActualData["water_total_consumption_cost_budget"];
	    $water_act_bud_pr_rn_diff = $water_per_room_night_actual - $water_per_room_night_budget;
	    if ($water_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($water_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if (($total_sum_data_water - $total_sum_pre_data_water) > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($water_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($water_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($water_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($water_act_bud_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($water_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($water_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'WATER')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'water') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'water') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_water_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_water_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_water, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_water_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_water_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_water_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_water, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_water_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["water_total_consumption_budget"]))
		->setCellValue('D' . $row2, num_format($water_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["water_total_consumption_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($water_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($water_consumption_difference))
		->setCellValue('E' . $row2, num_format($water_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($total_sum_data_water - $total_sum_pre_data_water, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($water_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($water_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($water_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_water_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($water_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($water_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($water_act_bud_diff))
		->setCellValue('H' . $row2, num_format($water_act_bud_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($water_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($water_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["water_total_consumption_actual"] != 0) ? ($currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"]) * 100 / $currentBudgetActualData["water_total_consumption_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($water_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($water_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format(floatval((string) $water_per_room_night_variation), $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_district_cooling']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          DISTRICT COOLING BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $district_cooling_act_bud_diff = $currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"];
	    $district_cooling_act_bud_tariff_diff = $district_cooling_tariff_actual - $district_cooling_tariff_budget;
	    $district_cooling_act_bud_cost_diff = $currentBudgetActualData["district_cooling_cost_actual"] - $currentBudgetActualData["district_cooling_cost_budget"];
	    $district_cooling_act_bud_pr_rn_diff = $district_cooling_per_room_night_actual - $district_cooling_per_room_night_budget;
	    if ($cooling_district_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($cooling_district_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if (($total_sum_data_cooling_district - $total_sum_pre_data_cooling_district) > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($cooling_district_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($cooling_district_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($district_cooling_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($district_cooling_act_bud_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($district_cooling_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($district_cooling_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'DISTRICT COOLING')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_detail['id'], 'district_cooling') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_detail['id'], 'district_cooling') . ')')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . ')')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_cooling_district_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_cooling_district_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_cooling_district, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_cooling_district_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_cooling_district_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_cooling_district_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_cooling_district, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_cooling_district_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["district_cooling_budget"]))
		->setCellValue('D' . $row2, num_format($district_cooling_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["district_cooling_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($district_cooling_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($cooling_district_consumption_difference))
		->setCellValue('E' . $row2, num_format($cooling_district_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($total_sum_data_cooling_district - $total_sum_pre_data_cooling_district, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($cooling_district_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($cooling_district_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($cooling_district_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_cooling_district_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($cooling_district_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($cooling_district_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($district_cooling_act_bud_diff))
		->setCellValue('H' . $row2, num_format($district_cooling_act_bud_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($district_cooling_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($district_cooling_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["district_cooling_actual"] != 0) ? ($currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"]) * 100 / $currentBudgetActualData["district_cooling_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($district_cooling_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($district_cooling_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($district_cooling_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	if ($site_detail['show_utility_district_heating']) {
	    $row1 = $header_row + 1;
	    $row2 = $header_row + 2;
	    $row3 = $header_row + 3;
	    $row4 = $header_row + 4;
	    /*
	     * ##################################################
	     *          DISTRICT HEATING BLOCK STYLES
	     * ##################################################
	     */
	    $objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row4)->applyFromArray($box_display);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row4)->applyFromArray($style_border_right_left);
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row4)->applyFromArray($style_border_right_left);
	    $district_heating_act_bud_diff = $currentBudgetActualData["district_heating_actual"] - $currentBudgetActualData["district_heating_budget"];
	    $district_heating_act_bud_tariff_diff = $district_heating_tariff_actual - $district_heating_tariff_budget;
	    $district_heating_act_bud_cost_diff = $currentBudgetActualData["district_heating_cost_actual"] - $currentBudgetActualData["district_heating_cost_budget"];
	    $district_heating_act_bud_pr_rn_diff = $district_heating_per_room_night_actual - $district_heating_per_room_night_budget;
	    if ($heating_district_consumption_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($heating_district_difference > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	    }
	    if (($total_sum_data_heating_district - $total_sum_pre_data_heating_district) > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($heating_district_per_room_night_difference_value > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('E' . $row4 . ':F' . $row4)->applyFromArray($style_positive_number);
	    }
	    if ($heating_district_ly > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($district_heating_act_bud_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	    }
	    if ($district_heating_act_bud_tariff_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row2 . ':I' . $row2)->applyFromArray($style_positive_number);
	    }
	    if ($district_heating_act_bud_cost_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row3 . ':I' . $row3)->applyFromArray($style_positive_number);
	    }
	    if ($district_heating_act_bud_pr_rn_diff > 0) {
		$objPHPExcel->getActiveSheet()->getStyle('H' . $row4 . ':I' . $row4)->applyFromArray($style_positive_number);
	    }
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $header_row, 'DISTRICT HEATING')
		->setCellValue('B' . $header_row, '');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $row1, 'Consumption (' . GetSiteUtilityUnitName($site_id, 'district_heating') . ')')
		->setCellValue('A' . $row2, 'Average Tariff (' . currency_symbol($isLocal) . ' / ' . GetSiteUtilityUnitName($site_id, 'district_heating') . '')
		->setCellValue('A' . $row3, 'Total Cost (' . currency_symbol($isLocal) . '')
		->setCellValue('A' . $row4, 'Consumption/Room Night');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row1, number_format($total_sum_data_heating_district_consumption))
		->setCellValue('B' . $row2, num_format($total_sum_data_heating_district_tariff, $value_decimal, $isLocal))
		->setCellValue('B' . $row3, num_format($total_sum_data_heating_district, 0, $isLocal))
		->setCellValue('B' . $row4, number_format($total_sum_data_heating_district_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('C' . $row1, number_format($total_sum_pre_data_heating_district_consumption))
		->setCellValue('C' . $row2, num_format($total_sum_pre_data_heating_district_tariff, $value_decimal, $isLocal))
		->setCellValue('C' . $row3, num_format($total_sum_pre_data_heating_district, 0, $isLocal))
		->setCellValue('C' . $row4, number_format($total_sum_pre_data_heating_district_per_room_night, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('D' . $row1, number_format($currentBudgetActualData["district_heating_budget"]))
		->setCellValue('D' . $row2, num_format($district_heating_tariff_budget, $value_decimal, $isLocal))
		->setCellValue('D' . $row3, num_format($currentBudgetActualData["district_heating_cost_budget"], 0, $isLocal))
		->setCellValue('D' . $row4, number_format($district_heating_per_room_night_budget, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('E' . $row1, number_format($heating_district_consumption_difference))
		->setCellValue('E' . $row2, num_format($heating_district_difference, $value_decimal, $isLocal))
		->setCellValue('E' . $row3, num_format($total_sum_data_heating_district - $total_sum_pre_data_heating_district, 0, $isLocal))
		->setCellValue('E' . $row4, number_format($heating_district_per_room_night_difference_value, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('F' . $row1, number_format($heating_district_consumption_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row2, number_format($heating_district_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row3, number_format($total_sum_data_heating_district_variation, $percentage_decimal) . "%")
		->setCellValue('F' . $row4, number_format($heating_district_per_room_night_difference_percent, $percentage_decimal) . "%");
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('G' . $row1, num_format($heating_district_ly, 0, $isLocal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('H' . $row1, number_format($district_heating_act_bud_diff))
		->setCellValue('H' . $row2, num_format($district_heating_act_bud_tariff_diff, $value_decimal, $isLocal))
		->setCellValue('H' . $row3, num_format($district_heating_act_bud_cost_diff, 0, $isLocal))
		->setCellValue('H' . $row4, number_format($district_heating_act_bud_pr_rn_diff, $percentage_decimal));
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('I' . $row1, number_format(($currentBudgetActualData["district_heating_actual"] != 0) ? ($currentBudgetActualData["district_heating_actual"] - $currentBudgetActualData["district_heating_budget"]) * 100 / $currentBudgetActualData["district_heating_actual"] : 0, $percentage_decimal) . "%")
		->setCellValue('I' . $row2, number_format($district_heating_tariff_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row3, number_format($district_heating_cost_variation, $percentage_decimal) . "%")
		->setCellValue('I' . $row4, number_format($district_heating_per_room_night_variation, $percentage_decimal) . "%");
	    $header_row += 5;
	}
	$row1 = $header_row + 1;
	$row2 = $header_row + 2;
	$row3 = $header_row + 3;
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A' . $header_row, 'TOTAL')
	    ->setCellValue('B' . $header_row, '');
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('A' . $row1, 'Total Cost(' . currency_symbol($isLocal) . ')')
	    ->setCellValue('A' . $row2, 'Total Cost / Room Night(' . currency_symbol($isLocal) . ')')
	    ->setCellValue('A' . $row3, 'Total Cost / m2(' . currency_symbol($isLocal) . ' / m2)');
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('B' . $row1, num_format($total_sum_data_sum, 0, $isLocal))
	    ->setCellValue('B' . $row2, num_format($total_sum_data_utility_cost_per_roomnight, $percentage_decimal, $isLocal))
	    ->setCellValue('B' . $row3, num_format($total_sum_data_utility_cost_per_m2, $percentage_decimal, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('C' . $row1, num_format($total_sum_pre_data_sum, 0, $isLocal))
	    ->setCellValue('C' . $row2, num_format($total_sum_pre_data_utility_cost_per_roomnight, $percentage_decimal, $isLocal))
	    ->setCellValue('C' . $row3, num_format($total_sum_pre_data_utility_cost_per_m2, $percentage_decimal, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('D' . $row1, num_format($total_cost_budget, 0, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('E' . $row1, num_format($total_sum_difference_value, 0, $isLocal))
	    ->setCellValue('E' . $row2, num_format($data_utility_cost_per_roomnight_difference, $percentage_decimal, $isLocal))
	    ->setCellValue('E' . $row3, num_format($data_utility_cost_per_m2_difference, $percentage_decimal, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('F' . $row1, number_format($total_sum_difference_percent, $percentage_decimal) . "%")
	    ->setCellValue('F' . $row2, number_format($data_utility_cost_per_roomnight_variation, $percentage_decimal) . "%")
	    ->setCellValue('F' . $row3, number_format($data_utility_cost_per_m2_variation, $percentage_decimal) . "%");
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('G' . $row1, num_format($total_cost_ly, 0, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('H' . $row1, num_format($total_cost_variation, 0, $isLocal));
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('I' . $row1, number_format($total_cost_variation_percentage, $percentage_decimal) . "%");
	//TOTAL columns style
	$objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $row3)->applyFromArray($style_total_number);
	$objPHPExcel->getActiveSheet()->mergeCells('B' . $header_row . ':I' . $header_row);
	$objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d8e1f2');
	$objPHPExcel->getActiveSheet()->getStyle('A' . $header_row . ':I' . $header_row)->applyFromArray($box_display);
	$objPHPExcel->getActiveSheet()->getStyle('A' . $row1 . ':I' . $row3)->applyFromArray($box_display);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $row1 . ':D' . $row3)->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':G' . $row3)->applyFromArray($style_border_right_left);
	$objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row3)->applyFromArray($style_border_right_left);
	if ($total_sum_difference_value > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row1 . ':F' . $row1)->applyFromArray($style_positive_number);
	}
	if ($data_utility_cost_per_roomnight_difference > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row2 . ':F' . $row2)->applyFromArray($style_positive_number);
	}
	if ($data_utility_cost_per_m2_difference > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $row3 . ':F' . $row3)->applyFromArray($style_positive_number);
	}
	if ($total_cost_ly > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('G' . $row1)->applyFromArray($style_positive_number);
	}
	if ($total_cost_variation > 0) {
	    $objPHPExcel->getActiveSheet()->getStyle('H' . $row1 . ':I' . $row1)->applyFromArray($style_positive_number);
	}
	return $objPHPExcel;
    }

    public function saveimage()
    {
	$url = $this->input->post('url');
	$imagename = $this->input->post('imagename');
	$image = ($imagename == '') ? 'image.png' : $imagename;
	$filePath = dirname(__FILE__) . '/' . $image;
	if ($url) {
	    list($type, $url) = explode(';', $url);
	    list(, $url) = explode(',', $url);
	    $url = base64_decode($url);
	    if (file_put_contents($filePath, $url)) {
		echo $image;
	    } else {
		echo '';
	    }
	}
    }

    public function getimage()
    {
	$imagename = $this->input->get('image');
	$image = ($imagename == '') ? 'image.png' : $imagename;
	$filePath = dirname(__FILE__) . '/' . $image;
	if (file_exists($filePath)) {
	    $fileName = basename($filePath);
	    $fileSize = filesize($filePath);
	    // Output headers.
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header("Pragma: ");
	    header("Cache-Control: ");
	    header("Content-Length: " . $fileSize);
	    header("Content-Disposition: attachment; filename=" . $fileName);
	    // Output file.
	    readfile($filePath);
	    unlink($filePath);
	    exit();
	} else {
	    die('The provided file path is not valid.');
	}
    }

    public function daily_metering()
    {
	/* if (!UTILITIES_DAILY_MENU) {
	  redirect("/dashboard");
	  } */
	$this->breadcrumb->add('Daily Metering', base_url() . BASE_ADMIN_URL_CUSTOM . '/reports/daily_metering');
	$this->load->model('sites/sites_model');
	$user_id = $this->reports_model->user_id;
	$site_id = $this->reports_model->site_id;
	$role_id = $this->reports_model->role_id;
	$room_keys = $site_detail['rooms_keys'];
	$daily_metering = $site_detail['daily_metering'];
	$is_hourly = $site_detail['is_hourly'];
	$month = (int) date('m');
	$year = (int) date('Y');
	$date = (int) date('d');
	$date_id = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$data = array();
	$data = $this->input->post();
	if (isset($data['month'])) {
	    $month = $this->input->post('month');
	}
	if (isset($data['year'])) {
	    $year = $this->input->post('year');
	}
	if (isset($data['date'])) {
	    $date = $this->input->post('date');
	}
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$data['year'] = $year;
	$data['month'] = $month;
	$data['date'] = $date;
	$decimal_point = 4;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $data = $this->input->post();
	    if (isset($data['month'])) {
		$month = (int) $this->input->post('month');
	    }
	    if (isset($data['year'])) {
		$year = (int) $this->input->post('year');
	    }
	}
	$last_year_month = $month;
	$last_year = $year - 1;
	$totalDays = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$totalDays_last = (int) cal_days_in_month(CAL_GREGORIAN, $last_year_month, $last_year);
	$today_date = (int) date('d');
	if ($month == (int) date('m') && $year == (int) date('Y')) {
	    $to_date = $today_date - 1;
	    $to_date_last_year = $today_date - 1;
	} else {
	    $to_date = $totalDays;
	    $to_date_last_year = $totalDays_last;
	}
	//Get selected month Data
	$this->reports_model->site_id = $site_id;
	//get_daily_metering_reading_data($site_id = 0, $month, $year, $to_date = 31)
	$selected_month_year_data = $this->reports_model->get_daily_metering_reading_data($site_id, $month, $year, $to_date, $date, $daily_metering);
	$last_month_year_data = $this->reports_model->get_daily_metering_reading_data($site_id, $last_year_month, $last_year, $to_date_last_year, $date, $daily_metering);
	$selected_month_year_static_data_results = $this->reports_model->get_daily_reading_static_data($site_id, $month, $year, $to_date);
	$last_month_year_static_data_results = $this->reports_model->get_daily_reading_static_data($site_id, $last_year_month, $last_year, $to_date_last_year);
	// Prepare Data
	$selected_month_year_static_data = array();
	$selected_month_year_static_data['electricity_cost'] = 0;
	$selected_month_year_static_data['lpg_cost'] = 0;
	$selected_month_year_static_data['water_cost'] = 0;
	$selected_month_year_static_data['fuel_oil_cost'] = 0;
	$selected_month_year_static_data['natural_gas_cost'] = 0;
	$selected_month_year_static_data['district_cooling_cost'] = 0;
	$selected_month_year_static_data['district_heating_cost'] = 0;
	$selected_month_year_static_data['cdd'] = 0;
	$selected_month_year_static_data['hdd'] = 0;
	$selected_month_year_static_data['total_room_night'] = 0;
	$selected_month_year_static_data['total_guests'] = 0;
	$last_month_year_static_data = array();
	$last_month_year_static_data['electricity_cost'] = 0;
	$last_month_year_static_data['lpg_cost'] = 0;
	$last_month_year_static_data['water_cost'] = 0;
	$last_month_year_static_data['fuel_oil_cost'] = 0;
	$last_month_year_static_data['natural_gas_cost'] = 0;
	$last_month_year_static_data['district_cooling_cost'] = 0;
	$last_month_year_static_data['district_heating_cost'] = 0;
	$last_month_year_static_data['cdd'] = 0;
	$last_month_year_static_data['hdd'] = 0;
	$last_month_year_static_data['total_room_night'] = 0;
	$last_month_year_static_data['total_guests'] = 0;
	foreach ($selected_month_year_static_data_results as $key => $value) {
	    $selected_month_year_static_data['electricity_cost'] += $value['electricity_cost'];
	    $selected_month_year_static_data['lpg_cost'] += $value['lpg_cost'];
	    $selected_month_year_static_data['water_cost'] += $value['water_cost'];
	    $selected_month_year_static_data['fuel_oil_cost'] += $value['fuel_oil_cost'];
	    $selected_month_year_static_data['natural_gas_cost'] += $value['natural_gas_cost'];
	    $selected_month_year_static_data['district_cooling_cost'] += $value['district_cooling_cost'];
	    $selected_month_year_static_data['district_heating_cost'] += $value['district_heating_cost'];
	    $selected_month_year_static_data['cdd'] += $value['cdd'];
	    $selected_month_year_static_data['hdd'] += $value['hdd'];
	    $selected_month_year_static_data['total_room_night'] += $value['total_room_night'];
	    $selected_month_year_static_data['total_guests'] += $value['total_guests'];
	}
	foreach ($last_month_year_static_data_results as $key => $value) {
	    $last_month_year_static_data['electricity_cost'] += $value['electricity_cost'];
	    $last_month_year_static_data['lpg_cost'] += $value['lpg_cost'];
	    $last_month_year_static_data['water_cost'] += $value['water_cost'];
	    $last_month_year_static_data['fuel_oil_cost'] += $value['fuel_oil_cost'];
	    $last_month_year_static_data['natural_gas_cost'] += $value['natural_gas_cost'];
	    $last_month_year_static_data['district_cooling_cost'] += $value['district_cooling_cost'];
	    $last_month_year_static_data['district_heating_cost'] += $value['district_heating_cost'];
	    $last_month_year_static_data['cdd'] += $value['cdd'];
	    $last_month_year_static_data['hdd'] += $value['hdd'];
	    $last_month_year_static_data['total_room_night'] += $value['total_room_night'];
	    $last_month_year_static_data['total_guests'] += $value['total_guests'];
	}
	$report_data = array();
	foreach ($selected_month_year_data as $utility) {
	    $tempData = array();
	    $tempData['id'] = $utility['id'];
	    $tempData['title'] = $utility['title'];
	    $tempData['submission'] = array();
	    foreach ($utility['submission_titles'] as $submission) {
		$tempData['submission'][$submission['hourly_title']]['current_year_total'] = $submission['total'];
	    }
	    $report_data[$utility['id']] = $tempData;
	}
	foreach ($last_month_year_data as $utility) {
	    foreach ($utility['submission_titles'] as $submission) {
		$report_data[$utility['id']]['submission'][$submission['hourly_title']]['last_year_total'] = $submission['total'];
	    }
	}
	// Get budget data
	$filters['startdate'] = $month . '/' . $year;
	$filters['enddate'] = $month . '/' . $year;
	$filters['start_month'] = $month;
	$filters['start_year'] = $year;
	$filters['end_month'] = $month;
	$filters['end_year'] = $year;
	//data array for dropdown utilities title
	$dropdownArray = $this->reports_model->get_daily_metering_reading_utilities_titles();
	$utilityArray = [];
	$utilityTitlesArray = [];
	$utilityTitles = [];
	$total_room_nights = [
	    'current' => 0,
	    'previous' => 0,
	];
	$i = 1;
	$chartData_titles = [];
	foreach ($dropdownArray as $option) {
	    if ($i == 1) {
		// $utility_select = $option['id'];
		$i = 0;
	    }
	    $utilityArray[$option['utility_id']] = $option['utility'];
	    $utilityTitlesArray[$option['utility_id']][$option['id']] = [
		'id' => $option['id'],
		'title' => $option['utility_title'] . GetSiteUtilityUnitName($site_id, $option['utility']),
	    ];
	    $utilityTitles[$option['id']] = [
		'name' => $option['utility_title'] . GetSiteUtilityUnitName($site_id, $option['utility']),
		'parent' => $option['utility'],
	    ];
	}
	$utilityArray = array_unique($utilityArray);
	if ($this->input->post('chart')) {
	    $utility_select = $data['utility_select'];
	    if (empty($utility_select)) {
		$this->theme->set_message(lang('utility-generate-chart-no-data'), 'error');
		redirect("/reports/daily_metering");
	    }
	    /* =================== current year data ======================= */
	    $is_half_hourly = '';
	    if (intval($is_hourly) == 1) {
		$quarterHours = array('00');
		$is_half_hourly = 0;
	    } else {
		$quarterHours = array('00', '30');
		$is_half_hourly = 1;
	    }
	    $filterData = [
		'title_id' => $utility_select,
		'month' => $month,
		'year_id' => $year,
		'date_id' => $date,
		'is_half_hourly' => $is_half_hourly,
	    ];
	    $utility_title_data['current'] = $this->reports_model->get_hourly_reading_utilities_title_data($filterData);
	    $daily_reading_utilities_data['current'] = $this->reports_model->get_hourly_reading_utilities_data($filterData);
	    $days_of_month_current = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    /* =================== current year data ======================= */
	    /* =================== previos year data ======================= */
	    $filterData = [
		'title_id' => $utility_select,
		'month' => $month,
		'year_id' => ($year - 1),
		'date_id' => $date,
		'is_half_hourly' => $is_half_hourly,
	    ];
	    $utility_title_data['previous'] = $this->reports_model->get_hourly_reading_utilities_title_data($filterData);
	    $daily_reading_utilities_data['previous'] = $this->reports_model->get_hourly_reading_utilities_data($filterData);
	    $days_of_month_previous = (int) cal_days_in_month(CAL_GREGORIAN, $month, $year);
	    /* =================== previos year data ======================= */
	    $total_days = ($days_of_month_current > $days_of_month_previous) ? $days_of_month_current : $days_of_month_previous;
	    if (count($data['utility_select']) == 1) {
		/* =================== chart data iitialization ================= */
		$chartData_cdd_hdd = [];
		$chartData_occupancy = [];
		$chartData_cdd_hdd[0] = [
		    'Date',
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year - 1),
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year)
		];
		$chartData_occupancy[0] = [
		    'Date',
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year - 1),
		    $utilityTitles[$utility_select[0]]['name'] . " - " . ($year),
		    /* 'Occupancy - ' . ($year - 1),
		      'Occupancy - ' . $year, */
		];
		/* =================== chart data iitialization ================= */
		for ($i = 0; $i < 24; $i++) {
		    for ($j = 0; $j < count($quarterHours); $j++) {
			$time = strval($i . ":" . $quarterHours[$j]);
			if ($i < 10) {
			    $time = "0" . $time;
			}
			/* ============================= CDD - HDD values ============================== */
			//Previous year
			if (array_key_exists($time, $daily_reading_utilities_data['previous'])) {
			    $previous_cdd = $daily_reading_utilities_data['previous'][$time]['cdd'];
			    $previous_hdd = $daily_reading_utilities_data['previous'][$time]['hdd'];
			} else {
			    $previous_cdd = 0;
			    $previous_hdd = 0;
			}
			//Current year
			if (array_key_exists($time, $daily_reading_utilities_data['current'])) {
			    $current_cdd = $daily_reading_utilities_data['current'][$time]['cdd'];
			    $current_hdd = $daily_reading_utilities_data['current'][$time]['hdd'];
			} else {
			    $current_cdd = 0;
			    $current_hdd = 0;
			}
			/* ============================= CDD - HDD values ============================== */
			/* ============================= Utilities values ============================== */
			//Previous year
			if (array_key_exists($time, $utility_title_data['previous'])) {
			    $utility_value_previous = $utility_title_data['previous'][$time][$utility_select[0]]['value'];
			} else {
			    $utility_value_previous = 0;
			}
			//Current year
			if (array_key_exists($time, $utility_title_data['current'])) {
			    $utility_value_current = $utility_title_data['current'][$time][$utility_select[0]]['value'];
			} else {
			    $utility_value_current = 0;
			}
			/* ============================= Utilities values ============================== */
			/* ============================= Occupancy values ============================== */
			//Previous year
			if (array_key_exists($time, $daily_reading_utilities_data['previous'])) {
			    $occupancy_previous = round(($daily_reading_utilities_data['previous'][$time]['total_room_night'] / $room_keys) * 100, $decimal_point);
			} else {
			    $occupancy_previous = 0;
			}
			//Current year
			if (array_key_exists($time, $daily_reading_utilities_data['current'])) {
			    $occupancy_current = round(($daily_reading_utilities_data['current'][$time]['total_room_night'] / $room_keys) * 100, $decimal_point);
			} else {
			    $occupancy_current = 0;
			}
			/* ============================= Occupancy values ============================== */
			/* ============================= Chart Data ==================================== */
			//chart data for cdd hdd
			$chartData_cdd_hdd[] = [
			    $time,
			    (float) $utility_value_previous,
			    (float) $utility_value_current,
			    /* (float) $previous_cdd,
			      (float) $current_cdd,
			      (float) $previous_hdd,
			      (float) $current_hdd, */
			];
			//chart data for Occupancy
			$chartData_occupancy[] = [
			    $time,
			    (float) $utility_value_previous,
			    (float) $utility_value_current,
			    /* (float) $occupancy_previous,
			      (float) $occupancy_current, */
			];
			/* ============================= Chart Data ============================== */
		    }
		}
	    } else {
		/* =================== chart data iitialization ================= */
		$chartData_cdd_hdd = [];
		$chartData_occupancy = [];
		$chartData_titles = [];
		$chartData_cdd_hdd[0] = ['Time'];
		$chartData_occupancy[0] = ['Time'];
		foreach ($utility_select as $utilityTitleId) {
		    //array_push($chartData_cdd_hdd[0],$utilityTitles[$utilityTitleId]['name'] . " - " . ($year - 1),$utilityTitles[$utilityTitleId]['name'] . " - " . ($year));
		    array_push($chartData_cdd_hdd[0], $utilityTitles[$utilityTitleId]['name']);
		    array_push($chartData_occupancy[0], $utilityTitles[$utilityTitleId]['name']);
		    $chartData_titles[$utilityTitleId]['name'] = $utilityTitles[$utilityTitleId]['name'];
		    $chartData_titles[$utilityTitleId]['parent'] = $utilityTitles[$utilityTitleId]['parent'];
		}
		// array_push($chartData_cdd_hdd[0], 'CDD', 'HDD');
		// array_push($chartData_occupancy[0], 'Occupancy');
		/* =================== chart data iitialization ================= */
		for ($i = 0; $i < 24; $i++) {
		    for ($j = 0; $j < count($quarterHours); $j++) {
			$time = strval($i . ":" . $quarterHours[$j]);
			if ($i < 10) {
			    $time = "0" . $time;
			}
			/* ============================= CDD - HDD values ============================== */
			/* //Previous year
			  if (array_key_exists($time, $daily_reading_utilities_data['previous'])) {
			  $previous_cdd = $daily_reading_utilities_data['previous'][$time]['cdd'];
			  $previous_hdd = $daily_reading_utilities_data['previous'][$time]['hdd'];
			  } else {
			  $previous_cdd = 0;
			  $previous_hdd = 0;
			  }
			  //Current year
			  if (array_key_exists($time, $daily_reading_utilities_data['current'])) {
			  $current_cdd = $daily_reading_utilities_data['current'][$time]['cdd'];
			  $current_hdd = $daily_reading_utilities_data['current'][$time]['hdd'];
			  } else {
			  $current_cdd = 0;
			  $current_hdd = 0;
			  } */
			/* ============================= CDD - HDD values ============================== */
			/* ============================= Occupancy values ============================== */
			//Previous year
			/* if (array_key_exists($time, $daily_reading_utilities_data['previous'])) {
			  $occupancy_previous = round(($daily_reading_utilities_data['previous'][$time]['total_room_night'] / $room_keys) * 100, $decimal_point);
			  } else {
			  $occupancy_previous = 0;
			  }
			  //Current year
			  if (array_key_exists($time, $daily_reading_utilities_data['current'])) {
			  $occupancy_current = round(($daily_reading_utilities_data['current'][$time]['total_room_night'] / $room_keys) * 100, $decimal_point);
			  } else {
			  $occupancy_current = 0;
			  } */
			/* ============================= Occupancy values ============================== */
			/* ============================= Utilities values ============================== */
			$tempHDD = $tempOcu = array();
			array_push($tempHDD, "$time");
			array_push($tempOcu, "$time");
			foreach ($data['utility_select'] as $selectedID) {
			    /* //Previous year
			      if (array_key_exists($time, $utility_title_data['previous'])) {
			      if(isset($utility_title_data['previous'][$time][$selectedID])) {
			      array_push($tempHDD,$utility_title_data['previous'][$time][$selectedID]['value']);
			      } else {
			      array_push($tempHDD,0);
			      }
			      } else {
			      array_push($tempHDD,0);
			      } */
			    //Current year
			    if (array_key_exists($time, $utility_title_data['current'])) {
				if (isset($utility_title_data['current'][$time][$selectedID])) {
				    array_push($tempHDD, (float) $utility_title_data['current'][$time][$selectedID]['value']);
				    array_push($tempOcu, (float) $utility_title_data['current'][$time][$selectedID]['value']);
				} else {
				    array_push($tempHDD, 0);
				    array_push($tempOcu, 0);
				}
			    } else {
				array_push($tempHDD, 0);
				array_push($tempOcu, 0);
			    }
			}
			//array_push($tempHDD,(float) $previous_cdd, (float) $current_cdd, (float) $previous_hdd, (float) $current_hdd);
			// array_push($tempHDD, (float) $current_cdd, (float) $current_hdd);
			array_push($tempOcu, (float) $occupancy_current);
			$chartData_cdd_hdd[] = $tempHDD;
			// $chartData_occupancy[] = $tempOcu;
			/* ============================= Utilities values ============================== */
		    }
		}
	    }
	}
	$this->utilities_model->utilities_date = $date;
	$this->utilities_model->utilities_month = $month;
	$this->utilities_model->utilities_year = $year;
	$template = 'admin_daily_metering';
	// Prepare data
	$data['month'] = $month;
	$data['year'] = $year;
	$data['last_year'] = $last_year;
	$data['to_date'] = $to_date;
	$data['report_data'] = $report_data;
	$data['current_year_static_data'] = $selected_month_year_static_data;
	$data['last_year_static_data'] = $last_month_year_static_data;
	$data['utilityArray'] = $utilityArray;
	$data['utilityTitlesArray'] = $utilityTitlesArray;
	$data['chartData_titles'] = $chartData_titles;
	$data['utilities_month'] = $month;
	$data['utilities_year'] = $year;
	$data['utilities_date'] = $date;
	$data['utility_select'] = $utility_select;
	$data['date_id'] = $date_id;
	if ($this->input->post('chart')) {
	    $data['chartData_cdd_hdd'] = $chartData_cdd_hdd;
	    $data['chartData_occupancy'] = $chartData_occupancy;
	    $data['utilityTitles'] = $utilityTitles;
	    if (!empty($utilityTitles)) {
		if (count($data['utility_select']) == 1) {
		    $template = 'admin_hourly_chart_single';
		} else {
		    $template = 'admin_hourly_chart';
		}
	    }
	}
	// generate excel report
	$view_type = $this->input->post('view_type', '');
	if (!$this->input->post('chart') && $view_type == 'excel') {
	    require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	    $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
	    $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
	    $optioncurrencyvalue = array('currency' => true);
	    $this->lang->load('sites/sites', 'english');
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("HEP")
		->setTitle("Excel Report")
		->setKeywords("Excel Report");
	    // Add logo
	    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	    } else {
		$site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	    }
	    $objDrawing = new PHPExcel_Worksheet_Drawing();
	    $objDrawing->setName('Logo');
	    $objDrawing->setDescription('Logo');
	    $objDrawing->setPath($site_logo);
	    $objDrawing->setCoordinates('A1');
	    $objDrawing->setHeight(100); // logo height
	    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	    // Prepare excel data
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $site_detail['site_location_name']);
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B4', $montharray[$month] . ' - ' . $year)
		->setCellValue('C4', $montharray[$month] . ' - ' . $last_year)
		->setCellValue('D4', 'Difference v/s Last Year');
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A5', "DAILY METERING")
		->setCellValue('B5', "# Date - $date")
		->setCellValue('D5', 'Value')
		->setCellValue('E5', '%');
	    // Calculation
	    /* $last_year_deference  = 0;
	      $last_year_percantage = 0;
	      $last_year_deference  = $data['current_year_static_data']['total_room_night'] - $data['last_year_static_data']['total_room_night'];
	      $last_year_percantage = (($last_year_deference * 100) / $data['current_year_static_data']['total_room_night']);
	      $objPHPExcel->setActiveSheetIndex(0)
	      ->setCellValue('A6', "Room Nights")
	      ->setCellValue('B6', number_format($data['current_year_static_data']['total_room_night']))
	      ->setCellValue('C6', number_format($data['last_year_static_data']['total_room_night']))
	      ->setCellValue('D6', number_format($last_year_deference))
	      ->setCellValue('E6', number_format($last_year_percantage)); */
	    // Calculation
	    /* $last_year_cdd_deference  = 0;
	      $last_year_cdd_percantage = 0;
	      $last_year_cdd_deference  = $data['current_year_static_data']['cdd'] - $data['last_year_static_data']['cdd'];
	      $last_year_cdd_percantage = (($last_year_cdd_deference * 100) / $data['current_year_static_data']['cdd']);
	      $objPHPExcel->setActiveSheetIndex(0)
	      ->setCellValue('A7', "CDD")
	      ->setCellValue('B7', number_format($data['current_year_static_data']['cdd']))
	      ->setCellValue('C7', number_format($data['last_year_static_data']['cdd']))
	      ->setCellValue('D7', number_format($last_year_cdd_deference))
	      ->setCellValue('E7', number_format($last_year_cdd_percantage)); */
	    // Calculation
	    /* $last_year_hdd_deference  = 0;
	      $last_year_hdd_percantage = 0;
	      $last_year_hdd_deference  = $data['current_year_static_data']['hdd'] - $data['last_year_static_data']['hdd'];
	      $last_year_hdd_percantage = (($last_year_hdd_deference * 100) / $data['current_year_static_data']['hdd']);
	      $objPHPExcel->setActiveSheetIndex(0)
	      ->setCellValue('A8', "HDD")
	      ->setCellValue('B8', number_format($data['current_year_static_data']['hdd']))
	      ->setCellValue('C8', number_format($data['last_year_static_data']['hdd']))
	      ->setCellValue('D8', number_format($last_year_hdd_deference))
	      ->setCellValue('E8', number_format($last_year_hdd_percantage)); */
	    $alphas = range('A', 'Z');
	    $active_row = 6;
	    $active_column = 0;
	    $merge_cells = array();
	    $legent_cells = array();
	    // Display Data
	    $current_year_total = 0;
	    $last_year_total = 0;
	    foreach ($report_data as $utility) {
		if (empty($utility['submission'])) {
		    continue;
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('A' . $active_row, lang('daily_report_title_' . $utility['title']));
		$legent_cells[] = $alphas[$active_column] . $active_row . ':' . $alphas[$active_column + 4] . $active_row;
		$active_row++;
		foreach ($utility['submission'] as $stitle => $submission) {
		    $last_year_deference = 0;
		    $last_year_percantage = 0;
		    $last_year_deference = $submission['current_year_total'] - $submission['last_year_total'];
		    $last_year_percantage = (($last_year_deference * 100) / $submission['current_year_total']);
		    $current_year_total += $submission['current_year_total'];
		    $last_year_total += $submission['last_year_total'];
		    $objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A' . $active_row, $stitle)
			->setCellValue('B' . $active_row, number_format($submission['current_year_total']))
			->setCellValue('C' . $active_row, number_format($submission['last_year_total']))
			->setCellValue('D' . $active_row, number_format($last_year_deference))
			->setCellValue('E' . $active_row, number_format($last_year_percantage));
		    $active_row++;
		}
	    }
	    // Excel cell formation
	    // Merge
	    foreach ($merge_cells as $cell) {
		$objPHPExcel->getActiveSheet()->mergeCells($cell);
	    }
	    $objPHPExcel->getActiveSheet()->mergeCells('B1:D1');
	    $objPHPExcel->getActiveSheet()->mergeCells('B5:C5');
	    $objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
	    // Style
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $style = array('font' => array('bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle("A{$active_row}:H{$active_row}")->applyFromArray($style);
	    $style = array('font' => array('size' => 20, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($style);
	    $style = array('font' => array('size' => 14, 'bold' => true));
	    $objPHPExcel->getActiveSheet()->getStyle('A5:H5')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B6:E100')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B5:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $style = array('font' => array('size' => 16, 'bold' => true, 'color' => array('rgb' => 'ffffff')));
	    $objPHPExcel->getActiveSheet()->getStyle('B4:E4')->applyFromArray($style);
	    $objPHPExcel->getActiveSheet()->getStyle('B4:E4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    $legent_utility_style = array('font' => array('color' => array('rgb' => 'ffffff')));
	    foreach ($legent_cells as $cell) {
		$objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($legent_utility_style);
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('666666');
	    }
	    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	    // Borders
	    $objPHPExcel->getActiveSheet()->getStyle("A4:A" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("C4:C" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("E4:E" . ($active_row - 1))->applyFromArray(
		array(
		    'borders' => array(
			'right' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A5:E5")->applyFromArray(
		array(
		    'borders' => array(
			'top' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
			'bottom' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ":E" . $active_row)->applyFromArray(
		array(
		    'borders' => array(
			'top' => array(
			    'style' => PHPExcel_Style_Border::BORDER_THIN,
			    'color' => array('rgb' => '000000'),
			),
		    ),
		)
	    );
	    $file_name = 'Daily_metering_Report_' . $date . '_' . $month . '_' . $year . '.xls';
	    ob_end_clean();
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="' . $file_name . '"');
	    header('Cache-Control: max-age=0');
	    // If you're serving to IE 9, then the following may be needed
	    header('Cache-Control: max-age=1');
	    // If you're serving to IE over SSL, then the following may be needed
	    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	    header('Pragma: public'); // HTTP/1.0
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	$this->theme->view($data, $template);
    }

    public function annual_excel_report($reportType = '', $selected_region, $selectedYear = '')
    {
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];
	// $year = (int) date('Y');
	$year = (int) $selectedYear;
	$current_month = (int) date('m');
	$this->load->model('sites/sites_model');
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	if (!$year or empty($year) or $year <= 0 or empty($this->session->userdata[$this->section_name]['user_id'])) {
	    redirect("/dashboard");
	}
	$data = array();
	$data['currency'] = "local";
	if ($this->input->post('currency')) {
	    $data['currency'] = $this->input->post('currency');
	}

	$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');

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
	$filters['current_year'] = $year;
	$previousmonthdata = date("m/Y", strtotime(date('Y-m') . " -1 months"));
	$previousdateexplode = explode('/', $previousmonthdata);
	$filters['previous_month'] = (int) $previousdateexplode[0];
	$filters['previous_year'] = $previousdateexplode[1];
	// FIlters for comparisional bar chart
	$filters_comparision_chart = array();
	$startdate = '1/' . $selectedYear;
	$enddate = '12/' . $selectedYear;
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
	/*$startdate_pre = '1/' . date("Y", strtotime(date('Y') . " -1 years"));
	$enddate_pre = '12/' . date("Y", strtotime(date('Y') . " -1 years"));*/
	$startdate_pre = '1/' . ($selectedYear);
	$enddate_pre = '12/' . ($selectedYear);
	$startdateexplode_pre = explode('/', $startdate_pre);
	$enddateexplode_pre = explode('/', $enddate_pre);
	$filters_comparision_chart_pre['startdate'] = (isset($startdate_pre)) ? $startdate_pre : '';
	$filters_comparision_chart_pre['enddate'] = (isset($enddate_pre)) ? $enddate_pre : '';
	$filters_comparision_chart_pre['start_month'] = (isset($startdateexplode_pre[0])) ? (int) $startdateexplode_pre[0] : '';
	$filters_comparision_chart_pre['start_year'] = (isset($startdateexplode_pre[1])) ? $startdateexplode_pre[1] : '';
	$filters_comparision_chart_pre['end_month'] = (isset($enddateexplode_pre[0])) ? (int) $enddateexplode_pre[0] : '';
	$filters_comparision_chart_pre['end_year'] = (isset($enddateexplode_pre[1])) ? $enddateexplode_pre[1] : '';
	$currentYear = $year;
	$currentMonth = intval(date('m'));
	if ($currentMonth == 1) {
	    $currentYear = $currentYear - 1;
	    $currentMonth = 12;
	}
	$filters_comparision_chart_pre['currentYear'] = $currentYear;
	$filters_comparision_chart_pre['currentMonth'] = $currentMonth;
	// Prepare sheet title and data filters
	$reportSheetTitle = '';
	if ($reportType == 'ytd') {
	    unset($filters_comparision_chart_pre);
	    $filters_comparision_chart_pre['start_year'] = date('Y');
	    $filters_comparision_chart_pre['start_month'] = 1;
	    $filters_comparision_chart_pre['end_year'] = date('Y');
	    $filters_comparision_chart_pre['end_month'] = date('m') - 1;
	    $filters_comparision_chart_pre['report_type'] = 'ytd';
	    $firstDay = date('01/01/Y');
	    $lastDay = date('d/m/Y', strtotime('last day of previous month'));
	    $reportSheetTitle = sprintf(lang('annual_group_report_ytd_title'), $firstDay, $lastDay);
	} else if ($reportType == 'month') {
	    unset($filters_comparision_chart_pre);
	    $startdate = $this->input->post('startdate', '');
	    $startdateexplode = explode("/", $startdate);
	    $filters_comparision_chart_pre['report_type'] = 'month';
	    $filters_comparision_chart_pre['start_year'] = $filters_comparision_chart_pre['end_year'] = (isset($startdateexplode[1])) ? (int) $startdateexplode[1] : '';
	    $filters_comparision_chart_pre['start_month'] = $filters_comparision_chart_pre['end_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
	    $startdate = $this->input->post('startdate', '');
	    $startdateexplode = explode('/', $startdate);
	    $monthName = date('F', mktime(0, 0, 0, (int) $startdateexplode[0], 10));
	    $reportSheetTitle = sprintf(lang('annual_group_report_month_title'), $monthName, $startdateexplode[1]);
	}
	// $site_details = $this->sites_model->get_all_site_listing_for_users_orderby($site_id, $role_id, $user_id = 0);
	$site_details = $this->sites_model->get_all_site_listing_for_users_orderby_with_region($site_id, $role_id, $user_id = 0, $selected_region);

	$all_site_electricity = 0;
	$all_site_fuel = 0;
	$all_site_natural_gas = 0;
	$all_site_lpg = 0;
	$all_site_district_cooling = 0;
	$all_site_district_heating = 0;
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Excel Report")
	    ->setKeywords("Excel Report");
	// Add logo
	if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo']) && !is_dir(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'])) {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_detail['site_logo'];
	} else {
	    $site_logo = BASE_PATH_CUSTOM . "/assets/uploads/default-site-logo.png";
	}
	$hotel = $this->reportscron_model->getHotel();
	$hotelLogo = $hotel['hotel_logo'];
	if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $hotelLogo)) {
	    $header_logo_src = BASE_PATH_CUSTOM . "/assets/uploads/" . $hotelLogo;
	} else {
	    $header_logo_src = BASE_PATH_CUSTOM . "/" . NOT_AVAILABLE_SITE_LOGO;
	}
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Logo');
	$objDrawing->setDescription('Logo');
	$objDrawing->setPath($header_logo_src);
	$objDrawing->setCoordinates('B1');
	// set resize to false first
	$objDrawing->setResizeProportional(false);
	$objDrawing->setWidth(50);
	$objDrawing->setHeight(85);
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(85);
	// set report title
	$report_title = "Energy and Carbon Report Year : " . ($selectedYear);
	if ($reportType == 'ytd' || $reportType == 'month') {
	    $report_title = $reportSheetTitle;
	}
	$objPHPExcel->getActiveSheet()->mergeCells("C1:F1");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $report_title);
	$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true)->setSize(16);
	$objPHPExcel->getActiveSheet()->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$alphaInc = 0;
	$fullAlphaArray = array_merge(range('A', 'Z'));
	$percentage_decimal = 3;
	$value_decimal = 4;
	$objPHPExcel->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B3:F3')->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	foreach (range('B', 'F') as $columnID) {
	    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
		->setAutoSize(true);
	}
	$co2text = 'CO<sub>2</sub> (KgCO<sub>2</sub>e)';
	/*
	  $objRichText = new PHPExcel_RichText();
	  $objRichText->createText('CO'); // CO2 (Kg CO2e)
	  $objSubscript = $objRichText->createTextRun('2');
	  $objSubscript->getFont()->setSubScript(true);
	  $str1 = $objRichText;
	  $objRichText1 = new PHPExcel_RichText();
	  $objRichText1->createText('(Kg CO'); // CO2 (Kg CO2e)
	  $objSubscript = $objRichText1->createTextRun('2');
	  $objSubscript->getFont()->setSubScript(true);
	  $str2 = $objRichText1; */
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('B3', "Site")
	    ->setCellValue('C3', "Utility")
	    ->setCellValue('D3', 'Consumption (KWH)')
	    ->setCellValue('E3', '%')
	    ->setCellValue('F3', 'CO2 (Kg CO2e) ');
	// CO<sub>2</sub> (KgCO<sub>2</sub>e)
	$row = 4;
	$finalArray = $utility_array = array();
	foreach ($site_details as $site_detail) {
	    $site_id = $site_detail['id'];
	    $site_name = $site_detail['site_location_name'];
	    $filters_comparision_chart_pre['site_id'] = $site_id;
	    if ($data['currency'] == "base") {
		$utility_cost_chart_results_pre = $this->reports_forex_model->utilityCostBarChartExcelAnnualReport($filters_comparision_chart_pre);
	    } else {
		$utility_cost_chart_results_pre = $this->reports_model->utilityCostBarChartExcelAnnualReportForTypeBased($filters_comparision_chart_pre);
	    }
	    // KWH Pie chart for current year
	    $filters_pre['report_year'] = ($selectedYear);
	    $filters_pre['max_month_id'] = 12;
	    $filters_pre['site_id'] = $site_id;
	    $filters['report_year_pre'] = $filters_pre['report_year'];
	    if ($reportType == 'ytd') {
		$filters_pre['report_year'] = date('Y');
		$filters_pre['max_month_id'] = date('m') - 1;
		$filters_pre['report_type'] = 'ytd';
	    } else if ($reportType == 'month') {
		$filters_pre['report_year'] = $filters_comparision_chart_pre['start_year'];
		$filters_pre['max_month_id'] = $filters_comparision_chart_pre['start_month'];
		$filters_pre['report_type'] = 'month';
	    }
	    $kwh_report_results_pre = $this->reports_model->kwhUnitBasedReportForCurrentYearExcelAnnualReportForMonthly($filters_pre);
	    if (!empty($kwh_report_results_pre)) {
		$data['kwh_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? ($kwh_report_results_pre['electricity'] * $electricity_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? ($kwh_report_results_pre['fuel'] * $fuel_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? ($kwh_report_results_pre['lpg'] * $lpg_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? ($kwh_report_results_pre['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? ($kwh_report_results_pre['heating_district'] * $heating_district_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? ($kwh_report_results_pre['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	    } else {
		$data['kwh_pie_chart_pre'] = array();
	    }
	    // Consumption
	    $kwh_pie_chart_pre = $data['kwh_pie_chart_pre'];
	    $electricity_value = round($kwh_pie_chart_pre['electricity']);
	    $fuel_value = round($kwh_pie_chart_pre['fuel']);
	    $lpg_value = round($kwh_pie_chart_pre['lpg']);
	    $natural_gas_value = round($kwh_pie_chart_pre['natural_gas']);
	    $heating_district_value = round($kwh_pie_chart_pre['heating_district']);
	    $cooling_district_value = round($kwh_pie_chart_pre['cooling_district']);
	    $utility_kwh_total = ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	    // check share %
	    $electricity_share = (round(($electricity_value * 100) / $utility_kwh_total, 1) > 0) ? round(($electricity_value * 100) / $utility_kwh_total, 1) : 0;
	    $fuel_share = (round(($fuel_value * 100) / $utility_kwh_total, 1) > 0) ? round(($fuel_value * 100) / $utility_kwh_total, 1) : 0;
	    $lpg_share = (round(($lpg_value * 100) / $utility_kwh_total, 1) > 0) ? round(($lpg_value * 100) / $utility_kwh_total, 1) : 0;
	    $natural_gas_share = (round(($natural_gas_value * 100) / $utility_kwh_total, 1) > 0) ? round(($natural_gas_value * 100) / $utility_kwh_total, 1) : 0;
	    $heating_district_share = (round(($heating_district_value * 100) / $utility_kwh_total, 1) > 0) ? round(($heating_district_value * 100) / $utility_kwh_total, 1) : 0;
	    $cooling_district_share = (round(($cooling_district_value * 100) / $utility_kwh_total, 1) > 0) ? round(($cooling_district_value * 100) / $utility_kwh_total, 1) : 0;
	    $total_sum_data_electricity = 0;
	    $total_sum_data_fuel = 0;
	    $total_sum_data_lpg = 0;
	    $total_sum_data_natural_gas = 0;
	    $total_sum_data_heating_district = 0;
	    $total_sum_data_cooling_district = 0;
	    /* new added */
	    $totalElectricity_utility_cost_pre = 0;
	    $totalFuel_utility_cost_pre = 0;
	    $totalLpg_utility_cost_pre = 0;
	    $totalNaturalGas_utility_cost_pre = 0;
	    $totalWater_utility_cost_pre = 0;
	    $totalHeatingDistrict_utility_cost_pre = 0;
	    $totalCoolingDistrict_utility_cost_pre = 0;
	    /* new added */
	    foreach ($utility_cost_chart_results_pre as $key => $value) {
		if (isset($data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    $value = array_map('floatval', $value);
		    $value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		    $value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];
		    $value['lpg'] = $value['lpg'] + $value['lpg_fixed_cost'];
		    $value['natural_gas'] = $value['natural_gas'] + $value['natural_gas_fixed_cost'];
		    $value['water'] = $value['water'] + $value['water_fixed_cost'];
		    $totalElectricity_utility_cost_pre += $value['electricity'];
		    $totalFuel_utility_cost_pre += $value['fuel'];
		    $totalLpg_utility_cost_pre += $value['lpg'];
		    $totalNaturalGas_utility_cost_pre += $value['natural_gas'];
		    $totalWater_utility_cost_pre += $value['water'];
		    $totalHeatingDistrict_utility_cost_pre += $value['heating_district'];
		    $totalCoolingDistrict_utility_cost_pre += $value['cooling_district'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['electricity_consumption'])) ? $value['electricity_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['onsite_generator'] = (!empty($value['onsite_generator'])) ? $value['onsite_generator'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['renewable_energy'] = (!empty($value['renewable_energy'])) ? $value['renewable_energy'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['onsite_generator_fuel_oil'] = (!empty($value['onsite_generator_fuel_oil'])) ? $value['onsite_generator_fuel_oil'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['onsite_generator_natural_gas'] = (!empty($value['onsite_generator_natural_gas'])) ? $value['onsite_generator_natural_gas'] : 0;
		    if (!empty($value['total_electricity_kwh'])) {
			$electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];
		    } else {
			$electricity_tariff_cost_per_kwh = 0;
		    }
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;
		    $days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
		}
	    }
	    /* new added*/
	    //Add total values in data array
	    $data['totalElectricity_utility_cost_pre'] = $totalElectricity_utility_cost_pre;
	    $data['totalFuel_utility_cost_pre'] = $totalFuel_utility_cost_pre;
	    $data['totalLpg_utility_cost_pre'] = $totalLpg_utility_cost_pre;
	    $data['totalNaturalGas_utility_cost_pre'] = $totalNaturalGas_utility_cost_pre;
	    $data['totalWater_utility_cost_pre'] = $totalWater_utility_cost_pre;
	    $data['totalHeatingDistrict_utility_cost_pre'] = $totalHeatingDistrict_utility_cost_pre;
	    $data['totalCoolingDistrict_utility_cost_pre'] = $totalCoolingDistrict_utility_cost_pre;
	    /* new added*/
	    // calculation from view
	    unset($resultkeys_pre);
	    unset($startmonthsarray_pre);
	    unset($utility_cost_chart_pre);
	    $utility_cost_chart_pre = $data['utility_cost_chart_pre'][$site_id];
	    $filters['filters_comparision_chart_pre'] = $filters_comparision_chart_pre;
	    $startmonthsarray_pre = array();
	    if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) {
		// If start and end year is same
		for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']["end_month"]; $i++) {
		    $startmonthsarray_pre[] = $i;
		}
		$resultkeys_pre = array();
		$resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;
	    } /* else { // If start and end year is not same
	      for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= 12; $i++) {
	      $startmonthsarray_pre[] = $i;
	      }
	      echo "here else ";
	      for ($i = 1; $i <= $filters['filters_comparision_chart_pre']['end_month']; $i++) {
	      $endmonthsarray_pre[] = $i;
	      }
	      $resultkeys_pre = array();
	      $resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;
	      $resultkeys_pre[$filters['filters_comparision_chart_pre']["end_year"]] = $endmonthsarray_pre;
	      } */
	    $total_months = 0;
	    $total_sum_data_electricity = 0;
	    $total_sum_data_fuel = 0;
	    $total_sum_data_lpg = 0;
	    $total_sum_data_natural_gas = 0;
	    $total_sum_data_heating_district = 0;
	    $total_sum_data_cooling_district = 0;
	    $total_sum_data_water = 0;
	    $total_sum_data_cdd = 0;
	    $total_sum_data_hdd = 0;
	    $total_sum_data_occupancy = 0;
	    $total_sum_data_room_night = 0;
	    $total_sum_data_electricity_kwh = 0;
	    foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Current year data
		    $data_electricity = 0;
		    $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year]['onsite_generator'] - $utility_cost_chart_pre[$month][$year]['renewable_energy']) : 0;
		    $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel_consumption'])) ? ($utility_cost_chart_pre[$month][$year]['fuel_consumption'] - $utility_cost_chart_pre[$month][$year]['onsite_generator_fuel_oil']) : 0;
		    $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg_consumption'])) ? $utility_cost_chart_pre[$month][$year]['lpg_consumption'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas_consumption'])) ? ($utility_cost_chart_pre[$month][$year]['natural_gas_consumption'] - $utility_cost_chart_pre[$month][$year]['onsite_generator_natural_gas']) : 0;
		    $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['heating_district_consumption'] : 0;
		    $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['cooling_district_consumption'] : 0;
		    $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water_consumption'])) ? $utility_cost_chart_pre[$month][$year]['water_consumption'] : 0;
		    $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd'])) ? $utility_cost_chart_pre[$month][$year]['cdd'] : 0;
		    $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd'])) ? $utility_cost_chart_pre[$month][$year]['hdd'] : 0;
		    $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy'])) ? $utility_cost_chart_pre[$month][$year]['occupancy'] : 0;
		    $data_room_night = (!empty($utility_cost_chart_pre[$month][$year]['room_night'])) ? $utility_cost_chart_pre[$month][$year]['room_night'] : 0;
		    $data_electricity_tariff = (!empty($utility_cost_chart_pre[$month][$year]['electricity_tariff'])) ? $utility_cost_chart_pre[$month][$year]['electricity_tariff'] : 0;
		    $data_electricity_kwh = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] : 0;
		    $data_electricity = round($data_electricity * $site_detail['electricity_emission_factor'], 2);
		    $data_fuel = round($data_fuel * $site_detail['fuel_emission_factor'], 2);
		    $data_lpg = round($data_lpg * $site_detail['lpg_emission_factor'], 2);
		    $data_natural_gas = round($data_natural_gas * $site_detail['natural_gas_emission_factor'], 2);
		    $data_heating_district = round($data_heating_district * $site_detail['district_heating_emission_factor'], 2);
		    $data_cooling_district = round($data_cooling_district * $site_detail['district_cooling_emission_factor'], 2);
		    $data_water = 0; // There is no calculation for water data
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
		    // Total sum Current year data
		    $total_sum_data_electricity += $data_electricity;
		    $total_sum_data_fuel += $data_fuel;
		    $total_sum_data_lpg += $data_lpg;
		    $total_sum_data_natural_gas += $data_natural_gas;
		    $total_sum_data_heating_district += $data_heating_district;
		    $total_sum_data_cooling_district += $data_cooling_district;
		    $total_sum_data_water += $data_water;
		    $total_sum_data_cdd += $data_cdd;
		    $total_sum_data_hdd += $data_hdd;
		    $total_sum_data_occupancy += $data_occupancy;
		    $total_sum_data_room_night += $data_room_night;
		    //$total_sum_data_electricity_tariff += $data_electricity_tariff;
		    $total_sum_data_electricity_kwh += $data_electricity_kwh;
		    $total_months++;
		}
	    }
	    if ($total_sum_data_electricity_kwh > 0) {
		$total_sum_data_electricity_tariff = ($total_sum_data_electricity / $total_sum_data_electricity_kwh);
	    } else {
		$total_sum_data_electricity_tariff = 0;
	    }
	    $total_sum_data_sum = ($total_sum_data_electricity + $total_sum_data_fuel + $total_sum_data_lpg + $total_sum_data_natural_gas + $total_sum_data_water + $total_sum_data_heating_district + $total_sum_data_cooling_district);
	    $total_consumption = 0;
	    $total_co2 = 0;
	    $total_percentage = 0;
	    $total_utility = 0;
	    $style = array('font' => array('bold' => true, 'color' => array('rgb' => '24478f')));
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->applyFromArray($style);
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $row, $site_name);
	    // fro alignment
	    $objPHPExcel->getActiveSheet()->getStyle('D1:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('E1:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('F1:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true)->setSize(12);
	    $b_row_start = $row;
	    $build_up_area = $site_detail['site_builtup_area'];
	    if ($site_detail['show_utility_electricity']) {
		array_push($utility_array, 'Electricity');
		if (isset($total_sum_data_electricity)) {
		    $total_consumption += $electricity_value;
		    $total_percentage += $electricity_share;
		    $total_co2 += round($total_sum_data_electricity);
		    $total_utility = $total_utility + 1;
		    $consumption = $electricity_value / 1000;
		    $co2 = $total_sum_data_electricity / 1000;
		    $finalArray[$site_name]['Electricity']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['Electricity']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['Electricity']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['Electricity']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "Electricity")
		    ->setCellValue('D' . $row, numb_format_without_currency($electricity_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($electricity_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_electricity), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_fuel_oil']) {
		array_push($utility_array, 'Fuel');
		if (isset($total_sum_data_fuel)) {
		    $total_consumption += $fuel_value;
		    $total_percentage += $fuel_share;
		    $total_co2 += round($total_sum_data_fuel);
		    $total_utility = $total_utility + 1;
		    $consumption = $fuel_value / 1000;
		    $co2 = $total_sum_data_fuel / 1000;
		    $finalArray[$site_name]['Fuel']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['Fuel']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['Fuel']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['Fuel']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "Fuel")
		    ->setCellValue('D' . $row, numb_format_without_currency($fuel_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($fuel_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_fuel), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_lpg']) {
		array_push($utility_array, 'LPG');
		if (isset($total_sum_data_lpg)) {
		    $total_consumption += $lpg_value;
		    $total_percentage += $lpg_share;
		    $total_co2 += round($total_sum_data_lpg);
		    $total_utility = $total_utility + 1;
		    $consumption = $lpg_value / 1000;
		    $co2 = $total_sum_data_lpg / 1000;
		    $finalArray[$site_name]['LPG']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['LPG']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['LPG']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['LPG']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "LPG")
		    ->setCellValue('D' . $row, numb_format_without_currency($lpg_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($lpg_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_lpg), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_natural_gas']) {
		array_push($utility_array, 'Natural Gas');
		if (isset($total_sum_data_natural_gas)) {
		    $total_consumption += $natural_gas_value;
		    $total_percentage += $natural_gas_share;
		    $total_co2 += round($total_sum_data_natural_gas);
		    $total_utility = $total_utility + 1;
		    $consumption = $natural_gas_value / 1000;
		    $co2 = $total_sum_data_natural_gas / 1000;
		    $finalArray[$site_name]['Natural Gas']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['Natural Gas']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['Natural Gas']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['Natural Gas']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "Natural Gas")
		    ->setCellValue('D' . $row, numb_format_without_currency($natural_gas_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($natural_gas_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_natural_gas), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_district_cooling']) {
		array_push($utility_array, 'District Cooling');
		if (isset($total_sum_data_cooling_district)) {
		    $total_consumption += $cooling_district_value;
		    $total_percentage += $cooling_district_share;
		    $total_co2 += round($total_sum_data_cooling_district);
		    $total_utility = $total_utility + 1;
		    $consumption = $cooling_district_value / 1000;
		    $co2 = $total_sum_data_cooling_district / 1000;
		    $finalArray[$site_name]['District Cooling']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['District Cooling']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['District Cooling']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['District Cooling']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "District Cooling")
		    ->setCellValue('D' . $row, numb_format_without_currency($cooling_district_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($cooling_district_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_cooling_district), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_district_heating']) {
		array_push($utility_array, 'District Heating');
		if (isset($total_sum_data_heating_district)) {
		    $total_consumption += $heating_district_value;
		    $total_percentage += $heating_district_share;
		    $total_co2 += round($total_sum_data_heating_district);
		    $total_utility = $total_utility + 1;
		    $consumption = $heating_district_value / 1000;
		    $co2 = $total_sum_data_heating_district / 1000;
		    $finalArray[$site_name]['District Heating']['Consumption'] = round($consumption, 2);
		    $finalArray[$site_name]['District Heating']['Consumption Intensity'] = round($consumption * 1000 / $build_up_area, 2);
		    $finalArray[$site_name]['District Heating']['CO2'] = round($co2, 2);
		    $finalArray[$site_name]['District Heating']['CO2 Intensity'] = round($co2 * 1000 / $build_up_area, 2);
		}
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('C' . $row, "District Heating")
		    ->setCellValue('D' . $row, numb_format_without_currency($heating_district_value, 2))
		    ->setCellValue('E' . $row, numb_format_without_currency($heating_district_share, 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_sum_data_heating_district), 2));
		$row++;
	    }
	    $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	    $objPHPExcel->getActiveSheet()->getStyle('C' . $row . ':F' . $row)->applyFromArray($styleArray);
	    $objPHPExcel->getActiveSheet()->getStyle('B3:F3')->applyFromArray($styleArray);
	    $BStyle = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $b_row_start . ':B' . $row)->applyFromArray($BStyle);
	    $objPHPExcel->getActiveSheet()->getStyle('C' . $b_row_start . ':C' . $row)->applyFromArray($BStyle);
	    $objPHPExcel->getActiveSheet()->getStyle('D' . $b_row_start . ':D' . $row)->applyFromArray($BStyle);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $b_row_start . ':E' . $row)->applyFromArray($BStyle);
	    $objPHPExcel->getActiveSheet()->getStyle('F' . $b_row_start . ':F' . $row)->applyFromArray($BStyle);
	    if ($total_utility != 0) {
		$objPHPExcel->getActiveSheet()->getStyle('C' . $row . ':F' . $row)->getFont()->setBold(true)->setSize(12);
		$objPHPExcel->setActiveSheetIndex(0)
		    ->setCellValue('B' . $row, '')
		    ->setCellValue('C' . $row, "TOTAL")
		    ->setCellValue('D' . $row, numb_format_without_currency(round($total_consumption), 2))
		    ->setCellValue('E' . $row, numb_format_without_currency(round($total_percentage), 2))
		    ->setCellValue('F' . $row, numb_format_without_currency(round($total_co2), 2));
		$row++;
	    }
	    if ($site_detail['show_utility_electricity']) {
		$all_site_electricity_value += $electricity_value;
		$all_site_electricity_share += $electricity_share;
		$all_site_electricity += $total_sum_data_electricity;
	    }
	    if ($site_detail['show_utility_fuel_oil']) {
		$all_site_fuel_value += $fuel_value;
		$all_site_fuel_share += $fuel_share;
		$all_site_fuel += $total_sum_data_fuel;
	    }
	    if ($site_detail['show_utility_lpg']) {
		$all_site_lpg_value += $lpg_value;
		$all_site_lpg_share += $lpg_share;
		$all_site_lpg += $total_sum_data_lpg;
	    }
	    if ($site_detail['show_utility_natural_gas']) {
		$all_site_natural_gas_value += $natural_gas_value;
		$all_site_natural_gas_share += $natural_gas_share;
		$all_site_natural_gas += $total_sum_data_natural_gas;
	    }
	    if ($site_detail['show_utility_district_heating']) {
		$all_site_district_heating_value += $heating_district_value;
		$all_site_district_heating_share += $heating_district_share;
		$all_site_district_heating += $total_sum_data_heating_district;
	    }
	    if ($site_detail['show_utility_district_cooling']) {
		$all_site_district_cooling_value += $cooling_district_value;
		$all_site_district_cooling_share += $cooling_district_share;
		$all_site_district_cooling += $total_sum_data_cooling_district;
	    }
	} // end of site loop
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('B' . $row, '')
	    ->setCellValue('C' . $row, "")
	    ->setCellValue('D' . $row, "Consumption (GJ)")
	    ->setCellValue('E' . $row, "%")
	    ->setCellValue('F' . $row, "CO2 (Tons CO2e) ");
	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$objPHPExcel->getActiveSheet()->getStyle('D' . $row . ':F' . $row)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D' . $row . ':F' . $row)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	$objPHPExcel->getActiveSheet()->getStyle('D' . $row . ':F' . $row)->getFont()->setBold(true)->setSize(12);
	$row++;
	$start_row = $row;
	$total_utility_sites = 0;
	$utilities_array = array_unique($utility_array);
	$consumptionTotal = 0;
	if (isset($all_site_electricity) && (in_array("Electricity", $utilities_array))) {
	    $consumptionTotal += $all_site_electricity_value;
	}
	if (isset($all_site_fuel) && (in_array("Fuel", $utilities_array))) {
	    $consumptionTotal += $all_site_fuel_value;
	}
	if (isset($all_site_lpg) && (in_array("LPG", $utilities_array))) {
	    $consumptionTotal += $all_site_lpg_value;
	}
	if (isset($all_site_natural_gas) && (in_array("Natural Gas", $utilities_array))) {
	    $consumptionTotal += $all_site_natural_gas_value;
	}
	if (isset($all_site_district_heating) && (in_array("District Heating", $utilities_array))) {
	    $consumptionTotal += $all_site_district_heating_value;
	}
	if (isset($all_site_district_cooling) && (in_array("District Cooling", $utilities_array))) {
	    $consumptionTotal += $all_site_district_cooling_value;
	}
	$consumptionTotal = round($consumptionTotal / 1000, 2);
	if (isset($all_site_electricity) && (in_array("Electricity", $utilities_array))) {
	    $percentage_electricity = $this->calculatePercentage($all_site_electricity_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_electricity_value;
	    $total_percentage_sites += $percentage_electricity;
	    $total_co2_sites += round($all_site_electricity);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "Electricity")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_electricity_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_electricity, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_electricity / 1000, 2), 2));
	    $row++;
	}
	if (isset($all_site_fuel) && (in_array("Fuel", $utilities_array))) {
	    $percentage_fuel = $this->calculatePercentage($all_site_fuel_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_fuel_value;
	    $total_percentage_sites += $percentage_fuel;
	    $total_co2_sites += round($all_site_fuel);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "Fuel")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_fuel_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_fuel, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_fuel / 1000, 2), 2));
	    $row++;
	}
	if (isset($all_site_lpg) && (in_array("LPG", $utilities_array))) {
	    $percentage_lpg = $this->calculatePercentage($all_site_lpg_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_lpg_value;
	    $total_percentage_sites += $percentage_lpg;
	    $total_co2_sites += round($all_site_lpg);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "LPG")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_lpg_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_lpg, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_lpg / 1000, 2), 2));
	    $row++;
	}
	if (isset($all_site_natural_gas) && (in_array("Natural Gas", $utilities_array))) {
	    $percentage_natural_gas = $this->calculatePercentage($all_site_natural_gas_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_natural_gas_value;
	    $total_percentage_sites += $percentage_natural_gas;
	    $total_co2_sites += round($all_site_natural_gas);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "Natural Gas")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_natural_gas_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_natural_gas, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_natural_gas / 1000, 2), 2));
	    $row++;
	}
	if (isset($all_site_district_heating) && (in_array("District Heating", $utilities_array))) {
	    $percentage_district_heating = $this->calculatePercentage($all_site_district_heating_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_district_heating_value;
	    $total_percentage_sites += $percentage_district_heating;
	    $total_co2_sites += round($all_site_district_heating);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "District Heating")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_district_heating_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_district_heating, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_district_heating / 1000, 2), 2));
	    $row++;
	}
	if (isset($all_site_district_cooling) && (in_array("District Cooling", $utilities_array))) {
	    $percentage_district_cooling = $this->calculatePercentage($all_site_district_cooling_value, $consumptionTotal);
	    $total_consumption_sites += $all_site_district_cooling_value;
	    $total_percentage_sites += $percentage_district_cooling;
	    $total_co2_sites += round($all_site_district_cooling);
	    $total_utility_sites = $total_utility_sites + 1;
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $row, '')
		->setCellValue('C' . $row, "District Cooling")
		->setCellValue('D' . $row, numb_format_without_currency(round($all_site_district_cooling_value / 1000, 2), 2))
		->setCellValue('E' . $row, numb_format_without_currency(round($percentage_district_cooling, 2), 2))
		->setCellValue('F' . $row, numb_format_without_currency(round($all_site_district_cooling / 1000, 2), 2));
	    $row++;
	}
	$end_row = $row;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $start_row, 'TOTAL HOTELS');
	$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row)->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row)->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells("B" . ($start_row) . ":B" . ($end_row));
	$objPHPExcel->getActiveSheet()->getStyle('C' . $row . ':F' . $row)->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle("B" . ($start_row))->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'CCCCCC'))));
	$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row . ':F' . $row)->applyFromArray($BStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $row . ':F' . $row)->applyFromArray($BStyle);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row)->applyFromArray($BStyle);
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('C' . $row, "Grand TOTAL")
	    ->setCellValue('D' . $row, numb_format_without_currency(round($total_consumption_sites / 1000, 2), 2))
	    ->setCellValue('E' . $row, numb_format_without_currency(round($total_percentage_sites, 2), 0))
	    ->setCellValue('F' . $row, numb_format_without_currency(round($total_co2_sites / 1000, 2), 2));
	$utilities_array = array_unique($utility_array);
	$newSortedTableStartRow = $row + 4;
	/* Create new Sorted list of MJ and CO2 */
	$objPHPExcel->setActiveSheetIndex(0)
	    ->setCellValue('B' . $newSortedTableStartRow, "Site")
	    ->setCellValue('C' . $newSortedTableStartRow, "Consumption (GJ)")
	    ->setCellValue('D' . $newSortedTableStartRow, "Energy Intensity (MJ/m2)")
	    ->setCellValue('E' . $newSortedTableStartRow, "")
	    ->setCellValue('F' . $newSortedTableStartRow, 'CO2 (Tons CO2e) ')
	    ->setCellValue('G' . $newSortedTableStartRow, 'Carbon Intensity (KgCO2/m2)');
	$objPHPExcel->getActiveSheet()->getStyle('D' . $newSortedTableStartRow)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('G' . $newSortedTableStartRow)->getAlignment()->setWrapText(true);
	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
	$objPHPExcel->getActiveSheet()->getStyle('B' . $newSortedTableStartRow . ':D' . $newSortedTableStartRow)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('B' . $newSortedTableStartRow . ':D' . $newSortedTableStartRow)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	$objPHPExcel->getActiveSheet()->getStyle('B' . $newSortedTableStartRow . ':D' . $newSortedTableStartRow)->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('F' . $newSortedTableStartRow . ':G' . $newSortedTableStartRow)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F' . $newSortedTableStartRow . ':G' . $newSortedTableStartRow)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	$objPHPExcel->getActiveSheet()->getStyle('F' . $newSortedTableStartRow . ':G' . $newSortedTableStartRow)->getFont()->setBold(true)->setSize(12);
	$countIntensity = $countCo2 = [];
	foreach ($finalArray as $siteName => $details) {
	    // Consumption Calculations
	    $total = $totalCO2 = $totalCO2Intensity = $totalIntensity = 0;
	    $totalElectricity = $details['Electricity']['Consumption'];
	    $totalLPG = $details['Fuel']['Consumption'];
	    $totalFuel = $details['LPG']['Consumption'];
	    $totalNaturalGas = $details['Natural Gas']['Consumption'];
	    $totalHeating = $details['District Heating']['Consumption'];
	    $totolCooling = $details['District Cooling']['Consumption'];
	    $total = $totalElectricity + $totalLPG + $totalFuel + $totalNaturalGas + $totalHeating + $totolCooling;
	    $finalArray[$siteName]['Total Consumption'] = $total;
	    // CO2 calculations
	    $totalElectricityCO2 = $details['Electricity']['CO2'];
	    $totalLPGCO2 = $details['Fuel']['CO2'];
	    $totalFuelCO2 = $details['LPG']['CO2'];
	    $totalNaturalGasCO2 = $details['Natural Gas']['CO2'];
	    $totalHeatingCO2 = $details['District Heating']['CO2'];
	    $totolCoolingCO2 = $details['District Cooling']['CO2'];
	    $totalCO2 = $totalElectricityCO2 + $totalLPGCO2 + $totalFuelCO2 + $totalNaturalGasCO2 + $totalHeatingCO2 + $totolCoolingCO2;
	    $finalArray[$siteName]['Total CO2'] = $totalCO2;
	    // Consumption intensity Calculations
	    $totalElectricityIntensity = $details['Electricity']['Consumption Intensity'];
	    $totalLPGIntensity = $details['Fuel']['Consumption Intensity'];
	    $totalFuelIntensity = $details['LPG']['Consumption Intensity'];
	    $totalNaturalGasIntensity = $details['Natural Gas']['Consumption Intensity'];
	    $totalHeatingIntensity = $details['District Heating']['Consumption Intensity'];
	    $totolCoolingIntensity = $details['District Cooling']['Consumption Intensity'];
	    $totalIntensity = $totalElectricityIntensity + $totalLPGIntensity + $totalFuelIntensity + $totalNaturalGasIntensity + $totalHeatingIntensity + $totolCoolingIntensity;
	    $finalArray[$siteName]['Total Consumption Intensity'] = $totalIntensity;
	    // CO2 intensity calculations
	    $totalElectricityCO2Intensity = $details['Electricity']['CO2 Intensity'];
	    $totalLPGCO2Intensity = $details['Fuel']['CO2 Intensity'];
	    $totalFuelCO2Intensity = $details['LPG']['CO2 Intensity'];
	    $totalNaturalGasCO2Intensity = $details['Natural Gas']['CO2 Intensity'];
	    $totalHeatingCO2Intensity = $details['District Heating']['CO2 Intensity'];
	    $totolCoolingCO2Intensity = $details['District Cooling']['CO2 Intensity'];
	    $totalCO2Intensity = $totalElectricityCO2Intensity + $totalLPGCO2Intensity + $totalFuelCO2Intensity + $totalNaturalGasCO2Intensity + $totalHeatingCO2Intensity + $totolCoolingCO2Intensity;
	    $finalArray[$siteName]['Total CO2 Intensity'] = $totalCO2Intensity;
	    $all_site_consumption_intensity += $totalIntensity;
	    $countIntensity[] = $totalIntensity;
	    $all_site_co2_intensity += $totalCO2Intensity;
	    $countCo2[] = $totalIntensity;
	    unset($finalArray[$siteName]['Electricity']);
	    unset($finalArray[$siteName]['Fuel']);
	    unset($finalArray[$siteName]['LPG']);
	    unset($finalArray[$siteName]['Natural Gas']);
	    unset($finalArray[$siteName]['District Heating']);
	    unset($finalArray[$siteName]['District Cooling']);
	}
	if (count($countIntensity) != 0) {
	    $all_site_consumption_intensity = $all_site_consumption_intensity / count($countIntensity);
	}
	if (count($countCo2) != 0) {
	    $all_site_co2_intensity = $all_site_co2_intensity / count($countCo2);
	}
	$sortedArray = array();
	foreach ($finalArray as $key => $row) {
	    $sortedArray[$key] = $row;
	}
	array_multisort($sortedArray, SORT_DESC, $finalArray);
	$startRow = $newSortedTableStartRow + 1;
	foreach ($sortedArray as $key => $value) {
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $startRow . ':B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('C' . $startRow . ':C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('D' . $startRow . ':D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('E' . $startRow . ':E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('F' . $startRow . ':F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('G' . $startRow . ':G256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $startRow . ':D' . $startRow)->applyFromArray($styleArray);
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $startRow . ':D' . $startRow)->getFont()->setBold(false)->setSize(12);
	    $objPHPExcel->getActiveSheet()->getStyle('F' . $startRow . ':G' . $startRow)->applyFromArray($styleArray);
	    $objPHPExcel->getActiveSheet()->getStyle('F' . $startRow . ':G' . $startRow)->getFont()->setBold(false)->setSize(12);
	    $style = array('font' => array('bold' => true, 'color' => array('rgb' => '24478f')));
	    $objPHPExcel->getActiveSheet()->getStyle('B' . $startRow)->applyFromArray($style);
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('B' . $startRow, $key)
		->setCellValue('C' . $startRow, numb_format_without_currency($value['Total Consumption'], 2))
		->setCellValue('D' . $startRow, ($value['Total Consumption Intensity']))
		->setCellValue('E' . $startRow, "")
		->setCellValue('F' . $startRow, numb_format_without_currency($value['Total CO2'], 2))
		->setCellValue('G' . $startRow, ($value['Total CO2 Intensity']));
	    $startRow++;
	}
	$box_display = array(
	    'borders' => array(
		'right' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'bottom' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'top' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'left' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
	    ),
	);
	$style_border_right_left = array(
	    'borders' => array(
		'right' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
		'left' => array(
		    'style' => PHPExcel_Style_Border::BORDER_THIN,
		    'color' => array('rgb' => '000000'),
		),
	    ),
	);
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $startRow . ':C' . $startRow);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $startRow . ':D' . $startRow)->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $startRow . ':D' . $startRow)->applyFromArray($box_display);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $startRow . ':D' . $startRow)->applyFromArray($style_border_right_left);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('B' . $startRow . ':D' . $startRow)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $startRow . ':G' . $startRow)->getFont()->setBold(true)->setSize(12);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $startRow . ':G' . $startRow)->applyFromArray($box_display);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $startRow . ':G' . $startRow)->applyFromArray($style_border_right_left);
	$objPHPExcel->setActiveSheetIndex(0)->getStyle('F' . $startRow . ':G' . $startRow)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'c2d1f0'))));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $startRow, 'Average Energy intensity (MJ/m2)');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $startRow, number_format((float)$all_site_consumption_intensity, 2, '.', ''));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $startRow, '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $startRow, 'Average Carbon intensity (KgCO2/m2)');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $startRow, number_format((float)$all_site_co2_intensity, 2, '.', ''));
	$objPHPExcel->getActiveSheet()->getStyle('F' . $startRow)->getAlignment()->setWrapText(true);
	/* Create new Sorted list of MJ and CO2 */
	if ($reportType == 'ytd') {
	    $reportFileName = 'Energy and Carbon Report YTD';
	} else if ($reportType == 'month') {
	    $reportFileName = str_replace('of - ', '-', $report_title);
	} else {
	    $reportFileName = str_replace('Year :', '-', $report_title);
	}
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(90);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(false)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(false)->setWidth(20);
	$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	ob_end_clean();
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $reportFileName . '.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
    }

    public function get_all_sites_electricity_consumption($details = array(), $reportType = '', $selectedYear = '')
    {
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];
	$year = (int) $selectedYear;
	$current_month = (int) date('m');
	$this->load->model('sites/sites_model');
	$this->sites_model->year = $year;
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$data = array();
	$data['currency'] = "local";

		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');

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
	$filters['current_year'] = $selectedYear;
	// FIlters for comparisional bar chart
	$filters_comparision_chart_pre = array();
	// $startdate_pre = '1/' . date("Y", strtotime(date('Y') . " -1 years"));
	// $enddate_pre = '12/' . date("Y", strtotime(date('Y') . " -1 years"));
	$startdate_pre = '1/' . ($selectedYear);
	$enddate_pre = '12/' . ($selectedYear);
	// $startdate_pre = '1/' . ($selectedYear - 1);
	// $enddate_pre = '12/' . ($selectedYear - 1);
	$startdateexplode_pre = explode('/', $startdate_pre);
	$enddateexplode_pre = explode('/', $enddate_pre);
	$filters_comparision_chart_pre['startdate'] = (isset($startdate_pre)) ? $startdate_pre : '';
	$filters_comparision_chart_pre['enddate'] = (isset($enddate_pre)) ? $enddate_pre : '';
	$filters_comparision_chart_pre['start_month'] = (isset($startdateexplode_pre[0])) ? (int) $startdateexplode_pre[0] : '';
	$filters_comparision_chart_pre['start_year'] = (isset($startdateexplode_pre[1])) ? $startdateexplode_pre[1] : '';
	$filters_comparision_chart_pre['end_month'] = (isset($enddateexplode_pre[0])) ? (int) $enddateexplode_pre[0] : '';
	$filters_comparision_chart_pre['end_year'] = (isset($enddateexplode_pre[1])) ? $enddateexplode_pre[1] : '';
	$currentYear = $selectedYear;
	$currentMonth = intval(date('m'));
	if ($currentMonth == 1) {
	    $currentYear = $currentYear - 1;
	    $currentMonth = 12;
	}
	$filters_comparision_chart_pre['currentYear'] = $currentYear;
	$filters_comparision_chart_pre['currentMonth'] = $currentMonth;
	// Prepare sheet title and data filters
	$reportSheetTitle = '';
	if ($reportType == 'ytd') {
	    unset($filters_comparision_chart_pre);
	    $filters_comparision_chart_pre['start_year'] = date('Y');
	    $filters_comparision_chart_pre['start_month'] = 1;
	    $filters_comparision_chart_pre['end_year'] = date('Y');
	    $filters_comparision_chart_pre['end_month'] = date('m') - 1;
	    $filters_comparision_chart_pre['report_type'] = 'ytd';
	    $firstDay = date('01/01/Y');
	    $lastDay = date('d/m/Y', strtotime('last day of previous month'));
	    $reportSheetTitle = sprintf(lang('annual_group_report_ytd_title'), $firstDay, $lastDay);
	} else if ($reportType == 'month') {
	    unset($filters_comparision_chart_pre);
	    $filters_comparision_chart_pre['report_type'] = 'month';
	    $startdate = $this->input->post('startdate', '');
	    $startdateexplode = explode("/", $startdate);
	    $filters_comparision_chart_pre['start_year'] = $filters_comparision_chart_pre['end_year'] = (isset($startdateexplode[1])) ? (int) $startdateexplode[1] : '';
	    $filters_comparision_chart_pre['start_month'] = $filters_comparision_chart_pre['end_month'] = (isset($startdateexplode[0])) ? (int) $startdateexplode[0] : '';
	    $startdate = $this->input->post('startdate', '');
	    $startdateexplode = explode('/', $startdate);
	    $monthName = date('F', mktime(0, 0, 0, (int) $startdateexplode[0], 10));
	    $reportSheetTitle = sprintf(lang('annual_group_report_month_title'), $monthName, $startdateexplode[1]);
	}
	// $site_details = $this->sites_model->get_all_site_listing_for_users_orderby($site_id, $role_id, $user_id = 0);
	$site_details = $this->sites_model->get_all_site_listing_for_users_orderby_with_region($site_id, $role_id, $user_id = 0, $details['selected_region']);
	// Initialize total
	$all_site_electricity = 0;
	$all_site_fuel = 0;
	$all_site_natural_gas = 0;
	$all_site_lpg = 0;
	$all_site_district_cooling = 0;
	$all_site_district_heating = 0;
	$utility_array = array();
	$co2Array = $totalArray = $finalArray = array();
	foreach ($site_details as $site_detail) {
	    $site_id = $site_detail['id'];
	    $site_name = $site_detail['site_location_name'];
	    $filters_pre['report_year'] = ($selectedYear);
	    $filters_pre['max_month_id'] = 12;
	    $filters_pre['site_id'] = $site_id;
	    // filters_comparision_chart_pre
	    $filters_comparision_chart_pre['site_id'] = $site_id;
	    if ($data['currency'] == "base") {
		$utility_cost_chart_results_pre = $this->reports_forex_model->utilityCostBarChartExcelAnnualReport($filters_comparision_chart_pre);
	    } else {
		$utility_cost_chart_results_pre = $this->reports_model->utilityCostBarChartExcelAnnualReportForTypeBased($filters_comparision_chart_pre);
	    }
	    if ($reportType == 'ytd') {
		$filters_pre['report_type'] = 'ytd';
		$filters_pre['report_year'] = date('Y');
		$filters_pre['max_month_id'] = date('m') - 1;
	    } else if ($reportType == 'month') {
		$filters_pre['report_type'] = 'month';
		$filters_pre['report_year'] = $filters_comparision_chart_pre['start_year'];
		$filters_pre['max_month_id'] = $filters_comparision_chart_pre['start_month'];
	    }
	    $kwh_report_results_pre = $this->reports_model->kwhUnitBasedReportForCurrentYearExcelAnnualReportForMonthly($filters_pre);
	    if (!empty($kwh_report_results_pre)) {
		$data['kwh_pie_chart_pre']['electricity'] = (!empty($kwh_report_results_pre['electricity'])) ? ($kwh_report_results_pre['electricity'] * $electricity_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['fuel'] = (!empty($kwh_report_results_pre['fuel'])) ? ($kwh_report_results_pre['fuel'] * $fuel_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['lpg'] = (!empty($kwh_report_results_pre['lpg'])) ? ($kwh_report_results_pre['lpg'] * $lpg_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['natural_gas'] = (!empty($kwh_report_results_pre['natural_gas'])) ? ($kwh_report_results_pre['natural_gas'] * $natural_gas_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['heating_district'] = (!empty($kwh_report_results_pre['heating_district'])) ? ($kwh_report_results_pre['heating_district'] * $heating_district_mmbtu_rate) : 0;
		$data['kwh_pie_chart_pre']['cooling_district'] = (!empty($kwh_report_results_pre['cooling_district'])) ? ($kwh_report_results_pre['cooling_district'] * $cooling_district_mmbtu_rate) : 0;
	    } else {
		$data['kwh_pie_chart_pre'] = array();
	    }
	    // Consumption
	    $kwh_pie_chart_pre = $data['kwh_pie_chart_pre'];
	    $electricity_value = round($kwh_pie_chart_pre['electricity']);
	    $fuel_value = round($kwh_pie_chart_pre['fuel']);
	    $lpg_value = round($kwh_pie_chart_pre['lpg']);
	    $natural_gas_value = round($kwh_pie_chart_pre['natural_gas']);
	    $heating_district_value = round($kwh_pie_chart_pre['heating_district']);
	    $cooling_district_value = round($kwh_pie_chart_pre['cooling_district']);
	    $utility_kwh_total = ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	    $finalArray[$site_name]['Electricity']['Consumption'] = round($kwh_pie_chart_pre['electricity'], 2);
	    $finalArray[$site_name]['Fuel']['Consumption'] = round($kwh_pie_chart_pre['fuel'], 2);
	    $finalArray[$site_name]['LPG']['Consumption'] = round($kwh_pie_chart_pre['lpg'], 2);
	    $finalArray[$site_name]['Natural Gas']['Consumption'] = round($kwh_pie_chart_pre['natural_gas'], 2);
	    $finalArray[$site_name]['District Cooling']['Consumption'] = round($kwh_pie_chart_pre['heating_district'], 2);
	    $finalArray[$site_name]['District Heating']['Consumption'] = round($kwh_pie_chart_pre['cooling_district'], 2);
	    // check share %
	    $electricity_share = (round(($electricity_value * 100) / $utility_kwh_total, 1) > 0) ? round(($electricity_value * 100) / $utility_kwh_total, 1) : 0;
	    $fuel_share = (round(($fuel_value * 100) / $utility_kwh_total, 1) > 0) ? round(($fuel_value * 100) / $utility_kwh_total, 1) : 0;
	    $lpg_share = (round(($lpg_value * 100) / $utility_kwh_total, 1) > 0) ? round(($lpg_value * 100) / $utility_kwh_total, 1) : 0;
	    $natural_gas_share = (round(($natural_gas_value * 100) / $utility_kwh_total, 1) > 0) ? round(($natural_gas_value * 100) / $utility_kwh_total, 1) : 0;
	    $heating_district_share = (round(($heating_district_value * 100) / $utility_kwh_total, 1) > 0) ? round(($heating_district_value * 100) / $utility_kwh_total, 1) : 0;
	    $cooling_district_share = (round(($cooling_district_value * 100) / $utility_kwh_total, 1) > 0) ? round(($cooling_district_value * 100) / $utility_kwh_total, 1) : 0;
	    $total_months = 0;
	    $total_sum_data_electricity = 0;
	    $total_sum_data_fuel = 0;
	    $total_sum_data_lpg = 0;
	    $total_sum_data_natural_gas = 0;
	    $total_sum_data_heating_district = 0;
	    $total_sum_data_cooling_district = 0;
	    $total_sum_data_water = 0;
	    $total_sum_data_cdd = 0;
	    $total_sum_data_hdd = 0;
	    $total_sum_data_occupancy = 0;
	    $total_sum_data_room_night = 0;
	    $total_sum_data_electricity_kwh = 0;
	    // for CO2 calculation in group energy chart
	    foreach ($utility_cost_chart_results_pre as $key => $value) {
		if (isset($data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]) && !empty($data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    $value = array_map('floatval', $value);
		    $value['cooling_district'] = $value['cooling_district'] + $value['district_cooling_fixed_cost'];
		    $value['heating_district'] = $value['heating_district'] + $value['district_heating_fixed_cost'];
		    $totalElectricity_utility_cost_pre += $value['electricity'];
		    $totalFuel_utility_cost_pre += $value['fuel'];
		    $totalLpg_utility_cost_pre += $value['lpg'];
		    $totalNaturalGas_utility_cost_pre += $value['natural_gas'];
		    $totalWater_utility_cost_pre += $value['water'];
		    $totalHeatingDistrict_utility_cost_pre += $value['heating_district'];
		    $totalCoolingDistrict_utility_cost_pre += $value['cooling_district'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity'] = (!empty($value['electricity'])) ? $value['electricity'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity_consumption'] = (!empty($value['electricity_consumption'])) ? $value['electricity_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['fuel'] = (!empty($value['fuel'])) ? $value['fuel'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['fuel_consumption'] = (!empty($value['fuel_consumption'])) ? $value['fuel_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['lpg'] = (!empty($value['lpg'])) ? $value['lpg'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['lpg_consumption'] = (!empty($value['lpg_consumption'])) ? $value['lpg_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['natural_gas'] = (!empty($value['natural_gas'])) ? $value['natural_gas'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['natural_gas_consumption'] = (!empty($value['natural_gas_consumption'])) ? $value['natural_gas_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['heating_district'] = (!empty($value['heating_district'])) ? $value['heating_district'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['heating_district_consumption'] = (!empty($value['heating_district_consumption'])) ? $value['heating_district_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cooling_district'] = (!empty($value['cooling_district'])) ? $value['cooling_district'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cooling_district_consumption'] = (!empty($value['cooling_district_consumption'])) ? $value['cooling_district_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['water'] = (!empty($value['water'])) ? $value['water'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['water_consumption'] = (!empty($value['water_consumption'])) ? $value['water_consumption'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['cdd'] = (!empty($value['cdd'])) ? $value['cdd'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['hdd'] = (!empty($value['hdd'])) ? $value['hdd'] : 0;
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['month_id'] = $value['month_id'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['year_id'] = $value['year_id'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['room_night'] = $value['total_room_night'];
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['total_electricity_kwh'] = (!empty($value['total_electricity_kwh'])) ? $value['total_electricity_kwh'] : 0;
		    if (!empty($value['total_electricity_kwh'])) {
			$electricity_tariff_cost_per_kwh = $value['electricity'] / $value['total_electricity_kwh'];
		    } else {
			$electricity_tariff_cost_per_kwh = 0;
		    }
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['electricity_tariff'] = (!empty($electricity_tariff_cost_per_kwh)) ? $electricity_tariff_cost_per_kwh : 0;
		    $days_of_month = cal_days_in_month(CAL_GREGORIAN, $value['month_id'], $value['year_id']);
		    $data['utility_cost_chart_pre'][$site_id][$value['month_id']][$value['year_id']]['occupancy'] = (($value['total_room_night'] / ($value['rooms_keys'] * $days_of_month)) * 100);
		}
	    }
	    /* new added */
	    //Add total values in data array
	    $data['totalElectricity_utility_cost_pre'] = $totalElectricity_utility_cost_pre;
	    $data['totalFuel_utility_cost_pre'] = $totalFuel_utility_cost_pre;
	    $data['totalLpg_utility_cost_pre'] = $totalLpg_utility_cost_pre;
	    $data['totalNaturalGas_utility_cost_pre'] = $totalNaturalGas_utility_cost_pre;
	    $data['totalWater_utility_cost_pre'] = $totalWater_utility_cost_pre;
	    $data['totalHeatingDistrict_utility_cost_pre'] = $totalHeatingDistrict_utility_cost_pre;
	    $data['totalCoolingDistrict_utility_cost_pre'] = $totalCoolingDistrict_utility_cost_pre;
	    /* new added */
	    // calculation from view
	    unset($resultkeys_pre);
	    unset($startmonthsarray_pre);
	    unset($utility_cost_chart_pre);
	    $utility_cost_chart_pre = $data['utility_cost_chart_pre'][$site_id];
	    $filters['filters_comparision_chart_pre'] = $filters_comparision_chart_pre;
	    $startmonthsarray_pre = array();
	    if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) {
		// If start and end year is same
		for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']["end_month"]; $i++) {
		    $startmonthsarray_pre[] = $i;
		}
		$resultkeys_pre = array();
		$resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;
	    }
	    /*foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Current year data
		    $data_electricity = 0;
		    $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['electricity'])) ? $utility_cost_chart_pre[$month][$year]['electricity'] : 0;
		    $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel'])) ? $utility_cost_chart_pre[$month][$year]['fuel'] : 0;
		    $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg_consumption'])) ? $utility_cost_chart_pre[$month][$year]['lpg_consumption'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas'])) ? $utility_cost_chart_pre[$month][$year]['natural_gas'] : 0;
		    $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['heating_district_consumption'] : 0;
		    $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['cooling_district_consumption'] : 0;
		    $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water'])) ? $utility_cost_chart_pre[$month][$year]['water'] : 0;
		    $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd'])) ? $utility_cost_chart_pre[$month][$year]['cdd'] : 0;
		    $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd'])) ? $utility_cost_chart_pre[$month][$year]['hdd'] : 0;
		    $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy'])) ? $utility_cost_chart_pre[$month][$year]['occupancy'] : 0;
		    $data_room_night = (!empty($utility_cost_chart_pre[$month][$year]['room_night'])) ? $utility_cost_chart_pre[$month][$year]['room_night'] : 0;
		    $data_electricity_tariff = (!empty($utility_cost_chart_pre[$month][$year]['electricity_tariff'])) ? $utility_cost_chart_pre[$month][$year]['electricity_tariff'] : 0;
		    $data_electricity_kwh = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] : 0;
		    $data_electricity = round($data_electricity * $site_detail['electricity_emission_factor'], 2);
		    $data_fuel = round($data_fuel * $site_detail['fuel_emission_factor'], 2);
		    $data_lpg = round($data_lpg * $site_detail['lpg_emission_factor'], 2);
		    $data_natural_gas = round($data_natural_gas * $site_detail['natural_gas_emission_factor'], 2);
		    $data_heating_district = round($data_heating_district * $site_detail['district_heating_emission_factor'], 2);
		    $data_cooling_district = round($data_cooling_district * $site_detail['district_cooling_emission_factor'], 2);
		    $data_water = 0; // There is no calculation for water data
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
		    // Total sum Current year data
		    $total_sum_data_electricity += $data_electricity;
		    $total_sum_data_fuel += $data_fuel;
		    $total_sum_data_lpg += $data_lpg;
		    $total_sum_data_natural_gas += $data_natural_gas;
		    $total_sum_data_heating_district += $data_heating_district;
		    $total_sum_data_cooling_district += $data_cooling_district;
		    $total_sum_data_water += $data_water;
		    $total_sum_data_cdd += $data_cdd;
		    $total_sum_data_hdd += $data_hdd;
		    $total_sum_data_occupancy += $data_occupancy;
		    $total_sum_data_room_night += $data_room_night;
		    //$total_sum_data_electricity_tariff += $data_electricity_tariff;
		    $total_sum_data_electricity_kwh += $data_electricity_kwh;
		    $total_months++;
		}
	    }*/
	    foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Current year data
		    $data_electricity = 0;
		    $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year]['onsite_generator'] - $utility_cost_chart_pre[$month][$year]['renewable_energy']) : 0;
		    $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel_consumption'])) ? ($utility_cost_chart_pre[$month][$year]['fuel_consumption'] - $utility_cost_chart_pre[$month][$year]['onsite_generator_fuel_oil']) : 0;
		    $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg_consumption'])) ? $utility_cost_chart_pre[$month][$year]['lpg_consumption'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas_consumption'])) ? ($utility_cost_chart_pre[$month][$year]['natural_gas_consumption'] - $utility_cost_chart_pre[$month][$year]['onsite_generator_natural_gas']) : 0;
		    $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['heating_district_consumption'] : 0;
		    $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart_pre[$month][$year]['cooling_district_consumption'] : 0;
		    $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water_consumption'])) ? $utility_cost_chart_pre[$month][$year]['water_consumption'] : 0;
		    $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd'])) ? $utility_cost_chart_pre[$month][$year]['cdd'] : 0;
		    $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd'])) ? $utility_cost_chart_pre[$month][$year]['hdd'] : 0;
		    $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy'])) ? $utility_cost_chart_pre[$month][$year]['occupancy'] : 0;
		    $data_room_night = (!empty($utility_cost_chart_pre[$month][$year]['room_night'])) ? $utility_cost_chart_pre[$month][$year]['room_night'] : 0;
		    $data_electricity_tariff = (!empty($utility_cost_chart_pre[$month][$year]['electricity_tariff'])) ? $utility_cost_chart_pre[$month][$year]['electricity_tariff'] : 0;
		    $data_electricity_kwh = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] : 0;
		    $data_electricity = round($data_electricity * $site_detail['electricity_emission_factor'], 2);
		    $data_fuel = round($data_fuel * $site_detail['fuel_emission_factor'], 2);
		    $data_lpg = round($data_lpg * $site_detail['lpg_emission_factor'], 2);
		    $data_natural_gas = round($data_natural_gas * $site_detail['natural_gas_emission_factor'], 2);
		    $data_heating_district = round($data_heating_district * $site_detail['district_heating_emission_factor'], 2);
		    $data_cooling_district = round($data_cooling_district * $site_detail['district_cooling_emission_factor'], 2);
		    $data_water = 0; // There is no calculation for water data
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
		    // Total sum Current year data
		    $total_sum_data_electricity += $data_electricity;
		    $total_sum_data_fuel += $data_fuel;
		    $total_sum_data_lpg += $data_lpg;
		    $total_sum_data_natural_gas += $data_natural_gas;
		    $total_sum_data_heating_district += $data_heating_district;
		    $total_sum_data_cooling_district += $data_cooling_district;
		    $total_sum_data_water += $data_water;
		    $total_sum_data_cdd += $data_cdd;
		    $total_sum_data_hdd += $data_hdd;
		    $total_sum_data_occupancy += $data_occupancy;
		    $total_sum_data_room_night += $data_room_night;
		    //$total_sum_data_electricity_tariff += $data_electricity_tariff;
		    $total_sum_data_electricity_kwh += $data_electricity_kwh;
		    $total_months++;
		}
	    }
	    // new added
	    if ($total_sum_data_electricity_kwh > 0) {
		$total_sum_data_electricity_tariff = ($total_sum_data_electricity / $total_sum_data_electricity_kwh);
	    } else {
		$total_sum_data_electricity_tariff = 0;
	    }
	    $total_sum_data_sum = ($total_sum_data_electricity + $total_sum_data_fuel + $total_sum_data_lpg + $total_sum_data_natural_gas + $total_sum_data_water + $total_sum_data_heating_district + $total_sum_data_cooling_district);
	    // new added
	    if ($site_detail['show_utility_electricity']) {
		array_push($utility_array, 'Electricity');
		if (isset($total_sum_data_electricity)) {
		    $total_consumption += $electricity_value;
		    $total_percentage += $electricity_share;
		    $total_co2 += round($total_sum_data_electricity);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['Electricity']['Consumption'] = round($electricity_value / 1000, 2);
		    $finalArray[$site_name]['Electricity']['CO2'] = round($total_sum_data_electricity / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_fuel_oil']) {
		array_push($utility_array, 'Fuel');
		if (isset($total_sum_data_fuel)) {
		    $total_consumption += $fuel_value;
		    $total_percentage += $fuel_share;
		    $total_co2 += round($total_sum_data_fuel);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['Fuel']['Consumption'] = round($fuel_value / 1000, 2);
		    $finalArray[$site_name]['Fuel']['CO2'] = round($total_sum_data_fuel / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_lpg']) {
		array_push($utility_array, 'LPG');
		if (isset($total_sum_data_lpg)) {
		    $total_consumption += $lpg_value;
		    $total_percentage += $lpg_share;
		    $total_co2 += round($total_sum_data_lpg);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['LPG']['Consumption'] = round($lpg_value / 1000, 2);
		    $finalArray[$site_name]['LPG']['CO2'] = round($total_sum_data_lpg / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_natural_gas']) {
		array_push($utility_array, 'Natural Gas');
		if (isset($total_sum_data_natural_gas)) {
		    $total_consumption += $natural_gas_value;
		    $total_percentage += $natural_gas_share;
		    $total_co2 += round($total_sum_data_natural_gas);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['Natural Gas']['Consumption'] = round($natural_gas_value / 1000, 2);
		    $finalArray[$site_name]['Natural Gas']['CO2'] = round($total_sum_data_natural_gas / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_district_cooling']) {
		array_push($utility_array, 'District Cooling');
		if (isset($total_sum_data_cooling_district)) {
		    $total_consumption += $cooling_district_value;
		    $total_percentage += $cooling_district_share;
		    $total_co2 += round($total_sum_data_cooling_district);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['District Cooling']['Consumption'] = round($cooling_district_value / 1000, 2);
		    $finalArray[$site_name]['District Cooling']['CO2'] = round($total_sum_data_cooling_district / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_district_heating']) {
		array_push($utility_array, 'District Heating');
		if (isset($total_sum_data_heating_district)) {
		    $total_consumption += $heating_district_value;
		    $total_percentage += $heating_district_share;
		    $total_co2 += round($total_sum_data_heating_district);
		    $total_utility = $total_utility + 1;
		    // $finalArray[$site_name]['District Heating']['Consumption'] = round($heating_district_value / 1000, 2);
		    $finalArray[$site_name]['District Heating']['CO2'] = round($total_sum_data_heating_district / 1000, 2);
		}
	    }
	    if ($site_detail['show_utility_electricity']) {
		$all_site_electricity_value += $electricity_value;
		$all_site_electricity_share += $electricity_share;
		$all_site_electricity += $total_sum_data_electricity;
	    }
	    if ($site_detail['show_utility_fuel_oil']) {
		$all_site_fuel_value += $fuel_value;
		$all_site_fuel_share += $fuel_share;
		$all_site_fuel += $total_sum_data_fuel;
	    }
	    if ($site_detail['show_utility_lpg']) {
		$all_site_lpg_value += $lpg_value;
		$all_site_lpg_share += $lpg_share;
		$all_site_lpg += $total_sum_data_lpg;
	    }
	    if ($site_detail['show_utility_natural_gas']) {
		$all_site_natural_gas_value += $natural_gas_value;
		$all_site_natural_gas_share += $natural_gas_share;
		$all_site_natural_gas += $total_sum_data_natural_gas;
	    }
	    if ($site_detail['show_utility_district_heating']) {
		$all_site_district_heating_value += $heating_district_value;
		$all_site_district_heating_share += $heating_district_share;
		$all_site_district_heating += $total_sum_data_heating_district;
	    }
	    if ($site_detail['show_utility_district_cooling']) {
		$all_site_district_cooling_value += $cooling_district_value;
		$all_site_district_cooling_share += $cooling_district_share;
		$all_site_district_cooling += $total_sum_data_cooling_district;
	    }
	    $utilities_array = array_unique($utility_array);
	    if (isset($all_site_electricity) && (in_array("Electricity", $utilities_array))) {
		$total_consumption_sites += $all_site_electricity_value;
		$total_percentage_sites += $all_site_electricity_share;
		$total_co2_sites += round($all_site_electricity);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    if (isset($all_site_fuel) && (in_array("Fuel", $utilities_array))) {
		$total_consumption_sites += $all_site_fuel_value;
		$total_percentage_sites += $all_site_fuel_share;
		$total_co2_sites += round($all_site_fuel);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    if (isset($all_site_lpg) && (in_array("LPG", $utilities_array))) {
		$total_consumption_sites += $all_site_lpg_value;
		$total_percentage_sites += $all_site_lpg_share;
		$total_co2_sites += round($all_site_lpg);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    if (isset($all_site_natural_gas) && (in_array("Natural Gas", $utilities_array))) {
		$total_consumption_sites += $all_site_natural_gas_value;
		$total_percentage_sites += $all_site_natural_gas_share;
		$total_co2_sites += round($all_site_natural_gas);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    if (isset($all_site_district_heating) && (in_array("District Heating", $utilities_array))) {
		$total_consumption_sites += $all_site_district_heating_value;
		$total_percentage_sites += $all_site_district_heating_share;
		$total_co2_sites += round($all_site_district_heating);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    if (isset($all_site_district_cooling) && (in_array("District Cooling", $utilities_array))) {
		$total_consumption_sites += $all_site_district_cooling_value;
		$total_percentage_sites += $all_site_district_cooling_share;
		$total_co2_sites += round($all_site_district_cooling);
		$total_utility_sites = $total_utility_sites + 1;
	    }
	    $utilities_array = array_unique($utility_array);
	}
	$totalArray = array();
	$totalElectricity = $totalLPG = $totalFuel = $totalNaturalGas = $totolCooling = $totalHeating = $totalElectricityCO2 = $totalLPGCO2 = $totalFuelCO2 = $totalNaturalGasCO2 = $totolCoolingCO2 = $totalHeatingCO2 = 0;
	$totalSiteConsumption = array();
	foreach ($finalArray as $siteName => $details) {
	    // Consumption Calculations
	    $total = $totalCO2 = 0;
	    $totalElectricity = $details['Electricity']['Consumption'];
	    $totalLPG = $details['Fuel']['Consumption'];
	    $totalFuel = $details['LPG']['Consumption'];
	    $totalNaturalGas = $details['Natural Gas']['Consumption'];
	    $totalHeating = $details['District Heating']['Consumption'];
	    $totolCooling = $details['District Cooling']['Consumption'];
	    $total = $totalElectricity + $totalLPG + $totalFuel + $totalNaturalGas + $totalHeating + $totolCooling;
	    $finalArray[$siteName]['Total Consumption'] = $total;
	    // CO2 calculations
	    $totalElectricityCO2 = $details['Electricity']['CO2'];
	    $totalLPGCO2 = $details['Fuel']['CO2'];
	    $totalFuelCO2 = $details['LPG']['CO2'];
	    $totalNaturalGasCO2 = $details['Natural Gas']['CO2'];
	    $totalHeatingCO2 = $details['District Heating']['CO2'];
	    $totolCoolingCO2 = $details['District Cooling']['CO2'];
	    $totalCO2 = $totalElectricityCO2 + $totalLPGCO2 + $totalFuelCO2 + $totalNaturalGasCO2 + $totalHeatingCO2 + $totolCoolingCO2;
	    $finalArray[$siteName]['Total CO2'] = $totalCO2;
	    unset($finalArray[$siteName]['Electricity']);
	    unset($finalArray[$siteName]['Fuel']);
	    unset($finalArray[$siteName]['LPG']);
	    unset($finalArray[$siteName]['Natural Gas']);
	    unset($finalArray[$siteName]['District Heating']);
	    unset($finalArray[$siteName]['District Cooling']);
	}
	return $finalArray;
    }

    public function calculatePercentage($value, $total)
    {
	if ($total != 0) {
	    return ($value * 100 / $total) / 1000;
	}
    }

    public function CalculateMeasures($data, $site_detials, $currYear, $currMonth)
    {
	$result = $site_detials;
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->utilities_model->utilities_month = ($currMonth) ? $currMonth - 1 : 0;
	$this->utilities_model->utilities_year = $currYear;
	if ($this->utilities_model->utilities_month == 0) {
	    $this->utilities_model->utilities_month = 12;
	    $this->utilities_model->utilities_year = $currYear - 1;
	}
	$this->utilities_model->site_id = $site_id;
	$getUtilityData = $this->utilities_model->getSiteUtilityCurYear();
	$this->utilities_model->utilities_month = ($currMonth) ? $currMonth : 0;
	$this->utilities_model->utilities_year = $currYear - 1;
	$getUtilityData_prev = $this->utilities_model->getSiteUtilityLastYear();
	$this->utilities_model->utilities_year = $currYear - 2;
	$getUtilityData_minus_two_year = $this->utilities_model->getSiteUtilityLastYear();
	$this->utilities_model->utilities_year = $currYear - 3;
	$getUtilityData_minus_three_year = $this->utilities_model->getSiteUtilityLastYear();
	foreach ($getUtilityData as $getUtilities) {
	    $totalElectricyKwhValue = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'];
	    $totalFuelOilCostValue = $getUtilities['total_fuel_oil_cost'] - $getUtilities['onsite_generators_fuel_oil_quantity'];
	    $totalNaturalGasValue = $getUtilities['total_natural_gas_cost'] - $getUtilities['onsite_generators_natural_gas_quantity'];
	    $carbon_footPrint += ($totalElectricyKwhValue * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($totalFuelOilCostValue * $site_detials['fuel_emission_factor']) + ($totalNaturalGasValue * $site_detials['natural_gas_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
	    $total_room_night += $getUtilities['total_room_night'];
	    $water_total_consumption += $getUtilities['water_total_consumption'];
	    $lpg_value = $getUtilities['total_lpg'] * 13.269;
	    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;
	    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;
	    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;
	    $heating_district_value = $getUtilities['district_heating'] * 1;
	    $cooling_district_value = $getUtilities['district_cooling'] * 1;
	    $utility_kwh_total += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	}
	foreach ($getUtilityData_prev as $getUtilities) {
	    $totalElectricyKwhValue_prev = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'];
	    $totalFuelOilCostValue_prev = $getUtilities['total_fuel_oil_cost'] - $getUtilities['onsite_generators_fuel_oil_quantity'];
	    $totalNaturalGasValue_prev = $getUtilities['total_natural_gas_cost'] - $getUtilities['onsite_generators_natural_gas_quantity'];
	    $carbon_footPrint_prev += ($totalElectricyKwhValue_prev * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($totalFuelOilCostValue_prev * $site_detials['fuel_emission_factor']) + ($totalNaturalGasValue_prev * $site_detials['natural_gas_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
	    $total_room_night_prev += $getUtilities['total_room_night'];
	    $water_total_consumption_prev += $getUtilities['water_total_consumption'];
	    $lpg_value = $getUtilities['total_lpg'] * 13.269;
	    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;
	    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;
	    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;
	    $heating_district_value = $getUtilities['district_heating'] * 1;
	    $cooling_district_value = $getUtilities['district_cooling'] * 1;
	    $utility_kwh_total_prev += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	}
	foreach ($getUtilityData_minus_two_year as $getUtilities) {
	    $totalElectricyKwhValue_minus_two_year = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'];
	    $totalFuelOilCostValue_minus_two_year = $getUtilities['total_fuel_oil_cost'] - $getUtilities['onsite_generators_fuel_oil_quantity'];
	    $totalNaturalGasValue_minus_two_year = $getUtilities['total_natural_gas_cost'] - $getUtilities['onsite_generators_natural_gas_quantity'];
	    $carbon_footPrint_minus_two_year += ($totalElectricyKwhValue_minus_two_year * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($totalFuelOilCostValue_minus_two_year * $site_detials['fuel_emission_factor']) + ($totalNaturalGasValue_minus_two_year * $site_detials['natural_gas_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
	    $total_room_night_minus_two_year += $getUtilities['total_room_night'];
	    $water_total_consumption_minus_two_year += $getUtilities['water_total_consumption'];
	    $lpg_value = $getUtilities['total_lpg'] * 13.269;
	    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;
	    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;
	    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;
	    $heating_district_value = $getUtilities['district_heating'] * 1;
	    $cooling_district_value = $getUtilities['district_cooling'] * 1;
	    $utility_kwh_total_minus_two_year += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	}
	foreach ($getUtilityData_minus_three_year as $getUtilities) {
	    $totalElectricyKwhValue_minus_three_year = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'];
	    $totalFuelOilCostValue_minus_three_year = $getUtilities['total_fuel_oil_cost'] - $getUtilities['onsite_generators_fuel_oil_quantity'];
	    $totalNaturalGasValue_minus_three_year = $getUtilities['total_natural_gas_cost'] - $getUtilities['onsite_generators_natural_gas_quantity'];
	    $carbon_footPrint_minus_three_year += ($totalElectricyKwhValue_minus_three_year * $site_detials['electricity_emission_factor']) + ($getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($totalFuelOilCostValue_minus_three_year * $site_detials['fuel_emission_factor']) + ($totalNaturalGasValue_minus_three_year * $site_detials['natural_gas_emission_factor']) + ($getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
	    $total_room_night_minus_three_year += $getUtilities['total_room_night'];
	    $water_total_consumption_minus_three_year += $getUtilities['water_total_consumption'];
	    $lpg_value = $getUtilities['total_lpg'] * 13.269;
	    $electricity_value = $getUtilities['total_electricity_kwh'] * 1;
	    $natural_gas_value = $getUtilities['total_natural_gas'] * 10.3454063;
	    $fuel_value = $getUtilities['total_fuel_oil'] * 9.95342803564829;
	    $heating_district_value = $getUtilities['district_heating'] * 1;
	    $cooling_district_value = $getUtilities['district_cooling'] * 1;
	    $utility_kwh_total_minus_three_year += ($electricity_value + $fuel_value + $lpg_value + $natural_gas_value + $heating_district_value + $cooling_district_value);
	}
	$data['measures']['HotelCarbonFootprintPerRoom'][$currYear] = round($carbon_footPrint / $result['rooms_keys'], 2);
	$data['measures']['HotelCarbonFootprintPerRoom'][$currYear - 1] = round($carbon_footPrint_prev / $result['rooms_keys'], 2);
	$data['measures']['HotelCarbonFootprintPerRoom'][$currYear - 2] = round($carbon_footPrint_minus_two_year / $result['rooms_keys'], 2);
	$data['measures']['HotelCarbonFootprintPerRoom'][$currYear - 3] = round($carbon_footPrint_minus_three_year / $result['rooms_keys'], 2);
	$data['measures']['HotelCarbonFootprintPerRoom']['measure_id'] = 2;
	$data['measures']['HotelCarbonFootprintPerOccupiedRoom'][$currYear] = $total_room_night ? round($carbon_footPrint / $total_room_night, 2) : 0;
	$data['measures']['HotelCarbonFootprintPerOccupiedRoom'][$currYear - 1] = $total_room_night_prev ? round($carbon_footPrint_prev / $total_room_night_prev, 2) : 0;
	$data['measures']['HotelCarbonFootprintPerOccupiedRoom'][$currYear - 2] = $total_room_night_minus_two_year ? round($carbon_footPrint_minus_two_year / $total_room_night_minus_two_year, 2) : 0;
	$data['measures']['HotelCarbonFootprintPerOccupiedRoom'][$currYear - 3] = $total_room_night_minus_three_year ? round($carbon_footPrint_minus_three_year / $total_room_night_minus_three_year, 2) : 0;
	$data['measures']['HotelCarbonFootprintPerOccupiedRoom']['measure_id'] = 3;
	$data['measures']['HCMIRoomsFootprintPerOccupiedRoom'][$currYear] = $total_room_night ? round($carbon_footPrint / $total_room_night, 2) : 0;
	$data['measures']['HCMIRoomsFootprintPerOccupiedRoom'][$currYear - 1] = $total_room_night_prev ? round($carbon_footPrint_prev / $total_room_night_prev, 2) : 0;
	$data['measures']['HCMIRoomsFootprintPerOccupiedRoom'][$currYear - 2] = $total_room_night_minus_two_year ? round($carbon_footPrint_minus_two_year / $total_room_night_minus_two_year, 2) : 0;
	$data['measures']['HCMIRoomsFootprintPerOccupiedRoom'][$currYear - 3] = $total_room_night_minus_three_year ? round($carbon_footPrint_minus_three_year / $total_room_night_minus_three_year, 2) : 0;
	$data['measures']['HCMIRoomsFootprintPerOccupiedRoom']['measure_id'] = 1;
	$data['measures']['HotelCarbonFootprintPerSquareMeter'][$currYear] = round($carbon_footPrint / $result['site_builtup_area'], 2);
	$data['measures']['HotelCarbonFootprintPerSquareMeter'][$currYear - 1] = round($carbon_footPrint_prev / $result['site_builtup_area'], 2);
	$data['measures']['HotelCarbonFootprintPerSquareMeter'][$currYear - 2] = round($carbon_footPrint_minus_two_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelCarbonFootprintPerSquareMeter'][$currYear - 3] = round($carbon_footPrint_minus_three_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelCarbonFootprintPerSquareMeter']['measure_id'] = 4;
	$data['measures']['HotelEnergyUsagePerOccupiedRoom'][$currYear] = $total_room_night ? round($utility_kwh_total / $total_room_night, 2) : 0;
	$data['measures']['HotelEnergyUsagePerOccupiedRoom'][$currYear - 1] = $total_room_night_prev ? round($utility_kwh_total_prev / $total_room_night_prev, 2) : 0;
	$data['measures']['HotelEnergyUsagePerOccupiedRoom'][$currYear - 2] = $total_room_night_minus_two_year ? round($utility_kwh_total_minus_two_year / $total_room_night_minus_two_year, 2) : 0;
	$data['measures']['HotelEnergyUsagePerOccupiedRoom'][$currYear - 3] = $total_room_night_minus_three_year ? round($utility_kwh_total_minus_three_year / $total_room_night_minus_three_year, 2) : 0;
	$data['measures']['HotelEnergyUsagePerOccupiedRoom']['measure_id'] = 5;
	$data['measures']['HotelEnergyUsagePerSquareMeter'][$currYear] = round($utility_kwh_total / $result['site_builtup_area'], 2);
	$data['measures']['HotelEnergyUsagePerSquareMeter'][$currYear - 1] = round($utility_kwh_total_prev / $result['site_builtup_area'], 2);
	$data['measures']['HotelEnergyUsagePerSquareMeter'][$currYear - 2] = round($utility_kwh_total_minus_two_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelEnergyUsagePerSquareMeter'][$currYear - 3] = round($utility_kwh_total_minus_three_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelEnergyUsagePerSquareMeter']['measure_id'] = 6;
	$data['measures']['HotelWaterUsagePerOccupiedRoom'][$currYear] = $total_room_night ? round($water_total_consumption / $total_room_night, 2) : 0;
	$data['measures']['HotelWaterUsagePerOccupiedRoom'][$currYear - 1] = $total_room_night_prev ? round($water_total_consumption_prev / $total_room_night_prev, 2) : 0;
	$data['measures']['HotelWaterUsagePerOccupiedRoom'][$currYear - 2] = $total_room_night_minus_two_year ? round($water_total_consumption_minus_two_year / $total_room_night_minus_two_year, 2) : 0;
	$data['measures']['HotelWaterUsagePerOccupiedRoom'][$currYear - 3] = $total_room_night_minus_three_year ? round($water_total_consumption_minus_three_year / $total_room_night_minus_three_year, 2) : 0;
	$data['measures']['HotelWaterUsagePerOccupiedRoom']['measure_id'] = 7;
	$data['measures']['HotelWaterUsagePerSquareMeter'][$currYear] = round($water_total_consumption / $result['site_builtup_area'], 2);
	$data['measures']['HotelWaterUsagePerSquareMeter'][$currYear - 1] = round($water_total_consumption_prev / $result['site_builtup_area'], 2);
	$data['measures']['HotelWaterUsagePerSquareMeter'][$currYear - 2] = round($water_total_consumption_minus_two_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelWaterUsagePerSquareMeter'][$currYear - 3] = round($water_total_consumption_minus_three_year / $result['site_builtup_area'], 2);
	$data['measures']['HotelWaterUsagePerSquareMeter']['measure_id'] = 8;
	$data['measures']['site_location'] = $site_detials['site_location_name'];
	return $data;
    }


	public function getCarbonRecords($site_id, $site_detials)
	{
		// $site_detials = $this->sites_model->get_site_detail_custom($site_id);
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');
		$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'water');

		/*Available utilies for site*/
		$this->utilities_model->utilities_month = date("n") - 1;
		$this->utilities_model->utilities_year  = date("Y");

		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year  = date("Y") - 1;
		}

		$this->utilities_model->site_id = $site_id;
		$getUtilities = $this->utilities_model->getUtility();
		$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
		$getUtilities['total_lpg_cost'] = ($getUtilities['total_lpg_cost'] != '') ? $getUtilities['total_lpg_cost'] : 0;
		$getUtilities['total_fuel_oil_cost'] = ($getUtilities['total_fuel_oil_cost'] != '') ? $getUtilities['total_fuel_oil_cost'] : 0;
		$getUtilities['total_natural_gas_cost'] = ($getUtilities['total_natural_gas_cost'] != '') ? $getUtilities['total_natural_gas_cost'] : 0;
		$getUtilities['district_heating_cost'] = ($getUtilities['district_heating_cost'] != '') ? $getUtilities['district_heating_cost'] : 0;
		$getUtilities['district_cooling_cost'] = ($getUtilities['district_cooling_cost'] != '') ? $getUtilities['district_cooling_cost'] : 0;
		$totalelectricitykwh = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'];
		$totalfueloil = $getUtilities['total_fuel_oil_cost']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
		$totalnaturalgas = $getUtilities['total_natural_gas_cost']; // - $getUtilities['onsite_generators_natural_gas_quantity'];

		$currentMonth_footPrint = ($electricity_mmbtu_rate * $totalelectricitykwh * $site_detials['electricity_emission_factor']) + ($lpg_mmbtu_rate * $getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloil * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgas * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);

		$dataCarbon['carbon_footprint_currentMonth'] = $currentMonth_footPrint;
		$dataCarbon['total_utility_cost_currentMonth'] = $getUtilities['total_electricity_cost'] + $getUtilities['total_fuel_oil_cost'] + $getUtilities['total_lpg_cost'] + $getUtilities['total_natural_gas_cost'] + $getUtilities['district_heating_cost'] + $getUtilities['district_cooling_cost'] + $getUtilities['water_total_consumption_cost'] + $getUtilities['district_cooling_fixed_cost'] + $getUtilities['district_heating_fixed_cost'] + $getUtilities['lpg_fixed_cost'] + $getUtilities['natural_gas_fixed_cost'] + $getUtilities['water_fixed_cost'];

		$total_budgeted_cost_currentMonth = $getUtilities['electricity_total_budget_cost'] + $getUtilities['fuel_total_budget_cost'] + $getUtilities['lpg_total_budget_cost'] + $getUtilities['natural_gas_total_budget_cost'] + $getUtilities['district_heating_total_budget_cost'] + $getUtilities['district_cooling_total_budget_cost'] + $getUtilities['water_total_consumption_budget_cost'];

		$variation = ($dataCarbon['total_utility_cost_currentMonth'] != '' && $total_budgeted_cost_currentMonth != '') ? $total_budgeted_cost_currentMonth - $dataCarbon['total_utility_cost_currentMonth'] : 0;
		$dataCarbon['variation'] = $variation;
		$dataCarbon['variationPercentage'] = $dataCarbon['total_utility_cost_currentMonth'] != '' ? ($variation * 100) / $dataCarbon['total_utility_cost_currentMonth'] : 0;

		//same month previous year added by hp18
		$this->utilities_model->utilities_month = date("n") - 1;
		$this->utilities_model->utilities_year = date("Y") - 1;

		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year = date("Y") - 2;
		}
		$this->sites_model->year = $this->utilities_model->utilities_year;
		$this->utilities_model->site_id = $site_id;
		$utilitiesSameMonthPreviousYear = $this->utilities_model->getUtility();

		$utilitiesSameMonthPreviousYear['total_electricity_kwh'] = ($utilitiesSameMonthPreviousYear['total_electricity_kwh'] != '') ? $utilitiesSameMonthPreviousYear['total_electricity_kwh'] : 0;
		$utilitiesSameMonthPreviousYear['total_lpg'] = ($utilitiesSameMonthPreviousYear['total_lpg'] != '') ? $utilitiesSameMonthPreviousYear['total_lpg'] : 0;
		$utilitiesSameMonthPreviousYear['total_fuel_oil'] = ($utilitiesSameMonthPreviousYear['total_fuel_oil'] != '') ? $utilitiesSameMonthPreviousYear['total_fuel_oil'] : 0;
		$utilitiesSameMonthPreviousYear['total_natural_gas'] = ($utilitiesSameMonthPreviousYear['total_natural_gas'] != '') ? $utilitiesSameMonthPreviousYear['total_natural_gas'] : 0;
		$utilitiesSameMonthPreviousYear['district_heating'] = ($utilitiesSameMonthPreviousYear['district_heating'] != '') ? $utilitiesSameMonthPreviousYear['district_heating'] : 0;
		$utilitiesSameMonthPreviousYear['district_cooling'] = ($utilitiesSameMonthPreviousYear['district_cooling'] != '') ? $utilitiesSameMonthPreviousYear['district_cooling'] : 0;

		$totalelectricitykwhprev = $utilitiesSameMonthPreviousYear['total_electricity_kwh'] - $utilitiesSameMonthPreviousYear['onsite_generators_quantity'];
		$totalfueloilprev = $utilitiesSameMonthPreviousYear['total_fuel_oil']; // - $utilitiesSameMonthPreviousYear['onsite_generators_fuel_oil_quantity'];
		$totalnaturalgasprev = $utilitiesSameMonthPreviousYear['total_natural_gas']; // - $utilitiesSameMonthPreviousYear['onsite_generators_natural_gas_quantity'];
		$SameMonthPreviousYear_footPrint = ($electricity_mmbtu_rate * $totalelectricitykwhprev * $site_detials['electricity_emission_factor']) + ($lpg_mmbtu_rate * $utilitiesSameMonthPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilprev * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgasprev * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $utilitiesSameMonthPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $utilitiesSameMonthPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);

		$dataCarbon['carbon_footprint_SameMonthPreviousYear'] = $SameMonthPreviousYear_footPrint;

		// YTD
		$ytd_carbon_footprint = $dataCarbon['carbon_footprint_currentMonth'];
		$ytd_carbon_footprintPreviousYear = 0;
		$total_utility_costs = $dataCarbon['total_utility_cost_currentMonth'];
		$total_budgeted_costs = $total_budgeted_cost_currentMonth;
		$currentMonth_footPrint_new = $baselineMonth_footPrint_new = 0;
		if (date("n") > 1) {

			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date('Y');
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			for ($i = 1; $i <= (date("n") - 1); $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y");

				$getUtilities = $this->utilities_model->getUtility();
				$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
				$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
				$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
				$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
				$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
				$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

				$totalelectricitykwhcurrentyear = $getUtilities['total_electricity_kwh'];
				if (isset($electricity_emission_factor_percentage)) {
					$currentMonth_footPrint_new += ((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_emission_factor * $electricity_mmbtu_rate) + ($getUtilities['total_lpg'] * $site_detials['lpg_emission_factor'] * $lpg_mmbtu_rate) + ($fuel_mmbtu_rate * $getUtilities['total_fuel_oil'] * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $getUtilities['total_natural_gas'] * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				} else {
					$currentMonth_footPrint_new += ((((1) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_emission_factor * $electricity_mmbtu_rate) + ($getUtilities['total_lpg'] * $site_detials['lpg_emission_factor'] * $lpg_mmbtu_rate) + ($fuel_mmbtu_rate * $getUtilities['total_fuel_oil'] * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $getUtilities['total_natural_gas'] * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				}
			}
		} else {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date('Y') - 1;
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			for ($i = 1; $i <= 12; $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y") - 1;

				$getUtilities = $this->utilities_model->getUtility();

				$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
				$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
				$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
				$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
				$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
				$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

				$totalelectricitykwhcurrentyear = $getUtilities['total_electricity_kwh'];
				$totalfueloilcurrentyear = $getUtilities['total_fuel_oil']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
				$totalnaturalgascurrentyear = $getUtilities['total_natural_gas']; // - $getUtilities['onsite_generators_natural_gas_quantity'];
				if (isset($electricity_emission_factor_percentage)) {
					$currentMonth_footPrint_new += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilcurrentyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgascurrentyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				} else {
					$currentMonth_footPrint_new += (((((1) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilcurrentyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgascurrentyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				}
			}
		}

if(date("n") != 1) {
		$compareMonth = date("n");
	} else {
		$compareMonth = 12;
	}
	for ($i = 1; $i <= ($compareMonth); $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = $site_detials['baseline_regression_year'];
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = $site_detials['baseline_regression_year'];

			$getUtilities = $this->utilities_model->getUtility();
			$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
			$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
			$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
			$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
			$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
			$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

			$totalelectricitykwhbaselineyear = $getUtilities['total_electricity_kwh'];
			$totalfueloilbaselineyear = $getUtilities['total_fuel_oil']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
			$totalnaturalgasbaselineyear = $getUtilities['total_natural_gas']; // - $getUtilities['onsite_generators_natural_gas_quantity'];
			$this->load->model('sites/sites_model');
			$this->sites_model->year = $site_detials['baseline_regression_year'];
			$site_detials = $this->sites_model->get_site_detail_custom($site_id);
			// echo '((((((1 - ('.$electricity_emission_factor_percentage.'/100)) *'. $totalelectricitykwhbaselineyear.')  - '.$getUtilities['onsite_generators_quantity'].' - '.$getUtilities['total_renewable_energy_production'].') * '.$electricity_mmbtu_rate.') * '.$electricity_emission_factor.') )<br/>';
			// echo ($natural_gas_mmbtu_rate.' * '.$totalnaturalgasbaselineyear.' * '.$site_detials['natural_gas_emission_factor']);
			if (isset($electricity_emission_factor_percentage)) {
				$baselineMonth_footPrint_new += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhbaselineyear)  - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilbaselineyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgasbaselineyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$baselineMonth_footPrint_new += (((((1) * $totalelectricitykwhbaselineyear)  - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilbaselineyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgasbaselineyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			}
		}
		for ($i = 1; $i < date("n"); $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date("Y") - 1;
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = date("Y") - 1;
			$this->sites_model->year = $this->utilities_model->utilities_year;
			$YtdUtilitiesPreviousYear = $this->utilities_model->getUtility();

			$YtdUtilitiesPreviousYear['total_electricity_kwh'] = ($YtdUtilitiesPreviousYear['total_electricity_kwh'] != '') ? $YtdUtilitiesPreviousYear['total_electricity_kwh'] : 0;
			$YtdUtilitiesPreviousYear['total_lpg'] = ($YtdUtilitiesPreviousYear['total_lpg'] != '') ? $YtdUtilitiesPreviousYear['total_lpg'] : 0;
			$YtdUtilitiesPreviousYear['total_fuel_oil'] = ($YtdUtilitiesPreviousYear['total_fuel_oil'] != '') ? $YtdUtilitiesPreviousYear['total_fuel_oil'] : 0;
			$YtdUtilitiesPreviousYear['total_natural_gas'] = ($YtdUtilitiesPreviousYear['total_natural_gas'] != '') ? $YtdUtilitiesPreviousYear['total_natural_gas'] : 0;
			$YtdUtilitiesPreviousYear['district_heating'] = ($YtdUtilitiesPreviousYear['district_heating'] != '') ? $YtdUtilitiesPreviousYear['district_heating'] : 0;
			$YtdUtilitiesPreviousYear['district_cooling'] = ($YtdUtilitiesPreviousYear['district_cooling'] != '') ? $YtdUtilitiesPreviousYear['district_cooling'] : 0;
			$YtdUtilitiesPreviousYear['water_total_consumption'] = ($YtdUtilitiesPreviousYear['water_total_consumption'] != '') ? $YtdUtilitiesPreviousYear['water_total_consumption'] : 0;

			$totalelectricitykwhpreviousyear = $YtdUtilitiesPreviousYear['total_electricity_kwh'];
			$totalfueloilpreviousyear = $YtdUtilitiesPreviousYear['total_fuel_oil_cost']; // - $YtdUtilitiesPreviousYear['onsite_generators_fuel_oil_quantity'];
			$totalnaturalgaspreviousyear = $YtdUtilitiesPreviousYear['total_natural_gas_cost']; // - $YtdUtilitiesPreviousYear['onsite_generators_natural_gas_quantity'];
			if (isset($electricity_emission_factor_percentage)) {
				$ytd_carbon_footprintPreviousYear += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhpreviousyear)  - $YtdUtilitiesPreviousYear['onsite_generators_quantity'] - $YtdUtilitiesPreviousYear['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $YtdUtilitiesPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilpreviousyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgaspreviousyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $YtdUtilitiesPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $YtdUtilitiesPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$ytd_carbon_footprintPreviousYear += (((((1) * $totalelectricitykwhpreviousyear)  - $YtdUtilitiesPreviousYear['onsite_generators_quantity'] - $YtdUtilitiesPreviousYear['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $electricity_emission_factor) + ($lpg_mmbtu_rate * $YtdUtilitiesPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $totalfueloilpreviousyear * $site_detials['fuel_emission_factor']) + ($natural_gas_mmbtu_rate * $totalnaturalgaspreviousyear * $site_detials['natural_gas_emission_factor']) + ($heating_district_mmbtu_rate * $YtdUtilitiesPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $YtdUtilitiesPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			}
		}

		for ($i = 1; $i <= date("n"); $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date("Y");
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = date("Y");
			$this->sites_model->year = $this->utilities_model->utilities_year;
			$YtdUtilities = $this->utilities_model->getUtility();

			$YtdUtilities['total_electricity_kwh'] = ($YtdUtilities['total_electricity_kwh'] != '') ? $YtdUtilities['total_electricity_kwh'] : 0;
			$YtdUtilities['total_lpg_cost'] = ($YtdUtilities['total_lpg_cost'] != '') ? $YtdUtilities['total_lpg_cost'] : 0;
			$YtdUtilities['total_fuel_oil_cost'] = ($YtdUtilities['total_fuel_oil_cost'] != '') ? $YtdUtilities['total_fuel_oil_cost'] : 0;
			$YtdUtilities['district_heating_cost'] = ($YtdUtilities['district_heating_cost'] != '') ? $YtdUtilities['district_heating_cost'] : 0;
			$YtdUtilities['district_cooling_cost'] = ($YtdUtilities['district_cooling_cost'] != '') ? $YtdUtilities['district_cooling_cost'] : 0;
			$YtdUtilities['water_total_consumption_cost'] = ($YtdUtilities['water_total_consumption_cost'] != '') ? $YtdUtilities['water_total_consumption_cost'] : 0;
			$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
			if (isset($electricity_emission_factor_percentage)) {
				$ytd_carbon_footprint += (((((1 - ($electricity_emission_factor_percentage / 100)) * $YtdUtilities['total_electricity_kwh'])  - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $site_detials['electricity_emission_factor']) + ($lpg_mmbtu_rate * $YtdUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $YtdUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($heating_district_mmbtu_rate * $YtdUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $YtdUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$ytd_carbon_footprint += (((((1) * $YtdUtilities['total_electricity_kwh'])  - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']) * $electricity_mmbtu_rate) * $site_detials['electricity_emission_factor']) + ($lpg_mmbtu_rate * $YtdUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($fuel_mmbtu_rate * $YtdUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($heating_district_mmbtu_rate * $YtdUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($cooling_district_mmbtu_rate * $YtdUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
			}
			//For variation
			$total_utility_costs += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'];

			$YtdUtilities['electricity_total_budget_cost'] = ($YtdUtilities['electricity_total_budget_cost'] != '') ? $YtdUtilities['electricity_total_budget_cost'] : 0;
			$YtdUtilities['fuel_total_budget_cost'] = ($YtdUtilities['fuel_total_budget_cost'] != '') ? $YtdUtilities['fuel_total_budget_cost'] : 0;
			$YtdUtilities['lpg_total_budget_cost'] = ($YtdUtilities['lpg_total_budget_cost'] != '') ? $YtdUtilities['lpg_total_budget_cost'] : 0;
			$YtdUtilities['natural_gas_total_budget_cost'] = ($YtdUtilities['natural_gas_total_budget_cost'] != '') ? $YtdUtilities['natural_gas_total_budget_cost'] : 0;
			$YtdUtilities['district_heating_total_budget_cost'] = ($YtdUtilities['district_heating_total_budget_cost'] != '') ? $YtdUtilities['district_heating_total_budget_cost'] : 0;
			$YtdUtilities['district_cooling_total_budget_cost'] = ($YtdUtilities['district_cooling_total_budget_cost'] != '') ? $YtdUtilities['district_cooling_total_budget_cost'] : 0;
			$YtdUtilities['water_total_consumption_budget_cost'] = ($YtdUtilities['water_total_consumption_budget_cost'] != '') ? $YtdUtilities['water_total_consumption_budget_cost'] : 0;

			$total_budgeted_costs += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
		}

		//ytd variation
		if (date("n") > 1) {
			$total_utility_costs_variation = 0;
			$total_budgeted_costs_variation = 0;
			for ($i = 1; $i <= (date("n") - 1); $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y");

				$YtdUtilities = $this->utilities_model->getUtility();

				$YtdUtilities['total_electricity_cost'] = ($YtdUtilities['total_electricity_cost'] != '') ? $YtdUtilities['total_electricity_cost'] : 0;
				$YtdUtilities['total_fuel_oil_cost'] = ($YtdUtilities['total_fuel_oil_cost'] != '') ? $YtdUtilities['total_fuel_oil_cost'] : 0;
				$YtdUtilities['total_lpg_cost'] = ($YtdUtilities['total_lpg_cost'] != '') ? $YtdUtilities['total_lpg_cost'] : 0;
				$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
				$YtdUtilities['district_heating_cost'] = ($YtdUtilities['district_heating_cost'] != '') ? $YtdUtilities['district_heating_cost'] : 0;
				$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
				$YtdUtilities['district_cooling_cost'] = ($YtdUtilities['district_cooling_cost'] != '') ? $YtdUtilities['district_cooling_cost'] : 0;
				$YtdUtilities['water_total_consumption_cost'] = ($YtdUtilities['water_total_consumption_cost'] != '') ? $YtdUtilities['water_total_consumption_cost'] : 0;
				$YtdUtilities['district_cooling_fixed_cost'] = ($YtdUtilities['district_cooling_fixed_cost'] != '') ? $YtdUtilities['district_cooling_fixed_cost'] : 0;
				$YtdUtilities['district_heating_fixed_cost'] = ($YtdUtilities['district_heating_fixed_cost'] != '') ? $YtdUtilities['district_heating_fixed_cost'] : 0;
				$YtdUtilities['lpg_fixed_cost'] = ($YtdUtilities['lpg_fixed_cost'] != '') ? $YtdUtilities['lpg_fixed_cost'] : 0;
				$YtdUtilities['natural_gas_fixed_cost'] = ($YtdUtilities['natural_gas_fixed_cost'] != '') ? $YtdUtilities['natural_gas_fixed_cost'] : 0;
				$YtdUtilities['water_fixed_cost'] = ($YtdUtilities['water_fixed_cost'] != '') ? $YtdUtilities['water_fixed_cost'] : 0;

				$YtdUtilities['electricity_total_budget_cost'] = ($YtdUtilities['electricity_total_budget_cost'] != '') ? $YtdUtilities['electricity_total_budget_cost'] : 0;
				$YtdUtilities['fuel_total_budget_cost'] = ($YtdUtilities['fuel_total_budget_cost'] != '') ? $YtdUtilities['fuel_total_budget_cost'] : 0;
				$YtdUtilities['lpg_total_budget_cost'] = ($YtdUtilities['lpg_total_budget_cost'] != '') ? $YtdUtilities['lpg_total_budget_cost'] : 0;
				$YtdUtilities['natural_gas_total_budget_cost'] = ($YtdUtilities['natural_gas_total_budget_cost'] != '') ? $YtdUtilities['natural_gas_total_budget_cost'] : 0;
				$YtdUtilities['district_heating_total_budget_cost'] = ($YtdUtilities['district_heating_total_budget_cost'] != '') ? $YtdUtilities['district_heating_total_budget_cost'] : 0;
				$YtdUtilities['district_cooling_total_budget_cost'] = ($YtdUtilities['district_cooling_total_budget_cost'] != '') ? $YtdUtilities['district_cooling_total_budget_cost'] : 0;
				$YtdUtilities['water_total_consumption_budget_cost'] = ($YtdUtilities['water_total_consumption_budget_cost'] != '') ? $YtdUtilities['water_total_consumption_budget_cost'] : 0;

				//For variation
				$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'] + $YtdUtilities['district_cooling_fixed_cost'] + $YtdUtilities['district_heating_fixed_cost'] + $YtdUtilities['lpg_fixed_cost'] + $YtdUtilities['natural_gas_fixed_cost'] + $YtdUtilities['water_fixed_cost'];

				$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
			}
		} else {
			$total_utility_costs_variation = 0;
			$total_budgeted_costs_variation = 0;
			for ($i = 1; $i <= 12; $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y") - 1;

				$YtdUtilities = $this->utilities_model->getUtility();

				//For variation
				$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'] + $YtdUtilities['district_cooling_fixed_cost'] + $YtdUtilities['district_heating_fixed_cost'] + $YtdUtilities['lpg_fixed_cost'] + $YtdUtilities['natural_gas_fixed_cost'] + $YtdUtilities['water_fixed_cost'];

				$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
			}
		}

		$variation_ytd = ($total_utility_costs_variation != '' && $total_budgeted_costs_variation != '') ? $total_budgeted_costs_variation - $total_utility_costs_variation : 0;
		$dataCarbon['variation_ytd'] = $variation_ytd;
		$dataCarbon['variationPercentage_ytd'] = $total_utility_costs_variation != '' ? ($variation_ytd * 100) / $total_utility_costs_variation : 0;
		$dataCarbon['ytd_carbon_footprint'] = $ytd_carbon_footprint;
		$dataCarbon['ytd_carbon_footprintPreviousYear'] = $ytd_carbon_footprintPreviousYear;
		$dataCarbon['ytd_carbon_footprint_new'] = $currentMonth_footPrint_new;
		$dataCarbon['ytd_carbon_footprint_baseline_new'] = $baselineMonth_footPrint_new;
		$currentMonth_cost_roomNight = ($dataCarbon['total_utility_cost_currentMonth'] != '' && $getUtilities['total_room_night']) ? $dataCarbon['total_utility_cost_currentMonth'] / $getUtilities['total_room_night'] : 0;
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
		$getUtilities_lastMonth = $this->utilities_model->getUtility();
		$getUtilities_lastMonth['total_electricity_cost'] = ($getUtilities_lastMonth['total_electricity_cost'] != '') ? $getUtilities_lastMonth['total_electricity_cost'] : 0;
		$getUtilities_lastMonth['total_fuel_oil_cost'] = ($getUtilities_lastMonth['total_fuel_oil_cost'] != '') ? $getUtilities_lastMonth['total_fuel_oil_cost'] : 0;
		$getUtilities_lastMonth['total_lpg_cost'] = ($getUtilities_lastMonth['total_lpg_cost'] != '') ? $getUtilities_lastMonth['total_lpg_cost'] : 0;
		$getUtilities_lastMonth['total_natural_gas_cost'] = ($getUtilities_lastMonth['total_natural_gas_cost'] != '') ? $getUtilities_lastMonth['total_natural_gas_cost'] : 0;
		$getUtilities_lastMonth['district_heating_cost'] = ($getUtilities_lastMonth['district_heating_cost'] != '') ? $getUtilities_lastMonth['district_heating_cost'] : 0;
		$getUtilities_lastMonth['district_cooling_cost'] = ($getUtilities_lastMonth['district_cooling_cost'] != '') ? $getUtilities_lastMonth['district_cooling_cost'] : 0;
		$getUtilities_lastMonth['water_total_consumption_cost'] = ($getUtilities_lastMonth['water_total_consumption_cost'] != '') ? $getUtilities_lastMonth['water_total_consumption_cost'] : 0;
		$getUtilities_lastMonth['district_heating_fixed_cost'] = ($getUtilities_lastMonth['district_heating_fixed_cost'] != '') ? $getUtilities_lastMonth['district_heating_fixed_cost'] : 0;
		$getUtilities_lastMonth['district_cooling_fixed_cost'] = ($getUtilities_lastMonth['district_cooling_fixed_cost'] != '') ? $getUtilities_lastMonth['district_cooling_fixed_cost'] : 0;
		$getUtilities_lastMonth['lpg_fixed_cost'] = ($getUtilities_lastMonth['lpg_fixed_cost'] != '') ? $getUtilities_lastMonth['lpg_fixed_cost'] : 0;
		$getUtilities_lastMonth['natural_gas_fixed_cost'] = ($getUtilities_lastMonth['natural_gas_fixed_cost'] != '') ? $getUtilities_lastMonth['natural_gas_fixed_cost'] : 0;
		$getUtilities_lastMonth['water_fixed_cost'] = ($getUtilities_lastMonth['water_fixed_cost'] != '') ? $getUtilities_lastMonth['water_fixed_cost'] : 0;
		$dataCarbon['total_utility_cost_lastMonth'] = $getUtilities_lastMonth['total_electricity_cost'] + $getUtilities_lastMonth['total_fuel_oil_cost'] + $getUtilities_lastMonth['total_lpg_cost'] + $getUtilities_lastMonth['total_natural_gas_cost'] + $getUtilities_lastMonth['district_heating_cost'] + $getUtilities_lastMonth['district_cooling_cost'] + $getUtilities_lastMonth['water_total_consumption_cost'] + $getUtilities_lastMonth['district_heating_fixed_cost'] + $getUtilities_lastMonth['district_cooling_fixed_cost'] + $getUtilities_lastMonth['lpg_fixed_cost'] + $getUtilities_lastMonth['natural_gas_fixed_cost'] + $getUtilities_lastMonth['water_fixed_cost'];
		$lastMonth_cost_roomNight = ($dataCarbon['total_utility_cost_lastMonth'] != '' && $getUtilities_lastMonth['total_room_night']) ? $dataCarbon['total_utility_cost_lastMonth'] / $getUtilities_lastMonth['total_room_night'] : 0;
		//last month - utilities cost/room night
		$this->utilities_model->utilities_month = date('n') - 1;
		$this->utilities_model->utilities_year = date("Y");
		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year = date("Y") - 1;
		}
		$getUtilities_lastMonth = $this->utilities_model->getUtility();
		$utility_cost_calculation = array();
		// $dataCarbon['currentmonth'] = date('F', strtotime('-1 months'));
		// $dataCarbon['currentyear']  = date('Y');
		// $dataCarbon['lastmonth'] = date('F', strtotime('-1 months'));
		// $dataCarbon['lastyear']  = date('Y', strtotime('-1 year'));
		//same month last year- utilities cost/room night
		$this->utilities_model->utilities_month = date('n') - 1;
		$this->utilities_model->utilities_year = date("Y", strtotime("-1 year"));
		$dataCarbon['lastyear']     = date('Y', strtotime('-1 year'));
		$dataCarbon['currentmonth'] = date('F', strtotime('-1 months'));
		$dataCarbon['lastmonth']    = date('F', strtotime('-1 months'));
		$dataCarbon['currentyear']  = date("Y");
		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year  = date("Y") - 2;
			$dataCarbon['lastyear']     = date("Y") - 2;
			$dataCarbon['currentmonth'] = date('F', strtotime('-13 months'));
			$dataCarbon['currentyear']  = date("Y", strtotime("-1 year"));
		}
		$getUtilities_sameMonth_lastYear = $this->utilities_model->getUtility();
		$getUtilities_sameMonth_lastYear['total_electricity_cost'] = ($getUtilities_sameMonth_lastYear['total_electricity_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_electricity_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] = ($getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_lpg_cost'] = ($getUtilities_sameMonth_lastYear['total_lpg_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_lpg_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_natural_gas_cost'] = ($getUtilities_sameMonth_lastYear['total_natural_gas_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_natural_gas_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_heating_cost'] = ($getUtilities_sameMonth_lastYear['district_heating_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_heating_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_cooling_cost'] = ($getUtilities_sameMonth_lastYear['district_cooling_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_cooling_cost'] : 0;
		$getUtilities_sameMonth_lastYear['water_total_consumption_cost'] = ($getUtilities_sameMonth_lastYear['water_total_consumption_cost'] != '') ? $getUtilities_sameMonth_lastYear['water_total_consumption_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] = ($getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] = ($getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['lpg_fixed_cost'] = ($getUtilities_sameMonth_lastYear['lpg_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['lpg_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] = ($getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['water_fixed_cost'] = ($getUtilities_sameMonth_lastYear['water_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['water_fixed_cost'] : 0;
		$dataCarbon['total_utility_cost_sameMonth_lastYear'] = $getUtilities_sameMonth_lastYear['total_electricity_cost'] + $getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] + $getUtilities_sameMonth_lastYear['total_lpg_cost'] + $getUtilities_sameMonth_lastYear['total_natural_gas_cost'] + $getUtilities_sameMonth_lastYear['district_heating_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_cost'] + $getUtilities_sameMonth_lastYear['water_total_consumption_cost'] + $getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] + $getUtilities_sameMonth_lastYear['lpg_fixed_cost'] + $getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] + $getUtilities_sameMonth_lastYear['water_fixed_cost'];
		$dataCarbon['variation_ytd'] = $variation_ytd;
		$dataCarbon['variationPercentage_ytd'] = $total_utility_costs_variation != '' ? ($variation_ytd * 100) / $total_utility_costs_variation : 0;

		$dataCarbon['ytd_carbon_footprint'] = $ytd_carbon_footprint;
		$dataCarbon['ytd_carbon_footprintPreviousYear'] = $ytd_carbon_footprintPreviousYear;
		$dataCarbon['ytd_carbon_footprint_new'] = $currentMonth_footPrint_new;
		$dataCarbon['ytd_carbon_footprint_baseline_new'] = $baselineMonth_footPrint_new;

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

		$dataCarbon['cdd_hdd'] = $utility_cost_calculation_chr;
		return $dataCarbon;
	}
}
define('K_PATH_IMAGES', BASE_PATH_CUSTOM);
require_once BASE_PATH_CUSTOM . '/application/libraries/tcpdf/tcpdf.php';
class MYPDF extends TCPDF
{

    public function Header()
    {
	$image_file = K_PATH_IMAGES . SITE_PDF_HEADER_LOGO;
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
