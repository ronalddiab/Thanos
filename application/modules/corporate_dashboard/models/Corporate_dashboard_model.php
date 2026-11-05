<?php
// if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

// class Corporate_dashboard_model extends Base_Model
// {
//     public function get_dashboard_data()
//     {
//     $this->load->model('reports/reports_model');
// 	$this->load->model('sites/sites_model');
// 	$this->load->model('utilities/utilities_model');
// 	$this->load->model('sites/site_waste_model');
//     $this->load->model('regions/regions_model');
//     $this->load->model('countries/countries_model');

//         $file = APPPATH . 'modules/corporate_dashboard/data/dashboard_mock.json';
//         if (!file_exists($file)) {
//             return array('meta' => array('cur_year' => date('Y'), 'prior_year' => date('Y') - 1, 'ytd_months' => 0, 'period_label' => '', 'generated' => date('d F, Y'), 'total_sites' => 0, 'targets' => array('energy' => -2, 'carbon' => -3, 'water' => 0)), 'sites' => array(), 'series' => array(), 'dq' => array());
//         }
//         $data = json_decode(file_get_contents($file), true);
//         $regions = $this->regions_model->get_region_listing();
//         $countries = $this->countries_model->getCountries();
//         return is_array($data) ? $data : array();
//     }
// }
?>

