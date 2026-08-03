<style>
.checkbox-outer.right-check .icheckbox_square {
    left:unset;
}

.country_dropdown > ul.dk-select-options {
    max-height: 240px;
    overflow-y: scroll;
}
</style>
<?php echo add_css(array('spectrum')); ?>
<?php echo add_js(array('spectrum')); ?>
<script type="text/javascript">
    (function ($) {
	$.fn.maxlength = function () {
	    $("textarea[maxlength]").keypress(function (event) {
		var key = event.which;
		//all keys including return.
		if (key >= 33 || key == 13 || key == 32) {
		    var maxLength = $(this).attr("maxlength");
		    var length = this.value.length;
		    if (length >= maxLength) {
			event.preventDefault();
		    }
		}
	    });
	}

    })(jQuery);
    $(document).ready(function ($) {

	//Set maxlength of all the textarea (call plugin)
	$().maxlength();
    });
    $(function() {
     $("#datepicker").datepicker();
     $('.area-section').hide();
   });
    function jsFunction(value)
    {
	if(value == 1) {
	    $('.area-section').show();
	} else if (value == 2) {
	    $('.area-section').hide();
	    window.open('<?php echo base_url().BASE_ADMIN_URL_CUSTOM.'sites/view_area_history/'.$site_id;?>','_blank')
	} else {
	    $('.area-section').hide();
	    return false;
	}
    }
</script>
<?php
$is_site_logo_exists = 0;

$sites_type = $this->_ci->config->config['sites_type'];
$popupInfoArray = [
    'f_b' => 'Number of food and beverage venues or function spaces operated by Four Seasons',
    'f_b_outsourced' => 'Number of food and beverage venues or function spaces operated by a third-party (not Four Seasons)',
    'rental_program_suites' => 'Number of rental program residence rooms/suites (includes rooms managed by FourSeasons AND Condo Hotel units)',
    'rental_private_suites' => 'Number of Private Residence Rooms/Suites (includes Residence Club units, others NOT managed by Four Seasons)',
    'total_built_up'=>'Gross interior area, measured from the external perimeter wall surfaces, includes all areas inside the building shell, whether conditioned or unconditioned. This includes enclosed indoor parking but excludes outdoor parking',
    'cooled_built_up'=>'Hotel area that is conditioned by any heating, ventilation, air conditioning (HVAC) equipment.',
    'room_areas'=>'Hotel area that is conditioned by any heating, ventilation, air conditioning (HVAC) equipment.',
    'residence_common_area'=>'Common areas serving only rental program residences NOT accessible to hotel guests or meeting attendees',
    'private_residence_common_area'=>'Common areas serving only private residences NOT accessible to hotel guests or meeting attendees',
    'rental_built_up'=>'Gross interior area of the Rental Program spaces. See `Built Up Area` as defined for the Hotel Program.',
    'rental_conditioned'=>'Rental program property area that is conditioned by any heating, ventilations, air conditioning (HVAC) equipment',
    'private_built_up'=>'Gross interior area of the Private Residence spaces. See `Built Up Area` as defined for the Hotel Program.',
    'private_conditioned'=>'Private residence property area that is conditioned by any heating, ventilations, air conditioning (HVAC) equipment.',
    'employee_quarter'=>'Gross interior area in which employees live (including all employee-only amenities such as bedrooms, kitchens, living rooms, etc.). See `Built Up Area` as defined for the Hotel Program.',
    'meeting_area'=>'Gross interior area belonging to meeting spaces and pre-function spaces. See `Built Up Area` as defined for the Hotel Program.',
    'open_air'=>'Area serving restaurants which is out-of-doors, covered, or otherwise outside the confines of the built-up area, e.g. patios or entranceways',
    'outdoor_area'=>'Area of all planting, turf, and water features; does not include hardscape like built-up area, sidewalks, driveways, parking lots, decks, patios, gravel or stone walks, other permeable or impervious hardscapes, or other non-irrigated areas designated for non- development (e.g., open spaces and existing native vegetation)',
    'spa_area'=>'Area serving spas which is out-of-doors, covered, or otherwise outside the confines of the built-up area',
    'indoor_parking'=>'Area where cars park, as well as all supporting areas for parking (ticket booths, walkways); does not include stairwells to access parking',
    'f_b_service'=>'Built-up area belonging to food and beverage venues or function spaces',
    'laundry'=>'Choose: “On Site” where energy consumption includes on site washing of bedroom linens or more “Outsourced” where on site washing (if any) excludes bedroom linens. For example, linen wash of restaurant linens or guest clothing only.',
    'room_area_rental_program'=>'Includes Residential Rental Program rooms/suites and corridors',
    'room_area_private_residence'=>'Includes Private Residences rooms/suites and corridors',
]
?>

