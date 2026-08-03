<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));
echo add_css(array('MonthPicker.min'));
$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$decimal_point = 4;
$current_year = date('Y');
//$baseline_regression_year
?>
<style>
    #occ_span{
     width: 14%;
    float: right;
    height: 41px;
    /*margin-top: -41px;*/
    padding-top: 12px;
    font-size: 15px;
    border-right: 1px lightgray solid;
    border-left: none;
    border-radius: 2px;
    }
</style>
<div id="ajax_table" class="report-detail">
    <article class="card">
	<div class="article-header">
	    <div class="row">
		<div class="col-sm-4"><?php echo lang('title'); ?></div>
		<div class="col-sm-4 text-center"><strong>Regression Analysis : Baseline Year <?php echo $baseline_regression_year; ?> </strong></div>
	    </div>
	</div>
	<div class="card-wrap">
	    <div class="row">
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-heading">
			    <strong>Select Utility</strong>
			</div>
			<div class="panel-body">
			    <form name="form-utility" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'reports_energy' ?>">
				<div class="form-group col-md-4 col-sm-4 col-xs-12">
				    <label class="control-label col-sm-4">
					Choose Utility :
				    </label>
				    <div class="form-dropdown col-sm-8">
					<select name="utility" data-type="custom-dropdown" id="utility_select">
					    <?php if ($site_detail['show_utility_electricity']) { ?>
						<option value="electricity" <?php echo ($utility == 'electricity') ? 'selected' : ''; ?>><?php echo lang('electricity'); ?></option>
					    <?php }if ($site_detail['show_utility_fuel_oil']) { ?>
						<option value="fuel_oil" <?php echo ($utility == 'fuel_oil') ? 'selected' : '' ?>><?php echo lang('fuel_oil'); ?></option>
					    <?php }if ($site_detail['show_utility_lpg']) { ?>
						<option value="lpg" <?php echo ($utility == 'lpg') ? 'selected' : '' ?>><?php echo lang('lpg'); ?></option>
					    <?php }if ($site_detail['show_utility_water']) { ?>
						<option value="water" <?php echo ($utility == 'water') ? 'selected' : '' ?>><?php echo lang('water'); ?></option>
					    <?php }if ($site_detail['show_utility_natural_gas']) { ?>
						<option value="natural_gas" <?php echo ($utility == 'natural_gas') ? 'selected' : '' ?>><?php echo lang('natural_gas'); ?></option>
					    <?php }if ($site_detail['show_utility_district_cooling']) { ?>
						<option value="district_cooling" <?php echo ($utility == 'district_cooling') ? 'selected' : '' ?>><?php echo lang('cooling'); ?></option>
					    <?php }if ($site_detail['show_utility_district_heating']) { ?>
						<option value="district_heating" <?php echo ($utility == 'district_heating') ? 'selected' : '' ?>><?php echo lang('heating'); ?></option>
					    <?php } ?>
					</select>
					<input type="hidden" name="selected_year" value="<?php echo $selected_year; ?>" />
				    </div>
				</div>
				<div class="form-group col-md-4 col-sm-4 col-xs-12">
				    <button type="submit" class="btn btn-secondary btn-submit">Submit</button>
				</div>
			    </form>
			</div>
		    </div>
		</div>
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div class="col-sm-7">
				<div class="col-sm-12" id="previous_year_energy_chart" style="height:700px;">
				    <?php if (sizeof($energy_data) == 1) { ?>
					<div class="table-responsive">
					    <table class="table table-responsive table-striped" >
						<tr>
						    <td><?php echo lang('no-records') ?></td>
						</tr>
					    </table>
					</div>
				    <?php } ?>
				</div>
				<div class="col-sm-12 alert text-center font-18" style="padding: 2px;color: #ff0000;">
				    <strong>
					Regression Equation : <?php echo GetSiteUtilityUnitName($site_id,$utility); ?> =
					<?php echo round($utility_energy_modeling_cur['x'], 2); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['cdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['cdd'], 2) . ' * CDD )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['hdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['hdd'], 2) . ' * HDD )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['occupancy'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['occupancy'], 2) . ' * OCC )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['days'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['days'], 2) . ' * Days of month )' : ''); ?>
				    </strong>
				    <hr/>
				    <strong>R<sup>2</sup> : <?php echo round($utility_energy_modeling_cur['r2'],2);?></strong>
				</div>
			    </div>
			    <div class="col-sm-5">
				<div class="table-responsive">
				    <table class="table table-responsive table-striped">
					<thead>
					<th width="20%">Month</th>
					<th width="20%"><?php echo $utility_array[$utility]['Label'] . ' - ' . ($baseline_regression_year ); ?></th>
					<th width="20%"><?php echo "Regression - " . ($baseline_regression_year ); ?></th>
					<th width="20%"><?php echo "Variation (" . GetSiteUtilityUnitName($site_id,$utility) . ")"; ?></th>
					<th width="20%"><?php echo "Variation (%)"; ?></th>
					</thead>
					<tbody>
					    <?php
					    $index = 1;
					    foreach ($table_data as $key => $energy) {
						?>
						<tr>
						    <td><?php echo $key; ?></td>
						    <td class="text-center"><?php echo number_format($energy['consumtion']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['regression']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['variation']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['precentage']); ?></td>
						</tr>
					    <?php }
					    ?>
					    <tr>
						<th><strong>Total</strong></th>
						<th class="text-center"><strong><?php echo number_format($total_consumption); ?></strong></th>
						<th class="text-center"><strong><?php echo number_format($total_regression); ?></strong></th>
						<th class="text-center"><strong><?php echo number_format($total_consumption - $total_regression); ?></strong></th>
						<th class="text-center"><strong><?php
							$total_variation = (($total_consumption - $total_regression) / $total_consumption) * 100;
							echo number_format($total_variation);
							?></strong></th>
					    </tr>
					</tbody>
				    </table>
				</div>
			    </div>
			</div>
		    </div>
		</div>
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-heading">
			    <strong>Year Wise Regression Analysis</strong>
			</div>
			<div class="panel-body">
			    <form name="form-utility" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'reports_energy' ?>">
				<div class="form-group col-md-4 col-sm-4 col-xs-12">
				    <label class="control-label col-sm-4">
					Select Year
				    </label>
				    <div class="form-dropdown col-sm-8">
					<input type="hidden" name="utility" value="<?php echo $utility ?>" />
					<select name="selected_year" data-type="custom-dropdown" id="utility_select">
					    <?php for($i=$baseline_regression_year; $i<=$current_year; $i++){   ?>
						<option value="<?php echo $i; ?>" <?php echo ($i==$selected_year)?'selected':''; ?> ><?php echo $i; ?></option>
					    <?php } ?>
					</select>
				    </div>
				</div>
				<div class="form-group col-md-4 col-sm-4 col-xs-12">
				    <button type="submit" class="btn btn-secondary btn-submit">Submit</button>
				</div>
			    </form>
			</div>
		    </div>
		</div>
		<div class="col-sm-12">
		    <div class="panel panel-primary">
			<div class="panel-body">
			    <div class="col-sm-7">
				<div class="col-sm-12" id="current_year_energy_chart" style="height:700px;">
				    <?php if (sizeof($energy_data_cur) == 1) { ?>
					<div class="table-responsive col-sm-12">
					    <table class="table table-responsive table-striped" >
						<tr>
						    <td><?php echo lang('no-records') ?></td>
						</tr>
					    </table>
					</div>
				    <?php } ?>
				</div>
				<div class="col-sm-12 alert text-center font-18" style="padding: 2px;color: #ff0000;">
				    <strong>Regression Equation : <?php echo GetSiteUtilityUnitName($site_id,$utility); ?> =
					<?php echo round($utility_energy_modeling_cur['x'], 2); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['cdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['cdd'], 2) . ' * CDD )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['hdd'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['hdd'], 2) . ' * HDD )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['occupancy'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['occupancy'], 2) . ' * OCC )' : ''); ?>
					<?php echo (!empty(round($utility_energy_modeling_cur['days'], 2)) ? ' + ( ' . round($utility_energy_modeling_cur['days'], 2) . ' * Days of month )' : ''); ?>
				    </strong>
				    <hr/>
				    <strong>R<sup>2</sup> : <?php echo round($utility_energy_modeling_cur['r2'],2);?></strong>
				</div>
			    </div>
			    <div class="col-sm-5">
				<div class="table-responsive">
				    <table class="table table-responsive table-striped">
					<thead>
					<th width="20%">Month</th>
					<th width="20%"><?php echo $utility_array[$utility]['Label'] . ' - ' . ($selected_year); ?></th>
					<th width="20%"><?php echo "Regression - " . ($selected_year); ?></th>
					<th width="20%"><?php echo "Variation (" . GetSiteUtilityUnitName($site_id,$utility) . ")"; ?></th>
					<th width="20%"><?php echo "Variation (%)"; ?></th>
					</thead>
					<tbody>
					    <?php
					    $index = 1;
					    foreach ($table_data_cur as $key => $energy) {
						?>
						<tr>
						    <td><?php echo $key; ?></td>
						    <td class="text-center"><?php echo number_format($energy['consumtion']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['regression']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['variation']); ?></td>
						    <td class="text-center"><?php echo number_format($energy['precentage']); ?></td>
						</tr>
					    <?php } ?>
					    <tr>
						<th><strong>Total</strong></th>
						<th class="text-center"><strong><?php echo number_format($total_consumption_cur); ?></strong></th>
						<th class="text-center"><strong><?php echo number_format($total_regression_cur); ?></strong></th>
						<th class="text-center"><strong><?php echo number_format($total_consumption_cur - $total_regression_cur); ?></strong></th>
						<th class="text-center"><strong><?php
							$total_variation_cur = (($total_consumption_cur - $total_regression_cur) / $total_consumption_cur) * 100;
							echo number_format($total_variation_cur);
							?></strong></th>
					    </tr>
					</tbody>
				    </table>
				</div>

			    </div>
			</div>
		    </div>
		</div>
	    </div>
	</div>
	<!--Form added-->
	<div class="card-wrap" style="margin-top: -87px;">
	<!--Horizontal Tab-->
	    <form id="calculateform" class="site-info-form" method="POST">
	    <!-- <form name="form-utility" method="POST" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'reports_energy' ?>"> -->
		<div id="energy-tabs" class="Tab-block">
		    <div class="resp-tabs-container hor_1">
			<div id="tab-1" data-tab-id="1">
			    <div class="panel panel-primary">
				<div class="panel-heading"><strong>Budget Preparation</strong>
				</div>
				<div class="panel-body">
				    <div class="row">
					<div class="col-md-1">
					    <div class="form-group">
						<label class="input-label">CDD</label>
					    </div>
					</div>
					<div class="col-md-4">
					    <input type="text" id="cdd" name="cdd" class="input-control intcheck" value="">
					</div>
				    </div>
				    <div class="row">
					<div class="col-md-1">
					    <div class="form-group">
						<label class="input-label">HDD</label>
					    </div>
					</div>
					<div class="col-md-4">
					    <input type="text" id="hdd" name="hdd" class="input-control intcheck" value="">
					</div>
				    </div>
				    <div class="row">
					<div class="col-md-1">
					    <div class="form-group">
						<label class="input-label">Occ</label>
					    </div>
					</div>
					<div class="col-md-4">
					    <span class="input-control input-group-addon" id="occ_span">%</span>
					    <input type="number" id="occ" name="occ" class="input-control intcheck" style="width: 86%;">

					</div>
				    </div>
				    <div class="row">
					<div class="col-md-1">
					    <div class="form-group">
						<label class="input-label">#days</label>
					    </div>
					</div>
					<div class="col-md-4">
					    <input type="text" id="days" name="days" class="input-control intcheck" value="">
					</div>
				    </div>
				    <div class="row">
					<div class="col-md-5">
					    <div class="form-group" style="text-align:center;">
						<button type='button' class="btn btn-secondary btn-submit" id="calculateRegButton">Calculate Budget</button>
					    </div>
					</div>
				    </div>
				    <div class="row">
					<div class="col-md-1">
					    Result
					</div>
					<div class="col-md-3">
					    <label id="regressionResult" name="regressionResult" class="input-control" disabled>
					</div>
				    </div>
				</div>
			    </div>
			</div>
		    </div>
		</div>
	    </form>
	</div>
    </article>
