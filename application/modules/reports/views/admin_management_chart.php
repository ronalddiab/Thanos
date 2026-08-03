<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));

$optioncurrencyvalue = array('currency' => true);
$optionintvalue = array();

$is_report_data = false;
$monthly_report_data_array = array();
$monthly_report_data_array[] = array(
    lang("utilities"),
    $montharray[$month] . ' - ' . $year,
    $montharray[$month] . ' - ' . $last_year,
    'Budget',
    lang("occupancy") . "-" . $year,
    lang("occupancy") . "-" . $last_year
);

if ($utility_select && $utility_select == 'mtd') {
    if (!empty($data['utility_key_array'])) {
        foreach ($data['utility_key_array'] as $key => $value) {

            $current_year_cost = $data['current_year']['total_' . $value['db_key'] . '_cost'];
            $previous_year_cost = $data['previous_year']['total_' . $value['db_key'] . '_cost'];
            $current_year_budget = $data['current_year'][$value['budget_key'] . '_cost'];
            $current_year_occupancy = $data['current_year']['occupancy'];
            $previous_year_occupancy = $data['previous_year']['occupancy'];

            $utility_array = array();
            $utility_array[] = $value['title'];
            $utility_array[] = (isset($current_year_cost) && !empty($current_year_cost)) ? round($current_year_cost, 2) : 0;
            $utility_array[] = (isset($previous_year_cost) && !empty($previous_year_cost)) ? round($previous_year_cost, 2) : 0;
            $utility_array[] = (isset($current_year_budget) && !empty($current_year_budget)) ? round($current_year_budget, 2) : 0;
            $utility_array[] = (isset($current_year_occupancy) && !empty($current_year_occupancy)) ? round($current_year_occupancy, 2) : 0;
            $utility_array[] = (isset($previous_year_occupancy) && !empty($previous_year_occupancy)) ? round($previous_year_occupancy, 2) : 0;

            $monthly_report_data_array[] = $utility_array;
        }
        $is_report_data = true;
        $is_utility = false;
    }
} else {
    $monthly_report_data_array = $utility_daily_data;
    $is_report_data = true;
    $is_utility = TRUE;
}
?>
<div id="ajax_table" class="utilities-detail-wrap">
    <article class="card">
        <div class="article-header"><?php echo lang('10-days-management-report'); ?></div>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <div class="col-sm-12">
                        <form name="management-report" id="management-report" method="post">
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
                            <div class="form-group col-md-2 col-sm-3 col-xs-12">
                                <div class="form-dropdown">
                                    <select name="utility_select" data-type="custom-dropdown" id="utility_select">
                                        <option value="mtd" <?php echo ($utility_select == 'mtd') ? 'selected' : '' ?>><?php echo lang('moth_to_data'); ?></option>
                                        <?php if ($site_detail['show_utility_electricity']) { ?>
                                            <option value="electricity" <?php echo ($utility_select == 'electricity') ? 'selected=' : ''; ?>><?php echo lang('electricity'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_fuel_oil']) { ?>
                                            <option value="fuel_oil" <?php echo ($utility_select == 'fuel_oil') ? 'selected' : '' ?>><?php echo lang('fuel_oil'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_lpg']) { ?>
                                            <option value="lpg" <?php echo ($utility_select == 'lpg') ? 'selected' : '' ?>><?php echo lang('lpg'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_water']) { ?>
                                            <option value="water" <?php echo ($utility_select == 'water') ? 'selected' : '' ?>><?php echo lang('water'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_irrigation_water']) { ?>
                                            <option value="water_irrigation" <?php echo ($utility_select == 'water_irrigation') ? 'selected' : '' ?>><?php echo lang('irrigation_water'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_natural_gas']) { ?>
                                            <option value="natural_gas" <?php echo ($utility_select == 'natural_gas') ? 'selected' : '' ?>><?php echo lang('natural_gas'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_district_cooling']) { ?>
                                            <option value="cooling" <?php echo ($utility_select == 'cooling') ? 'selected' : '' ?>><?php echo lang('cooling'); ?></option>
                                        <?php }
                                        if ($site_detail['show_utility_district_heating']) { ?>
                                            <option value="heating" <?php echo ($utility_select == 'heating') ? 'selected' : '' ?>><?php echo lang('heating'); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 gen-report">
                                <input id="genrate-chart" type="button" value="<?php echo lang('graphical-view'); ?>">
                            </div>
                            <input type="hidden" id="view_type" name="view_type" value="" />
                            <input type="hidden" id="month" name="month" value="<?php echo $month; ?>" />
                            <input type="hidden" id="year" name="year" value="<?php echo $year; ?>" />
                        </form>
                        <div class="col-sm-2 gen-report" id="export">
                            <form id="exportForm" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/export" method="POST">
                                <input type="hidden" name="month" value="" id="exportMonth"/>
                                <input type="hidden" name="year" value="" id="exportYear"/>
                                <button type="submit" name="submit" value="<?php echo lang('utilities-daily-export'); ?>" id="export-button" class="btn btn-custom btn-yellow"><?php echo lang('utilities-daily-export'); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <article class="card">
            <div class="article-content">
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="year">
                        <?php if ($is_utility) { ?>
                        <div class="row pull-right">
                            <button class="btn btn-secondary btn-submit btn-active" id="cdd-hdd">Utility chart with CDD and HDD</button>
                            <button class="btn btn-secondary btn-submit" id="occupancy">Utility chart with Occupancy</button>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-sm-12" id="utilities_container">
                                <div style="width: 100%; height: 700px;" id="monthly_report_chart">
                                    <?php if (!$is_report_data) { ?>
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
                        <div class="row">
                            <?php if ($is_utility) { ?>
                                <div class="col-sm-12" id="occupancy_container" style="height:0px;opacity:0;">
                                    <div style="width: 100%; height: 700px;" id="monthly_report_chart_occupancy">
                                        <?php if (!$is_report_data) { ?>
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
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </article>
</div>
<script>
    monthToReportHighchart();

    function monthToReportHighchart() {
        <?php if ($is_report_data && $utility_select == 'mtd') { ?>
            var utilityMonthlyReportArray = '<?php echo json_encode($monthly_report_data_array); ?>';
            var utilityMonthlyArrayData = JSON.parse(utilityMonthlyReportArray);
            var chartMonthTitle = utilityMonthlyArrayData[0];
            chartMonthTitle = chartMonthTitle.filter(value => value !== "Utilities");
            var chartMonthData = [];
            var monthTitle = [];
            var occupancy = 'Occupancy-';
            var currentYear = '<?php echo $year ?>';
            var previousYear = '<?php echo $year - 1 ?>';
            var occupancyYearSelected = occupancy.concat('', currentYear);
            var occupancyYearSelectedPrevious = occupancy.concat('', previousYear);
            $.each(chartMonthTitle, function(i) {
                var key = chartMonthTitle[i];
                chartMonthData[key] = [];
                for (var j = 1; j < utilityMonthlyArrayData.length; j++) {
                    chartMonthData[key].push(utilityMonthlyArrayData[j][i + 1]);
                }
            });
            for (var i = 1; i < utilityMonthlyArrayData.length; i++) {
                monthTitle.push(utilityMonthlyArrayData[i][0]);
            }
            var series = [];
            Object.entries(chartMonthData).forEach(([key, value]) => {
                if (!(key == occupancyYearSelected || key == occupancyYearSelectedPrevious)) {
                    series.push({
                        name: key,
                        data: chartMonthData[key]
                    }, );
                } else {
                    series.push({
                        type: 'spline',
                        name: key,
                        yAxis: 1,
                        data: chartMonthData[key],
                        marker: {
                            symbol: 'square',
                            lineWidth: 2,
                            lineColor: key == occupancyYearSelected ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                            fillColor: key == occupancyYearSelected ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                        },
                        color: key == occupancyYearSelected ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                    }, );
                }
            });
            Highcharts.chart('monthly_report_chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo lang("10-days-management-report"); ?>',
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
                subtitle: {
                    text: chartMonthTitle
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: '<?php echo lang("utilities"); ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    categories: monthTitle,
                    crosshair: true
                },
                yAxis: [{
                    min: 0,
                    max: 600000,
                    tickInterval: 100000,
                    tickAmount: 8,
                    title: {
                        text: '<?php echo lang("utility-cost-chart-yaxis-0-title"); ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '24px',
                            fontWeight: 'bold',
                        }
                    }
                }, {
                    min: 0,
                    max: 100,
                    tickInterval: 10,
                    title: {
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
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                },
                series: series,
            });
        <?php } ?>
        <?php if ($is_report_data && $utility_select !== 'mtd') {    ?>
            var utilityDailyMonth = '<?php echo json_encode($monthly_report_data_array); ?>';
            var utilityMonthDailyArrayData = JSON.parse(utilityDailyMonth);
            var chartUtilityTitle = utilityMonthDailyArrayData[0];
            chartUtilityTitle = chartUtilityTitle.filter(value => value !== "Date");
            var chartDailyMonthData = [];
            var dateArray = [];
            var yearSelected = '<?php echo $year; ?>';
            var yearSelectedPrevious = '<?php echo $year - 1; ?>';
            var cdd = 'CDD -';
            var cddyearSelected = cdd.concat(' ', yearSelected);
            var cddyearSelectedPrevious = cdd.concat(' ', yearSelectedPrevious);
            var hdd = 'HDD -';
            var hddyearSelected = hdd.concat(' ', yearSelected);
            var hddyearSelectedPrevious = hdd.concat(' ', yearSelectedPrevious);
            $.each(chartUtilityTitle, function(i) {
                var key = chartUtilityTitle[i];
                chartDailyMonthData[key] = [];
                for (var j = 1; j < utilityMonthDailyArrayData.length; j++) {
                    chartDailyMonthData[key].push(utilityMonthDailyArrayData[j][i + 1]);
                }
            });
            for (var i = 1; i < utilityMonthDailyArrayData.length; i++) {
                dateArray.push(utilityMonthDailyArrayData[i][0]);
            }
            var seriesDailyChart = [];
            Object.entries(chartDailyMonthData).forEach(([key, value]) => {
                if (!(key == cddyearSelected || key == cddyearSelectedPrevious || key == hddyearSelected || key == hddyearSelectedPrevious)) {
                    if (key == '<?php echo $montharray[$month] . ' - ' . $year ?>') {
                        seriesDailyChart.push({
                            name: key,
                            data: chartDailyMonthData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year]; ?>'
                        }, );
                    }
                    if (key == '<?php echo $montharray[$month] . ' - ' . ($year - 1) ?>') {
                        seriesDailyChart.push({
                            name: key,
                            data: chartDailyMonthData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)]; ?>'
                        }, );
                    }
                } else {
                    if (key == 'CDD - <?php echo $year ?>') {
                        seriesDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartDailyMonthData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                        }, );
                    }
                    if (key == 'CDD - <?php echo ($year - 1) ?>') {
                        seriesDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartDailyMonthData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                        }, );
                    }
                    if (key == 'HDD - <?php echo $year ?>') {
                        seriesDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartDailyMonthData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                        }, );
                    }
                    if (key == 'HDD - <?php echo ($year - 1) ?>') {
                        seriesDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartDailyMonthData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                        }, );
                    }
                }
            });
            Highcharts.chart('monthly_report_chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo "Daily Utility Report - " . $selected_utility_key_array['title']; ?>',
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
                subtitle: {
                    text: chartUtilityTitle
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: '<?php echo "Date"; ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    categories: dateArray,
                    crosshair: true
                },
                yAxis: [{
                    min: 0,
                    title: {
                        text: '<?php echo $selected_utility_key_array['title'] . " (" . $selected_utility_key_array['unit'] . ")"; ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    }
                }, {
                    min: 0,
                    tickInterval: 5,
                    tickAmount: 10,
                    title: {
                        text: '<?php echo "Degree Day"; ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                },
                series: seriesDailyChart
            });
        <?php } ?>
        <?php if ($is_utility) { ?>
            var utilityData = '<?php echo json_encode($utility_daily_data_occupancy); ?>';
            var utilityDataOccupancy = (utilityData != '' ) ? JSON.parse(utilityData) : '';
            var chartDataOccupancyTitle = utilityDataOccupancy[0];
            if (typeof chartDataOccupancyTitle !== 'undefined' && chartDataOccupancyTitle.length > 0) {
            chartDataOccupancyTitle = chartDataOccupancyTitle.filter(value => value !== "Date");
            }
            var chartOccupancyData = [];
            var dateOccupancyArray = [];
            var utilityOccupancy = 'Occupancy -';
            var currentYear = '<?php echo $year ?>';
            var previousYear = '<?php echo $year - 1 ?>';
            var occupancyUtilityYearSelected = utilityOccupancy.concat(' ', currentYear);
            var occupancyUtilityYearSelectedPrevious = utilityOccupancy.concat(' ', previousYear);
            if (typeof chartDataOccupancyTitle !== 'undefined' && chartDataOccupancyTitle.length > 0) {
            $.each(chartDataOccupancyTitle, function(i) {
                var key = chartDataOccupancyTitle[i];
                chartOccupancyData[key] = [];
                for (var j = 1; j < utilityDataOccupancy.length; j++) {
                    chartOccupancyData[key].push(utilityDataOccupancy[j][i + 1]);
                }
            });
            }
            for (var i = 1; i < utilityDataOccupancy.length; i++) {
                dateOccupancyArray.push(utilityDataOccupancy[i][0]);
            }
            var seriesOccupancyDailyChart = [];
            Object.entries(chartOccupancyData).forEach(([key, value]) => {
                if (!(key == occupancyUtilityYearSelected || key == occupancyUtilityYearSelectedPrevious)) {
                    if (key == '<?php echo $montharray[$month] . ' - ' . $year ?>') {
                        seriesOccupancyDailyChart.push({
                            name: key,
                            data: chartOccupancyData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year]; ?>'
                        }, );
                    }
                    if (key == '<?php echo $montharray[$month] . ' - ' . ($year - 1) ?>') {
                        seriesOccupancyDailyChart.push({
                            name: key,
                            data: chartOccupancyData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)]; ?>'
                        }, );
                    }
                } else {
                    if (key == 'Occupancy - <?php echo $year ?>') {
                        seriesOccupancyDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartOccupancyData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                        }, );
                    }
                    if (key == 'Occupancy - <?php echo ($year - 1) ?>') {
                        seriesOccupancyDailyChart.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartOccupancyData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                        }, );
                    }
                }
            });
            Highcharts.chart('monthly_report_chart_occupancy', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo "Daily Utility Report - " . $selected_utility_key_array['title']; ?> ',
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
                subtitle: {
                    text: chartDataOccupancyTitle
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: '<?php echo "Date"; ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    categories: dateOccupancyArray,
                    crosshair: true
                },
                yAxis: [{
                    min: 0,
                    title: {
                        text: '<?php echo $selected_utility_key_array['title'] . " (" . $selected_utility_key_array['unit'] . ")"; ?>',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    }
                }, {
                    min: 0,
                    tickInterval: 5,
                    tickAmount: 10,
                    title: {
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
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                },
                series: seriesOccupancyDailyChart
            });
        <?php } ?>
    }
