<?php echo add_css(array('spectrum')); ?>
<?php echo add_js(array('spectrum')); ?>
<?php
$electricity_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'electricity');
$fuel_oil_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'fuel_oil');
$lpg_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'lpg');
$natural_gas_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'natural_gas');
$district_cooling_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'district_cooling');
$district_heating_kgco2 = GetSiteUtilityUnitNameKgCO2e($site_id,'district_heating');
?>
<article class="card">
    <div class="article-header">
	<?php echo lang('view-site'); ?>
    </div>
    <div class="card-wrap">
	<ul class="form-outer-block">
	    <li>
		<?php echo form_label(lang('upload-hotel-logo'), 'upload_hotel_logo', ["class" => "main-label"]); ?>
		<div class="row">

		    <!-- <div class="form-col-2">
			<img src='<?php echo site_url() . "/assets/uploads/" . $site_logo; ?> ' height="40px;" width="40px;">
		    </div> -->

		    <?php if (isset($site_logo) && $site_logo != '') { ?>
			<?php if(file_exists(BASE_PATH_CUSTOM."/assets/uploads/" . $site_logo)) { $is_site_logo_exists = 1;?>
			    <div class="form-col-2">
				<img src='<?php echo site_url() . "assets/uploads/" . $site_logo; ?> '>
			    </div>
			<?php } else { ?>
			    <div class="form-col-2">
				<img class="siteImage" src='<?php echo site_url() . NOT_AVAILABLE_SITE_LOGO; ?> '>
			    </div>
			<?php } ?>
		    <?php } ?>
		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('change-hotel-theme'), 'change_hotel_theme', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-1">
			<?php
			$site_color_data = array(
			    'type' => 'hidden',
			    'name' => 'site_color',
			    'id' => 'site_color',
			    'disabled' => 'disabled',
			    'value' => set_value('site_color', ((isset($site_color)) ? htmlspecialchars_decode($site_color) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($site_color_data); ?>
			<div id="selectedcolorblock" style="border:1px dotted;width: 100%; float: left; display: inline-block; height: 40px;<?php echo (!empty($site_color))?'background-color:'.$site_color.';':'' ?>"></div>
			<label class="input-label validation_error"><?php echo form_error('site_color'); ?></label>
		    </div>

		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('location'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$site_location_name_data = array(
			    'name' => 'site_location_name',
			    'id' => 'site_location_name',
			    'value' => set_value('site_location_name', ((isset($site_location_name)) ? htmlspecialchars_decode($site_location_name) : '')),
			    'class' => 'input-control',
			    'maxlength' => 50,
			    'disabled' => 'disabled'
			);
			?>
			<?php echo form_input($site_location_name_data); ?><span class="validation_error"><?php echo form_error('site_location_name'); ?>
			<?php echo form_label(lang('enter-location'), 'location', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<?php
			$site_location_latitude_data = array(
			    'name' => 'site_location_latitude',
			    'id' => 'site_location_latitude',
			    'value' => set_value('site_location_latitude', ((isset($site_location_latitude)) ? html_entity_decode($site_location_latitude) : '')),
			    'class' => 'input-control',
			    'maxlength' => 10,
			    'disabled' => 'disabled'
			);
			?>
			<?php echo form_input($site_location_latitude_data); ?><span class="validation_error"><?php echo form_error('site_location_latitude'); ?></span>
			<?php echo form_label(lang('latitude'), 'latitude', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<?php
			$site_location_longitude_data = array(
			    'name' => 'site_location_longitude',
			    'id' => 'site_location_longitude',
			    'value' => set_value('site_location_longitude', ((isset($site_location_longitude)) ? html_entity_decode($site_location_longitude) : '')),
			    'class' => 'input-control',
			    'disabled' => 'disabled',
			    'maxlength' => 10
			);
			?>
			<?php echo form_input($site_location_longitude_data); ?><span class="validation_error"><?php echo form_error('site_location_longitude'); ?></span>
			<!-- <label class="input-label">Longitude</label> -->
			<?php echo form_label(lang('longitude'), 'longitude', ["class" => "input-label"]); ?>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('hotel'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    echo form_dropdown('hotel_id', $hotel_list, $hotel_id, 'disabled="disabled" data-type = "custom-dropdown" id="hotel_id"');
			    ?><span class="validation_error hotel-error"><?php echo form_error('hotel_id'); ?></span>
			</div>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('region'); ?> </label>
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $region_list_defualt = array('' => 'Select Region');
			    $region_list = $region_list_defualt+$region_list;
			    echo form_dropdown('region_id', $region_list, $region_id, 'disabled="disabled" data-type = "custom-dropdown" id="region_id"');
			    ?><span class="validation_error region-error"><?php echo form_error('region_id'); ?></span>
			</div>
		    </div>
		</div>
	    </li>

	    <li>
		<label class="main-label"><?php echo lang('country'); ?></label>
		<div class="row">
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $country_list_defualt = array('' => '');
			    $country_list = $country_list_defualt+$country_list;
			    echo form_dropdown('country_id', $country_list, $country_id, 'disabled="disabled" data-type = "custom-dropdown" id="country_id"');
			    ?>
			</div>
		    </div>
		</div>
	    </li>

	    <li>
		<label class="main-label"><?php echo lang('year-built'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$site_year_built_data = array(
			    'name' => 'site_year_built',
			    'id' => 'site_year_built',
			    'value' => set_value('site_year_built', ((isset($site_year_built)) ? htmlspecialchars_decode($site_year_built) : '')),
			    'class' => 'input-control',
			    'disabled' => 'disabled',
			    'maxlength' => 4
			);
			?>
			<?php echo form_input($site_year_built_data); ?><span class="validation_error"><?php echo form_error('site_year_built'); ?></span>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('total-built-up-area').'('.getLocalUnitText($id).')'; ?> </label>
		    <div class="form-col-3">
			<?php
			$site_builtup_area = array(
			    'name' => 'site_builtup_area',
			    'id' => 'site_builtup_area',
			    'value' => set_value('site_builtup_area', ((isset($site_builtup_area)) ? htmlspecialchars_decode($site_builtup_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($site_builtup_area); ?><span class="validation_error"><?php echo form_error('site_builtup_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('cooled-built-up-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$cooled_builtup_area = array(
			    'name' => 'cooled_builtup_area',
			    'id' => 'cooled_builtup_area',
			    'value' => set_value('cooled_builtup_area', ((isset($cooled_builtup_area)) ? htmlspecialchars_decode($cooled_builtup_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($cooled_builtup_area); ?><span class="validation_error"><?php echo form_error('cooled_builtup_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('total-meeting-area').'('.getLocalUnitText($id).')'; ?> </label>
		    <div class="form-col-3">
			<?php
			$total_meeting_area = array(
			    'name' => 'total_meeting_area',
			    'id' => 'total_meeting_area',
			    'value' => set_value('total_meeting_area', ((isset($total_meeting_area)) ? htmlspecialchars_decode($total_meeting_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($total_meeting_area); ?><span class="validation_error"><?php echo form_error('total_meeting_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('spa-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$total_spa_area = array(
			    'name' => 'total_spa_area',
			    'id' => 'total_spa_area',
			    'value' => set_value('total_spa_area', ((isset($total_spa_area)) ? htmlspecialchars_decode($total_spa_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($total_spa_area); ?><span class="validation_error"><?php echo form_error('total_spa_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('hotel-rooms-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$hotel_rooms_area = array(
			    'name' => 'hotel_rooms_area',
			    'id' => 'hotel_rooms_area',
			    'value' => set_value('hotel_rooms_area', ((isset($hotel_rooms_area)) ? htmlspecialchars_decode($hotel_rooms_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($hotel_rooms_area); ?><span class="validation_error"><?php echo form_error('hotel_rooms_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('residential-common-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$residential_common_area = array(
			    'name' => 'residential_common_area',
			    'id' => 'residential_common_area',
			    'value' => set_value('residential_common_area', ((isset($residential_common_area)) ? htmlspecialchars_decode($residential_common_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($residential_common_area); ?><span class="validation_error"><?php echo form_error('residential_common_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('employee-living-quarters-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$employee_living_quarters_area = array(
			    'name' => 'employee_living_quarters_area',
			    'id' => 'employee_living_quarters_area',
			    'value' => set_value('employee_living_quarters_area', ((isset($employee_living_quarters_area)) ? htmlspecialchars_decode($employee_living_quarters_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($employee_living_quarters_area); ?><span class="validation_error"><?php echo form_error('employee_living_quarters_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('f-b-service').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$f_b_service = array(
			    'name' => 'f_b_service',
			    'id' => 'f_b_service',
			    'value' => set_value('f_b_service', ((isset($f_b_service)) ? htmlspecialchars_decode($f_b_service) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($f_b_service); ?><span class="validation_error"><?php echo form_error('f_b_service'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('restaurant-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$restaurant_area = array(
			    'name' => 'restaurant_area',
			    'id' => 'restaurant_area',
			    'value' => set_value('restaurant_area', ((isset($restaurant_area)) ? htmlspecialchars_decode($restaurant_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($restaurant_area); ?><span class="validation_error"><?php echo form_error('restaurant_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('landscaped-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$landscaped_area = array(
			    'name' => 'landscaped_area',
			    'id' => 'landscaped_area',
			    'value' => set_value('landscaped_area', ((isset($landscaped_area)) ? htmlspecialchars_decode($landscaped_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($landscaped_area); ?><span class="validation_error"><?php echo form_error('landscaped_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('f-b-services-operated').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$f_b_services_operated = array(
			    'name' => 'f_b_services_operated',
			    'id' => 'f_b_services_operated',
			    'value' => set_value('f_b_services_operated', ((isset($f_b_services_operated)) ? htmlspecialchars_decode($f_b_services_operated) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($f_b_services_operated); ?><span class="validation_error"><?php echo form_error('f_b_services_operated'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('f-b-services-outsourced').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$f_b_services_outsourced = array(
			    'name' => 'f_b_services_outsourced',
			    'id' => 'f_b_services_outsourced',
			    'value' => set_value('f_b_services_outsourced', ((isset($f_b_services_outsourced)) ? htmlspecialchars_decode($f_b_services_outsourced) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($f_b_services_outsourced); ?><span class="validation_error"><?php echo form_error('f_b_services_outsourced'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('month-year-operation').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$month_year_operation = array(
			    'name' => 'month_year_operation',
			    'id' => 'month_year_operation',
			    'value' => set_value('month_year_operation', ((isset($month_year_operation)) ? htmlspecialchars_decode($month_year_operation) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($month_year_operation); ?><span class="validation_error"><?php echo form_error('month_year_operation'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('vehicle-electric').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$vehicle_electric = array(
			    'name' => 'vehicle_electric',
			    'id' => 'vehicle_electric',
			    'value' => set_value('vehicle_electric', ((isset($vehicle_electric)) ? htmlspecialchars_decode($vehicle_electric) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($vehicle_electric); ?><span class="validation_error"><?php echo form_error('vehicle_electric'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('vehicle-petrol').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$vehicle_petrol = array(
			    'name' => 'vehicle_petrol',
			    'id' => 'vehicle_petrol',
			    'value' => set_value('vehicle_petrol', ((isset($vehicle_petrol)) ? htmlspecialchars_decode($vehicle_petrol) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($vehicle_petrol); ?><span class="validation_error"><?php echo form_error('vehicle_petrol'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-program-residence').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_program_residence = array(
			    'name' => 'rental_program_residence',
			    'id' => 'rental_program_residence',
			    'value' => set_value('rental_program_residence', ((isset($rental_program_residence)) ? htmlspecialchars_decode($rental_program_residence) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($rental_program_residence); ?><span class="validation_error"><?php echo form_error('rental_program_residence'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-private-residence').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_private_residence = array(
			    'name' => 'rental_private_residence',
			    'id' => 'rental_private_residence',
			    'value' => set_value('rental_private_residence', ((isset($rental_private_residence)) ? htmlspecialchars_decode($rental_private_residence) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($rental_private_residence); ?><span class="validation_error"><?php echo form_error('rental_private_residence'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-program-residence-suites').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_program_residence_suites = array(
			    'name' => 'rental_program_residence_suites',
			    'id' => 'rental_program_residence_suites',
			    'value' => set_value('rental_program_residence_suites', ((isset($rental_program_residence_suites)) ? htmlspecialchars_decode($rental_program_residence_suites) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($rental_program_residence_suites); ?><span class="validation_error"><?php echo form_error('rental_program_residence_suites'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-private-residence-suites').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_private_residence_suites = array(
			    'name' => 'rental_private_residence_suites',
			    'id' => 'rental_private_residence_suites',
			    'value' => set_value('rental_private_residence_suites', ((isset($rental_private_residence_suites)) ? htmlspecialchars_decode($rental_private_residence_suites) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($rental_private_residence_suites); ?><span class="validation_error"><?php echo form_error('rental_private_residence_suites'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('indoor-parking-area').'('.getLocalUnitText($id).')'; ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$indoor_parking_area = array(
			    'name' => 'indoor_parking_area',
			    'id' => 'indoor_parking_area',
			    'value' => set_value('indoor_parking_area', ((isset($indoor_parking_area)) ? htmlspecialchars_decode($indoor_parking_area) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($indoor_parking_area); ?><span class="validation_error"><?php echo form_error('indoor_parking_area'); ?></span>
			<label class="input-label"><?php echo getLocalUnitText($id); ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('room-keys'); ?> </label>
		    <div class="form-col-3">
			<?php
			$rooms_keys = array(
			    'name' => 'rooms_keys',
			    'id' => 'rooms_keys',
			    'value' => set_value('rooms_keys', ((isset($rooms_keys)) ? htmlspecialchars_decode($rooms_keys) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control',
			    'maxlength' => 3
			);
			?>
			<?php echo form_input($rooms_keys); ?><span class="validation_error"><?php echo form_error('rooms_keys'); ?></span>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('outdoor-pools'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$outdoor_pools = array(
			    'name' => 'outdoor_pools',
			    'id' => 'outdoor_pools',
			    'value' => set_value('outdoor_pools', ((isset($outdoor_pools)) ? htmlspecialchars_decode($outdoor_pools) : '')),
			    'class' => 'input-control',
			    'disabled' => 'disabled',
			    'maxlength' => 3
			);
			?>
			<?php echo form_input($outdoor_pools); ?><span class="validation_error"><?php echo form_error('outdoor_pools'); ?></span>
			<label class="input-label"><?php echo lang('m3'); ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('indoor-pools'); ?> </label>
		    <div class="form-col-3">
			<?php
			$indoor_pools = array(
			    'name' => 'indoor_pools',
			    'id' => 'indoor_pools',
			    'value' => set_value('indoor_pools', ((isset($indoor_pools)) ? htmlspecialchars_decode($indoor_pools) : '')),
			    'class' => 'input-control',
			    'disabled' => 'disabled',
			    'maxlength' => 3
			);
			?>
			<?php echo form_input($indoor_pools); ?><span class="validation_error"><?php echo form_error('indoor_pools'); ?></span>
			<label class="input-label"><?php echo lang('m3'); ?></label>
		    </div>

		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('laundry'), 'laundry', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-6">
			<div class="form-dropdown">
			    <?php
			    $laundry_type_list = array('1' => 'Outsourced', '0' => 'On Site');
			    echo form_dropdown('laundry_type', $laundry_type_list, $laundry_type, 'disabled="disabled" data-type = "custom-dropdown" ');
			    ?>
			</div>
			<?php echo form_label(lang('onsite-outsourced'), 'onsite_outsourced', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-6">
			<div class="form-dropdown">
			    <?php
			    // $laundry_fuel_type_list = array('1' => 'Steam', '2' => 'Electricity', '3' => 'Gas');
			    $laundry_fuel_type_list = array('Steam' => 'Steam', 'Electricity' => 'Electricity', 'Gas' => 'Gas');
			    echo form_dropdown('laundry_fuel_type', $laundry_fuel_type_list, $laundry_fuel_type, 'disabled="disabled" data-type = "custom-dropdown" ');
			    ?>
			</div>
			<?php echo form_label(lang('onsite-outsourced'), 'onsite_outsourced', ["class" => "input-label"]); ?>
		    </div>
		</div>
	    </li>

	    <li>
		<label class="main-label"><?php echo lang('substation-rating'); ?> </label>
		<?php
		echo form_label(lang('substation-rating'), 'substation_rating', ["class" => "main-label"]);
		if (!empty($substations)) {
		    foreach ($substations as $key => $substation) {
			?>
			<div class="row add-row">
			    <div class="form-col-5">
				<?php
				$substation_quantity = array(
				    'name' => 'substation[substation_quantity][]',
				    'id' => 'substation_quantity',
				    'value' => $substation['substation_quantity'],
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 5
				);
				?>
				<input name="substation[substation_id][]" value="<?php echo $substation['id']; ?>" type="hidden" />
				<?php echo form_input($substation_quantity); ?><span class="validation_error"><?php echo form_error('substation[substation_quantity][]'); ?></span>
				<?php echo form_label(lang('substation-quantity'), 'substation_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-5 form-col-add">
				<?php
				$substation_power = array(
				    'name' => 'substation[substation_power][]',
				    'id' => 'substation_power',
				    'value' => $substation['substation_power'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($substation_power); ?><span class="validation_error"><?php echo form_error('substation[substation_power][]'); ?></span>
				<?php echo form_label(lang('substation-power'), 'substation_power', ["class" => "input-label"]); ?>
			    </div>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-5">
			    <?php
			    $substation_quantity = array(
				'name' => 'substation[substation_quantity][]',
				'id' => 'substation_quantity',
				'value' => set_value('substation[substation_quantity][]'),
				'class' => 'input-control',
				'disabled' => 'disabled',
				'maxlength' => 5
			    );
			    ?>
			    <input name="substation[substation_id][]" value="<?php echo $substation['id']; ?>" type="hidden" />
			    <?php echo form_input($substation_quantity); ?><span class="validation_error"><?php echo form_error('substation[substation_quantity][]'); ?></span>
			    <?php echo form_label(lang('substation-quantity'), 'substation_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-5 form-col-add">
			    <?php
			    $substation_power = array(
				'name' => 'substation[substation_power][]',
				'id' => 'substation_power',
				'value' => set_value('substation[substation_power][]'),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($substation_power); ?><span class="validation_error"><?php echo form_error('substation[substation_power][]'); ?></span>
			    <?php echo form_label(lang('substation-power'), 'substation_power', ["class" => "input-label"]); ?>
			</div>
		    </div>
		<?php } ?>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('onsite-generators'); ?> </label>
		<?php
		if (!empty($generators)) {
		    foreach ($generators as $key => $generator) {
			?>
			<div class="row add-row">
			    <div class="form-col-3 form-col-add">
				<?php
				$generators_name = array(
				    'name' => 'generator[generator_name][]',
				    'id' => 'generators_name',
				    'value' => $generator['generator_name'],
				    'disabled' => 'disabled',
				    'class' => 'input-control'
				);
				?>
				<?php echo form_input($generators_name); ?><span class="validation_error"><?php echo form_error('generator[generator_name][]'); ?></span>
				<?php echo form_label(lang('generators-name'), 'generators_name', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2">
				<?php
				$generators_quantity = array(
				    'name' => 'generator[generator_quantity][]',
				    'id' => 'generators_quantity',
				    'value' => $generator['generator_quantity'],
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 5
				);
				?>
				<input type="hidden" name="generator[generator_id][]" value="<?php echo $generator['id']; ?>"  />
				<?php echo form_input($generators_quantity); ?><span class="validation_error"><?php echo form_error('generator[generator_quantity][]'); ?></span>
				<?php echo form_label(lang('generators-quantity'), 'generators_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2 form-col-add">
				<?php
				$generators_power = array(
				    'name' => 'generator[generator_power][]',
				    'id' => 'generators_power',
				    'value' => $generator['generator_power'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($generators_power); ?><span class="validation_error"><?php echo form_error('generator[generator_power][]'); ?></span>
				<?php echo form_label(lang('generators-power'), 'generators_power', ["class" => "input-label"]); ?>
			    </div>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-3 form-col-add">
			    <?php
			    $generators_name = array(
				'name' => 'generator[generator_name][]',
				'id' => 'generators_name',
				'value' => set_value('generator[generator_name][]'),
				'disabled' => 'disabled',
				'class' => 'input-control'
			    );
			    ?>
			    <?php echo form_input($generators_name); ?><span class="validation_error"><?php echo form_error('generator[generator_name][]'); ?></span>
			    <?php echo form_label(lang('generators-name'), 'generators_name', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-2">
			    <?php
			    $generators_quantity = array(
				'name' => 'generator[generator_quantity][]',
				'id' => 'generators_quantity',
				'value' => set_value('generator[generator_quantity][]'),
				'class' => 'input-control',
				'disabled' => 'disabled',
				'maxlength' => 5
			    );
			    ?>
			    <input name='generator[generator_id][]' value='0' type='hidden' />
			    <?php echo form_input($generators_quantity); ?><span class="validation_error"><?php echo form_error('generator[generator_quantity][]'); ?></span>
			    <?php echo form_label(lang('generators-quantity'), 'generators_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-2 form-col-add">
			    <?php
			    $generators_power = array(
				'name' => 'generator[generator_power][]',
				'id' => 'generators_power',
				'value' => set_value('generator[generator_power][]'),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($generators_power); ?><span class="validation_error"><?php echo form_error('generator[generator_power][]'); ?></span>
			    <?php echo form_label(lang('generators-power'), 'generators_power', ["class" => "input-label"]); ?>
			</div>
		    </div>
		<?php } ?>
	    </li>

	    <li>
		<?php echo form_label(lang('chilled-water-system'), 'chilled_water_system', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<?php //pre($is_chilled_water_system); ?>
			<label class="radio-outer"><input type="radio" disabled="disabled"  class="icheck chilled_water_radio" name="is_chilled_water_system" <?php
			if (isset($is_chilled_water_system) && $is_chilled_water_system == 1) {
			    echo 'checked="checked"';
			}
			?> value="1">Yes</label>
			<label><input type="radio" disabled="disabled"  class="icheck chilled_water_radio"  <?php
			    if (!isset($is_chilled_water_system) || $is_chilled_water_system == 0) {
				echo 'checked="checked"';
			    }
			?> name="is_chilled_water_system" value="0">No</label>
		    </div>
		    <?php if(isset($is_chilled_water_system) && $is_chilled_water_system == 1) { ?>
			<div class="form-col-3 chilled_water_content">
			    <?php if (isset($is_chilled_water_system) && $is_chilled_water_system == 1 && isset($chilled_water_system_type) && $chilled_water_system_type != '' && isset($chilled_water_system_total_rate) && $chilled_water_system_total_rate != '') { ?>
				<div class="form-dropdown">
				    <?php
				    $chilled_water_system_type_list = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled');
				    echo form_dropdown('chilled_water_system_type', $chilled_water_system_type_list, $chilled_water_system_type, 'disabled="disabled" data-type = "custom-dropdown" ');
				    ?>
				</div>
				<?php echo form_label(lang('air-cooled-water-cooled'), 'air_cooled_water_cooled', ["class" => "input-label"]); ?>
			    <?php } else { ?>
				<div class="form-dropdown">
				    <?php
				    $chilled_water_system_type_list = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled');
				    echo form_dropdown('chilled_water_system_type', $chilled_water_system_type_list, '', 'disabled="disabled" data-type = "custom-dropdown" ');
				    ?>
				</div>
				<?php echo form_label(lang('air-cooled-water-cooled'), 'air_cooled_water_cooled', ["class" => "input-label"]); ?>

			    <?php } ?>
			</div>
			<div class="form-col-2 chilled_water_content">
			    <?php if (isset($is_chilled_water_system) && $is_chilled_water_system == 1 && isset($chilled_water_system_type) && $chilled_water_system_type != '' && isset($chilled_water_system_total_rate) && $chilled_water_system_total_rate != '') { ?>
				<?php
				$chilled_water_system_total_rate = array(
				    'name' => 'chilled_water_system_total_rate',
				    'id' => 'chilled_water_system_total_rate',
				    'value' => set_value('chilled_water_system_total_rate', ((isset($chilled_water_system_total_rate)) ? htmlspecialchars_decode($chilled_water_system_total_rate) : '')),
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($chilled_water_system_total_rate); ?><span class="validation_error"><?php echo form_error('chilled_water_system_total_rate'); ?></span>
				<?php echo form_label(lang('chilled-water-system-total-rate'), 'total_rt', ["class" => "input-label"]); ?>
				<?php
			    } else {
				$chilled_water_system_total_rate = array(
				    'name' => 'chilled_water_system_total_rate',
				    'id' => 'chilled_water_system_total_rate',
				    'value' => '',
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck');
				?>
				<?php echo form_input($chilled_water_system_total_rate); ?><span class="validation_error"><?php echo form_error('chilled_water_system_total_rate'); ?></span>
				<?php
				echo form_label(lang('chilled-water-system-total-rate'), 'total_rt', ["class" => "input-label"]);
			    }
			    ?>
			</div>

		    <?php
		    $system2exists = false;

		    if(!empty($chilled_water_system_total_rate2)){
			$system2exists = true;
		    }


		    $chilled_water_system_type_list_addmore = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled');
		    $dropdown = form_dropdown('chilled_water_system_type2', $chilled_water_system_type_list_addmore, '', 'id="chilled_water_system_type2" disabled="disabled" data-type = "custom-dropdown-addmore" ');
		    ?>
		    <?php } ?>
		</div>

		<?php if($system2exists){ ?>
		    <?php
		    $chilled_water_system_type_list_addmore = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled');
		    $dropdown = form_dropdown('chilled_water_system_type2', $chilled_water_system_type_list_addmore, $chilled_water_system_type2, 'id="chilled_water_system_type2" disabled="disabled" data-type = "custom-dropdown-addmore" ');
		    ?>
		    <div class='row add-row chilled_water_content'><div class='form-col-2 form-control-block'></div><div class='form-col-3 form-dropdown'><?php echo str_replace('"', "'", $dropdown); ?></div><div class='form-col-2'><input id='chilled_water_system_total_rate2' disabled="disabled" name='chilled_water_system_total_rate2' type='text' class='input-control floatcheck' value="<?php echo $chilled_water_system_total_rate2; ?>"></div><div class='form-col-1'></div>
		<?php } ?>
	    </li>
	    <li>
		<?php echo form_label(lang('split-dx-units'), 'split_dx_units', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" disabled="disabled"  class="icheck split_radio" <?php
		if (isset($is_split_dx_unit) && $is_split_dx_unit == 1) {
		    echo 'checked="checked"';
		}
		?> name="is_split_dx_unit" value="1">Yes</label>
			<label><input type="radio" disabled="disabled"  <?php
			    if (!isset($is_split_dx_unit) || $is_split_dx_unit == 0) {
				echo 'checked="checked"';
			    }
		?> class="icheck split_radio" name="is_split_dx_unit" value="0">No</label>
		    </div>
		    <?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1) { ?>
			<div class="form-col-2 split_content">
			    <?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1 && isset($total_split_dx_unit) && $total_split_dx_unit != '' && isset($total_rate_split_dx_unit) && $total_rate_split_dx_unit != '') { ?>
				<?php
				$total_split_dx_unit = array(
				    'name' => 'total_split_dx_unit',
				    'id' => 'total_split_dx_unit',
				    'value' => set_value('total_split_dx_unit', ((isset($total_split_dx_unit)) ? htmlspecialchars_decode($total_split_dx_unit) : '')),
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 3
				);
				?>
				<?php echo form_input($total_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_split_dx_unit'); ?></span>
				<?php echo form_label(lang('total-rt'), 'total_rt', ["class" => "input-label"]); ?>

			    <?php } else { ?>
				<?php
				$total_split_dx_unit = array(
				    'name' => 'total_split_dx_unit',
				    'id' => 'total_split_dx_unit',
				    'value' => '',
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 3
				);
				?>
				<?php echo form_input($total_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_split_dx_unit'); ?></span>
				<?php echo form_label(lang('total-rate-split-dx-unit'), 'total_split_dx_unit', ["class" => "input-label"]); ?>


			    <?php } ?>
			</div>
			<div class="form-col-5 split_content">
			    <?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1 && isset($total_split_dx_unit) && $total_split_dx_unit != '' && isset($total_rate_split_dx_unit) && $total_rate_split_dx_unit != '') { ?>
				<?php
				$total_rate_split_dx_unit = array(
				    'name' => 'total_rate_split_dx_unit',
				    'id' => 'total_rate_split_dx_unit',
				    'value' => set_value('total_rate_split_dx_unit', ((isset($total_rate_split_dx_unit)) ? htmlspecialchars_decode($total_rate_split_dx_unit) : '')),
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($total_rate_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_rate_split_dx_unit'); ?></span>
				<?php echo form_label(lang('total-rate-split-dx-unit'), 'total_rate_split_dx_unit', ["class" => "input-label"]); ?>
			    <?php } else { ?>
				<?php
				$total_rate_split_dx_unit = array(
				    'name' => 'total_rate_split_dx_unit',
				    'id' => 'total_rate_split_dx_unit',
				    'value' => '',
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($total_rate_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_rate_split_dx_unit'); ?></span>
				<?php echo form_label(lang('total-rt'), 'total_rt', ["class" => "input-label"]); ?>

			    <?php } ?>
			</div>
		    <?php } ?>
		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('vrv'), 'vrv', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" disabled="disabled"  <?php
			if (isset($is_vrv) && $is_vrv == 1) {
			    echo 'checked="checked"';
			}
			?> class="icheck vrv_radio" name="is_vrv" value="1">Yes</label>
				<label><input type="radio" disabled="disabled"  <?php
				    if (!isset($is_vrv) || $is_vrv == 0) {
					echo 'checked="checked"';
				    }
			?>  class="icheck vrv_radio" name="is_vrv" value="0">No</label>
		    </div>
		    <?php if (isset($is_vrv) && $is_vrv == 1) { ?>
			<div class="form-col-2 vrv_content">
			    <?php if (isset($is_vrv) && $is_vrv == 1 && isset($total_vrv) && $total_vrv != '') { ?>
				<?php
				$total_vrv_unit = array(
				    'name' => 'total_vrv_unit',
				    'id' => 'total_vrv_unit',
				    'value' => set_value('total_vrv_unit', ((isset($total_vrv_unit)) ? htmlspecialchars_decode($total_vrv_unit) : '')),
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 3
				);
				?>
				<?php echo form_input($total_vrv_unit); ?><span class="validation_error"><?php echo form_error('total_vrv_unit'); ?></span>
				<?php echo form_label(lang('total-rt'), 'total_rt', ["class" => "input-label"]); ?>

			    <?php } else { ?>
				<?php
				$total_vrv_unit = array(
				    'name' => 'total_vrv_unit',
				    'id' => 'total_vrv_unit',
				    'value' => '',
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 3
				);
				?>
				<?php echo form_input($total_vrv_unit); ?><span class="validation_error"><?php echo form_error('total_vrv_unit'); ?></span>
				<?php echo form_label(lang('total-vrv-unit-unit'), 'total_vrv_unit', ["class" => "input-label"]); ?>


			    <?php } ?>
			</div>
			<div class="form-col-5 vrv_content">
			    <?php if (isset($is_vrv) && $is_vrv == 1 && isset($total_vrv) && $total_vrv != '') { ?>

				<div class="form-dropdown">
				    <?php
				    $total_vrv = array(
					'name' => 'total_vrv',
					'id' => 'total_vrv',
					'value' => set_value('total_vrv', ((isset($total_vrv)) ? htmlspecialchars_decode($total_vrv) : '')),
					'class' => 'input-control',
					'disabled' => 'disabled',
					'maxlength' => 3
				    );
				    ?>
				    <?php echo form_input($total_vrv); ?><span class="validation_error"><?php echo form_error('total_vrv'); ?></span>
				</div>
				<?php echo form_label(lang('total'), 'total', ["class" => "input-label"]); ?>

			    <?php } else { ?>
				<div class="form-dropdown">
				    <?php
				    $total_vrv = array(
					'name' => 'total_vrv',
					'id' => 'total_vrv',
					'value' => '',
					'class' => 'input-control',
					'disabled' => 'disabled',
					'maxlength' => 3
				    );
				    ?>
				    <?php echo form_input($total_vrv); ?><span class="validation_error"><?php echo form_error('total_vrv'); ?></span>
				</div>
				<?php echo form_label(lang('total-vrv'), 'total_vrv', ["class" => "input-label"]); ?>
			    <?php } ?>
			</div>
		    <?php } ?>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('hot-water-boiler'); ?> </label>
		<?php
		if (!empty($hot_water_boilers)) {
		    foreach ($hot_water_boilers as $key => $hot_water_boiler) {
			?>
			<div class="row add-row">
			    <div class="form-col-5">
				<?php
				$hot_water_bolier_total_rate = array(
				    'name' => 'hot_water_boiler[hot_water_boiler_quantity][]',
				    'id' => 'hot_water_bolier_quantity',
				    'value' => $hot_water_boiler['hot_water_boiler_quantity'],
				    'class' => 'input-control',
				    'disabled' => 'disabled',
				    'maxlength' => 5
				);
				?>
				<?php echo form_input($hot_water_bolier_total_rate); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_quantity][]'); ?></span>
				<?php echo form_label(lang('hot-water-bolier-quantity'), 'hot_water_bolier_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-5 form-col-add">
				<?php
				$hot_water_boiler_power = array(
				    'name' => 'hot_water_boiler[hot_water_boiler_power][]',
				    'id' => 'hot_water_boiler_power',
				    'value' => $hot_water_boiler['hot_water_boiler_power'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($hot_water_boiler_power); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_power][]'); ?></span>

				<?php echo form_label(lang('hot-water-boiler-power'), 'hot_water_boiler_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='hot_water_boiler[hot_water_boiler_id][]' value='<?php echo $hot_water_boiler['id']; ?>' type='hidden' />
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-5">
			    <?php
			    $hot_water_bolier_total_rate = array(
				'name' => 'hot_water_boiler[hot_water_boiler_quantity][]',
				'id' => 'hot_water_bolier_quantity',
				'value' => set_value('hot_water_boiler[hot_water_boiler_quantity][]'),
				'class' => 'input-control',
				'disabled' => 'disabled',
				'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($hot_water_bolier_total_rate); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_quantity][]'); ?></span>
			    <?php echo form_label(lang('hot-water-bolier-quantity'), 'hot_water_bolier_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-5 form-col-add">
			    <?php
			    $hot_water_boiler_power = array(
				'name' => 'hot_water_boiler[hot_water_boiler_power][]',
				'id' => 'hot_water_boiler_power',
				'value' => set_value('hot_water_boiler[hot_water_boiler_power][]'),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($hot_water_boiler_power); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_power][]'); ?></span>

			    <?php echo form_label(lang('hot-water-boiler-power'), 'hot_water_boiler_power', ["class" => "input-label"]); ?>
			</div>
			<input name='hot_water_boiler[hot_water_boiler_id][]' value='0' type='hidden' />
		    </div>
		<?php } ?>
	    </li>







	    <li>
		<label class="main-label"><?php echo lang('calorifiers-label'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$calorifiers_unit = array(
			    'name' => 'calorifiers_unit',
			    'id' => 'calorifiers_unit',
			    'value' => set_value('calorifiers_unit', ((isset($calorifiers_unit)) ? htmlspecialchars_decode($calorifiers_unit) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($calorifiers_unit); ?><span class="validation_error"><?php echo form_error('calorifiers_unit'); ?></span>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('calorifiers-volume'); ?> </label>
		    <div class="form-col-3">
			<?php
			$calorifiers_volume = array(
			    'name' => 'calorifiers_volume',
			    'id' => 'calorifiers_volume',
			    'value' => set_value('calorifiers_volume', ((isset($calorifiers_volume)) ? htmlspecialchars_decode($calorifiers_volume) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($calorifiers_volume); ?><span class="validation_error"><?php echo form_error('calorifiers_volume'); ?></span>
		    </div>
		</div>
	    </li>






	    <li>
		<label class="main-label"><?php echo lang('steam-boiler'); ?> </label>
		<?php
		if (!empty($steam_boilers)) {
		    foreach ($steam_boilers as $key => $steam_boiler) {
			?>
			<div class="row add-row">
			    <div class="form-col-5">
				<?php
				$steam_bolier_quantity_total_rate = array(
				    'name' => 'steam_boiler[steam_boiler_quantity][]',
				    'id' => 'steam_bolier_quantity',
				    'value' => $steam_boiler['steam_boiler_quantity'],
				    'disabled' => 'disabled',
				    'class' => 'input-control',
				    'maxlength' => 5
				);
				?>
				<?php echo form_input($steam_bolier_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_quantity][]'); ?></span>
				<?php echo form_label(lang('steam-bolier-quantity'), 'steam_bolier_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-5 form-col-add">
				<?php
				$steam_boiler_power = array(
				    'name' => 'steam_boiler[steam_boiler_power][]',
				    'id' => 'steam_boiler_power',
				    'value' => $steam_boiler['steam_boiler_power'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($steam_boiler_power); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_power][]'); ?></span>

				<?php echo form_label(lang('steam-boiler-power'), 'steam_boiler_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='steam_boiler[steam_boiler_id][]' value='<?php echo $steam_boiler['id']; ?>' type='hidden' />
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-5">
			    <?php
			    $steam_bolier_quantity_total_rate = array(
				'name' => 'steam_boiler[steam_boiler_quantity][]',
				'id' => 'steam_bolier_quantity',
				'value' => set_value('steam_boiler[steam_boiler_quantity][]'),
				'class' => 'input-control',
				'disabled' => 'disabled',
				'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($steam_bolier_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_quantity][]'); ?></span>
			    <?php echo form_label(lang('steam-bolier-quantity'), 'steam_bolier_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-5 form-col-add">
			    <?php
			    $steam_boiler_power = array(
				'name' => 'steam_boiler[steam_boiler_power][]',
				'id' => 'steam_boiler_power',
				'value' => set_value('steam_boiler[steam_boiler_power][]'),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($steam_boiler_power); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_power][]'); ?></span>
			    <?php echo form_label(lang('steam-boiler-power'), 'steam_boiler_power', ["class" => "input-label"]); ?>
			</div>
			<input name='steam_boiler[steam_boiler_id][]' value='0' type='hidden' />

		    <?php } ?>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('electrical-hw'); ?> </label>
		<div class="row add-row">
		    <div class="form-col-3 form-col-add">
			<?php
			$elcetrical_hw_total = array(
			    'name' => 'elcetrical_hw_total',
			    'id' => 'elcetrical_hw_total',
			    'value' => set_value('elcetrical_hw_total', ((isset($elcetrical_hw_total)) ? htmlspecialchars_decode($elcetrical_hw_total) : '')),
			    'class' => 'input-control',
			    'disabled' => 'disabled',
			    'maxlength' => 5
			);
			?>
			<?php echo form_input($elcetrical_hw_total); ?><span class="validation_error"><?php echo form_error('elcetrical_hw_total'); ?></span>
			<?php echo form_label(lang('elcetrical-hw-total'), 'elcetrical_hw_total', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $elcetrical_hw_total_capacity = array(
				'name' => 'elcetrical_hw_total_capacity',
				'id' => 'elcetrical_hw_total_capacity',
				'value' => set_value('elcetrical_hw_total_capacity', ((isset($elcetrical_hw_total_capacity)) ? htmlspecialchars_decode($elcetrical_hw_total_capacity) : '')),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($elcetrical_hw_total_capacity); ?><span class="validation_error"><?php echo form_error('elcetrical_hw_total_power'); ?></span>
			</div>
			<?php echo form_label(lang('elcetrical-hw-total-capacity'), 'elcetrical_hw_total_capacity', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3 form-col-add">
			<?php
			$elcetrical_hw_total_power = array(
			    'name' => 'elcetrical_hw_total_power',
			    'id' => 'elcetrical_hw_total_power',
			    'value' => set_value('elcetrical_hw_total_power', ((isset($elcetrical_hw_total_power)) ? htmlspecialchars_decode($elcetrical_hw_total_power) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($elcetrical_hw_total_power); ?><span class="validation_error"><?php echo form_error('elcetrical_hw_total_power'); ?></span>
			<?php echo form_label(lang('elcetrical-hw-total-power'), 'elcetrical_hw_total_power', ["class" => "input-label"]); ?>
		    </div>

		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('ro-plant'), 'ro_plant', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" disabled="disabled"  <?php
		if (isset($is_ro_plant) && $is_ro_plant == 1) {
		    echo 'checked="checked"';
		}
		?>class="icheck ro_plant_radio" name="is_ro_plant" value="1">Yes</label>
			<label><input type="radio" disabled="disabled"  <?php
			    if (!isset($is_ro_plant) || $is_ro_plant == 0) {
				echo 'checked="checked"';
			    }
		?> class="icheck ro_plant_radio" name="is_ro_plant" value="0">No</label>
		    </div>
		    <?php if (isset($is_ro_plant) && $is_ro_plant == 1) { ?>
		    <div class="form-col-10 ro_plant_content">
			<?php if (isset($is_ro_plant) && $is_ro_plant == 1 && isset($ro_plant_capacity) && $ro_plant_capacity != '') { ?>
			    <?php
			    $ro_plant_capacity = array(
				'name' => 'ro_plant_capacity',
				'id' => 'ro_plant_capacity',
				'value' => set_value('ro_plant_capacity', ((isset($ro_plant_capacity)) ? htmlspecialchars_decode($ro_plant_capacity) : '')),
				'disabled' => 'disabled',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($ro_plant_capacity); ?><span class="validation_error"><?php echo form_error('ro_plant_capacity'); ?></span>
			    <?php echo form_label(lang('ro-capacity'), 'ro-capacity', ["class" => "input-label"]); ?>
			<?php } else { ?>
			    <?php
			    $ro_plant_capacity = array(
				'name' => 'ro_plant_capacity',
				'id' => 'ro_plant_capacity',
				'disabled' => 'disabled',
				'value' => '',
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($ro_plant_capacity); ?><span class="validation_error"><?php echo form_error('ro_plant_capacity'); ?></span>
			    <?php echo form_label(lang('ro-capacity'), 'ro-capacity', ["class" => "input-label"]); ?>
			<?php } ?>
		    </div>
		    <?php } ?>
		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('renewable-energy'), 'renewable_energy', ["class" => "main-label"]); ?>
		<div class="row add-row">
		    <?php do { ?>
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" disabled="disabled"  <?php
			if (isset($is_renewable_energy) && $is_renewable_energy == 1) {
			    echo 'checked="checked"';
			}
			?> class="icheck renewable_energy_radio" name="is_renewable_energy" value="1">Yes</label>
			    <label><input type="radio" disabled="disabled"  <?php
			    if (!isset($is_renewable_energy) || $is_renewable_energy == 0) {
				echo 'checked="checked"';
			    }
			?>  class="icheck renewable_energy_radio" name="is_renewable_energy" value="0">No</label>
			</div>
			<?php if (isset($is_renewable_energy) && $is_renewable_energy == 1) { ?>
			    <div class="form-col-2 form-col-lg-2 renewable_energy_content">
				<?php
				$renewable_energy_type = array(
				    'name' => 'renewable_energy[renewable_energy_type][]',
				    'id' => 'renewable_energy_type',
				    'value' => $renewable_energys[0]['renewable_energy_type'],
				    'disabled' => 'disabled',
				    'class' => 'input-control'
				);
				?>
				<?php echo form_input($renewable_energy_type); ?><span class="validation_error"><?php echo form_error('renewable_energy_type'); ?></span>

				<?php echo form_label(lang('renewable-energy-type'), 'renewable_energy_type', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2 form-col-lg-2 renewable_energy_content">
				<?php
				$renewable_energy_quantity_total_rate = array(
				    'name' => 'renewable_energy[renewable_energy_quantity][]',
				    'id' => 'renewable_energy_quantity',
				    'value' => $renewable_energys[0]['renewable_energy_quantity'],
				    'disabled' => 'disabled',
				    'class' => 'input-control',
				    'maxlength' => 5
				);
				?>
				<?php echo form_input($renewable_energy_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('renewable_energy_quantity'); ?></span>
				<?php echo form_label(lang('renewable-energy-quantity'), 'renewable_energy_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2 form-col-lg-2 renewable_energy_content">
				<?php
				$renewable_energy_power = array(
				    'name' => 'renewable_energy[renewable_energy_capacity][]',
				    'id' => 'renewable_energy_capacity',
				    'value' => $renewable_energys[0]['renewable_energy_capacity'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($renewable_energy_power); ?><span class="validation_error"><?php echo form_error('renewable_energy_capacity'); ?></span>
				<?php echo form_label(lang('renewable-energy-capacirty'), 'renewable_energy_power', ["class" => "input-label"]); ?>
			    </div>
			<?php } ?>
		    <?php } while (1 == 2)
		    ?>
		</div>
		<?php
		if (count($renewable_energys) >= 2) {
		    for ($i = 1; $i < count($renewable_energys); $i = $i + 1) {
			?>
			<div class="row add-row renewable_energy_content">
			    <div class="form-col-2 form-control-block">&nbsp; </div>
			    <div class="form-col-2 form-col-lg-2">
				<?php
				$renewable_energy_type = array(
				    'name' => 'renewable_energy[renewable_energy_type][]',
				    'id' => 'renewable_energy_type',
				    'value' => $renewable_energys[$i]['renewable_energy_type'],
				    'disabled' => 'disabled',
				    'class' => 'input-control'
				);
				?>
				<?php echo form_input($renewable_energy_type); ?><span class="validation_error"><?php echo form_error('renewable_energy_type'); ?></span>

				<?php echo form_label(lang('renewable-energy-type'), 'renewable_energy_type', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2 form-col-lg-2">
				<?php
				$renewable_energy_quantity_total_rate = array(
				    'name' => 'renewable_energy[renewable_energy_quantity][]',
				    'id' => 'renewable_energy_quantity',
				    'value' => $renewable_energys[$i]['renewable_energy_quantity'],
				    'disabled' => 'disabled',
				    'class' => 'input-control',
				    'maxlength' => 5
				);
				?>
				<?php echo form_input($renewable_energy_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('renewable_energy_quantity'); ?></span>
				<?php echo form_label(lang('renewable-energy-quantity'), 'renewable_energy_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-2 form-col-lg-2">
				<?php
				$renewable_energy_power = array(
				    'name' => 'renewable_energy[renewable_energy_capacity][]',
				    'id' => 'renewable_energy_capacity',
				    'value' => $renewable_energys[$i]['renewable_energy_capacity'],
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($renewable_energy_power); ?><span class="validation_error"><?php echo form_error('renewable_energy_capacity'); ?></span>

				<?php echo form_label(lang('renewable-energy-capacirty'), 'renewable_energy_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='renewable_energy[renewable_energy_id][]' value='<?php echo $renewable_energys[$i]['id'] ?>' type='hidden' />
			    <div class="form-col-1">
			    </div>
			</div>
			<?php
		    }
		}
		?>

	    </li>
	    <li>
		<?php echo form_label(lang('stp'), 'stp', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" disabled="disabled"  <?php
		if (isset($is_stp) && $is_stp == 1) {
		    echo 'checked="checked"';
		}
		?>  class="icheck stp_radio" name="is_stp" value="1">Yes</label>
			<label><input type="radio" disabled="disabled"   <?php
			    if (!isset($is_stp) || $is_stp == 0) {
				echo 'checked="checked"';
			    }
		?>  class="icheck stp_radio" name="is_stp" value="0">No</label>
		    </div>
		    <?php if (isset($is_stp) && $is_stp == 1) { ?>
			<div class="form-col-10 stp_content">
			    <?php if (isset($is_stp) && $is_stp == 1 && isset($stp_capacity) && $stp_capacity != '') { ?>
				<?php
				$stp_capacity = array(
				    'name' => 'stp_capacity',
				    'id' => 'stp_capacity',
				    'value' => set_value('stp_capacity', ((isset($stp_capacity)) ? htmlspecialchars_decode($stp_capacity) : '')),
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($stp_capacity); ?><span class="validation_error"><?php echo form_error('stp_capacity'); ?></span>
				<?php echo form_label(lang('stp-capacity'), 'stp-capacity', ["class" => "input-label"]); ?>
			    <?php } else { ?>
				<?php
				$stp_capacity = array(
				    'name' => 'stp_capacity',
				    'id' => 'stp_capacity',
				    'value' => '',
				    'disabled' => 'disabled',
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($stp_capacity); ?><span class="validation_error"><?php echo form_error('stp_capacity'); ?></span>
				<?php echo form_label(lang('stp-capacity'), 'stp-capacity', ["class" => "input-label"]); ?>
			    <?php } ?>
			</div>
		    <?php } ?>
		</div>
	    </li>
	</ul>
	<br/>
	<div class="form-group-label">
	    <h5><strong><?php echo lang('ghg_emissions_factor'); ?></strong></h5>
	</div>
	<ul class="form-outer-block">
	    <li>
		<label class="main-label"><?php echo lang('electricity-emission-factor'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$electricity_emission_factor = array(
			    'name' => 'electricity_emission_factor',
			    'id' => 'electricity_emission_factor',
			    'value' => set_value('electricity_emission_factor', ((isset($electricity_emission_factor)) ? htmlspecialchars_decode($electricity_emission_factor) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($electricity_emission_factor); ?><span class="validation_error"><?php echo form_error('electricity_emission_factor'); ?></span>
			<label class="input-label"><?php echo $electricity_kgco2; ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('fuel-emission-factor'); ?> </label>
		    <div class="form-col-3">
			<?php
			$fuel_emission_factor = array(
			    'name' => 'fuel_emission_factor',
			    'id' => 'fuel_emission_factor',
			    'value' => set_value('fuel_emission_factor', ((isset($fuel_emission_factor)) ? htmlspecialchars_decode($fuel_emission_factor) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($fuel_emission_factor); ?><span class="validation_error"><?php echo form_error('fuel_emission_factor'); ?></span>
			<label class="input-label"><?php echo $fuel_oil_kgco2; ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('lpg-emission-factor'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$lpg_emission_factor = array(
			    'name' => 'lpg_emission_factor',
			    'id' => 'lpg_emission_factor',
			    'value' => set_value('lpg_emission_factor', ((isset($lpg_emission_factor)) ? htmlspecialchars_decode($lpg_emission_factor) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($lpg_emission_factor); ?><span class="validation_error"><?php echo form_error('lpg_emission_factor'); ?></span>
			<label class="input-label"><?php echo $lpg_kgco2; ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('natural-gas-emission-factor'); ?> </label>
		    <div class="form-col-3">
			<?php
			$natural_gas_emission_factor = array(
			    'name' => 'natural_gas_emission_factor',
			    'id' => 'natural_gas_emission_factor',
			    'value' => set_value('natural_gas_emission_factor', ((isset($natural_gas_emission_factor)) ? htmlspecialchars_decode($natural_gas_emission_factor) : '')),
			    'disabled' => 'disabled',
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($natural_gas_emission_factor); ?><span class="validation_error"><?php echo form_error('natural_gas_emission_factor'); ?></span>
			<label class="input-label"><?php echo $natural_gas_kgco2; ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('district-cooling-emission-factor'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$district_cooling_emission_factor = array(
			    'name' => 'district_cooling_emission_factor',
			    'id' => 'district_cooling_emission_factor',
			    'value' => set_value('district_cooling_emission_factor', ((isset($district_cooling_emission_factor)) ? htmlspecialchars_decode($district_cooling_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
<?php echo form_input($district_cooling_emission_factor); ?><span class="validation_error"><?php echo form_error('district_cooling_emission_factor'); ?></span>

		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('district-heating-emission-factor'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$district_heating_emission_factor = array(
			    'name' => 'district_heating_emission_factor',
			    'id' => 'district_heating_emission_factor',
			    'value' => set_value('district_heating_emission_factor', ((isset($district_heating_emission_factor)) ? htmlspecialchars_decode($district_heating_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
<?php echo form_input($district_heating_emission_factor); ?><span class="validation_error"><?php echo form_error('district_heating_emission_factor'); ?></span>

		    </div>
		</div>
	    </li>
	    <li>
	    <li>
		<?php echo form_label(lang('status'), 'status', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-12">
			<div class="form-dropdown">
			    <?php
			    $statuslist = array('1' => 'Active', '0' => 'Inactive');
			    echo form_dropdown('status', $statuslist, $status, 'disabled="disabled" data-type = "custom-dropdown" ');
			    ?>
			</div>
		    </div>
		</div>
	    </li>
	</ul>

	<div class="form-btn-outer">
	    <button onclick="location.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'sites'; ?>'" class="btn btn-secondary reset-btn btn-submit" type="button"><?php echo lang('btn-back'); ?></button>
	</div>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function() {

	$("select[data-type='custom-dropdown-addmore']").dropkick({
	    mobile: true
	});


	var status = new Dropkick("#status");
	status.disable();

	var region = new Dropkick("#region");
	region.disable();

	var laundry_type = new Dropkick("#laundry_type");
	laundry_type.disable();

	var laundry_fuel_type = new Dropkick("#laundry_fuel_type");
	laundry_fuel_type.disable();

	var chilled_water_system_type = new Dropkick("#chilled_water_system_type");
	chilled_water_system_type.disable();

	var hotel = new Dropkick("#hotel");
	hotel.disable();

<?php
if (empty($is_stp)) {
    ?>$('.stp_content').hide();<?php
} else {
    ?>$('.stp_content').show();<?php
}
?>

<?php
if (empty($is_renewable_energy)) {
    ?>$('.renewable_energy_content').hide();<?php
} else {
    ?>$('.renewable_energy_content').show();<?php
}
?>

<?php
if (empty($is_ro_plant)) {
    ?>$('.ro_plant_content').hide();<?php
} else {
    ?>$('.ro_plant_content').show();<?php
}
?>

<?php
if (empty($is_chilled_water_system)) {
    ?>$('.chilled_water_content').hide();<?php
} else {
    ?>$('.chilled_water_content').show();<?php
}
?>

<?php
if (empty($is_split_dx_unit)) {
    ?>$('.split_content').hide();<?php
} else {
    ?>$('.split_content').show();<?php
}
?>

<?php
if (empty($is_vrv)) {
    ?>$('.vrv_content').hide();<?php
} else {
    ?>$('.vrv_content').show();<?php
}
?>

	});
</script>



