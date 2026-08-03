<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$CI =& get_instance();

$chart_legend_colors = $CI->config->config['chart_legend_colors'];

/*$viewName = array();
$viewName['view1'] = $data;
$viewName['view2'] = array();*/
// pre($chart_legend_colors);
// echo "<pre>";
// print_r($reportdata);
// print_r($report_type);
// print_r($filters);
// die;

?>

<div id="ajax_table" class="report-detail">
    <article class="card">
	<!-- <div class="article-header">Group Reports</div> -->
	<div class="card-wrap">
	    <div class="row">
		<div class="col-lg-12">
		    <div id="sites_chart_cost" style="height:800px;" >
			<?php if(empty($reportdata)) { ?>
			    <div class="table-responsive">
				<table class="table table-striped" >
				    <tr>
					<td><?php echo 'no-records'; ?></td>
				    </tr>
				</table>
			    </div>
			<?php } ?>
		    </div>

		    <div id="sites_chart" style="height:800px;">
			<?php if (empty($reportdata)) { ?>
			    <div class="table-responsive">
				<table class="table table-striped" >
				    <tr>
					<td><?php echo 'no-records'; ?></td>
				    </tr>
				</table>
			    </div>
			<?php } ?>
		    </div>

		    <?php
		    if ($filters['is_buildarea'] || $report_type == "total_utilities_by_room_night_and_build_area") { ?>
			<div id="sites_chart_build_area" style="height:800px;">
			    <?php if (empty($reportdata)) { ?>
				<div class="table-responsive">
				    <table class="table table-striped" >
					<tr>
					    <td><?php echo 'no-records'; ?></td>
					</tr>
				    </table>
				</div>
			    <?php } ?>
			</div>
			<?php
		    }
		    ?>
		</div>
		<div id="hidden_charts">
		    <input type="hidden" id="sites_chart_cost_img" name="sites_chart_cost_img" >
		    <input type="hidden" id="sites_chart_img" name="sites_chart_cost_img" >
		    <input type="hidden" id="sites_chart_build_area_img" name="sites_chart_build_area_img" >

		    <img id="sites_chart_cost_img_org" style="width:1000px; height: auto;" >
		    <img id="sites_chart_img_org" style="width:1000px; height: auto;">
		    <img id="sites_chart_build_area_img_org" style="width:1000px; height: auto;">

		</div>
	    </div>
	</div>
    </article>
