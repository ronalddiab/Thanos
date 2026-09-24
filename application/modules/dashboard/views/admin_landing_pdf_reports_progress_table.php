<?php
$ProgressTargetPercentage = isset($site['ProgressTargetPercentage']) ? $site['ProgressTargetPercentage'] : [];
$progress_roomnight_YTD = isset($site['progress_roomnight_YTD']) ? $site['progress_roomnight_YTD'] : 0;
$progress_baseline_roomnight_YTD = isset($site['progress_baseline_roomnight_YTD']) ? $site['progress_baseline_roomnight_YTD'] : 0;
$progress_guestnight_YTD = isset($site['progress_guestnight_YTD']) ? $site['progress_guestnight_YTD'] : 0;
$progress_baseline_guestnight_YTD = isset($site['progress_baseline_guestnight_YTD']) ? $site['progress_baseline_guestnight_YTD'] : 0;
$progressOnTargetWasteYtd = isset($site['progressOnTargetWasteYtd']) ? $site['progressOnTargetWasteYtd'] : [];
?>
<style>
	table {
		text-align: center !important;
		vertical-align: center !important;
	}
	
    p.unit-class {
	    font-size: smaller!important;
	    display: inline!important;
	    color: inherit!important;
    }
	
    .unit-class {
	    font-size: 14px !important;
	    display: inline!important;
	    color: inherit!important;
    }
	table td {
		font-size: 14px;
	}
	table th {
		font-size: 14px;
	}

	td {
		padding-left: 2px;
    	padding-right: 2px;
	}
