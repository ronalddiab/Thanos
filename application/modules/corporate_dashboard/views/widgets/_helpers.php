<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/*
 * Corporate Sustainability Dashboard view helpers.
 *
 * These helpers do NOT create a new data source. They calculate presentation
 * values from the existing model output:
 *
 *   $dashboard['meta']
 *   $dashboard['sites']
 *   $dashboard['series']
 *   $dashboard['dq']
 */

if (!function_exists('csd_num')) {
    function csd_num($value, $decimals = 2)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '&ndash;';
        }

        return number_format((float) $value, $decimals);
    }
}

if (!function_exists('csd_big')) {
    function csd_big($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '&ndash;';
        }

        $value = (float) $value;
        $abs = abs($value);

        if ($abs >= 1000000000) {
            return number_format($value / 1000000000, 2) . 'bn';
        }

        if ($abs >= 1000000) {
            return number_format($value / 1000000, 2) . 'm';
        }

        if ($abs >= 1000) {
            return number_format($value / 1000, 1) . 'k';
        }

        return number_format($value, 0);
    }
}

if (!function_exists('csd_pct')) {
    function csd_pct($value, $decimals = 2)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '&ndash;';
        }

        $value = (float) $value;

        return ($value >= 0 ? '+' : '')
            . number_format($value, $decimals)
            . '%';
    }
}

if (!function_exists('csd_cls')) {
    function csd_cls($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return 'na';
        }

        return ((float) $value < 0) ? 'good' : 'bad';
    }
}

if (!function_exists('csd_roll')) {
    /*
     * Exact PHP equivalent of the dashboard's JS roll() calculation.
     *
     * Intensity is total / total, never an average of property ratios.
     */
    function csd_roll($rows, $metric, $targets = array())
    {
        $ct = 0;
        $pt = 0;
        $cd = 0;
        $pd = 0;
        $n  = 0;

        foreach ($rows as $row) {
            $mt = isset($row[$metric . '_mt']) ? $row[$metric . '_mt'] : null;

            if (!is_array($mt)) {
                continue;
            }

            $rct = isset($mt['ct']) ? (float) $mt['ct'] : 0;
            $rpt = isset($mt['pt']) ? (float) $mt['pt'] : 0;
            $rcd = isset($mt['cd']) ? (float) $mt['cd'] : 0;
            $rpd = isset($mt['pd']) ? (float) $mt['pd'] : 0;

            if ($rcd > 0 && $rpd > 0) {
                $ct += $rct;
                $pt += $rpt;
                $cd += $rcd;
                $pd += $rpd;
                $n++;
            }
        }

        if (!$n || $cd <= 0 || $pd <= 0 || $pt == 0) {
            return null;
        }

        $ci = $ct / $cd;
        $pi = $pt / $pd;

        $target = isset($targets[$metric]) ? (float) $targets[$metric] : 0;
        $tg = $pi * (1 + ($target / 100));

        return array(
            'ci'   => $ci,
            'pi'   => $pi,
            'tg'   => $tg,
            'ct'   => $ct,
            'pt'   => $pt,
            'cd'   => $cd,
            'pd'   => $pd,
            'n'    => $n,
            'yoy'  => $pi != 0 ? (($ci / $pi) - 1) * 100 : null,
            'perf' => $tg != 0 ? (($ci / $tg) - 1) * 100 : null,
            'tot'  => $pt != 0 ? (($ct / $pt) - 1) * 100 : null,
            'den'  => $pd != 0 ? (($cd / $pd) - 1) * 100 : null
        );
    }
}

