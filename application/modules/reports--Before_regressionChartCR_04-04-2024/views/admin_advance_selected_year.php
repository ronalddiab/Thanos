<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs','MonthPicker.min'));
echo add_css(array('MonthPicker.min'));

// Config array
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$time_type_list = array(
        'advance_select_choose_date' => lang('advance_select_choose_date'),
        'advance_select_choose_year' => lang('advance_select_choose_year'),
        'advance_select_avg_ytd'      => lang('advance_select_avg_ytd')
    );
$time_type_list_change = array(
        'advance_select_choose_year' => lang('advance_select_choose_year'),
        'advance_select_avg_ytd'      => lang('advance_select_avg_ytd')
    );
$time_type_list_change_last12month = array(
        'advance_select_choose_year' => lang('advance_select_choose_year'),
        'advance_select_12months'      => lang('advance_select_12months')
    );

$utility_types_list = array();
if($site_detail['show_utility_electricity']){
    $utility_types_list['electricity'] = 'Electricity';
}
if($site_detail['show_utility_fuel_oil']){
    $utility_types_list['fuel'] = 'Fuel';
}
if($site_detail['show_utility_lpg']){
    $utility_types_list['lpg'] = 'LPG';
}
if($site_detail['show_utility_natural_gas']){
    $utility_types_list['natural_gas'] = 'Natural Gas';
}
if($site_detail['show_utility_water']){
    $utility_types_list['water'] = 'Water';
}
if($site_detail['show_utility_district_heating']){
    $utility_types_list['heating_district'] = 'District Heating';
}
if($site_detail['show_utility_district_cooling']){
    $utility_types_list['cooling_district'] = 'District Cooling';
}


