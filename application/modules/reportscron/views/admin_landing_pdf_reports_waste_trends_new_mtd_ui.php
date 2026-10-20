<?php
// "Waste trends" PDF page: YTD KPI cards, a monthly stacked waste-by-destination
// bar chart, four monthly trend mini-charts, and a YTD summary table.
// Drawn natively on $pdf because TCPDF's writeHTML() cannot render charts.
// Colors/labels/layout are matched to the HEP_monthly_report modernised mockup's
// "Waste trends" page. Reuses the waste_pdf_*() helpers (num/find/chip/tone)
// already defined in admin_landing_pdf_reports_waste.php.
if (!function_exists('render_waste_trends_pdf')) {
    require_once __DIR__ . '/admin_landing_pdf_reports_waste.php';

    // Picks a "nice" axis step (1/2/5 x a power of ten) so gridlines land on round numbers.
    function waste_trends_axis($minV, $maxV, $targetLines = 4)
    {
        $range = max($maxV - $minV, 0.0001);
        $rawStep = $range / $targetLines;
        $mag = pow(10, floor(log10($rawStep)));
        $norm = $rawStep / $mag;
        $niceNorm = ($norm <= 1) ? 1 : (($norm <= 2) ? 2 : (($norm <= 5) ? 5 : 10));
        $step = $niceNorm * $mag;
        $axisMin = floor($minV / $step) * $step;
        $axisMax = ceil($maxV / $step) * $step;
        if ($axisMax <= $axisMin) {
            $axisMax = $axisMin + $step;
        }
        return array($axisMin, $axisMax, $step);
    }

    function waste_trends_mini_chart($pdf, $x, $y, $w, $h, $title, $subtitle, $allMonths, $endMonth, $current, $previous, $decimals, $axisSuffix, $targetValue, $targetLabel, $year, $prevYear)
    {
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($x, $y, $w, $h, 1.6, '1111', 'D');

        $pad = 3;
        $pdf->setInterFont('B', 7);
        mn_pdf_text($pdf, '#17375E');
        $pdf->SetXY($x + $pad, $y + 2);
        $pdf->Cell(($w - ($pad * 2)) * 0.55, 3.5, $title, 0, 0, 'L');
        $pdf->setInterFont('', 5);
        mn_pdf_text($pdf, '#9AA6B2');
        $pdf->SetXY($x + $pad + (($w - ($pad * 2)) * 0.45), $y + 2.3);
        $pdf->Cell(($w - ($pad * 2)) * 0.55, 3, $subtitle, 0, 0, 'R');

        $axisW = 8;
        $plotX = $x + $pad + $axisW;
        $plotY = $y + 7;
        $plotW = $w - ($pad * 2) - $axisW;
        $plotH = $h - 17.5;

        // Scale the axis to include current year, previous year, and any target value
        // so neither line is clipped and target labels do not overlap the card header.
        $allVals = array_merge(array_values($current), array_values($previous));
        if ($targetValue !== null) {
            $allVals[] = $targetValue;
        }
        $allVals = array_filter($allVals, function ($v) { return $v !== null; });
        if (empty($allVals)) {
            $allVals = array(0, 1);
        }

        $minData = min($allVals);
        $maxData = max($allVals);

        // For percentage charts like Diversion, keep full 0 to 100 scale so dips are clear
        if (stripos($title, 'diverted') !== false) {
            $axisMin = 0;
            $axisMax = 100;
            $step = 25;
        } elseif (stripos($title, 'recyclables') !== false) {
            $axisMin = 0;
            $axisMax = max(20, ceil($maxData / 5) * 5);
            $step = ($axisMax > 20) ? 10 : 5;
        } else {
            // For room night charts, ensure min starts from 0 so relative trends are honest
            list($axisMin, $axisMax, $step) = waste_trends_axis(0, $maxData * 1.1);
        }
        $range = max($axisMax - $axisMin, 0.0001);

        $toY = function ($val) use ($plotY, $plotH, $axisMin, $axisMax, $range) {
            $clamped = max($axisMin, min($axisMax, $val));
            return $plotY + $plotH - (($clamped - $axisMin) / $range) * $plotH;
        };

        // y-axis gridlines + labels
        mn_pdf_draw($pdf, '#EEF2F6');
        $pdf->SetLineWidth(0.15);
        $pdf->setInterFont('', 4.3);
        mn_pdf_text($pdf, '#9AA6B2');
        for ($v = $axisMin; $v <= $axisMax + ($step / 2); $v += $step) {
            $gy = $toY($v);
            $pdf->Line($plotX, $gy, $plotX + $plotW, $gy);
            $pdf->SetXY($x + $pad, $gy - 1.3);
            $pdf->Cell($axisW - 1, 2.6, number_format($v, $decimals) . $axisSuffix, 0, 0, 'R');
        }

        $n = count($allMonths);
        $stepX = $n > 1 ? $plotW / ($n - 1) : 0;

        if ($targetValue !== null) {
            mn_pdf_draw($pdf, '#E8A33D');
            $pdf->SetLineWidth(0.3);
            $pdf->SetLineStyle(array('dash' => '0.8,0.8'));
            $ty = $toY($targetValue);
            $pdf->Line($plotX, $ty, $plotX + $plotW, $ty);
            $pdf->SetLineStyle(array('dash' => 0));
            $pdf->setInterFont('', 4.3);
            mn_pdf_text($pdf, '#9A5E0C');
            $tLabel = $targetLabel . ' ' . number_format($targetValue, $decimals);
            $tw = $pdf->GetStringWidth($tLabel) + 1;

            // The label sits at the right edge, just above the target line by default. If the
            // previous/current-year line is also close to the target value right there, that
            // default spot collides with it - flip the label to the other side of the line
            // instead, whichever side actually has clearance from the real data at that point.
            $lastMonthKey = null;
            foreach ($allMonths as $mk => $mLbl) {
                $lastMonthKey = $mk;
            }
            $nearbyVal = null;
            if ($lastMonthKey !== null) {
                if (isset($previous[$lastMonthKey])) {
                    $nearbyVal = $previous[$lastMonthKey];
                } elseif (isset($current[$lastMonthKey])) {
                    $nearbyVal = $current[$lastMonthKey];
                }
            }
            $labelAbove = true;
            if ($nearbyVal !== null) {
                $nearbyY = $toY($nearbyVal);
                if (abs($nearbyY - $ty) < 4.5) {
                    $labelAbove = ($nearbyY > $ty);
                }
            }
            $labelY = $labelAbove ? ($ty - 3.3) : ($ty + 0.5);
            $pdf->SetXY($plotX + $plotW - $tw, $labelY);
            $pdf->Cell($tw, 2.8, $tLabel, 0, 0, 'R');
        }

        $plotLine = function ($values, $color, $dashed, $dotR) use ($pdf, $allMonths, $plotX, $stepX, $toY) {
            mn_pdf_draw($pdf, $color);
            $pdf->SetLineWidth($dashed ? 0.3 : 0.5);
            if ($dashed) {
                $pdf->SetLineStyle(array('dash' => '0.8,0.8'));
            }
            $prevX = null;
            $prevY = null;
            $i = 0;
            $lastX = null;
            $lastY = null;
            foreach ($allMonths as $m => $label) {
                if (isset($values[$m])) {
                    $px = $plotX + ($i * $stepX);
                    $py = $toY($values[$m]);
                    if ($prevX !== null) {
                        $pdf->Line($prevX, $prevY, $px, $py);
                    }
                    $prevX = $px;
                    $prevY = $py;
                    $lastX = $px;
                    $lastY = $py;
                }
                $i++;
            }
            if ($dashed) {
                $pdf->SetLineStyle(array('dash' => 0));
            }
            mn_pdf_fill($pdf, $color);
            $i = 0;
            foreach ($allMonths as $m => $label) {
                if (isset($values[$m])) {
                    $pdf->Circle($plotX + ($i * $stepX), $toY($values[$m]), $dotR, 0, 360, 'F');
                }
                $i++;
            }
            return array($lastX, $lastY);
        };

        $plotLine($previous, '#A9B4C0', true, 0.6);
        list($lastX, $lastY) = $plotLine($current, '#2D55A8', false, 0.75);

        if ($lastX !== null) {
            $lastVal = null;
            foreach ($current as $v) {
                $lastVal = $v;
            }
            $label = number_format($lastVal, $decimals) . $axisSuffix;
            $pdf->setInterFont('B', 7);
            mn_pdf_text($pdf, '#17375E');
            $lw = $pdf->GetStringWidth($label) + 2;
            $lx = min(max($lastX + 1.5, $plotX), $plotX + $plotW - $lw);
            $pdf->SetXY($lx, max($plotY, $lastY - 4.5));
            $pdf->Cell($lw, 3.5, $label, 0, 0, 'L');
        }

        $pdf->setInterFont('', 4.3);
        $i = 0;
        foreach ($allMonths as $m => $label) {
            $px = $plotX + ($i * $stepX) - 3;
            $isCurrent = ($m == $endMonth);
            $pdf->setInterFont($isCurrent ? 'B' : '', 4.3);
            mn_pdf_text($pdf, ($m <= $endMonth) ? '#17375E' : '#9AA6B2');
            $pdf->SetXY($px, $plotY + $plotH + 1.2);
            $pdf->Cell(6, 2.5, $label, 0, 0, 'C');
            $i++;
        }

        // per-card legend
        $ly = $y + $h - 3.3;
        $lx = $x + $pad;
        $legendItems = array(array((string) $prevYear, '#A9B4C0', true));
        $legendItems[] = array((string) $year, '#2D55A8', false);
        if ($targetValue !== null) {
            $legendItems[] = array('YTD target', '#E8A33D', true);
        }
        $pdf->setInterFont('', 4.6);
        foreach ($legendItems as $it) {
            list($lbl, $col, $dashed) = $it;
            mn_pdf_draw($pdf, $col);
            $pdf->SetLineWidth(0.5);
            if ($dashed) {
                $pdf->SetLineStyle(array('dash' => '0.6,0.6'));
            }
            $pdf->Line($lx, $ly + 1, $lx + 3, $ly + 1);
            if ($dashed) {
                $pdf->SetLineStyle(array('dash' => 0));
            }
            mn_pdf_text($pdf, '#4A5A6C');
            $pdf->SetXY($lx + 4, $ly);
            $tw = $pdf->GetStringWidth($lbl) + 3;
            $pdf->Cell($tw, 2.6, $lbl, 0, 0, 'L');
            $lx += 4 + $tw;
        }
    }

    function render_waste_trends_pdf($pdf, $data)
    {
        // ---- INPUT DATA ----
        $report = isset($data['WasteReportYTD']) ? $data['WasteReportYTD'] : array();
        $wasteRows = isset($report['wasteReport']) ? $report['wasteReport'] : array();
        $guestRows = isset($report['wastePerGuest']) ? $report['wastePerGuest'] : array();
        $monthly = isset($data['WasteTrendsMonthly']) ? $data['WasteTrendsMonthly'] : array();
        if (empty($wasteRows) || empty($monthly)) {
            return;
        }
        $shortMonths = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $fullMonths = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
        $year = isset($report['currentYear']) ? (int) $report['currentYear'] : (int) date('Y');
        $endMonth = isset($report['currentMonth']) ? (int) $report['currentMonth'] : count($monthly);
        $allMonths = $shortMonths;

        $x0 = 10;
        $y = 26;

        // ---- PAGE TITLE ----
        $pdf->setInterFont('B', 8);
        mn_pdf_text($pdf, '#F0A04B');
        $pdf->SetXY($x0, $y);
        $pdf->Cell(120, 4, 'WASTE', 0, 0, 'L');
        $y += 4;
        $pdf->setInterFont('B', 16);
        mn_pdf_text($pdf, '#17375E');
        $pdf->SetXY($x0, $y);
        $pdf->Cell(150, 7, lang('waste_trends_title'), 0, 0, 'L');
        $y += 7;
        $pdf->setInterFont('', 9);
        mn_pdf_text($pdf, '#4A5A6C');
        $pdf->SetXY($x0, $y);
        $pdf->Cell(190, 4, $fullMonths[1] . ' ' . "\xE2\x80\x93" . ' ' . $fullMonths[$endMonth] . ' ' . $year . ' vs ' . ($year - 1), 0, 0, 'L');
        $y += 6;

        // ---- KPI cards ----
        $baseIconPath = FCPATH . 'themes/default/images/waste/';
        $icons = array(
            'waste_bin' => 'icon-waste-bin-navy.png',
            'room_night' => 'icon-room-night-navy.png',
            'recycle' => 'icon-recycle-navy.png',
            'diverted_truck' => 'icon-diverted-truck-navy.png',
            'food_waste' => 'icon-food-waste-navy.png',
            'hazardous' => 'icon-hazardous-navy.png',
            'bar_chart' => 'icon-bar-chart-navy.png',
            'calendar' => 'icon-calendar-navy.png',
        );
        $total = waste_pdf_find($wasteRows, 'Total waste generated (kg)');
        $room = waste_pdf_find($guestRows, 'Waste (kg/Room night)');
        $rate = waste_pdf_find($wasteRows, 'Recyclables Rate');
        $diverted = waste_pdf_find($wasteRows, 'Waste diverted from landfill (%)');
        $kpis = array(
            array('label' => 'TOTAL WASTE YTD', 'val' => $total['current'], 'unit' => 'kg', 'change' => $total['change'], 'vs' => $total['previous'], 'suffix' => '%', 'high' => false, 'icon' => $icons['waste_bin']),
            array('label' => 'WASTE PER ROOM NIGHT', 'val' => isset($room['current']) ? $room['current'] : '', 'unit' => 'kg/RN', 'change' => isset($room['value']) ? $room['value'] : '', 'vs' => $room['previous'], 'suffix' => '%', 'high' => false, 'icon' => $icons['room_night']),
            array('label' => 'RECYCLABLES RATE YTD', 'val' => $rate['current'], 'unit' => '%', 'change' => $rate['change'], 'vs' => $rate['previous'], 'suffix' => ' pts', 'high' => true, 'icon' => $icons['recycle']),
            array('label' => 'DIVERTED FROM LANDFILL', 'val' => $diverted['current'], 'unit' => '%', 'change' => $diverted['change'], 'vs' => $diverted['previous'], 'suffix' => ' pts', 'high' => true, 'icon' => $icons['diverted_truck']),
        );
        $gap = 2;
        $kw = (190 - (3 * $gap)) / 4;
        $kh = 24;
        
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFillColor(255, 255, 255);

        // Carbon Footprint KPI Card Function
        $drawKpiCard = function($pdf, $x, $y, $w, $h, $title, $valueStr, $unitStr, $changeStr, $prevValueStr, $suffix, $highBetter, $iconPath='') {
            $pdf->RoundedRect($x, $y, $w, $h, 1.5, '1111', 'DF');
            
            // Icon
            $titleXOffset = 3;
            if ($iconPath != '' && file_exists($iconPath)) {
                $pdf->Image($iconPath, $x + 3, $y + 2, 3.5, 3.5);
                $titleXOffset = 8;
            }

            // Title
            $pdf->SetXY($x + $titleXOffset, $y + 2);
            $pdf->setInterFont('B', 6); // slightly smaller to fit 4 columns
            $pdf->SetTextColor(107, 119, 133);
            $pdf->Cell($w - 10, 4, strtoupper($title), 0, 0, 'L');
            
            // Value
            $pdf->SetXY($x + 3, $y + 7);
            $pdf->setInterFont('B', 16);
            $pdf->SetTextColor(23, 55, 94);
            $pdf->Cell($w - 6, 6, $valueStr, 0, 0, 'L');
            
            // Unit
            $pdf->SetXY($x + 3, $y + 13);
            $pdf->setInterFont('', 6.5);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->Cell($w - 6, 3, $unitStr, 0, 0, 'L');

            // Variation Pill
            $pillY = $y + 18.5;
            $num = floatval(str_replace(',', '', $changeStr));
            
            $isGood = $highBetter ? ($num >= 0) : ($num <= 0);
            
            if ($isGood) {
                // Green
                $bg = array(234, 245, 237);
                $fg = array(60, 170, 90);
                $varStr = '▼ ' . abs($num) . $suffix . ' vs ' . $prevValueStr;
                if($highBetter && $num >= 0) $varStr = '▲ ' . abs($num) . $suffix . ' vs ' . $prevValueStr;
            } else {
                // Red
                $bg = array(252, 237, 237);
                $fg = array(217, 67, 67);
                $varStr = '▲ ' . abs($num) . $suffix . ' vs ' . $prevValueStr;
                if($highBetter && $num < 0) $varStr = '▼ ' . abs($num) . $suffix . ' vs ' . $prevValueStr;
            }
            if ($num == 0) {
                $bg = array(240, 243, 246);
                $fg = array(107, 119, 133);
                $varStr = '— 0' . $suffix . ' vs ' . $prevValueStr;
            }
            
            $pdf->setInterFont('B', 5);
            $strW = $pdf->GetStringWidth($varStr) + 4;
            $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
            $pdf->SetDrawColor($bg[0], $bg[1], $bg[2]); // transparent border
            $pdf->RoundedRect($x + 3, $pillY, $strW, 3.5, 1.0, '1111', 'DF');
            $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
            $pdf->SetXY($x + 3, $pillY + 0.3);
            $pdf->Cell($strW, 3, $varStr, 0, 0, 'C');
            
            // Reset colors
            $pdf->SetDrawColor(213, 223, 233);
            $pdf->SetFillColor(255, 255, 255);
        };

        foreach ($kpis as $i => $kpi) {
            $kx = $x0 + ($i * ($kw + $gap));
            $drawKpiCard($pdf, $kx, $y, $kw, $kh, $kpi['label'], $kpi['val'], $kpi['unit'], $kpi['change'], $kpi['vs'], $kpi['suffix'], $kpi['high'], $baseIconPath . $kpi['icon']);
        }
        $y += $kh + 3;

        // ---- Total waste by destination (paired monthly stacked bars) ----
        $chartH = 44;
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.2);
        $pdf->RoundedRect($x0, $y, 190, $chartH, 1.6, '1111', 'D');
        $pdf->setInterFont('B', 7.5);
        mn_pdf_text($pdf, '#17375E');
        if (file_exists($baseIconPath . $icons['bar_chart'])) {
            $pdf->Image($baseIconPath . $icons['bar_chart'], $x0 + 3, $y + 2, 3.5, 3.5);
            $pdf->SetXY($x0 + 7.5, $y + 2);
            $pdf->Cell(86, 4, lang('total_waste_by_destination'), 0, 0, 'L');
        } else {
            $pdf->SetXY($x0 + 3, $y + 2);
            $pdf->Cell(90, 4, lang('total_waste_by_destination'), 0, 0, 'L');
        }
        $pdf->setInterFont('', 6);
        mn_pdf_text($pdf, '#9AA6B2');
        $pdf->SetXY($x0 + 93, $y + 2.2);
        $pdf->Cell(94, 3.5, 'Monthly, kg ' . "\xC2\xB7" . ' full ' . ($year - 1) . ' shown for context', 0, 0, 'R');

        $legend = array(array('Waste to energy', '#2D55A8'), array('Recycled', '#2C9C8A'), array('Landfill', '#E8A33D'));
        $pdf->setInterFont('', 5.3);
        $items = array();
        foreach ($legend as $l) {
            $items[] = array($l[0], $l[1], $pdf->GetStringWidth($l[0]) + 5.5);
        }
        $lx = $x0 + 190 - 3 - array_sum(array_column($items, 2));
        foreach ($items as $it) {
            list($lbl, $col, $iw) = $it;
            mn_pdf_fill($pdf, $col);
            $pdf->Rect($lx, $y + $chartH - 4.8, 2, 2, 'F');
            mn_pdf_text($pdf, '#4A5A6C');
            $pdf->SetXY($lx + 3, $y + $chartH - 5.4);
            $pdf->Cell($iw - 3, 3, $lbl, 0, 0, 'L');
            $lx += $iw;
        }

        $axisW = 10;
        $plotX = $x0 + 3 + $axisW;
        $plotY = $y + 7;
        $plotW = 190 - 6 - $axisW;
        $plotH = $chartH - 19;

        // Scale the axis off the current year only: a single bad/outlier value in the
        // prior year's historical data shouldn't stretch the axis and flatten every
        // other month. Prior-year bars are simply clipped to the plot height instead.
        $monthTotals = array();
        foreach ($allMonths as $m => $lbl) {
            if ($m <= $endMonth) {
                $mrCur = isset($monthly[$m]['wasteReport']) ? $monthly[$m]['wasteReport'] : array();
                $monthTotals[$m] = waste_pdf_num(waste_pdf_find($mrCur, 'Total waste generated (kg)')['current']);
            }
        }
        list($axisMin, $axisMax, $axisStep) = waste_trends_axis(0, !empty($monthTotals) ? max($monthTotals) : 1);

        mn_pdf_draw($pdf, '#EEF2F6');
        $pdf->SetLineWidth(0.15);
        $pdf->setInterFont('', 4.5);
        mn_pdf_text($pdf, '#9AA6B2');
        for ($v = $axisMin; $v <= $axisMax + ($axisStep / 2); $v += $axisStep) {
            $gy = $plotY + $plotH - (($v - $axisMin) / ($axisMax - $axisMin)) * $plotH;
            $pdf->Line($plotX, $gy, $plotX + $plotW, $gy);
            $pdf->SetXY($x0 + 2, $gy - 1.3);
            $pdf->Cell($axisW - 1, 2.6, number_format($v), 0, 0, 'R');
        }

        $n = count($allMonths);
        $monthSlot = $plotW / $n;
        $barW = min(5.5, $monthSlot * 0.32);
        $barGap = 1;

        $i = 0;
        foreach ($allMonths as $m => $lbl) {
            $mr = isset($monthly[$m]['wasteReport']) ? $monthly[$m]['wasteReport'] : array();
            $slotX = $plotX + ($i * $monthSlot) + ($monthSlot / 2);

            // previous year bar (always, faded)
            $pWte = waste_pdf_num(waste_pdf_find($mr, 'Waste To Energy')['previous']);
            $pRecy = waste_pdf_num(waste_pdf_find($mr, 'Recyclables (kg)')['previous']);
            $pTot = waste_pdf_num(waste_pdf_find($mr, 'Total waste generated (kg)')['previous']);
            $pLandfill = max(0, $pTot - $pWte - $pRecy);
            $pdf->SetAlpha(0.45);
            $bx = $slotX - $barGap - $barW;
            $yCursor = $plotY + $plotH;
            $remainingH = $plotH;
            foreach (array(array($pWte, '#2D55A8'), array($pRecy, '#2C9C8A'), array($pLandfill, '#E8A33D')) as $seg) {
                list($val, $col) = $seg;
                $segH = ($axisMax > 0) ? min($remainingH, ($val / $axisMax) * $plotH) : 0;
                if ($segH > 0.15) {
                    mn_pdf_fill($pdf, $col);
                    $pdf->Rect($bx, $yCursor - $segH, $barW, $segH, 'F');
                }
                $yCursor -= $segH;
                $remainingH -= $segH;
            }
            $pdf->SetAlpha(1);

            // current year bar (only up to the current month)
            if ($m <= $endMonth) {
                $cWte = waste_pdf_num(waste_pdf_find($mr, 'Waste To Energy')['current']);
                $cRecy = waste_pdf_num(waste_pdf_find($mr, 'Recyclables (kg)')['current']);
                $cTot = waste_pdf_num(waste_pdf_find($mr, 'Total waste generated (kg)')['current']);
                $cLandfill = max(0, $cTot - $cWte - $cRecy);
                $bx = $slotX + $barGap;
                $yCursor = $plotY + $plotH;
                foreach (array(array($cWte, '#2D55A8'), array($cRecy, '#2C9C8A'), array($cLandfill, '#E8A33D')) as $seg) {
                    list($val, $col) = $seg;
                    $segH = ($axisMax > 0) ? ($val / $axisMax) * $plotH : 0;
                    if ($segH > 0.15) {
                        mn_pdf_fill($pdf, $col);
                        $pdf->Rect($bx, $yCursor - $segH, $barW, $segH, 'F');
                    }
                    $yCursor -= $segH;
                }
            }

            $isCurrent = ($m == $endMonth);
            $pdf->setInterFont($isCurrent ? 'B' : '', 4.5);
            mn_pdf_text($pdf, ($m <= $endMonth) ? '#17375E' : '#9AA6B2');
            $pdf->SetXY($plotX + ($i * $monthSlot), $plotY + $plotH + 1.2);
            $pdf->Cell($monthSlot, 2.5, $lbl, 0, 0, 'C');

            // Year labels under each pair of bars ('25 / '26)
            $pdf->setInterFont('', 3.6);
            $pdf->SetXY($slotX - $barGap - $barW - 1, $plotY + $plotH + 3.8);
            mn_pdf_text($pdf, '#9AA6B2');
            $pdf->Cell($barW + 2, 2.2, (string)($year - 1), 0, 0, 'C');
            if ($m <= $endMonth) {
                $pdf->SetXY($slotX + $barGap - 1, $plotY + $plotH + 3.8);
                mn_pdf_text($pdf, '#2D55A8');
                $pdf->Cell($barW + 2, 2.2, (string)$year, 0, 0, 'C');
            }

            $i++;
        }
        $y += $chartH + 3;

        // ---- 2x2 grid of monthly trend mini-charts ----
        $seriesCur = array('rn' => array(), 'food' => array(), 'rate' => array(), 'diverted' => array());
        $seriesPrev = array('rn' => array(), 'food' => array(), 'rate' => array(), 'diverted' => array());
        foreach ($allMonths as $m => $lbl) {
            $mr = isset($monthly[$m]['wasteReport']) ? $monthly[$m]['wasteReport'] : array();
            $mg = isset($monthly[$m]['wastePerGuest']) ? $monthly[$m]['wastePerGuest'] : array();
            $rnRow = waste_pdf_find($mg, 'Waste (kg/Room night)');
            $rateRow = waste_pdf_find($mr, 'Recyclables Rate');
            $divRow = waste_pdf_find($mr, 'Waste diverted from landfill (%)');
            $organicRow = waste_pdf_find($mr, 'Organic / food waste (kg)');

            $seriesPrev['rn'][$m] = waste_pdf_num($rnRow['previous']);
            $seriesPrev['rate'][$m] = waste_pdf_num($rateRow['previous']);
            $seriesPrev['diverted'][$m] = waste_pdf_num($divRow['previous']);
            $rnPrevNight = isset($data['utility_cost_chart'][$m][$year - 1]['room_night']) ? (float) $data['utility_cost_chart'][$m][$year - 1]['room_night'] : 0;
            $seriesPrev['food'][$m] = $rnPrevNight > 0 ? round(waste_pdf_num($organicRow['previous']) / $rnPrevNight, 2) : 0;

            if ($m <= $endMonth) {
                $seriesCur['rn'][$m] = waste_pdf_num($rnRow['current']);
                $seriesCur['rate'][$m] = waste_pdf_num($rateRow['current']);
                $seriesCur['diverted'][$m] = waste_pdf_num($divRow['current']);
                $rnCurNight = isset($data['utility_cost_chart'][$m][$year]['room_night']) ? (float) $data['utility_cost_chart'][$m][$year]['room_night'] : 0;
                $seriesCur['food'][$m] = $rnCurNight > 0 ? round(waste_pdf_num($organicRow['current']) / $rnCurNight, 2) : 0;
            }
        }

        // "YTD target" line for waste/room night, same formula as the progress card on page 1
        $targetPerRn = null;
        if (!empty($data['ProgressTargetPercentage']['Waste'])) {
            $wv = $data['ProgressTargetPercentage']['Waste'];
            $wasteTargetTotal = isset($wv['TOTAL_WASTE_TARGET_YTD']) ? $wv['TOTAL_WASTE_TARGET_YTD']
                : ((!empty($wv['site_saving_target'])) ? ($wv['TOTAL_WASTE_BASELINE_YTD'] * (1 - ((float) $wv['site_saving_target'] / 100))) : $wv['TOTAL_WASTE_BASELINE_YTD']);
            $baseRn = isset($data['progress_baseline_roomnight_YTD']) ? (float) $data['progress_baseline_roomnight_YTD'] : 0;
            $targetPerRn = $baseRn > 0 ? ((float) $wasteTargetTotal / $baseRn) : null;
        }

        $cardsDef = array(
            array('key' => 'rn', 'title' => lang('waste_per_room_night'), 'sub' => 'kg/room night', 'dec' => 2, 'suffix' => '', 'target' => $targetPerRn, 'targetLabel' => 'Target'),
            array('key' => 'food', 'title' => lang('food_waste_per_room_night'), 'sub' => 'kg/RN', 'dec' => 2, 'suffix' => '', 'target' => null, 'targetLabel' => ''),
            array('key' => 'rate', 'title' => lang('recyclables_rate'), 'sub' => lang('recyclables_total_waste'), 'dec' => 1, 'suffix' => '%', 'target' => null, 'targetLabel' => ''),
            array('key' => 'diverted', 'title' => lang('waste_diverted_landfill'), 'sub' => lang('waste_diverted_all_except_landfill'), 'dec' => 0, 'suffix' => '%', 'target' => null, 'targetLabel' => ''),
        );
        $cardGap = 3;
        $cardW = (190 - $cardGap) / 2;
        $cardH = 38;
        foreach ($cardsDef as $ci => $cd) {
            $row = intdiv($ci, 2);
            $col = $ci % 2;
            $cx = $x0 + $col * ($cardW + $cardGap);
            $cy = $y + $row * ($cardH + $cardGap);
            waste_trends_mini_chart($pdf, $cx, $cy, $cardW, $cardH, $cd['title'], $cd['sub'], $allMonths, $endMonth, $seriesCur[$cd['key']], $seriesPrev[$cd['key']], $cd['dec'], $cd['suffix'], $cd['target'], $cd['targetLabel'], $year, $year - 1);
        }
        $y += (2 * $cardH) + $cardGap + 3;

        // ---- YTD summary table ----
        $tableRows = array(
            array('Total waste generated', 'kg', waste_pdf_find($wasteRows, 'Total waste generated (kg)'), 'change', false, $icons['waste_bin']),
            array('Waste', 'kg/room night', waste_pdf_find($guestRows, 'Waste (kg/Room night)'), 'value', false, $icons['room_night']),
            array('Recyclables rate', '%', waste_pdf_find($wasteRows, 'Recyclables Rate'), 'change', true, $icons['recycle']),
            array('Waste diverted from landfill', '%', waste_pdf_find($wasteRows, 'Waste diverted from landfill (%)'), 'change', true, $icons['diverted_truck']),
            array('Organic / food waste', 'kg', waste_pdf_find($wasteRows, 'Organic / food waste (kg)'), 'change', false, $icons['food_waste']),
            array('Hazardous waste', 'kg', waste_pdf_find($wasteRows, 'Hazardous waste (kg)'), 'change', false, $icons['hazardous']),
        );
        $rh = 7;
        $tableH = 8 + 6 + (count($tableRows) * $rh);
        $startY = $y;

        $pdf->setInterFont('B', 9);
        mn_pdf_text($pdf, '#17375E');
        if (file_exists($baseIconPath . $icons['calendar'])) {
            $pdf->Image($baseIconPath . $icons['calendar'], $x0 + 2, $startY + 2, 3.5, 3.5);
            $pdf->SetXY($x0 + 6.5, $startY + 2);
            $pdf->Cell(180, 4, 'Year to date, ' . $fullMonths[1] . ' ' . "\xE2\x80\x93" . ' ' . $fullMonths[$endMonth], 0, 0, 'L');
        } else {
            $pdf->SetXY($x0 + 2, $startY + 2);
            $pdf->Cell(185, 4, 'Year to date, ' . $fullMonths[1] . ' ' . "\xE2\x80\x93" . ' ' . $fullMonths[$endMonth], 0, 0, 'L');
        }
        $y += 8;
        
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.2);
        $pdf->Line($x0, $y, $x0 + 190, $y);

        $cols = array(78, 38, 38, 36);
        $heads = array('Metric', 'YTD ' . $year, 'YTD ' . ($year - 1), 'Variation');
        $hx = $x0;
        mn_pdf_fill($pdf, '#F4F7FA');
        // Fill header for columns 2 and 3
        $pdf->Rect($x0 + $cols[0], $y, $cols[1] + $cols[2], 6, 'F');
        // Fill header for column 4
        mn_pdf_fill($pdf, '#EEF8F1');
        $pdf->Rect($x0 + $cols[0] + $cols[1] + $cols[2], $y, $cols[3], 6, 'F');
        
        $pdf->setInterFont('B', 6.5);
        mn_pdf_text($pdf, '#4A5A6C');
        foreach ($heads as $hi => $head) {
            $pdf->SetXY($hx + 1.5, $y + 1.2);
            $pdf->Cell($cols[$hi] - 2, 4, $head, 0, 0, $hi === 0 ? 'L' : 'C');
            $hx += $cols[$hi];
        }
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.2);
        $pdf->Line($x0, $y + 6, $x0 + array_sum($cols), $y + 6);
        $y += 6;

        $last = count($tableRows) - 1;
        foreach ($tableRows as $ri => $tr) {
            list($name, $unit, $row, $changeKey, $highBetter, $icon) = array_pad($tr, 6, '');
            mn_pdf_fill($pdf, '#F4F7FA');
            $pdf->Rect($x0 + $cols[0], $y, $cols[1] + $cols[2], $rh, 'F');
            mn_pdf_fill($pdf, '#EEF8F1');
            $pdf->Rect($x0 + $cols[0] + $cols[1] + $cols[2], $y, $cols[3], $rh, 'F');

            $pdf->setInterFont('B', 7.3);
            mn_pdf_text($pdf, '#17375E');
            $labelX = $x0 + 2;
            if ($icon && file_exists($baseIconPath . $icon)) {
                $pdf->Image($baseIconPath . $icon, $labelX, $y + 1.5, 4, 4);
                $labelX += 6;
            }
            $pdf->SetXY($labelX, $y + 1.2);
            $pdf->Cell($cols[0] - 4 - ($labelX - ($x0 + 2)), 3.2, $name, 0, 0, 'L');
            $pdf->setInterFont('', 5.8);
            mn_pdf_text($pdf, '#6B7785');
            $pdf->SetXY($labelX, $y + 4.2);
            $pdf->Cell($cols[0] - 4 - ($labelX - ($x0 + 2)), 3, '(' . $unit . ')', 0, 0, 'L');

            $pdf->setInterFont('B', 7.5);
            mn_pdf_text($pdf, '#17375E');
            $pdf->SetXY($x0 + $cols[0], $y + 2);
            $pdf->Cell($cols[1], 4, $row['current'], 0, 0, 'C');
            $pdf->setInterFont('', 7.5);
            mn_pdf_text($pdf, '#4A5A6C');
            $pdf->SetXY($x0 + $cols[0] + $cols[1], $y + 2);
            $pdf->Cell($cols[2], 4, $row['previous'], 0, 0, 'C');

            $changeVal = isset($row[$changeKey]) ? $row[$changeKey] : '';
            $num = waste_pdf_num($changeVal);
            list($dir, $tone) = waste_pdf_tone($num, $highBetter);
            $chipText = ($unit === '%') ? (($num > 0 ? '+' : '') . $changeVal . ' pts') : (($num > 0 ? '+' : '') . $changeVal . '%');
            $chipW = $pdf->GetStringWidth($chipText) + ($dir === 'flat' ? 7 : 8.5);
            waste_pdf_chip($pdf, $x0 + $cols[0] + $cols[1] + $cols[2] + (($cols[3] - $chipW) / 2), $y + 1.6, $chipText, $tone, $dir);

            if ($ri < $last) {
                mn_pdf_draw($pdf, '#EEF2F6');
                $pdf->SetLineWidth(0.15);
                $pdf->Line($x0, $y + $rh, $x0 + array_sum($cols), $y + $rh);
            }
            $y += $rh;
        }
        
        mn_pdf_draw($pdf, '#D5DFE9');
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($x0, $startY, 190, $tableH, 1.6, '1111', 'D');
        
        $y += 3;

        $pdf->SetTextColor(0, 0, 0);
    }
}
