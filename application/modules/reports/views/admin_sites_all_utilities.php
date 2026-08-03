<script type="text/css" src="<?php echo site_url(); ?>themes/default/css/highcharts.css"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
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

$sites_type1 = array(0=>'All sites');
$sites_type2 = $this->_ci->config->config['sites_type'];
$sites_type3 = array(3=>'Select Sites');
$sites_type = array_merge($sites_type1,$sites_type2,$sites_type3);

$chart_legend_colors = $this->_ci->config->config['chart_legend_colors'];
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
                                    <div class="col-sm-2 gen-report" style="float: right;" id="saveImage">
                                    </div>
                                    <input type="hidden" name="columnChartImg_monthly" id="columnChartImg_monthly" value="" />
							</div>
							<input type="hidden" id="view_type" name="view_type" value="" />
                        </form> 
                    </div>

					<div id="sites_chart_cost" style="height:800px;margin-top:50px;">
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

					<div id="sites_chart" style="height:800px;margin-top:50px;">
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

                    <?php
                    //if ($filters['is_buildarea']) {
                    if ($filters['is_buildarea'] || $report_type == "total_utilities_by_room_night_and_build_area") { ?>                        
						<div id="sites_chart_build_area" style="height:800px;margin-top:50px;">
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
                        <?php
                    }
                    ?>

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
    </article>
