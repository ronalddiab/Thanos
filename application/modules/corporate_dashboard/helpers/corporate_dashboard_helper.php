<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/*
 * Corporate Sustainability Dashboard helpers.
 *
 * Moved here from views/widgets/_helpers.php so they're available to the
 * controller itself (e.g. export_csv(), which calls csd_filter_sites()
 * before any view is ever loaded - a view-only helper file is never
 * included on that code path, which is why the CSV export previously
 * fataled with "Call to undefined function csd_filter_sites()").
 *
 * views/widgets/_helpers.php still defines the same functions (each
 * guarded with function_exists()) for the view-rendering context, so
 * whichever loads first wins and the other's definitions are skipped -
 * no behavior change there, just no longer a single point of failure.
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
