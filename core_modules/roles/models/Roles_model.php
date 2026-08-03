<?php

/**
 *  Role Model
 *
 *  To perform queries related to role management.
 *
 * @package CIDemoApplication
 * @subpackage Roles
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Roles_model extends Base_Model {

    protected $_tbl_roles = TBL_ROLES;
    protected $_tbl_users = TBL_USERS;
    protected $_tbl_role_permission = TBL_ROLE_PERMISSION;
    protected $_tbl_user_permission = TBL_USER_PERMISSION;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";

    function __construct() {
        parent::__construct();
    }

    /**
     * Function get_role_listing to fetch all records of roles
     */
    function get_role_listing() {
        if (isset($this->search_term) && $this->search_term != "") {
            $this->db->like("LOWER(R.role_name)", strtolower($this->search_term));
        }
        if (isset($this->sort_by) && $this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by('R.' . $this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('R.*');
        $this->db->from($this->_tbl_roles . ' AS R');
        $this->db->where('R.status !=', -1);
        $query = $this->db->get();
        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }

        return $result;
    }

    /**
     * Function get_role_listing to fetch id,role_name from roles
     */
    function matrix_roles($role_id) {

        //$role_array = array('1','2');
        //if (!in_array($role_id, $role_array)) {
        if ($role_id != 1) {
            $this->db->where('R.id >', $role_id);
            $this->db->where('R.id !=', 6);
        }

        $this->db->select('R.id,R.role_name');
        $this->db->from($this->_tbl_roles . ' AS R');
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

    function matrix_roles_for_permissions() {
        $this->db->select('R.id,R.role_name');
        $this->db->from($this->_tbl_roles . ' AS R');
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);

        return $result;
    }

    /**
     * Function get_role_listing to fetch all records of role_permission
     */
    function get_all_role_permissions() {
        $this->db->select('RP.*');
        $this->db->from($this->_tbl_role_permission . ' AS RP');
        $query = $this->db->get();
        $result = $this->db->custom_result($query);

        return $result;
    }

    /**
     * Function get_role_by_id to fetch record of role by id
     * @param int $id default = 0
     */
    function get_role_by_id($id = 0) {
        //Type Casting
        $id = intval($id);

        $this->db->select('*');
        $this->db->from($this->_tbl_roles);
        $this->db->where('id', $id);
        $result = $this->db->get();

        return $result->row_array();
    }

    /**
     * Function save_role to add/update roles
     * @param array $data
     */
    public function save_role($data) {
        //Type Casting
        $id = intval($data['id']);

        if ($id != 0 && $id != "") {
            $this->db->where('id', $id);
            $this->db->update($this->_tbl_roles, $data);
            $id = $id;
        } else {
            $this->db->insert($this->_tbl_roles, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * Function delete_role to delete roles
     * @param int $id
     */
    public function delete_role($id) {
        //Type Casting
        $id = intval($id);

        $this->db->where('id', $id);
        return $this->db->delete($this->_tbl_roles);
    }

    /**
     * Function insert_permission to assign permission to role
     * @param array $data
     */
    function insert_permission($data = array()) {

        $this->db->set($data);
        $this->db->insert($this->_tbl_role_permission);

        // return $this->db->_error_number(); // return the error occurred in last query
        $error =  $this->db->error(); // return the error occurred in last query
        if(isset($error['message']) && $error['message'] != ''){
            return $error['message'];
        }     
        return true;
    }

    /**
     * Function delete_permission to delete permission for role
     * @param int $role_id default = 0
     * @param int $permission_id default = 0
     */
    function delete_permission($role_id = 0, $permission_id = 0) {
        //Type Casting
        $role_id = intval($role_id);
        $permission_id = intval($permission_id);

        $this->db->where('role_id', $role_id);
        $this->db->where('permission_id', $permission_id);
        return $this->db->delete($this->_tbl_role_permission);
    }

    /**
     * Function check_permission to check module permission by permission_id & role_id
     * @param int $role_id default = 0
     * @param int $permission_id default = 0
     */
    function check_permission($role_id = 0, $permission_id = 0) {
        //Type Casting
        $role_id = intval($role_id);
        $permission_id = intval($permission_id);

        $this->db->select('*');
        $this->db->from($this->_tbl_role_permission);
        $this->db->where("role_id", $role_id);
        $this->db->where("permission_id", $permission_id);
        $result = $this->db->get();

        return $result->result_array();
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_roles);

        return true;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {

        // Getting Roles who have users
        $get_used_role = $this->get_used_role();
        $get_used_role = implode(',', $get_used_role);

        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->where('id NOT IN (' . $get_used_role . ')');
        $this->db->update($this->_tbl_roles);
        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_roles);

        return true;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_roles);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array()) {
        $this->db->where_in('id', $id);
        return $this->db->delete($this->_tbl_roles);
    }

    /**
     * Function get_user_permissions_by_id to fetch all records of user_permission by user_id
     */
    function get_user_permissions_by_id($user_id = 0) {
        //Type Casting
        $user_id = intval($user_id);

        $this->db->select('UP.*');
        $this->db->from($this->_tbl_user_permission . ' AS UP');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);

        return $result;
    }

    /**
     * Function get_user_permissions_by_id to fetch all records of user_permission by user_id
     */
    function get_role_permissions_by_id($role_id = 0) {
        //Type Casting
        $role_id = intval($role_id);

        $this->db->select('RP.*');
        $this->db->from($this->_tbl_role_permission . ' AS RP');
        $this->db->where('role_id', $role_id);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);

        return $result;
    }

    /**
     * Function delete_user_permission to delete permission for user
     * @param int $user_id default = 0
     */
    function delete_user_permission($user_id = 0) {
        //Type Casting
        $user_id = intval($user_id);

        $this->db->where('user_id', $user_id);
        return $this->db->delete($this->_tbl_user_permission);
    }

    /**
     * Function insert_user_permission to assign permission to user
     * @param array $data
     */
    function insert_user_permission($data = array()) {
        $this->db->set($data);
        $r = $this->db->insert($this->_tbl_user_permission);
        // return $this->db->_error_number(); // return the error occurred in last query
        $error =  $this->db->error(); // return the error occurred in last query
        if(isset($error['message']) && $error['message'] != ''){
            return $error['message'];
        }     
        return true;
    }

    /**
     * Function delete_role_permission to empty role_permission table
     */
    function delete_role_permission() {
        $this->db->empty_table($this->_tbl_role_permission);
    }

    function get_used_role() {
        $this->db->select('distinct(role_id)');
        $this->db->from('users');
        $this->db->where('status', '1');
        $query = $this->db->get();

        $roleArr = array();

        if (!empty($query)) {
            foreach ($query->result_array() as $row) {
                $roleArr[] = $row['role_id'];
            }
        }
        return $roleArr;
    }

    function reset_user_permission($user_id) {

        $this->db->select("role_id");
        $this->db->from($this->_tbl_users);
        $this->db->where('id', $user_id);
        $result = $this->db->get();
        $result = $result->result_array();
        $role_id = $result[0]['role_id'];
        
        $this->db->select("permission_id");
        $this->db->from($this->_tbl_role_permission);
        $this->db->where('role_id', $role_id);
        $result_permission = $this->db->get();
        $result_permissions = $result_permission->result_array();
        $final_array = array();
        for ($i = 0; $i < count($result_permissions); $i++) {
            $final_array[$i] = $result_permissions[$i]['permission_id'];
        }

        foreach ($final_array as $final_array_prmission) {
            $data['user_id'] = $user_id;
            $data['permission_id'] = $final_array_prmission;
            $this->db->set($data);
            $this->db->insert($this->_tbl_user_permission);
        }
    }

}