if (!function_exists('csd_filter_sites')) {
    function csd_filter_sites($sites)
    {
        $region  = isset($_GET['region']) ? trim($_GET['region']) : '';
        $country = isset($_GET['country']) ? trim($_GET['country']) : '';
        $brand   = isset($_GET['brand']) ? trim($_GET['brand']) : '';
        $site    = isset($_GET['site']) ? trim($_GET['site']) : '';
        $search  = isset($_GET['search']) ? trim($_GET['search']) : '';

        $filtered = array();

        foreach ($sites as $row) {
            if ($region !== '' && (!isset($row['region']) || $row['region'] !== $region)) {
                continue;
            }

            if ($country !== '' && (!isset($row['country']) || $row['country'] !== $country)) {
                continue;
            }

            if ($brand !== '') {
                $property_name = isset($row['site'])
                    ? strtolower($row['site'])
                    : '';

                if (strpos($property_name, strtolower($brand)) === false) {
                    continue;
                }
            }

            if ($site !== '' && (!isset($row['site']) || $row['site'] !== $site)) {
                continue;
            }

            if ($search !== '') {
                $name = isset($row['site']) ? strtolower($row['site']) : '';

                if (strpos($name, strtolower($search)) === false) {
                    continue;
                }
            }

            $filtered[] = $row;
        }

        return $filtered;
    }
}

if (!function_exists('csd_unique_values')) {
    function csd_unique_values($rows, $key)
    {
        $values = array();

        foreach ($rows as $row) {
            if (isset($row[$key]) && $row[$key] !== '') {
                $values[] = $row[$key];
            }
        }

        $values = array_values(array_unique($values));
        sort($values, SORT_NATURAL | SORT_FLAG_CASE);

        return $values;
    }
}

if (!function_exists('csd_arrow')) {
    function csd_arrow($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '';
        }

        $value = (float) $value;
        $good = $value < 0;
        $color = $good ? '#5cb85c' : '#e8544a';

        if ($value < 0) {
            $path = 'M4 0h4v9h3.2L6 16 1 9h3z';
        } else {
            $path = 'M4 16h4V7h3.2L6 0 1 7h3z';
        }

        return '<svg class="arr" width="9.36" height="13" viewBox="0 0 12 16" aria-hidden="true">'
            . '<path d="' . $path . '" fill="' . $color . '"></path>'
            . '</svg>';
    }
}

if (!function_exists('csd_activity_arrow')) {
    function csd_activity_arrow($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return '';
        }

        $value = (float) $value;
        $good = $value > 0;
        $color = $good ? '#5cb85c' : '#e8544a';

        if ($value < 0) {
            $path = 'M4 0h4v9h3.2L6 16 1 9h3z';
        } else {
            $path = 'M4 16h4V7h3.2L6 0 1 7h3z';
        }

        return '<svg class="arr" width="9.36" height="13" viewBox="0 0 12 16" aria-hidden="true">'
            . '<path d="' . $path . '" fill="' . $color . '"></path>'
            . '</svg>';
    }
}

if (!function_exists('csd_mode_toggle')) {
    /*
     * One of these renders inside each chart card's header (matching the
     * approved prototype). They all read/write the SAME global mode - see
     * widgets/filters.php's JS - so clicking any one of them reloads every
     * card on the page together, never just the card it sits in.
     */
    function csd_mode_toggle($active_mode)
    {
        $energy_on = $active_mode !== 'carbon';

        return '<span class="mode-toggle">'
            . '<button type="button" data-mode="energy" class="' . ($energy_on ? 'on' : '') . '">Energy</button>'
            . '<button type="button" data-mode="carbon" class="' . (!$energy_on ? 'on' : '') . '">Carbon</button>'
            . '</span>';
    }
}

$dashboard = isset($dashboard) && is_array($dashboard) ? $dashboard : array();

$meta   = isset($dashboard['meta']) && is_array($dashboard['meta']) ? $dashboard['meta'] : array();
$sites  = isset($dashboard['sites']) && is_array($dashboard['sites']) ? $dashboard['sites'] : array();
$series = isset($dashboard['series']) && is_array($dashboard['series']) ? $dashboard['series'] : array();
$dq     = isset($dashboard['dq']) && is_array($dashboard['dq']) ? $dashboard['dq'] : array();

