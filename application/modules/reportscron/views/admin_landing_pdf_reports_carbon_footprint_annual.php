<?php
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

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



$site_id = $this->session->userdata['hep_cron_session']['site_id'];
$dataFactor = getMmbtuFactorConversionAllUtility($site_id);

?>

<html style="text-align: left;">

    <body width="100%">

        <div style="border:2px solid  #f69546;padding:10px;">

            <table width="100%" cellpadding="5" cellspacing="5" >

                <tr>

                    <td width="100%">

                        <img src="<?php echo $columnChartCarbonFootprintImg; ?>" />

                    </td>

                </tr>

                <tr>

                    <td width="100%"><?php if(!empty($utility_cost_chart_pre)){ 

                            $ci = get_instance();

                            $total_months = 0;

                            foreach ($resultkeys_pre as $year => $value) {

                                foreach ($value as $key1 => $month) {

                                    // Previous year data

                                    $pre_monthdata = $montharray[$month] . ' ' . ($year-1);

                                    $pre_data_electricity = (!empty($utility_cost_chart_pre[$month][$year-1]['total_electricity_kwh']))?($utility_cost_chart_pre[$month][$year-1]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year-1]['onsite_generator'] - $utility_cost_chart_pre[$month][$year-1]['renewable_energy']):0;

                                    $pre_data_fuel = (!empty($utility_cost_chart_pre[$month][$year-1]['fuel_consumption']))?$utility_cost_chart_pre[$month][$year-1]['fuel_consumption']:0;

                                    $pre_data_lpg = (!empty($utility_cost_chart_pre[$month][$year-1]['lpg_consumption']))?$utility_cost_chart_pre[$month][$year-1]['lpg_consumption']:0;

                                    $pre_data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year-1]['natural_gas_consumption']))?$utility_cost_chart_pre[$month][$year-1]['natural_gas_consumption']:0;

                                    /*

                                    $pre_data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year-1]['natural_gas']))?$utility_cost_chart_pre[$month][$year-1]['natural_gas']:0;

                                    */

                                    $pre_data_heating_district = (!empty($utility_cost_chart_pre[$month][$year-1]['heating_district_consumption']))?$utility_cost_chart_pre[$month][$year-1]['heating_district_consumption']:0;

                                    $pre_data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year-1]['cooling_district_consumption']))?$utility_cost_chart_pre[$month][$year-1]['cooling_district_consumption']:0;

                                    $pre_data_water = (!empty($utility_cost_chart_pre[$month][$year-1]['water_consumption']))?$utility_cost_chart_pre[$month][$year-1]['water_consumption']:0;

                                    $pre_data_cdd = (!empty($utility_cost_chart_pre[$month][$year-1]['cdd']))?$utility_cost_chart_pre[$month][$year-1]['cdd']:0;

                                    $pre_data_hdd = (!empty($utility_cost_chart_pre[$month][$year-1]['hdd']))?$utility_cost_chart_pre[$month][$year-1]['hdd']:0;

                                    $pre_data_occupancy = (!empty($utility_cost_chart_pre[$month][$year-1]['occupancy']))?$utility_cost_chart_pre[$month][$year-1]['occupancy']:0;

                                    $pre_data_room_night = (!empty($utility_cost_chart_pre[$month][$year-1]['room_night']))?$utility_cost_chart_pre[$month][$year-1]['room_night']:0;

                                    $pre_data_guest_night = (!empty($utility_cost_chart_pre[$month][$year-1]['guest_night']))?$utility_cost_chart_pre[$month][$year-1]['guest_night']:0;

                                    $pre_data_electricity_tariff = (!empty($utility_cost_chart_pre[$month][$year-1]['electricity_tariff']))?$utility_cost_chart_pre[$month][$year-1]['electricity_tariff']:0;

                                    $pre_data_electricity_kwh = (!empty($utility_cost_chart_pre[$month][$year-1]['total_electricity_kwh']))?$utility_cost_chart_pre[$month][$year-1]['total_electricity_kwh']:0;

                                    

                                    // Current year data

                                    $monthdata = $montharray[$month] . ' ' . $year;

                                    $data_electricity = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh']))?($utility_cost_chart_pre[$month][$year]['total_electricity_kwh'] - $utility_cost_chart_pre[$month][$year]['onsite_generator'] - $utility_cost_chart_pre[$month][$year]['renewable_energy']):0;

                                    $data_fuel = (!empty($utility_cost_chart_pre[$month][$year]['fuel_consumption']))?$utility_cost_chart_pre[$month][$year]['fuel_consumption']:0;

                                    $data_lpg = (!empty($utility_cost_chart_pre[$month][$year]['lpg_consumption']))?$utility_cost_chart_pre[$month][$year]['lpg_consumption']:0;

                                    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas_consumption']))?$utility_cost_chart_pre[$month][$year]['natural_gas_consumption']:0;

                                    /*

                                    $data_natural_gas = (!empty($utility_cost_chart_pre[$month][$year]['natural_gas']))?$utility_cost_chart_pre[$month][$year]['natural_gas']:0;

                                    */

                                    $data_heating_district = (!empty($utility_cost_chart_pre[$month][$year]['heating_district_consumption']))?$utility_cost_chart_pre[$month][$year]['heating_district_consumption']:0;

                                    $data_cooling_district = (!empty($utility_cost_chart_pre[$month][$year]['cooling_district_consumption']))?$utility_cost_chart_pre[$month][$year]['cooling_district_consumption']:0;

                                    $data_water = (!empty($utility_cost_chart_pre[$month][$year]['water_consumption']))?$utility_cost_chart_pre[$month][$year]['water_consumption']:0;

                                    $data_cdd = (!empty($utility_cost_chart_pre[$month][$year]['cdd']))?$utility_cost_chart_pre[$month][$year]['cdd']:0;

                                    $data_hdd = (!empty($utility_cost_chart_pre[$month][$year]['hdd']))?$utility_cost_chart_pre[$month][$year]['hdd']:0;

                                    $data_occupancy = (!empty($utility_cost_chart_pre[$month][$year]['occupancy']))?$utility_cost_chart_pre[$month][$year]['occupancy']:0;

                                    $data_room_night = (!empty($utility_cost_chart_pre[$month][$year]['room_night']))?$utility_cost_chart_pre[$month][$year]['room_night']:0;

                                    $data_guest_night = (!empty($utility_cost_chart_pre[$month][$year]['guest_night']))?$utility_cost_chart_pre[$month][$year]['guest_night']:0;

                                    $data_electricity_tariff = (!empty($utility_cost_chart_pre[$month][$year]['electricity_tariff']))?$utility_cost_chart_pre[$month][$year]['electricity_tariff']:0;

                                    $data_electricity_kwh = (!empty($utility_cost_chart_pre[$month][$year]['total_electricity_kwh']))?$utility_cost_chart_pre[$month][$year]['total_electricity_kwh']:0;



                                    // Calculate carbon footprint

				    $pre_data_electricity      = ($pre_data_electricity >= 0) ? round($pre_data_electricity * $dataFactor['electricity'] * $site_detail['electricity_emission_factor'], 2) : 0;

				    $pre_data_fuel             = ($pre_data_fuel >= 0) ? round($pre_data_fuel * $dataFactor['fuel_oil'] * $site_detail['fuel_emission_factor'], 2) : 0;

				    $pre_data_lpg              = ($pre_data_lpg >= 0) ? round($pre_data_lpg * $dataFactor['lpg'] * $site_detail['lpg_emission_factor'], 2) : 0;

				    $pre_data_natural_gas      = ($pre_data_natural_gas >= 0) ? round($pre_data_natural_gas * $dataFactor['natural_gas'] * $site_detail['natural_gas_emission_factor'], 2) : 0;

				    $pre_data_heating_district = ($pre_data_heating_district >= 0) ? round($pre_data_heating_district * $dataFactor['district_heating'] * $site_detail['district_heating_emission_factor'], 2) : 0;

				    $pre_data_cooling_district = ($pre_data_cooling_district >= 0) ? round($pre_data_cooling_district * $dataFactor['district_cooling'] * $site_detail['district_cooling_emission_factor'], 2) : 0;

				    $pre_data_water            = 0; // There is no calculation for water data



				    $data_electricity      = ($data_electricity >= 0) ? round($data_electricity * $dataFactor['electricity'] * $site_detail['electricity_emission_factor'], 2) : 0;

				    $data_fuel             = ($data_fuel >= 0) ? round($data_fuel * $dataFactor['fuel_oil'] * $site_detail['fuel_emission_factor'], 2) : 0;

				    $data_lpg              = ($data_lpg >= 0) ? round($data_lpg * $dataFactor['lpg'] * $site_detail['lpg_emission_factor'], 2) : 0;

				    $data_natural_gas      = ($data_natural_gas >= 0) ? round($data_natural_gas * $dataFactor['natural_gas'] * $site_detail['natural_gas_emission_factor'], 2) : 0;

				    $data_heating_district = ($data_heating_district >= 0) ? round($data_heating_district * $dataFactor['district_heating'] * $site_detail['district_heating_emission_factor'], 2) : 0;

				    $data_cooling_district = ($data_cooling_district >= 0) ? round($data_cooling_district * $dataFactor['district_cooling'] * $site_detail['district_cooling_emission_factor'], 2) : 0;

                                    $data_water            = 0; // There is no calculation for water data






                                    // Round values

                                    $pre_data_occupancy = round($pre_data_occupancy,2);

                                    $data_occupancy = round($data_occupancy,2);



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



                            if($total_sum_pre_data_electricity_kwh>0){

                                $total_sum_pre_data_electricity_tariff = ($total_sum_pre_data_electricity/$total_sum_pre_data_electricity_kwh);

                            }else{

                                $total_sum_pre_data_electricity_tariff = 0;

                            }



                            if($total_sum_data_electricity_kwh>0){

                                $total_sum_data_electricity_tariff = ($total_sum_data_electricity/$total_sum_data_electricity_kwh);

                            }else{

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

                            if(!empty($total_sum_pre_data_electricity) && $total_sum_pre_data_electricity>0){

                                $total_sum_data_electricity_variation = round(((($total_sum_data_electricity-$total_sum_pre_data_electricity)*100)/$total_sum_pre_data_electricity),2);

                            }else{

                                if($total_sum_data_electricity==0){

                                    $total_sum_data_electricity_variation = 0;

                                }else{

                                    $total_sum_data_electricity_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_fuel) && $total_sum_pre_data_fuel>0){

                                $total_sum_data_fuel_variation = round(((($total_sum_data_fuel-$total_sum_pre_data_fuel)*100)/$total_sum_pre_data_fuel),2);

                            }else{

                                if($total_sum_data_fuel==0){

                                    $total_sum_data_fuel_variation = 0;

                                }else{

                                    $total_sum_data_fuel_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_lpg) && $total_sum_pre_data_lpg>0){

                                $total_sum_data_lpg_variation = round(((($total_sum_data_lpg-$total_sum_pre_data_lpg)*100)/$total_sum_pre_data_lpg),2);

                            }else{

                                if($total_sum_data_lpg==0){

                                    $total_sum_data_lpg_variation = 0;

                                }else{

                                    $total_sum_data_lpg_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_natural_gas) && $total_sum_pre_data_natural_gas>0){

                                $total_sum_data_natural_gas_variation = round(((($total_sum_data_natural_gas-$total_sum_pre_data_natural_gas)*100)/$total_sum_pre_data_natural_gas),2);

                            }else{

                                if($total_sum_data_natural_gas==0){

                                    $total_sum_data_natural_gas_variation = 0;

                                }else{

                                    $total_sum_data_natural_gas_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_heating_district) && $total_sum_pre_data_heating_district>0){

                                $total_sum_data_heating_district_variation = round(((($total_sum_data_heating_district-$total_sum_pre_data_heating_district)*100)/$total_sum_pre_data_heating_district),2);

                            }else{

                                if($total_sum_data_heating_district==0){

                                    $total_sum_data_heating_district_variation = 0;

                                }else{

                                    $total_sum_data_heating_district_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_cooling_district) && $total_sum_pre_data_cooling_district>0){

                                $total_sum_data_cooling_district_variation = round(((($total_sum_data_cooling_district-$total_sum_pre_data_cooling_district)*100)/$total_sum_pre_data_cooling_district),2);

                            }else{

                                if($total_sum_data_cooling_district==0){

                                    $total_sum_data_cooling_district_variation = 0;

                                }else{

                                    $total_sum_data_cooling_district_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_water) && $total_sum_pre_data_water>0){

                                $total_sum_data_water_variation = round(((($total_sum_data_water-$total_sum_pre_data_water)*100)/$total_sum_pre_data_water),2);

                            }else{

                                if($total_sum_data_water==0){

                                    $total_sum_data_water_variation = 0;

                                }else{

                                    $total_sum_data_water_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_cdd) && $total_sum_pre_data_cdd>0){

                                $total_sum_data_cdd_variation = round(((($total_sum_data_cdd-$total_sum_pre_data_cdd)*100)/$total_sum_pre_data_cdd),2);

                            }else{

                                if($total_sum_data_cdd==0){

                                    $total_sum_data_cdd_variation = 0;

                                }else{

                                    $total_sum_data_cdd_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_hdd) && $total_sum_pre_data_hdd>0){

                                $total_sum_data_hdd_variation = round(((($total_sum_data_hdd-$total_sum_pre_data_hdd)*100)/$total_sum_pre_data_hdd),2);

                            }else{

                                if($total_sum_data_hdd==0){

                                    $total_sum_data_hdd_variation = 0;

                                }else{

                                    $total_sum_data_hdd_variation = 100;

                                }

                            }

                            if(!empty($total_sum_pre_data_occupancy) && $total_sum_pre_data_occupancy>0){

                                $total_sum_data_occupancy_variation = round(((($total_sum_data_occupancy-$total_sum_pre_data_occupancy)*100)/$total_sum_pre_data_occupancy),2);

                            }else{

                                if($total_sum_data_occupancy==0){

                                    $total_sum_data_occupancy_variation = 0;

                                }else{

                                    $total_sum_data_occupancy_variation = 100;

                                }

                            }



                            if(!empty($total_sum_pre_data_room_night) && $total_sum_pre_data_room_night>0){

                                $total_sum_data_room_night_variation = round(((($total_sum_data_room_night-$total_sum_pre_data_room_night)*100)/$total_sum_pre_data_room_night),2);

                            }else{

                                if($total_sum_data_room_night==0){

                                    $total_sum_data_room_night_variation = 0;

                                }else{

                                    $total_sum_data_room_night_variation = 100;

                                }

                            }





                            

                            if(!empty($total_sum_pre_data_electricity_tariff) && $total_sum_pre_data_electricity_tariff>0){

                                $total_sum_data_electricity_tariff_variation = round(((($total_sum_data_electricity_tariff-$total_sum_pre_data_electricity_tariff)*100)/$total_sum_pre_data_electricity_tariff),2);

                            }else{

                                if($total_sum_data_electricity_tariff==0){

                                    $total_sum_data_electricity_tariff_variation = 0;

                                }else{

                                    $total_sum_data_electricity_tariff_variation = 100;

                                }

                            }

                            



                            // Total variation

                            if(!empty($total_sum_pre_data_sum) && $total_sum_pre_data_sum>0){

                                $total_sum_data_variation = round(((($total_sum_data_sum-$total_sum_pre_data_sum)*100)/$total_sum_pre_data_sum),2);

                            }else{

                                if($total_sum_data_sum == 0){

                                    $total_sum_data_variation = 0;

                                }else{

                                    $total_sum_data_variation = 100;

                                }

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

                            <table border="1" width="100%" cellpadding="5" cellspacing="0">

                                <thead>

                                    <tr>

                                        <th width="38%" align="center" style="background-color:#d8e1f2;"><strong>Previous Year <?php echo $year-1; ?></strong></th>

                                        <th width="38%" align="center" style="background-color:#d8e1f2;"><strong>Current Year <?php echo $year; ?></strong></th>
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

                                                    <?php if($totalElectricity_utility_cost_pre && $site_detail['show_utility_electricity']){ ?>

                                                    <tr>
                                                        <td width="70%">Electricity</td>
                                                        <td width="30%"><?php echo number_format($total_sum_pre_data_electricity); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                    <?php if($totalFuel_utility_cost_pre && $site_detail['show_utility_fuel_oil']){ ?>

                                                    <tr>

                                                        <td>Fuel</td>

                                                        <td><?php echo number_format($total_sum_pre_data_fuel); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                    <?php if($totalLpg_utility_cost_pre && $site_detail['show_utility_lpg']){ ?>

                                                    <tr>

                                                        <td>LPG</td>

                                                        <td><?php echo number_format($total_sum_pre_data_lpg); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                    <?php if($totalNaturalGas_utility_cost_pre && $site_detail['show_utility_natural_gas']){ ?>

                                                    <tr>

                                                        <td>Natural Gas</td>

                                                        <td><?php echo number_format($total_sum_pre_data_natural_gas); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                    <?php if($totalHeatingDistrict_utility_cost_pre && $site_detail['show_utility_district_heating']){ ?>

                                                    <tr>

                                                        <td>District Heating</td>

                                                        <td><?php echo number_format($total_sum_pre_data_heating_district); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                    <?php if($totalCoolingDistrict_utility_cost_pre && $site_detail['show_utility_district_cooling']){ ?>

                                                    <tr>

                                                        <td>District Cooling</td>

                                                        <td><?php echo number_format($total_sum_pre_data_cooling_district); ?></td>

                                                    </tr>

                                                    <?php } ?>



                                                </tbody>

                                            </table>

                                        </td>

                                        <td width="38%">

                                            <table width="100%" cellpadding="0" cellspacing="0">

                                                <tbody>

                                                    <?php if($totalElectricity_utility_cost_pre && $site_detail['show_utility_electricity']){ ?>

                                                    <tr>
                                                        <td width="70%">Electricity</td>
                                                        <td width="30%"><?php echo number_format($total_sum_data_electricity); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalFuel_utility_cost_pre && $site_detail['show_utility_fuel_oil']){ ?>

                                                    <tr>

                                                        <td>Fuel</td>

                                                        <td><?php echo number_format($total_sum_data_fuel); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalLpg_utility_cost_pre && $site_detail['show_utility_lpg']){ ?>

                                                    <tr>

                                                        <td>LPG</td>

                                                        <td><?php echo number_format($total_sum_data_lpg); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalNaturalGas_utility_cost_pre && $site_detail['show_utility_natural_gas']){ ?>

                                                    <tr>

                                                        <td>Natural Gas</td>

                                                        <td><?php echo number_format($total_sum_data_natural_gas); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalHeatingDistrict_utility_cost_pre && $site_detail['show_utility_district_heating']){ ?>

                                                    <tr>

                                                        <td>District Heating</td>

                                                        <td><?php echo number_format($total_sum_data_heating_district); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalCoolingDistrict_utility_cost_pre && $site_detail['show_utility_district_cooling']){ ?>

                                                    <tr>

                                                        <td>District Cooling</td>

                                                        <td><?php echo number_format($total_sum_data_cooling_district); ?></td>

                                                    </tr>

                                                    <?php } ?>

                                                </tbody>

                                            </table>

                                        </td>

                                        <td width="24%" align="center">

                                            <table width="100%" cellpadding="0" cellspacing="0">

                                                <tbody>

                                                    <?php if($totalElectricity_utility_cost_pre && $site_detail['show_utility_electricity']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_electricity_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalFuel_utility_cost_pre && $site_detail['show_utility_fuel']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_fuel_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalLpg_utility_cost_pre && $site_detail['show_utility_lpg']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_lpg_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalNaturalGas_utility_cost_pre && $site_detail['show_utility_natural_gas']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_natural_gas_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalHeatingDistrict_utility_cost_pre && $site_detail['show_utility_heating_district']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_heating_district_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

                                                    <?php if($totalCoolingDistrict_utility_cost_pre && $site_detail['show_utility_cooling_district']){ ?>

                                                    <tr>

                                                        <td><?php echo $total_sum_data_cooling_district_variation; ?>%</td>

                                                    </tr>

                                                    <?php } ?>

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
                                    <!-- GARIMA -->
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

                        } ?>

                    </td>

                </tr>

            </table>

        </div>

    </body>

</html>