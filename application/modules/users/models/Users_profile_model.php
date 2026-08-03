<?php

/**
 *  Users Model
 *
 *  To perform queries related to user management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Users_Profile_model extends Base_Model {

    protected $_tbl_user_profile = TBL_USER_PROFILE;

    /**
     * Function save_user to add/update user
     * @param array $data for user table
     * @param array $data_profile for user_profile table
     */
    public function save_user($data_profile) {

        if (isset($data_profile['user_id'])) {
            $userprofile_data ['user_id'] = $data_profile['user_id'];
        }
        if (isset($data_profile['gender'])) {
            $userprofile_data ['gender'] = $data_profile['gender'];
        }
        if (isset($data_profile['forumavtar'])) {
            $userprofile_data ['forumavtar'] = $data_profile['forumavtar'];
        }
        if (isset($data_profile['forumname'])) {
            $userprofile_data ['forumname'] = $data_profile['forumname'];
        }
        
        if (isset($this->id) && $this->id != 0 && $this->id != "") {
            $result = $this->db->query("SELECT * FROM {$this->_tbl_user_profile} WHERE user_id = {$this->id}");
            $isresult = $result->row_array();
            if(!empty($isresult)){
                $this->db->where('user_id', $this->id);
                $this->db->update($this->_tbl_user_profile, $userprofile_data);
            }else{
                $userprofile_data['user_id'] = $this->id;
                $this->db->insert($this->_tbl_user_profile, $userprofile_data);
            }
        } else {
            $this->db->insert($this->_tbl_user_profile, $userprofile_data);
        }
        return true;
    }


    public function save_profile($data_profile) {

        if (isset($this->user_id)) {
            $userprofile_data['user_id'] = $this->user_id;
        }
        if (isset($data_profile['gender'])) {
            $userprofile_data ['gender'] = $data_profile['gender'];
        }
        if (isset($data_profile['avtar'])) {
            $userprofile_data ['avtar'] = $data_profile['avtar'];
        }
        if (isset($data_profile['forumname'])) {
            $userprofile_data ['forumname'] = $data_profile['forumname'];
        }
        
        if (isset($this->user_id) && $this->user_id != 0 && $this->user_id != "") {
            $result = $this->db->query("SELECT * FROM {$this->_tbl_user_profile} WHERE user_id = {$this->user_id} LIMIT 0,1");
            if(!empty($result->row_array())){
                $this->db->where('user_id', $this->user_id);
                $this->db->update($this->_tbl_user_profile, $userprofile_data);
            }else{
                $userprofile_data['user_id'] = $this->user_id;
                $this->db->insert($this->_tbl_user_profile, $userprofile_data);
            }
        } else {
            $this->db->insert($this->_tbl_user_profile, $userprofile_data);
        }
        return true;
    }
}
