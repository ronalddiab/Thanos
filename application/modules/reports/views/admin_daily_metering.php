<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));

$optioncurrencyvalue = array('currency' => true);
$optionintvalue = array();

$utility_array = [
    'electricity' => [
        'unit' => GetSiteUtilityUnitName($site_id,'electricity'),
        'Label' => 'Electricity',
    ],
    'fuel_oil' => [
        'unit' => GetSiteUtilityUnitName($site_id,'fuel_oil'),
        'Label' => 'Fuel Oil',
    ],
    'lpg' => [
        'unit' => GetSiteUtilityUnitName($site_id,'lpg'),
        'Label' => 'LPG',
    ],
    'water' => [
        'unit' => GetSiteUtilityUnitName($site_id,'water'),
        'Label' => 'Water',
    ],
    'natural_gas' => [
        'unit' => GetSiteUtilityUnitName($site_id,'natural_gas'),
        'Label' => 'Natural Gas',
    ],
    'district_cooling' => [
        'unit' => GetSiteUtilityUnitName($site_id,'district_coooling'),
        'Label' => 'District Cooling',
    ],
    'district_heating' => [
        'unit' => GetSiteUtilityUnitName($site_id,'district_heating'),
        'Label' => 'District Heating',
    ],
];

?>
<style type="text/css">
    .data-info-block{
        width: 130px;
        display: inline-block;
        position: relative;
    }
</style>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header">
            <?php echo 'Daily Metering'; ?>
        </div>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <form name="daily-submission-report" id="daily-submission-report" method="post" >
                        <div style="float: left;margin-right: 19px; margin-top: 2px;">
                            
                            <div class="col-sm-1 data-info-block">
                                <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($month) && !empty($year)) ? $month . '/' . $year : ''; ?>">
                            </div> 
                            <div class="col-sm-1 data-info-block">
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
                                                            <button onclick="setDatePicker('<?php echo $i; ?>')"  class="button-1 ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only class-<?php echo $i; ?> <?php if ($i == $utilities_date) echo 'ui-state-active'; ?>" role="button" type="button" >
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
                        <!-- <div class="col-sm-2 gen-report">
                            <input  id="genrate-report" type="submit" value="Generate Report">
                        </div>
                        <div class="col-sm-2 gen-report">
                            <input id="genrate-excel" type="button" value="<?php //echo lang('generate-excel'); ?>">
                        </div> -->
                        <?php if(!empty($utilityTitlesArray)){ ?>
                        <div class="col-sm-3 gen-report">
                            <div class="form-dropdown">
                                <select name="utility_select[]" id="utility_select" multiple="multiple" class="utility_select">
                                    <?php foreach ($utilityArray as $id => $utility) { ?>
                                        <optgroup class="option-dropdown" label="<?php echo $utility_array[$utility]['Label']; ?>" style="background-color: #22A16D;"></optgroup>
                                        <?php foreach ($utilityTitlesArray[$id] as $title) { ?>
                                            <option value="<?php echo $title['id']; ?>" <?php echo ($utility_select == $title['id']) ? "selected" : ''; ?>><?php echo $title['title']; ?></option>
                                        <?php }
                                        ?>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2 gen-report">
                            <input id="genrate-chart" name="chart" type="submit" value="Generate Chart">
                        </div>
                        <?php } ?>
                        <input type="hidden" id="view_type" name="view_type" value="" />
                        <input type="hidden" id="month" name="month" value="<?php echo $month; ?>" />
                        <input type="hidden" id="year" name="year" value="<?php echo $year; ?>" />
                        <input type="hidden" id="date" name="date" class='Default' value="<?php echo (!empty($utilities_date) ) ? $utilities_date : ''; ?>">
                    </form>
                </div>
            </div>
        </div>
        <div class="row" > 
            <div class="article-header">
                <?php echo ''; ?>
            </div>
        </div>
        <div class="row" style="display: none;">
            <div class="col-sm-1"></div>
            <div class="col-sm-8">
                <div class="card-wrap table-responsive">
                    <table id="daily-report-table" class="table table-responsive table-bordered table-font-18 padding-8-5">
                        <thead>
                            <tr>
                                <th class="table-border-right table-border-bottom" style="width: 285px;border: 1px solid #fff;">&nbsp;</th>
                                <th style="text-align:center;background:#666;color:#FFF;"><b><?php echo $montharray[$month] . ' - ' . $year; ?></b></th>
                                <th style="text-align:center;background:#666;color:#FFF;"><b><?php echo $montharray[$month] . ' - ' . $last_year; ?></b></th>
                                <th colspan="2" style="text-align:center;background:#666;color:#FFF;"><b>Difference v/s Last Year </b></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class="table-border-right table-border-left table-border-bottom table-border-top-thick" scope="row"><b>DAILY METERING</b></th>
                                <td class="table-border-right table-border-bottom table-border-top-thick" colspan="2"><b># Date - <?php echo $date; ?></b></td>
                                <td class="table-border-bottom table-border-top-thick"><b>Value</b></td>
                                <td class="table-border-right table-border-bottom table-border-top-thick"><b>%</b></td>
                            </tr>

                            <?php
                            $current_year_total = 0;
                            $last_year_total = 0;
                            foreach ($report_data as $utility) {
                                if (empty($utility['submission'])) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <th class="table-border-right table-border-left" scope="row" style="text-align:center;background:#666;color:#FFF;"><?php echo lang('daily_report_title_' . $utility['title']); ?></th>
                                    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>
                                    <td class="table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>
                                    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>
                                    <td class="table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>
                                </tr>

                                <?php

                                foreach ($utility['submission'] as $stitle => $submission) {
                                    ?>
                                    <tr>
                                        <?php
                                        $last_year_deference = 0;
                                        $last_year_percantage = 0;

                                        $last_year_deference = $submission['current_year_total'] - $submission['last_year_total'];
                                        $last_year_percantage = (($last_year_deference * 100) / $submission['current_year_total']);

                                        $current_year_total += $submission['current_year_total'];
                                        $last_year_total += $submission['last_year_total'];
                                        ?>
                                        <th class="table-border-right table-border-left" scope="row"><?php echo $stitle; ?></th>
                                        <td><?php echo number_format($submission['current_year_total']); ?></td>
                                        <td class="table-border-right"><?php echo number_format($submission['last_year_total']); ?></td>
                                        <td><?php echo number_format($last_year_deference); ?></td>
                                        <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-sm-3"></div>
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
        $( ".ui-button-text-only" ).each(function( index ) {
            $( this ).removeClass('ui-state-active');
        });
        $("#date").val(dateValue);      
        $("#date_id").val(dateValue);      
        $("#DatePicker_DateFormat").toggle("slow"); 
        $('.class-'+dateValue).addClass('ui-state-active');
    }

    $(document).ready(function () {
        $("#DatePicker_Button_DateFormat").click(function () {
            $("#DatePicker_DateFormat").toggle("slow");
        });

        /* 
        var monthPickerObj = $("#MonthFormat").MonthPicker({
            'OnAfterChooseMonth': function (date) {
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                $('#month').val(month);
                $('#year').val(year);
            }
        }); */

        var monthPickerObj = $("#MonthFormat").MonthPicker({
            'OnAfterChooseMonth': function (date) {
                var month = date.getMonth() + 1;
                var year = date.getFullYear();

                $('#month').val(month);
                $('#year').val(year);
            }
        });

        $("#genrate-excel").click(function () {
            $("#view_type").val('excel');
            $("#daily-submission-report").submit();
        });

        $("#genrate-report").click(function () {
            $("#view_type").val('');
            $("#daily-submission-report").submit();
        });
    });
</script>