</div>
<script type="text/javascript">
	drawHighchart();

	function drawHighchart() {
		<?php if ($report_type == "total_utilities_by_room_night_and_build_area") {
			if (!empty($sites)) {
				$totalElectricity = 0;
				$totalFuel = 0;
				$totalLpg = 0;
				$totalNaturalGas = 0;
				$totalWater = 0;
				$totalHeatingDistrict = 0;
				$totalCoolingDistrict = 0;
				foreach ($sites as $site) {
					$sitedata = $site['site_location_name'];
					$electricitydata = (!empty($reportdata[$site['id']]['electricity_cost']) && is_numeric($reportdata[$site['id']]['electricity_cost']) && is_finite($reportdata[$site['id']]['electricity_cost'])) ? ($reportdata[$site['id']]['electricity_cost']) : 0;
					$fueldata = (!empty($reportdata[$site['id']]['fuel_cost']) && is_numeric($reportdata[$site['id']]['fuel_cost']) && is_finite($reportdata[$site['id']]['fuel_cost'])) ? ($reportdata[$site['id']]['fuel_cost']) : 0;
					$lpgdata = (!empty($reportdata[$site['id']]['lpg_cost']) && is_numeric($reportdata[$site['id']]['lpg_cost']) && is_finite($reportdata[$site['id']]['lpg_cost'])) ? ($reportdata[$site['id']]['lpg_cost']) : 0;
					$natural_gasdata = (!empty($reportdata[$site['id']]['natural_gas_cost']) && is_numeric($reportdata[$site['id']]['natural_gas_cost']) && is_finite($reportdata[$site['id']]['natural_gas_cost'])) ? ($reportdata[$site['id']]['natural_gas_cost']) : 0;
					$waterdata = (!empty($reportdata[$site['id']]['water_cost']) && is_numeric($reportdata[$site['id']]['water_cost']) && is_finite($reportdata[$site['id']]['water_cost'])) ? ($reportdata[$site['id']]['water_cost']) : 0;
					$heating_districtdata = (!empty($reportdata[$site['id']]['heating_district_cost']) && is_numeric($reportdata[$site['id']]['heating_district_cost']) && is_finite($reportdata[$site['id']]['heating_district_cost'])) ? ($reportdata[$site['id']]['heating_district_cost']) : 0;
					$cooling_districtdata = (!empty($reportdata[$site['id']]['cooling_district_cost']) && is_numeric($reportdata[$site['id']]['cooling_district_cost']) && is_finite($reportdata[$site['id']]['cooling_district_cost'])) ? ($reportdata[$site['id']]['cooling_district_cost']) : 0;
					$cdddata = (!empty($reportdata[$site['id']]['cdd']) && is_numeric($reportdata[$site['id']]['cdd']) && is_finite($reportdata[$site['id']]['cdd'])) ? ($reportdata[$site['id']]['cdd']) : 0;
					$occupancydata = (!empty($reportdata[$site['id']]['occupancy']) && is_numeric($reportdata[$site['id']]['occupancy']) && is_finite($reportdata[$site['id']]['occupancy'])) ? ($reportdata[$site['id']]['occupancy']) : 0;

                $electricitydata = round($electricitydata, 2);
                $fueldata = round($fueldata, 2);
                $lpgdata = round($lpgdata, 2);
                $natural_gasdata = round($natural_gasdata, 2);
                $waterdata = round($waterdata, 2);
                $heating_districtdata = round($heating_districtdata, 2);
                $cooling_districtdata = round($cooling_districtdata, 2);
                $cdddata = round($cdddata, 2);
                $occupancydata = round($occupancydata, 2);
                
                
                $totalElectricity += $electricitydata;
                $totalFuel += $fueldata;
                $totalLpg += $lpgdata;
                $totalNaturalGas += $natural_gasdata;
                $totalWater += $waterdata;
                $totalHeatingDistrict += $heating_districtdata;
                $totalCoolingDistrict += $cooling_districtdata;
                
                $array_1[] = array('site' => $sitedata, 'electricitydata' => $electricitydata, 'fueldata' => $fueldata, 'lpgdata' => $lpgdata, 'natural_gasdata' => $natural_gasdata, 'waterdata' => $waterdata, 'heating_districtdata' => $heating_districtdata, 'cooling_districtdata' => $cooling_districtdata, 'occupancydata' => $occupancydata);
                
            }
        }
        ?>

        var arrTitle = ['<?php echo lang("site"); ?>'];
        var arrValuesMulti = [];
        <?php 
                if($totalElectricity != 0){ ?>
                    arrTitle.push('<?php echo lang("electricity"); ?>');
        <?php   } ?>
        <?php if($totalFuel != 0){ ?>
                    arrTitle.push('<?php echo lang("fuel"); ?>');
        <?php   } ?>
        <?php if($totalLpg != 0){ ?>
                    arrTitle.push('<?php echo lang("lpg"); ?>');
        <?php   } ?>
        <?php if($totalNaturalGas != 0){ ?>
                    arrTitle.push('<?php echo lang("natural-gas"); ?>');
        <?php   } ?>
        <?php if($totalWater != 0){ ?>
                    arrTitle.push('<?php echo lang("water"); ?>');
        <?php   } ?>
        <?php if($totalHeatingDistrict != 0){ ?>
                    arrTitle.push('<?php echo lang("heating-district"); ?>');
        <?php   } ?>
        <?php if($totalCoolingDistrict != 0){ ?>
                    arrTitle.push('<?php echo lang("cooling-district"); ?>');
        <?php   } ?>
            arrTitle.push('<?php echo lang("occupancy"); ?>');
            arrValuesMulti.push(arrTitle);

			<?php foreach ($array_1 as $key => $val) {
            $electricityVal = $totalElectricity > 0 ? $val['electricitydata'] : '';
            $fuelVal = $totalFuel > 0 ? $val['fueldata'] : '';
            $lpgVal = $totalLpg > 0 ? $val['lpgdata'] : '';
            $natural_gasVal = $totalNaturalGas > 0 ? $val['natural_gasdata'] : '';
            $waterVal = $totalWater > 0 ? $val['waterdata'] : '';
            $heating_districtVal = $totalHeatingDistrict > 0 ? $val['heating_districtdata'] : '';
            $cooling_districtVal = $totalCoolingDistrict > 0 ? $val['cooling_districtdata'] : ''; 
        ?>
            var arrValues = ['<?php echo $val['site']; ?>'];
                <?php if($totalElectricity != 0){ ?>
                        arrValues.push(<?php echo $val['electricitydata']; ?>);
                <?php   } ?>
                <?php if($totalFuel != 0){ ?>
                            arrValues.push(<?php echo $val['fueldata']; ?>);
                <?php   } ?>
                <?php if($totalLpg != 0){ ?>
                            arrValues.push(<?php echo $val['lpgdata']; ?>);
                <?php   } ?>
                <?php if($totalNaturalGas != 0){ ?>
                            arrValues.push(<?php echo $val['natural_gasdata']; ?>);
                <?php   } ?>
                <?php if($totalWater != 0){ ?>
                            arrValues.push(<?php echo $val['waterdata']; ?>);
                <?php   } ?>
                <?php if($totalHeatingDistrict != 0){ ?>
                            arrValues.push(<?php echo $val['heating_districtdata']; ?>);
                <?php   } ?>
                <?php if($totalCoolingDistrict != 0){ ?>
                            arrValues.push(<?php echo $val['cooling_districtdata']; ?>);
                <?php   } ?>
                arrValues.push(<?php echo $val['occupancydata']; ?>);
                arrValuesMulti.push(arrValues);
        
			<?php }
			if ($report_type == "total_utilities_by_room_night_and_build_area") {
				$report_title = 'sites_total_utilities_by_cost_report_title';
			}
			?>
			var siteCostXaxisData = [];
			var siteCostDataArray = [];
			var siteCostSubtitle = arrValuesMulti[0];
			siteCostSubtitleName = siteCostSubtitle.filter(value => value !== "Site");
			for (var i = 1; i < arrValuesMulti.length; i++) {
				siteCostXaxisData.push(arrValuesMulti[i][0]);
			}
			$.each(siteCostSubtitleName, function(i) {
				var key = siteCostSubtitleName[i];
				siteCostDataArray[key] = [];
				for (var j = 1; j < arrValuesMulti.length; j++) {
					siteCostDataArray[key].push(arrValuesMulti[j][i + 1]);
				}
			});
			var siteCostSeries = [];
			Object.entries(siteCostDataArray).forEach(([key, value]) => {
				if (!(key == 'Occupancy')) {
					if (key == 'Electricity') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['Electricity']; ?>',
						}, );
					}
					if (key == 'Natural Gas') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>'
						}, );
					}
					if (key == 'Fuel') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['Fuel']; ?>'
						}, );
					}
					if (key == 'LPG') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['LPG']; ?>'
						}, );
					}
					if (key == 'Water') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['Water']; ?>'
						}, );
					}
					if (key == 'District Cooling') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Cooling']; ?>'
						}, );
					}
					if (key == 'District Heating') {
						siteCostSeries.push({
							name: key,
							data: siteCostDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Heating']; ?>'
						}, );
					}
				} else {
					siteCostSeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: siteCostDataArray[key],
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
			Highcharts.chart('sites_chart_cost', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<?php echo lang($report_title); ?>',
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
				xAxis: {
					title: {
						text: '<?php echo lang("sites"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					categories: siteCostXaxisData
				},
				yAxis: [{
					min: 0,
					reversedStacks: false,
					title: {
						text: '<?php echo $x_axis_title; ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				}, {
					min: 0,
					tickPositions: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					title: {
						rotation: 270,
						margin: 30,
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
					formatter: function() {
						return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + Highcharts.numberFormat(this.y, 1, '.', ',') + '<br/>';
					}
				},
				plotOptions: {
					column: {
						stacking: 'normal'
					}
				},
				series: siteCostSeries
			});
		<?php } ?>

/*******************************************************/
/*              Cost chart end                         */  
/*******************************************************/

		<?php if (!empty($sites)) {
		$totalElectricity = 0;
		$totalFuel = 0;
		$totalLpg = 0;
		$totalNaturalGas = 0;
		$totalWater = 0;
		$totalHeatingDistrict = 0;
		$totalCoolingDistrict = 0;
        foreach ($sites as $site) {
            $sitedata = $site['site_location_name'];
            if (!empty($site['country'])) {
               // $sitedata.= '-' . $site['country'];
            }
            $electricitydata = (!empty($reportdata[$site['id']]['electricity_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['electricity_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $fueldata = (!empty($reportdata[$site['id']]['fuel_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['fuel_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $lpgdata = (!empty($reportdata[$site['id']]['lpg_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['lpg_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $natural_gasdata = (!empty($reportdata[$site['id']]['natural_gas_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['natural_gas_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $waterdata = (!empty($reportdata[$site['id']]['water_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['water_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $heating_districtdata = (!empty($reportdata[$site['id']]['heating_district_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['heating_district_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $cooling_districtdata = (!empty($reportdata[$site['id']]['cooling_district_cost']) && !empty($reportdata[$site['id']]['total_room_night'])) ? ($reportdata[$site['id']]['cooling_district_cost'] / $reportdata[$site['id']]['total_room_night']) : 0;
            $cdddata = (!empty($reportdata[$site['id']]['cdd'])) ? ($reportdata[$site['id']]['cdd']) : 0;
            $occupancydata = (!empty($reportdata[$site['id']]['occupancy'])) ? ($reportdata[$site['id']]['occupancy']) : 0;

            $electricitydata = round($electricitydata, 2);
            $fueldata = round($fueldata, 2);
            $lpgdata = round($lpgdata, 2);
            $natural_gasdata = round($natural_gasdata, 2);
            $waterdata = round($waterdata, 2);
            $heating_districtdata = round($heating_districtdata, 2);
            $cooling_districtdata = round($cooling_districtdata, 2);
            $cdddata = round($cdddata, 2);
            $occupancydata = round($occupancydata, 2);            
            /* dataTable.addRow(["<?php echo $sitedata; ?>", <?php echo $electricitydata; ?>, <?php echo $fueldata; ?>, <?php echo $lpgdata; ?>, <?php echo $natural_gasdata; ?>, <?php echo $waterdata; ?>, <?php echo $heating_districtdata; ?>, <?php echo $cooling_districtdata; ?>, <?php echo $cdddata; ?>, <?php echo $occupancydata; ?>]); */
			
			$totalElectricity += $electricitydata;
			$totalFuel += $fueldata;
			$totalLpg += $lpgdata;
			$totalNaturalGas += $natural_gasdata;
			$totalWater += $waterdata;
			$totalHeatingDistrict += $heating_districtdata;
			$totalCoolingDistrict += $cooling_districtdata;
			
			$array[] = array('site' => $sitedata, 'electricitydata' => $electricitydata, 'fueldata' => $fueldata, 'lpgdata' => $lpgdata, 'natural_gasdata' => $natural_gasdata, 'waterdata' => $waterdata, 'heating_districtdata' => $heating_districtdata, 'cooling_districtdata' => $cooling_districtdata, 'occupancydata' => $occupancydata);
			
        }
	?>	

	
		var arrTitle = ['<?php echo lang("site"); ?>'];
		var arrValuesMulti = [];
		<?php 
				if($totalElectricity != 0){ ?>
					arrTitle.push('<?php echo lang("electricity"); ?>');
		<?php	} ?>
		<?php if($totalFuel != 0){ ?>
					arrTitle.push('<?php echo lang("fuel"); ?>');
		<?php	} ?>
		<?php if($totalLpg != 0){ ?>
					arrTitle.push('<?php echo lang("lpg"); ?>');
		<?php	} ?>
		<?php if($totalNaturalGas != 0){ ?>
					arrTitle.push('<?php echo lang("natural-gas"); ?>');
		<?php	} ?>
		<?php if($totalWater != 0){ ?>
					arrTitle.push('<?php echo lang("water"); ?>');
		<?php	} ?>
		<?php if($totalHeatingDistrict != 0){ ?>
					arrTitle.push('<?php echo lang("heating-district"); ?>');
		<?php	} ?>
		<?php if($totalCoolingDistrict != 0){ ?>
					arrTitle.push('<?php echo lang("cooling-district"); ?>');
		<?php	} ?>
			arrTitle.push('<?php echo lang("occupancy"); ?>');
			arrValuesMulti.push(arrTitle);

			<?php
			foreach ($array as $key => $val) {	?>
				var arrValues = ['<?php echo $val['site']; ?>'];
				<?php if($totalElectricity != 0){ ?>
						arrValues.push(<?php echo $val['electricitydata']; ?>);
				<?php	} ?>
				<?php if($totalFuel != 0){ ?>
							arrValues.push(<?php echo $val['fueldata']; ?>);
				<?php	} ?>
				<?php if($totalLpg != 0){ ?>
							arrValues.push(<?php echo $val['lpgdata']; ?>);
				<?php	} ?>
				<?php if($totalNaturalGas != 0){ ?>
							arrValues.push(<?php echo $val['natural_gasdata']; ?>);
				<?php	} ?>
				<?php if($totalWater != 0){ ?>
							arrValues.push(<?php echo $val['waterdata']; ?>);
				<?php	} ?>
				<?php if($totalHeatingDistrict != 0){ ?>
							arrValues.push(<?php echo $val['heating_districtdata']; ?>);
				<?php	} ?>
				<?php if($totalCoolingDistrict != 0){ ?>
							arrValues.push(<?php echo $val['cooling_districtdata']; ?>);
				<?php	} ?>
				arrValues.push(<?php echo $val['occupancydata']; ?>);
				arrValuesMulti.push(arrValues);
			<?php
			}
			if ($report_type == "total_utilities_by_room_night_and_build_area") {
				$report_title = 'sites_total_utilities_by_room_night_report_title';
			}
			?>
			var siteChartXaxisData = [];
			var siteChartDataArray = [];
			var siteChartSubtitle = arrValuesMulti[0];
			siteChartSubtitleName = siteChartSubtitle.filter(value => value !== "Site");
			for (var i = 1; i < arrValuesMulti.length; i++) {
				siteChartXaxisData.push(arrValuesMulti[i][0]);
			}
			$.each(siteChartSubtitleName, function(i) {
				var key = siteChartSubtitleName[i];
				siteChartDataArray[key] = [];
				for (var j = 1; j < arrValuesMulti.length; j++) {
					siteChartDataArray[key].push(arrValuesMulti[j][i + 1]);
				}
			});
			var siteChartSeries = [];
			Object.entries(siteChartDataArray).forEach(([key, value]) => {
				if (!(key == 'Occupancy')) {
					if (key == 'Electricity') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['Electricity']; ?>'
						}, );
					}
					if (key == 'Natural Gas') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>'
						}, );
					}
					if (key == 'Fuel') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['Fuel']; ?>'
						}, );
					}
					if (key == 'LPG') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['LPG']; ?>'
						}, );
					}
					if (key == 'Water') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['Water']; ?>'
						}, );
					}
					if (key == 'District Cooling') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Cooling']; ?>'
						}, );
					}
					if (key == 'District Heating') {
						siteChartSeries.push({
							name: key,
							data: siteChartDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Heating']; ?>'
						}, );
					}
				} else {
					siteChartSeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: siteChartDataArray[key],
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
					type: 'column'
				},
				title: {
					text: '<?php echo lang($report_title); ?>',
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
				xAxis: {
					title: {
						text: '<?php echo lang("sites"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					categories: siteChartXaxisData
				},
				yAxis: [{
					min: 0,
					reversedStacks: false,
					title: {
						text: '<?php echo $x_axis_title; ?> / Room night',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				}, {
					min: 0,
					tickPositions: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					title: {
						rotation: 270,
						margin: 30,
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
					formatter: function() {
						return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + Highcharts.numberFormat(this.y, 1, '.', ',') + '<br/>';
					}
				},
				plotOptions: {
					column: {
						stacking: 'normal'
					}
				},
				series: siteChartSeries
			});
		<?php } ?>
		<?php
		if ($filters['is_buildarea'] || $report_type == "total_utilities_by_room_night_and_build_area") { ?>
			<?php if (!empty($sites)) {
				$totalElectricity = 0;
				$totalFuel = 0;
				$totalLpg = 0;
				$totalNaturalGas = 0;
				$totalWater = 0;
				$totalHeatingDistrict = 0;
				$totalCoolingDistrict = 0;
				$array_1 = array();
				foreach ($sites as $site) {
					$sitedata = $site['site_location_name'];
					$electricitydata = (!empty($reportdata[$site['id']]['electricity_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['electricity_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['electricity_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['electricity_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$fueldata = (!empty($reportdata[$site['id']]['fuel_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['fuel_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['fuel_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['fuel_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$lpgdata = (!empty($reportdata[$site['id']]['lpg_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['lpg_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['lpg_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['lpg_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$natural_gasdata = (!empty($reportdata[$site['id']]['natural_gas_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['natural_gas_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['natural_gas_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['natural_gas_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$waterdata = (!empty($reportdata[$site['id']]['water_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['water_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['water_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['water_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$heating_districtdata = (!empty($reportdata[$site['id']]['heating_district_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['heating_district_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['heating_district_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['heating_district_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$cooling_districtdata = (!empty($reportdata[$site['id']]['cooling_district_cost']) && !empty($reportdata[$site['id']]['site_builtup_area']) && is_numeric($reportdata[$site['id']]['cooling_district_cost']) && is_numeric($reportdata[$site['id']]['site_builtup_area']) && is_finite($reportdata[$site['id']]['cooling_district_cost']) && is_finite($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['cooling_district_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
					$cdddata = (!empty($reportdata[$site['id']]['cdd']) && is_numeric($reportdata[$site['id']]['cdd']) && is_finite($reportdata[$site['id']]['cdd'])) ? ($reportdata[$site['id']]['cdd']) : 0;
					$occupancydata = (!empty($reportdata[$site['id']]['occupancy']) && is_numeric($reportdata[$site['id']]['occupancy']) && is_finite($reportdata[$site['id']]['occupancy'])) ? ($reportdata[$site['id']]['occupancy']) : 0;

                $electricitydata = round($electricitydata, 2);
                $fueldata = round($fueldata, 2);
                $lpgdata = round($lpgdata, 2);
                $natural_gasdata = round($natural_gasdata, 2);
                $waterdata = round($waterdata, 2);
                $heating_districtdata = round($heating_districtdata, 2);
                $cooling_districtdata = round($cooling_districtdata, 2);
                $cdddata = round($cdddata, 2);
                $occupancydata = round($occupancydata, 2);
                
				
				$totalElectricity += $electricitydata;
				$totalFuel += $fueldata;
				$totalLpg += $lpgdata;
				$totalNaturalGas += $natural_gasdata;
				$totalWater += $waterdata;
				$totalHeatingDistrict += $heating_districtdata;
				$totalCoolingDistrict += $cooling_districtdata;
				
				$array_1[] = array('site' => $sitedata, 'electricitydata' => $electricitydata, 'fueldata' => $fueldata, 'lpgdata' => $lpgdata, 'natural_gasdata' => $natural_gasdata, 'waterdata' => $waterdata, 'heating_districtdata' => $heating_districtdata, 'cooling_districtdata' => $cooling_districtdata, 'occupancydata' => $occupancydata);
				}
		}
		?>

		var arrTitle = ['<?php echo lang("site"); ?>'];
		var arrValuesMulti = [];
		<?php 
				if($totalElectricity != 0){ ?>
					arrTitle.push('<?php echo lang("electricity"); ?>');
		<?php	} ?>
		<?php if($totalFuel != 0){ ?>
					arrTitle.push('<?php echo lang("fuel"); ?>');
		<?php	} ?>
		<?php if($totalLpg != 0){ ?>
					arrTitle.push('<?php echo lang("lpg"); ?>');
		<?php	} ?>
		<?php if($totalNaturalGas != 0){ ?>
					arrTitle.push('<?php echo lang("natural-gas"); ?>');
		<?php	} ?>
		<?php if($totalWater != 0){ ?>
					arrTitle.push('<?php echo lang("water"); ?>');
		<?php	} ?>
		<?php if($totalHeatingDistrict != 0){ ?>
					arrTitle.push('<?php echo lang("heating-district"); ?>');
		<?php	} ?>
		<?php if($totalCoolingDistrict != 0){ ?>
					arrTitle.push('<?php echo lang("cooling-district"); ?>');
		<?php	} ?>
			arrTitle.push('<?php echo lang("occupancy"); ?>');
			arrValuesMulti.push(arrTitle);

			<?php
			foreach ($array_1 as $key => $val) {
				$electricityVal = $totalElectricity > 0 ? $val['electricitydata'] : '';
				$fuelVal = $totalFuel > 0 ? $val['fueldata'] : '';
				$lpgVal = $totalLpg > 0 ? $val['lpgdata'] : '';
				$natural_gasVal = $totalNaturalGas > 0 ? $val['natural_gasdata'] : '';
				$waterVal = $totalWater > 0 ? $val['waterdata'] : '';
				$heating_districtVal = $totalHeatingDistrict > 0 ? $val['heating_districtdata'] : '';
				$cooling_districtVal = $totalCoolingDistrict > 0 ? $val['cooling_districtdata'] : '';
			?>
				var arrValues = ['<?php echo $val['site']; ?>'];
				<?php if ($totalElectricity != 0) { ?>
					arrValues.push(<?php echo $val['electricitydata']; ?>);
				<?php	} ?>
				<?php if($totalFuel != 0){ ?>
							arrValues.push(<?php echo $val['fueldata']; ?>);
				<?php	} ?>
				<?php if($totalLpg != 0){ ?>
							arrValues.push(<?php echo $val['lpgdata']; ?>);
				<?php	} ?>
				<?php if($totalNaturalGas != 0){ ?>
							arrValues.push(<?php echo $val['natural_gasdata']; ?>);
				<?php	} ?>
				<?php if($totalWater != 0){ ?>
							arrValues.push(<?php echo $val['waterdata']; ?>);
				<?php	} ?>
				<?php if($totalHeatingDistrict != 0){ ?>
							arrValues.push(<?php echo $val['heating_districtdata']; ?>);
				<?php	} ?>
				<?php if($totalCoolingDistrict != 0){ ?>
							arrValues.push(<?php echo $val['cooling_districtdata']; ?>);
				<?php	} ?>
				arrValues.push(<?php echo $val['occupancydata']; ?>);
				arrValuesMulti.push(arrValues);

	<?php	
		}
        
        if($report_type == "total_utilities_by_room_night_and_build_area") {
            $report_title = 'sites_total_utilities_by_build_area_report_title';
        }
        ?>
			var siteChartBuildUpXaxisData = [];
			var siteChartBuildUpDataArray = [];
			var siteChartBuildUpSubtitle = arrValuesMulti[0];
			siteChartBuildUpSubtitleName = siteChartBuildUpSubtitle.filter(value => value !== "Site");
			for (var i = 1; i < arrValuesMulti.length; i++) {
				siteChartBuildUpXaxisData.push(arrValuesMulti[i][0]);
			}
			$.each(siteChartBuildUpSubtitleName, function(i) {
				var key = siteChartBuildUpSubtitleName[i];
				siteChartBuildUpDataArray[key] = [];
				for (var j = 1; j < arrValuesMulti.length; j++) {
					siteChartBuildUpDataArray[key].push(arrValuesMulti[j][i + 1]);
				}
			});
			var siteChartBuildUpSeries = [];
			Object.entries(siteChartBuildUpDataArray).forEach(([key, value]) => {
				if (!(key == 'Occupancy')) {
					if (key == 'Electricity') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['Electricity']; ?>'
						}, );
					}
					if (key == 'Natural Gas') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>'
						}, );
					}
					if (key == 'Fuel') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['Fuel']; ?>'
						}, );
					}
					if (key == 'LPG') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['LPG']; ?>'
						}, );
					}
					if (key == 'Water') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['Water']; ?>'
						}, );
					}
					if (key == 'District Cooling') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Cooling']; ?>'
						}, );
					}
					if (key == 'District Heating') {
						siteChartBuildUpSeries.push({
							name: key,
							data: siteChartBuildUpDataArray[key],
							color: '<?php echo $chart_legend_colors['District_Heating']; ?>'
						}, );
					}
				} else {
					siteChartBuildUpSeries.push({
						type: 'spline',
						name: key,
						yAxis: 1,
						data: siteChartBuildUpDataArray[key],
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
			Highcharts.chart('sites_chart_build_area', {
				chart: {
					type: 'column'
				},
				title: {
					text: '<?php echo lang($report_title); ?>',
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
				xAxis: {
					title: {
						text: '<?php echo lang("sites"); ?>',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					},
					categories: siteChartBuildUpXaxisData
				},
				yAxis: [{
					min: 0,
					reversedStacks: false,
					title: {
						text: '<?php echo $x_axis_title; ?> / Built up area',
						style: {
							color: Highcharts.getOptions().colors[1],
							fontFamily: 'Arial',
							fontSize: '15px',
							fontWeight: 'bold',
						}
					}
				}, {
					min: 0,
					tickPositions: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100],
					title: {
						rotation: 270,
						margin: 30,
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
					formatter: function() {
						return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + Highcharts.numberFormat(this.y, 1, '.', ',') + '<br/>';
					}
				},
				plotOptions: {
					column: {
						stacking: 'normal'
					}
				},
				series: siteChartBuildUpSeries
			});
		<?php } ?>
	}

    $(document).ready(function() {
        $("#site_type").change(function() {
            var site_type = $(this).val();
            if(site_type == 3){
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
    });
</script>