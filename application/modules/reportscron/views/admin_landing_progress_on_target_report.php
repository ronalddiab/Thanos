<!-- Progress Chart START-->
<?php
$current_month = date('m');
$current_year  = date('Y'); //2022
if ($current_month == 1) {
	$current_month = 12;
	$current_year  = date('Y') - 1;
}
$baseline_year = $site_detials['baseline_regression_year'];
?>
<style>
    table {
        text-align: center !important;
        vertical-align: center !important;
    }
</style>

<br><br><div style="border:2px solid  #f69546;padding:6px;">
<table style="vertical-align: central;padding: 10px;margin-top: 10px;" width="100%" cellpadding="2" cellspacing="2">
	    <thead width="100%">
		<tr style="font-size:12px;color:blue;" align="center">
			<td colspan="7"><strong>Progress on Reduction Targets</strong></td>
		</tr>
		<tr style="height:30px;">
            <th width="12%"></th>
            <th width="15%" style="text-align: center; font-size: 9px;"><b> YTD</b></th>
            <th width="15%" style="text-align: center; font-size: 9px;"><b> Base year <br>YTD</b></th>
            <th width="15%" style="text-align: center; font-size: 9px;"><b> YTD Intensity</b></th>
            <th width="15%" style="text-align: center; font-size: 9px;"><b> Target<br>YTD<br>Intensity</b></th>
            <th width="14%" style="text-align: center; font-size: 9px;"><b> Reduction<br/>Target %</b></th>
            <th width="14%" style="text-align: center; font-size: 9px;"><b> Performance YTD</b></th>
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
		<tr style="height:20px;">
			<td><b><?php echo $key; ?></b></td>
			<?php if($key == 'Waste') { ?>
				<td>
								<?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' %'; ?>
				</td>
				<td>
								<?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' %'; ?>
				</td>
			<?php } else { ?>
							<td><?php echo formatNumberAbbreviation($value['ACTUAL_YTD'], 0).' '.$unitText; ?></td>
							<td><?php echo formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 0).' '.$unitText; ?></td>
			<?php } ?>
				<td><?php echo $baseRoomnightValue.' '.$unitText . $unitRNText; ?></td>
				<td><?php echo $baseRoomnightBaselineValue.' '.$unitText . $unitRNText; ?></td>
				<td><?php echo isset($value['site_saving_target']) ? (($key != 'Waste') ? '-' : '') . $value['site_saving_target'] . '%' : '-'; ?></td>
				<td><?php echo number_format($value['YTD_Variance'], 2); ?> % <img src="<?php echo site_url(); ?>/themes/default/images/<?php echo $image; ?>" style="width: 10px;height: 10px;" /></td>

		</tr>
			<?php } ?>
		</tbody>
</table>

</div>
