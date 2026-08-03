<?php
$dataFactor = getMmbtuFactorConversionAllUtility($site_id);

$montharray     = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');



//Bar chart show last year data
if(date('m') == 1) {
	$current_year = date('Y') - 1;
	$last_year    = $current_year - 1;
}
else {
	$current_year = date('Y');
	$last_year    = $current_year - 1;
}

if ($filters['filters_comparision_chart']["start_year"] == $filters['filters_comparision_chart']["end_year"]) {

    // If start and end year is same

    if (isset($is_monthly) && $is_monthly == true) {

	$startmonthsarray[] = $filters['filters_comparision_chart']['start_month'];

    } else {

	$is_monthly = false;

	for ($i = $filters['filters_comparision_chart']['start_month']; $i <= $CURRENT_YEAR_MAX_MONTH_ID; $i++) {

	    $startmonthsarray[] = $i;

	}

    }



    $resultkeys                                                      = array();

    $resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;

} else {

    // If start and end year is not same

    for ($i = $filters['filters_comparision_chart']['start_month']; $i <= 12; $i++) {

	$startmonthsarray[] = $i;

    }



    for ($i = 1; $i <= $filters['filters_comparision_chart']['end_month']; $i++) {

	$endmonthsarray[] = $i;

    }

    $resultkeys                                                      = array();

    $resultkeys[$filters['filters_comparision_chart']["start_year"]] = $startmonthsarray;

    // $resultkeys[$filters['filters_comparision_chart']["end_year"]]   = $endmonthsarray;

}



?>

