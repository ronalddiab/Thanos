<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<style type="text/css">
    ul.multiselect-container {
        max-height: 240px;
        overflow-y: scroll;
    }
</style>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));
$optioncurrencyvalue = array('currency' => true);
$optionintvalue = array();

$utility_array = [
    'electricity' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'electricity'),
        'Label' => 'Electricity',
    ],
    'fuel_oil' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'fuel_oil'),
        'Label' => 'Fuel Oil',
    ],
    'lpg' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'lpg'),
        'Label' => 'LPG',
    ],
    'water' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'water'),
        'Label' => 'Water',
    ],
    'natural_gas' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'natural_gas'),
        'Label' => 'Natural Gas',
    ],
    'district_cooling' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'district_cooling'),
        'Label' => 'District Cooling',
    ],
    'district_heating' => [
        'unit' => GetSiteUtilityUnitName($site_id, 'district_heating'),
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

                        <?php if (!empty($utilityTitlesArray)) { ?>

                            <div class="col-sm-3 gen-report">

                                <div class="form-dropdown">

									<select name="utility_select[]" class="utility_select" id="utility_select" multiple="multiple">

										<?php foreach ($utilityArray as $id => $utility) { ?>

											<optgroup class="option-dropdown" label="<?php echo $utility_array[$utility]['Label']; ?>" style="background-color: #22A16D;"></optgroup>

											<?php foreach ($utilityTitlesArray[$id] as $title) { ?>

												<option value="<?php echo $title['id']; ?>" <?php echo (in_array($title['id'], $utility_select)) ? 'selected="selected"' : ''; ?>><?php echo $title['title']; ?></option>

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

            <div class=" col-sm-12">

                <div class="card-wrap table-responsive">

                    <div class="panel panel-primary">

                        <div class="panel-body">

                            <div class="col-sm-12">

                                <div class="row pull-right">

                                    <div class="col-sm-12">                                       

                                        <button class="btn btn-secondary btn-submit btn-active" id="cdd-hdd">Utility chart with CDD and HDD</button>

                                        <button class="btn btn-secondary btn-submit" id="occupancy">Utility chart with Occupancy</button>

                                        

                                    </div>

                                </div>



                                <div class="col-sm-12" id="utility_chart_container" style="display: block;">
                                    <div class="col-sm-12" id="utility_chart">

                                        <?php if (sizeof($chartData_cdd_hdd) <= 1) { ?>

                                            <div class="table-responsive">                  

                                                <table class="table table-responsive table-striped" >

                                                    <tr>

                                                        <td><?php echo lang('no-records') ?></td>

                                                    </tr>

                                                </table>

                                            </div>

                                        <?php } ?>

                                    </div>

                                </div>



                                <div class="col-sm-12" id="utility_chart_ocupancy_container" style="display: none;">
                                    <div class="col-sm-12" id="utility_chart_ocupancy">

                                        <?php if (sizeof($chartData_occupancy) <= 1) { ?>

                                            <div class="table-responsive">                  

                                                <table class="table table-responsive table-striped" >

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

                    </div>

                </div>

            </div>

        </div>

    </article>

</div>

<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>
<script>
    drawSingleHighChart();

    function drawSingleHighChart() {
        // blockUI();
        <?php if (sizeof($chartData_cdd_hdd) > 1) { ?>
            var cddHddSingleChartData = '<?php echo json_encode($chartData_cdd_hdd); ?>';
            var cddHddSingleChartArrayData = (cddHddSingleChartData != '' ) ? JSON.parse(cddHddSingleChartData) : '';
            var xAxisCddHddArray = [];
            var cddHddSinglechartSubTitle = [];
            var cddHddSinglechartData = [];
            var yearSelected = '<?php echo $year; ?>';
            var yearSelectedPrevious = '<?php echo $year - 1; ?>';
            var cdd = 'CDD -';
            var cddPrev = 'CDD -';
            var cddyearSelected = cdd.concat(' ', yearSelected);
            var cddyearSelectedPrevious = cddPrev.concat(' ', yearSelectedPrevious);
            var hdd = 'HDD -';
            var hddPrev = 'HDD -';
            var hddyearSelected = hdd.concat(' ', yearSelected);
            var hddyearSelectedPrevious = hddPrev.concat(' ', yearSelectedPrevious);
            cddHddSinglechartSubTitle = cddHddSingleChartArrayData[0];
            cddHddSinglechartSubTitle = cddHddSinglechartSubTitle.filter(value => value !== "Date");
            for (var i = 1; i < cddHddSingleChartArrayData.length; i++) {
                xAxisCddHddArray.push(cddHddSingleChartArrayData[i][0]);
            }
            $.each(cddHddSinglechartSubTitle, function(i) {
                var key = cddHddSinglechartSubTitle[i];
                cddHddSinglechartData[key] = [];
                for (var j = 1; j < cddHddSingleChartArrayData.length; j++) {
                    cddHddSinglechartData[key].push(cddHddSingleChartArrayData[j][i + 1]);
                }
            });
            var series = [];
            Object.entries(cddHddSinglechartData).forEach(([key, value]) => {
                str = (cddHddSinglechartSubTitle[0]);
                str = str.substring(0, str.length - 4);
                if ((key != cddyearSelected && key != cddyearSelectedPrevious && key != hddyearSelected && key != hddyearSelectedPrevious)) {
                    if (key == (str + '<?= $year;?>')) {
                        series.push({
                            pointWidth: 15,
                            name: key,
                            data: cddHddSinglechartData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year]; ?>'
                        }, );
                    }
                    if (key == (str + '<?= $year - 1;?>')) {
                        series.push({
                            pointWidth: 15,
                            name: key,
                            data: cddHddSinglechartData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)]; ?>'
                        }, );
                    }
                } else {
                    if (key == 'CDD - <?php echo $year; ?>') {
                        series.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: cddHddSinglechartData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                        }, );
                    }
                    if (key == 'CDD - <?php echo ($year - 1); ?>') {
                        series.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: cddHddSinglechartData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)] ?>'
                        }, );
                    }
                    if (key == 'HDD - <?php echo $year; ?>') {
                        series.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: cddHddSinglechartData[key],
                            marker: {
                                symbol: 'square',
                                lineWidth: 2,
                                lineColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>',
                                fillColor: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                            },
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year] ?>'
                        }, );
                    }
                    if (key == 'HDD - <?php echo ($year - 1); ?>') {
                        series.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: cddHddSinglechartData[key],
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
            Highcharts.chart('utility_chart', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo $utility_array[$utilityTitles[$utility_select[0]]['parent']]['Label'] . " (" . $utilityTitles[$utility_select[0]]['name'] . ") with Degree Day" ?>',
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
                    text: cddHddSinglechartSubTitle
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: 'Date',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    categories: xAxisCddHddArray,
                    crosshair: true
                },
                yAxis: [{
                    min: 0,
                    title: {
                        text: '<?php echo $utility_array[$utilityTitles[$utility_select[0]]['parent']]['Label'] . " (" . $utilityTitles[$utility_select[0]]['name'] . ")" ?>',
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
                        text: 'Degree Days',
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
        <?php if (isset($chartData_occupancy)) { ?>
            var occupancySingleChartData = '<?php echo json_encode($chartData_occupancy); ?>';
            var occupancySingleChartArrayData = (occupancySingleChartData != '' ) ? JSON.parse(occupancySingleChartData) : '';
            var xAxisOccupancyArray = [];
            var occupancySinglechartSubTitle = [];
            var occupancySinglechartData = [];
            var yearSelectedOccupancy = '<?php echo $year; ?>';
            var yearSelectedPreviousOccupancy = '<?php echo $year - 1; ?>';
            var occupancy = 'Occupancy -';
            var occupancyYearSelected = occupancy.concat(' ', yearSelectedOccupancy);
            var occupancyYearSelectedPrevious = occupancy.concat(' ', yearSelectedPreviousOccupancy);
            occupancySinglechartSubTitle = occupancySingleChartArrayData[0];
            if (typeof occupancySinglechartSubTitle !== 'undefined' && occupancySinglechartSubTitle.length > 0) {
                occupancySinglechartSubTitle = occupancySinglechartSubTitle.filter(value => value !== "Date");
            }
            for (var i = 1; i < occupancySingleChartArrayData.length; i++) {
                xAxisOccupancyArray.push(occupancySingleChartArrayData[i][0]);
            }
            if (typeof occupancySinglechartSubTitle !== 'undefined' && occupancySinglechartSubTitle.length > 0) {
                $.each(occupancySinglechartSubTitle, function(i) {
                    var key = occupancySinglechartSubTitle[i];
                    occupancySinglechartData[key] = [];
                    for (var j = 1; j < occupancySingleChartArrayData.length; j++) {
                        occupancySinglechartData[key].push(occupancySingleChartArrayData[j][i + 1]);
                    }
                });
            }
            var occupancySeries = [];
            Object.entries(occupancySinglechartData).forEach(([key, value]) => {
                if (!(key == occupancyYearSelected || key == occupancyYearSelectedPrevious)) {
                    str = (cddHddSinglechartSubTitle[0]);
                    str = str.substring(0, str.length - 4);
                    if (key == str+ '<?php echo $year; ?>') {
                        occupancySeries.push({
                            pointWidth: 15,
                            name: key,
                            data: occupancySinglechartData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][$year]; ?>'
                        }, );
                    }
                    if (key == str + '<?php echo ($year - 1); ?>') {
                        occupancySeries.push({
                            pointWidth: 15,
                            name: key,
                            data: occupancySinglechartData[key],
                            color: '<?php echo $this->_ci->config->config['chart_legend_colors'][($year - 1)]; ?>'
                        }, );
                    }
                } else {
                    if (key == 'Occupancy - <?php echo $year ?>') {
                        occupancySeries.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: occupancySinglechartData[key],
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
                        occupancySeries.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: occupancySinglechartData[key],
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
            Highcharts.chart('utility_chart_ocupancy', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '<?php echo $utility_array[$utilityTitles[$utility_select[0]]['parent']]['Label'] . " (" . $utilityTitles[$utility_select[0]]['name'] . ") with Degree Day" ?>',
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
                    text: occupancySinglechartSubTitle
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: 'Date',
                        style: {
                            color: Highcharts.getOptions().colors[1],
                            fontFamily: 'Arial',
                            fontSize: '15px',
                            fontWeight: 'bold',
                        }
                    },
                    categories: xAxisOccupancyArray,
                    crosshair: true
                },
                yAxis: [{
                    min: 0,
                    title: {
                        text: '<?php echo $utility_array[$utilityTitles[$utility_select[0]]['parent']]['Label'] . " (" . $utilityTitles[$utility_select[0]]['name'] . ")" ?>',
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
                        text: 'Occupancy',
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
                series: occupancySeries,
            });
        <?php } ?>
        unblockUI();
        return true;
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





        $("#cdd-hdd").on('click', function () {

            $("#cdd-hdd").addClass("btn-active");

            $("#occupancy").removeClass("btn-active");

            $("#utility_chart_ocupancy_container").css({

                "display": "none"

            });

            $("#utility_chart_container").css({

                "display": "block"

            });
            drawSingleHighChart();
        });

        $("#occupancy").on('click', function () {

            $("#occupancy").addClass("btn-active");

            $("#cdd-hdd").removeClass("btn-active");

            $("#utility_chart_ocupancy_container").css({

                "display": "block"

            });

            $("#utility_chart_container").css({

                "display": "none"

            });
            drawSingleHighChart();
        });

    });

</script>
