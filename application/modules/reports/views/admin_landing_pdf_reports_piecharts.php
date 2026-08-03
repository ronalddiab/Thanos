<?php
$isLocal = true;
if ($currency == "base") {
    $isLocal = false;
}
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year - 1;

if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) { // If start and end year is same
    for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $filters['filters_comparision_chart']["end_month"]; $i++) {
        $startmonthsarray[] = $i;
    }

    $resultkeys = array();
    $resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
} else { // If start and end year is not same
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
?>
<?php
$kwh_pie_chart_electricity_value = $kwh_pie_chart['electricity'];
$kwh_pie_chart_fuel_value = $kwh_pie_chart['fuel'];
$kwh_pie_chart_lpg_value = $kwh_pie_chart['lpg'];
$kwh_pie_chart_natural_gas_value = $kwh_pie_chart['natural_gas'];
$kwh_pie_chart_heating_district_value = $kwh_pie_chart['heating_district'];
$kwh_pie_chart_cooling_district_value = $kwh_pie_chart['cooling_district'];

$kwh_pie_chart_utility_kwh_total = ($kwh_pie_chart_electricity_value + $kwh_pie_chart_fuel_value + $kwh_pie_chart_lpg_value + $kwh_pie_chart_natural_gas_value + $kwh_pie_chart_heating_district_value + $kwh_pie_chart_cooling_district_value);

$kwh_pie_chart_electricity_share = is_infinite(($kwh_pie_chart_electricity_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_electricity_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);
$kwh_pie_chart_fuel_share = is_infinite(($kwh_pie_chart_fuel_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_fuel_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);
$kwh_pie_chart_lpg_share = is_infinite(($kwh_pie_chart_lpg_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_lpg_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);
$kwh_pie_chart_natural_gas_share = is_infinite(($kwh_pie_chart_natural_gas_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_natural_gas_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);
$kwh_pie_chart_heating_district_share = is_infinite(($kwh_pie_chart_heating_district_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_heating_district_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);
$kwh_pie_chart_cooling_district_share = is_infinite(($kwh_pie_chart_cooling_district_value * 100) / $kwh_pie_chart_utility_kwh_total) ? 0 : round(($kwh_pie_chart_cooling_district_value * 100) / $kwh_pie_chart_utility_kwh_total, 1);

$kwh_pie_chart_electricity_share = is_nan($kwh_pie_chart_electricity_share) ? 0 : $kwh_pie_chart_electricity_share;
$kwh_pie_chart_fuel_share = is_nan($kwh_pie_chart_fuel_share) ? 0 : $kwh_pie_chart_fuel_share;
$kwh_pie_chart_lpg_share = is_nan($kwh_pie_chart_lpg_share) ? 0 : $kwh_pie_chart_lpg_share;
$kwh_pie_chart_natural_gas_share = is_nan($kwh_pie_chart_natural_gas_share) ? 0 : $kwh_pie_chart_natural_gas_share;
$kwh_pie_chart_heating_district_share = is_nan($kwh_pie_chart_heating_district_share) ? 0 : $kwh_pie_chart_heating_district_share;
$kwh_pie_chart_cooling_district_share = is_nan($kwh_pie_chart_cooling_district_share) ? 0 : $kwh_pie_chart_cooling_district_share;

$total_share = ($kwh_pie_chart_electricity_share+$kwh_pie_chart_fuel_share+$kwh_pie_chart_lpg_share+$kwh_pie_chart_natural_gas_share+$kwh_pie_chart_heating_district_share+$kwh_pie_chart_cooling_district_share);

$kwh_pie_chart_previousmonth_electricity_value = $kwh_pie_chart_previousmonth['electricity'];
$kwh_pie_chart_previousmonth_fuel_value = $kwh_pie_chart_previousmonth['fuel'];
$kwh_pie_chart_previousmonth_lpg_value = $kwh_pie_chart_previousmonth['lpg'];
$kwh_pie_chart_previousmonth_natural_gas_value = $kwh_pie_chart_previousmonth['natural_gas'];
$kwh_pie_chart_previousmonth_heating_district_value = $kwh_pie_chart_previousmonth['heating_district'];
$kwh_pie_chart_previousmonth_cooling_district_value = $kwh_pie_chart_previousmonth['cooling_district'];

$kwh_pie_chart_previousmonth_utility_kwh_total = ($kwh_pie_chart_previousmonth_electricity_value + $kwh_pie_chart_previousmonth_fuel_value + $kwh_pie_chart_previousmonth_lpg_value + $kwh_pie_chart_previousmonth_natural_gas_value + $kwh_pie_chart_previousmonth_heating_district_value + $kwh_pie_chart_previousmonth_cooling_district_value);

$kwh_pie_chart_previousmonth_electricity_share = round(($kwh_pie_chart_previousmonth_electricity_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);
$kwh_pie_chart_previousmonth_fuel_share = round(($kwh_pie_chart_previousmonth_fuel_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);
$kwh_pie_chart_previousmonth_lpg_share = round(($kwh_pie_chart_previousmonth_lpg_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);
$kwh_pie_chart_previousmonth_natural_gas_share = round(($kwh_pie_chart_previousmonth_natural_gas_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);
$kwh_pie_chart_previousmonth_heating_district_share = round(($kwh_pie_chart_previousmonth_heating_district_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);
$kwh_pie_chart_previousmonth_cooling_district_share = round(($kwh_pie_chart_previousmonth_cooling_district_value * 100) / $kwh_pie_chart_previousmonth_utility_kwh_total, 1);

$total_share_lastyear = ($kwh_pie_chart_previousmonth_electricity_share+$kwh_pie_chart_previousmonth_fuel_share+$kwh_pie_chart_previousmonth_lpg_share+$kwh_pie_chart_previousmonth_natural_gas_share+$kwh_pie_chart_previousmonth_heating_district_share+$kwh_pie_chart_previousmonth_cooling_district_share);

$cost_pie_chart_t_sum = ($cost_pie_chart['electricity'] + $cost_pie_chart['fuel'] + $cost_pie_chart['lpg'] + $cost_pie_chart['natural_gas'] + $cost_pie_chart['heating_district'] + $cost_pie_chart['cooling_district'] + $cost_pie_chart['water']);
$cost_pie_chart_previousmonth_t_sum = ($cost_pie_chart_previousmonth['electricity'] + $cost_pie_chart_previousmonth['fuel'] + $cost_pie_chart_previousmonth['lpg'] + $cost_pie_chart_previousmonth['natural_gas'] + $cost_pie_chart_previousmonth['heating_district'] + $cost_pie_chart_previousmonth['cooling_district'] + $cost_pie_chart_previousmonth['water']);

// Calculation for percantage share
$kwh_pie_chart_cmonth_electricity_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['electricity'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_fuel_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['fuel'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_lPG_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['lpg'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_gas_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['natural_gas'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_heating_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['heating_district'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_cooling_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['cooling_district'] * 100) / $cost_pie_chart_t_sum) : 0;
$kwh_pie_chart_cmonth_water_share = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart['water'] * 100) / $cost_pie_chart_t_sum) : 0;

$kwh_pie_chart_previousmonth_cost_electricity_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['electricity'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_fuel_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['fuel'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_lPG_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['lpg'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_gas_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['natural_gas'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_heating_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['heating_district'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_cooling_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['cooling_district'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;
$kwh_pie_chart_previousmonth_cost_water_share = ($cost_pie_chart_previousmonth_t_sum > 0) ? round(($cost_pie_chart_previousmonth['water'] * 100) / $cost_pie_chart_previousmonth_t_sum, 1) : 0;

?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tr>
                    <td width="100%">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%"><img height="200" src="<?php echo $pieChartImg; ?>" /></td>
                                <td width="50%"><img height="200" src="<?php echo $pieChartNewImg; ?>" /></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="2" cellspacing="0">
                                        <thead>
                                            <tr>
											<th style="background-color:#d8e1f2;" align="center"><strong><?php echo 'Energy Consumption (% Share Of Total kWh) - Year to Date'; ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
															<td width="38%"><strong>Utilities</strong></td>
															<td width="38%"><strong>Consumption (kWh)</strong></td>
															<td width="24%"><strong>% Share</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if ($totalElectricity) { ?>
                                                                <tr>
                                                                    <td width="38%">Electricity</td>
                                                                    <td width="38%"><?php echo number_format(round($kwh_pie_chart_electricity_value, 0)); ?></td>
                                                                    <td width="24%"><?php echo $kwh_pie_chart_electricity_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalFuel) { ?>
                                                                <tr>
                                                                    <td>Fuel</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_fuel_value, 0)); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_fuel_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalLpg) { ?>
                                                                <tr>
                                                                    <td>LPG</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_lpg_value, 0)); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_lpg_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalNaturalGas) { ?>
                                                                <tr>
                                                                    <td>Natural Gas</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_natural_gas_value, 0)); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_natural_gas_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalHeatingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Heating</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_heating_district_value, 0)); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_heating_district_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalCoolingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Cooling</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_cooling_district_value, 0)); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cooling_district_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="38%"><strong>Total</strong></td>
                                                                <td width="38%"><strong><?php echo number_format(round($kwh_pie_chart_utility_kwh_total,0)); ?></strong></td>
                                                                <td width="24%"><strong><?php echo round($total_share); ?>%</strong></td>               
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="2" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="background-color:#d8e1f2;" align="center"><strong><?php echo lang("cost-pie-chart-title"); ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Utilities</strong></td>
                                                                <td width="35%"><strong>Cost (<?php echo $isLocal ? currency_symbol($isLocal) : CURRENCY . CURRENCY_SYMBOL ?>)</strong></td>
                                                                <td width="20%"><strong>% Share</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if ($totalElectricity) { ?>
                                                                <tr>
                                                                    <td width="45%">Electricity</td>
                                                                    <td width="35%"><?php number_format($cost_pie_chart['electricity']); ?></td>
                                                                    <td width="20%"><?php echo $kwh_pie_chart_cmonth_electricity_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalFuel) { ?>
                                                                <tr>
                                                                    <td>Fuel</td>
                                                                    <td><?php echo number_format($cost_pie_chart['fuel']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_fuel_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalLpg) { ?>
                                                                <tr>
                                                                    <td>LPG</td>
                                                                    <td><?php echo number_format($cost_pie_chart['lpg']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_lPG_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalNaturalGas) { ?>
                                                                <tr>
                                                                    <td>Natural Gas</td>
                                                                    <td><?php echo number_format($cost_pie_chart['natural_gas']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_gas_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalHeatingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Heating</td>
                                                                    <td><?php echo number_format($cost_pie_chart['heating_district']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_heating_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalCoolingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Cooling</td>
                                                                    <td><?php echo  number_format($cost_pie_chart['cooling_district']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_cooling_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalWater) { ?>
                                                                <tr>
                                                                    <td>Water</td>
                                                                    <td><?php echo number_format($cost_pie_chart['water']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_cmonth_water_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Total</strong></td>
                                                                <td width="35%"><strong><?php echo number_format($cost_pie_chart_t_sum); ?></strong></td>                                                                
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <td width="100%">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%"><img height="200" src="<?php echo $pieChartNew2Img; ?>" /></td>
                                <td width="50%"><img height="200" src="<?php echo $pieChartNew3Img; ?>" /></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="2" cellspacing="0">
                                        <thead>
                                            <tr>
											<th style="background-color:#d8e1f2;" align="center"><strong><?php echo 'Energy Consumption (% Share Of Total kWh)' . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td valign="top">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
															<td width="38%"><strong>Utilities</strong></td>
															<td width="38%"><strong>Consumption (kWh)</strong></td>
															<td width="24%"><strong>% Share</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if ($totalElectricity) { ?>
                                                                <tr>
                                                                    <td width="38%">Electricity</td>
                                                                    <td width="38%"><?php echo number_format(round($kwh_pie_chart_previousmonth_electricity_value, 0)); ?></td>
                                                                    <td width="24%"><?php echo floatval((string)$kwh_pie_chart_previousmonth_electricity_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalFuel) { ?>
                                                                <tr>
                                                                    <td>Fuel</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_previousmonth_fuel_value, 0)); ?></td>
                                                                    <td><?php echo floatval((string)$kwh_pie_chart_previousmonth_fuel_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalLpg) { ?>
                                                                <tr>
                                                                    <td>LPG</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_previousmonth_lpg_value, 0)); ?></td>
                                                                    <td><?php echo floatval((string)$kwh_pie_chart_previousmonth_lpg_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalNaturalGas) { ?>
                                                                <tr>
                                                                    <td>Natural Gas</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_previousmonth_natural_gas_value, 0)); ?></td>
                                                                    <td><?php echo floatval((string)$kwh_pie_chart_previousmonth_natural_gas_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalHeatingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Heating</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_previousmonth_heating_district_value, 0)); ?></td>
                                                                    <td><?php echo floatval((string)$kwh_pie_chart_previousmonth_heating_district_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalCoolingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Cooling</td>
                                                                    <td><?php echo number_format(round($kwh_pie_chart_previousmonth_cooling_district_value, 0)); ?></td>
                                                                    <td><?php echo floatval((string)$kwh_pie_chart_previousmonth_cooling_district_share); ?>%</td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td >
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="38%"><strong>Total</strong></td>
                                                                <td width="38%"><strong><?php echo number_format(round($kwh_pie_chart_previousmonth_utility_kwh_total,0)); ?></strong></td>
                                                                <td width="24%"><strong><?php echo floatval((string)$total_share_lastyear); ?>%</strong></td>               
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="2" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="background-color:#d8e1f2;" align="center"><strong><?php echo lang("cost-pie-chart-last12month-title") . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Utilities</strong></td>
                                                                <td width="35%"><strong>Cost (<?php echo CURRENCY_SYMBOL ?>)</strong></td>
                                                                <td width="20%"><strong>% Share</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if ($totalElectricity) { ?>
                                                                <tr>
                                                                    <td width="45%">Electricity</td>
                                                                    <td width="35%"><?php echo number_format($cost_pie_chart_previousmonth['electricity']); ?></td>
                                                                    <td width="20%"><?php echo $kwh_pie_chart_previousmonth_cost_electricity_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalFuel) { ?>
                                                                <tr>
                                                                    <td>Fuel</td>
                                                                    <td><?php echo number_format($cost_pie_chart_previousmonth['fuel']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_fuel_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalLpg) { ?>
                                                                <tr>
                                                                    <td>LPG</td>
                                                                    <td><?php echo number_format($cost_pie_chart_previousmonth['lpg']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_lPG_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalNaturalGas) { ?>
                                                                <tr>
                                                                    <td>Natural Gas</td>
                                                                    <td><?php echo number_format($cost_pie_chart_previousmonth['natural_gas']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_gas_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalHeatingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Heating</td>
                                                                    <td><?php echo number_format($cost_pie_chart_previousmonth['heating_district']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_heating_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalCoolingDistrict) { ?>
                                                                <tr>
                                                                    <td>District Cooling</td>
                                                                    <td><?php echo number_format($cost_pie_chart_previousmonth['cooling_district']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_cooling_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>

                                                            <?php if ($totalWater) { ?>
                                                                <tr>
                                                                    <td>Water</td>
                                                                    <td><?php echo  number_format($cost_pie_chart_previousmonth['water']); ?></td>
                                                                    <td><?php echo $kwh_pie_chart_previousmonth_cost_water_share; ?>%</td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Total</strong></td>
                                                                <td width="35%"><strong><?php echo number_format($cost_pie_chart_previousmonth_t_sum); ?></strong></td>                                                                
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>