<html style="text-align: left;">

    <body width="100%">

	<div style="border:2px solid  #f69546;padding:10px;">

	
	    <table width="100%" cellpadding="4" cellspacing="4" >

		<tr>

		    <td width="100%">

			<img src="<?php echo $columnChartCarbonFootprintImg; ?>" />

		    </td>

		</tr>

		<tr>

		    <td width="100%"><?php if (!empty($utility_cost_chart)) {

			    $ci           = get_instance();

			    $total_months = 0;

			    foreach ($resultkeys as $year => $value) {

				foreach ($value as $key1 => $month) {

				    // Previous year data

				    $prevYear = $year - 1;

				    $pre_monthdata               = $montharray[$month] . ' ' . ($prevYear);

				     $pre_data_electricity        = (!empty($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'] - $utility_cost_chart[$month][$prevYear]['onsite_generator'] - $utility_cost_chart[$month][$prevYear]['renewable_energy']) : 0;

				    $pre_data_fuel               = (!empty($utility_cost_chart[$month][$prevYear]['fuel_consumption'])) ? $utility_cost_chart[$month][$prevYear]['fuel_consumption'] : 0;

				    $pre_data_lpg                = (!empty($utility_cost_chart[$month][$prevYear]['lpg_consumption'])) ? $utility_cost_chart[$month][$prevYear]['lpg_consumption'] : 0;

				    $pre_data_natural_gas        = (!empty($utility_cost_chart[$month][$prevYear]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$prevYear]['natural_gas_consumption'] : 0;

				    $pre_data_heating_district   = (!empty($utility_cost_chart[$month][$prevYear]['heating_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['heating_district_consumption'] : 0;

				    $pre_data_cooling_district   = (!empty($utility_cost_chart[$month][$prevYear]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$prevYear]['cooling_district_consumption'] : 0;

				    $pre_data_water              = (!empty($utility_cost_chart[$month][$prevYear]['water_consumption'])) ? $utility_cost_chart[$month][$prevYear]['water_consumption'] : 0;

				    $pre_data_cdd                = (!empty($utility_cost_chart[$month][$prevYear]['cdd'])) ? $utility_cost_chart[$month][$prevYear]['cdd'] : 0;

				    $pre_data_hdd                = (!empty($utility_cost_chart[$month][$prevYear]['hdd'])) ? $utility_cost_chart[$month][$prevYear]['hdd'] : 0;

				    $pre_data_occupancy          = (!empty($utility_cost_chart[$month][$prevYear]['occupancy'])) ? $utility_cost_chart[$month][$prevYear]['occupancy'] : 0;

				    $pre_data_room_night         = (!empty($utility_cost_chart[$month][$prevYear]['room_night'])) ? $utility_cost_chart[$month][$prevYear]['room_night'] : 0;
				    $pre_data_guest_night       = (!empty($utility_cost_chart[$month][$prevYear]['guest_night'])) ? $utility_cost_chart[$month][$prevYear]['guest_night'] : 0;

				    $pre_data_electricity_tariff = (!empty($utility_cost_chart[$month][$prevYear]['electricity_tariff'])) ? $utility_cost_chart[$month][$prevYear]['electricity_tariff'] : 0;

				    $pre_data_electricity_kwh    = (!empty($utility_cost_chart[$month][$prevYear]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$prevYear]['total_electricity_kwh'] : 0;



				    // Current year data

				    $monthdata               = $montharray[$month] . ' ' . $year;

				   $data_electricity        = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? ($utility_cost_chart[$month][$year]['total_electricity_kwh'] - $utility_cost_chart[$month][$year]['onsite_generator'] - $utility_cost_chart[$month][$year]['renewable_energy']) : 0;

				    $data_fuel               = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? $utility_cost_chart[$month][$year]['fuel_consumption'] : 0;

				    $data_lpg                = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;

				    $data_natural_gas        = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;

				    $data_heating_district   = (!empty($utility_cost_chart[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year]['heating_district_consumption'] : 0;

				    $data_cooling_district   = (!empty($utility_cost_chart[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year]['cooling_district_consumption'] : 0;

				    $data_water              = (!empty($utility_cost_chart[$month][$year]['water_consumption'])) ? $utility_cost_chart[$month][$year]['water_consumption'] : 0;

				    $data_cdd                = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

				    $data_hdd                = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

				    $data_occupancy          = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

				    $data_room_night         = (!empty($utility_cost_chart[$month][$year]['room_night'])) ? $utility_cost_chart[$month][$year]['room_night'] : 0;
				    $data_guest_night       = (!empty($utility_cost_chart[$month][$year]['guest_night'])) ? $utility_cost_chart[$month][$year]['guest_night'] : 0;

				    $data_electricity_tariff = (!empty($utility_cost_chart[$month][$year]['electricity_tariff'])) ? $utility_cost_chart[$month][$year]['electricity_tariff'] : 0;

				    $data_electricity_kwh    = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year]['total_electricity_kwh'] : 0;



				    // Calculate carbon footprint

				    $pre_data_electricity      = round($pre_data_electricity * $dataFactor['electricity'] * $site_detail['electricity_emission_factor'], 2);

				    $pre_data_fuel             = round($pre_data_fuel * $dataFactor['fuel_oil'] * $site_detail['fuel_emission_factor'], 2);

				    $pre_data_lpg              = round($pre_data_lpg * $dataFactor['lpg'] * $site_detail['lpg_emission_factor'], 2);

				    $pre_data_natural_gas      = round($pre_data_natural_gas * $dataFactor['natural_gas'] * $site_detail['natural_gas_emission_factor'], 2);

				    $pre_data_heating_district = round($pre_data_heating_district * $dataFactor['district_heating'] * $site_detail['district_heating_emission_factor'], 2);

				    $pre_data_cooling_district = round($pre_data_cooling_district * $dataFactor['district_cooling'] * $site_detail['district_cooling_emission_factor'], 2);

				    $pre_data_water            = 0; // There is no calculation for water data



				    $data_electricity      = round($data_electricity * $dataFactor['electricity'] * $site_detail['electricity_emission_factor'], 2);

					$data_fuel             = round($data_fuel * $dataFactor['fuel_oil'] * $site_detail['fuel_emission_factor'], 2);

				    $data_lpg              = round($data_lpg * $dataFactor['lpg'] * $site_detail['lpg_emission_factor'], 2);

				    $data_natural_gas      = round($data_natural_gas * $dataFactor['natural_gas'] * $site_detail['natural_gas_emission_factor'], 2);

				    $data_heating_district = round($data_heating_district * $dataFactor['district_heating'] * $site_detail['district_heating_emission_factor'], 2);

				    $data_cooling_district = round($data_cooling_district * $dataFactor['district_cooling'] * $site_detail['district_cooling_emission_factor'], 2);

				    $data_water            = 0; // There is no calculation for water data



				    // Round values

				    $pre_data_occupancy = round($pre_data_occupancy, 2);

				    $data_occupancy     = round($data_occupancy, 2);



				    // Total sum Previous year data

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

				    $total_sum_pre_data_room_night += $pre_data_room_night;
				    $total_sum_pre_data_guest_night += $pre_data_guest_night;

				    //$total_sum_pre_data_electricity_tariff += $pre_data_electricity_tariff;

				    $total_sum_pre_data_electricity_kwh += $pre_data_electricity_kwh;



				    // Total sum Current year data

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

				    $total_sum_data_room_night += $data_room_night;
					$total_sum_data_guest_night += $data_guest_night;

				    //$total_sum_data_electricity_tariff += $data_electricity_tariff;

				    $total_sum_data_electricity_kwh += $data_electricity_kwh;



				    $total_months++;

				}

			    }

			    if ($total_sum_pre_data_electricity_kwh > 0) {

				$total_sum_pre_data_electricity_tariff = ($total_sum_pre_data_electricity / $total_sum_pre_data_electricity_kwh);

			    } else {

				$total_sum_pre_data_electricity_tariff = 0;

			    }



			    if ($total_sum_data_electricity_kwh > 0) {

				$total_sum_data_electricity_tariff = ($total_sum_data_electricity / $total_sum_data_electricity_kwh);

			    } else {

				$total_sum_data_electricity_tariff = 0;

			    }

				$total_sum_pre_data_sum = 0;
				$total_sum_data_sum = 0;
				if (!empty($site_detail['show_utility_electricity'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_electricity;
					$total_sum_data_sum += $total_sum_data_electricity;
				}
				if (!empty($site_detail['show_utility_fuel_oil'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_fuel;
					$total_sum_data_sum += $total_sum_data_fuel;
				}
				if (!empty($site_detail['show_utility_lpg'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_lpg;
					$total_sum_data_sum += $total_sum_data_lpg;
				}
				if (!empty($site_detail['show_utility_natural_gas'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_natural_gas;
					$total_sum_data_sum += $total_sum_data_natural_gas;
				}
				if (!empty($site_detail['show_utility_district_heating'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_heating_district;
					$total_sum_data_sum += $total_sum_data_heating_district;
				}
				if (!empty($site_detail['show_utility_district_cooling'])) {
					$total_sum_pre_data_sum += $total_sum_pre_data_cooling_district;
					$total_sum_data_sum += $total_sum_data_cooling_district;
				}



			    // Variation data

			    if (!empty($total_sum_pre_data_electricity) && $total_sum_pre_data_electricity > 0) {

				$total_sum_data_electricity_variation = round(((($total_sum_data_electricity - $total_sum_pre_data_electricity) * 100) / $total_sum_pre_data_electricity), 2);

			    } else {

				if ($total_sum_data_electricity == 0) {

				    $total_sum_data_electricity_variation = 0;

				} else {

				    $total_sum_data_electricity_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_fuel) && $total_sum_pre_data_fuel > 0) {

				$total_sum_data_fuel_variation = round(((($total_sum_data_fuel - $total_sum_pre_data_fuel) * 100) / $total_sum_pre_data_fuel), 2);

			    } else {

				if ($total_sum_data_fuel == 0) {

				    $total_sum_data_fuel_variation = 0;

				} else {

				    $total_sum_data_fuel_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_lpg) && $total_sum_pre_data_lpg > 0) {

				$total_sum_data_lpg_variation = round(((($total_sum_data_lpg - $total_sum_pre_data_lpg) * 100) / $total_sum_pre_data_lpg), 2);

			    } else {

				if ($total_sum_data_lpg == 0) {

				    $total_sum_data_lpg_variation = 0;

				} else {

				    $total_sum_data_lpg_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_natural_gas) && $total_sum_pre_data_natural_gas > 0) {

				$total_sum_data_natural_gas_variation = round(((($total_sum_data_natural_gas - $total_sum_pre_data_natural_gas) * 100) / $total_sum_pre_data_natural_gas), 2);

			    } else {

				if ($total_sum_data_natural_gas == 0) {

				    $total_sum_data_natural_gas_variation = 0;

				} else {

				    $total_sum_data_natural_gas_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_heating_district) && $total_sum_pre_data_heating_district > 0) {

				$total_sum_data_heating_district_variation = round(((($total_sum_data_heating_district - $total_sum_pre_data_heating_district) * 100) / $total_sum_pre_data_heating_district), 2);

			    } else {

				if ($total_sum_data_heating_district == 0) {

				    $total_sum_data_heating_district_variation = 0;

				} else {

				    $total_sum_data_heating_district_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_cooling_district) && $total_sum_pre_data_cooling_district > 0) {

				$total_sum_data_cooling_district_variation = round(((($total_sum_data_cooling_district - $total_sum_pre_data_cooling_district) * 100) / $total_sum_pre_data_cooling_district), 2);

			    } else {

				if ($total_sum_data_cooling_district == 0) {

				    $total_sum_data_cooling_district_variation = 0;

				} else {

				    $total_sum_data_cooling_district_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_water) && $total_sum_pre_data_water > 0) {

				$total_sum_data_water_variation = round(((($total_sum_data_water - $total_sum_pre_data_water) * 100) / $total_sum_pre_data_water), 2);

			    } else {

				if ($total_sum_data_water == 0) {

				    $total_sum_data_water_variation = 0;

				} else {

				    $total_sum_data_water_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_cdd) && $total_sum_pre_data_cdd > 0) {

				$total_sum_data_cdd_variation = round(((($total_sum_data_cdd - $total_sum_pre_data_cdd) * 100) / $total_sum_pre_data_cdd), 2);

			    } else {

				if ($total_sum_data_cdd == 0) {

				    $total_sum_data_cdd_variation = 0;

				} else {

				    $total_sum_data_cdd_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_hdd) && $total_sum_pre_data_hdd > 0) {

				$total_sum_data_hdd_variation = round(((($total_sum_data_hdd - $total_sum_pre_data_hdd) * 100) / $total_sum_pre_data_hdd), 2);

			    } else {

				if ($total_sum_data_hdd == 0) {

				    $total_sum_data_hdd_variation = 0;

				} else {

				    $total_sum_data_hdd_variation = 100;

				}

			    }

			    if (!empty($total_sum_pre_data_occupancy) && $total_sum_pre_data_occupancy > 0) {

				$total_sum_data_occupancy_variation = round(((($total_sum_data_occupancy - $total_sum_pre_data_occupancy) * 100) / $total_sum_pre_data_occupancy), 2);

			    } else {

				if ($total_sum_data_occupancy == 0) {

				    $total_sum_data_occupancy_variation = 0;

				} else {

				    $total_sum_data_occupancy_variation = 100;

				}

			    }



			    if (!empty($total_sum_pre_data_room_night) && $total_sum_pre_data_room_night > 0) {

				$total_sum_data_room_night_variation = round(((($total_sum_data_room_night - $total_sum_pre_data_room_night) * 100) / $total_sum_pre_data_room_night), 2);

			    } else {

				if ($total_sum_data_room_night == 0) {

				    $total_sum_data_room_night_variation = 0;

				} else {

				    $total_sum_data_room_night_variation = 100;

				}

			    }

				if (!empty($total_sum_pre_data_guest_night) && $total_sum_pre_data_guest_night > 0) {

				$total_sum_data_guest_night_variation = round(((($total_sum_data_guest_night - $total_sum_pre_data_guest_night) * 100) / $total_sum_pre_data_guest_night), 2);

			    } else {

				if ($total_sum_data_guest_night == 0) {

				    $total_sum_data_guest_night_variation = 0;

				} else {

				    $total_sum_data_guest_night_variation = 100;

				}

			    }



			    if (!empty($total_sum_pre_data_electricity_tariff) && $total_sum_pre_data_electricity_tariff > 0) {

				$total_sum_data_electricity_tariff_variation = round(((($total_sum_data_electricity_tariff - $total_sum_pre_data_electricity_tariff) * 100) / $total_sum_pre_data_electricity_tariff), 2);

			    } else {

				if ($total_sum_data_electricity_tariff == 0) {

				    $total_sum_data_electricity_tariff_variation = 0;

				} else {

				    $total_sum_data_electricity_tariff_variation = 100;

				}

			    }



			    // Total variation

			    if (!empty($total_sum_pre_data_sum) && $total_sum_pre_data_sum > 0) {

				$total_sum_data_variation = round(((($total_sum_data_sum - $total_sum_pre_data_sum) * 100) / $total_sum_pre_data_sum), 2);

			    } else {

				if ($total_sum_data_sum == 0) {

				    $total_sum_data_variation = 0;

				} else {

				    $total_sum_data_variation = 100;

				}

			    }

				$pre_roomnight_intensity = (!empty($total_sum_pre_data_room_night) && $total_sum_pre_data_room_night > 0) ? ($total_sum_pre_data_sum / $total_sum_pre_data_room_night) : 0;
				$data_roomnight_intensity = (!empty($total_sum_data_room_night) && $total_sum_data_room_night > 0) ? ($total_sum_data_sum / $total_sum_data_room_night) : 0;
				if ($pre_roomnight_intensity > 0) {
					$total_roomnight_data_variation = round(((($data_roomnight_intensity - $pre_roomnight_intensity) * 100) / $pre_roomnight_intensity), 2);
				} else {
					$total_roomnight_data_variation = ($data_roomnight_intensity == 0) ? 0 : 100;
				}

				$pre_guestnight_intensity = (!empty($total_sum_pre_data_guest_night) && $total_sum_pre_data_guest_night > 0) ? ($total_sum_pre_data_sum / $total_sum_pre_data_guest_night) : 0;
				$data_guestnight_intensity = (!empty($total_sum_data_guest_night) && $total_sum_data_guest_night > 0) ? ($total_sum_data_sum / $total_sum_data_guest_night) : 0;
				if ($pre_guestnight_intensity > 0) {
					$total_guestnights_data_variation = round(((($data_guestnight_intensity - $pre_guestnight_intensity) * 100) / $pre_guestnight_intensity), 2);
				} else {
					$total_guestnights_data_variation = ($data_guestnight_intensity == 0) ? 0 : 100;
				}

			    //$total_sum_data_variation = ($total_sum_data_electricity_variation+$total_sum_data_fuel_variation+$total_sum_data_lpg_variation+$total_sum_data_natural_gas_variation+$total_sum_data_water_variation+$total_sum_data_heating_district_variation+$total_sum_data_cooling_district_variation);

			    ?>

			    <table border="1" width="100%" cellpadding="4" cellspacing="0">

				<thead>

				    <tr>

					<th width="38%" align="center" style="background-color:#d8e1f2;"><strong>

					    <?php if ($is_monthly): ?>

						<?php echo $fullmontharray[$filters['filters_comparision_chart']['start_month']] . " - " . ($last_year) ?>

					    <?php else: ?>

						YTD - Previous Year <?php echo $last_year; ?>

					    <?php endif?>

					    </strong>

					</th>

					<th width="38%" align="center" style="background-color:#d8e1f2;"><strong>

					    <?php if ($is_monthly): ?>

						<?php echo $fullmontharray[$filters['filters_comparision_chart']['start_month']] . " - " . $year ?>

					    <?php else: ?>

						YTD - Current Year <?php echo $year; ?>

					    <?php endif?>


					    </strong>

					</th>

					<th width="24%" align="center" style="background-color:#d8e1f2;"><strong>Variation</strong></th>

				    </tr>

				</thead>

				<tbody>

				    <tr>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<thead>

						    <tr>

							<th width="60%"><strong>Utilities</strong></th>

							<th width="40%"><strong>CO<sub>2</sub> (kgCO<sub>2</sub>e)</strong></th>

						    </tr>

						</thead>

					    </table>

					</td>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<thead>

						    <tr>

							<th width="60%"><strong>Utilities</strong></th>

							<th width="40%"><strong>CO<sub>2</sub> (kgCO<sub>2</sub>e)</strong></th>

						    </tr>

						</thead>

					    </table>

					</td>

					<td width="24%" align="center">

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

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tbody>

						    <?php if ($totalElectricity && $site_detail['show_utility_electricity']) {?>

						    <tr>

							<td width="70%">Electricity</td>

							<td width="30%"><?php echo number_format($total_sum_pre_data_electricity); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalFuel  && $site_detail['show_utility_fuel_oil']) {?>

						    <tr>

							<td>Fuel</td>

							<td><?php echo number_format($total_sum_pre_data_fuel); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalLpg && $site_detail['show_utility_lpg']) {?>

						    <tr>

							<td>LPG</td>

							<td><?php echo number_format($total_sum_pre_data_lpg); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalNaturalGas && $site_detail['show_utility_natural_gas']) {?>

						    <tr>

							<td>Natural Gas</td>

							<td><?php echo number_format($total_sum_pre_data_natural_gas); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalHeatingDistrict && $site_detail['show_utility_district_heating']) {?>

						    <tr>

							<td>District Heating</td>

							<td><?php echo number_format($total_sum_pre_data_heating_district); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalCoolingDistrict && $site_detail['show_utility_district_cooling']) {?>

						    <tr>

							<td>District Cooling</td>

							<td><?php echo number_format($total_sum_pre_data_cooling_district); ?></td>

						    </tr>

						    <?php }?>



						</tbody>

					    </table>

					</td>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tbody>

						    <?php if ($totalElectricity && $site_detail['show_utility_electricity']) {?>

						    <tr>

							<td width="70%">Electricity</td>

							<td width="30%"><?php echo number_format($total_sum_data_electricity); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalFuel && $site_detail['show_utility_fuel_oil']) {?>

						    <tr>

							<td>Fuel</td>

							<td><?php echo number_format($total_sum_data_fuel); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalLpg && $site_detail['show_utility_lpg']) {?>

						    <tr>

							<td>LPG</td>

							<td><?php echo number_format($total_sum_data_lpg); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalNaturalGas && $site_detail['show_utility_natural_gas']) {?>

						    <tr>

							<td>Natural Gas</td>

							<td><?php echo number_format($total_sum_data_natural_gas); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalHeatingDistrict && $site_detail['show_utility_district_heating']) {?>

						    <tr>

							<td>District Heating</td>

							<td><?php echo number_format($total_sum_data_heating_district); ?></td>

						    </tr>

						    <?php }?>



						    <?php if ($totalCoolingDistrict && $site_detail['show_utility_district_cooling']) {?>

						    <tr>

							<td>District Cooling</td>

							<td><?php echo number_format($total_sum_data_cooling_district); ?></td>

						    </tr>

						    <?php }?>



						</tbody>

					    </table>

					</td>

					<td width="24%" align="center">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tbody>

						    <?php if ($totalElectricity && $site_detail['show_utility_electricity']) {?>

						    <tr>

							<td><?php echo $total_sum_data_electricity_variation; ?>%</td>

						    </tr>

						    <?php }?>



						    <?php if ($totalFuel && $site_detail['show_utility_fuel_oil']) {?>

						    <tr>

							<td><?php echo $total_sum_data_fuel_variation; ?>%</td>

						    </tr>

						    <?php }?>



						    <?php if ($totalLpg && $site_detail['show_utility_lpg']) {?>

						    <tr>

							<td><?php echo $total_sum_data_lpg_variation; ?>%</td>

						    </tr>

						    <?php }?>



						    <?php if ($totalNaturalGas && $site_detail['show_utility_natural_gas']) {?>

						    <tr>

							<td><?php echo $total_sum_data_natural_gas_variation; ?>%</td>

						    </tr>

						    <?php }?>



						    <?php if ($totalHeatingDistrict && $site_detail['show_utility_district_heating']) {?>

						    <tr>

							<td><?php echo $total_sum_data_heating_district_variation; ?>%</td>

						    </tr>

						    <?php }?>



						    <?php if ($totalCoolingDistrict && $site_detail['show_utility_district_cooling']) {?>

						    <tr>

							<td><?php echo $total_sum_data_cooling_district_variation; ?>%</td>

						    </tr>

						    <?php }?>



						</tbody>

					    </table>

					</td>

				    </tr>

				    <tr>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td width="70%"><strong>Total</strong></td>

						    <td width="30%"><strong><?php echo number_format($total_sum_pre_data_sum); ?></strong></td>

						</tr>

					    </table>

					</td>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td width="70%"></td>

						    <td width="30%"><strong><?php echo number_format($total_sum_data_sum); ?></strong></td>

						</tr>

					    </table>

					</td>

					<td width="24%" align="center">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td>

							<strong><?php echo $total_sum_data_variation; ?>%</strong>

						    </td>

						</tr>

					    </table>

					</td>

				    </tr>

					 <tr>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td width="70%"><strong>kgCO<sub>2</sub>e / Guest Nights </strong></td>

						    <td width="30%"><strong><?php echo number_format($pre_guestnight_intensity, 2); ?></strong></td>

						</tr>

					    </table>

					</td>

					<td width="38%">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td width="70%"></td>

						    <td width="30%"><strong><?php echo number_format($data_guestnight_intensity, 2); ?></strong></td>

						</tr>

					    </table>

					</td>

					<td width="24%" align="center">

					    <table width="100%" cellpadding="0" cellspacing="0">

						<tr>

						    <td>

							<strong><?php echo $total_guestnights_data_variation; ?>%</strong>

						    </td>

						</tr>

					    </table>

					</td>

				    </tr>

				</tbody>

			    </table>

			<?php

			}?>

		    </td>

		</tr>

	    </table>

	</div>

    </body>

</html>
