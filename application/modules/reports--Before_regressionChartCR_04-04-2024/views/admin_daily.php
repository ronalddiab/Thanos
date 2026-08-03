<style type="text/css">
ul.multiselect-container{
    max-height: 240px;
    overflow-y: scroll;
}
</style>
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
        'unit' => GetSiteUtilityUnitName($site_id,'district_cooling'),
        'Label' => 'District Cooling',
    ],
    'district_heating' => [
        'unit' => GetSiteUtilityUnitName($site_id,'district_heating'),
        'Label' => 'District Heating',
    ],
];
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header">
            <?php echo 'Month to Date submetering'; ?>
        </div>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <form name="daily-submission-report" id="daily-submission-report" method="post">
                        <div style="float: left;margin-right: 19px; margin-top: 2px;">
                            <label><?php echo lang('usage-date'); ?></label>
                            <div class="data-info-block">
                                <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($month) && !empty($year)) ? $month . '/' . $year : ''; ?>">
                            </div>
                        </div>		
                        <div class="col-sm-2 gen-report">
                            <input  id="genrate-report" type="submit" value="Generate Report">
                        </div>
                        <div class="col-sm-2 gen-report">
                            <input id="genrate-excel" type="button" value="<?php echo lang('generate-excel'); ?>">
                        </div>
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
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
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
                                <th class="table-border-right table-border-left table-border-bottom table-border-top-thick" scope="row"><b>MONTH TO DATE SUBMETERING</b></th>
                                <td class="table-border-right table-border-bottom table-border-top-thick" colspan="2"><b># Days - <?php echo $to_date; ?></b></td>
                                <td class="table-border-bottom table-border-top-thick"><b>Value</b></td>
                                <td class="table-border-right table-border-bottom table-border-top-thick"><b>%</b></td>
                            </tr>
                            <tr>
                                <?php
                                $last_year_deference = 0;
                                $last_year_percantage = 0;

                                $last_year_deference = $current_year_static_data['total_room_night'] - $last_year_static_data['total_room_night'];
                                $last_year_percantage = ($current_year_static_data['total_room_night'] != '') ? (($last_year_deference * 100) / $current_year_static_data['total_room_night']) : 0;
                                ?>
                                <th class="table-border-right table-border-left" scope="row">Room Nights</th>
                                <td><?php echo number_format($current_year_static_data['total_room_night']); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_static_data['total_room_night']); ?></td>
                                <td><?php echo number_format($last_year_deference); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>
                            </tr>
                            <tr>
                                <?php
                                $last_year_deference = 0;
                                $last_year_percantage = 0;

                                $last_year_deference = $current_year_static_data['cdd'] - $last_year_static_data['cdd'];
                                $last_year_percantage = ($current_year_static_data['cdd'] != '') ? (($last_year_deference * 100) / $current_year_static_data['cdd']) : 0;
                                ?>
                                <th class="table-border-right table-border-left" scope="row">CDD</th>
                                <td><?php echo number_format($current_year_static_data['cdd']); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_static_data['cdd']); ?></td>
                                <td><?php echo number_format($last_year_deference); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>
                            </tr>
                            <tr>
                                <?php
                                $last_year_deference = 0;
                                $last_year_percantage = 0;

                                $last_year_deference = $current_year_static_data['hdd'] - $last_year_static_data['hdd'];
                                $last_year_percantage = ($current_year_static_data['hdd'] != '') ? (($last_year_deference * 100) / $current_year_static_data['hdd']) : 0;
                                ?>
                                <th class="table-border-right table-border-left" scope="row">HDD</th>
                                <td><?php echo number_format($current_year_static_data['hdd']); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_static_data['hdd']); ?></td>
                                <td><?php echo number_format($last_year_deference); ?></td>
                                <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>
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
                                        $last_year_percantage = ($submission['current_year_total'] != '') ? (($last_year_deference * 100) / $submission['current_year_total']) : 0;

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

    $(document).ready(function () {
        $("#DatePicker_Button_DateFormat").click(function () {
            $("#DatePicker_DateFormat").toggle("slow");
        });

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