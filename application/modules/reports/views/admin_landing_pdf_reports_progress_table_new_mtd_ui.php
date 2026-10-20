<?php
// Progress on Target cards, drawn natively on $pdf (passed in via $data['pdf'])
// because TCPDF's writeHTML() cannot render this card layout reliably.
if (!function_exists('mn_pdf_rgb')) {
    function mn_pdf_rgb($hex)
    {
        $hex = ltrim($hex, '#');
        return array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
    }
    function mn_pdf_fill($pdf, $hex)
    {
        $c = mn_pdf_rgb($hex);
        $pdf->SetFillColor($c[0], $c[1], $c[2]);
    }
    function mn_pdf_text($pdf, $hex)
    {
        $c = mn_pdf_rgb($hex);
        $pdf->SetTextColor($c[0], $c[1], $c[2]);
    }
    function mn_pdf_draw($pdf, $hex)
    {
        $c = mn_pdf_rgb($hex);
        $pdf->SetDrawColor($c[0], $c[1], $c[2]);
    }
    // Shrinks the bold font until $text fits $maxW, so long "value + unit" strings
    // (e.g. "3.34m kgCO2e") don't overflow the card instead of wrapping/clipping.
    function mn_pdf_fit_bold($pdf, $text, $maxW, $startSize, $minSize = 6)
    {
        for ($size = $startSize; $size >= $minSize; $size -= 0.5) {
            $pdf->setInterFont('B', $size);
            if ($pdf->GetStringWidth($text) <= $maxW) {
                return $size;
            }
        }
        $pdf->setInterFont('B', $minSize);
        return $minSize;
    }
}

$ProgressTargetPercentage = isset($ProgressTargetPercentage) ? $ProgressTargetPercentage : array();
$progress_roomnight_YTD = isset($progress_roomnight_YTD) ? $progress_roomnight_YTD : 0;
$progress_baseline_roomnight_YTD = isset($progress_baseline_roomnight_YTD) ? $progress_baseline_roomnight_YTD : 0;
$progress_guestnight_YTD = isset($progress_guestnight_YTD) ? $progress_guestnight_YTD : 0;
$progress_baseline_guestnight_YTD = isset($progress_baseline_guestnight_YTD) ? $progress_baseline_guestnight_YTD : 0;

