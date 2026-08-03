<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');



$montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');



echo add_js(array('easyResponsiveTabs', 'MonthPicker.min'));

echo add_css(array('MonthPicker.min'));



$optioncurrencyvalue = array('currency' => true);

$optionintvalue = array();

?>

<div id="ajax_table" class="utilities-detail-wrap">

    <article class="card">

	<div class="article-header"><?php echo lang('10-days-management-report'); ?></div>

	<div class="data-info-block-outer">

	    <div class="row">

		<div class="col-sm-12">

		    <div class="col-sm-12">

			<form name="management-report" id="management-report" method="post">

			    <div style="float: left;margin-right: 19px; margin-top: 2px;">

				<label><?php echo lang('usage-date'); ?></label>

				<div class="data-info-block">

				    <input type="text" id="MonthFormat" class='Default' value="<?php echo (!empty($month) && !empty($year)) ? $month . '/' . $year : ''; ?>">

				</div>

			    </div>

			    <div class="col-sm-2 gen-report">

				<input  id="genrate-report" type="submit" value="Generate Report">

			    </div>

			    <div class="col-sm-2 gen-report">

				<input id="genrate-excel" type="button" value="<?php echo lang('generate-excel'); ?>">

			    </div>

			    <div class="form-group col-md-2 col-sm-3 col-xs-12">

				<div class="form-dropdown">

				    <select name="utility_select" id="utility_select"  data-type="custom-dropdown">

					<option value="mtd" <?php echo ($utility_select == 'mtd') ? 'selected' : '' ?>><?php echo lang('moth_to_data'); ?></option>

					<?php if ($site_detail['show_utility_electricity']) { ?>

					    <option value="electricity" <?php echo ($utility_select == 'electricity') ? 'selected=' : ''; ?>><?php echo lang('electricity'); ?></option>

					<?php }if ($site_detail['show_utility_fuel_oil']) { ?>

					    <option value="fuel_oil" <?php echo ($utility_select == 'fuel_oil') ? 'selected' : '' ?>><?php echo lang('fuel_oil'); ?></option>

					<?php }if ($site_detail['show_utility_lpg']) { ?>

					    <option value="lpg" <?php echo ($utility_select == 'lpg') ? 'selected' : '' ?>><?php echo lang('lpg'); ?></option>

					<?php }if ($site_detail['show_utility_water']) { ?>

					    <option value="water" <?php echo ($utility_select == 'water') ? 'selected' : '' ?>><?php echo lang('water'); ?></option>

					<?php }if ($site_detail['show_utility_irrigation_water']) { ?>

					    <option value="water_irrigation" <?php echo ($utility_select == 'water_irrigation') ? 'selected' : '' ?>><?php echo lang('irrigation_water'); ?></option>

					<?php }if ($site_detail['show_utility_water_waste']) { ?>

					    <option value="water_waste" <?php echo ($utility_select == 'water_waste') ? 'selected' : '' ?>><?php echo lang('waste_water'); ?></option>

					<?php }if ($site_detail['show_utility_natural_gas']) { ?>

					    <option value="natural_gas" <?php echo ($utility_select == 'natural_gas') ? 'selected' : '' ?>><?php echo lang('natural_gas'); ?></option>

					<?php }if ($site_detail['show_utility_district_cooling']) { ?>

					    <option value="cooling" <?php echo ($utility_select == 'cooling') ? 'selected' : '' ?>><?php echo lang('cooling'); ?></option>

					<?php }if ($site_detail['show_utility_district_heating']) { ?>

					    <option value="heating" <?php echo ($utility_select == 'heating') ? 'selected' : '' ?>><?php echo lang('heating'); ?></option>

					<?php } ?>

				    </select>

				</div>

			    </div>

			    <div class="col-sm-2 gen-report">

				<input id="genrate-chart" type="button" value="<?php echo lang('graphical-view'); ?>">

			    </div>

			    <input type="hidden" id="view_type" name="view_type" value="" />

			    <input type="hidden" id="month" name="month" value="<?php echo $month; ?>" />

			    <input type="hidden" id="year" name="year" value="<?php echo $year; ?>" />

			</form>

			<div class="col-sm-2 gen-report" id="export">

			    <form id="exportForm" action="<?php echo base_url() . BASE_ADMIN_URL_CUSTOM; ?>reports/export" method="POST">

				<input type="hidden" name="month" value="" id="exportMonth"/>

				<input type="hidden" name="year" value="" id="exportYear"/>
				<!-- <?php //if($_SESSION['admin']['user_id'] != 1) { ?> -->
				<?php $export_reports_management_permission = check_user_permission_by_label('admin.reports.export'); ?>
						<?php if(!$export_reports_management_permission && $_SESSION['admin']['user_id'] != 1){ ?>
				<button type="submit" name="submit" value="<?php echo lang('utilities-daily-export'); ?>" id="export-button" class="btn btn-custom btn-yellow" style="pointer-events: none; cursor: not-allowed;" disabled="disabled"><?php echo lang('utilities-daily-export'); ?></button>
				<?php } else {?>
			       <input type="submit" name="submit" value="<?php echo lang('utilities-daily-export'); ?>" id="export-button" class="btn btn-custom btn-yellow"/>
			       <?php } ?>
			    </form>

			</div>

		    </div>

		</div>

	    </div>

	</div>

	<div class="row">

	    <div class="col-sm-1"></div>

	    <div class="col-sm-8">

		<div class="card-wrap table-responsive">

		    <table class="table table-bordered table-font-18 padding-8-5">

			<thead>

			    <tr>

				<th class="table-border-right table-border-bottom" style="width: 285px;border: 1px solid #fff;">&nbsp;</th>

				<th style="text-align:center;background:#666;color:#FFF;padding: 0px;"><b><?php echo $montharray[$month] . ' - ' . $year; ?></b></th>

				<th style="text-align:center;background:#666;color:#FFF;padding: 0px;"><b><?php echo $montharray[$month] . ' - ' . $last_year; ?></b></th>

				<th class="table-border-right" style="text-align:center;background:#666;color:#FFF;padding: 0px;"><b>BUDGET</b></th>

				<th colspan="2" style="text-align:center;background:#666;color:#FFF;padding: 0px;"><b>Difference v/s Last Year </b></th>

				<th class="table-border-right table-border-left" colspan="2" style="text-align:center;background:#666;color:#FFF;padding: 0px;"><b>Difference v/s Budget </b></th>

			    </tr>

			</thead>

			<tbody>

			    <tr>

				<th class="table-border-right table-border-left table-border-bottom table-border-top-thick" scope="row"><b>MONTH TO DATE </b></th>

				<td class="table-border-right table-border-bottom table-border-top-thick" colspan="3"><b># Days - <?php echo $to_date; ?></b></td>

				<td class="table-border-bottom table-border-top-thick"><b>Value</b></td>

				<td class="table-border-right table-border-bottom table-border-top-thick"><b>%</b></td>

				<td class="table-border-bottom table-border-top-thick"><b>Value</b></td>

				<td class="table-border-right table-border-bottom table-border-top-thick"><b>%</b></td>

			    </tr>

			    <tr>

				<?php

				$last_year_deference = 0;

				$last_year_percantage = 0;



				$last_year_deference = $current_year['total_room_night'] - $previous_year['total_room_night'];

				$last_year_percantage = ($previous_year['total_room_night'] != 0) ? (($last_year_deference * 100) / $previous_year['total_room_night']) : 0;

				?>

				<th class="table-border-right table-border-left" scope="row">Room Nights</th>

				<td><?php echo number_format($current_year['total_room_night']); ?></td>

				<td><?php echo number_format($previous_year['total_room_night']); ?></td>

				<td class="table-border-right">&nbsp;</td>

				<td><?php echo number_format($last_year_deference); ?></td>

				<td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

				<td class="table-border-right" colspan="2">&nbsp;</td>

			    </tr>

			    <tr>

				<?php

				$last_year_deference = 0;

				$last_year_percantage = 0;



				$last_year_deference = $current_year['cdd'] - $previous_year['cdd'];

				$last_year_percantage = ($previous_year['cdd'] != 0) ? (($last_year_deference * 100) / $previous_year['cdd']) : 0;

				?>

				<th class="table-border-right table-border-left" scope="row">CDD</th>

				<td><?php echo round($current_year['cdd'], 2); ?></td>

				<td><?php echo round($previous_year['cdd'],2); ?></td>

				<td class="table-border-right">&nbsp;</td>

				<td><?php echo number_format($last_year_deference); ?></td>

				<td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

				<td class="table-border-right" colspan="2">&nbsp;</td>

			    </tr>

			    <tr>

				<?php

				$last_year_deference = 0;

				$last_year_percantage = 0;



				$last_year_deference = $current_year['hdd'] - $previous_year['hdd'];

				$last_year_percantage = ($previous_year['hdd'] != 0) ? (($last_year_deference * 100) / $previous_year['hdd']) : 0;

				?>

				<th class="table-border-right table-border-left" scope="row">HDD</th>

				<td><?php echo round($current_year['hdd'],2); ?></td>

				<td><?php echo round($previous_year['hdd'],2); ?></td>

				<td class="table-border-right">&nbsp;</td>

				<td><?php echo number_format($last_year_deference); ?></td>

				<td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

				<td class="table-border-right" colspan="2">&nbsp;</td>

			    </tr>

			    <?php foreach ($utility_key_array as $utility) { ?>

				<?php

				// Merge landscape and waste water in water consumption

				if(in_array($utility['db_key'], array('landscape_water_consumption', 'waste_water_consumption'))){

				    continue;

				}

				?>

				<tr>

				    <th class="table-border-right table-border-left" scope="row" style="text-align:center;background:#666;color:#FFF;"><?php echo $utility['title'] ?></th>

				    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td class="table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td class="table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				    <td class="table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				</tr>

				<tr>

				    <?php /*                                     * ************ First row (Consumpition)************* */ ?>

				    <?php

				    $last_year_deference = 0;

				    $last_year_percantage = 0;



				    $last_year_deference = $current_year['total_' . $utility['db_key']] - $previous_year['total_' . $utility['db_key']];

				    $last_year_percantage = ($current_year['total_' . $utility['db_key']] != 0) ? (floatval((($last_year_deference * 100) / $current_year['total_' . $utility['db_key']]))) : 0;

				    ?>

				    <th class="table-border-right table-border-left" scope="row"><?php echo $utility['title'] ?> Consumption (<?php echo $utility['unit']; ?>)</th>

				    <td><?php echo number_format($current_year['total_' . $utility['db_key']]); ?></td>

				    <td><?php echo number_format($previous_year['total_' . $utility['db_key']]); ?></td>

				    <td class="table-border-right"><?php echo number_format($current_year[$utility['budget_key']]); ?></td>

				    <td><?php echo number_format($last_year_deference); ?></td>

				    <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>



				    <?php // Budget comparision ?>

				    <?php if ($utility['budget_key'] != '') { ?>

					<?php

					$budget_deference = 0;

					$budget_percantage = 0;



					$budget_deference = $current_year['total_' . $utility['db_key']] - $current_year[$utility['budget_key']];

					$budget_percantage = ($current_year['total_' . $utility['db_key']] != 0) ? floatval((($budget_deference * 100) / $current_year['total_' . $utility['db_key']])) : 0;

					?>

					<td><?php echo number_format($budget_deference); ?></td>

					<td class="table-border-right"><?php echo number_format($budget_percantage); ?></td>

				    <?php } else { ?>

					<td class="table-border-right" colspan="2" rowspan="4">&nbsp;</td>

				    <?php } ?>

				</tr>



				<?php if($utility['db_key'] == 'water_consumption'){ ?>

				    <?php if($site_detail['show_utility_irrigation_water']){ ?>

					<tr>

					    <?php /*                                     * ************ Custom row (Consumpition)************* */ ?>

					    <?php

					    $last_year_deference = 0;

					    $last_year_percantage = 0;



					    $last_year_deference = $current_year['total_' . 'landscape_water_consumption'] - $previous_year['total_' . 'landscape_water_consumption'];

					    $last_year_percantage = ($current_year['total_' . 'landscape_water_consumption'] != 0) ? floatval((($last_year_deference * 100) / $current_year['total_' . 'landscape_water_consumption'])) : 0;

					    ?>

					    <th class="table-border-right table-border-left" scope="row"><?php echo 'Irrigation Water'; ?> Consumption (<?php echo $utility['unit']; ?>)</th>

					    <td><?php echo number_format($current_year['total_' . 'landscape_water_consumption']); ?></td>

					    <td><?php echo number_format($previous_year['total_' . 'landscape_water_consumption']); ?></td>

					    <td class="table-border-right">&nbsp;</td>

					    <td><?php echo number_format($last_year_deference); ?></td>

					    <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

					    <td>&nbsp;</td>

					    <td class="table-border-right">&nbsp;</td>

					</tr>

				    <?php } ?>



				    <?php if($site_detail['show_utility_water_waste']){ ?>

					<tr>

					    <?php /*                                     * ************ Custom row (Consumpition)************* */ ?>

					    <?php

					    $last_year_deference = 0;

					    $last_year_percantage = 0;



					    $last_year_deference = $current_year['total_' . 'waste_water_consumption'] - $previous_year['total_' . 'waste_water_consumption'];

					    $last_year_percantage = ($current_year['total_' . 'waste_water_consumption'] != '') ? floatval((($last_year_deference * 100) / $current_year['total_' . 'waste_water_consumption'])) : 0;

					    ?>

					    <th class="table-border-right table-border-left" scope="row"><?php echo 'Waste Water'; ?> Consumption (<?php echo $utility['unit']; ?>)</th>

					    <td><?php echo number_format($current_year['total_' . 'waste_water_consumption']); ?></td>

					    <td><?php echo number_format($previous_year['total_' . 'waste_water_consumption']); ?></td>

					    <td class="table-border-right">&nbsp;</td>

					    <td><?php echo number_format($last_year_deference); ?></td>

					    <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>



					    <td>&nbsp;</td>

					    <td class="table-border-right">&nbsp;</td>

					</tr>

				    <?php } ?>

				<?php } ?>



				<tr>

				    <?php /*                                     * ************ Second row (Cost)************* */ ?>

				    <?php

				    $last_year_deference = 0;

				    $last_year_percantage = 0;



				    $last_year_deference = $current_year['total_' . $utility['db_key'] . '_cost'] - $previous_year['total_' . $utility['db_key'] . '_cost'];

				    $last_year_percantage = ($current_year['total_' . $utility['db_key'] . '_cost'] != 0) ? floatval((($last_year_deference * 100) / $current_year['total_' . $utility['db_key'] . '_cost'])) : 0;

				    ?>

				    <th class="table-border-right table-border-left" scope="row">Total <?php echo $utility['title'] ?> Cost</th>

				    <td><?php echo report_value_format($current_year['total_' . $utility['db_key'] . '_cost'], $optioncurrencyvalue); ?></td>

				    <td><?php echo report_value_format($previous_year['total_' . $utility['db_key'] . '_cost'], $optioncurrencyvalue); ?></td>

				    <td class="table-border-right"><?php echo CURRENCY_SYMBOL . number_format($current_year[$utility['budget_key'] . '_cost']); ?></td>

				    <td><?php echo report_value_format($last_year_deference, $optioncurrencyvalue); ?></td>

				    <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

				    <?php if ($utility['budget_key'] != '') { ?>

					<?php

					$budget_deference = 0;

					$budget_percantage = 0;



					$budget_deference = $current_year['total_' . $utility['db_key'] . '_cost'] - $current_year[$utility['budget_key'] . '_cost'];

					$budget_percantage = ($current_year['total_' . $utility['db_key'] . '_cost'] != 0) ? floatval((($budget_deference * 100) / $current_year['total_' . $utility['db_key'] . '_cost'])) : 0;

					?>

					<td><?php echo report_value_format($budget_deference, $optioncurrencyvalue); ?></td>

					<td class="table-border-right"><?php echo number_format($budget_percantage); ?></td>

				    <?php } ?>

				</tr>

				<tr>

				    <?php /*                                     * ************ Third row (Consumption / roonnight)************* */ ?>

				    <?php

				    $current_per_room_night = 0;

				    $previous_per_room_night = 0;

				    $last_year_deference = 0;

				    $last_year_percantage = 0;



				    $current_per_room_night = ($current_year['total_room_night'] != 0) ? $current_year['total_' . $utility['db_key']] / $current_year['total_room_night'] : 0;

				    $previous_per_room_night = ($previous_year['total_room_night'] != 0) ? $previous_year['total_' . $utility['db_key']] / $previous_year['total_room_night'] : 0;



				    $last_year_deference = $current_per_room_night - $previous_per_room_night;

				    $last_year_percantage = ($current_per_room_night != 0) ? floatval((($last_year_deference * 100) / $current_per_room_night)) : 0;

				    ?>

				    <th class="table-border-right table-border-left" scope="row"><?php echo $utility['title'] ?> (<?php echo $utility['unit']; ?>) / room night</th>

				    <td><?php echo number_format(($current_per_room_night),2); ?></td>

				    <td><?php echo number_format(($previous_per_room_night),2); ?></td>

				    <td class="table-border-right" rowspan="2">&nbsp;</td>

				    <td><?php echo number_format($last_year_deference,2); ?></td>

				    <td class="table-border-right"><?php echo number_format($last_year_percantage,2); ?></td>

				    <?php if ($utility['budget_key'] != '') { ?>

					<td class="table-border-right" colspan="2" rowspan="2">&nbsp;</td>

				    <?php } ?>

				</tr>

				<tr>

				    <?php /*                                     * ************ Fourth row (cost / roonnight)************* */ ?>

				    <?php

				    $current_per_room_night = 0;

				    $previous_per_room_night = 0;

				    $last_year_deference = 0;

				    $last_year_percantage = 0;



				    $current_per_room_night = ($current_year['total_room_night'] != 0) ? $current_year['total_' . $utility['db_key'] . '_cost'] / $current_year['total_room_night'] : 0;

				    $previous_per_room_night = ($previous_year['total_room_night'] != 0) ? $previous_year['total_' . $utility['db_key'] . '_cost'] / $previous_year['total_room_night'] : 0;



				    $last_year_deference = $current_per_room_night - $previous_per_room_night;

				    $last_year_percantage = ($current_per_room_night != 0) ? floatval((($last_year_deference * 100) / $current_per_room_night)) : 0;

				    ?>



				    <th class="table-border-right table-border-left" scope="row"><?php echo $utility['title'] ?> Cost / room night</th>

				    <td><?php echo report_value_format($current_per_room_night, $optioncurrencyvalue); ?></td>

				    <td><?php echo report_value_format($previous_per_room_night, $optioncurrencyvalue); ?></td>

				    <td><?php echo report_value_format($last_year_deference, $optioncurrencyvalue); ?></td>

				    <td class="table-border-right"><?php echo number_format($last_year_percantage); ?></td>

				</tr>

			    <?php } ?>

			    <tr>

				<?php

				$last_year_deference = 0;

				$last_year_percantage = 0;



				$last_year_deference = $current_year['total_utility_cost'] - $previous_year['total_utility_cost'];

				$last_year_percantage = ($current_year['total_utility_cost'] != 0) ? (($last_year_deference * 100) / $current_year['total_utility_cost']) : 0 ;



				$budget_deference = 0;

				$budget_percantage = 0;



				$budget_deference = $current_year['total_utility_cost'] - $current_year['total_budget_cost'];

				$budget_percantage = ( $current_year['total_utility_cost'] !=0 ) ? floatval((($budget_deference * 100) / $current_year['total_utility_cost'])) : 0;

				?>

				<th class="table-border-top table-border-right table-border-left" scope="row" style="text-align:center;background:#666;color:#FFF;"><b>TOTAL</b></th>

				<td class="table-border-top" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format($current_year['total_utility_cost'], $optioncurrencyvalue); ?></b></td>

				<td class="table-border-top" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format($previous_year['total_utility_cost'], $optioncurrencyvalue); ?></b></td>

				<td class="table-border-top table-border-right" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format($current_year['total_budget_cost'], $optioncurrencyvalue); ?></b></td>

				<td class="table-border-top" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format($last_year_deference, $optioncurrencyvalue) ?></b></td>

				<td class="table-border-top table-border-right" style="text-align:center;background:#666;color:#FFF;"><b><?php echo number_format($last_year_percantage) ?></b></td>

				<td class="table-border-top" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format($budget_deference, $optioncurrencyvalue); ?></b></td>

				<td class="table-border-top table-border-right" style="text-align:center;background:#666;color:#FFF;"><b><?php echo number_format($budget_percantage); ?></b></td>

			    </tr>

			    <tr>

				<?php

				$last_year_deference = 0;

				$last_year_percantage = 0;



				$last_year_deference = $current_year['total_utility_cost_per_roomnight'] - $previous_year['total_utility_cost_per_roomnight'];

				$last_year_percantage = ($current_year['total_utility_cost_per_roomnight'] != 0) ? (($last_year_deference * 100) / $current_year['total_utility_cost_per_roomnight']) : 0;

				?>

				<th class="table-border-right table-border-bottom table-border-left" scope="row" style="text-align:center;background:#666;color:#FFF;"><b>Total Cost Per room night</b></th>

				<td class="table-border-bottom" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format(floatval((string)$current_year['total_utility_cost_per_roomnight']), $optioncurrencyvalue); ?></b></td>

				<td class="table-border-bottom" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format(floatval((string)$previous_year['total_utility_cost_per_roomnight']), $optioncurrencyvalue); ?></b></td>

				<td class="table-border-bottom table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				<td class="table-border-bottom" style="text-align:center;background:#666;color:#FFF;"><b><?php echo report_value_format(floatval((string)$last_year_deference), $optioncurrencyvalue) ?></b></td>

				<td class="table-border-bottom table-border-right" style="text-align:center;background:#666;color:#FFF;"><b><?php echo number_format(floatval((string)$last_year_percantage)); ?></b></td>

				<td class="table-border-bottom" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

				<td class="table-border-bottom table-border-right" style="text-align:center;background:#666;color:#FFF;">&nbsp;</td>

			    </tr>

			</tbody>

		    </table>

		</div>

	    </div>

	    <div class="col-sm-3"></div>

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



    $(document).ready(function () {

	var cur_date = new Date();

	var cur_month = cur_date.getMonth() + 1;

	var cur_year = cur_date.getFullYear();



	if ($('#month').val() >= cur_month && $('#year').val() >= cur_year) {

	    $('#export').addClass('hidden');

	} else {

	    $('#export').removeClass('hidden');

	}



	$('#export-button').on('click', function () {

	    $("#exportMonth").val($("#month").val());

	    $("#exportYear").val($("#year").val());

	    $('#exportForm').submit();

	});



	$("#DatePicker_Button_DateFormat").click(function () {

	    $("#DatePicker_DateFormat").toggle("slow");

	});



	var monthPickerObj = $("#MonthFormat").MonthPicker({

	    'OnAfterChooseMonth': function (date) {

		var month = date.getMonth() + 1;

		var year = date.getFullYear();

		$('#month').val(month);

		$('#year').val(year);



		if (month >= cur_month && year >= cur_year) {

		    $('#export').addClass('hidden');

		} else {

		    $('#export').removeClass('hidden');

		}

	    }

	});



	$("#genrate-excel").click(function () {

	    $("#view_type").val('excel');

	    $("#management-report").submit();

	});



	$("#genrate-report").click(function () {

	    $("#view_type").val('');

	    $("#management-report").submit();

	});



	$("#genrate-chart").click(function () {

	    $("#view_type").val('chart');

	    $("#management-report").submit();

	});

    });

</script>
