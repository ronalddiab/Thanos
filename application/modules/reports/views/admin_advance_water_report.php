<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
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

<script type="text/javascript">
	$(document).ready(function() {
		$(".monthpicker_input").MonthPicker();
		// Select change start
		function genratereporttypeselect(selected_value) {
			var selectbox = '<select name="utilitychanger" id="utilitychanger" data-type="custom-dropdown-report">';

			if (selected_value == 'electricity') {
				<?php
				foreach ($utility_changer_list_electricity as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'fuel') {
				<?php
				foreach ($utility_changer_list_fuel as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'lpg') {
				<?php
				foreach ($utility_changer_list_lpg as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'heating_district') {
				<?php
				foreach ($utility_changer_list_heating as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'cooling_district') {
				<?php
				foreach ($utility_changer_list_cooling as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'natural_gas') {
				<?php
				foreach ($utility_changer_list_natural_gas as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
				?>
					selectbox += '<option value="<?php echo $key; ?>" <?php echo $selected_sel; ?> class="list-of-<?php echo $value[utility_type]; ?>" data-utility-type="<?php echo $value[utility_type]; ?>" data-base-type="<?php echo $value[base_type]; ?>" data-formtype="<?php echo $value[formtype]; ?>"><?php echo $value[title]; ?></option>';
				<?php
				}
				?>
			} else if (selected_value == 'water') {
				<?php
				foreach ($utility_changer_list_water as $key => $value) {
					$selected_sel = ($key == $utilitychanger) ? 'selected="selected"' : '';
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

			if (selected_value == 'electricity_kwh_compare_last_year' ||
				selected_value == 'electricity_kwh_budget_forecast' ||
				selected_value == 'electricity_cost_budget_forecast' ||
				selected_value == 'lpg_m3_budget_forecast' ||
				selected_value == 'lpg_cost_budget_forecast' ||
				selected_value == 'district_heating_kwh_budget_forecast' ||
				selected_value == 'district_heating_cost_budget_forecast' ||
				selected_value == 'district_cooling_kwh_budget_forecast' ||
				selected_value == 'district_cooling_cost_budget_forecast' ||
				selected_value == 'natural_gas_m3_budget_forecast' ||
				selected_value == 'natural_gas_cost_budget_forecast' ||
				selected_value == 'oil_liters_budget_forecast' ||
				selected_value == 'oil_cost_budget_forecast' ||
				selected_value == 'water_liters_utility_cisterns_ro' ||
				selected_value == 'report_title_water_cost_budget_forecast') {
				var selectbox = '<?php echo $timetypeselectbox_change ?>';
			}
			else {
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
					<div id="utility_report" style="height:700px;margin-top:100px;">
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
<script>
	drawHighchart();

	function drawHighchart() {
		var utilityWaterBudgetReportArray = [];

		var commonwaterutilitydata_tooltip_Array = [];
		var commonwatercisternsdata_tooltip_Array = [];
		var commonwaterrodata_tooltip_Array = [];
		var commonbudgetdata_tooltip_Array = [];
		var commoncdddata_tooltip_Array = [];
		var commonhdddata_tooltip_Array = [];
		var commonoccupancydata_tooltip_Array = [];

		utilityWaterBudgetReportArray.push(['<?php echo lang("month"); ?>',
			'<?php echo lang("water-utility"); ?>',
			'<?php echo lang("water-cisterns"); ?>',
			'<?php echo lang("water-ro"); ?>',
			'<?php echo lang("budget"); ?>',
			<?php if ($is_occupancy) { ?> '<?php echo lang("occupancy"); ?>'
			<?php } ?>
		]);

		<?php
		$total_sum_cisterns = 0;
		$total_sum_utility = 0;
		$total_sum_ro = 0;
		$total_sum_budget = 0;
		$total_months = 0;
		$pre_total_months = 0;
		foreach ($resultkeys as $year => $value) {
			foreach ($value as $key1 => $month) {
				//Previous year data
				$pre_monthdata = $montharray[$month] . ' ' . ($year - 1);
				$pre_waterutilitydata = (!empty($reportdata[$month][$year - 1]['water_utility']) && is_numeric($reportdata[$month][$year - 1]['water_utility']) && is_finite($reportdata[$month][$year - 1]['water_utility'])) ? $reportdata[$month][$year - 1]['water_utility'] : 0;
				$pre_watercisternsdata = (!empty($reportdata[$month][$year - 1]['water_cisterns']) && is_numeric($reportdata[$month][$year - 1]['water_cisterns']) && is_finite($reportdata[$month][$year - 1]['water_cisterns'])) ? $reportdata[$month][$year - 1]['water_cisterns'] : 0;
				$pre_waterrodata = (!empty($reportdata[$month][$year - 1]['water_irrigation']) && is_numeric($reportdata[$month][$year - 1]['water_irrigation']) && is_finite($reportdata[$month][$year - 1]['water_irrigation'])) ? $reportdata[$month][$year - 1]['water_irrigation'] : 0;
				$pre_budgetdata = (!empty($reportdata[$month][$year - 1][$filters['utility_type'] . '_budget']) && is_numeric($reportdata[$month][$year - 1][$filters['utility_type'] . '_budget']) && is_finite($reportdata[$month][$year - 1][$filters['utility_type'] . '_budget'])) ? $reportdata[$month][$year - 1][$filters['utility_type'] . '_budget'] : 0;
				$pre_cdddata = (!empty($reportdata[$month][$year - 1]['cdd']) && is_numeric($reportdata[$month][$year - 1]['cdd']) && is_finite($reportdata[$month][$year - 1]['cdd'])) ? $reportdata[$month][$year - 1]['cdd'] : 0;
				$pre_hdddata = (!empty($reportdata[$month][$year - 1]['hdd']) && is_numeric($reportdata[$month][$year - 1]['hdd']) && is_finite($reportdata[$month][$year - 1]['hdd'])) ? $reportdata[$month][$year - 1]['hdd'] : 0;
				$pre_occupancydata = (!empty($reportdata[$month][$year - 1]['occupancy']) && is_numeric($reportdata[$month][$year - 1]['occupancy']) && is_finite($reportdata[$month][$year - 1]['occupancy'])) ? $reportdata[$month][$year - 1]['occupancy'] : 0;

				$pre_waterutilitydata = round($pre_waterutilitydata, 2);
				$pre_watercisternsdata = round($pre_watercisternsdata, 2);
				$pre_waterrodata = round($pre_waterrodata, 2);
				$pre_budgetdata = round($pre_budgetdata, 2);
				$pre_occupancydata = round($pre_occupancydata, 2);

				//Current year data
				$monthdata = $montharray[$month] . ' ' . $year;
				$waterutilitydata = (!empty($reportdata[$month][$year]['water_utility']) && is_numeric($reportdata[$month][$year]['water_utility']) && is_finite($reportdata[$month][$year]['water_utility'])) ? $reportdata[$month][$year]['water_utility'] : 0;
				$watercisternsdata = (!empty($reportdata[$month][$year]['water_cisterns']) && is_numeric($reportdata[$month][$year]['water_cisterns']) && is_finite($reportdata[$month][$year]['water_cisterns'])) ? $reportdata[$month][$year]['water_cisterns'] : 0;
				$waterrodata = (!empty($reportdata[$month][$year]['water_irrigation']) && is_numeric($reportdata[$month][$year]['water_irrigation']) && is_finite($reportdata[$month][$year]['water_irrigation'])) ? $reportdata[$month][$year]['water_irrigation'] : 0;
				$budgetdata = (!empty($reportdata[$month][$year][$filters['utility_type'] . '_budget']) && is_numeric($reportdata[$month][$year][$filters['utility_type'] . '_budget']) && is_finite($reportdata[$month][$year][$filters['utility_type'] . '_budget'])) ? $reportdata[$month][$year][$filters['utility_type'] . '_budget'] : 0;
				$cdddata = (!empty($reportdata[$month][$year]['cdd']) && is_numeric($reportdata[$month][$year]['cdd']) && is_finite($reportdata[$month][$year]['cdd'])) ? $reportdata[$month][$year]['cdd'] : 0;
				$hdddata = (!empty($reportdata[$month][$year]['hdd']) && is_numeric($reportdata[$month][$year]['hdd']) && is_finite($reportdata[$month][$year]['hdd'])) ? $reportdata[$month][$year]['hdd'] : 0;
				$occupancydata = (!empty($reportdata[$month][$year]['occupancy']) && is_numeric($reportdata[$month][$year]['occupancy']) && is_finite($reportdata[$month][$year]['occupancy'])) ? $reportdata[$month][$year]['occupancy'] : 0;

				$waterutilitydata = round($waterutilitydata, 2);
				$watercisternsdata = round($watercisternsdata, 2);
				$waterrodata = round($waterrodata, 2);
				$budgetdata = round($budgetdata, 2);
				$occupancydata = round($occupancydata, 2);


				/*###################Previous year###################*/
				// Utility Variant
				$pre_deference_value = $pre_budgetdata - $pre_waterutilitydata;
				if ($pre_waterutilitydata > 0) {
					$pre_utility_percentage = (($pre_deference_value * 100) / $pre_waterutilitydata);
					$pre_utility_percentage = round($pre_utility_percentage, 2);
				} else {
					$pre_utility_percentage = 100;
				}

				if ($pre_utility_percentage > 0) {
					$pre_utility_pclass = 'nagetive';
					$pre_utility_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($pre_utility_percentage < 0) {
					$pre_utility_pclass = 'positive';
					$pre_utility_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$pre_utility_pclass = '';
					$pre_utility_parrow = '';
				}

				// cisterns Variant
				$pre_deference_value = $pre_budgetdata - $pre_watercisternsdata;
				if ($pre_watercisternsdata > 0) {
					$pre_cisterns_percentage = (($pre_deference_value * 100) / $pre_watercisternsdata);
					$pre_cisterns_percentage = round($pre_cisterns_percentage, 2);
				} else {
					$pre_cisterns_percentage = 100;
				}

				if ($pre_cisterns_percentage > 0) {
					$pre_cisterns_pclass = 'nagetive';
					$pre_cisterns_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($pre_cisterns_percentage < 0) {
					$pre_cisterns_pclass = 'positive';
					$pre_cisterns_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$pre_cisterns_pclass = '';
					$pre_cisterns_parrow = '';
				}

				// RO Variant
				$pre_deference_value = $pre_budgetdata - $pre_waterrodata;
				if ($pre_waterrodata > 0) {
					$pre_ro_percentage = (($pre_deference_value * 100) / $pre_waterrodata);
					$pre_ro_percentage = round($pre_ro_percentage, 2);
				} else {
					$pre_ro_percentage = 100;
				}

				if ($pre_ro_percentage > 0) {
					$pre_ro_pclass = 'nagetive';
					$pre_ro_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($pre_ro_percentage < 0) {
					$pre_ro_pclass = 'positive';
					$pre_ro_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$pre_ro_pclass = '';
					$pre_ro_parrow = '';
				}

				// Remove - sign
				$pre_utility_percentage = abs($pre_utility_percentage);
				$pre_cisterns_percentage = abs($pre_cisterns_percentage);
				$pre_ro_percentage = abs($pre_ro_percentage);

				$pre_waterutilitydata_tooltip = '<div class=\"gc-tooltip\"><div class="\gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('actual') . ': ' . $pre_waterutilitydata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_utility_pclass . '\">' . $pre_utility_parrow . ' ' . $pre_utility_percentage . '%</span></div></div>';
				$pre_watercisternsdata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('actual') . ': ' . $pre_watercisternsdata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_cisterns_pclass . '\">' . $pre_cisterns_parrow . ' ' . $pre_cisterns_percentage . '%</span></div></div>';
				$pre_waterrodata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('actual') . ': ' . $pre_waterrodata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_ro_pclass . '\">' . $pre_ro_parrow . ' ' . $pre_ro_percentage . '%</span></div></div>';
				$pre_budgetdata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('budget') . ': ' . $pre_budgetdata . '</div></div>';
				$pre_cdddata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('cdd') . ': ' . $pre_cdddata . '</div></div>';
				$pre_hdddata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('hdd') . ': ' . $pre_hdddata . '</div></div>';
				$pre_occupancydata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . ($year - 1) . '</strong></div><div>' . lang('occupancy') . ': ' . $pre_occupancydata . '</div></div>';
				/*###################Previous year###################*/

				/*###################Current year####################*/
				// Utility Variant
				$deference_value = $budgetdata - $waterutilitydata;
				if ($waterutilitydata > 0) {
					$utility_percentage = (($deference_value * 100) / $waterutilitydata);
					$utility_percentage = round($utility_percentage, 2);
				} else {
					$utility_percentage = 100;
				}

				if ($utility_percentage > 0) {
					$utility_pclass = 'nagetive';
					$utility_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($utility_percentage < 0) {
					$utility_pclass = 'positive';
					$utility_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$utility_pclass = '';
					$utility_parrow = '';
				}

				// cisterns Variant
				$deference_value = $budgetdata - $watercisternsdata;
				if ($watercisternsdata > 0) {
					$cisterns_percentage = (($deference_value * 100) / $watercisternsdata);
					$cisterns_percentage = round($cisterns_percentage, 2);
				} else {
					$cisterns_percentage = 100;
				}

				if ($cisterns_percentage > 0) {
					$cisterns_pclass = 'nagetive';
					$cisterns_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($cisterns_percentage < 0) {
					$cisterns_pclass = 'positive';
					$cisterns_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$cisterns_pclass = '';
					$cisterns_parrow = '';
				}

				// RO Variant
				$deference_value = $budgetdata - $waterrodata;
				if ($waterrodata > 0) {
					$ro_percentage = (($deference_value * 100) / $waterrodata);
					$ro_percentage = round($ro_percentage, 2);
				} else {
					$ro_percentage = 100;
				}

				if ($ro_percentage > 0) {
					$ro_pclass = 'nagetive';
					$ro_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
				} else if ($ro_percentage < 0) {
					$ro_pclass = 'positive';
					$ro_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
				} else {
					$ro_pclass = '';
					$ro_parrow = '';
				}

				// Remove - sign
				$utility_percentage = abs($utility_percentage);
				$cisterns_percentage = abs($cisterns_percentage);
				$ro_percentage = abs($ro_percentage);

				$waterutilitydata_tooltip_Array = array();
				$watercisternsdata_tooltip_Array = array();
				$waterrodata_tooltip_Array = array();
				$budgetdata_tooltip_Array = array();
				$cdddata_tooltip_Array = array();
				$hdddata_tooltip_Array = array();
				$occupancydata_tooltip_Array = array();

				$waterutilitydata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('actual') . ': ' . $waterutilitydata . '</div><div>' . lang('vs-budget') . ' : <span class="variant-' . $utility_pclass . '">' . $utility_parrow . ' ' . $utility_percentage . '%</span></div></div>';
				$watercisternsdata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('actual') . ': ' . $watercisternsdata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $cisterns_pclass . '\">' . $cisterns_parrow . ' ' . $cisterns_percentage . '%</span></div></div>';
				$waterrodata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('actual') . ': ' . $waterrodata . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $ro_pclass . '\">' . $ro_parrow . ' ' . $ro_percentage . '%</span></div></div>';
				$budgetdata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('budget') . ': ' . $budgetdata . '</div></div>';
				$cdddata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('cdd') . ': ' . $cdddata . '</div></div>';
				$hdddata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('hdd') . ': ' . $hdddata . '</div></div>';
				$occupancydata_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>' . $montharray[$month] . ' ' . $year . '</strong></div><div>' . lang('occupancy') . ': ' . $occupancydata . '</div></div>';
				/*###################Current year####################*/

				if ($filters['CURRENT_YEAR_MAX_MONTH_ID'] >= $month) {
					//Previous year
					$pre_total_sum_utility += $pre_waterutilitydata;
					$pre_total_sum_cisterns += $pre_watercisternsdata;
					$pre_total_sum_ro += $pre_waterrodata;
					$pre_total_sum_budget += $pre_budgetdata;
					$pre_total_sum_cdd += $pre_cdddata;
					$pre_total_sum_hdd += $pre_hdddata;
					$pre_total_sum_occupancy += $pre_occupancydata;

					// Current year
					$total_sum_utility += $waterutilitydata;
					$total_sum_cisterns += $watercisternsdata;
					$total_sum_ro += $waterrodata;
					$total_sum_budget += $budgetdata;
					$total_sum_cdd += $cdddata;
					$total_sum_hdd += $hdddata;
					$total_sum_occupancy += $occupancydata;
					$total_months++;
					$pre_total_months++;
				}
		?>
				utilityWaterBudgetReportArray.push(["<?php echo $pre_monthdata; ?>", <?php echo $pre_waterutilitydata; ?>, <?php echo $pre_watercisternsdata; ?>, <?php echo $pre_waterrodata; ?>, null, <?php if ($is_occupancy) {
																																																				echo $pre_occupancydata;
																																																			} ?>]);
				utilityWaterBudgetReportArray.push(["<?php echo $monthdata; ?>", <?php echo $waterutilitydata; ?>, <?php echo $watercisternsdata; ?>, <?php echo $waterrodata; ?>, <?php echo $budgetdata; ?>, <?php if ($is_occupancy) {
																																																					echo $occupancydata;
																																																				} ?>]);
				//for previous tooltip array
				commonwaterutilitydata_tooltip_Array.push('<?php echo $pre_waterutilitydata_tooltip; ?>');
				commonwaterutilitydata_tooltip_Array.push('<?php echo $waterutilitydata_tooltip; ?>');
				commonwatercisternsdata_tooltip_Array.push('<?php echo $pre_watercisternsdata_tooltip; ?>');
				commonwatercisternsdata_tooltip_Array.push('<?php echo $watercisternsdata_tooltip; ?>');
				commonwaterrodata_tooltip_Array.push('<?php echo $pre_waterrodata_tooltip; ?>');
				commonwaterrodata_tooltip_Array.push('<?php echo $waterrodata_tooltip; ?>');
				commonbudgetdata_tooltip_Array.push('<?php echo $pre_budgetdata_tooltip; ?>');
				commonbudgetdata_tooltip_Array.push('<?php echo $budgetdata_tooltip; ?>');
				commoncdddata_tooltip_Array.push('<?php echo $pre_cdddata_tooltip; ?>');
				commoncdddata_tooltip_Array.push('<?php echo $cdddata_tooltip; ?>');
				commonhdddata_tooltip_Array.push('<?php echo $pre_hdddata_tooltip; ?>');
				commonhdddata_tooltip_Array.push('<?php echo $hdddata_tooltip; ?>');
				commonoccupancydata_tooltip_Array.push('<?php echo $pre_occupancydata_tooltip; ?>');
				commonoccupancydata_tooltip_Array.push('<?php echo $occupancydata_tooltip; ?>');
				//for current year tooltip
		<?php
			}
		}
		?>

		<?php
		/*##########################Previous data##########################*/
		if ($pre_total_months > 0) {
			$pre_waterutilityAvgData = ($pre_total_sum_utility / $pre_total_months);
			$pre_cisternsAvgData = ($pre_total_sum_cisterns / $pre_total_months);
			$pre_roAvgData = ($pre_total_sum_ro / $pre_total_months);
			$pre_budgetAvgData = ($pre_total_sum_budget / $pre_total_months);
			$pre_cddAvgData = ($pre_total_sum_cdd / $pre_total_months);
			$pre_hddAvgData = ($pre_total_sum_hdd / $pre_total_months);
			$pre_occupancyAvgData = ($pre_total_sum_occupancy / $pre_total_months);
		} else {
			$pre_waterutilityAvgData = 0;
			$pre_cisternsAvgData = 0;
			$pre_roAvgData = 0;
			$pre_budgetAvgData = 0;
			$pre_cddAvgData = 0;
			$pre_hddAvgData = 0;
			$pre_occupancyAvgData = 0;
		}


		// Utility Average Variant
		$pre_deference_value = $pre_budgetAvgData - $pre_waterutilityAvgData;
		if ($pre_waterutilityAvgData > 0) {
			$pre_utility_percentage = (($pre_deference_value * 100) / $pre_waterutilityAvgData);
			$pre_utility_percentage = round($pre_utility_percentage, 2);
		} else {
			$pre_utility_percentage = 100;
		}

		if ($pre_utility_percentage > 0) {
			$pre_utility_pclass = 'nagetive';
			$pre_utility_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($pre_utility_percentage < 0) {
			$pre_utility_pclass = 'positive';
			$pre_utility_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$pre_utility_pclass = '';
			$pre_utility_parrow = '';
		}

		// cisterns Average Variant
		$pre_deference_value = $pre_budgetAvgData - $pre_cisternsAvgData;
		if ($pre_cisternsAvgData > 0) {
			$pre_cisterns_percentage = (($pre_deference_value * 100) / $pre_cisternsAvgData);
			$pre_cisterns_percentage = round($pre_cisterns_percentage, 2);
		} else {
			$pre_cisterns_percentage = 100;
		}

		if ($pre_cisterns_percentage > 0) {
			$pre_cisterns_pclass = 'nagetive';
			$pre_cisterns_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($pre_cisterns_percentage < 0) {
			$pre_cisterns_pclass = 'positive';
			$pre_cisterns_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$pre_cisterns_pclass = '';
			$pre_cisterns_parrow = '';
		}

		// RO Average Variant
		$pre_deference_value = $pre_budgetAvgData - $pre_roAvgData;
		if ($pre_roAvgData > 0) {
			$pre_ro_percentage = (($pre_deference_value * 100) / $pre_roAvgData);
			$pre_ro_percentage = round($pre_ro_percentage, 2);
		} else {
			$pre_ro_percentage = 100;
		}

		if ($pre_ro_percentage > 0) {
			$pre_ro_pclass = 'nagetive';
			$pre_ro_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($pre_ro_percentage < 0) {
			$pre_ro_pclass = 'positive';
			$pre_ro_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$pre_ro_pclass = '';
			$pre_ro_parrow = '';
		}

		// Remove - sign
		$pre_utility_percentage = abs($pre_utility_percentage);
		$pre_cisterns_percentage = abs($pre_cisterns_percentage);
		$pre_ro_percentage = abs($pre_ro_percentage);

		$pre_waterutilityAvgData = round($pre_waterutilityAvgData, 2);
		$pre_cisternsAvgData = round($pre_cisternsAvgData, 2);
		$pre_roAvgData = round($pre_roAvgData, 2);
		$pre_budgetAvgData = round($pre_budgetAvgData, 2);
		$pre_cddAvgData = round($pre_cddAvgData, 2);
		$pre_hddAvgData = round($pre_hddAvgData, 2);
		$pre_occupancyAvgData = round($pre_occupancyAvgData, 2);

		$pre_waterutilityAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $pre_waterutilityAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_utility_pclass . '\">' . $pre_utility_parrow . ' ' . $pre_utility_percentage . '%</span></div></div>';
		$pre_cisternsAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $pre_cisternsAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_cisterns_pclass . '\">' . $pre_cisterns_parrow . ' ' . $pre_cisterns_percentage . '%</span></div></div>';
		$pre_roAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $pre_roAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $pre_ro_pclass . '\">' . $pre_ro_parrow . ' ' . $pre_ro_percentage . '%</span></div></div>';
		$pre_budgetAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('budget') . ': ' . $pre_budgetAvgData . '</div></div>';
		$pre_cddAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('cdd') . ': ' . $pre_cddAvgData . '</div></div>';
		$pre_hddAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('hdd') . ': ' . $pre_hddAvgData . '</div></div>';
		$pre_occupancyAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('occupancy') . ': ' . $pre_occupancyAvgData . '</div></div>';
		/*##########################Previous data##########################*/

		/*##########################Current data##########################*/
		if ($total_months > 0) {
			$waterutilityAvgData = ($total_sum_utility / $total_months);
			$cisternsAvgData = ($total_sum_cisterns / $total_months);
			$roAvgData = ($total_sum_ro / $total_months);
			$budgetAvgData = ($total_sum_budget / $total_months);
			$cddAvgData = ($total_sum_cdd / $total_months);
			$hddAvgData = ($total_sum_hdd / $total_months);
			$occupancyAvgData = ($total_sum_occupancy / $total_months);
		} else {
			$waterutilityAvgData = 0;
			$cisternsAvgData = 0;
			$roAvgData = 0;
			$budgetAvgData = 0;
			$cddAvgData = 0;
			$hddAvgData = 0;
			$occupancyAvgData = 0;
		}

		// Utility Average Variant
		$deference_value = $budgetAvgData - $waterutilityAvgData;
		if ($waterutilityAvgData > 0) {
			$utility_percentage = (($deference_value * 100) / $waterutilityAvgData);
			$utility_percentage = round($utility_percentage, 2);
		} else {
			$utility_percentage = 100;
		}

		if ($utility_percentage > 0) {
			$utility_pclass = 'nagetive';
			$utility_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($utility_percentage < 0) {
			$utility_pclass = 'positive';
			$utility_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$utility_pclass = '';
			$utility_parrow = '';
		}

		// cisterns Average Variant
		$deference_value = $budgetAvgData - $cisternsAvgData;
		if ($cisternsAvgData > 0) {
			$cisterns_percentage = (($deference_value * 100) / $cisternsAvgData);
			$cisterns_percentage = round($cisterns_percentage, 2);
		} else {
			$cisterns_percentage = 100;
		}

		if ($cisterns_percentage > 0) {
			$cisterns_pclass = 'nagetive';
			$cisterns_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($cisterns_percentage < 0) {
			$cisterns_pclass = 'positive';
			$cisterns_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$cisterns_pclass = '';
			$cisterns_parrow = '';
		}

		// RO Average Variant
		$deference_value = $budgetAvgData - $roAvgData;
		if ($roAvgData > 0) {
			$ro_percentage = (($deference_value * 100) / $roAvgData);
			$ro_percentage = round($ro_percentage, 2);
		} else {
			$ro_percentage = 100;
		}

		if ($ro_percentage > 0) {
			$ro_pclass = 'nagetive';
			$ro_parrow = '<span class=\"fa fa-angle-double-down\"></span>';
		} else if ($ro_percentage < 0) {
			$ro_pclass = 'positive';
			$ro_parrow = '<span class=\"fa fa-angle-double-up\"></span>';
		} else {
			$ro_pclass = '';
			$ro_parrow = '';
		}

		// Remove - sign
		$utility_percentage = abs($utility_percentage);
		$cisterns_percentage = abs($cisterns_percentage);
		$ro_percentage = abs($ro_percentage);

		$waterutilityAvgData = round($waterutilityAvgData, 2);
		$cisternsAvgData = round($cisternsAvgData, 2);
		$roAvgData = round($roAvgData, 2);
		$budgetAvgData = round($budgetAvgData, 2);
		$cddAvgData = round($cddAvgData, 2);
		$hddAvgData = round($hddAvgData, 2);
		$occupancyAvgData = round($occupancyAvgData, 2);

		$waterutilityAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $waterutilityAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $utility_pclass . '\">' . $utility_parrow . ' ' . $utility_percentage . '%</span></div></div>';
		$cisternsAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $cisternsAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $cisterns_pclass . '\">' . $cisterns_parrow . ' ' . $cisterns_percentage . '%</span></div></div>';
		$roAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('actual') . ': ' . $roAvgData . '</div><div>' . lang('vs-budget') . ' : <span class=\"variant-' . $ro_pclass . '\">' . $ro_parrow . ' ' . $ro_percentage . '%</span></div></div>';
		$budgetAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('budget') . ': ' . $budgetAvgData . '</div></div>';
		$cddAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('cdd') . ': ' . $cddAvgData . '</div></div>';
		$hddAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('hdd') . ': ' . $hddAvgData . '</div></div>';
		$occupancyAvgData_tooltip = '<div class=\"gc-tooltip\"><div class=\"gc-tooltip-title\"><strong>Average</strong></div><div>' . lang('occupancy') . ': ' . $occupancyAvgData . '</div></div>';
		/*##########################Current data##########################*/
		?>
		utilityWaterBudgetReportArray.push(["<?php echo lang('average'); ?>", <?php echo $pre_waterutilityAvgData; ?>, <?php echo $pre_cisternsAvgData; ?>, <?php echo $pre_roAvgData; ?>, null, <?php if ($is_occupancy) {
																																																		echo $pre_occupancyAvgData;
																																																	} ?>]);
		utilityWaterBudgetReportArray.push(["<?php echo lang('average'); ?>", <?php echo $waterutilityAvgData; ?>, <?php echo $cisternsAvgData; ?>, <?php echo $roAvgData; ?>, <?php echo $budgetAvgData; ?>, <?php if ($is_occupancy) {
																																																					echo $occupancyAvgData;
																																																				} ?>]);
		//for previous tooltip average
		commonwaterutilitydata_tooltip_Array.push('<?php echo $pre_waterutilityAvgData_tooltip; ?>');
		commonwaterutilitydata_tooltip_Array.push('<?php echo $waterutilityAvgData_tooltip; ?>');
		commonwatercisternsdata_tooltip_Array.push('<?php echo $pre_cisternsAvgData_tooltip; ?>');
		commonwatercisternsdata_tooltip_Array.push('<?php echo $cisternsAvgData_tooltip; ?>');
		commonwaterrodata_tooltip_Array.push('<?php echo $pre_roAvgData_tooltip; ?>');
		commonwaterrodata_tooltip_Array.push('<?php echo $roAvgData_tooltip; ?>');
		commonbudgetdata_tooltip_Array.push('<?php echo $pre_budgetAvgData_tooltip; ?>');
		commonbudgetdata_tooltip_Array.push('<?php echo $budgetAvgData_tooltip; ?>');
		commoncdddata_tooltip_Array.push('<?php echo $pre_cddAvgData_tooltip; ?>');
		commoncdddata_tooltip_Array.push('<?php echo $cddAvgData_tooltip; ?>');
		commonhdddata_tooltip_Array.push('<?php echo $pre_hddAvgData_tooltip; ?>');
		commonhdddata_tooltip_Array.push('<?php echo $hddAvgData_tooltip; ?>');
		commonoccupancydata_tooltip_Array.push('<?php echo $pre_occupancyAvgData_tooltip; ?>');
		commonoccupancydata_tooltip_Array.push('<?php echo $occupancyAvgData_tooltip; ?>');
		//for current tooltip average

		var utilityChartWaterBudgetData = [];
		var xAxisutilityWaterBudgetChart = [];
		var utilityChartWaterBudgetTitle = utilityWaterBudgetReportArray[0];
		utilityChartWaterBudgetTitle = utilityChartWaterBudgetTitle.filter(value => value !== "Month");
		for (var i = 1; i < utilityWaterBudgetReportArray.length; i++) {
			xAxisutilityWaterBudgetChart.push(utilityWaterBudgetReportArray[i][0]);
		}
		$.each(utilityChartWaterBudgetTitle, function(i) {
			var key = utilityChartWaterBudgetTitle[i];
			utilityChartWaterBudgetData[key] = [];
			for (var j = 1; j < utilityWaterBudgetReportArray.length; j++) {
				utilityChartWaterBudgetData[key].push(utilityWaterBudgetReportArray[j][i + 1]);
			}
		});
		var utilityChartWaterBudgetSeries = [];
		Object.entries(utilityChartWaterBudgetData).forEach(([key, value]) => {
			if (!(key == 'Budget' || key == 'Occupancy')) {
				if (key == 'Utility') {
					utilityChartWaterBudgetSeries.push({
						pointWidth: 15,
						name: key,
						data: utilityChartWaterBudgetData[key],
						color: '#3366CC'
					}, );
				}
				if (key == 'Cisterns') {
					utilityChartWaterBudgetSeries.push({
						pointWidth: 15,
						name: key,
						data: utilityChartWaterBudgetData[key],
						color: '#DC3912'
					}, );
				}
				if (key == 'Irrigation') {
					utilityChartWaterBudgetSeries.push({
						pointWidth: 15,
						name: key,
						data: utilityChartWaterBudgetData[key],
						color: '#FF9900'
					}, );
				}
			} else {
				if (key == 'Budget') {
					utilityChartWaterBudgetSeries.push({
						type: 'spline',
						name: key,
						yAxis: 0,
						data: utilityChartWaterBudgetData[key],
						marker: {
							symbol: 'square',
							lineWidth: 2,
							lineColor: '#109618',
							fillColor: '#109618'
						},
						color: '#109618'
					}, );
				}
				if (key == 'Occupancy') {
					utilityChartWaterBudgetSeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: utilityChartWaterBudgetData[key],
						marker: {
							symbol: 'square',
							lineWidth: 2,
							lineColor: '#990099',
							fillColor: '#990099'
						},
						color: '#990099'
					}, );
				}
			}
		});
		Highcharts.chart('utility_report', {
			chart: {
				type: 'column'
			},
			title: {
				text: '<?php echo $view_title; ?>',
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
				categories: xAxisutilityWaterBudgetChart
			},
			<?php if ($is_occupancy) { ?>
				yAxis: [{
					allowDecimals: false,
					min: 0,
					title: {
						text: '<?php echo $x_axis_title_value; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				}, {
					min: 0,
					tickPositions: [0, 100, 200, 300, 400],
					title: {
						rotation: 270,
						margin: 30,
						text: '<?php echo lang("occupancy"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					opposite: true
				}],
			<?php } else { ?>
				yAxis: {
					allowDecimals: false,
					min: 0,
					title: {
						text: '<?php echo $x_axis_title_value; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				},
			<?php } ?>
			tooltip: {
				useHTML: true,
				enabled: true,
				formatter: function() {
					var point = this;
					commonwaterutilitydata = 0;
					commonwatercisternsdata = 0;
					commonwaterrodata = 0;
					commonbudgetdata = 0;
					commoncdddata = 0;
					commonhdddata = 0
					commonoccupancydata = 0;

					for (var i = 0; i <= this.point.x; i++) {
						commonwaterutilitydata = commonwaterutilitydata_tooltip_Array[i];
						commonwatercisternsdata = commonwatercisternsdata_tooltip_Array[i];
						commonwaterrodata = commonwaterrodata_tooltip_Array[i];
						commonbudgetdata = commonbudgetdata_tooltip_Array[i];
						commoncdddata = commoncdddata_tooltip_Array[i];
						commonhdddata = commonhdddata_tooltip_Array[i];
						commonoccupancydata = commonoccupancydata_tooltip_Array[i];
					}
					if (point.series.name == "Utility") {
						return "<b>" + commonwaterutilitydata + "</b>";
					}
					if (point.series.name == "Cisterns") {
						return "<b>" + commonwatercisternsdata + "</b>";
					}
					if (point.series.name == "Irrigation") {
						return "<b>" + commonwaterrodata + "</b>";
					}
					if (point.series.name == "Budget") {
						return "<b>" + commonbudgetdata + "</b>";
					}
					if (point.series.name == "Occupancy") {
						return "<b>" + commonoccupancydata + "</b>";
					}
				},
			},
			plotOptions: {
				column: {
					stacking: 'normal'
				},
				series: {
					connectNulls: true
				}
			},
			series: utilityChartWaterBudgetSeries
		});
	}
</script>
