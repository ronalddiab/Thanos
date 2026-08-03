<?php

$isLocal = true;

if ($currency == "base") {

    $isLocal = false;

}

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$percentage_decimal = 3;

$value_decimal = 4;

//Bar chart show last year data

$current_year = date('Y');

$last_year = $current_year - 1;



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



if (!empty($utility_cost_chart)) {

    $ci = get_instance();

    $total_months = 0;

    foreach ($resultkeys as $year => $value) {

	foreach ($value as $key1 => $month) {

	    // Previous year data

	    $pre_monthdata = $montharray[$month] . ' ' . ($year - 1);

	    $pre_data_electricity = (!empty($utility_cost_chart[$month][$year - 1]['electricity'])) ? $utility_cost_chart[$month][$year - 1]['electricity'] : 0;

	    $pre_data_fuel = (!empty($utility_cost_chart[$month][$year - 1]['fuel'])) ? $utility_cost_chart[$month][$year - 1]['fuel'] : 0;

	    $pre_data_fuel_consumption = (!empty($utility_cost_chart[$month][$year - 1]['fuel_consumption'])) ? $utility_cost_chart[$month][$year - 1]['fuel_consumption'] : 0;

	    $pre_data_lpg = (!empty($utility_cost_chart[$month][$year - 1]['lpg'])) ? $utility_cost_chart[$month][$year - 1]['lpg'] : 0;

	    $pre_data_lpg_consumption = (!empty($utility_cost_chart[$month][$year - 1]['lpg_consumption'])) ? $utility_cost_chart[$month][$year - 1]['lpg_consumption'] : 0;

	    $pre_data_natural_gas = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas'] : 0;

	    $pre_data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year - 1]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year - 1]['natural_gas_consumption'] : 0;

	    $pre_data_heating_district = (!empty($utility_cost_chart[$month][$year - 1]['heating_district'])) ? $utility_cost_chart[$month][$year - 1]['heating_district'] : 0;

	    $pre_data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['heating_district_consumption'] : 0;

	    $pre_data_cooling_district = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district'] : 0;

	    $pre_data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year - 1]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year - 1]['cooling_district_consumption'] : 0;

	    $pre_data_water = (!empty($utility_cost_chart[$month][$year - 1]['water'])) ? $utility_cost_chart[$month][$year - 1]['water'] : 0;

	    $pre_data_water_consumption = (!empty($utility_cost_chart[$month][$year - 1]['water_consumption'])) ? $utility_cost_chart[$month][$year - 1]['water_consumption'] : 0;

	    $pre_data_cdd = (!empty($utility_cost_chart[$month][$year - 1]['cdd'])) ? $utility_cost_chart[$month][$year - 1]['cdd'] : 0;

	    $pre_data_hdd = (!empty($utility_cost_chart[$month][$year - 1]['hdd'])) ? $utility_cost_chart[$month][$year - 1]['hdd'] : 0;

	    $pre_data_occupancy = (!empty($utility_cost_chart[$month][$year - 1]['occupancy'])) ? $utility_cost_chart[$month][$year - 1]['occupancy'] : 0;

	    $pre_data_room_night = (!empty($utility_cost_chart[$month][$year - 1]['room_night'])) ? $utility_cost_chart[$month][$year - 1]['room_night'] : 0;

	    $pre_data_electricity_tariff = (!empty($utility_cost_chart[$month][$year - 1]['electricity_tariff'])) ? $utility_cost_chart[$month][$year - 1]['electricity_tariff'] : 0;

	    $pre_data_electricity_kwh = (!empty($utility_cost_chart[$month][$year - 1]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year - 1]['total_electricity_kwh'] : 0;



	    // Current year data

	    $monthdata = $montharray[$month] . ' ' . $year;

	    $data_electricity = (!empty($utility_cost_chart[$month][$year]['electricity'])) ? $utility_cost_chart[$month][$year]['electricity'] : 0;

	    $data_fuel = (!empty($utility_cost_chart[$month][$year]['fuel'])) ? $utility_cost_chart[$month][$year]['fuel'] : 0;

	    $data_fuel_consumption = (!empty($utility_cost_chart[$month][$year]['fuel_consumption'])) ? $utility_cost_chart[$month][$year]['fuel_consumption'] : 0;

	    $data_lpg = (!empty($utility_cost_chart[$month][$year]['lpg'])) ? $utility_cost_chart[$month][$year]['lpg'] : 0;

	    $data_lpg_consumption = (!empty($utility_cost_chart[$month][$year]['lpg_consumption'])) ? $utility_cost_chart[$month][$year]['lpg_consumption'] : 0;

	    $data_natural_gas = (!empty($utility_cost_chart[$month][$year]['natural_gas'])) ? $utility_cost_chart[$month][$year]['natural_gas'] : 0;

	    $data_natural_gas_consumption = (!empty($utility_cost_chart[$month][$year]['natural_gas_consumption'])) ? $utility_cost_chart[$month][$year]['natural_gas_consumption'] : 0;

	    $data_heating_district = (!empty($utility_cost_chart[$month][$year]['heating_district'])) ? $utility_cost_chart[$month][$year]['heating_district'] : 0;

	    $data_heating_district_consumption = (!empty($utility_cost_chart[$month][$year]['heating_district_consumption'])) ? $utility_cost_chart[$month][$year]['heating_district_consumption'] : 0;

	    $data_cooling_district = (!empty($utility_cost_chart[$month][$year]['cooling_district'])) ? $utility_cost_chart[$month][$year]['cooling_district'] : 0;

	    $data_cooling_district_consumption = (!empty($utility_cost_chart[$month][$year]['cooling_district_consumption'])) ? $utility_cost_chart[$month][$year]['cooling_district_consumption'] : 0;

	    $data_water = (!empty($utility_cost_chart[$month][$year]['water'])) ? $utility_cost_chart[$month][$year]['water'] : 0;

	    $data_water_consumption = (!empty($utility_cost_chart[$month][$year]['water_consumption'])) ? $utility_cost_chart[$month][$year]['water_consumption'] : 0;

	    $data_cdd = (!empty($utility_cost_chart[$month][$year]['cdd'])) ? $utility_cost_chart[$month][$year]['cdd'] : 0;

	    $data_hdd = (!empty($utility_cost_chart[$month][$year]['hdd'])) ? $utility_cost_chart[$month][$year]['hdd'] : 0;

	    $data_occupancy = (!empty($utility_cost_chart[$month][$year]['occupancy'])) ? $utility_cost_chart[$month][$year]['occupancy'] : 0;

	    $data_room_night = (!empty($utility_cost_chart[$month][$year]['room_night'])) ? $utility_cost_chart[$month][$year]['room_night'] : 0;

	    $data_electricity_tariff = (!empty($utility_cost_chart[$month][$year]['electricity_tariff'])) ? $utility_cost_chart[$month][$year]['electricity_tariff'] : 0;

	    $data_electricity_kwh = (!empty($utility_cost_chart[$month][$year]['total_electricity_kwh'])) ? $utility_cost_chart[$month][$year]['total_electricity_kwh'] : 0;



	    // Round values

	    $pre_data_occupancy = round($pre_data_occupancy, 2);

	    $data_occupancy = round($data_occupancy, 2);



	    // Total sum Previous year data

	    $total_sum_pre_data_electricity += $pre_data_electricity;

	    $total_sum_pre_data_fuel += $pre_data_fuel;

	    $total_sum_pre_data_fuel_consumption += $pre_data_fuel_consumption;

	    $total_sum_pre_data_lpg += $pre_data_lpg;

	    $total_sum_pre_data_lpg_consumption += $pre_data_lpg_consumption;

	    $total_sum_pre_data_natural_gas += $pre_data_natural_gas;

	    $total_sum_pre_data_natural_gas_consumption += $pre_data_natural_gas_consumption;

	    $total_sum_pre_data_heating_district += $pre_data_heating_district;

	    $total_sum_pre_data_heating_district_consumption += $pre_data_heating_district_consumption;

	    $total_sum_pre_data_cooling_district += $pre_data_cooling_district;

	    $total_sum_pre_data_cooling_district_consumption += $pre_data_cooling_district_consumption;

	    $total_sum_pre_data_water += $pre_data_water;

	    $total_sum_pre_data_water_consumption += $pre_data_water_consumption;

	    $total_sum_pre_data_cdd += $pre_data_cdd;

	    $total_sum_pre_data_hdd += $pre_data_hdd;

	    $total_sum_pre_data_occupancy += $pre_data_occupancy;

	    $total_sum_pre_data_room_night += $pre_data_room_night;

	    //$total_sum_pre_data_electricity_tariff += $pre_data_electricity_tariff;

	    $total_sum_pre_data_electricity_kwh += $pre_data_electricity_kwh;



	    // Total sum Current year data

	    $total_sum_data_electricity += $data_electricity;

	    $total_sum_data_fuel += $data_fuel;

	    $total_sum_data_fuel_consumption += $data_fuel_consumption;

	    $total_sum_data_lpg += $data_lpg;

	    $total_sum_data_lpg_consumption += $data_lpg_consumption;

	    $total_sum_data_natural_gas += $data_natural_gas;

	    $total_sum_data_natural_gas_consumption += $data_natural_gas_consumption;

	    $total_sum_data_heating_district += $data_heating_district;

	    $total_sum_data_heating_district_consumption += $data_heating_district_consumption;

	    $total_sum_data_cooling_district += $data_cooling_district;

	    $total_sum_data_cooling_district_consumption += $data_cooling_district_consumption;

	    $total_sum_data_water += $data_water;

	    $total_sum_data_water_consumption += $data_water_consumption;

	    $total_sum_data_cdd += $data_cdd;

	    $total_sum_data_hdd += $data_hdd;

	    $total_sum_data_occupancy += $data_occupancy;

	    $total_sum_data_room_night += $data_room_night;

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



    $total_sum_pre_data_sum = ($total_sum_pre_data_electricity + $total_sum_pre_data_fuel + $total_sum_pre_data_lpg + $total_sum_pre_data_natural_gas + $total_sum_pre_data_water + $total_sum_pre_data_heating_district + $total_sum_pre_data_cooling_district);

    $total_sum_data_sum = ($total_sum_data_electricity + $total_sum_data_fuel + $total_sum_data_lpg + $total_sum_data_natural_gas + $total_sum_data_water + $total_sum_data_heating_district + $total_sum_data_cooling_district);

    $total_sum_difference_value = $total_sum_data_sum - $total_sum_pre_data_sum;

    if (!empty($total_sum_pre_data_sum)) {

	$total_sum_difference_percent = round($total_sum_difference_value * 100 / $total_sum_pre_data_sum, $percentage_decimal);

    } else {

	$total_sum_difference_percent = 0;

    }



    if (!empty($total_sum_data_fuel)) {

	$total_sum_data_fuel_tariff = round($total_sum_data_fuel / $total_sum_data_fuel_consumption, $value_decimal);

    } else {

	$total_sum_data_fuel_tariff = 0;

    }



    if (!empty($total_sum_pre_data_fuel)) {

	$total_sum_pre_data_fuel_tariff = round($total_sum_pre_data_fuel / $total_sum_pre_data_fuel_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_fuel_tariff = 0;

    }



    if (!empty($total_sum_data_lpg)) {

	$total_sum_data_lpg_tariff = round($total_sum_data_lpg / $total_sum_data_lpg_consumption, $value_decimal);

    } else {

	$total_sum_data_lpg_tariff = 0;

    }



    if (!empty($total_sum_pre_data_lpg)) {

	$total_sum_pre_data_lpg_tariff = round($total_sum_pre_data_lpg / $total_sum_pre_data_lpg_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_lpg_tariff = 0;

    }



    if (!empty($total_sum_data_natural_gas)) {

	$total_sum_data_natural_gas_tariff = round($total_sum_data_natural_gas / $total_sum_data_natural_gas_consumption, $value_decimal);

    } else {

	$total_sum_data_natural_gas_tariff = 0;

    }



    if (!empty($total_sum_pre_data_natural_gas)) {

	$total_sum_pre_data_natural_gas_tariff = round($total_sum_pre_data_natural_gas / $total_sum_pre_data_natural_gas_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_natural_gas_tariff = 0;

    }



    if (!empty($total_sum_data_heating_district)) {

	$total_sum_data_heating_district_tariff = round($total_sum_data_heating_district / $total_sum_data_heating_district_consumption, $value_decimal);

    } else {

	$total_sum_data_heating_district_tariff = 0;

    }



    if (!empty($total_sum_pre_data_heating_district)) {

	$total_sum_pre_data_heating_district_tariff = round($total_sum_pre_data_heating_district / $total_sum_pre_data_heating_district_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_heating_district_tariff = 0;

    }



    if (!empty($total_sum_data_cooling_district)) {

	$total_sum_data_cooling_district_tariff = round($total_sum_data_cooling_district / $total_sum_data_cooling_district_consumption, $value_decimal);

    } else {

	$total_sum_data_cooling_district_tariff = 0;

    }



    if (!empty($total_sum_pre_data_cooling_district)) {

	$total_sum_pre_data_cooling_district_tariff = round($total_sum_pre_data_cooling_district / $total_sum_pre_data_cooling_district_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_cooling_district_tariff = 0;

    }



    if (!empty($total_sum_data_water)) {

	$total_sum_data_water_tariff = round($total_sum_data_water / $total_sum_data_water_consumption, $value_decimal);

    } else {

	$total_sum_data_water_tariff = 0;

    }



    if (!empty($total_sum_pre_data_water)) {

	$total_sum_pre_data_water_tariff = round($total_sum_pre_data_water / $total_sum_pre_data_water_consumption, $value_decimal);

    } else {

	$total_sum_pre_data_water_tariff = 0;

    }



    if (!empty($total_sum_pre_data_room_night)) {

	$total_sum_pre_data_fuel_per_room_night = round($total_sum_pre_data_fuel_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_lpg_per_room_night = round($total_sum_pre_data_lpg_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_natural_gas_per_room_night = round($total_sum_pre_data_natural_gas_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_heating_district_per_room_night = round($total_sum_pre_data_heating_district_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_cooling_district_per_room_night = round($total_sum_pre_data_cooling_district_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_water_per_room_night = round($total_sum_pre_data_water_consumption / $total_sum_pre_data_room_night, $percentage_decimal);

	$total_sum_pre_data_electricity_per_room_night = round($total_sum_pre_data_electricity_kwh / $total_sum_pre_data_room_night, $percentage_decimal);

    } else {

	$total_sum_pre_data_fuel_per_room_night = 0;

	$total_sum_pre_data_lpg_per_room_night = 0;

	$total_sum_pre_data_natural_gas_per_room_night = 0;

	$total_sum_pre_data_heating_district_per_room_night = 0;

	$total_sum_pre_data_cooling_district_per_room_night = 0;

	$total_sum_pre_data_water_per_room_night = 0;

	$total_sum_pre_data_electricity_per_room_night = 0;

    }



    if (!empty($total_sum_data_room_night)) {

	$total_sum_data_fuel_per_room_night = round($total_sum_data_fuel_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_lpg_per_room_night = round($total_sum_data_lpg_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_natural_gas_per_room_night = round($total_sum_data_natural_gas_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_heating_district_per_room_night = round($total_sum_data_heating_district_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_cooling_district_per_room_night = round($total_sum_data_cooling_district_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_water_per_room_night = round($total_sum_data_water_consumption / $total_sum_data_room_night, $percentage_decimal);

	$total_sum_data_electricity_per_room_night = round($total_sum_data_electricity_kwh / $total_sum_data_room_night, $percentage_decimal);

    } else {

	$total_sum_data_fuel_per_room_night = 0;

	$total_sum_data_lpg_per_room_night = 0;

	$total_sum_data_natural_gas_per_room_night = 0;

	$total_sum_data_heating_district_per_room_night = 0;

	$total_sum_data_cooling_district_per_room_night = 0;

	$total_sum_data_water_per_room_night = 0;

	$total_sum_data_electricity_per_room_night = 0;

    }



    $fuel_per_room_night_difference_value = $total_sum_data_fuel_per_room_night - $total_sum_pre_data_fuel_per_room_night;

    if (!empty($total_sum_pre_data_fuel_per_room_night)) {

	$fuel_per_room_night_difference_percent = round($fuel_per_room_night_difference_value * 100 / $total_sum_pre_data_fuel_per_room_night, $percentage_decimal);

    } else {

	$fuel_per_room_night_difference_percent = 0;

    }



    $lpg_per_room_night_difference_value = $total_sum_data_lpg_per_room_night - $total_sum_pre_data_lpg_per_room_night;

    if (!empty($total_sum_pre_data_lpg_per_room_night)) {

	$lpg_per_room_night_difference_percent = round($lpg_per_room_night_difference_value * 100 / $total_sum_pre_data_lpg_per_room_night, $percentage_decimal);

    } else {

	$lpg_per_room_night_difference_percent = 0;

    }



    $natural_gas_per_room_night_difference_value = $total_sum_data_natural_gas_per_room_night - $total_sum_pre_data_natural_gas_per_room_night;

    if (!empty($total_sum_pre_data_natural_gas_per_room_night)) {

	$natural_gas_per_room_night_difference_percent = round($natural_gas_per_room_night_difference_value * 100 / $total_sum_pre_data_natural_gas_per_room_night, $percentage_decimal);

    } else {

	$natural_gas_per_room_night_difference_percent = 0;

    }



    $heating_district_per_room_night_difference_value = $total_sum_data_heating_district_per_room_night - $total_sum_pre_data_heating_district_per_room_night;

    if (!empty($total_sum_pre_data_heating_district_per_room_night)) {

	$heating_district_per_room_night_difference_percent = round($heating_district_per_room_night_difference_value * 100 / $total_sum_pre_data_heating_district_per_room_night, $percentage_decimal);

    } else {

	$heating_district_per_room_night_difference_percent = 0;

    }



    $cooling_district_per_room_night_difference_value = $total_sum_data_cooling_district_per_room_night - $total_sum_pre_data_cooling_district_per_room_night;

    if (!empty($total_sum_pre_data_cooling_district_per_room_night)) {

	$cooling_district_per_room_night_difference_percent = round($cooling_district_per_room_night_difference_value * 100 / $total_sum_pre_data_cooling_district_per_room_night, $percentage_decimal);

    } else {

	$cooling_district_per_room_night_difference_percent = 0;

    }



    $water_per_room_night_difference_value = $total_sum_data_water_per_room_night - $total_sum_pre_data_water_per_room_night;

    if (!empty($total_sum_pre_data_water_per_room_night)) {

	$water_per_room_night_difference_percent = round($water_per_room_night_difference_value * 100 / $total_sum_pre_data_water_per_room_night, $percentage_decimal);

    } else {

	$water_per_room_night_difference_percent = 0;

    }



    $electricity_per_room_night_difference_value = $total_sum_data_electricity_per_room_night - $total_sum_pre_data_electricity_per_room_night;

    if (!empty($total_sum_pre_data_electricity_per_room_night)) {

	$electricity_per_room_night_difference_percent = round($electricity_per_room_night_difference_value * 100 / $total_sum_pre_data_electricity_per_room_night, $percentage_decimal);

    } else {

	$electricity_per_room_night_difference_percent = 0;

    }



    // Variation data

    $electricity_consumption_difference = $total_sum_data_electricity_kwh - $total_sum_pre_data_electricity_kwh;

    $fuel_consumption_difference = $total_sum_data_fuel_consumption - $total_sum_pre_data_fuel_consumption;

    $lpg_consumption_difference = $total_sum_data_lpg_consumption - $total_sum_pre_data_lpg_consumption;

    $natural_gas_consumption_difference = $total_sum_data_natural_gas_consumption - $total_sum_pre_data_natural_gas_consumption;

    $heating_district_consumption_difference = $total_sum_data_heating_district_consumption - $total_sum_pre_data_heating_district_consumption;

    $cooling_district_consumption_difference = $total_sum_data_cooling_district_consumption - $total_sum_pre_data_cooling_district_consumption;

    $water_consumption_difference = $total_sum_data_water_consumption - $total_sum_pre_data_water_consumption;



    if (!empty($total_sum_pre_data_electricity_kwh)) {

	$electricity_consumption_variation = round($electricity_consumption_difference * 100 / $total_sum_pre_data_electricity_kwh, $percentage_decimal);

    } else {

	$electricity_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_fuel_consumption)) {

	$fuel_consumption_variation = round($fuel_consumption_difference * 100 / $total_sum_pre_data_fuel_consumption, $percentage_decimal);

    } else {

	$fuel_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_lpg_consumption)) {

	$lpg_consumption_variation = round($lpg_consumption_difference * 100 / $total_sum_pre_data_lpg_consumption, $percentage_decimal);

    } else {

	$lpg_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_natural_gas_consumption)) {

	$natural_gas_consumption_variation = round($natural_gas_consumption_difference * 100 / $total_sum_pre_data_natural_gas_consumption, $percentage_decimal);

    } else {

	$natural_gas_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_heating_district_consumption)) {

	$heating_district_consumption_variation = round($heating_district_consumption_difference * 100 / $total_sum_pre_data_heating_district_consumption, $percentage_decimal);

    } else {

	$heating_district_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_cooling_district_consumption)) {

	$cooling_district_consumption_variation = round($cooling_district_consumption_difference * 100 / $total_sum_pre_data_cooling_district_consumption, $percentage_decimal);

    } else {

	$cooling_district_consumption_variation = 0;

    }



    if (!empty($total_sum_pre_data_water_consumption)) {

	$water_consumption_variation = round($water_consumption_difference * 100 / $total_sum_pre_data_water_consumption, $percentage_decimal);

    } else {

	$water_consumption_variation = 0;

    }





    if (!empty($total_sum_pre_data_electricity) && $total_sum_pre_data_electricity > 0) {

	$total_sum_data_electricity_variation = round(((($total_sum_data_electricity - $total_sum_pre_data_electricity) * 100) / $total_sum_pre_data_electricity), $percentage_decimal);

    } else {

	if ($total_sum_data_electricity == 0) {

	    $total_sum_data_electricity_variation = 0;

	} else {

	    $total_sum_data_electricity_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_fuel) && $total_sum_pre_data_fuel > 0) {

	$total_sum_data_fuel_variation = round(((($total_sum_data_fuel - $total_sum_pre_data_fuel) * 100) / $total_sum_pre_data_fuel), $percentage_decimal);

    } else {

	if ($total_sum_data_fuel == 0) {

	    $total_sum_data_fuel_variation = 0;

	} else {

	    $total_sum_data_fuel_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_lpg) && $total_sum_pre_data_lpg > 0) {

	$total_sum_data_lpg_variation = round(((($total_sum_data_lpg - $total_sum_pre_data_lpg) * 100) / $total_sum_pre_data_lpg), $percentage_decimal);

    } else {

	if ($total_sum_data_lpg == 0) {

	    $total_sum_data_lpg_variation = 0;

	} else {

	    $total_sum_data_lpg_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_natural_gas) && $total_sum_pre_data_natural_gas > 0) {

	$total_sum_data_natural_gas_variation = round(((($total_sum_data_natural_gas - $total_sum_pre_data_natural_gas) * 100) / $total_sum_pre_data_natural_gas), $percentage_decimal);

    } else {

	if ($total_sum_data_natural_gas == 0) {

	    $total_sum_data_natural_gas_variation = 0;

	} else {

	    $total_sum_data_natural_gas_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_heating_district) && $total_sum_pre_data_heating_district > 0) {

	$total_sum_data_heating_district_variation = round(((($total_sum_data_heating_district - $total_sum_pre_data_heating_district) * 100) / $total_sum_pre_data_heating_district), $percentage_decimal);

    } else {

	if ($total_sum_data_heating_district == 0) {

	    $total_sum_data_heating_district_variation = 0;

	} else {

	    $total_sum_data_heating_district_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_cooling_district) && $total_sum_pre_data_cooling_district > 0) {

	$total_sum_data_cooling_district_variation = round(((($total_sum_data_cooling_district - $total_sum_pre_data_cooling_district) * 100) / $total_sum_pre_data_cooling_district), $percentage_decimal);

    } else {

	if ($total_sum_data_cooling_district == 0) {

	    $total_sum_data_cooling_district_variation = 0;

	} else {

	    $total_sum_data_cooling_district_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_water) && $total_sum_pre_data_water > 0) {

	$total_sum_data_water_variation = round(((($total_sum_data_water - $total_sum_pre_data_water) * 100) / $total_sum_pre_data_water), $percentage_decimal);

    } else {

	if ($total_sum_data_water == 0) {

	    $total_sum_data_water_variation = 0;

	} else {

	    $total_sum_data_water_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_cdd) && $total_sum_pre_data_cdd > 0) {

	$total_sum_data_cdd_variation = round(((($total_sum_data_cdd - $total_sum_pre_data_cdd) * 100) / $total_sum_pre_data_cdd), $percentage_decimal);

    } else {

	if ($total_sum_data_cdd == 0) {

	    $total_sum_data_cdd_variation = 0;

	} else {

	    $total_sum_data_cdd_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_hdd) && $total_sum_pre_data_hdd > 0) {

	$total_sum_data_hdd_variation = round(((($total_sum_data_hdd - $total_sum_pre_data_hdd) * 100) / $total_sum_pre_data_hdd), $percentage_decimal);

    } else {

	if ($total_sum_data_hdd == 0) {

	    $total_sum_data_hdd_variation = 0;

	} else {

	    $total_sum_data_hdd_variation = 100;

	}

    }

    if (!empty($total_sum_pre_data_occupancy) && $total_sum_pre_data_occupancy > 0) {

	$total_sum_data_occupancy_variation = round(((($total_sum_data_occupancy - $total_sum_pre_data_occupancy) * 100) / $total_sum_pre_data_occupancy), $percentage_decimal);

    } else {

	if ($total_sum_data_occupancy == 0) {

	    $total_sum_data_occupancy_variation = 0;

	} else {

	    $total_sum_data_occupancy_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_room_night) && $total_sum_pre_data_room_night > 0) {

	$total_sum_data_room_night_variation = round(((($total_sum_data_room_night - $total_sum_pre_data_room_night) * 100) / $total_sum_pre_data_room_night), $percentage_decimal);

    } else {

	if ($total_sum_data_room_night == 0) {

	    $total_sum_data_room_night_variation = 0;

	} else {

	    $total_sum_data_room_night_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_electricity_tariff) && $total_sum_pre_data_electricity_tariff > 0) {

	$total_sum_data_electricity_tariff_variation = round(((($total_sum_data_electricity_tariff - $total_sum_pre_data_electricity_tariff) * 100) / $total_sum_pre_data_electricity_tariff), $percentage_decimal);

    } else {

	if ($total_sum_data_electricity_tariff == 0) {

	    $total_sum_data_electricity_tariff_variation = 0;

	} else {

	    $total_sum_data_electricity_tariff_variation = 100;

	}

    }



    // utility variation

    $fuel_difference = $total_sum_data_fuel_tariff - $total_sum_pre_data_fuel_tariff;

    if (!empty($total_sum_pre_data_fuel_tariff)) {

	$fuel_variation = round($fuel_difference * 100 / $total_sum_pre_data_fuel_tariff, $percentage_decimal);

    } else {

	$fuel_variation = 0;

    }



    $lpg_difference = $total_sum_data_lpg_tariff - $total_sum_pre_data_lpg_tariff;

    if (!empty($total_sum_pre_data_lpg_tariff)) {

	$lpg_variation = round($lpg_difference * 100 / $total_sum_pre_data_lpg_tariff, $percentage_decimal);

    } else {

	$lpg_variation = 0;

    }



    $natural_gas_difference = $total_sum_data_natural_gas_tariff - $total_sum_pre_data_natural_gas_tariff;

    if (!empty($total_sum_pre_data_natural_gas_tariff)) {

	$natural_gas_variation = round($natural_gas_difference * 100 / $total_sum_pre_data_natural_gas_tariff, $percentage_decimal);

    } else {

	$natural_gas_variation = 0;

    }



    $heating_district_difference = $total_sum_data_heating_district_tariff - $total_sum_pre_data_heating_district_tariff;

    if (!empty($total_sum_pre_data_heating_district_tariff)) {

	$heating_district_variation = round($heating_district_difference * 100 / $total_sum_pre_data_heating_district_tariff, $percentage_decimal);

    } else {

	$heating_district_variation = 0;

    }



    $cooling_district_difference = $total_sum_data_cooling_district_tariff - $total_sum_pre_data_cooling_district_tariff;

    if (!empty($total_sum_pre_data_cooling_district_tariff)) {

	$cooling_district_variation = round($cooling_district_difference * 100 / $total_sum_pre_data_cooling_district_tariff, $percentage_decimal);

    } else {

	$cooling_district_variation = 0;

    }



    $water_difference = $total_sum_data_water_tariff - $total_sum_pre_data_water_tariff;

    if (!empty($total_sum_pre_data_water_tariff)) {

	$water_variation = round($water_difference * 100 / $total_sum_pre_data_water_tariff, $percentage_decimal);

    } else {

	$water_variation = 0;

    }



    // Total variation

    if (!empty($total_sum_pre_data_sum) && $total_sum_pre_data_sum > 0) {

	$total_sum_data_variation = round(((($total_sum_data_sum - $total_sum_pre_data_sum) * 100) / $total_sum_pre_data_sum), $percentage_decimal);

    } else {

	if ($total_sum_data_sum == 0) {

	    $total_sum_data_variation = 0;

	} else {

	    $total_sum_data_variation = 100;

	}

    }



    //Cost @ LY Tariff calculation (utility_difference * previous_year_tariff);

    $fuel_oil_ly = round($fuel_consumption_difference * $total_sum_pre_data_fuel_tariff, $percentage_decimal);

    $lpg_ly = round($lpg_consumption_difference * $total_sum_pre_data_lpg_tariff, $percentage_decimal);

    $natural_gas_ly = round($natural_gas_consumption_difference * $total_sum_pre_data_natural_gas_tariff, $percentage_decimal);

    $heating_district_ly = round($heating_district_consumption_difference * $total_sum_pre_data_heating_district_tariff, $percentage_decimal);

    $cooling_district_ly = round($cooling_district_consumption_difference * $total_sum_pre_data_cooling_district_tariff, $percentage_decimal);

    $water_ly = round($water_consumption_difference * $total_sum_pre_data_water_tariff, $percentage_decimal);

    $electricity_ly = round($electricity_consumption_difference * $total_sum_pre_data_electricity_tariff, $percentage_decimal);

    $total_cost_ly = $fuel_oil_ly + $lpg_ly + $natural_gas_ly + $heating_district_ly + $cooling_district_ly + $water_ly + $electricity_ly;



    // Calculate utitlity per room night and builtup area

    $total_sum_pre_data_electricity_kwh_per_roomnight = ($total_sum_pre_data_electricity_kwh / $total_sum_pre_data_room_night);

    $total_sum_pre_data_electricity_kwh_per_m2 = ($total_sum_pre_data_electricity_kwh / $site_detail['site_builtup_area']);

    $total_sum_pre_data_water_liter_per_roomnight = ($total_sum_pre_data_water / $total_sum_pre_data_room_night);

    $total_sum_pre_data_utility_cost_per_roomnight = round($total_sum_pre_data_sum / $total_sum_pre_data_room_night, $value_decimal);

    $total_sum_pre_data_utility_cost_per_m2 = round($total_sum_pre_data_sum / $site_detail['site_builtup_area'], $value_decimal);



    $total_sum_data_electricity_kwh_per_roomnight = ($total_sum_data_electricity_kwh / $total_sum_data_room_night);

    $total_sum_data_electricity_kwh_per_m2 = ($total_sum_data_electricity_kwh / $site_detail['site_builtup_area']);

    $total_sum_data_water_liter_per_roomnight = ($total_sum_data_water / $total_sum_data_room_night);

    $total_sum_data_utility_cost_per_roomnight = round($total_sum_data_sum / $total_sum_data_room_night, $value_decimal);

    $total_sum_data_utility_cost_per_m2 = is_infinite($total_sum_data_sum / $site_detail['site_builtup_area']) ? 0 : round($total_sum_data_sum / $site_detail['site_builtup_area'], $value_decimal);



    $total_sum_data_utility_cost_per_m2 = is_nan($total_sum_data_utility_cost_per_m2) ? 0 : $total_sum_data_utility_cost_per_m2;



    $data_utility_cost_per_roomnight_difference = $total_sum_data_utility_cost_per_roomnight - $total_sum_pre_data_utility_cost_per_roomnight;

    $data_utility_cost_per_roomnight_variation = round($data_utility_cost_per_roomnight_difference * 100 / $total_sum_data_utility_cost_per_roomnight, $percentage_decimal);



    $data_utility_cost_per_m2_difference = $total_sum_data_utility_cost_per_m2 - $total_sum_pre_data_utility_cost_per_m2;

    $data_utility_cost_per_m2_variation = is_infinite($data_utility_cost_per_m2_difference * 100 / $total_sum_data_utility_cost_per_m2) ? 0 : round($data_utility_cost_per_m2_difference * 100 / $total_sum_data_utility_cost_per_m2, $percentage_decimal);

    $data_utility_cost_per_m2_variation = is_nan($data_utility_cost_per_m2_variation) ? 0 : $data_utility_cost_per_m2_variation;



    if (!empty($total_sum_pre_data_electricity_kwh_per_roomnight) && $total_sum_pre_data_electricity_kwh_per_roomnight > 0) {

	$total_sum_data_electricity_kwh_per_roomnight_variation = round(((($total_sum_data_electricity_kwh_per_roomnight - $total_sum_pre_data_electricity_kwh_per_roomnight) * 100) / $total_sum_pre_data_electricity_kwh_per_roomnight), $percentage_decimal);

    } else {

	if ($total_sum_data_electricity_kwh_per_roomnight == 0) {

	    $total_sum_data_electricity_kwh_per_roomnight_variation = 0;

	} else {

	    $total_sum_data_electricity_kwh_per_roomnight_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_electricity_kwh_per_m2) && $total_sum_pre_data_electricity_kwh_per_m2 > 0) {

	$total_sum_data_electricity_per_m2_kwh_variation = round(((($total_sum_data_electricity_kwh_per_m2 - $total_sum_pre_data_electricity_kwh_per_m2) * 100) / $total_sum_pre_data_electricity_kwh_per_m2), $percentage_decimal);

    } else {

	if ($total_sum_data_sum == 0) {

	    $total_sum_data_electricity_per_m2_kwh_variation = 0;

	} else {

	    $total_sum_data_electricity_per_m2_kwh_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_water_liter_per_roomnight) && $total_sum_pre_data_water_liter_per_roomnight > 0) {

	$total_sum_data_water_liter_per_roomnight_variation = round(((($total_sum_data_water_liter_per_roomnight - $total_sum_pre_data_water_liter_per_roomnight) * 100) / $total_sum_pre_data_water_liter_per_roomnight), $percentage_decimal);

    } else {

	if ($total_sum_data_sum == 0) {

	    $total_sum_data_water_liter_per_roomnight_variation = 0;

	} else {

	    $total_sum_data_water_liter_per_roomnight_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_utility_cost_per_roomnight) && $total_sum_pre_data_utility_cost_per_roomnight > 0) {

	$total_sum_data_utility_cost_per_roomnight_variation = round(((($total_sum_data_utility_cost_per_roomnight - $total_sum_pre_data_utility_cost_per_roomnight) * 100) / $total_sum_pre_data_utility_cost_per_roomnight), $percentage_decimal);

    } else {

	if ($total_sum_data_sum == 0) {

	    $total_sum_data_utility_cost_per_roomnight_variation = 0;

	} else {

	    $total_sum_data_utility_cost_per_roomnight_variation = 100;

	}

    }



    if (!empty($total_sum_pre_data_utility_cost_per_m2) && $total_sum_pre_data_utility_cost_per_m2 > 0) {

	$total_sum_data_utility_cost_per_m2_variation = round(((($total_sum_data_utility_cost_per_m2 - $total_sum_pre_data_utility_cost_per_m2) * 100) / $total_sum_pre_data_utility_cost_per_m2), $percentage_decimal);

    } else {

	if ($total_sum_data_sum == 0) {

	    $total_sum_data_utility_cost_per_m2_variation = 0;

	} else {

	    $total_sum_data_utility_cost_per_m2_variation = 100;

	}

    }

    //$total_sum_data_variation = ($total_sum_data_electricity_variation+$total_sum_data_fuel_variation+$total_sum_data_lpg_variation+$total_sum_data_natural_gas_variation+$total_sum_data_water_variation+$total_sum_data_heating_district_variation+$total_sum_data_cooling_district_variation);

}



if ($showCostBudgetVariance) {

    $electricity_tariff_budget = is_infinite($currentBudgetActualData["total_electricity_cost_budget"] / $currentBudgetActualData["total_electricity_kwh_budget"]) ? 0 : round($currentBudgetActualData["total_electricity_cost_budget"] / $currentBudgetActualData["total_electricity_kwh_budget"], $value_decimal);



    $fuel_oil_tariff_budget = is_infinite($currentBudgetActualData["total_fuel_oil_cost_budget"] / $currentBudgetActualData["total_fuel_oil_budget"]) ? 0 : round($currentBudgetActualData["total_fuel_oil_cost_budget"] / $currentBudgetActualData["total_fuel_oil_budget"], $value_decimal);



    $lpg_tariff_budget = is_infinite($currentBudgetActualData["total_lpg_cost_budget"] / $currentBudgetActualData["total_lpg_budget"]) ? 0 : round($currentBudgetActualData["total_lpg_cost_budget"] / $currentBudgetActualData["total_lpg_budget"], $value_decimal);



    $natural_gas_tariff_budget = is_infinite($currentBudgetActualData["total_natural_gas_cost_budget"] / $currentBudgetActualData["total_natural_gas_budget"]) ? 0 : round($currentBudgetActualData["total_natural_gas_cost_budget"] / $currentBudgetActualData["total_natural_gas_budget"], $value_decimal);



    $water_tariff_budget = is_infinite($currentBudgetActualData["water_total_consumption_cost_budget"] / $currentBudgetActualData["water_total_consumption_budget"]) ? 0 : round($currentBudgetActualData["water_total_consumption_cost_budget"] / $currentBudgetActualData["water_total_consumption_budget"], $value_decimal);



    $district_cooling_tariff_budget = is_infinite($currentBudgetActualData["district_cooling_cost_budget"] / $currentBudgetActualData["district_cooling_budget"]) ? 0 : round($currentBudgetActualData["district_cooling_cost_budget"] / $currentBudgetActualData["district_cooling_budget"], $value_decimal);



    $district_heating_tariff_budget = is_infinite($currentBudgetActualData["district_heating_cost_budget"] / $currentBudgetActualData["district_heating_budget"]) ? 0 : round($currentBudgetActualData["district_heating_cost_budget"] / $currentBudgetActualData["district_heating_budget"], $value_decimal);



    $electricity_tariff_budget = is_nan($electricity_tariff_budget) ? 0 : $electricity_tariff_budget;

    $fuel_oil_tariff_budget = is_nan($fuel_oil_tariff_budget) ? 0 : $fuel_oil_tariff_budget;

    $lpg_tariff_budget = is_nan($lpg_tariff_budget) ? 0 : $lpg_tariff_budget;

    $natural_gas_tariff_budget = is_nan($natural_gas_tariff_budget) ? 0 : $natural_gas_tariff_budget;

    $water_tariff_budget = is_nan($water_tariff_budget) ? 0 : $water_tariff_budget;

    $district_cooling_tariff_budget = is_nan($district_cooling_tariff_budget) ? 0 : $district_cooling_tariff_budget;

    $district_heating_tariff_budget = is_nan($district_heating_tariff_budget) ? 0 : $district_heating_tariff_budget;



    $electricity_tariff_actual = is_infinite($currentBudgetActualData["total_electricity_cost_actual"] / $currentBudgetActualData["total_electricity_kwh_actual"]) ? 0 : round($currentBudgetActualData["total_electricity_cost_actual"] / $currentBudgetActualData["total_electricity_kwh_actual"], $value_decimal);

    $fuel_oil_tariff_actual =  is_infinite($currentBudgetActualData["total_fuel_oil_cost_actual"] / $currentBudgetActualData["total_fuel_oil_actual"]) ? 0 : round($currentBudgetActualData["total_fuel_oil_cost_actual"] / $currentBudgetActualData["total_fuel_oil_actual"], $value_decimal);

    $lpg_tariff_actual =  is_infinite($currentBudgetActualData["total_lpg_cost_actual"] / $currentBudgetActualData["total_lpg_actual"]) ? 0 : round($currentBudgetActualData["total_lpg_cost_actual"] / $currentBudgetActualData["total_lpg_actual"], $value_decimal);

    $natural_gas_tariff_actual =  is_infinite($currentBudgetActualData["total_natural_gas_cost_actual"] / $currentBudgetActualData["total_natural_gas_actual"]) ? 0 : round($currentBudgetActualData["total_natural_gas_cost_actual"] / $currentBudgetActualData["total_natural_gas_actual"], $value_decimal);

    $water_tariff_actual =  is_infinite($currentBudgetActualData["water_total_consumption_cost_actual"] / $currentBudgetActualData["water_total_consumption_actual"]) ? 0 : round($currentBudgetActualData["water_total_consumption_cost_actual"] / $currentBudgetActualData["water_total_consumption_actual"], $value_decimal);

    $district_cooling_tariff_actual =  is_infinite($currentBudgetActualData["district_cooling_cost_actual"] / $currentBudgetActualData["district_cooling_actual"]) ? 0 : round($currentBudgetActualData["district_cooling_cost_actual"] / $currentBudgetActualData["district_cooling_actual"], $value_decimal);

    $district_heating_tariff_actual =  is_infinite($currentBudgetActualData["district_heating_cost_actual"] / $currentBudgetActualData["district_heating_actual"]) ? 0 : round($currentBudgetActualData["district_heating_cost_actual"] / $currentBudgetActualData["district_heating_actual"], $value_decimal);



    $electricity_tariff_actual = is_nan($electricity_tariff_actual) ? 0 : $electricity_tariff_actual;

    $fuel_oil_tariff_actual = is_nan($fuel_oil_tariff_actual) ? 0 : $fuel_oil_tariff_actual;

    $lpg_tariff_actual = is_nan($lpg_tariff_actual) ? 0 : $lpg_tariff_actual;

    $natural_gas_tariff_actual = is_nan($natural_gas_tariff_actual) ? 0 : $natural_gas_tariff_actual;

    $water_tariff_actual = is_nan($water_tariff_actual) ? 0 : $water_tariff_actual;

    $district_cooling_tariff_actual = is_nan($district_cooling_tariff_actual) ? 0 : $district_cooling_tariff_actual;

    $district_heating_tariff_actual = is_nan($district_heating_tariff_actual) ? 0 : $district_heating_tariff_actual;



    $electricity_per_room_night_actual = is_infinite($currentBudgetActualData["total_electricity_kwh_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_electricity_kwh_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $fuel_oil_per_room_night_actual = is_infinite($currentBudgetActualData["total_fuel_oil_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_fuel_oil_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $lpg_per_room_night_actual = is_infinite($currentBudgetActualData["total_lpg_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_lpg_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $natural_gas_per_room_night_actual = is_infinite($currentBudgetActualData["total_natural_gas_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_natural_gas_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $water_per_room_night_actual = is_infinite($currentBudgetActualData["water_total_consumption_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["water_total_consumption_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $district_cooling_per_room_night_actual = is_infinite($currentBudgetActualData["district_cooling_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["district_cooling_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $district_heating_per_room_night_actual = is_infinite($currentBudgetActualData["district_heating_actual"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["district_heating_actual"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);



    $electricity_per_room_night_actual = is_nan($electricity_per_room_night_actual) ? 0 : $electricity_per_room_night_actual;

    $fuel_oil_per_room_night_actual = is_nan($fuel_oil_per_room_night_actual) ? 0 : $fuel_oil_per_room_night_actual;

    $lpg_per_room_night_actual = is_nan($lpg_per_room_night_actual) ? 0 : $lpg_per_room_night_actual;

    $natural_gas_per_room_night_actual = is_nan($natural_gas_per_room_night_actual) ? 0 : $natural_gas_per_room_night_actual;

    $water_per_room_night_actual = is_nan($water_per_room_night_actual) ? 0 : $water_per_room_night_actual;

    $district_cooling_per_room_night_actual = is_nan($district_cooling_per_room_night_actual) ? 0 : $district_cooling_per_room_night_actual;

    $district_heating_per_room_night_actual = is_nan($district_heating_per_room_night_actual) ? 0 : $district_heating_per_room_night_actual;



    $electricity_per_room_night_budget = is_infinite($currentBudgetActualData["total_electricity_kwh_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_electricity_kwh_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $fuel_oil_per_room_night_budget = is_infinite($currentBudgetActualData["total_fuel_oil_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_fuel_oil_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $lpg_per_room_night_budget = round($currentBudgetActualData["total_lpg_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $natural_gas_per_room_night_budget = is_infinite($currentBudgetActualData["total_natural_gas_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["total_natural_gas_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $water_per_room_night_budget = is_infinite($currentBudgetActualData["water_total_consumption_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["water_total_consumption_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $district_cooling_per_room_night_budget = is_infinite($currentBudgetActualData["district_cooling_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["district_cooling_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);

    $district_heating_per_room_night_budget = is_infinite($currentBudgetActualData["district_heating_budget"] / $currentBudgetActualData["total_room_night"]) ? 0 : round($currentBudgetActualData["district_heating_budget"] / $currentBudgetActualData["total_room_night"], $percentage_decimal);



    $electricity_per_room_night_budget = is_nan($electricity_per_room_night_budget) ? 0 : $electricity_per_room_night_budget;

    $fuel_oil_per_room_night_budget = is_nan($fuel_oil_per_room_night_budget) ? 0 : $fuel_oil_per_room_night_budget;

    $lpg_per_room_night_budget = is_nan($lpg_per_room_night_budget) ? 0 : $lpg_per_room_night_budget;

    $natural_gas_per_room_night_budget = is_nan($natural_gas_per_room_night_budget) ? 0 : $natural_gas_per_room_night_budget;

    $water_per_room_night_budget = is_nan($water_per_room_night_budget) ? 0 : $water_per_room_night_budget;

    $district_cooling_per_room_night_budget = is_nan($district_cooling_per_room_night_budget) ? 0 : $district_cooling_per_room_night_budget;

    $district_heating_per_room_night_budget = is_nan($district_heating_per_room_night_budget) ? 0 : $district_heating_per_room_night_budget;



    $electricity_tariff_variation = is_infinite((($electricity_tariff_actual - $electricity_tariff_budget) * 100) / $electricity_tariff_budget) ? 0 : round((($electricity_tariff_actual - $electricity_tariff_budget) * 100) / $electricity_tariff_budget, $percentage_decimal);

    $fuel_oil_tariff_variation = is_infinite((($fuel_oil_tariff_actual - $fuel_oil_tariff_budget) * 100) / $fuel_oil_tariff_budget) ? 0 : round((($fuel_oil_tariff_actual - $fuel_oil_tariff_budget) * 100) / $fuel_oil_tariff_budget, $percentage_decimal);

    $lpg_tariff_variation = is_infinite((($lpg_tariff_actual - $lpg_tariff_budget) * 100) / $lpg_tariff_budget) ? 0 : round((($lpg_tariff_actual - $lpg_tariff_budget) * 100) / $lpg_tariff_budget, $percentage_decimal);

    $natural_gas_tariff_variation = is_infinite((($natural_gas_tariff_actual - $natural_gas_tariff_budget) * 100) / $natural_gas_tariff_budget) ? 0 : round((($natural_gas_tariff_actual - $natural_gas_tariff_budget) * 100) / $natural_gas_tariff_budget, $percentage_decimal);

    $water_tariff_variation = is_infinite((($water_tariff_actual - $water_tariff_budget) * 100) / $water_tariff_budget) ? 0 : round((($water_tariff_actual - $water_tariff_budget) * 100) / $water_tariff_budget, $percentage_decimal);

    $district_cooling_tariff_variation = is_infinite((($district_cooling_tariff_actual - $district_cooling_tariff_budget) * 100) / $district_cooling_tariff_budget) ? 0 : round((($district_cooling_tariff_actual - $district_cooling_tariff_budget) * 100) / $district_cooling_tariff_budget, $percentage_decimal);

    $district_heating_tariff_variation = is_infinite((($district_heating_tariff_actual - $district_heating_tariff_budget) * 100) / $district_heating_tariff_budget) ? 0 : round((($district_heating_tariff_actual - $district_heating_tariff_budget) * 100) / $district_heating_tariff_budget, $percentage_decimal);



    $electricity_tariff_variation = is_nan($electricity_tariff_variation) ? 0 : $electricity_tariff_variation;

    $fuel_oil_tariff_variation = is_nan($fuel_oil_tariff_variation) ? 0 : $fuel_oil_tariff_variation;

    $lpg_tariff_variation = is_nan($lpg_tariff_variation) ? 0 : $lpg_tariff_variation;

    $natural_gas_tariff_variation = is_nan($natural_gas_tariff_variation) ? 0 : $natural_gas_tariff_variation;

    $water_tariff_variation = is_nan($water_tariff_variation) ? 0 : $water_tariff_variation;

    $district_cooling_tariff_variation = is_nan($district_cooling_tariff_variation) ? 0 : $district_cooling_tariff_variation;

    $district_heating_tariff_variation = is_nan($district_heating_tariff_variation) ? 0 : $district_heating_tariff_variation;



    $electricity_cost_variation = ($currentBudgetActualData["total_electricity_cost_budget"] != 0) ? round(($currentBudgetActualData["total_electricity_cost_actual"] - $currentBudgetActualData["total_electricity_cost_budget"]) * 100 / $currentBudgetActualData["total_electricity_cost_budget"], $percentage_decimal) : 0;

    $fuel_oil_cost_variation = ($currentBudgetActualData["total_fuel_oil_cost_budget"] != 0) ?  round(($currentBudgetActualData["total_fuel_oil_cost_actual"] - $currentBudgetActualData["total_fuel_oil_cost_budget"]) * 100 / $currentBudgetActualData["total_fuel_oil_cost_budget"], $percentage_decimal) : 0;

    $lpg_cost_variation = ($currentBudgetActualData["total_lpg_cost_budget"] != 0) ? round(($currentBudgetActualData["total_lpg_cost_actual"] - $currentBudgetActualData["total_lpg_cost_budget"]) * 100 / $currentBudgetActualData["total_lpg_cost_budget"], $percentage_decimal) : 0;

    $natural_gas_cost_variation = ($currentBudgetActualData["total_natural_gas_cost_budget"] != 0) ? round(($currentBudgetActualData["total_natural_gas_cost_actual"] - $currentBudgetActualData["total_natural_gas_cost_budget"]) * 100 / $currentBudgetActualData["total_natural_gas_cost_budget"], $percentage_decimal) : 0;

    $water_cost_variation = ( $currentBudgetActualData["water_total_consumption_cost_budget"] != 0) ? round(($currentBudgetActualData["water_total_consumption_cost_actual"] - $currentBudgetActualData["water_total_consumption_cost_budget"]) * 100 / $currentBudgetActualData["water_total_consumption_cost_budget"], $percentage_decimal) : 0;

    $district_cooling_cost_variation = ($currentBudgetActualData["district_cooling_cost_budget"] != 0) ? round(($currentBudgetActualData["district_cooling_cost_actual"] - $currentBudgetActualData["district_cooling_cost_budget"]) * 100 / $currentBudgetActualData["district_cooling_cost_budget"], $percentage_decimal) : 0;

    $district_heating_cost_variation = ($currentBudgetActualData["district_heating_cost_budget"] != 0) ? round(($currentBudgetActualData["district_heating_cost_actual"] - $currentBudgetActualData["district_heating_cost_budget"]) * 100 / $currentBudgetActualData["district_heating_cost_budget"], $percentage_decimal) : 0;



    $electricity_per_room_night_variation = (($electricity_per_room_night_budget != 0) && ($electricity_per_room_night_actual != 0)) ? round(($electricity_per_room_night_actual - $electricity_per_room_night_budget) * 100 / $electricity_per_room_night_budget, $percentage_decimal) : 0;



    $fuel_oil_per_room_night_variation = (($fuel_oil_per_room_night_budget != 0) && ($fuel_oil_per_room_night_actual != 0)) ?  round(($fuel_oil_per_room_night_actual - $fuel_oil_per_room_night_budget) * 100 / $fuel_oil_per_room_night_budget, $percentage_decimal) : 0;



    $lpg_per_room_night_variation = (($lpg_per_room_night_budget != 0) && ($lpg_per_room_night_actual != 0)) ? round(($lpg_per_room_night_actual - $lpg_per_room_night_budget) * 100 / $lpg_per_room_night_budget, $percentage_decimal) : 0;



    $natural_gas_per_room_night_variation = (($natural_gas_per_room_night_budget != 0) && ($natural_gas_per_room_night_actual != 0)) ? round(($natural_gas_per_room_night_actual - $natural_gas_per_room_night_budget) * 100 / $natural_gas_per_room_night_budget, $percentage_decimal) : 0;



    $water_per_room_night_variation = (($water_per_room_night_budget != 0) && ($water_per_room_night_actual != 0)) ? round(($water_per_room_night_actual - $water_per_room_night_budget) * 100 / $water_per_room_night_budget, $percentage_decimal) : 0;



    $district_cooling_per_room_night_variation = (($district_cooling_per_room_night_budget != 0) && ($district_cooling_per_room_night_actual != 0)) ?  round(($district_cooling_per_room_night_actual - $district_cooling_per_room_night_budget) * 100 / $district_cooling_per_room_night_budget, $percentage_decimal) : 0;



    $district_heating_per_room_night_variation = (($district_heating_per_room_night_budget != 0) && ($district_heating_per_room_night_actual != 0)) ? round(($district_heating_per_room_night_actual - $district_heating_per_room_night_budget) * 100 / $district_heating_per_room_night_budget, $percentage_decimal) : 0;



    $total_cost_budget = $currentBudgetActualData["total_electricity_cost_budget"] + $currentBudgetActualData["total_fuel_oil_cost_budget"] + $currentBudgetActualData["total_lpg_cost_budget"] + $currentBudgetActualData["total_natural_gas_cost_budget"] + $currentBudgetActualData["water_total_consumption_cost_budget"] + $currentBudgetActualData["district_cooling_cost_budget"] + $currentBudgetActualData["district_heating_cost_budget"];

    $total_cost_actual = $currentBudgetActualData["total_electricity_cost_actual"] + $currentBudgetActualData["total_fuel_oil_cost_actual"] + $currentBudgetActualData["total_lpg_cost_actual"] + $currentBudgetActualData["total_natural_gas_cost_actual"] + $currentBudgetActualData["water_total_consumption_cost_actual"] + $currentBudgetActualData["district_cooling_cost_actual"] + $currentBudgetActualData["district_heating_cost_actual"];

    $total_cost_variation = $total_cost_actual - $total_cost_budget;

    $total_cost_variation_percentage = ($total_cost_budget != 0) ? round($total_cost_variation * 100 / $total_cost_budget, $percentage_decimal) : 0;



    $total_cost_actual_per_room_night = round($total_cost_actual / $currentBudgetActualData["total_room_night"], $value_decimal);

    //$total_cost_budget_per_room_night = round($total_cost_budget / $currentBudgetActualData["total_room_night"], $value_decimal);

    //$total_cost_per_room_night_variation = $total_cost_actual_per_room_night - $total_cost_budget_per_room_night;

    //$total_cost_per_room_night_variation_percentage = round($total_cost_per_room_night_variation * 100 / $total_cost_actual_per_room_night, $percentage_decimal);



    $total_cost_actual_per_m2 = round($total_cost_actual / $site_detail['site_builtup_area'], $value_decimal);

    //$total_cost_budget_per_m2 = round($total_cost_budget / $site_detail['site_builtup_area'], $value_decimal);

    //$total_cost_per_m2_variation = $total_cost_actual_per_m2 - $total_cost_budget_per_m2;

    //$total_cost_per_m2_variation_percentage = round($total_cost_per_m2_variation * 100 / $total_cost_actual_per_m2, $percentage_decimal);

}

include ('admin_landing_pdf_reports_table.php');

