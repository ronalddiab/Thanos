<?php

set_time_limit(0);
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 *  CMS Admin Controller
 *
 *  CMS Admin controller to display add / Edit / Delete / List Import Utilities page.
 *
 * @package CIDemoApplication
 *
 * @copyright   (c) 2013, TatvaSoft
 * @author NDP
 */
class Import_admin extends Base_Admin_Controller
{
    /*
     * Create an instance
     */

    public function __construct()
    {
	parent::__construct();
	// Login check for admin
	$this->access_control($this->access_rules());
	$this->load->library('form_validation');
	// Load required helpers
	$this->load->helper('url');

	// load required models
	$this->load->model('import/import_model');
	$this->load->model('sites/sites_model');
	$this->load->model('urls/urls_model');
	$this->load->model('survey/Survey_model');
	$this->load->model('survey/Survey_questions_answer_model');

	// Breadcrumb settings
	$this->breadcrumb->add(lang('import'), base_url() . 'import');
    }

    /**
     * function accessRules to check page access
     */
    private function access_rules()
    {
	return array(
	    array(
		'actions' => array('index', 'export', 'daily', 'export_monthly_data', 'waste', 'emission','site_data','survey','checkNegative','compareUtility'),
		'users' => array('@'),
	    ),
	);
    }

    public function index()
    {
	ini_set('memory_limit', '-1');
	$data = array();
	$success = '0';
	$sites_name = array();

	if (empty($this->input->post())) {
		$this->theme->set('page_title', lang('import'));
		$this->theme->view($data);
		return;
	}

	$decimal_places = 2;
	require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';

	$file_tmp = $_FILES['importfile']['tmp_name'];
	$file_name = $_FILES['importfile']['name'];
	$fileType = pathinfo($file_name, PATHINFO_EXTENSION);

	if ($fileType == "") {
		$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
		exit;
	}
	if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
		$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
		exit;
	}

	$cdd_hdd_values = $this->import_model->getMonthlyCddHddValues();

		// For notifications
		$fieldNamesArray = array(
		    'electricity_tariff',
		    'total_onsite_generators_cost',
		    'total_renewable_energy_production',
		    'total_renewable_energy_production_cost',
			'total_renewable_energy_exported',
		    'total_renewable_energy_exported_cost',
		    'fuel_oil_hot_water_boilers_cost',
		    'fuel_oil_steam_boilers_cost',
		    'fuel_oil_others_cost',
		    'lpg_hot_water_boilers_cost',
		    'lpg_steam_boilers_cost',
		    'lpg_kitchen_cost',
		    'natural_gas_hot_water_boilers_cost',
		    'natural_gas_steam_boilers_cost',
		    'natural_gas_kitchen_cost',
		    'district_heating_cost',
		    'district_cooling_cost',
		    'water_utility_supply_cost',
		    'waste_water_cost',
		    'water_Cisterns_cost',
		    'water_ro_cost',
		    'total_room_night',
		    'total_guests',
		    'total_room_night_budget',
		    'total_guests_budget',
		    'total_laundered',
		    'total_fb_services',
		);

	$this->load->model('utilities/utilities_model');
	$this->load->model('sites/site_residence_model');

	$target_file = BASE_PATH_CUSTOM . "/assets/uploads/upload_file." . $fileType;
	$_movestatus = move_uploaded_file($file_tmp, $target_file);

	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';