$visible_sites = csd_filter_sites($sites);

$targets = isset($meta['targets']) && is_array($meta['targets'])
    ? $meta['targets']
    : array(
        'energy' => -2,
        'carbon' => -3,
        'water'  => 0
    );

$energy_roll = csd_roll($visible_sites, 'energy', $targets);
$carbon_roll = csd_roll($visible_sites, 'carbon', $targets);
$water_roll  = csd_roll($visible_sites, 'water', $targets);

$kpi_rolls = array(
    'energy' => $energy_roll,
    'carbon' => $carbon_roll,
    'water'  => $water_roll
);

$regions = array();

foreach ($visible_sites as $row) {
    $region = isset($row['region']) && $row['region'] !== ''
        ? $row['region']
        : 'Unassigned';

    if (!isset($regions[$region])) {
        $regions[$region] = array();
    }

    $regions[$region][] = $row;
}

ksort($regions, SORT_NATURAL | SORT_FLAG_CASE);

/*
 * Global Energy/Carbon mode - one toggle drives every card on the page
 * (ranked list, trend chart, composition donut), not an independent
 * per-card switch. See widgets/filters.php for the control itself.
 *
 * $mode only decides which version is VISIBLE on first load (so the
 * page still honors ?mode=carbon as a shareable link/initial state).
 * Both versions are computed and rendered for every card below; the
 * toggle itself just shows/hides them client-side - see the JS in
 * filters.php - so flipping it never needs another server round trip.
 */
$mode = isset($_GET['mode']) && $_GET['mode'] === 'carbon' ? 'carbon' : 'energy';

$rank_sets = array();

foreach (array('energy' => 'energy_yoy', 'carbon' => 'carbon_yoy') as $rank_mode => $rank_metric) {

    $rows = array();

    foreach ($visible_sites as $row) {
        if (isset($row[$rank_metric]) && $row[$rank_metric] !== null && is_numeric($row[$rank_metric])) {
            $rows[] = $row;
        }
    }

    usort($rows, function ($a, $b) use ($rank_metric) {
        return ((float) $a[$rank_metric] <=> (float) $b[$rank_metric]);
    });

    $max = 1;

    foreach ($rows as $row) {
        $max = max($max, abs((float) $row[$rank_metric]));
    }

    $rank_sets[$rank_mode] = array(
        'metric' => $rank_metric,
        'best'   => array_slice($rows, 0, 5),
        'worst'  => array_reverse(array_slice($rows, -5)),
        'max'    => $max
    );
}

$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

$trend_current = array_fill(0, 12, 0);
$trend_previous = array_fill(0, 12, 0);
$trend_rn_current = array_fill(0, 12, 0);
$trend_rn_previous = array_fill(0, 12, 0);

/*
 * Carbon trend uses guest nights as its denominator, not room nights -
 * same convention as everywhere else in this model (build_site_row(),
 * calculate_year_data()).
 */
$trend_carbon_current = array_fill(0, 12, 0);
$trend_carbon_previous = array_fill(0, 12, 0);
$trend_guests_current = array_fill(0, 12, 0);
$trend_guests_previous = array_fill(0, 12, 0);

