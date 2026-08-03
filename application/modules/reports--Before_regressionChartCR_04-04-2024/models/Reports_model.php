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


	$query = "SELECT (u.total_electricity_kwh) as electricity, u.electricity_total_budget as electricity_budget,u.total_fuel_oil as fuel_oil, u.fuel_total_budget as fuel_budget,
			    u.total_lpg as lpg, u.lpg_total_budget as lpg_budget,

			    u.total_natural_gas as natural_gas, u.natural_gas_total_budget as natural_gas_budget,

			    u.district_heating as district_heating, u.district_heating_total_budget as district_heating_budget,

			    u.district_cooling as district_cooling, u.district_cooling_total_budget as district_cooling_budget,

			    u.water_total_consumption as water,u.water_total_consumption_budget as water_budget,

			    u.water_utility_supply as water_utility,u.water_irrigation as water_irrigation,u.waste_water as waste_water,u.water_Cisterns as water_cisterns,

			    u.cdd,u.hdd,

			    u.month_id,

			    u.year_id,

			    u.total_guests,

			    u.total_room_night,

			    u.total_laundered,

			    s.site_builtup_area,s.cooled_builtup_area,s.rooms_keys

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

		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0

		    ORDER BY year_id ASC,month_id ASC,u.id ASC";



	$result = $this->db->query($query);

	return $result->result_array();
    }



    function kwhUnitBasedReportForCurrentYear($filters)
    {

	$current_year = $filters['report_year']; //date('Y');

	$current_month = $filters['max_month_id'];

	$query = "SELECT SUM(total_electricity_kwh - onsite_generators_quantity) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district, district_cooling_fixed_cost, district_heating_fixed_cost, lpg_fixed_cost, natural_gas_fixed_cost, water_fixed_cost,

			SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

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



	$query = "SELECT SUM(total_electricity_cost) as electricity, SUM(total_fuel_oil_cost) as fuel, SUM(total_lpg_cost) as lpg, SUM(total_natural_gas_cost) as natural_gas, SUM(district_heating_cost) as heating_district, SUM(district_heating_fixed_cost) as district_heating_fixed_cost, SUM(district_cooling_fixed_cost) as district_cooling_fixed_cost, SUM(district_cooling_cost) as cooling_district, SUM(water_total_consumption_cost) as water, SUM(lpg_fixed_cost) as lpg_fixed_cost, SUM(natural_gas_fixed_cost) as natural_gas_fixed_cost, SUM(water_fixed_cost) as water_fixed_cost

		    FROM {$this->_tbl_utilities}

		    WHERE (year_id='$current_year' AND month_id<='$current_month')

		    AND site_id={$this->site_id}";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function kwhUnitBasedReportForPreviousMonth($filters = array())
    {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT (total_electricity_kwh - onsite_generators_quantity) as electricity, district_heating as heating_district, district_cooling as cooling_district,

			total_fuel_oil as fuel,total_lpg as lpg,total_natural_gas as natural_gas

		    FROM {$this->_tbl_utilities}

		    WHERE site_id={$this->site_id} AND (year_id='$year' AND month_id='$month')";



	$result = $this->db->query($query);

	return $result->row_array();
    }



    function costBasedReportForPreviousMonth($filters = array())
    {

	$month = $filters['previous_month'];

	$year = $filters['previous_year'];



	$query = "SELECT total_electricity_cost as electricity, total_fuel_oil_cost as fuel, total_lpg_cost as lpg, total_natural_gas_cost as natural_gas, district_heating_cost as heating_district, district_cooling_cost as cooling_district, district_heating_fixed_cost as district_heating_fixed_cost, district_cooling_fixed_cost as district_cooling_fixed_cost, lpg_fixed_cost, natural_gas_fixed_cost, water_fixed_cost, water_total_consumption_cost as water

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

			    (u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh_carbon,
			    (u.total_electricity_kwh) as total_electricity_kwh,

			month_id,year_id,total_room_night,s.rooms_keys

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



	$query = "SELECT (u.total_electricity_kwh - onsite_generators_quantity) as electricity,

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

			    (u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh,

			month_id,year_id,total_room_night,s.rooms_keys

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



	$query = "SELECT SUM(u.total_electricity_cost) as electricity, SUM(u.total_electricity_kwh - u.onsite_generators_quantity) as electricity_unit,

			SUM(u.total_fuel_oil_cost) as fuel, SUM(u.total_fuel_oil) as fuel_unit,

			SUM(u.total_lpg_cost) as lpg, SUM(u.total_lpg) as lpg_unit,

			SUM(u.total_natural_gas_cost) as natural_gas, SUM(u.total_natural_gas) as natural_gas_unit,

			SUM(u.district_heating_cost) as heating_district, SUM(u.district_heating) as heating_district_unit,

			SUM(u.district_cooling_cost) as cooling_district, SUM(u.district_cooling) as cooling_district_unit,

			SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost,

			SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

			SUM(u.lpg_fixed_cost) as lpg_fixed_cost,

			SUM(u.natural_gas_fixed_cost) as natural_gas_fixed_cost,

			SUM(u.water_fixed_cost) as water_fixed_cost,

			SUM(u.water_total_consumption_cost) as water, SUM(u.water_total_consumption) as water_unit,

			SUM(u.cdd) as cdd,

			SUM(u.hdd) as hdd,

			SUM(u.total_room_night) as total_room_night,

			((SUM(u.total_room_night)/(s.rooms_keys*IF(u.year_id % 4 = 0, 366, 365))) * 100) as occupancy,

			SUM(u.total_electricity_kwh - onsite_generators_quantity) as total_electricity_kwh,

			u.year_id as year_id,

			s.rooms_keys

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



	$query = "SELECT (u.total_electricity_kwh - u.onsite_generators_quantity) as electricity,u.total_electricity_cost as electricity_cost,

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



	$query = "SELECT SUM(u.total_electricity_kwh - onsite_generators_quantity) as electricity,SUM(u.total_electricity_cost) as electricity_cost,

			    SUM(u.total_fuel_oil) as fuel,SUM(u.total_fuel_oil_cost) as fuel_cost,

			    SUM(u.total_lpg) as lpg,SUM(u.total_lpg_cost) as lpg_cost,

			    SUM(u.total_natural_gas) as natural_gas,SUM(u.total_natural_gas_cost) as natural_gas_cost,

			    SUM(u.district_heating) as heating_district,SUM(u.district_heating_cost) as heating_district_cost,

			    SUM(u.district_cooling) as cooling_district,SUM(u.district_cooling_cost) as cooling_district_cost,

			    SUM(u.district_heating_fixed_cost) as district_heating_fixed_cost,

			    SUM(u.district_cooling_fixed_cost) as district_cooling_fixed_cost,

			    SUM(u.lpg_fixed_cost) as lpg_fixed_cost,

			    SUM(u.natural_gas_fixed_cost) as natural_gas_fixed_cost,

			    SUM(u.water_fixed_cost) as water_fixed_cost,

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

	if ($site_filters['region_id'] != '') {

	    $this->db->where('s.region_id', $site_filters['region_id']);
	}
	if ($site_filters['site_type'] == 1 || $site_filters['site_type'] == 2 || $site_filters['site_type'] == 5 || $site_filters['site_type'] == 4) {
	    $this->db->where('s.site_type', $site_filters['site_type']);
	} else if ($site_filters['site_type'] == 3) {

	    $this->db->where_in('s.id', $site_filters['site_ids']);
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

			    $value1['title'] = $value1['title'] . GetSiteUtilityUnitName($value1['site_id'], $value['title']);

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



	$query = "SELECT u.site_id,u.month_id,u.year_id,u.hdd,u.cdd,u.total_room_night,

			u.district_cooling as district_cooling_actual,

			u.district_cooling_total_budget as district_cooling_budget,

			u.district_cooling_cost as district_cooling_cost_actual,

			u.district_cooling_total_budget_cost as district_cooling_cost_budget,

			u.district_heating as district_heating_actual,

			u.district_heating_total_budget as district_heating_budget,

			u.district_heating_cost as district_heating_cost_actual,

			u.district_heating_total_budget_cost as district_heating_cost_budget,

			(u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh_actual,

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

			    $value1['title'] = $value1['title'] . GetSiteUtilityUnitName($value1['site_id'], $value['title']);

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

				cdd,

				hdd,

				(u.water_total_consumption_budget_cost+u.total_consumption_breakdown_budget_cost+u.fuel_total_budget_cost+u.lpg_total_budget_cost+u.natural_gas_total_budget_cost+u.district_heating_total_budget_cost+u.district_cooling_total_budget_cost+u.electricity_total_budget_cost) as total_budget,

				u.total_purchased_electricity,

				u.total_purchased_electricity_cost,

				(u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh,

			    month_id,year_id,total_room_night,s.rooms_keys,s.site_type

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

	    (u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh,

	    month_id,year_id,total_room_night,s.rooms_keys,s.site_type

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
	    $query = "SELECT SUM(total_electricity_kwh - onsite_generators_quantity) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

			    SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

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



	$query = "SELECT SUM(total_electricity_kwh - onsite_generators_quantity) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

			SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

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



	$query = "SELECT (total_electricity_cost) as electricity, (total_fuel_oil_cost) as fuel, (total_lpg_cost) as lpg, (total_natural_gas_cost) as natural_gas, (district_heating_cost) as heating_district, (district_heating_fixed_cost) as district_heating_fixed_cost, (district_cooling_fixed_cost) as district_cooling_fixed_cost, (lpg_fixed_cost) as lpg_fixed_cost, (natural_gas_fixed_cost) as natural_gas_fixed_cost, (water_fixed_cost) as water_fixed_cost, (district_cooling_cost) as cooling_district, (water_total_consumption_cost) as water

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



	$query = "SELECT SUM(total_electricity_kwh - onsite_generators_quantity) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

			SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

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
	    $query = "SELECT SUM(total_electricity_kwh - onsite_generators_quantity) as electricity, SUM(district_heating) as heating_district, SUM(district_cooling) as cooling_district,

			    SUM(total_fuel_oil) as fuel,SUM(total_lpg) as lpg,SUM(total_natural_gas) as natural_gas

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

			    (u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh,

			month_id,year_id,total_room_night,s.rooms_keys,s.site_type

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

			    (u.total_electricity_kwh - u.onsite_generators_quantity) as total_electricity_kwh,

			month_id,year_id,total_room_night,s.rooms_keys,s.site_type

		    FROM {$this->_tbl_utilities} as u

		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id

		    WHERE site_id={$filters['site_id']} AND {$where_more} AND year_id != 0 AND month_id != 0 ";
	$result = $this->db->query($query);
	$result = $result->result_array();

	return $this->fetchUpdatedRoomKeys($result);
    }

    function monthlyUtilityProgress($filters = array())
    {
	$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'electricity');
	$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'fuel_oil');
	$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'lpg');
	$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'natural_gas');
	$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_heating');
	$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_cooling');
	$water_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'water');

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

	$query = "SELECT ((u.total_electricity_kwh * " . $electricity_mmbtu_rate . ") +
			(u.district_heating * " . $heating_district_mmbtu_rate . ") +
			(u.district_cooling * " . $cooling_district_mmbtu_rate . ") +
			(u.total_fuel_oil * " . $fuel_mmbtu_rate . ") +
			(u.total_lpg * " . $lpg_mmbtu_rate . ") +
			(u.total_natural_gas * " . $natural_gas_mmbtu_rate . ") - u.onsite_generators_quantity) as energy,
			(u.total_renewable_energy_production) as onsite_energy_generator_quantity,
			(u.water_total_consumption * " . $water_mmbtu_rate . ") as water,
			s.site_builtup_area,s.cooled_builtup_area,u.month_id,u.year_id,site_id
		    FROM {$this->_tbl_utilities} as u
		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0
		    ORDER BY year_id ASC,month_id ASC,u.id ASC";

	$result = $this->db->query($query);
	$result = $result->result_array();
	return $this->fetchUpdatedRoomKeys($result);

    }

    function monthlyUtilityProgressOnTarget($filters = array())
    {
	$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'electricity');
	$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'fuel_oil');
	$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'lpg');
	$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'natural_gas');
	$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_heating');
	$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_cooling');
	$water_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'water');

	// For Jan month of new year
	$default_pre_month = date('n') - 1;
	$default_year = date('Y');
	$default_pre_year = date('Y') - 1;
	if($default_pre_month == 0) {
	    $default_pre_month = 12;
	    $default_year = date('Y') - 1;
	    $default_pre_year = date('Y') - 2;
	}

	$start_year = isset($filters['Progress_start_year']) ? $filters['Progress_start_year'] : $default_year;
	$pre_start_year = isset($filters['Progress_previous_year']) ? $filters['Progress_previous_year'] : $default_pre_year;
	$pre_month = isset($filters['Progress_previous_month']) ? $filters['Progress_previous_month'] : $default_pre_month;

	$where_more = '';
	$where_more = "(
				    (year_id='$start_year' AND (month_id>=1 AND month_id<='$pre_month'))
				    OR
				    (year_id='$pre_start_year' AND (month_id>=1 AND month_id<='$pre_month'))
				)";

	$query = "SELECT ((u.total_electricity_kwh * " . $electricity_mmbtu_rate . ") +
			(u.district_heating * " . $heating_district_mmbtu_rate . ") +
			(u.district_cooling * " . $cooling_district_mmbtu_rate . ") +
			(u.total_fuel_oil * " . $fuel_mmbtu_rate . ") +
			(u.total_lpg * " . $lpg_mmbtu_rate . ") +
			(u.total_natural_gas * " . $natural_gas_mmbtu_rate . ") - u.onsite_generators_quantity) as energy,
			(u.total_renewable_energy_production) as onsite_energy_generator_quantity,
			(u.water_total_consumption * " . 1 . ") as water,
			s.site_builtup_area,s.cooled_builtup_area,u.month_id,u.year_id
		    FROM {$this->_tbl_utilities} as u
		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
		    WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0
		    ORDER BY year_id ASC,month_id ASC,u.id ASC";
		$result = $this->db->query($query);
		$result = $result->result_array();
	return $this->fetchUpdatedRoomKeys($result);
	}

	function groupUtilityChart()
	{
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_cooling');
		$water_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'water');

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
						(year_id='$startYear' AND (month_id>='$startMonth'))
						OR
						(year_id='$endYear' AND (month_id<='$endMonth'))
						)";

		$query = "SELECT ((u.total_electricity_kwh * " . $electricity_mmbtu_rate . ") - u.onsite_generators_quantity) as electricity,
						((u.total_lpg * " . $lpg_mmbtu_rate . ") +
			    (u.total_natural_gas * " . $natural_gas_mmbtu_rate . ")) as gases,
			    ((u.district_heating * " . $heating_district_mmbtu_rate . ") +
			    (u.district_cooling * " . $cooling_district_mmbtu_rate . ") +
			    (u.total_fuel_oil * " . $fuel_mmbtu_rate . ") +
						(u.water_total_consumption * " . $water_mmbtu_rate . ")) as others,
			    s.cooled_builtup_area,u.month_id,u.year_id
			FROM {$this->_tbl_utilities} as u
			LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
			WHERE site_id={$this->site_id} AND {$where_more} AND year_id != 0 AND month_id != 0
			ORDER BY year_id ASC,month_id ASC,u.id ASC";
	$result = $this->db->query($query);
	$result = $result->result_array();
	return $this->fetchUpdatedRoomKeys($result);
    }


	function fetchReferenceYearEnergyTarget($filterTargetMonthly) {
		$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'electricity');
		$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'fuel_oil');
		$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'lpg');
		$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'natural_gas');
		$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_heating');
		$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_cooling');

		$query = "SELECT ((u.total_electricity_kwh * " . $electricity_mmbtu_rate . ") +
			(u.district_heating * " . $heating_district_mmbtu_rate . ") +
			(u.district_cooling * " . $cooling_district_mmbtu_rate . ") +
			(u.total_fuel_oil * " . $fuel_mmbtu_rate . ") +
			(u.total_lpg * " . $lpg_mmbtu_rate . ") +
			(u.total_natural_gas * " . $natural_gas_mmbtu_rate . ") - u.onsite_generators_quantity) as energy,
			s.cooled_builtup_area,u.month_id,u.year_id
		    FROM {$this->_tbl_utilities} as u
		    LEFT JOIN {$this->_tbl_sites} as s ON s.id=u.site_id
		    WHERE site_id={$this->site_id} AND year_id = {$filterTargetMonthly['year_id']} AND month_id != 0 AND year_id != 0
		    ORDER BY month_id ASC,u.id ASC";
			$result = $this->db->query($query);
			$result = $result->result_array();
			$data = $this->fetchUpdatedRoomKeys($result);
			foreach ($data as $key => $value) {
				$value['energyConverted'] = ($value['cooled_builtup_area'] != 0 && isset($value['energy']) && $value['energy'] != 0) ? $value['energy']/$value['cooled_builtup_area'] : 0;
				$referenceYearBudget[$value['month_id']] = $value;
			}
			return $referenceYearBudget;
	}

    function getPerformanceChartData($filters)
    {
	$electricity_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'electricity');
	$fuel_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'fuel_oil');
	$lpg_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'lpg');
	$natural_gas_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'natural_gas');
	$heating_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_heating');
	$cooling_district_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'district_cooling');
	$water_mmbtu_rate = getUtilityUnitFactorForConversion($this->site_id, 'water');
	$water_mmbtu_rate = 1;
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
	$this->utilities_model->utilities_year = $currentYear;
	$site_waste_utility = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
	$this->utilities_model->utilities_year = $currentYear - 1;
	$site_waste_previous_year_utility = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
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
		$totalUtilityCost = isset($result) ? $result['total_electricity_cost'] + $result['total_fuel_oil_cost'] + $result['total_lpg_cost'] + $result['total_natural_gas_cost'] + $result['district_heating_cost'] + $result['district_cooling_cost'] + $result['water_total_consumption_cost'] + $result['district_cooling_fixed_cost'] + $result['district_heating_fixed_cost'] + $result['lpg_fixed_cost'] + $result['natural_gas_fixed_cost'] + $result['water_fixed_cost'] : 0;

		//setting Utility Cost
		$performanceReportData['UtilityCost'][$result['month_id']][$result['year_id']] = isset($totalUtilityCost) && is_numeric($totalUtilityCost) && is_finite($totalUtilityCost) ? round($totalUtilityCost) : 0;

		//calculation for Utility Cost room night
		$performanceReportData['UtilityCostRoomNight'][$result['month_id']][$result['year_id']] = isset($result['total_room_night']) && $result['total_room_night'] != 0 ? round($totalUtilityCost / $result['total_room_night']) : 0;

		//calculation for Utility Cost intensity
		$performanceReportData['UtilityCostIntensity'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalUtilityCost / $site_detail['site_builtup_area']) : 0;

		// Calculation Utility Consumption per night room
		//taking value in a variable;
		$electricityKwh = (isset($result['total_electricity_kwh']) && $site_detail['show_utility_electricity'] == 1) ? $result['total_electricity_kwh'] : 0;
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
		$utilityConsumptionElectricityKwh = ($electricityKwh * $electricity_mmbtu_rate * $multiplyValue);
		isset($utilityConsumptionElectricityKwh) && is_numeric($utilityConsumptionElectricityKwh) && is_finite($utilityConsumptionElectricityKwh) ? $utilityConsumptionElectricityKwh : 0;
		$utilityConsumptionDistrictHeatingKwh = $districtHeatingKwh * $heating_district_mmbtu_rate * $multiplyValue;
		isset($utilityConsumptionDistrictHeatingKwh) && is_numeric($utilityConsumptionDistrictHeatingKwh) && is_finite($utilityConsumptionDistrictHeatingKwh) ? $utilityConsumptionDistrictHeatingKwh : 0;
		$utilityConsumptionDistrictCoolingKwh = $districtCoolingKwh * $cooling_district_mmbtu_rate * $multiplyValue;
		isset($utilityConsumptionDistrictCoolingKwh) && is_numeric($utilityConsumptionDistrictCoolingKwh) && is_finite($utilityConsumptionDistrictCoolingKwh) ? $utilityConsumptionDistrictCoolingKwh : 0;
		$utilityConsumptionFuelKwh = $fuelKwh * $fuel_mmbtu_rate * $multiplyValue;
		isset($utilityConsumptionFuelKwh) && is_numeric($utilityConsumptionFuelKwh) && is_finite($utilityConsumptionFuelKwh) ? $utilityConsumptionFuelKwh : 0;
		$utilityConsumptionLpgKwh = $lpgKwh * $lpg_mmbtu_rate * $multiplyValue;
		isset($utilityConsumptionLpgKwh) && is_numeric($utilityConsumptionLpgKwh) && is_finite($utilityConsumptionLpgKwh) ? $utilityConsumptionLpgKwh : 0;
		$utilityConsumptionNaturalGasKwh = $naturalGasKwh * $natural_gas_mmbtu_rate * $multiplyValue;
		isset($utilityConsumptionNaturalGasKwh) && is_numeric($utilityConsumptionNaturalGasKwh) && is_finite($utilityConsumptionNaturalGasKwh) ? $utilityConsumptionNaturalGasKwh : 0;

		$totalUtilityConsumption = ($utilityConsumptionElectricityKwh + $utilityConsumptionDistrictHeatingKwh + $utilityConsumptionDistrictCoolingKwh + $utilityConsumptionFuelKwh + $utilityConsumptionLpgKwh + $utilityConsumptionNaturalGasKwh) - $onsite_generators_quantityKWH;
		//Setting the Utility Consumption
		$performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id']] = round($totalUtilityConsumption);

		//Calculation of Utility Consumption Room night
		$performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][$result['year_id']] = isset($result['total_room_night']) && $result['total_room_night'] != 0 ? round($totalUtilityConsumptionRoomNight) : 0;

		//Calculation of Utility Consumption Intensity
		$performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalUtilityConsumptionBuildUp) : 0;

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

		//Calculation of Renewable energy generated Intensity Start.
		$performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][$result['year_id']] = isset($site_detail['site_builtup_area']) && $site_detail['site_builtup_area'] != 0 ? round($totalRenewableEnergy / $site_detail['site_builtup_area']) : 0;


		//calculation for Occupancy start
		$days_of_month = cal_days_in_month(CAL_GREGORIAN, $result['month_id'], $result['year_id']);
		$Occupancy = (isset($result['total_room_night']) && $result['total_room_night'] && $site_detail['rooms_keys'] != 0) ? (($result['total_room_night'] / ($site_detail['rooms_keys'] * $days_of_month)) * 100) : 0;
		// Add Occupancy for all utility chart types
		$performanceReportData['ScopeEmission'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['ScopeEmissionPerSquareFootage'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['RenewableEnergyGenerated'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['RenewableEnergyGeneratedIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityConsumption'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityConsumptionRoomNight'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityConsumptionIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCost'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCostRoomNight'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);
		$performanceReportData['UtilityCostIntensity'][$result['month_id']][$result['year_id'] . '_occupancy'] = round($Occupancy);

		// Add budget calculations for new performance chart Budget v\s Total utility cost
		if ($filters['performance_chart_type'] == 'budget_vs_total_utility_cost') {
		    $totalBudgetCost = isset($result) ? $result['electricity_total_budget_cost'] + $result['fuel_total_budget_cost'] + $result['lpg_total_budget_cost'] + $result['natural_gas_total_budget_cost'] + $result['district_heating_total_budget_cost'] + $result['district_cooling_total_budget_cost'] + $result['water_total_consumption_budget_cost'] : 0;
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
		$data['y_axis'] = 'Kwh';
		$data['report_title'] = 'Total Energy Consumption';
		$data['unit'] = 'Kwh';
		break;

	    case 'utility_consumption_intesity_per_square_footage':
		$data['performanceReportArray'] = $performanceReportData['UtilityConsumptionIntensity'];
				$data['y_axis'] = 'Kwh';
		$data['report_title'] = 'Total Energy Consumption Intensity (per square '. getLocalUnitFullText($site_detail['id']).')';
		$data['unit'] = $site_detail['local_currency'] . '/' .  getLocalUnitText($site_detail['id']);
		break;

	    case 'utility_consumption_intensity_per_room_night':
		$data['performanceReportArray'] = $performanceReportData['UtilityConsumptionRoomNight'];
				$data['y_axis'] = 'Kwh';
		$data['report_title'] = 'Total Energy Consumption Intensity (per room-night)';
		$data['unit'] = $site_detail['local_currency'] . '/room-night)';
		break;

	    case 'diversion_rate':
		$site_waste_setting = isset($site_waste_setting) ? $site_waste_setting[0]['s'] : [];
		$checkTotalTrackedWaste =  $checkLandfillWaste = $reportData = [];
		foreach ($site_waste_setting as $key => $value) {
		    if (strpos($key, 'typical_destination_') !== false) {
			$name = substr($key, strpos($key, 'typical_destination_') + strlen('typical_destination_'));
			if ($site_waste_setting['monthly_tracking_' . $name] == 2) {
			    array_push($checkTotalTrackedWaste, 'unit_measure_' . $name);
			    if ($value == 1) {
				array_push($checkLandfillWaste, 'unit_measure_' . $name);
			    }
			}
		    }
		}
		foreach ($site_waste_utility as $key => $value) {
		    $monthlyTrackedTotalWaste = $monthlyTrackedLandfillWaste = 0;
		    foreach ($value['s'] as $keyValue => $data) {
			if (in_array($keyValue, $checkTotalTrackedWaste)) {
			    $monthlyTrackedTotalWaste = $monthlyTrackedTotalWaste + $data;
			}
			if (in_array($keyValue, $checkLandfillWaste)) {
			    $monthlyTrackedLandfillWaste = $monthlyTrackedLandfillWaste + $data;
			}
		    }
		    $performanceReportData['DiversionRate'][$value['s']['month_id']][$value['s']['year_id']] = calculatePercentage($monthlyTrackedLandfillWaste, $monthlyTrackedTotalWaste);
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
		$data['y_axis'] = '% Landfill waste compared to total waste';
		$data['report_title'] = 'Diversion Rate';
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

	    case ('food_and_beverage_waste' || 'food_and_beverage_waste_room_night' || 'food_and_beverage_waste_total_food_handled'):
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

	    case 'tonnes_of_carbon_offsets_purchased':
		break;

	    default:
		break;
	}
	return $data;
    }

    public function fetchUpdatedRoomKeys($result) {
	foreach ($result as $key => $value) {
	    foreach ($value as $subkey => $subvalue) {
		if($subkey == 'rooms_keys' || $subkey == 'cooled_builtup_area') {
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
