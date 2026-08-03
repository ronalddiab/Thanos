<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts-more.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/accessibility.js"></script>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min', 'bootstrap-datepicker-new'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new'));

$report_types_list = array(
    'water_liters_per_room_night' => lang('water_consumption')." (".GetSiteUtilityUnitName($site_id,'water').") ".lang('per_room_night'),
    'electricity_kwh_per_room_night' => lang('electricity_consumption')." (".GetSiteUtilityUnitName($site_id,'electricity').") ".lang('per_room_night'),
    'average_kwh_tariff' => lang('average')." (".GetSiteUtilityUnitName($site_id,'electricity').") ".lang('s_tariff'),
    'total_utilities_by_room_night_and_build_area' => lang('sites_total_utilities_by_room_night_and_build_area'),
    'electricity_consumption_site_efficiency_benchmark' => lang('electricity')." ".GetSiteUtilityUnitName($site_id,'electricity')." ".lang('consumption_site_efficiency_benchmark'),
    'electricity_cost_consumption_site_efficiency_benchmark' => lang('sites_electricity_cost_consumption_site_efficiency_benchmark_report_title'),
    'utilities_cost_consumption_site_efficiency_benchmark' => lang('utilities_cost_consumption_site_efficiency_benchmark_report_title'),
    'sites_annual_group_energy_report' => lang('sites_annual_group_energy_report')
);

$time_type_list = array(
    'sites_select_choose_month' => lang('sites_select_choose_month'),
    'sites_select_avg_ytd' => lang('sites_select_avg_ytd'),
    'sites_select_avg_last_year' => lang('choose-year')
);

$time_type_list_change = array(
    'sites_select_avg_ytd' => lang('sites_select_avg_ytd'),
    'sites_select_avg_last_year' => lang('choose-year')
);

$base_type_variable = '';
if ($filters['base_type'] == 'cost') {
    $base_type_variable = '_cost';
}

$sites_type1 = array(0=>'All sites');
$sites_type2 = $this->_ci->config->config['sites_type'];
$sites_type3 = array(3=>'Select Sites');
$sites_type = array_merge($sites_type1,$sites_type2,$sites_type3);
?>
<style>
    table.table-condensed .disable-year {
	display: none;
    }
