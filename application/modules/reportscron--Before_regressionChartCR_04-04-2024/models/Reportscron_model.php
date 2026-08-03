<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Reportscron_model extends Base_Model {



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

    protected $_tbl_user_cron_notifications = 'user_cron_notifications';

    public $utilities_month = "";

    public $utilities_year = "";

    public $site_id = "";

    public $role_id = "";

    public $user_id = "";



    function __construct() {

	parent::__construct();

    }



    function get_site_listing_for_reports($site_id = 0) {

	$this->db->select('*');

	$this->db->from($this->_tbl_sites);

	$this->db->where('status =', 1);

	$this->db->where('id =', $site_id);

	$result = $this->db->get();

	return $result->result_array();

    }



    function monthlyUtilityBasedReportByCost($filters = array()) {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];



	$pre_start_year = $start_year - 1;

	$pre_end_year = $end_year - 1;





	$where_more = '';





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

		u.electricity_total_budget_cost as electricity_budget,

		u.total_fuel_oil_cost as fuel,

		u.fuel_total_budget_cost as fuel_budget,

		u.total_lpg_cost as lpg,

		u.lpg_total_budget_cost as lpg_budget,

		u.total_natural_gas_cost as natural_gas,

		u.natural_gas_total_budget_cost as natural_gas_budget,

		u.district_heating_cost as heating_district,

		u.district_heating_total_budget_cost as heating_district_budget,

		u.district_cooling_cost as cooling_district,

		u.district_cooling_total_budget_cost as cooling_district_budget,

		u.district_heating_fixed_cost as district_heating_fixed_cost,

		u.district_cooling_fixed_cost as district_cooling_fixed_cost,

			    u.lpg_fixed_cost as lpg_fixed_cost,

			    u.natural_gas_fixed_cost as natural_gas_fixed_cost,

			    u.water_fixed_cost as water_fixed_cost,
		u.water_total_consumption_cost as water,

		u.water_total_consumption_budget_cost as water_budget,

		u.cdd,u.hdd,

		u.month_id,

		u.year_id,

		u.total_guests,

		u.total_room_night,

		u.total_laundered,

		s.site_builtup_area,s.cooled_builtup_area,s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more}

		ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function monthlyUtilityBasedReportByUnit($filters = array()) {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];



	$pre_start_year = $start_year - 1;

	$pre_end_year = $end_year - 1;





	$where_more = '';





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





	$query = "SELECT u.total_electricity_kwh as electricity, u.electricity_total_budget as electricity_budget,

		u.total_fuel_oil as fuel, u.fuel_total_budget as fuel_budget,

		u.total_lpg as lpg, u.lpg_total_budget as lpg_budget,

		u.total_natural_gas as natural_gas, u.natural_gas_total_budget as natural_gas_budget,

		u.district_heating as heating_district, u.district_heating_total_budget as heating_district_budget,

		u.district_cooling as cooling_district, u.district_cooling_total_budget as cooling_district_budget,

		u.water_total_consumption as water,u.water_total_consumption_budget as water_budget,u.water_utility_supply as water_utility,u.waste_water as waste_water,u.water_Cisterns as water_cisterns,

		u.cdd,u.hdd,

		u.month_id,

		u.year_id,

		u.total_guests,

		u.total_room_night,

		u.total_laundered,

		s.site_builtup_area,s.cooled_builtup_area,s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more}

		ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function costBudget($filters = array()) {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];



	$pre_start_year = $start_year - 1;

	$pre_end_year = $end_year - 1;





	$where_more = '';





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





	$query = "SELECT u.electricity_total_budget_cost,

		u.fuel_total_budget_cost,

		u.lpg_total_budget_cost,

		u.natural_gas_total_budget_cost,

		u.district_heating_total_budget_cost,

		u.district_cooling_total_budget_cost,

		u.water_total_consumption_budget_cost,

		u.month_id,

		u.year_id

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more}

		ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function unitBudget($filters = array()) {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];



	$pre_start_year = $start_year - 1;

	$pre_end_year = $end_year - 1;





	$where_more = '';





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





	$query = "SELECT u.electricity_total_budget,

		u.fuel_total_budget,

		u.lpg_total_budget,

		u.natural_gas_total_budget,

		u.district_heating_total_budget,

		u.district_cooling_total_budget,

		u.water_total_consumption_budget,

		u.month_id,

		u.year_id

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more}

		ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function kwhUnitBasedReportForCurrentYear($filters) {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];

	$query = "SELECT SUM(total_electricity_kwh) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

		SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

		FROM {$this->_tbl_utilities}

		WHERE (year_id='$current_year' AND month_id <='$current_month')

		AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	if ($result) {

	    return $result->row_array();
	}
    }



    function costBasedReportForCurrentYear($filters) {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];



	$query = "SELECT SUM(total_electricity_cost) as electricity, SUM(total_fuel_oil_cost) as fuel, SUM(total_lpg_cost) as lpg, SUM(total_natural_gas_cost) as natural_gas, SUM(district_heating_cost) as heating_district, SUM(district_heating_fixed_cost) as district_heating_fixed_cost, SUM(district_cooling_fixed_cost) as district_cooling_fixed_cost, SUM(district_cooling_cost) as cooling_district, SUM(water_total_consumption_cost) as water

		FROM {$this->_tbl_utilities}

		WHERE (year_id='$current_year' AND month_id<='$current_month')

		AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();

    }



    function kwhUnitBasedReportForPreviousMonth($filters = array()) {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT total_electricity_kwh as electricity, district_heating as heating_district, district_cooling as cooling_district,

		total_fuel_oil as fuel,total_lpg as lpg,total_natural_gas as natural_gas

		FROM {$this->_tbl_utilities}

		WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();

    }



    function costBasedReportForPreviousMonth($filters = array()) {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT total_electricity_cost as electricity, total_fuel_oil_cost as fuel, total_lpg_cost as lpg, total_natural_gas_cost as natural_gas, district_heating_cost as heating_district, district_cooling_cost as cooling_district, district_heating_fixed_cost as district_heating_fixed_cost, district_cooling_fixed_cost as district_cooling_fixed_cost, water_total_consumption_cost as water

		FROM {$this->_tbl_utilities}

		WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();

    }



    function utilityCostBarChart($filters = array()) {

	$start_year = (int) $filters['start_year'];

	$start_month = (int) $filters['start_month'];

	$end_year = (int) $filters['end_year'];

	$end_month = (int) $filters['end_month'];



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

			    u.lpg_fixed_cost as lpg_fixed_cost,

			    u.natural_gas_fixed_cost as natural_gas_fixed_cost,

			    u.water_fixed_cost as water_fixed_cost,

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

		month_id,year_id,total_room_night,s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more} ";



	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function utilityUnitBarChart($filters = array()) {

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



	$query = "SELECT u.total_electricity_kwh as electricity,

		u.total_fuel_oil as fuel,

		u.total_lpg as lpg,

		u.total_natural_gas as natural_gas,

		u.district_heating as heating_district,

		u.district_cooling as cooling_district,

		u.water_total_consumption as water,

		cdd,

		hdd,

		(u.water_total_consumption_budget_cost+u.total_consumption_breakdown_budget_cost+u.fuel_total_budget_cost+u.lpg_total_budget_cost+u.natural_gas_total_budget_cost+u.district_heating_total_budget_cost+u.district_cooling_total_budget_cost+u.electricity_total_budget_cost) as total_budget,

		u.total_purchased_electricity,

		u.total_purchased_electricity_cost,

		u.total_electricity_kwh,

		month_id,year_id,total_room_night,s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more} ";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
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



	$query = "SELECT SUM(u.total_electricity_cost) as electricity, SUM(u.total_electricity_kwh) as electricity_unit,

		SUM(u.total_fuel_oil_cost) as fuel, SUM(u.total_fuel_oil) as fuel_unit,

		SUM(u.total_lpg_cost) as lpg, SUM(u.total_lpg) as lpg_unit,

		SUM(u.total_natural_gas_cost) as natural_gas, SUM(u.total_natural_gas) as natural_gas_unit,

		SUM(u.district_heating_cost) as heating_district, SUM(u.district_heating) as heating_district_unit,

		SUM(u.district_cooling_cost) as cooling_district, SUM(u.district_cooling) as cooling_district_unit,

		SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost,

		SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

		SUM(u.water_total_consumption_cost) as water, SUM(u.water_total_consumption) as water_unit,

		SUM(u.cdd) as cdd,

		SUM(u.hdd) as hdd,

		SUM(u.total_room_night) as total_room_night,

		((SUM(u.total_room_night)/(s.rooms_keys*IF(u.year_id % 4 = 0, 366, 365))) * 100) as occupancy,

		SUM(u.total_electricity_kwh) as total_electricity_kwh,

		u.year_id as year_id,

		s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE u.site_id={$this->site_id} AND {$where_more}

		GROUP BY u.year_id";

	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesUtilityBasedReportByMonth($filters = array()) {

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

			    u.lpg_fixed_cost as lpg_fixed_cost,

			    u.natural_gas_fixed_cost as natural_gas_fixed_cost,

			    u.water_fixed_cost as water_fixed_cost,

			    u.water_total_consumption as water,u.water_total_consumption_cost as water_cost,

		u.cdd,

		u.site_id,

		u.total_room_night,

		s.rooms_keys,

			    s.site_type,

			    s.site_builtup_area

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE month_id=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . " AND s.id IN($site_ids)";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesUtilityBasedReportByAvg($filters = array()) {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT SUM(u.total_electricity_kwh) as electricity,SUM(u.total_electricity_cost) as electricity_cost,

		SUM(u.total_fuel_oil) as fuel,SUM(u.total_fuel_oil_cost) as fuel_cost,

		SUM(u.total_lpg) as lpg,SUM(u.total_lpg_cost) as lpg_cost,

		SUM(u.total_natural_gas) as natural_gas,SUM(u.total_natural_gas_cost) as natural_gas_cost,

		SUM(u.district_heating) as heating_district,SUM(u.district_heating_cost) as heating_district_cost,

		SUM(u.district_cooling) as cooling_district,SUM(u.district_cooling_cost) as cooling_district_cost,

		SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost,

		SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

		SUM(u.water_total_consumption) as water,SUM(u.water_total_consumption_cost) as water_cost,

		SUM(u.cdd) as cdd,

		SUM(u.total_room_night/(s.rooms_keys*day(last_day(MAKEDATE(u.year_id,(u.month_id*28)))))) as occupancy,

		u.site_id,

		SUM(u.total_room_night) as total_room_night,

			    s.rooms_keys,

			    s.site_type,

		s.site_builtup_area

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE month_id<=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . "

		AND s.id IN($site_ids)

		GROUP BY u.site_id";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesTariffBasedReportByMonth($filters = array()) {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT AVG(t.tariff) as tariff,

		t.site_id,

		s.rooms_keys,

		s.site_builtup_area

		FROM {$this->_tbl_electricity_tariff} as t

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=t.site_id

		WHERE t.month_id=" . $this->db->escape($month) . " AND t.year_id=" . $this->db->escape($year) . "

		AND s.id IN($site_ids)

		GROUP BY site_id, month_id, year_id";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesTariffBasedReportByAvg($filters = array()) {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT SUM(avgtariff) as tariff,site_id,rooms_keys,site_builtup_area

		FROM (SELECT AVG(t.tariff) as avgtariff,t.site_id,s.rooms_keys,s.site_builtup_area

		FROM {$this->_tbl_electricity_tariff} as t

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=t.site_id

		WHERE month_id<=" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . "

		AND s.id IN($site_ids)

		GROUP BY site_id, month_id, year_id

		) as newtariff

		GROUP BY site_id";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function getSites($site_filters = array()) {

	$this->db->select("s.id,s.site_location_name,c.country");

	$this->db->from($this->_tbl_sites . ' AS s');

	$this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');

	$this->db->where('s.status', 1);

	if ($site_filters['site_type'] == 1 || $site_filters['site_type'] == 2) {

	    $this->db->where('s.site_type', $site_filters['site_type']);

	} else if ($site_filters['site_type'] == 3) {

	    $this->db->where_in('s.id', $site_filters['site_ids']);

	}



	$this->db->order_by('s.id', 'asc');



	$result = $this->db->get();

	return $result->result_array();

    }



    function getMaxCurrentMonth() {

	$this->db->select("MAX(month_id) as month_id");

	$this->db->from($this->_tbl_utilities);

	$this->db->where('year_id', date('Y'));



	$result = $this->db->get();

	return $result->row()->month_id;

    }



    function getEMACategories() {

	$this->db->select('*');

	$this->db->from($this->_tbl_projects_categories . ' as pc');

	$this->db->where('pc.status !=', -1);

	$result = $this->db->get();

	return $this->db->custom_result($result);

    }



    function getEMAPublicProjects($category_id = 0) {

	$this->db->select('p.*,pc.name,h.hotel_name');

	$this->db->from($this->_tbl_projects . ' as p');

	$this->db->join($this->_tbl_projects_categories . ' as pc', 'pc.id = p.project_category_id');

	$this->db->join($this->_tbl_hotels . ' as h', 'h.id = p.hotel_id');



	$this->db->where('p.project_category_id', $category_id);

	$this->db->where('p.status !=', -1);

	$result = $this->db->get();

	return $this->db->custom_result($result);

    }



    function get_ema_actionplans_todos_bysite($actiondata) {

	$this->db->select('t.*,a.status as astatus,a.target_date,a.completed_date,a.kwh_savings,a.cost_savings');

	$this->db->from($this->_tbl_projects_todos . ' as t');

	$this->db->join($this->_tbl_action_plan . ' as a', 'a.project_to_do_id = t.id');

	$this->db->where('t.project_id', $actiondata['project_id']);

	$this->db->where('a.site_id', $actiondata['site_id']);

	//$this->db->where('a.user_id', $actiondata['user_id']);

	$this->db->where('t.status != ', '-1');

	$this->db->group_by('a.project_to_do_id');

	$result = $this->db->get();

	return $result->result_array();

    }



    function getEMACategoriesList() {

	$this->db->select('*');

	$this->db->from($this->_tbl_projects_categories . ' as pc');

	$this->db->where('pc.status', 1);

	$result = $this->db->get();

	return $this->db->custom_result($result);

    }



    function getDailyReportData($month, $year, $to_date = 31) {



	$query = "SELECT * FROM {$this->_tbl_utilities_cost_daily}

		WHERE month_id = '" . $month . "'

		AND year_id = '" . $year . "'

		AND date_id <= '" . $to_date . "'

		AND site_id = '" . $this->site_id . "'

		order by date_id";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function utilityWasteChart($filters = array()) {

	$start_year = (int) $filters['start_year'];

	$start_month = (int) $filters['start_month'];

	$end_year = (int) $filters['end_year'];

	$end_month = (int) $filters['end_month'];



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





	$query = "SELECT *

		month_id,year_id,total_room_night,s.rooms_keys

		FROM {$this->_tbl_utilities} as u

		LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		WHERE site_id={$this->site_id} AND {$where_more} ";

	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }


    public function get_daily_reading_static_data($site_id = 0, $month, $year, $to_date = 31) {

	$query = "SELECT * FROM daily_reading_utilities_data

		WHERE month_id = '" . $month . "'

		AND year_id = '" . $year . "'

		AND date_id <= '" . $to_date . "'

		AND site_id = '" . $site_id . "'

		order by date_id";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    public function get_daily_reading_data($site_id = 0, $month, $year, $to_date = 31) {

	$utilities = array();



	$this->db->select('*');

	$this->db->from('daily_reading_utilities');

	$result = $this->db->get();

	$utilities = $result->result_array();



	$this->db->select('*');

	$this->db->from('daily_reading_utilities_titles');

	$this->db->where('site_id', $site_id);

	$result = $this->db->get();

	$utility_titles = $result->result_array();



	$query = "SELECT * FROM daily_reading_utilities_title_data

					WHERE month_id = '" . $month . "'

					AND year_id = '" . $year . "'

					AND date_id <= '" . $to_date . "'

					AND site_id = '" . $site_id . "'

					order by date_id";



	$result = $this->db->query($query);

	$utility_titles_data = $result->result_array();



	if (!empty($utilities)) {

	    foreach ($utilities as $key => $value) {

		$utilities[$key]['submission_titles'] = array();

		if (!empty($utility_titles)) {

		    foreach ($utility_titles as $key1 => $value1) {

			if ($value['id'] == $value1['utility_id']) {



			    $value1['reading'] = array();

			    $total = 0;



			    if (!empty($utility_titles_data)) {

				foreach ($utility_titles_data as $key2 => $value2) {

				    if ($value1['id'] == $value2['utility_title_id']) {

					$value1['reading'][] = $value2;

					$total += $value2['value'];

				    }

				}

			    }



			    $value1['total'] = $total;



			    $utilities[$key]['submission_titles'][] = $value1;

			}

		    }

		}

	    }

	}



	return $utilities;

    }



    function getFIlterUtilityMonthly($filters = array()) {

	$this->db->select('*');

	$this->db->from($this->_tbl_utilities);



	if ($filters['site_id']) {

	    $this->db->where('site_id', $filters['site_id']);

	}



	if ($filters['month_id']) {

	    $this->db->where('month_id', $filters['month_id']);

	}



	if ($filters['year_id']) {

	    $this->db->where('year_id', $filters['year_id']);

	}



	$result = $this->db->get();

	return $result->result_array();

    }



    function getUtilityActualBudgetData($filters = array()) {

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

		u.water_total_consumption_budget_cost as water_total_consumption_cost_budget

		FROM {$this->_tbl_utilities} as u

		WHERE u.site_id={$filters['site_id']} AND {$where_more}";



	$result = $this->db->query($query);

	return $result->result_array();

    }



    function getUserReports($uid) {

	$reports = array();

	$this->db->select('*');

	$this->db->from($this->_tbl_user_cron_notifications);

	$this->db->where('user_id', $uid);



	$result = $this->db->get();

	$result_array = $result->result_array();

	if (!empty($result_array)) {



	    foreach ($result_array as $report) {

		$reports[] = $report['notifications'];

	    }

	}

	return $reports;

    }



    function getUserSites($id) {

	$sites = array();

	// $query = "SELECT s.id, s.site_location_name FROM `user_sites` AS u INNER JOIN `sites` AS s ON u.site_id = s.id where u.user_id = {$id} order by s.site_location_name ASC";

	$query = "SELECT s.id, s.site_location_name FROM `user_sites` AS u INNER JOIN `sites` AS s ON u.site_id = s.id where u.user_id = {$id}";

	$result = $this->db->query($query);

	$result_array = $result->result_array();

	if (!empty($result_array)) {

	    foreach ($result_array as $site) {

		$sites[$site['id']] = $site['site_location_name'];

	    }

	}

	return $sites;

    }



    function getUserDetails() {

	$this->db->select('*');

	$this->db->from('users');

	$this->db->where('status', 1);

	$result = $this->db->get();

	$user_array = $result->result_array();



	foreach ($user_array as $key => $user) {

	    $reports = $this->getUserReports($user['id']);

	    $sites = $this->getUserSites($user['id']);

	    $user_array[$key]['reports'] = $reports;

	    $user_array[$key]['sites'] = $sites;

	}

	return $user_array;

    }



    function getHotel() {

	$this->db->select('*');

	$this->db->from('hotels');

	$this->db->where('id', 1);

	$result = $this->db->get();

	$user_array = $result->row_array();



	return $user_array;

    }



    function kwhUnitBasedReportForCurrentYearPieChartsForCron($filters) {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['report_month'];



	$query = "SELECT SUM(total_electricity_kwh) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

			SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id <='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();

    }



     function getUserDetailsWithRegion() {

	/* $this->db->select('users.*, GROUP_CONCAT(DISTINCT(user_sites.site_id) ORDER BY user_sites.site_id) as sites, GROUP_CONCAT(DISTINCT(user_regions.region_id) ORDER BY user_regions.region_id) as user_regions');
	$this->db->from('users');
	$this->db->join('user_cron_notifications', 'users.id = user_cron_notifications.user_id', 'left');
	$this->db->join('user_sites', 'users.id = user_sites.user_id', 'left');
	$this->db->join('user_regions', 'users.id = user_regions.user_id', 'left');
	$this->db->where('users.status', 1);
	$this->db->where('user_cron_notifications.notifications', 'upper_management');
	$this->db->group_by('users.id');
	$query = $this->db->get();
	$result = $query->result();
	foreach ($result as $key => $value) {
	    if($key == 'sites' || $key == 'user_regions') {
		$result[$key] = explode(',',$value);
	    }
	}
	return $result; */
	$this->db->select('*');

	$this->db->from('users');

	$this->db->where('status', 1);

	$result = $this->db->get();

	$user_array = $result->result_array();

	foreach ($user_array as $key => $user) {

	    $regions_count = 0;

	    $site_count = 0;

	    $user_regions = array();

	    $sites_array = array();

	    $site_ids = [];

	    $reports = $this->getUserReports($user['id']);

	    $sites = $this->getUserSites($user['id']);



	    if(!empty($sites)){

		foreach ($sites as $site_id =>$site_name) {

		    $site_ids[] =$site_id ;

		}

		$sites_array = implode(",", $site_ids);

		$user_regions =$this->getSiteRegion($sites_array);

		$regions_count = count($user_regions);

		$site_count = count($sites);



	    }



	    $user_array[$key]['reports'] = $reports;

	    $user_array[$key]['sites'] = $sites;

	    $user_array[$key]['user_regions'] = $user_regions;

	    $user_array[$key]['regions_count'] = $regions_count;

	    $user_array[$key]['site_count'] = $site_count;



	}

	return $user_array;

    }



    function getSiteRegion($sites_array){



	$this->db->select('s.region_id,r.region_name');

	$this->db->from($this->_tbl_sites . ' AS s');

	$this->db->join('regions as r', 's.region_id = r.id', 'LEFT');

	$this->db->where("s.id IN (".$sites_array.")",NULL, false);

	$result = $this->db->get();

	$arr = $result->result_array();

	// pre($arr);

	if(!empty($arr)){

	    foreach ($arr as $key =>$value) {

		$region_ids[$value['region_id']] =$value['region_name'] ;

	    }



	    // $arr_unique = array_unique($region_ids);

	    // pre($region_ids);

	    // $final_arr_count = count($arr_unique);



	    return $region_ids;

	}

    }



    function getUserSitesWithRegionId($id,$region_id) {

	$sites = array();

	$query = "SELECT s.id, s.site_location_name FROM `user_sites` AS u INNER JOIN `sites` AS s ON u.site_id = s.id where u.user_id = {$id} and s.region_id = {$region_id} order by s.site_location_name ASC";

	$result = $this->db->query($query);

	$result_array = $result->result_array();

	if (!empty($result_array)) {

	    foreach ($result_array as $site) {

		$sites[$site['id']] = $site['site_location_name'];

	    }

	}

	return $sites;

    }

    public function fetchUpdatedRoomKeys($result) {
	foreach ($result as $key => $value) {
	    foreach ($value as $subkey => $subvalue) {
		if($subkey == 'rooms_keys') {
		    $this->load->model('sites/sites_model');
		    $dataFetch['site_id'] = $this->site_id;
		    $dataFetch['area_update_field'] = $subkey;
		    $latestAreaEntry = $this->sites_model->getlatestSiteArea($dataFetch);
		    if(isset($latestAreaEntry) && !empty($latestAreaEntry)) {
			$latestAreaEntry = (array) $latestAreaEntry;
			$result[$key][$subkey] = $latestAreaEntry['area_update_value'];
		    }
		} else {
		    continue;
		}
	    }
	}
	return $result;
    }

}

