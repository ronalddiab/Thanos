<?php

/**
 *  Users Admin Controller
 *
 *  To perform user management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Users_admin extends Base_Admin_Controller {

    var $search_term;

    function __construct() {
	parent::__construct();

	$this->load->library('form_validation');
	$this->breadcrumb->add(lang('user-management'), base_url() . 'users');
	// Login check for admin
	$this->access_control($this->access_rules());
	$this->language = $this->uri->segment(4);
	$this->load->library('unit_test');
	$this->load->model('users/users_model');
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {

	return array(
	    array(
		'actions' => array('index', 'changepassword', 'action', 'delete', 'check_unique_email', 'save', 'view_data','entryToUserTable'),
		'users' => array('@'),
	    ),
	    array(
		'actions' => array('login', 'logout', 'insert_data', 'forgot_password', 'ajax_login'),
		'users' => array('*'),
	    )
	);
    }

    /**
     * Function index to view listing of users
     */
    function index() {
	//Paging parameters
	$offset = get_offset($this->page_number, $this->record_per_page);
	$this->users_model->record_per_page = $this->record_per_page;
	$this->users_model->offset = $offset;

	//set sort/search parameters in pagging
	if ($this->input->post()) {

	    $data = $this->input->post();
	    if (empty($data['page_number'])) {
		$this->session->set_custom_userdata($this->section_name, "user_offset", "");
		$this->session->set_custom_userdata($this->section_name, "user_record_per_page", "");
	    }
	    // Search Term ***
	    if (isset($data['search_term_firstname']) && !empty($data['search_term_firstname'])) {
		$this->users_model->search_term_firstname = trim($data['search_term_firstname']);
		$this->session->set_custom_userdata($this->section_name, "user_search_term_firtname", $this->input->post('search_term_firstname'));
	    } else {
		$this->session->set_custom_userdata($this->section_name, "user_search_term_firtname", "");
	    }

	    if (isset($data['search_term_username']) && !empty($data['search_term_username'])) {
		$this->users_model->search_term_username = trim($data['search_term_username']);
		$this->session->set_custom_userdata($this->section_name, "user_search_term_username", $this->input->post('search_term_username'));
	    } else {
		$this->session->set_custom_userdata($this->section_name, "user_search_term_username", "");
	    }

	    if (isset($data['search_site']) && !empty($data['search_site'])) {
		$this->users_model->search_site = trim($data['search_site']);
		$this->session->set_custom_userdata($this->section_name, "search_site", $this->input->post('search_site'));
	    } else {
		$this->session->set_custom_userdata($this->section_name, "search_site", "");
	    }


	    // Search Term ***
	    // Sort Order ***
	    if (isset($data['sort_by']) && $data['sort_order']) {
		$this->users_model->sort_by = $data['sort_by'];
		$this->users_model->sort_order = $data['sort_order'];
		$this->session->set_custom_userdata($this->section_name, "user_sort_by", $this->input->post('sort_by'));
		$this->session->set_custom_userdata($this->section_name, "user_sort_order", $this->input->post('sort_order'));
	    } else {
		$this->session->set_custom_userdata($this->section_name, "user_sort_by", "");
		$this->session->set_custom_userdata($this->section_name, "user_sort_order", "");
	    }
	    // Sort Order ***

	    if (isset($data['type']) && $data['type'] == 'delete') {

		// Newly added
		$tempArr = array();
		foreach ($data['ids'] as $key => $val) {
		    $tempArr[] = base64_decode($val);
		}
		// Newly added
		//if($this->users_model->delete_records($data['ids']))
		if ($this->users_model->delete_records($tempArr)) {
		    echo $this->theme->message(lang('user-delete-success'), 'success');
		    exit;
		}
	    }

	    if (isset($data['type']) && $data['type'] == 'active') {
		// Newly added
		$tempArr = array();
		foreach ($data['ids'] as $key => $val) {
		    $tempArr[] = base64_decode($val);
		}
		// Newly added
		if ($this->users_model->active_records($tempArr)) {
		    echo $this->theme->message(lang('user-active-success'), 'success');
		    $data_action = 'Update';
		    $site_id = $_SESSION['admin']['site_id'];
		    $user_id = $_SESSION['admin']['user_id'];
		    saveAuditTrail($user_id, $site_id, 'Activate Users', $data_action);
		    exit;
		}
	    }
	    if (isset($data['type']) && $data['type'] == 'inactive') {
		// Newly added
		$tempArr = array();
		foreach ($data['ids'] as $key => $val) {
		    $tempArr[] = base64_decode($val);
		}
		// Newly added
		if ($this->users_model->inactive_records($tempArr)) {
		    echo $this->theme->message(lang('user-inactive-success'), 'success');
		    $data_action = 'Update';
		    $site_id = $_SESSION['admin']['site_id'];
		    $user_id = $_SESSION['admin']['user_id'];
		    saveAuditTrail($user_id, $site_id, 'Inactive Users', $data_action);
		    exit;
		}
	    }
	    if (isset($data['type']) && $data['type'] == 'active_all') {

		if ($this->users_model->active_all_records()) {
		    echo $this->theme->message(lang('user-active-success'), 'success');
		    $data_action = 'Update';
		    $site_id = $_SESSION['admin']['site_id'];
		    $user_id = $_SESSION['admin']['user_id'];
		    saveAuditTrail($user_id, $site_id, 'Activate All Users', $data_action);
		    exit;
		}
	    }
	    if (isset($data['type']) && $data['type'] == 'inactive_all') {
		if ($this->users_model->inactive_all_records()) {
		    echo $this->theme->message(lang('user-inactive-success'), 'success');
		    $data_action = 'Update';
		    $site_id = $_SESSION['admin']['site_id'];
		    $user_id = $_SESSION['admin']['user_id'];
		    saveAuditTrail($user_id, $site_id, 'Inactive All Users', $data_action);
		    exit;
		}
	    }
	}

	if (!empty($this->session->userdata[$this->section_name]['user_search_term_firstname'])) {
	    $this->users_model->search_term = trim($this->session->userdata[$this->section_name]['user_search_term_firstname']);
	}
	if (!empty($this->session->userdata[$this->section_name]['user_search_term_username'])) {
	    $this->users_model->search_term = trim($this->session->userdata[$this->section_name]['user_search_term_username']);
	}
	if (!empty($this->session->userdata[$this->section_name]['user_sort_by'])) {
	    $this->users_model->sort_by = $this->session->userdata[$this->section_name]['user_sort_by'];
	}
	if (!empty($this->session->userdata[$this->section_name]['user_sort_order'])) {
	    $this->users_model->sort_order = $this->session->userdata[$this->section_name]['user_sort_order'];
	}
	if (!empty($this->session->userdata[$this->section_name]['user_offset'])) {
	    $this->roles_model->offset = $this->session->userdata[$this->section_name]['user_offset'];
	}
	if (!empty($this->session->userdata[$this->section_name]['user_record_per_page'])) {
	    $this->page_number = $this->session->userdata[$this->section_name]['user_record_per_page'];
	}
	if (!empty($this->session->userdata[$this->section_name]['search_site'])) {
	    $this->users_model->search_site = trim($this->session->userdata[$this->section_name]['search_site']);
	}

	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$role_id = $this->session->userdata[$this->section_name]['role_id'];
	$site_id = $this->session->userdata[$this->section_name]['site_id'];
	//Load data for url listing
	$users = $this->users_model->get_user_listing($user_id, $site_id, $role_id);
	$this->users_model->_record_count = true;
	$total_records = $this->users_model->get_user_listing($user_id, $site_id, $role_id);
	// Pass data to view file
	$this->search_term = $this->users_model->search_term;

	$this->load->model('sites/sites_model');
	$data['hotel_sites'] = $this->sites_model->get_login_sites();
	$data['users'] = $users;
	$data['page_number'] = $this->page_number;
	$data['total_records'] = $total_records;
	$data['search_term_firstname'] = $this->users_model->search_term_firstname;
	$data['search_term_username'] = $this->users_model->search_term_username;
	$data['search_site'] = $this->users_model->search_site;
	$data['sort_by'] = $this->users_model->sort_by;
	$data['sort_order'] = $this->users_model->sort_order;
	// pre($data);
	//Create page-title
	$this->theme->set('page_title', lang('user-management'));

	//Render view
	$this->theme->view($data);
    }

    /**
     * Function users_validation_rules to validate input
     */
    function users_validation_rules() {
	$this->form_validation->set_rules('firstname', lang('first-name'), 'trim|required|min_length[2]');
	$this->form_validation->set_rules('lastname', lang('last-name'), 'trim|required|min_length[2]');
	$id = intval($this->input->post('id'));
	$password = strip_tags($this->input->post('password'));
	$passconf = strip_tags($this->input->post('passconf'));

	$this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email|callback_check_unique_email');
	$this->form_validation->set_rules('avtar', lang('avtar'), 'callback_valid_upload');

	if ($id == 0 || $id == "") {
	    $this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[4]|max_length[50]');
	    $this->form_validation->set_rules('passconf', lang('c-password'), 'trim|required|matches[password]');
	    $this->form_validation->set_rules('username', lang('user-name'), 'trim|required|min_length[2]|callback_check_unique_username');
	} elseif ($password != '' || $passconf != '') {
	    $this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[4]|max_length[50]');
	    $this->form_validation->set_rules('passconf', lang('c-password'), 'trim|required|matches[password]');
	}
    }

    /**
     * Function login to view login page
     */
    public function login() {
	// pr($this->input->get('back_url'));exit;
	$data = array();
	if ($this->input->get('back_url')) {
	    $data['back_url'] = $this->input->get('back_url');
	}

	$loginuserdata = $this->session->all_userdata();
	if ($this->is_login()) {
	    if ($this->section_name == "admin" && $loginuserdata[$this->section_name]['role_id'] == 5) {
		redirect(BASE_ADMIN_URL_CUSTOM . 'forum');
	    } else if ($this->section_name == "admin" && $loginuserdata[$this->section_name]['role_id'] == 2) {
		redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard/sites');
	    } else {
		//redirect($this->section_name . '/dashboard/index');
		redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');
	    }
	    exit;
	}

	if (isset($loginuserdata['user_id']) && $loginuserdata['user_id'] != "") {
	    redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');
	}

	if ($this->input->post()) {
	    $this->form_validation->set_rules('username', lang('username'), 'trim|required');
	    $this->form_validation->set_rules('password', lang('password'), 'trim|required');
	    $this->form_validation->set_rules('sites', lang('sites'), 'trim');

	    $this->users_model->username = $this->input->post('username');
	    $this->users_model->password = $this->input->post('password');
	    $this->users_model->sites = $this->input->post('sites');

	    $data['username'] = $this->input->post('username');
	    if ($this->form_validation->run() == TRUE) {
		$result = $this->users_model->login();
		if (!empty($result)) {
		    if ($result[0]['u']['status'] == 1) {
			//add all data to session
			$newdata = array(
			    'user_id' => $result[0]['u']['id'],
			    'role_id' => $result[0]['u']['role_id'],
			    'email' => $result[0]['u']['email'],
			    'username' => $result[0]['u']['username'],
			    'firstname' => $result[0]['u']['firstname'],
			    'lastname' => $result[0]['u']['lastname'],
			    'logged_in' => TRUE,
			);
			$this->session->set_custom_userdata($this->section_name, $newdata);

			$this->load->model('user_login_log/user_login_log_model');
			$data = array(
			    'user_id' => $newdata['user_id'],
			    'role_id' => $newdata['role_id'],
			    'session_id' => $this->session->userdata['session_id']
			);
			$this->user_login_log_model->addLog($data);

			// Set permission in session
			$this->allowed_permission_list($newdata['role_id']);
			if ($result[0]['u']['id'] == '1') {
			    $this->session->set_custom_userdata($this->section_name, "super_user", "1");
			}
			//Update last login entry
			$this->users_model->update_last_login($result[0]['u']['id']);
			if ($this->input->post('back_url')) {
			    redirect($this->input->post('back_url'));
			} else {
			    if ($newdata['role_id'] == 5) {
				redirect(BASE_ADMIN_URL_CUSTOM . 'forum');
			    } else if ($newdata['role_id'] == 2) {
				$site_permission = check_user_permission_by_label('admin.dashboard.sites');
				if($site_permission){
				    redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard/sites');
				} else {
				    redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');
				}
			    } else {
				//redirect($this->section_name . '/dashboard');
				redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');
			    }
			}
		    } else {
			// For deleted & inactive group checking
			$this->theme->set_message(lang('inactive-account-msg'), 'error');
			//redirect($this->section_name.'/users/login');
			// exit;
		    }
		} else {
		    $this->theme->set_message(lang('invalid-email-password'), "error");
		    //redirect($this->section_name.'/users/login');
		    //exit;
		}
	    }
	}
	$this->load->model('sites/sites_model');

	$data['hotel_sites'] = $this->sites_model->get_login_sites();
	$this->theme->set_layout('layout');

	$this->theme->view($data);
    }

    /* Added For Ajax Login Check */

    public function ajax_login() {
	$this->load->model('users/users_model');
	$this->load->model('sites/sites_model');
	$this->load->model('hotels/hotels_model');
	$this->load->model('user_login_log/user_login_log_model');

	if ($this->input->post()) {
	    $this->form_validation->set_rules('username', lang('username'), 'trim|required');
	    $this->form_validation->set_rules('password', lang('password'), 'trim|required');
	    $this->form_validation->set_rules('sites', lang('sites'), 'trim');

	    $this->users_model->username = $this->input->post('username');
	    $this->users_model->password = $this->input->post('password');
	    //$this->users_model->site = $this->input->post('site');

	    $data['username'] = $this->input->post('username');
	    if ($this->form_validation->run() == TRUE) {
		$result = $this->users_model->login();
		$hotel_detail = $this->hotels_model->get_hotel_detail(1);

		// Set default site for user start

		if ($result[0]['u']['site_id'] == "0") {
		    $this->sites_model->record_per_page = 1;
		    $this->sites_model->offset = 0;
		    $first_site = $this->sites_model->get_site_listing_for_users($result[0]['u']['site_id'], $result[0]['u']['role_id'], $result[0]['u']['id']);
		    $result[0]['u']['site_id'] = $first_site[0]['s']['id'];
		} else {
		    $this->users_model->site = $result[0]['u']['site_id'];
		}
		// Set default site for user end

		if ($result[0]['u']['site_id'] != "0") {
		    $site_color_logo = $this->users_model->get_site_color_logo($result[0]['u']['site_id']);
		    $site_info = $this->sites_model->get_site_detail($result[0]['u']['site_id'],$result[0]['u']['id'],$result[0]['u']['role_id']);
		    $local_currency = $site_info['local_currency'];
		}

		if (!empty($result)) {
		    if ($result[0]['u']['status'] == 1) {
			//add all data to session
			$newdata = array(
			    'user_id' => $result[0]['u']['id'],
			    'role_id' => $result[0]['u']['role_id'],
			    'site_id' => $result[0]['u']['site_id'],
			    'local_currency' => $local_currency,
			    'hotel_id' => $hotel_detail['id'],
			    'hotel_logo' => $hotel_detail['hotel_logo'],
			    'project_id' => $result[0]['u']['project_id'],
			    'email' => $result[0]['u']['email'],
			    'username' => $result[0]['u']['username'],
			    'firstname' => $result[0]['u']['firstname'],
			    'lastname' => $result[0]['u']['lastname'],
			    'logged_in' => TRUE,
			);

			$newdata_cookie = array();
			if (isset($site_color_logo) && !empty($site_color_logo)) {
			    $newdata['site_logo'] = $site_color_logo['site_logo'];
			    $newdata['site_color'] = $site_color_logo['site_color'];

			    $newdata_cookie['site_logo'] = $site_color_logo['site_logo'];
			    $newdata_cookie['site_color'] = $site_color_logo['site_color'];
			}
			$newdata_cookie = json_encode($newdata_cookie);
			setcookie('hotel_theme_setting', $newdata_cookie, time() + (3600), "/");
			$this->session->set_custom_userdata($this->section_name, $newdata);

			// Set permission in session
			$this->allowed_permission_list($newdata['role_id']);
			if ($result[0]['u']['id'] == '1') {
			    $this->session->set_custom_userdata($this->section_name, "super_user", "1");
			}
			if (isset($this->session->userdata['session_id']) && $this->session->userdata['session_id']) {
			    $session_id = $this->session->userdata['session_id'];
			} else {
			    $session_id = '';
			}
			$data = array(
			    'user_id' => $newdata['user_id'],
			    'role_id' => $newdata['role_id'],
			    'session_id' => $session_id
			);
			$this->user_login_log_model->addLog($data);
			$data_action = 'Login';
			$site_id = $result[0]['u']['site_id'];
			$user_id = $newdata['user_id'];
			saveAuditTrail($user_id, $site_id, 'Login', $data_action);

			//Update last login entry
			$this->users_model->update_last_login($result[0]['u']['id']);
			if ($newdata['role_id'] == 5) {
			    $redirectUrl = base_url() . 'forum';
			} else if ($newdata['role_id'] == 2 || $newdata['role_id'] == 6) {
			    $site_permission = check_user_permission_by_label('admin.dashboard.sites');
			    if($site_permission){
				$redirectUrl = base_url() . 'dashboard/sites';
			    } else {
				$site_permission = check_user_permission_by_label('admin.dashboard.index');
				if($site_permission){
				    $redirectUrl = base_url() . 'dashboard';
				} else {
				    $this->session->unset_userdata($this->section_name);
				    $this->theme->set_message(lang('permission-not-allowed'), "error");
				    $resultArray = array('status' => 0, 'redirect' => base_url() . 'users/login');
				    echo json_encode($resultArray);
				    exit;
				}
			    }
			} else {
			    $site_permission = check_user_permission_by_label('admin.dashboard.index');
			    if($site_permission){
				$redirectUrl = base_url() . 'dashboard';
			    } else {
				$this->session->unset_userdata($this->section_name);
				$this->theme->set_message(lang('permission-not-allowed'), "error");
				$resultArray = array('status' => 0, 'redirect' => base_url() . 'users/login');
				echo json_encode($resultArray);
				exit;
			    }
			}

			/* if ($newdata['role_id'] == 5) {
			  $redirectUrl = base_url() . 'forum';
			  } else if ($newdata['role_id'] == 2) {
			  $redirectUrl = base_url() . 'users/index';
			  } else if ($newdata['role_id'] == 4) { // Site user
			  $redirectUrl = base_url() . 'users/action/edit/' . $result[0]['u']['id'];
			  } else {
			  $redirectUrl = base_url() . 'dashboard';
			  } */
		    } else {
			// For deleted & inactive group checking
			$this->theme->set_message(lang('inactive-account-msg'), 'error');
			//redirect($this->section_name.'/users/login');
			$resultArray = array('status' => 0, 'redirect' => base_url() . 'users/login');
			echo json_encode($resultArray);
			exit;
		    }
		    $resultArray = array('status' => 1, 'redirect' => $redirectUrl);
		    echo json_encode($resultArray);
		    exit;
		} else {
		    $this->theme->set_message(lang('invalid-email-password'), "error");
		    $resultArray = array('status' => 0, 'redirect' => base_url() . 'users/login');
		    echo json_encode($resultArray);
		    exit;
		}
	    } else {
		$this->theme->set_message(lang('invalid-email-password'), "error");
		$resultArray = array('status' => 0, 'redirect' => base_url() . 'users/login');
		echo json_encode($resultArray);
		exit;
	    }
	}
    }

    /**
     * Function logout to do logout action
     */
    public function logout() {
	$this->session->unset_userdata($this->section_name);

	//redirect($this->section_name . '/users/login');
	redirect(BASE_ADMIN_URL_CUSTOM . 'users/login');
	exit;
    }

    /**
     * Function changepassword to change password of user account
     */
    function changepassword() {
	$user_id = $this->session->userdata[$this->section_name]['user_id'];
	$password = trim(strip_tags($this->input->post('password')));
	if (isset($user_id) && $user_id != "" && $user_id != 0) {
	    if ($this->input->post('Submit')) {
		$this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[4]|max_length[50]');
		$this->form_validation->set_rules('passconf', lang('c-password'), 'trim|required|matches[password]');
		if ($this->form_validation->run() == TRUE) {
		    $user_data = $this->users_model->get_user_detail($user_id);
		    $current_password = encriptsha1($this->input->post('current_password'));
		    if ($current_password == $user_data['password']) {
			$this->users_model->changepassword($user_id, $password);
			$this->theme->set_message(lang('change-password-success'), 'success');
			redirect(BASE_ADMIN_URL_CUSTOM . 'users/changepassword');
		    } else {
			$this->theme->set_message(lang('does-not-match-currentpassword'), 'error');
			redirect(BASE_ADMIN_URL_CUSTOM . 'users/changepassword');
		    }
		}
	    }
	} else {
	    $this->theme->set_message(lang('do-login-msg-change-password'), 'info');
	    redirect(BASE_ADMIN_URL_CUSTOM . 'users/login');
	}

	// Breadcrumb settings
	$this->breadcrumb->add(lang('change-password'));

	$data = array();
	$data['cur_pass'] = $this->input->post('current_password');
	$this->theme->set('page_title', lang('change-password'));
	$this->theme->view($data);
    }

    /**
     * Function action to perform insert & update by action parameter
     * @param string $action default = 'add'
     * @param integer $id default = 0
     */
    function action($action = "add", $id = 0) {

	$login_role_id = $this->session->userdata[$this->section_name]['role_id'];
	$login_site_id = $this->session->userdata[$this->section_name]['site_id'];
	$login_user_id = $this->session->userdata[$this->section_name]['user_id'];
	if ($this->check_permission()) {
	    //Type Casting
	    $id = intval($id);
	    $action = trim(strip_tags($action));
	    $type = custom_filter_input('integer', $id);

	    //Variable Assignment
	    $firstname = "";
	    $lastname = "";
	    $username = "";
	    $email = "";
	    $password = "";
	    $passconf = "";
	    $status = "";
	    $role_id = "";
	    $site_id = "";
	    $region_id = "";
	    $avtar = "";
	    $user_report = "";
	    $this->load->model('sites/sites_model');
	    $data['hotel_sites'] = $this->users_model->get_sites_list();

	    $this->users_model->user_Id = $login_user_id;
	    $this->users_model->edit_user_Id = $id;
	    $region_list = $this->sites_model->region_list();

	    //Logic
	    switch ($action) {
		case 'add':
		    $data['user_id'] = "";
		    $role_list = $this->users_model->role_list($login_role_id);
		    break;
		case 'edit':
		    /* $this->db->where("id", $id);
		      $this->db->where("status", 1);
		      $user_exist = $this->db->get(TBL_USERS);

		      if (intval($user_exist->num_rows()) < 1) {
		      $this->theme->set_message(lang('user-not-exist'), 'error');
		      redirect($this->section_name . '/users');
		      } */
		    // Check for existing user ***
		    //Get user info by id

		    if ($id == 1 && $this->session->userdata[$this->section_name]['role_id'] != 1) {
			$this->theme->set_message(lang('permission-not-allowed'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
			exit;
		    }
		    ## site admin can access users only to their site
		    if ($login_role_id == 3) {
			$site_user_list = $this->users_model->get_site_user_list($login_site_id);
			if (!in_array($id, $site_user_list)) {
			    $this->theme->set_message(lang('permission-not-allowed'), 'error');
			    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
			}
		    }

	    //                    if ($this->session->userdata[$this->section_name]['user_id'] != 1 && $id != $this->session->userdata[$this->section_name]['user_id']) {
	    //                        $this->theme->set_message(lang('permission-not-allowed'), 'error');
	    //                        redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
	    //                        exit;
	    //                    }
	    //                    if ($id != $this->session->userdata[$this->section_name]['user_id'] && $this->session->userdata[$this->section_name]['user_id'] != 1) {
	    //                        $this->theme->set_message(lang('permission-not-allowed'), 'error');
	    //                        redirect($this->section_name . '/users');
	    //                        exit;
	    //                    }
		    $result = $this->users_model->get_user_detail($id);
		    $user_report = $this->users_model->get_user_reports($id);

		    $data['user_id'] = $result['id'];
		    if (!empty($result)) {
			//Variable assignment for edit view
			$firstname = $result['firstname'];
			$lastname = $result['lastname'];
			$username = $result['username'];
			$email = $result['email'];
			$password = '';
			$passconf = '';
			$status = isset($result['status']) ? $result['status'] : 1;
			$role_id = $result['role_id'];
			$site_id = $result['site_id'];
			$avtar = $result['avtar'];
			$role_list = $this->users_model->role_list($login_role_id);

			// lock record by passing ci object, table name and table id
			/* $flag = lock_record($this, TBL_USERS, $id, $result['lock_user_id']);
			  if (!$flag) {
			  $this->theme->set_message(lang('record-already-locked'), 'error');
			  redirect($this->section_name . '/users');
			  } */
			// end
		    } else {
			//If user not exist then redirecting to listing page
			$this->theme->set_message(lang('user-not-exist'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
		    }
		    break;
		case 'edit_profile':

		    if ($id != $login_user_id) {
			$this->theme->set_message(lang('permission-not-allowed'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
			exit;
		    }

		    $result = $this->users_model->get_user_detail($id);
		    $data['user_id'] = $result['id'];
		    if (!empty($result)) {
			//Variable assignment for edit view
			$firstname = $result['firstname'];
			$lastname = $result['lastname'];
			$username = $result['username'];
			$email = $result['email'];
			$password = '';
			$passconf = '';
			$status = isset($result['status']) ? $result['status'] : 1;
			$role_id = $result['role_id'];
			$site_id = $result['site_id'];
			$avtar = $result['avtar'];
			if($login_role_id == 5) {
			    $role_list = $this->users_model->role_list_moderator($login_role_id);
			} else {
			    $role_list = $this->users_model->role_list($login_role_id);
			}
		    } else {
			//If user not exist then redirecting to listing page
			$this->theme->set_message(lang('user-not-exist'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
		    }
		    break;
		default :
		    $this->theme->set_message(lang('action-not-allowed'), 'error');
		    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
		    break;
	    }

	    // Pass data to view file
	    $data['id'] = $id;
	    $data['firstname'] = $firstname;
	    $data['lastname'] = $lastname;
	    $data['username'] = $username;
	    $data['email'] = $email;
	    $data['password'] = $password;
	    $data['passconf'] = $passconf;
	    $data['status'] = $status;
	    $data['role_id'] = $role_id;
	    $data['site_id'] = $site_id;
	    $data['role_list'] = $role_list;
	    $data['region_list'] = isset($region_list) ? $region_list : [];
	    $data['login_role_list'] = $login_role_id;
	    $data['login_user_id'] = $login_user_id;
	    $data['avtar'] = $avtar;
	    $data['action'] = $action;
	    $data['user_report'] = $user_report;

	    $data['sites'] = array();
	    $site_results = $this->users_model->get_site_to_user($id);
	    if(!empty($site_results)){
		foreach ($site_results as $result) {
		    $data['sites'][] = $result['site_id'];
		}
	    }

	    $data['regions'] = array();
	    $region_results = $this->users_model->get_region_to_user($id);
	    if(!empty($region_results)){
		$data['region_id'] = $region_results[0]['region_id'];
		foreach ($region_results as $result) {
		    $data['regions'][] = $result['region_id'];
		}
	    }
	    //create breadcrumbs & page-title
	    if ($action == 'add') {
		$this->theme->set('page_title', lang('add-user'));
		$this->breadcrumb->add(lang('add-user'));
	    } else {
		$this->theme->set('page_title', lang('edit-user'));
		$this->breadcrumb->add(lang('edit-user'));
	    }

	    //Render view
	    $this->theme->view($data, 'admin_add');
	} else {
	    $this->theme->set_message(lang('permission-not-allowed'), 'error');
	    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
	    exit;
	}
    }

    /**
     * Function save to insert/update user data
     */
    function save() {

	//set form validation to check server side validation
	$this->load->library('form_validation');
	$login_role_id = $this->session->userdata[$this->section_name]['role_id'];
	if ($this->input->post()) {
	    $data = $this->input->post();
	    //Type Casting
	    $id = intval($data['id']);

	    $firstname = trim(strip_tags($data['firstname']));
	    $lastname = trim(strip_tags($data['lastname']));
	    $username = trim(strip_tags($data['username']));
	    $email = trim(strip_tags($data['email']));
	    if ($id == 0) {
		$password = $data['password'];
		$passconf = $data['passconf'];
		$status = $data['status'];
		$region_id = $data['region_id'];
	    } elseif ($data['password'] != '' && $data['passconf'] != '') {
		$password = $data['password'];
		$passconf = $data['passconf'];
		$status = $data['status'];
		$region_id = $data['region_id'];
	    } else {
		$password = '';
		$passconf = '';
		$status = $data['status'];
		$region_id = '';
	    }

			if ($login_role_id == '1') {
				$role_id = intval($data['role_id']);
			} else {
				$role_id = intval($data['login_role_list']);
			}
	    $role_array = array('1', '5');

	    ## in case when create user for hotel user and super admin
	    if (in_array($role_id, $role_array)) {
		$data['site_id'] = array();
	    }else{
		if(!empty($data['site_id']) && empty($data['region_id']) && !isset($data['region_id'])) {
			$data['site_id'] = array_unique($data['site_id']);
		} else if (!empty($data['region_id']) && isset($data['region_id']) && $role_id == 6) {
		    ## Assign all sites with selected region on create mode
		    $this->load->model('sites/sites_model');
		    $region_id = intval($data['region_id'][0]);
		    $data['site_id'] = [];
		    $regionalSitesData = $this->sites_model->get_site_detail_with_region_filter(0, 0, $role_id, $data['region_id']);
		    $regionalSites = isset($regionalSitesData ) ? array_column($regionalSitesData, 'id') : [];
		    $data['site_id'] = array_unique($regionalSites);
		}
	    }

	    if (isset($data['site_id']) && !empty($data['site_id'])) {
		$site_id = intval($data['site_id'][0]);
		$data_array['site_id'] = $site_id;
	    } else {
		if (isset($this->session->userdata[$this->section_name]['site_id']) && $this->session->userdata[$this->section_name]['site_id'] != '' && !in_array($role_id, $role_array)) {
		    $data_array['site_id'] = $this->session->userdata[$this->section_name]['site_id'];
		} else {
		    $data_array['site_id'] = '';
		}
	    }

	    if ($id == 1)
		$role_id = 1;

	    // field name, error message, validation rules
	    $this->users_validation_rules();

	    $image_status = true;
	    $avtar_image_name = '';
	    if (!empty($_FILES['avtar']['name'])) {
		$forumavtar_image = $_FILES['avtar']['name'];
		$ext = pathinfo($forumavtar_image, PATHINFO_EXTENSION);
		$avtar_image_name = 'forum_avtar-' . date('Ymd') . '-' . time() . '.' . $ext;
		$image_status = $this->upload_image('avtar', $avtar_image_name, 'avtar', '150', '150');
		if($image_status === false) {
		    $this->theme->set_message($this->upload->display_errors('',''), 'error');
		}
	    }

	    if ($this->form_validation->run($this) && $image_status) {
		$data_array['id'] = $id;
		$data_array['firstname'] = $firstname;
		$data_array['lastname'] = $lastname;
		$data_array['username'] = $username;
		$data_array['email'] = $email;
		if ($id == 0) {
		    $data_array['password'] = encriptsha1($password);
		} elseif ($password != '' && $passconf != '') {
		    $data_array['password'] = encriptsha1($password);
		}
		$data_array['role_id'] = $role_id;
		$data_array['status'] = $status;

		$inserted_id = $this->users_model->save_user($data_array);

		// Set sites for users (No reports for admin user)
		if($id != 1){
		    if(isset($data['site_id']) && !empty($data['site_id'])){
			$this->users_model->delete_site_to_user($inserted_id);
			$this->users_model->assign_site_to_user($data['site_id'], $inserted_id);
		    }

		    if(isset($data['region_id']) && !empty($data['region_id'])){
			$region_id = intval($data['region_id'][0]);
			$this->users_model->delete_region_to_user($inserted_id);
			$this->users_model->assign_region_to_user(array_unique($data['region_id']), $inserted_id);
		    }
		}

		//Set reports for users
		$this->users_model->delete_reports_to_user($inserted_id);
		if(isset($data['reports']) && !empty($data['reports'])){
		    $this->users_model->assign_report_to_user($data['reports'], $inserted_id);
		}

		// Save user profile
		$data_profile = array();
		if (!empty($_FILES['avtar']['name']) && $image_status) {
		    $data_profile['avtar'] = $avtar_image_name;
		}
		$this->load->model('users/users_profile_model');
		$this->users_profile_model->user_id = $inserted_id;
		$this->users_profile_model->save_profile($data_profile);
		// Save user profile

		if ($id == 0) {
		    $this->users_model->assign_permissions_to_user($role_id, $inserted_id);
		}
		/*No Need to update permissions for edit user*/
		/*else {
		    $this->users_model->delete_permissions_to_user($inserted_id);
		    $this->users_model->assign_permissions_to_user($role_id, $inserted_id);
		}*/

		if ($id == 0) {
		    $this->theme->set_message(lang('user-add-success'), 'success');
		} else {
		    $this->theme->set_message(lang('user-edit-success'), 'success');
		}
		$this->createAccountMediaWiki($username, $role_id);

		if ($this->input->post('action_page', '') == 'edit_profile') {
		    if($login_role_id == 5) {
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
		    } else {
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'dashboard');
		    }
		} else {
		    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
		}
		exit;
	    }
	} else {
	    $id = 0;
	    $firstname = "";
	    $lastname = "";
	    $email = "";
	    $password = "";
	    $passconf = "";
	    $status = "";
	    $role_id = "";
	    $site_id = "";
	    $region_id = "";
	}
	$role_list = $this->users_model->role_list($login_role_id);
	$data['hotel_sites'] = $this->users_model->get_sites_list();

	// Pass data to view file
	$data['id'] = $id;
	$data['firstname'] = $firstname;
	$data['lastname'] = $lastname;
	$data['email'] = $email;
	$data['password'] = $password;
	$data['passconf'] = $passconf;
	$data['status'] = $status;
	$data['role_id'] = $role_id;
	$data['role_list'] = $role_list;
	$data['site_id'] = $site_id;
	$data['region_id'] = $region_id;

	//Logic
	if ($id == 0) {
	    $data['user_id'] = 0;
	    $status = 1;
	    //create breadcrumbs & page-title
	    $this->theme->set('page_title', lang('add-user'));
	    $this->breadcrumb->add(lang('add-user'));
	} else {
	    $data['user_id'] = $id;
	    $status = $data['status'];
	    //create breadcrumbs & page-title
	    $this->theme->set('page_title', lang('edit-user'));
	    $this->breadcrumb->add(lang('edit-user'));
	}

	//Render view
	$this->theme->view($data, 'admin_add');
    }

    public function createAccountMediaWiki($userName, $role_id) {
	$url = base_url() . 'knowledgebase/api.php';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('action' => 'createaccount',
	    'format' => 'json',
	    'name' => $userName,
	    'password' => '111111',
	    'token' => '')
		)
	);

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($response);
	if ($result->createaccount->token) {
	    $token = $result->createaccount->token;

	    $ch1 = curl_init();
	    curl_setopt($ch1, CURLOPT_URL, $url);
	    curl_setopt($ch1, CURLOPT_POST, 1);
	    curl_setopt($ch1, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
	    curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query(array('action' => 'createaccount',
		'format' => 'json',
		'name' => $userName,
		'password' => '111111',
		'token' => $token)
		    )
	    );

	    // receive server response ...
	    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	    $finalResponse = curl_exec($ch1);
	    curl_close($ch1);
	    $finalResult = json_decode($finalResponse);
	    $userId = $finalResult->createaccount->userid;
	    if ($userId) {
		//Assign Group
		//if (($role_id == 1) || ($role_id == 2) || ($role_id == 3)) {
		if ($role_id == 1) { // Only super admin can manage knowledgebase contents
		    $groupName = 'Trustworthy';
		} else {
		    $groupName = 'Noteditable';
		}
		$userGroupData = array('ug_user' => $userId,
		    'ug_group' => $groupName);

		$this->db->insert('user_groups', $userGroupData);
		return true;
	    }
	}
    }

    function upload_image($fieldname, $filename, $folder_in_path, $width, $heigth) {

	$config['file_name'] = $filename;
	$config['upload_path'] = 'assets/uploads/' . $folder_in_path;
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size'] = '5120'; // unit is KB (Here 5 MB max)
	//echo $config['upload_path']; exit;
	if (!is_dir($config['upload_path'])) {
	    die("THE UPLOAD DIRECTORY DOES NOT EXIST");
	}

	$this->load->library('upload', $config);
	$this->upload->initialize($config);

	if (!$this->upload->do_upload($fieldname)) {
	    return false;
	} else {
	    $this->load->library('image_lib');
	    $img_cfg['image_library'] = 'gd2';
	    $img_cfg['source_image'] = 'assets/uploads/' . $folder_in_path . '/' . $filename;
	    $img_cfg['maintain_ratio'] = TRUE;
	    $config['create_thumb'] = TRUE;
	    $img_cfg['new_image'] = 'assets/uploads/' . $folder_in_path . '/thumb';
	    $img_cfg['width'] = $width;
	    $img_cfg['quality'] = 100;
	    $img_cfg['height'] = $heigth;
	    $this->image_lib->initialize($img_cfg);

	    if ($this->image_lib->resize()) {
		return true;
	    } else {
		return false;
	    }

	    //return array('upload_data' => $this->upload->data());
	}
    }

    /**
     * Function delete to user Ajax-Post
     */
    function delete() {
	if ($this->check_permission()) {
	    $data = $this->input->post();
	    //$id = intval($data['id']);
	    $id = intval(base64_decode($data['id']));

	    $result = $this->users_model->get_user_detail($id);
	    if ($id == 1) {
		echo $this->theme->message(lang('invalid-id-msg'), 'error');
		exit;
	    }
	    if (!empty($result)) {
		$res = $this->users_model->delete_user($id);
		if ($res) {
		    echo $this->theme->message(lang('user-delete-success'), 'success');
		}
	    } else {
		echo $this->theme->message(lang('invalid-id-msg'), 'error');
	    }
	} else {
	    $this->theme->set_message(lang('permission-not-allowed'), 'error');
	    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
	    exit;
	}
    }

    function valid_upload() {
	if (!empty($_FILES["avtar"]["tmp_name"])) {
	    $target_dir = "uploads/";
	    $target_file = $target_dir . basename($_FILES["avtar"]["name"]);
	    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

	    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
		$this->form_validation->set_message('valid_upload', "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
		return false;
	    } else {
		return true;
	    }
	} else {
	    return true;
	}
    }

    public function check_unique_email() {
	$data = $this->input->post();
	$result = $this->users_model->check_unique_email($data);
	if ($result > 0) {
	    $this->form_validation->set_message('check_unique_email', lang('msg-alvailable-email'));
	    return false;
	} else {
	    return true;
	}
    }

    public function check_unique_username() {
	$data = $this->input->post();
	$result = $this->users_model->check_unique_username($data);
	if ($result > 0) {
	    $this->form_validation->set_message('check_unique_username', lang('msg-alvailable-username'));
	    return false;
	} else {
	    return true;
	}
    }

    public function view_data($id = 0) {
	$login_role_id = $this->session->userdata[$this->section_name]['role_id'];
	$login_site_id = $this->session->userdata[$this->section_name]['site_id'];

	if ($login_role_id == 3) {
	    $site_user_list = $this->users_model->get_site_user_list($login_site_id);
	    if (!in_array($id, $site_user_list)) {
		$this->theme->set_message(lang('permission-not-allowed'), 'error');
		redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users');
	    }
	}
	$result = $this->users_model->get_user_detail($id);
	$role_list = $this->users_model->role_list($login_role_id);
	$data = array();
	$data = $result;
	$data['login_role_id'] = $login_role_id;
	$role_name = array('role_name' => $role_list[$result['role_id']]);
	$data = array_merge($role_name, $data);
	$data['role_list'] = $role_list;
	$data['hotel_sites'] = $this->users_model->get_sites_list();
	$this->breadcrumb->add(lang('view-user-data'));
	$this->theme->view($data);
    }

    public function forgot_password() {
	//Initializing
	$data = array();

	//If form submit
	if ($this->input->post()) {
	    //Set validation rules
	    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

	    //Logic
	    if ($this->form_validation->run() == TRUE) {
		$data['email'] = trim(strip_tags($this->input->post("email")));
		// get userdetail
		$this->users_model->email_id = $data['email'];

		$result = $this->users_model->get_user_detail_by_email($data['email']);
		if (!empty($result)) {
		    if ($result[0]['u']['status'] == '1') {
			$random_string = get_random_string();
			$data['password'] = encriptsha1($random_string);
			// send email for regerate password
			$this->users_model->forgot_password($data);

			$forgot_array['firstname'] = $result[0]['u']['firstname'];
			$forgot_array['username'] = $result[0]['u']['username'];
			$forgot_array['email'] = $data['email'];
			$forgot_array['password'] = $random_string;
			$flag = $this->send_email($forgot_array, 'forgot_password_email_template');

			$this->theme->set_message(lang('forgot-success-msg'), 'success');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users/login');
		    } else {
			// For deleted & inactive group checking
			$this->theme->set_message(lang('inactive-account-msg'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users/forgot_password');
		    }
		} else {
		    $this->theme->set_message(lang('forgot-error-msg'), 'error');
		    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'users/forgot_password');
		}
	    }
	}
	//Create page-title
	$this->theme->set('page_title', lang('forgot_password'));
	$this->theme->set_layout('layout');
	//Render view
	$this->theme->view($data);
    }

    public function send_email($data = array(), $template = NULL) {

	$this->load->library('mailer');
	$this->mailer->mail->SetFrom(SITE_FROM_EMAIL, SITE_NAME);
	$this->mailer->mail->IsHTML(true);

	$firstname = isset($data['firstname']) ? $data['firstname'] : '';
	$lastname = isset($data['lastname']) ? $data['lastname'] : '';
	$password = isset($data['password']) ? $data['password'] : '';
	$username = isset($data['username']) ? $data['username'] : '';
	$activation_code = isset($data['activation_code']) ? $data['activation_code'] : '';

	$mail_vars = array(
	    'USERNAME' => $username,
	    'name' => $firstname . ' ' . $lastname,
	    'activation_link' => base_url() . 'users/activation/' . $activation_code,
	    'SITE_NAME' => SITE_NAME,
	    'YEAR' => date('Y'),
	    'logopath' => site_base_url() . 'themes/default/images/logo.jpg',
	    'PASSWORD' => $password
	);

	$body = get_template_body($this, $template, $mail_vars, $this->session->userdata[$this->section_name]['site_lang_id']);
	$subject = get_template_subject($this, $template);

	if (trim($body) == "") {
	    return false;
	} else {
	    try {
		$this->mailer->sendmail(
			$data['email'], $firstname . " " . $lastname, $subject, $body
		);
	    } catch (phpmailerException $e) {
		echo $e->errorMessage();
		exit;
	    }
	    return true;
	}
    }

     public function entryToUserTable(){
	$returnArray = $this->users_model->compareUserAndUsersTableData();
	if(!empty($returnArray)){
	    foreach($returnArray as $key=>$value){
	       $this->createAccountMediaWiki($value['username'], $value['role_id']);
	    }
	    $this->theme->set_message('Successfully data added to User table', 'success');
	}
	redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');
    }

}
