<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Reports_forex_model extends Base_Model {



    protected $_tbl_utilities = TBL_UTILITIES_COST;

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_countries = TBL_COUNTRIES;

    protected $_tbl_electricity_tariff = TBL_ELECTRICITY_TARIFF;

    protected $_tbl_projects_categories = 'project_categories';

    protected $_tbl_projects = 'project_info';

    protected $_tbl_hotels = TBL_HOTELS;

    protected $_tbl_projects_todos = 'project_to_do_info';

    protected $_tbl_action_plan = 'action_plan';

    protected $_tbl_utilities_cost_daily = 'utilities_cost_daily';

    public $utilities_month = "";

    public $utilities_year = "";

    public $site_id = "";

    public $role_id = "";

    public $user_id = "";

    public $filed_array = [

        'electricity',

        'fuel',

        'lpg',

        'natural_gas',

        'heating_district',

        'cooling_district',

        'district_heating_fixed_cost',

        'district_cooling_fixed_cost',

        'water',

        'total_budget',

        'total_purchased_electricity_cost',

    ];



    function __construct() {

        parent::__construct();

    }



    function utilityCostBarChart($filters = array()) {

        $start_year = $filters['start_year'];

        $start_month = $filters['start_month'];

        $end_year = $filters['end_year'];

        $end_month = $filters['end_month'];



        $where_more = '';



        $pre_start_year = $start_year - 1;

        $pre_end_year = $end_year - 1;



        if ($start_year == $end_year) {

            $where_more = "(

                                (year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

                                OR

                                (year_id='$pre_start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

                            )";

        } else {

            $where_more = "((

                                 (year_id='$start_year' AND month_id>='$start_month') 

                                     OR 

                                 (year_id='$end_year' AND month_id<='$end_month')

                             ) OR (

                                 (year_id='{$pre_start_year}' AND month_id>='$start_month') 

                                     OR 

                                 (year_id='{$pre_end_year}' AND month_id<='$end_month')

                             ))";

        }



        $query = "SELECT u.total_electricity_cost as electricity, 

                            u.total_fuel_oil_cost as fuel,

                            u.total_fuel_oil as fuel_consumption,

                            u.total_lpg_cost as lpg, 

                            u.total_lpg as lpg_consumption, 

                            u.total_natural_gas_cost as natural_gas, 

                            u.total_natural_gas as natural_gas_consumption, 

                            u.district_heating_cost as heating_district, 

                            u.district_heating as heating_district_consumption, 

                            u.district_cooling_cost as cooling_district, 

                            u.district_cooling as cooling_district_consumption, 

                            u.district_heating_fixed_cost as district_heating_fixed_cost,

                            u.district_cooling_fixed_cost as district_cooling_fixed_cost,

                            u.water_total_consumption_cost as water,

                            u.water_total_consumption as water_consumption,

                            u.onsite_generators_quantity as onsite_generator,
							u.onsite_generators_fuel_oil_quantity as onsite_generator_fuel_oil,
							u.onsite_generators_natural_gas_quantity as onsite_generator_natural_gas,
                            u.total_renewable_energy_production as renewable_energy,

                            cdd,

                            hdd,

                            (u.water_total_consumption_budget_cost+u.total_consumption_breakdown_budget_cost+u.fuel_total_budget_cost+u.lpg_total_budget_cost+u.natural_gas_total_budget_cost+u.district_heating_total_budget_cost+u.district_cooling_total_budget_cost+u.electricity_total_budget_cost) as total_budget,

                            u.total_purchased_electricity,

                            u.total_purchased_electricity_cost,

                            u.total_electricity_kwh,

                        month_id,year_id,total_room_night,s.rooms_keys,u.forex

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE site_id={$this->site_id} AND {$where_more} 
                    
                    ORDER BY year_id ASC,month_id ASC,u.id ASC";



        $result = $this->db->query($query);

        $resultArray = $result->result_array();



        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($this->filed_array as $filed) {

                    if (array_key_exists($filed, $res)) {

                        $res[$filed] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        return $resultArray;

    }



    function costBasedReportForCurrentYear($filters) {

        $current_year = $filters['report_year']; //date('Y');

        $current_month = $filters['max_month_id'];



        $query = "SELECT SUM(total_electricity_cost) as electricity, 

                        SUM(total_fuel_oil_cost) as fuel, 

                        SUM(total_lpg_cost) as lpg, 

                        SUM(total_natural_gas_cost) as natural_gas, 

                        SUM(district_heating_cost) as heating_district, 

                        SUM(district_heating_fixed_cost) as district_heating_fixed_cost, 

                        SUM(district_cooling_fixed_cost) as district_cooling_fixed_cost, 

                        SUM(district_cooling_cost) as cooling_district, 

                        SUM(water_total_consumption_cost) as water, 

                        ROUND(AVG(forex),4) as forex

                    FROM {$this->_tbl_utilities}

                    WHERE (year_id='$current_year' AND month_id<='$current_month')

                    AND site_id={$this->site_id}
                    
                    ORDER BY year_id ASC,month_id ASC,u.id ASC";



        $result = $this->db->query($query);

        $rowArray = $result->row_array();



        $forex = $rowArray['forex'];



        foreach ($this->filed_array as $filed) {

            if (array_key_exists($filed, $rowArray)) {

                $rowArray[$filed] *= $forex;

            }

        }

        return $rowArray;

    }



    function costBasedReportForPreviousMonth($filters = array()) {

        $month = $filters['previous_month'];

        $year = $filters['previous_year'];



        $query = "SELECT total_electricity_cost as electricity, 

                        total_fuel_oil_cost as fuel, 

                        total_lpg_cost as lpg, 

                        total_natural_gas_cost as natural_gas, 

                        district_heating_cost as heating_district, 

                        district_cooling_cost as cooling_district, 

                        district_heating_fixed_cost as district_heating_fixed_cost, 

                        district_cooling_fixed_cost as district_cooling_fixed_cost, 

                        water_total_consumption_cost as water, 

                        forex

                    FROM {$this->_tbl_utilities}

                    WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')
                    
                    ORDER BY year_id ASC,month_id ASC,u.id ASC";



        $result = $this->db->query($query);

        $rowArray = $result->row_array();



        $forex = $rowArray['forex'];

        foreach ($this->filed_array as $filed) {

            if (array_key_exists($filed, $rowArray)) {

                $rowArray[$filed] *= $forex;

            }

        }

        

        return $rowArray;

    }



    function utilityCostBarChartByYears($filters = array()) {

        $start_year = $filters['start_year'];

        $start_month = $filters['start_month'];

        $end_year = $filters['end_year'];

        $end_month = $filters['end_month'];



        $where_more = '';



        if ($start_year == $end_year) {

            $where_more = "(

                                (year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

                            )";

        } else {

            $where_more = "((

                                 (year_id>='$start_year' AND month_id>='$start_month') 

                                     AND 

                                 (year_id<='$end_year' AND month_id<='$end_month')

                             ))";

        }



        $query = "SELECT SUM(u.total_electricity_cost) as electricity, 

                        SUM(u.total_electricity_kwh) as electricity_unit,

                        SUM(u.total_fuel_oil_cost) as fuel, 

                        SUM(u.total_fuel_oil) as fuel_unit,

                        SUM(u.total_lpg_cost) as lpg, 

                        SUM(u.total_lpg) as lpg_unit,

                        SUM(u.total_natural_gas_cost) as natural_gas, 

                        SUM(u.total_natural_gas) as natural_gas_unit,

                        SUM(u.district_heating_cost) as heating_district, 

                        SUM(u.district_heating) as heating_district_unit,

                        SUM(u.district_cooling_cost) as cooling_district, 

                        SUM(u.district_cooling) as cooling_district_unit,

                        SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost, 

                        SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

                        SUM(u.water_total_consumption_cost) as water, 

                        SUM(u.water_total_consumption) as water_unit,

                        SUM(u.cdd) as cdd,

                        SUM(u.hdd) as hdd,

                        SUM(u.total_room_night) as total_room_night,

                        ((SUM(u.total_room_night)/(s.rooms_keys*IF(u.year_id % 4 = 0, 366, 365))) * 100) as occupancy,

                        SUM(u.total_electricity_kwh) as total_electricity_kwh,

                        u.year_id as year_id,

                        s.rooms_keys,

                        ROUND(AVG(u.forex),4) as forex

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE u.site_id={$this->site_id} AND {$where_more} 

                    GROUP BY u.year_id";



        $result = $this->db->query($query);

        $resultArray = $result->result_array();

        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($this->field_array as $field) {

                    if (array_key_exists($field, $res)) {

                        $res[$field] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        return $resultArray;

        

    }



    function getUtilityActualBudgetData($filters = array()) {

        $field_array = [

            "district_cooling_cost_actual",

            "district_cooling_cost_budget",

            "district_heating_cost_actual",

            "district_heating_cost_budget",

            "total_electricity_cost_actual",

            "total_electricity_cost_budget",

            "total_fuel_oil_cost_actual",

            "total_fuel_oil_cost_budget",

            "total_lpg_cost_actual",

            "total_lpg_cost_budget",

            "total_natural_gas_cost_actual",

            "total_natural_gas_cost_budget",

            "water_total_consumption_cost_actual",

            "water_total_consumption_cost_budget"

        ];

        

        if (empty($filters['site_id'])) {

            $filters['site_id'] = 0;

        }



        if ($filters['start_year'] != $filters['end_year']) {

            $where_more = "(

                        (year_id = {$filters['start_year']} AND month_id >= {$filters['start_month']})

                            OR

                        (year_id = {$filters['end_year']} AND month_id <= {$filters['end_month']})

                        )";

        } else {

            if ($filters['start_month'] == $filters['end_month']) {

                $where_more = "(year_id = {$filters['start_year']} AND month_id = {$filters['start_month']})";

            } else {

                $where_more = "(year_id = {$filters['start_year']} AND (month_id >= {$filters['start_month']} AND month_id <= {$filters['end_month']}))";

            }

        }



        $query = "SELECT u.site_id,u.month_id,u.year_id,u.hdd,u.cdd,u.total_room_night,

                        u.district_cooling as district_cooling_actual,

                        u.district_cooling_total_budget as district_cooling_budget,

                        u.district_cooling_cost as district_cooling_cost_actual,

                        u.district_cooling_total_budget_cost as district_cooling_cost_budget,

                        u.district_heating as district_heating_actual,

                        u.district_heating_total_budget as district_heating_budget,

                        u.district_heating_cost as district_heating_cost_actual,

                        u.district_heating_total_budget_cost as district_heating_cost_budget,

                        u.total_electricity_kwh as total_electricity_kwh_actual,

                        u.electricity_total_budget as total_electricity_kwh_budget,

                        u.total_electricity_cost as total_electricity_cost_actual,

                        u.electricity_total_budget_cost as total_electricity_cost_budget,

                        u.total_fuel_oil as total_fuel_oil_actual,

                        u.fuel_total_budget as total_fuel_oil_budget,

                        u.total_fuel_oil_cost as total_fuel_oil_cost_actual,

                        u.fuel_total_budget_cost as total_fuel_oil_cost_budget,

                        u.total_lpg as total_lpg_actual,

                        u.lpg_total_budget as total_lpg_budget,

                        u.total_lpg_cost as total_lpg_cost_actual,

                        u.lpg_total_budget_cost as total_lpg_cost_budget,

                        u.total_natural_gas as total_natural_gas_actual,

                        u.natural_gas_total_budget as total_natural_gas_budget,

                        u.total_natural_gas_cost as total_natural_gas_cost_actual,

                        u.natural_gas_total_budget_cost as total_natural_gas_cost_budget,

                        u.water_total_consumption as water_total_consumption_actual,

                        u.water_total_consumption_budget as water_total_consumption_budget,

                        u.water_total_consumption_cost as water_total_consumption_cost_actual,

                        u.water_total_consumption_budget_cost as water_total_consumption_cost_budget,

                        u.forex

                    FROM {$this->_tbl_utilities} as u

                    WHERE u.site_id={$filters['site_id']} AND {$where_more}";



        $result = $this->db->query($query);

        $resultArray = $result->result_array();



        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($field_array as $filed) {

                    if (array_key_exists($filed, $res)) {

                        $res[$filed] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        return $resultArray;

    }

    

    function allsitesUtilityBasedReportByMonth($filters = array()) {

        

        $field_array = [

            'electricity_cost',

            'fuel_cost',

            'lpg_cost',

            'natural_gas_cost',

            'heating_district_cost',

            'cooling_district_cost',

            'district_heating_fixed_cost',

            'district_cooling_fixed_cost',

            'water_cost'

        ];

        

        $month = $filters['month'];

        $year = $filters['year'];

        $site_ids = $filters['site_ids'];



        $query = "SELECT u.total_electricity_kwh as electricity,u.total_electricity_cost as electricity_cost,

                            u.total_fuel_oil as fuel,u.total_fuel_oil_cost as fuel_cost,

                            u.total_lpg as lpg,u.total_lpg_cost as lpg_cost,

                            u.total_natural_gas as natural_gas,u.total_natural_gas_cost as natural_gas_cost,

                            u.district_heating as heating_district,u.district_heating_cost as heating_district_cost,

                            u.district_cooling as cooling_district,u.district_cooling_cost as cooling_district_cost,

                            u.district_heating_fixed_cost as district_heating_fixed_cost, u.district_cooling_fixed_cost as district_cooling_fixed_cost,

                            u.water_total_consumption as water,u.water_total_consumption_cost as water_cost,

                            u.cdd,

                            u.site_id,

                            u.total_room_night,

                            u.forex,

                            s.rooms_keys,

                            s.site_builtup_area

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE month_id=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . " AND s.id IN($site_ids)";



        $result = $this->db->query($query);

        $resultArray = $result->result_array();



        foreach ($resultArray as $key => $res) {

            $res = array_map('floatval', $res);

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($field_array as $filed) {

                    if (array_key_exists($filed, $res)) {

                        $res[$filed] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        return $resultArray;

    }

    

    function allsitesUtilityBasedReportByAvg($filters = array()) {

        

        $field_array = [

            'electricity_cost',

            'fuel_cost',

            'lpg_cost',

            'natural_gas_cost',

            'heating_district_cost',

            'cooling_district_cost',

            'district_heating_fixed_cost',

            'district_cooling_fixed_cost',

            'water_cost'

        ];

        

        $month = $filters['month'];

        $year = $filters['year'];

        $site_ids = $filters['site_ids'];



        $query = "SELECT SUM(u.total_electricity_kwh) as electricity,

                            SUM(u.total_electricity_cost) as electricity_cost,

                            SUM(u.total_fuel_oil) as fuel,

                            SUM(u.total_fuel_oil_cost) as fuel_cost,

                            SUM(u.total_lpg) as lpg,

                            SUM(u.total_lpg_cost) as lpg_cost,

                            SUM(u.total_natural_gas) as natural_gas,

                            SUM(u.total_natural_gas_cost) as natural_gas_cost,

                            SUM(u.district_heating) as heating_district,

                            SUM(u.district_heating_cost) as heating_district_cost,

                            SUM(u.district_cooling) as cooling_district,

                            SUM(u.district_cooling_cost) as cooling_district_cost,

                            SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost, 

                            SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

                            SUM(u.water_total_consumption) as water,

                            SUM(u.water_total_consumption_cost) as water_cost,

                            SUM(u.cdd) as cdd,

                            SUM(u.total_room_night/(s.rooms_keys*day(last_day(MAKEDATE(u.year_id,(u.month_id*28)))))) as occupancy,

                            u.site_id,

                            SUM(u.total_room_night) as total_room_night,

                            ROUND(AVG(u.forex),4) as forex,

                            s.rooms_keys,

                            s.site_builtup_area

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE month_id<=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . " AND u.total_electricity_cost > 0 

                    AND s.id IN($site_ids)

                    GROUP BY u.site_id";

        

        $result = $this->db->query($query);

        $resultArray = $result->result_array();

        

        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($field_array as $field) {

                    if (array_key_exists($field, $res)) {

                        $res[$field] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        

        return $resultArray;

    }



    function utilityCostBarChartExcelAnnualReport($filters = array()) {

        $start_year = $filters['start_year'];

        $start_month = $filters['start_month'];

        $end_year = $filters['end_year'];

        $end_month = $filters['end_month'];

        $site_id = $filters['site_id'];



        $where_more = ''; 



        $pre_start_year = $start_year - 1;

        $pre_end_year = $end_year - 1;



        if ($start_year == $end_year) {

            $where_more = "(

                                (year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

                                OR

                                (year_id='$pre_start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

                            )";

        } else {

            $where_more = "((

                                 (year_id='$start_year' AND month_id>='$start_month') 

                                     OR 

                                 (year_id='$end_year' AND month_id<='$end_month')

                             ) OR (

                                 (year_id='{$pre_start_year}' AND month_id>='$start_month') 

                                     OR 

                                 (year_id='{$pre_end_year}' AND month_id<='$end_month')

                             ))";

        }



        $query = "SELECT u.total_electricity_cost as electricity, 

                            u.total_fuel_oil_cost as fuel,

                            u.total_fuel_oil as fuel_consumption,

                            u.total_lpg_cost as lpg, 

                            u.total_lpg as lpg_consumption, 

                            u.total_natural_gas_cost as natural_gas, 

                            u.total_natural_gas as natural_gas_consumption, 

                            u.district_heating_cost as heating_district, 

                            u.district_heating as heating_district_consumption, 

                            u.district_cooling_cost as cooling_district, 

                            u.district_cooling as cooling_district_consumption, 

                            u.district_heating_fixed_cost as district_heating_fixed_cost,

                            u.district_cooling_fixed_cost as district_cooling_fixed_cost,

                            u.water_total_consumption_cost as water,

                            u.water_total_consumption as water_consumption,

                            cdd,

                            hdd,

                            (u.water_total_consumption_budget_cost+u.total_consumption_breakdown_budget_cost+u.fuel_total_budget_cost+u.lpg_total_budget_cost+u.natural_gas_total_budget_cost+u.district_heating_total_budget_cost+u.district_cooling_total_budget_cost+u.electricity_total_budget_cost) as total_budget,

                            u.total_purchased_electricity,

                            u.total_purchased_electricity_cost,

                            u.total_electricity_kwh,

                        month_id,year_id,total_room_night,s.rooms_keys,u.forex

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE site_id={$site_id} AND {$where_more} ";



        $result = $this->db->query($query);

        $resultArray = $result->result_array();



        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($this->filed_array as $filed) {

                    if (array_key_exists($filed, $res)) {

                        $res[$filed] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        return $resultArray;

    }



    function costBasedReportForCurrentYearPiecharts($filters) {

        $current_year = $filters['report_year_piechart']; //date('Y');

        $current_month = $filters['report_month_piechart'];



        $query = "SELECT SUM(total_electricity_cost) as electricity, 

                        SUM(total_fuel_oil_cost) as fuel, 

                        SUM(total_lpg_cost) as lpg, 

                        SUM(total_natural_gas_cost) as natural_gas, 

                        SUM(district_heating_cost) as heating_district, 

                        SUM(district_heating_fixed_cost) as district_heating_fixed_cost, 

                        SUM(district_cooling_fixed_cost) as district_cooling_fixed_cost, 

                        SUM(district_cooling_cost) as cooling_district, 

                        SUM(water_total_consumption_cost) as water, 

                        ROUND(AVG(forex),4) as forex

                    FROM {$this->_tbl_utilities}

                    WHERE (year_id='$current_year' AND month_id='$current_month')

                    AND site_id={$this->site_id}";



        $result = $this->db->query($query);

        $rowArray = $result->row_array();



        $forex = $rowArray['forex'];



        foreach ($this->filed_array as $filed) {

            if (array_key_exists($filed, $rowArray)) {

                $rowArray[$filed] *= $forex;

            }

        }

        return $rowArray;

    }



    function allsitesUtilityBasedReportByAvgForBenchmark($filters = array()) {

        

        $field_array = [

            'electricity_cost',

            'fuel_cost',

            'lpg_cost',

            'natural_gas_cost',

            'heating_district_cost',

            'cooling_district_cost',

            'district_heating_fixed_cost',

            'district_cooling_fixed_cost',

            'water_cost'

        ];

        

        $month = $filters['month'];

        $year = $filters['year'];

        $site_ids = $filters['site_ids'];



        $query = "SELECT SUM(u.total_electricity_kwh) as electricity,

                            SUM(u.total_electricity_cost) as electricity_cost,

                            SUM(u.total_fuel_oil) as fuel,

                            SUM(u.total_fuel_oil_cost) as fuel_cost,

                            SUM(u.total_lpg) as lpg,

                            SUM(u.total_lpg_cost) as lpg_cost,

                            SUM(u.total_natural_gas) as natural_gas,

                            SUM(u.total_natural_gas_cost) as natural_gas_cost,

                            SUM(u.district_heating) as heating_district,

                            SUM(u.district_heating_cost) as heating_district_cost,

                            SUM(u.district_cooling) as cooling_district,

                            SUM(u.district_cooling_cost) as cooling_district_cost,

                            SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost, 

                            SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

                            SUM(u.water_total_consumption) as water,

                            SUM(u.water_total_consumption_cost) as water_cost,

                            SUM(u.cdd) as cdd,

                            SUM(u.total_room_night/(s.rooms_keys*day(last_day(MAKEDATE(u.year_id,(u.month_id*28)))))) as occupancy,

                            u.site_id,

                            SUM(u.total_room_night) as total_room_night,

                            ROUND(AVG(u.forex),4) as forex,

                            s.rooms_keys,

                            s.site_builtup_area

                    FROM {$this->_tbl_utilities} as u

                    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

                    WHERE month_id<=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . "

                    AND s.id IN($site_ids)

                    GROUP BY u.site_id";

        

        $result = $this->db->query($query);

        $resultArray = $result->result_array();

        

        foreach ($resultArray as $key => $res) {

            $forex = $res['forex'];

            if (!empty($forex)) {

                foreach ($field_array as $field) {

                    if (array_key_exists($field, $res)) {

                        $res[$field] *= $forex;

                    }

                }

                $resultArray[$key] = $res;

            }

        }

        

        return $resultArray;

    }



}