<article class="card">
    <div class="article-header">
	<?php echo ($site_id > 0) ? lang('edit-site') : lang('add-site'); ?>
	<?php echo ($site_id > 0) ? anchor(BASE_ADMIN_URL_CUSTOM . 'sites/waste/'.$site_id, 'Waste', 'class="btn btn-blue pull-right" style="padding:6px 12px;"') : ''; ?>
	<?php echo ($site_id > 0) ? anchor(BASE_ADMIN_URL_CUSTOM . 'sites/emission/'.$site_id, lang('emission'), 'class="btn btn-blue pull-right" style="margin-right:20px;padding:6px 12px;"') : ''; ?>
	<?php echo ($site_id > 0) && (in_array(RENTAL_PROGRAM_RESIDENCE, $residence_types) || in_array(PRIVATE_RESIDENCE, $residence_types)) ? anchor(BASE_ADMIN_URL_CUSTOM . 'sites/residence/'.$site_id, lang('residence'), 'class="btn btn-blue pull-right" style="margin-right:20px;padding:6px 12px;"') : ''; ?>
    </div>
    <div class="card-wrap">
	<?php echo form_open_multipart('', array('id' => 'saveform', 'name' => 'saveform', 'class' => 'site-info-form')); ?>

	<ul class="form-outer-block">
	    <li>
		<label class="main-label"><?php echo lang('upload-hotel-logo'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-12">
			<div class="form-col-10">
			    <div class="custom-file-upload">
				<input type="file" id="file" name="site_logo" multiple value="<?php echo $site_logo; ?>" />
			    </div>
			</div>
			<?php if (isset($site_logo) && $site_logo != '') { ?>
			    <?php
			    if (file_exists(BASE_PATH_CUSTOM . "/assets/uploads/" . $site_logo)) {
				$is_site_logo_exists = 1;
				?>
				<div class="form-col-2">
				    <img src='<?php echo site_url() . "assets/uploads/" . $site_logo; ?> '>
				</div>
			    <?php } else { ?>
				<div class="form-col-2">
				    <img class="siteImage" src='<?php echo site_url() . NOT_AVAILABLE_SITE_LOGO; ?> '>
				</div>
			    <?php } ?>
			<?php } ?>
			<!-- <label class="input-label">Upload Image</label> -->
		    </div>
		    <div class="form-col-12">
			<div class="form-col-10">
			    <label class="input-label validation_error"><?php echo form_error('site_logo'); ?></label>
			</div>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('change-hotel-theme'); ?> <span class="asterisk">*</span></label>
		<!-- <div class="row">
		    <div class="form-col-1">
		<?php
		$site_color_data = array(
		    'type' => 'hidden',
		    'name' => 'site_color',
		    'id' => 'site_color',
		    'value' => set_value('site_color', ((!empty($site_color)) ? htmlspecialchars_decode($site_color) : '#397A3E')),
		    'class' => 'input-control'
		);
		?>
		<?php echo form_input($site_color_data); ?>
			<div id="selectedcolorblock" style="border:1px dotted;width: 100%; float: left; display: inline-block; height: 40px;<?php echo (!empty($site_color)) ? 'background-color:' . $site_color . ';' : 'background-color:#397A3E' ?>"></div>
		    </div>
		    <div class="form-col-1">
			<button class="btn-control addition" id="color-picker">
			    <img src="images/color-picker.png" alt="ColorPicker">
			</button>
		    </div>
		    <label class="input-label validation_error"><?php echo form_error('site_color'); ?></label>
		</div> -->
		<div class="row">
		    <div class="form-col-12">
			<?php
			$site_color_data = array(
			    'name' => 'site_color',
			    'id' => 'color-picker',
			    'value' => set_value('site_color', (!empty($site_color) ? htmlspecialchars_decode($site_color) : '#397A3E')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($site_color_data); ?><span class="validation_error"><?php echo form_error('site_color'); ?></span>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('sites-type'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-4">
			<div class="form-dropdown">
			    <?php
			    echo form_dropdown('site_type', $sites_type, $site_type, 'data-type = "custom-dropdown" id="site_type"');
			    ?><span class="validation_error type-error"><?php echo form_error('site_type'); ?></span>
			</div>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('location'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$site_location_name_data = array(
			    'name' => 'site_location_name',
			    'id' => 'site_location_name',
			    'value' => set_value('site_location_name', ((isset($site_location_name)) ? htmlspecialchars_decode($site_location_name) : '')),
			    'class' => 'input-control',
			    // 'maxlength' => 50
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
			    'maxlength' => 20
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
			    'maxlength' => 20
			);
			?>
			<?php echo form_input($site_location_longitude_data); ?><span class="validation_error"><?php echo form_error('site_location_longitude'); ?></span>
			<!-- <label class="input-label">Longitude</label> -->
			<?php echo form_label(lang('longitude'), 'longitude', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<?php
			$station_id_data = array(
			    'name' => 'station_id',
			    'id' => 'station_id',
			    'value' => set_value('station_id', ((isset($station_id)) ? htmlspecialchars_decode($station_id) : '')),
			    'class' => 'input-control',
			    'maxlength' => 20
			);
			?>
			<?php echo form_input($station_id_data); ?><span class="validation_error"><?php echo form_error('station_id'); ?></span>
			<?php echo form_label(lang('weather-station'), lang('weather-station'), ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<?php
			$base_cdd_data = array(
			    'name' => 'base_cdd_temprature',
			    'id' => 'base_cdd_temprature',
			    'value' => set_value('base_cdd_temprature', ((isset($base_cdd_temprature)) ? $base_cdd_temprature : '')),
			    'class' => 'input-control',
			    'maxlength' => 10
			);
			?>
			<?php echo form_input($base_cdd_data); ?><span class="validation_error"><?php echo form_error('base_cdd_temprature'); ?></span>
			<?php echo form_label('Base CDD temprature', 'Base CDD temprature', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<?php
			$base_hdd_data = array(
			    'name' => 'base_hdd_temprature',
			    'id' => 'base_hdd_temprature',
			    'value' => set_value('base_hdd_temprature', ((isset($base_hdd_temprature)) ? $base_hdd_temprature : '')),
			    'class' => 'input-control',
			    'maxlength' => 10
			);
			?>
			<?php echo form_input($base_hdd_data); ?><span class="validation_error"><?php echo form_error('base_hdd_temprature'); ?></span>
			<?php echo form_label('Base HDD temprature', 'Base HDD temprature', ["class" => "input-label"]); ?>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('attribute'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$attribute_data = array(
			    'name' => 'attribute',
			    'id' => 'attribute',
			    'value' => set_value('attribute', ((isset($attribute)) ? htmlspecialchars_decode($attribute) : '')),
			    'class' => 'input-control',
			    'maxlength' => 3
			);
			?>
			<?php echo form_input($attribute_data); ?><span class="validation_error"><?php echo form_error('attribute'); ?></span>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('residences-attribute'); ?> </label>
		    <div class="form-col-3">
			<?php
			$residences_attribute_data = array(
			    'name' => 'residences_attribute',
			    'id' => 'residences_attribute',
			    'value' => set_value('residences_attribute', ((isset($residences_attribute)) ? htmlspecialchars_decode($residences_attribute) : '')),
			    'class' => 'input-control',
			    'maxlength' => 3
			);
			?>
			<?php echo form_input($residences_attribute_data); ?><span class="validation_error"><?php echo form_error('residences_attribute'); ?></span>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-program-attribute'); ?></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_program_data = array(
			    'name' => 'rental_program_attribute',
			    'id' => 'rental_program_attribute',
			    'value' => set_value('rental_program_attribute', ((isset($rental_program_attribute)) ? htmlspecialchars_decode($rental_program_attribute) : '')),
			    'class' => 'input-control',
			    // 'maxlength' => 5
			);
			?>
			<?php echo form_input($rental_program_data); ?><span class="validation_error"><?php echo form_error('rental_program_attribute'); ?></span>
		    </div>
		    <!-- <label class="main-label col-sm-4 rightLabel"><?php echo lang('employee-quarter-attribute'); ?></label> -->
		    <!-- <div class="form-col-3">
			<?php
			$employee_quarter_data = array(
			    'name' => 'employee_quarter_attribute',
			    'id' => 'employee_quarter_attribute',
			    'value' => set_value('employee_quarter_attribute', ((isset($employee_quarter_attribute)) ? htmlspecialchars_decode($employee_quarter_attribute) : '')),
			    'class' => 'input-control',
			    // 'maxlength' => 5
			);
			?>
			<?php echo form_input($employee_quarter_data); ?><span class="validation_error"><?php echo form_error('employee_quarter_attribute'); ?></span>
		    </div> -->
		</div>
	    </li>
	    <!-- <li>
		<label class="main-label"><?php echo lang('hotel'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-4">
			<div class="form-dropdown">
			    <?php
			    // echo form_dropdown('hotel_id', $hotel_list, $hotel_id, 'data-type = "custom-dropdown" id="hotel_id"');
			    ?><span class="validation_error hotel-error"><?php echo form_error('hotel_id'); ?></span>
			</div>
		    </div>
		</div>
	    </li> -->
	    <li>
		<label class="main-label"><?php echo lang('region'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $region_list_defualt = array('' => 'Select Region');
			    $region_list = $region_list_defualt + $region_list;
			    echo form_dropdown('region_id', $region_list, $region_id, 'data-type = "custom-dropdown" id="region_id"');
			    ?><span class="validation_error region-error"><?php echo form_error('region_id'); ?></span>
			</div>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('country'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $country_list_defualt = array('' => 'Select Country');
			    $country_list = $country_list_defualt + $country_list;
			    echo form_dropdown('country_id', $country_list, $country_id, 'data-type = "custom-dropdown" id="country_id" class="country_dropdown"');
			    ?><span class="validation_error region-error"><?php echo form_error('country_id'); ?></span>
			</div>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('local_currency'); ?> </label>
		<div class="row">
		    <div class="form-col-3">
			<input type="text" name="local_currency" placeholder="Enter Local Currency" class='input-control' value="<?php echo $local_currency; ?>">
		    </div>
		    <?php echo form_label(lang('local_unit'), lang('local_unit'), ["class" => "main-label col-sm-4 rightLabel"]); ?>
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $list = [
				'm&#178;',
				'ft&#178;'
			    ];
			    $name = 'local_unit';
			    $value = $local_unit;
			    echo form_dropdown($name, $list, $value, 'data-type = "custom-dropdown" ');
			    ?>
			</div>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('year-built'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$site_year_built_data = array(
			    'name' => 'site_year_built',
			    'id' => 'site_year_built',
			    'value' => set_value('site_year_built', ((isset($site_year_built)) ? htmlspecialchars_decode($site_year_built) : '')),
			    'class' => 'input-control',
			    'maxlength' => 4
			);
			?>
			<?php echo form_input($site_year_built_data); ?><span class="validation_error"><?php echo form_error('site_year_built'); ?></span>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('room-keys'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$rooms_keys = array(
			    'name' => 'rooms_keys',
			    'id' => 'rooms_keys',
							'value' => ((isset($rooms_keys)) ? htmlspecialchars_decode($rooms_keys) : ''),
			    'class' => 'input-control',
			    'maxlength' => 3,
			    'disabled' => 'disabled'
			);
			?>
			<?php echo form_input($rooms_keys); ?><span class="validation_error"><?php echo form_error('rooms_keys'); ?></span>
		    </div>
		</div>
	    </li>
	    <br/>
	    <li style="padding-left:10px;">
		<div class="row">
		    <label class="checkbox-outer right-check col-sm-3">
			<span class="col-sm-8"><?php echo lang('rental-program'); ?></span>
			<input name='residence_types[]' class='icheck' value='<?php echo RENTAL_PROGRAM_RESIDENCE;?>' type='checkbox' <?php echo (in_array(RENTAL_PROGRAM_RESIDENCE, $residence_types)) ? 'checked' : ''; ?>>
		    </label>
		    <label class="checkbox-outer right-check col-sm-3">
			<span class="col-sm-8"><?php echo lang('rental-private'); ?></span>
			<input name='residence_types[]' class='icheck' value='<?php echo PRIVATE_RESIDENCE;?>' type='checkbox' <?php echo (in_array(PRIVATE_RESIDENCE, $residence_types)) ? 'checked' : ''; ?>>
		    </label>
		    <label class="checkbox-outer right-check col-sm-3">
			<span class="col-sm-8"><?php echo lang('employee-living-quarters-area'); ?></span>
			<input name='residence_types[]' class='icheck' value='<?php echo EMPLOYEE_LIVING_QUARTERS;?>' type='checkbox' <?php echo (in_array(EMPLOYEE_LIVING_QUARTERS, $residence_types)) ? 'checked' : ''; ?>>
		    </label>
		    <label class="checkbox-outer right-check col-sm-3">
			<span class="col-sm-8"><?php echo lang('employee-living-quarters-area-offsite'); ?></span>
			<input name='residence_types[]' class='icheck' value='<?php echo EMPLOYEE_LIVING_QUARTERS_OFFSITE;?>' type='checkbox' <?php echo (in_array(EMPLOYEE_LIVING_QUARTERS_OFFSITE, $residence_types)) ? 'checked' : ''; ?>>
		    </label>
		</div>
	    </li>
	    <br/>
	    <li>
		<label class="main-label"><?php echo lang('f-b-services-operated'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['f_b'];?>" data-original-title="<?php echo $popupInfoArray['f_b'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$f_b_services_operated = array(
			    'name' => 'f_b_services_operated',
			    'id' => 'f_b_services_operated',
			    'value' => set_value('f_b_services_operated', ((isset($f_b_services_operated)) ? htmlspecialchars_decode($f_b_services_operated) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($f_b_services_operated); ?><span class="validation_error"><?php echo form_error('f_b_services_operated'); ?></span>
			<label class="input-label"></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('f-b-services-outsourced'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['f_b_outsourced'];?>" data-original-title="<?php echo $popupInfoArray['f_b_outsourced'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$f_b_services_outsourced = array(
			    'name' => 'f_b_services_outsourced',
			    'id' => 'f_b_services_outsourced',
			    'value' => set_value('f_b_services_outsourced', ((isset($f_b_services_outsourced)) ? htmlspecialchars_decode($f_b_services_outsourced) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($f_b_services_outsourced); ?><span class="validation_error"><?php echo form_error('f_b_services_outsourced'); ?></span>
			<label class="input-label"></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('rental-program-residence-suites'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['rental_program_suites'];?>" data-original-title="<?php echo $popupInfoArray['rental_program_suites'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$rental_program_residence_suites = array(
			    'name' => 'rental_program_residence_suites',
			    'id' => 'rental_program_residence_suites',
							'value' => ((isset($rental_program_residence_suites)) ? htmlspecialchars_decode($rental_program_residence_suites) : ''),
			    'class' => 'input-control',
			    // 'maxlength' => 5
			);
			?>
			<?php echo form_input($rental_program_residence_suites); ?><span class="validation_error"><?php echo form_error('rental_program_residence_suites'); ?></span>
			<label class="input-label"></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('rental-private-residence-suites'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['rental_private_suites'];?>" data-original-title="<?php echo $popupInfoArray['rental_private_suites'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$rental_private_residence_suites = array(
			    'name' => 'rental_private_residence_suites',
			    'id' => 'rental_private_residence_suites',
							'value' => ((isset($rental_private_residence_suites)) ? htmlspecialchars_decode($rental_private_residence_suites) : ''),
			    'class' => 'input-control',
			    // 'maxlength' => 5
			);
			?>
			<?php echo form_input($rental_private_residence_suites); ?><span class="validation_error"><?php echo form_error('rental_private_residence_suites'); ?></span>
			<label class="input-label"></label>
		    </div>

		</div>
	    </li>
	    <li style="padding-left:10px;">
		<div class="row">
		    <label class="form-control-block col-sm-2" style="font-weight: 500;"><?php echo lang('month-year-operation'); ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['f_b'];?>" data-original-title="<?php echo $popupInfoArray['f_b'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="col-sm-2">
			<?php
			$month_year_operation = array(
			    'name' => 'month_year_operation',
			    'id' => 'month_year_operation',
			    'value' => set_value('month_year_operation', ((isset($month_year_operation)) ? htmlspecialchars_decode($month_year_operation) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($month_year_operation); ?><span class="validation_error"><?php echo form_error('month_year_operation'); ?></span>
			<label class="input-label"></label>
		    </div>
		    <label class="main-label col-sm-2 rightLabel"><?php echo lang('vehicle-electric'); ?><span class="asterisk">*</span></label>
		    <div class="col-sm-2">
			<?php
			$vehicle_electric = array(
			    'name' => 'vehicle_electric',
			    'id' => 'vehicle_electric',
			    'value' => set_value('vehicle_electric', ((isset($vehicle_electric)) ? htmlspecialchars_decode($vehicle_electric) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($vehicle_electric); ?><span class="validation_error"><?php echo form_error('vehicle_electric'); ?></span>
			<label class="input-label"></label>
		    </div>
		    <label class="main-label col-sm-2 rightLabel"><?php echo lang('vehicle-petrol'); ?> <span class="asterisk">*</span></label>
		    <div class="col-sm-2">
			<?php
			$vehicle_petrol = array(
			    'name' => 'vehicle_petrol',
			    'id' => 'vehicle_petrol',
			    'value' => set_value('vehicle_petrol', ((isset($vehicle_petrol)) ? htmlspecialchars_decode($vehicle_petrol) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($vehicle_petrol); ?><span class="validation_error"><?php echo form_error('vehicle_petrol'); ?></span>
			<label class="input-label"></label>
		    </div>
		</div>
	    </li>
	    <br/>
	    <div class="form-group-label form-outer-block">
		<div class="row add-row">
		    <div class="col-md-2">
			<h5><strong><?php echo "Areas"; ?></strong></h5>
		    </div>
		    <div class="col-md-3">
			<div class="form-dropdown">
			    <?php
			    $areaUpdateList = array('0' => 'None Selected', '1' => 'Update with new information', '2' => 'View update history');
			    echo form_dropdown('siteArea[area_update_type]', $areaUpdateList, $areaUpdate, 'data-type = "custom-dropdown" onchange="jsFunction(this.value);"');
			    ?>
			</div>
			<?php echo form_label('Value Update', 'siteArea[area_update_type]', ["class" => "input-label"]); ?>
		    </div>
		    <div class="area-section">
			<div class="col-md-3">
			    <div class="form-dropdown">
				<?php
				$areaUpdateField = array(
				    '0' => 'None Selected',
				    'site_builtup_area' => lang('total-built-up-area'),
				    'cooled_builtup_area' => lang('cooled-built-up-area'),
				    'rooms_keys' => lang('room-keys'),
				    'rental_program_residence' => lang('rental-program-residence'),
				    'rental_program_residence_conditioned' => lang('rental-program-residence-conditioned'),
				    'rental_private_residence' => lang('rental-private-residence'),
				    'rental_private_residence_conditioned' => lang('rental-private-residence-conditioned'),
				    'rental_program_residence_suites' => lang('rental-program-residence-suites'),
				    'rental_private_residence_suites' => lang('rental-private-residence-suites'),
				);
				echo form_dropdown('siteArea[area_update_field]', $areaUpdateField, $areaUpdate, 'data-type = "custom-dropdown" ');
				?>
			    </div>
			</div>
			<div class="col-md-2">
			    <input type="text" placeholder="Choose Date" id="datepicker" name="siteArea[area_update_date]" value="" class='input-control'/>
			</div>
			<div class="col-md-2">
			    <input type="number" step=".01" name="siteArea[area_update_value]" value="" class='input-control'/>
			</div>
		    </div>
		</div>
	    </div>

	    <ul class="form-outer-block">
		<li>
		    <label class="main-label"><?php echo lang('total-built-up-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['total_built_up'];?>" data-original-title="<?php echo $popupInfoArray['total_built_up'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $site_builtup_area_disabled = array(
								'value' => (isset($site_builtup_area) ? $site_builtup_area : ''),
				'class' => 'input-control floatcheck',
				'id' => 'site_builtup_area_disabled',
				'disabled' => 'disabled'
			    );
			    ?>
			    <input type="hidden" id="site_builtup_area" name="site_builtup_area" value="<?php echo isset($site_builtup_area) ? htmlspecialchars_decode($site_builtup_area) : 0;?>">
			    <?php echo form_input($site_builtup_area_disabled); ?><span class="validation_error"><?php echo form_error('site_builtup_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('cooled-built-up-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['cooled_built_up'];?>" data-original-title="<?php echo $popupInfoArray['cooled_built_up'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
			<div class="form-col-3">
			    <?php
			    $cooled_builtup_area = array(
				'name' => 'cooled_builtup_area',
				'id' => 'cooled_builtup_area',
								'value' => ((isset($cooled_builtup_area)) ? htmlspecialchars_decode($cooled_builtup_area) : 0),
				'class' => 'input-control floatcheck',
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($cooled_builtup_area); ?><span class="validation_error"><?php echo form_error('cooled_builtup_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('hotel-rooms-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['room_areas'];?>" data-original-title="<?php echo $popupInfoArray['room_areas'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $hotel_rooms_area = array(
				'name' => 'hotel_rooms_area',
				'id' => 'hotel_rooms_area',
				'value' => set_value('hotel_rooms_area', ((isset($hotel_rooms_area)) ? htmlspecialchars_decode($hotel_rooms_area) : '')),
				'class' => 'input-control floatcheck',
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($hotel_rooms_area); ?><span class="validation_error"><?php echo form_error('hotel_rooms_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('residential-common-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['residence_common_area'];?>" data-original-title="<?php echo $popupInfoArray['residence_common_area'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
			<div class="form-col-3">
			    <?php
			    $residential_common_area = array(
				'name' => 'residential_common_area',
				'id' => 'residential_common_area',
				'value' => set_value('residential_common_area', ((isset($residential_common_area)) ? htmlspecialchars_decode($residential_common_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($residential_common_area); ?><span class="validation_error"><?php echo form_error('residential_common_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('rental-program-residence').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['rental_built_up'];?>" data-original-title="<?php echo $popupInfoArray['rental_built_up'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $rental_program_residence = array(
				'name' => 'rental_program_residence',
				'id' => 'rental_program_residence',
								'value' => ((isset($rental_program_residence)) ? htmlspecialchars_decode($rental_program_residence) : ''),
				'class' => 'input-control',
				// 'maxlength' => 5,
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($rental_program_residence); ?><span class="validation_error"><?php echo form_error('rental_program_residence'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('rental-program-residence-conditioned').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['rental_conditioned'];?>" data-original-title="<?php echo $popupInfoArray['rental_conditioned'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $rental_program_residence_conditioned = array(
				'name' => 'rental_program_residence_conditioned',
				'id' => 'rental_program_residence_conditioned',
								'value' => ((isset($rental_program_residence_conditioned)) ? htmlspecialchars_decode($rental_program_residence_conditioned) : ''),
				'class' => 'input-control',
				// 'maxlength' => 5,
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($rental_program_residence_conditioned); ?><span class="validation_error"><?php echo form_error('rental_program_residence_conditioned'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('rental-private-residence').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['private_built_up'];?>" data-original-title="<?php echo $popupInfoArray['private_built_up'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $rental_private_residence = array(
				'name' => 'rental_private_residence',
				'id' => 'rental_private_residence',
								'value' => ((isset($rental_private_residence)) ? htmlspecialchars_decode($rental_private_residence) : ''),
				'class' => 'input-control',
				// 'maxlength' => 5,
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($rental_private_residence); ?><span class="validation_error"><?php echo form_error('rental_private_residence'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('rental-private-residence-conditioned').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['private_conditioned'];?>" data-original-title="<?php echo $popupInfoArray['private_conditioned'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $rental_private_residence_conditioned = array(
				'name' => 'rental_private_residence_conditioned',
				'id' => 'rental_private_residence_conditioned',
								'value' => ((isset($rental_private_residence_conditioned)) ? htmlspecialchars_decode($rental_private_residence_conditioned) : ''),
				'class' => 'input-control',
				// 'maxlength' => 5,
				'style' => 'cursor: not-allowed !important;pointer-events: none !important;'
			    );
			    ?>
			    <?php echo form_input($rental_private_residence_conditioned); ?><span class="validation_error"><?php echo form_error('rental_private_residence_conditioned'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('employee-living-quarters-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['employee_quarter'];?>" data-original-title="<?php echo $popupInfoArray['employee_quarter'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $employee_living_quarters_area = array(
				'name' => 'employee_living_quarters_area',
				'id' => 'employee_living_quarters_area',
				'value' => set_value('employee_living_quarters_area', ((isset($employee_living_quarters_area)) ? htmlspecialchars_decode($employee_living_quarters_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($employee_living_quarters_area); ?><span class="validation_error"><?php echo form_error('employee_living_quarters_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('total-meeting-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['meeting_area'];?>" data-original-title="<?php echo $popupInfoArray['meeting_area'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $total_meeting_area = array(
				'name' => 'total_meeting_area',
				'id' => 'total_meeting_area',
				'value' => set_value('total_meeting_area', ((isset($total_meeting_area)) ? htmlspecialchars_decode($total_meeting_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($total_meeting_area); ?><span class="validation_error"><?php echo form_error('total_meeting_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('restaurant-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['open_air'];?>" data-original-title="<?php echo $popupInfoArray['open_air'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $restaurant_area = array(
				'name' => 'restaurant_area',
				'id' => 'restaurant_area',
				'value' => set_value('restaurant_area', ((isset($restaurant_area)) ? htmlspecialchars_decode($restaurant_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($restaurant_area); ?><span class="validation_error"><?php echo form_error('restaurant_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('landscaped-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['outdoor_area'];?>" data-original-title="<?php echo $popupInfoArray['outdoor_area'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $landscaped_area = array(
				'name' => 'landscaped_area',
				'id' => 'landscaped_area',
				'value' => set_value('landscaped_area', ((isset($landscaped_area)) ? htmlspecialchars_decode($landscaped_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($landscaped_area); ?><span class="validation_error"><?php echo form_error('landscaped_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('spa-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['spa_area'];?>" data-original-title="<?php echo $popupInfoArray['spa_area'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $total_spa_area = array(
				'name' => 'total_spa_area',
				'id' => 'total_spa_area',
				'value' => set_value('total_spa_area', ((isset($total_spa_area)) ? htmlspecialchars_decode($total_spa_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($total_spa_area); ?><span class="validation_error"><?php echo form_error('total_spa_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<!-- <label class="main-label col-sm-4 rightLabel"><?php echo lang('guest-room-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="Info" data-original-title="Info"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $total_guest_room_area = array(
				'name' => 'total_guest_room_area',
				'id' => 'total_guest_room_area',
				'value' => set_value('total_guest_room_area', ((isset($total_guest_room_area)) ? htmlspecialchars_decode($total_guest_room_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($total_guest_room_area); ?><span class="validation_error"><?php echo form_error('total_guest_room_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div> -->
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('room-area-rental-program').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['room_area_rental_program'];?>" data-original-title="<?php echo $popupInfoArray['room_area_rental_program'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $room_area_rental_program = array(
				'name' => 'room_area_rental_program',
				'id' => 'room_area_rental_program',
				'value' => set_value('room_area_rental_program', ((isset($room_area_rental_program)) ? htmlspecialchars_decode($room_area_rental_program) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($room_area_rental_program); ?><span class="validation_error"><?php echo form_error('room_area_rental_program'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('room-area-private-residence').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['room_area_private_residence'];?>" data-original-title="<?php echo $popupInfoArray['room_area_private_residence'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $room_area_private_residence = array(
				'name' => 'room_area_private_residence',
				'id' => 'room_area_private_residence',
				'value' => set_value('room_area_private_residence', ((isset($room_area_private_residence)) ? htmlspecialchars_decode($room_area_private_residence) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($room_area_private_residence); ?><span class="validation_error"><?php echo form_error('room_area_private_residence'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<li>
		    <label class="main-label"><?php echo lang('indoor-parking-area').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['indoor_parking'];?>" data-original-title="<?php echo $popupInfoArray['indoor_parking'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $indoor_parking_area = array(
				'name' => 'indoor_parking_area',
				'id' => 'indoor_parking_area',
				'value' => set_value('indoor_parking_area', ((isset($indoor_parking_area)) ? htmlspecialchars_decode($indoor_parking_area) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($indoor_parking_area); ?><span class="validation_error"><?php echo form_error('indoor_parking_area'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('f-b-service').'('.getLocalUnitText($site_id).')'; ?><a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['f_b_service'];?>" data-original-title="<?php echo $popupInfoArray['f_b_service'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $f_b_service = array(
				'name' => 'f_b_service',
				'id' => 'f_b_service',
				'value' => set_value('f_b_service', ((isset($f_b_service)) ? htmlspecialchars_decode($f_b_service) : '')),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($f_b_service); ?><span class="validation_error"><?php echo form_error('f_b_service'); ?></span>
			    <label class="input-label"><?php echo getLocalUnitText($site_id); ?></label>
			</div>
		    </div>
		</li>
		<hr/>
		<li>
		    <label class="main-label"><?php echo lang('outdoor-pools'); ?> <span class="asterisk">*</span></label>
		    <div class="row">
			<div class="form-col-3">
			    <?php
			    $outdoor_pools = array(
				'name' => 'outdoor_pools',
				'id' => 'outdoor_pools',
				'value' => set_value('outdoor_pools', ((isset($outdoor_pools)) ? htmlspecialchars_decode($outdoor_pools) : '')),
				'class' => 'input-control',
				// 'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($outdoor_pools); ?><span class="validation_error"><?php echo form_error('outdoor_pools'); ?></span>
			    <label class="input-label"><?php echo lang('m3'); ?></label>
			</div>
			<label class="main-label col-sm-4 rightLabel"><?php echo lang('indoor-pools'); ?> <span class="asterisk">*</span></label>
			<div class="form-col-3">
			    <?php
			    $indoor_pools = array(
				'name' => 'indoor_pools',
				'id' => 'indoor_pools',
				'value' => set_value('indoor_pools', ((isset($indoor_pools)) ? htmlspecialchars_decode($indoor_pools) : '')),
				'class' => 'input-control',
				// 'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($indoor_pools); ?><span class="validation_error"><?php echo form_error('indoor_pools'); ?></span>
			    <label class="input-label"><?php echo lang('m3'); ?></label>
			</div>

		    </div>
		</li>
	    </ul>
	    <br/>
	    <li>
	    <label class="main-label"><?php echo lang('laundry'); ?> <a href="#" data-toggle="tooltip" data-container="article"  data-placement="right" title="<?php echo $popupInfoArray['laundry'];?>" data-original-title="<?php echo $popupInfoArray['laundry'];?>"><i class="fa fa-info-circle" aria-hidden="true"></i></a></label>
		<div class="row">
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $laundry_type_list = array('1' => 'Outsourced', '0' => 'On Site');
			    echo form_dropdown('laundry_type', $laundry_type_list, $laundry_type, 'data-type = "custom-dropdown" ');
			    ?>
			</div>
			<?php echo form_label(lang('onsite-outsourced'), 'onsite_outsourced', ["class" => "input-label"]); ?>
		    </div>
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    // $laundry_fuel_type_list = array('1' => 'Steam', '2' => 'Electricity', '3' => 'Gas');
			    $laundry_fuel_type_list = array('Steam' => 'Steam', 'Electricity' => 'Electricity', 'Gas' => 'Gas');
			    echo form_dropdown('laundry_fuel_type', $laundry_fuel_type_list, $laundry_fuel_type, 'data-type = "custom-dropdown" ');
			    ?>
			</div>
			<?php echo form_label(lang('steam-electricity-gas'), 'onsite_outsourced', ["class" => "input-label"]); ?>
		    </div>
		</div>
	    </li>

	    <li>
		<label class="main-label"><?php echo lang('substation-rating'); ?> <span class="asterisk">*</span></label>
		<?php
		//echo form_label(lang('substation-rating'), 'substation_rating', ["class" => "main-label"]);
		if (!empty($substations)) {
		    foreach ($substations as $key => $substation) {
			?>
			<div class="row add-row">
			    <div class="form-col-3">
				<?php
				$substation_quantity = array(
				    'name' => 'substation[substation_quantity][]',
				    'id' => 'substation_quantity',
				    'value' => $substation['substation_quantity'],
				    'class' => 'input-control',
				    // 'maxlength' => 5
				);
				?>
				<input name="substation[substation_id][]" value="<?php echo $substation['id']; ?>" type="hidden" />
				<?php echo form_input($substation_quantity); ?><span class="validation_error"><?php echo form_error('substation[substation_quantity][]'); ?></span>
				<?php echo form_label(lang('substation-quantity'), 'substation_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-3 form-col-add">
				<?php
				$substation_power = array(
				    'name' => 'substation[substation_power][]',
				    'id' => 'substation_power',
				    'value' => $substation['substation_power'],
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($substation_power); ?><span class="validation_error"><?php echo form_error('substation[substation_power][]'); ?></span>
				<?php echo form_label(lang('substation-power'), 'substation_power', ["class" => "input-label"]); ?>
			    </div>
			    <?php
			    if ($key == 0) {
				?>
				<div class="form-col-1">
				    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='substation[substation_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='substation[substation_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='substation[substation_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
				</div>
				<?php
			    } else {
				?>
				<div class="form-col-1">
				    <button class="btn-control substract" type="button"><img alt="Minus" src="images/minus-icon.png"></button>
				</div>
				<?php
			    }
			    ?>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-3">
			    <?php
			    $substation_quantity = array(
				'name' => 'substation[substation_quantity][]',
				'id' => 'substation_quantity',
				'value' => set_value('substation[substation_quantity][]'),
				'class' => 'input-control',
				// 'maxlength' => 5
			    );
			    ?>
			    <input name="substation[substation_id][]" value="<?php echo $substation['id']; ?>" type="hidden" />
			    <?php echo form_input($substation_quantity); ?><span class="validation_error"><?php echo form_error('substation[substation_quantity][]'); ?></span>
			    <?php echo form_label(lang('substation-quantity'), 'substation_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-3 form-col-add">
			    <?php
			    $substation_power = array(
				'name' => 'substation[substation_power][]',
				'id' => 'substation_power',
				'value' => set_value('substation[substation_power][]'),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($substation_power); ?><span class="validation_error"><?php echo form_error('substation[substation_power][]'); ?></span>
			    <?php echo form_label(lang('substation-power'), 'substation_power', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-1">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='substation[substation_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='substation[substation_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='substation[substation_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>
		    </div>
		<?php } ?>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('onsite-generators'); ?> <span class="asterisk">*</span></label>
		<?php
		if (!empty($generators)) {
		    foreach ($generators as $key => $generator) {
			?>
			<div class="row add-row">
			    <div class="form-col-2 form-col-add">
				<?php
				$generators_name = array(
				    'name' => 'generator[generator_name][]',
				    'id' => 'generators_name',
				    'value' => $generator['generator_name'],
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
				    // 'maxlength' => 5
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
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($generators_power); ?><span class="validation_error"><?php echo form_error('generator[generator_power][]'); ?></span>
				<?php echo form_label(lang('generators-power'), 'generators_power', ["class" => "input-label"]); ?>
			    </div>

			    <?php
			    if ($key == 0) {
				?>
				<div class="form-col-1">
				    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><div class='form-col-2 form-col-add'><input name='generator[generator_name][]' type='text' class='input-control'><input name='generator[generator_id][]' value='0' type='hidden' /></div><div class='form-col-2'><input name='generator[generator_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-2'><input name='generator[generator_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
				</div>
				<?php
			    } else {
				?>
				<div class="form-col-1">
				    <button class="btn-control substract" type="button"><img alt="Minus" src="images/minus-icon.png"></button>
				</div>
				<?php
			    }
			    ?>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-2 form-col-add">
			    <?php
			    $generators_name = array(
				'name' => 'generator[generator_name][]',
				'id' => 'generators_name',
				'value' => set_value('generator[generator_name][]'),
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
				// 'maxlength' => 5
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
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($generators_power); ?><span class="validation_error"><?php echo form_error('generator[generator_power][]'); ?></span>
			    <?php echo form_label(lang('generators-power'), 'generators_power', ["class" => "input-label"]); ?>
			</div>

			<div class="form-col-1">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><div class='form-col-2 form-col-add'><input name='generator[generator_name][]' type='text' class='input-control'><input name='generator[generator_id][]' value='0' type='hidden' /></div><div class='form-col-2'><input name='generator[generator_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-2'><input name='generator[generator_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>
		    </div>
		<?php } ?>
	    </li>

	    <li>
		<?php echo form_label(lang('chilled-water-system'), 'chilled_water_system', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<?php //pre($is_chilled_water_system);    ?>
			<label class="radio-outer"><input type="radio" class="icheck chilled_water_radio" name="is_chilled_water_system" <?php
			    if (isset($is_chilled_water_system) && $is_chilled_water_system == 1) {
				echo 'checked="checked"';
			    }
			    ?> value="1">Yes</label>
			<label><input type="radio" class="icheck chilled_water_radio"  <?php
			    if (!isset($is_chilled_water_system) || $is_chilled_water_system == 0) {
				echo 'checked="checked"';
			    }
			    ?> name="is_chilled_water_system" value="0">No</label>
		    </div>
		    <div class="form-col-3 chilled_water_content">
			<?php if (isset($is_chilled_water_system) && $is_chilled_water_system == 1 && isset($chilled_water_system_type) && $chilled_water_system_type != '' && isset($chilled_water_system_total_rate) && $chilled_water_system_total_rate != '') { ?>
			    <div class="form-dropdown">
				<?php
				$chilled_water_system_type_list = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled', 'District Cooling' => 'District Cooling');
				echo form_dropdown('chilled_water_system_type', $chilled_water_system_type_list, $chilled_water_system_type, 'data-type = "custom-dropdown" ');
				?>
			    </div>
			    <?php echo form_label(lang('air-cooled-water-cooled'), 'air_cooled_water_cooled', ["class" => "input-label"]); ?>
			<?php } else { ?>
			    <div class="form-dropdown">
				<?php
				$chilled_water_system_type_list = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled', 'District Cooling' => 'District Cooling');
				echo form_dropdown('chilled_water_system_type', $chilled_water_system_type_list, '', 'data-type = "custom-dropdown" ');
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
				'class' => 'input-control decimalcheck'
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
				'class' => 'input-control decimalcheck');
			    ?>
			    <?php echo form_input($chilled_water_system_total_rate); ?><span class="validation_error"><?php echo form_error('chilled_water_system_total_rate'); ?></span>
			    <?php
			    echo form_label(lang('chilled-water-system-total-rate'), 'total_rt', ["class" => "input-label"]);
			}
			?>
		    </div>

		    <?php
		    $system2exists = false;

		    if (!empty($chilled_water_system_total_rate2)) {
			$system2exists = true;
		    }


		    $chilled_water_system_type_list_addmore = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled', 'District Cooling' => 'District Cooling');
		    $dropdown = form_dropdown('chilled_water_system_type2', $chilled_water_system_type_list_addmore, '', 'id="chilled_water_system_type2" data-type = "custom-dropdown-addmore" ');
		    ?>
		    <div class="form-col-1 chilled_water_content">
			<button type="button" class="btn-control addition additiondropdown" <?php echo ($system2exists) ? 'style="display:none;"' : ''; ?> data-row="<div class='row add-row chilled_water_content'><div class='form-col-2 form-control-block'></div><div class='form-col-3'><div class='form-dropdown'><?php echo str_replace('"', "'", $dropdown); ?></div></div><div class='form-col-2'><input id='chilled_water_system_total_rate2' name='chilled_water_system_total_rate2' type='text' class='input-control decimalcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract substracttoggle'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
		    </div>
		</div>

		<?php if ($system2exists) { ?>
		    <?php
		    $chilled_water_system_type_list_addmore = array('Air Cooled' => 'Air Cooled', 'Water Cooled' => 'Water Cooled', 'District Cooling' => 'District Cooling');
		    $dropdown = form_dropdown('chilled_water_system_type2', $chilled_water_system_type_list_addmore, $chilled_water_system_type2, 'id="chilled_water_system_type2" data-type = "custom-dropdown-addmore" ');
		    ?>
		    <div class='row add-row chilled_water_content'><div class='form-col-2 form-control-block'></div><div class='form-col-3'><div class="form-dropdown"><?php echo str_replace('"', "'", $dropdown); ?></div></div><div class='form-col-2'><input id='chilled_water_system_total_rate2' name='chilled_water_system_total_rate2' type='text' class='input-control decimalcheck' value="<?php echo $chilled_water_system_total_rate2; ?>"></div><div class='form-col-1'><button type="button" class='btn-control substract substracttoggle'><img src='images/minus-icon.png' alt='Minus'></button></div>
		    <?php } ?>
	    </li>
	    <li>
		<?php echo form_label(lang('split-dx-units'), 'split_dx_units', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" class="icheck split_radio" <?php
			    if (isset($is_split_dx_unit) && $is_split_dx_unit == 1) {
				echo 'checked="checked"';
			    }
			    ?> name="is_split_dx_unit" value="1">Yes</label>
			<label><input type="radio" <?php
			    if (!isset($is_split_dx_unit) || $is_split_dx_unit == 0) {
				echo 'checked="checked"';
			    }
			    ?> class="icheck split_radio" name="is_split_dx_unit" value="0">No</label>
		    </div>
		    <div class="form-col-3 split_content">
			<?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1 && isset($total_split_dx_unit) && $total_split_dx_unit != '' && isset($total_rate_split_dx_unit) && $total_rate_split_dx_unit != '') { ?>
			    <?php
			    $total_split_dx_unit = array(
				'name' => 'total_split_dx_unit',
				'id' => 'total_split_dx_unit',
				'value' => set_value('total_split_dx_unit', ((isset($total_split_dx_unit)) ? htmlspecialchars_decode($total_split_dx_unit) : '')),
				'class' => 'input-control',
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
				'maxlength' => 3
			    );
			    ?>
			    <?php echo form_input($total_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_split_dx_unit'); ?></span>
			    <?php echo form_label(lang('total-rate-split-dx-unit'), 'total_split_dx_unit', ["class" => "input-label"]); ?>


			<?php } ?>
		    </div>
		    <div class="form-col-3 split_content">
			<?php if (isset($is_split_dx_unit) && $is_split_dx_unit == 1 && isset($total_split_dx_unit) && $total_split_dx_unit != '' && isset($total_rate_split_dx_unit) && $total_rate_split_dx_unit != '') { ?>
			    <?php
			    $total_rate_split_dx_unit = array(
				'name' => 'total_rate_split_dx_unit',
				'id' => 'total_rate_split_dx_unit',
				'value' => set_value('total_rate_split_dx_unit', ((isset($total_rate_split_dx_unit)) ? htmlspecialchars_decode($total_rate_split_dx_unit) : '')),
				'class' => 'input-control decimalcheck'
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
				'class' => 'input-control decimalcheck'
			    );
			    ?>
			    <?php echo form_input($total_rate_split_dx_unit); ?><span class="validation_error"><?php echo form_error('total_rate_split_dx_unit'); ?></span>
			    <?php echo form_label(lang('total-rt'), 'total_rt', ["class" => "input-label"]); ?>

			<?php } ?>
		    </div>

		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('vrv'), 'vrv', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-2 form-control-block">
			<label class="radio-outer"><input type="radio" <?php
			    if (isset($is_vrv) && $is_vrv == 1) {
				echo 'checked="checked"';
			    }
			    ?> class="icheck vrv_radio" name="is_vrv" value="1">Yes</label>
			<label><input type="radio" <?php
			    if (!isset($is_vrv) || $is_vrv == 0) {
				echo 'checked="checked"';
			    }
			    ?>  class="icheck vrv_radio" name="is_vrv" value="0">No</label>
		    </div>
		    <div class="form-col-3 vrv_content">
			<?php if (isset($is_vrv) && $is_vrv == 1 && isset($total_vrv) && $total_vrv != '') { ?>
			    <?php
			    $total_vrv_unit = array(
				'name' => 'total_vrv_unit',
				'id' => 'total_vrv_unit',
				'value' => set_value('total_vrv_unit', ((isset($total_vrv_unit)) ? htmlspecialchars_decode($total_vrv_unit) : '')),
				'class' => 'input-control',
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
				'maxlength' => 3
			    );
			    ?>
			    <?php echo form_input($total_vrv_unit); ?><span class="validation_error"><?php echo form_error('total_vrv_unit'); ?></span>
			    <?php echo form_label(lang('total-vrv-unit-unit'), 'total_vrv_unit', ["class" => "input-label"]); ?>


			<?php } ?>
		    </div>
		    <div class="form-col-3 vrv_content">
			<?php if (isset($is_vrv) && $is_vrv == 1 && isset($total_vrv) && $total_vrv != '') { ?>

			    <div class="form-dropdown">
				<?php
				$total_vrv = array(
				    'name' => 'total_vrv',
				    'id' => 'total_vrv',
				    'value' => set_value('total_vrv', ((isset($total_vrv)) ? htmlspecialchars_decode($total_vrv) : '')),
				    'class' => 'input-control',
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
				    'maxlength' => 3
				);
				?>
				<?php echo form_input($total_vrv); ?><span class="validation_error"><?php echo form_error('total_vrv'); ?></span>
			    </div>
			    <?php echo form_label(lang('total-vrv'), 'total_vrv', ["class" => "input-label"]); ?>
			<?php } ?>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('hot-water-boiler'); ?> <span class="asterisk">*</span></label>
		<?php
		if (!empty($hot_water_boilers)) {
		    foreach ($hot_water_boilers as $key => $hot_water_boiler) {
			?>
			<div class="row add-row">
			    <div class="form-col-3">
				<?php
				$hot_water_bolier_total_rate = array(
				    'name' => 'hot_water_boiler[hot_water_boiler_quantity][]',
				    'id' => 'hot_water_bolier_quantity',
				    'value' => $hot_water_boiler['hot_water_boiler_quantity'],
				    'class' => 'input-control',
				    // 'maxlength' => 5
				);
				?>
				<?php echo form_input($hot_water_bolier_total_rate); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_quantity][]'); ?></span>
				<?php echo form_label(lang('hot-water-bolier-quantity'), 'hot_water_bolier_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-3 form-col-add">
				<?php
				$hot_water_boiler_power = array(
				    'name' => 'hot_water_boiler[hot_water_boiler_power][]',
				    'id' => 'hot_water_boiler_power',
				    'value' => $hot_water_boiler['hot_water_boiler_power'],
				    'class' => 'input-control floatcheck'
				);
				?>
				<?php echo form_input($hot_water_boiler_power); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_power][]'); ?></span>

				<?php echo form_label(lang('hot-water-boiler-power'), 'hot_water_boiler_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='hot_water_boiler[hot_water_boiler_id][]' value='<?php echo $hot_water_boiler['id']; ?>' type='hidden' />
			    <?php
			    if ($key == 0) {
				?>
				<div class="form-col-1">
				    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='hot_water_boiler[hot_water_boiler_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='hot_water_boiler[hot_water_boiler_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='hot_water_boiler[hot_water_boiler_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
				</div>
				<?php
			    } else {
				?>
				<div class="form-col-1">
				    <button class="btn-control substract" type="button"><img alt="Minus" src="images/minus-icon.png"></button>
				</div>
				<?php
			    }
			    ?>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-3">
			    <?php
			    $hot_water_bolier_total_rate = array(
				'name' => 'hot_water_boiler[hot_water_boiler_quantity][]',
				'id' => 'hot_water_bolier_quantity',
				'value' => set_value('hot_water_boiler[hot_water_boiler_quantity][]'),
				'class' => 'input-control',
				// 'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($hot_water_bolier_total_rate); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_quantity][]'); ?></span>
			    <?php echo form_label(lang('hot-water-bolier-quantity'), 'hot_water_bolier_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-3 form-col-add">
			    <?php
			    $hot_water_boiler_power = array(
				'name' => 'hot_water_boiler[hot_water_boiler_power][]',
				'id' => 'hot_water_boiler_power',
				'value' => set_value('hot_water_boiler[hot_water_boiler_power][]'),
				'class' => 'input-control floatcheck'
			    );
			    ?>
			    <?php echo form_input($hot_water_boiler_power); ?><span class="validation_error"><?php echo form_error('hot_water_boiler[hot_water_boiler_power][]'); ?></span>

			    <?php echo form_label(lang('hot-water-boiler-power'), 'hot_water_boiler_power', ["class" => "input-label"]); ?>
			</div>
			<input name='hot_water_boiler[hot_water_boiler_id][]' value='0' type='hidden' />
			<div class="form-col-1">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='hot_water_boiler[hot_water_boiler_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='hot_water_boiler[hot_water_boiler_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='hot_water_boiler[hot_water_boiler_power][]' type='text' class='input-control floatcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>

		    </div>
		<?php } ?>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('calorifiers-label'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$calorifiers_unit = array(
			    'name' => 'calorifiers_unit',
			    'id' => 'calorifiers_unit',
			    'value' => set_value('calorifiers_unit', ((isset($calorifiers_unit)) ? htmlspecialchars_decode($calorifiers_unit) : '')),
			    'class' => 'input-control floatcheck'
			);
			?>
			<?php echo form_input($calorifiers_unit); ?><span class="validation_error"><?php echo form_error('calorifiers_unit'); ?></span>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('calorifiers-volume'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$calorifiers_volume = array(
			    'name' => 'calorifiers_volume',
			    'id' => 'calorifiers_volume',
			    'value' => set_value('calorifiers_volume', ((isset($calorifiers_volume)) ? htmlspecialchars_decode($calorifiers_volume) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($calorifiers_volume); ?><span class="validation_error"><?php echo form_error('calorifiers_volume'); ?></span>
		    </div>
		</div>
	    </li>

	    <li>
		<label class="main-label"><?php echo lang('steam-boiler'); ?> <span class="asterisk">*</span></label>
		<?php
		if (!empty($steam_boilers)) {
		    foreach ($steam_boilers as $key => $steam_boiler) {
			?>
			<div class="row add-row">
			    <div class="form-col-3">
				<?php
				$steam_bolier_quantity_total_rate = array(
				    'name' => 'steam_boiler[steam_boiler_quantity][]',
				    'id' => 'steam_bolier_quantity',
				    'value' => $steam_boiler['steam_boiler_quantity'],
				    'class' => 'input-control',
				    // 'maxlength' => 5
				);
				?>
				<?php echo form_input($steam_bolier_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_quantity][]'); ?></span>
				<?php echo form_label(lang('steam-bolier-quantity'), 'steam_bolier_quantity', ["class" => "input-label"]); ?>
			    </div>
			    <div class="form-col-3 form-col-add">
				<?php
				$steam_boiler_power = array(
				    'name' => 'steam_boiler[steam_boiler_power][]',
				    'id' => 'steam_boiler_power',
				    'value' => $steam_boiler['steam_boiler_power'],
				    'class' => 'input-control'
				);
				?>
				<?php echo form_input($steam_boiler_power); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_power][]'); ?></span>

				<?php echo form_label(lang('steam-boiler-power'), 'steam_boiler_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='steam_boiler[steam_boiler_id][]' value='<?php echo $steam_boiler['id']; ?>' type='hidden' />

			    <?php
			    if ($key == 0) {
				?>
				<div class="form-col-1">
				    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='steam_boiler[steam_boiler_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='steam_boiler[steam_boiler_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='steam_boiler[steam_boiler_power][]' type='text' class='input-control'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
				</div>
				<?php
			    } else {
				?>
				<div class="form-col-1">
				    <button class="btn-control substract" type="button"><img alt="Minus" src="images/minus-icon.png"></button>
				</div>
				<?php
			    }
			    ?>
			</div>
			<?php
		    }
		} else {
		    ?>
		    <div class="row add-row">
			<div class="form-col-3">
			    <?php
			    $steam_bolier_quantity_total_rate = array(
				'name' => 'steam_boiler[steam_boiler_quantity][]',
				'id' => 'steam_bolier_quantity',
				'value' => set_value('steam_boiler[steam_boiler_quantity][]'),
				'class' => 'input-control',
				// 'maxlength' => 5
			    );
			    ?>
			    <?php echo form_input($steam_bolier_quantity_total_rate); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_quantity][]'); ?></span>
			    <?php echo form_label(lang('steam-bolier-quantity'), 'steam_bolier_quantity', ["class" => "input-label"]); ?>
			</div>
			<div class="form-col-3 form-col-add">
			    <?php
			    $steam_boiler_power = array(
				'name' => 'steam_boiler[steam_boiler_power][]',
				'id' => 'steam_boiler_power',
				'value' => set_value('steam_boiler[steam_boiler_power][]'),
				'class' => 'input-control'
			    );
			    ?>
			    <?php echo form_input($steam_boiler_power); ?><span class="validation_error"><?php echo form_error('steam_boiler[steam_boiler_power][]'); ?></span>
			    <?php echo form_label(lang('steam-boiler-power'), 'steam_boiler_power', ["class" => "input-label"]); ?>
			</div>
			<input name='steam_boiler[steam_boiler_id][]' value='0' type='hidden' />
			<div class="form-col-1">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row'><input name='steam_boiler[steam_boiler_id][]' value='0' type='hidden' /><div class='form-col-3'><input name='steam_boiler[steam_boiler_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-3'><input name='steam_boiler[steam_boiler_power][]' type='text' class='input-control'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>

		    <?php } ?>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('electrical-hw'); ?> <span class="asterisk">*</span></label>
		<div class="row add-row">
		    <div class="form-col-3 form-col-add">
			<?php
			$elcetrical_hw_total = array(
			    'name' => 'elcetrical_hw_total',
			    'id' => 'elcetrical_hw_total',
			    'value' => set_value('elcetrical_hw_total', ((isset($elcetrical_hw_total)) ? htmlspecialchars_decode($elcetrical_hw_total) : '')),
			    'class' => 'input-control',
			    // 'maxlength' => 5
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
				'class' => 'input-control decimalcheck'
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
			    'class' => 'input-control decimalcheck'
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
			<label class="radio-outer"><input type="radio" <?php
			    if (isset($is_ro_plant) && $is_ro_plant == 1) {
				echo 'checked="checked"';
			    }
			    ?>class="icheck ro_plant_radio" name="is_ro_plant" value="1">Yes</label>
			<label><input type="radio" <?php
			    if (!isset($is_ro_plant) || $is_ro_plant == 0) {
				echo 'checked="checked"';
			    }
			    ?> class="icheck ro_plant_radio" name="is_ro_plant" value="0">No</label>
		    </div>
		    <div class="form-col-3 ro_plant_content">
			<?php if (isset($is_ro_plant) && $is_ro_plant == 1 && isset($ro_plant_capacity) && $ro_plant_capacity != '') { ?>
			    <?php
			    $ro_plant_capacity = array(
				'name' => 'ro_plant_capacity',
				'id' => 'ro_plant_capacity',
				'value' => set_value('ro_plant_capacity', ((isset($ro_plant_capacity)) ? htmlspecialchars_decode($ro_plant_capacity) : '')),
				'class' => 'input-control decimalcheck'
			    );
			    ?>
			    <?php echo form_input($ro_plant_capacity); ?><span class="validation_error"><?php echo form_error('ro_plant_capacity'); ?></span>
			    <?php echo form_label(lang('ro-capacity'), 'ro-capacity', ["class" => "input-label"]); ?>
			<?php } else { ?>
			    <?php
			    $ro_plant_capacity = array(
				'name' => 'ro_plant_capacity',
				'id' => 'ro_plant_capacity',
				'value' => '',
				'class' => 'input-control decimalcheck'
			    );
			    ?>
			    <?php echo form_input($ro_plant_capacity); ?><span class="validation_error"><?php echo form_error('ro_plant_capacity'); ?></span>
			    <?php echo form_label(lang('ro-capacity'), 'ro-capacity', ["class" => "input-label"]); ?>
			<?php } ?>
		    </div>
		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('renewable-energy'), 'renewable_energy', ["class" => "main-label"]); ?>
		<div class="row add-row">
		    <?php do { ?>
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" <?php
				if (isset($is_renewable_energy) && $is_renewable_energy == 1) {
				    echo 'checked="checked"';
				}
				?> class="icheck renewable_energy_radio" name="is_renewable_energy" value="1">Yes</label>
			    <label><input type="radio" <?php
				if (!isset($is_renewable_energy) || $is_renewable_energy == 0) {
				    echo 'checked="checked"';
				}
				?>  class="icheck renewable_energy_radio" name="is_renewable_energy" value="0">No</label>
			</div>
			<div class="form-col-2 form-col-lg-2 renewable_energy_content">
			    <?php
			    $renewable_energy_type = array(
				'name' => 'renewable_energy[renewable_energy_type][]',
				'id' => 'renewable_energy_type',
				'value' => $renewable_energys[0]['renewable_energy_type'],
				'class' => 'input-control renewable_energy_type_class'
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
				'class' => 'input-control',
				// 'maxlength' => 5
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
				'class' => 'input-control'
			    );
			    ?>
			    <?php echo form_input($renewable_energy_power); ?><span class="validation_error"><?php echo form_error('renewable_energy_capacity'); ?></span>
			    <?php echo form_label(lang('renewable-energy-capacirty'), 'renewable_energy_power', ["class" => "input-label"]); ?>
			</div>
			<input name='renewable_energy[renewable_energy_id][]' value='<?php echo $renewable_energys[0]['id'] ?>' type='hidden' />
			<div class="form-col-1 renewable_energy_content">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row renewable_energy_content'><div class='form-col-2 form-control-block'></div><div class='form-col-2 form-col-lg-2 renewable_energy_content'><input name='renewable_energy[renewable_energy_type][]' type='text' class='input-control renewable_energy_type_class'><input name='renewable_energy[renewable_energy_id][]' value='0' type='hidden' /></div><div class='form-col-2 form-col-lg-2 renewable_energy_content'><input name='renewable_energy[renewable_energy_quantity][]' maxlength=5 type='text' class='input-control'></div><div class='form-col-2 form-col-lg-2 renewable_energy_content'><input name='renewable_energy[renewable_energy_capacity][]' type='text' class='input-control decimalcheck'></div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>

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
				    'class' => 'input-control',
				    // 'maxlength' => 5
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
				    'class' => 'input-control decimalcheck'
				);
				?>
				<?php echo form_input($renewable_energy_power); ?><span class="validation_error"><?php echo form_error('renewable_energy_capacity'); ?></span>

				<?php echo form_label(lang('renewable-energy-capacirty'), 'renewable_energy_power', ["class" => "input-label"]); ?>
			    </div>
			    <input name='renewable_energy[renewable_energy_id][]' value='<?php echo $renewable_energys[$i]['id'] ?>' type='hidden' />
			    <div class="form-col-1">
				<button class="btn-control substract" type="button"><img alt="Minus" src="images/minus-icon.png"></button>
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
			<label class="radio-outer"><input type="radio" <?php
			    if (isset($is_stp) && $is_stp == 1) {
				echo 'checked="checked"';
			    }
			    ?>  class="icheck stp_radio" name="is_stp" value="1">Yes</label>
			<label><input type="radio"  <?php
			    if (!isset($is_stp) || $is_stp == 0) {
				echo 'checked="checked"';
			    }
			    ?>  class="icheck stp_radio" name="is_stp" value="0">No</label>
		    </div>
		    <div class="form-col-3 stp_content">
			<?php if (isset($is_stp) && $is_stp == 1 && isset($stp_capacity) && $stp_capacity != '') { ?>
			    <?php
			    $stp_capacity = array(
				'name' => 'stp_capacity',
				'id' => 'stp_capacity',
				'value' => set_value('stp_capacity', ((isset($stp_capacity)) ? htmlspecialchars_decode($stp_capacity) : '')),
				'class' => 'input-control decimalcheck'
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
				'class' => 'input-control decimalcheck'
			    );
			    ?>
			    <?php echo form_input($stp_capacity); ?><span class="validation_error"><?php echo form_error('stp_capacity'); ?></span>
			    <?php echo form_label(lang('stp-capacity'), 'stp-capacity', ["class" => "input-label"]); ?>
			<?php } ?>
		    </div>
		</div>
	    </li>
	</ul>
	<br/>
	<!-- <div class="form-group-label">
	    <h5><strong><?php echo lang('ghg_emissions_factor'); ?></strong></h5>
	</div>
	<ul class="form-outer-block">
	    <li>
		<label class="main-label"><?php echo lang('electricity-emission-factor'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$electricity_emission_factor = array(
			    'name' => 'electricity_emission_factor',
			    'id' => 'electricity_emission_factor',
			    'value' => set_value('electricity_emission_factor', ((isset($electricity_emission_factor)) ? htmlspecialchars_decode($electricity_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
			<?php echo form_input($electricity_emission_factor); ?><span class="validation_error"><?php echo form_error('electricity_emission_factor'); ?></span>
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'electricity'); ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('fuel-emission-factor'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$fuel_emission_factor = array(
			    'name' => 'fuel_emission_factor',
			    'id' => 'fuel_emission_factor',
			    'value' => set_value('fuel_emission_factor', ((isset($fuel_emission_factor)) ? htmlspecialchars_decode($fuel_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
			<?php echo form_input($fuel_emission_factor); ?><span class="validation_error"><?php echo form_error('fuel_emission_factor'); ?></span>
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'fuel_oil'); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('lpg-emission-factor'); ?> <span class="asterisk">*</span></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$lpg_emission_factor = array(
			    'name' => 'lpg_emission_factor',
			    'id' => 'lpg_emission_factor',
			    'value' => set_value('lpg_emission_factor', ((isset($lpg_emission_factor)) ? htmlspecialchars_decode($lpg_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
			<?php echo form_input($lpg_emission_factor); ?><span class="validation_error"><?php echo form_error('lpg_emission_factor'); ?></span>
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'lpg'); ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('natural-gas-emission-factor'); ?> <span class="asterisk">*</span></label>
		    <div class="form-col-3">
			<?php
			$natural_gas_emission_factor = array(
			    'name' => 'natural_gas_emission_factor',
			    'id' => 'natural_gas_emission_factor',
			    'value' => set_value('natural_gas_emission_factor', ((isset($natural_gas_emission_factor)) ? htmlspecialchars_decode($natural_gas_emission_factor) : '')),
			    'class' => 'input-control decimalcheck'
			);
			?>
			<?php echo form_input($natural_gas_emission_factor); ?><span class="validation_error"><?php echo form_error('natural_gas_emission_factor'); ?></span>
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'natural_gas'); ?></label>
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
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'district_cooling'); ?></label>
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
			<label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id,'district_heating'); ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<?php echo form_label(lang('status'), 'status', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-3">
			<div class="form-dropdown">
			    <?php
			    $statuslist = array('1' => 'Active', '0' => 'Inactive');
			    echo form_dropdown('status', $statuslist, $status, 'data-type = "custom-dropdown" ');
			    ?>
			</div>
		    </div>
		</div>
	    </li>
	</ul> -->

	<div class="row col-sm-12">
	    <div class="row col-sm-12">
		<ul class="form-outer-block">
		    <li style="padding-left: 0px;">
			<div class="row">
			    <div class="form-col-12 form-control-block col-sm-12">
				<div class="form-control-block col-sm-7">
				    <div class="form-group-label">
					<h5><strong><?php echo lang('utility_choice'); ?></strong></h5>
				    </div>
				</div>
				<div class="form-control-block col-sm-5">
				    <div class="form-group-label">
					<h5><strong><?php echo lang('regression_analysis'); ?></strong></h5>
				    </div>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
					<strong>R<sup>2</sup></strong>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
				    <strong>Days</strong>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
				    <strong>X</strong>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
				    <strong>OCC</strong>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
				    <strong>HDD</strong>
				</div>
				<div class="form-control-block col-sm-1 text-center pull-right">
				    <strong>CDD</strong>
				</div>
			    </div>
			</div>
		    </li>
		</ul>
		<?php
		$utilities = array('show_utility_electricity', 'show_utility_fuel_oil', 'show_utility_lpg', 'show_utility_water', 'show_utility_irrigation_water', 'show_utility_natural_gas', 'show_utility_district_cooling', 'show_utility_district_heating', 'show_utility_water_waste', 'show_waste_management');
		$utilities_unit = array('show_utility_electricity_unit', 'show_utility_fuel_oil_unit', 'show_utility_lpg_unit', 'show_utility_water_unit', 'show_utility_irrigation_water_unit', 'show_utility_natural_gas_unit', 'show_utility_district_cooling_unit', 'show_utility_district_heating_unit', 'show_utility_water_waste_unit', 'show_waste_management_unit');
		$energy_modelling = [
		    'show_utility_electricity' => 'electricity',
		    'show_utility_fuel_oil' => 'fuel_oil',
		    'show_utility_lpg' => 'lpg',
		    'show_utility_water' => 'water',
		    'show_utility_irrigation_water' => 'irrigation_water',
		    'show_utility_natural_gas' => 'natural_gas',
		    'show_utility_district_cooling' => 'district_cooling',
		    'show_utility_district_heating' => 'district_heating'
		];
		$decimal_point = 2;
		?>
		<?php
		foreach ($utilities as $utility) {
		    if (!empty($energy_modelling_data[$energy_modelling[$utility]])) {
			$energy_cdd = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['cdd']), $decimal_point);
			$energy_hdd = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['hdd']), $decimal_point);
			$energy_occupancy = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['occupancy']), $decimal_point);
			$energy_x = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['x']), $decimal_point);
			$energy_days = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['days']), $decimal_point);
			$energy_days = round((float) ($energy_modelling_data[$energy_modelling[$utility]]['r2']), $decimal_point);
		    } else {
			$energy_cdd = 0;
			$energy_hdd = 0;
			$energy_occupancy = 0;
			$energy_x = 0;
			$energy_days = 0;
			$energy_r2 = 0;
		    }
		    ?>
		    <ul class="form-outer-block">
			<li style="padding-left: 0px; margin-bottom: 0px;">
			    <div class="row">
				<div class="form-col-12 form-control-block col-sm-12">
				    <div class="form-control-block col-sm-2">
					<?php echo form_label(lang($utility), $utility, ["class" => "main-label"]); ?>
				    </div>
				    <div class="form-control-block col-sm-2">
					<label class="radio-outer"><input type="radio" <?php
					    if (isset($$utility) && $$utility == 1) {
						echo 'checked="checked"';
					    }
					    ?> class="icheck" name="<?php echo $utility; ?>" value="1">Yes</label>
					<label><input type="radio" <?php
					    if (!isset($$utility) || $$utility == 0) {
						echo 'checked="checked"';
					    }
					    ?> class="icheck" name="<?php echo $utility; ?>" value="0">No</label>
				    </div>
				    <div class="form-control-block col-sm-2">
				    <?php if ($utility != 'show_waste_management' && $utility != 'show_utility_water_waste' && $utility != 'show_utility_irrigation_water') { ?>
					<div class="form-dropdown">
					    <?php
					    $list = $energy_modelling_data[$energy_modelling[$utility]]['utility_unit_dropdown'];
					    $name = 'utility_unit_'.$energy_modelling[$utility];
					    $value = $energy_modelling_data[$energy_modelling[$utility]]['utility_unit_value'];
					    echo form_dropdown($name, $list, $value, 'data-type = "custom-dropdown" ');
					    ?>
					</div>
				    <?php } ?>
				    </div>
				    <!-- <div class="form-control-block col-sm-1">
					&nbsp;
				    </div> -->
				    <?php if ($utility != 'show_waste_management' && $utility != 'show_utility_water_waste' && $utility != 'show_utility_irrigation_water') { ?>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][cdd]'; ?>' type='text' class='input-control' placeholder='CDD' value="<?php echo $energy_cdd; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][cdd]'); ?></span>
					</div>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][hdd]'; ?>' type='text' class='input-control' placeholder='HDD' value="<?php echo $energy_hdd; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][hdd]'); ?></span>
					</div>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][occupancy]'; ?>' type='text' class='input-control' placeholder='OCC' value="<?php echo $energy_occupancy; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][occupancy]'); ?></span>
					</div>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][x]'; ?>' type='text' class='input-control' placeholder='X' value="<?php echo $energy_x; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][x]'); ?></span>
					</div>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][days]'; ?>' type='text' class='input-control' placeholder='X' value="<?php echo $energy_days; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][days]'); ?></span>
					</div>
					<div class="form-control-block col-sm-1">
					    <input name='<?php echo 'energy_modeling[' . $energy_modelling[$utility] . '][r2]'; ?>' type='text' class='input-control' placeholder='X' value="<?php echo $energy_r2; ?>">
					    <span class="validation_error"><?php echo form_error('energy_modeling[' . $energy_modelling[$utility] . '][r2]'); ?></span>
					</div>
				    <?php }
				    ?>
				</div>
			    </div>
			</li>
			<?php if ($utility == 'show_utility_district_heating') { ?>
			    <li style="padding-left:0px; margin-bottom:0px;left:35px;">
				<div class="row">
				    <div class="form-col-12 form-control-block col-sm-12">
					<div class="form-control-block col-sm-2" style="width: 10%;">
					    <?php echo form_label('Select Source', 'Select Source', ["class" => "main-label"]); ?>
					</div>
					<div class="form-control-block col-sm-3">
					    <div class="form-dropdown">
						<?php
						$list = [
						    0=>'Select Source',
						    1=>lang('show_utility_steam_boiler'),
						    2=>lang('show_utility_hot_water_boiler')
						];
						$name = 'show_utility_district_heating_boiler';
						$value = $show_utility_district_heating_boiler;
						echo form_dropdown($name, $list, $value, 'data-type = "custom-dropdown" ');
						?>
					    </div>
					</div>
				    </div>
				</div>
			    </li>
			<?php } ?>

		    </ul>
		<?php }
		?>
		<ul class="form-outer-block">
		    <li style="padding-left:10px;">
			<div class="row">
			    <div class="form-control-block col-sm-2">
				<?php echo form_label(lang('baseline_regression_year'), 'Reference Year', ["class" => "main-label"]); ?>
			    </div>
			    <div class="col-sm-2">
				<input type="text" name="baseline_regression_year" placeholder="Enter Year" class='input-control' value="<?php echo $baseline_regression_year; ?>">
				<span class="validation_error"><?php echo form_error('baseline_regression_year'); ?></span>
			    </div>
			</div>
		    </li>
		</ul>
	    </div>
	</div>

	<?php /*         * * Daily reading settings start ** */ ?>
	<div class="form-group-label form-outer-block">
			<div class="row add-row">
				<div class="col-md-3">
					<h5><strong><?php echo lang('daily_reading_setting'); ?></strong></h5>
				</div>
				<div class="form-col-4">
					<label class="checkbox-outer col-sm-4">
						<input name='is_used_in_cron' class='icheck' value='1' type='checkbox' <?php echo (isset($is_used_in_cron) && $is_used_in_cron == 1) ? 'checked' : ''; ?>>
						<span class="col-sm-12">7 days average consumption</span>
					</label>
				</div>
			</div>
	</div>

	<ul class="form-outer-block">
	    <li>
		<?php echo form_label(lang('daily_metering'), 'daily_metering', ["class" => "main-label"]); ?>
		<div class="row">
		    <div class="form-col-5 form-control-block">
			<label class="radio-outer"><input type="radio" <?php
			    if (isset($is_hourly) && $is_hourly == 1) {
				echo 'checked="checked"';
			    }
			    ?> class="icheck" name="is_hourly" value="1">Hourly</label>
			<label><input type="radio" <?php
			    if (!isset($is_hourly) || $is_hourly == 0) {
				echo 'checked="checked"';
			    }
			    ?> class="icheck" name="is_hourly" value="0">Half Hourly</label>
		    </div>
		</div>
	    </li>
	</ul>

	<?php foreach ($daily_reading_utilities_list as $utility) {
	    ?>
	    <ul class="form-outer-block">
		<li>
		    <?php echo form_label(lang("daily_reading_{$utility['title']}_title"), $utility['title'], ["class" => "main-label"]); ?>
		    <div class="row add-row">
			<div class="form-col-1">
			    <button class="btn-control addition" type="button" data-row="<div class='row add-row'>
				<div class='form-col-3'><input name='daily_reading[ids][{random}]' value='0' type='hidden'><input name='daily_reading[utilities][{random}]' value='<?php echo $utility['id']; ?>' type='hidden'><input name='daily_reading[titles][{random}]' type='text' class='input-control' placeholder='Title'></div><div class='form-col-3'><input name='daily_reading[hourly_titles][{random}]' type='text' class='input-control' placeholder='Hourly/ Half Hourly Title' value=''>
				</div><div class='form-col-1'><button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button></div><div class='form-col-1'><input id='{random}' name='daily_reading[is_used_in_cron][{random}]' class='icheck' value='1' type='checkbox' ></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>
			</div>
						<div class='form-col-1'>
							<input name='daily_reading_utlities[is_used_in_cron][]' class='icheck' value='<?php echo $utility['id']; ?>' type='checkbox' <?php echo (in_array($utility['id'], $read_daily_reading_utilites_setting)) ? 'checked' : ''; ?>>
						</div>
		    </div>

		    <?php // show Existing data ?>
		    <?php
		    if (!empty($daily_reading_settings)) {
			foreach ($daily_reading_settings as $key => $value) {
			    if ($value['utility_id'] == $utility['id']) {
				?>
				<div class='row add-row'>
				    <div class='form-col-3'>
					<input name='daily_reading[ids][]' value='<?php echo $value['id']; ?>' type='hidden'><input name='daily_reading[utilities][]' value='<?php echo $utility['id']; ?>' type='hidden'>
					<input name='daily_reading[titles][]' type='text' class='input-control' placeholder='Title' value="<?php echo $value['title']; ?>">
				    </div>
				    <div class='form-col-3'>
				       <input name='daily_reading[hourly_titles][]' type='text' class='input-control' placeholder='Hourly/ Half Hourly Title' value="<?php echo $value['hourly_title']; ?>">
				    </div>
				    <div class='form-col-1'>
					<button type='button' class='btn-control substract'><img src='images/minus-icon.png' alt='Minus'></button>
				    </div>
				    <div class='form-col-1'>
										<input name='daily_reading[is_used_in_cron][<?php echo $value['id']; ?>]' class='icheck' value='1' type='checkbox' <?php echo ($value['is_used_in_cron']) ? 'checked' : ''; ?>>
				    </div>
				</div>
				<?php
			    }
			}
		    }
		    ?>

		</li>
	    </ul>
	<?php }
	?>
		<ul class="form-outer-block">
			<li>
				<label class="main-label"><?php echo lang('threshold'); ?></label>
				<div class="row">
					<div class="form-col-3">
						<input name="threshold" value="<?php echo $threshold; ?>" id="threshold" class="input-control" tabindex="21" type="text">
						<label class="input-label">(%)</label>
					</div>
				</div>
			</li>
		</ul>
	<?php /*         * * Daily reading settings END ** */ ?>


	<div class="form-group-label">
	    <h5><strong><?php echo lang('notifications'); ?></strong></h5>
	</div>
	<?php
	$utilities = array('show_total_utility_notification');
	?>

	<?php foreach ($utilities as $utility) {
	    ?>
	    <ul class="form-outer-block">
		<li>
		    <?php echo form_label(lang($utility), $utility, ["class" => "main-label"]); ?>
		    <div class="row">
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" <?php
				if (isset($utility) && $utility == 1) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="<?php echo $utility; ?>" value="1">Yes</label>
			    <label><input type="radio" <?php
				if (!isset($utility) || $utility == 0) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="<?php echo $utility; ?>" value="0">No</label>
			</div>
		    </div>
		</li>
	    </ul>
	<?php }
	?>

	<div class="form-group-label">
	    <h5><strong><?php echo lang('csr'); ?></strong></h5>
	</div>

	    <ul class="form-outer-block">
		<li>
		    <?php echo form_label(lang('csr'), 'csr', ["class" => "main-label"]); ?>
		    <div class="row">
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" <?php
				if (isset($csr) && $csr == 1) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="csr" value="1">Yes</label>
			    <label><input type="radio" <?php
				if (!isset($csr) || $csr == 0) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="csr" value="0">No</label>
			</div>
		    </div>
		</li>
	    </ul>

	<div class="form-group-label">
	    <h5><strong><?php echo lang('daily_metering'); ?></strong></h5>
	</div>

	    <ul class="form-outer-block">
		<li>
		    <?php echo form_label(lang('daily_metering'), 'daily_metering', ["class" => "main-label"]); ?>
		    <div class="row">
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" <?php
				if (isset($daily_metering) && $daily_metering == 1) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="daily_metering" value="1">Yes</label>
			    <label><input type="radio" <?php
				if (!isset($daily_metering) || $daily_metering == 0) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="daily_metering" value="0">No</label>
			</div>
		    </div>
		</li>
	    </ul>

	<div class="form-group-label">
	    <h5><strong><?php echo lang('chsb_hotel_sustainability_benchmarking'); ?></strong></h5>
	</div>

	    <ul class="form-outer-block">
		<li>
		    <?php echo form_label(lang('chsb_reporting'), 'chsb_reporting', ["class" => "main-label"]); ?>
		    <div class="row">
			<div class="form-col-2 form-control-block">
			    <label class="radio-outer"><input type="radio" <?php
				if (isset($chsb_reporting) && $chsb_reporting == 1) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="chsb_reporting" value="1">Yes</label>
			    <label><input type="radio" <?php
				if (!isset($chsb_reporting) || $chsb_reporting == 0) {
				    echo 'checked="checked"';
				}
				?> class="icheck" name="chsb_reporting" value="0">No</label>
			</div>
			<label class="main-label col-sm-3 rightLabel"><?php echo lang('chsb_segment'); ?></label>
			<div class="form-control-block col-sm-4">
			    <div class="form-dropdown">
				<?php
				$segmentlist = [
				    0 => 'None Selected',
				    1 => 'Urban',
				    2 => 'Suburban',
				    3 => 'Rural',
				    4 => 'Airport',
				    5 => 'Convention',
				    6 => 'Resort',
				    7 => 'Timeshare',
				    8 => 'Small metro/town',
				    9 => 'Bed & breakfast'
				];
				echo form_dropdown('chsb_segment', $segmentlist, $chsb_segment, 'data-type = "custom-dropdown" ');
				?>
			    </div>
			</div>
		    </div>
		</li>
	    </ul>

	<div class="row">
	    <div class="col-sm-2">
	    <label class="main-label" style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;"><?php echo lang('import-file'); ?> : </label>
	    </div>
	    <div class="col-sm-8">
	       <?php
		$importfile_data = array(
		    'name' => 'importfile',
		    'id' => 'importfile',
		    'class' => 'form-control'
		);
		echo form_upload($importfile_data,'',$disabled);
		?>
		<span class="warning-msg"><?php echo form_error('importfile'); ?></span>
		<br />

	    </div>
	</div>

	<div class="form-group-label">
	    <h5><strong><?php echo lang('reduction-targets'); ?></strong></h5>
	</div>
	<div class="form-group-label">
	    <h6><strong><?php echo 'Industry Benchmark'; ?></strong></h6>
	</div>
	<ul class="form-outer-block">
	    <li>
		<label class="main-label"><?php echo lang('energy-intensity').'('.GetSiteUtilityUnitName($site_id,'electricity').'/'.getLocalUnitText($site_id).')'; ?></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$energy_intensity_benchmark_target = array(
			    'name' => 'energy_intensity_benchmark_target',
			    'value' => set_value('energy_intensity_benchmark_target', ((isset($energy_intensity_benchmark_target)) ? htmlspecialchars_decode($energy_intensity_benchmark_target) : '')),
			    'class' => 'input-control',
			    'id' => 'energy_intensity_benchmark_target'
			);
			?>
			<?php echo form_input($energy_intensity_benchmark_target); ?><span class="validation_error"><?php echo form_error('energy_intensity_benchmark_target'); ?></span>
			<!-- <label class="input-label"><?php echo '(%)'; ?></label> -->
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('ghg-intensity').'(Kg Co2e/'.getLocalUnitText($site_id).')'; ?></label>
		    <div class="form-col-3">
			<?php
			$ghg_intensity_benchmark_target = array(
			    'name' => 'ghg_intensity_benchmark_target',
			    'id' => 'ghg_intensity_benchmark_target',
			    'value' => set_value('ghg_intensity_benchmark_target', ((isset($ghg_intensity_benchmark_target)) ? htmlspecialchars_decode($ghg_intensity_benchmark_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($ghg_intensity_benchmark_target); ?><span class="validation_error"><?php echo form_error('ghg_intensity_benchmark_target'); ?></span>
			<!-- <label class="input-label"><?php echo '(%)'; ?></label> -->
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('water-intensity').'('.GetSiteUtilityUnitName($site_id,'water').'/'.getLocalUnitText($site_id).')'; ?></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$water_intensity_benchmark_target = array(
			    'name' => 'water_intensity_benchmark_target',
			    'id' => 'water_intensity_benchmark_target',
			    'value' => set_value('water_intensity_benchmark_target', ((isset($water_intensity_benchmark_target)) ? htmlspecialchars_decode($water_intensity_benchmark_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($water_intensity_benchmark_target); ?><span class="validation_error"><?php echo form_error('water_intensity_benchmark_target'); ?></span>
			<!-- <label class="input-label"><?php echo '(%)'; ?></label> -->
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('waste-intensity').'(Kg/'.getLocalUnitText($site_id).')'; ?></label>
		    <div class="form-col-3">
			<?php
			$waste_intensity_benchmark_target = array(
			    'name' => 'waste_intensity_benchmark_target',
			    'id' => 'waste_intensity_benchmark_target',
			    'value' => set_value('waste_intensity_benchmark_target', ((isset($waste_intensity_benchmark_target)) ? htmlspecialchars_decode($waste_intensity_benchmark_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($waste_intensity_benchmark_target); ?><span class="validation_error"><?php echo form_error('waste_intensity_benchmark_target'); ?></span>
			<!-- <label class="input-label"><?php echo '(%)'; ?></label> -->
		    </div>
		</div>
	    </li>
	</ul>
	<div class="form-group-label">
	    <h6><strong><?php echo 'Annual'; ?></strong></h6>
	</div>
	<ul class="form-outer-block">
	    <li>
		<label class="main-label"><?php echo lang('energy-intensity').'('.GetSiteUtilityUnitName($site_id,'electricity').'/'.getLocalUnitText($site_id).')'; ?></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$energy_intensity_annual_target = array(
			    'name' => 'energy_intensity_annual_target',
			    'value' => set_value('energy_intensity_annual_target', ((isset($energy_intensity_annual_target)) ? htmlspecialchars_decode($energy_intensity_annual_target) : '')),
			    'class' => 'input-control',
			    'id' => 'energy_intensity_annual_target'
			);
			?>
			<?php echo form_input($energy_intensity_annual_target); ?><span class="validation_error"><?php echo form_error('energy_intensity_annual_target'); ?></span>
			<label class="input-label"><?php echo '(%)'; ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('ghg-intensity').'(Kg Co2e/'.getLocalUnitText($site_id).')'; ?></label>
		    <div class="form-col-3">
			<?php
			$ghg_intensity_annual_target = array(
			    'name' => 'ghg_intensity_annual_target',
			    'id' => 'ghg_intensity_annual_target',
			    'value' => set_value('ghg_intensity_annual_target', ((isset($ghg_intensity_annual_target)) ? htmlspecialchars_decode($ghg_intensity_annual_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($ghg_intensity_annual_target); ?><span class="validation_error"><?php echo form_error('ghg_intensity_annual_target'); ?></span>
			<label class="input-label"><?php echo '(%)'; ?></label>
		    </div>
		</div>
	    </li>
	    <li>
		<label class="main-label"><?php echo lang('water-intensity').'('.GetSiteUtilityUnitName($site_id,'water').'/'.getLocalUnitText($site_id).')'; ?></label>
		<div class="row">
		    <div class="form-col-3">
			<?php
			$water_intensity_annual_target = array(
			    'name' => 'water_intensity_annual_target',
			    'id' => 'water_intensity_annual_target',
			    'value' => set_value('water_intensity_annual_target', ((isset($water_intensity_annual_target)) ? htmlspecialchars_decode($water_intensity_annual_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($water_intensity_annual_target); ?><span class="validation_error"><?php echo form_error('water_intensity_annual_target'); ?></span>
			<label class="input-label"><?php echo '(%)'; ?></label>
		    </div>
		    <label class="main-label col-sm-4 rightLabel"><?php echo lang('waste-intensity').'(Kg/'.getLocalUnitText($site_id).')'; ?></label>
		    <div class="form-col-3">
			<?php
			$waste_intensity_annual_target = array(
			    'name' => 'waste_intensity_annual_target',
			    'id' => 'waste_intensity_annual_target',
			    'value' => set_value('waste_intensity_annual_target', ((isset($waste_intensity_annual_target)) ? htmlspecialchars_decode($waste_intensity_annual_target) : '')),
			    'class' => 'input-control'
			);
			?>
			<?php echo form_input($waste_intensity_annual_target); ?><span class="validation_error"><?php echo form_error('waste_intensity_annual_target'); ?></span>
			<label class="input-label"><?php echo '(%)'; ?></label>
		    </div>
		</div>
	    </li>
	</ul>
		<?php
	if(!empty($measure_readings))
	{     ?>

	<div class="row">
	    <div class="form-col-5">
		<label style="font-weight: 500; left: 17px;"><strong><?php echo lang('measure'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
	       <label style="font-weight: 500;"><strong><?php echo lang('low'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('lower_quartile'); ?></strong>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('mean'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('median'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('upper_quartile'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('high'); ?></strong></label>
	    </div>
	    <div class="form-col-1">
		<label style="font-weight: 500;"><strong><?php echo lang('sd'); ?></strong></label>
	    </div>
	</div>
	<div style="clear: both;"></div>

	<?php
	foreach ($measure_readings as $key => $measure_reading) {
	    ?>
	    <div class="row">
		<div class="form-col-5">
		    <label style="font-weight: 500; left: 17px; " ><?php echo $measure_reading['title']; ?></label>
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'low'.$key; ?>" class='input-control' value="<?php echo $measure_reading['low']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px;width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'lower_quartile'.$key; ?>" class='input-control' value="<?php echo $measure_reading['lower_quartile']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'mean'.$key; ?>" class='input-control' value="<?php echo $measure_reading['mean']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'median'.$key; ?>" class='input-control' value="<?php echo $measure_reading['median']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'upper_quartile'.$key; ?>" class='input-control' value="<?php echo $measure_reading['upper_quartile']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'high'.$key; ?>" class='input-control' value="<?php echo $measure_reading['high']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
		<div class="form-col-1">
		    <input name="<?php echo 'sd'.$key; ?>" class='input-control' value="<?php echo $measure_reading['sd']; ?>" disabled="disabled" style="margin-bottom: 10px;padding: 6px 8px; width: 139%; font-size: 10px;">
		</div>
	    </div>
	    <?php

	}
    } ?>


	<div class="form-btn-outer">
	    <button type="submit" id="mysubmit" name="mysubmit" value="<?php echo lang('btn-save'); ?>" class="btn btn-secondary btn-submit"><?php echo lang('btn-save'); ?></button>
	    <a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'sites'; ?>" class="btn btn-secondary reset-btn btn-submit"><?php echo lang('btn-cancel'); ?></a>
	</div>

	<?php
	echo form_hidden('id', (isset($site_id)) ? $site_id : '0' );
	echo form_close();
	?>

	<!-- <article class="card">
	    <div class="article-header"><?php echo lang('import-header'); ?></div>
	    <div class="card-wrap">
	    <?php
		$attributes = array('name' => 'import_form', 'id' => 'import_form','enctype'=>'multipart/form-data');
		echo form_open('sites/edit/'.$site_id, $attributes);
	    ?>

		    <div class="form-outer-block">
			<div class="row">
			    <div class="form-control-block col-sm-2">
			    <label class="main-label" style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;"><?php echo form_label(lang('import-file'), 'Import File'); ?> : </label>
			    </div>
			    <div class="form-col-6">
			       <?php
				$importfile_data = array(
				    'name' => 'importfile',
				    'id' => 'importfile',
				    'class' => 'form-control'
				);
				echo form_upload($importfile_data,'',$disabled);
				?>
				<span class="warning-msg"><?php echo form_error('importfile'); ?></span>

			    </div>
			    <div class="form-col-4"><button type="submit" class="btn btn-secondary btn-submit" style="float: right;" id="mysubmit_import" name="mysubmit_import"><?php echo lang('btn-import'); ?></button>
			    </div>
			</div>
		    </div>

	    <?php echo form_close(); ?>
	</div>
	</article> -->

	<?php
	echo form_hidden('site_id', (isset($site_id)) ? $site_id : '0' );
	echo form_close();
	?>
    </div>
</article>
<script type="text/javascript">
    $(document).ready(function () {
	$('[data-toggle="tooltip"]').tooltip();
	$(".btn-control.substract").click(function (e) {
	    e.preventDefault();
	    var $this = $(this);
	    $this.closest(".row").remove();
	});
	$.validator.addClassRules('floatcheck', {
	    //number: true
	    digits: true
	});

	$.validator.addClassRules('decimalcheck', {
	    number: true
		    //digits: true
	});

	/*$("#dk2-combobox").on("blur",function() {
	 var validator = $( "#saveform" ).validate();
	 setTimeout(function() {
	 if(validator.element("#region_id") === false) {
	 $("#dk2-region_id div.dk-selected").addClass("dk-selected-error");
	 setTimeout(function() {
	 $('label[for="region_id"]').show();
	 }, 1);
	 } else {
	 $("#dk2-region_id div.dk-selected").removeClass("dk-selected-error");
	 }
	 }, 200);
	 });*/

	/*$("#dk1-combobox").on("blur",function() {
	 var validator = $("#saveform").validate();
	 setTimeout(function() {
	 if(validator.element("#hotel_id") === false) {
	 $("#dk1-hotel_id div.dk-selected").addClass("dk-selected-error");
	 setTimeout(function() {
	 $('label[for="hotel_id"]').show();
	 }, 1);
	 } else {
	 $("#dk1-hotel_id div.dk-selected").removeClass("dk-selected-error");
	 }
	 }, 200);
	 });*/

	/*$(".file-upload-input").on("blur",function() {
	 var validator = $("#saveform").validate();
	 if(validator.element("#file") === false) {
	 $(".file-upload-input").addClass("file-upload-input-error");
	 } else {
	 $(".file-upload-input").removeClass("file-upload-input-error");
	 }
	 });*/

	$("#mysubmit").click(function () {
	    var validator = $("#saveform").validate();

	    $('input[name="generator[generator_name][]"], input[name="generator[generator_quantity][]"], input[name="generator[generator_power][]"]').each(function (index) {
		validator.element(this);
	    });

<?php
if ($is_site_logo_exists == '0') {
    $sitelogo_require = 'site_logo:{
					    required: true
					},';
} else {
    $sitelogo_require = '';
}
?>

<?php /* if ($is_site_logo_exists == '0') { ?>
  $( 'input[name="site_logo"' ).rules( "add", { required: true });

  if(validator.element("#file") === false) {
  $(".file-upload-input").addClass("file-upload-input-error");
  } else {
  $(".file-upload-input").removeClass("file-upload-input-error");
  }
  <?php } */ ?>

	    /*if(validator.element("#hotel_id") === false) {
	     $("#dk1-hotel_id div.dk-selected").addClass("dk-selected-error");
	     setTimeout(function(){
	     $('label[for="hotel_id"]').show();
	     }, 1);
	     } else {
	     $("#dk1-hotel_id div.dk-selected").removeClass("dk-selected-error");
	     }*/

	    /*if(validator.element("#region_id") === false) {
	     $("#dk1-region_id div.dk-selected").addClass("dk-selected-error");
	     setTimeout(function(){
	     $('label[for="region_id"]').show();
	     }, 1);
	     } else {
	     $("#dk1-region_id div.dk-selected").removeClass("dk-selected-error");
	     }*/
	});

	$.validator.setDefaults({
	    ignore: []
	});

	$("#saveform").validate({
	    rules: {
<?php echo $sitelogo_require; ?>,
		site_location_name: {
		    required: true,
		    maxlength: 50
		},
		site_location_latitude: {
		    required: true,
		    maxlength: 20
			    //digits: true
		},
		site_location_longitude: {
		    required: true,
		    maxlength: 20
			    //digits: true
		},
		station_id: {
		    required: true,
		    maxlength: 20
			    //digits: true
		},
		hotel_id: {
		    required: true
		},
		site_type: {
		    required: true
		},
		attribute: {
		    required: true,
		    maxlength: 3
		},
		residences_attribute: {
		    maxlength: 3
		},
		rental_program_attribute: {
		    maxlength: 5
		},
		employee_quarter_attribute:{
		    maxlength: 5
		},
		region_id: {
		    required: true
		},
		country_id: {
		    required: true
		},
		site_year_built: {
		    required: true,
		    maxlength: 4,
		    minlength: 4,
		    //number: true
		    digits: true
		},
		site_builtup_area: {
		    // required: true,
		    //number: true,
		    digits: true
		},
		cooled_builtup_area: {
		    // required: true,
		    //number: true
		    digits: true
		},
		energy_intensity_annual_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		ghg_intensity_annual_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		water_intensity_annual_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		waste_intensity_annual_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		energy_intensity_benchmark_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		ghg_intensity_benchmark_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		water_intensity_benchmark_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		waste_intensity_benchmark_target: {
		    // required: true,
		    number: true
		    // digits: true
		},
		hotel_rooms_area: {
		    required: true,
		    //number: true
		    digits: true
		},
		indoor_parking_area: {
		    // required: true,
		    //number: true
		    digits: true
		},
		rooms_keys: {
		    required: true,
		    maxlength: 3,
		    //number: true
		    digits: true
		},
		outdoor_pools: {
		    // required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		indoor_pools: {
		    // required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'substation[substation_quantity][]': {
		    required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'calorifiers_unit': {
		    required: true,
		    //number: true
		    digits: true
		},
		'calorifiers_volume': {
		    required: true,
		    //number: true
		    digits: true
		},
		'total_meeting_area': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'total_spa_area': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'room_area_rental_program': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'room_area_private_residence': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'hotel_rooms_area': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'residential_common_area': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'employee_living_quarters_area': {
		    // required: true,
		    //number: true
		    // digits: true
		},
		'f_b_service': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'restaurant_area': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'landscaped_area': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'f_b_services_operated': {
		    required: true,
		    // number: true
		    // digits: true
		},
		'f_b_services_outsourced': {
		    required: true,
		    // number: true
		    // digits: true
		},
		'month_year_operation': {
		    required: true,
		    // number: true
		    // digits: true
		},
		'vehicle_electric': {
		    required: true,
		    //number: true
		    // digits: true
		},
		'vehicle_petrol': {
		    required: true,
		    // number: true
		    // digits: true
		},
		'rental_program_residence': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'rental_private_residence': {
		    // required: true,
		    // number: true,
		    // digits: true
		},
		'rental_program_residence_suites': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'rental_private_residence_suites': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'total_guest_room_area': {
		    // required: true,
		    // number: true
		    // digits: true
		},
		'substation[substation_power][]': {
		    required: true,
		    //number: true
		    digits: true
		},
		'generator[generator_name][]': {
		    required: true
		},
		'generator[generator_quantity][]': {
		    required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'generator[generator_power][]': {
		    required: true,
		    //number: true
		    digits: true
		},
		'hot_water_boiler[hot_water_boiler_quantity][]': {
		    required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'hot_water_boiler[hot_water_boiler_power][]': {
		    required: true,
		    //number: true
		    digits: true
		},
		'steam_boiler[steam_boiler_quantity][]': {
		    required: true,
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'steam_boiler[steam_boiler_power][]': {
		    required: true,
		    number: true
			    //digits: true
		},
		elcetrical_hw_total: {
		    required: true,
		    //number: true
		    digits: true
		},
		elcetrical_hw_total_capacity: {
		    required: true,
		    number: true
			    //digits: true
		},
		elcetrical_hw_total_power: {
		    required: true,
		    number: true
			    //digits: true
		},
		electricity_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		fuel_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		lpg_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		natural_gas_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		district_cooling_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		district_heating_emission_factor: {
		    required: true,
		    number: true
			    //digits: true
		},
		stp_capacity: {
		    required: function (element) {
			if ($('.stp_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    number: true
			    //digits: true
		},
		ro_plant_capacity: {
		    required: function (element) {
			if ($('.ro_plant_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    number: true
			    //digits: true
		},
		total_vrv_unit: {
		    required: function (element) {
			if ($('.vrv_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    maxlength: 3,
		    //number: true
		    digits: true
		},
		total_vrv: {
		    required: function (element) {
			if ($('.vrv_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    maxlength: 3,
		    //number: true
		    digits: true
		},
		total_split_dx_unit: {
		    required: function (element) {
			if ($('.split_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    maxlength: 3,
		    digits: true
		},
		total_rate_split_dx_unit: {
		    required: function (element) {
			if ($('.split_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    number: true
			    //digits: true
		},
		chilled_water_system_total_rate: {
		    required: function (element) {
			if ($('.chilled_water_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    number: true
			    //digits: true
		},
		'renewable_energy[renewable_energy_type][]': {
		    required: function (element) {
			if ($('.renewable_energy_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    }
		},
		'renewable_energy[renewable_energy_quantity][]': {
		    required: function (element) {
			if ($('.renewable_energy_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    },
		    // maxlength: 5,
		    //number: true
		    digits: true
		},
		'renewable_energy[renewable_energy_capacity][]': {
		    required: function (element) {
			if ($('.renewable_energy_radio:checked').val() == 0) {
			    return false;
			} else {
			    return true;
			}
		    }
		    //digits: true
		}
	    }
	});

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

	$(":input").each(function (i) {
	    $(this).attr('tabindex', i + 1);
	})

	$("input:text:visible:first").focus().val($('input:text:visible:first').val());
	$('.chilled_water_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1") {
		$('.chilled_water_content').show();
	    } else {
		$('.chilled_water_content').hide();
	    }
	});
	$('.split_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1" || $(this).val() == 1) {
		$('.split_content').show();
	    } else {
		$('.split_content').hide();
	    }
	});
	$('.vrv_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1") {
		$('.vrv_content').show();
	    } else {
		$('.vrv_content').hide();
	    }
	});
	$('.stp_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1") {
		$('.stp_content').show();
	    } else {
		$('.stp_content').hide();
	    }
	});
	$('.renewable_energy_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1") {
		$('.renewable_energy_content').show();
	    } else {
		$('.renewable_energy_content').hide();
	    }
	});
	$('.ro_plant_radio').on('ifChecked', function (event) {
	    if ($(this).val() == "1") {
		$('.ro_plant_content').show();
	    } else {
		$('.ro_plant_content').hide();
	    }
	});
	$("#color-picker").spectrum({
	    color: "<?php echo (!empty($site_color)) ? $site_color : '#397A3E'; ?>",
	    preferredFormat: "hex",
	    showInput: true,
	    showPalette: true,
	    /*change: function(color) {
	     $('#selectedcolorblock').css('background-color',color.toHexString());
	     $("#site_color").val(color.toHexString());
	     }*/
	});

	$(".additiondropdown").click(function () {
	    $("select[data-type='custom-dropdown-addmore']").dropkick({
		mobile: true
	    });
	    $(".additiondropdown").hide();
	    $(".substracttoggle").on('click', function () {
		$(".additiondropdown").show();
		$("#chilled_water_system_type2").val('');
		$("#chilled_water_system_total_rate2").val('');
	    });
	});
	$(".substracttoggle").on('click', function () {
	    $(".additiondropdown").show();
	    $("#chilled_water_system_type2").val('');
	    $("#chilled_water_system_total_rate2").val('');
	});
	$("select[data-type='custom-dropdown-addmore']").dropkick({
	    mobile: true
	});
    });

$('#rental_program_residence,#rental_private_residence').on('change', function() {
    if(parseFloat($(this).val()) != 0 && $(this).val() != '') {
	var site_built_up = parseFloat($('#site_builtup_area').val()) + parseFloat($(this).val());
	$('#site_builtup_area').val(site_built_up).trigger('change');
	$('#site_builtup_area_disabled').val(site_built_up).trigger('change');
    }
});
</script>
