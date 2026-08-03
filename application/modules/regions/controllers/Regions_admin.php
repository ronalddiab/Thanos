<?php

/**
 *  Hotels Admin Controller
 *
 *  To perform region management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Regions_admin extends Base_Admin_Controller {

    var $search_term;

    function __construct() {


        parent::__construct();

        $this->load->library('form_validation');
        $this->breadcrumb->add(lang('region-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'regions');
        // Login check for admin
        $this->access_control($this->access_rules());
        $this->language = $this->uri->segment(4);
        $this->load->library('unit_test');
        $this->load->model('regions/regions_model');        
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {

        return array(
            array(
                'actions' => array('index', 'action', 'delete', 'save', 'view_data'),
                'users' => array('@'),
            )
        );
    }

    /**
     * Function index to view listing of regions
     */
    function index() {
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->regions_model->record_per_page = $this->record_per_page;
        $this->regions_model->offset = $offset;


        //set sort/search parameters in pagging
        if ($this->input->post()) {

            $data = $this->input->post();
            // Search Term ***
            if (isset($data['search_term']) && !empty($data['search_term'])) {
                $this->regions_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "region_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "region_search_term", "");
            }
            // Search Term ***
            // Sort Order ***
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->regions_model->sort_by = $data['sort_by'];
                $this->regions_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "region_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "region_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "region_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "region_sort_order", "");
            }
            // Sort Order ***


            if (isset($data['type']) && $data['type'] == 'delete') {

                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = $val;
                }
                // Newly added
                if ($this->regions_model->delete_records($tempArr)) {
                    echo $this->theme->message(lang('region-delete-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('region-contains-site-error'), 'error');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = $val;
                }
                // Newly added
                if ($this->regions_model->active_records($tempArr)) {
                    echo $this->theme->message(lang('region-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = $val;
                }
                // Newly added
                if ($this->regions_model->inactive_records($tempArr)) {
                    echo $this->theme->message(lang('region-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('region-contains-site-error'), 'error');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->regions_model->active_all_records()) {
                    echo $this->theme->message(lang('region-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->regions_model->inactive_all_records()) {
                    echo $this->theme->message(lang('region-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('region-contains-site-error'), 'error');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['region_search_term'])) {
            $this->regions_model->search_term = trim($this->session->userdata[$this->section_name]['region_search_term']);
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['region_sort_by'])) {
            $this->regions_model->sort_by = $this->session->userdata[$this->section_name]['region_sort_by'];
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['region_sort_order'])) {
            $this->regions_model->sort_order = $this->session->userdata[$this->section_name]['region_sort_order'];
        }


        //Load data for url listing
        $regions = $this->regions_model->get_region_listing();
        $this->regions_model->_record_count = true;
        $total_records = $this->regions_model->get_region_listing();
        // Pass data to view file
        $this->search_term = $this->regions_model->search_term;
        $data['regions'] = $regions;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->regions_model->search_term;
        $data['sort_by'] = $this->regions_model->sort_by;
        $data['sort_order'] = $this->regions_model->sort_order;

        //Create page-title
        $this->theme->set('page_title', lang('region-management'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * Function regions_validation_rules to validate input
     */
    function regions_validation_rules() {
        $id = intval($this->input->post('id'));
        $this->form_validation->set_rules('region_name', lang('region-name'), 'trim|required|min_length[2]|callback_check_region_edit_unique[regions.region_name.id.'.$id.']');
    }


    public function check_region_edit_unique($str, $field) {
        list($table,$field,$field1,$value1) = explode('.', $field);

        if(!empty($value1)){
            $this->db->where($field1.' !=',$value1);
        }
        $this->db->where('status !=', -1);
        $query = $this->db->limit(1)->get_where($table, array($field => $str));
        
        $this->form_validation->set_message('check_region_edit_unique', lang('meg-region-name-already-exists'));
        return $query->num_rows() === 0;
    }

    function action($action = "add", $id = 0) {

        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            $type = custom_filter_input('integer', $id);

            //Variable Assignment
            $region_name = "";
            $status = "";

            //Logic
            switch ($action) {
                case 'add':
                    $data['region_id'] = "";
                    break;
                case 'edit':
                    $result = $this->regions_model->get_region_detail($id);
                    $data['region_id'] = $result['id'];
                    if (!empty($result)) {
                        //Variable assignment for edit view
                        $region_name = $result['region_name'];
                        $status = isset($result['status']) ? $result['status'] : 1;
                    } else {
                        //If region not exist then redirecting to listing page
                        $this->theme->set_message(lang('region-not-exist'), 'error');
                        redirect(BASE_ADMIN_URL_CUSTOM . 'regions');
                    }
                    break;
                default :
                    $this->theme->set_message(lang('action-not-allowed'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM. 'regions');
                    break;
            }

            // Pass data to view file
            $data['id'] = $id;
            $data['region_name'] = $region_name;
            $data['status'] = $status;
            $data['action'] = $action;

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add-region'));
                $this->breadcrumb->add(lang('add-region'));
            } else {
                $this->theme->set('page_title', lang('edit-region'));
                $this->breadcrumb->add(lang('edit-region'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'regions');
            exit;
        }
    }

    /**
     * Function save to insert/update region data
     */
    function save() {

        //set form validation to check server side validation
        $this->load->library('form_validation');

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();

            //Type Casting
            $id = intval($data['id']);
            $region_name = trim(strip_tags($data['region_name']));
            if ($id == 0) {
                $status = $data['status'];
            } else {
                $status = $data['status'];
            }

            // field name, error message, validation rules
            $this->regions_validation_rules();

            if ($this->form_validation->run($this)) {
                $data_array['id'] = $id;
                $data_array['region_name'] = $region_name;
                $data_array['status'] = $status;

                $this->regions_model->save_region($data_array);

                if ($id == 0) {
                    $this->theme->set_message(lang('region-add-success'), 'success');
                } else {
                    $this->theme->set_message(lang('region-edit-success'), 'success');
                }
                if(isset($data_array['id']))
                {
                    $data_action = 'Update';
                }
                else
                {
                    $data_action = 'Create';
                }
 
                $site_id = $this->session->userdata[$this->section_name]['site_id'];
                $user_id = $this->session->userdata[$this->section_name]['user_id'];
                saveAuditTrail($user_id, $site_id, 'Regions ('.$region_name.')', $data_action);

                redirect(BASE_ADMIN_URL_CUSTOM . 'regions');
                exit;
            }
        } else {
            $id = 0;
            $region_name = "";
            $status = "";
        }

        // Pass data to view file
        $data['id'] = $id;
        $data['region_name'] = $region_name;
        $data['status'] = $status;


        //Logic
        if ($id == 0) {
            $data['region_id'] = 0;
            $status = 1;
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('add-region'));
            $this->breadcrumb->add(lang('add-region'));
        } else {
            $data['region_id'] = $id;
            $status = $data['status'];
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('edit-region'));
            $this->breadcrumb->add(lang('edit-region'));
        }

        //Render view
        $this->theme->view($data, 'admin_add');
    }

    /**
     * Function delete to region Ajax-Post
     */
    function delete() {
        if ($this->check_permission()) {
            $data = $this->input->post();
            //$id = intval($data['id']);
            $id = intval(base64_decode($data['id']));

            $result = $this->regions_model->get_region_detail($id);

            if (!empty($result)) {
                $res = $this->regions_model->delete_region($id);
                if ($res) {
                    echo $this->theme->message(lang('region-delete-success'), 'success');
                }
            } else {
                echo $this->theme->message(lang('invalid-id-msg'), 'error');
            }
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'regions');
            exit;
        }
    }

    public function view_data($id = 0) {
        $result = $this->regions_model->get_region_detail($id);
        $data = array();
        $data = $result;
        $this->breadcrumb->add(lang('view-region'));
        $this->theme->view($data);
    }

}
