<?php

/**
 *  Role Controller
 *
 *  To perform role management.
 *
 * @package CIDemoApplication
 * @subpackage Roles
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Roles_admin extends Base_Admin_Controller {

    function __construct() {
        parent::__construct();

        //Logic
        $this->access_control($this->access_rules());

        $this->load->library('form_validation');
        //->add(lang('role-management'), base_url() . 'roles');
        $method_name = get_method($this);
        if ($method_name == 'user_permission_matrix') {
            $this->breadcrumb->add(lang('user-management'), base_url() . 'users');
        } else {
            $this->breadcrumb->add(lang('role-management'), base_url() . 'roles');
        }
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('index', 'action', 'delete', 'save', 'permission_matrix', 'update_matrix_permission', 'user_permission_matrix', 'update_user_permission', 'delete_user_permission', 'view_data'),
                'users' => array('@'),
            )
        );
    }

    /**
     * Function index to view listing of roles
     */
    public function index() {
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->roles_model->record_per_page = $this->record_per_page;
        $this->roles_model->offset = $offset;

//        if (!empty($this->roles_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_offset", $this->roles_model->offset);
//        }
//        if (!empty($this->roles_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_page_number", $this->page_number);
//        }
        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "roles_offset", "");
                $this->session->set_custom_userdata($this->section_name, "roles_page_number", "");
            }

            if (isset($data['search_term'])) {
                $this->roles_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "roles_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "roles_search_term", "");
            }


            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->roles_model->sort_by = $data['sort_by'];
                $this->roles_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "roles_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "roles_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "roles_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "roles_sort_order", "");
            }


            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->roles_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('role-delete-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->roles_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('role-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->roles_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('role-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->roles_model->active_all_records()) {
                    echo $this->theme->message(lang('role-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->roles_model->inactive_all_records()) {
                    echo $this->theme->message(lang('role-inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['roles_search_term'])) {
            $this->roles_model->search_term = trim($this->session->userdata[$this->section_name]['roles_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['roles_sort_by'])) {
            $this->roles_model->sort_by = $this->session->userdata[$this->section_name]['roles_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['roles_sort_order'])) {
            $this->roles_model->sort_order = $this->session->userdata[$this->section_name]['roles_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['roles_offset'])) {
            $this->roles_model->offset = $this->session->userdata[$this->section_name]['roles_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['roles_page_number'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['roles_page_number'];
        }

        //Get role listing data
        $roles = $this->roles_model->get_role_listing();
        $this->roles_model->_record_count = true;
        $total_records = $this->roles_model->get_role_listing();

        // get used role here
        $used_role = $this->roles_model->get_used_role();
        // get used role here
        // Pass data to view file
        $data['roles'] = $roles;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->roles_model->search_term;
        $data['sort_by'] = $this->roles_model->sort_by;
        $data['sort_order'] = $this->roles_model->sort_order;
        $data['used_role'] = $used_role;

        //Create page-title
        $this->theme->set('page_title', lang('role-management'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * Function action to perform insert & update by action parameter
     * @param string $action default = 'add'
     * @param integer $id default = 0
     */
    public function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            custom_filter_input('integer', $id);

            //Variable Assignment
            $role_name = "";
            $role_description = "";
            $default = "";
            $status = "";

            //Logic
            switch ($action) {
                case 'add':
                    break;
                case 'edit':
                    // echo "Here"; exit;
                    $result = $this->roles_model->get_role_by_id($id);

                    if (!empty($result)) {
                        //Variable assignment for edit view
                        $role_name = $result['role_name'];
                        $role_description = $result['role_description'];
                        $default = $result['default'];
                        $status = $result['status'];
                    } else {
                        //If role not exist then redirecting to listing page
                        redirect(BASE_ADMIN_URL_CUSTOM . 'roles');
                    }
                    break;
                default :
                    $this->theme->set_message(lang('action-not-allowed'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'roles');
                    break;
            }

            // Pass data to view file
            $data['id'] = $id;
            $data['role_name'] = $role_name;
            $data['role_description'] = $role_description;
            $data['default'] = $default;
            $data['status'] = $status;
            $data['action'] = $action;

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add-role'));
                $this->breadcrumb->add(lang('add-role'));
            } else {
                $this->theme->set('page_title', lang('edit-role'));
                $this->breadcrumb->add(lang('edit-role'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'users');
            exit;
        }
    }

    /**
     * Function save to insert/update role data
     */
    public function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');

        if ($this->input->post()) {
            $data = $this->input->post();
            //Type Casting
            $id = intval($data['id']);

            //Variable Assignment
            $role_name = trim(strip_tags($data['role_name']));
            $role_description = trim(strip_tags($data['role_description']));
            $status = $data['status'];

            //Validation rules for Role
            $this->form_validation->set_rules('role_name', 'Role', 'required|is_unique[roles.role_name.id.' . $id . ']');
            $this->form_validation->set_rules('role_description', 'Role Description', 'required');

            if ($this->form_validation->run()) {
                $data_array = array(
                    'id' => $id,
                    'role_name' => $role_name,
                    'role_description' => $role_description,
                    'status' => $status
                );

                // get used role here
                $used_role = $this->roles_model->get_used_role();
                if ($data_array['status'] == 0 && in_array($data_array['id'], $used_role)) {

                    $this->theme->set_message(lang('role-assigned-to-user'), 'error');
                    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'roles/action/edit/' . $data_array['id']);
                } else {
                    $lastId = $this->roles_model->save_role($data_array);


                    if ($id == 0) {
                        $this->theme->set_message(lang('role-add-success'), 'success');
                    } else {
                        $this->theme->set_message(lang('role-edit-success'), 'success');
                    }

                    redirect(BASE_ADMIN_URL_CUSTOM . 'roles');
                    exit;
                }
            }
        } else {
            $id = 0;
            $role_name = "";
            $role_description = "";
            $status = "";
        }

        // Pass data to view file
        $data['id'] = $id;
        $data['role_name'] = $role_name;
        $data['role_description'] = $role_description;
        $data['status'] = $status;

        //create breadcrumbs & page-title
        if ($id == 0 && $id != '') {
            $this->theme->set('page_title', lang('add-role'));
            $this->breadcrumb->add(lang('add-role'));
        } else {
            $this->theme->set('page_title', lang('edit-role'));
            $this->breadcrumb->add(lang('edit-role'));
        }
        //Render view
        $this->theme->view($data, 'admin_add');
    }

    /**
     * Function delete to Role (Ajax-Post)
     */
    function delete() {
        $data = $this->input->post();
        $id = intval($data['id']);

        $used_role = $this->roles_model->get_used_role();

        if (!in_array($id, $used_role)) {
            $result = $this->roles_model->get_role_by_id($id);
            if (!empty($result)) {
                $res = $this->roles_model->delete_role($id);
                if ($res) {
                    $message = $this->theme->message(lang('role-delete-success'), 'success');
                }
            } else {
                $message = $this->theme->message(lang('invalid-id-msg'), 'error');
            }
        } else {
            $message = $this->theme->message(lang('role-assigned-to-user'), 'error');
        }
        //message
        echo $message;
        exit;
    }

    /**
     * Function permission_matrix to show listing of role-permission relation
     */
    public function permission_matrix() {
        //Load another model
        $this->load->model('permissions/permissions_model');

        $role_id = $this->session->userdata[$this->section_name]['role_id'];


        //Initialization
        $matrix_role_permissions = array();

        $matrix_permissions = $this->permissions_model->matrix_permissions_list(0, 0);
        $matrix_roles = $this->roles_model->matrix_roles($role_id);

        $role_permissions = $this->roles_model->get_all_role_permissions();

        if (!empty($role_permissions)) {
            foreach ($role_permissions as $rp) {
                $current_permissions[] = $rp['RP']['role_id'] . ',' . $rp['RP']['permission_id'];
            }
            $matrix_role_permissions = $current_permissions;
        }

        // Pass data to view file
        $data['matrix_permissions'] = $matrix_permissions;
        $data['matrix_roles'] = $matrix_roles;
        $data['matrix_role_permissions'] = $matrix_role_permissions;

        //create breadcrumbs & page-title
        $this->theme->set('page_title', lang('permission-matrix'));
        $this->breadcrumb->add(lang('permission-matrix'));

        //Render View
        $this->theme->view($data, 'admin_role_permission_list');
    }

    /**
     * Function update_matrix_permission to update permission of matrix with Ajax
     */
    public function update_matrix_permission() {
        //Initialization
        $role_permission = array();
        $role_permission = $this->input->post('permission_id');

        $this->roles_model->delete_role_permission();

        if (!empty($role_permission)) {
            foreach ($role_permission as $res) {
                $data = array();
                if ($res != 0) {
                    $result = explode(',', $res);
                    $data['role_id'] = $result[0];
                    $data['permission_id'] = $result[1];

                    $error = $this->roles_model->insert_permission($data);
                }
            }
        }
    }

    /**
     * Function user_permission_matrix to show listing of user-permission relation
     */
    public function user_permission_matrix($user_id = 0) {
        //Type casting
        $user_id = intval($user_id);
        $matrix_user_permissions = array();
        //Load another model
        $this->load->model('permissions/permissions_model');
        $this->load->model('users/users_model');

        //$matrix_permissions = $this->permissions_model->user_matrix_permissions_list(0, 0, $user_id);
        $matrix_permissions = $this->permissions_model->custom_user_matrix_permissions_list(0, 0, $user_id);
        $user_result = $this->users_model->get_user_detail($user_id);
        $role_name = $this->users_model->get_role_name_by_id($user_result['role_id']);
        
        //$matrix_roles = $this->roles_model->matrix_roles();
        $matrix_roles = $this->roles_model->matrix_roles_for_permissions();
        $user_permissions = $this->roles_model->get_user_permissions_by_id($user_id);
        if (!empty($user_permissions)) {
            foreach ($user_permissions as $rp) {
                $current_permissions[] = $rp['UP']['user_id'] . ',' . $rp['UP']['permission_id'];
            }
            $matrix_user_permissions = $current_permissions;
        } else {
            $current_permissions = array();
            $role_permissions = $this->roles_model->get_role_permissions_by_id($user_id);
            foreach ($role_permissions as $rp) {
                $current_permissions[] = $rp['RP']['role_id'] . ',' . $rp['RP']['permission_id'];
            }
            $matrix_user_permissions = $current_permissions;
        }

        // Pass data to view file
        $data['matrix_permissions'] = $matrix_permissions;
        $data['user_id'] = $user_id;
        $data['matrix_user_permissions'] = $matrix_user_permissions;
        $data['first_name'] = $user_result['firstname'];
        $data['last_name'] = $user_result['lastname'];
        $data['role_name'] = $role_name;

        //create breadcrumbs & page-title

        $this->theme->set('page_title', lang('permission-matrix'));
        $this->breadcrumb->add(lang('permission-matrix'));

        //Render View
        $this->theme->view($data, 'admin_user_permission_list');
    }

    /**
     * Function update_user_permission to update user permission of matrix
     */
    public function update_user_permission() {
        //Type casting
        $permissions = $this->input->post('permission_id');
        $user_id = intval($this->input->post('user_id'));

        $this->roles_model->delete_user_permission($user_id);
        if (!empty($permissions)) {
            foreach ($permissions as $permission_id) {
                $data['user_id'] = $user_id;
                $data['permission_id'] = $permission_id;
                $this->roles_model->insert_user_permission($data);
            }
        }
        echo $this->theme->message(lang('permission-update-success'), 'success');
        exit;
    }

    /**
     * Function delete_user_permission to delete user permission
     */
    public function delete_user_permission() {
        //Type casting
        $user_id = intval($this->input->post('user_id'));
        $this->roles_model->delete_user_permission($user_id);
        $this->roles_model->reset_user_permission($user_id);
        echo $this->theme->message(lang('permission-delete-success'), 'success');
        exit;
    }

    public function view_data($id = 0) {

        $data = array();
        $data = array('data' => $this->roles_model->get_role_by_id($id));
        $this->breadcrumb->add(lang('view-data'));
        $this->theme->view($data);
    }

}
