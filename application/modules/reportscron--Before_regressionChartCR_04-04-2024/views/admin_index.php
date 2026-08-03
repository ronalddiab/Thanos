<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>


<div id="ajax_table" class="report-detail">
	
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/export-data.js"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/data.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/gstatic_loader.js"></script>
    <script type="text/javascript" src="<?php echo site_url(); ?>themes/default/js/google_charts.js"></script>

    <?php
    $CI = get_instance();
    $getData = $CI->input->get();
    $curent_id = 0;
    if (!empty($getData)) {
	$curent_id = $getData['ni'];
    }

    if($getData['type'] == 'annual'){
	$template = 'admin_index_template_annual';
    }else if($getData['type'] == 'mytd'){
	$template = 'admin_index_template_mytd';
    }else{
	echo 'END';exit;
    }

    foreach ($sites as $site) {
	$id = $site['site_detail']['id'];
	echo $CI->load->view($template, $site);
    }

    foreach ($sites as $key => $site) {
	$id = $site['site_detail']['id'];

	/*$attributes = array('name' => 'report_form_' . $id, 'id' => 'report_form_' . $id, 'enctype' => 'multipart/form-data');

	echo form_open('reportscron?ni=' . $id, $attributes);
	echo form_hidden('view_type', 'pdf', 'view_type');
	echo form_hidden('columnChartImg_' . $id, '', 'columnChartImg_' . $id);
	echo form_hidden('columnChartCarbonFootprintImg_' . $id, '', 'columnChartCarbonFootprintImg_' . $id);
	echo form_hidden('columnChartCarbonFootprintMonthlyImg_' . $id, '', 'columnChartCarbonFootprintMonthlyImg_' . $id);
	echo form_hidden('columnChartCarbonFootprintAnnualImg_' . $id, '', 'columnChartCarbonFootprintAnnualImg_' . $id);
	echo form_hidden('columnChartImg_hidden_' . $id, '', 'columnChartImg_hidden_' . $id);
	echo form_hidden('columnChartImg_monthly_' . $id, '', 'columnChartImg_monthly_' . $id);
	echo form_hidden('columnChartImg_monthly_month_' . $id, '', 'columnChartImg_monthly_month_' . $id);
	echo form_hidden('pieChartImg_' . $id, '', 'pieChartImg_' . $id);
	echo form_hidden('pieChartNewImg_' . $id, '', 'pieChartNewImg_' . $id);
	echo form_hidden('pieChartImg_hidden_' . $id, '', 'pieChartImg_hidden_' . $id);
	echo form_hidden('pieChartNewImg_hidden_' . $id, '', 'pieChartNewImg_hidden_' . $id);
	echo form_hidden('pieChartNew2Img_' . $id, '', 'pieChartNew2Img_' . $id);
	echo form_hidden('pieChartNew3Img_' . $id, '', 'pieChartNew3Img_' . $id);
	echo form_hidden('wasteChartImg_' . $id, '', 'wasteChartImg_' . $id);
	echo form_hidden('wastePieChartImg_' . $id, '', 'wastePieChartImg_' . $id);
	echo form_hidden('wasteLandfillPieChartImg_' . $id, '', 'wasteLandfillPieChartImg_' . $id);
	echo form_hidden('pieAnnualChartNewImg_hidden_' . $id, '', 'pieAnnualChartNewImg_hidden_' . $id);
	echo form_hidden('pieAnnualLandfillImg_hidden_' . $id, '', 'pieAnnualLandfillImg_hidden_' . $id);
	echo form_hidden('wasteMonthlyChartImg_' . $id, '', 'wasteMonthlyChartImg_' . $id);
	echo form_hidden('wasteMonthlyChartImg_month_' . $id, '', 'wasteMonthlyChartImg_month_' . $id);
	echo form_hidden('wastePieMonthlyChartImg_' . $id, '', 'wastePieMonthlyChartImg_' . $id);
	echo form_hidden('wastePieMonthlyChartImg_month_' . $id, '', 'wastePieMonthlyChartImg_month_' . $id);
	echo form_hidden('wastePieLandfillMonthlyChartImg_' . $id, '', 'wastePieLandfillMonthlyChartImg_' . $id);
	echo form_hidden('wasteChartPreImg_hidden_' . $id, '', 'wasteChartPreImg_hidden_' . $id);

	echo form_hidden('current_id', $curent_id, 'current_id');
	if(date('m') == 1){
	    echo form_hidden('monthly_report_month', 12, 'monthly_report_month');
	    echo form_hidden('monthly_report_year', (date('Y')-1), 'monthly_report_month');
	}else{
	    echo form_hidden('monthly_report_month', (date('m')-1), 'monthly_report_month');
	    echo form_hidden('monthly_report_year', (date('Y')), 'monthly_report_month');
	}*/
	?>
	<form name="report_form_<?php echo $id; ?>" id="report_form_<?php echo $id; ?>" enctype="multipart/form-data" method="post" action="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>reportscron?ni=<?php echo $id; ?>&type=<?php echo $getData['type']; ?>">
	    <input type="hidden" name="view_type" id="view_type" value="pdf" >
	    <input type="hidden" name="columnChartImg_<?php echo $id; ?>" id="columnChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartCarbonFootprintImg_<?php echo $id; ?>" id="columnChartCarbonFootprintImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartCarbonFootprintMonthlyImg_<?php echo $id; ?>" id="columnChartCarbonFootprintMonthlyImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartCarbonFootprintAnnualImg_<?php echo $id; ?>" id="columnChartCarbonFootprintAnnualImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartImg_hidden_<?php echo $id; ?>" id="columnChartImg_hidden_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartImg_monthly_<?php echo $id; ?>" id="columnChartImg_monthly_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="columnChartImg_monthly_month_<?php echo $id; ?>" id="columnChartImg_monthly_month_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartImg_<?php echo $id; ?>" id="pieChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartNewImg_<?php echo $id; ?>" id="pieChartNewImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartImg_hidden_<?php echo $id; ?>" id="pieChartImg_hidden_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartNewImg_hidden_<?php echo $id; ?>" id="pieChartNewImg_hidden_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartNew2Img_<?php echo $id; ?>" id="pieChartNew2Img_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieChartNew3Img_<?php echo $id; ?>" id="pieChartNew3Img_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wasteChartImg_<?php echo $id; ?>" id="wasteChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wastePieChartImg_<?php echo $id; ?>" id="wastePieChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wasteLandfillPieChartImg_<?php echo $id; ?>" id="wasteLandfillPieChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieAnnualChartNewImg_hidden_<?php echo $id; ?>" id="pieAnnualChartNewImg_hidden_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="pieAnnualLandfillImg_hidden_<?php echo $id; ?>" id="pieAnnualLandfillImg_hidden_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wasteMonthlyChartImg_<?php echo $id; ?>" id="wasteMonthlyChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wasteMonthlyChartImg_month_<?php echo $id; ?>" id="wasteMonthlyChartImg_month_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wastePieMonthlyChartImg_<?php echo $id; ?>" id="wastePieMonthlyChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wastePieMonthlyChartImg_month_<?php echo $id; ?>" id="wastePieMonthlyChartImg_month_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wastePieLandfillMonthlyChartImg_<?php echo $id; ?>" id="wastePieLandfillMonthlyChartImg_<?php echo $id; ?>" value="" >
	    <input type="hidden" name="wasteChartPreImg_hidden_<?php echo $id; ?>" id="wasteChartPreImg_hidden_<?php echo $id; ?>" value="" >

	    <input type="hidden" name="current_id" id="current_id" value="<?php echo $current_id; ?>" >
	    <?php

	    if(date('m') == 1){
		$monthly_report_month1 = 12;
		$monthly_report_year1  = (date('Y')-1);
	    }else{

		$monthly_report_month1 = (date('m')-1);
		$monthly_report_year1  = (date('Y'));
	    }?>

	    <input type="hidden" name="monthly_report_year" id="monthly_report_year" value="<?php echo $monthly_report_year1; ?>" >
	    <input type="hidden" name="monthly_report_month" id="monthly_report_month" value="<?php echo $monthly_report_month1; ?>" >

	    <div class="form-btn-outer" style="float: right; margin-bottom: 50px;">
		<button type="submit" class="btn btn-secondary btn-submit hidden" id="report_btn_<?php echo $id; ?>" name="submit" value="report_btn">Generate Report</button>
	    </div>

	</form>

	<?php
    }

    ?>

</div>
<script type="text/javascript">
    $(document).ready(function () {
	blockUI();
	setTimeout(function () {
	    var querystring = getUrlVars();
	    var action = $('#report_form_'+querystring['ni']).attr("action");
	    action = window.location;
	    if("ni" in querystring){
		if("type" in querystring){
		    $('#report_form_'+querystring["ni"]).attr("action", action);
		}
		$('#report_btn_'+querystring["ni"]).trigger('click');

	    }
	}, 5000);
	unblockUI();

	function getUrlVars()
	{
	    var vars = [], hash;
	    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	    for (var i = 0; i < hashes.length; i++)
	    {
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	    }
	    return vars;
	}
    });

</script>

