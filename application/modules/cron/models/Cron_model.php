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

class Cron_model extends Base_Model {



    protected $_tbl_users = TBL_USERS;

    protected $_tbl_roles = TBL_ROLES;

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_role_permission = TBL_ROLE_PERMISSION;

    protected $_tbl_user_permission = TBL_USER_PERMISSION;

    protected $_tbl_user_cron_notifications = 'user_cron_notifications';

    protected $_tbl_user_sites = 'user_sites';

    protected $_tbl_utilities_cost_daily = 'utilities_cost_daily';

    protected $_tbl_utilities_cost = 'utilities_cost';

    public $search_term_firstname = "";

    public $search_term_username = "";

    public $sort_by = "";

    public $sort_order = "";

    public $_record_count;

    public $site_id;

    public $month_id;

    public $year_id;



    /**

     * Function save_user to add/update user

     * @param array $data for user table

     * @param array $data_profile for user_profile table

     */

    public function save_user($data) {



        if (isset($data['id'])) {

            $user_data ['id'] = $data['id'];

        }

        if (isset($data['role_id'])) {

            $user_data ['role_id'] = $data['role_id'];

        }

        if (isset($data['site_id'])) {

            $user_data ['site_id'] = $data['site_id'];

        }

        if (isset($data['firstname'])) {

            $user_data ['firstname'] = $data['firstname'];

        }

        if (isset($data['lastname'])) {

            $user_data['lastname'] = $data['lastname'];

        }

        if (isset($data['email'])) {

            $user_data ['email'] = $data['email'];

        }

        if (isset($data['password'])) {

            $user_data ['password'] = $data['password'];

        }

        if (isset($data['last_login'])) {

            $user_data ['last_login'] = $data['last_login'];

        }

        if (isset($data['status'])) {

            $user_data ['status'] = $data['status'];

        }

        if (isset($data['created'])) {

            $user_data ['created'] = $data['created'];

        }

        if (isset($data['modified'])) {

            $user_data ['modified'] = $data['modified'];

        }



        if (isset($user_data['id']) && $user_data['id'] != 0 && $user_data['id'] != "") {

            $this->db->where('id', $user_data['id']);

            $this->db->update($this->_tbl_users, $user_data);

            $id = $user_data['id'];

        } else {



            $this->db->set('created', 'NOW()', FALSE);

            if ($this->db->insert($this->_tbl_users, $data)) {

                $id = $this->db->insert_id();

            }

        }

        return $id;

    }



