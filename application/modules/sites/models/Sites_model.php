<?php

/**
 *  Site Model
 *
 *  To perform queries related to Site management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright    (c) 2013, TatvaSoft
 * @author panks
 */
class Sites_model extends Base_Model
{

	protected $_tbl_sites                     = TBL_SITES;
	protected $_tbl_users                     = TBL_USERS;
	protected $_tbl_regions                   = TBL_REGIONS;
	protected $_tbl_countries                 = TBL_COUNTRIES;
	protected $_tbl_hotels                    = TBL_HOTELS;
	protected $_tbl_substations               = TBL_SUBSTATIONS;
	protected $_tbl_generators                = TBL_GENERATORS;
	protected $_tbl_hot_water_boilers         = TBL_HOT_WATER_BOLILERS;
	protected $_tbl_steam_boilers             = TBL_STEAM_BOLILERS;
	protected $_tbl_renewable_energy_info     = TBL_RENEWABLE_ENERGY_INFO;
	protected $_tbl_site_notifications        = TBL_SITE_NOTIFICATIONS;
	protected $_tbl_site_custom_notifications = TBL_SITE_CUSTOM_NOTIFICATIONS;
	protected $_tbl_energy_modelling          = 'energy_modelling';
	protected $_tbl_site_measures_reading     = 'site_measures_reading';
	protected $_tbl_site_measures             = 'measures';
	protected $_tbl_site_cron_settings        = 'site_cron_settings';
	protected $_tbl_site_area_update_history  = TBL_SITE_AREA_UPDATE_HISTORY;
	public $search_term                       = "";
	public $sort_by                           = "";
	public $sort_order                        = "";
	public $_record_count;
	public $year = "";

	public function get_site_listing($site_id, $role_id, $user_id)
	{
		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}
		$site_ids = [];
		if ($role_id == 6) {
			$site_ids = $this->get_regional_sites_for_corporate_user();
		}
		if ($role_id == 2) {
			$siteList = $this->get_site_listing_for_users($site_id, $role_id, $user_id);
			$site_ids = array_map(function($item) {
				return $item['s']['id'];
			}, $siteList);
		}

		$this->db->select('s.*, r.region_name,h.hotel_name,c.country');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->join($this->_tbl_regions . ' as r', 's.region_id = r.id', 'left');
		$this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');
		$this->db->join($this->_tbl_hotels . ' as h', 's.hotel_id = h.id', 'left');
		$this->db->where('s.status !=', -1);
		if ($role_id != 1 && $role_id != 6 && $role_id != 2) {
			$this->db->where('s.id', $site_id);
		}
		if ($role_id == 6 || $role_id == 2) {
			$this->db->where_in('s.id', $site_ids);
		}
		$query = $this->db->get();

