<?php
// Monthly utilities comparison table, drawn natively on $pdf (passed in via $data['pdf'])
// because TCPDF's writeHTML() cannot render this grouped-header layout reliably.
// mn_pdf_rgb()/mn_pdf_fill()/mn_pdf_text()/mn_pdf_draw() are defined in
// admin_landing_pdf_reports_progress_table_new.php, which is always included before this file.

if (isset($pdf) && !empty($utility_cost_chart)) {
    // ---- setup ----
    $isLocal = isset($isLocal) ? $isLocal : false;
    $percentage_decimal = isset($percentage_decimal) ? $percentage_decimal : 2;
    $value_decimal = isset($value_decimal) ? $value_decimal : 2;
    $colBad = '#C8382F';
    $colGood = '#2E8B57';

    $left = 10;
    $pageW = 190;
    $y = $pdf->GetY() + 5;

    $baseIconPath = FCPATH . 'themes/default/images/waste/';
    $icons = array(
        'table' => 'icon-table-navy.png',
    );
    if (file_exists($baseIconPath . $icons['table'])) {
        $pdf->Image($baseIconPath . $icons['table'], $left, $y + 0.5, 4, 4);
    }

    $pdf->setInterFont('B', 11.5);
    mn_pdf_text($pdf, '#17375E');
    $pdf->SetXY($left + 5, $y);
    $pdf->Cell(115, 5, !empty($is_ytd_report) ? lang('ytd_utilities_report') : lang('monthly_utilities_report'), 0, 0, 'L');
    $pdf->setInterFont('', 6);
    mn_pdf_text($pdf, '#6B7785');
    $pdf->SetXY($left + 120, $y + 0.8);
    $pdf->Cell($pageW - 120, 4, lang('variance_legend'), 0, 1, 'R');
    $y = $pdf->GetY() + 2;

    $colW = array(50, 15, 15, 15, 15, 13, 20, 16, 15);
    $sum = array_sum($colW);
    if ($sum != $pageW) {
        $colW[0] += ($pageW - $sum);
    }
    $headH1 = 4.4;
    $headH2 = 7.6;
    $rowH = 4.0;
    $secH = 4.6;
    $headH = $headH1 + $headH2;

    // ---- header ----
    mn_pdf_fill($pdf, '#F4F7FA');
    $pdf->Rect($left, $y, array_sum($colW), $headH, 'F');
    mn_pdf_fill($pdf, '#E3EDF7');
    $diffLYx = $left + $colW[0] + $colW[1] + $colW[2] + $colW[3];
    $diffLYw = $colW[4] + $colW[5] + $colW[6];
    $diffBx = $diffLYx + $diffLYw;
    $diffBw = $colW[7] + $colW[8];
    $pdf->Rect($diffLYx, $y, $diffLYw, $headH, 'F');
    $pdf->Rect($diffBx, $y, $diffBw, $headH, 'F');
    mn_pdf_draw($pdf, '#D5DFE9');

    $pdf->setInterFont('B', 6.5);
    mn_pdf_text($pdf, '#4A5A6C');
    $x = $left;
    $pdf->SetXY($x, $y + ($headH / 2) - 1.3);
    $pdf->Cell($colW[0], 3, !empty($is_ytd_report) ? 'YTD report' : 'Monthly report', 0, 0, 'L');
    $x += $colW[0];
    // Monthly's titles are short ("Oct 2026"), fit on one line. YTD's are longer
    // ("Current Year - 2026") and overlap into neighbouring columns on one line, so wrap
    // onto two lines the same way the (already narrower) Budget column does below.
    $heads1 = array($current_title, $previous_title);
    foreach ($heads1 as $i => $h) {
        $headParts = explode(' - ', $h, 2);
        if (count($headParts) > 1) {
            $pdf->SetXY($x, $y + ($headH / 2) - 3.1);
            $pdf->Cell($colW[1 + $i], 3, $headParts[0], 0, 0, 'C');
            $pdf->SetXY($x, $y + ($headH / 2) + 0.2);
            $pdf->Cell($colW[1 + $i], 3, $headParts[1], 0, 0, 'C');
        } else {
            $pdf->SetXY($x, $y + ($headH / 2) - 1.3);
            $pdf->Cell($colW[1 + $i], 3, $h, 0, 0, 'C');
        }
        $x += $colW[1 + $i];
    }
    // "Budget" title wraps onto two lines ("Budget" / the period) since its column is narrow
    $budgetParts = explode(' - ', $budget_title, 2);
    $pdf->SetXY($x, $y + ($headH / 2) - 3.1);
    $pdf->Cell($colW[3], 3, $budgetParts[0], 0, 0, 'C');
    $pdf->SetXY($x, $y + ($headH / 2) + 0.2);
    $pdf->Cell($colW[3], 3, isset($budgetParts[1]) ? $budgetParts[1] : '', 0, 0, 'C');
    $x += $colW[3];

    mn_pdf_text($pdf, '#17375E');
    $pdf->SetXY($diffLYx, $y + 0.4);
    $pdf->Cell($diffLYw, $headH1, lang('diff_vs_last_year'), 0, 0, 'C');
    $pdf->SetXY($diffBx, $y + 0.4);
    $pdf->Cell($diffBw, $headH1, lang('diff_vs_budget'), 0, 0, 'C');

    $pdf->setInterFont('', 6);
    mn_pdf_text($pdf, '#17375E');
    $subY = $y + $headH1 + 1;
    $pdf->SetXY($diffLYx, $subY);
    $pdf->Cell($colW[4], 3, 'Value', 0, 0, 'C');
    $pdf->SetXY($diffLYx + $colW[4], $subY);
    $pdf->Cell($colW[5], 3, '%', 0, 0, 'C');
    // "Cost @ last year tariff" wraps onto two lines to fit its column
    $costTariffX = $diffLYx + $colW[4] + $colW[5];
    $pdf->SetXY($costTariffX, $subY - 0.3);
    $pdf->Cell($colW[6], 3, lang('cost_last_year_tariff_line1'), 0, 0, 'C');
    $pdf->SetXY($costTariffX, $subY + 2.6);
    $pdf->Cell($colW[6], 3, lang('cost_last_year_tariff_line2'), 0, 0, 'C');
    $pdf->SetXY($diffBx, $subY);
    $pdf->Cell($colW[7], 3, 'Value', 0, 0, 'C');
    $pdf->SetXY($diffBx + $colW[7], $subY);
    $pdf->Cell($colW[8], 3, '%', 0, 0, 'C');
    $tableTopY = $y;
    $y += $headH1 + $headH2;
    $pdf->SetLineWidth(0.2);
    $pdf->Line(
        $left,
        $y,
        $left + array_sum($colW),
        $y
    );

    // ---- row renderer ----
    $drawRow = function ($label, $cur, $prev, $bud, $dv, $dp, $costLy, $bdv, $bdp, $dvBad, $bdvBad) use ($pdf, $left, $colW, $rowH, $colBad, $colGood) {
        $x = $left;
        $y0 = $pdf->GetY();
        $pdf->setInterFont('', 6.3);
        mn_pdf_text($pdf, '#4A5A6C');
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[0], $rowH, '  ' . $label, 0, 0, 'L');
        $x += $colW[0];
        mn_pdf_text($pdf, '#17375E');
        foreach (array($cur, $prev, $bud) as $i => $vv) {
            $pdf->SetXY($x, $y0);
            $pdf->Cell($colW[1 + $i], $rowH, $vv, 0, 0, 'C');
            $x += $colW[1 + $i];
        }
        $pdf->SetTextColor(0, 0, 0);
        mn_pdf_text($pdf, $dvBad ? $colBad : $colGood);
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[4], $rowH, $dv, 0, 0, 'C');
        $x += $colW[4];
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[5], $rowH, $dp, 0, 0, 'C');
        $x += $colW[5];
        mn_pdf_text($pdf, '#4A5A6C');
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[6], $rowH, $costLy, 0, 0, 'C');
        $x += $colW[6];
        mn_pdf_text($pdf, $bdvBad ? $colBad : $colGood);
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[7], $rowH, $bdv, 0, 0, 'C');
        $x += $colW[7];
        $pdf->SetXY($x, $y0);
        $pdf->Cell($colW[8], $rowH, $bdp, 0, 0, 'C');
        $pdf->SetY($y0 + $rowH);
        mn_pdf_draw($pdf, '#EEF2F6');
        $pdf->SetLineWidth(0.1);
        $pdf->Line($left, $y0 + $rowH, $left + array_sum($colW), $y0 + $rowH);
    };

    $drawSection = function ($label, $hex) use ($pdf, $left, $colW, $secH) {
        mn_pdf_fill($pdf, '#F4F7FA');
        $pdf->Rect($left, $pdf->GetY(), array_sum($colW), $secH, 'F');
        $yy = $pdf->GetY();
        mn_pdf_fill($pdf, $hex);
        $pdf->RoundedRect($left + 1,$yy + 1.6,2,2,0.4,'1111','F');
        $pdf->setInterFont('B', 7);
        mn_pdf_text($pdf, '#17375E');
        $pdf->SetXY($left + 4, $yy);
        $pdf->Cell(array_sum($colW) - 4, $secH, strtoupper($label), 0, 0, 'L');
        $pdf->SetY($yy + $secH);
    };

    $pdf->SetY($y);

    // Occupancy & climate
    $drawSection('Occupancy & climate', '#9AA6B2');
    $room_night_difference = $total_sum_data_room_night - $total_sum_pre_data_room_night;
    $room_night_act_bud_diff = $total_sum_data_room_night - $total_sum_data_total_room_night_budget;
    $room_night_budget_variation = ($total_sum_data_total_room_night_budget != 0) ? round(($room_night_act_bud_diff * 100) / $total_sum_data_total_room_night_budget, $percentage_decimal) : 0;
    $drawRow('Room nights', number_format($total_sum_data_room_night), number_format($total_sum_pre_data_room_night), number_format($total_sum_data_total_room_night_budget),
        number_format($room_night_difference), $total_sum_data_room_night_variation . '%', '',
        number_format($room_night_act_bud_diff), number_format($room_night_budget_variation, $percentage_decimal) . '%', $room_night_difference > 0, $room_night_act_bud_diff > 0);

    $guest_night_difference = $total_sum_data_guest_night - $total_sum_pre_data_guest_night;
    $guest_night_act_bud_diff = $total_sum_data_guest_night - $total_sum_data_guest_night_budget;
    $guest_night_budget_variation = ($total_sum_data_guest_night_budget != 0) ? round(($guest_night_act_bud_diff * 100) / $total_sum_data_guest_night_budget, $percentage_decimal) : 0;
    $drawRow('Guest nights', number_format($total_sum_data_guest_night), number_format($total_sum_pre_data_guest_night), number_format($total_sum_data_guest_night_budget),
        number_format($guest_night_difference), $total_sum_data_guest_night_variation . '%', '',
        number_format($guest_night_act_bud_diff), number_format($guest_night_budget_variation, $percentage_decimal) . '%', $guest_night_difference > 0, $guest_night_act_bud_diff > 0);

    $cdd_difference = $total_sum_data_cdd - $total_sum_pre_data_cdd;
    $drawRow('CDD', round($total_sum_data_cdd, 3), number_format($total_sum_pre_data_cdd), '', number_format($cdd_difference), $total_sum_data_cdd_variation . '%', '', '', '', $cdd_difference > 0, false);

    $hdd_difference = $total_sum_data_hdd - $total_sum_pre_data_hdd;
    $drawRow('HDD', round($total_sum_data_hdd, 3), round($total_sum_pre_data_hdd, 3), '', number_format($hdd_difference), $total_sum_data_hdd_variation . '%', '', '', '', $hdd_difference > 0, false);

    if ($site_detail['show_utility_fleet']) {
        $drawSection('Fleet fuel', '#A98BD1');
        $drawRow('Consumption (Litres)', number_format($total_sum_data_fleet_petrol), number_format($total_sum_pre_data_fleet_petrol), '',
            number_format($fleet_consumption_difference), number_format($fleet_consumption_variation, $percentage_decimal) . '%', '', '', '', $fleet_consumption_difference > 0, false);
        $drawRow('Total Cost', num_format_without_currency($total_sum_data_total_fleet_petrol_cost, 0, $isLocal), num_format_without_currency($total_sum_pre_data_total_fleet_petrol_cost, 0, $isLocal), '',
            num_format_without_currency($fleet_cost_difference, 0, $isLocal), number_format($fleet_cost_variation, $percentage_decimal) . '%', '', '', '', $fleet_cost_difference > 0, false);
    }

    if ($site_detail['show_utility_electricity']) {
        $drawSection(lang('electricity'), '#5B9BE8');
        $electricity_tariff_cl_diff = $total_sum_data_electricity_tariff - $total_sum_pre_data_electricity_tariff;
        $electricity_cost_cl_diff = $total_sum_data_electricity - $total_sum_pre_data_electricity;
        $electricity_actual_budget_diff = $currentBudgetActualData["total_electricity_kwh_actual"] - $currentBudgetActualData["total_electricity_kwh_budget"];
        $electricity_actual_budget_diff_pct = ($currentBudgetActualData["total_electricity_kwh_budget"] != 0) ? $electricity_actual_budget_diff * 100 / $currentBudgetActualData["total_electricity_kwh_budget"] : 0;
        $electricity_actual_budget_tariff_diff = $electricity_tariff_actual - $electricity_tariff_budget;
        $elecricity_act_bud_cost_diff = $currentBudgetActualData["total_electricity_cost_actual"] - $currentBudgetActualData["total_electricity_cost_budget"];
        $electricity_act_bud_pr_rn_diff = $electricity_per_room_night_actual - $electricity_per_room_night_budget;

        $drawRow('Consumption (kWh)', ($total_sum_pre_data_electricity != 0) ? number_format($total_sum_data_electricity_kwh) : 0, number_format($total_sum_pre_data_electricity_kwh), number_format($currentBudgetActualData["total_electricity_kwh_budget"]),
            number_format($electricity_consumption_difference), number_format($electricity_consumption_variation, $percentage_decimal) . '%', num_format_without_currency($electricity_ly, 0, $isLocal),
            number_format($electricity_actual_budget_diff), number_format($electricity_actual_budget_diff_pct, $percentage_decimal) . '%', $electricity_consumption_difference > 0, $electricity_actual_budget_diff > 0);
        $drawRow('Average tariff', num_format_without_currency($total_sum_data_electricity_tariff, $value_decimal, $isLocal), num_format_without_currency($total_sum_pre_data_electricity_tariff, $value_decimal, $isLocal), num_format_without_currency($electricity_tariff_budget, $value_decimal, $isLocal),
            num_format_without_currency($electricity_tariff_cl_diff, $value_decimal, $isLocal), number_format($total_sum_data_electricity_tariff_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($electricity_actual_budget_tariff_diff, $value_decimal, $isLocal), number_format($electricity_tariff_variation, $percentage_decimal) . '%', $electricity_tariff_cl_diff > 0, $electricity_actual_budget_tariff_diff > 0);
        $drawRow('Total Cost', num_format_without_currency($total_sum_data_electricity, 0, $isLocal), num_format_without_currency($total_sum_pre_data_electricity, 0, $isLocal), num_format_without_currency($currentBudgetActualData["total_electricity_cost_budget"], 0, $isLocal),
            num_format_without_currency($electricity_cost_cl_diff, 0, $isLocal), number_format($total_sum_data_electricity_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($elecricity_act_bud_cost_diff, 0, $isLocal), number_format($electricity_cost_variation, $percentage_decimal) . '%', $electricity_cost_cl_diff > 0, $elecricity_act_bud_cost_diff > 0);
        $drawRow('Consumption/Room Night', number_format($total_sum_data_electricity_per_room_night, $percentage_decimal), number_format($total_sum_pre_data_electricity_per_room_night, $percentage_decimal), number_format($electricity_per_room_night_budget, $percentage_decimal),
            number_format($electricity_per_room_night_difference_value, $percentage_decimal), number_format($electricity_per_room_night_difference_percent, $percentage_decimal) . '%', '',
            number_format($electricity_act_bud_pr_rn_diff, $percentage_decimal), number_format($electricity_per_room_night_variation, $percentage_decimal) . '%', $electricity_per_room_night_difference_value > 0, $electricity_act_bud_pr_rn_diff > 0);
    }

    if ($site_detail['show_utility_water']) {
        $drawSection(lang('water'), '#2E9E6A');
        $water_cl_cost_diff = $total_sum_data_water - $total_sum_pre_data_water;
        $water_act_bud_diff = $currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"];
        $water_act_bud_diff_pct = ($currentBudgetActualData["water_total_consumption_budget"] != 0) ? ($currentBudgetActualData["water_total_consumption_actual"] - $currentBudgetActualData["water_total_consumption_budget"]) * 100 / $currentBudgetActualData["water_total_consumption_budget"] : 0;
        $water_act_bud_tariff_diff = $water_tariff_actual - $water_tariff_budget;
        $water_act_bud_cost_diff = $currentBudgetActualData["water_total_consumption_cost_actual"] - $currentBudgetActualData["water_total_consumption_cost_budget"];
        $water_act_bud_pr_rn_diff = $water_per_room_night_actual - $water_per_room_night_budget;

        $drawRow('Consumption (m3)', number_format((float) $total_sum_data_water_consumption), number_format((float) $total_sum_pre_data_water_consumption), number_format((float) $currentBudgetActualData["water_total_consumption_budget"]),
            number_format($water_consumption_difference), number_format($water_consumption_variation, $percentage_decimal) . '%', num_format_without_currency($water_ly, 0, $isLocal),
            number_format($water_act_bud_diff), number_format($water_act_bud_diff_pct, $percentage_decimal) . '%', $water_consumption_difference > 0, $water_act_bud_diff > 0);
        $drawRow('Average tariff', num_format_without_currency((float) $total_sum_data_water_tariff, $value_decimal, $isLocal), num_format_without_currency((float) $total_sum_pre_data_water_tariff, $value_decimal, $isLocal), num_format_without_currency($water_tariff_budget, $value_decimal, $isLocal),
            num_format_without_currency($water_difference, $value_decimal, $isLocal), number_format($water_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($water_act_bud_tariff_diff, $value_decimal, $isLocal), number_format($water_tariff_variation, $percentage_decimal) . '%', $water_difference > 0, $water_act_bud_tariff_diff > 0);
        $drawRow('Total Cost', num_format_without_currency($total_sum_data_water, 0, $isLocal), num_format_without_currency($total_sum_pre_data_water, 0, $isLocal), num_format_without_currency($currentBudgetActualData["water_total_consumption_cost_budget"], 0, $isLocal),
            num_format_without_currency($water_cl_cost_diff, 0, $isLocal), number_format($total_sum_data_water_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($water_act_bud_cost_diff, 0, $isLocal), number_format($water_cost_variation, $percentage_decimal) . '%', $total_sum_data_water_variation > 0, $water_act_bud_cost_diff > 0);
        $drawRow('Consumption/Room Night', number_format($total_sum_data_water_per_room_night, $percentage_decimal), number_format($total_sum_pre_data_water_per_room_night, $percentage_decimal), number_format($water_per_room_night_budget, $percentage_decimal),
            number_format($water_per_room_night_difference_value, $percentage_decimal), number_format($water_per_room_night_difference_percent, $percentage_decimal) . '%', '',
            number_format($water_act_bud_pr_rn_diff, $percentage_decimal), number_format($water_per_room_night_variation, $percentage_decimal) . '%', $water_per_room_night_difference_value > 0, $water_act_bud_pr_rn_diff > 0);
    }

    if ($site_detail['show_utility_district_cooling']) {
        $drawSection(lang('cooling_district'), '#2D55A8');
        $cooling_district_cl_cost_diff = $total_sum_data_cooling_district - $total_sum_pre_data_cooling_district;
        $district_cooling_act_bud_diff = $currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"];
        $district_cooling_act_bud_diff_pct = ($currentBudgetActualData["district_cooling_budget"] != 0) ? ($currentBudgetActualData["district_cooling_actual"] - $currentBudgetActualData["district_cooling_budget"]) * 100 / $currentBudgetActualData["district_cooling_budget"] : 0;
        $district_cooling_act_bud_tariff_diff = $district_cooling_tariff_actual - $district_cooling_tariff_budget;
        $district_cooling_act_bud_cost_diff = $total_sum_data_cooling_district - $currentBudgetActualData["district_cooling_cost_budget"];
        $district_cooling_act_bud_cost_variation = ($currentBudgetActualData["district_cooling_cost_budget"] != 0) ? ($district_cooling_act_bud_cost_diff * 100) / $currentBudgetActualData["district_cooling_cost_budget"] : 0;
        $district_cooling_act_bud_pr_rn_diff = $district_cooling_per_room_night_actual - $district_cooling_per_room_night_budget;

        $drawRow('Consumption (RTH)', number_format($total_sum_data_cooling_district_consumption), number_format($total_sum_pre_data_cooling_district_consumption), number_format($currentBudgetActualData["district_cooling_budget"]),
            number_format($cooling_district_consumption_difference), number_format($cooling_district_consumption_variation, $percentage_decimal) . '%', num_format_without_currency($cooling_district_ly, 0, $isLocal),
            number_format($district_cooling_act_bud_diff), number_format($district_cooling_act_bud_diff_pct, $percentage_decimal) . '%', $cooling_district_consumption_difference > 0, $district_cooling_act_bud_diff > 0);
        $drawRow('Average tariff', num_format_without_currency($total_sum_data_cooling_district_tariff, $value_decimal, $isLocal), num_format_without_currency($total_sum_pre_data_cooling_district_tariff, $value_decimal, $isLocal), num_format_without_currency($district_cooling_tariff_budget, $value_decimal, $isLocal),
            num_format_without_currency($cooling_district_difference, $value_decimal, $isLocal), number_format($cooling_district_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($district_cooling_act_bud_tariff_diff, $value_decimal, $isLocal), number_format($district_cooling_tariff_variation, $percentage_decimal) . '%', $cooling_district_difference > 0, $district_cooling_act_bud_tariff_diff > 0);
        $drawRow('Total Cost', num_format_without_currency($total_sum_data_cooling_district, 0, $isLocal), num_format_without_currency($total_sum_pre_data_cooling_district, 0, $isLocal), num_format_without_currency($currentBudgetActualData["district_cooling_cost_budget"], 0, $isLocal),
            num_format_without_currency($cooling_district_cl_cost_diff, 0, $isLocal), number_format($total_sum_data_cooling_district_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($district_cooling_act_bud_cost_diff, 0, $isLocal), number_format($district_cooling_act_bud_cost_variation, $percentage_decimal) . '%', $total_sum_data_cooling_district_variation > 0, $district_cooling_act_bud_cost_diff > 0);
        $drawRow('Consumption/Room Night', number_format($total_sum_data_cooling_district_per_room_night, $percentage_decimal), number_format($total_sum_pre_data_cooling_district_per_room_night, $percentage_decimal), number_format($district_cooling_per_room_night_budget, $percentage_decimal),
            number_format($cooling_district_per_room_night_difference_value, $percentage_decimal), number_format($cooling_district_per_room_night_difference_percent, $percentage_decimal) . '%', '',
            number_format($district_cooling_act_bud_pr_rn_diff, $percentage_decimal), number_format($district_cooling_per_room_night_variation, $percentage_decimal) . '%', $cooling_district_per_room_night_difference_value > 0, $district_cooling_act_bud_pr_rn_diff > 0);
    }

    if (isset($site_detail['show_utility_lpg']) && $site_detail['show_utility_lpg']) {
        $drawSection(lang('lpg'), '#E8A33D');
        $lpg_cl_cost_diff = $total_sum_data_lpg - $total_sum_pre_data_lpg;
        $lpg_act_bud_diff = $currentBudgetActualData["total_lpg_actual"] - $currentBudgetActualData["total_lpg_budget"];
        $lpg_act_bud_diff_pct = ($currentBudgetActualData["total_lpg_budget"] != 0) ? $lpg_act_bud_diff * 100 / $currentBudgetActualData["total_lpg_budget"] : 0;
        $lpg_act_bud_tarrif_diff = $lpg_tariff_actual - $lpg_tariff_budget;
        $lpg_act_bud_cost_diff = $currentBudgetActualData["total_lpg_cost_actual"] - $currentBudgetActualData["total_lpg_cost_budget"];
        $lpg_act_bud_pr_rn_diff = $lpg_per_room_night_actual - $lpg_per_room_night_budget;

        $drawRow('Consumption (Litres)', number_format($total_sum_data_lpg_consumption), number_format($total_sum_pre_data_lpg_consumption), number_format($currentBudgetActualData["total_lpg_budget"]),
            number_format($lpg_consumption_difference), number_format($lpg_consumption_variation, $percentage_decimal) . '%', num_format_without_currency($lpg_ly, 0, $isLocal),
            number_format($lpg_act_bud_diff), number_format($lpg_act_bud_diff_pct, $percentage_decimal) . '%', $lpg_consumption_difference > 0, $lpg_act_bud_diff > 0);
        $drawRow('Total Cost', num_format_without_currency($total_sum_data_lpg, 0, $isLocal), num_format_without_currency($total_sum_pre_data_lpg, 0, $isLocal), num_format_without_currency($currentBudgetActualData["total_lpg_cost_budget"], 0, $isLocal),
            num_format_without_currency($lpg_cl_cost_diff, 0, $isLocal), number_format($total_sum_data_lpg_variation, $percentage_decimal) . '%', '',
            num_format_without_currency($lpg_act_bud_cost_diff, 0, $isLocal), number_format($lpg_cost_variation, $percentage_decimal) . '%', $lpg_cl_cost_diff > 0, $lpg_act_bud_cost_diff > 0);
        $drawRow('Consumption/Room Night', number_format($total_sum_data_lpg_per_room_night, $percentage_decimal), number_format($total_sum_pre_data_lpg_per_room_night, $percentage_decimal), number_format($lpg_per_room_night_budget, $percentage_decimal),
            number_format($lpg_per_room_night_difference_value, $percentage_decimal), number_format($lpg_per_room_night_difference_percent, $percentage_decimal) . '%', '',
            number_format($lpg_act_bud_pr_rn_diff, $percentage_decimal), number_format($lpg_per_room_night_variation, $percentage_decimal) . '%', $lpg_per_room_night_difference_value > 0, $lpg_act_bud_pr_rn_diff > 0);
    }

    // ---- total ----
    $drawSection('Total', '#17375E');
    $variation = $total_sum_data_sum - $total_cost_budget;
    $variation_percentage = ($total_cost_budget != 0) ? ($variation * 100) / $total_cost_budget : 0;
    $totRowY = $pdf->GetY();
    mn_pdf_fill($pdf, '#EEF4FA');
    $pdf->Rect($left, $totRowY, array_sum($colW), $rowH * 3, 'F');
    $pdf->setInterFont('B', 6.3);
    $drawRow('Total Cost', num_format_without_currency($total_sum_data_sum, 0, $isLocal), num_format_without_currency($total_sum_pre_data_sum, 0, $isLocal), num_format_without_currency($total_cost_budget, 0, $isLocal),
        num_format_without_currency($total_sum_difference_value, 0, $isLocal), number_format($total_sum_difference_percent, $percentage_decimal) . '%', num_format_without_currency($total_cost_ly, 0, $isLocal),
        num_format_without_currency($variation, 0, $isLocal), number_format($variation_percentage, $percentage_decimal) . '%', $total_sum_difference_value > 0, $variation > 0);
    $pdf->setInterFont('B', 6.3);
    $drawRow('Total Cost / Room Night', num_format_without_currency($total_sum_data_utility_cost_per_roomnight, $percentage_decimal, $isLocal), num_format_without_currency((float) $total_sum_pre_data_utility_cost_per_roomnight, $percentage_decimal, $isLocal), '',
        num_format_without_currency((float) $data_utility_cost_per_roomnight_difference, $percentage_decimal, $isLocal), number_format((float) $data_utility_cost_per_roomnight_variation, $percentage_decimal) . '%', '', '', '', $data_utility_cost_per_roomnight_difference > 0, false);
    $pdf->setInterFont('B', 6.3);
    $drawRow('Total Cost / m2', num_format_without_currency($total_sum_data_utility_cost_per_m2, $percentage_decimal, $isLocal), num_format_without_currency($total_sum_pre_data_utility_cost_per_m2, $percentage_decimal, $isLocal), '',
        num_format_without_currency($data_utility_cost_per_m2_difference, $percentage_decimal, $isLocal), number_format($data_utility_cost_per_m2_variation, $percentage_decimal) . '%', '', '', '', $data_utility_cost_per_m2_difference > 0, false);

    // Only bottom (horizontal) borders are used in this table - no outer box or
    // vertical column separators. $drawRow() already underlines each row; here we
    // just close off the header block and the very bottom of the table.
    $tableBottomY = $pdf->GetY();
    mn_pdf_draw($pdf, '#D5DFE9');
    $pdf->SetLineWidth(0.2);
    $pdf->Line($left, $tableBottomY, $left + array_sum($colW), $tableBottomY);

    $pdf->SetTextColor(0, 0, 0);

    // re-enable auto-page-break for whatever content follows this block
    // (disabled in admin_landing_pdf_reports_progress_table_new.php so TCPDF
    // wouldn't silently fragment the cards/table across pages)
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
}
