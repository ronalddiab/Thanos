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
                'actions' => array('index', 'export', 'daily', 'export_monthly_data'),
                'users'   => array('@'),
            ),
        );
    }

    public function index()
    {
        $data = array();
        if (!empty($this->input->post())) {
            $decimal_places = 2;
            require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';
            $file_tmp  = $_FILES['importfile']['tmp_name'];
            $file_name = $_FILES['importfile']['name'];
            $fileType  = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($fileType == "") {
                $this->theme->set_message("Please upload file type with .xls extension.", 'error');
                redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
                exit;
            } else if ($fileType != "xls") {
                $this->theme->set_message("File type with .xls extension is allowed.", 'error');
                redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
                exit;
            } else {

                $cdd_hdd_values = $this->import_model->getMonthlyCddHddValues();

                // For notifications
                $fieldNamesArray = array(
                    'electricity_tariff',
                    'total_onsite_generators_cost',
                    'total_renewable_energy_production',
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
                    'total_room_night',
                    'total_guests',
                    'total_laundered',
                    'total_fb_services',
                );

                $this->load->model('utilities/utilities_model');

                //$project_actionplan_image_name = "upload_file.".$fileType;
                //$target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_actionplan_image_name;
                //$_movestatus = move_uploaded_file($file_tmp, $target_file);

                $data = new Spreadsheet_Excel_Reader($file_tmp, false);

                $numberRow = $data->sheets[0]['numRows'];
                $numberCol = $data->sheets[0]['numCols'];

                /* Number Of columns define */
                $colmuns['Site Name']                                            = "site_id";
                $colmuns['Purchased Electricity Kwh']                            = "total_kwh";
                $colmuns['Purchased Electricity Tariff ($/Kwh)']                 = "tariff";
                $colmuns['Purchased Electricity Cost ($)']                       = "total_cost";
                $colmuns['Month']                                                = "month_id";
                $colmuns['Year']                                                 = "year_id";
                $colmuns['Maximum Demand KVA/KW']                                = "maximum_demand";
                $colmuns['Maximum Demand Tariff ($/KVA || $/KW)']                = "maximum_demand_price";
                $colmuns['Maximum Demand Cost ($)']                              = "total_maximum_demand";
                $colmuns['Fixed Fees Cost ($)']                                  = "fixed_fees";
                $colmuns['Average PF']                                           = "average_pf";
                $colmuns['Total Renewable Energy Production Kwh']                = "total_renewable_energy_production";
                $colmuns['Onsite Generators Kwh']                                = "onsite_generators_quantity";
                $colmuns['Onsite Generators Cost ($)']                           = "total_onsite_generators_cost";
                $colmuns['Total Electricity Cost']                               = "total_electricity_cost";
                $colmuns['Total Electricity Kwh']                                = "total_electricity_kwh";
                $colmuns['Electricity Total Budgeted']                           = "electricity_total_budget";
                $colmuns['Electricity Total Budgeted Cost ($)']                  = "electricity_total_budget_cost";
                $colmuns['Fuel Oil Hot-Water Boilers (Liters) Liter']            = "fuel_oil_hot_water_boilers";
                $colmuns['Fuel Oil Hot-Water Boilers (Liters) $/Liter']          = "fuel_oil_hot_water_boilers_rate";
                $colmuns['Fuel Oil Steam Boilers (Liters) Liter']                = "fuel_oil_steam_boilers";
                $colmuns['Fuel Oil Steam Boilers (Liters) $/Liter']              = "fuel_oil_steam_boilers_rate";
                $colmuns['Fuel Oil Others (Liters) Liter']                       = "fuel_oil_others";
                $colmuns['Fuel Oil Others (Liters) $/Liter']                     = "fuel_oil_others_rate";
                $colmuns['Fuel Oil Total Budgeted']                              = "fuel_total_budget";
                $colmuns['Fuel Oil Total Budgeted Cost ($)']                     = "fuel_total_budget_cost";
                $colmuns['LPG Hot-Water Boilers (Kg)']                           = "lpg_hot_water_boilers";
                $colmuns['LPG Hot-Water Boilers $/Kg']                           = "lpg_hot_water_boilers_rate";
                $colmuns['LPG Steam Boilers  (Kg)']                              = "lpg_steam_boilers";
                $colmuns['LPG Steam Boilers $/Kg']                               = "lpg_steam_boilers_rate";
                $colmuns['LPG Kitchen  (Kg)']                                    = "lpg_kitchen";
                $colmuns['LPG Kitchen $/Kg']                                     = "lpg_kitchen_rate";
                $colmuns['LPG Total Budgeted']                                   = "lpg_total_budget";
                $colmuns['LPG Total Budgeted Cost ($)']                          = "lpg_total_budget_cost";
                $colmuns['Natural Gas Hot-Water Boilers (m3) m3']                = "natural_gas_hot_water_boilers";
                $colmuns['Natural Gas Hot-Water Boilers (m3) $/m3']              = "natural_gas_hot_water_boilers_rate";
                $colmuns['Natural Gas Steam Boilers (m3) m3']                    = "natural_gas_steam_boilers";
                $colmuns['Natural Gas Steam Boilers (m3) $/m3']                  = "natural_gas_steam_boilers_rate";
                $colmuns['Natural Gas Kitchen (m3) m3']                          = "natural_gas_kitchen";
                $colmuns['Natural Gas Kitchen (m3) $/m3']                        = "natural_gas_kitchen_rate";
                $colmuns['Natural Gas Total Budgeted']                           = "natural_gas_total_budget";
                $colmuns['Natural Gas Total Budgeted Cost ($)']                  = "natural_gas_total_budget_cost";
                $colmuns['District Energy Heating Kwh']                          = "district_heating";
                $colmuns['District Energy Heating $/Kwh']                        = "district_heating_rate";
                $colmuns['Heating District Energy Total Budgeted']               = "district_heating_total_budget";
                $colmuns['Heating District Energy Total Budgeted Cost ($)']      = "district_heating_total_budget_cost";
                $colmuns['District Energy Cooling Kwh']                          = "district_cooling";
                $colmuns['District Energy Cooling $/Kwh']                        = "district_cooling_rate";
                $colmuns['Cooling District Energy Total Budgeted']               = "district_cooling_total_budget";
                $colmuns['Cooling District Energy Total Budgeted Cost ($)']      = "district_cooling_total_budget_cost";
                $colmuns['Water Utility Supply (m3) m3']                         = "water_utility_supply";
                $colmuns['Water Utility Supply (m3) $/m3']                       = "water_utility_supply_rate";
                $colmuns['Wastewater (m3) m3']                                   = "waste_water";
                $colmuns['Wastewater (m3) $/m3']                                 = "waste_water_rate";
                $colmuns['Water Cisterns (m3) m3']                               = "water_Cisterns";
                $colmuns['Water Cisterns (m3) $/m3']                             = "water_Cisterns_rate";
                $colmuns['Water Total Budgeted']                                 = "water_total_consumption_budget";
                $colmuns['Water Total Budgeted Cost ($)']                        = "water_total_consumption_budget_cost";                
                $colmuns['Irrigation Water (m3) m3'] = "water_irrigation";
                $colmuns['Irrigation Water (m3) $/m3'] = "water_irrigation_rate";
                $colmuns['Room Nights']  = "total_room_night";
                $colmuns['Total Guests'] = "total_guests";
                $colmuns['Laundry Load'] = "total_laundered";
                $colmuns['Food Covers']  = "total_fb_services";                
                $colmuns['Revenue']         = "revenue";
                $colmuns['General Waste']   = "operation_general_waste";
                $colmuns['Paper Waste']     = "operation_paper_waste";
                $colmuns['Food Waste']      = "operation_food_waste";
                $colmuns['Cardboard Waste'] = "operation_cardboard_waste";
                $colmuns['Plastic Waste']   = "operation_plastic_waste";
                $colmuns['Glass Waste']     = "operation_glass_waste";
                $colmuns['Recycled Waste']  = "operation_recycled_waste";

                $colmuns['Heating Fixed Cost'] = "district_heating_fixed_cost";
                $colmuns['Cooling Fixed Cost'] = "district_cooling_fixed_cost";
                $colmuns['Forex']              = "forex";
                /* Number Of columns define */

                $keyExists    = array('total_kwh', 'tariff', 'year_id', 'month_id');
                $keyNotExists = array('total_kwh', 'tariff', 'total_cost');
                $sites_name   = array();
                $k            = 0;
                $totalCol     = 0;
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
                $siteNames  = array_values(array_unique($siteNames));
                $allSiteids = $this->import_model->get_site_detail_by_name($siteNames);

                /* Start Of Number of rows*/
                for ($i = 2; $i <= $numberRow; $i++) {
                    $statusInsert        = 0;
                    $statusInsertUtility = 0;
                    $extraData           = array();
                    $dataInsertTotal     = array();
                    $dataInsert          = array();

                    if ($data->sheets[0]['cells'][$i][1] == '') {
                        //$this->theme->set_message("Site do not Exists.", 'error');
                        continue;
                    }

                    // Deprecate
                    /*$siteDetail = $this->import_model->get_siteId(trim($data->sheets[0]['cells'][$i][1]));
                    $siteId = $siteDetail[0]['sites']['id'];*/
                    // Deprecate
                    $siteId = $allSiteids[trim($data->sheets[0]['cells'][$i][1])]['id'];

                    $getMonth = trim($data->sheets[0]['cells'][$i][2]);
                    $getYear  = trim($data->sheets[0]['cells'][$i][3]);
                    if ($siteId == '' && $getMonth == '' && $getYear == '') {
                        continue;
                    } else if ($siteId == '') {
                        $sites_name[] = $data->sheets[0]['cells'][$i][1];
                        continue;
                    } else {
                        $dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;
                        $dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][1])]]      = $siteId;
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

                    $sumTotalElectricity          = 0;
                    $sumTotalPurchasedElectricity = 0;
                    $sumTotalElectricityCost      = 0;

                    $dataInsertTotal['total_cost'] = round($dataInsertTotal['tariff'] * $dataInsertTotal['total_kwh']);
                    $sumTotalElectricity += $dataInsertTotal['total_kwh'];
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
                                'site_id'    => $dataInsert['site_id'],
                                'field_name' => 'electricity_tariff',
                                'month'      => $dataInsert['month_id'],
                                'year'       => $dataInsert['year_id'],
                            );

                            $this->utilities_model->deleteNotification($deleteData);

                        }
                    }

                    $dataInsert['electricity_total_budget'] = (isset($dataInsert['electricity_total_budget'])) ? $dataInsert['electricity_total_budget'] : 0;
                    $dataInsert['electricity_total_budget_cost'] = (isset($dataInsert['electricity_total_budget_cost'])) ? $dataInsert['electricity_total_budget_cost'] : 0;
                    $dataInsert['lpg_hot_water_boilers'] = (isset($dataInsert['lpg_hot_water_boilers'])) ? $dataInsert['lpg_hot_water_boilers'] : 0;
                    $dataInsert['lpg_hot_water_boilers_rate'] = (isset($dataInsert['lpg_hot_water_boilers_rate'])) ? $dataInsert['lpg_hot_water_boilers_rate'] : 0;
                    $dataInsert['lpg_total_budget'] = (isset($dataInsert['lpg_total_budget'])) ? $dataInsert['lpg_total_budget'] : 0;
                    $dataInsert['lpg_total_budget_cost']= (isset($dataInsert['lpg_total_budget_cost'])) ? $dataInsert['lpg_total_budget_cost'] : 0;
                    $dataInsert['district_heating_total_budget'] = (isset($dataInsert['district_heating_total_budget'])) ? $dataInsert['district_heating_total_budget'] : 0;
                    $dataInsert['district_heating_total_budget_cost'] = (isset($dataInsert['district_heating_total_budget_cost'])) ? $dataInsert['district_heating_total_budget_cost'] : 0;
                    $dataInsert['water_total_consumption_budget'] = (isset($dataInsert['water_total_consumption_budget'])) ? $dataInsert['water_total_consumption_budget'] : 0;
                    $dataInsert['water_total_consumption_budget_cost'] = (isset($dataInsert['water_total_consumption_budget_cost'])) ? $dataInsert['water_total_consumption_budget_cost'] : 0;
                    $dataInsert['forex'] = (isset($dataInsert['forex']) && $dataInsert['forex'] != 0) ? $dataInsert['forex'] : 1;
                    $dataInsert['total_maximum_demand'] = (isset($dataInsert['total_maximum_demand'])) ? $dataInsert['total_maximum_demand'] : 0;
                    $dataInsert['total_purchased_electricity'] = (isset($dataInsert['total_purchased_electricity'])) ? $dataInsert['total_purchased_electricity'] : 0;
                    $dataInsert['total_purchased_electricity_cost'] = (isset($dataInsert['total_purchased_electricity_cost'])) ? $dataInsert['total_purchased_electricity_cost'] : 0;
                    $dataInsert['average_purchased_electricity'] = (isset($dataInsert['average_purchased_electricity'])) ? $dataInsert['average_purchased_electricity'] : 0;
                    $dataInsert['total_electricity_kwh'] = (isset($dataInsert['total_electricity_kwh'])) ? $dataInsert['total_electricity_kwh'] : 0;
                    $dataInsert['total_electricity_cost'] = (isset($dataInsert['total_electricity_cost'])) ? $dataInsert['total_electricity_cost'] : 0;
                    $dataInsert['average_cost_per_kwh'] = (isset($dataInsert['average_cost_per_kwh'])) ? $dataInsert['average_cost_per_kwh'] : 0;
                    $dataInsert['fuel_oil_hot_water_boilers_cost'] = (isset($dataInsert['fuel_oil_hot_water_boilers_cost'])) ? $dataInsert['fuel_oil_hot_water_boilers_cost'] : 0;
                    $dataInsert['fuel_oil_steam_boilers_cost'] = (isset($dataInsert['fuel_oil_steam_boilers_cost'])) ? $dataInsert['fuel_oil_steam_boilers_cost'] : 0;
                    $dataInsert['fuel_oil_others_cost'] = (isset($dataInsert['fuel_oil_others_cost'])) ? $dataInsert['fuel_oil_others_cost'] : 0;
                    $dataInsert['total_fuel_oil'] = (isset($dataInsert['total_fuel_oil'])) ? $dataInsert['total_fuel_oil'] : 0;
                    $dataInsert['total_fuel_oil_rate'] = (isset($dataInsert['total_fuel_oil_rate'])) ? $dataInsert['total_fuel_oil_rate'] : 0;
                    $dataInsert['total_fuel_oil_cost'] = (isset($dataInsert['total_fuel_oil_cost'])) ? $dataInsert['total_fuel_oil_cost'] : 0;
                    $dataInsert['lpg_hot_water_boilers_cost'] = (isset($dataInsert['lpg_hot_water_boilers_cost'])) ? $dataInsert['lpg_hot_water_boilers_cost'] : 0;
                    $dataInsert['lpg_steam_boilers_cost'] = (isset($dataInsert['lpg_steam_boilers_cost'])) ? $dataInsert['lpg_steam_boilers_cost'] : 0;
                    $dataInsert['lpg_kitchen_cost'] = (isset($dataInsert['lpg_kitchen_cost'])) ? $dataInsert['lpg_kitchen_cost'] : 0;
                    $dataInsert['total_lpg'] = (isset($dataInsert['total_lpg'])) ? $dataInsert['total_lpg'] : 0;
                    $dataInsert['total_lpg_rate'] = (isset($dataInsert['total_lpg_rate'])) ? $dataInsert['total_lpg_rate'] : 0;
                    $dataInsert['total_lpg_cost'] = (isset($dataInsert['total_lpg_cost'])) ? $dataInsert['total_lpg_cost'] : 0;
                    $dataInsert['natural_gas_hot_water_boilers_cost'] = (isset($dataInsert['natural_gas_hot_water_boilers_cost'])) ? $dataInsert['natural_gas_hot_water_boilers_cost'] : 0;
                    $dataInsert['natural_gas_steam_boilers_cost'] = (isset($dataInsert['natural_gas_steam_boilers_cost'])) ? $dataInsert['natural_gas_steam_boilers_cost'] : 0;
                    $dataInsert['natural_gas_kitchen_cost'] = (isset($dataInsert['natural_gas_kitchen_cost'])) ? $dataInsert['natural_gas_kitchen_cost'] : 0;
                    $dataInsert['total_natural_gas'] = (isset($dataInsert['total_natural_gas'])) ? $dataInsert['total_natural_gas'] : 0;
                    $dataInsert['total_natural_gas_rate'] = (isset($dataInsert['total_natural_gas_rate'])) ? $dataInsert['total_natural_gas_rate'] : 0;
                    $dataInsert['total_natural_gas_cost'] = (isset($dataInsert['total_natural_gas_cost'])) ? $dataInsert['total_natural_gas_cost'] : 0;
                    $dataInsert['district_heating_cost'] = (isset($dataInsert['district_heating_cost'])) ? $dataInsert['district_heating_cost'] : 0;
                    $dataInsert['district_cooling_cost'] = (isset($dataInsert['district_cooling_cost'])) ? $dataInsert['district_cooling_cost'] : 0;
                    $dataInsert['water_utility_supply_cost'] = (isset($dataInsert['water_utility_supply_cost'])) ? $dataInsert['water_utility_supply_cost'] : 0;
                    $dataInsert['waste_water_cost'] = (isset($dataInsert['waste_water_cost'])) ? $dataInsert['waste_water_cost'] : 0;
                    $dataInsert['water_Cisterns_cost'] = (isset($dataInsert['water_Cisterns_cost'])) ? $dataInsert['water_Cisterns_cost'] : 0;
                    $dataInsert['water_irrigation_cost'] = (isset($dataInsert['water_irrigation_cost'])) ? $dataInsert['water_irrigation_cost'] : 0;
                    $dataInsert['water_total_consumption'] = (isset($dataInsert['water_total_consumption'])) ? $dataInsert['water_total_consumption'] : 0;
                    $dataInsert['operation_recycled_waste'] = (isset($dataInsert['operation_recycled_waste'])) ? $dataInsert['operation_recycled_waste'] : 0;
                    $dataInsert['water_total_consumption_cost'] = (isset($dataInsert['water_total_consumption_cost'])) ? $dataInsert['water_total_consumption_cost'] : 0;
                    $dataInsert['water_total_consumption_rate'] = (isset($dataInsert['water_total_consumption_rate'])) ? $dataInsert['water_total_consumption_rate'] : 0;
                    $dataInsert['water_utility_supply_rate'] = (isset($dataInsert['water_utility_supply_rate'])) ? $dataInsert['water_utility_supply_rate'] : 0;
                    $dataInsert['water_utility_supply'] = (isset($dataInsert['water_utility_supply'])) ? $dataInsert['water_utility_supply'] : 0;
                    $dataInsert['waste_water'] = (isset($dataInsert['waste_water'])) ? $dataInsert['waste_water'] : 0;
                    $dataInsert['waste_water_rate'] = (isset($dataInsert['waste_water_rate'])) ? $dataInsert['waste_water_rate'] : 0;
                    $dataInsert['water_Cisterns'] = (isset($dataInsert['water_Cisterns'])) ? $dataInsert['water_Cisterns'] : 0;
                    $dataInsert['water_Cisterns_rate'] = (isset($dataInsert['water_Cisterns_rate'])) ? $dataInsert['water_Cisterns_rate'] : 0;
                    $dataInsert['water_irrigation'] = (isset($dataInsert['water_irrigation'])) ? $dataInsert['water_irrigation'] : 0;
                    $dataInsert['water_irrigation_rate'] = (isset($dataInsert['water_irrigation_rate'])) ? $dataInsert['water_irrigation_rate'] : 0;
                    $dataInsert['maximum_demand'] = (isset($dataInsert['maximum_demand'])) ? $dataInsert['maximum_demand'] : 0;
                    $dataInsert['maximum_demand_price'] = (isset($dataInsert['maximum_demand_price'])) ? $dataInsert['maximum_demand_price'] : 0;
                    $dataInsert['total_electricity_kwh'] = (isset($dataInsert['total_electricity_kwh'])) ? $dataInsert['total_electricity_kwh'] : 0;
                    $dataInsert['onsite_generators_quantity'] = (isset($dataInsert['onsite_generators_quantity'])) ? $dataInsert['onsite_generators_quantity'] : 0;
                    $dataInsert['total_renewable_energy_production'] = (isset($dataInsert['total_renewable_energy_production'])) ? $dataInsert['total_renewable_energy_production'] : 0;


                    $dataInsert['total_maximum_demand'] = round($dataInsert['maximum_demand'] * $dataInsert['maximum_demand_price']);

                    $dataInsert['total_purchased_electricity']      = $sumTotalElectricity;
                    $dataInsert['total_purchased_electricity_cost'] = $sumTotalElectricityCost;

                    $dataInsert['average_purchased_electricity'] = ($sumTotalElectricity != 0) ? $sumTotalElectricityCost / $sumTotalElectricity : 0;
                    $dataInsert['average_purchased_electricity'] = round($dataInsert['average_purchased_electricity'], $decimal_places);

                    $dataInsert['total_electricity_kwh'] = $dataInsert['total_purchased_electricity'] + $dataInsert['onsite_generators_quantity'] + $dataInsert['total_renewable_energy_production'];
                    //$dataInsert['total_electricity_cost'] = $dataInsert['total_purchased_electricity_cost']+$dataInsert['total_onsite_generators_cost'];

                    $dataInsert_total_maximum_demand             = (!empty($dataInsert['total_maximum_demand'])) ? $dataInsert['total_maximum_demand'] : 0;
                    $dataInsert_fixed_fees                       = (!empty($dataInsert['fixed_fees'])) ? $dataInsert['fixed_fees'] : 0;
                    $dataInsert_total_purchased_electricity_cost = (!empty($dataInsert['total_purchased_electricity_cost'])) ? $dataInsert['total_purchased_electricity_cost'] : 0;
                    $dataInsert_total_onsite_generators_cost     = (!empty($dataInsert['total_onsite_generators_cost'])) ? $dataInsert['total_onsite_generators_cost'] : 0;
                    $dataInsert['total_electricity_cost']        = $dataInsert_total_maximum_demand + $dataInsert_fixed_fees + $dataInsert_total_purchased_electricity_cost + $dataInsert_total_onsite_generators_cost;
                    $dataInsert['total_purchased_electricity_cost'] += $dataInsert_fixed_fees;

                    $dataInsert['average_cost_per_kwh'] = ($dataInsert['total_electricity_kwh'] != 0) ? $dataInsert['total_electricity_cost'] / $dataInsert['total_electricity_kwh'] : 0;
                    $dataInsert['average_cost_per_kwh'] = round($dataInsert['average_cost_per_kwh'], $decimal_places);

                    $dataInsert['fuel_oil_hot_water_boilers_cost'] = round($dataInsert['fuel_oil_hot_water_boilers_rate'] * $dataInsert['fuel_oil_hot_water_boilers']);
                    $dataInsert['fuel_oil_steam_boilers_cost']     = round($dataInsert['fuel_oil_steam_boilers_rate'] * $dataInsert['fuel_oil_steam_boilers']);
                    $dataInsert['fuel_oil_others_cost']            = round($dataInsert['fuel_oil_others_rate'] * $dataInsert['fuel_oil_others']);

                    $dataInsert['total_fuel_oil']      = $dataInsert['fuel_oil_hot_water_boilers'] + $dataInsert['fuel_oil_steam_boilers'] + $dataInsert['fuel_oil_others'];
                    $dataInsert['total_fuel_oil_rate'] = $dataInsert['fuel_oil_hot_water_boilers_rate'] + $dataInsert['fuel_oil_steam_boilers_rate'] + $dataInsert['fuel_oil_others_rate'];
                    $dataInsert['total_fuel_oil_cost'] = $dataInsert['fuel_oil_hot_water_boilers_cost'] + $dataInsert['fuel_oil_steam_boilers_cost'] + $dataInsert['fuel_oil_others_cost'];

                    $dataInsert['lpg_hot_water_boilers_cost'] = round($dataInsert['lpg_hot_water_boilers'] * $dataInsert['lpg_hot_water_boilers_rate']);
                    $dataInsert['lpg_steam_boilers_cost']     = round($dataInsert['lpg_steam_boilers'] * $dataInsert['lpg_steam_boilers_rate']);
                    $dataInsert['lpg_kitchen_cost']           = round($dataInsert['lpg_kitchen'] * $dataInsert['lpg_kitchen_rate']);

                    $dataInsert['total_lpg']      = $dataInsert['lpg_hot_water_boilers'] + $dataInsert['lpg_steam_boilers'] + $dataInsert['lpg_kitchen'];
                    $dataInsert['total_lpg_rate'] = $dataInsert['lpg_hot_water_boilers_rate'] + $dataInsert['lpg_steam_boilers_rate'] + $dataInsert['lpg_kitchen_rate'];
                    $dataInsert['total_lpg_cost'] = $dataInsert['lpg_hot_water_boilers_cost'] + $dataInsert['lpg_steam_boilers_cost'] + $dataInsert['lpg_kitchen_cost'];

                    $dataInsert['natural_gas_hot_water_boilers_cost'] = $dataInsert['natural_gas_hot_water_boilers'] * $dataInsert['natural_gas_hot_water_boilers_rate'];
                    $dataInsert['natural_gas_steam_boilers_cost']     = $dataInsert['natural_gas_steam_boilers'] * $dataInsert['natural_gas_steam_boilers_rate'];
                    $dataInsert['natural_gas_kitchen_cost']           = $dataInsert['natural_gas_kitchen'] * $dataInsert['natural_gas_kitchen_rate'];

                    $dataInsert['total_natural_gas']      = $dataInsert['natural_gas_hot_water_boilers'] + $dataInsert['natural_gas_steam_boilers'] + $dataInsert['natural_gas_kitchen'];
                    $dataInsert['total_natural_gas_rate'] = $dataInsert['natural_gas_hot_water_boilers_rate'] + $dataInsert['natural_gas_steam_boilers_rate'] + $dataInsert['natural_gas_kitchen_rate'];
                    $dataInsert['total_natural_gas_cost'] = $dataInsert['natural_gas_hot_water_boilers_cost'] + $dataInsert['natural_gas_steam_boilers_cost'] + $dataInsert['natural_gas_kitchen_cost'];

                    $dataInsert['district_heating_cost'] = round($dataInsert['district_heating'] * $dataInsert['district_heating_rate']);
                    $dataInsert['district_cooling_cost'] = round($dataInsert['district_cooling'] * $dataInsert['district_cooling_rate']);

                    $dataInsert['water_utility_supply_cost'] = round($dataInsert['water_utility_supply'] * $dataInsert['water_utility_supply_rate']);
                    $dataInsert['waste_water_cost']          = ((isset($dataInsert['waste_water'])) && (isset($dataInsert['waste_water_rate']))) ? round($dataInsert['waste_water'] * $dataInsert['waste_water_rate']) : 0;
                    $dataInsert['water_Cisterns_cost']       = ((isset($dataInsert['water_Cisterns'])) && (isset($dataInsert['water_Cisterns_rate']))) ? round($dataInsert['water_Cisterns'] * $dataInsert['water_Cisterns_rate']) : 0;
                    $dataInsert['water_irrigation_cost']  =   ((isset($dataInsert['water_irrigation'])) && (isset($dataInsert['water_irrigation_rate']))) ? round($dataInsert['water_irrigation'] * $dataInsert['water_irrigation_rate']) : 0;

                    $dataInsert['water_total_consumption']   = $dataInsert['water_utility_supply'] + $dataInsert['water_Cisterns'] + $dataInsert['water_irrigation'];
                    //by hp18

                    $dataInsert['operation_recycled_waste']  = $dataInsert['operation_paper_waste'] + $dataInsert['operation_glass_waste'] + $dataInsert['operation_cardboard_waste'] + $dataInsert['operation_plastic_waste'];

                    $dataInsert['water_total_consumption_cost'] = $dataInsert['water_utility_supply_cost'] + $dataInsert['waste_water_cost'] + $dataInsert['water_Cisterns_cost'] + $dataInsert['water_irrigation_cost'];
                    $dataInsert['water_total_consumption_rate'] = ( $dataInsert['water_total_consumption'] != 0) ? round($dataInsert['water_total_consumption_cost'] / $dataInsert['water_total_consumption'], $decimal_places) : 0;

                    /*$dataInsert['total_consumption_breakdown'] = $dataInsert['water_consumption_breakdown_cooling_towers']+$dataInsert['water_consumption_breakdown_boh']+$dataInsert['water_consumption_breakdown_rooms'];*/

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
                            unset($dataInsert['fuel_oil_steam_boilers_cost']);

                            unset($dataInsert['fuel_oil_hot_water_boilers_cost']);
                            unset($dataInsert["fuel_oil_hot_water_boilers"]);
                            unset($dataInsert["fuel_oil_hot_water_boilers_rate"]);

                            unset($dataInsert["fuel_oil_others"]);
                            unset($dataInsert["fuel_oil_others_rate"]);
                            unset($dataInsert['fuel_oil_others_cost']);

                            unset($dataInsert['total_fuel_oil']);
                            unset($dataInsert['total_fuel_oil_rate']);
                            unset($dataInsert['total_fuel_oil_cost']);

                            unset($dataInsert["fuel_total_budget"]);
                            unset($dataInsert["fuel_total_budget_cost"]);

                        }

                        if (!$allSiteids[$siteName]['show_utility_lpg']) {

                            unset($dataInsert["lpg_hot_water_boilers"]);
                            unset($dataInsert["lpg_hot_water_boilers_rate"]);
                            unset($dataInsert['lpg_hot_water_boilers_cost']);

                            unset($dataInsert["lpg_steam_boilers"]);
                            unset($dataInsert["lpg_steam_boilers_rate"]);
                            unset($dataInsert['lpg_steam_boilers_cost']);

                            unset($dataInsert["lpg_kitchen"]);
                            unset($dataInsert["lpg_kitchen_rate"]);
                            unset($dataInsert['lpg_kitchen_cost']);

                            unset($dataInsert['total_lpg']);
                            unset($dataInsert['total_lpg_rate']);
                            unset($dataInsert['total_lpg_cost']);

                            unset($dataInsert["lpg_total_budget"]);
                            unset($dataInsert["lpg_total_budget_cost"]);
                        }

                        if (!$allSiteids[$siteName]['show_utility_water']) {
                            unset($dataInsert["water_utility_supply"]);
                            unset($dataInsert["water_utility_supply_rate"]);
                            unset($dataInsert['water_utility_supply_cost']);

                            unset($dataInsert["waste_water"]);
                            unset($dataInsert["waste_water_rate"]);
                            unset($dataInsert['waste_water_cost']);

                            unset($dataInsert["water_Cisterns"]);
                            unset($dataInsert["water_Cisterns_rate"]);
                            unset($dataInsert['water_Cisterns_cost']);
                            unset($dataInsert['water_total_consumption']);
                            unset($dataInsert['water_total_consumption_rate']);
                            unset($dataInsert['water_total_consumption_cost']);

                            unset($dataInsert["water_total_consumption_budget"]);
                            unset($dataInsert["water_total_consumption_budget_cost"]);

                        }

                        if (!$allSiteids[$siteName]['show_utility_natural_gas']) {

                            unset($dataInsert["natural_gas_hot_water_boilers"]);
                            unset($dataInsert["natural_gas_hot_water_boilers_rate"]);
                            unset($dataInsert['natural_gas_hot_water_boilers_cost']);

                            unset($dataInsert["natural_gas_steam_boilers"]);
                            unset($dataInsert["natural_gas_steam_boilers_rate"]);
                            unset($dataInsert['natural_gas_steam_boilers_cost']);

                            unset($dataInsert["natural_gas_kitchen"]);
                            unset($dataInsert["natural_gas_kitchen_rate"]);
                            unset($dataInsert['natural_gas_kitchen_cost']);

                            unset($dataInsert['total_natural_gas']);
                            unset($dataInsert['total_natural_gas_rate']);
                            unset($dataInsert['total_natural_gas_cost']);

                            unset($dataInsert["natural_gas_total_budget"]);
                            unset($dataInsert["natural_gas_total_budget_cost"]);
                        }

                        if (!$allSiteids[$siteName]['show_utility_district_cooling']) {
                            unset($dataInsert['district_cooling_fixed_cost']);

                            unset($dataInsert["district_cooling"]);
                            unset($dataInsert["district_cooling_rate"]);
                            unset($dataInsert['district_cooling_cost']);

                            unset($dataInsert["district_cooling_total_budget"]);
                            unset($dataInsert["district_cooling_total_budget_cost"]);
                        }

                        if (!$allSiteids[$siteName]['show_utility_district_heating']) {
                            unset($dataInsert['district_heating_fixed_cost']);

                            unset($dataInsert["district_heating"]);
                            unset($dataInsert["district_heating_rate"]);
                            unset($dataInsert['district_heating_cost']);

                            unset($dataInsert["district_heating_total_budget"]);
                            unset($dataInsert["district_heating_total_budget_cost"]);
                        }
                    }
                    $statusInsertUtility = $this->import_model->insert_entity_details($dataInsert, $cdd_hdd_values);

                    foreach ($dataInsert as $key => $value) {
                        if (in_array($key, $fieldNamesArray)) {
                            if (!empty($value)) {
                                $deleteData = array(
                                    'site_id'    => $dataInsert['site_id'],
                                    'field_name' => $key,
                                    'month'      => $dataInsert['month_id'],
                                    'year'       => $dataInsert['year_id'],
                                );

                                $this->utilities_model->deleteNotification($deleteData);
                            }
                        }
                    }

                    if ($statusInsertUtility) {
                        $this->theme->set_message("File imported successfully.", 'success');
                        $success = '1';
                        unlink($target_file);
                    }
                }
                /* End Of Number of rows*/
            }

            if($success == '1')
            {
                // Save audit trail
                $site_id = $this->session->userdata[$this->section_name]['site_id'];
                $user_id = $this->session->userdata[$this->section_name]['user_id'];
                $data_action = 'Import';
                saveAuditTrail($user_id, $site_id, 'Import (Monthly) ', $data_action);
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

        $this->theme->set('page_title', lang('import'));
        $this->theme->view($data);
    }

    public function export()
    {
        require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';
        $file_tmp  = $_FILES['importfile']['tmp_name'];
        $file_name = $_FILES['importfile']['name'];
        $fileType  = pathinfo($file_name, PATHINFO_EXTENSION);
        if ($fileType == "") {
            $this->theme->set_message("Please upload file type with .xls extension.", 'error');
            redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
            exit;
        } else if ($fileType != "xls") {
            $this->theme->set_message("File type with .xls extension is allowed.", 'error');
            redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import');
            exit;
        } else {

            $cdd_hdd_values = $this->import_model->getMonthlyCddHddValues();

            // For notifications
            $fieldNamesArray = array(
                'electricity_tariff',
                'total_onsite_generators_cost',
                'total_renewable_energy_production',
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
                /*'water_consumption_breakdown_cooling_towers',
                'water_consumption_breakdown_rooms',*/
                'total_room_night',
                'total_guests',
                'total_laundered',
                'total_fb_services',
                /*'cdd',
            'hdd',*/
            );

            $this->load->model('utilities/utilities_model');

            //$project_actionplan_image_name = "upload_file.".$fileType;
            //$target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_actionplan_image_name;
            //$_movestatus = move_uploaded_file($file_tmp, $target_file);

            $data = new Spreadsheet_Excel_Reader($file_tmp, false);

            $numberRow = $data->sheets[0]['numRows'];
            $numberCol = $data->sheets[0]['numCols'];

            /* Number Of columns define */
            $colmuns['Site Name']                                            = "site_id";
            $colmuns['Purchased Electricity Kwh']                            = "total_kwh";
            $colmuns['Purchased Electricity Tariff ($/Kwh)']                 = "tariff";
            $colmuns['Purchased Electricity Cost ($)']                       = "total_cost";
            $colmuns['Month']                                                = "month_id";
            $colmuns['Year']                                                 = "year_id";
            $colmuns['Maximum Demand KVA/KW']                                = "maximum_demand";
            $colmuns['Maximum Demand Tariff ($/KVA || $/KW)']                = "maximum_demand_price";
            $colmuns['Maximum Demand Cost ($)']                              = "total_maximum_demand";
            $colmuns['Fixed Fees Cost ($)']                                  = "fixed_fees";
            $colmuns['Average PF']                                           = "average_pf";
            $colmuns['Total Renewable Energy Production Kwh']                = "total_renewable_energy_production";
            $colmuns['Onsite Generators Kwh']                                = "onsite_generators_quantity";
            $colmuns['Onsite Generators Cost ($)']                           = "total_onsite_generators_cost";
            $colmuns['Total Electricity Cost']                               = "total_electricity_cost";
            $colmuns['Total Electricity Kwh']                                = "total_electricity_kwh";
            $colmuns['Electricity Total Budgeted']                           = "electricity_total_budget";
            $colmuns['Electricity Total Budgeted Cost ($)']                  = "electricity_total_budget_cost";
            $colmuns['Fuel Oil Hot-Water Boilers (Liters) Liter']            = "fuel_oil_hot_water_boilers";
            $colmuns['Fuel Oil Hot-Water Boilers (Liters) $/Liter']          = "fuel_oil_hot_water_boilers_rate";
            $colmuns['Fuel Oil Steam Boilers (Liters) Liter']                = "fuel_oil_steam_boilers";
            $colmuns['Fuel Oil Steam Boilers (Liters) $/Liter']              = "fuel_oil_steam_boilers_rate";
            $colmuns['Fuel Oil Others (Liters) Liter']                       = "fuel_oil_others";
            $colmuns['Fuel Oil Others (Liters) $/Liter']                     = "fuel_oil_others_rate";
            $colmuns['Fuel Oil Total Budgeted']                              = "fuel_total_budget";
            $colmuns['Fuel Oil Total Budgeted Cost ($)']                     = "fuel_total_budget_cost";
            $colmuns['LPG Hot-Water Boilers (Kg)']                           = "lpg_hot_water_boilers";
            $colmuns['LPG Hot-Water Boilers $/Kg']                           = "lpg_hot_water_boilers_rate";
            $colmuns['LPG Steam Boilers  (Kg)']                              = "lpg_steam_boilers";
            $colmuns['LPG Steam Boilers $/Kg']                               = "lpg_steam_boilers_rate";
            $colmuns['LPG Kitchen  (Kg)']                                    = "lpg_kitchen";
            $colmuns['LPG Kitchen $/Kg']                                     = "lpg_kitchen_rate";
            $colmuns['LPG Total Budgeted']                                   = "lpg_total_budget";
            $colmuns['LPG Total Budgeted Cost ($)']                          = "lpg_total_budget_cost";
            $colmuns['Natural Gas Hot-Water Boilers (m3) m3']                = "natural_gas_hot_water_boilers";
            $colmuns['Natural Gas Hot-Water Boilers (m3) $/m3']              = "natural_gas_hot_water_boilers_rate";
            $colmuns['Natural Gas Steam Boilers (m3) m3']                    = "natural_gas_steam_boilers";
            $colmuns['Natural Gas Steam Boilers (m3) $/m3']                  = "natural_gas_steam_boilers_rate";
            $colmuns['Natural Gas Kitchen (m3) m3']                          = "natural_gas_kitchen";
            $colmuns['Natural Gas Kitchen (m3) $/m3']                        = "natural_gas_kitchen_rate";
            $colmuns['Natural Gas Total Budgeted']                           = "natural_gas_total_budget";
            $colmuns['Natural Gas Total Budgeted Cost ($)']                  = "natural_gas_total_budget_cost";
            $colmuns['District Energy Heating Kwh']                          = "district_heating";
            $colmuns['District Energy Heating $/Kwh']                        = "district_heating_rate";
            $colmuns['Heating District Energy Total Budgeted']               = "district_heating_total_budget";
            $colmuns['Heating District Energy Total Budgeted Cost ($)']      = "district_heating_total_budget_cost";
            $colmuns['District Energy Cooling Kwh']                          = "district_cooling";
            $colmuns['District Energy Cooling $/Kwh']                        = "district_cooling_rate";
            $colmuns['Cooling District Energy Total Budgeted']               = "district_cooling_total_budget";
            $colmuns['Cooling District Energy Total Budgeted Cost ($)']      = "district_cooling_total_budget_cost";
            $colmuns['Water Utility Supply (m3) m3']                         = "water_utility_supply";
            $colmuns['Water Utility Supply (m3) $/m3']                       = "water_utility_supply_rate";
            $colmuns['Wastewater (m3) m3']                                   = "waste_water";
            $colmuns['Wastewater (m3) $/m3']                                 = "waste_water_rate";
            $colmuns['Water Cisterns (m3) m3']                               = "water_Cisterns";
            $colmuns['Water Cisterns (m3) $/m3']                             = "water_Cisterns_rate";
            $colmuns['Water Total Budgeted']                                 = "water_total_consumption_budget";
            $colmuns['Water Total Budgeted Cost ($)']                        = "water_total_consumption_budget_cost";
          
            /*$colmuns['Water Consumption Breakdown Cooling Towers (m3)'] = "water_consumption_breakdown_cooling_towers";
            $colmuns['Water Consumption Breakdown BOH (m3)'] = "water_consumption_breakdown_boh";
            $colmuns['Water Consumption Breakdown Rooms (m3)'] = "water_consumption_breakdown_rooms";
            $colmuns['Water Consumption Breakdown Total Budgeted'] = "total_consumption_breakdown_budget";
            $colmuns['Water Consumption Breakdown Total Budgeted Cost ($)'] = "total_consumption_breakdown_budget_cost";*/
            $colmuns['Room Nights']  = "total_room_night";
            $colmuns['Total Guests'] = "total_guests";
            $colmuns['Laundry Load'] = "total_laundered";
            $colmuns['Food Covers']  = "total_fb_services";
            /*$colmuns['Cooling Degree Day'] = "cdd";
            $colmuns['Heating Degree Day'] = "hdd";*/
            $colmuns['Revenue']         = "revenue";
            $colmuns['General Waste']   = "operation_general_waste";
            $colmuns['Paper Waste']     = "operation_paper_waste";
            $colmuns['Food Waste']      = "operation_food_waste";
            $colmuns['Cardboard Waste'] = "operation_cardboard_waste";
            $colmuns['Plastic Waste']   = "operation_plastic_waste";
            $colmuns['Glass Waste']     = "operation_glass_waste";
            $colmuns['Recycled Waste']  = "operation_recycled_waste";

            $colmuns['Heating Fixed Cost'] = "district_heating_fixed_cost";
            $colmuns['Cooling Fixed Cost'] = "district_cooling_fixed_cost";
            /* Number Of columns define */

            $keyExists    = array('total_kwh', 'tariff', 'year_id', 'month_id');
            $keyNotExists = array('total_kwh', 'tariff', 'total_cost');
            $sites_name   = array();
            $k            = 0;
            $totalCol     = 0;
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
            $siteNames  = array_values(array_unique($siteNames));
            $allSiteids = $this->import_model->get_site_detail_by_name($siteNames);

            /* Start Of Number of rows*/
            for ($i = 2; $i <= $numberRow; $i++) {
                $statusInsert        = 0;
                $statusInsertUtility = 0;
                $extraData           = array();
                $dataInsertTotal     = array();
                $dataInsert          = array();

                if ($data->sheets[0]['cells'][$i][1] == '') {
                    //$this->theme->set_message("Site do not Exists.", 'error');
                    continue;
                }

                // Deprecate
                /*$siteDetail = $this->import_model->get_siteId(trim($data->sheets[0]['cells'][$i][1]));
                $siteId = $siteDetail[0]['sites']['id'];*/
                // Deprecate

                $siteId = $allSiteids[trim($data->sheets[0]['cells'][$i][1])]['id'];

                $getMonth = trim($data->sheets[0]['cells'][$i][2]);
                $getYear  = trim($data->sheets[0]['cells'][$i][3]);
                if ($siteId == '' && $getMonth == '' && $getYear == '') {
                    continue;
                } else if ($siteId == '') {
                    $sites_name[] = $data->sheets[0]['cells'][$i][1];
                    continue;
                } else {
                    $dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;
                    $dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][1])]]      = $siteId;
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

                $sumTotalElectricity          = 0;
                $sumTotalPurchasedElectricity = 0;
                $sumTotalElectricityCost      = 0;

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
                            'site_id'    => $dataInsert['site_id'],
                            'field_name' => 'electricity_tariff',
                            'month'      => $dataInsert['month_id'],
                            'year'       => $dataInsert['year_id'],
                        );

                        $this->utilities_model->deleteNotification($deleteData);

                    }
                }

                $dataInsert['total_maximum_demand'] = round($dataInsert['maximum_demand'] * $dataInsert['maximum_demand_price']);

                $dataInsert['total_purchased_electricity']      = $sumTotalElectricity;
                $dataInsert['total_purchased_electricity_cost'] = $sumTotalElectricityCost;

                $dataInsert['average_purchased_electricity'] = ($sumTotalElectricity != 0) ? $sumTotalElectricityCost / $sumTotalElectricity : 0;
                $dataInsert['average_purchased_electricity'] = round($dataInsert['average_purchased_electricity'], $decimal_places);

                $dataInsert['total_electricity_kwh'] = $dataInsert['total_purchased_electricity'] + $dataInsert['onsite_generators_quantity'] + $dataInsert['total_renewable_energy_production'];
                //$dataInsert['total_electricity_cost'] = $dataInsert['total_purchased_electricity_cost']+$dataInsert['total_onsite_generators_cost'];

                $dataInsert_total_maximum_demand             = (!empty($dataInsert['total_maximum_demand'])) ? $dataInsert['total_maximum_demand'] : 0;
                $dataInsert_fixed_fees                       = (!empty($dataInsert['fixed_fees'])) ? $dataInsert['fixed_fees'] : 0;
                $dataInsert_total_purchased_electricity_cost = (!empty($dataInsert['total_purchased_electricity_cost'])) ? $dataInsert['total_purchased_electricity_cost'] : 0;
                $dataInsert_total_onsite_generators_cost     = (!empty($dataInsert['total_onsite_generators_cost'])) ? $dataInsert['total_onsite_generators_cost'] : 0;
                $dataInsert['total_electricity_cost']        = $dataInsert_total_maximum_demand + $dataInsert_fixed_fees + $dataInsert_total_purchased_electricity_cost + $dataInsert_total_onsite_generators_cost;

                $dataInsert['average_cost_per_kwh'] = $dataInsert['total_electricity_cost'] / $dataInsert['total_electricity_kwh'];
                $dataInsert['average_cost_per_kwh'] = round($dataInsert['average_cost_per_kwh'], $decimal_places);

                $dataInsert['total_purchased_electricity_cost'] += $dataInsert_fixed_fees;

                $dataInsert['fuel_oil_hot_water_boilers_cost'] = round($dataInsert['fuel_oil_hot_water_boilers_rate'] * $dataInsert['fuel_oil_hot_water_boilers']);
                $dataInsert['fuel_oil_steam_boilers_cost']     = round($dataInsert['fuel_oil_steam_boilers_rate'] * $dataInsert['fuel_oil_steam_boilers']);
                $dataInsert['fuel_oil_others_cost']            = round($dataInsert['fuel_oil_others_rate'] * $dataInsert['fuel_oil_others']);

                $dataInsert['total_fuel_oil']      = $dataInsert['fuel_oil_hot_water_boilers'] + $dataInsert['fuel_oil_steam_boilers'] + $dataInsert['fuel_oil_others'];
                $dataInsert['total_fuel_oil_rate'] = $dataInsert['fuel_oil_hot_water_boilers_rate'] + $dataInsert['fuel_oil_steam_boilers_rate'] + $dataInsert['fuel_oil_others_rate'];
                $dataInsert['total_fuel_oil_cost'] = $dataInsert['fuel_oil_hot_water_boilers_cost'] + $dataInsert['fuel_oil_steam_boilers_cost'] + $dataInsert['fuel_oil_others_cost'];

                $dataInsert['lpg_hot_water_boilers_cost'] = round($dataInsert['lpg_hot_water_boilers'] * $dataInsert['lpg_hot_water_boilers_rate']);
                $dataInsert['lpg_steam_boilers_cost']     = round($dataInsert['lpg_steam_boilers'] * $dataInsert['lpg_steam_boilers_rate']);
                $dataInsert['lpg_kitchen_cost']           = round($dataInsert['lpg_kitchen'] * $dataInsert['lpg_kitchen_rate']);

                $dataInsert['total_lpg']      = $dataInsert['lpg_hot_water_boilers'] + $dataInsert['lpg_steam_boilers'] + $dataInsert['lpg_kitchen'];
                $dataInsert['total_lpg_rate'] = $dataInsert['lpg_hot_water_boilers_rate'] + $dataInsert['lpg_steam_boilers_rate'] + $dataInsert['lpg_kitchen_rate'];
                $dataInsert['total_lpg_cost'] = $dataInsert['lpg_hot_water_boilers_cost'] + $dataInsert['lpg_steam_boilers_cost'] + $dataInsert['lpg_kitchen_cost'];

                $dataInsert['natural_gas_hot_water_boilers_cost'] = $dataInsert['natural_gas_hot_water_boilers'] * $dataInsert['natural_gas_hot_water_boilers_rate'];
                $dataInsert['natural_gas_steam_boilers_cost']     = $dataInsert['natural_gas_steam_boilers'] * $dataInsert['natural_gas_steam_boilers_rate'];
                $dataInsert['natural_gas_kitchen_cost']           = $dataInsert['natural_gas_kitchen'] * $dataInsert['natural_gas_kitchen_rate'];

                $dataInsert['total_natural_gas']      = $dataInsert['natural_gas_hot_water_boilers'] + $dataInsert['natural_gas_steam_boilers'] + $dataInsert['natural_gas_kitchen'];
                $dataInsert['total_natural_gas_rate'] = $dataInsert['natural_gas_hot_water_boilers_rate'] + $dataInsert['natural_gas_steam_boilers_rate'] + $dataInsert['natural_gas_kitchen_rate'];
                $dataInsert['total_natural_gas_cost'] = $dataInsert['natural_gas_hot_water_boilers_cost'] + $dataInsert['natural_gas_steam_boilers_cost'] + $dataInsert['natural_gas_kitchen_cost'];

                $dataInsert['district_heating_cost'] = round($dataInsert['district_heating'] * $dataInsert['district_heating_rate']);
                $dataInsert['district_cooling_cost'] = round($dataInsert['district_cooling'] * $dataInsert['district_cooling_rate']);

                $dataInsert['water_utility_supply_cost'] = round($dataInsert['water_utility_supply'] * $dataInsert['water_utility_supply_rate']);
                $dataInsert['waste_water_cost']          = round($dataInsert['waste_water'] * $dataInsert['waste_water_rate']);
                $dataInsert['water_Cisterns_cost']       = round($dataInsert['water_Cisterns'] * $dataInsert['water_Cisterns_rate']);

                $dataInsert['water_total_consumption'] = $dataInsert['water_utility_supply'] + $dataInsert['water_Cisterns'];
                //by hp18
                $dataInsert['operation_recycled_waste']     = $dataInsert['operation_paper_waste'] + $dataInsert['operation_glass_waste'] + $dataInsert['operation_cardboard_waste'] + $dataInsert['operation_plastic_waste'];
                $dataInsert['water_total_consumption_cost'] = $dataInsert['water_utility_supply_cost'] + $dataInsert['waste_water_cost'] + $dataInsert['water_Cisterns_cost'];
                $dataInsert['water_total_consumption_rate'] = round($dataInsert['water_total_consumption_cost'] / $dataInsert['water_total_consumption'], 2);

                /*$dataInsert['total_consumption_breakdown'] = $dataInsert['water_consumption_breakdown_cooling_towers']+$dataInsert['water_consumption_breakdown_boh']+$dataInsert['water_consumption_breakdown_rooms'];*/

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
                        unset($dataInsert['lpg_hot_water_boilers_cost']);
                        unset($dataInsert['lpg_steam_boilers_cost']);
                        unset($dataInsert['lpg_kitchen_cost']);
                        unset($dataInsert['total_lpg']);
                        unset($dataInsert['total_lpg_rate']);
                        unset($dataInsert['total_lpg_cost']);
                    }

                    if (!$allSiteids[$siteName]['show_utility_water']) {
                        unset($dataInsert['water_utility_supply_cost']);
                        unset($dataInsert['waste_water_cost']);
                        unset($dataInsert['water_Cisterns_cost']);
                        unset($dataInsert['water_total_consumption']);
                        unset($dataInsert['water_total_consumption_rate']);
                        unset($dataInsert['water_total_consumption_cost']);
                    }

                    if (!$allSiteids[$siteName]['show_utility_natural_gas']) {
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
                /*if($dataInsert['month_id'] == '3' && $dataInsert['year_id'] == '2017'){
                echo '<pre>';print_r($dataInsert);exit;
                }*/

                $statusInsertUtility = $this->import_model->insert_entity_details($dataInsert, $cdd_hdd_values);

                foreach ($dataInsert as $key => $value) {
                    if (in_array($key, $fieldNamesArray)) {
                        if (!empty($value)) {
                            $deleteData = array(
                                'site_id'    => $dataInsert['site_id'],
                                'field_name' => $key,
                                'month'      => $dataInsert['month_id'],
                                'year'       => $dataInsert['year_id'],
                            );

                            $this->utilities_model->deleteNotification($deleteData);
                        }
                    }
                }
                if ($statusInsertUtility) {
                    $this->theme->set_message("File imported successfully.", 'success');
                    unlink($target_file);
                }
            }
            /* End Of Number of rows*/
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
        if (!empty($this->input->post())) {
            $file_tmp  = $_FILES['importfile']['tmp_name'];
            $file_name = $_FILES['importfile']['name'];
            $fileType  = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($fileType == "") {
                $this->theme->set_message("Please upload file type with .xls extension.", 'error');
                redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/daily');
                exit;
            } else if ($fileType != "xls") {
                $this->theme->set_message("File type with .xls extension is allowed.", 'error');
                redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'import/daily');
                exit;
            } else {
                $process = false;
                require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';

                $excelData = new Spreadsheet_Excel_Reader($file_tmp, false);
                
                if (!empty($excelData)) {

                    // Get daily cdd and hdd values
                    $cdd_hdd_values = $this->import_model->getDailyCddHddValues();

                    /******************************************************************/
                    /* Dialy start
                    /******************************************************************/
                    
                    // $data = $excelData->sheets[0];

                    // if ($data['numRows'] > 1) {

                    //     // Map excel columns with database keys
                    //     $colmuns                                   = array();
                    //     $colmuns['Site Name']                      = "site_id";
                    //     $colmuns['Day']                            = "date_id";
                    //     $colmuns['Month']                          = "month_id";
                    //     $colmuns['Year']                           = "year_id";
                    //     $colmuns['Total Electricity Kwh']          = "total_electricity_kwh";
                    //     $colmuns['Electricity Tariff ($/Kwh)']     = "total_electricity_kwh_tariff";
                    //     $colmuns['Total Fuel Oil  (Liters)']       = "total_diesel_fuel";
                    //     $colmuns['Fuel Oil  $/Liter']              = "total_diesel_fuel_tariff";
                    //     $colmuns['LPG (Kg)']                       = "total_lpg_consumption";
                    //     $colmuns['LPG $/Kg']                       = "total_lpg_consumption_tariff";
                    //     $colmuns['Natual Gas (m3)']                = "total_natural_gas_consumption";
                    //     $colmuns['Natural Gas $/m3']               = "total_natural_gas_consumption_tariff";
                    //     $colmuns['District Energy Heating Kwh']    = "total_district_heating_consumption";
                    //     $colmuns['District Energy Heating $/Kwh']  = "total_district_heating_consumption_tariff";
                    //     $colmuns['District Energy Cooling Kwh']    = "total_district_cooling_consumption";
                    //     $colmuns['District Energy Cooling $/Kwh']  = "total_district_cooling_consumption_tariff";
                    //     $colmuns['Water Utility Supply (m3) m3']   = "total_water_consumption";
                    //     $colmuns['Water Utility Supply (m3) $/m3'] = "total_water_consumption_tariff";
                    //     $colmuns['Irrigation Water (m3) m3']       = "total_landscape_water_consumption";
                    //     $colmuns['Irrigation water  $/m3']         = "total_landscape_water_consumption_tariff";
                    //     $colmuns['Waste Water (m3) m3']            = "total_waste_water_consumption";
                    //     $colmuns['Waste water $/m3']               = "total_waste_water_consumption_tariff";
                    //     $colmuns['Room Nights']                    = "total_room_night";
                    //     $colmuns['Total Guests']                   = "total_guests";
                    //     //$colmuns['Cooling Degree Day']             = "cdd";
                    //     //$colmuns['Heating Degree Day']             = "hdd";

                    //     // Convert data with database name mapping to use only configured columns to use
                    //     $importData = array();
                    //     $siteNames  = array();
                    //     $titleCells = $data['cells'][1];
                    //     foreach ($data['cells'] as $key => $cells) {
                    //         if ($key == 1) {
                    //             // For titles
                    //             continue;
                    //         }
                    //         $iData = array();
                    //         foreach ($titleCells as $key => $value) {
                    //             $value = trim($value);

                    //             if ($colmuns[$value] == 'site_id') {
                    //                 $siteNames[] = $cells[$key];
                    //             }

                    //             // Check if utility enabled in site config
                    //             if (isset($colmuns[$value])) {
                    //                 if ($cells[$key] != null) {
                    //                     $iData[$colmuns[$value]] = $cells[$key];
                    //                 } else {
                    //                     $iData[$colmuns[$value]] = '';
                    //                 }
                    //             }
                    //         }

                    //         $importData[] = $iData;

                    //     }

                    //     $siteNames  = array_values(array_unique($siteNames));
                    //     $allSiteids = $this->import_model->get_site_detail_by_name($siteNames);
                    //     $batchInsertData = array();
                    //     // Store data in database
                    //     if (!empty($importData)) {
                    //         foreach ($importData as $key => $value) {
                    //             $value['site_id'] = trim($value['site_id']);
                    //             if (isset($allSiteids[$value['site_id']])) {
                    //                 // Remove unused utilities from
                    //                 if (!$allSiteids[$value['site_id']]['show_utility_electricity']) {
                    //                     unset($value["total_electricity_kwh"]);
                    //                     unset($value["total_electricity_kwh_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_fuel_oil']) {
                    //                     unset($value["total_diesel_fuel"]);
                    //                     unset($value["total_diesel_fuel_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_lpg']) {
                    //                     unset($value["total_lpg_consumption"]);
                    //                     unset($value["total_lpg_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_water']) {
                    //                     unset($value["total_water_consumption"]);
                    //                     unset($value["total_water_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_irrigation_water']) {
                    //                     unset($value["total_landscape_water_consumption"]);
                    //                     unset($value["total_landscape_water_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_water_waste']) {
                    //                     unset($value["total_waste_water_consumption"]);
                    //                     unset($value["total_waste_water_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_natural_gas']) {
                    //                     unset($value["total_natural_gas_consumption"]);
                    //                     unset($value["total_natural_gas_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_district_cooling']) {
                    //                     unset($value["total_district_cooling_consumption"]);
                    //                     unset($value["total_district_cooling_consumption_tariff"]);
                    //                 }

                    //                 if (!$allSiteids[$value['site_id']]['show_utility_district_heating']) {
                    //                     unset($value["total_district_heating_consumption"]);
                    //                     unset($value["total_district_heating_consumption_tariff"]);
                    //                 }

                    //                 $value['site_id'] = $allSiteids[$value['site_id']]['id'];
                    //                 if (!empty($value['date_id'])) {
                    //                     // Delete if data already exists
                    //                     $this->import_model->delete_daily_utility_ifexists($value);
                    //                     $batchInsertData[] = $value;
                    //                     // // Insert new data
                    //                     // $statusInsertUtility = $this->import_model->insert_daily_utilities($value, $cdd_hdd_values);
                    //                 }
                    //             }

                    //         }

                    //         // Insert new data
                    //         $statusInsertUtility = $this->import_model->insert_daily_utilities($batchInsertData, $cdd_hdd_values);
                    //     }

                    //     $process = true;
                    // }

                    // ****************************************************************
                    // Dialy End
                    // ****************************************************************

                    // *****************************************************************
                    //  Dialy submission start
                    // /*****************************************************************

                    // Store daily reading data
                    // $dataSheet2 = $excelData->sheets[1];
                    // if ($dataSheet2['numRows'] > 1) {

                    //     // Static columns for site
                    //     $staticColmuns                 = array();
                    //     $staticColmuns['Day']          = "date_id";
                    //     $staticColmuns['Month']        = "month_id";
                    //     $staticColmuns['Year']         = "year_id";
                    //     $staticColmuns['Room Nights']  = "total_room_night";
                    //     $staticColmuns['Total Guests'] = "total_guests";
                    //     //$staticColmuns['Cooling Degree Day']            = "cdd";
                    //     //$staticColmuns['Heating Degree Day']            = "hdd";
                    //     $staticColmuns['Electricity Tariff ($/Kwh)']    = "electricity_cost";
                    //     $staticColmuns['Fuel Oil  $/Liter']             = "fuel_oil_cost";
                    //     $staticColmuns['LPG $/Kg']                      = "lpg_cost";
                    //     $staticColmuns['Water Cost ($)']                = "water_cost";
                    //     $staticColmuns['Natural Gas $/m3']              = "natural_gas_cost";
                    //     $staticColmuns['District Energy Heating $/Kwh'] = "district_heating_cost";
                    //     $staticColmuns['District Energy Cooling $/Kwh'] = "district_cooling_cost";

                    //     // Convert data with database name mapping to use only configured columns to use
                    //     $allSiteids                = $this->import_model->get_site_detail_by_name();
                    //     $allDailySubmissionDetails = $this->sites_model->get_all_daily_reading_settings();

                    //     $sites_daily_submission = array();
                    //     // Arrange submission data site wise
                    //     if (!empty($allDailySubmissionDetails)) {
                    //         foreach ($allDailySubmissionDetails as $key => $value) {
                    //             $dynamicTitle          = array();
                    //             $dynamicTitle['id']    = $value['id'];
                    //             $dynamicTitle['title'] = $value['title'];

                    //             $sites_daily_submission[$value['site_id']][] = $dynamicTitle;
                    //         }
                    //     }

                    //     $allData    = array();
                    //     $siteNames  = array();
                    //     $titleCells = $dataSheet2['cells'][1];
                    //     foreach ($dataSheet2['cells'] as $key => $cell) {
                    //         if ($key == 1) {
                    //             // For titles
                    //             continue;
                    //         }
                    //         $iData = array();

                    //         foreach ($titleCells as $key => $value) {
                    //             $value = trim($value);

                    //             if ($cell[$key] != null) {
                    //                 $iData[$value] = str_replace('* ', '', $cell[$key]);
                    //             } else {
                    //                 $iData[$value] = '';
                    //             }
                    //         }

                    //         $iData['site_id'] = $allSiteids[trim($iData['Site Name'])]['id'];

                    //         if (!empty($iData['site_id'])) {
                    //             $allData[] = $iData;
                    //         }

                    //     }

                    //     // Store data
                    //     if (!empty($allData)) {
                    //         foreach ($allData as $key => $value) {

                    //             if (empty($value['Site Name'])) {
                    //                 continue;
                    //             }

                    //             // Prepare Static data
                    //             $staticDataInsert            = array();
                    //             $staticDataInsert['site_id'] = $value['site_id'];
                    //             foreach ($staticColmuns as $key1 => $value1) {
                    //                 $staticDataInsert[$value1] = ($value[$key1] != null) ? $value[$key1] : '';
                    //             }

                    //             // Store fixed daily data in database
                    //             $this->import_model->delete_daily_fixed_submission_utility_ifexists($staticDataInsert);
                    //             $statusInsertFixedUtility = $this->import_model->insert_daily_fixed_submission_utilities($staticDataInsert, $cdd_hdd_values);

                    //             // Prepare Dynamic data
                    //             if (isset($sites_daily_submission[$value['site_id']]) && !empty($sites_daily_submission[$value['site_id']])) {

                    //                 $dynamicDataInsert            = array();
                    //                 $dynamicDataInsert['site_id'] = $value['site_id'];
                    //                 $bulkInsertData = array();
                    //                 foreach ($sites_daily_submission[$value['site_id']] as $value2) {

                    //                     //utility_title_id
                    //                     $dynamicDataInsert['date_id']          = $staticDataInsert['date_id'];
                    //                     $dynamicDataInsert['month_id']         = $staticDataInsert['month_id'];
                    //                     $dynamicDataInsert['year_id']          = $staticDataInsert['year_id'];
                    //                     $dynamicDataInsert['utility_title_id'] = $value2['id'];
                    //                     $dynamicDataInsert['value']            = ($value[$value2['title']] != null) ? $value[$value2['title']] : '';

                    //                     // Store dynamic daily data in database
                    //                     $this->import_model->delete_daily_dynamic_submission_utility_ifexists($dynamicDataInsert);
                    //                     // $statusInsertDynamicUtility = $this->import_model->insert_daily_dynamic_submission_utilities($dynamicDataInsert);
                    //                     $bulkInsertData[] = $dynamicDataInsert;
                    //                 }

                    //                 $statusInsertDynamicUtility = $this->import_model->insert_daily_dynamic_submission_utilities($bulkInsertData);
                    //             }
                    //         }
                    //     }
                    // } 

                    /******************************************************************/
                    /* Dialy submission End
                    /******************************************************************/

                    /******************************************************************/
                    /* Hourly start
                    /******************************************************************/

                    $data = $excelData->sheets[2];
                    if ($data['numRows'] > 1) {
                        // Map excel columns with database keys
                        $colmuns                                   = array();
                        $colmuns['Site Name']                      = "site_id";
                        $colmuns['Time']                           = "hour";
                        $colmuns['Day']                            = "date_id";
                        $colmuns['Month']                          = "month_id";
                        $colmuns['Year']                           = "year_id";

                        // Convert data with database name mapping to use only configured columns to use
                        $importData = array();
                        $siteNames  = array();
                        $titleCells = $data['cells'][1];
                        foreach ($data['cells'] as $key => $cell) {

                            if ($key == 1) {
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
                            $importData[] = $iData;
                        }

                        $siteNames  = array_values(array_unique($siteNames));
                        $allSiteids = $this->import_model->get_site_detail_by_name($siteNames);
                        // Store data in database
                        if (!empty($importData)) {

                            foreach ($importData as $key => $value) {
                                if(!empty($value)){
                                    $value['site_id'] = trim($value['site_id']);
                                    if (isset($allSiteids[$value['site_id']])) {

                                        $is_hourly = $allSiteids[$value['site_id']]['is_hourly'];
                                        $value['site_id'] = $allSiteids[$value['site_id']]['id'];
                                        
                                        // 0 => half hourly 1 => hourly 
                                        $value['is_half_hourly'] = (intval($is_hourly) == 1) ? 0 : 1;
                                  
                                        if (!empty($value['date_id'])) {

                                            // Delete if data already exists 
                                            $this->import_model->delete_hourly_utility_ifexists($value);

                                            // Insert new data
                                            $statusInsertUtility = $this->import_model->insert_hourly_utilities($value, $cdd_hdd_values);
                                        }
                                    }
                                }
                            }
                        }

                        $process = true;
                    }
                    /******************************************************************/
                    /* Hourly End
                    /******************************************************************/

                    /******************************************************************/
                    /* Hourly submission start
                    /******************************************************************/

                    // Store daily reading data
                    $dataSheet2 = $excelData->sheets[2];
                    if ($dataSheet2['numRows'] > 1) {

                        // Static columns for site
                        $staticColmuns                 = array();
                        $staticColmuns['Day']          = "date_id";
                        $staticColmuns['Month']        = "month_id";
                        $staticColmuns['Year']         = "year_id";
                        $staticColmuns['Time']         = "hour";

                        // Convert data with database name mapping to use only configured columns to use
                        $allSiteids                = $this->import_model->get_site_detail_by_name();
                        $allDailySubmissionDetails = $this->sites_model->get_all_hourly_reading_settings();

                        $sites_daily_submission = array();
                        // Arrange submission data site wise
                        if (!empty($allDailySubmissionDetails)) {
                            foreach ($allDailySubmissionDetails as $key => $value) {
                                $dynamicTitle                 = array();
                                $dynamicTitle['id']           = $value['id'];
                                $dynamicTitle['hourly_title'] = $value['hourly_title'];

                                $sites_daily_submission[$value['site_id']][] = $dynamicTitle;
                            }
                        }

                        $allData    = array();
                        $siteNames  = array();
                        $titleCells = $dataSheet2['cells'][1];
                        foreach ($dataSheet2['cells'] as $key => $cell) {
                            if ($key == 1) {
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

                        // Store data
                        if (!empty($allData)) {
                            foreach ($allData as $key => $value) {

                                if (empty($value['Site Name'])) {
                                    continue;
                                }

                                // Prepare Static data
                                $staticDataInsert            = array();
                                $staticDataInsert['site_id'] = $value['site_id'];
                                foreach ($staticColmuns as $key1 => $value1) {
                                    $staticDataInsert[$value1] = ($value[$key1] != null) ? $value[$key1] : '';
                                }

                                $is_hourly = $allSiteids[$value['site_id']]['is_hourly'];
                                    
                                // 0 => half hourly 1 => hourly 
                                $staticDataInsert['is_half_hourly'] = ($is_hourly == '1') ? 0 : 1;

                                // Store fixed daily data in database
                                $this->import_model->delete_hourly_fixed_submission_utility_ifexists($staticDataInsert);
                                $statusInsertFixedUtility = $this->import_model->insert_hourly_fixed_submission_utilities($staticDataInsert, $cdd_hdd_values);

                                // pre($sites_daily_submission);

                                // Prepare Dynamic data
                                if (isset($sites_daily_submission[$value['site_id']]) && !empty($sites_daily_submission[$value['site_id']])) {

                                    $dynamicDataInsert            = array();
                                    $dynamicDataInsert['site_id'] = $value['site_id'];

                                    foreach ($sites_daily_submission[$value['site_id']] as $value2) {

                                        $dynamicDataInsert['date_id']          = $staticDataInsert['date_id'];
                                        $dynamicDataInsert['month_id']         = $staticDataInsert['month_id'];
                                        $dynamicDataInsert['year_id']          = $staticDataInsert['year_id'];
                                        $dynamicDataInsert['hour']             = date("H:i:s", strtotime($staticDataInsert['hour']));
                                        $dynamicDataInsert['utility_title_id'] = $value2['id'];
                                        $dynamicDataInsert['value']            = ($value[$value2['hourly_title']] != null) ? $value[$value2['hourly_title']] : '';
                                        $dynamicDataInsert['is_half_hourly']   = $staticDataInsert['is_half_hourly'];
                                        // Store dynamic daily data in database
                                        $this->import_model->delete_hourly_dynamic_submission_utility_ifexists($dynamicDataInsert);
                                        $statusInsertDynamicUtility = $this->import_model->insert_hourly_dynamic_submission_utilities($dynamicDataInsert);
                                    }
                                } 
                            }
                        }
                    }

                    /******************************************************************/
                    /* Hourly submission End
                    /******************************************************************/                   
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

    public function export_monthly_data()
    {
        $decimal_places = 2;
        require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';

        $this->lang->load('sites/sites', 'english'); //get_site_listing_for_users
        $this->load->model('utilities/utilities_model');
        $this->load->model('sites/sites_model');

        $user_id = $this->session->userdata[$this->section_name]['user_id'];
        $site_id = $this->session->userdata[$this->section_name]['site_id'];
        $role_id = $this->session->userdata[$this->section_name]['role_id'];

        $site_details = $this->sites_model->get_site_listing_for_users($site_id, $role_id, $user_id);

        $this->utilities_model->site_id        = $site_id;
        $this->utilities_model->utilities_year = date('Y');

        //get utilities of current year of selected site
        $utility = $this->utilities_model->getSiteUtility();

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

        $columns["site_id"]                                 = 'Site Name';
        $columns["month_id"]                                = 'Month';
        $columns["year_id"]                                 = 'Year';
        $columns['total_kwh']                               = 'Purchased Electricity Kwh';
        $columns['tariff']                                  = 'Purchased Electricity Tariff ($/Kwh)';
        $columns["maximum_demand"]                          = 'Maximum Demand KVA/KW';
        $columns["maximum_demand_price"]                    = 'Maximum Demand Tariff ($/KVA || $/KW)';
        $columns["fixed_fees"]                              = 'Fixed Fees Cost ($)';
        $columns["electricity_total_budget"]                = 'Electricity Total Budgeted';
        $columns["electricity_total_budget_cost"]           = 'Electricity Total Budgeted Cost ($)';
        $columns["lpg_kitchen"]                             = 'LPG Kitchen  (Kg)';
        $columns["lpg_kitchen_rate"]                        = 'LPG Kitchen $/Kg';
        $columns["lpg_total_budget"]                        = 'LPG Total Budgeted';
        $columns["lpg_total_budget_cost"]                   = 'LPG Total Budgeted Cost ($)';
        $columns["district_heating"]                        = 'District Energy Heating Kwh';
        $columns["district_heating_rate"]                   = 'District Energy Heating $/Kwh';
        $columns["district_heating_fixed_cost"]             = 'Heating Fixed Cost';
        $columns["district_heating_total_budget"]           = 'Heating District Energy Total Budgeted';
        $columns["district_heating_total_budget_cost"]      = 'Heating District Energy Total Budgeted Cost ($)';
        
        $columns["water_utility_supply"]                    = 'Water Utility Supply (m3) m3';
        $columns["water_utility_supply_rate"]               = 'Water Utility Supply (m3) $/m3';
        $columns["water_total_consumption_budget"]          = 'Water Total Budgeted';
        $columns["water_total_consumption_budget_cost"]     = 'Water Total Budgeted Cost ($)';
        $columns["total_room_night"]                        = 'Room Nights';
        $columns["total_guests"]                            = 'Total Guests';
        $columns["total_laundered"]                         = 'Laundry Load';
        $columns["cdd"]                                     = "Cooling Degree Day";
        $columns["hdd"]                                     = "Heating Degree Day";

        $columns['onsite_generators_quantity']             = "Onsite Generators Kwh";
        $columns['total_onsite_generators_cost']           = "Onsite Generators Cost ($)";
        $columns["average_pf"]                             = 'Average PF';
        $columns["total_renewable_energy_production"]      = 'Total Renewable Energy Production Kwh';
        $columns["total_electricity_cost"]                 = 'Total Electricity Cost';
        $columns["total_electricity_kwh"]                  = 'Total Electricity Kwh';
        $columns["fuel_oil_hot_water_boilers"]             = 'Fuel Oil Hot-Water Boilers (Liters) Liter';
        $columns["fuel_oil_hot_water_boilers_rate"]        = 'Fuel Oil Hot-Water Boilers (Liters) $/Liter';
        $columns["fuel_oil_steam_boilers"]                 = 'Fuel Oil Steam Boilers (Liters) Liter';
        $columns["fuel_oil_steam_boilers_rate"]            = 'Fuel Oil Steam Boilers (Liters) $/Liter';
        $columns["fuel_oil_others"]                        = 'Fuel Oil Others (Liters) Liter';
        $columns["fuel_oil_others_rate"]                   = 'Fuel Oil Others (Liters) $/Liter';
        $columns["fuel_total_budget"]                      = 'Fuel Oil Total Budgeted';
        $columns["fuel_total_budget_cost"]                 = 'Fuel Oil Total Budgeted Cost ($)';
        $columns["lpg_hot_water_boilers"]                  = 'LPG Hot-Water Boilers (Kg)';
        $columns["lpg_hot_water_boilers_rate"]             = 'LPG Hot-Water Boilers $/Kg';
        $columns["lpg_steam_boilers"]                      = 'LPG Steam Boilers  (Kg)';
        $columns["lpg_steam_boilers_rate"]                 = 'LPG Steam Boilers $/Kg';
        $columns["natural_gas_hot_water_boilers"]          = 'Natural Gas Hot-Water Boilers (m3) m3';
        $columns["natural_gas_hot_water_boilers_rate"]     = 'Natural Gas Hot-Water Boilers (m3) $/m3';
        $columns["natural_gas_steam_boilers"]              = 'Natural Gas Steam Boilers (m3) m3';
        $columns["natural_gas_steam_boilers_rate"]         = 'Natural Gas Steam Boilers (m3) $/m3';
        $columns["natural_gas_kitchen"]                    = 'Natural Gas Kitchen (m3) m3';
        $columns["natural_gas_kitchen_rate"]               = 'Natural Gas Kitchen (m3) $/m3';
        $columns["natural_gas_total_budget"]               = 'Natural Gas Total Budgeted';
        $columns["natural_gas_total_budget_cost"]          = 'Natural Gas Total Budgeted Cost ($)';
        $columns["district_cooling"]                       = 'District Energy Cooling Kwh';
        $columns["district_cooling_rate"]                  = 'District Energy Cooling $/Kwh';
        $columns["district_cooling_total_budget"]          = 'Cooling District Energy Total Budgeted';
        $columns["district_cooling_total_budget_cost"]     = 'Cooling District Energy Total Budgeted Cost ($)';
        $columns["waste_water"]                            = 'Wastewater (m3) m3';
        $columns["waste_water_rate"]                       = 'Wastewater (m3) $/m3';
        $columns["water_Cisterns"]                         = 'Water Cisterns (m3) m3';
        $columns["water_Cisterns_rate"]                    = 'Water Cisterns (m3) $/m3';
        $columns["total_fb_services"]           = 'Food Covers';
        $columns["revenue"]                     = 'Revenue';
        $columns["operation_general_waste"]     = 'General Waste';
        $columns["operation_paper_waste"]       = 'Paper Waste';
        $columns["operation_food_waste"]        = 'Food Waste';
        $columns["operation_cardboard_waste"]   = 'Cardboard Waste';
        $columns["operation_plastic_waste"]     = 'Plastic Waste';
        $columns["operation_glass_waste"]       = 'Glass Waste';
        $columns["operation_recycled_waste"]    = 'Recycled Waste';
        $columns["district_cooling_fixed_cost"] = 'Cooling Fixed Cost';
        $columns['forex']                       = "Forex";

        //adding purchased electricity records to utility record
        foreach ($utility as $key => $utl) {
            $current_utility                        = $utl;
            $this->utilities_model->utilities_month = $utl['month_id'];
            $this->utilities_model->utilities_year  = $utl['year_id'];
            $electricityTariff                      = $this->utilities_model->getElectricityTariff();

            $temp = 0;
            foreach ($electricityTariff as $single) {
                if ($temp == 0) {
                    $current_utility['tariff']    = round($single['tariff'], $decimal_places);
                    $current_utility['total_kwh'] = round($single['total_kwh'], $decimal_places);
                } else {
                    array_push($columns['total_kwh' . $temp] = 'Purchased Electricity Kwh');
                    array_push($columns['tariff' . $temp] = 'Purchased Electricity Tariff ($/Kwh)');
                    $current_utility['tariff' . $temp]    = round($single['tariff'], $decimal_places);
                    $current_utility['total_kwh' . $temp] = round($single['total_kwh'], $decimal_places);
                }
                $temp++;
            }
            $utility[$key] = $current_utility;
        }

        $cells  = array();
        $later1 = "";
        $later2 = 'A';
        $flag   = 0;
        foreach ($columns as $key => $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
            $cells[$key] = $later1 . $later2;
            $objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
            if ($later2 == 'Z') {
                if ($flag == 0) {
                    $later1 = 'A';
                    $flag   = 1;
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
