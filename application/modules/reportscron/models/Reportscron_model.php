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





	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

		COALESCE(u.electricity_total_budget_cost, 0) as electricity_budget,

		COALESCE(u.total_fuel_oil_cost, 0) as fuel,

		COALESCE(u.fuel_total_budget_cost, 0) as fuel_budget,

		COALESCE(u.total_lpg_cost, 0) as lpg,

		COALESCE(u.lpg_total_budget_cost, 0) as lpg_budget,

		COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

		COALESCE(u.natural_gas_total_budget_cost, 0) as natural_gas_budget,

		COALESCE(u.district_heating_cost, 0) as heating_district,

		COALESCE(u.district_heating_total_budget_cost, 0) as heating_district_budget,

		COALESCE(u.district_cooling_cost, 0) as cooling_district,

		COALESCE(u.district_cooling_total_budget_cost, 0) as cooling_district_budget,

		COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

		COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,
		COALESCE(u.water_total_consumption_cost, 0) as water,

		COALESCE(u.water_total_consumption_budget_cost, 0) as water_budget,

		COALESCE(u.cdd, 0) as cdd, COALESCE(u.hdd, 0) as hdd,

		u.month_id,

		u.year_id,

		COALESCE(u.total_guests, 0) as total_guests,
		COALESCE(u.total_guests_budget, 0) as total_guests_budget,

		COALESCE(u.total_room_night, 0) as total_room_night,
		COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,

		COALESCE(u.total_laundered, 0) as total_laundered,

		COALESCE(s.site_builtup_area, 0) as site_builtup_area, COALESCE(s.cooled_builtup_area, 0) as cooled_builtup_area, COALESCE(s.rooms_keys, 0) as rooms_keys

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





	$query = "SELECT COALESCE(u.total_electricity_kwh, 0) as electricity, COALESCE(u.electricity_total_budget, 0) as electricity_budget,

		COALESCE(u.total_fuel_oil, 0) as fuel, COALESCE(u.fuel_total_budget, 0) as fuel_budget,

		COALESCE(u.total_lpg, 0) as lpg, COALESCE(u.lpg_total_budget, 0) as lpg_budget,

		COALESCE(u.total_natural_gas, 0) as natural_gas, COALESCE(u.natural_gas_total_budget, 0) as natural_gas_budget,

		COALESCE(u.district_heating, 0) as heating_district, COALESCE(u.district_heating_total_budget, 0) as heating_district_budget,

		COALESCE(u.district_cooling, 0) as cooling_district, COALESCE(u.district_cooling_total_budget, 0) as cooling_district_budget,

		COALESCE(u.water_total_consumption, 0) as water, COALESCE(u.water_total_consumption_budget, 0) as water_budget, COALESCE(u.water_utility_supply, 0) as water_utility, COALESCE(u.waste_water, 0) as waste_water, COALESCE(u.water_Cisterns, 0) as water_cisterns,

		COALESCE(u.cdd, 0) as cdd, COALESCE(u.hdd, 0) as hdd,

		u.month_id,

		u.year_id,

		COALESCE(u.total_guests, 0) as total_guests,
		COALESCE(u.total_guests_budget, 0) as total_guests_budget,

		COALESCE(u.total_room_night, 0) as total_room_night,
		COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,

		COALESCE(u.total_laundered, 0) as total_laundered,

		COALESCE(s.site_builtup_area, 0) as site_builtup_area, COALESCE(s.cooled_builtup_area, 0) as cooled_builtup_area, COALESCE(s.rooms_keys, 0) as rooms_keys

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





	$query = "SELECT COALESCE(u.electricity_total_budget_cost, 0) as electricity_total_budget_cost,

		COALESCE(u.fuel_total_budget_cost, 0) as fuel_total_budget_cost,

		COALESCE(u.lpg_total_budget_cost, 0) as lpg_total_budget_cost,

		COALESCE(u.natural_gas_total_budget_cost, 0) as natural_gas_total_budget_cost,

		COALESCE(u.district_heating_total_budget_cost, 0) as district_heating_total_budget_cost,

		COALESCE(u.district_cooling_total_budget_cost, 0) as district_cooling_total_budget_cost,

		COALESCE(u.water_total_consumption_budget_cost, 0) as water_total_consumption_budget_cost,

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





	$query = "SELECT COALESCE(u.electricity_total_budget, 0) as electricity_total_budget,

		COALESCE(u.fuel_total_budget, 0) as fuel_total_budget,

		COALESCE(u.lpg_total_budget, 0) as lpg_total_budget,

		COALESCE(u.natural_gas_total_budget, 0) as natural_gas_total_budget,

		COALESCE(u.district_heating_total_budget, 0) as district_heating_total_budget,

		COALESCE(u.district_cooling_total_budget, 0) as district_cooling_total_budget,

		COALESCE(u.water_total_consumption_budget, 0) as water_total_consumption_budget,

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

	$query = "SELECT SUM(COALESCE(total_electricity_kwh, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

		SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

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



	$query = "SELECT SUM(COALESCE(total_electricity_cost, 0)) as electricity, SUM(COALESCE(total_fuel_oil_cost, 0)) as fuel, SUM(COALESCE(total_lpg_cost, 0)) as lpg, SUM(COALESCE(total_natural_gas_cost, 0)) as natural_gas, SUM(COALESCE(district_heating_cost, 0)) as heating_district, SUM(COALESCE(district_heating_fixed_cost, 0)) as district_heating_fixed_cost, SUM(COALESCE(district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost, SUM(COALESCE(district_cooling_cost, 0)) as cooling_district, SUM(COALESCE(water_total_consumption_cost, 0)) as water

		FROM {$this->_tbl_utilities}

		WHERE (year_id='$current_year' AND month_id<='$current_month')

		AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();

    }



    function kwhUnitBasedReportForPreviousMonth($filters = array()) {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT COALESCE(total_electricity_kwh, 0) as electricity, COALESCE(district_heating, 0) as heating_district, COALESCE(district_cooling, 0) as cooling_district,

		COALESCE(total_fuel_oil, 0) as fuel, COALESCE(total_lpg, 0) as lpg, COALESCE(total_natural_gas, 0) as natural_gas

		FROM {$this->_tbl_utilities}

		WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();

    }



    function costBasedReportForPreviousMonth($filters = array()) {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT COALESCE(total_electricity_cost, 0) as electricity, COALESCE(total_fuel_oil_cost, 0) as fuel, COALESCE(total_lpg_cost, 0) as lpg, COALESCE(total_natural_gas_cost, 0) as natural_gas, COALESCE(district_heating_cost, 0) as heating_district, COALESCE(district_cooling_cost, 0) as cooling_district, COALESCE(district_heating_fixed_cost, 0) as district_heating_fixed_cost, COALESCE(district_cooling_fixed_cost, 0) as district_cooling_fixed_cost, COALESCE(water_total_consumption_cost, 0) as water

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

	$query = "
		SELECT
			COALESCE(u.total_electricity_cost, 0) AS electricity,
			COALESCE(u.total_fuel_oil_cost, 0) AS fuel,
			COALESCE(u.total_fuel_oil, 0) AS fuel_consumption,

			COALESCE(u.total_lpg_cost, 0) AS lpg,
			COALESCE(u.total_lpg, 0) AS lpg_consumption,

			COALESCE(u.total_natural_gas_cost, 0) AS natural_gas,
			COALESCE(u.total_natural_gas, 0) AS natural_gas_consumption,

			COALESCE(u.district_heating_cost, 0) AS heating_district,
			COALESCE(u.district_heating, 0) AS heating_district_consumption,

			COALESCE(u.district_cooling_cost, 0) AS cooling_district,
			COALESCE(u.district_cooling, 0) AS cooling_district_consumption,

			COALESCE(u.district_heating_fixed_cost, 0) AS district_heating_fixed_cost,
			COALESCE(u.district_cooling_fixed_cost, 0) AS district_cooling_fixed_cost,
			COALESCE(u.lpg_fixed_cost, 0) AS lpg_fixed_cost,
			COALESCE(u.natural_gas_fixed_cost, 0) AS natural_gas_fixed_cost,
			COALESCE(u.water_fixed_cost, 0) AS water_fixed_cost,

			COALESCE(u.water_total_consumption_cost, 0) AS water,
			COALESCE(u.water_total_consumption, 0) AS water_consumption,

			COALESCE(u.total_fleet_petrol_cost, 0) AS fleet_petrol,
			COALESCE(u.fleet_petrol, 0) AS fleet_petrol_consumption,

			COALESCE(u.onsite_generators_quantity, 0) AS onsite_generator,
			COALESCE(u.onsite_generators_fuel_oil_quantity, 0) AS onsite_generator_fuel_oil,
			COALESCE(u.onsite_generators_natural_gas_quantity, 0) AS onsite_generator_natural_gas,

			COALESCE(u.total_renewable_energy_production, 0) AS renewable_energy,

			COALESCE(cdd, 0) AS cdd,
			COALESCE(hdd, 0) AS hdd,

			(
				COALESCE(u.water_total_consumption_budget_cost, 0) +
				COALESCE(u.total_consumption_breakdown_budget_cost, 0) +
				COALESCE(u.fuel_total_budget_cost, 0) +
				COALESCE(u.lpg_total_budget_cost, 0) +
				COALESCE(u.natural_gas_total_budget_cost, 0) +
				COALESCE(u.district_heating_total_budget_cost, 0) +
				COALESCE(u.district_cooling_total_budget_cost, 0) +
				COALESCE(u.electricity_total_budget_cost, 0)
			) AS total_budget,

			COALESCE(u.total_purchased_electricity, 0) AS total_purchased_electricity,
			COALESCE(u.total_purchased_electricity_cost, 0) AS total_purchased_electricity_cost,
			COALESCE(u.total_electricity_kwh, 0) AS total_electricity_kwh,

			month_id,
			year_id,

			COALESCE(total_room_night, 0) AS total_room_night,
			COALESCE(s.rooms_keys, 0) AS rooms_keys,
			COALESCE(total_guests, 0) AS total_guests,
			COALESCE(total_guests_budget, 0) AS total_guests_budget,
			COALESCE(total_room_night_budget, 0) AS total_room_night_budget

		FROM {$this->_tbl_utilities} AS u
		LEFT JOIN {$this->_tbl_sites} AS s ON s.id = u.site_id
		WHERE site_id = {$this->site_id}
		AND {$where_more}
		";

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



	$query = "SELECT COALESCE(u.total_electricity_kwh, 0) as electricity,

		COALESCE(u.total_fuel_oil, 0) as fuel,

		COALESCE(u.total_lpg, 0) as lpg,

		COALESCE(u.total_natural_gas, 0) as natural_gas,

		COALESCE(u.district_heating, 0) as heating_district,

		COALESCE(u.district_cooling, 0) as cooling_district,

		COALESCE(u.water_total_consumption, 0) as water,

		COALESCE(cdd, 0) as cdd,

		COALESCE(hdd, 0) as hdd,

		(
			COALESCE(u.water_total_consumption_budget_cost, 0) +
			COALESCE(u.total_consumption_breakdown_budget_cost, 0) +
			COALESCE(u.fuel_total_budget_cost, 0) +
			COALESCE(u.lpg_total_budget_cost, 0) +
			COALESCE(u.natural_gas_total_budget_cost, 0) +
			COALESCE(u.district_heating_total_budget_cost, 0) +
			COALESCE(u.district_cooling_total_budget_cost, 0) +
			COALESCE(u.electricity_total_budget_cost, 0)
		) as total_budget,

		COALESCE(u.total_purchased_electricity, 0) as total_purchased_electricity,

		COALESCE(u.total_purchased_electricity_cost, 0) as total_purchased_electricity_cost,

		COALESCE(u.total_electricity_kwh, 0) as total_electricity_kwh,

		month_id,year_id,
		COALESCE(total_room_night, 0) as total_room_night,
		COALESCE(s.rooms_keys, 0) as rooms_keys,
		COALESCE(total_guests, 0) as total_guests,
		COALESCE(total_guests_budget, 0) as total_guests_budget,
		COALESCE(total_room_night_budget, 0) as total_room_night_budget

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



	$query = "SELECT SUM(COALESCE(u.total_electricity_cost, 0)) as electricity, SUM(COALESCE(u.total_electricity_kwh, 0)) as electricity_unit,

		SUM(COALESCE(u.total_fuel_oil_cost, 0)) as fuel, SUM(COALESCE(u.total_fuel_oil, 0)) as fuel_unit,

		SUM(COALESCE(u.total_lpg_cost, 0)) as lpg, SUM(COALESCE(u.total_lpg, 0)) as lpg_unit,

		SUM(COALESCE(u.total_natural_gas_cost, 0)) as natural_gas, SUM(COALESCE(u.total_natural_gas, 0)) as natural_gas_unit,

		SUM(COALESCE(u.district_heating_cost, 0)) as heating_district, SUM(COALESCE(u.district_heating, 0)) as heating_district_unit,

		SUM(COALESCE(u.district_cooling_cost, 0)) as cooling_district, SUM(COALESCE(u.district_cooling, 0)) as cooling_district_unit,

		SUM(COALESCE(u.district_heating_fixed_cost, 0)) as district_heating_fixed_cost,

		SUM(COALESCE(u.district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost,

		SUM(COALESCE(u.water_total_consumption_cost, 0)) as water, SUM(COALESCE(u.water_total_consumption, 0)) as water_unit,

		SUM(COALESCE(u.cdd, 0)) as cdd,

		SUM(COALESCE(u.hdd, 0)) as hdd,

		SUM(COALESCE(u.total_guests, 0)) as total_guests,
		SUM(COALESCE(u.total_guests_budget, 0)) as total_guests_budget,
		SUM(COALESCE(u.total_room_night, 0)) as total_room_night,
		SUM(COALESCE(u.total_room_night_budget, 0)) as total_room_night_budget,

		((SUM(COALESCE(u.total_room_night, 0))/(COALESCE(s.rooms_keys, 0)*IF(u.year_id % 4 = 0, 366, 365))) * 100) as occupancy,

		SUM(COALESCE(u.total_electricity_kwh, 0)) as total_electricity_kwh,

		u.year_id as year_id,

		COALESCE(s.rooms_keys, 0) as rooms_keys

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



	$query = "SELECT COALESCE(u.total_electricity_kwh, 0) as electricity, COALESCE(u.total_electricity_cost, 0) as electricity_cost,

		COALESCE(u.total_fuel_oil, 0) as fuel, COALESCE(u.total_fuel_oil_cost, 0) as fuel_cost,

		COALESCE(u.total_lpg, 0) as lpg, COALESCE(u.total_lpg_cost, 0) as lpg_cost,

		COALESCE(u.total_natural_gas, 0) as natural_gas, COALESCE(u.total_natural_gas_cost, 0) as natural_gas_cost,

		COALESCE(u.district_heating, 0) as heating_district, COALESCE(u.district_heating_cost, 0) as heating_district_cost,

		COALESCE(u.district_cooling, 0) as cooling_district, COALESCE(u.district_cooling_cost, 0) as cooling_district_cost,

		COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost, COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption, 0) as water, COALESCE(u.water_total_consumption_cost, 0) as water_cost,

		COALESCE(u.cdd, 0) as cdd,

		u.site_id,

		COALESCE(u.total_room_night, 0) as total_room_night,
		COALESCE(u.total_guests, 0) as total_guests,
		COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,
		COALESCE(u.total_guests_budget, 0) as total_guests_budget,

		COALESCE(s.rooms_keys, 0) as rooms_keys,

			    s.site_type,

			    COALESCE(s.site_builtup_area, 0) as site_builtup_area

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



	$query = "SELECT SUM(COALESCE(u.total_electricity_kwh, 0)) as electricity, SUM(COALESCE(u.total_electricity_cost, 0)) as electricity_cost,

		SUM(COALESCE(u.total_fuel_oil, 0)) as fuel, SUM(COALESCE(u.total_fuel_oil_cost, 0)) as fuel_cost,

		SUM(COALESCE(u.total_lpg, 0)) as lpg, SUM(COALESCE(u.total_lpg_cost, 0)) as lpg_cost,

		SUM(COALESCE(u.total_natural_gas, 0)) as natural_gas, SUM(COALESCE(u.total_natural_gas_cost, 0)) as natural_gas_cost,

		SUM(COALESCE(u.district_heating, 0)) as heating_district, SUM(COALESCE(u.district_heating_cost, 0)) as heating_district_cost,

		SUM(COALESCE(u.district_cooling, 0)) as cooling_district, SUM(COALESCE(u.district_cooling_cost, 0)) as cooling_district_cost,

		SUM(COALESCE(u.district_heating_fixed_cost, 0)) as district_heating_fixed_cost,

		SUM(COALESCE(u.district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost,

		SUM(COALESCE(u.water_total_consumption, 0)) as water, SUM(COALESCE(u.water_total_consumption_cost, 0)) as water_cost,

		SUM(COALESCE(u.cdd, 0)) as cdd,

		SUM(COALESCE(u.total_room_night, 0)/(COALESCE(s.rooms_keys, 0)*day(last_day(MAKEDATE(u.year_id,(u.month_id*28)))))) as occupancy,

		u.site_id,

		SUM(COALESCE(u.total_guests, 0)) as total_guests,
		SUM(COALESCE(u.total_guests_budget, 0)) as total_guests_budget,
		SUM(COALESCE(u.total_room_night, 0)) as total_room_night,
		SUM(COALESCE(u.total_room_night_budget, 0)) as total_room_night_budget,

			    COALESCE(s.rooms_keys, 0) as rooms_keys,

			    s.site_type,

		COALESCE(s.site_builtup_area, 0) as site_builtup_area

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

		month_id,year_id,total_room_night,total_room_night_budget,s.rooms_keys

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



	$query = "SELECT u.site_id,u.month_id,u.year_id,COALESCE(u.hdd, 0) as hdd,COALESCE(u.cdd, 0) as cdd,COALESCE(u.total_room_night, 0) as total_room_night,COALESCE(u.total_guests, 0) as total_guests,COALESCE(total_room_night_budget, 0) as total_room_night_budget,COALESCE(u.total_guests_budget, 0) as total_guests_budget,

		COALESCE(u.district_cooling, 0) as district_cooling_actual,

		COALESCE(u.district_cooling_total_budget, 0) as district_cooling_budget,

		COALESCE(u.district_cooling_cost, 0) as district_cooling_cost_actual,

		COALESCE(u.district_cooling_total_budget_cost, 0) as district_cooling_cost_budget,

		COALESCE(u.district_heating, 0) as district_heating_actual,

		COALESCE(u.district_heating_total_budget, 0) as district_heating_budget,

		COALESCE(u.district_heating_cost, 0) as district_heating_cost_actual,

		COALESCE(u.district_heating_total_budget_cost, 0) as district_heating_cost_budget,

		COALESCE(u.total_electricity_kwh, 0) as total_electricity_kwh_actual,

		COALESCE(u.electricity_total_budget, 0) as total_electricity_kwh_budget,

		COALESCE(u.total_electricity_cost, 0) as total_electricity_cost_actual,

		COALESCE(u.electricity_total_budget_cost, 0) as total_electricity_cost_budget,

		COALESCE(u.total_fuel_oil, 0) as total_fuel_oil_actual,

		COALESCE(u.fuel_total_budget, 0) as total_fuel_oil_budget,

		COALESCE(u.total_fuel_oil_cost, 0) as total_fuel_oil_cost_actual,

		COALESCE(u.fuel_total_budget_cost, 0) as total_fuel_oil_cost_budget,

		COALESCE(u.total_lpg, 0) as total_lpg_actual,

		COALESCE(u.lpg_total_budget, 0) as total_lpg_budget,

		COALESCE(u.total_lpg_cost, 0) as total_lpg_cost_actual,

		COALESCE(u.lpg_total_budget_cost, 0) as total_lpg_cost_budget,

		COALESCE(u.total_natural_gas, 0) as total_natural_gas_actual,

		COALESCE(u.natural_gas_total_budget, 0) as total_natural_gas_budget,

		COALESCE(u.total_natural_gas_cost, 0) as total_natural_gas_cost_actual,

		COALESCE(u.natural_gas_total_budget_cost, 0) as total_natural_gas_cost_budget,

		COALESCE(u.water_total_consumption, 0) as water_total_consumption_actual,

		COALESCE(u.water_total_consumption_budget, 0) as water_total_consumption_budget,

		COALESCE(u.water_total_consumption_cost, 0) as water_total_consumption_cost_actual,

		COALESCE(u.water_total_consumption_budget_cost, 0) as water_total_consumption_cost_budget

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



	$query = "SELECT SUM(COALESCE(total_electricity_kwh, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

			SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

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
	if (empty($result) || !is_array($result)) {
	    return $result;
	}
	$this->load->model('sites/sites_model');
	foreach ($result as $key => $value) {
	    if (!is_array($value)) {
		continue;
	    }
	    $row_site_id = !empty($value['site_id']) ? $value['site_id'] : $this->site_id;
	    if (empty($row_site_id)) {
		continue;
	    }
	    if (!array_key_exists('rooms_keys', $value)) {
		continue;
	    }
	    $dataFetch = array(
		'site_id' => $row_site_id,
		'area_update_field' => 'rooms_keys',
	    );
	    $latestAreaEntry = $this->sites_model->getlatestSiteArea($dataFetch);
	    if (!empty($latestAreaEntry)) {
		$latestAreaEntry = (array) $latestAreaEntry;
		if (isset($latestAreaEntry['area_update_value'])) {
		    $result[$key]['rooms_keys'] = $latestAreaEntry['area_update_value'];
		}
	    }
	}
	return $result;
    }

	public function getSitewiseTotalEui()
	{
		return $this->db->query("
			SELECT 
				s.id,
				s.site_location_name,
				SUM(r.electricity + r.gases + r.others) AS total_energy,
				SUM(r.room_night) AS total_roomnight
			FROM utility_reports r
			JOIN sites s ON s.id = r.site_id
			WHERE r.room_night > 0
			GROUP BY s.id
		")->result_array();
	}

}

