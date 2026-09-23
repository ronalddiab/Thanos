<?php
// Progress on Target - variables pre-calculated in controller
$baseline_year = $site_detail['baseline_regression_year'];
$ProgressTargetPercentage = isset($ProgressTargetPercentage) ? $ProgressTargetPercentage : [];
$progressTarget = isset($progressTargetData) ? $progressTargetData : [];
$progress_roomnight_YTD = isset($progress_roomnight_YTD) ? $progress_roomnight_YTD : 0;
$progress_last_roomnight_YTD = isset($progress_last_roomnight_YTD) ? $progress_last_roomnight_YTD : 0;
$progress_baseline_roomnight_YTD = isset($progress_baseline_roomnight_YTD) ? $progress_baseline_roomnight_YTD : 0;
$progress_guestnight_YTD = isset($progress_guestnight_YTD) ? $progress_guestnight_YTD : 0;
$progress_last_guestnight_YTD = isset($progress_last_guestnight_YTD) ? $progress_last_guestnight_YTD : 0;
$progress_baseline_guestnight_YTD = isset($progress_baseline_guestnight_YTD) ? $progress_baseline_guestnight_YTD : 0;
$landfill_waste_YTD = isset($landfill_waste_YTD) ? $landfill_waste_YTD : 0;
$landfill_waste_Baseline_YTD = isset($landfill_waste_Baseline_YTD) ? $landfill_waste_Baseline_YTD : 0;
?>
<style>
    table {
        text-align: center !important;
        vertical-align: center !important;
    }
</style>
<table style="vertical-align: central;padding: 10px;" width="100%" cellpadding="2" cellspacing="2">
	    <thead width="100%">
		<tr style="font-size:12px;color:blue;" align="center">
			<td colspan="7"><strong>Progress on Reduction Targets</strong></td>
		</tr>
		<tr style="height:30px;">
            <th width="14.2%"></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> YTD</b></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> Base year <br>YTD</b></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> YTD Intensity</b></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> Target YTD Intensity</b></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> Reduction<br/>Target %</b></th>
            <th width="14.2%" style="text-align: center; font-size: 9px;"><b> Performance YTD</b></th>
        </tr>
	    </thead>
	    <tbody>
			<?php foreach ($ProgressTargetPercentage as $key => $value) {
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
					$actualIntensity = !empty($progress_guestnight_YTD) ? ((float)$value['ACTUAL_YTD'] / (float)$progress_guestnight_YTD) : 0;
					$targetIntensity = !empty($progress_baseline_guestnight_YTD) ? ((float)$targetYtdVal / (float)$progress_baseline_guestnight_YTD) : 0;
					$unitRNText = '/GN';
				} else if ($key == 'Waste') {
					$wasteTargetTotal = $value['TOTAL_WASTE_TARGET_YTD']
						?? ((!empty($value['site_saving_target']))
							? ($value['TOTAL_WASTE_BASELINE_YTD'] * (1 - ((float) $value['site_saving_target'] / 100)))
							: $value['TOTAL_WASTE_BASELINE_YTD']);
					$actualIntensity = !empty($progress_roomnight_YTD) ? ((float)$value['TOTAL_WASTE_YTD'] / (float)$progress_roomnight_YTD) : 0;
					$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ((float)$wasteTargetTotal / (float)$progress_baseline_roomnight_YTD) : 0;
				} else {
					$actualIntensity = !empty($progress_roomnight_YTD) ? ((float)$value['ACTUAL_YTD'] / (float)$progress_roomnight_YTD) : 0;
					$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ((float)$targetYtdVal / (float)$progress_baseline_roomnight_YTD) : 0;
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
			<tr style="height:20px;">
				<td><b><?php echo $key; ?></b></td>
						<?php if($key == 'Waste') { ?>
							<td width="14.2%" style="text-align: center;">
								<?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' %'; ?>
							</td>
							<td width="14.2%" style="text-align: center;">
								<?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' %'; ?>
							</td>
						<?php } else { ?>
							<td width="14.2%" style="text-align: center;"><?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' '.$unitText; ?></td>
							<td width="14.2%" style="text-align: center;"><?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' '.$unitText; ?></td>
						<?php } ?>
				<td width="14.2%" style="text-align: center;"><?php echo $baseRoomnightValue.$unitText . $unitRNText; ?></td>
				<td width="14.2%" style="text-align: center;"><?php echo $baseRoomnightBaselineValue.$unitText . $unitRNText; ?></td>
				<td width="14.2%" style="text-align: center;"><?php echo isset($value['site_saving_target']) ? (($key != 'Waste') ? '-' : '') . $value['site_saving_target'] . '%' : '-'; ?></td>
				<td width="14.2%" style="text-align: center;"><?php echo number_format($value['YTD_Variance'], 2); ?> % <img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image; ?>" style="width: 10px;height: 10px;" /></td>

			</tr>
			<?php } ?>
		</tbody>
	</table>
