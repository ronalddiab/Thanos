<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<style type="text/css">
    input.larger {
        width: 20px;
        height: 20px;
    }

    strong {
        font-weight: 500;
    }

    .article-header {
        font-weight: 400 !important;
    }

    .chart_dropdown>ul.dk-select-options {
        max-height: 240px;
        overflow-y: scroll;
        width: 350px;
    }
</style>

<?php
// Config array for chart
$montharray     = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$current_month = date('m');
$current_year  = date('Y');

$utility_types = array(
    'electricity'      => 'Electricity',
    'fuel'             => 'Fuel',
    'lpg'              => 'LPG',
    'natural_gas'      => 'Natural Gas',
    'heating_district' => 'Heating District',
    'cooling_district' => 'Cooling District',
    'water'            => 'Water',
);

// Prepare array for loop
$startmonthsarray = array();
$endmonthsarray   = array();

if ($filters["start_year"] == $filters["end_year"]) {
    // If start and end year is same
    for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
        $startmonthsarray[] = $i;
    }

    $resultkeys                         = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
} else {
    // If start and end year is not same
    for ($i = $filters['start_month']; $i <= 12; $i++) {
        $startmonthsarray[] = $i;
    }

    for ($i = 1; $i <= $filters['end_month']; $i++) {
        $endmonthsarray[] = $i;
    }
    $resultkeys                         = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
    $resultkeys[$filters["end_year"]]   = $endmonthsarray;
}
?>
<!-- Monthly Utility Chart START -->
<?php
if (!empty($reportdata)) {
    $total_sum_current = 0;
    $total_sum_pre     = 0;
    $total_sum_occupancy  = 0;
    $total_months      = 0;
    $total_months_pre  = 0;

    if ($utility_chart_year == date('Y')) {
        $YTD_total_months = $this->_ci->config->config['YTD_month_count'];
    } else {
        $YTD_total_months = 12;
    }
    $monthdata = array();
    $budgetdata = array();
    $previousdata = array();
    $occupancydata = array();
    $previousoccupancydata = array();
    $currentdata_array = array();
    $budgetdata_array = array();
    foreach ($resultkeys as $year => $value) {
        foreach ($value as $key1 => $month) {
            $monthdata[]    = $montharray[$month] . ' ' . $year;
            $previousdata = (!empty($reportdata[$month][$year - 1][$filters['utility_type']])) ? $reportdata[$month][$year - 1][$filters['utility_type']] : 0;
            $currentdata  = (!empty($reportdata[$month][$year][$filters['utility_type']])) ? $reportdata[$month][$year][$filters['utility_type']] : 0;
            $previousoccupancydata  = (!empty($reportdata[$month][$year - 1]['occupancy'])) ? $reportdata[$month][$year - 1]['occupancy'] : 0;
            $occupancydata  = (!empty($reportdata[$month][$year]['occupancy'])) ? $reportdata[$month][$year]['occupancy'] : 0;
            $budgetdata  = (!empty($reportdata[$month][$year][$filters['utility_type'] . '_budget'])) ? $reportdata[$month][$year][$filters['utility_type'] . '_budget'] : 0;

            $previousdata = round($previousdata, 2);
            $currentdata  = round($currentdata, 2);
            $budgetdata   = round($budgetdata, 2);
            $occupancydata  = round($occupancydata, 2);
            $previousoccupancydata  = round($previousoccupancydata, 2);

            $previousdata_array[] = $previousdata;
            $currentdata_array[] = $currentdata;
            $occupancydata_array[] = $occupancydata;
            $previousoccupancydata_array[] = $previousoccupancydata;
            $budgetdata_array[] = $budgetdata;

            // Last year Occupancy Variant
            $deference_occupancy_value = $previousoccupancydata - $occupancydata;
            if ($occupancydata > 0) {
                $percentageoccupancy = (($deference_occupancy_value * 100) / $occupancydata);
                $percentageoccupancy = round($percentageoccupancy, 2);
            } else {
                $percentageoccupancy = 100;
            }

            if ($percentageoccupancy > 0) {
                $poclass = 'nagetive';
                $poarrow = '<span class=\"fa fa-angle-double-down\"></span>';
            } else if ($percentageoccupancy < 0) {
                $poclass = 'positive';
                $poarrow = '<span class=\"fa fa-angle-double-up\"></span>';
            } else {
                $poclass = '';
                $poarrow = '';
            }


            // Last year Variant
            $deference_value = $previousdata - $currentdata;
            if ($currentdata > 0) {
                $percentage = (($deference_value * 100) / $currentdata);
                $percentage = round($percentage, 2);
            } else {
                $percentage = 100;
            }

            if ($percentage > 0) {
                $pclass = 'nagetive';
                $parrow = '<span class=\"fa fa-angle-double-down\"></span>';
            } else if ($percentage < 0) {
                $pclass = 'positive';
                $parrow = '<span class=\"fa fa-angle-double-up\"></span>';
            } else {
                $pclass = '';
                $parrow = '';
            }

            // Budget Variant
            $deference_value = $budgetdata - $currentdata;
            if ($currentdata > 0) {
                $budget_percentage = (($deference_value * 100) / $currentdata);
                $budget_percentage = round($budget_percentage, 2);
            } else {
                $budget_percentage = 100;
            }

            if ($budget_percentage > 0) {
                $budget_pclass = 'nagetive';
                $budget_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
            } else if ($budget_percentage < 0) {
                $budget_pclass = 'positive';
                $budget_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
            } else {
                $budget_pclass = '';
                $budget_parrow = '';
            }
            // Remove - sign
            $percentage        = abs($percentage);
            $budget_percentage = abs($budget_percentage);
            $percentageoccupancy = abs($percentageoccupancy);
            $previousdata_tooltip_array = array();
            $currentdata_tooltip_array = array();
            $occupancydata_tooltip_array = array();
            $previousoccupancydata_tooltip_array = array();
            $previousdata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . ($year - 1) .  '</strong><br>' . lang('actual') . ': ' . $currentdata . '<br>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budgetdata . '</span><br>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $previousdata . '</span><br>';
            $currentdata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . $year . '</strong><br>' . lang('actual') . ': ' . $currentdata . '<br>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budgetdata . '</span><br>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $previousdata . '</span><br>';
            $occupancydata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . $year . '</strong><br>' . lang('occupancy') . ': ' . $occupancydata;
            $previousoccupancydata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . ($year - 1) . '</strong><br>' . lang('occupancy') . ': ' . $previousoccupancydata;
            $budgetdata_tooltip[]  = '<strong>' . $fullmontharray[$month] . ' ' . $year . '</strong><br>' . lang('budget') . ': ' . $budgetdata . '<br>';

            $previousdata_tooltip_array[] = $previousdata_tooltip;
            $currentdata_tooltip_array[] = $currentdata_tooltip;
            $occupancydata_tooltip_array[] = $occupancydata_tooltip;
            $previousoccupancydata_tooltip_array[] = $occupancydata_tooltip;
            $budgetdata_tooltip_array[] = $budgetdata_tooltip;

            if ($month <= $YTD_total_months) {
                $total_sum_pre += $previousdata;
            }

            $total_months_pre++;
            if ($filters['CURRENT_YEAR_MAX_MONTH_ID'] >= $month) {
                $total_sum_current += $currentdata;
                $total_sum_budget += $budgetdata;
                $total_months++;
            }
            $filter_utility_type_array = array();
            $filter_utility_type = $filters['utility_type'];
            $filter_utility_type_array = $filter_utility_type;
        }
    }

    // Total months for average is current month
    $currentAvgData  = ($total_sum_current / $YTD_total_months);
    $previousAvgData = ($total_sum_pre / $YTD_total_months);
    $budgetAvgData   = ($total_sum_budget / $total_months);

    $currentAvgData  = round($currentAvgData, 2);
    $previousAvgData = round($previousAvgData, 2);
    $budgetAvgData   = round($budgetAvgData, 2);

    // Last year average Variant
    $deference_value = $previousAvgData - $currentAvgData;
    if ($currentAvgData > 0) {
        $percentage = (($deference_value * 100) / $currentAvgData);
        $percentage = round($percentage, 2);
    } else {
        $percentage = 100;
    }

    if ($percentage > 0) {
        $pclass = 'nagetive';
        $parrow = '<span class=\"fa fa-angle-double-down\"></span>';
    } else if ($percentage < 0) {
        $pclass = 'positive';
        $parrow = '<span class=\"fa fa-angle-double-up\"></span>';
    } else {
        $pclass = '';
        $parrow = '';
    }

    // Budget Average Variant
    $deference_value = $budgetAvgData - $currentAvgData;
    if ($currentAvgData > 0) {
        $budget_percentage = (($deference_value * 100) / $currentAvgData);
        $budget_percentage = round($budget_percentage, 2);
    } else {
        $budget_percentage = 100;
    }

    if ($budget_percentage > 0) {
        $budget_pclass = 'nagetive';
        $budget_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
    } else if ($budget_percentage < 0) {
        $budget_pclass = 'positive';
        $budget_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
    } else {
        $budget_pclass = '';
        $budget_parrow = '';
    }
    // Remove - sign
    $percentage        = abs($percentage);
    $budget_percentage = abs($budget_percentage);
    $previousAvgData_tooltip = '<strong>' . lang('average') . '</strong><br>' . lang('actual') . ': ' . $currentAvgData . '<br>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budgetAvgData . '</span><br>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $previousAvgData . '</span><br>';
    $currentAvgData_tooltip  = '<br><strong>' . lang('average') . '</strong><br>' . lang('actual') . ': ' . $currentAvgData . '<br>' . lang('vs-budget') . ' : <span class=\"variant-' . $budget_pclass . '\">' . $budget_parrow . $budgetAvgData . '</span><br>' . lang('vs-last-year') . ' : <span class=\"variant-' . $pclass . '\">' . $parrow . $previousAvgData . '</span></div></div>';
    $occupancydata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . $year . '</strong><br>' . lang('occupancy') . ': ' . $occupancydata;
    $previousoccupancydata_tooltip[] = '<strong>' . $fullmontharray[$month] . ' ' . ($year - 1) . '</strong><br>' . lang('occupancy') . ': ' . $previousoccupancydata;
    $budgetAvgData_tooltip   = '<br><strong>' . lang('average') . '</strong></div><br/>' . lang('budget') . ': ' . $budgetAvgData . '</div>';

    $monthdata[] = "Average";

    $previousdata_array[] = '{y: ' . $previousAvgData . ', color: "grey"}';
    $currentdata_array[] = '{y: ' . $currentAvgData . ', color: "black"}';

    if ((string)$budgetAvgData == "NAN") {
        $budgetAvgData = 0;
    }
    $budgetdata_array[] = $budgetAvgData;
    $previousdata_tooltip_array[0][] = $previousAvgData_tooltip;
    $currentdata_tooltip_array[0][] = $currentAvgData_tooltip;
    $budgetdata_tooltip_array[0][] = $budgetAvgData_tooltip;

    $previousArray = array();
    $currentArray = array();
    $occupancyArray = array();
    $previousoccupancyArray = array();
    for ($i = 1; $i <= count($fullmontharray); $i++) {
        $previousArray[] = $fullmontharray[$i] . " " . ($filters['start_year'] - 1);
        $currentArray[] = $fullmontharray[$i] . " " . ($filters['start_year']);
        $occupancyArray[] = $fullmontharray[$i] . " " . ($filters['start_year']);
        $previousoccupancyArray[] = $fullmontharray[$i] . " " . ($filters['start_year']);
    }
    $previousArray[] = "Average";
    $currentArray[] = "Average";
}
?>

