<?php
$wastePerGuest = isset($WasteReport['wastePerGuest']) ? $WasteReport['wastePerGuest'] : array();
$wasteReportArray = isset($WasteReport['wasteReport']) ? $WasteReport['wasteReport'] : array();
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$currentPeriod = !empty($WasteReport['isYtd'])
    ? 'YTD - ' . $WasteReport['currentYear']
    : $fullmontharray[$WasteReport['currentMonth']] . ' - ' . $WasteReport['currentYear'];
$previousPeriod = !empty($WasteReport['isYtd'])
    ? 'YTD - ' . ($WasteReport['currentYear'] - 1)
    : $fullmontharray[$WasteReport['currentMonth']] . ' - ' . ($WasteReport['currentYear'] - 1);
if (!empty($WasteReport['isAnnual'])) {
    $currentPeriod = 'Annual - ' . $WasteReport['currentYear'];
    $previousPeriod = 'Annual - ' . ($WasteReport['currentYear'] - 1);
}

if (!function_exists('getVariation')) {
    function getVariation($value, $isHighBetter = false)
    {
        $value = (float) str_replace('%', '', $value);
        $base = base_url() . 'assets/waste/';
        if ($value > 0) {
            return $isHighBetter
                ? array('color' => '#278A68', 'symbol' => $base . 'green_up.png')
                : array('color' => '#CD3961', 'symbol' => $base . 'red_up.png');
        }
        if ($value < 0) {
            return $isHighBetter
                ? array('color' => '#CD3961', 'symbol' => $base . 'red_down.png')
                : array('color' => '#278A68', 'symbol' => $base . 'green_down.png');
        }
        return array('color' => '#94ACBA', 'symbol' => '-');
    }
}

if (!function_exists('getMetricImg')) {
    function getMetricImg($metric)
    {
        $metricImg = array(
            'Total waste generated (kg)' => 'total_waste_bag',
            'Organic / food waste (kg)' => 'organic_food',
            'Recyclables (kg)' => 'recyclables',
            'Waste To Energy' => 'waste_to_energy',
            'Hazardous waste (kg)' => 'hazardous_waste',
            'Waste diverted from landfill (%)' => 'diverted_from_landfill',
            'Recyclables Rate' => 'recyclables_rate',
            'Waste (kg/Room night)' => 'waste_per_room',
            'Organic waste (kg/Guest Night)' => 'organic_per_guest',
            'Recyclables (kg/Guest Night)' => 'recyclables_per_guest',
        );
        return isset($metricImg[$metric]) ? base_url() . 'assets/waste/' . $metricImg[$metric] . '.png' : '';
    }
}

if (!function_exists('getMetricName')) {
    function getMetricName($metric)
    {
        $pos = strrpos($metric, '(');
        return ($pos !== false) ? trim(substr($metric, 0, $pos)) : $metric;
    }
}

if (!function_exists('getMetricUnit')) {
    function getMetricUnit($metric)
    {
        if (strpos($metric, 'Recyclables Rate') !== false) {
            return '(%)';
        }
        if (strpos($metric, 'Waste To Energy') !== false) {
            return '(Kg)';
        }
        $pos = strrpos($metric, '(');
        return ($pos !== false) ? trim(substr($metric, $pos)) : '';
    }
}

