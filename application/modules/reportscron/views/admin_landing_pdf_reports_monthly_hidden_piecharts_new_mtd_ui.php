<?php
if (!function_exists('render_piecharts_new_pdf')) {
    function piecharts_pdf_num($value) {
        return (float) str_replace(array(',', '%', ' '), '', (string) $value);
    }
    
    function piecharts_format_compact($val) {
        if ($val >= 1000000) {
            return number_format($val / 1000000, 2) . 'm';
        } elseif ($val >= 1000) {
            return number_format($val / 1000, 0) . 'k';
        }
        return number_format($val);
    }
    
    function piecharts_pdf_tone($value, $highBetter) {
        if (abs($value) < 0.005) { return array('flat', 'neutral'); }
        $down = $value < 0;
        $good = $highBetter ? !$down : $down;
        return array($down ? 'down' : 'up', $good ? 'good' : 'bad');
    }
    
    function piecharts_pdf_chip($pdf, $x, $y, $text, $tone, $direction) {
        if ($tone === 'good') { $bg = array(230, 244, 234); $fg = array(46, 139, 87); } 
        elseif ($tone === 'bad') { $bg = array(253, 237, 237); $fg = array(200, 56, 47); } 
        else { $bg = array(241, 243, 244); $fg = array(95, 99, 104); }
        
        $pdf->setInterFont('B', 6);
        $w = $pdf->GetStringWidth($text) + ($direction === 'flat' ? 5 : 7);
        $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
        $pdf->RoundedRect($x, $y, $w, 4, 1, '1111', 'F');
        
        $pdf->SetFillColor($fg[0], $fg[1], $fg[2]);
        if ($direction === 'down') {
            $pdf->Polygon(array($x + 1.2, $y + 1, $x + 2.8, $y + 1, $x + 2, $y + 2.8), 'F');
            $tx = $x + 3.5;
        } elseif ($direction === 'up') {
            $pdf->Polygon(array($x + 1.2, $y + 2.8, $x + 2.8, $y + 2.8, $x + 2, $y + 1), 'F');
            $tx = $x + 3.5;
        } else {
            $tx = $x + 1.5;
        }
        
        $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
        $pdf->SetXY($tx, $y + 0.4);
        $pdf->Cell($w - ($tx - $x) - 0.5, 3.2, $text, 0, 0, 'L');
        $pdf->SetTextColor(0, 0, 0);
        return $w;
    }

    function piecharts_draw_progress_bar($pdf, $x, $y, $w, $h, $percent, $color) {
        $pdf->SetFillColor(238, 242, 246);
        $pdf->RoundedRect($x, $y, $w, $h, $h/2, '1111', 'F');
        if ($percent > 0) {
            $fillW = $w * ($percent / 100);
            if ($fillW < $h) $fillW = $h; // min width for rounded rect
            $pdf->SetFillColor($color[0], $color[1], $color[2]);
            $pdf->RoundedRect($x, $y, $fillW, $h, $h/2, '1111', 'F');
        }
    }

    function render_piecharts_new_pdf($pdf, $data) {
        // ---- INPUT DATA & PERIOD SETUP ----
        extract($data);
        
        $CI = &get_instance();
        if (isset($CI->monthly_utilities_vars)) {
            extract($CI->monthly_utilities_vars);
        }
        
        $isLocal = true;
        if (isset($currency) && $currency == "base") { $isLocal = false; }
        $currency_sym = $isLocal ? currency_symbol($isLocal) : CURRENCY_SYMBOL;
        
        $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
        
        $currMonth = isset($filters["report_month_piechart"]) ? $filters["report_month_piechart"] : date('n');
        $currYear = isset($filters["report_year_piechart"]) ? $filters["report_year_piechart"] : date('Y');
        if (!empty($is_ytd_report)) {
            // report_month_piechart/report_year_piechart come from a Monthly-report-only field
            // that a YTD request never sets, so they silently fell back to today's real calendar
            // date instead of the report's actual last-reported month - use the same
            // max_month_id/current_year every other YTD page already uses.
            $currMonth = isset($filters['max_month_id']) ? (int) $filters['max_month_id'] : $currMonth;
            $currYear = isset($filters['current_year']) ? (int) $filters['current_year'] : $currYear;
        }
        $currMonthName = $fullmontharray[$currMonth];
        $prevMonthName = $montharray[$currMonth]; // for chip
        $periodStr = $currMonthName . ' ' . $currYear;
        $prevYear = $currYear - 1;
        $prevPeriodStr = $prevMonthName . ' ' . $prevYear;
        if (!empty($is_ytd_report)) {
            // Data on this page is a Jan-to-currMonth YTD aggregate, not a single month - use the
            // same short "YTD <year>" wording as the Waste report page, everywhere this page
            // would otherwise show a single month name.
            $periodStr = 'YTD ' . $currYear;
            $prevPeriodStr = 'YTD ' . $prevYear;
            // $prevMonthName also feeds the per-utility bar labels below ("<value> - <this> <year>"),
            // which append their own year - so this must stay bare "YTD", not "YTD <year>".
            $prevMonthName = 'YTD';
        }
        // Kept as separate names for the cramped KPI-card labels/chips, which used to need a
        // shorter form than the (now also short, for YTD) page-level $periodStr/$prevPeriodStr.
        $cardPeriodStr = $periodStr;
        $cardPrevPeriodStr = $prevPeriodStr;

        // "Previous" data built by the controller (kwh/cost_pie_chart_previousmonth) is actually the
        // latest earlier MONTH within the same year with data, not the same month a year earlier - it
        // doesn't match this page's "vs {$prevPeriodStr}" captions. Build real same-month-prior-year
        // figures instead from $utility_cost_chart, which already holds both years correctly (and is
        // already used for room_night below), the same way admin_landing_pdf_reports_carbon_footprint_new.php does.
        if (!empty($is_ytd_report)) {
            // Every figure on this page (kwh_pie_chart/cost_pie_chart themselves, room_night
            // below) is a Jan-to-currMonth YTD sum - the prior-year comparison must be too, not
            // a single month, or the "vs YTD <year>" captions would compare a full-year total
            // against one month and produce a wildly inflated variance.
            $prevYearUtility = array();
            $sumFields = array('total_electricity_kwh', 'fuel_consumption', 'lpg_consumption', 'natural_gas_consumption', 'heating_district_consumption', 'cooling_district_consumption', 'electricity', 'fuel', 'lpg', 'lpg_fixed_cost', 'natural_gas', 'natural_gas_fixed_cost', 'heating_district', 'district_heating_fixed_cost', 'cooling_district', 'district_cooling_fixed_cost', 'water', 'water_fixed_cost');
            foreach ($sumFields as $sf) {
                $prevYearUtility[$sf] = 0;
            }
            for ($pyMonth = 1; $pyMonth <= $currMonth; $pyMonth++) {
                $monthRow = isset($utility_cost_chart[$pyMonth][$prevYear]) ? $utility_cost_chart[$pyMonth][$prevYear] : array();
                foreach ($sumFields as $sf) {
                    $prevYearUtility[$sf] += isset($monthRow[$sf]) ? (float) $monthRow[$sf] : 0;
                }
            }
        } else {
            $prevYearUtility = isset($utility_cost_chart[$currMonth][$prevYear]) ? $utility_cost_chart[$currMonth][$prevYear] : array();
        }
        $dataFactor = function_exists('getMmbtuFactorConversionAllUtility') ? getMmbtuFactorConversionAllUtility($site_id) : array();
        if (empty($dataFactor)) {
            $dataFactor = array('electricity' => 1, 'fuel_oil' => 1, 'lpg' => 1, 'natural_gas' => 1, 'district_heating' => 1, 'district_cooling' => 1);
        }
        $kwh_pie_chart_previousmonth = array(
            'electricity' => !empty($prevYearUtility['total_electricity_kwh']) ? $prevYearUtility['total_electricity_kwh'] * $dataFactor['electricity'] : 0,
            'fuel' => !empty($prevYearUtility['fuel_consumption']) ? $prevYearUtility['fuel_consumption'] * $dataFactor['fuel_oil'] : 0,
            'lpg' => !empty($prevYearUtility['lpg_consumption']) ? $prevYearUtility['lpg_consumption'] * $dataFactor['lpg'] : 0,
            'natural_gas' => !empty($prevYearUtility['natural_gas_consumption']) ? $prevYearUtility['natural_gas_consumption'] * $dataFactor['natural_gas'] : 0,
            'heating_district' => !empty($prevYearUtility['heating_district_consumption']) ? $prevYearUtility['heating_district_consumption'] * $dataFactor['district_heating'] : 0,
            'cooling_district' => !empty($prevYearUtility['cooling_district_consumption']) ? $prevYearUtility['cooling_district_consumption'] * $dataFactor['district_cooling'] : 0,
        );
        $cost_pie_chart_previousmonth = array(
            'electricity' => !empty($prevYearUtility['electricity']) ? $prevYearUtility['electricity'] : 0,
            'fuel' => !empty($prevYearUtility['fuel']) ? $prevYearUtility['fuel'] : 0,
            'lpg' => (!empty($prevYearUtility['lpg']) ? $prevYearUtility['lpg'] : 0) + (!empty($prevYearUtility['lpg_fixed_cost']) ? $prevYearUtility['lpg_fixed_cost'] : 0),
            'natural_gas' => (!empty($prevYearUtility['natural_gas']) ? $prevYearUtility['natural_gas'] : 0) + (!empty($prevYearUtility['natural_gas_fixed_cost']) ? $prevYearUtility['natural_gas_fixed_cost'] : 0),
            'heating_district' => (!empty($prevYearUtility['heating_district']) ? $prevYearUtility['heating_district'] : 0) + (!empty($prevYearUtility['district_heating_fixed_cost']) ? $prevYearUtility['district_heating_fixed_cost'] : 0),
            'cooling_district' => (!empty($prevYearUtility['cooling_district']) ? $prevYearUtility['cooling_district'] : 0) + (!empty($prevYearUtility['district_cooling_fixed_cost']) ? $prevYearUtility['district_cooling_fixed_cost'] : 0),
            'water' => (!empty($prevYearUtility['water']) ? $prevYearUtility['water'] : 0) + (!empty($prevYearUtility['water_fixed_cost']) ? $prevYearUtility['water_fixed_cost'] : 0),
        );

        // --- Calculate data for cards ---
        $kwh_pie_chart_utility_kwh_total = ($kwh_pie_chart['electricity'] + $kwh_pie_chart['fuel'] + $kwh_pie_chart['lpg'] + $kwh_pie_chart['natural_gas'] + $kwh_pie_chart['heating_district'] + $kwh_pie_chart['cooling_district']);
        $kwh_pie_chart_previousmonth_utility_kwh_total = ($kwh_pie_chart_previousmonth['electricity'] + $kwh_pie_chart_previousmonth['fuel'] + $kwh_pie_chart_previousmonth['lpg'] + $kwh_pie_chart_previousmonth['natural_gas'] + $kwh_pie_chart_previousmonth['heating_district'] + $kwh_pie_chart_previousmonth['cooling_district']);
        
        $cost_pie_chart_t_sum = ($cost_pie_chart['electricity'] + $cost_pie_chart['fuel'] + $cost_pie_chart['lpg'] + $cost_pie_chart['natural_gas'] + $cost_pie_chart['heating_district'] + $cost_pie_chart['cooling_district'] + $cost_pie_chart['water']);
        $cost_pie_chart_previousmonth_t_sum = ($cost_pie_chart_previousmonth['electricity'] + $cost_pie_chart_previousmonth['fuel'] + $cost_pie_chart_previousmonth['lpg'] + $cost_pie_chart_previousmonth['natural_gas'] + $cost_pie_chart_previousmonth['heating_district'] + $cost_pie_chart_previousmonth['cooling_district'] + $cost_pie_chart_previousmonth['water']);
        
        if (!empty($is_ytd_report)) {
            // Room nights must be the Jan-to-currMonth YTD sum here too, matching the already-YTD
            // $cost_pie_chart_t_sum above - using just the current single month (often 0, since
            // the latest month frequently has no room-night data entered yet) silently zeroed
            // out "cost per room night" for every YTD report.
            $room_night = 0;
            $pre_room_night = 0;
            for ($rnMonth = 1; $rnMonth <= $currMonth; $rnMonth++) {
                $room_night += isset($utility_cost_chart[$rnMonth][$currYear]['room_night']) ? (float) $utility_cost_chart[$rnMonth][$currYear]['room_night'] : 0;
                $pre_room_night += isset($utility_cost_chart[$rnMonth][$prevYear]['room_night']) ? (float) $utility_cost_chart[$rnMonth][$prevYear]['room_night'] : 0;
            }
        } else {
            $room_night = !empty($utility_cost_chart[$currMonth][$currYear]['room_night']) ? $utility_cost_chart[$currMonth][$currYear]['room_night'] : 0;
            $pre_room_night = !empty($utility_cost_chart[$currMonth][$prevYear]['room_night']) ? $utility_cost_chart[$currMonth][$prevYear]['room_night'] : 0;
        }
        
        $cost_per_room_night = ($room_night > 0) ? ($cost_pie_chart_t_sum / $room_night) : 0;
        $pre_cost_per_room_night = ($pre_room_night > 0) ? ($cost_pie_chart_previousmonth_t_sum / $pre_room_night) : 0;
        
        $site_area = isset($site_detail['site_builtup_area']) ? $site_detail['site_builtup_area'] : (isset($site_details['site_builtup_area']) ? $site_details['site_builtup_area'] : 0);
        $cost_per_m2 = ($site_area > 0) ? ($cost_pie_chart_t_sum / $site_area) : 0;
        $pre_cost_per_m2 = ($site_area > 0) ? ($cost_pie_chart_previousmonth_t_sum / $site_area) : 0;
        
        $baseIconPath = FCPATH . 'themes/default/images/waste/';
        $icons = array(
            'energy' => 'icon-energy-navy.png',
            'cost' => 'icon-cost-navy.png',
            'room_night' => 'icon-room-night-navy.png',
            'table' => 'icon-table-navy.png',
        );
        $kpis = array(
            array('label' => lang('energy_label_prefix') . ', ' . mb_strtoupper($cardPeriodStr, 'UTF-8'), 'value' => number_format($kwh_pie_chart_utility_kwh_total), 'unit' => 'kWh', 'current' => $kwh_pie_chart_utility_kwh_total, 'previous' => $kwh_pie_chart_previousmonth_utility_kwh_total, 'high' => false, 'icon' => $icons['energy']),
            array('label' => lang('utilities_cost_prefix') . ', ' . mb_strtoupper($cardPeriodStr, 'UTF-8'), 'value' => number_format($cost_pie_chart_t_sum), 'unit' => $currency_sym, 'current' => $cost_pie_chart_t_sum, 'previous' => $cost_pie_chart_previousmonth_t_sum, 'high' => false, 'icon' => $icons['cost']),
            array('label' => lang('cost_per_room_night_label'), 'value' => number_format($cost_per_room_night, 3), 'unit' => $currency_sym . '/RN', 'current' => $cost_per_room_night, 'previous' => $pre_cost_per_room_night, 'high' => false, 'icon' => $icons['room_night']),
            array('label' => lang('cost_per_m2_label'), 'value' => number_format($cost_per_m2, 3), 'unit' => $currency_sym . '/m²', 'current' => $cost_per_m2, 'previous' => $pre_cost_per_m2, 'high' => false, 'icon' => $icons['table']),
        );

        $pdf->AddPage();
        
        $x0 = 10;
        $y = 26;
        
        // --- Title Section ---
        $pdf->SetXY($x0, $y);
        $pdf->setInterFont('B', 7);
        $pdf->SetTextColor(242, 142, 43); // Orange
        $pdf->Cell(190, 4, lang('energy_cost_header'), 0, 1, 'L');
        $y += 4;
        
        $pdf->SetXY($x0, $y);
        $pdf->setInterFont('B', 16);
        $pdf->SetTextColor(17, 43, 73); // Dark blue
        $pdf->Cell(190, 7, lang('energy_cost_split'), 0, 1, 'L');
        $y += 7;
        
        $pdf->SetXY($x0, $y);
        $pdf->setInterFont('', 8);
        $pdf->SetTextColor(107, 119, 133); // Gray
        $pdf->Cell(190, 4, $periodStr, 0, 1, 'L');
        $y += 7;
        
        // --- Cards ---
        $gap = 2;
        $kw = (190 - (3 * $gap)) / 4;
        $kh = 22;
        
        foreach ($kpis as $i => $kpi) {
            $kx = $x0 + ($i * ($kw + $gap));
            
            $pdf->SetDrawColor(218, 225, 233);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->RoundedRect($kx, $y, $kw, $kh, 2, '1111', 'DF');
            
            $lblXOffset = 2;
            if (isset($kpi['icon']) && file_exists($baseIconPath . $kpi['icon'])) {
                $pdf->Image($baseIconPath . $kpi['icon'], $kx + 2, $y + 1, 3.5, 3.5);
                $lblXOffset = 6;
            }

            $pdf->setInterFont('B', 5.5);
            $pdf->SetTextColor(130, 140, 150);
            $pdf->SetXY($kx + $lblXOffset, $y + 1.5);
            $pdf->MultiCell($kw - 6, 3, $kpi['label'], 0, 'L');
            
            $pdf->setInterFont('B', 14);
            $pdf->SetTextColor(17, 43, 73);
            $pdf->SetXY($kx + 2, $y + 6);
            $pdf->Cell($kw - 4, 6, $kpi['value'], 0, 0, 'L');
            
            $pdf->setInterFont('', 6.5);
            $pdf->SetTextColor(130, 140, 150);
            $pdf->SetXY($kx + 2, $y + 12.5);
            $pdf->Cell($kw - 4, 3, $kpi['unit'], 0, 0, 'L');
            
            if ($i == 1 || $i == 2 || $i == 3) {
                if ($kpi['previous'] > 0) {
                    $change_val = (($kpi['current'] - $kpi['previous']) / $kpi['previous']) * 100;
                    $change_str = number_format(abs($change_val), 3);
                } else {
                    $change_val = 0;
                    $change_str = '0.000';
                }
                
                list($dir, $tone) = piecharts_pdf_tone($change_val, $kpi['high']);
                $chip = $change_str . '% vs ' . $cardPrevPeriodStr;
                piecharts_pdf_chip($pdf, $kx + 2, $y + 16, $chip, $tone, $dir);
            }
        }
        $y += $kh + 3;
        
        // --- Panels for Pie Charts and Tables ---
        $panelW = (190 - 4) / 2; // 93
        $px1 = $x0;
        $px2 = $x0 + $panelW + 4;
        
        // Chart layout dimensions
        $headerH = 8;
        $maxImgH = 42; 
        $imgOverlap = 0; 
        
        $utilities = array('electricity' => lang('electricity'), 'fuel' => lang('fuel'), 'lpg' => lang('lpg'), 'natural_gas' => lang('natural_gas'), 'heating_district' => lang('heating_district'), 'cooling_district' => lang('cooling_district'), 'water' => lang('water'));

        $colors = array(
            'electricity' => array(91, 155, 213),
            'fuel' => array(237, 125, 49),
            'lpg' => array(255, 192, 0),
            'natural_gas' => array(68, 114, 196),
            'heating_district' => array(255, 105, 180),
            'cooling_district' => array(37, 94, 145),
            'water' => array(112, 173, 71)
        );
        
        $leftRows = 0;
        $rightRows = 0;
        foreach ($utilities as $key => $name) {
            if ($key != 'water' && isset($kwh_pie_chart[$key]) && $kwh_pie_chart[$key] > 0) $leftRows++;
            if (isset($cost_pie_chart[$key]) && $cost_pie_chart[$key] > 0) $rightRows++;
        }
        
        $maxRows = max($leftRows, $rightRows);
        $rowH = 4.5;
        $tableHeight = 5 + ($maxRows * $rowH) + $rowH + 2; // header + rows + total + padding
        
        $panelH = $headerH + $maxImgH - $imgOverlap + $tableHeight + 6; 
        
        // Left Panel Background
        $pdf->SetDrawColor(218, 225, 233);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->RoundedRect($px1, $y, $panelW, $panelH, 2, '1111', 'DF');
        $pdf->Line($px1, $y + $headerH, $px1 + $panelW, $y + $headerH);
        
        // Right Panel Background
        $pdf->SetFillColor(255, 255, 255);
        $pdf->RoundedRect($px2, $y, $panelW, $panelH, 2, '1111', 'DF');
        $pdf->Line($px2, $y + $headerH, $px2 + $panelW, $y + $headerH);
        
        // Headers
        if (file_exists($baseIconPath . $icons['energy'])) {
            $pdf->Image($baseIconPath . $icons['energy'], $px1 + 2, $y + 2, 3.5, 3.5);
        }
        if (file_exists($baseIconPath . $icons['cost'])) {
            $pdf->Image($baseIconPath . $icons['cost'], $px2 + 2, $y + 2, 3.5, 3.5);
        }
        $pdf->setInterFont('B', 7);
        $pdf->SetTextColor(17, 43, 73);
        $pdf->SetXY($px1 + 6, $y + 2);
        $pdf->Cell($panelW - 8, 4, 'Energy consumption (kWh) – ' . $periodStr, 0, 0, 'L');
        
        $pdf->SetXY($px2 + 6, $y + 2);
        $pdf->Cell($panelW - 8, 4, 'Utilities cost – ' . $periodStr, 0, 0, 'L');
        
        $imgY = $y + $headerH + 2;
        
        // Keep image code as backup (commented)
        /*
        $imgW = 84;
        $imgData1 = ''; $imgData2 = '';
        if (!empty($pieChartImgkwhMonthly)) { $imgData1 = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $pieChartImgkwhMonthly)); }
        if (!empty($pieChartImgcostMonthly)) { $imgData2 = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $pieChartImgcostMonthly)); }
        if (!empty($imgData1)) { $pdf->Image('@' . $imgData1, $px1 + ($panelW - $imgW)/2, $imgY, $imgW, 0, '', '', '', false, 300, '', false, false, 0, false, false, false); }
        if (!empty($imgData2)) { $pdf->Image('@' . $imgData2, $px2 + ($panelW - $imgW)/2, $imgY, $imgW, 0, '', '', '', false, 300, '', false, false, 0, false, false, false); }
        */

        // Draw Native Donut Charts instead of Highchart images
        $r = 17;
        $cx1 = $px1 + 28;
        $cy1 = $imgY + 3 + $r;
        $cx2 = $px2 + 28;
        $cy2 = $imgY + 3 + $r;
        
        $legendX1 = $px1 + 52;
        $legendX2 = $px2 + 52;

        $pdf->SetDrawColor(255, 255, 255);
        $pdf->SetLineWidth(0.35);

        // --- Left Donut (Energy) ---
        if ($kwh_pie_chart_utility_kwh_total > 0) {
            $currentAngle = 270;
            $legY1 = $cy1 - (($leftRows * 4.5) / 2);
            
            foreach ($utilities as $key => $name) {
                if ($key != 'water' && isset($kwh_pie_chart[$key]) && $kwh_pie_chart[$key] > 0) {
                    $color = isset($colors[$key]) ? $colors[$key] : array(100,100,100);
                    $pdf->SetFillColor($color[0], $color[1], $color[2]);
                    $val = $kwh_pie_chart[$key];
                    $angle = ($val / $kwh_pie_chart_utility_kwh_total) * 360;
                    if ($angle > 0) {
                        if ($angle > 359.9) {
                            $pdf->Circle($cx1, $cy1, $r, 0, 360, 'DF');
                        } else {
                            $endAngle = $currentAngle + $angle;
                            $pdf->PieSector($cx1, $cy1, $r, $currentAngle, $endAngle, 'DF');
                            $currentAngle = $endAngle;
                        }
                    }
                    // Legend
                    $pdf->SetFillColor($color[0], $color[1], $color[2]);
                    $pdf->RoundedRect($legendX1, $legY1 + 1, 2.5, 2.5, 0.5, '1111', 'F');
                    $pdf->setInterFont('', 6.5);
                    $pdf->SetTextColor(107, 119, 133);
                    $pdf->SetXY($legendX1 + 4, $legY1 + 0.5);
                    $pdf->Cell(35, 4, $name, 0, 0, 'L');
                    $legY1 += 4.5;
                }
            }
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Circle($cx1, $cy1, $r * 0.72, 0, 360, 'DF');
            
            $pdf->setInterFont('B', 9);
            $pdf->SetTextColor(17, 43, 73);
            $totalStr = piecharts_format_compact($kwh_pie_chart_utility_kwh_total);
            $tw = $pdf->GetStringWidth($totalStr);
            $pdf->SetXY($cx1 - ($tw/2), $cy1 - 2.5);
            $pdf->Cell($tw, 4, $totalStr, 0, 0, 'C');
            
            $pdf->setInterFont('', 6);
            $pdf->SetTextColor(107, 119, 133);
            $unitStr = 'kWh';
            $tw2 = $pdf->GetStringWidth($unitStr);
            $pdf->SetXY($cx1 - ($tw2/2), $cy1 + 1.5);
            $pdf->Cell($tw2, 3, $unitStr, 0, 0, 'C');
        }

        // --- Right Donut (Cost) ---
        if ($cost_pie_chart_t_sum > 0) {
            $currentAngle = 270;
            $legY2 = $cy2 - (($rightRows * 4.5) / 2);
            
            foreach ($utilities as $key => $name) {
                if (isset($cost_pie_chart[$key]) && $cost_pie_chart[$key] > 0) {
                    $color = isset($colors[$key]) ? $colors[$key] : array(100,100,100);
                    $pdf->SetFillColor($color[0], $color[1], $color[2]);
                    $val = $cost_pie_chart[$key];
                    $angle = ($val / $cost_pie_chart_t_sum) * 360;
                    if ($angle > 0) {
                        if ($angle > 359.9) {
                            $pdf->Circle($cx2, $cy2, $r, 0, 360, 'DF');
                        } else {
                            $endAngle = $currentAngle + $angle;
                            $pdf->PieSector($cx2, $cy2, $r, $currentAngle, $endAngle, 'DF');
                            $currentAngle = $endAngle;
                        }
                    }
                    // Legend
                    $pdf->SetFillColor($color[0], $color[1], $color[2]);
                    $pdf->RoundedRect($legendX2, $legY2 + 1, 2.5, 2.5, 0.5, '1111', 'F');
                    $pdf->setInterFont('', 6.5);
                    $pdf->SetTextColor(107, 119, 133);
                    $pdf->SetXY($legendX2 + 4, $legY2 + 0.5);
                    $pdf->Cell(35, 4, $name, 0, 0, 'L');
                    $legY2 += 4.5;
                }
            }
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Circle($cx2, $cy2, $r * 0.7, 0, 360, 'DF');
            
            $pdf->setInterFont('B', 9);
            $pdf->SetTextColor(17, 43, 73);
            $totalStr = piecharts_format_compact($cost_pie_chart_t_sum);
            $tw = $pdf->GetStringWidth($totalStr);
            $pdf->SetXY($cx2 - ($tw/2), $cy2 - 2.5);
            $pdf->Cell($tw, 4, $totalStr, 0, 0, 'C');
            
            $pdf->setInterFont('', 6);
            $pdf->SetTextColor(107, 119, 133);
            $unitStr = $currency_sym;
            $tw2 = $pdf->GetStringWidth($unitStr);
            $pdf->SetXY($cx2 - ($tw2/2), $cy2 + 1.5);
            $pdf->Cell($tw2, 3, $unitStr, 0, 0, 'C');
        }
        
        $tableY = $imgY + $maxImgH - $imgOverlap;
        
        // Tables
        $pdf->SetFillColor(247, 249, 251);
        
        // Header Left
        $pdf->Rect($px1, $tableY, $panelW, 5, 'F');
        if (file_exists($baseIconPath . $icons['table'])) {
            $pdf->Image($baseIconPath . $icons['table'], $px1 + 1, $tableY + 1, 3, 3);
        }
        $pdf->setInterFont('B', 6);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->SetXY($px1 + 5, $tableY + 1);
        $pdf->Cell(32, 3, 'Utility', 0, 0, 'L');
        $pdf->Cell(25, 3, lang('consumption_kwh_label'), 0, 0, 'R');
        $pdf->Cell(28, 3, '% share', 0, 0, 'C');
        
        // Header Right
        $pdf->Rect($px2, $tableY, $panelW, 5, 'F');
        if (file_exists($baseIconPath . $icons['table'])) {
            $pdf->Image($baseIconPath . $icons['table'], $px2 + 1, $tableY + 1, 3, 3);
        }
        $pdf->SetXY($px2 + 5, $tableY + 1);
        $pdf->Cell(32, 3, 'Utility', 0, 0, 'L');
        $pdf->Cell(25, 3, 'Cost (' . $currency_sym . ')', 0, 0, 'R');
        $pdf->Cell(28, 3, '% share', 0, 0, 'C');
        
        $rowY = $tableY + 6;
        
        $leftY = $rowY;
        $rightY = $rowY;
        
        $pdf->setInterFont('', 7);
        $pdf->SetTextColor(17, 43, 73);
        
        foreach ($utilities as $key => $name) {
            $color = isset($colors[$key]) ? $colors[$key] : array(100, 100, 100);
            
            // Left Table
            if ($key != 'water' && isset($kwh_pie_chart[$key]) && $kwh_pie_chart[$key] > 0) {
                $pdf->SetFillColor($color[0], $color[1], $color[2]);
                $pdf->RoundedRect($px1 + 2, $leftY + 1, 2.5, 2.5, 0.5, '1111', 'F');
                $pdf->setInterFont('', 7);
                $pdf->SetXY($px1 + 6, $leftY + 0.5);
                $pdf->Cell(30, 4, $name, 0, 0, 'L');
                $pdf->SetXY($px1 + 36, $leftY + 0.5);
                $pdf->Cell(26, 4, number_format(round($kwh_pie_chart[$key], 0)), 0, 0, 'R');
                $share1 = ($kwh_pie_chart_utility_kwh_total > 0) ? round(($kwh_pie_chart[$key] * 100) / $kwh_pie_chart_utility_kwh_total, 1) : 0;
                $pdf->SetXY($px1 + 64, $leftY + 0.5);
                $pdf->Cell(12, 4, number_format($share1, 1) . '%', 0, 0, 'R');
                piecharts_draw_progress_bar($pdf, $px1 + 78, $leftY + 1.5, 12, 1.5, $share1, $color);
                
                $pdf->SetDrawColor(240, 242, 245);
                $pdf->Line($px1 + 2, $leftY + $rowH, $px1 + $panelW - 2, $leftY + $rowH);
                $leftY += $rowH;
            }
            
            // Right Table
            if (isset($cost_pie_chart[$key]) && $cost_pie_chart[$key] > 0) {
                $pdf->SetFillColor($color[0], $color[1], $color[2]);
                $pdf->RoundedRect($px2 + 2, $rightY + 1, 2.5, 2.5, 0.5, '1111', 'F');
                $pdf->SetXY($px2 + 6, $rightY + 0.5);
                $pdf->Cell(30, 4, $name, 0, 0, 'L');
                $pdf->SetXY($px2 + 36, $rightY + 0.5);
                $pdf->Cell(26, 4, number_format($cost_pie_chart[$key]), 0, 0, 'R');
                $share2 = ($cost_pie_chart_t_sum > 0) ? round(($cost_pie_chart[$key] * 100) / $cost_pie_chart_t_sum, 1) : 0;
                $pdf->SetXY($px2 + 64, $rightY + 0.5);
                $pdf->Cell(12, 4, number_format($share2, 1) . '%', 0, 0, 'R');
                piecharts_draw_progress_bar($pdf, $px2 + 78, $rightY + 1.5, 12, 1.5, $share2, $color);
                
                $pdf->SetDrawColor(240, 242, 245);
                $pdf->Line($px2 + 2, $rightY + $rowH, $px2 + $panelW - 2, $rightY + $rowH);
                $rightY += $rowH;
            }
        }
        
        // Align totals
        $maxFinalY = max($leftY, $rightY);
        $leftY = $maxFinalY;
        $rightY = $maxFinalY;
        
        // Left Total
        $leftY += 1;
        $pdf->setInterFont('B', 7);
        $pdf->SetXY($px1 + 2, $leftY);
        $pdf->Cell(30, 4, 'Total', 0, 0, 'L');
        $pdf->SetXY($px1 + 36, $leftY);
        $pdf->Cell(26, 4, number_format(round($kwh_pie_chart_utility_kwh_total, 0)), 0, 0, 'R');
        $pdf->SetXY($px1 + 64, $leftY);
        $pdf->Cell(12, 4, '100%', 0, 0, 'R');
        
        // Right Total
        $rightY += 1;
        $pdf->SetXY($px2 + 2, $rightY);
        $pdf->Cell(30, 4, 'Total', 0, 0, 'L');
        $pdf->SetXY($px2 + 36, $rightY);
        $pdf->Cell(26, 4, number_format($cost_pie_chart_t_sum), 0, 0, 'R');
        $pdf->SetXY($px2 + 64, $rightY);
        $pdf->Cell(12, 4, '100%', 0, 0, 'R');
        
        $y += $panelH + 4;
        
        // --- Full width Native Bar Chart ---
        $chart_data = array();
        $max_val = 0;
        
        $budget_keys = array(
            'electricity' => 'total_electricity_cost_budget',
            'fuel' => 'total_fuel_oil_cost_budget',
            'lpg' => 'total_lpg_cost_budget',
            'natural_gas' => 'total_natural_gas_cost_budget',
            'heating_district' => 'district_heating_cost_budget',
            'cooling_district' => 'district_cooling_cost_budget',
            'water' => 'water_total_consumption_cost_budget'
        );
        
        foreach ($utilities as $key => $name) {
            $c = isset($cost_pie_chart[$key]) ? $cost_pie_chart[$key] : 0;
            $p = isset($cost_pie_chart_previousmonth[$key]) ? $cost_pie_chart_previousmonth[$key] : 0;
            
            $b_key = isset($budget_keys[$key]) ? $budget_keys[$key] : '';
            $b = ($b_key && isset($currentBudgetActualData[$b_key])) ? $currentBudgetActualData[$b_key] : 0;
            
            if ($c > 0 || $p > 0 || $b > 0) {
                $color = isset($colors[$key]) ? $colors[$key] : array(100, 100, 100);
                $chart_data[$key] = array('name' => $name, 'c' => $c, 'p' => $p, 'b' => $b, 'color' => $color);
                $max_val = max($max_val, $c, $p, $b);
            }
        }
        
        if (count($chart_data) > 0) {
            $numItems = count($chart_data);
            $barGroupH = 12;
            $chartH = $headerH + 8 + ($numItems * $barGroupH) + 10;
            
            if ($y + $chartH > 280) {
                $pdf->AddPage();
                $y = 10;
            }
            
            // Panel Background
            $pdf->SetDrawColor(218, 225, 233);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->RoundedRect($x0, $y, 190, $chartH, 2, '1111', 'DF');
            $pdf->Line($x0, $y + $headerH, $x0 + 190, $y + $headerH);
            
            // Header
            if (file_exists($baseIconPath . $icons['cost'])) {
                $pdf->Image($baseIconPath . $icons['cost'], $x0 + 2, $y + 2, 3.5, 3.5);
            }
            $pdf->setInterFont('B', 7);
            $pdf->SetTextColor(17, 43, 73);
            $pdf->SetXY($x0 + 6, $y + 2);
            $pdf->Cell(180, 4, 'Cost by utility – ' . $periodStr . ' vs ' . $prevPeriodStr . ' and budget', 0, 0, 'L');
            
            // Legend
            $legY = $y + $headerH + 2;
            $legX = $x0 + 55;
            
            // Current
            $pdf->SetFillColor(17, 43, 73);
            $pdf->RoundedRect($legX, $legY + 1.5, 3, 3, 0.5, '1111', 'F');
            $pdf->setInterFont('', 6);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->SetXY($legX + 4, $legY + 1);
            $pdf->Cell(20, 4, 'Current Year', 0, 0, 'L');
            $legX += 25;
            
            // Previous
            $pdf->SetFillColor(150, 160, 170);
            $pdf->RoundedRect($legX, $legY + 1.5, 3, 3, 0.5, '1111', 'F');
            $pdf->SetXY($legX + 4, $legY + 1);
            $pdf->Cell(20, 4, 'Previous Year', 0, 0, 'L');
            $legX += 25;
            
            // Budget
            $pdf->SetDrawColor(107, 119, 133);
            $pdf->SetLineStyle(array('dash' => 1, 'color' => array(107, 119, 133)));
            $pdf->SetFillColor(255, 255, 255);
            $pdf->RoundedRect($legX, $legY + 1.5, 3, 3, 0.5, '1111', 'DF');
            $pdf->SetLineStyle(array('dash' => 0, 'color' => array(218, 225, 233))); // reset
            $pdf->SetXY($legX + 4, $legY + 1);
            $pdf->Cell(20, 4, 'Budget', 0, 0, 'L');
            
            // Axes
            $chartY = $legY + 6;
            $chartX = $x0 + 30;
            $chartW = 150;
            
            // Calculate scale
            $scale_step = pow(10, floor(log10(max($max_val, 1))));
            if ($scale_step == 0) $scale_step = 1;
            $scale_max = ceil($max_val / $scale_step) * $scale_step;
            if ($scale_max == 0) $scale_max = 1;
            // When the longest bar would land too close to the scale max, its value label (drawn
            // right after the bar) has nowhere to go before the panel edge and overflows. Bump the
            // scale up by one more step so every bar leaves room for its label.
            if ($max_val > 0 && ($max_val / $scale_max) > 0.82) {
                $scale_max += $scale_step;
            }
            
            $pdf->SetDrawColor(240, 242, 245);
            for ($i = 0; $i <= 5; $i++) {
                $vx = $chartX + ($chartW * ($i / 5));
                $pdf->Line($vx, $chartY, $vx, $chartY + ($numItems * $barGroupH));
                
                $pdf->setInterFont('', 6);
                $pdf->SetTextColor(130, 140, 150);
                $valStr = piecharts_format_compact($scale_max * ($i / 5));
                $tw = $pdf->GetStringWidth($valStr);
                $pdf->SetXY($vx - ($tw/2), $chartY + ($numItems * $barGroupH) + 1);
                $pdf->Cell($tw, 4, $valStr, 0, 0, 'C');
            }
            
            $currY = $chartY + 2;
            foreach ($chart_data as $key => $d) {
                $pdf->setInterFont('', 7);
                $pdf->SetTextColor(17, 43, 73);
                $pdf->SetXY($x0 + 2, $currY + 2.5);
                $pdf->Cell(26, 4, $d['name'], 0, 0, 'R');
                
                $barH = 2.5;
                $gap = 1.2;
                
                $pdf->setInterFont('', 5.5);
                
                // Current
                $w_c = ($d['c'] / $scale_max) * $chartW;
                if ($w_c > 0) {
                    $pdf->SetFillColor($d['color'][0], $d['color'][1], $d['color'][2]);
                    $pdf->RoundedRect($chartX, $currY, $w_c, $barH, 0.5, '1111', 'F');
                    
                    $pdf->SetTextColor(17, 43, 73);
                    $pdf->SetXY($chartX + $w_c + 1, $currY - 0.2);
                    $pdf->Cell(30, $barH, number_format($d['c']) . ' - ' . $prevMonthName . ' ' . $currYear, 0, 0, 'L');
                }
                $currY += $barH + $gap;
                
                // Previous
                $w_p = ($d['p'] / $scale_max) * $chartW;
                if ($w_p > 0) {
                    $r_light = ($d['color'][0] + 255) / 2;
                    $g_light = ($d['color'][1] + 255) / 2;
                    $b_light = ($d['color'][2] + 255) / 2;
                    $pdf->SetFillColor($r_light, $g_light, $b_light);
                    $pdf->RoundedRect($chartX, $currY, $w_p, $barH, 0.5, '1111', 'F');
                    
                    $pdf->SetTextColor(107, 119, 133);
                    $pdf->SetXY($chartX + $w_p + 1, $currY - 0.2);
                    $pdf->Cell(30, $barH, number_format($d['p']) . ' - ' . $prevMonthName . ' ' . $prevYear, 0, 0, 'L');
                }
                $currY += $barH + $gap;
                
                // Budget
                $w_b = ($d['b'] / $scale_max) * $chartW;
                if ($w_b > 0) {
                    $pdf->SetDrawColor($d['color'][0], $d['color'][1], $d['color'][2]);
                    $pdf->SetLineStyle(array('dash' => '1.5,1.5', 'color' => array($d['color'][0], $d['color'][1], $d['color'][2])));
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->RoundedRect($chartX, $currY, $w_b, $barH, 0.5, '1111', 'DF');
                    $pdf->SetLineStyle(array('dash' => 0, 'color' => array(240, 242, 245)));
                    
                    $pdf->SetTextColor(107, 119, 133);
                    $pdf->SetXY($chartX + $w_b + 1, $currY - 0.2);
                    $pdf->Cell(30, $barH, number_format($d['b']) . ' - Budget', 0, 0, 'L');
                }
                $currY += $barH + $gap + 1.5;
            }
        }
        
    }
}
?>
