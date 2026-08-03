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
    public $search_term                       = "";
    public $sort_by                           = "";
    public $sort_order                        = "";
    public $_record_count;

    public function get_site_listing($site_id, $role_id)
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

        $this->db->select('s.*, r.region_name,h.hotel_name,c.country');
        $this->db->from($this->_tbl_sites . ' AS s');
        $this->db->join($this->_tbl_regions . ' as r', 's.region_id = r.id', 'left');
        $this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');
        $this->db->join($this->_tbl_hotels . ' as h', 's.hotel_id = h.id', 'left');
        $this->db->where('s.status !=', -1);
        if ($role_id != 1) {
            $this->db->where('s.id', $site_id);
        }
        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
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

        if ($this->search_term != "") {
            $this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
        }
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        else
        {
            $this->db->order_by('s.site_location_name', 'asc');
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('s.id,s.site_location_name');
        $this->db->from($this->_tbl_sites . ' AS s');
        $this->db->where('s.status', 1);

        if ($role_id != 1 && $role_id != 2) {
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
        $this->db->select('s.id,s.hotel_name');
        $this->db->from($this->_tbl_sites . ' AS s');
        $this->db->where('s.status !=', -1);
        $result = $this->db->get();
        $return = array();
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $value) {
                $return[$value['id']] = $value['hotel_name'];
            }
        }

        return $return;
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
            return $siteArray;
        } else {
            return array();
        }
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
            return $siteArray;
        } else {
            return array();
        }
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
		
		if (isset($data['is_used_in_cron'])) {
            $site_data['is_used_in_cron'] = $data['is_used_in_cron'];
        }
		
        if (isset($data['threshold'])) {
            $site_data['threshold'] = $data['threshold'];
        }

        if (isset($data['chsb_reporting'])) {
            $site_data['chsb_reporting'] = $data['chsb_reporting'];
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

        if (isset($data['status'])) {
            $site_data['status'] = $data['status'];
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
        $additional_fields = $data['hotel_name'].' ('.$data['site_location_name'].')';
        saveAuditTrail($user_id, $site_id, 'Site - '.$additional_fields, $data_action); 

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
            saveAuditTrail($user_id, $site_id, 'Site - Set Notification - ('.$additional_field.')',$data_action);
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
        
        if(!empty($filterArray)){
            if(isset($filterArray['month']) && !empty($filterArray['month'])){
                $this->db->where('MONTH(date) =',$filterArray['month']);
            }
            if(isset($filterArray['year']) && !empty($filterArray['year'])){
                $this->db->where('YEAR(date) =',$filterArray['year']);
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
			if(isset($post_data['is_used_in_cron'][$id]) || (isset($post_data['is_used_in_cron'][$key]) && !$id)){
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
	
	public function save_daily_reading_utilites_setting($site_id = 0,$post_data = array())
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
		if($result){
			foreach($result as $key => $value){
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

        $query     = $this->db->where("status", 1)->get($this->_tbl_sites);
        $siteArray = $query->result_array();

        if (!empty($siteArray)) {
            return $siteArray;
        } else {
            return array();
        }
    }

    public function get_energy_modelling($filterArray = array())
    {
        $energyData = array();

        $this->db->from($this->_tbl_energy_modelling);
        $this->db->where('year_id =', $filterArray['year']);
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
                ];
            }
        } else {
            $energyData[$energy['utility']] = [
                'cdd'       => 0,
                'hdd'       => 0,
                'occupancy' => 0,
                'x'         => 0,
                'days'      => 0,
            ];
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
            ];

            $this->db->from($this->_tbl_energy_modelling);
            $this->db->where('year_id =', $value['year_id']);
            $this->db->where('site_id =', $value['site_id']);
            $this->db->where('utility =', $key);

            if ($this->db->count_all_results() > 0) {

                $this->db->where('year_id =', $value['year_id']);
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
        $this->db->from($this->_tbl_site_measures_reading. ' as r');
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

        if ($role_id != 1 && $role_id != 2) {
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

        if ($role_id != 1 && $role_id != 2) {
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
        return $data;
    }

}
