<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Categories Admin Controller
 *
 *  Categories Admin controller to display Add / Edit / Delete / List Audit page for each language.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author HTDO
 */
class Audit_admin extends Base_Admin_Controller {
    /*
     * Create an instance
     */

    function __construct() {
        parent::__construct();
        // Login check for admin
        $this->load->model('audit/audit_model');
                
        $this->access_control($this->access_rules());

        $this->user_id = isset($this->session->userdata[$this->section_name]['user_id'])?$this->session->userdata[$this->section_name]['user_id']:0;
        $this->role_id = isset($this->session->userdata[$this->section_name]['role_id'])?$this->session->userdata[$this->section_name]['role_id']:0;
        $this->site_id = isset($this->session->userdata[$this->section_name]['site_id'])?$this->session->userdata[$this->section_name]['site_id']:0;

        $this->audit_model->site_id = $this->site_id;

        // Load required helpers
        $this->load->helper('url');

        // Breadcrumb settings
        // $this->breadcrumb->add(lang('audit_management'), base_url(). 'audit');
    }

    /**
     * function accessRules to check page access
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('action', 'index', 'ajax_index', 'ajax_action', 'delete', 'view','inventory'),
                'users' => array('@'),
            )
        );
    }

    /**
     * action to load list of categories based on language passed or from default language
     * @param string $language_code
     */
    function index() {
        if ($this->input->post('page_number') != "") {
            $this->page_number = $this->input->post('page_number');
        }

        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->audit_model->record_per_page = $this->record_per_page;
        $this->audit_model->offset = $offset;

        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();            
            if (isset($data['search_term_start_date'])) {
                $this->audit_model->search_term_start_date = $data['search_term_start_date'];
                $this->session->set_custom_userdata($this->section_name, "search_term_start_date", $this->audit_model->search_term_start_date);
            }
            if (isset($data['search_term_end_date'])) {
                $this->audit_model->search_term_end_date = $data['search_term_end_date'];
                $this->session->set_custom_userdata($this->section_name, "search_term_end_date", $this->audit_model->search_term_end_date);
            }
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->audit_model->sort_by = $data['sort_by'];
                $this->audit_model->sort_order = $data['sort_order'];
            }
            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->audit_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('delete_success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->audit_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->audit_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->audit_model->active_all_records()) {
                    echo $this->theme->message(lang('active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->audit_model->inactive_all_records()) {
                    echo $this->theme->message(lang('inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['search_term_start_date'])) {
            $this->audit_model->search_term_start_date = $this->session->userdata[$this->section_name]['search_term_start_date'];
        }        
        if (!empty($this->session->userdata[$this->section_name]['search_term_end_date'])) {
            $this->audit_model->search_term_end_date = $this->session->userdata[$this->section_name]['search_term_end_date'];
        }

        $energy_audit_list = $this->audit_model->get_energy_audit_listing();
        $inventory_list = $this->audit_model->get_inventory_listing();

        $this->audit_model->_record_count = true;
        $total_records = $this->audit_model->get_energy_audit_listing();
        $inventory_count = $this->audit_model->get_inventory_listing();

        //Variable assignments to view
        $data = array(
            'energy_audit_list' => $energy_audit_list,
            'inventory_list' => $inventory_list,
            'page_number' => $this->page_number,
            'total_records' => $total_records,
            'inventory_count' => $inventory_count,
            'search_term_start_date' => $this->audit_model->search_term_start_date,
            'search_term_end_date' => $this->audit_model->search_term_end_date,
            'sort_by' => $this->audit_model->sort_by,
            'sort_order' => $this->audit_model->sort_order
        );
        $this->theme->view($data);
    }

    /**
     * action view to view categories page
     * @param string $language_code
     * @param string $id : if in edit mode
     */
    function view($language_code = '', $id = 0) {
        //Type Casting
        $language_code = strip_tags($language_code);
        $id = intval($id);

        //Initialize
        $energy_audit = array();
        $energy_audit_result = array();
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        // Logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if (isset($id) && $id != '' && $id != '0') {
            $energy_audit_result = $this->audit_model->get_category_view_detail_by_id($id, $language_detail[0]['l']['id']);
            $energy_audit = array_merge($energy_audit_result[0]['c'], $energy_audit_result[0]['cm']);
        }
        $language_list = $this->languages_model->get_languages(); // get list of languages
        // Breadcrumb settings

        $this->theme->set('page_title', lang('energy_audit'));
        $this->breadcrumb->add(lang('energy_audit'));

        //Variable assignments to view
        $data = array();
        $data['id'] = $id;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['category'] = $energy_audit;
        $data['content'] = $this->load->view('admin_ajax_view', $data, TRUE);
        $this->theme->view($data);
    }

    function valid_upload() {        
        if (!empty($_FILES["full_report"]["tmp_name"])) {
            $check = getimagesize($_FILES["full_report"]["tmp_name"]);            
            
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["full_report"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            
            $valid_formats = array("pdf");
            if (!in_array($imageFileType, $valid_formats)) {                
                $this->form_validation->set_message('valid_upload', "Sorry, only PDF files are allowed.");
                return false;
            } else {                
                // Image size validation
                if ($_FILES["full_report"]["size"] > (1024 * 1024 * 15)) {
                    $this->form_validation->set_message('valid_upload', 'Sorry, your file is too large. Maximum image size should be < 15MB');
                    return false;
                } else {
                    return true;
                }
            }            
        } else {
            $this->form_validation->set_message('valid_upload', "The full report is required.");
            return false;
        }
    }
    
    function valid_inventory_upload() {        
        if (!empty($_FILES["full_report"]["tmp_name"])) {
            $check = getimagesize($_FILES["full_report"]["tmp_name"]);            
            
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["full_report"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            
            $valid_formats = array("xls","xlsx","pdf","doc","docx","dwg");
            if (!in_array($imageFileType, $valid_formats)) {                
                $this->form_validation->set_message('valid_upload', "Sorry, only xls or xlsx files are allowed.");
                return false;
            } else {                
                // Image size validation
                if ($_FILES["full_report"]["size"] > (1024 * 1024 * 15)) {
                    $this->form_validation->set_message('valid_upload', 'Sorry, your file is too large. Maximum image size should be < 15MB');
                    return false;
                } else {
                    return true;
                }
            }            
        } else {
            $this->form_validation->set_message('valid_upload', "The full report is required.");
            return false;
        }
    }
    
    /**
     * action to add/edit audit page
     * @param string $action : add or edit     
     * @param string $id : if in edit mode
     */
    function action($action, $id = 0) {        
        //Type Casting
        $action = trim(strip_tags($action));        
        $id = intval($id);
        
        if ($this->input->post('energyauditsubmit')) {
            $audit_on = trim(strip_tags($this->input->post('audit_on')));
            $full_report_title = trim(strip_tags($this->input->post('full_report_title')));
            $executive_summary_title = trim(strip_tags($this->input->post('executive_summary_title')));
        }

        if ($action == 'add' || $action == 'edit') {
            //Initialize
            $energy_audit = array();
            $energy_audit_result = array();

            // Logic
            if ($this->input->post('energyauditsubmit')) {
                //Validation Check
                
                $this->load->library('form_validation');
                $this->form_validation->set_rules('audit_on', lang('audit_on'), 'trim|required');
                $this->form_validation->set_rules('full_report_title', lang('full_report_title'), 'trim|required');
                $this->form_validation->set_rules('executive_summary_title', lang('executive_summary_title'), 'trim|required');
                
                if (!empty($_FILES["full_report"]["tmp_name"])) {
                    $this->form_validation->set_rules('full_report', lang('full_report'), 'callback_valid_upload');
                }
                
                if (!empty($_FILES["executive_summary"]["tmp_name"])) {
                    $this->form_validation->set_rules('executive_summary', lang('executive_summary'), 'callback_valid_upload');
                }
                
                if ($this->form_validation->run($this) == true) {
                    $this->audit_model->audit_on = date('Y-m-d',strtotime($audit_on));
                    $this->audit_model->full_report_title = $full_report_title;
                    $this->audit_model->executive_summary_title = $executive_summary_title;
                    
                    if (isset($_FILES['full_report']['name'])) {
                        $config['upload_path'] = BASE_PATH_CUSTOM . "/assets/uploads/";
                        $config['max_size'] = '2048';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        $valid_formats = array("pdf");
                        $imagename = $_FILES['full_report']['name'];

                        $size = $_FILES['full_report']['size'];
                        $i = strrpos($imagename, ".");
                        if (!$i) {
                            $ext = '';
                        }
                        $l = strlen($imagename) - $i;
                        $ext = substr($imagename, $i + 1, $l);
                        $full_report_name = 'full_report_' . rand(11111, 9999999) . '.' . $ext;
                        if ($ext) {
                            if (in_array($ext, $valid_formats)) {
                                // procedure further if and only if image size can not be more than 15MB.
                                if ($size < (1024 * 1024 * 15)) {
                                    $uploadedfile = $_FILES['full_report']['tmp_name'];
                                    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/audit/" . $full_report_name;
                                    $_movestatus = move_uploaded_file($uploadedfile, $target_file);

                                    $this->audit_model->full_report = $full_report_name;
                                    $energy_audit['full_report'] = $full_report_name;
                                    if (!$_movestatus) {
                                        $this->theme->set_message('site image is not uploaded', 'error');
                                    } else {

                                        // Delete Old file
                                        $oldfile = BASE_PATH_CUSTOM . "/assets/uploads/audit" . $sitedetail['full_report'];
                                        if (file_exists($oldfile)) {
                                            unlink($oldfile);
                                        }
                                    }
                                } else {
                                    $this->theme->set_message('full report size is too large', 'error');
                                }
                            } else {
                                $this->theme->set_message('full report extension is not .pdf format', 'error');
                            }
                        }
                    }
                    if (isset($_FILES['executive_summary']['name'])) {
                        $config['upload_path'] = BASE_PATH_CUSTOM . "/assets/uploads/";
                        $config['max_size'] = '2048';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        $valid_formats = array("pdf");
                        $imagename = $_FILES['executive_summary']['name'];

                        $size = $_FILES['executive_summary']['size'];
                        $i = strrpos($imagename, ".");
                        if (!$i) {
                            $ext = '';
                        }
                        $l = strlen($imagename) - $i;
                        $ext = substr($imagename, $i + 1, $l);
                        $executive_summary_name = 'executive_summary_' . rand(11111, 9999999) . '.' . $ext;
                        if ($ext) {
                            if (in_array($ext, $valid_formats)) {
                                // procedure further if and only if image size can not be more than 15MB.
                                if ($size < (1024 * 1024 * 15)) {
                                    $uploadedfile = $_FILES['executive_summary']['tmp_name'];
                                    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/audit/" . $executive_summary_name;
                                    $_movestatus = move_uploaded_file($uploadedfile, $target_file);

                                    $this->audit_model->executive_summary = $executive_summary_name;
                                    $energy_audit['executive_summary'] = $executive_summary_name;
                                    if (!$_movestatus) {
                                        $this->theme->set_message('site image is not uploaded', 'error');
                                    } else {

                                        // Delete Old file
                                        $oldfile = BASE_PATH_CUSTOM . "/assets/uploads/audit" . $sitedetail['executive_summary'];
                                        if (file_exists($oldfile)) {
                                            unlink($oldfile);
                                        }
                                    }
                                } else {
                                    $this->theme->set_message('full report size is too large', 'error');
                                }
                            } else {
                                $this->theme->set_message('full report extension is not .pdf format', 'error');
                            }
                        }
                    }
                    
                    if ($id > 0) {
                        $this->audit_model->update_energy_audit($id);
                        $this->theme->set_message(lang('msg_update_success'), 'success');
                    } else {
                        $this->audit_model->insert_energy_audit($id);
                        $this->theme->set_message(lang('msg_add_success'), 'success');
                    }
                    redirect(BASE_ADMIN_URL_CUSTOM . 'audit/index');
                }
            }

            if (!$this->input->post()) {
                if (isset($id) && $id != '' && $id != '0') {                    
                    $energy_audit_result = $this->audit_model->get_energy_audit_detail_by_id($id);
                    $energy_audit = $energy_audit_result[0]['ea'];
                }
            } else {
                $energy_audit = $this->input->post();
            }
            
            // Breadcrumb settings
            $this->breadcrumb->add(lang('audit_management'), base_url(). 'audit');
            if ($action == "add") {
                $this->theme->set('page_title', $this->lang->line('add_audit'));
                $this->breadcrumb->add($this->lang->line('add_audit'));
                $id = '';
            } elseif ($action == "edit") {
                $this->theme->set('page_title', $this->lang->line('edit_audit'));
                $this->breadcrumb->add($this->lang->line('edit_audit'));
            }

            //Variable assignments to view
            $data = array();
            $data['action'] = $action;
            $data['id'] = $id;
            $data['audit_on'] = $audit_on;
            $data['full_report_title'] = $full_report_title;
            $data['executive_summary_title'] = $executive_summary_title;
            $data['csrf_token'] = $this->security->get_csrf_token_name();
            $data['csrf_hash'] = $this->security->get_csrf_hash();
            $data['audit'] = $energy_audit;            
            $data['content'] = $this->load->view('admin_ajax_action', $data, TRUE);
            $this->theme->view($data, 'admin_action');
        } else {
            $this->theme->set_message(lang('permission_not_allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'audit');
            exit;
        }
    }

    /* Actions For inventory */
    /**
     * action to add/edit audit page
     * @param string $action : add or edit     
     * @param string $id : if in edit mode
     */
    function inventory($action, $id = 0) {        
        //Type Casting
        $action = trim(strip_tags($action));        
        $id = intval($id);
        $inventory_title = '';
        if ($this->input->post('inventorysubmit')) {
            $inventory_on = trim(strip_tags($this->input->post('inventory_on')));
            $full_report_title = trim(strip_tags($this->input->post('inventoty_title')));
        }
        if($action == 'delete' && $this->input->post('type') == 'delete'){
            $inventoryId = $this->input->post('id'); 
            $isDeleted = $this->audit_model->delete_inventory($inventoryId);
            if($isDeleted){
                $this->theme->set_message(lang('inventory_delete_success'), 'success');
                // redirect(BASE_ADMIN_URL_CUSTOM . 'audit');
                header("Location: ".BASE_ADMIN_URL_CUSTOM . 'audit');
                exit;
            }
        }
        if ($action == 'add' || $action == 'edit') {
            //Initialize
            $inventoryAudit = array();
            $inventoryAuditResult = array();
            $inventoryTitles = $this->audit_model->get_inventory_titles();
            $titlesDropdownArray = array();
            if($inventoryTitles){
                foreach ($inventoryTitles as $key => $title) {
                    $titlesDropdownArray[$title['id']] = $title['title'];
                    if($title['id'] == $full_report_title){
                        $inventory_title = $title['title'];
                    }
                }
            }
            // Logic
            if ($this->input->post('inventorysubmit')) {
                //Validation Check
                
                $this->load->library('form_validation');
                $this->form_validation->set_rules('inventory_on', lang('inventory_on'), 'trim|required');
                $this->form_validation->set_rules('inventoty_title', lang('inventoty_title'), 'trim|required');
                
                if (!empty($_FILES["full_report"]["tmp_name"])) {
                    $this->form_validation->set_rules('full_report', lang('full_report'), 'callback_valid_inventory_upload');
                }
                
                if ($this->form_validation->run($this) == true) {
                    $this->audit_model->inventory_on = date('Y-m-d',strtotime($inventory_on));
                    $this->audit_model->full_report_title = $inventory_title;
                    $this->audit_model->inventory_title_id = $full_report_title;
                    
                    if (isset($_FILES['full_report']['name'])) {
                        $config['upload_path'] = BASE_PATH_CUSTOM . "/assets/uploads/";
                        $config['max_size'] = '2048';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        $valid_formats = array("xls","xlsx","pdf","doc","docx","dwg");
                        $imagename = $_FILES['full_report']['name'];

                        $size = $_FILES['full_report']['size'];
                        $i = strrpos($imagename, ".");
                        if (!$i) {
                            $ext = '';
                        }
                        $l = strlen($imagename) - $i;
                        $customName = explode(".", $imagename);
                        $ext = substr($imagename, $i + 1, $l);
                        $full_report_name = $customName[0].'_'.$inventory_on.'.' . $ext;
                        if ($ext) {
                            if (in_array($ext, $valid_formats)) {
                                // procedure further if and only if image size can not be more than 15MB.
                                if ($size < (1024 * 1024 * 15)) {
                                    $uploadedfile = $_FILES['full_report']['tmp_name'];
                                    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/inventory/" . $full_report_name;
                                    if(!is_dir(BASE_PATH_CUSTOM . "/assets/uploads/inventory/")){
                                        mkdir(BASE_PATH_CUSTOM . "/assets/uploads/inventory/");
                                    }
                                    $_movestatus = move_uploaded_file($uploadedfile, $target_file);
                                    $this->audit_model->full_report = $full_report_name;
                                    $inventoryAudit['full_report'] = $full_report_name;
                                    if (!$_movestatus) {
                                        $this->theme->set_message('site image is not uploaded', 'error');
                                    } else {

                                        // Delete Old file
                                        $oldfile = BASE_PATH_CUSTOM . "/assets/uploads/inventory" . $sitedetail['full_report'];
                                        if (file_exists($oldfile)) {
                                            unlink($oldfile);
                                        }
                                    }
                                } else {
                                    $this->theme->set_message('full report size is too large', 'error');
                                }
                            } else {
                                $this->theme->set_message('full report extension is not .pdf format', 'error');
                            }
                        }
                    }
                    if ($id == 0) {
                        $this->audit_model->insert_inventory($id);
                        $this->theme->set_message(lang('inventory_add_success'), 'success');
                    }
                    redirect(BASE_ADMIN_URL_CUSTOM . 'audit/index');
                }
            }
            
            // Breadcrumb settings
            if ($action == "add") {
                $this->theme->set('page_title', $this->lang->line('add_inventory'));
                $this->breadcrumb->add($this->lang->line('add_inventory'));
                $id = '';
            } elseif ($action == "edit") {
                $this->theme->set('page_title', $this->lang->line('edit_inventory'));
                $this->breadcrumb->add($this->lang->line('edit_inventory'));
            }

            //Variable assignments to view
            $data = array();
            $data['action'] = $action;
            $data['id'] = $id;
            $data['audit_on'] = $inventory_on;
            $data['inventory_titles'] = $titlesDropdownArray;
            $data['full_report_title'] = $full_report_title;
            $data['csrf_token'] = $this->security->get_csrf_token_name();
            $data['csrf_hash'] = $this->security->get_csrf_hash();
            $data['content'] = $this->load->view('admin_ajax_add_inventory', $data, TRUE);
            $this->theme->view($data, 'admin_action');
        } else {
            $this->theme->set_message(lang('permission_not_allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'audit');
            exit;
        }
    }   
    /* Actions For inventory */

    /**
     * action to add/edit category page load form from ajax based on language
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     * @param string $ajax_load : will be 1 if load from ajax mode
     */
    function ajax_action($action, $language_code = '', $id = 0, $ajax_load = 1) {        
        //Type Casting
        $action = trim(strip_tags($action));
        $language_code = strip_tags($language_code);
        $id = intval($id);
        $ajax_load = intval($ajax_load);

        //Initialize
        $energy_audit = array();
        $energy_audit_result = array();
        $data = array();
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);

        if (isset($id) && $id != '' && $id != '0') {
            $energy_audit_result = $this->audit_model->get_category_detail_by_id($id, $language_detail[0]['l']['id']);
            if (!empty($energy_audit_result)) {
                if (!empty($energy_audit_result[0]['c']))
                    $energy_audit = $energy_audit_result[0]['c'];
            }
        }
        //Variable assignments to view
        $data['action'] = $action;
        $data['id'] = $id;
        $data['language_id'] = $language_detail[0]['l']['id'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_code'] = $language_code;
        $data['category'] = $energy_audit;

        //Category Modulelist
        $category_module_list = $this->audit_model->get_category_module_list();
        $data['category_module_list'] = $category_module_list;

        if ($ajax_load == '1')
            echo $this->load->view('admin_ajax_action', $data);
        else
            return $this->load->view('admin_ajax_action', $data);
    }

    // Function to delete the category record and related url management record
    function delete() {
       
        $id = $this->input->post('id');       
        $id = intval($id);
        
        if ($id != 0 && $id != '' && is_numeric($id)) {
            $delete_records = $this->audit_model->delete_records($id);            
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        } else {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }
}
