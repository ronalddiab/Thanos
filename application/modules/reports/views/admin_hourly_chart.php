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

function array_unique_deep($chartData_titles, $key)
{
    $values = array();
    foreach ($chartData_titles as $k1 => $row)
    {
        foreach ($row as $k2 => $v)
        {
            if ($k2 == $key)
            {
                $values[ $k1 ] = $v;
                continue;
            }
        }
    }
    return array_unique($values);
}

if (!empty($chartData_titles)) 
{
    $title_arr = array_unique_deep($chartData_titles, 'parent');

    if(count($title_arr) == 1)
    {
        foreach ($title_arr as $title_value)
        {
            $title = $title_value; 
        }

        $unit = $utility_array[$title]['Label']." (".$utility_array[$title]['unit'].")";
    }
    else
    {
        $unit = 'Submeters';
    }   
}
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
            <?php echo 'Month to Date submetering'; ?>
        </div>
        <div class="data-info-block-outer">
            <div class="row">
                <div class="col-sm-12">
                    <form name="daily-submission-report" id="daily-submission-report" method="post">
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
                            <input id="genrate-excel" type="button" value="<?php // echo lang('generate-excel'); ?>">
                        </div> -->
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
                        <input type="hidden" id="date" name="date" class='Default' value="<?php echo (!empty($utilities_date) ) ? $utilities_date : ''; ?>">
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
                              
                                <div class="col-sm-12" id="utility_chart_container"style="display: block;">
                                    <div id="utility_chart_saveImage"></div>   
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
           
            $("#utility_chart_container").css({
                "display": "block"
            });
             $('#utility_chart_saveImage').css({
                "display":"block",
            });
             $('#utility_chart_occupancy_saveImage').css({
                "display":"none",
            });     
            drawChart();
        });
        
    });
</script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>
<script>
    blockUI();
    google.load("visualization", "1", {
        packages: ["corechart"]
    });
    google.setOnLoadCallback(drawChart);

    function drawChart() {
<?php if (sizeof($chartData_cdd_hdd) > 1) { ?>
            /*
             * ********************************************************************************************
             * google chart configuration for line chart 
             * ********************************************************************************************
             */
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData_cdd_hdd); ?>);
            var options = {
                height: 700,
				isStacked: true,
                title: 'Utilities Submeters',
                titleTextStyle: {
                    fontName: 'Arial',
                    fontSize: 25
                },
                hAxis: {title: 'Time', titleTextStyle: {fontName: 'Arial', fontSize: 18}, ticks: <?php echo json_encode(range(1, (sizeof($chartData_cdd_hdd) - 1))) ?>},
                vAxes: {
                    0: {
                        title: '<?php echo $unit; ?>', 
                        titleTextStyle: {
                            fontName: 'Arial',
                            fontSize: 24
                        },
                        'minValue': 0
                    },
                },    
                interpolateNulls: true,
                legend: {position: 'top', maxLines: 3, textStyle: {fontSize: 18}},
                chartArea: {'width': '75%'},
                pointSize: 10,
                pointShape: 'square',
            };
            var utility_chart = new google.visualization.LineChart(document.getElementById('utility_chart'));

            google.visualization.events.addListener(utility_chart, 'ready', function () {
                if($('.saveImgUrl').length == 0){
                    var download = document.createElement('a');
                    download.href = utility_chart.getImageURI();
                    download.download = "utility_chart.png";
                    download.onclick = saveAsPng;
                    download.text = 'Save as png';
                    download.className  = 'btn btn-secondary btn-submit saveImgUrl';
                    $('#utility_chart_saveImage').append(download);
                }
            }); 
            utility_chart.draw(data, options);
<?php } ?>

        unblockUI();
    }
</script>