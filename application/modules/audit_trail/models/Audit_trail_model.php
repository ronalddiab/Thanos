<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Audit_trail_model extends Base_Model {
    protected $_tbl_utilities = 'utilities_cost';
    protected $_tbl_site_custom_notifications = TBL_SITE_CUSTOM_NOTIFICATIONS;
    protected $_tbl_sites = 'sites';
    protected $_tbl_login_log   = 'user_login_log';
    protected $_tbl_audit_trail = 'audit_trail';
    protected $_tbl_users = TBL_USERS;
    protected $_tbl_roles = TBL_ROLES;

    public $search_term_module = '';

            
    function __construct() {
        parent::__construct();
        
    }
    
    function log_listing($filters){
        $this->db->select('l.*,u.firstname,u.username, s.site_location_name');
        $this->db->from($this->_tbl_audit_trail.' as l');
        $this->db->join($this->_tbl_users . ' as u', ('u.id = l.user_id'));
        $this->db->join($this->_tbl_sites . ' as s', ('l.site_id = s.id'));
        if(!empty($filters)){
            if(array_key_exists('user_id', $filters)){
                $this->db->where('user_id', $filters['user_id']);
            }
            if(array_key_exists('start_date', $filters)){
                $this->db->where('DATE(l.created) >=', $filters['start_date']);
            }
            if(array_key_exists('end_date', $filters)){
                $this->db->where('DATE(l.created) <=', $filters['end_date']);
            }
            if ($this->search_site != "") {
                $this->db->where('l.site_id', $this->search_site);
                $this->db->where('l.user_id != ', 1);
            }
            /* if(array_key_exists('role_id', $filters)){
                $this->db->where('l.role_id', $filters['role_id']);
            }*/
            if(array_key_exists('sort_by', $filters)){
                if($filters['sort_by'] == 'site_id')
                {
                    $this->db->order_by('s.site_location_name', $filters['sort_order']);
                }                    
                else
                {
                    $this->db->order_by($filters['sort_by'], $filters['sort_order']);
                }                
            }
        }

        if ($this->search_term_module != "") {
            $this->db->like("LOWER(l.module_name)", strtolower($this->search_term_module));
        }

        $this->db->order_by('l.id','DESC');
    }
    
    function getLog($filters = array()){
        
        $this->log_listing($filters);
        $this->db->limit($this->record_per_page, $this->offset);
        
        $result = $this->db->get();
        // echo $this->db->last_query();
        // exit;
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
