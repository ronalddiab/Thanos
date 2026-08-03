<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');



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





if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) { // If start and end year is same

    for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']["end_month"]; $i++) {

        $startmonthsarray_pre[] = $i;

    }

    $resultkeys_pre = array();

    $resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;

} else { // If start and end year is not same

    for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= 12; $i++) {

        $startmonthsarray_pre[] = $i;

    }



    for ($i = 1; $i <= $filters['filters_comparision_chart_pre']['end_month']; $i++) {

        $endmonthsarray_pre[] = $i;

    }

    $resultkeys_pre = array();

    $resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;

    $resultkeys_pre[$filters['filters_comparision_chart_pre']["end_year"]] = $endmonthsarray_pre;

}

$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];

?>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>

<script type="text/javascript">

    blockUI();

    google.load("visualization", "1", {

        packages: ["corechart"]

    });

    google.setOnLoadCallback(drawChart);

	

    function drawChart() {

        // Utility cost basr chart
        <?php if(!empty($utility_cost_chart_budget)){ 
			

			//For Labels

			$lableElectricity = ($totalElectricity != 0) ? lang("electricity") : '';

			$lableFuel = ($totalFuel != 0) ? lang("fuel") : '';

			$lableLpg = ($totalLpg != 0) ? lang("lpg") : '';

			$lableNaturalGas = ($totalNaturalGas != 0) ? lang("natural-gas") : '';

			$lableWater = ($totalWater != 0) ? lang("water") : '';

			$lableHeatingDistrict = ($totalHeatingDistrict != 0) ? lang("heating-district") : '';

			$lableCoolingDistrict = ($totalCoolingDistrict != 0) ? lang("cooling-district") : '';

			

			//For colors

			$colorElectricity = ($totalElectricity != 0) ? $chart_legend_colors['Electricity'] : '';

			$colorFuel = ($totalFuel != 0) ? $chart_legend_colors['Fuel'] : '';

			$colorLpg = ($totalLpg != 0) ? $chart_legend_colors['LPG'] : '';

			$colorNaturalGas = ($totalNaturalGas != 0) ? $chart_legend_colors['Natural_Gas'] : '';

			$colorWater = ($totalWater != 0) ? $chart_legend_colors['Water'] : '';

			$colorHeatingDistrict = ($totalHeatingDistrict != 0) ? $chart_legend_colors['District_Heating'] : '';

			$colorCoolingDistrict = ($totalCoolingDistrict != 0) ? $chart_legend_colors['District_Cooling'] : '';

			

		?>

			var arrTitle = ['Month'];

			var arrValuesMulti = [];

			<?php 

					if($totalElectricity != 0){ ?>

						arrTitle.push('<?php echo lang("electricity"); ?>');

			<?php	} ?>

			<?php if($totalFuel != 0){ ?>

						arrTitle.push('<?php echo lang("fuel"); ?>');

			<?php	} ?>

			<?php if($totalLpg != 0){ ?>

						arrTitle.push('<?php echo lang("lpg"); ?>');

			<?php	} ?>

			<?php if($totalNaturalGas != 0){ ?>

						arrTitle.push('<?php echo lang("natural-gas"); ?>');

			<?php	} ?>

			<?php if($totalWater != 0){ ?>

						arrTitle.push('<?php echo lang("water"); ?>');

			<?php	} ?>

			<?php if($totalHeatingDistrict != 0){ ?>

						arrTitle.push('<?php echo lang("heating-district"); ?>');

			<?php	} ?>

			<?php if($totalCoolingDistrict != 0){ ?>

						arrTitle.push('<?php echo lang("cooling-district"); ?>');

			<?php	} ?>

			

			arrTitle.push('<?php echo lang("occupancy")."-".$last_year; ?>');

			arrTitle.push('<?php echo lang("occupancy")."-".$current_year; ?>');

			arrTitle.push('<?php echo lang("budget"); ?>');

			arrValuesMulti.push(arrTitle);

		

		

          

                <?php 

                $total_months = 0;

                foreach ($resultkeys as $year => $value) {

                    foreach ($value as $key1 => $month) {

                        // Previous year data

                        $pre_monthdata = $montharray[$month] . ' ' . ($year-1);
                        $pre_data_electricity = (!empty($utility_cost_chart_budget[$month][$year-1]['electricity']))?$utility_cost_chart_budget[$month][$year-1]['electricity']:0;
                        $pre_data_fuel = (!empty($utility_cost_chart_budget[$month][$year-1]['fuel']))?$utility_cost_chart_budget[$month][$year-1]['fuel']:0;
                        $pre_data_lpg = (!empty($utility_cost_chart_budget[$month][$year-1]['lpg']))?$utility_cost_chart_budget[$month][$year-1]['lpg']:0;
                        $pre_data_natural_gas = (!empty($utility_cost_chart_budget[$month][$year-1]['natural_gas']))?$utility_cost_chart_budget[$month][$year-1]['natural_gas']:0;
                        $pre_data_heating_district = (!empty($utility_cost_chart_budget[$month][$year-1]['heating_district']))?$utility_cost_chart_budget[$month][$year-1]['heating_district']:0;
                        $pre_data_cooling_district = (!empty($utility_cost_chart_budget[$month][$year-1]['cooling_district']))?$utility_cost_chart_budget[$month][$year-1]['cooling_district']:0;
                        $pre_data_water = (!empty($utility_cost_chart_budget[$month][$year-1]['water']))?$utility_cost_chart_budget[$month][$year-1]['water']:0;
                        $pre_data_cdd = (!empty($utility_cost_chart_budget[$month][$year-1]['cdd']))?$utility_cost_chart_budget[$month][$year-1]['cdd']:0;
                        $pre_data_hdd = (!empty($utility_cost_chart_budget[$month][$year-1]['hdd']))?$utility_cost_chart_budget[$month][$year-1]['hdd']:0;
                        $pre_data_occupancy = (!empty($utility_cost_chart_budget[$month][$year-1]['occupancy']))?$utility_cost_chart_budget[$month][$year-1]['occupancy']:0;
                        $pre_data_budget = (!empty($utility_cost_chart_budget[$month][$year-1]['budget']))?$utility_cost_chart_budget[$month][$year-1]['budget']:0;
                        

                        // Current year data

                        $monthdata = $montharray[$month] . ' ' . $year;
                        $data_electricity = (!empty($utility_cost_chart_budget[$month][$year]['electricity']))?$utility_cost_chart_budget[$month][$year]['electricity']:0;
                        $data_fuel = (!empty($utility_cost_chart_budget[$month][$year]['fuel']))?$utility_cost_chart_budget[$month][$year]['fuel']:0;
                        $data_lpg = (!empty($utility_cost_chart_budget[$month][$year]['lpg']))?$utility_cost_chart_budget[$month][$year]['lpg']:0;
                        $data_natural_gas = (!empty($utility_cost_chart_budget[$month][$year]['natural_gas']))?$utility_cost_chart_budget[$month][$year]['natural_gas']:0;
                        $data_heating_district = (!empty($utility_cost_chart_budget[$month][$year]['heating_district']))?$utility_cost_chart_budget[$month][$year]['heating_district']:0;
                        $data_cooling_district = (!empty($utility_cost_chart_budget[$month][$year]['cooling_district']))?$utility_cost_chart_budget[$month][$year]['cooling_district']:0;
                        $data_water = (!empty($utility_cost_chart_budget[$month][$year]['water']))?$utility_cost_chart_budget[$month][$year]['water']:0;
                        $data_cdd = (!empty($utility_cost_chart_budget[$month][$year]['cdd']))?$utility_cost_chart_budget[$month][$year]['cdd']:0;
                        $data_hdd = (!empty($utility_cost_chart_budget[$month][$year]['hdd']))?$utility_cost_chart_budget[$month][$year]['hdd']:0;
                        $data_occupancy = (!empty($utility_cost_chart_budget[$month][$year]['occupancy']))?$utility_cost_chart_budget[$month][$year]['occupancy']:0;
                        $data_budget = (!empty($utility_cost_chart_budget[$month][$year]['budget']))?$utility_cost_chart_budget[$month][$year]['budget']:0;



                        // Round values

                        $pre_data_occupancy = round($pre_data_occupancy,2);

                        $data_occupancy = round($data_occupancy,2);



                        // Average Previous year data

                        $total_sum_pre_data_electricity += $pre_data_electricity;

                        $total_sum_pre_data_fuel += $pre_data_fuel;

                        $total_sum_pre_data_lpg += $pre_data_lpg;

                        $total_sum_pre_data_natural_gas += $pre_data_natural_gas;

                        $total_sum_pre_data_heating_district += $pre_data_heating_district;

                        $total_sum_pre_data_cooling_district += $pre_data_cooling_district;

                        $total_sum_pre_data_water += $pre_data_water;

                        $total_sum_pre_data_cdd += $pre_data_cdd;

                        $total_sum_pre_data_hdd += $pre_data_hdd;

                        $total_sum_pre_data_occupancy += $pre_data_occupancy;

                        $total_sum_pre_data_budget += $pre_data_budget;

                        

                        // Average Current year data

                        $total_sum_data_electricity += $data_electricity;

                        $total_sum_data_fuel += $data_fuel;

                        $total_sum_data_lpg += $data_lpg;

                        $total_sum_data_natural_gas += $data_natural_gas;

                        $total_sum_data_heating_district += $data_heating_district;

                        $total_sum_data_cooling_district += $data_cooling_district;

                        $total_sum_data_water += $data_water;

                        $total_sum_data_cdd += $data_cdd;

                        $total_sum_data_hdd += $data_hdd;

                        $total_sum_data_occupancy += $data_occupancy;

                        $total_sum_data_budget += $data_budget;



                        $total_months++;

                        ?>

						

						var arrValuesNull = [null];

						<?php if($totalElectricity != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalFuel != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalLpg != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalNaturalGas != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalWater != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalHeatingDistrict != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

						<?php if($totalCoolingDistrict != 0){ ?>

									arrValuesNull.push(null);

						<?php	} ?>

							arrValuesNull.push(null);

							arrValuesNull.push(null);

							arrValuesNull.push(null);

							

						var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];

						var arrValues = ['<?php echo $monthdata; ?>'];

						<?php if($totalElectricity != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_electricity; ?>);

						<?php	} ?>

						<?php if($totalFuel != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_fuel; ?>);

						<?php	} ?>

						<?php if($totalLpg != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_lpg; ?>);

						<?php	} ?>

						<?php if($totalNaturalGas != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_natural_gas; ?>);

						<?php	} ?>

						<?php if($totalWater != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_water; ?>);

						<?php	} ?>

						<?php if($totalHeatingDistrict != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_heating_district; ?>);

						<?php	} ?>

						<?php if($totalCoolingDistrict != 0){ ?>

									arrValuesPre.push(<?php echo $pre_data_cooling_district; ?>);

						<?php	} ?>

							arrValuesPre.push(<?php echo isset($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);

							arrValuesPre.push(null);

							arrValuesPre.push(null);

							

					

						<?php if($totalElectricity != 0){ ?>

								arrValues.push(<?php echo $data_electricity; ?>);

						<?php	} ?>

						<?php if($totalFuel != 0){ ?>

									arrValues.push(<?php echo $data_fuel; ?>);

						<?php	} ?>

						<?php if($totalLpg != 0){ ?>

									arrValues.push(<?php echo $data_lpg; ?>);

						<?php	} ?>

						<?php if($totalNaturalGas != 0){ ?>

									arrValues.push(<?php echo $data_natural_gas; ?>);

						<?php	} ?>

						<?php if($totalWater != 0){ ?>

									arrValues.push(<?php echo $data_water; ?>);

						<?php	} ?>

						<?php if($totalHeatingDistrict != 0){ ?>

									arrValues.push(<?php echo $data_heating_district; ?>);

						<?php	} ?>

						<?php if($totalCoolingDistrict != 0){ ?>

									arrValues.push(<?php echo $data_cooling_district; ?>);

						<?php	} ?>

				

							arrValues.push(null);

							arrValues.push(<?php echo isset($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);

							arrValues.push(<?php echo isset($data_budget) && is_finite($data_budget) ? $data_budget : 0; ?>);

							

							arrValuesMulti.push(arrValuesNull);

							arrValuesMulti.push(arrValuesPre);

							arrValuesMulti.push(arrValues);

						

						

                        /* ['<?php echo $pre_monthdata; ?>',<?php echo $pre_data_electricity; ?>,<?php echo $pre_data_fuel; ?>,<?php echo $pre_data_lpg; ?>,<?php echo $pre_data_natural_gas; ?>,<?php echo $pre_data_water; ?>,<?php echo $pre_data_heating_district; ?>,<?php echo $pre_data_cooling_district; ?>,<?php echo $pre_data_occupancy; ?>,null,null],                 

                        ['<?php echo $monthdata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,null,<?php echo $data_occupancy; ?>,<?php echo $data_budget; ?>],    */              

                        <?php

                    }

                }

                ?>

           



            <?php

            // Average Previous year data

            $AVG_pre_data_electricity = ($total_sum_pre_data_electricity/$total_months);

            $AVG_pre_data_fuel = ($total_sum_pre_data_fuel/$total_months);

            $AVG_pre_data_lpg = ($total_sum_pre_data_lpg/$total_months);

            $AVG_pre_data_natural_gas = ($total_sum_pre_data_natural_gas/$total_months);

            $AVG_pre_data_heating_district = ($total_sum_pre_data_heating_district/$total_months);

            $AVG_pre_data_cooling_district = ($total_sum_pre_data_cooling_district/$total_months);

            $AVG_pre_data_water = ($total_sum_pre_data_water/$total_months);

            $AVG_pre_data_cdd = ($total_sum_pre_data_cdd/$total_months);

            $AVG_pre_data_hdd = ($total_sum_pre_data_hdd/$total_months);

            $AVG_pre_data_occupancy = ($total_sum_pre_data_occupancy/$total_months);

            $AVG_pre_data_budget = ($total_sum_pre_data_budget/$total_months);

            

            // Average Current year data

            $YTD_total_months = $this->_ci->config->config['YTD_month_count'];            

            $AVG_data_electricity = ($total_sum_data_electricity/$YTD_total_months);

            $AVG_data_fuel = ($total_sum_data_fuel/$YTD_total_months);

            $AVG_data_lpg = ($total_sum_data_lpg/$YTD_total_months);

            $AVG_data_natural_gas = ($total_sum_data_natural_gas/$YTD_total_months);

            $AVG_data_heating_district = ($total_sum_data_heating_district/$YTD_total_months);

            $AVG_data_cooling_district = ($total_sum_data_cooling_district/$YTD_total_months);

            $AVG_data_water = ($total_sum_data_water/$YTD_total_months);

            $AVG_data_cdd = ($total_sum_data_cdd/$YTD_total_months);

            $AVG_data_hdd = ($total_sum_data_hdd/$YTD_total_months);

            $AVG_data_occupancy = ($total_sum_data_occupancy/$YTD_total_months);

            $AVG_data_budget = ($total_sum_data_budget/$total_months);



            // Check empty

            	$AVG_pre_data_electricity = (!empty($AVG_pre_data_electricity)) ? $AVG_pre_data_electricity : 0;

				$AVG_pre_data_fuel = (!empty($AVG_pre_data_fuel)) ? $AVG_pre_data_fuel : 0;

				$AVG_pre_data_lpg = (!empty($AVG_pre_data_lpg)) ? $AVG_pre_data_lpg : 0;

				$AVG_pre_data_natural_gas = (!empty($AVG_pre_data_natural_gas)) ? $AVG_pre_data_natural_gas : 0;

				$AVG_pre_data_heating_district = (!empty($AVG_pre_data_heating_district)) ? $AVG_pre_data_heating_district : 0;

				$AVG_pre_data_cooling_district = (!empty($AVG_pre_data_cooling_district)) ? $AVG_pre_data_cooling_district : 0;

				$AVG_pre_data_water = (!empty($AVG_pre_data_water)) ? $AVG_pre_data_water : 0;

				$AVG_pre_data_cdd = (!empty($AVG_pre_data_cdd)) ? $AVG_pre_data_cdd : 0;

				$AVG_pre_data_hdd = (!empty($AVG_pre_data_hdd)) ? $AVG_pre_data_hdd : 0;

				$AVG_pre_data_occupancy = (!empty($AVG_pre_data_occupancy)) ? $AVG_pre_data_occupancy : 0;

				$AVG_pre_data_budget = (!empty($AVG_pre_data_budget)) ? $AVG_pre_data_budget : 0;



	            $AVG_data_electricity = (!empty($AVG_data_electricity)) ? $AVG_data_electricity :0 ;

				$AVG_data_fuel = (!empty($AVG_data_fuel)) ? $AVG_data_fuel :0 ;

				$AVG_data_lpg = (!empty($AVG_data_lpg)) ? $AVG_data_lpg :0 ;

				$AVG_data_natural_gas = (!empty($AVG_data_natural_gas)) ? $AVG_data_natural_gas :0 ;

				$AVG_data_heating_district = (!empty($AVG_data_heating_district)) ? $AVG_data_heating_district :0 ;

				$AVG_data_cooling_district = (!empty($AVG_data_cooling_district)) ? $AVG_data_cooling_district :0 ;

				$AVG_data_water = (!empty($AVG_data_water)) ? $AVG_data_water :0 ;

				$AVG_data_cdd = (!empty($AVG_data_cdd)) ? $AVG_data_cdd :0 ;

				$AVG_data_hdd = (!empty($AVG_data_hdd)) ? $AVG_data_hdd :0 ;

				$AVG_data_occupancy = (!empty($AVG_data_occupancy)) ? $AVG_data_occupancy :0 ;

				$AVG_data_budget = (!empty($AVG_data_budget)) ? $AVG_data_budget :0 ;



            $AVG_pre_data_occupancy = round($AVG_pre_data_occupancy,2);

            $AVG_data_occupancy = round($AVG_data_occupancy,2);



            ?>

            /* data.addRow(['<?php echo ($year-1)." ".lang("average"); ?>',<?php echo $AVG_pre_data_electricity; ?>,<?php echo $AVG_pre_data_fuel; ?>,<?php echo $AVG_pre_data_lpg; ?>,<?php echo $AVG_pre_data_natural_gas; ?>,<?php echo $AVG_pre_data_water; ?>,<?php echo $AVG_pre_data_heating_district; ?>,<?php echo $AVG_pre_data_cooling_district; ?>,<?php echo $AVG_pre_data_occupancy; ?>,null,null]);

            data.addRow(['<?php echo $year." ".lang("average"); ?>',<?php echo $AVG_data_electricity; ?>,<?php echo $AVG_data_fuel; ?>,<?php echo $AVG_data_lpg; ?>,<?php echo $AVG_data_natural_gas; ?>,<?php echo $AVG_data_water; ?>,<?php echo $AVG_data_heating_district; ?>,<?php echo $AVG_data_cooling_district; ?>,null,<?php echo $AVG_data_occupancy; ?>,<?php echo $AVG_data_budget; ?>]); */

			

			var arrAvgNull = [null];

			<?php if($totalElectricity != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalFuel != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalLpg != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalNaturalGas != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalWater != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalHeatingDistrict != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

			<?php if($totalCoolingDistrict != 0){ ?>

						arrAvgNull.push(null);

			<?php	} ?>

				arrAvgNull.push(null);

				arrAvgNull.push(null);

				arrAvgNull.push(null);

				

			var arrAvgPre = ['<?php echo ($year-1)." ".lang("average"); ?>'];

			<?php if($totalElectricity != 0){ ?>

					arrAvgPre.push(<?php echo $AVG_pre_data_electricity; ?>);

			<?php	} ?>

			<?php if($totalFuel != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_fuel; ?>);

			<?php	} ?>

			<?php if($totalLpg != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_lpg; ?>);

			<?php	} ?>

			<?php if($totalNaturalGas != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_natural_gas; ?>);

			<?php	} ?>

			<?php if($totalWater != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_water; ?>);

			<?php	} ?>

			<?php if($totalHeatingDistrict != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_heating_district; ?>);

			<?php	} ?>

			<?php if($totalCoolingDistrict != 0){ ?>

						arrAvgPre.push(<?php echo $AVG_pre_data_cooling_district; ?>);

			<?php	} ?>

			

			arrAvgPre.push(<?php echo $AVG_pre_data_occupancy; ?>);

			arrAvgPre.push(null);

			arrAvgPre.push(null);

			

			var arrAvg = ['<?php echo ($year)." ".lang("average"); ?>'];

			<?php if($totalElectricity != 0){ ?>

					arrAvg.push(<?php echo $AVG_data_electricity; ?>);

			<?php	} ?>

			<?php if($totalFuel != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_fuel; ?>);

			<?php	} ?>

			<?php if($totalLpg != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_lpg; ?>);

			<?php	} ?>

			<?php if($totalNaturalGas != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_natural_gas; ?>);

			<?php	} ?>

			<?php if($totalWater != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_water; ?>);

			<?php	} ?>

			<?php if($totalHeatingDistrict != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_heating_district; ?>);

			<?php	} ?>

			<?php if($totalCoolingDistrict != 0){ ?>

						arrAvg.push(<?php echo $AVG_data_cooling_district; ?>);

			<?php	} ?>

			

			arrAvg.push(null);

			arrAvg.push(<?php echo $AVG_data_occupancy; ?>);

			arrAvg.push(<?php echo $AVG_data_budget; ?>);

			

			arrValuesMulti.push(arrAvgNull);

			arrValuesMulti.push(arrAvgPre);

			arrValuesMulti.push(arrAvg);

			

			

			var data = google.visualization.arrayToDataTable(arrValuesMulti);

            var options = {

                isStacked: true,
                title: '<?php echo lang("utility-cost-chart-budget-title"); ?>',
                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 28

                },

                hAxis: {title: '<?php echo lang("month"); ?>', titleTextStyle: {fontName: 'Arial'}, slantedText:true, slantedTextAngle:45},

                vAxes: {

                    0: { title:'<?php echo lang("utility-cost-chart-yaxis-0-title"); ?>',titleTextStyle: {fontName: 'Arial',}},

                    1: { title:'<?php echo lang("occupancy"); ?>',titleTextStyle: {fontName: 'Arial',fontSize: 18},'minValue': 100 ,ticks: [0,10,20,30,40,50,60,70,80,90,100] }

                },

                interpolateNulls: true,

                series: {

                    <?php $i = 0; 

						if($totalElectricity != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorElectricity; ?>' },

					<?php  $i += 1;	 }  ?>

					<?php if($totalFuel != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorFuel; ?>' },

					<?php	$i += 1; } ?>

					<?php if($totalLpg != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorLpg; ?>' },

					<?php	$i += 1; } ?>

					<?php if($totalNaturalGas != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorNaturalGas; ?>' },

					<?php	$i += 1; } ?>

					<?php if($totalWater != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorWater; ?>' },

					<?php	$i += 1; } ?>

					<?php if($totalHeatingDistrict != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorHeatingDistrict; ?>' },

					<?php	$i += 1; } ?>

					<?php if($totalCoolingDistrict != 0){ ?>

								<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $colorCoolingDistrict; ?>' },

					<?php	$i += 1; } ?>

                    <?php echo $i;$i+=1; ?>: { targetAxisIndex: 1, type: "line", pointShape:'square', pointSize:10},

                    <?php echo $i;$i+=1; ?>: { targetAxisIndex: 1, type: "line", pointShape:'square', pointSize:10},

                    <?php echo $i; ?>: { targetAxisIndex: 0, type: "line" ,pointShape:'triangle',pointSize:15},

                },

                legend: { position: 'top', maxLines: 3 }

            };

            var chart1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_budget'));			
			google.visualization.events.addListener(chart1, 'ready', function () {

		        setTimeout(function(){

		        	var imgUri = '';

					imgUri = chart1.getImageURI();

		        	document.getElementById('columnChartImg').value = imgUri;

		        },1000);				

		    });

			chart1.draw(data, options);

        <?php } ?>



        <?php if(!empty($utility_cost_chart_pre)){ ?>

            var data = google.visualization.arrayToDataTable([

                ['Month','<?php echo lang("electricity"); ?>', '<?php echo lang("fuel"); ?>','<?php echo lang("lpg"); ?>','<?php echo lang("natural-gas"); ?>','<?php echo lang("water"); ?>','<?php echo lang("heating-district"); ?>','<?php echo lang("cooling-district"); ?>','<?php echo lang("occupancy")."-".($last_year-1); ?>','<?php echo lang("occupancy")."-".($current_year-1); ?>'],

                <?php 

                $total_months = 0;

                foreach ($resultkeys_pre as $year => $value) {

                    foreach ($value as $key1 => $month) {

                        // Previous year data

                        $pre_monthdata = $montharray[$month] . ' ' . ($year-1);

                        $pre_data_electricity = (!empty($utility_cost_chart_pre[$month][$year-1]['electricity']))?$utility_cost_chart_pre[$month][$year-1]['electricity']:0;

                        $pre_data_fuel = (!empty($utility_cost_chart_pre[$month][$year-1]['fuel']))?$utility_cost_chart_pre[$month][$year-1]['fuel']:0;

                        $pre_data_lpg = (!empty($utility_cost_chart_pre[$month][$year-1]['lpg']))?$utility_cost_chart_pre[$month][$year-1]['lpg']:0;

                        $pre_data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year-1]['natural_gas']))?$utility_cost_chart_pre[$month][$year-1]['natural_gas']:0;

                        $pre_data_heating_district = (!empty($utility_cost_chart_pre[$month][$year-1]['heating_district']))?$utility_cost_chart_pre[$month][$year-1]['heating_district']:0;

                        $pre_data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year-1]['cooling_district']))?$utility_cost_chart_pre[$month][$year-1]['cooling_district']:0;

                        $pre_data_water = (!empty($utility_cost_chart_pre[$month][$year-1]['water']))?$utility_cost_chart_pre[$month][$year-1]['water']:0;

                        $pre_data_cdd = (!empty($utility_cost_chart_pre[$month][$year-1]['cdd']))?$utility_cost_chart_pre[$month][$year-1]['cdd']:0;

                        $pre_data_hdd = (!empty($utility_cost_chart_pre[$month][$year-1]['hdd']))?$utility_cost_chart_pre[$month][$year-1]['hdd']:0;

                        $pre_data_occupancy = (!empty($utility_cost_chart_pre[$month][$year-1]['occupancy']))?$utility_cost_chart_pre[$month][$year-1]['occupancy']:0;

                        

                        // Current year data

                        $monthdata = $montharray[$month] . ' ' . $year;

                        $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['electricity']))?$utility_cost_chart_pre[$month][$year]['electricity']:0;

                        $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel']))?$utility_cost_chart_pre[$month][$year]['fuel']:0;

                        $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg']))?$utility_cost_chart_pre[$month][$year]['lpg']:0;

                        $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas']))?$utility_cost_chart_pre[$month][$year]['natural_gas']:0;

                        $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district']))?$utility_cost_chart_pre[$month][$year]['heating_district']:0;

                        $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district']))?$utility_cost_chart_pre[$month][$year]['cooling_district']:0;

                        $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water']))?$utility_cost_chart_pre[$month][$year]['water']:0;

                        $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd']))?$utility_cost_chart_pre[$month][$year]['cdd']:0;

                        $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd']))?$utility_cost_chart_pre[$month][$year]['hdd']:0;

                        $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy']))?$utility_cost_chart_pre[$month][$year]['occupancy']:0;



                        // Round values

                        $pre_data_occupancy = round($pre_data_occupancy,2);

                        $data_occupancy = round($data_occupancy,2);



                        // Average Previous year data

                        $h_total_sum_pre_data_electricity += $pre_data_electricity;

                        $h_total_sum_pre_data_fuel += $pre_data_fuel;

                        $h_total_sum_pre_data_lpg += $pre_data_lpg;

                        $h_total_sum_pre_data_natural_gas += $pre_data_natural_gas;

                        $h_total_sum_pre_data_heating_district += $pre_data_heating_district;

                        $h_total_sum_pre_data_cooling_district += $pre_data_cooling_district;

                        $h_total_sum_pre_data_water += $pre_data_water;

                        $h_total_sum_pre_data_cdd += $pre_data_cdd;

                        $h_total_sum_pre_data_hdd += $pre_data_hdd;

                        $h_total_sum_pre_data_occupancy += $pre_data_occupancy;

                        

                        // Average Current year data

                        $h_total_sum_data_electricity += $data_electricity;

                        $h_total_sum_data_fuel += $data_fuel;

                        $h_total_sum_data_lpg += $data_lpg;

                        $h_total_sum_data_natural_gas += $data_natural_gas;

                        $h_total_sum_data_heating_district += $data_heating_district;

                        $h_total_sum_data_cooling_district += $data_cooling_district;

                        $h_total_sum_data_water += $data_water;

                        $h_total_sum_data_cdd += $data_cdd;

                        $h_total_sum_data_hdd += $data_hdd;

                        $h_total_sum_data_occupancy += $data_occupancy;



                        $total_months++;

                        ?>

                        ['<?php echo $pre_monthdata; ?>',<?php echo $pre_data_electricity; ?>,<?php echo $pre_data_fuel; ?>,<?php echo $pre_data_lpg; ?>,<?php echo $pre_data_natural_gas; ?>,<?php echo $pre_data_water; ?>,<?php echo $pre_data_heating_district; ?>,<?php echo $pre_data_cooling_district; ?>,<?php echo $pre_data_occupancy; ?>,null],                 

                        ['<?php echo $monthdata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,null,<?php echo $data_occupancy; ?>],                 

                        <?php

                    }

                }

                ?>

            ]);



            <?php

            // Average Previous year data

            $AVG_pre_data_electricity = ($h_total_sum_pre_data_electricity/$total_months);

            $AVG_pre_data_fuel = ($h_total_sum_pre_data_fuel/$total_months);

            $AVG_pre_data_lpg = ($h_total_sum_pre_data_lpg/$total_months);

            $AVG_pre_data_natural_gas = ($h_total_sum_pre_data_natural_gas/$total_months);

            $AVG_pre_data_heating_district = ($h_total_sum_pre_data_heating_district/$total_months);

            $AVG_pre_data_cooling_district = ($h_total_sum_pre_data_cooling_district/$total_months);

            $AVG_pre_data_water = ($h_total_sum_pre_data_water/$total_months);

            $AVG_pre_data_cdd = ($h_total_sum_pre_data_cdd/$total_months);

            $AVG_pre_data_hdd = ($h_total_sum_pre_data_hdd/$total_months);

            $AVG_pre_data_occupancy = ($h_total_sum_pre_data_occupancy/$total_months);

            

            // Average Current year data          

            $AVG_data_electricity = ($h_total_sum_data_electricity/$total_months);

            $AVG_data_fuel = ($h_total_sum_data_fuel/$total_months);

            $AVG_data_lpg = ($h_total_sum_data_lpg/$total_months);

            $AVG_data_natural_gas = ($h_total_sum_data_natural_gas/$total_months);

            $AVG_data_heating_district = ($h_total_sum_data_heating_district/$total_months);

            $AVG_data_cooling_district = ($h_total_sum_data_cooling_district/$total_months);

            $AVG_data_water = ($h_total_sum_data_water/$total_months);

            $AVG_data_cdd = ($h_total_sum_data_cdd/$total_months);

            $AVG_data_hdd = ($h_total_sum_data_hdd/$total_months);

            $AVG_data_occupancy = ($h_total_sum_data_occupancy/$total_months);



            $AVG_pre_data_occupancy = round($AVG_pre_data_occupancy,2);

            $AVG_data_occupancy = round($AVG_data_occupancy,2);

            

            $chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];            

            ?>

            data.addRow(['<?php echo ($year-1)." ".lang("average"); ?>',<?php echo $AVG_pre_data_electricity; ?>,<?php echo $AVG_pre_data_fuel; ?>,<?php echo $AVG_pre_data_lpg; ?>,<?php echo $AVG_pre_data_natural_gas; ?>,<?php echo $AVG_pre_data_water; ?>,<?php echo $AVG_pre_data_heating_district; ?>,<?php echo $AVG_pre_data_cooling_district; ?>,<?php echo $AVG_pre_data_occupancy; ?>,null]);

            data.addRow(['<?php echo $year." ".lang("average"); ?>',<?php echo $AVG_data_electricity; ?>,<?php echo $AVG_data_fuel; ?>,<?php echo $AVG_data_lpg; ?>,<?php echo $AVG_data_natural_gas; ?>,<?php echo $AVG_data_water; ?>,<?php echo $AVG_data_heating_district; ?>,<?php echo $AVG_data_cooling_district; ?>,null,<?php echo $AVG_data_occupancy; ?>]);

            var options = {

                height: 700,

                isStacked: true,

                title: '<?php echo lang("utility-cost-chart-title"); ?>',

                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 28

                },

                hAxis: {title: '<?php echo lang("month"); ?>', titleTextStyle: {fontName: 'Arial'}, slantedText:true, slantedTextAngle:45},

                vAxes: {

                    0: { title:'<?php echo lang("utility-cost-chart-yaxis-0-title"); ?>',titleTextStyle: {fontName: 'Arial',}},

                    1: { title:'<?php echo lang("occupancy"); ?>',titleTextStyle: {fontName: 'Arial',fontSize: 18},'minValue': 100 ,ticks: [0,10,20,30,40,50,60,70,80,90,100] }

                },

                interpolateNulls: true,

                series: {

                    0: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                    1: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                    2: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                    3: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                    4: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Water']; ?>' },

                    5: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                    6: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },

                    7: { targetAxisIndex: 1, type: "line" },

                    8: { targetAxisIndex: 1, type: "line" },

                },

                legend: { position: 'top', maxLines: 3 }

            };



            var chart1_1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_pre'));           

            google.visualization.events.addListener(chart1_1, 'ready', function () {

                setTimeout(function(){

                    var imgUri = chart1_1.getImageURI();

                    document.getElementById('columnChartImg_hidden').value = imgUri;

                },1000);                

            });

            chart1_1.draw(data, options);

        <?php } ?>



        // KWH Pie Chart for current year

        <?php if(!empty($kwh_pie_chart)){ ?>

            var data = google.visualization.arrayToDataTable([

              ['Energy', 'Usage'],

              <?php

				foreach($kwh_pie_chart as $key => $val)

				{

					if($val != 0)

					{

						echo '["'.lang($key).'",'.round($val,2).'],';

					}

				}

			?>

            ]);



            var options = {

                height:600,

                pieHole: 0.4,

                title: '<?php echo lang("kWh-pie-chart-title"); ?>',

                sliceVisibilityThreshold: .0,

                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

                },

                legend: { textStyle: { fontSize: 17   } },

                chartArea:{top:148,width:"100%"},

                slices: {

                  0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                  1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                  2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                  3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                  4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                  5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' }

                }

            };



            var chart = new google.visualization.PieChart(document.getElementById('kwh_pie_chart'));

			google.visualization.events.addListener(chart, 'ready', function () {

		        setTimeout(function(){

		        	var imgUri = chart.getImageURI();

		        	document.getElementById('pieChartImg').value = imgUri;

		        },1000);				

		    });

            chart.draw(data, options);

        <?php } ?>



        // Cost Pie Chart for current year

        <?php if(!empty($cost_pie_chart)){ ?>

        var data = google.visualization.arrayToDataTable([

            ['Energy', 'Usage'],

            <?php

				foreach($cost_pie_chart as $key => $val)

				{

					if($val != 0)

					{

						echo '["'.lang($key).'",'.$val.'],';

					}

				}

			?>

        ]);



        var options = {

            height:600,

            pieHole: 0.4,

            title: '<?php echo lang("cost-pie-chart-title"); ?>',

            sliceVisibilityThreshold: .0,

            titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

            },

            legend: { textStyle: { fontSize: 17   } },

            chartArea:{width:"100%"},

            slices: {                

                0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },

                6: { color: '<?php echo $chart_legend_colors['Water']; ?>' }

            }

        };



        var chart2 = new google.visualization.PieChart(document.getElementById('cost_pie_chart'));

		google.visualization.events.addListener(chart2, 'ready', function () {

	        setTimeout(function(){

	        	var imgUri = chart2.getImageURI();

	        	document.getElementById('pieChartNewImg').value = imgUri;

	        },1000);				

	    });

        chart2.draw(data, options);

        <?php } ?>



        <?php /*****************************************For pdf only*****************************************/ ?>

        <?php if(!empty($kwh_pie_chart_pre)){ ?>

            var data = google.visualization.arrayToDataTable([

              ['Energy', 'Usage'],

              ['<?php echo lang("electricity"); ?>',     <?php echo round($kwh_pie_chart_pre['electricity'],2); ?>],

              ['<?php echo lang("fuel"); ?>',     <?php echo round($kwh_pie_chart_pre['fuel'],2); ?>],

              ['<?php echo lang("lpg"); ?>',     <?php echo round($kwh_pie_chart_pre['lpg'],2); ?>],

              ['<?php echo lang("natural-gas"); ?>',     <?php echo round($kwh_pie_chart_pre['natural_gas'],2); ?>],

              ['<?php echo lang("heating-district"); ?>', <?php echo round($kwh_pie_chart_pre['heating_district'],2); ?>],

              ['<?php echo lang("cooling-district"); ?>', <?php echo round($kwh_pie_chart_pre['cooling_district'],2); ?>]

            ]);



            var options = {

                height:600,

                pieHole: 0.4,

                title: '<?php echo lang("kWh-pie-chart-last12month-title").' - '.$filters["report_year_pre"]; ?>',

                sliceVisibilityThreshold: .0,

                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

                },

                legend: { textStyle: { fontSize: 17   } },

                chartArea:{top:148,width:"100%"},

                slices: {

                  0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                  1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                  2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                  3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                  4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                  5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' }

                }

            };



            var chart_hidden1 = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_pre'));

            google.visualization.events.addListener(chart_hidden1, 'ready', function () {

                setTimeout(function(){

                    var imgUri = chart_hidden1.getImageURI();

                    document.getElementById('pieChartImg_hidden').value = imgUri;

                },1000);                

            });

            chart_hidden1.draw(data, options);

        <?php } ?>



        // Cost Pie Chart for current year

        <?php if(!empty($cost_pie_chart_pre)){ ?>

        var data = google.visualization.arrayToDataTable([

            ['Energy', 'Usage'],

            ['<?php echo lang("electricity"); ?>' , <?php echo $cost_pie_chart_pre['electricity']; ?>],

            ['<?php echo lang("fuel"); ?>' , <?php echo $cost_pie_chart_pre['fuel']; ?>],

            ['<?php echo lang("lpg"); ?>' , <?php echo $cost_pie_chart_pre['lpg']; ?>],

            ['<?php echo lang("natural-gas"); ?>' , <?php echo $cost_pie_chart_pre['natural_gas']; ?>],

            ['<?php echo lang("heating-district"); ?>' , <?php echo $cost_pie_chart_pre['heating_district']; ?>],

            ['<?php echo lang("cooling-district"); ?>' , <?php echo $cost_pie_chart_pre['cooling_district']; ?>],

            ['<?php echo lang("water"); ?>' , <?php echo $cost_pie_chart_pre['water']; ?>],

        ]);



        var options = {

            height:600,

            pieHole: 0.4,

            title: '<?php echo lang("cost-pie-chart-last12month-title").' - '.$filters["report_year_pre"]; ?>',

            sliceVisibilityThreshold: .0,

            titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

            },

            legend: { textStyle: { fontSize: 17   } },

            chartArea:{width:"100%"},

            slices: {                

                0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },

                6: { color: '<?php echo $chart_legend_colors['Water']; ?>' }

            }

        };



        var chart_hidden2 = new google.visualization.PieChart(document.getElementById('cost_pie_chart_pre'));

        google.visualization.events.addListener(chart_hidden2, 'ready', function () {

            setTimeout(function(){

                var imgUri = chart_hidden2.getImageURI();

                document.getElementById('pieChartNewImg_hidden').value = imgUri;

            },1000);                

        });

        chart_hidden2.draw(data, options);

        <?php } ?>

        <?php /*****************************************For pdf only*****************************************/ ?>



        // kWh Pie Chart for last 12 months

        <?php if(!empty($kwh_pie_chart_previousmonth)){ ?>

            var data = google.visualization.arrayToDataTable([

              ['Energy', 'Usage'],

              <?php

				foreach($kwh_pie_chart_previousmonth as $key => $val)

				{

					if($val != 0)

					{

						echo '["'.lang($key).'",'.round($val,2).'],';

					}

				}

			?>

            ]);



            var options = {

                height:600,

                pieHole: 0.4,

                title: '<?php echo lang("kWh-pie-chart-last12month-title").' - '.$fullmontharray[$filters["previous_month"]].' '.$filters["previous_year"]; ?>',

                sliceVisibilityThreshold: .0,

                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

                },

                legend: { textStyle: { fontSize: 17   } },

                chartArea:{top:148,width:"100%"},

                slices: {

                    0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                    1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                    2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                    3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                    4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                    5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' }                

                }

            };



            var chart3 = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_previousmonth'));

			google.visualization.events.addListener(chart3, 'ready', function () {

		        setTimeout(function(){

		        	var imgUri = chart3.getImageURI();

		        	document.getElementById('pieChartNew2Img').value = imgUri;

		        },1000);				

	    	});

            chart3.draw(data, options);

        <?php } ?>



        // Cost Pie Chart for last 12 month

        <?php if(!empty($cost_pie_chart_previousmonth)){ ?>

        var data = google.visualization.arrayToDataTable([

            ['Energy', 'Usage'],

            <?php

				foreach($cost_pie_chart_previousmonth as $key => $val)

				{

					if($val != 0)

					{

						echo '["'.lang($key).'",'.$val.'],';

					}

				}

			?>

        ]);



        var options = {

            height:600,

            pieHole: 0.4,

            title: '<?php echo lang("cost-pie-chart-last12month-title").' - '.$fullmontharray[$filters["previous_month"]].' '.$filters["previous_year"]; ?>',

            sliceVisibilityThreshold: .0,

            titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 24

            },

            legend: { textStyle: { fontSize: 17   } },

            chartArea:{width:"100%"},

            slices: {

                0: { color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                1: { color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                2: { color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                3: { color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                4: { color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                5: { color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },

                6: { color: '<?php echo $chart_legend_colors['Water']; ?>' }

            }

        };



        var chart4 = new google.visualization.PieChart(document.getElementById('cost_pie_chart_previousmonth'));

		google.visualization.events.addListener(chart4, 'ready', function () {

	        setTimeout(function(){

	        	var imgUri = chart4.getImageURI();

	        	document.getElementById('pieChartNew3Img').value = imgUri;

	        },1000);				

    	});

        chart4.draw(data, options);

        <?php } ?>





        /*==================Last 5 Year's chanrt*/

        <?php if(!empty($utility_cost_chart_5years)){ ?>

            var data = google.visualization.arrayToDataTable([

                ['Year','<?php echo lang("electricity"); ?>', '<?php echo lang("fuel"); ?>','<?php echo lang("lpg"); ?>','<?php echo lang("natural-gas"); ?>','<?php echo lang("water"); ?>','<?php echo lang("heating-district"); ?>','<?php echo lang("cooling-district"); ?>','<?php echo lang("occupancy"); ?>'],

                <?php 

                foreach ($utility_cost_chart_5years as $year => $value) {

                        $yeardata = $year;

                        $data_electricity = (!empty($utility_cost_chart_5years[$year]['electricity']))?$utility_cost_chart_5years[$year]['electricity']:0;

                        $data_fuel = (!empty($utility_cost_chart_5years[$year]['fuel']))?$utility_cost_chart_5years[$year]['fuel']:0;

                        $data_lpg = (!empty($utility_cost_chart_5years[$year]['lpg']))?$utility_cost_chart_5years[$year]['lpg']:0;

                        $data_natural_gas = (!empty($utility_cost_chart_5years[$year]['natural_gas']))?$utility_cost_chart_5years[$year]['natural_gas']:0;

                        $data_heating_district = (!empty($utility_cost_chart_5years[$year]['heating_district']))?$utility_cost_chart_5years[$year]['heating_district']:0;

                        $data_cooling_district = (!empty($utility_cost_chart_5years[$year]['cooling_district']))?$utility_cost_chart_5years[$year]['cooling_district']:0;

                        $data_water = (!empty($utility_cost_chart_5years[$year]['water']))?$utility_cost_chart_5years[$year]['water']:0;

                        $data_cdd = (!empty($utility_cost_chart_5years[$year]['cdd']))?$utility_cost_chart_5years[$year]['cdd']:0;

                        $data_hdd = (!empty($utility_cost_chart_5years[$year]['hdd']))?$utility_cost_chart_5years[$year]['hdd']:0;

                        $data_occupancy = (!empty($utility_cost_chart_5years[$year]['occupancy']))?$utility_cost_chart_5years[$year]['occupancy']:0;

                        ?>

                        ['<?php echo $yeardata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,<?php echo $data_occupancy; ?>],                 

                        <?php

                }

                ?>

            ]);



            var options = {

                height: 700,

                isStacked: true,

                title: '<?php echo lang("utility-cost-chart-title"); ?>',

                titleTextStyle: {

                  fontName: 'Arial',

                  fontSize: 28

                },

                hAxis: {title: '<?php echo lang("month"); ?>', titleTextStyle: {fontName: 'Arial'}, slantedText:true, slantedTextAngle:45},

                vAxes: {

                    0: { title:'<?php echo lang("utility-cost-chart-yaxis-0-title"); ?>',titleTextStyle: {fontName: 'Arial',}},

                    1: { title:'<?php echo lang("occupancy"); ?>',titleTextStyle: {fontName: 'Arial',fontSize: 18},'minValue': 100 ,ticks: [0,10,20,30,40,50,60,70,80,90,100] }

                },

                interpolateNulls: true,

                series: {

                    0: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Electricity']; ?>' },

                    1: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Fuel']; ?>' },

                    2: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['LPG']; ?>' },

                    3: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },

                    4: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Water']; ?>' },

                    5: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },

                    6: { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },

                    7: { targetAxisIndex: 1, type: "line" },

                    8: { targetAxisIndex: 1, type: "line" },

                },

                legend: { position: 'top', maxLines: 3 }

            };



            var chart1_1_1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_5years'));

            google.visualization.events.addListener(chart1_1_1, 'ready', function () {

                setTimeout(function(){

                    var imgUri1 = chart1_1_1.getImageURI();

                    document.getElementById('columnChartImg_5years_hidden').value = imgUri1;

                },1000);                

            });

            chart1_1_1.draw(data, options);

        <?php } ?>

        /*==================Last 5 Year's chanrt*/



        

        unblockUI();

    }



    $(window).resize(function() {

        drawChart();

    });

