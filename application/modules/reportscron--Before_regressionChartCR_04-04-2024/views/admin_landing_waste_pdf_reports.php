<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

//Bar chart show last year data
$current_year = date('Y');
// $last_year = $current_year-1;
$last_year = $current_year-2; // remove this in 2022

if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) { // If start and end year is same
    for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $CURRENT_YEAR_MAX_MONTH_ID; $i++) {
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
<html style="text-align: left;">
    <body width="100%">
        <div style="border:2px solid  #f69546;padding:10px;">
            <table width="100%" cellpadding="5" cellspacing="5" >
                <tr>
                    <td width="100%">
                        <img src="<?php echo $wasteChartImg; ?>" />
                    </td>
                </tr>                
                <tr>
                    <td width="100%"><?php if(!empty($utility_west_chart)){ 					
                            $ci = get_instance();
                            $total_months = 0;
                             foreach ($resultkeys as $year => $value) {
                                foreach ($value as $key1 => $month) {

                                        // $prevYear = $year - 1;
                                        $prevYear = $year - 2; // remove this in 2022
								        $pre_monthdata = $montharray[$month] . ' ' . ($prevYear);
										$pre_data_operation_general_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_general_waste']))?$utility_west_chart[$month][$prevYear]['operation_general_waste']:0;
										$pre_data_operation_paper_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_paper_waste']))?$utility_west_chart[$month][$prevYear]['operation_paper_waste']:0;
										
										$pre_data_operation_food_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_food_waste']))?$utility_west_chart[$month][$prevYear]['operation_food_waste']:0;
										$pre_data_operation_cardboard_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_cardboard_waste']))?$utility_west_chart[$month][$prevYear]['operation_cardboard_waste']:0;
										$pre_data_operation_plastic_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_plastic_waste']))?$utility_west_chart[$month][$prevYear]['operation_plastic_waste']:0;
										$pre_data_operation_glass_waste = (!empty($utility_west_chart[$month][$prevYear]['operation_glass_waste']))?$utility_west_chart[$month][$prevYear]['operation_glass_waste']:0;
										$pre_data_occupancy = (!empty($utility_west_chart[$month][$prevYear]['occupancy']))?$utility_west_chart[$month][$prevYear]['occupancy']:0;
										
									
                                    
										 $monthdata = $montharray[$month] . ' ' . $year;
										 $data_operation_general_waste = (!empty($utility_west_chart[$month][$year]['operation_general_waste']))?$utility_west_chart[$month][$year]['operation_general_waste']:0;
										 $data_operation_paper_waste = (!empty($utility_west_chart[$month][$year]['operation_paper_waste']))?$utility_west_chart[$month][$year]['operation_paper_waste']:0;
										 $data_operation_food_waste = (!empty($utility_west_chart[$month][$year]['operation_food_waste']))?$utility_west_chart[$month][$year]['operation_food_waste']:0;
										 $data_operation_cardboard_waste = (!empty($utility_west_chart[$month][$year]['operation_cardboard_waste']))?$utility_west_chart[$month][$year]['operation_cardboard_waste']:0;
										 $data_operation_plastic_waste = (!empty($utility_west_chart[$month][$year]['operation_plastic_waste']))?$utility_west_chart[$month][$year]['operation_plastic_waste']:0;
										 $data_operation_glass_waste = (!empty($utility_west_chart[$month][$year]['operation_glass_waste']))?$utility_west_chart[$month][$year]['operation_glass_waste']:0;
										 $data_occupancy = (!empty($utility_west_chart[$month][$year]['occupancy']))?$utility_west_chart[$month][$year]['occupancy']:0;
										 
                                    // Total sum Previous year data
                                    $total_sum_pre_data_operation_general_waste += $pre_data_operation_general_waste;
                                    $total_sum_pre_data_operation_paper_waste += $pre_data_operation_paper_waste;
                                    $total_sum_pre_data_operation_food_waste += $pre_data_operation_food_waste;
                                    $total_sum_pre_data_operation_cardboard_waste += $pre_data_operation_cardboard_waste;
                                    $total_sum_pre_data_operation_plastic_waste += $pre_data_operation_plastic_waste;
                                    $total_sum_pre_data_operation_glass_waste += $pre_data_operation_glass_waste;
									$total_sum_pre_data_occupancy += $pre_data_occupancy;
                                  
                                    
                                    // Total sum Current year data
                                    $total_sum_data_operation_general_waste += $data_operation_general_waste;
                                    $total_sum_data_operation_paper_waste += $data_operation_paper_waste;
                                    $total_sum_data_operation_food_waste += $data_operation_food_waste;
                                    $total_sum_data_operation_cardboard_waste += $data_operation_cardboard_waste;
                                    $total_sum_data_operation_plastic_waste += $data_operation_plastic_waste;
                                    $total_sum_data_operation_glass_waste += $data_operation_glass_waste;
                                    $total_sum_data_occupancy += $data_occupancy;
                                   
									//varients
									if($total_sum_pre_data_operation_general_waste > 0)
									{
										$variation = ($total_sum_data_operation_general_waste - $total_sum_pre_data_operation_general_waste)/$total_sum_pre_data_operation_general_waste;
										$variation_general_waste = round($variation*100,2);
									}
									else
									{
										$variation_general_waste = 0;
									}
                                    if($total_sum_pre_data_operation_paper_waste > 0)
									{
										$variation = ($total_sum_data_operation_paper_waste - $total_sum_pre_data_operation_paper_waste)/$total_sum_pre_data_operation_paper_waste;
										$variation_paper_waste = round($variation*100,2);
									}
									else
									{
										$variation_paper_waste = 0;
									}
                                    if($total_sum_pre_data_operation_food_waste > 0)
									{
										$variation = ($total_sum_data_operation_food_waste - $total_sum_pre_data_operation_food_waste)/$total_sum_pre_data_operation_food_waste;
										$variation_food_waste = round($variation*100,2);
									}
									else
									{
										$variation_food_waste = 0;
									}
                                    if( $total_sum_pre_data_operation_cardboard_waste > 0)
									{
										$variation = ($total_sum_data_operation_cardboard_waste - $total_sum_pre_data_operation_cardboard_waste)/$total_sum_pre_data_operation_cardboard_waste;
										$variation_cardboard_waste = round($variation*100,2);
									}
									else
									{
										$variation_cardboard_waste = 0;
									}
                                    if($total_sum_pre_data_operation_plastic_waste > 0)
									{
										$variation = ($total_sum_data_operation_plastic_waste - $total_sum_pre_data_operation_plastic_waste)/$total_sum_pre_data_operation_plastic_waste;
										$variation_plastic_waste = round($variation*100,2);
									}
									else
									{
										$variation_plastic_waste = 0;
									}
                                    if($total_sum_pre_data_operation_glass_waste > 0)
									{
										$variation = ($total_sum_data_operation_glass_waste - $total_sum_pre_data_operation_glass_waste)/$total_sum_pre_data_operation_glass_waste;
										$variation_glass_waste = round($variation*100,2);
									}
									else
									{
										$variation_glass_waste = 0;
									}
                                    
                                }
                            }

							$totalCurrentWaste = $total_sum_data_operation_general_waste + $total_sum_data_operation_paper_waste + $total_sum_data_operation_food_waste + $total_sum_data_operation_cardboard_waste +$total_sum_data_operation_plastic_waste +$total_sum_data_operation_glass_waste ;
							
							$totalPreviousWaste = $total_sum_pre_data_operation_general_waste + $total_sum_pre_data_operation_paper_waste +$total_sum_pre_data_operation_food_waste + $total_sum_pre_data_operation_cardboard_waste +$total_sum_pre_data_operation_plastic_waste +$total_sum_pre_data_operation_glass_waste  ;
							
							$totalVariationData =  ($totalCurrentWaste - $totalPreviousWaste)/$totalPreviousWaste;
							$totalVariation = round($totalVariationData * 100,2);
                            ?>
                            <table border="1" width="100%" cellpadding="5" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong>YTD - Previous Year <?php echo $last_year; ?></strong></th>
                                        <th width="42%" align="center" style="background-color:#d8e1f2;"><strong>YTD - Current Year <?php echo $year; ?></strong></th>
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
                                                        <td width="30%"><?php echo number_format($total_sum_pre_data_operation_general_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($total_sum_pre_data_operation_paper_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($total_sum_pre_data_operation_food_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($total_sum_pre_data_operation_cardboard_waste); ?></td>
                                                    </tr>
													<tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($total_sum_pre_data_operation_plastic_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($total_sum_pre_data_operation_glass_waste); ?></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </td>
                                        <td width="42%">
                                            <table width="100%" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">General Waste</td>
                                                        <td width="30%"><?php echo number_format($total_sum_data_operation_general_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Paper Waste</td>
                                                        <td><?php echo number_format($total_sum_data_operation_paper_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Food Waste</td>
                                                        <td><?php echo number_format($total_sum_data_operation_food_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cardboard Waste</td>
                                                        <td><?php echo number_format($total_sum_data_operation_cardboard_waste); ?></td>
                                                    </tr>
													<tr>
                                                        <td>Plastic Waste</td>
                                                        <td><?php echo number_format($total_sum_data_operation_plastic_waste); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Glass Waste</td>
                                                        <td><?php echo number_format($total_sum_data_operation_glass_waste); ?></td>
                                                    </tr>
                                                    
                                                </tbody>
                                            </table>
                                        </td>
										<td width="16%" align="center">
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
                                                        <strong><?php echo round($totalVariation); ?>%</strong>
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