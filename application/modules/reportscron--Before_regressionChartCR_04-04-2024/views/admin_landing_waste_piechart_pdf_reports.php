<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year-1;

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
if(!empty($waste_pie_report)){
	$generalwaste = $waste_pie_report['generalwaste'];
	$paperwaste = $waste_pie_report['paperwaste'];
	$foodwaste = $waste_pie_report['foodwaste'];
	$cardboardwaste = $waste_pie_report['cardboardwaste'];
	$plasticwaste = $waste_pie_report['plasticwaste'];
	$glasswaste = $waste_pie_report['glasswaste'];
	
	$totalwaste = $generalwaste+$paperwaste+$foodwaste+$cardboardwaste+$plasticwaste+$glasswaste;
	
	$generalwasteShare = round(($generalwaste*100)/$totalwaste,1);
	$paperwasteShare = round(($paperwaste*100)/$totalwaste,1);
	$foodwasteShare = round(($foodwaste*100)/$totalwaste,1);
	$cardboardwasteShare = round(($cardboardwaste*100)/$totalwaste,1);
	$plasticwasteShare = round(($plasticwaste*100)/$totalwaste,1);
	$glasswasteShare = round(($glasswaste*100)/$totalwaste,1);
	
	$totalShare = $generalwasteShare+$paperwasteShare+$foodwasteShare+$cardboardwasteShare+$plasticwasteShare+$glasswasteShare;
}
if(!empty($waste_pie_landfill))
{
	$recycledwaste = $waste_pie_landfill['recycledwaste'];
	$landfill = $waste_pie_landfill['landfill'];
	$total = $recycledwaste + $landfill;
	$recycledwasteShare = round(($recycledwaste*100)/$total,1);
	$landfillShare = round(($landfill*100)/$total,1);
	$share = $landfillShare+$recycledwasteShare;
}
?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tr>
                    <td width="100%">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="50%"><img height="350" src="<?php echo $wastePieChartImg; ?>" /></td>
                                <td width="50%"><img height="350" src="<?php echo $wasteLandfillPieChartImg; ?>" /></td>
                            </tr>
                            <tr>
                                <td valign="top">
                                    <table border="1" width="100%" cellpadding="2" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th style="background-color:#d8e1f2;" align="center"><strong><?php echo lang("waste_pie_report"); ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="38%"><strong>Waste</strong></td>
                                                                <td width="38%"><strong>Kgs</strong></td>
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
                                                            <tr>
                                                                <td width="38%">General waste</td>
                                                                <td width="38%"><?php echo number_format(round($generalwaste,0)); ?></td>
                                                                <td width="24%"><?php echo $generalwasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Paper Waste</td>
                                                                <td><?php echo number_format(round($paperwaste,0)); ?></td>
                                                                <td><?php echo $paperwasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Food Waste</td>
                                                                <td><?php echo number_format(round($foodwaste,0)); ?></td>
                                                                <td><?php echo $foodwasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Cardboard Waste</td>
                                                                <td><?php echo number_format(round($cardboardwaste,0)); ?></td>
                                                                <td><?php echo $cardboardwasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Plastic Waste</td>
                                                                <td><?php echo number_format(round($plasticwaste,0)); ?></td>
                                                                <td><?php echo $plasticwasteShare; ?>%</td>
                                                            </tr>
															<tr>
                                                                <td>Glass Waste</td>
                                                                <td><?php echo number_format(round($glasswaste,0)); ?></td>
                                                                <td><?php echo $glasswasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
											<tr>
												<td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="38%"><strong>Total</strong></td>
                                                                <td width="38%"><strong><?php echo number_format($totalwaste); ?></strong></td>
                                                                <td width="24%"><strong><?php echo number_format(round($totalShare)).'%'; ?></strong></td>
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
                                                <th style="background-color:#d8e1f2;" align="center"><strong><?php echo lang("waste_landfill_pie_report"); ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td width="100%">
                                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                            <tr>
                                                                <td width="45%"><strong>Waste</strong></td>
                                                                <td width="35%"><strong>Kgs</strong></td>
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
                                                            <tr>
                                                                <td width="45%">Recycled Waste</td>
                                                                <td width="35%"><?php echo number_format($recycledwaste); ?></td>
                                                                <td width="20%"><?php echo $recycledwasteShare; ?>%</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Landfill</td>
                                                                <td><?php echo number_format($landfill); ?></td>
                                                                <td><?php echo $landfillShare; ?>%</td>
                                                            </tr>
                                                            
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
                                                                <td width="35%"><strong><?php echo number_format($total);?></strong></td>
                                                                <td width="20%"><strong><?php echo round($share); ?>%</strong></td>
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