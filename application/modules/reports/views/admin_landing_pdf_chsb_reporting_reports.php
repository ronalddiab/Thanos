<?php
if (empty($measures) || empty($measure_readings)) {
    return;
}

$chsb_stat_keys = array('low', 'lower_quartile', 'mean', 'median', 'upper_quartile', 'high');
$chsb_rows = array(
    array('key' => 'HCMIRoomsFootprintPerOccupiedRoom', 'reading' => 1, 'decimals' => 0),
    array('key' => 'HotelCarbonFootprintPerRoom', 'reading' => 2, 'decimals' => 0),
    array('key' => 'HotelCarbonFootprintPerOccupiedRoom', 'reading' => 3, 'decimals' => 0),
    array('key' => 'HotelCarbonFootprintPerSquareMeter', 'reading' => 4, 'decimals' => 0),
    array('key' => 'HotelEnergyUsagePerOccupiedRoom', 'reading' => 6, 'decimals' => 0),
    array('key' => 'HotelEnergyUsagePerSquareMeter', 'reading' => 7, 'decimals' => 0),
    array('key' => 'HCMIMeetingFootprintPerMeetingHour', 'reading' => 9, 'decimals' => 2),
    array('key' => 'HotelWaterUsagePerOccupiedRoom', 'reading' => 10, 'decimals' => 0),
    array('key' => 'HotelWaterUsagePerSquareMeter', 'reading' => 11, 'decimals' => 0),
    array('key' => 'HWMIRoomsWaterUsagePerOccupiedRoom', 'reading' => 13, 'decimals' => 0),
    array('key' => 'HWMIMeetingWaterUsagePerMeetingHour', 'reading' => 14, 'decimals' => 2),
    array('key' => 'RenewableEnergyPercentage', 'reading' => 15, 'decimals' => 2, 'percent' => true, 'higher_better' => true),
    array('key' => 'RenewableElectricityPercentage', 'reading' => 16, 'decimals' => 2, 'percent' => true, 'higher_better' => true),
    array('key' => 'ElectricityToNonElectricEnergy', 'reading' => 17, 'decimals' => 2, 'no_color' => true),
);
?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid #f69546;">
            <table width="100%" cellpadding="0" cellspacing="4">
                <tr>
                    <td>
                        <table>
                            <tr style="font-size:14px;">
                                <td align="center">
                                    <strong><center>Cornell Hotel Sustainability Benchmarking Report</center></strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" cellpadding="5" border="1" cellspacing="0">
                            <tr>
                                <td width="30%" style="background-color:#d8e1f2;" align="center"><strong>Measures</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>Low</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>Lower Quartile</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>Mean</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>Median</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>Upper Quartile</strong></td>
                                <td width="8%" style="background-color:#d8e1f2;" align="center"><strong>High</strong></td>
                                <td width="14%" style="background-color:#d8e1f2;" align="center"><strong><?php echo $site_detail['site_location_name']; ?></strong></td>
                            </tr>
                            <?php foreach ($chsb_rows as $chsb_row) {
                                $reading_id = $chsb_row['reading'];
                                if (empty($measure_readings[$reading_id])) {
                                    continue;
                                }
                                $reading = $measure_readings[$reading_id];
                                $decimals = $chsb_row['decimals'];
                                $is_percent = !empty($chsb_row['percent']);
                                $site_value = isset($measures[$chsb_row['key']]['chsb_value'])
                                    ? $measures[$chsb_row['key']]['chsb_value']
                                    : null;
                                $chsb_color = array('background' => '', 'font' => '');
                                $site_style = '';
                                if (empty($chsb_row['no_color'])) {
                                    $chsb_color = getChsbColor(
                                        $site_value,
                                        $reading,
                                        !empty($chsb_row['higher_better'])
                                    );
                                    $site_style = 'background-color: ' . $chsb_color['background'] . '; color: ' . $chsb_color['font'] . ';';
                                }
                            ?>
                            <tr>
                                <td align="left"><?php echo $reading['measure_name']; ?></td>
                                <?php foreach ($chsb_stat_keys as $stat_key) {
                                    $cell_value = isset($reading[$stat_key]) ? $reading[$stat_key] : 0;
                                    $formatted = $is_percent
                                        ? number_format($cell_value * 100, 2) . '%'
                                        : number_format($cell_value, $decimals);
                                ?>
                                <td align="center"><?php echo $formatted; ?></td>
                                <?php } ?>
                                <td align="center" style="<?php echo $site_style; ?>">
                                    <?php
                                    if ($is_percent) {
                                        echo number_format(((float) $site_value) * 100, 2) . '%';
                                    } elseif (!empty($site_value)) {
                                        echo number_format($site_value, $decimals);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <div style="font-size: 11px; line-height: 20px;">
            <span style="background-color:#008000; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Better than every hotel in the peer group
            &nbsp;&nbsp;&nbsp;
            <span style="background-color:#90EE90; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Top quartile — among the best 25%
            <br>
            <span style="background-color:#C6E0B4; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Second quartile — better than the peer median
            &nbsp;&nbsp;&nbsp;
            <span style="background-color:#BDD7EE; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Exactly at the peer median
            <br>
            <span style="background-color:#FFFF00; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Third quartile — worse than the peer median
            &nbsp;&nbsp;&nbsp;
            <span style="background-color:#FFA500; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Bottom quartile — among the worst 25%
            <br>
            <span style="background-color:#FF0000; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Worse than every hotel in the peer group
            &nbsp;&nbsp;&nbsp;
            <span style="background-color:#D3D3D3; padding:5px 5px;">&nbsp;&nbsp;&nbsp;</span>
            Not enough data to calculate
        </div>
    </body>
</html>
