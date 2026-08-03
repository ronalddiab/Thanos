<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  CMS Admin Controller
 *
 *  CMS Admin controller to display add / Edit / Delete / List CMS page for each language.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author AMPT
 */
class Cms_admin extends Base_Admin_Controller {
    /*
     * Create an instance
     */

    function __construct() {
        parent::__construct();
        // Login check for admin
        $this->access_control($this->access_rules());
        $this->load->library('form_validation');
        $this->load->library('unit_test');
        // Load required helpers
        $this->load->helper('url');
        $this->load->helper('ckeditor');

        // load required models
        $this->load->model('cms/cms_meta_model');
        $this->load->model('urls/urls_model');
        $this->load->model('cms/cms_model');

        // Breadcrumb settings
        $this->breadcrumb->add(lang('cms-management'), base_url() . 'cms');
    }

    /**
     * function accessRules to check page access
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('action', 'index', 'ajax_index', 'ajax_action', 'delete', 'view_data','save', 'view', 'ajax_view'),
                'users' => array('@'),
            )
        );
    }

    public function index() {
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->cms_model->record_per_page = $this->record_per_page;
        $this->cms_model->offset = $offset;

//        if (!empty($this->cms_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_offset", $this->cms_model->offset);
//        }
//        if (!empty($this->cms_model->offset)) {
//            $this->session->set_custom_userdata($this->section_name, "roles_page_number", $this->page_number);
//        }
        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "cms_offset", "");
                $this->session->set_custom_userdata($this->section_name, "user_record_per_page", "");
            }

            if (isset($data['search_term'])) {
                $this->cms_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "cms_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "cms_search_term", "");
            }


            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->cms_model->sort_by = $data['sort_by'];
                $this->cms_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "cms_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "cms_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "cms_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "cms_sort_order", "");
            }


            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->cms_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('delete_success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->cms_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('cms-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->cms_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('cms-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->cms_model->active_all_records()) {
                    echo $this->theme->message(lang('cms-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->cms_model->inactive_all_records()) {
                    echo $this->theme->message(lang('cms-inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['cms_search_term'])) {
            $this->cms_model->search_term = trim($this->session->userdata[$this->section_name]['cms_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['cms_sort_by'])) {
            $this->cms_model->sort_by = $this->session->userdata[$this->section_name]['cms_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['cms_sort_order'])) {
            $this->cms_model->sort_order = $this->session->userdata[$this->section_name]['cms_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['cms_offset'])) {
            $this->cms_model->offset = $this->session->userdata[$this->section_name]['cms_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['user_record_per_page'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['user_record_per_page'];
        }

        //Get Email listing data
        $cms_list = $this->cms_model->get_cms_listing();
        $this->cms_model->_record_count = true;
        $total_records = $this->cms_model->get_cms_listing();
        // Pass data to view file
        $data['cms_list'] = $cms_list;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->cms_model->search_term;
        $data['sort_by'] = $this->cms_model->sort_by;
        $data['sort_order'] = $this->cms_model->sort_order;

        //Create page-title
        $this->theme->set('page_title', lang('cms-management'));

        //Render view
        $this->theme->view($data);
    }

    public function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            custom_filter_input('integer', $id);

            //Variable Assignment
            $title = "";
            $slug_url = "";
            $description = "";
            $status = "";
            $meta_title = "";
            $meta_keywords = "";
            $meta_description = "";
            $old_slug_url = "";

            //Logic
            switch ($action) {
                case 'add':
                    break;
                case 'edit':
                    $result = $this->cms_model->get_cms_detail_by_id($id);
                    if (!empty($result)) {
                        //Variable assignment for edit view
                        $title = $result[0]['c']['title'];
                        $slug_url = $result[0]['c']['slug_url'];
                        $description = $result[0]['c']['description'];
                        $status = $result[0]['c']['status'];
                        $meta_title = $result[0]['cm']['meta_title'];
                        $meta_keywords = $result[0]['cm']['meta_keywords'];
                        $meta_description = $result[0]['cm']['meta_description'];                        
                        $old_slug_url = $result[0]['c']['slug_url'];
                    } else {
                        //If role not exist then redirecting to listing page
                        redirect(BASE_ADMIN_URL_CUSTOM . 'cms');
                    }
                    break;
                default :
                    $this->theme->set_message(lang('action-not-allowed'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'cms');
                    break;
            }

            // Pass data to view file
            $data['id'] = $id;
            $data['title'] = $title;
            $data['slug_url'] = $slug_url;
            $data['description'] = $description;
            $data['status'] = $status;
            $data['meta_title'] = $meta_title;
            $data['meta_keywords'] = $meta_keywords;
            $data['meta_description'] = $meta_description;
            $data['action'] = $action;
            
            if($old_slug_url != "") {
                $data['old_slug_url'] = $old_slug_url;
            }

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add_cms'));
                $this->breadcrumb->add(lang('add_cms'));
            } else {
                $this->theme->set('page_title', lang('edit_cms'));
                $this->breadcrumb->add(lang('edit_cms'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'users');
            exit;
        }
    }

    function ajax_action($action, $language_code = '', $id = 0, $ajax_load = 1) {
        //Type Casting
        $action = trim(strip_tags($action));
        $language_code = strip_tags($language_code);
        $id = intval($id);
        $ajax_load = intval($ajax_load);

        //Initialize
        $cms_list = array();
        $cms_list_result = array();
        $data = array();
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);

        if (isset($id) && $id != '' && $id != '0') {
            $cms_list_result = $this->cms_model->get_cms_detail_by_id($id, $language_detail[0]['l']['id']);
            if (!empty($cms_list_result)) {
                if (!empty($cms_list_result[0]['c']) && !empty($cms_list_result[0]['cm']))
                    $cms_list = array_merge($cms_list_result[0]['c'], $cms_list_result[0]['cm']);
            }
        }
        //Variable assignments to view
        $data['action'] = $action;
        $data['id'] = $id;
        $data['language_id'] = $language_detail[0]['l']['id'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_code'] = $language_code;
        $data['cms'] = $cms_list;
        if ($ajax_load == '1')
            echo $this->load->view('admin_ajax_action', $data);
        else
            return $this->load->view('admin_ajax_action', $data);
    }

    public function save() {
        //set form validation to check server side validation
        //$this->load->library('form_validation');
        //$this->load->helper(array('form', 'url'));
        if ($this->input->post()) {
            $data = $this->input->post();
            //Type Casting
            $id = intval($data['id']);
            
            //Variable Assignment
            $title = trim(strip_tags($this->input->post('title')));
            $slug_url = trim(strip_tags($this->input->post('slug_url')));
            $old_slug_url = trim(strip_tags($this->input->post('old_slug_url')));
            $description = trim($this->input->post('description'));
            $status = trim(strip_tags($this->input->post('status')));
            $meta_title = trim(strip_tags($this->input->post('meta_title')));
            $meta_keywords = trim(strip_tags($this->input->post('meta_keywords')));
            $meta_description = trim(strip_tags($this->input->post('meta_description')));

            //Validation rules for Role
            //            $this->form_validation->set_rules('role_name', 'Role', 'required|is_unique[roles.role_name.id.' . $id . ']');
            //            $this->form_validation->set_rules('role_description', 'Role Description', 'required');

            $this->load->library('form_validation');
            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('slug_url', 'Slug URL', 'required|callback_check_unique_slug_url');
            
            if ($this->form_validation->run($this)) {
                $data_array = array(
                    'id' => $id,
                    'title' => $title,
                    'slug_url' => $slug_url,
                    'description' => $description,
                    'status' => $status
                    );
                $data_meta_array = array(
                    'cms_id' => $id,
                    'meta_title' => $meta_title,
                    'meta_keywords' => $meta_keywords,
                    'meta_description' => $meta_description
                );

                $site_id = $this->session->userdata[$this->section_name]['site_id'];
                $user_id = $this->session->userdata[$this->section_name]['user_id'];
             
                $lastId = $this->cms_model->save_cms($data_array,$data_meta_array,$site_id, $user_id);

                if ($id == 0) {
                    $this->theme->set_message(lang('add-cms-success'), 'success');
                } else {
                    $this->theme->set_message(lang('edit-cms-success'), 'success');
                }

                redirect(BASE_ADMIN_URL_CUSTOM . 'cms');
                exit;
            }
        } else {
            $id = 0;
            $title = "";
            $slug_url = "";
            $old_slug_url = "";
            $description = "";
            $status = "";
            $meta_title = "";
            $meta_keywords = "";
            $meta_description = "";
        }
        
        // Pass data to view file
        $data['id'] = $id;
        
        if($id > 0) {
            $data['action'] = 'edit';
        }
        $data['title'] = $title;
        $data['slug_url'] = $slug_url;
        $data['old_slug_url'] = $old_slug_url;
        
        $data['template_body'] = $description;
        $data['status'] = $status;
        $data['meta_title'] = $meta_title;
        $data['meta_keywords'] = $meta_keywords;
        $data['meta_description'] = $meta_description;
        
        if(isset($old_slug_url) && $old_slug_url != "") {            
            //$data['slug_url'] = $old_slug_url;            
        }

        //create breadcrumbs & page-title
        if ($id == 0 && $id != '') {
            $this->theme->set('page_title', lang('add-cms'));
            $this->breadcrumb->add(lang('add-cms'));
        } else {
            $this->theme->set('page_title', lang('edit-cms'));
            $this->breadcrumb->add(lang('edit-cms'));
        }
        //Render view
        $this->theme->view($data, 'admin_add');
    }

    function view_data($id = 0) {
        $data = array();
        $data = $this->cms_model->get_cms_detail_by_id($id);
        $this->breadcrumb->add(lang('view-cms'));
        $this->theme->view($data);
    }

    /**
     * action to add/edit cms page load form from ajax based on language
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     * @param string $ajax_load : will be 1 if load from ajax mode
     */
    function ajax_view($language_code = '', $id = 0, $ajax_load = 1) {
        //Type Casting
        $language_code = strip_tags($language_code);
        $id = intval($id);
        $ajax_load = intval($ajax_load);

        //Initialize
        $cms_list = array();
        $cms_list_result = array();
        $data = array();
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        if (isset($id) && $id != '' && $id != '0') {
            $cms_list_result = $this->cms_model->get_cms_detail_by_id($id, $language_detail[0]['l']['id']);
            if (!empty($cms_list_result)) {
                if (!empty($cms_list_result[0]['c']) && !empty($cms_list_result[0]['cm']))
                    $cms_list = array_merge($cms_list_result[0]['c'], $cms_list_result[0]['cm']);
            }
        }
        //Variable assignments to view
        $data['id'] = $id;
        $data['language_id'] = $language_detail[0]['l']['id'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_code'] = $language_code;
        $data['cms'] = $cms_list;
        if ($ajax_load == '1')
            echo $this->load->view('admin_ajax_view', $data);
        else
            return $this->load->view('admin_ajax_view', $data);
    }

    // Function to delete the cms record and related url management record
    function delete() {
        //Initialise
        $id = $this->input->post('id');
        $slug_url = $this->input->post('slug_url');

        //Type casting
        $id = intval($id);
        $slug_url = trim(strip_tags($slug_url));

        //logic
        if ($id != 0 && $id != '' && is_numeric($id)) {
            $data['cms'] = $this->cms_model->delete_cms($id);
            if ($slug_url != '') {
                $this->urls_model->delete_url_by_slug($slug_url);
            }
            $message = $this->theme->message(lang('delete_success'), 'success');
        } else {
            $message = $this->theme->message(lang('invalid-id-msg'), 'error');
        }
        echo $message;
    }

    /**
     * function check_unique_slug_url to check unique slug url
     */
    public function check_unique_slug_url() {
        //variable assignement
        $id = '';
        $data = $this->input->post();
        
        //Get url management id
        if ($this->input->post('old_slug_url') != '') {
            $url_detail = $this->cms_model->get_cms_id_from_slug_url($this->input->post('old_slug_url'));
            $id = $url_detail[0]['c']['id'];
        }

        $slug_url = $this->input->post('slug_url');        
        $result = $this->cms_model->check_unique_slug($data,$id);
        
        if ($result > 0) {
            $this->form_validation->set_message('check_unique_slug_url', lang('msg-alvailable-slug_url'));
            return false;
        } else {
            return true;
        }
    }
    
    /*public function check_unique_slug_url($str) {
        //variable assignement
       $data = $this->input->post();

        //Get url management id
//        if ($this->input->post('old_slug_url') != '') {
//            $url_detail = $this->urls_model->get_url_management_id_by_slug($this->input->post('old_slug_url'));
//            $id = $url_detail[0]['um']['id'];
//        }

        $result = $this->cms_model->check_unique_slug($data);
        if ($result > 0) {
            $this->form_validation->set_message('check_unique_slug_url', lang('msg-alvailable-slug_url'));
            return false;
        } else {
            return true;
        }
    } */
    
    

}
