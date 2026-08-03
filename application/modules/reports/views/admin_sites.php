<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
<style>
.invoice_site_dropdown > ul.dk-select-options {
    max-height: 240px;
    overflow-y: scroll;
}
.row {
	padding-top: 10px;
}
</style>
<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min', 'bootstrap-datepicker-new'));
echo add_css(array('MonthPicker.min', 'bootstrap-datepicker-new'));

$report_types_list = array(
    'water_liters' => lang('water_consumption')." (".GetSiteUtilityUnitName($site_id,'water').")",
    'electricity_kwh' => lang('electricity_consumption')." (".GetSiteUtilityUnitName($site_id,'electricity').")",
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

<div id="monthly_report_popup" style="display:none;">
	<form id="file_form" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/add_project_todo_file' ?>">
		<div style="padding: 15px 0px 15px 0;">
			<label for="commentbox" class="main-label">Select Month</label>
			<input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
		</div>
	</form>
</div>
<div id="monthly_waste_report_popup" style="display:none;">
	<form id="file_form" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/add_project_todo_file' ?>">
		<div style="padding: 15px 0px 15px 0;">
			<label for="commentbox" class="main-label">Select Month</label>
			<input type="text" id="MonthFormatWaste" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
		</div>
	</form>
</div>
<div id="monthly_discrepancy_report_popup" style="display:none;">
	<form id="file_form" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/generate_discrepancy_report_excel' ?>">
		<div style="padding: 15px 0px 15px 0;">
			<label for="commentbox" class="main-label">Select Month</label>
			<input type="text" id="MonthFormatDiscrepancy" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">
		</div>
	</form>
</div>
<article class="card">
	<div class="article-header">
		<div class="row">
			<div class="col-lg-3"><?php echo lang('sites-reports'); ?></div>
		</div>
	</div>
	<div class="card-wrap Tab-block" id="group-report" style="max-width: inherit;">
		<ul class="resp-tabs-list hor_1 clearfix">
			<li class="tab-custom-id-1"><?php echo '<b><span class="fa fa-bar-chart"></span> Charts</b>'; ?></li>
			<li class="tab-custom-id-2"><?php echo '<b><span class="fa fa-download"></span> Downloadable Reports</b>'; ?></li>
		</ul>
		<div class="resp-tabs-container hor_1">
			<div id="tab-1" data-tab-id="1">
				<div id="ajax_table" class="report-detail">
					<div class="panel panel-default">
					<div class="panel-body">
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
										<label><?php echo lang('sites_select_month'); ?></label>
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
											<div class="row">
													<div class="col-lg-2 gen-report">
														<input id="genrate-report" type="submit" value="<?php echo lang('generate-report'); ?>">
													</div>
													<div class="col-sm-2 gen-report">
														<input id="genrate-excel" type="button" value="<?php echo lang('generate-excel'); ?>">
													</div>
									<div class="col-sm-2 gen-report" style="float: right;" id="saveImage">
									</div>
									<input type="hidden" name="columnChartImg_monthly" id="columnChartImg_monthly" value="" />
											</div>
											<input type="hidden" id="view_type" name="view_type" value="" />
							</form>
							</div>

									<div id="sites_chart" style="height:500px; margin-top:50px;">

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
				</div>
			</div>
			<div id="tab-2" data-tab-id="2">
				<?php $export_utility_invoices_permission = check_user_permission_by_label('admin.sites.export_utility_invoices'); ?>
				<?php if($export_utility_invoices_permission) { ?>
				<div class="panel panel-default">
					<div class="panel-body">
						<?php
							$year = date('Y');
							echo form_open_multipart(site_url() . BASE_ADMIN_URL_CUSTOM.'sites/export_utility_invoices', array('id' => 'exportUtilityInvoicesForm', 'name' => 'exportUtilityInvoicesForm')); ?>
							<h3>Invoice Download</h3>
							<div class="row">
								<div class="col-lg-2"><b>Select Site:</b></div>
								<div class="col-lg-2">
									<?php if(isset($sites_list) && !empty($sites_list)) { ?>
									<div class="form-dropdown">
										<select class="invoice_site_dropdown" name="site_id_invoice" data-type="custom-dropdown" data-dkcacheid="0">
											<?php foreach ($sites_list as $key=>$value) { ?>
												<option value="<?= $value['id']; ?>"><?= $value['site_location_name']; ?></option>
											<?php } ?>
										</select>
									</div>
									<?php } ?>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-2"><b>Select Year:</b></div>
								<div class="col-lg-2">
									<input type="text" name="YearFormat" id="YearFormatInvoice" class='form-control' value="<?php echo (!empty($site_residence['YearFormatInvoice'])) ? $site_residence['YearFormatInvoice'] : $year; ?>">
								</div>
							</div>
							<div class="row">
								<div class="col-lg-2">
									<button type="submit" name="exportUtilityInvoicesFormSubmit" id="exportUtilityInvoicesFormSubmit" value="1" class="btn btn-secondary btn-submit">Export Utility Invoices</button>
								</div>
							</div>
						<?php echo form_close(); ?>
					</div>
				</div>
				<?php } ?>
				<div class="panel panel-default">
					<div class="panel-body">
						<h3>Data Export</h3>
						<div class="row">
							<?php 
							$role_id = (isset($_SESSION['admin']['role_id']) ? $_SESSION['admin']['role_id'] : 0);
							$export_site_info_permission = check_user_permission_by_label('admin.sites.export_site_info'); ?>
							<?php if($export_site_info_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_site_info" class="btn btn-warning btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-site-info');?></b></a></div>
							<?php } ?>
							<?php $export_utility_permission = check_user_permission_by_label('admin.sites.export_utility'); ?>
							<?php if($export_utility_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_utility" class="btn btn-warning btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-sites-utilities-db');?></b></a></div>
							<?php } ?>
							<?php $export_utility_choices_permission = check_user_permission_by_label('admin.sites.export_utility_choices'); ?>
							<?php if($export_utility_choices_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_utility_choices" class="btn btn-warning btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-sites-utility-choices');?></b></a></div>
							<?php } ?>
							<?php 
							$export_group_utility_permission =  check_user_permission_by_label('admin.sites.export_monthly_utility_report');?>
							<?php if($export_group_utility_permission){ ?>
							<div class="col-lg-3">
								<a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/group_utility_report" id="groupUtilityMonthPicker" class="btn btn-warning btn-submit group-utility-report" style="width:250px; padding-left: 5px;padding-right: 5px;">
									<b><?php echo lang('export-monthly-utility-report');?></b>
								</a>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php $export_waste_permission = check_user_permission_by_label('admin.sites.export_waste'); ?>
							<?php if($export_waste_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_waste" class="btn btn-info btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-waste');?></b></a></div>
							<?php } ?>
							<?php 
							$export_group_waste_permission =  check_user_permission_by_label('admin.sites.export_group_waste_corporate_report');?>
							<?php if($export_group_waste_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_group_waste_corporate_report"  id="groupWasteMonthPicker" class="btn btn-info btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-monthly-waste-report');?></b></a></div>
							<?php } ?>
						</div>
						<div class="row">
							<?php $export_negative_utility_permission = ($role_id == 1) ? 1 : 0; ?>
							<?php if($export_negative_utility_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>import/checkNegative" class="btn btn-success btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-negative-utility');?></b></a></div>
							<?php } ?>
							<?php $export_utility_updated_log_permission = ($role_id == 1) ? 1 : 0;?>
							<?php if($export_utility_updated_log_permission){ ?>
								<div class="col-lg-3"><a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_utility_last_updated_log" class="btn btn-success btn-submit" style="width:250px; padding-left: 5px;padding-right: 5px;"><b><?php echo lang('export-utility-update-logs');?></b></a></div>
							<?php } ?>
							<?php $export_discrepancy_log_permission = ($role_id == 1) ? 1 : 0;?>
							<?php if($export_discrepancy_log_permission){ ?>
								<div class="col-lg-3">
									<a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/generate_discrepancy_report_excel" id="groupDiscrepancyMonthPicker" class="btn btn-success btn-submit group-discrepancy-report" style="width:250px; padding-left: 5px;padding-right: 5px;">
										<b><?php echo lang('export-discrepancy-logs');?></b>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>

<script type="text/javascript">
	Highcharts.setOptions({
		lang: {
			thousandsSep: ','
		}
	});
	$(function() {
		$('#groupUtilityMonthPicker, #groupWasteMonthPicker, #groupDiscrepancyMonthPicker').on('click', function (e) {
			e.preventDefault();

			const popupMap = {
				groupUtilityMonthPicker: '#monthly_report_popup',
				groupWasteMonthPicker: '#monthly_waste_report_popup',
				groupDiscrepancyMonthPicker: '#monthly_discrepancy_report_popup'
			};

			$.blockUI({
				css: {
					cursor: 'default',
					top: '20%'
				},
				blockMsgClass: 'formblockui',
				overlayCSS: {
					cursor: 'default',
					'border-radius': '10px'
				},
				message: $(popupMap[this.id])
			});

			$('.blockOverlay').off('click').on('click', $.unblockUI);
		});

		var monthPickerObj = $("#MonthFormat").MonthPicker({
			'OnAfterChooseMonth': function(date) {
				var month = date.getMonth() + 1;
				var year = date.getFullYear();
				window.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/group_utility_report/' + (month < 10 ? '0' + month : month) + '-' + year;
				window.location.href = href;
				$.unblockUI();
			},
		});
		var monthPickerWasteObj = $("#MonthFormatWaste").MonthPicker({
			'OnAfterChooseMonth': function(date) {
				var month = date.getMonth() + 1;
				var year = date.getFullYear();
				window.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>sites/export_group_waste_corporate_report/' + (month < 10 ? '0' + month : month) + '-' + year;
				window.location.href = href;
				$.unblockUI();
			},
		});
		var monthPickerDiscrepancyObj = $("#MonthFormatDiscrepancy").MonthPicker({
			'OnAfterChooseMonth': function(date) {
				var month = date.getMonth() + 1;
				var year = date.getFullYear();
				window.href = '<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/generate_discrepancy_report_excel/' + (month < 10 ? '0' + month : month) + '-' + year;
				window.location.href = href;
				$.unblockUI();
			},
		});

		$('.Tab-block').easyResponsiveTabs({

            type: 'default',

            width: 'auto',

            fit: true,

            tabidentify: 'hor_1',

            activate: function (event) {

                // If need on tab change

            }

        });
		<?php if (!empty($reportdata) && $report_title != 'sites_annual_group_energy_report') { ?>
			<?php
			$site_count = 0;
			$total_sum_consumptiondata = 0;
			foreach ($sites as $site) {
				$consumptiondata = (!empty($reportdata[$site['id']][$filters['utility_type']]) && is_numeric($reportdata[$site['id']][$filters['utility_type']]) && is_finite($reportdata[$site['id']][$filters['utility_type']])) ? $reportdata[$site['id']][$filters['utility_type']] : 0;
				$total_sum_consumptiondata += $consumptiondata;
				$site_count++;
			}
			$occupancydataArray = $consumptiondataArray = $averagedataArray = [];
			foreach ($sites as $site) {
				$sitedata = $site['site_location_name'];
				$consumptiondata = (!empty($reportdata[$site['id']][$filters['utility_type']]) && is_numeric($reportdata[$site['id']][$filters['utility_type']]) && is_finite($reportdata[$site['id']][$filters['utility_type']])) ? $reportdata[$site['id']][$filters['utility_type']] : 0;
				$occupancydata = (!empty($reportdata[$site['id']]['occupancy']) && is_numeric($reportdata[$site['id']]['occupancy']) && is_finite($reportdata[$site['id']]['occupancy'])) ? $reportdata[$site['id']]['occupancy'] : 0;

	$AVG_consumptiondata = $total_sum_consumptiondata / $site_count;
				$AVG_consumptiondata = (isset($AVG_consumptiondata) && is_numeric($AVG_consumptiondata) && is_finite($AVG_consumptiondata)) ? round($AVG_consumptiondata, 2) : 0;
				$consumptiondata = (isset($consumptiondata) && is_numeric($consumptiondata) && is_finite($consumptiondata)) ? round($consumptiondata, 2) : 0;
				$occupancydata = (isset($occupancydata) && is_numeric($occupancydata) && is_finite($occupancydata)) ? round($occupancydata, 2) : 0;
				array_push($occupancydataArray, $occupancydata);
				array_push($consumptiondataArray, $consumptiondata);
				array_push($averagedataArray, $AVG_consumptiondata);
			}
			?>
			var stuff = '<?php echo json_encode($sites); ?>';
			var arrayData = JSON.parse(stuff);
			var result = Object.keys(arrayData).map((key) => arrayData[key]);
			let Labels = [];
			let OccupancyArray = [];
			let ConsumptionArray = [];
			result.forEach(element => {
				var element = Object.keys(element).map((key) => element[key]);
				Labels.push(element[1]);
			});
			var occupancydataArray = '<?php echo json_encode($occupancydataArray); ?>';
			var occupancyData = JSON.parse(occupancydataArray);
			var occupancyresult = Object.keys(occupancyData).map((key) => occupancyData[key]);
			var consumptiondataArray = '<?php echo json_encode($consumptiondataArray); ?>';
			var consumptionData = JSON.parse(consumptiondataArray);
			var consumptionresult = Object.keys(consumptionData).map((key) => consumptionData[key]);
			var averagedataArray = '<?php echo json_encode($averagedataArray); ?>';
			var averageData = JSON.parse(averagedataArray);
			var averageresult = Object.keys(averageData).map((key) => averageData[key]);
			Highcharts.chart('sites_chart', {
				chart: {
					type: 'column'
				},
				title: {
					margin: 0,
					useHTML: true,
					text: '<?php echo $view_title; ?>',
					style: {
						color: "#333333",
						fontWeight: "bold",
						fontSize: '24px',
						paddingBottom: "24px"
					}
				},
				credits: {
					enabled: false
				},
				xAxis: {
					categories: Labels,
					crosshair: true
				},
				yAxis: [{
					min: 0,
					title: {
						text: '<?php echo $x_axis_title; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						},
					}
				}, {
					min: 0,
					tickPositions: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					title: {
						rotation: 270,
						margin: 20,
						text: '<?php echo lang("occupancy-title"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						},
					},
					opposite: true,
				}],
				tooltip: {
					pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [{
					name: 'Consumption',
					data: consumptionresult,
					color: '#a4d3fd'
				}, {
					name: 'Occupancy',
					type: 'spline',
					yAxis: 1,
					data: occupancyresult,
					color: '#803632'
				}, {
					name: 'Average',
					type: 'line',
					data: averageresult,
					color: '#e0a800'
				}]
			});
		<?php } else if (!empty($reportdata) && $report_title == 'sites_annual_group_energy_report') { ?>
			var reportTitle = "<?php echo lang($report_title); ?>";
			<?php
			$reportTitle = lang($report_title);
			if ($time_type == 'sites_select_avg_ytd') {
				$reportTitle .= ' From ' . date('01/01/Y') . ' To 30/' . (date('n') - 1) . '/' . date('Y');
			} else {
				$reportTitle = "Annual " . $reportTitle;
			}
			?>
			reportTitle = "<?php echo $reportTitle; ?>";
			<?php
			if ($time_type == 'sites_select_choose_month') {
			?>
				reportTitle = "<?php echo lang($report_title); ?>";
				var dateString = $("#startdate_utility").val();
				if (dateString.length != 0) {
					var date = dateString.split("/");
					var customDate = date[0] + "/01/" + date[1];
					var objDate = new Date(customDate),
						locale = "en-us",
						month = objDate.toLocaleString(locale, {
							month: "short"
						});
					reportTitle += " of " + month + " " + date[1];
				}
			<?php } ?>

			var energyCarbonArray = [];
			energyCarbonArray.push(['<?php echo lang("sites"); ?>',
				'<?php echo lang("consumption"); ?>',
				'<?php echo lang("co2_tons_co2e"); ?>'
			]);
			<?php
			foreach ($reportdata as $site => $value) {
				$siteName = $site;
				$utilityConsumption = round($value['Total Consumption'], 2);
				$utilityCO2 = isset($value['Total CO2']) ? round($value['Total CO2'], 2) : 0;
			?>
				energyCarbonArray.push(["<?php echo $siteName; ?>", <?php echo trim($utilityConsumption); ?>, <?php echo trim($utilityCO2); ?>]);
			<?php
			}
			$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
			?>
			var energyCarbonArrayXaxisData = [];
			var energyCarbonDataArray = [];
			var energyCarbonArraySubtitle = energyCarbonArray[0];
			energyCarbonArraySubtitleName = energyCarbonArraySubtitle.filter(value => value !== "Sites");
			for (var i = 1; i < energyCarbonArray.length; i++) {
				energyCarbonArrayXaxisData.push(energyCarbonArray[i][0]);
			}
			$.each(energyCarbonArraySubtitleName, function(i) {
				var key = energyCarbonArraySubtitleName[i];
				energyCarbonDataArray[key] = [];
				for (var j = 1; j < energyCarbonArray.length; j++) {
					energyCarbonDataArray[key].push(energyCarbonArray[j][i + 1]);
				}
			});
			var energyCarbonSeries = [];
			Object.entries(energyCarbonDataArray).forEach(([key, value]) => {
				if (!(key == 'CO2 (Tons CO2e)')) {
					if (key == 'Consumption') {
						energyCarbonSeries.push({
							name: key,
							data: energyCarbonDataArray[key],
							color: '#3366CC'
						}, );
					}
				} else {
					energyCarbonSeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: energyCarbonDataArray[key],
						marker: {
							symbol: 'square',
							lineWidth: 2,
							fillColor: '<?php echo $chart_legend_colors['Occupancy']; ?>',
							lineColor: '<?php echo $chart_legend_colors['Occupancy']; ?>',
						},
						color: '<?php echo $chart_legend_colors['Occupancy']; ?>'
					}, );
				}
			});

			Highcharts.chart('sites_chart', {
				chart: {
					type: 'bar'
				},
				title: {
					text: reportTitle,
					style: {
						color: "#333333",
						fontWeight: "bold",
						fontSize: '24px',
						paddingBottom: "24px"
					}
				},
				credits: {
					enabled: false
				},
				xAxis: {
					categories: energyCarbonArrayXaxisData,
					title: {
						text: '<?php echo lang("sites"); ?>',
						style: {
							fontWeight: 'bold',
							fontSize: "15px",
							color: '#000'
						}
					}
				},
				yAxis: [{
					min: 0,
					title: {
						text: '<?php echo $x_axis_title; ?>',
						style: {
							fontWeight: 'bold',
							fontSize: "15px",
							color: '#000'
						}
					},
					labels: {
						overflow: 'justify'
					}
				}, {
					min: 0,
					title: {
						text: '<?php echo lang("co2_tons_co2e"); ?>',
						margin: 15,
						style: {
							fontWeight: 'bold',
							fontSize: "15px",
							color: '#000'
						}
					},
					labels: {
						overflow: 'justify'
					},
					opposite: true
				}],
				plotOptions: {
					bar: {
						stacking: "normal",
						dataLabels: {
							enabled: true,
							color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black'
						}
					}
				},
				credits: {
					enabled: false
				},
				series: energyCarbonSeries
			});
			Highcharts.chart('sites_chart', {
				chart: {
					type: 'column'
				},
				title: {
					text: reportTitle,
					style: {
						"color": "#333333",
						"fontSize": "24px",
						"fontWeight": "bold",
						"paddingBottom": "20px"
					}
				},
				credits: {
					enabled: false
				},
				xAxis: {
					title: {
						text: '<?php echo lang("sites"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						},
					},
					categories: energyCarbonArrayXaxisData,
					crosshair: true
				},
				yAxis: [{
					min: 0,
					title: {
						text: '<?php echo $x_axis_title; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						},
					}
				}, {
					min: 0,
					title: {
						text: '<?php echo lang("co2_tons_co2e"); ?>',
						rotation: 270,
						margin: 20,
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						},
					},
					opposite: true,
				}],
				tooltip: {
					pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: energyCarbonSeries
			});
		<?php } ?>
	});

    $(document).ready(function() {
	$("#site_type").change(function() {
	    var site_type = $(this).val();
	    if(site_type == 5){
		$.blockUI({
		    css: {cursor: 'default'},
		    blockMsgClass: 'formblockui site-filter-class',
		    overlayCSS: {cursor: 'default', 'border-radius': '10px'},
		    message: $('#save_form_sites'),
		    onUnblock: function() {
			var values = $("input[id='custom_sites_filter_select']:checked").map(function(){return $(this).val();}).get();
			$("#site_custom_filter").val(values);
		    }
		});
		$('.blockOverlay').click($.unblockUI);
	    }
	});

	$(".saveform").click(function() {
	    $.unblockUI({fadeOut: 200});
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
	var currentYear = (new Date()).getFullYear();
	$('#YearFormatInvoice').attr('readonly', 'readonly');
	$('#YearFormatInvoice').datepicker({
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
	    $('#YearFormatInvoice').val(fullYear);

	});
    });
</script>
