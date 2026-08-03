<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

//Bar chart show last year data
$current_year = date('Y');
$last_year = $current_year - 1;

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

<div class="card-wrap">
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-body">
					<div id="utility_cost_chart_roomnight" style="height:700px;">
						<?php if (empty($utility_cost_chart_roomnight)) { ?>
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
<script>
	roomNightHighchart();

	function roomNightHighchart() {
		<?php if (!empty($utility_cost_chart_roomnight)) {
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
			arrValuesMulti.push(arrTitle);
			
			
                <?php 
                $total_months = 0;
                foreach ($resultkeys as $year => $value) {
                    foreach ($value as $key1 => $month) {
                        // Previous year data
                        $pre_monthdata = $montharray[$month] . ' ' . ($year-1);
                        $pre_data_electricity = (!empty($utility_cost_chart_roomnight[$month][$year-1]['electricity']))?$utility_cost_chart_roomnight[$month][$year-1]['electricity']:0;
                        $pre_data_fuel = (!empty($utility_cost_chart_roomnight[$month][$year-1]['fuel']))?$utility_cost_chart_roomnight[$month][$year-1]['fuel']:0;
                        $pre_data_lpg = (!empty($utility_cost_chart_roomnight[$month][$year-1]['lpg']))?$utility_cost_chart_roomnight[$month][$year-1]['lpg']:0;
                        $pre_data_natural_gas = (!empty($utility_cost_chart_roomnight[$month][$year-1]['natural_gas']))?$utility_cost_chart_roomnight[$month][$year-1]['natural_gas']:0;
                        $pre_data_heating_district = (!empty($utility_cost_chart_roomnight[$month][$year-1]['heating_district']))?$utility_cost_chart_roomnight[$month][$year-1]['heating_district']:0;
                        $pre_data_cooling_district = (!empty($utility_cost_chart_roomnight[$month][$year-1]['cooling_district']))?$utility_cost_chart_roomnight[$month][$year-1]['cooling_district']:0;
                        $pre_data_water = (!empty($utility_cost_chart_roomnight[$month][$year-1]['water']))?$utility_cost_chart_roomnight[$month][$year-1]['water']:0;
                        $pre_data_cdd = (!empty($utility_cost_chart_roomnight[$month][$year-1]['cdd']))?$utility_cost_chart_roomnight[$month][$year-1]['cdd']:0;
                        $pre_data_hdd = (!empty($utility_cost_chart_roomnight[$month][$year-1]['hdd']))?$utility_cost_chart_roomnight[$month][$year-1]['hdd']:0;
                        $pre_data_occupancy = (!empty($utility_cost_chart_roomnight[$month][$year-1]['occupancy']))?$utility_cost_chart_roomnight[$month][$year-1]['occupancy']:0;
                        $pre_data_budget = (!empty($utility_cost_chart_roomnight[$month][$year-1]['budget']))?$utility_cost_chart_roomnight[$month][$year-1]['budget']:0;
                        
                        // Current year data
                        $monthdata = $montharray[$month] . ' ' . $year;
                        $data_electricity = (!empty($utility_cost_chart_roomnight[$month][$year]['electricity']))?$utility_cost_chart_roomnight[$month][$year]['electricity']:0;
                        $data_fuel = (!empty($utility_cost_chart_roomnight[$month][$year]['fuel']))?$utility_cost_chart_roomnight[$month][$year]['fuel']:0;
                        $data_lpg = (!empty($utility_cost_chart_roomnight[$month][$year]['lpg']))?$utility_cost_chart_roomnight[$month][$year]['lpg']:0;
                        $data_natural_gas = (!empty($utility_cost_chart_roomnight[$month][$year]['natural_gas']))?$utility_cost_chart_roomnight[$month][$year]['natural_gas']:0;
                        $data_heating_district = (!empty($utility_cost_chart_roomnight[$month][$year]['heating_district']))?$utility_cost_chart_roomnight[$month][$year]['heating_district']:0;
                        $data_cooling_district = (!empty($utility_cost_chart_roomnight[$month][$year]['cooling_district']))?$utility_cost_chart_roomnight[$month][$year]['cooling_district']:0;
                        $data_water = (!empty($utility_cost_chart_roomnight[$month][$year]['water']))?$utility_cost_chart_roomnight[$month][$year]['water']:0;
                        $data_cdd = (!empty($utility_cost_chart_roomnight[$month][$year]['cdd']))?$utility_cost_chart_roomnight[$month][$year]['cdd']:0;
                        $data_hdd = (!empty($utility_cost_chart_roomnight[$month][$year]['hdd']))?$utility_cost_chart_roomnight[$month][$year]['hdd']:0;
                        $data_occupancy = (!empty($utility_cost_chart_roomnight[$month][$year]['occupancy']))?$utility_cost_chart_roomnight[$month][$year]['occupancy']:0;
                        $data_budget = (!empty($utility_cost_chart_roomnight[$month][$year]['budget']))?$utility_cost_chart_roomnight[$month][$year]['budget']:0;

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

					var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
					var arrValues = ['<?php echo $monthdata; ?>'];
					<?php if ($totalElectricity != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_electricity; ?>);
					<?php	} ?>
					<?php if ($totalFuel != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_fuel; ?>);
					<?php	} ?>
					<?php if ($totalLpg != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_lpg; ?>);
					<?php	} ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_natural_gas; ?>);
					<?php	} ?>
					<?php if ($totalWater != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_water; ?>);
					<?php	} ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_heating_district; ?>);
					<?php	} ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValuesPre.push(<?php echo $pre_data_cooling_district; ?>);
					<?php	} ?>
					arrValuesPre.push(<?php echo $pre_data_occupancy; ?>);
					arrValuesPre.push(null);


					<?php if ($totalElectricity != 0) { ?>
						arrValues.push(<?php echo $data_electricity; ?>);
					<?php	} ?>
					<?php if ($totalFuel != 0) { ?>
						arrValues.push(<?php echo $data_fuel; ?>);
					<?php	} ?>
					<?php if ($totalLpg != 0) { ?>
						arrValues.push(<?php echo $data_lpg; ?>);
					<?php	} ?>
					<?php if ($totalNaturalGas != 0) { ?>
						arrValues.push(<?php echo $data_natural_gas; ?>);
					<?php	} ?>
					<?php if ($totalWater != 0) { ?>
						arrValues.push(<?php echo $data_water; ?>);
					<?php	} ?>
					<?php if ($totalHeatingDistrict != 0) { ?>
						arrValues.push(<?php echo $data_heating_district; ?>);
					<?php	} ?>
					<?php if ($totalCoolingDistrict != 0) { ?>
						arrValues.push(<?php echo $data_cooling_district; ?>);
					<?php	} ?>

					arrValues.push(null);
					arrValues.push(<?php echo $data_occupancy; ?>);

					arrValuesMulti.push(arrValuesPre);
					arrValuesMulti.push(arrValues);

					/* ['',null,null,null,null,null,null,null,null,null],
                        ['<?php echo $pre_monthdata; ?>',<?php echo $pre_data_electricity; ?>,<?php echo $pre_data_fuel; ?>,<?php echo $pre_data_lpg; ?>,<?php echo $pre_data_natural_gas; ?>,<?php echo $pre_data_water; ?>,<?php echo $pre_data_heating_district; ?>,<?php echo $pre_data_cooling_district; ?>,<?php echo $pre_data_occupancy; ?>,null],                 
                        ['<?php echo $monthdata; ?>',<?php echo $data_electricity; ?>,<?php echo $data_fuel; ?>,<?php echo $data_lpg; ?>,<?php echo $data_natural_gas; ?>,<?php echo $data_water; ?>,<?php echo $data_heating_district; ?>,<?php echo $data_cooling_district; ?>,null,<?php echo $data_occupancy; ?>],    */              
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
                
                $AVG_data_electricity = (!empty($AVG_data_electricity)) ? $AVG_data_electricity : 0;
                $AVG_data_fuel = (!empty($AVG_data_fuel)) ? $AVG_data_fuel : 0;
                $AVG_data_lpg = (!empty($AVG_data_lpg)) ? $AVG_data_lpg : 0;
                $AVG_data_natural_gas = (!empty($AVG_data_natural_gas)) ? $AVG_data_natural_gas : 0;
                $AVG_data_heating_district = (!empty($AVG_data_heating_district)) ? $AVG_data_heating_district : 0;
                $AVG_data_cooling_district = (!empty($AVG_data_cooling_district)) ? $AVG_data_cooling_district : 0;
                $AVG_data_water = (!empty($AVG_data_water)) ? $AVG_data_water : 0;
                $AVG_data_cdd = (!empty($AVG_data_cdd)) ? $AVG_data_cdd : 0;
                $AVG_data_hdd = (!empty($AVG_data_hdd)) ? $AVG_data_hdd : 0;
                $AVG_data_occupancy = (!empty($AVG_data_occupancy)) ? $AVG_data_occupancy : 0;
                $AVG_data_budget = (!empty($AVG_data_budget)) ? $AVG_data_budget : 0;

            $AVG_pre_data_occupancy = round($AVG_pre_data_occupancy,2);
            $AVG_data_occupancy = round($AVG_data_occupancy,2);

			?>
			var arrAvgPre = ['<?php echo ($year - 1) . " " . lang("average"); ?>'];
			<?php if ($totalElectricity != 0) { ?>
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
			
			var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
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

			arrValuesMulti.push(arrAvgPre);
			arrValuesMulti.push(arrAvg);

			var roomNightXaxisData = [];
			var roomNightDataArray = [];
			var yearSelectedRoomNight = '<?php echo $year; ?>';
			var yearSelectedPreRoomNight = '<?php echo $year - 1; ?>';
			var occupancy = 'Occupancy-';
			var occupancyRoomNightYear = occupancy.concat('', yearSelectedRoomNight);
			var occupancyRoomNightYearPrevious = occupancy.concat('', yearSelectedPreRoomNight);
			var roomNightSubtitle = arrValuesMulti[0];
			roomNightSubtitleName = roomNightSubtitle.filter(value => value !== "Month");
			for (var i = 1; i < arrValuesMulti.length; i++) {
				roomNightXaxisData.push(arrValuesMulti[i][0]);
			}
			$.each(roomNightSubtitleName, function(i) {
				var key = roomNightSubtitleName[i];
				roomNightDataArray[key] = [];
				for (var j = 1; j < arrValuesMulti.length; j++) {
					roomNightDataArray[key].push(arrValuesMulti[j][i + 1]);
				}
			});
			var roomNightOccupancySeries = [];
			Object.entries(roomNightDataArray).forEach(([key, value]) => {
				if (!(key == occupancyRoomNightYear || key == occupancyRoomNightYearPrevious)) {
					if (key == 'Electricity') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorElectricity; ?>'
						}, );
					}
					if (key == 'Natural Gas') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorNaturalGas; ?>'
						}, );
					}
					if (key == 'Fuel') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorFuel; ?>'
						}, );
					}
					if (key == 'LPG') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorLpg; ?>'
						}, );
					}
					if (key == 'Water') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorWater; ?>'
						}, );
					}
					if (key == 'District Cooling') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorCoolingDistrict; ?>'
						}, );
					}
					if (key == 'District Heating') {
						roomNightOccupancySeries.push({
							pointWidth: 15,
							name: key,
							data: roomNightDataArray[key],
							color: '<?php echo $colorHeatingDistrict; ?>'
						}, );
					}
				} else {
					roomNightOccupancySeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: roomNightDataArray[key],
						marker: {
							symbol: 'square',
							lineWidth: 2
						},
						color: key == occupancyRoomNightYear ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1]
					}, );
				}
			});
			Highcharts.setOptions({
				lang: {
					numericSymbols: null,
					thousandsSep: ',',
				}
			});
			Highcharts.chart('utility_cost_chart_roomnight', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<?php echo $utility_cost_chart_roomnight["utility_cost_chart_title"]; ?>',
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
				subtitle: {
					text: roomNightSubtitleName
				},
				xAxis: {
					title: {
						enabled: true,
						text: 'Month',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					categories: roomNightXaxisData
				},
				yAxis: [{
					allowDecimals: false,
					// min: 0,
					// max: 1500,
					title: {
						text: '<?php echo lang("utility-cost-chart-yaxis-0-title"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				}, {
					// min: 0,
					// tick: 10,
					tickPositions: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					title: {
						text: '<?php echo lang("occupancy"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					opposite: true,
				}],
				tooltip: {
					formatter: function() {
						return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + Highcharts.numberFormat(this.y, 1, '.', ',') + '<br/>';
					}
				},
				plotOptions: {
					series: {
						connectNulls: true
					},
					column: {
						stacking: 'normal'
					}
				},
				series: roomNightOccupancySeries
			});
	}
	<?php } ?>
</script>