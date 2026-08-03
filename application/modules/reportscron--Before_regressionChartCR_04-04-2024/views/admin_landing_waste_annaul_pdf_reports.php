<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year-1;

if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) { // If start and end year is same
    for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']['end_month']; $i++) {
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
?>
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" cellpadding="5" cellspacing="5">
                <tr>
                    <td width="100%">
                        <img src="<?php echo $wasteChartPreImg_hidden; ?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100%"><?php if(!empty($utility_cost_chart_pre)){ 
                            $ci = get_instance();
                            $total_months = 0;
                            foreach ($resultkeys as $year => $value) {
                                foreach ($value as $key1 => $month) {
                                    // Previous year data
                                    $pre_monthdata = $montharray[$month] . ' ' . ($year-1);
									$pre_data_generalwaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_general_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_general_waste']:0;
									$pre_data_paperwaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_paper_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_paper_waste']:0;
									$pre_data_foodwaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_food_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_food_waste']:0;
									$pre_data_cardboardwaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_cardboard_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_cardboard_waste']:0;
									$pre_data_plasticwaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_plastic_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_plastic_waste']:0;
									$pre_data_glasswaste = (!empty($utility_waste_chart_pre[$month][$year-1]['operation_glass_waste']))?$utility_waste_chart_pre[$month][$year-1]['operation_glass_waste']:0;						
									$pre_data_occupancy = (!empty($utility_waste_chart_pre[$month][$year-1]['occupancy']))?$utility_waste_chart_pre[$month][$year-1]['occupancy']:0;
									
									// Current year data
									$monthdata = $montharray[$month] . ' ' . $year;
									$data_generalwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_general_waste']))?$utility_waste_chart_pre[$month][$year]['operation_general_waste']:0;
									$data_paperwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_paper_waste']))?$utility_waste_chart_pre[$month][$year]['operation_paper_waste']:0;
									$data_foodwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_food_waste']))?$utility_waste_chart_pre[$month][$year]['operation_food_waste']:0;
									$data_cardboardwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_cardboard_waste']))?$utility_waste_chart_pre[$month][$year]['operation_cardboard_waste']:0;
									$data_plasticwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_plastic_waste']))?$utility_waste_chart_pre[$month][$year]['operation_plastic_waste']:0;
									$data_glasswaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_glass_waste']))?$utility_waste_chart_pre[$month][$year]['operation_glass_waste']:0;
									$data_occupancy = (!empty($utility_waste_chart_pre[$month][$year]['occupancy']))?$utility_waste_chart_pre[$month][$year]['occupancy']:0;

									// Round values
									$pre_data_occupancy = round($pre_data_occupancy,2);
									$data_occupancy = round($data_occupancy,2);

									// total Previous year data
									$av_total_sum_pre_data_generalwaste += $pre_data_generalwaste;
									$av_total_sum_pre_data_paperwaste += $pre_data_paperwaste;
									$av_total_sum_pre_data_foodwaste += $pre_data_foodwaste;
									$av_total_sum_pre_data_cardboardwaste += $pre_data_cardboardwaste;
									$av_total_sum_pre_data_plasticwaste += $pre_data_plasticwaste;
									$av_total_sum_pre_data_glasswaste += $pre_data_glasswaste;
									$av_total_sum_pre_data_occupancy += $pre_data_occupancy;
									
									// total Current year data
									$av_total_sum_data_generalwaste += $data_generalwaste;
									$av_total_sum_data_paperwaste += $data_paperwaste;
									$av_total_sum_data_foodwaste += $data_foodwaste;
									$av_total_sum_data_cardboardwaste += $data_cardboardwaste;
									$av_total_sum_data_plasticwaste += $data_plasticwaste;
									$av_total_sum_data_glasswaste += $data_glasswaste;
									$av_total_sum_data_occupancy += $data_occupancy;

									$total_months++;
                                }
                            }

							if($av_total_sum_pre_data_generalwaste > 0)
							{
								$variation = ($av_total_sum_data_generalwaste - $av_total_sum_pre_data_generalwaste)/$av_total_sum_pre_data_generalwaste;
								$variation_general_waste = round($variation*100,2);
							}
							else
							{
								$variation_general_waste = 0;
							}
							if($av_total_sum_pre_data_paperwaste > 0)
							{
								$variation = ($av_total_sum_data_paperwaste - $av_total_sum_pre_data_paperwaste)/$av_total_sum_pre_data_paperwaste;
								$variation_paper_waste = round($variation*100,2);
							}
							else
							{
								$variation_paper_waste = 0;
							}
							if($av_total_sum_pre_data_foodwaste > 0)
							{
								$variation = ($av_total_sum_data_foodwaste - $av_total_sum_pre_data_foodwaste)/$av_total_sum_pre_data_foodwaste;
								$variation_food_waste = round($variation*100,2);
							}
							else
							{
								$variation_food_waste = 0;
							}
							if($av_total_sum_pre_data_cardboardwaste > 0)
							{
								$variation = ($av_total_sum_data_cardboardwaste - $av_total_sum_pre_data_cardboardwaste)/$av_total_sum_pre_data_cardboardwaste;
								$variation_cardboard_waste = round($variation*100,2);
							}
							else
							{
								$variation_cardboard_waste = 0;
							}

							if($av_total_sum_pre_data_plasticwaste > 0)
							{
								$variation = ($av_total_sum_data_plasticwaste - $av_total_sum_pre_data_plasticwaste)/$av_total_sum_pre_data_plasticwaste;
								$variation_plastic_waste = round($variation*100,2);
							}
							else
							{
								$variation_plastic_waste = 0;
							}

							if($av_total_sum_pre_data_glasswaste > 0)
							{
								$variation = ($av_total_sum_data_glasswaste - $av_total_sum_pre_data_glasswaste)/$av_total_sum_pre_data_glasswaste;
								$variation_glass_waste = round($variation*100,2);
							}
							else
							{
								$variation_glass_waste = 0;
							}

							
							$totalPreviousWaste= $av_total_sum_pre_data_generalwaste + $av_total_sum_pre_data_paperwaste + $av_total_sum_pre_data_foodwaste + $av_total_sum_pre_data_cardboardwaste +$av_total_sum_pre_data_plasticwaste +$av_total_sum_pre_data_glasswaste ;
							
							$totalCurrentWaste = $av_total_sum_data_generalwaste + $av_total_sum_data_paperwaste +$av_total_sum_data_foodwaste + $av_total_sum_data_cardboardwaste +$av_total_sum_data_plasticwaste +$av_total_sum_data_glasswaste  ;
							
							$totalVariationData =  ($totalCurrentWaste - $totalPreviousWaste)/$totalPreviousWaste;
							$totalVariation = round($totalVariationData * 100,2);
                            ?><table border="1" width="100%" cellpadding="5" cellspacing="0">
                                <thead><tr>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong>Previous Year <?php echo $year-1; ?></strong></th>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong>Current Year <?php echo $year; ?></strong></th>
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
                                                        <td width="30%"><?php echo number_format($av_total_sum_pre_data_generalwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($av_total_sum_pre_data_paperwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($av_total_sum_pre_data_foodwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($av_total_sum_pre_data_cardboardwaste); ?></td>
                                                    </tr>
													<tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($av_total_sum_pre_data_plasticwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($av_total_sum_pre_data_glasswaste); ?></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">General Waste</td>
                                                        <td width="30%"><?php echo number_format($av_total_sum_data_generalwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($av_total_sum_data_paperwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($av_total_sum_data_foodwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($av_total_sum_data_cardboardwaste); ?></td>
                                                    </tr>
													<tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($av_total_sum_data_plasticwaste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($av_total_sum_data_glasswaste); ?></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </td>
                                        <td align="center" width="16%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo $variation_general_waste; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $variation_paper_waste; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $variation_food_waste; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $variation_cardboard_waste; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $variation_plastic_waste; ?>%</td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo $variation_glass_waste; ?>%</td>
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
                                                    <td width="30%"><strong><?php echo number_format($totalPreviousWaste); ?></strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td width="70%"></td>
                                                    <td width="30%"><strong><?php echo number_format($totalCurrentWaste); ?></strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="16%" align="center">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <strong><?php echo $totalVariation; ?>%</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table><?php } ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>