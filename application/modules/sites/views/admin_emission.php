<style type="text/css">
    .emission_image {
        width: 75px;
        height: 75px;
    }

    .emission_image_div {
        width: 100px;
        height: 100px;
        border: 2px solid #dbdbdb !important;
        padding: 10px;
    }
    .main-label {
        max-width: 250px;
    }
</style>
<?php
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min', 'bootstrap-datepicker-new', 'bootstrap-multiselect'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new', 'custom', 'bootstrap-multiselect'));
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header">
            <div class="row"><?php echo $site_name.' - Site ' . lang('emission'); ?></div>
            <div class="row">
                <h5><strong><?php echo lang('ghg_emissions_factor'); ?></strong></h5>
            </div>
        </div>
        <div class="data-info-block-outer" style="margin-bottom: 20px;">
            <div class="row">
                <div class="col-sm-12 Tab-block">
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="data-info-block">
                            <input type="text" id="YearFormat" class='Default' value="<?php echo (!empty($utilities_year)) ? $utilities_year : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_open_multipart('', array('id' => 'emissionForm', 'name' => 'emissionForm')); ?>
        <div class="card-wrap Tab-block" id="group-report" style="max-width: inherit;">
            <ul class="resp-tabs-list hor_1 clearfix">
                <li class="tab-custom-id-1"><?php echo '<b><span class="fa fa-recycle"></span> Scope 1 & 2 Emission Factors</b>'; ?></li>
                <li class="tab-custom-id-2"><?php echo '<b><span class="fa fa-recycle"></span> Scope 3 Waste Emission Factors</b>'; ?></li>
                <li class="tab-custom-id-3"><?php echo '<b><span class="fa fa-recycle"></span> Scope 3 Other Emission Factors</b>'; ?></li>
            </ul>
            <div class="resp-tabs-container hor_1">
                <div id="tab-1" data-tab-id="1">
                    <ul class="form-outer-block">
                        <li>
                            <label class="main-label"><?php echo lang('electricity-emission-factor'); ?> <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $electricity_emission_factor = array(
                                        'name' => 'electricity_emission_factor',
                                        'id' => 'electricity_emission_factor',
                                        'value' => set_value('electricity_emission_factor', ((isset($site_emission['electricity_emission_factor']) && !empty($site_emission['electricity_emission_factor'])) ? htmlspecialchars_decode($site_emission['electricity_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $electricity_emission_factor['style'] = "cursor: not-allowed;";
                                        $electricity_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($electricity_emission_factor); ?><span class="validation_error"><?php echo form_error('electricity_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_ELECTRICITY, true); ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel"><?php echo lang('fuel-emission-factor'); ?> <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $fuel_emission_factor = array(
                                        'name' => 'fuel_emission_factor',
                                        'id' => 'fuel_emission_factor',
                                        'value' => set_value('fuel_emission_factor', ((isset($site_emission['fuel_emission_factor'])) ? htmlspecialchars_decode($site_emission['fuel_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $fuel_emission_factor['style'] = "cursor: not-allowed;";
                                        $fuel_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($fuel_emission_factor); ?><span class="validation_error"><?php echo form_error('fuel_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_FUEL_OIL, true); ?></label>
                                </div>
                            </div>

                        </li>
                        <li>
                            <label class="main-label"><?php echo lang('electricity-emission-factor-percentage'); ?> <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $electricity_emission_factor_percentage = array(
                                        'name' => 'electricity_emission_factor_percentage',
                                        'id' => 'electricity_emission_factor_percentage',
                                        'value' => set_value('electricity_emission_factor_percentage', ((isset($site_emission['electricity_emission_factor_percentage']) && !empty($site_emission['electricity_emission_factor_percentage'])) ? htmlspecialchars_decode($site_emission['electricity_emission_factor_percentage']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $electricity_emission_factor_percentage['style'] = "cursor: not-allowed;";
                                        $electricity_emission_factor_percentage['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($electricity_emission_factor_percentage); ?><span class="validation_error"><?php echo form_error('electricity_emission_factor_percentage'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_PERCENTAGE; ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel"></label>
                                <div class="form-col-3"></div>
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
                                        'value' => set_value('lpg_emission_factor', ((isset($site_emission['lpg_emission_factor'])) ? htmlspecialchars_decode($site_emission['lpg_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $lpg_emission_factor['style'] = "cursor: not-allowed;";
                                        $lpg_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($lpg_emission_factor); ?><span class="validation_error"><?php echo form_error('lpg_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_LPG, true); ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel"><?php echo lang('natural-gas-emission-factor'); ?> <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $natural_gas_emission_factor = array(
                                        'name' => 'natural_gas_emission_factor',
                                        'id' => 'natural_gas_emission_factor',
                                        'value' => set_value('natural_gas_emission_factor', ((isset($site_emission['natural_gas_emission_factor'])) ? htmlspecialchars_decode($site_emission['natural_gas_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $natural_gas_emission_factor['style'] = "cursor: not-allowed;";
                                        $natural_gas_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($natural_gas_emission_factor); ?><span class="validation_error"><?php echo form_error('natural_gas_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_NATURAL_GAS, true); ?></label>
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
                                        'value' => set_value('district_cooling_emission_factor', ((isset($site_emission['district_cooling_emission_factor'])) ? htmlspecialchars_decode($site_emission['district_cooling_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $district_cooling_emission_factor['style'] = "cursor: not-allowed;";
                                        $district_cooling_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($district_cooling_emission_factor); ?><span class="validation_error"><?php echo form_error('district_cooling_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_DISTRICT_COOLING, true); ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel"><?php echo lang('district-heating-emission-factor'); ?> <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $district_heating_emission_factor = array(
                                        'name' => 'district_heating_emission_factor',
                                        'id' => 'district_heating_emission_factor',
                                        'value' => set_value('district_heating_emission_factor', ((isset($site_emission['district_heating_emission_factor'])) ? htmlspecialchars_decode($site_emission['district_heating_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $district_heating_emission_factor['style'] = "cursor: not-allowed;";
                                        $district_heating_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($district_heating_emission_factor); ?><span class="validation_error"><?php echo form_error('district_heating_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo GetSiteUtilityUnitNameKgCO2e($site_id, EMISSION_FACTOR_UTILITY_DISTRICT_HEATING, true); ?></label>
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="main-label"><?php echo lang('insert-invoice-scan'); ?> <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-6">
                                    <input name="emission_upload[]" id="emission_upload" type="file" class="custom-file-upload form " multiple="">
                                </div>
                                <?php
                                if (isset($site_emission['emission_upload']) && !empty($site_emission['emission_upload'])) {
                                    foreach (explode('|', $site_emission['emission_upload']) as $keyUpload => $valueUpload) {
                                        $extension = substr($valueUpload, -3);
                                        $class = 'emission_image_div';
                                        $name = "emission_upload";
                                        if ($valueUpload != '') {
                                            if ($valueUpload != '') {
                                                if ($extension != 'pdf') {
                                                    $fileName = $image_path = site_url() . "/assets/uploads/" . $valueUpload;
                                                } else {
                                                    $fileName = site_url() . "/assets/uploads/" . $valueUpload;
                                                    $image_path = site_url() . "/assets/uploads/pdf-image.png";
                                                }
                                            } else {
                                                $image_path = site_url() . "/assets/uploads/no-image-available.jpg";
                                            }
                                        } else {
                                            $image_path = site_url() . "/assets/uploads/no-image-available.jpg";
                                        }
                                        echo '<div class="form-col-2 ' . $class . '">
                                    <a class="close delete_emission_image" href="#" data-image="' . $valueUpload . '" data-id="' . $site_emission['site_emission_id'] . '">×</a>
                                    <a href="' . $fileName . '" target="_blank" >
                                        <img class="emission_image ' . $name . '" src="' . $image_path . '"/>
                                    </a>
                                </div>';
                                    }
                                }
                                ?>
                            </div>
                        </li>
                    </ul>
                    <div class="form-btn-outer">
                        <button type="submit" name="emissionFormSubmit" id="emissionFormSubmit" value="1" class="btn btn-secondary btn-submit">Submit</button>
                    </div>
                </div>
                <div id="tab-2" data-tab-id="2">
                    <?php include 'admin_emission_waste_scope_1_2_factors.php'; ?>
                </div>
                <div id="tab-3" data-tab-id="3">
                    <ul class="form-outer-block">
                        <li>
                            <label class="main-label">General Waste to Landfill Emission Factor <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $waste_general_waste_to_landfill_emission_factor = array(
                                        'name' => 'waste_general_waste_to_landfill_emission_factor',
                                        'id' => 'waste_general_waste_to_landfill_emission_factor',
                                        'value' => set_value('waste_general_waste_to_landfill_emission_factor', ((isset($site_emission['waste_general_waste_to_landfill_emission_factor'])) ? htmlspecialchars_decode($site_emission['waste_general_waste_to_landfill_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $waste_general_waste_to_landfill_emission_factor['style'] = "cursor: not-allowed;";
                                        $waste_general_waste_to_landfill_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($waste_general_waste_to_landfill_emission_factor); ?><span class="validation_error"><?php echo form_error('waste_general_waste_to_landfill_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_WASTE_MT; ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel">Recycling Streams Emission Factor <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $waste_recycling_streams_emission_factor = array(
                                        'name' => 'waste_recycling_streams_emission_factor',
                                        'id' => 'waste_recycling_streams_emission_factor',
                                        'value' => set_value('waste_recycling_streams_emission_factor', ((isset($site_emission['waste_recycling_streams_emission_factor'])) ? htmlspecialchars_decode($site_emission['waste_recycling_streams_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $waste_recycling_streams_emission_factor['style'] = "cursor: not-allowed;";
                                        $waste_recycling_streams_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($waste_recycling_streams_emission_factor); ?><span class="validation_error"><?php echo form_error('waste_recycling_streams_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_WASTE_MT; ?></label>
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="main-label">Business Travel - Flights Emission Factor <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $business_travel_flights_emission_factor = array(
                                        'name' => 'business_travel_flights_emission_factor',
                                        'id' => 'business_travel_flights_emission_factor',
                                        'value' => set_value('business_travel_flights_emission_factor', ((isset($site_emission['business_travel_flights_emission_factor'])) ? htmlspecialchars_decode($site_emission['business_travel_flights_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $business_travel_flights_emission_factor['style'] = "cursor: not-allowed;";
                                        $business_travel_flights_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($business_travel_flights_emission_factor); ?><span class="validation_error"><?php echo form_error('business_travel_flights_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_PASSENGER_KM; ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel">Business Travel - Car/Taxi Emission Factor <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $business_travel_car_taxi_emission_factor = array(
                                        'name' => 'business_travel_car_taxi_emission_factor',
                                        'id' => 'business_travel_car_taxi_emission_factor',
                                        'value' => set_value('business_travel_car_taxi_emission_factor', ((isset($site_emission['business_travel_car_taxi_emission_factor'])) ? htmlspecialchars_decode($site_emission['business_travel_car_taxi_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $business_travel_car_taxi_emission_factor['style'] = "cursor: not-allowed;";
                                        $business_travel_car_taxi_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($business_travel_car_taxi_emission_factor); ?><span class="validation_error"><?php echo form_error('business_travel_car_taxi_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_KM; ?></label>
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="main-label">Employee Commuting - Car Emission Factor <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $employee_commuting_car_emission_factor = array(
                                        'name' => 'employee_commuting_car_emission_factor',
                                        'id' => 'employee_commuting_car_emission_factor',
                                        'value' => set_value('employee_commuting_car_emission_factor', ((isset($site_emission['employee_commuting_car_emission_factor'])) ? htmlspecialchars_decode($site_emission['employee_commuting_car_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $employee_commuting_car_emission_factor['style'] = "cursor: not-allowed;";
                                        $employee_commuting_car_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($employee_commuting_car_emission_factor); ?><span class="validation_error"><?php echo form_error('employee_commuting_car_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_KM; ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel">Employee Commuting - Bus Emission Factor <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $employee_commuting_bus_emission_factor = array(
                                        'name' => 'employee_commuting_bus_emission_factor',
                                        'id' => 'employee_commuting_bus_emission_factor',
                                        'value' => set_value('employee_commuting_bus_emission_factor', ((isset($site_emission['employee_commuting_bus_emission_factor'])) ? htmlspecialchars_decode($site_emission['employee_commuting_bus_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $employee_commuting_bus_emission_factor['style'] = "cursor: not-allowed;";
                                        $employee_commuting_bus_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($employee_commuting_bus_emission_factor); ?><span class="validation_error"><?php echo form_error('employee_commuting_bus_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_PASSENGER_KM; ?></label>
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="main-label">Outsourced Laundry Emission Factor <span class="asterisk">*</span></label>
                            <div class="row">
                                <div class="form-col-3">
                                    <?php
                                    $outsourced_laundry_emission_factor = array(
                                        'name' => 'outsourced_laundry_emission_factor',
                                        'id' => 'outsourced_laundry_emission_factor',
                                        'value' => set_value('outsourced_laundry_emission_factor', ((isset($site_emission['outsourced_laundry_emission_factor'])) ? htmlspecialchars_decode($site_emission['outsourced_laundry_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $outsourced_laundry_emission_factor['style'] = "cursor: not-allowed;";
                                        $outsourced_laundry_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($outsourced_laundry_emission_factor); ?><span class="validation_error"><?php echo form_error('outsourced_laundry_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_KG_LAUNDRY; ?></label>
                                </div>
                                <label class="main-label col-sm-4 rightLabel">Purchased Goods Emission Factor <span class="asterisk">*</span></label>
                                <div class="form-col-3">
                                    <?php
                                    $purchased_goods_emission_factor = array(
                                        'name' => 'purchased_goods_emission_factor',
                                        'id' => 'purchased_goods_emission_factor',
                                        'value' => set_value('purchased_goods_emission_factor', ((isset($site_emission['purchased_goods_emission_factor'])) ? htmlspecialchars_decode($site_emission['purchased_goods_emission_factor']) : '')),
                                        'class' => 'input-control decimalcheck'
                                    );
                                    if ($_SESSION['admin']['role_id'] != 1) {
                                        $purchased_goods_emission_factor['style'] = "cursor: not-allowed;";
                                        $purchased_goods_emission_factor['disabled'] = "disabled";
                                    }
                                    ?>
                                    <?php echo form_input($purchased_goods_emission_factor); ?><span class="validation_error"><?php echo form_error('purchased_goods_emission_factor'); ?></span>
                                    <label class="input-label"><?php echo EMISSION_FACTOR_UNIT_PURCHASED_GOODS; ?></label>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="form-btn-outer">
                        <button type="submit" name="emissionFormSubmit" id="emissionFormSubmitTab3" value="1" class="btn btn-secondary btn-submit">Submit</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="year" id="year" value="<?php echo $utilities_year; ?>">
        </div>

        <?php echo form_close(); ?>
    </article>
</div>
<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>
<script type="text/javascript">
    $('.Tab-block').easyResponsiveTabs({

        type: 'default',

        width: 'auto',

        fit: true,

        tabidentify: 'hor_1',

        activate: function (event) {

            // If need on tab change

        }

    });
    $(document).ready(function() {
        var currentYear = (new Date()).getFullYear();
        $('#YearFormat').attr('readonly', 'readonly');
        $('#YearFormat').datepicker({
            format: " yyyy",
            viewMode: "years",
            minViewMode: "years",
            beforeShowYear: function(date) {
                var year = date.getFullYear();
                var disableClass = '';
                if (year >= currentYear) {
                    disableClass = 'disable-year';
                }
                return disableClass;
            },
            endDate: new Date(),
            autoclose: true
        }).on('changeDate', function(ev) {
            var year = ev.date.getFullYear();
            var link = '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/emission/<?php echo $site_param_id; ?>';
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/emission/<?php echo $site_param_id; ?>',
                data: {
                    <?php echo $this->ci()->security->get_csrf_token_name(); ?>: '<?php echo $this->ci()->security->get_csrf_hash(); ?>',
                    year: year
                },
                success: function(data) {
                    ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/emission/<?php echo $site_param_id; ?>', 'ajax_table', '<?php echo $querystr; ?>&year=' + year);
                }
            });
        });

        $('.emission_image').each(function() {
            var isrc = $(this).attr('src');
            if (!(isrc.includes("no-image-available.jpg"))) {
                $(this).parents('.emission_image_div').find('.delete_emission_image').show();
                $(this).parents('.emission_image_div').css('height', '120px');
            } else {
                $(this).parents('.emission_image_div').find('.delete_emission_image').hide();
                $(this).parents('.emission_image_div').css('height', '100px');
            }
        });

        $('.delete_emission_image').click(function() {
            res = confirm('<?php echo lang('delete_confirm_emission_image') ?>');
            if (res) {
                var $this = $(this);
                var siteEmissionId = $this.attr("data-id");
                var imageName = $this.attr("data-image");

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/delete_emission_image',
                    data: {
                        siteEmissionId: siteEmissionId,
                        imageName: imageName
                    },
                    error: function() {
                        return false;
                    },
                    complete: function() {},
                    success: function(data) {
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        });
    });
</script>