</div>
<?php
    $csrf_token = $CI->security->get_csrf_token_name();
    $csrf_hash = $CI->security->get_csrf_hash();
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"> </script>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/corechart.js"></script>
<script type="text/javascript">
<?php if (!empty($reportdata)) { ?>
	// blockUI();
	google.load("visualization", "1", {
	    packages: ["corechart"]
	});
	google.setOnLoadCallback(drawChart);
	function drawChart() {

    <?php

    //  Cost chart Start
    if ($report_type == "total_utilities_by_room_night_and_build_area") {
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
		if (!empty($site['country'])) {
		   // $sitedata.= '-' . $site['country'];
		}
		$electricitydata = (!empty($reportdata[$site['id']]['electricity_cost'])) ? ($reportdata[$site['id']]['electricity_cost']) : 0;
		$fueldata = (!empty($reportdata[$site['id']]['fuel_cost'])) ? ($reportdata[$site['id']]['fuel_cost']) : 0;
		$lpgdata = (!empty($reportdata[$site['id']]['lpg_cost'])) ? ($reportdata[$site['id']]['lpg_cost']) : 0;
		$natural_gasdata = (!empty($reportdata[$site['id']]['natural_gas_cost'])) ? ($reportdata[$site['id']]['natural_gas_cost']) : 0;
		$waterdata = (!empty($reportdata[$site['id']]['water_cost'])) ? ($reportdata[$site['id']]['water_cost']) : 0;
		$heating_districtdata = (!empty($reportdata[$site['id']]['heating_district_cost'])) ? ($reportdata[$site['id']]['heating_district_cost']) : 0;
		$cooling_districtdata = (!empty($reportdata[$site['id']]['cooling_district_cost'])) ? ($reportdata[$site['id']]['cooling_district_cost']) : 0;
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

    <?php
	foreach($array_1 as $key=>$val)
	{
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
    <?php
	}

	if($report_type == "total_utilities_by_room_night_and_build_area") {
	    $report_title = 'sites_total_utilities_by_cost_report_title';
	}
	?>
	var data = google.visualization.arrayToDataTable(arrValuesMulti);
	var options = {
	    title: '<?php echo lang($report_title); ?>',
	    titleTextStyle: {
		fontName: 'Arial',
		fontSize: 28
	    },
	    hAxis: {title: '<?php echo lang("sites"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, slantedText: true, slantedTextAngle: 45},
	    vAxes: {
		0: {title: '<?php echo $x_axis_title; ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}},
		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}
	    },
	    seriesType: 'bars',
	    series: {
		<?php $i = 0;
		    if($totalElectricity != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Electricity']; ?>' },
		<?php  $i += 1;  }  ?>
		<?php if($totalFuel != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Fuel']; ?>' },
		<?php   $i += 1; } ?>
		<?php if($totalLpg != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['LPG']; ?>' },
		<?php   $i += 1; } ?>
		<?php if($totalNaturalGas != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },
		<?php   $i += 1; } ?>
		<?php if($totalWater != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Water']; ?>' },
		<?php   $i += 1; } ?>
		<?php if($totalHeatingDistrict != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },
		<?php   $i += 1; } ?>
		<?php if($totalCoolingDistrict != 0){ ?>
			    <?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },
		<?php   $i += 1; } ?>
		<?php echo $i;$i+=1; ?>: {targetAxisIndex: 1, type: "line", pointShape:'square', pointSize:10, color: '<?php echo $chart_legend_colors['Occupancy']; ?>'},
		//8: {targetAxisIndex: 1, type: "line"},
	    },
	    animation: {duration: 500, startup: true},
	    legend: {'position': 'bottom'},
	    isStacked: true
	};

	var chart = new google.visualization.ComboChart(document.getElementById('sites_chart_cost'));

	google.visualization.events.addListener(chart, 'ready', function () {
	    // setTimeout(function(){
		var imgUri = chart.getImageURI();
		document.getElementById('sites_chart_cost_img').value = imgUri;
		// console.log(imgUri);

		$.ajax({
		    type: 'POST',
		    async: false,
		    url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',
		    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_cost_img").val(), filenm: 'sites_chart_cost'},
		    error: function() {
			// alert("Server problem. Please try again.");
			return false;
		    },
		    complete: function() {
		    },
		    success: function(imagepath) {
			$("#sites_chart_cost_img_org").attr('src', imagepath);
			$("#sites_chart_cost").hide();
		    }
		});
	    // }, 10);
	});
	chart.draw(data, options);
    <?php

    }
    // Cost chart end
   ?>

    <?php
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
		<?php if($totalElectricity != 0){ ?>
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
		foreach($array as $key=>$val)
		{
		?>
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
    }

    if($report_type == "total_utilities_by_room_night_and_build_area") {
	$report_title = 'sites_total_utilities_by_room_night_report_title';
    }
    ?>
		var data = google.visualization.arrayToDataTable(arrValuesMulti);
	var options = {
	    title: '<?php echo lang($report_title); ?>',
	    titleTextStyle: {
		fontName: 'Arial',
		fontSize: 28
	    },
	    hAxis: {title: '<?php echo lang("sites"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, slantedText: true, slantedTextAngle: 45},
	    vAxes: {
		0: {title: '<?php echo $x_axis_title; ?> / Room night', titleTextStyle: {fontName: 'Arial', fontSize: 18}},
		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}
	    },
	    seriesType: 'bars',
	    series: {
		<?php $i = 0;
					if($totalElectricity != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Electricity']; ?>' },
				<?php  $i += 1;	 }  ?>
				<?php if($totalFuel != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Fuel']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalLpg != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['LPG']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalNaturalGas != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalWater != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Water']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalHeatingDistrict != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalCoolingDistrict != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },
				<?php	$i += 1; } ?>
		<?php echo $i;$i+=1; ?>: {targetAxisIndex: 1, pointShape:'square', pointSize:10, type: "line", color: '<?php echo $chart_legend_colors['Occupancy']; ?>'},
		//8: {targetAxisIndex: 1, type: "line"},
	    },
	    animation: {duration: 500, startup: true},
	    legend: {'position': 'bottom'},
	    isStacked: true
	};

	var chart = new google.visualization.ComboChart(document.getElementById('sites_chart'));

	google.visualization.events.addListener(chart, 'ready', function () {
	    // setTimeout(function(){
		var imgUri = chart.getImageURI();
		document.getElementById('sites_chart_img').value = imgUri;
		// console.log(imgUri);
		// var res_sites_chart_img = $("#sites_chart_img").val();

		$.ajax({
		    type: 'POST',
		    async: false,
		    url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',
		    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_img").val(), filenm: 'sites_chart' },
		    error: function() {
			// alert("Server problem. Please try again.");
			return false;
		    },
		    complete: function() {
		    },
		    success: function(imagepath) {
			$("#sites_chart_img_org").attr('src', imagepath);
			$("#sites_chart").hide();
		    }
		});

	    // }, 100);
	});

	chart.draw(data, options);

    <?php
    if ($filters['is_buildarea'] || $report_type == "total_utilities_by_room_night_and_build_area") { ?>
	//var dataTable = new google.visualization.DataTable();

	<?php
	if (!empty($sites)) {
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
		if (!empty($site['country'])) {
		   // $sitedata.= '-' . $site['country'];
		}
		$electricitydata = (!empty($reportdata[$site['id']]['electricity_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['electricity_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$fueldata = (!empty($reportdata[$site['id']]['fuel_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['fuel_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$lpgdata = (!empty($reportdata[$site['id']]['lpg_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['lpg_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$natural_gasdata = (!empty($reportdata[$site['id']]['natural_gas_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['natural_gas_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$waterdata = (!empty($reportdata[$site['id']]['water_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['water_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$heating_districtdata = (!empty($reportdata[$site['id']]['heating_district_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['heating_district_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
		$cooling_districtdata = (!empty($reportdata[$site['id']]['cooling_district_cost']) && !empty($reportdata[$site['id']]['site_builtup_area'])) ? ($reportdata[$site['id']]['cooling_district_cost'] / $reportdata[$site['id']]['site_builtup_area']) : 0;
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


				$totalElectricity += $electricitydata;
				$totalFuel += $fueldata;
				$totalLpg += $lpgdata;
				$totalNaturalGas += $natural_gasdata;
				$totalWater += $waterdata;
				$totalHeatingDistrict += $heating_districtdata;
				$totalCoolingDistrict += $cooling_districtdata;

				$array_1[] = array('site' => $sitedata, 'electricitydata' => $electricitydata, 'fueldata' => $fueldata, 'lpgdata' => $lpgdata, 'natural_gasdata' => $natural_gasdata, 'waterdata' => $waterdata, 'heating_districtdata' => $heating_districtdata, 'cooling_districtdata' => $cooling_districtdata, 'occupancydata' => $occupancydata);

				/* dataTable.addRow(["<?php echo $sitedata; ?>", <?php echo $electricitydata; ?>, <?php echo $fueldata; ?>, <?php echo $lpgdata; ?>, <?php echo $natural_gasdata; ?>, <?php echo $waterdata; ?>, <?php echo $heating_districtdata; ?>, <?php echo $cooling_districtdata; ?>, <?php echo $cdddata; ?>, <?php echo $occupancydata; ?>]); */

	    }
		}
		?>

		var arrTitle = ['<?php echo lang("site"); ?>'];
		var arrValuesMulti = [];
		<?php if($totalElectricity != 0){ ?>
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
		foreach($array_1 as $key=>$val)
		{
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
		var data = google.visualization.arrayToDataTable(arrValuesMulti);
	var options = {
	    title: '<?php echo lang($report_title); ?>',
	    titleTextStyle: {
		fontName: 'Arial',
		fontSize: 28
	    },
	    hAxis: {title: '<?php echo lang("sites"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, slantedText: true, slantedTextAngle: 45},
	    vAxes: {
		0: {title: '<?php echo $x_axis_title; ?> / Built up area', titleTextStyle: {fontName: 'Arial', fontSize: 18}},
		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}
	    },
	    seriesType: 'bars',
	    series: {
		<?php $i = 0;
					if($totalElectricity != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Electricity']; ?>' },
				<?php  $i += 1;	 }  ?>
				<?php if($totalFuel != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Fuel']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalLpg != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['LPG']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalNaturalGas != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Natural_Gas']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalWater != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['Water']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalHeatingDistrict != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Heating']; ?>' },
				<?php	$i += 1; } ?>
				<?php if($totalCoolingDistrict != 0){ ?>
							<?php echo $i; ?> : { targetAxisIndex: 0, color: '<?php echo $chart_legend_colors['District_Cooling']; ?>' },
				<?php	$i += 1; } ?>
		<?php echo $i;$i+=1; ?>: {targetAxisIndex: 1, type: "line", pointShape:'square', pointSize:10, color: '<?php echo $chart_legend_colors['Occupancy']; ?>'},
				//8: {targetAxisIndex: 1, type: "line"},
	    },
	    animation: {duration: 500, startup: true},
	    legend: {'position': 'bottom'},
	    isStacked: true
	};

	var chart = new google.visualization.ComboChart(document.getElementById('sites_chart_build_area'));

	google.visualization.events.addListener(chart, 'ready', function () {
	    // setTimeout(function(){
		var imgUri = chart.getImageURI();
		document.getElementById('sites_chart_build_area_img').value = imgUri;
		console.log(imgUri);

		$.ajax({
		    type: 'POST',
		    async: false,
		    url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',
		    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_build_area_img").val(), filenm: 'sites_chart_build_area' },
		    error: function() {
			// alert("Server problem. Please try again.");
			return false;
		    },
		    complete: function() {
		    },
		    success: function(imagepath) {
			$("#sites_chart_build_area_img_org").attr('src', imagepath);
			$("#sites_chart_build_area").hide();
		    }
		});

	    // }, 100);
	});

	chart.draw(data, options);

    <?php } ?>
	    // unblockUI();
	}

	$(window).resize(function() {
	    drawChart();
	});

<?php } ?>


</script>