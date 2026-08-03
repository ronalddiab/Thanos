<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity_log_model extends Base_Model
{

    /**
     * Function save_activity_log to insert activity data
     */
    public function save_activity_log($data)
    {
        $this->db->set('created', 'NOW()',FALSE);
        $this->db->insert(TBL_ACTIVITY_LOG, $data);
        
        return true;
    }

}
