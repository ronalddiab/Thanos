<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"> </script>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>

<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/corechart.js"></script>



<?php



// if (!defined('BASEPATH'))

//     exit('No direct script access allowed');



$CI =& get_instance();



$chart_legend_colors = $CI->config->config['chart_legend_colors'];

// foreach($allChartData as $chart_data){

//     // pre($chart_data_val);

//     extract($chart_data);



foreach($allChartData as $chart_data =>$chart_data_val){

    // pr("inside main==");

    // pr($chart_data);

    // pre($chart_data_val);

    // if(count($chart_data_val) > 1){

    //     foreach($chart_data_val as $data){

    //         extract($data);

    //     }

    // }else{

    //     extract($chart_data_val);

    // }



    // if(count($chart_data_val) > 1){

    //     foreach($chart_data_val as $data){

    //         extract($data);

    //     }

    // }else{

    //     pr("====");

    //     $chart_data_val = array($chart_data_val);

    // }

    // pre($chart_data_val);

    // if($chart_data == 44){

	foreach($chart_data_val as $region_id_key=>$data){

	    // pr("inside inner loop");

	    // pr($key);

	    // pr("=====");

	    // pr($data);



	    extract($data);



	// }}}



//    pre("exit");

?>



<div id="ajax_table" class="report-detail">

    <article class="card">

	<!-- <div class="article-header">Group Reports</div> -->

	<div class="card-wrap">

	    <div class="row">

		<div class="col-lg-12">

		    <div id="sites_chart_cost" style="height:1500px;width: 3000px;" >

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



		    <div id="sites_chart" style="height:1500px;width:3000px;">

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

			<div id="sites_chart_build_area" style="height:1500px;width: 3000px;">

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

		    <div id="utility_cost_chart_carbon_footprint" style="height:1500px;width: 3000px;">

			<?php if (empty($chart_data['data']['utility_cost_chart'])) { ?>

			    <div class="table-responsive">

				<table class="table table-striped" >

				    <tr>

					<td><?php echo 'no-records'; ?></td>

				    </tr>

				</table>

			    </div>

			<?php } ?>

		    </div>

		</div>

		<div id="hidden_charts">

		    <input type="hidden" id="sites_chart_cost_img" name="sites_chart_cost_img" >

		    <input type="hidden" id="sites_chart_img" name="sites_chart_cost_img" >

		    <input type="hidden" id="sites_chart_build_area_img" name="sites_chart_build_area_img" >



		    <img id="sites_chart_cost_img_org" src="" style="width:1000px; height: auto;" >

		    <img id="sites_chart_img_org" src="" style="width:1000px; height: auto;">

		    <img id="sites_chart_build_area_img_org" src="" style="width:1000px; height: auto;">



		    <input type="hidden" id="sites_chart_cost_img_carbon" name="sites_chart_cost_img_carbon" >

		    <input type="hidden" id="sites_chart_img_carbon" name="sites_chart_cost_img_carbon" >

		    <input type="hidden" id="sites_chart_build_area_img_carbon" name="sites_chart_build_area_img_carbon" >



		    <img id="sites_chart_cost_img_org_carbon" src="" style="width:2000px; height: auto;" >

		    <img id="sites_chart_img_org_carbon" src="" style="width:2000px; height: auto;">

		    <img id="sites_chart_build_area_img_org_carbon" src="" style="width:2000px; height: auto;">

		    <input type="hidden" name="columnChartCarbonFootprintImg" id="columnChartCarbonFootprintImg" value="" >



		</div>

	    </div>

	</div>

    </article>

</div>

<?php

//pre($reportdata);

    $csrf_token = $CI->security->get_csrf_token_name();

    $csrf_hash = $CI->security->get_csrf_hash();

?>



<script type="text/javascript">

<?php

if (!empty($reportdata)) {



?>

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

	    $array_1 = array();

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

			arrValues.push(<?php echo isset($val['electricitydata']) && is_finite($val['electricitydata']) ? $val['electricitydata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalFuel != 0){ ?>

			    arrValues.push(<?php echo isset($val['fueldata']) && is_finite($val['fueldata']) ? $val['fueldata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalLpg != 0){ ?>

			    arrValues.push(<?php echo isset($val['lpgdata']) && is_finite($val['lpgdata']) ? $val['lpgdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalNaturalGas != 0){ ?>

			    arrValues.push(<?php echo isset($val['natural_gasdata']) && is_finite($val['natural_gasdata']) ? $val['natural_gasdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalWater != 0){ ?>

			    arrValues.push(<?php echo isset($val['waterdata']) && is_finite($val['waterdata']) ? $val['waterdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalHeatingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['heating_districtdata']) && is_finite($val['heating_districtdata']) ? $val['heating_districtdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalCoolingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['cooling_districtdata']) && is_finite($val['cooling_districtdata']) ? $val['cooling_districtdata'] : 0; ?>);

		<?php   } ?>

		arrValues.push(<?php echo isset($val['occupancydata']) && is_finite($val['occupancydata']) ? $val['occupancydata'] : 0; ?>);

		arrValuesMulti.push(arrValues);

    <?php

	}

	unset($array_1);



	if($report_type == "total_utilities_by_room_night_and_build_area") {

	    $report_title = 'sites_total_utilities_by_cost_report_title';

	}

	?>

	var data = google.visualization.arrayToDataTable(arrValuesMulti);

	var options = {

	    title: '<?php echo lang($report_title); ?>',

	    titleTextStyle: {

		fontName: 'Arial',

		fontSize: 34

	    },

	    hAxis: {titleTextStyle: {fontName: 'Arial', fontSize: 32}, slantedText: true, slantedTextAngle: 90},

	    vAxes: {

		0: {title: '<?php echo $x_axis_title; ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}},

		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}

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

	chart.clearChart();

	google.visualization.events.addListener(chart, 'ready', function () {

	    // setTimeout(function(){

		var imgUri = chart.getImageURI();

		document.getElementById('sites_chart_cost_img').value = imgUri;

		console.log("imgUri-1");



		$.ajax({

		    type: 'POST',

		    async: false,

		    url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',

		    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_cost_img").val(), filenm: 'sites_chart_cost', user_id: "<?php echo $user['id'];?>", region_id_key: '<?php echo $region_id_key; ?>'},

		    error: function() {

			// alert("Server problem. Please try again.");

			return false;

		    },

		    complete: function() {

		    },

		    success: function(imagepath) {

			$("#sites_chart_cost_img_org").attr('src', imagepath);

			// $("#sites_chart_cost").hide();

		    }

		});

	    // }, 10);

	});

	chart.draw(data, options);

	chart.clearChart();



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

	$array_n = array();

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



	    $array_n[] = array('site' => $sitedata, 'electricitydata' => $electricitydata, 'fueldata' => $fueldata, 'lpgdata' => $lpgdata, 'natural_gasdata' => $natural_gasdata, 'waterdata' => $waterdata, 'heating_districtdata' => $heating_districtdata, 'cooling_districtdata' => $cooling_districtdata, 'occupancydata' => $occupancydata);



	}

	// pre($array_n);

	?>



	var arrTitle = ['<?php echo lang("site"); ?>'];

	var arrValuesMulti = [];

	<?php if($totalElectricity != 0){ ?>

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

	foreach($array_n as $key=>$val)

	{

	?>

		var arrValues = ['<?php echo $val['site']; ?>'];

		<?php if($totalElectricity != 0){ ?>

			arrValues.push(<?php echo isset($val['electricitydata']) && is_finite($val['electricitydata']) ? $val['electricitydata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalFuel != 0){ ?>

			    arrValues.push(<?php echo isset($val['fueldata']) && is_finite($val['fueldata']) ? $val['fueldata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalLpg != 0){ ?>

			    arrValues.push(<?php echo isset($val['lpgdata']) && is_finite($val['lpgdata']) ? $val['lpgdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalNaturalGas != 0){ ?>

			    arrValues.push(<?php echo isset($val['natural_gasdata']) && is_finite($val['natural_gasdata']) ? $val['natural_gasdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalWater != 0){ ?>

			    arrValues.push(<?php echo isset($val['waterdata']) && is_finite($val['waterdata']) ? $val['waterdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalHeatingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['heating_districtdata']) && is_finite($val['heating_districtdata']) ? $val['heating_districtdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalCoolingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['cooling_districtdata']) && is_finite($val['cooling_districtdata']) ? $val['cooling_districtdata'] : 0; ?>);

		<?php   } ?>

		arrValues.push(<?php echo isset($val['occupancydata']) && is_finite($val['occupancydata']) ? $val['occupancydata'] : 0; ?>);

		arrValuesMulti.push(arrValues);

    <?php

	}

	unset($array_n);

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

		fontSize: 34

	    },

	    hAxis: {titleTextStyle: {fontName: 'Arial', fontSize: 32}, slantedText: true, slantedTextAngle: 90},

	    vAxes: {

		0: {title: '<?php echo $x_axis_title; ?> / Room night', titleTextStyle: {fontName: 'Arial', fontSize: 32}},

		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}

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

		<?php echo $i;$i+=1; ?>: {targetAxisIndex: 1, pointShape:'square', pointSize:10, type: "line", color: '<?php echo $chart_legend_colors['Occupancy']; ?>'},

		//8: {targetAxisIndex: 1, type: "line"},

	    },

	    animation: {duration: 500, startup: true},

	    legend: {'position': 'bottom'},

	    isStacked: true

	};



	var chart = new google.visualization.ComboChart(document.getElementById('sites_chart'));

	chart.clearChart();

	google.visualization.events.addListener(chart, 'ready', function () {

	    // setTimeout(function(){

		var imgUri = chart.getImageURI();

		document.getElementById('sites_chart_img').value = imgUri;



		// var res_sites_chart_img = $("#sites_chart_img").val();

		console.log("imgUri-2");

		$.ajax({

		    type: 'POST',

		    async: false,

		    url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',

		    data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_img").val(), filenm: 'sites_chart', user_id: "<?php echo $user['id'];?>", region_id_key: '<?php echo $region_id_key; ?>' },

		    error: function() {

			// alert("Server problem. Please try again.");

			return false;

		    },

		    complete: function() {

		    },

		    success: function(imagepath) {

			$("#sites_chart_img_org").attr('src', imagepath);

			// $("#sites_chart").hide();

		    }

		});



	    // }, 100);

	});



	chart.draw(data, options);

	chart.clearChart();



    //Carbon chart added

    <?php

    // if (!empty($sites)) {

	?>

	// var dataTable = new google.visualization.DataTable();



	<?php

	// pre($sites);

	if (!empty($sites)) {

	    // $utilityArray = array();

	    $totalElectricity = 0;

	    $totalFuel = 0;

	    $totalLpg = 0;

	    $totalNaturalGas = 0;

	    $totalWater = 0;

	    $totalHeatingDistrict = 0;

	    $totalCoolingDistrict = 0;

	    $total_sum_data_occupancy = 0;

	    $total_sum_data_room_night = 0;

	    $total_sum_data_cdd = 0;

	    $total_sum_data_hdd = 0;

	    $array_1 = array();

	    // pre($sites);

	    foreach ($sites as $site) {

		// pr($site);

		$utilityArray[$site['id']] = $site['utility_cost_chart'];

	    }

	    // pre($utilityArray);

	    // pre($utilityArray);

	    foreach ($sites as $site) {

		// $totalElectricity = 0;

		// $totalFuel = 0;

		// $totalLpg = 0;

		// $totalNaturalGas = 0;

		// $totalWater = 0;

		// $totalHeatingDistrict = 0;

		// $totalCoolingDistrict = 0;

		// $total_sum_data_occupancy = 0;

		// $total_sum_data_room_night = 0;

		// $total_sum_data_cdd = 0;

		// $total_sum_data_hdd = 0;

		// pr($site['id']);

		$sitedata = $site['site_location_name'];



	       $data_electricity        = (!empty($utilityArray[$site['id']]['total_electricity_kwh'])) ? ($utilityArray[$site['id']]['total_electricity_kwh'] - $utilityArray[$site['id']]['onsite_generator'] - $utilityArray[$site['id']]['renewable_energy']) : 0;



		$data_fuel               = (!empty($utilityArray[$site['id']]['fuel_consumption'])) ? $utilityArray[$site['id']]['fuel_consumption'] : 0;

		$data_lpg                = (!empty($utilityArray[$site['id']]['lpg_consumption'])) ? $utilityArray[$site['id']]['lpg_consumption'] : 0;

		$data_natural_gas        = (!empty($utilityArray[$site['id']]['natural_gas_consumption'])) ? $utilityArray[$site['id']]['natural_gas_consumption'] : 0;

		$data_heating_district   = (!empty($utilityArray[$site['id']]['heating_district_consumption'])) ? $utilityArray[$site['id']]['heating_district_consumption'] : 0;

		$data_cooling_district   = (!empty($utilityArray[$site['id']]['cooling_district_consumption'])) ? $utilityArray[$site['id']]['cooling_district_consumption'] : 0;



		$data_water              = (!empty($utilityArray[$site['id']]['water'])) ? $utilityArray[$site['id']]['water'] : 0;

		$data_cdd                = (!empty($utilityArray[$site['id']]['cdd'])) ? $utilityArray[$site['id']]['cdd'] : 0;

		$data_hdd                = (!empty($utilityArray[$site['id']]['hdd'])) ? $utilityArray[$site['id']]['hdd'] : 0;

		$data_occupancy          = (!empty($utilityArray[$site['id']]['occupancy'])) ? $utilityArray[$site['id']]['occupancy'] : 0;

		$data_room_night         = (!empty($utilityArray[$site['id']]['room_night'])) ? $utilityArray[$site['id']]['room_night'] : 0;

		$data_electricity_tariff = (!empty($utilityArray[$site['id']]['electricity_tariff'])) ? $utilityArray[$site['id']]['electricity_tariff'] : 0;

		$data_electricity_kwh    = (!empty($utilityArray[$site['id']]['total_electricity_kwh'])) ? $chart_data['data']['utility_cost_chart'][$month][$key1]['total_electricity_kwh'] : 0;



		//Current year

		// pr($data_electricity);





		// current year

		$data_electricity      = round($data_electricity * $site['electricity_emission_factor'], 2);

		$data_fuel             = round($data_fuel * $site['fuel_emission_factor'], 2);

		$data_lpg              = round($data_lpg * $site['lpg_emission_factor'], 2);

		$data_natural_gas      = round($data_natural_gas * $site['natural_gas_emission_factor'], 2);

		$data_heating_district = round($data_heating_district * $site['district_heating_emission_factor'], 2);

		$data_cooling_district = round($data_cooling_district * $site['district_cooling_emission_factor'], 2);

		$data_water            = 0; // There is no calculation for water data



		// Round values

		$data_occupancy     = round($data_occupancy, 2);



		//code on 5th march 2021 for co2/m2

		$data_electricity      = $data_electricity / $site['site_builtup_area'];

		$data_fuel             = $data_fuel / $site['site_builtup_area'];

		$data_lpg              = $data_lpg / $site['site_builtup_area'];

		$data_natural_gas      = $data_natural_gas / $site['site_builtup_area'];

		$data_heating_district = $data_heating_district / $site['site_builtup_area'];

		$data_cooling_district = $data_cooling_district / $site['site_builtup_area'];





		// Total sum Current year data

		// pr($data_electricity);

		$totalElectricity += $data_electricity;

		$totalFuel += $data_fuel;

		$totalLpg += $data_lpg;

		$totalNaturalGas += $data_natural_gas;

		$totalHeatingDistrict += $data_heating_district;

		$totalCoolingDistrict += $data_cooling_district;

		$totalWater += $data_water;

		$total_sum_data_cdd += $data_cdd;

		$total_sum_data_hdd += $data_hdd;

		$total_sum_data_occupancy += $data_occupancy;

		$total_sum_data_room_night += $data_room_night;

		//$total_sum_data_electricity_tariff += $data_electricity_tariff;

		$total_sum_data_electricity_kwh += $data_electricity_kwh;

		// pre($chart_data['data']['utility_cost_chart']);

		// pr($totalElectricity);

		$array_1[] = array('site' => $sitedata,'data_electricity' => $data_electricity, 'data_fuel' => $data_fuel, 'data_lpg' => $data_lpg, 'data_natural_gas' => $data_natural_gas, 'data_water' => $data_water, 'data_heating_district' => $data_heating_district, 'data_cooling_district' => $data_cooling_district, 'data_occupancy' => $data_occupancy);



	    }

	}

	// pr($pre_data_fuel);

	// pre($array_1);

	?>



	var arrValuesMulti = [];

	var arrTitle = ['<?php echo lang("site"); ?>'];

	<?php

	// pre($totalElectricity);

	if($totalElectricity != 0 ){ ?>

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

	    arrTitle.push('<?php echo "Occupancy"; ?>');

	    // arrTitle.push('<?php echo "Occupancy -". $key1; ?>');

	    arrValuesMulti.push(arrTitle);

	<?php



	    // pre($array_1);

	foreach($array_1 as $key=>$val)

	{

	    // pr($val);

	    $electricityVal = $totalElectricity > 0 ? $val['data_electricity'] : '';

	    $fuelVal = $totalFuel > 0 ? $val['data_fuel'] : '';

	    $lpgVal = $totalLpg > 0 ? $val['data_lpg'] : '';

	    $natural_gasVal = $totalNaturalGas > 0 ? $val['data_natural_gas'] : '';

	    $waterVal = $totalWater > 0 ? $val['data_water'] : '';

	    $heating_districtVal = $totalHeatingDistrict > 0 ? $val['data_heating_district'] : '';

	    $cooling_districtVal = $totalCoolingDistrict > 0 ? $val['data_cooling_district'] : '';

	?>

	    var carbonimg = '';

	    var arrValues = ['<?php echo $val['site']; ?>'];

		    <?php

		     if($totalElectricity != 0){ ?>

			arrValues.push(<?php echo isset($val['data_electricity']) && is_finite($val['data_electricity']) ? $val['data_electricity'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalFuel != 0){ ?>

				arrValues.push(<?php echo isset($val['data_fuel']) && is_finite($val['data_fuel']) ? $val['data_fuel'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalLpg != 0){ ?>

				arrValues.push(<?php echo isset($val['data_lpg']) && is_finite($val['data_lpg']) ? $val['data_lpg'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalNaturalGas != 0){ ?>

				arrValues.push(<?php echo isset($val['data_natural_gas']) && is_finite($val['data_natural_gas']) ? $val['data_natural_gas'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalWater != 0){ ?>

				arrValues.push(<?php echo isset($val['data_water']) && is_finite($val['data_water']) ? $val['data_water'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalHeatingDistrict != 0){ ?>

				arrValues.push(<?php echo isset($val['data_heating_district']) && is_finite($val['data_heating_district']) ? $val['data_heating_district'] : 0; ?>);

		    <?php   } ?>

		    <?php if($totalCoolingDistrict != 0){ ?>

				arrValues.push(<?php echo isset($val['data_cooling_district']) && is_finite($val['data_cooling_district']) ? $val['data_cooling_district'] : 0; ?>);

		    <?php   } ?>



		arrValues.push(<?php echo isset($val['data_occupancy']) && is_finite($val['data_occupancy']) ? $val['data_occupancy'] : 0; ?>);

		arrValuesMulti.push(arrValues);

	<?php

	}

	unset($array_1);

	    // $report_title = 'Total Utilities Cost Carbon Footprint';

	    $report_title = 'Carbon intensity comparison';

	?>

	// console.log(arrValuesMulti);

	var data = google.visualization.arrayToDataTable(arrValuesMulti);

	var options = {

	    title: '<?php echo $report_title; ?>',

	    titleTextStyle: {

		fontName: 'Arial',

		fontSize: 34

	    },

	    hAxis: {titleTextStyle: {fontName: 'Arial', fontSize: 32}, slantedText: true, slantedTextAngle: 90},

	    // hAxis: {title: '<?php echo lang("sites"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}, slantedText: true, slantedTextAngle: 90},

	    vAxes: {

		// 0: {title: 'KgCO2e', titleTextStyle: {fontName: 'Arial', fontSize: 18}},

		0: {title: '\n\n\n\n\n\n\nKgCO2/m2', titleTextStyle: {fontName: 'Arial', fontSize: 32}},

		// 1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 18}}

		1: {title: '\n\n\n\n\n\n\n<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}}

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

		<?php echo $i;$i+=1; ?>: {targetAxisIndex: 1, pointShape:'square', pointSize:10, type: "line", color: '<?php echo $chart_legend_colors['Occupancy']; ?>'},

		//8: {targetAxisIndex: 1, type: "line"},

	    },

	    animation: {duration: 500, startup: true},

	    legend: {'position': 'bottom'},

	    isStacked: true

	};



	var chart = new google.visualization.ComboChart(document.getElementById('utility_cost_chart_carbon_footprint'));

	chart.clearChart();

	google.visualization.events.addListener(chart, 'ready', function () {



	    var imgUri = chart.getImageURI();

	    document.getElementById('sites_chart_build_area_img_carbon').value = imgUri;

	    // console.log("imgUri");

	    console.log("imgUri-3-new");



	    $.ajax({

		type: 'POST',

		async: false,

		url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri_new',

		data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_build_area_img_carbon").val(), filenm: 'utility_cost_chart_carbon_footprint', user_id: "<?php echo $user['id'];?>",region_id_key: '<?php echo $region_id_key; ?>' },

		error: function() {

		    // alert("Server problem. Please try again.");

		    return false;

		},

		complete: function() {

		},

		success: function(imagepath) {

		    $("#sites_chart_cost_img_org_carbon").attr('src', imagepath);

		    // $("#sites_chart_build_area").hide();



		    if(imagepath){

			carbonimg = $("#sites_chart_cost_img_org_carbon").attr('src');



		    }

		}

	    });

	});



	chart.draw(data, options);

	chart.clearChart();



    <?php

    //}

    ?>



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

			arrValues.push(<?php echo isset($val['electricitydata']) && is_finite($val['electricitydata']) ? $val['electricitydata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalFuel != 0){ ?>

			    arrValues.push(<?php echo isset($val['fueldata']) && is_finite($val['fueldata']) ? $val['fueldata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalLpg != 0){ ?>

			    arrValues.push(<?php echo isset($val['lpgdata']) && is_finite($val['lpgdata']) ? $val['lpgdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalNaturalGas != 0){ ?>

			    arrValues.push(<?php echo isset($val['natural_gasdata']) && is_finite($val['natural_gasdata']) ? $val['natural_gasdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalWater != 0){ ?>

			    arrValues.push(<?php echo isset($val['waterdata']) && is_finite($val['waterdata']) ? $val['waterdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalHeatingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['heating_districtdata']) && is_finite($val['heating_districtdata']) ? $val['heating_districtdata'] : 0; ?>);

		<?php   } ?>

		<?php if($totalCoolingDistrict != 0){ ?>

			    arrValues.push(<?php echo isset($val['cooling_districtdata']) && is_finite($val['cooling_districtdata']) ? $val['cooling_districtdata'] : 0; ?>);

		<?php   } ?>

		arrValues.push(<?php echo isset($val['occupancydata']) && is_finite($val['occupancydata']) ? $val['occupancydata'] : 0; ?>);

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

		fontSize: 34

	    },

	    hAxis: {titleTextStyle: {fontName: 'Arial', fontSize: 32}, slantedText: true, slantedTextAngle: 90},

	    vAxes: {

		0: {title: '<?php echo $x_axis_title; ?> / Built up area', titleTextStyle: {fontName: 'Arial', fontSize: 32}},

		1: {title: '<?php echo lang("occupancy"); ?>', titleTextStyle: {fontName: 'Arial', fontSize: 32}, 'minValue': 100, ticks: [0,10,20,30,40,50,60,70,80,90,100]}

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



	var chart = new google.visualization.ComboChart(document.getElementById('sites_chart_build_area'));

	chart.clearChart();

	google.visualization.events.addListener(chart, 'ready', function () {



	    var imgUri = chart.getImageURI();

	    document.getElementById('sites_chart_build_area_img').value = imgUri;

	    // console.log(imgUri);

	    console.log("imgUri-4");

	    $.ajax({

		type: 'POST',

		async: false,

		url: '<?php echo base_url() . $this->_data["section_name"]; ?>reportscron/get_image_from_uri',

		data: {<?php echo $csrf_token; ?>: '<?php echo $csrf_hash; ?>',img_url: $("#sites_chart_build_area_img").val(), filenm: 'sites_chart_build_area', user_id: "<?php echo $user['id'];?>" ,region_id_key: '<?php echo $region_id_key; ?>'},

		error: function() {

		    // alert("Server problem. Please try again.");

		    return false;

		},

		complete: function() {

		},

		success: function(imagepath) {

		    $("#sites_chart_build_area_img_org").attr('src', imagepath);



		    if(imagepath){



			var view2img1 = $("#sites_chart_cost_img_org").attr('src');

			var view2img2 = $("#sites_chart_img_org").attr('src');

			var view2img3 = $("#sites_chart_build_area_img_org").attr('src');

		    }

		}

	    });

	});



	chart.draw(data, options);

	chart.clearChart();



    <?php } ?>

    //carbon chart start





    //carbon end

	    // unblockUI();

	}



	$(window).resize(function() {

	    drawChart();

	});



<?php } ?>



</script>



<?php }}?>