<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Corporate_dashboard_model extends Base_Model
{
    /**
     * Dashboard utility names used by the existing Reports_model.
     */
    private $utility_keys = array(
        'electricity',
        'fuel_oil',
        'lpg',
        'natural_gas',
        'district_heating',
        'district_cooling'
    );

    public function __construct()
    {
        parent::__construct();

        /*
         * Existing HEPP models.
         *
         * Reports_model already contains the application's
         * monthly utility/business calculations.
         */
        $this->load->model('reports/reports_model');
        $this->load->model('sites/sites_model');
        $this->load->model('sites/site_emission_model');
        $this->load->model('utilities/utilities_model');
        $this->load->model('regions/regions_model');
    }

    /**
     * Main public method.
     *
     * Returns the same JSON structure expected by the dashboard UI:
     *
     * meta
     * sites
     * series
     * dq
     */
    public function get_dashboard_data()
    {
        $date = $this->get_dashboard_period();

        $current_year  = $date['current_year'];
        $previous_year = $date['previous_year'];
        $current_month = $date['current_month'];

        /*
         * Get portfolio/sites.
         */
        $sites = $this->get_portfolio_sites();

        if (empty($sites)) {
            return array(
                'meta' => $this->get_meta(
                    $current_year,
                    $previous_year,
                    $current_month,
                    0
                ),
                'sites'  => array(),
                'series' => array(),
                'dq'     => array()
            );
        }

        /*
         * We need the Reports_model result for both years.
         *
         * The existing application uses monthlyUtilityBasedReportByUnit()
         * for this purpose.
         */
        $monthly_data = $this->get_monthly_utility_data(
            $sites,
            $current_year,
            $previous_year,
            $current_month
        );

        /*
         * Build dashboard rows.
         */
        $dashboard_sites = array();
        $series = array();
        $dq = array();

        foreach ($sites as $site) {

            $site_id = (int) $site['site_id'];

            /*
             * Existing utility conversion helper.
             *
             * This is deliberately NOT hardcoded.
             */
            $factors = getMmbtuFactorConversionAllUtility($site_id);

            /*
             * Get this site's monthly records.
             */
            $site_monthly = isset($monthly_data[$site_id])
                ? $monthly_data[$site_id]
                : array();

            /*
             * Build current/prior yearly totals.
             */
            $current = $this->calculate_year_data(
                isset($site_monthly[$current_year])
                    ? $site_monthly[$current_year]
                    : array(),
                $site_id,
                $site,
                $factors
            );

            $previous = $this->calculate_year_data(
                isset($site_monthly[$previous_year])
                    ? $site_monthly[$previous_year]
                    : array(),
                $site_id,
                $site,
                $factors
            );

            $like_for_like = $this->calculate_like_for_like(
                $site_monthly,
                $current_year,
                $previous_year,
                $current_month,
                $site_id,
                $site,
                $factors
            );

            /*
             * Build property-level dashboard row.
             */
            $row = $this->build_site_row(
                $site,
                $current,
                $previous,
                $like_for_like
            );

            $dashboard_sites[] = $row;

            /*
             * Monthly intensity series.
             */
            $series[$row['site']] = $this->build_site_series(
                $site_monthly,
                $current_year,
                $previous_year,
                $current_month,
                $site_id,
                $site,
                $factors
            );

            /*
             * Data quality.
             */
            $issues = $this->get_data_quality(
                $site,
                $site_monthly,
                $current_year,
                $previous_year,
                $current_month
            );

            if (!empty($issues)) {
                $dq[] = array(
                    'site'    => $row['site'],
                    'country' => $row['country'],
                    'sev'     => $this->get_quality_severity($issues),
                    'issues'  => $issues
                );
            }
        }

        return array(
            'meta' => $this->get_meta(
                $current_year,
                $previous_year,
                $current_month,
                count($dashboard_sites),
                $sites
            ),

            'sites'  => $dashboard_sites,
            'series' => $series,
            'dq'     => $dq
        );
    }

    /**
     * ---------------------------------------------------------
     * Reporting period
     * ---------------------------------------------------------
     */
    private function get_dashboard_period()
    {
        /*
         * HEPP already exposes CURRENT_YEAR_MAX_MONTH_ID.
         *
         * Use the same reporting convention instead of creating
         * another definition of YTD.
         */
        $current_year = (int) date('Y');

        $current_month = defined('CURRENT_YEAR_MAX_MONTH_ID')
            ? (int) CURRENT_YEAR_MAX_MONTH_ID - 1
            : (int) date('n') - 1;

        // $current_month = 7;

        /*
         * Avoid zero/invalid month.
         */
        if ($current_month < 1) {
            $current_month = 1;
        }

        if ($current_month > 12) {
            $current_month = 12;
        }

        return array(
            'current_year'  => $current_year,
            'previous_year' => $current_year - 1,
            'current_month' => $current_month
        );
    }

    /**
     * ---------------------------------------------------------
     * Portfolio
     * ---------------------------------------------------------
     */
    private function get_portfolio_sites()
    {
        /*
         * We need to use the existing site access mechanism.
         *
         * If your installation already has a corporate/multi-site
         * method for accessible sites, use it here.
         *
         * This fallback query only retrieves basic site information.
         */
        $this->db->select('
            s.id AS site_id,
            s.site_location_name AS site,
            s.city,
            s.country_id,
            s.region_id,
            s.hotel_id,
            s.rooms_keys,

            s.electricity_emission_factor,
            s.fuel_emission_factor,
            s.lpg_emission_factor,
            s.natural_gas_emission_factor,
            s.district_cooling_emission_factor,
            s.district_heating_emission_factor,

            s.energy_intensity_annual_target,
            s.ghg_intensity_annual_target,
            s.water_intensity_annual_target
        ');

        $this->db->from('sites s');

        $this->db->where('s.status =', 1);

        $query = $this->db->get();

        if (!$query->num_rows()) {
            return array();
        }

        $sites = $query->result_array();

        /*
         * We need readable region/country/brand values.
         *
         * Keep this lookup separate for now.
         */
        foreach ($sites as $key => $site) {

            $sites[$key]['region'] = $this->get_region_name(
                $site['region_id']
            );

            $sites[$key]['country'] = $this->get_country_name(
                $site['country_id']
            );

            $sites[$key]['brand'] = $this->get_brand_name(
                $site['hotel_id']
            );
        }

        return $sites;
    }

    private function get_region_name($region_id)
    {
        if (!$region_id) {
            return '';
        }

        $row = $this->db
            ->select('region_name')
            ->from('regions')
            ->where('id', $region_id)
            ->get()
            ->row_array();

        return !empty($row['region_name'])
            ? $row['region_name']
            : '';
    }

    private function get_country_name($country_id)
    {
        if (!$country_id) {
            return '';
        }

        $row = $this->db
            ->select('country')
            ->from('countries')
            ->where('id', $country_id)
            ->get()
            ->row_array();

        return !empty($row['country'])
            ? $row['country']
            : '';
    }

    private function get_brand_name($hotel_id)
    {
        if (!$hotel_id) {
            return '';
        }

        $row = $this->db
            ->select('hotel_name')
            ->from('hotels')
            ->where('id', $hotel_id)
            ->get()
            ->row_array();

        return !empty($row['hotel_name'])
            ? $row['hotel_name']
            : '';
    }

    /**
     * ---------------------------------------------------------
     * Existing HEPP monthly utility report
     * ---------------------------------------------------------
     */
    private function get_monthly_utility_data_bkp(
        $sites,
        $current_year,
        $previous_year,
        $current_month
    ) {
        $indexed = array();

        foreach ($sites as $site) {

            $site_id = (int) $site['site_id'];

            if ($site_id <= 0) {
                continue;
            }

            /*
            * IMPORTANT:
            *
            * Reports_model::monthlyUtilityBasedReportByUnit()
            * uses $this->reports_model->site_id when generating
            * the utilities WHERE clause.
            */
            $this->reports_model->site_id = $site_id;

            $filters = array(
                'startdate' => '1/' . $previous_year,
                'enddate'   => $current_month . '/' . $current_year,

                'start_month' => 1,
                'start_year'  => $previous_year,

                'end_month' => $current_month,
                'end_year'  => $current_year,

                'previous_month' => $current_month,
                'previous_year' => $previous_year,

                'current_year' => $current_year,

                'Progress_previous_year' => $previous_year,

                'CURRENT_YEAR_MAX_MONTH_ID' => $current_month
            );

            /*
            * Use the existing HEPP calculation.
            */
            $results = $this->reports_model
                ->monthlyUtilityBasedReportByUnit($filters);

            if (empty($results)) {
                continue;
            }

            foreach ($results as $result) {

                if (
                    !isset($result['year_id']) ||
                    !isset($result['month_id'])
                ) {
                    continue;
                }

                $year = (int) $result['year_id'];
                $month = (int) $result['month_id'];

                /*
                * We only need the two years for the corporate dashboard.
                */
                if (
                    $year != $current_year &&
                    $year != $previous_year
                ) {
                    continue;
                }

                /*
                * Don't include future months from current year.
                */
                if (
                    $year == $current_year &&
                    $month > $current_month
                ) {
                    continue;
                }

                if (!isset($indexed[$site_id])) {
                    $indexed[$site_id] = array();
                }

                if (!isset($indexed[$site_id][$year])) {
                    $indexed[$site_id][$year] = array();
                }

                $indexed[$site_id][$year][$month] = $result;
            }
        }

        return $indexed;
    }

    private function get_monthly_utility_data(
    $sites,
    $current_year,
    $previous_year,
    $current_month
    ) {
        $indexed = array();

        $site_ids = array();

        foreach ($sites as $site) {

            $site_id = isset($site['site_id'])
                ? (int) $site['site_id']
                : 0;

            if ($site_id > 0) {
                $site_ids[] = $site_id;
            }
        }

        $site_ids = array_values(array_unique($site_ids));

        if (empty($site_ids)) {
            return $indexed;
        }

        /*
        * All values are integers, so this is safe to use
        * directly in the IN() clause.
        */
        $site_ids_sql = implode(',', $site_ids);

        $where_more = "(
            (
                u.year_id = " . (int) $current_year . "
                AND u.month_id >= 1
                AND u.month_id <= " . (int) $current_month . "
            )
            OR
            (
                u.year_id = " . (int) $previous_year . "
                AND u.month_id >= 1
                AND u.month_id <= " . (int) $current_month . "
            )
        )";


        /*
        * ---------------------------------------------------------
        * 3. ONE query for ALL sites
        * ---------------------------------------------------------
        */
        $query = "
            SELECT

                u.site_id,

                COALESCE(u.total_electricity_kwh, 0) AS electricity,
                COALESCE(u.electricity_total_budget, 0) AS electricity_budget,

                COALESCE(u.total_fuel_oil, 0) AS fuel_oil,
                COALESCE(u.fuel_total_budget, 0) AS fuel_budget,

                COALESCE(u.total_lpg, 0) AS lpg,
                COALESCE(u.lpg_total_budget, 0) AS lpg_budget,

                COALESCE(u.total_natural_gas, 0) AS natural_gas,
                COALESCE(u.natural_gas_total_budget, 0) AS natural_gas_budget,

                COALESCE(u.district_heating, 0) AS district_heating,
                COALESCE(u.district_heating_total_budget, 0) AS district_heating_budget,

                COALESCE(u.district_cooling, 0) AS district_cooling,
                COALESCE(u.district_cooling_total_budget, 0) AS district_cooling_budget,

                COALESCE(u.water_total_consumption, 0) AS water,
                COALESCE(u.water_total_consumption_budget, 0) AS water_budget,

                COALESCE(u.water_utility_supply, 0) AS water_utility,
                COALESCE(u.water_irrigation, 0) AS water_irrigation,
                COALESCE(u.waste_water, 0) AS waste_water,
                COALESCE(u.water_Cisterns, 0) AS water_cisterns,

                COALESCE(u.cdd, 0) AS cdd,
                COALESCE(u.hdd, 0) AS hdd,

                u.month_id,
                u.year_id,

                COALESCE(u.total_guests, 0) AS total_guests,
                COALESCE(u.total_room_night, 0) AS total_room_night,
                COALESCE(u.total_room_night_budget, 0) AS total_room_night_budget,

                COALESCE(u.total_guests_budget, 0) AS total_guests_budget,
                COALESCE(u.total_laundered, 0) AS total_laundered,

                COALESCE(u.fleet_petrol, 0) AS fleet_petrol,

                COALESCE(s.site_builtup_area, 0) AS site_builtup_area,
                COALESCE(s.cooled_builtup_area, 0) AS cooled_builtup_area,
                COALESCE(s.rooms_keys, 0) AS rooms_keys

            FROM utilities_cost AS u

            LEFT JOIN sites AS s
                ON s.id = u.site_id

            WHERE u.site_id IN ({$site_ids_sql})
                AND {$where_more}
                AND u.year_id != 0
                AND u.month_id != 0

            ORDER BY
                u.site_id ASC,
                u.year_id ASC,
                u.month_id ASC,
                u.id ASC
        ";

        $result = $this->db->query($query)->result_array();


        /*
        * ---------------------------------------------------------
        * 4. Apply latest rooms_keys and cooled_builtup_area
        * ---------------------------------------------------------
        *
        * This replaces the behavior of:
        *
        * $this->reports_model->fetchUpdatedRoomKeys($result)
        *
        * We do it here so Reports_model doesn't need to change.
        */
        // if (!empty($result)) {

        //     $this->load->model('sites/sites_model');

        //     foreach ($result as $key => $row) {

        //         if (!is_array($row)) {
        //             continue;
        //         }

        //         $row_site_id = !empty($row['site_id'])
        //             ? (int) $row['site_id']
        //             : 0;

        //         if ($row_site_id <= 0) {
        //             continue;
        //         }

        //         foreach (array(
        //             'rooms_keys',
        //             'cooled_builtup_area'
        //         ) as $subkey) {

        //             if (!array_key_exists($subkey, $row)) {
        //                 continue;
        //             }

        //             $dataFetch = array(
        //                 'site_id' => $row_site_id,
        //                 'area_update_field' => $subkey
        //             );

        //             $latestAreaEntry = $this->sites_model
        //                 ->getlatestSiteArea($dataFetch);

        //             if (!empty($latestAreaEntry)) {

        //                 $latestAreaEntry = (array) $latestAreaEntry;

        //                 if (isset($latestAreaEntry['area_update_value'])) {

        //                     $result[$key][$subkey] =
        //                         $latestAreaEntry['area_update_value'];
        //                 }
        //             }
        //         }
        //     }
        // }
        if (!empty($result)) {

            $this->load->model('sites/sites_model');

            // Local cache only for this function execution.
            $area_cache = array();

            foreach ($result as $key => $row) {

                if (!is_array($row)) {
                    continue;
                }

                $row_site_id = !empty($row['site_id'])
                    ? (int) $row['site_id']
                    : 0;

                if ($row_site_id <= 0) {
                    continue;
                }

                foreach (array(
                    'rooms_keys',
                    'cooled_builtup_area'
                ) as $subkey) {

                    if (!array_key_exists($subkey, $row)) {
                        continue;
                    }

                    /*
                    * Load latest area only once per site + field.
                    */
                    if (!isset($area_cache[$row_site_id][$subkey])) {

                        $dataFetch = array(
                            'site_id' => $row_site_id,
                            'area_update_field' => $subkey
                        );

                        $latestAreaEntry = $this->sites_model
                            ->getlatestSiteArea($dataFetch);

                        $area_cache[$row_site_id][$subkey] = 0;

                        if (!empty($latestAreaEntry)) {

                            $latestAreaEntry = (array) $latestAreaEntry;

                            if (isset($latestAreaEntry['area_update_value'])) {

                                $area_cache[$row_site_id][$subkey] =
                                    $latestAreaEntry['area_update_value'];
                            }
                        }
                    }

                    /*
                    * Apply cached value to this monthly row.
                    */
                    $result[$key][$subkey] =
                        $area_cache[$row_site_id][$subkey];
                }
            }
        }


        /*
        * ---------------------------------------------------------
        * 5. Index data exactly like the old method
        * ---------------------------------------------------------
        *
        * Final structure:
        *
        * $indexed[$site_id][$year][$month] = $row;
        */
        foreach ($result as $row) {

            if (
                !isset($row['site_id']) ||
                !isset($row['year_id']) ||
                !isset($row['month_id'])
            ) {
                continue;
            }

            $site_id = (int) $row['site_id'];
            $year    = (int) $row['year_id'];
            $month   = (int) $row['month_id'];


            /*
            * Only current and previous year.
            */
            if (
                $year != $current_year &&
                $year != $previous_year
            ) {
                continue;
            }


            /*
            * Don't include future months
            * for the current year.
            */
            if (
                $year == $current_year &&
                $month > $current_month
            ) {
                continue;
            }


            if (!isset($indexed[$site_id])) {
                $indexed[$site_id] = array();
            }

            if (!isset($indexed[$site_id][$year])) {
                $indexed[$site_id][$year] = array();
            }

            $indexed[$site_id][$year][$month] = $row;
        }


        return $indexed;
    }

    /**
     * ---------------------------------------------------------
     * Year aggregation
     * ---------------------------------------------------------
     */
    private function calculate_year_data($rows, $site_id, $site = array(), $factors = array())
    {
        $data = array(
            'electricity'      => 0,
            'fuel_oil'         => 0,
            'lpg'              => 0,
            'natural_gas'      => 0,
            'district_heating' => 0,
            'district_cooling' => 0,

            'electricity_converted'      => 0,
            'fuel_oil_converted'         => 0,
            'lpg_converted'              => 0,
            'natural_gas_converted'      => 0,
            'district_heating_converted' => 0,
            'district_cooling_converted' => 0,

            'water'       => 0,
            'carbon'      => 0,
            'guests'      => 0,
            'room_nights' => 0,
            'laundered'   => 0,

            'energy' => 0,
            'months' => array(),

            /*
             * Per-utility carbon (consumption x that utility's OWN
             * emission factor), for the Carbon composition donut.
             * Kept separate from calculate_month_carbon_bkp()'s
             * blended 'carbon' total above (which also includes
             * fleet petrol) since the composition breakdown only
             * covers these 6 utility categories, same as Energy
             * composition does.
             */
            'electricity_carbon'      => 0,
            'fuel_oil_carbon'         => 0,
            'lpg_carbon'              => 0,
            'natural_gas_carbon'      => 0,
            'district_heating_carbon' => 0,
            'district_cooling_carbon' => 0
        );

        if (empty($rows)) {
            $data['energy_intensity'] = null;
            $data['carbon_intensity'] = null;
            $data['water_intensity'] = null;

            return $data;
        }

        foreach ($rows as $row) {

            $month = $this->calculate_month_data(
                $row,
                $site_id,
                $site,
                $factors
            );


            /*
            * Raw utilities
            */
            $data['electricity'] += $month['electricity'];
            $data['fuel_oil'] += $month['fuel_oil'];
            $data['lpg'] += $month['lpg'];
            $data['natural_gas'] += $month['natural_gas'];
            $data['district_heating'] += $month['district_heating'];
            $data['district_cooling'] += $month['district_cooling'];


            /*
            * Converted utilities
            */
            $data['electricity_converted'] +=
                $month['electricity_converted'];

            $data['fuel_oil_converted'] +=
                $month['fuel_oil_converted'];

            $data['lpg_converted'] +=
                $month['lpg_converted'];

            $data['natural_gas_converted'] +=
                $month['natural_gas_converted'];

            $data['district_heating_converted'] +=
                $month['district_heating_converted'];

            $data['district_cooling_converted'] +=
                $month['district_cooling_converted'];


            /*
            * Water / activity
            */
            $data['water'] += $month['water'];
            $data['guests'] += $month['guests'];
            $data['room_nights'] += $month['room_nights'];


            /*
            * Energy / carbon
            */
            $data['energy'] += $month['energy'];
            $data['carbon'] += $month['carbon'];


            /*
            * Per-utility carbon - same emission-factor lookup
            * calculate_month_carbon_bkp() uses, applied per utility
            * instead of blended into one number.
            */
            $carbon_year = isset($row['year_id']) ? (int) $row['year_id'] : 0;
            $emission    = $this->get_site_emission_factors($site_id, $carbon_year);

            $data['electricity_carbon'] +=
                $month['electricity_converted']
                * $this->number(isset($emission['electricity_emission_factor']) ? $emission['electricity_emission_factor'] : 0);

            $data['fuel_oil_carbon'] +=
                $month['fuel_oil_converted']
                * $this->number(isset($emission['fuel_emission_factor']) ? $emission['fuel_emission_factor'] : 0);

            $data['lpg_carbon'] +=
                $month['lpg_converted']
                * $this->number(isset($emission['lpg_emission_factor']) ? $emission['lpg_emission_factor'] : 0);

            $data['natural_gas_carbon'] +=
                $month['natural_gas_converted']
                * $this->number(isset($emission['natural_gas_emission_factor']) ? $emission['natural_gas_emission_factor'] : 0);

            $data['district_heating_carbon'] +=
                $month['district_heating_converted']
                * $this->number(isset($emission['district_heating_emission_factor']) ? $emission['district_heating_emission_factor'] : 0);

            $data['district_cooling_carbon'] +=
                $month['district_cooling_converted']
                * $this->number(isset($emission['district_cooling_emission_factor']) ? $emission['district_cooling_emission_factor'] : 0);


            /*
            * Laundry
            */
            $data['laundered'] += $this->number(
                isset($row['total_laundered'])
                    ? $row['total_laundered']
                    : 0
            );


            /*
            * Reporting months
            */
            if (
                isset($row['month_id']) &&
                (int) $row['month_id'] > 0
            ) {
                $data['months'][] = (int) $row['month_id'];
            }
        }


        /*
        * Calculate intensity AFTER aggregation.
        */
        $data['energy_intensity'] = $this->ratio(
            $data['energy'],
            $data['room_nights']
        );

        $data['carbon_intensity'] = $this->ratio(
            $data['carbon'],
            $data['guests']
        );

        $data['water_intensity'] = $this->ratio(
            $data['water'],
            $data['guests']
        );


        return $data;
    }

    /**
     * ---------------------------------------------------------
     * Site row
     * ---------------------------------------------------------
     */
    private function build_site_row(
        $site,
        $current,
        $previous,
        $like_for_like
    ) {
        /*
        * ---------------------------------------------------------
        * Like-for-like data
        * ---------------------------------------------------------
        */
        $energy_mt = isset($like_for_like['energy'])
            ? $like_for_like['energy']
            : array(
                'ct' => 0,
                'pt' => 0,
                'cd' => 0,
                'pd' => 0
            );

        $carbon_mt = isset($like_for_like['carbon'])
            ? $like_for_like['carbon']
            : array(
                'ct' => 0,
                'pt' => 0,
                'cd' => 0,
                'pd' => 0
            );

        $water_mt = isset($like_for_like['water'])
            ? $like_for_like['water']
            : array(
                'ct' => 0,
                'pt' => 0,
                'cd' => 0,
                'pd' => 0
            );


        /*
        * ---------------------------------------------------------
        * Energy intensity
        *
        * Energy = kWh
        * Denominator = Room Nights
        * ---------------------------------------------------------
        */
        $energy_current_intensity = $this->ratio(
            $energy_mt['ct'],
            $energy_mt['cd']
        );

        $energy_previous_intensity = $this->ratio(
            $energy_mt['pt'],
            $energy_mt['pd']
        );


        /*
        * ---------------------------------------------------------
        * Carbon intensity
        *
        * Carbon = kgCO2e
        * Denominator = Guest Nights
        * ---------------------------------------------------------
        */
        $carbon_current_intensity = $this->ratio(
            $carbon_mt['ct'],
            $carbon_mt['cd']
        );

        $carbon_previous_intensity = $this->ratio(
            $carbon_mt['pt'],
            $carbon_mt['pd']
        );


        /*
        * ---------------------------------------------------------
        * Water intensity
        *
        * Water = m3
        * Denominator = Guest Nights
        * ---------------------------------------------------------
        */
        $water_current_intensity = $this->ratio(
            $water_mt['ct'],
            $water_mt['cd']
        );

        $water_previous_intensity = $this->ratio(
            $water_mt['pt'],
            $water_mt['pd']
        );


        /*
        * ---------------------------------------------------------
        * YoY
        * ---------------------------------------------------------
        *
        * Positive value  = intensity increased
        * Negative value  = intensity decreased
        */
        $energy_yoy = $this->percentage_change(
            $energy_previous_intensity,
            $energy_current_intensity
        );

        $carbon_yoy = $this->percentage_change(
            $carbon_previous_intensity,
            $carbon_current_intensity
        );

        $water_yoy = $this->percentage_change(
            $water_previous_intensity,
            $water_current_intensity
        );


        /*
        * ---------------------------------------------------------
        * Energy target performance
        * ---------------------------------------------------------
        *
        * Dashboard target:
        * Energy = -2%
        *
        * Therefore:
        *
        * Target intensity =
        * Previous intensity * 98%
        */
        $energy_target = null;
        $energy_perf   = null;

        if ($energy_previous_intensity !== null) {

            $energy_target = $energy_previous_intensity * 0.98;

            $energy_perf = $this->percentage_change(
                $energy_target,
                $energy_current_intensity
            );
        }


        /*
        * ---------------------------------------------------------
        * Energy mix
        * ---------------------------------------------------------
        *
        * The frontend expects:
        *
        * elec = Electricity
        * dc   = District Cooling
        * lpg  = LPG
        * fo   = Fuel Oil
        * ng   = Natural Gas
        * dh   = District Heating
        *
        * calculate_energy_mix() should return percentages.
        */
        $mix = $this->calculate_energy_mix($current);

        /*
         * Carbon mix - same 6 categories, each weighted by its OWN
         * emission factor instead of sharing kWh totals. Feeds the
         * Carbon composition donut the same way $mix feeds Energy's.
         */
        $carbon_mix = $this->calculate_carbon_mix($current);

        $carbon_mix_total = (float) $current['electricity_carbon']
            + (float) $current['district_cooling_carbon']
            + (float) $current['lpg_carbon']
            + (float) $current['fuel_oil_carbon']
            + (float) $current['natural_gas_carbon']
            + (float) $current['district_heating_carbon'];


        /*
        * ---------------------------------------------------------
        * Return dashboard row
        * ---------------------------------------------------------
        */
        return array(

            /*
            * -----------------------------------------------------
            * Property information
            * -----------------------------------------------------
            */
            'site_id' => (int) $site['site_id'],

            'site'    => isset($site['site'])
                ? $site['site']
                : '',

            'city'    => isset($site['city'])
                ? $site['city']
                : '',

            'country' => isset($site['country'])
                ? $site['country']
                : '',

            'region'  => isset($site['region'])
                ? $site['region']
                : '',

            'brand'   => isset($site['brand'])
                ? $site['brand']
                : '',


            /*
            * -----------------------------------------------------
            * Energy totals
            * -----------------------------------------------------
            *
            * Full YTD totals.
            */
            'energy_cur' => isset($current['energy'])
                ? $current['energy']
                : 0,

            'energy_pri' => isset($previous['energy'])
                ? $previous['energy']
                : 0,


            /*
            * -----------------------------------------------------
            * Energy like-for-like intensity
            * -----------------------------------------------------
            */
            'energy_match_i' => $energy_current_intensity,

            'energy_pri_i' => $energy_previous_intensity,

            'energy_yoy' => $energy_yoy,


            /*
            * -----------------------------------------------------
            * Energy performance against target
            * -----------------------------------------------------
            */
            'energy_perf' => $energy_perf,


            /*
            * -----------------------------------------------------
            * Carbon totals
            * -----------------------------------------------------
            */
            'carbon_cur' => isset($current['carbon'])
                ? $current['carbon']
                : 0,

            'carbon_pri' => isset($previous['carbon'])
                ? $previous['carbon']
                : 0,


            /*
            * -----------------------------------------------------
            * Carbon YoY
            * -----------------------------------------------------
            */
            'carbon_yoy' => $carbon_yoy,
            'carbon_match_i' => $carbon_current_intensity,
            'carbon_pri_i' => $carbon_previous_intensity,


            /*
            * -----------------------------------------------------
            * Water totals
            * -----------------------------------------------------
            */
            'water_cur' => isset($current['water'])
                ? $current['water']
                : 0,

            'water_pri' => isset($previous['water'])
                ? $previous['water']
                : 0,


            /*
            * -----------------------------------------------------
            * Water like-for-like intensity
            * -----------------------------------------------------
            */
            'water_match_i' => $water_current_intensity,

            'water_pri_i' => $water_previous_intensity,

            'water_yoy' => $water_yoy,
            'water_match_i' => $water_current_intensity,
            'water_pri_i' => $water_previous_intensity,

            'carbon_tgt_i' => $carbon_previous_intensity !== null
                ? $carbon_previous_intensity * 0.97
                : null,

            'carbon_perf' => $carbon_previous_intensity !== null
                ? $this->percentage_change(
                    $carbon_previous_intensity * 0.97,
                    $carbon_current_intensity
                )
                : null,

            'water_tgt_i' => $water_previous_intensity,
            'water_perf' => $this->percentage_change(
                $water_previous_intensity,
                $water_current_intensity
            ),


            /*
            * -----------------------------------------------------
            * Like-for-like objects
            *
            * IMPORTANT:
            * The existing dashboard JS directly expects these.
            * -----------------------------------------------------
            */
            'energy_mt' => array(
                'ct' => isset($energy_mt['ct'])
                    ? $energy_mt['ct']
                    : 0,

                'pt' => isset($energy_mt['pt'])
                    ? $energy_mt['pt']
                    : 0,

                'cd' => isset($energy_mt['cd'])
                    ? $energy_mt['cd']
                    : 0,

                'pd' => isset($energy_mt['pd'])
                    ? $energy_mt['pd']
                    : 0
            ),

            'carbon_mt' => array(
                'ct' => isset($carbon_mt['ct'])
                    ? $carbon_mt['ct']
                    : 0,

                'pt' => isset($carbon_mt['pt'])
                    ? $carbon_mt['pt']
                    : 0,

                'cd' => isset($carbon_mt['cd'])
                    ? $carbon_mt['cd']
                    : 0,

                'pd' => isset($carbon_mt['pd'])
                    ? $carbon_mt['pd']
                    : 0
            ),

            'water_mt' => array(
                'ct' => isset($water_mt['ct'])
                    ? $water_mt['ct']
                    : 0,

                'pt' => isset($water_mt['pt'])
                    ? $water_mt['pt']
                    : 0,

                'cd' => isset($water_mt['cd'])
                    ? $water_mt['cd']
                    : 0,

                'pd' => isset($water_mt['pd'])
                    ? $water_mt['pd']
                    : 0
            ),


            /*
            * -----------------------------------------------------
            * Energy composition
            * -----------------------------------------------------
            */
            'mix' => $mix,

            'carbon_mix'       => $carbon_mix,
            'carbon_mix_total' => $carbon_mix_total,


            /*
            * -----------------------------------------------------
            * Activity
            * -----------------------------------------------------
            */
            'rn_cur' => isset($current['room_nights'])
                ? $current['room_nights']
                : 0,

            'rn_pri' => isset($previous['room_nights'])
                ? $previous['room_nights']
                : 0,

            'gn_cur' => isset($current['guests'])
                ? $current['guests']
                : 0,

            'gn_pri' => isset($previous['guests'])
                ? $previous['guests']
                : 0,


            /*
            * -----------------------------------------------------
            * Reporting coverage
            * -----------------------------------------------------
            */
            'months_cur' => isset($current['months'])
                ? count($current['months'])
                : 0,

            'months_pri' => isset($previous['months'])
                ? count($previous['months'])
                : 0
        );
    }

    /**
     * ---------------------------------------------------------
     * Monthly series
     * ---------------------------------------------------------
     */
    private function build_site_series(
        $site_monthly,
        $current_year,
        $previous_year,
        $max_month,
        $site_id,
        $site = array(),
        $factors = array()
    ) {
        $cur = array();
        $pri = array();

        $rn_cur = array();
        $rn_pri = array();

        $carbon_cur = array();
        $carbon_pri = array();

        $guests_cur = array();
        $guests_pri = array();

        for ($month = 1; $month <= 12; $month++) {

            if ($month > $max_month) {
                $cur[] = null;
                $pri[] = null;
                $rn_cur[] = null;
                $rn_pri[] = null;
                $carbon_cur[] = null;
                $carbon_pri[] = null;
                $guests_cur[] = null;
                $guests_pri[] = null;
                continue;
            }

            $cur_data = $this->calculate_month_data(
                isset($site_monthly[$current_year][$month])
                    ? $site_monthly[$current_year][$month]
                    : array(),
                $site_id,
                $site,
                $factors
            );

            $pri_data = $this->calculate_month_data(
                isset($site_monthly[$previous_year][$month])
                    ? $site_monthly[$previous_year][$month]
                    : array(),
                $site_id,
                $site,
                $factors
            );

            $cur[] = isset($cur_data['energy'])
                ? $cur_data['energy']
                : null;

            $pri[] = isset($pri_data['energy'])
                ? $pri_data['energy']
                : null;

            $rn_cur[] = $cur_data['room_nights'] ?: null;
            $rn_pri[] = $pri_data['room_nights'] ?: null;

            $carbon_cur[] = isset($cur_data['carbon']) ? $cur_data['carbon'] : null;
            $carbon_pri[] = isset($pri_data['carbon']) ? $pri_data['carbon'] : null;

            $guests_cur[] = $cur_data['guests'] ?: null;
            $guests_pri[] = $pri_data['guests'] ?: null;
        }

        return array(
            'cur'        => $cur,
            'pri'        => $pri,
            'rn_cur'     => $rn_cur,
            'rn_pri'     => $rn_pri,
            'carbon_cur' => $carbon_cur,
            'carbon_pri' => $carbon_pri,
            'guests_cur' => $guests_cur,
            'guests_pri' => $guests_pri
        );
    }

    private function calculate_month_data($row, $site_id, $site = array(), $factors = array())
    {
        /*
        * ---------------------------------------------------------
        * Raw utility values
        * ---------------------------------------------------------
        */
        $electricity = $this->number(
            isset($row['electricity']) ? $row['electricity'] : 0
        );

        $fuel_oil = $this->number(
            isset($row['fuel_oil']) ? $row['fuel_oil'] : 0
        );

        $lpg = $this->number(
            isset($row['lpg']) ? $row['lpg'] : 0
        );

        $natural_gas = $this->number(
            isset($row['natural_gas']) ? $row['natural_gas'] : 0
        );

        $district_heating = $this->number(
            isset($row['district_heating']) ? $row['district_heating'] : 0
        );

        $district_cooling = $this->number(
            isset($row['district_cooling']) ? $row['district_cooling'] : 0
        );

        $water = $this->number(
            isset($row['water']) ? $row['water'] : 0
        );

        $room_nights = $this->number(
            isset($row['total_room_night'])
                ? $row['total_room_night']
                : 0
        );

        $guests = $this->number(
            isset($row['total_guests'])
                ? $row['total_guests']
                : 0
        );


        /*
        * ---------------------------------------------------------
        * Existing HEPP conversion factors
        * ---------------------------------------------------------
        *
        * Do NOT hardcode conversion factors.
        */
        if(empty($factors)){
            $factors = getMmbtuFactorConversionAllUtility($site_id);
        }


        $electricity_factor = $this->number(
            isset($factors['electricity'])
                ? $factors['electricity']
                : 1
        );

        $fuel_oil_factor = $this->number(
            isset($factors['fuel_oil'])
                ? $factors['fuel_oil']
                : 1
        );

        $lpg_factor = $this->number(
            isset($factors['lpg'])
                ? $factors['lpg']
                : 1
        );

        $natural_gas_factor = $this->number(
            isset($factors['natural_gas'])
                ? $factors['natural_gas']
                : 1
        );

        $district_heating_factor = $this->number(
            isset($factors['district_heating'])
                ? $factors['district_heating']
                : 1
        );

        $district_cooling_factor = $this->number(
            isset($factors['district_cooling'])
                ? $factors['district_cooling']
                : 1
        );


        /*
        * ---------------------------------------------------------
        * Convert every energy utility to kWh
        * ---------------------------------------------------------
        */
        $electricity_converted =
            $electricity * $electricity_factor;

        $fuel_oil_converted =
            $fuel_oil * $fuel_oil_factor;

        $lpg_converted =
            $lpg * $lpg_factor;

        $natural_gas_converted =
            $natural_gas * $natural_gas_factor;

        $district_heating_converted =
            $district_heating * $district_heating_factor;

        $district_cooling_converted =
            $district_cooling * $district_cooling_factor;


        /*
        * ---------------------------------------------------------
        * Total energy
        * ---------------------------------------------------------
        */
        $energy =
            $electricity_converted
            + $fuel_oil_converted
            + $lpg_converted
            + $natural_gas_converted
            + $district_heating_converted
            + $district_cooling_converted;
        /*
         * ---------------------------------------------------------
         * Carbon calculation
         * ---------------------------------------------------------
         *
         * Use the existing HEPP monthly utility/emission calculation.
         */
        // $carbon = $this->calculate_month_carbon(
        //     $site_id,
        //     isset($row['year_id']) ? (int) $row['year_id'] : 0,
        //     isset($row['month_id']) ? (int) $row['month_id'] : 0,
        //     $site,
        //     $factors
        // );
        $carbon = $this->calculate_month_carbon_bkp(
                    $row,
                    $site,
                    $factors
                );

        /*
         * ---------------------------------------------------------
         * Return
        * ---------------------------------------------------------
        */
        return array(

            /*
            * Raw utility values
            */
            'electricity'      => $electricity,
            'fuel_oil'         => $fuel_oil,
            'lpg'              => $lpg,
            'natural_gas'      => $natural_gas,
            'district_heating' => $district_heating,
            'district_cooling' => $district_cooling,

            /*
            * Converted utility values
            */
            'electricity_converted'      => $electricity_converted,
            'fuel_oil_converted'         => $fuel_oil_converted,
            'lpg_converted'              => $lpg_converted,
            'natural_gas_converted'      => $natural_gas_converted,
            'district_heating_converted' => $district_heating_converted,
            'district_cooling_converted' => $district_cooling_converted,

            /*
            * Other utilities/activity
            */
            'water'       => $water,
            'guests'      => $guests,
            'room_nights' => $room_nights,

            /*
            * Totals
            */
            'energy' => $energy,
            'carbon' => $carbon,

            /*
            * Intensities
            */
            'energy_intensity' => $this->ratio(
                $energy,
                $room_nights
            ),

            'carbon_intensity' => $this->ratio(
                $carbon,
                $guests
            ),

            'water_intensity' => $this->ratio(
                $water,
                $guests
            ),

            /*
            * Other existing data
            */
            'site_builtup_area' => $this->number(
                isset($row['site_builtup_area'])
                    ? $row['site_builtup_area']
                    : 0
            ),

            'cooled_builtup_area' => $this->number(
                isset($row['cooled_builtup_area'])
                    ? $row['cooled_builtup_area']
                    : 0
            ),

            'rooms_keys' => $this->number(
                isset($row['rooms_keys'])
                    ? $row['rooms_keys']
                    : 0
            )
        );
    }


    /**
     * Get the same monthly utility record used by HEPP carbon reports.
     */
    private function get_utility_detail($site_id, $year, $month)
    {
        $site_id = (int) $site_id;
        $year = (int) $year;
        $month = (int) $month;

        if ($site_id <= 0 || $year <= 0 || $month <= 0) {
            return array();
        }

        $cache_key = $site_id . '_' . $year . '_' . $month;

        if (isset($this->utility_detail_cache[$cache_key])) {
            return $this->utility_detail_cache[$cache_key];
        }

        $this->utilities_model->utilities_month = $month;
        $this->utilities_model->utilities_year = $year;
        $this->utilities_model->site_id = $site_id;

        $utility = $this->utilities_model->getUtility();

        if (!is_array($utility)) {
            $utility = array();
        }

        $this->utility_detail_cache[$cache_key] = $utility;

        return $utility;
    }

    /**
     * Get year-specific electricity emission configuration.
     */
    private function get_site_emission_factors($site_id, $year)
    {
        $site_id = (int) $site_id;
        $year = (int) $year;
        $cache_key = $site_id . '_' . $year;

        if (isset($this->emission_factor_cache[$cache_key])) {
            return $this->emission_factor_cache[$cache_key];
        }

        $data = array(
            'electricity_emission_factor' => 0,
            'electricity_emission_factor_percentage' => 0
        );

        if ($site_id <= 0 || $year <= 0) {
            $this->emission_factor_cache[$cache_key] = $data;
            return $data;
        }

        $this->site_emission_model->site_id = $site_id;
        $this->site_emission_model->year_id = $year;

        $result = $this->site_emission_model
            ->get_site_emission_model_detail_by_siteId();

        if (!empty($result)) {
            $emission = isset($result[0]['s'])
                ? $result[0]['s']
                : $result[0];

            $data['electricity_emission_factor'] = $this->number(
                isset($emission['electricity_emission_factor'])
                    ? $emission['electricity_emission_factor']
                    : 0
            );
            $data['fuel_emission_factor'] = $this->number(
                isset($emission['fuel_emission_factor'])
                    ? $emission['fuel_emission_factor']
                    : 0
            );

            $data['lpg_emission_factor'] = $this->number(
                isset($emission['lpg_emission_factor'])
                    ? $emission['lpg_emission_factor']
                    : 0
            );

            $data['natural_gas_emission_factor'] = $this->number(
                isset($emission['natural_gas_emission_factor'])
                    ? $emission['natural_gas_emission_factor']
                    : 0
            );

            $data['district_cooling_emission_factor'] = $this->number(
                isset($emission['district_cooling_emission_factor'])
                    ? $emission['district_cooling_emission_factor']
                    : 0
            );

            $data['district_heating_emission_factor'] = $this->number(
                isset($emission['district_heating_emission_factor'])
                    ? $emission['district_heating_emission_factor']
                    : 0
            );

            $data['electricity_emission_factor_percentage'] = $this->number(
                isset($emission['electricity_emission_factor_percentage'])
                    ? $emission['electricity_emission_factor_percentage']
                    : 0
            );
        }

        $this->emission_factor_cache[$cache_key] = $data;

        return $data;
    }

    /**
     * HEPP-compatible monthly carbon calculation.
     */
    private function calculate_month_carbon_bkp($row, $site, $factors = array())
    {
        if (empty($row)) {
            return 0;
        }

        $site_id = isset($site['site_id'])
            ? (int) $site['site_id']
            : 0;

        $year = isset($row['year_id'])
            ? (int) $row['year_id']
            : 0;

        $month = isset($row['month_id'])
            ? (int) $row['month_id']
            : 0;

        if ($site_id <= 0 || $year <= 0 || $month <= 0) {
            return 0;
        }


        /*
        * ---------------------------------------------------------
        * RAW UTILITY CONSUMPTION
        * ---------------------------------------------------------
        */

        $electricity = $this->number(
            isset($row['electricity'])
                ? $row['electricity']
                : 0
        );

        $fuel_oil = $this->number(
            isset($row['fuel_oil'])
                ? $row['fuel_oil']
                : 0
        );

        $lpg = $this->number(
            isset($row['lpg'])
                ? $row['lpg']
                : 0
        );

        $natural_gas = $this->number(
            isset($row['natural_gas'])
                ? $row['natural_gas']
                : 0
        );

        $district_heating = $this->number(
            isset($row['district_heating'])
                ? $row['district_heating']
                : 0
        );

        $district_cooling = $this->number(
            isset($row['district_cooling'])
                ? $row['district_cooling']
                : 0
        );

        /*
        * Fleet petrol - flat 2.3 kgCO2e/Liter factor, same as the
        * per-property dashboard (Sites_model::getYtdCarbonFootprints()).
        * Was missing here entirely; now read from the same monthly
        * row fetched by monthlyUtilityBasedReportByUnit() - no extra query.
        */
        $fleet_petrol = $this->number(
            isset($row['fleet_petrol'])
                ? $row['fleet_petrol']
                : 0
        );


        /*
        * ---------------------------------------------------------
        * GET ADDITIONAL UTILITY DATA
        * ---------------------------------------------------------
        *
        * Needed because monthlyUtilityBasedReportByUnit()
        * does not provide the generator/renewable fields.
        */

        // $utility = $this->get_utility_detail(
        //     $site_id,
        //     $year,
        //     $month
        // );


        /*
        * ---------------------------------------------------------
        * ELECTRICITY
        * ---------------------------------------------------------
        *
        * HEPP carbon calculation:
        *
        * Total electricity
        * - onsite generator electricity
        * - renewable electricity
        */

        // $onsite_generator = $this->number(
        //     isset($utility['onsite_generators_quantity'])
        //         ? $utility['onsite_generators_quantity']
        //         : 0
        // );

        // $renewable_energy = $this->number(
        //     isset($utility['total_renewable_energy_production'])
        //         ? $utility['total_renewable_energy_production']
        //         : 0
        // );

        $electricity_consumption =
            $electricity;
            // - $onsite_generator
            // - $renewable_energy;

        if ($electricity_consumption < 0) {
            $electricity_consumption = 0;
        }


        /*
        * ---------------------------------------------------------
        * EMISSION FACTORS
        * ---------------------------------------------------------
        */

        $emission = $this->get_site_emission_factors(
            $site_id,
            $year
        );


         $electricity_emission_factor =
            $this->number($emission['electricity_emission_factor']);

        $fuel_emission_factor = $this->number(
            isset($emission['fuel_emission_factor'])
                ? $emission['fuel_emission_factor']
                : 0
        );
        $lpg_emission_factor = $this->number(
            isset($emission['lpg_emission_factor'])
                ? $emission['lpg_emission_factor']
                : 0
        );
        $natural_gas_emission_factor = $this->number(
            isset($emission['natural_gas_emission_factor'])
                ? $emission['natural_gas_emission_factor']
                : 0
        );
        $district_cooling_emission_factor = $this->number(
            isset($emission['district_cooling_emission_factor'])
                ? $emission['district_cooling_emission_factor']
                : 0
        );
        $district_heating_emission_factor = $this->number(
            isset($emission['district_heating_emission_factor'])
                ? $emission['district_heating_emission_factor']
                : 0
        );

        $electricity_mmbtu_rate = $this->number(
            isset($factors['electricity']) ? $factors['electricity'] : 1
        );

        $fuel_mmbtu_rate = $this->number(
            isset($factors['fuel_oil']) ? $factors['fuel_oil'] : 1
        );

        $lpg_mmbtu_rate = $this->number(
            isset($factors['lpg']) ? $factors['lpg'] : 1
        );

        $natural_gas_mmbtu_rate = $this->number(
            isset($factors['natural_gas']) ? $factors['natural_gas'] : 1
        );

        $heating_district_mmbtu_rate = $this->number(
            isset($factors['district_heating']) ? $factors['district_heating'] : 1
        );

        $cooling_district_mmbtu_rate = $this->number(
            isset($factors['district_cooling']) ? $factors['district_cooling'] : 1
        );


        /*
        * ---------------------------------------------------------
        * CARBON
        * ---------------------------------------------------------
        *
        * IMPORTANT:
        *
        * NO getUtilityUnitFactorForConversion()
        *
        * Carbon = consumption × emission factor
        */

        $carbon = 0;


        // Electricity
        $carbon +=
            $electricity_consumption
            * $electricity_emission_factor * $electricity_mmbtu_rate;


        // Fuel oil
        $carbon +=
            $fuel_oil
            * $fuel_emission_factor * $fuel_mmbtu_rate;


        // LPG
        $carbon +=
            $lpg
            * $lpg_emission_factor * $lpg_mmbtu_rate;


        // Natural gas
        $carbon +=
            $natural_gas
            * $natural_gas_emission_factor * $natural_gas_mmbtu_rate;


        // District heating
        $carbon +=
            $district_heating
            * $district_heating_emission_factor * $heating_district_mmbtu_rate;


        // District cooling
        $carbon +=
            $district_cooling
            * $district_cooling_emission_factor * $cooling_district_mmbtu_rate;


        // Fleet petrol
        $carbon +=
            $fleet_petrol * 2.3;


        return $carbon;
    }
    private function calculate_month_carbon($site_id, $year, $month, $site = array(), $factors = array())
    {
        // if ((int) $site_id <= 0 || (int) $year <= 0 || (int) $month <= 0) {
        //     return 0;
        // }

        // $utility = $this->get_utility_detail($site_id, $year, $month);

        // if (empty($utility)) {
        //     return 0;
        // }

        // $electricity_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'electricity')
        // );
        // $fuel_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'fuel_oil')
        // );
        // $lpg_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'lpg')
        // );
        // $natural_gas_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'natural_gas')
        // );
        // $heating_district_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'district_heating')
        // );
        // $cooling_district_mmbtu_rate = $this->number(
        //     getUtilityUnitFactorForConversion($site_id, 'district_cooling')
        // );

        $electricity_mmbtu_rate = $this->number(
            isset($factors['electricity']) ? $factors['electricity'] : 1
        );

        $fuel_mmbtu_rate = $this->number(
            isset($factors['fuel_oil']) ? $factors['fuel_oil'] : 1
        );

        $lpg_mmbtu_rate = $this->number(
            isset($factors['lpg']) ? $factors['lpg'] : 1
        );

        $natural_gas_mmbtu_rate = $this->number(
            isset($factors['natural_gas']) ? $factors['natural_gas'] : 1
        );

        $heating_district_mmbtu_rate = $this->number(
            isset($factors['district_heating']) ? $factors['district_heating'] : 1
        );

        $cooling_district_mmbtu_rate = $this->number(
            isset($factors['district_cooling']) ? $factors['district_cooling'] : 1
        );

        /*
         * These are the same utility fields used by HEPP's carbon report.
         */
        $total_electricity_kwh = $this->number(
            isset($row['total_electricity_kwh'])
                ? $row['total_electricity_kwh']
                : 0
        );
        $total_lpg = $this->number(
            isset($row['total_lpg'])
                ? $row['total_lpg']
                : 0
        );
        $total_fuel_oil = $this->number(
            isset($row['total_fuel_oil'])
                ? $row['total_fuel_oil']
                : 0
        );
        $total_natural_gas = $this->number(
            isset($row['total_natural_gas'])
                ? $row['total_natural_gas']
                : 0
        );
        $district_heating = $this->number(
            isset($row['district_heating'])
                ? $row['district_heating']
                : 0
        );
        $district_cooling = $this->number(
            isset($row['district_cooling'])
                ? $row['district_cooling']
                : 0
        );
        // $onsite_generators_quantity = $this->number(
        //     isset($row['onsite_generators_quantity'])
        //         ? $row['onsite_generators_quantity']
        //         : 0
        // );
        // $renewable_energy = $this->number(
        //     isset($row['total_renewable_energy_production'])
        //         ? $row['total_renewable_energy_production']
        //         : 0
        // );

        $emission = $this->get_site_emission_factors($site_id, $year);

        // $electricity_percentage =
        //     $emission['electricity_emission_factor_percentage'];

        // $electricity_for_carbon =
        //     (
        //         (
        //             1 - ($electricity_percentage / 100)
        //         )
        //         * $total_electricity_kwh
        //     )
        //     - $onsite_generators_quantity
        //     - $renewable_energy;
            $electricity_for_carbon =
            $total_electricity_kwh;
            // - $onsite_generators_quantity
            // - $renewable_energy;

        if ($electricity_for_carbon < 0) {
            $electricity_for_carbon = 0;
        }

        $electricity_emission_factor =
            $this->number($emission['electricity_emission_factor']);

        $fuel_emission_factor = $this->number(
            isset($emission['fuel_emission_factor'])
                ? $emission['fuel_emission_factor']
                : 0
        );
        $lpg_emission_factor = $this->number(
            isset($emission['lpg_emission_factor'])
                ? $emission['lpg_emission_factor']
                : 0
        );
        $natural_gas_emission_factor = $this->number(
            isset($emission['natural_gas_emission_factor'])
                ? $emission['natural_gas_emission_factor']
                : 0
        );
        $district_cooling_emission_factor = $this->number(
            isset($emission['district_cooling_emission_factor'])
                ? $emission['district_cooling_emission_factor']
                : 0
        );
        $district_heating_emission_factor = $this->number(
            isset($emission['district_heating_emission_factor'])
                ? $emission['district_heating_emission_factor']
                : 0
        );

        return
            (
                $electricity_mmbtu_rate
                * $electricity_for_carbon
                * $electricity_emission_factor
            )
            +
            (
                $lpg_mmbtu_rate
                * $total_lpg
                * $lpg_emission_factor
            )
            +
            (
                $fuel_mmbtu_rate
                * $total_fuel_oil
                * $fuel_emission_factor
            )
            +
            (
                $natural_gas_mmbtu_rate
                * $total_natural_gas
                * $natural_gas_emission_factor
            )
            +
            (
                $heating_district_mmbtu_rate
                * $district_heating
                * $district_heating_emission_factor
            )
            +
            (
                $cooling_district_mmbtu_rate
                * $district_cooling
                * $district_cooling_emission_factor
            );
    }

    /**
     * ---------------------------------------------------------
     * Data quality
     * ---------------------------------------------------------
     */
    private function get_data_quality(
        $site,
        $site_monthly,
        $current_year,
        $previous_year,
        $max_month
    ) {
        $issues = array();

        $current_months = isset($site_monthly[$current_year])
            ? $site_monthly[$current_year]
            : array();

        $previous_months = isset($site_monthly[$previous_year])
            ? $site_monthly[$previous_year]
            : array();

        if (count($current_months) < $max_month) {
            $issues[] = 'Incomplete current-year utility reporting';
        }

        if (count($previous_months) < $max_month) {
            $issues[] = 'Incomplete previous-year utility reporting';
        }

        if ($this->count_valid_denominator(
            $current_months,
            'total_room_night',
            $max_month
        ) < $max_month) {
            $issues[] = 'Missing room-night data';
        }

        if ($this->count_valid_denominator(
            $current_months,
            'total_guests',
            $max_month
        ) < $max_month) {
            $issues[] = 'Missing guest-night data';
        }

        if ($this->count_valid_denominator(
            $previous_months,
            'total_guests',
            $max_month
        ) < $max_month) {
            $issues[] = 'Missing previous-year guest-night data';
        }

        return $issues;
    }

    private function count_valid_denominator(
        $months,
        $field,
        $max_month
    ) {
        $count = 0;

        for ($month = 1; $month <= $max_month; $month++) {

            if (
                isset($months[$month]) &&
                isset($months[$month][$field]) &&
                (float) $months[$month][$field] > 0
            ) {
                $count++;
            }
        }

        return $count;
    }

    private function get_quality_severity($issues)
    {
        if (empty($issues)) {
            return 'low';
        }

        if (count($issues) >= 2) {
            return 'high';
        }

        return 'review';
    }

    /**
     * ---------------------------------------------------------
     * Meta
     * ---------------------------------------------------------
     */
    private function get_meta(
        $current_year,
        $previous_year,
        $current_month,
        $total_sites,
        $sites = array()
    ) {
        return array(
            'cur_year' => $current_year,
            'prior_year' => $previous_year,
            'ytd_months' => $current_month,

            'period_label' =>
                'January - ' .
                date(
                    'F',
                    mktime(0, 0, 0, $current_month, 1)
                ) .
                ' ' .
                $current_year .
                ' vs January - ' .
                date(
                    'F',
                    mktime(0, 0, 0, $current_month, 1)
                ) .
                ' ' .
                $previous_year,

            'generated' => date('d F Y'),

            'total_sites' => $total_sites,

            /*
             * Portfolio-wide reduction targets.
             *
             * Per-site targets (energy_intensity_annual_target,
             * ghg_intensity_annual_target, water_intensity_annual_target
             * on `sites`) are averaged across the currently loaded sites -
             * same columns the per-property dashboard reads per-site via
             * calculateProgressOnTarget()/$siteTargets.
             */
            'targets' => $this->get_portfolio_targets($sites),

            'source' => 'HEPP Reports_model'
        );
    }

    /**
     * Average each site's own reduction-target columns across the
     * given sites, instead of a single hardcoded portfolio-wide value.
     *
     * A site missing a target (null/0) is excluded from that metric's
     * average rather than pulling it toward 0.
     */
    private function get_portfolio_targets($sites)
    {
        $sums = array('energy' => 0, 'carbon' => 0, 'water' => 0);
        $counts = array('energy' => 0, 'carbon' => 0, 'water' => 0);

        $column_map = array(
            'energy' => 'energy_intensity_annual_target',
            'carbon' => 'ghg_intensity_annual_target',
            'water'  => 'water_intensity_annual_target'
        );

        foreach ($sites as $site) {
            foreach ($column_map as $key => $column) {

                if (!isset($site[$column]) || $site[$column] === '' || $site[$column] === null) {
                    continue;
                }

                $sums[$key] += (float) $site[$column];
                $counts[$key]++;
            }
        }

        $targets = array();

        foreach ($column_map as $key => $column) {
            $targets[$key] = $counts[$key] > 0
                ? round($sums[$key] / $counts[$key], 2)
                : 0.00;
        }

        return $targets;
    }

    private function calculate_like_for_like(
        $site_monthly,
        $current_year,
        $previous_year,
        $current_month,
        $site_id,
        $site,
        $factors = array()
    ) {
        $energy = array(
            'ct' => 0,
            'pt' => 0,
            'cd' => 0,
            'pd' => 0
        );

        $carbon = array(
            'ct' => 0,
            'pt' => 0,
            'cd' => 0,
            'pd' => 0
        );

        $water = array(
            'ct' => 0,
            'pt' => 0,
            'cd' => 0,
            'pd' => 0
        );

        for ($month = 1; $month <= $current_month; $month++) {

            if (
                !isset($site_monthly[$current_year][$month]) ||
                !isset($site_monthly[$previous_year][$month])
            ) {
                continue;
            }

            $current = $this->calculate_month_data(
                $site_monthly[$current_year][$month],
                $site_id,
                $site,
                $factors
            );

            $previous = $this->calculate_month_data(
                $site_monthly[$previous_year][$month],
                $site_id,
                $site,
                $factors
            );

            /*
            * ENERGY
            *
            * Requires energy and room nights in both years.
            */
            if (
                $current['energy'] > 0 &&
                $previous['energy'] > 0 &&
                $current['room_nights'] > 0 &&
                $previous['room_nights'] > 0
            ) {
                $energy['ct'] += $current['energy'];
                $energy['pt'] += $previous['energy'];
                $energy['cd'] += $current['room_nights'];
                $energy['pd'] += $previous['room_nights'];
            }

            /*
            * CARBON
            *
            * Requires carbon and guest nights in both years.
            */
            if (
                $current['carbon'] > 0 &&
                $previous['carbon'] > 0 &&
                $current['guests'] > 0 &&
                $previous['guests'] > 0
            ) {
                $carbon['ct'] += $current['carbon'];
                $carbon['pt'] += $previous['carbon'];
                $carbon['cd'] += $current['guests'];
                $carbon['pd'] += $previous['guests'];
            }

            /*
            * WATER
            *
            * Requires water and guest nights in both years.
            */
            if (
                $current['water'] > 0 &&
                $previous['water'] > 0 &&
                $current['guests'] > 0 &&
                $previous['guests'] > 0
            ) {
                $water['ct'] += $current['water'];
                $water['pt'] += $previous['water'];
                $water['cd'] += $current['guests'];
                $water['pd'] += $previous['guests'];
            }
        }

        return array(
            'energy' => $energy,
            'carbon' => $carbon,
            'water'  => $water
        );
    }

    private function calculate_energy_mix($data)
    {
        $total = (float) $data['energy'];

        if ($total <= 0) {
            return array(
                'elec' => 0,
                'dc'   => 0,
                'lpg'  => 0,
                'fo'   => 0,
                'ng'   => 0,
                'dh'   => 0
            );
        }

        return array(
            'elec' => ($data['electricity_converted'] / $total) * 100,

            'dc' => ($data['district_cooling_converted'] / $total) * 100,

            'lpg' => ($data['lpg_converted'] / $total) * 100,

            'fo' => ($data['fuel_oil_converted'] / $total) * 100,

            'ng' => ($data['natural_gas_converted'] / $total) * 100,

            'dh' => ($data['district_heating_converted'] / $total) * 100
        );
    }

    private function calculate_carbon_mix($data)
    {
        $total = (float) $data['electricity_carbon']
            + (float) $data['district_cooling_carbon']
            + (float) $data['lpg_carbon']
            + (float) $data['fuel_oil_carbon']
            + (float) $data['natural_gas_carbon']
            + (float) $data['district_heating_carbon'];

        if ($total <= 0) {
            return array(
                'elec' => 0,
                'dc'   => 0,
                'lpg'  => 0,
                'fo'   => 0,
                'ng'   => 0,
                'dh'   => 0
            );
        }

        return array(
            'elec' => ($data['electricity_carbon'] / $total) * 100,

            'dc' => ($data['district_cooling_carbon'] / $total) * 100,

            'lpg' => ($data['lpg_carbon'] / $total) * 100,

            'fo' => ($data['fuel_oil_carbon'] / $total) * 100,

            'ng' => ($data['natural_gas_carbon'] / $total) * 100,

            'dh' => ($data['district_heating_carbon'] / $total) * 100
        );
    }

    /**
     * ---------------------------------------------------------
     * Helpers
     * ---------------------------------------------------------
     */
    private function number($value, $key = null)
    {
        /*
        * Support both:
        *
        * $this->number($row, 'electricity')
        *
        * and:
        *
        * $this->number($row['electricity'])
        */
        if ($key !== null) {

            if (!is_array($value)) {
                return 0;
            }

            if (!isset($value[$key]) || $value[$key] === '') {
                return 0;
            }

            return (float) $value[$key];
        }

        if ($value === null || $value === '') {
            return 0;
        }

        return (float) $value;
    }

    private function ratio($numerator, $denominator)
    {
        if ((float) $denominator <= 0) {
            return null;
        }

        return (float) $numerator / (float) $denominator;
    }

    private function percentage_change($old, $new)
    {
        if ($old === null || (float) $old == 0) {
            return null;
        }

        return (($new - $old) / $old) * 100;
    }
    
}
?>