$high_better_metrics = array('Recyclables (kg)', 'Recyclables Rate', 'Recyclables (kg/Guest Night)');
$waste_sections = array(
    array(
        'rows' => $wasteReportArray,
        'change_key' => 'change',
        'title' => 'Waste Generated &amp; Diversion',
        'header_bg' => '#14375E',
        'header_icon' => 'waste_section_icon.png',
        'header_icon_size' => 13,
        'table_border' => '#F0F7FA',
        'current_header_bg' => '#D3E7FA',
        'current_header_color' => '#14375E',
        'current_cal' => 'blue_cal.png',
        'current_cal_style' => 'vertical-align:middle;margin-top:10px',
        'current_cell_bg' => '#F0F7FA',
        'row_line_height' => 3,
        'metric_margin' => '6px',
    ),
    array(
        'rows' => $wastePerGuest,
        'change_key' => 'value',
        'title' => 'Waste per Guest / Occupied Room',
        'header_bg' => '#0A645E',
        'header_icon' => 'occupied_room.png',
        'header_icon_size' => 14,
        'table_border' => '#B6D1D8',
        'current_header_bg' => '#D3EEE5',
        'current_header_color' => '#0A645E',
        'current_cal' => 'green_cal.png',
        'current_cal_style' => 'vertical-align:middle;',
        'current_cell_bg' => '#D3EEE5',
        'row_line_height' => 2,
        'metric_margin' => '20px',
    ),
);
?>
<br><br><div style="border:2px solid #f69546;padding:6px;padding-left:20px;display:flex;justify-content:center;align-items:center;">
<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<table width="90%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-left: auto;margin-right: auto;">
    <tr>
        <td style="padding:0;">
            <table width="90%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;background-color:#D3E7FA;">
                <tr>
                    <td style="padding:0 0 0 12px;vertical-align:middle;">
                        <table cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                            <tr>
                                <td style="width:1px;background-color:#278073;padding:0;">&nbsp;</td>
                                <td style="padding:0 0 0 12px;vertical-align:middle;">
                                    <div style="font-size:16px;font-weight:bold;color:#14375E;">Waste Report</div>
                                    <div style="font-size:9px;color:#4E7897;padding-top:2px;">
                                        <?= htmlspecialchars($currentPeriod) ?> vs <?= htmlspecialchars($previousPeriod) ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <?php foreach ($waste_sections as $section) { ?>
            <div style="height:7px;"></div>
            <table width="90%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;border:1px solid <?= $section['table_border'] ?>;">
                <tr>
                    <td colspan="4" style="padding:0 8px;background-color:<?= $section['header_bg'] ?>;color:#FEFEFE;font-size:10px;font-weight:bold;vertical-align:middle;line-height: 2;">
                        &nbsp;&nbsp;&nbsp;
                        <img src="<?= base_url() ?>assets/waste/<?= $section['header_icon'] ?>" width="<?= $section['header_icon_size'] ?>" height="<?= $section['header_icon_size'] ?>" alt="" style="vertical-align:middle;">
                        &nbsp;
                        <?= $section['title'] ?>
                    </td>
                </tr>
                <tr>
                    <td width="34%" style="padding:3px 6px;background-color:#F0F7FA;color:#14375E;font-size:7px;font-weight:bold;border:1px solid #B6D1D8;line-height: 3;">
                        &nbsp;&nbsp;&nbsp;Metric
                    </td>
                    <td width="22%" align="center" style="background-color:<?= $section['current_header_bg'] ?>;color:<?= $section['current_header_color'] ?>;font-size:7px;font-weight:bold;border:1px solid #B6D1D8;line-height: 3;">
                        <img src="<?= base_url() ?>assets/waste/<?= $section['current_cal'] ?>" width="8" height="8" alt="" style="<?= $section['current_cal_style'] ?>">
                        &nbsp;
                        <?= htmlspecialchars($currentPeriod) ?>
                    </td>
                    <td width="22%" align="center" style="padding:3px;background-color:#F0F7FA;color:#14375E;font-size:7px;font-weight:bold;border:1px solid #B6D1D8;line-height: 3;">
                        <img src="<?= base_url() ?>assets/waste/violet_cal.png" width="8" height="8" alt="" style="vertical-align:middle;">
                        &nbsp;
                        <?= htmlspecialchars($previousPeriod) ?>
                    </td>
                    <td width="22%" align="center" style="padding:3px;background-color:#D3EEE5;color:#0A645E;font-size:7px;font-weight:bold;border:1px solid #B6D1D8;line-height: 3;">
                        <img src="<?= base_url() ?>assets/waste/variation.png" width="9" height="9" alt="" style="vertical-align:middle;">
                        &nbsp;
                        Variation (%)
                    </td>
                </tr>
                <?php foreach ($section['rows'] as $row) {
                    $isHighBetter = in_array($row['metric'], $high_better_metrics);
                    $change_value = isset($row[$section['change_key']]) ? $row[$section['change_key']] : '';
                    $variation = getVariation($change_value, $isHighBetter);
                    $metricName = getMetricName($row['metric']);
                    $metricUnit = getMetricUnit($row['metric']);
                    $metricImg = getMetricImg($row['metric']);
                ?>
                <tr style="page-break-inside:avoid;">
                    <td width="34%" style="background-color:#FEFEFE;border:1px solid #B6D1D8;vertical-align:middle;line-height: 2;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                            <tr>
                                <td width="20%" align="center" style="line-height: 4;">
                                    <?php if ($metricImg != '') { ?>
                                    <img src="<?= $metricImg ?>" width="14" height="14" alt="" style="vertical-align:middle;">
                                    <?php } ?>
                                </td>
                                <td width="80%" align="left">
                                    <div style="display:inline-block; margin-left:<?= $section['metric_margin'] ?>; vertical-align:middle; line-height: 1;">
                                        <span style="color:#14375E;font-size:7px;font-weight:bold;vertical-align:middle;">
                                            <?= htmlspecialchars($metricName) ?>
                                        </span>
                                        <?php if ($metricUnit != '') { ?>
                                        <br>
                                        <span style="color:#94ACBA;font-size:5px;margin-left:20px;"><?= htmlspecialchars($metricUnit) ?></span>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="22%" align="center" style="padding:2px;background-color:<?= $section['current_cell_bg'] ?>;border:1px solid #B6D1D8;color:#14375E;font-size:8px;font-weight:bold;vertical-align:middle;line-height: <?= $section['row_line_height'] ?>;">
                        <?= htmlspecialchars($row['current']) ?>
                    </td>
                    <td width="22%" align="center" style="height:30px;padding:2px;background-color:#FEFEFE;border:1px solid #B6D1D8;color:#14375E;font-size:8px;vertical-align:middle;line-height: <?= $section['row_line_height'] ?>;">
                        <?= htmlspecialchars($row['previous']) ?>
                    </td>
                    <td width="22%" align="center" style="height:30px;padding:2px;background-color:#D3EEE5;border:1px solid #B6D1D8;color:<?= $variation['color'] ?>;font-size:8px;font-weight:bold;vertical-align:middle;line-height: <?= $section['row_line_height'] ?>;text-align:center;">
                        <?php if ($variation['symbol'] != '') { ?>
                            <span style="font-size:7px;font-weight:bold;">
                                <?php if ($variation['symbol'] != '-') { ?>
                                    <img src="<?= $variation['symbol'] ?>" width="8" height="8" alt="" style="vertical-align:middle;">
                                <?php } else { ?>
                                    <?= $variation['symbol'] ?>
                                <?php } ?>
                            </span>
                            &nbsp;
                        <?php } ?>
                        <?= htmlspecialchars($change_value) ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </td>
    </tr>
</table>
</div>
