<?php
$wastePerGuest = $WasteReport['wastePerGuest'];
$wasteReportArray = $WasteReport['wasteReport'];
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
?>

<!-- ============ WASTE REPORT TABLE ============ -->
<br><br><div style="border:2px solid  #f69546;padding:6px;">';
<br><br>';
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr style="color:blue;" align="center">
        <td colspan="4"><strong>Waste Report</strong></td>
    </tr>
    <tr style="color:black; background-color:#d8e1f2;" align="center">
        <th><strong>Metric</strong></th>
        <th><strong><?php echo $fullmontharray[$WasteReport['currentMonth']] . " - " . ($WasteReport['currentYear']); ?></strong></th>
        <th><strong><?php echo $fullmontharray[$WasteReport['currentMonth']] . " - " . ($WasteReport['currentYear'] - 1); ?></strong></th>
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
</table>

<br><br>

<!-- ============ WASTE PER GUEST TABLE ============ -->
<tr style="color:blue;" align="center">
    <td colspan="4"><strong>Waste per Guest / Occupied Room</strong></td>
</tr>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr style="color:black; background-color:#d8e1f2;" align="center">
        <th><strong>Metric</strong></th>
        <th><strong><?php echo $fullmontharray[$WasteReport['currentMonth']] . " - " . ($WasteReport['currentYear']); ?></strong></th>
        <th><strong><?php echo $fullmontharray[$WasteReport['currentMonth']] . " - " . ($WasteReport['currentYear'] - 1); ?></strong></th>
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
</table>
</div>