<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<!-- <script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script> -->
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min', 'bootstrap-datepicker-new'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new'));
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year - 1;
$utility_current_year = $current_year;
$utility_last_year = $last_year;
if ($utility_year_selected != $current_year) {
    $utility_current_year = $utility_year_selected;
    $utility_last_year = $utility_year_selected - 1;
}
//define currency;
$isLocal = true;
if ($currency == "base") {
    $isLocal = false;
}
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
?>
<?php
$currentYear = date('Y'); //date('Y');
$currentMonth = intval(date('n'));
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
<style>
    table.table-condensed .disable-year {
	display: none;
    }
</style>
<script type="text/javascript">
    blockUI();
    google.load("visualization", "1", {
	packages: ["corechart"]
    });
    google.setOnLoadCallback(drawChart);
    function drawChart() {
	<?php
	if (!empty($utility_west_chart)) {
	    //For colors
	    /* $colorgeneral = ($totalgeneral != 0) ? $chart_legend_colors['general'] : '';
      $colorFuel = ($totalFuel != 0) ? $chart_legend_colors['Fuel'] : '';
      $colorLpg = ($totalLpg != 0) ? $chart_legend_colors['LPG'] : '';
      $colorNaturalGas = ($totalNaturalGas != 0) ? $chart_legend_colors['Natural_Gas'] : '';
      $colorWater = ($totalWater != 0) ? $chart_legend_colors['Water'] : '';
      $colorHeatingDistrict = ($totalHeatingDistrict != 0) ? $chart_legend_colors['District_Heating'] : '';
      $colorCoolingDistrict = ($totalCoolingDistrict != 0) ? $chart_legend_colors['District_Cooling'] : ''; */
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
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_general = (!empty($utility_west_chart[$month][$year - 1]['operation_general_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_general_waste'] : 0;
		    $pre_data_paper = (!empty($utility_west_chart[$month][$year - 1]['operation_paper_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_paper_waste'] : 0;
		    $pre_data_food = (!empty($utility_west_chart[$month][$year - 1]['operation_food_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_food_waste'] : 0;
		    $pre_data_cardboard = (!empty($utility_west_chart[$month][$year - 1]['operation_cardboard_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_cardboard_waste'] : 0;
		    $pre_data_plastic = (!empty($utility_west_chart[$month][$year - 1]['operation_plastic_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_plastic_waste'] : 0;
		    $pre_data_glass = (!empty($utility_west_chart[$month][$year - 1]['operation_glass_waste'])) ? $utility_west_chart[$month][$year - 1]['operation_glass_waste'] : 0;
		    $pre_data_occupancy = (!empty($utility_west_chart[$month][$year - 1]['occupancy'])) ? $utility_west_chart[$month][$year - 1]['occupancy'] : 0;
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
			arrValuesPre.push(<?php echo is_finite($pre_data_general) ? $pre_data_general : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPaperWaste != 0) { ?>
			arrValuesPre.push(<?php echo is_finite($pre_data_paper) ? $pre_data_paper : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFoodWaste != 0) { ?>
			arrValuesPre.push(<?php echo is_finite($pre_data_food) ? $pre_data_food : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCardboardWaste != 0) { ?>
			arrValuesPre.push(<?php echo is_finite($pre_data_cardboard) ? $pre_data_cardboard : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPlasticWaste != 0) { ?>
			arrValuesPre.push(<?php echo is_finite($pre_data_plastic) ? $pre_data_plastic : 0; ?>);
		    <?php } ?>
		    <?php if ($totalGlassWaste != 0) { ?>
			arrValuesPre.push(<?php echo is_finite($pre_data_glass) ? $pre_data_glass : 0; ?>);
		    <?php } ?>
		    arrValuesPre.push(<?php echo is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
		    arrValuesPre.push(null);
		    <?php if ($totalGeneralWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_general) ? $data_general : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPaperWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_paper) ? $data_paper : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFoodWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_food) ? $data_food : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCardboardWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_cardboard) ? $data_cardboard : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPlasticWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_plastic) ? $data_plastic : 0; ?>);
		    <?php } ?>
		    <?php if ($totalGlassWaste != 0) { ?>
			arrValues.push(<?php echo is_finite($data_glass) ? $data_glass : 0; ?>);
		    <?php } ?>
		    arrValues.push(null);
		    arrValues.push(<?php echo is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
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
	    arrAvgPre.push(<?php echo (!empty($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy)) ? $AVG_pre_data_occupancy : 0; ?>);
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
	    arrAvg.push(<?php echo (!empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy)) ? $AVG_data_occupancy : 0; ?>);
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
	    var wasteChart = new google.visualization.ColumnChart(document.getElementById('wasteChart'));
	    google.visualization.events.addListener(wasteChart, 'ready', function() {
		setTimeout(function() {
		    var imgUri = '';
		    imgUri = wasteChart.getImageURI();
		    document.getElementById('wasteChartImg').value = imgUri;
		}, 1000);
	    });
	    wasteChart.draw(data, options);
	<?php } ?>
	// Utility cost bar chart
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
			arrTitle.push('<?php echo lang("occupancy") . "-" . $utility_last_year; ?>');
			arrTitle.push('<?php echo lang("occupancy") . "-" . $utility_current_year; ?>');
			arrTitle.push('<?php echo lang("budget"); ?>');
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
			if ($utility_year_selected != date('Y')) {
				$CURRENT_YEAR_MAX_MONTH_ID = 12;
				$utility_result_keys = [];
				for ($i = 1; $i <= 12; $i++) {
					$utility_result_keys[$utility_year_selected][] = $i;
				}
			} else {
				$utility_result_keys = $resultkeys;
			}
			foreach ($utility_result_keys as $year => $value) {
				foreach ($value as $key1 => $month) {
					// Previous year data
					$pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
					$pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] : 0;
					$pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;
					$pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;
					$pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] : 0;
					$pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;
					$pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;
					$pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;
					$pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;
					$pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;
					$pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;
					$pre_data_budget = (!empty($utility_cost_chart[$month][$year - 1]['budget'])) ? $utility_cost_chart[$month][$year - 1]['budget'] : 0;
					// Current year data
					$monthdata = $montharray[$month] . ' ' . $year;
					$data_electricity = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year]['total_electricity_kwh'] : 0;
					$data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;
					$data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;
					$data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;
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
					if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) {
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
					}
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
					arrValuesNull.push(null);
					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
					var arrValues = ['<?php echo $monthdata; ?>'];
					<?php if ($totalElectricity != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalWater != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_water) && is_finite($pre_data_water) ? $pre_data_water : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo !empty($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
					<?php } ?>
					arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
					arrValuesPre.push(null);
					arrValuesPre.push(null);
					<?php if ($totalElectricity != 0) { ?>
						arrValues.push(<?php echo !empty($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
					<?php } ?>
					<?php if ($totalFuel != 0) { ?>
						arrValues.push(<?php echo !empty($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
					<?php } ?>
					<?php if ($totalLpg != 0) { ?>
						arrValues.push(<?php echo !empty($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
					<?php } ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValues.push(<?php echo !empty($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
					<?php } ?>
					<?php if ($totalWater != 0) { ?>
						arrValues.push(<?php echo !empty($data_water) && is_finite($data_water) ? $data_water : 0; ?>);
					<?php } ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValues.push(<?php echo !empty($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
					<?php } ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValues.push(<?php echo !empty($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
					<?php } ?>
					arrValues.push(null);
					arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
					arrValues.push(<?php echo !empty($data_budget) && is_finite($data_budget) ? $data_budget : 0; ?>);
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
			if ($utility_year_selected != date('Y')) {
				$YTD_total_months = 12;
			}
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
			arrAvgNull.push(null);
			var arrAvgPre = ['<?php echo ($year - 1) . " " . lang("average"); ?>'];
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
			arrAvgPre.push(<?php echo (!empty($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy)) ? $AVG_pre_data_occupancy : 0; ?>);
			arrAvgPre.push(null);
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
			arrAvg.push(<?php echo !empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy) ? $AVG_data_occupancy : 0; ?>);
			arrAvg.push(<?php echo !empty($AVG_data_budget) && is_finite($AVG_data_budget) ? $AVG_data_budget: 0; ?>);
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
						title: '<?php echo $isLocal ? lang("utility-cost-chart-yaxis-0-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-yaxis-0-title") . ' (' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
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
					$i += 1; ?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},
					<?php echo $i;
					$i += 1; ?>: {
						targetAxisIndex: 1,
						type: "line",
						pointShape: 'square',
						pointSize: 10
					},
					<?php echo $i; ?>: {
						targetAxisIndex: 0,
						type: "line",
						pointShape: 'triangle',
						pointSize: 15
					},
				},
				legend: {
					position: 'top',
					maxLines: 3
				}
			};
			var chart1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart'));
			google.visualization.events.addListener(chart1, 'ready', function() {
				setTimeout(function() {
					var imgUri = '';
					imgUri = chart1.getImageURI();
					document.getElementById('columnChartImg').value = imgUri;
					//save as png
					if ($('.saveImgUrl').length == 0) {
						var download = document.createElement('a');
						download.href = imgUri;
						download.onclick = saveAsPng;
						download.download = "utility_cost_chart.png";
						download.text = 'Save as png';
						download.className = 'btn btn-secondary btn-submit saveImgUrl';
						$('#saveImage').append(download);
					}
				}, 1000);
			});
			chart1.draw(data, options);
		<?php } ?>
		<?php /* * *************************************Monthly column chart**************************************** */ ?>
		<?php if (!empty($utility_cost_chart)) { ?>

			<?php
			$current_year = date('Y');
			$current_month = date('n');
			$resultkeysMonthlyreport = array();
			$resultkeysMonthlyreport[$current_year] = array($current_month);

			$total_months = 0;
			foreach ($resultkeysMonthlyreport as $year => $value) {
				foreach ($value as $key1 => $month) {
					// Previous year data
					$pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
					$pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] : 0;
					$pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;
					$pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;
					$pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] : 0;
					$pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;
					$pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;
					$pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;
					$pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;
					$pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;
					$pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;
					$pre_data_budget = (!empty($utility_cost_chart[$month][$year - 1]['budget'])) ? $utility_cost_chart[$month][$year - 1]['budget'] : 0;

					// Current year data
					$monthdata = $montharray[$month] . ' ' . $year;
					$data_electricity = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year]['total_electricity_kwh'] : 0;
					$data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;
					$data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;
					$data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;
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
		    <?php if ($pre_data_electricity != 0 || $data_electricity != 0) { ?>
			arrTitle.push('<?php echo lang("electricity"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
			arrValues.push(<?php echo !empty($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
		    <?php }
		    if ($pre_data_fuel != 0 || $data_fuel != 0) {
		    ?>
			arrTitle.push('<?php echo lang("fuel"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
			arrValues.push(<?php echo !empty($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
		    <?php }
		    if ($pre_data_lpg != 0 || $data_lpg != 0) {
		    ?>
			arrTitle.push('<?php echo lang("lpg"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
			arrValues.push(<?php echo !empty($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
		    <?php }
		    if ($pre_data_natural_gas != 0 || $data_natural_gas != 0) {
		    ?>
			arrTitle.push('<?php echo lang("natural-gas"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
			arrValues.push(<?php echo !empty($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
		    <?php }
		    if ($pre_data_water != 0 || $data_water != 0) {
		    ?>
			arrTitle.push('<?php echo lang("water"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_water) && is_finite($pre_data_water) ? $pre_data_water : 0; ?>);
			arrValues.push(<?php echo !empty($data_water) && is_finite($data_water) ? $data_water : 0; ?>);
		    <?php }
		    if ($pre_data_heating_district != 0 || $data_heating_district != 0) {
		    ?>
			arrTitle.push('<?php echo lang("heating-district"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
			arrValues.push(<?php echo !empty($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
		    <?php }
		    if ($pre_data_cooling_district != 0 || $data_cooling_district != 0) {
		    ?>
			arrTitle.push('<?php echo lang("cooling-district"); ?>');
			arrValuesPre.push(<?php echo !empty($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
			arrValues.push(<?php echo !empty($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
		    <?php }
		    ?>
		    arrTitle.push('<?php echo lang("occupancy"); ?>');
		    arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
		    arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
		    arrValuesMulti.push(arrTitle);
		    arrValuesMulti.push(arrValuesPre);
		    arrValuesMulti.push(arrValues);
	    <?php
		}
	    }
	    ?>
	    var series1 = {};
	    var i = 0;
	    <?php if ($pre_data_electricity != 0 || $data_electricity != 0) { ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorElectricity; ?>'
		};
	    <?php }
	    if ($pre_data_fuel != 0 || $data_fuel != 0) {
	    ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorFuel; ?>'
		};
	    <?php }
	    if ($pre_data_lpg != 0 || $data_lpg != 0) {
	    ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorLpg; ?>'
		};
	    <?php }
	    if ($pre_data_natural_gas != 0 || $data_natural_gas != 0) {
	    ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorNaturalGas; ?>'
		};
	    <?php }
	    if ($pre_data_water != 0 || $data_water != 0) {
	    ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorWater; ?>'
		};
	    <?php }
	    if ($pre_data_heating_district != 0 || $data_heating_district != 0) {
	    ?>
		series1[i++] = {
		    targetAxisIndex: 0,
		    color: '<?php echo $colorHeatingDistrict; ?>'
		};
	    <?php }
	    if ($pre_data_cooling_district != 0 || $data_cooling_district != 0) {
	    ?>
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
			title: '<?php echo $isLocal ? lang("utility-cost-chart-yaxis-0-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-yaxis-0-title") . ' (' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
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
		},
		legend: {
		    position: 'top',
		    maxLines: 3,
		    textStyle: {
			fontSize: 18
		    }
		}
	    };
	    var chart1_monthly = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_monthly'));
	    google.visualization.events.addListener(chart1_monthly, 'ready', function() {
		setTimeout(function() {
		    var imgUri = '';
		    imgUri = chart1_monthly.getImageURI();
		    document.getElementById('columnChartImg_monthly').value = imgUri;
		}, 1000);
	    });
	    chart1_monthly.draw(data, options);
	<?php } ?>
	<?php /* * *************************************Monthly column chart**************************************** */ ?>
	<?php /* * ******************************Carbonfootprint Chart******************************************* */ ?>
	<?php if (!empty($utility_cost_chart)) { ?>
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
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] - $utility_cost_chart[$month][$year - 1]['onsite_generator'] - $utility_cost_chart[$month][$year - 1]['renewable_energy']) : 0;
		    $pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;
		    $pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;
		    $pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] : 0;
		    $pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;
		    $pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;
		    $pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;
		    $pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;
		    $pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;
		    $pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;
		    $pre_data_budget = (!empty($utility_cost_chart[$month][$year - 1]['budget'])) ? $utility_cost_chart[$month][$year - 1]['budget'] : 0;

		    // Current year data
		    $monthdata = $montharray[$month] . ' ' . $year;
		    $data_electricity = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year]['total_electricity_kwh'] - $utility_cost_chart[$month][$year]['onsite_generator'] - $utility_cost_chart[$month][$year]['renewable_energy']) : 0;
		    $data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;
		    $data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;
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
		    // Calculate carbon footprint
			$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'electricity');
			$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'fuel_oil');
			$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'lpg');
			$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'natural_gas');
			$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_heating');
			$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_cooling');
			$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'water');


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

		    if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) {
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
		    }
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
			arrValuesPre.push(<?php echo !empty($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
		    arrValuesPre.push(null);
		    var arrValues = ['<?php echo $monthdata; ?>'];
		    <?php if ($totalElectricity != 0) { ?>
			arrValues.push(<?php echo !empty($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel != 0) { ?>
			arrValues.push(<?php echo !empty($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg != 0) { ?>
			arrValues.push(<?php echo !empty($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas != 0) { ?>
			arrValues.push(<?php echo !empty($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict != 0) { ?>
			arrValues.push(<?php echo !empty($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict != 0) { ?>
			arrValues.push(<?php echo !empty($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValues.push(null);
		    arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
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
		title: 'Carbon Footprint',
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
			fontSize: 20
		    }
		}
	    };
	<?php } ?>
	<?php // for annual
	?>
	<?php if (!empty($utility_cost_chart_pre)) { ?>
	    var arrTitle = ['Month'];
	    var arrValuesMulti = [];
	    <?php if ($totalElectricity_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("electricity"); ?>');
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("fuel"); ?>');
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("lpg"); ?>');
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("natural-gas"); ?>');
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("heating-district"); ?>');
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
		arrTitle.push('<?php echo lang("cooling-district"); ?>');
	    <?php } ?>
	    arrTitle.push('<?php echo lang("occupancy") . "-" . ($filters['filters_comparision_chart_pre']["start_year"] - 1); ?>');
	    arrTitle.push('<?php echo lang("occupancy") . "-" . $filters['filters_comparision_chart_pre']["start_year"]; ?>');
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
	    foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Previous year data
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_annual_electricity = (!empty($utility_cost_chart_pre[$month][$year - 1]['total_electricity_kwh'])) ? ($utility_cost_chart_pre[$month][$year - 1]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year - 1]['onsite_generator'] - $utility_cost_chart_pre[$month][$year - 1]['renewable_energy']) : 0;
		    $pre_data_annual_fuel = (!empty($utility_cost_chart_pre[$month][$year - 1]['fuel'])) ? $utility_cost_chart_pre[$month][$year - 1]['fuel'] : 0;
		    $pre_data_annual_lpg = (!empty($utility_cost_chart_pre[$month][$year - 1]['lpg_consumption'])) ? $utility_cost_chart_pre[$month][$year - 1]['lpg_consumption'] : 0;
		    $pre_data_annual_natural_gas = (!empty($utility_cost_chart_pre[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart_pre[$month][$year - 1]['natural_gas_consumption'] : 0;
		    $pre_data_annual_heating_district = (!empty($utility_cost_chart_pre[$month][$year - 1]['heating_district'])) ? $utility_cost_chart_pre[$month][$year - 1]['heating_district'] : 0;
		    $pre_data_annual_cooling_district = (!empty($utility_cost_chart_pre[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart_pre[$month][$year - 1]['cooling_district'] : 0;
		    $pre_data_annual_water = (!empty($utility_cost_chart_pre[$month][$year - 1]['water'])) ? $utility_cost_chart_pre[$month][$year - 1]['water'] : 0;
		    $pre_data_annual_cdd = (!empty($utility_cost_chart_pre[$month][$year - 1]['cdd'])) ? $utility_cost_chart_pre[$month][$year - 1]['cdd'] : 0;
		    $pre_data_annual_hdd = (!empty($utility_cost_chart_pre[$month][$year - 1]['hdd'])) ? $utility_cost_chart_pre[$month][$year - 1]['hdd'] : 0;
		    $pre_data_annual_occupancy = (!empty($utility_cost_chart_pre[$month][$year - 1]['occupancy'])) ? $utility_cost_chart_pre[$month][$year - 1]['occupancy'] : 0;
		    $pre_data_annual_budget = (!empty($utility_cost_chart_pre[$month][$year - 1]['budget'])) ? $utility_cost_chart_pre[$month][$year - 1]['budget'] : 0;
		    // Current year data
		    $monthdata = $montharray[$month] . ' ' . $year;
		    $data_annual_electricity = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year]['onsite_generator'] - $utility_cost_chart_pre[$month][$year]['renewable_energy']) : 0;
		    $data_annual_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel'])) ? $utility_cost_chart_pre[$month][$year]['fuel'] : 0;
		    $data_annual_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg_consumption'])) ? $utility_cost_chart_pre[$month][$year]['lpg_consumption'] : 0;
		    $data_annual_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart_pre[$month][$year]['natural_gas_consumption'] : 0;
		    $data_annual_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district'])) ? $utility_cost_chart_pre[$month][$year]['heating_district'] : 0;
		    $data_annual_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district'])) ? $utility_cost_chart_pre[$month][$year]['cooling_district'] : 0;
		    $data_annual_water = (!empty($utility_cost_chart_pre[$month][$year]['water'])) ? $utility_cost_chart_pre[$month][$year]['water'] : 0;
		    $data_annual_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd'])) ? $utility_cost_chart_pre[$month][$year]['cdd'] : 0;
		    $data_annual_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd'])) ? $utility_cost_chart_pre[$month][$year]['hdd'] : 0;
		    $data_annual_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy'])) ? $utility_cost_chart_pre[$month][$year]['occupancy'] : 0;
		    $data_annual_budget = (!empty($utility_cost_chart_pre[$month][$year]['budget'])) ? $utility_cost_chart_pre[$month][$year]['budget'] : 0;
		    // Round values
		    $pre_data_annual_occupancy = round($pre_data_annual_occupancy, 2);
		    $data_annual_occupancy = round($data_annual_occupancy, 2);

		    // Calculate carbon footprint
			$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'electricity');
			$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'fuel_oil');
			$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'lpg');
			$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'natural_gas');
			$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_heating');
			$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'district_cooling');
			$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_detail['id'], 'water');


			$pre_data_annual_electricity = round($pre_data_annual_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

			$pre_data_annual_fuel = round($pre_data_annual_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

			$pre_data_annual_lpg = round($pre_data_annual_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

			$pre_data_annual_natural_gas = round($pre_data_annual_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

			$pre_data_annual_heating_district = round($pre_data_annual_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

			$pre_data_annual_cooling_district = round($pre_data_annual_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);



			$data_annual_electricity = round($data_annual_electricity  * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

			$data_annual_fuel = round($data_annual_fuel  * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

			$data_annual_lpg = round($data_annual_lpg  * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

			$data_annual_natural_gas = round($data_annual_natural_gas  * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

			$data_annual_heating_district = round($data_annual_heating_district  * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

			$data_annual_cooling_district = round($data_annual_cooling_district  * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);


		    // if ($month <= $CURRENT_YEAR_MAX_MONTH_ID) { //commented cause of average issue(average is taken jan data by default)
		    // Average Previous year data
		    $total_sum_pre_data_annual_electricity += $pre_data_annual_electricity;
		    $total_sum_pre_data_annual_fuel += $pre_data_annual_fuel;
		    $total_sum_pre_data_annual_lpg += $pre_data_annual_lpg;
		    $total_sum_pre_data_annual_natural_gas += $pre_data_annual_natural_gas;
		    $total_sum_pre_data_annual_heating_district += $pre_data_annual_heating_district;
		    $total_sum_pre_data_annual_cooling_district += $pre_data_annual_cooling_district;
		    $total_sum_pre_data_annual_water += $pre_data_annual_water;
		    $total_sum_pre_data_annual_cdd += $pre_data_annual_cdd;
		    $total_sum_pre_data_annual_hdd += $pre_data_annual_hdd;
		    $total_sum_pre_data_annual_occupancy += $pre_data_annual_occupancy;
		    $total_sum_pre_data_annual_budget += $pre_data_annual_budget;
		    // Average Current year data
		    $total_sum_data_annual_electricity += $data_annual_electricity;
		    $total_sum_data_annual_fuel += $data_annual_fuel;
		    $total_sum_data_annual_lpg += $data_annual_lpg;
		    $total_sum_data_annual_natural_gas += $data_annual_natural_gas;
		    $total_sum_data_annual_heating_district += $data_annual_heating_district;
		    $total_sum_data_annual_cooling_district += $data_annual_cooling_district;
		    $total_sum_data_annual_water += $data_annual_water;
		    $total_sum_data_annual_cdd += $data_annual_cdd;
		    $total_sum_data_annual_hdd += $data_annual_hdd;
		    $total_sum_data_annual_occupancy += $data_annual_occupancy;
		    $total_sum_data_annual_budget += $data_annual_budget;
		    $total_months++;
		    // }
	    ?>
		    var arrValuesNull = [null];
		    <?php if ($totalElectricity_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    arrValuesNull.push(null);
		    arrValuesNull.push(null);
		    var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
		    <?php if ($totalElectricity_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_electricity) && is_finite($pre_data_annual_electricity) ? $pre_data_annual_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_fuel) && is_finite($pre_data_annual_fuel) ? $pre_data_annual_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_lpg) && is_finite($pre_data_annual_lpg) ? $pre_data_annual_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_natural_gas) && is_finite($pre_data_annual_natural_gas) ? $pre_data_annual_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_heating_district) && is_finite($pre_data_annual_heating_district) ? $pre_data_annual_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_annual_cooling_district) && is_finite($pre_data_annual_cooling_district) ? $pre_data_annual_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValuesPre.push(<?php echo !empty($pre_data_annual_occupancy) && is_finite($pre_data_annual_occupancy) ? $pre_data_annual_occupancy : 0; ?>);
		    arrValuesPre.push(null);
		    var arrValues = ['<?php echo $monthdata; ?>'];
		    <?php if ($totalElectricity_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_electricity) && is_finite($data_annual_electricity) ? $data_annual_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_fuel) && is_finite($data_annual_fuel) ? $data_annual_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_lpg) && is_finite($data_annual_lpg) ? $data_annual_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_natural_gas) && is_finite($data_annual_natural_gas) ? $data_annual_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_heating_district) && is_finite($data_annual_heating_district) ? $data_annual_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
			arrValues.push(<?php echo !empty($data_annual_cooling_district) && is_finite($data_annual_cooling_district) ? $data_annual_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValues.push(null);
		    arrValues.push(<?php echo !empty($data_annual_occupancy) && is_finite($data_annual_occupancy) ? $data_annual_occupancy : 0; ?>);
		    arrValuesMulti.push(arrValuesNull);
		    arrValuesMulti.push(arrValuesPre);
		    arrValuesMulti.push(arrValues);
	    <?php
		}
	    }
	    ?>
	    <?php
	    // Average Previous year data
	    $AVG_pre_data_annual_electricity = ($total_sum_pre_data_annual_electricity / $total_months);
	    $AVG_pre_data_annual_fuel = ($total_sum_pre_data_annual_fuel / $total_months);
	    $AVG_pre_data_annual_lpg = ($total_sum_pre_data_annual_lpg / $total_months);
	    $AVG_pre_data_annual_natural_gas = ($total_sum_pre_data_annual_natural_gas / $total_months);
	    $AVG_pre_data_annual_heating_district = ($total_sum_pre_data_annual_heating_district / $total_months);
	    $AVG_pre_data_annual_cooling_district = ($total_sum_pre_data_annual_cooling_district / $total_months);
	    $AVG_pre_data_annual_water = ($total_sum_pre_data_annual_water / $total_months);
	    $AVG_pre_data_annual_cdd = ($total_sum_pre_data_annual_cdd / $total_months);
	    $AVG_pre_data_annual_hdd = ($total_sum_pre_data_annual_hdd / $total_months);
	    $AVG_pre_data_annual_occupancy = ($total_sum_pre_data_annual_occupancy / $total_months);
	    $AVG_pre_data_annual_budget = ($total_sum_pre_data_annual_budget / $total_months);
	    // Average Current year data
	    // $YTD_total_months = $this->_ci->config->config['YTD_month_count']; //commented cause of average issue(average is taken jan data by default)
	    $YTD_total_months = $total_months;
	    $AVG_data_annual_electricity = ($total_sum_data_annual_electricity / $YTD_total_months);
	    $AVG_data_annual_fuel = ($total_sum_data_annual_fuel / $YTD_total_months);
	    $AVG_data_annual_lpg = ($total_sum_data_annual_lpg / $YTD_total_months);
	    $AVG_data_annual_natural_gas = ($total_sum_data_annual_natural_gas / $YTD_total_months);
	    $AVG_data_annual_heating_district = ($total_sum_data_annual_heating_district / $YTD_total_months);
	    $AVG_data_annual_cooling_district = ($total_sum_data_annual_cooling_district / $YTD_total_months);
	    $AVG_data_annual_water = ($total_sum_data_annual_water / $YTD_total_months);
	    $AVG_data_annual_cdd = ($total_sum_data_annual_cdd / $YTD_total_months);
	    $AVG_data_annual_hdd = ($total_sum_data_annual_hdd / $YTD_total_months);
	    $AVG_data_annual_occupancy = ($total_sum_data_annual_occupancy / $YTD_total_months);
	    $AVG_data_annual_budget = ($total_sum_data_annual_budget / $total_months);
	    $AVG_pre_data_annual_occupancy = round($AVG_pre_data_annual_occupancy, 2);
	    $AVG_data_annual_occupancy = round($AVG_data_annual_occupancy, 2);
	    $chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
	    $prevYear = $year - 1;
	    ?>
	    var arrAvgNull = [null];
	    <?php if ($totalElectricity_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    arrAvgNull.push(null);
	    arrAvgNull.push(null);
	    var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];
	    <?php if ($totalElectricity_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_electricity) && is_finite($AVG_pre_data_annual_electricity)) ? $AVG_pre_data_annual_electricity : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_fuel) && is_finite($AVG_pre_data_annual_fuel)) ? $AVG_pre_data_annual_fuel : 0; ?>);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_lpg) && is_finite($AVG_pre_data_annual_lpg)) ? $AVG_pre_data_annual_lpg : 0; ?>);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_natural_gas) && is_finite($AVG_pre_data_annual_natural_gas)) ? $AVG_pre_data_annual_natural_gas : 0; ?>);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_heating_district) && is_finite($AVG_pre_data_annual_heating_district)) ? $AVG_pre_data_annual_heating_district : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
		arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_cooling_district) && is_finite($AVG_pre_data_annual_cooling_district)) ? $AVG_pre_data_annual_cooling_district : 0; ?>);
	    <?php } ?>
	    arrAvgPre.push(<?php echo (!empty($AVG_pre_data_annual_occupancy) && is_finite($AVG_pre_data_annual_occupancy)) ? $AVG_pre_data_annual_occupancy : 0; ?>);
	    arrAvgPre.push(null);
	    var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
	    <?php if ($totalElectricity_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_electricity) && is_finite($AVG_data_annual_electricity)) ? $AVG_data_annual_electricity : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_fuel) && is_finite($AVG_data_annual_fuel)) ? $AVG_data_annual_fuel : 0; ?>);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_lpg) && is_finite($AVG_data_annual_lpg)) ? $AVG_data_annual_lpg : 0; ?>);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_natural_gas) && is_finite($AVG_data_annual_natural_gas)) ? $AVG_data_annual_natural_gas : 0; ?>);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_heating_district) && is_finite($AVG_data_annual_heating_district)) ? $AVG_data_annual_heating_district : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
		arrAvg.push(<?php echo (!empty($AVG_data_annual_cooling_district) && is_finite($AVG_data_annual_cooling_district)) ? $AVG_data_annual_cooling_district : 0; ?>);
	    <?php } ?>
	    arrAvg.push(null);
	    arrAvg.push(<?php echo (!empty($AVG_data_annual_occupancy) && is_finite($AVG_data_annual_occupancy)) ? $AVG_data_annual_occupancy : 0; ?>);
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
		    if ($totalElectricity_utility_cost_pre) {
		    ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorElectricity; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalFuel_utility_cost_pre) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorFuel; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalLpg_utility_cost_pre) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorLpg; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorNaturalGas; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorHeatingDistrict; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre) { ?>
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
			fontSize: 20
		    }
		}
	    };
	    var carbonchartannual = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_carbon_footprint_annual'));
	    google.visualization.events.addListener(carbonchartannual, 'ready', function() {
		setTimeout(function() {
		    var imgUri = '';
		    imgUri = carbonchartannual.getImageURI();
		    document.getElementById('columnChartCarbonFootprintAnnualImg').value = imgUri;
		}, 1000);
	    });
	    carbonchartannual.draw(data, options);
	<?php } ?>
	<?php /* * ******************************Carbonfootprint Chart******************************************* */ ?>
	<?php if (!empty($utility_waste_chart_pre)) { ?>
	    var arrTitle = ['Month'];
	    var arrValuesMulti = [];
	    <?php if ($totalGeneralWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("generalwaste"); ?>');
	    <?php } ?>
	    <?php if ($totalPaperWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("paperwaste"); ?>');
	    <?php } ?>
	    <?php if ($totalFoodWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("foodwaste"); ?>');
	    <?php } ?>
	    <?php if ($totalCardboardWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("cardboardwaste"); ?>');
	    <?php } ?>
	    <?php if ($totalPlasticWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("plasticwaste"); ?>');
	    <?php } ?>
	    <?php if ($totalGlassWastePre != 0) { ?>
		arrTitle.push('<?php echo lang("glasswaste"); ?>');
	    <?php } ?>
	    arrTitle.push('<?php echo lang("occupancy") . "-" . ($last_year - 1); ?>');
	    arrTitle.push('<?php echo lang("occupancy") . "-" . ($current_year - 1); ?>');
	    arrValuesMulti.push(arrTitle);
	    <?php
	    $total_months = 0;
	    foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    // Previous year data
		    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
		    $pre_data_generalwaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_general_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_general_waste'] : 0;
		    $pre_data_paperwaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_paper_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_paper_waste'] : 0;
		    $pre_data_foodwaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_food_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_food_waste'] : 0;
		    $pre_data_cardboardwaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_cardboard_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_cardboard_waste'] : 0;
		    $pre_data_plasticwaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_plastic_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_plastic_waste'] : 0;
		    $pre_data_glasswaste = (!empty($utility_waste_chart_pre[$month][$year - 1]['operation_glass_waste'])) ? $utility_waste_chart_pre[$month][$year - 1]['operation_glass_waste'] : 0;
		    $pre_data_occupancy = (!empty($utility_waste_chart_pre[$month][$year - 1]['occupancy'])) ? $utility_waste_chart_pre[$month][$year - 1]['occupancy'] : 0;
		    // Current year data
		    $monthdata = $montharray[$month] . ' ' . $year;
		    $data_generalwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_general_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_general_waste'] : 0;
		    $data_paperwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_paper_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_paper_waste'] : 0;
		    $data_foodwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_food_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_food_waste'] : 0;
		    $data_cardboardwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_cardboard_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_cardboard_waste'] : 0;
		    $data_plasticwaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_plastic_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_plastic_waste'] : 0;
		    $data_glasswaste = (!empty($utility_waste_chart_pre[$month][$year]['operation_glass_waste'])) ? $utility_waste_chart_pre[$month][$year]['operation_glass_waste'] : 0;
		    $data_occupancy = (!empty($utility_waste_chart_pre[$month][$year]['occupancy'])) ? $utility_waste_chart_pre[$month][$year]['occupancy'] : 0;
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
		    // Average Previous year data
		    $av_total_sum_pre_data_generalwaste += $pre_data_generalwaste;
		    $av_total_sum_pre_data_paperwaste += $pre_data_paperwaste;
		    $av_total_sum_pre_data_foodwaste += $pre_data_foodwaste;
		    $av_total_sum_pre_data_cardboardwaste += $pre_data_cardboardwaste;
		    $av_total_sum_pre_data_plasticwaste += $pre_data_plasticwaste;
		    $av_total_sum_pre_data_glasswaste += $pre_data_glasswaste;
		    $av_total_sum_pre_data_occupancy += $pre_data_occupancy;
		    // Average Current year data
		    $av_total_sum_data_generalwaste += $data_generalwaste;
		    $av_total_sum_data_paperwaste += $data_paperwaste;
		    $av_total_sum_data_foodwaste += $data_foodwaste;
		    $av_total_sum_data_cardboardwaste += $data_cardboardwaste;
		    $av_total_sum_data_plasticwaste += $data_plasticwaste;
		    $av_total_sum_data_glasswaste += $data_glasswaste;
		    $av_total_sum_data_occupancy += $data_occupancy;
		    $total_months++;
	    ?>
		    var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
		    var arrValues = ['<?php echo $monthdata; ?>'];
		    <?php if ($totalGeneralWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_generalwaste) && is_finite($pre_data_generalwaste) ? $pre_data_generalwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPaperWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_paperwaste) && is_finite($pre_data_paperwaste) ? $pre_data_paperwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFoodWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_foodwaste) && is_finite($pre_data_foodwaste) ? $pre_data_foodwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCardboardWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_cardboardwaste) && is_finite($pre_data_cardboardwaste) ? $pre_data_cardboardwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPlasticWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_plasticwaste) && is_finite($pre_data_plasticwaste) ? $pre_data_plasticwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalGlassWastePre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_glasswaste) && is_finite($pre_data_glasswaste) ? $pre_data_glasswaste : 0; ?>);
		    <?php } ?>
		    arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
		    arrValuesPre.push(null);
		    var arrValuesNull = [null];
		    <?php if ($totalGeneralWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalPaperWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalFoodWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalCardboardWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalPlasticWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalGlassWastePre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    arrValuesNull.push(null);
		    arrValuesNull.push(null);
		    <?php if ($totalGeneralWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_generalwaste) && is_finite($data_generalwaste) ? $data_generalwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPaperWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_paperwaste) && is_finite($data_paperwaste) ? $data_paperwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFoodWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_foodwaste) && is_finite($data_foodwaste) ? $data_foodwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCardboardWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_cardboardwaste) && is_finite($data_cardboardwaste) ? $data_cardboardwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalPlasticWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_plasticwaste) && is_finite($data_plasticwaste) ? $data_plasticwaste : 0; ?>);
		    <?php } ?>
		    <?php if ($totalGlassWastePre != 0) { ?>
			arrValues.push(<?php echo !empty($data_glasswaste) && is_finite($data_glasswaste) ? $data_glasswaste : 0; ?>);
		    <?php } ?>
		    arrValues.push(null);
		    arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
		    arrValuesMulti.push(arrValuesNull);
		    arrValuesMulti.push(arrValuesPre);
		    arrValuesMulti.push(arrValues);
	    <?php
		}
	    }
	    // Average Previous year data
	    $AV_pre_data_generalwaste = ($av_total_sum_pre_data_generalwaste / $total_months);
	    $AV_pre_data_paperwaste = ($av_total_sum_pre_data_paperwaste / $total_months);
	    $AV_pre_data_foodwaste = ($av_total_sum_pre_data_foodwaste / $total_months);
	    $AV_pre_data_cardboardwaste = ($av_total_sum_pre_data_cardboardwaste / $total_months);
	    $AV_pre_data_plasticwaste = ($av_total_sum_pre_data_plasticwaste / $total_months);
	    $AV_pre_data_glasswaste = ($av_total_sum_pre_data_glasswaste / $total_months);
	    $AV_pre_data_occupancy = ($av_total_sum_pre_data_occupancy / $total_months);
	    // Average Current year data
	    $AV_data_generalwaste = ($av_total_sum_data_generalwaste / $total_months);
	    $AV_data_paperwaste = ($av_total_sum_data_paperwaste / $total_months);
	    $AV_data_foodwaste = ($av_total_sum_data_foodwaste / $total_months);
	    $AV_data_cardboardwaste = ($av_total_sum_data_cardboardwaste / $total_months);
	    $AV_data_plasticwaste = ($av_total_sum_data_plasticwaste / $total_months);
	    $AV_data_glasswaste = ($av_total_sum_data_glasswaste / $total_months);
	    $AV_data_occupancy = ($av_total_sum_data_occupancy / $total_months);
	    $AV_pre_data_occupancy = round($AVG_pre_data_occupancy, 2);
	    $AV_data_occupancy = round($AVG_data_occupancy, 2);
	    $chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
	    $prevYear = $year - 1;
	    ?>
	    var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];
	    <?php if ($totalGeneralWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_generalwaste) && is_finite($AV_pre_data_generalwaste) ? $AV_pre_data_generalwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalPaperWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_paperwaste) && is_finite($AV_pre_data_paperwaste) ? $AV_pre_data_paperwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFoodWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_foodwaste) && is_finite($AV_pre_data_foodwaste) ? $AV_pre_data_foodwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCardboardWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_cardboardwaste) && is_finite($AV_pre_data_cardboardwaste) ? $AV_pre_data_cardboardwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalPlasticWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_plasticwaste) && is_finite($AV_pre_data_plasticwaste) ? $AV_pre_data_plasticwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalGlassWastePre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AV_pre_data_glasswaste) && is_finite($AV_pre_data_glasswaste) ? $AV_pre_data_glasswaste : 0; ?>);
	    <?php } ?>
	    arrAvgPre.push(<?php echo !empty($AV_pre_data_occupancy) && is_finite($AV_pre_data_occupancy) ? $AV_pre_data_occupancy : 0; ?>);
	    arrAvgPre.push(null);
	    var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
	    <?php if ($totalGeneralWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_generalwaste) && is_finite($AV_data_generalwaste) ? $AV_data_generalwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalPaperWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_paperwaste) && is_finite($AV_data_paperwaste) ? $AV_data_paperwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFoodWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_foodwaste) && is_finite($AV_data_foodwaste) ? $AV_data_foodwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCardboardWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_cardboardwaste) && is_finite($AV_data_cardboardwaste) ? $AV_data_cardboardwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalPlasticWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_plasticwaste) && is_finite($AV_data_plasticwaste) ? $AV_data_plasticwaste : 0; ?>);
	    <?php } ?>
	    <?php if ($totalGlassWastePre != 0) { ?>
		arrAvg.push(<?php echo !empty($AV_data_glasswaste) && is_finite($AV_data_glasswaste) ? $AV_data_glasswaste : 0; ?>);
	    <?php } ?>
	    arrAvg.push(null);
	    arrAvg.push(<?php echo !empty($AV_data_occupancy) && is_finite($AV_data_occupancy) ? $AV_data_occupancy : 0; ?>);
	    var arrAvgNull = [null];
	    <?php if ($totalGeneralWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalPaperWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalFoodWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalCardboardWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalPlasticWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalGlassWastePre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    arrAvgNull.push(null);
	    arrAvgNull.push(null);
	    arrValuesMulti.push(arrAvgNull);
	    arrValuesMulti.push(arrAvgPre);
	    arrValuesMulti.push(arrAvg);
	    var data = google.visualization.arrayToDataTable(arrValuesMulti);
	    var options = {
		height: 700,
		isStacked: true,
		title: '<?php echo lang("totalwest"); ?>',
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
			title: '<?php echo lang("kgs"); ?>',
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
			ticks: [0, 50, 100, 150, 200]
		    }
		},
		interpolateNulls: true,
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
		},
		legend: {
		    position: 'top',
		    maxLines: 3,
		    textStyle: {
			fontSize: 20
		    }
		}
	    };
	    var wastePre = new google.visualization.ColumnChart(document.getElementById('utility_waste_chart_pre'));
	    google.visualization.events.addListener(wastePre, 'ready', function() {
		setTimeout(function() {
		    var imgUri = wastePre.getImageURI();
		    document.getElementById('wasteChartPreImg_hidden').value = imgUri;
		}, 1000);
	    });
	    wastePre.draw(data, options);
	<?php } ?>
	<?php
	if (!empty($utility_cost_chart_pre)) {
	    //For colors
	    /* $colorElectricity = ($totalElectricity_utility_cost_pre != 0) ? $chart_legend_colors['Electricity'] : '';
      $colorFuel = ($totalFuel_utility_cost_pre != 0) ? $chart_legend_colors['Fuel'] : '';
      $colorLpg = ($totalLpg_utility_cost_pre != 0) ? $chart_legend_colors['LPG'] : '';
      $colorNaturalGas = ($totalNaturalGas_utility_cost_pre != 0) ? $chart_legend_colors['Natural_Gas'] : '';
      $colorWater = ($totalWater_utility_cost_pre != 0) ? $chart_legend_colors['Water'] : '';
      $colorHeatingDistrict = ($totalHeatingDistrict_utility_cost_pre != 0) ? $chart_legend_colors['District_Heating'] : '';
      $colorCoolingDistrict = ($totalCoolingDistrict_utility_cost_pre != 0) ? $chart_legend_colors['District_Cooling'] : ''; */
	?>
	    var arrTitle = ['Month'];
	    var arrValuesMulti = [];
	    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("electricity"); ?>');
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("fuel"); ?>');
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("lpg"); ?>');
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("natural-gas"); ?>');
	    <?php } ?>
	    <?php if ($totalWater_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("water"); ?>');
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("heating-district"); ?>');
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
		arrTitle.push('<?php echo lang("cooling-district"); ?>');
	    <?php } ?>
	    // arrTitle.push('<?php echo lang("occupancy") . "-" . ($last_year - 1); ?>');
	    // arrTitle.push('<?php echo lang("occupancy") . "-" . ($current_year - 1); ?>');
	    // arrValuesMulti.push(arrTitle);
	    arrTitle.push('<?php echo lang("occupancy") . "-" . ($filters['filters_comparision_chart_pre']["start_year"] - 1); ?>');
	    arrTitle.push('<?php echo lang("occupancy") . "-" . $filters['filters_comparision_chart_pre']["start_year"]; ?>');
	    arrValuesMulti.push(arrTitle);
	    /* var data = google.visualization.arrayToDataTable([
	     ['Month','<?php echo lang("electricity"); ?>', '<?php echo lang("fuel"); ?>','<?php echo lang("lpg"); ?>','<?php echo lang("natural-gas"); ?>','<?php echo lang("water"); ?>','<?php echo lang("heating-district"); ?>','<?php echo lang("cooling-district"); ?>','<?php echo lang("occupancy") . "-" . ($last_year - 1); ?>','<?php echo lang("occupancy") . "-" . ($current_year - 1); ?>'], */
	    <?php
	    $total_months = 0;
	    foreach ($resultkeys_pre as $year => $value) {
		foreach ($value as $key1 => $month) {
		    $prevYear = $year - 1;
		    // Previous year data
		    $pre_monthdata = $montharray[$month] . ' ' . ($prevYear);
		    $pre_data_electricity = (!empty($utility_cost_chart_pre[$month][$prevYear]['electricity'])) ? $utility_cost_chart_pre[$month][$prevYear]['electricity'] : 0;
		    $pre_data_fuel = (!empty($utility_cost_chart_pre[$month][$prevYear]['fuel'])) ? $utility_cost_chart_pre[$month][$prevYear]['fuel'] : 0;
		    $pre_data_lpg = (!empty($utility_cost_chart_pre[$month][$prevYear]['lpg'])) ? $utility_cost_chart_pre[$month][$prevYear]['lpg'] : 0;
		    $pre_data_natural_gas = (!empty($utility_cost_chart_pre[$month][$prevYear]['natural_gas'])) ? $utility_cost_chart_pre[$month][$prevYear]['natural_gas'] : 0;
		    $pre_data_heating_district = (!empty($utility_cost_chart_pre[$month][$prevYear]['heating_district'])) ? $utility_cost_chart_pre[$month][$prevYear]['heating_district'] : 0;
		    $pre_data_cooling_district = (!empty($utility_cost_chart_pre[$month][$prevYear]['cooling_district'])) ? $utility_cost_chart_pre[$month][$prevYear]['cooling_district'] : 0;
		    $pre_data_water = (!empty($utility_cost_chart_pre[$month][$prevYear]['water'])) ? $utility_cost_chart_pre[$month][$prevYear]['water'] : 0;
		    $pre_data_cdd = (!empty($utility_cost_chart_pre[$month][$prevYear]['cdd'])) ? $utility_cost_chart_pre[$month][$prevYear]['cdd'] : 0;
		    $pre_data_hdd = (!empty($utility_cost_chart_pre[$month][$prevYear]['hdd'])) ? $utility_cost_chart_pre[$month][$prevYear]['hdd'] : 0;
		    $pre_data_occupancy = (!empty($utility_cost_chart_pre[$month][$prevYear]['occupancy'])) ? $utility_cost_chart_pre[$month][$prevYear]['occupancy'] : 0;
		    // Current year data
		    $monthdata = $montharray[$month] . ' ' . $year;
		    $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['electricity'])) ? $utility_cost_chart_pre[$month][$year]['electricity'] : 0;
		    $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel'])) ? $utility_cost_chart_pre[$month][$year]['fuel'] : 0;
		    $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg'])) ? $utility_cost_chart_pre[$month][$year]['lpg'] : 0;
		    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas'])) ? $utility_cost_chart_pre[$month][$year]['natural_gas'] : 0;
		    $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district'])) ? $utility_cost_chart_pre[$month][$year]['heating_district'] : 0;
		    $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district'])) ? $utility_cost_chart_pre[$month][$year]['cooling_district'] : 0;
		    $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water'])) ? $utility_cost_chart_pre[$month][$year]['water'] : 0;
		    $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd'])) ? $utility_cost_chart_pre[$month][$year]['cdd'] : 0;
		    $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd'])) ? $utility_cost_chart_pre[$month][$year]['hdd'] : 0;
		    $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy'])) ? $utility_cost_chart_pre[$month][$year]['occupancy'] : 0;
		    // Round values
		    $pre_data_occupancy = round($pre_data_occupancy, 2);
		    $data_occupancy = round($data_occupancy, 2);
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
		    var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
		    var arrValues = ['<?php echo $monthdata; ?>'];
		    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalWater_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_water) && is_finite($pre_data_water) ? $pre_data_water : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
			arrValuesPre.push(<?php echo !empty($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
		    arrValuesPre.push(null);
		    var arrValuesNull = [null];
		    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalWater_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
			arrValuesNull.push(null);
		    <?php } ?>
		    arrValuesNull.push(null);
		    arrValuesNull.push(null);
		    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>);
		    <?php } ?>
		    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>);
		    <?php } ?>
		    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>);
		    <?php } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>);
		    <?php } ?>
		    <?php if ($totalWater_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_water) && is_finite($data_water) ? $data_water : 0; ?>);
		    <?php } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>);
		    <?php } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
			arrValues.push(<?php echo !empty($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>);
		    <?php } ?>
		    arrValues.push(null);
		    arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
		    arrValuesMulti.push(arrValuesNull);
		    arrValuesMulti.push(arrValuesPre);
		    arrValuesMulti.push(arrValues);
		    /* ['<?php echo $pre_monthdata; ?>',<?php echo $pre_data_electricity; ?>,<?php echo $pre_data_fuel; ?>,<?php echo $pre_data_lpg; ?>,<?php echo $pre_data_natural_gas; ?>,<?php echo $pre_data_water; ?>,<?php echo $pre_data_heating_district; ?>,<?php echo $pre_data_cooling_district; ?>,<?php echo $pre_data_occupancy; ?>,null],
		     ['<?php echo $monthdata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,null,<?php echo $data_occupancy; ?>],       */
	    <?php
		}
	    }
	    // Average Previous year data
	    $AVG_pre_data_electricity = ($h_total_sum_pre_data_electricity / $total_months);
	    $AVG_pre_data_fuel = ($h_total_sum_pre_data_fuel / $total_months);
	    $AVG_pre_data_lpg = ($h_total_sum_pre_data_lpg / $total_months);
	    $AVG_pre_data_natural_gas = ($h_total_sum_pre_data_natural_gas / $total_months);
	    $AVG_pre_data_heating_district = ($h_total_sum_pre_data_heating_district / $total_months);
	    $AVG_pre_data_cooling_district = ($h_total_sum_pre_data_cooling_district / $total_months);
	    $AVG_pre_data_water = ($h_total_sum_pre_data_water / $total_months);
	    $AVG_pre_data_cdd = ($h_total_sum_pre_data_cdd / $total_months);
	    $AVG_pre_data_hdd = ($h_total_sum_pre_data_hdd / $total_months);
	    $AVG_pre_data_occupancy = ($h_total_sum_pre_data_occupancy / $total_months);
	    // Average Current year data
	    $AVG_data_electricity = ($h_total_sum_data_electricity / $total_months);
	    $AVG_data_fuel = ($h_total_sum_data_fuel / $total_months);
	    $AVG_data_lpg = ($h_total_sum_data_lpg / $total_months);
	    $AVG_data_natural_gas = ($h_total_sum_data_natural_gas / $total_months);
	    $AVG_data_heating_district = ($h_total_sum_data_heating_district / $total_months);
	    $AVG_data_cooling_district = ($h_total_sum_data_cooling_district / $total_months);
	    $AVG_data_water = ($h_total_sum_data_water / $total_months);
	    $AVG_data_cdd = ($h_total_sum_data_cdd / $total_months);
	    $AVG_data_hdd = ($h_total_sum_data_hdd / $total_months);
	    $AVG_data_occupancy = ($h_total_sum_data_occupancy / $total_months);
	    $AVG_pre_data_occupancy = round($AVG_pre_data_occupancy, 2);
	    $AVG_data_occupancy = round($AVG_data_occupancy, 2);
	    $chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
	    $prevYear = $year - 1;
	    ?>
	    /* data.addRow(['<?php echo ($year - 1) . " " . lang("average"); ?>',<?php echo $AVG_pre_data_electricity; ?>,<?php echo $AVG_pre_data_fuel; ?>,<?php echo $AVG_pre_data_lpg; ?>,<?php echo $AVG_pre_data_natural_gas; ?>,<?php echo $AVG_pre_data_water; ?>,<?php echo $AVG_pre_data_heating_district; ?>,<?php echo $AVG_pre_data_cooling_district; ?>,<?php echo $AVG_pre_data_occupancy; ?>,null]);
	     data.addRow(['<?php echo $year . " " . lang("average"); ?>',<?php echo $AVG_data_electricity; ?>,<?php echo $AVG_data_fuel; ?>,<?php echo $AVG_data_lpg; ?>,<?php echo $AVG_data_natural_gas; ?>,<?php echo $AVG_data_water; ?>,<?php echo $AVG_data_heating_district; ?>,<?php echo $AVG_data_cooling_district; ?>,null,<?php echo $AVG_data_occupancy; ?>]); */
	    var arrAvgPre = ['<?php echo ($prevYear) . " " . lang("average"); ?>'];
	    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_electricity) && is_finite($AVG_pre_data_electricity) ? $AVG_pre_data_electricity : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_fuel) && is_finite($AVG_pre_data_fuel) ? $AVG_pre_data_fuel : 0; ?>);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_lpg) && is_finite($AVG_pre_data_lpg) ? $AVG_pre_data_lpg : 0; ?>);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_natural_gas) && is_finite($AVG_pre_data_natural_gas) ? $AVG_pre_data_natural_gas : 0; ?>);
	    <?php } ?>
	    <?php if ($totalWater_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_water) && is_finite($AVG_pre_data_water) ? $AVG_pre_data_water : 0; ?>);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_heating_district) && is_finite($AVG_pre_data_heating_district) ? $AVG_pre_data_heating_district : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_cooling_district) && is_finite($AVG_pre_data_cooling_district) ? $AVG_pre_data_cooling_district : 0; ?>);
	    <?php } ?>
	    arrAvgPre.push(<?php echo !empty($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy) ? $AVG_pre_data_occupancy : 0; ?>);
	    arrAvgPre.push(null);
	    var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
	    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_electricity) && is_finite($AVG_data_electricity) ? $AVG_data_electricity : 0; ?>);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_fuel) && is_finite($AVG_data_fuel) ? $AVG_data_fuel : 0; ?>);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_lpg) && is_finite($AVG_data_lpg) ? $AVG_data_lpg : 0; ?>);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_natural_gas) && is_finite($AVG_data_natural_gas) ? $AVG_data_natural_gas : 0; ?>);
	    <?php } ?>
	    <?php if ($totalWater_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_water) && is_finite($AVG_data_water) ? $AVG_data_water : 0; ?>);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_heating_district) && is_finite($AVG_data_heating_district) ? $AVG_data_heating_district : 0; ?>);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
		arrAvg.push(<?php echo !empty($AVG_data_cooling_district) && is_finite($AVG_data_cooling_district) ? $AVG_data_cooling_district : 0; ?>);
	    <?php } ?>
	    arrAvg.push(null);
	    arrAvg.push(<?php echo !empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy) ? $AVG_data_occupancy : 0; ?>);
	    var arrAvgNull = [null];
	    <?php if ($totalElectricity_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalWater_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
		arrAvgNull.push(null);
	    <?php } ?>
	    arrAvgNull.push(null);
	    arrAvgNull.push(null);
	    arrValuesMulti.push(arrAvgNull);
	    arrValuesMulti.push(arrAvgPre);
	    arrValuesMulti.push(arrAvg);
	    var data = google.visualization.arrayToDataTable(arrValuesMulti);
	    var options = {
		height: 700,
		isStacked: true,
		title: '<?php echo $isLocal ? lang("utility-cost-chart-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-title") . '(' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 30
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
			title: '<?php echo $isLocal ? lang("utility-cost-chart-yaxis-0-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-yaxis-0-title") . ' (' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
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
		    if ($totalElectricity_utility_cost_pre != 0) {
		    ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorElectricity; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalFuel_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorFuel; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalLpg_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorLpg; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalNaturalGas_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorNaturalGas; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalWater_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorWater; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorHeatingDistrict; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_pre != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorCoolingDistrict; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php echo $i;
		    $i += 1; ?>: {
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
		    maxLines: 3,
		    textStyle: {
			fontSize: 20
		    }
		}
	    };
	    var chart1_1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_pre'));
	    google.visualization.events.addListener(chart1_1, 'ready', function() {
		setTimeout(function() {
		    var imgUri = chart1_1.getImageURI();
		    document.getElementById('columnChartImg_hidden').value = imgUri;
		}, 1000);
	    });
	    chart1_1.draw(data, options);
	<?php } ?>
	<?php if (!empty($waste_pie_report)) { ?>
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
			fontSize: 17
		    }
		},
		chartArea: {
		    width: "100%"
		},
		slices: {
		    0: {
			color: '<?php echo $chart_legend_colors['Generalwaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		    1: {
			color: '<?php echo $chart_legend_colors['Paperwaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		    2: {
			color: '<?php echo $chart_legend_colors['Foodwaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		    3: {
			color: '<?php echo $chart_legend_colors['Cardboardwaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		    4: {
			color: '<?php echo $chart_legend_colors['Plasticwaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		    5: {
			color: '<?php echo $chart_legend_colors['Glasswaste']; ?>',
			textStyle: {
			    fontSize: 18
			}
		    },
		}
	    };
	    var wastePieChart = new google.visualization.PieChart(document.getElementById('waste_pie_chart'));
	    google.visualization.events.addListener(wastePieChart, 'ready', function() {
		setTimeout(function() {
		    var imgUri = wastePieChart.getImageURI();
		    document.getElementById('wastePieChartImg').value = imgUri;
		}, 1000);
	    });
	    wastePieChart.draw(data, options);
	<?php } ?>
	<?php if (!empty($waste_pie_landfill)) { ?>
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
	    var wasteLandfillPieChart = new google.visualization.PieChart(document.getElementById('waste_landfill_pie_chart'));
	    google.visualization.events.addListener(wasteLandfillPieChart, 'ready', function() {
		setTimeout(function() {
		    var imgUri = wasteLandfillPieChart.getImageURI();
		    document.getElementById('wasteLandfillPieChartImg').value = imgUri;
		}, 1000);
	    });
	    wasteLandfillPieChart.draw(data, options);
	<?php } ?>
	<?php /* * ***************************************For pdf only**************************************** */ ?>
	<?php
	if (!empty($kwh_pie_chart_pre)) {
	    //For colors
	    /* $colorElectricity = ($kwh_pie_chart_pre['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';
      $colorFuel = ($kwh_pie_chart_pre['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';
      $colorLpg = ($kwh_pie_chart_pre['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';
      $colorNaturalGas = ($kwh_pie_chart_pre['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';
      $colorHeatingDistrict = ($kwh_pie_chart_pre['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';
      $colorCoolingDistrict = ($kwh_pie_chart_pre['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : '';
      $colorWater = ($kwh_pie_chart_pre['water'] != 0) ? $chart_legend_colors['Water'] : ''; */
	?>
	    var data = google.visualization.arrayToDataTable([
		['Energy', 'Usage'],
		<?php
		foreach ($kwh_pie_chart_pre as $key => $val) {
		    if ($val != 0) {
			echo '["' . lang($key) . '",' . $val . '],';
		    }
		}
		?>
	    ]);
	    var options = {
		height: 600,
		title: '<?php echo lang("kwh-pie-chart-last12month-title") . ' - ' . $filters["report_year_pre"]; ?>',
		sliceVisibilityThreshold: .0,
		pieHole: 0.4,
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 24
		},
		legend: {
		    textStyle: {
			fontSize: 17
		    }
		},
		chartArea: {
		    width: "100%"
		},
		slices: {
		    <?php $i = 0;
		    if ($kwh_pie_chart_pre['electricity'] != 0) {
		    ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorElectricity; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['fuel'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorFuel; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['lpg'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorLpg; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['natural_gas'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorNaturalGas; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['water'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorWater; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['heating_district'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorHeatingDistrict; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($kwh_pie_chart_pre['cooling_district'] != 0) { ?>
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
	    var chart_hidden1 = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_pre'));
	    google.visualization.events.addListener(chart_hidden1, 'ready', function() {
		setTimeout(function() {
		    var imgUri = chart_hidden1.getImageURI();
		    document.getElementById('pieChartImg_hidden').value = imgUri;
		}, 1000);
	    });
	    chart_hidden1.draw(data, options);
	<?php } ?>
	<?php if (!empty($waste_anual_pie_chart)) { ?>
	    var data = google.visualization.arrayToDataTable([
		['Waste', 'Usage'],
		<?php
		foreach ($waste_anual_pie_chart as $key => $val) {
		    if ($val != 0) {
			echo '["' . lang($key) . '",' . $val . '],';
		    }
		}
		?>
	    ]);
	    var options = {
		height: 600,
		title: '<?php echo lang("waste_annual_pie_report") . ' - ' . $filters["report_year_pre"]; ?>',
		sliceVisibilityThreshold: .0,
		pieHole: 0.4,
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 24
		},
		legend: {
		    textStyle: {
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
	    var annualWasteChart = new google.visualization.PieChart(document.getElementById('waste_annual_pie_chart'));
	    google.visualization.events.addListener(annualWasteChart, 'ready', function() {
		setTimeout(function() {
		    var imgUri = annualWasteChart.getImageURI();
		    document.getElementById('pieAnnualChartNewImg_hidden').value = imgUri;
		}, 1000);
	    });
	    annualWasteChart.draw(data, options);
	<?php } ?>
	<?php if (!empty($waste_anual_Landfill_pie_chart)) { ?>
	    var data = google.visualization.arrayToDataTable([
		['Waste', 'Usage'],
		<?php
		foreach ($waste_anual_Landfill_pie_chart as $key => $val) {
		    if ($val != 0) {
			echo '["' . lang($key) . '",' . $val . '],';
		    }
		}
		?>
	    ]);
	    var options = {
		height: 600,
		title: '<?php echo lang("waste_annual_landfill_pie_report") . ' - ' . $filters["report_year_pre"]; ?>',
		sliceVisibilityThreshold: .0,
		pieHole: 0.4,
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 24
		},
		legend: {
		    textStyle: {
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
	    var annualLandfillWasteChart = new google.visualization.PieChart(document.getElementById('waste_annual_landfill_pie_chart'));
	    google.visualization.events.addListener(annualLandfillWasteChart, 'ready', function() {
		setTimeout(function() {
		    var imgUri = annualLandfillWasteChart.getImageURI();
		    document.getElementById('pieAnnualLandfillImg_hidden').value = imgUri;
		}, 1000);
	    });
	    annualLandfillWasteChart.draw(data, options);
	<?php } ?>
	// Cost Pie Chart for current year
	<?php
	if (!empty($cost_pie_chart_pre)) {
	    //For colors
	    /* $colorElectricity = ($cost_pie_chart_pre['electricity'] != 0) ? $chart_legend_colors['Electricity'] : '';
      $colorFuel = ($cost_pie_chart_pre['fuel'] != 0) ? $chart_legend_colors['Fuel'] : '';
      $colorLpg = ($cost_pie_chart_pre['lpg'] != 0) ? $chart_legend_colors['LPG'] : '';
      $colorNaturalGas = ($cost_pie_chart_pre['natural_gas'] != 0) ? $chart_legend_colors['Natural_Gas'] : '';
      $colorHeatingDistrict = ($cost_pie_chart_pre['heating_district'] != 0) ? $chart_legend_colors['District_Heating'] : '';
      $colorCoolingDistrict = ($cost_pie_chart_pre['cooling_district'] != 0) ? $chart_legend_colors['District_Cooling'] : '';
      $colorWater = ($cost_pie_chart_pre['water'] != 0) ? $chart_legend_colors['Water'] : ''; */
	?>
	    var data = google.visualization.arrayToDataTable([
		['Energy', 'Usage'],
		<?php
		foreach ($cost_pie_chart_pre as $key => $val) {
		    if ($val != 0) {
			echo '["' . lang($key) . '",' . $val . '],';
		    }
		}
		?>
	    ]);
	    var options = {
		height: 600,
		title: '<?php echo lang("cost-pie-chart-last12month-title") . ' - ' . $filters["report_year_pre"]; ?>',
		sliceVisibilityThreshold: .0,
		pieHole: 0.4,
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 24
		},
		legend: {
		    textStyle: {
			fontSize: 17
		    }
		},
		chartArea: {
		    width: "100%"
		},
		slices: {
		    <?php $i = 0;
		    if ($cost_pie_chart_pre['electricity'] != 0) {
		    ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorElectricity; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['fuel'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorFuel; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['lpg'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorLpg; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['natural_gas'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorNaturalGas; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['heating_district'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorHeatingDistrict; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['cooling_district'] != 0) { ?>
			<?php echo $i; ?>: {
			    color: '<?php echo $colorCoolingDistrict; ?>',
			    textStyle: {
				fontSize: 18
			    }
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($cost_pie_chart_pre['water'] != 0) { ?>
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
	    var chart_hidden2 = new google.visualization.PieChart(document.getElementById('cost_pie_chart_pre'));
	    google.visualization.events.addListener(chart_hidden2, 'ready', function() {
		setTimeout(function() {
		    var imgUri = chart_hidden2.getImageURI();
		    document.getElementById('pieChartNewImg_hidden').value = imgUri;
		}, 1000);
	    });
	    chart_hidden2.draw(data, options);
	<?php } ?>
	<?php /* * ***************************************For pdf only**************************************** */ ?>

	/*==================Last 5 Year's chanrt*/
	<?php
	if (!empty($utility_cost_chart_5years)) {
	    //For colors
	    /* $colorElectricity = ($totalElectricity_utility_cost_5years != 0) ? $chart_legend_colors['Electricity'] : '';
      $colorFuel = ($totalFuel_utility_cost_5years != 0) ? $chart_legend_colors['Fuel'] : '';
      $colorLpg = ($totalLpg_utility_cost_5years != 0) ? $chart_legend_colors['LPG'] : '';
      $colorNaturalGas = ($totalNaturalGas_utility_cost_5years != 0) ? $chart_legend_colors['Natural_Gas'] : '';
      $colorWater = ($totalWater_utility_cost_5years != 0) ? $chart_legend_colors['Water'] : '';
      $colorHeatingDistrict = ($totalHeatingDistrict_utility_cost_5years != 0) ? $chart_legend_colors['District_Heating'] : '';
      $colorCoolingDistrict = ($totalCoolingDistrict_utility_cost_5years != 0) ? $chart_legend_colors['District_Cooling'] : ''; */
	?>
	    var arrTitle = ['Year'];
	    var arrValuesMulti = [];
	    <?php if ($totalElectricity_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("electricity"); ?>');
	    <?php } ?>
	    <?php if ($totalFuel_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("fuel"); ?>');
	    <?php } ?>
	    <?php if ($totalLpg_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("lpg"); ?>');
	    <?php } ?>
	    <?php if ($totalNaturalGas_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("natural-gas"); ?>');
	    <?php } ?>
	    <?php if ($totalWater_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("water"); ?>');
	    <?php } ?>
	    <?php if ($totalHeatingDistrict_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("heating-district"); ?>');
	    <?php } ?>
	    <?php if ($totalCoolingDistrict_utility_cost_5years != 0) { ?>
		arrTitle.push('<?php echo lang("cooling-district"); ?>');
	    <?php } ?>
	    arrTitle.push('<?php echo lang("occupancy"); ?>');
	    arrValuesMulti.push(arrTitle);
	    <?php
	    foreach ($utility_cost_chart_5years as $year => $value) {
		$yeardata = $year;
		$data_electricity = (!empty($utility_cost_chart_5years[$year]['electricity'])) ? $utility_cost_chart_5years[$year]['electricity'] : 0;
		$data_fuel = (!empty($utility_cost_chart_5years[$year]['fuel'])) ? $utility_cost_chart_5years[$year]['fuel'] : 0;
		$data_lpg = (!empty($utility_cost_chart_5years[$year]['lpg'])) ? $utility_cost_chart_5years[$year]['lpg'] : 0;
		$data_natural_gas = (!empty($utility_cost_chart_5years[$year]['natural_gas'])) ? $utility_cost_chart_5years[$year]['natural_gas'] : 0;
		$data_heating_district = (!empty($utility_cost_chart_5years[$year]['heating_district'])) ? $utility_cost_chart_5years[$year]['heating_district'] : 0;
		$data_cooling_district = (!empty($utility_cost_chart_5years[$year]['cooling_district'])) ? $utility_cost_chart_5years[$year]['cooling_district'] : 0;
		$data_water = (!empty($utility_cost_chart_5years[$year]['water'])) ? $utility_cost_chart_5years[$year]['water'] : 0;
		$data_cdd = (!empty($utility_cost_chart_5years[$year]['cdd'])) ? $utility_cost_chart_5years[$year]['cdd'] : 0;
		$data_hdd = (!empty($utility_cost_chart_5years[$year]['hdd'])) ? $utility_cost_chart_5years[$year]['hdd'] : 0;
		$data_occupancy = (!empty($utility_cost_chart_5years[$year]['occupancy'])) ? $utility_cost_chart_5years[$year]['occupancy'] : 0;
	    ?>
		var arrValuesPre = ['<?php echo $yeardata; ?>'];
		var arrValues = ['<?php echo $monthdata; ?>'];
		<?php if ($totalElectricity_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_electricity; ?>);
		<?php } ?>
		<?php if ($totalFuel_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_fuel; ?>);
		<?php } ?>
		<?php if ($totalLpg_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_lpg; ?>);
		<?php } ?>
		<?php if ($totalNaturalGas_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_natural_gas; ?>);
		<?php } ?>
		<?php if ($totalWater_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_water; ?>);
		<?php } ?>
		<?php if ($totalHeatingDistrict_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_heating_district; ?>);
		<?php } ?>
		<?php if ($totalCoolingDistrict_utility_cost_5years != 0) { ?>
		    arrValuesPre.push(<?php echo $data_cooling_district; ?>);
		<?php } ?>
		arrValuesPre.push(<?php echo $data_occupancy; ?>);
		arrValuesMulti.push(arrValuesPre);
		/* ['<?php echo $yeardata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,<?php echo $data_occupancy; ?>],   */
	    <?php
	    }
	    ?>
	    var data = google.visualization.arrayToDataTable(arrValuesMulti);
	    var options = {
		height: 700,
		isStacked: true,
		title: '<?php echo ($isLocal) ? lang("utility-cost-chart-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-title") . '(' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
		titleTextStyle: {
		    fontName: 'Arial',
		    fontSize: 30
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
			title: '<?php echo $isLocal ? lang("utility-cost-chart-yaxis-0-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-yaxis-0-title") . ' (' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
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
		    if ($totalElectricity_utility_cost_5years != 0) {
		    ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorElectricity; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalFuel_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorFuel; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalLpg_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorLpg; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalNaturalGas_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorNaturalGas; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalWater_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorWater; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalHeatingDistrict_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorHeatingDistrict; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php if ($totalCoolingDistrict_utility_cost_5years != 0) { ?>
			<?php echo $i; ?>: {
			    targetAxisIndex: 0,
			    color: '<?php echo $colorCoolingDistrict; ?>'
			},
		    <?php $i += 1;
		    } ?>
		    <?php echo $i;
		    $i += 1; ?>: {
			targetAxisIndex: 1,
			type: "line",
			pointShape: 'square',
			pointSize: 20
		    },
		    <?php echo $i; ?>: {
			targetAxisIndex: 1,
			type: "line",
			pointShape: 'square',
			pointSize: 20
		    },
		},
		legend: {
		    position: 'top',
		    maxLines: 3,
		    textStyle: {
			fontSize: 20
		    }
		}
	    };
	    var chart1_1_1 = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_5years'));
	    google.visualization.events.addListener(chart1_1_1, 'ready', function() {
		setTimeout(function() {
		    var imgUri1 = chart1_1_1.getImageURI();
		    document.getElementById('columnChartImg_5years_hidden').value = imgUri1;
		}, 1000);
	    });
	    chart1_1_1.draw(data, options);
	<?php } ?>
	/*==================Last 5 Year's chanrt*/
	unblockUI();
    }
    $(window).resize(function() {
	drawChart();
    });
    $(window).load(function() {
	drawPieHighchart();
    });
    function addHighcharts(){
	var filepath="<?php echo site_url(); ?>themes/default/js/highcharts.js";
	if ($('body script[src="' + filepath + '"]').length > 0) {
	    return;
	}
	var highcharts = document.createElement('script');
	highcharts.setAttribute('src','<?php echo site_url(); ?>themes/default/js/highcharts.js');
	highcharts.setAttribute('id','highchartscript');
	$('body').prepend(highcharts);

	var exporting = document.createElement('script');
	exporting.setAttribute('src','<?php echo site_url(); ?>themes/default/js/exporting.js');
	exporting.setAttribute('id','exportingscript');
	$('body').prepend(exporting);

	var exportdata = document.createElement('script');
	exportdata.setAttribute('src','<?php echo site_url(); ?>themes/default/js/export-data.js');
	exportdata.setAttribute('id','exportdatascript');
	$('body').prepend(exportdata);

	var data = document.createElement('script');
	data.setAttribute('src','<?php echo site_url(); ?>themes/default/js/data.js');
	data.setAttribute('id','datascript');
	$('body').prepend(data);
    }
    function removeHighcharts(){
	$("#highchartscript").remove();
	$("#exportingscript").remove();
	$("#exportdatascript").remove();
	$("#datascript").remove();
    }

    function drawPieHighchart(){
	addHighcharts();
	Highcharts.setOptions({
	    lang: {
		thousandsSep: ',',
	    }
	});
	<?php
	    if (!empty($kwh_pie_chart)) {
		foreach ($kwh_pie_chart as $kwhPieYtdKey => $kwhPieYtdVal) {
		    if ($kwhPieYtdVal != 0) {
			$kwhPieYtdName = lang($kwhPieYtdKey);
			$kwhPieYtdSeriesArray[$kwhPieYtdName] .= $kwhPieYtdVal;
		    }
		}
	    ?>
	    var kwhPieYtd = '<?php echo json_encode($kwhPieYtdSeriesArray); ?>';
	    var kwhPieYtdData = JSON.parse(kwhPieYtd);
	    var kwhPieYtdArray = [];
	    if(kwhPieYtdData != null && kwhPieYtdData != undefined && typeof (kwhPieYtdData) == 'object') {
			Object.entries(kwhPieYtdData).forEach(([key, value]) => {
				if(key == 'Electricity'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorElectricity; ?>',
					},);
				}
				if(key == 'Fuel'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorFuel; ?>'
					},);
				}
				if(key == 'Water'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorWater; ?>'
					},);
				}
				if(key == 'LPG'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorLpg; ?>'
					},);
				}
				if(key == 'Natural Gas'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorNaturalGas; ?>'
					},);
				}
				if(key == 'District Heating'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorHeatingDistrict; ?>'
					},);
				}
				if(key == 'District Cooling'){
					kwhPieYtdArray.push({
						name: key,
						y: Number(kwhPieYtdData[key]),
						color: '<?php echo $colorCoolingDistrict; ?>'
					},);
				}
			});
	    }
			Highcharts.chart('kwh_pie_chart', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: '<?php echo lang("kwh-pie-chart-title"); ?>',
					style: {
			color: Highcharts.getOptions().colors[1],
			fontFamily: 'Arial',
			fontSize: '24px',
						fontWeight: 'bold',
		    }
				},
				credits: {
					enabled: false
				},
				tooltip: {
					pointFormat: '<b>{point.y:,.f} ({point.percentage:.1f}%)</b>'
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				legend: {
					x : 0,
					y : 80,
					align: 'right',
					verticalAlign: 'top',
					layout: 'vertical'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						size: 100,
						align: 'right',
						showInLegend: true,
						dataLabels: {
							enabled: true,
							format: '<br>{point.percentage:.1f} %',
							style: {
								fontSize: '15px',
							},
							distance: -50,
							filter: {
								property: 'percentage',
								operator: '>',
								value: 4
							}
						}
					}
				},
				series: [{
					colorByPoint: true,
					size: '100%',
			innerSize: '40%',
					data: kwhPieYtdArray
				}]
			});
		<?php } ?>
		<?php
		if (!empty($cost_pie_chart)) {
		foreach ($cost_pie_chart as $costPieYtdKey => $costPieYtdVal) {
		    if ($costPieYtdVal != 0) {
			$costPieYtdName = lang($costPieYtdKey);
						$costPieYtdSeriesArray[$costPieYtdName] .= $costPieYtdVal;
		    }
		}
	    ?>
			var costPieYtd = '<?php echo json_encode($costPieYtdSeriesArray); ?>';
			var costPieYtdData = JSON.parse(costPieYtd);
			var costPieYtdArray = [];
	    if(costPieYtdData != null && costPieYtdData != undefined && typeof (costPieYtdData) == 'object') {
			Object.entries(costPieYtdData).forEach(([key, value]) => {
				if(key == 'Electricity'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorElectricity; ?>',
					},);
				}
				if(key == 'Fuel'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorFuel; ?>'
					},);
				}
				if(key == 'Water'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorWater; ?>'
					},);
				}
				if(key == 'LPG'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorLpg; ?>'
					},);
				}
				if(key == 'Natural Gas'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorNaturalGas; ?>'
					},);
				}
				if(key == 'District Heating'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorHeatingDistrict; ?>'
					},);
				}
				if(key == 'District Cooling'){
					costPieYtdArray.push({
						name: key,
						y: Number(costPieYtdData[key]),
						color: '<?php echo $colorCoolingDistrict; ?>'
					},);
				}
			});
	    }
			Highcharts.chart('cost_pie_chart', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: '<?php echo lang("cost-pie-chart-title"); ?>',
					style: {
			color: Highcharts.getOptions().colors[1],
			fontFamily: 'Arial',
			fontSize: '24px',
						fontWeight: 'bold',
		    }
				},
				credits: {
					enabled: false
				},
				tooltip: {
					pointFormat: '<b>{point.y:,.f} ({point.percentage:.1f}%)</b>'
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				legend: {
					x : 0,
					y : 80,
					align: 'right',
					verticalAlign: 'top',
					layout: 'vertical'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						size: 100,
						align: 'right',
						showInLegend: true,
						dataLabels: {
							enabled: true,
							format: '<br>{point.percentage:.1f} %',
							style: {
								fontSize: '15px',
							},
							distance: -50,
							filter: {
								property: 'percentage',
								operator: '>',
								value: 4
							}
						}
					}
				},
				series: [{
					colorByPoint: true,
					size: '100%',
			innerSize: '40%',
					data: costPieYtdArray
				}]
			});
		<?php } ?>
		<?php
		if (!empty($kwh_pie_chart_previousmonth)) {
				foreach ($kwh_pie_chart_previousmonth as $kwhPiePrekey => $kwhPiePreValue) {
					if ($kwhPiePreValue != 0) {
						$kwhPiePreName = lang($kwhPiePrekey);
						$kwhPiePreSeriesArray[$kwhPiePreName] .= $kwhPiePreValue;
					}
				}
			?>
			var kwhPieChartPreviousMonth = '<?php echo json_encode($kwhPiePreSeriesArray); ?>';
			var kwhPieChartPreviousMonthData = JSON.parse(kwhPieChartPreviousMonth);
			var kwhPieChartPreviousMonthArray = [];
	    if(kwhPieChartPreviousMonthData != null && kwhPieChartPreviousMonthData != undefined && typeof (kwhPieChartPreviousMonthData) == 'object') {
			Object.entries(kwhPieChartPreviousMonthData).forEach(([key, value]) => {
				if(key == 'Electricity'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorElectricity; ?>',
					},);
				}
				if(key == 'Fuel'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorFuel; ?>'
					},);
				}
				if(key == 'Water'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorWater; ?>'
					},);
				}
				if(key == 'LPG'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorLpg; ?>'
					},);
				}
				if(key == 'Natural Gas'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorNaturalGas; ?>'
					},);
				}
				if(key == 'District Heating'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorHeatingDistrict; ?>'
					},);
				}
				if(key == 'District Cooling'){
					kwhPieChartPreviousMonthArray.push({
						name: key,
						y: Number(kwhPieChartPreviousMonthData[key]),
						color: '<?php echo $colorCoolingDistrict; ?>'
					},);
				}
			});
	    }
			Highcharts.chart('kwh_pie_chart_previousmonth', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: '<?php echo lang("kwh-pie-chart-last12month-title-monthly") . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?>',
					style: {
			color: Highcharts.getOptions().colors[1],
			fontFamily: 'Arial',
			fontSize: '24px',
						fontWeight: 'bold',
		    }
				},
				credits: {
					enabled: false
				},
				tooltip: {
					pointFormat: '<b>{point.y:,.f} ({point.percentage:.1f}%)</b>'
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				legend: {
					x : 0,
					y : 50,
					align: 'right',
					verticalAlign: 'top',
					layout: 'vertical'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						size: 100,
						align: 'right',
						showInLegend: true,
						dataLabels: {
							enabled: true,
							format: '<br>{point.percentage:.1f} %',
							style: {
								fontSize: '15px',
							},
							distance: -50,
							filter: {
								property: 'percentage',
								operator: '>',
								value: 4
							}
						}
					}
				},
				series: [{
					colorByPoint: true,
					size: '100%',
			innerSize: '40%',
					data: kwhPieChartPreviousMonthArray
				}]
			});
		<?php  } ?>
		<?php
			if (!empty($cost_pie_chart_previousmonth)) {
				foreach ($cost_pie_chart_previousmonth as $costPiePreKey => $costPiePreVal) {
					if ($costPiePreVal != 0) {
						$costPiePreName = lang($costPiePreKey);
						$costPiePreSeriesArray[$costPiePreName] .= $costPiePreVal;
					}
				}
				?>
				var costPieChartPreviousMonth = '<?php echo json_encode($costPiePreSeriesArray); ?>';
				var costPieChartPreviousMonthData = JSON.parse(costPieChartPreviousMonth);
				var costPieChartPreviousMonthArray = [];
		if(costPieChartPreviousMonthData != null && costPieChartPreviousMonthData != undefined && typeof (costPieChartPreviousMonthData) == 'object') {
				Object.entries(costPieChartPreviousMonthData).forEach(([key, value]) => {
					if(key == 'Electricity'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorElectricity; ?>'
						},);
					}
					if(key == 'Fuel'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorFuel; ?>'
						},);
					}
					if(key == 'Water'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorWater; ?>'
						},);
					}
					if(key == 'LPG'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorLpg; ?>'
						},);
					}
					if(key == 'Natural Gas'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorNaturalGas; ?>'
						},);
					}
					if(key == 'District Heating'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorHeatingDistrict; ?>'
						},);
					}
					if(key == 'District Cooling'){
						costPieChartPreviousMonthArray.push({
							name: key,
							y: Number(costPieChartPreviousMonthData[key]),
							color: '<?php echo $colorCoolingDistrict; ?>'
						},);
					}
				});
		}
			Highcharts.chart('cost_pie_chart_previousmonth', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: '<?php echo lang("cost-pie-chart-last12month-title") . ' - ' . $fullmontharray[$filters["previous_month"]] . ' ' . $filters["previous_year"]; ?>',
					style: {
			color: Highcharts.getOptions().colors[1],
			fontFamily: 'Arial',
			fontSize: '24px',
						fontWeight: 'bold',
		    }
				},
				credits: {
					enabled: false
				},
				tooltip: {
					pointFormat: '<b>{point.y:,.f} ({point.percentage:.1f}%)</b>',
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				legend: {
					x : 0,
					y : 50,
					align: 'right',
					verticalAlign: 'top',
					layout: 'vertical'
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						size: 100,
						align: 'right',
						showInLegend: true,
						dataLabels: {
							enabled: true,
							format: '<br>{point.percentage:.1f} %',
							style: {
								fontSize: '15px',
							},
							distance: -50,
							filter: {
								property: 'percentage',
								operator: '>',
								value: 4
							}
						}
					}
				},
				series: [{
		    colorByPoint: true,
					size: '100%',
			innerSize: '40%',
		    data: costPieChartPreviousMonthArray
		}]
	    });
	<?php } ?>
	removeHighcharts();
    }