    function assign_permissions_to_user($role_id, $inserted_id) {

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



    public function save_profile($data, $data_profile) {



        if (isset($data['id'])) {

            $user_data ['id'] = $data['id'];

        }

        if (isset($data['role_id'])) {

            $user_data ['role_id'] = $data['role_id'];

        }

        if (isset($data['firstname'])) {

            $user_data ['firstname'] = $data['firstname'];

        }

        if (isset($data['lastname'])) {

            $user_data['lastname'] = $data['lastname'];

        }

        if (isset($data['email'])) {

            $user_data ['email'] = $data['email'];

        }





        if (isset($data['status'])) {

            $user_data ['status'] = $data['status'];

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

    public function update_last_login($id = 0) {

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

    function login() {

        $username = trim($this->username);

        $password = trim($this->password);

        //$sites = (int) $this->security->xss_clean($this->site);



        $this->db->join($this->_tbl_roles . ' as r', ('u.role_id = r.id OR u.id = 1'), 'left');

        $this->db->where("u.username", $username);

        $this->db->where("u.status != ", '-1');

//$this->db->where("u.role_id IN (1,2,3,4,5)");

        $this->db->where("u.role_id !=", '6');

        //$this->db->where("u.site_id =", $sites);

        $this->db->where("r.status  ", '1');

        $this->db->where("u.password", encriptsha1($password));

        $query = $this->db->get($this->_tbl_users . ' AS u');



        if ($query->num_rows() > 0) {

            return $this->db->custom_result($query);

        }

        return false;

    }



    /**

     * Function role_list to get listing of active roles

     */

    function role_list($id) {

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



    function role_list_moderator($id) {

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



    function get_role_name_by_id($role_id) {

        $this->db->select("role_name");

        $this->db->from($this->_tbl_roles);

        $this->db->where('id', $role_id);

        $result = $this->db->get();

        $result = $result->result_array();

        return $result[0]['role_name'];

    }



    function get_default_role() {

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

    function get_user_detail($id = 0) {

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

     * Function changepassword to change user password

     * @param integer $user_id default = 0

     */

    function changepassword($user_id = 0, $password = NULL) {

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

    function get_user_listing($user_id, $site_id, $role_id) {



        $role_array = array(1, 2);

        if ($this->search_term_firstname != "") {

            $this->db->like("LOWER(u.firstname)", strtolower($this->search_term_firstname));

        }

        if ($this->search_term_username != "") {

            $this->db->like("LOWER(u.username)", strtolower($this->search_term_username));

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

            $this->db->where('u.role_id >', $role_id);

        }

        $this->db->select('u.*,r.role_name,s.site_location_name');

        $this->db->from($this->_tbl_users . ' AS u');

        $this->db->join($this->_tbl_roles . ' as r', 'u.role_id = r.id', 'left');

        $this->db->join($this->_tbl_sites . ' as s', 'u.site_id = s.id', 'left');

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



    function get_user_list_helper() {

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

    public function check_front_login($email, $password) {

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

    function activation($activation_key) {





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

    function get_user_detail_by_email($email = NULL) {

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

    function forgot_password($data = array()) {

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

    public function delete_user($id) {

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

    function check_group_status($id = 0) {

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

    function check_unique_email($data) {

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



    function check_unique_username($data) {

        $username = trim(strip_tags($data['username']));

        $user_id = intval($data['id']);



        $this->db->select('id,username');

        $this->db->from($this->_tbl_users);

        if (isset($user_id) && $user_id != '' && $user_id != 0) {

            $this->db->where('id != ', $user_id);

        }

        $this->db->where('LOWER(username) = ', mb_strtolower($username, 'UTF-8'));

        //$this->db->where('status != ', -1);



        $this->db->limit(1);

        $result = $this->db->get()->num_rows();

        return $result;

    }



    /**

     * Function update_activation_key to update activate field in DB

     * @param string $activation_key

     */

    function update_activation_key($activation_key) {

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

    function get_user_data_by_activation_key($activation_key) {

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

    public function inactive_records($id = array()) {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 0);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_users);



        return $id;

    }



    /**

     * Function inactive_all_records to inactive all records without deleted records

     */

    public function inactive_all_records() {

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

    public function active_records($id = array()) {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->set('status', 1);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_users);



        return $id;

    }



    /**

     * Function active_all_records to active all records without deleted records

     */

    public function active_all_records() {

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

    public function delete_records($id = array()) {

        $this->db->set('modified', 'NOW()', FALSE);

        $this->db->where_in('id', $id);

        $date = new DateTime();

        $ctimestamp = $date->getTimestamp();

        $this->db->set('username', "CONCAT(username, '-$ctimestamp')", false);

        $this->db->set('status', '-1');

        return $this->db->update($this->_tbl_users);

    }



    function get_sites_list() {

        $this->db->select('id,site_location_name');

        $this->db->from($this->_tbl_sites);

        $this->db->where('status', 1);

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



    function get_site_user_list($id) {



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



    function get_site_color_logo($site_id) {

        $this->db->select("site_logo,site_color");

        $this->db->from($this->_tbl_sites);

        $this->db->where('id', $site_id);

        $this->db->where('status !=', -1);

        $result = $this->db->get();

        return $result->row_array();

    }



    function delete_permissions_to_user($id) {



        $id = intval($id);

        $this->db->where('user_id', $id);

        return $this->db->delete($this->_tbl_user_permission);

    }



    function get_all_sites_for_location($id=0) {

        $this->db->select('s.id,s.site_type,s.site_location_name,s.station_id,s.base_cdd_temprature,s.base_hdd_temprature');

        $this->db->from($this->_tbl_sites . ' AS s');

        //$this->db->where('s.status !=', -1); // commented for execute cron for active sites only

        $this->db->where('s.status',1);

        if(isset($id) && $id != 0) {

            $this->db->where('s.region_id',$id);
        
        }

        $result = $this->db->get();

        $return = array();

        if ($result->num_rows() > 0) {

            return $result->result_array();

        }



        return array();

    }



    public function insert_daily_utilities_cdd($sites_data) {



        /* ini_set('display_errors', 1);

          error_reporting(E_ALL); */



        if (!empty($sites_data)) {

            foreach ($sites_data as $site => $utility_data) {

                foreach ($utility_data as $key => $value) {

                    $date = explode('-', $key);



                    $insert_data = array();

                    $insert_data['cdd'] = $value['cdd'];

                    $insert_data['hdd'] = $value['hdd'];

                    $insert_data['site_id'] = $site;

                    $insert_data['year_id'] = $date[0];

                    $insert_data['month_id'] = $date[1];

                    $insert_data['date_id'] = $date[2];



                    if ((int) $insert_data['date_id'] <= 0) {

                        continue;

                    }



                    /**

                     * daily_reading_utilities_data table

                     */

                    $this->db->select('id');

                    $this->db->from('daily_reading_utilities_data');

                    $this->db->where('site_id', $insert_data['site_id']);

                    $this->db->where('date_id', $insert_data['date_id']);

                    $this->db->where('month_id', $insert_data['month_id']);

                    $this->db->where('year_id', $insert_data['year_id']);

                    $query = $this->db->get();



                    if ($query->num_rows() > 0) {

                        $this->db->where('site_id', $insert_data['site_id']);

                        $this->db->where('date_id', $insert_data['date_id']);

                        $this->db->where('month_id', $insert_data['month_id']);

                        $this->db->where('year_id', $insert_data['year_id']);



                        $this->db->update('daily_reading_utilities_data', $insert_data);

                    } else {

                        $this->db->insert('daily_reading_utilities_data', $insert_data);

                    }



                    /**

                     * utilities_cost_daily table

                     */

                    $this->db->select('id');

                    $this->db->from('utilities_cost_daily');

                    $this->db->where('site_id', $insert_data['site_id']);

                    $this->db->where('date_id', $insert_data['date_id']);

                    $this->db->where('month_id', $insert_data['month_id']);

                    $this->db->where('year_id', $insert_data['year_id']);

                    $query = $this->db->get();



                    if ($query->num_rows() > 0) {

                        $this->db->where('site_id', $insert_data['site_id']);

                        $this->db->where('date_id', $insert_data['date_id']);

                        $this->db->where('month_id', $insert_data['month_id']);

                        $this->db->where('year_id', $insert_data['year_id']);



                        $this->db->update('utilities_cost_daily', $insert_data);

                    } else {

                        $this->db->insert('utilities_cost_daily', $insert_data);

                    }

                }

            }

        }

    }



    public function insert_monthly_utilities_cdd($sites_data) {



        if (!empty($sites_data)) {

            foreach ($sites_data as $site => $utility_data) {

                foreach ($utility_data as $key => $value) {

                    $date = explode('-', $key);



                    $insert_data = array();

                    $insert_data['cdd'] = $value['cdd'];

                    $insert_data['hdd'] = $value['hdd'];

                    $insert_data['site_id'] = $site;

                    $insert_data['year_id'] = $date[0];

                    $insert_data['month_id'] = $date[1];



                    /**

                     * utilities_cost table

                     */

                    $this->db->select('id');

                    $this->db->from('utilities_cost');

                    $this->db->where('site_id', $insert_data['site_id']);

                    $this->db->where('month_id', $insert_data['month_id']);

                    $this->db->where('year_id', $insert_data['year_id']);

                    $query = $this->db->get();



                    if ($query->num_rows() > 0) {

                        $this->db->where('site_id', $insert_data['site_id']);

                        $this->db->where('month_id', $insert_data['month_id']);

                        $this->db->where('year_id', $insert_data['year_id']);



                        $this->db->update('utilities_cost', $insert_data);

                    } else {

                        $this->db->insert('utilities_cost', $insert_data);

                    }

                }

            }

        }

    }



    function getUserCronNotifications() {

        $notifications = array();

        $this->db->select('*');

        $this->db->from($this->_tbl_user_cron_notifications . " as n");

        $this->db->join($this->_tbl_users . " as u", 'n.user_id = u.id');

        $this->db->where("u.role_id <>", "1");

        $result = $this->db->get();



        $result_array = $result->result_array();



        if (!empty($result_array)) {



            foreach ($result_array as $notification) {

                $notifications[$notification['user_id']][] = $notification['notifications'];

            }

        }

        return $notifications;

    }



    function getUserSites() {

        $sites = array();

        $this->db->select('us.user_id, us.site_id as id, s.site_location_name');

        $this->db->from($this->_tbl_user_sites . " as us");

        $this->db->join($this->_tbl_sites . " as s", "s.id = us.site_id");

        $result_array = $this->db->get();

        $result = $result_array->result_array();



        foreach ($result as $site_result) {

            $sites[$site_result['user_id']][$site_result['id']] = $site_result['site_location_name'];

        }

        return $sites;

    }



    /**
     * Function getCorporateUserSites to fetch active sites mapped to Corporate users
     * through their assigned regions (user_regions -> sites.region_id).
     * Returns array indexed by user_id with [site_id => site_location_name].
     */
    function getCorporateUserSites() {

        $sites = array();

        $this->db->select('ur.user_id, s.id as site_id, s.site_location_name');

        $this->db->from('user_regions as ur');

        $this->db->join($this->_tbl_sites . ' as s', 's.region_id = ur.region_id');

        $this->db->where('s.status', 1);

        $result_array = $this->db->get();

        $result = $result_array->result_array();

        foreach ($result as $site_result) {

            if ($site_result['site_location_name'] != '') {

                $sites[$site_result['user_id']][$site_result['site_id']] = $site_result['site_location_name'];

            }

        }

        return $sites;

    }



    function getUserList() {

        $this->db->select('id, role_id, firstname, lastname, email');

        $this->db->from($this->_tbl_users);

        $this->db->where('status', 1);

        $result = $this->db->get();

        return $result->result_array();

    }



    function getDaysUtility($date_id) {



        $query = "SELECT SUM(total_electricity_kwh) as 'total_electricity_consumption', 

                    SUM(total_diesel_fuel) as 'total_diesel_fuel', 

                    SUM(total_heavy_fuel) as 'total_heavy_fuel', 

                    SUM(total_lpg_consumption) as 'total_lpg_consumption', 

                    SUM(total_water_consumption) as 'total_water_consumption', 

                    SUM(total_landscape_water_consumption) as 'total_landscape_water_consumption', 

                    SUM(total_natural_gas_consumption) as 'total_natural_gas_consumption', 

                    SUM(total_district_cooling_consumption) as 'total_district_cooling_consumption', 

                    SUM(total_district_heating_consumption) as 'total_district_heating_consumption',

                    SUM(total_room_night) as 'total_room_night',

                    SUM(hdd) as 'total_hdd',

                    SUM(cdd) as 'total_cdd'

                    FROM `{$this->_tbl_utilities_cost_daily}`

                    WHERE `date_id` <= {$date_id} 

                        AND `date_id` > 0 

                        AND `month_id` = {$this->month_id}

                        AND `year_id` = {$this->year_id}

                        AND `site_id` = {$this->site_id}";

                        

        $result = $this->db->query($query);

        return $result->row_array();

    }



    function getDaysUtilityCost($date_id) {



        $query = "SELECT SUM(total_electricity_kwh * total_electricity_kwh_tariff) as 'total_electricity_cost', 

                    SUM(total_diesel_fuel * total_diesel_fuel_tariff) as 'total_diesel_fuel_cost', 

                    SUM(total_heavy_fuel * total_heavy_fuel_tariff) as 'total_heavy_fuel_cost', 

                    SUM(total_lpg_consumption * total_lpg_consumption_tariff) as 'total_lpg_cost', 

                    SUM(total_water_consumption * total_water_consumption_tariff) as 'total_water_cost', 

                    SUM(total_landscape_water_consumption * total_landscape_water_consumption_tariff) as 'total_landscape_water_cost', 

                    SUM(total_natural_gas_consumption * total_natural_gas_consumption_tariff) as 'total_natural_gas_cost', 

                    SUM(total_district_cooling_consumption * total_district_cooling_consumption_tariff) as 'total_district_cooling_cost', 

                    SUM(total_district_heating_consumption * total_district_heating_consumption_tariff) as 'total_district_heating_cost' 

                    FROM `{$this->_tbl_utilities_cost_daily}`

                    WHERE `date_id` <= {$date_id} 

                        AND `date_id` > 0 

                        AND `month_id` = {$this->month_id}

                        AND `year_id` = {$this->year_id}

                        AND `site_id` = {$this->site_id}";

        

        $result = $this->db->query($query);

        return $result->row_array();

    }



    function getUtilityBudget() {



        $query = "SELECT 

                    FROM `{$this->_tbl_utilities_cost_daily}`

                    WHERE `date_id` <= {$date_id} 

                        AND `date_id` > 0 

                        AND `month_id` = {$this->month_id}

                        AND `year_id` = {$this->year_id}

                        AND `site_id` = {$this->site_id}";

        

        $result = $this->db->query($query);

        return $result->row_array();

    }



    function get_site_details() {

        $result_array = array();

        $this->db->where(array('status' => 1));// Added condition for execute cron only for active sites

        $tablesites = $this->db->get($this->_tbl_sites);

        $result = $tablesites->result_array();

        foreach ($result as $site) {

            $result_array[$site['id']] = $site;

        }

        return $result_array;

    }



    function get_ytd_data() {

        $end_date = date('m') - 1;

        $current_year = date('Y');

        $query = "SELECT SUM(total_electricity_cost) as 'total_electricity_cost',

                    SUM(water_total_consumption_cost) as 'water_total_consumption_cost',

                    SUM(total_fuel_oil_cost) as 'total_fuel_oil_cost',

                    SUM(total_lpg_cost) as 'total_lpg_cost',

                    SUM(total_natural_gas_cost) as 'total_natural_gas_cost',

                    SUM(district_heating_cost) as 'district_heating_cost',

                    SUM(district_cooling_cost) as 'district_cooling_cost',



                    SUM(electricity_total_budget_cost) as 'electricity_total_budget_cost',

                    SUM(water_total_consumption_budget_cost) as 'water_total_consumption_budget_cost',

                    SUM(fuel_total_budget_cost) as 'fuel_total_budget_cost',

                    SUM(lpg_total_budget_cost) as 'lpg_total_budget_cost',

                    SUM(natural_gas_total_budget_cost) as 'natural_gas_total_budget_cost',

                    SUM(district_heating_total_budget_cost) as 'district_heating_total_budget_cost',

                    SUM(district_cooling_total_budget_cost) as 'district_cooling_total_budget_cost'

                    FROM `{$this->_tbl_utilities_cost}`

                    WHERE `month_id` >= 1 AND `month_id` <= {$end_date}

                    AND `year_id` = {$current_year}

                    AND `site_id` = {$this->site_id}";



        $result = $this->db->query($query);

        return $result->row_array();

    }

	

	public function get_daily_reading_cron_settings($data)

    {

		if($data['endMonth'] == $data['startMonth']){			

			$query = "SELECT (sum(td.value)/7) as Avg,td.utility_title_id,t.title,tu.id as utility_id FROM daily_reading_utilities_titles as t left join daily_reading_utilities_title_data as td on t.id = td.utility_title_id left join daily_reading_utilities as tu on tu.id=t.utility_id where t.is_used_in_cron=1 and year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and date_id<=".$data['endDay']." and date_id>=".$data['startDay']." and month_id<=".$data['endMonth']." and month_id>=".$data['startMonth']." and td.site_id=".$data['id']." group by td.utility_title_id";

		} else {

			$query = "SELECT (sum(td.value)/7) as Avg,td.utility_title_id,t.title,tu.id as utility_id FROM daily_reading_utilities_titles as t left join daily_reading_utilities_title_data as td on t.id = td.utility_title_id left join daily_reading_utilities as tu on tu.id=t.utility_id where t.is_used_in_cron=1 and year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and ((date_id<=".$data['endDay']." and month_id=".$data['endMonth'].") or (date_id>=".$data['startDay']." and month_id=".$data['startMonth'].")) and td.site_id=".$data['id']." group by td.utility_title_id";

		}



        $result = $this->db->query($query);

        return $result->result_array();

    }

		

	public function get_daily_utility_cron_settings($data)

    {

		if($data['endMonth'] == $data['startMonth']){			

			$query = "SELECT (sum(total_electricity_kwh)/7) as electricity,(sum(total_water_consumption)/7) as water,(sum(total_lpg_consumption)/7) as lpg,(sum(total_diesel_fuel)/7) as fuel_oil, (sum(total_natural_gas_consumption)/7) as natural_gas, (sum(total_district_cooling_consumption)/7) as district_cooling, (sum(total_district_heating_consumption)/7) as district_heating FROM utilities_cost_daily where year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and date_id<=".$data['endDay']." and date_id>=".$data['startDay']." and month_id<=".$data['endMonth']." and month_id>=".$data['startMonth']." and site_id=".$data['id'];

		} else {

			$query = "SELECT (sum(total_electricity_kwh)/7) as electricity,(sum(total_water_consumption)/7) as water,(sum(total_lpg_consumption)/7) as lpg,(sum(total_diesel_fuel)/7) as fuel_oil, (sum(total_natural_gas_consumption)/7) as natural_gas, (sum(total_district_cooling_consumption)/7) as district_cooling, (sum(total_district_heating_consumption)/7) as district_heating FROM utilities_cost_daily where year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and ((date_id<=".$data['endDay']." and month_id=".$data['endMonth'].") or (date_id>=".$data['startDay']." and month_id=".$data['startMonth'].")) and site_id=".$data['id'];

		}



        $result = $this->db->query($query);

        return $result->row_array();

    }

	

	public function read_daily_reading_utilites_setting()

    {

        $this->db->select("*");

        $this->db->from('utility_in_7days_cron');

        $result = $this->db->get();

        $result = $result->result_array();

		$response = array();

		if($result){

			foreach($result as $key => $value){

				$response[$value['site_id']][] = $value['utility_id'];

			}

		}

		return $response;

    }

	

	public function read_main_utilites()

    {

        $this->db->select("*");

        $this->db->from('daily_reading_utilities');

        $result = $this->db->get();

        $result = $result->result_array();

		$response = array();

		if($result){

			foreach($result as $key => $value){

				$response[$value['title']] = $value['id'];

			}

		}

		return $response;

    }



    public function get_daily_reading_cron_settings_chr($data)

    {

        if($data['endMonth'] == $data['startMonth']){           

            $query = "SELECT (sum(td.value)/7) as Avg,td.utility_title_id,t.title,tu.id as utility_id FROM daily_reading_utilities_titles as t left join daily_reading_utilities_title_data as td on t.id = td.utility_title_id left join daily_reading_utilities as tu on tu.id=t.utility_id where t.is_used_in_cron=1 and year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and date_id<=".$data['endDay']." and date_id>=".$data['startDay']." and month_id<=".$data['endMonth']." and month_id>=".$data['startMonth']." and td.site_id=".$data['id']." group by td.utility_title_id";

        } else {

            $query = "SELECT (sum(td.value)/7) as Avg,td.utility_title_id,t.title,tu.id as utility_id FROM daily_reading_utilities_titles as t left join daily_reading_utilities_title_data as td on t.id = td.utility_title_id left join daily_reading_utilities as tu on tu.id=t.utility_id where t.is_used_in_cron=1 and year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and ((date_id<=".$data['endDay']." and month_id=".$data['endMonth'].") or (date_id>=".$data['startDay']." and month_id=".$data['startMonth'].")) and td.site_id=".$data['id']." group by td.utility_title_id";

        }

        

        $result = $this->db->query($query);

        return $result->result_array();

    }

        

    public function get_daily_utility_cron_settings_chr($data)

    {

        if($data['endMonth'] == $data['startMonth']){           

            $query = "SELECT (sum(total_room_night)/7) as total_room_night,(sum(hdd)/7) as hdd,(sum(cdd)/7) as cdd FROM utilities_cost_daily where year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and date_id<=".$data['endDay']." and date_id>=".$data['startDay']." and month_id<=".$data['endMonth']." and month_id>=".$data['startMonth']." and site_id=".$data['id'];

        } else {

            $query = "SELECT (sum(total_room_night)/7) as total_room_night,(sum(hdd)/7) as hdd,(sum(cdd)/7) as cdd FROM utilities_cost_daily where year_id<=".$data['endYear']." and year_id>=".$data['startYear']." and ((date_id<=".$data['endDay']." and month_id=".$data['endMonth'].") or (date_id>=".$data['startDay']." and month_id=".$data['startMonth'].")) and site_id=".$data['id'];

        }



        $result = $this->db->query($query);

        return $result->row_array();

    }

    



}

