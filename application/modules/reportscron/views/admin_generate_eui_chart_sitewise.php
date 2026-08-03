<script src="<?php echo site_url(); ?>themes/default/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<?php
if (!empty($groupUtilityChartDataArray)) {
    $electricity = $gases = $others = $axisMonth = $budget = [];
    foreach ($groupUtilityChartDataArray as $key => $value) {
        foreach ($value as $k => $val) {
            $axisMonth[] = date("M", mktime(0, 0, 0, $key, 10)) . '-' . $k;
            $electricity[] = $val['electricity'];
            $gases[] = $val['gases'];
            $others[] = $val['others'];
            $budget[] = $val['target'];
        }
    }
}
?>
<div class="article-content">
    <div id="eui_monthly_sitewise" style="height: 500px;margin: 0 auto"></div>
</div>
<script>
    $(document).ready(function() {
        var electricity = gases = others = budget = axisMonth = '';
        <?php if ((isset($electricity) && json_encode($electricity) != '' && !empty($electricity))) { ?>
            electricity = JSON.parse('<?php echo json_encode($electricity); ?>');
        <?php } ?>
        <?php if ((isset($gases) && json_encode($gases) != '' && !empty($gases))) { ?>
            gases = JSON.parse('<?php echo json_encode($gases); ?>');
        <?php } ?>
        <?php if ((isset($others) && json_encode($others) != '' && !empty($others))) { ?>
            others = JSON.parse('<?php echo json_encode($others); ?>');
        <?php } ?>
        <?php if ((isset($budget) && json_encode($budget) != '' && !empty($budget))) { ?>
            budget = JSON.parse('<?php echo json_encode($budget); ?>');
        <?php } ?>
        <?php if ((isset($axisMonth) && json_encode($axisMonth) != '' && !empty($axisMonth))) { ?>
            axisMonth = JSON.parse('<?php echo json_encode($axisMonth); ?>');
        <?php } ?>

        Highcharts.setOptions({
            lang: {
            numericSymbols: null,
            thousandsSep: ','
            }
        });
        const chart = Highcharts.chart('eui_monthly_sitewise', {
            chart: {
                type: 'area'
            },
            title: {
                text: '<?php echo $chartTitle; ?>',
                style: {
                    color: Highcharts.getOptions().colors[1],
                    fontFamily: 'Arial',
                    fontSize: '24px',
                }
            },
            xAxis: {
                crosshair: {
                    enabled: true,
                    width: 2,
                    color: 'brown',
                    dashStyle: 'longdash',
                    fillOpacity: 0.25
                },
                categories: axisMonth,
            },
            yAxis: {
                title: {
                    useHTML: true,
                    text: '<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/RN' ?>' + '</b>',
                    style: {
                        color: Highcharts.getOptions().colors[1],
                        fontFamily: 'Arial',
                        fontSize: '15px',
                        fontWeight: 'bold',
                    }
                }
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                series: {
                    fillOpacity: 0.5,
                },
                area: {
                    stacking: 'normal',
                    lineColor: '#fcfffd',
                    lineWidth: 2,
                    marker: {
                        lineWidth: 2,
                        lineColor: '#fcfffd',
                        symbol: 'circle'
                    }
                }
            },
            series: [{
                name: 'Electricity',
                data: electricity,
                color: '#a8cd41',
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
                        '<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/RN' ?>' + '</b>'
                },
            }, {
                name: 'Gas',
                data: gases,
                color: '#f2a041',
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
                        '<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/RN' ?>' + '</b>'
                },
            }, {
                name: 'Others',
                data: others,
                color: '#3b4255',
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} ' +
                        '<?php echo GetSiteUtilityUnitName($site_id, 'electricity') . '/RN' ?>' + '</b>'
                },
            }, {
                type: 'line',
                name: 'Target',
                data: budget,
                dashStyle: 'longdash',
                lineWidth: 2,
                marker: {
                    radius: 1,
                    symbol: 'circle',
                    lineColor: 'black',
                    fillColor: 'black',
                    enabled: false
                },
                tooltip: {
                    pointFormat: '<b>{series.name} : {point.y:,.2f} </b>'
                },
                color: 'black',
            }]
        });
    });
</script>