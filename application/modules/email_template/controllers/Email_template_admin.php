<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Email Template Admin Controller
 *
 *  Email Template Admin controller to display add / Edit / Delete / List email template page for each language.
 * 
 * @package CIDemoApplication
 *  
 * @copyright	(c) 2013
 * @author AMPT 
 */
class Email_template_admin extends Base_Admin_Controller {
    /*
     * Create an instance
     */

    function __construct() {
        parent::__construct();
        // Login check for admin
        $this->access_control($this->access_rules());

        // Load required helpers        
        $this->load->helper('ckeditor');
        // Breadcrumb settings
        $this->breadcrumb->add('Email Template Management', base_url() . '/email_template');
    }

    /**
     * function accessRules to check page access     
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('action', 'index', 'ajax_index', 'ajax_action', 'delete', 'view_data', 'view', 'ajax_view', 'save'),
                'users' => array('@'),
            )
        );
    }

    /**
     * action to display language wise list of email template  page
     * @param string $language_code
     */
    public function index() {
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->email_template_model->record_per_page = $this->record_per_page;
        $this->email_template_model->offset = $offset;

//        if (!empty($this->email_template_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_offset", $this->email_template_model->offset);
//        }
//        if (!empty($this->email_template_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_page_number", $this->page_number);
//        }
        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "email_template_offset", "");
                $this->session->set_custom_userdata($this->section_name, "email_template_page_number", "");
            }

            if (isset($data['search_term'])) {
                $this->email_template_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "email_template_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "email_template_search_term", "");
            }


            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->email_template_model->sort_by = $data['sort_by'];
                $this->email_template_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "email_template_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "email_template_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "email_template_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "email_template_sort_order", "");
            }


            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->email_template_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('delete_success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->email_template_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('email-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->email_template_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('email-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->email_template_model->active_all_records()) {
                    echo $this->theme->message(lang('email-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->email_template_model->inactive_all_records()) {
                    echo $this->theme->message(lang('email-inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['email_template_search_term'])) {
            $this->email_template_model->search_term = trim($this->session->userdata[$this->section_name]['email_template_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['email_template_sort_by'])) {
            $this->email_template_model->sort_by = $this->session->userdata[$this->section_name]['email_template_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['email_template_sort_order'])) {
            $this->email_template_model->sort_order = $this->session->userdata[$this->section_name]['email_template_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['email_template_offset'])) {
            $this->email_template_model->offset = $this->session->userdata[$this->section_name]['email_template_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['email_template_page_number'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['email_template_page_number'];
        }

        //Get Email listing data
        $email_template_model_list = $this->email_template_model->get_email_template_listing();
        $this->email_template_model->_record_count = true;
        $total_records = $this->email_template_model->get_email_template_listing();

        // Pass data to view file
        $data['email_template_model_list'] = $email_template_model_list;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->email_template_model->search_term;
        $data['sort_by'] = $this->email_template_model->sort_by;
        $data['sort_order'] = $this->email_template_model->sort_order;

        //Create page-title
        $this->theme->set('page_title', lang('email-template-management'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * action to add/edit Email Template page
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     */
    public function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            custom_filter_input('integer', $id);

            //Variable Assignment
            $template_name = "";
            $template_subject = "";
            $template_body = "";
            $status = "";

            //Logic
            switch ($action) {
                case 'add':
                    break;
                case 'edit':
                    $result = $this->email_template_model->get_template_detail_by_id($id);
                    if (!empty($result)) {
                        //Variable assignment for edit view
                        $template_name = $result[0]['c']['template_name'];
                        $template_subject = $result[0]['c']['template_subject'];
                        $template_body = $result[0]['c']['template_body'];
                        $status = $result[0]['c']['status'];
                    } else {
                        //If role not exist then redirecting to listing page
                        redirect(BASE_ADMIN_URL_CUSTOM . 'email_template');
                    }
                    break;
                default :
                    $this->theme->set_message(lang('action-not-allowed'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'email_template');
                    break;
            }

            // Pass data to view file
            $data['id'] = $id;
            $data['template_name'] = $template_name;
            $data['template_subject'] = $template_subject;
            $data['template_body'] = $template_body;
            $data['status'] = $status;

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add-email-template'));
                $this->breadcrumb->add(lang('add-email-template'));
            } else {
                $this->theme->set('page_title', lang('edit-email-template'));
                $this->breadcrumb->add(lang('edit-email-template'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'users');
            exit;
        }
    }

    public function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');

        if ($this->input->post()) {
            $data = $this->input->post();
            //Type Casting
            $id = intval($data['id']);

            //Variable Assignment
            $template_name = trim(strip_tags($data['template_name']));
            $template_subject = trim(strip_tags($data['template_subject']));
            $template_body = trim(strip_tags($data['template_body']));
            $status = $data['status'];

            //Validation rules for Role
//            $this->form_validation->set_rules('role_name', 'Role', 'required|is_unique[roles.role_name.id.' . $id . ']');
//            $this->form_validation->set_rules('role_description', 'Role Description', 'required');

            $this->form_validation->set_rules('template_name', 'Template Name', 'required|callback_check_space|callback_check_unique_email_template_name');
            $this->form_validation->set_rules('template_subject', 'Template Subject', 'required');

            if ($this->form_validation->run($this)) {
                $data_array = array(
                    'id' => $id,
                    'template_name' => $template_name,
                    'template_subject' => $template_subject,
                    'template_body' => $template_body,
                    'status' => $status
                );

                $lastId = $this->email_template_model->save_template($data_array);


                if ($id == 0) {
                    $this->theme->set_message(lang('add-email-template-success'), 'success');
                } else {
                    $this->theme->set_message(lang('edit-email-template-success'), 'success');
                }

                redirect(BASE_ADMIN_URL_CUSTOM . 'email_template');
                exit;
            }
        } else {
            $id = 0;
            $template_name = "";
            $template_subject = "";
            $template_body = "";
            $status = "";
        }

        // Pass data to view file
        $data['id'] = $id;
        $data['template_name'] = $template_name;
        $data['template_subject'] = $template_subject;
        $data['template_body'] = $template_body;
        $data['status'] = $status;

        //create breadcrumbs & page-title
        if ($id == 0 && $id != '') {
            $this->theme->set('page_title', lang('add-email-template'));
            $this->breadcrumb->add(lang('add-email-template'));
        } else {
            $this->theme->set('page_title', lang('edit-email-template'));
            $this->breadcrumb->add(lang('edit-email-template'));
        }
        //Render view
        $this->theme->view($data, 'admin_add');
    }

    function view_data($id = 0) {
        $data = array();
        $data = $this->email_template_model->get_template_detail_by_id($id);
        $this->breadcrumb->add(lang('view-email-template-data'));
        $this->theme->view($data);
    }

    function delete() {
        //Initialise
        $id = $this->input->post('id');
        $slug_url = $this->input->post('slug_url');

        //Type casting
        $id = intval($id);
        $slug_url = trim(strip_tags($slug_url));

        //logic
        if ($id != 0 && $id != '' && is_numeric($id)) {
            $data['email_template'] = $this->email_template_model->delete_email_template($id);
            $message = $this->theme->message(lang('delete_success'), 'success');
        } else {
            $message = $this->theme->message(lang('invalid-id-msg'), 'error');
        }
        echo $message;
    }

    /**
     * function check_unique_email_template_name to check unique template name     
     */
    public function check_unique_email_template_name() {
        //variable assignement
        $id = '';
        //pre($this->input->post());
        //Get url management id
        if ($this->input->post('template_name') != '') {
            $template_detail = $this->email_template_model->get_template_id_by_name($this->input->post('template_name'), $this->lang_id);

            $id = $template_detail[0]['c']['id'];
        }
        $template_name = $this->input->post('template_name');
        $result = $this->email_template_model->check_unique_template_name($template_name, $id);

        if (count($result) > 0) {
            $this->form_validation->set_message('check_unique_email_template_name', lang('msg-alvailable-template_name'));
            return false;
        } else {
            return true;
        }
    }

    public function check_space() {

        $template_name = $this->input->post('template_name');
        if (strpos($template_name, " ")) {
            $this->form_validation->set_message('check_space', lang('msg-space-not-allowed'));
            return false;
        } else {
            return true;
        }
    }

}
