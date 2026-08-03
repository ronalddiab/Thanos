<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));

$budget_disable = '';
$current_month = date('m');
$current_year = date('Y');
$current_week = date('W');

if ($current_week != '1' && $role_id != 1 && $current_year >= $utilities_year) {
    $budget_disable = ' disabled="disabled" style="cursor:not-allowed;" ';
}

// pre($utility);
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header"><?php echo lang('utilities-title'); ?> <?php echo "( " . lang('utilities-title-daily') . " ) "; ?></div>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-12">
                        <div style="float: left;margin-right: 19px; margin-top: 2px;">
                            <label><?php echo lang('usage-date'); ?></label>
                            <div class="data-info-block">
                                <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
                            </div>
                            <div class="data-info-block">
                                <input type="text" id="date_id" name="date_id" class='Default' value="<?php echo (!empty($utilities_date) ) ? $utilities_date : ''; ?>">
                                <span class="month-picker-open-button ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only" id="DatePicker_Button_DateFormat" role="button" aria-disabled="false" title="<?php echo lang('open-date'); ?>">
                                    <span class="ui-button-icon-primary ui-icon ui-icon-calculator"></span>
                                    <span class="ui-button-text"><?php echo lang('open-date'); ?></span>						
                                </span>
                                <div class="month-picker ui-widget ui-widget-content ui-corner-all" id="DatePicker_DateFormat" style="display: none;">
                                    <div>
                                        <table class="month-picker-month-table">
                                            <tbody>
                                                <tr>
                                                    <?php
                                                    for ($i = 1; $i < $date_id; $i++) {
                                                        ?>
                                                        <td>
                                                            <button onclick="setDatePicker('<?php echo $i; ?>')"  class="button-1 ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only <?php if ($i == $utilities_date) echo 'ui-state-active'; ?>" role="button" >
                                                                <span class="ui-button-text"><?php echo $i; ?></span>
                                                            </button>
                                                        </td>
                                                        <?php
                                                        if ($i % 5 == 0) {
                                                            ?>
                                                        </tr><tr>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-3">
                        <label class="col-sm-2"><?php echo lang('projects'); ?></label>
                        <div class="form-dropdown col-sm-10">
                    <?php echo form_dropdown('project_id', $projects, $project_id, 'data-type="custom-dropdown"'); ?>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="card-wrap">
            <!--Horizontal Tab-->
            <form id="saveform" class="site-info-form" method="post">
                <div id="energy-tabs" class="Tab-block">
                    <ul class="resp-tabs-list hor_1 clearfix">
                        <li class="tab-custom-id-1"><?php echo lang('tab-occupancy-others'); ?></li>
                    </ul>
                    <div class="resp-tabs-container hor_1">
                        <div id="tab-1" data-tab-id="1">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <ul class="form-outer-block">
                                        <li>
                                            <label class="main-label col-sm-3"><?php echo lang('total-room-night'); ?></label> 
                                            <div class="row">
                                                <div class="form-col-3">
                                                    <input type="text" name="total_room_night" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_room_night']; ?>">
                                                </div>
                                            </div>
                                        </li><li>
                                            <div class="row">
                                                <label class="main-label col-sm-3"><?php echo lang('total-guests'); ?></label> 
                                                <div class="form-col-3">
                                                    <input type="text" name="total_guests" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_guests']; ?>">
                                                </div>
                                            </div>
                                        </li>
                                        <?php if ($site_detail['show_utility_electricity']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('total-electricity-kWh'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_electricity_kwh" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_electricity_kwh']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_electricity_kwh_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_electricity_kwh_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'electricity'); ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php if ($site_detail['show_utility_fuel_oil']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('diesel-fuel'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_diesel_fuel" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_diesel_fuel']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_diesel_fuel_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_diesel_fuel_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'fuel_oil'); ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php /* ?>
                                          <li>
                                          <label class="main-label col-sm-3"><?php echo lang('heavy-fuel'); ?></label>
                                          <div class="row">
                                          <div class="form-col-3">
                                          <input type="text" name="total_heavy_fuel" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_heavy_fuel']; ?>">
                                          <label class="input-label"><?php echo lang('liter'); ?></label>
                                          </div>

                                          <div class="form-col-3">
                                          <input type="text" name="total_heavy_fuel_tariff" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_heavy_fuel_tariff']; ?>">
                                          <label class="input-label"><?php echo lang('liter_rate'); ?></label>
                                          </div>
                                          </div>
                                          </li>
                                          <?php */ ?>
                                        <?php if ($site_detail['show_utility_lpg']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-lpg-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_lpg_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_lpg_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'lpg'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_lpg_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_lpg_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'lpg');; ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php if ($site_detail['show_utility_water']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-water-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_water_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_water_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_water_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_water_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php if ($site_detail['show_utility_irrigation_water']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-landscape-water-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_landscape_water_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_landscape_water_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_landscape_water_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_landscape_water_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water');  ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>
                                        <?php if ($site_detail['show_utility_water_waste']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-waste-water-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_waste_water_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_waste_water_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water');  ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_waste_water_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_waste_water_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water');  ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>

                                        <?php if ($site_detail['show_utility_natural_gas']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-natural-gas-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_natural_gas_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_natural_gas_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_natural_gas_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_natural_gas_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'natural_gas'); ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>

                                        <?php if ($site_detail['show_utility_district_cooling']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-district-cooling-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_district_cooling_consumption" class="input-control intcheck"  maxlength="6" value="<?php echo $utility['total_district_cooling_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'district_cooling'); ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_district_cooling_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_district_cooling_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'district_cooling'); ; ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>

                                        <?php if ($site_detail['show_utility_district_heating']) { ?>
                                            <li>
                                                <label class="main-label col-sm-3"><?php echo lang('label-district-heating-consumption'); ?></label> 
                                                <div class="row">
                                                    <div class="form-col-3">
                                                        <input type="text" name="total_district_heating_consumption" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['total_district_heating_consumption']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'district_heating');  ?></label>
                                                    </div>

                                                    <div class="form-col-3">
                                                        <input type="text" name="total_district_heating_consumption_tariff" class="input-control floatcheck"  maxlength="10" value="<?php echo $utility['total_district_heating_consumption_tariff']; ?>">
                                                        <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'district_heating');  ?></label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php } ?>

                                        <li>
                                            <label class="main-label col-sm-3"><?php echo lang('degree-day'); ?></label> 
                                            <div class="row">
                                                <div class="form-col-3">
                                                    <input type="text" name="cdd" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['cdd']; ?>">
                                                    <label class="input-label"><?php echo lang('cooling'); ?></label>
                                                </div>

                                                <div class="form-col-3">
                                                    <input type="text" name="hdd" class="input-control intcheck"  maxlength="5" value="<?php echo $utility['hdd']; ?>">
                                                    <label class="input-label"><?php echo lang('heating'); ?></label>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="month" name="month" value="<?php echo $utilities_month; ?>" />
                <input type="hidden" id="year" name="year" value="<?php echo $utilities_year; ?>" />
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <input type="hidden" id="date" name="date" class='Default' value="<?php echo (!empty($utilities_date) ) ? $utilities_date : ''; ?>">
                <div class="form-btn-outer">
                    <button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>
                </div>
            </form>
        </div>
    </article>
</div>

<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>
<script type="text/javascript">
    $(document).mouseup(function (e)
    {
        var container = $("#DatePicker_DateFormat");

        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide("slow");
        }
    });
    function setDatePicker(dateValue)
    {
        ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>utilities/daily', 'ajax_table', '<?php echo $querystr; ?>&date=' + dateValue + '&month=' + $("#month").val() + '&year=' + $("#year").val());
    }
    $(document).ready(function () {
        $("#DatePicker_Button_DateFormat").click(function () {
            $("#DatePicker_DateFormat").toggle("slow");
        });

        var monthPickerObj = $("#MonthFormat").MonthPicker({
            'OnAfterChooseMonth': function (date) {
                var month = date.getMonth() + 1;
                var year  = date.getFullYear();

                getDateAjaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>utilities/daily', 'DatePicker_DateFormat', '<?php echo $querystr; ?>&get_date=1&month=' + month + '&year=' + year);
            }
        });

        function getDateAjaxLink(path, elm, params)
        {
            blockUI();
            $.ajax({
                type: "POST",
                url: path,
                data: params,
                success: function (list) {
                    var listData = list.split("###");
                    var month = listData[0];
                    var year  = listData[1];
                    var days  = listData[2];

                    $("#DatePicker_DateFormat").toggle("slow");

                    var selectbox = '<div>';
                    selectbox += '<table class="month-picker-month-table">';
                    selectbox += '<tbody>';
                    selectbox += '<tr>';
                    if (days != 0)
                    {
                        for (var i = 1; i <= days; i++)
                        {
                            selectbox += '<td>';
                            selectbox += '<button class="button-1 ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ';
                            if (i == 1)
                            {
                                selectbox += 'ui-state-active';
                            }
                            selectbox += '" onclick="setDatePicker(\'' + i + '\')" >';
                            selectbox += '<span class="ui-button-text">' + i + '</span>';
                            selectbox += '</button>';
                            selectbox += '</td>';
                            if (i % 5 == 0) {
                                selectbox += '</tr><tr>';
                            }
                        }
                    }
                    selectbox += '</tr>';
                    selectbox += '</tbody>';
                    selectbox += '</table>';
                    selectbox += '</div>';
                    $('#' + elm).html(selectbox);

                    $("#date_id").val(1);
                    $("#date").val(1);
                    $("#month").val(month)
                    $("#year").val(year)

                    unblockUI();
                }
            });
        }

        $('#saveform').on('keydown', '.intcheck', function (event) {
            var key = event.charCode || event.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end
            var keycharcheck = (
                    key == 8 ||
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 190 ||
                    key == 17 ||
                    key == 67 ||
                    key == 86 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));

            if (!keycharcheck) {
                event.preventDefault();
                return false;
            }
        });

        // Allow formate like 123456.1234 only
        $('#saveform').on('keydown', '.floatcheck', function (event) {
            var key = event.charCode || event.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            var keycharcheck = (
                    key == 8 ||
                    key == 9 ||
                    key == 13 ||
                    key == 17 ||
                    key == 46 ||
                    key == 67 ||
                    key == 86 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));

            if (!keycharcheck) {
                event.preventDefault();
                return false;
            }
            
            var keyval = event.which;
            if (keyval != 8 && keyval != 46 && keyval != 17 && keyval != 37 && keyval != 39 && keyval != 190 && keyval != 110 && keyval != 9) {
                var val = this.value;                
                var totalDecimal = 0;
                var ispointval = false;
                for(var i=0;i<val.length;i++){
                    if(ispointval){
                        totalDecimal++;
                    }
                    if(this.value.charAt(i) == "."){
                        ispointval = true;
                    }
                }
                
                if(totalDecimal >= 4){
                    event.preventDefault();
                    return false;
                }
            }
    });

    // Allow formate like 12.12 only
    $('#saveform').on('keydown', '.pricecheck', function (event) {
        var key = event.charCode || event.keyCode || 0;
        // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
        // home, end, period, and numpad decimal
        var keycharcheck = (
                key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                key == 17 ||
                key == 67 ||
                key == 86 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));

        if (!keycharcheck) {
            event.preventDefault();
            return false;
        }

        var keyval = event.which;
        if (keyval != 8 && keyval != 46 && keyval != 17 && keyval != 37 && keyval != 39 && keyval != 190 && keyval != 110 && keyval != 9) {
            var val = this.value;
            var pointval = false;
            var pointcount = 0;
            var charcount = 0;
            var newVal = '';
            var ispointval = this.value.indexOf(".");
            for (var i = 0; i < val.length; i++) {
                if (pointval) {
                    pointcount++;
                }

                charcount++;

                if (val[i] == '.') {
                    pointval = true;
                }

                if (i == 1 && ispointval == '-1') {
                    newVal += val[i] + '.';
                } else {
                    newVal += val[i];
                }
            }

            if (pointval) {
                if (pointcount > 1) {
                    event.preventDefault();
                }
            } else {
                if (charcount > 1) {
                    event.preventDefault();
                }
            }
            this.value = newVal;
        }
    });

    $.validator.setDefaults({
        ignore: []
    });

    $.validator.addClassRules('floatcheck', {
        number: true
    });

    $.validator.addClassRules('pricecheck', {
        number: true
    });

    $("#saveform").validate({
    invalidHandler: function (form, validator) {
    var errors = validator.numberOfInvalids();
            if (errors) {

    validator.validElements().each(function () {
    var tabid = $(this).closest('.resp-tab-content').attr('data-tab-id');
            $('.tab-custom-id-' + tabid).removeClass('error-tab');
    });
            validator.invalidElements().each(function () {
    var tabid = $(this).closest('.resp-tab-content').attr('data-tab-id');
            $('.tab-custom-id-' + tabid).addClass('error-tab');
    });
    }
    }
    });
    }
    );
</script>