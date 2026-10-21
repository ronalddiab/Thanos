<?php
// Carbon Footprint Native TCPDF Version

if (!function_exists('render_carbon_footprint_new_pdf')) {

    function render_carbon_footprint_new_pdf($pdf, $data) {
        // ---- INPUT DATA ----
        // Safe extract variables
        $filters = isset($data['filters']) ? $data['filters'] : array();
        $utility_cost_chart = isset($data['utility_cost_chart']) ? $data['utility_cost_chart'] : array();
        $utility_cost_chart_pre = isset($data['utility_cost_chart_pre']) ? $data['utility_cost_chart_pre'] : array();
        $site_detail = isset($data['site_detail']) ? $data['site_detail'] : array();
        $site_id = isset($data['site_id']) ? $data['site_id'] : 0;
        $is_monthly = isset($data['is_monthly']) ? $data['is_monthly'] : false;
        $is_ytd_report = isset($data['is_ytd_report']) ? $data['is_ytd_report'] : false;

        $dataFactor = function_exists('getMmbtuFactorConversionAllUtility') ? getMmbtuFactorConversionAllUtility($site_id) : array();
        // Fallback factors if missing
        if (empty($dataFactor)) {
            $dataFactor = array('electricity' => 1, 'fuel_oil' => 1, 'lpg' => 1, 'natural_gas' => 1, 'district_heating' => 1, 'district_cooling' => 1);
        }

        $montharray = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        $fullmontharray = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

        // ---- REPORT PERIOD ----
        $CURRENT_YEAR_MAX_MONTH_ID = isset($data['CURRENT_YEAR_MAX_MONTH_ID']) ? $data['CURRENT_YEAR_MAX_MONTH_ID'] : date('m');

        if(date('m') == 1) {
            $current_year = date('Y') - 1;
            $last_year    = $current_year - 1;
        } else {
            $current_year = date('Y');
            $last_year    = $current_year - 1;
        }

        if (isset($filters['filters_comparision_chart'])) {
            $start_month = $filters['filters_comparision_chart']['start_month'];
            $end_month = isset($filters['filters_comparision_chart']['end_month']) ? $filters['filters_comparision_chart']['end_month'] : date('m');
            $filter_year = $filters['filters_comparision_chart']['start_year'];
        } else {
            $start_month = 1;
            $end_month = date('m');
            $filter_year = $current_year;
        }

        $is_monthly = ($start_month == $end_month);

        $resultkeys = array();
        $startmonthsarray = array();
        
        // For the chart, we always want to show from Jan to the current month if it's a monthly report.
        $chart_start_month = $is_monthly ? 1 : $start_month;
        $chart_end_month = $is_monthly ? $start_month : $CURRENT_YEAR_MAX_MONTH_ID;
        
        for ($i = $chart_start_month; $i <= $chart_end_month; $i++) {
            $startmonthsarray[] = $i;
        }
        $resultkeys[$filter_year] = $startmonthsarray;

        // --- Accumulators ---
        // (Copy exact accumulation logic from original file)
        $total_sum_pre_data_electricity = 0; $total_sum_pre_data_fuel = 0; $total_sum_pre_data_lpg = 0;
        $total_sum_pre_data_natural_gas = 0; $total_sum_pre_data_heating_district = 0; $total_sum_pre_data_cooling_district = 0;
        $total_sum_pre_data_fleet_petrol = 0; $total_sum_pre_data_occupancy = 0; $total_sum_pre_data_guest_night = 0;
        
        $total_sum_data_electricity = 0; $total_sum_data_fuel = 0; $total_sum_data_lpg = 0;
        $total_sum_data_natural_gas = 0; $total_sum_data_heating_district = 0; $total_sum_data_cooling_district = 0;
        $total_sum_data_fleet_petrol = 0; $total_sum_data_occupancy = 0; $total_sum_data_guest_night = 0;

        $chartData = array();

        // ---- MONTHLY EMISSION CALCULATION (drives both the chart and the table totals) ----
        foreach ($resultkeys as $year => $value) {
            foreach ($value as $month) {
                $prevYear = $year - 1;

                // PREVIOUS YEAR raw consumption (same fields/formulas as admin_landing_pdf_reports_carbon_footprint.php)
                $pre_data_electricity = !empty($utility_cost_chart_pre[$month][$prevYear]['total_electricity_kwh']) ? max(0, floatval($utility_cost_chart_pre[$month][$prevYear]['total_electricity_kwh']) - floatval($utility_cost_chart_pre[$month][$prevYear]['onsite_generator']) - floatval($utility_cost_chart_pre[$month][$prevYear]['renewable_energy'])) : 0;
                $pre_data_fuel = !empty($utility_cost_chart_pre[$month][$prevYear]['fuel_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['fuel_consumption']) : 0;
                $pre_data_lpg  = !empty($utility_cost_chart_pre[$month][$prevYear]['lpg_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['lpg_consumption']) : 0;
                $pre_data_natural_gas  = !empty($utility_cost_chart_pre[$month][$prevYear]['natural_gas_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['natural_gas_consumption']) : 0;
                $pre_data_heating_district = !empty($utility_cost_chart_pre[$month][$prevYear]['heating_district_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['heating_district_consumption']) : 0;
                $pre_data_cooling_district = !empty($utility_cost_chart_pre[$month][$prevYear]['cooling_district_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['cooling_district_consumption']) : 0;
                $pre_data_fleet_petrol = !empty($utility_cost_chart_pre[$month][$prevYear]['fleet_petrol']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['fleet_petrol']) : 0;
                $pre_data_occupancy  = !empty($utility_cost_chart_pre[$month][$prevYear]['occupancy']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['occupancy']) : 0;
                $pre_data_guest_night = !empty($utility_cost_chart_pre[$month][$prevYear]['guest_night']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['guest_night']) : 0;
                // Kept for parity with the legacy view, even though this page doesn't display them.
                $pre_data_water = !empty($utility_cost_chart_pre[$month][$prevYear]['water_consumption']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['water_consumption']) : 0;
                $pre_data_cdd = !empty($utility_cost_chart_pre[$month][$prevYear]['cdd']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['cdd']) : 0;
                $pre_data_hdd = !empty($utility_cost_chart_pre[$month][$prevYear]['hdd']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['hdd']) : 0;
                $pre_data_room_night = !empty($utility_cost_chart_pre[$month][$prevYear]['room_night']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['room_night']) : 0;
                $pre_data_electricity_tariff = !empty($utility_cost_chart_pre[$month][$prevYear]['electricity_tariff']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['electricity_tariff']) : 0;
                $pre_data_electricity_kwh = !empty($utility_cost_chart_pre[$month][$prevYear]['total_electricity_kwh']) ? floatval($utility_cost_chart_pre[$month][$prevYear]['total_electricity_kwh']) : 0;

                // CURRENT YEAR raw consumption (same fields/formulas as admin_landing_pdf_reports_carbon_footprint.php)
                $data_electricity = !empty($utility_cost_chart[$month][$year]['total_electricity_kwh']) ? max(0, floatval($utility_cost_chart[$month][$year]['total_electricity_kwh']) - floatval($utility_cost_chart[$month][$year]['onsite_generator']) - floatval($utility_cost_chart[$month][$year]['renewable_energy'])) : 0;
                $data_fuel = !empty($utility_cost_chart[$month][$year]['fuel_consumption']) ? floatval($utility_cost_chart[$month][$year]['fuel_consumption']) : 0;
                $data_lpg  = !empty($utility_cost_chart[$month][$year]['lpg_consumption']) ? floatval($utility_cost_chart[$month][$year]['lpg_consumption']) : 0;
                $data_natural_gas  = !empty($utility_cost_chart[$month][$year]['natural_gas_consumption']) ? floatval($utility_cost_chart[$month][$year]['natural_gas_consumption']) : 0;
                $data_heating_district = !empty($utility_cost_chart[$month][$year]['heating_district_consumption']) ? floatval($utility_cost_chart[$month][$year]['heating_district_consumption']) : 0;
                $data_cooling_district = !empty($utility_cost_chart[$month][$year]['cooling_district_consumption']) ? floatval($utility_cost_chart[$month][$year]['cooling_district_consumption']) : 0;
                $data_fleet_petrol = !empty($utility_cost_chart[$month][$year]['fleet_petrol']) ? floatval($utility_cost_chart[$month][$year]['fleet_petrol']) : 0;
                $data_occupancy  = !empty($utility_cost_chart[$month][$year]['occupancy']) ? floatval($utility_cost_chart[$month][$year]['occupancy']) : 0;
                $data_guest_night = !empty($utility_cost_chart[$month][$year]['guest_night']) ? floatval($utility_cost_chart[$month][$year]['guest_night']) : 0;
                // Kept for parity with the legacy view, even though this page doesn't display them.
                $data_water = !empty($utility_cost_chart[$month][$year]['water_consumption']) ? floatval($utility_cost_chart[$month][$year]['water_consumption']) : 0;
                $data_cdd = !empty($utility_cost_chart[$month][$year]['cdd']) ? floatval($utility_cost_chart[$month][$year]['cdd']) : 0;
                $data_hdd = !empty($utility_cost_chart[$month][$year]['hdd']) ? floatval($utility_cost_chart[$month][$year]['hdd']) : 0;
                $data_room_night = !empty($utility_cost_chart[$month][$year]['room_night']) ? floatval($utility_cost_chart[$month][$year]['room_night']) : 0;
                $data_electricity_tariff = !empty($utility_cost_chart[$month][$year]['electricity_tariff']) ? floatval($utility_cost_chart[$month][$year]['electricity_tariff']) : 0;
                $data_electricity_kwh = !empty($utility_cost_chart[$month][$year]['total_electricity_kwh']) ? floatval($utility_cost_chart[$month][$year]['total_electricity_kwh']) : 0;

                // EMISSION CALCULATION (re-assigns the raw consumption vars above into emission totals, same as the legacy view)
                $pre_data_electricity = round($pre_data_electricity * floatval(isset($dataFactor['electricity']) ? $dataFactor['electricity'] : 0) * floatval(isset($site_detail['electricity_emission_factor']) ? $site_detail['electricity_emission_factor'] : 0), 2);
                $pre_data_fuel = round($pre_data_fuel * floatval(isset($dataFactor['fuel_oil']) ? $dataFactor['fuel_oil'] : 0) * floatval(isset($site_detail['fuel_emission_factor']) ? $site_detail['fuel_emission_factor'] : 0), 2);
                $pre_data_lpg  = round($pre_data_lpg * floatval(isset($dataFactor['lpg']) ? $dataFactor['lpg'] : 0) * floatval(isset($site_detail['lpg_emission_factor']) ? $site_detail['lpg_emission_factor'] : 0), 2);
                $pre_data_natural_gas  = round($pre_data_natural_gas * floatval(isset($dataFactor['natural_gas']) ? $dataFactor['natural_gas'] : 0) * floatval(isset($site_detail['natural_gas_emission_factor']) ? $site_detail['natural_gas_emission_factor'] : 0), 2);
                $pre_data_heating_district = round($pre_data_heating_district * floatval(isset($dataFactor['district_heating']) ? $dataFactor['district_heating'] : 0) * floatval(isset($site_detail['district_heating_emission_factor']) ? $site_detail['district_heating_emission_factor'] : 0), 2);
                $pre_data_cooling_district = round($pre_data_cooling_district * floatval(isset($dataFactor['district_cooling']) ? $dataFactor['district_cooling'] : 0) * floatval(isset($site_detail['district_cooling_emission_factor']) ? $site_detail['district_cooling_emission_factor'] : 0), 2);
                $pre_data_fleet_petrol= round($pre_data_fleet_petrol * 2.3, 2);

                $data_electricity = round($data_electricity * floatval(isset($dataFactor['electricity']) ? $dataFactor['electricity'] : 0) * floatval(isset($site_detail['electricity_emission_factor']) ? $site_detail['electricity_emission_factor'] : 0), 2);
                $data_fuel = round($data_fuel * floatval(isset($dataFactor['fuel_oil']) ? $dataFactor['fuel_oil'] : 0) * floatval(isset($site_detail['fuel_emission_factor']) ? $site_detail['fuel_emission_factor'] : 0), 2);
                $data_lpg  = round($data_lpg * floatval(isset($dataFactor['lpg']) ? $dataFactor['lpg'] : 0) * floatval(isset($site_detail['lpg_emission_factor']) ? $site_detail['lpg_emission_factor'] : 0), 2);
                $data_natural_gas  = round($data_natural_gas * floatval(isset($dataFactor['natural_gas']) ? $dataFactor['natural_gas'] : 0) * floatval(isset($site_detail['natural_gas_emission_factor']) ? $site_detail['natural_gas_emission_factor'] : 0), 2);
                $data_heating_district = round($data_heating_district * floatval(isset($dataFactor['district_heating']) ? $dataFactor['district_heating'] : 0) * floatval(isset($site_detail['district_heating_emission_factor']) ? $site_detail['district_heating_emission_factor'] : 0), 2);
                $data_cooling_district = round($data_cooling_district * floatval(isset($dataFactor['district_cooling']) ? $dataFactor['district_cooling'] : 0) * floatval(isset($site_detail['district_cooling_emission_factor']) ? $site_detail['district_cooling_emission_factor'] : 0), 2);
                $data_fleet_petrol= round($data_fleet_petrol * 2.3, 2);

                // Accumulate totals ONLY if it's not monthly OR if it's the specific target month
                if (!$is_monthly || $month == $start_month) {
                    $total_sum_pre_data_electricity += $pre_data_electricity;
                    $total_sum_pre_data_fuel += $pre_data_fuel;
                    $total_sum_pre_data_lpg += $pre_data_lpg;
                    $total_sum_pre_data_natural_gas += $pre_data_natural_gas;
                    $total_sum_pre_data_heating_district += $pre_data_heating_district;
                    $total_sum_pre_data_cooling_district += $pre_data_cooling_district;
                    $total_sum_pre_data_fleet_petrol += $pre_data_fleet_petrol;
                    $total_sum_pre_data_occupancy += $pre_data_occupancy;
                    $total_sum_pre_data_guest_night += $pre_data_guest_night;

                    $total_sum_data_electricity += $data_electricity;
                    $total_sum_data_fuel += $data_fuel;
                    $total_sum_data_lpg += $data_lpg;
                    $total_sum_data_natural_gas += $data_natural_gas;
                    $total_sum_data_heating_district += $data_heating_district;
                    $total_sum_data_cooling_district += $data_cooling_district;
                    $total_sum_data_fleet_petrol += $data_fleet_petrol;
                    $total_sum_data_occupancy += $data_occupancy;
                    $total_sum_data_guest_night += $data_guest_night;
                }

                // Save to chart data ALWAYS (so the chart builds the YTD view)
                $chartData[] = array(
                    'month_lbl' => $montharray[$month],
                    'pre' => array(
                        'electricity' => $pre_data_electricity, 'fuel' => $pre_data_fuel, 'lpg' => $pre_data_lpg,
                        'natural_gas' => $pre_data_natural_gas, 'district_heating' => $pre_data_heating_district,
                        'district_cooling' => $pre_data_cooling_district, 'fleet_petrol' => $pre_data_fleet_petrol,
                        'total' => $pre_data_electricity+$pre_data_fuel+$pre_data_lpg+$pre_data_natural_gas+$pre_data_heating_district+$pre_data_cooling_district+$pre_data_fleet_petrol,
                        'occ' => $pre_data_occupancy
                    ),
                    'cur' => array(
                        'electricity' => $data_electricity, 'fuel' => $data_fuel, 'lpg' => $data_lpg,
                        'natural_gas' => $data_natural_gas, 'district_heating' => $data_heating_district,
                        'district_cooling' => $data_cooling_district, 'fleet_petrol' => $data_fleet_petrol,
                        'total' => $data_electricity+$data_fuel+$data_lpg+$data_natural_gas+$data_heating_district+$data_cooling_district+$data_fleet_petrol,
                        'occ' => $data_occupancy
                    )
                );
            }
        }

        // ---- TOTALS & INTENSITY (KPI cards + table) ----
        $total_sum_pre_data_sum = 0; $total_sum_data_sum = 0;
        if (!empty($site_detail['show_utility_electricity'])) { $total_sum_pre_data_sum += $total_sum_pre_data_electricity; $total_sum_data_sum += $total_sum_data_electricity; }
        if (!empty($site_detail['show_utility_fuel_oil'])) { $total_sum_pre_data_sum += $total_sum_pre_data_fuel; $total_sum_data_sum += $total_sum_data_fuel; }
        if (!empty($site_detail['show_utility_lpg'])) { $total_sum_pre_data_sum += $total_sum_pre_data_lpg; $total_sum_data_sum += $total_sum_data_lpg; }
        if (!empty($site_detail['show_utility_natural_gas'])) { $total_sum_pre_data_sum += $total_sum_pre_data_natural_gas; $total_sum_data_sum += $total_sum_data_natural_gas; }
        if (!empty($site_detail['show_utility_district_heating'])) { $total_sum_pre_data_sum += $total_sum_pre_data_heating_district; $total_sum_data_sum += $total_sum_data_heating_district; }
        if (!empty($site_detail['show_utility_district_cooling'])) { $total_sum_pre_data_sum += $total_sum_pre_data_cooling_district; $total_sum_data_sum += $total_sum_data_cooling_district; }
        if (!empty($site_detail['show_utility_fleet'])) { $total_sum_pre_data_sum += $total_sum_pre_data_fleet_petrol; $total_sum_data_sum += $total_sum_data_fleet_petrol; }

        $pre_guestnight_intensity = ($total_sum_pre_data_guest_night > 0) ? ($total_sum_pre_data_sum / $total_sum_pre_data_guest_night) : 0;
        $data_guestnight_intensity = ($total_sum_data_guest_night > 0) ? ($total_sum_data_sum / $total_sum_data_guest_night) : 0;
        
        $total_sum_data_variation = ($total_sum_pre_data_sum > 0) ? round((($total_sum_data_sum - $total_sum_pre_data_sum) * 100) / $total_sum_pre_data_sum, 2) : (($total_sum_data_sum == 0) ? 0 : 100);
        $total_guestnights_data_variation = ($pre_guestnight_intensity > 0) ? round((($data_guestnight_intensity - $pre_guestnight_intensity) * 100) / $pre_guestnight_intensity, 2) : (($data_guestnight_intensity == 0) ? 0 : 100);
        $total_sum_data_fleet_petrol_variation = ($total_sum_pre_data_fleet_petrol > 0) ? round((($total_sum_data_fleet_petrol - $total_sum_pre_data_fleet_petrol) * 100) / $total_sum_pre_data_fleet_petrol, 2) : (($total_sum_data_fleet_petrol == 0) ? 0 : 100);
        if($is_monthly) { 
            $scope1 = $total_sum_data_fuel + $total_sum_data_lpg + $total_sum_data_natural_gas + $total_sum_data_fleet_petrol;
            $scope2 = $total_sum_data_electricity + $total_sum_data_cooling_district + $total_sum_data_heating_district;
            $scope1_pre = $total_sum_pre_data_fuel + $total_sum_pre_data_lpg + $total_sum_pre_data_natural_gas + $total_sum_pre_data_fleet_petrol;
            $scope2_pre = $total_sum_pre_data_electricity + $total_sum_pre_data_cooling_district + $total_sum_pre_data_heating_district;
            $scope1_variation = ($scope1_pre > 0) ? round((($scope1 - $scope1_pre) * 100) / $scope1_pre, 2) : 0;
            $scope2_variation = ($scope2_pre > 0) ? round((($scope2 - $scope2_pre) * 100) / $scope2_pre, 2) : 0;
            
		}

        // Calculate YTD Average for chart
        $monthsCount = count($chartData);
        if ($monthsCount > 0) {
            $avgPre = array('electricity'=>0,'fuel'=>0,'lpg'=>0,'natural_gas'=>0,'district_heating'=>0,'district_cooling'=>0,'fleet_petrol'=>0,'total'=>0,'occ'=>0);
            $avgCur = array('electricity'=>0,'fuel'=>0,'lpg'=>0,'natural_gas'=>0,'district_heating'=>0,'district_cooling'=>0,'fleet_petrol'=>0,'total'=>0,'occ'=>0);
            foreach($chartData as $cd) {
                foreach($cd['pre'] as $k=>$v) $avgPre[$k] += $v;
                foreach($cd['cur'] as $k=>$v) $avgCur[$k] += $v;
            }
            foreach($avgPre as $k=>$v) $avgPre[$k] /= $monthsCount;
            foreach($avgCur as $k=>$v) $avgCur[$k] /= $monthsCount;
            $chartData[] = array('month_lbl' => "Monthly avg\n{$last_year}-{$filter_year} YTD", 'pre' => $avgPre, 'cur' => $avgCur);
        }

        // ---------- PDF DRAWING ----------
        if (!$pdf) return;
        $left = 10;
        $pageW = 190;
        $y = 26;

        // Header
        $pdf->SetXY($left, $y);
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(240, 160, 75);
        $pdf->Cell($pageW, 4, 'CARBON', 0, 1, 'L');
        $pdf->SetX($left);
        $pdf->setInterFont('B', 16);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Cell($pageW, 7, lang('carbon_emissions'), 0, 1, 'L');
        $pdf->SetX($left);
        $pdf->setInterFont('', 8);
        $pdf->SetTextColor(74, 90, 108);
        // Use $chart_end_month (the actual last month the chart/data below covers), not the
        // separately-sourced $end_month, which for a YTD report falls back to today's real
        // calendar month rather than the report's last-reported month.
        $monthTitle = $is_monthly ? $fullmontharray[$start_month]." ".$filter_year : $fullmontharray[$chart_start_month]."–".$fullmontharray[$chart_end_month]." ".$filter_year;
        $monthTitlePrev = $is_monthly ? $fullmontharray[$start_month]." ".$last_year : $fullmontharray[$chart_start_month]."–".$fullmontharray[$chart_end_month]." ".$last_year;
        // Short form for the "Total emissions" KPI card below, which has little width to spare.
        $cardMonthTitle = $is_monthly ? $monthTitle : 'YTD ' . $filter_year;
        $pdf->Cell($pageW, 4, "Scope 1 & 2 • {$monthTitle} vs {$monthTitlePrev}", 0, 1, 'L');
        
        $y = $pdf->GetY() + 4;

        // --- 3 KPI Cards ---
        $cardW = ($pageW - 8) / 3;
        $cardH = 25;
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFillColor(255, 255, 255);

        // Draw Card Function
        $drawKpiCard = function($pdf, $x, $y, $w, $h, $title, $valueStr, $unitStr, $variation, $prevValueStr, $iconPath='') {
            $pdf->RoundedRect($x, $y, $w, $h, 1.5, '1111', 'DF');
            
            // Icon
            $titleXOffset = 3;
            if ($iconPath != '' && file_exists($iconPath)) {
                $pdf->Image($iconPath, $x + 3, $y + 2, 3.5, 3.5);
                $titleXOffset = 8;
            }

            // Title
            $pdf->SetXY($x + $titleXOffset, $y + 2);
            $pdf->setInterFont('B', 6.5);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->Cell($w - 10, 4, strtoupper($title), 0, 0, 'L');
            
            // Value
            $pdf->SetXY($x + 3, $y + 7);
            $pdf->setInterFont('B', 16);
            $pdf->SetTextColor(23, 55, 94);
            $pdf->Cell($w - 6, 6, $valueStr, 0, 0, 'L');
            
            // Unit
            $pdf->SetXY($x + 3, $y + 15);
            $pdf->setInterFont('', 6.5);
            $pdf->SetTextColor(107, 119, 133);
            $pdf->Cell($w - 6, 3, $unitStr, 0, 0, 'L');

            // Variation Pill
            $pillY = $y + 19;
            if ($variation <= 0) {
                // Negative is good (Green)
                $bg = array(234, 245, 237);
                $fg = array(60, 170, 90);
                $varStr = '▼ ' . number_format(abs($variation), 2) . '% vs ' . $prevValueStr;
            } else {
                // Positive is bad (Red)
                $bg = array(252, 237, 237);
                $fg = array(217, 67, 67);
                $varStr = '▲ ' . number_format($variation, 2) . '% vs ' . $prevValueStr;
            }
            $pdf->setInterFont('B', 6);
            $strW = $pdf->GetStringWidth($varStr) + 4;
            $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
            $pdf->SetDrawColor($bg[0], $bg[1], $bg[2]); // transparent border
            $pdf->RoundedRect($x + 3, $pillY, $strW, 4, 1.0, '1111', 'DF');
            $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
            $pdf->SetXY($x + 3, $pillY + 0.3);
            $pdf->Cell($strW, 3.5, $varStr, 0, 0, 'C');
            
            // Reset colors
            $pdf->SetDrawColor(213, 223, 233);
            $pdf->SetFillColor(255, 255, 255);
        };

        $baseIconPath = FCPATH . 'themes/default/images/waste/';
        $icons = array(
            'carbon' => 'icon-carbon-navy.png',
            'room_night' => 'icon-room-night-navy.png',
            'fleet' => 'icon-fleet-navy.png',
            'bar_chart' => 'icon-bar-chart-navy.png',
            'table' => 'icon-table-navy.png',
        );
        // Shared RGB palette for the new (modern-mockup) design, looked up by utility key; see getUtilityPanelColorNewMYTDUi().
        $utilityColorsNew = function_exists('getUtilityPanelColorNewMYTDUi') ? getUtilityPanelColorNewMYTDUi() : array();
        // Lightens an RGB triple toward white by $amount (0-1); used for the "previous year" faded bars/legend.
        $lightenColor = function($rgb, $amount = 0.55) {
            return array(
                round($rgb[0] + (255 - $rgb[0]) * $amount),
                round($rgb[1] + (255 - $rgb[1]) * $amount),
                round($rgb[2] + (255 - $rgb[2]) * $amount),
            );
        };
        // 1. Total Emissions
        $drawKpiCard($pdf, $left, $y, $cardW, $cardH, lang('total_emissions') . ", " . mb_strtoupper($cardMonthTitle, 'UTF-8'), number_format($total_sum_data_sum), lang('unit_kgco2e'), $total_sum_data_variation, number_format($total_sum_pre_data_sum), $baseIconPath . $icons['carbon']);

        // 2. Per Guest Night
        $drawKpiCard($pdf, $left + $cardW + 4, $y, $cardW, $cardH, lang('per_guest_night'), number_format($data_guestnight_intensity, 2), lang('unit_kgco2e') . '/GN', $total_guestnights_data_variation, number_format($pre_guestnight_intensity, 2), $baseIconPath . $icons['room_night']);

        // 3. Fleet Petrol
        $drawKpiCard($pdf, $left + ($cardW + 4)*2, $y, $cardW, $cardH, strtoupper(lang('fleet_petrol')), number_format($total_sum_data_fleet_petrol), lang('unit_kgco2e'), $total_sum_data_fleet_petrol_variation, number_format($total_sum_pre_data_fleet_petrol), $baseIconPath . $icons['fleet']);

        $y += $cardH + 4;

        // --- Chart 1: Bar Chart ---
        $chartH = 76;
        $pdf->RoundedRect($left, $y, $pageW, $chartH, 1.5, '1111', 'D');
        
        // Title bar
        if (file_exists($baseIconPath . $icons['bar_chart'])) {
            $pdf->Image($baseIconPath . $icons['bar_chart'], $left + 3, $y + 2.5, 3.5, 3.5);
        }
        $pdf->SetXY($left + 8, $y + 2);
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Cell(80, 5, lang('carbon_emissions_by_source'), 0, 0, 'L');
        $pdf->SetXY($left + $pageW - 80, $y + 2);
        $pdf->setInterFont('', 6.5);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->Cell(78, 5, 'Monthly, thousand kgCO₂e - bar labels = month total', 0, 0, 'R');
        $pdf->SetDrawColor(235, 240, 245);
        $pdf->Line($left, $y + 8, $left + $pageW, $y + 8);
        
        // Find max Y for scaling
        $maxY = 10;
        foreach($chartData as $cd) {
            if($cd['pre']['total'] > $maxY) $maxY = $cd['pre']['total'];
            if($cd['cur']['total'] > $maxY) $maxY = $cd['cur']['total'];
        }
        $maxY = ceil($maxY / 100000) * 100000;
        if($maxY == 0) $maxY = 100000;

        $cx = $left + 15;
        $cy = $y + 12;
        $cw = $pageW - 20;
        $ch = $chartH - 24;

        // Y-axis grid lines
        $pdf->setInterFont('', 6);
        $pdf->SetTextColor(150, 160, 170);
        $steps = 4;
        for($i=0; $i<=$steps; $i++) {
            $val = $maxY - ($i * ($maxY / $steps));
            $gy = $cy + ($i * ($ch / $steps));
            if($i < $steps) {
                $pdf->SetDrawColor(235, 240, 245);
                $pdf->Line($cx, $gy, $cx + $cw, $gy);
            }
            $pdf->SetXY($left + 2, $gy - 2);
            $pdf->Cell(11, 4, ($val/1000).'k', 0, 0, 'R');
        }

        // Draw Bars
        $barCount = count($chartData);
        // Utility -> [show_utility flag, color key, legend label], in the same order
        // and gated by the same site_detail flags as the table below, so the chart/
        // legend only ever show a utility the table itself would also show.
        $utilitySeriesDefs = array(
            'electricity' => array('show_utility_electricity', 'electricity', lang('electricity')),
            'fuel' => array('show_utility_fuel_oil', 'fuel_oil', lang('fuel')),
            'lpg' => array('show_utility_lpg', 'lpg', lang('lpg')),
            'natural_gas' => array('show_utility_natural_gas', 'natural_gas', lang('natural_gas')),
            'district_heating' => array('show_utility_district_heating', 'district_heating', lang('heating_district')),
            'district_cooling' => array('show_utility_district_cooling', 'district_cooling', 'District cooling'),
            'fleet_petrol' => array('show_utility_fleet', 'fleet_petrol', 'Fleet petrol'),
        );
        if($barCount > 0) {
            $slotW = $cw / $barCount;
            $barW = min(7, ($slotW / 2) - 1);
            $gap = 1;
            $series = array();
            foreach ($utilitySeriesDefs as $chartKey => $def) {
                if (!empty($site_detail[$def[0]])) {
                    $series[$chartKey] = $utilityColorsNew[$def[1]];
                }
            }
            $seriesFaded = array();
            foreach ($series as $k => $col) {
                $seriesFaded[$k] = $lightenColor($col);
            }

            foreach($chartData as $i => $cd) {
                $bx = $cx + ($i * $slotW) + ($slotW/2) - $barW - ($gap/2);
                
                // Pre bar
                $by = $cy + $ch;
                foreach($series as $k => $col) {
                    if($cd['pre'][$k] > 0) {
                        $bh = ($cd['pre'][$k] / $maxY) * $ch;
                        $by -= $bh;
                        $pdf->SetFillColor($seriesFaded[$k][0], $seriesFaded[$k][1], $seriesFaded[$k][2]);
                        $pdf->Rect($bx, $by, $barW, $bh, 'F');
                        // White separator
                        $pdf->SetDrawColor(255, 255, 255);
                        $pdf->SetLineWidth(0.3);
                        $pdf->Line($bx, $by+$bh, $bx+$barW, $by+$bh);
                    }
                }
                // Pre Total Label
                if($cd['pre']['total'] > 0) {
                    $pdf->SetXY($bx - 2, $by - 3.5);
                    $pdf->setInterFont('', 5.5);
                    $pdf->SetTextColor(150, 160, 170);
                    $pdf->Cell($barW + 4, 3, round($cd['pre']['total']/1000), 0, 0, 'C');
                }

                // Cur bar
                $bx2 = $bx + $barW + $gap;
                $by2 = $cy + $ch;
                foreach($series as $k => $col) {
                    if($cd['cur'][$k] > 0) {
                        $bh = ($cd['cur'][$k] / $maxY) * $ch;
                        $by2 -= $bh;
                        $pdf->SetFillColor($col[0], $col[1], $col[2]);
                        $pdf->Rect($bx2, $by2, $barW, $bh, 'F');
                        // White separator
                        $pdf->SetDrawColor(255, 255, 255);
                        $pdf->SetLineWidth(0.3);
                        $pdf->Line($bx2, $by2+$bh, $bx2+$barW, $by2+$bh);
                    }
                }
                // Cur Total Label
                if($cd['cur']['total'] > 0) {
                    $pdf->SetXY($bx2 - 2, $by2 - 3.5);
                    $pdf->setInterFont('B', 5.5);
                    $pdf->SetTextColor(23, 55, 94);
                    $pdf->Cell($barW + 4, 3, round($cd['cur']['total']/1000), 0, 0, 'C');
                }

                // X-axis label
                $lbls = explode("\n", $cd['month_lbl']);
                $pdf->SetXY($cx + ($i * $slotW), $cy + $ch + 1);
                $pdf->setInterFont('B', 6);
                $pdf->SetTextColor(23, 55, 94);
                $pdf->Cell($slotW, 3, $lbls[0], 0, 0, 'C');
                $pdf->SetXY($cx + ($i * $slotW), $cy + $ch + 4);
                $pdf->setInterFont('', 5.5);
                $pdf->SetTextColor(150, 160, 170);
                $pdf->Cell($slotW, 3, isset($lbls[1]) ? $lbls[1] : "{$last_year}-{$filter_year}", 0, 0, 'C');
            }
        }

        // Legend Bottom - same utilities/order/gating as the bar chart series above
        $legY = $y + $chartH - 5;
        $legX = $left + 5;
        $legends = array();
        foreach ($utilitySeriesDefs as $chartKey => $def) {
            if (!empty($site_detail[$def[0]])) {
                $legends[$def[2]] = $utilityColorsNew[$def[1]];
            }
        }
        $legends[$last_year . ' shown faded'] = array(200, 200, 200); // faded gray
        foreach($legends as $k => $c) {
            $pdf->SetFillColor($c[0], $c[1], $c[2]);
            $pdf->RoundedRect($legX, $legY+1, 2.5, 2.5, 0.5, '1111', 'F');
            $pdf->SetXY($legX + 4, $legY);
            $pdf->setInterFont('', 6.5);
            $pdf->SetTextColor(107, 119, 133);
            $tw = $pdf->GetStringWidth($k) + 6;
            $pdf->Cell($tw, 4, $k, 0, 0, 'L');
            $legX += $tw + 2;
        }

        $y += $chartH + 4;

        // --- Chart 2: Occupancy Line Chart ---
        $chartH2 = 37;
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->RoundedRect($left, $y, $pageW, $chartH2, 1.5, '1111', 'D');
        
        $pdf->SetXY($left + 8, $y + 2);
        if (file_exists($baseIconPath . $icons['room_night'])) {
            $pdf->Image($baseIconPath . $icons['room_night'], $left + 3, $y + 2.5, 3.5, 3.5);
        }
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Cell(80, 5, lang('occupancy'), 0, 0, 'L');
        $pdf->SetDrawColor(235, 240, 245);
        $pdf->Line($left, $y + 8, $left + $pageW, $y + 8);

        $cy2 = $y + 12;
        $ch2 = $chartH2 - 20;
        
        $pdf->setInterFont('', 6);
        $pdf->SetTextColor(150, 160, 170);
        $pdf->SetDrawColor(235, 240, 245);
        $pdf->Line($cx, $cy2, $cx + $cw, $cy2); // 100%
        $pdf->Line($cx, $cy2 + ($ch2/2), $cx + $cw, $cy2 + ($ch2/2)); // 50%
        $pdf->Line($cx, $cy2 + $ch2, $cx + $cw, $cy2 + $ch2); // 0%
        
        $pdf->SetXY($left + 2, $cy2 - 2); $pdf->Cell(11, 4, '100%', 0, 0, 'R');
        $pdf->SetXY($left + 2, $cy2 + ($ch2/2) - 2); $pdf->Cell(11, 4, '50%', 0, 0, 'R');
        $pdf->SetXY($left + 2, $cy2 + $ch2 - 2); $pdf->Cell(11, 4, '0%', 0, 0, 'R');

        if($barCount > 0) {
            $prevX = -1; $prevY1 = -1; $prevY2 = -1;
            foreach($chartData as $i => $cd) {
                $px = $cx + ($i * $slotW) + ($slotW/2);
                $py1 = $cy2 + $ch2 - (($cd['pre']['occ']/100)*$ch2);
                $py2 = $cy2 + $ch2 - (($cd['cur']['occ']/100)*$ch2);
                
                if($prevX != -1) {
                    // Dotted line for PREV
                    $pdf->SetDrawColor(176, 184, 192);
                    $pdf->SetLineWidth(0.4);
                    $pdf->Line($prevX, $prevY1, $px, $py1, array('dash' => '1.5,1.5'));
                    // Solid line for CUR
                    $pdf->SetDrawColor(45, 85, 166);
                    $pdf->Line($prevX, $prevY2, $px, $py2, array('dash' => 0));
                }
                $prevX = $px; $prevY1 = $py1; $prevY2 = $py2;
                
                // X labels
                $lbls = explode("\n", $cd['month_lbl']);
                $pdf->SetXY($px - ($slotW/2), $cy2 + $ch2 + 1);
                $pdf->setInterFont('', 6);
                $pdf->SetTextColor(107, 119, 133);
                $pdf->Cell($slotW, 3, $lbls[0], 0, 0, 'C');
            }
            // Draw points
            foreach($chartData as $i => $cd) {
                $px = $cx + ($i * $slotW) + ($slotW/2);
                $py1 = $cy2 + $ch2 - (($cd['pre']['occ']/100)*$ch2);
                $py2 = $cy2 + $ch2 - (($cd['cur']['occ']/100)*$ch2);
                
                // Pre point
                $pdf->SetFillColor(176, 184, 192);
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetLineWidth(0.2);
                $pdf->Circle($px, $py1, 1.2, 0, 360, 'DF');
                $pdf->SetXY($px - 5, $py1 - 4);
                $pdf->setInterFont('', 5.5);
                $pdf->SetTextColor(150, 160, 170);
                $pdf->Cell(10, 3, round($cd['pre']['occ']).'%', 0, 0, 'C');
                
                // Cur point
                $pdf->SetFillColor(45, 85, 166);
                $pdf->Circle($px, $py2, 1.2, 0, 360, 'DF');
                $pdf->SetXY($px - 5, $py2 + 1);
                $pdf->setInterFont('B', 5.5);
                $pdf->SetTextColor(45, 85, 166);
                $pdf->Cell(10, 3, round($cd['cur']['occ']).'%', 0, 0, 'C');
            }
            $pdf->SetLineStyle(array('dash' => 0)); // reset
        }
        
        $legY2 = $y + $chartH2 - 4;
        $pdf->SetXY($left + 5, $legY2);
        $pdf->setInterFont('', 6.5);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->SetDrawColor(176, 184, 192);
        $pdf->SetLineWidth(0.4);
        $pdf->Line($left + 5, $legY2+2, $left + 9, $legY2+2, array('dash' => '1.5,1.5'));
        $pdf->SetLineStyle(array('dash' => 0));
        $pdf->SetXY($left + 11, $legY2);
        $pdf->Cell(15, 4, $last_year, 0, 0, 'L');
        
        $pdf->SetDrawColor(45, 85, 166);
        $pdf->Line($left + 26, $legY2+2, $left + 30, $legY2+2);
        $pdf->SetXY($left + 32, $legY2);
        $pdf->Cell(15, 4, $filter_year, 0, 0, 'L');

        $y += $chartH2 + 4;

        // --- Table ---
        if($y + 40 > 280) {
            $pdf->AddPage();
            $y = 10;
        }

        $tableStartY = $y;

        $pdf->SetFillColor(244, 247, 250);
        $pdf->Rect($left, $y, $pageW, 8, 'F');

        $pdf->SetXY($left + 8, $y + 1.5);
        if (file_exists($baseIconPath . $icons['table'])) {
            $pdf->Image($baseIconPath . $icons['table'], $left + 3, $y + 2, 3.5, 3.5);
        }
        $pdf->setInterFont('B', 8);
        $pdf->SetTextColor(23, 55, 94);
        $pdf->Cell(100, 5, "Carbon by utility -  {$monthTitle} vs {$monthTitlePrev}", 0, 0, 'L');
        $y += 8;

        $colW = array(60, 45, 45, 40);
        $rowH = 7;
        
        // Headers
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($left, $y, $pageW, $rowH, 'F');
        // Highlight the current-year column
        $pdf->SetFillColor(234, 243, 253);
        $pdf->Rect($left + $colW[0] + $colW[1], $y, $colW[2], $rowH, 'F');
        $pdf->SetDrawColor(238, 242, 246);
        $pdf->Line($left, $y + $rowH, $left + $pageW, $y + $rowH);
        
        $pdf->setInterFont('B', 6.5);
        $pdf->SetTextColor(107, 119, 133);
        $pdf->SetXY($left + 2, $y + 2); $pdf->Cell($colW[0]-4, 3, 'Utility', 0, 0, 'L');
        // Short form here too - these columns (45 units) are too narrow for the full
        // "January-September 2025" range even wrapped onto two lines.
        $cardMonthTitlePrev = $is_monthly ? $monthTitlePrev : 'YTD ' . $last_year;
        $pdf->SetXY($left + $colW[0], $y + 1); $pdf->MultiCell($colW[1], 3, "{$cardMonthTitlePrev}\nCO₂ (" . lang('unit_kgco2e') . ")", 0, 'R');
        $pdf->SetTextColor(23, 55, 94);
        $pdf->SetXY($left + $colW[0] + $colW[1], $y + 1); $pdf->MultiCell($colW[2], 3, "{$cardMonthTitle}\nCO₂ (" . lang('unit_kgco2e') . ")", 0, 'R');
        $pdf->SetTextColor(107, 119, 133);
        $pdf->SetXY($left + $colW[0] + $colW[1] + $colW[2], $y + 2); $pdf->Cell($colW[3]-4, 3, 'Variation (%)', 0, 0, 'C');
        
        $y += $rowH + 1;

        $drawTableRow = function($pdf, $left, $y, $colW, $rowH, $isStriped, $utility, $preVal, $curVal, $varVal, $color=null) {
            // Highlight the current-year column
            $pdf->SetFillColor(234, 243, 253);
            $pdf->Rect($left + $colW[0] + $colW[1], $y, $colW[2], $rowH, 'F');
            $pdf->SetDrawColor(238, 242, 246);
            $pdf->Line($left, $y + $rowH, $left + array_sum($colW), $y + $rowH);
            
            // Utility
            if($color) {
                $pdf->SetFillColor($color[0], $color[1], $color[2]);
                $pdf->RoundedRect($left + 2, $y + 2, 2.5, 2.5, 0.5, '1111', 'F');
                $pdf->SetXY($left + 6, $y + 1.5);
            } else {
                $pdf->SetXY($left + 2, $y + 1.5);
            }
            $pdf->setInterFont('', 7.5);
            $pdf->SetTextColor(23, 55, 94);
            if(!$color) $pdf->setInterFont('B', 7.5);
            $pdf->Cell($colW[0]-8, 4, $utility, 0, 0, 'L');
            
            // Pre
            $pdf->SetXY($left + $colW[0], $y + 1.5);
            $pdf->setInterFont('', 7.5);
            $pdf->SetTextColor(107, 119, 133);
            if(!$color) $pdf->SetTextColor(23, 55, 94);
            if(!$color) $pdf->setInterFont('B', 7.5);
            $pdf->Cell($colW[1]-2, 4, number_format($preVal), 0, 0, 'R');
            
            // Cur
            $pdf->SetXY($left + $colW[0] + $colW[1], $y + 1.5);
            $pdf->setInterFont('B', 7.5);
            $pdf->SetTextColor(23, 55, 94);
            $pdf->Cell($colW[2]-2, 4, number_format($curVal), 0, 0, 'R');
            
            // Variation
            $pillY = $y + 1.5;
            $varX = $left + $colW[0] + $colW[1] + $colW[2] + 4;
            if ($varVal <= 0) {
                $bg = array(234, 245, 237);
                $fg = array(60, 170, 90);
                $varStr = '▼ ' . number_format(abs($varVal), 2) . '%';
            } else {
                $bg = array(252, 237, 237);
                $fg = array(217, 67, 67);
                $varStr = '▲ ' . number_format($varVal, 2) . '%';
            }
            $pdf->setInterFont('B', 6);
            $strW = 20;
            $varX = $left + $colW[0] + $colW[1] + $colW[2] + ($colW[3]/2) - ($strW/2);
            $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
            $pdf->SetDrawColor($bg[0], $bg[1], $bg[2]);
            $pdf->RoundedRect($varX, $pillY, $strW, 4, 1.0, '1111', 'DF');
            $pdf->SetTextColor($fg[0], $fg[1], $fg[2]);
            $pdf->SetXY($varX, $pillY + 0.3);
            $pdf->Cell($strW, 3.5, $varStr, 0, 0, 'C');
        };

        $i = 0;
        if (!empty($site_detail['show_utility_electricity'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('electricity'), $total_sum_pre_data_electricity, $total_sum_data_electricity, ($total_sum_pre_data_electricity>0)?(($total_sum_data_electricity-$total_sum_pre_data_electricity)*100/$total_sum_pre_data_electricity):(($total_sum_data_electricity==0)?0:100), $utilityColorsNew['electricity']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_fuel_oil'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('fuel'), $total_sum_pre_data_fuel, $total_sum_data_fuel, ($total_sum_pre_data_fuel>0)?(($total_sum_data_fuel-$total_sum_pre_data_fuel)*100/$total_sum_pre_data_fuel):(($total_sum_data_fuel==0)?0:100), $utilityColorsNew['fuel_oil']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_lpg'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('lpg'), $total_sum_pre_data_lpg, $total_sum_data_lpg, ($total_sum_pre_data_lpg>0)?(($total_sum_data_lpg-$total_sum_pre_data_lpg)*100/$total_sum_pre_data_lpg):(($total_sum_data_lpg==0)?0:100), $utilityColorsNew['lpg']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_natural_gas'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('natural_gas'), $total_sum_pre_data_natural_gas, $total_sum_data_natural_gas, ($total_sum_pre_data_natural_gas>0)?(($total_sum_data_natural_gas-$total_sum_pre_data_natural_gas)*100/$total_sum_pre_data_natural_gas):(($total_sum_data_natural_gas==0)?0:100), $utilityColorsNew['natural_gas']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_district_heating'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('heating_district'), $total_sum_pre_data_heating_district, $total_sum_data_heating_district, ($total_sum_pre_data_heating_district>0)?(($total_sum_data_heating_district-$total_sum_pre_data_heating_district)*100/$total_sum_pre_data_heating_district):(($total_sum_data_heating_district==0)?0:100), $utilityColorsNew['district_heating']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_district_cooling'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, 'District cooling', $total_sum_pre_data_cooling_district, $total_sum_data_cooling_district, ($total_sum_pre_data_cooling_district>0)?(($total_sum_data_cooling_district-$total_sum_pre_data_cooling_district)*100/$total_sum_pre_data_cooling_district):(($total_sum_data_cooling_district==0)?0:100), $utilityColorsNew['district_cooling']);
            $y += $rowH; $i++;
        }
        if (!empty($site_detail['show_utility_fleet'])) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, 'Fleet petrol', $total_sum_pre_data_fleet_petrol, $total_sum_data_fleet_petrol, ($total_sum_pre_data_fleet_petrol>0)?(($total_sum_data_fleet_petrol-$total_sum_pre_data_fleet_petrol)*100/$total_sum_pre_data_fleet_petrol):(($total_sum_data_fleet_petrol==0)?0:100), $utilityColorsNew['fleet_petrol']);
            $y += $rowH; $i++;
        }

        // Scope 1 and Scope 2 totals - Monthly report only; YTD has no scope-split data
        // source wired up, so these would always render as a meaningless flat 0 row.
        if (empty($is_ytd_report)) {
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('total_scope_1'), $scope1_pre, $scope1,$scope1_variation, null);
            $y += $rowH; $i++;
            $drawTableRow($pdf, $left, $y, $colW, $rowH, $i%2==0, lang('total_scope_2'), $scope2_pre, $scope2,$scope2_variation, null);
            $y += $rowH; $i++;
        }

        // Totals row
        $drawTableRow($pdf, $left, $y, $colW, $rowH, false, 'Total', $total_sum_pre_data_sum, $total_sum_data_sum, $total_sum_data_variation, null);
        $y += $rowH;
        
        // Intensity row
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->Line($left, $y, $left + $pageW, $y); // thicker separator
        $drawTableRow($pdf, $left, $y, $colW, $rowH, false, lang('unit_kgco2e') . ' / guest night', $pre_guestnight_intensity, $data_guestnight_intensity, $total_guestnights_data_variation, null);
        $y += $rowH;

        // Outer border around the whole table (title bar + header + all rows)
        $pdf->SetDrawColor(213, 223, 233);
        $pdf->SetLineWidth(0.3);
        $pdf->RoundedRect($left, $tableStartY, $pageW, $y - $tableStartY, 1.5, '1111', 'D');
    }
}
?>