<!-- Monthly Utility Chart END -->
<!-- Progress Chart START-->
<?php
//Creation of array for progress on target widget
$progressTarget = $ProgressTargetPercentage = [];
if (!empty($progressOnTarget)) {
    $progressEnergyYTD = 0;
    $progressWaterYTD = 0;
    foreach ($progressOnTarget as $key => $value) {
        foreach ($value as $key1 => $value1) {
            // monthly data of Current year (water and Energy)
            if ($value1['month_id'] == date('m') - 1 && $value1['year_id'] == date('Y')) {
                $progressTarget['energy']['energy_monthly'] = $value1['energy_target'];
                $progressTarget['water']['water_monthly'] = $value1['water_target'];
            } else 
            // monthly data of last year (water and Energy)
            if ($value1['month_id'] == date('m') - 1 && $value1['year_id'] == date('Y') - 1) {
                $progressTarget['energy']['energy_last_monthly'] = $value1['energy_target'];
                $progressTarget['water']['water_last_monthly'] = $value1['water_target'];
            } else {
                $progressTarget['energy']['energy_last_monthly'] = 0;
                $progressTarget['water']['water_last_monthly'] = 0;
                $progressTarget['energy']['energy_monthly'] = 0;
                $progressTarget['water']['water_monthly'] = 0;
            }
            // YTD data of Current and last year (Energy)
            if ($value1['month_id'] < date('m') && $value1['year_id'] == date('Y')) {
                $progressEnergyYTD += $value1['energy_target'];
                $progressTarget['energy']['energy_YTD'] = $progressEnergyYTD;
            } else {
                $progressEnergyYTD += $value1['energy_target'];
                $progressTarget['energy']['energy_last_YTD'] = $progressEnergyYTD;
            }
            // YTD data of Curret and last year (water)
            if ($value1['month_id'] < date('m') && $value1['year_id'] == date('Y') - 1) {
                $progressWaterYTD += $value1['water_target'];
                $progressTarget['water']['water_YTD'] = $progressWaterYTD;
            } else {
                $progressWaterYTD += $value1['water_target'];
                $progressTarget['water']['water_last_YTD'] = $progressWaterYTD;
            }
            // carbon current , last month (monthly) , last year,  current year YTD data
            $progressTarget['carbon']['carbon_monthly'] = isset($carbon_footprint_currentMonth) ? $carbon_footprint_currentMonth : 0;
            $progressTarget['carbon']['carbon_last_monthly'] = isset($carbon_footprint_SameMonthPreviousYear) ? $carbon_footprint_SameMonthPreviousYear : 0;
            $progressTarget['carbon']['carbon_YTD'] = isset($ytd_carbon_footprint_new) ? $ytd_carbon_footprint_new : 0;
            $progressTarget['carbon']['carbon_last_YTD'] = isset($ytd_carbon_footprintPreviousYear) ? $ytd_carbon_footprintPreviousYear : 0;

            $progressTarget['waste']['waste_monthly'] = 5;
            $progressTarget['waste']['YTD'] = 2;
        }
    }
    // Calculation of Progress on Targets
    // energy_intensity_annual_target% from side edit and Percentage difference from previous year to current year
    foreach ($progressTarget as $key => $value) {
        if ($key == 'energy') {
            $differenceEnergyMonthly = $value['energy_last_monthly'] - $value['energy_monthly'];
            $progressEnergyMonthlyTargetValue = (($value['energy_last_monthly'] * $site_detials['energy_intensity_annual_target']) / 100);
            $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyMonthly, $value['energy_last_monthly']);
            $ProgressTargetPercentage['Energy']['monthly'] = isset($ProgressEnergyTargetPercentageValue) ? $ProgressEnergyTargetPercentageValue : 0;
            $ProgressTargetPercentage['Energy']['site_saving_target'] = isset($site_detials['energy_intensity_annual_target']) ? $site_detials['energy_intensity_annual_target'] : 0;

            $differenceEnergyYTD = $value['energy_last_YTD'] - $value['energy_YTD'];
            $progressEnergyYTDTargetValue = (($value['energy_last_YTD'] * $site_detials['energy_intensity_annual_target']) / 100);
            $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyYTD, $value['energy_last_YTD']);
            $ProgressTargetPercentage['Energy']['YTD'] = isset($ProgressEnergyTargetPercentageValue) ? $ProgressEnergyTargetPercentageValue : 0;
        }
        if ($key == 'water') {
            $differenceWaterMonthly = $value['water_last_monthly'] - $value['water_monthly'];
            $progressWaterMonthlyTargetValue = (($value['water_last_monthly'] * $site_detials['water_intensity_annual_target']) / 100);
            $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterMonthly, $value['water_last_monthly']);
            $ProgressTargetPercentage['Water']['monthly'] = isset($ProgressWaterTargetPercentageValue) ? $ProgressWaterTargetPercentageValue : 0;
            $ProgressTargetPercentage['Water']['site_saving_target'] = isset($site_detials['water_intensity_annual_target']) ? $site_detials['water_intensity_annual_target'] : 0;

            $differenceWaterYTD = $value['water_last_YTD'] - $value['water_YTD'];
            $progressWaterYTDTargetValue = (($value['water_last_YTD'] * $site_detials['water_intensity_annual_target']) / 100);
            $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterYTD, $value['water_last_YTD']);
            $ProgressTargetPercentage['Water']['YTD'] = isset($ProgressWaterTargetPercentageValue) ? $ProgressWaterTargetPercentageValue : 0;
        }
        if ($key == 'carbon') {
            $differenceCarbonMonthly = $value['carbon_last_monthly'] - $value['carbon_monthly'];
            $progressCarbonMonthlyTargetValue = (($value['carbon_last_monthly'] * $site_detials['ghg_intensity_annual_target']) / 100);
            $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonMonthly, $value['carbon_last_monthly']);
            $ProgressTargetPercentage['Carbon']['monthly'] = isset($ProgressCarbonTargetPercentageValue) ? $ProgressCarbonTargetPercentageValue : 0;
            $ProgressTargetPercentage['Carbon']['site_saving_target'] = isset($site_detials['ghg_intensity_annual_target']) ? $site_detials['ghg_intensity_annual_target'] : 0;

            $differenceCarbonYTD = $value['carbon_last_YTD'] - $value['carbon_YTD'];
            $progressCarbonYTDTargetValue = (($value['carbon_last_YTD'] * $site_detials['ghg_intensity_annual_target']) / 100);
            $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonYTD, $value['carbon_last_YTD']);
            $ProgressTargetPercentage['Carbon']['YTD'] = isset($ProgressCarbonTargetPercentageValue) ? $ProgressCarbonTargetPercentageValue : 0;
        }
        if ($key == 'waste') {
            $ProgressTargetPercentage['Waste']['monthly'] = isset($value['waste_monthly']) ? $value['waste_monthly'] : 0;
            $ProgressTargetPercentage['Waste']['YTD'] = isset($value['YTD']) ? $value['YTD'] : 0;
            $ProgressTargetPercentage['Waste']['site_saving_target'] = isset($site_detials['waste_intensity_annual_target']) ? $site_detials['waste_intensity_annual_target'] : 0;
        }
    }
}
// Progress Chart Data array
if (!empty($progressReportData)) {
    $dataPrev = $dataCurrent = $dataChart = $dataBudget = $dataSavingPercentage = $dataTargetPrev = $dataTargetCurr = $dataBudgetPrev = $dataBudgetCurr = [];
    foreach ($progressReportData as $key => $value) {
        $jsonKey = $fullmontharray[$key];
        foreach ($value as $keyData => $valueData) {
            $jsonValue = $valueData[$progress_chart_utility];
            $jsonTargetValue = $valueData[$progress_chart_utility . '_target'];
            $jsonBudgetValue = $valueData[$progress_chart_utility . '_budget'];
            if ($keyData < date('Y') && $progress_chart_year != 'industry_benchmark') {
                $dataTargetPrev[] = $jsonTargetValue;
                $dataBudgetPrev[] = $jsonBudgetValue;
                $dataPrev[] =  $jsonValue;
            } else {
                $dataTargetCurr[] = $jsonTargetValue;
                $dataBudgetCurr[] = $jsonBudgetValue;
                $dataCurrent[] = $jsonValue;
            }
        }
    }

    if ($progress_chart_year != 'industry_benchmark') {
        foreach ($dataPrev as $key => $value) {
            $reductionUtilityValue = ($progress_chart_utility == 'energy') ? 'energy_intensity_annual_target' : 'water_intensity_annual_target';
            $bugetPrevValue = $dataBudgetPrev[$key];
            $bugetCurrValue = $dataBudgetCurr[$key];
            $dataBudgetValue = (($bugetPrevValue * $site_detials[$reductionUtilityValue]) / 100);
            $dataBudget[] = $bugetPrevValue - $dataBudgetValue;
            if ($is_percent_check == 1) {
                $dataPrevValue = $dataTargetPrev[$key];
                $dataCurrValue = $dataTargetCurr[$key];
                $differenceFromPrev = $dataPrevValue - $dataCurrValue;
                $dataSavingTargetValue = (($dataPrevValue * $site_detials[$reductionUtilityValue]) / 100);
                $dataSavingPercentage[] = calculateDashboardPercentage($differenceFromPrev, $dataPrevValue);
            }
        }
    }
    if ($progress_chart_year == 'industry_benchmark') {
        foreach ($dataCurrent as $key => $value) {
            $reductionUtilityKey = ($progress_chart_utility == 'energy') ? 'energy_intensity_benchmark_target' : 'water_intensity_benchmark_target';
            $dataChart[$key] = round(calculateDashboardPercentage($value, $site_detials[$reductionUtilityKey]), 2);
        }
    }
    $chartTitleUnit = ($progress_chart_utility == 'energy') ? 'EUI' : 'WUI';
	if ($progress_chart_year == 'eui_by_energy_composition') {
		$chartTitle =  $chartTitleUnit . ' by energy composition';
	} else if ($progress_chart_year == $site_detials['baseline_regression_year']) {
        $chartTitle =  $chartTitleUnit . ' v\s Baseline Year';
    } else if ($progress_chart_year == date('Y') - 1) {
        $chartTitle =  $chartTitleUnit . ' v\s Previous Year';
    } else if ($progress_chart_year == 'industry_benchmark') {
        $chartTitle = '% Reduction in intensity v\s industry benchmark';
    } else if ($progress_chart_year == ('on_site_renewable-' . $site_detials['baseline_regression_year'])) {
        $chartTitle = 'On-site renewable generation v\s baseline year';
    } else if ($progress_chart_year == ('on_site_renewable-' . (date('Y') - 1))) {
        $chartTitle = 'On-site renewable generation v\s previous year';
	}
}
// Calculation of EUi by energy composition
if (!empty($groupUtilityChartDataArray)) {
	$electricity = $gases = $others = [];
	foreach ($groupUtilityChartDataArray as $key => $value) {
		foreach ($value as $k => $val) {
			$electricity[] = $val['electricity'];
			$gases[] = $val['gases'];
			$others[] = $val['others'];
		}
    }
}
?>
<!-- Progress Chart END-->
<div class="dashboard-boxes row" style="margin-left: 0px;margin-right: -15px;">
    <div class="col-sm-3" style="min-height:260px !important;max-height:260px !important;">
        <div class="row block-listing" style="margin-left: -6px;">
            <div class="row">
                <div class="col-lg-12" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card card-left-column yellow">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/energy.png" alt="thumb" class="media-object" style="height: 100%;width:100%;">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports"><b class="common-boxtitle yellow">Utilities Cost</b></a>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_currentMonth)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?> </div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_lastMonth)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="indicator-left-content"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($total_utility_cost_sameMonth_lastYear)); ?></span>
                            </div>
                        </div>
                    </article>
                </div>
                <!-- <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card blue">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/thumb03.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/roomnight"><strong class="common-boxtitle blue">Utilities Cost/Room Night</strong></a>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($currentMonth_cost_roomNight)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($lastMonth_cost_roomNight)); ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv"><?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?></div>
                                <span><?php echo currency_symbol(true); ?><?php echo number_format(round($sameMonth_lastYear_cost_roomNight)); ?></span>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-4" style="padding-right:  10px;padding-left: 10px;">
                    <article class="card cyan">
                        <div class="article-content clearfix">
                            <div class="article-thumb">
                                <img src="images/thumb01.png" alt="thumb" class="media-object">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/carbon"><strong class="common-boxtitle cyan">Carbon FootPrint</strong></a>

                            <div>Unit: KgCO<sub>2</sub>e</div>
                            <div class="indicator-container">
                                <div class="labelDiv indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                                <span><?php echo isset($carbon_footprint_currentMonth) ? number_format(round($carbon_footprint_currentMonth)) : 0; ?></span>
                            </div>
                            <div class="indicator-container">
                                <div class="labelDiv indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y') - 1)); ?></div>
                                <span><?php echo isset($carbon_footprint_SameMonthPreviousYear) ? number_format(round($carbon_footprint_SameMonthPreviousYear)) : 0; ?></span>
                            </div>
                            <div class="clearfix indicator-container">
                                <div class="labelDiv indicator-left-content">Year To Date</div>
                                <span><?php echo isset($ytd_carbon_footprint_new) ? number_format(round($ytd_carbon_footprint_new)) : 0; ?></span>
                            </div>
                        </div>
                    </article>
                </div> -->
            </div>
        </div>
    </div>
    <div class="col-sm-3" style="min-height:260px !important;max-height:260px !important;">
        <div class="row block-listing" style="margin-left: 0px;">
            <div class="row">
                <div class="col-lg-12">
                    <article class="card blue">
                        <div class="article-content clearfix" style="padding-left:5%;padding-right:5%;">
                            <div class="article-thumb">
                                <img src="images/import.png" alt="thumb" class="media-object" style="height: 100%;width:100%;">
                            </div>
                            <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports"><b class="common-boxtitle blue" style="padding-left: 20%">Progress on Targets</b></a>
                            <ul class="default-listing">
                                <table width="100%" border="0" cellpadding="2" cellspacing="2" style="font-size: medium; margin-top:50px;">
                                    <thead>
                                        <tr style="height:30px;">
                                            <th width="30%"></th>
                                            <th width="20%"><b>Monthly</b></th>
                                            <th width="20%"><b>YTD</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ProgressTargetPercentage as $key => $value) { ?>
                                            <tr style="height:20px;">
                                                <td><?php echo $key . ' (' . round($value['site_saving_target']) . '%)'; ?></td>
                                                <td><?php echo number_format($value['monthly'], 2); ?> %</td>
                                                <td><?php echo number_format($value['YTD'], 2); ?> %</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 block-listing" style="padding-right: 0px;margin-left: -10px;">
        <div class="col-lg-6" style="padding-right:  5px;padding-left: 5px;">
            <article class="card green">
                <div class="article-content clearfix">
                    <div class="article-thumb">
                        <img src="images/budget.png" alt="thumb" class="media-object" style="height: 100%;width:100%;">
                    </div>
                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/budget"><b class="common-boxtitle green">Utilities Cost v/s Budget</b></a>
                    <div class="clearfix indicator-container">
                        <div class="indicator-left-content"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?></div>
                        <?php
                        $image = $variation < 0 ? 'upArrow.png' : 'downArrow.png';
                        $color = $variation < 0 ? '#dc2727' : '#2ecc71';

                        $image_ytd = $variation_ytd < 0 ? 'upArrow.png' : 'downArrow.png';
                        $color_ytd = $variation_ytd < 0 ? '#dc2727' : '#2ecc71';
                        ?>
                        <span style="color:<?php echo $color; ?>"><?php echo round(abs($variationPercentage)); ?>% <img src="images/<?php echo $image; ?>"></span>
                    </div>
                    <div class="clearfix indicator-container">
                        <div class="indicator-left-content">Year To Date</div>
                        <span style="color:<?php echo $color_ytd; ?>"><?php echo round(abs($variationPercentage_ytd)); ?>% <img src="images/<?php echo $image_ytd; ?>"></span>
                    </div>
                </div>
            </article>
        </div>
        <div class="col-lg-6" style="padding-right:  5px;padding-left: 5px;">
            <article class="card dark-green">
                <div class="ema-article-content article-content clearfix">
                    <div class="article-thumb">
                        <img src="images/project.png" alt="thumb" class="media-object" style="height: 100%;width:100%;">
                        <span>Total: <?php echo $actionPlanCounts; ?></span>
                    </div>
                    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>projects/actionplans/<?php echo $site_detials['id']; ?>"><b class="common-boxtitle dark-green">Efficiency Measures Actions</b></a>
                    <div class="dashboard-progress-container clearfix">
                        <div class="row">
                            <p class="col-lg-10 title-bar">Awaiting Approval - <?php echo $actionPlanAwaitingApprovalCounts; ?></p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $actionPlanAwaitingApprovalPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanAwaitingApprovalPercentage; ?>%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <div class="row">
                            <p class="col-lg-10 title-bar">On Hold - <?php echo $actionPlanOnholdCounts; ?></p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $actionPlanOnholdPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanOnholdPercentage; ?>%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <div class="row">
                            <p class="col-lg-10 title-bar">In Progress - <?php echo $actionPlanInProgressCounts; ?></p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $actionPlanInProgressPercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanInProgressPercentage; ?>%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                        <div class="row">
                            <p class="col-lg-10 title-bar">Completed - <?php echo $actionPlanCompleteCounts; ?></p>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $actionPlanCompletePercentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $actionPlanCompletePercentage; ?>%">
                                <span class="sr-only"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
<div class="dashboard-boxes row">
    <div class="col-sm-8">
        <article class="card" style="min-height:260px !important;max-height:260px !important;">
            <div class="col-sm-7">
                <div class="article-header notification_div_header"><b>Last Month Utilities and Waste Trends</b></div>
            </div>
            <div class="col-sm-5">
                <div class="article-header notification_div_header">
                    <b>Utilities Cost(%)</b>
                </div>
            </div>
            <div class="article-content">
                <div class="col-sm-6">
                    <div class="notification_div">
                        <ul class="default-listing">
                            <?php
                            if (!empty($utility_cost_calculation)) {   ?>
                                <table width="100%" border="0" cellpadding="2" cellspacing="2" style="font-size: medium;">
                                    <tbody>
                                        <thead>
                                            <tr>
                                                <th colspan="3">
                                                    <center><b><?php echo $currentmonth . " " . $currentyear . " v/s " . $lastmonth . " " . $lastyear; ?><center></b>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="28%"><b>Utilities</b></td>
                                            <td width="30%"><b>Consumption</b></td>
                                            <td width="42%"><b>Cost</b></td>
                                        </tr>
                                        <?php
                                        foreach ($utility_cost_calculation as $utility_cost_data) {
                                            $image = $utility_cost_data['consumption'] < 0 ? 'upArrow.png' : 'downArrow.png'; ?>
                                            <tr>
                                                <td><?php echo $utility_cost_data['title']; ?></td>
                                                <td><?php echo round($utility_cost_data['consumption'], 2); ?>% <?php if (round($utility_cost_data['consumption'], 2) == 0) {
                                                                                                                } else { ?> <img src="images/<?php echo $utility_cost_data['consumption_image']; ?>" style="width: 18px;"> <?php } ?> </td>
                                                <td><?php echo round($utility_cost_data['cost'], 2); ?>% <?php if (round($utility_cost_data['cost'], 2) == 0) {
                                                                                                            } else { ?><img src="images/<?php echo $utility_cost_data['cost_image']; ?>" style="width: 16px;"> <?php } ?> </td>
                                            </tr>

                                        <?php
                                        }   ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <?php
                                            foreach ($utility_cost_calculation_chr as $utility_cost_data) {
                                            ?>
                                                <td><?php echo $utility_cost_data['title']; ?> <?php echo round(abs($utility_cost_data['consumption'])); ?>% <?php if (round(abs($utility_cost_data['consumption'])) == 0) {
                                                                                                                                                                } else { ?> <img src="images/<?php echo $utility_cost_data['consumption_image']; ?>" style="width: 18px;"> <?php } ?></td>
                                            <?php
                                            }   ?>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div id="utility_pie_highchart"></div>
                </div>
            </div>
        </article>
    </div>
    <div class="col-sm-4" style="padding-right:  5px;padding-left: 5px;margin-left:-10px;">
        <article class="card" style="min-height:260px !important;max-height:260px !important;">
            <div class="article-header notification_div_header">
                <i><img src="images/bell.png" alt="Notification"></i>
                <b>Notifications</b>
            </div>
            <div class="article-content notification_div">
                <ul class="default-listing">
                    <?php
                    if ($notifications or !empty($utilityForLastMonthCompare)) {

                        if ($site_detials['show_total_utility_notification']) {
                            
                            if ($utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['revenue'] > 0) {
                                $total_current_utility_revenue = ((100 * $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_utility']) / $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['revenue']);
                            } else {
                                $total_current_utility_revenue = 100;
                            }

                            if ($utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['revenue'] > 0) {
                                $total_previous_utility_revenue = ((100 * $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_utility']) / $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['revenue']);
                            } else {
                                $total_previous_utility_revenue = 100;
                            }

                            $total_current_utility_revenue  = abs(round($total_current_utility_revenue, 2));
                            $total_previous_utility_revenue = abs(round($total_previous_utility_revenue, 2));

                            if ($total_current_utility_revenue) { ?>
                                <li class="clearfix notification-status-none"><span>Your <strong>Total Utilities</strong> for <?php echo $filters_notification['currentmonth']; ?> represent <?php echo $total_current_utility_revenue; ?>% of the total <strong>revenue</strong> . <?php echo $filters_notification['previousmonth']; ?> represented <?php echo $total_previous_utility_revenue; ?>%</span></li><?php
                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                                                            $current_utility_by_room_night  = 0;
                                                                                                                                                                                                                                                                                                                                                                                                            // Total utilities with room nights
                                                                                                                                                                                                                                                                                                                                                                                                            if ($utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_room_night'] > 0 && $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_room_night'] > 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                $current_utility_by_room_night  = $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_utility'] / $utilityForLastMonthCompare[$filters_notification['cyear']][$filters_notification['cmonth']]['total_room_night'];
                                                                                                                                                                                                                                                                                                                                                                                                                $previous_utility_by_room_night = $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_utility'] / $utilityForLastMonthCompare[$filters_notification['pyear']][$filters_notification['pmonth']]['total_room_night'];

                                                                                                                                                                                                                                                                                                                                                                                                                if ($previous_utility_by_room_night > 0) {
                                                                                                                                                                                                                                                                                                                                                                                                                    $total_utility_room_night_difference = $current_utility_by_room_night - $previous_utility_by_room_night;
                                                                                                                                                                                                                                                                                                                                                                                                                    $total_utility_room_night_percentage = $total_utility_room_night_difference * 100 / $previous_utility_by_room_night;
                                                                                                                                                                                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                                                                                                                                                                                    $total_utility_room_night_percentage = 100;
                                                                                                                                                                                                                                                                                                                                                                                                                }

                                                                                                                                                                                                                                                                                                                                                                                                                $total_utility_room_night_percentage = round($total_utility_room_night_percentage, 2);
                                                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                                                            if ($current_utility_by_room_night > 0 && $total_utility_room_night_percentage > 10) { ?>
                            <li class="clearfix total-utility-room-night-notification-div"><span>Your <strong>Utilities cost per room nights</strong> of <?php echo $filters_notification['currentmonth']; ?> has increased by <?php echo $total_utility_room_night_percentage; ?>% compared to <?php echo $filters_notification['previousmonth']; ?></span></li>
                            <?php }

                                                                                                                                                                                                                                                                                                                                                                                                            if ($sitescustomnotification) {
                                                                                                                                                                                                                                                                                                                                                                                                                foreach ($sitescustomnotification as $key => $value) {
                            ?>
                                <li class="clearfix sitecustomnotification"><span><?php echo $value['notification']; ?></span><a><?php echo date("F - Y", strtotime($value['date'])); ?></a></li>
                        <?php
                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                            }

                                                                                                                                                                                                                                                                                                                                                                                                            if ($notifications) {
                                                                                                                                                                                                                                                                                                                                                                                                                for ($i = 0; $i < count($notifications); $i++) {
                                                                                                                                                                                                                                                                                                                                                                                                                    echo '<li class="clearfix"><span>' . $notifications[$i]['field_label'] . ' is missing</span><a>' . date("F", mktime(0, 0, 0, $notifications[$i]['month'], 10)) . ' - ' . $notifications[$i]['year'] . '</a></li>';
                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                            }
                        ?>
                </ul><?php
                    } ?>
            </div>
        </article>
    </div>
</div>
<!-- Progress Chart START-->
<div class="graph-outer">
    <article class="card">
        <div class="article-header">
            <div class="row">
                <form name="form_progress_chart" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard' ?>">
                    <input type="hidden" name="is_occupancy_check" id="hidden_is_occupancy_check" value="<?php echo $is_occupancy_check; ?>" />
                    <input type="hidden" name="is_occupancy_check_utility" id="hidden_is_occupancy_check_utility" value="<?php echo $is_occupancy_check_utility; ?>" />
                    <input type="hidden" name="is_budget_check_utility" id="hidden_is_budget_check_utility" value="<?php echo $is_budget_check_utility; ?>" />
                    <input type="hidden" name="is_baseline_year" id="hidden_is_baseline_year" value="<?php echo $is_baseline_year; ?>" />
                    <input type="hidden" name="onclickUtility" id="hidden_onclickUtility" value="0" />
                    <input type="hidden" name="onclickProgress" id="hidden_onclickProgress" value="1" />
                    <input type="hidden" name="onclickPerformance" id="hidden_onclickPerformance" value="0" />
                    <input type="hidden" name="utility_type_list" id="hidden_utility_type_list" value="<?php echo $selected_utility; ?>" />
                    <input type="hidden" name="utility_chart_year" id="hidden_utility_chart_year" value="<?php echo $utility_chart_year; ?>" />
                    <input type="hidden" name="performance_chart_type" id="hidden_performance_chart_type" value="<?php echo $performance_chart_type; ?>" />
                    <div class="col-sm-3">
                        <i><img src="images/graph.png" alt="Graph"></i>
                        <?php echo lang('progress-chart-label'); ?>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label col-sm-6">
                            Choose Utility:
                        </label>
                        <div class="form-dropdown col-sm-6">
                            <?php
                            $utilityConstant = [
                                'energy' => 'Energy',
                                'water' => 'Water',
                            ];
                            ?>
                            <select onchange="this.form.submit()" name="progress_chart_utility" data-type="custom-dropdown-update-progress-utility" id="progress_chart_utility">
                                <?php
                                foreach ($utilityConstant as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($key == $progress_chart_utility) ? "selected" : ""; ?>><?php echo $value; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <label class="control-label col-sm-4">
                            Choose Chart:
                        </label>
                        <!-- 'industry_benchmark' => '% reduction in intensity compared to industry benchmark', -->
                        <div class="form-dropdown col-sm-8">
                            <?php
                            $prevYear = date('Y') - 1;
                            $baselineYear = $site_detials['baseline_regression_year'];
                            if ($progress_chart_utility == 'energy') {
                                $progressChartConstant = [
									'eui_by_energy_composition' => $chartTitleUnit . ' by energy composition',
                                    $prevYear  => $chartTitleUnit . ' vs previous year',
                                    $baselineYear => $chartTitleUnit . ' vs baseline year',
                                    'on_site_renewable-' . $prevYear => 'On-site renewable generation vs previous year',
                                    'on_site_renewable-' . $baselineYear => 'On-site renewable generation vs baseline year',
                                ];
                            } else {
                                $progressChartConstant = [
                                    $prevYear  => $chartTitleUnit . ' vs previous year',
                                    $baselineYear => $chartTitleUnit . ' vs baseline year',
                                ];
                            }
                            ?>
                            <select onchange="this.form.submit()" name="progress_chart_year" data-type="custom-dropdown-update-progress-year" id="progress_select" class="chart_dropdown">
                                <?php
                                foreach ($progressChartConstant as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($key == $progress_chart_year) ? "selected" : ""; ?>><?php echo $value; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-sm-1">
                        <button type="submit" class="btn btn-success" name="progress_chart_button" value="1" style="padding: 10px;">
                            <img src="<?php echo base_url(); ?>themes/default/images/search-icon.png"></button>
                    </div> -->
                    <div class="row">
                        <div class="col-sm-8">
                            <div style="margin-left: 15px;">
                                <a href="#" data-toggle="tooltip" data-container="article" data-placement="right" title="info" data-original-title="info"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-4">
							<?php if ($progress_chart_year != 'eui_by_energy_composition' && $progress_chart_year != 'industry_benchmark') { ?>
                                <div class="col-sm-12">
                                    <input type="checkbox" class="larger" id="is_percent_check" name="is_percent_check" value="<?php echo $is_percent_check; ?>" style="padding-right:0px;">
                                    <label class="control-label">% Net Savings</label>
                                </div>
                            <?php } ?>
                            <div class="col-sm-1"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="article-content">
            <div id="progress_chart" style="min-width:310px;height: 500px;margin: 0 auto"></div>
        </div>
    </article>
</div>
<!-- Progress Chart END-->
<!-- Performance Chart START-->
<div class="graph-outer">
    <article class="card">
        <div class="article-header">
            <form name="form_performance_chart" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard' ?>">
                <div class="row">
                    <input type="hidden" name="is_occupancy_check_utility" id="hidden_is_occupancy_check_utility" value="<?php echo $is_occupancy_check_utility; ?>" />
                    <input type="hidden" name="is_budget_check_utility" id="hidden_is_budget_check_utility" value="<?php echo $is_budget_check_utility; ?>" />
                    <input type="hidden" name="is_percent_check" id="hidden_is_percent_check" value="<?php echo $is_percent_check; ?>" />
                    <input type="hidden" name="onclickUtility" id="hidden_onclickUtility" value="0" />
                    <input type="hidden" name="onclickProgress" id="hidden_onclickProgress" value="0" />
                    <input type="hidden" name="onclickPerformance" id="hidden_onclickPerformance" value="1" />
                    <input type="hidden" name="utility_type_list" id="hidden_utility_type_list" value="<?php echo $selected_utility; ?>" />
                    <input type="hidden" name="utility_chart_year" id="hidden_utility_chart_year" value="<?php echo $utility_chart_year; ?>" />
                    <input type="hidden" name="progress_chart_year" id="hidden_progress_chart_year" value="<?php echo $progress_chart_year; ?>" />
                    <input type="hidden" name="progress_chart_utility" id="hidden_progress_chart_utility" value="<?php echo $progress_chart_utility; ?>" />
                    <div class="col-sm-5">
                        <div class="col-sm-12">
                            <i><img src="images/graph.png" alt="Graph"></i>
                            <?php echo lang('performance-chart-label'); ?>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <label class="control-label col-sm-4" style="padding-left: 51px;padding-top: 7px;">
                            Choose Chart:
                        </label>
                        <div class="form-dropdown col-sm-8">
                            <?php
                            // 'scope_1_+_2_emissions' => 'Scope 1 + 2 Emissions',
                            // 'scope_1_+_2_emissions_per_square_footage' => 'Scope 1 + 2 Emissions (per square footage)',
                            // 'food_and_beverage_waste' => 'Food and Beverage Waste',
                            // 'food_and_beverage_waste_total_food_handled' => 'Food and Beverage Waste/Total Food Handled (Food Cover)',
                            // 'food_and_beverage_waste_room_night' => 'Food and Beverage Waste/Room-Night',
                            $performanceChartConstant = [
                                'utility_consumption'  => 'Total Energy Consumption',
                                'utility_consumption_intesity_per_square_footage' => 'Total Energy Consumption Intensity (per square '.getLocalUnitFullText($site_id).')',
                                'utility_consumption_intensity_per_room_night' => 'Total Energy Consumption Intensity (per room-night)',
                                'diversion_rate' => 'Diversion Rate',
                                'utility_cost' => 'Utility Cost',
                                'utility_cost_intensity_per_square_footage' => 'Utility Cost Intensity (per square  '.getLocalUnitFullText($site_id).')',
                                'utility_cost_intensity_per_room_night' => 'Utility Cost Intensity (per room-night)',
                                'budget_vs_total_utility_cost' => 'Budget vs total utility cost',
                                'renewable_energy_generated' => 'Renewable Energy Generated',
                                'renewable_energy_generated_intensity' => 'Renewable Energy Generated Intensity',
                                'tonnes_of_carbon_offsets_purchased' => 'Tonnes of carbon offsets purchased'
                            ];
                            ?>
                            <select onchange="this.form.submit()" name="performance_chart_type" data-type="custom-dropdown" id="progress_select" class="chart_dropdown">
                                <?php
                                foreach ($performanceChartConstant as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($key == $performance_chart_type) ? "selected" : ""; ?>><?php echo $value; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="col-sm-1">
                        <button type="submit" class="btn btn-success" name="performance_chart_button" value="1" style="padding: 10px;">
                            <img src="<?php echo base_url(); ?>themes/default/images/search-icon.png"></button>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col-sm-7">
                        <div style="margin-left: 15px;">
                            <a href="#" data-toggle="tooltip" data-container="article" data-placement="right" title="info" data-original-title="info"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <?php if (
                            $performanceReportData['report_title'] != 'Diversion Rate' && $performanceReportData['report_title'] != 'Food and Beverage Waste'
                            && $performanceReportData['report_title'] != 'Food and Beverage Waste/Total Food Handled (Food Cover)'
                            && $performanceReportData['report_title'] != 'Food and Beverage Waste/Room-Night'
                            && $performanceReportData['report_title'] != 'Tonnes of carbon offsets purchased'
                        ) { ?>
                            <div class="col-sm-6">
                                <input type="checkbox" class="larger" id="is_occupancy_check" name="is_occupancy_check" value="<?php echo $is_occupancy_check; ?>" style="padding-right:0px;">
                                <label class="control-label">Occupancy</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="checkbox" class="larger" id="is_baseline_year" name="is_baseline_year" value="<?php echo $is_baseline_year; ?>" style="padding-right:0px;">
                                <label class="control-label">Baseline Year</label>
                            </div>
                        <?php } ?>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="article-content">
            <?php if (isset($performanceReportData)) { ?>
                <div id="performance_chart" style="min-width: 310px;height: 500px;margin: 0 auto"></div>
            <?php } ?>
        </div>
    </article>
</div>
<!-- Performance Chart END-->
<!-- Monthly Utility Chart START-->
<div class="graph-outer">
    <article class="card">
        <div class="article-header">
            <form name="form-utility" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard' ?>">
                <div class="row">
                    <input type="hidden" name="is_occupancy_check" id="hidden_is_occupancy_check" value="<?php echo $is_occupancy_check; ?>" />
                    <input type="hidden" name="is_baseline_year" id="hidden_is_baseline_year" value="<?php echo $is_baseline_year; ?>" />
                    <input type="hidden" name="is_percent_check" id="hidden_is_percent_check" value="<?php echo $is_percent_check; ?>" />
                    <input type="hidden" name="onclickUtility" id="hidden_onclickUtility" value="1" />
                    <input type="hidden" name="onclickProgress" id="hidden_onclickProgress" value="0" />
                    <input type="hidden" name="onclickPerformance" id="hidden_onclickPerformance" value="0" />
                    <input type="hidden" name="progress_chart_year" id="hidden_progress_chart_year" value="<?php echo $progress_chart_year; ?>" />
                    <input type="hidden" name="progress_chart_utility" id="hidden_progress_chart_utility" value="<?php echo $progress_chart_utility; ?>" />
                    <input type="hidden" name="performance_chart_type" id="hidden_performance_chart_type" value="<?php echo $performance_chart_type; ?>" />

                    <div class="col-sm-4">
                        <i><img src="images/graph.png" alt="Graph"></i>
                        <?php echo lang('total-energy-usage-label'); ?> <?php echo $filters['start_year']; ?> <?php echo lang('to-label'); ?> <?php echo $filters['start_year'] - 1; ?> <?php //echo lang('kwh-label-'.$filter_utility_type); 
                                                                                                                                                                                        ?>
                    </div>

                    <!-- Utilities Dropdown -->
                    <div class="col-sm-4">
                        <label class="control-label col-sm-4">
                            Choose Utility :
                        </label>
                        <div class="form-dropdown col-sm-8">
                            <select onchange="this.form.submit()" name="utility_type_list" data-type="custom-dropdown-update-utility-type-list" id="utility_type_list">
                                <?php
                                if (!empty($mUtilities)) {
                                    foreach ($mUtilities as $key => $value) {
                                        $selected = '';
                                        if ($value == $selected_utility) {
                                            $selected = 'selected';
                                        }
                                ?>
                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                            <?php echo $value; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label col-sm-5">
                            Choose year :
                        </label>
                        <div class="form-dropdown col-sm-7">
                            <select onchange="this.form.submit()" name="utility_chart_year" data-type="custom-dropdown-update-utility-year" id="utility_select">
                                <?php
                                for ($i = date('Y') - 3; $i <= date('Y'); $i++) { ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($i == $utility_chart_year) ? "selected" : ""; ?>><?php echo $i; ?></option>
                                <?php }
                                ?>
                            </select>
                        </div>
                        <!-- <div class="col-sm-3"> -->
                        <!-- <button type="button" class="btn btn-success" onclick="this.form.submit()" style="padding: 10px;">
                                <img src="<?php echo base_url(); ?>themes/default/images/search-icon.png"></button> -->
                        <!-- </div> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <a href="#" data-toggle="tooltip" data-container="article" data-placement="right" title="info" data-original-title="info"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-sm-4">
                        <div class="col-sm-6">
                            <input type="checkbox" class="larger" id="is_occupancy_check_utility" name="is_occupancy_check_utility" value="<?php echo $is_occupancy_check_utility; ?>" style="padding-right:0px;">
                            <label class="control-label">Occupancy</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="checkbox" class="larger" id="is_budget_check_utility" name="is_budget_check_utility" value="<?php echo $is_budget_check_utility; ?>" style="padding-right:0px;">
                            <label class="control-label">Budget</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="article-content">
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="year">
                    <div class="row">
                        <div class="col-sm-12">
                            <div style="width: 100%; height: 500px;" id="utility_chart">
                                <?php if (empty($reportdata)) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <td><?php echo lang('no-records') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>
<!-- Monthly Utility Chart END -->
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    // Monthly Utility Chart
    var previoustooltipArray = <?php echo json_encode($previousdata_tooltip_array[0]); ?>;
    var currenttooltipArray = <?php echo json_encode($currentdata_tooltip_array[0]); ?>;
    var occupancytooltipArray = <?php echo json_encode($occupancydata_tooltip_array[0]); ?>;
    var previousoccupancytooltipArray = <?php echo json_encode($previousoccupancydata_tooltip_array[0]); ?>;
    var currentmonthArray = <?php echo json_encode($currentArray); ?>;
    $(function() {
        var onclickUtility = "<?php echo $onclickUtility; ?>";
        Highcharts.setOptions({
            lang: {
                numericSymbols: null,
                thousandsSep: ','
            }
        });
        Highcharts.chart('utility_chart', {
            chart: {
                events: {
                    load: function(event) {
                        if (onclickUtility == 1) {
                            $('html,body').animate({
                                scrollTop: $('#utility_chart').offset().top - 100
                            }, 'slow');
                        }
                    }
                },
                zoomType: 'xy'
            },
            lang: {
                numericSymbols: null //otherwise by default ['k', 'M', 'G', 'T', 'P', 'E']
            },
            title: {
                text: '<?php echo lang("report-title-" . $filter_utility_type); ?>',
                style: {
                    color: Highcharts.getOptions().colors[1],
                    fontFamily: 'Arial',
                    fontSize: '28px'
                }
            },
            xAxis: {
                categories: [<?php echo '"' . implode('","', $monthdata) . '"'; ?>],
                title: {
                    text: '<?php echo lang("haxis-title"); ?>',
                    style: {
                        color: Highcharts.getOptions().colors[1],
                        fontFamily: 'Arial',
                    }

                }

            },

            yAxis: [{
                    title: {
                        text: '<?php echo lang("kwh-label-" . $filter_utility_type); ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }

                    }
                },
                <?php if (isset($is_occupancy_check_utility) && !empty(($is_occupancy_check_utility))) { ?> {
                        title: {
                            text: '<?php echo lang("occupancy"); ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial'
                            }
                        },
                        opposite: true
                    }
                <?php } ?>
            ],
            tooltip: {
                formatter: function() {
                    var point = this;
                    previoustooltipdata = 0;
                    currenttooltipdata = 0;

                    for (var i = 0; i <= this.point.x; i++) {
                        previoustooltipdata = (typeof(previoustooltipArray[i]) != "undefined") ? previoustooltipArray[i] : 0;
                        currenttooltipdata = (typeof(currenttooltipArray[i]) != "undefined") ? currenttooltipArray[i] : 0;
                        currentmonth = currentmonthArray[i];
                    }

                    if (point.series.name == "Previous Year") {
                        return "<b>" + previoustooltipdata + "</b>";
                    } else if (point.series.name == "Current Year") {
                        return "<b>" + currenttooltipdata + "</b>";
                    } else {
                        return "<b>" + currentmonth + "</b><br>" + point.series.name + ': <b>' + point.y + '</b>';
                    }
                },
                style: {
                    fontFamily: "Arial",
                    fontSize: "16px",
                    color: "#222222"
                }
            },
            plotOptions: {
                series: {
                    states: {
                        inactive: {
                            enabled: false
                        }
                    }
                }
            },
            animation: {
                duration: 500,
                startup: true
            },
            credits: {
                enabled: false
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'rgba(255,255,255,0.25)'
            },
            series: [{
                    events: {
                        legendItemClick: function() {
                            return false;
                        }
                    },
                    name: 'Previous Year',
                    type: 'column',
                    color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($utility_chart_year - 1)]; ?>',
                    yAxis: 0,
                    data: [<?php echo implode(',', $previousdata_array); ?>],

                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#dc3912'
                },
                {
                    events: {
                        legendItemClick: function() {
                            return false;
                        }
                    },
                    name: 'Current Year',
                    color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$utility_chart_year]; ?>',
                    type: 'column',
                    yAxis: 0,
                    data: [<?php echo implode(',', $currentdata_array); ?>],
                    tooltip: {
                        useHTML: true,
                        formatter: function() {
                            return <?php echo $currentdata_tooltip ?>;
                        }
                    },
                },
                <?php if (isset($is_occupancy_check_utility) && !empty(($is_occupancy_check_utility))) { ?> {
                        events: {
                            legendItemClick: function() {
                                return false;
                            }
                        },
                        name: 'Previous Occupancy',
                        color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($utility_chart_year - 1)]; ?>',
                        type: 'spline',
                        yAxis: 1,
                        data: [<?php echo implode(',', $previousoccupancydata_array); ?>]

                    }, {
                        events: {
                            legendItemClick: function() {
                                return false;
                            }
                        },
                        name: 'Current Occupancy',
                        color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$utility_chart_year]; ?>',
                        type: 'spline',
                        yAxis: 1,
                        data: [<?php echo implode(',', $occupancydata_array); ?>]

                    },
                <?php } ?>
                <?php if (isset($is_budget_check_utility) && !empty(($is_budget_check_utility))) { ?> {
                        events: {
                            legendItemClick: function() {
                                return false; // <== returning false will cancel the default action
                            }
                        },
                        name: 'Budget',
                        type: 'spline',
                        color: '#ff9900',
                        xAxis: 0,
                        data: [<?php echo implode(',', $budgetdata_array); ?>],
                        tooltip: {
                            useHTML: true
                        },
                        marker: {
                            enabled: false
                        }
                    }
                <?php } ?>
            ]
        });
    });
    // Performance Chart
    <?php if (isset($performanceReportData) && isset($performanceReportData['performanceReportArray'])) { ?>
        var performanceReportData = '<?php echo json_encode($performanceReportData['performanceReportArray']) ?>';
        var performanceReportArrayData = JSON.parse(performanceReportData);
        var result = Object.keys(performanceReportArrayData).map((key) => performanceReportArrayData[key]);
        let currentYear = new Date().getFullYear();
        let PreviousYear = '<?php echo ($is_baseline_year) ? $site_detials['baseline_regression_year'] : date('Y') - 1; ?>';
        let performanceReportPreviousYear = [];
        let performanceReportCurrentYear = [];
        let performanceReportPreviousOccupancyYear = [];
        let performanceReportCurrentOccupancyYear = [];
        let performanceReportPreviousBudgetYear = [];
        let performanceReportCurrentBudgetYear = [];
        result.forEach(element => {
            Object.keys(element).map((key) => {
                if (key == PreviousYear) {
                    performanceReportPreviousYear.push({
                        y: Number(Number(element[key]).toFixed(4)),
                    });
                }
                if (key == currentYear) {
                    performanceReportCurrentYear.push({
                        y: Number(Number(element[key]).toFixed(4)),
                    });
                }
                if (key == (PreviousYear + '_occupancy')) {
                    performanceReportPreviousOccupancyYear.push({
                        y: Number(Number(element[key]).toFixed(2)),
                    });
                }
                if (key == (currentYear + '_occupancy')) {
                    performanceReportCurrentOccupancyYear.push({
                        y: Number(Number(element[key]).toFixed(2)),
                    });
                }
                if (key == (PreviousYear + '_budget')) {
                    performanceReportPreviousBudgetYear.push({
                        y: Number(Number(element[key]).toFixed(2)),
                    });
                }
                if (key == (currentYear + '_budget')) {
                    performanceReportCurrentBudgetYear.push({
                        y: Number(Number(element[key]).toFixed(2)),
                    });
                }
            });
        });
        var ChartType = "<?php echo $performanceReportData['report_title']; ?>";
        if (ChartType != 'Diversion Rate') {
            var performancePercentArray = [];
            for (let i = 0; i < performanceReportCurrentYear.length; i++) {
                var currentPercentYear = 0;
                var previousPercentYear = 0;
                var difference_value = 0;
                var performancePercentage = 0;
                currentPercentYear = (typeof(performanceReportCurrentYear[i]) != "undefined") ? performanceReportCurrentYear[i]['y'] : 0;
                previousPercentYear = (typeof(performanceReportPreviousYear[i]) != "undefined") ? performanceReportPreviousYear[i]['y'] : 0;
                difference_value = currentPercentYear - previousPercentYear;
                if (currentPercentYear > 0) {
                    performancePercentage = ((difference_value * 100) / currentPercentYear);
                } else {
                    performancePercentage = 100;
                }
                performancePercentArray.push(performancePercentage);
            }
        }
        var OccupancyChecked = "<?php echo $is_occupancy_check; ?>";
        if (ChartType == 'Diversion Rate') {
            var series = [{
                name: '<?php echo date('Y'); ?>',
                data: performanceReportCurrentYear,
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')]; ?>',
                tooltip: {
                    useHTML: true,
                    valueSuffix: '<?php echo $performanceReportData['unit']; ?>',
                    formatter: function() {
                        return '<span style="font-size:10px">' + this.category + '</span><table>' +
                            '<tr><td style="color:' + this.series.color + ';padding:0">' + this.series.name + ': </td>' +
                            '<td style="padding:0"><b>' + this.y + '</b></td></tr>' +
                            '</table>';
                    },
                },
            }];
        } else if (ChartType == 'Budget vs total utility cost') {
            var series = [{
                name: '<?php echo ($is_baseline_year) ? $site_detials['baseline_regression_year'] : date('Y') - 1; ?>',
                data: performanceReportPreviousYear,
                color: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>',
            }, {
                name: '<?php echo date('Y'); ?>',
                data: performanceReportCurrentYear,
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')]; ?>',
            }, {
                name: 'Budget <?php echo ($is_baseline_year) ? $site_detials['baseline_regression_year'] : date('Y') - 1; ?>',
                type: 'spline',
                color: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>',
                data: performanceReportPreviousBudgetYear,
                marker: {
                    enabled: false
                }
            }, {
                name: 'Budget <?php echo date('Y') ?>',
                type: 'spline',
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')] ?>',
                data: performanceReportCurrentBudgetYear,
                marker: {
                    enabled: false
                }
            }, ];
        } else {
            var series = [{
                name: '<?php echo ($is_baseline_year) ? $site_detials['baseline_regression_year'] : date('Y') - 1; ?>',
                data: performanceReportPreviousYear,
                color: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>',
            }, {
                name: '<?php echo date('Y'); ?>',
                data: performanceReportCurrentYear,
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')]; ?>',
            }];
        }
        if (OccupancyChecked == 1) {
            series.push({
                type: 'spline',
                name: 'Occupancy <?php echo ($is_baseline_year) ? $site_detials['baseline_regression_year'] : date('Y') - 1; ?>',
                yAxis: 1,
                data: performanceReportPreviousOccupancyYear,
                tooltip: {
                    useHtml: true,
                    valueSuffix: '%',
                    formatter: function() {
                        return '<span style="font-size:10px">' + this.category + '</span><table>' +
                            '<tr><td style="color:' + this.series.color + ';padding:0">' + this.series.name + ': </td>' +
                            '<td style="padding:0"><b>' + this.y + '</b></td></tr>' +
                            '</table>';
                    },
                },
                marker: {
                    lineWidth: 2,
                    symbol: 'square',
                    lineColor: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>',
                    fillColor: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>'
                },
                color: '<?php echo ($is_baseline_year) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][date('Y') - 1] ?>',
            });
            series.push({
                type: 'spline',
                name: 'Occupancy <?php echo date('Y'); ?>',
                yAxis: 1,
                data: performanceReportCurrentOccupancyYear,
                tooltip: {
                    useHtml: true,
                    valueSuffix: '%',
                    formatter: function() {
                        return '<span style="font-size:10px">' + this.category + '</span><table>' +
                            '<tr><td style="color:' + this.series.color + ';padding:0">' + this.series.name + ': </td>' +
                            '<td style="padding:0"><b>' + this.y + '</b></td></tr>' +
                            '</table>';
                    },
                },
                marker: {
                    lineWidth: 2,
                    symbol: 'square',
                    lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')] ?>',
                    fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')] ?>'
                },
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')] ?>'
            });
        }
        $(function() {
            var onclickPerformance = "<?php echo $onclickPerformance; ?>";
            const chart = Highcharts.chart('performance_chart', {
                <?php if ($performanceReportData['report_title'] == 'Diversion Rate') { ?>
                    chart: {
                        events: {
                            load: function(event) {
                                if (onclickPerformance == 1) {
                                    $('html,body').animate({
                                        scrollTop: $('#performance_chart').offset().top - 100
                                    }, 'slow');
                                }
                            }
                        },
                        type: 'area'
                    },
                <?php } else { ?>
                    chart: {
                        events: {
                            load: function(event) {
                                if (onclickPerformance == 1) {
                                    $('html,body').animate({
                                        scrollTop: $('#performance_chart').offset().top - 100
                                    }, 'slow');
                                }
                            }
                        },
                        type: 'column'
                    },
                <?php } ?>
                title: {
                    text: '<?php echo $performanceReportData['report_title']; ?>',
                    style: {
                        color: Highcharts.getOptions().colors[1],
                        fontFamily: 'Arial',
                        fontSize: '28px'
                    }
                },
                xAxis: {
                    categories: [
                        'JAN',
                        'FEB',
                        'MAR',
                        'APR',
                        'MAY',
                        'JUN',
                        'JUL',
                        'AUG',
                        'SEP',
                        'OCT',
                        'NOV',
                        'DEC',
                        'AVG'
                    ],
                    crosshair: true
                },
                credits: {
                    enabled: false
                },
                <?php if (
                    $performanceReportData['report_title'] == 'Diversion Rate' || $performanceReportData['report_title'] == 'Food and Beverage Waste'
                    || $performanceReportData['report_title'] == 'Food and Beverage Waste/Total Food Handled (Food Cover)'
                    || $performanceReportData['report_title'] == 'Food and Beverage Waste/Room-Night'
                    || $performanceReportData['report_title'] == 'Tonnes of carbon offsets purchased'
                ) { ?>
                    yAxis: [{
                        min: 0,
                        title: {
                            text: '<?php echo $performanceReportData['y_axis']; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        }
                    }],
                <?php } else { ?>
                    yAxis: [{
                        min: 0,
                        title: {
                            text: '<?php echo $performanceReportData['y_axis']; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                    }, {
                        min: 0,
                        title: {
                            text: '<?php echo $performanceReportData['opposite_Y_axis']; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                        opposite: true
                    }],
                <?php } ?>
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                tooltip: {
                    formatter: function() {
                        var point = this;
                        currenttooltippercent = 0;
                        if (ChartType != 'Diversion Rate') {
                            for (var i = 0; i <= this.point.x; i++) {
                                currenttooltippercent = (typeof(performancePercentArray[i]) != "undefined") ? performancePercentArray[i] : 0;
                            }
                        }

                        var currentYear = '<?php echo date('Y'); ?>';
                        if (point.series.name == currentYear && ChartType != 'Diversion Rate') {
                            return '<span style="font-size:13px">' + this.point.category + ', ' + currenttooltippercent.toFixed(2) + ' % </span><br><table>' +
                                '<tr><td style="color:' + point.series.color + ';padding:0">' + point.series.name + ': </td>' +
                                '<td style="padding:0"><b>' + point.y + '</b></td></tr>' +
                                '</table>';
                        } else if (point.series.name == currentYear && ChartType == 'Diversion Rate') {
                            return '<span style="font-size:13px">' + this.point.category + '</span><br><table>' +
                                '<tr><td style="color:' + point.series.color + ';padding:0">' + point.series.name + ': </td>' +
                                '<td style="padding:0"><b>' + point.y + '</b></td></tr>' +
                                '</table>';
                        } else {
                            return '<span style="font-size:13px">' + this.point.category + '</span><br><table>' +
                                '<tr><td style="color:' + point.series.color + ';padding:0">' + point.series.name + ': </td>' +
                                '<td style="padding:0"><b>' + point.y + '</b></td></tr>' +
                                '</table>';
                        }
                    },
                    style: {
                        fontFamily: "Arial",
                        fontSize: "16px",
                        color: "#222222"
                    }
                },
                series: series,
            });
        });
    <?php } ?>
    // Occupancy and baseline checkbox value set/unset script
    $(function() {
        var is_occupancy_check_utility = "<?php echo $is_occupancy_check_utility; ?>";
        if (is_occupancy_check_utility == 1) {
            $("#is_occupancy_check_utility").prop('checked', true);
        } else {
            $("#is_occupancy_check_utility").prop('checked', false);
        }

        var is_occupancy_check = "<?php echo $is_occupancy_check; ?>";
        if (is_occupancy_check == 1) {
            $("#is_occupancy_check").prop('checked', true);
        } else {
            $("#is_occupancy_check").prop('checked', false);
        }

        var is_baseline_year = "<?php echo $is_baseline_year; ?>";
        if (is_baseline_year == 1) {
            $("#is_baseline_year").prop('checked', true);
        } else {
            $("#is_baseline_year").prop('checked', false);
        }
        var is_percent_check = "<?php echo $is_percent_check; ?>";
        if (is_percent_check == 1) {
            $("#is_percent_check").prop('checked', true);
        } else {
            $("#is_percent_check").prop('checked', false);
        }
        var is_budget_check_utility = "<?php echo $is_budget_check_utility; ?>";
        if (is_budget_check_utility == 1) {
            $("#is_budget_check_utility").prop('checked', true);
        } else {
            $("#is_budget_check_utility").prop('checked', false);
        }
    });
    // Utility Occupancy checkbox change event
    $("#is_occupancy_check_utility").change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        this.form.submit();
    });
    // Utility Budget Checkbox change event
    $("#is_budget_check_utility").change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        this.form.submit();
    });
    // Occupancy checkbox change event
    $("#is_occupancy_check").change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        this.form.submit();
    });
    // Baseline checkbox change event
    $("#is_baseline_year").change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        this.form.submit();
    });
    // progress % checkbox change event
    $("#is_percent_check").change(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
        this.form.submit();
    });
    // Progress Chart
    $(function() {
		var onclickProgress = "<?php echo $onclickProgress; ?>";
		<?php if ($progress_chart_year != 'eui_by_energy_composition') { ?>
        var progressSeries = [];
        <?php if (!empty($dataPrev) && !empty($dataCurrent)) { ?>
            var previousDataChart = JSON.parse('<?php echo json_encode($dataPrev); ?>');
            var currentDataChart = JSON.parse('<?php echo json_encode($dataCurrent); ?>');
            var dataBudget = JSON.parse('<?php echo json_encode($dataBudget); ?>');
            <?php if ($progress_chart_year == $site_detials['baseline_regression_year'] || $progress_chart_year == date('Y') - 1) {
                $seriesName = $progress_chart_year;
                $color = $progress_chart_year == $site_detials['baseline_regression_year'] ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][(date('Y') - 1)];
            } else if ($progress_chart_year == 'on_site_renewable-' . $prevYear || $progress_chart_year == 'on_site_renewable-' . $baselineYear) {
                $seriesName = ucfirst(str_replace('-', ' ', str_replace('_', ' ', $progress_chart_year)));
                $color = (ltrim($seriesName, "On site renewable") == $site_detials['baseline_regression_year']) ? $this->_ci->config->config['chart_legend_colors'][$site_detials['baseline_regression_year']] : $this->_ci->config->config['chart_legend_colors'][(date('Y') - 1)];
            }
            ?>
            progressSeries.push({
                data: previousDataChart,
                name: '<?php echo $seriesName; ?>',
                color: '<?php echo $color; ?>',
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} ' + '<?php echo $progress_chart_utility == 'energy' ? GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id) : GetSiteUtilityUnitName($site_id, 'water') . '/' . getLocalUnitText($site_id); ?>' + '</b>'
                },
            }, {
                data: currentDataChart,
                name: '<?php echo date('Y'); ?>',
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')]; ?>',
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} ' + '<?php echo $progress_chart_utility == 'energy' ? GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id) : GetSiteUtilityUnitName($site_id, 'water') . '/' . getLocalUnitText($site_id); ?>' + '</b>'
                },
            }, {
                type: 'line',
                name: 'Target',
                data: dataBudget,
                dashStyle: 'longdash',
                lineWidth: 2,
                marker: {
                    radius: 1,
                    symbol: 'circle',
                    lineColor: 'black',
                    fillColor: 'black',
                    enabled: false
                },
                color: 'black',
            });
        <?php } else if (!empty($dataChart)) { ?>
            var dataChart = JSON.parse('<?php echo json_encode($dataChart); ?>')
            progressSeries.push({
                data: dataChart,
                name: '<?php echo date('Y'); ?>',
                color: '<?php echo $this->_ci->config->config['chart_legend_colors'][date('Y')]; ?>'
            });
        <?php } ?>
        var is_percent_check = '<?php echo $is_percent_check; ?>';
        if (is_percent_check == 1) {
            var dataSavingPercentage = JSON.parse('<?php echo json_encode($dataSavingPercentage); ?>');
            progressSeries.push({
                type: 'line',
                name: '% Saving',
                data: dataSavingPercentage,
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} %</b>'
                },
                yAxis: 1,
                dashStyle: 'longdash',
                marker: {
                    lineWidth: 2,
                    symbol: 'circle',
                    lineColor: 'red',
                    fillColor: 'red',
                    enabled: false
                },
                color: 'red',
            });
        }
        const chart = Highcharts.chart('progress_chart', {
            chart: {
                events: {
                    load: function(event) {
                        if (onclickProgress == 1) {
                            $('html,body').animate({
                                scrollTop: $('#progress_chart').offset().top - 100
                            }, 'slow');
                        }
                    }
                },
                type: 'area'
            },
            title: {
                text: '<?php echo $chartTitle; ?>',
                style: {
                    color: Highcharts.getOptions().colors[1],
                    fontFamily: 'Arial',
                    fontSize: '28px'
                }
            },
            plotOptions: {
                series: {
                    fillOpacity: 0.25,
                },
                area: {
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            legend: {
                enabled: true,
                symbolWidth: 40
            },
            tooltip: {
                pointFormat: '<b>{series.name} : {point.y:,.2f}</b>'
            },
            xAxis: {
                crosshair: {
                    enabled: true,
                    width: 2,
                    color: 'brown',
                    dashStyle: 'longdash',
                    fillOpacity: 0.25
                },
                title: {
                    text: 'Months',
                    margin: 20,
                    style: {
                        color: Highcharts.getOptions().colors[1],
                        fontFamily: 'Arial',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    },
                },
                categories: [
                    'JAN',
                    'FEB',
                    'MAR',
                    'APR',
                    'MAY',
                    'JUN',
                    'JUL',
                    'AUG',
                    'SEP',
                    'OCT',
                    'NOV',
                    'DEC'
                ],
            },
            credits: {
                enabled: false
            },
            yAxis: [{
                    labels: {
                        format: '{value}',
                    },
                    title: {
                        text: '<?php echo $progress_chart_utility == 'energy' ? GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id) : GetSiteUtilityUnitName($site_id, 'water') . '/' . getLocalUnitText($site_id); ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    showFirstLabel: false
                },
                <?php if ($is_percent_check == 1) { ?> {
                        labels: {
                            format: '{value} %'
                        },
                        title: {
                            rotation: 270,
                            margin: 30,
                            text: '% Saving',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                        opposite: true,
                        showFirstLabel: false
                    }
                <?php } ?>
            ],
            series: progressSeries,
            exporting: {
                allowHTML: true
            }
        });
			<?php } else { ?>
                var electricity = gases = others = dataBudget = '';
                <?php if ((isset($electricity) && json_encode($electricity) != '' && !empty($electricity))) { ?>
                    electricity = JSON.parse('<?php echo json_encode($electricity); ?>');
                <?php }?>
                <?php if ((isset($gases) && json_encode($gases) != '' && !empty($gases))) { ?>
                    gases = JSON.parse('<?php echo json_encode($gases); ?>');
                <?php }?>
                <?php if ((isset($others) && json_encode($others) != '' && !empty($others))) { ?>
                    others = JSON.parse('<?php echo json_encode($others); ?>');
                <?php }?>
                <?php if ((isset($dataBudget) && json_encode($dataBudget) != '' && !empty($dataBudget))) { ?>
                    dataBudget = JSON.parse('<?php echo json_encode($dataBudget); ?>');
                <?php }?>
			const chart = Highcharts.chart('progress_chart', {
				chart: {
					events: {
						load: function(event) {
							if (onclickProgress == 1) {
								$('html,body').animate({
									scrollTop: $('#progress_chart').offset().top - 100
								}, 'slow');
							}
						}
					},
					type: 'area'
				},
				title: {
					text: '<?php echo $chartTitle; ?>',
					style: {
						color: Highcharts.getOptions().colors[1],
						fontFamily: 'Arial',
						fontSize: '24px',
					}
				},
				xAxis: {
					crosshair: {
						enabled: true,
						width: 2,
						color: 'brown',
						dashStyle: 'longdash',
						fillOpacity: 0.25
					},
					categories: [
						'JAN',
						'FEB',
						'MAR',
						'APR',
						'MAY',
						'JUN',
						'JUL',
						'AUG',
						'SEP',
						'OCT',
						'NOV',
						'DEC'
					]
				},
				yAxis: {
					title: {
						useHTML: true,
						text: '<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					series: {
						fillOpacity: 0.5,
					},
					area: {
						stacking: 'normal',
						lineColor: '#fcfffd',
						lineWidth: 2,
						marker: {
							lineWidth: 2,
							lineColor: '#fcfffd',
							symbol: 'circle'
						}
					}
				},
				series: [{
					name: 'Electricity',
					data: electricity,
					color: '#a8cd41',
					tooltip: {
						pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
							'<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id); ?>' + '</b>'
					},
				}, {
					name: 'Gas',
					data: gases,
					color: '#f2a041',
					tooltip: {
						pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
							'<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id); ?>' + '</b>'
					},
				}, {
					name: 'Others',
					data: others,
					color: '#3b4255',
					tooltip: {
						pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
							'<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/' . getLocalUnitText($site_id); ?>' + '</b>'
					},
				}, {
					type: 'line',
					name: 'Target',
					data: dataBudget,
					dashStyle: 'longdash',
					lineWidth: 2,
					marker: {
						radius: 1,
						symbol: 'circle',
						lineColor: 'black',
						fillColor: 'black',
						enabled: false
					},
					tooltip: {
						pointFormat: '<b>{series.name} : {point.y:,.2f} </b>'
					},
					color: 'black',
				}]
			});
		<?php } ?>
    });
    $(function() {
        <?php
        if (!empty($cost_pie_chart_previousmonth)) {
            foreach ($cost_pie_chart_previousmonth as $costPiePreKey => $costPiePreVal) {
                if ($costPiePreVal != 0) {
                    $costPiePreName = lang($costPiePreKey);
                    $costPiePreSeriesArray[$costPiePreName] .= $costPiePreVal;
                }
            }
        ?>
            var costPieChartPreviousMonth = '<?php echo json_encode($costPiePreSeriesArray); ?>';
            if (costPieChartPreviousMonth != 'null') {
                var costPieChartPreviousMonthData = JSON.parse(costPieChartPreviousMonth);
                var utilityCostSeries = [];
                if(costPieChartPreviousMonthData != null && costPieChartPreviousMonthData != undefined && typeof (costPieChartPreviousMonthData) == 'object') {
                Object.entries(costPieChartPreviousMonthData).forEach(([key, value]) => {
                    if (key == 'Electricity') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['Electricity']; ?>'
                        }, );
                    }
                    if (key == 'Fuel') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['Fuel']; ?>'
                        }, );
                    }
                    if (key == 'Water') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['Water']; ?>'
                        }, );
                    }
                    if (key == 'LPG') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['LPG']; ?>'
                        }, );
                    }
                    if (key == 'Natural Gas') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['Natural_Gas']; ?>'
                        }, );
                    }
                    if (key == 'District Heating') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['District_Heating']; ?>'
                        }, );
                    }
                    if (key == 'District Cooling') {
                        utilityCostSeries.push({
                            name: key,
                            y: Number(costPieChartPreviousMonthData[key]),
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors']['District_Cooling']; ?>'
                        }, );
                    }
                });
                }
            }
            Highcharts.chart('utility_pie_highchart', {
                chart: {
                    height: 190,
                    marginTop: 20,
                    marginRight: 120,
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    formatter: function() {
                        return '<b>' + this.point.name + '</b> : <b> ' + this.point.percentage.toFixed(1) + ' %</b>';
                    },
                    shadow: false
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    x: 20,
                    y: 0,
                    layout: 'vertical',
                    enabled: true,
                    align: 'right',
                    verticalAlign: 'bottom',
                    itemStyle: {
                        fontSize: '11px',
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        size: 100,
                        align: 'right',
                        showInLegend: true,
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                return '<b>' + this.point.percentage.toFixed(1) + '%</b>';
                            },
                            style: {
                                fontSize: '10px',
                                textOutline: false,
                            },
                            distance: -45,
                            filter: {
                                property: 'percentage',
                                operator: '>',
                                value: 0
                            }
                        }
                    }
                },
                series: [{
                    states: {
                        hover: {
                            enabled: false
                        }
                    },
                    colorByPoint: true,
                    size: '140%',
                    innerSize: '20%',
                    data: utilityCostSeries
                }]
            });
        <?php } ?>
    });
    // Dropkick dropdown scripts
    $(function() {
        $("select[data-type='custom-dropdown-update-progress-utility']").dropkick({
            mobile: true
        });
        $("select[data-type='custom-dropdown-update-progress-year']").dropkick({
            mobile: true
        });
        $("select[data-type='custom-dropdown-update-utility-year']").dropkick({
            mobile: true
        });
        <?php if (!empty($mUtilities)) { ?>
            $("select[data-type='custom-dropdown-update-utility-type-list']").dropkick({
                mobile: true
            });
        <?php } ?>
    });
</script>
