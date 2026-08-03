<?php



/**

 *  Permission Controller

 *

 *  To perform permission management.

 *

 * @package CIDemoApplication

 * @subpackage Permissions

 * @copyright	(c) 2013, TatvaSoft

 * @author panks

 */

class Permissions_admin extends Base_Admin_Controller {



    function __construct() {

        parent::__construct();



        //Logic

        $this->access_control($this->access_rules());



        $this->load->library('form_validation');



        //Create breadcrumb

        $this->breadcrumb->add(lang('permission-management'), base_url() . 'permissions');

    }



    /**

     * Function for set permission

     * @return array

     */

    private function access_rules() {

        return array(

            array(

                'actions' => array('index', 'action', 'delete', 'save', 'view_data'),

                'users' => array('@'),

            )

        );

    }



    /**

     * Function index to view listing of permissions

     */

    public function index() {

        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);

        $this->permissions_model->record_per_page = $this->record_per_page;

        $this->permissions_model->offset = $offset;



//        if (!empty($this->permissions_model->offset)) {

//            $this->session->set_custom_userdata($this->section_name, "permissions_offset", $this->permissions_model->offset);

//        }

//        if (!empty($this->permissions_model->offset)) {

//            $this->session->set_custom_userdata($this->section_name, "permissions_page_number", $this->page_number);

//        }

        //set sort/search parameters in pagging

