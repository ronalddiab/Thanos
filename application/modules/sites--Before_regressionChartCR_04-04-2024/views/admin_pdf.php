<?php
$electricity_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'electricity');
$fuel_oil_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'fuel_oil');
$lpg_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'lpg');
$natural_gas_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'natural_gas');
$district_cooling_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'district_cooling');
$district_heating_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'district_heating');
?>
<html style="text-align: left;">
    <body width="100%">
	<table width="100%" cellpadding="0" cellspacing="0">
	    <tr>
		<td style="border-bottom: black dashed thin;"><h4>View Site</h4></td>
	    </tr>
	    <tr>
		<td style="line-height:5px;">&nbsp;</td>
	    </tr>
	    <tr>
		<td>
		    <table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			    <td width="20%"><?php echo lang('upload-hotel-logo'); ?></td>
			    <td width="80%">
			    <?php if (isset($site_logo) && $site_logo != '') { ?>
				<?php if(file_exists(BASE_PATH_CUSTOM."/assets/uploads/" . $site_logo)) { $is_site_logo_exists = 1;?>
				    <img src='<?php echo site_url() . "assets/uploads/" . $site_logo; ?> ' />
				<?php } else { ?>
				    <img src="<?php echo site_url(); ?><?php echo NOT_AVAILABLE_SITE_LOGO; ?>" width="66" height="46" />
				<?php } ?>
			    <?php } ?>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('change-hotel-theme'); ?></td>
			    <td width="80%">
				<table width="3%">
				    <tr>
					<td><div width="20" style="width:20px; height: 20px;background-color:<?php echo $site_color; ?>"></div></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('location'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_location_name; ?></td>
					<td width="33%"><?php echo lang('latitude').": ".$site_location_latitude; ?></td>
					<td width="33%"><?php echo lang('longitude').": ".$site_location_longitude; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('hotel'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $hotel_list[$hotel_id]; ?></td>
					<td width="33%"><?php echo lang('region'); ?></td>
					<td width="33%">
					    <?php
					    $region_list_defualt = array('' => 'Select Region');
					    $region_list = $region_list_defualt+$region_list;
					    echo $region_list[$region_id];
					    ?>
					</td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('country'); ?></td>
			    <td width="80%">
				<?php
				$country_list_defualt = array('' => '');
				$country_list = $country_list_defualt+$country_list;
				echo $country_list[$country_id];
				?>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('year-built'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_year_built; ?></td>
					<td width="33%"><?php echo lang('total-built-up-area').'('.getLocalUnitText($id).')'; ?></td>
					<td width="33%"><?php echo $site_builtup_area; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('cooled-built-up-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $cooled_builtup_area; ?></td>
					<td width="33%"><?php echo lang('total-meeting-area').'('.getLocalUnitText($id).')'; ?></td>
					<td width="33%"><?php echo $total_meeting_area; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('spa-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $total_spa_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('spa-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $total_spa_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('hotel-rooms-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $hotel_rooms_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('residential-common-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $residential_common_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('employee-living-quarters-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $employee_living_quarters_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('f-b-service').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $f_b_service; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('restaurant-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $restaurant_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('landscaped-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $landscaped_area; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('f-b-services-operated').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $f_b_services_operated; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('f-b-services-outsourced').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $f_b_services_outsourced; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('month-year-operation').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $month_year_operation; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('vehicle-electric').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $vehicle_electric; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('vehicle-petrol').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $vehicle_petrol; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('rental-program-residence').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $rental_program_residence; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('rental-private-residence').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $rental_private_residence; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('rental-program-residence-suites').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $rental_program_residence_suites; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('rental-private-residence-suites').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $rental_private_residence_suites; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr><td colspan="2" style="line-height:5px;">&nbsp;</td></tr>
			<tr>
			    <td width="20%"><?php echo lang('indoor-parking-area').'('.getLocalUnitText($id).')'; ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $indoor_parking_area; ?></td>
					<td width="33%"><?php echo lang('room-keys'); ?></td>
					<td width="33%"><?php echo $rooms_keys; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('outdoor-pools'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $outdoor_pools; ?></td>
					<td width="33%"><?php echo lang('indoor-pools'); ?></td>
					<td width="33%"><?php echo $indoor_pools; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('laundry'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php $laundry_type_list = array('1' => 'Outsourced', '0' => 'On Site'); echo $laundry_type_list[$laundry_type]; ?></td>
					<td width="33%"><?php echo $laundry_fuel_type; ?></td>
										<td width="33%"></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('substation-rating'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($substations)) {
				    foreach ($substations as $key => $substation) {
				    ?>
				    <tr>
					<td width="33%"><?php echo "Quantity: ".$substation['substation_quantity']; ?></td>
					<td width="33%"><?php echo "Power: ".$substation['substation_power']; ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php
				    }
				    }else{ ?>
				    <tr>
					<td width="33%"><?php echo "Quantity: ".set_value('substation[substation_quantity][]'); ?></td>
					<td width="33%"><?php echo "Power: ".set_value('substation[substation_power][]'); ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('onsite-generators'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($generators)) {
				    foreach ($generators as $key => $generator) {
				    ?>
				    <tr>
					<td width="33%"><?php echo lang('generators-name').": ".$generator['generator_name']; ?></td>
					<td width="33%"><?php echo lang('generators-quantity').": ".$generator['generator_quantity']; ?></td>
										<td width="33%"><?php echo lang('generators-power').": ".$generator['generator_power']; ?></td>
									</tr>
				    <?php
				    }
				    }else{ ?>
				    <tr>
					<td width="33%"><?php echo lang('generators-name').": ".set_value('generator[generator_name][]'); ?></td>
					<td width="33%"><?php echo lang('generators-quantity').": ".set_value('generator[generator_quantity][]'); ?></td>
					<td width="33%"><?php echo lang('generators-power').": ".set_value('generator[generator_power][]'); ?></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('chilled-water-system'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php if (isset($is_chilled_water_system) && $is_chilled_water_system == 1 && isset($chilled_water_system_type) && $chilled_water_system_type != '' && isset($chilled_water_system_total_rate) && $chilled_water_system_total_rate != '') { ?>
				    <tr>
					<td width="33%"><?php if (!isset($is_chilled_water_system) || $is_chilled_water_system == 0) { echo 'No'; } ?></td>
					<td width="33%"><?php echo $chilled_water_system_type; ?></td>
										<td width="33%"></td>
				    </tr>
				    <tr>
					<td width="33%"><?php echo lang('chilled-water-system-total-rate').": ".$chilled_water_system_total_rate; ?></td>
					<td width="33%">
										<?php
					$system2exists = false;
					if(!empty($chilled_water_system_total_rate2)){
					    $system2exists = true;
					}
					if($system2exists){ echo $chilled_water_system_type2; }
					?></td>
										<td width="33%"></td>
				    </tr>
				    <tr>
					<td width="33%"><?php echo $chilled_water_system_total_rate2; ?></td>
					<td width="33%"></td>
										<td width="33%"></td>
				    </tr>
				    <?php }else{ ?>
				    <tr>
					<td width="33%"><?php if (!isset($is_chilled_water_system) || $is_chilled_water_system == 0) { echo 'No'; } ?></td>
					<td width="33%"><?php echo $chilled_water_system_type; ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php } ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('split-dx-units'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
			    <?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1 && isset($total_split_dx_unit) && $total_split_dx_unit != '' && isset($total_rate_split_dx_unit) && $total_rate_split_dx_unit != '') { ?>
				    <tr>
					<td width="33%"><?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 0) { echo 'No'; } ?></td>
					<td width="33%"><?php echo lang('total-rt').": ".$total_split_dx_unit; ?></td>
					<td width="33%"></td>
				    </tr>
				    <tr>
					<td colspan="3"><?php echo lang('total-rt').": ".$total_rate_split_dx_unit; ?></td>
				    </tr>
				    <?php
				    }else{ ?>
				    <tr>
					<td width="33%"><?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 0) { echo 'No'; } ?></td>
					<td width="33%"></td>
					<td width="33%"></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('vrv'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				<?php if (isset($is_vrv) && $is_vrv == 1 && isset($total_vrv) && $total_vrv != '') { ?>
				    <tr>
					<td width="33%"><?php if (!isset($is_vrv) || $is_vrv == 0) { echo 'No'; } ?></td>
					<td width="33%"><?php echo lang('total-rt').": ".$total_vrv_unit; ?></td>
					<td width="33%"></td>
				    </tr>
				    <tr>
					<td colspan="3"><?php echo lang('total-rt').": ".$total_vrv; ?></td>
				    </tr>
				    <?php
				    }else{ ?>
				    <tr>
					<td width="33%"><?php if (!isset($is_vrv) || $is_vrv == 0) { echo 'No'; } ?></td>
					<td width="33%"></td>
					<td width="33%"></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('hot-water-boiler'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($hot_water_boilers)) {
				    foreach ($hot_water_boilers as $key => $hot_water_boiler) {
				    ?>
				    <tr>
					<td width="33%"><?php echo lang('hot-water-bolier-quantity').": ".$hot_water_boiler['hot_water_boiler_quantity']; ?></td>
					<td width="33%"><?php echo lang('hot-water-boiler-power').": ".$hot_water_boiler['hot_water_boiler_power']; ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php
				    }
				    }else{ ?>
				    <tr>
					<td width="33%"><?php echo lang('hot-water-bolier-quantity').": ".set_value('hot_water_boiler[hot_water_boiler_quantity][]'); ?></td>
					<td width="67%"><?php echo lang('hot-water-boiler-power').": ".set_value('hot_water_boiler[hot_water_boiler_power][]'); ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('calorifiers-label'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $calorifiers_unit; ?></td>
					<td width="33%"><?php echo lang('calorifiers-volume'); ?></td>
					<td width="33%"><?php echo $calorifiers_volume; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('hot-water-boiler'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($steam_boilers)) {
				    foreach ($steam_boilers as $key => $steam_boiler) {
				    ?>
				    <tr>
					<td width="33%"><?php echo lang('steam-bolier-quantity').": ".$steam_boiler['steam_boiler_quantity']; ?></td>
					<td width="33%"><?php echo lang('steam-boiler-power').": ".$steam_boiler['steam_boiler_power']; ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php
				    }
				    }else{ ?>
				    <tr>
					<td width="33%"><?php echo lang('steam-bolier-quantity').": ".set_value('steam_boiler[steam_boiler_quantity][]'); ?></td>
					<td width="33%"><?php echo lang('steam-boiler-power').": ".set_value('steam_boiler[steam_boiler_power][]'); ?></td>
										<td width="33%"></td>
				    </tr>
				    <?php }?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('electrical-hw'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo lang('elcetrical-hw-total').": ".$elcetrical_hw_total; ?></td>
					<td width="33%"><?php echo lang('elcetrical-hw-total-capacity').": ".$elcetrical_hw_total_capacity; ?></td>
					<td width="33%"><?php echo lang('elcetrical-hw-total-power').": ".$elcetrical_hw_total_power; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('ro-plant'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td><?php if (!isset($is_ro_plant) || $is_ro_plant == 0) { echo 'No'; } ?></td>
				    </tr>
				    <?php if (isset($is_ro_plant) && $is_ro_plant == 1 && isset($ro_plant_capacity) && $ro_plant_capacity != '') { ?>
					<tr><td><?php echo lang('ro-capacity').": ".$ro_plant_capacity; ?></td></tr>
				    <?php } ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('renewable-energy'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				<?php do { ?>
				    <tr>
										<?php if (isset($is_renewable_energy) && $is_renewable_energy == 1) { ?>
					<td width="33%"><?php echo lang('renewable-energy-type').": ".$renewable_energys[0]['renewable_energy_type']; ?></td>
					<td width="33%"><?php echo lang('renewable-energy-quantity').": ".$renewable_energys[0]['renewable_energy_quantity']; ?></td>
					<td width="33%"><?php echo lang('renewable-energy-capacirty').": ".$renewable_energys[0]['renewable_energy_capacity']; ?></td>
										<?php }
										if (!isset($is_renewable_energy) || $is_renewable_energy == 0) { ?>
										<td width="33%"><?php if (!isset($is_renewable_energy) || $is_renewable_energy == 0) { echo 'No'; } ?></td>
										<td width="33%"></td>
										<td width="33%"></td>
										<?php } ?>
				    </tr>
				<?php } while (1 == 2) ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('stp'); ?></td>
			    <td width="80%">
				<table>
				    <?php if (isset($is_stp) && $is_stp == 1) { ?>
					<tr><td width="70%"><?php echo lang('stp-capacity').": ".$stp_capacity; ?></td></tr>
				    <?php }if (!isset($is_stp) || $is_stp == 0) { ?>
										<tr><td><?php echo 'No'; ?></td></tr>
									<?php } ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
		    </table>
		</td>
	    </tr>
			<tr>
				<td style="line-height:5px;">&nbsp;</td>
			</tr>
	    <tr>
		<td><h4><strong><?php echo lang('ghg_emissions_factor'); ?></strong></h4></td>
	    </tr>
	    <tr>
		<td>
		    <table width="100%" cellpadding="0" cellspacing="0" style="font-size:9;">
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('electricity-emission-factor'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $electricity_kgco2.": ".$electricity_emission_factor; ?></td>
					<td width="33%"><?php echo lang('fuel-emission-factor'); ?></td>
					<td width="33%"><?php echo $fuel_oil_kgco2.": ".$fuel_emission_factor; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="20%"><?php echo lang('lpg-emission-factor'); ?></td>
			    <td width="80%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $lpg_kgco2.": ".$lpg_emission_factor; ?></td>
					<td width="33%"><?php echo lang('natural-gas-emission-factor'); ?></td>
					<td width="33%"><?php echo $natural_gas_kgco2.": ".$natural_gas_emission_factor; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
						<tr>
			    <td width="20%"><?php echo lang('status'); ?></td>
			    <td width="80%">
								<?php $statuslist = array('1' => 'Active', '0' => 'Inactive'); ?>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $statuslist[$status]; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
		    </table>
		</td>
	    </tr>
			<tr>
		<td><img src="<?php echo $columnChartImg; ?>" /></td>
	    </tr>
			<tr>
		<td><img src="<?php echo $pieChartImg; ?>" /></td>
	    </tr>
			<tr>
		<td><img src="<?php echo $pieChartNewImg; ?>" /></td>
	    </tr>
			<tr>
		<td><img src="<?php echo $pieChartNew2Img; ?>" /></td>
	    </tr>
			<tr>
		<td><img src="<?php echo $pieChartNew3Img; ?>" /></td>
	    </tr>
	</table>
    </body>
</html>