foreach ($visible_sites as $row) {
    $site_name = isset($row['site']) ? $row['site'] : '';

    if (!isset($series[$site_name]) || !is_array($series[$site_name])) {
        continue;
    }

    $site_series = $series[$site_name];

    for ($i = 0; $i < 12; $i++) {
        $cur = isset($site_series['cur'][$i]) ? $site_series['cur'][$i] : null;
        $pri = isset($site_series['pri'][$i]) ? $site_series['pri'][$i] : null;
        $rn_cur = isset($site_series['rn_cur'][$i]) ? $site_series['rn_cur'][$i] : null;
        $rn_pri = isset($site_series['rn_pri'][$i]) ? $site_series['rn_pri'][$i] : null;

        /*
         * Like-for-like gate: a site/month only contributes to EITHER
         * year's total when BOTH years have valid data for it - same
         * principle calculate_like_for_like()/csd_roll() already use
         * elsewhere in this model. Without this, a new hotel with only
         * a current year (no prior-year comparison) inflates the
         * current-year line with nothing to offset it, and a site
         * with prior-year guest/room nights but zero energy/carbon
         * (e.g. missing emission factors) drags the prior-year line
         * down - exactly the "chart doesn't match the real YoY move"
         * symptom this was causing.
         */
        $energy_both_years_valid =
            $cur !== null && $rn_cur !== null && (float) $rn_cur > 0 &&
            $pri !== null && $rn_pri !== null && (float) $rn_pri > 0;

        if ($energy_both_years_valid) {
            $trend_current[$i] += (float) $cur;
            $trend_rn_current[$i] += (float) $rn_cur;
            $trend_previous[$i] += (float) $pri;
            $trend_rn_previous[$i] += (float) $rn_pri;
        }

        $carbon_cur = isset($site_series['carbon_cur'][$i]) ? $site_series['carbon_cur'][$i] : null;
        $carbon_pri = isset($site_series['carbon_pri'][$i]) ? $site_series['carbon_pri'][$i] : null;
        $guests_cur = isset($site_series['guests_cur'][$i]) ? $site_series['guests_cur'][$i] : null;
        $guests_pri = isset($site_series['guests_pri'][$i]) ? $site_series['guests_pri'][$i] : null;

        /*
         * carbon_cur/carbon_pri use > 0, not just !== null: a site
         * with no emission factors configured for a given year (e.g.
         * a newly-added property missing its base year's factors)
         * computes a real, non-null carbon value of exactly 0 even
         * though it has real guest nights that month - that 0 is a
         * missing-data artifact, not an actual reading, and still
         * drags the average down the same way a null would if let
         * through here.
         */
        $carbon_both_years_valid =
            $carbon_cur !== null && (float) $carbon_cur > 0 && $guests_cur !== null && (float) $guests_cur > 0 &&
            $carbon_pri !== null && (float) $carbon_pri > 0 && $guests_pri !== null && (float) $guests_pri > 0;

        if ($carbon_both_years_valid) {
            $trend_carbon_current[$i] += (float) $carbon_cur;
            $trend_guests_current[$i] += (float) $guests_cur;
            $trend_carbon_previous[$i] += (float) $carbon_pri;
            $trend_guests_previous[$i] += (float) $guests_pri;
        }
    }
}

$trend_current_intensity = array();
$trend_previous_intensity = array();
$trend_carbon_current_intensity = array();
$trend_carbon_previous_intensity = array();

for ($i = 0; $i < 12; $i++) {
    $trend_current_intensity[$i] = $trend_rn_current[$i] > 0
        ? $trend_current[$i] / $trend_rn_current[$i]
        : null;

    $trend_previous_intensity[$i] = $trend_rn_previous[$i] > 0
        ? $trend_previous[$i] / $trend_rn_previous[$i]
        : null;

    $trend_carbon_current_intensity[$i] = $trend_guests_current[$i] > 0
        ? $trend_carbon_current[$i] / $trend_guests_current[$i]
        : null;

    $trend_carbon_previous_intensity[$i] = $trend_guests_previous[$i] > 0
        ? $trend_carbon_previous[$i] / $trend_guests_previous[$i]
        : null;
}

$mix_totals = array(
    'elec' => 0,
    'dc'   => 0,
    'lpg'  => 0,
    'fo'   => 0,
    'ng'   => 0,
    'dh'   => 0
);

$mix_labels = array(
    'elec' => 'Electricity',
    'dc'   => 'District cooling',
    'lpg'  => 'LPG',
    'fo'   => 'Fuel oil',
    'ng'   => 'Natural gas',
    'dh'   => 'District heating'
);

