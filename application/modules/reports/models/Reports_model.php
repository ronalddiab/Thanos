<?php




if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Reports_model extends Base_Model
{



    protected $_tbl_utilities = TBL_UTILITIES_COST;

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_countries = TBL_COUNTRIES;

    protected $_tbl_electricity_tariff = TBL_ELECTRICITY_TARIFF;

    protected $_tbl_projects_categories = 'project_categories';

    protected $_tbl_projects = 'project_info';

    protected $_tbl_hotels = TBL_HOTELS;

    protected $_tbl_regions = TBL_REGIONS;

    protected $_tbl_projects_todos = 'project_to_do_info';

    protected $_tbl_action_plan = 'action_plan';

    protected $_tbl_utilities_cost_daily = 'utilities_cost_daily';

    public $utilities_month = "";

    public $utilities_year = "";

    public $site_id = "";

    public $role_id = "";

    public $user_id = "";



    function __construct()
    {

	parent::__construct();
    }



    function monthlyUtilityBasedReportByCost($filters = array())
    {

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

			    COALESCE(u.total_room_night, 0) as total_room_night,

			    COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,
				
			    COALESCE(u.total_guests_budget, 0) as total_guests_budget,

			    COALESCE(u.total_laundered, 0) as total_laundered,

			    COALESCE(s.site_builtup_area, 0) as site_builtup_area, COALESCE(s.cooled_builtup_area, 0) as cooled_builtup_area, COALESCE(s.rooms_keys, 0) as rooms_keys

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    ORDER BY year_id ASC,month_id ASC,u.id ASC";

	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function monthlyUtilityBasedReportByUnit($filters = array())
    {

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


	$query = "SELECT COALESCE(u.total_electricity_kwh, 0) as electricity, COALESCE(u.electricity_total_budget, 0) as electricity_budget, COALESCE(u.total_fuel_oil, 0) as fuel_oil, COALESCE(u.fuel_total_budget, 0) as fuel_budget,
			    COALESCE(u.total_lpg, 0) as lpg, COALESCE(u.lpg_total_budget, 0) as lpg_budget,

			    COALESCE(u.total_natural_gas, 0) as natural_gas, COALESCE(u.natural_gas_total_budget, 0) as natural_gas_budget,

			    COALESCE(u.district_heating, 0) as district_heating, COALESCE(u.district_heating_total_budget, 0) as district_heating_budget,

			    COALESCE(u.district_cooling, 0) as district_cooling, COALESCE(u.district_cooling_total_budget, 0) as district_cooling_budget,

			    COALESCE(u.water_total_consumption, 0) as water, COALESCE(u.water_total_consumption_budget, 0) as water_budget,

			    COALESCE(u.water_utility_supply, 0) as water_utility, COALESCE(u.water_irrigation, 0) as water_irrigation, COALESCE(u.waste_water, 0) as waste_water, COALESCE(u.water_Cisterns, 0) as water_cisterns,

			    COALESCE(u.cdd, 0) as cdd, COALESCE(u.hdd, 0) as hdd,

			    u.month_id,

			    u.year_id,

			    COALESCE(u.total_guests, 0) as total_guests,

			    COALESCE(u.total_room_night, 0) as total_room_night,

			    COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,
				
			    COALESCE(u.total_guests_budget, 0) as total_guests_budget,

			    COALESCE(u.total_laundered, 0) as total_laundered,

			    COALESCE(s.site_builtup_area, 0) as site_builtup_area, COALESCE(s.cooled_builtup_area, 0) as cooled_builtup_area, COALESCE(s.rooms_keys, 0) as rooms_keys

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function costBudget($filters = array())
    {

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

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();
    }



    function unitBudget($filters = array())
    {

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

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();
    }



    function kwhUnitBasedReportForCurrentYear($filters)
    {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];

	$query = "SELECT SUM(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district, COALESCE(district_cooling_fixed_cost, 0) as district_cooling_fixed_cost, COALESCE(district_heating_fixed_cost, 0) as district_heating_fixed_cost, COALESCE(lpg_fixed_cost, 0) as lpg_fixed_cost, COALESCE(natural_gas_fixed_cost, 0) as natural_gas_fixed_cost, COALESCE(water_fixed_cost, 0) as water_fixed_cost,

			SUM(COALESCE(total_fuel_oil, 0)) as fuel, SUM(COALESCE(total_lpg, 0)) as lpg, SUM(COALESCE(total_natural_gas, 0)) as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id<='$current_month')

		    AND site_id={$this->site_id}";

	$result = $this->db->query($query);

	if ($result) {

	    return $result->row_array();
	}
    }



    function costBasedReportForCurrentYear($filters)
    {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];



	$query = "SELECT SUM(COALESCE(total_electricity_cost, 0)) as electricity, SUM(COALESCE(total_fuel_oil_cost, 0)) as fuel, SUM(COALESCE(total_lpg_cost, 0)) as lpg, SUM(COALESCE(total_natural_gas_cost, 0)) as natural_gas, SUM(COALESCE(district_heating_cost, 0)) as heating_district, SUM(COALESCE(district_heating_fixed_cost, 0)) as district_heating_fixed_cost, SUM(COALESCE(district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost, SUM(COALESCE(district_cooling_cost, 0)) as cooling_district, SUM(COALESCE(water_total_consumption_cost, 0)) as water, SUM(COALESCE(lpg_fixed_cost, 0)) as lpg_fixed_cost, SUM(COALESCE(natural_gas_fixed_cost, 0)) as natural_gas_fixed_cost, SUM(COALESCE(water_fixed_cost, 0)) as water_fixed_cost

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id<='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function kwhUnitBasedReportForPreviousMonth($filters = array())
    {

	$month = $filters['previous_month'];

	$year = isset($filters['current_year']) ? $filters['current_year'] : $filters['previous_year'];



	$query = "SELECT (COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, COALESCE(district_heating, 0) as heating_district, COALESCE(district_cooling, 0) as cooling_district,

			COALESCE(total_fuel_oil, 0) as fuel, COALESCE(total_lpg, 0) as lpg, COALESCE(total_natural_gas, 0) as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function costBasedReportForPreviousMonth($filters = array())
    {

	$month = $filters['previous_month'];

	$year = isset($filters['current_year']) ? $filters['current_year'] : $filters['previous_year'];



	$query = "SELECT COALESCE(total_electricity_cost, 0) as electricity, COALESCE(total_fuel_oil_cost, 0) as fuel, COALESCE(total_lpg_cost, 0) as lpg, COALESCE(total_natural_gas_cost, 0) as natural_gas, COALESCE(district_heating_cost, 0) as heating_district, COALESCE(district_cooling_cost, 0) as cooling_district, COALESCE(district_heating_fixed_cost, 0) as district_heating_fixed_cost, COALESCE(district_cooling_fixed_cost, 0) as district_cooling_fixed_cost, COALESCE(lpg_fixed_cost, 0) as lpg_fixed_cost, COALESCE(natural_gas_fixed_cost, 0) as natural_gas_fixed_cost, COALESCE(water_fixed_cost, 0) as water_fixed_cost, COALESCE(water_total_consumption_cost, 0) as water

		    FROM {$this->_tbl_utilities}

		    WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function utilityCostBarChart($filters = array())
    {

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



	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

			    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

			    COALESCE(u.total_fuel_oil, 0) as fuel_consumption,

			    COALESCE(u.total_lpg_cost, 0) as lpg,

			    COALESCE(u.total_lpg, 0) as lpg_consumption,

			    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

			    COALESCE(u.total_natural_gas, 0) as natural_gas_consumption,

			    COALESCE(u.district_heating_cost, 0) as heating_district,

			    COALESCE(u.district_heating, 0) as heating_district_consumption,

			    COALESCE(u.district_cooling_cost, 0) as cooling_district,

			    COALESCE(u.district_cooling, 0) as cooling_district_consumption,

			    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

			    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption_cost, 0) as water,

			    COALESCE(u.water_total_consumption, 0) as water_consumption,

			    COALESCE(u.onsite_generators_quantity, 0) as onsite_generator,
			    COALESCE(u.onsite_generators_fuel_oil_quantity, 0) as onsite_generator_fuel_oil,
			    COALESCE(u.onsite_generators_natural_gas_quantity, 0) as onsite_generator_natural_gas,
			    COALESCE(u.total_renewable_energy_production, 0) as renewable_energy,
			    u.total_renewable_energy_exported as renewable_energy_exported,

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
				) AS total_budget,

			    u.total_purchased_electricity,

			    COALESCE(u.total_purchased_electricity, 0) as total_purchased_electricity,

			    COALESCE(u.total_purchased_electricity_cost, 0) as total_purchased_electricity_cost,
			    COALESCE(u.fleet_petrol, 0) as fleet_petrol,
			    COALESCE(u.total_fleet_petrol_cost, 0) as total_fleet_petrol_cost,

			    (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh_carbon,
			    COALESCE(u.total_electricity_kwh, 0) as total_electricity_kwh,

			month_id,year_id,
			    COALESCE(total_room_night, 0) as total_room_night,
			    COALESCE(s.rooms_keys, 0) as rooms_keys,
			    COALESCE(total_guests, 0) as total_guests,
			    COALESCE(total_room_night_budget, 0) as total_room_night_budget,
			    COALESCE(total_guests_budget, 0) as total_guests_budget

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0 ";



	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function utilityUnitBarChart($filters = array())
    {

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



	$query = "SELECT (COALESCE(u.total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity,

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

			    (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh,

			month_id,year_id,
			    COALESCE(total_room_night, 0) as total_room_night,
			    COALESCE(s.rooms_keys, 0) as rooms_keys,
			    COALESCE(total_guests, 0) as total_guests, 
			    COALESCE(total_room_night_budget, 0) as total_room_night_budget,
			    COALESCE(total_guests_budget, 0) as total_guests_budget

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0 ";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function utilityCostBarChartByYears($filters = array())
    {

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



	$query = "SELECT SUM(COALESCE(u.total_electricity_cost, 0)) as electricity, SUM(COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as electricity_unit,

			SUM(COALESCE(u.total_fuel_oil_cost, 0)) as fuel, SUM(COALESCE(u.total_fuel_oil, 0)) as fuel_unit,

			SUM(COALESCE(u.total_lpg_cost, 0)) as lpg, SUM(COALESCE(u.total_lpg, 0)) as lpg_unit,

			SUM(COALESCE(u.total_natural_gas_cost, 0)) as natural_gas, SUM(COALESCE(u.total_natural_gas, 0)) as natural_gas_unit,

			SUM(COALESCE(u.district_heating_cost, 0)) as heating_district, SUM(COALESCE(u.district_heating, 0)) as heating_district_unit,

			SUM(COALESCE(u.district_cooling_cost, 0)) as cooling_district, SUM(COALESCE(u.district_cooling, 0)) as cooling_district_unit,

			SUM(COALESCE(u.district_heating_fixed_cost, 0)) as district_heating_fixed_cost,

			SUM(COALESCE(u.district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost,

			SUM(COALESCE(u.lpg_fixed_cost, 0)) as lpg_fixed_cost,

			SUM(COALESCE(u.natural_gas_fixed_cost, 0)) as natural_gas_fixed_cost,

			SUM(COALESCE(u.water_fixed_cost, 0)) as water_fixed_cost,

			SUM(COALESCE(u.water_total_consumption_cost, 0)) as water, SUM(COALESCE(u.water_total_consumption, 0)) as water_unit,

			SUM(COALESCE(u.cdd, 0)) as cdd,

			SUM(COALESCE(u.hdd, 0)) as hdd,

			SUM(COALESCE(u.total_room_night, 0)) as total_room_night,
			SUM(COALESCE(u.total_guests, 0)) as total_guests,
			SUM(COALESCE(u.total_room_night_budget, 0)) as total_room_night_budget,
			SUM(COALESCE(u.total_guests_budget, 0)) as total_guests_budget,

			((SUM(COALESCE(u.total_room_night, 0))/(COALESCE(s.rooms_keys, 0)*IF(u.year_id % 4 = 0, 366, 365))) * 100) as occupancy,

			SUM(COALESCE(u.total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as total_electricity_kwh,

			u.year_id as year_id,

			COALESCE(s.rooms_keys, 0) as rooms_keys

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE u.site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    GROUP BY u.year_id";

	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesUtilityBasedReportByMonth($filters = array())
    {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as electricity, COALESCE(u.total_electricity_cost, 0) as electricity_cost,

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

		    WHERE month_id =" . $this->db->escape($month) . " AND year_id=" . $this->db->escape($year) . " AND s.id IN($site_ids)";



	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function allsitesUtilityBasedReportByAvg($filters = array())
    {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT SUM(COALESCE(u.total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(u.total_electricity_cost, 0)) as electricity_cost,

			    SUM(COALESCE(u.total_fuel_oil, 0)) as fuel, SUM(COALESCE(u.total_fuel_oil_cost, 0)) as fuel_cost,

			    SUM(COALESCE(u.total_lpg, 0)) as lpg, SUM(COALESCE(u.total_lpg_cost, 0)) as lpg_cost,

			    SUM(COALESCE(u.total_natural_gas, 0)) as natural_gas, SUM(COALESCE(u.total_natural_gas_cost, 0)) as natural_gas_cost,

			    SUM(COALESCE(u.district_heating, 0)) as heating_district, SUM(COALESCE(u.district_heating_cost, 0)) as heating_district_cost,

			    SUM(COALESCE(u.district_cooling, 0)) as cooling_district, SUM(COALESCE(u.district_cooling_cost, 0)) as cooling_district_cost,

			    SUM(COALESCE(u.district_heating_fixed_cost, 0)) as district_heating_fixed_cost,

			    SUM(COALESCE(u.district_cooling_fixed_cost, 0)) as district_cooling_fixed_cost,

			    SUM(COALESCE(u.lpg_fixed_cost, 0)) as lpg_fixed_cost,

			    SUM(COALESCE(u.natural_gas_fixed_cost, 0)) as natural_gas_fixed_cost,

			    SUM(COALESCE(u.water_fixed_cost, 0)) as water_fixed_cost,

			    SUM(COALESCE(u.water_total_consumption, 0)) as water, SUM(COALESCE(u.water_total_consumption_cost, 0)) as water_cost,

			    SUM(COALESCE(u.cdd, 0)) as cdd,

			    SUM(COALESCE(u.total_room_night, 0) * 1.0 / NULLIF(COALESCE(s.rooms_keys, 0) * DAY(LAST_DAY(MAKEDATE(u.year_id, (u.month_id * 28)))), 0)) as occupancy,

			    u.site_id,

			    SUM(COALESCE(u.total_room_night, 0)) as total_room_night,
				SUM(COALESCE(u.total_guests, 0)) as total_guests,
				SUM(COALESCE(u.total_room_night_budget, 0)) as total_room_night_budget,
				SUM(COALESCE(u.total_guests_budget, 0)) as total_guests_budget,

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



    function allsitesTariffBasedReportByMonth($filters = array())
    {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT AVG(t.tariff) as tariff,

			    t.site_id,

			    s.rooms_keys,

			    s.site_type,

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



    function allsitesTariffBasedReportByAvg($filters = array())
    {

	$month = $filters['month'];

	$year = $filters['year'];

	$site_ids = $filters['site_ids'];



	$query = "SELECT SUM(avgtariff) as tariff,site_id,rooms_keys,site_builtup_area

		    FROM (SELECT AVG(t.tariff) as avgtariff,t.site_id,s.rooms_keys,s.site_builtup_area,s.site_type

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

    function getSites($site_filters = array())
    {

	$this->db->select("s.id,s.site_location_name,c.country");

	$this->db->from($this->_tbl_sites . ' AS s');

	$this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');

	$this->db->where('s.status', 1);

	if (!empty($site_filters['region_id'])) {

	    $this->db->where('s.region_id', $site_filters['region_id']);
	}
	// Dropdown after merge: 1=Resort, 2=City Hotel, 3=Standalone Residence, 4=Corporate Office, 5=Select Sites
	$site_type = isset($site_filters['site_type']) ? (int) $site_filters['site_type'] : 0;
	if (in_array($site_type, array(1, 2, 3, 4), true)) {
	    $this->db->where('s.site_type', $site_type);
	} else if ($site_type == 5) {
	    $site_ids = isset($site_filters['site_ids']) ? $site_filters['site_ids'] : array();
	    if (!is_array($site_ids)) {
		$site_ids = explode(',', $site_ids);
	    }
	    $site_ids = array_filter($site_ids);
	    if (!empty($site_ids)) {
		$this->db->where_in('s.id', $site_ids);
	    } else {
		$this->db->where('s.id', 0);
	    }
	}



	// $this->db->order_by('s.id', 'asc');

	$this->db->order_by('s.site_location_name', 'asc');

	$result = $this->db->get();


	return $result->result_array();
    }



    function getMaxCurrentMonth()
    {

	$this->db->select("MAX(month_id) as month_id");

	$this->db->from($this->_tbl_utilities);

	$this->db->where('year_id', date('Y'));



	$result = $this->db->get();

	return $result->row()->month_id;
    }



    function getEMACategories()
    {

	$this->db->select('*');

	$this->db->from($this->_tbl_projects_categories . ' as pc');

	$this->db->where('pc.status !=', -1);

	$result = $this->db->get();

	return $this->db->custom_result($result);
    }



    function getEMAPublicProjects($category_id = 0)
    {

	$this->db->select('p.*,pc.name,h.hotel_name');

	$this->db->from($this->_tbl_projects . ' as p');

	$this->db->join($this->_tbl_projects_categories . ' as pc', 'pc.id = p.project_category_id');

	$this->db->join($this->_tbl_hotels . ' as h', 'h.id = p.hotel_id');



	$this->db->where('p.project_category_id', $category_id);

	$this->db->where('p.status !=', -1);

	$result = $this->db->get();

	return $this->db->custom_result($result);
    }



    function get_ema_actionplans_todos_bysite($actiondata)
    {

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



    function getEMACategoriesList()
    {

	$this->db->select('*');

	$this->db->from($this->_tbl_projects_categories . ' as pc');

	$this->db->where('pc.status', 1);

	$result = $this->db->get();

	return $this->db->custom_result($result);
    }



    function getDailyReportData($month, $year, $to_date = 31)
    {



	$query = "SELECT * FROM {$this->_tbl_utilities_cost_daily}

		    WHERE month_id = '" . $month . "'

		    AND year_id = '" . $year . "'

		    AND date_id <= '" . $to_date . "'

		    AND site_id = '" . $this->site_id . "'

		    order by date_id";



	$result = $this->db->query($query);

	return $result->result_array();
    }

    public function get_daily_reading_static_data($site_id = 0, $month, $year, $to_date = 31)
    {

	$query = "SELECT * FROM daily_reading_utilities_data

		    WHERE month_id = '" . $month . "'

		    AND year_id = '" . $year . "'

		    AND date_id <= '" . $to_date . "'

		    AND site_id = '" . $site_id . "'

		    order by date_id";



	$result = $this->db->query($query);

	return $result->result_array();
    }



    public function get_daily_reading_utilities_titles()
    {

	$query = "SELECT drut.id, drut.utility_id, dru.title as 'utility', drut.title as 'utility_title'

		    FROM  `daily_reading_utilities` AS  `dru`

		    INNER JOIN  `daily_reading_utilities_titles` AS  `drut` ON dru.id = drut.utility_id

		    Where drut.site_id = " . $this->site_id . "

		    order by drut.id

		";

	$result = $this->db->query($query);

	return $result->result_array();
    }



    public function get_daily_reading_utilities_title_data($data = array())
    {

	$title_id = implode(",", $data['title_id']);

	$month_id = $data['month'];

	$year_id = $data['year_id'];

	$utility_data = [];

	$query = "SELECT * FROM daily_reading_utilities_title_data

		    WHERE month_id = '" . $month_id . "'

		    AND year_id = '" . $year_id . "'

		    AND site_id = '" . $this->site_id . "'

		    AND utility_title_id IN (" . $title_id . ")

		    order by date_id";



	$result = $this->db->query($query);

	$result_array = $result->result_array();



	foreach ($result_array as $utility) {

	    $utility_data[$utility['date_id']][$utility['utility_title_id']] = $utility;
	}

	return $utility_data;
    }



    public function get_daily_reading_utilities_data($data = array())
    {

	$month_id = $data['month'];

	$year_id = $data['year_id'];



	$query = "SELECT * FROM daily_reading_utilities_data

		    WHERE month_id = '" . $month_id . "'

		    AND year_id = '" . $year_id . "'

		    AND site_id = '" . $this->site_id . "'

		    order by date_id";



	$result = $this->db->query($query);

	$resultArray = $result->result_array();

	$utilities_data = array();



	foreach ($resultArray as $utility) {

	    $utilities_data[$utility['date_id']] = [

		'cdd' => $utility['cdd'],

		'hdd' => $utility['hdd'],

		'total_room_night' => $utility['total_room_night']

	    ];
	}

	return $utilities_data;
    }



    public function get_daily_reading_data($site_id = 0, $month, $year, $to_date = 31)
    {

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

			    $value1['title'] = $value1['title']   . " - " . GetSiteUtilityUnitName($value1['site_id'], $value['title']);

			    $value1['reading'] = array();

			    $total = 0;



			    if (!empty($utility_titles_data)) {

				foreach ($utility_titles_data as $key2 => $value2) {

				    if ($value1['id'] == $value2['utility_title_id']) {

					$value1['reading'][] = $value2;

					$total += floatval($value2['value']);
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



    function getFIlterUtilityMonthly($filters = array())
    {

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



    function getUtilityActualBudgetData($filters = array())
    {



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



	$query = "SELECT u.site_id,u.month_id,u.year_id,COALESCE(u.hdd, 0) as hdd,COALESCE(u.cdd, 0) as cdd,COALESCE(u.total_room_night, 0) as total_room_night,COALESCE(u.total_guests, 0) as total_guests,COALESCE(u.total_room_night_budget, 0) as total_room_night_budget,COALESCE(u.total_guests_budget, 0) as total_guests_budget,

			COALESCE(u.district_cooling, 0) as district_cooling_actual,

			COALESCE(u.district_cooling_total_budget, 0) as district_cooling_budget,

			COALESCE(u.district_cooling_cost, 0) as district_cooling_cost_actual,

			COALESCE(u.district_cooling_total_budget_cost, 0) as district_cooling_cost_budget,

			COALESCE(u.district_heating, 0) as district_heating_actual,

			COALESCE(u.district_heating_total_budget, 0) as district_heating_budget,

			COALESCE(u.district_heating_cost, 0) as district_heating_cost_actual,

			COALESCE(u.district_heating_total_budget_cost, 0) as district_heating_cost_budget,

			(COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh_actual,

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

		    WHERE u.site_id={$filters['site_id']} AND {$where_more} AND year_id != 0 AND month_id != 0";



	$result = $this->db->query($query);

	return $result->result_array();
    }



    // to get hourly titles

    public function get_daily_metering_reading_utilities_titles()
    {

	$query = "SELECT drut.id, drut.utility_id, dru.title as 'utility', drut.hourly_title as 'utility_title'

		    FROM  `daily_reading_utilities` AS  `dru`

		    INNER JOIN  `daily_reading_utilities_titles` AS  `drut` ON dru.id = drut.utility_id

		    Where drut.site_id = " . $this->site_id . "

		    AND drut.hourly_title != ''

		    order by drut.id

		";

	$result = $this->db->query($query);

	return $result->result_array();
    }



    // To get hourly readings

    public function get_daily_metering_reading_data($site_id = 0, $month, $year, $to_date = 31, $date, $daily_metering)
    {

	$utilities = array();



	$this->db->select('*');

	$this->db->from('daily_reading_utilities');

	$result = $this->db->get();

	$utilities = $result->result_array();



	$this->db->select('*');

	$this->db->from('daily_reading_utilities_titles');

	$this->db->where('site_id', $site_id);

	$this->db->where('hourly_title !=', '');

	$result = $this->db->get();

	$utility_titles = $result->result_array();



	$query = "SELECT * FROM hourly_reading_utilities_title_data

		    WHERE month_id = '" . $month . "'

		    AND year_id = '" . $year . "'

		    AND date_id = '" . $date . "'

		    AND site_id = '" . $site_id . "'

		    AND is_half_hourly = '" . $daily_metering . "'

		    order by date_id";



	$result = $this->db->query($query);

	$utility_titles_data = $result->result_array();



	if (!empty($utilities)) {

	    foreach ($utilities as $key => $value) {

		$utilities[$key]['submission_titles'] = array();

		if (!empty($utility_titles)) {

		    foreach ($utility_titles as $key1 => $value1) {



			if ($value['id'] == $value1['utility_id']) {

			    $value1['title'] = $value1['title']   . " - " . GetSiteUtilityUnitName($value1['site_id'], $value['title']);

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



    // To get daily reading utilities title data

    public function get_hourly_reading_utilities_title_data($data = array())
    {

	$title_id = implode(",", $data['title_id']);

	$month_id = $data['month'];

	$year_id = $data['year_id'];

	$date_id = $data['date_id'];

	$is_half_hourly = $data['is_half_hourly'];



	$utility_data = [];

	$query = "SELECT * FROM hourly_reading_utilities_title_data

		    WHERE month_id = '" . $month_id . "'

		    AND year_id = '" . $year_id . "'

		    AND date_id = '" . $date_id . "'

		    AND is_half_hourly = '" . $is_half_hourly . "'

		    AND site_id = '" . $this->site_id . "'

		    AND utility_title_id IN (" . $title_id . ")

		    order by hour";



	$result = $this->db->query($query);

	$result_array = $result->result_array();



	foreach ($result_array as $utility) {

	    $hr = date("H:i", strtotime($utility['hour']));

	    $utility_data[$hr][$utility['utility_title_id']] = $utility;
	}


	return $utility_data;
    }



    // To get hourly reading utilities data

    public function get_hourly_reading_utilities_data($data = array())
    {

	$month_id = $data['month'];

	$year_id = $data['year_id'];

	$date_id = $data['date_id'];

	$is_half_hourly = $data['is_half_hourly'];



	$query = "SELECT * FROM hourly_reading_utilities_data

		    WHERE month_id = '" . $month_id . "'

		    AND year_id = '" . $year_id . "'

		    AND date_id = '" . $date_id . "'

		    AND is_half_hourly = '" . $is_half_hourly . "'

		    AND site_id = '" . $this->site_id . "'

		    order by hour";



	$result = $this->db->query($query);

	$resultArray = $result->result_array();

	$utilities_data = array();



	foreach ($resultArray as $utility) {

	    $utilities_data[$utility['date_id']] = [

		'cdd' => $utility['cdd'],

		'hdd' => $utility['hdd'],

		'total_room_night' => $utility['total_room_night']

	    ];
	}

	return $utilities_data;
    }



    function utilityCostBarChartExcelAnnualReport($filters = array())
    {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];

	$site_id = $filters['site_id'];



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


	if (isset($site_id) && !empty($site_id) && $site_id != 0) {
	    $query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

				COALESCE(u.total_fuel_oil_cost, 0) as fuel,

				COALESCE(u.total_fuel_oil, 0) as fuel_consumption,

				COALESCE(u.total_lpg_cost, 0) as lpg,

				COALESCE(u.total_lpg, 0) as lpg_consumption,

				COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

				COALESCE(u.total_natural_gas, 0) as natural_gas_consumption,

				COALESCE(u.district_heating_cost, 0) as heating_district,

				COALESCE(u.district_heating, 0) as heating_district_consumption,

				COALESCE(u.district_cooling_cost, 0) as cooling_district,

				COALESCE(u.district_cooling, 0) as cooling_district_consumption,

				COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

				COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

				COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

				COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

				COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

				COALESCE(u.water_total_consumption_cost, 0) as water,

				COALESCE(u.water_total_consumption, 0) as water_consumption,

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

				(COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh,

			    month_id,year_id,
			    COALESCE(total_room_night, 0) as total_room_night,
			    COALESCE(s.rooms_keys, 0) as rooms_keys,
			    s.site_type,
			    COALESCE(total_guests, 0) as total_guests,
			    COALESCE(total_room_night_budget, 0) as total_room_night_budget,
			    COALESCE(total_guests_budget, 0) as total_guests_budget

			FROM {$this->_tbl_utilities} as u

			LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

			WHERE site_id={$site_id} AND {$where_more} AND year_id != 0 AND month_id != 0 ";



	    $result = $this->db->query($query);

	    $result = $result->result_array();

	    return $this->fetchUpdatedRoomKeys($result);
	} else {
	    return [];
	}
    }



    function utilityCostBarChartExcelAnnualReportForTypeBased($filters = array())
    {

	$start_year = $filters['start_year'];

	$start_month = $filters['start_month'];

	$end_year = $filters['end_year'];

	$end_month = $filters['end_month'];

	$site_id = $filters['site_id'];

	$pre_start_year = $start_year - 1;

	$pre_end_year = $end_year - 1;



	if ($start_year == $end_year) {

	    if ($filters['report_type'] == 'month') {

		$where_more = "(

				(year_id='$start_year' AND (month_id={$start_month}))

			    )";
	    } else if ($filters['report_type'] == 'ytd') {

		$where_more = "((year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month})))";
	    } else {

		/*$where_more = "(

				(year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

			    )";*/



		$where_more = "(

				(year_id='$start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

				OR

				(year_id='$pre_start_year' AND (month_id>={$start_month} AND month_id<={$end_month}))

			    )";
	    }
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

	/*$query = "SELECT u.*,

			    cdd,

			    hdd,

			    (u.water_total_consumption_budget_cost+u.total_consumption_breakdown_budget_cost+u.fuel_total_budget_cost+u.lpg_total_budget_cost+u.natural_gas_total_budget_cost+u.district_heating_total_budget_cost+u.district_cooling_total_budget_cost+u.electricity_total_budget_cost) as total_budget,

			    u.total_purchased_electricity,

			    u.total_purchased_electricity_cost,

			    u.total_electricity_kwh,

			month_id,year_id,total_room_night,s.rooms_keys

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$site_id} AND {$where_more} ";*/

	if (isset($site_id) && !empty($site_id) && $site_id != 0) {

	    $query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

	    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

	    COALESCE(u.total_fuel_oil, 0) as fuel_consumption,

	    COALESCE(u.total_lpg_cost, 0) as lpg,

	    COALESCE(u.total_lpg, 0) as lpg_consumption,

	    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

	    COALESCE(u.total_natural_gas, 0) as natural_gas_consumption,

	    COALESCE(u.district_heating_cost, 0) as heating_district,

	    COALESCE(u.district_heating, 0) as heating_district_consumption,

	    COALESCE(u.district_cooling_cost, 0) as cooling_district,

	    COALESCE(u.district_cooling, 0) as cooling_district_consumption,

	    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

	    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

	    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

	    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

	    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

	    COALESCE(u.water_total_consumption_cost, 0) as water,

	    COALESCE(u.water_total_consumption, 0) as water_consumption,

	    COALESCE(u.onsite_generators_quantity, 0) as onsite_generator,
							COALESCE(u.onsite_generators_fuel_oil_quantity, 0) as onsite_generator_fuel_oil,
							COALESCE(u.onsite_generators_natural_gas_quantity, 0) as onsite_generator_natural_gas,
	    COALESCE(u.total_renewable_energy_production, 0) as renewable_energy,

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

	    (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh,

	    month_id,year_id,
	    COALESCE(total_room_night, 0) as total_room_night,
	    COALESCE(s.rooms_keys, 0) as rooms_keys,
	    s.site_type,
	    COALESCE(total_guests, 0) as total_guests,
	    COALESCE(total_room_night_budget, 0) as total_room_night_budget,
	    COALESCE(total_guests_budget, 0) as total_guests_budget

	    FROM {$this->_tbl_utilities} as u

	    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

	    WHERE site_id={$site_id} AND {$where_more} AND year_id != 0 AND month_id != 0 ";

	    $result = $this->db->query($query);

	    $result = $result->result_array();

	    return $this->fetchUpdatedRoomKeys($result);
	} else {
	    return [];
	}
    }

    function kwhUnitBasedReportForCurrentYearExcelAnnualReport($filters)
    {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];

	$site_id = $filters['site_id'];

	if (isset($site_id) && !empty($site_id) && $site_id != 0) {
	    $query = "SELECT SUM(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

			    SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

			FROM {$this->_tbl_utilities}

			WHERE (year_id='$current_year' AND month_id<='$current_month')

			AND site_id={$site_id}";

	    $result = $this->db->query($query);

	    return $result->row_array();
	} else {
	    return [];
	}
    }



    function kwhUnitBasedReportForCurrentYearPieCharts($filters)
    {

	$current_year = $filters['report_year_piechart']; //date('Y');

	$current_month = $filters['report_month_piechart'];



	$query = "SELECT SUM(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

			SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id ='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function costBasedReportForCurrentYearPiecharts($filters)
    {

	$current_year = $filters['report_year_piechart']; //date('Y');

	$current_month = $filters['report_month_piechart'];



	$query = "SELECT COALESCE((total_electricity_cost), 0) as electricity, COALESCE((total_fuel_oil_cost), 0) as fuel, COALESCE((total_lpg_cost), 0) as lpg, COALESCE((total_natural_gas_cost), 0) as natural_gas, COALESCE((district_heating_cost), 0) as heating_district, COALESCE((district_heating_fixed_cost), 0) as district_heating_fixed_cost, COALESCE((district_cooling_fixed_cost), 0) as district_cooling_fixed_cost, COALESCE((lpg_fixed_cost), 0) as lpg_fixed_cost, COALESCE((natural_gas_fixed_cost), 0) as natural_gas_fixed_cost, COALESCE((water_fixed_cost), 0) as water_fixed_cost, COALESCE((district_cooling_cost), 0) as cooling_district, COALESCE((water_total_consumption_cost), 0) as water

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);
	return $result->row_array();
    }



    /*

     * Function for get all available regions

     */



    function getAllRegions()
    {

	$query = "SELECT id,region_name FROM {$this->_tbl_regions}

		    WHERE (status = '1')";



	$result = $this->db->query($query);

	if ($result) {

	    return $result->result_array();
	}

	return false;
    }



    function kwhUnitBasedReportForCurrentYearPieChartsForMonthly($filters)
    {

	$current_year = $filters['report_year_piechart']; //date('Y');

	$current_month = $filters['report_month_piechart'];



	$query = "SELECT SUM(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

			SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id <='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function kwhUnitBasedReportForCurrentYearExcelAnnualReportForMonthly($filters)
    {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];

	$site_id = $filters['site_id'];

	$where = "(year_id='$current_year' AND month_id <= '$current_month')";

	if ($filters['report_type'] == 'month') {

	    $where = "(year_id='$current_year' AND month_id = '$current_month')";
	}

	if (isset($site_id) && !empty($site_id) && $site_id != 0) {
	    $query = "SELECT SUM(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) as electricity, SUM(COALESCE(district_heating, 0)) as heating_district, SUM(COALESCE(district_cooling, 0)) as cooling_district,

			    SUM(COALESCE(total_fuel_oil, 0)) as fuel,SUM(COALESCE(total_lpg, 0)) as lpg,SUM(COALESCE(total_natural_gas, 0)) as natural_gas

			FROM {$this->_tbl_utilities}

			WHERE {$where}

			AND site_id={$site_id}";



	    $result = $this->db->query($query);

	    return $result->row_array();
	} else {
	    return [];
	}
    }



    function utilityCostBarChartUpperManagement($filters = array())
    {


	$start_year = $filters['start_year'];

	$previous_month = ($filters['previous_month'] - 1);

	$end_year = $filters['end_year'];

	$end_month = $filters['previous_month'];

	$pmonth = $filters['pmonth'];

	$pyear = $filters['pyear'];



	$where_more = '';



	$where_more = "((year_id={$start_year} AND month_id={$end_month}))";

	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

			    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

			    COALESCE(u.total_fuel_oil, 0) as fuel_consumption,

			    COALESCE(u.total_lpg_cost, 0) as lpg,

			    COALESCE(u.total_lpg, 0) as lpg_consumption,

			    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

			    COALESCE(u.total_natural_gas, 0) as natural_gas_consumption,

			    COALESCE(u.district_heating_cost, 0) as heating_district,

			    COALESCE(u.district_heating, 0) as heating_district_consumption,

			    COALESCE(u.district_cooling_cost, 0) as cooling_district,

			    COALESCE(u.district_cooling, 0) as cooling_district_consumption,

			    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

			    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption_cost, 0) as water,

			    COALESCE(u.water_total_consumption, 0) as water_consumption,

			    COALESCE(u.onsite_generators_quantity, 0) as onsite_generator,
							COALESCE(u.onsite_generators_fuel_oil_quantity, 0) as onsite_generator_fuel_oil,
							COALESCE(u.onsite_generators_natural_gas_quantity, 0) as onsite_generator_natural_gas,
			    COALESCE(u.total_renewable_energy_production, 0) as renewable_energy,

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

			    (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh,

			month_id,year_id,
			COALESCE(total_room_night, 0) as total_room_night,
			COALESCE(s.rooms_keys, 0) as rooms_keys,
			s.site_type,
			COALESCE(total_guests, 0) as total_guests,
			COALESCE(total_room_night_budget, 0) as total_room_night_budget,
			COALESCE(total_guests_budget, 0) as total_guests_budget

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$filters['site_id']} AND {$where_more} AND year_id != 0 AND month_id != 0 ";


	$result = $this->db->query($query);

	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }



    function utilityCostBarPdfUpperManagement($filters = array())
    {


	$start_year = $filters['start_year'];

	$previous_month = ($filters['previous_month'] - 1);

	$end_year = $filters['end_year'];

	$end_month = $filters['previous_month'];

	$pmonth = $filters['pmonth'];

	$pyear = $filters['pyear'];

	$endmonth = $filters['end_month'];



	$where_more = '';



	$where_more = "((year_id={$start_year} AND month_id={$previous_month}) OR (year_id={$start_year} AND month_id={$end_month}) OR (year_id={$pyear} AND month_id={$pmonth}) OR (year_id={$pyear} AND month_id={$endmonth}))";

	$query = "SELECT COALESCE(u.total_electricity_cost, 0) as electricity,

			    COALESCE(u.total_fuel_oil_cost, 0) as fuel,

			    COALESCE(u.total_fuel_oil, 0) as fuel_consumption,

			    COALESCE(u.total_lpg_cost, 0) as lpg,

			    COALESCE(u.total_lpg, 0) as lpg_consumption,

			    COALESCE(u.total_natural_gas_cost, 0) as natural_gas,

			    COALESCE(u.total_natural_gas, 0) as natural_gas_consumption,

			    COALESCE(u.district_heating_cost, 0) as heating_district,

			    COALESCE(u.district_heating, 0) as heating_district_consumption,

			    COALESCE(u.district_cooling_cost, 0) as cooling_district,

			    COALESCE(u.district_cooling, 0) as cooling_district_consumption,

			    COALESCE(u.district_heating_fixed_cost, 0) as district_heating_fixed_cost,

			    COALESCE(u.district_cooling_fixed_cost, 0) as district_cooling_fixed_cost,

			    COALESCE(u.lpg_fixed_cost, 0) as lpg_fixed_cost,

			    COALESCE(u.natural_gas_fixed_cost, 0) as natural_gas_fixed_cost,

			    COALESCE(u.water_fixed_cost, 0) as water_fixed_cost,

			    COALESCE(u.water_total_consumption_cost, 0) as water,

			    COALESCE(u.water_total_consumption, 0) as water_consumption,

			    COALESCE(u.onsite_generators_quantity, 0) as onsite_generator,
							COALESCE(u.onsite_generators_fuel_oil_quantity, 0) as onsite_generator_fuel_oil,
							COALESCE(u.onsite_generators_natural_gas_quantity, 0) as onsite_generator_natural_gas,
			    COALESCE(u.total_renewable_energy_production, 0) as renewable_energy,

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

			    (COALESCE(u.total_electricity_kwh, 0) - COALESCE(u.onsite_generators_quantity, 0) - COALESCE(u.total_renewable_energy_production, 0)) as total_electricity_kwh,

			month_id,year_id,
			COALESCE(total_room_night, 0) as total_room_night,
			COALESCE(s.rooms_keys, 0) as rooms_keys,
			s.site_type,
			COALESCE(total_guests, 0) as total_guests,
			COALESCE(total_room_night_budget, 0) as total_room_night_budget,
			COALESCE(total_guests_budget, 0) as total_guests_budget

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$filters['site_id']} AND {$where_more} AND year_id != 0 AND month_id != 0 ";
	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }

    /**
     * Conversion factors for SQL interpolation. Empty/missing values become 1 so the query stays valid.
     */
    private function getMmbtuFactorsForQuery()
    {
	$keys = ['electricity', 'fuel_oil', 'lpg', 'natural_gas', 'district_heating', 'district_cooling', 'water'];
	$dataFactor = getMmbtuFactorConversionAllUtility($this->site_id);
	if (!is_array($dataFactor)) {
	    $dataFactor = [];
	}
	foreach ($keys as $key) {
	    $value = isset($dataFactor[$key]) ? $dataFactor[$key] : null;
	    if (function_exists('sanitizeMmbtuFactorForQuery')) {
		$dataFactor[$key] = sanitizeMmbtuFactorForQuery($value);
	    } else {
		$dataFactor[$key] = ($value === null || $value === '' || !is_numeric($value)) ? 1 : (float) $value;
	    }
	}
	return $dataFactor;
    }

    function monthlyUtilityProgress($filters = array())
    {
	$dataFactor = $this->getMmbtuFactorsForQuery();

	// For Jan month of new year
	$default_pre_month = date('n') - 1;
	$default_year = date('Y');
	$default_pre_year = date('Y') - 1;
	if($default_pre_month == 0) {
	    $default_pre_month = 12;
	    $default_year = date('Y') - 1;
	    $default_pre_year = date('Y') - 2;
	}

	$start_year = $default_year;
	$pre_start_year = isset($filters['progress_year']) ? $filters['progress_year'] : $default_pre_year;
	$pre_month = $default_pre_month;

	$where_more = '';
	$where_more = "(
			    (year_id='$start_year' AND (month_id>=1 AND month_id<='$pre_month'))
			    OR
			    (year_id='$pre_start_year' AND (month_id>=1 AND month_id<='$pre_month'))
			)";

	$query = "SELECT ((COALESCE(u.total_electricity_kwh,0) * " . $dataFactor['electricity'] . ") +
			(COALESCE(u.district_heating,0) * " . $dataFactor['district_heating'] . ") +
			(COALESCE(u.district_cooling,0) * " . $dataFactor['district_cooling'] . ") +
			(COALESCE(u.total_fuel_oil,0) * " . $dataFactor['fuel_oil'] . ") +
			(COALESCE(u.total_lpg,0) * " . $dataFactor['lpg'] . ") +
			(COALESCE(u.total_natural_gas,0) * " . $dataFactor['natural_gas'] . ") - u.onsite_generators_quantity - COALESCE(u.total_renewable_energy_production,0)) as energy,
			(COALESCE(u.total_renewable_energy_production,0)) as onsite_energy_generator_quantity,
			(COALESCE(u.total_renewable_energy_exported,0)) as renewable_energy_exported,
			(COALESCE(u.water_total_consumption,0) * " . $dataFactor['water'] . ") as water,
			(COALESCE(u.total_room_night,0)) as room_night,
			(COALESCE(u.total_guests,0)) as guest_night,
			s.site_builtup_area,s.cooled_builtup_area,u.month_id,u.year_id,site_id
		    FROM {$this->_tbl_utilities} as u
		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0
		    ORDER BY year_id ASC,month_id ASC,u.id ASC";
	$result = $this->db->query($query);
	$result = $result->result_array();
	return $this->fetchUpdatedRoomKeys($result);

    }

    /**
     * Get monthly utility data for progress on target widget
     * Supports fetching current year, previous year, and baseline year data in a single query
     * 
     * @param array $filters Contains Progress_start_year, Progress_previous_year, Progress_previous_month, baseline_year
     * @return array Utility data indexed by month/year
     */
    function monthlyUtilityProgressOnTarget($filters = array())
    {
        $dataFactor = $this->getMmbtuFactorsForQuery();
        $dateParams = getProgressWidgetDateParams();
        
        $default_pre_month = $dateParams['month'];
        $default_year = $dateParams['year'];
        $default_pre_year = $dateParams['previous_year'];

        $twoMonthsAgoTs = mktime(0, 0, 0, date('n') - 2, 1, date('Y'));
        $twoMonthsAgoMonth = (int)date('n', $twoMonthsAgoTs);
        $twoMonthsAgoYear = (int)date('Y', $twoMonthsAgoTs);

        $start_year = isset($filters['Progress_start_year']) ? $filters['Progress_start_year'] : $default_year;
        $pre_start_year = isset($filters['Progress_previous_year']) ? $filters['Progress_previous_year'] : $default_pre_year;
        $pre_month = isset($filters['Progress_previous_month']) ? $filters['Progress_previous_month'] : $default_pre_month;
        $baseline_year = isset($filters['baseline_year']) ? $filters['baseline_year'] : null;
		

        $yearConditions = [
            "(year_id='$start_year' AND (month_id>=1 AND month_id<='$pre_month'))",
            "(year_id='$twoMonthsAgoYear' AND month_id='$twoMonthsAgoMonth')"
        ];
        
        if ($pre_start_year !== 'NOTSET') {
            $yearConditions[] = "(year_id='$pre_start_year' AND (month_id>=1 AND month_id<='$pre_month'))";
        }
        
        if ($baseline_year && $baseline_year != $start_year && $baseline_year != $pre_start_year) {
            $yearConditions[] = "(year_id='$baseline_year' AND (month_id>=1 AND month_id<='$pre_month'))";
        }
        
        $where_more = "(" . implode(" OR ", $yearConditions) . ")";

        $query = "SELECT 
			SUM(
				COALESCE(u.total_electricity_kwh,0) * {$dataFactor['electricity']} +
				COALESCE(u.district_heating,0) * {$dataFactor['district_heating']} +
				COALESCE(u.district_cooling,0) * {$dataFactor['district_cooling']} +
				COALESCE(u.total_fuel_oil,0) * {$dataFactor['fuel_oil']} +
				COALESCE(u.total_lpg,0) * {$dataFactor['lpg']} +
				COALESCE(u.total_natural_gas,0) * {$dataFactor['natural_gas']}
			) - SUM(COALESCE(u.onsite_generators_quantity,0)) - SUM(COALESCE(u.total_renewable_energy_production,0)) AS energy,

			SUM(COALESCE(u.total_renewable_energy_production,0)) AS onsite_energy_generator_quantity,
			SUM(COALESCE(u.water_total_consumption,0) * {$dataFactor['water']}) AS water,
			SUM(COALESCE(u.total_room_night,0)) AS room_night,
			SUM(COALESCE(u.total_guests,0)) AS guest_night,

			s.site_builtup_area,
			s.cooled_builtup_area,
			u.year_id";
			if(!empty($filters['group_by']) && $filters['group_by'] == 'month') {
				$query .= ", u.month_id";
			}
		$query .= " FROM {$this->_tbl_utilities} AS u
		LEFT JOIN {$this->_tbl_sites} AS s ON s.id = u.site_id
		WHERE u.site_id = {$this->site_id} 
		AND {$where_more} 
		AND u.year_id != 0 
		AND u.month_id != 0
		GROUP BY u.year_id";
		if(!empty($filters['group_by']) && $filters['group_by'] == 'month') {
			$query .= ", u.month_id";
		}
		$query .= " ORDER BY u.year_id ASC";	
        $result = $this->db->query($query);
        $result = $result->result_array();
        return $this->fetchUpdatedRoomKeys($result);
    }

    /**
     * Get progress on target data with baseline included in single query
     * Returns structured data ready for calculateProgressOnTarget helper
     * 
     * @param int $baseline_year The baseline/regression year for the site
     * @return array ['current' => [...], 'baseline' => [...]]
     */
    function getProgressOnTargetWithBaseline($baseline_year, $groupBy = '', $selectedMonth = null, $selectedYear = null)
    {
        $dateParams = getProgressWidgetDateParams($selectedMonth, $selectedYear);
        $current_year = $dateParams['year'];
        $previous_year = $dateParams['previous_year'];
        $current_month = $dateParams['month'];
        
        $filters = [
            'Progress_start_year' => $current_year,
            'Progress_previous_year' => $previous_year,
            'Progress_previous_month' => $current_month,
            'baseline_year' => $baseline_year
        ];

        if(!empty($groupBy) && $groupBy == 'month') {
            $filters['group_by'] = 'month';
        }

        $allData = $this->monthlyUtilityProgressOnTarget($filters);
		if(!empty($groupBy) && $groupBy == 'month') {			
			$progressOnTarget = [];
			foreach ($allData as $key => $value) {
				$progressOnTarget[$value['year_id']][$value['month_id']] = $value;
			}
			return $progressOnTarget;
		} else {
			$progressOnTarget = [];
			foreach ($allData as $key => $value) {
				$progressOnTarget[$value['year_id']] = $value;
			}
			return $progressOnTarget;
		}
    }

	function groupUtilityChart()
	{
		$dataFactor = $this->getMmbtuFactorsForQuery();

		if(date('n') == 1){
			$startMonth = 1;
			$endMonth = 12;
			$startYear = date('Y') - 1;
			$endYear = date('Y') - 1;
		} else {
			$startMonth = date('n');
			$endMonth = date('n') - 1;
			$startYear = date('Y') - 1;
			$endYear = date('Y');
		}

		$where_more = '';
		$where_more = "(
						(u.year_id='$startYear' AND (u.month_id>='$startMonth'))
						OR
						(u.year_id='$endYear' AND (u.month_id<='$endMonth'))
						)";

		$query = "SELECT ((COALESCE(u.total_electricity_kwh,0) * " . $dataFactor['electricity'] . ") - COALESCE(u.onsite_generators_quantity,0) - COALESCE(u.total_renewable_energy_production,0)) as electricity,
						((COALESCE(u.total_lpg,0) * " . $dataFactor['lpg'] . ") +
			    (COALESCE(u.total_natural_gas,0) * " . $dataFactor['natural_gas'] . ")) as gases,
			    ((COALESCE(u.district_heating,0) * " . $dataFactor['district_heating'] . ") +
			    (COALESCE(u.district_cooling,0) * " . $dataFactor['district_cooling'] . ") +
			    (COALESCE(u.total_fuel_oil,0) * " . $dataFactor['fuel_oil'] . ")) as others,
			    s.cooled_builtup_area,u.month_id,u.year_id,u.total_room_night as room_night, u.total_guests as guest_night
			FROM {$this->_tbl_utilities} as u
			LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
			WHERE u.site_id={$this->site_id} AND {$where_more} AND u.year_id != 0 AND u.month_id != 0
			ORDER BY u.year_id ASC,u.month_id ASC,u.id ASC";
	$result = $this->db->query($query);
	$result = $result->result_array();
	return $this->fetchUpdatedRoomKeys($result);
    }


	function fetchReferenceYearEnergyTarget($filterTargetMonthly, $isRoomNight = false) {
		$dataFactor = $this->getMmbtuFactorsForQuery();
		$filterTargetMonthly['year_id'] = is_numeric($filterTargetMonthly['year_id']) && !empty($filterTargetMonthly['year_id']) ? $filterTargetMonthly['year_id'] : date('Y') - 1;

		$query = "SELECT ((COALESCE(u.total_electricity_kwh,0) * " . $dataFactor['electricity'] . ") +
			(COALESCE(u.district_heating,0) * " . $dataFactor['district_heating'] . ") +
			(COALESCE(u.district_cooling,0) * " . $dataFactor['district_cooling'] . ") +
			(COALESCE(u.total_fuel_oil,0) * " . $dataFactor['fuel_oil'] . ") +
			(COALESCE(u.total_lpg,0) * " . $dataFactor['lpg'] . ") +
			(COALESCE(u.total_natural_gas,0) * " . $dataFactor['natural_gas'] . ") - u.onsite_generators_quantity - COALESCE(u.total_renewable_energy_production,0)) as energy,
			s.cooled_builtup_area,u.month_id,u.year_id, u.total_room_night as room_night
		    FROM {$this->_tbl_utilities} as u
		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
		    WHERE site_id={$this->site_id} AND year_id = {$filterTargetMonthly['year_id']} AND month_id != 0 AND year_id != 0
		    ORDER BY month_id ASC,u.id ASC";
			$result = $this->db->query($query);
			$result = $result->result_array();
			$data = $this->fetchUpdatedRoomKeys($result);
			foreach ($data as $key => $value) {
				if($isRoomNight) {
					$value['energyConverted'] = ($value['room_night'] != 0 && isset($value['energy']) && $value['energy'] != 0) ? $value['energy']/$value['room_night'] : 0;
				} else {
					$value['energyConverted'] = ($value['cooled_builtup_area'] != 0 && isset($value['energy']) && $value['energy'] != 0) ? $value['energy']/$value['cooled_builtup_area'] : 0;
				}
				$referenceYearBudget[$value['month_id']] = $value;
			}
			return $referenceYearBudget;
	}

    function getPerformanceChartData($filters)
    {
	$dataFactor = $this->getMmbtuFactorsForQuery();
	$dataFactor['water'] = 1;
	$multiplyValue = '1';
	$areaUnit = [
	    'm&#178;',
	    'ft&#178;'
	];
	$totalScopeEmissionPreviousAverage = 0;
	$totalScopeEmissionCurrentAverage = 0;
	$this->load->model('utilities/utilities_model');
	$this->load->model('sites/sites_model');

	$site_detail = $this->sites_model->get_site_detail_custom($filters['site_id']);
	$this->utilities_model->site_id = $filters['site_id'];
	if(date('n') == 1) {
		$currentYear = date('Y') - 1;
		$lastYear = date('Y') - 2;
		$TwoYearBack = date('Y') - 3;
	} else {
		$currentYear = date('Y');
		$lastYear = date('Y') - 1;
		$TwoYearBack = date('Y') - 2;
	}
	$this->utilities_model->utilities_year = $currentYear;

	if ($filters['is_baseline_year']) {
	    $filters['baseline_regression_year'] = $site_detail['baseline_regression_year'];
	    $getSiteUtility = $this->utilities_model->getSiteBaselineUtility($filters);
	} else {
	    $getSiteUtility = $this->utilities_model->getSiteUtility();
	}
	$this->load->model('sites/site_waste_model');
	$this->site_waste_model->site_id = $filters['site_id'];
	$this->site_waste_model->year_id = $currentYear;
	$this->site_waste_model->month_id = NULL;
	$site_waste_utility = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
	$this->site_waste_model->year_id = $currentYear - 1;
	$this->site_waste_model->month_id = NULL;
	$site_waste_previous_year_utility = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
	// Settings (no year/month filter)
	$this->site_waste_model->year_id = NULL;
	$this->site_waste_model->month_id = NULL;
	$site_waste_setting = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();

	$utitlityRoomNight = $utitlityTotalFoodHandled = [];
	if (!empty($getSiteUtility)) {
	    foreach ($getSiteUtility as $key => $result) {
	    $electricityKwh = $districtHeatingKwh = $districtCoolingKwh = $fuelKwh = $lpgKwh = $naturalGasKwh = $onsite_generators_quantityKWH = 0;
	    $utilityConsumptionElectricityKwh = $utilityConsumptionDistrictHeatingKwh = $utilityConsumptionDistrictCoolingKwh = $utilityConsumptionFuelKwh = $utilityConsumptionLpgKwh = $utilityConsumptionNaturalGasKwh = 0;
	    if (isset($performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id']]) && !empty($performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id']])) {
		continue;
	    } else {
		//food and beverages room night
		$utitlityRoomNight[$result['month_id']][$result['year_id']] = isset($result['total_room_night']) ? $result['total_room_night'] : 0;
		//food and beverages total food handled
		$utitlityTotalFoodHandled[$result['month_id']][$result['year_id']] = isset($result['total_fb_services']) ? $result['total_fb_services'] : 0;

		//calculation start
		//Utility Cost start
		$totalUtilityCost = floatval($result['total_electricity_cost'] ?? 0)
		    + floatval($result['total_fuel_oil_cost'] ?? 0)
		    + floatval($result['total_lpg_cost'] ?? 0)
		    + floatval($result['total_natural_gas_cost'] ?? 0)
		    + floatval($result['district_heating_cost'] ?? 0)
		    + floatval($result['district_cooling_cost'] ?? 0)
		    + floatval($result['water_total_consumption_cost'] ?? 0)
		    + floatval($result['district_cooling_fixed_cost'] ?? 0)
		    + floatval($result['district_heating_fixed_cost'] ?? 0)
		    + floatval($result['lpg_fixed_cost'] ?? 0)
		    + floatval($result['natural_gas_fixed_cost'] ?? 0)
		    + floatval($result['water_fixed_cost'] ?? 0);

		//setting Utility Cost
		$performanceReportData['UtilityCost'][$result['month_id']][$result['year_id']] = isset($totalUtilityCost) && is_numeric($totalUtilityCost) && is_finite($totalUtilityCost) ? round($totalUtilityCost) : 0;

		//calculation for Utility Cost room night
		$performanceReportData['UtilityCostRoomNight'][$result['month_id']][$result['year_id']] = isset($result['total_room_night']) && $result['total_room_night'] != 0 ? round($totalUtilityCost / $result['total_room_night']) : 0;

		//calculation for Utility Cost intensity
		$performanceReportData['UtilityCostIntensity'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalUtilityCost / $site_detail['site_builtup_area']) : 0;

		// Calculation Utility Consumption per night room
		//taking value in a variable;
		$electricityKwh = (isset($result['total_electricity_kwh']) && $site_detail['show_utility_electricity'] == 1) ? $result['total_electricity_kwh'] : 0;
		$electricityKwhExcludingOnsiteGenerator = 
		(isset($result['total_electricity_kwh']) && $site_detail['show_utility_electricity'] == 1) ? 
		$result['total_electricity_kwh'] - $result['onsite_generators_quantity'] - ($result['total_renewable_energy_production'] ?? 0) : 0;
		$districtHeatingKwh = (isset($result['district_heating']) && $site_detail['show_utility_district_heating'] == 1) ? $result['district_heating'] : 0;
		$districtCoolingKwh = (isset($result['district_cooling']) && $site_detail['show_utility_district_cooling'] == 1) ? $result['district_cooling'] : 0;
		$fuelKwh = (isset($result['total_fuel_oil']) && $site_detail['show_utility_fuel_oil'] == 1) ? $result['total_fuel_oil'] : 0;
		$lpgKwh = (isset($result['total_lpg']) && $site_detail['show_utility_lpg'] == 1) ? $result['total_lpg'] : 0;
		$naturalGasKwh = (isset($result['total_natural_gas']) && $site_detail['show_utility_natural_gas'] == 1) ? $result['total_natural_gas'] : 0;
		$onsite_generators_quantityKWH = (isset($result['onsite_generators_quantity']) && $site_detail['show_utility_electricity'] == 1) ? $result['onsite_generators_quantity'] : 0;

		//Calculation of Utility Consumption Room night
		$totalUtilityConsumptionRoomNight = (isset($result['total_room_night']) && $result['total_room_night'] != 0) ? ($electricityKwh / $result['total_room_night']) + ($districtHeatingKwh / $result['total_room_night']) + ($districtCoolingKwh / $result['total_room_night']) + ($fuelKwh / $result['total_room_night']) + ($lpgKwh / $result['total_room_night']) + ($naturalGasKwh / $result['total_room_night']) : 0;

		//Calculation of Utility Consumption per square footage
		$totalUtilityConsumptionBuildUp = (isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0) ? ($electricityKwh / $site_detail['site_builtup_area']) + ($districtHeatingKwh / $site_detail['site_builtup_area']) + ($districtCoolingKwh / $site_detail['site_builtup_area']) + ($fuelKwh / $site_detail['site_builtup_area']) + ($lpgKwh / $site_detail['site_builtup_area']) + ($naturalGasKwh / $site_detail['site_builtup_area']) : 0;

		//Calculation Utility Consumption Start
		$utilityConsumptionElectricityKwh = ($electricityKwh * $dataFactor['electricity'] * $multiplyValue);
		isset($utilityConsumptionElectricityKwh) && is_numeric($utilityConsumptionElectricityKwh) && is_finite($utilityConsumptionElectricityKwh) ? $utilityConsumptionElectricityKwh : 0;
		$utilityConsumptionElectricityKwhExcOnSite = ($electricityKwhExcludingOnsiteGenerator * $dataFactor['electricity'] * $multiplyValue);
		isset($utilityConsumptionElectricityKwhExcOnSite) && is_numeric($utilityConsumptionElectricityKwhExcOnSite) && is_finite($utilityConsumptionElectricityKwhExcOnSite) ? $utilityConsumptionElectricityKwhExcOnSite : 0;

		$utilityConsumptionDistrictHeatingKwh = $districtHeatingKwh * $dataFactor['district_heating'] * $multiplyValue;
		isset($utilityConsumptionDistrictHeatingKwh) && is_numeric($utilityConsumptionDistrictHeatingKwh) && is_finite($utilityConsumptionDistrictHeatingKwh) ? $utilityConsumptionDistrictHeatingKwh : 0;
		$utilityConsumptionDistrictCoolingKwh = $districtCoolingKwh * $dataFactor['district_cooling'] * $multiplyValue;
		isset($utilityConsumptionDistrictCoolingKwh) && is_numeric($utilityConsumptionDistrictCoolingKwh) && is_finite($utilityConsumptionDistrictCoolingKwh) ? $utilityConsumptionDistrictCoolingKwh : 0;
		$utilityConsumptionFuelKwh = $fuelKwh * $dataFactor['fuel_oil'] * $multiplyValue;
		isset($utilityConsumptionFuelKwh) && is_numeric($utilityConsumptionFuelKwh) && is_finite($utilityConsumptionFuelKwh) ? $utilityConsumptionFuelKwh : 0;
		$utilityConsumptionLpgKwh = $lpgKwh * $dataFactor['lpg'] * $multiplyValue;
		isset($utilityConsumptionLpgKwh) && is_numeric($utilityConsumptionLpgKwh) && is_finite($utilityConsumptionLpgKwh) ? $utilityConsumptionLpgKwh : 0;
		$utilityConsumptionNaturalGasKwh = $naturalGasKwh * $dataFactor['natural_gas'] * $multiplyValue;
		isset($utilityConsumptionNaturalGasKwh) && is_numeric($utilityConsumptionNaturalGasKwh) && is_finite($utilityConsumptionNaturalGasKwh) ? $utilityConsumptionNaturalGasKwh : 0;
		
		//Calculation of Utility Carbon footprint Start
		$totalCarbonFootprint = 0;
		$totalCarbonFootprint = ($utilityConsumptionElectricityKwhExcOnSite * $site_detail['electricity_emission_factor']) + ($utilityConsumptionDistrictHeatingKwh * $site_detail['district_heating_emission_factor']) + ($utilityConsumptionDistrictCoolingKwh * $site_detail['district_cooling_emission_factor']) + ($utilityConsumptionFuelKwh * $site_detail['fuel_emission_factor']) + ($utilityConsumptionLpgKwh * $site_detail['lpg_emission_factor']) + ($utilityConsumptionNaturalGasKwh * $site_detail['natural_gas_emission_factor']);

		//Calculation of Utility Carbon footprint
		$performanceReportData['CarbonFootprint'][$result['month_id']][$result['year_id']] =  round($totalCarbonFootprint) ?? 0;

		//Calculation of Utility Carbon footprint per guest night
		$performanceReportData['CarbonFootprintGuestNight'][$result['month_id']][$result['year_id']] = isset($result['total_guests']) && $result['total_guests'] != 0 ? round($totalCarbonFootprint / $result['total_guests'], 2) : 0;

		$totalUtilityConsumption = ($utilityConsumptionElectricityKwh + $utilityConsumptionDistrictHeatingKwh + $utilityConsumptionDistrictCoolingKwh + $utilityConsumptionFuelKwh + $utilityConsumptionLpgKwh + $utilityConsumptionNaturalGasKwh) - $onsite_generators_quantityKWH - ($result['total_renewable_energy_production'] ?? 0);
		//Setting the Utility Consumption
		$performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id']] = round($totalUtilityConsumption) ?? 0;

		$cooledBuiltupArea = !empty($site_detail['cooled_builtup_area']) ? $site_detail['cooled_builtup_area'] : 0;
		$totalUtilityConsumptionRoomNight = (isset($result['total_room_night']) && $result['total_room_night'] != 0)
		    ? ($totalUtilityConsumption / $result['total_room_night'])
		    : 0;
		$totalUtilityConsumptionBuildUp = ($cooledBuiltupArea != 0)
		    ? ($totalUtilityConsumption / $cooledBuiltupArea)
		    : 0;

		//Calculation of Utility Consumption Room night
		$performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][$result['year_id']] = isset($result['total_room_night']) && $result['total_room_night'] != 0 ? round($totalUtilityConsumptionRoomNight, 2) : 0;

		//Calculation of Utility Consumption Intensity (per conditioned area, matching EUI chart)
		$performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][$result['year_id']] = ($cooledBuiltupArea != 0) ? round($totalUtilityConsumptionBuildUp, 2) : 0;

		//calculation scope emission start
		$scopeEmissionElectricityKwh = (isset($result['total_electricity_kwh']) && $site_detail['show_utility_electricity'] == 1) ? $result['total_electricity_kwh'] : 0;
		$scopeEmissionNaturalGas = (isset($result['total_natural_gas']) && $site_detail['show_utility_natural_gas'] == 1) ? ($result['total_natural_gas'] * $site_detail['natural_gas_emission_factor']) : 0;
		$scopeEmissionDistrictCooling = (isset($result['district_cooling']) && $site_detail['show_utility_district_cooling'] == 1) ? ($result['district_cooling'] * $site_detail['district_cooling_emission_factor']) : 0;
		$totalScopeEmission = $scopeEmissionElectricityKwh + $scopeEmissionNaturalGas + $scopeEmissionDistrictCooling;

		//Calculation scope Emission Average
		if ($result['year_id'] == $currentYear) {
		    $totalScopeEmissionCurrentAverage = $totalScopeEmissionCurrentAverage + $totalScopeEmission;
		} else {
		    $totalScopeEmissionPreviousAverage = $totalScopeEmissionPreviousAverage + $totalScopeEmission;
		}
		//Calculation Scope Emission
		$performanceReportData['ScopeEmission'][$result['month_id']][$result['year_id']] = round($totalScopeEmission);

		//Calculation Scope Emission Per Square Footage area
		$performanceReportData['ScopeEmissionPerSquareFootage'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalScopeEmission / $site_detail['site_builtup_area']) : 0;

		//Calculation of Renewable energy generated Start.
		$totalRenewableEnergy = isset($result['total_renewable_energy_production']) ? ($result['total_renewable_energy_production']) : 0;
		$performanceReportData['RenewableEnergyGenerated'][$result['month_id']][$result['year_id']] = round($totalRenewableEnergy);

		//Calculation of Renewable energy generated Start.
		$totalRenewableEnergyExported = isset($result['total_renewable_energy_exported']) ? ($result['total_renewable_energy_exported']) : 0;
		$performanceReportData['RenewableEnergyExported'][$result['month_id']][$result['year_id']] = round($totalRenewableEnergyExported);
		//Calculation of Renewable energy generated Intensity Start.
		$performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalRenewableEnergy / $site_detail['site_builtup_area']) : 0;


		//calculation for Occupancy start
		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
		$Occupancy = (isset($result['total_room_night']) && $result['total_room_night'] && $site_detail['rooms_keys'] != 0) ? (($result['total_room_night'] / ($site_detail['rooms_keys'] * $days_of_month)) * 100) : 0;
		// Add Occupancy for all utility chart types
		$performanceReportData['ScopeEmission'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['ScopeEmissionPerSquareFootage'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['CarbonFootprint'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['CarbonFootprintGuestNight'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['RenewableEnergyGenerated'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['RenewableEnergyExported'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy) ?? 0;
		$performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCost'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCostRoomNight'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCostIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);

		// Add budget calculations for new performance chart Budget v\s Total utility cost
		if ($filters['performance_chart_type'] == 'budget_vs_total_utility_cost') {
		    $totalBudgetCost = isset($result) ? floatval($result['electricity_total_budget_cost'] ?? 0) + floatval($result['fuel_total_budget_cost'] ?? 0) + floatval($result['lpg_total_budget_cost'] ?? 0) + floatval($result['natural_gas_total_budget_cost'] ?? 0) + floatval($result['district_heating_total_budget_cost'] ?? 0) + floatval($result['district_cooling_total_budget_cost'] ?? 0) + floatval($result['water_total_consumption_budget_cost'] ?? 0) : 0;
		    $performanceReportData['UtilityCost'][$result['month_id']][$result['year_id'].'_budget'] = isset($totalBudgetCost) && is_numeric($totalBudgetCost) && is_finite($totalBudgetCost) ? round($totalBudgetCost) : 0;
		}

		if (!$filters['is_baseline_year']) {
		    //Unset
		    unset($performanceReportData['UtilityCost'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['UtilityCostRoomNight'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['UtilityCostIntensity'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['UtilityConsumption'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['ScopeEmission'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['ScopeEmissionPerSquareFootage'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['CarbonFootprint'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['CarbonFootprintGuestNight'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['RenewableEnergyGenerated'][$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][$TwoYearBack]);
		    unset($utitlityRoomNight[$result['month_id']][$TwoYearBack]);
		    unset($utitlityTotalFoodHandled[$result['month_id']][$TwoYearBack]);
		    unset($performanceReportData['RenewableEnergyGenerated'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['UtilityConsumption'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['UtilityCost'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    if ($filters['performance_chart_type'] == 'budget_vs_total_utility_cost') {
			unset($performanceReportData['UtilityCost'][$result['month_id']][($TwoYearBack) . '_budget']);
		    }
		    unset($performanceReportData['UtilityCostRoomNight'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['UtilityCostIntensity'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['ScopeEmission'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['ScopeEmissionPerSquareFootage'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['CarbonFootprint'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		    unset($performanceReportData['CarbonFootprintGuestNight'][$result['month_id']][($TwoYearBack) . '_occupancy']);
		}
	    }
	    }
	}
	$performanceReportData['ScopeEmission'][13][$currentYear] = round($totalScopeEmissionCurrentAverage / 12);
	$comparingYear = ($filters['is_baseline_year']) ? $site_detail['baseline_regression_year'] : $currentYear - 1;
	$performanceReportData['ScopeEmission'][13][$comparingYear] = round($totalScopeEmissionPreviousAverage / 12);
	$data = [];
	switch ($filters['performance_chart_type']) {
	    case 'utility_consumption':
		$data['performanceReportArray'] = $performanceReportData['UtilityConsumption'];
		$data['y_axis'] = 'kWh';
		$data['report_title'] = 'Total Energy Consumption';
		$data['unit'] = 'kWh';
		break;

	    case 'utility_consumption_intesity_per_square_footage':
		$data['performanceReportArray'] = $performanceReportData['UtilityConsumptionIntensity'];
		$data['y_axis'] = 'kWh'. '/' .  getLocalUnitText($site_detail['id']);
		$data['report_title'] = 'Total Energy Consumption Intensity (per square '. getLocalUnitFullText($site_detail['id']).')';
		$data['unit'] = $site_detail['local_currency'] . '/' .  getLocalUnitText($site_detail['id']);
		break;

	    case 'utility_consumption_intensity_per_room_night':
		$data['performanceReportArray'] = $performanceReportData['UtilityConsumptionRoomNight'];
		$data['y_axis'] = 'kWh/RN';
		$data['report_title'] = 'Total Energy Consumption Intensity (per room-night)';
		$data['unit'] = $site_detail['local_currency'] . '/room-night)';
		break;

	    case 'diversion_rate':
		$site_waste_setting = isset($site_waste_setting) ? $site_waste_setting[0]['s'] : [];
		$checkTotalTrackedWaste =  $checkDivertedWaste = $reportData = [];
		foreach ($site_waste_setting as $key => $value) {
		    if (strpos($key, 'typical_destination_') !== false) {
			$name = substr($key, strpos($key, 'typical_destination_') + strlen('typical_destination_'));
			if ($site_waste_setting['monthly_tracking_' . $name] == 2) {
			    array_push($checkTotalTrackedWaste, 'unit_measure_' . $name);
			    // Diverted = everything except Landfill (1) and Unknown (7)
			    if (!in_array((int) $value, array(1, 7), true)) {
				array_push($checkDivertedWaste, 'unit_measure_' . $name);
			    }
			}
		    }
		}
		foreach ($site_waste_utility as $key => $value) {
		    $monthlyTrackedTotalWaste = $monthlyTrackedDivertedWaste = 0;
		    foreach ($value['s'] as $keyValue => $dataInLoop) {
			if (in_array($keyValue, $checkTotalTrackedWaste)) {
			    $monthlyTrackedTotalWaste = $monthlyTrackedTotalWaste + $dataInLoop;
			}
			if (in_array($keyValue, $checkDivertedWaste)) {
			    $monthlyTrackedDivertedWaste = $monthlyTrackedDivertedWaste + $dataInLoop;
			}
		    }
		    $performanceReportData['DiversionRate'][$value['s']['month_id']][$value['s']['year_id']] = round((float) calculateDashboardPercentage($monthlyTrackedDivertedWaste, $monthlyTrackedTotalWaste), 2);
		}
		for ($i = 1; $i < 13; $i++) {
		    if (isset($performanceReportData['DiversionRate'][$i][$currentYear]) && !empty($performanceReportData['DiversionRate'][$i][$currentYear])) {
			continue;
		    } else {
			$performanceReportData['DiversionRate'][$i][$currentYear] = 0;
		    }
		}
		ksort($performanceReportData['DiversionRate']);
		$data['performanceReportArray'] = $performanceReportData['DiversionRate'];
		$data['y_axis'] = '% Waste diverted from Landfill';
		$data['report_title'] = 'Waste Diversion Rate';
		$data['unit'] = '%';
		break;

	    case 'scope_1_+_2_emissions':
		$data['performanceReportArray'] = $performanceReportData['ScopeEmission'];
		$data['y_axis'] = $data['unit'] = 'kgCO2';
		$data['opposite_Y_axis'] = 'Occupancy';
		$data['report_title'] = 'Scope 1 + 2 Emissions';
		break;

	    case 'scope_1_+_2_emissions_per_square_footage':
		$data['performanceReportArray'] = $performanceReportData['ScopeEmissionPerSquareFootage'];
		$data['y_axis'] = $data['unit'] = 'kgCO2/' .  getLocalUnitText($site_detail['id']);
		$data['opposite_Y_axis'] = 'Occupancy';
		$data['report_title'] = 'Scope 1 + 2 Emissions (per square '.  getLocalUnitFullText($site_detail['id']).')';
		break;

	    case 'utility_cost':
		$data['performanceReportArray'] = $performanceReportData['UtilityCost'];
		$data['y_axis'] = 'Cost(' . $site_detail['local_currency'] . ')';
		$data['report_title'] = 'Utilities Cost';
		$data['unit'] = $site_detail['local_currency'];
		break;

	    case 'budget_vs_total_utility_cost':
		$data['performanceReportArray'] = $performanceReportData['UtilityCost'];
		$data['y_axis'] = 'Cost(' . $site_detail['local_currency'] . ')';
		$data['report_title'] = 'Budget vs total utility cost';
		$data['unit'] = $site_detail['local_currency'];
		break;

	    case 'utility_cost_intensity_per_square_footage':
		$data['performanceReportArray'] = $performanceReportData['UtilityCostIntensity'];
		$data['y_axis'] = 'Cost(' . $site_detail['local_currency'] . '/' .  getLocalUnitText($site_detail['id']) . ')';
		$data['report_title'] = 'Utility Cost (per square '.  getLocalUnitFullText($site_detail['id']).')';
		$data['unit'] = $site_detail['local_currency'] . '/' .  getLocalUnitText($site_detail['id']);
		break;

	    case 'utility_cost_intensity_per_room_night':
		$data['performanceReportArray'] = $performanceReportData['UtilityCostRoomNight'];
		$data['y_axis'] = 'Cost(' . $site_detail['local_currency'] . '/room-night)';
		$data['report_title'] = 'Total Utility Cost (per room-night)';
		$data['unit'] = $site_detail['local_currency'] . '/room-night)';
		break;

	    case 'renewable_energy_generated':
		$data['performanceReportArray'] = $performanceReportData['RenewableEnergyGenerated'];
		$data['y_axis'] = $data['unit'] = 'kWh';
		$data['report_title'] = 'Renewable Energy Generated';
		break;

	    case 'renewable_energy_generated_intensity':
		$data['performanceReportArray'] = $performanceReportData['RenewableEnergyGeneratedIntensity'];
		$data['y_axis'] = $data['unit'] = 'kWh/' .  getLocalUnitText($site_detail['id']);
		$data['report_title'] = 'Renewable Energy Generated Intensity';
		break;

	    case 'food_and_beverage_waste':
		case 'food_and_beverage_waste_room_night':
		case 'food_and_beverage_waste_total_food_handled':
		$site_waste_setting = isset($site_waste_setting) ? $site_waste_setting[0]['s'] : [];
		$trackedFoodType = [];
		foreach ($site_waste_setting as $key => $value) {
		    if (strpos($key, 'typical_destination_') !== false) {
			$name = substr($key, strpos($key, 'typical_destination_') + strlen('typical_destination_'));
			if ($site_waste_setting['monthly_tracking_' . $name] == 2 && (strpos($key, 'food') !== false || strpos($key, 'inedible_parts') !== false)) {
			    array_push($trackedFoodType, 'unit_measure_' . $name);
			}
		    }
		}

		foreach ($site_waste_previous_year_utility as $key => $value) {
		    $monthlyTrackedTotalFood = 0;
		    foreach ($value['s'] as $keyValue => $data) {
			if (in_array($keyValue, $trackedFoodType)) {
			    $monthlyTrackedTotalFood = $monthlyTrackedTotalFood + $data;
			}
		    }
		    $performanceReportData['FoodAndBeverageWaste'][$value['s']['month_id']][$value['s']['year_id']] = round($monthlyTrackedTotalFood);
		    $utitlityRoomNightValue = $utitlityRoomNight[$value['s']['month_id']][$value['s']['year_id']];
		    $utitlityTotalFoodHandledValue = $utitlityTotalFoodHandled[$value['s']['month_id']][$value['s']['year_id']];
		    $performanceReportData['FoodAndBeverageWasteRoomNight'][$value['s']['month_id']][$value['s']['year_id']] = isset($utitlityRoomNightValue) && ($utitlityRoomNightValue != 0) ? round($monthlyTrackedTotalFood / $utitlityRoomNightValue, 2) : 0;
		    $performanceReportData['FoodAndBeverageWasteTotalFoodhandled'][$value['s']['month_id']][$value['s']['year_id']] = isset($utitlityTotalFoodHandledValue) && ($utitlityTotalFoodHandledValue != 0) ? round($monthlyTrackedTotalFood / $utitlityTotalFoodHandledValue, 2) : 0;
		}

		foreach ($site_waste_utility as $key => $value) {
		    $monthlyTrackedTotalFood = 0;
		    foreach ($value['s'] as $keyValue => $data) {
			if (in_array($keyValue, $trackedFoodType)) {
			    $monthlyTrackedTotalFood = $monthlyTrackedTotalFood + $data;
			}
		    }
		    $performanceReportData['FoodAndBeverageWaste'][$value['s']['month_id']][$value['s']['year_id']] = round($monthlyTrackedTotalFood);
		    $utitlityRoomNightValue = $utitlityRoomNight[$value['s']['month_id']][$value['s']['year_id']];
		    $utitlityTotalFoodHandledValue = $utitlityTotalFoodHandled[$value['s']['month_id']][$value['s']['year_id']];
		    $performanceReportData['FoodAndBeverageWasteRoomNight'][$value['s']['month_id']][$value['s']['year_id']] = isset($utitlityRoomNightValue) && ($utitlityRoomNightValue != 0) ? round($monthlyTrackedTotalFood / $utitlityRoomNightValue, 2) : 0;
		    $performanceReportData['FoodAndBeverageWasteTotalFoodhandled'][$value['s']['month_id']][$value['s']['year_id']] = isset($utitlityTotalFoodHandledValue) && ($utitlityTotalFoodHandledValue != 0) ? round($monthlyTrackedTotalFood / $utitlityTotalFoodHandledValue, 2) : 0;
		}

		if ($filters['performance_chart_type'] == 'food_and_beverage_waste') {
		    $data['performanceReportArray'] = $performanceReportData['FoodAndBeverageWaste'];
		    $data['y_axis'] = $data['unit'] = 'Kg';
		    $data['report_title'] = 'Food and Beverage Waste';
		} elseif ($filters['performance_chart_type'] ==  'food_and_beverage_waste_room_night') {
		    $data['performanceReportArray'] = $performanceReportData['FoodAndBeverageWasteRoomNight'];
		    $data['y_axis'] = $data['unit'] = 'Kg/night';
		    $data['report_title'] = 'Food and Beverage Waste/Room-Night';
		} elseif ($filters['performance_chart_type'] == 'food_and_beverage_waste_total_food_handled') {
		    $data['performanceReportArray'] = $performanceReportData['FoodAndBeverageWasteTotalFoodhandled'];
		    $data['y_axis'] = $data['unit'] = 'Kg/kg';
		    $data['report_title'] = 'Food and Beverage Waste/Total Food Handled (Food Cover)';
		} else {
		    break;
		}
		break;

	    case  'carbon_footprint':
		$data['performanceReportArray'] = $performanceReportData['CarbonFootprint'];
		$data['y_axis'] = $data['unit'] = 'kgCO2';
		$data['report_title'] = 'Carbon Emissions (Scope 1 and 2)';
		break;

	    case 'carbon_emissions_kgco2_gn':
		$data['performanceReportArray'] = $performanceReportData['CarbonFootprintGuestNight'];
		$data['y_axis'] = $data['unit'] = 'kgCO2/GN';
		$data['report_title'] = 'Carbon Emissions kgCO2/GN';
		break;

	    case 'tonnes_of_carbon_offsets_purchased':
		break;

	    default:
		break;
	}
	return $data;
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
	    foreach (array('rooms_keys', 'cooled_builtup_area') as $subkey) {
		if (!array_key_exists($subkey, $value)) {
		    continue;
		}
		$dataFetch = array(
		    'site_id' => $row_site_id,
		    'area_update_field' => $subkey,
		);
		$latestAreaEntry = $this->sites_model->getlatestSiteArea($dataFetch);
		if (!empty($latestAreaEntry)) {
		    $latestAreaEntry = (array) $latestAreaEntry;
		    if (isset($latestAreaEntry['area_update_value'])) {
			$result[$key][$subkey] = $latestAreaEntry['area_update_value'];
		    }
		}
	    }
	}
	return $result;
    }
}
