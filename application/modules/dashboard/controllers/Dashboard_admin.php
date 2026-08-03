<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  CMS Admin Controller
 *
 *  CMS Admin controller to display add / Edit / Delete / List CMS page for each language.
 *
 * @package CIDemoApplication
 *
 * @copyright   (c) 2013, TatvaSoft
 * @author AMPT
 */
class Dashboard_admin extends Base_Admin_Controller {
    /*
     * Create an instance
     */

    function __construct() {
	parent::__construct();
	// Login check for admin
	$this->access_control($this->access_rules());

	$this->load->model('dashboard_model');

	// Breadcrumb settings
	$this->breadcrumb->add('Dashboard', base_url() . $this->section_name . '/dashboard');
    }

    /**
     * function accessRules to check page access
     */
    private function access_rules() {
	return array(
	    array(
		'actions' => array('index2','index', 'sites'),
		'users' => array('@'),
	    )
	);
    }

    /**
     * action to display language wise list of cms page
     * @param string $language_code
     */
    function index($language_code = '') {
	$this->theme->set('page_title', 'Dashboard');

	// Load models
	$this->load->model('reports/reports_model');
	$this->load->model('sites/sites_model');
	$this->load->model('utilities/utilities_model');
	$this->load->model('sites/site_waste_model');

	// Site and baseline data
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$site_detials = $this->sites_model->get_site_detail_custom($site_id);
	$this->reports_model->site_id = $site_id;
	$baselineYear = $site_detials['baseline_regression_year'];

	// Date parameters
	$dateParams = getProgressWidgetDateParams();
	$current_month = $dateParams['month'];
	$current_year = $dateParams['year'];
	$previous_year = $dateParams['previous_year'];
	$running_month = $dateParams['running_month'];
	$running_year = $dateParams['running_year'];

	define('CURRENT_YEAR_MAX_MONTH_ID', $running_month - 1);

	// Chart defaults
	$utility_chart_year = ($running_month == 1) ? $running_year - 1 : $running_year;
	$startdate = '1/' . $utility_chart_year;
	$enddate = '12/' . $utility_chart_year;
	$utility_name = 'electricity';
	$progress_chart_year = 'eui_by_energy_composition';
	$progress_chart_utility = 'energy';
	$progress_chart_unit = 'per_rn';
	$performance_chart_type = 'utility_consumption';
	$onclickPerformance = $onclickProgress = $onclickUtility = 0;
	$is_percent_check = $is_occupancy_check_utility = $is_budget_check_utility = 0;
	$is_occupancy_check = $is_baseline_year = 0;

	// Handle POST data
	if ($this->input->post()) {
	    $utility_chart_year = $this->input->post('utility_chart_year');
	    $startdate = '1/' . $utility_chart_year;
	    $enddate = '12/' . $utility_chart_year;
	    $utility_name = $this->input->post('utility_type_list');
	    $progress_chart_utility = $this->input->post('progress_chart_utility');
	    $progress_chart_unit = $this->input->post('progress_chart_unit');
	    if ($progress_chart_utility == 'water') {
		$progress_chart_year = is_numeric($this->input->post('progress_chart_year')) ? $this->input->post('progress_chart_year') : $previous_year;
		$progress_chart_unit = 'per_gn';
	    } else {
		$progress_chart_year = $this->input->post('progress_chart_year');
	    }
	    $performance_chart_type = $this->input->post('performance_chart_type');
	    $excludedPerformanceTypes = ['diversion_rate', 'food_and_beverage_waste', 'food_and_beverage_waste_total_food_handled', 'food_and_beverage_waste_room_night', 'tonnes_of_carbon_offsets_purchased'];
	    if (in_array($performance_chart_type, $excludedPerformanceTypes)) {
		$is_occupancy_check = 0;
		$is_baseline_year = 0;
	    } else if ($progress_chart_year == 'industry_benchmark') {
		$is_percent_check = 0;
	    } else {
		$is_occupancy_check = $this->input->post('is_occupancy_check');
		$is_baseline_year = $this->input->post('is_baseline_year');
		$is_percent_check = $this->input->post('is_percent_check');
	    }
	    $is_occupancy_check_utility = !empty($this->input->post('is_occupancy_check_utility')) ? $this->input->post('is_occupancy_check_utility') : 0;
	    $is_budget_check_utility = !empty($this->input->post('is_budget_check_utility')) ? $this->input->post('is_budget_check_utility') : 0;
	    $onclickPerformance = !empty($this->input->post('onclickPerformance')) ? $this->input->post('onclickPerformance') : 0;
	    $onclickProgress = !empty($this->input->post('onclickProgress')) ? $this->input->post('onclickProgress') : 0;
	    $onclickUtility = !empty($this->input->post('onclickUtility')) ? $this->input->post('onclickUtility') : 0;
	}

	// Build filters array
	$startdateexplode = explode('/', $startdate);
	$enddateexplode = explode('/', $enddate);
	$filters = [
	    'startdate' => $startdate,
	    'enddate' => $enddate,
	    'utility_type' => $this->prepareUtilitiyType($utility_name),
	    'start_month' => (int) $startdateexplode[0],
	    'start_year' => $startdateexplode[1],
	    'end_month' => (int) $enddateexplode[0],
	    'end_year' => $enddateexplode[1],
	    'previous_month' => $current_month,
	    'previous_year' => $current_year,
	    'CURRENT_YEAR_MAX_MONTH_ID' => ($utility_chart_year != $running_year) ? 12 : CURRENT_YEAR_MAX_MONTH_ID,
	    'Progress_previous_year' => $previous_year,
	    'current_year' => $current_year
	];

	// Monthly utility report data
	$results = $this->reports_model->monthlyUtilityBasedReportByUnit($filters);
	$reportData = [];
	foreach ($results as $result) {
	    if (!isset($reportData[$result['month_id']][$result['year_id']])) {
		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
		$result['occupancy'] = (isset($result['total_room_night']) && $result['total_room_night'] && $site_detials['rooms_keys'] != 0) 
		    ? (($result['total_room_night'] / ($site_detials['rooms_keys'] * $days_of_month)) * 100) : 0;
		$reportData[$result['month_id']][$result['year_id']] = $result;
	    }
	}

	// Progress on target data
	$filters['baseline_year'] = $baselineYear;
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

	// Widget data
	$widgetData = $this->sites_model->getMySitesWidgetData($site_detials);
	$currentMonthData = $widgetData[0] ?? [];
	$lastMonthData = $widgetData[1] ?? [];
	$sameMonthLastYearData = $widgetData[2] ?? [];

	$carbonData = [
	    'carbon_footprint_currentMonth' => $currentMonthData['carbon_footprint'] ?? 0,
	    'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYearData['carbon_footprint'] ?? 0,
	    'ytd_carbon_footprint_new' => $widgetData['ytd_carbon_footprint_new'] ?? 0,
	    'ytd_carbon_footprintPreviousYear' => $widgetData['ytd_carbon_footprintPreviousYear'] ?? 0,
	    'ytd_carbon_footprint_baseline_new' => $widgetData['ytd_carbon_footprint_baseline_new'] ?? 0
	];

	$progressOnTargetResult = calculateProgressOnTarget(
	    $progressOnTarget,
	    $current_month,
	    $current_year,
	    $site_detials,
	    $carbonData,
	    $progressValueWasteYTD
	);

	// Progress Chart data
	$groupUtilityChartDataArray = array();
	$groupUtilityChartData = $this->reports_model->groupUtilityChart();

	$filterTargetMonthly['year_id'] = isset($site_detials['baseline_regression_year']) ? $site_detials['baseline_regression_year'] : date('Y') - 1;

	if ($progress_chart_unit == 'per_rn') {
	    $referenceYearEnergy = $this->reports_model->fetchReferenceYearEnergyTarget($filterTargetMonthly, $isRoomNight = true);
	} else if ($progress_chart_unit == 'per_meter') {
	    $referenceYearEnergy = $this->reports_model->fetchReferenceYearEnergyTarget($filterTargetMonthly);
	}

	if (!empty($groupUtilityChartData)) {
	    foreach ($groupUtilityChartData as $key => $value) {
		if (isset($groupUtilityChartDataArray[$value['month_id']][$value['year_id']]) && !empty($groupUtilityChartDataArray[$value['month_id']][$value['year_id']])) {
		    continue;
		} else {
		    if ($progress_chart_unit == 'per_meter') {

				$groupChartData['electricity'] =
					(!empty($value['electricity']) && !empty($value['cooled_builtup_area']))
					? round(($value['electricity'] / $value['cooled_builtup_area']), 2)
					: 0;

				$groupChartData['gases'] =
					(!empty($value['gases']) && !empty($value['cooled_builtup_area']))
					? round(($value['gases'] / $value['cooled_builtup_area']), 2)
					: 0;

				$groupChartData['others'] =
					(!empty($value['others']) && !empty($value['cooled_builtup_area']))
					? round(($value['others'] / $value['cooled_builtup_area']), 2)
					: 0;

			} else if ($progress_chart_unit == 'per_rn') {

				$groupChartData['electricity'] =
					(!empty($value['electricity']) && !empty($value['room_night']))
					? round(($value['electricity'] / $value['room_night']), 2)
					: 0;

				$groupChartData['gases'] =
					(!empty($value['gases']) && !empty($value['room_night']))
					? round(($value['gases'] / $value['room_night']), 2)
					: 0;

				$groupChartData['others'] =
					(!empty($value['others']) && !empty($value['room_night']))
					? round(($value['others'] / $value['room_night']), 2)
					: 0;
			}

			$groupChartData['month_id'] = $value['month_id'];
			$groupChartData['year_id'] = $value['year_id'];

			$referencePercentage =
				($referenceYearEnergy[$value['month_id']]['energyConverted']
				* $site_detials['energy_intensity_annual_target']) / 100;

			$groupChartData['target'] = (!empty($value['room_night'])) ? $referenceYearEnergy[$value['month_id']]['energyConverted'] - $referencePercentage : 0;
		}
		$groupUtilityChartDataArray[$value['month_id']][$value['year_id']] = $groupChartData;
	    }
	}

	// Progress report data
	$progressReportData = $performanceReportData = array();
	if ($progress_chart_utility == 'energy' && $progress_chart_year == 'eui_by_energy_composition') {
	    if (date('n') == 1) {
		$filters['progress_year'] = date('Y') - 2;
	    } else {
		$filters['progress_year'] = date('Y') - 1;
	    }
	} else {
	    if (is_integer($progress_chart_year)) {
		if (date('n') == 1) {
		    $progress_chart_year = date('Y') - 2;
		}
	    }
	    $filters['progress_year'] = (isset($progress_chart_year) && $progress_chart_year != 'industry_benchmark') ? $progress_chart_year : end(explode('-', $progress_chart_year));
	}

	$progressResults = $this->reports_model->monthlyUtilityProgress($filters);
	$filtersTargetLine['progress_year'] = (int)$site_detials['baseline_regression_year'];
	$progressSeriesBudgetResults = $this->reports_model->monthlyUtilityProgress($filtersTargetLine);
	if ($filters['previous_month'] == 0) {
	    $filtersTargetLine['progress_year'] = (int)date('Y') - 2;
	} else {
	    $filtersTargetLine['progress_year'] = (int)date('Y') - 1;
	}
	$progressSeriesBudgetPreviousYearResults = $this->reports_model->monthlyUtilityProgress($filtersTargetLine);
	$budgetReportData = $budgetReportPreviousYearData = [];
	$siteArrayForPreviousYearBudgetLine = [63,118,82,41,88,114,66,90,61,101,105,107,109,120,129];

	foreach ($progressSeriesBudgetPreviousYearResults as $value) {
	    $budgetReportPreviousYearData[$value['month_id']][$value['year_id']] = $value;
	}
	foreach ($progressSeriesBudgetResults as $value) {
	    $budgetReportData[$value['month_id']][$value['year_id']] = $value;
	}

	if (!empty($progressResults)) {
	    foreach ($progressResults as $result) {
		if (isset($progressReportData[$result['month_id']][$result['year_id']]) && !empty($progressReportData[$result['month_id']][$result['year_id']])) {
		    continue;
		} else {
		    if (in_array($result['site_id'], $siteArrayForPreviousYearBudgetLine)) {
			if ($filters['previous_month'] == 0) {
			    $budgetIndex = $budgetReportPreviousYearData[$result['month_id']][date('Y') - 2];
			} else {
			    $budgetIndex = $budgetReportPreviousYearData[$result['month_id']][date('Y') - 1];
			}
		    } else {
			$budgetIndex = $budgetReportData[$result['month_id']][$site_detials['baseline_regression_year']];
		    }

		    if (substr($progress_chart_year, 0, 17) == "on_site_renewable") {
			$result['energy_target'] = (isset($result['energy']) && $result['energy'] != 0 && $result['room_night'] != 0) ? $result['energy'] : 0;
			$result['water_target'] = (isset($result['water']) && $result['water'] != 0 && $result['room_night'] != 0) ? $result['water'] : 0;
			if ($progress_chart_unit == 'per_rn') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['room_night']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['room_night']), 2) : 0;
			} else if ($progress_chart_unit == 'per_meter') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['cooled_builtup_area']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['cooled_builtup_area']), 2) : 0;
			} else if ($progress_chart_unit == 'per_gn') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['guest_night']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['guest_night']), 2) : 0;
			}
			$result['energy'] = (isset($result['energy']) && $result['energy'] != 0) ? round(calculatePercentage($result['onsite_energy_generator_quantity'], $result['energy']), 2) : 0;
			$result['water'] = (isset($result['water']) && $result['water'] != 0) ? round(calculatePercentage($result['onsite_energy_generator_quantity'], $result['water']), 2) : 0;
		    } else {
			$result['energy_target'] = (isset($result['energy']) && $result['energy'] != 0 && $result['room_night'] != 0) ? $result['energy'] : 0;
			$result['water_target'] = (isset($result['water']) && $result['water'] != 0 && $result['room_night'] != 0) ? $result['water'] : 0;
			if ($progress_chart_unit == 'per_rn') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['room_night']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['room_night']), 2) : 0;
			    $result['energy'] = (isset($result['room_night']) && $result['room_night'] != 0) ? round(($result['energy'] / $result['room_night']), 2) : 0;
			    $result['water'] = (isset($result['room_night']) && $result['room_night'] != 0) ? round(($result['water'] / $result['room_night']), 2) : 0;
			} else if ($progress_chart_unit == 'per_meter') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['cooled_builtup_area']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['cooled_builtup_area']), 2) : 0;
			    $result['energy'] = (isset($result['cooled_builtup_area']) && $result['cooled_builtup_area'] != 0) ? round(($result['energy'] / $result['cooled_builtup_area']), 2) : 0;
			    $result['water'] = (isset($result['cooled_builtup_area']) && $result['cooled_builtup_area'] != 0) ? round(($result['water'] / $result['cooled_builtup_area']), 2) : 0;
			} else if ($progress_chart_unit == 'per_gn') {
			    $result['energy_budget'] = (isset($budgetIndex['energy']) && $budgetIndex['energy'] != 0) ? round(($budgetIndex['energy'] / $result['guest_night']), 2) : 0;
			    $result['water_budget'] = (isset($budgetIndex['water']) && $budgetIndex['water'] != 0) ? round(($budgetIndex['water'] / $result['guest_night']), 2) : 0;
			    $result['energy'] = (isset($result['guest_night']) && $result['guest_night'] != 0) ? round(($result['energy'] / $result['guest_night']), 2) : 0;
			    $result['water'] = (isset($result['guest_night']) && $result['guest_night'] != 0) ? round(($result['water'] / $result['guest_night']), 2) : 0;
			}
		    }

		    if ($result['month_id'] <= date('m')) {
			$progressReportData[$result['month_id']][$result['year_id']] = $result;
		    } else if (date('m') == 1) {
			$progressReportData[$result['month_id']][$result['year_id']] = $result;
		    } else {
			$result['site_builtup_area'] = 0;
			$result['cooled_builtup_area'] = 0;
			$result['room_night'] = 0;
			$result['energy'] = 0;
			$result['water'] = 0;
			$result['onsite_energy_generator_quantity'] = 0;
			$progressReportData[$result['month_id']][$result['year_id']] = $result;
		    }
		}
	    }
	}

	// Performance Chart data
	$filters['performance_chart_type'] = (isset($performance_chart_type) ? $performance_chart_type : $performance_chart_type = '');
	$filters['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$filters['is_baseline_year'] = $is_baseline_year;
	$performanceReportData = $this->reports_model->getPerformanceChartData($filters);

	// Utility model data
	$this->utilities_model->site_id = $site_id;
	$lastUpdatedMonthUtility = $this->utilities_model->getLastMonthForSite($site_id);
	$lastUpdatedMonthWaste = $this->site_waste_model->getLastMonthForSite($site_id);
	$notifications = $this->utilities_model->getNotifications();
	$availableSiteUtilities = $this->prepareUtilitiesOfSite($site_detials);

	// Variation calculations (using local variables first)
	$variation = ($currentMonthData['total_utility_cost'] ?? 0) - ($currentMonthData['total_budgeted_cost'] ?? 0);
	$variationPercentage = ($currentMonthData['total_budgeted_cost'] != 0) 
	    ? ($variation * 100) / $currentMonthData['total_budgeted_cost'] : 0;
	$variation_ytd = ($currentMonthData['total_utility_cost_ytd'] ?? 0) - ($currentMonthData['total_budgeted_cost_ytd'] ?? 0);
	$variationPercentage_ytd = ($currentMonthData['total_budgeted_cost_ytd'] != 0) 
	    ? ($variation_ytd * 100) / $currentMonthData['total_budgeted_cost_ytd'] : 0;

	// Date labels
	$lastyear = ($running_month == 1) ? $running_year - 2 : $running_year - 1;
	$currentmonth = date('F', strtotime($running_year . '-' . $running_month . ' -1 months'));
	$lastmonth = date('F', strtotime($running_year . '-' . $running_month . ' -1 months'));
	$currentyear = ($running_month == 1) ? $running_year - 1 : $running_year;

	// Utility cost & consumption calculations
	$utility_cost_calculation = [];
	$utilityConfig = [
	    'electricity' => ['show' => 'show_utility_electricity', 'cost_key' => 'electricity_cost_raw', 'consumption_key' => 'electricity_raw', 'title' => 'Electricity'],
	    'fuel_oil' => ['show' => 'show_utility_fuel_oil', 'cost_key' => 'fuel_cost_raw', 'consumption_key' => 'fuel_raw', 'title' => 'Fuel Oil'],
	    'lpg' => ['show' => 'show_utility_lpg', 'cost_key' => 'lpg_cost_raw', 'consumption_key' => 'lpg_raw', 'title' => 'LPG'],
	    'natural_gas' => ['show' => 'show_utility_natural_gas', 'cost_key' => 'natural_gas_cost_raw', 'consumption_key' => 'natural_gas_raw', 'title' => 'Natural Gas'],
	    'district_heating' => ['show' => 'show_utility_district_heating', 'cost_key' => 'heating_cost_raw', 'consumption_key' => 'heating_raw', 'title' => 'District Heating'],
	    'district_cooling' => ['show' => 'show_utility_district_cooling', 'cost_key' => 'cooling_cost_raw', 'consumption_key' => 'cooling_raw', 'title' => 'District Cooling'],
	    'water' => ['show' => 'show_utility_water', 'cost_key' => 'water_cost_raw', 'consumption_key' => 'water_raw', 'title' => 'Water']
	];

	foreach ($utilityConfig as $utilityKey => $config) {
	    if (!empty($site_detials[$config['show']])) {
			$lastCost = floatval($currentMonthData[$config['cost_key']] ?? 0);
			$prevCost = floatval($sameMonthLastYearData[$config['cost_key']] ?? 0);
			$lastConsumption = floatval($currentMonthData[$config['consumption_key']] ?? 0);
			$prevConsumption = floatval($sameMonthLastYearData[$config['consumption_key']] ?? 0);

			$utility_cost_calculation[$utilityKey] = [
				'cost' => ($prevCost != 0) ? (($lastCost - $prevCost) * 100 / $prevCost) : 0,
				'consumption' => ($prevConsumption != 0) ? (($lastConsumption - $prevConsumption) * 100 / $prevConsumption) : 0,
				'title' => $config['title'],
				'cost_image' => ($lastCost > $prevCost) ? 'upArrow.png' : 'downArrow.png',
				'consumption_image' => ($lastConsumption > $prevConsumption) ? 'upArrow.png' : 'downArrow.png'
			];
	    }
	}

	// CDD, HDD, Room Nights calculations
	$utility_cost_calculation_chr = [];
	$chrConfig = [
	    'cdd' => ['key' => 'cdd', 'title' => 'CDD', 'up' => 'upArrow.png', 'down' => 'downArrow.png'],
	    'hdd' => ['key' => 'hdd', 'title' => 'HDD', 'up' => 'upArrow.png', 'down' => 'downArrow.png'],
	    'room_nights' => ['key' => 'total_room_night', 'title' => 'Room Nights', 'up' => 'upArrowGreen.png', 'down' => 'downArrowRed.png']
	];

	foreach ($chrConfig as $chrKey => $config) {
	    $lastVal = floatval($currentMonthData[$config['key']] ?? 0);
	    $prevVal = floatval($sameMonthLastYearData[$config['key']] ?? 0);

	    $utility_cost_calculation_chr[$chrKey] = [
		'consumption' => ($prevVal != 0) ? (($lastVal - $prevVal) * 100 / $prevVal) : 0,
		'title' => $config['title'],
		'consumption_image' => ($lastVal > $prevVal) ? $config['up'] : $config['down']
	    ];
	}

	// Notification filters and data
	$filters_notification = array();
	$filters_notification['previousmonth'] = date("F-Y", strtotime(date('Y-m') . " -13 months"));
	$filters_notification['currentmonth'] = date("F-Y", strtotime(date('Y-m') . " -1 months"));
	$filters_notification['pmonth'] = (int) date("m", strtotime(date('Y-m') . " -13 months"));
	$filters_notification['pyear'] = date("Y", strtotime(date('Y-m') . " -13 months"));
	$filters_notification['cmonth'] = (int) date("m", strtotime(date('Y-m') . " -1 months"));
	$filters_notification['cyear'] = date("Y", strtotime(date('Y-m') . " -1 months"));
	$this->dashboard_model->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;

	// Utility comparison data
	$utilityForLastMonthCompare_result = $this->dashboard_model->getUtilityComparisionForLastMonth($filters_notification);
	$utilityForLastMonthCompare = array();
	foreach ($utilityForLastMonthCompare_result as $value) {
	    $value = array_map('intval', $value);
	    $value['total_utility'] = ($value['electricity'] + $value['fuel'] + $value['lpg'] + $value['natural_gas'] + $value['heating_district'] + $value['district_heating_fixed_cost'] + $value['cooling_district'] + $value['district_cooling_fixed_cost'] + $value['lpg_fixed_cost'] + $value['natural_gas_fixed_cost'] + $value['water_fixed_cost'] + $value['water']);
	    $utilityForLastMonthCompare[$value['year_id']][$value['month_id']] = $value;
	}

	// Cost pie chart data
	$cost_pie_chart_previousmonth = [
	    'electricity' => floatval($currentMonthData['electricity_cost_raw'] ?? 0),
	    'fuel' => floatval($currentMonthData['fuel_cost_raw'] ?? 0),
	    'lpg' => floatval($currentMonthData['lpg_cost_raw'] ?? 0) + floatval($currentMonthData['lpg_fixed_cost_raw'] ?? 0),
	    'natural_gas' => floatval($currentMonthData['natural_gas_cost_raw'] ?? 0) + floatval($currentMonthData['natural_gas_fixed_cost_raw'] ?? 0),
	    'heating_district' => floatval($currentMonthData['heating_cost_raw'] ?? 0) + floatval($currentMonthData['heating_fixed_cost_raw'] ?? 0),
	    'cooling_district' => floatval($currentMonthData['cooling_cost_raw'] ?? 0) + floatval($currentMonthData['cooling_fixed_cost_raw'] ?? 0),
	    'water' => floatval($currentMonthData['water_cost_raw'] ?? 0) + floatval($currentMonthData['water_fixed_cost_raw'] ?? 0)
	];

	// Custom notification data
	$startmonth = intval(date('m') - 1);
	$startmonth = ($startmonth == 0) ? 12 : $startmonth;
	$site_cuatom_notification_filter = array(
	    'year' => date('Y'),
	    'start_month' => $startmonth,
	    'end_month' => intval(date('m'))
	);
	$sitescustomnotification = $this->dashboard_model->getSiteCustomNotifications($site_cuatom_notification_filter);

	// ========================================
	// Assign all data to view array
	// ========================================
	$data = [];
	
	// Date and time data
	$data['current_month'] = $current_month;
	$data['current_year'] = $current_year;
	$data['lastyear'] = $lastyear;
	$data['currentmonth'] = $currentmonth;
	$data['lastmonth'] = $lastmonth;
	$data['currentyear'] = $currentyear;

	// Progress target data
	$data['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
	$data['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
	$data['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
	$data['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
	$data['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];

	// Chart configuration data
	$data['utility_chart_year'] = $utility_chart_year;
	$data['progress_chart_year'] = $progress_chart_year;
	$data['progress_chart_utility'] = $progress_chart_utility;
	$data['progress_chart_unit'] = $progress_chart_unit;
	$data['performance_chart_type'] = $performance_chart_type;

	// Chart report data
	$data['reportdata'] = $reportData;
	$data['progressReportData'] = $progressReportData;
	$data['performanceReportData'] = $performanceReportData;
	$data['groupUtilityChartDataArray'] = $groupUtilityChartDataArray;

	// Checkbox states
	$data['is_occupancy_check_utility'] = $is_occupancy_check_utility;
	$data['is_budget_check_utility'] = $is_budget_check_utility;
	$data['is_occupancy_check'] = $is_occupancy_check;
	$data['is_baseline_year'] = $is_baseline_year;
	$data['is_percent_check'] = $is_percent_check;

	// Click states
	$data['onclickPerformance'] = $onclickPerformance;
	$data['onclickProgress'] = $onclickProgress;
	$data['onclickUtility'] = $onclickUtility;

	// Filters
	$data['filters'] = $filters;
	$data['filters_notification'] = $filters_notification;

	// Last updated data
	$data['lastUpdatedMonthUtility'] = $lastUpdatedMonthUtility;
	$data['lastUpdatedMonthWaste'] = $lastUpdatedMonthWaste;

	// Notifications
	$data['notifications'] = $notifications;
	$data['sitescustomnotification'] = $sitescustomnotification;

	// Utility cost data
	$data['total_utility_cost_currentMonth'] = $currentMonthData['total_utility_cost'] ?? 0;
	$data['total_utility_cost_lastMonth'] = $lastMonthData['total_utility_cost'] ?? 0;
	$data['total_utility_cost_sameMonth_lastYear'] = $sameMonthLastYearData['total_utility_cost'] ?? 0;

	// Variation data
	$data['variation'] = $variation;
	$data['variationPercentage'] = $variationPercentage;
	$data['variation_ytd'] = $variation_ytd;
	$data['variationPercentage_ytd'] = $variationPercentage_ytd;

	// Utility calculations
	$data['utility_cost_calculation'] = $utility_cost_calculation;
	$data['utility_cost_calculation_chr'] = $utility_cost_calculation_chr;
	$data['utilityForLastMonthCompare'] = $utilityForLastMonthCompare;
	$data['cost_pie_chart_previousmonth'] = $cost_pie_chart_previousmonth;

	// Site data
	$data['site_detials'] = $site_detials;
	$data['site_id'] = $site_id;
	$data['mUtilities'] = $availableSiteUtilities;
	$data['selected_utility'] = $utility_name;

	$__ajax_partial = $this->input->post('dashboard_ajax_partial');
	if ($__ajax_partial && in_array($__ajax_partial, array('progress', 'performance', 'utility'), true)
	    && $this->input->is_ajax_request()) {
	    $data['dashboard_ajax_partial'] = $__ajax_partial;
	    $this->output->set_content_type('text/html; charset=UTF-8');
	    $this->load->view('admin_index', $data);
	    return;
	}

	$this->theme->view($data);
    }

    function sites($language_code = '')
    {
	$data = [];
	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;

	$this->theme->set('page_title', 'Dashboard');
	$this->load->model('projects/projects_model');
	$this->load->model('users/users_model');
	$this->load->model('sites/sites_model');
	$this->load->model('utilities/utilities_model');
	$this->load->model('reports/reports_model');
	$this->load->model('sites/site_waste_model');

	$region_list = $this->sites_model->region_list();
	$all_region_list = $this->sites_model->all_region_list();
	$data['region_list'] = $region_list;

	$region_id = $all_region_list[0]['id'];
	if ($this->input->post()) {
	    $region_id = $this->input->post('region_id');
	} else {
	    $siteDetail = $this->dashboard_model->getUserSiteRegion($user_id);
	    if (isset($siteDetail) && !empty($siteDetail)) {
		$region_id = isset($siteDetail['region_id']) ? $siteDetail['region_id'] : 1;
	    }
	}

	$data['region_id'] = $region_id;
	$data['role_id'] = $role_id;

	$dateParams = getProgressWidgetDateParams();
	$current_month = $dateParams['month'];
	$current_year = $dateParams['year'];
	$running_year = $dateParams['running_year'];

	$site_ids = [];
	$site_results = $this->users_model->get_site_to_user_with_region($user_id, $region_id, $role_id);
	foreach ($site_results as $result) {
	    $site_ids[] = $result['site_id'];
	}

	if (!empty($site_ids) || $role_id == 1) {
	    $site_filters = [
		'order_by' => 'site_location_name',
		'order' => 'asc',
		'region_id' => $region_id
	    ];
	    $sites = $this->sites_model->get_site_detail_multiple($site_ids, $site_filters);

	    foreach ($sites as $site_detials) {
		$site_id = $site_detials['id'];
		$baselineYear = $site_detials['baseline_regression_year'];
		$data['sites'][$site_id] = $site_detials;

		$this->reports_model->site_id = $site_id;
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

		$data['sites'][$site_id]['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
		$data['sites'][$site_id]['progressOnTarget'] = $progressOnTarget;

		$widgetData = $this->sites_model->getMySitesWidgetData($site_detials);
		$currentMonth = $widgetData[0] ?? [];
		$lastMonth = $widgetData[1] ?? [];
		$sameMonthLastYear = $widgetData[2] ?? [];

		$carbonData = [
		    'carbon_footprint_currentMonth' => $currentMonth['carbon_footprint'] ?? 0,
		    'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYear['carbon_footprint'] ?? 0,
		    'ytd_carbon_footprint_new' => $widgetData['ytd_carbon_footprint_new'] ?? 0,
		    'ytd_carbon_footprintPreviousYear' => $widgetData['ytd_carbon_footprintPreviousYear'] ?? 0,
		    'ytd_carbon_footprint_baseline_new' => $widgetData['ytd_carbon_footprint_baseline_new'] ?? 0
		];

		$progressOnTargetResult = calculateProgressOnTarget(
		    $progressOnTarget,
		    $current_month,
		    $current_year,
		    $site_detials,
		    $carbonData,
		    $progressValueWasteYTD
		);

		$data['sites'][$site_id]['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
		$data['sites'][$site_id]['progressTarget'] = $progressOnTargetResult['progressTarget'] ?? [];
		$data['sites'][$site_id]['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
		$data['sites'][$site_id]['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
		$data['sites'][$site_id]['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
		$data['sites'][$site_id]['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];

		$data['sites'][$site_id]['carbon_footprint_currentMonth'] = $currentMonth['carbon_footprint'] ?? 0;
		$data['sites'][$site_id]['total_utility_cost_currentMonth'] = $currentMonth['total_utility_cost'] ?? 0;
		$data['sites'][$site_id]['carbon_footprint_SameMonthPreviousYear'] = $sameMonthLastYear['carbon_footprint'] ?? 0;
		$data['sites'][$site_id]['variation'] = ($currentMonth['total_budgeted_cost'] ?? 0) - ($currentMonth['total_utility_cost'] ?? 0);
		$data['sites'][$site_id]['variationPercentage'] = ($currentMonth['total_utility_cost'] != 0) 
		    ? (($currentMonth['total_budgeted_cost'] - $currentMonth['total_utility_cost']) * 100 / $currentMonth['total_utility_cost']) : 0;
		$data['sites'][$site_id]['variation_ytd'] = ($currentMonth['total_budgeted_cost_ytd'] ?? 0) - ($currentMonth['total_utility_cost_ytd'] ?? 0);
		$data['sites'][$site_id]['variationPercentage_ytd'] = ($currentMonth['total_utility_cost_ytd'] != 0) 
		    ? (($currentMonth['total_budgeted_cost_ytd'] - $currentMonth['total_utility_cost_ytd']) * 100 / $currentMonth['total_utility_cost_ytd']) : 0;
		$data['sites'][$site_id]['ytd_carbon_footprint'] = $widgetData['ytd_carbon_footprint'] ?? 0;
		$data['sites'][$site_id]['ytd_carbon_footprintPreviousYear'] = $widgetData['ytd_carbon_footprintPreviousYear'] ?? 0;
		$data['sites'][$site_id]['ytd_carbon_footprint_new'] = $widgetData['ytd_carbon_footprint_new'] ?? 0;
		$data['sites'][$site_id]['ytd_carbon_footprint_baseline_new'] = $widgetData['ytd_carbon_footprint_baseline_new'] ?? 0;
		$data['sites'][$site_id]['total_utility_cost_lastMonth'] = $lastMonth['total_utility_cost'] ?? 0;
		$data['sites'][$site_id]['total_utility_cost_sameMonth_lastYear'] = $sameMonthLastYear['total_utility_cost'] ?? 0;
		$data['sites'][$site_id]['currentMonth_cost_roomNight'] = $currentMonth['cost_roomNight'] ?? 0;
		$data['sites'][$site_id]['lastMonth_cost_roomNight'] = $lastMonth['cost_roomNight'] ?? 0;
		$data['sites'][$site_id]['sameMonth_lastYear_cost_roomNight'] = $sameMonthLastYear['cost_roomNight'] ?? 0;
	    }
	}
	$this->theme->view($data);
    }

    /*
    * Function prepareUtilitiesOfSite for generate dropdown of Utilities of Site
    */

    function prepareUtilitiesOfSite($siteDetails = array()){
	$utilityArray = array();
	if($siteDetails['show_utility_electricity']){
	    $utilityArray['electricity'] = 'Electricity';
	}
	if($siteDetails['show_utility_fuel_oil']){
	    $utilityArray['oil'] = 'Fuel Oil';
	}
	if($siteDetails['show_utility_lpg']){
	    $utilityArray['lpg'] = 'LPG';
	}
	if($siteDetails['show_utility_water']){
	    $utilityArray['water'] = 'Water';
	}
	if($siteDetails['show_utility_natural_gas']){
	    $utilityArray['natural_gas'] = 'Natural Gas';
	}
	if($siteDetails['show_utility_district_cooling']){
	    $utilityArray['district_cooling'] = 'District Cooling';
	}
	if($siteDetails['show_utility_district_heating']){
	    $utilityArray['district_heating'] = 'District Heating';
	}

	return $utilityArray;
    }

    /*
    * Function prepareUtilitiyType for generate name of utility type
    */

    function prepareUtilitiyType($name = ''){
	$mName = '';
	switch ($name) {
	    case 'Electricity':
		$mName = 'electricity';
		break;
	    case 'Fuel Oil':
		$mName = 'fuel_oil';
		break;
	    case 'LPG':
		$mName = 'lpg';
		break;
	    case 'Water':
		$mName = 'water';
		break;
	    case 'District Cooling':
		$mName = 'district_cooling';
		break;
	    case 'District Heating':
		$mName = 'district_heating';
		break;
	    case 'Natural Gas':
		$mName = 'natural_gas';
		break;
	    default:
		$mName = 'electricity';
		break;
	}

	return $mName;
    }
}
