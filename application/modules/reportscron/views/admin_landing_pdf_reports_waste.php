<?php
if (!function_exists('render_waste_report_pdf')) {
    function waste_pdf_num($value)
    {
        return (float) str_replace(array(',', '%', ' '), '', (string) $value);
    }

    function waste_pdf_find($rows, $needle)
    {
        foreach ($rows as $row) {
            if (isset($row['metric']) && strpos($row['metric'], $needle) === 0) {
                return $row;
            }
        }
        return array('metric' => $needle, 'current' => '', 'previous' => '', 'change' => '', 'value' => '');
    }

    function waste_pdf_chip($pdf, $x, $y, $text, $tone, $direction)
    {
        if ($tone === 'good') {
            $bg = array(227, 243, 234);
            $fg = array(46, 139, 87);
        } elseif ($tone === 'bad') {
            $bg = array(251, 231, 229);
            $fg = array(200, 56, 47);
        } else {
            $bg = array(238, 242, 246);
            $fg = array(74, 90, 108);
        }
        $pdf->setInterFont('B', 7);
        $w = $pdf->GetStringWidth($text) + ($direction === 'flat' ? 7 : 8.5);
        $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
        $pdf->RoundedRect($x, $y, $w, 4.4, 2.2, '1111', 'F');
        if ($direction === 'down' || $direction === 'up') {
            $arrowImage = site_url() . 'themes/default/images/waste/tri-' . $direction . '-' . $tone . '.png';
            $pdf->Image($arrowImage, $x + 1.5, $y + 1.2, 2.4, 2.4, '', '', '', false, 300, '', false, false, 0);
            $tx = $x + 4.2;
        } else {
            $tx = $x + 2.2;
        }
        $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
        $pdf->SetXY($tx, $y + 0.5);
        $pdf->Cell($w - ($tx - $x) - 1, 3.4, $text, 0, 0, 'L');
        $pdf->SetTextColor(0, 0, 0);
        return $w;
    }

    function waste_pdf_tone($value, $highBetter)
    {
        if (abs($value) < 0.005) {
            return array('flat', 'neutral');
        }
        $down = $value < 0;
        $good = $highBetter ? !$down : $down;
        return array($down ? 'down' : 'up', $good ? 'good' : 'bad');
    }

    if (!function_exists('getMetricImg')) {
        function getMetricImg($metric)
        {
            $metricImg = array(
                'Total waste generated (kg)' => 'icon-waste-bin-navy',
                'Organic / food waste (kg)' => 'icon-food-waste-navy',
                'Recyclables (kg)' => 'icon-recycle-navy',
                'Waste To Energy' => 'icon-energy-navy',
                'Hazardous waste (kg)' => 'icon-hazardous-navy',
                'Waste diverted from landfill (%)' => 'icon-diverted-truck-navy',
                'Recyclables Rate' => 'icon-recycle-navy',
                'Waste (kg/Room night)' => 'icon-room-night-navy',
                'Organic waste (kg/Guest Night)' => 'icon-organic-leaf-navy',
                'Recyclables (kg/Guest Night)' => 'icon-recycle-navy',
            );
            return isset($metricImg[$metric]) ? site_url() . 'themes/default/images/waste/' . $metricImg[$metric] . '.png' : '';
        }
    }

    function render_waste_report_pdf($pdf, $data)
    {
        $report = isset($data['WasteReport']) ? $data['WasteReport'] : array();
        $wasteRows = isset($report['wasteReport']) ? $report['wasteReport'] : array();
        $guestRows = isset($report['wastePerGuest']) ? $report['wastePerGuest'] : array();
        if (empty($wasteRows)) {
            return;
        }
        $months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
        $short = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $month = isset($report['currentMonth']) ? (int) $report['currentMonth'] : 0;
        $year = isset($report['currentYear']) ? $report['currentYear'] : '';
        if (!empty($report['isAnnual'])) {
            $currentPeriod = 'Annual - ' . $year;
            $previousPeriod = 'Annual - ' . ($year - 1);
            $currentShort = 'Annual ' . $year;
            $previousShort = 'Annual ' . ($year - 1);
        } elseif (!empty($report['isYtd'])) {
            $currentPeriod = 'YTD - ' . $year;
            $previousPeriod = 'YTD - ' . ($year - 1);
            $currentShort = 'YTD ' . $year;
            $previousShort = 'YTD ' . ($year - 1);
        } else {
            $currentPeriod = (isset($months[$month]) ? $months[$month] : '') . ' ' . $year;
            $previousPeriod = (isset($months[$month]) ? $months[$month] : '') . ' ' . ($year - 1);
            $currentShort = (isset($short[$month]) ? $short[$month] : '') . ' ' . $year;
            $previousShort = (isset($short[$month]) ? $short[$month] : '') . ' ' . ($year - 1);
        }

        $x0 = 10;
        $y = 26;
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(240, 160, 75);
        $pdf->SetXY($x0, $y);
        $pdf->Cell(80, 4, 'WASTE', 0, 0, 'L');
        $y += 5;
        $pdf->setInterFont('B', 16);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->SetXY($x0, $y);
        $pdf->Cell(120, 7, 'Waste report', 0, 0, 'L');
        $y += 8;
        $pdf->setInterFont('', 9);
        $pdf->SetTextColor(74, 90, 108);
        $pdf->SetXY($x0, $y);
        $pdf->Cell(160, 4, $currentPeriod . ' vs ' . $previousPeriod, 0, 0, 'L');
        $y += 7;

        $total = waste_pdf_find($wasteRows, 'Total waste generated');
        $room = waste_pdf_find($guestRows, 'Waste (kg/Room night)');
        $rate = waste_pdf_find($wasteRows, 'Recyclables Rate');
        $diverted = waste_pdf_find($wasteRows, 'Waste diverted');
        $wasteImagePath = site_url() . 'themes/default/images/waste/';
        $kpis = array(
            array('label' => 'TOTAL WASTE GENERATED', 'icon' => 'icon-waste-bin-navy.png', 'value' => $total['current'], 'unit' => 'kg', 'change' => $total['change'], 'vs' => $total['previous'], 'suffix' => '%', 'high' => false),
            array('label' => 'WASTE PER ROOM NIGHT', 'icon' => 'icon-room-night-navy.png', 'value' => $room['current'], 'unit' => 'kg/RN', 'change' => isset($room['value']) ? $room['value'] : '', 'vs' => $room['previous'], 'suffix' => '%', 'high' => false),
            array('label' => 'RECYCLABLES RATE', 'icon' => 'icon-recycle-navy.png', 'value' => $rate['current'], 'unit' => '%', 'change' => $rate['change'], 'vs' => $rate['previous'], 'suffix' => ' pts', 'high' => true),
            array('label' => 'DIVERTED FROM LANDFILL', 'icon' => 'icon-diverted-truck-navy.png', 'value' => $diverted['current'], 'unit' => '%', 'change' => $diverted['change'], 'vs' => $diverted['previous'], 'suffix' => ' pts', 'high' => true),
        );
        $gap = 2;
        $kw = (190 - (3 * $gap)) / 4;
        $kh = 24;
        foreach ($kpis as $i => $kpi) {
            $kx = $x0 + ($i * ($kw + $gap));
            $pdf->SetDrawColor(213, 223, 233);
            $pdf->SetLineWidth(0.2);
            $pdf->RoundedRect($kx, $y, $kw, $kh, 1.8, '1111', 'D');
            $pdf->setInterFont('', 5.5);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->Image($wasteImagePath . $kpi['icon'], $kx + 2, $y + 1.7, 3.5, 3.5, '', '', '', false, 300, '', false, false, 0);
            $pdf->SetXY($kx + 7, $y + 1.6);
            $pdf->Cell($kw - 8.5, 3.5, $kpi['label'], 0, 0, 'L');
            $pdf->setInterFont('B', 12);
            $pdf->SetTextColor(23, 55, 94);
            $pdf->SetXY($kx + 2, $y + 7);
            $pdf->Cell($kw - 3, 6, $kpi['value'], 0, 0, 'L');
            $pdf->setInterFont('', 7);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->SetXY($kx + 2, $y + 13);
            $pdf->Cell($kw - 3, 3, $kpi['unit'], 0, 0, 'L');
            $num = waste_pdf_num($kpi['change']);
            list($dir, $tone) = waste_pdf_tone($num, $kpi['high']);
            $chip = ($num > 0 ? '+' : '') . $kpi['change'] . $kpi['suffix'] . ' vs ' . $kpi['vs'];
            waste_pdf_chip($pdf, $kx + 2, $y + 18.2, $chip, $tone, $dir);
        }
        $y += $kh + 3;

        // Higher is better for these metrics (more diverted from landfill / more recycled), so
        // an increase should colour green, not red. "Waste diverted from landfill" was missing
        // here, showing an increase in diversion (a good thing) in red.
        $highBetter = array('Recyclables (kg)', 'Recyclables Rate', 'Recyclables (kg/Guest Night)', 'Waste To Energy', 'Waste diverted from landfill (%)');
        $sections = array(
            array('title' => 'Waste generated & diversion', 'bg' => array(23, 55, 94), 'rows' => $wasteRows, 'key' => 'change', 'header_icon' => 'icon-waste-bin-white.png', 'current_cal' => 'icon-calendar-navy.png'),
            array('title' => 'Waste per guest / occupied room', 'bg' => array(15, 102, 86), 'rows' => $guestRows, 'key' => 'value', 'header_icon' => 'icon-room-night-white.png', 'current_cal' => 'icon-calendar-navy.png'),
        );
        $cols = array(78, 38, 37, 37);
        foreach ($sections as $section) {
            if (empty($section['rows'])) {
                continue;
            }
            $pdf->SetFillColor($section['bg'][0], $section['bg'][1], $section['bg'][2]);
            $pdf->RoundedRect($x0, $y, 190, 7, 1.6, '1001', 'F');
            $pdf->Rect($x0, $y + 3, 190, 4, 'F');
            $pdf->setInterFont('B', 9);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Image($wasteImagePath . $section['header_icon'], $x0 + 3, $y + 1.5, 4, 4, '', '', '', false, 300, '', false, false, 0);
            $pdf->SetXY($x0 + 9, $y + 1.2);
            $pdf->Cell(174, 4.5, $section['title'], 0, 0, 'L');
            $y += 7;
            $heads = array('Metric', $currentPeriod, $previousPeriod, 'Variation (%)');
            $headIcons = array('', $section['current_cal'], 'icon-calendar-navy.png', 'icon-trend-variation-teal.png');
            $headBg = array(array(244, 247, 250), array(227, 237, 247), array(244, 247, 250), array(221, 240, 232));
            $hx = $x0;
            $pdf->setInterFont('B', 6.5);
            $pdf->SetTextColor(23, 55, 94);
            foreach ($heads as $hi => $head) {
                $pdf->SetFillColor($headBg[$hi][0], $headBg[$hi][1], $headBg[$hi][2]);
                $pdf->Rect($hx, $y, $cols[$hi], 6, 'F');
                $headTextX = $hx + 1;
                $headTextWidth = $cols[$hi] - 2;
                if ($headIcons[$hi] !== '') {
                    $iconWidth = 3;
                    $iconGap = 1.5;
                    $textWidth = $pdf->GetStringWidth($head);
                    $groupWidth = $iconWidth + $iconGap + $textWidth;
                    $groupX = $hx + (($cols[$hi] - $groupWidth) / 2);
                    $pdf->Image($wasteImagePath . $headIcons[$hi], $groupX, $y + 1.5, $iconWidth, 3, '', '', '', false, 300, '', false, false, 0);
                    $headTextX = $groupX + $iconWidth + $iconGap;
                    $headTextWidth = $textWidth + 0.5;
                }
                $pdf->SetXY($headTextX, $y + 1);
                $pdf->Cell($headTextWidth, 4, $head, 0, 0, $hi === 0 ? 'L' : ($headIcons[$hi] !== '' ? 'L' : 'C'));
                $hx += $cols[$hi];
            }
            $pdf->SetDrawColor(213, 223, 233);
            $pdf->Line($x0, $y + 6, $x0 + 190, $y + 6);
            $y += 6;
            $rh = 8;
            $last = count($section['rows']) - 1;
            foreach ($section['rows'] as $ri => $row) {
                $metric = $row['metric'];
                $pos = strrpos($metric, '(');
                if (strpos($metric, 'Recyclables Rate') !== false) {
                    $name = 'Recyclables rate';
                    $unit = '(%, change in points)';
                } elseif (strpos($metric, 'Waste To Energy') !== false) {
                    $name = 'Waste to energy';
                    $unit = '(kg)';
                } elseif ($pos !== false) {
                    $name = trim(substr($metric, 0, $pos));
                    $unit = trim(substr($metric, $pos));
                } else {
                    $name = $metric;
                    $unit = '';
                }
                $metricImg = getMetricImg($metric);
                $change = isset($row[$section['key']]) ? $row[$section['key']] : '';
                $pdf->SetFillColor(240, 246, 252);
                $pdf->Rect($x0 + $cols[0], $y, $cols[1], $rh, 'F');
                $pdf->SetFillColor(241, 249, 245);
                $pdf->Rect($x0 + $cols[0] + $cols[1] + $cols[2], $y, $cols[3], $rh, 'F');
                $metricTextX = $x0 + 2;
                if ($metricImg !== '') {
                    $pdf->Image($metricImg, $x0 + 2, $y + 1.5, 5, 5, '', '', '', false, 300, '', false, false, 0);
                    $metricTextX = $x0 + 9;
                }
                $pdf->setInterFont('B', 7.5);
                $pdf->SetTextColor(23, 55, 94);
                $pdf->SetXY($metricTextX, $y + 1.2);
                $pdf->Cell($cols[0] - ($metricTextX - $x0) - 2, 3.2, $name, 0, 0, 'L');
                if ($unit !== '') {
                    $pdf->setInterFont('', 6);
                    $pdf->SetTextColor(107, 119, 133);
                    $pdf->SetXY($metricTextX, $y + 4.2);
                    $pdf->Cell($cols[0] - ($metricTextX - $x0) - 2, 3, $unit, 0, 0, 'L');
                }
                $pdf->setInterFont('B', 8);
                $pdf->SetTextColor(23, 55, 94);
                $pdf->SetXY($x0 + $cols[0], $y + 2);
                $pdf->Cell($cols[1], 4, $row['current'], 0, 0, 'C');
                $pdf->setInterFont('', 8);
                $pdf->SetXY($x0 + $cols[0] + $cols[1], $y + 2);
                $pdf->Cell($cols[2], 4, $row['previous'], 0, 0, 'C');
                $num = waste_pdf_num($change);
                $isHigh = in_array($metric, $highBetter, true);
                list($dir, $tone) = waste_pdf_tone($num, $isHigh);
                // Both are percentages compared point-to-point (a +X percentage-point move),
                // not a relative "% change" - so the variation chip reads "+X pts" for both,
                // same as the Recyclables Rate KPI card above.
                $chipText = (strpos($metric, 'Recyclables Rate') !== false || strpos($metric, 'Waste diverted from landfill') !== false)
                    ? (($num > 0 ? '+' : '') . $change . ' pts')
                    : $change;
                $chipW = $pdf->GetStringWidth($chipText) + ($dir === 'flat' ? 7 : 8.5);
                waste_pdf_chip($pdf, $x0 + $cols[0] + $cols[1] + $cols[2] + (($cols[3] - $chipW) / 2), $y + 1.8, $chipText, $tone, $dir);
                if ($ri < $last) {
                    $pdf->SetDrawColor(238, 242, 246);
                    $pdf->SetLineWidth(0.15);
                    $pdf->Line($x0, $y + $rh, $x0 + 190, $y + $rh);
                }
                $y += $rh;
            }
            $pdf->SetDrawColor(213, 223, 233);
            $pdf->SetLineWidth(0.25);
            $pdf->Line($x0, $y, $x0 + 190, $y);
            $y += 3.5;
        }

        $wte = waste_pdf_find($wasteRows, 'Waste To Energy');
        $rec = waste_pdf_find($wasteRows, 'Recyclables (kg)');
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->RoundedRect($x0, $y, 190, 33, 1.6, '1111', 'D');
        $pdf->SetFillColor(247, 250, 252);
        $pdf->RoundedRect($x0, $y, 190, 7, 1.6, '1001', 'F');
        $pdf->Rect($x0 + 0.2, $y + 3, 189.6, 4, 'F');
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->Line($x0, $y + 7, $x0 + 190, $y + 7);
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Image($wasteImagePath . 'icon-diverted-truck-navy.png', $x0 + 3, $y + 1.7, 3.5, 3.5, '', '', '', false, 300, '', false, false, 0);
        $pdf->SetXY($x0 + 8, $y + 1.5);
        $pdf->Cell(65, 4, 'Where the waste went', 0, 0, 'L');
        $pdf->setInterFont('', 7);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->SetXY($x0 + 78, $y + 1.6);
        $pdf->Cell(108, 4, 'Share of total waste by destination', 0, 0, 'R');
        $y += 9;
        $pairs = array(
            array($currentShort, $wte['current'], $rec['current'], $total['current'], 1),
            array($previousShort, $wte['previous'], $rec['previous'], $total['previous'], 0.45),
        );
        foreach ($pairs as $pair) {
            $wteN = waste_pdf_num($pair[1]);
            $recN = waste_pdf_num($pair[2]);
            $totalN = waste_pdf_num($pair[3]);
            if ($totalN <= 0) {
                $totalN = $wteN + $recN;
            }
            $pdf->setInterFont('B', 7);
            $pdf->SetTextColor(23, 55, 94);
            $pdf->SetXY($x0 + 2, $y + 1);
            $pdf->Cell(28, 5, $pair[0], 0, 0, 'R');
            $barX = $x0 + 32;
            $barW = 128;
            if ($totalN > 0) {
                $wtePctN = min(100, max(0, ($wteN / $totalN) * 100));
                $recPctN = min(100 - $wtePctN, max(0, ($recN / $totalN) * 100));
                // Whatever's left after waste-to-energy + recyclables is landfill - previously
                // left as uncolored background track with no label, showing as an empty gap.
                $landfillPctN = max(0, 100 - $wtePctN - $recPctN);
                $landfillPct = number_format($landfillPctN, 2);
                // Binary floating-point means wtePctN + recPctN rarely sums to *exactly* 100
                // even when it mathematically should (e.g. wte+rec == total already) - leaving
                // a near-zero epsilon in $landfillPctN that still passed "> 0" below, and the
                // minimum-visible-width floor then rendered that epsilon as a visible sliver.
                // Gate on the same rounded value the label uses, not the raw float.
                $showLandfillSegment = ($landfillPct !== '0.00');
                $w1 = $barW * ($wtePctN / 100);
                $w2 = $barW * ($recPctN / 100);
                $w3 = $showLandfillSegment ? ($barW * ($landfillPctN / 100)) : 0;
                $pdf->SetAlpha($pair[4]);
                $pdf->SetFillColor(238, 242, 246);
                $pdf->RoundedRect($barX, $y, $barW, 6, 1, '1111', 'F');
                $pdf->SetFillColor(45, 85, 168);
                if ($w1 > 0) {
                    $pdf->RoundedRect($barX, $y, max($w1, 0.8), 6, 1, '1111', 'F');
                }
                $pdf->SetFillColor(44, 156, 138);
                if ($w2 > 0) {
                    $pdf->RoundedRect($barX + $w1 + 0.6, $y, max($w2 - 0.6, 0.8), 6, 1, '1111', 'F');
                }
                $pdf->SetFillColor(237, 165, 59);
                if ($showLandfillSegment) {
                    $pdf->RoundedRect($barX + $w1 + $w2 + 0.6, $y, max($w3 - 0.6, 0.8), 6, 1, '1111', 'F');
                }
                $pdf->SetAlpha(1);
                $wtePct = number_format($wtePctN, 2);
                $recPct = number_format($recPctN, 2);
                $pdf->setInterFont('B', 6.5);
                $pdf->SetTextColor(255, 255, 255);
                if ($w1 > 42) {
                    $pdf->SetXY($barX + 1.5, $y + 1.2);
                    $pdf->Cell($w1 - 2, 3.6, 'Waste to energy ' . $wtePct . '%', 0, 0, 'L');
                }
                $pdf->setInterFont('', 6);
                $pdf->SetTextColor(23, 55, 94);
                $pdf->SetXY($x0 + 32, $y + 6.6);
                // Omit the Landfill segment/label entirely when there's nothing to show - a
                // "Landfill 0.00%" label (and the invisible 0-width segment) is just clutter.
                $belowBarLabel = 'Recyclables ' . $recPct . '%';
                if ($landfillPct !== '0.00') {
                    $belowBarLabel .= '  ·  Landfill ' . $landfillPct . '%';
                }
                $pdf->Cell($barW, 3, $belowBarLabel, 0, 0, 'L');
            }
            $y += 11;
        }
        $pdf->SetAlpha(1);
        $pdf->SetTextColor(0, 0, 0);
    }
}