        if ($this->input->post()) {

            $data = $this->input->post();



            if (empty($data['page_number'])) {

                $this->session->set_custom_userdata($this->section_name, "permissions_offset", "");

                $this->session->set_custom_userdata($this->section_name, "permissions_page_number", "");

            }





            if (isset($data['search_term'])) {

                $this->permissions_model->search_term = trim($data['search_term']);

                $this->session->set_custom_userdata($this->section_name, "permissions_search_term", $this->input->post('search_term'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "url_search_term", "");

            }





            if (isset($data['sort_by']) && $data['sort_order']) {

                $this->permissions_model->sort_by = $data['sort_by'];

                $this->permissions_model->sort_order = $data['sort_order'];

                $this->session->set_custom_userdata($this->section_name, "permissions_sort_by", $this->input->post('sort_by'));

                $this->session->set_custom_userdata($this->section_name, "permissions_sort_order", $this->input->post('sort_order'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "permissions_sort_by", "");

                $this->session->set_custom_userdata($this->section_name, "permissions_sort_order", "");

            }



            if (isset($data['type']) && $data['type'] == 'delete') {

                if ($this->permissions_model->delete_records($data['ids'])) {

                    echo $this->theme->message(lang('permission-delete-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active') {

                if ($this->permissions_model->active_records($data['ids'])) {

                    echo $this->theme->message(lang('permission-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive') {



                if ($this->permissions_model->inactive_records($data['ids'])) {

                    echo $this->theme->message(lang('permission-inactive-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->permissions_model->active_all_records()) {

                    echo $this->theme->message(lang('permission-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive_all') {

                if ($this->permissions_model->inactive_all_records()) {

                    echo $this->theme->message(lang('permission-inactive-success'), 'success');

                    exit;

                }

            }

        }





        if (!empty($this->session->userdata[$this->section_name]['permissions_search_term'])) {

            $this->permissions_model->search_term = trim($this->session->userdata[$this->section_name]['permissions_search_term']);

        }

        if (!empty($this->session->userdata[$this->section_name]['permissions_sort_by'])) {

            $this->permissions_model->sort_by = $this->session->userdata[$this->section_name]['permissions_sort_by'];

        }

        if (!empty($this->session->userdata[$this->section_name]['permissions_sort_order'])) {

            $this->permissions_model->sort_order = $this->session->userdata[$this->section_name]['permissions_sort_order'];

        }

        if (!empty($this->session->userdata[$this->section_name]['permissions_offset'])) {

            $this->permissions_model->offset = $this->session->userdata[$this->section_name]['permissions_offset'];

        }

        if (!empty($this->session->userdata[$this->section_name]['permissions_page_number'])) {

            $this->page_number = $this->session->userdata[$this->section_name]['permissions_page_number'];

        }



        //Get permission listing data

        $permissions = $this->permissions_model->get_permission_listing();

        $this->permissions_model->_record_count = true;

        $total_records = $this->permissions_model->get_permission_listing();

        // pre($permissions);

        //Pass data to view file

        $data['permissions'] = $permissions;

        $data['page_number'] = $this->page_number;

        $data['total_records'] = $total_records;

        $data['search_term'] = $this->permissions_model->search_term;

        $data['sort_by'] = $this->permissions_model->sort_by;

        $data['sort_order'] = $this->permissions_model->sort_order;



        //Create page-title

        $this->theme->set('page_title', lang('permission-management'));



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

            $permission_label = "";

            $permission_title = "";

            $parent_id = "";

            $status = "";



            $parent_dropdown[0] = 'Root';



            $permission_dropdown = $this->permissions_model->get_parent_selectlist(0, 0);

            foreach ($permission_dropdown as $permission_id => $title) {

                $parent_dropdown[$permission_id] = $title;

            }

            $parent_list = $parent_dropdown;



            //Logic

            switch ($action) {

                case 'add':

                    break;

                case 'edit':

                    $result = $this->permissions_model->get_permission_by_id($id);

                    if (!empty($result)) {

                        //Variable assignment for edit view

                        $permission_label = $result['permission_label'];

                        $permission_title = $result['permission_title'];

                        $parent_id = $result['parent_id'];

                        $status = $result['status'];

                    } else {

                        //If permission not exist then redirecting to listing page

                        redirect(BASE_ADMIN_URL_CUSTOM . 'permissions');

                    }

                    break;

                default :

                    $this->theme->set_message(lang('action-not-allowed'), 'error');

                    redirect(BASE_ADMIN_URL_CUSTOM . 'permissions');

                    break;

            }



            // Pass data to view file

            $data['id'] = $id;

            $data['permission_label'] = $permission_label;

            $data['permission_title'] = $permission_title;

            $data['parent_id'] = $parent_id;

            $data['parent_list'] = $parent_list;

            $data['status'] = $status;



            //create breadcrumbs & page-title

            if ($action == 'add') {

                $this->theme->set('page_title', lang('add-permission'));

                $this->breadcrumb->add(lang('add-permission'));

            } else {

                $this->theme->set('page_title', lang('edit-permission'));

                $this->breadcrumb->add(lang('edit-permission'));

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

     * Function save to insert/update permission data

     */

    public function save() {

        //set form validation to check server side validation

        $this->load->library('form_validation');



        if ($this->input->post()) {

            $data = $this->input->post();



            //Type Casting

            $id = intval($data['id']);



            //Variable Assignment

            $permission_label = trim(strip_tags($data['permission_label']));

            $permission_title = trim(strip_tags($data['permission_title']));

            $parent_id = intval($data['parent_id']);

            $status = $data['status'];

            //Validation rules for Permission

            if (isset($id) && $id != 0 && $parent_id != 0) {

                $Ids = $this->permissions_model->get_child_permission_array($id);

                if (in_array($parent_id, $Ids, true)) {

                    $this->form_validation->set_rules('parent_id', lang('parent'), 'child_permission');

                }

                if ($parent_id == $id) {

                    $this->form_validation->set_rules('parent_id', lang('parent'), 'child_permission');

                }

            }

            $this->form_validation->set_rules('permission_label', 'Permission Label', 'required|is_unique[permissions.permission_label.id.' . $id . ']');

            $this->form_validation->set_rules('permission_title', 'Permission Title', 'required|is_unique[permissions.permission_title.id.' . $id . ']');



            if ($this->form_validation->run()) {

                $data_array = array(

                    'id' => $id,

                    'permission_label' => $permission_label,

                    'permission_title' => $permission_title,

                    'parent_id' => $parent_id,

                    'status' => $status

                );



                $lastId = $this->permissions_model->save_permissions($data_array);



                if ($id == 0) {

                    $this->theme->set_message(lang('permission-add-success'), 'success');

                } else {

                    $this->theme->set_message(lang('permission-edit-success'), 'success');

                }



                redirect(BASE_ADMIN_URL_CUSTOM . 'permissions');

                exit;

            }

        } else {

            $id = 0;

            $permission_label = '';

            $permission_title = '';

            $parent_id = '';

            $status = '';

        }



        $parent_dropdown[0] = 'Root';

        $dropdown = $this->permissions_model->get_parent_selectlist(0, 0);

        foreach ($dropdown as $permission_id => $title) {

            $parent_dropdown[$permission_id] = $title;

        }

        $parent_list = $parent_dropdown;



        // Pass data to view file

        $data['id'] = $id;

        $data['permission_label'] = $permission_label;

        $data['permission_title'] = $permission_title;

        $data['parent_id'] = $parent_id;

        $data['parent_list'] = $parent_list;

        $data['status'] = $status;



        //create breadcrumbs & page-title

        if ($id == 0 && $id != '') {

            $this->theme->set('page_title', lang('add-permission'));

            $this->breadcrumb->add(lang('add-permission'));

        } else {

            $this->theme->set('page_title', lang('edit-permission'));

            $this->breadcrumb->add(lang('edit-permission'));

        }

        //Render view

        $this->theme->view($data, 'admin_add');

    }



    /**

     * Function delete to Permission (Ajax-Post)

     */

    function delete() {

        $data = $this->input->post();

        $id = intval($data['id']);



        $result = $this->permissions_model->get_permission_by_id($id);

        if (!empty($result)) {

            $res = $this->permissions_model->delete_permission($id);

            if ($res) {

                $message = $this->theme->set_message(lang('permission-delete-success'), 'success');

            }

        } else {

            $message = $this->theme->set_message(lang('invalid-id-msg'), 'error');

        }



        //message

        echo $message;

    }



    function view_data($id = 0) {

        $data = array();

        //$data = array('data' => $this->permissions_model->get_all_permission_by_id($id));

        $data = array('data' => $this->permissions_model->get_permission_by_id($id));

        $parent_id = $data['data']['parent_id'];

        $parent_dropdown[0] = 'Root';



        $permission_dropdown = $this->permissions_model->get_parent_selectlist(0, 0);

        foreach ($permission_dropdown as $permission_id => $title) {

            $parent_dropdown[$permission_id] = $title;

        }

        $parent_list = $parent_dropdown;

        $data['parent_list'] = $parent_list;

        $data['parent_id'] = $parent_id;

        $this->breadcrumb->add(lang('view-pemission-data'));

        $this->theme->view($data);

    }



}

