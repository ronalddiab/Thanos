<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_login_log_model extends Base_Model {
    protected $_tbl_utilities = 'utilities_cost';
    protected $_tbl_site_custom_notifications = TBL_SITE_CUSTOM_NOTIFICATIONS;
    protected $_tbl_sites = 'sites';
    protected $_tbl_login_log = 'user_login_log';
    protected $_tbl_users = TBL_USERS;
    protected $_tbl_roles = TBL_ROLES;
            
    function __construct() {
        parent::__construct();
        
    }
    
    function log_listing($filters){
        $this->db->select('l.*,u.firstname,u.username,r.role_name,');
        $this->db->from($this->_tbl_login_log.' as l');
        $this->db->join($this->_tbl_users . ' as u', ('u.id = l.user_id'));
        $this->db->join($this->_tbl_roles . ' as r', ('l.role_id = r.id'));
        if(!empty($filters)){
            if(array_key_exists('user_id', $filters)){
                $this->db->where('user_id', $filters['user_id']);
            }
            if(array_key_exists('start_date', $filters)){
                $this->db->where('DATE(logged_in_at) >=', $filters['start_date']);
            }
            if(array_key_exists('end_date', $filters)){
                $this->db->where('DATE(logged_in_at) <=', $filters['end_date']);
            }
            if(array_key_exists('role_id', $filters)){
                $this->db->where('l.role_id', $filters['role_id']);
            }
            if(array_key_exists('sort_by', $filters)){
                $this->db->order_by($filters['sort_by'], $filters['sort_order']);
            }
        }
        $this->db->order_by('logged_in_at','DESC');
    }
    
    function getLog($filters = array()){
        
        $this->log_listing($filters);
        $this->db->limit($this->record_per_page, $this->offset);
        
        $result = $this->db->get();
//        echo $this->db->last_query();
//        exit;
        return $result->result_array();
        
    }
    function getLogCount($filters = array()){
        $this->log_listing($filters);
        return $this->db->count_all_results();
    }
    
    function addLog($data = array()){
        if(!empty($data)){
            $data['logged_in_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->_tbl_login_log, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }
    
    function getRoles(){
        $this->db->select('*');
        $this->db->from($this->_tbl_roles);
        $result = $this->db->get();
        return $result->result_array();
    }
}
