<?php
$wastePerGuest = $WasteReport['wastePerGuest'];
$wasteReportArray = $WasteReport['wasteReport'];
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$currentPeriod = !empty($WasteReport['isYtd'])
    ? 'YTD - ' . $WasteReport['currentYear']
    : $fullmontharray[$WasteReport['currentMonth']] . ' - ' . $WasteReport['currentYear'];
$previousPeriod = !empty($WasteReport['isYtd'])
    ? 'YTD - ' . ($WasteReport['currentYear'] - 1)
    : $fullmontharray[$WasteReport['currentMonth']] . ' - ' . ($WasteReport['currentYear'] - 1);
?>

<!-- ============ WASTE REPORT TABLE ============ -->
<br><br><div style="border:2px solid  #f69546;padding:6px; display:flex; flex-direction:column;align-items:center;justify-content:center;">
<br><br>
<!-- <table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr style="color:blue;" align="center">
        <td colspan="4"><strong>Waste Report</strong></td>
    </tr>
    <tr style="color:black; background-color:#d8e1f2;" align="center">
        <th><strong>Metric</strong></th>
        <th><strong><?php echo $currentPeriod; ?></strong></th>
        <th><strong><?php echo $previousPeriod; ?></strong></th>
        <th><strong>Variation (%)</strong></th>
    </tr>

    <?php foreach ($wasteReportArray as $row): ?>
        <tr align="center">
            <td><strong><?= $row['metric'] ?></strong></td>
            <td><?= $row['current'] ?></td>
            <td><?= $row['previous'] ?></td>
            <td><?= $row['change'] ?></td>
        </tr>
    <?php endforeach; ?>
</table> -->

<!-- <br><br> -->

<!-- ============ WASTE PER GUEST TABLE ============ -->
<!-- <tr style="color:blue;" align="center">
    <td colspan="4"><strong>Waste per Guest / Occupied Room</strong></td>
</tr>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr style="color:black; background-color:#d8e1f2;" align="center">
        <th><strong>Metric</strong></th>
        <th><strong><?php echo $currentPeriod; ?></strong></th>
        <th><strong><?php echo $previousPeriod; ?></strong></th>
        <th><strong>Variation (%)</strong></th>
    </tr>

    <?php foreach ($wastePerGuest as $row): ?>
        <tr align="center">
            <td><strong><?= $row['metric'] ?></strong></td>
            <td><?= $row['current'] ?></td>
            <td><?= $row['previous'] ?></td>
            <td><?= $row['value'] ?></td>
        </tr>
    <?php endforeach; ?>
</table> -->

<?php

function getVariation($value)
{
    $value = (float) str_replace('%', '', $value);

    if ($value < 0) {
        return array(
            'color' => '#df3165',
            'symbol' => '-'
        );
    }

    if ($value > 0) {
        return array(
            'color' => '#239568',
            'symbol' => '+'
        );
    }

    return array(
        'color' => '#718096',
        'symbol' => ''
    );
}


function getMetricImg($metric)
{
    $metricImg = array(
        'Total waste generated (kg)'       => 'total_waste',
        'Organic / food waste (kg)'        => 'food_waste',
        'Recyclables (kg)'                 => 'recyclable',
        'Waste To Energy'                  => 'waste_energy',
        'Hazardous waste (kg)'             => 'hazard',
        'Waste diverted from landfill (%)' => 'landfill',
        'Recyclables Rate'                 => 'recyclable_rate',

        'Waste (kg/Room night)'            => 'waste',
        'Organic waste (kg/Guest Night)'   => 'organic_waste',
        'Recyclables (kg/Guest Night)'     => 'recyclable_per_night'
    );

    if (!isset($metricImg[$metric])) {
        return '';
    }

    return base_url() . 'assets/waste/' . $metricImg[$metric] . '.png';
}


/*
 * Convert:
 *
 * Total waste generated (kg)
 *
 * into:
 *
 * Total waste generated
 * (kg)
 */