</script>
<div id="ajax_table" class="report-detail">
    <article class="card">
	<div class="article-header">
	    <div class="row">
		<div class="col-sm-6">
		    <?php echo lang('site_total_utilities_reports'); ?>
		</div>
		<div class="col-sm-6 pull-right">
		    <form name="currency_form" method="post">
			<button type="submit" class="btn btn-secondary btn-submit pull-right <?php echo ($currency == "base") ? "btn-active" : ""; ?>" id="base" name="currency" value="base">Base Currency</button>
			<button type="submit" class="btn btn-secondary btn-submit pull-right <?php echo ($currency == "local") ? "btn-active" : ""; ?>" id="local" name="currency" value="local" style="margin: 0px 5px;">Local Currency</button>
		    </form>
		</div>
	    </div>
	</div>
	<div>
	    <form name="report_form" id="report_form" enctype="multipart/form-data" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports">
		<input type="hidden" name="view_type" id="view_type" value="pdf">
		<input type="hidden" name="currency" id="currency" value="<?php echo $currency; ?>">
		<input type="hidden" name="columnChartImg" id="columnChartImg" value="">
		<input type="hidden" name="columnChartCarbonFootprintImg" id="columnChartCarbonFootprintImg" value="">
		<input type="hidden" name="chsb_report_chart_1" id="chsb_report_chart_1" value="">
		<input type="hidden" name="chsb_report_chart_2" id="chsb_report_chart_2" value="">
		<input type="hidden" name="chsb_report_chart_3" id="chsb_report_chart_3" value="">
		<input type="hidden" name="chsb_report_chart_4" id="chsb_report_chart_4" value="">
		<input type="hidden" name="chsb_report_chart_5" id="chsb_report_chart_5" value="">
		<input type="hidden" name="chsb_report_chart_6" id="chsb_report_chart_6" value="">
		<input type="hidden" name="chsb_report_chart_7" id="chsb_report_chart_7" value="">
		<input type="hidden" name="chsb_report_chart_8" id="chsb_report_chart_8" value="">
		<input type="hidden" name="columnChartCarbonFootprintMonthlyImg" id="columnChartCarbonFootprintMonthlyImg" value="">
		<input type="hidden" name="columnChartCarbonFootprintAnnualImg" id="columnChartCarbonFootprintAnnualImg" value="">
		<input type="hidden" name="columnChartImg_hidden" id="columnChartImg_hidden" value="">
		<input type="hidden" name="columnChartImg_5years_hidden" id="columnChartImg_5years_hidden" value="">
		<input type="hidden" name="columnChartImg_monthly" id="columnChartImg_monthly" value="">
		<input type="hidden" name="pieChartImg" id="pieChartImg" value="">
		<input type="hidden" name="pieChartNewImg" id="pieChartNewImg" value="">
		<input type="hidden" name="pieChartImg_hidden" id="pieChartImg_hidden" value="">
		<input type="hidden" name="pieChartNewImg_hidden" id="pieChartNewImg_hidden" value="">
		<input type="hidden" name="pieChartNew2Img" id="pieChartNew2Img" value="">
		<input type="hidden" name="pieChartNew3Img" id="pieChartNew3Img" value="">
		<input type="hidden" name="monthly_report_month" id="monthly_report_month" value="">
		<input type="hidden" name="monthly_report_year" id="monthly_report_year" value="">
		<input type="hidden" name="yearly_report_year" id="yearly_report_year" value="">
		<input type="hidden" name="wasteChartImg" id="wasteChartImg" value="">
		<input type="hidden" name="wastePieChartImg" id="wastePieChartImg" value="">
		<input type="hidden" name="wasteLandfillPieChartImg" id="wasteLandfillPieChartImg" value="">
		<input type="hidden" name="pieAnnualChartNewImg_hidden" id="pieAnnualChartNewImg_hidden" value="">
		<input type="hidden" name="pieAnnualLandfillImg_hidden" id="pieAnnualLandfillImg_hidden" value="">
		<input type="hidden" name="wasteMonthlyChartImg" id="wasteMonthlyChartImg" value="">
		<input type="hidden" name="wastePieMonthlyChartImg" id="wastePieMonthlyChartImg" value="">
		<input type="hidden" name="wastePieLandfillMonthlyChartImg" id="wastePieLandfillMonthlyChartImg" value="">
		<input type="hidden" name="wasteChartPreImg_hidden" id="wasteChartPreImg_hidden" value="">
		<input type="hidden" name="pieChartImgkwhMonthly" id="pieChartImgkwhMonthly" value="">
		<input type="hidden" name="pieChartImgcostMonthly" id="pieChartImgcostMonthly" value="">
		<?php
		/* $CI = & get_instance();
	      $id = $CI->session->userdata['admin']['site_id']; */
		/*$attributes = array('name' => 'report_form', 'id' => 'report_form', 'enctype' => 'multipart/form-data');
	    echo form_open('reports', $attributes);
	    echo form_hidden('view_type', 'pdf', 'view_type');
	    echo form_hidden('currency', $currency, '<?php echo $currency; ?>');
	    echo form_hidden('columnChartImg', '', 'columnChartImg');
	    echo form_hidden('columnChartCarbonFootprintImg', '', 'columnChartCarbonFootprintImg');
	    echo form_hidden('columnChartCarbonFootprintMonthlyImg', '', 'columnChartCarbonFootprintMonthlyImg');
	    echo form_hidden('columnChartCarbonFootprintAnnualImg', '', 'columnChartCarbonFootprintAnnualImg');
	    echo form_hidden('columnChartImg_hidden', '', 'columnChartImg_hidden');
	    echo form_hidden('columnChartImg_5years_hidden', '', 'columnChartImg_5years_hidden');
	    echo form_hidden('pieChartImg', '', 'pieChartImg');
	    echo form_hidden('pieChartNewImg', '', 'pieChartNewImg');
	    echo form_hidden('pieChartImg_hidden', '', 'pieChartImg_hidden');
	    echo form_hidden('pieChartNewImg_hidden', '', 'pieChartNewImg_hidden');
	    echo form_hidden('pieChartNew2Img', '', 'pieChartNew2Img');
	    echo form_hidden('pieChartNew3Img', '', 'pieChartNew3Img');
	    echo form_hidden('monthly_report_month', '', 'monthly_report_month');
	    echo form_hidden('monthly_report_year', '', 'monthly_report_year');
	    echo form_hidden('wasteChartImg', '', 'wasteChartImg');
	    echo form_hidden('wastePieChartImg', '', 'wastePieChartImg');
	    echo form_hidden('wasteLandfillPieChartImg', '', 'wasteLandfillPieChartImg');
	    echo form_hidden('pieAnnualChartNewImg_hidden', '', 'pieAnnualChartNewImg_hidden');
	    echo form_hidden('pieAnnualLandfillImg_hidden', '', 'pieAnnualLandfillImg_hidden');
	    echo form_hidden('wasteMonthlyChartImg', '', 'wasteMonthlyChartImg');
	    echo form_hidden('wastePieMonthlyChartImg', '', 'wastePieMonthlyChartImg');
	    echo form_hidden('wastePieLandfillMonthlyChartImg', '', 'wastePieLandfillMonthlyChartImg');
	    echo form_hidden('wasteChartPreImg_hidden', '', 'wasteChartPreImg_hidden');
	    echo form_hidden('pieChartImgkwhMonthly', '', 'pieChartImgkwhMonthly');
	    echo form_hidden('pieChartImgcostMonthly', '', 'pieChartImgcostMonthly');*/
		?>
		<img id="columnChartImg" style="display:none;" />
		<img id="columnChartCarbonFootprintImg" style="display:none;" />
		<img id="chsb_report_chart_1" style="display:none;" />
		<img id="chsb_report_chart_2" style="display:none;" />
		<img id="chsb_report_chart_3" style="display:none;" />
		<img id="chsb_report_chart_4" style="display:none;" />
		<img id="chsb_report_chart_5" style="display:none;" />
		<img id="chsb_report_chart_6" style="display:none;" />
		<img id="chsb_report_chart_7" style="display:none;" />
		<img id="chsb_report_chart_8" style="display:none;" />
		<img id="pieChartImg" style="display:none;" />
		<img id="pieChartNewImg" style="display:none;" />
		<img id="pieChartNew2Img" style="display:none;" />
		<img id="pieChartNew3Img" style="display:none;" />
		<?php if ($cview == 'index') { ?>
		    <div class="form-btn-outer col-sm-12" style="float: left;">
			<!-- <a class="btn btn-secondary btn-submit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/roomnight">Utilities Cost/Room Night</a>
		    <a class="btn btn-secondary btn-submit" href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/budget">Utilities Cost v/s Budget</a> -->
			<div class="col-md-4 col-sm-6 col-xs-12 pull-right">
			    <label class="control-label col-sm-4">
				Choose year :
			    </label>
			    <div class="form-dropdown col-sm-8">
				<select name="utility_year_select_year" data-type="custom-dropdown" id="utility_year_select_year">
				    <?php
				    for ($i = date('Y') - 8; $i <= date('Y'); $i++) { ?>
					<option value="<?php echo $i; ?>" <?php echo ($i == $utility_year_selected) ? "selected" : ""; ?>><?php echo $i; ?></option>
				    <?php }
				    ?>
				</select>
			    </div>
			</div>
		    </div>
		<?php } ?>
		<?php if ($cview == 'index') { ?>
		    <div class="form-btn-outer" style="float: right;">
			<button type="submit" class="btn btn-secondary btn-submit" id="monthly_report_popup_btn" name="submit" value="download_monthly_hidden">Monthly Report</button>
			<button type="submit" class="btn btn-secondary btn-submit <?php echo ($utility_year_selected != date('Y')) ? "disabled" : ""; ?>" id="ytd_report_popup_btn" name="submit" value="download">YTD Report</button>
			<button type="submit" class="btn btn-secondary btn-submit" id="annual_report_popup_btn" name="submit" value="download_hidden">Annual Report</button>
			<button type="submit" class="btn btn-secondary btn-submit" id="5year_report_popup_btn" name="submit" value="download_5years_hidden">5 Years Report</button>
			<button id="monthly_report_popup_submit" name="submit" value="download_monthly_hidden" style="opacity: 0;height: 0px;width: 0px;display: none;"></button>
			<a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/prepare_budget" class="btn btn-secondary btn-submit" id="prepare_budget_btn" name="submit" style="margin: 5px 0 0 0;">Budget Preparation</a>
			<!-- Carbon Footprint -->
			<!-- <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/carbon_footprint" style="margin: 5px 0 0 0;" class="btn btn-secondary btn-submit" id="carbon_footprint_btn" name="submit">Carbon Footprint</a> -->
			<!-- Carbon Footprint -->
			<button type="submit" class="btn btn-secondary btn-submit" id="ytd_report_popup_btn_hidden" name="submit" value="download" style="display: none;">YTD Report</button>
			<button type="submit" class="btn btn-secondary btn-submit" id="annual_report_popup_btn_hidden" name="submit" value="download_hidden" style="display: none;">Annual Report</button>
			<button type="submit" class="btn btn-secondary btn-submit" id="5year_report_popup_btn_hidden" name="submit" value="download_5years_hidden" style="display: none;">5 Years Report</button>
		    </div>
		<?php } ?>
		<?php //echo form_close();
		?>
	    </form>
	    <?php if ($cview == 'index') { ?>
		<form name="report_excel_form" id="report_excel_form" method="post">
		    <input type="hidden" name="view_type" value="excel" />
		    <div class="form-btn-outer" style="float: left;">
			<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="submit" value="download_excel_index">Generate Excel</button>
		    </div>
		</form>
	    <?php } ?>
	    <?php if ($cview == 'index') { ?>
		<div class="form-btn-outer" style="float: right;" id="saveImage">
		</div>
	    <?php } ?>
	    <?php if ($cview == 'index') { ?>
		<form name="utility_year_selected_form" id="utility_year_selected_form" method="post">
		    <input type="hidden" name="utility_year_selected" value="<?php echo $utility_year_selected; ?>" />
		</form>
	    <?php } ?>
	</div>
	<div style="clear: both;"></div>
	<div class="card-wrap">
	    <div class="row">
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div id="utility_cost_chart" style="height:700px;">
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
			    <div id="chart_div_1" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_2" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_3" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_4" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_5" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_6" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_7" style="height:0px;opacity:0;"></div>
			    <div id="chart_div_8" style="height:0px;opacity:0;"></div>
			    <div id="wasteChart" style="height:0px;opacity:0;"></div>
			    <div id="wasteMonthlyChart" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart_carbon_footprint_monthly" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart_carbon_footprint_annual" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart_pre" style="height:0px;opacity:0;"></div>
			    <div id="utility_waste_chart_pre" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart_5years" style="height:0px;opacity:0;"></div>
			    <div id="utility_cost_chart_monthly" style="height:0px;opacity:0;">
				<!-- </div> -->
			    </div>
			</div>
		    </div>
		</div>
		<br />
		<div id="utility_cost_chart_budget_div" style="height:700px;opacity:1;"></div>
		<br />
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div class="col-sm-6">
				<div id="kwh_pie_chart">
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
				<div id="kwh_pie_chart_pre" style="height:0px;opacity:0;"></div>
				<form method="post">
				    <input type="hidden" name="view_type" value="excel" />
				    <div class="form-btn-outer" style="text-align:center;">
					<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="submit" value="download_excel_index_kwh_pie_chart">Generate Excel</button>
				    </div>
				</form>
			    </div>
			    <div class="col-sm-6">
				<div id="cost_pie_chart">
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
				<div id="cost_pie_chart_pre" style="height:0px;opacity:0;"></div>
				<form method="post">
				    <input type="hidden" name="view_type" value="excel" />
				    <div class="form-btn-outer" style="text-align:center;">
					<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="submit" value="download_excel_index_cost_pie_chart">Generate Excel</button>
				    </div>
				</form>
			    </div>
			</div>
		    </div>
		</div>
		<br />
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div class="col-sm-6">
				<div id="waste_pie_chart" style="height:0px;opacity:0;"></div>
				<div id="waste_pie_monthly_chart" style="height:0px;opacity:0;"></div>
				<div id="waste_annual_pie_chart" style="height:0px;opacity:0;"></div>
				<div id="kwh_pie_chart_monthly" style="height:0px;opacity:0;"></div>
				<div id="cost_pie_chart_monthly" style="height:0px;opacity:0;"></div>
			    </div>
			    <div class="col-sm-6">
				<div id="waste_landfill_pie_chart" style="height:0px;opacity:0;"></div>
				<div id="waste_pie_landfill_monthly_chart" style="height:0px;opacity:0;"></div>
				<div id="waste_annual_landfill_pie_chart" style="height:0px;opacity:0;"></div>
				<div id="cost_pie_chart_monthly_chart" style="height:0px;opacity:0;"></div>
				<div id="kwh_pie_chart_monthly_chart" style="height:0px;opacity:0;"></div>
			    </div>
			</div>
		    </div>
		</div>
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div class="col-sm-6">
				<div id="kwh_pie_chart_previousmonth">
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
				<form method="post">
				    <input type="hidden" name="view_type" value="excel" />
				    <div class="form-btn-outer" style="text-align:center;">
					<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="submit" value="download_excel_index_kwh_pie_chart_previousmonth">Generate Excel</button>
				    </div>
				</form>
			    </div>
			    <div class="col-sm-6">
				<div id="cost_pie_chart_previousmonth">
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
				<form method="post">
				    <input type="hidden" name="view_type" value="excel" />
				    <div class="form-btn-outer" style="text-align:center;">
					<button type="submit" class="btn btn-secondary btn-submit" id="mysubmit" name="submit" value="download_excel_index_cost_pie_chart_previousmonth">Generate Excel</button>
				    </div>
				</form>
			    </div>
			</div>
		    </div>
		</div>
		<br />
		<div id="utility_cost_chart_roomnight_div" style="height:700px;opacity:1;"></div>
		<br />
		<div id="utility_cost_chart_carbon_footprint_div" style="height:700px;opacity:1;"></div>
	    </div>
	    <!-- </div> -->
    </article>
