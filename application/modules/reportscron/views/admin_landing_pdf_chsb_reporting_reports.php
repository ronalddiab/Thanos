<?php
if (!function_exists('render_chsb_reporting_pdf')) {
    function chsb_pdf_rgb($hex)
    {
        $hex = ltrim($hex, '#');
        return array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
    }

    function chsb_pdf_spark($pdf, $x, $y, $w, $reading, $value, $dotHex)
    {
        $low = isset($reading['low']) ? (float) $reading['low'] : 0;
        $high = isset($reading['high']) ? (float) $reading['high'] : 0;
        $span = $high - $low;
        if ($span == 0 || $w <= 0) {
            return;
        }
        $map = function ($n) use ($x, $w, $low, $span) {
            $p = ((float) $n - $low) / $span;
            if ($p < 0) {
                $p = 0;
            }
            if ($p > 1) {
                $p = 1;
            }
            return $x + ($p * $w);
        };
        $mid = $y + 3.2;
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->SetLineWidth(0.45);
        $pdf->Line($x + 1.2, $mid, $x + $w - 1.2, $mid);
        $x1 = $map($reading['lower_quartile']);
        $x2 = $map($reading['upper_quartile']);
        if ($x2 < $x1) {
            $swap = $x1;
            $x1 = $x2;
            $x2 = $swap;
        }
        $bw = max(1.6, $x2 - $x1);
        $pdf->SetFillColor(220, 230, 240);
        $pdf->RoundedRect($x1, $mid - 1.35, $bw, 2.7, 0.7, '1111', 'F');
        $mx = $map($reading['median']);
        $pdf->SetDrawColor(74, 90, 108);
        $pdf->SetLineWidth(0.3);
        $pdf->Line($mx, $mid - 2.3, $mx, $mid + 2.3);
        if ($value !== null && $value !== '') {
            $dx = $map($value);
            $rgb = chsb_pdf_rgb($dotHex);
            $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
            $pdf->SetDrawColor(23, 55, 94);
            $pdf->SetLineWidth(0.25);
            $pdf->Circle($dx, $mid, 1.35, 0, 360, 'DF');
        }
    }

    function render_chsb_reporting_pdf($pdf, $data)
    {
        $measures = isset($data['measures']) ? $data['measures'] : array();
        $measure_readings = isset($data['measure_readings']) ? $data['measure_readings'] : array();
        if (empty($measures) || empty($measure_readings)) {
            return;
        }
        $site_detail = isset($data['site_detail']) ? $data['site_detail'] : array();
        $hotel_name = !empty($site_detail['site_location_name']) ? $site_detail['site_location_name'] : 'This hotel';
        $period = !empty($data['current_title']) ? $data['current_title'] : '';

        $chsb_stat_keys = array('low', 'lower_quartile', 'mean', 'median', 'upper_quartile', 'high');
        $chsb_stat_labels = array(
            'low' => "Low",
            'lower_quartile' => "Lower\nquartile",
            'mean' => 'Mean',
            'median' => 'Median',
            'upper_quartile' => "Upper\nquartile",
            'high' => 'High',
        );
        $chsb_rows = array(
            array('key' => 'HCMIRoomsFootprintPerOccupiedRoom', 'reading' => 1, 'decimals' => 0, 'unit' => 'kgCO₂e'),
            array('key' => 'HotelCarbonFootprintPerRoom', 'reading' => 2, 'decimals' => 0, 'unit' => 'kgCO₂e'),
            array('key' => 'HotelCarbonFootprintPerOccupiedRoom', 'reading' => 3, 'decimals' => 0, 'unit' => 'kgCO₂e'),
            array('key' => 'HotelCarbonFootprintPerSquareMeter', 'reading' => 4, 'decimals' => 0, 'unit' => 'kgCO₂e'),
            array('key' => 'HotelEnergyUsagePerOccupiedRoom', 'reading' => 6, 'decimals' => 0, 'unit' => 'kWh'),
            array('key' => 'HotelEnergyUsagePerSquareMeter', 'reading' => 7, 'decimals' => 0, 'unit' => 'kWh'),
            array('key' => 'HCMIMeetingFootprintPerMeetingHour', 'reading' => 9, 'decimals' => 2, 'unit' => 'kgCO₂e'),
            array('key' => 'HotelWaterUsagePerOccupiedRoom', 'reading' => 10, 'decimals' => 0, 'unit' => 'L'),
            array('key' => 'HotelWaterUsagePerSquareMeter', 'reading' => 11, 'decimals' => 0, 'unit' => 'L'),
            array('key' => 'HWMIRoomsWaterUsagePerOccupiedRoom', 'reading' => 13, 'decimals' => 0, 'unit' => 'L'),
            array('key' => 'HWMIMeetingWaterUsagePerMeetingHour', 'reading' => 14, 'decimals' => 2, 'unit' => 'L'),
            array('key' => 'RenewableEnergyPercentage', 'reading' => 15, 'decimals' => 2, 'unit' => '%', 'percent' => true, 'higher_better' => true),
            array('key' => 'RenewableElectricityPercentage', 'reading' => 16, 'decimals' => 2, 'unit' => '%', 'percent' => true, 'higher_better' => true),
            array('key' => 'ElectricityToNonElectricEnergy', 'reading' => 17, 'decimals' => 2, 'unit' => 'ratio', 'no_color' => true),
        );

        $left = 10;
        $pageW = 190;
        $y = 26;

        $pdf->SetXY($left, $y);
        $pdf->setInterFont( 'B', 8);
        $pdf->SetTextColor(240, 160, 75);
        $pdf->Cell($pageW, 4, 'BENCHMARKING', 0, 1, 'L');
        $pdf->SetX($left);
        $pdf->setInterFont( 'B', 14);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Cell($pageW, 7, 'Cornell Hotel Sustainability Benchmarking', 0, 1, 'L');
        $pdf->SetX($left);
        $pdf->setInterFont( '', 8);
        $pdf->SetTextColor(74, 90, 108);
        $pdf->Cell($pageW, 4, "Peer group statistics and this hotel's position. Lower is better except Measures 12-13.", 0, 1, 'L');
        $y = $pdf->GetY() + 2;

        // $period comes from $data['current_title'], which only parses as a clean "Month Year"
        // for the Monthly report ("Oct 2026"). YTD uses a "Current - 2026"-style title that
        // isn't a real date - rebuild a proper "Month Year" string from the report's own filters
        // instead, so the 12-month range below can still be computed (not just the bare year).
        if (!empty($data['is_ytd_report']) && !empty($data['filters']['max_month_id']) && !empty($data['filters']['current_year'])) {
            $chsbMonthNames = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
            $period = $chsbMonthNames[(int) $data['filters']['max_month_id']] . ' ' . $data['filters']['current_year'];
        } elseif (preg_match('/^[A-Za-z]+\s*-\s*(\d{4})$/', trim($period), $periodMatch)) {
            // 5-Year/Annual use the same "<word> - 2026" style title but have no equivalent
            // filters wired up here - fall back to just the year rather than the raw title.
            $period = $periodMatch[1];
        }
        $period_label = ($period !== '' ? $period : 'the report month');
        $range_label = '';
        $period_date = DateTime::createFromFormat('!F Y', $period_label);
        if ($period_date !== false) {
            $range_start = clone $period_date;
            $range_start->modify('-11 months');
            $range_label = ' (' . $range_start->format('M Y') . ' – ' . $period_date->format('M Y') . ')';
        }
        $note = '<span style="color:#17375E;font-weight:bold;">CHSB figures cover the 12 months ending ' . htmlspecialchars($period_label . $range_label) . '.</span> <span style="color:#4A5A6C;">They will not match the single-month figures on the earlier pages.</span>';
        $noteH = 9;
        $pdf->SetFillColor(255, 246, 234);
        $pdf->SetDrawColor(245, 211, 166);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($left, $y, $pageW, $noteH, 1.8, '1111', 'DF');
        $pdf->Image(site_url() . 'themes/default/images/waste/icon-calendar-navy.png', $left + 3, $y + 2.5, 4, 4, '', '', '', false, 300, '', false, false, 0);
        $pdf->writeHTMLCell($pageW - 11, $noteH - 2, $left + 9, $y + 1.6, $note, 0, 0, false, true, 'L');
        $y += $noteH + 2.5;

        $colW = array(48, 13, 16, 12, 13, 16, 12, 28, 32);
        $rowH = 9.2;
        $headH = 8;

        $pdf->SetFillColor(244, 247, 250);
        $pdf->Rect($left, $y, array_sum(array_slice($colW, 0, 7)) + $colW[8], $headH, 'F');
        $pdf->SetFillColor(227, 237, 247);
        $hx = $left;
        for ($i = 0; $i < 7; $i++) {
            $hx += $colW[$i];
        }
        $pdf->Rect($hx, $y, $colW[7], $headH, 'F');
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->Line($left, $y + $headH, $left + $pageW, $y + $headH);

        $heads = array('Measure', 'Low', "Lower\nquartile", 'Mean', 'Median', "Upper\nquartile", 'High', $hotel_name, 'Position in peer range');
        $x = $left;
        foreach ($heads as $i => $label) {
            $pdf->SetXY($x, $y + 0.6);
            $pdf->setInterFont( 'B', 6.5);
            $pdf->SetTextColor($i === 7 ? 23 : 74, $i === 7 ? 55 : 90, $i === 7 ? 94 : 108);
            $align = ($i === 0) ? 'L' : (($i >= 7) ? 'C' : 'R');
            $pdf->MultiCell($colW[$i], 3.2, $label, 0, $align, false, 0);
            $x += $colW[$i];
        }
        $y += $headH;

        $measure_no = 0;
        foreach ($chsb_rows as $chsb_row) {
            $reading_id = $chsb_row['reading'];
            if (empty($measure_readings[$reading_id])) {
                continue;
            }
            $measure_no++;
            $reading = $measure_readings[$reading_id];
            $decimals = $chsb_row['decimals'];
            $is_percent = !empty($chsb_row['percent']);
            $site_value = isset($measures[$chsb_row['key']]['chsb_value']) ? $measures[$chsb_row['key']]['chsb_value'] : null;
            $has_value = ($site_value !== null && $site_value !== '');
            if (empty($chsb_row['no_color'])) {
                $chsb_color = getChsbColor($has_value ? $site_value : null, $reading, !empty($chsb_row['higher_better']));
            } else {
                $chsb_color = array('background' => '#FFFFFF', 'font' => '#17375E');
            }
            $dotHex = !empty($chsb_row['no_color']) ? '#17375E' : $chsb_color['background'];
            if ($is_percent && $has_value) {
                $site_label = number_format(((float) $site_value) * 100, 2) . '%';
            } elseif ($has_value) {
                $site_label = number_format((float) $site_value, $decimals);
            } else {
                $site_label = 'Not enough data';
            }

            if ($measure_no % 2 === 0) {
                $pdf->SetFillColor(250, 252, 253);
                $pdf->Rect($left, $y, $pageW, $rowH, 'F');
            }
            $pdf->SetFillColor(247, 250, 253);
            $hotelX = $left + array_sum(array_slice($colW, 0, 7));
            $pdf->Rect($hotelX, $y, $colW[7], $rowH, 'F');
            $pdf->SetDrawColor(238, 242, 246);
            $pdf->SetLineWidth(0.15);
            $pdf->Line($left, $y + $rowH, $left + $pageW, $y + $rowH);

            $pdf->SetFillColor(23, 55, 94);
            $pdf->RoundedRect($left + 0.6, $y + 2.3, 4.6, 4.6, 1.0, '1111', 'F');
            $pdf->SetTextColor(255, 255, 255);
            $pdf->setInterFont( 'B', 7);
            $pdf->SetXY($left + 0.6, $y + 2.3);
            $pdf->Cell(4.6, 4.6, (string) $measure_no, 0, 0, 'C', false);

            $pdf->SetTextColor(23, 55, 94);
            $pdf->setInterFont( '', 6.2);
            $pdf->SetXY($left + 6, $y + 1.2);
            $measure_name = trim($reading['measure_name']);
            if (preg_match('/^(.*?)\s*(\([^)]+\))\s*$/u', $measure_name, $measure_parts)) {
                // Stored name already ends in "(unit)" - use that instead of appending another.
                $measure_name = trim($measure_parts[1]);
                $measure_unit = $measure_parts[2];
            } elseif (preg_match('/^(.*?)\s*(%)\s*$/u', $measure_name, $measure_parts)) {
                // Stored name ends in a bare "%" (e.g. "Renewable Electricity %") instead of
                // "(unit)" - strip it and format consistently with every other measure's
                // "(unit)" style, instead of appending "(%)" again on top of it.
                $measure_name = trim($measure_parts[1]);
                $measure_unit = '(' . $measure_parts[2] . ')';
            } else {
                $measure_unit = '(' . $chsb_row['unit'] . ')';
            }
            $measure_label = '<span style="color:#17375E;">' . htmlspecialchars($measure_name) . '</span> '
                . '<span style="color:#6B7785;font-size:5.5pt;">' . htmlspecialchars($measure_unit) . '</span>';
            $pdf->writeHTMLCell($colW[0] - 6.5, 6.5, $left + 6, $y + 1.2, $measure_label, 0, 0, false, true, 'L');

            $x = $left + $colW[0];
            $pdf->setInterFont( '', 6.5);
            $pdf->SetTextColor(23, 55, 94);
            foreach ($chsb_stat_keys as $si => $stat_key) {
                $cell_value = isset($reading[$stat_key]) ? $reading[$stat_key] : 0;
                $formatted = $is_percent
                    ? number_format($cell_value * 100, 2) . '%'
                    : number_format($cell_value, $decimals);
                $pdf->SetXY($x, $y + 2.6);
                $pdf->Cell($colW[$si + 1] - 0.4, 4, $formatted, 0, 0, 'R');
                $x += $colW[$si + 1];
            }

            $pdf->setInterFont( 'B', 6.5);
            $pillW = min($colW[7] - 1.5, $pdf->GetStringWidth($site_label) + 3.2);
            $pillH = 4.6;
            $pillX = $hotelX + (($colW[7] - $pillW) / 2);
            $pillY = $y + (($rowH - $pillH) / 2);
            $bg = chsb_pdf_rgb($chsb_color['background']);
            $fg = chsb_pdf_rgb($chsb_color['font']);
            $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
            if (!empty($chsb_row['no_color'])) {
                $pdf->SetDrawColor(213, 223, 233);
                $pdf->SetLineWidth(0.2);
                $pdf->RoundedRect($pillX, $pillY, $pillW, $pillH, 1.1, '1111', 'DF');
            } else {
                $pdf->RoundedRect($pillX, $pillY, $pillW, $pillH, 1.1, '1111', 'F');
            }
            $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
            $pdf->SetXY($pillX, $pillY);
            $pdf->Cell($pillW, $pillH, $site_label, 0, 0, 'C', false);

            $posX = $hotelX + $colW[7];
            chsb_pdf_spark($pdf, $posX + 1, $y + 1.2, $colW[8] - 2, $reading, $has_value ? $site_value : null, $dotHex);
            $y += $rowH;
        }

        $y += 2;
        $keyX = $left;
        chsb_pdf_spark($pdf, $keyX, $y, 36, array(
            'low' => 0, 'high' => 100, 'lower_quartile' => 28, 'upper_quartile' => 72, 'median' => 48,
        ), null, '#17375E');
        $pdf->SetXY($keyX + 38, $y + 1.4);
        $pdf->setInterFont( '', 7);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->Cell(150, 4, 'Peer range from low to high. Shaded box = lower to upper quartile. Tick = median. Dot = this hotel, coloured by band.', 0, 1, 'L');
        $y = $pdf->GetY() + 2;

        $legend = array(
            array('#006100', 'Better than every hotel in the peer group', '#63be7b', 'Top quartile — among the best 25%'),
            array('#c6e0b4', 'Second quartile — better than the peer median', '#bdd7ee', 'Exactly at the peer median'),
            array('#ffd966', 'Third quartile — worse than the peer median', '#f4b183', 'Bottom quartile — among the worst 25%'),
            array('#c00000', 'Worse than every hotel in the peer group', '#d9d9d9', 'Not enough data to calculate'),
        );
        $boxH = 22;
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($left, $y, $pageW, $boxH, 1.8, '1111', 'D');
        $ly = $y + 2;
        foreach ($legend as $pair) {
            $sw = 3.4;
            foreach (array(0, 2) as $side => $colorIndex) {
                $sx = $left + 3 + ($side * 96);
                $rgb = chsb_pdf_rgb($pair[$colorIndex]);
                $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
                $pdf->RoundedRect($sx, $ly, $sw, $sw, 0.7, '1111', 'F');
                $pdf->SetXY($sx + $sw + 1.5, $ly - 0.2);
                $pdf->setInterFont( '', 7);
                $pdf->SetTextColor(74, 90, 108);
                $pdf->Cell(86, $sw + 0.4, $pair[$colorIndex + 1], 0, 0, 'L');
            }
            $ly += 4.6;
        }
        $pdf->SetTextColor(0, 0, 0);
    }
}