function getMetricName($metric)
{
    $pos = strrpos($metric, '(');

    if ($pos !== false) {
        return trim(substr($metric, 0, $pos));
    }

    return $metric;
}


function getMetricUnit($metric)
{
    $pos = strrpos($metric, '(');

    if ($pos !== false) {
        return trim(substr($metric, $pos));
    }

    return '';
}

?>


<!-- =========================================================
     MAIN WRAPPER
========================================================= -->

<table
    width="90%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="border-collapse:collapse;"
>
    <tr>
        <td style="padding:0;">
<!-- Title -->
            <table
                width="90%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    border-collapse:collapse;
                    background-color:#eaf5f7;
                "
                >
                <tr>
                    <td
                        style="
                            padding:0 0 0 12px;
                            vertical-align:middle;
                        "
                    >

                        <table
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="border-collapse:collapse;"
                        >
                            <tr>

                                <td
                                    style="
                                        width:1px;
                                        background-color:#78a9b7;
                                        padding:0;
                                    "
                                >
                                    &nbsp;
                                </td>

                                <td
                                    style="
                                        padding:0 0 0 12px;
                                        vertical-align:middle;
                                    "
                                >

                                    <div
                                        style="
                                            font-size:16px;
                                            font-weight:bold;
                                            color:#173f70;
                                        "
                                    >
                                        Waste Report
                                    </div>

                                    <div
                                        style="
                                            font-size:9px;
                                            color:#426789;
                                            padding-top:2px;
                                        "
                                    >
                                        <?= htmlspecialchars($currentPeriod) ?>
                                        vs
                                        <?= htmlspecialchars($previousPeriod) ?>
                                    </div>

                                </td>

                            </tr>
                        </table>

                    </td>
                </tr>
            </table>


            <!-- GAP -->

            <div style="height:7px;"></div>


            <!-- WASTE GENERATED & DIVERSION-->

            <table
                width="90%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    border-collapse:collapse;
                    border:1px solid #b9d5df;
                "
                >

                <!-- SECTION HEADER -->

                <tr>
                    <td
                        colspan="4"
                        style="
                            /* height:24px; */
                            padding:0 8px;
                            background-color:#285d82;
                            color:#ffffff;
                            font-size:10px;
                            font-weight:bold;
                            vertical-align:middle;
                            line-height: 2;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/waste_generated.png"
                            width="13"
                            height="10"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        Waste Generated &amp; Diversion
                    </td>
                </tr>


                <!-- COLUMN HEADER -->

                <tr>

                    <td
                        width="34%"
                        style="
                            /* height:20px; */
                            padding:3px 6px;
                            background-color:#f1f6f8;
                            color:#315b68;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dce5;
                            line-height: 3;
                        "
                    >
                        Metric
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            background-color:#d9ebfa;
                            color:#173f70;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dce5;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/blue_cal.png"
                            width="8"
                            height="8"
                            alt=""
                        >
                        &nbsp;
                        <?= htmlspecialchars($currentPeriod) ?>
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            padding:3px;
                            background-color:#e5ebf3;
                            color:#294f6d;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dce5;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/violet_cal.png"
                            width="8"
                            height="8"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        <?= htmlspecialchars($previousPeriod) ?>
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            padding:3px;
                            background-color:#dff2e9;
                            color:#237353;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dce5;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/variation.png"
                            width="9"
                            height="9"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        Variation (%)
                    </td>

                </tr>


                <!-- DATA -->

                <?php foreach ($wasteReportArray as $row): ?>

                    <?php
                        $variation = getVariation($row['change']);
                        $metricName = getMetricName($row['metric']);
                        $metricUnit = getMetricUnit($row['metric']);
                    ?>

                    <tr style="page-break-inside:avoid;">

                        <!-- METRIC -->

                        <td
                            width="34%"
                            style="
                                background-color:#ffffff;
                                border:1px solid #d9e4e9;
                                vertical-align:middle;
                                line-height: 2;
                            "
                        >
                        <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="border-collapse:collapse;"
                    >
                        <tr>
                            <td width="20%" align="center" style="line-height: 4;">
                                <img
                                    src="<?= getMetricImg($row['metric']) ?>"
                                    width="14"
                                    height="14"
                                    alt=""
                                    style="vertical-align:middle;"
                                >
                            </td>
                            <td width="80%" align="left">
                                <div style="display:inline-block; margin-left:6px; vertical-align:middle; line-height: 1;">
                                    <span
                                        style="
                                            color:#173f70;
                                            font-size:7px;
                                            font-weight:bold;
                                            vertical-align:middle;
                                        "
                                    >
                                        <?= htmlspecialchars($metricName) ?>
                                    </span>

                                    <?php if ($metricUnit != ''): ?>

                                        <br>

                                        <span
                                            style="
                                                color:#315b68;
                                                font-size:5px;
                                                margin-left:20px;
                                            "
                                        >
                                            <?= htmlspecialchars($metricUnit) ?>
                                        </span>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                        </td>


                        <!-- CURRENT -->

                        <td
                            width="22%"
                            align="center"
                            style="
                                padding:2px;
                                background-color:#eef7fd;
                                border:1px solid #d9e4e9;
                                color:#173f70;
                                font-size:8px;
                                font-weight:bold;
                                vertical-align:middle;
                                line-height: 3;
                            "
                        >
                            <?= htmlspecialchars($row['current']) ?>
                        </td>


                        <!-- PREVIOUS -->

                        <td
                            width="22%"
                            align="center"
                            style="
                                height:30px;
                                padding:2px;
                                background-color:#f5f8fb;
                                border:1px solid #d9e4e9;
                                color:#294f6d;
                                font-size:8px;
                                vertical-align:middle;
                                line-height: 3;
                            "
                        >
                            <?= htmlspecialchars($row['previous']) ?>
                        </td>


                        <!-- VARIATION -->

                        <td
                            width="22%"
                            align="center"
                            style="
                                height:30px;
                                padding:2px;
                                background-color:#eff9f4;
                                border:1px solid #d9e4e9;
                                color:<?= $variation['color'] ?>;
                                font-size:8px;
                                font-weight:bold;
                                vertical-align:middle;
                                line-height: 3;
                            "
                        >

                            <?php if ($variation['symbol'] != ''): ?>

                                <span
                                    style="
                                        font-size:7px;
                                        font-weight:bold;
                                    "
                                >
                                    <?= $variation['symbol'] ?>
                                </span>

                                &nbsp;

                            <?php endif; ?>

                            <?= htmlspecialchars($row['change']) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>


            <!-- GAP -->

            <div style="height:7px;"></div>


            <!-- WASTE PER GUEST / OCCUPIED ROOM -->

            <table
                width="90%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    border-collapse:collapse;
                    border:1px solid #9bcfc7;
                "
                >

                <!-- SECTION HEADER -->

                <tr>
                    <td
                        colspan="4"
                        style="
                            height:24px;
                            padding:0 8px;
                            background-color:#075f57;
                            color:#ffffff;
                            font-size:10px;
                            font-weight:bold;
                            vertical-align:middle;
                            line-height: 2;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/waste_per_guest.png"
                            width="13"
                            height="13"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        Waste per Guest / Occupied Room
                    </td>
                </tr>


                <!-- COLUMN HEADER -->

                <tr>

                    <td
                        width="34%"
                        style="
                            height:20px;
                            padding:3px 6px;
                            background-color:#f1f8f7;
                            color:#315b68;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dddd;
                            line-height: 2;
                        "
                    >
                        Metric
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            height:20px;
                            padding:3px;
                            background-color:#dff2ec;
                            color:#17695f;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dddd;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/green_cal.png"
                            width="8"
                            height="8"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        <?= htmlspecialchars($currentPeriod) ?>
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            height:20px;
                            padding:3px;
                            background-color:#e5ebf3;
                            color:#294f6d;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dddd;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/violet_cal.png"
                            width="8"
                            height="8"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        <?= htmlspecialchars($previousPeriod) ?>
                    </td>

                    <td
                        width="22%"
                        align="center"
                        style="
                            height:20px;
                            padding:3px;
                            background-color:#dff2e9;
                            color:#237353;
                            font-size:7px;
                            font-weight:bold;
                            border:1px solid #c7dddd;
                            line-height: 3;
                        "
                    >
                        <img
                            src="<?= base_url() ?>assets/waste/variation.png"
                            width="9"
                            height="9"
                            alt=""
                            style="vertical-align:middle;"
                        >
                        &nbsp;
                        Variation (%)
                    </td>

                </tr>


                <!-- DATA -->

                <?php foreach ($wastePerGuest as $row): ?>

                    <?php
                        $variation = getVariation($row['value']);
                        $metricName = getMetricName($row['metric']);
                        $metricUnit = getMetricUnit($row['metric']);
                    ?>

                    <tr style="page-break-inside:avoid;">

                        <!-- METRIC -->

                        <td
                            width="34%"
                            style="
                                /* height:30px; */
                                /* padding:2px 6px; */
                                background-color:#ffffff;
                                border:1px solid #d9e4e9;
                                vertical-align:middle;
                                line-height: 2;
                            "
                        >
                        <table
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="border-collapse:collapse;"
                        >
                            <tr>
                                <td width="20%" align="center" style="line-height: 4;">
                            <img
                                src="<?= getMetricImg($row['metric']) ?>"
                                width="14"
                                height="14"
                                alt=""
                                style="vertical-align:middle;"
                            >
                                </td>
                                <td width="80%" align="left">
                            <div style="display:inline-block; margin-left:20px; vertical-align:middle; line-height: 1;">
                                <span
                                    style="
                                        color:#17695f;
                                        font-size:7px;
                                        font-weight:bold;
                                        vertical-align:middle;
                                    "
                                >
                                    <?= htmlspecialchars($metricName) ?>
                                </span>

                                <?php if ($metricUnit != ''): ?>

                                    <br>

                                    <span
                                        style="
                                            color:#315b68;
                                            font-size:5px;
                                            margin-left:20px;
                                        "
                                    >
                                        <?= htmlspecialchars($metricUnit) ?>
                                    </span>

                                <?php endif; ?>
                            </div>
                                </td>
                            </tr>
                        </table>

                        </td>


                        <!-- CURRENT -->

                        <td
                            width="22%"
                            align="center"
                            style="
                                height:30px;
                                padding:2px;
                                background-color:#eff9f7;
                                border:1px solid #d9e4e9;
                                color:#17695f;
                                font-size:8px;
                                font-weight:bold;
                                vertical-align:middle;
                                line-height: 2;
                            "
                        >
                            <?= htmlspecialchars($row['current']) ?>
                        </td>


                        <!-- PREVIOUS -->

                        <td
                            width="22%"
                            align="center"
                            style="
                                height:30px;
                                padding:2px;
                                background-color:#f5f8fb;
                                border:1px solid #d9e4e9;
                                color:#294f6d;
                                font-size:8px;
                                vertical-align:middle;
                                line-height: 2;
                            "
                        >
                            <?= htmlspecialchars($row['previous']) ?>
                        </td>


                        <!-- VARIATION -->

                        <td
                            width="22%"
                            
                            style="
                                height:30px;
                                padding:2px;
                                background-color:#eff9f4;
                                border:1px solid #d9e4e9;
                                color:<?= $variation['color'] ?>;
                                font-size:8px;
                                font-weight:bold;
                                vertical-align:middle;
                                line-height: 2;
                                text-align:center;
                            "
                        >

                            <?php if ($variation['symbol'] != ''): ?>

                                <span
                                    style="
                                        font-size:7px;
                                        font-weight:bold;
                                    "
                                >
                                    <?= $variation['symbol'] ?>
                                </span>

                                &nbsp;

                            <?php endif; ?>

                            <?= htmlspecialchars($row['value']) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        </td>
    </tr>
</table>
</div>
</div>