</style>
<table style="vertical-align: central;" width="100%" cellpadding="2" cellspacing="2">
	<thead>
		<tr style="height:30px;">
            <th width="14.2%"></th>
            <th width="14.2%" style="text-align: center;"><b> YTD</b></th>
            <th width="14.2%" style="text-align: center;"><b> Base year <br>YTD</b></th>
            <th width="14.2%" style="text-align: center;"><b> YTD Intensity</b></th>
            <th width="14.2%" style="text-align: center;"><b> Target YTD Intensity</b></th>
            <th width="14.2%" style="text-align: center;"><b> Reduction<br/>Target %</b></th>
            <th width="14.2%" style="text-align: center;"><b> Performance YTD</b></th>
        </tr>
	</thead>
	<tbody>
		<?php 
		foreach ($ProgressTargetPercentage as $ProgressTargetkey => $progressTargetvalue) {
			if ($ProgressTargetkey == 'Energy') {
				$unitKey = 'electricity';
				$unitText = GetSiteUtilityUnitName($site_id, $unitKey);
			} else if ($ProgressTargetkey == 'Water') {
				$unitKey = 'water';
				$unitText = GetSiteUtilityUnitName($site_id, $unitKey);
			} else if ($ProgressTargetkey == 'Carbon') {
				$unitText = 'kgCO<sub>2</sub>';
			}  else if($ProgressTargetkey == 'Waste'){
				$unitText = 'kg';
			} else {
				$unitText = '';
			}
			$targetYtdVal = $progressTargetvalue['TARGET_YTD'] ?? $progressTargetvalue['TARGET_BASELINE_YTD'];
			$actualIntensity = 0;
			$targetIntensity = 0;
			$unitRNText = '/RN';
			if ($ProgressTargetkey == 'Water' || $ProgressTargetkey == 'Carbon') {
				$actualIntensity = !empty($progress_guestnight_YTD) ? ($progressTargetvalue['ACTUAL_YTD'] / $progress_guestnight_YTD) : 0;
				$targetIntensity = !empty($progress_baseline_guestnight_YTD) ? ($targetYtdVal / $progress_baseline_guestnight_YTD) : 0;
				$unitRNText = '/GN';
			} else if ($ProgressTargetkey == 'Waste') {
				$wasteTargetTotal = $progressTargetvalue['TOTAL_WASTE_TARGET_YTD']
					?? ((!empty($progressTargetvalue['site_saving_target']))
						? ($progressTargetvalue['TOTAL_WASTE_BASELINE_YTD'] * (1 - ((float) $progressTargetvalue['site_saving_target'] / 100)))
						: $progressTargetvalue['TOTAL_WASTE_BASELINE_YTD']);
				$actualIntensity = !empty($progress_roomnight_YTD) ? ($progressTargetvalue['TOTAL_WASTE_YTD'] / $progress_roomnight_YTD) : 0;
				$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ($wasteTargetTotal / $progress_baseline_roomnight_YTD) : 0;
			} else {
				$actualIntensity = !empty($progress_roomnight_YTD) ? ($progressTargetvalue['ACTUAL_YTD'] / $progress_roomnight_YTD) : 0;
				$targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ($targetYtdVal / $progress_baseline_roomnight_YTD) : 0;
			}
			$baseRoomnightValue = number_format($actualIntensity, 2);
			$baseRoomnightBaselineValue = number_format($targetIntensity, 2);
			// Performance YTD = (actual - target) / target  (always divide by target)
			$progressTargetvalue['YTD_Variance'] = ($targetIntensity != 0)
				? (($actualIntensity - $targetIntensity) / $targetIntensity) * 100
				: 0;
			$image = $progressTargetvalue['YTD_Variance'] < 0 ? 'downArrow.png' : 'upArrow.png';
			$color = $progressTargetvalue['YTD_Variance'] < 0 ? '#dc2727' : '#2ecc71';
			if($ProgressTargetkey == 'Waste') {
				$image = $progressTargetvalue['YTD_Variance'] < 0 ? 'downArrowRed.png' : 'upArrowGreen.png';
			}
		?>
			<tr style="height:20px;">
				<td><b><?php echo $ProgressTargetkey; ?></b></td>
						<?php if($ProgressTargetkey == 'Waste') { ?>
							<td>
								<?php echo formatNumberAbbreviation($progressTargetvalue['ACTUAL_YTD'], 0).'<p class="unit-class"> % </p>'; ?>
								<a href="#" data-toggle="tooltip" data-container="article" data-placement="right" title="Diversion Rate" data-original-title="Diversion Rate" style="padding-left:2px;"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
							</td>
							<td>
								<?php echo formatNumberAbbreviation($progressTargetvalue['TARGET_BASELINE_YTD'], 0).'<p class="unit-class"> % </p>'; ?>
								<a href="#" data-toggle="tooltip" data-container="article" data-placement="right" title="Diversion Rate" data-original-title="Diversion Rate" style="padding-left:2px;"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
							</td>
						<?php } else { ?>
							<td><?php echo formatNumberAbbreviation($progressTargetvalue['ACTUAL_YTD'], 0).'<p class="unit-class"> '.$unitText.'</p>'; ?></td>
							<td><?php echo formatNumberAbbreviation($progressTargetvalue['TARGET_BASELINE_YTD'], 0).'<p class="unit-class"> '.$unitText.'</p>'; ?></td>
						<?php } ?>
				<td><?php echo $baseRoomnightValue . '<p class="unit-class"> ' . $unitText . $unitRNText . '</p>'; ?></td>
				<td><?php echo $baseRoomnightBaselineValue . '<p class="unit-class"> ' . $unitText . $unitRNText . '</p>'; ?></td>
				<td><?php echo isset($progressTargetvalue['site_saving_target']) ? (($ProgressTargetkey != 'Waste') ? '-' : '') . $progressTargetvalue['site_saving_target'] . '%' : '-'; ?></td>
				<td><?php echo number_format($progressTargetvalue['YTD_Variance'], 2); ?> % <img src="images/<?= $image; ?>" style="top: -2px;position: relative;float: right;margin-right: 20%;" height="15" width="15" /></td>

			</tr>
		<?php } ?>
	</tbody>
</table>