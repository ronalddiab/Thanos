<?php

/**
 *  Survey Model
 *
 *  To perform queries related to user management.
 *
 * @package CIDemoApplication
 * @subpackage Survey
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Survey_access_model extends Base_Model
{
    protected $_table = TBL_SURVEY_ACCESS;
 
    public function insert_survey_access()
    {        
        $data_array = array();
        if (isset($this->user_id)) {
            $data_array['user_id'] = $this->user_id;
        }
        if (isset($this->close_date)) {
            $data_array['close_date'] = $this->close_date;
        }

        $data_action = 'Create';
        $this->db->set($data_array);
        $id = $this->db->insert($this->_table);

        // Save audit trail
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        saveAuditTrail($user_id, $site_id, 'Survey Form Opened', $data_action);

        return $id;         
    }

    public function deleteExistingEntry($user_id)
    {
        $this->db->where('user_id',$user_id);
        $this->db->delete($this->_table);
    }
}
