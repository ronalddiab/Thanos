<!-- Progress Chart START-->
<?php
$current_month = date('m');
$current_year  = date('Y'); //2022
if ($current_month == 1) {
	$current_month = 12;
	$current_year  = date('Y') - 1;
}
$baseline_year = $site_detials['baseline_regression_year'];
//Creation of array for progress on target widget
$progressTarget = $ProgressTargetPercentage = [];
if (!empty($progressOnTarget)) {
    $progressEnergyYTD = 0;
    $progressWaterYTD = 0;
    $YTDMonth = ($current_month == 12) ? $current_month : $current_month - 1;
    foreach ($progressOnTarget as $key => $value) {
        foreach ($value as $key1 => $value1) {
            // monthly data of Current year (water and Energy)
            if ($value1['month_id'] == $YTDMonth && $value1['year_id'] == $current_year) {
                $progressTarget['energy']['energy_monthly'] = isset($value1['energy_target']) ? $value1['energy_target'] : 0;
                $progressTarget['water']['water_monthly'] = isset($value1['water_target']) ? $value1['water_target'] : 0;
            }
            // monthly data of last year (water and Energy)
            if ($value1['month_id'] == $YTDMonth && $value1['year_id'] == $current_year - 1) {
                $progressTarget['energy']['energy_last_monthly'] = isset($value1['energy_target']) ? $value1['energy_target'] : 0;
                $progressTarget['water']['water_last_monthly'] = isset($value1['water_target']) ? $value1['water_target'] : 0;
            }
            // YTD data of Current and last year (Energy)
            if ($value1['month_id'] <= $YTDMonth && $value1['year_id'] == $current_year) {
                $progressEnergyYTD += $value1['energy_target'];
                $progressTarget['energy']['energy_YTD'] = isset($progressEnergyYTD) ? $progressEnergyYTD : 0;
            } else if ($value1['month_id'] <= $YTDMonth && $value1['year_id'] == $current_year - 1) {
                $progressEnergyYTDLast += $value1['energy_target'];
                $progressTarget['energy']['energy_last_YTD'] = isset($progressEnergyYTDLast) ? $progressEnergyYTDLast : 0;
            }
            // YTD data of Curret and last year (water)
            if ($value1['month_id'] <= $YTDMonth && $value1['year_id'] == $current_year) {
                $progressWaterYTD += $value1['water_target'];
                $progressTarget['water']['water_YTD'] = isset($progressWaterYTD) ? $progressWaterYTD : 0;
            } else if ($value1['month_id'] <= $YTDMonth && $value1['year_id'] == $current_year - 1) {
                $progressWaterYTDLast += $value1['water_target'];
                $progressTarget['water']['water_last_YTD'] = isset($progressWaterYTDLast) ? $progressWaterYTDLast : 0;
            }

            // YTD for baseline year (WATER)
            $progressWaterBaselineYTD += $value1['water_baseline_target'];
            $progressTarget['water']['water_baseline_YTD'] = isset($progressWaterBaselineYTD) ? $progressWaterBaselineYTD : 0;

            // YTD for baseline year (ENERGY)
            $progressEnergyBaselineYTD += $value1['energy_baseline_target'];
            $progressTarget['energy']['energy_baseline_YTD'] = isset($progressEnergyBaselineYTD) ? $progressEnergyBaselineYTD : 0;


            // carbon current , last month (monthly) , last year,  current year YTD data
            $progressTarget['carbon']['carbon_monthly'] = isset($carbon['carbon_footprint_currentMonth']) ? $carbon['carbon_footprint_currentMonth'] : 0;
            $progressTarget['carbon']['carbon_last_monthly'] = isset($carbon['carbon_footprint_SameMonthPreviousYear']) ? $carbon['carbon_footprint_SameMonthPreviousYear'] : 0;
            $progressTarget['carbon']['carbon_YTD'] = isset($carbon['ytd_carbon_footprint_new']) ? $carbon['ytd_carbon_footprint_new'] : 0;
            $progressTarget['carbon']['carbon_last_YTD'] = isset($carbon['ytd_carbon_footprintPreviousYear']) ? $carbon['ytd_carbon_footprintPreviousYear'] : 0;
            $progressTarget['carbon']['carbon_baseline_YTD'] = isset($carbon['ytd_carbon_footprint_baseline_new']) ? $carbon['ytd_carbon_footprint_baseline_new'] : 0;
            // $progressTarget['waste']['waste_monthly'] = 5;
            // $progressTarget['waste']['YTD'] = 2;

        }
    }
    $sortKey = array('energy', 'carbon', 'water');
    $orderedprogressTargetArray = array();
    foreach ($sortKey as $key) {
        $orderedprogressTargetArray[$key] = $progressTarget[$key];
    }
    // Calculation of Progress on Targets
    // energy_intensity_annual_target% from side edit and Percentage difference from previous year to current year
    foreach ($orderedprogressTargetArray as $key => $value) {
        if ($key == 'energy') {
            $differenceEnergyMonthly = $value['energy_last_monthly'] - $value['energy_monthly'];
            $progressEnergyMonthlyTargetValue = (($value['energy_last_monthly'] * $site_detials['energy_intensity_annual_target']) / 100);
            $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyMonthly, $value['energy_last_monthly']);
            $ProgressTargetPercentage['Energy']['monthly'] = $ProgressEnergyTargetPercentageValue;
            $ProgressTargetPercentage['Energy']['site_saving_target'] = $site_detials['energy_intensity_annual_target'];

            $differenceEnergyYTD = $value['energy_last_YTD'] - $value['energy_YTD'];
            $progressEnergyYTDTargetValue = (($value['energy_last_YTD'] * $site_detials['energy_intensity_annual_target']) / 100);
            $ProgressEnergyTargetPercentageValue = calculateDashboardPercentage($differenceEnergyYTD, $value['energy_last_YTD']);
            $ProgressTargetPercentage['Energy']['YTD'] = $ProgressEnergyTargetPercentageValue;
            $ProgressTargetPercentage['Energy']['ACTUAL_YTD'] = $value['energy_YTD'];
            // Consumption Target YTD
            $progressEnergyYTDBaselineTargetValue = (($value['energy_baseline_YTD'] * $site_detials['energy_intensity_annual_target']) / 100);
            $ProgressTargetPercentage['Energy']['TARGET_BASELINE_YTD'] = $value['energy_baseline_YTD'] - $progressEnergyYTDBaselineTargetValue;
        }
        if ($key == 'water') {
            $differenceWaterMonthly = $value['water_last_monthly'] - $value['water_monthly'];
            $progressWaterMonthlyTargetValue = (($value['water_last_monthly'] * $site_detials['water_intensity_annual_target']) / 100);
            $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterMonthly, $value['water_last_monthly']);
            $ProgressTargetPercentage['Water']['monthly'] = $ProgressWaterTargetPercentageValue;
            $ProgressTargetPercentage['Water']['site_saving_target'] = $site_detials['water_intensity_annual_target'];

            $differenceWaterYTD = $value['water_last_YTD'] - $value['water_YTD'];
            $progressWaterYTDTargetValue = (($value['water_last_YTD'] * $site_detials['water_intensity_annual_target']) / 100);
            $ProgressWaterTargetPercentageValue = calculateDashboardPercentage($differenceWaterYTD, $value['water_last_YTD']);
            $ProgressTargetPercentage['Water']['YTD'] = $ProgressWaterTargetPercentageValue;
            $ProgressTargetPercentage['Water']['ACTUAL_YTD'] = $value['water_YTD'];
            // Consumption Target YTD
            $progressWaterYTDBaselineTargetValue = (($value['water_baseline_YTD'] * $site_detials['water_intensity_annual_target']) / 100);
            $ProgressTargetPercentage['Water']['TARGET_BASELINE_YTD'] = $value['water_baseline_YTD'] - $progressWaterYTDBaselineTargetValue;
        }
        if ($key == 'carbon') {
            $differenceCarbonMonthly = $value['carbon_last_monthly'] - $value['carbon_monthly'];
            $progressCarbonMonthlyTargetValue = (($value['carbon_last_monthly'] * $site_detials['ghg_intensity_annual_target']) / 100);
            $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonMonthly, $value['carbon_last_monthly']);
            $ProgressTargetPercentage['Carbon']['monthly'] = $ProgressCarbonTargetPercentageValue;
            $ProgressTargetPercentage['Carbon']['site_saving_target'] = $site_detials['ghg_intensity_annual_target'];

            $differenceCarbonYTD = $value['carbon_last_YTD'] - $value['carbon_YTD'];
            $progressCarbonYTDTargetValue = (($value['carbon_last_YTD'] * $site_detials['ghg_intensity_annual_target']) / 100);
            $ProgressCarbonTargetPercentageValue = calculateDashboardPercentage($differenceCarbonYTD, $value['carbon_last_YTD']);
            $ProgressTargetPercentage['Carbon']['YTD'] = $ProgressCarbonTargetPercentageValue;
            $ProgressTargetPercentage['Carbon']['ACTUAL_YTD'] = $value['carbon_YTD'];
            // Consumption Target YTD
            $progressCarbonYTDBaselineTargetValue = (($value['carbon_baseline_YTD'] * $site_detials['ghg_intensity_annual_target']) / 100);
            $ProgressTargetPercentage['Carbon']['TARGET_BASELINE_YTD'] = $value['carbon_baseline_YTD'] - $progressCarbonYTDBaselineTargetValue;
        }
        if ($key == 'waste') {
            $ProgressTargetPercentage['Waste']['monthly'] = $value['waste_monthly'];
            $ProgressTargetPercentage['Waste']['YTD'] = $value['YTD'];
            $ProgressTargetPercentage['Waste']['ACTUAL_YTD'] = 0;
            $ProgressTargetPercentage['Waste']['TARGET_BASELINE_YTD'] = 0;
            $ProgressTargetPercentage['Waste']['site_saving_target'] = $site_detials['waste_intensity_annual_target'];
        }
    }
}
?>
<html style="text-align: left;">