$mix_total = 0;

foreach ($visible_sites as $row) {
    $mt = isset($row['energy_mt']) && is_array($row['energy_mt'])
        ? $row['energy_mt']
        : array();

    $ct = isset($mt['ct']) ? (float) $mt['ct'] : 0;

    if ($ct <= 0 || !isset($row['mix']) || !is_array($row['mix'])) {
        continue;
    }

    foreach ($mix_totals as $key => $unused) {
        $share = isset($row['mix'][$key]) ? (float) $row['mix'][$key] : 0;
        $value = ($share / 100) * $ct;

        $mix_totals[$key] += $value;
        $mix_total += $value;
    }
}

/*
 * Carbon composition - same structure as the Energy mix above, but
 * scaled against each site's like-for-like carbon total (carbon_mt)
 * and split using carbon_mix (each utility weighted by its OWN
 * emission factor), not the kWh-based mix.
 */
$carbon_mix_totals = array(
    'elec' => 0,
    'dc'   => 0,
    'lpg'  => 0,
    'fo'   => 0,
    'ng'   => 0,
    'dh'   => 0
);

$carbon_mix_total = 0;

foreach ($visible_sites as $row) {
    $cmt = isset($row['carbon_mt']) && is_array($row['carbon_mt'])
        ? $row['carbon_mt']
        : array();

    $cct = isset($cmt['ct']) ? (float) $cmt['ct'] : 0;

    if ($cct <= 0 || !isset($row['carbon_mix']) || !is_array($row['carbon_mix'])) {
        continue;
    }

    foreach ($carbon_mix_totals as $key => $unused) {
        $share = isset($row['carbon_mix'][$key]) ? (float) $row['carbon_mix'][$key] : 0;
        $value = ($share / 100) * $cct;

        $carbon_mix_totals[$key] += $value;
        $carbon_mix_total += $value;
    }
}

$sort_key = isset($_GET['sort']) ? $_GET['sort'] : 'energy_yoy';
$sort_dir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'desc' : 'asc';

$allowed_sort_keys = array(
    'site', 'country', 'brand', 'energy_cur', 'energy_match_i',
    'energy_yoy', 'carbon_yoy', 'water_yoy', 'energy_perf', 'rn_cur'
);

if (!in_array($sort_key, $allowed_sort_keys, true)) {
    $sort_key = 'energy_yoy';
}

usort($visible_sites, function ($a, $b) use ($sort_key, $sort_dir) {
    $av = isset($a[$sort_key]) ? $a[$sort_key] : null;
    $bv = isset($b[$sort_key]) ? $b[$sort_key] : null;

    if ($av === null && $bv === null) {
        return 0;
    }

    if ($av === null) {
        return 1;
    }

    if ($bv === null) {
        return -1;
    }

    if (is_string($av) || is_string($bv)) {
        $result = strcasecmp((string) $av, (string) $bv);
    } else {
        $result = ((float) $av <=> (float) $bv);
    }

    return $sort_dir === 'desc' ? -$result : $result;
});

$current_year = isset($meta['cur_year']) ? $meta['cur_year'] : date('Y');
$previous_year = isset($meta['prior_year']) ? $meta['prior_year'] : ($current_year - 1);
$ytd_months = isset($meta['ytd_months']) ? (int) $meta['ytd_months'] : 0;
$period_label = isset($meta['period_label']) ? $meta['period_label'] : '';
$generated = isset($meta['generated']) ? $meta['generated'] : '';
$total_sites = isset($meta['total_sites']) ? (int) $meta['total_sites'] : count($sites);
$source = isset($meta['source']) ? $meta['source'] : '';

$flagged_count = count($dq);
$high_count = 0;

foreach ($dq as $item) {
    if (isset($item['sev']) && $item['sev'] === 'high') {
        $high_count++;
    }
}
?>
