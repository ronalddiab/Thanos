<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year-1;

if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) { // If start and end year is same
    for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']["end_month"]; $i++) {
        $startmonthsarray[] = $i;
    }

    $resultkeys = array();
    $resultkeys[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray;
} else { // If start and end year is not same
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

$cost_pie_chart_pre_h_sum = ($cost_pie_chart_pre['electricity']+$cost_pie_chart_pre['fuel']+$cost_pie_chart_pre['lpg']+$cost_pie_chart_pre['natural_gas']+$cost_pie_chart_pre['heating_district']+$cost_pie_chart_pre['cooling_district']+$cost_pie_chart_pre['water']);

?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;"> 
            <?php 
            $electricity_value = $kwh_pie_chart_pre['electricity'];
            $fuel_value = $kwh_pie_chart_pre['fuel'];
            $lpg_value = $kwh_pie_chart_pre['lpg'];
            $natural_gas_value = $kwh_pie_chart_pre['natural_gas'];
            $heating_district_value = $kwh_pie_chart_pre['heating_district'];
            $cooling_district_value = $kwh_pie_chart_pre['cooling_district'];

            $utility_kwh_total = ($electricity_value+$fuel_value+$lpg_value+$natural_gas_value+$heating_district_value+$cooling_district_value);

            $electricity_share = round(($electricity_value*100)/$utility_kwh_total,1);
            $fuel_share = round(($fuel_value*100)/$utility_kwh_total,1);
            $lpg_share = round(($lpg_value*100)/$utility_kwh_total,1);
            $natural_gas_share = round(($natural_gas_value*100)/$utility_kwh_total,1);
            $heating_district_share = round(($heating_district_value*100)/$utility_kwh_total,1);
            $cooling_district_share = round(($cooling_district_value*100)/$utility_kwh_total,1);

            $total_share = ($electricity_share+$fuel_share+$lpg_share+$natural_gas_share+$heating_district_share+$cooling_district_share);
              
            $cost_share_electricity  = round(($cost_pie_chart_pre['electricity'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_fuel  = round(($cost_pie_chart_pre['fuel'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_lPG  = round(($cost_pie_chart_pre['lpg'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_gas  = round(($cost_pie_chart_pre['natural_gas'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_heating  = round(($cost_pie_chart_pre['heating_district'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_cooling  = round(($cost_pie_chart_pre['cooling_district'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            $cost_share_water  = round(($cost_pie_chart_pre['water'] * 100) / $cost_pie_chart_pre_h_sum, 1);
            ?>   
            <table width="100%" border="0" cellpadding="5" cellspacing="5">
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="5" cellspacing="0">
                            <tr>
                                <td width="50%"><img height="350" src="<?php echo $pieChartImg; ?>" /></td>
                                <td width="50%"><img height="350" src="<?php echo $pieChartNewImg; ?>" /></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="5" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th align="center" style="background-color:#d8e1f2;"><strong><?php echo lang("kWh-pie-chart-last12month-title").' - '.$filters["report_year_pre"]; ?></strong></th>
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
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if($totalElectricity_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td width="38%">Electricity</td>
                                                                <td width="38%"><?php echo number_format(round($electricity_value,0)); ?></td>
                                                                <td width="24%"><?php echo $electricity_share; ?>%</td>
                                                            </tr>
                                                            <?php } ?>

                                                            <?php if($totalFuel_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>Fuel</td>
                                                                <td><?php echo number_format(round($fuel_value,0)); ?></td>
                                                                <td><?php echo $fuel_share; ?>%</td>
                                                            </tr>
                                                            <?php } ?>

                                                            <?php if($totalLpg_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>LPG</td>
                                                                <td><?php echo number_format(round($lpg_value,0)); ?></td>
                                                                <td><?php echo $lpg_share; ?>%</td>
                                                            </tr>
                                                            <?php } ?>

                                                            <?php if($totalNaturalGas_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>Natural Gas</td>
                                                                <td><?php echo number_format(round($natural_gas_value,0)); ?></td>
                                                                <td><?php echo $natural_gas_share; ?>%</td>
                                                            </tr>
                                                            <?php } ?>

                                                            <?php if($totalHeatingDistrict_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>District Heating</td>
                                                                <td><?php echo number_format(round($heating_district_value,0)); ?></td>
                                                                <td><?php echo $heating_district_share; ?>%</td>
                                                            </tr>
                                                            <?php } ?>

                                                            <?php if($totalCoolingDistrict_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>District Cooling</td>
                                                                <td><?php echo number_format(round($cooling_district_value,0)); ?></td>
                                                                <td><?php echo $cooling_district_share; ?>%</td>
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
                                                                <td width="38%"><strong><?php echo number_format(round($utility_kwh_total,0)); ?></strong></td>
                                                                <td width="24%"><strong><?php echo round($total_share); ?>%</strong></td>               
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </td>
                                <td>                                    
                                    <table border="1" width="100%" cellpadding="5" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="background-color:#d8e1f2;" align="center"><strong><?php echo lang("cost-pie-chart-last12month-title").' - '.$filters["report_year_pre"]; ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Utilities</strong></td>
                                                                <td width="35%"><strong>Cost (<?php echo REPORT_CURRENCY_SYMBOL  ?>)</strong></td>
                                                                <td width="20%"><strong>% Share</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <?php if($totalElectricity_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td width="45%">Electricity</td>
                                                                <td width="35%"><?php echo number_format($cost_pie_chart_pre['electricity']); ?></td>
                                                                <td width="20%"><?php echo $cost_share_electricity; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php if($totalFuel_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>Fuel</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['fuel']); ?></td>
                                                                <td><?php echo $cost_share_fuel; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php if($totalLpg_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>LPG</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['lpg']); ?></td>
                                                                <td><?php echo $cost_share_lPG; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php if($totalNaturalGas_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>Natural Gas</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['natural_gas']); ?></td>
                                                                <td><?php echo $cost_share_gas; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php if($totalHeatingDistrict_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>District Heating</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['heating_district']); ?></td>
                                                                <td><?php echo $cost_share_heating; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php if($totalCoolingDistrict_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>District Cooling</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['cooling_district']); ?></td>
                                                                <td><?php echo $cost_share_cooling; ?>%</td>
                                                            </tr>
                                                            <?php } ?>
                                                            
                                                            <?php if($totalWater_utility_cost_pre){ ?>
                                                            <tr>
                                                                <td>Water</td>
                                                                <td><?php echo number_format($cost_pie_chart_pre['water']); ?></td>
                                                                <td><?php echo $cost_share_water; ?>%</td>
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
                                                                <td width="35%"><strong><?php echo number_format(round($cost_pie_chart_pre_h_sum)); ?></strong></td>
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