</div>
<div id="yearly_report_popup" style="display:none;">
    <form id="yearly_file_form" method="post" action="">
	<div style="padding: 15px 0px 15px 0;">
	    <label for="commentbox" class="main-label">Select Year</label>
	    <input type="text" id="YearFormat" class='Default' value="<?php echo (!empty($utilities_year)) ? $utilities_year : ''; ?>">
	</div>
    </form>
</div>
<div id="monthly_report_popup" style="display:none;">
    <form id="file_form" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/add_project_todo_file' ?>">
	<div style="padding: 15px 0px 15px 0;">
	    <label for="commentbox" class="main-label">Select Month</label>
	    <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
	</div>
    </form>
</div>
<div id="pdf_excel_report_popup" style="display:none;">
    <div style="padding: 15px 0px 15px 0;">
	<button type="button" class="btn btn-secondary btn-submit" id="pdf" name="btn_pdf" value="pdf">Generate Pdf</button>
	<button type="button" class="btn btn-secondary btn-submit" id="excel" name="btn_pdf" value="excel">Generate Excel</button>
    </div>
</div>
<script>
    $(document).ready(function() {
	getReportsOnMain();
	$("#ytd_report_popup_btn, #annual_report_popup_btn, #monthly_report_popup_btn").click(function(event) {
	    event.preventDefault();
	    if (!$(this).hasClass('disabled')) {
		$("#pdf").val($(this).val());
		$("#excel").val($(this).val());
		$.blockUI({
		    css: {
			cursor: 'default',
			'top': '20%'
		    },
		    blockMsgClass: 'formblockui',
		    overlayCSS: {
			cursor: 'default',
			'border-radius': '10px'
		    },
		    message: $('#pdf_excel_report_popup'),
		    onUnblock: function() {
		    }
		});
		$('.blockOverlay').click($.unblockUI);
	    }
	});
	$("#utility_year_select_year").change(function() {
	    $('input[name=utility_year_selected]').val($(this).val());
	    if ((new Date()).getFullYear() == $(this).val()) {
		$("#ytd_report_popup_btn, #annual_report_popup_btn, #monthly_report_popup_btn").addClass('disbled');
	    }
	    getReportsOnMain();
	    $('#utility_year_selected_form').submit();
	})
	$("#5year_report_popup_btn").click(function(event) {
	    event.preventDefault();
	    $('#view_type').val('pdf');
	    $("#5year_report_popup_btn_hidden").trigger('click');
	});
	$('#pdf, #excel').on('click', function() {
	    var val = $(this).val();
	    var id = this.id;
	    if (id == 'excel') {
		$('#view_type').val('excel');
	    } else if (id == 'pdf') {
		$('#view_type').val('pdf');
	    }
	    if (val == 'download') {
		$('#ytd_report_popup_btn_hidden').trigger('click');
		unblockUI();
	    } else if (val == 'download_hidden') {
		$.blockUI({
		    css: {
			cursor: 'default',
			'top': '20%'
		    },
		    blockMsgClass: 'formblockui',
		    overlayCSS: {
			cursor: 'default',
			'border-radius': '10px'
		    },
		    message: $('#yearly_report_popup'),
		    onUnblock: function() {}
		});
		$('.blockOverlay').click($.unblockUI);
	    } else if (val == 'download_monthly_hidden') {
		$.blockUI({
		    css: {
			cursor: 'default',
			'top': '20%'
		    },
		    blockMsgClass: 'formblockui',
		    overlayCSS: {
			cursor: 'default',
			'border-radius': '10px'
		    },
		    message: $('#monthly_report_popup'),
		    onUnblock: function() {}
		});
		$('.blockOverlay').click($.unblockUI);
	    }
	});
	$(function() {
	    var currentYear = (new Date()).getFullYear();
	    $('#YearFormat').attr('readonly', 'readonly');
	    $('#YearFormat').datepicker({
		format: " yyyy",
		viewMode: "years",
		minViewMode: "years",
		beforeShowYear: function(date) {
		    var year = date.getFullYear();
		    var disableClass = '';
		    if (year >= currentYear) {
			disableClass = 'disable-year';
		    }
		    return disableClass;
		},
		endDate: new Date(),
		autoclose: true
	    }).on('changeDate', function(ev) {
		var fullYear = ev.date.getFullYear();
		$('#yearly_report_year').val(fullYear);
		var viewType = $('#view_type').val();
		$.ajax({
		    type: 'POST',
		    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/',
		    data: {
			<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',
			view_type: viewType,
			yearly_report_year: fullYear,
			monthly_report_year: fullYear,
			monthly_report_month: 0,
			currency: '<?php echo $currency; ?>',
			submit: 'download_hidden'
		    },
		    success: function(response) {
			removeHighcharts();
			$('#ajax_table').html(response);
			setTimeout(function() {
			    $('#yearly_report_year').val(fullYear);
			    $('#monthly_report_year').val(fullYear);
			    $('#monthly_report_month').val(0);
			    $('#view_type').val(viewType);
			    $('#annual_report_popup_btn_hidden').trigger('click');
			}, 3000);
		    }
		});
	    });
	});
	var monthPickerObj = $("#MonthFormat").MonthPicker({
	    'OnAfterChooseMonth': function(date) {
		var month = date.getMonth() + 1;
		var year = date.getFullYear();
		$("#monthly_report_month").val(month);
		$("#monthly_report_year").val(year);
		// Update monthly chart for get seleted month chart image in PDF
		$.ajax({
		    type: 'POST',
		    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/getMonthlyReportChart',
		    data: {
			<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',
			view_type: 'ajax',
			monthly_report_month: month,
			monthly_report_year: year,
			currency: '<?php echo $currency; ?>',
			submit: 'download_monthly_hidden'
		    },
		    dataType: 'json',
		    success: function(response) {
			$.unblockUI();
			if (response.chsb_measures_chart_data) {
			    google.setOnLoadCallback(drawChart);
			    google.charts.load("visualization", "1", {
				packages: ["corechart"]
			    });
			    for (let i = 1; i <= 8; i++) {
				google.charts.setOnLoadCallback(function() {
				    drawVisualization(i, response.chsb_measures_chart_data.measures, response.chsb_measures_chart_data.measure_readings)
				});
			    }
			}
			var data = google.visualization.arrayToDataTable(response.chart_data);
			var series = {};
			var i = 0;
			$.each(response.chart_index, function(index, value) {
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
			});
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
			var options = {
			    height: 470,
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
				// slantedText: true,
				// slantedTextAngle: 45
			    },
			    vAxes: {
				0: {
				    title: '<?php echo $isLocal ? lang("utility-cost-chart-yaxis-0-title") . ' (' . currency_symbol($isLocal) . ')' : lang("utility-cost-chart-yaxis-0-title") . ' (' . BASE_CURRENCY . BASE_CURRENCY_SYMBOL . ')'; ?>',
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
				    fontSize: 18
				}
			    }
			};
			var chart1_monthly = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_monthly'));
			google.visualization.events.addListener(chart1_monthly, 'ready', function() {
			    setTimeout(function() {
				var imgUri = '';
				imgUri = chart1_monthly.getImageURI();
				document.getElementById('columnChartImg_monthly').value = imgUri;
				unblockUI();
				$("#monthly_report_popup_submit").trigger('click');
			    }, 1000);
			});
			chart1_monthly.draw(data, options);
			var data = google.visualization.arrayToDataTable(response.carbon_footprint);
			var series = {};
			var i = 0;
			$.each(response.chart_index_carbon, function(index, value) {
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
			});
			series[i + 1] = {
			    targetAxisIndex: 1,
			    type: "line",
			    color: '#000',
			    pointShape: 'square',
			    pointSize: 10
			};
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
				    fontSize: 18
				}
			    }
			};
			var chart1_carbon_footprint = new google.visualization.ColumnChart(document.getElementById('utility_cost_chart_carbon_footprint_monthly'));
			google.visualization.events.addListener(chart1_carbon_footprint, 'ready', function() {
			    setTimeout(function() {
				var imgUri = '';
				imgUri = chart1_carbon_footprint.getImageURI();
				document.getElementById('columnChartCarbonFootprintMonthlyImg').value = imgUri;
				unblockUI();
			    }, 1000);
			});
			chart1_carbon_footprint.draw(data, options);
		       /*  var wasteMonthData = google.visualization.arrayToDataTable(response.chart_waste_data);
			var optionsNew = {
			    height: 700,
			    isStacked: true,
			    title: 'Monthly Waste',
			    titleTextStyle: {
				fontName: 'Arial',
				fontSize: 30
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
			    0: {
				title: 'waste',
				titleTextStyle: {
				    fontName: 'Arial',
				    fontSize: 24
				}
			    },
			    vAxes: {
				1: {
				    title: 'Kgs',
				    titleTextStyle: {
					fontName: 'Arial',
					fontSize: 24
				    },
				    'minValue': 100,
				    ticks: [0, 50, 100, 150, 200]
				}
			    },
			    interpolateNulls: true,
			    series: {
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
				6: {
				    targetAxisIndex: 1,
				    type: "line"
				},
				7: {
				    targetAxisIndex: 1,
				    type: "line"
				},
			    },
			    legend: {
				position: 'top',
				maxLines: 3,
				textStyle: {
				    fontSize: 18
				}
			    }
			};
			var waste_monthly = new google.visualization.ColumnChart(document.getElementById('wasteMonthlyChart'));
			google.visualization.events.addListener(waste_monthly, 'ready', function() {
			    setTimeout(function() {
				var imgUri = '';
				imgUri = waste_monthly.getImageURI();
				document.getElementById('wasteMonthlyChartImg').value = imgUri;
				unblockUI();
				//$("#monthly_report_popup_submit").trigger('click');
			    }, 1000);
			});
			waste_monthly.draw(wasteMonthData, optionsNew);
			var wastePieMonthData = google.visualization.arrayToDataTable([
			    ['month', 'waste'],
			    response.chart_pie_data['generalwaste'],
			    response.chart_pie_data['paperwaste'],
			    response.chart_pie_data['foodwaste'],
			    response.chart_pie_data['cardboardwaste'],
			    response.chart_pie_data['plasticwaste'],
			    response.chart_pie_data['glasswaste'],
			]);
			var options = {
			    height: 600,
			    title: 'Waste Monthly Report',
			    sliceVisibilityThreshold: .0,
			    pieHole: 0.4,
			    titleTextStyle: {
				fontName: 'Arial',
				fontSize: 24
			    },
			    legend: {
				textStyle: {
				    fontSize: 17
				}
			    },
			    chartArea: {
				width: "100%"
			    },
			    slices: {
				0: {
				    color: '<?php echo $chart_legend_colors['Generalwaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				1: {
				    color: '<?php echo $chart_legend_colors['Paperwaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				2: {
				    color: '<?php echo $chart_legend_colors['Foodwaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				3: {
				    color: '<?php echo $chart_legend_colors['Cardboardwaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				4: {
				    color: '<?php echo $chart_legend_colors['Plasticwaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				5: {
				    color: '<?php echo $chart_legend_colors['Glasswaste']; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
			    }
			};
			var wasteMonthlyPieChart = new google.visualization.PieChart(document.getElementById('waste_pie_monthly_chart'));
			google.visualization.events.addListener(wasteMonthlyPieChart, 'ready', function() {
			    setTimeout(function() {
				var imgUri = wasteMonthlyPieChart.getImageURI();
				document.getElementById('wastePieMonthlyChartImg').value = imgUri;
				unblockUI();
			    }, 1000);
			});
			wasteMonthlyPieChart.draw(wastePieMonthData, options);
			var wastePieLandfillMonthData = google.visualization.arrayToDataTable([
			    ['month', 'waste'],
			    response.chart_pie_data['recycledwaste'],
			    response.chart_pie_data['landfill'],
			]);
			var options = {
			    height: 600,
			    title: 'Waste Monthly Report',
			    sliceVisibilityThreshold: .0,
			    pieHole: 0.4,
			    titleTextStyle: {
				fontName: 'Arial',
				fontSize: 24
			    },
			    legend: {
				textStyle: {
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
			var wasteMonthlyLandfillPieChart = new google.visualization.PieChart(document.getElementById('waste_pie_landfill_monthly_chart'));
			google.visualization.events.addListener(wasteMonthlyLandfillPieChart, 'ready', function() {
			    setTimeout(function() {
				var imgUri = wasteMonthlyLandfillPieChart.getImageURI();
				document.getElementById('wastePieLandfillMonthlyChartImg').value = imgUri;
				unblockUI();
				$("#monthly_report_popup_submit").trigger('click');
			    }, 1000);
			});
			wasteMonthlyLandfillPieChart.draw(wastePieLandfillMonthData, options);
			*/
			// For monthly kwh piechart (Energy Consumption (MJ) - MONTH YEAR)
			var kwh_data = google.visualization.arrayToDataTable(response.kwh_pie_chart);
			var series = {};
			var i = 0;
			var options = {
			    height: 480,
			    title: response.kwh_pie_chart_title,
			    sliceVisibilityThreshold: .0,
			    pieHole: 0.4,
			    titleTextStyle: {
				fontName: 'Arial',
				fontSize: 24,
				bold: true
			    },
			    legend: {
				textStyle: {
				    fontSize: 17
				}
			    },
			    chartArea: {
				width: "100%"
			    },
			    slices: {
				0: {
				    color: '<?php echo $colorElectricity; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				1: {
				    color: '<?php echo $colorFuel; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				2: {
				    color: '<?php echo $colorLpg; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				3: {
				    color: '<?php echo $colorNaturalGas; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				4: {
				    color: '<?php echo $colorWater; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				5: {
				    color: '<?php echo $colorHeatingDistrict; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				6: {
				    color: '<?php echo $colorCoolingDistrict; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
			    }
			};
			var kwh_chart = new google.visualization.PieChart(document.getElementById('kwh_pie_chart_monthly_chart'));
			google.visualization.events.addListener(kwh_chart, 'ready', function() {
			    var imgUri = kwh_chart.getImageURI();
			    document.getElementById('pieChartImgkwhMonthly').value = imgUri;
			    unblockUI();
			    $("#monthly_report_popup_submit").trigger('click');
			});
			kwh_chart.draw(kwh_data, options);
			// For monthly cost piechart (Energy Consumption (MJ) - MONTH YEAR)
			var kwh_data = google.visualization.arrayToDataTable(response.cost_pie_chart);
			var series = {};
			var i = 0;
			var options = {
			    height: 480,
			    title: response.cost_pie_chart_title,
			    sliceVisibilityThreshold: .0,
			    pieHole: 0.4,
			    titleTextStyle: {
				fontName: 'Arial',
				fontSize: 24,
				bold: true
			    },
			    legend: {
				textStyle: {
				    fontSize: 17
				}
			    },
			    chartArea: {
				width: "100%"
			    },
			    slices: {
				0: {
				    color: '<?php echo $colorElectricity; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				1: {
				    color: '<?php echo $colorFuel; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				2: {
				    color: '<?php echo $colorLpg; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				3: {
				    color: '<?php echo $colorNaturalGas; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				4: {
				    color: '<?php echo $colorWater; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				5: {
				    color: '<?php echo $colorHeatingDistrict; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
				6: {
				    color: '<?php echo $colorCoolingDistrict; ?>',
				    textStyle: {
					fontSize: 18
				    }
				},
			    }
			};
			var cost_month_chart = new google.visualization.PieChart(document.getElementById('cost_pie_chart_monthly_chart'));
			google.visualization.events.addListener(cost_month_chart, 'ready', function() {
			    var imgUri = cost_month_chart.getImageURI();
			    document.getElementById('pieChartImgcostMonthly').value = imgUri;
			    unblockUI();
			});
			cost_month_chart.draw(kwh_data, options);
		    }
		});
	    }
	});
    });
    function getReportsOnMain(functionName) {
	if ($("#utility_year_select_year").val()) {
	    selectedyear = $("#utility_year_select_year").val();
	} else {
	    selectedyear = (new Date()).getFullYear();
	}
	blockUI();
	$.ajax({
	    type: 'POST',
	    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/roomnight',
	    data: {
		<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',
		view_type: $('#view_type').val(),
		yearly_report_year: selectedyear,
		currency: '<?php echo $currency; ?>',
	    },
	    success: function(response) {
		$('#utility_cost_chart_roomnight_div').html(response);
		$.ajax({
		    type: 'POST',
		    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/carbon_footprint',
		    data: {
			<?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',
			view_type: $('#view_type').val(),
			yearly_report_year: selectedyear,
			currency: '<?php echo $currency; ?>',
		    },
		    success: function(response) {
			$('#utility_cost_chart_carbon_footprint_div').html(response);
			unblockUI();
		    }
		});
	    }
	}, 5000);
    }
</script>