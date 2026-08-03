<?php


$this->_ci = get_instance();



if (!defined('BASEPATH'))

	exit('No direct script access allowed');

$id = $site_detail['id'];

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');



//Bar chart show last year data

$current_year = date('Y');

$last_year = $current_year - 1;

if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) {

	// If start and end year is same

	for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $filters['filters_comparision_chart']["end_month"]; $i++) {

		$startmonthsarray[] = $i;
	}



	$resultkeys = array();

	$resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;
} else {

	// If start and end year is not same

	for ($i = $filters['filters_comparision_chart']['start_month']; $i <= 12; $i++) {

		$startmonthsarray[] = $i;
	}



	for ($i = 1; $i <= $filters['filters_comparision_chart']['end_month']; $i++) {

		$endmonthsarray[] = $i;
	}

	$resultkeys = array();

	$resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;

	$resultkeys[$filters['filters_comparision_chart']["end_year"]] = $endmonthsarray; // uncomment this in 2022

}



if ($filters['filters_comparision_chart_pre']["start_year"] == $filters['filters_comparision_chart_pre']["end_year"]) {

	// If start and end year is same

	for ($i = $filters['filters_comparision_chart_pre']['start_month']; $i <= $filters['filters_comparision_chart_pre']["end_month"]; $i++) {

		$startmonthsarray_pre[] = $i;
	}

	$resultkeys_pre = array();

	$resultkeys_pre[$filters['filters_comparision_chart_pre']["start_year"]] = $startmonthsarray_pre;
} else {

	// If start and end year is not same

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

?>

<?php

$currentYear = date('Y'); //date('Y');

$currentMonth = intval(date('m'));

if ($currentMonth == 1) {

	$currentYear = $currentYear - 1;

	$currentMonth = 12;
}

$filters_pre['currentYear'] = $currentYear;

$filters_pre['currentMonth'] = $currentMonth;

$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];



$colorElectricity = $chart_legend_colors['Electricity'];

$colorFuel = $chart_legend_colors['Fuel'];

$colorLpg = $chart_legend_colors['LPG'];

$colorNaturalGas = $chart_legend_colors['Natural_Gas'];

$colorWater = $chart_legend_colors['Water'];

$colorHeatingDistrict = $chart_legend_colors['District_Heating'];

$colorCoolingDistrict = $chart_legend_colors['District_Cooling'];

?>