</script>
<div class="card-wrap">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div id="utility_cost_chart_budget" style="height:700px;">

                        <?php if(empty($utility_cost_chart_budget)){ ?>

                                    <div class="table-responsive">                  

                                        <table class="table table-striped" >

                                            <tr>

                                                <td><?php echo lang('no-records') ?></td>

                                            </tr>

                                        </table>

                                    </div>

                                <?php } ?>

                            </div>

                            <div id="utility_cost_chart_pre" style="height:0px;opacity:0;"></div>

                            <div id="utility_cost_chart_5years" style="height:0px;opacity:0;"></div>

                        </div>

                    </div>

                </div>

                <br/>

                <div class="col-sm-12">

                    <div class="panel panel-primary">

                        <div class="panel-body">

                            <div class="col-sm-6">

                                <div id="kwh_pie_chart">

                                    <?php if(empty($kwh_pie_chart)){ ?>

                                        <div class="table-responsive">                  

                                            <table class="table table-striped" >

                                                <tr>

                                                    <td><?php echo lang('no-records') ?></td>

                                                </tr>

                                            </table>

                                        </div>

                                    <?php } ?>

                                </div>

                                <div id="kwh_pie_chart_pre" style="height:0px;opacity:0;"></div>

                            </div>

                            <div class="col-sm-6">

                                <div id="cost_pie_chart">

                                    <?php if(empty($cost_pie_chart)){ ?>

                                        <div class="table-responsive">                  

                                            <table class="table table-striped" >

                                                <tr>

                                                    <td><?php echo lang('no-records') ?></td>

                                                </tr>

                                            </table>

                                        </div>

                                    <?php } ?>

                                </div>

                                <div id="cost_pie_chart_pre" style="height:0px;opacity:0;"></div>

                            </div>

                        </div>

                    </div>

                </div> 

                <br/>

                <div class="col-sm-12">

                    <div class="panel panel-primary">

                        <div class="panel-body">

                            <div class="col-sm-6">

                                <div id="kwh_pie_chart_previousmonth">

                                    <?php if(empty($kwh_pie_chart_previousmonth)){ ?>

                                        <div class="table-responsive">                  

                                            <table class="table table-striped" >

                                                <tr>

                                                    <td><?php echo lang('no-records') ?></td>

                                                </tr>

                                            </table>

                                        </div>

                                    <?php } ?>

                                </div>

                            </div>

                            <div class="col-sm-6">

                                <div id="cost_pie_chart_previousmonth">

                                    <?php if(empty($cost_pie_chart_previousmonth)){ ?>

                                        <div class="table-responsive">                  

                                            <table class="table table-striped" >

                                                <tr>

                                                    <td><?php echo lang('no-records') ?></td>

                                                </tr>

                                            </table>

                                        </div>

                                    <?php } ?>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

</article>

</div>

