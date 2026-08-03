<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$resultkeys = array();
$resultkeys[$filters['filters_comparision_chart']['start_year']] = array($filters['filters_comparision_chart']['start_month']);

?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" cellpadding="5" cellspacing="5" >
                <tr>
                    <td width="100%">
                        <img src="<?php echo $wasteMonthlyChartImg; ?>" />
                    </td>
                </tr>                
                <tr>
                    <td width="100%"><?php if(!empty($utility_west_chart)){ 
                            $ci = get_instance();
                            $total_months = 0;
                            foreach ($resultkeys as $year => $value) {                               
								foreach ($value as $key1 => $month) {
									$prewastemonthdata = $montharray[$month] . ' ' . ($year-1);
									$pre_waste_generalwaste = (!empty($utility_west_chart[$month][$year-1]['operation_general_waste']))?$utility_west_chart[$month][$year-1]['operation_general_waste']:0;
									$pre_waste_paperwaste = (!empty($utility_west_chart[$month][$year-1]['operation_paper_waste']))?$utility_west_chart[$month][$year-1]['operation_paper_waste']:0;
									$pre_waste_foodwaste = (!empty($utility_west_chart[$month][$year-1]['operation_food_waste']))?$utility_west_chart[$month][$year-1]['operation_food_waste']:0;
									$pre_waste_cardboardwaste = (!empty($utility_west_chart[$month][$year-1]['operation_cardboard_waste']))?$utility_west_chart[$month][$year-1]['operation_cardboard_waste']:0;
									$pre_waste_plasticwaste = (!empty($utility_west_chart[$month][$year-1]['operation_plastic_waste']))?$utility_west_chart[$month][$year-1]['operation_plastic_waste']:0;
									$pre_waste_glasswaste = (!empty($utility_west_chart[$month][$year-1]['operation_glass_waste']))?$utility_west_chart[$month][$year-1]['operation_glass_waste']:0;
									
									$wastemonthdata = $montharray[$month] . ' ' . $year;
									$waste_generalwaste = (!empty($utility_west_chart[$month][$year]['operation_general_waste']))?$utility_west_chart[$month][$year]['operation_general_waste']:0;
									$waste_paperwaste = (!empty($utility_west_chart[$month][$year]['operation_paper_waste']))?$utility_west_chart[$month][$year]['operation_paper_waste']:0;
									$waste_foodwaste = (!empty($utility_west_chart[$month][$year]['foodwaste']))?$utility_west_chart[$month][$year]['foodwaste']:0;
									$waste_cardboardwaste = (!empty($utility_west_chart[$month][$year]['operation_cardboard_waste']))?$utility_west_chart[$month][$year]['operation_cardboard_waste']:0;
									$waste_plasticwaste = (!empty($utility_west_chart[$month][$year]['operation_plastic_waste']))?$utility_west_chart[$month][$year]['operation_plastic_waste']:0;
									$waste_glasswaste = (!empty($utility_west_chart[$month][$year]['operation_glass_waste']))?$utility_west_chart[$month][$year]['operation_glass_waste']:0;
							
									$total_pre_generalwaste += $pre_waste_generalwaste;
									$total_pre_paperwaste += $pre_waste_paperwaste;
									$total_pre_foodwaste += $pre_waste_foodwaste;
									$total_pre_cardboardwaste += $pre_waste_cardboardwaste;
									$total_pre_plasticwaste += $pre_waste_plasticwaste;
									$total_pre_glasswaste += $pre_waste_glasswaste;
									
									$total_generalwaste += $waste_generalwaste;
									$total_paperwaste += $waste_paperwaste;
									$total_foodwaste += $waste_foodwaste;
									$total_cardboardwaste += $waste_cardboardwaste;
									$total_plasticwaste += $waste_plasticwaste;
									$total_glasswaste += $waste_glasswaste;
								}
                            }

                            

                            // Variation data
                            if(!empty($total_pre_generalwaste) && $total_pre_generalwaste>0){
                                $total_generalwaste_variation = round(((($total_generalwaste-$total_pre_generalwaste)*100)/$total_pre_generalwaste),2);
                            }else{
								$total_generalwaste_variation = 0;
                            }
							if(!empty($total_pre_paperwaste) && $total_pre_paperwaste>0){
                                $total_paperwaste_variation = round(((($total_paperwaste-$total_pre_paperwaste)*100)/$total_pre_paperwaste),2);
                            }else{
								$total_paperwaste_variation = 0;
                            }
							if(!empty($total_pre_foodwaste) && $total_pre_foodwaste>0){
                                $total_foodwaste_variation = round(((($total_foodwaste-$total_pre_foodwaste)*100)/$total_pre_foodwaste),2);
                            }else{
								$total_foodwaste_variation = 0;
                            }
							if(!empty($total_pre_cardboardwaste) && $total_pre_cardboardwaste>0){
                                $total_cardboard_variation = round(((($total_cardboardwaste-$total_pre_cardboardwaste)*100)/$total_pre_cardboardwaste),2);
                            }else{
								$total_cardboard_variation = 0;
                            }
							if(!empty($total_pre_plasticwaste) && $total_pre_plasticwaste>0){
                                $total_plastic_variation = round(((($total_plasticwaste-$total_pre_plasticwaste)*100)/$total_pre_plasticwaste),2);
                            }else{
								$total_plastic_variation = 0;
                            }
							if(!empty($total_pre_glasswaste) && $total_pre_glasswaste>0){
                                $total_glass_variation = round(((($total_glasswaste-$total_pre_glasswaste)*100)/$total_pre_glasswaste),2);
                            }else{
								$total_glass_variation = 0;
                            }
							
							
							
							
							$totalPreWaste = $total_pre_glasswaste+$total_pre_plasticwaste+$total_pre_cardboardwaste+$total_pre_foodwaste+$total_pre_paperwaste+$total_pre_generalwaste;
							
							$totalWaste = $total_glasswaste+$total_plasticwaste+$total_cardboardwaste+$total_foodwaste+$total_paperwaste+$total_generalwaste;
							
							$totalVariationData =  ($totalWaste - $totalPreWaste)/$totalPreWaste;
							$totalvariation = round($totalVariationData * 100,2);
                            ?>
                            <table border="1" width="100%" cellpadding="5" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong><?php echo $montharray[$filters['filters_comparision_chart']['start_month']]; ?> - <?php echo $year-1; ?></strong></th>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong><?php echo $montharray[$filters['filters_comparision_chart']['start_month']]; ?> - <?php echo $year; ?></strong></th>
                                        <th width="16%" align="center" style="background-color:#d8e1f2;"><strong>Variation</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="70%"><strong>Waste</strong></th>
                                                        <th width="30%"><strong>kgs</strong></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="70%"><strong>Waste</strong></th>
                                                        <th width="30%"><strong>kgs</strong></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </td>
                                        <td width="16%" align="center">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th><strong>(%)</strong></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">General Waste</td>
                                                        <td width="30%"><?php echo number_format($total_pre_generalwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($total_pre_paperwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($total_pre_foodwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($total_pre_cardboardwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($total_pre_plasticwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($total_pre_glasswaste); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">General Waste</td>
                                                        <td width="30%"><?php echo number_format($total_generalwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($total_paperwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($total_foodwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($total_cardboardwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($total_plasticwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($total_glasswaste); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td width="16%" align="center">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $total_generalwaste_variation; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $total_paperwaste_variation; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $total_foodwaste_variation; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $total_cardboard_variation; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $total_plastic_variation; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $total_glass_variation; ?>%</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="70%"><strong>Total</strong></td>
                                                    <td width="30%"><strong><?php echo number_format($totalPreWaste); ?></strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="70%"></td>
                                                    <td width="30%"><strong><?php echo number_format($totalWaste); ?></strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="16%" align="center">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $totalvariation; ?>%</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <?php
                        } ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>