</style>
<div id="ajax_table" class="report-detail">
    <article class="card">
	<div class="article-header"><?php echo lang('sites-reports'); ?></div>
	<div class="card-wrap">
	    <div class="row">
		<div class="col-lg-12">
		    <div class="data-info-block-outer">
			<form id="report_form_utility" method="post">
			    <div class="row">
				<div class="col-lg-6">
				    <div class="row">
					<div class="col-lg-4">
					    <label><?php echo lang('report-type'); ?></label>
					</div>
					<div class="col-lg-7">
					    <div class="form-dropdown">
						<?php echo form_dropdown('report_type', $report_types_list, $report_type, 'id="report_type" data-type="custom-dropdown"'); ?>
					    </div>
					</div>
				    </div>
				</div>
			    </div>
			    <br/>
			    <!-- Filter by Regions -->
			    <?php
			    if($regions && !empty($regions)){
				?>
				<div class="row">
				    <div class="col-lg-6">
					<div class="row">
					    <div class="col-lg-4">
						<label><?php echo lang('region'); ?></label>
					    </div>
					    <div class="col-lg-7">
						<div class="form-dropdown">
						    <?php echo form_dropdown('regions', $regions, $selected_region, 'id="regions" data-type="custom-dropdown"'); ?>
						</div>
					    </div>
					</div>
				    </div>
				</div>
				<br/>
				<?php
			    }
			    ?>
			    <!-- Filter by Regions -->
			    <div class="row">
				<div class="col-lg-6">
				    <div class="row">
					<div class="col-lg-4">
					    <label><?php echo lang('filter_by'); ?></label>
					</div>
					<div class="col-lg-7">
					    <div class="form-dropdown">
						<?php echo form_dropdown('site_type', $sites_type, $site_type, 'id="site_type" data-type="custom-dropdown"'); ?>
						<input type="hidden" id="site_custom_filter" name="site_custom_filter" value="<?php echo implode(',', $filters['site_custom_filter']); ?>" />
					    </div>
					</div>
				    </div>
				</div>
			    </div>
			    <br/>
			    <div class="row report-data-block">
				<div class="col-lg-6">
				    <div class="row">
					<div class="col-lg-4">
					    <label><?php echo lang('sites_select_time'); ?></label>
					</div>
					<div class="col-lg-7">
					    <div id="timetypeselect" class="form-dropdown">
						<?php echo form_dropdown('time_type', $time_type_list, $time_type, 'id="time_type" data-type="custom-dropdown"'); ?>
					    </div>
					</div>
				    </div>
				</div>
				<div id="month_picker_box" class="col-lg-4">
				    <div class="row">
					<div class="col-lg-4">
					    <label><?php echo lang('start-date'); ?></label>
					</div>
					<div class="col-lg-8">
					    <div class="data-info-block">
						<input type="text" id="startdate_utility" name="startdate" class='Default validDate monthpicker_input' value="<?php echo (!empty($filters['startdate'])) ? $filters['startdate'] : ''; ?>">
					    </div>
					</div>
				    </div>
				</div>
				<div id="year_picker_box" class="col-lg-4" style="display: none;">
				    <div class="row">
					<div class="col-lg-4">
					    <label><?php echo lang('sites_select_year'); ?></label>
					</div>
					<div class="col-lg-8">
					    <div class="data-info-block">
						<input type="text" id="YearFormat" name="year" class='Default validDate year_picker_box' value="<?php echo (!empty($filters['year'])) ? $filters['year'] : ''; ?>">
						<input type="hidden" id="annual_year" name="annual_year" value="<?php echo (!empty($filters['year'])) ? $filters['year'] : ''; ?>" />
					    </div>
					</div>
				    </div>
				</div>
			    </div>
			    <br>
			    <div class="row">
				    <div class="col-lg-2 gen-report">
					<input id="genrate-report" type="submit" value="<?php echo lang('generate-report'); ?>">
				    </div>
				    <div class="col-sm-2 gen-report">
					<input id="genrate-excel" type="button" value="<?php echo lang('generate-excel'); ?>">
				    </div>
				    <div class="col-sm-2 gen-report" style="float: right;" id="saveImage"></div>
				    <input type="hidden" name="columnChartImg_monthly" id="columnChartImg_monthly" value="" />
			    </div>
			    <input type="hidden" id="view_type" name="view_type" value="" />
			</form>
		    </div>

		    <div class="row">
						<div class="col-lg-7">
							<div id="sites_chart" style="height:500px;margin-top:50px">
				<?php if (empty($reportdata)) { ?>
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
						<div class="col-lg-5 report-table-data table-responsive category-table-block">
			    <?php if (!empty($reportdata)) { ?>
				<table class="table">
				    <thead>
				    <th><strong>Site</strong></th>
				    <?php
				    if($time_type=='sites_select_avg_ytd'){
					?>
					<th><strong>This Year</strong></th>
					<th><strong>Previous Year</strong></th>
					<?php
				    }else{
					?>
					<th><strong><?php echo $filters['year']; ?></strong></th>
					<th><strong><?php echo ($filters['year'] - 1); ?></strong></th>
					<?php
				    }
				    ?>
				    <th><strong>% Change</strong></th>
				    <th><strong>Built Up Area</strong></th>
				    </thead>
				    <tbody>
					<?php
					foreach ($sites as $site) {
					    if (!empty($reportdata[$site['id']]['electricity' . $base_type_variable])) {

						$total_utility_current = 0;
						$total_utility_previous = 0;

						$total_utility_current += (!empty($reportdata[$site['id']]['electricity' . $base_type_variable])) ? $reportdata[$site['id']]['electricity' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['fuel' . $base_type_variable])) ? $reportdata[$site['id']]['fuel' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['lpg' . $base_type_variable])) ? $reportdata[$site['id']]['lpg' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['natural_gas' . $base_type_variable])) ? $reportdata[$site['id']]['natural_gas' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['heating_district' . $base_type_variable])) ? $reportdata[$site['id']]['heating_district' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['cooling_district' . $base_type_variable])) ? $reportdata[$site['id']]['cooling_district' . $base_type_variable] : 0;
						$total_utility_current += (!empty($reportdata[$site['id']]['water' . $base_type_variable])) ? $reportdata[$site['id']]['water' . $base_type_variable] : 0;

						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['electricity' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['electricity' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['fuel' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['fuel' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['lpg' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['lpg' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['natural_gas' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['natural_gas' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['heating_district' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['heating_district' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['cooling_district' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['cooling_district' . $base_type_variable] : 0;
						$total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['water' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['water' . $base_type_variable] : 0;

						$currentdata = (!empty($total_utility_current) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? $total_utility_current / $reportdata[$site['id']]['site_builtup_area'] : 0;
						$previousdata = (!empty($total_utility_previous) && !empty($reportdata['previousdata'][$site['id']]['site_builtup_area'])) ? $total_utility_previous / $reportdata['previousdata'][$site['id']]['site_builtup_area'] : 0;
						$built_up_areadata = (!empty($reportdata[$site['id']]['site_builtup_area'])) ? $reportdata[$site['id']]['site_builtup_area'] : 0;

						$currentdata = round($currentdata, 2);
						$previousdata = round($previousdata, 2);
						$built_up_areadata = round($built_up_areadata, 2);

						$difference = $currentdata - $previousdata;
						$percentage_change = ($difference * 100 / $currentdata);
						$percentage_change = round($percentage_change, 2);

						$sitedata = $site['site_location_name'];
						if (!empty($site['country'])) {
						   // $sitedata.= '-' . $site['country'];
						}
						?>
						<tr>
						    <td><strong><?php echo $sitedata; ?></strong></td>
						    <td><?php echo $currentdata; ?></td>
						    <td><?php echo $previousdata; ?></td>
						    <td><?php echo $percentage_change; ?>%</td>
						    <td><?php echo $built_up_areadata; ?></td>
						</tr>
						<?php
					    }
					}
					?>
				    </tbody>
				</table>
			    <?php } ?>
			</div>

			<div id="save_form_sites" class="card-wrap" style="display:none;">
			    <div class="table-responsive">
				<table class="table table-striped">
				    <thead>
					<tr>
					    <th><?php echo lang('no') ?></th>
					    <th><?php echo lang('site'); ?></th>
					    <th>
						<?php $checked = (count($filters['site_custom_filter']) == count($sites_list))?'checked="checked"':''; ?>
						<input type="checkbox" name="check_all" id="check_all" value="0" class="icheck" <?php echo $checked; ?> />
					    </th>
					</tr>
				    </thead>
				    <tbody>
					<?php
					$i = 1;
					foreach ($sites_list as $key=>$value) {
					    ?>
					    <tr>
						<td align="center"><?php echo $i; ?></td>
						<td><?php echo $value['site_location_name']; ?></td>
						<td>
						    <?php $checked = (in_array($value['id'], $filters['site_custom_filter']))?'checked="checked"':''; ?>
						    <input type="checkbox" id="custom_sites_filter_select" name="custom_sites_filter_select[]" class="check_box icheck" value="<?php echo $value['id']; ?>" <?php echo $checked; ?> >
						</td>
					    </tr>
					    <?php
					    $i++;
					}
					?>
				    </tbody>
				</table>
			    </div>
			    <div class="form-btn-outer">
				<a href="javascript:void(0)" class="btn btn-secondary btn-submit saveform"><?php echo lang('btn-save'); ?></a>
			    </div>
			</div>

		    </div>
		</div>
	    </div>
	</div>
    </article>
</div>
<script type="text/javascript">
	drawHighchart();

	function drawHighchart() {
		<?php if (!empty($reportdata)) { ?>
			var utilityScatterDataArray = [];
			<?php
			foreach ($sites as $site) {
				if (!empty($reportdata[$site['id']]['electricity' . $base_type_variable])) {
					$total_utility_current = 0;
					$total_utility_previous = 0;

	    $total_utility_current += (!empty($reportdata[$site['id']]['electricity' . $base_type_variable])) ? $reportdata[$site['id']]['electricity' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['fuel' . $base_type_variable])) ? $reportdata[$site['id']]['fuel' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['lpg' . $base_type_variable])) ? $reportdata[$site['id']]['lpg' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['natural_gas' . $base_type_variable])) ? $reportdata[$site['id']]['natural_gas' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['heating_district' . $base_type_variable])) ? $reportdata[$site['id']]['heating_district' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['cooling_district' . $base_type_variable])) ? $reportdata[$site['id']]['cooling_district' . $base_type_variable] : 0;
	    $total_utility_current += (!empty($reportdata[$site['id']]['water' . $base_type_variable])) ? $reportdata[$site['id']]['water' . $base_type_variable] : 0;

	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['electricity' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['electricity' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['fuel' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['fuel' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['lpg' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['lpg' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['natural_gas' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['natural_gas' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['heating_district' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['heating_district' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['cooling_district' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['cooling_district' . $base_type_variable] : 0;
	    $total_utility_previous += (!empty($reportdata['previousdata'][$site['id']]['water' . $base_type_variable])) ? $reportdata['previousdata'][$site['id']]['water' . $base_type_variable] : 0;

	    $currentdata = (!empty($total_utility_current) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? $total_utility_current / $reportdata[$site['id']]['site_builtup_area'] : 0;
	    $previousdata = (!empty($total_utility_previous) && !empty($reportdata['previousdata'][$site['id']]['site_builtup_area'])) ? $total_utility_previous / $reportdata['previousdata'][$site['id']]['site_builtup_area'] : 0;
	    $built_up_areadata = (!empty($reportdata[$site['id']]['site_builtup_area'])) ? $reportdata[$site['id']]['site_builtup_area'] : 0;

	    $kwh_per_built_area_data = round($currentdata, 2);
	    $built_up_area_data = round($built_up_areadata, 2);

	    $sitedata = $site['site_location_name'];
	    if (!empty($site['country'])) {
		//$sitedata.= '-' . $site['country'];
	    }

					$tooltip_data = $sitedata . ' : ' . $kwh_per_built_area_data;
	    //if($time_type == "sites_select_avg_last_year") {
		if($currentdata > $previousdata) { ?>
						utilityScatterDataArray.push(['<?php echo $sitedata; ?>', <?php echo $kwh_per_built_area_data; ?>, <?php echo $built_up_area_data; ?>, <?php echo $kwh_per_built_area_data; ?>, 'triangle', 'red']);
		<?php } else { ?>
						utilityScatterDataArray.push(['<?php echo $sitedata; ?>', <?php echo $kwh_per_built_area_data; ?>, <?php echo $built_up_area_data; ?>, <?php echo $kwh_per_built_area_data; ?>, 'triangle-down', 'limegreen']);
			<?php }
				}
			}
			?>
			utilityScatterSeries = [];
			Object.entries(utilityScatterDataArray).forEach(([key, value]) => {
				utilityScatterSeries.push({
					showInLegend: false,
					name: utilityScatterDataArray[key][0],
					color: utilityScatterDataArray[key][5],
					data: [
						[utilityScatterDataArray[key][2], utilityScatterDataArray[key][1]]
					],
					marker: {
						symbol: utilityScatterDataArray[key][4],
					}
				}, );
			});
			Highcharts.chart('sites_chart', {
				chart: {
					type: 'scatter',
					zoomType: 'xy'
				},
				credits: {
					enabled: false
				},
				title: {
					text: '<?php echo $view_title; ?>',
					style: {
						color: Highcharts.getOptions().colors[1],
						fontFamily: 'Arial',
						fontSize: '24px',
						fontWeight: 'bold',
					}
				},
				xAxis: {
					gridLineWidth: 1,
					lineColor: Highcharts.getOptions().colors[1],
					lineWidth: 1,
					title: {
						enabled: true,
						text: '<?php echo lang("axis-title-area-built-up") . '(' . getLocalUnitText($site_id) . ')'; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					startOnTick: true,
					endOnTick: true,
					showLastLabel: true
				},
				yAxis: {
					lineColor: Highcharts.getOptions().colors[1],
					lineWidth: 1,
					title: {
						text: '<?php echo $x_axis_title . getLocalUnitText($site_id); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				},
				plotOptions: {
					scatter: {
						marker: {
							radius: 5,
						},
						tooltip: {
							headerFormat: '<b>{series.name}</b> : ',
							pointFormat: '{point.y}',
						}
					}
				},
				series: utilityScatterSeries
			});
		<?php } ?>
	}

    $(document).ready(function() {
	$("#site_type").change(function() {
	    var site_type = $(this).val();
	    if (site_type == 3) {
		$.blockUI({
		    css: {
			cursor: 'default'
		    },
		    blockMsgClass: 'formblockui site-filter-class',
		    overlayCSS: {
			cursor: 'default',
			'border-radius': '10px'
		    },
		    message: $('#save_form_sites'),
		    onUnblock: function() {
			var values = $("input[id='custom_sites_filter_select']:checked").map(function() {
			    return $(this).val();
			}).get();
			$("#site_custom_filter").val(values);
		    }
		});
		$('.blockOverlay').click($.unblockUI);
	    }
	});

	$(".saveform").click(function() {
	    $.unblockUI({
		fadeOut: 200
	    });
	});

	$('#check_all').on('ifChecked', function(event) {
	    $('.check_box').iCheck('check');
	});

	$('#check_all').on('ifUnchecked', function(event) {
	    if ($('.check_box').filter(':checked').length == $('.check_box').length) {
		$('.check_box').iCheck('uncheck');
	    }
	});
	$('.check_box').on('ifUnchecked', function(event) {
	    $('#check_all').iCheck('uncheck');
	});

	$('.check_box').on('ifChecked', function(event) {
	    if ($('.check_box').filter(':checked').length == $('.check_box').length) {
		$('#check_all').iCheck('check');
	    }
	});

	$(".monthpicker_input").MonthPicker();
	var time_type_val = $('#time_type').val();
	setreporttime(time_type_val);
	$("#timetypeselect").on('change', '#time_type', function() {
	    setreporttime($(this).val());
	});

	function setreporttime(time_type_val) {
	    if (time_type_val == 'sites_select_choose_month') {
		$("#month_picker_box").show();
	    } else {
		$("#month_picker_box").hide();
	    }
	    if (time_type_val == 'sites_select_avg_last_year') {
		$("#year_picker_box").show();
	    } else {
		$("#year_picker_box").hide();
	    }
	}

	$('#report_type').change(function() {
	    genratetimetypeselect($(this).val());
	    $("#time_type").trigger("change");
	});
	genratetimetypeselect('<?php echo $report_type; ?>');
	function genratetimetypeselect(selected_value) {
<?php $timetypeselectbox = str_replace("\n", '', form_dropdown('time_type', $time_type_list, $time_type, 'id="time_type" data-type="custom-dropdown-timetype"')); ?>
<?php $timetypeselectbox_change = str_replace("\n", '', form_dropdown('time_type', $time_type_list_change, $time_type, 'id="time_type" data-type="custom-dropdown-timetype"')); ?>

	    if (selected_value == 'electricity_consumption_site_efficiency_benchmark' || selected_value == 'electricity_cost_consumption_site_efficiency_benchmark' || selected_value == 'utilities_cost_consumption_site_efficiency_benchmark') {
		var selectbox = '<?php echo $timetypeselectbox_change ?>';
	    } else {
		var selectbox = '<?php echo $timetypeselectbox ?>';
	    }

	    //selectbox += '</select>';
	    $("#timetypeselect").html($(selectbox));
	    $("select[data-type='custom-dropdown-timetype']").dropkick({
		mobile: true
	    });
	}

	$("#report_form_utility").validate({
	    rules: {
		startdate: {
		    required: true,
		    maxlength: 25
		}
	    }
	});

	$("#genrate-excel").click(function(){
	    $("#view_type").val('excel');
	    $("#report_form_utility").submit();
	});

	$("#genrate-report").click(function(){
		$("#view_type").val('');
		$("#report_form_utility").submit();
	    });
    });

    $(function() {
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
	    endDate : new Date(),
	    autoclose:true

	}).on('changeDate', function(ev){
	    var fullYear = ev.date.getFullYear();
	    $('#annual_year').val(fullYear);
	    if (fullYear >= currentYear) {
		$('#annual_year').val(fullYear);
	    }
	});
    });
</script>