<?php

$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'electricity');
$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'fuel_oil');
$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'lpg');
$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'natural_gas');
$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_heating');
$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'district_cooling');
$water_mmbtu_rate = getUtilityUnitFactorForConversion($site_id, 'water');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Config array
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

// Prepare array for loop
$startmonthsarray = array();
$endmonthsarray = array();

if ($filters["start_year"] == $filters["end_year"]) { // If start and end year is same
    for ($i = $filters['start_month']; $i <= $filters["end_month"]; $i++) {
	$startmonthsarray[] = $i;
    }

    $resultkeys = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
} else { // If start and end year is not same
    for ($i = $filters['start_month']; $i <= 12; $i++) {
	$startmonthsarray[] = $i;
    }

    for ($i = 1; $i <= $filters['end_month']; $i++) {
	$endmonthsarray[] = $i;
    }
    $resultkeys = array();
    $resultkeys[$filters["start_year"]] = $startmonthsarray;
    $resultkeys[$filters["end_year"]] = $endmonthsarray;
}
$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
?>

<div class="card-wrap">
	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-body">
					<div id="utility_cost_chart_carbon_footprint" style="height:700px;">
						<?php if (empty($utility_cost_chart_carbon_footprint)) { ?>
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
	drawCorbonFootprintChart();

	function drawCorbonFootprintChart() {
		<?php
				//For colors
				$colorElectricity = ($totalElectricity != 0) ? $chart_legend_colors['Electricity'] : '';
				$colorFuel = ($totalFuel != 0) ? $chart_legend_colors['Fuel'] : '';
				$colorLpg = ($totalLpg != 0) ? $chart_legend_colors['LPG'] : '';
				$colorNaturalGas = ($totalNaturalGas != 0) ? $chart_legend_colors['Natural_Gas'] : '';
				$colorWater = ($totalWater != 0) ? $chart_legend_colors['Water'] : '';
				$colorHeatingDistrict = ($totalHeatingDistrict != 0) ? $chart_legend_colors['District_Heating'] : '';
				$colorCoolingDistrict = ($totalCoolingDistrict != 0) ? $chart_legend_colors['District_Cooling'] : '';
				$colorlastYear = $chart_legend_colors[date('Y') - 1];
				$colorCurrentYear = $chart_legend_colors[date('Y')];

		?>
			var arrTitle = ['Month'];
			var arrValuesMulti = [];
			arrTitle.push('<?php echo date('Y') - 1 ?>');
			arrTitle.push('<?php echo date('Y') ?>');

			arrTitle.push('<?php echo lang("occupancy")."-".$last_year; ?>');
			arrTitle.push('<?php echo lang("occupancy")."-".$current_year; ?>');
			arrValuesMulti.push(arrTitle);


		<?php
		$total_months = 0;
		foreach ($resultkeys as $year => $value) {
		    foreach ($value as $key1 => $month) {
			// Previous year data
			$pre_monthdata = $montharray[$month] . ' ' . ($year-1);
			$pre_data_electricity = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['total_electricity_kwh']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['total_electricity_kwh']:0;
			$pre_data_fuel = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['fuel']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['fuel']:0;
			$pre_data_lpg = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['lpg']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['lpg']:0;
			$pre_data_natural_gas = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['natural_gas']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['natural_gas']:0;
			$pre_data_heating_district = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['heating_district']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['heating_district']:0;
			$pre_data_cooling_district = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['cooling_district']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['cooling_district']:0;
			$pre_data_water = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['water']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['water']:0;
			$pre_data_cdd = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['cdd']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['cdd']:0;
			$pre_data_hdd = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['hdd']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['hdd']:0;
			$pre_data_occupancy = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['occupancy']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['occupancy']:0;
			$pre_data_budget = (!empty($utility_cost_chart_carbon_footprint[$month][$year-1]['budget']))?$utility_cost_chart_carbon_footprint[$month][$year-1]['budget']:0;

			// Current year data
			$monthdata = $montharray[$month] . ' ' . $year;
			$data_electricity = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['total_electricity_kwh']))?$utility_cost_chart_carbon_footprint[$month][$year]['total_electricity_kwh']:0;
			$data_fuel = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['fuel']))?$utility_cost_chart_carbon_footprint[$month][$year]['fuel']:0;
			$data_lpg = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['lpg']))?$utility_cost_chart_carbon_footprint[$month][$year]['lpg']:0;
			$data_natural_gas = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['natural_gas']))?$utility_cost_chart_carbon_footprint[$month][$year]['natural_gas']:0;
			$data_heating_district = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['heating_district']))?$utility_cost_chart_carbon_footprint[$month][$year]['heating_district']:0;
			$data_cooling_district = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['cooling_district']))?$utility_cost_chart_carbon_footprint[$month][$year]['cooling_district']:0;
			$data_water = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['water']))?$utility_cost_chart_carbon_footprint[$month][$year]['water']:0;
			$data_cdd = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['cdd']))?$utility_cost_chart_carbon_footprint[$month][$year]['cdd']:0;
			$data_hdd = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['hdd']))?$utility_cost_chart_carbon_footprint[$month][$year]['hdd']:0;
			$data_occupancy = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['occupancy']))?$utility_cost_chart_carbon_footprint[$month][$year]['occupancy']:0;
			$data_budget = (!empty($utility_cost_chart_carbon_footprint[$month][$year]['budget']))?$utility_cost_chart_carbon_footprint[$month][$year]['budget']:0;

			// Calculate carbon footprint

			$pre_data_electricity      = round($pre_data_electricity * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

			$pre_data_fuel             = round($pre_data_fuel * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

			$pre_data_lpg              = round($pre_data_lpg * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

			$pre_data_natural_gas      = round($pre_data_natural_gas * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

			$pre_data_heating_district = round($pre_data_heating_district * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

			$pre_data_cooling_district = round($pre_data_cooling_district * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);

			$pre_data_water            = 0; // There is no calculation for water data



			$data_electricity      = round($data_electricity * $electricity_mmbtu_rate * $site_detail['electricity_emission_factor'], 2);

			$data_fuel             = round($data_fuel * $fuel_mmbtu_rate * $site_detail['fuel_emission_factor'], 2);

			$data_lpg              = round($data_lpg * $lpg_mmbtu_rate * $site_detail['lpg_emission_factor'], 2);

			$data_natural_gas      = round($data_natural_gas * $natural_gas_mmbtu_rate * $site_detail['natural_gas_emission_factor'], 2);

			$data_heating_district = round($data_heating_district * $heating_district_mmbtu_rate * $site_detail['district_heating_emission_factor'], 2);

			$data_cooling_district = round($data_cooling_district * $cooling_district_mmbtu_rate * $site_detail['district_cooling_emission_factor'], 2);

			$data_water            = 0; // There is no calculation for water data

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

				var pre_data_electricity = pre_data_fuel = pre_data_lpg = pre_data_natural_gas = pre_data_heating_district = pre_data_cooling_district = data_electricity = data_fuel = data_lpg = data_natural_gas = data_water = data_heating_district = data_cooling_district = AVG_pre_data_electricity = AVG_pre_data_fuel = AVG_pre_data_lpg = AVG_pre_data_natural_gas = AVG_pre_data_water = AVG_pre_data_heating_district = AVG_pre_data_cooling_district = AVG_data_electricity = AVG_data_fuel = AVG_data_lpg = AVG_data_natural_gas = AVG_data_water = AVG_data_heating_district = AVG_data_cooling_district = 0;
				var arrValuesPre = ['<?php echo $pre_monthdata; ?>'];
				var arrValues = ['<?php echo $monthdata; ?>'];
				<?php if ($totalElectricity != 0) { ?>
					var pre_data_electricity = <?php echo !empty($pre_data_electricity) && is_finite($pre_data_electricity) ? $pre_data_electricity : 0; ?>;
				<?php	} ?>
				<?php if ($totalFuel != 0) { ?>
					var pre_data_fuel = <?php echo !empty($pre_data_fuel) && is_finite($pre_data_fuel) ? $pre_data_fuel : 0; ?>;
				<?php	} ?>
				<?php if ($totalLpg != 0) { ?>
					var pre_data_lpg = <?php echo !empty($pre_data_lpg) && is_finite($pre_data_lpg) ? $pre_data_lpg : 0; ?>;
				<?php	} ?>
				<?php if ($totalNaturalGas != 0) { ?>
					var pre_data_natural_gas = <?php echo !empty($pre_data_natural_gas) && is_finite($pre_data_natural_gas) ? $pre_data_natural_gas : 0; ?>;
				<?php	} ?>
				<?php if ($totalHeatingDistrict != 0) { ?>
					var pre_data_heating_district = <?php echo !empty($pre_data_heating_district) && is_finite($pre_data_heating_district) ? $pre_data_heating_district : 0; ?>;
				<?php	} ?>
				<?php if ($totalCoolingDistrict != 0) { ?>
					var pre_data_cooling_district = <?php echo !empty($pre_data_cooling_district) && is_finite($pre_data_cooling_district) ? $pre_data_cooling_district : 0; ?>;
				<?php	} ?>
				var total_arrValuesPre = pre_data_electricity + pre_data_fuel + pre_data_lpg + pre_data_natural_gas + pre_data_heating_district + pre_data_cooling_district;
				arrValuesPre.push(total_arrValuesPre);
				arrValuesPre.push(null);
				arrValuesPre.push(<?php echo !empty($pre_data_occupancy) && is_finite($pre_data_occupancy) ? $pre_data_occupancy : 0; ?>);
				arrValuesPre.push(null);


				<?php if ($totalElectricity != 0) { ?>
					var data_electricity = <?php echo !empty($data_electricity) && is_finite($data_electricity) ? $data_electricity : 0; ?>;
				<?php	} ?>
				<?php if ($totalFuel != 0) { ?>
					var data_fuel = <?php echo !empty($data_fuel) && is_finite($data_fuel) ? $data_fuel : 0; ?>;
				<?php	} ?>
				<?php if ($totalLpg != 0) { ?>
					var data_lpg = <?php echo !empty($data_lpg) && is_finite($data_lpg) ? $data_lpg : 0; ?>;
				<?php	} ?>
				<?php if ($totalNaturalGas != 0) { ?>
					var data_natural_gas = <?php echo !empty($data_natural_gas) && is_finite($data_natural_gas) ? $data_natural_gas : 0; ?>;
				<?php	} ?>
				<?php if ($totalHeatingDistrict != 0) { ?>
					var data_heating_district = <?php echo !empty($data_heating_district) && is_finite($data_heating_district) ? $data_heating_district : 0; ?>;
				<?php	} ?>
				<?php if ($totalCoolingDistrict != 0) { ?>
					var data_cooling_district = <?php echo !empty($data_cooling_district) && is_finite($data_cooling_district) ? $data_cooling_district : 0; ?>;
				<?php	} ?>

				var total_arrValues = data_electricity + data_fuel + data_lpg + data_natural_gas + data_water + data_heating_district + data_cooling_district;
				arrValues.push(null);
				arrValues.push(total_arrValues);
				arrValues.push(null);
				arrValues.push(<?php echo !empty($data_occupancy) && is_finite($data_occupancy) ? $data_occupancy : 0; ?>);
				arrValuesMulti.push(arrValuesPre);
				arrValuesMulti.push(arrValues);

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
	    // $YTD_total_months = $total_months;		
		if(date('n') == 1) {
			$YTD_total_months = 12;
		} else {
			$YTD_total_months = date('n') - 1;
		}
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
	$AVG_pre_data_electricity = (!empty($AVG_pre_data_electricity)) && !is_infinite($AVG_pre_data_electricity) ? $AVG_pre_data_electricity : 0;
	$AVG_pre_data_fuel = (!empty($AVG_pre_data_fuel)) && !is_infinite($AVG_pre_data_fuel) ? $AVG_pre_data_fuel : 0;
	$AVG_pre_data_lpg = (!empty($AVG_pre_data_lpg)) && !is_infinite($AVG_pre_data_lpg) ? $AVG_pre_data_lpg : 0;
	$AVG_pre_data_natural_gas = (!empty($AVG_pre_data_natural_gas)) && !is_infinite($AVG_pre_data_natural_gas) ? $AVG_pre_data_natural_gas : 0;
	$AVG_pre_data_heating_district = (!empty($AVG_pre_data_heating_district)) && !is_infinite($AVG_pre_data_heating_district) ? $AVG_pre_data_heating_district : 0;
	$AVG_pre_data_cooling_district = (!empty($AVG_pre_data_cooling_district)) && !is_infinite($AVG_pre_data_cooling_district) ? $AVG_pre_data_cooling_district : 0;
	$AVG_pre_data_water = (!empty($AVG_pre_data_water)) && !is_infinite($AVG_pre_data_water) ? $AVG_pre_data_water : 0;
	$AVG_pre_data_cdd = (!empty($AVG_pre_data_cdd)) && !is_infinite($AVG_pre_data_cdd) ? $AVG_pre_data_cdd : 0;
	$AVG_pre_data_hdd = (!empty($AVG_pre_data_hdd)) && !is_infinite($AVG_pre_data_hdd) ? $AVG_pre_data_hdd : 0;
	$AVG_pre_data_occupancy = (!empty($AVG_pre_data_occupancy)) && !is_infinite($AVG_pre_data_occupancy) ? $AVG_pre_data_occupancy : 0;
	$AVG_pre_data_budget = (!empty($AVG_pre_data_budget)) && !is_infinite($AVG_pre_data_budget) ? $AVG_pre_data_budget : 0;

	$AVG_data_electricity = (!empty($AVG_data_electricity)) && !is_infinite($AVG_data_electricity) ? $AVG_data_electricity : 0;
	$AVG_data_fuel = (!empty($AVG_data_fuel)) && !is_infinite($AVG_data_fuel) ? $AVG_data_fuel : 0;
	$AVG_data_lpg = (!empty($AVG_data_lpg)) && !is_infinite($AVG_data_lpg) ? $AVG_data_lpg : 0;
	$AVG_data_natural_gas = (!empty($AVG_data_natural_gas)) && !is_infinite($AVG_data_natural_gas) ? $AVG_data_natural_gas : 0;
	$AVG_data_heating_district = (!empty($AVG_data_heating_district)) && !is_infinite($AVG_data_heating_district) ? $AVG_data_heating_district : 0;
	$AVG_data_cooling_district = (!empty($AVG_data_cooling_district)) && !is_infinite($AVG_data_cooling_district) ? $AVG_data_cooling_district : 0;
	$AVG_data_water = (!empty($AVG_data_water)) && !is_infinite($AVG_data_water) ? $AVG_data_water : 0;
	$AVG_data_cdd = (!empty($AVG_data_cdd)) && !is_infinite($AVG_data_cdd) ? $AVG_data_cdd : 0;
	$AVG_data_hdd = (!empty($AVG_data_hdd)) && !is_infinite($AVG_data_hdd) ? $AVG_data_hdd : 0;
	$AVG_data_occupancy = (!empty($AVG_data_occupancy)) && !is_infinite($AVG_data_occupancy) ? $AVG_data_occupancy : 0;
	$AVG_data_budget = (!empty($AVG_data_budget)) && !is_infinite($AVG_data_budget) ? $AVG_data_budget : 0;

	$AVG_pre_data_occupancy = (!empty($AVG_pre_data_occupancy)) && !is_infinite($AVG_pre_data_occupancy) ? round($AVG_pre_data_occupancy,2) : 0;
	$AVG_data_occupancy = (!empty($AVG_data_occupancy)) && !is_infinite($AVG_data_occupancy) ? round($AVG_data_occupancy,2) : 0;

		?>

		var arrAvgPre = ['<?php echo ($year - 1) . " " . lang("average"); ?>'];
		<?php if ($totalElectricity != 0) { ?>
		var AVG_pre_data_electricity = <?php echo !empty($AVG_pre_data_electricity) && is_finite($AVG_pre_data_electricity) ? $AVG_pre_data_electricity : 0; ?>;
		<?php	} ?>
		<?php if ($totalFuel != 0) { ?>
		var AVG_pre_data_fuel = <?php echo !empty($AVG_pre_data_fuel) && is_finite($AVG_pre_data_fuel) ? $AVG_pre_data_fuel : 0; ?>;
		<?php	} ?>
		<?php if ($totalLpg != 0) { ?>
		var AVG_pre_data_lpg = <?php echo !empty($AVG_pre_data_lpg) && is_finite($AVG_pre_data_lpg) ? $AVG_pre_data_lpg : 0; ?>;
		<?php	} ?>
		<?php if ($totalNaturalGas != 0) { ?>
		var AVG_pre_data_natural_gas = <?php echo !empty($AVG_pre_data_natural_gas) && is_finite($AVG_pre_data_natural_gas) ? $AVG_pre_data_natural_gas : 0; ?>;
		<?php	} ?>
		<?php if ($totalHeatingDistrict != 0) { ?>
		var AVG_pre_data_heating_district = <?php echo !empty($AVG_pre_data_heating_district) && is_finite($AVG_pre_data_heating_district) ? $AVG_pre_data_heating_district : 0; ?>;
		<?php	} ?>
		<?php if ($totalCoolingDistrict != 0) { ?>
		var AVG_pre_data_cooling_district = <?php echo !empty($AVG_pre_data_cooling_district) && is_finite($AVG_pre_data_cooling_district) ? $AVG_pre_data_cooling_district : 0; ?>;
		<?php	} ?>
		var total_arrAvgPre = AVG_pre_data_electricity + AVG_pre_data_fuel + AVG_pre_data_lpg + AVG_pre_data_natural_gas + AVG_pre_data_water + AVG_pre_data_heating_district + AVG_pre_data_cooling_district;
		arrAvgPre.push(total_arrAvgPre);
		arrAvgPre.push(<?php echo !empty($AVG_pre_data_occupancy) && is_finite($AVG_pre_data_occupancy) ? $AVG_pre_data_occupancy : 0; ?>);

		var arrAvg = ['<?php echo ($year) . " " . lang("average"); ?>'];
		<?php if ($totalElectricity != 0) { ?>
			var AVG_data_electricity = <?php echo !empty($AVG_data_electricity) && is_finite($AVG_data_electricity) ? $AVG_data_electricity : 0; ?>;
		<?php	} ?>
		<?php if ($totalFuel != 0) { ?>
			var AVG_data_fuel = <?php echo !empty($AVG_data_fuel) && is_finite($AVG_data_fuel) ? $AVG_data_fuel : 0; ?>;
		<?php	} ?>
		<?php if ($totalLpg != 0) { ?>
			var AVG_data_lpg = <?php echo !empty($AVG_data_lpg) && is_finite($AVG_data_lpg) ? $AVG_data_lpg : 0; ?>;
		<?php	} ?>
		<?php if ($totalNaturalGas != 0) { ?>
			var AVG_data_natural_gas = <?php echo !empty($AVG_data_natural_gas) && is_finite($AVG_data_natural_gas) ? $AVG_data_natural_gas : 0; ?>;
		<?php	} ?>
		<?php if ($totalHeatingDistrict != 0) { ?>
			var AVG_data_heating_district = <?php echo !empty($AVG_data_heating_district) && is_finite($AVG_data_heating_district) ? $AVG_data_heating_district : 0; ?>;
		<?php	} ?>
		<?php if ($totalCoolingDistrict != 0) { ?>
			var AVG_data_cooling_district = <?php echo !empty($AVG_data_cooling_district) && is_finite($AVG_data_cooling_district) ? $AVG_data_cooling_district : 0; ?>;
		<?php	} ?>

		var total_arrAvg = AVG_data_electricity + AVG_data_fuel + AVG_data_lpg + AVG_data_natural_gas + AVG_data_water + AVG_data_heating_district + AVG_data_cooling_district;
		arrAvg.push(total_arrAvg);
		arrAvg.push(<?php echo !empty($AVG_data_occupancy) && is_finite($AVG_data_occupancy) ? $AVG_data_occupancy : 0; ?>);
		var carbonFootprintDataArray = [];
		var carbonFootprintDataArrayFormatted = [];
		var yearSelectedCarbonFootprint = '<?php echo $year; ?>';
		var yearSelectedPreCarbonFootprint = '<?php echo $year - 1; ?>';
		var occupancy = 'Occupancy-';
		var occupancyCarbonFootprintYear = occupancy.concat('', yearSelectedCarbonFootprint);
		var occupancyCarbonFootprintYearPrevious = occupancy.concat('', yearSelectedPreCarbonFootprint);
		var currentYear = '<?php echo date('Y'); ?>';
		var lastYear = '<?php echo date('Y') - 1; ?>';
		var carbonFootprintSubtitle = arrValuesMulti[0];
		carbonFootprintSubtitleName = carbonFootprintSubtitle.filter(value => value !== "Month");
		$.each(carbonFootprintSubtitleName, function(i) {
			var key = carbonFootprintSubtitleName[i];
			carbonFootprintDataArray[key] = [];
			for (var j = 1; j < arrValuesMulti.length; j++) {
				if((key == currentYear) || (key == lastYear) || (key == occupancyCarbonFootprintYear) || (key == occupancyCarbonFootprintYearPrevious))
				{
					carbonFootprintDataArray[key].push(arrValuesMulti[j][i + 1]);
				}
			}
		});
		Object.entries(carbonFootprintDataArray).forEach(([key, value]) => {
			carbonFootprintDataArrayFormatted[key] = [];
			for (var propName in value) {
				if (value[propName] === null || value[propName] === undefined) {
					delete carbonFootprintDataArray[key][propName];
				} else {
					if((key == currentYear) || (key == lastYear) || (key == occupancyCarbonFootprintYear) || (key == occupancyCarbonFootprintYearPrevious))
					{
						carbonFootprintDataArrayFormatted[key].push(value[propName]);
					}
				}
			}
			if(key == currentYear) {
				carbonFootprintDataArrayFormatted[key].push(arrAvg[1]);
			} else if(key == lastYear) {
				carbonFootprintDataArrayFormatted[key].push(arrAvgPre[1]);
			} else if(key == occupancyCarbonFootprintYear) {
				carbonFootprintDataArrayFormatted[key].push(arrAvg[2]);
			} else if(key == occupancyCarbonFootprintYearPrevious) {
				carbonFootprintDataArrayFormatted[key].push(arrAvgPre[2]);
			}
		});
		var carbonFootprintOccupancySeries = [];
		Object.entries(carbonFootprintDataArrayFormatted).forEach(([key, value]) => {
			if (!(key == occupancyCarbonFootprintYear || key == occupancyCarbonFootprintYearPrevious)) {
				if (key == currentYear) {
					carbonFootprintOccupancySeries.push({
						pointWidth: 15,
						name: key,
						data: carbonFootprintDataArrayFormatted[key],
						color: '<?php echo $colorCurrentYear; ?>'
					}, );
				} else if (key == lastYear) {
					carbonFootprintOccupancySeries.push({
						pointWidth: 15,
						name: key,
						data: carbonFootprintDataArrayFormatted[key],
						color: '<?php echo $colorlastYear; ?>'
					}, );
				}
			} else {
				carbonFootprintOccupancySeries.push({
					type: 'spline',
					name: key,
					yAxis: 1,
					data: carbonFootprintDataArrayFormatted[key],
					marker: {
						symbol: 'square',
						lineWidth: 2
					},
					color: key == occupancyCarbonFootprintYear ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1]
				}, );
			}
		});
		Highcharts.setOptions({
			lang: {
				numericSymbols: null,
				thousandsSep: ',',
			}
		});
		Highcharts.chart('utility_cost_chart_carbon_footprint', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo lang('carbon-footprint-title'); ?>',
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
				categories:  [
			'JAN',
			'FEB',
			'MAR',
			'APR',
			'MAY',
			'JUN',
			'JUL',
			'AUG',
			'SEP',
			'OCT',
			'NOV',
			'DEC',
			'AVG'
		    ],
		    crosshair: true
			},
			yAxis: [{
				allowDecimals: false,
				min: 0,
				title: {
					text: '<?php echo lang("utility-kgco2-chart-yaxis-0-title"); ?>',
					style: {
						color: Highcharts.getOptions().colors[1],
						fontFamily: 'Arial',
						fontSize: '15px',
						fontWeight: 'bold',
					}
				}
			}, {
				min: 0,
				tick: 10,
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
			},
			series: carbonFootprintOccupancySeries
		});
	}
</script>
