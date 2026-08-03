<?php
$notification_list = '';
extract($site);
$filters = array();
$filters['current_month'] = (int) date('m');
$filters['current_year'] = date('Y');
$baseline_year = $site['baseline_regression_year'];
$current_month = date('m');
$current_year  = date('Y');//2022
$baseline_year = $site['baseline_regression_year'];
//Creation of array for progress on target widget
$progressOnTarget = isset($site['data']['progressOnTarget']) ? $site['data']['progressOnTarget'] : [];
$progressTarget = $ProgressTargetPercentage = [];
if (!empty($progressOnTarget)) {
    $progressEnergyYTD = 0;
    $progressWaterYTD = 0;
    foreach ($progressOnTarget as $key => $value) {
	foreach ($value as $key1 => $value1) {
	    // monthly data of Current year (water and Energy)
	    if ($value1['month_id'] == $current_month - 1 && $value1['year_id'] == $current_year) {
		$progressTarget['energy']['energy_monthly'] = isset($value1['energy_target']) ? $value1['energy_target'] : 0;
		$progressTarget['water']['water_monthly'] = isset($value1['water_target']) ? $value1['water_target'] : 0;
	    }
	    // monthly data of last year (water and Energy)
	    if ($value1['month_id'] == $current_month - 1 && $value1['year_id'] == $current_year - 1) {
		$progressTarget['energy']['energy_last_monthly'] = isset($value1['energy_target']) ? $value1['energy_target'] : 0;
		$progressTarget['water']['water_last_monthly'] = isset($value1['water_target']) ? $value1['water_target'] : 0;
	    }
	    // YTD data of Current and last year (Energy)
	    if ($value1['month_id'] < $current_month && $value1['year_id'] == $current_year) {
		$progressEnergyYTD += $value1['energy_target'];
		$progressTarget['energy']['energy_YTD'] = isset($progressEnergyYTD) ? $progressEnergyYTD : 0;
	    } else if ($value1['month_id'] < $current_month && $value1['year_id'] == $current_year - 1) {
		$progressEnergyYTDLast += $value1['energy_target'];
		$progressTarget['energy']['energy_last_YTD'] = isset($progressEnergyYTDLast) ? $progressEnergyYTDLast : 0;
	    }
	    // YTD data of Curret and last year (water)
	    if ($value1['month_id'] < $current_month && $value1['year_id'] == $current_year) {
		$progressWaterYTD += $value1['water_target'];
		$progressTarget['water']['water_YTD'] = isset($progressWaterYTD) ? $progressWaterYTD : 0;
	    } else if ($value1['month_id'] < $current_month && $value1['year_id'] == $current_year - 1) {
		$progressWaterYTDLast += $value1['water_target'];
		$progressTarget['water']['water_last_YTD'] = isset($progressWaterYTDLast) ? $progressWaterYTDLast : 0;
	    }

	    // YTD for baseline year (WATER)
	    $progressWaterBaselineYTD += $value1['water_baseline_target'];
	    $progressTarget['water']['water_baseline_YTD'] = isset($progressWaterBaselineYTD) ? $progressWaterBaselineYTD : 0;

	    // YTD for baseline year (ENERGY)
	    $progressEnergyBaselineYTD += $value1['energy_baseline_target'];
	    $progressTarget['energy']['energy_baseline_YTD'] = isset($progressEnergyBaselineYTD) ? $progressEnergyBaselineYTD : 0;

	    $carbon_footprint_currentMonth = $site['data']['kpi']['carbon_footprint_currentMonth'];
			$carbon_footprint_SameMonthPreviousYear = $site['data']['kpi']['carbon_footprint_SameMonthPreviousYear'];
			$ytd_carbon_footprint_new = $site['data']['kpi']['ytd_carbon_footprint_new'];
			$ytd_carbon_footprintPreviousYear = $site['data']['kpi']['ytd_carbon_footprintPreviousYear'];
			$ytd_carbon_footprint_baseline_new = $site['data']['kpi']['ytd_carbon_footprint_baseline_new'];
	    // carbon current , last month (monthly) , last year,  current year YTD data
	    $progressTarget['carbon']['carbon_monthly'] = isset($carbon_footprint_currentMonth) ? $carbon_footprint_currentMonth : 0;
	    $progressTarget['carbon']['carbon_last_monthly'] = isset($carbon_footprint_SameMonthPreviousYear) ? $carbon_footprint_SameMonthPreviousYear : 0;
	    $progressTarget['carbon']['carbon_YTD'] = isset($ytd_carbon_footprint_new) ? $ytd_carbon_footprint_new : 0;
	    $progressTarget['carbon']['carbon_last_YTD'] = isset($ytd_carbon_footprintPreviousYear) ? $ytd_carbon_footprintPreviousYear : 0;
	    $progressTarget['carbon']['carbon_baseline_YTD'] = isset($ytd_carbon_footprint_baseline_new) ? $ytd_carbon_footprint_baseline_new : 0;
	    // $progressTarget['waste']['waste_monthly'] = 5;
	    // $progressTarget['waste']['YTD'] = 2;

	}
    }
    $sortKey = array('energy','carbon','water');
    $orderedprogressTargetArray = array();
    foreach ($sortKey as $key) {
	$orderedprogressTargetArray[$key] = $progressTarget[$key];
    }
    // Calculation of Progress on Targets
    // energy_intensity_annual_target% from side edit and Percentage difference from previous year to current year
    foreach ($orderedprogressTargetArray as $key => $value) {
	if ($key == 'energy') {
	    $differenceEnergyMonthly = $value['energy_last_monthly'] - $value['energy_monthly'];
	    $progressEnergyMonthlyTargetValue = (($value['energy_last_monthly'] * $site['energy_intensity_annual_target']) / 100);
	    $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyMonthly, $value['energy_last_monthly']);
	    $ProgressTargetPercentage['Energy']['monthly'] = $ProgressEnergyTargetPercentageValue;
	    $ProgressTargetPercentage['Energy']['site_saving_target'] = $site['energy_intensity_annual_target'];

	    $differenceEnergyYTD = $value['energy_last_YTD'] - $value['energy_YTD'];
	    $progressEnergyYTDTargetValue = (($value['energy_last_YTD'] * $site['energy_intensity_annual_target']) / 100);
	    $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyYTD, $value['energy_last_YTD']);
	    $ProgressTargetPercentage['Energy']['YTD'] = $ProgressEnergyTargetPercentageValue;
	    $ProgressTargetPercentage['Energy']['ACTUAL_YTD'] = $value['energy_YTD'];
	    // Consumption Target YTD
	    $progressEnergyYTDBaselineTargetValue = (($value['energy_baseline_YTD'] * $site['energy_intensity_annual_target']) / 100);
	    $ProgressTargetPercentage['Energy']['TARGET_BASELINE_YTD'] = $value['energy_baseline_YTD'] - $progressEnergyYTDBaselineTargetValue;
	}
	if ($key == 'water') {
	    $differenceWaterMonthly = $value['water_last_monthly'] - $value['water_monthly'];
	    $progressWaterMonthlyTargetValue = (($value['water_last_monthly'] * $site['water_intensity_annual_target']) / 100);
	    $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterMonthly, $value['water_last_monthly']);
	    $ProgressTargetPercentage['Water']['monthly'] = $ProgressWaterTargetPercentageValue;
	    $ProgressTargetPercentage['Water']['site_saving_target'] = $site['water_intensity_annual_target'];

	    $differenceWaterYTD = $value['water_last_YTD'] - $value['water_YTD'];
	    $progressWaterYTDTargetValue = (($value['water_last_YTD'] * $site['water_intensity_annual_target']) / 100);
	    $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterYTD, $value['water_last_YTD']);
	    $ProgressTargetPercentage['Water']['YTD'] = $ProgressWaterTargetPercentageValue;
	    $ProgressTargetPercentage['Water']['ACTUAL_YTD'] = $value['water_YTD'];
	    // Consumption Target YTD
	    $progressWaterYTDBaselineTargetValue = (($value['water_baseline_YTD'] * $site['water_intensity_annual_target']) / 100);
	    $ProgressTargetPercentage['Water']['TARGET_BASELINE_YTD'] = $value['water_baseline_YTD'] - $progressWaterYTDBaselineTargetValue;
	}
	if ($key == 'carbon') {
	    $differenceCarbonMonthly = $value['carbon_last_monthly'] - $value['carbon_monthly'];
	    $progressCarbonMonthlyTargetValue = (($value['carbon_last_monthly'] * $site['ghg_intensity_annual_target']) / 100);
	    $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonMonthly, $value['carbon_last_monthly']);
	    $ProgressTargetPercentage['Carbon']['monthly'] = $ProgressCarbonTargetPercentageValue;
	    $ProgressTargetPercentage['Carbon']['site_saving_target'] = $site['ghg_intensity_annual_target'];

	    $differenceCarbonYTD = $value['carbon_last_YTD'] - $value['carbon_YTD'];
	    $progressCarbonYTDTargetValue = (($value['carbon_last_YTD'] * $site['ghg_intensity_annual_target']) / 100);
	    $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonYTD, $value['carbon_last_YTD']);
	    $ProgressTargetPercentage['Carbon']['YTD'] = $ProgressCarbonTargetPercentageValue;
	    $ProgressTargetPercentage['Carbon']['ACTUAL_YTD'] = $value['carbon_YTD'];
	    // Consumption Target YTD
	    $progressCarbonYTDBaselineTargetValue = (($value['carbon_baseline_YTD'] * $site['ghg_intensity_annual_target']) / 100);
	    $ProgressTargetPercentage['Carbon']['TARGET_BASELINE_YTD'] = $value['carbon_baseline_YTD'] - $progressCarbonYTDBaselineTargetValue;
	}
	if ($key == 'waste') {
	    $ProgressTargetPercentage['Waste']['monthly'] = $value['waste_monthly'];
	    $ProgressTargetPercentage['Waste']['YTD'] = $value['YTD'];
	    $ProgressTargetPercentage['Waste']['ACTUAL_YTD'] = 0;
	    $ProgressTargetPercentage['Waste']['TARGET_BASELINE_YTD'] = 0;
	    $ProgressTargetPercentage['Waste']['site_saving_target'] = $site['waste_intensity_annual_target'];
	}
    }
}
// $filters_notification['premonth'] = $filters_notification['cmonth']-1;