		if (isset($this->_record_count) && $this->_record_count == true) {
			return count($this->db->custom_result($query));
		} else {
			$siteArray = $this->getSiteListAreaFormat($this->db->custom_result($query));
			// return $this->db->custom_result($query);
			return $siteArray;
		}
	}

	public function get_site_listing_for_users($site_id, $role_id, $user_id = 0)
	{
		if ($role_id == 2) {
			$site_id = array();
			$this->db->select('*');
			$this->db->from('user_sites');
			$this->db->where('user_id', $user_id);

			$query        = $this->db->get();
			$site_results = $query->result_array();

			if (!empty($site_results)) {
				foreach ($site_results as $result) {
					$site_id[] = $result['site_id'];
				}
			}
		}
		if ($role_id == 6) {
			$site_id = $this->get_regional_sites_for_corporate_user();
		}

		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		} else {
			$this->db->order_by('s.site_location_name', 'asc');
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$this->db->select('s.id,s.site_location_name');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status', 1);

		if ($role_id != 1 && $role_id != 2 && $role_id != 6) {
			$this->db->where('s.id', $site_id);
		}

		if ($role_id != 1) {
			if (is_array($site_id)) {
				$this->db->where_in('s.id', $site_id);
			} else {
				$this->db->where('s.id', $site_id);
			}
		}

		$query = $this->db->get();

		return $this->db->custom_result($query);
	}

	public function get_site_list_helper()
	{
		$this->db->select('s.id,s.site_location_name');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status !=', -1);
		$result = $this->db->get();
		$return = array();
		if ($result->num_rows() > 0) {
			foreach ($result->result_array() as $value) {
				$return[$value['id']] = $value['site_location_name'];
			}
		}

		return $return;
	}

	/**
	 * Function get_site_last_import_date to get last updated Import Utility & Waste
	 */
	public function get_site_last_import_date($site_id, $module_name) {
		$this->db->select('created as Date')
         ->from('audit_trail')
         ->where('site_id', $site_id)
         ->where('module_name', $module_name)
         ->where('action', 'Import')
         ->order_by('created', 'DESC')
         ->limit(1);

		$query = $this->db->get();
		$result = $query->row(); 
		return $result;
	}

	/**
	 * Function region_list to get listing of active regions
	 */
	public function region_list()
	{
		$this->db->select("id,region_name");
		$this->db->from($this->_tbl_regions);
		$this->db->where('status', 1);
		$this->db->order_by('region_name', 'asc');
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
			$result = $result->result_array();
			foreach ($result as $region) {
				$regions[$region['id']] = $region['region_name'];
			}
			return $regions;
		} else {
			return array();
		}
	}

	public function getAllUtility()
	{
		$this->db->select('*');
		$this->db->from('utilities_cost');
		$this->db->join('sites', 'utilities_cost.site_id = sites.id');
		$this->db->where('year_id !=', 0);
		$this->db->where('month_id !=', 0);
		$this->db->where('sites.status', 1);
		$this->db->where('year_id is NOT NULL', NULL, FALSE);
		$this->db->where('month_id is NOT NULL', NULL, FALSE);
		$this->db->order_by("site_location_name", "asc");
		$this->db->order_by("year_id", "asc");
		$this->db->order_by("month_id", "asc");

		$results = $this->db->get();
		$results = $results->result_array();
		foreach ($results as $key => $result) {
			$results[$key]['electricity_unit'] = GetSiteUtilityUnitName($result['site_id'], 'electricity');
			$results[$key]['fuel_oil_unit'] = GetSiteUtilityUnitName($result['site_id'], 'fuel_oil');
			$results[$key]['lpg_unit'] = GetSiteUtilityUnitName($result['site_id'], 'lpg');
			$results[$key]['water_unit'] = GetSiteUtilityUnitName($result['site_id'], 'water');
			$results[$key]['natural_gas_unit'] = GetSiteUtilityUnitName($result['site_id'], 'natural_gas');
			$results[$key]['district_cooling_unit'] = GetSiteUtilityUnitName($result['site_id'], 'district_cooling');
			$results[$key]['district_heating_unit'] = GetSiteUtilityUnitName($result['site_id'], 'district_heating');
		}
		return $results;
	}

	public function country_list()
	{
		$this->db->select("id,country");
		$this->db->from($this->_tbl_countries);
		$this->db->where('status', 1);
		$this->db->order_by('country', 'asc');
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
			$result = $result->result_array();
			foreach ($result as $country) {
				$countries[$country['id']] = $country['country'];
			}
			return $countries;
		} else {
			return array();
		}
	}

	public function hotel_list()
	{
		$this->db->select("id,hotel_name");
		$this->db->from($this->_tbl_hotels);
		$this->db->where('status', 1);
		$this->db->order_by('hotel_name', 'asc');
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
			$result = $result->result_array();
			foreach ($result as $hotel) {
				$hotels[$hotel['id']] = $hotel['hotel_name'];
			}
			return $hotels;
		} else {
			return array();
		}
	}

	/**
	 * Function get_site_detail to return site array of particular id
	 * @param integer $id
	 */
	public function get_site_detail($id = 0, $user_id, $role_id)
	{
		//Type Casting
		$id = intval($id);
		if (isset($this->site_id) && !empty($this->site_id) && $role_id != 1) {
			$this->db->where('id', $this->site_id);
		} else {
			$this->db->where("id", $id);
		}
		$this->db->where_in("status", array(1, 0));
		$tablesites = $this->db->get($this->_tbl_sites);
		$siteArray  = $tablesites->row_array();

		if (!empty($siteArray)) {
			$siteArray = $this->getEmissionFactorYearly($siteArray);
			$siteArray = $this->getSiteListAreaFormat($siteArray);
			$siteArray = $this->getSiteResidenceAllocation($siteArray);
			return $siteArray;
		} else {
			return array();
		}
	}

	public function getSiteResidenceAllocation($siteArray)
	{
		$consumption_constants = getConsumptionConstant();
		if (date("n") == 1) {
			$defaultYear = date('Y') - 1;
		} else {
			$defaultYear = date('Y');
		}
		$year = isset($this->year) && $this->year != 0 && !empty($this->year) ? $this->year : $defaultYear;
		$this->db->where("site_id", intval($siteArray['id']));
		$this->db->where("year_id", intval($year));
		$tablesites = $this->db->get('site_residence');
		$siteResidenceArray  = $tablesites->result_array();
		if (!empty($siteResidenceArray)) {
			foreach ($siteResidenceArray as $key => $value) {
				$siteArray['' . $value['utility_type'] . '_private_program_consumption'] = isset($value['private_program_consumption']) && !empty($value['private_program_consumption']) ? $consumption_constants[$value['private_program_consumption']] : '';
				$siteArray['' . $value['utility_type'] . '_private_program_fixed'] = isset($value['private_program_fixed']) && !empty($value['private_program_fixed']) ? $value['private_program_fixed'] : '';
				$siteArray['' . $value['utility_type'] . '_private_program_float'] = isset($value['private_program_float']) && !empty($value['private_program_float']) ? $value['private_program_float'] : '';
				$siteArray['' . $value['utility_type'] . '_private_program_hotel_connected'] = isset($value['private_program_hotel_connected']) && !empty($value['private_program_hotel_connected']) ? (($value['private_program_hotel_connected'] == 1) ? 'Yes' : 'No') : '';
				$siteArray['' . $value['utility_type'] . '_rental_program_residence_consumption'] = isset($value['rental_program_residence_consumption']) && !empty($value['rental_program_residence_consumption']) ? $consumption_constants[$value['rental_program_residence_consumption']] : '';
				$siteArray['' . $value['utility_type'] . '_rental_program_residence_fixed'] = isset($value['rental_program_residence_fixed']) && !empty($value['rental_program_residence_fixed']) ? $value['rental_program_residence_fixed'] : '';
				$siteArray['' . $value['utility_type'] . '_rental_program_residence_float'] = isset($value['rental_program_residence_float']) && !empty($value['rental_program_residence_float']) ? $value['rental_program_residence_float'] : '';
				$siteArray['' . $value['utility_type'] . '_rental_program_residence_hotel_connected'] = isset($value['rental_program_residence_hotel_connected']) && !empty($value['rental_program_residence_hotel_connected']) ? ($value['rental_program_residence_hotel_connected'] == 1 ? 'Yes' : 'No')  : '';
			}
		}
		return $siteArray;
	}

	public function get_site_detail_by_name($name = '')
	{
		$this->db->where("site_location_name", $name);
		$tablesites = $this->db->get($this->_tbl_sites);
		$siteArray  = $tablesites->row_array();

		if (!empty($siteArray)) {
			return $siteArray;
		} else {
			return array();
		}
	}

	public function get_site_detail_custom($id = 0)
	{
		//Type Casting
		$id = intval($id);
		$this->db->where("id", $id);
		$this->db->where_in("status", array(1, 0));
		$tablesites = $this->db->get($this->_tbl_sites);
		$siteArray  = $tablesites->row_array();

		if (!empty($siteArray)) {
			$siteArray = $this->getEmissionFactorYearly($siteArray);
			$siteArray = $this->getSiteListAreaFormat($siteArray);
			return $siteArray;
		} else {
			return array();
		}
	}

	public function getEmissionFactorYearly($siteArray)
	{
		foreach ($siteArray as $key => $value) {
			if (gettype($value) == 'array') {
				if (array_key_exists("s", $value)) {
					$site_id = $value['s']['id'];
				} else {
					$site_id = $value['id'];
				}
			} else {
				$site_id = $siteArray['id'];
			}
			if (isset($site_id) && !empty($site_id)) {
				if (date("n") == 1) {
					$defaultYear = date('Y') - 1;
				} else {
					$defaultYear = date('Y');
				}
				$year = isset($this->year) && $this->year != 0 && !empty($this->year) ? $this->year : $defaultYear;
				// echo $year;exit;
				$this->db->where("site_id", intval($siteArray['id']));
				$this->db->where("year_id", intval($year));
				$this->db->where("deleted_at is NULL");
				$this->db->where("deleted_by is NULL");
				$tablesites = $this->db->get('site_emission');
				$siteEmissionArray  = $tablesites->row_array();

				if (!empty($siteEmissionArray)) {
					$siteArray['electricity_emission_factor'] = isset($siteEmissionArray['electricity_emission_factor']) && isset($siteEmissionArray['electricity_emission_factor_percentage']) ? ((1 - ($siteEmissionArray['electricity_emission_factor_percentage'] / 100)) * $siteEmissionArray['electricity_emission_factor']) : $siteEmissionArray['electricity_emission_factor'];
					$siteArray['electricity_emission_factor_percentage'] = isset($siteEmissionArray['electricity_emission_factor_percentage']) ? $siteEmissionArray['electricity_emission_factor_percentage'] : 0;
					// $siteArray['electricity_emission_factor'] = isset($siteEmissionArray['electricity_emission_factor']) ? $siteEmissionArray['electricity_emission_factor'] : 0;
					$siteArray['fuel_emission_factor'] = isset($siteEmissionArray['fuel_emission_factor']) ? $siteEmissionArray['fuel_emission_factor'] : 0;
					$siteArray['lpg_emission_factor'] = isset($siteEmissionArray['lpg_emission_factor']) ? $siteEmissionArray['lpg_emission_factor'] : 0;
					$siteArray['natural_gas_emission_factor'] = isset($siteEmissionArray['natural_gas_emission_factor']) ? $siteEmissionArray['natural_gas_emission_factor'] : 0;
					$siteArray['district_cooling_emission_factor'] = isset($siteEmissionArray['district_cooling_emission_factor']) ? $siteEmissionArray['district_cooling_emission_factor'] : 0;
					$siteArray['district_heating_emission_factor'] = isset($siteEmissionArray['district_heating_emission_factor']) ? $siteEmissionArray['district_heating_emission_factor'] : 0;
				} else {
					$siteArray['electricity_emission_factor'] = 0;
					$siteArray['fuel_emission_factor'] = 0;
					$siteArray['lpg_emission_factor'] = 0;
					$siteArray['natural_gas_emission_factor'] = 0;
					$siteArray['district_cooling_emission_factor'] = 0;
					$siteArray['district_heating_emission_factor'] = 0;
				}
			}
		}
		return $siteArray;
	}

	/**
	 * Function delete_site to delete site
	 * @param integer $id
	 */
	public function delete_site($id)
	{
		//Type Casting
		$id = intval($id);
		$this->db->where('id', $id);
		$this->db->set('status', '-1');
		return $this->db->update($this->_tbl_sites);
	}

	/**
	 * Function save_site to add/update site
	 * @param array $data for site table
	 * @param array $data_profile for site_profile table
	 */
	public function save_site($data)
	{
		if (isset($data['id'])) {
			$site_data['id'] = $data['id'];
		}
		if (isset($data['hotel_name'])) {
			$site_data['hotel_name'] = $data['hotel_name'];
		}
		if (isset($data['site_location_name'])) {
			$site_data['site_location_name'] = $data['site_location_name'];
		}
		if (isset($data['residence_types'])) {
			$site_data['residence_types'] = $data['residence_types'];
		} else {
			$site_data['residence_types'] = NULL;
		}
		if (isset($data['site_location_latitude'])) {
			$site_data['site_location_latitude'] = $data['site_location_latitude'];
		}
		if (isset($data['site_location_longitude'])) {
			$site_data['site_location_longitude'] = $data['site_location_longitude'];
		}
		if (isset($data['station_id'])) {
			$site_data['station_id'] = $data['station_id'];
		}
		if (isset($data['base_cdd_temprature'])) {
			$site_data['base_cdd_temprature'] = $data['base_cdd_temprature'];
		}
		if (isset($data['base_hdd_temprature'])) {
			$site_data['base_hdd_temprature'] = $data['base_hdd_temprature'];
		}
		if (isset($data['region_id'])) {
			$site_data['region_id'] = $data['region_id'];
		}
		if (isset($data['country_id'])) {
			$site_data['country_id'] = $data['country_id'];
		}
		if (isset($data['hotel_id'])) {
			$site_data['hotel_id'] = $data['hotel_id'];
		}
		if (isset($data['site_type'])) {
			$site_data['site_type'] = $data['site_type'];
		}
		if (isset($data['attribute'])) {
			$site_data['attribute'] = $data['attribute'];
		}
		if (isset($data['residences_attribute'])) {
			$site_data['residences_attribute'] = $data['residences_attribute'];
		}
		if (isset($data['rental_program_attribute'])) {
			$site_data['rental_program_attribute'] = $data['rental_program_attribute'];
		}
		if (isset($data['employee_quarter_attribute'])) {
			$site_data['employee_quarter_attribute'] = $data['employee_quarter_attribute'];
		}
		if (isset($data['site_year_built'])) {
			$site_data['site_year_built'] = $data['site_year_built'];
		}
		if (isset($data['site_builtup_area'])) {
			$site_data['site_builtup_area'] = $data['site_builtup_area'];
		}
		if (isset($data['cooled_builtup_area'])) {
			$site_data['cooled_builtup_area'] = $data['cooled_builtup_area'];
		}
		if (isset($data['indoor_parking_area'])) {
			$site_data['indoor_parking_area'] = $data['indoor_parking_area'];
		}
		if (isset($data['rooms_keys'])) {
			$site_data['rooms_keys'] = $data['rooms_keys'];
		}
		if (isset($data['outdoor_pools'])) {
			$site_data['outdoor_pools'] = $data['outdoor_pools'];
		}
		if (isset($data['indoor_pools'])) {
			$site_data['indoor_pools'] = $data['indoor_pools'];
		}
		if (isset($data['laundry_type'])) {
			$site_data['laundry_type'] = $data['laundry_type'];
		}
		if (isset($data['laundry_fuel_type'])) {
			$site_data['laundry_fuel_type'] = $data['laundry_fuel_type'];
		}
		if (isset($data['is_chilled_water_system'])) {
			$site_data['is_chilled_water_system'] = $data['is_chilled_water_system'];
		}
		if (isset($data['chilled_water_system_type'])) {
			$site_data['chilled_water_system_type'] = $data['chilled_water_system_type'];
		}
		if (isset($data['chilled_water_system_total_rate'])) {
			$site_data['chilled_water_system_total_rate'] = $data['chilled_water_system_total_rate'];
		}
		if (isset($data['is_split_dx_unit'])) {
			$site_data['is_split_dx_unit'] = $data['is_split_dx_unit'];
		}
		if (isset($data['total_split_dx_unit'])) {
			$site_data['total_split_dx_unit'] = $data['total_split_dx_unit'];
		}
		if (isset($data['total_rate_split_dx_unit'])) {
			$site_data['total_rate_split_dx_unit'] = $data['total_rate_split_dx_unit'];
		}
		if (isset($data['is_vrv'])) {
			$site_data['is_vrv'] = $data['is_vrv'];
		}
		if (isset($data['total_vrv'])) {
			$site_data['total_vrv'] = $data['total_vrv'];
		}
		if (isset($data['total_vrv_unit'])) {
			$site_data['total_vrv_unit'] = $data['total_vrv_unit'];
		}
		if (isset($data['elcetrical_hw_total'])) {
			$site_data['elcetrical_hw_total'] = $data['elcetrical_hw_total'];
		}
		if (isset($data['elcetrical_hw_total_capacity'])) {
			$site_data['elcetrical_hw_total_capacity'] = $data['elcetrical_hw_total_capacity'];
		}
		if (isset($data['elcetrical_hw_total_power'])) {
			$site_data['elcetrical_hw_total_power'] = $data['elcetrical_hw_total_power'];
		}
		if (isset($data['is_ro_plant'])) {
			$site_data['is_ro_plant'] = $data['is_ro_plant'];
		}
		if (isset($data['ro_plant_capacity'])) {
			$site_data['ro_plant_capacity'] = $data['ro_plant_capacity'];
		}
		if (isset($data['is_renewable_energy'])) {
			$site_data['is_renewable_energy'] = $data['is_renewable_energy'];
		}
		if (isset($data['is_stp'])) {
			$site_data['is_stp'] = $data['is_stp'];
		}
		if (isset($data['stp_capacity'])) {
			$site_data['stp_capacity'] = $data['stp_capacity'];
		}
		if (isset($data['site_logo'])) {
			$site_data['site_logo'] = $data['site_logo'];
		}
		if (isset($data['site_color'])) {
			$site_data['site_color'] = $data['site_color'];
		}
		if (isset($data['electricity_emission_factor'])) {
			$site_data['electricity_emission_factor'] = $data['electricity_emission_factor'];
		}
		if (isset($data['fuel_emission_factor'])) {
			$site_data['fuel_emission_factor'] = $data['fuel_emission_factor'];
		}
		if (isset($data['lpg_emission_factor'])) {
			$site_data['lpg_emission_factor'] = $data['lpg_emission_factor'];
		}
		if (isset($data['natural_gas_emission_factor'])) {
			$site_data['natural_gas_emission_factor'] = $data['natural_gas_emission_factor'];
		}
		if (isset($data['district_cooling_emission_factor'])) {
			$site_data['district_cooling_emission_factor'] = $data['district_cooling_emission_factor'];
		}
		if (isset($data['district_heating_emission_factor'])) {
			$site_data['district_heating_emission_factor'] = $data['district_heating_emission_factor'];
		}

		if (isset($data['total_meeting_area'])) {
			$site_data['total_meeting_area'] = $data['total_meeting_area'];
		}
		if (isset($data['total_spa_area'])) {
			$site_data['total_spa_area'] = $data['total_spa_area'];
		}
		if (isset($data['room_area_rental_program'])) {
			$site_data['room_area_rental_program'] = $data['room_area_rental_program'];
		}
		if (isset($data['room_area_private_residence'])) {
			$site_data['room_area_private_residence'] = $data['room_area_private_residence'];
		}
		if (isset($data['hotel_rooms_area'])) {
			$site_data['hotel_rooms_area'] = $data['hotel_rooms_area'];
		}
		if (isset($data['residential_common_area'])) {
			$site_data['residential_common_area'] = $data['residential_common_area'];
		}
		if (isset($data['employee_living_quarters_area'])) {
			$site_data['employee_living_quarters_area'] = $data['employee_living_quarters_area'];
		}
		if (isset($data['f_b_service'])) {
			$site_data['f_b_service'] = $data['f_b_service'];
		}
		if (isset($data['restaurant_area'])) {
			$site_data['restaurant_area'] = $data['restaurant_area'];
		}
		if (isset($data['landscaped_area'])) {
			$site_data['landscaped_area'] = $data['landscaped_area'];
		}
		if (isset($data['comments'])) {
			$site_data['comments'] = $data['comments'];
		}
		if (isset($data['f_b_services_operated'])) {
			$site_data['f_b_services_operated'] = $data['f_b_services_operated'];
		}
		if (isset($data['f_b_services_outsourced'])) {
			$site_data['f_b_services_outsourced'] = $data['f_b_services_outsourced'];
		}
		if (isset($data['month_year_operation'])) {
			$site_data['month_year_operation'] = $data['month_year_operation'];
		}
		if (isset($data['vehicle_electric'])) {
			$site_data['vehicle_electric'] = $data['vehicle_electric'];
		}
		if (isset($data['vehicle_petrol'])) {
			$site_data['vehicle_petrol'] = $data['vehicle_petrol'];
		}
		if (isset($data['rental_program_residence'])) {
			$site_data['rental_program_residence'] = $data['rental_program_residence'];
		}
		if (isset($data['rental_private_residence'])) {
			$site_data['rental_private_residence'] = $data['rental_private_residence'];
		}
		if (isset($data['rental_program_residence_conditioned'])) {
			$site_data['rental_program_residence_conditioned'] = $data['rental_program_residence_conditioned'];
		}
		if (isset($data['rental_private_residence_conditioned'])) {
			$site_data['rental_private_residence_conditioned'] = $data['rental_private_residence_conditioned'];
		}
		if (isset($data['rental_program_residence_suites'])) {
			$site_data['rental_program_residence_suites'] = $data['rental_program_residence_suites'];
		}
		if (isset($data['rental_private_residence_suites'])) {
			$site_data['rental_private_residence_suites'] = $data['rental_private_residence_suites'];
		}
		if (isset($data['total_guest_room_area'])) {
			$site_data['total_guest_room_area'] = $data['total_guest_room_area'];
		}
		if (isset($data['calorifiers_unit'])) {
			$site_data['calorifiers_unit'] = $data['calorifiers_unit'];
		}
		if (isset($data['calorifiers_volume'])) {
			$site_data['calorifiers_volume'] = $data['calorifiers_volume'];
		}

		if (isset($data['chilled_water_system_type2'])) {
			$site_data['chilled_water_system_type2'] = $data['chilled_water_system_type2'];
		} else {
			$site_data['chilled_water_system_type2'] = '';
		}

		if (isset($data['chilled_water_system_total_rate2'])) {
			$site_data['chilled_water_system_total_rate2'] = $data['chilled_water_system_total_rate2'];
		} else {
			$site_data['chilled_water_system_total_rate2'] = null;
		}

		if (isset($data['show_utility_electricity'])) {
			$site_data['show_utility_electricity'] = $data['show_utility_electricity'];
		}

		if (isset($data['show_utility_fuel_oil'])) {
			$site_data['show_utility_fuel_oil'] = $data['show_utility_fuel_oil'];
		}
		if (isset($data['show_utility_lpg'])) {
			$site_data['show_utility_lpg'] = $data['show_utility_lpg'];
		}
		if (isset($data['show_utility_water'])) {
			$site_data['show_utility_water'] = $data['show_utility_water'];
		}
		if (isset($data['show_utility_irrigation_water'])) {
			$site_data['show_utility_irrigation_water'] = $data['show_utility_irrigation_water'];
		}
		if (isset($data['show_utility_natural_gas'])) {
			$site_data['show_utility_natural_gas'] = $data['show_utility_natural_gas'];
		}
		if (isset($data['show_utility_district_cooling'])) {
			$site_data['show_utility_district_cooling'] = $data['show_utility_district_cooling'];
		}
		if (isset($data['show_utility_district_heating'])) {
			$site_data['show_utility_district_heating'] = $data['show_utility_district_heating'];
		}
		if (isset($data['show_utility_district_heating_boiler'])) {
			$site_data['show_utility_district_heating_boiler'] = $data['show_utility_district_heating_boiler'];
		}
		if (isset($data['show_utility_water_waste'])) {
			$site_data['show_utility_water_waste'] = $data['show_utility_water_waste'];
		}
		if (isset($data['show_waste_management'])) {
			$site_data['show_waste_management'] = $data['show_waste_management'];
		}

		if (isset($data['show_total_utility_notification'])) {
			$site_data['show_total_utility_notification'] = $data['show_total_utility_notification'];
		}

		if (isset($data['baseline_regression_year'])) {
			$site_data['baseline_regression_year'] = $data['baseline_regression_year'];
		}

		if (isset($data['local_currency'])) {
			$site_data['local_currency'] = $data['local_currency'];
		}

		if (isset($data['local_unit'])) {
			$site_data['local_unit'] = $data['local_unit'];
		} else {
			$site_data['local_unit'] = 0;
		}

		if (isset($data['is_used_in_cron'])) {
			$site_data['is_used_in_cron'] = $data['is_used_in_cron'];
		}

		if (isset($data['threshold'])) {
			$site_data['threshold'] = $data['threshold'];
		}

		if (isset($data['chsb_reporting'])) {
			$site_data['chsb_reporting'] = $data['chsb_reporting'];
		}

		if (isset($data['chsb_segment'])) {
			$site_data['chsb_segment'] = $data['chsb_segment'];
		}

		if (isset($data['csr'])) {
			$site_data['csr'] = $data['csr'];
		}

		if (isset($data['daily_metering'])) {
			$site_data['daily_metering'] = $data['daily_metering'];
		}

		if (isset($data['is_hourly'])) {
			$site_data['is_hourly'] = $data['is_hourly'];
		}

		if (isset($data['energy_intensity_annual_target'])) {
			$site_data['energy_intensity_annual_target'] = $data['energy_intensity_annual_target'];
		}
		if (isset($data['ghg_intensity_annual_target'])) {
			$site_data['ghg_intensity_annual_target'] = $data['ghg_intensity_annual_target'];
		}
		if (isset($data['water_intensity_annual_target'])) {
			$site_data['water_intensity_annual_target'] = $data['water_intensity_annual_target'];
		}
		if (isset($data['waste_intensity_annual_target'])) {
			$site_data['waste_intensity_annual_target'] = $data['waste_intensity_annual_target'];
		}
		if (isset($data['energy_intensity_benchmark_target'])) {
			$site_data['energy_intensity_benchmark_target'] = $data['energy_intensity_benchmark_target'];
		}
		if (isset($data['ghg_intensity_benchmark_target'])) {
			$site_data['ghg_intensity_benchmark_target'] = $data['ghg_intensity_benchmark_target'];
		}
		if (isset($data['water_intensity_benchmark_target'])) {
			$site_data['water_intensity_benchmark_target'] = $data['water_intensity_benchmark_target'];
		}
		if (isset($data['waste_intensity_benchmark_target'])) {
			$site_data['waste_intensity_benchmark_target'] = $data['waste_intensity_benchmark_target'];
		}

		if (isset($data['status'])) {
			$site_data['status'] = $data['status'];
		}
		if (isset($data['utility_unit_electricity'])) {
			$site_data['utility_unit_electricity'] = $data['utility_unit_electricity'];
		}
		if (isset($data['utility_unit_fuel_oil'])) {
			$site_data['utility_unit_fuel_oil'] = $data['utility_unit_fuel_oil'];
		}
		if (isset($data['utility_unit_lpg'])) {
			$site_data['utility_unit_lpg'] = $data['utility_unit_lpg'];
		}
		if (isset($data['utility_unit_water'])) {
			$site_data['utility_unit_water'] = $data['utility_unit_water'];
		}
		if (isset($data['utility_unit_natural_gas'])) {
			$site_data['utility_unit_natural_gas'] = $data['utility_unit_natural_gas'];
		}
		if (isset($data['utility_unit_district_cooling'])) {
			$site_data['utility_unit_district_cooling'] = $data['utility_unit_district_cooling'];
		}
		if (isset($data['utility_unit_district_heating'])) {
			$site_data['utility_unit_district_heating'] = $data['utility_unit_district_heating'];
		}

		if (isset($site_data['id']) && $site_data['id'] != 0 && $site_data['id'] != "") {
			$this->db->set('modify_on', 'NOW()', false);
			$site_data['modify_by'] = $data['user_id'];
			$this->db->where('id', $site_data['id']);
			$this->db->update($this->_tbl_sites, $site_data);
			$id = $site_data['id'];
			$data_action = 'Update';
		} else {
			$site_data['modify_by']  = $data['user_id'];
			$site_data['created_by'] = $data['user_id'];
			$this->db->set('modify_on', 'NOW()', false);
			$this->db->set('created_on', 'NOW()', false);
			if ($this->db->insert($this->_tbl_sites, $site_data)) {
				$id = $this->db->insert_id();
			}
			$data_action = 'Create';
		}
		$site_id = $id;
		$user_id = $data['user_id'];
		$additional_fields = $data['hotel_name'] . ' (' . $data['site_location_name'] . ')';
		saveAuditTrail($user_id, $site_id, 'Site - ' . $additional_fields, $data_action);

		return $id;
	}

	public function save_substation($site_id, $substations)
	{
		$data['site_id'] = $site_id;
		// Delete not posted data
		$this->db->where_not_in('id', $substations['substation_id']);
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_substations);

		if (!empty($substations['substation_id'])) {
			foreach ($substations['substation_id'] as $key => $substation) {
				$data['substation_name']     = $substations['substation_name'][$key];
				$data['substation_quantity'] = $substations['substation_quantity'][$key];
				$data['substation_power']    = $substations['substation_power'][$key];
				if (!empty($substation)) {
					// Add new substation
					$this->db->where('id', $substation);
					$this->db->update($this->_tbl_substations, $data);
				} else {
					// Update substation
					$this->db->insert($this->_tbl_substations, $data);
				}
			}
		}
	}

	public function save_generator($site_id, $generators)
	{
		$data['site_id'] = $site_id;
		// Delete not posted data
		$this->db->where_not_in('id', $generators['generator_id']);
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_generators);

		if (!empty($generators['generator_id'])) {
			foreach ($generators['generator_id'] as $key => $generator) {
				$data['generator_name']     = $generators['generator_name'][$key];
				$data['generator_quantity'] = $generators['generator_quantity'][$key];
				$data['generator_power']    = $generators['generator_power'][$key];
				if (!empty($generator)) {
					// Add new generator
					$this->db->where('id', $generator);
					$this->db->update($this->_tbl_generators, $data);
				} else {
					// Update generator
					$this->db->insert($this->_tbl_generators, $data);
				}
			}
		}
	}

	public function save_hot_water_boiler($site_id, $hot_water_boilers)
	{
		$data['site_id'] = $site_id;
		// Delete not posted data
		$this->db->where_not_in('id', $hot_water_boilers['hot_water_boiler_id']);
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_hot_water_boilers);
		/* $hot_water_boiler_ids = implode(',', $hot_water_boilers['hot_water_boiler_id']);
	$this->db->query("DELETE FROM `hot_water_boilers` WHERE `id` NOT IN (" . $hot_water_boiler_ids . ") AND `site_id` =  " . $site_id); */

		if (!empty($hot_water_boilers['hot_water_boiler_id'])) {
			foreach ($hot_water_boilers['hot_water_boiler_id'] as $key => $hot_water_boiler) {
				$data['hot_water_boiler_name']     = $hot_water_boilers['hot_water_boiler_name'][$key];
				$data['hot_water_boiler_quantity'] = $hot_water_boilers['hot_water_boiler_quantity'][$key];
				$data['hot_water_boiler_power']    = $hot_water_boilers['hot_water_boiler_power'][$key];
				if (!empty($hot_water_boiler)) {
					$this->db->where('id', $hot_water_boiler);
					$this->db->update($this->_tbl_hot_water_boilers, $data);
				} else {
					$this->db->insert($this->_tbl_hot_water_boilers, $data);
				}
			}
		}
	}

	public function save_steam_boiler($site_id, $steam_boilers)
	{
		$data['site_id'] = $site_id;
		// Delete not posted data
		$this->db->where_not_in('id', $steam_boilers['steam_boiler_id']);
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_steam_boilers);

		if (!empty($steam_boilers['steam_boiler_id'])) {
			foreach ($steam_boilers['steam_boiler_id'] as $key => $steam_boiler) {
				$data['steam_boiler_name']     = $steam_boilers['steam_boiler_name'][$key];
				$data['steam_boiler_quantity'] = $steam_boilers['steam_boiler_quantity'][$key];
				$data['steam_boiler_power']    = $steam_boilers['steam_boiler_power'][$key];
				if (!empty($steam_boiler)) {
					$this->db->where('id', $steam_boiler);
					$this->db->update($this->_tbl_steam_boilers, $data);
				} else {
					$this->db->insert($this->_tbl_steam_boilers, $data);
				}
			}
		}
	}

	public function save_renewable_energy($site_id, $renewable_energys, $is_renewable_energy)
	{
		$data['site_id'] = $site_id;
		/* if (isset($is_renewable_energy) && $is_renewable_energy == 0) {
	$this->db->query("DELETE FROM `renewable_energy_info` WHERE `site_id` =  " . $site_id);
	}

	$renewable_energy_ids = implode(',', $renewable_energys['renewable_energy_id']);
	if (isset($renewable_energy_ids) &&  !in_array('',$renewable_energys['renewable_energy_id']) && !in_array(0,$renewable_energys['renewable_energy_id'])) {
	$this->db->query("DELETE FROM `renewable_energy_info` WHERE `id` NOT IN (" . $renewable_energy_ids . ") AND `site_id` =  " . $site_id);
	} */

		$this->db->where_not_in('id', $renewable_energys['renewable_energy_id']);
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_renewable_energy_info);

		if (!empty($renewable_energys['renewable_energy_id'])) {
			foreach ($renewable_energys['renewable_energy_id'] as $key => $renewable_energy) {
				$data['renewable_energy_type']     = $renewable_energys['renewable_energy_type'][$key];
				$data['renewable_energy_quantity'] = $renewable_energys['renewable_energy_quantity'][$key];
				$data['renewable_energy_capacity'] = $renewable_energys['renewable_energy_capacity'][$key];
				if (!empty($renewable_energy)) {
					$this->db->where('id', $renewable_energy);
					$this->db->update($this->_tbl_renewable_energy_info, $data);
				} else {
					$this->db->insert($this->_tbl_renewable_energy_info, $data);
				}
			}
		}
	}

	public function get_substations($site_id)
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_substations);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_generators($site_id)
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_generators);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_hot_water_boilers($site_id)
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_hot_water_boilers);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_steam_boilers($site_id)
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_steam_boilers);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_renewable_energys($site_id)
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_renewable_energy_info);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	/**
	 * Function inactive_records to inactive records
	 * @param array $id
	 */
	public function inactive_records($id = array())
	{
		$this->db->set('s.modify_on', 'NOW()', false);
		$this->db->set('s.status', 0);
		$this->db->where_in('s.id', $id);
		$this->db->update($this->_tbl_sites . ' AS s');
		/*$this->db->set('u.status', 0);
	$this->db->where_in('u.site_id', $id);
	$this->db->where('u.id !=', 1);
	$this->db->update($this->_tbl_users . ' AS u');*/
		return $id;
	}

	/**
	 * Function inactive_all_records to inactive all records without deleted records
	 */
	public function inactive_all_records($site_id, $role_id)
	{
		$this->db->set('s.modify_on', 'NOW()', false);
		$this->db->set('s.status', 0);
		$this->db->where('s.status !=', -1);

		if ($role_id != 1) {
			$this->db->where('s.id', $site_id);
		}

		$this->db->update($this->_tbl_sites . ' AS s');
		/*$this->db->set('u.status', 0);
	$this->db->where('u.site_id != ', 0);
	$this->db->where('u.role_id != ', 1);
	$this->db->where('u.role_id != ', 2);
	$this->db->where('u.status !=', -1);
	$this->db->update($this->_tbl_users . ' AS u');*/
		return true;
	}

	/**
	 * Function active_records to active records
	 * @param array $id
	 */
	public function active_records($id = array())
	{
		$this->db->set('s.modify_on', 'NOW()', false);
		$this->db->set('s.status', 1);
		$this->db->where_in('s.id', $id);
		$this->db->update($this->_tbl_sites . ' AS s');
		/*$this->db->set('u.status', 1);
	$this->db->where_in('u.site_id', $id);
	$this->db->where('u.id !=', 1);
	$this->db->update($this->_tbl_users . ' AS u');*/
		return $id;
	}

	/**
	 * Function active_all_records to active all records without deleted records
	 */
	public function active_all_records($site_id, $role_id)
	{
		$this->db->set('s.modify_on', 'NOW()', false);
		$this->db->set('s.status', 1);
		$this->db->where('s.status !=', -1);
		if ($role_id != 1) {
			$this->db->where('s.id', $site_id);
		}
		$this->db->update($this->_tbl_sites . ' As s');
		/*$this->db->set('u.status', 1);
	$this->db->where('u.site_id !=', 0);
	$this->db->where('u.role_id !=', 1);
	$this->db->where('u.role_id !=', 2);
	$this->db->where('u.status !=', -1);
	$this->db->update($this->_tbl_users . ' AS u');*/
		return true;
	}

	/**
	 * Function delete_records to delete URL
	 * @param integer $id
	 */
	public function delete_records($id = array())
	{
		$this->db->set('modify_on', 'NOW()', false);
		$this->db->where_in('id', $id);
		$this->db->set('status', '-1');
		$this->db->set('deleted_by', $this->user_id, false);
		$this->db->set('deleted_on', 'NOW()', false);
		return $this->db->update($this->_tbl_sites);
	}

	public function get_login_sites()
	{
		$this->db->select('s.id, site_location_name');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status =', 1);
		$this->db->order_by('s.site_location_name', 'asc');
		$query = $this->db->get();
		if (isset($this->_record_count) && $this->_record_count == true) {
			return count($query->result_array());
		} else {
			return $query->result_array();
		}
	}

	public function get_site_color_logo($site_id)
	{
		$this->db->select("site_logo,site_color");
		$this->db->from($this->_tbl_sites);
		$this->db->where('id', $site_id);
		$this->db->where('status !=', -1);
		$result = $this->db->get();
		return $result->row_array();
	}

	//Notification
	public function get_sites()
	{
		$this->db->select("id");
		$this->db->from($this->_tbl_sites);
		$this->db->where('status =', 1);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function save_default_notifications($site_id, $notifications)
	{
		if (!empty($notifications)) {
			$insert_data = array();
			if (!empty($notifications)) {
				foreach ($notifications as $key => $value) {
					$insert_data[] = array(
						'site_id'            => $site_id,
						'notification_title' => $key,
					);
				}
			}
			$this->db->insert_batch($this->_tbl_site_notifications, $insert_data);
		}
	}

	public function setSiteNotifications($site_id = 0, $notifications = array(), $user_id = 0)
	{
		// Delete old records
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_site_notifications);

		// Insert new records
		if (!empty($notifications)) {
			$insert_data = array();
			if (!empty($notifications)) {
				foreach ($notifications as $value) {
					$insert_data[] = array(
						'site_id'            => $site_id,
						'notification_title' => $value,
					);
				}
			}
			$this->db->insert_batch($this->_tbl_site_notifications, $insert_data);

			$this->db->where("id", $site_id);
			$this->db->where_in("status", array(1, 0));
			$tablesites = $this->db->get($this->_tbl_sites);
			$siteArray  = $tablesites->row_array();

			$data_action = 'Create';
			$additional_field = $siteArray['site_location_name'];
			saveAuditTrail($user_id, $site_id, 'Site - Set Notification - (' . $additional_field . ')', $data_action);
		}
		return true;
	}

	public function setSiteCustomNotifications($site_id = 0, $notifications = array())
	{
		// Delete old records
		$this->db->where('site_id', $site_id);
		$this->db->delete($this->_tbl_site_custom_notifications);

		// Insert new records
		if (!empty($notifications)) {
			$insert_data = array();
			if (!empty($notifications)) {
				foreach ($notifications as $value) {
					$insert_data[] = array(
						'site_id'      => $site_id,
						'notification' => $value['notification'],
						'ytd'          => $value['ytd'],
						'annual'       => $value['annual'],
						'date'         => (isset($value['date']) && $value['date'] != "") ? $value['date'] : date('Y-m-d'),
					);
				}
			}
			$this->db->insert_batch($this->_tbl_site_custom_notifications, $insert_data);
		}
		return true;
	}

	public function getSiteNotifications($site_id = 0)
	{
		$this->db->select("notification_title");
		$this->db->from($this->_tbl_site_notifications);
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function getSiteCustomNotifications($site_id = 0, $filterArray = array())
	{
		$this->db->select("*");
		$this->db->from($this->_tbl_site_custom_notifications);
		$this->db->where('site_id', $site_id);

		if (!empty($filterArray)) {
			if (isset($filterArray['month']) && !empty($filterArray['month'])) {
				$this->db->where('MONTH(date) =', $filterArray['month']);
			}
			if (isset($filterArray['year']) && !empty($filterArray['year'])) {
				$this->db->where('YEAR(date) =', $filterArray['year']);
			}
		}

		$result = $this->db->get();
		return $result->result_array();
	}

	public function daily_reading_utilities_list()
	{
		$this->db->select("*");
		$this->db->from('daily_reading_utilities');
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_daily_reading_settings($site_id)
	{
		$this->db->select('*');
		$this->db->from('daily_reading_utilities_titles');
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_all_daily_reading_settings()
	{
		$this->db->select('*');
		$this->db->from('daily_reading_utilities_titles');
		$result = $this->db->get();
		return $result->result_array();
	}

	public function save_daily_reading_settings($site_id = 0, $post_data = array())
	{
		if (empty($post_data) or empty($post_data['ids'])) {
			return true;
		}

		$data['site_id'] = $site_id;

		$this->db->where_not_in('id', $post_data['ids']);
		$this->db->where('site_id', $site_id);
		$this->db->delete('daily_reading_utilities_titles');

		foreach ($post_data['ids'] as $key => $id) {
			$data['utility_id']   = $post_data['utilities'][$key];
			$data['title']        = $post_data['titles'][$key];
			$data['hourly_title'] = $post_data['hourly_titles'][$key];

			$data['is_used_in_cron'] = 0;
			if (isset($post_data['is_used_in_cron'][$id]) || (isset($post_data['is_used_in_cron'][$key]) && !$id)) {
				$data['is_used_in_cron'] = 1;
			}

			if (!empty($id)) {
				$this->db->where('id', $id);
				$this->db->update('daily_reading_utilities_titles', $data);
			} else {
				$this->db->insert('daily_reading_utilities_titles', $data);
			}
		}
	}

	public function save_daily_reading_utilites_setting($site_id = 0, $post_data = array())
	{
		if (empty($post_data) or empty($post_data['is_used_in_cron'])) {
			return true;
		}

		$this->db->where('site_id', $site_id);
		$this->db->delete('utility_in_7days_cron');
		foreach ($post_data['is_used_in_cron'] as $key => $utility_id) {
			$data['site_id'] = $site_id;
			$data['utility_id'] = $utility_id;
			$this->db->insert('utility_in_7days_cron', $data);
		}
	}

	public function read_daily_reading_utilites_setting($site_id = 0)
	{
		$this->db->select("utility_id");
		$this->db->from('utility_in_7days_cron');
		$this->db->where('site_id', $site_id);
		$result = $this->db->get();
		$result = $result->result_array();
		$response = array();
		if ($result) {
			foreach ($result as $key => $value) {
				$response[] = $value['utility_id'];
			}
		}
		return $response;
	}

	public function get_site_detail_multiple($sites = array(), $filters = array())
	{
		// Filter sites if id passed
		if (!empty($sites)) {
			$this->db->where_in("id", $sites);
		}

		if (isset($filters['order_by']) && !empty($filters['order_by'])) {
			$order_by = $filters['order_by'];
			$order    = (isset($filters['order']) && !empty($filters['order'])) ? $filters['order'] : 'ASC';
			$this->db->order_by($order_by, $order);
		}

		if (isset($filters['region_id'])) {
			$this->db->where("region_id", $filters['region_id']);
		}

		$query     = $this->db->where("status", 1)->get($this->_tbl_sites);
		$siteArray = $query->result_array();
		if (!empty($siteArray)) {
			foreach($siteArray as $key => $site) {
				$siteArray[$key] = $this->getEmissionFactorYearly($site);
			}
			return $siteArray;
		} else {
			return array();
		}
	}

	public function get_energy_modelling($filterArray = array())
	{
		$energyData = array();

		$this->db->from($this->_tbl_energy_modelling);
		// $this->db->where('year_id =', $filterArray['year']);
		$this->db->where('site_id =', $filterArray['site_id']);
		$result      = $this->db->get();
		$resultArray = $result->result_array();
		if (!empty($resultArray)) {
			foreach ($resultArray as $energy) {
				$energyData[$energy['utility']] = [
					'cdd'       => $energy['cdd'],
					'hdd'       => $energy['hdd'],
					'occupancy' => $energy['occupancy'],
					'x'         => $energy['x'],
					'days'      => $energy['days'],
					'r2'      => $energy['r2'],
					'report'      => $energy['report'],
					'utility_unit_dropdown' => GetUtilityDropdownFromConstant($energy['utility']),
					'utility_unit_value' => GetSiteUtilityUnitValue($filterArray['site_id'], $energy['utility'])
				];
			}
		}
		$utility = ['electricity', 'fuel_oil', 'lpg', 'water', 'natural_gas', 'district_heating', 'district_cooling', 'steam_boiler', 'hot_water_boiler'];
		foreach ($utility as $key => $energy) {
			if (!isset($energyData[$energy]) && empty($energyData[$energy])) {
				$energyData[$energy] = [
					'cdd'       => 0,
					'hdd'       => 0,
					'occupancy' => 0,
					'x'         => 0,
					'days'      => 0,
					'r2'      => 0,
					'report'      => 0,
					'utility_unit_dropdown' => GetUtilityDropdownFromConstant($energy),
					'utility_unit_value' => 0
				];
			}
		}
		return $energyData;
	}

	public function save_energy_modelling($data)
	{
		$insertData = array();
		foreach ($data as $key => $value) {
			$updateData = array();
			$updateData = [
				'cdd'       => $value['cdd'],
				'hdd'       => $value['hdd'],
				'occupancy' => $value['occupancy'],
				'x'         => $value['x'],
				'days'      => $value['days'],
				'r2'      => $value['r2'],
				'report'      => $value['report'],
			];

			$this->db->from($this->_tbl_energy_modelling);
			// $this->db->where('year_id =', $value['year_id']);
			$this->db->where('site_id =', $value['site_id']);
			$this->db->where('utility =', $key);

			if ($this->db->count_all_results() > 0) {

				// $this->db->where('year_id =', $value['year_id']);
				$this->db->where('site_id =', $value['site_id']);
				$this->db->where('utility =', $key);
				$this->db->update($this->_tbl_energy_modelling, $updateData);
			} else {
				$value['utility'] = $key;
				$insertData[]     = $value;
			}
		}

		//batch insert data which is not present
		if (!empty($insertData)) {
			$this->db->insert_batch($this->_tbl_energy_modelling, $insertData);
		}
		return true;
	}
	public function get_measure_readings($site_id)
	{
		$this->db->select('r.*, m.title');
		$this->db->from($this->_tbl_site_measures_reading . ' as r');
		$this->db->join($this->_tbl_site_measures . ' as m', 'm.id = r.measure_id', 'left');
		$this->db->where('r.site_id', $site_id);
		$result = $this->db->get();
		return $result->result_array();
	}

	// get_all_hourly_reading_settings
	public function get_all_hourly_reading_settings()
	{
		$this->db->select('*');
		$this->db->from('daily_reading_utilities_titles');
		$this->db->where('hourly_title != ', '');
		$result = $this->db->get();
		return $result->result_array();
	}

	public function get_all_site_listing_for_users($site_id, $role_id, $user_id = 0)
	{

		if ($role_id == 2) {
			$site_id = array();
			$this->db->select('*');
			$this->db->from('user_sites');
			$this->db->where('user_id', $user_id);

			$query        = $this->db->get();
			$site_results = $query->result_array();

			if (!empty($site_results)) {
				foreach ($site_results as $result) {
					$site_id[] = $result['site_id'];
				}
			}
		}

		if ($role_id == 6) {
			$site_id = $this->get_regional_sites_for_corporate_user();
		}

		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$this->db->select('s.*');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status', 1);

		if ($role_id != 1 && $role_id != 2 && $role_id != 6) {
			$this->db->where('s.id', $site_id);
		}

		if ($role_id != 1) {
			if (is_array($site_id)) {
				$this->db->where_in('s.id', $site_id);
			} else {
				$this->db->where('s.id', $site_id);
			}
		}

		$query = $this->db->get();
		$data  = $query->result_array();
		return $data;
	}

	public function get_all_site_listing_for_users_orderby($site_id, $role_id, $user_id = 0)
	{

		if ($role_id == 2) {
			$site_id = array();
			$this->db->select('*');
			$this->db->from('user_sites');
			$this->db->where('user_id', $user_id);

			$query        = $this->db->get();
			$site_results = $query->result_array();

			if (!empty($site_results)) {
				foreach ($site_results as $result) {
					$site_id[] = $result['site_id'];
				}
			}
		}

		if ($role_id == 6) {
			$site_id = $this->get_regional_sites_for_corporate_user();
		}

		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$this->db->select('s.*');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status', 1);

		if ($role_id != 1 && $role_id != 2 && $role_id != 6) {
			$this->db->where('s.id', $site_id);
		}

		if ($role_id != 1) {
			if (is_array($site_id)) {
				$this->db->where_in('s.id', $site_id);
			} else {
				$this->db->where('s.id', $site_id);
			}
		}

		$this->db->order_by('s.site_location_name');
		$query = $this->db->get();
		$data  = $query->result_array();
		$data = $this->getEmissionFactorYearly($data);
		$data = $this->getSiteListAreaFormat($data);
		return $data;
	}

	public function getAllDailyReadingSettings($fields = "")
	{
		if (strlen($fields) != 0) {
			$this->db->select($fields);
		} else {
			$this->db->select('*');
		}
		$this->db->from('daily_reading_utilities_titles');
		$result = $this->db->get();
		return $result->result_array();
	}

	// getAllHourlyReadingSettings
	public function getAllHourlyReadingSettings($fields = "")
	{
		if (strlen($fields) != 0) {
			$this->db->select($fields);
		} else {
			$this->db->select('*');
		}
		$this->db->from('daily_reading_utilities_titles');
		$this->db->where('hourly_title != ', '');
		$result = $this->db->get();
		return $result->result_array();
	}

	public function deleteSiteCronSettings($region_id)
	{
		$sites = array();
		// $site_arr =array();
		$this->db->select('id');
		$this->db->where('region_id', $region_id);
		$result = $this->db->get($this->_tbl_sites);
		$result = $result->result_array();
		foreach ($result as $site) {
			$sites[] = $site['id'];
		}
		$site_arr = implode(",", array_filter($sites));
		// $this->db->where_in('site_id', $site_arr);
		$this->db->where("site_id IN (" . $site_arr . ")", NULL, false);
		$this->db->update($this->_tbl_site_cron_settings, ['deleted_at' => date('Y-m-d H:i:s')]);
	}

	public function getSiteCronSettings()
	{
		$this->db->select('*');
		$this->db->from($this->_tbl_site_cron_settings);
		$this->db->where('deleted_at', null);

		$query = $this->db->get();
		return $this->db->custom_result($query);
	}
	public function saveSiteCronSettings($data)
	{
		// $query = $this->db->get_where($this->_tbl_site_cron_settings,
		// array('cron_type'=>$data['cron_type'],'site_id'=>$data['site_id'], 'deleted_at' => NULL));
		// if ($query->num_rows() > 0) {

		// } else {
		if ($this->db->insert($this->_tbl_site_cron_settings, $data)) {
			$id = $this->db->insert_id();
		}
		// }
	}

	public function get_active_site_listing($site_id, $role_id, $region_id)
	{

		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$status = array(-1, 0);

		$this->db->select('s.*, r.region_name,h.hotel_name,c.country');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->join($this->_tbl_regions . ' as r', 's.region_id = r.id', 'left');
		$this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');
		$this->db->join($this->_tbl_hotels . ' as h', 's.hotel_id = h.id', 'left');
		$this->db->where_not_in('s.status', $status);
		if ($role_id != 1) {
			$this->db->where('s.id', $site_id);
		}
		$this->db->where('s.region_id', $region_id);
		$this->db->order_by('s.site_location_name', 'asc');
		$query = $this->db->get();

		if (isset($this->_record_count) && $this->_record_count == true) {
			return count($this->db->custom_result($query));
		} else {
			$siteArray = $this->getSiteListAreaFormat($this->db->custom_result($query));
			// return $this->db->custom_result($query);
			return $siteArray;
		}
	}

	/**
	 * Function region_list to get listing of active regions
	 */
	public function all_region_list()
	{
		$this->db->select("id,region_name");
		$this->db->from($this->_tbl_regions);
		$this->db->where('status', 1);
		$this->db->order_by('id', 'asc');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	/**
	 * Function get_site_detail to return site array of particular id
	 * @param integer $id
	 */
	public function get_site_detail_with_region_filter($id = 0, $user_id, $role_id, $region_ids)
	{
		//Type Casting
		$id = intval($id);
		if ($id != 0) {
			if (isset($this->site_id) && !empty($this->site_id) && $role_id != 1) {
				$this->db->where('id', $this->site_id);
			} else {
				$this->db->where("id", $id);
			}
		}
		$this->db->where_in("status", array(1, 0));
		if (!empty($region_ids)) {
			$region_arr = implode(",", array_filter($region_ids));
			$this->db->where("region_id IN (" . $region_arr . ")", NULL, false);
		}
		$tablesites = $this->db->get($this->_tbl_sites);
		if ($id != 0) {
			$siteArray  = $tablesites->row_array();
		} else {
			$siteArray  = $tablesites->result_array();
		}
		if (!empty($siteArray)) {
			$siteArray = $this->getEmissionFactorYearly($siteArray);
			$siteArray = $this->getSiteListAreaFormat($siteArray);
			return $siteArray;
		} else {
			return array();
		}
	}

	/**
	 * Function all sites with region filter
	 * @param integer $region_ids
	 */
	public function get_all_sites_with_region_filter($region_ids)
	{
		$this->db->select('id,site_location_name');
		$this->db->from($this->_tbl_sites);
		$this->db->where('status =', 1);
		if (!empty($region_ids)) {
			$region_arr = implode(",", array_filter($region_ids));
			$this->db->where("region_id IN (" . $region_arr . ")", NULL, false);
		}
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
			$result = $result->result_array();
			foreach ($result as $site) {
				if ($site['site_location_name'] != '') {
					$sites[$site['id']] = $site['site_location_name'];
				}
			}
			return $sites;
		} else {
			return NULL;
		}
	}

	public function get_all_site_listing_for_users_orderby_with_region($site_id, $role_id, $user_id = 0, $selected_region)
	{

		if ($role_id == 2) {
			$site_id = array();
			$this->db->select('*');
			$this->db->from('user_sites');
			$this->db->where('user_id', $user_id);

			$query        = $this->db->get();
			$site_results = $query->result_array();

			if (!empty($site_results)) {
				foreach ($site_results as $result) {
					$site_id[] = $result['site_id'];
				}
			}
		}

		if ($role_id == 6) {
			$site_id = $this->get_regional_sites_for_corporate_user();
		}

		if ($selected_region != '') {
			$this->db->where("region_id", $selected_region);
		}
		if ($this->search_term != "") {
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if ($this->sort_by != "" && $this->sort_order != "") {
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$this->db->select('s.*');
		$this->db->from($this->_tbl_sites . ' AS s');
		$this->db->where('s.status', 1);

		if ($role_id != 1 && $role_id != 2 && $role_id != 6) {
			$this->db->where('s.id', $site_id);
		}

		if ($role_id != 1 && !empty($site_id)) {
			if (is_array($site_id) && !empty($site_id)) {
				$this->db->where_in('s.id', $site_id);
			} else {
				$this->db->where('s.id', $site_id);
			}
		}

		$this->db->order_by('s.site_location_name');
		$query = $this->db->get();
		$data  = $query->result_array();
		foreach ($data as $key => $value) {
			if (date("n") == 1) {
				$defaultYear = date('Y') - 1;
			} else {
				$defaultYear = date('Y');
			}
			$year = isset($this->year) && $this->year != 0 && !empty($this->year) ? $this->year : $defaultYear;
			// echo $year;exit;
			$this->db->where("site_id", intval($value['id']));
			$this->db->where("year_id", intval($year));
			$this->db->where("deleted_at is NULL");
			$this->db->where("deleted_by is NULL");
			$tablesites = $this->db->get('site_emission');
			$siteEmissionArray  = $tablesites->row_array();
			$tablesites = $this->db->get('site_emission');
			$siteEmissionArray  = $tablesites->row_array();

			if (!empty($siteEmissionArray)) {
				$data[$key]['electricity_emission_factor'] = isset($siteEmissionArray['electricity_emission_factor']) && isset($siteEmissionArray['electricity_emission_factor_percentage']) ? ((1 - ($siteEmissionArray['electricity_emission_factor_percentage'] / 100)) * $siteEmissionArray['electricity_emission_factor']) : $siteEmissionArray['electricity_emission_factor'];
				$data[$key]['electricity_emission_factor_percentage'] = isset($siteEmissionArray['electricity_emission_factor_percentage']) ? $siteEmissionArray['electricity_emission_factor_percentage'] : 0;
				// $data[$key]['electricity_emission_factor'] = isset($siteEmissionArray['electricity_emission_factor']) ? $siteEmissionArray['electricity_emission_factor'] : 0;
				$data[$key]['fuel_emission_factor'] = isset($siteEmissionArray['fuel_emission_factor']) ? $siteEmissionArray['fuel_emission_factor'] : 0;
				$data[$key]['lpg_emission_factor'] = isset($siteEmissionArray['lpg_emission_factor']) ? $siteEmissionArray['lpg_emission_factor'] : 0;
				$data[$key]['natural_gas_emission_factor'] = isset($siteEmissionArray['natural_gas_emission_factor']) ? $siteEmissionArray['natural_gas_emission_factor'] : 0;
				$data[$key]['district_cooling_emission_factor'] = isset($siteEmissionArray['district_cooling_emission_factor']) ? $siteEmissionArray['district_cooling_emission_factor'] : 0;
				$data[$key]['district_heating_emission_factor'] = isset($siteEmissionArray['district_heating_emission_factor']) ? $siteEmissionArray['district_heating_emission_factor'] : 0;
			} else {
				$data[$key]['electricity_emission_factor'] = 0;
				$data[$key]['fuel_emission_factor'] = 0;
				$data[$key]['lpg_emission_factor'] = 0;
				$data[$key]['natural_gas_emission_factor'] = 0;
				$data[$key]['district_cooling_emission_factor'] = 0;
				$data[$key]['district_heating_emission_factor'] = 0;
			}
		}
		$data = $this->getSiteListAreaFormat($data);
		return $data;
	}

	public function updateSiteAreas($data, $site_location_name)
	{
		$ifEntryExist = $this->get_site_area_update_history($data['site_id'], $data);
		unset($ifEntryExist['unit']);
		if (isset($ifEntryExist) && !empty($ifEntryExist)) {
			if ($data['area_update_field'] == 'site_builtup_area') {
				$key = lang('total-built-up-area');
			} else if ($data['area_update_field'] == 'rooms_keys') {
				$key = lang('room-keys');
			} else if ($data['area_update_field'] == 'cooled_builtup_area') {
				$key = lang('cooled-built-up-area');
			} else if ($data['area_update_field'] == 'rental_program_residence') {
				$key = lang('rental-program-residence');
			} else if ($data['area_update_field'] == 'rental_program_residence_conditioned') {
				$key = lang('rental-program-residence-conditioned');
			} else if ($data['area_update_field'] == 'rental_private_residence') {
				$key = lang('rental-private-residence');
			} else if ($data['area_update_field'] == 'rental_private_residence_conditioned') {
				$key = lang('rental-private-residence-conditioned');
			} else if ($data['area_update_field'] == 'rental_program_residence_suites') {
				$key = lang('rental-program-residence-suites');
			} else if ($data['area_update_field'] == 'rental_private_residence_suites') {
				$key = lang('rental-private-residence-suites');
			}
			$id = $ifEntryExist[$key][0]['sa']['id'];
			$this->db->where(array('id' => $id));
			$this->db->set($data);
			$this->db->update($this->_tbl_site_area_update_history);
			$data_action = 'Update';
		} else {
			$this->db->insert($this->_tbl_site_area_update_history, $data);
			$id = $this->db->insert_id();
			$data_action = 'Create';
		}
		$site_id = $data['site_id'];
		$user_id = $data['user_id'];
		$additional_fields = ''.SITE_APPLICATION_NAME.' HOTELS AND RESORTS (' . $site_location_name . ')';
		saveAuditTrail($user_id, $site_id, 'Site Update Area - ' . $additional_fields, $data_action);

		return $id;
	}

	public function get_site_area_update_history($site_id, $extraWhere = [])
	{
		$this->db->select('sa.*,u.firstname');
		$this->db->where('sa.site_id', $site_id);
		if (isset($extraWhere) && !empty($extraWhere)) {
			if (isset($extraWhere['area_update_field'])) {
				$this->db->where('sa.area_update_field', $extraWhere['area_update_field']);
			}
			if (isset($extraWhere['area_update_date'])) {
				$year = date("Y", strtotime($extraWhere['area_update_date']));
				$month = date("m", strtotime($extraWhere['area_update_date']));
				$this->db->where('Year(sa.area_update_date)', $year);
				$this->db->where('Month(sa.area_update_date)', $month);
			}
		}
		$this->db->from($this->_tbl_site_area_update_history . ' AS sa');
		$this->db->join($this->_tbl_users . ' as u', 'sa.created_by = u.id', 'left');
		$this->db->order_by('sa.area_update_date', 'desc');
		$query = $this->db->get();
		$responseArray = $this->db->custom_result($query);
		$data['unit'] = ' (' . getLocalUnitText($site_id) . ') ';
		foreach ($responseArray as $key => $value) {
			if ($value['sa']['area_update_field'] == 'site_builtup_area') {
				$data[lang('total-built-up-area')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rooms_keys') {
				$data[lang('room-keys')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'cooled_builtup_area') {
				$data[lang('cooled-built-up-area')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_program_residence') {
				$data[lang('rental-program-residence')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_program_residence_conditioned') {
				$data[lang('rental-program-residence-conditioned')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_private_residence') {
				$data[lang('rental-private-residence')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_private_residence_conditioned') {
				$data[lang('rental-private-residence-conditioned')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_program_residence_suites') {
				$data[lang('rental-program-residence-suites')][$key] =  $value;
			} else if ($value['sa']['area_update_field'] == 'rental_private_residence_suites') {
				$data[lang('rental-private-residence-suites')][$key] =  $value;
			}
		}
		return $data;
	}

	public function getlatestSiteArea($data)
	{
		$responseArray = [];
		$this->db->select('sa.*');
		$this->db->where('sa.site_id', $data['site_id']);
		$this->db->where('sa.area_update_field', $data['area_update_field']);
		$this->db->from($this->_tbl_site_area_update_history . ' AS sa');
		$this->db->order_by('sa.created_at', 'desc');
		$query = $this->db->get();
		$responseArray = $query->row();
		// $responseArray = $this->db->custom_result($query);
		return json_decode(json_encode($responseArray), true);
	}

	public function getSiteListAreaFormat($siteArray)
	{
		foreach ($siteArray as $key => $value) {
			if (gettype($value) == 'array') {
				if (array_key_exists("s", $value)) {
					$site_id = $value['s']['id'];
				} else {
					$site_id = $value['id'];
				}
			} else {
				$site_id = $siteArray['id'];
			}
			if (isset($site_id) && !empty($site_id)) {
				$searchArray['site_id'] = $site_id;
				$searchArray['area_update_field'] = 'site_builtup_area';
				$dataSiteBuildUpArea = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'cooled_builtup_area';
				$dataSiteCooledBuildUpArea = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rooms_keys';
				$dataSiteRoomsKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_program_residence';
				$dataSiteRentalResidenceKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_program_residence_conditioned';
				$dataSiteRentalResidenceConditionedKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_private_residence';
				$dataSitePrivateResidenceKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_private_residence_conditioned';
				$dataSitePrivateResidenceConditionedKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_program_residence_suites';
				$dataSiteRentalResidenceSuiteKeys = $this->getlatestSiteArea($searchArray);
				$searchArray['area_update_field'] = 'rental_private_residence_suites';
				$dataSitePrivateResidenceSuiteKeys = $this->getlatestSiteArea($searchArray);
				if (isset($dataSiteBuildUpArea) && !empty($dataSiteBuildUpArea)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['site_builtup_area'] = $dataSiteBuildUpArea['area_update_value'];
						} else {
							$siteArray[$key]['site_builtup_area'] = $dataSiteBuildUpArea['area_update_value'];
						}
					} else {
						$siteArray['site_builtup_area'] = $dataSiteBuildUpArea['area_update_value'];
					}
				}
				if (isset($dataSiteCooledBuildUpArea) && !empty($dataSiteCooledBuildUpArea)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['cooled_builtup_area'] = $dataSiteCooledBuildUpArea['area_update_value'];
						} else {
							$siteArray[$key]['cooled_builtup_area'] = $dataSiteCooledBuildUpArea['area_update_value'];
						}
					} else {
						$siteArray['cooled_builtup_area'] = $dataSiteCooledBuildUpArea['area_update_value'];
					}
				}
				if (isset($dataSiteRoomsKeys) && !empty($dataSiteRoomsKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rooms_keys'] = $dataSiteRoomsKeys['area_update_value'];
						} else {
							$siteArray[$key]['rooms_keys'] = $dataSiteRoomsKeys['area_update_value'];
						}
					} else {
						$siteArray['rooms_keys'] = $dataSiteRoomsKeys['area_update_value'];
					}
				}
				if (isset($dataSiteRentalResidenceKeys) && !empty($dataSiteRentalResidenceKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_program_residence'] = $dataSiteRentalResidenceKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_program_residence'] = $dataSiteRentalResidenceKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_program_residence'] = $dataSiteRentalResidenceKeys['area_update_value'];
					}
				}
				if (isset($dataSiteRentalResidenceConditionedKeys) && !empty($dataSiteRentalResidenceConditionedKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_program_residence_conditioned'] = $dataSiteRentalResidenceConditionedKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_program_residence_conditioned'] = $dataSiteRentalResidenceConditionedKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_program_residence_conditioned'] = $dataSiteRentalResidenceConditionedKeys['area_update_value'];
					}
				}
				if (isset($dataSitePrivateResidenceKeys) && !empty($dataSitePrivateResidenceKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_private_residence'] = $dataSitePrivateResidenceKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_private_residence'] = $dataSitePrivateResidenceKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_private_residence'] = $dataSitePrivateResidenceKeys['area_update_value'];
					}
				}
				if (isset($dataSitePrivateResidenceConditionedKeys) && !empty($dataSitePrivateResidenceConditionedKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_private_residence_conditioned'] = $dataSitePrivateResidenceConditionedKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_private_residence_conditioned'] = $dataSitePrivateResidenceConditionedKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_private_residence_conditioned'] = $dataSitePrivateResidenceConditionedKeys['area_update_value'];
					}
				}
				if (isset($dataSiteRentalResidenceSuiteKeys) && !empty($dataSiteRentalResidenceSuiteKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_program_residence_suites'] = $dataSiteRentalResidenceSuiteKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_program_residence_suites'] = $dataSiteRentalResidenceSuiteKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_program_residence_suites'] = $dataSiteRentalResidenceSuiteKeys['area_update_value'];
					}
				}
				if (isset($dataSitePrivateResidenceSuiteKeys) && !empty($dataSitePrivateResidenceSuiteKeys)) {
					if (gettype($value) == 'array') {
						if (array_key_exists("s", $value)) {
							$siteArray[$key]['s']['rental_private_residence_suites'] = $dataSitePrivateResidenceSuiteKeys['area_update_value'];
						} else {
							$siteArray[$key]['rental_private_residence_suites'] = $dataSitePrivateResidenceSuiteKeys['area_update_value'];
						}
					} else {
						$siteArray['rental_private_residence_suites'] = $dataSitePrivateResidenceSuiteKeys['area_update_value'];
					}
				}
			}
		}
		return $siteArray;
	}

	public function updateTargets($dataInsert)
	{
		$id = intval($dataInsert['site_id']);
		unset($dataInsert['site_id']);
		$this->db->where('id', $id);
		foreach ($dataInsert as $key => $value) {
			$this->db->set($key, $value);
		}
		return $this->db->update($this->_tbl_sites);
	}

	public function get_regional_sites_for_corporate_user()
	{
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = isset($this->session->userdata[$this->section_name]['role_id'])
			? $this->session->userdata[$this->section_name]['role_id']
			: 6;
		$region_id = array();
		$this->db->select('*');
		$this->db->from('user_regions');
		$this->db->where('user_id', $user_id);

		$query        = $this->db->get();
		$site_results = $query->result_array();

		if (!empty($site_results)) {
			foreach ($site_results as $result) {
				$region_id[] = $result['region_id'];
			}
		}
		$regionalSitesData = $this->get_site_detail_with_region_filter(0, $user_id, $role_id, $region_id);
		$regionalSites = isset($regionalSitesData) ? array_column($regionalSitesData, 'id') : [];
		$site_id = array_values(array_unique($regionalSites));
		return $site_id;
	}
	public function getCarbonRecords($site_id, $site_detials)
	{
		// $site_detials = $this->sites_model->get_site_detail_custom($site_id);
		$dataFactor = getMmbtuFactorConversionAllUtility($site_id);

		/*Available utilies for site*/
		$this->utilities_model->utilities_month = date("n") - 1;
		$this->utilities_model->utilities_year  = date("Y");
		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year  = date("Y") - 1;
		}
		$this->utilities_model->site_id = $site_id;
		$getUtilities = $this->utilities_model->getUtility();
		$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
		$getUtilities['total_lpg_cost'] = ($getUtilities['total_lpg_cost'] != '') ? $getUtilities['total_lpg_cost'] : 0;
		$getUtilities['total_fuel_oil_cost'] = ($getUtilities['total_fuel_oil_cost'] != '') ? $getUtilities['total_fuel_oil_cost'] : 0;
		$getUtilities['total_natural_gas_cost'] = ($getUtilities['total_natural_gas_cost'] != '') ? $getUtilities['total_natural_gas_cost'] : 0;
		$getUtilities['district_heating_cost'] = ($getUtilities['district_heating_cost'] != '') ? $getUtilities['district_heating_cost'] : 0;
		$getUtilities['district_cooling_cost'] = ($getUtilities['district_cooling_cost'] != '') ? $getUtilities['district_cooling_cost'] : 0;
		$totalelectricitykwh = $getUtilities['total_electricity_kwh'] - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production'];
		$totalfueloil = $getUtilities['total_fuel_oil_cost']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
		$totalnaturalgas = $getUtilities['total_natural_gas_cost']; // - $getUtilities['onsite_generators_natural_gas_quantity'];
		$currentMonth_footPrint = ($dataFactor['electricity'] * $totalelectricitykwh * $site_detials['electricity_emission_factor']) + ($dataFactor['lpg'] * $getUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloil * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgas * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);

		$dataCarbon['carbon_footprint_currentMonth'] = $currentMonth_footPrint;
		$dataCarbon['total_utility_cost_currentMonth'] = $getUtilities['total_electricity_cost'] + $getUtilities['total_fuel_oil_cost'] + $getUtilities['total_lpg_cost'] + $getUtilities['total_natural_gas_cost'] + $getUtilities['district_heating_cost'] + $getUtilities['district_cooling_cost'] + $getUtilities['water_total_consumption_cost'] + $getUtilities['district_cooling_fixed_cost'] + $getUtilities['district_heating_fixed_cost'] + $getUtilities['lpg_fixed_cost'] + $getUtilities['natural_gas_fixed_cost'] + $getUtilities['water_fixed_cost'];

		$total_budgeted_cost_currentMonth = $getUtilities['electricity_total_budget_cost'] + $getUtilities['fuel_total_budget_cost'] + $getUtilities['lpg_total_budget_cost'] + $getUtilities['natural_gas_total_budget_cost'] + $getUtilities['district_heating_total_budget_cost'] + $getUtilities['district_cooling_total_budget_cost'] + $getUtilities['water_total_consumption_budget_cost'];

		$variation = ($dataCarbon['total_utility_cost_currentMonth'] != '' && $total_budgeted_cost_currentMonth != '') ? $total_budgeted_cost_currentMonth - $dataCarbon['total_utility_cost_currentMonth'] : 0;
		$dataCarbon['variation'] = $variation;
		$dataCarbon['variationPercentage'] = $dataCarbon['total_utility_cost_currentMonth'] != '' ? ($variation * 100) / $dataCarbon['total_utility_cost_currentMonth'] : 0;

		//same month previous year added by hp18
		$this->utilities_model->utilities_month = date("n") - 1;
		$this->utilities_model->utilities_year = date("Y") - 1;

		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year = date("Y") - 2;
		}
		$this->sites_model->year = $this->utilities_model->utilities_year;
		$this->utilities_model->site_id = $site_id;
		$utilitiesSameMonthPreviousYear = $this->utilities_model->getUtility();

		$utilitiesSameMonthPreviousYear['total_electricity_kwh'] = ($utilitiesSameMonthPreviousYear['total_electricity_kwh'] != '') ? $utilitiesSameMonthPreviousYear['total_electricity_kwh'] : 0;
		$utilitiesSameMonthPreviousYear['total_lpg'] = ($utilitiesSameMonthPreviousYear['total_lpg'] != '') ? $utilitiesSameMonthPreviousYear['total_lpg'] : 0;
		$utilitiesSameMonthPreviousYear['total_fuel_oil'] = ($utilitiesSameMonthPreviousYear['total_fuel_oil'] != '') ? $utilitiesSameMonthPreviousYear['total_fuel_oil'] : 0;
		$utilitiesSameMonthPreviousYear['total_natural_gas'] = ($utilitiesSameMonthPreviousYear['total_natural_gas'] != '') ? $utilitiesSameMonthPreviousYear['total_natural_gas'] : 0;
		$utilitiesSameMonthPreviousYear['district_heating'] = ($utilitiesSameMonthPreviousYear['district_heating'] != '') ? $utilitiesSameMonthPreviousYear['district_heating'] : 0;
		$utilitiesSameMonthPreviousYear['district_cooling'] = ($utilitiesSameMonthPreviousYear['district_cooling'] != '') ? $utilitiesSameMonthPreviousYear['district_cooling'] : 0;

		$totalelectricitykwhprev = $utilitiesSameMonthPreviousYear['total_electricity_kwh'] - $utilitiesSameMonthPreviousYear['onsite_generators_quantity'] - $utilitiesSameMonthPreviousYear['total_renewable_energy_production'];
		$totalfueloilprev = $utilitiesSameMonthPreviousYear['total_fuel_oil']; // - $utilitiesSameMonthPreviousYear['onsite_generators_fuel_oil_quantity'];
		$totalnaturalgasprev = $utilitiesSameMonthPreviousYear['total_natural_gas']; // - $utilitiesSameMonthPreviousYear['onsite_generators_natural_gas_quantity'];
		$SameMonthPreviousYear_footPrint = ($dataFactor['electricity'] * $totalelectricitykwhprev * $site_detials['electricity_emission_factor']) + ($dataFactor['lpg'] * $utilitiesSameMonthPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilprev * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgasprev * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $utilitiesSameMonthPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $utilitiesSameMonthPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);
		$dataCarbon['carbon_footprint_SameMonthPreviousYear'] = $SameMonthPreviousYear_footPrint;

		// YTD
		$ytd_carbon_footprint = $dataCarbon['carbon_footprint_currentMonth'];
		$ytd_carbon_footprintPreviousYear = 0;
		$total_utility_costs = $dataCarbon['total_utility_cost_currentMonth'];
		$total_budgeted_costs = $total_budgeted_cost_currentMonth;
		$currentMonth_footPrint_new = $baselineMonth_footPrint_new = 0;
		if (date("n") > 1) {

			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date('Y');
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			for ($i = 1; $i <= (date("n") - 1); $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y");

				$getUtilities = $this->utilities_model->getUtility();
				$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
				$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
				$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
				$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
				$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
				$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

				$totalelectricitykwhcurrentyear = $getUtilities['total_electricity_kwh'];
				if (isset($electricity_emission_factor_percentage) && !empty($electricity_emission_factor_percentage)) {
					$currentMonth_footPrint_new += ((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_emission_factor * $dataFactor['electricity']) + ($getUtilities['total_lpg'] * $site_detials['lpg_emission_factor'] * $dataFactor['lpg']) + ($dataFactor['fuel_oil'] * $getUtilities['total_fuel_oil'] * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $getUtilities['total_natural_gas'] * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				} else {
					$currentMonth_footPrint_new += ((((1) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $electricity_emission_factor * $dataFactor['electricity']) + ($getUtilities['total_lpg'] * $site_detials['lpg_emission_factor'] * $dataFactor['lpg']) + ($dataFactor['fuel_oil'] * $getUtilities['total_fuel_oil'] * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $getUtilities['total_natural_gas'] * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				}
			}
		} else {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date('Y') - 1;
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			for ($i = 1; $i <= 12; $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y") - 1;

				$getUtilities = $this->utilities_model->getUtility();

				$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
				$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
				$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
				$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
				$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
				$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

				$totalelectricitykwhcurrentyear = $getUtilities['total_electricity_kwh'];
				$totalfueloilcurrentyear = $getUtilities['total_fuel_oil']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
				$totalnaturalgascurrentyear = $getUtilities['total_natural_gas']; // - $getUtilities['onsite_generators_natural_gas_quantity'];
				if (isset($electricity_emission_factor_percentage) && !empty($electricity_emission_factor_percentage)) {
					$currentMonth_footPrint_new += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilcurrentyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgascurrentyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				} else {
					$currentMonth_footPrint_new += (((((1) * $totalelectricitykwhcurrentyear) - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilcurrentyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgascurrentyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
				}
			}
		}
		if(date("n") != 1) {
			$compareMonth = date("n");
		} else {
			$compareMonth = 12;
		}
		for ($i = 1; $i < ($compareMonth); $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = $site_detials['baseline_regression_year'];
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = $site_detials['baseline_regression_year'];

			$getUtilities = $this->utilities_model->getUtility();
			$getUtilities['total_electricity_kwh'] = ($getUtilities['total_electricity_kwh'] != '') ? $getUtilities['total_electricity_kwh'] : 0;
			$getUtilities['total_lpg'] = ($getUtilities['total_lpg'] != '') ? $getUtilities['total_lpg'] : 0;
			$getUtilities['total_fuel_oil'] = ($getUtilities['total_fuel_oil'] != '') ? $getUtilities['total_fuel_oil'] : 0;
			$getUtilities['total_natural_gas'] = ($getUtilities['total_natural_gas'] != '') ? $getUtilities['total_natural_gas'] : 0;
			$getUtilities['district_heating'] = ($getUtilities['district_heating'] != '') ? $getUtilities['district_heating'] : 0;
			$getUtilities['district_cooling'] = ($getUtilities['district_cooling'] != '') ? $getUtilities['district_cooling'] : 0;

			$totalelectricitykwhbaselineyear = $getUtilities['total_electricity_kwh'];
			$totalfueloilbaselineyear = $getUtilities['total_fuel_oil']; // - $getUtilities['onsite_generators_fuel_oil_quantity'];
			$totalnaturalgasbaselineyear = $getUtilities['total_natural_gas']; // - $getUtilities['onsite_generators_natural_gas_quantity'];
			$this->load->model('sites/sites_model');
			$this->sites_model->year = $site_detials['baseline_regression_year'];
			$site_detials = $this->sites_model->get_site_detail_custom($site_id);
			// echo '((((((1 - ('.$electricity_emission_factor_percentage.'/100)) *'. $totalelectricitykwhbaselineyear.')  - '.$getUtilities['onsite_generators_quantity'].' - '.$getUtilities['total_renewable_energy_production'].') * '.$dataFactor['electricity'].') * '.$electricity_emission_factor.') )<br/>';
			// echo ($dataFactor['natural_gas'].' * '.$totalnaturalgasbaselineyear.' * '.$site_detials['natural_gas_emission_factor']);
			if (isset($electricity_emission_factor_percentage) && !empty($electricity_emission_factor_percentage)) {
				$baselineMonth_footPrint_new += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhbaselineyear)  - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilbaselineyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgasbaselineyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$baselineMonth_footPrint_new += (((((1) * $totalelectricitykwhbaselineyear)  - $getUtilities['onsite_generators_quantity'] - $getUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $getUtilities['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilbaselineyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgasbaselineyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $getUtilities['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $getUtilities['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			}
		}
		for ($i = 1; $i < $compareMonth; $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date("Y") - 1;
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = date("Y") - 1;
			$this->sites_model->year = $this->utilities_model->utilities_year;
			$YtdUtilitiesPreviousYear = $this->utilities_model->getUtility();

			$YtdUtilitiesPreviousYear['total_electricity_kwh'] = ($YtdUtilitiesPreviousYear['total_electricity_kwh'] != '') ? $YtdUtilitiesPreviousYear['total_electricity_kwh'] : 0;
			$YtdUtilitiesPreviousYear['total_lpg'] = ($YtdUtilitiesPreviousYear['total_lpg'] != '') ? $YtdUtilitiesPreviousYear['total_lpg'] : 0;
			$YtdUtilitiesPreviousYear['total_fuel_oil'] = ($YtdUtilitiesPreviousYear['total_fuel_oil'] != '') ? $YtdUtilitiesPreviousYear['total_fuel_oil'] : 0;
			$YtdUtilitiesPreviousYear['total_natural_gas'] = ($YtdUtilitiesPreviousYear['total_natural_gas'] != '') ? $YtdUtilitiesPreviousYear['total_natural_gas'] : 0;
			$YtdUtilitiesPreviousYear['district_heating'] = ($YtdUtilitiesPreviousYear['district_heating'] != '') ? $YtdUtilitiesPreviousYear['district_heating'] : 0;
			$YtdUtilitiesPreviousYear['district_cooling'] = ($YtdUtilitiesPreviousYear['district_cooling'] != '') ? $YtdUtilitiesPreviousYear['district_cooling'] : 0;
			$YtdUtilitiesPreviousYear['water_total_consumption'] = ($YtdUtilitiesPreviousYear['water_total_consumption'] != '') ? $YtdUtilitiesPreviousYear['water_total_consumption'] : 0;

			$totalelectricitykwhpreviousyear = $YtdUtilitiesPreviousYear['total_electricity_kwh'];
			$totalfueloilpreviousyear = $YtdUtilitiesPreviousYear['total_fuel_oil'];
			$totalnaturalgaspreviousyear = $YtdUtilitiesPreviousYear['total_natural_gas'];
			if (isset($electricity_emission_factor_percentage) && !empty($electricity_emission_factor_percentage)) {
				$ytd_carbon_footprintPreviousYear += (((((1 - ($electricity_emission_factor_percentage / 100)) * $totalelectricitykwhpreviousyear)  - $YtdUtilitiesPreviousYear['onsite_generators_quantity'] - $YtdUtilitiesPreviousYear['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $YtdUtilitiesPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilpreviousyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgaspreviousyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $YtdUtilitiesPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $YtdUtilitiesPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$ytd_carbon_footprintPreviousYear += (((((1) * $totalelectricitykwhpreviousyear)  - $YtdUtilitiesPreviousYear['onsite_generators_quantity'] - $YtdUtilitiesPreviousYear['total_renewable_energy_production']) * $dataFactor['electricity']) * $electricity_emission_factor) + ($dataFactor['lpg'] * $YtdUtilitiesPreviousYear['total_lpg'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $totalfueloilpreviousyear * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $totalnaturalgaspreviousyear * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $YtdUtilitiesPreviousYear['district_heating'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $YtdUtilitiesPreviousYear['district_cooling'] * $site_detials['district_cooling_emission_factor']);
			}
		}

		for ($i = 1; $i <= date("n"); $i++) {
			$this->load->model('sites/site_emission_model');
			$this->site_emission_model->site_id = $site_id;
			$this->site_emission_model->year_id = date("Y");
			$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
			if (isset($site_emission) && !empty($site_emission)) {
				$electricity_emission_factor = $site_emission[0]['s']['electricity_emission_factor'];
				$electricity_emission_factor_percentage = $site_emission[0]['s']['electricity_emission_factor_percentage'];
			}
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = date("Y");
			$this->sites_model->year = $this->utilities_model->utilities_year;
			$YtdUtilities = $this->utilities_model->getUtility();

			$YtdUtilities['total_electricity_kwh'] = ($YtdUtilities['total_electricity_kwh'] != '') ? $YtdUtilities['total_electricity_kwh'] : 0;
			$YtdUtilities['total_lpg_cost'] = ($YtdUtilities['total_lpg_cost'] != '') ? $YtdUtilities['total_lpg_cost'] : 0;
			$YtdUtilities['total_fuel_oil_cost'] = ($YtdUtilities['total_fuel_oil_cost'] != '') ? $YtdUtilities['total_fuel_oil_cost'] : 0;
			$YtdUtilities['district_heating_cost'] = ($YtdUtilities['district_heating_cost'] != '') ? $YtdUtilities['district_heating_cost'] : 0;
			$YtdUtilities['district_cooling_cost'] = ($YtdUtilities['district_cooling_cost'] != '') ? $YtdUtilities['district_cooling_cost'] : 0;
			$YtdUtilities['water_total_consumption_cost'] = ($YtdUtilities['water_total_consumption_cost'] != '') ? $YtdUtilities['water_total_consumption_cost'] : 0;
			$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
			if (isset($electricity_emission_factor_percentage) && !empty($electricity_emission_factor_percentage)) {
				$ytd_carbon_footprint += (((((1 - ($electricity_emission_factor_percentage / 100)) * $YtdUtilities['total_electricity_kwh'])  - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $site_detials['electricity_emission_factor']) + ($dataFactor['lpg'] * $YtdUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $YtdUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $YtdUtilities['total_natural_gas_cost'] * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $YtdUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $YtdUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
			} else {
				$ytd_carbon_footprint += (((((1) * $YtdUtilities['total_electricity_kwh'])  - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']) * $dataFactor['electricity']) * $site_detials['electricity_emission_factor']) + ($dataFactor['lpg'] * $YtdUtilities['total_lpg_cost'] * $site_detials['lpg_emission_factor']) + ($dataFactor['fuel_oil'] * $YtdUtilities['total_fuel_oil_cost'] * $site_detials['fuel_emission_factor']) + ($dataFactor['natural_gas'] * $YtdUtilities['total_natural_gas_cost'] * $site_detials['natural_gas_emission_factor']) + ($dataFactor['district_heating'] * $YtdUtilities['district_heating_cost'] * $site_detials['district_heating_emission_factor']) + ($dataFactor['district_cooling'] * $YtdUtilities['district_cooling_cost'] * $site_detials['district_cooling_emission_factor']);
			}
			//For variation
			$total_utility_costs += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'];

			$YtdUtilities['electricity_total_budget_cost'] = ($YtdUtilities['electricity_total_budget_cost'] != '') ? $YtdUtilities['electricity_total_budget_cost'] : 0;
			$YtdUtilities['fuel_total_budget_cost'] = ($YtdUtilities['fuel_total_budget_cost'] != '') ? $YtdUtilities['fuel_total_budget_cost'] : 0;
			$YtdUtilities['lpg_total_budget_cost'] = ($YtdUtilities['lpg_total_budget_cost'] != '') ? $YtdUtilities['lpg_total_budget_cost'] : 0;
			$YtdUtilities['natural_gas_total_budget_cost'] = ($YtdUtilities['natural_gas_total_budget_cost'] != '') ? $YtdUtilities['natural_gas_total_budget_cost'] : 0;
			$YtdUtilities['district_heating_total_budget_cost'] = ($YtdUtilities['district_heating_total_budget_cost'] != '') ? $YtdUtilities['district_heating_total_budget_cost'] : 0;
			$YtdUtilities['district_cooling_total_budget_cost'] = ($YtdUtilities['district_cooling_total_budget_cost'] != '') ? $YtdUtilities['district_cooling_total_budget_cost'] : 0;
			$YtdUtilities['water_total_consumption_budget_cost'] = ($YtdUtilities['water_total_consumption_budget_cost'] != '') ? $YtdUtilities['water_total_consumption_budget_cost'] : 0;

			$total_budgeted_costs += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
		}

		//ytd variation
		if (date("n") > 1) {
			$total_utility_costs_variation = 0;
			$total_budgeted_costs_variation = 0;
			for ($i = 1; $i <= (date("n") - 1); $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y");

				$YtdUtilities = $this->utilities_model->getUtility();

				$YtdUtilities['total_electricity_cost'] = ($YtdUtilities['total_electricity_cost'] != '') ? $YtdUtilities['total_electricity_cost'] : 0;
				$YtdUtilities['total_fuel_oil_cost'] = ($YtdUtilities['total_fuel_oil_cost'] != '') ? $YtdUtilities['total_fuel_oil_cost'] : 0;
				$YtdUtilities['total_lpg_cost'] = ($YtdUtilities['total_lpg_cost'] != '') ? $YtdUtilities['total_lpg_cost'] : 0;
				$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
				$YtdUtilities['district_heating_cost'] = ($YtdUtilities['district_heating_cost'] != '') ? $YtdUtilities['district_heating_cost'] : 0;
				$YtdUtilities['total_natural_gas_cost'] = ($YtdUtilities['total_natural_gas_cost'] != '') ? $YtdUtilities['total_natural_gas_cost'] : 0;
				$YtdUtilities['district_cooling_cost'] = ($YtdUtilities['district_cooling_cost'] != '') ? $YtdUtilities['district_cooling_cost'] : 0;
				$YtdUtilities['water_total_consumption_cost'] = ($YtdUtilities['water_total_consumption_cost'] != '') ? $YtdUtilities['water_total_consumption_cost'] : 0;
				$YtdUtilities['district_cooling_fixed_cost'] = ($YtdUtilities['district_cooling_fixed_cost'] != '') ? $YtdUtilities['district_cooling_fixed_cost'] : 0;
				$YtdUtilities['district_heating_fixed_cost'] = ($YtdUtilities['district_heating_fixed_cost'] != '') ? $YtdUtilities['district_heating_fixed_cost'] : 0;
				$YtdUtilities['lpg_fixed_cost'] = ($YtdUtilities['lpg_fixed_cost'] != '') ? $YtdUtilities['lpg_fixed_cost'] : 0;
				$YtdUtilities['natural_gas_fixed_cost'] = ($YtdUtilities['natural_gas_fixed_cost'] != '') ? $YtdUtilities['natural_gas_fixed_cost'] : 0;
				$YtdUtilities['water_fixed_cost'] = ($YtdUtilities['water_fixed_cost'] != '') ? $YtdUtilities['water_fixed_cost'] : 0;

				$YtdUtilities['electricity_total_budget_cost'] = ($YtdUtilities['electricity_total_budget_cost'] != '') ? $YtdUtilities['electricity_total_budget_cost'] : 0;
				$YtdUtilities['fuel_total_budget_cost'] = ($YtdUtilities['fuel_total_budget_cost'] != '') ? $YtdUtilities['fuel_total_budget_cost'] : 0;
				$YtdUtilities['lpg_total_budget_cost'] = ($YtdUtilities['lpg_total_budget_cost'] != '') ? $YtdUtilities['lpg_total_budget_cost'] : 0;
				$YtdUtilities['natural_gas_total_budget_cost'] = ($YtdUtilities['natural_gas_total_budget_cost'] != '') ? $YtdUtilities['natural_gas_total_budget_cost'] : 0;
				$YtdUtilities['district_heating_total_budget_cost'] = ($YtdUtilities['district_heating_total_budget_cost'] != '') ? $YtdUtilities['district_heating_total_budget_cost'] : 0;
				$YtdUtilities['district_cooling_total_budget_cost'] = ($YtdUtilities['district_cooling_total_budget_cost'] != '') ? $YtdUtilities['district_cooling_total_budget_cost'] : 0;
				$YtdUtilities['water_total_consumption_budget_cost'] = ($YtdUtilities['water_total_consumption_budget_cost'] != '') ? $YtdUtilities['water_total_consumption_budget_cost'] : 0;

				//For variation
				$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'] + $YtdUtilities['district_cooling_fixed_cost'] + $YtdUtilities['district_heating_fixed_cost'] + $YtdUtilities['lpg_fixed_cost'] + $YtdUtilities['natural_gas_fixed_cost'] + $YtdUtilities['water_fixed_cost'];

				$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
			}
		} else {
			$total_utility_costs_variation = 0;
			$total_budgeted_costs_variation = 0;
			for ($i = 1; $i <= 12; $i++) {
				$this->utilities_model->utilities_month = $i;
				$this->utilities_model->utilities_year = date("Y") - 1;

				$YtdUtilities = $this->utilities_model->getUtility();

				//For variation
				$total_utility_costs_variation += $YtdUtilities['total_electricity_cost'] + $YtdUtilities['total_fuel_oil_cost'] + $YtdUtilities['total_lpg_cost'] + $YtdUtilities['total_natural_gas_cost'] + $YtdUtilities['district_heating_cost'] + $YtdUtilities['district_cooling_cost'] + $YtdUtilities['water_total_consumption_cost'] + $YtdUtilities['district_cooling_fixed_cost'] + $YtdUtilities['district_heating_fixed_cost'] + $YtdUtilities['lpg_fixed_cost'] + $YtdUtilities['natural_gas_fixed_cost'] + $YtdUtilities['water_fixed_cost'];

				$total_budgeted_costs_variation += $YtdUtilities['electricity_total_budget_cost'] + $YtdUtilities['fuel_total_budget_cost'] + $YtdUtilities['lpg_total_budget_cost'] + $YtdUtilities['natural_gas_total_budget_cost'] + $YtdUtilities['district_heating_total_budget_cost'] + $YtdUtilities['district_cooling_total_budget_cost'] + $YtdUtilities['water_total_consumption_budget_cost'];
			}
		}

		$variation_ytd = ($total_utility_costs_variation != '' && $total_budgeted_costs_variation != '') ? $total_budgeted_costs_variation - $total_utility_costs_variation : 0;
		$dataCarbon['variation_ytd'] = $variation_ytd;
		$dataCarbon['variationPercentage_ytd'] = $total_utility_costs_variation != '' ? ($variation_ytd * 100) / $total_utility_costs_variation : 0;
		$dataCarbon['ytd_carbon_footprint'] = $ytd_carbon_footprint;
		$dataCarbon['ytd_carbon_footprintPreviousYear'] = $ytd_carbon_footprintPreviousYear;
		$dataCarbon['ytd_carbon_footprint_new'] = $currentMonth_footPrint_new;
		$dataCarbon['ytd_carbon_footprint_baseline_new'] = $baselineMonth_footPrint_new;
		$currentMonth_cost_roomNight = ($dataCarbon['total_utility_cost_currentMonth'] != '' && $getUtilities['total_room_night']) ? $dataCarbon['total_utility_cost_currentMonth'] / $getUtilities['total_room_night'] : 0;
		//last month - utilities cost/room night
		$this->utilities_model->utilities_month = date('n') - 2;
		$this->utilities_model->utilities_year = date("Y");
		if ($this->utilities_model->utilities_month == -1) {
			$this->utilities_model->utilities_month = 11;
			$this->utilities_model->utilities_year = date("Y") - 1;
		} else if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year = date("Y") - 1;
		}
		$getUtilities_lastMonth = $this->utilities_model->getUtility();
		$getUtilities_lastMonth['total_electricity_cost'] = ($getUtilities_lastMonth['total_electricity_cost'] != '') ? $getUtilities_lastMonth['total_electricity_cost'] : 0;
		$getUtilities_lastMonth['total_fuel_oil_cost'] = ($getUtilities_lastMonth['total_fuel_oil_cost'] != '') ? $getUtilities_lastMonth['total_fuel_oil_cost'] : 0;
		$getUtilities_lastMonth['total_lpg_cost'] = ($getUtilities_lastMonth['total_lpg_cost'] != '') ? $getUtilities_lastMonth['total_lpg_cost'] : 0;
		$getUtilities_lastMonth['total_natural_gas_cost'] = ($getUtilities_lastMonth['total_natural_gas_cost'] != '') ? $getUtilities_lastMonth['total_natural_gas_cost'] : 0;
		$getUtilities_lastMonth['district_heating_cost'] = ($getUtilities_lastMonth['district_heating_cost'] != '') ? $getUtilities_lastMonth['district_heating_cost'] : 0;
		$getUtilities_lastMonth['district_cooling_cost'] = ($getUtilities_lastMonth['district_cooling_cost'] != '') ? $getUtilities_lastMonth['district_cooling_cost'] : 0;
		$getUtilities_lastMonth['water_total_consumption_cost'] = ($getUtilities_lastMonth['water_total_consumption_cost'] != '') ? $getUtilities_lastMonth['water_total_consumption_cost'] : 0;
		$getUtilities_lastMonth['district_heating_fixed_cost'] = ($getUtilities_lastMonth['district_heating_fixed_cost'] != '') ? $getUtilities_lastMonth['district_heating_fixed_cost'] : 0;
		$getUtilities_lastMonth['district_cooling_fixed_cost'] = ($getUtilities_lastMonth['district_cooling_fixed_cost'] != '') ? $getUtilities_lastMonth['district_cooling_fixed_cost'] : 0;
		$getUtilities_lastMonth['lpg_fixed_cost'] = ($getUtilities_lastMonth['lpg_fixed_cost'] != '') ? $getUtilities_lastMonth['lpg_fixed_cost'] : 0;
		$getUtilities_lastMonth['natural_gas_fixed_cost'] = ($getUtilities_lastMonth['natural_gas_fixed_cost'] != '') ? $getUtilities_lastMonth['natural_gas_fixed_cost'] : 0;
		$getUtilities_lastMonth['water_fixed_cost'] = ($getUtilities_lastMonth['water_fixed_cost'] != '') ? $getUtilities_lastMonth['water_fixed_cost'] : 0;
		$dataCarbon['total_utility_cost_lastMonth'] = $getUtilities_lastMonth['total_electricity_cost'] + $getUtilities_lastMonth['total_fuel_oil_cost'] + $getUtilities_lastMonth['total_lpg_cost'] + $getUtilities_lastMonth['total_natural_gas_cost'] + $getUtilities_lastMonth['district_heating_cost'] + $getUtilities_lastMonth['district_cooling_cost'] + $getUtilities_lastMonth['water_total_consumption_cost'] + $getUtilities_lastMonth['district_heating_fixed_cost'] + $getUtilities_lastMonth['district_cooling_fixed_cost'] + $getUtilities_lastMonth['lpg_fixed_cost'] + $getUtilities_lastMonth['natural_gas_fixed_cost'] + $getUtilities_lastMonth['water_fixed_cost'];
		$lastMonth_cost_roomNight = ($dataCarbon['total_utility_cost_lastMonth'] != '' && $getUtilities_lastMonth['total_room_night']) ? $dataCarbon['total_utility_cost_lastMonth'] / $getUtilities_lastMonth['total_room_night'] : 0;
		//last month - utilities cost/room night
		$this->utilities_model->utilities_month = date('n') - 1;
		$this->utilities_model->utilities_year = date("Y");
		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year = date("Y") - 1;
		}
		$getUtilities_lastMonth = $this->utilities_model->getUtility();
		$utility_cost_calculation = array();
		// $dataCarbon['currentmonth'] = date('F', strtotime('-1 months'));
		// $dataCarbon['currentyear']  = date('Y');
		// $dataCarbon['lastmonth'] = date('F', strtotime('-1 months'));
		// $dataCarbon['lastyear']  = date('Y', strtotime('-1 year'));
		//same month last year- utilities cost/room night
		$this->utilities_model->utilities_month = date('n') - 1;
		$this->utilities_model->utilities_year = date("Y", strtotime("-1 year"));
		$dataCarbon['lastyear']     = date('Y', strtotime('-1 year'));
		$dataCarbon['currentmonth'] = date('F', strtotime('-1 months'));
		$dataCarbon['lastmonth']    = date('F', strtotime('-1 months'));
		$dataCarbon['currentyear']  = date("Y");
		if ($this->utilities_model->utilities_month == 0) {
			$this->utilities_model->utilities_month = 12;
			$this->utilities_model->utilities_year  = date("Y") - 2;
			$dataCarbon['lastyear']     = date("Y") - 2;
			$dataCarbon['currentmonth'] = date('F', strtotime('-13 months'));
			$dataCarbon['currentyear']  = date("Y", strtotime("-1 year"));
		}
		$getUtilities_sameMonth_lastYear = $this->utilities_model->getUtility();
		$getUtilities_sameMonth_lastYear['total_electricity_cost'] = ($getUtilities_sameMonth_lastYear['total_electricity_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_electricity_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] = ($getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_lpg_cost'] = ($getUtilities_sameMonth_lastYear['total_lpg_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_lpg_cost'] : 0;
		$getUtilities_sameMonth_lastYear['total_natural_gas_cost'] = ($getUtilities_sameMonth_lastYear['total_natural_gas_cost'] != '') ? $getUtilities_sameMonth_lastYear['total_natural_gas_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_heating_cost'] = ($getUtilities_sameMonth_lastYear['district_heating_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_heating_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_cooling_cost'] = ($getUtilities_sameMonth_lastYear['district_cooling_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_cooling_cost'] : 0;
		$getUtilities_sameMonth_lastYear['water_total_consumption_cost'] = ($getUtilities_sameMonth_lastYear['water_total_consumption_cost'] != '') ? $getUtilities_sameMonth_lastYear['water_total_consumption_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] = ($getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] = ($getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['lpg_fixed_cost'] = ($getUtilities_sameMonth_lastYear['lpg_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['lpg_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] = ($getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] : 0;
		$getUtilities_sameMonth_lastYear['water_fixed_cost'] = ($getUtilities_sameMonth_lastYear['water_fixed_cost'] != '') ? $getUtilities_sameMonth_lastYear['water_fixed_cost'] : 0;
		$dataCarbon['total_utility_cost_sameMonth_lastYear'] = $getUtilities_sameMonth_lastYear['total_electricity_cost'] + $getUtilities_sameMonth_lastYear['total_fuel_oil_cost'] + $getUtilities_sameMonth_lastYear['total_lpg_cost'] + $getUtilities_sameMonth_lastYear['total_natural_gas_cost'] + $getUtilities_sameMonth_lastYear['district_heating_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_cost'] + $getUtilities_sameMonth_lastYear['water_total_consumption_cost'] + $getUtilities_sameMonth_lastYear['district_heating_fixed_cost'] + $getUtilities_sameMonth_lastYear['district_cooling_fixed_cost'] + $getUtilities_sameMonth_lastYear['lpg_fixed_cost'] + $getUtilities_sameMonth_lastYear['natural_gas_fixed_cost'] + $getUtilities_sameMonth_lastYear['water_fixed_cost'];
		$dataCarbon['variation_ytd'] = $variation_ytd;
		$dataCarbon['variationPercentage_ytd'] = $total_utility_costs_variation != '' ? ($variation_ytd * 100) / $total_utility_costs_variation : 0;
		$sameMonth_lastYear_cost_roomNight = ($dataCarbon['total_utility_cost_sameMonth_lastYear'] != '' && $getUtilities_sameMonth_lastYear['total_room_night']) ? $dataCarbon['total_utility_cost_sameMonth_lastYear'] / $getUtilities_sameMonth_lastYear['total_room_night'] : 0;

		$dataCarbon['ytd_carbon_footprint'] = $ytd_carbon_footprint;
		$dataCarbon['ytd_carbon_footprintPreviousYear'] = $ytd_carbon_footprintPreviousYear;
		$dataCarbon['ytd_carbon_footprint_new'] = $currentMonth_footPrint_new;
		$dataCarbon['ytd_carbon_footprint_baseline_new'] = $baselineMonth_footPrint_new;

		$dataCarbon['currentMonth_cost_roomNight'] = $currentMonth_cost_roomNight;
		$dataCarbon['lastMonth_cost_roomNight'] = $lastMonth_cost_roomNight;
		$dataCarbon['sameMonth_lastYear_cost_roomNight'] = $sameMonth_lastYear_cost_roomNight;

		/*hdd cdd added in report*/

		$utility_cost_calculation_chr['cdd']['consumption'] = ($getUtilities_sameMonth_lastYear['cdd'] != '' && $getUtilities_lastMonth['cdd']) ? ($getUtilities_lastMonth['cdd'] - $getUtilities_sameMonth_lastYear['cdd']) * 100 / $getUtilities_sameMonth_lastYear['cdd'] : 0;
		$utility_cost_calculation_chr['cdd']['consumption'] = $utility_cost_calculation_chr['cdd']['consumption'];
		$utility_cost_calculation_chr['cdd']['title'] = 'CDD';
		$utility_cost_calculation_chr['cdd']['consumption_image'] = $getUtilities_sameMonth_lastYear['cdd'] < $getUtilities_lastMonth['cdd'] ? 'upArrow.png' : 'downArrow.png';

		$utility_cost_calculation_chr['hdd']['consumption'] = ($getUtilities_sameMonth_lastYear['hdd'] != '' && $getUtilities_lastMonth['hdd']) ? ($getUtilities_lastMonth['hdd'] - $getUtilities_sameMonth_lastYear['hdd']) * 100 / $getUtilities_sameMonth_lastYear['hdd'] : 0;
		$utility_cost_calculation_chr['hdd']['consumption'] = $utility_cost_calculation_chr['hdd']['consumption'];
		$utility_cost_calculation_chr['hdd']['title'] = 'HDD';
		$utility_cost_calculation_chr['hdd']['consumption_image'] = $getUtilities_sameMonth_lastYear['hdd'] < $getUtilities_lastMonth['hdd'] ? 'upArrow.png' : 'downArrow.png';

		// if it is higher than last year, thn it will be green arrow up
		$utility_cost_calculation_chr['room_nights']['consumption'] = ($getUtilities_sameMonth_lastYear['total_room_night'] != '' && $getUtilities_lastMonth['total_room_night']) ? ($getUtilities_lastMonth['total_room_night'] - $getUtilities_sameMonth_lastYear['total_room_night']) * 100 / $getUtilities_sameMonth_lastYear['total_room_night'] : 0;
		$utility_cost_calculation_chr['room_nights']['consumption'] = $utility_cost_calculation_chr['room_nights']['consumption'];
		$utility_cost_calculation_chr['room_nights']['title'] = 'Room Nights';
		// $utility_cost_calculation_chr['room_nights']['consumption_image'] = $getUtilities_sameMonth_lastYear['room_nights'] < $getUtilities_lastMonth['room_nights'] ? 'downArrowRed.png' : 'upArrowGreen.png';
		$utility_cost_calculation_chr['room_nights']['consumption_image'] = $getUtilities_sameMonth_lastYear['total_room_night'] < $getUtilities_lastMonth['total_room_night'] ? 'upArrowGreen.png' : 'downArrowRed.png';

		$dataCarbon['cdd_hdd'] = $utility_cost_calculation_chr;

		return $dataCarbon;
	}
	public function getYtdCarbonFootprints($site_id, $site_details, $dataFactor, $emissionFactor, $current_year, $previous_year, $current_month)
	{
		$ytd_carbon_footprint = 0;
		$ytd_carbon_footprintPreviousYear = 0;
		$emissionDefaults = [
			'electricity_emission_factor' => 0,
			'electricity_emission_factor_percentage' => 0,
			'lpg_emission_factor' => 0,
			'fuel_emission_factor' => 0,
			'natural_gas_emission_factor' => 0,
			'district_heating_emission_factor' => 0,
			'district_cooling_emission_factor' => 0
		];
		$currentYearEmissions = array_merge($emissionDefaults, $emissionFactor[$current_year] ?? []);
		$previousYearEmissions = array_merge($emissionDefaults, $emissionFactor[$previous_year] ?? []);
		$baselineYearEmissions = array_merge($emissionDefaults, $emissionFactor[$site_details['baseline_regression_year']] ?? []);

		foreach (['currentYearEmissions', 'previousYearEmissions', 'baselineYearEmissions'] as $varName) {
			foreach ($$varName as $key => $value) {
				$$varName[$key] = is_numeric($value) ? floatval($value) : 0;
			}
		}

		$this->utilities_model->site_id = $site_id;

		// --- YTD Carbon Footprint NEW for Current Year ---
		for ($i = 1; $i <= $current_month; $i++) {
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = $current_year;
			$YtdUtilities = $this->utilities_model->getUtility();

			$YtdUtilities['total_electricity_kwh'] = floatval($YtdUtilities['total_electricity_kwh'] ?? 0);
			$YtdUtilities['total_lpg'] = floatval($YtdUtilities['total_lpg'] ?? 0);
			$YtdUtilities['total_fuel_oil'] = floatval($YtdUtilities['total_fuel_oil'] ?? 0);
			$YtdUtilities['total_natural_gas'] = floatval($YtdUtilities['total_natural_gas'] ?? 0);
			$YtdUtilities['district_heating'] = floatval($YtdUtilities['district_heating'] ?? 0);
			$YtdUtilities['district_cooling'] = floatval($YtdUtilities['district_cooling'] ?? 0);
			$YtdUtilities['onsite_generators_quantity'] = floatval($YtdUtilities['onsite_generators_quantity'] ?? 0);
			$YtdUtilities['total_renewable_energy_production'] = floatval($YtdUtilities['total_renewable_energy_production'] ?? 0);

			$electricity_consumption = (1 - ($currentYearEmissions['electricity_emission_factor_percentage'] / 100)) * ($YtdUtilities['total_electricity_kwh'] - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']);
			$net_electricity = $electricity_consumption;

			$ytd_carbon_footprint_new += ($net_electricity * $dataFactor['electricity'] * $currentYearEmissions['electricity_emission_factor']) 
				+ ($dataFactor['lpg'] * $YtdUtilities['total_lpg'] * $currentYearEmissions['lpg_emission_factor']) 
				+ ($dataFactor['fuel_oil'] * $YtdUtilities['total_fuel_oil'] * $currentYearEmissions['fuel_emission_factor']) 
				+ ($dataFactor['natural_gas'] * $YtdUtilities['total_natural_gas'] * $currentYearEmissions['natural_gas_emission_factor'])
				+ ($dataFactor['district_heating'] * $YtdUtilities['district_heating'] * $currentYearEmissions['district_heating_emission_factor']) 
				+ ($dataFactor['district_cooling'] * $YtdUtilities['district_cooling'] * $currentYearEmissions['district_cooling_emission_factor']);
		}

		// --- YTD Carbon Footprint for Current Year ---
		for ($i = 1; $i <= $current_month; $i++) {
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = $current_year;
			$YtdUtilities = $this->utilities_model->getUtility();

			$YtdUtilities['total_electricity_kwh'] = floatval($YtdUtilities['total_electricity_kwh'] ?? 0);
			$YtdUtilities['total_lpg_cost'] = floatval($YtdUtilities['total_lpg_cost'] ?? 0);
			$YtdUtilities['total_fuel_oil_cost'] = floatval($YtdUtilities['total_fuel_oil_cost'] ?? 0);
			$YtdUtilities['total_natural_gas_cost'] = floatval($YtdUtilities['total_natural_gas_cost'] ?? 0);
			$YtdUtilities['district_heating_cost'] = floatval($YtdUtilities['district_heating_cost'] ?? 0);
			$YtdUtilities['district_cooling_cost'] = floatval($YtdUtilities['district_cooling_cost'] ?? 0);
			$YtdUtilities['onsite_generators_quantity'] = floatval($YtdUtilities['onsite_generators_quantity'] ?? 0);
			$YtdUtilities['total_renewable_energy_production'] = floatval($YtdUtilities['total_renewable_energy_production'] ?? 0);

			$electricity_consumption = (1 - ($currentYearEmissions['electricity_emission_factor_percentage'] / 100)) * ($YtdUtilities['total_electricity_kwh'] - $YtdUtilities['onsite_generators_quantity'] - $YtdUtilities['total_renewable_energy_production']);
			$net_electricity = $electricity_consumption;

			$ytd_carbon_footprint += ($net_electricity * $dataFactor['electricity'] * $currentYearEmissions['electricity_emission_factor']) 
				+ ($dataFactor['lpg'] * $YtdUtilities['total_lpg_cost'] * $currentYearEmissions['lpg_emission_factor']) 
				+ ($dataFactor['fuel_oil'] * $YtdUtilities['total_fuel_oil_cost'] * $currentYearEmissions['fuel_emission_factor']) 
				+ ($dataFactor['natural_gas'] * $YtdUtilities['total_natural_gas_cost'] * $currentYearEmissions['natural_gas_emission_factor'])
				+ ($dataFactor['district_heating'] * $YtdUtilities['district_heating_cost'] * $currentYearEmissions['district_heating_emission_factor']) 
				+ ($dataFactor['district_cooling'] * $YtdUtilities['district_cooling_cost'] * $currentYearEmissions['district_cooling_emission_factor']);
		}

		// --- YTD Carbon Footprint for Previous Year ---
		for ($i = 1; $i <= $current_month; $i++) {
			$this->utilities_model->utilities_month = $i;
			$this->utilities_model->utilities_year = $previous_year;
			$YtdUtilitiesPreviousYear = $this->utilities_model->getUtility();

			$YtdUtilitiesPreviousYear['total_electricity_kwh'] = floatval($YtdUtilitiesPreviousYear['total_electricity_kwh'] ?? 0);
			$YtdUtilitiesPreviousYear['total_lpg'] = floatval($YtdUtilitiesPreviousYear['total_lpg'] ?? 0);
			$YtdUtilitiesPreviousYear['total_fuel_oil'] = floatval($YtdUtilitiesPreviousYear['total_fuel_oil'] ?? 0);
			$YtdUtilitiesPreviousYear['total_natural_gas'] = floatval($YtdUtilitiesPreviousYear['total_natural_gas'] ?? 0);
			$YtdUtilitiesPreviousYear['district_heating'] = floatval($YtdUtilitiesPreviousYear['district_heating'] ?? 0);
			$YtdUtilitiesPreviousYear['district_cooling'] = floatval($YtdUtilitiesPreviousYear['district_cooling'] ?? 0);
			$YtdUtilitiesPreviousYear['onsite_generators_quantity'] = floatval($YtdUtilitiesPreviousYear['onsite_generators_quantity'] ?? 0);
			$YtdUtilitiesPreviousYear['total_renewable_energy_production'] = floatval($YtdUtilitiesPreviousYear['total_renewable_energy_production'] ?? 0);
			$YtdUtilitiesPreviousYear['total_fuel_oil_cost'] = floatval($YtdUtilitiesPreviousYear['total_fuel_oil_cost'] ?? 0);
			$YtdUtilitiesPreviousYear['total_natural_gas_cost'] = floatval($YtdUtilitiesPreviousYear['total_natural_gas_cost'] ?? 0);

			$electricity_consumption_prev = (1 - ($previousYearEmissions['electricity_emission_factor_percentage'] / 100)) * ($YtdUtilitiesPreviousYear['total_electricity_kwh'] - $YtdUtilitiesPreviousYear['onsite_generators_quantity'] - $YtdUtilitiesPreviousYear['total_renewable_energy_production']);
			$net_electricity_prev = $electricity_consumption_prev;
			$totalfueloilpreviousyear = $YtdUtilitiesPreviousYear['total_fuel_oil_cost'];
			$totalnaturalgaspreviousyear = $YtdUtilitiesPreviousYear['total_natural_gas_cost'];
			
			$ytd_carbon_footprintPreviousYear += ($net_electricity_prev * $dataFactor['electricity'] * $previousYearEmissions['electricity_emission_factor']) 
				+ ($dataFactor['lpg'] * $YtdUtilitiesPreviousYear['total_lpg'] * $previousYearEmissions['lpg_emission_factor']) 
				+ ($dataFactor['fuel_oil'] * $YtdUtilitiesPreviousYear['total_fuel_oil'] * $previousYearEmissions['fuel_emission_factor']) 
				+ ($dataFactor['natural_gas'] * $YtdUtilitiesPreviousYear['total_natural_gas'] * $previousYearEmissions['natural_gas_emission_factor']) 
				+ ($dataFactor['district_heating'] * $YtdUtilitiesPreviousYear['district_heating'] * $previousYearEmissions['district_heating_emission_factor']) 
				+ ($dataFactor['district_cooling'] * $YtdUtilitiesPreviousYear['district_cooling'] * $previousYearEmissions['district_cooling_emission_factor']);
		}

		// --- YTD Carbon Footprint for Baseline Year ---
        for ($i = 1; $i <= $current_month; $i++) {
            $this->utilities_model->utilities_month = $i;
            $this->utilities_model->utilities_year = $site_details['baseline_regression_year'];
            $YtdUtilitiesBaseline = $this->utilities_model->getUtility();

			$YtdUtilitiesBaseline['total_electricity_kwh'] = floatval($YtdUtilitiesBaseline['total_electricity_kwh'] ?? 0);
            $YtdUtilitiesBaseline['total_lpg'] = floatval($YtdUtilitiesBaseline['total_lpg'] ?? 0);
            $YtdUtilitiesBaseline['total_fuel_oil'] = floatval($YtdUtilitiesBaseline['total_fuel_oil'] ?? 0);
            $YtdUtilitiesBaseline['total_natural_gas'] = floatval($YtdUtilitiesBaseline['total_natural_gas'] ?? 0);
            $YtdUtilitiesBaseline['district_heating'] = floatval($YtdUtilitiesBaseline['district_heating'] ?? 0);
            $YtdUtilitiesBaseline['district_cooling'] = floatval($YtdUtilitiesBaseline['district_cooling'] ?? 0);
            $YtdUtilitiesBaseline['onsite_generators_quantity'] = floatval($YtdUtilitiesBaseline['onsite_generators_quantity'] ?? 0);
            $YtdUtilitiesBaseline['total_renewable_energy_production'] = floatval($YtdUtilitiesBaseline['total_renewable_energy_production'] ?? 0);

            $electricity_consumption_baseline = (1 - ($baselineYearEmissions['electricity_emission_factor_percentage'] / 100)) * ($YtdUtilitiesBaseline['total_electricity_kwh'] - $YtdUtilitiesBaseline['onsite_generators_quantity'] - $YtdUtilitiesBaseline['total_renewable_energy_production']);
            $net_electricity_baseline = $electricity_consumption_baseline;

            $ytd_carbon_footprint_baseline_new += ($net_electricity_baseline * $dataFactor['electricity'] * $baselineYearEmissions['electricity_emission_factor'])
                + ($dataFactor['lpg'] * $YtdUtilitiesBaseline['total_lpg'] * $baselineYearEmissions['lpg_emission_factor'])
                + ($dataFactor['fuel_oil'] * $YtdUtilitiesBaseline['total_fuel_oil'] * $baselineYearEmissions['fuel_emission_factor'])
                + ($dataFactor['natural_gas'] * $YtdUtilitiesBaseline['total_natural_gas'] * $baselineYearEmissions['natural_gas_emission_factor'])
                + ($dataFactor['district_heating'] * $YtdUtilitiesBaseline['district_heating'] * $baselineYearEmissions['district_heating_emission_factor'])
                + ($dataFactor['district_cooling'] * $YtdUtilitiesBaseline['district_cooling'] * $baselineYearEmissions['district_cooling_emission_factor']);
        }


        return [
            'ytd_carbon_footprint' => $ytd_carbon_footprint,
            'ytd_carbon_footprintPreviousYear' => $ytd_carbon_footprintPreviousYear,
            'ytd_carbon_footprint_new' => $ytd_carbon_footprint_new,
            'ytd_carbon_footprint_baseline_new' => $ytd_carbon_footprint_baseline_new,
        ];
	}

	public function getMySitesWidgetData($site_details) {
		$this->load->model('sites/site_emission_model');
		$this->load->model('utilities/utilities_model');
		$baseline_year = $site_details['baseline_regression_year']; 
		$baseDate = new DateTime('first day of this month');
		$prev1 = (clone $baseDate)->modify('-1 month');
		$prev2 = (clone $baseDate)->modify('-2 months');
		$prev1LastYear = (clone $prev1)->modify('-1 year');

		$prev1Year  = (int) $prev1->format('Y');
		$prev1Month = (int) $prev1->format('n');

		$prev2Year  = (int) $prev2->format('Y');
		$prev2Month = (int) $prev2->format('n');

		$prev1LYYear  = (int) $prev1LastYear->format('Y');
		$prev1LYMonth = (int) $prev1LastYear->format('n');

		$dataFactor = getMmbtuFactorConversionAllUtility($site_details['id']);
		$emission_by_year = [];
		$this->site_emission_model->site_id = $site_details['id'];
		$this->site_emission_model->year_ids = [
			(int) $prev1->format('Y'),          // Previous month year
			(int) $prev1LastYear->format('Y'),  // Same month last year
			(int) $baseline_year
		];
		$site_emission = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
		if (!empty($site_emission)) {
			foreach ($site_emission as $row) {
				$emission_by_year[$row['s']['year_id']] = $row['s'];
			}
		}
		$this->db->select("
			CONCAT(year_id, '-', LPAD(month_id, 2, '0')) AS period,
			year_id,
			month_id,

			-- Total Utility Cost
			(
				COALESCE(total_electricity_cost, 0) + COALESCE(total_fuel_oil_cost, 0) + COALESCE(total_lpg_cost, 0) +
				COALESCE(total_natural_gas_cost, 0) + COALESCE(district_heating_cost, 0) + COALESCE(district_cooling_cost, 0) +
				COALESCE(water_total_consumption_cost, 0) + COALESCE(district_cooling_fixed_cost, 0) + COALESCE(district_heating_fixed_cost, 0) +
				COALESCE(lpg_fixed_cost, 0) + COALESCE(natural_gas_fixed_cost, 0) + COALESCE(water_fixed_cost, 0)
			) AS total_utility_cost,

			-- Total Utility Cost YTD
			(
				SELECT SUM(
					COALESCE(uc2.total_electricity_cost, 0) + COALESCE(uc2.total_fuel_oil_cost, 0) + COALESCE(uc2.total_lpg_cost, 0) +
					COALESCE(uc2.total_natural_gas_cost, 0) + COALESCE(uc2.district_heating_cost, 0) + COALESCE(uc2.district_cooling_cost, 0) +
					COALESCE(uc2.water_total_consumption_cost, 0) + COALESCE(uc2.district_cooling_fixed_cost, 0) + COALESCE(uc2.district_heating_fixed_cost, 0) +
					COALESCE(uc2.lpg_fixed_cost, 0) + COALESCE(uc2.natural_gas_fixed_cost, 0) + COALESCE(uc2.water_fixed_cost, 0)
				)
				FROM utilities_cost AS uc2
				WHERE uc2.site_id = utilities_cost.site_id
				AND uc2.year_id = utilities_cost.year_id
				AND uc2.month_id <= utilities_cost.month_id
			) AS total_utility_cost_ytd,

			-- Total Budgeted Cost
			(
				COALESCE(electricity_total_budget_cost, 0) +
				COALESCE(fuel_total_budget_cost, 0) +
				COALESCE(lpg_total_budget_cost, 0) +
				COALESCE(natural_gas_total_budget_cost, 0) +
				COALESCE(district_heating_total_budget_cost, 0) +
				COALESCE(district_cooling_total_budget_cost, 0) +
				COALESCE(water_total_consumption_budget_cost, 0)
			) AS total_budgeted_cost,

			-- Total Budgeted Cost YTD
			(
				SELECT SUM(
					COALESCE(uc2.electricity_total_budget_cost, 0) +
					COALESCE(uc2.fuel_total_budget_cost, 0) +
					COALESCE(uc2.lpg_total_budget_cost, 0) +
					COALESCE(uc2.natural_gas_total_budget_cost, 0) +
					COALESCE(uc2.district_heating_total_budget_cost, 0) +
					COALESCE(uc2.district_cooling_total_budget_cost, 0) +
					COALESCE(uc2.water_total_consumption_budget_cost, 0)
				)
				FROM utilities_cost AS uc2
				WHERE uc2.site_id = utilities_cost.site_id
				AND uc2.year_id = utilities_cost.year_id
				AND uc2.month_id <= utilities_cost.month_id
			) AS total_budgeted_cost_ytd,

			-- Cost Per Room Night
			(
				CASE 
					WHEN COALESCE(total_room_night, 0) > 0 THEN 
						(
							COALESCE(total_electricity_cost, 0) + COALESCE(total_fuel_oil_cost, 0) + COALESCE(total_lpg_cost, 0) +
							COALESCE(total_natural_gas_cost, 0) + COALESCE(district_heating_cost, 0) + COALESCE(district_cooling_cost, 0) +
							COALESCE(water_total_consumption_cost, 0) + COALESCE(district_cooling_fixed_cost, 0) + COALESCE(district_heating_fixed_cost, 0) +
							COALESCE(lpg_fixed_cost, 0) + COALESCE(natural_gas_fixed_cost, 0) + COALESCE(water_fixed_cost, 0)
						) / COALESCE(total_room_night, 0)
					ELSE 0
				END
			) AS cost_roomNight,

			-- Total Electricity kWh (minus onsite generators)
			(COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) AS totalelectricitykwh,

			-- Carbon Footprint
			(
				(".($dataFactor['electricity'] ?? 0)." * (COALESCE(total_electricity_kwh, 0) - COALESCE(onsite_generators_quantity, 0) - COALESCE(total_renewable_energy_production, 0)) * ".(float) ($site_details['electricity_emission_factor'] ?? 0).") +
				(".($dataFactor['lpg'] ?? 0)." * COALESCE(total_lpg, 0) * ".(float) ($site_details['lpg_emission_factor'] ?? 0).") +
				(".($dataFactor['fuel_oil'] ?? 0)." * COALESCE(total_fuel_oil, 0) * ".(float) ($site_details['fuel_emission_factor'] ?? 0).") +
				(".($dataFactor['natural_gas'] ?? 0)." * COALESCE(total_natural_gas, 0) * ".(float) ($site_details['natural_gas_emission_factor'] ?? 0).") +
				(".($dataFactor['district_heating'] ?? 0)." * COALESCE(district_heating, 0) * ".(float) ($site_details['district_heating_emission_factor'] ?? 0).") +
				(".($dataFactor['district_cooling'] ?? 0)." * COALESCE(district_cooling, 0) * ".(float) ($site_details['district_cooling_emission_factor'] ?? 0).") 
			) AS carbon_footprint,
			-- CDD and HDD
			COALESCE(cdd, 0) AS cdd,
			COALESCE(hdd, 0) AS hdd,
			COALESCE(total_room_night, 0) AS total_room_night,

			-- TOTAL UTILITY (SUM CONSUMPTION)
			(
				COALESCE(total_electricity_kwh, 0) +
				COALESCE(total_fuel_oil, 0) +
				COALESCE(total_lpg, 0) +
				COALESCE(total_natural_gas, 0) +
				COALESCE(district_heating, 0) +
				COALESCE(district_cooling, 0) +
				COALESCE(water_total_consumption, 0)
			) AS total_utility_consumption,

			-- MMBTU CONVERSIONS
			(COALESCE(total_fuel_oil, 0) * ".($dataFactor['fuel_oil'] ?? 0).") AS mmbtu_fuel,
			(COALESCE(total_lpg, 0) * ".($dataFactor['lpg'] ?? 0).") AS mmbtu_lpg,
			(COALESCE(total_natural_gas, 0) * ".($dataFactor['natural_gas'] ?? 0).") AS mmbtu_natural_gas,
			(COALESCE(district_heating, 0) * ".($dataFactor['district_heating'] ?? 0).") AS mmbtu_heating_district,
			(COALESCE(district_cooling, 0) * ".($dataFactor['district_cooling'] ?? 0).") AS mmbtu_cooling_district,

			-- RAW UTILITIES
			COALESCE(total_electricity_kwh, 0) as electricity_raw,
			COALESCE(total_fuel_oil, 0) as fuel_raw,
			COALESCE(total_lpg, 0) as lpg_raw,
			COALESCE(total_natural_gas, 0) as natural_gas_raw,
			COALESCE(district_heating, 0) as heating_raw,
			COALESCE(district_cooling, 0) as cooling_raw,
			COALESCE(water_total_consumption, 0) as water_raw,

			-- RAW UTILITIES COST
			COALESCE(total_electricity_cost, 0) as electricity_cost_raw,
			COALESCE(total_fuel_oil_cost, 0) as fuel_cost_raw,
			COALESCE(total_lpg_cost, 0) as lpg_cost_raw,
			COALESCE(total_natural_gas_cost, 0) as natural_gas_cost_raw,
			COALESCE(district_heating_cost, 0) as heating_cost_raw,
			COALESCE(district_cooling_cost, 0) as cooling_cost_raw,
			COALESCE(water_total_consumption_cost, 0) as water_cost_raw,
			COALESCE(district_heating_fixed_cost, 0) as heating_fixed_cost_raw,
			COALESCE(district_cooling_fixed_cost, 0) as cooling_fixed_cost_raw,
			COALESCE(lpg_fixed_cost, 0) as lpg_fixed_cost_raw,
			COALESCE(natural_gas_fixed_cost, 0) as natural_gas_fixed_cost_raw,
			COALESCE(water_fixed_cost, 0) as water_fixed_cost_raw

		", FALSE);
		$this->db->from('utilities_cost');
		$this->db->where('site_id', $site_details['id']);
		$this->db->where("
		(
			-- Previous Month
			(year_id = {$prev1Year} AND month_id = {$prev1Month})

			OR

			-- Month Before Previous
			(year_id = {$prev2Year} AND month_id = {$prev2Month})

			OR

			-- Same Month Last Year (previous month LY)
			(year_id = {$prev1LYYear} AND month_id = {$prev1LYMonth})
		)
	 ", NULL, FALSE);


		$this->db->order_by('year_id', 'DESC');
		$this->db->order_by('month_id', 'DESC');
		$query = $this->db->get();
		$rawResult = $query->result_array();
		$this->load->model('utilities/utilities_model');
		$currentYearFromDate   = (int) $prev1->format('Y');   // YTD year = previous month year
		$lastYearFromDate      = $currentYearFromDate - 1;
		$currentMonthFromDate  = (int) $prev1->format('n');   // YTD up to previous month

		$dataNewYtd = $this->getYtdCarbonFootprints(
			$site_details['id'],
			$site_details,
			$dataFactor,
			$emission_by_year,
			$currentYearFromDate,
			$lastYearFromDate,
			$currentMonthFromDate
		);

		$emptyRow = [
			'period' => '',
			'year_id' => 0,
			'month_id' => 0,
			'total_utility_cost' => 0,
			'total_utility_cost_ytd' => 0,
			'total_budgeted_cost' => 0,
			'total_budgeted_cost_ytd' => 0,
			'cost_roomNight' => 0,
			'totalelectricitykwh' => 0,
			'carbon_footprint' => 0,
			'cdd' => 0,
			'hdd' => 0,
			'total_room_night' => 0,
			'total_utility_consumption' => 0,
			'mmbtu_fuel' => 0,
			'mmbtu_lpg' => 0,
			'mmbtu_natural_gas' => 0,
			'mmbtu_heating_district' => 0,
			'mmbtu_cooling_district' => 0,
			'electricity_raw' => 0,
			'fuel_raw' => 0,
			'lpg_raw' => 0,
			'natural_gas_raw' => 0,
			'heating_raw' => 0,
			'cooling_raw' => 0,
			'water_raw' => 0
		];

		$result = [];
		$result[0] = $emptyRow;
		$result[1] = $emptyRow;
		$result[2] = $emptyRow;

		foreach ($rawResult as $row) {
			$rowYear = (int) $row['year_id'];
			$rowMonth = (int) $row['month_id'];

			if ($rowYear == $prev1Year && $rowMonth == $prev1Month) {
				$result[0] = $row;
			} else if ($rowYear == $prev2Year && $rowMonth == $prev2Month) {
				$result[1] = $row;
			} else if ($rowYear == $prev1LYYear && $rowMonth == $prev1LYMonth) {
				$result[2] = $row;
			}
		}

		$result['ytd_carbon_footprint_baseline_new'] = $dataNewYtd['ytd_carbon_footprint_baseline_new'] ?? 0;
		$result['ytd_carbon_footprint'] = $dataNewYtd['ytd_carbon_footprint'] ?? 0;
		$result['ytd_carbon_footprint_new'] = $dataNewYtd['ytd_carbon_footprint_new'] ?? 0;
		$result['ytd_carbon_footprintPreviousYear'] = $dataNewYtd['ytd_carbon_footprintPreviousYear'] ?? 0;

		return $result;
	}

    public function generateGroupUtilityReport($currentYear,$currMonth,$prevYear,$prevMonth,$lastYear, $isAttachment=0) {
        ob_end_clean();
		ob_start();
		$decimal_places = 2;
        $this->load->model([
			'utilities/utilities_model',
			'reports/reports_model',
			'sites/site_waste_model'
		]);
		
        $parentHeading = [
			'Room Nights',
			'Guest Nights',
			'Electricity kWh',
			'Water m3',
			'District Cooling kWh',
			'District Heating kWh',
			'LPG kWh',
			'Natural Gas kWh',
			'Fuel Oil kWh',
			'Energy kWh/Room Nights',
			'Carbon kgCO2',
			'Carbon kgCO2/Guest Night',
			'Water m3/Guest Nights'
		];

		$columns = [
			'Hotel Name',
			'City',
			'Room Nights '.$lastYear,
			'Room Nights '.$currentYear,
			'Room Nights Trend',

			'Guest Nights '.$lastYear,
			'Guest Nights '.$currentYear,
			'Guest Nights Trend',

			'Electricity kWh '.$lastYear,
			'Electricity kWh '.$currentYear,
			'Electricity kWh Trend',

			'Water m3'.$lastYear,
			'Water m3'.$currentYear,
			'Water m3 Trend',

			'District Cooling kWh'.$lastYear,
			'District Cooling kWh'.$currentYear,
			'District Cooling kWh Trend',

			'District Heating kWh'.$lastYear,
			'District Heating kWh'.$currentYear,
			'District Heating kWh Trend',

			'LPG kWh'.$lastYear,
			'LPG kWh'.$currentYear,
			'LPG kWh Trend',

			'Natural Gas kWh'.$lastYear,
			'Natural Gas kWh'.$currentYear,
			'Natural Gas kWh Trend',

			'Fuel Oil kWh'.$lastYear,
			'Fuel Oil kWh'.$currentYear,
			'Fuel Oil kWh Trend',

			'Energy kWh/Room Nights '.$lastYear,
			'Energy kWh/Room Nights '.$currentYear,
			'Energy kWh/Room Nights Trend',

			'Carbon kgCO2'.$lastYear,
			'Carbon kgCO2'.$currentYear,
			'Carbon Trend',

			'Carbon kgCO2/Guest Night'.$lastYear,
			'Carbon kgCO2/Guest Night'.$currentYear,
			'Carbon kgCO2/Guest Night Trend',

			'Water m3/Guest Nights '.$lastYear,
			'Water m3/Guest Nights '.$currentYear,
			'Water m3/Guest Nights Trend',
		];
		$childLabels = [
			$lastYear,
			$currentYear,
			'Trend'
		];

		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$this->lang->load('sites/sites', 'english'); 
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP")
			->setTitle("Group Utility Report")
			->setKeywords("Group Utility Report");

		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
		$highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('YTD');
		applyHeaderColors($objPHPExcel->getActiveSheet());
		autoSizeColumns($objPHPExcel->getActiveSheet());
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Hotel Name');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'City');
		$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
		$startCol = 2;
		foreach ($parentHeading as $heading) {
			$colStart = PHPExcel_Cell::stringFromColumnIndex($startCol);
			$colEnd   = PHPExcel_Cell::stringFromColumnIndex($startCol + 2);

			$objPHPExcel->getActiveSheet()->setCellValue($colStart . '1', $heading);
			$objPHPExcel->getActiveSheet()->mergeCells($colStart . '1:' . $colEnd . '1');

			$startCol += 3;
		}
		$colIndex = 0;
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValue('A2', '');
		$sheet->setCellValue('B2', '');
		$startCol = 2;
		foreach ($parentHeading as $heading) {
			foreach ($childLabels as $label) {
				$sheet->setCellValueByColumnAndRow($startCol, 2, $label);
				$startCol++;
			}
		}
		$rowNum = 3;

		// LOGIC TO FETCH DATA AND POPULATE THE EXCEL SHEET
		$data = $rowsYtd = $rowsCurrentMonth = [];
		$siteCronSettings = $this->sites_model->getSiteCronSettings();
        $monthlyTickedSites = array();
        foreach ($siteCronSettings as $cronSettings) {
            if ($cronSettings['site_cron_settings']['cron_type'] == 'MONTHLY') {
                array_push($monthlyTickedSites,  $cronSettings['site_cron_settings']['site_id']);
            }
        }
		$sites = $this->sites_model->get_site_detail_multiple();
		usort($sites, function ($a, $b) {
			return strcasecmp($a['site_location_name'], $b['site_location_name']);
		});

	    if (!empty($sites)) {
			foreach ($sites as $key => $site_detail) {
				$site_id = $site_detail['id'];
				if(!in_array($site_id, $monthlyTickedSites)) {
					continue;
				}
				$progressOnTarget = [];
				$progressOnTargetWasteYtd = [];
				$filtersBaseline = [];

				$data['sites'][$site_id]['site_location_name'] = $site_detail['site_location_name'];
				$data['sites'][$site_id]['city'] = $site_detail['city'];
				$dataFactor = getMmbtuFactorConversionAllUtility($site_id);
				$filters['previous_month'] = $prevMonth;
				$filters['previous_year']  = $prevYear;

				$this->load->model('utilities/utilities_model');
				$util = $this->utilities_model;
				$util->site_id = $site_id;
				$util->utilities_month = $currMonth;
				$util->utilities_year  = $currentYear;
				$getUtilities = $util->getUtility();
				$getUtilitiesYTD = $util->getUtilityYTD();
				$data['sites'][$site_id]['dataUtilityCurrent']['total_electricity_kwh'] = (float) ($getUtilities['total_electricity_kwh'] * $dataFactor['electricity'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['total_lpg'] = (float) ($getUtilities['total_lpg'] * $dataFactor['lpg'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['total_fuel_oil'] = (float) ($getUtilities['total_fuel_oil'] * $dataFactor['fuel_oil'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['total_natural_gas'] = (float) ($getUtilities['total_natural_gas'] * $dataFactor['natural_gas'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['district_heating'] = (float) ($getUtilities['district_heating'] * $dataFactor['district_heating'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['district_cooling'] = (float) ($getUtilities['district_cooling'] * $dataFactor['district_cooling'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrent']['water_total_consumption'] = (float) ($getUtilities['water_total_consumption'] * $dataFactor['water'] ?? 0);

				$data['sites'][$site_id]['dataUtilityCurrentYTD']['total_electricity_kwh'] = (float) ($getUtilitiesYTD['total_electricity_kwh'] * $dataFactor['electricity'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['total_lpg'] = (float) ($getUtilitiesYTD['total_lpg'] * $dataFactor['lpg'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['total_fuel_oil'] = (float) ($getUtilitiesYTD['total_fuel_oil'] * $dataFactor['fuel_oil'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['total_natural_gas'] = (float) ($getUtilitiesYTD['total_natural_gas'] * $dataFactor['natural_gas'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['district_heating'] = (float) ($getUtilitiesYTD['district_heating'] * $dataFactor['district_heating'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['district_cooling'] = (float) ($getUtilitiesYTD['district_cooling'] * $dataFactor['district_cooling'] ?? 0);
				$data['sites'][$site_id]['dataUtilityCurrentYTD']['water_total_consumption'] = (float) ($getUtilitiesYTD['water_total_consumption'] * $dataFactor['water'] ?? 0);
				
				$util->utilities_year  = $lastYear;
				$getUtilitiesLastYear = $util->getUtility();
				$getUtilitiesLastYearYTD = $util->getUtilityYTD();
				$data['sites'][$site_id]['dataUtilityLastYear']['total_electricity_kwh'] = (float) ($getUtilitiesLastYear['total_electricity_kwh'] * $dataFactor['electricity'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['total_lpg'] = (float) ($getUtilitiesLastYear['total_lpg'] * $dataFactor['lpg'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['total_fuel_oil'] = (float) ($getUtilitiesLastYear['total_fuel_oil'] * $dataFactor['fuel_oil'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['total_natural_gas'] = (float) ($getUtilitiesLastYear['total_natural_gas'] * $dataFactor['natural_gas'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['district_heating'] = (float) ($getUtilitiesLastYear['district_heating'] * $dataFactor['district_heating'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['district_cooling'] = (float) ($getUtilitiesLastYear['district_cooling'] * $dataFactor['district_cooling'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYear']['water_total_consumption'] = (float) ($getUtilitiesLastYear['water_total_consumption'] * $dataFactor['water'] ?? 0);

				$data['sites'][$site_id]['dataUtilityLastYearYTD']['total_electricity_kwh'] = (float) ($getUtilitiesLastYearYTD['total_electricity_kwh'] * $dataFactor['electricity'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['total_lpg'] = (float) ($getUtilitiesLastYearYTD['total_lpg'] * $dataFactor['lpg'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['total_fuel_oil'] = (float) ($getUtilitiesLastYearYTD['total_fuel_oil'] * $dataFactor['fuel_oil'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['total_natural_gas'] = (float) ($getUtilitiesLastYearYTD['total_natural_gas'] * $dataFactor['natural_gas'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['district_heating'] = (float) ($getUtilitiesLastYearYTD['district_heating'] * $dataFactor['district_heating'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['district_cooling'] = (float) ($getUtilitiesLastYearYTD['district_cooling'] * $dataFactor['district_cooling'] ?? 0);
				$data['sites'][$site_id]['dataUtilityLastYearYTD']['water_total_consumption'] = (float) ($getUtilitiesLastYearYTD['water_total_consumption'] * $dataFactor['water'] ?? 0);
				
				$this->load->model('reports/reports_model');
				$this->reports_model->site_id = $site_id;
				$baselineYear = $site_detail['baseline_regression_year'];
				$progressOnTargetMonthly = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear, 'month');
				$progressOnTarget = $this->reports_model->getProgressOnTargetWithBaseline($baselineYear);
				$landfillData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, 'landfill', $currentYear, $currMonth);
				$totalWasteData = $this->site_waste_model->getWasteYTDByDestinationAndCurrMonth($site_detail, '', $currentYear, $currMonth);

				$progressOnTarget[$baselineYear]['landfill_waste_target'] = isset($landfillData['YTDTotal'][$baselineYear]) ? $landfillData['YTDTotal'][$baselineYear] : 0;
				$progressOnTarget[$baselineYear]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0;
				$progressOnTarget[$running_year]['landfill_waste_target'] = isset($landfillData['YTDTotal'][$running_year]) ? $landfillData['YTDTotal'][$running_year] : 0;
				$progressOnTarget[$running_year]['total_waste_target'] = isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0;

				$progressValueWasteYTD = [
					'total_waste_baseline_target' => isset($totalWasteData['YTDTotal'][$baselineYear]) ? $totalWasteData['YTDTotal'][$baselineYear] : 0,
					'total_waste_target' => isset($totalWasteData['YTDTotal'][$running_year]) ? $totalWasteData['YTDTotal'][$running_year] : 0
				];
				$data['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
				$data['sites'][$site_id]['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
				$data['sites'][$site_id]['progressOnTargetMonthly'] = isset($progressOnTargetMonthly) ? $progressOnTargetMonthly : [];
				$dataNew = $currentMonth = $lastMonth = $sameMonthLastYear = [];
				$dataNew = $this->sites_model->getMySitesWidgetData($site_detail);
				$currentMonth = $dataNew[0] ?? [];
				$lastMonth = $dataNew[1] ?? [];
				$sameMonthLastYear = $dataNew[2] ?? [];

				$carbonData = [
					'carbon_footprint_currentMonth' => $currentMonth['carbon_footprint'] ?? 0,
					'carbon_footprint_SameMonthPreviousYear' => $sameMonthLastYear['carbon_footprint'] ?? 0,
					'ytd_carbon_footprint_new' => $dataNew['ytd_carbon_footprint_new'] ?? 0,
					'ytd_carbon_footprintPreviousYear' => $dataNew['ytd_carbon_footprintPreviousYear'] ?? 0,
					'ytd_carbon_footprint_baseline_new' => $dataNew['ytd_carbon_footprint_baseline_new'] ?? 0
				];
				$progressOnTargetResult = calculateProgressOnTarget(
					$progressOnTarget,
					$currMonth,
					$currentYear,
					$site_detials,
					$carbonData,
					$progressValueWasteYTD
				);
				$data['sites'][$site_id]['ProgressTargetPercentage'] = $progressOnTargetResult['ProgressTargetPercentage'];
				$data['sites'][$site_id]['progressTarget'] = $progressOnTargetResult['progressTarget'] ?? [];
				$data['sites'][$site_id]['progress_roomnight_YTD'] = $progressOnTargetResult['progress_roomnight_YTD'];
				$data['sites'][$site_id]['progress_baseline_roomnight_YTD'] = $progressOnTargetResult['progress_baseline_roomnight_YTD'];
				$data['sites'][$site_id]['progress_guestnight_YTD'] = $progressOnTargetResult['progress_guestnight_YTD'];
				$data['sites'][$site_id]['progress_baseline_guestnight_YTD'] = $progressOnTargetResult['progress_baseline_guestnight_YTD'];

				$data['sites'][$site_id]['progressOnTargetWasteYtd'] = $progressValueWasteYTD;
				$data['sites'][$site_id]['progressOnTarget'] = isset($progressOnTarget) ? $progressOnTarget : [];
				$data['sites'][$site_id]['progressOnTargetMonthly'] = isset($progressOnTargetMonthly) ? $progressOnTargetMonthly : [];
				$data['sites'][$site_id]['carbon_footprint_currentMonth'] = $currentMonth['carbon_footprint'] ?? 0;
				$data['sites'][$site_id]['total_utility_cost_currentMonth'] = $currentMonth['total_utility_cost'] ?? 0;
				$data['sites'][$site_id]['carbon_footprint_SameMonthPreviousYear'] = $sameMonthLastYear['carbon_footprint'] ?? 0;
				$data['sites'][$site_id]['total_utility_cost_lastMonth'] = $lastMonth['total_utility_cost'] ?? 0;
				$data['sites'][$site_id]['total_utility_cost_sameMonth_lastYear'] = $sameMonthLastYear['total_utility_cost'] ?? 0;
				
			}

			foreach ($data['sites'] as $siteId => $site) {

				$row = array_fill_keys($columns, '');
				$row['Hotel Name'] = $site['site_location_name'];
				$row['City'] = $site['city'];
				$carbonData = $site['progressTarget']['carbon'] ?? [];
				$widgetYtd = $site['progressOnTarget'];
				$utilityCurrent = $site['dataUtilityCurrent'];
				$utilityLast = $site['dataUtilityLastYear'];
				$utilityCurrentYTD = $site['dataUtilityCurrentYTD'];
				$utilityLastYTD = $site['dataUtilityLastYearYTD'];
				// --- Fill Excel columns ---
				$row['Room Nights '.$lastYear]        = fmt($widgetYtd[$lastYear]['room_night']);
				$row['Room Nights '.$currentYear]     = fmt($widgetYtd[$currentYear]['room_night']);
				$row['Room Nights Trend']        = varianceImg($widgetYtd[$currentYear]['room_night'], $widgetYtd[$lastYear]['room_night']);
				
				$row['Guest Nights '.$lastYear]        = fmt($widgetYtd[$lastYear]['guest_night']);
				$row['Guest Nights '.$currentYear]     = fmt($widgetYtd[$currentYear]['guest_night']);
				$row['Guest Nights Trend']        = varianceImg($widgetYtd[$currentYear]['guest_night'], $widgetYtd[$lastYear]['guest_night']);

				// Energy = electricity + cooling + heating + LPG + gas + fuel oil only (never water m³)
				$energyYtdLast = (float) ($utilityLastYTD['total_electricity_kwh'] ?? 0)
					+ (float) ($utilityLastYTD['district_cooling'] ?? 0)
					+ (float) ($utilityLastYTD['district_heating'] ?? 0)
					+ (float) ($utilityLastYTD['total_lpg'] ?? 0)
					+ (float) ($utilityLastYTD['total_natural_gas'] ?? 0)
					+ (float) ($utilityLastYTD['total_fuel_oil'] ?? 0);
				$energyYtdCurrent = (float) ($utilityCurrentYTD['total_electricity_kwh'] ?? 0)
					+ (float) ($utilityCurrentYTD['district_cooling'] ?? 0)
					+ (float) ($utilityCurrentYTD['district_heating'] ?? 0)
					+ (float) ($utilityCurrentYTD['total_lpg'] ?? 0)
					+ (float) ($utilityCurrentYTD['total_natural_gas'] ?? 0)
					+ (float) ($utilityCurrentYTD['total_fuel_oil'] ?? 0);
				$rnYtdLast = (float) ($widgetYtd[$lastYear]['room_night'] ?? 0);
				$rnYtdCurrent = (float) ($widgetYtd[$currentYear]['room_night'] ?? 0);
				$energyPerRnYtdLast = ($rnYtdLast > 0) ? ($energyYtdLast / $rnYtdLast) : 0;
				$energyPerRnYtdCurrent = ($rnYtdCurrent > 0) ? ($energyYtdCurrent / $rnYtdCurrent) : 0;
				$row['Energy kWh/Room Nights '.$lastYear] = fmt($energyPerRnYtdLast);
				$row['Energy kWh/Room Nights '.$currentYear] = fmt($energyPerRnYtdCurrent);
				$row['Energy kWh/Room Nights Trend'] = varianceImg($energyPerRnYtdCurrent, $energyPerRnYtdLast);

				$row['Carbon kgCO2'.$lastYear] = fmt($carbonData['carbon_last_YTD']);
				$row['Carbon kgCO2'.$currentYear] = fmt($carbonData['carbon_YTD']);
				$row['Carbon Trend'] = varianceImg($carbonData['carbon_YTD'], $carbonData['carbon_last_YTD']);

				$row['Carbon kgCO2/Guest Night'.$lastYear] = fmt($carbonData['carbon_last_YTD']/$widgetYtd[$lastYear]['guest_night']);
				$row['Carbon kgCO2/Guest Night'.$currentYear] = fmt($carbonData['carbon_YTD']/$widgetYtd[$currentYear]['guest_night']);
				$row['Carbon kgCO2/Guest Night Trend'] = varianceImg($carbonData['carbon_YTD']/$widgetYtd[$currentYear]['guest_night'], $carbonData['carbon_last_YTD']/$widgetYtd[$lastYear]['guest_night']);

				$row['Water m3/Guest Nights '.$lastYear] = ($widgetYtd[$lastYear]['guest_night'] ?? 0) > 0
					? fmt($widgetYtd[$lastYear]['water']/$widgetYtd[$lastYear]['guest_night'], 2) : 0;
				$row['Water m3/Guest Nights '.$currentYear] = ($widgetYtd[$currentYear]['guest_night'] ?? 0) > 0
					? fmt($widgetYtd[$currentYear]['water']/$widgetYtd[$currentYear]['guest_night'], 2) : 0;
				$row['Water m3/Guest Nights Trend'] = varianceImg($widgetYtd[$currentYear]['water']/$widgetYtd[$currentYear]['guest_night'], $widgetYtd[$lastYear]['water']/$widgetYtd[$lastYear]['guest_night']);

				$row['Electricity kWh '.$lastYear]    = fmt($utilityLastYTD['total_electricity_kwh']) ?? 0;
				$row['Electricity kWh '.$currentYear] = fmt($utilityCurrentYTD['total_electricity_kwh']) ?? 0;
				$row['Electricity kWh Trend']    = varianceImg(
					$utilityCurrentYTD['total_electricity_kwh'] ?? 0,
					$utilityLastYTD['total_electricity_kwh'] ?? 0
				);

				$row['Water m3'.$lastYear]    = fmt($utilityLastYTD['water_total_consumption']) ?? 0;
				$row['Water m3'.$currentYear] = fmt($utilityCurrentYTD['water_total_consumption']) ?? 0;
				$row['Water m3 Trend']    = varianceImg(
					$utilityCurrentYTD['water_total_consumption'] ?? 0,
					$utilityLastYTD['water_total_consumption'] ?? 0
				);

				$row['District Cooling kWh'.$lastYear]    = fmt($utilityLastYTD['district_cooling']) ?? 0;
				$row['District Cooling kWh'.$currentYear] = fmt($utilityCurrentYTD['district_cooling']) ?? 0;
				$row['District Cooling kWh Trend']    = varianceImg(
					$utilityCurrentYTD['district_cooling'] ?? 0,
					$utilityLastYTD['district_cooling'] ?? 0
				);

				$row['District Heating kWh'.$lastYear]    = fmt($utilityLastYTD['district_heating']) ?? 0;
				$row['District Heating kWh'.$currentYear] = fmt($utilityCurrentYTD['district_heating']) ?? 0;
				$row['District Heating kWh Trend']    = varianceImg(
					$utilityCurrentYTD['district_heating'] ?? 0,
					$utilityLastYTD['district_heating'] ?? 0
				);

				$row['Fuel Oil kWh'.$lastYear]    = fmt($utilityLastYTD['total_fuel_oil']) ?? 0;
				$row['Fuel Oil kWh'.$currentYear] = fmt($utilityCurrentYTD['total_fuel_oil']) ?? 0;
				$row['Fuel Oil kWh Trend']    = varianceImg(
					$utilityCurrentYTD['total_fuel_oil'] ?? 0,
					$utilityLastYTD['total_fuel_oil'] ?? 0
				);

				$row['Natural Gas kWh'.$lastYear]    = fmt($utilityLastYTD['total_natural_gas']) ?? 0;
				$row['Natural Gas kWh'.$currentYear] = fmt($utilityCurrentYTD['total_natural_gas']) ?? 0;
				$row['Natural Gas kWh Trend']    = varianceImg(
					$utilityCurrentYTD['total_natural_gas'] ?? 0,
					$utilityLastYTD['total_natural_gas'] ?? 0
				);

				$row['LPG kWh'.$lastYear]    = fmt($utilityLastYTD['total_lpg']) ?? 0;
				$row['LPG kWh'.$currentYear] = fmt($utilityCurrentYTD['total_lpg']) ?? 0;
				$row['LPG kWh Trend']    = varianceImg(
					$utilityCurrentYTD['total_lpg'] ?? 0,
					$utilityLastYTD['total_lpg'] ?? 0
				);
				$rowsYtd[$siteId] = $row;
				
				$rowCurrentMonth = array_fill_keys($columns, '');
				$rowCurrentMonth['Hotel Name'] = $site['site_location_name'];
				$rowCurrentMonth['City'] = $site['city'];
				$widgetCurrentMonth = $site['progressOnTargetMonthly'] ?? [];
				$widgetCurrentMonth[$currentYear] = $widgetCurrentMonth[$currentYear][$currMonth] ?? [];
				$widgetCurrentMonth[$lastYear] = $widgetCurrentMonth[$lastYear][$currMonth] ?? [];
				// Room Nights
				$rowCurrentMonth['Room Nights '.$lastYear]    = fmt($widgetCurrentMonth[$lastYear]['room_night']) ?? 0;
				$rowCurrentMonth['Room Nights '.$currentYear] = fmt($widgetCurrentMonth[$currentYear]['room_night']) ?? 0;
				$rowCurrentMonth['Room Nights Trend']    = varianceImg(
					$widgetCurrentMonth[$currentYear]['room_night'] ?? 0,
					$widgetCurrentMonth[$lastYear]['room_night'] ?? 0
				);

				// Guest Nights
				$rowCurrentMonth['Guest Nights '.$lastYear]    = fmt($widgetCurrentMonth[$lastYear]['guest_night']) ?? 0;
				$rowCurrentMonth['Guest Nights '.$currentYear] = fmt($widgetCurrentMonth[$currentYear]['guest_night']) ?? 0;
				$rowCurrentMonth['Guest Nights Trend']    = varianceImg(
					$widgetCurrentMonth[$currentYear]['guest_night'] ?? 0,
					$widgetCurrentMonth[$lastYear]['guest_night'] ?? 0
				);

				// Energy = electricity + cooling + heating + LPG + gas + fuel oil only (never water m³)
				$energyMonthLast = (float) ($utilityLast['total_electricity_kwh'] ?? 0)
					+ (float) ($utilityLast['district_cooling'] ?? 0)
					+ (float) ($utilityLast['district_heating'] ?? 0)
					+ (float) ($utilityLast['total_lpg'] ?? 0)
					+ (float) ($utilityLast['total_natural_gas'] ?? 0)
					+ (float) ($utilityLast['total_fuel_oil'] ?? 0);
				$energyMonthCurrent = (float) ($utilityCurrent['total_electricity_kwh'] ?? 0)
					+ (float) ($utilityCurrent['district_cooling'] ?? 0)
					+ (float) ($utilityCurrent['district_heating'] ?? 0)
					+ (float) ($utilityCurrent['total_lpg'] ?? 0)
					+ (float) ($utilityCurrent['total_natural_gas'] ?? 0)
					+ (float) ($utilityCurrent['total_fuel_oil'] ?? 0);
				$rnMonthLast = (float) ($widgetCurrentMonth[$lastYear]['room_night'] ?? 0);
				$rnMonthCurrent = (float) ($widgetCurrentMonth[$currentYear]['room_night'] ?? 0);
				$energyPerRnLast = ($rnMonthLast > 0) ? ($energyMonthLast / $rnMonthLast) : 0;
				$energyPerRnCurrent = ($rnMonthCurrent > 0) ? ($energyMonthCurrent / $rnMonthCurrent) : 0;
				$rowCurrentMonth['Energy kWh/Room Nights '.$lastYear]    = fmt($energyPerRnLast);
				$rowCurrentMonth['Energy kWh/Room Nights '.$currentYear] = fmt($energyPerRnCurrent);
				$rowCurrentMonth['Energy kWh/Room Nights Trend']        = varianceImg($energyPerRnCurrent, $energyPerRnLast);

				// Water
				$waterPerGnLast = (($widgetCurrentMonth[$lastYear]['guest_night'] ?? 0) > 0)
					? ($widgetCurrentMonth[$lastYear]['water'] / $widgetCurrentMonth[$lastYear]['guest_night']) : 0;
				$waterPerGnCurrent = (($widgetCurrentMonth[$currentYear]['guest_night'] ?? 0) > 0)
					? ($widgetCurrentMonth[$currentYear]['water'] / $widgetCurrentMonth[$currentYear]['guest_night']) : 0;
				$rowCurrentMonth['Water m3/Guest Nights '.$lastYear]    = fmt($waterPerGnLast, 2);
				$rowCurrentMonth['Water m3/Guest Nights '.$currentYear] = fmt($waterPerGnCurrent, 2);
				$rowCurrentMonth['Water m3/Guest Nights Trend']    = varianceImg($waterPerGnCurrent, $waterPerGnLast);

				// Carbon
				$rowCurrentMonth['Carbon kgCO2'.$lastYear]    = fmt($carbonData['carbon_last_monthly']);
				$rowCurrentMonth['Carbon kgCO2'.$currentYear] = fmt($carbonData['carbon_monthly']);
				$rowCurrentMonth['Carbon Trend']    = varianceImg(
					$carbonData['carbon_monthly'],
					$carbonData['carbon_last_monthly']
				);

				// Carbon kgCO2/ Guest Night
				$carbonPerGnLast = (($widgetCurrentMonth[$lastYear]['guest_night'] ?? 0) > 0)
					? ($carbonData['carbon_last_monthly'] / $widgetCurrentMonth[$lastYear]['guest_night']) : 0;
				$carbonPerGnCurrent = (($widgetCurrentMonth[$currentYear]['guest_night'] ?? 0) > 0)
					? ($carbonData['carbon_monthly'] / $widgetCurrentMonth[$currentYear]['guest_night']) : 0;
				$rowCurrentMonth['Carbon kgCO2/Guest Night'.$lastYear]    = fmt($carbonPerGnLast);
				$rowCurrentMonth['Carbon kgCO2/Guest Night'.$currentYear] = fmt($carbonPerGnCurrent);
				$rowCurrentMonth['Carbon kgCO2/Guest Night Trend']    = varianceImg($carbonPerGnCurrent, $carbonPerGnLast);

				$rowCurrentMonth['Electricity kWh '.$lastYear]    = fmt($utilityLast['total_electricity_kwh']) ?? 0;
				$rowCurrentMonth['Electricity kWh '.$currentYear] = fmt($utilityCurrent['total_electricity_kwh']) ?? 0;
				$rowCurrentMonth['Electricity kWh Trend']    = varianceImg(
					$utilityCurrent['total_electricity_kwh'] ?? 0,
					$utilityLast['total_electricity_kwh'] ?? 0
				);

				$rowCurrentMonth['Water m3'.$lastYear]    = fmt($utilityLast['water_total_consumption']) ?? 0;
				$rowCurrentMonth['Water m3'.$currentYear] = fmt($utilityCurrent['water_total_consumption']) ?? 0;
				$rowCurrentMonth['Water m3 Trend']    = varianceImg(
					$utilityCurrent['water_total_consumption'] ?? 0,
					$utilityLast['water_total_consumption'] ?? 0
				);

				$rowCurrentMonth['District Cooling kWh'.$lastYear]    = fmt($utilityLast['district_cooling']) ?? 0;
				$rowCurrentMonth['District Cooling kWh'.$currentYear] = fmt($utilityCurrent['district_cooling']) ?? 0;
				$rowCurrentMonth['District Cooling kWh Trend']    = varianceImg(
					$utilityCurrent['district_cooling'] ?? 0,
					$utilityLast['district_cooling'] ?? 0
				);

				$rowCurrentMonth['District Heating kWh'.$lastYear]    = fmt($utilityLast['district_heating']) ?? 0;
				$rowCurrentMonth['District Heating kWh'.$currentYear] = fmt($utilityCurrent['district_heating']) ?? 0;
				$rowCurrentMonth['District Heating kWh Trend']    = varianceImg(
					$utilityCurrent['district_heating'] ?? 0,
					$utilityLast['district_heating'] ?? 0
				);

				$rowCurrentMonth['Fuel Oil kWh'.$lastYear]    = fmt($utilityLast['total_fuel_oil']) ?? 0;
				$rowCurrentMonth['Fuel Oil kWh'.$currentYear] = fmt($utilityCurrent['total_fuel_oil']) ?? 0;
				$rowCurrentMonth['Fuel Oil kWh Trend']    = varianceImg(
					$utilityCurrent['total_fuel_oil'] ?? 0,
					$utilityLast['total_fuel_oil'] ?? 0
				);

				$rowCurrentMonth['Natural Gas kWh'.$lastYear]    = fmt($utilityLast['total_natural_gas']) ?? 0;
				$rowCurrentMonth['Natural Gas kWh'.$currentYear] = fmt($utilityCurrent['total_natural_gas']) ?? 0;
				$rowCurrentMonth['Natural Gas kWh Trend']    = varianceImg(
					$utilityCurrent['total_natural_gas'] ?? 0,
					$utilityLast['total_natural_gas'] ?? 0
				);

				$rowCurrentMonth['LPG kWh'.$lastYear]    = fmt($utilityLast['total_lpg']) ?? 0;
				$rowCurrentMonth['LPG kWh'.$currentYear] = fmt($utilityCurrent['total_lpg']) ?? 0;
				$rowCurrentMonth['LPG kWh Trend']    = varianceImg(
					$utilityCurrent['total_lpg'] ?? 0,
					$utilityLast['total_lpg'] ?? 0
				);

				$rowsCurrentMonth[$siteId] = $rowCurrentMonth;
			}
		}

		// foreach ($rowsYtd as $row) {
		// 	$colIndex = 0;
		// 	foreach ($columns as $col) {
		// 		$objPHPExcel->getActiveSheet()
		// 			->setCellValueByColumnAndRow($colIndex++, $rowNum, $row[$col]);
		// 	}
		// 	$rowNum++;
		// }
		$sheet = $objPHPExcel->getActiveSheet();
		foreach ($rowsYtd as $row) {
			$colIndex = 0;
			foreach ($columns as $colName) {
				if (strpos($colName, 'Trend') !== false) {
					$direction = $row[$colName];
					// Occupancy: green up / red down. Consumption metrics: red up / green down.
					$useOccupancyArrows = ($colName === 'Room Nights Trend' || $colName === 'Guest Nights Trend');
					addVarianceImage($sheet, $colIndex, $rowNum, $direction, false, $useOccupancyArrows);
					$sheet->setCellValueByColumnAndRow($colIndex, $rowNum, '');
				} else {
					$sheet->setCellValueByColumnAndRow(
						$colIndex,
						$rowNum,
						$row[$colName]
					);
				}
				$colIndex++;
			}
			$rowNum++;
		}

		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(1);
		applyHeaderColors($objPHPExcel->getActiveSheet());
		autoSizeColumns($objPHPExcel->getActiveSheet());
		$reportDate = DateTime::createFromFormat('Y-n-d',"$currentYear-$currMonth-01");
		$sheetTitle = $reportDate->format('F Y');
		$objPHPExcel->getActiveSheet()->setTitle('Month - ' . $sheetTitle);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Hotel Name');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'City');
		$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
		$startCol = 2;
		foreach ($parentHeading as $heading) {
			$colStart = PHPExcel_Cell::stringFromColumnIndex($startCol);
			$colEnd   = PHPExcel_Cell::stringFromColumnIndex($startCol + 2);

			$objPHPExcel->getActiveSheet()->setCellValue($colStart . '1', $heading);
			$objPHPExcel->getActiveSheet()->mergeCells($colStart . '1:' . $colEnd . '1');

			$startCol += 3;
		}
		$colIndex = 0;
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValue('A2', '');
		$sheet->setCellValue('B2', '');
		$startCol = 2;
		foreach ($parentHeading as $heading) {
			foreach ($childLabels as $label) {
				$sheet->setCellValueByColumnAndRow($startCol, 2, $label);
				$startCol++;
			}
		}
		$rowNum = 3;
		// foreach ($rowsCurrentMonth as $row) {
		// 	$colIndex = 0;
		// 	foreach ($columns as $col) {
		// 		$objPHPExcel->getActiveSheet()
		// 			->setCellValueByColumnAndRow($colIndex++, $rowNum, $row[$col]);
		// 	}
		// 	$rowNum++;
		// }
		foreach ($rowsCurrentMonth as $row) {
			$colIndex = 0;
			foreach ($columns as $colName) {
				if (strpos($colName, 'Trend') !== false) {
					$direction = $row[$colName];
					// Occupancy: green up / red down. Consumption metrics: red up / green down.
					$useOccupancyArrows = ($colName === 'Room Nights Trend' || $colName === 'Guest Nights Trend');
					addVarianceImage($sheet, $colIndex, $rowNum, $direction, false, $useOccupancyArrows);
					$sheet->setCellValueByColumnAndRow($colIndex, $rowNum, '');
				} else {
					$sheet->setCellValueByColumnAndRow(
						$colIndex,
						$rowNum,
						$row[$colName]
					);
				}
				$colIndex++;
			}
			$rowNum++;
		}
		$sheet0 = $objPHPExcel->getSheet(0);
		$highestRow0 = $sheet0->getHighestRow();
		$highestColumn0 = $sheet0->getHighestColumn();
		$sheet0->setAutoFilter("A2:{$highestColumn0}{$highestRow0}");
		$sheet0->getStyle("C3:{$highestColumn0}{$highestRow0}")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// $sheet0->getStyle("C3:{$highestColumn0}{$highestRow0}")
		// 	->getNumberFormat()
		// 	->setFormatCode('#,##0.00');
		$headerRow = 2;
		foreach (range('C', $highestColumn0) as $col) {
			$header = $sheet0->getCell("{$col}{$headerRow}")
				->getValue();
			if (stripos($header, 'trend') !== false) {
				continue;
			}
			$sheet0->getStyle("{$col}3:{$col}{$highestRow0}")
				->getNumberFormat()
				->setFormatCode('#,##0.00');
		}


		$sheet1 = $objPHPExcel->getSheet(1);
		$highestRow1 = $sheet1->getHighestRow();
		$highestColumn1 = $sheet1->getHighestColumn();
		$sheet1->setAutoFilter("A2:{$highestColumn1}{$highestRow1}");
		$sheet1->getStyle("C3:{$highestColumn1}{$highestRow1}")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$headerRow = 2;
		$sheet1->getStyle("C3:{$highestColumn1}{$highestRow1}")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		foreach (range('C', $highestColumn1) as $col) {
			$header = $sheet1->getCell("{$col}{$headerRow}")
				->getValue();
			if (stripos($header, 'trend') !== false) {
				continue;
			}
			$sheet1->getStyle("{$col}3:{$col}{$highestRow1}")
				->getNumberFormat()
				->setFormatCode('#,##0.00');
		}
        header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Group Utility Report.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if($isAttachment) {
            $reportsDir = BASE_PATH_CUSTOM . '/assets/cron/reports';
			if (!is_dir($reportsDir)) {
				@mkdir($reportsDir, 0755, true);
			}
			$filename = 'group_utility_report_' . $currentYear . '_' . $currMonth . '.xls';
			$filePath = $reportsDir . '/' . $filename;
			$objWriter->save($filePath);
			return $filePath;
        } else {
            // Default: stream to browser
		    return $objWriter->save('php://output');
        }
    }

}