if (isset($pdf) && !empty($ProgressTargetPercentage)) {
    $colors = array('Energy' => '#5B9BE8', 'Carbon' => '#2D55A8', 'Water' => '#2E9E6A', 'Waste' => '#E8A33D');

    // This whole page (cards + utilities table) is drawn with fixed, pre-computed
    // coordinates. TCPDF's automatic page-break would otherwise silently insert a
    // page break mid-Cell() the instant Y nears the bottom margin, fragmenting the
    // cards/table across many pages. We take over pagination ourselves instead:
    // force one fresh page up front if there isn't room for the whole block, then
    // disable auto-break for the duration of the draw.
    $neededHeight = 235; // cards (~75mm) + utilities table (~150mm), with margin
    if (($pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin()) < $neededHeight) {
        $pdf->AddPage();
    }
    $pdf->SetAutoPageBreak(false, 0);

    $left = 10;
    $pageW = 190;
    $y = $pdf->GetY() + 5;

    $pdf->SetXY($left, $y);
    $pdf->setInterFont('B', 8);
    mn_pdf_text($pdf, '#F0A04B');
    $pdf->Cell($pageW, 4, lang('performance_overview'), 0, 1, 'L');
    $pdf->SetX($left);
    $pdf->setInterFont('B', 14);
    mn_pdf_text($pdf, '#17375E');
    $pdf->Cell($pageW, 7, lang('progress_reduction_targets'), 0, 1, 'L');
    $pdf->SetX($left);
    $pdf->setInterFont('', 7.5);
    mn_pdf_text($pdf, '#4A5A6C');
    $pdf->Cell($pageW, 4, lang('performance_ytd_desc'), 0, 1, 'L');
    $y = $pdf->GetY() + 2;

    $n = count($ProgressTargetPercentage);
    $gap = 5;
    $cardW = ($pageW - ($gap * ($n - 1))) / $n;
    $cardH = 56;

    // ---- reduction target cards (Energy / Water / Carbon / Waste) ----
    $baseIconPath = FCPATH . 'themes/default/images/waste/';
    $icons = array(
        'Energy' => 'icon-energy-navy.png',
        'Carbon' => 'icon-carbon-navy.png',
        'Water'  => 'icon-water-navy.png',
        'Waste'  => 'icon-waste-bin-navy.png'
    );
    // Pre-pass: find one font size that fits every card's ACTUAL/BASELINE YTD text, so
    // the longest unit (e.g. "kgCO2e" on Carbon) doesn't make that card's text visibly
    // smaller than the others - only the UNIT shrinks to fit, the value stays a fixed size
    // on every card.
    $cwForFit = $cardW - (2.5 * 2);
    $halfWForFit = $cwForFit / 2;
    $valueFontSize = 8;
    $unitFontSize = 6.5;
    foreach ($ProgressTargetPercentage as $fitKey => $fitValue) {
        if ($fitKey == 'Energy') {
            $fitUnitText = GetSiteUtilityUnitName($site_id, 'electricity');
        } elseif ($fitKey == 'Water') {
            $fitUnitText = GetSiteUtilityUnitName($site_id, 'water');
        } elseif ($fitKey == 'Carbon') {
            $fitUnitText = lang('unit_kgco2e');
        } elseif ($fitKey == 'Waste') {
            $fitUnitText = 'kg';
        } else {
            $fitUnitText = '';
        }
        $fitSuffix = ($fitKey == 'Waste') ? '%' : $fitUnitText;
        $pdf->setInterFont('B', $valueFontSize);
        $fitActualValueW = $pdf->GetStringWidth(formatNumberAbbreviation($fitValue['ACTUAL_YTD'], 2) . ' ');
        $fitBaselineValueW = $pdf->GetStringWidth(formatNumberAbbreviation($fitValue['TARGET_BASELINE_YTD'], 2) . ' ');
        $unitFontSize = min(
            $unitFontSize,
            mn_pdf_fit_bold($pdf, $fitSuffix, $halfWForFit - 2 - $fitActualValueW, $unitFontSize, 5.5),
            mn_pdf_fit_bold($pdf, $fitSuffix, $halfWForFit - 2 - $fitBaselineValueW, $unitFontSize, 5.5)
        );
    }

    $x = $left;
    foreach ($ProgressTargetPercentage as $key => $value) {
        if ($key == 'Energy') {
            $unitText = GetSiteUtilityUnitName($site_id, 'electricity');
        } elseif ($key == 'Water') {
            $unitText = GetSiteUtilityUnitName($site_id, 'water');
        } elseif ($key == 'Carbon') {
            $unitText = lang('unit_kgco2e');
        } elseif ($key == 'Waste') {
            $unitText = 'kg';
        } else {
            $unitText = '';
        }

        $targetYtdVal = isset($value['TARGET_YTD']) ? $value['TARGET_YTD'] : $value['TARGET_BASELINE_YTD'];
        $actualIntensity = 0;
        $targetIntensity = 0;
        $unitRNText = '/RN';
        if ($key == 'Water' || $key == 'Carbon') {
            $actualIntensity = !empty($progress_guestnight_YTD) ? ((float) $value['ACTUAL_YTD'] / (float) $progress_guestnight_YTD) : 0;
            $targetIntensity = !empty($progress_baseline_guestnight_YTD) ? ((float) $targetYtdVal / (float) $progress_baseline_guestnight_YTD) : 0;
            $unitRNText = '/GN';
        } elseif ($key == 'Waste') {
            $wasteTargetTotal = isset($value['TOTAL_WASTE_TARGET_YTD']) ? $value['TOTAL_WASTE_TARGET_YTD']
                : ((!empty($value['site_saving_target'])) ? ($value['TOTAL_WASTE_BASELINE_YTD'] * (1 - ((float) $value['site_saving_target'] / 100))) : $value['TOTAL_WASTE_BASELINE_YTD']);
            $actualIntensity = !empty($progress_roomnight_YTD) ? ((float) $value['TOTAL_WASTE_YTD'] / (float) $progress_roomnight_YTD) : 0;
            $targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ((float) $wasteTargetTotal / (float) $progress_baseline_roomnight_YTD) : 0;
        } else {
            $actualIntensity = !empty($progress_roomnight_YTD) ? ((float) $value['ACTUAL_YTD'] / (float) $progress_roomnight_YTD) : 0;
            $targetIntensity = !empty($progress_baseline_roomnight_YTD) ? ((float) $targetYtdVal / (float) $progress_baseline_roomnight_YTD) : 0;
        }

        $actVal = number_format($actualIntensity, 2);
        $tgtVal = number_format($targetIntensity, 2);
        $variance = ($targetIntensity != 0) ? (($actualIntensity - $targetIntensity) / $targetIntensity) * 100 : 0;
        $isGood = $variance <= 0;
        $bg = $isGood ? '#E3F3EA' : '#FBE7E5';
        $fg = $isGood ? '#2E8B57' : '#C8382F';
        // $theme = isset($colors[$key]) ? $colors[$key] : '#2D55A8';
        $theme = '#2D55A8';

        // card border
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($x, $y, $cardW, $cardH, 1.6, '1111', 'D');

        $pad = 2.5;
        $cx = $x + $pad;
        $cw = $cardW - ($pad * 2);
        $cy = $y + 2.5;

        // dot + title
        $iconFile = isset($icons[$key]) ? $icons[$key] : '';
        if ($iconFile && file_exists($baseIconPath . $iconFile)) {
            $pdf->Image($baseIconPath . $iconFile, $cx, $cy + 0.5, 3.5, 3.5);
            $pdf->SetXY($cx + 4, $cy);
        } else {
            mn_pdf_fill($pdf, $theme);
            $pdf->Rect($cx, $cy + 1.3, 2.2, 2.2, 'F');
            $pdf->SetXY($cx + 3, $cy);
        }

        $pdf->setInterFont('B', 9.5);
        mn_pdf_text($pdf, '#17375E');
        $pdf->Cell($cw * 0.55, 5, ' ' . $key, 0, 0, 'L');

        // chip top-right
        $chipLabel = ($variance >= 0 ? "\xE2\x96\xB2 " : "\xE2\x96\xBC ") . number_format(abs($variance), 2) . '%';
        $pdf->setInterFont('B', 7);
        $chipW = $pdf->GetStringWidth($chipLabel) + 2.6;
        $chipH = 3.6;
        $chipX = $x + $cardW - $pad - $chipW;
        mn_pdf_fill($pdf, $bg);
        $pdf->RoundedRect($chipX, $cy, $chipW, $chipH, 1, '1111', 'F');
        mn_pdf_text($pdf, $fg);
        $pdf->SetXY($chipX, $cy);
        $pdf->Cell($chipW, $chipH, $chipLabel, 0, 0, 'C');
        $pdf->SetXY($x + $pad, $cy + $chipH + 0.3);
        $pdf->setInterFont('', 5.5);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->Cell($cw, 2.6, lang('performance_ytd'), 0, 1, 'R');

        $ry = $cy + 8;
        $halfW = $cw / 2;
        $pdf->setInterFont('', 6);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($halfW - 2, 3, ($key == 'Waste') ? 'DIVERSION YTD' : 'YTD', 0, 0, 'L');
        $pdf->SetXY($cx + $halfW + 2, $ry);
        $pdf->Cell($halfW - 2, 3, lang('base_year_ytd'), 0, 0, 'L');

        $ry += 3.3;
        $actualValueText = formatNumberAbbreviation($value['ACTUAL_YTD'], 2);
        $baselineValueText = formatNumberAbbreviation($value['TARGET_BASELINE_YTD'], 2);
        $unitSuffix = ($key == 'Waste') ? '%' : $unitText;

        mn_pdf_text($pdf, '#17375E');
        $pdf->setInterFont('B', $valueFontSize);
        $actualValueW = $pdf->GetStringWidth($actualValueText);
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($actualValueW, 5, $actualValueText, 0, 0, 'L');
        $pdf->setInterFont('B', $unitFontSize);
        $pdf->SetXY($cx + $actualValueW + 1, $ry);
        $pdf->Cell($halfW - 2 - $actualValueW - 1, 5, $unitSuffix, 0, 0, 'L');

        mn_pdf_text($pdf, '#6B7785');
        $pdf->setInterFont('B', $valueFontSize);
        $baselineValueW = $pdf->GetStringWidth($baselineValueText);
        $pdf->SetXY($cx + $halfW + 2, $ry);
        $pdf->Cell($baselineValueW, 5, $baselineValueText, 0, 0, 'L');
        $pdf->setInterFont('B', $unitFontSize);
        $pdf->SetXY($cx + $halfW + 2 + $baselineValueW + 1, $ry);
        $pdf->Cell($halfW - 2 - $baselineValueW - 1, 5, $unitSuffix, 0, 0, 'L');

        $pdf->setInterFont('B', 8);

        $ry += 6;
        $pdf->setInterFont('', 6);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($cw, 3, lang('reduction_target'), 0, 1, 'L');
        $ry += 3.3;
        $pdf->setInterFont('B', 9.5);
        mn_pdf_text($pdf, '#17375E');
        $pdf->SetXY($cx, $ry);
        $targetLabel = isset($value['site_saving_target']) ? (($key != 'Waste') ? '-' : '') . $value['site_saving_target'] . '%' : '-';
        $pdf->Cell($cw, 5, $targetLabel, 0, 1, 'L');

        $ry += 5.5;
        $pdf->setInterFont('', 6);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($cw, 3, lang('ytd_intensity'), 0, 1, 'L');
        $ry += 3.3;
        $pdf->setInterFont('B', 10);
        mn_pdf_text($pdf, '#17375e');
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($cw, 5.5, $actVal, 0, 1, 'L');
        $valueWidth = $pdf->GetStringWidth($actVal);
        $pdf->setInterFont('B', 7.5);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->SetXY($cx+$valueWidth + 1, $ry);
        $pdf->Cell($cw, 5.5,' ' . $unitText . $unitRNText, 0, 1, 'L');
        $ry += 5.3;
        $pdf->setInterFont('', 6);
        mn_pdf_text($pdf, '#6B7785');
        $pdf->SetXY($cx, $ry);
        $pdf->Cell($cw, 3, lang('vs_target') . ' ' . $tgtVal . ' ' . $unitText . $unitRNText, 0, 1, 'L');

        $ry += 5.3;
        $barX = $cx + 1;
        $barW = $cw - 2;
        $barH = 1.4;

        // Scale is 0 to 1.12x target (per spec), not 0-100% - this is what lets the bar show
        // how far above/below target the actual value sits, instead of always filling the track.
        $maxScale = ($targetIntensity > 0) ? ($targetIntensity * 1.12) : $actualIntensity;
        $fillRatio = ($maxScale > 0) ? min(1, max(0, $actualIntensity / $maxScale)) : 0;
        $fillW = $barW * $fillRatio;

        mn_pdf_fill($pdf, '#E3E7EE');
        $pdf->RoundedRect($barX, $ry, $barW, $barH, $barH / 2, '1111', 'F');
        if ($fillW > 0) {
            mn_pdf_fill($pdf, $theme);
            $pdf->RoundedRect($barX, $ry, $fillW, $barH, $barH / 2, '1111', 'F');
        }

        $tickRatio = ($maxScale > 0) ? min(0.98, max(0.02, $targetIntensity / $maxScale)) : 0.5;
        $tickX = $barX + ($barW * $tickRatio);
        mn_pdf_draw($pdf, '#C98A2E');
        $pdf->SetLineWidth(0.35);
        $pdf->SetLineStyle(array('dash' => '0.4,0.4'));
        $pdf->Line($tickX, $ry - 0.5, $tickX, $ry + $barH + 0.5);
        $pdf->SetLineStyle(array('dash' => 0));

        $ry += $barH + 1.8;
        $pdf->setInterFont('', 5.5);
        mn_pdf_text($pdf, '#9A5E0C');
        $targetText = 'Target ' . $tgtVal;
        $targetW = $pdf->GetStringWidth($targetText) + 1;
        $targetX = max($cx, min($tickX - ($targetW / 2), $cx + $cw - $targetW));
        $pdf->SetXY($targetX, $ry);
        $pdf->Cell($targetW, 3, $targetText, 0, 0, 'L');

        $x += $cardW + $gap;
    }

    $pdf->SetXY($left, $y + $cardH + 3);
    mn_pdf_text($pdf, '#000000');
}
