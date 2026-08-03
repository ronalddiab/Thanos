<?php

/**
 *  survey_submit_log Model
 *
 *
 * @package CIDemoApplication
 * @subpackage Survey
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Survey_submit_log extends Base_Model
{
    protected $_table = TBL_SURVEY_SUBMIT_LOG;
 
    public function insert_survey_submit_log()
    {        
        $data_array = array();
        if (isset($this->user_id)) {
            $data_array['user_id'] = $this->user_id;
        }
        if (isset($this->site_id)) {
            $data_array['site_id'] = $this->site_id;
        }

        $data_action = 'Create';
        $this->db->set($data_array);
        $id = $this->db->insert($this->_table);

        // Save audit trail
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        saveAuditTrail($user_id, $site_id, 'Survey Submit log entry', $data_action);

        return $id;         
    }

    public function get_survey_submit_log()
    {
        $this->db->select('*')
                ->from($this->_table)
                ->where(array('user_id' => $this->user_id, 'site_id' => $this->site_id));
        $query = $this->db->get();
        return $query->row_array();
    }
}
