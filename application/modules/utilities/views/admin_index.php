<style type="text/css">
.utility_image {
	width: 75px;
    height: 75px;
}
.utility_image_div {
	width: 100px;
    height: 120px;
    border: 2px solid #dbdbdb !important;
    padding: 10px;
}

    .bolder-label {
	color: black;
	font-weight: bolder;
    }

    label.input-control {
	font: inherit;
	padding-top: 9px;
	pointer-events: auto ! important;
	cursor: not-allowed ! important;
    }
</style>
<?php
$utility_types = getUtilityConstant();
$utility_colors = getUtilityPanelColor();
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));

echo add_css(array('MonthPicker.min'));



$budget_disable = '';

$current_month = date('m');

$current_year = date('Y');

$current_week = date('W');

if ($current_week != '1' && $role_id != 1 && $current_year >= $utilities_year) {

    $budget_disable = ' disabled="disabled" style="cursor:not-allowed;" ';
}
?>
<?php
function displayResidenceBlock($residence_types, $utility_types, $current_utility, $utility, $residence_data)
{
    return null;
    $utility_colors = getUtilityPanelColor();
    if (isset($residence_types) && isset($residence_data) && isset($residence_data[$current_utility])) {
	$utilityName =  ' - ' . $utility_types[$current_utility];
	$displayNone = $hideLabels = '';
	$isHotelConnected = '';
	foreach ($residence_types as $key => $value) {
	    if ($value == RENTAL_PROGRAM_RESIDENCE) {
		$name = 'rental_program_residence_' . $current_utility;
		$heading = lang('rental-program-residence') . $utilityName;
		$consumptionMethod = $residence_data[$current_utility]['rental_program_residence_consumption'];
		$inputType = ($consumptionMethod != 1) ? 'hidden' : 'text';
		$hideLabels = ($consumptionMethod != 1) ? '' : "style=display:none;";
		if ($consumptionMethod == 1) {
		    $isHotelConnected = $residence_data[$current_utility]['rental_program_residence_hotel_connected'];
		    if ($isHotelConnected == 1) {
			$displayNone = "style=display:none;";
		    }
		    if ($isHotelConnected == 2) {
			$displayNone = "style=display:block;";
		    }
		}
	    } else if ($value == PRIVATE_RESIDENCE) {
		$name = 'private_program_' . $current_utility;
		$heading = lang('rental-private-residence') . $utilityName;
		$consumptionMethod = $residence_data[$current_utility]['private_program_consumption'];
		$inputType = ($consumptionMethod != 1) ? 'hidden' : 'text';
		$hideLabels = ($consumptionMethod != 1) ? '' : "style=display:none;";
		if ($consumptionMethod == 1) {
		    $isHotelConnected = $residence_data[$current_utility]['private_program_hotel_connected'];
		    if ($isHotelConnected == 1) {
			$displayNone = "style=display:none;";
		    }
		    if ($isHotelConnected == 2) {
			$displayNone = "style=display:block;";
		    }
		}
		$inputType = ($consumptionMethod != 1) ? 'hidden' : 'text';
	    } else if ($value == EMPLOYEE_LIVING_QUARTERS) {
		$name = 'employee_living_quarter_' . $current_utility;
		$heading = lang('employee-living-quarter-residence') . $utilityName;
		$displayNone = "style=display:none;";
		$inputType = 'text';
	    } else if($value == EMPLOYEE_LIVING_QUARTERS_OFFSITE) {
		$name = 'employee_living_quarter_offsite_'.$current_utility;
		$heading = lang('employee-living-quarter-residence-offsite') . $utilityName;
		$displayNone="style=display:none;";
		$inputType = 'text';
	    }
	    $utility[$name] = isset($utility[$name]) ? $utility[$name] : 0;
	    $utility[$name . "_rate"] = isset($utility[$name . "_rate"]) ? $utility[$name . "_rate"] : 0;
	    $utility[$name . "_cost"] = isset($utility[$name . "_cost"]) ? $utility[$name . "_cost"] : 0;
	    echo '<div class="panel panel-primary" style="' . $utility_colors[$current_utility] . '">
		<div class="panel-heading" style="' . $utility_colors[$current_utility . '_heading'] . '"><strong>' . $heading . '</strong></div>
		<div class="panel-body">
		    <ul class="form-outer-block">
			<li>
			    <label class="main-label col-sm-3">Consumption (' . GetSiteUtilityUnitName($utility["site_id"], $current_utility) . ')</label>
			    <div class="row">
				<div class="form-col-3">
				    <input type="' . $inputType . '" id="' . $name . '" name="' . $name . '" class="input-control fuel-oil-helper intcheck" value="' . $utility[$name] . '">
				    <label class="input-label bolder-label" ' . $hideLabels . '>' . $utility[$name] . '</label>
				    <label class="input-label">' . GetSiteUtilityUnitName($utility["site_id"], $current_utility) . '</label>
				</div>
				<div class="form-col-3" ' . $displayNone . '>
				    <input type="' . $inputType . '" id="' . $name . '_rate" name="' . $name . '_rate" class="input-control fuel-oil-rate-helper floatcheck" value="' . $utility[$name . "_rate"] . '">
				    <label class="input-label bolder-label" ' . $hideLabels . '>' . $utility[$name . "_rate"] . '</label>
				    <label class="input-label">' . GetSiteUtilityUnitNameRate($utility["site_id"], $current_utility) . '</label>
				</div>
			    </div>
			</li>
		    </ul>
		</div>
	    </div>';
	}
    }
}
?>