<head>
    <style>
        table {
            text-align: center !important;
            vertical-align: center !important;
        }
    </style>
</head>

<body width="100%">
    <br>
    <br>
    <div style="border:2px solid  #f69546;padding:6px;">
        <table>
            <thead>
                <tr colspan="2" style="font-size:12px;color:blue;">
                    <td><strong>Progress on property reduction targets</strong></td>
                </tr>
            </thead>
        </table>
        <table style="vertical-align: central;padding: 10px;" width="100%" cellpadding="2" cellspacing="2">
            <thead width="100%">
                <tr>
                    <th width="14%"></th>
                    <th width="14%">Consumption YTD</th>
                    <th width="14%">Target Consumption YTD</th>
                    <th width="14%">Intensity YTD</th>
                    <th width="14%">Target Intensity YTD</th>
                    <th width="14%">Reduction <br />Target %</th>
                    <th width="15.2%">Performance YTD</th>
                </tr>
            </thead>
            <tbody width="100%">
                <tr></tr>
                <?php foreach ($ProgressTargetPercentage as $key => $value) {
                    if ($key == 'Energy') {
                        $unitKey = 'electricity';
                        $unitText = GetSiteUtilityUnitName($site_id, $unitKey);
                    } else if ($key == 'Water') {
                        $unitKey = 'water';
                        $unitText = GetSiteUtilityUnitName($site_id, $unitKey);
                    } else if ($key == 'Carbon') {
                        $unitText = 'KgCO2';
                    } else {
                        $unitText = '';
                    }
                    if ($value['ACTUAL_YTD'] > $value['TARGET_BASELINE_YTD']) {
                        $value['YTD_Variance'] = (($value['ACTUAL_YTD'] - $value['TARGET_BASELINE_YTD']) / ($value['ACTUAL_YTD'])) * 100;
                    } else {
                        $value['YTD_Variance'] = '-' . (($value['TARGET_BASELINE_YTD'] - $value['ACTUAL_YTD']) / ($value['TARGET_BASELINE_YTD'])) * 100;
                    }
                    $image = $value['YTD_Variance'] < 0 ? 'downArrow.png' : 'upArrow.png';
                    $color = $value['YTD_Variance'] < 0 ? '#dc2727' : '#2ecc71';
                    ?>
                    <tr style="height:20px;">
                        <td><?php echo isset($unitText) && !empty($unitText) ? $key . ' (' . $unitText . ')' : $key; ?></td>
                        <td><?php echo number_format($value['ACTUAL_YTD'], 0); ?></td>
                        <td><?php echo number_format($value['TARGET_BASELINE_YTD'], 0); ?></td>
                        <td><?php echo isset($site_detials['cooled_builtup_area']) && !empty($site_detials['cooled_builtup_area']) ? number_format($value['ACTUAL_YTD'] / $site_detials['cooled_builtup_area'], 2) : 0; ?></td>
                        <td><?php echo isset($site_detials['cooled_builtup_area']) && !empty($site_detials['cooled_builtup_area']) ? number_format($value['TARGET_BASELINE_YTD'] / $site_detials['cooled_builtup_area'], 2) : 0; ?></td>
                        <td><?php echo isset($value['site_saving_target']) ? '-' . $value['site_saving_target'] . '%' : '-'; ?></td>
                        <td><?php echo number_format($value['YTD_Variance'], 2); ?> % </td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>