<script type="text/javascript">
	google.load("visualization", "1", {

		packages: ["corechart"]

	});

	google.setOnLoadCallback(drawChart);

	function drawChart() {





		<?php

		if (!empty($utility_west_chart)) {

			?>





			var arrTitle = ['Month'];

			var arrValuesMulti = [];

			<?php if ($totalGeneralWaste != 0) { ?>

				arrTitle.push('<?php echo lang("generalwaste"); ?>');

			<?php } ?>

			<?php if ($totalPaperWaste != 0) { ?>

				arrTitle.push('<?php echo lang("paperwaste"); ?>');

			<?php } ?>

			<?php if ($totalFoodWaste != 0) { ?>

				arrTitle.push('<?php echo lang("foodwaste"); ?>');

			<?php } ?>

			<?php if ($totalCardboardWaste != 0) { ?>

				arrTitle.push('<?php echo lang("cardboardwaste"); ?>');

			<?php } ?>

			<?php if ($totalPlasticWaste != 0) { ?>

				arrTitle.push('<?php echo lang("plasticwaste"); ?>');

			<?php } ?>

			<?php if ($totalGlassWaste != 0) { ?>

				arrTitle.push('<?php echo lang("glasswaste"); ?>');

			<?php } ?>

			arrTitle.push('<?php echo lang("occupancy") . "-" . $last_year; ?>');

			arrTitle.push('<?php echo lang("occupancy") . "-" . $current_year; ?>');

			arrValuesMulti.push(arrTitle);

			<?php

				$total_months = 0;

				$total_sum_pre_data_general = 0;

				$total_sum_pre_data_paper = 0;

				$total_sum_pre_data_food = 0;

				$total_sum_pre_data_cardboard = 0;

				$total_sum_pre_data_plastic = 0;

				$total_sum_pre_data_glass = 0;

				$total_sum_pre_data_occupancy = 0;

				$total_sum_data_general = 0;

				$total_sum_data_paper = 0;

				$total_sum_data_food = 0;

				$total_sum_data_cardboard = 0;

				$total_sum_data_plastic = 0;

				$total_sum_data_glass = 0;

				$total_sum_data_occupancy = 0;

				foreach ($resultkeys as $year => $value) {

					foreach ($value as $key1 => $month) {

						// Previous year data

						$prevYear = $year - 1;

						$pre_monthdata = $montharray[$month] . ' ' . ($prevYear);

						$pre_data_general = (!empty($utility_west_chart[$month][$prevYear]['operation_general_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_general_waste'] : 0;

						$pre_data_paper = (!empty($utility_west_chart[$month][$prevYear]['operation_paper_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_paper_waste'] : 0;

						$pre_data_food = (!empty($utility_west_chart[$month][$prevYear]['operation_food_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_food_waste'] : 0;

						$pre_data_cardboard = (!empty($utility_west_chart[$month][$prevYear]['operation_cardboard_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_cardboard_waste'] : 0;

						$pre_data_plastic = (!empty($utility_west_chart[$month][$prevYear]['operation_plastic_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_plastic_waste'] : 0;

						$pre_data_glass = (!empty($utility_west_chart[$month][$prevYear]['operation_glass_waste'])) ? $utility_west_chart[$month][$prevYear]['operation_glass_waste'] : 0;

						$pre_data_occupancy = (!empty($utility_west_chart[$month][$prevYear]['occupancy'])) ? $utility_west_chart[$month][$prevYear]['occupancy'] : 0;



						// Current year data

						$monthdata = $montharray[$month] . ' ' . $year;

						$data_general = (!empty($utility_west_chart[$month][$year]['operation_general_waste'])) ? $utility_west_chart[$month][$year]['operation_general_waste'] : 0;

						$data_paper = (!empty($utility_west_chart[$month][$year]['operation_paper_waste'])) ? $utility_west_chart[$month][$year]['operation_paper_waste'] : 0;

						$data_food = (!empty($utility_west_chart[$month][$year]['operation_food_waste'])) ? $utility_west_chart[$month][$year]['operation_food_waste'] : 0;

						$data_cardboard = (!empty($utility_west_chart[$month][$year]['operation_cardboard_waste'])) ? $utility_west_chart[$month][$year]['operation_cardboard_waste'] : 0;

						$data_plastic = (!empty($utility_west_chart[$month][$year]['operation_plastic_waste'])) ? $utility_west_chart[$month][$year]['operation_plastic_waste'] : 0;

						$data_glass = (!empty($utility_west_chart[$month][$year]['operation_glass_waste'])) ? $utility_west_chart[$month][$year]['operation_glass_waste'] : 0;

						$data_occupancy = (!empty($utility_west_chart[$month][$year]['occupancy'])) ? $utility_west_chart[$month][$year]['occupancy'] : 0;



						// Round values

						//$pre_data_occupancy = round($pre_data_occupancy,2);

						//$data_occupancy = round($data_occupancy,2);



						if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) {

							// Average Previous year data

							$total_sum_pre_data_general += $pre_data_general;

							$total_sum_pre_data_paper += $pre_data_paper;

							$total_sum_pre_data_food += $pre_data_food;

							$total_sum_pre_data_cardboard += $pre_data_cardboard;

							$total_sum_pre_data_plastic += $pre_data_plastic;

							$total_sum_pre_data_glass += $pre_data_glass;

							$total_sum_pre_data_occupancy += $pre_data_occupancy;



							// Average Current year data

							$total_sum_data_general += $data_general;

							$total_sum_data_paper += $data_paper;

							$total_sum_data_food += $data_food;

							$total_sum_data_cardboard += $data_cardboard;

							$total_sum_data_plastic += $data_plastic;

							$total_sum_data_glass += $data_glass;

							$total_sum_data_occupancy += $data_occupancy;



							$total_months++;
						}

						?>



					var arrValuesNull = [null];

					<?php if ($totalGeneralWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalPaperWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalFoodWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalCardboardWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalPlasticWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalGlassWaste != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					arrValuesNull.push(null);

					arrValuesNull.push(null);

					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];

					var arrValues = ['<?php echo $monthdata; ?>'];

					<?php if ($totalGeneralWaste != 0) { ?>

						arrValuesPre.push(<?php echo isset($pre_data_general) && is_finite($pre_data_general) ? $pre_data_general : 0; ?>);
					<?php } ?>
					<?php if ($totalPaperWaste != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_paper) && is_finite($pre_data_paper) ? $pre_data_paper : 0; ?>);
					<?php } ?>
					<?php if ($totalFoodWaste != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_food) && is_finite($pre_data_food) ? $pre_data_food : 0; ?>);
					<?php } ?>
					<?php if ($totalCardboardWaste != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_cardboard) && is_finite($pre_data_cardboard) ? $pre_data_cardboard : 0; ?>);
					<?php } ?>
					<?php if ($totalPlasticWaste != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_plastic) && is_finite($pre_data_plastic) ? $pre_data_plastic : 0; ?>);
					<?php } ?>
					<?php if ($totalGlassWaste != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_glass) && is_finite($pre_data_glass) ? $pre_data_glass : 0; ?>);
					<?php } ?>
					arrValuesPre.push(<?php echo isset($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
					arrValuesPre.push(null);
					<?php if ($totalGeneralWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_general) && is_finite($data_general) ? $data_general : 0; ?>);
					<?php } ?>
					<?php if ($totalPaperWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_paper) && is_finite($data_paper) ? $data_paper : 0; ?>);
					<?php } ?>
					<?php if ($totalFoodWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_food) && is_finite($data_food) ? $data_food : 0; ?>);
					<?php } ?>
					<?php if ($totalCardboardWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_cardboard) && is_finite($data_cardboard) ? $data_cardboard : 0; ?>);
					<?php } ?>
					<?php if ($totalPlasticWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_plastic) && is_finite($data_plastic) ? $data_plastic : 0; ?>);
					<?php } ?>
					<?php if ($totalGlassWaste != 0) { ?>
						arrValues.push(<?php echo isset($data_glass) && is_finite($data_glass) ? $data_glass : 0; ?>);
					<?php } ?>
					arrValues.push(null);
					arrValues.push(<?php echo isset($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
					arrValuesMulti.push(arrValuesNull);

					arrValuesMulti.push(arrValuesPre);

					arrValuesMulti.push(arrValues);

			<?php

					}
				}

				// Average Previous year data

				$AVG_pre_data_general = ($total_sum_pre_data_general / $total_months);

				$AVG_pre_data_paper = ($total_sum_pre_data_paper / $total_months);

				$AVG_pre_data_food = ($total_sum_pre_data_food / $total_months);

				$AVG_pre_data_cardboard = ($total_sum_pre_data_cardboard / $total_months);

				$AVG_pre_data_plastic = ($total_sum_pre_data_plastic / $total_months);

				$AVG_pre_data_glass = ($total_sum_pre_data_glass / $total_months);

				$AVG_pre_data_occupancy = ($total_sum_pre_data_occupancy / $total_months);



				// Average Current year data

				$YTD_total_months = $this->_ci->config->config['YTD_month_count'];

				$AVG_data_general = ($total_sum_data_general / $YTD_total_months);

				$AVG_data_paper = ($total_sum_data_paper / $YTD_total_months);

				$AVG_data_food = ($total_sum_data_food / $YTD_total_months);

				$AVG_data_cardboard = ($total_sum_data_cardboard / $YTD_total_months);

				$AVG_data_plastic = ($total_sum_data_plastic / $YTD_total_months);

				$AVG_data_glass = ($total_sum_data_glass / $YTD_total_months);

				$AVG_data_occupancy = ($total_sum_data_occupancy / $YTD_total_months);



				$AVG_pre_data_occupancy = round($AVG_pre_data_occupancy, 2);

				$AVG_data_occupancy = round($AVG_data_occupancy, 2);



				$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];

				$prevYear = $year - 1;

				?>

			var arrAvgNull = [null];

			<?php if ($totalGeneralWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalPaperWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalFoodWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalCardboardWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalPlasticWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalGlassWaste != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			arrAvgNull.push(null);

			arrAvgNull.push(null);

			var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];

			<?php if ($totalGeneralWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_general) && is_finite($AVG_pre_data_general)) ? $AVG_pre_data_general : 0; ?>);
			<?php } ?>
			<?php if ($totalPaperWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_paper) && is_finite($AVG_pre_data_paper)) ? $AVG_data_paper : 0; ?>);
			<?php } ?>
			<?php if ($totalFoodWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_food) && is_finite($AVG_pre_data_food)) ? $AVG_pre_data_food : 0; ?>);
			<?php } ?>
			<?php if ($totalCardboardWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_cardboard) && is_finite($AVG_pre_data_cardboard)) ? $AVG_pre_data_cardboard : 0; ?>);
			<?php } ?>
			<?php if ($totalPlasticWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_plastic) && is_finite($AVG_pre_data_plastic)) ? $AVG_pre_data_plastic : 0; ?>);
			<?php } ?>
			<?php if ($totalGlassWaste != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_glass) && is_finite($AVG_pre_data_glass)) ? $AVG_pre_data_glass : 0; ?>);
			<?php } ?>
			arrAvgPre.push(<?php echo isset($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy) ? $AVG_pre_data_occupancy : 0; ?>);
			arrAvgPre.push(null);
			var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
			<?php if ($totalGeneralWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_general) && is_finite($AVG_data_general)) ? $AVG_data_general : 0; ?>);
			<?php } ?>
			<?php if ($totalPaperWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_paper) && is_finite($AVG_data_paper)) ? $AVG_data_paper : 0; ?>);
			<?php } ?>
			<?php if ($totalFoodWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_food) && is_finite($AVG_data_food)) ? $AVG_data_food : 0; ?>);
			<?php } ?>
			<?php if ($totalCardboardWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_cardboard) && is_finite($AVG_data_cardboard)) ? $AVG_data_cardboard : 0; ?>);
			<?php } ?>
			<?php if ($totalPlasticWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_plastic) && is_finite($AVG_data_plastic)) ? $AVG_data_plastic : 0; ?>);
			<?php } ?>
			<?php if ($totalGlassWaste != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_glass) && is_finite($AVG_data_glass)) ? $AVG_data_glass : 0; ?>);
			<?php } ?>
			arrAvg.push(null);
			arrAvg.push(<?php echo isset($AVG_data_occupancy) && is_finite($AVG_data_occupancy) ? $AVG_data_occupancy : 0; ?>);
			arrValuesMulti.push(arrAvgNull);
			arrValuesMulti.push(arrAvgPre);
			arrValuesMulti.push(arrAvg);

			var data = google.visualization.arrayToDataTable(arrValuesMulti);

			var options = {

				height: 700,

				title: '<?php echo lang("totalwest"); ?>',

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 28

				},

				hAxis: {
					title: '<?php echo lang("month"); ?>',
					titleTextStyle: {
						fontName: 'Arial'
					},
					slantedText: true,
					slantedTextAngle: 45
				},

				vAxes: {

					0: {
						title: '<?php echo lang("kgs"); ?>',
						titleTextStyle: {
							fontName: 'Arial',
						}
					},

					1: {
						title: '<?php echo lang("occupancy"); ?>',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 18
						},
						'minValue': 200,
						ticks: [0, 50, 100, 150, 200]
					}

				},

				interpolateNulls: true,

				legend: {
					position: 'top',
					maxLines: 1
				},

				isStacked: true,

				series: {

					0: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Generalwaste']; ?>'
					},

					1: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Paperwaste']; ?>'
					},

					2: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Foodwaste']; ?>'
					},

					3: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Cardboardwaste']; ?>'
					},

					4: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Plasticwaste']; ?>'
					},

					5: {
						targetAxisIndex: 0,
						color: '<?php echo $chart_legend_colors['Glasswaste']; ?>'
					},

					6: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

					7: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

				}

			}



			var wasteChart = new google.visualization.ColumnChart(document.getElementById('wasteChart_<?php echo $id; ?>'));

			google.visualization.events.addListener(wasteChart, 'ready', function() {

				setTimeout(function() {

					var imgUri = '';

					imgUri = wasteChart.getImageURI();

					document.getElementById('wasteChartImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			wasteChart.draw(data, options);

		<?php } ?>

		// Utility cost basr chart



		<?php

		if (!empty($utility_cost_chart)) {



			//For colors

			/* $colorElectricity = ($totalElectricity != 0) ? $chart_legend_colors['Electricity'] : '';

      $colorFuel = ($totalFuel != 0) ? $chart_legend_colors['Fuel'] : '';

      $colorLpg = ($totalLpg != 0) ? $chart_legend_colors['LPG'] : '';

      $colorNaturalGas = ($totalNaturalGas != 0) ? $chart_legend_colors['Natural_Gas'] : '';

      $colorWater = ($totalWater != 0) ? $chart_legend_colors['Water'] : '';

      $colorHeatingDistrict = ($totalHeatingDistrict != 0) ? $chart_legend_colors['District_Heating'] : '';

      $colorCoolingDistrict = ($totalCoolingDistrict != 0) ? $chart_legend_colors['District_Cooling'] : ''; */

			?>





			var arrTitle = ['Month'];

			var arrValuesMulti = [];

			<?php if ($totalElectricity != 0) { ?>

				arrTitle.push('<?php echo lang("electricity"); ?>');

			<?php } ?>

			<?php if ($totalFuel != 0) { ?>

				arrTitle.push('<?php echo lang("fuel"); ?>');

			<?php } ?>

			<?php if ($totalLpg != 0) { ?>

				arrTitle.push('<?php echo lang("lpg"); ?>');

			<?php } ?>

			<?php if ($totalNaturalGas != 0) { ?>

				arrTitle.push('<?php echo lang("natural-gas"); ?>');

			<?php } ?>

			<?php if ($totalWater != 0) { ?>

				arrTitle.push('<?php echo lang("water"); ?>');

			<?php } ?>

			<?php if ($totalHeatingDistrict != 0) { ?>

				arrTitle.push('<?php echo lang("heating-district"); ?>');

			<?php } ?>

			<?php if ($totalCoolingDistrict != 0) { ?>

				arrTitle.push('<?php echo lang("cooling-district"); ?>');

			<?php } ?>

			arrTitle.push('<?php echo lang("occupancy") . "-" . $last_year; ?>');

			arrTitle.push('<?php echo lang("occupancy") . "-" . $current_year; ?>');

			arrValuesMulti.push(arrTitle);

			<?php

				$total_months = 0;

				$total_sum_pre_data_electricity = 0;

				$total_sum_pre_data_fuel = 0;

				$total_sum_pre_data_lpg = 0;

				$total_sum_pre_data_natural_gas = 0;

				$total_sum_pre_data_heating_district = 0;

				$total_sum_pre_data_cooling_district = 0;

				$total_sum_pre_data_water = 0;

				$total_sum_pre_data_cdd = 0;

				$total_sum_pre_data_hdd = 0;

				$total_sum_pre_data_occupancy = 0;

				$total_sum_pre_data_budget = 0;

				$total_sum_data_electricity = 0;

				$total_sum_data_fuel = 0;

				$total_sum_data_lpg = 0;

				$total_sum_data_natural_gas = 0;

				$total_sum_data_heating_district = 0;

				$total_sum_data_cooling_district = 0;

				$total_sum_data_water = 0;

				$total_sum_data_cdd = 0;

				$total_sum_data_hdd = 0;

				$total_sum_data_occupancy = 0;

				$total_sum_data_budget = 0;

				foreach ($resultkeys as $year => $value) {

					foreach ($value as $key1 => $month) {

						// Previous year data

						$prevYear = $year - 1;

						$pre_monthdata = $montharray[$month] . ' ' . ($prevYear);

						$pre_data_electricity = (!empty($utility_cost_chart[$month][$prevYear]['electricity'])) ? $utility_cost_chart[$month][$prevYear]['electricity'] : 0;

						$pre_data_fuel = (!empty($utility_cost_chart[$month][$prevYear]['fuel'])) ? $utility_cost_chart[$month][$prevYear]['fuel'] : 0;

						$pre_data_lpg = (!empty($utility_cost_chart[$month][$prevYear]['lpg'])) ? $utility_cost_chart[$month][$prevYear]['lpg'] : 0;

						$pre_data_natural_gas = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas'] : 0;

						$pre_data_heating_district = (!empty($utility_cost_chart[$month][$prevYear]['heating_district'])) ? $utility_cost_chart[$month][$prevYear]['heating_district'] : 0;

						$pre_data_cooling_district = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district'] : 0;

						$pre_data_water = (!empty($utility_cost_chart[$month][$prevYear]['water'])) ? $utility_cost_chart[$month][$prevYear]['water'] : 0;

						$pre_data_cdd = (!empty($utility_cost_chart[$month][$prevYear]['cdd'])) ? $utility_cost_chart[$month][$prevYear]['cdd'] : 0;

						$pre_data_hdd = (!empty($utility_cost_chart[$month][$prevYear]['hdd'])) ? $utility_cost_chart[$month][$prevYear]['hdd'] : 0;

						$pre_data_occupancy = (!empty($utility_cost_chart[$month][$prevYear]['occupancy'])) ? $utility_cost_chart[$month][$prevYear]['occupancy'] : 0;

						$pre_data_budget = (!empty($utility_cost_chart[$month][$prevYear]['budget'])) ? $utility_cost_chart[$month][$prevYear]['budget'] : 0;



						// Current year data

						$monthdata = $montharray[$month] . ' ' . $year;

						$data_electricity = (!empty($utility_cost_chart[$month][$year]['electricity'])) ? $utility_cost_chart[$month][$year]['electricity'] : 0;

						$data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;

						$data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;

						$data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas'])) ? $utility_cost_chart[$month][$year]['natural_gas'] : 0;

						$data_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district'])) ? $utility_cost_chart[$month][$year]['heating_district'] : 0;

						$data_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district'])) ? $utility_cost_chart[$month][$year]['cooling_district'] : 0;

						$data_water = (!empty($utility_cost_chart[$month][$year]['water'])) ? $utility_cost_chart[$month][$year]['water'] : 0;

						$data_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

						$data_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

						$data_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

						$data_budget = (!empty($utility_cost_chart[$month][$year]['budget'])) ? $utility_cost_chart[$month][$year]['budget'] : 0;



						// Round values

						$pre_data_occupancy = round($pre_data_occupancy, 2);

						$data_occupancy = round($data_occupancy, 2);

						$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'electricity');
						$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'fuel_oil');
						$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'lpg');
						$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'natural_gas');
						$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_heating');
						$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_cooling');
						$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'water');
						// Calculate carbon footprint

						$pre_data_electricity = round($pre_data_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

						$pre_data_fuel = round($pre_data_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

						$pre_data_lpg = round($pre_data_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

						$pre_data_natural_gas = round($pre_data_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

						$pre_data_heating_district = round($pre_data_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

						$pre_data_cooling_district = round($pre_data_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);



						$data_electricity = round($data_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

						$data_fuel = round($data_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

						$data_lpg = round($data_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

						$data_natural_gas = round($data_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

						$data_heating_district = round($data_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

						$data_cooling_district = round($data_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);



						// if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) {

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

						// }

						?>

					var arrValuesNull = [null];

					<?php if ($totalElectricity != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalFuel != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalLpg != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalNaturalGas != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalWater != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalHeatingDistrict != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalCoolingDistrict != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					arrValuesNull.push(null);

					arrValuesNull.push(null);

					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];

					var arrValues = ['<?php echo $monthdata; ?>'];

					<?php if ($totalElectricity != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalWater != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_water) && is_finite($pre_data_water) ? $pre_data_water : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
					<?php } ?>
					arrValuesPre.push(<?php echo isset($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
					arrValuesPre.push(null);
					<?php if ($totalElectricity != 0) { ?>
						arrValues.push(<?php echo isset($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValues.push(<?php echo isset($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValues.push(<?php echo isset($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValues.push(<?php echo isset($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalWater != 0) { ?>
						arrValues.push(<?php echo isset($data_water) && is_finite($data_water) ? $data_water : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValues.push(<?php echo isset($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValues.push(<?php echo isset($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
					<?php } ?>

					arrValues.push(null);
					arrValues.push(<?php echo isset($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
					arrValuesMulti.push(arrValuesNull);
					arrValuesMulti.push(arrValuesPre);
					arrValuesMulti.push(arrValues);
			<?php
					}
				}
				// Average Previous year data
				$AVG_pre_data_electricity = ($total_sum_pre_data_electricity / $total_months);
				$AVG_pre_data_fuel = ($total_sum_pre_data_fuel / $total_months);

				$AVG_pre_data_lpg = ($total_sum_pre_data_lpg / $total_months);

				$AVG_pre_data_natural_gas = ($total_sum_pre_data_natural_gas / $total_months);

				$AVG_pre_data_heating_district = ($total_sum_pre_data_heating_district / $total_months);

				$AVG_pre_data_cooling_district = ($total_sum_pre_data_cooling_district / $total_months);

				$AVG_pre_data_water = ($total_sum_pre_data_water / $total_months);

				$AVG_pre_data_cdd = ($total_sum_pre_data_cdd / $total_months);

				$AVG_pre_data_hdd = ($total_sum_pre_data_hdd / $total_months);

				$AVG_pre_data_occupancy = ($total_sum_pre_data_occupancy / $total_months);

				$AVG_pre_data_budget = ($total_sum_pre_data_budget / $total_months);



				// Average Current year data

				$YTD_total_months = $this->_ci->config->config['YTD_month_count'];

				$AVG_data_electricity = ($total_sum_data_electricity / $YTD_total_months);

				$AVG_data_fuel = ($total_sum_data_fuel / $YTD_total_months);

				$AVG_data_lpg = ($total_sum_data_lpg / $YTD_total_months);

				$AVG_data_natural_gas = ($total_sum_data_natural_gas / $YTD_total_months);

				$AVG_data_heating_district = ($total_sum_data_heating_district / $YTD_total_months);

				$AVG_data_cooling_district = ($total_sum_data_cooling_district / $YTD_total_months);

				$AVG_data_water = ($total_sum_data_water / $YTD_total_months);

				$AVG_data_cdd = ($total_sum_data_cdd / $YTD_total_months);

				$AVG_data_hdd = ($total_sum_data_hdd / $YTD_total_months);

				$AVG_data_occupancy = ($total_sum_data_occupancy / $YTD_total_months);

				$AVG_data_budget = ($total_sum_data_budget / $total_months);



				$AVG_pre_data_occupancy = round($AVG_pre_data_occupancy, 2);

				$AVG_data_occupancy = round($AVG_data_occupancy, 2);



				$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];

				$prevYear = $year - 1;

				?>

			var arrAvgNull = [null];

			<?php if ($totalElectricity != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalFuel != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalLpg != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalNaturalGas != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalWater != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalHeatingDistrict != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalCoolingDistrict != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			arrAvgNull.push(null);

			arrAvgNull.push(null);

			var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];

			<?php if ($totalElectricity != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_electricity) && is_finite($AVG_pre_data_electricity)) ? $AVG_pre_data_electricity : 0; ?>);
			<?php } ?>
			<?php if ($totalFuel != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_fuel) && is_finite($AVG_pre_data_fuel)) ? $AVG_pre_data_fuel : 0; ?>);
			<?php } ?>
			<?php if ($totalLpg != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_lpg) && is_finite($AVG_pre_data_lpg)) ? $AVG_pre_data_lpg : 0; ?>);
			<?php } ?>
			<?php if ($totalNaturalGas != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_natural_gas) && is_finite($AVG_pre_data_natural_gas)) ? $AVG_pre_data_natural_gas : 0; ?>);
			<?php } ?>
			<?php if ($totalWater != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_water) && is_finite($AVG_pre_data_water)) ? $AVG_pre_data_water : 0; ?>);
			<?php } ?>
			<?php if ($totalHeatingDistrict != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_heating_district) && is_finite($AVG_pre_data_heating_district)) ? $AVG_pre_data_heating_district : 0; ?>);
			<?php } ?>
			<?php if ($totalCoolingDistrict != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_cooling_district) && is_finite($AVG_pre_data_cooling_district)) ? $AVG_pre_data_cooling_district : 0; ?>);
			<?php } ?>

			arrAvgPre.push(<?php echo isset($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy) ? $AVG_pre_data_occupancy : 0; ?>);
			arrAvgPre.push(null);
			var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
			<?php if ($totalElectricity != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_electricity) && is_finite($AVG_data_electricity)) ? $AVG_data_electricity : 0; ?>);
			<?php } ?>
			<?php if ($totalFuel != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_fuel) && is_finite($AVG_data_fuel)) ? $AVG_data_fuel : 0; ?>);
			<?php } ?>
			<?php if ($totalLpg != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_lpg) && is_finite($AVG_data_lpg)) ? $AVG_data_lpg : 0; ?>);
			<?php } ?>
			<?php if ($totalNaturalGas != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_natural_gas) && is_finite($AVG_data_natural_gas)) ? $AVG_data_natural_gas : 0; ?>);
			<?php } ?>
			<?php if ($totalWater != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_water) && is_finite($AVG_data_water)) ? $AVG_data_water : 0; ?>);
			<?php } ?>
			<?php if ($totalHeatingDistrict != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_heating_district) && is_finite($AVG_data_heating_district)) ? $AVG_data_heating_district : 0; ?>);
			<?php } ?>
			<?php if ($totalCoolingDistrict != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_cooling_district) && is_finite($AVG_data_cooling_district)) ? $AVG_data_cooling_district : 0; ?>);
			<?php } ?>



			arrAvg.push(null);

			arrAvg.push(<?php echo !empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy) ? ($AVG_data_occupancy) : 0; ?>);

			arrValuesMulti.push(arrAvgNull);

			arrValuesMulti.push(arrAvgPre);

			arrValuesMulti.push(arrAvg);

			var data = google.visualization.arrayToDataTable(arrValuesMulti);

			var options = {

				height: 700,

				isStacked: true,

				title: '<?php echo $utility_cost_chart["utility_cost_chart_title"]; ?>',

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 30

				},

				hAxis: {
					title: '<?php echo lang("month"); ?>',
					titleTextStyle: {
						fontName: 'Arial'
					},
					slantedText: true,
					slantedTextAngle: 45
				},

				vAxes: {

					0: {
						title: '<?php echo lang("utility-cost-chart-yaxis-0-title") . ' ( ' . REPORT_CURRENCY . '' . REPORT_CURRENCY_SYMBOL . ' )'; ?>',
						titleTextStyle: {
							fontName: 'Arial',
						}
					},

					1: {
						title: '<?php echo lang("occupancy"); ?>',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 18
						},
						'minValue': 100,
						ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
					}

				},

				interpolateNulls: true,

				series: {

					<?php $i = 0;

						if ($totalElectricity != 0) {

							?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorElectricity; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalFuel != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorFuel; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalLpg != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorLpg; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalNaturalGas != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorNaturalGas; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalWater != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorWater; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalHeatingDistrict != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorHeatingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalCoolingDistrict != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorCoolingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php echo $i;

						$i += 1;

						?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

					<?php echo $i; ?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

				},

				legend: {
					position: 'top',
					maxLines: 3
				}

			};

			var chart1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart1, 'ready', function() {

				setTimeout(function() {

					var imgUri = '';

					imgUri = chart1.getImageURI();

					document.getElementById('columnChartImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart1.draw(data, options);

		<?php } ?>



		<?php /* * *************************************Monthly column chart**************************************** */ ?>

		<?php if (!empty($utility_cost_chart)) {

			?>



			<?php

				$current_year = date('Y');

				$current_month = date('n');

				if ($current_month == 1) {

					$current_year_monthly = $current_year - 1;

					$current_month_monthly = 12;
				} else {

					$current_year_monthly = $current_year;

					$current_month_monthly = $current_month - 1;
				}





				$resultkeysMonthlyreport = array();

				$resultkeysMonthlyreport[$current_year_monthly] = array($current_month_monthly);



				$total_months = 0;

				foreach ($resultkeysMonthlyreport as $year => $value) {

					foreach ($value as $key1 => $month) {

						// Previous year data

						$prevYear = $year - 1;

						$pre_monthdata = $montharray[$month] . ' ' . ($prevYear);

						$pre_data_electricity = (!empty($utility_cost_chart[$month][$prevYear]['electricity'])) ? $utility_cost_chart[$month][$prevYear]['electricity'] : 0;

						$pre_data_fuel = (!empty($utility_cost_chart[$month][$prevYear]['fuel'])) ? $utility_cost_chart[$month][$prevYear]['fuel'] : 0;

						$pre_data_lpg = (!empty($utility_cost_chart[$month][$prevYear]['lpg'])) ? $utility_cost_chart[$month][$prevYear]['lpg'] : 0;

						$pre_data_natural_gas = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas'] : 0;

						$pre_data_heating_district = (!empty($utility_cost_chart[$month][$prevYear]['heating_district'])) ? $utility_cost_chart[$month][$prevYear]['heating_district'] : 0;

						$pre_data_cooling_district = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district'] : 0;

						$pre_data_water = (!empty($utility_cost_chart[$month][$prevYear]['water'])) ? $utility_cost_chart[$month][$prevYear]['water'] : 0;

						$pre_data_cdd = (!empty($utility_cost_chart[$month][$prevYear]['cdd'])) ? $utility_cost_chart[$month][$prevYear]['cdd'] : 0;

						$pre_data_hdd = (!empty($utility_cost_chart[$month][$prevYear]['hdd'])) ? $utility_cost_chart[$month][$prevYear]['hdd'] : 0;

						$pre_data_occupancy = (!empty($utility_cost_chart[$month][$prevYear]['occupancy'])) ? $utility_cost_chart[$month][$prevYear]['occupancy'] : 0;

						$pre_data_budget = (!empty($utility_cost_chart[$month][$prevYear]['budget'])) ? $utility_cost_chart[$month][$prevYear]['budget'] : 0;



						// Current year data

						$monthdata = $montharray[$month] . ' ' . $year;

						$data_electricity = (!empty($utility_cost_chart[$month][$year]['electricity'])) ? $utility_cost_chart[$month][$year]['electricity'] : 0;

						$data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;

						$data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;

						$data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas'])) ? $utility_cost_chart[$month][$year]['natural_gas'] : 0;

						$data_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district'])) ? $utility_cost_chart[$month][$year]['heating_district'] : 0;

						$data_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district'])) ? $utility_cost_chart[$month][$year]['cooling_district'] : 0;

						$data_water = (!empty($utility_cost_chart[$month][$year]['water'])) ? $utility_cost_chart[$month][$year]['water'] : 0;

						$data_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

						$data_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

						$data_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

						$data_budget = (!empty($utility_cost_chart[$month][$year]['budget'])) ? $utility_cost_chart[$month][$year]['budget'] : 0;



						// Round values

						$pre_data_occupancy = round($pre_data_occupancy, 2);

						$data_occupancy = round($data_occupancy, 2);

						?>

					var arrTitle = ['Month'];

					var arrValuesMulti = [];



					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];

					var arrValues = ['<?php echo $monthdata; ?>'];

					<?php

								if ($pre_data_electricity != 0 || $data_electricity != 0) { ?>

						arrTitle.push('<?php echo lang("electricity"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
						arrValues.push(<?php echo isset($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
					<?php }

								if ($pre_data_fuel != 0 || $data_fuel != 0) { ?>
						arrTitle.push('<?php echo lang("fuel"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
						arrValues.push(<?php echo isset($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
					<?php }

								if ($pre_data_lpg != 0 || $data_lpg != 0) { ?>
						arrTitle.push('<?php echo lang("lpg"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
						arrValues.push(<?php echo isset($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
					<?php }

								if ($pre_data_natural_gas != 0 || $data_natural_gas != 0) { ?>
						arrTitle.push('<?php echo lang("natural-gas"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
						arrValues.push(<?php echo isset($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
					<?php }

								if ($pre_data_water != 0 || $data_water != 0) { ?>
						arrTitle.push('<?php echo lang("water"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_water) && is_finite($pre_data_water) ? $pre_data_water : 0; ?>);
						arrValues.push(<?php echo isset($data_water) && is_finite($data_water) ? $data_water : 0; ?>);
					<?php }

								if ($pre_data_heating_district != 0 || $data_heating_district != 0) { ?>
						arrTitle.push('<?php echo lang("heating-district"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
						arrValues.push(<?php echo isset($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
					<?php }

								if ($pre_data_cooling_district != 0 || $data_cooling_district != 0) { ?>
						arrTitle.push('<?php echo lang("cooling-district"); ?>');
						arrValuesPre.push(<?php echo isset($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
						arrValues.push(<?php echo isset($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
					<?php }
								?>

					arrTitle.push('<?php echo lang("occupancy"); ?>');
					arrValuesPre.push(<?php echo isset($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
					arrValues.push(<?php echo isset($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);

					arrValuesMulti.push(arrTitle);
					arrValuesMulti.push(arrValuesPre);

					arrValuesMulti.push(arrValues);

			<?php

					}
				}

				?>

			var series1 = {};

			var i = 0;

			<?php

				if ($pre_data_electricity != 0 || $data_electricity != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorElectricity; ?>'
				};

			<?php }



				if ($pre_data_fuel != 0 || $data_fuel != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorFuel; ?>'
				};

			<?php }



				if ($pre_data_lpg != 0 || $data_lpg != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorLpg; ?>'
				};

			<?php }



				if ($pre_data_natural_gas != 0 || $data_natural_gas != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorNaturalGas; ?>'
				};

			<?php }



				if ($pre_data_water != 0 || $data_water != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorWater; ?>'
				};

			<?php }



				if ($pre_data_heating_district != 0 || $data_heating_district != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorHeatingDistrict; ?>'
				};

			<?php }



				if ($pre_data_cooling_district != 0 || $data_cooling_district != 0) { ?>

				series1[i++] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorCoolingDistrict; ?>'
				};

			<?php }

				?>

			series1[i++] = {
				targetAxisIndex: 1,
				type: "line",
				pointShape: 'square',
				pointSize: 10
			};

			series1[i++] = {
				targetAxisIndex: 1,
				type: "line",
				pointShape: 'square',
				pointSize: 10
			};





			var data = google.visualization.arrayToDataTable(arrValuesMulti);

			var options = {

				height: 700,

				isStacked: true,

				title: '<?php echo $utility_cost_chart["utility_cost_chart_title"]; ?>',

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 32

				},

				hAxis: {
					title: '<?php echo lang("month"); ?>',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					},
					slantedText: true,
					slantedTextAngle: 45
				},

				vAxes: {

					0: {
						title: '<?php echo lang("utility-cost-chart-yaxis-0-title") . ' (' . REPORT_CURRENCY . '' . REPORT_CURRENCY_SYMBOL . ')'; ?>',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 24
						}
					},

					1: {
						title: '<?php echo lang("occupancy"); ?>',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 24
						},
						'minValue': 100,
						ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
					}

				},

				interpolateNulls: true,

				series: series1,

				legend: {
					position: 'top',
					maxLines: 3,
					textStyle: {
						fontName: 'Arial',
						fontSize: 18
					}
				}

			};

			var chart1_monthly_<?php echo $id; ?> = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_monthly_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart1_monthly_<?php echo $id; ?>, 'ready', function() {

				setTimeout(function() {

					var imgUri = '';

					imgUri = chart1_monthly_<?php echo $id; ?>.getImageURI();

					document.getElementById('columnChartImg_monthly_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart1_monthly_<?php echo $id; ?>.draw(data, options);

		<?php } ?>

		<?php /* * *************************************Monthly column chart**************************************** */ ?>



		<?php /* * ******************************Carbonfootprint Chart******************************************* */ ?>

		<?php if (!empty($utility_cost_chart)) {

			?>

			var arrTitle = ['Month'];

			var arrValuesMulti = [];

			<?php if ($totalElectricity != 0) { ?>

				arrTitle.push('<?php echo lang("electricity"); ?>');

			<?php } ?>

			<?php if ($totalFuel != 0) { ?>

				arrTitle.push('<?php echo lang("fuel"); ?>');

			<?php } ?>

			<?php if ($totalLpg != 0) { ?>

				arrTitle.push('<?php echo lang("lpg"); ?>');

			<?php } ?>

			<?php if ($totalNaturalGas != 0) { ?>

				arrTitle.push('<?php echo lang("natural-gas"); ?>');

			<?php } ?>

			<?php if ($totalHeatingDistrict != 0) { ?>

				arrTitle.push('<?php echo lang("heating-district"); ?>');

			<?php } ?>

			<?php if ($totalCoolingDistrict != 0) { ?>

				arrTitle.push('<?php echo lang("cooling-district"); ?>');

			<?php } ?>

			arrTitle.push('<?php echo lang("occupancy") . "-" . $last_year; ?>');

			arrTitle.push('<?php echo lang("occupancy") . "-" . $current_year; ?>');

			arrValuesMulti.push(arrTitle);

			<?php

				$total_months = 0;

				$total_sum_pre_data_electricity = 0;

				$total_sum_pre_data_fuel = 0;

				$total_sum_pre_data_lpg = 0;

				$total_sum_pre_data_natural_gas = 0;

				$total_sum_pre_data_heating_district = 0;

				$total_sum_pre_data_cooling_district = 0;

				$total_sum_pre_data_water = 0;

				$total_sum_pre_data_cdd = 0;

				$total_sum_pre_data_hdd = 0;

				$total_sum_pre_data_occupancy = 0;

				$total_sum_pre_data_budget = 0;

				$total_sum_data_electricity = 0;

				$total_sum_data_fuel = 0;

				$total_sum_data_lpg = 0;

				$total_sum_data_natural_gas = 0;

				$total_sum_data_heating_district = 0;

				$total_sum_data_cooling_district = 0;

				$total_sum_data_water = 0;

				$total_sum_data_cdd = 0;

				$total_sum_data_hdd = 0;

				$total_sum_data_occupancy = 0;

				$total_sum_data_budget = 0;


				foreach ($resultkeys as $year => $value) {

					foreach ($value as $key1 => $month) {

						// Previous year data

						$prevYear = $year - 1;

						$pre_monthdata = $montharray[$month] . ' ' . ($prevYear);

						$pre_data_carbon_electricity = (!empty($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'] - $utility_cost_chart[$month][$prevYear]['onsite_generator'] - $utility_cost_chart[$month][$prevYear]['renewable_energy']) : 0;

						$pre_data_carbon_fuel = (!empty($utility_cost_chart[$month][$prevYear]['fuel_consumption'])) ? $utility_cost_chart[$month][$prevYear]['fuel_consumption'] : 0;

						$pre_data_carbon_lpg = (!empty($utility_cost_chart[$month][$prevYear]['lpg_consumption'])) ? $utility_cost_chart[$month][$prevYear]['lpg_consumption'] : 0;

						$pre_data_carbon_natural_gas = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas_consumption'] : 0;

						$pre_data_carbon_heating_district = (!empty($utility_cost_chart[$month][$prevYear]['heating_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['heating_district_consumption'] : 0;

						$pre_data_carbon_cooling_district = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district_consumption'] : 0;

						$pre_data_carbon_water = (!empty($utility_cost_chart[$month][$prevYear]['water_consumption'])) ? $utility_cost_chart[$month][$prevYear]['water_consumption'] : 0;

						$pre_data_carbon_cdd = (!empty($utility_cost_chart[$month][$prevYear]['cdd'])) ? $utility_cost_chart[$month][$prevYear]['cdd'] : 0;

						$pre_data_carbon_hdd = (!empty($utility_cost_chart[$month][$prevYear]['hdd'])) ? $utility_cost_chart[$month][$prevYear]['hdd'] : 0;

						$pre_data_carbon_occupancy = (!empty($utility_cost_chart[$month][$prevYear]['occupancy'])) ? $utility_cost_chart[$month][$prevYear]['occupancy'] : 0;

						$pre_data_carbon_budget = (!empty($utility_cost_chart[$month][$prevYear]['budget'])) ? $utility_cost_chart[$month][$prevYear]['budget'] : 0;



						// Current year data

						$monthdata = $montharray[$month] . ' ' . $year;

						$data_carbon_electricity = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year]['total_electricity_kwh'] - $utility_cost_chart[$month][$year]['onsite_generator'] - $utility_cost_chart[$month][$year]['renewable_energy']) : 0;

						$data_carbon_fuel = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? $utility_cost_chart[$month][$year]['fuel_consumption'] : 0;

						$data_carbon_lpg = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;

						$data_carbon_heating_district = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;

						$data_carbon_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year]['heating_district_consumption'] : 0;

						$data_carbon_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year]['cooling_district_consumption'] : 0;

						$data_carbon_water = (!empty($utility_cost_chart[$month][$year]['water_consumption'])) ? $utility_cost_chart[$month][$year]['water_consumption'] : 0;

						$data_carbon_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

						$data_carbon_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

						$data_carbon_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

						$data_carbon_budget = (!empty($utility_cost_chart[$month][$year]['budget'])) ? $utility_cost_chart[$month][$year]['budget'] : 0;



						// Round values

						$pre_data_carbon_occupancy = round($pre_data_carbon_occupancy, 2);

						$data_carbon_occupancy = round($data_carbon_occupancy, 2);

						$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'electricity');
						$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'fuel_oil');
						$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'lpg');
						$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'natural_gas');
						$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_heating');
						$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_cooling');
						$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'water');
						// Calculate carbon footprint

						$pre_data_carbon_electricity = round($pre_data_carbon_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

						$pre_data_carbon_fuel = round($pre_data_carbon_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

						$pre_data_carbon_lpg = round($pre_data_carbon_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

						$pre_data_carbon_natural_gas = round($pre_data_carbon_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

						$pre_data_carbon_heating_district = round($pre_data_carbon_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

						$pre_data_carbon_cooling_district = round($pre_data_carbon_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);



						$data_carbon_electricity = round($data_carbon_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

						$data_carbon_fuel = round($data_carbon_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

						$data_carbon_lpg = round($data_carbon_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

						$data_carbon_natural_gas = round($data_carbon_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

						$data_carbon_heating_district = round($data_carbon_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

						$data_carbon_cooling_district = round($data_carbon_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);


						// if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) { //commented cause of average issue(average is taken jan data by default)

						// Average Previous year data

						$total_sum_pre_data_electricity += $pre_data_carbon_electricity;

						$total_sum_pre_data_fuel += $pre_data_carbon_fuel;

						$total_sum_pre_data_lpg += $pre_data_carbon_lpg;

						$total_sum_pre_data_natural_gas += $pre_data_carbon_natural_gas;

						$total_sum_pre_data_heating_district += $pre_data_carbon_heating_district;

						$total_sum_pre_data_cooling_district += $pre_data_carbon_cooling_district;

						$total_sum_pre_data_water += $pre_data_carbon_water;

						$total_sum_pre_data_cdd += $pre_data_carbon_cdd;

						$total_sum_pre_data_hdd += $pre_data_carbon_hdd;

						$total_sum_pre_data_occupancy += $pre_data_carbon_occupancy;

						$total_sum_pre_data_budget += $pre_data_carbon_budget;



						// Average Current year data

						$total_sum_data_electricity += $data_carbon_electricity;

						$total_sum_data_fuel += $data_carbon_fuel;

						$total_sum_data_lpg += $data_carbon_lpg;

						$total_sum_data_natural_gas += $data_carbon_natural_gas;

						$total_sum_data_heating_district += $data_carbon_heating_district;

						$total_sum_data_cooling_district += $data_carbon_cooling_district;

						$total_sum_data_water += $data_carbon_water;

						$total_sum_data_cdd += $data_carbon_cdd;

						$total_sum_data_hdd += $data_carbon_hdd;

						$total_sum_data_occupancy += $data_carbon_occupancy;

						$total_sum_data_budget += $data_carbon_budget;



						$total_months++;

						// }

						?>

					var arrValuesNull = [null];

					<?php if ($totalElectricity != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalFuel != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalLpg != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalNaturalGas != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalHeatingDistrict != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					<?php if ($totalCoolingDistrict != 0) { ?>

						arrValuesNull.push(null);

					<?php } ?>

					arrValuesNull.push(null);

					arrValuesNull.push(null);

					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];

					<?php if ($totalElectricity != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_electricity) && is_finite($pre_data_carbon_electricity) ? $pre_data_carbon_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_fuel) && is_finite($pre_data_carbon_fuel) ? $pre_data_carbon_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_lpg) && is_finite($pre_data_carbon_lpg) ? $pre_data_carbon_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_natural_gas) && is_finite($pre_data_carbon_natural_gas) ? $pre_data_carbon_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_heating_district) && is_finite($pre_data_carbon_heating_district) ? $pre_data_carbon_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo isset($pre_data_carbon_cooling_district) && is_finite($pre_data_carbon_cooling_district) ? $pre_data_carbon_cooling_district : 0; ?>);
					<?php } ?>
					arrValuesPre.push(<?php echo isset($pre_data_carbon_occupancy) && is_finite($pre_data_carbon_occupancy) ? $pre_data_carbon_occupancy : 0; ?>);
					arrValuesPre.push(null);
					var arrValues = ['<?php echo $monthdata; ?>'];
					<?php if ($totalElectricity != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_electricity) && is_finite($data_carbon_electricity) ? $data_carbon_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_fuel) && is_finite($data_carbon_fuel) ? $data_carbon_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_lpg) && is_finite($data_carbon_lpg) ? $data_carbon_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_natural_gas) && is_finite($data_carbon_natural_gas) ? $data_carbon_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_heating_district) && is_finite($data_carbon_heating_district) ? $data_carbon_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValues.push(<?php echo isset($data_carbon_cooling_district) && is_finite($data_carbon_cooling_district) ? $data_carbon_cooling_district : 0; ?>);
					<?php } ?>
					arrValues.push(null);
					arrValues.push(<?php echo isset($data_carbon_occupancy) && is_finite($data_carbon_occupancy) ? $data_carbon_occupancy : 0; ?>);
					arrValuesMulti.push(arrValuesNull);
					arrValuesMulti.push(arrValuesPre);
					arrValuesMulti.push(arrValues);
			<?php

					}
				}

				// Average Previous year data

				$AVG_pre_data_electricity = ($total_sum_pre_data_electricity / $total_months);

				$AVG_pre_data_fuel = ($total_sum_pre_data_fuel / $total_months);

				$AVG_pre_data_lpg = ($total_sum_pre_data_lpg / $total_months);

				$AVG_pre_data_natural_gas = ($total_sum_pre_data_natural_gas / $total_months);

				$AVG_pre_data_heating_district = ($total_sum_pre_data_heating_district / $total_months);

				$AVG_pre_data_cooling_district = ($total_sum_pre_data_cooling_district / $total_months);

				$AVG_pre_data_water = ($total_sum_pre_data_water / $total_months);

				$AVG_pre_data_cdd = ($total_sum_pre_data_cdd / $total_months);

				$AVG_pre_data_hdd = ($total_sum_pre_data_hdd / $total_months);

				$AVG_pre_data_occupancy = ($total_sum_pre_data_occupancy / $total_months);

				$AVG_pre_data_budget = ($total_sum_pre_data_budget / $total_months);



				// Average Current year data

				// $YTD_total_months = $this->_ci->config->config['YTD_month_count']; //commented cause of average issue(average is taken jan data by default)

				$YTD_total_months = $total_months;

				$AVG_data_electricity = ($total_sum_data_electricity / $YTD_total_months);

				$AVG_data_fuel = ($total_sum_data_fuel / $YTD_total_months);

				$AVG_data_lpg = ($total_sum_data_lpg / $YTD_total_months);

				$AVG_data_natural_gas = ($total_sum_data_natural_gas / $YTD_total_months);

				$AVG_data_heating_district = ($total_sum_data_heating_district / $YTD_total_months);

				$AVG_data_cooling_district = ($total_sum_data_cooling_district / $YTD_total_months);

				$AVG_data_water = ($total_sum_data_water / $YTD_total_months);

				$AVG_data_cdd = ($total_sum_data_cdd / $YTD_total_months);

				$AVG_data_hdd = ($total_sum_data_hdd / $YTD_total_months);

				$AVG_data_occupancy = ($total_sum_data_occupancy / $YTD_total_months);

				$AVG_data_budget = ($total_sum_data_budget / $total_months);



				$AVG_pre_data_occupancy = round($AVG_pre_data_occupancy, 2);

				$AVG_data_occupancy = round($AVG_data_occupancy, 2);



				$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];

				$prevYear = $year - 1;

				?>

			var arrAvgNull = [null];

			<?php if ($totalElectricity != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalFuel != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalLpg != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalNaturalGas != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalHeatingDistrict != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			<?php if ($totalCoolingDistrict != 0) { ?>

				arrAvgNull.push(null);

			<?php } ?>

			arrAvgNull.push(null);

			arrAvgNull.push(null);

			var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];

			<?php if ($totalElectricity != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_electricity) && is_finite($AVG_pre_data_electricity)) ? $AVG_pre_data_electricity : 0; ?>);
			<?php } ?>
			<?php if ($totalFuel != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_fuel) && is_finite($AVG_pre_data_fuel)) ? $AVG_pre_data_fuel : 0; ?>);
			<?php } ?>
			<?php if ($totalLpg != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_lpg) && is_finite($AVG_pre_data_lpg)) ? $AVG_pre_data_lpg : 0; ?>);
			<?php } ?>
			<?php if ($totalNaturalGas != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_natural_gas) && is_finite($AVG_pre_data_natural_gas)) ? $AVG_pre_data_natural_gas : 0; ?>);
			<?php } ?>
			<?php if ($totalHeatingDistrict != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_heating_district) && is_finite($AVG_pre_data_heating_district)) ? $AVG_pre_data_heating_district : 0; ?>);
			<?php } ?>
			<?php if ($totalCoolingDistrict != 0) { ?>
				arrAvgPre.push(<?php echo (!empty($AVG_pre_data_cooling_district) && is_finite($AVG_pre_data_cooling_district)) ? $AVG_pre_data_cooling_district : 0; ?>);
			<?php } ?>
			arrAvgPre.push(<?php echo (!empty($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy)) ? $AVG_pre_data_occupancy : 0; ?>);
			arrAvgPre.push(null);
			var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
			<?php if ($totalElectricity != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_electricity) && is_finite($AVG_data_electricity)) ? $AVG_data_electricity : 0; ?>);
			<?php } ?>
			<?php if ($totalFuel != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_fuel) && is_finite($AVG_data_fuel)) ? $AVG_data_fuel : 0; ?>);
			<?php } ?>
			<?php if ($totalLpg != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_lpg) && is_finite($AVG_data_lpg)) ? $AVG_data_lpg : 0; ?>);
			<?php } ?>
			<?php if ($totalNaturalGas != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_natural_gas) && is_finite($AVG_data_natural_gas)) ? $AVG_data_natural_gas : 0; ?>);
			<?php } ?>
			<?php if ($totalHeatingDistrict != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_heating_district) && is_finite($AVG_data_heating_district)) ? $AVG_data_heating_district : 0; ?>);
			<?php } ?>
			<?php if ($totalCoolingDistrict != 0) { ?>
				arrAvg.push(<?php echo (!empty($AVG_data_cooling_district) && is_finite($AVG_data_cooling_district)) ? $AVG_data_cooling_district : 0; ?>);
			<?php } ?>
			arrAvg.push(null);
			arrAvg.push(<?php echo (!empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy)) ? $AVG_data_occupancy : 0; ?>);
			arrValuesMulti.push(arrAvgNull);

			arrValuesMulti.push(arrAvgPre);

			arrValuesMulti.push(arrAvg);
			var data = google.visualization.arrayToDataTable(arrValuesMulti);

			var options = {

				height: 700,

				isStacked: true,

				title: 'Carbon Emissions (Scope 1 & Scope 2)',

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 28

				},

				hAxis: {
					title: '<?php echo lang("month"); ?>',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					},
					slantedText: true,
					slantedTextAngle: 45
				},

				vAxes: {

					0: {
						title: 'KgCO2e',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 24
						}
					},

					1: {
						title: '<?php echo lang("occupancy"); ?>',
						titleTextStyle: {
							fontName: 'Arial',
							fontSize: 24
						},
						'minValue': 100,
						ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
					}

				},

				interpolateNulls: true,

				series: {

					<?php $i = 0;

						if ($totalElectricity != 0) {

							?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorElectricity; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalFuel != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorFuel; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalLpg != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorLpg; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalNaturalGas != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorNaturalGas; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalHeatingDistrict != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorHeatingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($totalCoolingDistrict != 0) { ?>

						<?php echo $i; ?>: {
							targetAxisIndex: 0,
							color: '<?php echo $colorCoolingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php echo $i; ?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

					<?php $i += 1; ?>

					<?php echo $i; ?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},

				},

				legend: {
					position: 'top',
					maxLines: 3,
					textStyle: {
						fontName: 'Arial',
						fontSize: 20
					}
				}

			};

			var carbonchart = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_carbon_footprint_<?php echo $id; ?>'));

			google.visualization.events.addListener(carbonchart, 'ready', function() {

				setTimeout(function() {

					var imgUri = '';

					imgUri = carbonchart.getImageURI();

					document.getElementById('columnChartCarbonFootprintImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			carbonchart.draw(data, options);

		<?php } ?>



		// KWH Pie Chart for current year

		<?php

		if (!empty($kwh_pie_chart)) {

			//For colors

			/* $colorElectricity = ($kwh_pie_chart['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';

      $colorFuel = ($kwh_pie_chart['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';

      $colorLpg = ($kwh_pie_chart['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';

      $colorNaturalGas = ($kwh_pie_chart['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';

      $colorWater = ($kwh_pie_chart['water'] != 0) ? $chart_legend_colors['Water'] : '';

      $colorHeatingDistrict = ($kwh_pie_chart['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';

      $colorCoolingDistrict = ($kwh_pie_chart['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : ''; */

			?>

			var data = google.visualization.arrayToDataTable([

				['Energy', 'Usage'],

				<?php

					foreach ($kwh_pie_chart as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . round($val, 2) . '],';
						}
					}

					?>

			]);

			var options = {

				height: 600,

				title: '<?php echo lang("kwh-pie-chart-title"); ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					<?php $i = 0;

						if ($kwh_pie_chart['electricity'] != 0) {

							?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorElectricity; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['fuel'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorFuel; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['lpg'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorLpg; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['natural_gas'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorNaturalGas; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['water'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorWater; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['heating_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorHeatingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart['cooling_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorCoolingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>



				}

			};

			var chart = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart, 'ready', function() {

				setTimeout(function() {

					var imgUri = chart.getImageURI();

					document.getElementById('pieChartImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart.draw(data, options);

		<?php } ?>



		// Cost Pie Chart for current year

		<?php

		if (!empty($cost_pie_chart)) {

			//For colors

			/* $colorElectricity = ($cost_pie_chart['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';

      $colorFuel = ($cost_pie_chart['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';

      $colorLpg = ($cost_pie_chart['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';

      $colorNaturalGas = ($cost_pie_chart['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';

      $colorHeatingDistrict = ($cost_pie_chart['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';

      $colorCoolingDistrict = ($cost_pie_chart['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : '';

      $colorWater = ($cost_pie_chart['water'] != 0) ? $chart_legend_colors['Water'] : ''; */

			?>

			var data = google.visualization.arrayToDataTable([

				['Energy', 'Usage'],

				<?php

					foreach ($cost_pie_chart as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . $val . '],';
						}
					}

					?>

			]);

			var options = {

				height: 600,

				title: '<?php echo lang("cost-pie-chart-title"); ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					<?php $i = 0;

						if ($cost_pie_chart['electricity'] != 0) {

							?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorElectricity; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['fuel'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorFuel; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['lpg'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorLpg; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['natural_gas'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorNaturalGas; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['heating_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorHeatingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['cooling_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorCoolingDistrict; ?>'
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart['water'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorWater; ?>'
						},

					<?php $i += 1;
						} ?>

				}

			};

			var chart2 = new google.visualization.PieChart(document.getElementById('cost_pie_chart_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart2, 'ready', function() {

				setTimeout(function() {

					var imgUri = chart2.getImageURI();

					document.getElementById('pieChartNewImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart2.draw(data, options);

		<?php } ?>

		<?php if (!empty($waste_pie_report)) {

			?>

			var data = google.visualization.arrayToDataTable([

				['Waste', 'Usage'],

				<?php

					foreach ($waste_pie_report as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . $val . '],';
						}
					}

					?>

			]);

			var options = {

				height: 600,

				title: '<?php echo lang("waste_pie_report"); ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					0: {
						color: '<?php echo $chart_legend_colors['Generalwaste']; ?>'
					},

					1: {
						color: '<?php echo $chart_legend_colors['Paperwaste']; ?>'
					},

					2: {
						color: '<?php echo $chart_legend_colors['Foodwaste']; ?>'
					},

					3: {
						color: '<?php echo $chart_legend_colors['Cardboardwaste']; ?>'
					},

					4: {
						color: '<?php echo $chart_legend_colors['Plasticwaste']; ?>'
					},

					5: {
						color: '<?php echo $chart_legend_colors['Glasswaste']; ?>'
					},

				}



			};

			var wastePieChart = new google.visualization.PieChart(document.getElementById('waste_pie_chart_<?php echo $id; ?>'));

			google.visualization.events.addListener(wastePieChart, 'ready', function() {

				setTimeout(function() {

					var imgUri = wastePieChart.getImageURI();

					document.getElementById('wastePieChartImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			wastePieChart.draw(data, options);

		<?php } ?>



		<?php if (!empty($waste_pie_landfill)) {

			?>

			var data = google.visualization.arrayToDataTable([

				['Waste', 'Usage'],

				<?php

					foreach ($waste_pie_landfill as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . $val . '],';
						}
					}

					?>



			]);

			var options = {

				height: 600,

				title: '<?php echo lang("waste_landfill_pie_report"); ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					0: {
						color: '<?php echo $chart_legend_colors['Recyclewaste']; ?>'
					},

					1: {
						color: '<?php echo $chart_legend_colors['Landfill']; ?>'
					},

				}



			};

			var wasteLandfillPieChart = new google.visualization.PieChart(document.getElementById('waste_landfill_pie_chart_<?php echo $id; ?>'));

			google.visualization.events.addListener(wasteLandfillPieChart, 'ready', function() {

				setTimeout(function() {

					var imgUri = wasteLandfillPieChart.getImageURI();

					document.getElementById('wasteLandfillPieChartImg_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			wasteLandfillPieChart.draw(data, options);

		<?php } ?>



		// KWh Pie Chart for last 12 months

		<?php

		if (!empty($kwh_pie_chart_previousmonth)) {

			//For colors

			/* $colorElectricity = ($kwh_pie_chart_previousmonth['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';

      $colorFuel = ($kwh_pie_chart_previousmonth['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';

      $colorLpg = ($kwh_pie_chart_previousmonth['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';

      $colorNaturalGas = ($kwh_pie_chart_previousmonth['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';

      $colorHeatingDistrict = ($kwh_pie_chart_previousmonth['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';

      $colorCoolingDistrict = ($kwh_pie_chart_previousmonth['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : '';

      $colorWater = ($kwh_pie_chart_previousmonth['water'] != 0) ? $chart_legend_colors['Water'] : ''; */

			?>

			var data = google.visualization.arrayToDataTable([

				['Energy', 'Usage'],

				<?php

					foreach ($kwh_pie_chart_previousmonth as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . round($val, 2) . '],';
						}
					}

					?>



			]);

			var options = {

				height: 480,

				title: '<?php echo lang("kwh-pie-chart-last12month-title-monthly") . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					<?php $i = 0;

						if ($kwh_pie_chart_previousmonth['electricity'] != 0) {

							?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorElectricity; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart_previousmonth['fuel'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorFuel; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart_previousmonth['lpg'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorLpg; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart_previousmonth['natural_gas'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorNaturalGas; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart_previousmonth['heating_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorHeatingDistrict; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($kwh_pie_chart_previousmonth['cooling_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorCoolingDistrict; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>



				}

			};

			var chart3 = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_previousmonth_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart3, 'ready', function() {

				setTimeout(function() {

					var imgUri = chart3.getImageURI();

					document.getElementById('pieChartNew2Img_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart3.draw(data, options);

		<?php } ?>



		// Cost Pie Chart for last 12 month

		<?php

		if (!empty($cost_pie_chart_previousmonth)) {

			//For colors

			/* $colorElectricity = ($cost_pie_chart_previousmonth['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';

      $colorFuel = ($cost_pie_chart_previousmonth['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';

      $colorLpg = ($cost_pie_chart_previousmonth['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';

      $colorNaturalGas = ($cost_pie_chart_previousmonth['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';

      $colorHeatingDistrict = ($cost_pie_chart_previousmonth['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';

      $colorCoolingDistrict = ($cost_pie_chart_previousmonth['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : '';

      $colorWater = ($cost_pie_chart_previousmonth['water'] != 0) ? $chart_legend_colors['Water'] : ''; */

			?>

			var data = google.visualization.arrayToDataTable([

				['Energy', 'Usage'],

				<?php

					foreach ($cost_pie_chart_previousmonth as $key => $val) {

						if ($val != 0) {

							echo '["' . lang($key) . '",' . $val . '],';
						}
					}

					?>



			]);

			var options = {

				height: 480,

				title: '<?php echo lang("cost-pie-chart-last12month-title") . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?>',

				sliceVisibilityThreshold: .0,

				pieHole: 0.4,

				titleTextStyle: {

					fontName: 'Arial',

					fontSize: 24

				},

				legend: {
					textStyle: {
						fontName: 'Arial',
						fontSize: 17
					}
				},

				chartArea: {
					width: "100%"
				},

				slices: {

					<?php $i = 0;

						if ($cost_pie_chart_previousmonth['electricity'] != 0) {

							?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorElectricity; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['fuel'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorFuel; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['lpg'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorLpg; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['natural_gas'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorNaturalGas; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['heating_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorHeatingDistrict; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['cooling_district'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorCoolingDistrict; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

					<?php if ($cost_pie_chart_previousmonth['water'] != 0) { ?>

						<?php echo $i; ?>: {
							color: '<?php echo $colorWater; ?>',
							textStyle: {
								fontSize: 18
							}
						},

					<?php $i += 1;
						} ?>

				}

			};

			var chart4 = new google.visualization.PieChart(document.getElementById('cost_pie_chart_previousmonth_<?php echo $id; ?>'));

			google.visualization.events.addListener(chart4, 'ready', function() {

				setTimeout(function() {

					var imgUri = chart4.getImageURI();

					document.getElementById('pieChartNew3Img_<?php echo $id; ?>').value = imgUri;

				}, 1000);

			});

			chart4.draw(data, options);

		<?php } ?>



		<?php /* * *************************************Monthly Charts **************************************** */ ?>

		var chart_data = [];
		// <?php
			// foreach($monthly_chart_data['chart_data'] as $key => $value){
			?>
		//     chart_data['<?php echo $key ?>'] = [];
		// <?php
			//     foreach ($value as $k=>$v){
			?>
		//         chart_data['<?php echo $key; ?>']['<?php echo round($k); ?>'] = <?php echo is_finite($v) ? (is_numeric($v) ? $v : "'" . $v . "'") : 0; ?>;
		//     <?php
				// }
				// }
				//
				?>
		var chart_data = <?php echo json_encode($monthly_chart_data['chart_data']); ?>;
		var chart_index = <?php echo json_encode($monthly_chart_data['chart_index']); ?>;

		var series = {};

		/*

		* "electricity",

		   "fuel",

		   "lpg",

		   "natural_gas",

		   "water",

		   "heating_district",

		   "cooling_district"

		*/

		var i = 0;

		$.each(chart_index, function(index, value) {

			if (value == "electricity") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorElectricity; ?>'
				};

			}

			if (value == "fuel") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorFuel; ?>'
				};

			}

			if (value == "lpg") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorLpg; ?>'
				};

			}

			if (value == "natural_gas") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorNaturalGas; ?>'
				};

			}

			if (value == "water") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorWater; ?>'
				};

			}

			if (value == "heating_district") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorHeatingDistrict; ?>'
				};

			}

			if (value == "cooling_district") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorCoolingDistrict; ?>'
				};

			}

			i = index;



		})

		series[i + 1] = {
			targetAxisIndex: 1,
			type: "line",
			pointShape: 'square',
			pointSize: 10
		};

		series[i + 2] = {
			targetAxisIndex: 1,
			type: "line",
			pointShape: 'square',
			pointSize: 10
		};



		var data = google.visualization.arrayToDataTable(chart_data);

		var options = {

			height: 650,

			isStacked: true,

			title: '<?php echo $utility_cost_chart["utility_cost_chart_title"]; ?>',

			titleTextStyle: {

				fontName: 'Arial',

				fontSize: 32

			},

			hAxis: {
				title: '<?php echo lang("month"); ?>',
				titleTextStyle: {
					fontName: 'Arial',
					fontSize: 24
				},
				slantedText: true,
				slantedTextAngle: 45
			},

			vAxes: {

				0: {
					title: '<?php echo lang("utility-cost-chart-yaxis-0-title") . ' (' . REPORT_CURRENCY . '' . REPORT_CURRENCY_SYMBOL . ')'; ?>',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					}
				},

				1: {
					title: '<?php echo lang("occupancy"); ?>',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					},
					'minValue': 100,
					ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
				}

			},

			interpolateNulls: true,

			series: series,

			legend: {
				position: 'top',
				maxLines: 3,
				textStyle: {
					fontName: 'Arial',
					fontSize: 18
				}
			}

		};



		var chart1_monthly = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_monthly_month_<?php echo $id; ?>'));

		google.visualization.events.addListener(chart1_monthly, 'ready', function() {

			setTimeout(function() {

				var imgUri = '';

				imgUri = chart1_monthly.getImageURI();

				document.getElementById('columnChartImg_monthly_month_<?php echo $id; ?>').value = imgUri;

				unblockUI();

			}, 3000);

		});

		chart1_monthly.draw(data, options);





		var carbon_footprint = [];
		// <?php
			// foreach($monthly_chart_data['carbon_footprint'] as $key => $value){
			?>
		//     carbon_footprint['<?php echo $key ?>'] = [];
		// <?php
			//     foreach ($value as $k=>$v){
			?>
		//         carbon_footprint['<?php echo $key; ?>']['<?php echo round($k); ?>'] = <?php echo is_numeric($v) ? $v : "'" . $v . "'"; ?>;
		//     <?php
				// }
				// }
				//
				?>
		var carbon_footprint = <?php echo json_encode($monthly_chart_data['carbon_footprint']); ?>;
		var chart_index_carbon = <?php echo json_encode($monthly_chart_data['chart_index_carbon']); ?>;

		var series = {};

		/*

		* "electricity",

		   "fuel",

		   "lpg",

		   "natural_gas",

		   "heating_district",

		   "cooling_district"

		*/

		var i = 0;

		$.each(chart_index_carbon, function(index, value) {

			if (value == "electricity") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorElectricity; ?>'
				};

			}

			if (value == "fuel") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorFuel; ?>'
				};

			}

			if (value == "lpg") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorLpg; ?>'
				};

			}

			if (value == "natural_gas") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorNaturalGas; ?>'
				};

			}

			if (value == "heating_district") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorHeatingDistrict; ?>'
				};

			}

			if (value == "cooling_district") {

				series[index] = {
					targetAxisIndex: 0,
					color: '<?php echo $colorCoolingDistrict; ?>'
				};

			}

			i = index;



		})

		series[i + 1] = {
			targetAxisIndex: 1,
			type: "line",
			pointShape: 'square',
			color: "#000",
			pointSize: 10
		};



		var data = google.visualization.arrayToDataTable(carbon_footprint);

		var options = {

			height: 700,

			isStacked: true,

			title: 'Carbon Emissions (Scope 1 & Scope 2)',

			titleTextStyle: {

				fontName: 'Arial',

				fontSize: 32

			},

			hAxis: {
				title: '<?php echo lang("month"); ?>',
				titleTextStyle: {
					fontName: 'Arial',
					fontSize: 24
				},
				slantedText: true,
				slantedTextAngle: 45
			},

			vAxes: {

				0: {
					title: 'KgCO2e',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					}
				},

				1: {
					title: '<?php echo lang("occupancy"); ?>',
					titleTextStyle: {
						fontName: 'Arial',
						fontSize: 24
					},
					'minValue': 100,
					ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
				}

			},

			interpolateNulls: true,

			series: series,

			legend: {
				position: 'top',
				maxLines: 3,
				textStyle: {
					fontName: 'Arial',
					fontSize: 18
				}
			}

		};

		var chart1_carbon_footprint = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_carbon_footprint_monthly_<?php echo $id; ?>'));



		google.visualization.events.addListener(chart1_carbon_footprint, 'ready', function() {

			setTimeout(function() {

				var imgUri = '';

				imgUri = chart1_carbon_footprint.getImageURI();

				document.getElementById('columnChartCarbonFootprintMonthlyImg_<?php echo $id; ?>').value = imgUri;

				unblockUI();

			}, 1000);

		});

		chart1_carbon_footprint.draw(data, options);


	}



	$(window).resize(function() {

		drawChart();

	});
</script>

<article class="card">

	<div class="article-header"><?php echo lang('site_total_utilities_reports'); /* lang('reports'); */ ?> : <?php echo $site_detail['site_location_name']; ?></div>

	<div class="card-wrap">

		<div class="row">

			<div class="col-sm-12">

				<div class="panel panel-primary">

					<div class="panel-body">

						<div id="utility_cost_chart_<?php echo $id; ?>" style="height:700px;">

							<?php if (empty($utility_cost_chart)) { ?>

								<div class="table-responsive">

									<table class="table table-striped">

										<tr>

											<td><?php echo lang('no-records') ?></td>

										</tr>

									</table>

								</div>

							<?php } ?>

						</div>

						<div id="wasteChart_<?php echo $id; ?>"></div>

						<div id="wasteMonthlyChart_<?php echo $id; ?>"></div>

						<div id="wasteMonthlyChart_month_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_carbon_footprint_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_carbon_footprint_monthly_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_carbon_footprint_annual_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_pre_<?php echo $id; ?>"></div>

						<div id="utility_waste_chart_pre_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_monthly_<?php echo $id; ?>"></div>

						<div id="utility_cost_chart_monthly_month_<?php echo $id; ?>"></div>

					</div>

				</div>

			</div>

			<br />

			<div class="col-sm-12">

				<div class="panel panel-primary">

					<div class="panel-body">

						<div class="col-sm-6">

							<div id="kwh_pie_chart_<?php echo $id; ?>">

								<?php if (empty($kwh_pie_chart)) { ?>

									<div class="table-responsive">

										<table class="table table-striped">

											<tr>

												<td><?php echo lang('no-records') ?></td>

											</tr>

										</table>

									</div>

								<?php } ?>

							</div>

							<div id="kwh_pie_chart_pre_<?php echo $id; ?>"></div>

						</div>

						<div class="col-sm-6">

							<div id="cost_pie_chart_<?php echo $id; ?>">

								<?php if (empty($cost_pie_chart)) { ?>

									<div class="table-responsive">

										<table class="table table-striped">

											<tr>

												<td><?php echo lang('no-records') ?></td>

											</tr>

										</table>

									</div>

								<?php } ?>

							</div>

							<div id="cost_pie_chart_pre_<?php echo $id; ?>"></div>

						</div>

					</div>

				</div>

			</div>

			<br />

			<div class="col-sm-12">

				<div class="panel panel-primary">

					<div class="panel-body">

						<div class="col-sm-6">

							<div id="waste_pie_chart_<?php echo $id; ?>"></div>

							<div id="waste_pie_monthly_chart_<?php echo $id; ?>"></div>

							<div id="waste_pie_monthly_chart_month_<?php echo $id; ?>"></div>

							<div id="waste_annual_pie_chart_<?php echo $id; ?>"></div>

						</div>

						<div class="col-sm-6">

							<div id="waste_landfill_pie_chart_<?php echo $id; ?>"></div>

							<div id="waste_pie_landfill_monthly_chart_<?php echo $id; ?>"></div>

							<div id="waste_annual_landfill_pie_chart_<?php echo $id; ?>"></div>

						</div>

					</div>

				</div>

			</div>

			<div class="col-sm-12">

				<div class="panel panel-primary">

					<div class="panel-body">

						<div class="col-sm-6">

							<div id="kwh_pie_chart_previousmonth_<?php echo $id; ?>">

								<?php if (empty($kwh_pie_chart_previousmonth)) { ?>

									<div class="table-responsive">

										<table class="table table-striped">

											<tr>

												<td><?php echo lang('no-records') ?></td>

											</tr>

										</table>

									</div>

								<?php } ?>

							</div>

						</div>

						<div class="col-sm-6">

							<div id="cost_pie_chart_previousmonth_<?php echo $id; ?>">

								<?php if (empty($cost_pie_chart_previousmonth)) { ?>

									<div class="table-responsive">

										<table class="table table-striped">

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