<div id="ajax_table" class="utilities-detail-wrap">

    <article class="card">

	<div class="article-header"><?php echo lang('utilities-title'); ?><?php echo "( " . lang('utilities-title-monthly') . " ) "; ?></div>

	<div class="data-info-block-outer">

	    <div class="row">

		<div class="col-sm-12 Tab-block">

		    <div class="col-lg-4 col-sm-6 col-xs-12">

			<label><?php echo lang('usage-date'); ?></label>

			<div class="data-info-block">

			    <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($utilities_month) && !empty($utilities_year)) ? $utilities_month . '/' . $utilities_year : ''; ?>">

			</div>

		    </div>

		    <div class="data-info-block col-lg-8 col-sm-6 col-xs-12">

			<a href="<?php echo site_url() . BASE_ADMIN_URL_CUSTOM; ?>import/export_monthly_data" class="btn btn-secondary btn-submit"><?php echo lang('utilities-current-export');?></a>

		    </div>

		    <!-- <div class="col-sm-3">
			<label class="col-sm-2"><?php echo lang('projects'); ?></label>
			<div class="form-dropdown col-sm-10">

		    <?php echo form_dropdown('project_id', $projects, $project_id, 'data-type="custom-dropdown"'); ?>

			</div>

		    </div> -->

		</div>

	    </div>

	</div>



	<div class="card-wrap">

	    <!--Horizontal Tab-->
	    <form id="saveform" class="site-info-form" method="post" enctype="multipart/form-data">
		<div id="energy-tabs" class="Tab-block">

		    <ul class="resp-tabs-list hor_1 clearfix">

			<li class="tab-custom-id-1"><?php echo lang('tab-electricity'); ?></li>

			<li class="tab-custom-id-2"><?php echo lang('tab-fuel-oil-gas'); ?></li>

			<li class="tab-custom-id-3"><?php echo lang('tab-water'); ?></li>

			<li class="tab-custom-id-4"><?php echo lang('tab-occupancy-others'); ?></li>

		    </ul>

		    <div class="resp-tabs-container hor_1">

			<div id="tab-1" data-tab-id="1">
			    <?php if (isset($site_detail['show_utility_electricity']) && $site_detail['show_utility_electricity'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['electricity']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['electricity_heading']; ?>"><strong><?php echo lang('total-purchased-electricity-label'); ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('purchased-electricity'); ?></label>

					    <?php

					    if (!empty($tariffs)) {

						foreach ($tariffs as $key => $tariff) {

						    ?>

						    <div class="row add-row">

							<div class="form-col-2 form-col-add">

							    <input name="tariff[total_kwh][]" type="text" class="input-control tariff-kwh-addition intcheck"  value="<?php echo $tariff['total_kwh']; ?>">

							    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>

							</div>

							<div class="form-col-2 form-col-add">

							    <input name="tariff[tariff][]" type="text" class="input-control tariff-addition pricecheck"  value="<?php echo $tariff['tariff']; ?>">

							    <label class="input-label"><?php echo lang('tariff1-name'); ?></label>

							    <input name="tariff[tariff_id][]" value="<?php echo $tariff['id']; ?>" type="hidden" />

							</div>

							<div class="form-col-2 form-col-add">

							    <input name="tariff[total_cost][]" type="text" class="input-control intcheck tariff-kwh-cost"  value="<?php echo $tariff['total_cost']; ?>">

							    <label class="input-label"><?php echo lang('tariff1-total-cost'); ?></label>

							</div>

							<?php if ($key == 0) { ?>

							    <div class="form-col-1">

								<button class="btn-control addition-plus" type='button' data-row="<div class='row add-row'><div class='form-col-2'><input name='tariff[total_kwh][]' type='text' maxlength='11' class='input-control tariff-kwh-addition intcheck'><label class='input-label'><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label></div><div class='form-col-2'><input name='tariff[tariff][]' type='text' maxlength='11' class='input-control tariff-addition pricecheck'><label class='input-label'><?php echo lang('tariff1-name'); ?></label><input name='tariff[tariff_id][]'' value='0' type='hidden' /></div><div class='form-col-2 form-col-add'><input name='tariff[total_cost][]'' type='text' class='input-control intcheck tariff-kwh-cost'><label class='input-label'><?php echo lang('tariff1-total-cost'); ?></label></div><div class='form-col-1'><button type='button' class='btn-control substract-minus'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>

							    </div>

							<?php } else { ?>

							    <div class="form-col-1">

								<button type='button' class="btn-control substract-minus"><img alt="Minus" src="images/minus-icon.png"></button>

							    </div>

							<?php } ?>

						    </div>

						    <?php

						}

					    } else {

						?>

						<div class="row add-row">

						    <div class="form-col-2 form-col-add">

							<input name="tariff[total_kwh][]" type="text" class="input-control tariff-kwh-addition intcheck">

							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>

						    </div>

						    <div class="form-col-2 form-col-add">

							<input name="tariff[tariff][]" type="text" class="input-control tariff-addition pricecheck">

							<label class="input-label"><?php echo lang('tariff1-name'); ?></label>

							<input name="tariff[tariff_id][]" value="0" type="hidden" />

						    </div>

						    <div class="form-col-2 form-col-add">

							<input name="tariff[total_cost][]" type="text" class="input-control intcheck tariff-kwh-cost">

							<label class="input-label"><?php echo lang('tariff1-total-cost'); ?></label>

						    </div>

						    <div class="form-col-1">

							<button class="btn-control addition-plus" type='button' data-row="<div class='row add-row'><div class='form-col-2'><input name='tariff[total_kwh][]' maxlength='11' type='text' class='input-control tariff-kwh-addition intcheck'><label class='input-label'><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label></div><div class='form-col-2'><input name='tariff[tariff][]' maxlength='11' type='text' class='input-control tariff-addition pricecheck'><label class='input-label'><?php echo lang('tariff1-name'); ?></label><input name='tariff[tariff_id][]'' value='0' type='hidden' /></div><div class='form-col-2 form-col-add'><input name='tariff[total_cost][]'' type='text' class='input-control intcheck tariff-kwh-cost'><label class='input-label'><?php echo lang('tariff1-total-cost'); ?></label></div><div class='form-col-1'><button type='button' class='btn-control substract-minus'><img src='images/minus-icon.png' alt='Minus'></button></div></div>"><img src="images/plus-icon.png" alt="Plus"></button>

						    </div>

						</div>

					    <?php } ?>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('maximum-demand'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" id="maximum_demand" name="maximum_demand" class="input-control intcheck"  value="<?php echo $utility['maximum_demand']; ?>">

						    <label class="input-label"><?php echo lang('kva-/-kw'); ?></label>

						</div>

						<?php /* <div class="form-col-3">

						  <div class="form-dropdown">

						  <select name="maximum_demand_unit" data-type="custom-dropdown-maximum-demand">

						  <option <?php echo ($utility['maximum_demand_unit'] == lang('kva'))?'selected="select"':''; ?> value="<?php echo lang('kva'); ?>"><?php echo lang('kva'); ?></option>

						  <option <?php echo ($utility['maximum_demand_unit'] == lang('kwh'))?'selected="select"':''; ?> value="<?php echo lang('kwh'); ?>"><?php echo lang('kwh'); ?></option>

						  </select>

						  </div>

						  </div> */ ?>

						<div class="form-col-2">

						    <input type="text" id="maximum_demand_price" name="maximum_demand_price" class="input-control pricecheck"  value="<?php echo $utility['maximum_demand_price']; ?>">

						    <label class="input-label"><?php echo lang('tariff-kva-kw'); ?></label>

						</div>

						<div class="form-col-2">

						    <input type="text" id="total_maximum_demand" name="total_maximum_demand" class="input-control intcheck"  value="<?php echo $utility['total_maximum_demand']; ?>">

						    <label class="input-label"><?php echo lang('tariff1-total-cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-fees'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" id="fixed_fees" name="fixed_fees" class="input-control negativecheck"  value="<?php echo $utility['fixed_fees']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="electricity_invoice_scan[]" id="electricity_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php foreach (explode(',',$utility['electricity_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if(strtolower($extension) != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="electricity_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image electricity_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>

				</div>

				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-purchased-electricity'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" id="total_purchased_electricity"  name="total_purchased_electricity" class="input-control intcheck" value="<?php echo $utility['total_purchased_electricity']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>

						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('total-purchased-electricity-cost'); ?></label>

						<div class="form-col-2">

						    <input type="text" id="total_purchased_electricity_cost" name="total_purchased_electricity_cost"  class="input-control intcheck" value="<?php echo $utility['total_purchased_electricity_cost']; ?>">

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('average-purchased-electricity'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" id="average_purchased_electricity"  name="average_purchased_electricity" class="input-control floatcheck" value="<?php echo $utility['average_purchased_electricity']; ?>">

						    <label class="input-label"><?php echo lang('$-kwh'); ?></label>

						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('average-pf'); ?></label>

						<div class="form-col-2">

						    <input type="text" id="average_pf" name="average_pf" maxlength="1" class="input-control floatcheck" value="<?php echo $utility['average_pf']; ?>">

						</div>

					    </div>

					</li>

				    </ul>

				</div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('onsite-generators-cost'); ?></label>

					    <div class="row">

						<div class="form-col-6">

						    <input type="text" id="onsite_generators_quantity" name="onsite_generators_quantity" class="input-control intcheck" value="<?php echo $utility['onsite_generators_quantity']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>

						</div>

						<div class="form-col-6">

						    <input type="text" id="total_onsite_generators_cost" name="total_onsite_generators_cost"  class="input-control intcheck" value="<?php echo $utility['total_onsite_generators_cost']; ?>">

						    <?php /* <!-- <input type="text" id="onsite_generators_price" name="onsite_generators_price" class="input-control pricecheck" value="<?php echo $utility['onsite_generators_price']; ?>"> --> */ ?>

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-renewable-energy-production'); ?></label>

					    <div class="row">
						<div class="form-col-6">
						    <input type="text" id="total_renewable_energy_production" name="total_renewable_energy_production" class="input-control intcheck" value="<?php echo $utility['total_renewable_energy_production']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>

						</div>
						<div class="form-col-6">
						    <input type="text" id="total_renewable_energy_production_cost" name="total_renewable_energy_production_cost"  class="input-control intcheck" value="<?php echo $utility['total_renewable_energy_production_cost']; ?>">
						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>

					</li>
					<li>
					    <label class="main-label col-sm-3"><?php echo lang('total-renewable-energy-exported'); ?></label>
					    <div class="row">
						<div class="form-col-6">
						    <input type="text" id="total_renewable_energy_exported" name="total_renewable_energy_exported" class="input-control intcheck" value="<?php echo $utility['total_renewable_energy_exported']; ?>">
						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'electricity'); ?></label>
						</div>
						<div class="form-col-6">
						    <input type="text" id="total_renewable_energy_exported_cost" name="total_renewable_energy_exported_cost"  class="input-control intcheck" value="<?php echo $utility['total_renewable_energy_exported_cost']; ?>">
						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>

				    </ul>
					<hr />
				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-electricity-kwh'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="total_electricity_kwh" name="total_electricity_kwh"  class="input-control intcheck" value="<?php echo $utility['total_electricity_kwh']; ?>">

						</div>

						<div class="form-col-4">

						    <label class="main-label col-sm-3 rightLabel"><?php echo lang('total-electricity-cost'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_electricity_cost" name="total_electricity_cost"  class="input-control intcheck" value="<?php echo $utility['total_electricity_cost']; ?>">

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('average-cost-per-kwh'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="average_cost_per_kwh" name="average_cost_per_kwh"  class="input-control intcheck" value="<?php echo $utility['average_cost_per_kwh']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'electricity'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['electricity']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['electricity_heading']; ?>"><strong><?php echo lang('hotel-label-electricity'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-electricity'); ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="electricity_hotel" name="electricity_hotel" class="input-control intcheck" value="<?php echo $utility['electricity_hotel']; ?>">
						    </div>
						    <div class="form-col-4">
							<label class="main-label col-sm-3 rightLabel"><?php echo lang('total-hotel-purchased-electricity-cost'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="electricity_hotel_cost" name="electricity_hotel_cost" class="input-control intcheck" value="<?php echo $utility['electricity_hotel_cost']; ?>">
						    </div>
						</div>
					    </li>
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('average-cost-per-kwh'); ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="electricity_hotel_rate" name="electricity_hotel_rate" class="input-control intcheck" value="<?php echo $utility['electricity_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'electricity'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				    <div class="panel-footer">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>
						<div class="row">
						    <div class="form-col-2">
							<input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['electricity_total_budget']; ?>" class="input-control intcheck" name="electricity_total_budget" id="electricity_total_budget">
						    </div>
						    <label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>
						    <div class="form-col-2">
							<input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['electricity_total_budget_cost']; ?>" class="input-control intcheck" name="electricity_total_budget_cost" id="electricity_total_budget_cost">
						    </div>
						</div>
					    </li>
					</ul>
				    </div>
				</div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'electricity', $utility, $residence_data); ?>
			    <?php } ?>
			</div>

			<div id="tab-2"  data-tab-id="2">
			    <?php if (isset($site_detail['show_utility_fuel_oil']) && $site_detail['show_utility_fuel_oil'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['fuel_oil']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['fuel_oil_heading']; ?>"><strong><?php echo lang('label-fuel-oil'); ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fuel-oil-hot-water-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'fuel_oil').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_hot_water_boilers" name="fuel_oil_hot_water_boilers"  class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['fuel_oil_hot_water_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_hot_water_boilers_rate" name="fuel_oil_hot_water_boilers_rate"  class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['fuel_oil_hot_water_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_hot_water_boilers_cost" name="fuel_oil_hot_water_boilers_cost"  class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['fuel_oil_hot_water_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('liter_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fuel-oil-steam-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'fuel_oil').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_steam_boilers" name="fuel_oil_steam_boilers"  class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['fuel_oil_steam_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_steam_boilers_rate" name="fuel_oil_steam_boilers_rate"  class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['fuel_oil_steam_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_steam_boilers_cost" name="fuel_oil_steam_boilers_cost"  class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['fuel_oil_steam_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('liter_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fuel-oil-others').'('.GetSiteUtilityUnitName($utility['site_id'],'fuel_oil').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_others" name="fuel_oil_others"  class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['fuel_oil_others']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_others_rate" name="fuel_oil_others_rate"  class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['fuel_oil_others_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="fuel_oil_others_cost" name="fuel_oil_others_cost"  class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['fuel_oil_others_cost']; ?>">

						    <label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('onsite-generators-cost'); ?></label>
						<div class="row">
						<div class="form-col-3">
						    <input type="text" id="onsite_generators_fuel_oil_quantity" name="onsite_generators_fuel_oil_quantity" class="input-control intcheck fuel-oil-helper" value="<?php echo $utility['onsite_generators_fuel_oil_quantity']; ?>">
						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>
						</div>
						<div class="form-col-3">
						    <input type="text" id="onsite_generators_fuel_oil_price" name="onsite_generators_fuel_oil_price"  class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['onsite_generators_fuel_oil_price']; ?>">
						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'fuel_oil'); ?></label>
						</div>
						<div class="form-col-3">
						    <input type="text" id="total_onsite_generators_fuel_oil_cost" name="total_onsite_generators_fuel_oil_cost"  class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['total_onsite_generators_fuel_oil_cost']; ?>">
						    <label class="input-label"><?php echo lang('liter_cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="fuel_oil_invoice_scan[]" id="fuel_oil_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['fuel_oil_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="fuel_oil_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image fuel_oil_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>

				</div>

				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-fuel-oil-cost').'('.GetSiteUtilityUnitName($utility['site_id'],'fuel_oil').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="total_fuel_oil" name="total_fuel_oil"  class="input-control intcheck" value="<?php echo $utility['total_fuel_oil']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'fuel_oil'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_fuel_oil_rate" name="total_fuel_oil_rate"  class="input-control intcheck" value="<?php echo $utility['total_fuel_oil_rate']; ?>">

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_fuel_oil_cost" name="total_fuel_oil_cost"  class="input-control intcheck" value="<?php echo $utility['total_fuel_oil_cost']; ?>">
						    </div>
						</div>
					    </li>
					</ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['fuel_oil']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['fuel_oil_heading']; ?>"><strong><?php echo lang('hotel-label-fuel-oil'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-fuel-oil') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'fuel_oil') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="fuel_oil_hotel" name="fuel_oil_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['fuel_oil_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'fuel_oil'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="fuel_oil_hotel_rate" name="fuel_oil_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['fuel_oil_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'fuel_oil'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="fuel_oil_hotel_cost" name="fuel_oil_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['fuel_oil_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				    <div class="panel-footer">
					<ul class="form-outer-block">
					    <li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['fuel_total_budget']; ?>" class="input-control intcheck" name="fuel_total_budget" id="fuel_total_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['fuel_total_budget_cost']; ?>" class="input-control intcheck" name="fuel_total_budget_cost" id="fuel_total_budget_cost">

						</div>

					    </div>

					</li>



				    </ul>

				</div>

			    </div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'fuel_oil', $utility, $residence_data); ?>
			    <br/>
			    <?php } ?>
			    <?php if (isset($site_detail['show_utility_lpg']) && $site_detail['show_utility_lpg'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['lpg']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['lpg_heading']; ?>"><strong><?php echo lang('label-lpg'); ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('lpg-hot-water-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'lpg').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="lpg_hot_water_boilers" name="lpg_hot_water_boilers"  class="input-control lpg-total-helper intcheck" value="<?php echo $utility['lpg_hot_water_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_hot_water_boilers_rate" name="lpg_hot_water_boilers_rate"  class="input-control lpg-total-rate-helper intcheck" value="<?php echo $utility['lpg_hot_water_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_hot_water_boilers_cost" name="lpg_hot_water_boilers_cost"  class="input-control lpg-total-cost-helper intcheck" value="<?php echo $utility['lpg_hot_water_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('kg_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('lpg-steam-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'lpg').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="lpg_steam_boilers" name="lpg_steam_boilers"  class="input-control lpg-total-helper intcheck" value="<?php echo $utility['lpg_steam_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_steam_boilers_rate" name="lpg_steam_boilers_rate"  class="input-control lpg-total-rate-helper intcheck" value="<?php echo $utility['lpg_steam_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_steam_boilers_cost" name="lpg_steam_boilers_cost"  class="input-control lpg-total-cost-helper intcheck" value="<?php echo $utility['lpg_steam_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('kg_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('lpg-kitchen').'('.GetSiteUtilityUnitName($utility['site_id'],'lpg').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="lpg_kitchen" name="lpg_kitchen"  class="input-control lpg-total-helper intcheck" value="<?php echo $utility['lpg_kitchen']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_kitchen_rate" name="lpg_kitchen_rate"  class="input-control lpg-total-rate-helper intcheck" value="<?php echo $utility['lpg_kitchen_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="lpg_kitchen_cost" name="lpg_kitchen_cost"  class="input-control lpg-total-cost-helper intcheck" value="<?php echo $utility['lpg_kitchen_cost']; ?>">

						    <label class="input-label"><?php echo lang('kg_cost'); ?></label>

						</div>

					    </div>

					</li>

				    </ul>

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-cost'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="lpg_fixed_cost" name="lpg_fixed_cost" class="input-control intcheck"  value="<?php echo $utility['lpg_fixed_cost']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="lpg_invoice_scan[]" id="lpg_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['lpg_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="lpg_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image lpg_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>

				</div>

				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-lpg-cost').'('.GetSiteUtilityUnitName($utility['site_id'],'lpg').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="total_lpg" name="total_lpg"  class="input-control intcheck" value="<?php echo $utility['total_lpg']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'lpg'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_lpg_rate" name="total_lpg_rate"  class="input-control intcheck" value="<?php echo $utility['total_lpg_rate']; ?>">

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_lpg_cost" name="total_lpg_cost"  class="input-control intcheck" value="<?php echo $utility['total_lpg_cost']; ?>">
						    </div>
						</div>
					    </li>
					</ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['lpg']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['lpg_heading']; ?>"><strong><?php echo lang('hotel-label-lpg'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-lpg') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'lpg') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="lpg_hotel" name="lpg_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['lpg_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'lpg'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="lpg_hotel_rate" name="lpg_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['lpg_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'lpg'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="lpg_hotel_cost" name="lpg_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['lpg_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				    <div class="panel-footer">
					<ul class="form-outer-block">
					    <li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['lpg_total_budget']; ?>" class="input-control intcheck" name="lpg_total_budget" id="lpg_total_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['lpg_total_budget_cost']; ?>" class="input-control intcheck" name="lpg_total_budget_cost" id="lpg_total_budget_cost">

						</div>

					    </div>

					</li>



				    </ul>

				</div>

			    </div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'lpg', $utility, $residence_data); ?>
			    <br/>
			    <?php } ?>
			    <?php if (isset($site_detail['show_utility_natural_gas']) && $site_detail['show_utility_natural_gas'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['natural_gas']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['natural_gas_heading']; ?>"><strong><?php echo lang('label-natural-gas') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'natural_gas') . ')'; ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('natural-gas-hot-water-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'natural_gas').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="natural_gas_hot_water_boilers" name="natural_gas_hot_water_boilers"  class="input-control gas-total-helper intcheck" value="<?php echo $utility['natural_gas_hot_water_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_hot_water_boilers_rate" name="natural_gas_hot_water_boilers_rate"  class="input-control gas-total-rate-helper intcheck" value="<?php echo $utility['natural_gas_hot_water_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_hot_water_boilers_cost" name="natural_gas_hot_water_boilers_cost"  class="input-control gas-total-cost-helper intcheck" value="<?php echo $utility['natural_gas_hot_water_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('natural-gas-steam-boilers').'('.GetSiteUtilityUnitName($utility['site_id'],'natural_gas').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="natural_gas_steam_boilers" name="natural_gas_steam_boilers"  class="input-control gas-total-helper intcheck" value="<?php echo $utility['natural_gas_steam_boilers']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_steam_boilers_rate" name="natural_gas_steam_boilers_rate"  class="input-control gas-total-rate-helper intcheck" value="<?php echo $utility['natural_gas_steam_boilers_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_steam_boilers_cost" name="natural_gas_steam_boilers_cost"  class="input-control gas-total-cost-helper intcheck" value="<?php echo $utility['natural_gas_steam_boilers_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('natural-gas-kitchen').'('.GetSiteUtilityUnitName($utility['site_id'],'natural_gas').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="natural_gas_kitchen" name="natural_gas_kitchen"  class="input-control gas-total-helper intcheck" value="<?php echo $utility['natural_gas_kitchen']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_kitchen_rate" name="natural_gas_kitchen_rate"  class="input-control gas-total-rate-helper intcheck" value="<?php echo $utility['natural_gas_kitchen_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="natural_gas_kitchen_cost" name="natural_gas_kitchen_cost"  class="input-control gas-total-cost-helper intcheck" value="<?php echo $utility['natural_gas_kitchen_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>
						    </div>
						</div>
					    </li>
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('onsite-generators-cost'); ?></label>
						<div class="row">
						<div class="form-col-3">
						    <input type="text" id="onsite_generators_natural_gas_quantity" name="onsite_generators_natural_gas_quantity" class="input-control gas-total-helper intcheck" value="<?php echo $utility['onsite_generators_natural_gas_quantity']; ?>">
						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>
						</div>
						<div class="form-col-3">
						    <input type="text" id="onsite_generators_natural_gas_price" name="onsite_generators_natural_gas_price"  class="input-control gas-total-rate-helper intcheck" value="<?php echo $utility['onsite_generators_natural_gas_price']; ?>">
						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'natural_gas'); ?></label>
						</div>
						<div class="form-col-3">
						    <input type="text" id="total_onsite_generators_natural_gas_cost" name="total_onsite_generators_natural_gas_cost"  class="input-control gas-total-cost-helper intcheck" value="<?php echo $utility['total_onsite_generators_natural_gas_cost']; ?>">
						    <label class="input-label"><?php echo lang('liter_cost'); ?></label>
						</div>
					    </div>
					</li>

				    </ul>

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-cost'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="natural_gas_fixed_cost" name="natural_gas_fixed_cost" class="input-control intcheck"  value="<?php echo $utility['natural_gas_fixed_cost']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="natural_gas_invoice_scan[]" id="natural_gas_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['natural_gas_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="natural_gas_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image natural_gas_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>

				</div>

				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-natural-gas-cost').'('.GetSiteUtilityUnitName($utility['site_id'],'natural_gas').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="total_natural_gas" name="total_natural_gas"  class="input-control intcheck" value="<?php echo $utility['total_natural_gas']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'natural_gas'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_natural_gas_rate" name="total_natural_gas_rate"  class="input-control intcheck" value="<?php echo $utility['total_natural_gas_rate']; ?>">

						</div>

						<div class="form-col-3">

						    <input type="text" id="total_natural_gas_cost" name="total_natural_gas_cost"  class="input-control intcheck" value="<?php echo $utility['total_natural_gas_cost']; ?>">
						    </div>
						</div>
					    </li>
					</ul>
					    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['natural_gas']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['natural_gas_heading']; ?>"><strong><?php echo lang('hotel-label-natural-gas'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-natural-gas') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'natural_gas') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="natural_gas_hotel" name="natural_gas_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['natural_gas_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'natural_gas'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="natural_gas_hotel_rate" name="natural_gas_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['natural_gas_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'natural_gas'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="natural_gas_hotel_cost" name="natural_gas_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['natural_gas_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				    <div class="panel-footer">
					<ul class="form-outer-block">
					<li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['natural_gas_total_budget']; ?>" class="input-control intcheck" name="natural_gas_total_budget" id="natural_gas_total_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['natural_gas_total_budget_cost']; ?>" class="input-control intcheck" name="natural_gas_total_budget_cost" id="natural_gas_total_budget_cost">

						</div>

					    </div>

					</li>

				    </ul>

				</div>

			    </div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'natural_gas', $utility, $residence_data); ?>
			    <br/>
			    <?php } ?>
			    <?php if (isset($site_detail['show_utility_district_heating']) && $site_detail['show_utility_district_heating'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['district_heating']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['district_heating_heading']; ?>"><strong><?php echo lang('label-district'); ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('District-heating'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="district_heating" name="district_heating" class="input-control intcheck"  value="<?php echo $utility['district_heating']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'district_heating'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="district_heating_rate" name="district_heating_rate"  class="input-control intcheck" value="<?php echo $utility['district_heating_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'district_heating'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="district_heating_cost" name="district_heating_cost"  class="input-control intcheck" value="<?php echo $utility['district_heating_cost']; ?>">

						    <label class="input-label"><?php echo lang('kwh-label-cost'); ?></label>

						</div>

					    </div>

					</li>

				    </ul>

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-cost'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="district_heating_fixed_cost" name="district_heating_fixed_cost" class="input-control intcheck"  value="<?php echo $utility['district_heating_fixed_cost']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="district_heating_invoice_scan[]" id="district_heating_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['district_heating_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="district_heating_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image district_heating_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['district_heating']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['district_heating_heading']; ?>"><strong><?php echo lang('hotel-label-district-heating'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-district-heating') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'district_heating') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="district_heating_hotel" name="district_heating_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['district_heating_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'district_heating'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="district_heating_hotel_rate" name="district_heating_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['district_heating_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'district_heating'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="district_heating_hotel_cost" name="district_heating_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['district_heating_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['district_heating_total_budget']; ?>" class="input-control intcheck" name="district_heating_total_budget" id="district_heating_total_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['district_heating_total_budget_cost']; ?>" class="input-control intcheck" name="district_heating_total_budget_cost" id="district_heating_total_budget_cost">

						</div>

					    </div>

					</li>



				    </ul>
				    </div>
				</div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'district_heating', $utility, $residence_data); ?>
				<br />
			    <?php } ?>
			    <?php if (isset($site_detail['show_utility_district_cooling']) && $site_detail['show_utility_district_cooling'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['district_cooling']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['district_cooling_heading']; ?>"><strong><?php echo lang('label-district'); ?></strong></div>
				<div class="panel-body">

				    <ul  class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('District-cooling'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="district_cooling" name="district_cooling" class="input-control intcheck"  value="<?php echo $utility['district_cooling']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'district_cooling'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="district_cooling_rate" name="district_cooling_rate"  class="input-control intcheck" value="<?php echo $utility['district_cooling_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'district_cooling'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="district_cooling_cost" name="district_cooling_cost"  class="input-control intcheck" value="<?php echo $utility['district_cooling_cost']; ?>">

						    <label class="input-label"><?php echo lang('kwh-label-cost'); ?></label>

						</div>

					    </div>

					</li>

				    </ul>

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-cost'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="district_cooling_fixed_cost" name="district_cooling_fixed_cost" class="input-control intcheck" value="<?php echo $utility['district_cooling_fixed_cost']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="district_cooling_invoice_scan[]" id="district_cooling_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['district_cooling_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="district_cooling_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image district_cooling_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['district_cooling']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['district_cooling_heading']; ?>"><strong><?php echo lang('hotel-label-district-cooling'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-district-cooling') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'district_cooling') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="district_cooling_hotel" name="district_cooling_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['district_cooling_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'district_cooling'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="district_cooling_hotel_rate" name="district_cooling_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['district_cooling_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'district_cooling'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="district_cooling_hotel_cost" name="district_cooling_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['district_cooling_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['district_cooling_total_budget']; ?>" class="input-control intcheck" name="district_cooling_total_budget" id="district_cooling_total_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['district_cooling_total_budget_cost']; ?>" class="input-control intcheck" name="district_cooling_total_budget_cost" id="district_cooling_total_budget_cost">

						</div>

					    </div>

					</li>



				    </ul>

				</div>

			    </div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'district_cooling', $utility, $residence_data); ?>
				<br />
			    <?php } ?>
			</div>
			<div id="tab-3" data-tab-id="3">
			    <?php if (isset($site_detail['show_utility_water']) && $site_detail['show_utility_water'] != 0) { ?>
				<div class="panel panel-primary" style="<?php echo $utility_colors['water']; ?>">
				    <div class="panel-heading" style="<?php echo $utility_colors['water_heading']; ?>"><strong><?php echo lang('label-water'); ?></strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-utility-supply').'('.GetSiteUtilityUnitName($utility['site_id'],'water').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_utility_supply" name="water_utility_supply"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['water_utility_supply']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_utility_supply_rate" name="water_utility_supply_rate"  class="input-control water-utility-rate-helper intcheck" value="<?php echo $utility['water_utility_supply_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_utility_supply_cost" name="water_utility_supply_cost"  class="input-control water-utility-cost-helper intcheck" value="<?php echo $utility['water_utility_supply_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-irrigation').'('.GetSiteUtilityUnitName($utility['site_id'],'water').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_irrigation" name="water_irrigation"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['water_irrigation']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_irrigation_rate" name="water_irrigation_rate"  class="input-control water-utility-rate-helper intcheck" value="<?php echo $utility['water_irrigation_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_irrigation_cost" name="water_irrigation_cost"  class="input-control water-utility-cost-helper intcheck" value="<?php echo $utility['water_irrigation_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-Cisterns').'('.GetSiteUtilityUnitName($utility['site_id'],'water').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_Cisterns" name="water_Cisterns"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['water_Cisterns']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_Cisterns_rate" name="water_Cisterns_rate"  class="input-control water-utility-rate-helper intcheck" value="<?php echo $utility['water_Cisterns_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_Cisterns_cost" name="water_Cisterns_cost"  class="input-control water-utility-cost-helper intcheck" value="<?php echo $utility['water_Cisterns_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-waste-plant').'('.GetSiteUtilityUnitName($utility['site_id'],'water').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="waste_water" name="waste_water"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['waste_water']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="waste_water_rate" name="waste_water_rate"  class="input-control water-utility-rate-helper intcheck" value="<?php echo $utility['waste_water_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="waste_water_cost" name="waste_water_cost"  class="input-control water-utility-cost-helper intcheck" value="<?php echo $utility['waste_water_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-ro-plant').'('.GetSiteUtilityUnitName($utility['site_id'],'water').')'; ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_ro" name="water_ro"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['water_ro']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_ro_rate" name="water_ro_rate"  class="input-control water-utility-rate-helper intcheck" value="<?php echo $utility['water_ro_rate']; ?>">

						    <label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'],'water'); ?></label>

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_ro_cost" name="water_ro_cost"  class="input-control water-utility-cost-helper intcheck" value="<?php echo $utility['water_ro_cost']; ?>">

						    <label class="input-label"><?php echo lang('m3_cost'); ?></label>

						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('fixed-cost'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_fixed_cost" name="water_fixed_cost" class="input-control intcheck"  value="<?php echo $utility['water_fixed_cost']; ?>">

						    <label class="input-label"><?php echo lang('total-cost'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <div class="row">
						<label class="main-label col-sm-3 "><?php echo lang('insert-invoice-scan'); ?></label>
						<div class="form-col-6">
						    <input name="water_invoice_scan[]" id="water_invoice_scan" type="file" class="custom-file-upload form " multiple>
						</div>
						<?php
						foreach (explode(',',$utility['water_invoice_scan']) as $key => $value) {
						    $extension = substr($value, -3);
						    if($value != '') {
							if($extension != 'pdf') {
							    $fileName = $image_path = $value;
							} else {
							    $fileName = $value;
							    $image_path = site_url() . "/assets/uploads/pdf-image.png";
							}
						    } else {
							$image_path = site_url() . "/assets/uploads/no-image-available.jpg";
						    }
						    $class = 'utility_image_div';
						    ?>
						    <div class="form-col-2 <?php echo $class;?>">
							<a class="close delete_utility_image" href="#" style="display: none;" data-feild="water_invoice_scan">×</a>
							<a href="<?php echo $fileName; ?>" target="_blank" >
							    <img class="utility_image water_invoice_scan" src="<?php echo $image_path; ?>"/>
							</a>
						    </div>
						<?php } ?>
					    </div>
					</li>

				    </ul>

				</div>

				<div class="panel-footer">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('water-total-consumption'); ?></label>

					    <div class="row">

						<div class="form-col-3">

						    <input type="text" id="water_total_consumption" name="water_total_consumption"  class="input-control intcheck water-utility-helper" value="<?php echo $utility['water_total_consumption']; ?>">

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_total_consumption_rate" name="water_total_consumption_rate"  class="input-control intcheck" value="<?php echo $utility['water_total_consumption_rate']; ?>">

						</div>

						<div class="form-col-3">

						    <input type="text" id="water_total_consumption_cost" name="water_total_consumption_cost"  class="input-control intcheck" value="<?php echo $utility['water_total_consumption_cost']; ?>">
						    </div>
						</div>
					    </li>
					</ul>
				    </div>
				</div>
				<div class="panel panel-primary" style="<?php echo $utility_colors['water']; ?>">
                                    <!-- <div class="panel-heading" style="<?php echo $utility_colors['water_heading']; ?>"><strong><?php echo lang('hotel-label-water'); ?></strong></div>
				    <div class="panel-body">
					<ul class="form-outer-block">
					    <li>
						<label class="main-label col-sm-3"><?php echo lang('hotel-label-water') . '(' . GetSiteUtilityUnitName($utility['site_id'], 'water') . ')'; ?></label>
						<div class="row">
						    <div class="form-col-3">
							<input type="text" id="water_hotel" name="water_hotel" class="input-control fuel-oil-helper intcheck" value="<?php echo $utility['water_hotel']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitName($utility['site_id'], 'water'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="water_hotel_rate" name="water_hotel_rate" class="input-control fuel-oil-rate-helper intcheck" value="<?php echo $utility['water_hotel_rate']; ?>">
							<label class="input-label"><?php echo GetSiteUtilityUnitNameRate($utility['site_id'], 'water'); ?></label>
						    </div>
						    <div class="form-col-3">
							<input type="text" id="water_hotel_cost" name="water_hotel_cost" class="input-control fuel-oil-cost-helper intcheck" value="<?php echo $utility['water_hotel_cost']; ?>">
							<label class="input-label"><?php echo lang('liter_cost'); ?></label>
						    </div>
						</div>
					    </li>
					</ul>
                                    </div> -->
				    <div class="panel-footer">
					<ul class="form-outer-block">
					    <li>

					    <label class="main-label col-sm-3 budgetLabel"><?php echo lang('total-budgeted-label'); ?></label>

					    <div class="row">

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['water_total_consumption_budget']; ?>" class="input-control intcheck" name="water_total_consumption_budget" id="water_total_consumption_budget">

						</div>

						<label class="main-label col-sm-4 rightLabel budgetLabel"><?php echo lang('total-budgeted-cost-label'); ?></label>

						<div class="form-col-2">

						    <input type="text" <?php echo $budget_disable; ?> value="<?php echo $utility['water_total_consumption_budget_cost']; ?>" class="input-control intcheck" name="water_total_consumption_budget_cost" id="water_total_consumption_budget_cost">

						</div>

					    </div>

					</li>



				    </ul>

				</div>

			    </div>
				<?php displayResidenceBlock($residence_types, $utility_types, 'water', $utility, $residence_data); ?>
			    <br/>
			    <?php } ?>


			</div>

			<div id="tab-4"  data-tab-id="4">

                            <div class="panel panel-primary">
                                <div class="panel-heading"><strong>Operational Data</strong></div>

				<div class="panel-body">

				    <ul class="form-outer-block">

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-room-night'); ?></label>

					    <div class="row">

						<div class="form-col-3">
						    <input type="textbox" name="total_room_night" class="input-control intcheck"  maxlength="7" value="<?php echo $utility['total_room_night']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['total_room_night']) ? $utility['total_room_night'] : 0; ?></label> -->
						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('total-guests'); ?></label>

						<div class="form-col-3">
						    <input type="textbox" name="total_guests" class="input-control intcheck"  maxlength="7" value="<?php echo $utility['total_guests']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['total_guests']) ? $utility['total_guests'] : 0; ?></label> -->
						</div>

					    </div>

					</li>

					<li>

					    <label class="main-label col-sm-3"><?php echo lang('total-laundered'); ?></label>

					    <div class="row">

						<div class="form-col-3">
						    <input type="textbox" name="total_laundered" class="input-control intcheck" maxlength="7" value="<?php echo $utility['total_laundered']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['total_laundered']) ? $utility['total_laundered'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('kg-label'); ?></label>

						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('total-fb-services'); ?></label>

						<div class="form-col-3">
						    <input type="textbox" name="total_fb_services" class="input-control intcheck" maxlength="7" value="<?php echo $utility['total_fb_services']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['total_fb_services']) ? $utility['total_fb_services'] : 0; ?></label> -->
						</div>

					    </div>

					</li>



					<li>

					    <label class="main-label col-sm-3"><?php echo lang('cdd'); ?></label>

					    <div class="row">

						<div class="form-col-3">
						    <input type="textbox" name="cdd" class="input-control intcheck" value="<?php echo $utility['cdd']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['cdd']) ? $utility['cdd'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('cdd-label'); ?></label>

						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('hdd'); ?></label>

						<div class="form-col-3">
						    <input type="textbox" name="hdd" class="input-control intcheck" value="<?php echo $utility['hdd']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['hdd']) ? $utility['hdd'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('hdd-label'); ?></label>

						</div>

					    </div>

					</li>



					<li>

					    <label class="main-label col-sm-3"><?php echo lang('revenue'); ?></label>

					    <div class="row">

						<div class="form-col-3">
						    <input type="textbox" name="revenue" class="input-control intcheck" value="<?php echo $utility['revenue']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['revenue']) ? $utility['revenue'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('revenue-label'); ?></label>

						</div>

						<label class="main-label col-sm-3 rightLabel"><?php echo lang('forex'); ?></label>

						<div class="form-col-3">
						    <input type="textbox" name="forex" class="input-control floatcheck" value="<?php echo isset($utility['forex']) ? $utility['forex'] : 1; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['forex']) ? $utility['forex'] : 1; ?></label> -->
						    <label class="input-label"><?php echo lang('forex-label'); ?></label>
						</div>
					    </div>
					</li>

					<li>
					    <label class="main-label col-sm-3">#Days</label>
					    <div class="row">
						<div class="form-col-3">
						    <input type="text" name="days" class="input-control intcheck" value="<?php echo $utility['days']; ?>">
						</div>
						<label class="main-label col-sm-3 rightLabel"><?php echo lang('vehicle-petrol'); ?></label>
						<div class="form-col-3">
						    <input type="textbox" name="vehicle_petrol" class="input-control floatcheck" value="<?php echo isset($utility['vehicle_petrol']) ? $utility['vehicle_petrol'] : 0; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['vehicle_petrol']) ? $utility['vehicle_petrol'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('vehicle-petrol-label'); ?></label>
						</div>
					    </div>
					</li>
					<li>
					    <label class="main-label col-sm-3"><?php echo lang('total-f-b-sales'); ?></label>
					    <div class="row">
						<div class="form-col-3">
						    <input type="textbox" name="total_f_b_sales" class="input-control intcheck" value="<?php echo $utility['total_f_b_sales']; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['total_f_b_sales']) ? $utility['total_f_b_sales'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('total-f-b-sales'); ?></label>
						</div>
						<label class="main-label col-sm-3 rightLabel"><?php echo lang('fleet-petrol'); ?></label>
						<div class="form-col-3">
						    <input type="textbox" name="fleet_petrol" class="input-control floatcheck" value="<?php echo isset($utility['fleet_petrol']) ? $utility['fleet_petrol'] : 0; ?>">
						    <!-- <label class="input-control"><?php echo isset($utility['fleet_petrol']) ? $utility['fleet_petrol'] : 0; ?></label> -->
						    <label class="input-label"><?php echo lang('fleet-petrol-label'); ?></label>
						</div>
					    </div>
					</li>
				    </ul>

				</div>

                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-heading"><strong>Budget</strong></div>

                                <div class="panel-body">

                                    <ul class="form-outer-block">

                                        <li>

                                            <label class="main-label col-sm-3"><?php echo lang('total-room-night'); ?></label> 

                                            <div class="row">

                                                <div class="form-col-3">

                                                    <input type="text" id="total_room_night_budget" name="total_room_night_budget"  class="input-control intcheck" value="<?php echo $utility['total_room_night_budget']; ?>">

                                                </div>


                                                <label class="main-label col-sm-3 rightLabel"><?php echo lang('total-guests'); ?></label> 

                                                <div class="form-col-3">

                                                    <input type="text" id="total_guests_budget" name="total_guests_budget"  class="input-control intcheck" value="<?php echo $utility['total_guests_budget']; ?>">

                                                </div>
                                            
                                            </div>

                                        </li>

                                    </ul>

                                </div>
                            </div>
                            
                        </div>

		    </div>

		</div>



		<input type="hidden" id="month" name="month" value="<?php echo $utilities_month; ?>" />

		<input type="hidden" id="year" name="year" value="<?php echo $utilities_year; ?>" />
		<input type="hidden" id="utility_id" name="id" value="<?php echo $id; ?>" />
		<div class="form-btn-outer">

		    <button type="submit" name="submit" value="1" class="btn btn-secondary btn-submit"><?php echo lang('btn-submit'); ?></button>

		</div>



	    </form>

	</div>

    </article>

</div>



<?php $querystr = $this->_ci->security->get_csrf_token_name() . '=' . urlencode($this->_ci->security->get_csrf_hash()); ?>

<script type="text/javascript">

    $(document).ready(function () {
	// delete_utility_image
	$('.utility_image').each(function() {
	    var isrc = $(this).attr('src');
	    if (!(isrc.includes("no-image-available.jpg"))) {
		$(this).parents('.utility_image_div').find('.delete_utility_image').show();
	    } else {
		$(this).parents('.utility_image_div').find('.delete_utility_image').hide();
			$(this).parents('.utility_image_div').css('height', '100px');
	    }
	});


	//delete_utility_image
	$('.delete_utility_image').click(function() {
	    res = confirm('<?php echo lang('delete_confirm_utility') ?>');
	    if(res){
		var $this = $(this);
		var field = $this.attr("data-feild");
		var id    = $("#utility_id").val();
		var field = $this.attr("data-feild");

		$.ajax({
		    type: 'POST',
		    url: '<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>utilities/delete_utility_image',
		    data: {
			id: id,
			field: field
		    },
		    error: function() {
			// alert("Server problem. Please try again.");
			return false;
		    },
		    complete: function() {},
		    success: function(data) {
			$('.'+field).attr('src', '<?php echo site_url() . "/assets/uploads/no-image-available.jpg"; ?>');
			$this.parents('.utility_image_div').find('.delete_utility_image').hide();
			$this.parents('.utility_image_div').css('height', '100px');

		    }
		});
	    }else{
		return false;
	    }
	});


	var days_of_selected_month = <?php echo cal_days_in_month(CAL_GREGORIAN, $utilities_month, $utilities_year); ?>;

	var decimal_point_allowed = 4;

	$("select[data-type='custom-dropdown-maximum-demand']").dropkick({

	    mobile: true

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



	var monthPickerObj = $("#MonthFormat").MonthPicker({

	    /*MonthFormat:'MM yy',

	     StartYear: <?php echo $utilities_year; ?>,

	     SelectedMonth:<?php echo $utilities_month - 2; ?>,*/

	    'OnAfterChooseMonth': function (date) {

		var month = date.getMonth() + 1;

		var year = date.getFullYear();

		ajaxLink('<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>utilities', 'ajax_table', '<?php echo $querystr; ?>&month=' + month + '&year=' + year);

	    }

	});



	//Addition & Subtraction

	// Tariff

	$("#ajax_table .btn-control.addition-plus").click(function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    $this.closest("li").append($this.attr("data-row"));



	    $("#ajax_table .btn-control.substract-minus").click(function (e) {

		e.preventDefault();

		var $this = $(this);

		$this.closest(".row").remove();

		calculate_tarrif_cost();

		$(".tariff-kwh-addition").trigger("change");

	    });

	});



	$(".btn-control.substract-minus").click(function (e) {

	    e.preventDefault();

	    var $this = $(this);

	    $this.closest(".row").remove();

	    calculate_tarrif_cost();

	    $(".tariff-kwh-addition").trigger("change");

	});



	$('#ajax_table').on('change', '.tariff-kwh-addition', function () {

	    var totalkwh = 0;

	    $('#ajax_table .tariff-kwh-addition').each(function () {

		var currentvalue = Math.round(parseFloat($(this).val()));

		if (!isNaN(currentvalue)) {

		    totalkwh += currentvalue;

		}

	    });

	    //$("#total_purchased_electricity").val(totalkwh.toFixed(4));

	    $("#total_purchased_electricity").val(Math.round(parseFloat(totalkwh)));

	    $("#total_purchased_electricity").trigger("change");

	});



	$('#ajax_table').on('change', '.tariff-addition,.tariff-kwh-addition,#fixed_fees', function () {

	    calculate_tarrif_cost();

	});



	function calculate_tarrif_cost() {

	    var $tarrifmap = $('.tariff-addition').map(function () {

		return this.value;

	    });

	    var $tarrifkwh = $('.tariff-kwh-addition').map(function () {

		return this.value;

	    });

	    var $total_cost_obj = $('.tariff-kwh-cost').map(function () {

		return this;

	    });

	    var $fixed_fees = $('#fixed_fees').val();

	    var $it_count = $tarrifmap.length;

	    var $total_cost = 0;

	    var $total_kwh = 0;



	    if ($it_count > 0) {

		for (var i = 0; i < $it_count; i++) {

		    if ($tarrifmap[i] != '' && $tarrifkwh[i] != '') {

			$total_cost += Math.round(parseFloat($tarrifmap[i] * $tarrifkwh[i]));

			$total_kwh += ($tarrifkwh[i] * 1);



			$tarrif_multiplication = $tarrifmap[i] * $tarrifkwh[i];

			//$($total_cost_obj[i]).val($tarrif_multiplication.toFixed(4));

			$($total_cost_obj[i]).val(Math.round(parseFloat($tarrif_multiplication)));

		    }

		}

	    }



	    if ($total_cost > 0 && $total_kwh > 0) {

		$avg_tariff = $total_cost / $total_kwh;

		$avg_tariff = $avg_tariff.toFixed(2);

		$('#average_purchased_electricity').val($avg_tariff);

	    } else {

		$('#average_purchased_electricity').val(0);

	    }



	    $total_cost += ($fixed_fees * 1);



	    //$total_cost = $total_cost.toFixed(4);

	    var $total_maximum_demand = $('#total_maximum_demand').val();

	    $total_cost = Math.round(parseFloat($total_cost + Number($total_maximum_demand)));

	    $('#total_purchased_electricity_cost').val($total_cost);

	    $("#total_purchased_electricity_cost").trigger("change");

	}

	// Tariff





	// Demand

	$('#maximum_demand,#maximum_demand_price').change(function () {

	    var quantity = Math.round(parseFloat($('#maximum_demand').val()));

	    var price = parseFloat($('#maximum_demand_price').val());

	    var total = (quantity * price);



	    if (!isNaN(total)) {

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

		$("#total_maximum_demand").val(total);

	    } else {

		$("#total_maximum_demand").val(0);

	    }

	    $("#total_maximum_demand").trigger("change");

	    calculate_tarrif_cost();

	});

	// Demand



	// Generator

	/*$('#onsite_generators_quantity,#onsite_generators_price').change(function(){

	 var quantity = parseFloat($('#onsite_generators_quantity').val());

	 var price = parseFloat($('#onsite_generators_price').val());

	 var total = (quantity*price);



	 if(!isNaN(total)){

	 total = total.toFixed(4);

	 $("#total_onsite_generators_cost").val(total);

	 }else{

	 $("#total_onsite_generators_cost").val(0);

	 }

	 $("#total_onsite_generators_cost").trigger("change");

	 });*/

	// Generator





	$('#ajax_table').on('change', '#lpg_fixed_cost', function () {

	    var $lpg_fixed_cost = $('#lpg_fixed_cost').val();

	    var $total_lpg_cost = 0;

	    $total_lpg_cost += ($lpg_fixed_cost * 1);

	    var $lpg_hot_water_boilers_cost = $('#lpg_hot_water_boilers_cost').val();

	    var $lpg_steam_boilers_cost = $('#lpg_steam_boilers_cost').val();

	    var $lpg_kitchen_cost = $('#lpg_kitchen_cost').val();

	    $total_lpg_cost = Math.round(parseFloat($total_lpg_cost + Number($lpg_hot_water_boilers_cost) + Number($lpg_steam_boilers_cost) + Number($lpg_kitchen_cost)));

	    $('#total_lpg_cost').val($total_lpg_cost);

	    $("#total_lpg_cost").trigger("change");

	});



	$('#ajax_table').on('change', '#water_fixed_cost', function () {

	    var $water_fixed_cost = $('#water_fixed_cost').val();

	    var $total_water_cost = 0;

	    $total_water_cost += ($water_fixed_cost * 1);

	    var $water_utility_supply_cost = $('#water_utility_supply_cost').val();

	    var $water_irrigation_cost = $('#water_irrigation_cost').val();

	    var $water_Cisterns_cost = $('#water_Cisterns_cost').val();

	    var $waste_water_cost = $('#waste_water_cost').val();

	    var $water_ro_cost = $('#water_ro_cost').val();

	    $total_water_cost = Math.round(parseFloat($total_water_cost + Number($water_utility_supply_cost) + Number($water_irrigation_cost) + Number($water_Cisterns_cost) + Number($waste_water_cost) + Number($water_ro_cost)));

	    $('#water_total_consumption_cost').val($total_water_cost);

	    $("#water_total_consumption_cost").trigger("change");

	});



	$('#ajax_table').on('change', '#natural_gas_fixed_cost', function () {

	    var $natural_gas_fixed_cost = $('#natural_gas_fixed_cost').val();

	    var $total_natural_gas_cost = 0;

	    var $total_kwh = 0;

	    $total_natural_gas_cost += ($natural_gas_fixed_cost * 1);

	    var $natural_gas_hot_water_boilers_cost = $('#natural_gas_hot_water_boilers_cost').val();

	    var $natural_gas_steam_boilers_cost = $('#natural_gas_steam_boilers_cost').val();

	    var $natural_gas_kitchen_cost = $('#natural_gas_kitchen_cost').val();

	    $total_natural_gas_cost = Math.round(parseFloat($total_natural_gas_cost + Number($natural_gas_hot_water_boilers_cost) + Number($natural_gas_steam_boilers_cost) + Number($natural_gas_kitchen_cost)));

	    $('#total_natural_gas_cost').val($total_natural_gas_cost);

	    $("#total_natural_gas_cost").trigger("change");

	});



	// Electricity

	$('#ajax_table').on('change', '#total_purchased_electricity,#onsite_generators_quantity,#total_renewable_energy_production,#total_renewable_energy_exported', function () {

	    changeElectricityKwh();

	});



	function changeElectricityKwh() {

	    var total_purchased_electricity = Math.round(parseFloat($('#total_purchased_electricity').val()));

	    var onsite_generators_quantity = Math.round(parseFloat($('#onsite_generators_quantity').val()));

	    var total_renewable_energy_production = Math.round(parseFloat($('#total_renewable_energy_production').val()));
	    var total_renewable_energy_exported = Math.round(parseFloat($('#total_renewable_energy_exported').val()));



	    if (isNaN(total_purchased_electricity)) {

		total_purchased_electricity = 0;

	    }

	    if (isNaN(onsite_generators_quantity)) {

		onsite_generators_quantity = 0;

	    }

	    if (isNaN(total_renewable_energy_production)) {

		total_renewable_energy_production = 0;

	    }

	    if (isNaN(total_renewable_energy_exported)) {

		total_renewable_energy_exported = 0;

	    }
	    var total = (total_purchased_electricity + onsite_generators_quantity + total_renewable_energy_production - total_renewable_energy_exported);



	    if (!isNaN(total)) {

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

		$("#total_electricity_kwh").val(total);

	    } else {

		$("#total_electricity_kwh").val(0);

	    }



	    changeElectricityAvgParKwh();

	}



	$('#ajax_table').on('change', '#total_purchased_electricity_cost,#total_maximum_demand,#total_onsite_generators_cost,#total_renewable_energy_production_cost,#total_renewable_energy_exported_cost', function () {

	    changeElectricityTotalCost();

	});



	function changeElectricityTotalCost() {

	    var total_purchased_electricity_cost = Math.round(parseFloat($('#total_purchased_electricity_cost').val()));

	    var total_maximum_demand = Math.round(parseFloat($('#total_maximum_demand').val()));

	    var total_onsite_generators_cost = Math.round(parseFloat($('#total_onsite_generators_cost').val()));
	    var total_renewable_energy_production_cost = Math.round(parseFloat($('#total_renewable_energy_production_cost').val()));
	    var total_renewable_energy_exported_cost = Math.round(parseFloat($('#total_renewable_energy_exported_cost').val()));


	    if (isNaN(total_purchased_electricity_cost)) {

		total_purchased_electricity_cost = 0;

	    }

	    if (isNaN(total_maximum_demand)) {

		total_maximum_demand = 0;

	    }

	    if (isNaN(total_onsite_generators_cost)) {

		total_onsite_generators_cost = 0;

	    }

	    if(isNaN(total_renewable_energy_production_cost)) {
		total_renewable_energy_production_cost = 0;
	    }
	    if(isNaN(total_renewable_energy_exported_cost)) {
		total_renewable_energy_exported_cost = 0;
	    }
	    // var total = (total_purchased_electricity_cost + total_maximum_demand + total_onsite_generators_cost);



	    var total = (total_purchased_electricity_cost + total_onsite_generators_cost + total_renewable_energy_production_cost - total_renewable_energy_exported_cost);

	    if (!isNaN(total)) {

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

		$("#total_electricity_cost").val(total);

	    } else {

		$("#total_electricity_cost").val(0);

	    }



	    changeElectricityAvgParKwh();

	}



	function changeElectricityAvgParKwh() {

	    var total_electricity_kwh = Math.round(parseFloat($('#total_electricity_kwh').val()));

	    var total_electricity_cost = Math.round(parseFloat($('#total_electricity_cost').val()));



	    if (isNaN(total_electricity_kwh)) {

		total_electricity_kwh = 0;

	    }



	    if (isNaN(total_electricity_cost)) {

		total_electricity_cost = 0;

	    }



	    var avgtotal = (total_electricity_cost / total_electricity_kwh);

	    if (avgtotal > 0) {

		//avgtotal = avgtotal.toFixed(4);

		avgtotal = avgtotal.toFixed(2);

		//avgtotal = Math.round(parseFloat(avgtotal));

	    } else {

		avgtotal = '';

	    }

	    $("#average_cost_per_kwh").val(avgtotal);

	}

	// Electricity



	/*$(".water-total-breakdown-helper").change(function () {

	    var water_consumption_breakdown_cooling_towers_val = Math.round(parseFloat($("#water_consumption_breakdown_cooling_towers").val()));

	    var water_consumption_breakdown_boh_val = Math.round(parseFloat($("#water_consumption_breakdown_boh").val()));

	    var water_consumption_breakdown_rooms_val = Math.round(parseFloat($("#water_consumption_breakdown_rooms").val()));



	    var total = 0;

	    if (!isNaN(water_consumption_breakdown_cooling_towers_val)) {

		total += water_consumption_breakdown_cooling_towers_val;

	    }

	    if (!isNaN(water_consumption_breakdown_boh_val)) {

		total += water_consumption_breakdown_boh_val;

	    }

	    if (!isNaN(water_consumption_breakdown_rooms_val)) {

		total += water_consumption_breakdown_rooms_val;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_consumption_breakdown").val(total);

	});*/



	$(".gas-total-helper").change(function () {

	    var natural_gas_hot_water_boilers = Math.round(parseFloat($("#natural_gas_hot_water_boilers").val()));

	    var natural_gas_steam_boilers = Math.round(parseFloat($("#natural_gas_steam_boilers").val()));

	    var natural_gas_kitchen = Math.round(parseFloat($("#natural_gas_kitchen").val()));
	    var onsite_generators_natural_gas_quantity = Math.round(parseFloat($("#onsite_generators_natural_gas_quantity").val()));

	    var total = 0;

	    if (!isNaN(natural_gas_hot_water_boilers)) {

		total += natural_gas_hot_water_boilers;

	    }

	    if (!isNaN(natural_gas_steam_boilers)) {

		total += natural_gas_steam_boilers;

	    }

	    if (!isNaN(natural_gas_kitchen)) {

		total += natural_gas_kitchen;
	    }
	    if (!isNaN(onsite_generators_natural_gas_quantity)) {
		total += onsite_generators_natural_gas_quantity;
	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_natural_gas").val(total);

	});



	$(".gas-total-rate-helper").change(function () {

	    var natural_gas_hot_water_boilers_rate = Math.round(parseFloat($("#natural_gas_hot_water_boilers_rate").val()));

	    var natural_gas_steam_boilers_rate = Math.round(parseFloat($("#natural_gas_steam_boilers_rate").val()));

	    var natural_gas_kitchen_rate = Math.round(parseFloat($("#natural_gas_kitchen_rate").val()));
	    var onsite_generators_natural_gas_price = Math.round(parseFloat($("#onsite_generators_natural_gas_price").val()));


	    var total = 0;

	    if (!isNaN(natural_gas_hot_water_boilers_rate)) {

		total += natural_gas_hot_water_boilers_rate;

	    }

	    if (!isNaN(natural_gas_steam_boilers_rate)) {

		total += natural_gas_steam_boilers_rate;

	    }

	    if (!isNaN(natural_gas_kitchen_rate)) {

		total += natural_gas_kitchen_rate;
	    }
	    if (!isNaN(onsite_generators_natural_gas_price)) {
		total += onsite_generators_natural_gas_price;
	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_natural_gas_rate").val(total);

	});



	$(".gas-total-cost-helper").change(function () {

	    var natural_gas_hot_water_boilers_cost = Math.round(parseFloat($("#natural_gas_hot_water_boilers_cost").val()));

	    var natural_gas_steam_boilers_cost = Math.round(parseFloat($("#natural_gas_steam_boilers_cost").val()));

	    var natural_gas_kitchen_cost = Math.round(parseFloat($("#natural_gas_kitchen_cost").val()));
	    var total_onsite_generators_natural_gas_cost = Math.round(parseFloat($("#total_onsite_generators_natural_gas_cost").val()));


	    var total = 0;

	    if (!isNaN(natural_gas_hot_water_boilers_cost)) {

		total += natural_gas_hot_water_boilers_cost;

	    }

	    if (!isNaN(natural_gas_steam_boilers_cost)) {

		total += natural_gas_steam_boilers_cost;

	    }

	    if (!isNaN(natural_gas_kitchen_cost)) {

		total += natural_gas_kitchen_cost;
	    }
	    if (!isNaN(total_onsite_generators_natural_gas_cost)) {
		total += total_onsite_generators_natural_gas_cost;
	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_natural_gas_cost").val(total);

	});



	$(".lpg-total-helper").change(function () {

	    var lpg_hot_water_boilers = Math.round(parseFloat($("#lpg_hot_water_boilers").val()));

	    var lpg_steam_boilers = Math.round(parseFloat($("#lpg_steam_boilers").val()));

	    var lpg_kitchen = Math.round(parseFloat($("#lpg_kitchen").val()));



	    var total = 0;

	    if (!isNaN(lpg_hot_water_boilers)) {

		total += lpg_hot_water_boilers;

	    }

	    if (!isNaN(lpg_steam_boilers)) {

		total += lpg_steam_boilers;

	    }

	    if (!isNaN(lpg_kitchen)) {

		total += lpg_kitchen;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_lpg").val(total);

	});



	$(".lpg-total-rate-helper").change(function () {

	    var lpg_hot_water_boilers_rate = parseFloat($("#lpg_hot_water_boilers_rate").val());

	    var lpg_steam_boilers_rate = parseFloat($("#lpg_steam_boilers_rate").val());

	    var lpg_kitchen_rate = parseFloat($("#lpg_kitchen_rate").val());



	    var total = 0;

	    if (!isNaN(lpg_hot_water_boilers_rate)) {

		total += lpg_hot_water_boilers_rate;

	    }

	    if (!isNaN(lpg_steam_boilers_rate)) {

		total += lpg_steam_boilers_rate;

	    }

	    if (!isNaN(lpg_kitchen_rate)) {

		total += lpg_kitchen_rate;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_lpg_rate").val(total);

	});



	$(".lpg-total-cost-helper").change(function () {

	    var lpg_hot_water_boilers_cost = Math.round(parseFloat($("#lpg_hot_water_boilers_cost").val()));

	    var lpg_steam_boilers_cost = Math.round(parseFloat($("#lpg_steam_boilers_cost").val()));

	    var lpg_kitchen_cost = Math.round(parseFloat($("#lpg_kitchen_cost").val()));



	    var total = 0;

	    if (!isNaN(lpg_hot_water_boilers_cost)) {

		total += lpg_hot_water_boilers_cost;

	    }

	    if (!isNaN(lpg_steam_boilers_cost)) {

		total += lpg_steam_boilers_cost;

	    }

	    if (!isNaN(lpg_kitchen_cost)) {

		total += lpg_kitchen_cost;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_lpg_cost").val(total);

	});



	$(".fuel-oil-helper").change(function () {

	    var fuel_oil_hot_water_boilers = Math.round(parseFloat($("#fuel_oil_hot_water_boilers").val()));

	    var fuel_oil_steam_boilers = Math.round(parseFloat($("#fuel_oil_steam_boilers").val()));

	    var fuel_oil_others = Math.round(parseFloat($("#fuel_oil_others").val()));
	    var onsite_generators_fuel_oil_quantity = Math.round(parseFloat($("#onsite_generators_fuel_oil_quantity").val()));


	    var total = 0;

	    if (!isNaN(fuel_oil_hot_water_boilers)) {

		total += fuel_oil_hot_water_boilers;

	    }

	    if (!isNaN(fuel_oil_steam_boilers)) {

		total += fuel_oil_steam_boilers;

	    }

	    if (!isNaN(fuel_oil_others)) {

		total += fuel_oil_others;
	    }
	    if (!isNaN(onsite_generators_fuel_oil_quantity)) {
		total += onsite_generators_fuel_oil_quantity;
	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_fuel_oil").val(total);

	});



	$(".fuel-oil-rate-helper").change(function () {

	    var fuel_oil_hot_water_boilers_rate = parseFloat($("#fuel_oil_hot_water_boilers_rate").val());

	    var fuel_oil_steam_boilers_rate = parseFloat($("#fuel_oil_steam_boilers_rate").val());

	    var fuel_oil_others_rate = parseFloat($("#fuel_oil_others_rate").val());
	    var onsite_generators_fuel_oil_price = Math.round(parseFloat($("#onsite_generators_fuel_oil_price").val()));


	    var total = 0;

	    if (!isNaN(fuel_oil_hot_water_boilers_rate)) {

		total += fuel_oil_hot_water_boilers_rate;

	    }

	    if (!isNaN(fuel_oil_steam_boilers_rate)) {

		total += fuel_oil_steam_boilers_rate;

	    }

	    if (!isNaN(fuel_oil_others_rate)) {

		total += fuel_oil_others_rate;
	    }
	    if (!isNaN(onsite_generators_fuel_oil_price)) {
		total += onsite_generators_fuel_oil_price;
	    }



	    //total = total.toFixed(4);

	    //total = Math.round(parseFloat(total));

	    total = total.toFixed(2);

	    $("#total_fuel_oil_rate").val(total);

	});



	$(".fuel-oil-cost-helper").change(function () {

	    var fuel_oil_hot_water_boilers_cost = Math.round(parseFloat($("#fuel_oil_hot_water_boilers_cost").val()));

	    var fuel_oil_steam_boilers_cost = Math.round(parseFloat($("#fuel_oil_steam_boilers_cost").val()));

	    var fuel_oil_others_cost = Math.round(parseFloat($("#fuel_oil_others_cost").val()));
	    var total_onsite_generators_fuel_oil_cost = Math.round(parseFloat($("#total_onsite_generators_fuel_oil_cost").val()));


	    var total = 0;

	    if (!isNaN(fuel_oil_hot_water_boilers_cost)) {

		total += fuel_oil_hot_water_boilers_cost;

	    }

	    if (!isNaN(fuel_oil_steam_boilers_cost)) {

		total += fuel_oil_steam_boilers_cost;

	    }

	    if (!isNaN(fuel_oil_others_cost)) {

		total += fuel_oil_others_cost;
	    }
	    if (!isNaN(total_onsite_generators_fuel_oil_cost)) {
		total += total_onsite_generators_fuel_oil_cost;
	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#total_fuel_oil_cost").val(total);

	});



	$("#fuel_oil_hot_water_boilers,#fuel_oil_hot_water_boilers_rate").change(function () {

	    var fuel_oil_hot_water_boilers = Math.round(parseFloat($("#fuel_oil_hot_water_boilers").val()));

	    var fuel_oil_hot_water_boilers_rate = parseFloat($("#fuel_oil_hot_water_boilers_rate").val());



	    var calculation = (fuel_oil_hot_water_boilers * fuel_oil_hot_water_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#fuel_oil_hot_water_boilers_cost").val(total);

	    $("#fuel_oil_hot_water_boilers_cost").trigger('change');

	});



	$("#fuel_oil_steam_boilers,#fuel_oil_steam_boilers_rate").change(function () {

	    var fuel_oil_steam_boilers = Math.round(parseFloat($("#fuel_oil_steam_boilers").val()));

	    var fuel_oil_steam_boilers_rate = parseFloat($("#fuel_oil_steam_boilers_rate").val());



	    var calculation = (fuel_oil_steam_boilers * fuel_oil_steam_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#fuel_oil_steam_boilers_cost").val(total);

	    $("#fuel_oil_steam_boilers_cost").trigger('change');

	});



	$("#fuel_oil_others,#fuel_oil_others_rate").change(function () {

	    var fuel_oil_others = Math.round(parseFloat($("#fuel_oil_others").val()));

	    var fuel_oil_others_rate = parseFloat($("#fuel_oil_others_rate").val());



	    var calculation = (fuel_oil_others * fuel_oil_others_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#fuel_oil_others_cost").val(total);

	    $("#fuel_oil_others_cost").trigger('change');

	});

	$("#onsite_generators_fuel_oil_quantity,#onsite_generators_fuel_oil_price").change(function () {
	    var onsite_generators_fuel_oil_quantity = Math.round(parseFloat($("#onsite_generators_fuel_oil_quantity").val()));
	    var onsite_generators_fuel_oil_price = parseFloat($("#onsite_generators_fuel_oil_price").val());

	    var calculation = (onsite_generators_fuel_oil_quantity * onsite_generators_fuel_oil_price);
	    if (!isNaN(calculation)) {
		total = calculation;
		//total = total.toFixed(4);
		total = Math.round(parseFloat(total));
	    } else {
		total = '';
	    }
	    $("#total_onsite_generators_fuel_oil_cost").val(total);
	    $("#total_onsite_generators_fuel_oil_cost").trigger('change');
	});

	$("#lpg_hot_water_boilers,#lpg_hot_water_boilers_rate").change(function () {

	    var lpg_hot_water_boilers = Math.round(parseFloat($("#lpg_hot_water_boilers").val()));

	    var lpg_hot_water_boilers_rate = parseFloat($("#lpg_hot_water_boilers_rate").val());



	    var calculation = (lpg_hot_water_boilers * lpg_hot_water_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#lpg_hot_water_boilers_cost").val(total);

	    $("#lpg_hot_water_boilers_cost").trigger('change');

	});



	$("#lpg_steam_boilers,#lpg_steam_boilers_rate").change(function () {

	    var lpg_steam_boilers = Math.round(parseFloat($("#lpg_steam_boilers").val()));

	    var lpg_steam_boilers_rate = parseFloat($("#lpg_steam_boilers_rate").val());



	    var calculation = (lpg_steam_boilers * lpg_steam_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#lpg_steam_boilers_cost").val(total);

	    $("#lpg_steam_boilers_cost").trigger('change');

	});



	$("#lpg_kitchen,#lpg_kitchen_rate").change(function () {

	    var lpg_kitchen = Math.round(parseFloat($("#lpg_kitchen").val()));

	    var lpg_kitchen_rate = parseFloat($("#lpg_kitchen_rate").val());



	    var calculation = (lpg_kitchen * lpg_kitchen_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#lpg_kitchen_cost").val(total);

	    $("#lpg_kitchen_cost").trigger('change');

	});



	$("#natural_gas_hot_water_boilers,#natural_gas_hot_water_boilers_rate").change(function () {

	    var natural_gas_hot_water_boilers = Math.round(parseFloat($("#natural_gas_hot_water_boilers").val()));

	    var natural_gas_hot_water_boilers_rate = parseFloat($("#natural_gas_hot_water_boilers_rate").val());



	    var calculation = (natural_gas_hot_water_boilers * natural_gas_hot_water_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#natural_gas_hot_water_boilers_cost").val(total);

	    $("#natural_gas_hot_water_boilers_cost").trigger('change');

	});



	$("#natural_gas_steam_boilers,#natural_gas_steam_boilers_rate").change(function () {

	    var natural_gas_steam_boilers = Math.round(parseFloat($("#natural_gas_steam_boilers").val()));

	    var natural_gas_steam_boilers_rate = parseFloat($("#natural_gas_steam_boilers_rate").val());



	    var calculation = (natural_gas_steam_boilers * natural_gas_steam_boilers_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#natural_gas_steam_boilers_cost").val(total);

	    $("#natural_gas_steam_boilers_cost").trigger('change');
	});





	$("#natural_gas_kitchen,#natural_gas_kitchen_rate").change(function () {

	    var natural_gas_kitchen = Math.round(parseFloat($("#natural_gas_kitchen").val()));

	    var natural_gas_kitchen_rate = parseFloat($("#natural_gas_kitchen_rate").val());



	    var calculation = (natural_gas_kitchen * natural_gas_kitchen_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#natural_gas_kitchen_cost").val(total);

	    $("#natural_gas_kitchen_cost").trigger('change');

	});

	$("#onsite_generators_natural_gas_quantity,#onsite_generators_natural_gas_price").change(function () {
	    var onsite_generators_natural_gas_quantity = Math.round(parseFloat($("#onsite_generators_natural_gas_quantity").val()));
	    var onsite_generators_natural_gas_price = parseFloat($("#onsite_generators_natural_gas_price").val());

	    var calculation = (onsite_generators_natural_gas_quantity * onsite_generators_natural_gas_price);
	    if (!isNaN(calculation)) {
		total = calculation;
		//total = total.toFixed(4);
		total = Math.round(parseFloat(total));
	    } else {
		total = '';
	    }
	    $("#total_onsite_generators_natural_gas_cost").val(total);
	    $("#total_onsite_generators_natural_gas_cost").trigger('change');
	});

	$("#district_heating,#district_heating_rate").change(function () {

	    var district_heating = Math.round(parseFloat($("#district_heating").val()));

	    var district_heating_rate = parseFloat($("#district_heating_rate").val());



	    var total = district_heating * district_heating_rate;

	    if (!isNaN(total)) {

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#district_heating_cost").val(total);

	});



	$("#district_cooling,#district_cooling_rate").change(function () {

	    var district_cooling = Math.round(parseFloat($("#district_cooling").val()));

	    var district_cooling_rate = parseFloat($("#district_cooling_rate").val());



	    var total = district_cooling * district_cooling_rate;

	    if (!isNaN(total)) {

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#district_cooling_cost").val(total);

	});



	$(".water-utility-helper").change(function () {

	    var water_utility_supply = Math.round(parseFloat($("#water_utility_supply").val()));

	    var water_Cisterns = Math.round(parseFloat($("#water_Cisterns").val()));

	    var water_irrigation = Math.round(parseFloat($("#water_irrigation").val()));

	    var water_ro = Math.round(parseFloat($("#water_ro").val()));



	    var total = 0;



	    if (!isNaN(water_utility_supply)) {

		total += water_utility_supply;

	    }



	    if (!isNaN(water_irrigation)) {

		total += water_irrigation;

	    }



	    if (!isNaN(water_Cisterns)) {

		total += water_Cisterns;

	    }



	    if (!isNaN(water_ro)) {

		total += water_ro;

	    }

	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#water_total_consumption").val(total);

	});

	$(".operation-utility-helper").change(function () {

	    var operation_paper_waste = Math.round(parseFloat($("#operation_paper_waste").val()));

	    var operation_glass_waste = Math.round(parseFloat($("#operation_glass_waste").val()));

	    var operation_cardboard_waste = Math.round(parseFloat($("#operation_cardboard_waste").val()));

	    var operation_plastic_waste = Math.round(parseFloat($("#operation_plastic_waste").val()));



	    var total = 0;

	    if (!isNaN(operation_paper_waste)) {

		total += operation_paper_waste;

	    }

	    if (!isNaN(operation_glass_waste)) {

		total += operation_glass_waste;

	    }

	    if (!isNaN(operation_cardboard_waste)) {

		total += operation_cardboard_waste;

	    }

	    if (!isNaN(operation_plastic_waste)) {

		total += operation_plastic_waste;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#operation_recycled_waste").val(total);

	});



	$(".water-utility-rate-helper").change(function () {



	    var water_total_consumption = parseFloat($("#water_total_consumption").val());

	    var water_total_consumption_cost = parseFloat($("#water_total_consumption_cost").val());
	    // console.log(water_total_consumption);
	    // console.log(water_total_consumption_cost);
	    var total = 0;

	    if(water_total_consumption != 0){

		total = water_total_consumption_cost / water_total_consumption;

	    }



	    //total = total.toFixed(4);

	    //total = Math.round(parseFloat(total));

	    total = total.toFixed(2);

	    $("#water_total_consumption_rate").val(total);

	});



	$(".water-utility-cost-helper").change(function () {

	    var water_utility_supply_cost = Math.round(parseFloat($("#water_utility_supply_cost").val()));

	    var waste_water_cost = Math.round(parseFloat($("#waste_water_cost").val()));

	    var water_ro_cost = Math.round(parseFloat($("#water_ro_cost").val()));

	    var water_Cisterns_cost = Math.round(parseFloat($("#water_Cisterns_cost").val()));

	    var water_irrigation_cost = Math.round(parseFloat($("#water_irrigation_cost").val()));



	    var total = 0;

	    if (!isNaN(water_utility_supply_cost)) {

		total += water_utility_supply_cost;

	    }

	    if (!isNaN(waste_water_cost)) {

		total += waste_water_cost;

	    }

	    if (!isNaN(water_ro_cost)) {

		total += water_ro_cost;

	    }

	    if (!isNaN(water_Cisterns_cost)) {

		total += water_Cisterns_cost;

	    }

	    if (!isNaN(water_irrigation_cost)) {

		total += water_irrigation_cost;

	    }



	    //total = total.toFixed(4);

	    total = Math.round(parseFloat(total));

	    $("#water_total_consumption_cost").val(total);

	    $(".water-utility-rate-helper").trigger('change');

	});



	$("#water_utility_supply,#water_utility_supply_rate").change(function () {

	    var water_utility_supply = Math.round(parseFloat($("#water_utility_supply").val()));

	    var water_utility_supply_rate = parseFloat($("#water_utility_supply_rate").val());



	    var calculation = (water_utility_supply * water_utility_supply_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#water_utility_supply_cost").val(total);

	    $("#water_utility_supply_cost").trigger('change');

	});



	$("#water_irrigation, #water_irrigation_rate").change(function () {

	    var water_irrigation = Math.round(parseFloat($("#water_irrigation").val()));

	    var water_irrigation_rate = parseFloat($("#water_irrigation_rate").val());



	    var calculation = (water_irrigation * water_irrigation_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#water_irrigation_cost").val(total);

	    $("#water_irrigation_cost").trigger('change');

	});



	$("#waste_water,#waste_water_rate").change(function () {

	    var waste_water = Math.round(parseFloat($("#waste_water").val()));

	    var waste_water_rate = parseFloat($("#waste_water_rate").val());



	    var calculation = (waste_water * waste_water_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#waste_water_cost").val(total);

	    $("#waste_water_cost").trigger('change');

	});



	$("#water_ro,#water_ro_rate").change(function () {

	    var water_ro = Math.round(parseFloat($("#water_ro").val()));

	    var water_ro_rate = parseFloat($("#water_ro_rate").val());



	    var calculation = (water_ro * water_ro_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#water_ro_cost").val(total);

	    $("#water_ro_cost").trigger('change');

	});



	$("#water_Cisterns,#water_Cisterns_rate").change(function () {

	    var water_Cisterns = Math.round(parseFloat($("#water_Cisterns").val()));

	    var water_Cisterns_rate = parseFloat($("#water_Cisterns_rate").val());



	    var calculation = (water_Cisterns * water_Cisterns_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#water_Cisterns_cost").val(total);

	    $("#water_Cisterns_cost").trigger('change');

	});



	$("#water_utility_supply,#water_utility_supply_rate").change(function () {

	    var water_utility_supply = Math.round(parseFloat($("#water_utility_supply").val()));

	    var water_utility_supply_rate = parseFloat($("#water_utility_supply_rate").val());



	    var calculation = (water_utility_supply * water_utility_supply_rate);

	    if (!isNaN(calculation)) {

		total = calculation;

		//total = total.toFixed(4);

		total = Math.round(parseFloat(total));

	    } else {

		total = '';

	    }

	    $("#water_utility_supply_cost").val(total);

	});





	$("#electricity_total_budget,#fuel_total_budget,#lpg_total_budget,#natural_gas_total_budget,#district_heating_total_budget,#district_cooling_total_budget,#water_total_consumption_budget,#total_consumption_breakdown_budget").change(function () {

	    var idPrefix = $(this).attr('id');

	    var value = parseFloat($(this).val());

	    if (value > 0) {

		var value_1_10 = Math.round(((value / days_of_selected_month) * 10));

		var value_11_20 = Math.round(((value / days_of_selected_month) * 20));

	    } else {

		var value_1_10 = 0;

		var value_11_20 = 0;

	    }



	    $("#" + idPrefix + "_1_10").val(value_1_10);

	    $("#" + idPrefix + "_11_20").val(value_11_20);

	});



	$("#electricity_total_budget_cost,#fuel_total_budget_cost,#lpg_total_budget_cost,#natural_gas_total_budget_cost,#district_heating_total_budget_cost,#district_cooling_total_budget_cost,#water_total_consumption_budget_cost,#total_consumption_breakdown_budget_cost").change(function () {

	    var idPrefix = $(this).attr('id');

	    var value = parseFloat($(this).val());

	    if (value > 0) {

		var value_1_10 = Math.round(((value / days_of_selected_month) * 10));

		var value_11_20 = Math.round(((value / days_of_selected_month) * 20));

	    } else {

		var value_1_10 = 0;

		var value_11_20 = 0;

	    }



	    $("#" + idPrefix + "_1_10").val(value_1_10);

	    $("#" + idPrefix + "_11_20").val(value_11_20);

	});



	$('#saveform').on('keydown', '.intcheck', function (event) {

	    var key = event.charCode || event.keyCode || 0;

	    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY

	    // home, end

	    var keycharcheck = (

		    key == 8 ||

		    key == 9 ||

		    key == 13 ||

		    key == 46 ||

		    key == 190 ||

		    key == 17 ||

		    key == 67 ||

		    key == 86 ||

		    (key >= 35 && key <= 40) ||

		    (key >= 48 && key <= 57) ||

		    (key >= 96 && key <= 105));



	    if (!keycharcheck) {

		event.preventDefault();

		return false;

	    }
	});
	$('#saveform').on('click', function(event) {
	    changeElectricityKwh();
	    changeElectricityTotalCost();
	    changeElectricityAvgParKwh();
	});





	$('#saveform').on('keydown', '.negativecheck', function (event) {

	    var key = event.charCode || event.keyCode || 0;

	    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY

	    // home, end, negative values

	    var keycharcheck = (

		    key == 8 ||

		    key == 9 ||

		    key == 13 ||

		    key == 46 ||

		    key == 190 ||

		    key == 17 ||

		    key == 67 ||

		    key == 86 ||

		    key == 109 ||

		    (key >= 35 && key <= 40) ||

		    (key >= 48 && key <= 57) ||

		    (key >= 96 && key <= 105));



	    if (!keycharcheck) {

		event.preventDefault();

		return false;

	    }

	});



	// Allow formate like 123456.1234 only

	$('#saveform').on('keydown', '.floatcheck', function (event) {

	    var key = event.charCode || event.keyCode || 0;

	    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY

	    // home, end, period, and numpad decimal

	    var keycharcheck = (

		    key == 8 ||

		    key == 9 ||

		    key == 13 ||

		    key == 17 ||

		    key == 46 ||

		    key == 67 ||

		    key == 86 ||

		    key == 110 ||

		    key == 190 ||

		    (key >= 35 && key <= 40) ||

		    (key >= 48 && key <= 57) ||

		    (key >= 96 && key <= 105));



	    if (!keycharcheck) {

		event.preventDefault();

		return false;

	    }

	    var keyval = event.which;

	    if (keyval != 8 && keyval != 46 && keyval != 17 && keyval != 37 && keyval != 39 && keyval != 190 && keyval != 110 && keyval != 9) {

		var val = this.value;

		var totalDecimal = 0;

		var ispointval = false;

		for(var i=0;i<val.length;i++){

		    if(ispointval){

			totalDecimal++;

		    }

		    if(this.value.charAt(i) == "."){

			ispointval = true;

		    }

		}



		if(totalDecimal >= decimal_point_allowed){

		    event.preventDefault();

		    return false;

		}

	    }

	    /*var keyval = event.which;

	     if(keyval!=8 && keyval!=46 && keyval!=17 && keyval!=37 && keyval!=39 && keyval!=190 && keyval!=110 && keyval!=9){

	     var val = this.value;

	     var pointval = false;

	     var pointcount = 0;

	     var charcount = 0;

	     var newVal = '';

	     var ispointval = this.value.indexOf(".");



	     for (var i = 0;i<val.length;i++) {

	     if(pointval){

	     pointcount++;

	     }



	     charcount++;



	     if(val[i] == '.'){

	     pointval = true;

	     }



	     if(i==5 && ispointval=='-1'){

	     newVal += val[i]+'.';

	     }else{

	     newVal += val[i];

	     }

	     }

	     if(pointval){

	     if(pointcount>3){

	     event.preventDefault();

	     }

	     }else{

	     if(charcount>5){

	     event.preventDefault();

	     }

	     }

	     this.value = newVal;

	     }*/

	});



	// Allow formate like 12.12 only

	$('#saveform').on('keydown', '.pricecheck', function (event) {

	    var key = event.charCode || event.keyCode || 0;

	    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY

	    // home, end, period, and numpad decimal

	    var keycharcheck = (

		    key == 8 ||

		    key == 9 ||

		    key == 13 ||

		    key == 46 ||

		    key == 110 ||

		    key == 190 ||

		    key == 17 ||

		    key == 67 ||

		    key == 86 ||

		    (key >= 35 && key <= 40) ||

		    (key >= 48 && key <= 57) ||

		    (key >= 96 && key <= 105));



	    if (!keycharcheck) {

		event.preventDefault();

		return false;

	    }



	    var keyval = event.which;

	    if (keyval != 8 && keyval != 46 && keyval != 17 && keyval != 37 && keyval != 39 && keyval != 190 && keyval != 110 && keyval != 9) {

		var val = this.value;

		var totalDecimal = 0;

		var ispointval = false;

		for(var i=0;i<val.length;i++){

		    if(ispointval){

			totalDecimal++;

		    }

		    if(this.value.charAt(i) == "."){

			ispointval = true;

		    }

		}



		if(totalDecimal >= decimal_point_allowed){

		    event.preventDefault();

		    return false;

		}

	    }

	    /*

	    var keyval = event.which;

	    if (keyval != 8 && keyval != 46 && keyval != 17 && keyval != 37 && keyval != 39 && keyval != 190 && keyval != 110 && keyval != 9) {

		var val = this.value;

		var pointval = false;

		var pointcount = 0;

		var charcount = 0;

		var newVal = '';

		var ispointval = this.value.indexOf(".");

		for (var i = 0; i < val.length; i++) {

		    if (pointval) {

			pointcount++;

		    }



		    charcount++;



		    if (val[i] == '.') {

			pointval = true;

		    }



		    if (i == 1 && ispointval == '-1') {

			newVal += val[i] + '.';

		    } else {

			newVal += val[i];

		    }

		}



		if (pointval) {

		    if (pointcount > 1) {

			event.preventDefault();

		    }

		} else {

		    if (charcount > 1) {

			event.preventDefault();

		    }

		}

		this.value = newVal;

	    }

	    */

	});



	$.validator.setDefaults({

	    ignore: []

	});



	$.validator.addClassRules('floatcheck', {

	    number: true

	});



	$.validator.addClassRules('pricecheck', {

	    number: true

	});



	$("#saveform").validate({

	    invalidHandler: function (form, validator) {

		var errors = validator.numberOfInvalids();

		if (errors) {



		    validator.validElements().each(function () {

			var tabid = $(this).closest('.resp-tab-content').attr('data-tab-id');

			$('.tab-custom-id-' + tabid).removeClass('error-tab');

		    });



		    validator.invalidElements().each(function () {

			var tabid = $(this).closest('.resp-tab-content').attr('data-tab-id');

			$('.tab-custom-id-' + tabid).addClass('error-tab');

		    });

		}

	    }

	});

    });

</script>