</script>

<script type="text/javascript">
    $(document).mouseup(function(e) {
        var container = $("#DatePicker_DateFormat");

        if (!container.is(e.target) // if the target of the click isn't the container...
            &&
            container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide("slow");
        }
    });

    $(document).ready(function () {

        $("#cdd-hdd").on('click', function () {
            $("#cdd-hdd").addClass("btn-active");
            $("#occupancy").removeClass("btn-active");
            $("#occupancy_container").css({
                "height":"0px",
                "opacity": "0"
            });
            $("#monthly_report_chart").css({
                "height":"700px",
                "opacity": "1"
            });
            $("#monthly_report_chart").css({
                "height": "700px",
                "opacity": "1",
            });
        });
        $("#occupancy").on('click', function () {
            $("#occupancy").addClass("btn-active");
            $("#cdd-hdd").removeClass("btn-active");
            $("#occupancy_container").css({
                "height":"700px",
                "opacity": "1"
            });
            $("#monthly_report_chart").css({
                "height": "0px",
                "opacity": "0",
            });
        });

        var cur_date = new Date();
        var cur_month = cur_date.getMonth() + 1;
        var cur_year = cur_date.getFullYear();

        if ($('#month').val() >= cur_month && $('#year').val() >= cur_year) {
            $('#export').addClass('hidden');
        } else {
            $('#export').removeClass('hidden');
        }

        $('#export-button').on('click', function () {
            $("#exportMonth").val($("#month").val());
            $("#exportYear").val($("#year").val());
            $('#exportForm').submit();
        });

        $("#DatePicker_Button_DateFormat").click(function () {
            $("#DatePicker_DateFormat").toggle("slow");
        });

        var monthPickerObj = $("#MonthFormat").MonthPicker({
            'OnAfterChooseMonth': function (date) {
                var month = date.getMonth() + 1;
                var year = date.getFullYear();
                $('#month').val(month);
                $('#year').val(year);


                if (month >= cur_month && year >= cur_year) {
                    $('#export').addClass('hidden');
                } else {
                    $('#export').removeClass('hidden');
                }
            }
        });

        $("#genrate-excel").click(function () {
            $("#view_type").val('excel');
            $("#management-report").submit();
        });

        $("#genrate-report").click(function () {
            $("#view_type").val('');
            $("#management-report").submit();
        });

        $("#genrate-chart").click(function () {
            $("#view_type").val('chart');
            $("#management-report").submit();
        });
    });
</script>