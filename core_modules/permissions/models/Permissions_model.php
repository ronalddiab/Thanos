<?php

/**
 *  Permission Model
 *
 *  To perform queries related to role management.
 * 
 * @package CIDemoApplication
 * @subpackage Permissions
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Permissions_model extends Base_Model {

    protected $_tbl_roles = TBL_ROLES;
    protected $_tbl_users = TBL_USERS;
    protected $_tbl_permission = TBL_PERMISSIONS;
    protected $_tbl_role_permission = TBL_ROLE_PERMISSION;
    protected $_tbl_user_permission = TBL_USER_PERMISSION;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";

    function __construct() {
        parent::__construct();
    }

    /**
     * Function get_permission_listing to fetch all records of permissions
     */
    function get_permission_listing() {
        if (isset($this->search_term) && $this->search_term != "") {
            $this->db->like("LOWER(P.permission_title)", strtolower($this->search_term));
        }
        if (isset($this->sort_by) && $this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by('P.' . $this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('P.*');
        $this->db->from($this->_tbl_permission . ' AS P');
        $this->db->where('P.status !=', -1);
        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }

        return $result;
    }

    /**
     * Function matrix_permissions to fetch all records of permissions
     */
    function matrix_permissions() {

        $this->db->select('id, permission_label, permission_title, parent_id');
        $this->db->from($this->_tbl_permission);
        $this->db->where('status !=', -1);
        $this->db->order_by('permission_title');
        $result = $this->db->get();
        return $result;
    }

    /**
     * Function matrix_permissions_list to generate drop down of by parent permission
     * @param integer $parent_id   
     * @param integer $count default = 0
     */
    function matrix_permissions_list($parent_id, $count = 0) {
        //Type casting
        $parent_id = intval($parent_id);

        static $option_results;

        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('id,parent_id,permission_label, permission_title');
        $this->db->from($this->_tbl_permission);
        $this->db->where('parent_id', $parent_id);
        $this->db->where('status', 1);
        $result = $this->db->get();

        $get_options = $result->result_array();
        $num_options = count($get_options);
        // our permission is apparently valid, so go ahead €¦
        if ($num_options > 0) {
            foreach ($get_options as $row) {
                $permission_id = $row['id'];
                $option_results[$row['id']]['id'] = $row['id'];
                $option_results[$row['id']]['permission_lable'] = $row['permission_label'];
                $option_results[$row['id']]['permission_title'] = $row['permission_title'];
                $option_results[$row['id']]['parent_id'] = $row['parent_id'];
                // now call the function again, to recurse through the child permissions
                $this->matrix_permissions_list($row['id'], $count);
            }
        }
        return $option_results;
    }

    function user_matrix_permissions_list($parent_id, $count = 0, $user_id = 0) {
        //Type casting
        $this->db->select('role_id');
        $this->db->from($this->_tbl_users);
        $this->db->where('id', $user_id);
        $result = $this->db->get();

        $results = $result->row_array();
        $role_id = $results['role_id'];

        $parent_id = intval($parent_id);
        static $option_results;
        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('pr.id,pr.parent_id,pr.permission_label, pr.permission_title');
        $this->db->from($this->_tbl_permission . ' AS pr');
        $this->db->join($this->_tbl_role_permission . ' as rp', 'pr.id = rp.permission_id', 'left');
        //$this->db->where('pr.parent_id', $parent_id);
        $this->db->where('rp.role_id', $role_id);
        $this->db->where('pr.status', 1);
        $result1 = $this->db->get();

        $get_options = $result1->result_array();
        $num_options = count($get_options);

        // our permission is apparently valid, so go ahead €¦
        if ($num_options > 0) {
            foreach ($get_options as $row) {
                $permission_id = $row['id'];
                $option_results[$row['id']]['id'] = $row['id'];
                $option_results[$row['id']]['permission_lable'] = $row['permission_label'];
                $option_results[$row['id']]['permission_title'] = $row['permission_title'];
                $option_results[$row['id']]['parent_id'] = $row['parent_id'];
                // now call the function again, to recurse through the child permissions
                $this->matrix_permissions_list($row['id'], $count);
            }
        }

        return $option_results;
    }

    /**
     * Function get_permission_by_id to fetch records of permissions by id
     * @param integer $id default = 0
     */
    function get_permission_by_id($id = 0) {
        //Type casting
        $id = intval($id);

        $this->db->select('*');
        $this->db->from($this->_tbl_permission);
        $this->db->where('id', $id);
        $result = $this->db->get();

        return $result->row_array();
    }

    /**
     * Function get_permission_by_id to add/update permissions
     * @param array $data
     */
    public function save_permissions($data) {
        //Type casting
        $id = intval($data['id']);

        if ($id != 0 && $id != "") {
            $this->db->where('id', $id);
            $this->db->update($this->_tbl_permission, $data);
            $id = $id;
        } else {
            $this->db->insert($this->_tbl_permission, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * Function delete_permission to delete permissions
     * @param integer $id
     */
    public function delete_permission($id) {
        //Type casting
        $id = intval($id);

        $this->db->where('id', $id);
        return $this->db->delete($this->_tbl_permission);
    }

    /**
     * Function get_permission_id_by_name to check if permission declared or not
     * @param integer $id
     */
    function get_permission_id_by_name($permission = NULL) {
        $this->db->select("id");
        $this->db->from($this->_tbl_permission);
        $this->db->where("(LOWER(permission_label) LIKE '%{$permission}%')");
        $this->db->where('status !=', -1);
        $result = $this->db->get();

        return $result->result_array();
    }

    /**
     * Function get_parent to fetch all parent records
     */
    function get_parent() {

        $this->db->select('id,permission_title');
        $this->db->from($this->_tbl_permission);
        $this->db->where('parent_id', 0);
        $result = $this->db->get();

        return $result->result_array();
    }

    /**
     * Function get_parent_selectlist to generate list for drop-down of permission
     * @param integer $parent_id
     * @param integer $count
     */
    function get_parent_selectlist($parent_id, $count) {

        static $option_results;
        $indent_flag = '';
        // if there is no current permission id set, start off at the top level (zero)
        if (!isset($parent_id)) {
            $parent_id = 0;
        }
        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('id,permission_title,parent_id');
        $this->db->from($this->_tbl_permission);
        $this->db->where('parent_id', $parent_id);
        $this->db->where('status', 1);
        $this->db->order_by('UPPER(permission_title)', 'asc');
        $result = $this->db->get();

        $get_options = $result->result_array();
        $num_options = count($get_options);

        // our permission is apparently valid, so go ahead €¦
        if ($num_options > 0) {
            foreach ($get_options as $row) {
                // if its not a top-level permission, indent it to
                //show that its a child permission
                if ($parent_id != 0) {
                    $indent_flag = '&nbsp;&nbsp;-';
                    for ($x = 2; $x <= $count; $x++) {
                        $indent_flag .= '->';
                    }
                }
                $permission_title = $indent_flag . $row['permission_title'];
                $option_results[$row['id']] = $permission_title;
                // now call the function again, to recurse through the child permissions
                $this->get_parent_selectlist($row['id'], $count);
            }
        }

        return $option_results;
    }

    /**
     * Function get_child_permission_array to fetch all child permissions as array
     * @param integer $parent_id
     * @param integer $count default = 0
     */
    function get_child_permission_array($parent_id, $count = 0) {

        static $option_results;
        $indent_flag = '';
        // if there is no current permission id set, start off at the top level (zero)
        if (!isset($parent_id)) {
            $parent_id = 0;
        }
        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('id,parent_id');
        $this->db->from($this->_tbl_permission);
        $this->db->where('parent_id', $parent_id);
        $this->db->where('status', 1);
        $result = $this->db->get();

        $get_options = $result->result_array();
        $num_options = count($get_options);

        // our permission is apparently valid, so go ahead €¦
        if ($num_options > 0) {
            foreach ($get_options as $row) {
                $permission_id = $row['id'];
                $option_results[$row['id']] = $permission_id;
                // now call the function again, to recurse through the child permissions
                $this->get_child_permission_array($row['id'], $count);
            }
        }

        return $option_results;
    }

    function allowed_permission_list($role_id = NULL) {
        $this->db->select('P.permission_label', FALSE);
        $this->db->from($this->_tbl_role_permission . ' as RP');
        $this->db->join($this->_tbl_permission . ' as P', 'RP.permission_id = P.id', 'left');
        $this->db->where('RP.role_id', $role_id);
        //$this->db->group_by('M.id');
        $result = $this->db->get();

        return $this->db->custom_result($result);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id 
     */
    public function inactive_records($id = array()) {
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_permission);

        return true;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_permission);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id 
     */
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_permission);

        return true;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_permission);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id 
     */
    public function delete_records($id = array()) {
        $this->db->where_in('id', $id);
        return $this->db->delete($this->_tbl_permission);
    }

    function get_all_permission_by_id($id = 0) {
        //Type casting
        $id = intval($id);

        $this->db->select('permission_label, permission_title ,(select permission_title from ' . $this->_tbl_permission . ' as sp where sp.id=p.parent_id) as title ,status');
        $this->db->from($this->_tbl_permission . ' as p');
        $this->db->where('id', $id);
        $result = $this->db->get();
        return $result->row_array();
    }

    function allowed_user_permission_list($user_id = NULL) {
        $this->db->select('p.permission_label', FALSE);
        $this->db->from($this->_tbl_user_permission . ' as up');
        $this->db->join($this->_tbl_permission . ' as p', 'up.permission_id = p.id', 'left');
        $this->db->where('up.user_id', $user_id);
        $result = $this->db->get();
        return $this->db->custom_result($result);
    }
    
    function custom_user_matrix_permissions_list($parent_id, $count = 0, $user_id = 0) {
        //Type casting
        $this->db->select('role_id');
        $this->db->from($this->_tbl_users);
        $this->db->where('id', $user_id);
        $result = $this->db->get();

        $results = $result->row_array();
        $role_id = $results['role_id'];

        $parent_id = intval($parent_id);
        static $option_results;
        // increment the counter by 1
        $count = $count + 1;

        $this->db->select('pr.id,pr.parent_id,pr.permission_label, pr.permission_title');
        $this->db->from($this->_tbl_permission . ' AS pr');
        $this->db->join($this->_tbl_role_permission . ' as rp', 'pr.id = rp.permission_id', 'left');
        //$this->db->where('pr.parent_id', $parent_id);        
        if($role_id == 4) {
            //$this->db->where("(rp.role_id = ".$role_id. " OR rp.role_id = 3) ", NULL, FALSE);
        }
        //$this->db->where('rp.role_id', $role_id);
        $this->db->where('pr.status', 1, true);
        $result1 = $this->db->get();

        $get_options = $result1->result_array();
        $num_options = count($get_options);

        // our permission is apparently valid, so go ahead
        if ($num_options > 0) {
            foreach ($get_options as $row) {
                $permission_id = $row['id'];
                $option_results[$row['id']]['id'] = $row['id'];
                $option_results[$row['id']]['permission_lable'] = $row['permission_label'];
                $option_results[$row['id']]['permission_title'] = $row['permission_title'];
                $option_results[$row['id']]['parent_id'] = $row['parent_id'];
                // now call the function again, to recurse through the child permissions
                $this->matrix_permissions_list($row['id'], $count);
            }
        }

        return $option_results;
    }
}
