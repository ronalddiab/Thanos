<style>
	table {
		text-align: center !important;
		vertical-align: center !important;
	}
</style>
<br>
<table width="100%" cellpadding="1" cellspacing="2"><?php
													if ($show_site_details == false) {
														?>
		<tr colspan="2" style="font-size:12px;color:blue;" align="center">
			<td><strong><?php echo $pdf_report_title; ?></strong></td>
		</tr><?php
				}
				?>
	<tr align="center">
		<td width="100%">
			<img src="<?php echo $columnChartImg; ?>" />
		</td>
	</tr>
	<tr>
		<td width="100%"><?php
							if (!empty($utility_cost_chart)) {

								?><table border="1" width="100%" cellpadding="2" cellspacing="0" style="font-size:6px;">
					<thead>
						<tr style="background-color:#d8e1f2;">
							<th width="20%"><strong><?php echo $table_title; ?></strong></th>
							<th width="30%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<thead>
										<tr>
											<th width="35%"><strong><?php echo $current_title ?></strong></th>
											<th width="35%"><strong><?php echo $previous_title; ?></strong></th>
											<th width="30%"><strong><?php echo $budget_title ?></strong></th>
										</tr>
									</thead>
								</table>
							</th>
							<th width="30%">
								<table width="100%" cellpadding="2" cellspacing="0">
									<thead>
										<tr align="center" style="background-color: #9fb6df;">
											<th colspan="3"><strong>Difference v/s Last Year</strong></th>
										</tr>
										<tr align="center">
											<th width="35%"><strong>Value</strong></th>
											<th width="35%"><strong>%</strong></th>
											<th width="30%"><strong>Cost @ Last Year Tariff</strong></th>
										</tr>
									</thead>
								</table>
							</th>
							<th width="20%">
								<table width="100%" cellpadding="2" cellspacing="0">
									<thead>
										<tr align="center" style="background-color: #9fb6df;">
											<th colspan="2"><strong>Difference v/s Budget</strong></th>
										</tr>
										<tr align="center">
											<th width="50%"><strong>Value</strong></th>
											<th width="50%"><strong>%</strong></th>
										</tr>
									</thead>
								</table>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="20%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<td>Room Nights</td>
										</tr>
										<tr>
											<td>CDD</td>
										</tr>
										<tr>
											<td>HDD</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="30%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<td width="35%"><?php echo number_format($total_sum_data_room_night); ?></td>
											<td width="35%"><?php echo number_format($total_sum_pre_data_room_night); ?></td>
											<td width="30%"></td>
										</tr>
										<tr>
											<td width="35%"><?php echo round($total_sum_data_cdd, 3); ?></td>
											<td width="35%"><?php echo number_format($total_sum_pre_data_cdd); ?></td>
											<td width="30%"></td>
										</tr>
										<tr>
											<td width="35%"><?php echo round($total_sum_data_hdd, 3); ?></td>
											<td width="35%"><?php echo round($total_sum_pre_data_hdd, 3); ?></td>
											<td width="30%"></td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="30%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<?php $room_night_difference = ($total_sum_data_room_night - $total_sum_pre_data_room_night); ?>
											<td width="35%" align="center" style="<?php echo $room_night_difference > 0 ? '' : $positive_number_style; ?>"><?php echo number_format($room_night_difference); ?></td>
											<td width="35%" align="center" style="<?php echo $room_night_difference > 0 ? '' : $positive_number_style; ?>"><?php echo $total_sum_data_room_night_variation; ?>%</td>
											<td width="30%" align="center"></td>
										</tr>
										<tr>
											<?php $cdd_difference = $total_sum_data_cdd - $total_sum_pre_data_cdd; ?>
											<td align="center" style="<?php echo $cdd_difference > 0 ? $positive_number_style : ''; ?>"><?php echo number_format($cdd_difference); ?></td>
											<td align="center" style="<?php echo $cdd_difference > 0 ? $positive_number_style : ''; ?>"><?php echo $total_sum_data_cdd_variation; ?>%</td>
											<td align="center"></td>
										</tr>
										<tr>
											<?php $hdd_difference = $total_sum_data_hdd - $total_sum_pre_data_hdd ?>
											<td align="center" style="<?php echo $hdd_difference > 0 ? $positive_number_style : ''; ?>"><?php echo number_format($hdd_difference); ?></td>
											<td align="center" style="<?php echo $hdd_difference > 0 ? $positive_number_style : ''; ?>"><?php echo $total_sum_data_hdd_variation; ?>%</td>
											<td align="center"></td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="20%">
							</td>
						</tr>
						<?php if ($site_detail['show_utility_electricity']) { ?>

							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>ELECTRICITY</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'electricity'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'electricity'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'electricity'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo ($total_sum_pre_data_electricity != 0) ? number_format($total_sum_data_electricity_kwh) : 0; ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_electricity_kwh); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["total_electricity_kwh_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_electricity_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_electricity_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($electricity_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_electricity, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_electricity, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["total_electricity_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_electricity_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_electricity_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($electricity_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $electricity_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($electricity_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($electricity_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($electricity_ly, 0, $isLocal); ?></td>
											</tr>
											<?php $electricity_tariff_cl_diff = $total_sum_data_electricity_tariff - $total_sum_pre_data_electricity_tariff; ?>
											<tr style="<?php echo $electricity_tariff_cl_diff > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($electricity_tariff_cl_diff, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_electricity_tariff_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<?php $electricity_cost_cl_diff = $total_sum_data_electricity - $total_sum_pre_data_electricity; ?>
											<tr style="<?php echo $electricity_cost_cl_diff > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($electricity_cost_cl_diff, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_electricity_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $electricity_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($electricity_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($electricity_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $electricity_actual_budget_diff = $currentBudgetActualData["total_electricity_kwh_actual"] - $currentBudgetActualData["total_electricity_kwh_budget"]; ?>
											<tr style="<?php echo $electricity_actual_budget_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format(floatval((string) $electricity_actual_budget_diff)); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(floatval((string) $electricity_actual_budget_diff * 100 / $currentBudgetActualData["total_electricity_kwh_actual"], $percentage_decimal)) . "%"; ?></td>
											</tr>
											<?php $electricity_actual_budget_tariff_diff = $electricity_tariff_actual - $electricity_tariff_budget; ?>
											<tr style="<?php echo $electricity_actual_budget_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $electricity_actual_budget_tariff_diff, $value_decimal), $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $electricity_tariff_variation, $percentage_decimal)) . "%"; ?></td>
											</tr>
											<?php $elecricity_act_bud_cost_diff = $currentBudgetActualData["total_electricity_cost_actual"] - $currentBudgetActualData["total_electricity_cost_budget"]; ?>
											<tr style="text-align: center;<?php echo $elecricity_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td><?php echo num_format_without_currency(floatval((string) $elecricity_act_bud_cost_diff), 0, $isLocal); ?></td>
												<td><?php echo number_format($electricity_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $electricity_act_bud_pr_rn_diff = $electricity_per_room_night_actual - $electricity_per_room_night_budget; ?>
											<tr style="text-align: center;<?php echo $electricity_act_bud_pr_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td><?php echo number_format($electricity_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td><?php echo number_format($electricity_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_fuel_oil']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>FUEL OIL</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'fuel_oil'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'fuel_oil'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'fuel_oil'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format($total_sum_data_fuel_consumption); ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_fuel_consumption); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["total_fuel_oil_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_fuel_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_fuel_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($fuel_oil_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_fuel, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_fuel, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["total_fuel_oil_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_fuel_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_fuel_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($fuel_oil_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $fuel_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($fuel_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($fuel_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($fuel_oil_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $fuel_difference > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($fuel_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($fuel_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<?php $fuel_cost_cl_diff = $total_sum_data_fuel - $total_sum_pre_data_fuel; ?>
											<tr style="<?php echo $fuel_cost_cl_diff > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($fuel_cost_cl_diff, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_fuel_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $fuel_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($fuel_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($fuel_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $fuel_act_bud_diff = $currentBudgetActualData["total_fuel_oil_actual"] - $currentBudgetActualData["total_fuel_oil_budget"]; ?>
											<tr style="<?php echo $fuel_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format(floatval((string) $fuel_act_bud_diff)); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(($currentBudgetActualData["total_fuel_oil_actual"] != 0) ? $fuel_act_bud_diff * 100 / $currentBudgetActualData["total_fuel_oil_actual"] : 0, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $fuel_act_bud_tariff_diff = $fuel_oil_tariff_actual - $fuel_oil_tariff_budget; ?>
											<tr style="<?php echo $fuel_act_bud_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $fuel_act_bud_tariff_diff), $value_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $fuel_oil_tariff_variation), $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $fuel_act_bud_cost_diff = $currentBudgetActualData["total_fuel_oil_cost_actual"] - $currentBudgetActualData["total_fuel_oil_cost_budget"]; ?>
											<tr style="<?php $fuel_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $fuel_act_bud_cost_diff), 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $fuel_oil_cost_variation), $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $fuel_act_bud_rn_diff = $fuel_oil_per_room_night_actual - $fuel_oil_per_room_night_budget; ?>
											<tr style="<?php echo $fuel_act_bud_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($fuel_act_bud_rn_diff, $percentage_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($fuel_oil_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_lpg']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>LPG</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'lpg'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'lpg'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'lpg'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format($total_sum_data_lpg_consumption); ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_lpg_consumption); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["total_lpg_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_lpg_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_lpg_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($lpg_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_lpg, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_lpg, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["total_lpg_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_lpg_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_lpg_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($lpg_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $lpg_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($lpg_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($lpg_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($lpg_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $lpg_difference > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($lpg_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($lpg_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<?php $lpg_cl_cost_diff = $total_sum_data_lpg - $total_sum_pre_data_lpg; ?>
											<tr style="<?php echo $lpg_cl_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($lpg_cl_cost_diff, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_lpg_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $lpg_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($lpg_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($lpg_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $lpg_act_bud_diff = $currentBudgetActualData["total_lpg_actual"] - $currentBudgetActualData["total_lpg_budget"]; ?>
											<tr style="<?php echo $lpg_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format($lpg_act_bud_diff); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(($currentBudgetActualData["total_lpg_actual"] != 0) ? $lpg_act_bud_diff * 100 / $currentBudgetActualData["total_lpg_actual"] : 0, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $lpg_act_bud_tarrif_diff = $lpg_tariff_actual - $lpg_tariff_budget; ?>
											<tr style="<?php echo $lpg_act_bud_tarrif_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $lpg_act_bud_tarrif_diff), $value_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $lpg_tariff_variation), $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $lpg_act_bud_cost_diff = $currentBudgetActualData["total_lpg_cost_actual"] - $currentBudgetActualData["total_lpg_cost_budget"]; ?>
											<tr style="<?php echo $lpg_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($lpg_act_bud_cost_diff, 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($lpg_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $lpg_act_bud_pr_rn_diff = $lpg_per_room_night_actual - $lpg_per_room_night_budget; ?>
											<tr style="<?php echo $lpg_act_bud_pr_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo number_format($lpg_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td style="text-align: center;"><?php echo number_format($lpg_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_natural_gas']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>NATURAL GAS</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'natural_gas'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'natural_gas'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'natural_gas'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format($total_sum_data_natural_gas_consumption); ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_natural_gas_consumption); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["total_natural_gas_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_data_natural_gas_tariff), $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_pre_data_natural_gas_tariff), $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency(floatval((string) $natural_gas_tariff_budget), $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_data_natural_gas), 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_pre_data_natural_gas), 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["total_natural_gas_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_natural_gas_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_natural_gas_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($natural_gas_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>

											<tr style="<?php echo $natural_gas_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($natural_gas_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($natural_gas_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($natural_gas_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $natural_gas_variation > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($natural_gas_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($natural_gas_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $total_sum_data_natural_gas_variation > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($total_sum_data_natural_gas - $total_sum_pre_data_natural_gas, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_natural_gas_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $natural_gas_per_room_night_difference_percent > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($natural_gas_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($natural_gas_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $natural_gas_act_bud_diff = $currentBudgetActualData["total_natural_gas_actual"] - $currentBudgetActualData["total_natural_gas_budget"];
													$diff = is_infinite($natural_gas_act_bud_diff * 100 / $currentBudgetActualData["total_natural_gas_actual"]) ? 0 : $natural_gas_act_bud_diff * 100 / $currentBudgetActualData["total_natural_gas_actual"];
													$diff = is_nan($diff) ? 0 : $diff;
													?>
											<tr style="<?php echo $natural_gas_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format($natural_gas_act_bud_diff); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format($diff, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $natural_gas_act_bud_tariff_diff = $natural_gas_tariff_actual - $natural_gas_tariff_budget; ?>
											<tr style="<?php echo $natural_gas_act_bud_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($natural_gas_act_bud_tariff_diff, $value_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($natural_gas_tariff_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $natural_gas_act_bud_cost_diff = $currentBudgetActualData["total_natural_gas_cost_actual"] - $currentBudgetActualData["total_natural_gas_cost_budget"] ?>
											<tr style="<?php echo $natural_gas_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($natural_gas_act_bud_cost_diff, 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($natural_gas_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $natural_gas_act_bud_pr_rn_diff = $natural_gas_per_room_night_actual - $natural_gas_per_room_night_budget; ?>
											<tr style="<?php echo $natural_gas_act_bud_pr_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo number_format($natural_gas_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td style="text-align: center;"><?php echo number_format($natural_gas_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_water']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>WATER</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'water'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'water'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'water'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format(floatval((string) $total_sum_data_water_consumption)); ?></td>
												<td width="35%"><?php echo number_format(floatval((string) $total_sum_pre_data_water_consumption)); ?></td>
												<td width="30%"><?php echo number_format(floatval((string) $currentBudgetActualData["water_total_consumption_budget"])); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_data_water_tariff), $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency(floatval((string) $total_sum_pre_data_water_tariff), $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($water_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_water, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_water, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["water_total_consumption_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_water_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_water_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($water_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $water_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($water_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($water_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($water_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $water_difference > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($water_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($water_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $total_sum_data_water_variation > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($total_sum_data_water - $total_sum_pre_data_water, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_water_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $water_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($water_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($water_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $water_act_bud_diff = $currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"]; ?>
											<tr style="<?php echo $water_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format($water_act_bud_diff); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(($currentBudgetActualData["water_total_consumption_actual"] != 0) ? ($currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"]) * 100 / $currentBudgetActualData["water_total_consumption_actual"] : 0, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $water_act_bud_tariff_diff = $water_tariff_actual - $water_tariff_budget; ?>
											<tr style="<?php echo $water_act_bud_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $water_act_bud_tariff_diff, $value_decimal), $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $water_tariff_variation), $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $water_act_bud_cost_diff = $currentBudgetActualData["water_total_consumption_cost_actual"] - $currentBudgetActualData["water_total_consumption_cost_budget"]; ?>
											<tr style="<?php echo $water_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($water_act_bud_cost_diff, 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($water_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $water_act_bud_pr_rn_diff = $water_per_room_night_actual - $water_per_room_night_budget; ?>
											<tr style="<?php echo $water_act_bud_pr_rn_diff ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo number_format($water_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td style="text-align: center;"><?php echo number_format($water_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_district_cooling']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>DISTRICT COOLING</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'district_cooling'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'district_cooling'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'district_cooling'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format($total_sum_data_cooling_district_consumption); ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_cooling_district_consumption); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["district_cooling_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_cooling_district_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_cooling_district_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($district_cooling_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_cooling_district, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_cooling_district, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["district_cooling_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_cooling_district_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_cooling_district_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($district_cooling_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $cooling_district_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo number_format($cooling_district_consumption_difference); ?></td>
												<td width="35%" align="center"><?php echo number_format($cooling_district_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($cooling_district_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $cooling_district_difference > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($cooling_district_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($cooling_district_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $total_sum_data_cooling_district_variation > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($total_sum_data_cooling_district - $total_sum_pre_data_cooling_district, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_cooling_district_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $cooling_district_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo number_format($cooling_district_per_room_night_difference_value, $percentage_decimal); ?></td>
												<td align="center"><?php echo number_format($cooling_district_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $district_cooling_act_bud_diff = $currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"]; ?>
											<tr style="<?php echo $district_cooling_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format($district_cooling_act_bud_diff); ?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(($currentBudgetActualData["district_cooling_actual"] != 0) ? ($currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"]) * 100 / $currentBudgetActualData["district_cooling_actual"] : 0, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $district_cooling_act_bud_tariff_diff = $district_cooling_tariff_actual - $district_cooling_tariff_budget; ?>
											<tr style="<?php echo $district_cooling_act_bud_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($district_cooling_act_bud_tariff_diff, $value_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format(floatval((string) $district_cooling_tariff_variation, $percentage_decimal)) . "%"; ?></td>
											</tr>
											<?php
													$district_cooling_act_bud_cost_diff = $total_sum_data_cooling_district - $currentBudgetActualData["district_cooling_cost_budget"];
													$district_cooling_act_bud_cost_variation = ($district_cooling_act_bud_cost_diff * 100) / $currentBudgetActualData["district_cooling_cost_budget"];

													/*$district_cooling_act_bud_cost_diff = $currentBudgetActualData["district_cooling_cost_actual"] - $currentBudgetActualData["district_cooling_cost_budget"];*/ ?>
											<tr style="<?php echo $district_cooling_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency(floatval((string) $district_cooling_act_bud_cost_diff), 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($district_cooling_act_bud_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $district_cooling_act_bud_pr_rn_diff = $district_cooling_per_room_night_actual - $district_cooling_per_room_night_budget; ?>
											<tr style="<?php echo $district_cooling_act_bud_pr_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo number_format($district_cooling_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td style="text-align: center;"><?php echo number_format($district_cooling_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php }
							if ($site_detail['show_utility_district_heating']) { ?>
							<tr style="background-color:#d8e1f2;">
								<td width="20%" colspan="4"><strong>DISTRICT HEATING</strong></td>
							</tr>
							<tr>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td>Consumption (<?php echo GetSiteUtilityUnitName($site_id, 'district_heating'); ?>)</td>
											</tr>
											<tr>
												<td>Average tariff (<?php echo currency_symbol($isLocal) ?> / <?php echo GetSiteUtilityUnitName($site_id, 'district_heating'); ?>)</td>
											</tr>
											<tr>
												<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
											</tr>
											<tr>
												<td>Consumption/Room Night (<?php echo GetSiteUtilityUnitName($site_id, 'district_heating'); ?>)</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr>
												<td width="35%"><?php echo number_format($total_sum_data_heating_district_consumption); ?></td>
												<td width="35%"><?php echo number_format($total_sum_pre_data_heating_district_consumption); ?></td>
												<td width="30%"><?php echo number_format($currentBudgetActualData["district_heating_budget"]); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_heating_district_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_heating_district_tariff, $value_decimal, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($district_heating_tariff_budget, $value_decimal, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo num_format_without_currency($total_sum_data_heating_district, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($total_sum_pre_data_heating_district, 0, $isLocal); ?></td>
												<td><?php echo num_format_without_currency($currentBudgetActualData["district_heating_cost_budget"], 0, $isLocal); ?></td>
											</tr>
											<tr>
												<td><?php echo number_format($total_sum_data_heating_district_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($total_sum_pre_data_heating_district_per_room_night, $percentage_decimal); ?></td>
												<td><?php echo number_format($district_heating_per_room_night_budget, $percentage_decimal); ?></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="30%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<tr style="<?php echo $heating_district_consumption_difference > 0 ? $positive_number_style : ''; ?>">
												<td width="35%" align="center"><?php echo num_format_without_currency($heating_district_consumption_difference, 0, $isLocal); ?></td>
												<td width="35%" align="center"><?php echo number_format($heating_district_consumption_variation, $percentage_decimal); ?>%</td>
												<td width="30%" align="center"><?php echo num_format_without_currency($heating_district_ly, 0, $isLocal); ?></td>
											</tr>
											<tr style="<?php echo $heating_district_difference > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($heating_district_difference, $value_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($heating_district_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $total_sum_data_heating_district_variation > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($total_sum_data_heating_district - $total_sum_pre_data_heating_district, 0, $isLocal); ?></td>
												<td align="center"><?php echo number_format($total_sum_data_heating_district_variation, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
											<tr style="<?php echo $heating_district_per_room_night_difference_value > 0 ? $positive_number_style : ''; ?>">
												<td align="center"><?php echo num_format_without_currency($heating_district_per_room_night_difference_value, $percentage_decimal, $isLocal); ?></td>
												<td align="center"><?php echo number_format($heating_district_per_room_night_difference_percent, $percentage_decimal); ?>%</td>
												<td align="center"></td>
											</tr>
										</tbody>
									</table>
								</td>
								<td width="20%">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tbody>
											<?php $district_heating_act_bud_diff = $currentBudgetActualData["district_heating_actual"] - $currentBudgetActualData["district_heating_budget"]; ?>
											<tr style="<?php echo $district_heating_act_bud_diff > 0 ? $positive_number_style : ''; ?>">
												<td width="50%" style="text-align: center;"><?php echo number_format($district_heating_act_bud_diff);
																									?></td>
												<td width="50%" style="text-align: center;"><?php echo number_format(($currentBudgetActualData["district_heating_actual"] - $currentBudgetActualData["district_heating_budget"]) * 100 / $currentBudgetActualData["district_heating_actual"], $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $district_heating_act_bud_tariff_diff = $district_heating_tariff_actual - $district_heating_tariff_budget; ?>
											<tr style="<?php echo $district_heating_act_bud_tariff_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($district_heating_act_bud_tariff_diff, $value_decimal, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($district_heating_tariff_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $district_heating_act_bud_cost_diff = $currentBudgetActualData["district_heating_cost_actual"] - $currentBudgetActualData["district_heating_cost_budget"]; ?>
											<tr style="<?php echo $district_heating_act_bud_cost_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo num_format_without_currency($district_heating_act_bud_cost_diff, 0, $isLocal); ?></td>
												<td style="text-align: center;"><?php echo number_format($district_heating_cost_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
											<?php $district_heating_act_bud_pr_rn_diff = $district_heating_per_room_night_actual - $district_heating_per_room_night_budget; ?>
											<tr style="<?php echo $district_heating_act_bud_pr_rn_diff > 0 ? $positive_number_style : ''; ?>">
												<td style="text-align: center;"><?php echo number_format($district_heating_act_bud_pr_rn_diff, $percentage_decimal); ?></td>
												<td style="text-align: center;"><?php echo number_format($district_heating_per_room_night_variation, $percentage_decimal) . "%"; ?></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<?php } ?>
						<tr style="background-color:#d8e1f2;color:blue;">
							<td width="20%" colspan="4"><strong>TOTAL</strong></td>
						</tr>
						<tr style="font-weight: bold;color:blue;">
							<td width="20%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody style="font-weight: bold;color:blue;">
										<tr>
											<td>Total Cost (<?php echo currency_symbol($isLocal) ?>)</td>
										</tr>
										<tr>
											<td>Total Cost / Room Night</td>
										</tr>
										<tr>
											<td>Total Cost / m<sup>2</sup> (<?php echo currency_symbol($isLocal) ?> / m<sup>2</sup>)</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="30%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody style="font-weight: bold;color:blue;">
										<tr>
											<td width="35%"><?php echo num_format_without_currency($total_sum_data_sum, 0, $isLocal); ?></td>
											<td width="35%"><?php echo num_format_without_currency($total_sum_pre_data_sum, 0, $isLocal); ?></td>
											<td width="30%"><?php echo num_format_without_currency($total_cost_budget, 0, $isLocal); ?></td>
										</tr>
										<tr>
											<td><?php echo num_format_without_currency($total_sum_data_utility_cost_per_roomnight, $percentage_decimal, $isLocal); ?></td>
											<td><?php echo num_format_without_currency(floatval((string) $total_sum_pre_data_utility_cost_per_roomnight), $percentage_decimal, $isLocal); ?></td>
											<td><?php //echo num_format_without_currency($total_cost_budget_per_room_night, 0, $isLocal);
													?></td>
										</tr>
										<tr>
											<td><?php echo num_format_without_currency($total_sum_data_utility_cost_per_m2, $percentage_decimal, $isLocal); ?></td>
											<td><?php echo num_format_without_currency($total_sum_pre_data_utility_cost_per_m2, $percentage_decimal, $isLocal); ?></td>
											<td><?php //echo num_format_without_currency($total_cost_budget_per_m2, $percentage_decimal, $isLocal);
													?></td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="30%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody style="font-weight: bold;color:blue;">
										<tr>
											<td width="35%" align="center" style="<?php echo $total_sum_difference_value > 0 ? $positive_number_style : ''; ?>"><?php echo num_format_without_currency($total_sum_difference_value, 0, $isLocal); ?></td>
											<td width="35%" align="center" style="<?php echo $total_sum_difference_value > 0 ? $positive_number_style : ''; ?>"><?php echo number_format($total_sum_difference_percent, $percentage_decimal); ?>%</td>
											<td width="30%" align="center" style="<?php echo $total_cost_ly > 0 ? $positive_number_style : ''; ?>"><?php echo num_format_without_currency($total_cost_ly, 0, $isLocal); ?></td>
										</tr>
										<tr style="<?php echo $data_utility_cost_per_roomnight_difference > 0 ? $positive_number_style : ''; ?>">
											<td align="center"><?php echo num_format_without_currency(floatval((string) $data_utility_cost_per_roomnight_difference), $percentage_decimal, $isLocal); ?></td>
											<td align="center"><?php echo number_format(floatval((string) $data_utility_cost_per_roomnight_variation), $percentage_decimal); ?>%</td>
											<td align="center"></td>
										</tr>
										<tr style="<?php echo $data_utility_cost_per_m2_difference > 0 ? $positive_number_style : ''; ?>">
											<td align="center"><?php echo num_format_without_currency($data_utility_cost_per_m2_difference, $percentage_decimal, $isLocal); ?></td>
											<td align="center"><?php echo number_format($data_utility_cost_per_m2_variation, $percentage_decimal); ?>%</td>
											<td align="center"></td>
										</tr>
									</tbody>
								</table>
							</td>
							<td width="20%">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody style="font-weight: bold;color:blue;">
										<?php
											$variation = $total_sum_data_sum - $total_cost_budget;
											$variation_percentage = ($variation * 100) / $total_cost_budget;
											?>
										<tr style="<?php echo $variation > 0 ? $positive_number_style : ''; ?>">
											<td width="50%" align="center"><?php echo num_format_without_currency($variation, 0, $isLocal); ?></td>
											<td width="50%" align="center"><?php echo number_format($variation_percentage, $percentage_decimal) ?>%</td>
										</tr>
										<tr>
											<td align="center"><?php //echo number_format($total_cost_per_room_night_variation);
																	?></td>
											<td align="center"><?php //echo $total_cost_per_room_night_variation_percentage . "%";
																	?></td>
										</tr>
										<tr>
											<td align="center"><?php //echo $total_cost_per_m2_variation;
																	?></td>
											<td align="center"><?php //echo $total_cost_per_m2_variation_percentage . "%";
																	?></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			<?php }
			?>
		</td>
	</tr>
	<?php if (isset($customNotifications) && !empty($customNotifications)) : ?>
		<tr>
			<td width="100%">
				<table border="1" width="100%" cellpadding="2" cellspacing="0" style="font-size:8px;">
					<thead>
						<tr>
							<th style="background-color:#d8e1f2;" align="center"><strong>Notes.</strong></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td width="100%">
								<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:8px;">
									<tbody>
										<?php if (!empty($customNotifications)) : ?>
											<?php foreach ($customNotifications as $key => $notification) : ?>
												<tr>
													<td width="10%">
														<?php echo $key + 1; ?>.
													</td>
													<td width="90%" style="text-align: left;">
														<?php echo $notification['notification'] ?>
													</td>
												</tr>
											<?php endforeach ?>
										<?php else : ?>
											<tr>
												<td width="100">
													No Notifications.
												</td>
											</tr>
										<?php endif ?>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	<?php endif ?>
</table>
