<?php
$electricity_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');
$fuel_oil_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');//GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'fuel_oil');
$lpg_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');//GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'lpg');
$natural_gas_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');//GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'natural_gas');
$district_cooling_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');//GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'district_cooling');
$district_heating_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'electricity');//GetSiteUtilityUnitNameKgCO2e($site_detail['id'],'district_heating');

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

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
?>
<html style="text-align: left;">
    <body>
	<br>
	<br>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	    <tr>
		<td>
		    <table width="100%" cellpadding="0" cellspacing="0">
			<tr colspan="2" style="font-size:15px;color:blue;">
			    <td><strong><?php echo $pdf_report_title; ?></strong></td>
			</tr>
			<tr colspan="2">
			    <td>&nbsp;</td>
			</tr>
			<tr colspan="2" style="font-size:14px;">
			    <td><strong>Site Info :</strong></td>
			</tr>
			<tr colspan="2">
			    <td>&nbsp;</td>
			</tr>
			<tr>
			    <td width="30%"><strong><?php echo lang('location'); ?></strong></td>
			    <td width="70%">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['site_location_name']; ?></td>
					<td width="33%"><strong><?php echo lang('latitude') ; ?></strong><?php echo ": " . $site_detail['site_location_latitude']; ?></td>
					<td width="33%"><strong><?php echo lang('longitude') ; ?></strong><?php echo ": " . $site_detail['site_location_longitude']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('hotel'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $hotel_list[$site_detail['hotel_id']]; ?></td>
					<td width="33%"><strong><?php echo lang('region'); ?></strong></td>
					<td width="33%">
					    <?php
					    $region_list_defualt = array('' => 'Select Region');
					    //$region_list = $region_list_defualt + $region_list;
					    echo $region_list[$site_detail['region_id']];
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
			    <td><strong><?php echo lang('country'); ?></strong></td>
			    <td>
				<?php
				$country_list_defualt = array('' => '');
				//$country_list = $country_list_defualt + $country_list;
				echo $country_list[$site_detail['country_id']];
				?>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('year-built'); ?></strong></td>
			    <td >
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['site_year_built']; ?></td>
					<td width="33%"><strong><?php echo lang('total-built-up-area'); ?></strong></td>
					<td width="33%"><?php echo $site_detail['site_builtup_area']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('cooled-built-up-area'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['cooled_builtup_area']; ?></td>
					<td width="33%"><strong><?php echo lang('total-meeting-area'); ?></strong></td>
					<td width="33%"><?php echo $site_detail['total_meeting_area']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('spa-area'); ?></strong></td>
			    <td >
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['total_spa_area']; ?></td>
					<td width="33%"><?php echo ''; ?></td>
					<td width="33%"><?php echo ''; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr><td colspan="2" style="line-height:5px;">&nbsp;</td></tr>
			<tr>
			    <td><strong><?php echo lang('indoor-parking-area'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['indoor_parking_area']; ?></td>
					<td width="33%"><strong><?php echo lang('room-keys'); ?></strong></td>
					<td width="33%"><?php echo $site_detail['rooms_keys']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('outdoor-pools'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['outdoor_pools']; ?></td>
					<td width="33%"><strong><?php echo lang('indoor-pools'); ?></strong></td>
					<td width="33%"><?php echo $site_detail['indoor_pools']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('laundry'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php $laundry_type_list = array('1' => 'Outsourced', '0' => 'On Site'); echo $laundry_type_list[$site_detail['laundry_type']]; ?></td>
					<td width="33%">Type: <?php echo $site_detail['laundry_fuel_type']; ?></td>
					<td width="33%"></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('substation-rating'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($substations)) {
					foreach ($substations as $key => $substation) {
					    ?>
					    <tr>
						<td width="33%"><?php echo lang("quantity") . ": " . $substation['substation_quantity']; ?></td>
						<td width="33%"><?php echo lang('power-kva') . ": " . $substation['substation_power']; ?></td>
						<td width="33%"></td>
					    </tr>
					    <?php
					}
				    } else {
					?>
					<tr>
					    <td width="33%"><?php echo lang("quantity") . ": " . set_value('substation[substation_quantity][]'); ?></td>
					    <td width="33%"><?php echo lang('power-kva') . ": " . set_value('substation[substation_power][]'); ?></td>
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
			    <td><strong><?php echo lang('onsite-generators'); ?></strong></td>
			    <td >
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($generators)) {
					foreach ($generators as $key => $generator) {
					    ?>
					    <tr>
						<td width="33%"><?php echo lang('generators-name') . ": " . $generator['generator_name']; ?></td>
						<td width="33%"><?php echo lang('generators-quantity') . ": " . $generator['generator_quantity']; ?></td>
						<td width="33%"><?php echo lang('generators-power') . ": " . $generator['generator_power']; ?></td>
					    </tr>
					    <?php
					}
				    } else {
					?>
					<tr>
					    <td width="33%"><?php echo lang('generators-name') . ": " . set_value('generator[generator_name][]'); ?></td>
					    <td width="33%"><?php echo lang('generators-quantity') . ": " . set_value('generator[generator_quantity][]'); ?></td>
					    <td width="33%"><?php echo lang('generators-power') . ": " . set_value('generator[generator_power][]'); ?></td>
					</tr>
<?php } ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td valign="top"><strong><?php echo lang('chilled-water-system'); ?></strong></td>
			    <td valign="top">
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<?php if(!isset($site_detail['is_chilled_water_system']) || $site_detail['is_chilled_water_system'] == 0) { ?>
					    <td width="33%">No</td>
					    <td width="33%"></td>
					    <td width="33%"></td>
					<?php } else { ?>
					    <td width="33%"><?php echo $site_detail['chilled_water_system_type']; ?></td>
					    <td width="33%"><?php echo lang('chilled-water-system-total-rate') . ": " . $site_detail['chilled_water_system_total_rate']; ?></td>
					    <td width="33%"></td>
					<?php } ?>
				    </tr>
				    <tr>
					<td width="33%"><?php
					    if (!empty($site_detail['chilled_water_system_total_rate2'])) {
						echo $site_detail['chilled_water_system_type2'];
					    }
					    ?>
					</td>
					<td width="33%"><?php echo $site_detail['chilled_water_system_total_rate2']; ?></td>
					<td width="33%"></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('split-dx-units'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<?php if (isset($site_detail['is_split_dx_unit']) && $site_detail['is_split_dx_unit'] == 0) { ?>
					<td width="33%">No</td>
					<td width="33%"></td>
					<td width="33%"></td>
					<?php } else { ?>
					<td width="33%"><?php echo lang('total-rt') . ": " . $site_detail['total_split_dx_unit']; ?></td>
					<td width="33%"><?php echo "Total Number: " . $site_detail['total_rate_split_dx_unit']; ?></td>
					<td width="33%"></td>
					<?php } ?>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('vrv'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
					    <?php if (!isset($site_detail['is_vrv']) || $site_detail['is_vrv'] == 0) { ?>
						<td width="33%">No</td>
						<td width="33%"></td>
						<td width="33%"></td>
					    <?php } else { ?>
						<td width="33%"><?php echo lang('total-rt') . ": " . $site_detail['total_vrv_unit']; ?></td>
						<td width="33%"><?php echo 'Total Number' . ": " . $site_detail['total_vrv']; ?></td>
						<td width="33%"></td>
					    <?php } ?>
					</tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('hot-water-boiler'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <?php
				    if (!empty($hot_water_boilers)) {
					foreach ($hot_water_boilers as $key => $hot_water_boiler) {
					    ?>
					    <tr>
						<td width="33%"><?php echo lang('hot-water-bolier-quantity') . ": " . $hot_water_boiler['hot_water_boiler_quantity']; ?></td>
						<td width="33%"><?php echo lang('hot-water-boiler-power') . ": " . $hot_water_boiler['hot_water_boiler_power']; ?></td>
						<td width="33%"></td>
					    </tr>
	<?php
    }                                   } else { ?>
					<tr>
					    <td width="33%"><?php echo lang('hot-water-bolier-quantity') . ": " . set_value('hot_water_boiler[hot_water_boiler_quantity][]'); ?></td>
					    <td width="67%"><?php echo lang('hot-water-boiler-power') . ": " . set_value('hot_water_boiler[hot_water_boiler_power][]'); ?></td>
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
			    <td><strong><?php echo lang('calorifiers-label'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo $site_detail['calorifiers_unit']; ?></td>
					<td width="33%"><?php echo lang('calorifiers-volume'); ?>: <?php echo $site_detail['calorifiers_volume']; ?></td>
					<td width="33%"></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('steam-boiler'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
<?php
if (!empty($steam_boilers)) {
    foreach ($steam_boilers as $key => $steam_boiler) {
	?>
					    <tr>
						<td width="33%"><?php echo lang('steam-bolier-quantity') . ": " . $steam_boiler['steam_boiler_quantity']; ?></td>
						<td width="33%"><?php echo lang('steam-boiler-power') . ": " . $steam_boiler['steam_boiler_power']; ?></td>
						<td width="33%"></td>
					    </tr>
	<?php
    }
} else {
    ?>
					<tr>
					    <td width="33%"><?php echo lang('steam-bolier-quantity') . ": " . set_value('steam_boiler[steam_boiler_quantity][]'); ?></td>
					    <td width="33%"><?php echo lang('steam-boiler-power') . ": " . set_value('steam_boiler[steam_boiler_power][]'); ?></td>
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
			    <td><strong><?php echo lang('electrical-hw'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td width="33%"><?php echo lang('elcetrical-hw-total') . ": " . $site_detail['elcetrical_hw_total']; ?></td>
					<td width="33%"><?php echo lang('elcetrical-hw-total-capacity') . ": " . $site_detail['elcetrical_hw_total_capacity']; ?></td>
					<td width="33%"><?php echo lang('elcetrical-hw-total-power') . ": " . $site_detail['elcetrical_hw_total_power']; ?></td>
				    </tr>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('ro-plant'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
				    <tr>
					<td><?php if (!isset($site_detail['is_ro_plant']) || $site_detail['is_ro_plant'] == 0) {
					echo 'No';
				    } ?></td>
				    </tr>
<?php if (isset($site_detail['is_ro_plant']) && $site_detail['is_ro_plant'] == 1 && isset($site_detail['ro_plant_capacity']) && $site_detail['ro_plant_capacity'] != '') { ?>
					<tr><td><?php echo lang('ro-capacity') . ": " . $site_detail['ro_plant_capacity']; ?></td></tr>
					<?php } ?>
				</table>
			    </td>
			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td><strong><?php echo lang('renewable-energy'); ?></strong></td>
			    <td>
				<table width="100%" cellpadding="0" cellspacing="0">
<?php do { ?>
					<tr>
					<?php if (isset($site_detail['is_renewable_energy']) && $site_detail['is_renewable_energy'] == 1) { ?>
						<td width="33%"><?php echo lang('renewable-energy-type') . ": " . $renewable_energys[0]['renewable_energy_type']; ?></td>
						<td width="33%"><?php echo lang('renewable-energy-quantity') . ": " . $renewable_energys[0]['renewable_energy_quantity']; ?></td>
						<td width="33%"><?php echo lang('renewable-energy-capacirty') . ": " . $renewable_energys[0]['renewable_energy_capacity']; ?></td>
					<?php }
					if (!isset($site_detail['is_renewable_energy']) || $site_detail['is_renewable_energy'] == 0) {
					    ?>
						<td width="33%"><?php if (!isset($site_detail['is_renewable_energy']) || $site_detail['is_renewable_energy'] == 0) {
					echo 'No';
				    } ?></td>
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
			    <td><strong><?php echo lang('stp'); ?></strong></td>
			    <td>
				<table>
<?php if (isset($site_detail['is_stp']) && $site_detail['is_stp'] == 1) { ?>
					<tr><td width="70%"><?php echo lang('stp-capacity') . ": " . $site_detail['stp_capacity']; ?></td></tr>
<?php }if (!isset($site_detail['is_stp']) || $site_detail['is_stp'] == 0) { ?>
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
		<td><hr></td>
	    </tr>
	    <tr>
		<td style="line-height:5px;">&nbsp;</td>
	    </tr>
	    <tr>
		<td><h4><strong><?php echo lang('ghg_emissions_factor'); ?></strong></h4></td>
	    </tr>
	    <tr>
		<td>
		    <table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="25%"><?php echo lang('electricity-emission-factor'); ?></td>

			    <td width="25%"><?php echo $electricity_kgco2 . ": " . $site_detail['electricity_emission_factor']; ?></td>

			    <td width="25%"><?php echo lang('fuel-emission-factor'); ?></td>

			    <td width="25%"><?php echo $fuel_oil_kgco2 . ": " . $site_detail['fuel_emission_factor']; ?></td>

			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			<tr>
			    <td width="25%"><?php echo lang('lpg-emission-factor'); ?></td>

			    <td width="25%"><?php echo $lpg_kgco2. ": " . $site_detail['lpg_emission_factor']; ?></td>

			    <td width="25%"><?php echo lang('natural-gas-emission-factor'); ?></td>

			    <td width="25%"><?php echo $natural_gas_kgco2 . ": " . $site_detail['natural_gas_emission_factor']; ?></td>

			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
			 <tr>
			    <td width="25%"><?php echo lang('district-cooling-emission-factor'); ?></td>

			    <td width="25%"><?php echo $district_cooling_kgco2 . ": " . $site_detail['district_cooling_emission_factor']; ?></td>

			    <td width="25%"><?php echo lang('district-heating-emission-factor'); ?></td>

			    <td width="25%"><?php echo $district_heating_kgco2 . ": " . $site_detail['district_heating_emission_factor']; ?></td>

			</tr>
			<tr>
			    <td colspan="2" style="line-height:5px;">&nbsp;</td>
			</tr>
		    </table>
		</td>
	    </tr>
	</table>
    </body>
</html>