$utility_changer_list_electricity = array(
        'electricity_cost_per_room_night' => array(
                'title'=> lang('electricity_cost_per_room_night'),
                'utility_type'=>'electricity',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'electricity_cost_per_guest' => array(
                'title'=> lang('electricity_cost_per_guest'),
                'utility_type'=>'electricity',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'electricity_cost_per_built_area' => array(
                'title'=> lang('electricity_cost_per_built_area'),
                'utility_type'=>'electricity',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'electricity_cost_per_conditional_area' => array(
                'title'=> lang('electricity_cost_per_conditional_area'),
                'utility_type'=>'electricity',
                'base_type'=>'cost',
                'formtype'=>'usage_by_conditional_area'
            ),
        'electricity_kwh_per_room_night' => array(
                'title'=> lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('per_room_night'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'electricity_kwh_per_guest' => array(
                'title'=> lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('per_guest'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'electricity_kwh_per_built_area' => array(
                'title'=> lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('per_built_area'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'electricity_kwh_per_conditional_area' => array(
                'title'=> lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('per_cooled_area'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_conditional_area'
            ),
        'electricity_kwh_compare_last_year' => array(
                'title'=> lang('electricity_kwh_compare_last_year'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'electricity_kwh_budget_forecast' => array(
                'title'=> lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('vs_budget'),
                'utility_type'=>'electricity',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'electricity_cost_budget_forecast' => array(
                'title'=> lang('electricity_cost_budget_forecast'),
                'utility_type'=>'electricity',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            ) 
    );

	$utility_changer_list_heating = array(
		'district_heating_kwh_per_room_night' => array(
                'title'=> lang('heating_kwh_per_room_night'),
                'utility_type'=>'heating_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'district_heating_kwh_per_guest' => array(
                'title'=> lang('heating_kwh_per_guest'),
                'utility_type'=>'heating_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'district_heating_kwh_per_built_area' => array(
                'title'=> lang('heating_kwh_per_built_area'),
                'utility_type'=>'heating_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'district_heating_cost_per_room_night' => array(
                'title'=> lang('heating_cost_per_room_night'),
                'utility_type'=>'heating_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'district_heating_cost_per_guest' => array(
                'title'=> lang('heating_cost_per_guest'),
                'utility_type'=>'heating_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'district_heating_cost_per_built_area' => array(
                'title'=> lang('heating_cost_per_built_area'),
                'utility_type'=>'heating_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'district_heating_kwh_budget_forecast' => array(
                'title'=> lang('heating_kwh_budget_forecast'),
                'utility_type'=>'heating_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'district_heating_cost_budget_forecast' => array(
                'title'=> lang('heating_cost_budget_forecast'),
                'utility_type'=>'heating_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            )
        );	

$utility_changer_list_cooling = array(
		'district_cooling_kwh_per_room_night' => array(
                'title'=> lang('cooling_kwh_per_room_night'),
                'utility_type'=>'cooling_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'district_cooling_kwh_per_guest' => array(
                'title'=> lang('cooling_kwh_per_guest'),
                'utility_type'=>'cooling_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'district_cooling_kwh_per_built_area' => array(
                'title'=> lang('cooling_kwh_per_built_area'),
                'utility_type'=>'cooling_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'district_cooling_cost_per_room_night' => array(
                'title'=> lang('cooling_cost_per_room_night'),
                'utility_type'=>'cooling_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'district_cooling_cost_per_guest' => array(
                'title'=> lang('cooling_cost_per_guest'),
                'utility_type'=>'cooling_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'district_cooling_cost_per_built_area' => array(
                'title'=> lang('cooling_cost_per_built_area'),
                'utility_type'=>'cooling_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'district_cooling_kwh_budget_forecast' => array(
                'title'=> lang('cooling_kwh_budget_forecast'),
                'utility_type'=>'cooling_district',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'district_cooling_cost_budget_forecast' => array(
                'title'=> lang('cooling_cost_budget_forecast'),
                'utility_type'=>'cooling_district',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            )
        );
		
$utility_changer_list_lpg = array(
    'lpg_m3_per_room_night' => array(
                'title'=> lang('lpg_m3_per_room_night'),
                'utility_type'=>'lpg',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'lpg_m3_per_guest' => array(
                'title'=> lang('lpg_m3_per_guest'),
                'utility_type'=>'lpg',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'lpg_m3_per_built_area' => array(
                'title'=> lang('lpg_m3_per_built_area'),
                'utility_type'=>'lpg',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'lpg_cost_per_room_night' => array(
                'title'=> lang('lpg_cost_per_room_night'),
                'utility_type'=>'lpg',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'lpg_cost_per_guest' => array(
                'title'=> lang('lpg_cost_per_guest'),
                'utility_type'=>'lpg',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'lpg_cost_per_built_area' => array(
                'title'=> lang('lpg_cost_per_built_area'),
                'utility_type'=>'lpg',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'lpg_m3_budget_forecast' => array(
                'title'=> lang('lpg_m3_budget_forecast'),
                'utility_type'=>'lpg',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'lpg_cost_budget_forecast' => array(
                'title'=> lang('lpg_cost_budget_forecast'),
                'utility_type'=>'lpg',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            )
        );

$utility_changer_list_natural_gas = array(
    'natural_gas_m3_per_room_night' => array(
                'title'=> lang('natural_gas_m3_per_room_night'),
                'utility_type'=>'natural_gas',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'natural_gas_m3_per_guest' => array(
                'title'=> lang('natural_gas_m3_per_guest'),
                'utility_type'=>'natural_gas',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'natural_gas_m3_per_built_area' => array(
                'title'=> lang('natural_gas_m3_per_built_area'),
                'utility_type'=>'natural_gas',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'natural_gas_cost_per_room_night' => array(
                'title'=> lang('natural_gas_cost_per_room_night'),
                'utility_type'=>'natural_gas',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'natural_gas_cost_per_guest' => array(
                'title'=> lang('natural_gas_cost_per_guest'),
                'utility_type'=>'natural_gas',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'natural_gas_cost_per_built_area' => array(
                'title'=> lang('natural_gas_cost_per_built_area'),
                'utility_type'=>'natural_gas',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'natural_gas_m3_budget_forecast' => array(
                'title'=> lang('natural_gas_m3_budget_forecast'),
                'utility_type'=>'natural_gas',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'natural_gas_cost_budget_forecast' => array(
                'title'=> lang('natural_gas_cost_budget_forecast'),
                'utility_type'=>'natural_gas',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            )
    );

$utility_changer_list_fuel = array(
    'oil_liters_per_room_night' => array(
                'title'=> lang('oil_liters_per_room_night'),
                'utility_type'=>'fuel',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'oil_liters_per_guest' => array(
                'title'=> lang('oil_liters_per_guest'),
                'utility_type'=>'fuel',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'oil_liters_per_built_area' => array(
                'title'=> lang('oil_liters_per_built_area'),
                'utility_type'=>'fuel',
                'base_type'=>'unit',
                'formtype'=>'usage_by_built_area'
            ),
        'oil_cost_per_room_night' => array(
                'title'=> lang('oil_cost_per_room_night'),
                'utility_type'=>'fuel',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'oil_cost_per_guest' => array(
                'title'=> lang('oil_cost_per_guest'),
                'utility_type'=>'fuel',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'oil_cost_per_built_area' => array(
                'title'=> lang('oil_cost_per_built_area'),
                'utility_type'=>'fuel',
                'base_type'=>'cost',
                'formtype'=>'usage_by_built_area'
            ),
        'oil_liters_budget_forecast' => array(
                'title'=> lang('oil_liters_budget_forecast'),
                'utility_type'=>'fuel',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            ),
        'oil_cost_budget_forecast' => array(
                'title'=> lang('oil_cost_budget_forecast'),
                'utility_type'=>'fuel',
                'base_type'=>'cost',
                'formtype'=>'usage_by_utility'
            )
    );

$utility_changer_list_water = array(
    'water_liters_per_room_night' => array(
                'title'=> lang('water_liters_per_room_night'),
                'utility_type'=>'water',
                'base_type'=>'unit',
                'formtype'=>'usage_by_room_nights'
            ),
        'water_liters_per_guest' => array(
                'title'=> lang('water_liters_per_guest'),
                'utility_type'=>'water',
                'base_type'=>'unit',
                'formtype'=>'usage_by_guests'
            ),
        'water_liters_per_laundered' => array(
                'title'=> lang('water_liters_per_laundered'),
                'utility_type'=>'water',
                'base_type'=>'unit',
                'formtype'=>'usage_by_laundered'
            ),
        'water_cost_per_room_night' => array(
                'title'=> lang('water_cost_per_room_night'),
                'utility_type'=>'water',
                'base_type'=>'cost',
                'formtype'=>'usage_by_room_nights'
            ),
        'water_cost_per_guest' => array(
                'title'=> lang('water_cost_per_guest'),
                'utility_type'=>'water',
                'base_type'=>'cost',
                'formtype'=>'usage_by_guests'
            ),
        'water_liters_utility_cisterns_ro' => array(
                'title'=> lang('water_liters_utility_cisterns_ro'),
                'utility_type'=>'water',
                'base_type'=>'unit',
                'formtype'=>'usage_by_utility'
            )
    );

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

$current_year_text = lang("current-year");
$previous_year_text = lang("previous-year");

// Override if selected period is not for current year
if($filters['start_year'] != date("Y")){
    $current_year_text = 'Year - '.$filters['start_year'];
    $previous_year_text = 'Year - '.($filters['start_year']-1);
}
?>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>
<script type="text/javascript">
    <?php if (!empty($reportdata)) {  ?>
        google.load("visualization", "1", {packages: ["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', '<?php echo lang("month"); ?>');
            dataTable.addColumn('number', '<?php echo $previous_year_text; ?>');
            dataTable.addColumn('number', '<?php echo $current_year_text; ?>');
            <?php if($is_occupancy){ ?>
                dataTable.addColumn('number', '<?php echo (date("Y")-1)." - ".lang("occupancy"); ?>');
                dataTable.addColumn('number', '<?php echo date("Y")." - ".lang("occupancy"); ?>');
            <?php } ?>

            <?php 
            $total_sum_current = 0;
            $total_sum_pre = 0;
            $total_sum_budget = 0;
            $total_months = 0;
            $total_months_pre=0;
            $YTD_total_months = $this->_ci->config->config['YTD_month_count'];
            foreach ($resultkeys as $year => $value) {
                foreach ($value as $key1 => $month) {
                    $monthdata = $montharray[$month] . ' ' . $year;
					$previousdata = (!empty($reportdata[$month][$year - 1][$filters['utility_type']])) ? $reportdata[$month][$year - 1][$filters['utility_type']] : 0;
                    $currentdata = (!empty($reportdata[$month][$year][$filters['utility_type']])) ? $reportdata[$month][$year][$filters['utility_type']] : 0;
                    $budgetdata = (!empty($reportdata[$month][$year][$filters['utility_type'] . '_budget'])) ? $reportdata[$month][$year][$filters['utility_type'] . '_budget'] : 0;
                    $cdddata = (!empty($reportdata[$month][$year]['cdd']))?$reportdata[$month][$year]['cdd']:0;
                    $hdddata = (!empty($reportdata[$month][$year]['hdd']))?$reportdata[$month][$year]['hdd']:0;
                    $occupancydata = (!empty($reportdata[$month][$year]['occupancy']))?$reportdata[$month][$year]['occupancy']:0;
                    $previousoccupancydata = (!empty($reportdata[$month][$year-1]['occupancy']))?$reportdata[$month][$year-1]['occupancy']:0;

                    $previousdata = round($previousdata,2);
                    
                    $currentdata = round($currentdata,2);
                    $budgetdata = round($budgetdata,2);
                    $occupancydata = round($occupancydata,2);
                    $previousoccupancydata = round($previousoccupancydata,2);
                    
                    $total_months_pre++;

                    // For average up to data available
                    if ($filters['CURRENT_YEAR_MAX_MONTH_ID'] >= $month) {
                        $total_sum_pre += $previousdata;
                        $total_sum_current += $currentdata;
                        $total_sum_budget += $budgetdata;
                        $total_sum_cdd += $cdddata;
                        $total_sum_hdd += $hdddata;
                        $total_sum_occupancy += $occupancydata;
                        $total_sum_previousoccupancy += $previousoccupancydata;
                        $total_months++;
                    }
                    ?>
                    dataTable.addRow(["<?php echo $monthdata; ?>", <?php echo $previousdata; ?>, <?php echo $currentdata; ?><?php if($is_occupancy){echo ','.$previousoccupancydata;echo ','.$occupancydata; } ?>]);
                    <?php
                }
            }
            ?>

            <?php
            if($total_months>0){
                $previousAvgData = ($total_sum_pre / $total_months);
                $currentAvgData = ($total_sum_current / $total_months);
                $budgetAvgData = ($total_sum_budget / $total_months);
                $cddAvgData = ($total_sum_cdd / $total_months);
                $hddAvgData = ($total_sum_hdd / $total_months);
                $occupancyAvgData = ($total_sum_occupancy / $total_months);
                $previousoccupancyAvgData = ($total_sum_previousoccupancy / $total_months);
            }else{
                $previousAvgData = 0;
                $currentAvgData = 0;
                $budgetAvgData = 0;
                $cddAvgData = 0;
                $hddAvgData = 0;
                $occupancyAvgData = 0;
                $previousoccupancyAvgData = 0;
            }
            
            $previousAvgData = round($previousAvgData,2);
            $currentAvgData = round($currentAvgData,2);
            $budgetAvgData = round($budgetAvgData,2);
            $cddAvgData = round($cddAvgData,2);
            $hddAvgData = round($hddAvgData,2);
            $occupancyAvgData = round($occupancyAvgData,2);

            ?>
            dataTable.addRow(["<?php echo lang('average'); ?>",<?php echo $previousAvgData; ?>, <?php echo $currentAvgData; ?><?php if($is_occupancy){echo ','.$previousoccupancyAvgData;echo ','.$occupancyAvgData; } ?>]);

            var options = {
                title: '<?php echo $view_title; ?>',
                titleTextStyle: {
                  fontName: 'Arial',
                  fontSize: 28
                },
                hAxis: {title: '<?php echo lang("haxis-title"); ?>',titleTextStyle:{fontName:'Arial',fontSize: 18}},
                vAxes: {
                    0: { title:'<?php echo $x_axis_title_value; ?>',titleTextStyle: {fontName: 'Arial',fontSize: 18}},
                    <?php if($is_occupancy){ ?>
                        1: { title:'<?php echo lang("occupancy"); ?>',titleTextStyle: {fontName: 'Arial',fontSize: 18},'minValue': 100 ,ticks: [0,10,20,30,40,50,60,70,80,90,100] }
                    <?php } ?>
                },
                seriesType: 'bars',
                series: {
                    0: { targetAxisIndex: 0 },
                    1: { targetAxisIndex: 0 },              
                    <?php if($is_occupancy){
                        ?>2: { targetAxisIndex: 1, type: "line" ,pointShape:'square',pointSize:10},3: { targetAxisIndex: 1, type: "line" ,pointShape:'square',pointSize:10},<?php 
                    } ?>
                },
                animation: {duration: 500, startup: true},
                legend: {'position': 'bottom'}
            };

            var chart = new google.visualization.ComboChart(document.getElementById('utility_report'));
            chart.draw(dataTable, options);
        }

        $(window).resize(function() {
            drawChart();
        });

        <?php } ?>

        $(document).ready(function() {
            $(".monthpicker_input").MonthPicker();
            // Select change start
            function genratereporttypeselect(selected_value){
                var selectbox = '<select name="utilitychanger" id="utilitychanger" data-type="custom-dropdown-report">';
                    
                if(selected_value == 'electricity'){
                    <?php
                    foreach ($utility_changer_list_electricity as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'fuel'){
                    <?php
                    foreach ($utility_changer_list_fuel as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'lpg'){
                    <?php
                    foreach ($utility_changer_list_lpg as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'heating_district'){
                    <?php
                    foreach ($utility_changer_list_heating as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'cooling_district'){
                    <?php
                    foreach ($utility_changer_list_cooling as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'natural_gas'){
                    <?php
                    foreach ($utility_changer_list_natural_gas as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }else if(selected_value == 'water'){
                    <?php
                    foreach ($utility_changer_list_water as $key => $value) {
                        $selected_sel = ($key==$utilitychanger)?'selected="selected"':'';
                        ?>
                        selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
                        <?php
                    }
                    ?>
                }

                selectbox += '</select>';
                $("#utilitychanger_div").html($(selectbox));
                $("select[data-type='custom-dropdown-report']").dropkick({
                    mobile: true
                });

                // Set form data for default selected type
                setTimeout(function(){
                    $("#utilitychanger").trigger("change");
                },100);
                
            }

            $("#utility_type_select").change(function(){
                var selected_value = $(this).val();
                genratereporttypeselect(selected_value);
            });
            genratereporttypeselect('<?php echo $utility_type_select; ?>');

            $("#utilitychanger_div").on('change','#utilitychanger',function(){
                var utility_type = $('option:selected',this).data('utility-type');
                var base_type = $('option:selected',this).data('base-type');
                var formtype = $('option:selected',this).data('formtype');

                $("#utility_type").val(utility_type);
                $("#base_type").val(base_type);
                $("#formtype").val(formtype);

                var finalchartvalue = $(this).val();
                genratetimetypeselect(finalchartvalue);
                $("#time_type").trigger("change");
            });

            function genratetimetypeselect(selected_value){
                <?php $timetypeselectbox = str_replace("\n", '', form_dropdown('time_type', $time_type_list, $time_type, 'id="time_type" data-type="custom-dropdown-timetype"')); ?>
                <?php $timetypeselectbox_change = str_replace("\n", '', form_dropdown('time_type', $time_type_list_change, $time_type, 'id="time_type" data-type="custom-dropdown-timetype"')); ?>
                <?php $timetypeselectbox_change_last12month = str_replace("\n", '', form_dropdown('time_type', $time_type_list_change_last12month, $time_type, 'id="time_type" data-type="custom-dropdown-timetype"')); ?>

                if(selected_value=='electricity_kwh_compare_last_year' 
                        || selected_value == 'electricity_kwh_budget_forecast' 
                        || selected_value=='electricity_cost_budget_forecast'
                        || selected_value=='lpg_m3_budget_forecast'
                        || selected_value=='lpg_cost_budget_forecast'
						|| selected_value=='district_heating_kwh_budget_forecast'
                        || selected_value=='district_heating_cost_budget_forecast'
						|| selected_value=='district_cooling_kwh_budget_forecast'
                        || selected_value=='district_cooling_cost_budget_forecast'
                        || selected_value=='natural_gas_m3_budget_forecast'
                        || selected_value=='natural_gas_cost_budget_forecast'
                        || selected_value=='oil_liters_budget_forecast'
                        || selected_value=='oil_cost_budget_forecast'
                        || selected_value=='water_liters_utility_cisterns_ro'
                        || selected_value=='report_title_water_cost_budget_forecast'){
                    var selectbox = '<?php echo $timetypeselectbox_change ?>';
                }/*else if(selected_value=='water_liters_utility_cisterns_ro'){
                    var selectbox = '<?php echo $timetypeselectbox_change_last12month ?>';
                }*/else{
                    var selectbox = '<?php echo $timetypeselectbox ?>';
                }

                $("#timetypeselect").html($(selectbox));
                $("select[data-type='custom-dropdown-timetype']").dropkick({
                    mobile: true
                });
            }

            var time_type_val = $('#time_type').val();
            setreporttime(time_type_val);

            $("#timetypeselect").on('change','#time_type',function(){
                setreporttime($(this).val());
            });

            function setreporttime(time_type_val){
                if(time_type_val=='advance_select_choose_date'){
                    $("#month_picker_box").show();
                }else{
                    $("#month_picker_box").hide();
                }
                if(time_type_val=='advance_select_choose_year'){
                    $("#year_picker_box").show();
                }else{
                    $("#year_picker_box").hide();
                }
            }
            // Select change end

            // Validation
            $.validator.addMethod('validDate', function(value, element) {
                return this.optional(element) || /^(0?[1-9]|1[012])[ /][0-9]{4}$/.test(value);
            }, 'Please provide a date in the mm/yyyy format');

            $.validator.addMethod('dateBefore', function(value, element, params) {
                var end = $(params);
                if (value == '' || end.val() == '') {
                    return true;
                } else {
                    var endval = end.val().split('/');
                    var startval = value.split('/');
                    return new Date(startval[1], startval[0], 1) < new Date(endval[1], endval[0], 1);
                }
            }, 'Must be before corresponding end date');

            $.validator.addMethod('dateAfter', function(value, element, params) {
                var start = $(params);
                var endval = value.split('/');
                var startval = start.val().split('/');
                return new Date(endval[1], endval[0], 1) > new Date(startval[1], startval[0], 1);
            }, 'Must be after corresponding start date');

            $.validator.addMethod('monthdefer', function(value, element, params) {
                var start = $(params).val();
                var end = value;
                if (start == '' || end == '') {
                    return true;
                } else {
                    var startval = start.split('/');
                    var endval = end.split('/');

                    var startdate = new Date(startval[1], startval[0], 1);
                    var enddate = new Date(endval[1], endval[0], 1);

                    var diffDays = parseInt((enddate - startdate) / (1000 * 60 * 60 * 24));
                    return (diffDays < 364);
                }
            }, 'Max. 12 months');

            /*$.validator.setDefaults({
                ignore: []
            });*/

            $('#report_form_utility').validate({// initialize the plugin
                rules: {
                    startdate: {
                        dateBefore: '#enddate_utility'
                    },
                    enddate: {
                        dateAfter: '#startdate_utility',
                        monthdefer: '#startdate_utility'
                    }
                }
            });

            $("#genrate-report").click(function(){
                $("#view_type").val('');
                $("#report_form_utility").submit();
            });

            $("#genrate-excel").click(function(){
                $("#view_type").val('excel');
                $("#report_form_utility").submit();
            });
        });
</script>

<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header"><?php echo lang('advance_reports'); ?></div>
        <div class="card-wrap">
            <div class="row">
                <div class="col-sm-12">
                    <div class="data-info-block-outer">
                        <form id="report_form_utility" method="post"> 
                            <div class="row">
                                <div class="col-sm-6 col-md-5">
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label><?php echo lang('utilities'); ?></label>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="form-dropdown">
                                                <?php echo form_dropdown('utility_type_select', $utility_types_list, $utility_type_select, 'id="utility_type_select" data-type="custom-dropdown"'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label><?php echo lang('report-type-title'); ?></label>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="form-dropdown">
                                                <div id="utilitychanger_div">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <label><?php echo lang('advance_select_time'); ?></label>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="form-dropdown">
                                                <div id="timetypeselect" class="form-dropdown">
                                                    <?php echo form_dropdown('time_type', $time_type_list, $time_type, 'id="time_type" data-type="custom-dropdown"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row report-data-block">
                                <div id="month_picker_box" class="clearfix">
                                    <div class="col-sm-6 col-md-5 dateinputs">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <label><?php echo lang('start-date'); ?></label>
                                            </div>
                                            <div class="col-sm-7">
                                                <div class="data-info-block">
                                                    <input type="text" id="startdate_utility" name="startdate" class='Default validDate monthpicker_input' value="<?php echo (!empty($filters['startdate'])) ? $filters['startdate'] : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-5 dateinputs">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <label><?php echo lang('end-date'); ?></label>
                                            </div>
                                            <div class="col-sm-7">
                                                <div class="data-info-block">
                                                    <input type="text" id="enddate_utility" name="enddate" class='Default validDate monthpicker_input' value="<?php echo (!empty($filters['enddate'])) ? $filters['enddate'] : ''; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <div id="year_picker_box" class="clearfix">
                                    <div class="col-sm-6 col-md-5 dateinputs">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <label><?php echo lang('choose-year'); ?></label>
                                            </div>
                                             <div class="col-sm-7">
                                                <div class="form-dropdown">
                                                <?php
                                                    //get the current year
                                                    $Startyear=date('Y');
                                                    $endYear=$Startyear-10;

                                                    // set start and end year range i.e the start year
                                                    $yearArray = range($Startyear,$endYear);
                                                    ?>
                                                    <select name="year" data-type="custom-dropdown">
                                                        <option value="">Select Year</option>
                                                        <?php
                                                        foreach ($yearArray as $year) {
                                                            // this allows you to select a particular year
                                                            $selected_year = (!empty($filters['selected_year'])) ? $filters['selected_year'] : '';
                                                            if($selected_year && $year == $selected_year){
                                                                $selected = 'selected';
                                                            } else{
                                                                $selected = '';
                                                            }
                                                            echo '<option '.$selected.' value="'.$year.'">'.$year.'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="utility_type" name="utility_type" value="<?php echo $utility_type; ?>" />
                                <input type="hidden" id="base_type" name="base_type" value="<?php echo $base_type; ?>" />
                                <input type="hidden" id="formtype" name="formtype" value="<?php echo $formtype; ?>" />
                                <input type="hidden" id="view_type" name="view_type" value="" />
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-sm-2 gen-report">
                                    <input id="genrate-report" type="submit" value="<?php echo lang('generate-report'); ?>">
                                </div>
                                <div class="col-sm-2 gen-report">
                                    <input id="genrate-excel" type="button" value="<?php echo lang('generate-excel'); ?>">
                                </div>
                            </div>
                        </form>  
                    </div>

					<div id="utility_report" style="height:500px;margin-top:100px;">
                        <?php if(empty($reportdata)){ ?>
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
    </article>
</div>