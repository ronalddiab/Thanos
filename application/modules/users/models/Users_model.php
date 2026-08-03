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

class Users_model extends Base_Model

{



    protected $_tbl_users = TBL_USERS;

    protected $_tbl_user_only = TBL_USER;

    protected $_tbl_roles = TBL_ROLES;

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_role_permission = TBL_ROLE_PERMISSION;

    protected $_tbl_user_permission = TBL_USER_PERMISSION;

    protected $_tbl_user_cron_notifications = 'user_cron_notifications';

    public $search_term_firstname = "";

    public $search_term_username = "";

    public $sort_by = "";

    public $sort_order = "";

    public $_record_count;



    /**

     * Function save_user to add/update user

     * @param array $data for user table

     * @param array $data_profile for user_profile table

     */

    public function save_user($data)

    {


        if (isset($data['id'])) {

            $user_data['id'] = $data['id'];
        }

        if (isset($data['role_id'])) {

            $user_data['role_id'] = $data['role_id'];
        }

        if (isset($data['site_id'])) {

            $user_data['site_id'] = $data['site_id'];
        }

        if (isset($data['firstname'])) {

            $user_data['firstname'] = $data['firstname'];
        }

        if (isset($data['lastname'])) {

            $user_data['lastname'] = $data['lastname'];
        }

        if (isset($data['email'])) {

            $user_data['email'] = $data['email'];
        }

        if (isset($data['password'])) {

            $user_data['password'] = $data['password'];
        }

        if (isset($data['last_login'])) {

            $user_data['last_login'] = $data['last_login'];
        }

        if (isset($data['status'])) {

            $user_data['status'] = $data['status'];
        }

        if (isset($data['created'])) {

            $user_data['created'] = $data['created'];
        }

        if (isset($data['modified'])) {

            $user_data['modified'] = $data['modified'];
        }

        if (isset($data['status'])) {

            $user_data['status'] = $data['status'];
        }

        if (isset($user_data['id']) && $user_data['id'] != 0 && $user_data['id'] != "") {

            $this->db->where('id', $user_data['id']);

            $this->db->update($this->_tbl_users, $user_data);

            $id = $user_data['id'];

            $data_action = 'Update';
        } else {



            $this->db->set('created', 'NOW()', FALSE);

            if ($this->db->insert($this->_tbl_users, $data)) {

                $id = $this->db->insert_id();
            }

            $data_action = 'Create';
        }

        // Code to save audit log

        $additional_field = $data['firstname'];

        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];

        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];

        saveAuditTrail($user_id, $site_id, 'User (' . $additional_field . ')', $data_action);



        return $id;
    }



    function delete_site_to_user($user_id)

    {

        $this->db->where('user_id', $user_id);

        $this->db->delete('user_sites');



        return true;
    }

    function delete_region_to_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('user_regions');

        return true;
    }

    function delete_reports_to_user($user_id)

    {

        $this->db->where('user_id', $user_id);

        $this->db->delete($this->_tbl_user_cron_notifications);



        return true;
    }



    function get_site_to_user($user_id)

    {

        $this->db->select('*');

        $this->db->from('user_sites');

        $this->db->where('user_id', $user_id);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    function get_region_to_user($user_id)
    {
        $this->db->select('*');
        $this->db->from('user_regions');
        $this->db->where('user_id', $user_id);

        $query = $this->db->get();

        $result = $query->result_array();



        return $result;
    }



    function assign_site_to_user($sites = array(), $user_id)

    {

        if (!empty($sites)) {

            $data = array();

            $data['user_id'] = $user_id;

            foreach ($sites as $key => $value) {

                $data['site_id'] = $value;

                $this->db->set($data);

                $this->db->insert('user_sites');
            }
        }

        return true;
    }

    function assign_region_to_user($regions = array(), $user_id)
    {
        if (!empty($regions)) {
            $data = array();
            $data['user_id'] = $user_id;
            foreach ($regions as $key => $value) {
                $data['region_id'] = $value;
                $this->db->set($data);
                $this->db->insert('user_regions');
            }
        }

        return true;
    }



    function assign_report_to_user($sites = array(), $user_id)

    {

        if (!empty($sites)) {

            $data = array();

            $data['user_id'] = $user_id;

            foreach ($sites as $key => $value) {

                $data['notifications'] = $value;

                $this->db->set($data);

                $this->db->insert($this->_tbl_user_cron_notifications);
            }
        }



        return true;
    }



    function assign_permissions_to_user($role_id, $inserted_id)

    {

        $this->db->select("permission_id");

        $this->db->from($this->_tbl_role_permission);

        $this->db->where('role_id', $role_id);

        $result = $this->db->get();

        $result = $result->result_array();

        $final_array = array();

        for ($i = 0; $i < count($result); $i++) {

            $final_array[$i] = $result[$i]['permission_id'];
        }



        foreach ($final_array as $final_array_prmission) {

            $data['user_id'] = $inserted_id;

            $data['permission_id'] = $final_array_prmission;

            $this->db->set($data);

            $this->db->insert($this->_tbl_user_permission);
        }
    }



    public function save_profile($data, $data_profile)

    {



        if (isset($data['id'])) {

            $user_data['id'] = $data['id'];
        }

        if (isset($data['role_id'])) {

            $user_data['role_id'] = $data['role_id'];
        }

        if (isset($data['firstname'])) {

            $user_data['firstname'] = $data['firstname'];
        }

        if (isset($data['lastname'])) {

            $user_data['lastname'] = $data['lastname'];
        }

        if (isset($data['email'])) {

            $user_data['email'] = $data['email'];
        }





        if (isset($data['status'])) {

            $user_data['status'] = $data['status'];
        }





        if (isset($user_data['id']) && $user_data['id'] != 0 && $user_data['id'] != "") {

            $this->db->where('id', $user_data['id']);

            $this->db->update($this->_tbl_users, $user_data);

            $id = $user_data['id'];

            $this->users_profile_model->id = $id;

            $this->users_profile_model->save_user($data_profile);
        } else {



            $this->db->set('created', 'NOW()', FALSE);

            if ($this->db->insert($this->_tbl_users, $data)) {

                $id = $this->db->insert_id();

                $data_profile['user_id'] = $id;

                $this->users_profile_model->save_user($data_profile);
            }
        }

        return $id;
    }



    /**

     * Function update_last_login to update last_login field

     * @param integer $id

     */

    public function update_last_login($id = 0)

    {

        //Type Casting

        $id = intval($id);



        $this->db->set('last_login', 'NOW()', FALSE);

        $this->db->where('id', $id);

        $this->db->update($this->_tbl_users);



        return $id;
    }



    /**

     * Function login to do login

     */

    function login()

    {

        $username = trim($this->username);

        $password = $this->password;

        //$sites = (int) $this->security->xss_clean($this->site);



        $this->db->join($this->_tbl_roles . ' as r', ('u.role_id = r.id OR u.id = 1'), 'left');

        $this->db->where("u.username", $username);

        $this->db->where("u.status != ", '-1');

        $this->db->where("u.role_id IN (1,2,3,4,5,6)");

        // $this->db->where("u.role_id !=", '6');

        //$this->db->where("u.site_id =", $sites);

        $this->db->where("r.status  ", '1');

        $this->db->where("u.password", encriptsha1($password));

        $query = $this->db->get($this->_tbl_users . ' AS u');

        if ($query->num_rows() > 0) {

            $userData = $this->db->custom_result($query);

            $adminRolesIdsArray = array(1, 2, 3, 6);

            $isActive = '';

            if (!in_array($userData[0]['u']['role_id'], $adminRolesIdsArray)) {

                $isActive = $this->isActiveSite($userData[0]['u']['site_id']);

                if ($isActive) {

                    return $userData;
                }
            }

            return $this->db->custom_result($query);
        }

        return false;
    }



    /**

     * Function role_list to get listing of active roles

     */

    function role_list($id)

    {

        if ($this->user_Id == $this->edit_user_Id) {

            $this->db->where('id >=', $id);

            $this->db->where('id !=', '5');
        } else if ($id != 1) {

            $this->db->where('id >', $id);

            $this->db->where('id !=', '5');
        }



        $this->db->select("id,role_name");

        $this->db->from($this->_tbl_roles);

        $this->db->where('status', 1);

        $this->db->order_by('role_name', 'asc');

        $result = $this->db->get();



        if ($result->num_rows() > 0) {

            $result = $result->result_array();

            foreach ($result as $role) {

                $roles[$role['id']] = $role['role_name'];
            }

            return $roles;
        } else {

            return NULL;
        }
    }



    function role_list_moderator($id)

    {

        if ($this->user_Id == $this->edit_user_Id) {

            $this->db->where('id >=', $id);
        } else if ($id != 1) {

            $this->db->where('id >', $id);
        }



        $this->db->select("id,role_name");

        $this->db->from($this->_tbl_roles);

        $this->db->where('status', 1);

        $this->db->order_by('role_name', 'asc');

        $result = $this->db->get();



        if ($result->num_rows() > 0) {

            $result = $result->result_array();

            foreach ($result as $role) {

                $roles[$role['id']] = $role['role_name'];
            }

            return $roles;
        } else {

            return NULL;
        }
    }



    function get_role_name_by_id($role_id)

    {

        $this->db->select("role_name");

        $this->db->from($this->_tbl_roles);

        $this->db->where('id', $role_id);

        $result = $this->db->get();

        $result = $result->result_array();

        return $result[0]['role_name'];
    }



    function get_default_role()

    {

        $this->db->select("id");

        $this->db->from($this->_tbl_roles);

        $this->db->where('default', 1);

        $result = $this->db->get();



        return $result->row_array();
    }



    /**

     * Function get_user_detail to return user array of particular id

     * @param integer $id

     */

    function get_user_detail($id = 0)

    {

        //Type Casting

        $id = intval($id);



        $this->db->where("id", $id);

        $this->db->where_in("status", array(1, 0));

        $tableusers = $this->db->get($this->_tbl_users);

        $userArray = $tableusers->row_array();



        if (!empty($userArray)) {

            $this->db->where("user_id", $id);

            $tableuserprofile = $this->db->get('user_profile');

            $userArray += $tableuserprofile->row_array();

            return $userArray;
        } else {

            return '';
        }
    }



    /**

     * Function get_user_report to return user report array of particular id

     * @param integer $id

     */

    function get_user_reports($id = 0)

    {

        //Type Casting

        $id = intval($id);

        $reportArray = array();

        $resultArray = array();

        $this->db->select('*');

        $this->db->from($this->_tbl_user_cron_notifications);

        $this->db->where("user_id", $id);

        $reports = $this->db->get();

        $reportArray = $reports->result_array();

        foreach ($reportArray as $report) {

            $resultArray[] = array(

                'id' => $report['id'],

                'user_id' => $report['user_id'],

                'reports' => $report['notifications'],

            );
        }



        return $resultArray;
    }



    /**

     * Function changepassword to change user password

     * @param integer $user_id default = 0

     */

    function changepassword($user_id = 0, $password = NULL)

    {

        //Type Casting

        $user_id = intval($user_id);

        $password = trim(strip_tags($password));



        if ($user_id != 0 && $user_id) {

            $data['password'] = encriptsha1($password);



            $this->db->where('id', $user_id);

            $this->db->update($this->_tbl_users, $data);
        }
    }



    /**

     * Function get_user_listing to fetch all records of users

     * @param integer $user_id default = 0

     */

    function get_user_listing($user_id, $site_id, $role_id)

    {



        $role_array = array(1, 2);

        if ($this->search_term_firstname != "") {

            $this->db->like("LOWER(u.firstname)", strtolower($this->search_term_firstname));
        }

        if ($this->search_term_username != "") {

            $this->db->like("LOWER(u.username)", strtolower($this->search_term_username));
        }

        if ($this->search_site != "") {

            $this->db->where('us.site_id =', $this->search_site);
        }

        if ($this->sort_by != "" && $this->sort_order != "") {

            $this->db->order_by($this->sort_by, $this->sort_order);
        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {

            $this->db->limit($this->record_per_page, $this->offset);
        }



        if ($role_id != 1) {

            if (!in_array($role_id, $role_array)) {

                $this->db->where('u.site_id', $site_id);
            }

            $this->db->where('u.role_id ', $role_id);
            $this->db->where('u.id', $user_id);
        }

        $this->db->select('u.*,r.role_name,s.site_location_name');

        $this->db->from($this->_tbl_users . ' AS u');

        $this->db->join($this->_tbl_roles . ' as r', 'u.role_id = r.id', 'left');

        $this->db->join($this->_tbl_sites . ' as s', 'u.site_id = s.id', 'left');



        if ($this->search_site != "") {

            $this->db->join('user_sites as us', 'us.user_id = u.id', 'left');
        }



        $this->db->where('u.status !=', -1);



        $query = $this->db->get();

        //echo $this->db->last_query();exit;

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($query));
        } else {

            return $this->db->custom_result($query);

            //pre($this->db->custom_result($query));

        }
    }



    function get_user_list_helper()

    {

        $this->db->select('u.id,u.username');

        $this->db->from($this->_tbl_users . ' AS u');

        $this->db->where('u.status !=', -1);



        $result = $this->db->get();

        $return = array();

        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $value) {

                $return[$value['id']] = $value['username'];
            }
        }



        return $return;
    }



    /**

     * Function check front login

     */

    public function check_front_login($email, $password)

    {

        $email = trim($email);

        $password = $password;



        $this->db->select('u.*');

        $this->db->where("u.email", $email);

        $this->db->where("u.status != ", -1);

        $this->db->where("u.password", encriptsha1($password));

        $query = $this->db->get($this->_tbl_users . " as u");



        if ($query->num_rows() == 1) {

            return $this->db->custom_result($query);
        }

        return false;
    }



    /**

     * Function do activation

     * @params $activation_key for update activation key

     */

    function activation($activation_key)

    {





        $this->db->select("u.*");

        $this->db->where('u.activation_code', '' . $activation_key . '');



        $useres = $this->db->get($this->_tbl_users . ' as u');

        //  echo $this->db->last_query();exit;

        $user_data = $this->db->custom_result($useres);



        if (isset($user_data)) {

            $activation_expiry = $user_data[0]['u']['activation_expiry'];

            $now_date = date("Y-m-d H:i:s");

            if (strtotime($now_date) < strtotime($activation_expiry)) {

                $data = array(

                    'activation_code' => "",

                    'status' => 1

                );

                $this->db->where('activation_code', '' . $activation_key . '');

                $this->db->update($this->_tbl_users, $data);

                $flag = 1;
            } else {

                $flag = 2;
            }
        } else {

            $flag = 3;
        }

        return $flag;
    }



    /**

     * Function get user data by passing email id

     */

    function get_user_detail_by_email($email = NULL)

    {

        //Type casting

        $email = strip_tags($email);

        $this->db->select("u.*");

        $this->db->where("u.email", $email);

        $this->db->where("u.status", '1');

        $this->db->where("u.role_id !=", '1');



        $useres = $this->db->get($this->_tbl_users . ' as u');

        return $this->db->custom_result($useres);
    }



    /**

     * Function for set autogenerate password and send email for new password

     */

    function forgot_password($data = array())

    {

        //Type casting

        $email = strip_tags($data['email']);



        $this->db->where('email', $email);

        $this->db->where('status', '1');

        $this->db->update($this->_tbl_users, $data);
    }



    /**

     * Function delete_user to delete user

     * @param integer $id

     */

    public function delete_user($id)

    {

        //Type Casting

        $id = intval($id);



        $this->db->where('id', $id);

        $date = new DateTime();

        $ctimestamp = $date->getTimestamp();

        $this->db->set('username', "CONCAT(username, '-$ctimestamp')", false);

        $this->db->set('status', '-1');

        return $this->db->update($this->_tbl_users);
    }



    /**

     * Function check_group_status to check Role status

     * @param integer $id default = 0

     */

    function check_group_status($id = 0)

    {

        //Type Casting

        $id = intval($id);



        if ($id != 0) {

            $this->db->select("status");

            $this->db->where("id", $id);

            $groupData = $this->db->get($this->_tbl_roles);

            $groupData = $groupData->row_array();

            return $groupData['status'];
        } else {

            return 0;
        }
    }



    /**

     * Function check_unique_mail to check duplicate emails

     * @param array $data

     */

    function check_unique_email($data)

    {

        $email = trim(strip_tags($data['email']));

        $user_id = intval($data['id']);



        $this->db->select('id,email');

        $this->db->from($this->_tbl_users);

        if (isset($user_id) && $user_id != '' && $user_id != 0) {

            $this->db->where('id != ', $user_id);
        }

        $this->db->where('LOWER(email) = ', mb_strtolower($email, 'UTF-8'));

        $this->db->where('status != ', -1);



        $this->db->limit(1);

        $result = $this->db->get()->num_rows();

        return $result;
    }



    function check_unique_username($data)

    {

        $username = trim(strip_tags($data['username']));

        $user_id = intval($data['id']);



        $this->db->select('id,username');

        $this->db->from($this->_tbl_users);

        if (isset($user_id) && $user_id != '' && $user_id != 0) {

            $this->db->where('id != ', $user_id);
        }

        $this->db->where('LOWER(username) = ', mb_strtolower($username, 'UTF-8'));

        $this->db->where('status != ', -1);



        $this->db->limit(1);

        $result = $this->db->get()->num_rows();

        return $result;
    }



    /**

     * Function update_activation_key to update activate field in DB

     * @param string $activation_key

     */

    function update_activation_key($activation_key)

    {

        $data = array(

            'activation_code' => get_random_string(),

            'activation_expiry' => date('Y:m:d H:i:s', strtotime('+1 day', now()))

        );

        $this->db->where('activation_code', '' . $activation_key . '');

        $this->db->update($this->_tbl_users, $data);

        return $data['activation_code'];
    }



    /**

     * Function update_activation_key to update activate field in DB

     * @param string $activation_key

     */

    function get_user_data_by_activation_key($activation_key)

    {

        $this->db->select("u.*");

        $this->db->where('u.activation_code', '' . $activation_key . '');



        $useres = $this->db->get($this->_tbl_users . ' as u');



        $user_data = $this->db->custom_result($useres);

        return $user_data[0]['U'];
    }



    /**

     * Function inactive_records to inactive records

     * @param array $id

     */

    public function inactive_records($id = array())

    {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 0);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_users);



        return $id;
    }



    /**

     * Function inactive_all_records to inactive all records without deleted records

     */

    public function inactive_all_records()

    {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 0);

        $this->db->where('status !=', -1);

        $this->db->where('id !=', 1);

        $this->db->update($this->_tbl_users);



        return true;
    }



    /**

     * Function active_records to active records

     * @param array $id

     */

    public function active_records($id = array())

    {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 1);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_users);



        return $id;
    }



    /**

     * Function active_all_records to active all records without deleted records

     */

    public function active_all_records()

    {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 1);

        $this->db->where('status !=', -1);

        $this->db->where('id !=', 1);

        $this->db->update($this->_tbl_users);



        return true;
    }



    /**

     * Function delete_records to delete URL

     * @param integer $id

     */

    public function delete_records($id = array())

    {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->where_in('id', $id);

        $date = new DateTime();

        $ctimestamp = $date->getTimestamp();

        $this->db->set('username', "CONCAT(username, '-$ctimestamp')", false);

        $this->db->set('status', '-1');

        return $this->db->update($this->_tbl_users);
    }



    function get_sites_list()

    {

        $this->db->select('id,site_location_name');

        $this->db->from($this->_tbl_sites);

        $this->db->where('status =', 1);

        $this->db->order_by('site_location_name', 'asc');

        $result = $this->db->get();



        if ($result->num_rows() > 0) {

            $result = $result->result_array();

            foreach ($result as $site) {

                if ($site['site_location_name'] != '') {

                    $sites[$site['id']] = $site['site_location_name'];
                }
            }

            return $sites;
        } else {

            return NULL;
        }
    }



    function get_site_user_list($id)

    {



        $this->db->select('id');

        $this->db->from($this->_tbl_users);

        $this->db->where('status =', 1);

        $this->db->where('site_id =', $id);

        $result = $this->db->get();

        $result = $result->result_array();

        $i = 0;

        foreach ($result as $user) {

            $users[$i] = $user['id'];

            $i++;
        }

        return $users;
    }



    function get_site_color_logo($site_id)

    {

        $this->db->select("site_logo,site_color");

        $this->db->from($this->_tbl_sites);

        $this->db->where('id', $site_id);

        $this->db->where('status !=', -1);

        $result = $this->db->get();

        return $result->row_array();
    }



    function delete_permissions_to_user($id)

    {



        $id = intval($id);

        $this->db->where('user_id', $id);

        return $this->db->delete($this->_tbl_user_permission);
    }



    /*

     * Function for check site is active or not by siteId

     */



    function isActiveSite($siteId)

    {

        $this->db->select('id,status');

        $this->db->from($this->_tbl_sites);

        $this->db->where('status =', 1);

        $this->db->where('id =', $siteId);

        $result = $this->db->get();

        if ($result->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }



    /*

     * Function for add data to userTable from UsersTable

     */



    function compareUserAndUsersTableData()
    {



        $this->db->select('u.*');

        $this->db->from($this->_tbl_users . ' AS u');

        $this->db->where('u.status !=', -1);

        $result = $this->db->get();



        foreach ($result->result_array() as $value) {

            $UserName = ucfirst($value['username']);

            $this->db->select('us.*');

            $this->db->from($this->_tbl_user_only . ' AS us');

            $this->db->where('us.user_name', $UserName);

            $query = $this->db->get();

            $queryResult = $query->result_array();

            if (empty($queryResult)) {



                $returnArray[] = array(

                    'id' => $value['id'],

                    'username' => $value['username'],

                    'role_id' => $value['role_id'],

                );
            }
        }

        return $returnArray;
    }



    function get_site_to_user_with_region($user_id, $region_id)

    {

        $this->db->select('*');

        $this->db->from('user_sites u');

        $this->db->join($this->_tbl_sites . ' as s', 'u.site_id = s.id');

        $this->db->where('u.user_id', $user_id);

        $this->db->where('s.region_id', $region_id);



        $query = $this->db->get();

        // pre($query);

        $result = $query->result_array();



        return $result;
    }



    function get_site_to_user_with_region_sites($user_id, $region_ids)

    {

        $this->db->select('*')->from('sites');

        if (!empty($region_ids)) {

            $region_arr = implode(",", array_filter($region_ids));

            $this->db->where("region_id IN (" . $region_arr . ")", NULL, false);
        }

        $this->db->or_where('`id` IN (SELECT `site_id` FROM `user_sites` WHERE `user_id` = ' . $user_id . ')', NULL, FALSE);

        $this->db->group_by('id');

        $query = $this->db->get();

        $result = $query->result_array();



        return $result;
    }



    function get_user_region_selected($id = 0)

    {

        $id = intval($id);

        $this->db->select("region_id");

        $this->db->where("id", $id);

        $tableusers = $this->db->get($this->_tbl_users);

        $userArray = $tableusers->row_array();

        if (!empty($userArray['region_id'])) {

            $region_arr = explode(",", $userArray['region_id']);

            return $region_arr;
        } else {

            return '';
        }
    }

    function insertPermission($user_id, $permission_id)
    {
        $data['user_id'] = $user_id;
        $data['permission_id'] = $permission_id;
        $this->db->set($data);
        $this->db->insert($this->_tbl_user_permission);
    }
}
