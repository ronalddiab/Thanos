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

$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

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

function array_unique_deep($chartData_titles, $key)
{
    $values = array();
    foreach ($chartData_titles as $k1 => $row) {
        foreach ($row as $k2 => $v) {
            if ($k2 == $key) {
                $values[$k1] = $v;
                continue;
            }
        }
    }
    return array_unique($values);
}

if (!empty($chartData_titles)) {
    $title_arr = array_unique_deep($chartData_titles, 'parent');

    if (count($title_arr) == 1) {
        foreach ($title_arr as $title_value) {
            $title = $title_value;
        }

        $unit = $utility_array[$title]['Label'] . " (" . $utility_array[$title]['unit'] . ")";
    } else {
        $unit = 'Submeters';
    }
}

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
    drawHighchart();

    function drawHighchart() {
        //Utility Highchart Data Functionality
        <?php if (sizeof($chartData_cdd_hdd) > 1) { ?>
            var cddHddData = '<?php echo json_encode($chartData_cdd_hdd); ?>';
            if (cddHddData != '') {
                var cddHddArrayData = JSON.parse(cddHddData);
                var xAxisArray = [];
                var chartSubTitle = [];
                var chartData = [];
                chartSubTitle = cddHddArrayData[0];
                chartSubTitle = chartSubTitle.filter(value => value !== "Date");
                $.each(chartSubTitle, function(i) {
                    var key = chartSubTitle[i];
                    chartData[key] = [];
                    for (var j = 1; j < cddHddArrayData.length; j++) {
                        chartData[key].push(cddHddArrayData[j][i + 1]);
                    }
                });
                for (var i = 1; i < cddHddArrayData.length; i++) {
                    xAxisArray.push(cddHddArrayData[i][0]);
                }
                var series = [];
                Object.entries(chartData).forEach(([key, value]) => {
                    if (key !== 'CDD' && key !== 'HDD')
                        series.push({
                            name: key,
                            data: chartData[key]
                        });
                    if (key == 'CDD' || key == 'HDD') {
                        series.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartData[key],
                            marker: {
                                lineWidth: 2,
                                symbol: 'square',
                                lineColor: key == 'HDD' ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                                fillColor: key == 'HDD' ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                            },
                            color: key == 'HDD' ? Highcharts.getOptions().colors[0] : Highcharts.getOptions().colors[1],
                        });
                    }
                });
                Highcharts.chart('utility_chart', {
                    chart: {
                        type: 'column',
                        height: 600,
                    },
                    title: {
                        text: 'Utilities submeters with Degree Day',
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
                    xAxis: [{
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
                        categories: xAxisArray
                    }],
                    yAxis: [{
                        min: 0,
                        title: {
                            text: '<?php echo $unit; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                        // tickInterval: 2000,
                        // tickAmount: 10,
                    }, {
                        min: 0,
                        title: {
                            text: 'Degree Days',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                        tickInterval: 5,
                        tickAmount: 10,
                        opposite: true,
                    }],
                    tooltip: {
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal'
                        }
                    },
                    series: series,
                });
            }
        <?php } ?>
        // Occupancy Chart Data Functionality
        <?php if (sizeof($chartData_occupancy) > 1) { ?>
            var occupancyData = '<?php echo json_encode($chartData_occupancy); ?>';
            if (occupancyData != '') {
                var occupancyArrayData = JSON.parse(occupancyData);
                var chartSubTitleOccupancy = [];
                var chartDataOccupancy = [];
                chartSubTitleOccupancy = occupancyArrayData[0];
                chartSubTitleOccupancy = chartSubTitleOccupancy.filter(value => value !== "Date");
                $.each(chartSubTitleOccupancy, function(i) {
                    var key = chartSubTitleOccupancy[i];
                    chartDataOccupancy[key] = [];
                    for (var j = 1; j < occupancyArrayData.length; j++) {
                        chartDataOccupancy[key].push(occupancyArrayData[j][i + 1]);
                    }
                });
                var xAxisArrayOccupancy = [];
                for (var i = 1; i < occupancyArrayData.length; i++) {
                    xAxisArrayOccupancy.push(occupancyArrayData[i][0]);
                }
                var seriesOccupancy = [];
                Object.entries(chartDataOccupancy).forEach(([key, value]) => {
                    if (key !== 'Occupancy') {
                        seriesOccupancy.push({
                            name: key,
                            data: chartDataOccupancy[key]
                        }, );
                    }
                    if (key == 'Occupancy') {
                        seriesOccupancy.push({
                            type: 'spline',
                            name: key,
                            yAxis: 1,
                            data: chartDataOccupancy[key],
                            marker: {
                                lineWidth: 2,
                                symbol: 'square',
                                lineColor: Highcharts.getOptions().colors[0],
                                fillColor: Highcharts.getOptions().colors[0],
                            },
                            color: Highcharts.getOptions().colors[0],
                        });
                    }
                });
                Highcharts.chart('utility_chart_ocupancy', {
                    chart: {
                        type: 'column',
                        height: 600,
                    },
                    title: {
                        text: 'Utilities submeters with Occupancy',
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
                    xAxis: [{
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
                        categories: xAxisArrayOccupancy
                    }],
                    yAxis: [{
                        min: 0,
                        title: {
                            text: '<?php echo $unit; ?>',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        }
                    }, {
                        min: 0,
                        title: {
                            text: 'Occupancy',
                            style: {
                                color: Highcharts.getOptions().colors[1],
                                fontFamily: 'Arial',
                                fontSize: '15px',
                                fontWeight: 'bold',
                            }
                        },
                        opposite: true,
                    }],
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                        }
                    },
                    series: seriesOccupancy,
                });
            }

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
            drawHighchart();
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
            drawHighchart();
        });
    });
</script>