$filters_notification['c_year'] = $filters_notification['cyear'];
$filters_notification['c_month'] = $filters_notification['cmonth'];
if($filters_notification['cmonth'] == 1){
    $filters_notification['premonth'] = date("m",strtotime("-2 month"));;
    $filters_notification['c_year'] =date('Y')-1;
}else{
    $filters_notification['premonth'] = $filters_notification['cmonth']-1;
}
?>
<div class="gradient" style="padding: 3px 0px;width: 535px;vertical-align: central; float: <?php echo ($temp % 2 == 0) ? 'right' : 'left'; ?>;">
    <table style="font-size: 12px; vertical-align: central;padding: 10px;" width="100%">
	<tr>
	    <td width="15%" style="padding-top: 55px;text-align: center;border-right: 1px solid black;">
		<?php echo strtoupper($site_location_name); ?>
	    </td>
	    <td style="width: 200px;border-right: 1px solid black;">
		<table width="100%" style="vertical-align: central;">
			<tr>
			    <th colspan="2" align="center" style="padding: 5px;"><?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> v/s <?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y') - 1)); ?> Variations</th>
			</tr>
			<?php
			if ($notifications or ! empty($utilityForLastMonthCompare)) {
			    ?>
			    <tr>
				<th width="50%" style="padding: 7px;">
				    <?php
				    if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'] > 0) {
					$electricitydifference = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['electricity'] - $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'];
					$electricitypercentage = $electricitydifference * 100 / $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity'];
				    } else {
					$electricitypercentage = 100;
				    }
				    $electricitypercentage = round($electricitypercentage, 2);
				    if ($electricitypercentage < 0) {
					$electricity_img = 'downArrow.png';
					$difference_status = 'decreased';
				    } else {
					$electricity_img = 'upArrow.png';
					$difference_status = 'increased';
				    }
				    $electricitypercentage = abs($electricitypercentage);

				    echo "Electricity";
				    ?>
				</th>
				<td width="50%" style="padding: 7px;">
				    <?php
				    echo " : " . $electricitypercentage . "% ";
				    if($electricitypercentage != 0){
					echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $electricity_img . '"/>';
				    }
				    ?>
				</td>
			    </tr>
			    <tr>
				<th  style="padding: 7px;">
				    <?php
				    if ($utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'] > 0) {
					$waterdifference = $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['water'] - $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'];
					$waterpercentage = $waterdifference * 100 / $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['water'];
				    } else {
					$waterpercentage = 100;
				    }
				    $waterpercentage = round($waterpercentage, 2);
				    if ($waterpercentage < 0) {
					$water_img = "downArrow.png";
					$difference_status = 'decreased';
				    } else {
					$water_img = "upArrow.png";
					$difference_status = 'increased';
				    }
				    $waterpercentage = abs($waterpercentage);

				    echo "Water";
				    ?>
				</th>
				<td style="padding: 7px;">
				    <?php
				    echo " : " . $waterpercentage . "% ";
				    if($waterpercentage != 0){
					echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $water_img . '"/>';
				    }
				    ?>
				</td>
			    </tr>
			    <tr>
				<th style="padding: 7px;">
				    <?php
				    $current_thermal_energy = 0;
				    // $current_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['fuel'];
				    $current_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['lpg'];
				    $current_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['natural_gas'];
				    // $current_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['heating_district'];
				    // $current_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['cooling_district'];

				    $previous_thermal_energy = 0;
				    // $previous_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['fuel'];
				    $previous_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['lpg'];
				    $previous_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['natural_gas'];
				    // $previous_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['heating_district'];
				    // $previous_thermal_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['cooling_district'];

				    if ($previous_thermal_energy > 0) {
					$thermaldifference = $current_thermal_energy - $previous_thermal_energy;
					$thermalpercentage = $thermaldifference * 100 / $previous_thermal_energy;
				    } else {
					$thermalpercentage = 100;
				    }
				    $thermalpercentage = round($thermalpercentage, 2);
				    if ($thermalpercentage < 0) {
					$thermal_img = "downArrow.png";
					$difference_status = 'decreased';
				    } else {
					$thermal_img = "upArrow.png";
					$difference_status = 'increased';
				    }
				    $thermalpercentage = abs($thermalpercentage);
				    echo "Gas";
				    ?>
				</th>
				<td style="padding: 7px;">
				    <?php
				    echo " : " . $thermalpercentage . "% ";
				    if($thermalpercentage != 0){
					echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $thermal_img . '"/>';
				    }
				    ?>
				</td>
			    </tr>
			    <?php
				    $current_others_energy = 0;
				    $current_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['fuel'];
				    // $current_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['lpg'];
				    // $current_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['natural_gas'];
				    $current_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['heating_district'];
				    $current_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['cyear']][$filters_notification['cmonth']]['cooling_district'];

				    $previous_others_energy = 0;
				    $previous_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['fuel'];
				    // $previous_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['lpg'];
				    // $previous_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['natural_gas'];
				    $previous_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['heating_district'];
				    $previous_others_energy += $utilityForLastMonthCompare_unit[$filters_notification['pyear']][$filters_notification['pmonth']]['cooling_district'];

				    if ($previous_others_energy > 0) {
					$othersdifference = $current_others_energy - $previous_others_energy;
					$otherspercentage = $othersdifference * 100 / $previous_others_energy;
				    } else {
					$otherspercentage = 0;
				    }
				    $otherspercentage = round($otherspercentage, 2);
				    if ($otherspercentage < 0) {
					$others_img = "downArrow.png";
					$difference_status = 'decreased';
				    } else {
					$others_img = "upArrow.png";
					$difference_status = 'increased';
				    }
				    $otherspercentage = abs($otherspercentage);
				?>
			    <?php
				if(isset($otherspercentage) && !empty($otherspercentage)) {
			    ?>
			    <tr>
				<th style="padding: 7px;">
				Others
				</th>
				<td style="padding: 7px;">
				<?php
				    echo " : " . $otherspercentage . "% ";
				    if($otherspercentage != 0){
					echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $others_img . '"/>';
				    }
				    ?>
				</td>
			    </tr>
			    <?php } ?>
			<?php } ?>
		    </table>
	    </td>
	    <td style="width: 500px;border-right: 1px solid black;">
		<table style="vertical-align: central; width: 450px;">
		    <tr>
			<th align="center" style="padding: 5px;">TOTAL UTILITIES</th>
		    </tr>
		    <tr>
			<td style="width: 350px; padding-left: 5px;">
			    <?php
				if (!empty($utility_cost_chart)) {
				    $ci           = get_instance();
					    // Previous year data

					   $pre_data_electricity        = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['total_electricity_kwh'])) ? ($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['total_electricity_kwh'] - $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['onsite_generator'] - $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['renewable_energy']) : 0;
					    $pre_data_fuel               = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['fuel_consumption'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['fuel_consumption'] : 0;
					    $pre_data_lpg                = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['lpg_consumption'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['lpg_consumption'] : 0;
					    $pre_data_natural_gas        = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['natural_gas_consumption'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['natural_gas_consumption'] : 0;
					    $pre_data_heating_district   = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['heating_district_consumption'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['heating_district_consumption'] : 0;
					    $pre_data_cooling_district   = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['cooling_district_consumption'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['cooling_district_consumption'] : 0;

					    $pre_data_water              = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['water'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['water'] : 0;

					    $pre_data_cdd                = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['cdd'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['cdd'] : 0;
					    $pre_data_hdd                = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['hdd'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['hdd'] : 0;
					    $pre_data_occupancy          = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['occupancy'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['occupancy'] : 0;
					    $pre_data_room_night         = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['room_night'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['room_night'] : 0;
					    $pre_data_electricity_tariff = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity_tariff'])) ? $utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['electricity_tariff'] : 0;
					    $pre_data_electricity_kwh    = (!empty($utility_cost_chart[$filters_notification['pyear']][$filters_notification['pmonth']]['total_electricity_kwh'])) ? $utility_cost_chart['pmonth']['pyear']['total_electricity_kwh'] : 0;

					    // Current year previous month data
					   $data_pre_electricity        = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['total_electricity_kwh'])) ? ($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['total_electricity_kwh'] - $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['onsite_generator'] - $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['renewable_energy']) : 0;
					    $data_pre_fuel               = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['fuel_consumption'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['fuel_consumption'] : 0;
					    $data_pre_lpg                = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['lpg_consumption'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['lpg_consumption'] : 0;
					    $data_pre_natural_gas        = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['natural_gas_consumption'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['natural_gas_consumption'] : 0;
					    $data_pre_heating_district   = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['heating_district_consumption'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['heating_district_consumption'] : 0;
					    $data_pre_cooling_district   = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['cooling_district_consumption'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['cooling_district_consumption'] : 0;

					    $data_pre_water              = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['water'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['water'] : 0;
					    $data_pre_cdd                = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['cdd'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['cdd'] : 0;
					    $data_pre_hdd                = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['hdd'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['hdd'] : 0;
					    $data_pre_occupancy          = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['occupancy'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['occupancy'] : 0;
					    $data_pre_room_night         = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['room_night'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['room_night'] : 0;
					    $data_pre_electricity_tariff = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['electricity_tariff'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['electricity_tariff'] : 0;
					    $data_pre_electricity_kwh    = (!empty($utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['total_electricity_kwh'])) ? $utility_cost_chart[$filters_notification['c_year']][$filters_notification['premonth']]['total_electricity_kwh'] : 0;

					    // Current year data
					    $data_electricity        = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['total_electricity_kwh'])) ? ($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['total_electricity_kwh'] - $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['onsite_generator'] - $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['renewable_energy']) : 0;
					    $data_fuel               = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['fuel_consumption'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['fuel_consumption'] : 0;
					    $data_lpg                = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['lpg_consumption'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['lpg_consumption'] : 0;
					    $data_natural_gas        = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['natural_gas_consumption'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['natural_gas_consumption'] : 0;
					    $data_heating_district   = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['heating_district_consumption'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['heating_district_consumption'] : 0;
					    $data_cooling_district   = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['cooling_district_consumption'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['cooling_district_consumption'] : 0;

					    $data_water              = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['water'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['water'] : 0;
					    $data_cdd                = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['cdd'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['cdd'] : 0;
					    $data_hdd                = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['hdd'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['hdd'] : 0;
					    $data_occupancy          = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['occupancy'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['occupancy'] : 0;
					    $data_room_night         = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['room_night'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['room_night'] : 0;
					    $data_electricity_tariff = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['electricity_tariff'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['electricity_tariff'] : 0;
					    $data_electricity_kwh    = (!empty($utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['total_electricity_kwh'])) ? $utility_cost_chart[$filters_notification['cyear']][$filters_notification['cmonth']]['total_electricity_kwh'] : 0;
					    // Calculate carbon footprint
					    $pre_data_electricity      = round($pre_data_electricity * $site['electricity_emission_factor'], 2);
					    $pre_data_fuel             = round($pre_data_fuel * $site['fuel_emission_factor'], 2);
					    $pre_data_lpg              = round($pre_data_lpg * $site['lpg_emission_factor'], 2);
					    $pre_data_natural_gas      = round($pre_data_natural_gas * $site['natural_gas_emission_factor'], 2);
					    $pre_data_heating_district = round($pre_data_heating_district * $site['district_heating_emission_factor'], 2);
					    $pre_data_cooling_district = round($pre_data_cooling_district * $site['district_cooling_emission_factor'], 2);
					    $pre_data_water            = 0; // There is no calculation for water data

					    $data_electricity      = round($data_electricity * $site['electricity_emission_factor'], 2);
					    $data_fuel             = round($data_fuel * $site['fuel_emission_factor'], 2);
					    $data_lpg              = round($data_lpg * $site['lpg_emission_factor'], 2);
					    $data_natural_gas      = round($data_natural_gas * $site['natural_gas_emission_factor'], 2);
					    $data_heating_district = round($data_heating_district * $site['district_heating_emission_factor'], 2);
					    $data_cooling_district = round($data_cooling_district * $site['district_cooling_emission_factor'], 2);
					    $data_water            = 0; // There is no calculation for water data

					    $data_pre_electricity      = round($data_pre_electricity * $site['electricity_emission_factor'], 2);
					    $data_pre_fuel             = round($data_pre_fuel * $site['fuel_emission_factor'], 2);
					    $data_pre_lpg              = round($data_pre_lpg * $site['lpg_emission_factor'], 2);
					    $data_pre_natural_gas      = round($data_pre_natural_gas * $site['natural_gas_emission_factor'], 2);
					    $data_pre_heating_district = round($data_pre_heating_district * $site['district_heating_emission_factor'], 2);
					    $data_pre_cooling_district = round($data_pre_cooling_district * $site['district_cooling_emission_factor'], 2);
					    $data_pre_water            = 0; // There is no calculation for water data

					    // Round values
					    $pre_data_occupancy = round($pre_data_occupancy, 2);
					    $data_pre_occupancy = round($data_pre_occupancy, 2);
					    $data_occupancy     = round($data_occupancy, 2);

					    // Total sum Previous year data
					    $total_sum_pre_data_electricity += $pre_data_electricity;
					    $total_sum_pre_data_fuel += $pre_data_fuel;
					    $total_sum_pre_data_lpg += $pre_data_lpg;
					    $total_sum_pre_data_natural_gas += $pre_data_natural_gas;
					    $total_sum_pre_data_heating_district += $pre_data_heating_district;
					    $total_sum_pre_data_cooling_district += $pre_data_cooling_district;
					    $total_sum_pre_data_water += $pre_data_water;
					    $total_sum_pre_data_cdd += $pre_data_cdd;
					    $total_sum_pre_data_hdd += $pre_data_hdd;
					    $total_sum_pre_data_occupancy += $pre_data_occupancy;
					    $total_sum_pre_data_room_night += $pre_data_room_night;
					    //$total_sum_pre_data_electricity_tariff += $pre_data_electricity_tariff;
					    $total_sum_pre_data_electricity_kwh += $pre_data_electricity_kwh;

					    // Total sum Current year previous month data
					    $total_sum_data_pre_electricity += $data_pre_electricity;
					    $total_sum_data_pre_fuel += $data_pre_fuel;
					    $total_sum_data_pre_lpg += $data_pre_lpg;
					    $total_sum_data_pre_natural_gas += $data_pre_natural_gas;
					    $total_sum_data_pre_heating_district += $data_pre_heating_district;
					    $total_sum_data_pre_cooling_district += $data_pre_cooling_district;
					    $total_sum_data_pre_water += $data_pre_water;
					    $total_sum_data_pre_cdd += $data_pre_cdd;
					    $total_sum_data_pre_hdd += $data_pre_hdd;
					    $total_sum_data_pre_occupancy += $data_pre_occupancy;
					    $total_sum_data_pre_room_night += $data_pre_room_night;
					    //$total_sum_data_pre_electricity_tariff += $data_pre_electricity_tariff;
					    $total_sum_data_pre_electricity_kwh += $data_pre_electricity_kwh;

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


				    $total_sum_data_pre_sum = ($total_sum_data_pre_electricity + $total_sum_data_pre_fuel + $total_sum_data_pre_lpg + $total_sum_data_pre_natural_gas + $total_sum_data_pre_water + $total_sum_data_pre_heating_district + $total_sum_data_pre_cooling_district);
				    $total_sum_pre_data_sum = ($total_sum_pre_data_electricity + $total_sum_pre_data_fuel + $total_sum_pre_data_lpg + $total_sum_pre_data_natural_gas + $total_sum_pre_data_water + $total_sum_pre_data_heating_district + $total_sum_pre_data_cooling_district);
				    $total_sum_data_sum     = ($total_sum_data_electricity + $total_sum_data_fuel + $total_sum_data_lpg + $total_sum_data_natural_gas + $total_sum_data_water + $total_sum_data_heating_district + $total_sum_data_cooling_district);
				?>
			    <table style="vertical-align: central; width: 350px;" border="1">
				<tr>
				    <th style="width: 80px;text-align: center;">&nbsp;</th>
				    <th style="padding: 5px;text-align: center; font-size: 14px; width: 80px;">EUI - <?php echo "Kwh/".getLocalUnitText($site['id']);?></th>
				    <th style="padding: 5px;text-align: center; font-size: 14px; width: 80px;">Cost</th>
				    <!-- <th style="padding: 5px;text-align: center; font-size: 14px;width: 130px;">Cost/Room night</th> -->
				    <!-- <th style="padding: 5px;text-align: center; font-size: 14px;width: 130px;">CO<sub>2</sub> (KgCO<sub>2</sub>e)</th> -->
				    <th style="padding: 5px;text-align: center; font-size: 14px;width: 130px;">Cost v/s Budget</th>
				</tr>
				<tr>
				    <th style="padding: 5px;">
					<?php echo date('F Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?>
				    </th>
				    <td style="padding: 5px;text-align: center;"><?php echo $progressOnTarget[date('m') - 1][date('Y')]['energy']; ?>
				    <td style="padding: 5px;text-align: center;"><?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['total_utility_cost_currentMonth'])); ?>
				    </td>
				    <!-- <td style="padding: 5px;text-align: center;">
					<?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['currentMonth_cost_roomNight'])); ?>
				    </td> -->
				    <!-- <td style="padding: 8px;text-align: center;">
					<?php //echo number_format($total_sum_data_pre_sum); ?>
					<?php echo number_format($total_sum_data_sum); ?>
				    </td> -->
				    <?php
				    $image = $site['data']['kpi']['variation'] < 0 ? 'upArrow.png' : 'downArrow.png';
				    $color = $site['data']['kpi']['variation'] < 0 ? '#dc2727' : '#2ecc71';

				    $image_ytd = $site['data']['kpi']['variation_ytd'] < 0 ? 'upArrow.png' : 'downArrow.png';
				    $color_ytd = $site['data']['kpi']['variation_ytd'] < 0 ? '#dc2727' : '#2ecc71';
				    ?>
				    <td style="padding: 5px; text-align: center;">
					<?php echo round(abs($site['data']['kpi']['variationPercentage'])); ?>%
					<?php
					    if($site['data']['kpi']['variationPercentage'] != 0){
						?>
						    <img width="15px" height="15px" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image; ?>">
						<?php
					    }
					?>
				    </td>
				</tr>
				<tr>
				    <th style="padding: 5px;">
					<?php echo date('F Y', mktime(0, 0, 0, date('m') - 2, 1, date('Y'))); ?>
				    </th>
				    <td style="padding: 5px;text-align: center;"><?php echo $progressOnTarget[date('m') - 2][date('Y')]['energy']; ?>
				    <td style="padding: 5px;text-align: center;">
					<?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['total_utility_cost_lastMonth'])); ?>
				    </td>
				    <!-- <td style="padding: 5px;text-align: center;">
					<?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['lastMonth_cost_roomNight'])); ?>
				    </td> -->
				    <!-- <td style="padding: 8px;text-align: center;">
					<?php
					//  echo number_format($total_sum_data_sum);
					 echo number_format($total_sum_data_pre_sum);
					 ?>
				    </td> -->
				    <td style="padding: 3px;text-align: center;">
					<div style="font-size: 14px; font-weight: bold;margin-bottom: 10px;">
					    Year To Date
					</div>
				    </td>
				</tr>
				<tr >
				    <th style="padding: 5px;">
					<?php echo date('F', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); ?> <?php echo date('Y', strtotime('-1 year -1 month')); ?>
				    </th>
				    <td style="padding: 5px;text-align: center;"><?php echo $progressOnTarget[date('m') - 1][date('Y') - 1]['energy']; ?>
				    <td style="padding: 5px;text-align: center;">
					<?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['total_utility_cost_sameMonth_lastYear'])); ?>
				    </td>
				    <!-- <td style="padding: 5px;text-align: center;">
					<?php echo BASE_CURRENCY_SYMBOL; ?><?php echo number_format(round($site['data']['kpi']['sameMonth_lastYear_cost_roomNight'])); ?>
				    </td> -->
				    <!-- <td style="padding: 8px;text-align: center;">
					<?php echo number_format($total_sum_pre_data_sum); ?>
				    </td> -->
				    <td style="padding: 5px;text-align: center;">
					<div style="margin-top: 10px!important;text-align: center;">
					    <?php echo round(abs($site['data']['kpi']['variationPercentage_ytd'])); ?>%
					    <?php
						if($site['data']['kpi']['variationPercentage_ytd'] != 0){
						    ?>
							<img width="15px" height="15px" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image_ytd; ?>"/>
						    <?php
						}
					    ?>
					</div>
				    </td>
				</tr>
			    </table>
			<?php } ?>
			</td>
			<!-- <td style="width: 135px; padding-left: 5px; padding-right: 5px;">
			    <table style="vertical-align: central; width: 100px;" border="1">
				<tr>
				    <th style="padding: 5px; font-size: 14px;">Cost v/s Budget</th>
				</tr>
				<tr>
				    <?php
				    $image = $site['data']['kpi']['variation'] < 0 ? 'upArrow.png' : 'downArrow.png';
				    $color = $site['data']['kpi']['variation'] < 0 ? '#dc2727' : '#2ecc71';

				    $image_ytd = $site['data']['kpi']['variation_ytd'] < 0 ? 'upArrow.png' : 'downArrow.png';
				    $color_ytd = $site['data']['kpi']['variation_ytd'] < 0 ? '#dc2727' : '#2ecc71';
				    ?>
				    <td style="padding: 5px; text-align: center;">
					<?php echo round(abs($site['data']['kpi']['variationPercentage'])); ?>%
					<?php
					    if($site['data']['kpi']['variationPercentage'] != 0){
						?>
						    <img width="15px" height="15px" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image; ?>">
						<?php
					    }
					?>
				    </td>
				</tr>
				<tr>
				    <td style="padding: 0px 5px 3px 5px;text-align: center;">
					<div style="font-size: 14px; font-weight: bold;margin-bottom: 10px;">
					    Year To Date
					</div>
					<br/>
					<div style="margin-top: 10px!important;text-align: center;">
					    <?php echo round(abs($site['data']['kpi']['variationPercentage_ytd'])); ?>%
					    <?php
						if($site['data']['kpi']['variationPercentage_ytd'] != 0){
						    ?>
							<img width="15px" height="15px" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image_ytd; ?>"/>
						    <?php
						}
					    ?>
					</div>
				    </td>
				</tr>
			    </table>
			</td> -->
		    </tr>
		</table>
	    </td>
	</tr>
	<tr>
	    <td style="border-right: 1px solid black;"></td>
	    <td style="border-right: 1px solid black;">
		<hr>
		<table style="vertical-align: central; width: 350px;" border="1">



		    <tr>
			<th style="font-size: 11px;padding: 7px;"><?php echo $site['data']['kpi']['cdd_hdd']['cdd']['title']; ?> </th>
			<td style="font-size: 11px;padding: 7px;"><?php echo round(abs($site['data']['kpi']['cdd_hdd']['cdd']['consumption'])); ?>%
			<?php
			if (round(abs($site['data']['kpi']['cdd_hdd']['cdd']['consumption'])) == 0 || is_infinite($site['data']['kpi']['cdd_hdd']['cdd']['consumption'])) {

			} else {
			    ?>
			    <img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $site['data']['kpi']['cdd_hdd']['cdd']['consumption_image']; ?>" style="width: 15px;height: 15px;">
			<?php } ?>
			</td>
		    </tr>
		    <tr>
			<th style="font-size: 11px;padding: 7px;"><?php echo $site['data']['kpi']['cdd_hdd']['hdd']['title']; ?> </th>
			<td style="font-size: 11px;padding: 7px;"><?php echo is_infinite($site['data']['kpi']['cdd_hdd']['hdd']['consumption']) ? '&#8734;' : round(abs($site['data']['kpi']['cdd_hdd']['hdd']['consumption'])).'%'; ?>
			<?php
			if (round(abs($site['data']['kpi']['cdd_hdd']['hdd']['consumption'])) == 0 || is_infinite($site['data']['kpi']['cdd_hdd']['cdd']['consumption'])) {

			} else {
			    ?>
			    <img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $site['data']['kpi']['cdd_hdd']['hdd']['consumption_image']; ?>" style="width: 15px;height: 15px;">
			<?php } ?>
			</td>
		    </tr>
		    <tr>
			<th style="font-size: 11px;padding: 7px;"><?php echo $site['data']['kpi']['cdd_hdd']['room_nights']['title']; ?> </th>
			<td style="font-size: 11px;padding: 7px;"><?php echo is_infinite($site['data']['kpi']['cdd_hdd']['room_nights']['consumption']) ? '<h3>&#8734;</h3>' : round(abs($site['data']['kpi']['cdd_hdd']['room_nights']['consumption'])).'%'; ?>
			<?php
			if (round(abs($site['data']['kpi']['cdd_hdd']['room_nights']['consumption'])) == 0 || is_infinite($site['data']['kpi']['cdd_hdd']['cdd']['consumption'])) {

			} else {
			    ?>
			    <img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $site['data']['kpi']['cdd_hdd']['room_nights']['consumption_image']; ?>" style="width: 15px;height: 15px;">
			<?php } ?>
			</td>
		    </tr>
		</table>
	    </td>
	    <td style="padding-left: 5px;">
		<?php
			foreach ($ProgressTargetPercentage as $key => $value) {
			}
		?>
		<table style="vertical-align: central; width: 100%;" border="1">
		    <tr>
			<th colspan="3" style="text-align: center;font-size: 14px;">Progress On Targets</th>
		    </tr>
		    <tr>
			<th style="padding: 7px;">Type (% Target)</th>
			<th style="text-align: center;padding: 5px; font-size: 14px;">Consumption YTD</th>
			<th style="text-align: center;padding: 5px; font-size: 14px;">Target Consumption YTD</th>
			<th style="text-align: center;padding: 5px; font-size: 14px;">Performance YTD</th>
		    </tr>
			<?php foreach ($ProgressTargetPercentage as $key => $value) {
			if($value['ACTUAL_YTD'] > $value['TARGET_BASELINE_YTD']) {
				$value['YTD_Variance'] = ($value['ACTUAL_YTD'] != 0) ? (($value['ACTUAL_YTD'] - $value['TARGET_BASELINE_YTD']) / ($value['ACTUAL_YTD'])) * 100 : 0;
			} else {
				if($value['TARGET_BASELINE_YTD'] != 0) {
					$value['YTD_Variance'] = '-'.(($value['TARGET_BASELINE_YTD'] - $value['ACTUAL_YTD']) / ($value['TARGET_BASELINE_YTD'])) * 100;
				} else {
					$value['YTD_Variance'] = 0;
				}
			} ?>
		    <tr>
			<th style="padding: 5px;"><?= $key;?> (<?= isset($value['site_saving_target']) ? $value['site_saving_target'] : 0; ?>) :</td>
			<td style="text-align: center;padding: 5px;"><?php echo number_format($value['ACTUAL_YTD'], 0); ?></td>
			<td style="text-align: center;padding: 5px;"><?php echo number_format($value['TARGET_BASELINE_YTD'], 0); ?></td>
			<td style="text-align: center;padding: 5px;"><?php echo isset($value['YTD_Variance']) ? number_format($value['YTD_Variance'], 2) : 0; ?> %</td>
		    </tr>
			<?php } ?>
		</table>
	    </td>
	</tr>
    </table>
</div>
