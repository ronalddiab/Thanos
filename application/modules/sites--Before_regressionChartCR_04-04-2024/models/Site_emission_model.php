<?php

/**
 *  Site Emission Model
 *
 *  To perform queries related to user management.
 *
 * @package CIDemoApplication
 * @subpackage Site Emission
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Site_Emission_model extends Base_Model
{
    protected $_table = TBL_SITE_EMISSION;
    public $site_id = "";
    // public $user_id = "";
    public $year_id = "";
    
    public function get_site_emission_model_detail_by_siteId()
    {
        if(!isset($this->year_id) && empty($this->year_id)) {
            $this->year_id = null;
        } else {
            $this->year_id = (int) $this->year_id;
        }
        // if(!isset($this->user_id) && empty($this->user_id)) {
        //     $this->user_id = null;
        // } else {
        //     $this->user_id = (int) $this->user_id;
        // }
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);
        $this->db->where('s.site_id', $this->site_id);
        // $this->db->where('s.user_id', $this->user_id);
        $this->db->where('s.year_id', $this->year_id);
        $this->db->from($this->_table . ' AS s');
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function insert_site_emission to insert record
     */
    function insert_site_emission() {

        $data_array = array();

        if(isset($this->site_id)) {
            $data_array['site_id'] = $this->site_id;
        }

        // if(isset($this->user_id)) {
        //     $data_array['user_id'] = $this->user_id;
        // }

        if(isset($this->year_id)) {
            $data_array['year_id'] = $this->year_id;
        }

        if(isset($this->electricity_emission_factor_percentage)) {
            $data_array['electricity_emission_factor_percentage'] = $this->electricity_emission_factor_percentage;
        }

        if(isset($this->electricity_emission_factor)) {
            $data_array['electricity_emission_factor'] = $this->electricity_emission_factor;
        }

        if(isset($this->fuel_emission_factor)) {
            $data_array['fuel_emission_factor'] = $this->fuel_emission_factor;
        }

        if(isset($this->lpg_emission_factor)) {
            $data_array['lpg_emission_factor'] = $this->lpg_emission_factor;
        }

        if(isset($this->natural_gas_emission_factor)) {
            $data_array['natural_gas_emission_factor'] = $this->natural_gas_emission_factor;
        }

        if(isset($this->district_cooling_emission_factor)) {
            $data_array['district_cooling_emission_factor'] = $this->district_cooling_emission_factor;
        }

        if(isset($this->district_heating_emission_factor)) {
            $data_array['district_heating_emission_factor'] = $this->district_heating_emission_factor;
        }

        if(isset($this->status)) {
            $data_array['status'] = $this->status;
        }

        if (isset($this->emission_upload)) {
            $data_array['emission_upload'] = $this->emission_upload;
        }
        
        $dataAlreadyExist = $this->get_site_emission_model_detail_by_siteId(); 
        if(empty($dataAlreadyExist)) {
            $data_array['created_at'] = GetCurrentDateTime();
            $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Create';
            
            $this->db->set($data_array);
            $id = $this->db->insert($this->_table);
        } else {
            $data_array['modify_at'] = GetCurrentDateTime();
            $data_array['modify_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $data_action = 'Update';

            $this->db->where(array('site_id' => $this->site_id));//,'user_id' => $this->user_id
            if(isset($this->year_id) && !empty($this->year_id)) {
                $this->db->where(array('year_id' => $this->year_id));
            } else {
                $this->db->where(array('year_id' => NULL,'month_id' => NULL));
            }
            $this->db->set($data_array);
            $id = $this->db->update($this->_table);
        }

        // Save audit trail
        $site_id = $this->site_id;
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        saveAuditTrail($this->user_id, $this->site_id, 'Site Emission', $data_action);

        return $id; 
    }

    public function delete_entry_ifexist($data){
        $site_id  = $data['site_id'];
        $year_id  = $data['year_id'];
        // $user_id  = $data['user_id'];

        $this->db->where('site_id',$site_id);
        $this->db->where('year_id',$year_id); 
        // $this->db->where('user_id',$user_id); 
       return $this->db->delete($this->_table);
    }

    public function delete_emission_image($dataArray) {
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);

        if(isset($dataArray['site_emission_id']) && !empty($dataArray['site_emission_id']) && $dataArray['site_emission_id'] != 0) {
            $this->db->where('s.site_emission_id', $dataArray['site_emission_id']);
        }
        $this->db->from($this->_table . ' AS s');
        $result = $this->db->get()->row_array();

        if(isset($result) && !empty($result)) {
            $allFiles = explode("|", $result['emission_upload']);
            $deleteIndex = array_search($dataArray['emission_upload'], $allFiles);
            array_splice($allFiles, $deleteIndex, 1);
            $data_array['emission_upload'] = isset($allFiles) && !empty($allFiles) ? implode("|", $allFiles) : NULL;
            $this->db->where(array('site_emission_id' => $dataArray['site_emission_id']));
            $this->db->set($data_array);
            $this->db->update($this->_table);
            return true;
        } else {
            return false;
        }
    }

}
