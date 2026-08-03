<?php
$notification_list = '';
extract($site);
$filters = array();
$filters['current_month'] = (int) date('m');
$filters['current_year'] = date('Y');
$baseline_year = $site['baseline_regression_year'];
$current_month = date('m');
$current_year  = date('Y');
$site_id = isset($site['site_id']) ? $site['site_id'] : [];
$ProgressTargetPercentage = isset($site['ProgressTargetPercentage']) ? $site['ProgressTargetPercentage'] : [];
$progress_roomnight_YTD = isset($site['progress_roomnight_YTD']) ? $site['progress_roomnight_YTD'] : [];
$progress_baseline_roomnight_YTD = isset($site['progress_baseline_roomnight_YTD']) ? $site['progress_baseline_roomnight_YTD'] : [];
$progress_guestnight_YTD = isset($site['progress_guestnight_YTD']) ? $site['progress_guestnight_YTD'] : [];
$progress_baseline_guestnight_YTD = isset($site['progress_baseline_guestnight_YTD']) ? $site['progress_baseline_guestnight_YTD'] : [];
$progressOnTarget = isset($site['progressOnTarget']) ? $site['progressOnTarget'] : [];
$progressOnTargetMonthly = isset($site['progressOnTargetMonthly']) ? $site['progressOnTargetMonthly'] : [];
$filters_notification['c_year'] = $filters_notification['cyear'];
$filters_notification['c_month'] = $filters_notification['cmonth'];
if ($filters_notification['cmonth'] == 1) {
	$filters_notification['premonth'] = date("m", strtotime("-2 month"));
	$filters_notification['c_year'] = date('Y') - 1;
} else {
	$filters_notification['premonth'] = $filters_notification['cmonth'] - 1;
}
?>
<div class="gradient" style="padding: 3px 0px;width: 535px;vertical-align: central; float: <?php echo ($temp % 2 == 0) ? 'right' : 'left'; ?>; height:max-content;">
	<table style="font-size: 12px; vertical-align: central;padding: 10px;" width="100%" height="250">
		<tr>
			<td width="15%" style="text-align: center;border-right: 1px solid black; font-weight: bold;height:100%;">
				<span style="margin-top: 20%;"><?php echo strtoupper($site_location_name); ?></span>
			</td>
			<td style="width: 200px;border-right: 1px solid black;">
				<table width="100%" style="vertical-align: central;">
					<tr>
						<th colspan="2" align="center" style="padding: 5px;"><?php echo !empty($site['report_comparison_month_label']) ? $site['report_comparison_month_label'] : date('F Y', strtotime('first day of last month')); ?> v/s <?php echo !empty($site['report_comparison_month_last_year_label']) ? $site['report_comparison_month_last_year_label'] : date('F Y', strtotime('first day of last month -1 year')); ?> Variations</th>
					</tr>
					<?php if($site['show_utility_electricity']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_electricity_raw'] > 0) {
								$electricitydifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_electricity_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_electricity_raw'];
								$electricitypercentage = $electricitydifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_electricity_raw'];
							} else {
								$electricitypercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_electricity_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_electricity_raw']) ? 0 : 100;
							}
							$electricitypercentage = round($electricitypercentage, 2);
							if ($electricitypercentage < 0) {
								$electricity_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$electricity_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$electricitypercentage = abs($electricitypercentage);

							echo "Electricity";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $electricitypercentage . "% ";
							if ($electricitypercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $electricity_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_fuel_oil']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_fuel_raw'] > 0) {
								$fueldifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_fuel_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_fuel_raw'];
								$fuelpercentage = $fueldifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_fuel_raw'];
							} else {
								$fuelpercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_fuel_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_fuel_raw']) ? 0 : 100;
							}
							$fuelpercentage = round($fuelpercentage, 2);
							if ($fuelpercentage < 0) {
								$fuel_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$fuel_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$fuelpercentage = abs($fuelpercentage);

							echo "Fuel Oil";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $fuelpercentage . "% ";
							if ($fuelpercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $fuel_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_lpg']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_lpg_raw'] > 0) {
								$lpgdifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_lpg_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_lpg_raw'];
								$lpgpercentage = $lpgdifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_lpg_raw'];
							} else {
								$lpgpercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_lpg_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_lpg_raw']) ? 0 : 100;
							}
							$lpgpercentage = round($lpgpercentage, 2);
							if ($lpgpercentage < 0) {
								$lpg_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$lpg_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$lpgpercentage = abs($lpgpercentage);

							echo "LPG";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $lpgpercentage . "% ";
							if ($lpgpercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $lpg_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_natural_gas']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_natural_gas_raw'] > 0) {
								$natural_gasdifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_natural_gas_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_natural_gas_raw'];
								$natural_gaspercentage = $natural_gasdifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_natural_gas_raw'];
							} else {
								$natural_gaspercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_natural_gas_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_natural_gas_raw']) ? 0 : 100;
							}
							$natural_gaspercentage = round($natural_gaspercentage, 2);
							if ($natural_gaspercentage < 0) {
								$natural_gas_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$natural_gas_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$natural_gaspercentage = abs($natural_gaspercentage);

							echo "Natural Gas";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $natural_gaspercentage . "% ";
							if ($natural_gaspercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $natural_gas_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_district_heating']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_heating_raw'] > 0) {
								$heatingdifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_heating_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_heating_raw'];
								$heatingpercentage = $heatingdifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_heating_raw'];
							} else {
								$heatingpercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_heating_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_heating_raw']) ? 0 : 100;
							}
							$heatingpercentage = round($heatingpercentage, 2);
							if ($heatingpercentage < 0) {
								$heating_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$heating_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$heatingpercentage = abs($heatingpercentage);

							echo "District Heating";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $heatingpercentage . "% ";
							if ($heatingpercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $heating_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_district_cooling']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_cooling_raw'] > 0) {
								$coolingdifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_cooling_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_cooling_raw'];
								$coolingpercentage = $coolingdifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_cooling_raw'];
							} else {
								$coolingpercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_cooling_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_cooling_raw']) ? 0 : 100;
							}
							$coolingpercentage = round($coolingpercentage, 2);
							if ($coolingpercentage < 0) {
								$cooling_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$cooling_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$coolingpercentage = abs($coolingpercentage);

							echo "District Cooling";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $coolingpercentage . "% ";
							if ($coolingpercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $cooling_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
					<?php if($site['show_utility_water']) { ?>
					<tr>
						<th width="50%" style="padding: 7px;">
							<?php
							if ($kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_water_raw'] > 0) {
								$waterdifference = $kpi['utilityForLastMonthCompare_unit_currentMonth_water_raw'] - $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_water_raw'];
								$waterpercentage = $waterdifference * 100 / $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_water_raw'];
							} else {
								$waterpercentage = ((float) $kpi['utilityForLastMonthCompare_unit_currentMonth_water_raw'] == (float) $kpi['utilityForLastMonthCompare_unit_sameMonthLastYear_water_raw']) ? 0 : 100;
							}
							$waterpercentage = round($waterpercentage, 2);
							if ($waterpercentage < 0) {
								$water_img = 'downArrow.png';
								$difference_status = 'decreased';
							} else {
								$water_img = 'upArrow.png';
								$difference_status = 'increased';
							}
							$waterpercentage = abs($waterpercentage);

							echo "Water";
							?>
						</th>
						<td width="50%" style="padding: 7px;">
							<?php
							echo " : " . $waterpercentage . "% ";
							if ($waterpercentage != 0) {
								echo '<img width="15px" height="15px" src="' . site_url() . '/themes/default/images/' . $water_img . '"/>';
							}
							?>
						</td>
					</tr>
					<?php } ?>
				</table>
			</td>
			<td style="width: 500px;border-right: 1px solid black;">
				<table style="vertical-align: central; width: 450px;">
					<tr>
						<th align="center" style="padding: 5px;">TOTAL UTILITIES</th>
					</tr>
					<tr>
						<td style="width: 350px; padding-left: 5px;">
							<?php
								$reportRefTs = !empty($site['report_reference_date']) ? strtotime($site['report_reference_date']) : time();
								$reportRefMonth = (int) date('n', $reportRefTs);
								$reportRefYear = (int) date('Y', $reportRefTs);
								$oneMonthAgoTs = mktime(0, 0, 0, $reportRefMonth - 1, 1, $reportRefYear);
								$twoMonthsAgoTs = mktime(0, 0, 0, $reportRefMonth - 2, 1, $reportRefYear);
								$oneMonthAgoLastYearTs = mktime(0, 0, 0, $reportRefMonth - 1, 1, $reportRefYear - 1);
								
								$oneMonthAgoMonth = (int)date('n', $oneMonthAgoTs);
								$oneMonthAgoYear = (int)date('Y', $oneMonthAgoTs);
								$twoMonthsAgoMonth = (int)date('n', $twoMonthsAgoTs);
								$twoMonthsAgoYear = (int)date('Y', $twoMonthsAgoTs);
								$oneMonthAgoLastYearMonth = (int)date('n', $oneMonthAgoLastYearTs);
								$oneMonthAgoLastYearYear = (int)date('Y', $oneMonthAgoLastYearTs);
							?>
							<table style="vertical-align: central; width: 350px;" border="1">
								<tr>
									<th style="width: 80px;text-align: center;">&nbsp;</th>
									<th style="padding: 5px;text-align: center; font-size: 14px; width: 80px;">EUI - kWh/RN</th>
									<th style="padding: 5px;text-align: right; font-size: 14px; width: 120px;">Cost</th>
									<th style="padding: 5px;text-align: center; font-size: 14px;width: 130px;">Cost v/s Budget</th>
								</tr>
								<tr>
									<th style="padding: 5px;"><?php echo date('F Y', $oneMonthAgoTs); ?></th>
									<td style="padding: 5px;text-align: center;"><?php echo (isset($progressOnTargetMonthly[$oneMonthAgoYear][$oneMonthAgoMonth]) && $progressOnTargetMonthly[$oneMonthAgoYear][$oneMonthAgoMonth]['room_night'] != 0) ? formatNumberAbbreviation($progressOnTargetMonthly[$oneMonthAgoYear][$oneMonthAgoMonth]['energy'] / $progressOnTargetMonthly[$oneMonthAgoYear][$oneMonthAgoMonth]['room_night']) : 0; ?></td>
									<td style="padding: 5px;text-align: right;"><span style="font-weight:600; margin-right:6px;"><?php echo BASE_CURRENCY_SYMBOL; ?></span><?php echo ' '.formatNumberAbbreviation(round($kpi['total_utility_cost_currentMonth'])); ?></td>
									<td style="padding: 5px; text-align: center;"><?php echo round(abs($kpi['variationPercentage'])); ?>%
										<?php $image = $kpi['variationPercentage'] < 0 ? 'upArrow.png' : 'downArrow.png'; if ($kpi['variationPercentage'] != 0) { ?>
										<img width="15" height="15" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image; ?>" />
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th style="padding: 5px;"><?php echo date('F Y', $twoMonthsAgoTs); ?></th>
									<td style="padding: 5px;text-align: center;"><?php echo (isset($progressOnTargetMonthly[$twoMonthsAgoYear][$twoMonthsAgoMonth]) && $progressOnTargetMonthly[$twoMonthsAgoYear][$twoMonthsAgoMonth]['room_night'] != 0) ? formatNumberAbbreviation($progressOnTargetMonthly[$twoMonthsAgoYear][$twoMonthsAgoMonth]['energy'] / $progressOnTargetMonthly[$twoMonthsAgoYear][$twoMonthsAgoMonth]['room_night']) : 0; ?></td>
									<td style="padding: 5px;text-align: right;"><span style="font-weight:600; margin-right:6px;"><?php echo BASE_CURRENCY_SYMBOL; ?></span><?php echo ' '.formatNumberAbbreviation(round($kpi['total_utility_cost_lastMonth'])); ?></td>
									<td style="padding: 3px;text-align: center;"><div style="font-size:14px;font-weight:bold;">Year To Date</div></td>
								</tr>
								<tr>
									<th style="padding: 5px;"><?php echo date('F Y', $oneMonthAgoLastYearTs); ?></th>
									<td style="padding: 5px;text-align: center;"><?php echo (isset($progressOnTargetMonthly[$oneMonthAgoLastYearYear][$oneMonthAgoLastYearMonth]) && $progressOnTargetMonthly[$oneMonthAgoLastYearYear][$oneMonthAgoLastYearMonth]['room_night'] != 0) ? formatNumberAbbreviation($progressOnTargetMonthly[$oneMonthAgoLastYearYear][$oneMonthAgoLastYearMonth]['energy'] / $progressOnTargetMonthly[$oneMonthAgoLastYearYear][$oneMonthAgoLastYearMonth]['room_night']) : 0; ?></td>
									<td style="padding: 5px;text-align: right;"><span style="font-weight:600; margin-right:6px;"><?php echo BASE_CURRENCY_SYMBOL; ?></span><?php echo ' '.formatNumberAbbreviation(round($kpi['total_utility_cost_sameMonth_lastYear'])); ?></td>
									<td style="padding: 5px;text-align: center;"><div style="margin-top:10px!important;text-align:center;"><?php echo round(abs($kpi['variationPercentage_ytd'])); ?>% <?php $image_ytd = $kpi['variationPercentage_ytd'] < 0 ? 'upArrow.png' : 'downArrow.png'; if ($kpi['variationPercentage_ytd'] != 0) { ?> <img width="15" height="15" src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image_ytd; ?>" /> <?php } ?></div></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr>
				<td style="border-right: 1px solid black;"></td>
				<td style="border-right: 1px solid black;">
					<hr>
					<table style="vertical-align: central; width: 350px;" border="1">
						<tr>
							<th style="font-size: 11px;padding: 7px;">CDD </th>
							<td style="font-size: 11px;padding: 7px;"><?php echo round(abs($kpi['cdd_consumption'])); ?>%
								<?php
								if (round(abs($kpi['cdd_consumption'])) == 0 || is_infinite($kpi['cdd_consumption'])) {
								} else {
									?>
									<img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $kpi['cdd_consumption_image']; ?>" style="width: 15px;height: 15px;">
									<?php } ?>
							</td>
						</tr>
						<tr>
							<th style="font-size: 11px;padding: 7px;">HDD </th>
							<td style="font-size: 11px;padding: 7px;"><?php echo is_infinite($kpi['hdd_consumption']) ? '&#8734;' : round(abs($kpi['hdd_consumption'])) . '%'; ?>
								<?php
								if (round(abs($kpi['hdd_consumption'])) == 0 || is_infinite($kpi['hdd_consumption'])) {
								} else {
									?>
									<img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $kpi['hdd_consumption_image']; ?>" style="width: 15px;height: 15px;">
									<?php } ?>
							</td>
						</tr>
						<tr>
							<th style="font-size: 11px;padding: 7px;">Room Nights </th>
							<td style="font-size: 11px;padding: 7px;"><?php echo is_infinite($kpi['total_room_night_consumption']) ? '<h3>&#8734;</h3>' : round(abs($kpi['total_room_night_consumption'])) . '%'; ?>
								<?php
								if (round(abs($kpi['total_room_night_consumption'])) == 0 || is_infinite($kpi['total_room_night_consumption'])) {
								} else {
									?>
									<img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $kpi['total_room_night_consumption_image']; ?>" style="width: 15px;height: 15px;">
									<?php } ?>
							</td>
						</tr>
					</table>
				</td>
				<td style="padding-left: 5px;">
					<table style="vertical-align: central; width: 100%;" border="1">
					<tr>
						<th colspan="7" style="text-align: center; font-size: 14px; background:#f7fbff; padding:8px;">Progress On Targets</th>
					</tr>
					<tr style="background:#fafafa;">
						<th style="padding:6px; width:14%;">Type</th>
						<th style="padding:6px; width:14%; text-align:center;">YTD</th>
						<th style="padding:6px; width:14%; text-align:center;">Base year YTD</th>
						<th style="padding:6px; width:14%; text-align:center;">YTD Intensity</th>
						<th style="padding:6px; width:14%; text-align:center;">Target YTD Intensity</th>
						<th style="padding:6px; width:14%; text-align:center;">Reduction Target %</th>
						<th style="padding:6px; width:14%; text-align:center;">Performance YTD</th>
					</tr>
					<?php 
					foreach ($ProgressTargetPercentage as $key => $value) {
						if($key == 'Energy') {
						$unitKey = 'electricity';
						$unitText = GetSiteUtilityUnitName($site_id, $unitKey);

						} else if($key == 'Water') {
						$unitKey = 'water';
						$unitText = GetSiteUtilityUnitName($site_id, $unitKey);
						} else if($key == 'Carbon'){
						$unitText = 'kgCO<sub>2</sub>';
						}  else if($key == 'Waste'){
						$unitText = 'kg';
						} else {
						$unitText = '';
						}
						$targetYtdVal = $value['TARGET_YTD'] ?? $value['TARGET_BASELINE_YTD'];
						$actualIntensity = 0;
						$targetIntensity = 0;
                        $unitRNText = '/RN';
						if ($key == 'Water' || $key == 'Carbon') {
                            $actualIntensity = !empty($progress_guestnight_YTD) ? ($value['ACTUAL_YTD'] / $progress_guestnight_YTD) : 0;
                            $targetIntensity = !empty($progress_baseline_guestnight_YTD) ? ($targetYtdVal / $progress_baseline_guestnight_YTD) : 0;
                            $unitRNText = '/GN';
						} else if ($key == 'Waste') {
							$wasteTargetTotal = $value['TOTAL_WASTE_TARGET_YTD']
								?? ((!empty($value['site_saving_target']))
									? ($value['TOTAL_WASTE_BASELINE_YTD'] * (1 - ((float) $value['site_saving_target'] / 100)))
									: $value['TOTAL_WASTE_BASELINE_YTD']);
							$actualIntensity = !empty($progress_roomnight_YTD) ? ($value['TOTAL_WASTE_YTD'] / $progress_roomnight_YTD) : 0;
							$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ($wasteTargetTotal / $progress_baseline_roomnight_YTD) : 0;
						} else {
							$actualIntensity = !empty($progress_roomnight_YTD) ? ($value['ACTUAL_YTD'] / $progress_roomnight_YTD) : 0;
							$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ($targetYtdVal / $progress_baseline_roomnight_YTD) : 0;
						}
						$baseRoomnightValue = number_format($actualIntensity, 2);
						$baseRoomnightBaselineValue = number_format($targetIntensity, 2);
						// Performance YTD = (actual - target) / target  (always divide by target)
						$value['YTD_Variance'] = ($targetIntensity != 0)
							? (($actualIntensity - $targetIntensity) / $targetIntensity) * 100
							: 0;
						$image = $value['YTD_Variance'] < 0 ? 'downArrow.png' : 'upArrow.png';
						$color = $value['YTD_Variance'] < 0 ? '#dc2727' : '#2ecc71';
					?>
					<tr>
						<td style="padding:6px;"><b><?php echo $key; ?></b></td>
						<?php if($key == 'Waste') { ?>
							<td style="padding:6px; text-align:center;">
								<?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' %'; ?>
							</td>
							<td style="padding:6px; text-align:center;">
								<?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' %'; ?>
							</td>
						<?php } else { ?>
							<td style="padding:6px; text-align:center;"><?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' '.$unitText; ?></td>
							<td style="padding:6px; text-align:center;"><?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' '.$unitText; ?></td>
						<?php } ?>
						<td style="padding:6px; text-align:center;"><?php echo $baseRoomnightValue.' '.$unitText . $unitRNText; ?></td>
						<td style="padding:6px; text-align:center;"><?php echo $baseRoomnightBaselineValue.' '.$unitText . $unitRNText; ?></td>
						<td style="padding:6px; text-align:center;"><?php echo isset($value['site_saving_target']) ? (($key != 'Waste') ? '-' : '') . $value['site_saving_target'] . '%' : '-'; ?></td>
						<td style="padding:6px; text-align:center;"><?php echo isset($value['YTD_Variance']) ? number_format($value['YTD_Variance'], 2).'%'.'<img src='.site_url() . '/themes/default/images/' . $image.' height="15" width="15"/>' : 0; ?></td>
					</tr>
					<?php } ?>
				</table>
			</td>
			</tr>
		</table>
</div>