	if ($_movestatus) {
		$inputFileType = PHPExcel_IOFactory::identify($target_file);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setLoadAllSheets();
		$objPHPExcel = $objReader->load($target_file);
		$worksheetData = $objReader->listWorksheetInfo($target_file);
		$numberRow = $worksheetData[0]['totalRows'];
		$numberCol = $worksheetData[0]['totalColumns'];
		$dataCells = $objPHPExcel->getSheet(0)->toArray();
		$titleCells = $dataCells[0];

		if (in_array('Day', $titleCells)) {
			$this->theme->set_message("Please upload monthly import file.", 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
			exit;
		}

		$currentSiteName = array(isset($dataCells[1][0]) ? $dataCells[1][0] : 0);
		$currentSiteData = $this->import_model->getSiteDetailByName($currentSiteName);
		$site_detail = isset($currentSiteData[$dataCells[1][0]]) ? $currentSiteData[$dataCells[1][0]] : array();
		$site_id = !empty($site_detail['id'])
			? $site_detail['id']
			: $this->session->userdata[$this->section_name]['site_id'];

		$electricity_unit = GetSiteUtilityUnitName($site_id, 'electricity');
		$fuel_oil_unit = GetSiteUtilityUnitName($site_id, 'fuel_oil');
		$lpg_unit = GetSiteUtilityUnitName($site_id, 'lpg');
		$water_unit = GetSiteUtilityUnitName($site_id, 'water');
		$natural_gas_unit = GetSiteUtilityUnitName($site_id, 'natural_gas');
		$district_cooling_unit = GetSiteUtilityUnitName($site_id, 'district_cooling');
		$district_heating_unit = GetSiteUtilityUnitName($site_id, 'district_heating');

		/* Excel label => DB column mapping */
		$colmuns = array();
		    $colmuns['Site Name'] = "site_id";
		    $colmuns['Purchased Electricity '.$electricity_unit.''] = "total_kwh";
		    $colmuns['Purchased Electricity Tariff ($/'.$electricity_unit.')'] = "tariff";
		    $colmuns['Purchased Electricity Cost ($)'] = "total_cost";
		    $colmuns['Month'] = "month_id";
		    $colmuns['Year'] = "year_id";
		    $colmuns['Maximum Demand KVA/KW'] = "maximum_demand";
		    $colmuns['Maximum Demand Tariff ($/KVA || $/KW)'] = "maximum_demand_price";
		    $colmuns['Maximum Demand Cost ($)'] = "total_maximum_demand";
		    $colmuns['Fixed Fees Cost ($)'] = "fixed_fees";
		    $colmuns['Average PF'] = "average_pf";
		    $colmuns['Total Renewable Energy Production '.$electricity_unit.''] = "total_renewable_energy_production";
		    $colmuns['Total Renewable Energy Cost ($)'] = "total_renewable_energy_production_cost";
			$colmuns['Total Renewable Energy Exported '.$electricity_unit.''] = "total_renewable_energy_exported";
		    $colmuns['Total Renewable Energy Exported Cost ($)'] = "total_renewable_energy_exported_cost";
		    $colmuns['Onsite Generators '.$electricity_unit.''] = "onsite_generators_quantity";
		    $colmuns['Onsite Generators ($)'] = "total_onsite_generators_cost";
		    $colmuns['Onsite Generators Fuel Oil '.$fuel_oil_unit.''] = "onsite_generators_fuel_oil_quantity";
		    $colmuns['Onsite Generator Fuel Oil $/'.$fuel_oil_unit.''] = "onsite_generators_fuel_oil_price";
		    $colmuns['Onsite Generators Natural Gas '.$natural_gas_unit.''] = "onsite_generators_natural_gas_quantity";
		    $colmuns['Onsite Generators Natural Gas $/'.$natural_gas_unit.''] = "onsite_generators_natural_gas_price";
		    $colmuns['Total Electricity Cost'] = "total_electricity_cost";
		    $colmuns['Total Electricity '.$electricity_unit.''] = "total_electricity_kwh";
		    $colmuns['Electricity Total Budgeted'] = "electricity_total_budget";
		    $colmuns['Electricity Total Budgeted Cost ($)'] = "electricity_total_budget_cost";
		    $colmuns['Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_hot_water_boilers";
		    $colmuns['Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_hot_water_boilers_rate";
		    $colmuns['Fuel Oil Steam Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_steam_boilers";
		    $colmuns['Fuel Oil Steam Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_steam_boilers_rate";
		    $colmuns['Fuel Oil Others ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_others";
		    $colmuns['Fuel Oil Others ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_others_rate";
		    $colmuns['Fuel Oil Total Budgeted'] = "fuel_total_budget";
		    $colmuns['Fuel Oil Total Budgeted Cost ($)'] = "fuel_total_budget_cost";
		    $colmuns['LPG Hot-Water Boilers ('.$lpg_unit.')'] = "lpg_hot_water_boilers";
		    $colmuns['LPG Hot-Water Boilers $/'.$lpg_unit.''] = "lpg_hot_water_boilers_rate";
		    $colmuns['LPG Steam Boilers ('.$lpg_unit.')'] = "lpg_steam_boilers";
		    $colmuns['LPG Steam Boilers $/'.$lpg_unit.''] = "lpg_steam_boilers_rate";
		    $colmuns['LPG Kitchen ('.$lpg_unit.')'] = "lpg_kitchen";
		    $colmuns['LPG Kitchen $/'.$lpg_unit.''] = "lpg_kitchen_rate";
		    $colmuns['LPG Total Budgeted'] = "lpg_total_budget";
		    $colmuns['LPG Total Budgeted Cost ($)'] = "lpg_total_budget_cost";
		    $colmuns['Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_hot_water_boilers";
		    $colmuns['Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_hot_water_boilers_rate";
		    $colmuns['Natural Gas Steam Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_steam_boilers";
		    $colmuns['Natural Gas Steam Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_steam_boilers_rate";
		    $colmuns['Natural Gas Kitchen ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_kitchen";
		    $colmuns['Natural Gas Kitchen ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_kitchen_rate";
		    $colmuns['Natural Gas Total Budgeted'] = "natural_gas_total_budget";
		    $colmuns['Natural Gas Total Budgeted Cost ($)'] = "natural_gas_total_budget_cost";
		    $colmuns['District Energy Heating '.$district_heating_unit.''] = "district_heating";
		    $colmuns['District Energy Heating $/'.$district_heating_unit.''] = "district_heating_rate";
		    $colmuns['Heating District Energy Total Budgeted'] = "district_heating_total_budget";
		    $colmuns['Heating District Energy Total Budgeted Cost ($)'] = "district_heating_total_budget_cost";
		    $colmuns['District Energy Cooling '.$district_cooling_unit.''] = "district_cooling";
		    $colmuns['District Energy Cooling $/'.$district_cooling_unit.''] = "district_cooling_rate";
		    $colmuns['Cooling District Energy Total Budgeted'] = "district_cooling_total_budget";
		    $colmuns['Cooling District Energy Total Budgeted Cost ($)'] = "district_cooling_total_budget_cost";
		    $colmuns['Water Utility Supply ('.$water_unit.') '.$water_unit.''] = "water_utility_supply";
		    $colmuns['Water Utility Supply ('.$water_unit.') $/'.$water_unit.''] = "water_utility_supply_rate";
		    $colmuns['Wastewater ('.$water_unit.') '.$water_unit.''] = "waste_water";
		    $colmuns['Wastewater ('.$water_unit.') $/'.$water_unit.''] = "waste_water_rate";
		    $colmuns['Water Cisterns ('.$water_unit.') '.$water_unit.''] = "water_Cisterns";
		    $colmuns['Water Cisterns ('.$water_unit.') $/'.$water_unit.''] = "water_Cisterns_rate";
		    $colmuns['Water RO ('.$water_unit.') '.$water_unit.''] = "water_ro";
		    $colmuns['Water RO ('.$water_unit.') $/'.$water_unit.''] = "water_ro_rate";
		    $colmuns['Water Total Budgeted'] = "water_total_consumption_budget";
		    $colmuns['Water Total Budgeted Cost ($)'] = "water_total_consumption_budget_cost";
		    $colmuns['Irrigation Water ('.$water_unit.') '.$water_unit.''] = "water_irrigation";
		    $colmuns['Irrigation Water ('.$water_unit.') $/'.$water_unit.''] = "water_irrigation_rate";
		    $colmuns['Room Nights'] = "total_room_night";
		    $colmuns['Room Nights Budget'] = "total_room_night_budget";
		    $colmuns['Total Guests'] = "total_guests";
		    $colmuns['Total Guests Budget'] = "total_guests_budget";
		    $colmuns['Laundry Load'] = "total_laundered";
		    $colmuns['Food Covers'] = "total_fb_services";
		    $colmuns['Revenue'] = "revenue";
		    $colmuns['Vehicle Petrol (Liter)'] = "vehicle_petrol";
		    $colmuns['Total F and B Sales'] = "total_f_b_sales";

		    $colmuns['Heating Fixed Cost'] = "district_heating_fixed_cost";
		    $colmuns['Cooling Fixed Cost'] = "district_cooling_fixed_cost";
		    $colmuns['LPG Fixed Cost'] = "lpg_fixed_cost";
		    $colmuns['Natural Gas Fixed Cost'] = "natural_gas_fixed_cost";
		    $colmuns['Water Fixed Cost'] = "water_fixed_cost";
		    $colmuns['Forex'] = "forex";
		    $colmuns['Cooling Degree Day'] = "cdd";
		    $colmuns['Heating Degree Day'] = "hdd";
		    $colmuns['Employee Quarter Electricity'] = "employee_living_quarter_electricity";
		    $colmuns['Employee Quarter Fuel Oil/Diesel'] = "employee_living_quarter_fuel_oil";
		    $colmuns['Employee Quarter LPG'] = "employee_living_quarter_lpg";
		    $colmuns['Employee Quarter Natural Gas'] = "employee_living_quarter_natural_gas";
		    $colmuns['Employee Quarter District Heating'] = "employee_living_quarter_district_heating";
		    $colmuns['Employee Quarter District Cooling'] = "employee_living_quarter_district_cooling";
		    $colmuns['Employee Quarter Water'] = "employee_living_quarter_water";

		    $colmuns['Employee Quarter Electricity (offsite)'] = "employee_living_quarter_offsite_electricity";
		    $colmuns['Employee Quarter Fuel Oil/Diesel (offsite)'] = "employee_living_quarter_offsite_fuel_oil";
		    $colmuns['Employee Quarter LPG (offsite)'] = "employee_living_quarter_offsite_lpg";
		    $colmuns['Employee Quarter Natural Gas (offsite)'] = "employee_living_quarter_offsite_natural_gas";
		    $colmuns['Employee Quarter District Heating (offsite)'] = "employee_living_quarter_offsite_district_heating";
		    $colmuns['Employee Quarter District Cooling (offsite)'] = "employee_living_quarter_offsite_district_cooling";
		    $colmuns['Employee Quarter Water (offsite)'] = "employee_living_quarter_offsite_water";
		    // Charged by meter values store rental
		    $colmuns['Rental Electricity'] = "rental_program_residence_electricity";
		    $colmuns['Rental Electricity Rate'] = "rental_program_residence_electricity_rate";
		    $colmuns['Rental Electricity Cost'] = "rental_program_residence_electricity_cost";
		    $colmuns['Rental Fuel/Diesel Oil'] = "rental_program_residence_fuel_oil";
		    $colmuns['Rental Fuel/Diesel Oil Rate'] = "rental_program_residence_fuel_oil_rate";
		    $colmuns['Rental Fuel/Diesel Oil Cost'] = "rental_program_residence_fuel_oil_cost";
		    $colmuns['Rental Lpg'] = "rental_program_residence_lpg";
		    $colmuns['Rental Lpg Rate'] = "rental_program_residence_lpg_rate";
		    $colmuns['Rental Lpg Cost'] = "rental_program_residence_lpg_cost";
		    $colmuns['Rental Natural Gas'] = "rental_program_residence_natural_gas";
		    $colmuns['Rental Natural Gas Rate'] = "rental_program_residence_natural_gas_rate";
		    $colmuns['Rental Natural Gas Cost'] = "rental_program_residence_natural_gas_cost";
		    $colmuns['Rental District Cooling'] = "rental_program_residence_district_cooling";
		    $colmuns['Rental District Cooling Rate'] = "rental_program_residence_district_cooling_rate";
		    $colmuns['Rental District Cooling Cost'] = "rental_program_residence_district_cooling_cost";
		    $colmuns['Rental District Heating'] = "rental_program_residence_district_heating";
		    $colmuns['Rental District Heating Rate'] = "rental_program_residence_district_heating_rate";
		    $colmuns['Rental District Heating Cost'] = "rental_program_residence_district_heating_cost";
		    $colmuns['Rental Water'] = "rental_program_residence_water";
		    $colmuns['Rental Water Rate'] = "rental_program_residence_water_rate";
		    $colmuns['Rental Water Cost'] = "rental_program_residence_water_cost";

		    // Charged by meter values store private
		    $colmuns['Private Electricity'] = "private_program_electricity";
		    $colmuns['Private Electricity Rate'] = "private_program_electricity_rate";
		    $colmuns['Private Electricity Cost'] = "private_program_electricity_cost";
		    $colmuns['Private Fuel/Diesel Oil'] = "private_program_fuel_oil";
		    $colmuns['Private Fuel/Diesel Oil Rate'] = "private_program_fuel_oil_rate";
		    $colmuns['Private Fuel/Diesel Oil Cost'] = "private_program_fuel_oil_cost";
		    $colmuns['Private Lpg'] = "private_program_lpg";
		    $colmuns['Private Lpg Rate'] = "private_program_lpg_rate";
		    $colmuns['Private Lpg Cost'] = "private_program_lpg_cost";
		    $colmuns['Private Natural Gas'] = "private_program_natural_gas";
		    $colmuns['Private Natural Gas Rate'] = "private_program_natural_gas_rate";
		    $colmuns['Private Natural Gas Cost'] = "private_program_natural_gas_cost";
		    $colmuns['Private District Cooling'] = "private_program_district_cooling";
		    $colmuns['Private District Cooling Rate'] = "private_program_district_cooling_rate";
		    $colmuns['Private District Cooling Cost'] = "private_program_district_cooling_cost";
		    $colmuns['Private District Heating'] = "private_program_district_heating";
		    $colmuns['Private District Heating Rate'] = "private_program_district_heating_rate";
		    $colmuns['Private District Heating Cost'] = "private_program_district_heating_cost";
		    $colmuns['Private Water'] = "private_program_water";
		    $colmuns['Private Water Rate'] = "private_program_water_rate";
		    $colmuns['Private Water Cost'] = "private_program_water_cost";
		$colmuns = array_change_key_case($colmuns, CASE_LOWER);
		    $keyExists = array('total_kwh', 'tariff', 'year_id', 'month_id');
		    $keyNotExists = array('total_kwh', 'tariff', 'total_cost');

		/*
		* Walk every header index (0..n). Do NOT shrink by counting only non-empty
		* headers � empty/hidden/foreign labels in the middle would otherwise
		* cause trailing mapped columns to be skipped.
		*/
		$maxColIndex = max((int) $numberCol, count($titleCells) - 1);

		$siteNames = array();
		for ($i = 1; $i <= $numberRow; $i++) {
			if (!isset($dataCells[$i][0])) {
				continue;
			}
			$siteNames[] = trim(strtolower($dataCells[$i][0]));
		}
		$siteNames = array_filter(array_values(array_unique($siteNames)));
		$allSiteids = $this->import_model->getSiteDetailByName($siteNames);

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$k = 0;

		for ($i = 1; $i <= $numberRow; $i++) {
			$statusInsertUtility = 0;
			$extraData = array();
			$dataInsertTotal = array();
			$dataInsert = array();

			if (!isset($dataCells[$i][0]) || $dataCells[$i][0] == '') {
				continue;
			}

			$siteName = trim($dataCells[$i][0]);
			$siteId = isset($allSiteids[$siteName]['id']) ? $allSiteids[$siteName]['id'] : '';
			$getMonth = isset($dataCells[$i][1]) ? trim($dataCells[$i][1]) : '';
			$getYear = isset($dataCells[$i][2]) ? trim($dataCells[$i][2]) : '';

			if ($getYear <= 2022 && $user_id != 1) {
				continue;
			}
			if ($siteId == '' && $getMonth == '' && $getYear == '') {
				continue;
			}
			if ($siteId == '') {
				$sites_name[] = $dataCells[$i][0];
				continue;
			}

			$siteHeaderKey = trim(strtolower($dataCells[0][0]));
			$dataInsertTotal[$colmuns[$siteHeaderKey]] = $siteId;
			$dataInsert[$colmuns[$siteHeaderKey]] = $siteId;

			for ($j = 1; $j <= $maxColIndex; $j++) {
				if (!array_key_exists($j, $dataCells[$i])) {
					continue;
				}

				$rawHeader = isset($dataCells[0][$j]) ? $dataCells[0][$j] : '';
				$headerKey = trim(iconv("UTF-8", "ISO-8859-1", strtolower($rawHeader)), " \t\n\r\0\x0B\xA0");
				if ($headerKey === '' || !isset($colmuns[$headerKey])) {
					// empty / hidden / foreign label � skip this cell, keep scanning
					continue;
				}

				$dbColumn = $colmuns[$headerKey];
				$cellValue = $dataCells[$i][$j];

				// Empty cell: do not overwrite (important for CDD/HDD preserve)
				if ($cellValue === null || $cellValue === '') {
					continue;
				}
				if (is_string($cellValue) && trim($cellValue) === '') {
					continue;
				}

				if (in_array($dbColumn, $keyExists, true)) {
					if (array_key_exists($dbColumn, $dataInsertTotal)) {
						if (isset($extraData[$k]) && count($extraData[$k]) == 2) {
							$k++;
						}
						$extraData[$k][$dbColumn] = $cellValue;
					} else {
						$dataInsertTotal[$dbColumn] = $cellValue;
					}
				}

				if (!in_array($dbColumn, $keyNotExists, true)) {
					if (is_numeric($cellValue) && $cellValue < 0) {
						$this->theme->set_message("File upload has negative values please check and re-upload.", 'error');
						redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
						exit;
					}
					$dataInsert[$dbColumn] = $cellValue;
				}
			}
			$k++;

			$sumTotalElectricity = 0;
			$sumTotalElectricityCost = 0;

			$dataInsertTotal['total_cost'] = round(
				(isset($dataInsertTotal['tariff']) ? $dataInsertTotal['tariff'] : 0)
				* (isset($dataInsertTotal['total_kwh']) ? $dataInsertTotal['total_kwh'] : 0)
			);
			$sumTotalElectricity += isset($dataInsertTotal['total_kwh']) ? $dataInsertTotal['total_kwh'] : 0;
			$sumTotalElectricityCost += $dataInsertTotal['total_cost'];

			$this->import_model->delete_entry_ifexist($dataInsertTotal);
			if (!empty($allSiteids[$siteName]['show_utility_electricity'])) {
				$this->import_model->insert_entity_details_electricity_tariff($dataInsertTotal);
				if (!empty($extraData)) {
					foreach ($extraData as $value) {
						foreach ($value as $key1 => $value1) {
							$dataInsertTotal[$key1] = $value1;
						}
						$dataInsertTotal['total_cost'] = round($dataInsertTotal['tariff'] * $dataInsertTotal['total_kwh']);
						$sumTotalElectricity += $dataInsertTotal['total_kwh'];
						$sumTotalElectricityCost += $dataInsertTotal['total_cost'];
						$this->import_model->insert_entity_details_electricity_tariff($dataInsertTotal);
					}
				}
			}

			if (!empty($sumTotalElectricityCost)) {
				$this->utilities_model->deleteNotification(array(
						'site_id' => $dataInsert['site_id'],
						'field_name' => 'electricity_tariff',
						'month' => $dataInsert['month_id'],
						'year' => $dataInsert['year_id'],
					));
			}

			$defaultZeroFields = array(
				'total_room_night', 'total_room_night_budget', 'total_guests_budget', 'total_guests',
				'total_laundered', 'total_fb_services', 'electricity_total_budget', 'electricity_total_budget_cost',
				'lpg_hot_water_boilers', 'lpg_hot_water_boilers_rate', 'lpg_steam_boilers', 'lpg_steam_boilers_rate',
				'lpg_kitchen', 'lpg_kitchen_rate', 'lpg_total_budget', 'lpg_total_budget_cost',
				'district_heating_total_budget', 'district_heating_total_budget_cost',
				'water_total_consumption_budget', 'water_total_consumption_budget_cost',
				'vehicle_petrol',
				'total_maximum_demand', 'total_purchased_electricity', 'total_purchased_electricity_cost',
				'average_purchased_electricity', 'total_electricity_kwh', 'total_electricity_cost', 'average_cost_per_kwh',
				'fuel_oil_hot_water_boilers', 'fuel_oil_hot_water_boilers_rate', 'fuel_oil_hot_water_boilers_cost',
				'fuel_oil_steam_boilers', 'fuel_oil_steam_boilers_rate', 'fuel_oil_steam_boilers_cost',
				'fuel_oil_others', 'fuel_oil_others_rate', 'fuel_oil_others_cost',
				'total_fuel_oil', 'total_fuel_oil_rate', 'total_fuel_oil_cost',
				'lpg_hot_water_boilers_cost', 'lpg_steam_boilers_cost', 'lpg_kitchen_cost',
				'total_lpg', 'total_lpg_rate', 'total_lpg_cost',
				'natural_gas_hot_water_boilers', 'natural_gas_hot_water_boilers_rate', 'natural_gas_hot_water_boilers_cost',
				'natural_gas_steam_boilers', 'natural_gas_steam_boilers_rate', 'natural_gas_steam_boilers_cost',
				'natural_gas_kitchen', 'natural_gas_kitchen_rate', 'natural_gas_kitchen_cost',
				'total_natural_gas', 'total_natural_gas_rate', 'total_natural_gas_cost',
				'district_heating', 'district_heating_rate', 'district_heating_cost',
				'district_cooling', 'district_cooling_rate', 'district_cooling_cost',
				'water_utility_supply_cost', 'waste_water_cost', 'water_Cisterns_cost', 'water_ro_cost', 'water_irrigation_cost',
				'water_total_consumption', 'water_total_consumption_cost', 'water_total_consumption_rate',
				'water_utility_supply_rate', 'water_utility_supply', 'waste_water', 'waste_water_rate',
				'water_Cisterns', 'water_Cisterns_rate', 'water_ro', 'water_ro_rate', 'water_irrigation', 'water_irrigation_rate',
				'maximum_demand', 'maximum_demand_price',
				'onsite_generators_quantity', 'total_onsite_generators_cost',
				'onsite_generators_fuel_oil_quantity', 'onsite_generators_fuel_oil_price',
				'onsite_generators_natural_gas_quantity', 'onsite_generators_natural_gas_price',
				'total_renewable_energy_production', 'total_renewable_energy_production_cost',
				'fixed_fees', 'lpg_fixed_cost', 'water_fixed_cost', 'natural_gas_fixed_cost',
			);
			foreach ($defaultZeroFields as $field) {
				if (!isset($dataInsert[$field]) || $dataInsert[$field] === '') {
					$dataInsert[$field] = 0;
				}
			}
			$dataInsert['forex'] = (!empty($dataInsert['forex'])) ? $dataInsert['forex'] : 1;
			// CDD/HDD: do NOT default to empty/0 � leave unset so model keeps existing DB values

			$v = function ($key) use (&$dataInsert) {
				return isset($dataInsert[$key]) && $dataInsert[$key] !== '' ? $dataInsert[$key] : 0;
			};

			$dataInsert['total_maximum_demand'] = round($v('maximum_demand') * $v('maximum_demand_price'));
			$dataInsert['total_purchased_electricity'] = $sumTotalElectricity;
			$dataInsert['total_purchased_electricity_cost'] = $sumTotalElectricityCost;
			$dataInsert['average_purchased_electricity'] = ($sumTotalElectricity != 0)
				? round($sumTotalElectricityCost / $sumTotalElectricity, $decimal_places) : 0;

			$dataInsert['total_electricity_kwh'] = $dataInsert['total_purchased_electricity']
			+ $v('onsite_generators_quantity') + $v('total_renewable_energy_production');

			$fixedFees = $v('fixed_fees');
			$lpgFixed = $v('lpg_fixed_cost');
			$waterFixed = $v('water_fixed_cost');
			$naturalGasFixed = $v('natural_gas_fixed_cost');

			$dataInsert['fixed_fees'] = $fixedFees;
			$dataInsert['lpg_fixed_cost'] = $lpgFixed;
			$dataInsert['water_fixed_cost'] = $waterFixed;
			$dataInsert['natural_gas_fixed_cost'] = $naturalGasFixed;
			$dataInsert['total_onsite_generators_cost'] = $v('total_onsite_generators_cost');
			$dataInsert['total_renewable_energy_production_cost'] = $v('total_renewable_energy_production_cost');

			$dataInsert['total_electricity_cost'] = $dataInsert['total_maximum_demand'] + $fixedFees
			+ $dataInsert['total_purchased_electricity_cost']
			+ $dataInsert['total_onsite_generators_cost']
			+ $dataInsert['total_renewable_energy_production_cost'];
			$dataInsert['total_purchased_electricity_cost'] += $fixedFees;

			$dataInsert['average_cost_per_kwh'] = ($dataInsert['total_electricity_kwh'] != 0)
				? round($dataInsert['total_electricity_cost'] / $dataInsert['total_electricity_kwh'], $decimal_places) : 0;

			$dataInsert['fuel_oil_hot_water_boilers_cost'] = round($v('fuel_oil_hot_water_boilers_rate') * $v('fuel_oil_hot_water_boilers'));
			$dataInsert['fuel_oil_steam_boilers_cost'] = round($v('fuel_oil_steam_boilers_rate') * $v('fuel_oil_steam_boilers'));
			$dataInsert['fuel_oil_others_cost'] = round($v('fuel_oil_others_rate') * $v('fuel_oil_others'));
			$dataInsert['total_onsite_generators_fuel_oil_cost'] = $v('onsite_generators_fuel_oil_quantity') * $v('onsite_generators_fuel_oil_price');
			$dataInsert['total_fuel_oil'] = $v('fuel_oil_hot_water_boilers') + $v('fuel_oil_steam_boilers') + $v('fuel_oil_others') + $v('onsite_generators_fuel_oil_quantity');
			$dataInsert['total_fuel_oil_rate'] = $v('fuel_oil_hot_water_boilers_rate') + $v('fuel_oil_steam_boilers_rate') + $v('fuel_oil_others_rate') + $v('onsite_generators_fuel_oil_price');
			$dataInsert['total_fuel_oil_cost'] = $dataInsert['fuel_oil_hot_water_boilers_cost'] + $dataInsert['fuel_oil_steam_boilers_cost'] + $dataInsert['fuel_oil_others_cost'] + $dataInsert['total_onsite_generators_fuel_oil_cost'];

			$dataInsert['lpg_hot_water_boilers_cost'] = round($v('lpg_hot_water_boilers') * $v('lpg_hot_water_boilers_rate'));
			$dataInsert['lpg_steam_boilers_cost'] = round($v('lpg_steam_boilers') * $v('lpg_steam_boilers_rate'));
			$dataInsert['lpg_kitchen_cost'] = round($v('lpg_kitchen') * $v('lpg_kitchen_rate'));
			$dataInsert['total_lpg'] = $v('lpg_hot_water_boilers') + $v('lpg_steam_boilers') + $v('lpg_kitchen');
			$dataInsert['total_lpg_rate'] = $v('lpg_hot_water_boilers_rate') + $v('lpg_steam_boilers_rate') + $v('lpg_kitchen_rate');
			$dataInsert['total_lpg_cost'] = $dataInsert['lpg_hot_water_boilers_cost'] + $dataInsert['lpg_steam_boilers_cost'] + $dataInsert['lpg_kitchen_cost'] + $lpgFixed;

			$dataInsert['natural_gas_hot_water_boilers_cost'] = $v('natural_gas_hot_water_boilers') * $v('natural_gas_hot_water_boilers_rate');
			$dataInsert['natural_gas_steam_boilers_cost'] = $v('natural_gas_steam_boilers') * $v('natural_gas_steam_boilers_rate');
			$dataInsert['natural_gas_kitchen_cost'] = $v('natural_gas_kitchen') * $v('natural_gas_kitchen_rate');
			$dataInsert['total_onsite_generators_natural_gas_cost'] = $v('onsite_generators_natural_gas_quantity') * $v('onsite_generators_natural_gas_price');
			$dataInsert['total_natural_gas'] = $v('natural_gas_hot_water_boilers') + $v('natural_gas_steam_boilers') + $v('natural_gas_kitchen');
			$dataInsert['total_natural_gas_rate'] = $v('natural_gas_hot_water_boilers_rate') + $v('natural_gas_steam_boilers_rate') + $v('natural_gas_kitchen_rate') + $v('onsite_generators_natural_gas_price');
			$dataInsert['total_natural_gas_cost'] = $dataInsert['natural_gas_hot_water_boilers_cost'] + $dataInsert['natural_gas_steam_boilers_cost'] + $dataInsert['natural_gas_kitchen_cost'] + $dataInsert['total_onsite_generators_natural_gas_cost'] + $naturalGasFixed;

			$dataInsert['district_heating_cost'] = round($v('district_heating') * $v('district_heating_rate'));
			$dataInsert['district_cooling_cost'] = round($v('district_cooling') * $v('district_cooling_rate'));

			$dataInsert['water_utility_supply_cost'] = round($v('water_utility_supply') * $v('water_utility_supply_rate'));
			$dataInsert['waste_water_cost'] = round($v('waste_water') * $v('waste_water_rate'));
			$dataInsert['water_Cisterns_cost'] = round($v('water_Cisterns') * $v('water_Cisterns_rate'));
			$dataInsert['water_ro_cost'] = round($v('water_ro') * $v('water_ro_rate'));
			$dataInsert['water_irrigation_cost'] = round($v('water_irrigation') * $v('water_irrigation_rate'));
			$dataInsert['water_total_consumption'] = $v('water_utility_supply') + $v('water_Cisterns') + $v('water_irrigation') + $v('water_ro');
			$dataInsert['water_total_consumption_cost'] = $dataInsert['water_utility_supply_cost'] + $dataInsert['waste_water_cost'] + $dataInsert['water_Cisterns_cost'] + $dataInsert['water_irrigation_cost'];
			$dataInsert['water_total_consumption_rate'] = ($dataInsert['water_total_consumption'] != 0)
				? round($dataInsert['water_total_consumption_cost'] / $dataInsert['water_total_consumption'], $decimal_places) : 0;
			$dataInsert['water_total_consumption_cost'] += $waterFixed;

			if (isset($allSiteids[$siteName])) {
				$siteFlags = $allSiteids[$siteName];
				$utilityFields = array(
					'show_utility_electricity' => array(
						'total_purchased_electricity', 'total_purchased_electricity_cost', 'average_purchased_electricity',
						'total_electricity_kwh', 'total_electricity_cost', 'total_maximum_demand', 'maximum_demand',
						'maximum_demand_price', 'fixed_fees', 'average_cost_per_kwh',
						'electricity_total_budget', 'electricity_total_budget_cost',
					),
					'show_utility_fuel_oil' => array(
						'fuel_oil_steam_boilers', 'fuel_oil_steam_boilers_rate', 'fuel_oil_steam_boilers_cost',
						'fuel_oil_hot_water_boilers_cost', 'fuel_oil_hot_water_boilers', 'fuel_oil_hot_water_boilers_rate',
						'fuel_oil_others', 'fuel_oil_others_rate', 'fuel_oil_others_cost',
						'total_fuel_oil', 'total_fuel_oil_rate', 'total_fuel_oil_cost',
						'fuel_total_budget', 'fuel_total_budget_cost',
					),
					'show_utility_lpg' => array(
						'lpg_fixed_cost', 'lpg_hot_water_boilers', 'lpg_hot_water_boilers_rate', 'lpg_hot_water_boilers_cost',
						'lpg_steam_boilers', 'lpg_steam_boilers_rate', 'lpg_steam_boilers_cost',
						'lpg_kitchen', 'lpg_kitchen_rate', 'lpg_kitchen_cost',
						'total_lpg', 'total_lpg_rate', 'total_lpg_cost', 'lpg_total_budget', 'lpg_total_budget_cost',
					),
					'show_utility_water' => array(
						'water_fixed_cost', 'water_utility_supply', 'water_utility_supply_rate', 'water_utility_supply_cost',
						'waste_water', 'waste_water_rate', 'waste_water_cost',
						'water_Cisterns', 'water_Cisterns_rate', 'water_Cisterns_cost',
						'water_ro', 'water_ro_rate', 'water_ro_cost',
						'water_total_consumption', 'water_total_consumption_rate', 'water_total_consumption_cost',
						'water_total_consumption_budget', 'water_total_consumption_budget_cost',
					),
					'show_utility_natural_gas' => array(
						'natural_gas_fixed_cost', 'natural_gas_hot_water_boilers', 'natural_gas_hot_water_boilers_rate', 'natural_gas_hot_water_boilers_cost',
						'natural_gas_steam_boilers', 'natural_gas_steam_boilers_rate', 'natural_gas_steam_boilers_cost',
						'natural_gas_kitchen', 'natural_gas_kitchen_rate', 'natural_gas_kitchen_cost',
						'total_natural_gas', 'total_natural_gas_rate', 'total_natural_gas_cost',
						'natural_gas_total_budget', 'natural_gas_total_budget_cost',
					),
					'show_utility_district_cooling' => array(
						'district_cooling_fixed_cost', 'district_cooling', 'district_cooling_rate', 'district_cooling_cost',
						'district_cooling_total_budget', 'district_cooling_total_budget_cost',
					),
					'show_utility_district_heating' => array(
						'district_heating_fixed_cost', 'district_heating', 'district_heating_rate', 'district_heating_cost',
						'district_heating_total_budget', 'district_heating_total_budget_cost',
					),
				);
				foreach ($utilityFields as $flag => $fields) {
					if (empty($siteFlags[$flag])) {
						foreach ($fields as $field) {
							unset($dataInsert[$field]);
						}
					}
				}

				$rowSiteDetail = $allSiteids[$siteName];
				$residence_types = isset($rowSiteDetail['residence_types']) ? explode(',', $rowSiteDetail['residence_types']) : array();
				$utility_types = getUtilityConstant();

				$this->site_residence_model->site_id = $siteId;
				$this->site_residence_model->user_id = $user_id;
				$this->site_residence_model->year_id = $dataInsert['year_id'];

				$dataInsert['month'] = $dataInsert['month_id'];
				$dataInsert['year'] = $dataInsert['year_id'];
				$dataInsert['user_id'] = $user_id ? $user_id : 0;
				$this->utilities_model->utilities_month = $dataInsert['month_id'];
				$this->utilities_model->utilities_year = $dataInsert['year_id'];

				$dataInsert = $this->site_residence_model->residenceBlockLogic(
					$dataInsert,
					$utility_types,
					$rowSiteDetail,
					$this->site_residence_model,
					$residence_types
				);
				unset($dataInsert['month'], $dataInsert['year'], $dataInsert['user_id']);

				// Delete + create utilities_cost (CDD/HDD preserved in model when unset)
				$statusInsertUtility = $this->import_model->insert_entity_details($dataInsert, $cdd_hdd_values);

				foreach ($dataInsert as $key => $value) {
					if (in_array($key, $fieldNamesArray, true) && !empty($value)) {
						$this->utilities_model->deleteNotification(array(
								'site_id' => $dataInsert['site_id'],
								'field_name' => $key,
								'month' => $dataInsert['month_id'],
								'year' => $dataInsert['year_id'],
							));
					}
				}
			}

			if ($statusInsertUtility) {
				$this->theme->set_message("File imported successfully.", 'success');
				$success = '1';
			}
		}
	}

	if ($success == '1') {
		$auditSiteId = $this->session->userdata[$this->section_name]['site_id'];
		$auditUserId = $this->session->userdata[$this->section_name]['user_id'];
		saveAuditTrail($auditUserId, $auditSiteId, 'Import (Monthly) ', 'Import');
	}

	$this->theme->set('page_title', lang('import'));
	if (!empty($sites_name)) {
		$this->theme->set_message("Sites - " . implode(',', array_unique($sites_name)) . " do not Exists.", 'error');
	}
	$this->theme->view($data);
    }




    public function export()
    {
	require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';
	$file_tmp = $_FILES['importfile']['tmp_name'];
	$file_name = $_FILES['importfile']['name'];
	$fileType = pathinfo($file_name, PATHINFO_EXTENSION);
	if ($fileType == "") {
	    $this->theme->set_message("Please upload file type with .xls extension.", 'error');
	    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
	    exit;
	} else if ($fileType != "xls") {
	    $this->theme->set_message("File type with .xls extension is allowed.", 'error');
	    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
	    exit;
	} else {

	    $site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	    $this->load->model('sites/sites_model');
	    $site_detail = $this->sites_model->get_site_detail_custom($site_id);

	    $cdd_hdd_values = $this->import_model->getMonthlyCddHddValues();

	    // For notifications
	    $fieldNamesArray = array_keys(getNotificationStaticList($site_id));

	    $this->load->model('utilities/utilities_model');

	    //$project_actionplan_image_name = "upload_file.".$fileType;
	    //$target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_actionplan_image_name;
	    //$_movestatus = move_uploaded_file($file_tmp, $target_file);

	    $data = new Spreadsheet_Excel_Reader($file_tmp, false);

	    $numberRow = $data->sheets[0]['numRows'];
	    $numberCol = $data->sheets[0]['numCols'];

	    $electricity_unit = GetSiteUtilityUnitName($site_id,'electricity');
	    $fuel_oil_unit = GetSiteUtilityUnitName($site_id,'fuel_oil');
	    $lpg_unit = GetSiteUtilityUnitName($site_id,'lpg');
	    $water_unit = GetSiteUtilityUnitName($site_id,'water');
	    $natural_gas_unit = GetSiteUtilityUnitName($site_id,'natural_gas');
	    $district_cooling_unit = GetSiteUtilityUnitName($site_id,'district_cooling');
	    $district_heating_unit = GetSiteUtilityUnitName($site_id,'district_heating');

	    /* Number Of columns define */
	    $colmuns['Site Name']                                            = "site_id";
	    $colmuns['Purchased Electricity '.$electricity_unit.''] = "total_kwh";
	    $colmuns['Purchased Electricity Tariff ($/'.$electricity_unit.')'] = "tariff";
	    $colmuns['Purchased Electricity Cost ($)']                       = "total_cost";
	    $colmuns['Month']                                                = "month_id";
	    $colmuns['Year']                                                 = "year_id";
	    $colmuns['Maximum Demand KVA/KW']                                = "maximum_demand";
	    $colmuns['Maximum Demand Tariff ($/KVA || $/KW)']                = "maximum_demand_price";
	    $colmuns['Maximum Demand Cost ($)']                              = "total_maximum_demand";
	    $colmuns['Fixed Fees Cost ($)']                                  = "fixed_fees";
	    $colmuns['Average PF']                                           = "average_pf";
	    $colmuns['Total Renewable Energy Production '.$electricity_unit.''] = "total_renewable_energy_production";
	    $colmuns['Total Renewable Energy Cost ($)'] = "total_renewable_energy_production_cost";
		$colmuns['Total Renewable Energy Exported '.$electricity_unit.''] = "total_renewable_energy_exported";
	    $colmuns['Total Renewable Energy Exported Cost ($)'] = "total_renewable_energy_exported_cost";
	    $colmuns['Onsite Generators '.$electricity_unit.''] = "onsite_generators_quantity";
	    $colmuns['Onsite Generators Cost ($)']                           = "total_onsite_generators_cost";
	    $colmuns['Total Electricity Cost']                               = "total_electricity_cost";
	    $colmuns['Total Electricity '.$electricity_unit.''] = "total_electricity_kwh";
	    $colmuns['Electricity Total Budgeted']                           = "electricity_total_budget";
	    $colmuns['Electricity Total Budgeted Cost ($)']                  = "electricity_total_budget_cost";
	    $colmuns['Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_hot_water_boilers";
	    $colmuns['Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_hot_water_boilers_rate";
	    $colmuns['Fuel Oil Steam Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_steam_boilers";
	    $colmuns['Fuel Oil Steam Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_steam_boilers_rate";
	    $colmuns['Fuel Oil Others ('.$fuel_oil_unit.') '.$fuel_oil_unit.''] = "fuel_oil_others";
	    $colmuns['Fuel Oil Others ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.''] = "fuel_oil_others_rate";
	    $colmuns['Fuel Oil Total Budgeted']                              = "fuel_total_budget";
	    $colmuns['Fuel Oil Total Budgeted Cost ($)']                     = "fuel_total_budget_cost";
	    $colmuns['LPG Hot-Water Boilers ('.$lpg_unit.')'] = "lpg_hot_water_boilers";
	    $colmuns['LPG Hot-Water Boilers $/'.$lpg_unit.''] = "lpg_hot_water_boilers_rate";
	    $colmuns['LPG Steam Boilers ('.$lpg_unit.')'] = "lpg_steam_boilers";
	    $colmuns['LPG Steam Boilers $/'.$lpg_unit.''] = "lpg_steam_boilers_rate";
	    $colmuns['LPG Kitchen ('.$lpg_unit.')'] = "lpg_kitchen";
	    $colmuns['LPG Kitchen $/'.$lpg_unit.''] = "lpg_kitchen_rate";
	    $colmuns['LPG Total Budgeted']                                   = "lpg_total_budget";
	    $colmuns['LPG Total Budgeted Cost ($)']                          = "lpg_total_budget_cost";
	    $colmuns['Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_hot_water_boilers";
	    $colmuns['Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_hot_water_boilers_rate";
	    $colmuns['Natural Gas Steam Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_steam_boilers";
	    $colmuns['Natural Gas Steam Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_steam_boilers_rate";
	    $colmuns['Natural Gas Kitchen ('.$natural_gas_unit.') '.$natural_gas_unit.''] = "natural_gas_kitchen";
	    $colmuns['Natural Gas Kitchen ('.$natural_gas_unit.') $/'.$natural_gas_unit.''] = "natural_gas_kitchen_rate";
	    $colmuns['Natural Gas Total Budgeted']                           = "natural_gas_total_budget";
	    $colmuns['Natural Gas Total Budgeted Cost ($)']                  = "natural_gas_total_budget_cost";
	    $colmuns['District Energy Heating '.$district_heating_unit.''] = "district_heating";
	    $colmuns['District Energy Heating $/'.$district_heating_unit.''] = "district_heating_rate";
	    $colmuns['Heating District Energy Total Budgeted']               = "district_heating_total_budget";
	    $colmuns['Heating District Energy Total Budgeted Cost ($)']      = "district_heating_total_budget_cost";
	    $colmuns['District Energy Cooling '.$district_cooling_unit.''] = "district_cooling";
	    $colmuns['District Energy Cooling $/'.$district_cooling_unit.''] = "district_cooling_rate";
	    $colmuns['Cooling District Energy Total Budgeted']               = "district_cooling_total_budget";
	    $colmuns['Cooling District Energy Total Budgeted Cost ($)']      = "district_cooling_total_budget_cost";
	    $colmuns['Water Utility Supply ('.$water_unit.') '.$water_unit.''] = "water_utility_supply";
	    $colmuns['Water Utility Supply ('.$water_unit.') $/'.$water_unit.''] = "water_utility_supply_rate";
	    $colmuns['Wastewater ('.$water_unit.') '.$water_unit.''] = "waste_water";
	    $colmuns['Wastewater ('.$water_unit.') $/'.$water_unit.''] = "waste_water_rate";
	    $colmuns['Water Cisterns ('.$water_unit.') '.$water_unit.''] = "water_Cisterns";
	    $colmuns['Water Cisterns ('.$water_unit.') $/'.$water_unit.''] = "water_Cisterns_rate";
	    $colmuns['Water Total Budgeted']                                 = "water_total_consumption_budget";
	    $colmuns['Water Total Budgeted Cost ($)']                        = "water_total_consumption_budget_cost";

	    /*$colmuns['Water Consumption Breakdown Cooling Towers (m3)'] = "water_consumption_breakdown_cooling_towers";
	    $colmuns['Water Consumption Breakdown BOH (m3)'] = "water_consumption_breakdown_boh";
	    $colmuns['Water Consumption Breakdown Rooms (m3)'] = "water_consumption_breakdown_rooms";
	    $colmuns['Water Consumption Breakdown Total Budgeted'] = "total_consumption_breakdown_budget";
	    $colmuns['Water Consumption Breakdown Total Budgeted Cost ($)'] = "total_consumption_breakdown_budget_cost";*/
	    $colmuns['Room Nights']  = "total_room_night";
	    $colmuns['Room Nights Budget']  = "total_room_night_budget";
	    $colmuns['Total Guests Budget'] = "total_guests_budget";
	    $colmuns['Total Guests'] = "total_guests";
	    $colmuns['Laundry Load'] = "total_laundered";
	    $colmuns['Food Covers'] = "total_fb_services";
	    /* $colmuns['Cooling Degree Day'] = "cdd";
	      $colmuns['Heating Degree Day'] = "hdd"; */
	    $colmuns['Revenue'] = "revenue";
	    $colmuns['Total F and B Sales'] = "total_f_b_sales";
	    $colmuns['Heating Fixed Cost'] = "district_heating_fixed_cost";
	    $colmuns['Cooling Fixed Cost'] = "district_cooling_fixed_cost";
	    $colmuns['LPG Fixed Cost'] = "lpg_fixed_cost";
	    $colmuns['Natural Gas Fixed Cost'] = "natural_gas_fixed_cost";
	    $colmuns['Water Fixed Cost'] = "water_fixed_cost";
	    /* Number Of columns define */

	    $keyExists = array('total_kwh', 'tariff', 'year_id', 'month_id');
	    $keyNotExists = array('total_kwh', 'tariff', 'total_cost');
	    $sites_name = array();
	    $k = 0;
	    $totalCol = 0;
	    for ($i = 1; $i < 2; $i++) {
		for ($j = 1; $j <= $numberCol; $j++) {
		    if ($data->sheets[0]['cells'][1][$j] != '') {
			$totalCol++;
		    }
		}
	    }

	    // Get all site id for imported data
	    $siteNames = array();
	    for ($i = 2; $i <= $numberRow; $i++) {
		$siteNames[] = trim($data->sheets[0]['cells'][$i][1]);
	    }
	    $siteNames = array_values(array_unique($siteNames));
	    $allSiteids = $this->import_model->get_site_detail_by_name($siteNames);

	    /* Start Of Number of rows */
	    for ($i = 2; $i <= $numberRow; $i++) {
		$statusInsert = 0;
		$statusInsertUtility = 0;
		$extraData = array();
		$dataInsertTotal = array();
		$dataInsert = array();

		if ($data->sheets[0]['cells'][$i][1] == '') {
		    //$this->theme->set_message("Site do not Exists.", 'error');
		    continue;
		}

		// Deprecate
		/* $siteDetail = $this->import_model->get_siteId(trim($data->sheets[0]['cells'][$i][1]));
		  $siteId = $siteDetail[0]['sites']['id']; */
		// Deprecate

		$siteId = $allSiteids[trim($data->sheets[0]['cells'][$i][1])]['id'];

		$getMonth = trim($data->sheets[0]['cells'][$i][2]);
		$getYear = trim($data->sheets[0]['cells'][$i][3]);
		if ($siteId == '' && $getMonth == '' && $getYear == '') {
		    continue;
		} else if ($siteId == '') {
		    $sites_name[] = $data->sheets[0]['cells'][$i][1];
		    continue;
		} else {
		    $dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;
		    $dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;
		}

		for ($j = 2; $j <= $totalCol; $j++) {
		    if (trim($data->sheets[0]['cells'][$i][$j]) != '') {
			if (in_array($colmuns[trim($data->sheets[0]['cells'][1][$j])], $keyExists)) {
			    if (array_key_exists($colmuns[trim($data->sheets[0]['cells'][1][$j])], $dataInsertTotal)) {
				if (count($extraData[$k]) == 2) {
				    $k++;
				}
				$extraData[$k][$colmuns[trim($data->sheets[0]['cells'][1][$j])]] = $data->sheets[0]['cells'][$i][$j];
			    } else {
				$dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][$j])]] = $data->sheets[0]['cells'][$i][$j];
			    }
			}
			if (!in_array($colmuns[trim($data->sheets[0]['cells'][1][$j])], $keyNotExists)) {
			    $dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][$j])]] = $data->sheets[0]['cells'][$i][$j];
			}
		    } else {
			continue;
		    }
		}
		$k++;

		$sumTotalElectricity = 0;
		$sumTotalPurchasedElectricity = 0;
		$sumTotalElectricityCost = 0;

		$dataInsertTotal['total_cost'] = round($dataInsertTotal['tariff'] * $dataInsertTotal['total_kwh']);
		$sumTotalElectricity += floatval($dataInsertTotal['total_kwh']);
		$sumTotalPurchasedElectricity += $dataInsertTotal['tariff'];
		$sumTotalElectricityCost += $dataInsertTotal['total_cost'];

		$this->import_model->delete_entry_ifexist($dataInsertTotal);
		$siteName = trim($data->sheets[0]['cells'][$i][1]);

		if ($allSiteids[$siteName]['show_utility_electricity']) {
		    $statusInsert = $this->import_model->insert_entity_details_electricity_tariff($dataInsertTotal);
		    if (count($extraData) > 0) {
			foreach ($extraData as $key => $value) {
			    foreach ($value as $key1 => $value1) {
				$dataInsertTotal[$key1] = $value1;
			    }
			    $dataInsertTotal['total_cost'] = round($dataInsertTotal['tariff'] * $dataInsertTotal['total_kwh']);
			    $sumTotalElectricity += $dataInsertTotal['total_kwh'];
			    $sumTotalPurchasedElectricity += $dataInsertTotal['tariff'];
			    $sumTotalElectricityCost += $dataInsertTotal['total_cost'];
			    $statusInsert = $this->import_model->insert_entity_details_electricity_tariff($dataInsertTotal);
			}
		    }
		}

		// Delete electricity notification
		if (in_array('electricity_tariff', $fieldNamesArray)) {
		    if (!empty($sumTotalElectricityCost)) {

			$deleteData = array(
			    'site_id' => $dataInsert['site_id'],
			    'field_name' => 'electricity_tariff',
			    'month' => $dataInsert['month_id'],
			    'year' => $dataInsert['year_id'],
			);

			$this->utilities_model->deleteNotification($deleteData);
		    }
		}

		$dataInsert['total_maximum_demand'] = round($dataInsert['maximum_demand'] * $dataInsert['maximum_demand_price']);

		$dataInsert['total_purchased_electricity'] = $sumTotalElectricity;
		$dataInsert['total_purchased_electricity_cost'] = $sumTotalElectricityCost;

		$dataInsert['average_purchased_electricity'] = ($sumTotalElectricity != 0) ? $sumTotalElectricityCost / $sumTotalElectricity : 0;
		$dataInsert['average_purchased_electricity'] = round($dataInsert['average_purchased_electricity'], $decimal_places);

		$dataInsert['total_electricity_kwh'] = $dataInsert['total_purchased_electricity'] + $dataInsert['onsite_generators_quantity'] + $dataInsert['total_renewable_energy_production'] - $dataInsert['total_renewable_energy_exported'];
		//$dataInsert['total_electricity_cost'] = $dataInsert['total_purchased_electricity_cost']+$dataInsert['total_onsite_generators_cost'];

		$dataInsert_total_maximum_demand = (!empty($dataInsert['total_maximum_demand'])) ? $dataInsert['total_maximum_demand'] : 0;
		$dataInsert_fixed_fees = (!empty($dataInsert['fixed_fees'])) ? $dataInsert['fixed_fees'] : 0;
		$dataInsert_total_purchased_electricity_cost = (!empty($dataInsert['total_purchased_electricity_cost'])) ? $dataInsert['total_purchased_electricity_cost'] : 0;
		$dataInsert_total_onsite_generators_cost = (!empty($dataInsert['total_onsite_generators_cost'])) ? $dataInsert['total_onsite_generators_cost'] : 0;
		$dataInsert_total_renewable_energy_production_cost = (!empty($dataInsert['total_renewable_energy_production_cost'])) ? $dataInsert['total_renewable_energy_production_cost'] : 0;
		$dataInsert_total_renewable_energy_exported_cost = (!empty($dataInsert['total_renewable_energy_exported_cost'])) ? $dataInsert['total_renewable_energy_exported_cost'] : 0;
		$dataInsert['total_electricity_cost'] = $dataInsert_total_maximum_demand + $dataInsert_fixed_fees + $dataInsert_total_purchased_electricity_cost + $dataInsert_total_onsite_generators_cost + $dataInsert_total_renewable_energy_production_cost - $dataInsert_total_renewable_energy_exported_cost;

		$dataInsert['average_cost_per_kwh'] = $dataInsert['total_electricity_cost'] / $dataInsert['total_electricity_kwh'];
		$dataInsert['average_cost_per_kwh'] = round($dataInsert['average_cost_per_kwh'], $decimal_places);

		$dataInsert['total_purchased_electricity_cost'] += $dataInsert_fixed_fees;

		$dataInsert['fuel_oil_hot_water_boilers_cost'] = round($dataInsert['fuel_oil_hot_water_boilers_rate'] * $dataInsert['fuel_oil_hot_water_boilers']);
		$dataInsert['fuel_oil_steam_boilers_cost'] = round($dataInsert['fuel_oil_steam_boilers_rate'] * $dataInsert['fuel_oil_steam_boilers']);
		$dataInsert['fuel_oil_others_cost'] = round($dataInsert['fuel_oil_others_rate'] * $dataInsert['fuel_oil_others']);

		$dataInsert['total_fuel_oil'] = $dataInsert['fuel_oil_hot_water_boilers'] + $dataInsert['fuel_oil_steam_boilers'] + $dataInsert['fuel_oil_others'];
		$dataInsert['total_fuel_oil_rate'] = $dataInsert['fuel_oil_hot_water_boilers_rate'] + $dataInsert['fuel_oil_steam_boilers_rate'] + $dataInsert['fuel_oil_others_rate'];
		$dataInsert['total_fuel_oil_cost'] = $dataInsert['fuel_oil_hot_water_boilers_cost'] + $dataInsert['fuel_oil_steam_boilers_cost'] + $dataInsert['fuel_oil_others_cost'];

		$dataInsert['lpg_hot_water_boilers_cost'] = round($dataInsert['lpg_hot_water_boilers'] * $dataInsert['lpg_hot_water_boilers_rate']);
		$dataInsert['lpg_steam_boilers_cost'] = round($dataInsert['lpg_steam_boilers'] * $dataInsert['lpg_steam_boilers_rate']);
		$dataInsert['lpg_kitchen_cost'] = round($dataInsert['lpg_kitchen'] * $dataInsert['lpg_kitchen_rate']);

		$dataInsert['total_lpg'] = $dataInsert['lpg_hot_water_boilers'] + $dataInsert['lpg_steam_boilers'] + $dataInsert['lpg_kitchen'];
		$dataInsert['total_lpg_rate'] = $dataInsert['lpg_hot_water_boilers_rate'] + $dataInsert['lpg_steam_boilers_rate'] + $dataInsert['lpg_kitchen_rate'];
		$dataInsert['total_lpg_cost'] = $dataInsert['lpg_hot_water_boilers_cost'] + $dataInsert['lpg_steam_boilers_cost'] + $dataInsert['lpg_kitchen_cost'];

		$dataInsert['natural_gas_hot_water_boilers_cost'] = $dataInsert['natural_gas_hot_water_boilers'] * $dataInsert['natural_gas_hot_water_boilers_rate'];
		$dataInsert['natural_gas_steam_boilers_cost'] = $dataInsert['natural_gas_steam_boilers'] * $dataInsert['natural_gas_steam_boilers_rate'];
		$dataInsert['natural_gas_kitchen_cost'] = $dataInsert['natural_gas_kitchen'] * $dataInsert['natural_gas_kitchen_rate'];

		$dataInsert['total_natural_gas'] = $dataInsert['natural_gas_hot_water_boilers'] + $dataInsert['natural_gas_steam_boilers'] + $dataInsert['natural_gas_kitchen'];
		$dataInsert['total_natural_gas_rate'] = $dataInsert['natural_gas_hot_water_boilers_rate'] + $dataInsert['natural_gas_steam_boilers_rate'] + $dataInsert['natural_gas_kitchen_rate'];
		$dataInsert['total_natural_gas_cost'] = $dataInsert['natural_gas_hot_water_boilers_cost'] + $dataInsert['natural_gas_steam_boilers_cost'] + $dataInsert['natural_gas_kitchen_cost'];

		$dataInsert['district_heating_cost'] = round($dataInsert['district_heating'] * $dataInsert['district_heating_rate']);
		$dataInsert['district_cooling_cost'] = round($dataInsert['district_cooling'] * $dataInsert['district_cooling_rate']);

		$dataInsert['water_utility_supply_cost'] = round($dataInsert['water_utility_supply'] * $dataInsert['water_utility_supply_rate']);
		$dataInsert['waste_water_cost'] = round($dataInsert['waste_water'] * $dataInsert['waste_water_rate']);
		$dataInsert['water_Cisterns_cost'] = round($dataInsert['water_Cisterns'] * $dataInsert['water_Cisterns_rate']);

		$dataInsert['water_total_consumption'] = $dataInsert['water_utility_supply'] + $dataInsert['water_Cisterns'];
		//by hp18
		$dataInsert['water_total_consumption_cost'] = $dataInsert['water_utility_supply_cost'] + $dataInsert['waste_water_cost'] + $dataInsert['water_Cisterns_cost'];
		$dataInsert['water_total_consumption_rate'] = round($dataInsert['water_total_consumption_cost'] / $dataInsert['water_total_consumption'], 2);

		/* $dataInsert['total_consumption_breakdown'] = $dataInsert['water_consumption_breakdown_cooling_towers']+$dataInsert['water_consumption_breakdown_boh']+$dataInsert['water_consumption_breakdown_rooms']; */

		// Check if utility is enable in site detail
		$siteName = trim($data->sheets[0]['cells'][$i][1]);
		if (isset($allSiteids[$siteName])) {

		    // Remove unused utilities from
		    if (!$allSiteids[$siteName]['show_utility_electricity']) {
			unset($dataInsert['total_purchased_electricity']);
			unset($dataInsert['total_purchased_electricity_cost']);
			unset($dataInsert['average_purchased_electricity']);
			unset($dataInsert['total_electricity_kwh']);
			unset($dataInsert['total_electricity_cost']);
			unset($dataInsert['total_maximum_demand']);
			unset($dataInsert['maximum_demand']);
			unset($dataInsert['maximum_demand_price']);
			unset($dataInsert['fixed_fees']);
			unset($dataInsert['average_cost_per_kwh']);
			unset($dataInsert['electricity_total_budget']);
			unset($dataInsert['electricity_total_budget_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_fuel_oil']) {
			unset($dataInsert['fuel_oil_steam_boilers']);
			unset($dataInsert['fuel_oil_steam_boilers_rate']);
			unset($dataInsert['fuel_oil_hot_water_boilers_cost']);
			unset($dataInsert['fuel_oil_steam_boilers_cost']);
			unset($dataInsert['fuel_oil_others_cost']);
			unset($dataInsert['total_fuel_oil']);
			unset($dataInsert['total_fuel_oil_rate']);
			unset($dataInsert['total_fuel_oil_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_lpg']) {
			unset($dataInsert['lpg_fixed_cost']);
			unset($dataInsert['lpg_hot_water_boilers_cost']);
			unset($dataInsert['lpg_steam_boilers_cost']);
			unset($dataInsert['lpg_kitchen_cost']);
			unset($dataInsert['total_lpg']);
			unset($dataInsert['total_lpg_rate']);
			unset($dataInsert['total_lpg_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_water']) {
			unset($dataInsert['water_fixed_cost']);
			unset($dataInsert['water_utility_supply_cost']);
			unset($dataInsert['waste_water_cost']);
			unset($dataInsert['water_Cisterns_cost']);
			unset($dataInsert['water_ro_cost']);
			unset($dataInsert['water_total_consumption']);
			unset($dataInsert['water_total_consumption_rate']);
			unset($dataInsert['water_total_consumption_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_natural_gas']) {
			unset($dataInsert['natural_gas_fixed_cost']);
			unset($dataInsert['natural_gas_hot_water_boilers_cost']);
			unset($dataInsert['natural_gas_steam_boilers_cost']);
			unset($dataInsert['natural_gas_kitchen_cost']);
			unset($dataInsert['total_natural_gas']);
			unset($dataInsert['total_natural_gas_rate']);
			unset($dataInsert['total_natural_gas_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_district_cooling']) {
			unset($dataInsert['district_cooling_fixed_cost']);
			unset($dataInsert['district_cooling_cost']);
		    }

		    if (!$allSiteids[$siteName]['show_utility_district_heating']) {
			unset($dataInsert['district_heating_fixed_cost']);
			unset($dataInsert['district_heating_cost']);
		    }
		}
		/* if($dataInsert['month_id'] == '3' && $dataInsert['year_id'] == '2017'){
		  echo '<pre>';print_r($dataInsert);exit;
		  } */

		$statusInsertUtility = $this->import_model->insert_entity_details($dataInsert, $cdd_hdd_values);

		foreach ($dataInsert as $key => $value) {
		    if (in_array($key, $fieldNamesArray)) {
			if (!empty($value)) {
			    $deleteData = array(
				'site_id' => $dataInsert['site_id'],
				'field_name' => $key,
				'month' => $dataInsert['month_id'],
				'year' => $dataInsert['year_id'],
			    );

			    $this->utilities_model->deleteNotification($deleteData);
			}
		    }
		}
		if ($statusInsertUtility) {
		    $this->theme->set_message("File imported successfully.", 'success');
//                    unlink($target_file);
		}
	    }
	    /* End Of Number of rows */
	}

	//Create page-title
	$this->theme->set('page_title', lang('import'));
	if (!empty($sites_name)) {
	    $site_names = implode(',', $sites_name);
	    $this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');
	}
	//Render view
	redirect(BASE_ADMIN_URL_CUSTOM . 'import');
    }

    public function daily()
    {
	ini_set('memory_limit', '-1');
	if (!empty($this->input->post())) {
	    $file_tmp = $_FILES['importfile']['tmp_name'];
	    $file_name = $_FILES['importfile']['name'];
	    $fileType = pathinfo($file_name, PATHINFO_EXTENSION);

	    if ($fileType == "") {
		$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/daily');
		exit;
	    } else if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
		$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/daily');
		exit;
	    } else {
		$TOTAL_INDEX = 0;
		$SUBMETERING_INDEX = 1;
		$HOURLY_INDEX = 2;
		$HALF_HOURLY_INDEX = 3;
		$DJB_DAILY_READINGS_INDEX = 4;
		$process = false;

		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$this->load->model('sites/sites_model');
		$site_detail = $this->sites_model->get_site_detail_custom($site_id);

		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$file = "E:/Shared/HEP/HourlyHEP.xlsx";
		$destinationFile = BASE_PATH_CUSTOM . '/assets/uploads/imported_excels/';
		if (!is_dir($destinationFile)) {
		    mkdir($destinationFile);
		}
		$newFile = $destinationFile .uniqid().'_'.date("Y-n-j").'_'. time() . '.'.$fileType;
		if (move_uploaded_file($file_tmp, $newFile)) {
		    try {
			$inputFileType = PHPExcel_IOFactory::identify($newFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadAllSheets();
			$objPHPExcel = $objReader->load($newFile);
			$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
			$worksheetData = $objReader->listWorksheetInfo($newFile);

			// Get daily cdd and hdd values
			$cdd_hdd_values = $this->import_model->getDailyCddHddValues();
			/* ------Daily Data importing start ----- */

			$totalRows = $worksheetData[$TOTAL_INDEX]['totalRows'];
			$sheetName = $worksheetData[$TOTAL_INDEX]['worksheetName'];

			$electricity_unit = GetSiteUtilityUnitName($site_id,'electricity');
			$fuel_oil_unit = GetSiteUtilityUnitName($site_id,'fuel_oil');
			$lpg_unit = GetSiteUtilityUnitName($site_id,'lpg');
			$water_unit = GetSiteUtilityUnitName($site_id,'water');
			$natural_gas_unit = GetSiteUtilityUnitName($site_id,'natural_gas');
			$district_cooling_unit = GetSiteUtilityUnitName($site_id,'district_cooling');
			$district_heating_unit = GetSiteUtilityUnitName($site_id,'district_heating');

			// echo 'Daily Start @ '.date('Y-n-j h:i:sa').'<br/>';
			if ($totalRows > 1) {
			    // Map excel columns with database keys
			    $colmuns                                   = array();
			    $colmuns['Site Name']                      = "site_id";
			    $colmuns['Day']                            = "date_id";
			    $colmuns['Month']                          = "month_id";
			    $colmuns['Year']                           = "year_id";
			    $colmuns['Total Electricity '.$electricity_unit.''] = "total_electricity_kwh";
			    $colmuns['Electricity Tariff ($/'.$electricity_unit.')'] = "total_electricity_kwh_tariff";
			    $colmuns['Total Fuel Oil  ('.$fuel_oil_unit.')'] = "total_diesel_fuel";
			    $colmuns['Fuel Oil  $/'.$fuel_oil_unit.''] = "total_diesel_fuel_tariff";
			    $colmuns['LPG ('.$lpg_unit.')'] = "total_lpg_consumption";
			    $colmuns['LPG $/'.$lpg_unit.''] = "total_lpg_consumption_tariff";
			    $colmuns['Natural Gas ('.$natural_gas_unit.')'] = "total_natural_gas_consumption";
			    $colmuns['Natural Gas $/'.$natural_gas_unit.''] = "total_natural_gas_consumption_tariff";
			    $colmuns['District Energy Heating '.$district_heating_unit.''] = "total_district_heating_consumption";
			    $colmuns['District Energy Heating $/'.$district_heating_unit.''] = "total_district_heating_consumption_tariff";
			    $colmuns['District Energy Cooling '.$district_cooling_unit.''] = "total_district_cooling_consumption";
			    $colmuns['District Energy Cooling $/'.$district_cooling_unit.''] = "total_district_cooling_consumption_tariff";
			    $colmuns['Water Utility Supply ('.$water_unit.') '.$water_unit.''] = "total_water_consumption";
			    $colmuns['Water Utility Supply ('.$water_unit.') $/'.$water_unit.''] = "total_water_consumption_tariff";
			    $colmuns['Irrigation Water ('.$water_unit.') '.$water_unit.''] = "total_landscape_water_consumption";
			    $colmuns['Irrigation water  $/'.$water_unit.''] = "total_landscape_water_consumption_tariff";
			    $colmuns['Waste Water ('.$water_unit.') '.$water_unit.''] = "total_waste_water_consumption";
			    $colmuns['Waste water $/'.$water_unit.''] = "total_waste_water_consumption_tariff";
			    $colmuns['Room Nights']                    = "total_room_night";
			    $colmuns['Total Guests']                   = "total_guests";
			    $colmuns['Cooling Degree Day']             = "cdd";
			    $colmuns['Heating Degree Day']             = "hdd";
			    // Convert data with database name mapping to use only configured columns to use
			    $importData = array();
			    $siteNames = array();
			    $dataCells = $objPHPExcel->getSheet($TOTAL_INDEX)->toArray();
			    $titleCells = $dataCells[0];
			    $selectCondition = array();
			    $colmuns=array_change_key_case($colmuns, CASE_LOWER);

			    foreach ($dataCells as $key => $cells) {
				if ($key == 0) {
				    // For titles
				    continue;
				}
				$iData = array();
				foreach ($titleCells as $key => $value) {
				    $value = trim(strtolower($value));

				    if ($colmuns[$value] == 'site_id') {
					$siteNames[] = $cells[$key];
				    }

				    // Check if utility enabled in site config
				    if (isset($colmuns[$value])) {
					if ($cells[$key] != null) {
					    $iData[$colmuns[$value]] = $cells[$key];
					} else {
					    $iData[$colmuns[$value]] = '';
					}
				    }
				}
				$monthIDs[] = $iData['month_id'];
				if (isset($iData['site_id']) && $iData['site_id'] != '') {
				    if (!array_key_exists($iData['site_id'], $selectCondition)) {
					$selectCondition[$iData['site_id']] = array(
					    'year_id' => $iData['year_id'],
					    'month_id' => $iData['month_id']
					);
				    }
				    $importData[] = $iData;
				}
			    }
			    $selectCondition[$iData['site_id']]['month_id'] = array_unique($monthIDs);
			    $siteNames = array_values(array_unique($siteNames));
			    $siteFetchFields = "id,site_location_name,show_utility_electricity,show_utility_fuel_oil,show_utility_lpg,show_utility_water,show_utility_irrigation_water,show_utility_water_waste,show_utility_natural_gas,show_utility_district_heating,show_utility_district_cooling";
			    $allSiteids = $this->import_model->getSiteDetailByName($siteNames, $siteFetchFields);
			    $batchInsertData = array();
			    // Store data in database
			    $siteIds = array();
			    if (!empty($importData)) {
				foreach ($importData as $key => $value) {
				    $value['site_id'] = trim($value['site_id']);
				    if (isset($allSiteids[$value['site_id']])) {
					foreach ($selectCondition as $sKey => $site) {
					    if ($sKey == $value['site_id']) {
						$selectCondition[$allSiteids[$value['site_id']]['id']] = $site;
						unset($selectCondition[$sKey]);
					    }
					}
					// Remove unused utilities from
					if (!$allSiteids[$value['site_id']]['show_utility_electricity']) {
					    unset($value["total_electricity_kwh"]);
					    unset($value["total_electricity_kwh_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_fuel_oil']) {
					    unset($value["total_diesel_fuel"]);
					    unset($value["total_diesel_fuel_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_lpg']) {
					    unset($value["total_lpg_consumption"]);
					    unset($value["total_lpg_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_water']) {
					    unset($value["total_water_consumption"]);
					    unset($value["total_water_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_irrigation_water']) {
					    unset($value["total_landscape_water_consumption"]);
					    unset($value["total_landscape_water_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_water_waste']) {
					    unset($value["total_waste_water_consumption"]);
					    unset($value["total_waste_water_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_natural_gas']) {
					    unset($value["total_natural_gas_consumption"]);
					    unset($value["total_natural_gas_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_district_cooling']) {
					    unset($value["total_district_cooling_consumption"]);
					    unset($value["total_district_cooling_consumption_tariff"]);
					}

					if (!$allSiteids[$value['site_id']]['show_utility_district_heating']) {
					    unset($value["total_district_heating_consumption"]);
					    unset($value["total_district_heating_consumption_tariff"]);
					}

					$value['site_id'] = $allSiteids[$value['site_id']]['id'];
					// Check for valid value
					$value['total_electricity_kwh'] = !is_numeric($value['total_electricity_kwh']) ? 0 : $value['total_electricity_kwh'];
					$value['total_electricity_kwh_tariff'] = !is_numeric($value['total_electricity_kwh_tariff']) ? 0 : $value['total_electricity_kwh_tariff'];
					$value['total_natural_gas_consumption'] = !is_numeric($value['total_natural_gas_consumption']) ? 0 : $value['total_natural_gas_consumption'];
					$value['total_natural_gas_consumption_tariff'] = !is_numeric($value['total_natural_gas_consumption_tariff']) ? 0 : $value['total_natural_gas_consumption_tariff'];
					$value['total_water_consumption'] = !is_numeric($value['total_water_consumption']) ? 0 : $value['total_water_consumption'];
					$value['total_water_consumption_tariff'] = !is_numeric($value['total_water_consumption_tariff']) ? 0 : $value['total_water_consumption_tariff'];
					$value['total_room_night'] = !is_numeric($value['total_room_night']) ? 0 : $value['total_room_night'];

					array_push($siteIds, $value['site_id']);
					if (!empty($value['date_id'])) {
					    $batchInsertData[] = $value;
					}
				    }
				}

				// Select existing utilities
				foreach ($selectCondition as $selectKey => $value) {
				    if(!is_numeric($selectKey)){
					unset($selectCondition[$selectKey]);
				    }
				}
				$selectFields = "id,site_id,date_id,month_id,year_id,total_electricity_kwh,total_electricity_kwh_tariff,total_lpg_consumption,total_lpg_consumption_tariff,total_water_consumption,total_water_consumption_tariff,total_landscape_water_consumption,total_landscape_water_consumption_tariff,total_room_night,total_guests";
				$existingUtilities = $this->import_model->batchSelectUtility($selectCondition, $selectFields);
				// Prepare existing utilities for delete
				$deleteId = array();
				foreach ($batchInsertData as $utilities) {
					$this->import_model->deleteDailyUtilityIfexists($utilities);
				}
				// Insert new data
				$statusInsertUtility = $this->import_model->insert_daily_utilities($batchInsertData, $cdd_hdd_values);
			    }
			    $process = true;
			}
			// echo 'Daily End @ '.date('Y-n-j h:i:sa').'<br/>';
			/* ------Daily Data importing End ----- */

			/* Dialy submission Start */
			$totalRows = $worksheetData[$SUBMETERING_INDEX]['totalRows'];
			$sheetName = $worksheetData[$SUBMETERING_INDEX]['worksheetName'];
			// echo 'Daily Submetering Start @ '.date('Y-n-j h:i:sa').'<br/>';
			if ($totalRows > 1) {
			    $allData = array();
			    $siteNames = array();
			    $dataCells = $objPHPExcel->getSheet($SUBMETERING_INDEX)->toArray();
			    $titleCells = $dataCells[0];
			    // Static columns for site
			    $staticColmuns = array();
			    $staticColmuns['Day'] = "date_id";
			    $staticColmuns['Month'] = "month_id";
			    $staticColmuns['Year'] = "year_id";
			    $staticColmuns['Room Nights'] = "total_room_night";
			    $staticColmuns['Total Guests'] = "total_guests";
			    //$staticColmuns['Cooling Degree Day']            = "cdd";
			    //$staticColmuns['Heating Degree Day']            = "hdd";
			    $staticColmuns['Electricity Tariff ($/'.$electricity_unit.')'] = "electricity_cost";
			    $staticColmuns['Fuel Oil  $/'.$fuel_oil_unit.''] = "fuel_oil_cost";
			    $staticColmuns['LPG $/'.$lpg_unit.''] = "lpg_cost";
			    $staticColmuns['Water Cost ($)'] = "water_cost";
			    $staticColmuns['Natural Gas $/'.$natural_gas_unit.''] = "natural_gas_cost";
			    $staticColmuns['District Energy Heating $/'.$district_heating_unit.''] = "district_heating_cost";
			    $staticColmuns['District Energy Cooling $/'.$district_cooling_unit.''] = "district_cooling_cost";

			    // Convert data with database name mapping to use only configured columns to use
			    $siteFetchFields = "id,site_location_name";
			    $allSiteids = $this->import_model->getSiteDetailByName(array(), $siteFetchFields);
			    $dailySubmissionFields = "id,title,site_id";
			    $allDailySubmissionDetails = $this->sites_model->getAllDailyReadingSettings($dailySubmissionFields);

			    $sites_daily_submission = array();
			    // Arrange submission data site wise
			    if (!empty($allDailySubmissionDetails)) {
				foreach ($allDailySubmissionDetails as $key => $value) {
				    $dynamicTitle = array();
				    $dynamicTitle['id'] = $value['id'];
				    $dynamicTitle['title'] = $value['title'];
				    $sites_daily_submission[$value['site_id']][] = $dynamicTitle;
				}
			    }

			    $selectCondition = array();
			    foreach ($dataCells as $key => $cell) {
				if ($key == 0) {
				    // For titles
				    continue;
				}

				$siteNames[] = $cell[0];

				$iData = array();

				foreach ($titleCells as $key => $value) {
				    $value = trim($value);

				    if ($cell[$key] != null) {
					$iData[$value] = str_replace('* ', '', $cell[$key]);
				    } else {
					$iData[$value] = '';
				    }
				}

				$iData['site_id'] = $allSiteids[trim($iData['Site Name'])]['id'];

				if (!empty($iData['site_id'])) {
				    $allData[] = $iData;
				}
			    }
			    $siteNames = array_values(array_unique($siteNames));
			    // Store data
			    if (!empty($allData)) {
				$insertBulkFixedData = array();
				$bulkInsertData = array();
				$staticFixedCondition = array();

				foreach ($allData as $key => $value) {
				    if (empty($value['Site Name'])) {
					continue;
				    }

				    if (!isset($staticFixedCondition[$value['site_id']])) {
					$staticFixedCondition[$value['site_id']] = array('year_id' => $value['Year'], 'month_id' => $value['Month']);
				    }
				    // Prepare Static data
				    $staticDataInsert = array();
				    $staticDataInsert['site_id'] = $value['site_id'];
				    foreach ($staticColmuns as $key1 => $value1) {
					$staticDataInsert[$value1] = is_numeric($value[$key1]) ? $value[$key1] : 0;
				    }
				    // Store fixed daily data in database
				    $insertBulkFixedData[] = $staticDataInsert;

				    // Prepare Dynamic data
				    if (isset($sites_daily_submission[$value['site_id']]) && !empty($sites_daily_submission[$value['site_id']])) {
					$dynamicDataInsert = array();

					foreach ($sites_daily_submission[$value['site_id']] as $value2) {

					    //utility_title_id
					    $dynamicDataInsert['site_id'] = $value['site_id'];
					    $dynamicDataInsert['date_id'] = $staticDataInsert['date_id'];
					    $dynamicDataInsert['month_id'] = $staticDataInsert['month_id'];
					    $dynamicDataInsert['year_id'] = $staticDataInsert['year_id'];
					    $dynamicDataInsert['utility_title_id'] = $value2['id'];
					    $dynamicDataInsert['value'] = is_numeric($value[$value2['title']]) ? $value[$value2['title']] : 0;
					    $key = $dynamicDataInsert['site_id'] . '_' . $dynamicDataInsert['year_id'] . '_' . $dynamicDataInsert['month_id'] . '_' . $dynamicDataInsert['date_id'] . '_' . $dynamicDataInsert['utility_title_id'];
					    $bulkInsertData[$key] = $dynamicDataInsert;
					    unset($dynamicDataInsert);
					}
				    }
				}

				//Dynamic data delete and insert
				$existingDynamicData = $this->import_model->batchSelectDynamicUtility($staticFixedCondition);

				$dynamicDeleteIds = array();
				foreach ($bulkInsertData as $key => $dynamicData) {
				    if (isset($existingDynamicData[$key])) {
					$dynamicDeleteIds[] = $existingDynamicData[$key];
				    }
				}


				if (!empty($bulkInsertData)) {
				    $statusInsertDynamicUtility = $this->import_model->insert_daily_dynamic_submission_utilities($bulkInsertData);
				}

				if (!empty($dynamicDeleteIds) && $statusInsertDynamicUtility == true) {
				    $existingFixedData1 = $this->import_model->deleteDailyDynamicSubmissionUtilityIfexists($dynamicDeleteIds);
				}

				//Dynamic data delete and insert
				//Fixed data delete and insert
				$existingFixedData = $this->import_model->batchSelectFixedUtility($staticFixedCondition);
				$fixedDelete = array();
				foreach ($insertBulkFixedData as $fixedData) {
				    if (isset($existingFixedData[$fixedData['site_id']][$fixedData['year_id']][$fixedData['month_id']][$fixedData['date_id']])) {
					$fixedDelete[] = $existingFixedData[$fixedData['site_id']][$fixedData['year_id']][$fixedData['month_id']][$fixedData['date_id']];
				    }
				}

				$statusInsertFixedUtility = false;
				if (!empty($insertBulkFixedData)) {
				    $statusInsertFixedUtility = $this->import_model->insert_daily_fixed_submission_utilities($insertBulkFixedData, $cdd_hdd_values);
				}

				if (!empty($fixedDelete) && $statusInsertFixedUtility == true) {
				    $deletedStatus = $this->import_model->deleteDailyFixedSubmissionUtilityIfexists($fixedDelete);
				}
				//Fixed data delete and insert
			    }
			}

			// echo 'Daily Submetering End @ '.date('Y-n-j h:i:sa').'<br/>';
			/* Dialy submission End */

			/* Dialy Hourly Start */
			// echo 'Daily Hourly start @ '.date('Y-n-j h:i:sa').'<br/>';

			/*$totalRows = $worksheetData[$HOURLY_INDEX]['totalRows'];
			$sheetName = $worksheetData[$HOURLY_INDEX]['worksheetName'];

			if ($totalRows > 1) {
			    $colmuns = array();
			    $colmuns['Site Name'] = "site_id";
			    $colmuns['Time'] = "hour";
			    $colmuns['Day'] = "date_id";
			    $colmuns['Month'] = "month_id";
			    $colmuns['Year'] = "year_id";

			    // Convert data with database name mapping to use only configured columns to use
			    $importData = array();
			    $siteNames = array();
			    $dataCells = $objPHPExcel->getSheet($HOURLY_INDEX)->toArray();
			    $titleCells = $dataCells[0];

			    foreach ($dataCells as $key => $cell) {

				if ($key == 0) {
				    // For titles
				    continue;
				}
				$iData = array();

				foreach ($titleCells as $key => $value) {
				    $value = trim($value);

				    if ($colmuns[$value] == 'site_id') {
					$siteNames[] = $cell[$key];
				    }

				    // Check if utility enabled in site config
				    if (isset($colmuns[$value])) {
					if ($cell[$key] != null) {
					    $iData[$colmuns[$value]] = $cell[$key];
					} else {
					    $iData[$colmuns[$value]] = '';
					}
				    }
				}
				if (!empty($iData)) {
				    $importData[] = $iData;
				}
			    }

			    $siteNames = array_values(array_unique($siteNames));
			    $fields = "id,site_location_name,is_hourly";
			    $allSiteids = $this->import_model->getSiteDetailByName($siteNames, $fields);

			    if (!empty($importData)) {
				$selectCondition = array();
				foreach ($importData as $key => $value) {
				    if (!empty($value)) {
					$value['site_id'] = trim($value['site_id']);
					if (isset($allSiteids[$value['site_id']])) {
					    $is_hourly = $allSiteids[$value['site_id']]['is_hourly'];
					    $value['site_id'] = $allSiteids[$value['site_id']]['id'];

					    // 0 => half hourly 1 => hourly
					    $value['is_half_hourly'] = (intval($is_hourly) == 1) ? 0 : 1;
					    if (!empty($value['date_id'])) {
						$selectCondition[$value['site_id']] = array('month_id' => $value['month_id'], 'year_id' => $value['year_id'], 'date_id' => $value['date_id']);
						if (preg_match('/^((?:[01][0-9]|2[0-3])|(?:[0-9]|2[0-3])):[0-5][0-9] (AM|PM)$/', $value['hour'])) {
						    $bulkInsertHourlyData[] = $value;
						}
					    }
					}
				    }
				}

				// Select existing hourly utilities
				$fields = "";
				$extingHourlyData = $this->import_model->selectBatchOfHourlyUtilities($selectCondition, $fields);

				if (!empty($bulkInsertHourlyData)) {
				    $statusInsertUtility = $this->import_model->insert_hourly_utilities($bulkInsertHourlyData, $cdd_hdd_values);
				}
			    }

			    $process = true;
			}*/

			// echo 'Daily Hourly end @ '.date('Y-n-j h:i:sa').'<br/>';
			// echo 'Daily Half Hourly Start @ '.date('Y-n-j h:i:sa').'<br/>';
			// Hourly submission starts here
			/*$totalRows = $worksheetData[$HALF_HOURLY_INDEX]['totalRows'];
			$sheetName = $worksheetData[$HALF_HOURLY_INDEX]['worksheetName'];

			if ($totalRows > 1) {
			    // Static columns for site
			    $staticColmuns = array();
			    $staticColmuns['Day'] = "date_id";
			    $staticColmuns['Month'] = "month_id";
			    $staticColmuns['Year'] = "year_id";
			    $staticColmuns['Time'] = "hour";

			    // Convert data with database name mapping to use only configured columns to use
			    $fields = 'id,is_hourly,site_location_name';
			    $allSiteids = $this->import_model->getSiteDetailByName(array(), $fields);
			    $allDailySubmissionDetails = $this->sites_model->getAllHourlyReadingSettings();
			    // $allDailySubmissionDetails = $this->sites_model->get_all_hourly_reading_settings();

			    $allData = array();
			    $siteNames = array();
			    $dataCells = $objPHPExcel->getSheet($HOURLY_INDEX)->toArray();
			    $titleCells = $dataCells[0];

			    $sites_daily_submission = array();
			    // Arrange submission data site wise
			    if (!empty($allDailySubmissionDetails)) {
				foreach ($allDailySubmissionDetails as $key => $value) {
				    $dynamicTitle = array();
				    if (in_array($value['hourly_title'], $titleCells)) {
					$dynamicTitle['id'] = $value['id'];
					$dynamicTitle['hourly_title'] = $value['hourly_title'];
					$sites_daily_submission[$value['site_id']][] = $dynamicTitle;
				    }
				}
			    }

			    foreach ($dataCells as $key => $cell) {
				if ($key == 0) {
				    // For titles
				    continue;
				}
				$iData = array();

				foreach ($titleCells as $key => $value) {
				    $value = trim($value);

				    if ($cell[$key] != null) {
					$iData[$value] = str_replace('* ', '', $cell[$key]);
				    } else {
					$iData[$value] = '';
				    }
				}

				$iData['site_id'] = $allSiteids[trim($iData['Site Name'])]['id'];

				if (!empty($iData['site_id'])) {
				    $allData[] = $iData;
				}
			    }

			    $mBulkStaticDataInsert = array();
			    $bulkDynamicDataInsert = array();
			    $staticFixedCondition = array();
			    if (!empty($allData)) {
				foreach ($allData as $key => $value) {

				    if (empty($value['Site Name'])) {
					continue;
				    }


				    if (!isset($staticFixedCondition[$value['site_id']])) {
					$staticFixedCondition[$value['site_id']] = array('year_id' => $value['Year'], 'month_id' => $value['Month']);
				    }

				    // Prepare Static data
				    $staticDataInsert = array();
				    $staticDataInsert['site_id'] = $value['site_id'];
				    foreach ($staticColmuns as $key1 => $value1) {
					$staticDataInsert[$value1] = is_numeric($value[$key1]) ? $value[$key1] : 0;
				    }
				    $is_hourly = $allSiteids[$value['site_id']]['is_hourly'];

				    // 0 => half hourly 1 => hourly
				    $staticDataInsert['is_half_hourly'] = ($is_hourly == '1') ? 0 : 1;

				    // Store fixed daily data in database
				    // $this->import_model->delete_hourly_fixed_submission_utility_ifexists($staticDataInsert);
				    $mBulkStaticDataInsert[] = $staticDataInsert;
				    // $statusInsertFixedUtility = $this->import_model->insert_hourly_fixed_submission_utilities($staticDataInsert, $cdd_hdd_values);
				    // Prepare Dynamic data
				    if (isset($sites_daily_submission[$value['site_id']]) && !empty($sites_daily_submission[$value['site_id']])) {

					$dynamicDataInsert = array();
					$dynamicDataInsert['site_id'] = $value['site_id'];

					foreach ($sites_daily_submission[$value['site_id']] as $value2) {

					    $dynamicDataInsert['date_id'] = $staticDataInsert['date_id'];
					    $dynamicDataInsert['month_id'] = $staticDataInsert['month_id'];
					    $dynamicDataInsert['year_id'] = $staticDataInsert['year_id'];
					    $dynamicDataInsert['hour'] = date("H:i:s", strtotime($staticDataInsert['hour']));
					    $dynamicDataInsert['utility_title_id'] = $value2['id'];
					    $dynamicDataInsert['value'] = is_numeric($value[$value2['hourly_title']]) ? $value[$value2['hourly_title']] : 0;
					    $dynamicDataInsert['is_half_hourly'] = $staticDataInsert['is_half_hourly'];
					    // Store dynamic daily data in database
					    // $this->import_model->delete_hourly_dynamic_submission_utility_ifexists($dynamicDataInsert);
					    $bulkDynamicDataInsert[] = $dynamicDataInsert;
					    // $statusInsertDynamicUtility = $this->import_model->insert_hourly_dynamic_submission_utilities($dynamicDataInsert);
					}
				    }
				    unset($staticDataInsert);
				}

				// Static data insert in bulk
				if (!empty($mBulkStaticDataInsert)) {
				    $statusInsertFixedUtility = $this->import_model->insert_hourly_fixed_submission_utilities($mBulkStaticDataInsert, $cdd_hdd_values);
				}

				if (!empty($bulkDynamicDataInsert)) {
				    $statusInsertDynamicUtility = $this->import_model->insert_hourly_dynamic_submission_utilities($bulkDynamicDataInsert);
				}
			    }
			}*/
			// Hourly submission ends here
			// echo 'Daily Half Hourly end @ '.date('Y-n-j h:i:sa').'<br/>';
			/* Dialy Hourly End */
		    } catch (Exception $e) {
			die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
		    }
		}
		if (file_exists($newFile)) {
//                    unlink($newFile);
		}

		if ($process) {
		    // Success
		    $this->theme->set_message("File imported successfully.", 'success');

		    // Save audit trail
		    $site_id = $this->session->userdata[$this->section_name]['site_id'];
		    $user_id = $this->session->userdata[$this->section_name]['user_id'];
		    $data_action = 'Import';
		    saveAuditTrail($user_id, $site_id, 'Import (Daily)', $data_action);
		} else {
		    // Error
		    $this->theme->set_message("Error while importing data.", 'error');
		}
	    }
	}

	$this->theme->set('page_title', lang('import'));
	$this->theme->view($data);
    }

	public function survey()
	{
		ini_set('memory_limit', '-1');
		$data = array();
		if (!empty($this->input->post())) {
			$decimal_places = 2;
			require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';
			$file_tmp = $_FILES['importfile']['tmp_name'];
			$file_name = $_FILES['importfile']['name'];
			$fileType = pathinfo($file_name, PATHINFO_EXTENSION);

			if ($fileType == "") {
				$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
				exit;
			} else if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
				$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
				exit;
			} else {
				$project_actionplan_image_name = "upload_file." . $fileType;
				$newFile = $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_actionplan_image_name;
				$_movestatus = move_uploaded_file($file_tmp, $target_file);

				// Start PHPEXCEL import
				require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';

				if ($_movestatus) {
					$inputFileType = PHPExcel_IOFactory::identify($newFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objReader->setLoadAllSheets();
					$objPHPExcel = $objReader->load($newFile);
					$worksheetData = $objReader->listWorksheetInfo($newFile);
					$numberRow = $worksheetData[0]['totalRows'];
					$numberCol = $worksheetData[0]['totalColumns'];

					$dataCells = $objPHPExcel->getSheet(0)->toArray(); // old method
					$titleCells = $dataCells[0];

					$colmuns['Site Name'] = "site_id";
					$colmuns['Question Identical Number'] = 'order_number';
					$colmuns['Question Text'] = 'question_id';
					$colmuns['Answer Options'] = 'answer_option';
					$colmuns['Answer Text'] = 'question_answer';

					// $k = 0;
					$totalCol = 0;

					for ($i = 1; $i < 2; $i++) {
						for ($j = 1; $j <= $numberCol; $j++) {
							if ($titleCells[$j] != '') {
								$totalCol++;
							}
						}
					}
					// Get all site id for imported data
					$siteNames = array();
					for ($i = 1; $i <= $numberRow; $i++) {
						$siteNames[] = trim(strtolower($dataCells[$i][0]));
					}
					$siteNames = array_filter(array_values(array_unique($siteNames)));

					$fields = "";
					$allSiteids = $this->import_model->getSiteDetailByName($siteNames, $fields);
					for ($i = 1; $i <= $numberRow; $i++) {
						$dataInsert = array();

						if ($dataCells[$i][0] == '') {
							continue;
						}

						$colmuns = array_change_key_case($colmuns, CASE_LOWER);
						$siteId = $allSiteids[trim(ucfirst(strtolower($dataCells[$i][0])))]['id'];
						if ($siteId == '') {
							$sites_name[] = $dataCells[$i][0];
							continue;
						} else {
							$this->Survey_questions_answer_model->site_id = $siteId;
						}
						for ($j = 1; $j <= $totalCol; $j++) {
							if (trim($dataCells[$i][$j]) != '') {
								if ($dataCells[0][$j] == 'Question Identical Number' && !empty($dataCells[$i][1])) {
									$surveyQuestionData = $this->Survey_model->get_question_id($dataCells[$i][1]);
									if (!empty($surveyQuestionData)) {
										$this->Survey_questions_answer_model->question_id = $surveyQuestionData[0]['question_id'];
										if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'radio') {
											if ($dataCells[$i][4] == 'Yes' || $dataCells[$i][4] == 'No' || $dataCells[$i][4] == 'Other') {
												$this->Survey_questions_answer_model->question_answer = $dataCells[$i][4];
											}
										} else if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'dropdown') {
											$datacellsWithoutSpace = preg_replace('/\s+/', '', $dataCells[$i][4]);
											$dropDown = explode('|', $surveyQuestionData[0]['question_options']);
											if (in_array($datacellsWithoutSpace, $dropDown)) {
												$this->Survey_questions_answer_model->question_answer = $datacellsWithoutSpace;
											}
										} else if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'checkbox') {
											$checkbox = explode('|', $surveyQuestionData[0]['question_options']);
											$datacellCheckbox = explode('|', $dataCells[$i][4]);
											$anstext = [];
											foreach ($datacellCheckbox as $value) {
												if (in_array($value, $checkbox)) {
													$anstext[] = $value;
												}
											}
											$this->Survey_questions_answer_model->question_answer = implode('|', $anstext);
										} else if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'multiselect') {
											$multiselect = explode('|', $surveyQuestionData[0]['question_options']);
											$datacellmultiselect = explode('|', $dataCells[$i][4]);
											$anstext = [];
											foreach ($datacellmultiselect as $value) {
												if (in_array($value, $multiselect)) {
													$anstext[] = $value;
												}
											}
											$this->Survey_questions_answer_model->question_answer = implode('|', $anstext);
										} else if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'textarea') {
											$this->Survey_questions_answer_model->question_answer = $dataCells[$i][4];
										} else if ($dataCells[0][4] == 'Answer Text' && $surveyQuestionData[0]['question_type'] === 'textbox') {
											$this->Survey_questions_answer_model->question_answer = $dataCells[$i][4];
										}
									}
								}
							} else {
								continue;
							}
							$statusInsertQuestionAnswer = $this->Survey_questions_answer_model->insert_survey_questions_answer();
						}
						if ($statusInsertQuestionAnswer) {
							$this->theme->set_message("File imported successfully.", 'success');
						}
					}
				}
			}
		}
		$this->theme->set('page_title', lang('import'));
		$this->theme->view($data);
	}

	public function export_monthly_data()
	{
		ob_end_clean();
		ob_start();
		$decimal_places = 2;
	$siteName = '';
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';

	$this->lang->load('sites/sites', 'english'); //get_site_listing_for_users
	$this->load->model('utilities/utilities_model');
	$this->load->model('sites/sites_model');

	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];

	$site_details = $this->sites_model->get_site_listing_for_users($site_id, $role_id, $user_id);

	$this->utilities_model->site_id = $site_id;
	$this->utilities_model->utilities_year = date('Y');

	//get utilities of current year of selected site
	$utility = $this->utilities_model->getSiteUtilityLastTenMonths();

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Excel Report")
	    ->setKeywords("Excel Report");

	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);

	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);

	$electricity_unit = GetSiteUtilityUnitName($site_id,'electricity');
	$fuel_oil_unit = GetSiteUtilityUnitName($site_id,'fuel_oil');
	$lpg_unit = GetSiteUtilityUnitName($site_id,'lpg');
	$water_unit = GetSiteUtilityUnitName($site_id,'water');
	$natural_gas_unit = GetSiteUtilityUnitName($site_id,'natural_gas');
	$district_cooling_unit = GetSiteUtilityUnitName($site_id,'district_cooling');
	$district_heating_unit = GetSiteUtilityUnitName($site_id,'district_heating');

	$columns["site_id"] = 'Site Name';
	$columns["month_id"] = 'Month';
	$columns["year_id"] = 'Year';
	$columns['total_kwh'] = 'Purchased Electricity '.$electricity_unit.'';
	$columns['tariff'] = 'Purchased Electricity Tariff ($/'.$electricity_unit.')';
	$columns["maximum_demand"]                          = 'Maximum Demand KVA/KW';
	$columns["maximum_demand_price"]                    = 'Maximum Demand Tariff ($/KVA || $/KW)';
	$columns["fixed_fees"]                              = 'Fixed Fees Cost ($)';
	$columns["electricity_total_budget"]                = 'Electricity Total Budgeted';
	$columns["electricity_total_budget_cost"]           = 'Electricity Total Budgeted Cost ($)';
	$columns["lpg_kitchen"] = 'LPG Kitchen  ('.$lpg_unit.')';
	$columns["lpg_kitchen_rate"] = 'LPG Kitchen $/'.$lpg_unit.'';
	$columns["lpg_fixed_cost"] = 'LPG Fixed Cost';
	$columns["lpg_total_budget"]                        = 'LPG Total Budgeted';
	$columns["lpg_total_budget_cost"]                   = 'LPG Total Budgeted Cost ($)';
	$columns["district_heating"] = 'District Energy Heating '.$district_heating_unit.'';
	$columns["district_heating_rate"] = 'District Energy Heating $/'.$district_heating_unit.'';
	$columns["district_heating_fixed_cost"]             = 'Heating Fixed Cost';
	$columns["district_heating_total_budget"]           = 'Heating District Energy Total Budgeted';
	$columns["district_heating_total_budget_cost"]      = 'Heating District Energy Total Budgeted Cost ($)';

	$columns["water_utility_supply"] = 'Water Utility Supply ('.$water_unit.') '.$water_unit.'';
	$columns["water_utility_supply_rate"] = 'Water Utility Supply ('.$water_unit.') $/'.$water_unit.'';
	$columns["water_fixed_cost"] = 'Water Fixed Cost';
	$columns["water_total_consumption_budget"]          = 'Water Total Budgeted';
	$columns["water_total_consumption_budget_cost"]     = 'Water Total Budgeted Cost ($)';
	$columns["total_room_night"]                        = 'Room Nights';
	$columns["total_room_night_budget"]                 = 'Room Nights Budget';
	$columns["total_guests_budget"]                     = 'Total Guests Budget';
	$columns["total_guests"]                            = 'Total Guests';
	$columns["total_laundered"]                         = 'Laundry Load';
	$columns["cdd"]                                     = "Cooling Degree Day";
	$columns["hdd"]                                     = "Heating Degree Day";

	$columns['onsite_generators_quantity'] = 'Onsite Generators '.$electricity_unit.'';
	$columns['total_onsite_generators_cost']           = "Onsite Generators Cost ($)";
	$columns["average_pf"]                             = 'Average PF';
	$columns["total_renewable_energy_production"] = 'Total Renewable Energy Production '.$electricity_unit.'';
	$columns["total_renewable_energy_production_cost"] = 'Total Renewable Energy Cost ($)';
	$columns["total_renewable_energy_exported"] = 'Total Renewable Energy Exported '.$electricity_unit.'';
	$columns["total_renewable_energy_exported_cost"] = 'Total Renewable Energy Exported Cost ($)';
	$columns["total_electricity_cost"] = 'Total Electricity Cost';
	$columns["total_electricity_kwh"] = 'Total Electricity '.$electricity_unit.'';
	$columns["fuel_oil_hot_water_boilers"] = 'Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.'';
	$columns["fuel_oil_hot_water_boilers_rate"] = 'Fuel Oil Hot-Water Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.'';
	$columns["fuel_oil_steam_boilers"] = 'Fuel Oil Steam Boilers ('.$fuel_oil_unit.') '.$fuel_oil_unit.'';
	$columns["fuel_oil_steam_boilers_rate"] = 'Fuel Oil Steam Boilers ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.'';
	$columns["fuel_oil_others"] = 'Fuel Oil Others ('.$fuel_oil_unit.') '.$fuel_oil_unit.'';
	$columns["fuel_oil_others_rate"] = 'Fuel Oil Others ('.$fuel_oil_unit.') $/'.$fuel_oil_unit.'';
	$columns['onsite_generators_fuel_oil_quantity'] = 'Onsite Generator Fuel Oil '.$fuel_oil_unit;
	$columns['onsite_generators_fuel_oil_price'] = 'Onsite Generator Fuel Oil $/'.$fuel_oil_unit;
	$columns["fuel_total_budget"] = 'Fuel Oil Total Budgeted';
	$columns["fuel_total_budget_cost"] = 'Fuel Oil Total Budgeted Cost ($)';
	$columns["lpg_hot_water_boilers"] = 'LPG Hot-Water Boilers ('.$lpg_unit.')';
	$columns["lpg_hot_water_boilers_rate"] = 'LPG Hot-Water Boilers $/'.$lpg_unit.'';
	$columns["lpg_steam_boilers"] = 'LPG Steam Boilers  ('.$lpg_unit.')';
	$columns["lpg_steam_boilers_rate"] = 'LPG Steam Boilers $/'.$lpg_unit.'';
	$columns["natural_gas_hot_water_boilers"] = 'Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.'';
	$columns["natural_gas_hot_water_boilers_rate"] = 'Natural Gas Hot-Water Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.'';
	$columns["natural_gas_steam_boilers"] = 'Natural Gas Steam Boilers ('.$natural_gas_unit.') '.$natural_gas_unit.'';
	$columns["natural_gas_steam_boilers_rate"] = 'Natural Gas Steam Boilers ('.$natural_gas_unit.') $/'.$natural_gas_unit.'';
	$columns["natural_gas_kitchen"] = 'Natural Gas Kitchen ('.$natural_gas_unit.') '.$natural_gas_unit.'';
	$columns["natural_gas_kitchen_rate"] = 'Natural Gas Kitchen ('.$natural_gas_unit.') $/'.$natural_gas_unit.'';
	$columns["natural_gas_fixed_cost"] = 'Natural Gas Fixed Cost';
	$columns['onsite_generators_natural_gas_quantity'] = 'Onsite Generators Natural Gas '.$natural_gas_unit;
	$columns['onsite_generators_natural_gas_price'] = 'Onsite Generators Natural Gas $/'.$natural_gas_unit;
	$columns["natural_gas_total_budget"] = 'Natural Gas Total Budgeted';
	$columns["natural_gas_total_budget_cost"] = 'Natural Gas Total Budgeted Cost ($)';
	$columns["district_cooling"] = 'District Energy Cooling '.$district_cooling_unit.'';
	$columns["district_cooling_rate"] = 'District Energy Cooling $/'.$district_cooling_unit.'';
	$columns["district_cooling_total_budget"] = 'Cooling District Energy Total Budgeted';
	$columns["district_cooling_total_budget_cost"] = 'Cooling District Energy Total Budgeted Cost ($)';
	$columns["waste_water"] = 'Wastewater ('.$water_unit.') '.$water_unit.'';
	$columns["waste_water_rate"] = 'Wastewater ('.$water_unit.') $/'.$water_unit.'';
	$columns["water_ro"] = 'Water RO ('.$water_unit.') '.$water_unit.'';
	$columns["water_ro_rate"] = 'Water RO ('.$water_unit.') $/'.$water_unit.'';
	$columns["water_Cisterns"] = 'Water Cisterns ('.$water_unit.') '.$water_unit.'';
	$columns["water_Cisterns_rate"] = 'Water Cisterns ('.$water_unit.') $/'.$water_unit.'';
	$columns["total_fb_services"]           = 'Food Covers';
	$columns["revenue"]                     = 'Revenue';
	$colmuns['total_f_b_sales'] = "Total F and B Sales";
	$columns["district_cooling_fixed_cost"] = 'Cooling Fixed Cost';
	$columns['forex'] = "Forex";

	//adding purchased electricity records to utility record
	foreach ($utility as $key => $utl) {
	    $current_utility = $utl;
	    $this->utilities_model->utilities_month = $utl['month_id'];
	    $this->utilities_model->utilities_year = $utl['year_id'];
	    $electricityTariff = $this->utilities_model->getElectricityTariff();

	    $temp = 0;
	    foreach ($electricityTariff as $single) {
		if ($temp == 0) {
		    $current_utility['tariff'] = round($single['tariff'], $decimal_places);
		    $current_utility['total_kwh'] = round($single['total_kwh'], $decimal_places);
		} else {
		    array_push($columns['total_kwh' . $temp] = 'Purchased Electricity kWh');
		    array_push($columns['tariff' . $temp] = 'Purchased Electricity Tariff ($/kWh)');
		    $current_utility['tariff' . $temp] = round($single['tariff'], $decimal_places);
		    $current_utility['total_kwh' . $temp] = round($single['total_kwh'], $decimal_places);
		}
		$temp++;
	    }
	    $utility[$key] = $current_utility;
	}

	if (!empty($site_detail)) {
	    $export_columns_by_site_utility_flag = array(
		'show_utility_electricity' => array(
		    'total_kwh', 'tariff', 'maximum_demand', 'maximum_demand_price', 'fixed_fees',
		    'electricity_total_budget', 'electricity_total_budget_cost',
		    'onsite_generators_quantity', 'total_onsite_generators_cost', 'average_pf',
		    'total_renewable_energy_production', 'total_renewable_energy_production_cost',
		    'total_electricity_cost', 'total_electricity_kwh',
		),
		'show_utility_lpg' => array(
		    'lpg_kitchen', 'lpg_kitchen_rate', 'lpg_fixed_cost', 'lpg_total_budget', 'lpg_total_budget_cost',
		    'lpg_hot_water_boilers', 'lpg_hot_water_boilers_rate', 'lpg_steam_boilers', 'lpg_steam_boilers_rate',
		),
		'show_utility_district_heating' => array(
		    'district_heating', 'district_heating_rate', 'district_heating_fixed_cost',
		    'district_heating_total_budget', 'district_heating_total_budget_cost',
		),
		'show_utility_water' => array(
		    'water_utility_supply', 'water_utility_supply_rate', 'water_fixed_cost',
		    'water_total_consumption_budget', 'water_total_consumption_budget_cost',
		    'water_ro', 'water_ro_rate', 'water_Cisterns', 'water_Cisterns_rate',
		),
		'show_utility_water_waste' => array('waste_water', 'waste_water_rate'),
		'show_utility_fuel_oil' => array(
		    'fuel_oil_hot_water_boilers', 'fuel_oil_hot_water_boilers_rate',
		    'fuel_oil_steam_boilers', 'fuel_oil_steam_boilers_rate',
		    'fuel_oil_others', 'fuel_oil_others_rate',
		    'onsite_generators_fuel_oil_quantity', 'onsite_generators_fuel_oil_price',
		    'fuel_total_budget', 'fuel_total_budget_cost',
		),
		'show_utility_natural_gas' => array(
		    'natural_gas_hot_water_boilers', 'natural_gas_hot_water_boilers_rate',
		    'natural_gas_steam_boilers', 'natural_gas_steam_boilers_rate',
		    'natural_gas_kitchen', 'natural_gas_kitchen_rate', 'natural_gas_fixed_cost',
		    'onsite_generators_natural_gas_quantity', 'onsite_generators_natural_gas_price',
		    'natural_gas_total_budget', 'natural_gas_total_budget_cost',
		),
		'show_utility_district_cooling' => array(
		    'district_cooling', 'district_cooling_rate', 'district_cooling_total_budget',
		    'district_cooling_total_budget_cost', 'district_cooling_fixed_cost',
		),
	    );
	    foreach ($export_columns_by_site_utility_flag as $flag => $column_keys) {
		if (array_key_exists($flag, $site_detail) && (int) $site_detail[$flag] !== 1) {
		    foreach ($column_keys as $col_key) {
			unset($columns[$col_key]);
		    }
		}
	    }
	    if (array_key_exists('show_utility_electricity', $site_detail) && (int) $site_detail['show_utility_electricity'] !== 1) {
		foreach (array_keys($columns) as $col_key) {
		    if (preg_match('/^(total_kwh|tariff)([1-9]\d*)$/', $col_key)) {
			unset($columns[$col_key]);
		    }
		}
	    }
	}

	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
	    $cells[$key] = $later1 . $later2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
	    if ($later2 == 'Z') {
		if ($flag == 0) {
		    $later1 = 'A';
		    $flag = 1;
		} else {
		    $later1++;
		}
		$later2 = 'A';
	    } else {
		$later2++;
	    }
	}
	$row = 2;

	foreach ($utility as $utl) {

	    foreach ($utl as $key => $val) {
		if (!empty($val)) {
		    $val = round($val, 4);
		}

		if ($key == 'site_id') {
		    $site_id = $val;
		    foreach ($site_details as $site) {

			if ($site['s']['id'] == $site_id) {

			    $val = $site['s']['site_location_name'];
					$siteName = $site['s']['site_location_name'];

			    break;

			}
		    }
		}

		if (array_key_exists($key, $cells)) {
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}


	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$siteName.' - Excel Report.xls"');
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

    public function waste() {


	if (!empty($this->input->post())) {
	    $file_tmp = $_FILES['importfile']['tmp_name'];
	    $file_name = $_FILES['importfile']['name'];
	    $fileType = pathinfo($file_name, PATHINFO_EXTENSION);

	    if ($fileType == "") {
		$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/waste');
		exit;
	    } else if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
		$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/waste');
		exit;
	    } else {
		$TOTAL_INDEX = 0;
		$SUBMETERING_INDEX = 1;
		$HOURLY_INDEX = 2;
		$HALF_HOURLY_INDEX = 3;
		$DJB_DAILY_READINGS_INDEX = 4;
		$process = false;

		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$this->load->model('sites/sites_model');
		$site_detail = $this->sites_model->get_site_detail_custom($site_id);

		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$file = "E:/Shared/HEP/HourlyHEP.xlsx";
		$destinationFile = BASE_PATH_CUSTOM . '/assets/uploads/imported_excels/';
		if (!is_dir($destinationFile)) {
		    mkdir($destinationFile);
		}
		$newFile = $destinationFile .uniqid().'_'.date("Y-n-j").'_'. time() . '.'.$fileType;
		if (move_uploaded_file($file_tmp, $newFile)) {
			$inputFileType = PHPExcel_IOFactory::identify($newFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadAllSheets();
			$objPHPExcel = $objReader->load($newFile);
			$worksheetData = $objReader->listWorksheetInfo($newFile);
			$numberRow = $worksheetData[0]['totalRows'];
			$numberCol = $worksheetData[0]['totalColumns'];

			$keyExists = array('year_id', 'month_id');
			$keyNotExists = array();

			$dataCells = $objPHPExcel->getSheet(0)->toArray();
			$titleCells = $dataCells[0];

			if ($numberRow > 1) {
			    $colmuns = array();
			    $colmuns['Site Name'] = "site_id";
			    $colmuns['Month'] = "month_id";
			    $colmuns['Year'] = "year_id";

			    $colmuns['Total (per Unit) bottles cans'] = 'unit_measure_bottles_cans';

			    $colmuns['Total cost bottles cans'] = 'total_bottles_cans';

			    $colmuns['Total (per Unit) cardboard'] = 'unit_measure_cardboard';

			    $colmuns['Total cost cardboard'] = 'total_cardboard';

			    $colmuns['Total (per Unit) paper'] = 'unit_measure_paper';

			    $colmuns['Total cost paper'] = 'total_paper';

			    $colmuns['Total (per Unit) mixed glass'] = 'unit_measure_mixed_glass';

			    $colmuns['Total cost mixed glass'] = 'total_mixed_glass';

			    $colmuns['Total (per Unit) aluminum'] = 'unit_measure_alluminium';

			    $colmuns['Total cost aluminum'] = 'total_alluminium';

			    $colmuns['Total (per Unit) plastic pet'] = 'unit_measure_pete_plastic_bottles';

			    $colmuns['Total cost plastic pet'] = 'total_pete_plastic_bottles';

			    $colmuns['Total (per Unit) plastic hdpe'] = 'unit_measure_hdpe';

			    $colmuns['Total cost plastic hdpe'] = 'total_hdpe';

			    $colmuns['Total (per Unit) other plastics'] = 'unit_measure_other_plastics';

			    $colmuns['Total cost other plastics'] = 'total_other_plastics';

			    $colmuns['Total (per Unit) bath amenity bottles'] = 'unit_measure_bottled_amenities';

			    $colmuns['Total cost bath amenity bottles'] = 'total_bottled_amenities';

			    $colmuns['Total (per Unit) soap bars'] = 'unit_measure_soap_bars';

			    $colmuns['Total cost soap bars'] = 'total_soap_bars';

			    $colmuns['Total (per Unit) pallet and crates'] = 'unit_measure_palettes_and_crates';

			    $colmuns['Total cost pallet and crates'] = 'total_palettes_and_crates';

			    $colmuns['Total (per Unit) e waste'] = 'unit_measure_e_waste';

			    $colmuns['Total cost e waste'] = 'total_e_waste';

			    $colmuns['Total (per Unit) durable goods'] = 'unit_measure_durable_goods';

			    $colmuns['Total cost durable goods'] = 'total_durable_goods';

			    $colmuns['Total (per Unit) solid food waste'] = 'unit_measure_solid_food_waste';

			    $colmuns['Total cost solid food waste'] = 'total_solid_food_waste';

			    $colmuns['Total (per Unit) leftover food'] = 'unit_measure_leftover_food';

			    $colmuns['Total cost leftover food'] = 'total_leftover_food';

			    $colmuns['Total (per Unit) inedible parts'] = 'unit_measure_inedible_parts';

			    $colmuns['Total cost inedible parts'] = 'total_inedible_parts';

			    $colmuns['Total (per Unit) liquid food waste'] = 'unit_measure_liquid_food_waste';

			    $colmuns['Total cost liquid food waste'] = 'total_liquid_food_waste';

			    $colmuns['Total (per Unit) kitchen grease'] = 'unit_measure_kitchen_grease';

			    $colmuns['Total cost kitchen grease'] = 'total_kitchen_grease';

			    $colmuns['Total (per Unit) liquid hazardous waste'] = 'unit_measure_liquid_hazardous_waste';

			    $colmuns['Total cost liquid hazardous waste'] = 'total_liquid_hazardous_waste';

			    $colmuns['Total (per Unit) other hazardous waste'] = 'unit_measure_other_hazardous_waste';

			    $colmuns['Total cost other hazardous waste'] = 'total_other_hazardous_waste';

			    $colmuns['Total (per Unit) batteries'] = 'unit_measure_batteries';

			    $colmuns['Total cost batteries'] = 'total_batteries';

			    $colmuns['Total (per Unit) light bulbs'] = 'unit_measure_light_bulbs';

			    $colmuns['Total cost light bulbs'] = 'total_light_bulbs';

			    $colmuns['Total (per Unit) light fixtures'] = 'unit_measure_light_fixtures';

			    $colmuns['Total cost light fixtures'] = 'total_light_fixtures';

			    $colmuns['Total (per Unit) textiles'] = 'unit_measure_textiles';

			    $colmuns['Total cost textiles'] = 'total_textiles';

			    $colmuns['Total (per Unit) wood'] = 'unit_measure_wood';

			    $colmuns['Total cost wood'] = 'total_wood';

			    $colmuns['Total (per Unit) building constructions'] = 'unit_measure_building_constructions';

			    $colmuns['Total cost building constructions'] = 'total_building_constructions';

			    $colmuns['Total (per Unit) other'] = 'unit_measure_other';

			    $colmuns['Total cost other'] = 'total_other';

			    $colmuns['Total (per Unit) recyclable'] = 'unit_measure_recycling';

			    $colmuns['Total cost recyclable'] = 'total_recycling';

			    $colmuns['Total (per Unit) commingled recyclables'] = 'unit_measure_commingled_recyclables';

			    $colmuns['Total cost commingled recyclables'] = 'total_commingled_recyclables';

			    $colmuns['Total (per Unit) paper cardboard'] = 'unit_measure_paper_cardboard';

			    $colmuns['Total cost paper cardboard'] = 'total_paper_cardboard';

			    $colmuns['Total (per Unit) mixed metals'] = 'unit_measure_mixed_metals';

			    $colmuns['Total cost mixed metals'] = 'total_mixed_metals';

			    $colmuns['Total (per Unit) plastics'] = 'unit_measure_plastics';

			    $colmuns['Total cost plastics'] = 'total_plastics';

			    $colmuns['Total (per Unit) toiletries and durable goods'] = 'unit_measure_donations';
			    $colmuns['Total cost toiletries and durable goods'] = 'total_donations';

			    $colmuns['Total (per Unit) bath toiletries'] = 'unit_measure_toiletry_donations';

			    $colmuns['Total cost bath toiletries'] = 'total_toiletry_donations';

			    $colmuns['Total (per Unit) biodegradable'] = 'unit_measure_biodegradable';

			    $colmuns['Total cost biodegradable'] = 'total_biodegradable';

			    $colmuns['Total (per Unit) green landscaping waste'] = 'unit_measure_mixed_organic';

			    $colmuns['Total cost green landscaping waste'] = 'total_mixed_organic';

			    $colmuns['Total (per Unit) food waste'] = 'unit_measure_food_waste';

			    $colmuns['Total cost food waste'] = 'total_food_waste';

			    $colmuns['Total (per Unit) general municipal solid waste'] = 'unit_measure_landfill_other';

			    $colmuns['Total cost general municipal solid waste'] = 'total_landfill_other';

			    $colmuns['Total (per Unit) hazardous waste'] = 'unit_measure_hazardous_waste';

			    $colmuns['Total cost hazardous waste'] = 'total_hazardous_waste';

			    $colmuns['Total (per Unit) universal waste'] = 'unit_measure_universal_waste';

			    $colmuns['Total cost universal waste'] = 'total_universal_waste';

			    $colmuns['Total (per Unit) other materials'] = 'unit_measure_other_materials';

			    $colmuns['Total cost other materials'] = 'total_other_materials';

			    $colmuns['Total (per Unit) hazardous and universal waste'] = 'unit_measure_hazardous_and_universal_waste';

			    $colmuns['Total cost hazardous and universal waste'] = 'total_hazardous_and_universal_waste';

			    $colmuns['Total (per Unit) medical waste'] = 'unit_measure_medical_waste';

			    $colmuns['Total cost medical waste'] = 'total_medical_waste';

			    $colmuns['Total (per Unit) tin'] = 'unit_measure_tin';

			    $colmuns['Total cost tin'] = 'total_tin';

			    $sites_name = array();
			    $k = 0;
			    $totalCol = 0;

			    for ($i = 1; $i < 2; $i++) {
				for ($j = 1; $j <= $numberCol; $j++) {
				    if ($titleCells[$j] != '') {
					$totalCol++;
				    }
				}
			    }
			    // Get all site id for imported data
			    $siteNames = array();
			    for ($i = 1; $i <= $numberRow; $i++) {
				$siteNames[] = trim($dataCells[$i][0]);
			    }

			    $siteNames = array_filter(array_values(array_unique($siteNames)));

			    $fields = "";
			    $allSiteids = $this->import_model->getSiteDetailByName($siteNames, $fields);

			    /* Start Of Number of rows */
			    for ($i = 1; $i <= $numberRow; $i++) {
				$this->load->model('sites/site_waste_model','',TRUE);
				$statusInsert = 0;
				$statusInsertUtility = 0;
				$extraData = array();
				$dataInsertTotal = array();
				$dataInsert = array();

				if ($dataCells[$i][0] == '') {
				    continue;
				}

				$siteId = $allSiteids[trim($dataCells[$i][0])]['id'];
				$getMonth = trim($dataCells[$i][1]);
				$getYear = trim($dataCells[$i][2]);

				// Lock utilities update on or before 2022 for all sites
				if($getYear <= 2022 && $this->session->userdata[$this->section_name]['user_id'] != 1) {
				    continue;
				}
				if ($siteId == '' && $getMonth == '' && $getYear == '') {
				    continue;
				} else if ($siteId == '') {
				    $sites_name[] = $dataCells[$i][0];
				    continue;
				} else {
				    $dataInsertTotal[$colmuns[trim($dataCells[0][0])]] = $siteId;
				    $dataInsert[$colmuns[trim($dataCells[0][0])]] = $siteId;
				}
				for ($j = 1; $j <= $totalCol; $j++) {
				    if (trim(strtolower($dataCells[$i][$j])) != '') {
					if (in_array($colmuns[trim(strtolower($dataCells[0][$j]))], $keyExists)) {
					    if (array_key_exists($colmuns[trim(strtolower($dataCells[0][$j]))], $dataInsertTotal)) {
						if (count($extraData[$k]) == 2) {
						    $k++;
						}
						$extraData[$k][$colmuns[trim(strtolower($dataCells[0][$j]))]] = $dataCells[$i][$j];
					    } else {
						$dataInsertTotal[$colmuns[trim(strtolower($dataCells[0][$j]))]] = $dataCells[$i][$j];
					    }
					}
					if (!in_array($colmuns[trim(strtolower($dataCells[0][$j]))], $keyNotExists)) {
					    $key = trim(iconv("UTF-8","ISO-8859-1",$dataCells[0][$j])," \t\n\r\0\x0B\xA0");
					    $dataInsert[$colmuns[$key]] = $dataCells[$i][$j];
					}
				    } else {
					continue;
				    }
				}
				$k++;
				foreach ($dataInsert as $key => $value) {
				    $yearlength = strlen((string)$getYear);
				    if($yearlength == 4 && (int)$getMonth <= 31 && (int)$getMonth >= 1) {
					$this->site_waste_model->$key = is_numeric($value) && is_finite($value) ? $value : 0;
					if(substr($key, 0, 6 ) == "total_") {
					    $name = substr($key, strpos($key, "total_") + 6); //bottles_cans
					    $disposalKey = 'disposal_cost_'.$name;
					    $unitKey = 'unit_measure_'.$name;
					    $unitValue = (isset($this->site_waste_model->$unitKey) && is_numeric($this->site_waste_model->$unitKey) && is_finite($this->site_waste_model->$unitKey)) ? $this->site_waste_model->$unitKey : 0;
					    if(isset($value) && (int)$value != 0 && is_finite($value)) {
						$this->site_waste_model->$disposalKey = (is_numeric(round($value/$unitValue, 2)) && is_finite(round($value/$unitValue, 2))) ? (double)round($value/$unitValue, 2) : 0;
						$dataInsert[$disposalKey] = (is_numeric(round($value/$unitValue, 2)) && is_finite(round($value/$unitValue, 2))) ? (double)round($value/$unitValue, 2) : 0;
					    } else {
						$this->site_waste_model->$disposalKey = 0;
						$dataInsert[$disposalKey] = 0;
					    }
					}
					$this->site_waste_model->user_id = $this->session->userdata[$this->section_name]['user_id'];
					$this->site_waste_model->site_id = (int)$siteId;
					$this->site_waste_model->month_id = (int)$getMonth;
					$this->site_waste_model->year_id = (int)$getYear;
					$dataInsert['user_id'] = (int)$this->session->userdata[$this->section_name]['user_id'];
					$dataInsert['site_id'] = (int)$siteId;
					$dataInsert['month_id'] = (int)$getMonth;
					$dataInsert['year_id'] = (int)$getYear;
					$this->site_waste_model->delete_entry_ifexist($dataInsert);
					$siteName = trim($dataCells[$i][0]);
					$statusInsertUtility = $this->site_waste_model->insert_site_waste();
				    } else {
					continue;
				    }

				}


				if ($statusInsertUtility) {
				    $this->theme->set_message("File imported successfully.", 'success');
				    $success = '1';
				    foreach ($dataInsert as $key => $value) {
					$this->site_waste_model->$key = NULL;
				    }
				}
			    }
			}
			if ($success == '1') {
			    // Save audit trail
			    $site_id = $this->session->userdata[$this->section_name]['site_id'];
			    $user_id = $this->session->userdata[$this->section_name]['user_id'];
			    $data_action = 'Import';
			    saveAuditTrail($user_id, $site_id, 'Import Waste (Monthly) ', $data_action);
			}

			//Create page-title
			$this->theme->set('page_title', lang('import-waste'));
			$this->breadcrumb->add(lang('import-waste'));
			if (!empty($sites_name)) {
			    $site_names = implode(',', $sites_name);
			    $this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');
			}
		}
	    }
	}
	$this->theme->set('page_title', lang('import-waste'));
	$this->breadcrumb->add(lang('import-waste'));
	$this->theme->view($data);
    }

    public function emission() {


	if (!empty($this->input->post())) {
	    $file_tmp = $_FILES['importfile']['tmp_name'];
	    $file_name = $_FILES['importfile']['name'];
	    $fileType = pathinfo($file_name, PATHINFO_EXTENSION);

	    if ($fileType == "") {
		$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission');
		exit;
	    } else if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
		$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission');
		exit;
	    } else {
		$TOTAL_INDEX = 0;
		$SUBMETERING_INDEX = 1;
		$HOURLY_INDEX = 2;
		$HALF_HOURLY_INDEX = 3;
		$DJB_DAILY_READINGS_INDEX = 4;
		$process = false;

		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$this->load->model('sites/sites_model');
		$site_detail = $this->sites_model->get_site_detail_custom($site_id);

		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$file = "E:/Shared/HEP/HourlyHEP.xlsx";
		$destinationFile = BASE_PATH_CUSTOM . '/assets/uploads/imported_excels/';
		if (!is_dir($destinationFile)) {
		    mkdir($destinationFile);
		}
		$newFile = $destinationFile .uniqid().'_'.date("Y-n-j").'_'. time() . '.'.$fileType;
		if (move_uploaded_file($file_tmp, $newFile)) {
			$inputFileType = PHPExcel_IOFactory::identify($newFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadAllSheets();
			$objPHPExcel = $objReader->load($newFile);
			$worksheetData = $objReader->listWorksheetInfo($newFile);
			$numberRow = $worksheetData[0]['totalRows'];
			$numberCol = $worksheetData[0]['totalColumns'];

			$keyExists = array('year_id');
			$keyNotExists = array();

			$dataCells = $objPHPExcel->getSheet(0)->toArray();
			$titleCells = $dataCells[0];

			if ($numberRow > 1) {
			    $colmuns = array();
			    $colmuns['Site Name'] = "site_id";
			    $colmuns['Year'] = "year_id";
			    $colmuns['Electricity Emission Factor'] = 'electricity_emission_factor';
			    $colmuns['Fuel Emission Factor'] = 'fuel_emission_factor';
			    $colmuns['LPG Emission Factor'] = 'lpg_emission_factor';
			    $colmuns['Natural Gas Emission Factor'] = 'natural_gas_emission_factor';
			    $colmuns['District Cooling Emission Factor'] = 'district_cooling_emission_factor';
			    $colmuns['District Heating Emission Factor'] = 'district_heating_emission_factor';
			    $colmuns['Green Electricity %'] = 'electricity_emission_factor_percentage';
			    $sites_name = array();
			    $k = 0;
			    $totalCol = 0;

			    for ($i = 1; $i < 2; $i++) {
				for ($j = 1; $j <= $numberCol; $j++) {
				    if ($titleCells[$j] != '') {
					$totalCol++;
				    }
				}
			    }
			    // Get all site id for imported data
			    $siteNames = array();
			    for ($i = 1; $i <= $numberRow; $i++) {
				$siteNames[] = trim($dataCells[$i][0]);
			    }

			    $siteNames = array_filter(array_values(array_unique($siteNames)));

			    $fields = "";
			    $allSiteids = $this->import_model->getSiteDetailByName($siteNames, $fields);

			    /* Start Of Number of rows */
			    for ($i = 1; $i <= $numberRow; $i++) {
				$statusInsert = 0;
				$statusInsertUtility = 0;
				$extraData = array();
				$dataInsertTotal = array();
				$dataInsert = array();

				if ($dataCells[$i][0] == '') {
				    continue;
				}

				$siteId = $allSiteids[trim($dataCells[$i][0])]['id'];
				$getMonth = trim($dataCells[$i][1]);
				$getYear = trim($dataCells[$i][2]);

				// Lock utilities update on or before 2022 for all sites
				if($getYear <= 2022 && $this->session->userdata[$this->section_name]['user_id'] != 1) {
				    continue;
				}
				if ($siteId == '' && $getMonth == '' && $getYear == '') {
				    continue;
				} else if ($siteId == '') {
				    $sites_name[] = $dataCells[$i][0];
				    continue;
				} else {
				    $dataInsertTotal[$colmuns[trim($dataCells[0][0])]] = $siteId;
				    $dataInsert[$colmuns[trim($dataCells[0][0])]] = $siteId;
				}
				for ($j = 1; $j <= $totalCol; $j++) {
				    if (trim($dataCells[$i][$j]) != '') {
					if (in_array($colmuns[trim($dataCells[0][$j])], $keyExists)) {
					    if (array_key_exists($colmuns[trim($dataCells[0][$j])], $dataInsertTotal)) {
						if (count($extraData[$k]) == 2) {
						    $k++;
						}
						$extraData[$k][$colmuns[trim($dataCells[0][$j])]] = $dataCells[$i][$j];
					    } else {
						$dataInsertTotal[$colmuns[trim($dataCells[0][$j])]] = $dataCells[$i][$j];
					    }
					}
					if (!in_array($colmuns[trim($dataCells[0][$j])], $keyNotExists)) {
					    $key = trim(iconv("UTF-8","ISO-8859-1",$dataCells[0][$j])," \t\n\r\0\x0B\xA0");
					    $dataInsert[$colmuns[$key]] = $dataCells[$i][$j];
					}
				    } else {
					continue;
				    }
				}
				$k++;
				$this->load->model('sites/site_emission_model');
				foreach ($dataInsert as $key => $value) {
				    $this->site_emission_model->{$key} = $value;
				}
				$this->site_emission_model->user_id = $this->session->userdata[$this->section_name]['user_id'];
				$this->site_emission_model->status = (int)1;
				// $dataInsert['user_id'] = (int)$this->session->userdata[$this->section_name]['user_id'];
				$dataInsert['site_id'] = (int)$siteId;
				$dataInsert['year_id'] = (int)$getYear;
				$dataInsert['status'] = (int)1;
				$this->site_emission_model->delete_entry_ifexist($dataInsert);
				$siteName = trim($dataCells[$i][0]);
				$statusInsertUtility = $this->site_emission_model->insert_site_emission($dataInsert);


				if ($statusInsertUtility) {
				    $this->theme->set_message("File imported successfully.", 'success');
				    $success = '1';
				}
			    }
			}
			if ($success == '1') {
			    // Save audit trail
			    $site_id = $this->session->userdata[$this->section_name]['site_id'];
			    $user_id = $this->session->userdata[$this->section_name]['user_id'];
			    $data_action = 'Import';
			    saveAuditTrail($user_id, $site_id, 'Import Site Emission Factors ', $data_action);
			}

			//Create page-title
			$this->theme->set('page_title', lang('import-emission'));
			$this->breadcrumb->add(lang('import-emission'));
			if (!empty($sites_name)) {
			    $site_names = implode(',', $sites_name);
			    $this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');
			}
		}
	    }
	}
	$this->theme->set('page_title', lang('import-emission'));
	$this->breadcrumb->add(lang('import-emission'));
	$this->theme->view($data);
    }

    public function site_data() {
	if (!empty($this->input->post())) {
	    $file_tmp = $_FILES['importfile']['tmp_name'];
	    $file_name = $_FILES['importfile']['name'];
	    $fileType = pathinfo($file_name, PATHINFO_EXTENSION);

	    if ($fileType == "") {
		$this->theme->set_message("Please upload file type with .xls or .xlsx extension.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission');
		exit;
	    } else if ($fileType != "xls" && $fileType != "xlsx" && $fileType != "ods") {
		$this->theme->set_message("File type with .xls or .xlsx extension is allowed.", 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/emission');
		exit;
	    } else {
		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$this->load->model('sites/sites_model');

		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$file = "E:/Shared/HEP/HourlyHEP.xlsx";
		$destinationFile = BASE_PATH_CUSTOM . '/assets/uploads/imported_excels/';
		if (!is_dir($destinationFile)) {
		    mkdir($destinationFile);
		}
		$newFile = $destinationFile .uniqid().'_'.date("Y-n-j").'_'. time() . '.'.$fileType;
		if (move_uploaded_file($file_tmp, $newFile)) {
			$inputFileType = PHPExcel_IOFactory::identify($newFile);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setLoadAllSheets();
			$objPHPExcel = $objReader->load($newFile);
			$worksheetData = $objReader->listWorksheetInfo($newFile);
			$numberRow = $worksheetData[0]['totalRows'];
			$numberCol = $worksheetData[0]['totalColumns'];

			$keyExists = array('year_id');
			$keyNotExists = array();

			$dataCells = $objPHPExcel->getSheet(0)->toArray();
			$titleCells = $dataCells[0];

			if ($numberRow > 1) {
			    $colmuns = array();
			    $colmuns['Site'] = "site_id";
			    $colmuns['Energy Use Intensity (EUI)  - % reduction'] = 'energy_intensity_annual_target';
			    $colmuns['GHG Intensity (GHGI)  - % reduction'] = 'ghg_intensity_annual_target';
			    $colmuns['Water Use Intensity (WUI) - % reduction'] = 'water_intensity_annual_target';
			    $colmuns['Waste Intensity (coming soon)  - % reduction'] = 'waste_intensity_annual_target';
			    $colmuns['Energy Use Intensity (EUI)  -benchmark'] = 'energy_intensity_benchmark_target';
			    $colmuns['GHG Intensity (GHGI)  -benchmark'] = 'ghg_intensity_benchmark_target';
			    $colmuns['Water Use Intensity (WUI) -benchmark'] = 'water_intensity_benchmark_target';
			    $colmuns['Waste Intensity (coming soon)  -benchmark'] = 'waste_intensity_benchmark_target';

			    $sites_name = array();
			    $k = 0;
			    $totalCol = 0;

			    for ($i = 1; $i < 2; $i++) {
				for ($j = 1; $j <= $numberCol; $j++) {
				    if ($titleCells[$j] != '') {
					$totalCol++;
				    }
				}
			    }
			    // Get all site id for imported data
			    $siteNames = array();
			    for ($i = 1; $i <= $numberRow; $i++) {
				$siteNames[] = trim($dataCells[$i][0]);
			    }

			    $siteNames = array_filter(array_values(array_unique($siteNames)));

			    $fields = "";
			    $allSiteids = $this->import_model->getSiteDetailByName($siteNames, $fields);

			    /* Start Of Number of rows */
			    for ($i = 1; $i <= $numberRow; $i++) {
				$statusInsert = 0;
				$statusInsertUtility = 0;
				$extraData = array();
				$dataInsertTotal = array();
				$dataInsert = array();

				if ($dataCells[$i][0] == '') {
				    continue;
				}

				$siteId = $allSiteids[trim($dataCells[$i][0])]['id'];

				if ($siteId == '') {
				    continue;
				} else if ($siteId == '') {
				    $sites_name[] = $dataCells[$i][0];
				    continue;
				} else {
				    $dataInsertTotal[$colmuns[trim($dataCells[0][0])]] = $siteId;
				    $dataInsert[$colmuns[trim($dataCells[0][0])]] = $siteId;
				}
				for ($j = 1; $j <= $totalCol; $j++) {
				    if (trim($dataCells[$i][$j]) != '') {
					if (in_array($colmuns[trim($dataCells[0][$j])], $keyExists)) {
					    if (array_key_exists($colmuns[trim($dataCells[0][$j])], $dataInsertTotal)) {
						if (count($extraData[$k]) == 2) {
						    $k++;
						}
						$extraData[$k][$colmuns[trim($dataCells[0][$j])]] = $dataCells[$i][$j];
					    } else {
						$dataInsertTotal[$colmuns[trim($dataCells[0][$j])]] = $dataCells[$i][$j];
					    }
					}
					if (!in_array($colmuns[trim($dataCells[0][$j])], $keyNotExists)) {
					    $key = trim(iconv("UTF-8","ISO-8859-1",$dataCells[0][$j])," \t\n\r\0\x0B\xA0");
					    $dataInsert[$colmuns[$key]] = str_replace("%","",$dataCells[$i][$j]);
					}
				    } else {
					continue;
				    }
				}
				$k++;
				$this->load->model('sites/sites_model');
				$siteName = trim($dataCells[$i][0]);
				$statusInsertUtility = $this->sites_model->updateTargets($dataInsert);


				if ($statusInsertUtility) {
				    $this->theme->set_message("File imported successfully.", 'success');
				    $success = '1';
				}
			    }
			}
			if ($success == '1') {
			    // Save audit trail
			    $site_id = $this->session->userdata[$this->section_name]['site_id'];
			    $user_id = $this->session->userdata[$this->section_name]['user_id'];
			    $data_action = 'Import';
			    saveAuditTrail($user_id, $site_id, 'Import Site Emission Factors ', $data_action);
			}

			//Create page-title
			$this->theme->set('page_title', lang('import-emission'));
			$this->breadcrumb->add(lang('import-emission'));
			if (!empty($sites_name)) {
			    $site_names = implode(',', $sites_name);
			    $this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');
			}
		}
	    }
	}
	exit("in");
	$this->theme->set('page_title', lang('import-emission'));
	$this->breadcrumb->add(lang('import-emission'));
	$this->theme->view($data);
    }

    public function checkNegative() {
		ini_set('memory_limit', '-1');
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		
		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$this->load->model('sites/sites_model');
		$site_detail = $this->sites_model->get_site_detail_custom($site_id);
		$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
		$worksheetTitles = ['Monthly Utilities','Daily Utilities'];
		for($index=0; $index < 2; $index++) {
			// Add new sheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($index);
			$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
			$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords($worksheetTitles[$index]);
			$objPHPExcel->getActiveSheet()->setTitle($worksheetTitles[$index]);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
			$excelNegativeReport = $columns = [];
			
			$this->load->model('utilities/utilities_model');
			$this->utilities_model->utilities_year = date('Y');
			$this->utilities_model->site_id = 0;
			if($index == 0) {
				$currentYearUTilities = $this->utilities_model->getSiteUtility();
			} else {
				$currentYearUTilities = $this->utilities_model->getSiteUtilityDaily();
			}
	    $this->utilities_model->site_id = $site_id;
	    $i = 0;
	    foreach ($currentYearUTilities as $key => $value) {
		foreach ($value as $elementField => $elementValue) {
		    if(!empty($elementValue) && isset($elementValue) && is_numeric($elementValue) && $elementValue < 0) {
			$this->load->model('sites/sites_model');
			$siteDetail = $this->sites_model->get_site_detail($value['site_id'],1,1);                        
			$excelNegativeReport[$i]['Site Name'] = $siteDetail['site_location_name'];
			$columns['Site Name'] = 'Site Name';
			$excelNegativeReport[$i]['Year'] = $value['year_id'];
			$columns['Year'] = 'Year';
			$excelNegativeReport[$i]['Month'] = $months[$value['month_id']];
			$columns['Month'] = 'Month';
			if($index != 0) {
			    $excelNegativeReport[$i]['Day'] = $value['date_id'];
			    $columns['Day'] = 'Day';
			}
			$excelNegativeReport[$i]['Input Field Name'] = ucwords(str_replace('_', ' ', $elementField));
			$columns['Input Field Name'] = 'Input Field Name';
			$excelNegativeReport[$i]['Input Field Value'] = $elementValue;
			$columns['Input Field Value'] = 'Input Field Value';
			$i++;
		    }
		}
	    }

	    $cells = array();
	    $later1 = "";
	    $later2 = 'A';
	    $flag = 0;
	    foreach ($columns as $key => $column) {
		$objPHPExcel->setActiveSheetIndex($index)->setCellValue($later1 . $later2 . "1", $column);
		$cells[$key] = $later1 . $later2;

		$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
		if ($later2 == 'Z') {
		    if ($flag == 0) {
			$later1 = 'A';
			$flag = 1;
		    } else {
			$later1++;
		    }
		    $later2 = 'A';
		} else {
		    $later2++;
		}
	    }
	    $row = 2;
	    foreach ($excelNegativeReport as $data) {
		foreach ($data as $key => $val) {
		    if (array_key_exists($key, $cells)) {
			$objPHPExcel->setActiveSheetIndex($index)->setCellValue($cells[$key] . $row, $val);
		    }
		}
		$row++;
	    }
	}
	$data_action = 'Export';
	$site_id = $_SESSION['admin']['site_id'];
	$user_id = $_SESSION['admin']['user_id'];
	saveAuditTrail($user_id, $site_id, 'Export Negative Utilities', $data_action);
	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Utility Negative Data.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }

    public function compareUtility() {
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();

	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	$excelCompareReport = $columns = [];
	$this->load->model('utilities/utilities_model');
	$this->utilities_model->utilities_year = date('Y');
	$this->utilities_model->site_id = $site_id;
	$currentYearMonthlyUTilities = $this->utilities_model->getSiteUtility();
	$utilityMonthly = [
	    'electricity' => 'total_electricity_kwh',
	    'fuel_oil' => 'total_fuel_oil',
	    'lpg' => 'total_lpg',
	    'natural_gas' => 'total_natural_gas',
	    'district_heating' => 'district_heating',
	    'district_cooling' => 'district_cooling',
	    'water' => 'water_total_consumption',
	];
	$utilityDaily = [
	    'electricity' => 'total_electricity_kwh',
	    'fuel_oil' => 'total_diesel_fuel',
	    'lpg' => 'total_lpg_consumption',
	    'natural_gas' => 'total_natural_gas_consumption',
	    'district_heating' => 'total_district_heating_consumption',
	    'district_cooling' => 'total_district_cooling_consumption',
	    'water' => 'total_water_consumption',
	];
	$checkDisplay = [
	    'electricity' => 'show_utility_electricity',
	    'fuel_oil' => 'show_utility_fuel_oil',
	    'lpg' => 'show_utility_lpg',
	    'natural_gas' => 'show_utility_natural_gas',
	    'district_heating' => 'show_utility_district_heating',
	    'district_cooling' => 'show_utility_district_cooling',
	    'water' => 'show_utility_water'
	];
	$columns = [
	    'Site Name' => 'Site Name',
	    'Year' => 'Year',
	    'Month' => 'Month',
	    'Utility Name' => 'Utility Name',
	    'Value Daily' => 'Value Daily',
	    'Value Monthly' => 'Value Monthly'
	];
	foreach ($currentYearMonthlyUTilities as $key => $value) {
	    $this->load->model('sites/sites_model');
		$this->sites_model->id = $value['site_id'];
	    $siteDetail = $this->sites_model->get_site_detail($value['site_id'],1,1);
	    foreach ($utilityDaily as $keyUtility => $utility_name) {
		if ($siteDetail[$checkDisplay[$keyUtility]] == 1) {
		    $dailyData = $this->utilities_model->getSiteDailyByMonthlyUtility($value['site_id'],$value['year_id'],$value['month_id'],$utility_name, $keyUtility);
		    if ($value[$utilityMonthly[$keyUtility]] !== $dailyData[$keyUtility]) {
			$excelCompareReport[$i]['Site Name'] = $siteDetail['site_location_name'];
			$excelCompareReport[$i]['Year'] = $value['year_id'];
			$excelCompareReport[$i]['Month'] = $months[$value['month_id']];
			$excelCompareReport[$i]['Utility Name'] = ucwords(str_replace('_', ' ', $keyUtility));
			$excelCompareReport[$i]['Value Daily'] = $dailyData[$keyUtility];
			$excelCompareReport[$i]['Value Monthly'] = $value[$utilityMonthly[$keyUtility]];
			$i++;
		    }
		}
	    }
	}
	// Add new sheet
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Compare Utilities Report');
	$objPHPExcel->getActiveSheet()->setTitle('Compare Utilities Report');
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
	    $cells[$key] = $later1 . $later2;

	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
	    if ($later2 == 'Z') {
		if ($flag == 0) {
		    $later1 = 'A';
		    $flag = 1;
		} else {
		    $later1++;
		}
		$later2 = 'A';
	    } else {
		$later2++;
	    }
	}
	$row = 2;
	foreach ($excelCompareReport as $data) {
	    foreach ($data as $key => $val) {
		if (array_key_exists($key, $cells)) {
		    if($val == 0) {
			$val = '';
		    }
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}

	$data_action = 'Export';
	$site_id = $_SESSION['admin']['site_id'];
	$user_id = $_SESSION['admin']['user_id'];
	saveAuditTrail($user_id, $site_id, 'Export Compare Utilities', $data_action);
	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Compare Utilities Report.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }
}
