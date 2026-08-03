
<?php 
if(!empty($waste_monthly_pie_chart_results)){
	$generalwaste = $waste_monthly_pie_chart_results['operation_general_waste'];
	$paperwaste = $waste_monthly_pie_chart_results['operation_paper_waste'];
	$foodwaste = $waste_monthly_pie_chart_results['operation_food_waste'];
	$cardboardwaste = $waste_monthly_pie_chart_results['operation_cardboard_waste'];
	$plasticwaste = $waste_monthly_pie_chart_results['operation_plastic_waste'];
	$glasswaste = $waste_monthly_pie_chart_results['operation_glass_waste'];
	
	$totalwaste = $generalwaste+$paperwaste+$foodwaste+$cardboardwaste+$plasticwaste+$glasswaste;
	
	$generalwasteShare = round(($generalwaste*100)/$totalwaste,1);
	$paperwasteShare = round(($paperwaste*100)/$totalwaste,1);
	$foodwasteShare = round(($foodwaste*100)/$totalwaste,1);
	$cardboardwasteShare = round(($cardboardwaste*100)/$totalwaste,1);
	$plasticwasteShare = round(($plasticwaste*100)/$totalwaste,1);
	$glasswasteShare = round(($glasswaste*100)/$totalwaste,1);
	
	$totalShare = $generalwasteShare+$paperwasteShare+$foodwasteShare+$cardboardwasteShare+$plasticwasteShare+$glasswasteShare;
	
	$recycledwaste = $waste_monthly_pie_chart_results['operation_recycled_waste'];
	$landfill = $generalwaste + $foodwaste;
	$total = $recycledwaste + $landfill;
	$recycledwasteShare = round(($recycledwaste*100)/$total,1);
	$landfillShare = round(($landfill*100)/$total,1);
	$share = $recycledwasteShare + $landfillShare;
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
                                <td width="50%"><img height="350" src="<?php echo $wastePieMonthlyChartImg; ?>" /></td>
                                <td width="50%"><img height="350" src="<?php echo $wastePieLandfillMonthlyChartImg; ?>" /></td>
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