<?php
$isLocal = true;
if ($currency == "base") {
    $isLocal = false;
}
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$utilities_list = array('electricity' => 'Electricity', 'fuel' => 'Fuel', 'lpg' => 'LPG', 'natural_gas' => 'Natural Gas', 'water' => 'Water', 'heating_district' => 'District Heating', 'cooling_district' => 'District Cooling');

$utilities_value_check = array(
    'electricity' => 'totalElectricity_utility_cost_5years',
    'fuel' => 'totalFuel_utility_cost_5years',
    'lpg' => 'totalLpg_utility_cost_5years',
    'natural_gas' => 'totalNaturalGas_utility_cost_5years',
    'water' => 'totalWater_utility_cost_5years',
    'heating_district' => 'totalHeatingDistrict_utility_cost_5years',
    'cooling_district' => 'totalCoolingDistrict_utility_cost_5years',
);
?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" cellpadding="5" cellspacing="5">
                <?php if ($show_site_details == false) { ?>
                    <tr colspan="2" style="font-size:15px;color:blue;">
                        <td><strong><?php echo $pdf_report_title; ?></strong></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td width="100%">
                        <img src="<?php echo $columnChartImg; ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        <?php if (!empty($utility_cost_chart_5years)) {
                            ?>
                            <table width="100%" cellpadding="3" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th align="center" style="background-color:#d8e1f2;border-top-color:#000000;border-top-width:1px;border-top-style:solid;border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid;border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><strong>Utilities</strong></th>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $value) {
                                            ?>
                                            <th align="center" style="background-color:#d8e1f2;border-top-color:#000000;border-top-width:1px;border-top-style:solid;border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><strong>Year <?php echo $year; ?></strong></th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($utilities_list as $key => $value) {
                                        if (${$utilities_value_check[$key]}) {
                                            ?>
                                            <tr>
                                                <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo $value; ?></td>
                                                <?php
                                                foreach ($utility_cost_chart_5years as $year => $year_value) {
                                                    ?>
                                                    <th align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo currency_symbol($isLocal) . number_format($year_value[$key]); ?></th>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td style="border-top-color:#000000;border-top-width:1px;border-top-style:solid;border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid;border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><strong>Total</strong></td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            $total = 0;
                                            foreach ($utilities_list as $key => $value) {
                                                $total += $year_value[$key];
                                            }
                                            ?>
                                            <th align="center" style="border-top-color:#000000;border-top-width:1px;border-top-style:solid;border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><strong><?php echo currency_symbol($isLocal) . number_format($total); ?></strong></th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">CDD</td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            ?>
                                            <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo number_format($year_value['cdd']); ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">HDD</td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            ?>
                                            <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo number_format($year_value['hdd']); ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">Room nights</td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            ?>
                                            <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo number_format($year_value['room_night']); ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <?php if ($totalElectricity_utility_cost_5years) { ?>
                                        <tr>
                                            <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">Electricity Tariff (<?php echo currency_symbol($isLocal) ?>/<?php echo GetSiteUtilityUnitName($site_id,'electricity'); ?>)</td>
                                            <?php
                                            foreach ($utility_cost_chart_5years as $year => $year_value) {
                                                ?>
                                                <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo currency_symbol($isLocal) . ' ' . round($year_value['electricity_tariff'], 2); ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo GetSiteUtilityUnitName($site_id,'electricity'); ?> / Room Night</td>
                                            <?php
                                            foreach ($utility_cost_chart_5years as $year => $year_value) {
                                                ?>
                                                <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo round(($year_value['total_electricity_kwh'] / $year_value['room_night']), 2); ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo GetSiteUtilityUnitName($site_id,'electricity'); ?> / m<sup>2</sup></td>
                                            <?php
                                            foreach ($utility_cost_chart_5years as $year => $year_value) {
                                                ?>
                                                <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo round(($year_value['total_electricity_kwh'] / $site_detail['site_builtup_area']), 2); ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($totalWater_utility_cost_5years) { ?>
                                        <tr>
                                            <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">Water (<?php echo GetSiteUtilityUnitName($site_id,'water'); ?>) / Room Night</td>
                                            <?php
                                            foreach ($utility_cost_chart_5years as $year => $year_value) {
                                                ?>
                                                <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo round(($year_value['water_unit'] / $year_value['room_night']), 2); ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;">Utilities Cost (<?php echo currency_symbol($isLocal); ?>) / Room Night</td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            $total = 0;
                                            foreach ($utilities_list as $key => $value) {
                                                $total += $year_value[$key];
                                            }
                                            ?>
                                            <td align="center" style="border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo round(($total / $year_value['room_night']), 2); ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <td style="border-left-color:#000000;border-left-width:1px;border-left-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; ">Utilities Cost (<?php echo currency_symbol($isLocal); ?>) / <?php echo GetSiteUtilityUnitName($site_id,'water'); ?></td>
                                        <?php
                                        foreach ($utility_cost_chart_5years as $year => $year_value) {
                                            $total = 0;
                                            foreach ($utilities_list as $key => $value) {
                                                $total += $year_value[$key];
                                            }
                                            ?>
                                            <td align="center" style="border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid;border-right-color:#000000;border-right-width:1px;border-right-style:solid;"><?php echo round(($total / $site_detail['site_builtup_area']), 2); ?></td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                            ?><?php } ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>