</div>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>
<script>
    blockUI();
    google.load("visualization", "1", {
	packages: ["corechart"]
    });
    google.setOnLoadCallback(drawChart);

    function drawChart() {

	/*
	 * ********************************************************************************************
	 * google chart configuration for line chart of previous year cosumption vs regession analysis
	 * ********************************************************************************************
	 */
	<?php
	if(sizeof($energy_data) > 1 && isset($energy_data)) {?>
	var data = google.visualization.arrayToDataTable(<?php echo json_encode($energy_data); ?>);
	var options = {
	    height: 700,
	    title: '<?php echo $utility_array[$utility]['Label'] . " - " . "Regression Model" . " - " . ($baseline_regression_year ); ?>',
	    titleTextStyle: {
		fontName: 'Arial',
		fontSize: 20
	    },
	    hAxis: {title: '<?php echo lang("month"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, slantedText: true, slantedTextAngle: 45},
	    vAxes: {
		0: {
		    title: '<?php echo $utility_array[$utility]['Label'] . " (" . GetSiteUtilityUnitName($site_id,$utility) . ")" ?>',
		    titleTextStyle: {
			fontName: 'Arial',
			fontSize: 18
		    },
		    'minValue': 0
		},
	    },
	    series: {
		0: {targetAxisIndex: 0, type: "line", pointShape: 'square', pointSize: 10},
		1: {targetAxisIndex: 0, type: "line", pointShape: 'square', pointSize: 10, color: '#e57e00'},
	    },
	    interpolateNulls: true,
	    legend: {position: 'top', maxLines: 3, textStyle: {fontSize: 18}},
	    chartArea: {'width': '75%'},
	};
	var previous_year_energy_chart = new google.visualization.LineChart(document.getElementById('previous_year_energy_chart'));

	previous_year_energy_chart.draw(data, options);
	<?php } ?>
	/*
	 * ********************************************************************************************
	 * google chart configuration for bar chart of current year cosumption vs regession analysis
	 * ********************************************************************************************
	 */
	<?php if(sizeof($energy_data_cur) > 1 && isset($energy_data_cur)){ ?>
	var data_cur = google.visualization.arrayToDataTable(<?php echo json_encode($energy_data_cur); ?>);
	var options_cur = {
	    height: 700,
	    isStacked: false,
	    title: '<?php echo $utility_array[$utility]['Label'] . " - " . "Actual vs Prediction" . " - " . ($selected_year); ?>',
	    titleTextStyle: {
		fontName: 'Arial',
		fontSize: 20
	    },
	    hAxis: {title: '<?php echo lang("month"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, slantedText: true, slantedTextAngle: 45},
	    vAxes: {
		0: {
		    title: '<?php echo $utility_array[$utility]['Label'] . " (" . GetSiteUtilityUnitName($site_id,$utility) . ")" ?>',
		    titleTextStyle: {
			fontName: 'Arial',
			fontSize: 18
		    },
		    'minValue': 0
		},
	    },
	    series: {
		0: {targetAxisIndex: 0},
		1: {targetAxisIndex: 0, color: '#e57e00'},
	    },
	    interpolateNulls: true,
	    legend: {position: 'top', maxLines: 3, textStyle: {fontSize: 18}},
	    chartArea: {'width': '75%'},
	};
	var current_year_energy_chart = new google.visualization.ColumnChart(document.getElementById('current_year_energy_chart'));

	current_year_energy_chart.draw(data_cur, options_cur);

	<?php } ?>

	unblockUI();
    }
</script>
<script>
$(document).on('click', '#calculateRegButton', function(e){
    e.preventDefault();
    $validate = $("#calculateform").validate({
		rules: {
		    occ: {
			min: 0,
			max: 1
		    }
		}
	    }).form();
    if($validate){

	var cdd = $("#cdd").val();
	var hdd = $("#hdd").val();
	var occ = $("#occ").val();
	var days = $("#days").val();
	var fixX = <?php echo round($utility_energy_modeling_cur['x'],2); ?>;
	var fixCDD = <?php echo round($utility_energy_modeling_cur['cdd'],2); ?>;
	var fixHDD = <?php echo round($utility_energy_modeling_cur['hdd'],2); ?>;
	var fixOCC = <?php echo round($utility_energy_modeling_cur['occupancy'],2); ?>;
	var fixDAYS = <?php echo round($utility_energy_modeling_cur['days'],2); ?>;

	$.ajax({
	    type: "POST",

	    url: "<?php echo base_url() . BASE_ADMIN_URL_CUSTOM . 'reports_energy'.'/calculateRegression' ?>",
	    data: {
		cdd: cdd,
		hdd: hdd,
		occ: occ,
		days: days,
		fixX: fixX,
		fixCDD: fixCDD,
		fixHDD: fixHDD,
		fixOCC: fixOCC,
		fixDAYS: fixDAYS
	    },
	    success: function(result) {
		$('#regressionResult').html(result);
	    },
	});
	}
    return false;
});
</script>